<?php
// SE O CARD ESTIVER DESATIVADO E O USUÁRIO TENTAR ENTRAR PELA URL, RETORNA PARA A PÁGINA DOS CARDS
if (base64_decode($_GET['tp']) == 1 && $propc_status == 0) {
  header("Location: nova_proposta.php");
}
?>

<div class="card-body p-sm-4 p-3">

  <form class="needs-validation" method="POST" action="controller/controller_propostas.php?funcao=cad_step1" id="BotaoProgress" autocomplete="off" novalidate>

    <div class="row grid gx-3">

      <input type="hidden" class="form-control" name="prop_tipo" value="<?= base64_encode($propc_cat) ?>" required>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Título da Proposta <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="prop_titulo" maxlength="200" required>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-12 mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="prop_vinculo_programa" name="prop_vinculo_programa" value="1">
          <label class="form-check-label" for="prop_vinculo_programa">A atividade é vinculada a um programa institucional ou a um componente curricular</label>
        </div>
      </div>

      <div class="col-12" id="campo_vinculo_programa" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Qual? <span>*</span></label>
          <textarea class="form-control" name="prop_qual_vinculo_programa" id="prop_qual_vinculo_programa" rows="3"></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <script>
        var prop_vinculo_programa = document.getElementById("prop_vinculo_programa");
        var campo_vinculo_programa = document.getElementById("campo_vinculo_programa");

        prop_vinculo_programa.addEventListener("change", function() {
          if (prop_vinculo_programa.checked) {
            campo_vinculo_programa.style.display = "block";
            document.getElementById('prop_qual_vinculo_programa').required = true;
          } else {
            campo_vinculo_programa.style.display = "none";
            document.getElementById('prop_qual_vinculo_programa').required = false;
          }
        });
      </script>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Descrição da Atividade <span>*</span><label class="text-muted fst-italic ms-1">(Faça um preâmbulo sobre seu projeto)</label></label>
          <textarea class="form-control" name="prop_descricao" rows="3" required></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="mb-3">
          <?php try {
            $sql = $conn->query("SELECT cc_id, cc_curso FROM conf_cursos_coordenadores ORDER BY CASE WHEN cc_curso = 'OUTRO' THEN 1 WHEN cc_curso = 'NÃO POSSUO VÍNCULO' THEN 2 ELSE 0 END, cc_curso");
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar o perfil";
          } ?>
          <label class="form-label">Qual curso/atividade você está vinculado? <span>*</span></label>
          <select class="form-select" name="prop_curso_vinculo" id="prop_curso_vinculo" required>
            <option selected disabled></option>
            <?php foreach ($result as $res) : ?>
              <option value="<?= $res['cc_id'] ?>"><?= $res['cc_curso'] ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6 col-lg-8" id="campo_prop_nome_curso_vinculo" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Nome do curso/atividade <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="prop_nome_curso_vinculo" id="prop_nome_curso_vinculo" value="">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <script>
        const prop_curso_vinculo = document.getElementById("prop_curso_vinculo");
        const campo_prop_nome_curso_vinculo = document.getElementById("campo_prop_nome_curso_vinculo");

        prop_curso_vinculo.addEventListener("change", function() {
          if (prop_curso_vinculo.value === "20") {
            campo_prop_nome_curso_vinculo.style.display = "block";
            document.getElementById("prop_nome_curso_vinculo").required = true;
          } else {
            campo_prop_nome_curso_vinculo.style.display = "none";
            document.getElementById("prop_nome_curso_vinculo").required = false;
          }
        });

        if (prop_curso_vinculo.value === "20") {
          campo_prop_nome_curso_vinculo.style.display = "block";
          document.getElementById("prop_nome_curso_vinculo").required = true;
        }
      </script>

    </div>

    <div class="tit_section">
      <h3>Justificativa</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Justificativa <span>*</span></label>
          <textarea class="form-control" name="prop_justificativa" rows="3" required></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

    </div>

    <div class="tit_section">
      <h3>Objetivos Pedagógicos</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Objetivos <span>*</span></label>
          <textarea class="form-control" name="prop_obj_pedagogico" rows="3" required></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

    </div>

    <div class="tit_section">
      <h3>Público Alvo</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Público Alvo <span>*</span></label>
          <textarea class="form-control" name="prop_publico_alvo" rows="3" required></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

    </div>

    <div class="tit_section">
      <h3>Área do Conhecimento</h3>
    </div>

    <div class="row">
      <div class="col-12 mb-3">
        <?php try {
          $sql = $conn->query("SELECT ac_id, ac_area_conhecimento FROM conf_areas_conhecimento ORDER BY ac_area_conhecimento");
          $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
          // echo "Erro: " . $e->getMessage();
          echo "Erro ao tentar recuperar o perfil";
        } ?>
        <!-- <label class="form-label">Área do Conhecimento <span>*</span></label> -->
        <div class="check_item_container">
          <?php foreach ($result as $res) : ?>
            <input type="checkbox" class="btn-check check_formulario_check" name="prop_area_conhecimento[]" id="checkConhecimento<?= $res['ac_id'] ?>" value="<?= $res['ac_id'] ?>">
            <label class="check_item check_formulario" for="checkConhecimento<?= $res['ac_id'] ?>"><?= $res['ac_area_conhecimento'] ?></label>
          <?php endforeach; ?>
          <div id="msgCheckConhecimento" class="invalid-feedback">
            Selecione pelo menos uma opção
          </div>
        </div>
      </div>
    </div>

    <div class="tit_section">
      <h3>Área Temática</h3>
    </div>

    <div class="row">
      <div class="col-12 mb-3">
        <?php try {
          $sql = $conn->query("SELECT at_id, at_area_tematica FROM conf_areas_tematicas ORDER BY at_area_tematica");
          $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
          // echo "Erro: " . $e->getMessage();
          echo "Erro ao tentar recuperar o perfil";
        } ?>
        <!-- <label class="form-label">Área do Conhecimento <span>*</span></label> -->
        <div class="check_item_container">
          <?php foreach ($result as $res) : ?>
            <input type="checkbox" class="btn-check" name="prop_area_tematica[]" id="checkATematica<?= $res['at_id'] ?>" value="<?= $res['at_id'] ?>">
            <label class="check_item" for="checkATematica<?= $res['at_id'] ?>"><?= $res['at_area_tematica'] ?></label>
          <?php endforeach; ?>
          <div id="msgCheckATematica" class="invalid-feedback">
            Selecione pelo menos uma opção
          </div>
        </div>
      </div>
    </div>

    <div class="tit_section">
      <h3>Informações Complementares</h3>
    </div>

    <div class="row grid gx-3">
      <div class="col-12 mb-3">
        <?php try {
          $sql = $conn->query("SELECT week_id, week_dias FROM dias_semana ORDER BY week_id");
          $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
          // echo "Erro: " . $e->getMessage();
          echo "Erro ao tentar recuperar o perfil";
        } ?>
        <label class="form-label m-0">Dias da Semana <span>*</span></label>
        <div class="check_item_container">
          <?php foreach ($result as $res) : ?>
            <input type="checkbox" class="btn-check" name="prop_semana[]" id="checkSemana<?= $res['week_id'] ?>" value="<?= $res['week_id'] ?>">
            <label class="check_item" for="checkSemana<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></label>
          <?php endforeach; ?>
          <div id="msgCheckDSemana" class="invalid-feedback">
            Selecione pelo menos uma opção
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Horários <span>*</span></label>
          <textarea class="form-control" name="prop_horario" rows="3" required></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="mb-3">
          <label class="form-label">Data prevista para início <span>*</span></label>
          <input type="date" class="form-control" name="prop_data_inicio" id="prop_data_inicio" onblur="checkDateInicio()" required>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
        <script>
          // function checkDateInicio() {
          //   var prop_data_inicio = document.getElementById("prop_data_inicio").value;
          //   var dataAtualInicio = new Date().toISOString().slice(0, 10);

          //   if (prop_data_inicio < dataAtualInicio) {
          //     Swal.fire({
          //       title: 'Data Inválida!',
          //       text: 'A data inicial não pode ser menor que a data de hoje.',
          //       icon: 'error',
          //       confirmButtonText: 'OK'
          //     }).then((result) => {
          //       document.getElementById("prop_data_inicio").focus(); // Retorna o foco ao campo de entrada
          //     });
          //     document.getElementById('prop_data_inicio').value = "";
          //     return false;
          //   }
          // }

          document.getElementById('prop_data_inicio').addEventListener('blur', function() {
            // Obtém a data atual
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Define a hora para 00:00:00 para comparação correta

            // Obtém a data do input
            const inputDate = new Date(this.value);

            // Verifica se a data foi informada e se é menor que a data atual
            if (this.value && inputDate < today) {
              // Exibe o alerta
              Swal.fire({
                icon: 'error',
                title: 'Data inválida',
                text: 'A data informada não pode ser menor que a data atual.'
              });

              // Apaga a data informada
              this.value = '';
            }
          });
        </script>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="mb-3">
          <label class="form-label">Data prevista para finalização <span>*</span></label>
          <input type="date" class="form-control" name="prop_data_fim" id="prop_data_fim" onblur="checkDateFinal()" required>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
        <script>
          // function checkDateFinal() {
          //   var prop_data_fim = document.getElementById("prop_data_fim").value;
          //   var dataAtualFim = new Date().toISOString().slice(0, 10);

          //   if (prop_data_fim < dataAtualFim) {
          //     Swal.fire({
          //       title: 'Data Inválida!',
          //       text: 'A data final não pode ser menor que a data atual.',
          //       icon: 'error',
          //       confirmButtonText: 'OK'
          //     }).then((result) => {
          //       document.getElementById("prop_data_fim").focus(); // Retorna o foco ao campo de entrada
          //     });
          //     document.getElementById('prop_data_fim').value = "";
          //     return false;
          //   }
          // }

          document.getElementById('prop_data_fim').addEventListener('blur', function() {
            // Obtém a data atual
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Define a hora para 00:00:00 para comparação correta

            // Obtém a data do input
            const inputDate = new Date(this.value);

            // Verifica se a data foi informada e se é menor que a data atual
            if (this.value && inputDate < today) {
              // Exibe o alerta
              Swal.fire({
                icon: 'error',
                title: 'Data inválida',
                text: 'A data informada não pode ser menor que a data atual.'
              });

              // Apaga a data informada
              this.value = '';
            }
          });
          ////
          const dataInicioInput = document.getElementById('prop_data_inicio');
          const dataFimInput = document.getElementById('prop_data_fim');

          dataInicioInput.addEventListener('change', verificarDatas);
          dataFimInput.addEventListener('change', verificarDatas);

          function verificarDatas() {
            const dataInicio = new Date(dataInicioInput.value);
            const dataFim = new Date(dataFimInput.value);

            if (dataInicio > dataFim) {
              Swal.fire({
                icon: 'error',
                title: 'Data Inválida!',
                text: 'A data de início não pode ser maior que a data de finalização.',
                confirmButtonText: 'OK'
              }).then((result) => {
                document.getElementById("prop_data_inicio").focus(); // Retorna o foco ao campo de entrada
              });
              document.getElementById('prop_data_inicio').value = "";
              document.getElementById('prop_data_fim').value = "";
              return false;
            }
          }
        </script>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="mb-3">
          <label class="form-label">Carga horária (Horas) <span>*</span></label>
          <input type="text" class="form-control" name="prop_carga_hora" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" placeholder="Horas" required>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="mb-3">
          <label class="form-label">Quant. total de vagas ofertadas <span>*</span></label>
          <input type="text" class="form-control" name="prop_total_vaga" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <?php
      // ESTE CAMPO SÓ DEVE APARECER PARA AS CURSOS = 1, E EVENTOS CIENTÍFICOS = 2
      $cat_propostas = array(1, 2);
      if (in_array(base64_decode($_GET['tp']), $cat_propostas)) { ?>
        <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
          <div class="mb-3">
            <label class="form-label">Quant. mínima de inscritos <span>*</span></label>
            <input type="text" class="form-control" name="prop_quant_turma" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>
      <?php } ?>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="mb-3">
          <?php try {
            $stmt = $conn->query("SELECT mod_en_id, mod_en_modalidade FROM modalidade_encontro ORDER BY mod_en_modalidade");
            $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar materiais";
          } ?>
          <label class="form-label">Modalidade do Encontro <span>*</span></label>
          <select class="form-select text-uppercase" name="prop_modalidade" id="prop_modalidade" required>
            <option selected disabled value=""></option>
            <?php foreach ($result_itens as $result_iten) : ?>
              <option value="<?= $result_iten['mod_en_id']; ?>"><?= $result_iten['mod_en_modalidade']; ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2" id="campo_prop_campus" style="display: none;">
        <div class="mb-3">
          <?php try {
            $stmt = $conn->query("SELECT uni_id, uni_unidade FROM unidades ORDER BY CASE WHEN uni_unidade = 'OUTRO' THEN 1 ELSE 0 END, uni_unidade");
            $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar materiais";
          } ?>
          <label class="form-label">Campus <span>*</span></label>
          <select class="form-select text-uppercase" name="prop_campus" id="prop_campus" required>
            <option selected disabled value=""></option>
            <?php foreach ($result_itens as $result_iten) : ?>
              <option value="<?= $result_iten['uni_id']; ?>"><?= $result_iten['uni_unidade']; ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2" id="campo_prop_local" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Local <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="prop_local" id="prop_local" maxlength="100" required>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <script>
        const prop_modalidade = document.getElementById("prop_modalidade");
        const campo_prop_campus = document.getElementById("campo_prop_campus");
        const prop_campus = document.getElementById("prop_campus");
        const campo_prop_local = document.getElementById("campo_prop_local");

        prop_modalidade.addEventListener("change", function() {
          if (prop_modalidade.value === "3" || prop_modalidade.value === "4") {
            campo_prop_campus.style.display = "block";
            document.getElementById("prop_campus").required = true;
          } else {
            campo_prop_campus.style.display = "none";
            document.getElementById("prop_campus").required = false;

            campo_prop_local.style.display = "none";
            document.getElementById("prop_local").required = false;
          }
        });

        if (prop_modalidade.value === "3" || prop_modalidade.value === "4") {
          campo_prop_campus.style.display = "block";
          document.getElementById("prop_campus").required = true;
        }

        prop_campus.addEventListener("change", function() {
          if (prop_campus.value === "1") {
            campo_prop_local.style.display = "block";
            document.getElementById("prop_local").required = true;
          } else {
            campo_prop_local.style.display = "none";
            document.getElementById("prop_local").required = false;
          }
        });

        if (prop_campus.value === "1") {
          campo_prop_local.style.display = "block";
          document.getElementById("prop_local").required = true;
        }
      </script>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2">
        <div class="mb-3">
          <?php try {
            $stmt = $conn->query("SELECT for_acess_id, for_acess_forma_acesso FROM forma_acesso ORDER BY CASE WHEN for_acess_forma_acesso = 'OUTRO' THEN 1 ELSE 0 END, for_acess_forma_acesso");
            $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar materiais";
          } ?>
          <label class="form-label">Forma de acesso dos participantes <span>*</span></label>
          <select class="form-select text-uppercase" name="prop_forma_acesso" id="prop_forma_acesso" required>
            <option selected disabled value=""></option>
            <?php foreach ($result_itens as $result_iten) : ?>
              <option value="<?= $result_iten['for_acess_id']; ?>"><?= $result_iten['for_acess_forma_acesso']; ?></option>
            <?php endforeach; ?>
          </select>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2" id="campo_prop_preco" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Valor pretendido</label>
          <input type="text" class="form-control money" name="prop_preco" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" placeholder="R$">
        </div>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2" id="campo_prop_preco_parcelas" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Quantidade de parcelas</label>
          <input type="text" class="form-control" name="prop_preco_parcelas" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10">
        </div>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-4" id="campo_prop_outra_forma_acesso" style="display: none;">
        <div class="mb-3">
          <label class="form-label">Descreva o acesso dos participantes <span>*</span></label>
          <input type="text" class="form-control text-uppercase" name="prop_outra_forma_acesso" id="prop_outra_forma_acesso" maxlength="50">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
      </div>

      <script>
        const prop_forma_acesso = document.getElementById("prop_forma_acesso");
        const campo_prop_preco = document.getElementById("campo_prop_preco");
        const campo_prop_preco_parcelas = document.getElementById("campo_prop_preco_parcelas");
        const campo_prop_outra_forma_acesso = document.getElementById("campo_prop_outra_forma_acesso");

        prop_forma_acesso.addEventListener("change", function() {
          if (prop_forma_acesso.value === "4" || prop_forma_acesso.value === "3") {
            campo_prop_preco.style.display = "block";
          } else {
            campo_prop_preco.style.display = "none";
          }

          if (prop_forma_acesso.value === "3") {
            campo_prop_preco_parcelas.style.display = "block";
          } else {
            campo_prop_preco_parcelas.style.display = "none";
          }

          if (prop_forma_acesso.value === "1") {
            campo_prop_outra_forma_acesso.style.display = "block";
            document.getElementById('prop_outra_forma_acesso').required = true;
          } else {
            campo_prop_outra_forma_acesso.style.display = "none";
            document.getElementById('prop_outra_forma_acesso').required = false;
          }
        });
      </script>

    </div>

    <div class="tit_section">
      <h3>Ações de Acessibilidade Vinculadas ao Projeto</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12 mb-2">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="prop_acao_acessibilidade" id="prop_acao_acessibilidade" value="1">
          <label class="form-check-label" for="prop_acao_acessibilidade">O projeto propõe alguma ação de acessibilidade para públicos específicos (pessoas pretas, mulheres, crianças, PCD, LGBTQIA+, quilombolas)</label>
        </div>
      </div>

      <div class="col-12 mt-2" id="campo_prop_desc_acao_acessibilidade" style="display: none;">
        <div class="mb-4">
          <label class="form-label">Descreva <span>*</span></label>
          <textarea class="form-control" name="prop_desc_acao_acessibilidade" id="prop_desc_acao_acessibilidade" rows="3"></textarea>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
        <script>
          // MOSTRA CAMPO QUANDO CLICAR NO CHECKBOX
          var prop_acao_acessibilidade = document.getElementById("prop_acao_acessibilidade");
          var campo_prop_desc_acao_acessibilidade = document.getElementById("campo_prop_desc_acao_acessibilidade");

          prop_acao_acessibilidade.addEventListener("change", function() {
            if (prop_acao_acessibilidade.checked) {
              campo_prop_desc_acao_acessibilidade.style.display = "block"; // Mostrar o campo de entrada
              document.getElementById('prop_desc_acao_acessibilidade').required = true;
            } else {
              campo_prop_desc_acao_acessibilidade.style.display = "none"; // Ocultar o campo de entrada
              document.getElementById('prop_desc_acao_acessibilidade').required = false;
            }
          });
        </script>
      </div>

      <div class="col-12 mb-2">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="prop_ofertas_vagas" name="prop_ofertas_vagas" value="1">
          <label class="form-check-label" for="prop_ofertas_vagas">Serão ofertadas vagas</label>
        </div>
      </div>

      <div class="col-md-6 col-lg-4 col-xl-3 col-xxl-2 mt-2" id="campo_prop_quant_beneficios" style="display: none;">
        <div class="mb-4">
          <label class="form-label">Quantidade prevista <span>*</span></label>
          <input type="text" class="form-control" name="prop_quant_beneficios" id="prop_quant_beneficios" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10">
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>
        <script>
          // MOSTRA CAMPO QUANDO CLICAR NO CHECKBOX
          var prop_ofertas_vagas = document.getElementById("prop_ofertas_vagas");
          var campo_prop_quant_beneficios = document.getElementById("campo_prop_quant_beneficios");

          prop_ofertas_vagas.addEventListener("change", function() {
            if (prop_ofertas_vagas.checked) {
              campo_prop_quant_beneficios.style.display = "block"; // Mostrar o campo de entrada
              document.getElementById('prop_quant_beneficios').required = true;
            } else {
              campo_prop_quant_beneficios.style.display = "none"; // Ocultar o campo de entrada
              document.getElementById('prop_quant_beneficios').required = false;
            }
          });
        </script>
      </div>

      <div class="col-12 mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="prop_atendimento_doacao" name="prop_atendimento_doacao" value="1">
          <label class="form-check-label" for="prop_atendimento_doacao">Serão realizados atendimentos/doações</label>
        </div>
      </div>

      <div id="campo_atendimentos" style="display: none;">

        <div class="col-12">
          <div class="mb-3">
            <label class="form-label">Descrição do público que será atendido <span>*</span></label>
            <textarea class="form-control" name="prop_desc_beneficios" id="prop_desc_beneficios" rows="3"></textarea>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12">
          <div class="mb-3">
            <label class="form-label">Informe qual é a instituição ou comunidade atendida <span>*</span></label>
            <textarea class="form-control" name="prop_comunidade" id="prop_comunidade" rows="3"></textarea>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12">
          <div class="mb-3">
            <label class="form-label">Localidade/Terrítório <span>*</span></label>
            <textarea class="form-control" name="prop_localidade" id="prop_localidade" rows="3"></textarea>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12">
          <div class="mb-3">
            <label class="form-label">Responsável na comunidade/instituição/território <span>*</span></label>
            <textarea class="form-control" name="prop_responsavel" id="prop_responsavel" rows="3"></textarea>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12">
          <div class="mb-3">
            <label class="form-label">Contato do Responsável <span>*</span></label>
            <textarea class="form-control" name="prop_responsavel_contato" id="prop_responsavel_contato" rows="3"></textarea>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <script>
          // MOSTRA CAMPO QUANDO CLICAR NO CHECKBOX
          var prop_atendimento_doacao = document.getElementById("prop_atendimento_doacao");
          var campo_atendimentos = document.getElementById("campo_atendimentos");

          prop_atendimento_doacao.addEventListener("change", function() {
            if (prop_atendimento_doacao.checked) {
              campo_atendimentos.style.display = "block"; // Mostrar o campo de entrada
              document.getElementById('prop_desc_beneficios').required = true;
              document.getElementById('prop_comunidade').required = true;
              document.getElementById('prop_localidade').required = true;
              document.getElementById('prop_responsavel').required = true;
              document.getElementById('prop_responsavel_contato').required = true;
            } else {
              campo_atendimentos.style.display = "none"; // Ocultar o campo de entrada
              document.getElementById('prop_desc_beneficios').required = false;
              document.getElementById('prop_comunidade').required = false;
              document.getElementById('prop_localidade').required = false;
              document.getElementById('prop_responsavel').required = false;
              document.getElementById('prop_responsavel_contato').required = false;
            }
          });
        </script>

      </div>

    </div>

    <div class="tit_section">
      <h3>Mais Informações</h3>
    </div>

    <div class="row grid gx-3">
      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Comentários e solicitações complementares</label>
          <textarea class="form-control" name="prop_info_complementar" id="" rows="3"></textarea>
        </div>
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