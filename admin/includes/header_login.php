<?php
session_start();
ob_start(); //Limpa o buff de saida
include '../conexao/conexao.php';
?>

<!doctype html>
<html lang="pt-BR" data-layout="horizontal" data-topbar="dark" data-sidebar-size="lg" data-sidebar="light" data-sidebar-image="none" data-preloader="disable">

<head>
  <meta charset="utf-8" />
  <title>RESERVM - Área do Administrador</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="RESERVM - Sistema de Gestão de Atividades de Extensão Bahiana" name="description" />
  <!-- FAVICON -->
  <link rel="shortcut icon" href="../assets/img/favicon.png">
  <!-- BOOTSTRAP -->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- ICONES -->
  <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <!-- APP -->
  <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" />
  <!-- CUSTOM -->
  <link href="../assets/css/custom.min.css" rel="stylesheet" type="text/css" />
  <!-- FONT -->
  <link href="../assets/fontawesome/css/all.css" rel="stylesheet" type="text/css" />
  <!-- STYLE -->
  <link href="../assets/css/style_login.css" rel="stylesheet" type="text/css" />

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
    <div class="row m-0 justify-content-center align-items-center flex-column">

      <div class="col">
        <div class="auth-page-content pt-4">
          <div class="container d-flex justify-content-center flex-column">

            <div class="logo_login text-center">
              <img src="../assets/images/logo-dark-register.svg" width="220" alt="RESERVM">
              <p>Painel Administrativo</p>
            </div>

            <div class="card_login_admin">