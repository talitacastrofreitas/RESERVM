<div class="card-body p-sm-4 p-3">

  <form class="needs-validation" method="POST" action="controller/controller_propostas.php?funcao=cad_prop_prog" id="ValidaBotaoProgress" enctype="multipart/form-data" autocomplete="off" novalidate>

    <input type="hidden" class="form-control" name="prop_id" value="<?= base64_encode($prop_id) ?>" required>
    <input type="hidden" class="form-control" name="prop_tipo" value="<?= base64_encode($propc_cat) ?>" required>
    <input type="hidden" class="form-control" name="prop_codigo" value="<?= base64_encode($prop_codigo) ?>" required>
    <input type="hidden" class="form-control" name="prop_status_etapa" value="<?= base64_encode($prop_status_etapa) ?>" required>

    <div class="tit_section">
      <h3>Tipo de Programa</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-lg-6">
        <div class="mb-3">
          <?php try {
            $sql = $conn->query("SELECT tipprog_id, tipprog_programa FROM tipo_programa WHERE tipprog_id != '$tipprog_id' ORDER BY tipprog_id");
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar o perfil";
          } ?>
          <label class="form-label">Tipo de Programa <span>*</span></label>
          <select class="form-select" name="prop_prog_tipo" id="cad_prop_prog_tipo" required>
            <option selected value="<?= $tipprog_id ?>"><?= $tipprog_programa ?></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['tipprog_id'] ?>"><?= $res['tipprog_programa'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-lg-6" id="campo_cad_prop_prog_categoria_pet" style="display: none;">
        <div class="mb-3">
          <?php try {
            $sql_pet = $conn->query("SELECT ctp_id, ctp_tipo, ctp_categoria FROM conf_tipo_programa WHERE ctp_tipo = 'PET' AND ctp_categoria != '$ctp_categoria' ORDER BY ctp_categoria");
            $result_pet = $sql_pet->fetchAll(PDO::FETCH_ASSOC);

            if ($ctp_tipo == 'PET') {
              $ctp_id_pet        = $ctp_id;
              $ctp_categoria_pet = $ctp_categoria;
            } else {
              $ctp_id_pet        = NULL;
              $ctp_categoria_pet = NULL;
            }
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar os dados";
          } ?>
          <label class="form-label">Tipo de PET <span>*</span></label>
          <select class="form-select" name="prop_prog_categoria_pet" id="cad_prop_prog_categoria_pet">
            <option selected value="<?= $ctp_id_pet ?>"><?= $ctp_categoria_pet ?></option>
            <?php foreach ($result_pet as $res_pet) : ?>
              <option value="<?= $res_pet['ctp_id'] ?>"><?= $res_pet['ctp_categoria'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-lg-6" id="campo_cad_prop_prog_categoria_pec" style="display: none;">
        <div class="mb-3">
          <?php try {
            $sql_pec = $conn->query("SELECT ctp_id, ctp_tipo, ctp_categoria FROM conf_tipo_programa WHERE ctp_tipo = 'PEC' AND ctp_categoria != '$ctp_categoria' ORDER BY ctp_categoria");
            $result_pec = $sql_pec->fetchAll(PDO::FETCH_ASSOC);

            if ($ctp_tipo == 'PEC') {
              $ctp_id_pec        = $ctp_id;
              $ctp_categoria_pec = $ctp_categoria;
            } else {
              $ctp_id_pec        = NULL;
              $ctp_categoria_pec = NULL;
            }
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar os dados";
          }
          ?>
          <label class="form-label">Tipo de PEC <span>*</span></label>
          <select class="form-select" name="prop_prog_categoria_pec" id="cad_prop_prog_categoria_pec">
            <option selected value="<?= $ctp_id_pec ?>"><?= $ctp_categoria_pec ?></option>
            <?php foreach ($result_pec as $res_pec) : ?>
              <option value="<?= $res_pec['ctp_id'] ?>"><?= $res_pec['ctp_categoria'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

    </div>




    <div id="campo_cad_prop_prog_integrado_pet" style="display: none;">

      <div class="tit_section">
        <h3>Programas Integrados ao Ensino</h3>
      </div>

      <div class="row grid gx-3">

        <div class="col-12">
          <div class="mb-4">
            <div class="mb-2">
              <label class="form-label m-0">Submissão do Edital <span>*</span></label>
            </div>
            <div class="input-group">
              <?php
              $sql = "SELECT COUNT(*) FROM propostas_arq WHERE parq_prop_id = :parq_prop_id AND parq_categoria = 4";
              $stmt = $conn->prepare($sql);
              $stmt->bindParam(":parq_prop_id", $prop_id, PDO::PARAM_STR);
              $stmt->execute();
              $quant_arq = $stmt->fetchColumn();
              if ($quant_arq == 0) { ?>
                <input type="file" class="form-control input_arquivo" name="arquivo" id="arquivo" onchange="imgCert(this)" accept=".pdf, .xlsx, .xls, .doc, .docx, .csv, .jpg, .JPG, .jpeg, .png, .PNG">
              <?php } else { ?>
                <input type="file" class="form-control input_arquivo" name="" disabled>
              <?php } ?>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              // ARQUIVO NÃO PODE ULTRAPASSAR 10MB
              document.addEventListener("DOMContentLoaded", function() {
                const inputFile = document.getElementById("arquivo");

                inputFile.addEventListener("change", function() {
                  const arquivo = inputFile.files[0];

                  if (arquivo && arquivo.size > 10 * 1024 * 1024) {
                    Swal.fire({
                      icon: 'error',
                      title: 'Erro encontrado!',
                      text: 'O arquivo deve ter menos de 10MB.',
                    })
                    inputFile.value = ""; // Limpar o valor do campo de arquivo
                  }
                });
              });

              // PERMITE 20 ARQUIVOS
              document.getElementById("arquivo").addEventListener("change", function() {
                var inputElement = this;
                var maxFiles = 20;
                var allowedExtensions = ["pdf", "xlsx", "xls", "doc", "docx", "csv", "jpg", "JPG", "jpeg", "png", "PNG"];
                var fileExtension = inputElement.value.split(".").pop().toLowerCase();
                if (inputElement.files.length > maxFiles) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Limite de imagens excedida!',
                    text: 'Por favor, selecione até ' + maxFiles + ' arquivo.',
                  })
                  inputElement.value = "";
                };

                // FORMATOS DE ARQUIVOS PERMITIDOS
                if (allowedExtensions.indexOf(fileExtension) === -1) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Erro encontrado!',
                    text: 'Formato de arquivo inválido.',
                    // text: 'Formato de arquivo inválido. Apenas arquivo JPG, JPEG ou PNG são permitidos.',
                  })
                  inputElement.value = ""; // Limpa a seleção de arquivo inválido
                }
              });
            </script>

            <div class="mt-3">

              <?php $sql = $conn->query("SELECT * FROM propostas_arq WHERE parq_prop_id = '$prop_id' AND parq_categoria = 4");
              while ($arq = $sql->fetch(PDO::FETCH_ASSOC)) {
                $parq_id        = $arq['parq_id'];
                $parq_prop_id   = $arq['parq_prop_id'];
                $parq_categoria = $arq['parq_categoria'];
                $parq_arquivo   = $arq['parq_arquivo'];
              ?>

                <div class="result_file mt-2">
                  <div class="result_file_name"><a href="uploads/propostas/<?= $prop_codigo . '/' . $parq_categoria . '/' . $parq_arquivo ?>" target="_blank"><?= $parq_arquivo ?></a></div>
                  <span class="item_bt_row">
                    <a href="controller/controller_propostas.php?funcao=exc_arq&ident=<?= $parq_id ?>&p=4&c=<?= $prop_codigo ?>&f=<?= $parq_arquivo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                  </span>
                </div>

              <?php } ?>

            </div>

          </div>
        </div>

      </div>

    </div>

    <div id="campo_cad_prop_prog_integrado_pec" style="display: none;">

      <div class="tit_section">
        <h3>Programas Integrados ao Ensino</h3>
      </div>

      <div class="row grid gx-3">

        <div class="col-lg-6 col-xl-4">
          <div class="mb-3">
            <?php try {
              $stmt = $conn->query("SELECT NOMESOCIAL FROM $view_colaboradores ORDER BY NOMESOCIAL");
              $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar materiais";
            } ?>
            <label class="form-label">Docente Orientador <span>*</span></label>
            <select class="form-select text-uppercase" name="prop_prog_docente" id="prop_prog_docente">
              <option selected value="<?= $prop_prog_docente ?>"><?= $prop_prog_docente ?></option>
              <?php foreach ($result_itens as $result_iten) : ?>
                <option value="<?= $result_iten['NOMESOCIAL']; ?>"><?= $result_iten['NOMESOCIAL']; ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-lg-6 col-xl-4">
          <div class="mb-3">
            <?php try {
              $stmt = $conn->query("SELECT at_id, at_area_tematica FROM conf_areas_tematicas ORDER BY at_area_tematica");
              $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar materiais";
            } ?>
            <label class="form-label">Área de Atuação <span>*</span></label>
            <select class="form-select text-uppercase" name="prop_prog_area_atuacao" id="prop_prog_area_atuacao">
              <option selected value="<?= $at_id ?>"><?= $at_area_tematica ?></option>
              <?php foreach ($result_itens as $result_iten) : ?>
                <option value="<?= $result_iten['at_id']; ?>"><?= $result_iten['at_area_tematica']; ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-lg-6 col-xl-4">
          <div class="mb-3">
            <label class="form-label">Local de Atuação</label>
            <input type="text" class="form-control text-uppercase" name="prop_prog_local_atuacao" id="prop_prog_local_atuacao" value="<?= $prop_prog_local_atuacao ?>" maxlength="50">
          </div>
        </div>

        <div class="col-lg-6 col-xl-4">
          <div class="mb-3">
            <label class="form-label">Valor da Inscrição <span>*</span></label>
            <input type="text" class="form-control money" name="prop_prog_valor_inscricao" id="prop_prog_valor_inscricao" value="<?= $prop_prog_valor_inscricao ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" placeholder="R$">
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-lg-6 col-xl-4">
          <div class="mb-3">
            <label class="form-label">Data de Início <span>*</span></label>
            <input type="date" class="form-control" name="prop_prog_data_inicio" id="prop_prog_data_inicio" value="<?= $prop_prog_data_inicio ?>" onblur="checkDateInicio()">
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
          <script>
            function checkDateInicio() {
              var prop_prog_data_inicio = document.getElementById("prop_prog_data_inicio").value;
              var dataAtualInicio = new Date().toISOString().slice(0, 10);

              if (prop_prog_data_inicio < dataAtualInicio) {
                Swal.fire({
                  title: 'Data Inválida!',
                  text: 'A data inicial não pode ser menor que a data de hoje.',
                  icon: 'error',
                  confirmButtonText: 'OK'
                }).then((result) => {
                  // document.getElementById("prop_prog_data_inicio").focus(); // Retorna o foco ao campo de entrada
                });
                document.getElementById('prop_prog_data_inicio').value = "";
                return false;
              }
            }
          </script>
        </div>

        <div class="col-lg-6 col-xl-4">
          <div class="mb-3">
            <label class="form-label">Data de Finalização <span>*</span></label>
            <input type="date" class="form-control" name="prop_prog_data_fim" id="prop_prog_data_fim" value="<?= $prop_prog_data_fim ?>" onblur="checkDateFinal()">
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
          <script>
            function checkDateFinal() {
              var prop_prog_data_fim = document.getElementById("prop_prog_data_fim").value;
              var dataAtualFim = new Date().toISOString().slice(0, 10);

              if (prop_prog_data_fim < dataAtualFim) {
                Swal.fire({
                  title: 'Data Inválida!',
                  text: 'A data final não pode ser menor que a data atual.',
                  icon: 'error',
                  confirmButtonText: 'OK'
                }).then((result) => {
                  //document.getElementById("prop_data_fim").focus(); // Retorna o foco ao campo de entrada
                });
                document.getElementById('prop_prog_data_fim').value = "";
                return false;
              }
            }
            ////
            const dataInicioInput = document.getElementById('prop_prog_data_inicio');
            const dataFimInput = document.getElementById('prop_prog_data_fim');

            dataInicioInput.addEventListener('change', verificarDatas);
            dataFimInput.addEventListener('change', verificarDatas);

            function verificarDatas() {
              const dataInicio = new Date(dataInicioInput.value);
              const dataFim = new Date(dataFimInput.value);

              if (dataInicio > dataFim) {
                Swal.fire({
                  icon: 'error',
                  title: 'Data Inválida!',
                  text: 'A data de início não pode ser maior que a data de finalização.',
                  confirmButtonText: 'OK'
                }).then((result) => {
                  //document.getElementById("prop_prog_data_inicio").focus(); // Retorna o foco ao campo de entrada
                });
                document.getElementById('prop_prog_data_inicio').value = "";
                document.getElementById('prop_prog_data_fim').value = "";
                return false;
              }
            }
          </script>
        </div>

      </div>

    </div>

    <script>
      const cad_prop_prog_tipo = document.getElementById("cad_prop_prog_tipo");
      const campo_cad_prop_prog_categoria_pet = document.getElementById("campo_cad_prop_prog_categoria_pet");
      const campo_cad_prop_prog_categoria_pec = document.getElementById("campo_cad_prop_prog_categoria_pec");

      const campo_cad_prop_prog_integrado_pet = document.getElementById("campo_cad_prop_prog_integrado_pet");
      const campo_cad_prop_prog_integrado_pec = document.getElementById("campo_cad_prop_prog_integrado_pec");

      cad_prop_prog_tipo.addEventListener("change", function() {
        if (cad_prop_prog_tipo.value === "1") {
          campo_cad_prop_prog_categoria_pet.style.display = "block";
          campo_cad_prop_prog_integrado_pet.style.display = "block";
          campo_cad_prop_prog_categoria_pec.style.display = "none";
          campo_cad_prop_prog_integrado_pec.style.display = "none";
          //
          document.getElementById("cad_prop_prog_categoria_pet").required = true;
          document.getElementById("arquivos").required = true;
          //
          document.getElementById("cad_prop_prog_categoria_pec").required = false;
          document.getElementById("prop_prog_docente").required = false;
          document.getElementById("prop_prog_area_atuacao").required = false;
          document.getElementById("prop_prog_valor_inscricao").required = false;
          document.getElementById("prop_prog_data_inicio").required = false;
          document.getElementById("prop_prog_data_fim").required = false;

        } else if (cad_prop_prog_tipo.value === "2") {
          campo_cad_prop_prog_categoria_pet.style.display = "none";
          campo_cad_prop_prog_integrado_pet.style.display = "none";
          campo_cad_prop_prog_categoria_pec.style.display = "block";
          campo_cad_prop_prog_integrado_pec.style.display = "block";
          //
          document.getElementById("cad_prop_prog_categoria_pec").required = true;
          document.getElementById("prop_prog_docente").required = true;
          document.getElementById("prop_prog_area_atuacao").required = true;
          document.getElementById("prop_prog_valor_inscricao").required = true;
          document.getElementById("prop_prog_data_inicio").required = true;
          document.getElementById("prop_prog_data_fim").required = true;
          //
          document.getElementById("cad_prop_prog_categoria_pet").required = false;
          document.getElementById("arquivos").required = false;
        } else {
          campo_cad_prop_prog_categoria_pet.style.display = "none";
          campo_cad_prop_prog_integrado_pet.style.display = "none";
          campo_cad_prop_prog_categoria_pec.style.display = "none";
          campo_cad_prop_prog_integrado_pec.style.display = "none";
          //
          document.getElementById("cad_prop_prog_categoria_pet").required = false;
          document.getElementById("arquivos").required = false;
          document.getElementById("cad_prop_prog_categoria_pec").required = false;
          document.getElementById("prop_prog_docente").required = false;
          document.getElementById("prop_prog_area_atuacao").required = false;
          document.getElementById("prop_prog_valor_inscricao").required = false;
          document.getElementById("prop_prog_data_inicio").required = false;
          document.getElementById("prop_prog_data_fim").required = false;
        }
      });

      if (cad_prop_prog_tipo.value === "1") {
        campo_cad_prop_prog_categoria_pet.style.display = "block";
        campo_cad_prop_prog_integrado_pet.style.display = "block";
        campo_cad_prop_prog_categoria_pec.style.display = "none";
        campo_cad_prop_prog_integrado_pec.style.display = "none";
        //
        document.getElementById("cad_prop_prog_categoria_pet").required = true;
        document.getElementById("arquivos").required = true;
        //
        document.getElementById("cad_prop_prog_categoria_pec").required = false;
        document.getElementById("prop_prog_docente").required = false;
        document.getElementById("prop_prog_area_atuacao").required = false;
        document.getElementById("prop_prog_valor_inscricao").required = false;
        document.getElementById("prop_prog_data_inicio").required = false;
        document.getElementById("prop_prog_data_fim").required = false;
      } else if (cad_prop_prog_tipo.value === "2") {
        campo_cad_prop_prog_categoria_pet.style.display = "none";
        campo_cad_prop_prog_integrado_pet.style.display = "none";
        campo_cad_prop_prog_categoria_pec.style.display = "block";
        campo_cad_prop_prog_integrado_pec.style.display = "block";
        //
        document.getElementById("cad_prop_prog_categoria_pec").required = true;
        document.getElementById("prop_prog_docente").required = true;
        document.getElementById("prop_prog_area_atuacao").required = true;
        document.getElementById("prop_prog_valor_inscricao").required = true;
        document.getElementById("prop_prog_data_inicio").required = true;
        document.getElementById("prop_prog_data_fim").required = true;
        //
        document.getElementById("cad_prop_prog_categoria_pet").required = false;
        document.getElementById("arquivos").required = false;
      } else {
        campo_cad_prop_prog_categoria_pet.style.display = "none";
        campo_cad_prop_prog_integrado_pet.style.display = "none";
        campo_cad_prop_prog_categoria_pec.style.display = "none";
        campo_cad_prop_prog_integrado_pec.style.display = "none";
        //
        document.getElementById("cad_prop_prog_categoria_pet").required = false;
        document.getElementById("arquivos").required = false;
        document.getElementById("cad_prop_prog_categoria_pec").required = false;
        document.getElementById("prop_prog_docente").required = false;
        document.getElementById("prop_prog_area_atuacao").required = false;
        document.getElementById("prop_prog_valor_inscricao").required = false;
        document.getElementById("prop_prog_data_inicio").required = false;
        document.getElementById("prop_prog_data_fim").required = false;
      }
    </script>



    <div class="tit_section">
      <h3>Informações Adicionais</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Comentários e Solicitações Complementares</label>
          <textarea class="form-control" name="prop_prog_obs" rows="3"><?= str_replace('<br />', '', $prop_prog_obs) ?></textarea>
        </div>
      </div>
    </div>

    <div class="tit_section" id="parq_ancora">
      <h3>Arquivos</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="mb-4">
          <div class="mb-2">
            <label class="form-label m-0">Arquivos diversos</label>
            <label class="label_info">
              (Limite de arquivos: 20)<br>
              (Arquivos permitidos: pdf, xlsx, xls, doc, docx, csv, jpg, jpeg, png)
            </label>
          </div>
          <div class="input-group">
            <?php
            $sql = "SELECT COUNT(*) FROM propostas_arq WHERE parq_prop_id = :parq_prop_id AND parq_categoria = 3";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":parq_prop_id", $prop_id, PDO::PARAM_STR);
            $stmt->execute();
            $quant_arq = $stmt->fetchColumn();
            if ($quant_arq == 0) { ?>
              <input type="file" class="form-control input_arquivo" name="arquivos[]" multiple onchange="imgCert(this)" accept=".pdf, .xlsx, .xls, .doc, .docx, .csv, .jpg, .JPG, .jpeg, .png, .PNG">
            <?php } else if ($quant_arq <= 20) { ?>
              <input type="file" class="form-control input_arquivo" name="arquivos[]" onchange="imgCert(this)" accept=".pdf, .xlsx, .xls, .doc, .docx, .csv, .jpg, .JPG, .jpeg, .png, .PNG">
            <?php } else { ?>
              <input type="file" class="form-control input_arquivo" name="" disabled>
            <?php } ?>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
          <script>
            // ARQUIVO NÃO PODE ULTRAPASSAR 10MB
            document.addEventListener("DOMContentLoaded", function() {
              const inputFile = document.getElementById("arquivos");

              inputFile.addEventListener("change", function() {
                const arquivo = inputFile.files[0];

                if (arquivo && arquivo.size > 10 * 1024 * 1024) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Erro encontrado!',
                    text: 'O arquivo deve ter menos de 10MB.',
                  })
                  inputFile.value = ""; // Limpar o valor do campo de arquivo
                }
              });
            });

            // PERMITE 20 ARQUIVOS
            document.getElementById("arquivos").addEventListener("change", function() {
              var inputElement = this;
              var maxFiles = 20;
              var allowedExtensions = ["pdf", "xlsx", "xls", "doc", "docx", "csv", "jpg", "JPG", "jpeg", "png", "PNG"];
              var fileExtension = inputElement.value.split(".").pop().toLowerCase();
              if (inputElement.files.length > maxFiles) {
                Swal.fire({
                  icon: 'error',
                  title: 'Limite de imagens excedida!',
                  text: 'Por favor, selecione até ' + maxFiles + ' arquivos.',
                })
                inputElement.value = "";
              };

              // FORMATOS DE ARQUIVOS PERMITIDOS
              if (allowedExtensions.indexOf(fileExtension) === -1) {
                Swal.fire({
                  icon: 'error',
                  title: 'Erro encontrado!',
                  text: 'Formato de arquivo inválido.',
                  // text: 'Formato de arquivo inválido. Apenas arquivos JPG, JPEG ou PNG são permitidos.',
                })
                inputElement.value = ""; // Limpa a seleção de arquivo inválido
              }
            });
          </script>

          <div class="mt-3">

            <?php $sql = $conn->query("SELECT * FROM propostas_arq WHERE parq_prop_id = '$prop_id' AND parq_categoria = 3");
            while ($arq = $sql->fetch(PDO::FETCH_ASSOC)) {
              $parq_id        = $arq['parq_id'];
              $parq_prop_id   = $arq['parq_prop_id'];
              $parq_categoria = $arq['parq_categoria'];
              $parq_arquivo   = $arq['parq_arquivo'];
            ?>

              <div class="result_file mt-2">
                <div class="result_file_name"><a href="uploads/propostas/<?= $prop_codigo . '/' . $parq_categoria . '/' . $parq_arquivo ?>" target="_blank"><?= $parq_arquivo ?></a></div>
                <span class="item_bt_row">
                  <a href="controller/controller_propostas.php?funcao=exc_arq&ident=<?= $parq_id ?>&p=3&c=<?= $prop_codigo ?>&f=<?= $parq_arquivo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                </span>
              </div>

            <?php } ?>

          </div>

        </div>
      </div>


      <div class="col-lg-12">
        <div class="hstack gap-3 align-items-center justify-content-between mt-4">
          <a type="buttom" onclick="location.href='cad_proposta.php?tp=<?= base64_encode($propc_cat) ?>&st=<?= base64_encode(4) ?>&i=<?= $_GET['i'] ?>'" class="btn botao btn-light waves-effect">Voltar</a>

          <?php if ($prop_sta_status < 7) { ?>
            <button type="submit" class="btn botao botao_verde waves-effect">Concluir</button>
          <?php } else { ?>
            <a class="btn botao botao_disabled waves-effect">Concluir</a>
          <?php } ?>

        </div>
      </div>

    </div>

  </form>

</div>