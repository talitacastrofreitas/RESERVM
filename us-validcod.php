<?php include 'includes/header_registro.php'; ?>

<?php
// ACESSO RESTRITO SE NÃO ENCONTRAR A SESSÃO 
if (!isset($_SESSION['user_val_cod']) && !isset($_GET['i'])) {
  header("Location: $url_sistema");
  exit();
}
$i = $_SESSION['user_val_cod'] ?? $_GET['i'];
?>

<div class="card card_user_registro">
  <div class="card-body">

    <div class="header_card_login text-center mb-4">
      <h5>Verificação de segurança</h5>
      <p>Digite o código de segurança que enviamos para seu e-mail cadastrado</p>
    </div>

    <form class="needs-validation" method="POST" action="router/web.php?r=UserValcod" id="codigoForm" novalidate>

      <input type="hidden" class="form-control form-control-codigo" value="<?= $i ?>" name="i" required>
      <input type="hidden" class="form-control" name="acao" value="validar" required>

      <style>
        .form-control.is-invalid,
        .was-validated .form-control:invalid,
        .form-select.is-invalid,
        .was-validated .form-select:invalid {
          border-color: var(--vermelho) !important;
        }
      </style>

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
        <button type="submit" class="btn botao botao_verde waves-effect waves-light w-100">Validar código</button>
      </div>

    </form>

    <div class="link_back_reg mb-0">
      <span class="mb-0 d-inline-flex">Não recebeu o código? <div class="text-muted ms-1" id="contador"></div></span>
    </div>

    <?php
    // BOSCA OS DADOS DE TOKEN PARA EXIBIR O TEMPO QUE EXPIRA
    $stmt = $conn->prepare("SELECT tok_data_valida FROM token WHERE tok_user_id = :tok_user_id");
    $stmt->execute([':tok_user_id' => $i]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //
    $expira_em = $row['tok_data_valida']; // exemplo: '2025-06-01 14:35:00'
    $expira_em_timestamp = strtotime($expira_em);
    $agora_timestamp = time();
    $tempo_restante = max($expira_em_timestamp - $agora_timestamp, 0);
    ?>

    <script>
      var tempoRestante = <?= $tempo_restante ?>; // <-- vindo do PHP

      function iniciarContador() {
        var contador = setInterval(function() {
          var minutos = Math.floor(tempoRestante / 60);
          var segundos = tempoRestante % 60;
          document.getElementById('contador').innerHTML =
            'O código expira em ' + minutos + ':' + (segundos < 10 ? '0' : '') + segundos + 's';

          if (tempoRestante <= 0) {
            clearInterval(contador);
            document.getElementById('contador').innerHTML =
              '<a class="link" href="router/web.php?r=UserSendCod&acao=SendCod&i=<?= $i ?>" id="linkReenviar">Reenviar Código</a>';

            // Agora que o link existe, adiciona o eventListener nele
            var linkReenviar = document.getElementById('linkReenviar');
            linkReenviar.addEventListener('click', function(e) {
              e.preventDefault(); // impede o clique de seguir o link imediatamente
              e.stopPropagation(); // impede o clique de subir e disparar o form

              // Muda o texto para "Aguarde..."
              this.innerText = 'Aguarde...';

              // Troca o próprio link por um div "Aguarde..."
              this.outerHTML = '<div class="text-muted ms-1">Aguarde...</div>';

              // Redireciona para a página de reenvio manualmente
              window.location.href = this.href;

              // (Opcional) Reiniciar o contador:
              // tempoRestante = 120;
              // contadorAtual = iniciarContador();
            });
          }

          tempoRestante--;
        }, 1000);

        return contador;
      }

      var contadorAtual = iniciarContador();
    </script>

  </div>
</div>

<div class="link_back_reg">
  <p class="mb-0">Lembrou sua senha? <a href="<?= $url_sistema ?>" class="link"> Clique aqui</a> </p>
</div>

<?php include 'includes/footer_registro.php'; ?>
<!-- maxlength -->
<script src="assets/js/maxlength-cod.js"></script>
<!-- VALIDA FORMULÁRIOS -->
<script src="assets/js/valida_form.js"></script>