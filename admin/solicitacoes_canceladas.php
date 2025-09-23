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
            <h5 class="card-title mb-0">Lista de Solicitações Canceladas</h5>
          </div>
          <div class="col-md-6 d-flex align-items-center d-flex justify-content-md-end justify-content-center">

            <div class="d-inline-flex justify-content-center justify-content-md-end gap-2">
              <!-- <button class="btn botao botao_roxo waves-effect mt-3 mt-md-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><i class="ri-filter-3-line align-bottom me-1"></i> Filtro</button> -->

              <button class="btn botao botao_amarelo waves-effect mt-3 mt-md-0" data-bs-toggle="modal"
                data-bs-toggle="button" data-bs-target="#modal_cad_solicitacao">+ Nova Solicitação</button>
            </div>

            <script src="assets/js/filter_card.js"></script>

          </div>
          <div class="collapse" id="collapseExample">

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-6 g-3 mt-2 mb-3">

              <div class="col">
                <?php try {
                  $sql = $conn->prepare("SELECT solic_codigo FROM solicitacao ORDER BY solic_codigo");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div>
                  <label class="form-label">Código</label>
                  <select class="form-select text-uppercase" name="" id="solic_codigo">
                    <option></option>
                    <?php foreach ($result as $res): ?>
                      <option value="<?= $res['solic_codigo'] ?>"><?= $res['solic_codigo'] ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_codigo").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <?php try {
                  $sql = $conn->prepare("SELECT cta_id, UPPER(cta_tipo_atividade) AS cta_tipo_atividade FROM conf_tipo_atividade ORDER BY cta_tipo_atividade");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div>
                  <label class="form-label">Tipo de Atividade</label>
                  <select class="form-select text-uppercase" name="" id="">
                    <option></option>
                    <?php foreach ($result as $res): ?>
                      <option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_atividade'] ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_tipo_ativ").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <?php try {
                  $sql = $conn->prepare("SELECT DISTINCT curs_id, curs_curso FROM solicitacao
                                      INNER JOIN cursos ON cursos.curs_id = solicitacao.solic_curso
                                      ORDER BY curs_curso");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div>
                  <label class="form-label">Curso</label>
                  <select class="form-select text-uppercase" name="" id="solic_curso">
                    <option></option>
                    <?php foreach ($result as $res): ?>
                      <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_curso").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <?php try {
                  $sql = $conn->prepare("SELECT DISTINCT compc_id, compc_componente, solic_nome_atividade, solic_nome_comp_ativ FROM solicitacao
                                      LEFT JOIN componente_curricular ON componente_curricular.compc_id = solicitacao.solic_comp_curric
                                      ORDER BY compc_componente");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div>
                  <label class="form-label">Componente/Atividade</label>
                  <select class="form-select text-uppercase" name="" id="solic_comp_ativ">
                    <option></option>
                    <?php foreach ($result as $res): ?>
                      <option value="<?= $res['compc_id'] ?>"><?= $res['compc_componente'] ?></option>
                      <option value="<?= $res['solic_nome_atividade'] ?>"><?= $res['solic_nome_atividade'] ?></option>
                      <option value="<?= $res['solic_nome_comp_ativ'] ?>"><?= $res['solic_nome_comp_ativ'] ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_comp_ativ").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <?php try {
                  $sql = $conn->prepare("SELECT DISTINCT solic_id, solic_nome_prof_resp FROM solicitacao ORDER BY solic_nome_prof_resp");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div>
                  <label class="form-label">Professor/Responsável</label>
                  <select class="form-select text-uppercase" name="" id="solic_nome_prof_resp">
                    <option></option>
                    <?php foreach ($result as $res): ?>
                      <option value="<?= $res['solic_nome_prof_resp'] ?>"><?= $res['solic_nome_prof_resp'] ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_nome_prof_resp").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <?php try {
                  $sql = $conn->prepare("SELECT DISTINCT solic_id, cs_id, cs_semestre FROM solicitacao
                                      LEFT JOIN conf_semestre ON conf_semestre.cs_id = solicitacao.solic_semestre
                                      ORDER BY cs_semestre");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div>
                  <label class="form-label">Semestre</label>
                  <select class="form-select text-uppercase" name="" id="solic_semestre">
                    <option></option>
                    <?php foreach ($result as $res): ?>
                      <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_semestre").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <?php try {
                  $sql = $conn->prepare("SELECT uni_id, UPPER(uni_unidade) AS uni_unidade FROM unidades ORDER BY uni_unidade");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div>
                  <label class="form-label">Campus - Aula Prática</label>
                  <select class="form-select text-uppercase" name="" id="">
                    <option></option>
                    <?php foreach ($result as $res): ?>
                      <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_unidade_pratica").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <div>
                  <label class="form-label">Tipo de Reserva - Aula Prática</label>
                  <select class="form-select text-uppercase" name="" id="">
                    <option></option>
                    <option value="1">ESPORÁDICA</option>
                    <option value="2">FIXA</option>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_tipo_reserva_pratica").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <?php try {
                  $sql = $conn->prepare("SELECT uni_id, UPPER(uni_unidade) AS uni_unidade FROM unidades ORDER BY uni_unidade");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  echo "Erro ao tentar recuperar os dados";
                } ?>
                <div>
                  <label class="form-label">Campus - Aula Teórica</label>
                  <select class="form-select text-uppercase" name="" id="">
                    <option></option>
                    <?php foreach ($result as $res): ?>
                      <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
                    <?php endforeach; ?>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_unidade_teorica").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <div>
                  <label class="form-label">Tipo de Reserva - Aula Teórica</label>
                  <select class="form-select text-uppercase" name="" id="">
                    <option></option>
                    <option value="1">ESPORÁDICA</option>
                    <option value="2">FIXA</option>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_tipo_reserva_teorica").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <div>
                  <label class="form-label">Status</label>
                  <select class="form-select text-uppercase" name="" id="">
                    <option></option>
                    <option value="1">EM ELABORAÇÃO</option>
                    <option value="2">SOLICITADO</option>
                    <option value="2">EM ANÁLISE</option>
                    <option value="2">CONFIRMADA</option>
                    <option value="2">DEFERIDA</option>
                  </select>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <script>
                  $("#solic_status").select2({
                    // dropdownParent: $("#cad_usuario"),
                    placeholder: "",
                    allowClear: true, // opcional: mostra o "x" para limpar a seleção
                    language: {
                      noResults: function (params) {
                        return "Dados não encontrado";
                      },
                    },
                  });
                </script>
              </div>

              <div class="col">
                <label class="form-label">Data da Solicitação</label>
                <input type="date" class="form-control" name="">
              </div>

              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-end">
                <div class="d-inline-flex justify-content-center justify-content-md-end gap-2">
                  <button type="submit" class="btn botao btn-light waves-effect">Limpar</button>
                  <button type="submit" class="btn botao botao_azul_escuro waves-effect">Filtrar</button>
                </div>
              </div>

              <!--end col-->
            </div>

          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <table id="tab_solic_user" class="table dt-responsive  align-middle" style="width:100%">
          <thead>
            <tr>
              <th nowrap="nowrap"><span class="me-2">Código</span></th>
              <th nowrap="nowrap"><span class="me-2">Curso</span></th>
              <th nowrap="nowrap"><span class="me-2">Componente/Atividade</span></th>
              <th nowrap="nowrap"><span class="me-2">Solicitante</span></th>
              <th nowrap="nowrap"><span class="me-2">Data Solicitação</span></th>
              <th nowrap="nowrap"><span class="me-2">Status</span></th>
              <!-- <th width="20px"></th> -->
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM solicitacao
                                      LEFT JOIN solicitacao_status ON solicitacao_status.solic_sta_solic_id = solicitacao.solic_id
                                      LEFT JOIN status_solicitacao ON status_solicitacao.stsolic_id = solicitacao_status.solic_sta_status
                                      --INNER JOIN conf_tipo_atividade ON conf_tipo_atividade.cta_id = solicitacao.solic_tipo_ativ
                                      LEFT JOIN componente_curricular ON componente_curricular.compc_id = solicitacao.solic_comp_curric
                                      LEFT JOIN cursos ON cursos.curs_id = solicitacao.solic_curso
                                      LEFT JOIN conf_cursos_extensao_curricularizada ON conf_cursos_extensao_curricularizada.cexc_id = solicitacao.solic_nome_curso
                                      LEFT JOIN conf_semestre ON conf_semestre.cs_id = solicitacao.solic_semestre
                                      LEFT JOIN usuarios ON usuarios.user_id = solicitacao.solic_cad_por
                                      LEFT JOIN admin ON admin.admin_id = solicitacao.solic_cad_por
                                      WHERE solicitacao_status.solic_sta_status = 8
                                      ");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                // CONFIGURAÇÃO DO STATUS
                if ($solic_sta_status == 1) {
                  $status_color = 'bg_info_laranja';
                }
                if ($solic_sta_status == 2) {
                  $status_color = 'bg_info_azul';
                }
                if ($solic_sta_status == 3) {
                  $status_color = 'bg_info_roxo';
                }
                if ($solic_sta_status == 4) {
                  $status_color = 'bg_info_verde';
                }
                if ($solic_sta_status == 5) {
                  $status_color = 'bg_info_azul_escuro';
                }
                if ($solic_sta_status == 6) {
                  $status_color = 'bg_info_vermelho';
                }


                // PEGA O PRIMEIRO NOME E ÚLTIMO NOME
                if (isset($user_nome)) {
                  $partesNome = explode(" ", $user_nome);
                } else {
                  $partesNome = explode(" ", $admin_nome);
                }
                $primeiroNome = $partesNome[0];
                $ultimoNome = end($partesNome);

                ?>
                <tr role="button" data-href='solicitacao_analise.php?i=<?= $solic_id ?>'>
                  <th scope="row"><?= $solic_codigo ?></th>
                  <td scope="row"><?= $curs_curso ?></td>
                  <td scope="row"><?= $compc_componente ?><?= $solic_nome_atividade ?><?= $solic_nome_comp_ativ ?></td>
                  <td scope="row" nowrap="nowrap"><?= $primeiroNome . ' ' . $ultimoNome ?></td>
                  <td scope="row" nowrap="nowrap"><span
                      class="hide_data"><?= date('Ymd', strtotime($solic_data_cad)) ?></span><?= date('d/m/Y H:i', strtotime($solic_data_cad)) ?>
                  </td>
                  <td scope="row"><span class="badge <?= $status_color ?>"><?= $stsolic_status ?></span></td>
                  <!-- <td class="text-end">
                    <div class="dropdown dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_espaco"
                            data-bs-esp_id="<?= $esp_id ?>"
                            data-bs-esp_codigo="<?= $esp_codigo ?>"
                            data-bs-esp_nome_local="<?= $esp_nome_local ?>"
                            data-bs-esp_nome_local_resumido="<?= $esp_nome_local_resumido ?>"
                            data-bs-esp_tipo_espaco="<?= $esp_tipo_espaco ?>"
                            data-bs-esp_andar="<?= $esp_andar ?>"
                            data-bs-esp_pavilhao="<?= $esp_pavilhao ?>"
                            data-bs-esp_quant_maxima="<?= $esp_quant_maxima ?>"
                            data-bs-esp_quant_media="<?= $esp_quant_media ?>"
                            data-bs-esp_quant_minima="<?= $esp_quant_minima ?>"
                            data-bs-esp_unidade="<?= $esp_unidade ?>"
                            data-bs-esp_recursos="<?= $esp_recursos ?>"
                            data-bs-esp_status="<?= $esp_status ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="../router/web.php?r=esp&func=exc_esp&esp_id=<?= $esp_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                      </ul>
                    </div>
                  </td> -->
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


<!-- CADASTRAR SOLICITAÇÃO -->
<div class="modal fade modal_padrao" id="modal_cad_solicitacao" tabindex="-1" aria-labelledby="modal_cad_solicitacao"
  aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_solicitacao">Cadastrar Solicitação</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
            class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form class="needs-validation" method="POST" action="../router/web.php?r=AdminSolic" autocomplete="off"
          novalidate>

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
                  <?php foreach ($result as $res): ?>
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
                  <?php foreach ($result as $res): ?>
                    <option value="<?= $res['cexc_id'] ?>"><?= $res['cexc_curso'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12" id="campo_solic_nome_curso_text" style="display: none;">
              <div class="mb-3">
                <label class="form-label">Nome do Curso <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="solic_nome_curso_text"
                  id="cad_solic_nome_curso_text" maxlength="200">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12" id="campo_solic_nome_atividade" style="display: none;">
              <div class="mb-3">
                <label class="form-label">Nome da Atividade <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="solic_nome_atividade"
                  id="cad_solic_nome_atividade" maxlength="200">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12" id="campo_solic_nome_comp_ativ" style="display: none;">
              <div class="mb-3">
                <label class="form-label">Nome do Componente/Atividade <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="solic_nome_comp_ativ"
                  id="cad_solic_nome_comp_ativ" maxlength="200">
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
                  <?php foreach ($result as $res): ?>
                    <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-md-6" id="campo_solic_nome_prof_resp" style="display: none;">
              <div class="mb-3">
                <label class="form-label">Nome do Professor/Responsável <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="solic_nome_prof_resp"
                  id="cad_solic_nome_prof_resp" maxlength="200">
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
  $(document).ready(function () {
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

    $('#cad_solic_tipo_ativ, #cad_solic_curso, #cad_solic_comp_curric, #cad_solic_nome_curso, #campo_solic_nome_curso_text').change(function () {
      $('[id^="campo_"]').hide().find('input, select').prop('required', false);
      toggleFields();
    });

    toggleFields();
  });
</script>

<script>
  $(document).ready(function () {
    // Quando o curso for alterado
    $('#cad_solic_curso').change(function () {
      var cursoId = $(this).val();
      if (cursoId !== "") {
        $.ajax({
          url: '../buscar_componentes.php',
          type: 'POST',
          data: {
            curso_id: cursoId
          },
          success: function (data) {
            $('#cad_solic_comp_curric').html(data);
          }
        });
      } else {
        $('#cad_solic_comp_curric').html('<option value="">Selecione um componente</option>');
      }
    });
  });
</script>

<?php include 'includes/footer.php'; ?>

<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>