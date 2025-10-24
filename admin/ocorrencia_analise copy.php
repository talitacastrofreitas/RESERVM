<?php
// Inclui cabeçalho (deve ter session_start() e $conn)
include 'includes/header.php';

// =====================================================================
// 1. OBTENÇÃO E VALIDAÇÃO DO ID
// =====================================================================
$oco_id_url = $_GET['i'] ?? die("ID da Ocorrência não fornecido.");


// =====================================================================
// 2. LÓGICA DE BUSCA NO BD
// =====================================================================
$ocorrencia_atual = null;
$versao_historico_recente = null; // Para o card do operador
$versao_original_absoluta = null; // Para popular o modal de edição

try {
    // 1. BUSCA A OCORRÊNCIA ATUAL (sempre a versão mais recente)
    $sql_atual = "
        SELECT 
            o.*, 
            u_criacao.admin_nome AS criador_nome,
            u_edicao.admin_nome AS editor_nome
        FROM ocorrencias o
        LEFT JOIN admin u_criacao ON u_criacao.admin_id = o.oco_user_id
        LEFT JOIN admin u_edicao ON u_edicao.admin_id = o.oco_autor_edicao
        WHERE o.oco_id = :id
    ";
    $stmt_atual = $conn->prepare($sql_atual);
    $stmt_atual->execute([':id' => $oco_id_url]);
    $ocorrencia_atual = $stmt_atual->fetch(PDO::FETCH_ASSOC);

    if (!$ocorrencia_atual) {
        die("Ocorrência não encontrada.");
    }

    // 2. BUSCA A VERSÃO MAIS RECENTE DO HISTÓRICO (PARA O CARD DO OPERADOR)
    $sql_historico_recente = "
        SELECT TOP 1 h.*, u_criacao.admin_nome AS criador_nome 
        FROM ocorrencias_historico h
        LEFT JOIN admin u_criacao ON u_criacao.admin_id = h.oco_user_id
        WHERE h.oco_id = :id ORDER BY h.hist_id DESC
    ";
    $stmt_historico_recente = $conn->prepare($sql_historico_recente);
    $stmt_historico_recente->execute([':id' => $ocorrencia_atual['oco_id']]);
    $versao_historico_recente = $stmt_historico_recente->fetch(PDO::FETCH_ASSOC);

    // 3. BUSCA A VERSÃO MAIS ANTIGA DO HISTÓRICO (PARA O MODAL DE EDIÇÃO)
    $sql_original_absoluto = "
        SELECT TOP 1 h.*, u_criacao.admin_nome AS criador_nome 
        FROM ocorrencias_historico h
        LEFT JOIN admin u_criacao ON u_criacao.admin_id = h.oco_user_id
        WHERE h.oco_id = :id ORDER BY h.hist_id ASC
    ";
    $stmt_original_absoluto = $conn->prepare($sql_original_absoluto);
    $stmt_original_absoluto->execute([':id' => $ocorrencia_atual['oco_id']]);
    $versao_original_absoluta = $stmt_original_absoluto->fetch(PDO::FETCH_ASSOC);


} catch (PDOException $e) {
    die("Erro ao recuperar os dados da ocorrência: " . $e->getMessage());
}

try {
    $sql_tipos = $conn->prepare("SELECT cto_id, UPPER(cto_tipo_ocorrencia) AS cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_status = 1");
    $sql_tipos->execute();
    $tipos_ocorrencia_map = [];
    foreach ($sql_tipos->fetchAll(PDO::FETCH_ASSOC) as $tipo) {
        $tipos_ocorrencia_map[$tipo['cto_id']] = htmlspecialchars($tipo['cto_tipo_ocorrencia']);
    }
} catch (PDOException $e) {
    die("Erro ao recuperar tipos de ocorrência: " . $e->getMessage());
}

$PERFIL_ADMIN = 1;
$perfil_id = $global_admin_perfil ?? 0;
$pode_editar = ((int) $perfil_id === $PERFIL_ADMIN);

