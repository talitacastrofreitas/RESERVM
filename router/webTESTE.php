<?php

session_start();
ob_start();

// Verificação de segurança para evitar acesso direto.
if (!isset($_SERVER['HTTP_REFERER'])) {
  http_response_code(403);
  header("Location: ../sair.php");
  exit();
}

// Inclusão de arquivos essenciais.
include '../includes/conn/conexao.php';
include '../includes/helper/funcoes.php';

// Verificação de login.
if (!isset($_SESSION['usuario_logado'])) {
  header('Location: ../../index.php');
  exit();
}

$rota = $_GET['r'] ?? '';
$acao = $_GET['acao'] ?? '';

switch ($rota) {

  // ROTAS DO ADMINISTRADOR
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

  // CANCELAR SOLICITAÇÃO - USUÁRIO


  case 'SolicitarCancelamento':
    $solic_id = $_POST['solic_id'] ?? null;
    $usuario_logado_id = $global_user_id; // Supondo que esta variável está disponível globalmente
    $motivo = $_POST['motivo'] ?? 'Solicitação de cancelamento enviada';

    if ($solic_id) {
      try {
        $conn->beginTransaction();

        // Mudar o status da SOLICITAÇÃO para "Aguardando Cancelamento" (status 7)
        $stmt_solic = $conn->prepare("UPDATE solicitacao_status SET solic_sta_status = 7 WHERE solic_sta_solic_id = :solic_id");
        $stmt_solic->execute([':solic_id' => $solic_id]);

        // Mudar o status das RESERVAS para "Aguardando Cancelamento" (status 7)
        $stmt_reservas = $conn->prepare("
                UPDATE reservas
                SET res_status = 7
                WHERE res_solic_id = :solic_id AND CAST(res_data AS DATETIME) + CAST(res_hora_inicio AS DATETIME) >= GETDATE() AND res_status NOT IN (7, 8)
            ");
        $stmt_reservas->execute([':solic_id' => $solic_id]);

        // Registrar no histórico
        $stmt_hist = $conn->prepare("INSERT INTO historico_status (hist_solic_id, hist_status_id, hist_data, hist_user_id, hist_motivo) VALUES (:solic_id, 7, GETDATE(), :user_id, :motivo)");
        $stmt_hist->execute([':solic_id' => $solic_id, ':user_id' => $usuario_logado_id, ':motivo' => $motivo]);

        $conn->commit();
        header("Location: ../solicitacoes.php?sucesso=solicitado");
        exit;
      } catch (PDOException $e) {
        $conn->rollBack();
        header("Location: ../solicitacoes.php?erro=solicitar_cancelamento");
        exit;
      }
    } else {
      header("Location: ../solicitacoes.php?erro=parametro_invalido");
      exit;
    }
    break;


  // CANCELAR SOLICITAÇÃO - SAAP


  case 'ConfirmarCancelamento':
    $solic_id = $_POST['solic_id'] ?? null;
    $usuario_logado_id = $global_user_id; // Supondo que esta variável está disponível globalmente
    $motivo = $_POST['motivo'] ?? 'Cancelamento de solicitação aprovado pelo SAAP';

    if ($solic_id) {
      try {
        $conn->beginTransaction();

        // Mudar o status da SOLICITAÇÃO para "Cancelado" (status 8)
        $stmt_solic = $conn->prepare("UPDATE solicitacao_status SET solic_sta_status = 8 WHERE solic_sta_solic_id = :solic_id");
        $stmt_solic->execute([':solic_id' => $solic_id]);

        // Mudar o status das RESERVAS para "Cancelado" (status 8)
        $stmt_reservas = $conn->prepare("
                UPDATE reservas
                SET res_status = 8
                WHERE res_solic_id = :solic_id AND res_status = 7
            ");
        $stmt_reservas->execute([':solic_id' => $solic_id]);

        // Registrar no histórico
        $stmt_hist = $conn->prepare("INSERT INTO historico_status (hist_solic_id, hist_status_id, hist_data, hist_user_id, hist_motivo) VALUES (:solic_id, 8, GETDATE(), :user_id, :motivo)");
        $stmt_hist->execute([':solic_id' => $solic_id, ':user_id' => $usuario_logado_id, ':motivo' => $motivo]);

        $conn->commit();
        header("Location: ../solicitacoes_saap.php?sucesso=cancelado");
        exit;
      } catch (PDOException $e) {
        $conn->rollBack();
        header("Location: ../solicitacoes_saap.php?erro=confirmar_cancelamento");
        exit;
      }
    } else {
      header("Location: ../solicitacoes_saap.php?erro=parametro_invalido");
      exit;
    }
    break;

  default:
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Rota não encontrada!";
    echo "<script> history.go(-1);</script>";
    break;
}