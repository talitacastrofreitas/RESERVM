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
      <h4 class="mb-sm-0">Configurações</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Configurações</a></li>
          <li class="breadcrumb-item active">Configurações</li>
        </ol>
      </div>
    </div>
  </div>
</div>


<?php
$sql = $conn->prepare("SELECT * FROM conf_semestre_periodo");
$sql->execute();
$row_semp = $sql->fetch(PDO::FETCH_ASSOC);
$semp_id          = $row_semp['semp_id'];
$semp_data_inicio = $row_semp['semp_data_inicio'];
$semp_data_fim    = $row_semp['semp_data_fim'];
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-12">
            <h5 class="card-title mb-0">Período do Semestre</h5>
          </div>
        </div>
      </div>
      <form class="needs-validation" method="POST" action="../router/web.php?r=ConfPeriodoSemestre" novalidate>
        <div class="card-body p-4 px-3 px-md-4">

          <p>Preencha os campos para definir o período de vigência do semestre acadêmico.</p>

          <div class="row grid gx-3">
            <div class="col-lg-6 col-xl-4">
              <div class="row g-3">

                <input type="hidden" class="form-control" name="semp_id" value="<?= htmlspecialchars($semp_id) ?>">
                <input type="hidden" class="form-control" name="acao" value="atualizar">

                <div class="col-sm">
                  <label class="form-label">Início do semestre <span>*</span></label>
                  <input type="text" class="form-control flatpickr-input" name="semp_data_inicio" id="cad_semp_data_inicio" value="<?= htmlspecialchars($semp_data_inicio) ?>" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <div class="col-sm">
                  <label class="form-label">Fim do semestre <span>*</span></label>
                  <input type="text" class="form-control flatpickr-input" name="semp_data_fim" id="cad_semp_data_fim" value="<?= htmlspecialchars($semp_data_fim) ?>" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>

              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="hstack gap-3 align-items-center justify-content-end">
            <p class="label_asterisco me-auto my-0"><span>*</span> Campo obrigatório</p>
            <button type="submit" class="btn botao botao_verde waves-effect">Salvar</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Seleciona os campos de data
    const inputDataInicio = document.getElementById("cad_semp_data_inicio");
    const inputDataFim = document.getElementById("cad_semp_data_fim");

    // Inicializa o Flatpickr com altInput e formatação
    [inputDataInicio, inputDataFim].forEach(input => {
      flatpickr(input, {
        dateFormat: "Y-m-d", // valor enviado ao backend
        altInput: true, // cria input alternativo visível
        altFormat: "d/m/Y", // formato exibido ao usuário
        locale: "pt" // idioma em português
      });

      // Remove o erro visual ao digitar/modificar o valor
      input.addEventListener('input', function() {
        if (input.value.trim()) {
          input.classList.remove('is-invalid');
        }
      });
    });

    // Validação personalizada ao enviar o formulário
    document.querySelector('form.needs-validation').addEventListener('submit', function(event) {
      let formValido = true;

      [inputDataInicio, inputDataFim].forEach(input => {
        if (!input.value.trim()) {
          input.classList.add('is-invalid');
          formValido = false;
        } else {
          input.classList.remove('is-invalid');
        }
      });

      if (!formValido) {
        event.preventDefault(); // impede envio do form
        event.stopPropagation(); // evita propagação
      }
    });
  </script>


  <!-- FOOTER -->
  <?php include 'includes/footer.php'; ?>