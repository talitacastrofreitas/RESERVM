<?php
include 'includes/header.php';

// Inicializa variáveis
$totalPaginas = 1;
$cancelamentos = [];

try {
    global $conn;
    // === PAGINAÇÃO BÁSICA ===
    $limite = 100; // máximo de registros por página
    $pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) && $_GET['pagina'] > 0 ? (int) $_GET['pagina'] : 1;
    $offset = ($pagina - 1) * $limite;

    // === CONSULTA OTIMIZADA ===

    $sql = "
     SELECT
    c.solcanc_id,
    c.solcanc_tipo,
    c.solcanc_solic_id,
    c.solcanc_res_id,
    u.user_nome AS usuario_nome,
    c.solcanc_data_solic,
    c.solcanc_motivo AS motivo_display, -- REMOVIDO: solcanc_motivo_negacao e Motivo
    CASE 
        WHEN c.solcanc_status = 2 THEN ISNULL(r_status.res_status, s_status.solic_sta_status)
        WHEN c.solcanc_status = 3 THEN c.solcanc_status
        ELSE c.solcanc_status
    END AS status_display,

    -- *** COLUNA UNIFICADA PARA O CÓDIGO ***
    CASE 
        WHEN c.solcanc_tipo = 'Reserva' THEN r.res_codigo
        WHEN c.solcanc_tipo = 'Solicitacao' THEN s.solic_codigo
        ELSE NULL
    END AS codigo_display,
    
    r.res_nome_atividade AS res_nome,
    s.solic_nome_atividade AS solic_nome,
    cc_r.compc_componente AS compc_reserva,
    cc_s.compc_componente AS compc_solic

FROM solicitacao_cancelamento AS c
LEFT JOIN Usuarios AS u ON u.user_id = c.solcanc_usuario_id
LEFT JOIN Reservas AS r ON r.res_id = c.solcanc_res_id
LEFT JOIN Solicitacao AS s ON s.solic_id = c.solcanc_solic_id
LEFT JOIN Reservas AS r_status ON r_status.res_id = c.solcanc_res_id AND c.solcanc_status = 2
LEFT JOIN solicitacao_status AS s_status ON s_status.solic_sta_solic_id = c.solcanc_solic_id AND c.solcanc_status = 2
LEFT JOIN componente_curricular AS cc_r ON cc_r.compc_id = r.res_componente_atividade
LEFT JOIN componente_curricular AS cc_s ON cc_s.compc_id = s.solic_comp_curric
WHERE c.solcanc_status IN (1, 2, 3) 

UNION ALL

-- === 1. RESERVAS CANCELADAS (Status 8 ou 9) SEM UM PEDIDO DE CANCELAMENTO ===
SELECT
    NULL AS solcanc_id, 
    'reserva' AS solcanc_tipo,
    r.res_solic_id AS solcanc_solic_id, 
    r.res_id AS solcanc_res_id,
    u.user_nome AS usuario_nome,
    r.res_data_cad AS solcanc_data_solic, 
    r.res_motivo_cancelamento AS motivo_display, -- REMOVIDO: res_motivo_cancelamento
    r.res_status AS status_display,
    
    -- *** COLUNA UNIFICADA PARA O CÓDIGO ***
    r.res_codigo AS codigo_display,
    
    r.res_nome_atividade AS res_nome,
    NULL AS solic_nome,
    cc_r.compc_componente AS compc_reserva,
    NULL AS compc_solic
FROM Reservas AS r
LEFT JOIN Usuarios AS u ON u.user_id = r.res_user_id
LEFT JOIN componente_curricular AS cc_r ON cc_r.compc_id = r.res_componente_atividade
WHERE r.res_status IN (8, 9) 
    AND r.res_solic_cancelamento_id IS NULL

UNION ALL

-- === 2. SOLICITAÇÕES CANCELADAS (Status 8 ou 9) SEM UM PEDIDO DE CANCELAMENTO ===
SELECT
    NULL AS solcanc_id, 
    'solicitacao' AS solcanc_tipo,
    s.solic_id AS solcanc_solic_id,
    NULL AS solcanc_res_id,
    u.user_nome AS usuario_nome,
    s.solic_data_cad AS solcanc_data_solic, 
    NULL AS motivo_display, -- REMOVIDO: solic_motivo_cancelamento
    ss.solic_sta_status AS status_display,
    
    -- *** COLUNA UNIFICADA PARA O CÓDIGO ***
    s.solic_codigo AS codigo_display,
    
    NULL AS res_nome,
    s.solic_nome_atividade AS solic_nome,
    NULL AS compc_reserva,
    cc_s.compc_componente AS compc_solic
