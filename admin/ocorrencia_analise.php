<?php
// Inclui cabeçalho (deve ter session_start() e $conn)
// Reativação de erros no topo para garantir visibilidade em caso de falha:
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/header.php';

// =====================================================================
// 1. OBTENÇÃO E VALIDAÇÃO DO ID
// =====================================================================
$oco_id_url = $_GET['i'] ?? die("ID da Ocorrência não fornecido.");


// =====================================================================
// 2. LÓGICA DE BUSCA NO BD - CORRIGIDO: JOINs para Nomes de FKs e Campos de Data/Componente
// =====================================================================
$ocorrencia_atual = null;
$versao_historico_recente = null;
$versao_original_absoluta = null;
$erro_busca_sql = null;

try {
    // 1. BUSCA A OCORRÊNCIA ATUAL (sempre a versão mais recente)
    $sql_atual = "
        SELECT 
            o.*, 
            u_criacao.admin_nome AS criador_nome,
            u_edicao.admin_nome AS editor_nome,
            r.res_id, r.res_codigo, r.res_data, r.res_hora_inicio, r.res_hora_fim, ctr.ctr_tipo_reserva AS res_tipo,
            
            s.solic_nome_prof_resp AS professor, 
            c.curs_curso AS curso, /* NOME DO CURSO */
            
            cc.compc_componente AS comp_curric_nome, /* NOME DO COMPONENTE CURRICULAR (via ID) */
            s.solic_nome_comp_ativ,
            s.solic_nome_atividade,
            r.res_nome_atividade AS res_ativ_nome,
            r.res_componente_atividade_nome AS res_comp_nome_manual,
            
            r.res_data_inicio_semanal, /* Datas de intervalo para FIXA */
            r.res_data_fim_semanal, /* Datas de intervalo para FIXA */
            r.res_dia_semana AS dias_semana_fixa, /* Dias selecionados para FIXA */
            
            e.esp_nome_local AS local_nome, e.esp_codigo AS local_id,
            te.tipesp_tipo_espaco AS tipo_espaco,
            te.tipesp_tipo_espaco AS tipo_sala,
            ta.cta_tipo_aula AS tipo_aula,
            u_and.and_andar AS andar_nome,
            u_pav.pav_pavilhao AS pavi_nome,
            u_uni.uni_unidade AS campus_nome
        FROM ocorrencias o
        LEFT JOIN admin u_criacao ON u_criacao.admin_id = o.oco_user_id
        LEFT JOIN admin u_edicao ON u_edicao.admin_id = o.oco_autor_edicao
        INNER JOIN reservas r ON r.res_id = o.oco_res_id
        INNER JOIN solicitacao s ON s.solic_id = r.res_solic_id
        INNER JOIN conf_tipo_reserva ctr ON ctr.ctr_id = r.res_tipo_reserva
        
        /* JOINs para obter o NOME do CURSO e do COMPONENTE (via FK) */
        LEFT JOIN cursos c ON c.curs_id = s.solic_curso
        LEFT JOIN componente_curricular cc ON cc.compc_id = s.solic_comp_curric
        
        INNER JOIN espaco e ON e.esp_id = r.res_espaco_id
        INNER JOIN tipo_espaco te ON te.tipesp_id = e.esp_tipo_espaco
        /* LEFT JOIN conf_tipo_sala ts ON ts.tiposala_id = e.esp_tipo_sala -- REMOVIDO */
        LEFT JOIN conf_tipo_aula ta ON ta.cta_id = r.res_tipo_aula
        LEFT JOIN pavilhoes u_pav ON u_pav.pav_id = e.esp_pavilhao
        LEFT JOIN andares u_and ON u_and.and_id = e.esp_andar
        LEFT JOIN unidades u_uni ON u_uni.uni_id = e.esp_unidade
        WHERE o.oco_id = :id
    ";
    $stmt_atual = $conn->prepare($sql_atual);
    $stmt_atual->execute([':id' => $oco_id_url]);
    $ocorrencia_atual = $stmt_atual->fetch(PDO::FETCH_ASSOC);

    if ($ocorrencia_atual) {
        // 2. BUSCA A VERSÃO MAIS ANTIGA DO HISTÓRICO (LANÇAMENTO ORIGINAL)
        $sql_historico_recente = "
            SELECT TOP 1 h.*, u_criacao.admin_nome AS criador_nome 
            FROM ocorrencias_historico h
            LEFT JOIN admin u_criacao ON u_criacao.admin_id = h.oco_user_id
            WHERE h.oco_id = :id 
            ORDER BY h.hist_id ASC
        ";

        $stmt_historico_recente = $conn->prepare($sql_historico_recente);
        $stmt_historico_recente->execute([':id' => $ocorrencia_atual['oco_id']]);
        $versao_historico_recente = $stmt_historico_recente->fetch(PDO::FETCH_ASSOC);

        // 3. DEFINE A VERSÃO ORIGINAL
        $versao_original_absoluta = $versao_historico_recente ?: $ocorrencia_atual;

        // ====================================================================
        // LÓGICA DE PRIORIDADE DO CAMPO 'COMPONENTE'
        // ====================================================================
        $componente_atual = $ocorrencia_atual['comp_curric_nome']
            ?: $ocorrencia_atual['res_comp_nome_manual']
            ?: $ocorrencia_atual['res_ativ_nome']
            ?: $ocorrencia_atual['solic_nome_comp_ativ']
            ?: $ocorrencia_atual['solic_nome_atividade']
            ?: 'N/A';

        $ocorrencia_atual['componente'] = $componente_atual;

        // Faz o mesmo para a versão histórica, se existir.
        if ($versao_historico_recente) {
            $versao_historico_recente['componente'] = $ocorrencia_atual['componente'];
        }

    }

} catch (PDOException $e) {
    // Armazena o erro para ser exibido no layout de fallback
    $ocorrencia_atual = null;
    $erro_busca_sql = "Falha ao consultar o banco de dados: " . $e->getMessage();
}
// =====================================================================
// 3. BUSCA DOS TIPOS DE OCORRÊNCIA
// =====================================================================
$tipos_ocorrencia_map = [];
try {
    if (!$erro_busca_sql) {
        $sql_tipos = $conn->prepare("SELECT cto_id, UPPER(cto_tipo_ocorrencia) AS cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_status = 1");
        $sql_tipos->execute();
        foreach ($sql_tipos->fetchAll(PDO::FETCH_ASSOC) as $tipo) {
            $tipos_ocorrencia_map[$tipo['cto_id']] = htmlspecialchars($tipo['cto_tipo_ocorrencia']);
        }
    }
} catch (PDOException $e) {
    $erro_busca_sql .= "<br>Falha ao buscar tipos de ocorrência (conf_tipo_ocorrencia): " . $e->getMessage();
}


