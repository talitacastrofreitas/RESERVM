<?php
session_start();

if (!isset($_SERVER['HTTP_REFERER'])) {
  http_response_code(403);
  header("Location: ../sair.php");
  exit();
}

$rota = $_GET['r'] ?? '';

switch ($rota) {

  // ADMINISTRADOR
  case 'Admin':
    require '../admin/controller/controller_admin.php';
    break;

  case 'AdminExcConta':
    require '../admin/controller/controller_perfil.php';
    break;


  case 'acess': // ACESSO
    require '../admin/controller/controller_acesso.php';
    break;

  case 'reset': // SOLICITA RESETAR SENHA
    require '../admin/controller/controller_acesso.php';
    break;

  case 'valcod': // VALIDA CÓDIGO
    require '../admin/controller/controller_acesso.php';
    break;

  case 'updPass': // ALTERA A SENHA
    require '../admin/controller/controller_acesso.php';
    break;

  case 'updPassEx': // ALTERA A SENHA EXPIRADA
    require '../admin/controller/controller_acesso.php';
    break;

  case 'updPassPerf': // ALTERA A SENHA PELO PERFIL
    require '../admin/controller/controller_acesso.php';
    break;

  // ESPAÇO
  case 'Espac':
    require '../admin/controller/controller_espaco.php';
    break;

  // CURSOS
  case 'Curs':
    require '../admin/controller/controller_cursos.php';
    break;

  // DATAS BLOQUEADAS
  case 'DataBloq':
    require '../admin/controller/controller_data_bloqueada.php';
    break;


  // COMPONENTE CURRICULAR
  case 'CompC':
    require '../admin/controller/controller_componente_curricular.php';
    break;

  // RECURSOS
  case 'Recurs':
    require '../admin/controller/controller_recursos.php';
    break;

  // SEMESTRE PERÍODO
  case 'ConfPeriodoSemestre':
    require '../admin/controller/controller_semestre_periodo.php';
    break;

  // HORA DE FUNCIONAMENTO
  case 'HoraFunc':
    require '../admin/controller/controller_hora_funcionamento.php';
    break;


  // SOLICITAÇÃO
  case 'AdminSolic':
    require '../admin/controller/controller_solicitacao.php';
    break;




  //////////////
  // USUÁRIOS //
  //////////////

  case 'UserAcess': // ACESSO
    require '../controller/controller_acesso.php';
    break;

  case 'UserExcConta':
    require '../controller/controller_perfil.php';
    break;

  case 'UserRecord':
    require '../controller/controller_usuarios.php';
    break;

  case 'UserValcod': // VALIDA CÓDIGO
    require '../controller/controller_acesso.php';
    break;

  case 'UserSendCod':
    require '../controller/controller_acesso.php';
    break;

  case 'UserUpdPass': // ALTERA A SENHA
    require '../controller/controller_acesso.php';
    break;

  case 'UserExcPerf':
    require '../controller/controller_usuarios.php';
    break;

  case 'UserReset': // SOLICITA RESETAR SENHA
    require '../controller/controller_acesso.php';
    break;


  //////////////////
  // SOLICITAÇÕES //
  //////////////////

  case 'Solic':
    require '../controller/controller_solicitacao.php';
    break;


  case 'AprovaAnalise':
    require '../controller/controller_solicitacao_analise_status.php';
    break;

  case 'SolicDuplic':
    require '../controller/controller_clone.php';
    break;

  //////////////
  // RESERVAS //
  //////////////

  case 'Reserv':
    require '../admin/controller/controller_reservas.php';
    break;


  case 'AprovaAnaliseAdmin':
    require '../admin/controller/controller_solicitacao_analise_status.php';
    break;



  /////////////////
  // OCORRÊNCIAS //
  /////////////////

  case 'Ocorrenc':
    require '../admin/controller/controller_ocorrencias.php';
    break;

  case 'TipoOcor':
    require '../admin/controller/controller_tipo_ocorrencia.php';
    break;


  default:
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Rota não encontrada!";
    echo "<script> history.go(-1);</script>";
    break;
}
