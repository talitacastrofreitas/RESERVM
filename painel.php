<?php include 'includes/header.php'; ?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

<div class="row breadcrumb_painel">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Solicitações</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
          <li class="breadcrumb-item active">Solicitações</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-sm-6 text-sm-start text-center">
            <h5 class="card-title mb-0">Lista de Solicitações</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <a href="nova_solicitacao.php" class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0">+ Nova
              Solicitação</a>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_solic_user" class="table dt-responsive  align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-2">Código</span></th>
              <th><span class="me-2">Curso</span></th>
              <th><span class="me-2">Componente/Atividade</span></th>
              <th><span class="me-2">Professor/Responsável</span></th>
              <th><span class="me-2">Data Solicitação</span></th>
              <th><span class="me-2">Status</span></th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT
    solicitacao.*,
    solicitacao_status.*,
    status_solicitacao.*,
    componente_curricular.compc_componente,
    cursos.curs_curso,
    sub.data_proxima_reserva
FROM solicitacao
LEFT JOIN solicitacao_status ON solicitacao_status.solic_sta_solic_id = solicitacao.solic_id
LEFT JOIN status_solicitacao ON status_solicitacao.stsolic_id = solicitacao_status.solic_sta_status
LEFT JOIN componente_curricular ON componente_curricular.compc_id = solicitacao.solic_comp_curric
LEFT JOIN cursos ON cursos.curs_id = solicitacao.solic_curso
LEFT JOIN (
    SELECT
        res_solic_id,
        MAX(DATEADD(SECOND, DATEDIFF(SECOND, 0, res_hora_inicio), CAST(res_data AS DATETIME))) AS data_proxima_reserva
    FROM reservas
    WHERE res_status NOT IN (7, 8)
    AND DATEADD(SECOND, DATEDIFF(SECOND, 0, res_hora_inicio), CAST(res_data AS DATETIME)) > GETDATE()
    GROUP BY res_solic_id
) AS sub ON sub.res_solic_id = solicitacao.solic_id
WHERE solicitacao.solic_cad_por = :solic_cad_por");
              $stmt->execute([':solic_cad_por' => $global_user_id]);

              while ($row_solic = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract($row);
            
                $solic_id = $row_solic['solic_id'];
                $solic_codigo = $row_solic['solic_codigo'];
                $solic_nome_comp_ativ = $row_solic['solic_nome_comp_ativ'];
                $solic_nome_atividade = $row_solic['solic_nome_atividade'];
                $solic_nome_prof_resp = $row_solic['solic_nome_prof_resp'];
                $solic_data_cad = $row_solic['solic_data_cad'];
                $solic_sta_status = $row_solic['solic_sta_status'];
                $solic_etapa = $row_solic['solic_etapa'];
                $curs_curso = $row_solic['curs_curso'];
                $compc_componente = $row_solic['compc_componente'];
                $stsolic_status = $row_solic['stsolic_status_simples'];

                // CONFIGURAÇÃO DO STATUS
                $status_colors = [
                  1 => 'bg_info_laranja',
                  2 => 'bg_info_azul',
                  3 => 'bg_info_roxo',
                  4 => 'bg_info_verde',
                  5 => 'bg_info_roxo',
                  6 => 'bg_info_vermelho',
                  7 => 'bg_info_roxo',
                  8 => 'bg_info_vermelho',
                ];

                $status_color = $status_colors[$solic_sta_status] ?? 'bg_info_padrao';

                // DATE 
                // --- LÓGICA DE VALIDAÇÃO DE 48 HORAS COM BASE NA NOVA CONSULTA ---
                $link_disabled = true; // Assume que o botão será desabilitado
                $classe_link = 'link_cinza_claro disabled';

                // Primeiro, verifique se a solicitação pode ser cancelada (status 4)
                if ($solic_sta_status == 4) {

                  // Nova consulta para verificar se existe QUALQUER reserva com mais de 48h de antecedência
                  $sql_reserva_habilita = $conn->prepare("
            SELECT 1
            FROM reservas
            WHERE res_solic_id = :solic_id
            AND (res_status NOT IN (7, 8) OR res_status IS NULL)
            AND CAST(res_data AS DATETIME) + CAST(res_hora_inicio AS DATETIME) >= DATEADD(hour, 48, GETDATE())
          ");
                  $sql_reserva_habilita->execute([':solic_id' => $solic_id]);

                  $reserva_existe = $sql_reserva_habilita->fetchColumn();

                  // Se a consulta retornou pelo menos uma linha, habilita o botão
                  if ($reserva_existe) {
                    $link_disabled = false;
                    $classe_link = 'link_vermelho';
                  }
                }

                ?>
                <tr role="button" data-href='nova_solicitacao.php?st=1&i=<?= htmlspecialchars($solic_id) ?>'>

                  <th scope="row"><?= htmlspecialchars($solic_codigo) ?></th>
                  <td scope="row"><?= htmlspecialchars($curs_curso) ?></td>
                  <td scope="row">
                    <?= htmlspecialchars($compc_componente) ?>     <?= htmlspecialchars($solic_nome_atividade) ?>
                    <?= htmlspecialchars($solic_nome_comp_ativ) ?>
                  </td>
                  <td scope="row"><?= htmlspecialchars($solic_nome_prof_resp) ?></td>
                  <td scope="row" nowrap="nowrap"><span
                      class="hide_data"><?= htmlspecialchars(date('Ymd', strtotime($solic_data_cad))) ?></span><?= htmlspecialchars(date('d/m/Y H:i', strtotime($solic_data_cad))) ?>
                  </td>
                  <td scope="row"><span class="badge <?= $status_color ?>"><?= htmlspecialchars($stsolic_status) ?></span>
                  </td>
                  <td class="text-end">
                    <div class="dropdown dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($solic_etapa == 3) { ?>
                          <li><a href="router/web.php?r=SolicDuplic&i=<?= $solic_id ?>"
                              class="dropdown-item edit-item-btn clone-btn" title="Duplicar" disabled><i
                                class="fa-regular fa-clone me-2"></i> Duplicar</a></li>
                        <?php } else { ?>
                          <li><span class="dropdown-item edit-item-block-btn" title="Duplicar" disabled><i
                                class="fa-regular fa-clone me-2"></i> Duplicar</span></li>
                        <?php } ?>

                        <li>
                          <?php if ($solic_sta_status == 4) { ?>
                            <a href="#" class="dropdown-item <?= $classe_link ?>" data-bs-toggle="modal"
                              data-bs-target="#modal_cancelar_solicitacao" data-solic-id="<?= htmlspecialchars($solic_id) ?>"
                              data-action="../router/web.php?r=SolicitarCancelamento">
                              <i class="fa-solid fa-ban me-2"></i> Solicitar Cancelamento
                            </a>
                          <?php } ?>
                        </li>

                        <li><a
                            href="router/web.php?r=Solic&acao=deletar&solic_id=<?= $solic_id ?>&solic_codigo=<?= $solic_codigo ?>"
                            class="dropdown-item remove-item-btn del-btn" title="Excluir"><i
                              class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                      </ul>
                    </div>
                  </td>

                </tr>
              <?php }
            } catch (PDOException $e) {
              echo "Erro: " . $e->getMessage();
              // echo "Erro ao tentar recuperar os dados";
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    // Clique na linha da tabela, com exceções
    $('table').on('click', 'tr', function (e) {
      // Ignora cliques em dropdowns ou controles de expansão
      if (
        $(e.target).closest('.dropdown').length > 0 ||
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
    $(document).on('click', '.dropdown, td.dtr-control', function (e) {
      e.stopPropagation();
    });
  });
</script>

<script>
  // DUPLICA PROPOSTA
  var $ = jQuery.noConflict();
  $('.clone-btn').on('click', function (e) {
    e.preventDefault();
    const href = $(this).attr('href')
    Swal.fire({
      title: 'Deseja realmente duplicar esta solicitação?',
      text: 'Ao duplicar a solicitação, revise as informações!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#38C172',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Duplicar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.value) {
        document.location.href = href;
        // MOSTRA O LOAD ENQUANTO TERMINA A OPERAÇÃO
        function mostrarPreloader() {
          document.getElementById("preloader_delay").style.display = "block";
        }

        function ocultarPreloader() {
          document.getElementById("preloader_delay").style.display = "none";
        }
        mostrarPreloader();
        setTimeout(function () {
          ocultarPreloader();
        }, 100000);
        ///////////////////////////////////////
      }
    })
  })
</script>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/modal_dinamico.js"></script>

<?php include 'includes/modal/modal_cancelar_solicitacao.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var cancelaSolicitacaoModal = document.getElementById('modal_cancelar_solicitacao');
    cancelaSolicitacaoModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var solicId = button.getAttribute('data-solic-id');
      var modalSolicIdInput = cancelaSolicitacaoModal.querySelector('#solic_id_cancelar');
      modalSolicIdInput.value = solicId;
    });
  });
</script>