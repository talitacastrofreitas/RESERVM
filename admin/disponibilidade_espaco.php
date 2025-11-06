<?php

date_default_timezone_set('America/Sao_Paulo');

include 'includes/header.php';
?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

<div class="row breadcrumb_painel">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Disponibilidade</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="">Disponibilidade</a></li>
          <li class="breadcrumb-item active">Disponibilidade</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
          <div class="row g-3">
            <div class="col-12 col-sm-6 col-md-3 col-lg-2 col-xl-2">
              <label for="data_inicio" class="form-label">Data Início</label>
              <input type="date" class="form-control flatpickr-input" name="data_inicio" id="data_inicio"
                value="<?= htmlspecialchars($_GET['data_inicio'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="col-12 col-sm-6 col-md-3 col-lg-2 col-xl-2">
              <label for="data_fim" class="form-label">Data Fim</label>
              <input type="date" class="form-control flatpickr-input" name="data_fim" id="data_fim"
                value="<?= htmlspecialchars($_GET['data_fim'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="col-12 col-sm-6 col-md-3 col-lg-2 col-xl-2">
              <label for="hora_inicio" class="form-label">Hora Início</label>
              <input type="time" class="form-control chf_hora" name="hora_inicio" id="hora_inicio"
                value="<?= htmlspecialchars($_GET['hora_inicio'] ?? '07:00') ?>">
            </div>
            <div class="col-12 col-sm-6 col-md-3 col-lg-2 col-xl-2">
              <label for="hora_fim" class="form-label">Hora Fim</label>
              <input type="time" class="form-control chf_hora" name="hora_fim" id="hora_fim"
                value="<?= htmlspecialchars($_GET['hora_fim'] ?? '22:00') ?>">
            </div>
            <script>
              flatpickr(".chf_hora", {
                enableTime: true, // ativa o seletor de hora
                noCalendar: true, // oculta o calendário
                dateFormat: "H:i", // formato 24h: horas:minutos
                time_24hr: true, // garante o formato 24h
                allowInput: true // permite apagar e digitar manualmente
              });
            </script>

            <!-- <div class="col-12 col-md-6 col-lg-2 col-xl-2">
              <label for="espaco_id" class="form-label">Espaço/Sala</label>
              <select class="form-select" name="espaco_id" id="espaco_id">
                <option value="">Todos os Espaços</option>
                <?php
                // Fetch all spaces to populate the dropdown
                try {
                  $stmt_spaces = $conn->query("SELECT esp_id, esp_nome_local FROM espaco ORDER BY esp_nome_local");
                  $spaces = $stmt_spaces->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($spaces as $space) {
                    $selected = (isset($_GET['espaco_id']) && $_GET['espaco_id'] == $space['esp_id']) ? 'selected' : '';
                    echo "<option value='{$space['esp_id']}' {$selected}>" . htmlspecialchars($space['esp_nome_local']) . "</option>";
                  }
                } catch (PDOException $e) {
                  error_log('Error fetching spaces for dropdown: ' . $e->getMessage());
                }
                ?>
              </select>
            </div> -->


            <div class="col-12 col-md-6 col-lg-2 col-xl-2">
              <label for="espaco_id" class="form-label">Espaço/Sala</label>
              <select class="form-select" name="espaco_id" id="espaco_id">
                <option value="">Todos os Espaços</option>
                <?php
                // Fetch all spaces to populate the dropdown
                try {
                  // Alterar a query para selecionar também a coluna 'esp_codigo'
                  // E adicionar a ordenação por 'esp_unidade' e 'esp_nome_local'
                  $stmt_spaces = $conn->query("SELECT esp_id, esp_codigo, esp_nome_local FROM espaco ORDER BY esp_unidade, esp_nome_local");
                  $spaces = $stmt_spaces->fetchAll(PDO::FETCH_ASSOC);
                  foreach ($spaces as $space) {
                    $selected = (isset($_GET['espaco_id']) && $_GET['espaco_id'] == $space['esp_id']) ? 'selected' : '';

                    // Concatena o código e o nome do espaço para a exibição
                    $displayText = $space['esp_codigo'] . ' - ' . htmlspecialchars($space['esp_nome_local']);

                    echo "<option value='{$space['esp_id']}' {$selected}>" . $displayText . "</option>";
                  }
                } catch (PDOException $e) {
                  error_log('Error fetching spaces for dropdown: ' . $e->getMessage());
                }
                ?>
              </select>
            </div>

            <div
              class="col-12 col-md-6 col-lg-2 col-xl-2 d-flex align-items-end justify-content-center justify-content-md-end">
              <div class="d-flex w-100">
                <button type="submit" class="btn botao botao_azul_escuro waves-effect w-100">Filtrar</button>
                <!-- <div onclick="window.location.href='painel.php'" class="btn botao botao_cinza waves-effect w-100 ms-2">
                  Limpar</div> -->
                <div onclick="limparFiltroCompleto()" class="btn botao botao_cinza waves-effect w-100 ms-2">
                  Limpar</div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .header_time {
    width: 100px;
    font-size: 11px;
    padding: 0px !important;
    text-align: center;
  }

  @media (max-width: 1600px) {
    .header_time {
      display: none;
    }
  }

  .table_painel thead th,
  .table_painel thead td,
  .table_painel thead tr:hover {
    background: transparent !important;
    padding: 6px 2px 6px 6px !important;
  }

  .table_painel tbody tr td.barra {
    width: 100px !important;
    font-size: 11px !important;
    /* Ajustado para caber mais texto */
    padding: 0px 5px !important;
    text-align: center !important;
    background: #2d9866 !important;
    color: #fff !important;
  }

  .table_painel tbody tr td.barra.conflito {
    background-color: var(--vermelho) !important;
  }

  .table_painel tbody tr td.barra.blocked {
    background-color: #808080 !important;
    cursor: not-allowed;
  }


  /*.table_painel tbody tr td.barra.border_l {
    border-left: 6px solid #fff !important;
  }

  .table_painel tbody tr td.barra.border_r {
    border-right: 6px solid #fff !important;
  }
*/

  .table_painel tbody tr {
    background: #F2F4F8;
  }

  .table_painel tbody tr:hover {
    background: #E1E9F1;
  }

  .table_painel tbody th,
  .table_painel tbody td {
    padding: 0 !important;
    border-bottom: 6px solid #fff !important;
    border-color: #fff !important;
  }

  .table_painel .col_cod {
    /* padding: 5px 10px !important; */
    text-align: center;
  }

  .table_painel td {
    height: 32.1px !important;
  }


  .table_painel .col_loc {
    padding: 0 10px;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    width: 150px;
    max-width: 250px;
    box-sizing: border-box !important;
  }

  .table_painel .col_ch {
    padding: 0 10px;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    width: 80px;
    max-width: 120px;
    box-sizing: border-box !important;
  }

  .table_painel .box {
    width: 30px !important;
    padding: 0 12px !important;
  }

  .table_painel .teste_status {
    border-left: 6px solid #FFFF;
    border-right: 6px solid #FFF;
    font-weight: 500;
    padding: 5px !important;
    font-size: 11px !important;
    /* margin: 0px -6px 0px 0px; */
    width: 160px;
    min-width: 160px;
    max-width: 160px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* .table_painel .box_c {
 background: #E7DCED !important;
 border-left: 6px solid #fff;
 border-right: 6px solid #fff;
 font-weight: 500;
 color: #8652A6 !important;
 font-size: 11px !important;
 text-align: center !important;
 padding: 5px !important;
 margin: 0px -6px 0px 0px;
 }*/

  .teste_status.bg_info_azul_escuro {
    color: var(--azul_escuro) !important;
    background-color: var(--azul_escuro_alpha) !important;
  }

  .teste_status.bg_info_vermelho {
    color: var(--vermelho) !important;
    background-color: var(--vermelho_alpha) !important;
  }





  /* .border_l {
 border-left: 6px solid #fff !important;
 padding: 5px 10px !important;
 margin: 0 5px !important;
 }


 .border_r {
 border-right: 0px solid #fff !important;
 padding: 5px 10px !important;
 margin: 0 5px !important;
 }


 @media (max-width: 1600px) {
 .border_l {
  border-left: 6px solid #fff !important;
 }

 .border_r {
  border-right: 0px solid #fff !important;
 }

 .table_painel tbody tr td.barra.border_r {
  border-right: 0px solid #fff !important;
 }
 }*/
</style>

<?php
try {
  // Get filter parameters, setting defaults if not provided
  $data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d');
  $data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d');
  $hora_inicio_filter = isset($_GET['hora_inicio']) ? $_GET['hora_inicio'] : '07:00';
  $hora_fim_filter = isset($_GET['hora_fim']) ? $_GET['hora_fim'] : '22:00';
  $espaco_id_filter = isset($_GET['espaco_id']) && $_GET['espaco_id'] !== '' ? $_GET['espaco_id'] : null;

  // A variável $start_date_day_of_week é usada apenas para o cabeçalho "Segunda-feiras de..."
  $start_date_day_of_week = (new DateTime($data_inicio))->format('w'); // 0 (Dom) a 6 (Sáb)

  // --- Consulta para buscar todos os espaços e suas infos ---
  // $sql_espacos = "SELECT esp_id, esp_nome_local, esp_nome_local_resumido, esp_codigo, tipesp_tipo_espaco_icone, and_andar_icone, pav_pavilhao_icone, uni_unidade_icone
  //  FROM espaco AS esp
  //  INNER JOIN tipo_espaco AS tipesp ON tipesp.tipesp_id = esp.esp_tipo_espaco
  //  LEFT JOIN andares ON andares.and_id = esp.esp_andar
  //  LEFT JOIN pavilhoes ON pavilhoes.pav_id = esp.esp_pavilhao
  //  INNER JOIN unidades ON unidades.uni_id = esp.esp_unidade
  //  WHERE 1=1 ";

  // $params_espacos = [];

  // if ($espaco_id_filter) {
  //   $sql_espacos .= " AND esp.esp_id = :espaco_id ";
  //   $params_espacos[':espaco_id'] = $espaco_id_filter;
  // }
  // $sql_espacos .= " ORDER BY esp_nome_local";

  // Query SQL
  $sql_espacos = "SELECT esp_id, esp_nome_local, esp_nome_local_resumido, esp_codigo, esp.esp_unidade, tipesp_tipo_espaco_icone, and_andar_icone, pav_pavilhao_icone, uni_unidade_icone
   FROM espaco AS esp
   INNER JOIN tipo_espaco AS tipesp ON tipesp.tipesp_id = esp.esp_tipo_espaco
   LEFT JOIN andares ON andares.and_id = esp.esp_andar
   LEFT JOIN pavilhoes ON pavilhoes.pav_id = esp.esp_pavilhao
   INNER JOIN unidades ON unidades.uni_id = esp.esp_unidade
   WHERE 1=1 ";

  $params_espacos = [];

  if ($espaco_id_filter) {
    $sql_espacos .= " AND esp.esp_id = :espaco_id ";
    $params_espacos[':espaco_id'] = $espaco_id_filter;
  }
  $sql_espacos .= " ORDER BY esp.esp_unidade, esp_nome_local";



  $stmt_espacos = $conn->prepare($sql_espacos);
  $stmt_espacos->execute($params_espacos);
  $all_spaces = $stmt_espacos->fetchAll(PDO::FETCH_ASSOC);


  // --- Consulta para buscar TODAS as reservas relevantes (por data ou semana) no período ---
  // Esta consulta agora é mais abrangente para pegar tudo no intervalo de data/hora
  $sql_reservas = "SELECT
   res_id, -- Adicionado para identificar cada reserva individualmente
   res_espaco_id, res_data, res_hora_inicio, res_hora_fim, res_solic_id, res_tipo_reserva, res_dia_semana
   FROM reservas
   WHERE (
   (res_tipo_reserva = 1 AND res_data BETWEEN :data_inicio_diaria AND :data_fim_diaria)
   OR
   (res_tipo_reserva = 2 AND (res_data IS NULL OR res_data <= :data_fim_semanal))
   )
   AND res_hora_inicio < :hora_fim_filter AND res_hora_fim > :hora_inicio_filter
   ";
  $params_reservas = [
    ':data_inicio_diaria' => $data_inicio,
    ':data_fim_diaria' => $data_fim,
    ':data_fim_semanal' => $data_fim, // Para semanais que começam antes do fim do período
    ':hora_inicio_filter' => $hora_inicio_filter,
    ':hora_fim_filter' => $hora_fim_filter,
  ];
  if ($espaco_id_filter) {
    $sql_reservas .= " AND res_espaco_id = :espaco_id ";
    $params_reservas[':espaco_id'] = $espaco_id_filter;
  }

  $stmt_reservas = $conn->prepare($sql_reservas);
  $stmt_reservas->execute($params_reservas);
  $all_reservations = $stmt_reservas->fetchAll(PDO::FETCH_ASSOC);


  $sql_blocked_dates = "SELECT
 cdb.dbloq_data,
 cdbm.dbloqm_motivo AS dbloq_motivo
 FROM conf_dias_bloqueadas AS cdb
 INNER JOIN conf_dias_bloqueadas_motivo AS cdbm ON cdb.dbloq_motivo = cdbm.dbloqm_id
 WHERE cdb.dbloq_data BETWEEN :data_inicio AND :data_fim
 AND cdb.dbloq_data IS NOT NULL
 AND cdb.dbloq_status = 1";
  $stmt_blocked_dates = $conn->prepare($sql_blocked_dates);
  $stmt_blocked_dates->execute([':data_inicio' => $data_inicio, ':data_fim' => $data_fim]);
  $blocked_dates = [];
  while ($row = $stmt_blocked_dates->fetch(PDO::FETCH_ASSOC)) {
    $blocked_dates[$row['dbloq_data']] = $row['dbloq_motivo'];
  }

  $sql_blocked_week_days = "SELECT
 cdb.dbloq_dia,
 cdbm.dbloqm_motivo AS dbloq_motivo
 FROM conf_dias_bloqueadas AS cdb
 INNER JOIN conf_dias_bloqueadas_motivo AS cdbm ON cdb.dbloq_motivo = cdbm.dbloqm_id
 WHERE cdb.dbloq_data IS NULL
 AND cdb.dbloq_status = 1";
  $stmt_blocked_week_days = $conn->query($sql_blocked_week_days);
  $blocked_week_days = [];
  while ($row = $stmt_blocked_week_days->fetch(PDO::FETCH_ASSOC)) {
    $blocked_week_days[(int) $row['dbloq_dia']] = $row['dbloq_motivo'];
  }

  // --- Processamento para construir a grade de disponibilidade ---
  $processed_salas = [];
  $current_date_obj = new DateTime($data_inicio);
  $end_date_obj = new DateTime($data_fim);

  while ($current_date_obj <= $end_date_obj) {
    $current_date_str = $current_date_obj->format('Y-m-d');
    $current_day_of_week_num = (int) $current_date_obj->format('w'); // 0 (Dom) a 6 (Sáb)

    // RE-HABILITADO: Apenas processa a data se o dia da semana da data atual
    // for o mesmo da data de início do filtro.
    if ($current_day_of_week_num == $start_date_day_of_week) {

      foreach ($all_spaces as $space) {
        $chave = $space['esp_id'] . '|' . $space['esp_nome_local'] . '|' . $current_date_str;

        $is_current_date_blocked = isset($blocked_dates[$current_date_str]) ? $blocked_dates[$current_date_str] : false;
        $is_current_week_day_blocked = isset($blocked_week_days[$current_day_of_week_num]) ? $blocked_week_days[$current_day_of_week_num] : false;

        $processed_salas[$chave] = [
          'space_info' => $space,
          'reservations_for_day' => [],
          'is_blocked_date' => $is_current_date_blocked,
          'is_blocked_week' => $is_current_week_day_blocked,
          'current_display_date' => $current_date_str
        ];
      }

      // Popula as reservas para o dia atual (para todos os espaços)
      foreach ($all_reservations as $reserva) {
        $reservation_applies_to_current_day = false;

        // Daily reservations: MUST match current date
        if ($reserva['res_tipo_reserva'] == 1 && $reserva['res_data'] === $current_date_str) {
          $reservation_applies_to_current_day = true;
        }

        // Weekly reservations: MUST match current day of week AND start date must be <= current date
        // Ajuste para normalizar o dia da semana do DB para o formato PHP (0-6)
        // Usando o mapeamento mais comum: 1=Seg a 6=Sáb, 7=Dom.
        $db_day_of_week_normalized = (int) $reserva['res_dia_semana'];
        if ($db_day_of_week_normalized == 7) { // Se 7 é Domingo no DB, converte para 0 (PHP)
          $db_day_of_week_normalized = 0;
        }
        // Se seu DB já usa 0-6 (Dom-Sáb), então (int) $reserva['res_dia_semana'] já estaria correto.

        if ($reserva['res_tipo_reserva'] == 2 && $db_day_of_week_normalized == $current_day_of_week_num) {
          $res_start_date_obj = $reserva['res_data'] ? new DateTime($reserva['res_data']) : null;

          if (($res_start_date_obj === null || $current_date_obj >= $res_start_date_obj)) {
            $reservation_applies_to_current_day = true;
          }
        }

        // Se a reserva se aplica ao dia atual E ao espaço filtrado (se houver)
        if ($reservation_applies_to_current_day && ($espaco_id_filter === null || $reserva['res_espaco_id'] == $espaco_id_filter)) {
          $found_space_name = '';
          foreach ($all_spaces as $s) { // Encontra o nome do local para construir a chave
            if ($s['esp_id'] == $reserva['res_espaco_id']) { // Correção: usar res_espaco_id da reserva
              $found_space_name = $s['esp_nome_local'];
              break;
            }
          }
          $chave_reserva = $reserva['res_espaco_id'] . '|' . $found_space_name . '|' . $current_date_str;

          if (isset($processed_salas[$chave_reserva])) {
            $processed_salas[$chave_reserva]['reservations_for_day'][] = $reserva;
          }
        }
      }
    } // FECHAMENTO DO IF RE-HABILITADO
    $current_date_obj->modify('+1 day');
  }

  //   // Ordena os dados processados para exibição primeiro por data, depois por nome da sala.
//   uksort($processed_salas, function ($a, $b) {
//     list($esp_id_a, $sala_a, $date_a) = explode('|', $a);
//     list($esp_id_b, $sala_b, $date_b) = explode('|', $b);

  //     $date_comparison = strcmp($date_a, $date_b);
//     if ($date_comparison !== 0) {
//       return $date_comparison;
//     }
//     return strcmp($sala_a, $sala_b);
//   });
// } catch (PDOException $e) {
//   echo 'Connection failed: ' . $e->getMessage();
//   $processed_salas = [];
// }

  // Ordena os dados processados para exibição primeiro por data, depois por unidade e, por fim, pelo nome da sala.
  uksort($processed_salas, function ($a, $b) use ($all_spaces) {
    list($esp_id_a, $sala_a, $date_a) = explode('|', $a);
    list($esp_id_b, $sala_b, $date_b) = explode('|', $b);

    $date_comparison = strcmp($date_a, $date_b);
    if ($date_comparison !== 0) {
      return $date_comparison;
    }

    // Busca a unidade para cada espaço, pois não está na chave
    $unidade_a = null;
    $unidade_b = null;

    foreach ($all_spaces as $space) {
      if ($space['esp_id'] == $esp_id_a) {
        $unidade_a = $space['esp_unidade']; // Mudança para usar o valor numérico da unidade
      }
      if ($space['esp_id'] == $esp_id_b) {
        $unidade_b = $space['esp_unidade']; // Mudança para usar o valor numérico da unidade
      }
      if ($unidade_a !== null && $unidade_b !== null) {
        break;
      }
    }

    // Compara primeiro pela unidade, numericamente para garantir que 1 venha antes de 2, 10, etc.
    // Usamos o operador de comparação <=> (Spaceship operator) para simplificar
    $unidade_comparison = $unidade_a <=> $unidade_b;
    if ($unidade_comparison !== 0) {
      return $unidade_comparison;
    }

    // Se as unidades são iguais, compara pelo nome da sala
    return strcmp($sala_a, $sala_b);
  });
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
  $processed_salas = [];
}
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-sm-6 text-sm-start text-center">
            <h5 class="card-title mb-sm-0 mb-2">Lista de Disponibilidade</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <?php
            $meses = [
              1 => 'Janeiro',
              2 => 'Fevereiro',
              3 => 'Março',
              4 => 'Abril',
              5 => 'Maio',
              6 => 'Junho',
              7 => 'Julho',
              8 => 'Agosto',
              9 => 'Setembro',
              10 => 'Outubro',
              11 => 'Novembro',
              12 => 'Dezembro'
            ];
            $diasSemana = [
              'Sunday' => 'Domingo',
              'Monday' => 'Segunda-feira',
              'Tuesday' => 'Terça-feira',
              'Wednesday' => 'Quarta-feira',
              'Thursday' => 'Quinta-feira',
              'Friday' => 'Sexta-feira',
              'Saturday' => 'Sábado'
            ];

            $dataInicioObj = new DateTime($data_inicio);
            $dataFimObj = new DateTime($data_fim);

            // Cabeçalho da data/período
            $header_date_text = '';
            if ($data_inicio == $data_fim) {
              $dia = $dataInicioObj->format('d');
              $mes = (int) $dataInicioObj->format('m');
              $ano = $dataInicioObj->format('Y');
              $diaSemanaIngles = $dataInicioObj->format('l');
              $diaSemana = $diasSemana[$diaSemanaIngles];
              $header_date_text = "{$diaSemana}, {$dia} de {$meses[$mes]} de {$ano}";
            } else {
              $dia_inicio = $dataInicioObj->format('d');
              $mes_inicio = (int) $dataInicioObj->format('m');
              $ano_inicio = $dataInicioObj->format('Y');

              $dia_fim = $dataFimObj->format('d');
              $mes_fim = (int) $dataFimObj->format('m');
              $ano_fim = $dataFimObj->format('Y');

              // Mantido o cabeçalho "Dia da semana + s" já que o filtro está ativo
              $diaSemanaInicioIngles = $dataInicioObj->format('l');
              $diaSemanaInicio = $diasSemana[$diaSemanaInicioIngles];
              $header_date_text = "{$diaSemanaInicio}s de {$dia_inicio} de {$meses[$mes_inicio]} de {$ano_inicio} a {$dia_fim} de {$meses[$mes_fim]} de {$ano_fim}";
            }
            echo $header_date_text;
            ?>
          </div>
        </div>
      </div>
      <div class="card-body px-3 py-1">
        <div class="table-responsive">
          <table class="table align-middle table_painel">
            <thead>
              <tr>
                <th>Local</th>
                <th>Data</th>
                <th>Dia</th>
                <th>Mês</th>
                <th>Ano</th>
                <th>Hora Início</th>
                <th>Hora Fim</th>
                <th>Status</th>
                <?php
                $start_time_stamp = strtotime($hora_inicio_filter);
                $end_time_stamp = strtotime($hora_fim_filter);
                if ($end_time_stamp <= $start_time_stamp) {
                  $end_time_stamp = $start_time_stamp + 1800;
                }
                for ($i = $start_time_stamp; $i < $end_time_stamp; $i += 1800) {
                  ?>
                  <th class="header_time"><?= date("H:i", $i) ?></th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php
              if (empty($processed_salas)) {
                $total_cols = 8 + ceil(($end_time_stamp - $start_time_stamp) / 1800);
                echo '<tr><td colspan="' . $total_cols . '" class="text-center py-4">Nenhuma disponibilidade encontrada para os filtros selecionados.</td></tr>';
              } else {
                foreach ($processed_salas as $chave => $data_for_row) {
                  $space_info = $data_for_row['space_info'];
                  $reservations_for_day = $data_for_row['reservations_for_day'];
                  $is_blocked_date_motivo = $data_for_row['is_blocked_date'];
                  $is_blocked_week_motivo = $data_for_row['is_blocked_week'];
                  $current_display_date = $data_for_row['current_display_date'];

                  $display_date_obj = new DateTime($current_display_date);
                  $display_day_of_week = $diasSemana[$display_date_obj->format('l')];
                  $display_month_name = $meses[(int) $display_date_obj->format('m')];
                  $display_year = $display_date_obj->format('Y');

                  $status_data_text = 'DATA NÃO BLOQUEADA';
                  $status_data_class = 'bg_info_azul_escuro ';
                  $is_slot_blocked_overall = false;

                  if ($is_blocked_date_motivo !== false) {
                    $status_data_text = htmlspecialchars($is_blocked_date_motivo);
                    $status_data_class = 'bg_info_vermelho';
                    $is_slot_blocked_overall = true;
                  } elseif ($is_blocked_week_motivo !== false) {
                    $status_data_text = htmlspecialchars($is_blocked_week_motivo);
                    $status_data_class = 'bg_info_vermelho';
                    $is_slot_blocked_overall = true;
                  }

                  // Esta seção é para as colunas "Hora Início" e "Hora Fim" da tabela principal.
                  // Pega a reserva que inicia mais cedo para preencher essas colunas.
                  $reserva_hora_inicio = '';
                  $reserva_hora_fim = '';
                  if (!$is_slot_blocked_overall && !empty($reservations_for_day)) {
                    usort($reservations_for_day, function ($a, $b) {
                      return strtotime($a['res_hora_inicio']) - strtotime($b['res_hora_inicio']);
                    });
                    $reserva_hora_inicio = date("H:i", strtotime($reservations_for_day[0]['res_hora_inicio']));
                    $reserva_hora_fim = date("H:i", strtotime($reservations_for_day[0]['res_hora_fim']));
                  }
                  ?>
                  <tr>
                    <!-- <td>
                      <div class="col_loc fw-semibold" title="<?= htmlspecialchars($space_info['esp_nome_local']) ?>">
                        <?= htmlspecialchars($space_info['esp_nome_local_resumido'] ?: $space_info['esp_nome_local']); ?>
                      </div>
                    </td> -->

                    <td>
                      <div class="col_loc fw-semibold" title="<?= htmlspecialchars($space_info['esp_nome_local']) ?>">
                        <?= htmlspecialchars($space_info['esp_codigo'] . ' - ' . $space_info['esp_nome_local']); ?>
                      </div>
                    </td>

                    <td>
                      <div class="col_loc text-uppercase"><?= $display_date_obj->format('d/m/Y') ?></div>
                    </td>
                    <td>
                      <div class="col_loc text-uppercase"><?= $display_day_of_week ?></div>
                    </td>
                    <td>
                      <div class="col_loc text-uppercase"><?= $display_month_name ?></div>
                    </td>
                    <td>
                      <div class="col_loc text-uppercase"><?= $display_year ?></div>
                    </td>
                    <td>
                      <div class="col_ch"><?= htmlspecialchars($reserva_hora_inicio) ?></div>
                    </td>
                    <td>
                      <div class="col_ch"><?= htmlspecialchars($reserva_hora_fim) ?></div>
                    </td>
                    <td>
                      <div class="teste_status px-2 text-center text-uppercase <?= $status_data_class ?>">
                        <?= $status_data_text ?>
                      </div>
                    </td>
                    <?php
                    // Variáveis para armazenar o ID da reserva que será usada para o link
                    $link_res_id = null;
                    // Encontra o ID da primeira reserva (a que começa mais cedo) para usar no link da linha
                    if (!$is_slot_blocked_overall && !empty($reservations_for_day)) {
                      usort($reservations_for_day, function ($a, $b) {
                        return strtotime($a['res_hora_inicio']) - strtotime($b['res_hora_inicio']);
                      });
                      $link_res_id = $reservations_for_day[0]['res_solic_id'];
                    }


                    for ($i = $start_time_stamp; $i < $end_time_stamp; $i += 1800) {
                      $hora_coluna_timestamp = $i;
                      $hora_coluna_formatada = date("H:i", $hora_coluna_timestamp);

                      $unique_reservations_in_slot = [];
                      $unique_reservation_hashes = []; // Renomeado para clareza
                
                      if (!$is_slot_blocked_overall) {
                        // Popula $unique_reservations_in_slot para CADA slot de 30 minutos
                        foreach ($reservations_for_day as $reserva_individual) {
                          $res_inicio_ts = strtotime($reserva_individual['res_hora_inicio']);
                          $res_fim_ts = strtotime($reserva_individual['res_hora_fim']);

                          // Verifica se o slot atual se sobrepõe a esta reserva
                          // (start_A < end_B) AND (end_A > start_B)
                          if (($hora_coluna_timestamp < $res_fim_ts) && (($hora_coluna_timestamp + 1800) > $res_inicio_ts)) {
                            // Agora usa o res_id (hash único da reserva individual) para verificar unicidade
                            if (!in_array($reserva_individual['res_id'], $unique_reservation_hashes)) {
                              $unique_reservations_in_slot[] = $reserva_individual;
                              $unique_reservation_hashes[] = $reserva_individual['res_id'];
                            }
                          }
                        }
                      }

                      $cell_display_text = ''; // Texto a ser exibido na célula
                      $conflito = count($unique_reservations_in_slot) > 1;


                      // NOVO: Coleta todos os horários de início e fim para esta célula de 30 minutos
                      $cell_times_to_display_raw = []; // Armazena todos os horários brutos
                      foreach ($unique_reservations_in_slot as $res) {
                        $res_start_ts = strtotime($res['res_hora_inicio']);
                        $res_fim_ts = strtotime($res['res_hora_fim']);

                        // Se a reserva começa EXATAMENTE neste slot
                        if ($res_start_ts == $hora_coluna_timestamp) {
                          $cell_times_to_display_raw[] = date("H:i", $res_start_ts);
                        }
                        // Se a reserva termina EXATAMENTE no fim deste slot
                        // ou se termina DENTRO deste slot, e este é o slot final da reserva
                        if ($res_fim_ts == ($hora_coluna_timestamp + 1800) || ($res_fim_ts > $hora_coluna_timestamp && $res_fim_ts < ($hora_coluna_timestamp + 1800))) {
                          // Evita adicionar o mesmo horário se já adicionou como início
                          if (!in_array(date("H:i", $res_fim_ts), $cell_times_to_display_raw) || $res_start_ts != $res_fim_ts) {
                            $cell_times_to_display_raw[] = date("H:i", $res_fim_ts);
                          }
                        }
                      }
                      // Garante horários únicos e ordenados
                      $cell_times_to_display_raw = array_unique($cell_times_to_display_raw);
                      sort($cell_times_to_display_raw);
                      $cell_display_text = implode(' ', $cell_times_to_display_raw);


                      if ($is_slot_blocked_overall) {
                        ?>
                        <td class="barra blocked" title="<?= htmlspecialchars($status_data_text) ?>"></td>
                      <?php } elseif (count($unique_reservations_in_slot) > 0) {
                        // O tooltip agora é construído com base em todas as reservas no slot
                        $full_tooltip_text = '';
                        foreach ($unique_reservations_in_slot as $idx => $res) {
                          $tipo_reserva_label = ($res['res_tipo_reserva'] == 1) ? 'Diária' : 'Semanal';
                          $data_info = ($res['res_tipo_reserva'] == 2 && $res['res_data']) ? " (a partir de " . date("d/m/Y", strtotime($res['res_data'])) . ")" : "";
                          $full_tooltip_text .= "- ID: " . $res['res_solic_id'] . " [{$tipo_reserva_label}{$data_info}] (" . date("H:i", strtotime($res['res_hora_inicio'])) . " - " . date("H:i", strtotime($res['res_hora_fim'])) . ")\n";
                        }
                        $full_tooltip_text = trim($full_tooltip_text);


                        $classe = 'barra' . ($conflito ? ' conflito' : '');

                        // As bordas (border_l, border_r) serão aplicadas se o slot for o início/fim de alguma reserva presente nele
                        $border_classes = '';

                        // Lógica para aplicar bordas individuais a cada segmento de reserva
                        foreach ($unique_reservations_in_slot as $res_segment) {
                          $res_inicio_ts_segment = strtotime($res_segment['res_hora_inicio']);
                          $res_fim_ts_segment = strtotime($res_segment['res_hora_fim']);

                          // Arredonda para o slot de 30min da reserva individual
                          $hora_inicio_arred_segment = floor($res_inicio_ts_segment / 1800) * 1800;
                          $hora_fim_arred_segment = ceil($res_fim_ts_segment / 1800) * 1800;

                          // Aplica border_l se o slot atual é o início de uma reserva
                          if ($hora_coluna_timestamp == $hora_inicio_arred_segment) {
                            $border_classes .= ' border_l';
                            break; // Adicionar 'break' para garantir apenas uma borda de início por slot
                          }
                          // Aplica border_r se o slot atual é o que antecede o fim de uma reserva
                          if ($hora_coluna_timestamp == ($hora_fim_arred_segment - 1800)) {
                            $border_classes .= ' border_r';
                            break; // Adicionar 'break' para garantir apenas uma borda de fim por slot
                          }
                        }
                        $border_classes = trim($border_classes);
                        ?>
                        <td role="button"
                          onclick="location.href='solicitacao_analise.php?i=<?= htmlspecialchars($link_res_id) ?>'"
                          class="<?= $classe ?> <?= $border_classes ?>" title="<?= htmlspecialchars($full_tooltip_text) ?>"
                          data-debug-count="<?= count($unique_reservations_in_slot) ?>"
                          data-debug-timeslot="<?= $hora_coluna_formatada ?>">
                          <?= htmlspecialchars($cell_display_text) ?>
                        </td>
                      <?php } else { ?>
                        <td></td>
                      <?php } ?>
                    <?php } ?>
                  </tr>
                  <?php
                }
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function debounce(func, delay) {
    let timeout;
    return function (...args) {
      const context = this;
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(context, args), delay);
    };
  }

  let currentSweetAlertInstance = null;

  // Função para formatar uma data para d/m/Y
  function formatarDataParaExibicao(dataString) {
    if (!dataString) return '';
    const [ano, mes, dia] = dataString.split('-');
    return `${dia}/${mes}/${ano}`;
  }

  // Funções auxiliares para obter valores
  function getDataInicioValue() {
    return document.getElementById('data_inicio').value;
  }

  function getDataFimValue() {
    return document.getElementById('data_fim').value;
  }

  function getHoraInicioValue() {
    return document.getElementById('hora_inicio').value;
  }

  function getHoraFimValue() {
    return document.getElementById('hora_fim').value;
  }

  // Função para exibir SweetAlert, com controle de exibição e fechamento de anterior
  function showSweetAlert(options) {
    if (currentSweetAlertInstance && Swal.isVisible()) {
      Swal.close();
    }
    currentSweetAlertInstance = Swal.fire(options).then(() => {
      currentSweetAlertInstance = null;
    });
  }


  // Funções de validação ORIGINAIS (NÃO DEBOUNCED)
  function validateDates() {
    const dataInicioVal = getDataInicioValue();
    const dataFimVal = getDataFimValue();

    if (!dataInicioVal || !dataFimVal) return true;

    const dataInicioObj = new Date(dataInicioVal + 'T00:00:00'); // Adiciona T00:00:00 para evitar problemas de fuso horário
    const dataFimObj = new Date(dataFimVal + 'T00:00:00');

    if (dataFimObj.getFullYear() < dataInicioObj.getFullYear()) {
      showSweetAlert({
        icon: 'error',
        title: 'Data Inválida',
        text: `A Data Fim (${formatarDataParaExibicao(dataFimVal)}) não pode ser de um ano anterior à Data Início (${formatarDataParaExibicao(dataInicioVal)}).`,
        confirmButtonText: 'OK'
      });
      return false;
    } else if (dataFimObj.getFullYear() === dataInicioObj.getFullYear() && dataFimObj < dataInicioObj) {
      showSweetAlert({
        icon: 'error',
        title: 'Data Inválida',
        text: `A data fim não pode ser anterior à data início.`,
        confirmButtonText: 'OK'
      });
      return false;
    }
    return true;
  }

  function validateTime() {
    const horaInicioVal = getHoraInicioValue();
    const horaFimVal = getHoraFimValue();

    if (!horaInicioVal || !horaFimVal) return true;

    const inicioParts = horaInicioVal.split(':').map(Number);
    const fimParts = horaFimVal.split(':').map(Number);

    const inicioMinutos = inicioParts[0] * 60 + inicioParts[1];
    const fimMinutos = fimParts[0] * 60 + fimParts[1];

    if (fimMinutos <= inicioMinutos) {
      showSweetAlert({
        icon: 'error',
        title: 'Horário Inválida',
        text: `O horário fim não pode ser igual ou anterior ao horário início. Por favor, ajuste os horários.`,
        confirmButtonText: 'OK'
      });
      return false;
    }
    return true;
  }

  const validateDatesDebounced = debounce(validateDates, 300);
  const validateTimeDebounced = debounce(validateTime, 300);


  const dataInicioPicker = flatpickr("#data_inicio", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d/m/Y",
    locale: "pt",
    onChange: function (selectedDates, dateStr, instance) {
      validateDatesDebounced();
      validateTimeDebounced();
    }
  });

  const dataFimPicker = flatpickr("#data_fim", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d/m/Y",
    locale: "pt",
    onChange: function (selectedDates, dateStr, instance) {
      validateDatesDebounced();
      validateTimeDebounced();
    }
  });

  const horaInicioPicker = flatpickr("#hora_inicio", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    allowInput: true,
    onChange: function (selectedDates, dateStr, instance) {
      validateTimeDebounced();
    }
  });

  const horaFimPicker = flatpickr(".chf_hora[name='hora_fim']", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: true,
    allowInput: true,
    onChange: function (selectedDates, dateStr, instance) {
      validateTimeDebounced();
    }
  });

  document.querySelector('form').addEventListener('submit', function (event) {

    const isDateValid = validateDates();
    const isTimeValid = validateTime();

    if (!isDateValid || !isTimeValid || Swal.isVisible()) {
      event.preventDefault();
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    validateDates();
    validateTime();
  });


  document.querySelector('form').addEventListener('submit', function (event) {
    let shouldPreventSubmit = false;

    const isDateValid = validateDates.apply(this);
    const isTimeValid = validateTime.apply(this);

    if (!isDateValid) {
      shouldPreventSubmit = true;
    }

    if (!isTimeValid) {
      shouldPreventSubmit = true;
    }

    if (Swal.isVisible()) {
      shouldPreventSubmit = true;
    }

    if (shouldPreventSubmit) {
      event.preventDefault();
    } else {
      // Se tudo estiver válido, o formulário será submetido normalmente
    }
  });
</script>


<script>
  // Chaves de Local Storage específicas para o filtro completo
  const LS_DATA_INICIO = 'disponibilidade_espaco_data_inicio';
  const LS_DATA_FIM = 'disponibilidade_espaco_data_fim';
  const LS_HORA_INICIO = 'disponibilidade_espaco_hora_inicio';
  const LS_HORA_FIM = 'disponibilidade_espaco_hora_fim';
  const LS_ESPACO_ID = 'disponibilidade_espaco_espaco_id';

  const form = document.querySelector('form');

  // Função para salvar todos os 5 filtros
  function salvarFiltroCompleto() {
    localStorage.setItem(LS_DATA_INICIO, document.getElementById('data_inicio').value);
    localStorage.setItem(LS_DATA_FIM, document.getElementById('data_fim').value);
    localStorage.setItem(LS_HORA_INICIO, document.getElementById('hora_inicio').value);
    localStorage.setItem(LS_HORA_FIM, document.getElementById('hora_fim').value);
    localStorage.setItem(LS_ESPACO_ID, document.getElementById('espaco_id').value);
  }

  // Função para limpar todos os 5 filtros e recarregar
  function limparFiltroCompleto() {
    localStorage.removeItem(LS_DATA_INICIO);
    localStorage.removeItem(LS_DATA_FIM);
    localStorage.removeItem(LS_HORA_INICIO);
    localStorage.removeItem(LS_HORA_FIM);
    localStorage.removeItem(LS_ESPACO_ID);

    // Redireciona para a página base, limpando os parâmetros GET
    window.location.href = 'disponibilidade_espaco.php';
  }

  // Função que carrega os valores salvos e submete o formulário
  function carregarFiltroCompleto() {
    // Só carrega se a URL não tiver parâmetros GET (ou seja, sem filtro aplicado)
    if (window.location.search === "" || window.location.search === "?") {
      let shouldSubmit = false;

      // Carrega os valores do Local Storage
      const savedDataInicio = localStorage.getItem(LS_DATA_INICIO);
      const savedDataFim = localStorage.getItem(LS_DATA_FIM);
      const savedHoraInicio = localStorage.getItem(LS_HORA_INICIO);
      const savedHoraFim = localStorage.getItem(LS_HORA_FIM);
      const savedEspacoId = localStorage.getItem(LS_ESPACO_ID);

      // Aplica os valores salvos aos inputs, se existirem
      if (savedDataInicio && document.getElementById('data_inicio')) {
        document.getElementById('data_inicio').value = savedDataInicio;
        shouldSubmit = true;
      }
      if (savedDataFim && document.getElementById('data_fim')) {
        document.getElementById('data_fim').value = savedDataFim;
        shouldSubmit = true;
      }
      if (savedHoraInicio && document.getElementById('hora_inicio')) {
        document.getElementById('hora_inicio').value = savedHoraInicio;
        shouldSubmit = true;
      }
      if (savedHoraFim && document.getElementById('hora_fim')) {
        document.getElementById('hora_fim').value = savedHoraFim;
        // O flatpickr para hora_fim está como '.chf_hora[name="hora_fim"]' no seu JS original.
        // Se a inicialização do flatpickr ocorreu antes, isso pode não funcionar perfeitamente.
        // Submeter o formulário (abaixo) é o que resolve a aplicação do filtro.
        shouldSubmit = true;
      }
      if (savedEspacoId && document.getElementById('espaco_id')) {
        document.getElementById('espaco_id').value = savedEspacoId;
        shouldSubmit = true;
      }

      // Se pelo menos um filtro foi carregado, submete o formulário para aplicar o filtro
      if (shouldSubmit) {
        // Ignora a validação de data/hora (que pode disparar SweetAlerts) no carregamento automático
        // e submete o formulário via JavaScript.
        form.submit();
      }
    }
  }

  // **ANEXAR LISTENERS NO CARREGAMENTO DA PÁGINA**
  document.addEventListener('DOMContentLoaded', function () {
    // 1. Tenta carregar e aplicar o filtro salvo (se a URL estiver limpa)
    carregarFiltroCompleto();

    // 2. Garante que os filtros sejam salvos quando o usuário clica em "Filtrar"
    // O seu formulário já chama a validação no submit, então salvamos no submit.
    form.addEventListener('submit', salvarFiltroCompleto);
  });
</script>


<?php include 'includes/footer.php'; ?>
<script src="includes/select/select2.js"></script>