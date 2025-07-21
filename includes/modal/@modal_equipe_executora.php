<!-- CADASTRAR EQUIPE EXECUTORA -->
<div class="modal fade modal_padrao" id="modal_cad_equipe_executora" tabindex="-1" aria-labelledby="modal_cad_equipe_executora" aria-modal="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_equipe_executora">Cadastrar Equipe Executora</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_equipe_executora.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control" name="pex_proposta_id" value="<?= $_GET['i'] ?>" required>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Nome Completo <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="pex_nome" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">E-mail <span>*</span></label>
              <input type="email" class="form-control text-lowercase" name="pex_email" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Contato <span>*</span></label>
              <input type="text" class="form-control cel_tel" name="pex_contato" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT cc_id, cc_curso FROM conf_cursos_coordenadores WHERE cc_id != 21 ORDER BY CASE WHEN cc_curso = 'OUTRO' THEN 1 ELSE 0 END, cc_curso");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Curso ou Área de Atuação <span>*</span></label>
              <select class="form-select text-uppercase" name="pex_area_atuacao" id="cad_pex_area_atuacao" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cc_id']; ?>"><?= $result_iten['cc_curso']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_cad_pex_nome_area_atuacao" style="display: none;">
              <label class="form-label">Nome do Curso Área de Atuação <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="pex_nome_area_atuacao" id="cad_pex_nome_area_atuacao" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const cad_pex_area_atuacao = document.getElementById("cad_pex_area_atuacao");
              const campo_cad_pex_nome_area_atuacao = document.getElementById("campo_cad_pex_nome_area_atuacao");

              cad_pex_area_atuacao.addEventListener("change", function() {
                if (cad_pex_area_atuacao.value === "20") {
                  campo_cad_pex_nome_area_atuacao.style.display = "block";
                  document.getElementById("cad_pex_nome_area_atuacao").required = true;
                } else {
                  campo_cad_pex_nome_area_atuacao.style.display = "none";
                  document.getElementById("cad_pex_nome_area_atuacao").required = false;
                  document.getElementById("cad_pex_nome_area_atuacao").value = '';
                }
              });
            </script>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT * FROM usuario_perfil ORDER BY CASE WHEN us_pe_perfil = 'OUTRO' THEN 1 ELSE 0 END, us_pe_perfil");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Perfil do Participante <span>*</span></label>
              <select class="form-select text-uppercase" name="pex_partic_perfil" id="cad_pex_partic_perfil" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['us_pe_id']; ?>"><?= $result_iten['us_pe_perfil']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_cad_pex_outro_partic_perfil" style="display: none;">
              <label class="form-label">Descreva o Perfil do Participante <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="pex_outro_partic_perfil" id="cad_pex_outro_partic_perfil" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const cad_pex_partic_perfil = document.getElementById("cad_pex_partic_perfil");
              const campo_cad_pex_outro_partic_perfil = document.getElementById("campo_cad_pex_outro_partic_perfil");

              cad_pex_partic_perfil.addEventListener("change", function() {
                if (cad_pex_partic_perfil.value === "9") {
                  campo_cad_pex_outro_partic_perfil.style.display = "block";
                  document.getElementById("cad_pex_outro_partic_perfil").required = true;
                } else {
                  campo_cad_pex_outro_partic_perfil.style.display = "none";
                  document.getElementById("cad_pex_outro_partic_perfil").required = false;
                  document.getElementById("cad_pex_outro_partic_perfil").value = '';
                }
              });
            </script>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT cpp_id, cpp_categoria FROM conf_categoria_participacao_projeto ORDER BY CASE WHEN cpp_categoria = 'OUTRO' THEN 1 ELSE 0 END, cpp_categoria");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Categoria de Participação no Projeto <span>*</span></label>
              <select class="form-select text-uppercase" name="pex_partic_categ" id="cad_pex_partic_categ" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cpp_id']; ?>"><?= $result_iten['cpp_categoria']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_cad_pex_qual_partic_categ" style="display: none;">
              <label class="form-label">Descreva a Categoria de Participação no Projeto <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="pex_qual_partic_categ" id="cad_pex_qual_partic_categ" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const cad_pex_partic_categ = document.getElementById("cad_pex_partic_categ");
              const campo_cad_pex_qual_partic_categ = document.getElementById("campo_cad_pex_qual_partic_categ");

              cad_pex_partic_categ.addEventListener("change", function() {
                if (cad_pex_partic_categ.value === "11") {
                  campo_cad_pex_qual_partic_categ.style.display = "block";
                  document.getElementById("cad_pex_qual_partic_categ").required = true;
                } else {
                  campo_cad_pex_qual_partic_categ.style.display = "none";
                  document.getElementById("cad_pex_qual_partic_categ").required = false;
                  document.getElementById("cad_pex_qual_partic_categ").value = '';
                }
              });
            </script>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Horas dedicadas ao projeto <span>*</span></label>
              <input type="text" class="form-control" name="pex_carga_hora" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Formação Profissional <span>*</span></label>
              <textarea class="form-control" name="pex_formacao" rows="3" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Link do Currículo Lattes</label>
              <input type="text" class="form-control" name="pex_lattes">
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadEquipeExec">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- EDITAR EQUIPE EXECUTORA -->
<div class="modal fade modal_padrao" id="modal_edit_equipe_executora" tabindex="-1" aria-labelledby="modal_edit_equipe_executora" aria-modal="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_equipe_executora">Cadastrar Equipe Executora</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_equipe_executora.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control pex_id" name="pex_id" required>
            <input type="hidden" class="form-control pex_proposta_id" name="pex_proposta_id" required>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Nome Completo <span>*</span></label>
              <input type="text" class="form-control text-uppercase pex_nome" name="pex_nome" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">E-mail <span>*</span></label>
              <input type="email" class="form-control text-lowercase pex_email" name="pex_email" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Contato <span>*</span></label>
              <input type="text" class="form-control cel_tel pex_contato" name="pex_contato" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT cc_id, cc_curso FROM conf_cursos_coordenadores WHERE cc_id != 21 ORDER BY CASE WHEN cc_curso = 'OUTRO' THEN 1 ELSE 0 END, cc_curso");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Curso ou Área de Atuação <span>*</span></label>
              <select class="form-select text-uppercase pex_area_atuacao" name="pex_area_atuacao" id="edit_pex_area_atuacao" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cc_id']; ?>"><?= $result_iten['cc_curso']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_edit_pex_nome_area_atuacao" style="display: none;">
              <label class="form-label">Nome do Curso Área de Atuação <span>*</span></label>
              <input type="text" class="form-control text-uppercase pex_nome_area_atuacao" name="pex_nome_area_atuacao" id="edit_pex_nome_area_atuacao" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const edit_pex_area_atuacao = document.getElementById("edit_pex_area_atuacao");
              const campo_edit_pex_nome_area_atuacao = document.getElementById("campo_edit_pex_nome_area_atuacao");

              edit_pex_area_atuacao.addEventListener("change", function() {
                if (edit_pex_area_atuacao.value === "20") {
                  campo_edit_pex_nome_area_atuacao.style.display = "block";
                  document.getElementById("edit_pex_nome_area_atuacao").required = true;
                } else {
                  campo_edit_pex_nome_area_atuacao.style.display = "none";
                  document.getElementById("edit_pex_nome_area_atuacao").required = false;
                  document.getElementById("edit_pex_nome_area_atuacao").value = '';
                }
              });

              if (edit_pex_area_atuacao.value === "20") {
                campo_edit_pex_nome_area_atuacao.style.display = "block";
                document.getElementById("edit_pex_nome_area_atuacao").required = true;
              }
            </script>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT * FROM usuario_perfil ORDER BY CASE WHEN us_pe_perfil = 'OUTRO' THEN 1 ELSE 0 END, us_pe_perfil");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Perfil do Participante <span>*</span></label>
              <select class="form-select text-uppercase pex_partic_perfil" name="pex_partic_perfil" id="edit_pex_partic_perfil" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['us_pe_id']; ?>"><?= $result_iten['us_pe_perfil']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_edit_pex_outro_partic_perfil" style="display: none;">
              <label class="form-label">Descreva o Perfil do Participante <span>*</span></label>
              <input type="text" class="form-control text-uppercase pex_outro_partic_perfil" name="pex_outro_partic_perfil" id="edit_pex_outro_partic_perfil" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const edit_pex_partic_perfil = document.getElementById("edit_pex_partic_perfil");
              const campo_edit_pex_outro_partic_perfil = document.getElementById("campo_edit_pex_outro_partic_perfil");

              edit_pex_partic_perfil.addEventListener("change", function() {
                if (edit_pex_partic_perfil.value === "9") {
                  campo_edit_pex_outro_partic_perfil.style.display = "block";
                  document.getElementById("edit_pex_outro_partic_perfil").required = true;
                } else {
                  campo_edit_pex_outro_partic_perfil.style.display = "none";
                  document.getElementById("edit_pex_outro_partic_perfil").required = false;
                  document.getElementById("edit_pex_outro_partic_perfil").value = '';
                }
              });

              if (edit_pex_partic_perfil.value === "9") {
                campo_edit_pex_outro_partic_perfil.style.display = "block";
                document.getElementById("edit_pex_outro_partic_perfil").required = true;
              }
            </script>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT cpp_id, cpp_categoria FROM conf_categoria_participacao_projeto ORDER BY CASE WHEN cpp_categoria = 'OUTRO' THEN 1 ELSE 0 END, cpp_categoria");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Categoria de Participação no Projeto <span>*</span></label>
              <select class="form-select text-uppercase pex_partic_categ" name="pex_partic_categ" id="edit_pex_partic_categ" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cpp_id']; ?>"><?= $result_iten['cpp_categoria']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_edit_pex_qual_partic_categ" style="display: none;">
              <label class="form-label">Descreva a Categoria de Participação no Projeto <span>*</span></label>
              <input type="text" class="form-control text-uppercase pex_qual_partic_categ" name="pex_qual_partic_categ" id="edit_pex_qual_partic_categ" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const edit_pex_partic_categ = document.getElementById("edit_pex_partic_categ");
              const campo_edit_pex_qual_partic_categ = document.getElementById("campo_edit_pex_qual_partic_categ");

              edit_pex_partic_categ.addEventListener("change", function() {
                if (edit_pex_partic_categ.value === "11") {
                  campo_edit_pex_qual_partic_categ.style.display = "block";
                  document.getElementById("edit_pex_qual_partic_categ").required = true;
                } else {
                  campo_edit_pex_qual_partic_categ.style.display = "none";
                  document.getElementById("edit_pex_qual_partic_categ").required = false;
                  document.getElementById("edit_pex_qual_partic_categ").value = '';
                }
              });
              if (edit_pex_partic_categ.value === "11") {
                campo_edit_pex_qual_partic_categ.style.display = "block";
                document.getElementById("edit_pex_qual_partic_categ").required = true;
              }
            </script>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Horas dedicadas ao projeto <span>*</span></label>
              <input type="text" class="form-control pex_carga_hora" name="pex_carga_hora" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Formação Profissional <span>*</span></label>
              <textarea class="form-control pex_formacao" name="pex_formacao" rows="3" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Link do Currículo Lattes</label>
              <input type="text" class="form-control pex_lattes" name="pex_lattes">
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditCoordProjeto">Atualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>


  <script>
    const modal_edit_equipe_executora = document.getElementById('modal_edit_equipe_executora')
    if (modal_edit_equipe_executora) {
      modal_edit_equipe_executora.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        // EXTRAI DADOS DO data-bs-* 
        const pex_id = button.getAttribute('data-bs-pex_id')
        const pex_proposta_id = button.getAttribute('data-bs-pex_proposta_id')
        const pex_nome = button.getAttribute('data-bs-pex_nome')
        const pex_email = button.getAttribute('data-bs-pex_email')
        const pex_contato = button.getAttribute('data-bs-pex_contato')
        const pex_partic_perfil = button.getAttribute('data-bs-pex_partic_perfil')
        const pex_outro_partic_perfil = button.getAttribute('data-bs-pex_outro_partic_perfil')
        const pex_partic_categ = button.getAttribute('data-bs-pex_partic_categ')
        const pex_qual_partic_categ = button.getAttribute('data-bs-pex_qual_partic_categ')
        const pex_carga_hora = button.getAttribute('data-bs-pex_carga_hora')
        const pex_area_atuacao = button.getAttribute('data-bs-pex_area_atuacao')
        const pex_nome_area_atuacao = button.getAttribute('data-bs-pex_nome_area_atuacao')
        const pex_formacao = button.getAttribute('data-bs-pex_formacao')
        const pex_lattes = button.getAttribute('data-bs-pex_lattes')
        // 
        const modalTitle = modal_edit_equipe_executora.querySelector('.modal-title')
        const modal_pex_id = modal_edit_equipe_executora.querySelector('.pex_id')
        const modal_pex_proposta_id = modal_edit_equipe_executora.querySelector('.pex_proposta_id')
        const modal_pex_nome = modal_edit_equipe_executora.querySelector('.pex_nome')
        const modal_pex_email = modal_edit_equipe_executora.querySelector('.pex_email')
        const modal_pex_contato = modal_edit_equipe_executora.querySelector('.pex_contato')
        const modal_pex_partic_perfil = modal_edit_equipe_executora.querySelector('.pex_partic_perfil')
        const modal_pex_outro_partic_perfil = modal_edit_equipe_executora.querySelector('.pex_outro_partic_perfil')
        const modal_pex_partic_categ = modal_edit_equipe_executora.querySelector('.pex_partic_categ')
        const modal_pex_qual_partic_categ = modal_edit_equipe_executora.querySelector('.pex_qual_partic_categ')
        const modal_pex_carga_hora = modal_edit_equipe_executora.querySelector('.pex_carga_hora')
        const modal_pex_area_atuacao = modal_edit_equipe_executora.querySelector('.pex_area_atuacao')
        const modal_pex_nome_area_atuacao = modal_edit_equipe_executora.querySelector('.pex_nome_area_atuacao')
        const modal_pex_formacao = modal_edit_equipe_executora.querySelector('.pex_formacao')
        const modal_pex_lattes = modal_edit_equipe_executora.querySelector('.pex_lattes')
        //
        modalTitle.textContent = 'Atualizar Dados'
        modal_pex_id.value = pex_id
        modal_pex_proposta_id.value = pex_proposta_id
        modal_pex_nome.value = pex_nome
        modal_pex_email.value = pex_email
        modal_pex_contato.value = pex_contato
        modal_pex_partic_perfil.value = pex_partic_perfil
        modal_pex_outro_partic_perfil.value = pex_outro_partic_perfil
        modal_pex_partic_categ.value = pex_partic_categ
        modal_pex_qual_partic_categ.value = pex_qual_partic_categ
        modal_pex_carga_hora.value = pex_carga_hora
        modal_pex_area_atuacao.value = pex_area_atuacao
        modal_pex_nome_area_atuacao.value = pex_nome_area_atuacao
        modal_pex_formacao.value = pex_formacao
        modal_pex_lattes.value = pex_lattes

        if (pex_area_atuacao === '20') {
          document.getElementById('campo_edit_pex_nome_area_atuacao').style.display = "block";
          document.getElementById('edit_pex_nome_area_atuacao').required = true;
        } else {
          document.getElementById('campo_edit_pex_nome_area_atuacao').style.display = "none";
          document.getElementById('edit_pex_nome_area_atuacao').required = false;
        }

        if (pex_partic_perfil === '9') {
          document.getElementById('campo_edit_pex_outro_partic_perfil').style.display = "block";
          document.getElementById('edit_pex_outro_partic_perfil').required = true;
        } else {
          document.getElementById('campo_edit_pex_outro_partic_perfil').style.display = "none";
          document.getElementById('edit_pex_outro_partic_perfil').required = false;
        }

        if (pex_partic_categ === '11') {
          document.getElementById('campo_edit_pex_qual_partic_categ').style.display = "block";
          document.getElementById('edit_pex_qual_partic_categ').required = true;
        } else {
          document.getElementById('campo_edit_pex_qual_partic_categ').style.display = "none";
          document.getElementById('edit_pex_qual_partic_categ').required = false;
        }
      })
    }
  </script>
</div>