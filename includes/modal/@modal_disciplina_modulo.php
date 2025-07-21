<!-- CADASTRAR DISCIPLINA / MÓDULO -->
<div class="modal fade modal_padrao" id="modal_cad_disciplina_modulo" tabindex="-1" aria-labelledby="modal_cad_disciplina_modulo" aria-modal="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_disciplina_modulo">Cadastrar Disciplina / Módulo</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_cursos_modulo.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control" name="prop_id" value="<?= $_GET['i'] ?>" required>

            <div class="col-lg-6 col-xl-3">
              <label class="form-label">Docente Interno? <span>*</span></label>
              <select class="form-select" name="prop_cmod_tipo_docente" id="prop_cmod_tipo_docente_cad" required>
                <option selected disabled value=""></option>
                <option value="1">SIM</option>
                <option value="0">NÃO</option>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-9" id="campo_prop_cmod_nome_docente_cad_sim" style="display: none;">
              <?php try {
                $stmt = $conn->query("SELECT NOMESOCIAL FROM $view_colaboradores ORDER BY NOMESOCIAL");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Nome do Docente Interno <span>*</span></label>
              <select class="form-select text-uppercase prop_cmod_nome_docente" name="prop_cmod_nome_docente_int" id="prop_cmod_tipo_docente_cad_sim" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['NOMESOCIAL']; ?>"><?= $result_iten['NOMESOCIAL']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-9" id="campo_prop_cmod_nome_docente_cad_nao" style="display: none;">
              <label class="form-label">Nome do Docente Externo <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="prop_cmod_nome_docente_ext" id="prop_cmod_tipo_docente_cad_nao" maxlength="60" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <script>
              // EXIBE CAMPOS NOMES DOCENTES INTERNOS E EXTERNOS
              const prop_cmod_tipo_docente_cad = document.getElementById("prop_cmod_tipo_docente_cad");
              const campo_prop_cmod_nome_docente_cad_sim = document.getElementById("campo_prop_cmod_nome_docente_cad_sim");
              const campo_prop_cmod_nome_docente_cad_nao = document.getElementById("campo_prop_cmod_nome_docente_cad_nao");

              prop_cmod_tipo_docente_cad.addEventListener("change", function() {
                if (prop_cmod_tipo_docente_cad.value === "1") {
                  campo_prop_cmod_nome_docente_cad_sim.style.display = "block";
                  document.getElementById("prop_cmod_tipo_docente_cad_sim").required = true;
                } else {
                  campo_prop_cmod_nome_docente_cad_sim.style.display = "none";
                  document.getElementById("prop_cmod_tipo_docente_cad_sim").required = false;
                }
                if (prop_cmod_tipo_docente_cad.value === "0") {
                  campo_prop_cmod_nome_docente_cad_nao.style.display = "block";
                  document.getElementById("prop_cmod_tipo_docente_cad_nao").required = true;
                } else {
                  campo_prop_cmod_nome_docente_cad_nao.style.display = "none";
                  document.getElementById("prop_cmod_tipo_docente_cad_nao").required = false;
                }
              });

              if (prop_cmod_tipo_docente_cad.value === "1") {
                campo_prop_cmod_nome_docente_cad_sim.style.display = "block";
                document.getElementById("prop_cmod_tipo_docente_cad_sim").required = true;
              }
              if (prop_cmod_tipo_docente_cad.value === "0") {
                campo_prop_cmod_nome_docente_cad_nao.style.display = "block";
                document.getElementById("prop_cmod_tipo_docente_cad_nao").required = true;
              }
            </script>

            <div class="col-l12">
              <label class="form-label">Título da Disciplina / Módulo <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="prop_cmod_titulo" maxlength="200" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Assuntos <span>*</span></label>
              <textarea class="form-control" name="prop_cmod_assunto" rows="3" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Datas e Horários <span>*</span></label>
              <textarea class="form-control" name="prop_cmod_data_hora" rows="3" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <?php try {
                $stmt = $conn->query("SELECT esporg_id, esporg_espaco_organizacao FROM conf_tipo_espaco_organizacao ORDER BY CASE WHEN esporg_espaco_organizacao = 'OUTRO' THEN 1 ELSE 0 END, esporg_espaco_organizacao");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Formato de Organização da Sala <span>*</span></label>
              <select class="form-select text-uppercase" name="prop_cmod_organizacao" id="prop_cmod_organizacao_cad" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['esporg_id']; ?>"><?= $result_iten['esporg_espaco_organizacao']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12" id="campo_prop_cmod_outra_organizacao_cad" style="display: none;">
              <label class="form-label">Descreva a Organização da Sala <span>*</span></label>
              <textarea class="form-control" name="prop_cmod_outra_organizacao" id="prop_cmod_outra_organizacao_cad" rows="3" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              // EXIBE CAMPOS NOMES DOCENTES INTERNOS E EXTERNOS
              const prop_cmod_organizacao_cad = document.getElementById("prop_cmod_organizacao_cad");
              const campo_prop_cmod_outra_organizacao_cad = document.getElementById("campo_prop_cmod_outra_organizacao_cad");

              prop_cmod_organizacao_cad.addEventListener("change", function() {
                if (prop_cmod_organizacao_cad.value === "7") {
                  campo_prop_cmod_outra_organizacao_cad.style.display = "block";
                  document.getElementById("prop_cmod_outra_organizacao_cad").required = true;
                } else {
                  campo_prop_cmod_outra_organizacao_cad.style.display = "none";
                  document.getElementById("prop_cmod_outra_organizacao_cad").required = false;
                }
              });

              if (prop_cmod_organizacao_cad.value === "7") {
                campo_prop_cmod_outra_organizacao_cad.style.display = "block";
                document.getElementById("prop_cmod_outra_organizacao_cad").required = true;
              }
            </script>

            <div class="col-12">
              <?php try {
                $stmt = $conn->query("SELECT for_pag_id, for_pag_forma_pagamento FROM forma_pagamento ORDER BY for_pag_forma_pagamento");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Forma de Pagamento Docente <span>*</span></label>
              <select class="form-select text-uppercase" name="prop_cmod_forma_pagamento" required>
                <option selected disabled value=""></option>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['for_pag_id']; ?>"><?= $result_iten['for_pag_forma_pagamento']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Currículo Resumido <span>*</span></label>
              <textarea class="form-control" name="prop_cmod_curriculo" id="prop_cmod_curriculo_cad" rows="3" maxlength="1000" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <p class="label_info text-end mt-1">Caracteres restantes: <span id="caracteres-rest">1000</span></p>
              <script>
                const textarea_cad = document.getElementById("prop_cmod_curriculo_cad");
                const caracteresRest = document.getElementById("caracteres-rest");
                const limiteCaracteresCad = 1000;
                textarea_cad.addEventListener("input", function() {
                  const texto = textarea_cad.value;
                  const caracteresDigitados = texto.length;
                  if (caracteresDigitados > limiteCaracteresCad) {
                    textarea_cad.value = texto.substring(0, limiteCaracteresCad);
                  }
                  const caracteresRestantesCount = limiteCaracteresCad - caracteresDigitados;
                  caracteresRest.textContent = caracteresRestantesCount;
                });
              </script>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadDisciplinaModulo">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>




<!-- EDITAR DISCIPLINA / M -->
<div class="modal fade modal_padrao" id="modal_edit_disciplina_modulo" tabindex="-1" aria-labelledby="modal_edit_disciplina_modulo" aria-modal="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_disciplina_modulo">Cadastrar Coordenador(a) do Projeto</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_cursos_modulo.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control prop_cmod_id" name="prop_cmod_id" required>
            <input type="hidden" class="form-control prop_cmod_prop_id" name="prop_cmod_prop_id" required>

            <div class="col-lg-6 col-xl-3">
              <label class="form-label">Docente Interno? <span>*</span></label>
              <select class="form-select prop_cmod_tipo_docente" name="prop_cmod_tipo_docente" id="prop_cmod_tipo_docente_edit" required>
                <option value="1">SIM</option>
                <option value="0">NÃO</option>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-9" id="campo_prop_cmod_nome_docente_edit_sim" style="display: none;">
              <?php try {
                $stmt = $conn->query("SELECT NOMESOCIAL FROM $view_colaboradores ORDER BY NOMESOCIAL");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Nome do Docente Interno <span>*</span></label>
              <select class="form-select text-uppercase prop_cmod_nome_docente_int" name="prop_cmod_nome_docente_int" id="prop_cmod_tipo_docente_edit_sim">
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['NOMESOCIAL']; ?>"><?= $result_iten['NOMESOCIAL']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-6 col-xl-9" id="campo_prop_cmod_nome_docente_edit_nao" style="display: none;">
              <label class="form-label">Nome do Docente Externo <span>*</span></label>
              <input type="text" class="form-control text-uppercase prop_cmod_nome_docente_ext" name="prop_cmod_nome_docente_ext" id="prop_cmod_tipo_docente_edit_nao" maxlength="60">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <script>
              // EXIBE CAMPOS NOMES DOCENTES INTERNOS E EXTERNOS
              const prop_cmod_tipo_docente_edit = document.getElementById("prop_cmod_tipo_docente_edit");
              const campo_prop_cmod_nome_docente_edit_sim = document.getElementById("campo_prop_cmod_nome_docente_edit_sim");
              const campo_prop_cmod_nome_docente_edit_nao = document.getElementById("campo_prop_cmod_nome_docente_edit_nao");

              prop_cmod_tipo_docente_edit.addEventListener("change", function() {
                if (prop_cmod_tipo_docente_edit.value === "1") {
                  campo_prop_cmod_nome_docente_edit_sim.style.display = "block";
                  document.getElementById("prop_cmod_tipo_docente_edit_sim").required = true;
                } else {
                  campo_prop_cmod_nome_docente_edit_sim.style.display = "none";
                  document.getElementById("prop_cmod_tipo_docente_edit_sim").required = false;
                }
                if (prop_cmod_tipo_docente_edit.value === "0") {
                  campo_prop_cmod_nome_docente_edit_nao.style.display = "block";
                  document.getElementById("prop_cmod_tipo_docente_edit_nao").required = true;
                } else {
                  campo_prop_cmod_nome_docente_edit_nao.style.display = "none";
                  document.getElementById("prop_cmod_tipo_docente_edit_nao").required = false;
                }
              });

              if (prop_cmod_tipo_docente_edit.value === "1") {
                campo_prop_cmod_nome_docente_edit_sim.style.display = "block";
                document.getElementById("prop_cmod_tipo_docente_edit_sim").required = true;
              }
              if (prop_cmod_tipo_docente_edit.value === "0") {
                campo_prop_cmod_nome_docente_edit_nao.style.display = "block";
                document.getElementById("prop_cmod_tipo_docente_edit_nao").required = true;
              }
            </script>

            <div class="col-l12">
              <label class="form-label">Título da Disciplina / Módulo <span>*</span></label>
              <input type="text" class="form-control text-uppercase prop_cmod_titulo" name="prop_cmod_titulo" maxlength="200" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Assuntos <span>*</span></label>
              <textarea class="form-control prop_cmod_assunto" name="prop_cmod_assunto" rows="3" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Datas e Horários <span>*</span></label>
              <textarea class="form-control prop_cmod_data_hora" name="prop_cmod_data_hora" rows="3" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <?php try {
                $stmt = $conn->query("SELECT esporg_id, esporg_espaco_organizacao FROM conf_tipo_espaco_organizacao ORDER BY CASE WHEN esporg_espaco_organizacao = 'OUTRO' THEN 1 ELSE 0 END, esporg_espaco_organizacao");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Formato de Organização da Sala <span>*</span></label>
              <select class="form-select text-uppercase prop_cmod_organizacao" name="prop_cmod_organizacao" id="prop_cmod_organizacao_edit" required>
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['esporg_id']; ?>"><?= $result_iten['esporg_espaco_organizacao']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12" id="campo_prop_cmod_outra_organizacao_edit" style="display: none;">
              <label class="form-label">Descreva a Organização da Sala <span>*</span></label>
              <textarea class="form-control prop_cmod_outra_organizacao" name="prop_cmod_outra_organizacao" id="prop_cmod_outra_organizacao_edit" rows="3"></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
            <script>
              // EXIBE CAMPOS NOMES DOCENTES INTERNOS E EXTERNOS
              const prop_cmod_organizacao_edit = document.getElementById("prop_cmod_organizacao_edit");
              const campo_prop_cmod_outra_organizacao_edit = document.getElementById("campo_prop_cmod_outra_organizacao_edit");

              prop_cmod_organizacao_edit.addEventListener("change", function() {
                if (prop_cmod_organizacao_edit.value === "7") {
                  campo_prop_cmod_outra_organizacao_edit.style.display = "block";
                  document.getElementById("prop_cmod_outra_organizacao_edit").required = true;
                } else {
                  campo_prop_cmod_outra_organizacao_edit.style.display = "none";
                  document.getElementById("prop_cmod_outra_organizacao_edit").required = false;
                  document.getElementById("prop_cmod_outra_organizacao_edit").value = '';
                }
              });

              if (prop_cmod_organizacao_edit.value === "7") {
                campo_prop_cmod_outra_organizacao_edit.style.display = "block";
                document.getElementById("prop_cmod_outra_organizacao_edit").required = true;
              }
            </script>

            <div class="col-12">
              <?php try {
                $stmt = $conn->query("SELECT for_pag_id, for_pag_forma_pagamento FROM forma_pagamento ORDER BY for_pag_forma_pagamento");
                $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar materiais";
              } ?>
              <label class="form-label">Forma de Pagamento Docente <span>*</span></label>
              <select class="form-select text-uppercase prop_cmod_forma_pagamento" name="prop_cmod_forma_pagamento">
                <?php foreach ($result_itens as $result_iten) : ?>
                  <option value="<?= $result_iten['for_pag_id']; ?>"><?= $result_iten['for_pag_forma_pagamento']; ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">Currículo Resumido <span>*</span></label>
              <textarea class="form-control prop_cmod_curriculo" name="prop_cmod_curriculo" id="prop_cmod_curriculo_edit" rows="3" maxlength="1000" required></textarea>
              <div class="invalid-feedback">Este campo é obrigatório</div>
              <p class="label_info text-end mt-1">Caracteres restantes: <span id="caracteres-rest-edit">1000</span></p>
              <script>
                const textarea_edit = document.getElementById("prop_cmod_curriculo_edit");
                const caracteresRestEdit = document.getElementById("caracteres-rest-edit");
                const limiteCaracteresedit = 1000;
                textarea_edit.addEventListener("input", function() {
                  const texto = textarea_edit.value;
                  const caracteresDigitados = texto.length;
                  if (caracteresDigitados > limiteCaracteresedit) {
                    textarea_edit.value = texto.substring(0, limiteCaracteresedit);
                  }
                  const caracteresRestantesCount = limiteCaracteresedit - caracteresDigitados;
                  caracteresRestEdit.textContent = caracteresRestantesCount;
                });
              </script>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditDisciplinaModulo">Atualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    const modal_edit_disciplina_modulo = document.getElementById('modal_edit_disciplina_modulo')
    if (modal_edit_disciplina_modulo) {
      modal_edit_disciplina_modulo.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        // EXTRAI DADOS DO data-bs-* 
        const prop_cmod_id = button.getAttribute('data-bs-prop_cmod_id')
        const prop_cmod_prop_id = button.getAttribute('data-bs-prop_cmod_prop_id')
        const prop_cmod_tipo_docente = button.getAttribute('data-bs-prop_cmod_tipo_docente')
        const prop_cmod_nome_docente_int = button.getAttribute('data-bs-prop_cmod_nome_docente')
        const prop_cmod_nome_docente_ext = button.getAttribute('data-bs-prop_cmod_nome_docente')
        const prop_cmod_titulo = button.getAttribute('data-bs-prop_cmod_titulo')
        const prop_cmod_assunto = button.getAttribute('data-bs-prop_cmod_assunto')
        const prop_cmod_data_hora = button.getAttribute('data-bs-prop_cmod_data_hora')
        const prop_cmod_organizacao = button.getAttribute('data-bs-prop_cmod_organizacao')
        const prop_cmod_outra_organizacao = button.getAttribute('data-bs-prop_cmod_outra_organizacao')
        const prop_cmod_forma_pagamento = button.getAttribute('data-bs-prop_cmod_forma_pagamento')
        const prop_cmod_curriculo = button.getAttribute('data-bs-prop_cmod_curriculo')
        // 
        const modalTitle = modal_edit_disciplina_modulo.querySelector('.modal-title')
        const modal_prop_cmod_id = modal_edit_disciplina_modulo.querySelector('.prop_cmod_id')
        const modal_prop_cmod_prop_id = modal_edit_disciplina_modulo.querySelector('.prop_cmod_prop_id')
        const modal_prop_cmod_tipo_docente = modal_edit_disciplina_modulo.querySelector('.prop_cmod_tipo_docente')
        const modal_prop_cmod_nome_docente_int = modal_edit_disciplina_modulo.querySelector('.prop_cmod_nome_docente_int')
        const modal_prop_cmod_nome_docente_ext = modal_edit_disciplina_modulo.querySelector('.prop_cmod_nome_docente_ext')
        const modal_prop_cmod_titulo = modal_edit_disciplina_modulo.querySelector('.prop_cmod_titulo')
        const modal_prop_cmod_assunto = modal_edit_disciplina_modulo.querySelector('.prop_cmod_assunto')
        const modal_prop_cmod_data_hora = modal_edit_disciplina_modulo.querySelector('.prop_cmod_data_hora')
        const modal_prop_cmod_organizacao = modal_edit_disciplina_modulo.querySelector('.prop_cmod_organizacao')
        const modal_prop_cmod_outra_organizacao = modal_edit_disciplina_modulo.querySelector('.prop_cmod_outra_organizacao')
        const modal_prop_cmod_forma_pagamento = modal_edit_disciplina_modulo.querySelector('.prop_cmod_forma_pagamento')
        const modal_prop_cmod_curriculo = modal_edit_disciplina_modulo.querySelector('.prop_cmod_curriculo')
        //
        modalTitle.textContent = 'Atualizar Dados'
        modal_prop_cmod_id.value = prop_cmod_id
        modal_prop_cmod_prop_id.value = prop_cmod_prop_id
        modal_prop_cmod_tipo_docente.value = prop_cmod_tipo_docente
        modal_prop_cmod_nome_docente_int.value = prop_cmod_nome_docente_int
        modal_prop_cmod_nome_docente_ext.value = prop_cmod_nome_docente_ext
        // modal_prop_cmod_nome_docente.value = prop_cmod_nome_docente
        modal_prop_cmod_titulo.value = prop_cmod_titulo
        modal_prop_cmod_assunto.value = prop_cmod_assunto
        modal_prop_cmod_data_hora.value = prop_cmod_data_hora
        modal_prop_cmod_organizacao.value = prop_cmod_organizacao
        modal_prop_cmod_outra_organizacao.value = prop_cmod_outra_organizacao
        modal_prop_cmod_forma_pagamento.value = prop_cmod_forma_pagamento
        modal_prop_cmod_curriculo.value = prop_cmod_curriculo

        if (prop_cmod_tipo_docente === '1') {
          document.getElementById('campo_prop_cmod_nome_docente_edit_sim').style.display = "block";
          document.getElementById('campo_prop_cmod_nome_docente_edit_nao').style.display = "none";
          modal_prop_cmod_nome_docente_ext.value = ''
          //
          document.getElementById('prop_cmod_tipo_docente_edit_sim').required = true;
          document.getElementById('prop_cmod_tipo_docente_edit_nao').required = false;
        } else {
          document.getElementById('campo_prop_cmod_nome_docente_edit_sim').style.display = "none";
          document.getElementById('campo_prop_cmod_nome_docente_edit_nao').style.display = "block";
          modal_prop_cmod_nome_docente_int.value = ''
          //
          document.getElementById('prop_cmod_tipo_docente_edit_sim').required = false;
          document.getElementById('prop_cmod_tipo_docente_edit_nao').required = true;
        }

        if (prop_cmod_organizacao === '7') {
          document.getElementById('campo_prop_cmod_outra_organizacao_edit').style.display = "block";
          document.getElementById('prop_cmod_outra_organizacao_edit').required = true;
        } else {
          document.getElementById('campo_prop_cmod_outra_organizacao_edit').style.display = "none";
          document.getElementById('prop_cmod_outra_organizacao_edit').required = false;
        }

        // ESTE SELETOR LEVA O DADO DA BASE PARA O CAMPO SELECT2
        $('#prop_cmod_tipo_docente_edit_sim').val(prop_cmod_nome_docente_int).trigger('change');
      })
    }
  </script>
</div>