FROM Solicitacao AS s
LEFT JOIN Usuarios AS u ON u.user_id = s.solic_cad_por
LEFT JOIN solicitacao_status AS ss ON ss.solic_sta_solic_id = s.solic_id
LEFT JOIN componente_curricular AS cc_s ON cc_s.compc_id = s.solic_comp_curric
WHERE ss.solic_sta_status IN (8, 9)
    AND s.solic_solic_cancelamento_id IS NULL

ORDER BY solcanc_data_solic DESC
OFFSET :offset ROWS FETCH NEXT :limite ROWS ONLY;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();

    $cancelamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $totalQuery = $conn->query("SELECT COUNT(*) FROM solicitacao_cancelamento");
    $totalRegistros = (int) $totalQuery->fetchColumn();
    $totalPaginas = ceil($totalRegistros / $limite);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erro ao carregar cancelamentos: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>


<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg"></div>
</div>

<div class="row breadcrumb_painel">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Solicitações Canceladas</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="painel.php">Solicitações</a></li>
                    <li class="breadcrumb-item active">Cancelado</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista de Canceladas</h5>
            </div>

            <div class="card-body p-0">
                <table id="solic_canc_pendentes" class="table dt-responsive align-middle" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Componente / Atividade</th>
                            <th>Usuário</th>
                            <th>Motivo</th>
                            <th>Data da Solicitação</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (!empty($cancelamentos)): ?>
                            <?php foreach ($cancelamentos as $c): ?>


                                <?php
                                // Define o ID do item alvo (Solicitação ou Reserva)
                                $target_id = ($c['solcanc_tipo'] === 'solicitacao' || $c['solcanc_tipo'] === 'Solicitacao')
                                    ? ($c['solcanc_solic_id'] ?? $c['solic_id'])
                                    : ($c['solcanc_solic_id'] ?? $c['solic_id']); // Corrigido para usar solcanc_res_id
                        
                                // O ID do pedido de cancelamento é a chave para a página de análise, se existir.
                                // Se o registro for um 'Cancelado' direto (sem solcanc_id), talvez o link deva ser desabilitado ou apontar para a reserva/solicitação.
                                $link_id = $c['solcanc_id'] ?? $target_id;
                                ?>


                                <tr role="button" data-href='solicitacao_analise.php?i=<?= htmlspecialchars($target_id) ?>'>

                                    <td scope="row"><strong>
                                            <?= htmlspecialchars($c['codigo_display'] ?? '-') ?></strong>
                                    </td>
                                    
                                  <?php
$tipo = $c['solcanc_tipo'];
$badge_class = '';
$nome_exibicao = '';

// Lógica para definir a classe do Badge e o nome a ser exibido
switch (strtoupper($tipo)) {
    case 'SOLICITACAO':
        $badge_class = 'bg_info_azul_escuro'; // Cor azul
        $nome_exibicao = 'SOLICITAÇÃO';
        break;
    case 'RESERVA':
        $badge_class = 'bg_info_roxo'; // Cor vermelha
        $nome_exibicao = 'RESERVA';
        break;
   
    default:
        $badge_class = 'bg-light text-dark'; // Padrão
        $nome_exibicao = htmlspecialchars($tipo);
}
?>
  

                                    <td scope="row" class="text-uppercase">
    <span class="badge <?= $badge_class ?>"><?= $nome_exibicao ?></span>
</td>

                                    <td scope="row">
                                        <?php
                                        // Prioriza o nome do Componente (JOIN) e usa o nome da Atividade como fallback
                                        if ($c['solcanc_tipo'] === 'reserva') {
                                            echo htmlspecialchars($c['compc_reserva'] ?? $c['res_nome'] ?? '-');
                                        } else {
                                            echo htmlspecialchars($c['compc_solic'] ?? $c['solic_nome'] ?? '-');
                                        }
                                        ?>
                                    </td>
                                    <td scope="row"><?= htmlspecialchars($c['usuario_nome'] ?? '-') ?></td>
                                    <td scope="row">
                                        <?php if (!empty($c['motivo_display'])): ?>
                                            <?php
                                            $motivo_completo = htmlspecialchars($c['motivo_display']);
                                            $motivo_tooltip = substr($motivo_completo, 0, 100) . (strlen($motivo_completo) > 100 ? '...' : '');
                                            ?>

                                         
  <button type="button"
        class="btn "
        data-bs-toggle="modal"
        data-bs-target="#modal_motivo_completo"
        data-motivo-completo="<?= $motivo_completo ?>"
        title="<?= $motivo_tooltip ?>">
 
       <i class="fa-solid fa-comment-dots fa-lg" style="color: #0461ad;"></i>
