<?php
session_start();

// 1. Inclua o arquivo de conexão
include '../conexao/conexao.php';

// 2. Defina o ID do usuário (sem o $global no topo)
$global_user_id = $_SESSION['reservm_user_id'] ?? null;

// 3. Declare a $conn como global.
// Se conexao.php define $conn, esta linha a torna global no web.php
global $conn;
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

  // --- NOVAS ROTAS DE CANCELAMENTO (Mantenha-as agrupadas aqui) ---
  case 'SolicCanc':
    require '../controller/controller_solicitacao_cancelamento.php';
    break;
  case 'AdminConfCanc':
    require '../admin/controller/controller_solicitacao_cancelamento.php';
    break; // ✅ Adicione 'exit;' aqui também, para ser seguro
  case 'AdminNegCanc':
    require '../admin/controller/controller_solicitacao_cancelamento.php';
    break; // ✅ Adicione 'exit;' aqui também


  // EDIÇÃO EM MASSA DE RESERVAS
  //////////////
  // RESERVAS //
  //////////////

  case 'Reserv':
    // 1. Rota de Ações Padrão (Exclusão Individual, Cadastro, Atualização Individual, etc.)
    if (!isset($_GET['acao']) || $_GET['acao'] !== 'editar_massa') {




      require '../admin/controller/controller_reservas.php';
      break; // Sai do switch
    }

    // 2. Rota de EDIÇÃO EM MASSA (Ação: 'editar_massa')

    $ids_reservas_str = $_POST['ids_reservas'] ?? '';
    $res_solic_id = $_POST['res_solic_id'] ?? null;
    $res_tipo_reserva = $_POST['res_tipo_reserva'] ?? null;

    // Verificação de segurança
    if (empty($ids_reservas_str) || empty($res_solic_id)) {
      header("Location: ../solicitacao_analise.php?i=" . urlencode($res_solic_id) . "&msg=erro_id&tab=reservas");
      exit;
    }
    $ids_array = explode(',', $ids_reservas_str);

    try {
      $conn->beginTransaction();


      if ($res_tipo_reserva == 2) {
        // ====================================================
        // LÓGICA DE EXCLUSÃO E RE-CRIAÇÃO (RESERVA FIXA)
        // ====================================================

        // A. EXCLUIR todas as reservas selecionadas - CORREÇÃO CRÍTICA PARA CHAR(32)
        $placeholders_nomeados = [];
        $params_delete = [];

        // 1. Cria placeholders nomeados e o array de parâmetros
        foreach ($ids_array as $index => $id) {
          $placeholder = ":id{$index}";
          $placeholders_nomeados[] = $placeholder;
          // Limpa espaços em branco (trim)
          $params_delete[$placeholder] = trim($id);
        }

        // 2. Aplica CAST e TRIM na coluna res_id na query para forçar a correspondência
        $query_delete = "DELETE FROM reservas WHERE TRIM(CAST(res_id AS VARCHAR(32))) IN (" . implode(', ', $placeholders_nomeados) . ")";
        $stmt_delete = $conn->prepare($query_delete);

        // ==========================================
        // DEBUG CRÍTICO EM TELA (PARA TESTE)
        // ESTE BLOCO VAI PARAR A EXECUÇÃO E MOSTRAR OS DADOS
        // ==========================================
        echo "<h1>DEBUG CRÍTICO: DADOS DA EXCLUSÃO</h1>";
        echo "<h2>Query de Exclusão (SQL)</h2>";
        echo "<textarea style='width: 100%; height: 100px;'>";
        echo htmlspecialchars($query_delete);
        echo "</textarea>";

        echo "<h2>IDs (Parâmetros Limpos do PHP)</h2>";
        echo "<pre>";
        print_r($params_delete);
        echo "</pre>";

        // === SAÍDA FORÇADA ===
        exit("FIM DO DEBUG. COPIE OS DADOS ACIMA.");
        // =====================

        // 3. EXECUTA (Nunca será alcançado no debug)
        $stmt_delete->execute($params_delete);


        // B. Captura os dados necessários para a RE-CRIAÇÃO (POST)
        $res_campus = $_POST['res_campus'] ?? null;
        $res_espaco_id = ($res_campus == 1) ? ($_POST['res_espaco_id_cabula'] ?? null) : ($_POST['res_espaco_id_brotas'] ?? null);
        $res_data_inicio_semanal = $_POST['res_data_inicio_semanal'] ?? null;
        $res_data_fim_semanal = $_POST['res_data_fim_semanal'] ?? null;
        $res_dia_semana = $_POST['res_dia_semana'] ?? null;

        // Se a sua coluna 'res_dia_semana' no BD for varchar(50), 
        // e você precisar do nome do mês, use o código do controller_reservas para buscar os nomes.
        // O código a seguir usa a convenção de números para o dia da semana no BD, o que é mais comum.

        $campos_comuns = [
          // Se sua query INSERT precisar de res_id/res_codigo, remova o comentário e adapte.
          // ':res_id' => null, ':res_codigo' => null, 
          ':res_solic_id' => $res_solic_id,
          ':res_quant_pessoas' => $_POST['res_quant_pessoas'] ?? null,
          ':res_recursos' => $_POST['res_recursos'] ?? null,
          ':res_recursos_add' => implode(',', (array) ($_POST['res_recursos_add'] ?? [])),
          ':res_obs' => $_POST['res_obs'] ?? null,
          ':res_tipo_aula' => $_POST['res_tipo_aula'] ?? null,
          ':res_curso' => $_POST['res_curso'] ?? null,
          ':res_curso_extensao' => $_POST['res_curso_extensao'] ?? null,
          ':res_semestre' => $_POST['res_semestre'] ?? null,
          ':res_componente_atividade' => $_POST['res_componente_atividade'] ?? null,
          ':res_componente_atividade_nome' => $_POST['res_componente_atividade_nome'] ?? null,
          ':res_nome_atividade' => $_POST['res_nome_atividade'] ?? null,
          ':res_curso_nome' => $_POST['res_curso_nome'] ?? null,
          ':res_modulo' => $_POST['res_modulo'] ?? null,
          ':res_titulo_aula' => $_POST['res_titulo_aula'] ?? null,
          ':res_professor' => $_POST['res_professor'] ?? null,
          ':res_turno' => $_POST['res_turno'] ?? null,
          ':res_campus' => $res_campus,
          ':res_espaco_id' => $res_espaco_id,
          ':res_user_id' => $global_user_id,
          ':res_tipo_reserva' => 2,
          ':res_hora_inicio' => $_POST['res_hora_inicio'] ?? null,
          ':res_hora_fim' => $_POST['res_hora_fim'] ?? null,
          // Parâmetros de repetição
          ':res_data_inicio_semanal' => $res_data_inicio_semanal,
          ':res_data_fim_semanal' => $res_data_fim_semanal,
        ];


        // C. Lógica de RECRIAÇÃO
        if ($res_data_inicio_semanal && $res_data_fim_semanal && $res_dia_semana) {

          $data_inicio = new DateTime($res_data_inicio_semanal);
          $data_fim = new DateTime($res_data_fim_semanal);
          $dia_semana_alvo = (int) $res_dia_semana;

          // Adiciona +1 dia à data final para incluir o último dia no loop
          $data_fim_mais_um = (clone $data_fim)->modify('+1 day');
          $periodo = new DatePeriod($data_inicio, new DateInterval('P1D'), $data_fim_mais_um);

          // Mapeamento de meses para a coluna res_mes (VARCHAR(50))
          $mapa_meses = [
            '01' => 'JANEIRO',
            '02' => 'FEVEREIRO',
            '03' => 'MARÇO',
            '04' => 'ABRIL',
            '05' => 'MAIO',
            '06' => 'JUNHO',
            '07' => 'JULHO',
            '08' => 'AGOSTO',
            '09' => 'SETEMBRO',
            '10' => 'OUTUBRO',
            '11' => 'NOVEMBRO',
            '12' => 'DEZEMBRO'
          ];

          // Query INSERT (inclua res_id e res_codigo se o BD for NOT NULL)
          $query_insert = "INSERT INTO reservas 
                                 (res_id, res_codigo, res_solic_id, res_data, res_mes, res_ano, res_dia_semana, 
                                  res_hora_inicio, res_hora_fim, res_turno, res_tipo_reserva, res_tipo_aula, 
                                  res_campus, res_espaco_id, res_quant_pessoas, res_recursos, res_recursos_add, 
                                  res_obs, res_curso, res_curso_extensao, res_semestre, res_componente_atividade, 
                                  res_componente_atividade_nome, res_nome_atividade, res_curso_nome, res_modulo, 
                                  res_titulo_aula, res_professor, res_data_inicio_semanal, res_data_fim_semanal, 
                                  res_user_id, res_data_cad, res_data_upd) 
                                 VALUES 
                                 (:res_id, :res_codigo, :res_solic_id, :res_data, :res_mes, :res_ano, :res_dia_semana, 
                                  :res_hora_inicio, :res_hora_fim, :res_turno, :res_tipo_reserva, :res_tipo_aula, 
                                  :res_campus, :res_espaco_id, :res_quant_pessoas, :res_recursos, :res_recursos_add, 
                                  :res_obs, :res_curso, :res_curso_extensao, :res_semestre, :res_componente_atividade, 
                                  :res_componente_atividade_nome, :res_nome_atividade, :res_curso_nome, :res_modulo, 
                                  :res_titulo_aula, :res_professor, :res_data_inicio_semanal, :res_data_fim_semanal, 
                                  :res_user_id, GETDATE(), GETDATE())";
          $stmt_insert = $conn->prepare($query_insert);

          foreach ($periodo as $data) {
            // Obtém o dia da semana no formato ISO (1=Segunda a 7=Domingo)
            $dia_semana_iso = (int) $data->format('N');

            if ($dia_semana_iso === $dia_semana_alvo) {

              // Gera ID e Código únicos para a nova reserva
              $res_id_loop = bin2hex(random_bytes(16));
              $res_codigo_fixa = 'RF' . random_int(100000, 999999);

              $data_formatada = $data->format('Y-m-d');
              $mes_numero = $data->format('m');
              $mes_nome = $mapa_meses[$mes_numero];
              $ano = $data->format('Y');

              // Adapta o array de dados para a nova reserva
              $data_insert = array_merge($campos_comuns, [
                // Campos específicos de data
                ':res_data' => $data_formatada,
                ':res_mes' => $mes_nome,
                ':res_ano' => $ano,
                ':res_dia_semana' => $dia_semana_alvo,
                // IDs e Códigos
                ':res_id' => $res_id_loop,
                ':res_codigo' => $res_codigo_fixa,
              ]);

              // Remove a vírgula de res_recursos_add se estiver vazia (melhor prática)
              if (empty($data_insert[':res_recursos_add'])) {
                $data_insert[':res_recursos_add'] = null;
              }

              $stmt_insert->execute($data_insert);
            }
          } // Fim do foreach
        } // Fim do if de Recriação

      } else {
        // ====================================================
        // LÓGICA PADRÃO DE UPDATE (Reservas NÃO-Fixas)
        // ====================================================
        // (Mantida a lógica de UPDATE em massa para outros tipos, se houver)

        $updates = [];
        $params = [];

        foreach ($campos_validos as $campo) {
          if (isset($_POST[$campo])) {
            $updates[] = "$campo = :$campo";
            if (is_array($_POST[$campo])) {
              $params[":$campo"] = implode(',', $_POST[$campo]);
            } else {
              $params[":$campo"] = $_POST[$campo];
            }
          }
        }

        if (empty($updates)) {
          $conn->commit();
          header("Location: ../solicitacao_analise.php?i=" . urlencode($res_solic_id) . "&msg=sucesso&tab=reservas");
          exit;
        }

        $query_base = "UPDATE reservas SET " . implode(', ', $updates) . " WHERE res_id = :id";
        $stmt_update = $conn->prepare($query_base);

        foreach ($ids_array as $id) {
          $params[':id'] = trim($id);
          $stmt_update->execute($params);
        }
      } // Fim do if/else $res_tipo_reserva == 2

      $conn->commit();
      header("Location: ../solicitacao_analise.php?i=" . urlencode($res_solic_id) . "&msg=sucesso&tab=reservas");
      exit;
    } catch (PDOException $e) {
      $conn->rollBack();
      error_log("Erro na edição em massa: " . $e->getMessage());
      header("Location: ../solicitacao_analise.php?i=" . urlencode($res_solic_id) . "&msg=erro&tab=reservas");
      exit;
    }

  default:
    http_response_code(404);
    $_SESSION["erro"] = "Rota '{$rota}' não encontrada!";
    header("Location: ../painel.php");
    exit;
} // Fecha o switch
?>