// =====================================================================
// 4. VARIÁVEIS DE EXIBIÇÃO
// =====================================================================
$PERFIL_ADMIN = 1;
$perfil_id = $global_admin_perfil ?? 0;
$pode_editar = ((int) $perfil_id === $PERFIL_ADMIN);

$dados_reserva = $ocorrencia_atual;
$oco_status = $ocorrencia_atual['oco_status'] ?? 0;

// Lógica de status para o badge principal
$status_badge_html = '';
if ($oco_status == 2) {
    $status_badge_html = '<span class="badge bg-success" style="font-size: 9.9px">VALIDADA</span>';
} elseif ($oco_status == 3) {
    $status_badge_html = '<span class="badge bg-danger" style="font-size: 9.9px">REJEITADA</span>';
} else {
    $status_badge_html = '<span class="badge bg-warning text-dark " style="font-size: 9.9px">PENDENTE</span>';
}

// Lógica para determinar se a seção de Admin deve ser exibida
$mostrar_secao_admin = false;
$dados_operador_para_exibir = $ocorrencia_atual;

if ($versao_historico_recente) {
    if (
        trim($ocorrencia_atual['oco_tipo_ocorrencia'] ?? '') != trim($versao_historico_recente['oco_tipo_ocorrencia'] ?? '') ||
        ($ocorrencia_atual['oco_hora_inicio_realizado'] ?? '') != ($versao_historico_recente['oco_hora_inicio_realizado'] ?? '') ||
        ($ocorrencia_atual['oco_hora_fim_realizado'] ?? '') != ($versao_historico_recente['oco_hora_fim_realizado'] ?? '') ||
        !empty($ocorrencia_atual['oco_parecer_tecnico'])
    ) {
        $mostrar_secao_admin = true;
    }
    $dados_operador_para_exibir = $versao_historico_recente;
    if ($versao_historico_recente) {
        $dados_operador_para_exibir['componente'] = $ocorrencia_atual['componente'];
    }
}

