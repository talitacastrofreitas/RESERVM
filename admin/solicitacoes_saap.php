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
          <div class="col-md-6 text-md-start text-center">
            <h5 class="card-title mb-0">Lista de Solicitações</h5>
          </div>
          <div class="col-md-6 d-flex align-items-center d-flex justify-content-md-end justify-content-center">

            <div class="d-inline-flex justify-content-center justify-content-md-end gap-2">

              <button class="btn botao botao_amarelo waves-effect mt-3 mt-md-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_solicitacao">+ Nova Solicitação</button>
            </div>

            <script src="assets/js/filter_card.js"></script>

          </div>
          <!-- </div> -->
          <div class="card-body p-0">
            <table id="tab_solic_user" class="table dt-responsive align-middle" style="width:100%">
              <thead>
                <tr>
                  <th nowrap="nowrap"><span class="me-2">Código</span></th>
                  <th nowrap="nowrap"><span class="me-2">Curso</span></th>
                  <th nowrap="nowrap"><span class="me-2">Componente/Atividade</span></th>
                  <th nowrap="nowrap"><span class="me-2">Solicitante</span></th>
                  <th nowrap="nowrap"><span class="me-2">Data Solicitação</span></th>
                  <th nowrap="nowrap"><span class="me-2">Status</span></th>
                  <th nowrap="nowrap" width="20px">Ações</th>
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
    usuarios.user_nome,
    admin.admin_nome
  FROM solicitacao
  LEFT JOIN solicitacao_status ON solicitacao_status.solic_sta_solic_id = solicitacao.solic_id
  LEFT JOIN status_solicitacao ON status_solicitacao.stsolic_id = solicitacao_status.solic_sta_status
  LEFT JOIN componente_curricular ON componente_curricular.compc_id = solicitacao.solic_comp_curric
  LEFT JOIN cursos ON cursos.curs_id = solicitacao.solic_curso
  LEFT JOIN usuarios ON usuarios.user_id = solicitacao.solic_cad_por
  LEFT JOIN admin ON admin.admin_id = solicitacao.solic_cad_por");
                  $stmt->execute();

                  while ($row_solic = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $solic_id = $row_solic['solic_id'];
                    $solic_codigo = $row_solic['solic_codigo'];
                    $solic_nome_comp_ativ = $row_solic['solic_nome_comp_ativ'];
                    $solic_nome_atividade = $row_solic['solic_nome_atividade'];
                    $solic_data_cad = $row_solic['solic_data_cad'];
                    $solic_sta_status = $row_solic['solic_sta_status'];
                    $solic_etapa = $row_solic['solic_etapa'];
                    $curs_curso = $row_solic['curs_curso'];
                    $compc_componente = $row_solic['compc_componente'];
                    $stsolic_status = $row_solic['stsolic_status'];
                    $solicitante_nome = $row_solic['user_nome'] ?: $row_solic['admin_nome'];

                    // CONFIGURAÇÃO DO STATUS
                    $status_colors = [
                      1 => 'bg_info_laranja',
                      2 => 'bg_info_azul',
                      3 => 'bg_info_roxo',
                      4 => 'bg_info_verde',
                      5 => 'bg_info_azul_escuro',
                      6 => 'bg_info_vermelho',
                      7 => 'bg_info_laranja',
                      8 => 'bg_info_vermelho',
                    ];
                    $status_color = $status_colors[$solic_sta_status] ?? 'bg_info_padrao';

                    // --- LÓGICA DO BOTÃO DE CANCELAR PARA O SAAP ---
                    $link_disabled = true;
                    $classe_link = 'link_cinza_claro disabled';

                    if ($solic_sta_status == 7) { // Condição alterada para o status 7 (Aguardando Cancelamento)
                      $sql_reserva_habilita = $conn->prepare("
  SELECT 1
  FROM reservas
  WHERE res_solic_id = :solic_id
  AND (res_status NOT IN (8) OR res_status IS NULL)
  AND CAST(res_data AS DATETIME) + CAST(res_hora_inicio AS DATETIME) >= DATEADD(hour, 48, GETDATE())
");
                      $sql_reserva_habilita->execute([':solic_id' => $solic_id]);
                      $reserva_existe = $sql_reserva_habilita->fetchColumn();

                      if ($reserva_existe) {
                        $link_disabled = false;
                        $classe_link = 'link_vermelho';
                      }
                    }
                ?>
                    <tr role="button" data-href='solicitacao_analise.php?i=<?= htmlspecialchars($solic_id) ?>'>
                      <th scope="row"><?= htmlspecialchars($solic_codigo) ?></th>
                      <td scope="row"><?= htmlspecialchars($curs_curso) ?></td>
                      <td scope="row"><?= htmlspecialchars($compc_componente) ?><?= htmlspecialchars($solic_nome_atividade) ?><?= htmlspecialchars($solic_nome_comp_ativ) ?></td>
                      <td scope="row"><?= htmlspecialchars($solicitante_nome) ?></td>
                      <td scope="row" nowrap="nowrap"><span class="hide_data"><?= htmlspecialchars(date('Ymd', strtotime($solic_data_cad))) ?></span><?= htmlspecialchars(date('d/m/Y H:i', strtotime($solic_data_cad))) ?></td>
                      <td scope="row"><span class="badge <?= $status_color ?>"><?= htmlspecialchars($stsolic_status) ?></span></td>
                      <td class="text-end">
                        <div class="dropdown dropdown drop_tabela d-inline-block">
                          <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-fill align-middle"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="solicitacao_analise.php?i=<?= $solic_id ?>" class="dropdown-item edit-item-btn" title="Analisar"><i class="ri-eye-line me-2"></i> Analisar</a></li>

                            <?php if ($solic_sta_status == 7) { ?>
                              <li>
                                <a href="#" class="dropdown-item <?= $classe_link ?>" data-bs-toggle="modal" data-bs-target="#modal_cancelar_solicitacao" data-solic-id="<?= htmlspecialchars($solic_id) ?>" data-action="../controller/controller_saap_cancelamento.php">
                                  <i class="fa-solid fa-ban me-2"></i> Cancelar Solicitação
                                </a>
                              </li>
                            <?php } ?>
                          </ul>
                        </div>
                      </td>
                    </tr>
                <?php }
                } catch (PDOException $e) {
                  echo "Erro: " . $e->getMessage();
                } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>



    <script>
      $(document).ready(function() {
        // Clique na linha da tabela, com exceções
        $('table').on('click', 'tr', function(e) {
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
        $(document).on('click', '.dropdown, td.dtr-control', function(e) {
          e.stopPropagation();
        });
      });
    </script>


    <!-- CADASTRAR SOLICITAÇÃO -->
    <div class="modal fade modal_padrao" id="modal_cad_solicitacao" tabindex="-1" aria-labelledby="modal_cad_solicitacao" aria-modal="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header modal_padrao_cinza">
            <h5 class="modal-title" id="modal_cad_solicitacao">Cadastrar Solicitação</h5>
            <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
          </div>
          <div class="modal-body">
            <form class="needs-validation" method="POST" action="../router/web.php?r=AdminSolic" autocomplete="off" novalidate>

              <div class="row grid gx-3">

                <input type="hidden" class="form-control" name="solic_acao" value="cadastrar" required>

                <div class="col-12">
                  <div class="mb-3">
                    <?php try {
                      $sql = $conn->prepare("SELECT curs_id, curs_curso FROM cursos WHERE curs_status != 0 ORDER BY curs_curso");
                      $sql->execute();
                      $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                      // echo "Erro: " . $e->getMessage();
                      echo "Erro ao tentar recuperar os dados";
                    } ?>
                    <label class="form-label">Curso <span>*</span></label>
                    <select class="form-select text-uppercase" name="solic_curso" id="cad_solic_curso" required>
                      <option selected disabled value=""></option>
                      <?php foreach ($result as $res) : ?>
                        <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
                      <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-12" id="campo_solic_comp_curric" style="display: none;">
                  <div class="mb-3">
                    <label class="form-label">Componente Curricular <span>*</span></label>
                    <select class="form-select text-uppercase" name="solic_comp_curric" id="cad_solic_comp_curric">
                    </select>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-md-6" id="campo_solic_nome_curso" style="display: none;">
                  <div class="mb-3">
                    <?php try {
                      $sql = $conn->prepare("SELECT cexc_id, cexc_curso FROM conf_cursos_extensao_curricularizada ORDER BY cexc_curso");
                      $sql->execute();
                      $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                      echo "Erro ao tentar recuperar os dados";
                    } ?>
                    <label class="form-label">Nome do Curso <span>*</span></label>
                    <select class="form-select text-uppercase" name="solic_nome_curso" id="cad_solic_nome_curso">
                      <option selected disabled value=""></option>
                      <?php foreach ($result as $res) : ?>
                        <option value="<?= $res['cexc_id'] ?>"><?= $res['cexc_curso'] ?></option>
                      <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-12" id="campo_solic_nome_curso_text" style="display: none;">
                  <div class="mb-3">
                    <label class="form-label">Nome do Curso <span>*</span></label>
                    <input type="text" class="form-control text-uppercase" name="solic_nome_curso_text" id="cad_solic_nome_curso_text" maxlength="200">
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-12" id="campo_solic_nome_atividade" style="display: none;">
                  <div class="mb-3">
                    <label class="form-label">Nome da Atividade <span>*</span></label>
                    <input type="text" class="form-control text-uppercase" name="solic_nome_atividade" id="cad_solic_nome_atividade" maxlength="200">
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-12" id="campo_solic_nome_comp_ativ" style="display: none;">
                  <div class="mb-3">
                    <label class="form-label">Nome do Componente/Atividade <span>*</span></label>
                    <input type="text" class="form-control text-uppercase" name="solic_nome_comp_ativ" id="cad_solic_nome_comp_ativ" maxlength="200">
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-md-6" id="campo_solic_semestre" style="display: none;">
                  <div class="mb-3">
                    <?php try {
                      $sql = $conn->prepare("SELECT * FROM conf_semestre ORDER BY cs_id ASC");
                      $sql->execute();
                      $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                      echo "Erro ao tentar recuperar os dados";
                    } ?>
                    <label class="form-label">Semestre <span>*</span></label>
                    <select class="form-select text-uppercase" name="solic_semestre" id="cad_solic_semestre">
                      <option selected disabled value=""></option>
                      <?php foreach ($result as $res) : ?>
                        <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
                      <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-md-6" id="campo_solic_nome_prof_resp" style="display: none;">
                  <div class="mb-3">
                    <label class="form-label">Nome do Professor/Responsável <span>*</span></label>
                    <input type="text" class="form-control text-uppercase" name="solic_nome_prof_resp" id="cad_solic_nome_prof_resp" maxlength="200">
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-md-6" id="campo_solic_contato" style="display: none;">
                  <div class="mb-3">
                    <label class="form-label">Telefone para contato <span>*</span></label>
                    <input type="text" class="form-control cel_tel" name="solic_contato" id="cad_solic_contato">
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-lg-12">
                  <div class="hstack gap-3 align-items-center justify-content-end mt-4">
                    <p class="label_asterisco me-auto my-0"><span>*</span> Campo obrigatório</p>
                    <button type="submit" class="btn botao botao_verde waves-effect">Cadastrar</button>
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script>
      $(document).ready(function() {
        function toggleFields() {
          var tipoAtiv = $('#cad_solic_tipo_ativ').val();
          var curso = $('#cad_solic_curso').val();
          var compCurric = $('#cad_solic_comp_curric').val();
          var nomeCurso = $('#cad_solic_nome_curso').val();

          $('[id^="campo_"]').hide().find('input, select').prop('required', false);

          if (tipoAtiv == '1') {
            // 1	ATIVIDADE ACADÊMICA
            $('#campo_solic_curso').show().find('#cad_solic_curso').prop('required', true);
          } else if (tipoAtiv == '2') {
            // 2	ATIVIDADE ADMINISTRATIVA
            $('[id^="campo_"]').hide();
            $('#campo_solic_nome_atividade, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input').prop('required', true);
            return;
          }

          if ([2, 5, 6, 9, 13, 14, 17, 18, 21].includes(parseInt(curso))) {
            // 2	BIOMEDICINA
            // 5	EDUCAÇÃO FÍSICA
            // 6	ENFERMAGEM
            // 9	FISIOTERAPIA
            // 13	LIGA ACADÊMICA
            // 14	MEDICINA
            // 17	NÚCLEO COMUM
            // 18	ODONTOLOGIA
            // 21	PSICOLOGIA
            $('#campo_solic_comp_curric').show().find('#cad_solic_comp_curric').prop('required', true);
            // 0	OUTRO
            if (compCurric == '0') {
              $('#campo_solic_nome_comp_ativ, #campo_solic_semestre, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input, select').prop('required', true);
            } else if (compCurric) {
              $('#campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input').prop('required', true);
            }
          }

          if (curso == '8') {
            // 8	EXTENSÃO CURRICULARIZADA
            $('#campo_solic_nome_curso').show().find('#cad_solic_nome_curso').prop('required', true);
            if (nomeCurso) {
              $('#campo_solic_nome_atividade, #campo_solic_semestre, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input, select').prop('required', true);
            }
          }

          if ([7, 10, 19, 28, 31].includes(parseInt(curso))) {
            // 7	EXTENSÃO
            // 10	GRUPO DE PESQUISA
            // 19	PROGRAMA CANDEAL
            // 28	NIDD
            // 31	RESERVAS ADMINISTRATIVAS
            $('#campo_solic_nome_atividade, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input').prop('required', true);
          }

          if ([11, 22].includes(parseInt(curso))) {
            // 11	LATO SENSU
            // 22	STRICTO SENSU
            $('#campo_solic_nome_curso_text, #campo_solic_nome_comp_ativ, #campo_solic_semestre, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input, select').prop('required', true);
          }
        }

        $('#cad_solic_tipo_ativ, #cad_solic_curso, #cad_solic_comp_curric, #cad_solic_nome_curso, #campo_solic_nome_curso_text').change(function() {
          $('[id^="campo_"]').hide().find('input, select').prop('required', false);
          toggleFields();
        });

        toggleFields();
      });
    </script>

    <script>
      $(document).ready(function() {
        // Quando o curso for alterado
        $('#cad_solic_curso').change(function() {
          var cursoId = $(this).val();
          if (cursoId !== "") {
            $.ajax({
              url: '../buscar_componentes.php',
              type: 'POST',
              data: {
                curso_id: cursoId
              },
              success: function(data) {
                $('#cad_solic_comp_curric').html(data);
              }
            });
          } else {
            $('#cad_solic_comp_curric').html('<option value="">Selecione um componente</option>');
          }
        });
      });
    </script>


    <?php include '../includes/modal/modal_cancelar_solicitacao.php'; ?>
    <script src="../includes/modal/modal_dinamico.js"></script>
    <?php include 'includes/footer.php'; ?>