<div class="card-body p-sm-4 p-3">

  <form class="needs-validation" method="POST" action="controller/controller_propostas.php" enctype="multipart/form-data" autocomplete="off" novalidate>

    <input type="hidden" class="form-control" name="prop_id" value="<?= base64_encode($prop_id) ?>" required>
    <input type="hidden" class="form-control" name="prop_tipo" value="<?= base64_encode($propc_cat) ?>" required>
    <input type="hidden" class="form-control" name="prop_codigo" value="<?= base64_encode($prop_codigo) ?>" required>

    <div class="tit_section" id="parq_ancora">
      <h3>Divulgação e Promoção da Atividade</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12 mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="prop_card" id="prop_card" value="1" <?php echo ($prop_card == 1) ? 'checked' : ''; ?>>
          <label class="form-check-label" for="prop_vinculo_programa">Será necessário card para divulgação</label>
        </div>
      </div>

      <div id="campo_promocao" style="display: none;">

        <div class="col-12">
          <div class="mb-4">
            <div class="mb-2">
              <label class="form-label m-0">Sugestão de imagem para inspiração <span>*</span></label>
              <label class="label_info d-inline ms-0 ms-md-1">(Limite de imagens: 2 imagens nos formatos .jpg, jpeg ou .png)</label>
            </div>
            <div class="input-group">
              <?php
              $sql = "SELECT COUNT(*) FROM propostas_arq WHERE parq_prop_id = :parq_prop_id AND parq_categoria = 1";
              $stmt = $conn->prepare($sql);
              $stmt->bindParam(":parq_prop_id", $prop_id);
              $stmt->execute();
              $quant_img = $stmt->fetchColumn();
              if ($quant_img == 0) { ?>
                <input type="file" class="form-control input_arquivo" name="arquivo_img[]" multiple id="arquivo_img" onchange="imgCert(this)" accept=".jpg, .jpeg, .png">
              <?php } else if ($quant_img == 1) { ?>
                <input type="file" class="form-control input_arquivo" name="arquivo_img[]" onchange="imgCert(this)" accept=".jpg, .jpeg, .png">
              <?php } else { ?>
                <input type="file" class="form-control input_arquivo" name="" disabled>
              <?php } ?>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              // ARQUIVO NÃO PODE ULTRAPASSAR 10MB
              document.addEventListener("DOMContentLoaded", function() {
                const inputFile = document.getElementById("arquivo_img");

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

              // PERMITE APENAS 2 ARQUIVOS
              document.getElementById("arquivo_img").addEventListener("change", function() {
                var inputElement = this;
                var maxFiles = 2;
                var allowedExtensions = ["jpg", "jpeg", "png"];
                var fileExtension = inputElement.value.split(".").pop().toLowerCase();
                if (inputElement.files.length > maxFiles) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Limite de imagens excedida!',
                    text: 'Por favor, selecione até ' + maxFiles + ' arquivos.',
                  })
                  inputElement.value = "";
                };

                // PERMITE APENAS FORMATO JPG E PNG
                if (allowedExtensions.indexOf(fileExtension) === -1) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Erro encontrado!',
                    text: 'Formato de arquivo inválido. Apenas arquivos JPG, JPEG ou PNG são permitidos.',
                  })
                  inputElement.value = ""; // Limpa a seleção de arquivo inválido
                }
              });
            </script>

            <?php $sql = $conn->query("SELECT * FROM propostas_arq WHERE parq_prop_id = '$prop_id' AND parq_categoria = 1");
            while ($arq = $sql->fetch(PDO::FETCH_ASSOC)) {
              $parq_id        = $arq['parq_id'];
              $parq_prop_id   = $arq['parq_prop_id'];
              $parq_categoria = $arq['parq_categoria'];
              $parq_arquivo   = $arq['parq_arquivo'];
            ?>

              <div class="result_file mt-2">
                <div class="result_file_name"><a href="uploads/propostas/<?= $prop_codigo . '/' . $parq_categoria . '/' . $parq_arquivo ?>" target="_blank"><?= $parq_arquivo ?></a></div>
                <span class="item_bt_row">
                  <a href="controller/controller_propostas.php?funcao=exc_arq&ident=<?= $parq_id ?>&p=1&c=<?= $prop_codigo ?>&f=<?= $parq_arquivo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                </span>
              </div>

            <?php } ?>


          </div>
        </div>

        <div class="col-12 mt-2">
          <div class="mb-3">
            <label class="form-label">Descreva o texto para divulgação dessas atividades nas redes sociais <span>*</span></label>
            <textarea class="form-control" name="prop_texto_divulgacao" id="prop_texto_divulgacao" rows="3"><?= str_replace('<br />', '', $prop_texto_divulgacao) ?></textarea>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12 mt-2">
          <div class="mb-3">
            <label class="form-label mb-0">Quais os diferencias ofertados <span>*</span></label>
            <div class="label_info mb-2 mt-0">Abaixo sugestões de questionamentos que podem ajudar você na contextualização desses diferenciais: <br>
              - A atividade é uma novidade na área?<br>
              - O tema tratado na atividade é atual?<br>
              - Existe uma demanda por profissionais qualificados nessa modalidade?<br>
              - Os professores são referências no assunto?<br>
              - As aulas são práticas? Como é pensado o currículo do curso?
            </div>
            <textarea class="form-control" name="prop_diferenciais" id="prop_diferenciais" rows="3"><?= str_replace('<br />', '', $prop_diferenciais) ?></textarea>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12 mt-2 d-none">
          <div class="mb-3">
            <label class="form-label mb-0">Briefing <span>*</span></label>
            <div class="label_info mb-2 mt-0">(Aqui você deve destacar todas as informações imprescindíveis para o desenvolvimento de um trabalho criativo e certeiro e que resulte em uma comunicação eficaz, com resultados positivos. As informações constantes aqui são as responsáveis pelas brilhantes ideias que nascem no nosso processo de criação. Então, não se intimide, escreva tudo que achar relevante sobre o trabalho que está solicitando. Cada detalhe)</div>
            <textarea class="form-control" name="prop_brienfing" id="" rows="3"></textarea>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12 mb-3" id="parq_ancora_arq">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="prop_parceria" name="prop_parceria" value="1" <?php echo ($prop_parceria == 1) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="prop_parceria">O projeto tem parceria</label>
          </div>
        </div>

        <div class="col-12" id="campo_arquivo_logo" style="display: none;">
          <div class="mb-3">
            <label class="form-label">Anexar logo vetorizada das instituições parceiras <span>*</span></label>
            <div class="input-group">
              <input type="file" class="form-control input_arquivo" name="arquivo[]" multiple id="arquivo" onchange="imgCert(this)">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              // MOSTRA CAMPO QUANDO CLICAR NO CHECKBOX
              var prop_parceria = document.getElementById("prop_parceria");
              var campo_arquivo_logo = document.getElementById("campo_arquivo_logo");
              prop_parceria.addEventListener("change", function() {
                if (prop_parceria.checked) {
                  campo_arquivo_logo.style.display = "block"; // Mostrar o campo de entrada
                } else {
                  campo_arquivo_logo.style.display = "none"; // Ocultar o campo de entrada
                }
              });
              if (prop_parceria.checked) {
                campo_arquivo_logo.style.display = "block"; // Mostrar o campo de entrada
              }
            </script>
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
            </script>

            <?php $sql = $conn->query("SELECT * FROM propostas_arq WHERE parq_prop_id = '$prop_id' AND parq_categoria = 2");
            while ($arq = $sql->fetch(PDO::FETCH_ASSOC)) {
              $parq_id        = $arq['parq_id'];
              $parq_prop_id   = $arq['parq_prop_id'];
              $parq_categoria = $arq['parq_categoria'];
              $parq_arquivo   = $arq['parq_arquivo'];
            ?>

              <div class="result_file mt-2">
                <div class="result_file_name"><a href="uploads/propostas/<?= $prop_codigo . '/' . $parq_categoria . '/' . $parq_arquivo ?>" target="_blank"><?= $parq_arquivo ?></a></div>
                <span class="item_bt_row">
                  <a href="controller/controller_propostas.php?funcao=exc_arq&ident=<?= $parq_id ?>&p=2&c=<?= $prop_codigo ?>&f=<?= $parq_arquivo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                </span>
              </div>

            <?php } ?>


          </div>

          <script>
            // MOSTRA CAMPO QUANDO CLICAR NO CHECKBOX
            var prop_parceria = document.getElementById("prop_parceria");
            var campo_arquivo_logo = document.getElementById("campo_arquivo_logo");
            prop_parceria.addEventListener("change", function() {
              if (prop_parceria.checked) {
                campo_arquivo_logo.style.display = "block"; // Mostrar o campo de entrada
              } else {
                campo_arquivo_logo.style.display = "none"; // Ocultar o campo de entrada
              }
            });
            if (prop_parceria.checked) {
              campo_arquivo_logo.style.display = "block"; // Mostrar o campo de entrada
            }
          </script>
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
          </script>
        </div>

        <div class="col-12 mt-2">
          <div class="mb-3">
            <label class="form-label">Demais informações</label>
            <textarea class="form-control" name="prop_informacoes" rows="3"><?= str_replace('<br />', '', $prop_informacoes) ?></textarea>
          </div>
        </div>

      </div>

      <script>
        var prop_card = document.getElementById("prop_card");
        var campo_promocao = document.getElementById("campo_promocao");
        var prop_parceria = document.getElementById("prop_parceria");

        prop_parceria.addEventListener("change", function() {
          if (prop_card.checked && prop_parceria.checked) {
            document.getElementById('arquivo').required = true;
          } else {
            document.getElementById('arquivo').required = false;
          }
        });
      </script>

    </div>

    <script>
      // MOSTRA CAMPO QUANDO CLICAR NO CHECKBOX
      var prop_card = document.getElementById("prop_card");
      var campo_promocao = document.getElementById("campo_promocao");

      prop_card.addEventListener("change", function() {
        if (prop_card.checked) {
          campo_promocao.style.display = "block"; // Mostrar o campo de entrada
          <?php if ($quant_img == 0) { ?>
            document.getElementById('arquivo_img').required = true;
          <?php } ?>
          document.getElementById('prop_texto_divulgacao').required = true;
          document.getElementById('prop_diferenciais').required = true;
          // document.getElementById('prop_brienfing').required = true;
        } else {
          campo_promocao.style.display = "none"; // Ocultar o campo de entrada
          <?php if ($quant_img == 0) { ?>
            document.getElementById('arquivo_img').required = false;
          <?php } ?>
          document.getElementById('prop_texto_divulgacao').required = false;
          document.getElementById('prop_diferenciais').required = false;
          // document.getElementById('prop_brienfing').required = false;
        }
      });
      if (prop_card.checked) {
        campo_promocao.style.display = "block"; // Mostrar o campo de entrada
        <?php if ($quant_img == 0) { ?>
          document.getElementById('arquivo_img').required = true;
        <?php } ?>
        document.getElementById('prop_texto_divulgacao').required = true;
        document.getElementById('prop_diferenciais').required = true;
        // document.getElementById('prop_brienfing').required = true;
      }
    </script>


    <div class="col-lg-12">
      <div class="hstack gap-3 align-items-center justify-content-between mt-4">
        <a type="buttom" onclick="location.href='cad_proposta.php?tp=<?= base64_encode($propc_cat) ?>&st=<?= base64_encode(3) ?>&i=<?= $_GET['i'] ?>'" class="btn botao btn-light waves-effect">Voltar</a>

        <?php if ($prop_sta_status < 7) { ?>
          <button type="submit" class="btn botao botao_verde waves-effect" name="CadPropostaStep4">Próximo</button>
        <?php } else { ?>
          <a class="btn botao botao_disabled waves-effect">Próximo</a>
        <?php } ?>

      </div>
    </div>

  </form>

</div>