// CORREÇÃO APLICADA AQUI: Pegar o nome da reserva para a condicional no HTML.
$tipo_reserva_nome = strtoupper($dados_reserva['res_tipo'] ?? '');


/**
 * Renderiza os dados de uma versão da ocorrência (Operador ou Admin).
 * REESTRUTURADO PARA SINTAXE DE TEMPLATE.
 * PARECER TÉCNICO REMOVIDO DO LAYOUT ADMIN DENTRO DESTA FUNÇÃO.
 */
function renderOcorrenciaCard($data, $is_admin_card, $tipos_map = [])
{
    if (!$data)
        return;

    // Campos comuns
    $criador_nome = htmlspecialchars($data['criador_nome'] ?? 'N/A');
    $data_cad = isset($data['oco_data_cad']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['oco_data_cad']))) : 'N/A';
    $data_edicao = isset($data['oco_data_edicao']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['oco_data_edicao']))) : 'N/A';

    $obs = nl2br(htmlspecialchars($data['oco_obs'] ?? 'N/A'));
    $parecer = nl2br(htmlspecialchars($data['oco_parecer_tecnico'] ?? ''));
    $carga_horaria = $data['oco_carga_horaria_calculada'] ?? null;
    $editor_nome = htmlspecialchars($data['editor_nome'] ?? 'Admin'); // Usado apenas no Admin Card

    $oco_tipo_ids = $data['oco_tipo_ocorrencia'] ?? '';
    $tipos_selecionados_html = 'N/A';
    if (!empty($oco_tipo_ids) && !empty($tipos_map)) {
        $ids_array = explode(',', $oco_tipo_ids);
        $nomes_tipos = [];
        foreach ($ids_array as $id) {
            $id = trim($id);
            if (isset($tipos_map[$id])) {
                $nomes_tipos[] = '• ' . $tipos_map[$id];
            }
        }
        if (!empty($nomes_tipos)) {
            $tipos_selecionados_html = implode('<br>', $nomes_tipos);
        }
    }

    // Horários Realizados
    $inicio_realizado = !empty($data['oco_hora_inicio_realizado']) ? htmlspecialchars(date('H:i', strtotime($data['oco_hora_inicio_realizado']))) : 'N/A';
    $fim_realizado = !empty($data['oco_hora_fim_realizado']) ? htmlspecialchars(date('H:i', strtotime($data['oco_hora_fim_realizado']))) : 'N/A';


    if (!$is_admin_card):
        // LAYOUT DO OPERADOR (Sintaxe de Template)
        ?>
        <div class="row">
            <div class="col-12">
                <label class="mb-1 text-muted">Tipo(s) de ocorrência</label>
                <p class="text-uppercase mb-4"><?= $tipos_selecionados_html ?></p>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-xl-4 col-xxl-3">
                <label class="mb-1 text-muted">Operador</label>
                <p class="fw-medium mb-0"><?= $criador_nome ?></p>
                <hr>
            </div>
            <div class="col-sm-6 col-xl-4 col-xxl-3">
                <label class="mb-1 text-muted">Data do cadastro</label>
                <p class="fw-medium mb-0"><?= $data_cad ?></p>
                <hr>
            </div>
            <div class="col-sm-6 col-xl-4 col-xxl-3">
                <label class="mb-1 text-muted">Início Realizado</label>
                <p class="fw-medium mb-0"><?= $inicio_realizado ?></p>
                <hr>
            </div>
            <div class="col-sm-6 col-xl-4 col-xxl-3">
                <label class="mb-1 text-muted">Término Realizado</label>
                <p class="fw-medium mb-0"><?= $fim_realizado ?></p>
                <hr>
            </div>
        </div>

        <div class="col-12 ">
            <label class="mb-1 text-muted">Observações</label>
            <p class="mb-0"><?= ($obs ?? 'N/A') ?></p>
        </div>

        <?php
    else:
        // LAYOUT DO ADMIN (Card de Revisão Técnica - SÓ CARGA HORÁRIA E EDIÇÃO)
        ?>
        <div class="card p-3 my-3 card-body-admin">
            <h6 class="mb-3 text-primary">Revisão Técnica</h6>
            <div class="row g-3">

                <div class="col-sm-12 col-xl-4 col-xxl-3">
                    <label>Tipo(s) de Ocorrência:</label>
                    <p class="text-uppercase"><?= $tipos_selecionados_html ?> </p>
                </div>
                <hr>
            </div>
            <div class="row g-3">
                <div class="col-sm-6 col-xl-4 col-xxl-3">
                    <label>Início Realizado:</label>
                    <p> <?= $inicio_realizado ?> </p>
                    <hr>
                </div>
                <div class="col-sm-6 col-xl-4 col-xxl-3">
                    <label>Término Realizado:</label>
                    <p> <?= $fim_realizado ?> </p>
                    <hr>
                </div>
            </div>

                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                <label>Carga Horária Calculada:</label>
                <p> <?= (($data['oco_status'] == 2 && $carga_horaria) ? htmlspecialchars(date('H:i', strtotime($carga_horaria))) : 'N/A') ?>
                </p>
                <hr>
            </div>
            <div class="col-sm-6 col-xl-4 col-xxl-3">
                <label>Operador</label>
                <p> <?= $editor_nome ?> </p>
                <hr>
            </div>

            <div class="col-sm-6 col-xl-4 col-xxl-3">
                <label>Data da Edição:</label>
                <p> <?= $data_edicao ?> </p>
                <hr>
            </div>
        </div>
        </div>
        <?php
    endif;
}


