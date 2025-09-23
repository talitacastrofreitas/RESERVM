<div class="card-body p-sm-4 p-3">
  <form class="needs-validation meuFormulario form_solicitacao" method="POST" action="router/web.php?r=Solic" id="ValidaBotaoProgress" autocomplete="off" novalidate>
    <div class="row grid gx-3">

      <input type="hidden" class="form-control" name="solic_id" value="<?= $_GET['i'] ?>" required>
      <input type="hidden" class="form-control" name="solic_acao" value="atualizar_3" required>
      <input type="hidden" class="form-control" name="solic_etapa" value="3" required>
      <input type="hidden" name="solic_ap_aula_pratica_hidden" value="<?= $_SESSION['solic_ap_aula_pratica_choice'] ?? '' ?>">

      <div class="col-12">
        <label class="form-label">Deseja realizar a solicitação de reserva de espaços para aulas teóricas? <span>*</span></label>
        <div class="label_info label_info_verde">Os espaços de ensino para atividades teóricas são: Salas de Aulas, Laboratórios de Informática e Auditórios.</div>

        <div id="atividades_validation_message" class="label_info label_info_vermelho" style="display: none;">
          Você ainda não cadastrou nenhum tipo de aula! Cadastre pelo menos uma aula prática e/ou uma aula teórica para continuar.
        </div>

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
            <select class="form-select text-uppercase" name="solic_at_campus" id="solic_at_campus">
              <option selected value="<?= $campus_teorico_id  ?>"><?= $campus_teorico_nome ?></option>v
              <?php foreach ($result as $res) : ?>
                <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div id="campo_info_pratic_espaco" style="display: none;">

          <div class="col-12">
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
        </div>

        <div id="campo_solic_at_quant_particip" style="display: none;">

          <div class="col-12">
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

          <div class="col-12" id="campo_solic_at_data_reserva" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Data(s) da reserva <span>*</span></label>
              <textarea class="form-control" name="solic_at_data_reserva" id="solic_at_data_reserva" rows="5"><?= htmlspecialchars(str_replace('<br />', '', $solic_at_data_reserva)) ?></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form_margem">
                <label class="form-label">Horário inicial <span>*</span></label>
                <input type="time" class="form-control hora" name="solic_at_hora_inicio" id="solic_at_hora_inicio" value="<?= ($solic_at_hora_inicio) ? date("H:i", strtotime($solic_at_hora_inicio)) : ''; ?>">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
              <script>
                flatpickr("#solic_at_hora_inicio", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
            </div>

            <div class="col-md-6">
              <div class="form_margem">
                <label class="form-label">Horário final <span>*</span></label>
                <input type="time" class="form-control hora" name="solic_at_hora_fim" id="solic_at_hora_fim" value="<?= ($solic_at_hora_fim) ? date("H:i", strtotime($solic_at_hora_fim)) : ''; ?>">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
              <script>
                flatpickr("#solic_at_hora_fim", {
                  enableTime: true, // ativa o seletor de hora
                  noCalendar: true, // oculta o calendário
                  dateFormat: "H:i", // formato 24h: horas:minutos
                  time_24hr: true, // garante o formato 24h
                  allowInput: true // permite apagar e digitar manualmente
                });
              </script>
            </div>

            <script>
              document.addEventListener('DOMContentLoaded', function() {
                const horaInicio = document.getElementById('solic_at_hora_inicio');
                const horaFim = document.getElementById('solic_at_hora_fim');

                function validarHoras() {
                  const inicio = horaInicio.value;
                  const fim = horaFim.value;

                  // Só valida se ambos os campos estiverem preenchidos
                  if (inicio && fim) {
                    if (inicio >= fim) {
                      Swal.fire({
                        icon: 'warning',
                        title: 'Horário inválido',
                        text: 'A hora de início deve ser menor que a hora de fim.',
                      }).then(() => {
                        horaInicio.value = '';
                        horaFim.value = '';
                        horaInicio.focus();
                      });
                    }
                  }
                }

                // Você pode ajustar o tipo de evento conforme preferir
                horaInicio.addEventListener('change', validarHoras);
                horaFim.addEventListener('change', validarHoras);
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
        <a type="buttom" onclick="location.href='nova_solicitacao.php?st=2&i=<?= $solic_id ?>'" class="btn btn-light btn-label previestab waves-effect"><i class="ri-arrow-left-line label-icon align-middle fs-16 me-2"></i> Voltar</a>

        <?php
        $sta_solic = array(3, 5, 6);
        if (in_array($solic_sta_status, $sta_solic)) {
        ?><a class="btn botao botao_disabled waves-effect">Concluir</a>
        <?php } else { ?>
          <button type="submit" class="btn botao botao_verde waves-effect">Concluir</button>
        <?php } ?>

      </div>
    </div>

  </form>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {

    const cad_at_aula_teorica = document.querySelectorAll('input[name="solic_at_aula_teorica"]');
    const campo_solic_at_campus = document.getElementById('campo_solic_at_campus');

    const solic_at_campus = document.getElementById('solic_at_campus');
    const campo_info_pratic_espaco = document.getElementById('campo_info_pratic_espaco');

    const solic_at_quant_sala = document.getElementById('solic_at_quant_sala');
    const solic_at_quant_particip = document.getElementById('solic_at_quant_particip');
    const campo_solic_at_quant_particip = document.getElementById('campo_solic_at_quant_particip');
    const solic_at_tipo_reserva = document.querySelectorAll('input[name="solic_at_tipo_reserva"]');

    const campo_info_teoric_data_reserva = document.getElementById('campo_info_teoric_data_reserva');
    const campo_solic_at_data_reserva = document.getElementById('campo_solic_at_data_reserva');
    const campo_solic_at_dia_reserva = document.getElementById('campo_solic_at_dia_reserva');

    const solic_at_hora_inicio = document.getElementById('solic_at_hora_inicio');
    const solic_at_hora_fim = document.getElementById('solic_at_hora_fim');
    const solic_at_data_reserva = document.getElementById('solic_at_data_reserva');
    const cad_solic_at_dia_reserva = document.getElementById('cad_solic_at_dia_reserva');



    // Função para resetar e esconder todos os campos relacionados à aula prática
    function resetarCamposPratica() {
      campo_solic_at_campus.style.display = 'none';
      campo_info_pratic_espaco.style.display = 'none';
      campo_solic_at_quant_particip.style.display = 'none';
      campo_info_teoric_data_reserva.style.display = 'none';
      campo_solic_at_data_reserva.style.display = 'none';
      campo_solic_at_dia_reserva.style.display = 'none';

      // Remover obrigatoriedades
      document.getElementById('solic_at_campus').required = false;
      solic_at_quant_sala.required = false;
      solic_at_quant_particip.required = false;
      solic_at_tipo_reserva.forEach(r => r.required = false);
      solic_at_hora_inicio.required = false;
      solic_at_hora_fim.required = false;
      solic_at_data_reserva.required = false;
      cad_solic_at_dia_reserva.required = false;
    }

    // Exibe ou oculta campos com base em "aula teóricas"
    cad_at_aula_teorica.forEach(radio => {
      radio.addEventListener('change', () => {
        if (radio.value === '1' && radio.checked) {
          campo_solic_at_campus.style.display = 'block';
          document.getElementById('solic_at_campus').required = true;
        } else if (radio.value === '0' && radio.checked) {
          resetarCamposPratica();
        }
      });
    });

    // Campus selecionado
    solic_at_campus.addEventListener('change', () => {
      if (solic_at_campus.value !== "") {
        campo_info_pratic_espaco.style.display = 'block';
        solic_at_quant_sala.required = true;
      } else {
        campo_info_pratic_espaco.style.display = 'none';
        solic_at_quant_sala.required = true;
      }
    });

    // Quantidade de turmas
    solic_at_quant_sala.addEventListener('change', () => {
      if (solic_at_quant_sala.value !== "") {
        campo_solic_at_quant_particip.style.display = 'block';
        solic_at_quant_particip.required = true;
        solic_at_tipo_reserva.forEach(r => r.required = true);
      } else {
        campo_solic_at_quant_particip.style.display = 'none';
        solic_at_quant_particip.required = false;
        solic_at_tipo_reserva.forEach(r => r.required = false);
      }
    });

    // Tipo de reserva (data x dias da semana)
    solic_at_tipo_reserva.forEach(radio => {
      radio.addEventListener('change', () => {
        campo_info_teoric_data_reserva.style.display = 'block';
        solic_at_hora_inicio.required = true;
        solic_at_hora_fim.required = true;

        if (radio.value === "1") {
          campo_solic_at_data_reserva.style.display = 'block';
          campo_solic_at_dia_reserva.style.display = 'none';
          solic_at_data_reserva.required = true;
          cad_solic_at_dia_reserva.required = false;
        } else if (radio.value === "2") {
          campo_solic_at_data_reserva.style.display = 'none';
          campo_solic_at_dia_reserva.style.display = 'block';
          solic_at_data_reserva.required = false;
          cad_solic_at_dia_reserva.required = true;
        }
      });
    });


    // VERIFICAÇÃO (NOVO BLOCO)

    const solic_at_aula_teorica_sim = document.getElementById('solic_at_aula_teorica_sim');
    const solic_at_aula_teorica_nao = document.getElementById('solic_at_aula_teorica_nao');
    const formStep3 = document.getElementById('ValidaBotaoProgress'); // ID do seu formulário
    const solic_ap_aula_pratica_hidden = document.querySelector('input[name="solic_ap_aula_pratica_hidden"]');
    const atividadesValidationMessage = document.getElementById('atividades_validation_message'); // Referência à div de mensagem
    const concluirButton = formStep3.querySelector('button[type="submit"]'); // NOVO: Referência ao botão "Concluir"

    // Função para validar, mostrar/ocultar a mensagem E habilitar/desabilitar o botão
    function validateActivitySelection() {
      if (!solic_ap_aula_pratica_hidden || !solic_at_aula_teorica_nao || !concluirButton) {
        return; // Garante que os elementos existam
      }

      const practicalSelectedNo = solic_ap_aula_pratica_hidden.value === '0'; // '0' para Não
      const theoreticalSelectedNo = solic_at_aula_teorica_nao.checked; // Se o rádio 'Não' para teóricas está marcado

      if (practicalSelectedNo && theoreticalSelectedNo) {
        atividadesValidationMessage.style.display = 'block'; // Mostra a mensagem
        concluirButton.disabled = true; // NOVO: Desabilita o botão
      } else {
        atividadesValidationMessage.style.display = 'none'; // Oculta a mensagem
        concluirButton.disabled = false; // NOVO: Habilita o botão
      }
    }

    // Chamada da validação ao carregar a página (caso o formulário já venha preenchido)
    validateActivitySelection();

    // Chamada da validação quando os rádios de Aulas Teóricas mudam
    solic_at_aula_teorica_sim.addEventListener('change', validateActivitySelection);
    solic_at_aula_teorica_nao.addEventListener('change', validateActivitySelection);

    if (formStep3) {
      formStep3.addEventListener('submit', function(event) {
        validateActivitySelection(); // Valida novamente no submit para garantir

        const practicalSelectedNo = solic_ap_aula_pratica_hidden.value === '0';
        const theoreticalSelectedNo = solic_at_aula_teorica_nao.checked;

        if (practicalSelectedNo && theoreticalSelectedNo) {
          event.preventDefault(); // Impede o envio se a validação falhar
          // A mensagem já será exibida/atualizada e o botão desabilitado pela função validateActivitySelection
        }
      });
    }

    // Chamada da validação ao carregar a página (caso o formulário já venha preenchido)
    // Isso é importante se o usuário voltar para esta etapa.
    validateActivitySelection();

    // Chamada da validação quando os rádios de Aulas Teóricas mudam
    solic_at_aula_teorica_sim.addEventListener('change', validateActivitySelection);
    solic_at_aula_teorica_nao.addEventListener('change', validateActivitySelection);

    if (formStep3) {
      formStep3.addEventListener('submit', function(event) {
        validateActivitySelection(); // Valida novamente no submit para garantir

        const practicalSelectedNo = solic_ap_aula_pratica_hidden.value === '0';
        const theoreticalSelectedNo = solic_at_aula_teorica_nao.checked;

        if (practicalSelectedNo && theoreticalSelectedNo) {
          event.preventDefault(); // Impede o envio se a validação falhar
          // A mensagem já será exibida/atualizada pela função validateActivitySelection
        }
      });
    }

    // Chamada da validação ao carregar a página (caso o formulário já venha preenchido)
    validateActivitySelection();

    // Chamada da validação quando os rádios de Aulas Teóricas mudam
    solic_at_aula_teorica_sim.addEventListener('change', validateActivitySelection);
    solic_at_aula_teorica_nao.addEventListener('change', validateActivitySelection);

    if (formStep3) {
      formStep3.addEventListener('submit', function(event) {
        validateActivitySelection(); // Valida novamente no submit para garantir

        const practicalSelectedNo = solic_ap_aula_pratica_hidden.value === '0';
        const theoreticalSelectedNo = solic_at_aula_teorica_nao.checked;

        if (practicalSelectedNo && theoreticalSelectedNo) {
          event.preventDefault(); // Impede o envio se a validação falhar
          // A mensagem já será exibida pela função validateActivitySelection
        }
      });
    }

    inicializarFormularioPreenchido(); // Mantenha esta linha para inicializar os campos

  });
</script>