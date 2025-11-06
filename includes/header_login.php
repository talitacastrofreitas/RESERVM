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

  <!-- EFEITO BOTÃO -->
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
  <!-- FIM EFEITO BOTÃO -->

  <div class="auth-page-wrapper">
    <div class="row m-0">

      <div class="col-xl-7 col-xxl-8 p-0 m-0 d-none">
        <div class="carousel slide carousel-fade" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="4000">
              <div class="carousel_login carousel_login_img1"></div>
            </div>
            <!-- <div class="carousel-item" data-bs-interval="4000">
              <div class="carousel_login carousel_login_img2"></div>
            </div>
            <div class="carousel-item" data-bs-interval="4000">
              <div class="carousel_login carousel_login_img3"></div>
            </div>
            <div class="carousel-item" data-bs-interval="4000">
              <div class="carousel_login carousel_login_img4"></div>
            </div> -->
          </div>
        </div>
      </div>

      <!-- <div class="col-xl-5 col-xxl-4 px-0 px-sm-4"> -->
      <div class="col-12">
        <div class="d-flex align-items-center flex-column vh-100">