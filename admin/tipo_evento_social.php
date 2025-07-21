<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!empty($global_admin_id) && $global_admin_perfil != 1) {
  header("Location: sair.php");
}
?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Tipos de Eventos Sociais</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Tipos de Eventos Sociais</li>
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
            <h5 class="card-title mb-0">Lista de Tipos de Eventos Sociais</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button class="btn botao botao_azul_escuro waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_area_conhecimento">+ Cadastrar Tipo de Evento Social</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_tipo_evento_social" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>Tipo de Evento Social</th>
              <th>Status</th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM conf_tipo_evento_social
                                      INNER JOIN status ON status.st_id = conf_tipo_evento_social.tes_status
                                      WHERE tes_evento_social != 'OUTRO'");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                //CONFIGURAÇÃO DO STATUS
                $status_color = ($st_id == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= $tes_evento_social ?></th>
                  <td><span class="badge <?= $status_color ?>"><?= $st_status ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_tipo_evento_social" data-bs-tes_id="<?= $tes_id ?>" data-bs-tes_evento_social="<?= $tes_evento_social ?>" data-bs-tes_status="<?= $tes_status ?>" title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="controller/controller_tipo_evento_social.php?funcao=exc_tes&tes_id=<?= $tes_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_cad_area_conhecimento" tabindex="-1" aria-labelledby="modal_cad_area_conhecimento" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_area_conhecimento">Cadastrar Tipo de Evento Social</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_tipo_evento_social.php" class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-12">
              <div>
                <label class="form-label">Tipo de Evento Social <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="tes_evento_social" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="tes_status" name="tes_status" value="1" checked>
                <label class="form-check-label" for="tes_status">Ativo</label>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadTipoEventoSocial">Cadastrar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<div class="modal fade modal_padrao" id="modal_edit_tipo_evento_social" tabindex="-1" aria-labelledby="modal_edit_tipo_evento_social" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_tipo_evento_social">Editar Tipo de Evento Social</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_tipo_evento_social.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control tes_id" name="tes_id">

            <div class="col-12">
              <div>
                <label class="form-label">Tipo de Evento Social <span>*</span></label>
                <input type="text" class="form-control text-uppercase tes_evento_social" name="tes_evento_social" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input tes_status" type="checkbox" id="tes_status" name="tes_status" value="1" checked>
                <label class="form-check-label" for="tes_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditTipoEventoSocial">Atualizar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const modal_edit_tipo_evento_social = document.getElementById('modal_edit_tipo_evento_social')
  if (modal_edit_tipo_evento_social) {
    modal_edit_tipo_evento_social.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const tes_id = button.getAttribute('data-bs-tes_id')
      const tes_evento_social = button.getAttribute('data-bs-tes_evento_social')
      const tes_status = button.getAttribute('data-bs-tes_status')
      // 
      const modalTitle = modal_edit_tipo_evento_social.querySelector('.modal-title')
      const modal_tes_id = modal_edit_tipo_evento_social.querySelector('.tes_id')
      const modal_tes_evento_social = modal_edit_tipo_evento_social.querySelector('.tes_evento_social')
      const modal_tes_status = modal_edit_tipo_evento_social.querySelector('.tes_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_tes_id.value = tes_id
      modal_tes_evento_social.value = tes_evento_social
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (tes_status === '1') {
        modal_tes_status.checked = true;
      } else {
        modal_tes_status.checked = false;
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>