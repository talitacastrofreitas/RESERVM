<?php
if (isset($_GET['i'])) {
  $solic_id = $_GET['i'];
  $sql = 'SELECT *, campus_pratico.uni_id AS campus_pratico_id, campus_pratico.uni_unidade AS campus_pratico_nome, campus_teorico.uni_id AS campus_teorico_id, campus_teorico.uni_unidade AS campus_teorico_nome
          FROM solicitacao
          LEFT JOIN solicitacao_status ON solicitacao_status.solic_sta_solic_id = solicitacao.solic_id
          LEFT JOIN status_solicitacao ON status_solicitacao.stsolic_id = solicitacao_status.solic_sta_status
          LEFT JOIN componente_curricular ON componente_curricular.compc_id = solicitacao.solic_comp_curric
          LEFT JOIN cursos ON cursos.curs_id = solicitacao.solic_curso
          LEFT JOIN conf_cursos_extensao_curricularizada ON conf_cursos_extensao_curricularizada.cexc_id = solicitacao.solic_nome_curso
          LEFT JOIN conf_semestre ON conf_semestre.cs_id = solicitacao.solic_semestre
          LEFT JOIN conf_turma_pratica ON conf_turma_pratica.ctp_id = solicitacao.solic_ap_quant_turma
          LEFT JOIN conf_sala_teorica ON conf_sala_teorica.cst_id = solicitacao.solic_at_quant_sala
          LEFT JOIN conf_tipo_reserva ON conf_tipo_reserva.ctr_id = solicitacao.solic_ap_tipo_reserva OR conf_tipo_reserva.ctr_id = solicitacao.solic_at_tipo_reserva
          LEFT JOIN unidades AS campus_pratico ON campus_pratico.uni_id = solicitacao.solic_ap_campus
          LEFT JOIN unidades AS campus_teorico ON campus_teorico.uni_id = solicitacao.solic_at_campus
          LEFT JOIN usuarios ON usuarios.user_id = solicitacao.solic_cad_por
          LEFT JOIN admin ON admin.admin_id = solicitacao.solic_cad_por
          WHERE solic_id = :solic_id';
  $stmt = $conn->prepare($sql);
  $stmt->execute(['solic_id' => $solic_id]);
  $solic_row = $stmt->fetch();
  if ($solic_row) {
    extract($solic_row);

    // $solic_tipo_ativ = trim(isset($solic_row['solic_nome_curso'])) ? $solic_row['solic_nome_curso'] : NULL;


    // CONFIGURAÇÃO DO STATUS
    $status_colors = [
      1 => 'tag_header_laranja',
      2 => 'tag_header_azul',
      3 => 'tag_header_roxo',
      4 => 'tag_header_verde ',
      5 => 'tag_header_azul',
      6 => 'tag_header_vermelho',
      7 => 'tag_header_roxo'
    ];

    $tag_header_color = $status_colors[$solic_sta_status] ?? ''; // Usa '' como padrão se não existir
  } else {
    header("Location: ../admin/solicitacoes.php");
    exit;
  }
}
?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg profile-analise-bg"></div>
</div>

<div class="pt-4 mb-4 mb-lg-3 pb-lg-3 profile-wrapper header_perfil">
  <div class="row g-4 align-items-start">

    <div class="col-lg-12">
      <div>
        <div
          class="d-flex justify-content-sm-between justify-content-start align-items-start align-items-sm-center flex-sm-row flex-column">
          <h3 class="text-uppercase mb-1" style="color: var(--amarelo);">
            <div class="d-inline"><strong class="me-2"><?= $solic_row['solic_codigo'] ?>:
              </strong><?= $solic_row['compc_componente'] . $solic_row['solic_nome_atividade'] . $solic_row['solic_nome_comp_ativ'] ?>
          </h3>
          <div class="botao <?= $tag_header_color ?> my-md-0 my-3"><?= $stsolic_status ?></div>
        </div>
        <div class="row mt-3">

          <!-- <div class="col-xl-3 col-lg-6 mb-3">
            <span>Tipo de Atividade</span>
            <p><?= $solic_row['cta_tipo_atividade'] ?></p>
          </div> -->

          <div class="col-xl-3 col-lg-6 mb-3">
            <span>Solicitante</span>
            <p><?= $solic_row['user_nome'] ? $solic_row['user_nome'] : $solic_row['admin_nome'] ?></p>
          </div>

          <div class="col-xl-3 col-lg-6 mb-3">
            <span>E-mail</span>
            <p><?= $solic_row['user_email'] ? $solic_row['user_email'] : $solic_row['admin_email'] ?></p>
          </div>

          <div class="col-xl-3 col-lg-6 mb-3">
            <span>Data Solicitação</span>
            <p><?= date('d/m/Y H:i', strtotime($solic_row['solic_data_cad'])) ?></p>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>