</button>


                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td scope="row"><?= date('d/m/Y H:i', strtotime($c['solcanc_data_solic'])) ?></td>
                                    <td scope="row">
                                        <?php
                                        switch ($c['status_display']) { // *** USANDO status_display ***
                                            case 1:
                                                echo "<span class='badge bg_info_laranja'>Pendente</span>";
                                                break;
                                            case 8:
                                                echo "<span class='badge bg_info_vermelho'>Cancelado fora do prazo</span>";
                                                break;
                                            case 9:
                                                echo "<span class='badge bg_info_vermelho'>Cancelado dentro do prazo</span>";
                                                break;
                                            case 3:
                                                echo "<span class='badge bg_info_vermelho'>Pedido Negado</span>";
                                                break;
                                            default:
                                                echo "<span class='badge bg-secondary'>Desconhecido</span>";
                                        }
                                        ?>
                                    </td>
                                    <td scope="row">
                                        <?php
                                        // Lógica para o data-alvo do modal: Código + Componente/Atividade
                                        if ($c['solcanc_tipo'] === 'reserva') {
                                            $codigo_alvo = $c['codigo_display'] ?? '-';
                                            $nome_completo_alvo = $c['compc_reserva'] ?? $c['res_nome'] ?? '';
                                        } else {
                                            $codigo_alvo = $c['codigo_display'] ?? '-';
                                            $nome_completo_alvo = $c['compc_solic'] ?? $c['solic_nome'] ?? '';
                                        }

                                        $data_alvo_modal = htmlspecialchars($codigo_alvo . ($nome_completo_alvo ? ' - ' . $nome_completo_alvo : ''));

                                        // Define se as ações estão disponíveis (somente se status for 1 - Pendente)
                                        $acoes_disponiveis = ($c['status_display'] == 1);
                                        $disabled_class = $acoes_disponiveis ? '' : 'disabled text-muted';
                                        $disabled_attrs = $acoes_disponiveis ? 'href="#" data-bs-toggle="modal"' : 'href="javascript:void(0)"';
                                        ?>

                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">

                                                <li>
                                                    <a class="dropdown-item edit-item-btn <?= $disabled_class ?>" <?= $disabled_attrs ?>
                                                        data-bs-target="#modal_confirmacao_cancelamento"
                                                        data-solcanc-id="<?= htmlspecialchars($c['solcanc_id']) ?>"
                                                        data-alvo="<?= $data_alvo_modal ?>">
                                                        <i class="fa-solid fa-ban me-2"></i>Cancelar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn  <?= $disabled_class ?>" <?= $disabled_attrs ?>
                                                        data-bs-target="#modal_negacao_cancelamento"
                                                        data-solcanc-id="<?= htmlspecialchars($c['solcanc_id']) ?>"
                                                        data-alvo="<?= $data_alvo_modal ?>">
                                                       <i class="fa-solid fa-xmark me-2"></i>Indeferir
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Nenhuma solicitação de cancelamento
                                    encontrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPaginas > 1): ?>
                <nav class="p-3">
                    <ul class="pagination justify-content-center mt-3">
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade modal_padrao" id="modal_confirmacao_cancelamento" tabindex="-1"
    aria-labelledby="modal_confirmacao_cancelamento_label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">


            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title" id="modal_confirmacao_cancelamento_label">Confirmar Cancelamento</h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form id="ValidaBotaoProgressDeferir" method="POST"
                    action="controller/controller_solicitacao_cancelamento.php" class="form_solicitacao">
                    <input type="hidden" name="acao" value="confirmar">
                    <input type="hidden" name="solcanc_id" id="confirmar_solcanc_id">

                    <div class="mb-3">
                        <span><strong id="confirmar_alvo"></strong>
                        </span>
                        <div class="label_info label_info_verde mt-0">O status do cancelamento (“Cancelado dentro do
                            prazo”
                            ou “Cancelado fora do prazo”) será determinado automaticamente pelo sistema com base na
                            diferença entre a data da solicitação e a data da reserva.</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirmar_obs_admin" class="form-label">Observação</label>
                        <textarea class="form-control" id="confirmar_obs_admin" name="obs_admin" rows="6"></textarea>
                    </div>


                    <div class="col-lg-12">
                        <div class="hstack gap-3 align-items-center justify-content-end mt-2">

                            <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal"
                                data-bs-toggle="button">Cancelar</button>
                            <button type="submit" class="btn botao botao_verde waves-effect">Confirmar
                                cancelamento</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


