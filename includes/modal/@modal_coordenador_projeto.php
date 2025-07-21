<!-- CADASTRAR COORDENADOR PROJETO -->
<div class="modal fade modal_padrao" id="modal_cad_coordenador_projeto" tabindex="-1" aria-labelledby="modal_cad_coordenador_projeto" aria-modal="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_coordenador_projeto">Cadastrar Coordenador(a) do Projeto</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_coordenador_projeto.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control" name="pcp_proposta_id" value="<?= $_GET['i'] ?>" required>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Nome Completo <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="pcp_nome" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">E-mail <span>*</span></label>
              <input type="email" class="form-control text-lowercase" name="pcp_email" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Contato <span>*</span></label>
              <input type="text" class="form-control cel_tel" name="pcp_contato" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT * FROM usuario_perfil ORDER BY CASE WHEN us_pe_perfil = 'OUTRO' THEN 1 ELSE 0 END, us_pe_perfil");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Perfil do Participante <span>*</span></label>
              <select class="form-select text-uppercase" name="pcp_partic_perfil" id="cad_pcp_perfil_participante" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['us_pe_id']; ?>"><?= $result_iten['us_pe_perfil']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_cad_pcp_outro_partic_perfil" style="display: none;">
              <label class="form-label">Descreva o Perfil <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="pcp_outro_partic_perfil" id="pcp_outro_partic_perfil" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const cad_pcp_perfil_participante = document.getElementById("cad_pcp_perfil_participante");
              const campo_cad_pcp_outro_partic_perfil = document.getElementById("campo_cad_pcp_outro_partic_perfil");

              cad_pcp_perfil_participante.addEventListener("change", function() {
                if (cad_pcp_perfil_participante.value === "9") {
                  campo_cad_pcp_outro_partic_perfil.style.display = "block";
                  document.getElementById("pcp_outro_partic_perfil").required = true;
                } else {
                  campo_cad_pcp_outro_partic_perfil.style.display = "none";
                  document.getElementById("pcp_outro_partic_perfil").required = false;
                  document.getElementById("pcp_outro_partic_perfil").value = '';
                }
              });
            </script>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT cc_id, cc_curso FROM conf_cursos_coordenadores WHERE cc_id != 21 ORDER BY CASE WHEN cc_curso = 'OUTRO' THEN 1 ELSE 0 END, cc_curso");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Curso ou Área de Atuação <span>*</span></label>
              <select class="form-select text-uppercase" name="pcp_area_atuacao" id="cad_pcp_area_atuacao" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cc_id']; ?>"><?= $result_iten['cc_curso']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_cad_pcp_nome_area_atuacao" style="display: none;">
              <label class="form-label">Nome do curso área de atuação <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="pcp_nome_area_atuacao" id="cad_pcp_nome_area_atuacao" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const cad_pcp_area_atuacao = document.getElementById("cad_pcp_area_atuacao");
              const campo_cad_pcp_nome_area_atuacao = document.getElementById("campo_cad_pcp_nome_area_atuacao");

              cad_pcp_area_atuacao.addEventListener("change", function() {
                if (cad_pcp_area_atuacao.value === "20") {
                  campo_cad_pcp_nome_area_atuacao.style.display = "block";
                  document.getElementById("cad_pcp_nome_area_atuacao").required = true;
                } else {
                  campo_cad_pcp_nome_area_atuacao.style.display = "none";
                  document.getElementById("cad_pcp_nome_area_atuacao").required = false;
                  document.getElementById("cad_pcp_nome_area_atuacao").value = '';
                }
              });
            </script>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Horas dedicadas ao projeto <span>*</span></label>
              <input type="text" class="form-control" name="pcp_carga_hora" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Formação Profissional <span>*</span></label>
              <textarea class="form-control" name="pcp_formacao" rows="3" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Link do Currículo Lattes</label>
              <input type="text" class="form-control" name="pcp_lattes">
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadCoordProjeto">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- EDITAR COORDENADOR PROJETO -->
<div class="modal fade modal_padrao" id="modal_edit_coordenador_projeto" tabindex="-1" aria-labelledby="modal_edit_coordenador_projeto" aria-modal="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_coordenador_projeto">Cadastrar Coordenador(a) do Projeto</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_coordenador_projeto.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control pcp_id" name="pcp_id" required>
            <input type="hidden" class="form-control pcp_proposta_id" name="pcp_proposta_id" required>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Nome Completo <span>*</span></label>
              <input type="text" class="form-control text-uppercase pcp_nome" name="pcp_nome" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">E-mail <span>*</span></label>
              <input type="email" class="form-control text-lowercase pcp_email" name="pcp_email" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Contato <span>*</span></label>
              <input type="text" class="form-control cel_tel pcp_contato" name="pcp_contato" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT * FROM usuario_perfil ORDER BY CASE WHEN us_pe_perfil = 'OUTRO' THEN 1 ELSE 0 END, us_pe_perfil");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Perfil do Participante <span>*</span></label>
              <select class="form-select text-uppercase pcp_partic_perfil" name="pcp_partic_perfil" id="edit_pcp_perfil_participante" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['us_pe_id']; ?>"><?= $result_iten['us_pe_perfil']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_edit_pcp_outro_partic_perfil" style="display: none;">
              <label class="form-label">Descreva o Perfil <span>*</span></label>
              <input type="text" class="form-control text-uppercase pcp_outro_partic_perfil" name="pcp_outro_partic_perfil" id="edit_pcp_outro_partic_perfil" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const edit_pcp_perfil_participante = document.getElementById("edit_pcp_perfil_participante");
              const campo_edit_pcp_outro_partic_perfil = document.getElementById("campo_edit_pcp_outro_partic_perfil");

              edit_pcp_perfil_participante.addEventListener("change", function() {
                if (edit_pcp_perfil_participante.value === "9") {
                  campo_edit_pcp_outro_partic_perfil.style.display = "block";
                  document.getElementById("edit_pcp_outro_partic_perfil").required = true;
                } else {
                  campo_edit_pcp_outro_partic_perfil.style.display = "none";
                  document.getElementById("edit_pcp_outro_partic_perfil").required = false;
                  document.getElementById("edit_pcp_outro_partic_perfil").value = '';
                }
              });

              if (edit_pcp_perfil_participante.value === "9") {
                campo_edit_pcp_outro_partic_perfil.style.display = "block";
                document.getElementById("edit_pcp_outro_partic_perfil").required = true;
              }
            </script>

            <div class="col-lg-6 col-xl-4">
              <?php try {
                $stmt = $conn->query("SELECT cc_id, cc_curso FROM conf_cursos_coordenadores WHERE cc_id != 21 ORDER BY CASE WHEN cc_curso = 'OUTRO' THEN 1 ELSE 0 END, cc_curso");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Curso ou Área de Atuação <span>*</span></label>
              <select class="form-select text-uppercase pcp_area_atuacao" name="pcp_area_atuacao" id="edit_pcp_area_atuacao" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['cc_id']; ?>"><?= $result_iten['cc_curso']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-4" id="campo_edit_pcp_nome_area_atuacao" style="display: none;">
              <label class="form-label">Nome do curso área de atuação <span>*</span></label>
              <input type="text" class="form-control text-uppercase pcp_nome_area_atuacao" name="pcp_nome_area_atuacao" id="edit_pcp_nome_area_atuacao" maxlength="50">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              const edit_pcp_area_atuacao = document.getElementById("edit_pcp_area_atuacao");
              const campo_edit_pcp_nome_area_atuacao = document.getElementById("campo_edit_pcp_nome_area_atuacao");

              edit_pcp_area_atuacao.addEventListener("change", function() {
                if (edit_pcp_area_atuacao.value === "20") {
                  campo_edit_pcp_nome_area_atuacao.style.display = "block";
                  document.getElementById("edit_pcp_nome_area_atuacao").required = true;
                } else {
                  campo_edit_pcp_nome_area_atuacao.style.display = "none";
                  document.getElementById("edit_pcp_nome_area_atuacao").required = false;
                  document.getElementById("edit_pcp_nome_area_atuacao").value = '';
                }
              });

              if (edit_pcp_area_atuacao.value === "20") {
                campo_edit_pcp_nome_area_atuacao.style.display = "block";
                document.getElementById("edit_pcp_nome_area_atuacao").required = true;
              }
            </script>

            <div class="col-lg-6 col-xl-4">
              <label class="form-label">Horas dedicadas ao projeto <span>*</span></label>
              <input type="text" class="form-control pcp_carga_hora" name="pcp_carga_hora" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Formação Profissional <span>*</span></label>
              <textarea class="form-control pcp_formacao" name="pcp_formacao" rows="3" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Link do Currículo Lattes</label>
              <input type="text" class="form-control pcp_lattes" name="pcp_lattes">
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
    const modal_edit_coordenador_projeto = document.getElementById('modal_edit_coordenador_projeto')
    if (modal_edit_coordenador_projeto) {
      modal_edit_coordenador_projeto.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        // EXTRAI DADOS DO data-bs-* 
        const pcp_id = button.getAttribute('data-bs-pcp_id')
        const pcp_proposta_id = button.getAttribute('data-bs-pcp_proposta_id')
        const pcp_nome = button.getAttribute('data-bs-pcp_nome')
        const pcp_email = button.getAttribute('data-bs-pcp_email')
        const pcp_contato = button.getAttribute('data-bs-pcp_contato')
        const pcp_partic_perfil = button.getAttribute('data-bs-pcp_partic_perfil')
        const pcp_outro_partic_perfil = button.getAttribute('data-bs-pcp_outro_partic_perfil')
        const pcp_carga_hora = button.getAttribute('data-bs-pcp_carga_hora')
        const pcp_area_atuacao = button.getAttribute('data-bs-pcp_area_atuacao')
        const pcp_nome_area_atuacao = button.getAttribute('data-bs-pcp_nome_area_atuacao')
        const pcp_formacao = button.getAttribute('data-bs-pcp_formacao')
        const pcp_lattes = button.getAttribute('data-bs-pcp_lattes')
        // 
        const modalTitle = modal_edit_coordenador_projeto.querySelector('.modal-title')
        const modal_pcp_id = modal_edit_coordenador_projeto.querySelector('.pcp_id')
        const modal_pcp_proposta_id = modal_edit_coordenador_projeto.querySelector('.pcp_proposta_id')
        const modal_pcp_nome = modal_edit_coordenador_projeto.querySelector('.pcp_nome')
        const modal_pcp_email = modal_edit_coordenador_projeto.querySelector('.pcp_email')
        const modal_pcp_contato = modal_edit_coordenador_projeto.querySelector('.pcp_contato')
        const modal_pcp_partic_perfil = modal_edit_coordenador_projeto.querySelector('.pcp_partic_perfil')
        const modal_pcp_outro_partic_perfil = modal_edit_coordenador_projeto.querySelector('.pcp_outro_partic_perfil')
        const modal_pcp_carga_hora = modal_edit_coordenador_projeto.querySelector('.pcp_carga_hora')
        const modal_pcp_area_atuacao = modal_edit_coordenador_projeto.querySelector('.pcp_area_atuacao')
        const modal_pcp_nome_area_atuacao = modal_edit_coordenador_projeto.querySelector('.pcp_nome_area_atuacao')
        const modal_pcp_formacao = modal_edit_coordenador_projeto.querySelector('.pcp_formacao')
        const modal_pcp_lattes = modal_edit_coordenador_projeto.querySelector('.pcp_lattes')
        //
        modalTitle.textContent = 'Atualizar Dados'
        modal_pcp_id.value = pcp_id
        modal_pcp_proposta_id.value = pcp_proposta_id
        modal_pcp_nome.value = pcp_nome
        modal_pcp_email.value = pcp_email
        modal_pcp_contato.value = pcp_contato
        modal_pcp_partic_perfil.value = pcp_partic_perfil
        modal_pcp_outro_partic_perfil.value = pcp_outro_partic_perfil
        modal_pcp_carga_hora.value = pcp_carga_hora
        modal_pcp_area_atuacao.value = pcp_area_atuacao
        modal_pcp_nome_area_atuacao.value = pcp_nome_area_atuacao
        modal_pcp_formacao.value = pcp_formacao
        modal_pcp_lattes.value = pcp_lattes

        if (pcp_partic_perfil === '9') {
          document.getElementById('campo_edit_pcp_outro_partic_perfil').style.display = "block";
          document.getElementById('edit_pcp_perfil_participante').required = true;
        } else {
          document.getElementById('campo_edit_pcp_outro_partic_perfil').style.display = "none";
          document.getElementById('edit_pcp_perfil_participante').required = false;
        }

        if (pcp_area_atuacao === '20') {
          document.getElementById('campo_edit_pcp_nome_area_atuacao').style.display = "block";
          document.getElementById('edit_pcp_nome_area_atuacao').required = true;
        } else {
          document.getElementById('campo_edit_pcp_nome_area_atuacao').style.display = "none";
          document.getElementById('edit_pcp_nome_area_atuacao').required = false;
        }
      })
    }
  </script>
</div>