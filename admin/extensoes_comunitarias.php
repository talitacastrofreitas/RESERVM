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
      <h4 class="mb-lg-0">Programas de Extenão Comunitária</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Programas de Extenão Comunitária</li>
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
            <h5 class="card-title mb-0">Lista de Programas de Extenão Comunitária</h5>
          </div>
          <div class="col-md-6 d-flex align-items-center d-flex justify-content-md-end justify-content-center">
            <button class="btn botao botao_azul_escuro waves-effect mt-3 mt-md-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_extensao_comunitaria">+ Cadastrar Programa</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_extensao_comunitaria" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>Programa</th>
              <th>Descrição</th>
              <th>Status</th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM conf_extensao_comunitaria
                                      INNER JOIN status ON status.st_id = conf_extensao_comunitaria.cec_status");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $cec_extensao_comunitaria = htmlspecialchars($cec_extensao_comunitaria, ENT_QUOTES, 'UTF-8'); // EXIBE PALAVRAS COM ASPAS DUPLAS
                $cec_desc                 = htmlspecialchars($cec_desc, ENT_QUOTES, 'UTF-8'); // EXIBE PALAVRAS COM ASPAS DUPLAS

                //CONFIGURAÇÃO DO STATUS
                $status_color = ($st_id == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= $cec_extensao_comunitaria ?></th>
                  <td><?= $cec_desc ?></td>
                  <td><span class="badge <?= $status_color ?>"><?= $st_status ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_prog_extensao_comunitaria" data-bs-cec_id="<?= $cec_id ?>" data-bs-cec_extensao_comunitaria="<?= $cec_extensao_comunitaria ?>" data-bs-cec_desc="<?= $cec_desc ?>" data-bs-cec_status="<?= $cec_status ?>" title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="controller/controller_extensao_comunitaria.php?funcao=exc_cec&cec_id=<?= $cec_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_cad_extensao_comunitaria" tabindex="-1" aria-labelledby="modal_cad_extensao_comunitaria" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_extensao_comunitaria">Cadastrar Programa</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_extensao_comunitaria.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <div class="col-12">
              <div>
                <label class="form-label">Programa de Extensão Comunitária <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="cec_extensao_comunitaria" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">Descrição do Programa <span>*</span></label>
                <textarea class="form-control text-lowercase" name="cec_desc" id="cec_desc_cad" rows="3" required></textarea>
                <p class="label_info text-end mt-1 mb-0">Caracteres restantes: <span id="charCountCad">200</span></p>
                <div class="invalid-feedback mt-n3">Este campo é obrigatório</div>
              </div>
              <script>
                const cec_desc_cad = document.getElementById('cec_desc_cad');
                const charCountCad = document.getElementById('charCountCad');
                const maxCharsCad = 200; // Número máximo de caracteres

                cec_desc_cad.addEventListener('input', function() {
                  const remainingChars = maxCharsCad - cec_desc_cad.value.length;
                  charCountCad.textContent = remainingChars;

                  if (remainingChars < 0) {
                    charCountCad.style.color = 'red';
                  } else {
                    charCountCad.style.color = 'black';
                  }

                  if (cec_desc_cad.value.length > maxCharsCad) {
                    cec_desc_cad.value = cec_desc_cad.value.substring(0, maxCharsCad);
                  }
                });
              </script>
            </div>

            <div class="col-12 mt-n1">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="cec_status_cad" name="cec_status" value="1" checked>
                <label class="form-check-label" for="cec_status_cad">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadExtensaoComunitaria">Cadastrar</button>
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
        <h5 class="modal-title" id="modal_edit_prog_extensao_comunitaria">Editar Área do Conhecimento</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_extensao_comunitaria.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control cec_id" name="cec_id">

            <div class="col-12">
              <div>
                <label class="form-label">Programa de Extensão Comunitária <span>*</span></label>
                <input type="text" class="form-control text-uppercase cec_extensao_comunitaria" name="cec_extensao_comunitaria" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">Descrição do Programa <span>*</span></label>
                <textarea class="form-control text-lowercase cec_desc" name="cec_desc" id="cec_desc_edit" rows="3" required></textarea>
                <p class="label_info text-end mt-1 mb-0">Caracteres restantes: <span id="charCountEdit">200</span></p>
                <div class="invalid-feedback mt-n3">Este campo é obrigatório</div>
              </div>
              <script>
                const cec_desc_edit = document.getElementById('cec_desc_edit');
                const charCountEdit = document.getElementById('charCountEdit');
                const maxCharsEdit = 200; // Número máximo de caracteres	
                cec_desc_edit.addEventListener('input', function() {
                  const remainingCharsEdit = maxCharsEdit - cec_desc_edit.value.length;
                  charCountEdit.textContent = remainingCharsEdit;

                  if (remainingCharsEdit < 0) {
                    charCountEdit.style.color = 'red';
                  } else {
                    charCountEdit.style.color = 'black';
                  }

                  if (cec_desc_edit.value.length > maxCharsEdit) {
                    cec_desc_edit.value = cec_desc_edit.value.substring(0, maxCharsEdit);
                  }
                });
              </script>
            </div>

            <div class="col-12 mt-n1">
              <div class="form-check">
                <input class="form-check-input cec_status" type="checkbox" id="cec_status" name="cec_status" value="1" checked>
                <label class="form-check-label" for="cec_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditExtensaoComunitaria">Atualizar</button>
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
      const cec_id = button.getAttribute('data-bs-cec_id')
      const cec_extensao_comunitaria = button.getAttribute('data-bs-cec_extensao_comunitaria')
      const cec_desc = button.getAttribute('data-bs-cec_desc')
      const cec_status = button.getAttribute('data-bs-cec_status')
      // 
      const modalTitle = modal_edit_prog_extensao_comunitaria.querySelector('.modal-title')
      const modal_cec_id = modal_edit_prog_extensao_comunitaria.querySelector('.cec_id')
      const modal_cec_extensao_comunitaria = modal_edit_prog_extensao_comunitaria.querySelector('.cec_extensao_comunitaria')
      const modal_cec_desc = modal_edit_prog_extensao_comunitaria.querySelector('.cec_desc')
      const modal_cec_status = modal_edit_prog_extensao_comunitaria.querySelector('.cec_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_cec_id.value = cec_id
      modal_cec_extensao_comunitaria.value = cec_extensao_comunitaria
      modal_cec_desc.value = cec_desc
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (cec_status === '1') {
        modal_cec_status.checked = true;
      } else {
        modal_cec_status.checked = false;
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>