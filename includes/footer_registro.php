</div>
</div>
</div>
</div>
</div>

<footer class="footer_user_register">
  <img src="assets/images/logo_bahiana_registro.svg" alt="" width="180">
  <p class="mt-2" style="color: #808EA7;">&copy; <script>
      document.write(new Date().getFullYear())
    </script> RESERVM - Sistema de Reserva de Espaço Bahiana.<br>Todos os direitos reservados.</p>
</footer>

</div>
</div>


<!-- JAVASCRIPT -->
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/node-waves/waves.min.js"></script>
<script src="assets/libs/feather-icons/feather.min.js"></script>
<script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="assets/js/plugins.js"></script>
<!-- VALIDA FORMULÁRIOS -->
<script src="assets/js/valida_form.js"></script>
<!-- MASCARAS -->
<script src="assets/js/mask/311_jquery.min.js"></script>
<script src="assets/js/mask/jquery.mask.min.js"></script>
<script src="assets/js/mask/mask.js"></script>
<!-- SWEET ALERTA JS -->
<script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
<!-- APP -->
<script src="assets/js/app.js"></script>



<!-- CONFIRMAÇÃO -->
<?php if (isset($_SESSION["msg"])) { ?>
  <script>
    Toastify({
      text: "<?= $_SESSION["msg"] ?>",
      duration: 5000, // Duração em milissegundos
      close: true, // Mostra o botão de fechar
      gravity: "top", // `top` ou `bottom`
      position: "right", // `left`, `center` ou `right`
      backgroundColor: "#38C172", // Cor de fundo
      stopOnFocus: true, // Para a duração ao passar o mouse
    }).showToast();
  </script>
<?php unset($_SESSION["msg"]);
} ?>

<?php if (isset($_SESSION["erro"])) { ?>
  <script>
    Toastify({
      text: "<?= $_SESSION["erro"] ?>",
      duration: 5000, // Duração em milissegundos
      close: true, // Mostra o botão de fechar
      gravity: "top", // `top` ou `bottom`
      position: "right", // `left`, `center` ou `right`
      backgroundColor: "#C4453E", // Cor de fundo
      stopOnFocus: true, // Para a duração ao passar o mouse
    }).showToast();
  </script>
<?php unset($_SESSION["erro"]);
} ?>

<?php if (isset($_SESSION["rest"])) { ?>
  <script>
    Toastify({
      text: "<?= $_SESSION["rest"] ?>",
      duration: 5000, // Duração em milissegundos
      close: true, // Mostra o botão de fechar
      gravity: "top", // `top` ou `bottom`
      position: "right", // `left`, `center` ou `right`
      backgroundColor: "#F6993F", // Cor de fundo
      stopOnFocus: true, // Para a duração ao passar o mouse
    }).showToast();
  </script>
<?php unset($_SESSION["rest"]);
} ?>

<?php if (isset($_SESSION["atencao"])) { ?>
  <script>
    Toastify({
      text: "<?= $_SESSION["atencao"] ?>",
      duration: 5000, // Duração em milissegundos
      close: true, // Mostra o botão de fechar
      gravity: "top", // `top` ou `bottom`
      position: "right", // `left`, `center` ou `right`
      backgroundColor: "#F6993F", // Cor de fundo
      stopOnFocus: true, // Para a duração ao passar o mouse
    }).showToast();
  </script>
<?php unset($_SESSION["atencao"]);
} ?>

<script>
  function showPage() {
    document.body.style.opacity = "1"; // Mostra a página alterando a opacidade
  }
</script>
</body>

</html>