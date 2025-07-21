<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!empty($global_admin_id) && $global_admin_perfil != 1) {
  header("Location: sair.php");
  exit();
}
?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Administradores</h4>

      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Administradores</li>
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
            <h5 class="card-title mb-0">Lista de Administradores</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_admin">+ Cadastrar Administrador</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_admin" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-3">Matrícula</span></th>
              <th><span class="me-3">Nome</span></th>
              <th><span class="me-3">E-mail</span></th>
              <th><span class="me-3">Perfil</span></th>
              <th><span class="me-3">Último Acesso</span></th>
              <th><span class="me-3">Status</span></th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT admin_id, admin_nome, admin_email, admin_matricula, admin_perfil, adm_perfil, admin_status, adm_status, admin_data_acesso
                                      FROM admin
                                      INNER JOIN admin_perfil ON adm_perfil_id = admin_perfil
                                      INNER JOIN admin_status ON adm_status_id = admin_status
                                      WHERE nivel_acesso NOT IN (0) AND admin_status NOT IN (2)");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $admin_id          = $row['admin_id'];
                $admin_nome        = $row['admin_nome'];
                $admin_email       = $row['admin_email'];
                $admin_matricula   = $row['admin_matricula'];
                $admin_perfil      = $row['admin_perfil'];
                $adm_perfil        = $row['adm_perfil'];
                $admin_status      = $row['admin_status'];
                $adm_status        = $row['adm_status'];
                $admin_data_acesso = $row['admin_data_acesso'];

                //CONFIGURAÇÃO DO PERFIL
                $perfil_color = ($admin_perfil == 1) ? 'bg_info_azul_escuro' : 'bg_info_laranja';

                //CONFIGURAÇÃO DO STATUS
                $status_color = ($admin_status == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= htmlspecialchars($admin_matricula) ?></th>
                  <td><?= htmlspecialchars($admin_nome) ?></td>
                  <td><?= htmlspecialchars($admin_email) ?></td>
                  <td><span class="badge <?= $perfil_color ?>"><?= htmlspecialchars($adm_perfil) ?></span></td>
                  <?php if (!empty($admin_data_acesso)) { ?>
                    <td><span class="hide_data"><?= date('Ymd H:i', strtotime($admin_data_acesso)) ?></span><?= htmlspecialchars(date('d/m/Y H:i', strtotime($admin_data_acesso))) ?></td>
                  <?php } else { ?>
                    <td><span class="badge bg_info_cinza">SEM ACESSO</span></td>
                  <?php } ?>
                  <td><span class="badge <?= $status_color ?>"><?= htmlspecialchars($adm_status) ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_admin"
                            data-bs-admin_id="<?= htmlspecialchars($admin_id) ?>"
                            data-bs-admin_matricula="<?= htmlspecialchars($admin_matricula) . ' - ' . htmlspecialchars($admin_nome) ?>"
                            data-bs-admin_email="<?= htmlspecialchars($admin_email) ?>"
                            data-bs-admin_perfil="<?= htmlspecialchars($admin_perfil) ?>"
                            data-bs-admin_status="<?= htmlspecialchars($admin_status) ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <?php if ($global_admin_id != $admin_id) { ?>

                          <?php if (empty($admin_data_acesso)) { ?>
                            <li><a href="../router/web.php?r=Admin&acao=deletar&admin_id=<?= $admin_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                          <?php } else { ?>
                            <li><span class="dropdown-item edit-item-block-btn" title="Excluir" disabled><i class="fa-regular fa-trash-can me-2"></i> Excluir</span></li>
                          <?php } ?>

                        <?php } ?>
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
<div class="modal fade modal_padrao" id="modal_cad_admin" tabindex="-1" aria-labelledby="modal_cad_admin" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_admin">Cadastrar Administrador</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Admin" class="needs-validation" id="ValidaBotaoProgress" novalidate>
          <div class="row g-3">

            <input type="hidden" name="acao" value="cadastrar">

            <div class="col-12">
              <label class="form-label">Nome <span>*</span></label>
              <select class="form-select" name="admin_matricula_nome" id="cad_admin_matricula" required>
                <option selected disabled value=""></option>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <label class="form-label">E-mail <span>*</span></label>
              <input type="email" class="form-control text-lowercase" name="admin_email" id="cad_admin_email" readonly>
            </div>

            <div class="col-12">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM admin_perfil");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Perfil <span>*</span></label>
              <select class="form-select" name="admin_perfil" required>
                <option selected disabled value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['adm_perfil_id'] ?>" <?= ($_SESSION['form_admin']['admin_perfil'] ?? '') == $res['adm_perfil_id'] ? 'selected' : '' ?>><?= $res['adm_perfil'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="cad_admin_status" name="admin_status" value="1" <?= (!isset($_SESSION['form_admin']) || !empty($_SESSION['form_admin']['admin_status'])) ? 'checked' : '' ?>>
                <label class="form-check-label" for="cad_admin_status">Acesso permitido</label>
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
$formData = $_SESSION['form_admin'] ?? [];
unset($_SESSION['form_admin']);
?>

