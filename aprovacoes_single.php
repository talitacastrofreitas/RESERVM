<?php include 'includes/header.php'; ?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

<?php
$solic_id = $_GET['i'];
$sql = 'SELECT *, campus_pratico.uni_id AS campus_pratico_id, campus_pratico.uni_unidade AS campus_pratico_nome, campus_teorico.uni_id AS campus_teorico_id, campus_teorico.uni_unidade AS campus_teorico_nome
          FROM solicitacao
          LEFT JOIN solicitacao_status ON solicitacao_status.solic_sta_solic_id = solicitacao.solic_id
          LEFT JOIN status_solicitacao ON status_solicitacao.stsolic_id = solicitacao_status.solic_sta_status
          LEFT JOIN componente_curricular ON componente_curricular.compc_id = solicitacao.solic_comp_curric
          LEFT JOIN cursos ON cursos.curs_id = solicitacao.solic_curso
          LEFT JOIN conf_cursos_extensao_curricularizada ON conf_cursos_extensao_curricularizada.cexc_id = solicitacao.solic_nome_curso
          LEFT JOIN conf_semestre ON conf_semestre.cs_id = solicitacao.solic_semestre
          LEFT JOIN conf_turma_pratica ON conf_turma_pratica.ctp_id = solicitacao.solic_ap_quant_turma
          LEFT JOIN conf_sala_teorica ON conf_sala_teorica.cst_id = solicitacao.solic_at_quant_sala
          LEFT JOIN conf_tipo_reserva ON conf_tipo_reserva.ctr_id = solicitacao.solic_ap_tipo_reserva OR conf_tipo_reserva.ctr_id = solicitacao.solic_at_tipo_reserva
          LEFT JOIN unidades AS campus_pratico ON campus_pratico.uni_id = solicitacao.solic_ap_campus
          LEFT JOIN unidades AS campus_teorico ON campus_teorico.uni_id = solicitacao.solic_at_campus
          INNER JOIN usuarios ON usuarios.user_id = solicitacao.solic_cad_por
          WHERE solic_id = :solic_id';
$stmt = $conn->prepare($sql);
$stmt->execute(['solic_id' => $solic_id]);
$solic_row = $stmt->fetch();
if ($solic_row && $solic_row['solic_sta_status'] > 1) {
  extract($solic_row);

  // CONFIGURAÇÃO DO STATUS
  $status_colors = [
    1 => 'tag_header_laranja',
    2 => 'tag_header_azul',
    3 => 'tag_header_roxo',
    4 => 'tag_header_verde',
    5 => 'tag_header_azul',
    6 => 'tag_header_vermelho'
  ];

  $tag_header_color = $status_colors[$solic_sta_status] ?? ''; // Usa '' como padrão se não existir

} else {
  header("Location: aprovacoes.php");
  exit();
}
?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg profile-analise-bg"></div>
</div>

