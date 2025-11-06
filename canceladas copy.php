<?php include 'includes/header.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
// ----------------------------------------------------
// Lógica de consulta para a TABELA PRINCIPAL (Canceladas)
// ----------------------------------------------------
global $conn;
global $global_user_id;

$solicitacoes_canceladas = [];
$debug_msg = '';

//  -- 1. SOLICITAÇÕES CANCELADAS (Status 8 ou 9)
//         SELECT 
//             s.solic_id AS id_alvo, 
//             s.solic_codigo AS codigo_alvo,
//             'solicitacao' AS tipo_alvo,
//             -- Componente da SOLICITACAO via JOIN
//             cc_s.compc_componente AS componente_comp,
//             s.solic_nome_atividade AS atividade_nome,
//             s.solic_nome_prof_resp AS professor_resp,
//             s.solic_data_cad AS data_cadastro,
//             -- COLUNAS DE STATUS SEM A COR
//             st.stsolic_status_simples AS status_nome 
//         FROM solicitacao s
//         JOIN solicitacao_status ss ON ss.solic_sta_solic_id = s.solic_id
//         JOIN status_solicitacao st ON st.stsolic_id = ss.solic_sta_status
//         -- NOVO JOIN para buscar o nome do componente
//         LEFT JOIN componente_curricular AS cc_s ON cc_s.compc_id = s.solic_comp_curric
//         WHERE s.solic_cad_por = :user_id_s 
//         AND ss.solic_sta_status IN (8, 9)

//         UNION ALL

//         -- 2. RESERVAS CANCELADAS (Status 8 ou 9)
//         SELECT 
//             r.res_id AS id_alvo, 
//             r.res_codigo AS codigo_alvo,
//             'reserva' AS tipo_alvo,
//             -- Componente da RESERVA via JOIN
//             cc_r.compc_componente AS componente_comp,
//             r.res_nome_atividade AS atividade_nome,
//             r.res_professor AS professor_resp,
//             r.res_data_cad AS data_cadastro,
//             -- COLUNAS DE STATUS SEM A COR
//             st.stsolic_status_simples AS status_nome
//         FROM reservas r
//         JOIN status_solicitacao st ON st.stsolic_id = r.res_status 
//         -- NOVO JOIN para buscar o nome do componente
//         LEFT JOIN componente_curricular AS cc_r ON cc_r.compc_id = r.res_componente_atividade
//         WHERE r.res_user_id = :user_id_r 
//         AND r.res_status IN (8, 9)

//         ORDER BY data_cadastro DESC

try {

  // === CONSULTA UNIFICADA (UNION ALL) PARA BUSCAR APENAS STATUS 8 e 9 ===
  $sql = "
       -- 1. SOLICITAÇÕES CANCELADAS (Status 8 ou 9)
        SELECT 
            s.solic_id AS id_alvo, 
            s.solic_codigo AS codigo_alvo,
            'solicitacao' AS tipo_alvo,
            -- Componente da SOLICITACAO via JOIN
            cc_s.compc_componente AS componente_comp,
            s.solic_nome_atividade AS atividade_nome,
            s.solic_nome_prof_resp AS professor_resp,
            s.solic_data_cad AS data_cadastro,
            -- COLUNAS DE STATUS SEM A COR
            st.stsolic_status_simples AS status_nome 
        FROM solicitacao s
        JOIN solicitacao_status ss ON ss.solic_sta_solic_id = s.solic_id
        JOIN status_solicitacao st ON st.stsolic_id = ss.solic_sta_status
        -- NOVO JOIN para buscar o nome do componente
        LEFT JOIN componente_curricular AS cc_s ON cc_s.compc_id = s.solic_comp_curric
        WHERE s.solic_cad_por = :user_id_s 
        AND ss.solic_sta_status IN (8, 9)

        UNION ALL

        -- 2. RESERVAS CANCELADAS (Status 8 ou 9) - CORRIGIDO PARA LIDAR COM res_status NULL
        SELECT 
            r.res_id AS id_alvo, 
            r.res_codigo AS codigo_alvo,
            'reserva' AS tipo_alvo,
            -- Componente da RESERVA via JOIN
            cc_r.compc_componente AS componente_comp,
            r.res_nome_atividade AS atividade_nome,
            r.res_professor AS professor_resp,
            r.res_data_cad AS data_cadastro,
            -- COLUNAS DE STATUS SEM A COR (Usa COALESCE para exibir status quando é NULL, já que o JOIN falhou)
            COALESCE(st.stsolic_status_simples, 'Status Inválido/Nulo') AS status_nome -- 1. COALESCE para exibir o nome
        FROM reservas r
        -- MUDANÇA PRINCIPAL: Usar LEFT JOIN para incluir reservas mesmo que r.res_status seja NULL
        LEFT JOIN status_solicitacao st ON st.stsolic_id = r.res_status 
        -- NOVO JOIN para buscar o nome do componente
        LEFT JOIN componente_curricular AS cc_r ON cc_r.compc_id = r.res_componente_atividade
        WHERE r.res_user_id = :user_id_r 
        AND (r.res_status IN (8, 9) OR r.res_status IS NULL) -- 2. Inclui registros onde o status é NULL
        
        ORDER BY data_cadastro DESC
    ";

  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':user_id_s', $global_user_id, PDO::PARAM_STR);
  $stmt->bindValue(':user_id_r', $global_user_id, PDO::PARAM_STR);
  $stmt->execute();

  $solicitacoes_canceladas = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // =======================================================
  // BLOCO DE DEBUG (MANTIDO)
  // =======================================================
  if ($stmt->errorCode() !== '00000') {
    $errorInfo = $stmt->errorInfo();
    $debug_msg = "ERRO SQL: " . htmlspecialchars($errorInfo[2] ?? "Desconhecido") . "<br>USER ID: " . htmlspecialchars($global_user_id);
  }

} catch (PDOException $e) {
  error_log("Erro ao buscar solicitações canceladas: " . $e->getMessage());
  $debug_msg = "Erro ao buscar solicitações: " . htmlspecialchars($e->getMessage());
}
?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

