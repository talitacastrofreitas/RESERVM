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
      <h4 class="mb-sm-0">Materiais e Serviços</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Materiais e Serviços</li>
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
            <h5 class="card-title mb-0">Lista de Materiais e Serviços</h5>
          </div>
          <div class="col-md-6 d-flex align-items-center d-flex justify-content-md-end justify-content-center">
            <button class="btn botao botao_azul_escuro waves-effect mt-3 mt-md-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_material_servico">+ Cadastrar Material ou Serviço</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_material_servico" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>Item</th>
              <th>Natureza Material</th>
              <th>Valor Unitário</th>
              <th>Status</th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM conf_material_servico
                                      INNER JOIN natureza_material ON natureza_material.nm_id = conf_material_servico.cms_natureza
                                      INNER JOIN status ON status.st_id = conf_material_servico.cms_status");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $cms_material_servico = htmlspecialchars($cms_material_servico, ENT_QUOTES, 'UTF-8'); // EXIBE PALAVRAS COM ASPAS DUPLAS

                //CONFIGURAÇÃO DO STATUS
                $status_color = ($st_id == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= $cms_material_servico ?></th>
                  <td><?= $nm_natureza_material  ?></td>
                  <?php if (empty($cms_valor)) { ?>
                    <td> <span class="badge bg_info_azul_escuro">NÃO SE APLICA</span></td>
                  <?php } else { ?>
                    <td>R$ <?= $cms_valor ?></td>
                  <?php } ?>
                  <td><span class="badge <?= $status_color ?>"><?= $st_status ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_material_servico" data-bs-cms_id="<?= $cms_id ?>"
                            data-bs-cms_material_servico="<?= $cms_material_servico ?>"
                            data-bs-cms_natureza="<?= $cms_natureza ?>"
                            data-bs-cms_valor="<?= $cms_valor ?>"
                            data-bs-cms_status="<?= $cms_status ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="controller/controller_material_servico.php?funcao=exc_cms&cms_id=<?= $cms_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_cad_material_servico" tabindex="-1" aria-labelledby="modal_cad_material_servico" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_material_servico">Cadastrar Material ou Serviço</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_material_servico.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <div class="col-12">
              <div>
                <label class="form-label">Natureza do Material <span>*</span></label>
                <select class="form-select text-uppercase" name="cms_natureza" required>
                  <option selected disabled value=""></option>
                  <?php $sql = $conn->query("SELECT nm_id, nm_natureza_material FROM natureza_material ORDER BY nm_natureza_material");
                  while ($nm = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $nm['nm_id'] ?>"><?= $nm['nm_natureza_material'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">Material ou Serviço <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="cms_material_servico" maxlength="100" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="cms_valor_add_cad" name="cms_valor_add" value="1" onchange="mostraCampoValorCad()">
                <label class="form-check-label" for="cms_valor_add_cad">Possui valor unitário</label>
              </div>
            </div>

            <div class="col-12 mb-2" id="campo_cms_valor_cad" style="display: none;">
              <div>
                <label class="form-label">Valor Unitário <span>*</span></label>
                <input type="text" class="form-control money" name="cms_valor" id="cms_valor_cad" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>

              <script>
                function mostraCampoValorCad() {
                  const checkbox = document.getElementById('cms_valor_add_cad');
                  const campo_cms_valor_cad = document.getElementById('campo_cms_valor_cad');
                  document.getElementById('cms_valor_cad').required = false;
                  if (checkbox.checked) {
                    campo_cms_valor_cad.style.display = 'block';
                    document.getElementById('cms_valor_cad').required = true;
                  } else {
                    campo_cms_valor_cad.style.display = 'none';
                    document.getElementById('cms_valor_cad').required = false;
                    document.getElementById("cms_valor_cad").value = "";
                  }
                }
              </script>
            </div>

            <div class="col-12 mt-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="cms_status" name="cms_status" value="1" checked>
                <label class="form-check-label" for="cms_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadMaterialServico">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<div class="modal fade modal_padrao" id="modal_edit_material_servico" tabindex="-1" aria-labelledby="modal_edit_material_servico" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_material_servico">Editar Curso</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_material_servico.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control cms_id" name="cms_id">

            <div class="col-12">
              <div>
                <label class="form-label">Natureza do Material <span>*</span></label>
                <select class="form-select text-uppercase cms_natureza" name="cms_natureza" required>
                  <?php $sql = $conn->query("SELECT nm_id, nm_natureza_material FROM natureza_material ORDER BY nm_natureza_material");
                  while ($nm = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $nm['nm_id'] ?>"><?= $nm['nm_natureza_material'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">Material ou Serviço <span>*</span></label>
                <input type="text" class="form-control text-uppercase cms_material_servico" name="cms_material_servico" maxlength="100" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check campo_check">
                <input class="form-check-input cms_valor_add" type="checkbox" name="cms_valor_add" value="1" id="cms_valor_add" onchange="mostraCampoValor()">
                <label class="form-check-label form-label" for="cms_valor_add">Adicionar valor unitário</label>
              </div>
            </div>

            <div class="col-md-12 mb-2" id="campo_cms_valor" style="display: none;">
              <label class="form-label">Valor Unitário <span>*</span></label>
              <input type="text" class="form-control money cms_valor" name="cms_valor" id="cms_valor" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <script>
              function mostraCampoValor() {
                const checkbox = document.getElementById('cms_valor_add');
                const campo_cms_valor = document.getElementById('campo_cms_valor');
                document.getElementById('cms_valor').required = false;
                if (checkbox.checked) {
                  campo_cms_valor.style.display = 'block';
                  document.getElementById('cms_valor').required = true;
                } else {
                  campo_cms_valor.style.display = 'none';
                  document.getElementById('cms_valor').required = false;
                  document.getElementById("cms_valor").value = "";
                }
              }
            </script>

            <div class="col-12 mt-2">
              <div class="form-check">
                <input class="form-check-input cms_status" type="checkbox" id="cms_status" name="cms_status" value="1" checked>
                <label class="form-check-label" for="cms_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditMaterialServico">Atualizar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const modal_edit_material_servico = document.getElementById('modal_edit_material_servico')
  if (modal_edit_material_servico) {
    modal_edit_material_servico.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const cms_id = button.getAttribute('data-bs-cms_id')
      const cms_material_servico = button.getAttribute('data-bs-cms_material_servico')
      const cms_natureza = button.getAttribute('data-bs-cms_natureza')
      const cms_valor_add = button.getAttribute('data-cms_valor_add')
      const cms_valor = button.getAttribute('data-bs-cms_valor')
      const cms_status = button.getAttribute('data-bs-cms_status')
      // 
      const modalTitle = modal_edit_material_servico.querySelector('.modal-title')
      const modal_cms_id = modal_edit_material_servico.querySelector('.cms_id')
      const modal_cms_material_servico = modal_edit_material_servico.querySelector('.cms_material_servico')
      const modal_cms_natureza = modal_edit_material_servico.querySelector('.cms_natureza')
      const modal_cms_valor_add = modal_edit_material_servico.querySelector('.cms_valor_add')
      const modal_cms_valor = modal_edit_material_servico.querySelector('.cms_valor')
      const modal_cms_status = modal_edit_material_servico.querySelector('.cms_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_cms_id.value = cms_id
      modal_cms_material_servico.value = cms_material_servico
      modal_cms_natureza.value = cms_natureza
      modal_cms_valor_add.value = cms_valor_add
      modal_cms_valor.value = cms_valor
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      const campo_cms_valor = document.getElementById('campo_cms_valor');
      if (cms_valor) {
        campo_cms_valor.style.display = 'block';
        modal_cms_valor_add.checked = true;
      } else {
        campo_cms_valor.style.display = 'none';
        modal_cms_valor_add.checked = false;
      }
      //
      if (cms_status === '1') {
        modal_cms_status.checked = true;
      } else {
        modal_cms_status.checked = false;
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>