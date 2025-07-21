<div class="card-body p-sm-4 p-3">

  <form class="needs-validation" method="POST" action="controller/controller_propostas.php?funcao=cad_prop_eve_cient" id="ValidaBotaoProgress" enctype="multipart/form-data" autocomplete="off" novalidate>

    <input type="hidden" class="form-control" name="prop_id" value="<?= base64_encode($prop_id) ?>" required>
    <input type="hidden" class="form-control" name="prop_tipo" value="<?= base64_encode($propc_cat) ?>" required>
    <input type="hidden" class="form-control" name="prop_codigo" value="<?= base64_encode($prop_codigo) ?>" required>
    <input type="hidden" class="form-control" name="prop_status_etapa" value="<?= base64_encode($prop_status_etapa) ?>" required>

    <div class="tit_section">
      <h3>Informações Adicionais</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">O evento terá patrocínio? <span>*</span></label>
          <select class="form-select" name="prop_event_patrocinio" id="prop_event_patrocinio" required>
            <option selected value="<?= $prop_event_patrocinio ?>"><?= $prop_event_patrocinio ?></option>
            <option value="SIM">SIM</option>
            <option value="NÃO">NÃO</option>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_prop_event_qual_patrocinio" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Quais? <span>*</span></label>
          <textarea class="form-control" name="prop_event_qual_patrocinio" id="prop_event_qual_patrocinio" rows="3"><?= str_replace('<br />', '', $prop_event_qual_patrocinio) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
        <script>
          const prop_event_patrocinio = document.getElementById("prop_event_patrocinio");
          const campo_prop_event_qual_patrocinio = document.getElementById("campo_prop_event_qual_patrocinio");

          prop_event_patrocinio.addEventListener("change", function() {
            if (prop_event_patrocinio.value === "SIM") {
              campo_prop_event_qual_patrocinio.style.display = "block";
              document.getElementById("prop_event_qual_patrocinio").required = true;
            } else {
              campo_prop_event_qual_patrocinio.style.display = "none";
              document.getElementById("prop_event_qual_patrocinio").required = false;
            }
          });

          if (prop_event_patrocinio.value === "SIM") {
            campo_prop_event_qual_patrocinio.style.display = "block";
            document.getElementById("prop_event_qual_patrocinio").required = true;
          }
        </script>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">O evento terá parceria? <span>*</span></label>
          <select class="form-select" name="prop_event_parceria" id="prop_event_parceria" required>
            <option selected value="<?= $prop_event_parceria ?>"><?= $prop_event_parceria ?></option>
            <option value="SIM">SIM</option>
            <option value="NÃO">NÃO</option>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_prop_event_qual_parceria" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Quais? <span>*</span></label>
          <textarea class="form-control" name="prop_event_qual_parceria" id="prop_event_qual_parceria" rows="3"><?= str_replace('<br />', '', $prop_event_qual_parceria) ?></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
        <script>
          const prop_event_parceria = document.getElementById("prop_event_parceria");
          const campo_prop_event_qual_parceria = document.getElementById("campo_prop_event_qual_parceria");

          prop_event_parceria.addEventListener("change", function() {
            if (prop_event_parceria.value === "SIM") {
              campo_prop_event_qual_parceria.style.display = "block";
              document.getElementById("prop_event_qual_parceria").required = true;
            } else {
              campo_prop_event_qual_parceria.style.display = "none";
              document.getElementById("prop_event_qual_parceria").required = false;
              document.getElementById("prop_parc_orgao_empresa").value = "";
            }
          });

          if (prop_event_parceria.value === "SIM") {
            campo_prop_event_qual_parceria.style.display = "block";
            document.getElementById("prop_event_qual_parceria").required = true;
          }
        </script>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Nomes e contatos adicionais (Pessoa de contato em caso de indisponibilidade do Coordenador Geral)</label>
          <textarea class="form-control" name="prop_event_contatos" rows="3"><?= str_replace('<br />', '', $prop_event_contatos) ?></textarea>
        </div>
      </div>

      <div class="col-12 mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="prop_event_sorteio" value="1" <?php echo ($prop_event_sorteio == 1) ? 'checked' : ''; ?>>
          <label class="form-check-label" for="prop_vinculo_programa">Haverá sorteio durante o evento</label>
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

          <?php if ($prop_sta_status < 7) { ?>
            <button type="submit" class="btn botao botao_verde waves-effect" name="CadEventosCientificos">Concluir</button>
          <?php } else { ?>
            <a class="btn botao botao_disabled waves-effect">Concluir</a>
          <?php } ?>

        </div>
      </div>

    </div>

  </form>

</div>


<?php include 'includes/modal/modal_coordenador_projeto.php'; ?>
<?php include 'includes/modal/modal_equipe_executora.php'; ?>
<?php include 'includes/modal/modal_parceiro_patrocinador.php'; ?>