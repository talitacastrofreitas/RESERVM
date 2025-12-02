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
include '../conexao/conexao.php';
$global_user_id = $_SESSION['reservm_user_id'] ?? null;
global $conn;

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

 // ROTA PARA O CALENDÁRIO (ADICIONE ESTE BLOCO)
  case 'EventosCalendario':
    require '../admin/controller/controller_eventos_calendario.php';
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

  // CANCELAR SOLICITAÇÃO 

  case 'SolicCanc':
    require '../controller/controller_solicitacao_cancelamento.php';
    break;
  case 'AdminConfCanc':
    require '../admin/controller/controller_solicitacao_cancelamento.php';
    break; // ✅ Adicione 'exit;' aqui também, para ser seguro
  case 'AdminNegCanc':
    require '../admin/controller/controller_solicitacao_cancelamento.php';
    break; // ✅ Adicione 'exit;' aqui também



    // EDITAR EM MASSA

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
