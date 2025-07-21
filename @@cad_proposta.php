<?php include 'includes/header.php'; ?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-md-flex align-items-center justify-content-between">
      <h4 class="mb-md-0">Nova Solicitação</h4>
      <div class="page-title-right">

        <div class="row">
          <div class="col-12 text-truncate">
            <ol class="breadcrumb text-truncate m-0">
              <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
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
          INNER JOIN conf_tipo_atividade ON conf_tipo_atividade.cta_id = solicitacao.solic_tipo_ativ
          LEFT JOIN componente_curricular ON componente_curricular.compc_id = solicitacao.solic_comp_curric
          LEFT JOIN cursos ON cursos.curs_id = solicitacao.solic_curso
          LEFT JOIN conf_cursos_extensao_curricularizada ON conf_cursos_extensao_curricularizada.cexc_id = solicitacao.solic_nome_curso
          LEFT JOIN conf_semestre ON conf_semestre.cs_id = solicitacao.solic_semestre
          LEFT JOIN conf_turma_pratica ON conf_turma_pratica.ctp_id = solicitacao.solic_ap_quant_turma
          LEFT JOIN conf_sala_teorica ON conf_sala_teorica.cst_id = solicitacao.solic_at_quant_sala
          --LEFT JOIN unidades ON unidades.uni_id = solicitacao.solic_ap_campus
          LEFT JOIN unidades AS campus_pratico ON campus_pratico.uni_id = solicitacao.solic_ap_campus
          LEFT JOIN unidades AS campus_teorico ON campus_teorico.uni_id = solicitacao.solic_at_campus
          WHERE solic_id = :solic_id';
  $stmt = $conn->prepare($sql);
  $stmt->execute(['solic_id' => $solic_id]);
  $result = $stmt->fetch();
  if ($result) {
    extract($result);

    $solic_tipo_ativ = trim(isset($result['solic_nome_curso'])) ? $result['solic_nome_curso'] : NULL;
  }
}
?>