<div class="row breadcrumb_painel">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Solicitações Canceladas</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
          <li class="breadcrumb-item active">Solicitações Canceladas</li>
        </ol>
      </div>
    </div>
    <?php if (!empty($debug_msg)): ?>
      <div class='alert alert-danger mt-2'>**DEBUG:** <?= $debug_msg ?></div>
    <?php endif; ?>

  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-sm-6 text-sm-start text-center">
            <h5 class="card-title mb-0">Lista de Solicitações Canceladas</h5>
          </div>

          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button type="button" class="btn botao botao_amarelo waves-effect" data-bs-toggle="modal"
              data-bs-target="#modal_nova_solicitacao_cancelamento">
              <i class="ri-close-circle-line align-bottom me-1"></i> Nova Solicitação de Cancelamento
            </button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <table id="tab_solic_canceladass" class="table dt-responsive  align-middle" style="width:100%">
          <thead class="table-light">
            <tr>
              <th data-priority="1">Código</th>
              <th>Professor Responsável</th>
              <th>Componente/Atividade</th>
              <th data-priority="2">Data Cadastro</th>
              <th data-priority="3">Status</th>
              <!-- <th>Ações</th> -->
            </tr>
          </thead>
          <tbody>
            <?php
            if (!empty($solicitacoes_canceladas)) {
              foreach ($solicitacoes_canceladas as $solic) {

                // Cor do status é forçada para 'danger' (vermelho) já que a coluna foi removida do SQL.
                $status_color = 'badge bg_info_vermelho';
                $stsolic_status = htmlspecialchars($solic['status_nome'] ?? 'Desconhecido');

                // Prioriza o Componente via JOIN, usa Atividade como fallback
                $componente = htmlspecialchars($solic['componente_comp'] ?: $solic['atividade_nome']);

                $data_cad_formatada = (new DateTime($solic['data_cadastro']))->format('d/m/Y H:i');
                $professor_resp = htmlspecialchars($solic['professor_resp'] ?? 'N/A');

                $detalhes_link = "solicitacao_analise.php?i=" . urlencode($solic['id_alvo']) . "&tipo=" . urlencode($solic['tipo_alvo']) . "&tab=cancelamento";
                ?>
                <tr data-href="<?= $detalhes_link ?>">
                  <td scope="row"><strong><?= htmlspecialchars($solic['codigo_alvo']) ?></strong></td>
                  <td><?= $professor_resp ?></td>
                  <td><?= $componente ?></td>
                  <td><?= $data_cad_formatada ?></td>
                  <td scope="row"><span class="badge bg-<?= $status_color ?>"><?= $stsolic_status ?></span>
                  </td>

                </tr>
                <?php
              }
            } else {
              echo '<tr><td colspan="6" class="text-center">Nenhuma solicitação cancelada encontrada.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php';
include 'includes/modal/modal_nova_solicitacao_cancelamento.php';
?>