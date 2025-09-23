<?php
// session_start();
require_once '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao']; // AÇÃO: CADASTRAR, ATUALIZAR, DELETAR
    $rvm_admin_id = $_SESSION['reservm_admin_id'];

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
        empty($_POST['res_tipo_reserva']) ||
        empty($_POST['res_hora_inicio']) ||
        empty($_POST['res_hora_fim'])
      ) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // ----------------------------------------------
      // EXTRAÇÃO DE DADOS DOS CAMPOS COMUNS
      // ----------------------------------------------
      $res_solic_id = $_POST['res_solic_id'];
      $res_campus = $_POST['res_campus'];
      $res_espaco_id = ($_POST['res_campus'] == 1) ? $_POST['res_espaco_id_cabula'] : $_POST['res_espaco_id_brotas'];
      $res_quant_pessoas = $_POST['res_quant_pessoas'];
      $res_recursos = $_POST['res_recursos'];
      $res_recursos_add = isset($_POST['res_recursos_add']) && is_array($_POST['res_recursos_add'])
        ? implode(', ', array_map('htmlspecialchars', $_POST['res_recursos_add']))
        : null;
      $res_obs = trim($_POST['res_obs']) !== '' ? nl2br(trim($_POST['res_obs'])) : NULL;
      $res_tipo_aula = $_POST['res_tipo_aula'];
      $res_curso = $_POST['res_curso'];
      $res_curso_nome = $_POST['res_curso_nome'] !== '' ? trim($_POST['res_curso_nome']) : NULL;
      $res_curso_extensao = isset($_POST['res_curso_extensao']) && $_POST['res_curso_extensao'] !== '' ? (int) $_POST['res_curso_extensao'] : null;
      $res_semestre = isset($_POST['res_semestre']) && $_POST['res_semestre'] !== '' ? (int) $_POST['res_semestre'] : null;
      $res_componente_atividade = isset($_POST['res_componente_atividade']) && $_POST['res_componente_atividade'] !== '' ? (int) $_POST['res_componente_atividade'] : null;
      $res_componente_atividade_nome = $_POST['res_componente_atividade_nome'] !== '' ? trim($_POST['res_componente_atividade_nome']) : NULL;
      $res_nome_atividade = $_POST['res_nome_atividade'] !== '' ? trim($_POST['res_nome_atividade']) : NULL;
      $res_modulo = $_POST['res_modulo'] !== '' ? trim($_POST['res_modulo']) : NULL;
      $res_titulo_aula = $_POST['res_titulo_aula'] !== '' ? trim($_POST['res_titulo_aula']) : NULL;
      $res_professor = $_POST['res_professor'] !== '' ? trim($_POST['res_professor']) : NULL;
      $res_tipo_reserva = $_POST['res_tipo_reserva'];
      $res_hora_inicio = $_POST['res_hora_inicio'];
      $res_hora_fim = $_POST['res_hora_fim'];
      $res_turno = $_POST['res_turno']; // CAMPO ÚNICO PARA AMBOS

      // VARIÁVEIS PARA OS TIPOS DE RESERVA
      $res_data_diaria = NULL;
      $res_dia_semana_diaria = NULL;
      $res_mes_diaria = NULL;
      $res_ano_diaria = NULL;
      $res_data_inicio_semanal = NULL;
      $res_data_fim_semanal = NULL;
      $res_dia_semana_fixa = NULL;

      if ($res_tipo_reserva == 1) { // Esporádica
        $res_data_diaria = $_POST['res_data'] !== '' ? $_POST['res_data'] : NULL;
        $res_dia_semana_diaria = $_POST['res_dia_semana'] !== '' ? (int) $_POST['res_dia_semana'] : NULL;
        $res_mes_diaria = $_POST['res_mes'] !== '' ? $_POST['res_mes'] : NULL;
        $res_ano_diaria = $_POST['res_ano'] !== '' ? $_POST['res_ano'] : NULL;
      } else { // Fixa (Semanal)
        $res_data_inicio_semanal = $_POST['res_data_inicio_semanal'] !== '' ? $_POST['res_data_inicio_semanal'] : NULL;
        $res_data_fim_semanal = $_POST['res_data_fim_semanal'] !== '' ? $_POST['res_data_fim_semanal'] : NULL;
        $res_dia_semana_fixa = $_POST['res_dia_semana_fixa'] !== '' ? (int) $_POST['res_dia_semana_fixa'] : NULL;
      }

      // ----------------------------------------------
    }


    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // SQL base para INSERT
      $sql_insert_res = "INSERT INTO reservas (
                                      res_id, res_codigo, res_solic_id, res_tipo_reserva, res_data,
                                      res_mes, res_ano, res_dia_semana, res_data_inicio_semanal,
                                      res_data_fim_semanal, res_hora_inicio, res_hora_fim, res_turno,
                                      res_tipo_aula, res_curso, res_curso_nome, res_curso_extensao,
                                      res_semestre, res_componente_atividade, res_componente_atividade_nome,
                                      res_nome_atividade, res_modulo, res_professor, res_titulo_aula,
                                      res_espaco_id, res_campus, res_quant_pessoas, res_recursos,
                                      res_recursos_add, res_obs, res_user_id, res_data_cad, res_data_upd
                                    ) VALUES (
                                      :res_id, :res_codigo, :res_solic_id, :res_tipo_reserva, :res_data,
                                      UPPER(:res_mes), :res_ano, :res_dia_semana, :res_data_inicio_semanal,
                                      :res_data_fim_semanal, :res_hora_inicio, :res_hora_fim, :res_turno,
                                      :res_tipo_aula, :res_curso, UPPER(:res_curso_nome), :res_curso_extensao,
                                      :res_semestre, :res_componente_atividade, UPPER(:res_componente_atividade_nome),
                                      UPPER(:res_nome_atividade), UPPER(:res_modulo), UPPER(:res_professor),
                                      UPPER(:res_titulo_aula), :res_espaco_id, :res_campus, :res_quant_pessoas,
                                      :res_recursos, :res_recursos_add, :res_obs, :res_user_id, GETDATE(), GETDATE()
                                    )";
      $stmt_insert_res = $conn->prepare($sql_insert_res);

      $reservation_inserted = false;

      // LÓGICA PARA RESERVA FIXA (SEMANAL)
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

        $inicio = new DateTime($res_data_inicio_semanal);
        $fim = new DateTime($res_data_fim_semanal);
        $fim->modify('+1 day');
        $intervalo = new DateInterval('P1D');
        $periodo = new DatePeriod($inicio, $intervalo, $fim);

        foreach ($periodo as $dataObj) {
          // VERIFICA SE O DIA DA SEMANA DO OBJETO É O MESMO SELECIONADO PELO USUÁRIO
          if ((int) $dataObj->format('N') === $res_dia_semana_fixa) {
            $res_data_loop = $dataObj->format('Y-m-d');
            $mesNumero = $dataObj->format('m');
            $res_mes_loop = $meses[$mesNumero];
            $res_ano_loop = $dataObj->format('Y');

            $res_id_loop = bin2hex(random_bytes(16));
            $tentativas_codigo_fixa = 0;
            do {
              $tentativas_codigo_fixa++;
              if ($tentativas_codigo_fixa > 5) {
                throw new Exception("Não foi possível gerar um código único para reserva fixa após várias tentativas.");
              }
              $res_codigo_fixa = 'RF' . random_int(100000, 999999);
              $existe_fixa = verificarCodigoNoBanco($res_codigo_fixa, $conn);
            } while ($existe_fixa);

            $stmt_insert_res->execute([
              ':res_id' => $res_id_loop,
              ':res_codigo' => $res_codigo_fixa,
              ':res_solic_id' => $res_solic_id,
              ':res_tipo_reserva' => $res_tipo_reserva,
              ':res_data' => $res_data_loop,
              ':res_mes' => $res_mes_loop,
              ':res_ano' => $res_ano_loop,
              ':res_dia_semana' => $res_dia_semana_fixa,
              ':res_data_inicio_semanal' => $res_data_inicio_semanal,
              ':res_data_fim_semanal' => $res_data_fim_semanal,
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
            if ($stmt_insert_res->rowCount() > 0) {
              $reservation_inserted = true;
            }
          }
        }
      } else {
        // LÓGICA PARA RESERVA ESPORÁDICA (DIÁRIA)
        $res_id = bin2hex(random_bytes(16));
        $tentativas = 0;
        do {
          $tentativas++;
          if ($tentativas > 5) {
            throw new Exception("Não foi possível gerar um código único para reserva esporádica após várias tentativas.");
          }
          $res_codigo = 'RE' . random_int(100000, 999999);
          $existe = verificarCodigoNoBanco($res_codigo, $conn);
        } while ($existe);

        $stmt_insert_res->execute([
          ':res_id' => $res_id,
          ':res_codigo' => $res_codigo,
          ':res_solic_id' => $res_solic_id,
          ':res_tipo_reserva' => $res_tipo_reserva,
          ':res_data' => $res_data_diaria,
          ':res_mes' => $res_mes_diaria,
          ':res_ano' => $res_ano_diaria,
          ':res_dia_semana' => $res_dia_semana_diaria,
          ':res_data_inicio_semanal' => NULL,
          ':res_data_fim_semanal' => NULL,
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

        if ($stmt_insert_res->rowCount() > 0) {
          $reservation_inserted = true;
        }
      }

      if ($reservation_inserted) {
        $status_reservado_id = 4;
        $status_aguardando_reserva_id = 5;

        // Busca o status atual da solicitação para verificar se está em "Aguardando Reserva"
        $sql_check_status = "SELECT solic_sta_status FROM solicitacao_status WHERE solic_sta_solic_id = :solic_sta_solic_id";
        $stmt_check_status = $conn->prepare($sql_check_status);
        $stmt_check_status->execute([':solic_sta_solic_id' => $res_solic_id]);
        $current_status = $stmt_check_status->fetchColumn();

        // Apenas se o status atual for 'Aguardando Reserva', atualiza para 'Reservado' e registra a ação.
        if ($current_status == $status_aguardando_reserva_id) {
          // Atualiza o status principal da solicitação na tabela `solicitacao_status`
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

          // Insere o registro na tabela de histórico `solicitacao_analise_status`
          // $observacao_status = "Reserva cadastrada.";
          $sql_insert_status_log = "INSERT INTO solicitacao_analise_status
            (sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd)
            VALUES (:solic_id, :status_id, :observacao, :user_id, GETDATE(), GETDATE())";

          $stmt_insert_log = $conn->prepare($sql_insert_status_log);
          $stmt_insert_log->execute([
            ':solic_id' => $res_solic_id,
            ':status_id' => $status_reservado_id,
            ':observacao' => $observacao_status,
            ':user_id' => $rvm_admin_id
          ]);

          // Insere o log de ação, se necessário
          $log_acao_status = 'Status_Solicitacao_Reservado';
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

    } elseif ($acao === 'atualizar') {
      if (empty($_POST['res_id'])) {
        throw new Exception("ID é obrigatório para atualização.");
      }

      $res_ids_str = $_POST['res_id'];
      $log_acao = 'Atualização';

      $ids = explode(',', $res_ids_str);
      $res_tipo_reserva = $_POST['res_tipo_reserva'];

      $updates = [];
      $params = [];

      // Mapeamento de campos do POST para os campos do banco de dados
      $res_espaco_id = ($_POST['res_campus'] == 1) ? ($_POST['res_espaco_id_cabula'] ?? null) : ($_POST['res_espaco_id_brotas'] ?? null);

      $campos_do_post = [
        'res_solic_id' => $_POST['res_solic_id'] ?? null,
        'res_campus' => $_POST['res_campus'] ?? null,
        'res_espaco_id' => $res_espaco_id,
        'res_quant_pessoas' => $_POST['res_quant_pessoas'] ?? null,
        'res_recursos' => $_POST['res_recursos'] ?? null,
        'res_recursos_add' => isset($_POST['res_recursos_add']) ? implode(', ', $_POST['res_recursos_add']) : null,
        'res_obs' => trim($_POST['res_obs']) !== '' ? nl2br(trim($_POST['res_obs'])) : NULL,
        'res_tipo_aula' => $_POST['res_tipo_aula'] ?? null,
        'res_curso' => $_POST['res_curso'] ?? null,
        'res_curso_nome' => trim($_POST['res_curso_nome']) !== '' ? trim($_POST['res_curso_nome']) : NULL,
        'res_curso_extensao' => $_POST['res_curso_extensao'] ?? null,
        'res_semestre' => $_POST['res_semestre'] ?? null,
        'res_componente_atividade' => $_POST['res_componente_atividade'] ?? null,
        'res_componente_atividade_nome' => trim($_POST['res_componente_atividade_nome']) !== '' ? trim($_POST['res_componente_atividade_nome']) : NULL,
        'res_nome_atividade' => trim($_POST['res_nome_atividade']) !== '' ? trim($_POST['res_nome_atividade']) : NULL,
        'res_modulo' => trim($_POST['res_modulo']) !== '' ? trim($_POST['res_modulo']) : NULL,
        'res_professor' => trim($_POST['res_professor']) !== '' ? trim($_POST['res_professor']) : NULL,
        'res_titulo_aula' => trim($_POST['res_titulo_aula']) !== '' ? trim($_POST['res_titulo_aula']) : NULL,
        'res_tipo_reserva' => $_POST['res_tipo_reserva'] ?? null,
        'res_hora_inicio' => $_POST['res_hora_inicio'] ?? null,
        'res_hora_fim' => $_POST['res_hora_fim'] ?? null,
        'res_turno' => $_POST['res_turno'] ?? null,
        // Campos de data/dia
        'res_dia_semana' => $_POST['res_dia_semana_fixa'] ?? ($_POST['res_dia_semana'] ?? null),
        'res_data_inicio_semanal' => $_POST['res_data_inicio_semanal'] ?? null,
        'res_data_fim_semanal' => $_POST['res_data_fim_semanal'] ?? null,
        'res_data' => $_POST['res_data'] ?? null,
        'res_mes' => $_POST['res_mes'] ?? null,
        'res_ano' => $_POST['res_ano'] ?? null,
      ];

      foreach ($campos_do_post as $campo => $valor) {
        // Ignorar campos de ID de espaço que não foram selecionados
        if (($campo === 'res_espaco_id_cabula' || $campo === 'res_espaco_id_brotas') && empty($valor)) {
          continue;
        }

        // Lógica para construir o SET do UPDATE
        if ($valor !== null && $valor !== '') {
          $updates[] = str_replace(['res_espaco_id_cabula', 'res_espaco_id_brotas', 'res_dia_semana_fixa'], ['res_espaco_id', 'res_espaco_id', 'res_dia_semana'], $campo) . " = ?";
          $params[] = $valor;
        }
      }

      // Executa a atualização nos registros existentes
      if (!empty($updates)) {
        $update_sql = "UPDATE reservas SET " . implode(', ', $updates) . ", res_data_upd = GETDATE() WHERE res_id IN (" . rtrim(str_repeat('?,', count($ids)), ',') . ")";
        $params_update = array_merge($params, $ids);

        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->execute($params_update);
      }

      // Lógica para criar novas reservas se o período de repetição for expandido
      if ($res_tipo_reserva == 2 && isset($_POST['res_data_inicio_semanal']) && isset($_POST['res_data_fim_semanal'])) {
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

        $inicio = new DateTime($_POST['res_data_inicio_semanal']);
        $fim = new DateTime($_POST['res_data_fim_semanal']);
        $fim->modify('+1 day');
        $intervalo = new DateInterval('P1D');
        $periodo = new DatePeriod($inicio, $intervalo, $fim);

        // Consulta para obter as datas já existentes
        $sql_existing_dates = "SELECT res_data FROM reservas WHERE res_solic_id = ? AND res_tipo_reserva = 2 AND res_dia_semana = ?";
        $stmt_existing = $conn->prepare($sql_existing_dates);
        $stmt_existing->execute([$_POST['res_solic_id'], $_POST['res_dia_semana_fixa']]);
        $existing_dates = $stmt_existing->fetchAll(PDO::FETCH_COLUMN);
        $existing_dates = array_map(function ($date) {
          return (new DateTime($date))->format('Y-m-d');
        }, $existing_dates);

        $sql_insert_res = "INSERT INTO reservas (
            res_id, res_codigo, res_solic_id, res_tipo_reserva, res_data,
            res_mes, res_ano, res_dia_semana, res_data_inicio_semanal,
            res_data_fim_semanal, res_hora_inicio, res_hora_fim, res_turno,
            res_tipo_aula, res_curso, res_curso_nome, res_curso_extensao,
            res_semestre, res_componente_atividade, res_componente_atividade_nome,
            res_nome_atividade, res_modulo, res_professor, res_titulo_aula,
            res_espaco_id, res_campus, res_quant_pessoas, res_recursos,
            res_recursos_add, res_obs, res_user_id, res_data_cad, res_data_upd
        ) VALUES (
            :res_id, :res_codigo, :res_solic_id, :res_tipo_reserva, :res_data,
            UPPER(:res_mes), :res_ano, :res_dia_semana, :res_data_inicio_semanal,
            :res_data_fim_semanal, :res_hora_inicio, :res_hora_fim, :res_turno,
            :res_tipo_aula, :res_curso, UPPER(:res_curso_nome), :res_curso_extensao,
            :res_semestre, :res_componente_atividade, UPPER(:res_componente_atividade_nome),
            UPPER(:res_nome_atividade), UPPER(:res_modulo), UPPER(:res_professor),
            UPPER(:res_titulo_aula), :res_espaco_id, :res_campus, :res_quant_pessoas,
            :res_recursos, :res_recursos_add, :res_obs, :res_user_id, GETDATE(), GETDATE()
        )";
        $stmt_insert_res = $conn->prepare($sql_insert_res);

        foreach ($periodo as $dataObj) {
          $data_loop_formatada = $dataObj->format('Y-m-d');
          if ((int) $dataObj->format('N') === (int) $_POST['res_dia_semana_fixa'] && !in_array($data_loop_formatada, $existing_dates)) {
            $mesNumero = $dataObj->format('m');
            $res_mes_loop = $meses[$mesNumero];
            $res_ano_loop = $dataObj->format('Y');

            $res_id_loop = bin2hex(random_bytes(16));
            $tentativas_codigo_fixa = 0;
            do {
              $tentativas_codigo_fixa++;
              if ($tentativas_codigo_fixa > 5) {
                throw new Exception("Não foi possível gerar um código único para reserva fixa após várias tentativas.");
              }
              $res_codigo_fixa = 'RF' . random_int(100000, 999999);
              $existe_fixa = verificarCodigoNoBanco($res_codigo_fixa, $conn);
            } while ($existe_fixa);

            $stmt_insert_res->execute([
              ':res_id' => $res_id_loop,
              ':res_codigo' => $res_codigo_fixa,
              ':res_solic_id' => $_POST['res_solic_id'],
              ':res_tipo_reserva' => $_POST['res_tipo_reserva'],
              ':res_data' => $data_loop_formatada,
              ':res_mes' => $res_mes_loop,
              ':res_ano' => $res_ano_loop,
              ':res_dia_semana' => $_POST['res_dia_semana_fixa'],
              ':res_data_inicio_semanal' => $_POST['res_data_inicio_semanal'],
              ':res_data_fim_semanal' => $_POST['res_data_fim_semanal'],
              ':res_hora_inicio' => $_POST['res_hora_inicio'],
              ':res_hora_fim' => $_POST['res_hora_fim'],
              ':res_turno' => $_POST['res_turno'],
              ':res_tipo_aula' => $_POST['res_tipo_aula'],
              ':res_curso' => $_POST['res_curso'],
              ':res_curso_nome' => $_POST['res_curso_nome'],
              ':res_curso_extensao' => $_POST['res_curso_extensao'],
              ':res_semestre' => $_POST['res_semestre'],
              ':res_componente_atividade' => $_POST['res_componente_atividade'],
              ':res_componente_atividade_nome' => $_POST['res_componente_atividade_nome'],
              ':res_nome_atividade' => $_POST['res_nome_atividade'],
              ':res_modulo' => $_POST['res_modulo'],
              ':res_professor' => $_POST['res_professor'],
              ':res_titulo_aula' => $_POST['res_titulo_aula'],
              ':res_espaco_id' => $res_espaco_id,
              ':res_campus' => $_POST['res_campus'],
              ':res_quant_pessoas' => $_POST['res_quant_pessoas'],
              ':res_recursos' => $_POST['res_recursos'],
              ':res_recursos_add' => isset($_POST['res_recursos_add']) ? implode(', ', $_POST['res_recursos_add']) : null,
              ':res_obs' => trim($_POST['res_obs']) !== '' ? nl2br(trim($_POST['res_obs'])) : NULL,
              ':res_user_id' => $rvm_admin_id
            ]);
          }
        }
      }
    } elseif ($_GET['acao'] === 'deletar') {
      if (empty($_GET['res_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $res_id = $_GET['res_id'];
      $log_acao = 'Exclusão';
      $sql = "DELETE FROM reservas WHERE res_id = :res_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':res_id' => $res_id]);
      $sql = "DELETE FROM ocorrencias WHERE oco_res_id = :oco_res_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':oco_res_id' => $res_id]);

    } elseif ($acao === 'deletar_selecao') {
      $ids = $_POST['exc_selecionados'];
      $log_acao = 'Exclusão Selecionadas';
      if (empty($ids)) {
        throw new Exception("Nenhum ID para excluir.");
      }
      $placeholders = rtrim(str_repeat('?,', count($ids)), ',');
      $stmt = $conn->prepare("DELETE FROM reservas WHERE res_id IN ($placeholders)");
      $stmt->execute($ids);

    } else {
      throw new Exception("Ação inválida.");
    }

    $conn->commit();
    if ($acao === 'cadastrar') {
      $_SESSION["msg"] = "Dados cadastrados com sucesso!";
    } elseif ($acao === 'atualizar') {
      $_SESSION["msg"] = "Dados atualizados com sucesso!";
    } elseif ($acao === 'deletar_selecao' || $_GET['acao'] === 'deletar') {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
    }

    header(sprintf('location: %s#ancora_reservas_confirmadas', $_SERVER['HTTP_REFERER']));
    exit;

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
?>