function renderOcorrenciaCard($data, $is_admin_card, $tipos_map = [], $status_atual = 1)
{
    if (!$data)
        return;

    $card_class = $is_admin_card ? 'border-primary' : 'border-secondary';
    $header_class = $is_admin_card ? 'text-primary' : 'text-secondary';

    $criador_nome = htmlspecialchars($data['criador_nome'] ?? 'Desconhecido');
    $data_cad = isset($data['oco_data_cad']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['oco_data_cad']))) : 'N/A';
    $editor_nome = htmlspecialchars($data['editor_nome'] ?? 'Admin');
    $data_edicao = isset($data['oco_data_edicao']) ? htmlspecialchars(date('d/m/Y H:i', strtotime($data['oco_data_edicao']))) : 'N/A';

    $obs = nl2br(htmlspecialchars($data['oco_obs'] ?? 'N/A'));
    $parecer = nl2br(htmlspecialchars($data['oco_parecer_tecnico'] ?? ''));
    $carga_horaria = $data['oco_carga_horaria_calculada'] ?? null;

    $status_id_do_card = $data['oco_status'] ?? 1;

    $tipo_versao_label = $is_admin_card ? 'Revisado' : 'Original';
    $badge_cor = $is_admin_card ? 'bg-primary' : 'bg-secondary';

    $oco_tipo_ids = $data['oco_tipo_ocorrencia'] ?? '';
    $tipos_selecionados_html = '';

    if (!empty($oco_tipo_ids) && !empty($tipos_map)) {
        $ids_array = explode(',', $oco_tipo_ids);
        $nomes_tipos = [];
        foreach ($ids_array as $id) {
            $id = trim($id);
            if (isset($tipos_map[$id])) {
                $nomes_tipos[] = '<span class="badge ' . $badge_cor . '">' . $tipos_map[$id] . '</span>';
            }
        }
        if (!empty($nomes_tipos)) {
            $tipos_selecionados_html = '
                <div class="mt-3">
                    <strong>Tipo(s) de Ocorrência (' . $tipo_versao_label . '):</strong>
                    <div class="d-flex flex-wrap gap-2 mt-1">' . implode(' ', $nomes_tipos) . '</div>
                </div>';
        }
    }

    $status_badge = '';
    if (!$is_admin_card) {
        if ($status_atual == 2) {
            $status_badge = '<span class="badge bg-success">VALIDADA</span>';
        } elseif ($status_atual == 3) {
            $status_badge = '<span class="badge bg-danger">REJEITADA</span>';
        } else {
            $status_badge = '<span class="badge bg-warning text-dark">PENDENTE</span>';
        }
    }

    echo '
    <div class="card ' . $card_class . ' mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 ' . $header_class . '">' . ($is_admin_card ? 'Dados da Revisão Técnica' : 'Lançamento Original do Operador') . '</h6>
                ' . $status_badge . '
            </div>
        </div>
        <div class="card-body">
            ' . $tipos_selecionados_html . '
            <div class="row g-2 mt-3">
                <div class="col-md-6">
                    <strong>Início Realizado:</strong>
                    ' . (!empty($data['oco_hora_inicio_realizado']) ? htmlspecialchars(date('H:i', strtotime($data['oco_hora_inicio_realizado']))) : 'N/A') . '
                </div>
                <div class="col-md-6">
                    <strong>Término Realizado:</strong>
                    ' . (!empty($data['oco_hora_fim_realizado']) ? htmlspecialchars(date('H:i', strtotime($data['oco_hora_fim_realizado']))) : 'N/A') . '
                </div>
            </div>
            ' . ((!$is_admin_card && $obs) ? '
                <div class="mt-3">
                    <strong>Observações:</strong> ' . $obs . '
                    <div class="text-muted small mt-1">Registrado por: ' . $criador_nome . ' em ' . $data_cad . '</div>
                </div>' : '') . '
            
            ' . (($status_id_do_card == 2 && $carga_horaria) ?
        ' <div class="mt-3">
                    <strong>Carga Horária Calculada:</strong> ' . htmlspecialchars(date('H:i', strtotime($carga_horaria))) . '
                </div>'
        : '') . '
        </div>
    </div>';

    if ($is_admin_card && $parecer) {
        echo '
        <div class="p-3 mb-4 bg-light border rounded">
            <strong>Parecer Técnico:</strong> ' . $parecer . '
            <div class="text-muted small mt-1">Por: ' . $editor_nome . ' em ' . $data_edicao . '</div>
        </div>';
    }
}
?>
<!-- 
<div class="row breadcrumb_painel">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Análise de Ocorrência #<?= htmlspecialchars($ocorrencia_atual['oco_codigo']) ?></h4>
        </div>
    </div>
