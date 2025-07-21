<?php
session_start();
ob_start(); //Limpa o buff de saida
include 'conexao/conexao.php';

// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA PERFIL DE USUÁRIO
if (empty($_SESSION['session_user_logged_in'])) {
  $_SESSION["rest"] = "Acesso restrito!";
  header('Location:' . $url_sistema);
  exit;
}
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
  <!-- DATATABLE-->
  <link href="assets/css/datatable/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/datatable/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- BOOTSTRAP -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- SWEET ALERT -->
  <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
  <!-- FLATPICKR -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
  <!-- SELECT2 -->
  <link href="assets/css/select2.min.css" rel="stylesheet" />
  <script src="assets/js/371.jquery.min.js" crossorigin="anonymous"></script>
  <script src="assets/js/select2.min.js"></script>
  <!-- ICONS -->
  <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <!-- CUSTOM-->
  <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />
  <!-- APP -->
  <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
  <!-- FONT -->
  <link href="assets/fontawesome/css/all.css" rel="stylesheet" type="text/css" />
  <!-- STYLE -->
  <link href="assets/css/style.css" rel="stylesheet" type="text/css" />

</head>

<body onload="showPage()">

  <!-- Preloader delay -->
  <div class="preloader_delay" id="preloader_delay">
    <div class="inner">
      <div class="text-center">
        <img src="assets/img/loader.gif" alt="RESERVM">
      </div>
    </div>
  </div>

  <!-- Begin page -->
  <div id="layout-wrapper">

    <header id="page-topbar">
      <div class="layout-width">
        <div class="navbar-header">
          <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box horizontal-logo">
              <a href="<?= $url_sistema ?>/painel.php" class="logo logo-dark">
                <span class="logo-sm">
                  <img src="assets/images/logo_header_branco_icone.svg" alt="" height="24">
                </span>
                <span class="logo-lg">
                  <img src="assets/images/logo_header_branco.svg" alt="" height="24">
                </span>
              </a>

              <a href="<?= $url_sistema ?>/painel.php" class="logo logo-light">
                <span class="logo-sm">
                  <img src="assets/images/logo_header_branco_icone.svg" alt="" height="24">
                </span>
                <span class="logo-lg">
                  <img src="assets/images/logo_header_branco.svg" alt="" height="24">
                </span>
              </a>
            </div>

            <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
              <span class="hamburger-icon">
                <span></span>
                <span></span>
                <span></span>
              </span>
            </button>

          </div>

          <div class="d-flex align-items-center">

            <?php
            // DADOS DO ADMINISTRADOR
            $sql = $conn->prepare("SELECT * FROM usuarios
                                  LEFT JOIN cursos ON cursos.curs_matricula_prof = usuarios.user_matricula
                                  WHERE usuarios.user_id = :user_id");
            $sql->bindParam(':user_id', $_SESSION['reservm_user_id']);
            $sql->execute();
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            if ($row) {
              $global_user_id            = trim(isset($row['user_id'])) ? $row['user_id'] : NULL;
              $global_user_nome = trim(isset($row['user_nome'])) ? $row['user_nome'] : NULL;
              $global_user_matricula     = trim(isset($row['user_matricula'])) ? $row['user_matricula'] : NULL;
              $global_user_email         = trim(isset($row['user_email'])) ? $row['user_email'] : NULL;
              $global_user_nivel_acesso  = trim(isset($row['nivel_acesso'])) ? $row['nivel_acesso'] : NULL;
              $user_status               = trim(isset($row['user_status'])) ? $row['user_status'] : NULL;
              //
              $curs_matricula_prof       = trim(isset($row['curs_matricula_prof'])) ? $row['curs_matricula_prof'] : NULL;

              // PEGA O PRIMEIRO NOME E ÚLTIMO NOME
              $partesNome = explode(" ", $global_user_nome);
              $primeiroNome = $partesNome[0];
              $ultimoNome = end($partesNome);

              // PEGA A PRIMEIRO E ÚLTIMO LETRA
              $firstNameInitial = strtoupper(substr($partesNome[0], 0, 1)); // PEGA A PRIMEIRA LETRA DO PRIMEIRO NOME
              $lastNameInitial = strtoupper(substr(end($partesNome), 0, 1)); // PEGA A PRIMEIRA LETRA DO ÚLTIMO NOME
              $iniciais = $firstNameInitial . $lastNameInitial; // RETORNA AS INICIAIS

            } else {
              header("Location: sair.php");
              exit();
            }
            ?>

            <div class="dropdown ms-sm-3 header-item topbar-user" id="top_ancora">
              <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="d-flex align-items-center">
                  <div class="icon_avatar icon_avatar_roxo"><?= $iniciais ?></div>
                  <span class="text-start d-sm-block d-none ms-3">
                    <span class="d-inline-block fw-medium user-name-text"><?= $primeiroNome . '&nbsp;&nbsp;' . $ultimoNome ?></span>
                    <!-- <span class="d-block fs-10 user-name-sub-text text-truncate"><?= $global_user_email ?></span> -->
                  </span>
                </span>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <!-- item-->
                <a class="dropdown-item icon_drop" href="perfil.php"><i class="mdi mdi-account-circle fs-16 align-middle me-1"></i> <span class="align-middle">Minha Conta</span></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item icon_drop" href="sair.php"><i class="mdi mdi-logout fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Sair</span></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="app-menu navbar-menu">
      <!-- LOGO -->
      <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index-2.html" class="logo logo-dark">
          <span class="logo-sm">
            <img src="assets/images/logo-sm.png" alt="" height="22">
          </span>
          <span class="logo-lg">
            <img src="assets/images/logo-dark.png" alt="" height="17">
          </span>
        </a>
        <!-- Light Logo-->
        <a href="index-2.html" class="logo logo-light">
          <span class="logo-sm">
            <img src="assets/images/logo-sm.png" alt="" height="22">
          </span>
          <span class="logo-lg">
            <img src="assets/images/logo-light.png" alt="" height="17">
          </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
          <i class="ri-record-circle-line"></i>
        </button>
      </div>

      <div id="scrollbar">
        <div class="container-fluid">

          <div id="two-column-menu">
          </div>
          <ul class="navbar-nav" id="navbar-nav">
            <li
              class="menu-title"><span data-key="t-menu">Menu</span></li>
            <li class="nav-item">
              <a class="nav-link menu-link" href="painel.php"><span>Solicitações</span></a>
            </li>

            <li class="nav-item">
              <a class="nav-link menu-link" href="nova_solicitacao.php"><span>Nova Solicitação</span></a>
            </li>

            <li class="nav-item">
              <a class="nav-link menu-link" href="programacao_diaria.php"><span>Programação</span></a>
            </li>

            <?php if (!empty($curs_matricula_prof)) { ?>
              <li class="nav-item">
                <a class="nav-link menu-link" href="aprovacoes.php"><span>Aprovações</span>
                  <?php
                  // QUANTIDADE DE SOLICITAÇÃO DE SUBMISSÃO PENDENTES
                  $query = "SELECT * FROM solicitacao
                  LEFT JOIN solicitacao_status ON solicitacao_status.solic_sta_solic_id = solicitacao.solic_id
                  LEFT JOIN cursos ON cursos.curs_id = solicitacao.solic_curso
                  INNER JOIN usuarios ON usuarios.user_matricula = cursos.curs_matricula_prof
                  WHERE solic_sta_status = 2 AND user_id = :user_id AND solic_cad_por != :solic_cad_por";
                  $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                  $stmt->execute([':user_id' => $global_user_id, ':solic_cad_por' => $global_user_id]);
                  $row_count = $stmt->rowCount();
                  ?>
                  <?php if ($row_count) {  ?>
                    <div class="cont_sub"><?= $row_count ?></div>
                  <?php } ?>
                </a>
              </li>
            <?php } ?>

          </ul>
        </div>
        <!-- Sidebar -->
      </div>
    </div>

    <div class="main-content">

      <div class="page-content">
        <div class="container-fluid">