<style>
  .card_color_azul {
    background: var(--azul_alpha);

    & h5 {
      text-transform: uppercase;
      margin-right: 10px;
    }

    & span {
      background: var(--azul);
      padding: 4px 10px;
      font-size: 12px;
      border-radius: 3px;
      text-transform: uppercase;
      color: #fff;
    }
  }

  .card_color_roxo {
    background: var(--roxo_alpha);

    & h5 {
      text-transform: uppercase;
      margin-right: 10px;
    }

    & span {
      background: var(--roxo);
      padding: 4px 10px;
      font-size: 12px;
      border-radius: 3px;
      text-transform: uppercase;
      color: #fff;
    }
  }

  .form_solicitacao {}

  .form_solicitacao .form-label {
    font-size: .875rem !important;
    margin: 0;
  }

  .form_solicitacao .form_margem {
    margin-bottom: 30px;
  }

  .form_solicitacao .label_info {
    margin: 5px 0 0 0;
    font-size: 0.813rem;
    font-style: normal;
  }

  .form_solicitacao .form-select,
  .form_solicitacao .form-control,
  .form_solicitacao .select2-selection {
    margin: 8px 0 10px 0;
  }

  .form_solicitacao .check_container {
    margin: 20px 0 40px 0;
  }

  .form_solicitacao .form-check-label {
    font-size: 0.875rem;
  }

  .form_solicitacao .select2-container--default .select2-selection--single .select2-selection__arrow {
    right: 10px;
    top: 10px;
    left: auto;
  }
</style>






























