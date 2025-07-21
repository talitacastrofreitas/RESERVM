<?php
session_start();
ob_start(); //Limpa o buff de saida

// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA PERFIL DE ADMINISTRADOR
if (empty($_SESSION['session_admin_logged_in'])) {
  $_SESSION["rest"] = "Acesso restrito!";
  header("Location: ../admin");
} else {

  // CONEXÃO COM O BANCO DE DADOS
  include '../conexao/conexao.php';

  // *** ADICIONE ESTE BLOCO PARA DEPURAR ***
  if (!isset($conn) || $conn === null) {
    error_log("Erro crítico: \$conn é null em header.php após incluir conexao.php");
    die("Problema interno: Conexão com o banco de dados não estabelecida. Contate o administrador.");
  }
  // ***************************************
}
?>



<!doctype html>
<html lang="pt-BR" data-layout="horizontal" data-topbar="dark" data-sidebar-size="lg" data-sidebar="light"
  data-sidebar-image="none" data-preloader="disable">

<head>
  <meta charset="utf-8" />
  <title>RESERVM - Área do Administrador</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="RESERVM - Sistema de Gestão de Atividades de Extensão Bahiana" name="description" />
  <!-- FAVICON -->
  <link rel="shortcut icon" href="../assets/img/favicon.png">
  <!-- DATATABLE-->
  <link href="../assets/css/datatable/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/datatable/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
  <!-- BOOTSTRAP -->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- SWEET ALERT -->
  <link href="../assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
  <!-- SELECT2 -->
  <link href="../assets/css/select2.min.css" rel="stylesheet" />
  <script src="../assets/js/371.jquery.min.js" crossorigin="anonymous"></script>
  <script src="../assets/js/select2.min.js"></script>
  <!-- FLATPICKR -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
  <!-- ICONS -->
  <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
  <!-- CUSTOM-->
  <link href="../assets/css/custom.min.css" rel="stylesheet" type="text/css" />
  <!-- APP -->
  <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" />
  <!-- FONT -->
  <link href="../assets/fontawesome/css/all.css" rel="stylesheet" type="text/css" />
  <!-- STYLE -->
  <link href="../assets/css/style.css" rel="stylesheet" type="text/css" />

</head>

