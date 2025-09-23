<?php include 'includes/header.php'; ?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

<div class="row breadcrumb_painel">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Programação Diária</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="">Dashboard</a></li>
          <li class="breadcrumb-item active">Programação Diária</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">

        <form method="GET">
          <div class="row g-3">

            <div class="col-md-3 col-lg-3 col-xl-4">
              <input type="date" class="form-control flatpickr-input" name="data" id="data" value="<?= htmlspecialchars($_GET['data'] ?? date('Y-m-d')) ?>">
              <script>
                flatpickr("#data", {
                  dateFormat: "Y-m-d", // formato do valor REAL do input (enviado na query)
                  altInput: true, // ativa um input visível com formato alternativo
                  altFormat: "d/m/Y", // formato exibido para o usuário
                  locale: "pt" // idioma português
                });
              </script>
            </div>

            <div class="col-md-3 col-lg-3">
              <div>
                <select class="form-select text-uppercase" name="unidade">
                  <option selected disabled value="">CAMPUS</option>
                  <?php
                  $sql = $conn->query("SELECT uni_id, uni_unidade FROM unidades ORDER BY uni_unidade");
                  while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($_GET['unidade'] ?? '') == $propc['uni_id'] ? 'selected' : '';
                    echo "<option value='{$propc['uni_id']}' $selected>{$propc['uni_unidade']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="col-md-3 col-lg-3">
              <div>
                <select class="form-select" name="turno">
                  <option selected disabled value="">TURNO</option>
                  <?php
                  $sql = $conn->query("SELECT cturn_id, cturn_turno FROM conf_turno ORDER BY cturn_id");
                  while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($_GET['turno'] ?? '') == $propc['cturn_turno'] ? 'selected' : '';
                    echo "<option value='{$propc['cturn_turno']}' $selected>{$propc['cturn_turno']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="col-md-3 col-lg-3 col-xl-2 d-flex gap-1">
              <button type="submit" class="btn botao botao_azul_escuro w-100">Filtrar</button>
              <a href="programacao_diaria.php" class="btn botao botao_cinza waves-effect w-100 ms-2">Limpar</a>
            </div>

          </div>
        </form>

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
            <h5 class="card-title mb-0">Lista de Programação</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center"></div>
        </div>
      </div>

      <style>
        table.dataTable>tbody>tr.child ul.dtr-details>li:last-child {
          text-align: left;
          padding: 0.5em 0 !important;
        }
      </style>

      <div class="card-body p-0">
        <table id="tab_prog_diaria" class="table dt-responsive  align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-2">Data</span></th>
              <th><span class="me-2">Início</span></th>
              <th><span class="me-2">Fim</span></th>
              <th><span class="me-2">Curso</span></th>
              <th><span class="me-2">Semestre</span></th>
              <th><span class="me-2">Componente Curricular</span></th>
              <th><span class="me-2">Módulo</span></th>
              <th><span class="me-2">Professor</span></th>
              <th><span class="me-2">Local</span></th>
              <th><span class="me-2">Pavilhão</span></th>
              <th><span class="me-2">Andar</span></th>
              <th><span class="me-2">Campus</span></th>
              <th><span class="me-2"></span></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              // Parâmetros iniciais
              $params = [];

              // Solicitante
              $user_id = $_SESSION['reservm_user_id'];
              $params[':user_id'] = $user_id;

              // Construção da query SQL em partes
              $sql = "SELECT res_id, res_status, res_data, res_hora_inicio, res_hora_fim, curs_curso, cs_semestre, compc_componente, res_componente_atividade, res_componente_atividade_nome, res_nome_atividade, res_modulo, res_professor, esp_nome_local_resumido,pav_pavilhao, and_andar, uni_unidade, res_obs, user_id, user_nome, solicitacao.solic_id
                      FROM reservas
                      INNER JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
                      LEFT JOIN cursos ON cursos.curs_id = reservas.res_curso
                      LEFT JOIN conf_semestre ON conf_semestre.cs_id = reservas.res_semestre
                      LEFT JOIN componente_curricular ON componente_curricular.compc_id = reservas.res_componente_atividade
                      INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                      INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                      LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                      LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                      LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                      INNER JOIN usuarios ON usuarios.user_id = solicitacao.solic_cad_por
                      WHERE user_id = :user_id 
                      AND (res_status IS NULL OR res_status NOT IN (8))
                      -- WHERE res_data = :data
                      ";

              // Filtro por data
              if (!empty($_GET['data'])) {
                $sql .= " AND res_data = :res_data";
                $params[':res_data'] = $_GET['data'];
              }

              // Filtro por unidade
              if (!empty($_GET['unidade'])) {
                $sql .= " AND esp_unidade = :unidade";
                $params[':unidade'] = $_GET['unidade'];
              }

              // Filtro por turno
              if (!empty($_GET['turno'])) {
                $sql .= " AND res_turno = :turno";
                $params[':turno'] = $_GET['turno'];
              }

              // Agora sim: preparar e executar com a SQL montada
              $stmt = $conn->prepare($sql);
              $stmt->execute($params);
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $res_id = $row['res_id']; // Variável crucial para o cancelamento
                $solic_id = $row['solic_id'];

                $res_data                      = $row['res_data'];
                $res_hora_inicio               = $row['res_hora_inicio'];
                $res_hora_fim                  = $row['res_hora_fim'];
                $curs_curso                    = $row['curs_curso'];
                $cs_semestre                   = $row['cs_semestre'];
                $compc_componente              = $row['compc_componente'];
                $res_componente_atividade      = $row['res_componente_atividade'];
                $res_componente_atividade_nome = $row['res_componente_atividade_nome'];
                $res_nome_atividade            = $row['res_nome_atividade'];
                $res_modulo                    = $row['res_modulo'];
                $res_professor                 = $row['res_professor'];
                $esp_nome_local_resumido       = $row['esp_nome_local_resumido'];
                $pav_pavilhao                  = $row['pav_pavilhao'];
                $and_andar                     = $row['and_andar'];
                $uni_unidade                   = $row['uni_unidade'];

                // CONFIGURAÇÃO COMPONENTES
                if (!empty($res_componente_atividade)) {
                  $componente = $compc_componente;
                } else if (!empty($res_componente_atividade_nome)) {
                  $componente = $res_componente_atividade_nome;
                } else if (!empty($res_nome_atividade)) {
                  $componente = $res_nome_atividade;
                }
                // Lógica de validação das 48 horas
                $res_data_hora = new DateTime($res_data . ' ' . $res_hora_inicio);
                $data_hora_atual = new DateTime();
                $intervalo = $data_hora_atual->diff($res_data_hora);
                $horas_totais = $intervalo->days * 24 + $intervalo->h;

                // Se a reserva já passou, o botão também não deve ser habilitado
                $reserva_ja_passou = $data_hora_atual > $res_data_hora;

                // Condição para desabilitar o botão
                $link_disabled = ($horas_totais < 48 || $reserva_ja_passou) ? 'disabled' : '';


                // Define a classe CSS e a condição de desabilitado
                if ($horas_totais < 48 || $reserva_ja_passou) {
                  $classe_link = 'link_cinza_claro disabled';
                } else {
                  $classe_link = 'text-danger';
                }

            ?>
                <tr>
                  <th scope="row" nowrap="nowrap"><span class="hide_data"><?= date('Ymd', strtotime($res_data)) ?></span><?= htmlspecialchars(date('d/m/Y', strtotime($res_data))) ?></th>
                  <th scope="row" class="fw-bold"><?= htmlspecialchars(date('H:i', strtotime($res_hora_inicio))) ?></th>
                  <th scope="row" class="fw-bold"><?= htmlspecialchars(date('H:i', strtotime($res_hora_fim))) ?></th>
                  <td scope="row"><?= htmlspecialchars($curs_curso) ?></td>
                  <td scope="row" nowrap="nowrap"><?= htmlspecialchars($cs_semestre) ?></td>
                  <td scope="row"><?= htmlspecialchars($componente) ?></td>
                  <td scope="row"><?= htmlspecialchars($res_modulo) ?></td>
                  <td scope="row"><?= htmlspecialchars($res_professor) ?></td>
                  <td scope="row" class="text-uppercase"><?= htmlspecialchars($esp_nome_local_resumido) ?></td>
                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($pav_pavilhao) ?></td>
                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($and_andar) ?></td>
                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($uni_unidade) ?></td>

                  <td class="text-end">
                    <div class="dropdown dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">




                        <li>
                          <a href="#" class="dropdown-item <?= $classe_link ?>" data-bs-toggle="modal" data-bs-target="#modal_cancelar_reserva_unica" data-res-id="<?= htmlspecialchars($res_id) ?>">
                            <i class="fa-solid fa-ban me-2"></i> Cancelar Reserva
                          </a>
                        </li>


                      </ul>
                    </div>

                  </td>

                </tr>
            <?php }
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar os dados" . $e->getMessage();
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</div>
</div>







<?php include 'includes/footer.php'; ?>

<!-- MODAL -->
<?php include 'includes/modal/modal_cancelar_reserva.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var cancelReservaUnicaModal = document.getElementById('modal_cancelar_reserva_unica');
    cancelReservaUnicaModal.addEventListener('show.bs.modal', function(event) {
      var button = event.relatedTarget;
      var resId = button.getAttribute('data-res-id');
      var modalResIdInput = cancelReservaUnicaModal.querySelector('#res_id_cancelar');
      modalResIdInput.value = resId;
    });
  });
</script>