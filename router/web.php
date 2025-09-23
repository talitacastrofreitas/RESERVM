<?php
session_start();

// // DEPURAR ADMIN_EMAIL NO INÍCIO DO ROUTER
// if (isset($admin_email)) {
//   echo "admin_email no início do router (set): " . htmlspecialchars($admin_email) . "<br>";
// } else {
//   echo "admin_email no início do router (não set): NÃO DEFINIDA<br>";
// }
// var_dump($GLOBALS['admin_email'] ?? 'Não existe no $GLOBALS'); // Verifica no array de globais
// exit; // PARE A EXECUÇÃO AQUI!

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

  // CANCELAR SOLICITAÇÃO - USUÁRIO


  case 'SolicitarCancelamento':
    $solic_id = $_POST['solic_id'] ?? null;
    $usuario_logado_id = $global_user_id;
    $motivo = $_POST['motivo'] ?? 'Solicitação de cancelamento enviada'; // Adicione um campo de motivo se houver no modal

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
    $usuario_logado_id = $global_user_id;
    $motivo = $_POST['motivo'] ?? 'Cancelamento de solicitação aprovado pelo SAAP'; // Adicione um campo de motivo se houver no modal

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



    if ($_GET['r'] == 'Reserv' && $_GET['acao'] == 'editar_massa') {
      $ids_reservas_str = $_POST['ids_reservas'] ?? '';
      if (empty($ids_reservas_str)) {
        header("Location: ../solicitacao_analise.php?i=" . urlencode($_POST['res_solic_id']) . "&msg=erro&tab=reservas");
        exit;
      }
      $ids_array = explode(',', $ids_reservas_str);

      $updates = [];
      $params = [];
      $campos_validos = [
        'res_campus',
        'res_espaco_id_cabula',
        'res_espaco_id_brotas',
        'res_quant_pessoas',
        'res_recursos',
        'res_obs',
        'res_tipo_aula',
        'res_curso',
        'res_curso_extensao',
        'res_semestre',
        'res_componente_atividade',
        'res_componente_atividade_nome',
        'res_nome_atividade',
        'res_curso_nome',
        'res_modulo',
        'res_titulo_aula',
        'res_professor',
        'res_tipo_reserva',
        'res_dia_semana',
        'res_data',
        'res_mes',
        'res_ano',
        'res_hora_inicio',
        'res_hora_fim',
        'res_turno'
      ];

      foreach ($campos_validos as $campo) {
        if (isset($_POST[$campo]) && !empty($_POST[$campo])) {
          $updates[] = "$campo = :$campo";
          if (is_array($_POST[$campo])) {
            $params[":$campo"] = implode(',', $_POST[$campo]);
          } else {
            $params[":$campo"] = $_POST[$campo];
          }
        }
      }

      if (empty($updates)) {
        header("Location: ../solicitacao_analise.php?i=" . urlencode($_POST['res_solic_id']) . "&msg=sucesso&tab=reservas");
        exit;
      }

      try {
        $conn->beginTransaction();
        $query_base = "UPDATE reservas SET " . implode(', ', $updates) . " WHERE res_id = :id";
        $stmt = $conn->prepare($query_base);

        foreach ($ids_array as $id) {
          $params[':id'] = (int) $id;
          $stmt->execute($params);
        }

        $conn->commit();
        header("Location: ../solicitacao_analise.php?i=" . urlencode($_POST['res_solic_id']) . "&msg=sucesso&tab=reservas");
        exit;
      } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Erro na edição em massa: " . $e->getMessage());
        header("Location: ../solicitacao_analise.php?i=" . urlencode($_POST['res_solic_id']) . "&msg=erro&tab=reservas");
        exit;
      }
    }
  default:
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Rota não encontrada!";
    echo "<script> history.go(-1);</script>";
    break;
}
