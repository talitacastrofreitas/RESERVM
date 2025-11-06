<?php include 'includes/header_registro.php'; ?>

<div class="card card_user_registro">
  <div class="card-body">

    <form id="ValidaBotaoProgressPadrao" method="POST" action="router/web.php?r=UserRecord" class="needs-validation" novalidate>

      <div class="setup-content" id="step-1">

        <input type="hidden" name="acao" value="cadastrar">

        <div class="mb-3">
          <label class="form-label">Seu e-mail institucional <span>*</span></label>
          <input type="email" class="form-control text-lowercase" name="user_email" required>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>

        <!-- <div class="mb-3">
          <label class="form-label">Nome completo</label>
          <input type="text" class="form-control text-uppercase" name="user_nome" id="cad_admin_nome" maxlength="100" readonly>
          <div class="invalid-feedback">Este campo é obrigatório</div>
        </div>

        <div class="mb-3">
          <label class="form-label">E-mail</label>
          <input type="email" class="form-control text-lowercase" name="user_email" id="cad_admin_email" maxlength="100" readonly>
          <div class="invalid-feedback">Digite um e-mail válido</div>
        </div> -->

        <div class="col-12">
          <div class="mb-3">
            <div class="form-check form-check-success">
              <input class="form-check-input" type="checkbox" name="user_termos" value="1" id="check" onClick="HabiDsabi()">
              <label class="form-check-label" for="check">Concordo com a </label><a href="" class="link_strong ms-1" data-bs-toggle="modal" data-bs-target="#politicaPrivacidade">Política de Privacidade</a>
            </div>
          </div>
        </div>

        <div class="card_user_registro_footer label_asterisco d-flex align-items-center align-items-start gap-3">
          <!-- <p class="me-auto my-0"><span>*</span> Campo obrigatório</p> -->
          <button type="submit" class="btn botao botao_verde waves-effect waves-light ms-auto" id="cadastrar">Cadastrar</button>
        </div>

      </div>

  </div>
</div>


<div class="link_back_reg">
  <p class="mb-0">Já tem uma conta? <a href="<?= $url_sistema ?>" class="link"> Clique aqui</a> </p>
</div>


<!-- FOOTER -->
<?php include 'includes/footer_registro.php'; ?>


<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#cad_admin_matricula').on('blur', function() {
    const matricula = $(this).val().trim();

    if (matricula === '') return;

    $.ajax({
      url: 'includes/select/get_colaborador.php',
      method: 'POST',
      data: {
        matricula: matricula
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#cad_admin_nome').val(response.nome);
          $('#cad_admin_email').val(response.email);
        } else {
          $('#cad_admin_matricula').val('');
          $('#cad_admin_nome').val('');
          $('#cad_admin_email').val('');
          // alert('Usuário não encontrado.');
          Swal.fire({
            icon: 'warning',
            title: 'Usuário não encontrado',
            text: 'Verifique a matrícula digitada.',
            confirmButtonText: 'Ok'
          });
        }
      },
      error: function() {
        //alert('Erro ao buscar dados.');
        $('#cad_admin_nome').val('');
        $('#cad_admin_email').val('');
        Swal.fire({
          icon: 'error',
          title: 'Erro de comunicação',
          text: 'Não foi possível consultar o servidor.',
          confirmButtonText: 'Fechar'
        });
      }
    });
  });
</script> -->

<!-- MODAL POLITICA DE PRIVACIDADE -->
<?php require 'includes/modal/modal_politica_privacidade.php' ?>

<!-- CHECKBOX TERMOS -->
<script type="text/javascript">
  function HabiDsabi() {
    if (document.getElementById('check').checked == true) {
      document.getElementById('cadastrar').disabled = ""
    }
    if (document.getElementById('check').checked == false) {
      document.getElementById('cadastrar').disabled = "disabled"
    }
  }
  HabiDsabi();
</script>

<!-- COMPLETO FORM -->
<!-- <script src="includes/select/completa_form.js"></script> -->