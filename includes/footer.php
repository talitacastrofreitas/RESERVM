<footer class="footer">
  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-8 text-truncate">
        <p class="m-0">
          <script>
            document.write(new Date().getFullYear())
          </script> © RESERVM <span class="d-none d-lg-inline-block"> - Sistema de Gestão de Atividades de Extensão
            Bahiana. Todos os direitos reservados.</span>
        </p>
      </div>
      <div class="col-4">
        <div class="text-end">
          <img src="assets/img/logo_bahiana_footer.png" alt="" class="img-fluid">
        </div>
      </div>
    </div>
  </div>
</footer>
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->



<!--start back-to-top-->
<button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
  <i class="ri-arrow-up-line"></i>
</button>
<!--end back-to-top-->

<!-- JAVASCRIPT -->
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/node-waves/waves.min.js"></script>
<script src="assets/libs/feather-icons/feather.min.js"></script>
<script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="assets/js/plugins.js"></script>

<!-- prismjs plugin -->
<script src="assets/libs/prismjs/prism.js"></script>

<!-- notifications init -->
<script src="assets/js/pages/notifications.init.js"></script>

<!-- SWEET ALERTS -->
<script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>

<!-- ALERTAS -->
<script>
  // EXCLUIR
  $('.del-btn').on('click', function (e) {
    e.preventDefault();
    const href = $(this).attr('href')
    Swal.fire({
      text: 'Deseja excluir este registro?',
      // title: "You won't be able to revert this!",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#0461AD',
      cancelButtonColor: '#C4453E',
      confirmButtonText: 'Excluir',
      cancelButtonText: 'Cancelar',
      // reverseButtons: true
    }).then((result) => {
      if (result.value) {
        document.location.href = href;
      }
    })
  })

  // EXCLUIR CONTA
  $('.del-btn-conta').on('click', function (e) {
    e.preventDefault();
    const href = $(this).attr('href')
    Swal.fire({
      title: "Deseja excluir sua conta?",
      text: 'Depois de excluir sua conta, não há como voltar atrás. Por favor, tenha certeza!',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#285FAB',
      cancelButtonColor: '#C4453E',
      confirmButtonText: 'Excluir',
      cancelButtonText: 'Cancelar',
      //reverseButtons: true
    }).then((result) => {
      if (result.value) {
        document.location.href = href;
      }
    })
  })
</script>


<!-- MASCARA -->
<script src="assets/js/mask/jquery.mask.min.js"></script>
<script src="assets/js/mask/mask.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<!-- DATATABLE JS -->
<script src="assets/js/datatable/jquery.dataTables.min.js"></script>
<script src="assets/js/datatable/dataTables.responsive.min.js"></script>
<script src="assets/js/pages/datatables.init.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>

<!-- APP JS -->
<script src="assets/js/app.js"></script>

<!-- VALIDA FORMULÁRIO -->
<script src="assets/js/valida_form.js"></script>

<!-- SELECT2 -->
<script src="assets/js/371.jquery.min.js"></script>
<script src="assets/js/select2.min.js"></script>

<!-- CONFIRMAÇÃO -->
<?php if (isset($_SESSION["msg"])) { ?>
  <script>
    Toastify({
      text: "<?= $_SESSION["msg"] ?>",
      duration: 10000, // Duração em milissegundos
      close: true, // Mostra o botão de fechar
      gravity: "top", // `top` ou `bottom`
      position: "center", // `left`, `center` ou `right`
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
      duration: 10000, // Duração em milissegundos
      close: true, // Mostra o botão de fechar
      gravity: "top", // `top` ou `bottom`
      position: "center", // `left`, `center` ou `right`
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
      duration: 10000, // Duração em milissegundos
      close: true, // Mostra o botão de fechar
      gravity: "top", // `top` ou `bottom`
      position: "center", // `left`, `center` ou `right`
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
      position: "center", // `left`, `center` ou `right`
      backgroundColor: "#F6993F", // Cor de fundo
      stopOnFocus: true, // Para a duração ao passar o mouse
    }).showToast();
  </script>
  <?php unset($_SESSION["atencao"]);
} ?>

<!-- LOADER DE PÁGINA -->
<script>
  function showPage() {
    document.body.style.opacity = "1"; // Mostra a página alterando a opacidade
  }
</script>

</body>

</html>