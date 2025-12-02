<?php
// Inclui cabeçalho (deve ter session_start(), $conn e as variáveis globais de perfil)
include 'includes/header.php';

// IDs dos perfis para referência
$PERFIL_ADMIN = 1;
$PERFIL_OPERADOR = 2; // Assumindo que o ID do perfil de Operador é 2

$perfil_id = $global_admin_perfil ?? 0;
?>


<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg"></div>
</div>

<div class="row breadcrumb_painel">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Canceladas</h4>
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
        <h5 class="card-title mb-0">Lista de Canceladas </ol>
          </ol>
        </h5>
      </div>
      <div class="card-body p-0">
        <table id="solic_canc_p3ndentes" class="table dt-responsive align-middle" style="width:100%">
          <thead>
            <tr>
              <th>ID Pedido</th>
              <th>Tipo</th>
              <th>Cód. Alvo</th>
              <th>Atividade/Componente</th>
              <th>Solicitante</th>
              <th>Data Pedido</th>
              <th>Motivo (Usuário)</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("
        SELECT 
            sc.solcanc_id,
            sc.solcanc_tipo,
            sc.solcanc_motivo,
            sc.solcanc_data_solic, 
            sc.solcanc_solic_id, 
            sc.solcanc_res_id,   
            u.user_nome,
            u.user_email,
            COALESCE(s.solic_codigo, r.res_codigo) as codigo_alvo, 
            COALESCE(cc.compc_componente, s.solic_nome_atividade, 'Reserva avulsa') as nome_atividade,
            COALESCE(r.res_data, (SELECT MIN(res_data) FROM reservas WHERE res_solic_id = sc.solcanc_solic_id)) as data_alvo
        FROM solicitacao_cancelamento sc
        INNER JOIN usuarios u ON u.user_id = sc.solcanc_usuario_id
        LEFT JOIN solicitacao s ON s.solic_id = sc.solcanc_solic_id
        LEFT JOIN reservas r ON r.res_id = sc.solcanc_res_id
        LEFT JOIN componente_curricular cc ON cc.compc_id = r.res_componente_atividade OR cc.compc_id = s.solic_comp_curric
        
        WHERE sc.solcanc_status = 1 
        ORDER BY sc.solcanc_data_solic ASC
    ");
              $stmt->execute();

              // INÍCIO DA MUDANÇA: USANDO WHILE PARA ITERAR
              while ($solic = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr role="button" data-href='<?= $rota_detalhes ?>'>
                  <td scope="row"><?= htmlspecialchars($solic['solcanc_id']) ?></td>
                  <td><span class="badge bg-primary"><?= htmlspecialchars($solic['solcanc_tipo']) ?></span>
                  </td>

                  <td scope="row">
                    <a href="<?= $rota_detalhes ?>" target="_blank" class="fw-bold text-primary">
                      <?= htmlspecialchars($solic['codigo_alvo']) ?>
                      <i class="ri-share-box-line align-bottom"></i>
                    </a>
                  </td>

                  <td scope="row"><?= htmlspecialchars($solic['nome_atividade']) ?></td>
                  <td><?= htmlspecialchars($solic['user_nome']) ?></td>
                  <td><?= date('d/m/Y H:i', strtotime($solic['solcanc_data_solic'])) ?></td>
                  <td scope="row">

                    <?= htmlspecialchars(substr($solic['solcanc_motivo'], 0, 50)) . (strlen($solic['solcanc_motivo']) > 50 ? '...' : '') ?>
                  </td>
                  <td>
                    <div class="dropdown d-inline-block">
                      <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                          <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#modal_confirmacao_cancelamento" data-solcanc-id="<?= $solic['solcanc_id'] ?>"
                            data-alvo="<?= htmlspecialchars($solic['codigo_alvo'] . ' - ' . $solic['nome_atividade']) ?>">
                            <i class="ri-check-line align-bottom me-2 text-success"></i> Confirmar
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="#" data-bs-toggle="modal"
                            data-bs-target="#modal_negacao_cancelamento" data-solcanc-id="<?= $solic['solcanc_id'] ?>"
                            data-alvo="<?= htmlspecialchars($solic['codigo_alvo'] . ' - ' . $solic['nome_atividade']) ?>">
                            <i class="ri-close-line align-bottom me-2 text-danger"></i> Negar
                          </a>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
                <?php
              } // FIM DO WHILE
            } catch (PDOException $e) {
              echo "Erro: " . $e->getMessage();
            } ?>
          </tbody>
        </table>
      </div>
      <script>
        $(document).ready(function () {
          $('table').on('click', 'tr', function (e) {
            if ($(e.target).closest('a, .btn, .no-link').length === 0) {
              const href = $(this).data('href');
              if (href) {
                window.location.href = href;
              }
            }
          });
        });
      </script>
    </div>
  </div>
</div>


<?php
// Assumindo que o arquivo de modal será incluído aqui
include 'includes/modal/modal_confirmacao_cancelamento.php';
include 'includes/footer.php';
?>