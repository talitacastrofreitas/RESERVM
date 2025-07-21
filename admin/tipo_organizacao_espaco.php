<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!empty($global_admin_id) && $global_admin_perfil != 1) {
  header("Location: sair.php");
}
?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-md-flex align-items-center justify-content-between">
      <h4 class="mb-md-0">Tipos de Organização de Espaço</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Tipos de Organização de Espaço</li>
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
          <div class="col-lg-6 text-lg-start text-center">
            <h5 class="card-title mb-0">Lista de Tipos de Organização de Espaço</h5>
          </div>
          <div class="col-lg-6 d-flex align-items-center d-flex justify-content-lg-end justify-content-center">
            <button class="btn botao botao_azul_escuro waves-effect mt-3 mt-lg-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_oranizacao_espaco">+ Cadastrar Tipo de Organização de Espaço</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_tipo_organizacao_espaco" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>Tipo de Organização de Espaço</th>
              <th>Status</th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM conf_tipo_espaco_organizacao
                                      INNER JOIN status ON status.st_id = conf_tipo_espaco_organizacao.esporg_status
                                      WHERE esporg_espaco_organizacao != 'OUTRO'");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $esporg_espaco_organizacao = htmlspecialchars($esporg_espaco_organizacao, ENT_QUOTES, 'UTF-8'); // EXIBE PALAVRAS COM ASPAS DUPLAS
                //CONFIGURAÇÃO DO STATUS
                $status_color = ($st_id == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= $esporg_espaco_organizacao ?></th>
                  <td><span class="badge <?= $status_color ?>"><?= $st_status ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_tipo_organizacao_espaco" data-bs-esporg_id="<?= $esporg_id ?>" data-bs-esporg_espaco_organizacao="<?= $esporg_espaco_organizacao ?>" data-bs-esporg_status="<?= $esporg_status ?>" title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="controller/controller_espaco_organizacao.php?funcao=exc_esporg&esporg_id=<?= $esporg_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_cad_oranizacao_espaco" tabindex="-1" aria-labelledby="modal_cad_oranizacao_espaco" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_oranizacao_espaco">Cadastrar Tipo de Organização de Espaço</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_espaco_organizacao.php" class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-12">
              <div>
                <label class="form-label">Organização do Espaço <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="esporg_espaco_organizacao" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="esporg_status" name="esporg_status" value="1" checked>
                <label class="form-check-label" for="esporg_status">Ativo</label>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadEspacoOrganizacao">Cadastrar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<div class="modal fade modal_padrao" id="modal_edit_tipo_organizacao_espaco" tabindex="-1" aria-labelledby="modal_edit_tipo_organizacao_espaco" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_tipo_organizacao_espaco">Editar Tipo de Organização de Espaço</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_espaco_organizacao.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control esporg_id" name="esporg_id">

            <div class="col-12">
              <div>
                <label class="form-label">Organização do Espaço <span>*</span></label>
                <input type="text" class="form-control text-uppercase esporg_espaco_organizacao" name="esporg_espaco_organizacao" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input esporg_status" type="checkbox" id="esporg_status" name="esporg_status" value="1" checked>
                <label class="form-check-label" for="esporg_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditEspacoOrganizacao">Atualizar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const modal_edit_tipo_organizacao_espaco = document.getElementById('modal_edit_tipo_organizacao_espaco')
  if (modal_edit_tipo_organizacao_espaco) {
    modal_edit_tipo_organizacao_espaco.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const esporg_id = button.getAttribute('data-bs-esporg_id')
      const esporg_espaco_organizacao = button.getAttribute('data-bs-esporg_espaco_organizacao')
      const esporg_status = button.getAttribute('data-bs-esporg_status')
      // 
      const modalTitle = modal_edit_tipo_organizacao_espaco.querySelector('.modal-title')
      const modal_esporg_id = modal_edit_tipo_organizacao_espaco.querySelector('.esporg_id')
      const modal_esporg_espaco_organizacao = modal_edit_tipo_organizacao_espaco.querySelector('.esporg_espaco_organizacao')
      const modal_esporg_status = modal_edit_tipo_organizacao_espaco.querySelector('.esporg_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_esporg_id.value = esporg_id
      modal_esporg_espaco_organizacao.value = esporg_espaco_organizacao
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (esporg_status === '1') {
        modal_esporg_status.checked = true;
      } else {
        modal_esporg_status.checked = false;
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>