<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!empty($global_admin_id) && $global_admin_perfil != 1) {
  header("Location: sair.php");
}
?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-lg-flex align-items-center justify-content-between">
      <h4 class="mb-lg-0">Tipos de Programas</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Tipos de Programas</li>
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
          <div class="col-md-6 text-md-start text-center">
            <h5 class="card-title mb-0">Lista de Tipos de Programas</h5>
          </div>
          <div class="col-md-6 d-flex align-items-center d-flex justify-content-md-end justify-content-center">
            <button class="btn botao botao_azul_escuro waves-effect mt-3 mt-md-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_tipo_programa">+ Cadastrar Tipo de Programa</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_tipo_programa" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>Tipo de Programa</th>
              <th>Categoria</th>
              <th>Status</th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM conf_tipo_programa
                                      INNER JOIN status ON status.st_id = conf_tipo_programa.ctp_status");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $ctp_tipo      = htmlspecialchars($ctp_tipo, ENT_QUOTES, 'UTF-8'); // EXIBE PALAVRAS COM ASPAS DUPLAS
                $ctp_categoria = htmlspecialchars($ctp_categoria, ENT_QUOTES, 'UTF-8'); // EXIBE PALAVRAS COM ASPAS DUPLAS

                //CONFIGURAÇÃO DO STATUS
                $status_color = ($st_id == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= $ctp_tipo ?></th>
                  <td><?= $ctp_categoria ?></td>
                  <td><span class="badge <?= $status_color ?>"><?= $st_status ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_prog_extensao_comunitaria" data-bs-ctp_id="<?= $ctp_id ?>" data-bs-ctp_tipo="<?= $ctp_tipo ?>" data-bs-ctp_categoria="<?= $ctp_categoria ?>" data-bs-ctp_status="<?= $ctp_status ?>" title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="controller/controller_tipo_programa.php?funcao=exc_ctp&ctp_id=<?= $ctp_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_cad_tipo_programa" tabindex="-1" aria-labelledby="modal_cad_tipo_programa" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_tipo_programa">Cadastrar Tipo de Programa</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_tipo_programa.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <div class="col-12">
              <div>
                <label class="form-label">Tipo de Programa <span>*</span></label>
                <select class="form-select text-uppercase" name="ctp_tipo" required>
                  <option selected value=""></option>
                  <option value="PET">PET</option>
                  <option value="PEC">PEC</option>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div>
                <div class="col-12 mb-3">
                  <label class="form-label">Categoria <span>*</span></label>
                  <input type="text" class="form-control text-uppercase" name="ctp_categoria" maxlength="50" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            </div>

            <div class="col-12 mt-0">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="ctp_status_cad" name="ctp_status" value="1" checked>
                <label class="form-check-label" for="ctp_status_cad">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadTipoPrograma">Cadastrar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<div class="modal fade modal_padrao" id="modal_edit_prog_extensao_comunitaria" tabindex="-1" aria-labelledby="modal_edit_prog_extensao_comunitaria" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_prog_extensao_comunitaria">Editar Tipo de Programa</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_tipo_programa.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control ctp_id" name="ctp_id">

            <div class="col-12">
              <div>
                <label class="form-label">Tipo de Programa <span>*</span></label>
                <select class="form-select text-uppercase ctp_tipo" name="ctp_tipo" required>
                  <option value="PET">PET</option>
                  <option value="PEC">PEC</option>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div>
                <div>
                  <div class="col-12 mb-3">
                    <label class="form-label">Categoria <span>*</span></label>
                    <input type="text" class="form-control text-uppercase ctp_categoria" name="ctp_categoria" maxlength="50" required>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 mt-0">
              <div class="form-check">
                <input class="form-check-input ctp_status" type="checkbox" id="ctp_status" name="ctp_status" value="1" checked>
                <label class="form-check-label" for="ctp_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditTipoPrograma">Atualizar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const modal_edit_prog_extensao_comunitaria = document.getElementById('modal_edit_prog_extensao_comunitaria')
  if (modal_edit_prog_extensao_comunitaria) {
    modal_edit_prog_extensao_comunitaria.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const ctp_id = button.getAttribute('data-bs-ctp_id')
      const ctp_tipo = button.getAttribute('data-bs-ctp_tipo')
      const ctp_categoria = button.getAttribute('data-bs-ctp_categoria')
      const ctp_status = button.getAttribute('data-bs-ctp_status')
      // 
      const modalTitle = modal_edit_prog_extensao_comunitaria.querySelector('.modal-title')
      const modal_ctp_id = modal_edit_prog_extensao_comunitaria.querySelector('.ctp_id')
      const modal_ctp_tipo = modal_edit_prog_extensao_comunitaria.querySelector('.ctp_tipo')
      const modal_ctp_categoria = modal_edit_prog_extensao_comunitaria.querySelector('.ctp_categoria')
      const modal_ctp_status = modal_edit_prog_extensao_comunitaria.querySelector('.ctp_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_ctp_id.value = ctp_id
      modal_ctp_tipo.value = ctp_tipo
      modal_ctp_categoria.value = ctp_categoria
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (ctp_status === '1') {
        modal_ctp_status.checked = true;
      } else {
        modal_ctp_status.checked = false;
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>