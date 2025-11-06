<?php
include 'includes/header.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
// ----------------------------------------------------
// Lógica de consulta para a TABELA PRINCIPAL (Canceladas)
// ----------------------------------------------------
global $conn;
global $global_user_id;

$solicitacoes_canceladas = [];
$sql = '';
$params = [];
$debug_msg = '';

// --- 1. BUSCAR NOME DO USUÁRIO LOGADO PARA USO EM DEBUG/EXIBIÇÃO ---
$global_user_nome = '';
if (!empty($global_user_id)) {
  try {
    $stmt_user_name = $conn->prepare("SELECT user_nome FROM Usuarios WHERE user_id = :user_id");
    $stmt_user_name->execute([':user_id' => $global_user_id]);
    $user_data = $stmt_user_name->fetch(PDO::FETCH_ASSOC);
    if ($user_data) {
      $global_user_nome = $user_data['user_nome'];
    }
  } catch (PDOException $e) {
    // Ignora erro, usa nome vazio
  }
}
// --------------------------------------------------------------------

try {

  // === CONSULTA UNIFICADA (UNION ALL) ===
  $sql = "
        -- 1. SOLICITAÇÕES CANCELADAS (Status 8 ou 9) - FILTRA PELO STATUS MAIS RECENTE
        SELECT 
            s.solic_id AS id_alvo, 
            s.solic_codigo AS codigo_alvo,
            'solicitacao' AS tipo_alvo,
            cc_s.compc_componente AS componente_comp,
            s.solic_nome_atividade AS atividade_nome,
            s.solic_nome_prof_resp AS professor_resp,
            s.solic_data_cad AS data_cadastro,
            st.stsolic_status_simples AS status_nome,
            -- Campos de debug vazios para o UNION ALL
            NULL AS res_user_id_debug,
            NULL AS res_status_debug
        FROM solicitacao s
        JOIN solicitacao_status ss ON ss.solic_sta_solic_id = s.solic_id
        JOIN (
            SELECT solic_sta_solic_id, MAX(solic_sta_data_cad) AS max_data
            FROM solicitacao_status
            GROUP BY solic_sta_solic_id
        ) AS UltimoStatus ON UltimoStatus.solic_sta_solic_id = ss.solic_sta_solic_id 
                         AND UltimoStatus.max_data = ss.solic_sta_data_cad
        
        JOIN status_solicitacao st ON st.stsolic_id = ss.solic_sta_status
        LEFT JOIN componente_curricular AS cc_s ON cc_s.compc_id = s.solic_comp_curric
        WHERE TRIM(s.solic_cad_por) = :user_id_s -- Aplica TRIM por segurança, se for CHAR
        AND ss.solic_sta_status IN (8, 9)

        UNION ALL

        -- 2. RESERVAS CANCELADAS (Status 8 ou 9) - JOINT NA SOLICITACAO PARA PEGAR O USER ID CORRETO
        SELECT 
            r.res_id AS id_alvo, 
            r.res_codigo AS codigo_alvo,
            'reserva' AS tipo_alvo,
            cc_r.compc_componente AS componente_comp,
            r.res_nome_atividade AS atividade_nome,
            r.res_professor AS professor_resp,
            r.res_data_cad AS data_cadastro,
            COALESCE(st.stsolic_status_simples, 'Status Inválido/Nulo') AS status_nome,
            -- NOVOS CAMPOS DE DEBUG PARA RESERVAS
            TRIM(s.solic_cad_por) AS res_user_id_debug, -- DEBUG: Usa o ID da Solicitacao
            r.res_status AS res_status_debug
        FROM reservas r
        -- NOVO JOIN: Liga a Reserva à Solicitação para obter o ID do criador
        JOIN solicitacao s ON s.solic_id = r.res_solic_id
        LEFT JOIN status_solicitacao st ON st.stsolic_id = r.res_status 
        LEFT JOIN componente_curricular AS cc_r ON cc_r.compc_id = r.res_componente_atividade
        WHERE r.res_status IN (8, 9) -- A reserva está cancelada
        AND TRIM(s.solic_cad_por) = :user_id_r -- FILTRA PELO ID DO CRIADOR DA SOLICITAÇÃO
        
        ORDER BY data_cadastro DESC
    ";

  $params = [
    ':user_id_s' => trim($global_user_id),
    // Aplica trim na variável PHP
    ':user_id_r' => trim($global_user_id)
  ];

  $stmt = $conn->prepare($sql);

  // Bind dos parâmetros
  $stmt->bindValue(':user_id_s', $params[':user_id_s'], PDO::PARAM_STR);
  $stmt->bindValue(':user_id_r', $params[':user_id_r'], PDO::PARAM_STR);

  $stmt->execute();

  $solicitacoes_canceladas = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // =======================================================
  // BLOCO DE DEBUG (Contagem e Erro)
  // =======================================================
  if ($stmt->errorCode() !== '00000') {
    $errorInfo = $stmt->errorInfo();
    $debug_msg = "ERRO SQL (APÓS EXECUÇÃO): " . htmlspecialchars($errorInfo[2] ?? "Desconhecido") . "<br>USER ID: " . htmlspecialchars($global_user_id);
  }

  $reserva_count = 0;
  $debug_reserva_data = [];
  $user_id_param = $params[':user_id_r'];

  foreach ($solicitacoes_canceladas as $solic) {
    if ($solic['tipo_alvo'] === 'reserva') {
      $reserva_count++;
      // Adiciona dados de debug da reserva
      $debug_reserva_data[] = [
        'Código' => $solic['codigo_alvo'],
        'Status_DB' => $solic['res_status_debug'],
        'User_ID_DB' => $solic['res_user_id_debug']
      ];
    }
  }




} catch (PDOException $e) {
  // Tratamento de erro aprimorado
  $error_message = "Erro ao buscar solicitações: " . htmlspecialchars($e->getMessage());
  $error_message .= "<br>SQL Usado: <pre>" . htmlspecialchars($sql) . "</pre>";
  $error_message .= "Parâmetros: " . htmlspecialchars(print_r($params, true));

  error_log("Erro no SQL de Cancelamento: " . $e->getMessage());
  $debug_msg = $error_message;

  $solicitacoes_canceladas = [];
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

    <?php
    // Exibe o DEBUG com informações críticas
    if (!empty($debug_msg)): ?>
      <div class='alert alert-danger mt-2'>**DEBUG:** <br><?= $debug_msg ?></div>
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
        <table id="tab_solic_canceladass" class="table dt-responsive align-middle" style="width:100%">
          <thead class="table-light">
            <tr>
              <th data-priority="1">Código</th>
              <th>Professor Responsável</th>
              <th>Componente/Atividade</th>
              <th data-priority="2">Data Cadastro</th>
              <th data-priority="3">Status</th>
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