// ==============================================================================
// 5. EXIBIÇÃO DO LAYOUT
// ==============================================================================

// VERIFICAÇÃO PRINCIPAL DE ERRO/OCORRÊNCIA
if (!$ocorrencia_atual || $erro_busca_sql):
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card p-5">
                <h3 class="text-danger">Erro na Análise da Ocorrência</h3>
                <p>Não foi possível carregar os detalhes da ocorrência. Motivo provável:</p>
                <p class="text-muted">
                    <?php
                    if ($erro_busca_sql) {
                        echo nl2br($erro_busca_sql);
                    } else {
                        echo "Ocorrência não encontrada com o ID fornecido na URL.";
                    }
                    ?>
                </p>
                <a href="ocorrencias.php" class="btn btn-primary mt-3 w-25">Voltar para a Lista</a>
            </div>
        </div>
    </div>

<?php else: // SE TUDO ESTIVER OK, EXIBE A TELA NORMAL ?>

    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg"></div>
    </div>

    <div class="row breadcrumb_painel">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Análise de Ocorrência #<?= htmlspecialchars($ocorrencia_atual['oco_codigo'] ?? '') ?>
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="ocorrencias.php">Ocorrências</a></li>
                        <li class="breadcrumb-item active">Análise</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header ">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Dados da ocorrência</h5>
                        <div class="d-flex justify-content-end ">

                            <?php if ($pode_editar && $ocorrencia_atual['oco_status'] == 1): ?>
                                <form id="form_validar_ocorrencia" method="POST" action="../router/web.php?r=Ocorrenc"
                                    style="display:inline;">
                                    <input type="hidden" name="acao" value="validar">
                                    <input type="hidden" name="oco_id"
                                        value="<?= htmlspecialchars($ocorrencia_atual['oco_id'] ?? '') ?>">
                                    <button type="button"
                                        class="btn botao_w botao botao_verde waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3"
                                        id="btn_validar_rapido">
                                        Validar
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($pode_editar):
                                $dados_originais_para_modal = $versao_original_absoluta;
                                ?>
                                <button type="button"
                                    class="btn botao_w botao botao_azul_escuro waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3"
                                    data-bs-toggle="modal" data-bs-target="#modal_admin_ocorrencia"
                                    data-bs-oco_id="<?= htmlspecialchars($ocorrencia_atual['oco_id'] ?? '') ?>"
                                    data-bs-oco_res_id="<?= htmlspecialchars($ocorrencia_atual['oco_res_id'] ?? '') ?>"
                                    data-bs-oco_status="<?= htmlspecialchars($ocorrencia_atual['oco_status'] ?? '1') ?>"
                                    data-bs-oco_parecer_tecnico="<?= htmlspecialchars($ocorrencia_atual['oco_parecer_tecnico'] ?? '') ?>"
                                    data-bs-oco_tipo_ocorrencia="<?= htmlspecialchars($dados_originais_para_modal['oco_tipo_ocorrencia'] ?? '') ?>"
                                    data-bs-oco_hora_inicio_realizado="<?= htmlspecialchars($dados_originais_para_modal['oco_hora_inicio_realizado'] ?? '') ?>"
                                    data-bs-oco_hora_fim_realizado="<?= htmlspecialchars($dados_originais_para_modal['oco_hora_fim_realizado'] ?? '') ?>"
                                    data-bs-oco_obs="<?= htmlspecialchars($dados_originais_para_modal['oco_obs'] ?? '') ?>"
                                    data-bs-res_hora_inicio="<?= htmlspecialchars($dados_reserva['res_hora_inicio'] ?? '') ?>"
                                    data-bs-res_hora_fim="<?= htmlspecialchars($dados_reserva['res_hora_fim'] ?? '') ?>"
                                    data-bs-res_codigo_data="<?= htmlspecialchars($dados_reserva['res_codigo'] . ': ' . date('d/m/Y', strtotime($dados_reserva['res_data'] ?? ''))) ?>">
                                    Parecer técnico
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-end">
                        <?= $status_badge_html ?>
                    </div>


                    <?php renderOcorrenciaCard($dados_operador_para_exibir, false, $tipos_ocorrencia_map); ?>

                    <?php if ($mostrar_secao_admin): ?>
                                            <?php endif; ?>
                    <?php if ($mostrar_secao_admin): ?>
                        <h5 class="card-title mb-3 mt-4">Dados de Revisão (Admin)</h5>
                        <div class="card p-3 my-3 card-body-admin">
                            <h6 class="mb-3 text-primary">Revisão Técnica</h6>
                            <div class="row g-3">

                                <div class="col-sm-12 col-xl-4 col-xxl-3">
                                    <label>Tipo(s) de Ocorrência:</label>
                                    <p class="text-uppercase"><?= $tipos_selecionados_html ?> </p>
                                </div>
                                <hr>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-6 col-xl-4 col-xxl-3">
                                    <label>Início Realizado:</label>
                                    <p> <?= !empty($ocorrencia_atual['oco_hora_inicio_realizado']) ? htmlspecialchars(date('H:i', strtotime($ocorrencia_atual['oco_hora_inicio_realizado']))) : 'N/A' ?> </p>
                                    <hr>
                                </div>
                                <div class="col-sm-6 col-xl-4 col-xxl-3">
                                    <label>Término Realizado:</label>
                                    <p> <?= !empty($ocorrencia_atual['oco_hora_fim_realizado']) ? htmlspecialchars(date('H:i', strtotime($ocorrencia_atual['oco_hora_fim_realizado']))) : 'N/A' ?> </p>
                                    <hr>
                                </div>
                            </div>

                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                <label>Carga Horária Calculada:</label>
                                <p> <?= (($ocorrencia_atual['oco_status'] == 2 && $ocorrencia_atual['oco_carga_horaria_calculada']) ? htmlspecialchars(date('H:i', strtotime($ocorrencia_atual['oco_carga_horaria_calculada']))) : 'N/A') ?>
                                </p>
                                <hr>
                            </div>
                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                <label>Operador</label>
                                <p> <?= htmlspecialchars($ocorrencia_atual['editor_nome'] ?? 'Admin') ?> </p>
                                <hr>
                            </div>

                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                <label>Data da Edição:</label>
                                <p> <?= isset($ocorrencia_atual['oco_data_edicao']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($ocorrencia_atual['oco_data_edicao']))) : 'N/A' ?> </p>
                                <hr>
                            </div>
                        </div>
                    <?php endif; ?>
                                        <hr class="mt-4 mb-3" />
                </div>
            </div>


            <div class="accordion" id="accordionFlushExample">

                <div class="accordion-item mb-3">
                    <h2 class="accordion-header" id="flush-headingTwo">
                        <button class="accordion-button collapsed fw-medium" style="background: var(--roxo_alpha);"
                            type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo"
                            aria-expanded="false" aria-controls="flush-collapseTwo">
                            <h3 class="ms-0 fs-16">Dados da Reserva</h3 class="ms-0 fs-16">
                        </button>
                    </h2>

                    <div id="flush-collapseTwo" class="accordion-collapse collapse show" aria-labelledby="flush-headingTwo"
                        data-bs-parent="#accordionFlushExample">

                        <div class="card">
                            <div class="card-header ">
                                <div class="card-body">
                                    <div class="row g-3">

                                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                                            <label>ID da Reserva</label>
                                            <p>
                                                <?= htmlspecialchars($dados_reserva['res_codigo'] ?? 'N/A') ?>
                                            </p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                                            <label>Curso</label>
                                            <p><?= htmlspecialchars($dados_reserva['curso'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3"><label>Componente
                                                Curricular/Atividade</label>
                                            <p><?= htmlspecialchars($dados_reserva['componente'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                                            <label>Nome do Professor/Responsável</label>
                                            <p><?= htmlspecialchars($dados_reserva['professor'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>

                                    </div>

                                    <div class="row g-3 mt-0">
                                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                                            <label>ID do Local</label>
                                            <p> <?= htmlspecialchars($dados_reserva['local_id'] ?? 'N/A') ?>
                                            </p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3"><label>Local
                                                Reservado</label>
                                            <p><?= htmlspecialchars($dados_reserva['local_nome'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                                            <label>Campus</label>
                                            <p><?= htmlspecialchars($dados_reserva['campus_nome'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                                            <label>Pavilhão</label>
                                            <p><?= htmlspecialchars($dados_reserva['pavi_nome'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-0">
                                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                                            <label>Andar</label>
                                            <p><?= htmlspecialchars($dados_reserva['andar_nome'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3"><label>Tipo de
                                                Sala</label>
                                            <p><?= htmlspecialchars($dados_reserva['tipo_sala'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3"><label>Tipo de
                                                Aula</label>

                                            <p><?= htmlspecialchars($dados_reserva['tipo_aula'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3"><label>Tipo de
                                                Reserva</label>
                                            <p><?= htmlspecialchars($dados_reserva['res_tipo'] ?? 'N/A') ?></p>
                                            <hr>
                                        </div>
                                    </div>

                                    <?php
                                    $res_data_formatada = isset($dados_reserva['res_data']) ? htmlspecialchars(date('d/m/Y', strtotime($dados_reserva['res_data']))) : 'N/A';
                                    $res_inicio_formatado = isset($dados_reserva['res_hora_inicio']) ? htmlspecialchars(date('H:i', strtotime($dados_reserva['res_hora_inicio']))) : 'N/A';
                                    $res_fim_formatado = isset($dados_reserva['res_hora_fim']) ? htmlspecialchars(date('H:i', strtotime($dados_reserva['res_hora_fim']))) : 'N/A';

                                    // Datas de intervalo (para fixas)
                                    $data_inicio_semanal_raw = $dados_reserva['res_data_inicio_semanal'] ?? null;
                                    $data_fim_semanal_raw = $dados_reserva['res_data_fim_semanal'] ?? null;

                                    $data_inicio_semanal = $data_inicio_semanal_raw ? htmlspecialchars(date('d/m/Y', strtotime($data_inicio_semanal_raw))) : 'N/A';
                                    $data_fim_semanal = $data_fim_semanal_raw ? htmlspecialchars(date('d/m/Y', strtotime($data_fim_semanal_raw))) : 'N/A';


                                    $tipo_reserva_nome = strtoupper($dados_reserva['res_tipo'] ?? '');
                                    ?>

                                    <div class="row g-3 mt-0">
                                        <?php if ($tipo_reserva_nome !== 'FIXA'): ?>
                                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                                <label>Data da Reserva</label>
                                                <p><?= $res_data_formatada ?></p>
                                                <hr>
                                            </div>
                                        <?php elseif ($tipo_reserva_nome === 'FIXA'): ?>
                                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                                <label>Data Início</label>
                                                <p><?= $data_inicio_semanal ?></p>
                                                <hr>
                                            </div>
                                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                                <label>Data Fim</label>
                                                <p><?= $data_fim_semanal ?></p>
                                                <hr>
                                            </div>
                                        <?php endif; ?>

                                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                                            <label>Horário Inicial (Previsto)</label>
                                            <p><?= $res_inicio_formatado ?></p>
                                            <hr>
                                        </div>
                                        <div class="col-sm-6 col-xl-4 col-xxl-3">
                                            <label>Horário Final (Previsto)</label>
                                            <p><?= $res_fim_formatado ?></p>
                                            <hr>
                                        </div>

                                    </div>

                                  <?php if ($tipo_reserva_nome === 'FIXA'): ?>
    <div class="row g-3 mt-0">
        <div class="col-12">
            <label class="mb-2">Dias da Semana (Recorrência)</label><br>

            <div class="check_item_container hstack gap-2 flex-wrap mt-2">
                <?php
                $map_dias = [
                    '1' => 'SEGUNDA',
                    '2' => 'TERÇA',
                    '3' => 'QUARTA',
                    '4' => 'QUINTA',
                    '5' => 'SEXTA',
                    '6' => 'SÁBADO',
                    '7' => 'DOMINGO'
                ];
                $dias_selecionados_ids = explode(',', $dados_reserva['dias_semana_fixa'] ?? '');
                
                foreach ($map_dias as $id => $nome_dia):
                    $is_selected = in_array($id, $dias_selecionados_ids);
                    $checked_attr = $is_selected ? "checked" : "";
                    ?>
                    <input type="checkbox" class="btn-check check_formulario_check"
                        id="dias_semana<?= $id ?>" value="<?= $id ?>"
                        <?= $checked_attr ?>
                        disabled>
                    <label class="check_item check_formulario"
                        for="dias_semana<?= $id ?>"><?= htmlspecialchars($nome_dia) ?></label>
                <?php endforeach; ?>
            </div>
            <hr class="mt-4">
        </div>
        </div>
<?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


               <?php 
                // CONDIÇÃO PARA EXIBIÇÃO DO ACCORDION DO PARECER TÉCNICO (SE PODE EDITAR E SE HOUVER PARECER)
                if ($pode_editar && !empty($ocorrencia_atual['oco_parecer_tecnico'])): ?>
                    <div class="accordion-item mb-4">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed" style="background: var(--azul_alpha);" type="button"
                                data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false"
                                aria-controls="flush-collapseThree">
                                <h3 class="ms-0 fs-16">Parecer Técnico (Histórico de Revisões)</h3> 
                            </button>
                        </h2>
                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree"
                            data-bs-parent="#accordionFlushExample">
                            <div class="card">
                                <div class="card-header ">
                                    <div class="card-body">
                                        <h5>Detalhes da Revisão Técnica:</h5>
                                        <?php
                                        $data_ac = $ocorrencia_atual; // Dados da ocorrência atual
                                        
                                        // Redefinição de variáveis necessárias para a exibição completa
                                        $parecer_ac = nl2br(htmlspecialchars($data_ac['oco_parecer_tecnico'] ?? ''));
                                        $carga_horaria_ac = $data_ac['oco_carga_horaria_calculada'] ?? null;
                                        $editor_nome_ac = htmlspecialchars($data_ac['editor_nome'] ?? 'Admin');
                                        $oco_tipo_ids_ac = $data_ac['oco_tipo_ocorrencia'] ?? '';
                                        $data_edicao_ac = isset($data_ac['oco_data_edicao']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($data_ac['oco_data_edicao']))) : 'N/A';
                                        $inicio_realizado_ac = !empty($data_ac['oco_hora_inicio_realizado']) ? htmlspecialchars(date('H:i', strtotime($data_ac['oco_hora_inicio_realizado']))) : 'N/A';
                                        $fim_realizado_ac = !empty($data_ac['oco_hora_fim_realizado']) ? htmlspecialchars(date('H:i', strtotime($data_ac['oco_hora_fim_realizado']))) : 'N/A';
                                        
                                        // Lógica dos Tipos de Ocorrência para o Accordion
                                        $tipos_selecionados_html_ac = 'N/A';
                                        if (!empty($oco_tipo_ids_ac) && !empty($tipos_ocorrencia_map)) {
                                            $ids_array_ac = explode(',', $oco_tipo_ids_ac);
                                            $nomes_tipos_ac = [];
                                            foreach ($ids_array_ac as $id) {
                                                $id = trim($id);
                                                if (isset($tipos_ocorrencia_map[$id])) {
                                                    $nomes_tipos_ac[] = '• ' . $tipos_ocorrencia_map[$id];
                                                }
                                            }
                                            if (!empty($nomes_tipos_ac)) {
                                                $tipos_selecionados_html_ac = implode('<br>', $nomes_tipos_ac);
                                            }
                                        }
                                        ?>
                                        
                                        <div class="row g-3">
                                            <div class="col-sm-12">
                                                <label>Tipo(s) de Ocorrência:</label>
                                                <p class="text-uppercase"><?= $tipos_selecionados_html_ac ?> </p>
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                                <label>Início Realizado:</label>
                                                <p> <?= $inicio_realizado_ac ?> </p>
                                                <hr>
                                            </div>
                                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                                <label>Término Realizado:</label>
                                                <p> <?= $fim_realizado_ac ?> </p>
                                                <hr>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-sm-12">
                                                <label>Parecer Técnico:</label>
                                                <div class="p-3 bg-light border rounded">
                                                    <p class="mb-0"> <?= ($parecer_ac ?: 'N/A') ?> </p>
                                                </div>
                                                <hr>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                                <label>Carga Horária Calculada:</label>
                                                <p> <?= (($data_ac['oco_status'] == 2 && $carga_horaria_ac) ? htmlspecialchars(date('H:i', strtotime($carga_horaria_ac))) : 'N/A') ?>
                                                </p>
                                                <hr>
                                            </div>
                                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                                <label>Operador (Edição):</label>
                                                <p> <?= $editor_nome_ac ?> </p>
                                                <hr>
                                            </div>
                                            <div class="col-sm-6 col-xl-4 col-xxl-3">
                                                <label>Data da Edição:</label>
                                                <p> <?= $data_edicao_ac ?> </p>
                                                <hr>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>


<?php endif; // FIM DO IF/ELSE PRINCIPAL ?>

<style>
    #preloader,
    #status,
    .page-loader {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    body {
        visibility: visible !important;
        overflow: auto !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnValidarRapido = document.getElementById('btn_validar_rapido');
        const formValidarOcorrencia = document.getElementById('form_validar_ocorrencia');

        if (btnValidarRapido && formValidarOcorrencia) {
            btnValidarRapido.addEventListener('click', function (e) {
                e.preventDefault();

                // Assumindo que você tem o SweetAlert configurado (window.Swal)
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Confirmar Validação?',
                        html: 'Tem certeza que deseja <strong>VALIDAR</strong> esta ocorrência? Isso calculará a carga horária com base nos horários registrados e atualizará o status.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sim, Validar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formValidarOcorrencia.submit();
                        }
                    });
                } else {
                    // Fallback para o alert simples caso o SweetAlert não esteja carregado
                    if (confirm('Tem certeza que deseja VALIDAR esta ocorrência? Isso calculará a carga horária com base nos horários registrados.')) {
                        formValidarOcorrencia.submit();
                    }
                }
            });
        }
    });
</script>

<?php include 'includes/modal/modal_ocorrencia.php'; ?>
<?php include 'includes/footer.php'; ?>