<div class="card-body p-sm-4 p-3 d-none">

  <form class="needs-validation form_solicitacao" method="POST" action="cad_proposta.php?tp=MQ==&st=Mg==" id="BotaoProgress" autocomplete="off" novalidate>

    <div class="row grid gx-3">

      <input type="hidden" class="form-control" name="prop_tipo" value="<?= base64_encode($propc_cat) ?>" required>

      <div class="col-12">
        <div class="form_margem">
          <label class="form-label">Tipo de Atividade <span>*</span></label>
          <select class="form-select text-uppercase" name="" id="cad_iden_tipo_atividade" required>
            <option selected disabled value=""></option>
            <option value="1">Atividade Acadêmica</option>
            <option value="2">Atividade Administrativa</option>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_ident_curso" style="display: none;">
        <div class="form_margem">
          <?php try {
            $sql = $conn->query("SELECT curs_id, curs_curso FROM cursos WHERE curs_status != 0 ORDER BY curs_curso");
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar os dados";
          } ?>
          <label class="form-label">Curso <span>*</span></label>
          <select class="form-select text-uppercase" name="" id="cad_ident_cursos">
            <option selected disabled value=""></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_ident_comp_curricular" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Componente Curricular <span>*</span></label>
          <div class="label_info mt-0">Os componentes curriculares estão ordenados por semestre e ordem alfabética.</div>
          <select class="form-select text-uppercase" name="" id="cad_iden_comp_curricular">
            <option selected disabled value=""></option>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_ident_select_nome_curso" style="display: none;">
        <div class="form_margem">
          <?php try {
            $sql = $conn->query("SELECT curs_id, curs_curso FROM cursos WHERE curs_id IN (1,5,6,9,14,18,21)  ORDER BY curs_curso");
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar os dados";
          } ?>
          <label class="form-label">Nome do Curso <span>*</span></label>
          <select class="form-select text-uppercase" name="cad_iden_select_nome_curso" id="cad_iden_select_nome_curso">
            <option selected disabled value=""></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_ident_nome_atividade" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Nome da Atividade <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="cad_iden_nome_ativ" id="cad_iden_nome_ativ" maxlength="100">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_ident_nome_comp_ativ" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Nome do Componente/Atividade <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="cad_iden_nome_comp_ativ" id="cad_iden_nome_comp_ativ" maxlength="100">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_ident_semestre" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Semestre <span>*</span></label>
          <select class="form-select text-uppercase" name="cad_iden_semestre" id="cad_iden_semestre">
            <option selected disabled value=""></option>
            <option value="1">1º SEMESTRE</option>
            <option value="2">2º SEMESTRE</option>
            <option value="3">3º SEMESTRE</option>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_ident_nome_ativ_comp" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Nome da Atividade/Componente <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="" id="cad_iden_nome_atv_comp" maxlength="100">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_ident_prof" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Nome do Professor/Responsável <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="" id="cad_iden_nome_prof" maxlength="100">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_ident_tel" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Telefone para contato <span>*</span></label>
          <input type="text" class="form-control cel_tel" name="" id="cad_iden_contato">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>







      <script>
        ////////////////////////
        // CARD IDENTIFICAÇÃO //
        ////////////////////////
        document.addEventListener("DOMContentLoaded", function() {

          // CAMPOS
          const campo_ident_curso = document.getElementById("campo_ident_curso");
          const campo_ident_comp_curricular = document.getElementById("campo_ident_comp_curricular");
          const campo_ident_nome_ativ_comp = document.getElementById("campo_ident_nome_ativ_comp");
          const campo_ident_nome_comp_ativ = document.getElementById("campo_ident_nome_comp_ativ");
          const campo_ident_nome_atividade = document.getElementById("campo_ident_nome_atividade");
          const campo_ident_prof = document.getElementById("campo_ident_prof");
          const campo_ident_semestre = document.getElementById("campo_ident_semestre");
          const campo_ident_tel = document.getElementById("campo_ident_tel");
          const campo_ident_select_nome_curso = document.getElementById("campo_ident_select_nome_curso");
          const campo_ident_nome_curso = document.getElementById("campo_ident_nome_curso");
          // IDs
          const cad_iden_tipo_atividade = document.getElementById("cad_iden_tipo_atividade");
          const cad_iden_comp_curricular = document.getElementById("cad_iden_comp_curricular");
          const cad_iden_nome_comp_ativ = document.getElementById("cad_iden_nome_comp_ativ");
          const cad_iden_nome_ativ = document.getElementById("cad_iden_nome_ativ");
          const cad_iden_select_nome_curso = document.getElementById("cad_iden_select_nome_curso");
          const cad_iden_nome_curso = document.getElementById("cad_iden_nome_curso");
          const cad_iden_nome_atv_comp = document.getElementById("cad_iden_nome_atv_comp");
          const cad_iden_semestre = document.getElementById("cad_iden_semestre");
          const cad_iden_nome_prof = document.getElementById("cad_iden_nome_prof");
          const cad_iden_contato = document.getElementById("cad_iden_contato");

          // INICIA O SELECT2 // NOVA SOLICITAÇÃO - CURSOS
          $(document).ready(function() {
            $("#cad_ident_cursos").select2({
              closeOnSelect: true, // Fecha o dropdown ao selecionar um item
              language: {
                noResults: function(params) {
                  return "Nenhum resultado encontrado";
                },
              },
            });
          });
          const cad_ident_curso = $("#cad_ident_cursos"); // SE COMPO FOR SELECT2
          //

          cad_iden_tipo_atividade.addEventListener("change", function() {
            if (cad_iden_tipo_atividade.value === "1") {

              campo_ident_curso.style.display = "block"; // CAMPO CURSO
              cad_ident_curso.prop("required", true);

              campo_ident_nome_ativ_comp.style.display = "none"; // CAMPO ATIVIDADE/COMPONENTE
              cad_iden_nome_atv_comp.required = false;

              campo_ident_prof.style.display = "none"; // CAMPO NOME PROFESSOR
              cad_iden_nome_prof.required = false;

              campo_ident_tel.style.display = "none"; // CAMPO CONTATO
              cad_iden_contato.required = false;

            } else {

              campo_ident_curso.style.display = "none"; // CAMPO CURSO
              cad_ident_curso.prop("required", false);
              cad_ident_curso.val("").trigger("change.select2"); // RESETA CAMPO SELECT2
              cad_ident_curso.prop("selectedIndex", 0); // GARANTE QUE A SELEÇÃO DESAPAREÇA

              campo_ident_comp_curricular.style.display = "none"; // CAMPO COMPONENTE CURRICULAR
              cad_iden_comp_curricular.required = false;

              campo_ident_select_nome_curso.style.display = "none"; // CAMPO NOME DO CURSO
              cad_iden_select_nome_curso.required = false;

              campo_ident_nome_atividade.style.display = "none"; // CAMPO NOME ATIVIDADE
              cad_iden_nome_ativ.required = false;

              campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
              cad_iden_nome_comp_ativ.required = false;

              campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
              cad_iden_semestre.required = false;

              campo_ident_nome_ativ_comp.style.display = "block"; // CAMPO ATIVIDADE/COMPONENTE
              cad_iden_nome_atv_comp.required = true;

              campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
              cad_iden_nome_prof.required = true;

              campo_ident_tel.style.display = "block"; // CAMPO CONTATO
              cad_iden_contato.required = true;
            }
          });



          // CAMPO CURSO
          $(document).ready(function() {
            $('#cad_ident_cursos').on('change', function() {
              $(this).select2('close'); // FECHA O DROPDOWN AO SELECIONAR UM ITEM

              const numMostraComp = [1, 7, 8, 10, 11, 19, 22, 28];
              const numeNomeAtiv = [1, 7, 8, 10, 19, 22];

              if (!numMostraComp.includes(parseInt($(this).val()))) {

                campo_ident_comp_curricular.style.display = "block"; // CAMPO COMPONENTE CURRICULAR
                cad_iden_comp_curricular.required = true;

              } else if (numeNomeAtiv.includes(parseInt($(this).val()))) {

                campo_ident_comp_curricular.style.display = "none"; // CAMPO COMPONENTE CURRICULAR
                cad_iden_comp_curricular.required = false;

              } else {
                //campo_ident_comp_curricular.style.display = "none";
                //campo_ident_nome_atividade.style.display = "none";
              }

              // SE 1: RESERVAS ADMINISTRATIVAS
              // SE 7: EXTENSÃO
              // SE 10: GRUPO DE PESQUISA
              // SE 19: PROGRAMA CADEAL
              // SE 28: NIDD
              if ($(this).val() === "1" || $(this).val() === "7" || $(this).val() === "10" || $(this).val() === "19" || $(this).val() === "28") {

                campo_ident_select_nome_curso.style.display = "none"; // CAMPO NOME CURSO SELECT
                cad_iden_select_nome_curso.required = false;

                campo_ident_nome_atividade.style.display = "block"; // CAMPO NOME ATIVIDADE
                cad_iden_nome_ativ.required = true;

                campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
                cad_iden_nome_prof.required = true;
                cad_iden_nome_ativ.required = true;

                campo_ident_tel.style.display = "block"; // CAMPO CONTATO
                cad_iden_contato.required = true;

                campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
                cad_iden_nome_comp_ativ.required = false;

                campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
                cad_iden_semestre.required = false;

                campo_ident_comp_curricular.style.display = "none"; // CAMPO COMPONENTE CURRICULAR
                cad_iden_comp_curricular.required = false;

                campo_ident_nome_curso.style.display = "none"; // CAMPO NOME CURSO
                cad_iden_nome_curso.required = false;

                // SE 8: EXTENSÃO CURRICULARIZADA
              } else if ($(this).val() === "8") {

                campo_ident_select_nome_curso.style.display = "block"; // CAMPO NOME CURSO SELECT
                cad_iden_select_nome_curso.required = true;
                cad_iden_select_nome_curso.value = '';

                campo_ident_nome_curso.style.display = "none"; // CAMPO NOME CURSO
                cad_iden_nome_curso.required = false;

                campo_ident_nome_atividade.style.display = "none"; // CAMPO NOME ATIVIDADE
                cad_iden_nome_ativ.required = false;

                campo_ident_prof.style.display = "none"; // CAMPO NOME PROFESSOR
                cad_iden_nome_prof.required = false;
                cad_iden_nome_ativ.required = false;

                campo_ident_tel.style.display = "none"; // CAMPO CONTATO
                cad_iden_contato.required = false;

                campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
                cad_iden_nome_comp_ativ.required = false;

                campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
                cad_iden_semestre.required = false;

                // SE 11: LATO SENSU
                // SE 22: STRICTO SENSU
              } else if ($(this).val() === "11" || $(this).val() === "22") {

                campo_ident_nome_curso.style.display = "block"; // CAMPO NOME CURSO
                cad_iden_nome_curso.required = true;

                campo_ident_comp_curricular.style.display = "none"; // CAMPO COMPONENTE CURRICULAR
                cad_iden_comp_curricular.required = false;

                campo_ident_select_nome_curso.style.display = "none"; // CAMPO NOME CURSO SELECT

                campo_ident_nome_comp_ativ.style.display = "block"; // CAMPO COMPONENTE ATIVIDADE
                cad_iden_nome_comp_ativ.required = true;

                campo_ident_semestre.style.display = "block"; // CAMPO SEMESTRE
                cad_iden_semestre.required = true;

                campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
                cad_iden_nome_prof.required = true;

                campo_ident_tel.style.display = "block"; // CAMPO CONTATO
                cad_iden_contato.required = true;

                campo_ident_nome_atividade.style.display = "none"; // CAMPO NOME ATIVIDADE
                cad_iden_nome_ativ.required = false;
              } else {
                campo_ident_nome_atividade.style.display = "none"; // CAMPO NOME ATIVIDADE
                cad_iden_nome_ativ.required = false;

                campo_ident_select_nome_curso.style.display = "none"; // CAMPO NOME CURSO SELECT
                cad_iden_nome_curso.required = false;
                cad_iden_nome_curso.value = '';

                campo_ident_nome_curso.style.display = "none"; // CAMPO NOME CURSO
                cad_iden_nome_curso.required = false;

                campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
                cad_iden_nome_comp_ativ.required = false;

                campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
                cad_iden_semestre.required = false;

                campo_ident_prof.style.display = "none"; // CAMPO NOME PROFESSOR
                cad_iden_nome_prof.required = false;

                campo_ident_tel.style.display = "none"; // CAMPO CONTATO
                cad_iden_contato.required = false;
              }
            });
          });




          // CAMPO COMPONENTE CURRICULAR
          $(document).ready(function() {
            $('#cad_iden_comp_curricular').on('change', function() {
              if ($(this).val()) {
                campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
                cad_iden_nome_prof.required = true;

                campo_ident_tel.style.display = "block"; // CAMPO CONTATO
                cad_iden_contato.required = true;
              } else {
                campo_ident_prof.style.display = "none"; // CAMPO NOME PROFESSOR
                cad_iden_nome_prof.required = false;

                campo_ident_tel.style.display = "none"; // CAMPO CONTATO
                cad_iden_contato.required = false;
              }

              // SE 0: OUTRO
              if ($(this).val() === "0") {
                campo_ident_nome_comp_ativ.style.display = "block"; // CAMPO COMPONENTE ATIVIDADE
                cad_iden_nome_comp_ativ.required = true;

                campo_ident_semestre.style.display = "block"; // CAMPO SEMESTRE
                cad_iden_semestre.required = true;

                campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
                cad_iden_nome_prof.required = true;

                campo_ident_tel.style.display = "block"; // CAMPO CONTATO
                cad_iden_contato.required = true;
              } else {
                campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
                cad_iden_nome_comp_ativ.required = false;

                campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
                cad_iden_semestre.required = false;
              }
            });
          });

          // CAMPO NOME CURSO
          cad_iden_select_nome_curso.addEventListener("change", function() {
            if (cad_iden_select_nome_curso.value) {
              campo_ident_nome_atividade.style.display = "block";
              campo_ident_semestre.style.display = "block";
              campo_ident_prof.style.display = "block";
              campo_ident_tel.style.display = "block";

              document.getElementById("cad_iden_nome_ativ").required = true;
              document.getElementById("cad_iden_semestre").required = true;
              document.getElementById("cad_iden_nome_prof").required = true;
              document.getElementById("cad_iden_contato").required = true;
            } else {
              campo_ident_nome_atividade.style.display = "none";
              campo_ident_semestre.style.display = "none";
              campo_ident_prof.style.display = "none";
              campo_ident_tel.style.display = "none";

              document.getElementById("cad_iden_nome_ativ").required = false;
              document.getElementById("cad_iden_semestre").required = false;
              document.getElementById("cad_iden_nome_prof").required = false;
              document.getElementById("cad_iden_contato").required = false;
            }
          });
        });
      </script>

      <div class="col-lg-12">
        <div class="hstack gap-3 align-items-center justify-content-end mt-4">
          <p class="label_asterisco me-auto my-0"><span>*</span> Campo obrigatório</p>
          <button type="submit" class="btn botao botao_verde waves-effect">Próximo</button>
        </div>
      </div>


  </form>

</div>












<script>
  $(document).ready(function() {
    // Quando o curso for alterado
    $('#cad_ident_cursos').change(function() {
      var cursoId = $(this).val();
      if (cursoId !== "") {
        $.ajax({
          url: 'buscar_componentes.php',
          type: 'POST',
          data: {
            curso_id: cursoId
          },
          success: function(data) {
            $('#cad_iden_comp_curricular').html(data);
          }
        });
      } else {
        $('#cad_iden_comp_curricular').html('<option value="">Selecione um componente</option>');
      }
    });
  });
</script>