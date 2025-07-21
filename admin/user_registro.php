<?php
session_start();
ob_start(); //Limpa o buff de saida
include 'conexao/conexao.php';
?>

<!doctype html>
<html lang="pt-BR" data-layout="horizontal" data-topbar="dark" data-sidebar-size="lg" data-sidebar="light" data-sidebar-image="none" data-preloader="disable">

<head>
  <meta charset="utf-8" />
  <title>RESERVM - Sistema de Gestão de Atividades de Extensão Bahiana</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="RESERVM - Sistema de Gestão de Atividades de Extensão Bahiana" name="description" />
  <!-- FAVICON -->
  <link rel="shortcut icon" href="assets/img/favicon.png">
  <!-- BOOTSTRAP -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- SWEET ALERT -->
  <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
  <!-- ICONES -->
  <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <!-- APP -->
  <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
  <!-- CUSTOM -->
  <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />
  <!-- FONT -->
  <link href="assets/fontawesome/css/all.css" rel="stylesheet" type="text/css" />
  <!-- STYLE -->
  <link href="assets/css/style.css" rel="stylesheet" type="text/css" />

</head>

<body onload="showPage()">

  <!-- EFEITO BOTAO -->
  <div class="app-menu navbar-menu">
    <div id="scrollbar">
      <div class="container-fluid">
        <div id="two-column-menu">
        </div>
        <ul class="navbar-nav" id="navbar-nav">
          <li class="menu-title"><span data-key="t-menu"></span></li>
        </ul>
      </div>
    </div>
  </div>
  <!-- FIM EFEITO BOTAO -->

  <div class="auth-page-wrapper">
    <div class="row m-0">

      <div class="col-xl-12">
        <div class="auth-page-content pt-1">
          <div class="container">

            <div class="row">
              <div class="col-lg-12">
                <div class="text-center my-4 py-2">
                  <div class="logo_login">
                    <img src="assets/images/logo-dark-register.svg" alt="RESERVM">
                  </div>
                </div>
              </div>
            </div>

            <div class="row justify-content-center">
              <div class="col-md-8 col-lg-6 col-xl-5 mb-5 pb-4">

                <?php if (empty($_SESSION["fim_registro"])) { ?>

                  <div class="card card_user_registro">
                    <div class="card-body">

                      <div class="stepwizard">
                        <div class="stepwizard-row setup-panel d-flex justify-content-between">
                          <div class="stepwizard-step">
                            <a href="#step-1" type="button" class="btn botao_azul bt_padrao btn-circle pe-none">1</a>
                          </div>
                          <div class="stepwizard-step">
                            <a href="#step-2" type="button" class="btn btn-light btn-circle pe-none" disabled>2</a>
                          </div>
                          <div class="stepwizard-step">
                            <a href="#step-3" type="button" class="btn btn-light btn-circle pe-none" disabled>3</a>
                          </div>
                        </div>
                      </div>



                      <form id="UserRegistro" method="POST" action="controller/controller_usuarios.php?funcao=reg_user" class="needs-validation" novalidate>

                        <div class="setup-content" id="step-1">

                          <div class="col-12">
                            <div class="mb-3">
                              <div class="form-check form-check-success">
                                <input class="form-check-input" type="checkbox" value="1" name="user_brasileiro" id="idSouBrasileiro" checked>
                                <label class="form-check-label" for="idSouBrasileiro">Sou brasileiro</label>
                              </div>
                            </div>
                          </div>

                          <div class="mb-3">
                            <label class="form-label">Nome completo</label>
                            <input type="text" class="form-control text-uppercase" name="user_nome" minlength="10" maxlength="100" required>
                            <div class="invalid-feedback">Informe um nome válido</div>
                          </div>

                          <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control text-lowercase" name="user_email" maxlength="100" required>
                            <div class="invalid-feedback">Digite um e-mail válido</div>
                          </div>

                          <div id="campo_cpf" class="col-12">
                            <div class="mb-3">
                              <label class="form-label">CPF</label>
                              <input type="text" class="form-control cpf" id="idCpf" name="user_cpf" required>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div id="campo_passaporte" class="col-12 hide">
                            <div class="mb-3">
                              <label class="form-label">Passaporte</label>
                              <input type="text" class="form-control text-uppercase" id="idPassaporte" name="user_passaporte" maxlength="30">
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <div id="campo_data_nascimento" class="col-12 hide">
                            <div class="mb-3">
                              <label class="form-label">Data de nascimento</label>
                              <input type="date" class="form-control" id="idDataNascimento" name="user_data_nascimento">
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                          </div>

                          <script>
                            document.getElementById('idSouBrasileiro').addEventListener('change', function() {
                              var campo_cpf = document.getElementById('campo_cpf');
                              var campo_passaporte = document.getElementById('campo_passaporte');
                              var campo_data_nascimento = document.getElementById('campo_data_nascimento');
                              var idPassaporte = document.getElementById('idPassaporte');

                              if (this.checked) {
                                campo_passaporte.classList.add('hide');
                                document.getElementById('idPassaporte').required = false;

                                campo_data_nascimento.classList.add('hide');
                                document.getElementById('idDataNascimento').required = false;

                                campo_cpf.classList.remove('hide');
                                document.getElementById('idCpf').required = true;
                              } else {
                                campo_passaporte.classList.remove('hide');
                                document.getElementById('idPassaporte').required = true;

                                campo_data_nascimento.classList.remove('hide');
                                document.getElementById('idDataNascimento').required = true;

                                campo_cpf.classList.add('hide');
                                document.getElementById('idCpf').required = false;
                              }
                            });
                          </script>

                          <div class="col-12">
                            <div class="mb-3">
                              <div class="form-check form-check-success">
                                <input class="form-check-input" type="checkbox" value="1" name="user_vinculo" id="user_vinculo">
                                <label class="form-check-label" for="user_vinculo">Possuo vínculo com a instituição</label>
                              </div>
                            </div>
                          </div>

                          <div class="card_user_registro_footer d-flex align-items-start gap-3">
                            <button type="button" class="btn botao botao_verde waves-effect waves-light ms-auto nextBtn">Próximo</button>
                          </div>

                        </div><!-- step-1 -->


                        <div class="setup-content" id="step-2">

                          <fieldset class="fieldset-password">

                            <div class="mb-3">
                              <label class="form-label" for="pwd">Senha</label>
                              <div class="position-relative auth-pass-inputgroup mb-3">
                                <input type="password" class="form-control pe-5 password-input" id="pwd" name="user_senha" required>
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
                                <!-- <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button> -->
                                <div class="invalid-feedback">Confirme a senha</div>
                              </div>
                            </div>

                            <div id="senha" class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert" style="display: none;">
                              <i class="ri-error-warning-line label-icon"></i><strong>Erro</strong> - As senhas não correspondem!
                              <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                            </div>

                          </fieldset>


                          <div class="col-12">
                            <div class="mb-3">
                              <div class="form-check form-check-success">
                                <input class="form-check-input" type="checkbox" name="user_termos" value="1" id="check" onClick="HabiDsabi()">
                                <label class="form-check-label" for="check">Concordo com a </label><a href="" class="link_strong ms-1" data-bs-toggle="modal" data-bs-target="#politicaPrivacidade">Política de Privacidade</a>
                              </div>
                            </div>
                          </div>

                          <div class="card_user_registro_footer d-flex align-items-start gap-3">
                            <button type="button" class="btn botao botao_cinza waves-effect prevBtn">Voltar</button>
                            <button type="submit" class="btn botao botao_verde waves-effect waves-light btn-label right ms-auto" id="entrar">Concluir</button>
                          </div>

                        </div><!-- step-2 -->

                      </form>


                    </div>
                    <!-- end card body -->
                  </div>

                <?php } else { ?>

                  <div class="card card_user_registro">
                    <div class="card-body">

                      <div class="stepwizard">
                        <div class="stepwizard-row setup-panel d-flex justify-content-between">
                          <div class="stepwizard-step">
                            <a href="#step-1" type="button" class="btn botao_azul bt_padrao btn-circle pe-none">1</a>
                          </div>
                          <div class="stepwizard-step">
                            <a href="#step-2" type="button" class="btn botao_azul bt_padrao btn-circle pe-none">2</a>
                          </div>
                          <div class="stepwizard-step">
                            <a href="#step-3" type="button" class="btn botao_azul bt_padrao btn-circle pe-none">3</a>
                          </div>
                        </div>
                      </div>

                      <div class="setup-content" id="step-3">
                        <div class="text-center">

                          <h5 class="mb-4">Cadastro realizado!</h5>

                          <div class="mb-4">
                            <img src="assets/img/icones/icon_confirma.svg" alt="">
                          </div>

                          <p class="text-muted">Você receberá um email com um link de confirmação para ativar seu acesso. <br><br>
                            Por favor, verifique sua caixa de entrada e siga as instruções do email.<br><br>
                            Caso não receba o email em até 10 minutos, verifique sua pasta de spam ou entre em contato conosco.</p>

                          <a href="<?= $url_sistema ?>" class="btn botao botao_verde mt-3" type="button">Fazer login</a>
                        </div>
                      </div>

                    </div>
                  </div>

                <?php unset($_SESSION["fim_registro"]);
                } ?>

                <div class="mt-4 text-center">
                  <p class="mb-0">Já tem uma conta? <a href="<?= $url_sistema ?>" class="link"> Fazer login</a> </p>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <footer class="footer_user_register">
        <img src="assets/images/logo_bahiana_registro.svg" alt="" width="180">
        <p class="mt-2" style="color: #808EA7;">&copy; <script>
            document.write(new Date().getFullYear())
          </script> RESERVM - Sistema de Gestão de Atividades de Extensão Bahiana. Todos os direitos reservados.</p>
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

  <!-- MODAL POLITICA DE PRIVACIDADE -->
  <?php require 'includes/modal/modal_politica_privacidade.php' ?>

  <!-- APP -->
  <script src="assets/js/app.js"></script>
  <!-- VALIDA FORMULÁRIOS -->
  <script src="assets/js/valida_form.js"></script>
  <!-- VALIDA SENHA -->
  <script src="assets/js/valid-password.js"></script>
  <!-- PASSWORD ADDON INIT -->
  <script src="assets/js/pages/password-addon.init.js"></script>
  <!-- SWEET ALERTA JS -->
  <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
  <!-- MASCARAS -->
  <script src="assets/js/mask/311_jquery.min.js"></script>
  <script src="assets/js/mask/jquery.mask.min.js"></script>
  <script src="assets/js/mask/mask.js"></script>
  <!-- VALIDA CPF -->
  <script src="assets/js/valida_cpf.js"></script>

  <!-- VALIDA OS CAMPOS DO FORM POR ETAPA -->
  <script src="assets/js/360.jquery.min.js"></script>
  <script src="assets/js/valida_form_user_registro.js"></script>

  <script>
    function showPage() {
      document.body.style.opacity = "1"; // TRANSIÇÃO DE PÁGINA
    }
  </script>
</body>

</html>