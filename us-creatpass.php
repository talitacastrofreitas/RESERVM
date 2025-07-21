<?php include 'includes/header_registro.php'; ?>
<?php
// ACESSO RESTRITO SE NÃO ENCONTRAR A SESSÃO 
if (!isset($_SESSION['user_id_cript'])) {
  header("Location: us-forgot-pass.php");
  exit();
}
?>

<div class="card card_user_registro">
  <div class="card-body">

    <div class="header_card_login text-center mb-4">
      <p>Agora, vamos cadastrar sua nova senha.</p>
    </div>

    <form class="needs-validation" method="POST" action="router/web.php?r=UserUpdPass" id="UserRegistro" novalidate>

      <input type="hidden" class="form-control" value="<?= $_SESSION['user_id_cript'] ?>" name="i" required>
      <input type="hidden" class="form-control" name="acao" value="password" required>

      <div class="mb-3">
        <label class="form-label" for="pwd">Senha</label>
        <div class="position-relative auth-pass-inputgroup mb-3">
          <input type="password" class="form-control pe-5 password-input" id="pwd" name="senha" required>
          <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
          <div class="invalid-feedback">Digite a senha</div>
        </div>
      </div>

      <div id="criterios" class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert" style="display: none;">
        <i class="ri-error-warning-line label-icon"></i><strong>Erro</strong> - A senha deve atender os critérios!
        <!-- <button  type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
      </div>

      <p class="mb-0">Todas as marcas de seleção devem ficar verdes para prosseguir</p>
      <div id="password-info" class="password-info">
        <ul>
          <li id="length" class="invalid clearfix">
            <span class="icon-container"></span>
            Pelo menos 8 caracteres
          </li>
          <li id="capital" class="invalid clearfix">
            <span class="icon-container"></span>
            Pelo menos 1 letra maiúscula
          </li>
          <li id="letter" class="invalid clearfix">
            <span class="icon-container"></span>
            Pelo menos 1 letra minúscula
          </li>
          <li id="numb" class="invalid clearfix">
            <span class="icon-container"></span>
            Pelo menos 1 número
          </li>
          <li id="special" class="invalid clearfix">
            <span class="icon-container"></span>
            Pelo menos 1 <span><a data-tooltip="! @ # $ % ^ & *">caractere especial</a></span>
          </li>
        </ul>
      </div>

      <div class="mb-3">
        <label class="form-label" for="cpwd">Confirmar senha</label>
        <div class="position-relative auth-pass-inputgroup mb-3">
          <input type="password" class="form-control pe-5 password-input" id="cpwd" required>
          <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
          <div class="invalid-feedback">Confirme a senha</div>
        </div>
      </div>

      <div id="senha" class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert" style="display: none;">
        <i class="ri-error-warning-line label-icon"></i><strong>Erro</strong> - As senhas não correspondem!
        <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
      </div>

      <div class="mt-4">
        <button type="submit" class="btn botao botao_verde waves-effect waves-light w-100">Cadastrar</button>
      </div>

    </form>

  </div>
</div>

<div class="link_back_reg">
  <p class="mb-0">Lembrou sua senha? <a href="<?= $url_sistema ?>" class="link"> Clique aqui</a> </p>
</div>

<?php include 'includes/footer_registro.php'; ?>
<!-- VALIDA FORMULÁRIOS -->
<script src="assets/js/valida_form.js"></script>
<!-- VALIDA SENHA -->
<script src="assets/js/valid-password.js"></script>
<!-- PASSWORD ADDON INIT -->
<script src="assets/js/pages/password-addon.init.js"></script>