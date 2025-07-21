<div class="card-body p-sm-4 p-3">
  <form class="needs-validation meuFormulario form_solicitacao" method="POST" action="controller/controller_solicitacao.php" id="BotaoProgress" enctype="multipart/form-data" autocomplete="off" novalidate>
    <div class="row grid gx-3">

      <input type="hidden" class="form-control" name="solic_id" value="<?= $_GET['i'] ?>" required>
      <input type="hidden" class="form-control" name="solic_acao" value="atualizar_2" required>
      <input type="hidden" class="form-control" name="solic_etapa" value="2" required>

      <div class="col-12">
        <label class="form-label">Deseja realizar a solicitação de reserva de espaços para aulas práticas? <span>*</span></label>
        <div class="label_info label_info_verde">Os espaços de ensino para atividades práticas são: Laboratórios de Ensino, Clínica de Fisioterapia e Espaços Externos. </div>

        <div class="check_container">
          <div class="form-check form_solicita">
            <input class="form-check-input form_solicita" type="radio" name="solic_ap_aula_pratica" id="solic_ap_aula_pratica_sim" value="1" <?= isset($solic_ap_aula_pratica) && $solic_ap_aula_pratica == 1 ? 'checked' : ''; ?> required>
            <label class="form-check-label" for="solic_ap_aula_pratica_sim">Sim</label>
          </div>

          <div class="form-check form_solicita">
            <input class="form-check-input form_solicita" type="radio" name="solic_ap_aula_pratica" id="solic_ap_aula_pratica_nao" value="0" <?= isset($solic_ap_aula_pratica) && $solic_ap_aula_pratica == 0 ? 'checked' : ''; ?> required>
            <label class="form-check-label" for="solic_ap_aula_pratica_nao">Não</label>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>
      </div>

      <div id="campo_info_pratic_campus" style="display: none;">
        <div class="col-12">
          <div class="form_margem">
            <?php try {
              $sql = $conn->prepare("SELECT uni_id, uni_unidade FROM unidades ORDER BY uni_unidade ASC");
              $sql->execute();
              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              echo "Erro ao tentar recuperar os dados";
            } ?>
            <label class="form-label">Campus <span>*</span></label>
            <select class="form-select text-uppercase" name="solic_ap_campus" id="solic_ap_campus">
              <option selected value="<?= $campus_pratico_id  ?>"><?= $campus_pratico_nome ?></option>
              <?php foreach ($result as $res) : ?>
                <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
          <script>
            var solic_ap_aula_pratica_sim = document.getElementById("solic_ap_aula_pratica_sim");
            var campo_info_pratic_campus = document.getElementById("campo_info_pratic_campus");

            solic_ap_aula_pratica_sim.addEventListener("change", function() {
              if (solic_ap_aula_pratica_sim.checked) {
                campo_info_pratic_campus.style.display = "block";
                document.getElementById('solic_ap_campus').required = true;
              } else {
                campo_info_pratic_campus.style.display = "none";
                document.getElementById('solic_ap_campus').required = false;
              }
            });
            if (solic_ap_aula_pratica_sim.checked) {
              campo_info_pratic_campus.style.display = "block"; // Mostrar o campo de entrada
              document.getElementById('solic_ap_campus').required = true;
            }
          </script>
        </div>




        <div id="campo_info_pratic_espaco" style="display: none;">

          <div class="col-12" id="campo_info_pratic_espaco_brotas" style="display: none;">
            <?php try {
              $espaco_b = explode(", ", $solic_ap_espaco);
              $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local, esp_nome_local_resumido, esp_quant_maxima, UPPER(and_andar) as and_andar, UPPER(pav_pavilhao) AS pav_pavilhao
                                    FROM espaco
                                    LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                    INNER JOIN andares ON andares.and_id = espaco.esp_andar
                                    WHERE esp_tipo_espaco NOT IN (1, 5) AND esp_unidade = 2 ORDER BY esp_nome_local");
              $sql->execute();
              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar o perfil";
            } ?>
            <label class="form-label">Espaço sugerido <span>*</span></label>
            <div class="label_info label_info_verde">
              • A alocação de alunos depende do cenário escolhido, o qual varia conforme organização de mesas,
              cadeiras, bancadas e macas.<br>
              • Caso seja necessário, o solicitante pode selecionar mais de um espaço para realizar a prática.</div>
            <select class="form-select text-uppercase" name="solic_ap_espaco_brotas[]" multiple id="cad_reserva_local_brotas_mult" required>
              <!-- <option selected value=""></option> -->
              <?php foreach ($result as $res) : ?>
                <option value="<?= $res['esp_id'] ?>" <?= in_array($res['esp_id'], $espaco_b) ? 'selected' : '' ?>>
                  <?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] . ' - ' . $res['and_andar'] . ' - ' . $res['pav_pavilhao'] . ' - CAPACIDADE: ' . $res['esp_quant_maxima'] . ' ALUNOS' ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
            <script>
              $(document).ready(function() {
                $('#cad_reserva_local_brotas_mult').select2({
                  placeholder: "Selecione as opções",
                  tags: false,
                  allowClear: true,
                  // dropdownParent: $('#modal_cad_espaco'),
                  width: '100%'
                });
              });
            </script>
          </div>

          <div class="col-12" id="campo_info_pratic_espaco_cabula" style="display: none;">
            <?php try {
              $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local, esp_nome_local_resumido, esp_quant_maxima, UPPER(and_andar) as and_andar, UPPER(pav_pavilhao) AS pav_pavilhao
                                    FROM espaco
                                    LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                    INNER JOIN andares ON andares.and_id = espaco.esp_andar
                                    WHERE esp_tipo_espaco NOT IN (1, 5) AND esp_unidade = 1 ORDER BY esp_nome_local");
              $sql->execute();
              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar o perfil";
            } ?>
            <label class="form-label">Espaço sugerido <span>*</span></label>
            <div class="label_info label_info_verde">
              • A alocação de alunos depende do cenário escolhido, o qual varia conforme organização de mesas,
              cadeiras, bancadas e macas.<br>
              • Caso seja necessário, o solicitante pode selecionar mais de um espaço para realizar a prática.</div>
            <select class="form-select text-uppercase" name="solic_ap_espaco_cabula[]" multiple id="cad_reserva_local_cabula_mult" required>
              <!-- <option selected value=""></option> -->
              <?php foreach ($result as $res) : ?>
                <option value="<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] . ' - ' . $res['and_andar'] . ' - ' . $res['pav_pavilhao'] . ' - CAPACIDADE: ' . $res['esp_quant_maxima'] . ' ALUNOS' ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
            <script>
              $(document).ready(function() {
                $('#cad_reserva_local_cabula_mult').select2({
                  placeholder: "Selecione as opções",
                  tags: false,
                  allowClear: true,
                  // dropdownParent: $('#modal_cad_espaco'),
                  width: '100%'
                });
              });
            </script>
          </div>

          <div class="col-12 mt-3">
            <div class="form_margem">
              <?php try {
                $sql = $conn->prepare("SELECT ctp_id, ctp_turma FROM conf_turma_pratica ORDER BY ctp_turma ASC");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar os dados";
              } ?>
              <label class="form-label">Quantidade de turmas <span>*</span></label>
              <select class="form-select text-uppercase" name="solic_ap_quant_turma" id="solic_ap_quant_turma">
                <option selected value="<?= $ctp_id  ?>"><?= $ctp_turma ?></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['ctp_id'] ?>"><?= $res['ctp_turma'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>

        </div>

        <div id="campo_info_pratic_tipo_reserva" style="display: none;">

          <div class="col-12">
            <div class="form_margem">
              <label class="form-label">Número estimado de participantes <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="solic_ap_quant_particip" id="solic_ap_quant_particip" value="<?= $solic_ap_quant_particip ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Tipo da reserva <span>*</span></label>

            <div class="check_container">
              <div class="form-check form_solicita">
                <input type="radio" class="form-check-input form_solicita" id="solic_ap_tipo_reserva1" name="solic_ap_tipo_reserva" value="1" <?php echo ($solic_ap_tipo_reserva == 1) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="solic_ap_tipo_reserva1">Esporádica - Reserva em data(s) específica(s).</label>
              </div>
              <div class="form-check form_solicita">
                <input type="radio" class="form-check-input form_solicita" id="solic_ap_tipo_reserva2" name="solic_ap_tipo_reserva" value="2" <?php echo ($solic_ap_tipo_reserva == 2) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="solic_ap_tipo_reserva2">Fixa - Reserva permanente em determinado(s) dia(s) da semana, durante todo o semestre de acordo com o calendário acadêmico.</label>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
          </div>
          <script>
            const solic_ap_campus = document.getElementById("solic_ap_campus");
            const campo_info_pratic_espaco = document.getElementById("campo_info_pratic_espaco");
            //
            const campo_info_pratic_espaco_brotas = document.getElementById("campo_info_pratic_espaco_brotas");
            const campo_info_pratic_espaco_cabula = document.getElementById("campo_info_pratic_espaco_cabula");

            solic_ap_campus.addEventListener("change", function() {
              if (solic_ap_campus.value && solic_ap_campus.value === "1") {
                campo_info_pratic_espaco.style.display = "block";
                campo_info_pratic_espaco_cabula.style.display = "block";
                campo_info_pratic_espaco_brotas.style.display = "none";
                document.getElementById("cad_reserva_local_cabula_mult").required = true;
                document.getElementById("cad_reserva_local_brotas_mult").required = false;
                document.getElementById("solic_ap_quant_turma").required = true;
              } else {
                campo_info_pratic_espaco.style.display = "block";
                campo_info_pratic_espaco_cabula.style.display = "none";
                campo_info_pratic_espaco_brotas.style.display = "block";
                document.getElementById("cad_reserva_local_cabula_mult").required = false;
                document.getElementById("cad_reserva_local_brotas_mult").required = true;
                document.getElementById("solic_ap_quant_turma").required = true;
              }
            });

            if (solic_ap_campus.value && solic_ap_campus.value === "1") {
              campo_info_pratic_espaco.style.display = "block";
              campo_info_pratic_espaco_cabula.style.display = "block";
              campo_info_pratic_espaco_brotas.style.display = "none";
              document.getElementById("cad_reserva_local_cabula_mult").required = true;
              document.getElementById("cad_reserva_local_brotas_mult").required = false;
              document.getElementById("solic_ap_quant_turma").required = true;
            }

            if (solic_ap_campus.value && solic_ap_campus.value === "2") {
              campo_info_pratic_espaco.style.display = "block";
              campo_info_pratic_espaco_cabula.style.display = "none";
              campo_info_pratic_espaco_brotas.style.display = "block";
              document.getElementById("cad_reserva_local_cabula_mult").required = false;
              document.getElementById("cad_reserva_local_brotas_mult").required = true;
              document.getElementById("solic_ap_quant_turma").required = true;
            }
          </script>
          <script>
            const solic_ap_quant_turma = document.getElementById("solic_ap_quant_turma");
            const campo_info_pratic_tipo_reserva = document.getElementById("campo_info_pratic_tipo_reserva");

            solic_ap_quant_turma.addEventListener("change", function() {
              if (solic_ap_quant_turma.value) {
                campo_info_pratic_tipo_reserva.style.display = "block";
                document.getElementById("solic_ap_quant_particip").required = true;
                document.getElementById("solic_ap_tipo_reserva1").required = true;
                document.getElementById("solic_ap_tipo_reserva2").required = true;
              } else {
                campo_info_pratic_tipo_reserva.style.display = "none";
                document.getElementById("solic_ap_quant_particip").required = false;
                document.getElementById("solic_ap_tipo_reserva1").required = false;
                document.getElementById("solic_ap_tipo_reserva2").required = false;
              }
            });

            if (solic_ap_quant_turma.value) {
              campo_info_pratic_tipo_reserva.style.display = "block";
              document.getElementById("solic_ap_quant_particip").required = true;
              document.getElementById("solic_ap_tipo_reserva1").required = true;
              document.getElementById("solic_ap_tipo_reserva2").required = true;
            }
          </script>
        </div>

        <div id="campo_info_pratic_data_reserva" style="display: none;">

          <div class="col-12 mb-4" id="campo_info_pratic_dias_semana" style="display: none;">
            <?php try {
              $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
              $sql->execute();
              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar o perfil";
            } ?>
            <label class="form-label">Dia(s) da semana <span>*</span></label>
            <div class="label_info label_info_verde">Caso seu componente seja encerrado antes da última semana de finalização das aulas pelo calendário acadêmico, ou haja alguma exceção para alguma data do(s) dia(s) da semana selecionado, favor descrever no campo observação, para que a reserva não seja efetivada.</div>
            <select class="form-select text-uppercase" name="solic_ap_dia_reserva[]" multiple id="cad_solic_ap_dia_reserva">
              <!-- <option selected value=""></option> -->
              <?php foreach ($result as $res) : ?>
                <option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
            <script>
              $(document).ready(function() {
                $('#cad_solic_ap_dia_reserva').select2({
                  placeholder: "Selecione as opções",
                  tags: false,
                  allowClear: true,
                  // dropdownParent: $('#modal_cad_espaco'),
                  width: '100%'
                });
              });
            </script>
          </div>

          <div class="col-12" id="campo_info_pratic_datas" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Data(s) da reserva <span>*</span></label>
              <textarea class="form-control" name="solic_ap_data_reserva" id="solic_ap_data_reserva" rows="5"><?= htmlspecialchars(str_replace('<br />', '', $solic_ap_data_reserva)) ?></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form_margem">
                <label class="form-label">Horário inicial <span>*</span></label>
                <input type="time" class="form-control" name="solic_ap_hora_inicio" id="solic_ap_hora_inicio" value="<?php echo ($solic_ap_hora_inicio) ? date("H:i", strtotime($solic_ap_hora_inicio)) : ''; ?>">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form_margem">
                <label class="form-label">Horário final <span>*</span></label>
                <input type="time" class="form-control" name="solic_ap_hora_fim" id="solic_ap_hora_fim" value="<?php echo ($solic_ap_hora_fim) ? date("H:i", strtotime($solic_ap_hora_fim)) : ''; ?>">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Selecione como deseja informar quais serão os materiais, equipamentos e insumos necessários para a realização da aula nos espaços de prática <span>*</span></label>

            <div class="check_container">
              <div class="form-check form_solicita">
                <input type="radio" class="form-check-input form_solicita" id="validationFormCheck2" name="solic_ap_tipo_material" value="1" <?php echo ($solic_ap_tipo_material == 1) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="validationFormCheck2">Anexar o formulário de planejamento de atividades de práticas nos laboratórios de ensino.</label>
              </div>
              <div class="form-check form_solicita">
                <input type="radio" class="form-check-input form_solicita" id="validationFormCheck3" name="solic_ap_tipo_material" value="2" <?php echo ($solic_ap_tipo_material == 2) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="validationFormCheck3">Informar o título da aula (caso o formulário já esteja no banco de dados do laboratório de ensino).</label>
              </div>
              <div class="form-check form_solicita">
                <input type="radio" class="form-check-input form_solicita" id="validationFormCheck4" name="solic_ap_tipo_material" value="3" <?php echo ($solic_ap_tipo_material == 3) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="validationFormCheck4">Descrevê-los com as respectivas quantidades.</label>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
          </div>

          <script>
            function mostraCamposReserva() {
              // Obtém o radio selecionado
              let solic_ap_tipo_reserva = document.querySelector('input[name="solic_ap_tipo_reserva"]:checked');

              // Se houver um radio selecionado, exibe o campo correspondente
              if (solic_ap_tipo_reserva) {
                if (solic_ap_tipo_reserva.value === "1") {
                  document.getElementById("campo_info_pratic_datas").style.display = "block";
                  document.getElementById("solic_ap_data_reserva").required = true;
                  document.getElementById("campo_info_pratic_dias_semana").style.display = "none";
                  document.getElementById("cad_solic_ap_dia_reserva").required = false;
                  //
                  document.getElementById("solic_ap_hora_inicio").required = true;
                  document.getElementById("solic_ap_hora_fim").required = true;
                  //
                  document.getElementById("validationFormCheck2").required = true;
                  document.getElementById("validationFormCheck3").required = true;
                  document.getElementById("validationFormCheck4").required = true;
                } else if (solic_ap_tipo_reserva.value === "2") {
                  document.getElementById("campo_info_pratic_datas").style.display = "none";
                  document.getElementById("solic_ap_data_reserva").required = false;
                  document.getElementById("campo_info_pratic_dias_semana").style.display = "block";
                  document.getElementById("cad_solic_ap_dia_reserva").required = true;
                  //
                  document.getElementById("solic_ap_hora_inicio").required = true;
                  document.getElementById("solic_ap_hora_fim").required = true;
                  //
                  document.getElementById("validationFormCheck2").required = true;
                  document.getElementById("validationFormCheck3").required = true;
                  document.getElementById("validationFormCheck4").required = true;
                }
              }
            }

            // Executa ao mudar de opção
            document.querySelectorAll('input[name="solic_ap_tipo_reserva"]').forEach(radio => {
              radio.addEventListener('change', mostraCamposReserva);
            });

            // Executa ao carregar a página (para exibir o campo já marcado)
            mostraCamposReserva();
          </script>

          <script>
            document.addEventListener("DOMContentLoaded", () => {
              const radios = document.querySelectorAll('input[name="solic_ap_tipo_reserva"]');
              const campoInfo = document.getElementById("campo_info_pratic_data_reserva");

              // Função para exibir o campo se um rádio for selecionado
              function verificarSelecao() {
                const selecionado = document.querySelector('input[name="solic_ap_tipo_reserva"]:checked');
                campoInfo.style.display = selecionado ? "block" : "none";
              }

              // Verifica a seleção ao carregar a página
              verificarSelecao();

              // Adiciona o evento de mudança aos rádios
              radios.forEach(radio => {
                radio.addEventListener("change", verificarSelecao);
              });
            });
          </script>
        </div>

        <div id="campo_info_pratic_anexar" style="display: none;">

          <div class="col-12" id="file_ancora">
            <div class="form_margem">
              <label class="form-label m-0">Formulário de planejamento de atividades de práticas nos laboratórios de ensino <span>*</span></label>
              <label class="label_info label_info_verde">
                Anexe aqui o(s) Formulário(s) de Planejamento de Atividades de Práticas nos Laboratórios de Ensino, com a descrição e quantidade dos materiais, insumos e equipamentos necessários para a realização da aula prática. <br>
                Caso seja solicitado reserva para mais de uma aula: <br><br>
                • cada aula deve ter um formulário; <br>
                • cada formulário deve ter a data que a aula será realizada.
              </label>
              <div class="input-group">
                <input type="file" class="form-control input_arquivo" name="arquivos[]" multiple id="cad_info_pratic_arquivo" accept=".doc,.docx,.xls,.xlsx,.ppt,.pptx,.pdf,.jpg,.jpeg,.png,.gif,.bmp, .mp4,.avi,.mov,.mkv,
          .mp3,.wav,.ogg">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
              <label class="label_info label_info_verde">
                • Limite de número de arquivos: 10 <br>
                • Limite de tamanho de arquivo único: 1GB<br>
                • Tipos de arquivo permitidos: Word, Excel, PPT, PDF, Imagem, Vídeo, Áudio
              </label>

              <script>
                const inputArquivos = document.querySelector('input[type="file"]');
                const form = document.querySelector('form');

                const tiposPermitidos = [
                  "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // Word
                  "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // Excel
                  "application/vnd.ms-powerpoint", "application/vnd.openxmlformats-officedocument.presentationml.presentation", // PPT
                  "application/pdf", // PDF
                  "image/jpeg", "image/png", "image/gif", "image/bmp", // Imagens
                  "video/mp4", "video/x-msvideo", "video/quicktime", "video/x-matroska", // Vídeos
                  "audio/mpeg", "audio/wav", "audio/ogg" // Áudios
                ];

                // Validação ao selecionar arquivos
                inputArquivos.addEventListener("change", function() {
                  const arquivos = inputArquivos.files;

                  if (arquivos.length > 10) {
                    Swal.fire("Erro encontrado!", "Você só pode selecionar até 10 arquivos.", "error");
                    inputArquivos.value = ""; // limpa seleção
                    return;
                  }

                  for (let file of arquivos) {
                    if (file.size > 1 * 1024 * 1024 * 1024) {
                      Swal.fire("Erro encontrado!", `O arquivo ${file.name} excede o limite de 1GB.`, "error");
                      inputArquivos.value = ""; // limpa seleção
                      return;
                    }

                    if (!tiposPermitidos.includes(file.type)) {
                      Swal.fire("Erro encontrado!", `O tipo do arquivo ${file.name} não é permitido.`, "error");
                      inputArquivos.value = ""; // limpa seleção
                      return;
                    }
                  }
                });

                // Validação extra no envio do formulário (caso o usuário ignore ou manipule via devtools)
                form.addEventListener("submit", function(e) {
                  const arquivos = inputArquivos.files;

                  if (arquivos.length > 10) {
                    e.preventDefault();
                    Swal.fire("Erro", "Você só pode enviar até 10 arquivos.", "error");
                    return;
                  }

                  for (let file of arquivos) {
                    if (file.size > 1 * 1024 * 1024 * 1024 || !tiposPermitidos.includes(file.type)) {
                      e.preventDefault();
                      Swal.fire("Erro", `Há arquivos inválidos. Selecione novamente.`, "error");
                      return;
                    }
                  }
                });
              </script>

              <div class="mt-0 mb-2">

                <?php $sql = $conn->prepare("SELECT * FROM solicitacao_arq WHERE sarq_solic_id = :sarq_solic_id");
                $sql->execute(['sarq_solic_id' => $solic_id]);
                while ($arq = $sql->fetch(PDO::FETCH_ASSOC)) {
                  $sarq_id        = $arq['sarq_id'];
                  $sarq_solic_id  = $arq['sarq_solic_id'];
                  $sarq_categoria = $arq['sarq_categoria'];
                  $sarq_arquivo   = $arq['sarq_arquivo'];
                ?>

                  <div class="result_file">
                    <div class="result_file_name"><a href="uploads/solicitacoes/<?= $solic_codigo . '/' . $sarq_arquivo ?>" target="_blank"><?= $sarq_arquivo ?></a></div>

                    <?php
                    $sta_solic = array(3, 5, 6);
                    if (in_array($solic_sta_status, $sta_solic)) {
                    ?>
                      <span class="item_bt_row"></span>
                    <?php } else { ?>
                      <span class="item_bt_row">
                        <a href="controller/controller_propostas.php?funcao=exc_arq&ident=<?= $sarq_id ?>&p=3&c=<?= $solic_codigo ?>&f=<?= $sarq_arquivo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                      </span>
                    <?php } ?>

                  </div>

                <?php } ?>

              </div>
            </div>
          </div>

        </div>
        <div id="campo_info_pratic_titulo" style="display: none;">

          <div class="col-12">
            <div class="form_margem">
              <label class="form-label">Informe o título da(s) aula(s) <span>*</span></label>
              <label class="label_info label_info_verde">
                O título da aula deve ser informado da mesma maneira que foi cadastrado no banco de dados no espaço de prática.
                Obs.: Informar a quantidade de bancadas necessárias para a realização da aula. <br>
                Caso seja solicitado reserva para mais de uma aula:<br><br>
                • Cada título deve ter a data que a aula será realizada, conforme o exemplo abaixo:<br>
                03/09/2020 - Isolamento do campo Operatório;<br>
                10/09/2020 - Restauração de resina composta Classe II.
              </label>
              <textarea class="form-control" name="solic_ap_tit_aulas" id="solic_ap_tit_aulas" rows="5"><?= htmlspecialchars(str_replace('<br />', '', $solic_ap_tit_aulas)) ?></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>

        </div>
        <div id="campo_info_pratic_quant" style="display: none;">

          <div class="col-12">
            <div class="form_margem">
              <label class="form-label">Descreva os materiais, insumos e equipamentos, com suas respectivas quantidades, que serão necessários para a realização da aula no espaço de prática <span>*</span></label>
              <label class="label_info label_info_verde">
                Deve ser informada a quantidade com o respectivo nome do material, insumo ou equipamento.<br>
                Obs.: Caso seja realizada mais de uma aula, informar quais materiais devem ser disponibilizados para cada uma.<br>
                Por exemplo:<br>
                20/09/2022<br>
                05 macas;<br>
                01 Neurodyn portátil.<br><br>
                15/09/2022<br>
                08 colchonetes grandes para ginástica;<br>
                10 bolas suíças.
              </label>
              <textarea class="form-control" name="solic_ap_quant_material" id="solic_ap_quant_material" rows="5"><?= htmlspecialchars(str_replace('<br />', '', $solic_ap_quant_material)) ?></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>
        </div>

        <div id="campo_info_pratic_obs" style="display: none;">
          <div class="col-12">
            <div class="form_margem">
              <label class="form-label">Observações</label>
              <textarea class="form-control" name="solic_ap_obs" rows="5"><?= htmlspecialchars(str_replace('<br />', '', $solic_ap_obs)) ?></textarea>
            </div>
          </div>
        </div>


        <script>
          function atualizarCampos() {
            // Oculta todos os campos
            //document.getElementById("campo_info_pratic_anexar").style.display = "none";
            //document.getElementById("campo_info_pratic_titulo").style.display = "none";
            //document.getElementById("campo_info_pratic_quant").style.display = "none";

            // Obtém o radio selecionado
            let selecionado = document.querySelector('input[name="solic_ap_tipo_material"]:checked');

            // Se houver um radio selecionado, exibe o campo correspondente
            if (selecionado) {
              if (selecionado.value === "1") {
                document.getElementById("campo_info_pratic_anexar").style.display = "block";
                document.getElementById("cad_info_pratic_arquivo").required = true;
                document.getElementById("campo_info_pratic_titulo").style.display = "none";
                document.getElementById("solic_ap_tit_aulas").required = false;
                document.getElementById("campo_info_pratic_quant").style.display = "none";
                document.getElementById("solic_ap_quant_material").required = false;
                //
                document.getElementById("campo_info_pratic_obs").style.display = "block";
              } else if (selecionado.value === "2") {
                document.getElementById("campo_info_pratic_anexar").style.display = "none";
                document.getElementById("cad_info_pratic_arquivo").required = false;
                document.getElementById("campo_info_pratic_titulo").style.display = "block";
                document.getElementById("solic_ap_tit_aulas").required = true;
                document.getElementById("campo_info_pratic_quant").style.display = "none";
                document.getElementById("solic_ap_quant_material").required = false;
                document.getElementById("campo_info_pratic_obs").style.display = "block";
              } else if (selecionado.value === "3") {
                document.getElementById("campo_info_pratic_anexar").style.display = "none";
                document.getElementById("cad_info_pratic_arquivo").required = false;
                document.getElementById("campo_info_pratic_titulo").style.display = "none";
                document.getElementById("solic_ap_tit_aulas").required = false;
                document.getElementById("campo_info_pratic_quant").style.display = "block";
                document.getElementById("solic_ap_quant_material").required = true;
                //
                document.getElementById("campo_info_pratic_obs").style.display = "block";
              }
            }
          }

          // Executa ao mudar de opção
          document.querySelectorAll('input[name="solic_ap_tipo_material"]').forEach(radio => {
            radio.addEventListener('change', atualizarCampos);
          });

          // Executa ao carregar a página (para exibir o campo já marcado)
          atualizarCampos();
        </script>

      </div>
    </div>


    <div class="col-lg-12">
      <div class="hstack gap-3 align-items-center justify-content-between mt-4">
        <a type="buttom" onclick="location.href='nova_solicitacao.php?st=1&i=<?= $solic_id ?>'" class="btn botao btn-light waves-effect">Voltar</a>

        <?php
        $sta_solic = array(3, 5, 6);
        if (in_array($solic_sta_status, $sta_solic)) {
        ?><a class="btn botao botao_disabled waves-effect">Próximo</a>
        <?php } else { ?>
          <button type="submit" class="btn botao botao_verde waves-effect">Próximo</button>
        <?php } ?>
        <button type="submit" class="btn botao botao_verde waves-effect">Próximo</button>

      </div>
    </div>

  </form>
</div>



<script>
  document.addEventListener('DOMContentLoaded', function() {
    const radios = document.getElementsByName('solic_ap_aula_pratica');
    const campoInfoPraticCampus = document.getElementById('campo_info_pratic_campus');

    radios.forEach(radio => {
      radio.addEventListener('change', function() {
        if (this.value === '1') {
          campoInfoPraticCampus.style.display = 'block';
        } else {
          campoInfoPraticCampus.style.display = 'none';
        }
      });
    });
  });
</script>