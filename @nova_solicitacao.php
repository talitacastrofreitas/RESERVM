<?php include 'includes/header.php'; ?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-md-flex align-items-center justify-content-between">
      <h4 class="mb-md-0">Nova Solicitação</h4>
      <div class="page-title-right">
        <div class="row">
          <div class="col-12 text-truncate">
            <ol class="breadcrumb text-truncate m-0">
              <li class="breadcrumb-item"><a href="painel.php">Solicitações</a></li>
              <li class="breadcrumb-item active text-truncate">Nova Solicitações</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
if (isset($_GET['i'])) {
  $solic_id = $_GET['i'];
  $sql = 'SELECT *, campus_pratico.uni_id AS campus_pratico_id, campus_pratico.uni_unidade AS campus_pratico_nome, campus_teorico.uni_id AS campus_teorico_id, campus_teorico.uni_unidade AS campus_teorico_nome
          FROM solicitacao
          LEFT JOIN solicitacao_status ON solicitacao_status.solic_sta_solic_id = solicitacao.solic_id
          LEFT JOIN componente_curricular ON componente_curricular.compc_id = solicitacao.solic_comp_curric
          LEFT JOIN cursos ON cursos.curs_id = solicitacao.solic_curso
          LEFT JOIN conf_cursos_extensao_curricularizada ON conf_cursos_extensao_curricularizada.cexc_id = solicitacao.solic_nome_curso
          LEFT JOIN conf_semestre ON conf_semestre.cs_id = solicitacao.solic_semestre
          LEFT JOIN conf_turma_pratica ON conf_turma_pratica.ctp_id = solicitacao.solic_ap_quant_turma
          LEFT JOIN conf_sala_teorica ON conf_sala_teorica.cst_id = solicitacao.solic_at_quant_sala
          LEFT JOIN unidades AS campus_pratico ON campus_pratico.uni_id = solicitacao.solic_ap_campus
          LEFT JOIN unidades AS campus_teorico ON campus_teorico.uni_id = solicitacao.solic_at_campus
          WHERE solic_id = :solic_id';
  $stmt = $conn->prepare($sql);
  $stmt->execute(['solic_id' => $solic_id]);
  $result = $stmt->fetch();
  if ($result) {
    extract($result);
    // Persiste as escolhas da sessão para manter o estado do formulário
    $solic_ap_aula_pratica = $_SESSION['solic_ap_aula_pratica_choice'] ?? ($solic_ap_aula_pratica ?? null);
    // Note: solic_at_aula_teorica não é persistido na sessão para a leitura aqui,
    // pois a validação acontece no momento do POST da etapa 3.
  }
}
?>

