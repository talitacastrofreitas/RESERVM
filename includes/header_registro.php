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

      <div class="col-xl-12">
        <div class="auth-page-content pt-1 pb-0">
          <div class="container">

            <div class="row">
              <div class="col-lg-12">
                <div class="text-center my-4 py-2">
                  <div class="logo_login">
                    <img src="assets/images/logo-dark-register.svg" width="220" alt="RESERVM">
                  </div>
                </div>
              </div>
            </div>

            <div class="row justify-content-center align-items-center flex-columns">
              <div class="card_login mb-5 pb-4 mt-3">