<div class="row justify-content-md-center">
  <div class="col-lg-8 col-xxl-6">
    <div class="card">
      <div class="card-header border-0 p-sm-4 p-3">

        <?php
        if (isset($_GET['st'])) {
          if ($_GET['st'] == 1) {
            $color_barra = '0%';
          } elseif ($_GET['st'] == 2) {
            $color_barra = '50%';
          } else {
            $color_barra = '100%';
          }
        }
        ?>

        <?php
        // SE CATEGORIA NÃO FOR PARCERIA (4), MOSTRA ETAPAS
        if (!empty($_GET['st'])) { ?>

          <div id="custom-progress-bar" class="progress-nav step_prop mb-4">
            <div class="progress" style="height: 1px;">
              <div class="progress-bar" role="progressbar" style="width: <?= $color_barra ?>;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
              <li class="nav-item" role="presentation">
                <button onclick="location.href='cad_proposta.php?st=1&i=<?= $_GET['i'] ?>'" class="nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="true" data-position="0"><i class="fa-solid fa-check"></i></button>
              </li>
              <li class="nav-item" role="presentation">
                <button onclick="location.href='cad_proposta.php?st=2&i=<?= $_GET['i'] ?>'" class="nav-link rounded-pill <?= ($_GET['st'] >= 2) ? 'active' : ''; ?>" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="false" data-position="1" tabindex="-1" <?php echo ($solic_etapa == 0) ? 'disabled' : ''; ?>><?php echo ($solic_etapa >= 2) ? '<i class="fa-solid fa-check"></i>' : '2'; ?></button>
              </li>
              <li class="nav-item" role="presentation">
                <button onclick="location.href='cad_proposta.php?st=3&i=<?= $_GET['i'] ?>'" class="nav-link rounded-pill <?= ($_GET['st'] >= 3) ? 'active' : ''; ?>" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="false" data-position="2" tabindex="-1" <?php echo ($solic_etapa < 2) ? 'disabled' : ''; ?>><?php echo ($solic_etapa >= 3) ? '<i class="fa-solid fa-check"></i>' : '3'; ?></button>
              </li>
              <!-- <li class="nav-item" role="presentation">
                <button onclick="location.href='cad_proposta.php?st=1&st=4'" class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="false" data-position="2" tabindex="-1">4</button>
              </li> -->
              <!-- <li class="nav-item" role="presentation">
                <button onclick="location.href='cad_proposta.php?st=1&st=5'" class="nav-link rounded-pill" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="false" data-position="2" tabindex="-1">5</button>
              </li> -->
              <!-- <li class="nav-item" role="presentation">
                <button onclick="location.href='cad_proposta.php?st=<?= base64_encode($propc_cat) ?>&st=<?= base64_encode(6) ?>&i=<?= $_GET['i'] ?>'" class="nav-link rounded-pill <?php echo (base64_decode($_GET['st']) >= 6) ? 'active' : ''; ?>" data-progressbar="custom-progress-bar" id="pills-success-tab" data-bs-toggle="pill" data-bs-target="#pills-success" type="button" role="tab" aria-controls="pills-success" aria-selected="false" data-position="2" tabindex="-1" <?php echo ($prop_status_etapa < 5) ? 'disabled' : ''; ?>>6</button>
              </li> -->
            </ul>
          </div>

        <?php } else { ?>

          <div id="custom-progress-bar" class="progress-nav step_prop mb-4">
            <div class="progress" style="height: 1px;">
              <div class="progress-bar" role="progressbar" style="width: <?= $color_barra ?>;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <ul class="nav nav-pills progress-bar-tab custom-nav" role="tablist">
              <li class="nav-item" role="presentation">
                <button onclick="location.href='cad_proposta.php'" class=" nav-link rounded-pill active" data-progressbar="custom-progress-bar" id="pills-gen-info-tab" data-bs-toggle="pill" data-bs-target="#pills-gen-info" type="button" role="tab" aria-controls="pills-gen-info" aria-selected="true" data-position="0">1</button>
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


        <div class="row tit_cad_proposta">
          <div class="col-12">

            <?php
            if (isset($_GET['st'])) {
              if ($_GET['st'] == 1) {
                // $step_tit = 'Informações Preliminares';
                $step_tit = 'Identificação';
              }
              if ($_GET['st'] == 2) {
                $step_tit = 'Informações da Reserva - <span class="ident_aula_solic ident_aula_solic_color_azul">Aulas Práticas</span>';
              }
              if ($_GET['st'] == 3) {
                $step_tit = 'Informações da Reserva - <span class="ident_aula_solic ident_aula_solic_color_roxo">Aulas Teóricas</span>';
              }
              if ($_GET['st'] == 4) {
                $step_tit = 'Divulgação e Promoção da Atividade';
              }
              if ($_GET['st'] == 5) {
                $step_tit = 'Concluir';
              }
            } else {
              $step_tit = 'Identificação';
            }
            ?>
            <h3 class="text-uppercase"><?= $step_tit ?></h3>
          </div>
        </div>

      </div>

      <?php
      if (!isset($_GET['st'])) {
        require 'includes/proposta/form_step.php';
      } else if (isset($_GET['st']) && $_GET['st'] == 1) {
        require 'includes/proposta/form_step1.php';
      } else if (isset($_GET['st']) && $_GET['st'] == 2) {
        require 'includes/proposta/form_step2.php';
      } else if (isset($_GET['st']) && $_GET['st'] == 3) {
        require 'includes/proposta/form_step3.php';
      } else if (isset($_GET['st']) && $_GET['st'] == 4) {
        require 'includes/proposta/form_step4.php';
      } else {
        // header("Location: painel.php");
      }
      ?>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- CEP -->
<!-- <script src="assets/js/1120_jquery.min.js"></script> -->
<script src="assets/js/CEP.js"></script>
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>