<div class="row justify-content-md-center">
  <div class="col-lg-8 col-xxl-6">
    <div class="card">
      <div class="card-header border-0 p-sm-4 p-3">

        <?php
        // CONFIGURAÇÃO DA BARRA DE PROGRASSO
        $color_map = [
          1 => '0%',
          2 => '50%',
          3 => '100%', // Adicionado para a etapa 3
        ];

        $current_step = isset($_GET['st']) ? (int)$_GET['st'] : 1;
        $color_barra = $color_map[$current_step] ?? '0%';
        ?>

        <?php
        if (!empty($_GET['st'])) { ?>

          <div id="custom-progress-bar" class="progress-nav step_prop mb-4">
            <div class="progress" style="height: 1px;">
              <div class="progress-bar" role="progressbar" style="width: <?= $color_barra ?>;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
              <li class="nav-item" role="presentation">
                <button onclick="location.href='nova_solicitacao.php?st=1&i=<?= $_GET['i'] ?>'" class="nav-link rounded-pill <?= ($current_step >= 1) ? 'active' : ''; ?>" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="true" data-position="0"><i class="fa-solid fa-check"></i></button>
              </li>
              <li class="nav-item" role="presentation">
                <button onclick="location.href='nova_solicitacao.php?st=2&i=<?= $_GET['i'] ?>'" class="nav-link rounded-pill <?= ($current_step >= 2) ? 'active' : ''; ?>" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="false" data-position="1" tabindex="-1" <?php echo ($solic_etapa == 0) ? 'disabled' : ''; ?>><?php echo ($solic_etapa >= 2 || $current_step > 1) ? '<i class="fa-solid fa-check"></i>' : '2'; ?></button>
              </li>
              <li class="nav-item" role="presentation">
                <button onclick="location.href='nova_solicitacao.php?st=3&i=<?= $_GET['i'] ?>'" class="nav-link rounded-pill <?= ($current_step >= 3) ? 'active' : ''; ?>" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="false" data-position="2" tabindex="-1" <?php echo ($solic_etapa < 2) ? 'disabled' : ''; ?>><?php echo ($solic_etapa >= 3 || $current_step > 2) ? '<i class="fa-solid fa-check"></i>' : '3'; ?></button>
              </li>
            </ul>
          </div>

        <?php } else { ?>

          <div id="custom-progress-bar" class="progress-nav step_prop mb-4">
            <div class="progress" style="height: 1px;">
              <div class="progress-bar" role="progressbar" style="width: <?= $color_barra ?>;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
              <li class="nav-item" role="presentation">
                <button onclick="location.href='nova_solicitacao.php'" class=" nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="true" data-position="0">1</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled="">2</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-info-desc-tab" data-bs-toggle="pill" data-bs-target="#pills-info-desc" type="button" role="tab" aria-controls="pills-info-desc" aria-selected="false" data-position="1" tabindex="-1" disabled="">3</button>
              </li>
            </ul>
          </div>

        <?php } ?>


        <style>
          .tit_nova_solicitacao h3 {
            font-size: 1.2rem;
          }
        </style>

        <div class="row tit_nova_solicitacao">
          <div class="col-12">

            <?php
            if (isset($_GET['st'])) {
              if ($_GET['st'] == 1) {
                $step_tit = 'Identificação';
              }
              if ($_GET['st'] == 2) {
                $step_tit = 'Informações da Reserva <span class="d-inline-block ident_aula_solic ident_aula_solic_color_azul ms-sm-2 ms-0 mt-2 mt-sm-0">Aulas Práticas</span>';
              }
              if ($_GET['st'] == 3) {
                $step_tit = 'Informações da Reserva <span class="d-inline-block ident_aula_solic ident_aula_solic_color_roxo ms-sm-2 ms-0 mt-2 mt-sm-0">Aulas Teóricas</span>';
              }
            } else {
              $step_tit = 'Identificação';
            }
            ?>
            <h3 class="text-uppercase text-center"><?= $step_tit ?></h3>
          </div>
        </div>
      </div>

      <?php
      // Bloco para exibir mensagens de erro/sucesso
      // Certifique-se que session_start() está no seu includes/header.php
      if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger mt-3" role="alert">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']); // Limpa a mensagem após exibir
      }
      if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success mt-3" role="alert">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']); // Limpa a mensagem após exibir
      }
      ?>

      <?php
      if (!isset($_GET['st'])) {
        require 'includes/solicitacao/form_step.php';
      } else if (isset($_GET['st']) && $_GET['st'] == 1) {
        require 'includes/solicitacao/form_step1.php';
      } else if (isset($_GET['st']) && $_GET['st'] == 2) {
        require 'includes/solicitacao/form_step2.php';
      } else if (isset($_GET['st']) && $_GET['st'] == 3) {
        require 'includes/solicitacao/form_step3.php';
      } else if (isset($_GET['st']) && $_GET['st'] == 4) {
        require 'includes/solicitacao/form_step4.php';
      } else {
        header("Location: painel.php");
        exit(); // Adicionado exit para garantir que o script pare
      }
      ?>

    </div>
  </div>
</div> <?php include 'includes/footer.php'; ?>

<script src="includes/select/select2.js"></script>