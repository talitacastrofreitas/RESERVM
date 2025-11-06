<?php include 'includes/header.php'; ?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

<div class="row breadcrumb_painel">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Aprovações</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
          <li class="breadcrumb-item active">Aprovações</li>
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
            <h5 class="card-title mb-0">Lista de Aprovações</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_aprova" class="table dt-responsive  align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-2">Código</span></th>
              <th><span class="me-2">Componente/Atividade</span></th>
              <th><span class="me-2">Professor/Responsável</span></th>
              <th><span class="me-2">Data Solicitação</span></th>
              <th><span class="me-2">Status</span></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT solic_id, solic_codigo, compc_componente, solic_nome_atividade, solic_nome_comp_ativ, solic_nome_prof_resp, solic_data_cad, stsolic_status, solic_sta_status FROM solicitacao
                                      LEFT JOIN solicitacao_status ON solicitacao_status.solic_sta_solic_id = solicitacao.solic_id
                                      LEFT JOIN status_solicitacao ON status_solicitacao.stsolic_id = solicitacao_status.solic_sta_status
                                      LEFT JOIN componente_curricular ON componente_curricular.compc_id = solicitacao.solic_comp_curric
                                      LEFT JOIN curso_coordenador ON curso_coordenador.curs_id = solicitacao.solic_curso
                                      INNER JOIN usuarios ON usuarios.user_matricula = curso_coordenador.coordenador_matricula
                                      WHERE solic_etapa != 1 AND solic_cad_por != :solic_cad_por");
              $stmt->execute([':solic_cad_por' => $_SESSION['reservm_user_id']]);
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $solic_id = $row['solic_id'];
                $solic_codigo = $row['solic_codigo'];
                $compc_componente = $row['compc_componente'];
                $solic_nome_atividade = $row['solic_nome_atividade'];
                $solic_nome_comp_ativ = $row['solic_nome_comp_ativ'];
                $solic_nome_prof_resp = $row['solic_nome_prof_resp'];
                $solic_data_cad = $row['solic_data_cad'];
                $stsolic_status = $row['stsolic_status'];
                $solic_sta_status = $row['solic_sta_status'];

                // CONFIGURAÇÃO DO STATUS
                $status_classes = [
                  1 => 'bg_info_laranja', // EM ELABORAÇÃO
                  2 => 'bg_info_azul', // SOLICITADO
                  3 => 'bg_info_roxo', // EM ANÁLISE PELO COORDENADOR
                  4 => 'bg_info_verde', //RESERVADO
                  5 => 'bg_info_azul_escuro', // APROVADO PELO COORDENADOR
                  6 => 'bg_info_vermelho', // INDEFERIDO
                  7 => 'bg_info_roxo', // EM ANÁLISE PELO SAAP
                  8 => 'bg_info_vermelho' // CANCELADO
                ];
                $status_color = $status_classes[$solic_sta_status] ?? '';
                ?>
                <tr role="button" data-href='aprovacoes_single.php?i=<?= htmlspecialchars($solic_id) ?>'>
                  <th scope="row"><?= htmlspecialchars($solic_codigo) ?></th>
                  <td scope="row">
                    <?= htmlspecialchars($compc_componente) . htmlspecialchars($solic_nome_atividade) . htmlspecialchars($solic_nome_comp_ativ) ?>
                  </td>
                  <td scope="row"><?= htmlspecialchars($solic_nome_prof_resp) ?></td>
                  <td scope="row" nowrap="nowrap"><span
                      class="hide_data"><?= date('Ymd', strtotime($solic_data_cad)) ?></span><?= htmlspecialchars(date('d/m/Y H:i', strtotime($solic_data_cad))) ?>
                  </td>
                  <td scope="row"><span class="badge <?= $status_color ?>"><?= htmlspecialchars($stsolic_status) ?></span>
                  </td>
                </tr>
              <?php }
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar os dados";
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



<?php include 'includes/footer.php'; ?>