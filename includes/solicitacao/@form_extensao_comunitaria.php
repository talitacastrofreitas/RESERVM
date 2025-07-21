<div class="card-body p-sm-4 p-3">

  <form class="needs-validation" method="POST" action="controller/controller_propostas.php?funcao=cad_prop_ext_comun" id="ValidaBotaoProgress" enctype="multipart/form-data" autocomplete="off" novalidate>

    <input type="hidden" class="form-control" name="prop_id" value="<?= base64_encode($prop_id) ?>" required>
    <input type="hidden" class="form-control" name="prop_tipo" value="<?= base64_encode($propc_cat) ?>" required>
    <input type="hidden" class="form-control" name="prop_codigo" value="<?= base64_encode($prop_codigo) ?>" required>
    <input type="hidden" class="form-control" name="prop_status_etapa" value="<?= base64_encode($prop_status_etapa) ?>" required>

    <div class="tit_section" id="pcp_ancora">
      <h3>Responsável</h3>
    </div>

    <div class="row grid gx-3 mb-5">

      <div class="col-12">
        <div class="row grid g-3 align-items-center">
          <div class="col-md-8 d-flex">
            <p style="color: #0461AD; margin: 0; background: #D6E9F8; border-radius: 4px; padding: 10px 10px;">Cadastre, no mínimo, uma pessoa na comunidade/território</p>
          </div>

          <div class="col-md-4 text-end d-grid d-md-block">
            <a class="btn botao botao_azul_escuro waves-effect" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_responsavel">+ Cadastrar Responsável</a>
          </div>
        </div>
      </div>

      <table id="tab_responsavel" class="table dt-responsive nowrap align-middle mt-3 w-100">
        <thead>
          <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Contato</th>
            <th width="20px"></th>
          </tr>
        </thead>
        <tbody>

          <?php
          // SE HOUVER APENAS UM RESPONSÁVEL CADASTRADA, O BOTÃO "EXCLUIR" DESAPARECE
          $sql = "SELECT COUNT(*) FROM propostas_extensao_responsavel_contato WHERE prc_proposta_id = :prc_proposta_id";
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(":prc_proposta_id", $prop_id);
          $stmt->execute();
          if ($stmt->fetchColumn() > 1) {
            $ativa_link_excluir = '';
          } else {
            $ativa_link_excluir = 'd-none';
          }
          // -------------------------------

          try {
            $stmt = $conn->prepare("SELECT * FROM propostas_extensao_responsavel_contato WHERE prc_proposta_id = '$prop_id'");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              extract($row);
          ?>

              <tr>
                <th>
                  <nobr><?= $prc_nome ?></nobr>
                </th>
                <td>
                  <nobr><?= $prc_email ?></nobr>
                </td>
                <td>
                  <nobr><?= $prc_contato ?></nobr>
                </td>
                <td class="text-end">
                  <div class="dropdown drop_tabela d-inline-block">
                    <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="ri-more-fill align-middle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_responsavel"
                          data-bs-prc_id="<?= base64_encode($prc_id) ?>"
                          data-bs-prc_proposta_id="<?= base64_encode($prc_proposta_id) ?>"
                          data-bs-prc_nome="<?= $prc_nome ?>"
                          data-bs-prc_contato="<?= $prc_contato ?>"
                          data-bs-prc_email="<?= $prc_email ?>"
                          title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                      <li class="<?= $ativa_link_excluir ?>"><a href="controller/controller_responsavel_contato.php?funcao=exc_prc&prc_id=<?= base64_encode($prc_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                    </ul>
                  </div>
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

    <div class="tit_section" id="parq_ancora">
      <h3>Programa de Extensão Comunitária</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="mb-3">
          <?php try {
            $sql = $conn->query("SELECT cec_id, cec_extensao_comunitaria, cec_desc FROM conf_extensao_comunitaria WHERE cec_status = 1 ORDER BY cec_extensao_comunitaria");
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar o perfil";
          } ?>
          <label class="form-label">Tipo de programa de extensão comunitária <span>*</span></label>
          <select class="form-select" name="prop_ext_tipo_programa" required>
            <option selected value="<?= $cec_id ?>"><?= $cec_extensao_comunitaria ?></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['cec_id'] ?>"><?= $res['cec_extensao_comunitaria'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

    </div>

    <div class="tit_section" id="parq_ancora">
      <h3>Tipo do Evento Social</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-xl-4">
        <div class="mb-3">
          <?php try {
            $sql = $conn->query("SELECT tes_id, tes_evento_social FROM conf_tipo_evento_social ORDER BY CASE WHEN tes_evento_social = 'OUTRO' THEN 1 ELSE 0 END, tes_evento_social");
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar o perfil";
          } ?>
          <label class="form-label">Tipo de programa de extensão comunitária <span>*</span></label>
          <select class="form-select text-uppercase" name="prop_ext_categoria_evento" id="prop_ext_categoria_evento" required>
            <option selected value="<?= $tes_id ?>"><?= $tes_evento_social ?></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['tes_id'] ?>"><?= $res['tes_evento_social'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-xl-4" id="campo_prop_ext_categoria_evento_outro" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Descreve o tipo de evento social <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="prop_ext_categoria_evento_outro" id="prop_ext_categoria_evento_outro" value="<?= $prop_ext_categoria_evento_outro ?>" maxlength="50">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <script>
        const prop_ext_categoria_evento = document.getElementById("prop_ext_categoria_evento");
        const campo_prop_ext_categoria_evento_outro = document.getElementById("campo_prop_ext_categoria_evento_outro");

        prop_ext_categoria_evento.addEventListener("change", function() {
          if (prop_ext_categoria_evento.value === "10") {
            campo_prop_ext_categoria_evento_outro.style.display = "block";
            document.getElementById("prop_ext_categoria_evento_outro").required = true;
          } else {
            campo_prop_ext_categoria_evento_outro.style.display = "none";
            document.getElementById("prop_ext_categoria_evento_outro").required = false;
          }
        });

        if (prop_ext_categoria_evento.value === "10") {
          campo_prop_ext_categoria_evento_outro.style.display = "block";
          document.getElementById("prop_ext_categoria_evento_outro").required = true;
        }
      </script>

    </div>

    <div class="tit_section" id="parq_ancora">
      <h3>Informações Adicionais</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Instituição/comunidade atendida <span>*</span></label>
          <textarea class="form-control" name="prop_ext_inst_atendida" rows="3"><?= str_replace('<br />', '', $prop_ext_inst_atendida) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Atividades que serão desenvolvidas durante a ação <span>*</span></label>
          <textarea class="form-control" name="prop_ext_atividades" rows="3"><?= str_replace('<br />', '', $prop_ext_atividades) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Datas e horários as atividades <span>*</span></label>
          <textarea class="form-control" name="prop_ext_datas_horas" rows="3"><?= str_replace('<br />', '', $prop_ext_datas_horas) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Mobiliário e Equipamentos <span>*</span></label>
          <textarea class="form-control" name="prop_ext_mob_equipamento" rows="3"><?= str_replace('<br />', '', $prop_ext_mob_equipamento) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Descreva como será a dinâmica do espaço <span>*</span></label>
          <textarea class="form-control" name="prop_ext_dinamica" rows="3"><?= str_replace('<br />', '', $prop_ext_dinamica) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="mb-3">
          <label class="form-label">Quantidade prevista de atendimentos <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="prop_ext_quant_atendimento" value="<?= $prop_ext_quant_atendimento ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="mb-3">
          <?php try {
            $sql = $conn->query("SELECT tip_id, tip_tipo_ingresso FROM tipo_ingresso_participante ORDER BY CASE WHEN tip_tipo_ingresso = 'OUTRO' THEN 1 ELSE 0 END, tip_tipo_ingresso");
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar o perfil";
          } ?>
          <label class="form-label">Forma de ingresso do aluno participante <span>*</span></label>
          <select class="form-select text-uppercase" name="prop_ext_forma_ingresso" id="prop_ext_forma_ingresso" required>
            <option selected value="<?= $tip_id ?>"><?= $tip_tipo_ingresso ?></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['tip_id'] ?>"><?= $res['tip_tipo_ingresso'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-lg-4" id="campo_prop_ext_valor_bolsa" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Valor da bolsa</label>
          <input type="text" class="form-control money" name="prop_ext_valor_bolsa" id="prop_ext_valor_bolsa" value="<?= $prop_ext_valor_bolsa ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" placeholder="R$">
        </div>

        <script>
          const prop_ext_forma_ingresso = document.getElementById("prop_ext_forma_ingresso");
          const campo_prop_ext_valor_bolsa = document.getElementById("campo_prop_ext_valor_bolsa");

          prop_ext_forma_ingresso.addEventListener("change", function() {
            if (prop_ext_forma_ingresso.value === "1") {
              campo_prop_ext_valor_bolsa.style.display = "block";
            } else {
              campo_prop_ext_valor_bolsa.style.display = "none";
            }
          });

          if (prop_ext_forma_ingresso.value === "1") {
            campo_prop_ext_valor_bolsa.style.display = "block";
          }
        </script>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Quais atendimentos serão ofertados <span>*</span></label>
          <textarea class="form-control" name="prop_ext_atendimento_ofertado" rows="3"><?= str_replace('<br />', '', $prop_ext_atendimento_ofertado) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Qual impacto social esperado <span>*</span></label>
          <textarea class="form-control" name="prop_ext_impacto_social" rows="3"><?= str_replace('<br />', '', $prop_ext_impacto_social) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Comentários e solicitações complementares <span>*</span></label>
          <textarea class="form-control" name="prop_ext_obs" rows="3"><?= str_replace('<br />', '', $prop_ext_obs) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
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
            <label class="form-label m-0">Arquivos diversos <span>*</span></label>
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


      <div class="col-lg-12">
        <div class="hstack gap-3 align-items-center justify-content-between mt-4">
          <a type="buttom" onclick="location.href='cad_proposta.php?tp=<?= base64_encode($propc_cat) ?>&st=<?= base64_encode(4) ?>&i=<?= $_GET['i'] ?>'" class="btn botao btn-light waves-effect">Voltar</a>

          <?php if (empty($prc_id)) { ?>
            <button type="submit" class="btn botao botao_verde waves-effect" disabled>Próximo</button>
          <?php } else { ?>

            <?php if ($prop_sta_status < 7) { ?>
              <button type="submit" class="btn botao botao_verde waves-effect">Concluir</button>
            <?php } else { ?>
              <a class="btn botao botao_disabled waves-effect">Concluir</a>
            <?php } ?>

          <?php } ?>

        </div>
      </div>

    </div>

  </form>

</div>


<?php include 'includes/modal/modal_responsavel.php'; ?>