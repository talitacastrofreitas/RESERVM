<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!isset($_SESSION['reservm_admin_id']) || $_SESSION['reservm_admin_perfil'] != 1) {
  header("Location: sair.php");
  exit;
}
?>

<style>
  .select2-container {
    display: block !important;
    height: auto !important;
    width: 100% !important;
    /* Ou o que for necessário */
    opacity: 1 !important;
    visibility: visible !important;
    z-index: 9999 !important;
    /* Para garantir que esteja na frente */
  }

  .select2-dropdown {
    /* Se o dropdown aparece, mas não o input */
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    z-index: 9999 !important;
  }
</style>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Cursos</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Cursos</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-sm-6 text-sm-start text-center">
            <h5 class="card-title mb-0">Lista de Cursos</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal"
              data-bs-toggle="button" data-bs-target="#modal_cad_curso">+ Cadastrar Curso</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_cursos" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-3">Curso</span></th>
              <th><span class="me-3">Matrícula</span></th>
              <th><span class="me-3">Coordenador(a)</span></th>
              <th><span class="me-3">E-mail</span></th>
              <th><span class="me-3">Status</span></th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("
                SELECT
                    c.curs_id,
                    c.curs_curso,
                    c.curs_status,
                    s.st_id,
                    s.st_status,
                    -- Coordenadores Matriculas
                    STUFF((
                        SELECT ', ' + col.CHAPA
                        FROM curso_coordenador cc_sub
                        LEFT JOIN $view_colaboradores col ON col.CHAPA = cc_sub.coordenador_matricula
                        WHERE cc_sub.curs_id = c.curs_id
                        ORDER BY col.CHAPA
                        FOR XML PATH('')
                    ), 1, 2, '') AS CoordenadoresMatriculas,
                    -- Coordenadores Nomes
                    STUFF((
                        SELECT ', ' + col.NOMESOCIAL
                        FROM curso_coordenador cc_sub
                        LEFT JOIN $view_colaboradores col ON col.CHAPA = cc_sub.coordenador_matricula
                        WHERE cc_sub.curs_id = c.curs_id
                        ORDER BY col.NOMESOCIAL
                        FOR XML PATH('')
                    ), 1, 2, '') AS CoordenadoresNomes,
                    -- Coordenadores Emails
                    STUFF((
                        SELECT ', ' + col.EMAIL
                        FROM curso_coordenador cc_sub
                        LEFT JOIN $view_colaboradores col ON col.CHAPA = cc_sub.coordenador_matricula
                        WHERE cc_sub.curs_id = c.curs_id
                        ORDER BY col.EMAIL
                        FOR XML PATH('')
                    ), 1, 2, '') AS CoordenadoresEmails
                FROM cursos c
                INNER JOIN status s ON s.st_id = c.curs_status
                ORDER BY c.curs_curso
              ");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $curs_id = $row['curs_id'];
                $curs_curso = $row['curs_curso'];
                $curs_status = $row['curs_status'];
                $st_id = $row['st_id'];
                $st_status = $row['st_status'];
                // Novos campos concatenados da consulta
                $CoordenadoresMatriculas = $row['CoordenadoresMatriculas'];
                $CoordenadoresNomes = $row['CoordenadoresNomes'];
                $CoordenadoresEmails = $row['CoordenadoresEmails'];

                //CONFIGURAÇÃO DO STATUS
                $status_color = ($st_id == 1) ? 'bg_info_verde' : 'bg_info_cinza';
                ?>

                <tr>
                  <th><?= htmlspecialchars($curs_curso) ?></th>
                  <td><?= htmlspecialchars($CoordenadoresMatriculas) ?></td>
                  <td><?= htmlspecialchars($CoordenadoresNomes) ?></td>
                  <td><?= htmlspecialchars($CoordenadoresEmails) ?></td>
                  <td><span class="badge <?= $status_color ?>"><?= htmlspecialchars($st_status) ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal"
                            data-bs-target="#modal_edit_curso" data-bs-curs_id="<?= htmlspecialchars($curs_id) ?>"
                            data-bs-curs_curso="<?= htmlspecialchars($curs_curso) ?>"
                            data-bs-curs_status="<?= htmlspecialchars($curs_status) ?>" title="Editar"><i
                              class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="../router/web.php?r=Curs&acao=deletar&curs_id=<?= $curs_id ?>"
                            class="dropdown-item remove-item-btn del-btn" title="Excluir"><i
                              class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>

              <?php }
            } catch (PDOException $e) {
              error_log("Erro ao tentar recuperar os dados: " . $e->getMessage()); // Mantenha este log para depuração
              echo "Erro ao tentar recuperar os dados. Por favor, verifique os logs de erro para mais detalhes."; // Mensagem mais amigável
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<!-- CADASTRAR -->
<div class="modal fade modal_padrao" id="modal_cad_curso" tabindex="-1" aria-labelledby="modal_cad_curso"
  aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_curso">Cadastrar Curso</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
            class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Curs" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" name="acao" value="cadastrar">

            <div class="col-12">
              <label class="form-label">Curso <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="curs_curso"
                value="<?= htmlspecialchars($_SESSION['form_curs']['curs_curso'] ?? '') ?>" maxlength="50" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <!-- <div class="col-12">
              <label class="form-label">Coordenador(a)</label>
              <select class="form-select text-uppercase" name="curs_matricula_prof" id="cad_curs_matricula_prof">
                <option selected value=""></option>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div> -->

            <div class="col-12">
              <label class="form-label">Coordenador(a)</label>
              <select class="form-select text-uppercase" name="curs_matricula_prof[]" id="cad_curs_matricula_prof"
                multiple>
                <option value=""></option>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">E-mail</label>
              <input type="text" class="form-control text-lowercase" id="cad_curs_email_prof" readonly>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="curs_status" name="curs_status" value="1"
                  <?= (!isset($_SESSION['form_curs']) || !empty($_SESSION['form_curs']['curs_status'])) ? 'checked' : '' ?>>
                <label class="form-check-label" for="curs_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal"
                  data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Cadastrar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
// APAGA AS SESSÕES QUE PREENCHEM O FORMULÁRIO
$formData = $_SESSION['form_curs'] ?? [];
unset($_SESSION['form_curs']);
?>


<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_curso" tabindex="-1" aria-labelledby="modal_edit_curso"
  aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_curso">Editar Curso</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
            class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Curs" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control curs_id" name="curs_id">
            <input type="hidden" name="acao" value="atualizar">

            <div class="col-12">
              <label class="form-label">Curso <span>*</span></label>
              <input type="text" class="form-control text-uppercase curs_curso" name="curs_curso" maxlength="50"
                required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <!-- <div class="col-12">
              <label class="form-label">Coordenador(a)</label>
              <select class="form-select text- curs_matricula_prof" name="curs_matricula_prof" id="edit_curs_matricula_prof">
                <option selected value=""></option>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div> -->

            <div class="col-12">
              <label class="form-label">Coordenador(a)</label>
              <select class="form-select text- curs_matricula_prof" name="curs_matricula_prof[]"
                id="edit_curs_matricula_prof" multiple>
                <option value=""></option>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">E-mail</label>
              <input type="text" class="form-control text-lowercase curs_email" id="edit_curs_email_prof" readonly>

            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input curs_status" type="checkbox" id="curs_status" name="curs_status"
                  value="1" checked>
                <label class="form-check-label" for="curs_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal"
                  data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Atualizar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- <script>
  const modal_edit_curso = document.getElementById('modal_edit_curso')
  if (modal_edit_curso) {
    modal_edit_curso.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const curs_id = button.getAttribute('data-bs-curs_id')
      const curs_curso = button.getAttribute('data-bs-curs_curso')
      const curs_nomesocial = button.getAttribute('data-bs-curs_nomesocial')
      const curs_status = button.getAttribute('data-bs-curs_status')
      // 
      const modalTitle = modal_edit_curso.querySelector('.modal-title')
      const modal_curs_id = modal_edit_curso.querySelector('.curs_id')
      const modal_curs_curso = modal_edit_curso.querySelector('.curs_curso')
      const modal_curs_nomesocial = modal_edit_curso.querySelector('.curs_nomesocial')
      const modal_curs_status = modal_edit_curso.querySelector('.curs_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_curs_id.value = curs_id
      modal_curs_curso.value = curs_curso

      // ESTE SELETOR LEVA O DADO DA BASE PARA O CAMPO SELECT2
      $('#edit_curs_matricula_prof').val(curs_nomesocial).trigger('change');

      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (curs_status === '1') {
        modal_curs_status.checked = true;
      } else {
        modal_curs_status.checked = false;
      }
    })
  }
