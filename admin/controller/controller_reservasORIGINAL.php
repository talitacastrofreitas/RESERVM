<?php
//session_start();
require_once '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao']; // AÇÃO: CADASTRAR, ATUALIZAR, DELETAR
    $rvm_admin_id        = $_SESSION['reservm_admin_id'];

    if ($acao === 'cadastrar' || $acao === 'atualizar') {

      // FUNÇÃO PARA VERIFICAR SE O CÓDIGO JÁ EXISTE
      function verificarCodigoNoBanco($res_codigo, $conn)
      {
        $stmt_check_code = $conn->prepare("SELECT COUNT(*) FROM reservas WHERE res_codigo = :res_codigo");
        $stmt_check_code->bindParam(':res_codigo', $res_codigo);
        $stmt_check_code->execute();
        return $stmt_check_code->fetchColumn() > 0;
      }

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (
        empty($_POST['res_solic_id']) ||
        empty($_POST['res_quant_pessoas']) ||
        empty($_POST['res_tipo_aula']) ||
        empty($_POST['res_curso']) ||
        // empty($_POST['res_tipo_reserva']) ||
        empty($_POST['res_hora_inicio']) ||
        empty($_POST['res_hora_fim']) ||
        empty($_POST['res_turno'])
      ) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // ----------------------------------------------

      $res_solic_id                  = $_POST['res_solic_id'];

      //////////////// ETAPA 1
      $res_campus                    = $_POST['res_campus'];
      $res_espaco_id                 = ($_POST['res_campus'] == 1) ? $_POST['res_espaco_id_cabula'] : $_POST['res_espaco_id_brotas'];
      //
      $res_quant_pessoas             = $_POST['res_quant_pessoas'];
      $res_recursos                  = $_POST['res_recursos'];
      // PROCESSA OS CHECKBOXES COMO STRING
      $res_recursos_add = isset($_POST['res_recursos_add']) && is_array($_POST['res_recursos_add'])
        ? implode(', ', array_map('htmlspecialchars', $_POST['res_recursos_add']))
        : null;
      //
      $res_obs                       = trim($_POST['res_obs']) !== '' ? nl2br(trim($_POST['res_obs'])) : NULL;

      //////////////// ETAPA 2      
      $res_tipo_aula                 = $_POST['res_tipo_aula'];
      $res_curso                     = $_POST['res_curso'];
      $res_curso_nome                = $_POST['res_curso_nome'] !== '' ? trim($_POST['res_curso_nome']) : NULL;
      $res_curso_extensao            = isset($_POST['res_curso_extensao']) && $_POST['res_curso_extensao'] !== '' ? (int)$_POST['res_curso_extensao'] : null;
      $res_semestre                  = isset($_POST['res_semestre']) && $_POST['res_semestre'] !== '' ? (int)$_POST['res_semestre'] : null;
      $res_componente_atividade      = isset($_POST['res_componente_atividade']) && $_POST['res_componente_atividade'] !== '' ? (int)$_POST['res_componente_atividade'] : null;
      $res_componente_atividade_nome = $_POST['res_componente_atividade_nome'] !== '' ? trim($_POST['res_componente_atividade_nome']) : NULL;
      $res_nome_atividade            = $_POST['res_nome_atividade'] !== '' ? trim($_POST['res_nome_atividade']) : NULL;
      $res_modulo                    = $_POST['res_modulo'] !== '' ? trim($_POST['res_modulo']) : NULL;
      $res_titulo_aula               = $_POST['res_titulo_aula'] !== '' ? trim($_POST['res_titulo_aula']) : NULL;
      $res_professor                 = $_POST['res_professor'] !== '' ? trim($_POST['res_professor']) : NULL;

      //////////////// ETAPA 3 
      $res_tipo_reserva              = $_POST['res_tipo_reserva'];
      $res_hora_inicio               = $_POST['res_hora_inicio'];
      $res_hora_fim                  = $_POST['res_hora_fim'];
      $res_turno                     = $_POST['res_turno'];
      ////////////////

      // BUSCA O PERÍODO DO SEMESTRE NO BANCO DE DADOS
      $sql_periodo = "SELECT semp_data_inicio, semp_data_fim FROM conf_semestre_periodo";
      $stmt_periodo = $conn->query($sql_periodo);
      $resultado = $stmt_periodo->fetch(PDO::FETCH_ASSOC);

      // Intervalo fixo (ajuste conforme necessário)
      $inicio = new DateTime($resultado['semp_data_inicio']);
      $fim = new DateTime($resultado['semp_data_fim']);
      $fim->modify('+1 day'); // para incluir o dia final
      $diaSemanaEscolhido = (int) $_POST['res_dia_semana']; // 1 (segunda) a 7 (domingo)
    }




    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // MOVER A GERAÇÃO DO CÓDIGO ÚNICO PARA CÁ (PARA RESERVAS ESPORÁDICAS)
      if ($res_tipo_reserva != 2) { // Somente para reservas esporádicas
        $tentativas = 0;
        do {
          $tentativas++;
          if ($tentativas > 5) {
            throw new Exception("Não foi possível gerar um código único para reserva esporádica após várias tentativas.");
          }
          $res_codigo = 'RE' . random_int(100000, 999999);
          $existe = verificarCodigoNoBanco($res_codigo, $conn); // Verifica se esse CÓDIGO já existe (não se a reserva já existe)
        } while ($existe);
        error_log("res_codigo gerado para esporádica: " . $res_codigo);
      }
      // FIM DA GERAÇÃO DO CÓDIGO ÚNICO


      $sql_insert_res = "INSERT INTO reservas (
                                      res_id,
                                      res_codigo,
                                      res_solic_id,
                                      res_tipo_reserva,
                                      res_data,
                                      res_mes,
                                      res_ano,
                                      res_dia_semana,
                                      res_hora_inicio,
                                      res_hora_fim,
                                      res_turno,
                                      res_tipo_aula,
                                      res_curso,
                                      res_curso_nome,
                                      res_curso_extensao,
                                      res_semestre,
                                      res_componente_atividade,
                                      res_componente_atividade_nome,
                                      res_nome_atividade,
                                      res_modulo,
                                      res_professor,
                                      res_titulo_aula,
                                      res_espaco_id,
                                      res_campus,
                                      res_quant_pessoas,
                                      res_recursos,
                                      res_recursos_add,
                                      res_obs,
                                      res_user_id,
                                      res_data_cad,
                                      res_data_upd
                                    ) VALUES (
                                      :res_id,
                                      :res_codigo,
                                      :res_solic_id,
                                      :res_tipo_reserva,
                                      :res_data,
                                      UPPER(:res_mes),
                                      :res_ano,
                                      :res_dia_semana,
                                      :res_hora_inicio,
                                      :res_hora_fim,
                                      :res_turno,
                                      :res_tipo_aula,
                                      :res_curso,
                                      UPPER(:res_curso_nome),
                                      :res_curso_extensao,
                                      :res_semestre,
                                      :res_componente_atividade,
                                      UPPER(:res_componente_atividade_nome),
                                      UPPER(:res_nome_atividade),
                                      UPPER(:res_modulo),
                                      UPPER(:res_professor),
                                      UPPER(:res_titulo_aula),
                                      :res_espaco_id,
                                      :res_campus,
                                      :res_quant_pessoas,
                                      :res_recursos,
                                      :res_recursos_add,
                                      :res_obs,
                                      :res_user_id,
                                      GETDATE(),
                                      GETDATE()
                                    )";
      $stmt_insert_res = $conn->prepare($sql_insert_res); // Use um nome diferente para evitar conflitos futuros

      $reservation_inserted = false; // Inicializa a flag aqui

      // RESERVA FIXA (várias datas)
      if ($res_tipo_reserva == 2) {

        $meses = [
          '01' => 'Janeiro',
          '02' => 'Fevereiro',
          '03' => 'Março',
          '04' => 'Abril',
          '05' => 'Maio',
          '06' => 'Junho',
          '07' => 'Julho',
          '08' => 'Agosto',
          '09' => 'Setembro',
          '10' => 'Outubro',
          '11' => 'Novembro',
          '12' => 'Dezembro'
        ];


        $periodo = new DatePeriod($inicio, new DateInterval('P1D'), $fim);
        foreach ($periodo as $dataObj) {
          if ((int) $dataObj->format('N') === $diaSemanaEscolhido) {
            $res_data = $dataObj->format('Y-m-d');
            $mesNumero = $dataObj->format('m');
            $res_mes = $meses[$mesNumero];
            $res_ano = $dataObj->format('Y');

            // Novo ID por data (garante unicidade)
            $res_id     = bin2hex(random_bytes(16));
            // Gerar novo res_codigo para cada reserva fixa
            $tentativas_codigo_fixa = 0;
            do {
              $tentativas_codigo_fixa++;
              if ($tentativas_codigo_fixa > 5) {
                throw new Exception("Não foi possível gerar um código único para reserva fixa após várias tentativas.");
              }
              $res_codigo_fixa = 'RE' . random_int(100000, 999999);
              $existe_fixa = verificarCodigoNoBanco($res_codigo_fixa, $conn);
            } while ($existe_fixa);


            // Executa o insert
            $stmt_insert_res->execute([ // Use o stmt_insert_res
              ':res_id' => $res_id,
              ':res_codigo' => $res_codigo_fixa, // Use o novo código gerado
              ':res_solic_id' => $res_solic_id,
              ':res_tipo_reserva' => $res_tipo_reserva,
              ':res_data' => $res_data,
              ':res_mes' => $res_mes,
              ':res_ano' => $res_ano,
              ':res_dia_semana' => $diaSemanaEscolhido,
              ':res_hora_inicio' => $res_hora_inicio,
              ':res_hora_fim' => $res_hora_fim,
              ':res_turno' => $res_turno,
              ':res_tipo_aula' => $res_tipo_aula,
              ':res_curso' => $res_curso,
              ':res_curso_nome' => $res_curso_nome,
              ':res_curso_extensao' => $res_curso_extensao,
              ':res_semestre' => $res_semestre,
              ':res_componente_atividade' => $res_componente_atividade,
              ':res_componente_atividade_nome' => $res_componente_atividade_nome,
              ':res_nome_atividade' => $res_nome_atividade,
              ':res_modulo' => $res_modulo,
              ':res_professor' => $res_professor,
              ':res_titulo_aula' => $res_titulo_aula,
              ':res_espaco_id' => $res_espaco_id,
              ':res_campus' => $res_campus,
              ':res_quant_pessoas' => $res_quant_pessoas,
              ':res_recursos' => $res_recursos,
              ':res_recursos_add' => $res_recursos_add,
              ':res_obs' => $res_obs,
              ':res_user_id' => $rvm_admin_id
            ]);

            // Se pelo menos uma reserva for inserida, defina a flag como true
            if ($stmt_insert_res->rowCount() > 0) {
              $reservation_inserted = true;
            }
          }
        }
        error_log("Tipo de Reserva: FIXA");
      } else {
        // RESERVA ESPORÁDICA (data única)

        $data = $_POST['res_data'];
        $dateTime = new DateTime($data, new DateTimeZone('America/Sao_Paulo'));

        $mesNumero = $dateTime->format('m');
        // $res_mes = $meses[$mesNumero];
        $res_mes = $_POST['res_mes'];
        $res_ano = $dateTime->format('Y');
        $res_dia_semana = $dateTime->format('N');
        $res_data = $dateTime->format('Y-m-d');

        $res_id = bin2hex(random_bytes(16));

        $stmt_insert_res->execute([ // Use o stmt_insert_res
          ':res_id' => $res_id,
          ':res_codigo' => $res_codigo,
          ':res_solic_id' => $res_solic_id,
          ':res_tipo_reserva' => $res_tipo_reserva,
          ':res_data' => $res_data,
          ':res_mes' => $res_mes,
          ':res_ano' => $res_ano,
          ':res_dia_semana' => $res_dia_semana,
          ':res_hora_inicio' => $res_hora_inicio,
          ':res_hora_fim' => $res_hora_fim,
          ':res_turno' => $res_turno,
          ':res_tipo_aula' => $res_tipo_aula,
          ':res_curso' => $res_curso,
          ':res_curso_nome' => $res_curso_nome,
          ':res_curso_extensao' => $res_curso_extensao,
          ':res_semestre' => $res_semestre,
          ':res_componente_atividade' => $res_componente_atividade,
          ':res_componente_atividade_nome' => $res_componente_atividade_nome,
          ':res_nome_atividade' => $res_nome_atividade,
          ':res_modulo' => $res_modulo,
          ':res_professor' => $res_professor,
          ':res_titulo_aula' => $res_titulo_aula,
          ':res_espaco_id' => $res_espaco_id,
          ':res_campus' => $res_campus,
          ':res_quant_pessoas' => $res_quant_pessoas,
          ':res_recursos' => $res_recursos,
          ':res_recursos_add' => $res_recursos_add,
          ':res_obs' => $res_obs,
          ':res_user_id' => $rvm_admin_id
        ]);

        // Se a reserva esporádica for inserida, defina a flag como true
        if ($stmt_insert_res->rowCount() > 0) {
          $reservation_inserted = true;
          error_log("Reserva esporádica INSERIDA com sucesso. res_id: " . $res_id);
        } else {
          error_log("Reserva esporádica: rowCount é 0. Nenhuma linha inserida.");
        }
      }

      // -------------------------------------------------------------
      // INÍCIO DA NOVA LÓGICA: ATUALIZAR STATUS DA SOLICITAÇÃO
      // Este bloco deve ser adicionado AQUI, após as inserções de reserva
      // -------------------------------------------------------------


      if ($reservation_inserted) {
        $status_reservado_id = 4; // ID do status 'Reservado' na tabela status_solicitacao

        // Consulta para verificar o status atual da solicitação
        $sql_check_status = "SELECT solic_sta_status FROM solicitacao_status WHERE solic_sta_solic_id = :solic_sta_solic_id";
        $stmt_check_status = $conn->prepare($sql_check_status);
        $stmt_check_status->execute([':solic_sta_solic_id' => $res_solic_id]);
        $current_status = $stmt_check_status->fetchColumn();



        if ($current_status != $status_reservado_id && $current_status != 6) {
          $sql_update_solicitation_status = "
                        UPDATE solicitacao_status
                        SET
                            solic_sta_status = :solic_sta_status,
                            solic_sta_user_id = :solic_sta_user_id,
                            solic_sta_data_cad = GETDATE()
                        WHERE
                            solic_sta_solic_id = :solic_sta_solic_id
                    ";
          $stmt_update_status = $conn->prepare($sql_update_solicitation_status);
          $stmt_update_status->execute([
            ':solic_sta_status' => $status_reservado_id,
            ':solic_sta_user_id' => $rvm_admin_id,
            ':solic_sta_solic_id' => $res_solic_id
          ]);


          // Opcional: Registrar no log essa mudança de status para 'Reservado'
          $log_acao_status = 'Status_Solicitacao_Reservado'; // Ação específica para o log
          $log_dados_status = ['solic_id' => $res_solic_id, 'novo_status_id' => $status_reservado_id, 'novo_status_nome' => 'Reservado'];
          $sqlLogStatus = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
                                   VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
          $stmtLogStatus = $conn->prepare($sqlLogStatus);
          $stmtLogStatus->execute([
            ':modulo' => 'SOLICITAÇÃO STATUS',
            ':acao' => $log_acao_status,
            ':acao_id' => $res_solic_id,
            ':dados' => json_encode($log_dados_status, JSON_UNESCAPED_UNICODE),
            ':user_id' => $rvm_admin_id
          ]);
        }
      }
      // -------------------------------------------------------------
      // FIM DA NOVA LÓGICA
      // ----


      // -------------------------------
      // ATUALIZAR
      // -------------------------------
    } elseif ($acao === 'atualizar') {

      if (empty($_POST['res_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $res_id    = $_POST['res_id'];
      $log_acao  = 'Atualização';

      $sql = "UPDATE reservas SET 
                                  res_solic_id                  = :res_solic_id,
                                  --res_tipo_reserva              = :res_tipo_reserva,
                                  res_data                      = :res_data,
                                  res_mes                       = UPPER(:res_mes),
                                  res_ano                       = :res_ano,
                                  res_dia_semana                = :res_dia_semana,
                                  res_hora_inicio               = :res_hora_inicio,
                                  res_hora_fim                  = :res_hora_fim,
                                  res_turno                     = :res_turno,
                                  res_tipo_aula                 = :res_tipo_aula,
                                  res_curso                     = :res_curso,
                                  res_curso_nome                = UPPER(:res_curso_nome),
                                  res_curso_extensao            = :res_curso_extensao,
                                  res_semestre                  = :res_semestre,
                                  res_componente_atividade      = :res_componente_atividade,
                                  res_componente_atividade_nome = UPPER(:res_componente_atividade_nome),
                                  res_nome_atividade            = UPPER(:res_nome_atividade),
                                  res_modulo                    = UPPER(:res_modulo),
                                  res_professor                 = UPPER(:res_professor),
                                  res_titulo_aula               = UPPER(:res_titulo_aula),
                                  res_espaco_id                 = :res_espaco_id,
                                  res_campus                    = :res_campus,
                                  res_quant_pessoas             = :res_quant_pessoas,
                                  res_recursos                  = :res_recursos,
                                  res_recursos_add              = :res_recursos_add,
                                  res_obs                       = :res_obs,
                                  res_user_id                   = :res_user_id,
                                  res_data_upd                  = GETDATE()
                            WHERE 
                                  res_id = :res_id";
      $stmt = $conn->prepare($sql);

      // RESERVA ESPORÁDICA (data única)
      $data = $_POST['res_data'];
      $dateTime = new DateTime($data, new DateTimeZone('America/Sao_Paulo'));

      $mesNumero = $dateTime->format('m');
      // $res_mes = $meses[$mesNumero];
      $res_mes = $_POST['res_mes'];
      $res_ano = $dateTime->format('Y');
      $res_dia_semana = $dateTime->format('N');
      $res_data = $dateTime->format('Y-m-d');

      $stmt->execute([
        ':res_id' => $res_id,
        ':res_solic_id' => $res_solic_id,
        //':res_tipo_reserva' => $res_tipo_reserva,
        ':res_data' => $res_data,
        ':res_mes' => $res_mes,
        ':res_ano' => $res_ano,
        ':res_dia_semana' => $res_dia_semana,
        ':res_hora_inicio' => $res_hora_inicio,
        ':res_hora_fim' => $res_hora_fim,
        ':res_turno' => $res_turno,
        ':res_tipo_aula' => $res_tipo_aula,
        ':res_curso' => $res_curso,
        ':res_curso_nome' => $res_curso_nome,
        ':res_curso_extensao' => $res_curso_extensao,
        ':res_semestre' => $res_semestre,
        ':res_componente_atividade' => $res_componente_atividade,
        ':res_componente_atividade_nome' => $res_componente_atividade_nome,
        ':res_nome_atividade' => $res_nome_atividade,
        ':res_modulo' => $res_modulo,
        ':res_professor' => $res_professor,
        ':res_titulo_aula' => $res_titulo_aula,
        ':res_espaco_id' => $res_espaco_id,
        ':res_campus' => $res_campus,
        ':res_quant_pessoas' => $res_quant_pessoas,
        ':res_recursos' => $res_recursos,
        ':res_recursos_add' => $res_recursos_add,
        ':res_obs' => $res_obs,
        ':res_user_id' => $rvm_admin_id
      ]);






      // -------------------------------
      // EXCLUIR
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      if (empty($_GET['res_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $res_id   = $_GET['res_id'];
      $log_acao = 'Exclusão';

      $sql = "DELETE FROM reservas WHERE res_id = :res_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':res_id' => $res_id]);

      // OCORRÊNCIAS
      $sql = "DELETE FROM ocorrencias WHERE oco_res_id = :oco_res_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':oco_res_id' => $res_id]);










      // -------------------------------
      // EXCLUIR SELECIONADAS
      // -------------------------------
    } elseif ($acao === 'deletar_selecao') {

      $ids = $_POST['exc_selecionados']; // não filtre como numérico
      $log_acao = 'Exclusão Selecionadas';

      if (empty($ids)) {
        throw new Exception("Nenhum ID para excluir.");
      }

      $placeholders = rtrim(str_repeat('?,', count($ids)), ',');
      $stmt = $conn->prepare("DELETE FROM reservas WHERE res_id IN ($placeholders)");
      $stmt->execute($ids);







      // -------------------------------
      // AÇÃO INVÁLIDA
      // -------------------------------
    } else {
      throw new Exception("Ação inválida.");
    }


    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    if ($acao === 'cadastrar') {
      $_SESSION["msg"] = "Dados cadastrados com sucesso!";
    } elseif ($acao === 'atualizar') {
      $_SESSION["msg"] = "Dados atualizados com sucesso!";
    } elseif ($acao === 'deletar_selecao') {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
    } else {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
    }
    // -------------------------------
    header(sprintf('location: %s#ancora_reservas_confirmadas', $_SERVER['HTTP_REFERER']));
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  exit;
}
