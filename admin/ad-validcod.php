<?php include 'includes/header_login.php'; ?>

<?php
// ACESSO RESTRITO SE NÃO ENCONTRAR A SESSÃO 
if (!isset($_SESSION['admin_id_cript_cod']) && !isset($_GET['tk'])) {
  header("Location: ad-forgot-pass.php");
  exit();
}

if (isset($_SESSION['admin_id_cript_cod'])) {
  $tk = $_SESSION['admin_id_cript_cod'];
} else {
  $tk = $_GET['tk'];
}
?>

<style>
  .form-control.is-invalid,
  .was-validated .form-control:invalid,
  .form-select.is-invalid,
  .was-validated .form-select:invalid {
    border-color: var(--vermelho) !important;
  }
</style>

<div class="row justify-content-center align-items-center flex-column">
  <div class="card card_login">
    <div class="card-body px-3 px-sm-4 py-4">

      <div class="header_card_login text-center mb-4">
        <h5>Verificação de segurança</h5>
        <p>Digite o código de segurança que enviamos para seu e-mail cadastrado</p>
      </div>

      <form class="needs-validation" method="POST" action="../router/web.php?r=valcod" id="codigoForm" novalidate>

        <input type="hidden" class="form-control form-control-codigo" value="<?= htmlspecialchars($tk) ?>" name="cod" required>

        <div class="mb-2">
          <label class="form-label">Código de segurança</label>
          <div class="row g-2">
            <div class="col">
              <input type="text" id="input1" class="form-control form-control-codigo text-center cod_jump" name="cod1" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="1" required>
            </div>
            <div class="col">
              <input type="text" id="input2" class="form-control form-control-codigo text-center cod_jump" name="cod2" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="1" required>
            </div>
            <div class="col">
              <input type="text" id="input3" class="form-control form-control-codigo text-center cod_jump" name="cod3" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="1" required>
            </div>
            <div class="col">
              <input type="text" id="input4" class="form-control form-control-codigo text-center cod_jump" name="cod4" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="1" required>
            </div>
            <div class="col">
              <input type="text" id="input5" class="form-control form-control-codigo text-center cod_jump" name="cod5" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="1" required>
            </div>
            <div class="col">
              <input type="text" id="input6" class="form-control form-control-codigo text-center cod_jump" name="cod6" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="1" required>
            </div>
            <div class="col">
              <input type="text" id="input7" class="form-control form-control-codigo text-center cod_jump" name="cod7" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="1" required>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn botao botao_verde_musgo waves-effect waves-light w-100" name="ValCod">Validar código</button>
        </div>

      </form>

      <div class="mt-4 text-center">
        <p class="mb-0"><a href="../admin" class="link"> Voltar para o login</a> </p>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer_login.php'; ?>
<!-- maxlength -->
<script src="../assets/js/maxlength-cod.js"></script>
<!-- VALIDA FORMULÁRIOS -->
<script src="../assets/js/valida_form.js"></script>