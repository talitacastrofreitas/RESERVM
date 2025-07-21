<div class="card-body p-sm-4 p-3">
  <form class="needs-validation form_solicitacao" method="POST" action="controller/controller_solicitacao.php" id="BotaoProgress" autocomplete="off" novalidate>

    <div class="row grid gx-3">

      <input type="hidden" class="form-control" name="solic_codigo" value="RE<?= str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT) ?>" required>
      <input type="hidden" class="form-control" name="solic_acao" value="cadastrar_1" required>
      <input type="hidden" class="form-control" name="solic_etapa" value="1" required>

      <div class="col-12">
        <div class="form_margem">
          <?php try {
            $sql = $conn->prepare("SELECT cta_id, cta_tipo_atividade FROM conf_tipo_atividade ORDER BY cta_id ASC");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            echo "Erro ao tentar recuperar os dados";
          } ?>
          <label class="form-label">Tipo de Atividade <span>*</span></label>
          <select class="form-select text-uppercase" name="solic_tipo_ativ" id="cad_solic_tipo_ativ" required>
            <option selected disabled value=""></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['cta_id'] ?>"><?= $res['cta_tipo_atividade'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_solic_curso" style="display: none;">
        <div class="form_margem">
          <?php try {
            $sql = $conn->prepare("SELECT curs_id, curs_curso FROM cursos WHERE curs_status != 0 ORDER BY curs_curso");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar os dados";
          } ?>
          <label class="form-label">Curso <span>*</span></label>
          <select class="form-select text-uppercase" name="solic_curso" id="cad_solic_curso">
            <option selected disabled value=""></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_solic_comp_curric" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Componente Curricular <span>*</span></label>
          <div class="label_info label_info_verde  mt-0">Os componentes curriculares estão ordenados por semestre e ordem alfabética.</div>
          <select class="form-select text-uppercase" name="solic_comp_curric" id="cad_solic_comp_curric">
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_solic_nome_curso" style="display: none;">
        <div class="form_margem">
          <?php try {
            $sql = $conn->prepare("SELECT cexc_id, cexc_curso FROM conf_cursos_extensao_curricularizada ORDER BY cexc_curso");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            echo "Erro ao tentar recuperar os dados";
          } ?>
          <label class="form-label">Nome do Curso <span>*</span></label>
          <select class="form-select text-uppercase" name="solic_nome_curso" id="cad_solic_nome_curso">
            <option selected disabled value=""></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['cexc_id'] ?>"><?= $res['cexc_curso'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_solic_nome_curso_text" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Nome do Curso <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="solic_nome_curso_text" id="cad_solic_nome_curso_text" maxlength="200">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_solic_nome_atividade" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Nome da Atividade <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="solic_nome_atividade" id="cad_solic_nome_atividade" maxlength="200">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12" id="campo_solic_nome_comp_ativ" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Nome do Componente/Atividade <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="solic_nome_comp_ativ" id="cad_solic_nome_comp_ativ" maxlength="200">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_solic_semestre" style="display: none;">
        <div class="form_margem">
          <?php try {
            $sql = $conn->prepare("SELECT * FROM conf_semestre ORDER BY cs_id ASC");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            echo "Erro ao tentar recuperar os dados";
          } ?>
          <label class="form-label">Semestre <span>*</span></label>
          <select class="form-select text-uppercase" name="solic_semestre" id="cad_solic_semestre">
            <option selected disabled value=""></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['cs_id'] ?>"><?= $res['cs_semestre'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_solic_nome_prof_resp" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Nome do Professor/Responsável <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="solic_nome_prof_resp" id="cad_solic_nome_prof_resp" maxlength="200">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6" id="campo_solic_contato" style="display: none;">
        <div class="form_margem">
          <label class="form-label">Telefone para contato <span>*</span></label>
          <input type="text" class="form-control cel_tel" name="solic_contato" id="cad_solic_contato">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

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
    function toggleFields() {
      var tipoAtiv = $('#cad_solic_tipo_ativ').val();
      var curso = $('#cad_solic_curso').val();
      var compCurric = $('#cad_solic_comp_curric').val();
      var nomeCurso = $('#cad_solic_nome_curso').val();

      $('[id^="campo_"]').hide().find('input, select').prop('required', false);

      if (tipoAtiv == '1') {
        // 1	ATIVIDADE ACADÊMICA
        $('#campo_solic_curso').show().find('#cad_solic_curso').prop('required', true);
      } else if (tipoAtiv == '2') {
        // 2	ATIVIDADE ADMINISTRATIVA
        $('[id^="campo_"]').hide();
        $('#campo_solic_nome_atividade, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input').prop('required', true);
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
        $('#campo_solic_comp_curric').show().find('#cad_solic_comp_curric').prop('required', true);
        // 0	OUTRO
        if (compCurric == '0') {
          $('#campo_solic_nome_comp_ativ, #campo_solic_semestre, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input, select').prop('required', true);
        } else if (compCurric) {
          $('#campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input').prop('required', true);
        }
      }

      if (curso == '8') {
        // 8	EXTENSÃO CURRICULARIZADA
        $('#campo_solic_nome_curso').show().find('#cad_solic_nome_curso').prop('required', true);
        if (nomeCurso) {
          $('#campo_solic_nome_atividade, #campo_solic_semestre, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input, select').prop('required', true);
        }
      }

      if ([7, 10, 19, 28].includes(parseInt(curso))) {
        // 7	EXTENSÃO
        // 10	GRUPO DE PESQUISA
        // 19	PROGRAMA CANDEAL
        // 28	NIDD
        $('#campo_solic_nome_atividade, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input').prop('required', true);
      }

      if ([11, 22].includes(parseInt(curso))) {
        // 11	LATO SENSU
        // 22	STRICTO SENSU
        $('#campo_solic_nome_curso_text, #campo_solic_nome_comp_ativ, #campo_solic_semestre, #campo_solic_nome_prof_resp, #campo_solic_contato').show().find('input, select').prop('required', true);
      }
    }

    $('#cad_solic_tipo_ativ, #cad_solic_curso, #cad_solic_comp_curric, #cad_solic_nome_curso, #campo_solic_nome_curso_text').change(function() {
      $('[id^="campo_"]').hide().find('input, select').prop('required', false);
      toggleFields();
    });

    toggleFields();
  });
</script>

<script>
  $(document).ready(function() {
    // Quando o curso for alterado
    $('#cad_solic_curso').change(function() {
      var cursoId = $(this).val();
      if (cursoId !== "") {
        $.ajax({
          url: 'buscar_componentes.php',
          type: 'POST',
          data: {
            curso_id: cursoId
          },
          success: function(data) {
            $('#cad_solic_comp_curric').html(data);
          }
        });
      } else {
        $('#cad_solic_comp_curric').html('<option value="">Selecione um componente</option>');
      }
    });
  });
</script>