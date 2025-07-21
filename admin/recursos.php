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
      <h4 class="mb-sm-0">Recursos</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Recursos</li>
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
            <h5 class="card-title mb-0">Lista de Recursos</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_recurso">+ Cadastrar Recurso</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_recurso" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-3">Recurso</span></th>
              <th><span class="me-3">Status</span></th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT rec_id, rec_recurso, rec_status, st_status FROM recursos
                                      INNER JOIN status ON status.st_id = recursos.rec_status
                                      ORDER BY rec_recurso");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $rec_id              = $row['rec_id'];
                $rec_recurso         = $row['rec_recurso'];
                $rec_status          = $row['rec_status'];
                $st_status           = $row['st_status'];

                //CONFIGURAÇÃO DO STATUS
                $status_color = ($rec_status == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= htmlspecialchars($rec_recurso) ?></th>
                  <td><span class="badge <?= $status_color ?>"><?= htmlspecialchars($st_status) ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_recurso"
                            data-bs-rec_id="<?= htmlspecialchars($rec_id) ?>"
                            data-bs-rec_recurso="<?= htmlspecialchars($rec_recurso) ?>"
                            data-bs-rec_status="<?= htmlspecialchars($rec_status) ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="../router/web.php?r=Recurs&acao=deletar&rec_id=<?= $rec_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_cad_recurso" tabindex="-1" aria-labelledby="modal_cad_recurso" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_recurso">Cadastrar Recurso</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Recurs" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" name="acao" value="cadastrar">

            <div class="col-12">
              <div>
                <label class="form-label">Recurso <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="rec_recurso" value="<?= htmlspecialchars($_SESSION['form_rec']['rec_recurso'] ?? '') ?>" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rec_status" name="rec_status" value="1" <?= (!isset($_SESSION['form_rec']) || !empty($_SESSION['form_rec']['rec_status'])) ? 'checked' : '' ?>>
                <label class="form-check-label" for="rec_status">Ativo</label>
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
$formData = $_SESSION['form_rec'] ?? [];
unset($_SESSION['form_rec']);
?>


<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_recurso" tabindex="-1" aria-labelledby="modal_edit_recurso" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_recurso">Editar Recurso</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Recurs" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control rec_id" name="rec_id">
            <input type="hidden" name="acao" value="atualizar">

            <div class="col-12">
              <div>
                <label class="form-label">Recurso <span>*</span></label>
                <input type="text" class="form-control text-uppercase rec_recurso" name="rec_recurso" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input rec_status" type="checkbox" id="rec_status" name="rec_status" value="1" checked>
                <label class="form-check-label" for="rec_status">Ativo</label>
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
  const modal_edit_recurso = document.getElementById('modal_edit_recurso')
  if (modal_edit_recurso) {
    modal_edit_recurso.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const rec_id = button.getAttribute('data-bs-rec_id')
      const rec_recurso = button.getAttribute('data-bs-rec_recurso')
      const rec_status = button.getAttribute('data-bs-rec_status')
      // 
      const modalTitle = modal_edit_recurso.querySelector('.modal-title')
      const modal_rec_id = modal_edit_recurso.querySelector('.rec_id')
      const modal_rec_recurso = modal_edit_recurso.querySelector('.rec_recurso')
      const modal_rec_status = modal_edit_recurso.querySelector('.rec_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_rec_id.value = rec_id
      modal_rec_recurso.value = rec_recurso
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (rec_status === '1') {
        modal_rec_status.checked = true;
      } else {
        modal_rec_status.checked = false;
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>