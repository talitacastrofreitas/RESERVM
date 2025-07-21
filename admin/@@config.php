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

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-12 text-sm-start text-center">
            <h5 class="card-title mb-0">Horário de Funcionamento</h5>
          </div>
        </div>
      </div>
      <div class="card-body p-4 px-3 px-md-4">
        <form class="needs-validation" method="POST" action="controller/controller_configuracoes.php?funcao=CadConfig" novalidate>

          <p>Desative a categoria quando desejar bloquear seu cadastro de proposta extensionista</p>

          <div class="row grid gx-3">
            <div class="col-md-6 col-lg-3">
              <div class="row g-3">
                <div class="col-12">
                  <p class="text-center bg-warning-subtle fs-16 m-0 p-2">NOITE</p>
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="row g-3">
                <div class="col-12">
                  <p class="text-center bg-success-subtle fs-16 m-0 p-2">MANHÃ</p>
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="row g-3">
                <div class="col-12">
                  <p class="text-center bg-primary-subtle fs-16 m-0 p-2">TARDE</p>
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3">
              <div class="row g-3">
                <div class="col-12">
                  <p class="text-center bg-danger-subtle fs-16 m-0 p-2">NOITE</p>
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
                <div class="col-6">
                  <input type="time" class="form-control">
                </div>
              </div>
            </div>

        </form>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>