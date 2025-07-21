<?php include 'includes/header.php'; ?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

<div class="row breadcrumb_painel">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Ocorrências</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
          <li class="breadcrumb-item active">Ocorrências</li>
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
            <h5 class="card-title mb-0">Lista de Ocorrências</h5>
          </div>
          <div class="col-md-6 d-flex align-items-center d-flex justify-content-md-end justify-content-center d-none">

            <div class="d-inline-flex justify-content-center justify-content-md-end gap-2">
              <button class="btn botao botao_roxo waves-effect mt-3 mt-md-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><i class="ri-filter-3-line align-bottom me-1"></i> Filtro</button>

              <button class="btn botao botao_amarelo waves-effect mt-3 mt-md-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_espaco">+ Nova Solicitação</button>
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
                    <?php foreach ($result as $res) : ?>
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
                      noResults: function(params) {
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
                    <?php foreach ($result as $res) : ?>
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
                      noResults: function(params) {
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
                    <?php foreach ($result as $res) : ?>
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
                      noResults: function(params) {
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
                    <?php foreach ($result as $res) : ?>
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
                      noResults: function(params) {
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
                    <?php foreach ($result as $res) : ?>
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
                      noResults: function(params) {
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
                    <?php foreach ($result as $res) : ?>
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
                      noResults: function(params) {
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
                    <?php foreach ($result as $res) : ?>
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
                      noResults: function(params) {
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
                      noResults: function(params) {
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
                    <?php foreach ($result as $res) : ?>
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
                      noResults: function(params) {
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
                      noResults: function(params) {
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
                      noResults: function(params) {
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
        <table id="tab_ocor" class="table dt-responsive align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-2">Código Ocorrência</span></th>
              <th><span class="me-2">Data Ocorrência</span></th>
              <th><span class="me-2">Início Realizado</span></th>
              <th><span class="me-2">Término Realizado</span></th>
              <th><span class="me-2">Tipo Ocorrência</span></th>
              <th><span class="me-2">Local</span></th>
              <th><span class="me-2">Andar</span></th>
              <th><span class="me-2">Pavilhão</span></th>
              <th><span class="me-2">Campus</span></th>
              <th><span class="me-2">Tipo de espaço</span></th>
              <th><span class="me-2">Operador</span></th>
              <th><span class="me-2">Data cadastro</span></th>
              <!-- <th width="20px"></th> -->
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM ocorrencias
                                                  INNER JOIN reservas ON reservas.res_id = ocorrencias.oco_res_id
                                                  INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                                                  INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                                  LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                                  LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                                                  LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                                  INNER JOIN admin ON admin.admin_id = ocorrencias.oco_user_id");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                ////////////////////////////////////////////
                // TRATA OS DADOS DOS TIPOS DE OCORRÊNCIAS //
                ////////////////////////////////////////////

                // Pegue o campo oco_tipo_ocorrencia e limpe para o formato certo
                $tipo_ocorrencia_ids = trim($row['oco_tipo_ocorrencia'] ?? '');
                $tipo_ocorrencia_ids = rtrim($tipo_ocorrencia_ids, ','); // Remove vírgula final, se existir

                if (empty($tipo_ocorrencia_ids)) {
                  // Sem topo de ocorrencia
                  $row['tipos_formatados'] = '';
                } else {
                  // Explode e filtra só ids numéricos
                  $ids_array = array_filter(array_map('trim', explode(',', $tipo_ocorrencia_ids)), 'ctype_digit');

                  if (count($ids_array) === 0) {
                    $row['tipos_formatados'] = '';
                  } else {
                    $tipo_ocorrencia_ids_sql = implode(',', $ids_array);

                    // Busca nomes dos tipos para esses IDs
                    $sql_tipo_oco = "SELECT cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_id IN ($tipo_ocorrencia_ids_sql)";
                    $stmt_oco = $conn->prepare($sql_tipo_oco);
                    $stmt_oco->execute();
                    $tipo_oco = $stmt_oco->fetchAll(PDO::FETCH_COLUMN);

                    // Monta string separada por ' / '
                    $row['tipos_formatados'] = '• ' . implode('<br>• ', $tipo_oco);
                  }
                }

                ///////////////////////
                // FIM DO TRATAMENTO //
                ///////////////////////


                extract($row);


            ?>
                <tr role="button" data-href='solicitacao_analise.php?i=<?= htmlspecialchars($solic_id) ?>'>
                  <th scope="row" class="text-bolder"><?= $oco_codigo ?></th>
                  <td scope="row" nowrap="nowrap" class="bg_table_fix_azul"><span class="hide_data"><?= date('Ymd', strtotime($res_data)) ?></span><strong><?= htmlspecialchars(date('d/m/Y', strtotime($res_data))) ?></strong></td>
                  <td scope="row" nowrap="nowrap" class="bg_table_fix_roxo"><span class="hide_data"><?= date('iH', strtotime($oco_hora_inicio_realizado)) ?></span><strong><?= date('H:i', strtotime($oco_hora_inicio_realizado)) ?></strong></td>
                  <td scope="row" nowrap="nowrap" class="bg_table_fix_rosa"><span class="hide_data"><?= date('iH', strtotime($oco_hora_fim_realizado)) ?></span><strong><?= date('H:i', strtotime($oco_hora_fim_realizado)) ?></strong></td>
                  <td scope="row" class="text-uppercase"><?= $tipos_formatados ?></td>
                  <td scope="row" class="text-uppercase"><?= $esp_nome_local ?></td>
                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= $and_andar ?></td>
                  <td scope="row" nowrap="nowrap" class="text-uppercase"><?= $pav_pavilhao ?></td>
                  <td scope="row" class="text-uppercase"><?= $uni_unidade ?></td>
                  <td scope="row" class="text-uppercase"><?= $tipesp_tipo_espaco ?></td>
                  <!-- <td scope="row" nowrap="nowrap"><?= $primeiroNome . ' ' . $ultimoNome ?></td> -->
                  <td scope="row" class="text-uppercase"><?= $admin_nome ?></td>
                  <td scope="row" nowrap="nowrap" class="text-bolder"><span class="hide_data"><?= date('Ymd', strtotime($oco_data_cad)) ?></span><?= date('d/m/Y H:i', strtotime($oco_data_cad)) ?></td>
                  <!-- <td class="text-end">
                    <div class="dropdown dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_ocorrencia"
                            data-bs-oco_id="<?= $oco_id ?>"
                            data-bs-oco_res_id="<?= $oco_res_id ?>"
                            data-bs-oco_solic_id="<?= $oco_solic_id ?>"
                            data-bs-oco_tipo_ocorrencia="<?= $oco_tipo_ocorrencia ?>"
                            data-bs-oco_hora_inicio_realizado="<?= date('H:i', strtotime($oco_hora_inicio_realizado)) ?>"
                            data-bs-oco_hora_fim_realizado="<?= date('H:i', strtotime($oco_hora_fim_realizado)) ?>"
                            data-bs-oco_obs="<?= $oco_obs ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="../router/web.php?r=Ocorrenc&acao=deletar&oco_id=<?= $oco_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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

      <script>
        $(document).ready(function() {
          // Clique na linha da tabela, com exceções
          $('table').on('click', 'tr', function(e) {
            // Ignora cliques em dropdowns ou controles de expansão
            if ($(e.target).closest('.btn').length > 0) {
              return;
            }

            // Vai para o link especificado no atributo data-href
            const href = $(this).data('href');
            if (href) {
              window.location.href = href;
            }
          });

          // Apenas por segurança, evita propagação em elementos específicos
          $(document).on('click', '.btn', function(e) {
            e.stopPropagation();
          });
        });
      </script>

    </div>
  </div>
</div>


</div>
</div>


<?php include 'includes/footer.php'; ?>