</script> -->

<!-- 
<script>
  const modal_edit_curso = document.getElementById('modal_edit_curso')
  if (modal_edit_curso) {
    modal_edit_curso.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-*
      const curs_id = button.getAttribute('data-bs-curs_id')
      const curs_curso = button.getAttribute('data-bs-curs_curso')
      // const curs_nomesocial = button.getAttribute('data-bs-curs_nomesocial') // ESTE NÃO SERÁ MAIS USADO AQUI
      const curs_status = button.getAttribute('data-bs-curs_status')
      //
      const modalTitle = modal_edit_curso.querySelector('.modal-title')
      const modal_curs_id = modal_edit_curso.querySelector('.curs_id')
      const modal_curs_curso = modal_edit_curso.querySelector('.curs_curso')
      // const modal_curs_nomesocial = modal_edit_curso.querySelector('.curs_nomesocial') // NÃO SERÁ USADO MAIS
      const modal_curs_status = modal_edit_curso.querySelector('.curs_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_curs_id.value = curs_id
      modal_curs_curso.value = curs_curso

      // VERIFICA SE O CHECKBOX ESTÁ MARCADO
      if (curs_status === '1') {
        modal_curs_status.checked = true;
      } else {
        modal_curs_status.checked = false;
      }

      // NOVO: BUSCA OS COORDENADORES JÁ ASSOCIADOS A ESTE CURSO E PREENCHE O SELECT MULTIPLO
      $.ajax({
        url: "controller/get_course_coordinators.php", // Novo script que você vai criar
        type: "GET",
        dataType: "json",
        data: {
          curs_id: curs_id
        },
        success: function(data) {
          const selectedCoordinators = [];
          $.each(data, function(index, coordinator) {
            // O valor da option será "CHAPA - NOMESOCIAL"
            selectedCoordinators.push(coordinator.CHAPA + ' - ' + coordinator.NOMESOCIAL);
          });
          // Define os valores selecionados no select2
          $('#edit_curs_matricula_prof').val(selectedCoordinators).trigger('change');
        },
        error: function(xhr, status, error) {
          console.error("Erro ao buscar os coordenadores do curso:", error);
          // Opcional: exiba uma mensagem de erro para o usuário
        }
      });
    })
  }
</script> -->



<!-- ITENS DOS SELECTS -->
<!-- <script src="../assets/js/351.jquery.min.js"></script> -->
<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>


<!-- COMPLETO FORM -->
<!-- <script src="includes/select/completa_form.js"></script> -->
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>
<!-- ITENS DOS SELECTS -->
<script src="includes/select/select_colaboradores.js"></script>