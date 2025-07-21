<div class="card-body p-sm-4 p-3">

  <form class="needs-validation" method="POST" action="controller/controller_propostas.php?funcao=cad_prop_curso" enctype="multipart/form-data" id="ValidaBotaoProgress" autocomplete="off" novalidate>

    <input type="hidden" class="form-control" name="prop_id" value="<?= base64_encode($prop_id) ?>" required>
    <input type="hidden" class="form-control" name="prop_tipo" value="<?= base64_encode($propc_cat) ?>" required>
    <input type="hidden" class="form-control" name="prop_codigo" value="<?= base64_encode($prop_codigo) ?>" required>
    <input type="hidden" class="form-control" name="prop_status_etapa" value="<?= base64_encode($prop_status_etapa) ?>" required>

    <div class="row grid gx-3">

      <div class="tit_section" id="cmod_ancora">
        <h3>Cadastrar Disciplina / Módulo</h3>
      </div>

      <div class="col-12">
        <div class="row grid g-3 align-items-center">
          <div class="col-md-8 d-flex">
            <p style="color: #0461AD; margin: 0; background: #D6E9F8; border-radius: 4px; padding: 10px 10px;">Cadastre pelo menos uma disciplina ou módulo.</p>
          </div>

          <div class="col-md-4 text-end d-grid d-md-block">
            <a class="btn botao botao_azul_escuro waves-effect" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_disciplina_modulo">+ Cadastrar Disciplina/Módulo</a>
          </div>
        </div>
      </div>

      <div class="tab-pane mt-3" id="profile1" role="tabpanel">
        <div class="accordion custom-accordionwithicon accordion-flush accordion-fill-success mb-4" id="accordionFill_cp">

          <?php
          // SELECIONA O DADO COM A DATA DE CADASTRO MAIS ALTUAL - USANDO PARA DEIXAR O ACORDION ABERTO NESTA DATA
          $stmt = $conn->prepare(" SELECT TOP 1 * FROM propostas_cursos_modulo WHERE prop_cmod_prop_id = :prop_cmod_prop_id ORDER BY prop_cmod_data_cad DESC");
          $stmt->bindParam(':prop_cmod_prop_id', $prop_id, PDO::PARAM_STR);
          $stmt->execute();
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          $id_max_data = isset($result['prop_cmod_id']) ? $result['prop_cmod_id'] : NULL;
          // -------------------------------

          // SE HOUVER APENAS UMA DISCIPLINA/MÓDULO CADASTRADA, O BOTÃO "EXCLUIR" DESAPARECE
          $sql = "SELECT COUNT(*) FROM propostas_cursos_modulo WHERE prop_cmod_prop_id = :prop_cmod_prop_id";
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(":prop_cmod_prop_id", $prop_id);
          $stmt->execute();
          if ($stmt->fetchColumn() > 1) {
            $ativa_link_excluir = '';
          } else {
            $ativa_link_excluir = 'd-none';
          }
          // -------------------------------

          $sql = $conn->query("SELECT * FROM propostas_cursos_modulo
                              INNER JOIN conf_tipo_espaco_organizacao ON conf_tipo_espaco_organizacao.esporg_id = propostas_cursos_modulo.prop_cmod_organizacao
                              INNER JOIN forma_pagamento ON forma_pagamento.for_pag_id = propostas_cursos_modulo.prop_cmod_forma_pagamento
                              WHERE prop_cmod_prop_id = '$prop_id' ORDER BY prop_cmod_data_cad DESC");
          while ($prop_cmod = $sql->fetch(PDO::FETCH_ASSOC)) {
            $prop_cmod_id                = $prop_cmod['prop_cmod_id'];
            $prop_cmod_prop_id           = $prop_cmod['prop_cmod_prop_id'];
            $prop_cmod_tipo_docente      = $prop_cmod['prop_cmod_tipo_docente'];
            $prop_cmod_nome_docente      = htmlspecialchars($prop_cmod['prop_cmod_nome_docente'], ENT_QUOTES, 'UTF-8');
            $prop_cmod_titulo            = htmlspecialchars($prop_cmod['prop_cmod_titulo'], ENT_QUOTES, 'UTF-8');
            $prop_cmod_assunto           = $prop_cmod['prop_cmod_assunto'];
            $prop_cmod_data_hora         = $prop_cmod['prop_cmod_data_hora'];
            $prop_cmod_organizacao       = $prop_cmod['prop_cmod_organizacao'];
            $prop_cmod_outra_organizacao = $prop_cmod['prop_cmod_outra_organizacao'];
            $prop_cmod_curriculo         = $prop_cmod['prop_cmod_curriculo'];
            $prop_cmod_forma_pagamento   = $prop_cmod['prop_cmod_forma_pagamento'];
            $prop_cmod_user_id           = $prop_cmod['prop_cmod_user_id'];
            $prop_cmod_data_cad          = $prop_cmod['prop_cmod_data_cad'];
            $prop_cmod_data_upd          = $prop_cmod['prop_cmod_data_upd'];
            // ORGANIZAÇÃO
            $esporg_id                   = htmlspecialchars($prop_cmod['esporg_id'], ENT_QUOTES, 'UTF-8');
            $esporg_espaco_organizacao   = htmlspecialchars($prop_cmod['esporg_espaco_organizacao'], ENT_QUOTES, 'UTF-8');
            // PAGAMENTO
            $for_pag_id                  = htmlspecialchars($prop_cmod['for_pag_id'], ENT_QUOTES, 'UTF-8');
            $for_pag_forma_pagamento     = htmlspecialchars($prop_cmod['for_pag_forma_pagamento'], ENT_QUOTES, 'UTF-8');

            // CONFIGURAÇÃO DOCENTE INTERNO E EXTERNO
            if ($prop_cmod_tipo_docente == 1) {
              $prop_cmod_nome_docente_int = $prop_cmod_nome_docente;
              $prop_cmod_nome_docente_ext = '';
            } else {
              $prop_cmod_nome_docente_ext = $prop_cmod_nome_docente;
              $prop_cmod_nome_docente_int = '';
            }

            // CONFIGURAÇÃO DA FORMATAÇÃO
            $prop_cmod_assunto_cmd = nl2br($prop_cmod_assunto);
            $prop_cmod_assunto_cmd = str_replace('<br />', '', $prop_cmod_assunto_cmd);
            $prop_cmod_assunto_cmd = str_replace('"', '&quot;', $prop_cmod_assunto_cmd); // MOSTRA ASPAS DUPLAS

            $prop_cmod_data_hora_cmod = nl2br($prop_cmod_data_hora);
            $prop_cmod_data_hora_cmod = str_replace('<br />', '', $prop_cmod_data_hora_cmod);
            $prop_cmod_data_hora_cmod = str_replace('"', '&quot;', $prop_cmod_data_hora_cmod); // MOSTRA ASPAS DUPLAS

            $prop_cmod_outra_organizacao_cmod = nl2br($prop_cmod_outra_organizacao);
            $prop_cmod_outra_organizacao_cmod = str_replace('<br />', '', $prop_cmod_outra_organizacao_cmod);
            $prop_cmod_outra_organizacao_cmod = str_replace('"', '&quot;', $prop_cmod_outra_organizacao_cmod); // MOSTRA ASPAS DUPLAS

            $prop_cmod_curriculo_cmod = nl2br($prop_cmod_curriculo);
            $prop_cmod_curriculo_cmod = str_replace('<br />', '', $prop_cmod_curriculo_cmod);
            $prop_cmod_curriculo_cmod = str_replace('"', '&quot;', $prop_cmod_curriculo_cmod); // MOSTRA ASPAS DUPLAS
          ?>

            <div class="accordion-item">
              <h2 class="accordion-header" id="accordionFill<?= $prop_cmod_id ?>">
                <button class="accordion-button fw-semibold <?php if ($id_max_data != $prop_cmod_id) {
                                                              echo 'collapsed';
                                                            } ?>" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill<?= $prop_cmod_id ?>" aria-expanded="true" aria-controls="accor_fill<?= $prop_cmod_id ?>"><?= $prop_cmod_titulo ?></button>
              </h2>
              <div id="accor_fill<?= $prop_cmod_id ?>" class="accordion-collapse collapse <?php if ($id_max_data == $prop_cmod_id) {
                                                                                            echo 'show';
                                                                                          } ?>" aria-labelledby="accordionFill<?= $prop_cmod_id ?>" data-bs-parent="#accordionFill_cp">
                <div class="accordion-body">

                  <div class="row dados_user_tabela">

                    <div class="col-10 col-sm-11">

                      <div class="row">

                        <div class="col-12">
                          <label>Docente Interno</label>
                          <p><?= $prop_cmod_nome_docente ?></p>
                        </div>



                      </div>

                    </div>

                    <div class="col-2 col-sm-1 text-end">
                      <div class="dropdown drop_tabela d-inline-block">
                        <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_disciplina_modulo"
                              data-bs-prop_cmod_id="<?= base64_encode($prop_cmod_id) ?>"
                              data-bs-prop_cmod_prop_id="<?= base64_encode($prop_cmod_prop_id) ?>"
                              data-bs-prop_cmod_tipo_docente="<?= $prop_cmod_tipo_docente ?>"
                              data-bs-prop_cmod_nome_docente="<?= $prop_cmod_nome_docente ?>"
                              data-bs-prop_cmod_titulo="<?= $prop_cmod_titulo ?>"
                              data-bs-prop_cmod_assunto="<?= str_replace('<br />', '', $prop_cmod_assunto) ?>"
                              data-bs-prop_cmod_data_hora="<?= str_replace('<br />', '', $prop_cmod_data_hora) ?>"
                              data-bs-prop_cmod_organizacao="<?= $prop_cmod_organizacao ?>"
                              data-bs-prop_cmod_outra_organizacao="<?= str_replace('<br />', '', $prop_cmod_outra_organizacao) ?>"
                              data-bs-prop_cmod_forma_pagamento="<?= $prop_cmod_forma_pagamento ?>"
                              data-bs-prop_cmod_curriculo="<?= str_replace('<br />', '', $prop_cmod_curriculo_cmod) ?>"
                              title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                          <li class="<?= $ativa_link_excluir ?>"><a href="controller/controller_propostas_cursos_modulo.php?funcao=exc_cmod&ident=<?= base64_encode($prop_cmod_id) ?>&i=<?= base64_encode($prop_cmod_prop_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                        </ul>
                      </div>

                    </div>

                    <div class="col-12">
                      <label>Assuntos</label>
                      <p><?= $prop_cmod_assunto ?></p>
                    </div>

                    <div class="col-12">
                      <label>Datas e Horários</label>
                      <p><?= $prop_cmod_data_hora ?></p>
                    </div>

                    <?php if ($prop_cmod_organizacao != 7) { ?>
                      <div class="col-12">
                        <label>Formato de Organização da Sala</label>
                        <p><?= $esporg_espaco_organizacao ?></p>
                      </div>
                    <?php } else { ?>
                      <div class="col-12">
                        <label>Formato de Organização da Sala</label>
                        <p><?= $prop_cmod_outra_organizacao ?></p>
                      </div>
                    <?php } ?>

                    <div class="col-12">
                      <label>Forma de Pagamento Docente</label>
                      <p><?= $for_pag_forma_pagamento ?></p>
                    </div>

                    <div class="col-12">
                      <label>Currículo Resumido</label>
                      <p><?= $prop_cmod_curriculo ?></p>
                    </div>

                  </div>

                </div>
              </div>
            </div>

          <?php } ?>

          <?php
          if (!isset($prop_cmod_id)) { ?>
            <div>
              <p>Nenhuma Disciplina ou Módulo cadastrado</p>
            </div>
          <?php } ?>

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
              <input type="file" class="form-control input_arquivo" name="arquivos[]" multiple id="arquivos" onchange="imgCert(this)" accept=".pdf, .xlsx, .xls, .doc, .docx, .csv, .jpg, .JPG, .jpeg, .png, .PNG">
            <?php } else if ($quant_arq <= 20) { ?>
              <input type="file" class="form-control input_arquivo" name="arquivos[]" id="arquivos" onchange="imgCert(this)" accept=".pdf, .xlsx, .xls, .doc, .docx, .csv, .jpg, .JPG, .jpeg, .png, .PNG">
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

    </div>

    <div class="col-lg-12">
      <div class="hstack gap-3 align-items-center justify-content-between mt-4">
        <a type="buttom" onclick="location.href='cad_proposta.php?tp=<?= base64_encode($propc_cat) ?>&st=<?= base64_encode(1) ?>&i=<?= $_GET['i'] ?>'" class="btn botao btn-light waves-effect">Voltar</a>
        <?php if (empty($prop_cmod_id)) { ?>
          <button type="submit" class="btn botao botao_verde waves-effect" disabled>Concluir</button>
        <?php } else { ?>

          <?php if ($prop_sta_status < 8) { ?>
            <button type="submit" class="btn botao botao_verde waves-effect">Concluir</button>
          <?php } else { ?>
            <a class="btn botao botao_disabled waves-effect">Concluir</a>
          <?php } ?>

        <?php } ?>

      </div>
    </div>

  </form>

</div>

<?php include 'includes/modal/modal_disciplina_modulo.php'; ?>