<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_admin" tabindex="-1" aria-labelledby="modal_edit_admin" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_admin">Cadastrar Administrador</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Admin" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control admin_id" name="admin_id" required>
            <input type="hidden" name="acao" value="atualizar">

            <div class="col-12">
              <label class="form-label">Nome</label>
              <input type="text" class="form-control text-uppercase admin_matricula" name="" disabled>
            </div>

            <div class="col-12">
              <label class="form-label">E-mail</label>
              <input type="email" class="form-control text-lowercase admin_email" name="" disabled>
            </div>

            <div class="col-12">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM admin_perfil");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Perfil</label>
              <select class="form-select admin_perfil" name="admin_perfil">
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['adm_perfil_id'] ?>"><?= $res['adm_perfil'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input admin_status" type="checkbox" id="edit_admin_status" name="admin_status" value="1" checked>
                <label class="form-check-label" for="edit_admin_status">Acesso permitido</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
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
  const modal_edit_admin = document.getElementById('modal_edit_admin')
  if (modal_edit_admin) {
    modal_edit_admin.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const admin_id = button.getAttribute('data-bs-admin_id')
      const admin_matricula = button.getAttribute('data-bs-admin_matricula')
      const admin_email = button.getAttribute('data-bs-admin_email')
      const admin_perfil = button.getAttribute('data-bs-admin_perfil')
      const admin_status = button.getAttribute('data-bs-admin_status')
      // 
      const modalTitle = modal_edit_admin.querySelector('.modal-title')
      const modal_admin_id = modal_edit_admin.querySelector('.admin_id')
      const modal_admin_matricula = modal_edit_admin.querySelector('.admin_matricula')
      const modal_admin_email = modal_edit_admin.querySelector('.admin_email')
      const modal_admin_perfil = modal_edit_admin.querySelector('.admin_perfil')
      const modal_admin_status = modal_edit_admin.querySelector('.admin_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_admin_id.value = admin_id
      modal_admin_matricula.value = admin_matricula
      modal_admin_email.value = admin_email
      modal_admin_perfil.value = admin_perfil
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (admin_status === '1') {
        modal_admin_status.checked = true;
      } else {
        modal_admin_status.checked = false;
      }
      // ESTE SELETOR LEVA O DADO DA BASE PARA O CAMPO SELECT2
      $('#edit_admin_matricula').val(admin_matricula).trigger('change');
    })
  }
</script>

<!-- ITENS DOS SELECTS -->
<script src="../assets/js/351.jquery.min.js"></script>
<script src="includes/select/select_colaboradores.js"></script>
<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- COMPLETO FORM -->
<script src="includes/select/completa_form.js"></script>
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>