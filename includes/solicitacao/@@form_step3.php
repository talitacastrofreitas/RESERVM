<div class="card-body p-sm-4 p-3">
  <form class="needs-validation meuFormulario form_solicitacao" method="POST" action="controller/controller_solicitacao.php" id="BotaoProgress" autocomplete="off" novalidate>
    <div class="row grid gx-3">

      <input type="hidden" class="form-control" name="solic_id" value="<?= $_GET['i'] ?>" required>
      <input type="hidden" class="form-control" name="solic_acao" value="atualizar_3" required>
      <input type="hidden" class="form-control" name="solic_etapa" value="3" required>

      <div class="col-12">
        <label class="form-label">Deseja realizar a solicitação de reserva de espaços para aulas teóricas? <span>*</span></label>
        <div class="label_info label_info_verde">Os espaços de ensino para atividades teóricas são: Salas de Aulas, Laboratórios de Informática e Auditórios.</div>

        <div class="check_container">
          <div class="form-check form_solicita">
            <input class="form-check-input form_solicita" type="radio" name="solic_at_aula_teorica" id="solic_at_aula_teorica_sim" value="1" <?= isset($solic_at_aula_teorica) && $solic_at_aula_teorica == 1 ? 'checked' : ''; ?> required>
            <label class="form-check-label" for="solic_at_aula_teorica_sim">Sim</label>
          </div>

          <div class="form-check form_solicita">
            <input class="form-check-input form_solicita" type="radio" name="solic_at_aula_teorica" id="solic_at_aula_teorica_nao" value="0" <?= isset($solic_at_aula_teorica) && $solic_at_aula_teorica == 0 ? 'checked' : ''; ?> required>
            <label class="form-check-label" for="solic_at_aula_teorica_nao">Não</label>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

      </div>

      <div id="campo_solic_at_campus" style="display: none;">
        <div class="col-sm-6">
          <div class="form_margem">
            <?php try {
              $sql = $conn->prepare("SELECT uni_id, uni_unidade FROM unidades ORDER BY uni_unidade ASC");
              $sql->execute();
              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              echo "Erro ao tentar recuperar os dados";
            } ?>
            <label class="form-label">Campus <span>*</span></label>
            <select class="form-select text-uppercase" name="solic_at_campus" id="solic_at_campus">
              <option selected value="<?= $campus_teorico_id  ?>"><?= $campus_teorico_nome ?></option>v
              <?php foreach ($result as $res) : ?>
                <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
          <script>
            var solic_at_aula_teorica_sim = document.getElementById("solic_at_aula_teorica_sim");
            var campo_solic_at_campus = document.getElementById("campo_solic_at_campus");

            solic_at_aula_teorica_sim.addEventListener("change", function() {
              if (solic_at_aula_teorica_sim.checked) {
                campo_solic_at_campus.style.display = "block";
                document.getElementById('solic_at_campus').required = true;
              } else {
                campo_solic_at_campus.style.display = "none";
                document.getElementById('solic_at_campus').required = false;
              }
            });
            if (solic_at_aula_teorica_sim.checked) {
              campo_solic_at_campus.style.display = "block"; // Mostrar o campo de entrada
              document.getElementById('solic_at_campus').required = true;
            }
          </script>
        </div>

        <div id="campo_info_pratic_espaco" style="display: none;">

          <div class="col-sm-6">
            <div class="form_margem">
              <?php try {
                $sql = $conn->prepare("SELECT cst_id, cst_sala FROM conf_sala_teorica ORDER BY cst_id ASC");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                echo "Erro ao tentar recuperar os dados";
              } ?>
              <label class="form-label">Quantidade de sala(s) / laboratório(s) de informática <span>*</span></label>
              <select class="form-select text-uppercase" name="solic_at_quant_sala" id="solic_at_quant_sala">
                <option selected value="<?= $cst_id  ?>"><?= $cst_sala ?></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['cst_id'] ?>"><?= $res['cst_sala'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>
          <script>
            const solic_at_campus = document.getElementById("solic_at_campus");
            const campo_info_pratic_espaco = document.getElementById("campo_info_pratic_espaco");

            solic_at_campus.addEventListener("change", function() {
              if (solic_at_campus.value) {
                campo_info_pratic_espaco.style.display = "block";
              } else {
                campo_info_pratic_espaco_cabula.style.display = "none";
              }
            });

            if (solic_at_campus.value) {
              campo_info_pratic_espaco.style.display = "block";
            }
          </script>
        </div>

        <div id="campo_solic_at_quant_particip" style="display: none;">

          <div class="col-sm-6">
            <div class="form_margem">
              <label class="form-label">Número estimado de participantes <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="solic_at_quant_particip" id="solic_at_quant_particip" value="<?= $solic_at_quant_particip ?>" maxlength="10">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Tipo da reserva <span>*</span></label>

            <div class="check_container">
              <div class="form-check form_solicita">
                <input type="radio" class="form-check-input form_solicita" id="solic_at_tipo_reserva1" name="solic_at_tipo_reserva" value="1" <?= isset($solic_at_tipo_reserva) && $solic_at_tipo_reserva == 1 ? 'checked' : ''; ?>>
                <label class="form-check-label" for="solic_at_tipo_reserva1">Esporádica - Reserva em data(s) específica(s).</label>
              </div>
              <div class="form-check form_solicita">
                <input type="radio" class="form-check-input form_solicita" id="solic_at_tipo_reserva2" name="solic_at_tipo_reserva" value="2" <?= isset($solic_at_tipo_reserva) && $solic_at_tipo_reserva == 2 ? 'checked' : ''; ?>>
                <label class="form-check-label" for="solic_at_tipo_reserva2">Fixa - Reserva permanente em determinado(s) dia(s) da semana, durante todo o semestre de acordo com o calendário acadêmico.</label>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
          </div>
          <script>
            const solic_at_quant_sala = document.getElementById("solic_at_quant_sala");
            const campo_solic_at_quant_particip = document.getElementById("campo_solic_at_quant_particip");

            solic_at_quant_sala.addEventListener("change", function() {
              if (solic_at_quant_sala.value) {
                campo_solic_at_quant_particip.style.display = "block";
              } else {
                campo_solic_at_quant_particip.style.display = "none";
              }
            });

            if (solic_at_quant_sala.value) {
              campo_solic_at_quant_particip.style.display = "block";
            }
          </script>
        </div>

        <div id="campo_info_teoric_data_reserva" style="display: none;">



          <div class="col-12 mb-4" id="campo_solic_at_dia_reserva" style="display: none;">
            <?php try {
              $dias = explode(", ", $solic_at_dia_reserva);
              $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
              $sql->execute();
              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar o perfil";
            } ?>
            <label class="form-label">Dia(s) da semana <span>*</span></label>
            <div class="label_info label_info_verde">Caso seu componente seja encerrado antes da última semana de finalização das aulas pelo calendário acadêmico, ou haja alguma exceção para alguma data do(s) dia(s) da semana selecionado, favor descrever no campo observação, para que a reserva não seja efetivada.</div>
            <select class="form-select text-uppercase" name="solic_at_dia_reserva[]" multiple id="cad_solic_at_dia_reserva">
              <!-- <option selected value=""></option> -->
              <?php foreach ($result as $res) : ?>
                <option value="<?= $res['week_id'] ?>" <?= in_array($res['week_id'], $dias) ? 'selected' : '' ?>><?= $res['week_dias'] ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
            <script>
              $(document).ready(function() {
                $('#cad_solic_at_dia_reserva').select2({
                  placeholder: "Selecione as opções",
                  tags: false,
                  allowClear: true,
                  // dropdownParent: $('#modal_cad_espaco'),
                  width: '100%'
                });
              });
            </script>
          </div>






          <!-- <div class="col-12" id="campo_solic_at_dia_reserva" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Dia(s) da semana <span>*</span></label>
              <div class="label_info label_info_verde">Caso seu componente seja encerrado antes da última semana de finalização das aulas pelo calendário acadêmico, ou haja alguma exceção para alguma data do(s) dia(s) da semana selecionado, favor descrever no campo observação, para que a reserva não seja efetivada.</div>

              <div class="check_container">
                <?php
                $dias = explode(", ", $solic_at_dia_reserva);
                $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                $sql->execute();
                while ($result = $sql->fetch(PDO::FETCH_ASSOC)) {
                  if (in_array($result['week_id'], $dias)) {
                    $solic_at_dia_reserva_checked = "checked";
                  } else {
                    $solic_at_dia_reserva_checked = "";
                  }
                ?>
                  <div class="form-check form_solicita">
                    <input class="form-check-input form_solicita me-2" type="checkbox" name="solic_at_dia_reserva[]" id="dias_semana<?= $result['week_id'] ?>" value="<?= $result['week_id'] ?>" <?= $solic_at_dia_reserva_checked = in_array($result['week_id'], $dias) ? "checked" : ""; ?>>
                    <label class="form-check-label" for="dias_semana<?= $result['week_id'] ?>"><?= $result['week_dias'] ?></label>
                  </div>
                <?php } ?>
                <div id="msgCheckInfoPraticDiasSemana" class="invalid-feedback">
                  Selecione pelo menos uma opção
                </div>

              </div>
            </div>
          </div> -->

          <div class="col-12" id="campo_solic_at_data_reserva" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Data(s) da reserva <span>*</span></label>
              <textarea class="form-control" name="solic_at_data_reserva" id="solic_at_data_reserva" rows="5"><?= htmlspecialchars(str_replace('<br />', '', $solic_at_data_reserva)) ?></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>
          <script>
            function mostraCamposReserva() {
              // Obtém o radio selecionado
              let solic_at_tipo_reserva = document.querySelector('input[name="solic_at_tipo_reserva"]:checked');

              // Se houver um radio selecionado, exibe o campo correspondente
              if (solic_at_tipo_reserva) {
                if (solic_at_tipo_reserva.value === "1") {
                  document.getElementById("campo_solic_at_data_reserva").style.display = "block";
                } else if (solic_at_tipo_reserva.value === "2") {
                  document.getElementById("campo_solic_at_dia_reserva").style.display = "block";
                }
              }
            }

            // Executa ao mudar de opção
            document.querySelectorAll('input[name="solic_at_tipo_reserva"]').forEach(radio => {
              radio.addEventListener('change', mostraCamposReserva);
            });

            // Executa ao carregar a página (para exibir o campo já marcado)
            mostraCamposReserva();
          </script>

          <div class="row">
            <div class="col-md-6 col-xl-4">
              <div class="form_margem">
                <label class="form-label">Horário inicial <span>*</span></label>
                <input type="time" class="form-control" name="solic_at_hora_inicio" id="solic_at_hora_inicio" value="<?= ($solic_at_hora_inicio) ? date("H:i", strtotime($solic_at_hora_inicio)) : ''; ?>">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-md-6 col-xl-4">
              <div class="form_margem">
                <label class="form-label">Horário final <span>*</span></label>
                <input type="time" class="form-control" name="solic_at_hora_fim" id="solic_at_hora_fim" value="<?= ($solic_at_hora_fim) ? date("H:i", strtotime($solic_at_hora_fim)) : ''; ?>">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <script>
              document.addEventListener("DOMContentLoaded", () => {
                const radios = document.querySelectorAll('input[name="solic_at_tipo_reserva"]');
                const campoInfo = document.getElementById("campo_info_teoric_data_reserva");

                // Função para exibir o campo se um rádio for selecionado
                function verificarSelecao() {
                  const selecionado = document.querySelector('input[name="solic_at_tipo_reserva"]:checked');
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

            <div class="col-12">
              <div class="form_margem">
                <label class="form-label">Recursos audiovisuais adicionais</label>
                <label class="label_info label_info_verde">Todas as salas de aulas já possuem computador, projetor/ TV e caixa de som. Se necessário, incluir apenas informação para kit transmissão e microfone.</label>
                <textarea class="form-control" name="solic_at_recursos" rows="5"><?= htmlspecialchars(str_replace('<br />', '', $solic_at_recursos)) ?></textarea>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div class="form_margem">
                <label class="form-label">Observações</label>
                <textarea class="form-control" name="solic_at_obs" rows="5"><?= htmlspecialchars(str_replace('<br />', '', $solic_at_obs)) ?></textarea>
              </div>
            </div>

          </div>

        </div>

      </div>
    </div>


    <div class="col-lg-12">
      <div class="hstack gap-3 align-items-center justify-content-between mt-4">
        <a type="buttom" onclick="location.href='nova_solicitacao.php?st=2&i=<?= $solic_id ?>'" class="btn botao btn-light waves-effect">Voltar</a>



        <?php
        $sta_solic = array(3, 5, 6);
        if (in_array($solic_sta_status, $sta_solic)) {
        ?><a class="btn botao botao_disabled waves-effect">Concluir</a>
        <?php } else { ?>
          <button type="submit" class="btn botao botao_verde waves-effect">Concluir</button>
        <?php } ?>
        <button type="submit" class="btn botao botao_verde waves-effect">Concluir</button>

      </div>
    </div>

  </form>
</div>

<script>
  /////////////////////////////////////////////
  // INFORMAÇÕES DA RESERVA - AULAS PRÁTICAS //
  /////////////////////////////////////////////

  // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - SOLICITA RESERVA PARA AULAS PRÁTICAS
  document.addEventListener("DOMContentLoaded", function() {
    // const form = document.getElementById("meuFormulario");
    const form = document.querySelector(".meuFormulario");
    const radios_solic_at_aula_teorica = document.querySelectorAll('input[name="solic_at_aula_teorica"]');
    const campo_solic_at_campus = document.getElementById("campo_solic_at_campus");
    const solic_at_campus = document.getElementById("solic_at_campus");
    const solic_at_quant_sala = document.getElementById("solic_at_quant_sala");
    const campo_info_pratic_espaco = document.getElementById("campo_info_pratic_espaco");

    let solicitacaoSimSelecionada = false; // Variável para armazenar o estado do radio "Sim"

    // Evento para mostrar/esconder os campos ao mudar o radio
    radios_solic_at_aula_teorica.forEach(radio => {
      radio.addEventListener("change", function() {
        solicitacaoSimSelecionada = this.value === "1"; // Atualiza o estado baseado no radio selecionado

        if (solicitacaoSimSelecionada) {
          campo_solic_at_campus.style.display = "block"; // Exibe campo campus
          solic_at_campus.required = true;
        } else {
          campo_solic_at_campus.style.display = "none"; // Oculta campo campus
          solic_at_campus.required = false;
          campo_info_pratic_espaco.style.display = "none"; // Oculta todos os checkboxes

          // 🔹 Desativar todas as obrigações dos campos relacionados
          const camposObrigatorios = [
            solic_at_campus,
            solic_at_quant_sala,
            solic_at_quant_particip,
            solic_at_data_reserva,
            solic_at_hora_inicio,
            solic_at_hora_fim
          ];

          camposObrigatorios.forEach(campo => {
            campo.required = false;
            campo.value = ""; // Opcional: limpar os valores dos campos
          });

          // 🔹 Ocultar campos dependentes
          campo_info_pratic_espaco.style.display = "none";
          campo_solic_at_quant_particip.style.display = "none";
          campo_info_teoric_data_reserva.style.display = "none";
          campo_solic_at_dia_reserva.style.display = "none";

          // **Desmarcar checkboxes e rádios**
          document.querySelectorAll('input[type="checkbox"], input[name="solic_at_tipo_reserva"]').forEach(input => {
            input.checked = false;
          });

          // **Remover classes de erro**
          document.querySelectorAll('.is-invalid').forEach(element => {
            element.classList.remove('is-invalid');
          });


        }
      });
    });

    // Evento para exibir os checkboxes corretos ao selecionar o campus
    solic_at_campus.addEventListener("change", function() {
      if (!solicitacaoSimSelecionada) return; // Se "Sim" não estiver selecionado, ignora

      if (solic_at_campus.value === "2") {
        campo_info_pratic_espaco.style.display = "block";
      } else {
        campo_info_pratic_espaco.style.display = "block";
      }
      solic_at_quant_sala.required = true;
    });

    // Evento para validar os checkboxes apenas se "Sim" estiver marcado
    form.addEventListener("submit", function(event) {
      let valido = true;

      if (solicitacaoSimSelecionada) {
        if (solic_at_campus.value === "2") {
          if (!validarCheckboxesPorNome("cad_info_pratic_espaco_brotas", "msgCheckInfoPraticEspacoBrotas")) {
            valido = false;
          }
        } else {
          if (!validarCheckboxesPorNome("cad_info_pratic_espaco_cabula", "msgCheckInfoPraticEspacoCabula")) {
            valido = false;
          }
        }
      }

      if (!valido) {
        event.preventDefault();
        event.stopPropagation();
      }
    });

    // Evento para validar os checkboxes ao marcar/desmarcar
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener("change", function() {
        if (!solicitacaoSimSelecionada) return; // Só valida se "Sim" estiver marcado

        if (solic_at_campus.value === "2") {
          validarCheckboxesPorNome("cad_info_pratic_espaco_brotas", "msgCheckInfoPraticEspacoBrotas");
        } else {
          validarCheckboxesPorNome("cad_info_pratic_espaco_cabula", "msgCheckInfoPraticEspacoCabula");
        }
      });
    });




    // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - NÚMERO DE PARTICIPANTES / TIPO RESERVA
    document.getElementById('solic_at_quant_sala').addEventListener('change', function() {
      const campo_solic_at_quant_particip = document.getElementById('campo_solic_at_quant_particip');

      // NÚMERO DE PARTICIPANTES
      if (this.value) {
        campo_solic_at_quant_particip.style.display = 'block';
        solic_at_quant_particip.required = true;
      } else {
        campo_solic_at_quant_particip.style.display = 'none';
        solic_at_quant_particip.required = false;
      }
    });

    // TIPO RESERVA
    form.addEventListener('submit', function(event) {
      const campo_solic_at_quant_particip = document.getElementById('campo_solic_at_quant_particip');
      const radios_info_pratic_tipo_reserva = document.querySelectorAll('input[name="solic_at_tipo_reserva"]');
      let radioSelecionado = false;

      radios_info_pratic_tipo_reserva.forEach(radio => {
        if (radio.checked) {
          radioSelecionado = true;
        }
      });

      if (campo_solic_at_quant_particip.style.display === 'block' && !radioSelecionado) {
        event.preventDefault();
        campo_solic_at_quant_particip.classList.add('was-validated');
        radios_info_pratic_tipo_reserva.forEach(radio => {
          radio.classList.add('is-invalid');
        });
      }
    });

    // Adiciona um evento para remover a validação quando um radio é selecionado
    document.querySelectorAll('input[name="solic_at_tipo_reserva"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const campo_solic_at_quant_particip = document.getElementById('campo_solic_at_quant_particip');
        const radios = document.querySelectorAll('input[name="solic_at_tipo_reserva"]');

        // Remove a classe is-invalid de todos os radios
        radios.forEach(radio => {
          radio.classList.remove('is-invalid');
        });

        // Remove a classe was-validated do campo oculto
        campo_solic_at_quant_particip.classList.remove('was-validated');
      });
    });



    // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - TIPO DE RESERVA
    const radio = document.querySelectorAll('input[name="solic_at_tipo_reserva"]');
    const campo_info_teoric_data_reserva = document.getElementById("campo_info_teoric_data_reserva");
    const checkboxes = document.querySelectorAll("input[name='solic_at_dia_reserva[]']");
    const feedback = document.getElementById("msgCheckInfoPraticDiasSemana");

    radio.forEach(radio => {
      radio.addEventListener("change", function() {
        if (this.value === "1") {
          campo_info_teoric_data_reserva.style.display = "block";
          solic_at_data_reserva.required = true;

          campo_solic_at_dia_reserva.style.display = "none";
          solic_at_hora_inicio.required = true;

          campo_solic_at_data_reserva.style.display = "block";
          solic_at_hora_fim.required = true;

        } else {
          campo_info_teoric_data_reserva.style.display = "block";
          solic_at_data_reserva.required = false;

          campo_solic_at_dia_reserva.style.display = "block";
          campo_solic_at_data_reserva.style.display = "none";

          solic_at_hora_inicio.required = true;
          solic_at_hora_fim.required = true;
        }
      });
    });


    const opcao1 = document.getElementById("solic_at_tipo_reserva1");
    const opcao2 = document.getElementById("solic_at_tipo_reserva2");
    const textareaContainer = document.getElementById("campo_solic_at_data_reserva");
    const checkboxContainer = document.getElementById("campo_solic_at_dia_reserva");
    const textarea = document.getElementById("solic_at_data_reserva");
    const checkboxes_info_pratic_dias_semana = document.querySelectorAll("input[name='solic_at_dia_reserva[]']");
    const checkboxFeedback = document.getElementById("msgCheckInfoPraticDiasSemana");

    function validarCheckboxes() {
      let algumMarcado = Array.from(checkboxes_info_pratic_dias_semana).some(checkbox => checkbox.checked);
      checkboxFeedback.style.display = algumMarcado ? "none" : "block";
      return algumMarcado;
    }

    opcao1.addEventListener("change", function() {
      if (this.checked) {
        textareaContainer.style.display = "block";
        checkboxContainer.style.display = "none";
        textarea.classList.remove("is-invalid");
      }
    });

    opcao2.addEventListener("change", function() {
      if (this.checked) {
        checkboxContainer.style.display = "block";
        textareaContainer.style.display = "none";
        checkboxFeedback.style.display = "none";
      }
    });

    textarea.addEventListener("input", function() {
      if (this.value.trim() !== "") {
        this.classList.remove("is-invalid");
      }
    });

    checkboxes_info_pratic_dias_semana.forEach(checkbox => {
      checkbox.addEventListener("change", validarCheckboxes);
    });

    form.addEventListener("submit", function(event) {
      let valido = true;

      if (opcao1.checked && textarea.value.trim() === "") {
        textarea.classList.add("is-invalid");
        valido = false;
      }

      if (opcao2.checked && !validarCheckboxes()) {
        valido = false;
      }

      if (!valido) {
        event.preventDefault();
        event.stopPropagation();
      }
    });

  });
</script>