</div> -->

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header ">
                <div class="d-flex justify-content-between">
                    <h6 class="mb-sm-0">Análise de Ocorrência #<?= htmlspecialchars($ocorrencia_atual['oco_codigo']) ?>
                    </h6>

                    <div class="d-flex justify-content-end gap-2">
                        <?php if ($pode_editar && $ocorrencia_atual['oco_status'] == 1): ?>
                            <form method="POST" action="../router/web.php?r=Ocorrenc">
                                <input type="hidden" name="acao" value="validar">
                                <input type="hidden" name="oco_id"
                                    value="<?= htmlspecialchars($ocorrencia_atual['oco_id']) ?>">
                                <button type="submit" class="btn btn-success">
                                    <i class="ri-check-line"></i> Validar
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($pode_editar):
                            $dados_originais_para_modal = $versao_original_absoluta ?: $ocorrencia_atual;
                            ?>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#modal_admin_ocorrencia"
                                data-bs-oco_id="<?= htmlspecialchars($ocorrencia_atual['oco_id']) ?>"
                                data-bs-oco_res_id="<?= htmlspecialchars($ocorrencia_atual['oco_res_id']) ?>"
                                data-bs-oco_status="<?= htmlspecialchars($ocorrencia_atual['oco_status']) ?>"
                                data-bs-oco_parecer_tecnico="<?= htmlspecialchars($ocorrencia_atual['oco_parecer_tecnico'] ?? '') ?>"
                                data-bs-oco_tipo_ocorrencia="<?= htmlspecialchars($dados_originais_para_modal['oco_tipo_ocorrencia'] ?? '') ?>"
                                data-bs-oco_hora_inicio_realizado="<?= htmlspecialchars($dados_originais_para_modal['oco_hora_inicio_realizado'] ?? '') ?>"
                                data-bs-oco_hora_fim_realizado="<?= htmlspecialchars($dados_originais_para_modal['oco_hora_fim_realizado'] ?? '') ?>"
                                data-bs-oco_obs="<?= htmlspecialchars($dados_originais_para_modal['oco_obs'] ?? '') ?>">
                                <i class="ri-pencil-line"></i> Editar / Validar
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <?php
                // Seção 1: Card do Operador (sempre exibido)
                $dados_operador_para_exibir = $versao_historico_recente ?: $ocorrencia_atual;
                renderOcorrenciaCard($dados_operador_para_exibir, false, $tipos_ocorrencia_map, $ocorrencia_atual['oco_status']);

                // ==============================================================================
                // CORREÇÃO: LÓGICA INTELIGENTE PARA MOSTRAR O CARD DO ADMIN
                // ==============================================================================
                $mostrar_secao_admin = false; // Começa como falso
                
                // CORREÇÃO: A condição agora verifica se $versao_historico_recente é um array válido
                if ($versao_historico_recente) {
                    // Compara campos-chave para ver se houve uma mudança real
                    if (
                        trim($ocorrencia_atual['oco_tipo_ocorrencia']) != trim($versao_historico_recente['oco_tipo_ocorrencia']) ||
                        $ocorrencia_atual['oco_hora_inicio_realizado'] != $versao_historico_recente['oco_hora_inicio_realizado'] ||
                        $ocorrencia_atual['oco_hora_fim_realizado'] != $versao_historico_recente['oco_hora_fim_realizado'] ||
                        !empty($ocorrencia_atual['oco_parecer_tecnico'])
                    ) {
                        $mostrar_secao_admin = true;
                    }
                }

                if ($mostrar_secao_admin) {
                    renderOcorrenciaCard($ocorrencia_atual, true, $tipos_ocorrencia_map, $ocorrencia_atual['oco_status']);
                }
                // ==============================================================================
                ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/modal/modal_ocorrencia.php'; ?>
<?php include 'includes/footer.php'; ?>