<div class="modal fade modal_padrao" id="modal_negacao_cancelamento" tabindex="-1"
    aria-labelledby="modal_negacao_cancelamento_label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">


            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title" id="modal_negacao_cancelamento_label">Negar Cancelamento</h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form id="ValidaBotaoProgressIndeferir" method="POST"
                    action="controller/controller_solicitacao_cancelamento.php"
                    class="needs-validation form_solicitacao" novalidate>
                    <input type="hidden" name="acao" value="negar">
                    <input type="hidden" name="solcanc_id" id="negar_solcanc_id">

                    <div class="mb-3">
                        <span><strong id="negar_alvo"></strong></span>
                        <div class="label_info label_info_verde mt-0">Caso a solicitação de cancelamento seja negada, o
                            status da solicitação permanecerá inalterado.
                            O usuário solicitante será notificado por e-mail, contendo a informação sobre a negação do
                            pedido e o motivo detalhado da decisão.</div>
                    </div>

                    <div class="mb-3">
                        <label for="negar_motivo_negacao" class="form-label">Motivo <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="negar_motivo_negacao" name="motivo_negacao" rows="6"
                            required></textarea>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                    </div>

                    <div class="col-lg-12">
                        <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                            <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório
                            </p>
                            <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal"
                                data-bs-toggle="button">Cancelar</button>
                            <button type="submit" class="btn botao botao_vermelho waves-effect">Negar
                                cancelamento</button>
                        </div>
                    </div>
            </div>
            </form>
        </div>

    </div>
</div>


<!-- MODAL MOTIVO -->
<div class="modal fade modal_padrao" id="modal_motivo_completo" tabindex="-1"
    aria-labelledby="modal_motivo_completo_label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title" id="modal_motivo_completo_label">Motivo da Solicitação de Cancelamento</h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <!-- <label><strong>Motivo</strong></label> -->
                <textarea class="form-control text-break" rows="5" disabled name="motivo_completo_conteudo" id="motivo_completo_conteudo" class=""></textarea>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn botao botao_verde waves-effect" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

</div>
<script>
    // Configuração para o modal de Confirmação
    var confirmarModal = document.getElementById('modal_confirmacao_cancelamento');
    confirmarModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Botão que acionou o modal
        var solcancId = button.getAttribute('data-solcanc-id');
        var alvo = button.getAttribute('data-alvo');

        var modalTitle = confirmarModal.querySelector('.modal-title');
        var inputId = confirmarModal.querySelector('#confirmar_solcanc_id');
        var alvoStrong = confirmarModal.querySelector('#confirmar_alvo');

        inputId.value = solcancId;
        alvoStrong.textContent = alvo;
    });

    // Configuração para o modal de Negação
    var negarModal = document.getElementById('modal_negacao_cancelamento');
    negarModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Botão que acionou o modal
        var solcancId = button.getAttribute('data-solcanc-id');
        var alvo = button.getAttribute('data-alvo');

        var modalTitle = negarModal.querySelector('.modal-title');
        var inputId = negarModal.querySelector('#negar_solcanc_id');
        var alvoStrong = negarModal.querySelector('#negar_alvo');

        inputId.value = solcancId;
        alvoStrong.textContent = alvo;
    });
</script>


<script>
    $(document).ready(function () {

        // 1. Inicializa Tooltips (Balão ao passar o mouse)
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('a[data-bs-target="#modal_motivo_completo"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

        // 2. Transfere o motivo completo para o modal ao clicar no ícone
        var motivoModal = document.getElementById('modal_motivo_completo');
        motivoModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // O ícone clicado
            var motivoCompleto = button.getAttribute('data-motivo-completo');

            var contentParagraph = motivoModal.querySelector('#motivo_completo_conteudo');
            contentParagraph.innerHTML = motivoCompleto.replace(/\n/g, '<br>'); // Mantém quebras de linha

            // Força a remoção do tooltip se ele estiver ativo
            var tooltip = bootstrap.Tooltip.getInstance(button);
            if (tooltip) {
                tooltip.hide();
            }
        });

        // Clique na linha da tabela, com exceções
        $('table').on('click', 'tr', function (e) {
            // Ignora cliques em dropdowns ou controles de expansão
            if (
                $(e.target).closest('.dropdown').length > 0 ||
                $(e.target).closest('.fa-comment-dots').length > 0 ||
                $(e.target).closest('.dtr-control').length > 0
            ) {
                return;
            }

            // Vai para o link especificado no atributo data-href
            const href = $(this).data('href');
            if (href) {
                window.location.href = href;
            }
        });

        // Apenas por segurança, evita propagação em elementos específicos
        $(document).on('click', '.dropdown, td.dtr-control, .fa-comment-dots', function (e) {
            e.stopPropagation();
        });
    });
</script>
<?php

include 'includes/footer.php'; ?>