<body onload="showPage()">

  <div class="preloader_delay" id="preloader_delay">
    <div class="inner">
      <div class="text-center">
        <img src="../assets/img/loader.gif" alt="RESERVM">
      </div>
    </div>
  </div>

  <div id="layout-wrapper">

    <header id="page-topbar">
      <div class="layout-width">
        <div class="navbar-header">
          <div class="d-flex">
            <div class="navbar-brand-box horizontal-logo">
              <a href="<?= $url_sistema ?>/admin/painel.php" class="logo logo-dark">
                <span class="logo-sm">
                  <img src="../assets/images/logo_header_branco_icone.svg" alt="" height="20">
                </span>
                <span class="logo-lg">
                  <img src="../assets/images/logo_header_branco.svg" alt="" height="26">
                </span>
              </a>

              <a href="<?= $url_sistema ?>/admin/painel.php" class="logo logo-light">
                <span class="logo-sm">
                  <img src="../assets/images/logo_header_branco_icone.svg" alt="" height="20">
                </span>
                <span class="logo-lg">
                  <div class="logo_admin">
                    <span>ADM</span>
                    <img src="../assets/images/logo_header_branco.svg" alt="" height="26">
                  </div>
                </span>
              </a>
            </div>

            <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
              id="topnav-hamburger-icon">
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
            $sql = $conn->prepare("SELECT * FROM admin
                                  INNER JOIN admin_perfil ON admin_perfil.adm_perfil_id = admin.admin_perfil
                                  WHERE admin.admin_id = :admin_id");
            $sql->bindParam(':admin_id', $_SESSION['reservm_admin_id']);
            $sql->execute();
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $global_admin_id = $row['admin_id'];
            $global_admin_matricula = $row['admin_matricula'];
            $global_admin_nome = $row['admin_nome'];
            $global_admin_email = $row['admin_email'];
            $global_admin_perfil = $row['admin_perfil'];
            $global_admin_status = $row['admin_status'];
            $global_admin_senha = $row['admin_senha'];
            $global_admin_data_reset_senha = $row['admin_data_reset_senha'];
            $global_admin_data_acesso = $row['admin_data_acesso'];
            $global_nivel_acesso = $row['nivel_acesso'];
            $global_admin_data_cad = $row['admin_data_cad'];
            $global_admin_data_upd = $row['admin_data_upd'];
            // ADMIN PERFIL
            $global_adm_perfil_id = $row['adm_perfil_id'];
            $global_adm_perfil = $row['adm_perfil'];

            // PEGA O PRIMEIRO NOME E ÚLTIMO NOME
            $partesNome = explode(" ", $global_admin_nome);
            $primeiroNome = $partesNome[0];
            $ultimoNome = end($partesNome);

            // PEGA A PRIMEIRO E ÚLTIMO LETRA
            $firstNameInitial = strtoupper(substr($partesNome[0], 0, 1)); // PEGA A PRIMEIRA LETRA DO PRIMEIRO NOME
            $lastNameInitial = strtoupper(substr(end($partesNome), 0, 1)); // PEGA A PRIMEIRA LETRA DO ÚLTIMO NOME
            $iniciais = $firstNameInitial . $lastNameInitial; // RETORNA AS INICIAIS
            ?>

            <div class="dropdown ms-sm-3 header-item topbar-user">
              <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="d-flex align-items-center">
                  <div class="icon_avatar icon_avatar_azul"><?= htmlspecialchars($iniciais) ?></div>
                  <span class="text-start d-sm-block d-none ms-3">
                    <span
                      class="d-inline-block fw-medium user-name-text"><?= htmlspecialchars($primeiroNome) . '&nbsp;&nbsp;' . htmlspecialchars($ultimoNome) ?></span>
                    <span
                      class="d-block fs-10 user-name-sub-text text-truncate"><?= htmlspecialchars($global_adm_perfil) ?></span>
                  </span>
                </span>
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item icon_drop" href="perfil.php"><i
                    class="mdi mdi-account-circle fs-16 align-middle me-1"></i> <span class="align-middle">Minha
                    Conta</span></a>
                <a class="dropdown-item icon_drop <?php echo ($global_admin_perfil != 1) ? 'd-none' : ''; ?>"
                  href="config.php"><i class="fa-solid fa-sliders fs-14 align-middle me-1"></i> <span
                    class="align-middle">Configurações</span></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item icon_drop" href="sair.php"><i
                    class="mdi mdi-logout fs-16 align-middle me-1"></i> <span class="align-middle"
                    data-key="t-logout">Sair</span></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="app-menu navbar-menu">
      <div class="navbar-brand-box">
        <a href="index-2.html" class="logo logo-dark">
          <span class="logo-sm">
            <img src="../assets/images/logo-sm.png" alt="" height="22">
          </span>
          <span class="logo-lg">
            <img src="../assets/images/logo-dark.png" alt="" height="17">
          </span>
        </a>
        <a href="index-2.html" class="logo logo-light">
          <span class="logo-sm">
            <img src="../assets/images/logo-sm.png" alt="" height="22">
          </span>
          <span class="logo-lg">
            <img src="../assets/images/logo-light.png" alt="" height="17">
          </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
          id="vertical-hover">
          <i class="ri-record-circle-line"></i>
        </button>
      </div>

      <div id="scrollbar">
        <div class="container-fluid">

          <div id="two-column-menu">
          </div>
          <ul class="navbar-nav" id="navbar-nav">
            <li class="menu-title"><span data-key="t-menu">Menu</span></li>

            <li class="nav-item">
              <a class="nav-link menu-link" href="solicitacoes.php"><span>Solicitações</span>
                <?php
                // QUANTIDADE DE SOLICITAÇÃO DE SUBMISSÃO PENDENTES
                $query = "SELECT * FROM solicitacao_status WHERE solic_sta_status IN (2,3)";
                $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->execute();
                $row_count = $stmt->rowCount();
                ?>
                <?php if ($row_count) { ?>
                  <div class="cont_sub"><?= $row_count ?></div>
                <?php } ?>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link menu-link" href="painel.php"><span>Disponibilidade</span></a>
            </li>

            <li class="nav-item">
              <a class="nav-link menu-link" href="reservas_confirmadas.php"><span>Reservas Confirmadas</span></a>
            </li>

            <li class="nav-item">
              <a class="nav-link menu-link" href="programacao_diaria.php"><span>Programação Diária</span></a>
            </li>

            <li class="nav-item">
              <a class="nav-link menu-link" href="ocorrencias.php"><span>Ocorrências</span></a>
            </li>

            <li class="nav-item">
              <a class="nav-link menu-link" href="usuarios.php"><span>Usuários</span></a>
            </li>

            <li class="nav-item <?php echo ($global_admin_perfil != 1) ? 'd-none' : ''; ?>">
              <a class="nav-link menu-link" href="admin.php"><span>Administradores</span></a>
            </li>
            <!-- <li class="nav-item">
              <a class="nav-link menu-link" href="usuarios.php"><span>Ocorrências</span></a>
            </li> -->

            <!-- <li class="nav-item">
              <a class="nav-link menu-link" href="usuarios.php"><span>Nova Solicitação</span></a>
            </li> -->

            <!-- <li class="nav-item">
              <a class="nav-link menu-link" href="usuarios.php"><span>Registro de Ocorrência</span></a>
            </li> -->

            <!-- <li class="nav-item <?php echo ($global_admin_perfil != 1) ? 'd-none' : ''; ?>">
              <a class="nav-link menu-link" href="admin.php"><span>Administração</span></a>
            </li> -->

            <li class="nav-item <?php echo ($global_admin_perfil != 1) ? 'd-none' : ''; ?>">
              <a class="nav-link menu-link" href="#sidebarLanding" data-bs-toggle="collapse" role="button"
                aria-expanded="false" aria-controls="sidebarLanding"><span data-key="t-landing">Cadastros</span></a>
              <div class="collapse menu-dropdown" id="sidebarLanding">
                <ul class="nav nav-sm flex-column">
                  <li class="nav-item">
                    <a href="componente_curricular.php" class="nav-link">Componentes Curriculares</a>
                  </li>
                  <li class="nav-item">
                    <a href="cursos.php" class="nav-link">Cursos</a>
                  </li>
                  <li class="nav-item">
                    <a href="datas_bloqueadas.php" class="nav-link">Datas Bloqueadas</a>
                  </li>
                  <li class="nav-item">
                    <a href="espacos.php" class="nav-link">Espaços</a>
                  </li>
                  <li class="nav-item">
                    <a href="recursos.php" class="nav-link">Recursos</a>
                  </li>
                  <li class="nav-item">
                    <a href="hora_funcionamento.php" class="nav-link">Horários de Funcionamento</a>
                  </li>
                  <li class="nav-item">
                    <a href="tipo_ocorrencia.php" class="nav-link">Tipos de Ocorrências</a>
                  </li>
                  <li class="nav-item">
                    <a href="publicidades.php" class="nav-link">Publicidades</a>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="main-content">

      <div class="page-content">
        <div class="container-fluid">