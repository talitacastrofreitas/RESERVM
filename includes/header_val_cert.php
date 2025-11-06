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
  <!-- DATATABLE-->
  <link href="assets/css/datatable/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/datatable/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- BOOTSTRAP -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- SWEET ALERT -->
  <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
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

  <style>
    [data-layout="horizontal"] .page-content {
      padding-top: 0 !important;
    }

    .profile-wid-bg {
      height: auto !important;
      padding: 10px 0;
    }
  </style>

  <!-- Begin page -->
  <div id="layout-wrapper">

    <header class="border-0 mt-0 position-relative" id="page-topbar">
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
          </div>

          <div class="d-flex align-items-center">
            <a href="<?= $url_sistema ?>" type="submit" class="btn botao botao_azul_escuro">Login e Registro</a>
          </div>
        </div>
      </div>
    </header>



    <div class="main-content">


      <div class="page-content m-0">
        <div class="container-fluid">