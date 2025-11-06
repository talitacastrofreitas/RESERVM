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
      <h4 class="mb-sm-0">Tipos de Ocorrências</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Tipos de Ocorrências</li>
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
            <h5 class="card-title mb-0">Lista de Tipos de Ocorrências</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_tipo_ocorrencia">+ Cadastrar Tipo de Ocorrência</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_recurso" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-3">Tipo de Ocorrência</span></th>
              <th><span class="me-3">Status</span></th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT cto_id, cto_tipo_ocorrencia, cto_status, st_status FROM conf_tipo_ocorrencia
                                      INNER JOIN status ON status.st_id = conf_tipo_ocorrencia.cto_status
                                      ORDER BY cto_tipo_ocorrencia");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $cto_id              = $row['cto_id'];
                $cto_tipo_ocorrencia = $row['cto_tipo_ocorrencia'];
                $cto_status          = $row['cto_status'];
                $st_status           = $row['st_status'];

                //CONFIGURAÇÃO DO STATUS
                $status_color = ($cto_status == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th class="text-uppercase"><?= htmlspecialchars($cto_tipo_ocorrencia) ?></th>
                  <td><span class="badge <?= $status_color ?>"><?= htmlspecialchars($st_status) ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_tipo_ocorrencia"
                            data-bs-cto_id="<?= htmlspecialchars($cto_id) ?>"
                            data-bs-cto_tipo_ocorrencia="<?= htmlspecialchars($cto_tipo_ocorrencia) ?>"
                            data-bs-cto_status="<?= htmlspecialchars($cto_status) ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="../router/web.php?r=TipoOcor&acao=deletar&cto_id=<?= $cto_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_cad_tipo_ocorrencia" tabindex="-1" aria-labelledby="modal_cad_tipo_ocorrencia" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_tipo_ocorrencia">Cadastrar Tipo de Ocorrência</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=TipoOcor" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" name="acao" value="cadastrar">

            <div class="col-12">
              <label class="form-label">Tipo de Ocorrência <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="cto_tipo_ocorrencia" value="<?= htmlspecialchars($_SESSION['form_cto']['cto_tipo_ocorrencia'] ?? '') ?>" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="cad_cto_status" name="cto_status" value="1" <?= (!isset($_SESSION['form_cto']) || !empty($_SESSION['form_cto']['cto_status'])) ? 'checked' : '' ?>>
                <label class="form-check-label" for="cad_cto_status">Ativo</label>
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
$formData = $_SESSION['form_cto'] ?? [];
unset($_SESSION['form_cto']);
?>


<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_tipo_ocorrencia" tabindex="-1" aria-labelledby="modal_edit_tipo_ocorrencia" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered  modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_tipo_ocorrencia">Editar Recurso</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=TipoOcor" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control cto_id" name="cto_id">
            <input type="hidden" name="acao" value="atualizar">

            <div class="col-12">
              <label class="form-label">Tipo de Ocorrência <span>*</span></label>
              <input type="text" class="form-control text-uppercase cto_tipo_ocorrencia" name="cto_tipo_ocorrencia" value="<?= htmlspecialchars($_SESSION['form_cto']['cto_tipo_ocorrencia'] ?? '') ?>" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input cto_status" type="checkbox" id="edit_cto_status" name="cto_status" value="1" checked>
                <label class="form-check-label" for="edit_cto_status">Ativo</label>
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
  const modal_edit_tipo_ocorrencia = document.getElementById('modal_edit_tipo_ocorrencia')
  if (modal_edit_tipo_ocorrencia) {
    modal_edit_tipo_ocorrencia.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const cto_id = button.getAttribute('data-bs-cto_id')
      const cto_tipo_ocorrencia = button.getAttribute('data-bs-cto_tipo_ocorrencia')
      const cto_status = button.getAttribute('data-bs-cto_status')
      // 
      const modalTitle = modal_edit_tipo_ocorrencia.querySelector('.modal-title')
      const modal_cto_id = modal_edit_tipo_ocorrencia.querySelector('.cto_id')
      const modal_cto_tipo_ocorrencia = modal_edit_tipo_ocorrencia.querySelector('.cto_tipo_ocorrencia')
      const modal_cto_status = modal_edit_tipo_ocorrencia.querySelector('.cto_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_cto_id.value = cto_id
      modal_cto_tipo_ocorrencia.value = cto_tipo_ocorrencia
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (cto_status === '1') {
        modal_cto_status.checked = true;
      } else {
        modal_cto_status.checked = false;
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>