<div class="pt-4 mb-4 mb-lg-3 pb-lg-3 profile-wrapper header_perfil">
  <div class="row g-4 justify-content-center">

    <div class="col-lg-8 col-xxl-6">
      <div>
        <div class="d-flex justify-content-sm-between justify-content-start align-items-start flex-sm-row flex-column">
          <h3 class="text-uppercase mb-1 pe-4" style="color: var(--amarelo);">
            <div class="d-inline"><strong class="me-2"><?= htmlspecialchars($solic_row['solic_codigo']) ?>:
              </strong><?= htmlspecialchars($solic_row['compc_componente']) . htmlspecialchars($solic_row['solic_nome_atividade']) . htmlspecialchars($solic_row['solic_nome_comp_ativ']) ?>
          </h3>
          <div class="botao <?= $tag_header_color ?> mt-md-0 mt-3 text-nowrap"><?= htmlspecialchars($stsolic_status) ?>
          </div>
        </div>
        <div class="row g-3 mt-3">

          <div class="col-xl col-md-6">
            <span>Solicitante</span>
            <p><?= htmlspecialchars($solic_row['user_nome']) ?></p>
          </div>

          <div class="col-xl col-md-6">
            <span>E-mail</span>
            <p><?= htmlspecialchars($solic_row['user_email']) ?></p>
          </div>

          <div class="col-xl col-md-6">
            <span>Data Solicitação</span>
            <p><?= htmlspecialchars(date('d/m/Y H:i', strtotime($solic_row['solic_data_cad']))) ?></p>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<div class="row justify-content-md-center">
  <div class="col-lg-8 col-xxl-6">

    <div class="card">

      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-sm-6 tit_nova_solicitacao text-sm-start text-center">
            <h3 class="text-uppercase m-0 fs-16">Identificação</h3>
          </div>
          <div class="col-sm-6">
            <nav
              class="navbar d-flex align-items-center justify-content-sm-end justify-content-center p-0 mt-3 mt-sm-0">

              <?php
              $sta_solic = array(1, 2, 4, 5, 6, 7, 8);
              if (!in_array($solic_sta_status, $sta_solic)) {
                ?>
                <button class="btn botao_w botao botao_vermelho waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3" type="button"
                  data-bs-toggle="modal" data-bs-toggle="button"
                  data-bs-target="#modal_indeferir_solicitacao">Indeferir</button>

                <!-- INDEFERIR -->
                <?php include 'includes/modal/modal_indeferir_solicitacao.php'; ?>

              <?php } else { ?>
                <a class="btn botao_w botao botao_disabled waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3">Indeferir</a>
              <?php } ?>

              <?php
              $sta_solic = array(1, 2, 4, 5, 6, 7, 8);
              if (!in_array($solic_sta_status, $sta_solic)) {
                ?>
                <button class="btn botao_w botao botao_verde waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3" type="button"
                  data-bs-toggle="modal" data-bs-toggle="button"
                  data-bs-target="#modal_deferir_solicitacao">Aprovar</button>

                <!-- DEFERIR -->
                <?php include 'includes/modal/modal_deferir_solicitacao.php'; ?>

              <?php } else { ?>
                <button class="btn botao_w botao botao_disabled waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3"
                  type="button">Aprovar</button>
              <?php } ?>


              <form method="POST" action="router/web.php?r=AprovaAnalise" style="display:inline;"
                onsubmit="return confirmarAcao('iniciar_analise', 'Deseja iniciar a análise desta solicitação?', this);">
                <input type="hidden" name="acao" value="iniciar_analise">
                <input type="hidden" name="solic_id" value="<?= htmlspecialchars($solic_id) ?>">
                <input type="hidden" name="sta_an_solic_codigo" value="<?= htmlspecialchars($solic_codigo) ?>">

                <?php
                $sta_solic = array(1, 3, 4, 5, 6, 7, 8);
                if (!in_array($solic_sta_status, $sta_solic)) {
                  ?>
                  <button class="btn botao_w botao botao_azul_escuro waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3"
                    type="submit">
                    Iniciar Análise
                  </button>

                <?php } else { ?>
                  <button class="btn botao_w botao botao_disabled waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3" type="button">
                    Iniciar Análise
                  </button>
                <?php } ?>
              </form>

              <script>
                // Função para confirmar a ação (você precisa garantir que esta função SweetAlert existe no seu header/footer)
                function confirmarAcao(acao, mensagem, form) {
                  Swal.fire({
                    text: mensagem,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0461AD',
                    cancelButtonColor: '#C4453E',
                    confirmButtonText: 'Sim, continuar',
                    cancelButtonText: 'Cancelar',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      form.submit();
                    }
                  });
                  return false; // Previne o envio padrão do formulário
                }
              </script>

            </nav>
          </div>
        </div>
      </div>


      <div class="card-body p-sm-4 p-3 form_solicitacao">

        <div class="row grid gx-3">

          <div class="col-12">
            <div class="form_margem">
              <?php try {
                $sql = $conn->prepare("SELECT curs_id, curs_curso FROM cursos WHERE curs_status != 0 ORDER BY curs_curso");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                echo "Erro ao tentar recuperar os dados";
              } ?>
              <label class="form-label">Curso <span>*</span></label>
              <select class="form-select text-uppercase" id="cad_solic_curso" disabled>
                <option selected value="<?= htmlspecialchars($solic_curso) ?>"><?= htmlspecialchars($curs_curso) ?>
                </option>
              </select>
            </div>
            <script>
              const cad_solic_tipo_ativ = document.getElementById("cad_solic_tipo_ativ");
              const camp_solic_curso = document.getElementById("camp_solic_curso");

              cad_solic_tipo_ativ.addEventListener("change", function () {
                if (cad_solic_tipo_ativ.value === "1") {
                  camp_solic_curso.style.display = "block";
                  document.getElementById("cad_solic_curso").required = true;
                } else {
                  camp_solic_curso.style.display = "none";
                  document.getElementById("cad_solic_curso").required = false;
                }
              });

              if (cad_solic_tipo_ativ.value === "1") {
                camp_solic_curso.style.display = "block";
                document.getElementById("cad_solic_curso").required = true;
              }
            </script>
          </div>

          <div class="col-12" id="camp_solic_comp_curric" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Componente Curricular <span>*</span></label>
              <select class="form-select text-uppercase" id="cad_solic_comp_curric"
                data-valor="<?= htmlspecialchars($compc_id) ?>" disabled>
                <option value="<?= htmlspecialchars($compc_id) ?>"><?= htmlspecialchars($compc_componente) ?></option>
              </select>
            </div>
            <script>
              $(document).ready(function () {
                // Inicializa o select2
                $('#cad_solic_curso').select2();

                // Função para verificar e exibir o campo oculto se necessário
                function verificarSelecao() {
                  if ($('#cad_solic_curso').val() == "2") { // Ajuste para o valor que deve exibir o campo
                    $('#camp_solic_comp_curric').show();
                  } else {
                    $('#camp_oculto').hide();
                  }
                }

                // Verifica na inicialização
                verificarSelecao();

                // Adiciona o evento de mudança
                $('#cad_solic_curso').on('change', function () {
                  verificarSelecao();
                });
              });
            </script>
          </div>

          <div class="col-12" id="camp_solic_nome_curso" style="display: none;">
            <div class="form_margem">
              <?php try {
                $sql = $conn->prepare("SELECT cexc_id, cexc_curso FROM conf_cursos_extensao_curricularizada ORDER BY cexc_curso");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                echo "Erro ao tentar recuperar os dados";
              } ?>
              <label class="form-label">Nome do Curso <span>*</span></label>
              <select class="form-select text-uppercase" id="cad_solic_nome_curso" disabled>
                <option selected value="<?= htmlspecialchars($cexc_id) ?>"><?= htmlspecialchars($cexc_curso) ?>
                </option>
              </select>
            </div>
          </div>

          <div class="col-12" id="camp_solic_nome_curso_text" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Nome do Curso <span>*</span></label>
              <input type="text" class="form-control text-uppercase" id="cad_solic_nome_curso_text"
                value="<?= htmlspecialchars($solic_nome_curso_text) ?>" disabled>
            </div>
          </div>

          <div class="col-12" id="camp_solic_nome_atividade" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Nome da Atividade <span>*</span></label>
              <input type="text" class="form-control text-uppercase" id="cad_solic_nome_atividade"
                value="<?= htmlspecialchars($solic_nome_atividade) ?>" disabled>
            </div>
          </div>

          <div class="col-12" id="camp_solic_nome_comp_ativ" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Nome do Componente/Atividade <span>*</span></label>
              <input type="text" class="form-control text-uppercase" id="cad_solic_nome_comp_ativ"
                value="<?= htmlspecialchars($solic_nome_comp_ativ) ?>" disabled>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>

          <div class="col-md-6" id="camp_solic_semestre" style="display: none;">
            <div class="form_margem">
              <?php try {
                $sql = $conn->prepare("SELECT cs_id, cs_semestre FROM conf_semestre ORDER BY cs_id ASC");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar os dados";
              } ?>
              <label class="form-label">Semestre <span>*</span></label>
              <select class="form-select text-uppercase" id="cad_solic_semestre" disabled>
                <option selected value="<?= htmlspecialchars($cs_id) ?>"><?= htmlspecialchars($cs_semestre) ?></option>
              </select>
            </div>
          </div>

          <div class="col-md-6" id="camp_solic_nome_prof_resp" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Nome do Professor/Responsável <span>*</span></label>
              <input type="text" class="form-control text-uppercase" id="cad_solic_nome_prof_resp"
                value="<?= htmlspecialchars($solic_nome_prof_resp) ?>" disabled>
            </div>
          </div>

          <div class="col-md-6" id="camp_solic_contato" style="display: none;">
            <div class="form_margem">
              <label class="form-label">Telefone para contato <span>*</span></label>
              <input type="text" class="form-control cel_tel" id="cad_solic_contato"
                value="<?= htmlspecialchars($solic_contato) ?>" disabled>
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

              $('[id^="camp_"]').hide().find('input, select').prop('required', false);

              if (tipoAtiv == '1') {
                // 1	ATIVIDADE ACADÊMICA
                $('#camp_solic_curso').show().find('#cad_solic_curso').prop('required', true);
              } else if (tipoAtiv == '2') {
                // 2	ATIVIDADE ADMINISTRATIVA
                $('[id^="camp_"]').hide();
                $('#camp_solic_nome_atividade, #camp_solic_nome_prof_resp, #camp_solic_contato').show().find('input').prop('required', true);
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
                $('#camp_solic_comp_curric').show().find('#cad_solic_comp_curric').prop('required', true);
                // 0	OUTRO
                if (compCurric == '0') {
                  $('#camp_solic_nome_comp_ativ, #camp_solic_semestre, #camp_solic_nome_prof_resp, #camp_solic_contato').show().find('input, select').prop('required', true);
                } else if (compCurric) {
                  $('#camp_solic_nome_prof_resp, #camp_solic_contato').show().find('input').prop('required', true);
                }
              }

              if (curso == '8') {
                // 8	EXTENSÃO CURRICULARIZADA
                $('#camp_solic_nome_curso').show().find('#cad_solic_nome_curso').prop('required', true);
                if (nomeCurso) {
                  $('#camp_solic_nome_atividade, #camp_solic_semestre, #camp_solic_nome_prof_resp, #camp_solic_contato').show().find('input, select').prop('required', true);
                }
              }

              if ([7, 10, 19, 28, 31].includes(parseInt(curso))) {
                // 7	EXTENSÃO
                // 10	GRUPO DE PESQUISA
                // 19	PROGRAMA CANDEAL
                // 28	NIDD
                // 31	RESERVAS ADMINISTRATIVAS
                $('#camp_solic_nome_atividade, #camp_solic_nome_prof_resp, #camp_solic_contato').show().find('input').prop('required', true);
              }

              if ([11, 22].includes(parseInt(curso))) {
                // 11	LATO SENSU
                // 22	STRICTO SENSU
                $('#camp_solic_nome_curso_text, #camp_solic_nome_comp_ativ, #camp_solic_semestre, #camp_solic_nome_prof_resp, #camp_solic_contato').show().find('input, select').prop('required', true);
              }
            }

            $('#cad_solic_tipo_ativ, #cad_solic_curso, #cad_solic_comp_curric, #cad_solic_nome_curso, #camp_solic_nome_curso_text').change(function () {
              $('[id^="camp_"]').hide().find('input, select').prop('required', false);
              toggleFields();
            });

            toggleFields();
          });
        </script>

        <script>
          $(document).ready(function () {
            function carregarComponentes(cursoId, componenteSelecionado) {
              if (cursoId !== "") {
                $.ajax({
                  url: 'buscar_componentes.php',
                  type: 'POST',
                  data: {
                    curso_id: cursoId
                  },
                  success: function (data) {
                    $('#cad_solic_comp_curric').html(data);

                    // Se houver um componente já selecionado, definir ele no select
                    if (componenteSelecionado) {
                      $('#cad_solic_comp_curric').val(componenteSelecionado).trigger('change');
                    }
                  }
                });
              } else {
                $('#cad_solic_comp_curric').html('<option value="">Selecione um componente</option>');
              }
            }

            // Evento quando o curso for alterado manualmente pelo usuário
            $('#cad_solic_curso').change(function () {
              var cursoId = $(this).val();
              carregarComponentes(cursoId, null);
            });

            // Verificar se já existem valores pré-selecionados ao carregar a página
            var cursoSelecionado = $('#cad_solic_curso').val();
            var componenteSelecionado = $('#cad_solic_comp_curric').data('valor'); // Defina esse valor no HTML

            if (cursoSelecionado) {
              carregarComponentes(cursoSelecionado, componenteSelecionado);
            }
          });
        </script>
      </div>
    </div>

    <div class="card">

      <div class="card-header" style="background: var(--azul_alpha);">
        <div class="row align-items-center">
          <div class="col-12 tit_nova_solicitacao">
            <h3 class="text-uppercase m-0 fs-16" style="color: var(--preto);">Informações da Reserva <span
                class="fs-12 ms-2"
                style="background: var(--azul); color: #fff; padding: 3px 10px; border-radius: 3px; font-weight: 500;">Aulas
                Práticas</span></h3>
          </div>
        </div>
      </div>

      <div class="card-body p-sm-4 p-3 form_solicitacao">

        <div class="row grid gx-3">

          <div class="col-12">
            <label class="form-label">Deseja realizar a solicitação de reserva de espaços para aulas práticas?
              <span>*</span></label>
            <div class="check_container">
              <div class="form-check form_solicita">
                <input class="form-check-input form_solicita" type="radio" name="solic_ap_aula_pratica"
                  id="solic_ap_aula_pratica_sim" value="1" <?= isset($solic_ap_aula_pratica) && $solic_ap_aula_pratica == 1 ? 'checked' : ''; ?> disabled>
                <label class="form-check-label" for="solic_ap_aula_pratica_sim">Sim</label>
              </div>

              <div class="form-check form_solicita">
                <input class="form-check-input form_solicita" type="radio" name="solic_ap_aula_pratica"
                  id="solic_ap_aula_pratica_nao" value="0" <?= isset($solic_ap_aula_pratica) && $solic_ap_aula_pratica == 0 ? 'checked' : ''; ?> disabled>
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
                <select class="form-select text-uppercase" id="solic_ap_campus" disabled>
                  <option selected value="<?= htmlspecialchars($campus_pratico_id) ?>">
                    <?= htmlspecialchars($campus_pratico_nome) ?>
                  </option>
                </select>
              </div>
            </div>

            <div id="campo_ap_info_pratic_espaco" style="display: none;">

              <div class="col-12" id="campo_info_pratic_espaco_brotas">
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
                <select class="form-select text-uppercase" name="solic_ap_espaco_brotas[]" multiple
                  id="cad_reserva_local_brotas_mult" disabled>
                  <?php foreach ($result as $res): ?>
                    <option value="<?= $res['esp_id'] ?>" <?= in_array($res['esp_id'], $espaco_b) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($res['esp_codigo']) . ' - ' . htmlspecialchars($res['esp_nome_local']) . ' - ' . htmlspecialchars($res['and_andar']) . ' - ' . htmlspecialchars($res['pav_pavilhao']) . ' - CAPACIDADE: ' . htmlspecialchars($res['esp_quant_maxima']) . ' ALUNOS' ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <script>
                  $(document).ready(function () {
                    $('#cad_reserva_local_brotas_mult').select2({
                      placeholder: "Selecione as opções",
                      tags: false,
                      allowClear: true,
                      width: '100%'
                    });
                  });
                </script>
              </div>

              <div class="col-12" id="campo_info_pratic_espaco_cabula" style="display: none;">
                <?php try {
                  $espaco_c = explode(", ", $solic_ap_espaco);
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
                <select class="form-select text-uppercase" name="solic_ap_espaco_cabula[]" multiple
                  id="cad_reserva_local_cabula_mult" disabled>
                  <?php foreach ($result as $res): ?>
                    <option value="<?= $res['esp_id'] ?>" <?= in_array($res['esp_id'], $espaco_c) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($res['esp_codigo']) . ' - ' . htmlspecialchars($res['esp_nome_local']) . ' - ' . htmlspecialchars($res['and_andar']) . ' - ' . htmlspecialchars($res['pav_pavilhao']) . ' - CAPACIDADE: ' . htmlspecialchars($res['esp_quant_maxima']) . ' ALUNOS' ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <script>
                  $(document).ready(function () {
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
                  <select class="form-select text-uppercase" id="solic_ap_quant_turma" disabled>
                    <option selected value="<?= htmlspecialchars($ctp_id) ?>"><?= htmlspecialchars($ctp_turma) ?>
                    </option>
                  </select>
                </div>
              </div>

            </div>

            <div id="campo_info_pratic_tipo_reserva" style="display: none;">

              <div class="col-12">
                <div class="form_margem">
                  <label class="form-label">Número estimado de participantes <span>*</span></label>
                  <input type="text" class="form-control text-uppercase" name="solic_ap_quant_particip"
                    id="solic_ap_quant_particip" value="<?= htmlspecialchars($solic_ap_quant_particip) ?>" disabled>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Tipo da reserva <span>*</span></label>

                <div class="check_container">
                  <div class="form-check form_solicita">
                    <input type="radio" class="form-check-input form_solicita" id="solic_ap_tipo_reserva1"
                      name="solic_ap_tipo_reserva" value="1" <?php echo ($solic_ap_tipo_reserva == 1) ? 'checked' : ''; ?>
                      disabled>
                    <label class="form-check-label" for="solic_ap_tipo_reserva1">Esporádica - Reserva em data(s)
                      específica(s).</label>
                  </div>
                  <div class="form-check form_solicita">
                    <input type="radio" class="form-check-input form_solicita" id="solic_ap_tipo_reserva2"
                      name="solic_ap_tipo_reserva" value="2" <?php echo ($solic_ap_tipo_reserva == 2) ? 'checked' : ''; ?>
                      disabled>
                    <label class="form-check-label" for="solic_ap_tipo_reserva2">Fixa - Reserva permanente em
                      determinado(s) dia(s) da semana, durante todo o semestre de acordo com o calendário
                      acadêmico.</label>
                  </div>
                </div>
              </div>
            </div>

            <div id="campo_info_pratic_data_reserva" style="display: none;">

              <div class="col-12 mb-4" id="campo_info_pratic_dias_semana" style="display: none;">
                <?php try {
                  $dias = explode(", ", $solic_ap_dia_reserva);
                  $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Dia(s) da semana <span>*</span></label>
                <select class="form-select text-uppercase" name="solic_ap_dia_reserva[]" multiple
                  id="cad_solic_ap_dia_reserva" disabled>
                  <?php foreach ($result as $res): ?>
                    <option value="<?= $res['week_id'] ?>" <?= in_array($res['week_id'], $dias) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($res['week_dias']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <script>
                  $(document).ready(function () {
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
                  <textarea class="form-control" id="solic_ap_data_reserva" rows="5"
                    disabled><?= htmlspecialchars(str_replace('<br />', '', $solic_ap_data_reserva)) ?></textarea>
                </div>
              </div>

              <div class="row">

                <?php if ($solic_ap_tipo_reserva == 2) { ?>
                  <div class="col-md-6">
                    <div class="form_margem">
                      <label class="form-label">Data Início</label>
                      <input type="text" class="form-control" id="solic_ap_data_inicio"
                        value="<?= $solic_ap_data_inicio ? htmlspecialchars(date("d/m/Y", strtotime($solic_ap_data_inicio))) : ''; ?>"
                        disabled>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form_margem">
                      <label class="form-label">Data Fim</label>
                      <input type="text" class="form-control" id="solic_ap_data_fim"
                        value="<?= $solic_ap_data_fim ? htmlspecialchars(date("d/m/Y", strtotime($solic_ap_data_fim))) : ''; ?>"
                        disabled>
                    </div>
                  </div>
                <?php } ?>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form_margem">
                    <label class="form-label">Horário inicial <span>*</span></label>
                    <input type="time" class="form-control hora" id="solic_ap_hora_inicio"
                      value="<?= $solic_ap_hora_inicio ? htmlspecialchars(date("H:i", strtotime($solic_ap_hora_inicio))) : ''; ?>"
                      disabled>
                  </div>
                  <script>
                    flatpickr("#solic_ap_hora_inicio", {
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
                    <input type="time" class="form-control hora" id="solic_ap_hora_fim"
                      value="<?= $solic_ap_hora_fim ? htmlspecialchars(date("H:i", strtotime($solic_ap_hora_fim))) : ''; ?>"
                      disabled>
                  </div>
                  <script>
                    flatpickr("#solic_ap_hora_fim", {
                      enableTime: true, // ativa o seletor de hora
                      noCalendar: true, // oculta o calendário
                      dateFormat: "H:i", // formato 24h: horas:minutos
                      time_24hr: true, // garante o formato 24h
                      allowInput: true // permite apagar e digitar manualmente
                    });
                  </script>
                </div>
                <script>
                  document.addEventListener('DOMContentLoaded', function () {
                    const horaInicio = document.getElementById('solic_ap_hora_inicio');
                    const horaFim = document.getElementById('solic_ap_hora_fim');

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
              </div>

              <div class="col-12">
                <label class="form-label">Selecione como deseja informar quais serão os materiais, equipamentos e
                  insumos necessários para a realização da aula nos espaços de prática <span>*</span></label>

                <div class="check_container">
                  <div class="form-check form_solicita">
                    <input type="radio" class="form-check-input form_solicita" id="validationFormCheck2"
                      name="solic_ap_tipo_material" value="1" <?php echo ($solic_ap_tipo_material == 1) ? 'checked' : ''; ?> disabled>
                    <label class="form-check-label" for="validationFormCheck2">Anexar o formulário de planejamento de
                      atividades de práticas nos laboratórios de ensino.</label>
                  </div>
                  <div class="form-check form_solicita">
                    <input type="radio" class="form-check-input form_solicita" id="validationFormCheck3"
                      name="solic_ap_tipo_material" value="2" <?php echo ($solic_ap_tipo_material == 2) ? 'checked' : ''; ?> disabled>
                    <label class="form-check-label" for="validationFormCheck3">Informar o título da aula (caso o
                      formulário já esteja no banco de dados do laboratório de ensino).</label>
                  </div>
                  <div class="form-check form_solicita">
                    <input type="radio" class="form-check-input form_solicita" id="validationFormCheck4"
                      name="solic_ap_tipo_material" value="3" <?php echo ($solic_ap_tipo_material == 3) ? 'checked' : ''; ?> disabled>
                    <label class="form-check-label" for="validationFormCheck4">Descrevê-los com as respectivas
                      quantidades.</label>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>
              </div>
            </div>

            <div id="campo_info_pratic_anexar" style="display: none;">

              <div class="col-12" id="file_ancora">
                <div class="form_margem">
                  <label class="form-label m-0">Formulário de planejamento de atividades de práticas nos laboratórios de
                    ensino <span>*</span></label>

                  <div class="input-group">
                    <input type="file" class="form-control input_arquivo" name="arquivos[]" id="cad_info_pratic_arquivo"
                      disabled>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                  <div class="mt-0 mb-2">

                    <?php $sql = $conn->prepare("SELECT * FROM solicitacao_arq WHERE sarq_solic_id = :sarq_solic_id");
                    $sql->execute(['sarq_solic_id' => $solic_id]);
                    while ($arq = $sql->fetch(PDO::FETCH_ASSOC)) {
                      $sarq_id = $arq['sarq_id'];
                      $sarq_solic_id = $arq['sarq_solic_id'];
                      $sarq_categoria = $arq['sarq_categoria'];
                      $sarq_arquivo = $arq['sarq_arquivo'];
                      ?>

                      <div class="result_file">
                        <div class="result_file_name"><a
                            href="uploads/solicitacoes/<?= $solic_codigo . '/' . $sarq_arquivo ?>"
                            target="_blank"><?= $sarq_arquivo ?></a></div>

                        <?php
                        $sta_solic = array(3, 5, 6);
                        if (in_array($solic_sta_status, $sta_solic)) {
                          ?>
                          <span class="item_bt_row"></span>
                        <?php } else { ?>
                          <span class="item_bt_row">
                            <a href="controller/controller_propostas.php?funcao=exc_arq&ident=<?= $sarq_id ?>&p=3&c=<?= $solic_codigo ?>&f=<?= $sarq_arquivo ?>"
                              class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
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
                  <textarea class="form-control" id="solic_ap_tit_aulas" rows="5"
                    disabled><?= htmlspecialchars(str_replace('<br />', '', $solic_ap_tit_aulas)) ?></textarea>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>

            </div>
            <div id="campo_info_pratic_quant" style="display: none;">

              <div class="col-12">
                <div class="form_margem">
                  <label class="form-label">Descreva os materiais, insumos e equipamentos, com suas respectivas
                    quantidades, que serão necessários para a realização da aula no espaço de prática
                    <span>*</span></label>
                  <textarea class="form-control" id="solic_ap_quant_material" rows="5"
                    disabled><?= htmlspecialchars(str_replace('<br />', '', $solic_ap_quant_material)) ?></textarea>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            </div>

            <div id="campo_info_pratic_obs" style="display: none;">
              <div class="col-12">
                <div class="form_margem">
                  <label class="form-label">Observações</label>
                  <textarea class="form-control" rows="5"
                    disabled><?= htmlspecialchars(str_replace('<br />', '', $solic_ap_obs)) ?></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <script>
          document.addEventListener('DOMContentLoaded', function () {

            const cad_ap_aula_pratica = document.querySelectorAll('input[name="solic_ap_aula_pratica"]');
            const campo_info_pratic_campus = document.getElementById('campo_info_pratic_campus');

            const solic_ap_campus = document.getElementById('solic_ap_campus');
            const campo_ap_info_pratic_espaco = document.getElementById('campo_ap_info_pratic_espaco');
            const campo_info_pratic_espaco_cabula = document.getElementById('campo_info_pratic_espaco_cabula');
            const campo_info_pratic_espaco_brotas = document.getElementById('campo_info_pratic_espaco_brotas');

            const solic_ap_quant_turma = document.getElementById('solic_ap_quant_turma');
            const solic_ap_quant_particip = document.getElementById('solic_ap_quant_particip');
            const campo_info_pratic_tipo_reserva = document.getElementById('campo_info_pratic_tipo_reserva');
            const solic_ap_tipo_reserva = document.querySelectorAll('input[name="solic_ap_tipo_reserva"]');

            const campo_info_pratic_data_reserva = document.getElementById('campo_info_pratic_data_reserva');
            const campo_info_pratic_datas = document.getElementById('campo_info_pratic_datas');
            const campo_info_pratic_dias_semana = document.getElementById('campo_info_pratic_dias_semana');

            const solic_ap_hora_inicio = document.getElementById('solic_ap_hora_inicio');
            const solic_ap_hora_fim = document.getElementById('solic_ap_hora_fim');
            const solic_ap_data_reserva = document.getElementById('solic_ap_data_reserva');
            const cad_solic_ap_dia_reserva = document.getElementById('cad_solic_ap_dia_reserva');

            const solic_ap_tipo_material = document.querySelectorAll('input[name="solic_ap_tipo_material"]');

            const campo_info_pratic_anexar = document.getElementById('campo_info_pratic_anexar');
            const campo_info_pratic_titulo = document.getElementById('campo_info_pratic_titulo');
            const campo_info_pratic_quant = document.getElementById('campo_info_pratic_quant');
            const campo_info_pratic_obs = document.getElementById('campo_info_pratic_obs');

            const cad_info_pratic_arquivo = document.getElementById('cad_info_pratic_arquivo');
            const solic_ap_tit_aulas = document.getElementById('solic_ap_tit_aulas');
            const solic_ap_quant_material = document.getElementById('solic_ap_quant_material');

            // Função para resetar e esconder todos os campos relacionados à aula prática
            function resetarCamposPratica() {
              campo_info_pratic_campus.style.display = 'none';
              campo_ap_info_pratic_espaco.style.display = 'none';
              campo_info_pratic_espaco_cabula.style.display = 'none';
              campo_info_pratic_espaco_brotas.style.display = 'none';
              campo_info_pratic_tipo_reserva.style.display = 'none';
              campo_info_pratic_data_reserva.style.display = 'none';
              campo_info_pratic_datas.style.display = 'none';
              campo_info_pratic_dias_semana.style.display = 'none';
              campo_info_pratic_anexar.style.display = 'none';
              campo_info_pratic_titulo.style.display = 'none';
              campo_info_pratic_quant.style.display = 'none';
              campo_info_pratic_obs.style.display = 'none';

              // Remover obrigatoriedades
              document.getElementById('solic_ap_campus').required = false;
              document.getElementById('cad_reserva_local_cabula_mult').required = false;
              document.getElementById('cad_reserva_local_brotas_mult').required = false;
              solic_ap_quant_turma.required = false;
              solic_ap_quant_particip.required = false;

              solic_ap_tipo_reserva.forEach(r => r.required = false);
              solic_ap_tipo_material.forEach(r => r.required = false);

              solic_ap_hora_inicio.required = false;
              solic_ap_hora_fim.required = false;
              solic_ap_data_reserva.required = false;
              cad_solic_ap_dia_reserva.required = false;

              cad_info_pratic_arquivo.required = false;
              solic_ap_tit_aulas.required = false;
              solic_ap_quant_material.required = false;
            }

            // Exibe ou oculta campos com base em "aula prática"
            cad_ap_aula_pratica.forEach(radio => {
              radio.addEventListener('change', () => {
                if (radio.value === '1' && radio.checked) {
                  campo_info_pratic_campus.style.display = 'block';
                  document.getElementById('solic_ap_campus').required = true;
                } else if (radio.value === '0' && radio.checked) {
                  resetarCamposPratica();
                }
              });
            });

            // Campus selecionado
            solic_ap_campus.addEventListener('change', () => {
              if (solic_ap_campus.value !== "") {
                campo_ap_info_pratic_espaco.style.display = 'block';

                if (solic_ap_campus.value == 1) {
                  campo_info_pratic_espaco_cabula.style.display = 'block';
                  campo_info_pratic_espaco_brotas.style.display = 'none';
                  document.getElementById('cad_reserva_local_cabula_mult').required = true;
                  document.getElementById('cad_reserva_local_brotas_mult').required = false;
                } else {
                  campo_info_pratic_espaco_cabula.style.display = 'none';
                  campo_info_pratic_espaco_brotas.style.display = 'block';
                  document.getElementById('cad_reserva_local_cabula_mult').required = false;
                  document.getElementById('cad_reserva_local_brotas_mult').required = true;
                }

                solic_ap_quant_turma.required = true;
              } else {
                campo_ap_info_pratic_espaco.style.display = 'none';
                document.getElementById('cad_reserva_local_cabula_mult').required = false;
                document.getElementById('cad_reserva_local_brotas_mult').required = false;
                solic_ap_quant_turma.required = true;
              }
            });

            // Quantidade de turmas
            solic_ap_quant_turma.addEventListener('change', () => {
              if (solic_ap_quant_turma.value !== "") {
                campo_info_pratic_tipo_reserva.style.display = 'block';
                solic_ap_quant_particip.required = true;
                solic_ap_tipo_reserva.forEach(r => r.required = true);
              } else {
                campo_info_pratic_tipo_reserva.style.display = 'none';
                solic_ap_quant_particip.required = false;
                solic_ap_tipo_reserva.forEach(r => r.required = false);
              }
            });

            // Tipo de reserva (data x dias da semana)
            solic_ap_tipo_reserva.forEach(radio => {
              radio.addEventListener('change', () => {
                campo_info_pratic_data_reserva.style.display = 'block';
                solic_ap_hora_inicio.required = true;
                solic_ap_hora_fim.required = true;
                solic_ap_tipo_material.forEach(r => r.required = true);

                if (radio.value === "1") {
                  campo_info_pratic_datas.style.display = 'block';
                  campo_info_pratic_dias_semana.style.display = 'none';
                  solic_ap_data_reserva.required = true;
                  cad_solic_ap_dia_reserva.required = false;
                } else if (radio.value === "2") {
                  campo_info_pratic_datas.style.display = 'none';
                  campo_info_pratic_dias_semana.style.display = 'block';
                  solic_ap_data_reserva.required = false;
                  cad_solic_ap_dia_reserva.required = true;
                }
              });
            });

            // Tipo de material
            solic_ap_tipo_material.forEach(radio => {
              radio.addEventListener('change', () => {
                campo_info_pratic_obs.style.display = 'block';
                campo_info_pratic_anexar.style.display = 'none';
                campo_info_pratic_titulo.style.display = 'none';
                campo_info_pratic_quant.style.display = 'none';

                cad_info_pratic_arquivo.required = false;
                solic_ap_tit_aulas.required = false;
                solic_ap_quant_material.required = false;

                if (radio.value === "1") {
                  campo_info_pratic_anexar.style.display = 'block';
                  cad_info_pratic_arquivo.required = true;
                } else if (radio.value === "2") {
                  campo_info_pratic_titulo.style.display = 'block';
                  solic_ap_tit_aulas.required = true;
                } else if (radio.value === "3") {
                  campo_info_pratic_quant.style.display = 'block';
                  solic_ap_quant_material.required = true;
                }
              });
            });

            // <- AQUI adicionamos a chamada da inicialização
            function inicializarFormularioPreenchido() {
              const radioAulaSelecionado = document.querySelector('input[name="solic_ap_aula_pratica"]:checked');
              if (radioAulaSelecionado) {
                radioAulaSelecionado.dispatchEvent(new Event('change'));
              }

              if (solic_ap_campus.value !== "") {
                solic_ap_campus.dispatchEvent(new Event('change'));
              }

              if (solic_ap_quant_turma.value !== "") {
                solic_ap_quant_turma.dispatchEvent(new Event('change'));
              }

              const radioReservaSelecionado = document.querySelector('input[name="solic_ap_tipo_reserva"]:checked');
              if (radioReservaSelecionado) {
                radioReservaSelecionado.dispatchEvent(new Event('change'));
              }

              const radioMaterialSelecionado = document.querySelector('input[name="solic_ap_tipo_material"]:checked');
              if (radioMaterialSelecionado) {
                radioMaterialSelecionado.dispatchEvent(new Event('change'));
              }
            }

            inicializarFormularioPreenchido();

          });
        </script>
      </div>
    </div>

    <div class="card">

      <div class="card-header" style="background: var(--roxo_alpha);">
        <div class="row align-items-center">
          <div class="col-12 tit_nova_solicitacao">
            <h3 class="text-uppercase m-0 fs-16" style="color: var(--preto);">Informações da Reserva <span
                class="fs-12 ms-2"
                style="background: var(--roxo); color: #fff; padding: 3px 10px; border-radius: 3px; font-weight: 500;">Aulas
                Teóricas</span></h3>
          </div>
        </div>
      </div>

      <div class="card-body p-sm-4 p-3 form_solicitacao">

        <div class="row grid gx-3">

          <div class="col-12">
            <label class="form-label">Deseja realizar a solicitação de reserva de espaços para aulas teóricas?
              <span>*</span></label>

            <div class="check_container">
              <div class="form-check form_solicita">
                <input class="form-check-input form_solicita" type="radio" name="solic_at_aula_teorica"
                  id="solic_at_aula_teorica_sim" value="1" <?= isset($solic_at_aula_teorica) && $solic_at_aula_teorica == 1 ? 'checked' : ''; ?> disabled>
                <label class="form-check-label" for="solic_at_aula_teorica_sim">Sim</label>
              </div>

              <div class="form-check form_solicita">
                <input class="form-check-input form_solicita" type="radio" name="solic_at_aula_teorica"
                  id="solic_at_aula_teorica_nao" value="0" <?= isset($solic_at_aula_teorica) && $solic_at_aula_teorica == 0 ? 'checked' : ''; ?> disabled>
                <label class="form-check-label" for="solic_at_aula_teorica_nao">Não</label>
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
                <select class="form-select text-uppercase" id="solic_at_campus" disabled>
                  <option selected value="<?= htmlspecialchars($campus_teorico_id) ?>">
                    <?= htmlspecialchars($campus_teorico_nome) ?>
                  </option>v
                </select>
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
                  <select class="form-select text-uppercase" id="solic_at_quant_sala" disabled>
                    <option selected value="<?= htmlspecialchars($cst_id) ?>"><?= htmlspecialchars($cst_sala) ?>
                    </option>
                  </select>
                </div>
              </div>
            </div>

            <div id="campo_solic_at_quant_particip" style="display: none;">

              <div class="col-12">
                <div class="form_margem">
                  <label class="form-label">Número estimado de participantes <span>*</span></label>
                  <input type="text" class="form-control text-uppercase" id="solic_at_quant_particip"
                    value="<?= htmlspecialchars($solic_at_quant_particip) ?>" disabled>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Tipo da reserva <span>*</span></label>

                <div class="check_container">
                  <div class="form-check form_solicita">
                    <input type="radio" class="form-check-input form_solicita" id="solic_at_tipo_reserva1"
                      name="solic_at_tipo_reserva" value="1" <?= isset($solic_at_tipo_reserva) && $solic_at_tipo_reserva == 1 ? 'checked' : ''; ?> disabled>
                    <label class="form-check-label" for="solic_at_tipo_reserva1">Esporádica - Reserva em data(s)
                      específica(s).</label>
                  </div>
                  <div class="form-check form_solicita">
                    <input type="radio" class="form-check-input form_solicita" id="solic_at_tipo_reserva2"
                      name="solic_at_tipo_reserva" value="2" <?= isset($solic_at_tipo_reserva) && $solic_at_tipo_reserva == 2 ? 'checked' : ''; ?> disabled>
                    <label class="form-check-label" for="solic_at_tipo_reserva2">Fixa - Reserva permanente em
                      determinado(s) dia(s) da semana, durante todo o semestre de acordo com o calendário
                      acadêmico.</label>
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
                <select class="form-select text-uppercase" name="solic_at_dia_reserva[]" multiple
                  id="cad_solic_at_dia_reserva" disabled>
                  <?php foreach ($result as $res): ?>
                    <option value="<?= $res['week_id'] ?>" <?= in_array($res['week_id'], $dias) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($res['week_dias']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <script>
                  $(document).ready(function () {
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
                  <textarea class="form-control" id="solic_at_data_reserva" rows="5"
                    disabled><?= htmlspecialchars(str_replace('<br />', '', $solic_at_data_reserva)) ?></textarea>
                </div>
              </div>

              <div class="row">
                <?php if ($solic_at_tipo_reserva == 2) { ?>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form_margem">
                        <label class="form-label">Data Início</label>
                        <input type="text" class="form-control" id="solic_at_data_inicio"
                          value="<?= $solic_at_data_inicio ? htmlspecialchars(date("d/m/Y", strtotime($solic_at_data_inicio))) : ''; ?>"
                          disabled>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form_margem">
                        <label class="form-label">Data Fim</label>
                        <input type="text" class="form-control" id="solic_at_data_fim"
                          value="<?= $solic_at_data_fim ? htmlspecialchars(date("d/m/Y", strtotime($solic_at_data_fim))) : ''; ?>"
                          disabled>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>


              <div class="row">
                <div class="col-md-6">
                  <div class="form_margem">
                    <label class="form-label">Horário inicial <span>*</span></label>
                    <input type="time" class="form-control hora" id="solic_at_hora_inicio"
                      value="<?= $solic_at_hora_inicio ? htmlspecialchars(date("H:i", strtotime($solic_at_hora_inicio))) : ''; ?>"
                      disabled>
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
                    <input type="time" class="form-control hora" id="solic_at_hora_fim"
                      value="<?= $solic_at_hora_fim ? htmlspecialchars(date("H:i", strtotime($solic_at_hora_fim))) : ''; ?>"
                      disabled>
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
                  document.addEventListener('DOMContentLoaded', function () {
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
                    <textarea class="form-control" name="solic_at_recursos" rows="5"
                      disabled><?= htmlspecialchars(str_replace('<br />', '', $solic_at_recursos)) ?></textarea>
                  </div>
                </div>

                <div class="col-12">
                  <div class="form_margem">
                    <label class="form-label">Observações</label>
                    <textarea class="form-control" name="solic_at_obs" rows="5"
                      disabled><?= htmlspecialchars(str_replace('<br />', '', $solic_at_obs)) ?></textarea>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
        <script>
          document.addEventListener('DOMContentLoaded', function () {

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


            // <- AQUI adicionamos a chamada da inicialização
            function inicializarFormularioPreenchido() {
              const radioAulaSelecionado = document.querySelector('input[name="solic_at_aula_teorica"]:checked');
              if (radioAulaSelecionado) {
                radioAulaSelecionado.dispatchEvent(new Event('change'));
              }

              if (solic_at_campus.value !== "") {
                solic_at_campus.dispatchEvent(new Event('change'));
              }

              if (solic_at_quant_sala.value !== "") {
                solic_at_quant_sala.dispatchEvent(new Event('change'));
              }

              const radioReservaSelecionado = document.querySelector('input[name="solic_at_tipo_reserva"]:checked');
              if (radioReservaSelecionado) {
                radioReservaSelecionado.dispatchEvent(new Event('change'));
              }
            }

            inicializarFormularioPreenchido();

          });
        </script>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>