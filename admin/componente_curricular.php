<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!isset($_SESSION['reservm_admin_id']) || $_SESSION['reservm_admin_perfil'] != 1) {
  header("Location: sair.php");
  exit;
}
?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Componentes Curriculares</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Componentes Curriculares</li>
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
            <h5 class="card-title mb-0">Lista de Componentes Curriculares</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_componente_curricular">+ Cadastrar Componente Curricular</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_comp_curricular" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>
                <div class="me-2">Componente Curricular</div>
              </th>
              <th>
                <div class="me-2">Curso</div>
              </th>
              <th>
                <div class="me-2">Status</div>
              </th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT compc_id, compc_componente, compc_curso, compc_status, curs_curso, st_id, st_status FROM componente_curricular
                                      INNER JOIN cursos ON cursos.curs_id = componente_curricular.compc_curso
                                      INNER JOIN status ON status.st_id = componente_curricular.compc_status");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $compc_id         = $row['compc_id'];
                $compc_componente = $row['compc_componente'];
                $compc_curso      = $row['compc_curso'];
                $compc_status     = $row['compc_status'];
                $curs_curso       = $row['curs_curso'];
                $st_id            = $row['st_id'];
                $st_status        = $row['st_status'];

                // CONFIGURAÇÃO DO STATUS
                $status_color = ($st_id == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= htmlspecialchars($compc_componente) ?></th>
                  <td><?= htmlspecialchars($curs_curso) ?></td>
                  <td><span class="badge <?= $status_color ?>"><?= htmlspecialchars($st_status) ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_componente_curricular"
                            data-bs-compc_id="<?= htmlspecialchars($compc_id) ?>"
                            data-bs-compc_componente="<?= htmlspecialchars($compc_componente) ?>"
                            data-bs-compc_curso="<?= htmlspecialchars($compc_curso) ?>"
                            data-bs-compc_status="<?= htmlspecialchars($compc_status) ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="../router/web.php?r=CompC&acao=deletar&compc_id=<?= $compc_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>

            <?php }
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar os dados";
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<!-- CADASTRAR -->
<div class="modal fade modal_padrao" id="modal_cad_componente_curricular" tabindex="-1" aria-labelledby="modal_cad_componente_curricular" aria-modal="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_componente_curricular">Cadastrar Componente Curricular</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=CompC" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" name="acao" value="cadastrar">

            <div class="col-12">
              <div>
                <label class="form-label">Componente Curricular <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="compc_componente" value="<?= htmlspecialchars($_SESSION['form_compc']['compc_componente'] ?? '') ?>" maxlength="200" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div>
                <?php try {
                  $sql = $conn->query("SELECT * FROM cursos WHERE curs_status = 1 ORDER BY curs_curso");
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Curso <span>*</span></label>
                <select class="form-select text-uppercase" name="compc_curso" id="cad_compc_curso" required>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['curs_id'] ?>" <?= ($_SESSION['form_compc']['compc_curso'] ?? '') == $res['curs_id'] ? 'selected' : '' ?>><?= $res['curs_curso'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="compc_status" name="compc_status" value="1" <?= (!isset($_SESSION['form_compc']) || !empty($_SESSION['form_compc']['compc_status'])) ? 'checked' : '' ?>>
                <label class="form-check-label" for="compc_status">Ativo</label>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
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
$formData = $_SESSION['form_compc'] ?? [];
unset($_SESSION['form_compc']);
?>


<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_componente_curricular" tabindex="-1" aria-labelledby="modal_edit_componente_curricular" aria-modal="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_componente_curricular">Editar Componente Curricular</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=CompC" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control compc_id" name="compc_id">
            <input type="hidden" name="acao" value="atualizar">

            <div class="col-12">
              <div>
                <label class="form-label">Componente Curricular <span>*</span></label>
                <input type="text" class="form-control text-uppercase compc_componente" name="compc_componente" maxlength="200" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div>
                <?php try {
                  $sql = $conn->query("SELECT * FROM cursos WHERE curs_status = 1 ORDER BY curs_curso");
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <label class="form-label">Curso <span>*</span></label>
                <select class="form-select text-uppercase compc_curso" name="compc_curso" id="edit_compc_curso" required>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input compc_status" type="checkbox" id="compc_status" name="compc_status" value="1" checked>
                <label class="form-check-label" for="compc_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Atualizar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const modal_edit_componente_curricular = document.getElementById('modal_edit_componente_curricular')
  if (modal_edit_componente_curricular) {
    modal_edit_componente_curricular.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const compc_id = button.getAttribute('data-bs-compc_id')
      const compc_componente = button.getAttribute('data-bs-compc_componente')
      const compc_curso = button.getAttribute('data-bs-compc_curso')
      const compc_status = button.getAttribute('data-bs-compc_status')
      // 
      const modalTitle = modal_edit_componente_curricular.querySelector('.modal-title')
      const modal_compc_id = modal_edit_componente_curricular.querySelector('.compc_id')
      const modal_compc_componente = modal_edit_componente_curricular.querySelector('.compc_componente')
      const modal_compc_curso = modal_edit_componente_curricular.querySelector('.compc_curso')
      const modal_compc_status = modal_edit_componente_curricular.querySelector('.compc_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_compc_id.value = compc_id
      modal_compc_componente.value = compc_componente
      modal_compc_curso.value = compc_curso
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (compc_status === '1') {
        modal_compc_status.checked = true;
      } else {
        modal_compc_status.checked = false;
      }
      // ESTE SELETOR LEVA O DADO DA BASE PARA O CAMPO SELECT2
      $('#edit_compc_curso').val(compc_curso).trigger('change');
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- SELECT2 FORM -->
<script src="../includes/select/select2.js"></script>