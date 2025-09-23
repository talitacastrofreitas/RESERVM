<?php
// Inclua a conexão com o banco de dados no topo do script.
include '../conexao/conexao.php';

// FUNÇÕES DE CÁLCULO DE HORAS
function calcularDiferencaHoras($inicio, $fim)
{
  if (!$inicio || !$fim)
    return null;
  $inicio = DateTime::createFromFormat('H:i:s.u', substr($inicio, 0, 15));
  $fim = DateTime::createFromFormat('H:i:s.u', substr($fim, 0, 15));
  if ($inicio && $fim && $fim > $inicio) {
    $diff = $fim->diff($inicio);
    return $diff->format('%H:%I');
  }
  return '00:00';
}

function paraMinutos($hora)
{
  list($h, $m) = explode(':', $hora);
  return ($h * 60) + $m;
}

function paraHoraMin($minutos)
{
  $h = floor($minutos / 60);
  $m = $minutos % 60;
  return sprintf('%02d:%02d', $h, $m);
}

// LÓGICA DE FILTRO
$data_inicio = $_GET['data_inicio'] ?? '';
$dia_semana = $_GET['dia_semana'] ?? '';
$tipo_aula = $_GET['tipo_aula'] ?? '';
$curso = $_GET['curso'] ?? '';
$semestre = $_GET['semestre'] ?? '';
$componente = $_GET['componente'] ?? '';
$modulo = $_GET['modulo'] ?? '';
$professor = $_GET['professor'] ?? '';
$titulo_aula = $_GET['titulo_aula'] ?? '';
$tipo_reserva = $_GET['tipo_reserva'] ?? '';
$local = $_GET['local'] ?? '';
$andar = $_GET['andar'] ?? '';
$pavilhao = $_GET['pavilhao'] ?? '';
$tipo_sala = $_GET['tipo_sala'] ?? '';
$confirmado_por = $_GET['confirmado_por'] ?? '';

// CONSTRUÇÃO DINÂMICA DA CLÁUSULA WHERE
$sql_where = " WHERE 1=1";
$params = [];
if (!empty($data_inicio)) {
  $sql_where .= " AND reservas.res_data >= ?";
  $params[] = $data_inicio;
}
if (!empty($dia_semana)) {
  $sql_where .= " AND reservas.res_dia_semana = ?";
  $params[] = $dia_semana;
}
if (!empty($tipo_aula)) {
  $sql_where .= " AND reservas.res_tipo_aula = ?";
  $params[] = $tipo_aula;
}
if (!empty($curso)) {
  $sql_where .= " AND reservas.res_curso = ?";
  $params[] = $curso;
}
if (!empty($semestre)) {
  $sql_where .= " AND reservas.res_semestre = ?";
  $params[] = $semestre;
}
if (!empty($componente)) {
  $sql_where .= " AND (reservas.res_componente_atividade_nome LIKE ? OR componente_curricular.compc_componente LIKE ?)";
  $params[] = "%" . $componente . "%";
  $params[] = "%" . $componente . "%";
}
if (!empty($modulo)) {
  $sql_where .= " AND reservas.res_modulo LIKE ?";
  $params[] = "%" . $modulo . "%";
}
if (!empty($professor)) {
  $sql_where .= " AND reservas.res_professor LIKE ?";
  $params[] = "%" . $professor . "%";
}
if (!empty($titulo_aula)) {
  $sql_where .= " AND reservas.res_titulo_aula LIKE ?";
  $params[] = "%" . $titulo_aula . "%";
}
if (!empty($tipo_reserva)) {
  $sql_where .= " AND reservas.res_tipo_reserva = ?";
  $params[] = $tipo_reserva;
}
if (!empty($local)) {
  $sql_where .= " AND reservas.res_espaco_id = ?";
  $params[] = $local;
}
if (!empty($andar)) {
  $sql_where .= " AND espaco.esp_andar = ?";
  $params[] = $andar;
}
if (!empty($pavilhao)) {
  $sql_where .= " AND espaco.esp_pavilhao = ?";
  $params[] = $pavilhao;
}
if (!empty($tipo_sala)) {
  $sql_where .= " AND espaco.esp_tipo_espaco = ?";
  $params[] = $tipo_sala;
}
if (!empty($confirmado_por)) {
  $sql_where .= " AND reservas.res_user_id = ?";
  $params[] = $confirmado_por;
}

// VERIFICAÇÃO E PROCESSAMENTO DA EXPORTAÇÃO DO CSV
if (isset($_GET['export_csv']) && $_GET['export_csv'] == 1) {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="reservas_confirmadas_filtradas.csv"');
  $output = fopen('php://output', 'w');

  // Adiciona o BOM (Byte Order Mark) para UTF-8. Isso ajuda o Excel a reconhecer a codificação.
  fwrite($output, "\xEF\xBB\xBF");

  $headers = array("Data", "Dia", "Mês", "Ano", "Início", "Fim", "Turno", "ID da Reserva", "Tipo de Aula", "Curso", "Semestre", "Componente Curricular/Atividade", "Módulo", "Professor", "Título Aula", "Recursos", "Recursos Audiovisuais Add", "Obs", "Nº Pessoas", "Tipo Reserva", "ID Local", "Local Reservado", "Andar", "Pavilhão", "Campus", "Tipo de Sala", "Capacidade", "Confirmado por", "Data Solicitação", "Data Reserva", "ID Solicitação", "CH Programada", "ID Ocorrência", "Início Realizado", "Fim Realizado", "CH Realizada", "CH Faltante", "CH Mais", "Conflito");
  fputcsv($output, $headers, ';');

  $sql = "SELECT solic_id, res_id, res_espaco_id, res_data, week_dias, res_mes, res_ano, res_hora_inicio, res_hora_fim, res_turno, res_tipo_aula, res_tipo_reserva, res_recursos, res_recursos_add, res_codigo, cta_tipo_aula, curs_curso, cs_semestre, res_componente_atividade, compc_componente, res_componente_atividade_nome, res_nome_atividade, res_modulo, res_professor, res_titulo_aula, res_obs, res_quant_pessoas, ctr_tipo_reserva, esp_codigo, esp_nome_local, and_andar, pav_pavilhao, uni_unidade, tipesp_tipo_espaco, esp_quant_maxima, admin_nome, solic_data_cad, res_data_cad, solic_codigo, oco_codigo, oco_hora_inicio_realizado, oco_hora_fim_realizado
                                 FROM reservas
                                 INNER JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
                                 INNER JOIN conf_dias_semana ON conf_dias_semana.week_id = reservas.res_dia_semana
                                 LEFT JOIN cursos ON cursos.curs_id = reservas.res_curso
                                 LEFT JOIN conf_semestre ON conf_semestre.cs_id = reservas.res_semestre
                                 LEFT JOIN componente_curricular ON componente_curricular.compc_id = reservas.res_componente_atividade
                                 INNER JOIN conf_tipo_reserva ON conf_tipo_reserva.ctr_id = reservas.res_tipo_reserva
                                 INNER JOIN conf_tipo_aula ON conf_tipo_aula.cta_id = reservas.res_tipo_aula
                                 INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                                 INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                 LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao LEFT JOIN andares ON andares.and_id = espaco.esp_andar LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade LEFT JOIN recursos ON ',' + ISNULL(reservas.res_recursos_add, '') + ',' LIKE '%,' + CAST(recursos.rec_id AS VARCHAR) + ',%' INNER JOIN admin ON admin.admin_id = reservas.res_user_id LEFT JOIN ocorrencias ON ocorrencias.oco_res_id = reservas.res_id" . $sql_where;

  $stmt = $conn->prepare($sql);
  $stmt->execute($params);

  $reservas_analisadas = [];
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $res_recursos_ids = trim($row['res_recursos_add'] ?? '');
    $res_recursos_ids = rtrim($res_recursos_ids, ',');
    $recursos_formatados = '';
    if (!empty($res_recursos_ids)) {
      $ids_array = array_filter(array_map('trim', explode(',', $res_recursos_ids)), 'ctype_digit');
      if (count($ids_array) > 0) {
        $res_recursos_ids_sql = implode(',', $ids_array);
        $sql_recursos = "SELECT rec_recurso FROM recursos WHERE rec_id IN ($res_recursos_ids_sql)";
        $stmt_rec = $conn->prepare($sql_recursos);
        $stmt_rec->execute();
        $recursos = $stmt_rec->fetchAll(PDO::FETCH_COLUMN);
        $recursos_formatados = implode(' / ', $recursos);
      }
    }
    $ch_programada = calcularDiferencaHoras($row['res_hora_inicio'], $row['res_hora_fim']);
    $inicio_real = $row['oco_hora_inicio_realizado'] ?: $row['res_hora_inicio'];
    $fim_real = $row['oco_hora_fim_realizado'] ?: $row['res_hora_fim'];
    $realizada = calcularDiferencaHoras($inicio_real, $fim_real);
    $prog_min = paraMinutos($ch_programada);
    $real_min = paraMinutos($realizada);
    $faltante = $real_min < $prog_min ? paraHoraMin($prog_min - $real_min) : '00:00';
    $a_mais = $real_min > $prog_min ? paraHoraMin($real_min - $prog_min) : '00:00';
    $componente = (!empty($row['res_componente_atividade'])) ? $row['compc_componente'] : ((!empty($row['res_componente_atividade_nome'])) ? $row['res_componente_atividade_nome'] : ((!empty($row['res_nome_atividade'])) ? $row['res_nome_atividade'] : ''));

    $conflito = '';
    $hora_inicio = new DateTime($row['res_hora_inicio']);
    $hora_fim = new DateTime($row['res_hora_fim']);
    foreach ($reservas_analisadas as $r) {
      if ($r['data'] === $row['res_data'] && $r['espaco_id'] === $row['res_espaco_id'] && ($hora_inicio < $r['fim'] && $hora_fim > $r['inicio'])) {
        $conflito = 'CONFLITO';
        break;
      }
    }
    $reservas_analisadas[] = ['data' => $row['res_data'], 'espaco_id' => $row['res_espaco_id'], 'inicio' => $hora_inicio, 'fim' => $hora_fim];

    $data_csv = array(date('d/m/Y', strtotime($row['res_data'])), $row['week_dias'], $row['res_mes'], $row['res_ano'], date("H:i", strtotime($row['res_hora_inicio'])), date("H:i", strtotime($row['res_hora_fim'])), $row['res_turno'], $row['res_codigo'], $row['cta_tipo_aula'], $row['curs_curso'], $row['cs_semestre'], $componente, $row['res_modulo'], $row['res_professor'], $row['res_titulo_aula'], $row['res_recursos'], $recursos_formatados, $row['res_obs'], $row['res_quant_pessoas'], $row['ctr_tipo_reserva'], $row['esp_codigo'], $row['esp_nome_local'], $row['and_andar'], $row['pav_pavilhao'], $row['uni_unidade'], $row['tipesp_tipo_espaco'], $row['esp_quant_maxima'], $row['admin_nome'], date('d/m/Y', strtotime($row['solic_data_cad'])), date('d/m/Y', strtotime($row['res_data_cad'])), $row['solic_codigo'], $ch_programada, $row['oco_codigo'], date("H:i", strtotime($inicio_real)), date("H:i", strtotime($fim_real)), $realizada, $faltante, $a_mais, $conflito);

    fputcsv($output, $data_csv, ';');
  }
  fclose($output);
  exit; // FINALIZA O SCRIPT AQUI
}

// Se não for uma requisição CSV, o script continua aqui.
include 'includes/header.php'; // Inclua o header apenas para a exibição da página.
?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Reservas Confirmadas</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
          <li class="breadcrumb-item active">Reservas Confirmadas</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Filtros do Relatório</h5>
      </div>
      <div class="card-body">
        <form id="formFiltros" method="get" action="reservas_confirmadas.php">
          <div class="row g-3">
            <div class="col-md-3">
              <label for="data_inicio" class="form-label">Data Início</label>
              <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                value="<?= htmlspecialchars($_GET['data_inicio'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label for="dia_semana" class="form-label">Dia da Semana</label>
              <select class="form-select" id="dia_semana" name="dia_semana">
                <option value="">Todos</option>
                <?php
                $stmt_dias = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                $stmt_dias->execute();
                while ($dia = $stmt_dias->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['dia_semana']) && $_GET['dia_semana'] == $dia['week_id']) ? 'selected' : '';
                  echo "<option value='{$dia['week_id']}' {$selected}>" . htmlspecialchars($dia['week_dias']) . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="tipo_aula" class="form-label">Tipo de Aula</label>
              <select class="form-select" id="tipo_aula" name="tipo_aula">
                <option value="">Todos</option>
                <?php
                $stmt_tipos = $conn->prepare("SELECT cta_id, cta_tipo_aula FROM conf_tipo_aula");
                $stmt_tipos->execute();
                while ($tipo = $stmt_tipos->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['tipo_aula']) && $_GET['tipo_aula'] == $tipo['cta_id']) ? 'selected' : '';
                  echo "<option value='{$tipo['cta_id']}' {$selected}>" . htmlspecialchars($tipo['cta_tipo_aula']) . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-3">
              <label for="curso" class="form-label">Curso</label>
              <select class="form-select" id="curso" name="curso">
                <option value="">Todos</option>
                <?php
                $stmt_cursos = $conn->prepare("SELECT curs_id, curs_curso FROM cursos");
                $stmt_cursos->execute();
                while ($curso = $stmt_cursos->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['curso']) && $_GET['curso'] == $curso['curs_id']) ? 'selected' : '';
                  echo "<option value='{$curso['curs_id']}' {$selected}>" . htmlspecialchars($curso['curs_curso']) . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="semestre" class="form-label">Semestre</label>
              <select class="form-select" id="semestre" name="semestre">
                <option value="">Todos</option>
                <?php
                $stmt_semestres = $conn->prepare("SELECT cs_id, cs_semestre FROM conf_semestre");
                $stmt_semestres->execute();
                while ($semestre = $stmt_semestres->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['semestre']) && $_GET['semestre'] == $semestre['cs_id']) ? 'selected' : '';
                  echo "<option value='{$semestre['cs_id']}' {$selected}>" . htmlspecialchars($semestre['cs_semestre']) . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="componente" class="form-label">Componente Curricular</label>
              <input type="text" class="form-control" id="componente" name="componente"
                value="<?= htmlspecialchars($_GET['componente'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label for="modulo" class="form-label">Módulo</label>
              <input type="text" class="form-control" id="modulo" name="modulo"
                value="<?= htmlspecialchars($_GET['modulo'] ?? '') ?>">
            </div>

            <div class="col-md-3">
              <label for="professor" class="form-label">Professor</label>
              <input type="text" class="form-control" id="professor" name="professor"
                value="<?= htmlspecialchars($_GET['professor'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label for="titulo_aula" class="form-label">Título da Aula</label>
              <input type="text" class="form-control" id="titulo_aula" name="titulo_aula"
                value="<?= htmlspecialchars($_GET['titulo_aula'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label for="tipo_reserva" class="form-label">Tipo de Reserva</label>
              <select class="form-select" id="tipo_reserva" name="tipo_reserva">
                <option value="">Todos</option>
                <?php
                $stmt_tipos_res = $conn->prepare("SELECT ctr_id, ctr_tipo_reserva FROM conf_tipo_reserva");
                $stmt_tipos_res->execute();
                while ($tipo_res = $stmt_tipos_res->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['tipo_reserva']) && $_GET['tipo_reserva'] == $tipo_res['ctr_id']) ? 'selected' : '';
                  echo "<option value='{$tipo_res['ctr_id']}' {$selected}>" . htmlspecialchars($tipo_res['ctr_tipo_reserva']) . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="local" class="form-label">Local Reservado</label>
              <select class="form-select" id="local" name="local">
                <option value="">Todos</option>
                <?php
                $stmt_locais = $conn->prepare("SELECT esp_id, esp_nome_local, esp_codigo FROM espaco ORDER BY esp_nome_local");
                $stmt_locais->execute();
                while ($local = $stmt_locais->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['local']) && $_GET['local'] == $local['esp_id']) ? 'selected' : '';
                  echo "<option value='{$local['esp_id']}' {$selected}>" . htmlspecialchars($local['esp_nome_local']) . " (" . htmlspecialchars($local['esp_codigo']) . ")" . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-3">
              <label for="andar" class="form-label">Andar</label>
              <select class="form-select" id="andar" name="andar">
                <option value="">Todos</option>
                <?php
                $stmt_andares = $conn->prepare("SELECT and_id, and_andar FROM andares ORDER BY and_andar");
                $stmt_andares->execute();
                while ($andar = $stmt_andares->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['andar']) && $_GET['andar'] == $andar['and_id']) ? 'selected' : '';
                  echo "<option value='{$andar['and_id']}' {$selected}>" . htmlspecialchars($andar['and_andar']) . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="pavilhao" class="form-label">Pavilhão</label>
              <select class="form-select" id="pavilhao" name="pavilhao">
                <option value="">Todos</option>
                <?php
                $stmt_pavilhoes = $conn->prepare("SELECT pav_id, pav_pavilhao FROM pavilhoes ORDER BY pav_pavilhao");
                $stmt_pavilhoes->execute();
                while ($pavilhao = $stmt_pavilhoes->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['pavilhao']) && $_GET['pavilhao'] == $pavilhao['pav_id']) ? 'selected' : '';
                  echo "<option value='{$pavilhao['pav_id']}' {$selected}>" . htmlspecialchars($pavilhao['pav_pavilhao']) . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="tipo_sala" class="form-label">Tipo de Sala</label>
              <select class="form-select" id="tipo_sala" name="tipo_sala">
                <option value="">Todos</option>
                <?php
                $stmt_tipos_sala = $conn->prepare("SELECT tipesp_id, tipesp_tipo_espaco FROM tipo_espaco ORDER BY tipesp_tipo_espaco");
                $stmt_tipos_sala->execute();
                while ($tipo_sala = $stmt_tipos_sala->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['tipo_sala']) && $_GET['tipo_sala'] == $tipo_sala['tipesp_id']) ? 'selected' : '';
                  echo "<option value='{$tipo_sala['tipesp_id']}' {$selected}>" . htmlspecialchars($tipo_sala['tipesp_tipo_espaco']) . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="confirmado_por" class="form-label">Confirmado por</label>
              <select class="form-select" id="confirmado_por" name="confirmado_por">
                <option value="">Todos</option>
                <?php
                $stmt_admins = $conn->prepare("SELECT admin_id, admin_nome FROM admin ORDER BY admin_nome");
                $stmt_admins->execute();
                while ($admin = $stmt_admins->fetch(PDO::FETCH_ASSOC)) {
                  $selected = (isset($_GET['confirmado_por']) && $_GET['confirmado_por'] == $admin['admin_id']) ? 'selected' : '';
                  echo "<option value='{$admin['admin_id']}' {$selected}>" . htmlspecialchars($admin['admin_nome']) . "</option>";
                }
                ?>
              </select>
            </div>

            <input type="hidden" name="export_csv" id="export_csv" value="0">


            <!-- <div class="d-flex justify-content-between col-4 mt-3">
              <button type="submit" class="btn botao botao_azul_escuro waves-effect w-100">Filtrar</button>

              <a href="reservas_confirmadas.php" class="btn botao botao_cinza waves-effect w-100 ms-2">Limpar</a>

            </div> -->
            <div class=" col-md-3 " style="margin-top: 2.8rem;">


              <div class="d-flex justify-content-between aligns-items-center">
                <button type="submit" class="btn botao botao_azul_escuro waves-effect w-100">Filtrar</button>
                <!-- <button type="submit" class="btn botao botao_roxo waves-effect" id="btnExportCsv">CSV</button> -->
                <a href="reservas_confirmadas.php" class="btn botao botao_cinza waves-effect w-100 ms-2">Limpar</a>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-sm-8 text-sm-start text-center">
            <h5 class="card-title mb-0">Lista de Reservas Confirmadas</h5>
          </div>
          <div class="col-sm-4 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button type="submit" class="btn botao botao_roxo waves-effect mt-3 mt-sm-0" id="btnExportCsv"
              style="padding: 7.5px 13px !important">CSV</button>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="tab_reserva_confirm" class="table align-middle" style="width:100%">
            <thead>
              <tr>
                <th nowrap="nowrap"><span class="me-3">Data</span></th>
                <th nowrap="nowrap"><span class="me-3">Dia</span></th>
                <th nowrap="nowrap"><span class="me-3">Mês</span></th>
                <th nowrap="nowrap"><span class="me-3">Ano</span></th>
                <th nowrap="nowrap"><span class="me-3">Início</span></th>
                <th nowrap="nowrap"><span class="me-3">Fim</span></th>
                <th nowrap="nowrap"><span class="me-3">Turno</span></th>
                <th nowrap="nowrap"><span class="me-3">ID da Reserva</span></th>
                <th nowrap="nowrap"><span class="me-3">Tipo de Aula</span></th>
                <th nowrap="nowrap"><span class="me-3">Curso</span></th>
                <th nowrap="nowrap"><span class="me-3">Semestre</span></th>
                <th nowrap="nowrap"><span class="me-3">Componente Curricular/Atividade</span></th>
                <th nowrap="nowrap"><span class="me-3">Módulo</span></th>
                <th nowrap="nowrap"><span class="me-3">Professor</span></th>
                <th nowrap="nowrap"><span class="me-3">Título Aula</span></th>
                <th nowrap="nowrap"><span class="me-3">Recursos</span></th>
                <th nowrap="nowrap"><span class="me-3">Recursos Audiovisuais Add</span></th>
                <th nowrap="nowrap"><span class="me-3">Obs</span></th>
                <th nowrap="nowrap"><span class="me-3">Nº Pessoas</span></th>
                <th nowrap="nowrap"><span class="me-3">Tipo Reserva</span></th>
                <th nowrap="nowrap"><span class="me-3">ID Local</span></th>
                <th nowrap="nowrap"><span class="me-3">Local Reservado</span></th>
                <th nowrap="nowrap"><span class="me-3">Andar</span></th>
                <th nowrap="nowrap"><span class="me-3">Pavilhão</span></th>
                <th nowrap="nowrap"><span class="me-3">Campus</span></th>
                <th nowrap="nowrap"><span class="me-3">Tipo de Sala</span></th>
                <th nowrap="nowrap"><span class="me-3">Capacidade</span></th>
                <th nowrap="nowrap"><span class="me-3">Confirmado por</span></th>
                <th nowrap="nowrap"><span class="me-3">Data Solicitação</span></th>
                <th nowrap="nowrap"><span class="me-3">Data Reserva</span></th>
                <th nowrap="nowrap"><span class="me-3">ID Solicitação</span></th>
                <th nowrap="nowrap"><span class="me-3">CH Programada</span></th>
                <th nowrap="nowrap"><span class="me-3">ID Ocorrência</span></th>
                <th nowrap="nowrap"><span class="me-3">Início Realizado</span></th>
                <th nowrap="nowrap"><span class="me-3">Fim Realizado</span></th>
                <th nowrap="nowrap"><span class="me-3">CH Realizada</span></th>
                <th nowrap="nowrap"><span class="me-3">CH Faltante</span></th>
                <th nowrap="nowrap"><span class="me-3">CH Mais</span></th>
                <th nowrap="nowrap"><span class="me-3">Conflito</span></th>
              </tr>
            </thead>
            <tbody>

              <?php
              try {
                $reservas_analisadas = [];
                $reservas = [];

                $sql = "SELECT solic_id, res_id, res_espaco_id, res_data, week_dias, res_mes, res_ano, res_hora_inicio, res_hora_fim, res_turno, res_tipo_aula, res_tipo_reserva, res_recursos, res_recursos_add, res_codigo, cta_tipo_aula, curs_curso, cs_semestre,res_componente_atividade, compc_componente, res_componente_atividade_nome, res_nome_atividade, res_modulo, res_professor, res_titulo_aula, res_obs, res_quant_pessoas, ctr_tipo_reserva, esp_codigo, esp_nome_local, and_andar, pav_pavilhao, uni_unidade, tipesp_tipo_espaco, esp_quant_maxima, admin_nome, solic_data_cad, res_data_cad, solic_codigo, oco_codigo, oco_hora_inicio_realizado,oco_hora_fim_realizado
                                                                 FROM reservas
                                                                 INNER JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
                                                                 INNER JOIN conf_dias_semana ON conf_dias_semana.week_id = reservas.res_dia_semana
                                                                 LEFT JOIN cursos ON cursos.curs_id = reservas.res_curso
                                                                 LEFT JOIN conf_semestre ON conf_semestre.cs_id = reservas.res_semestre
                                                                 LEFT JOIN componente_curricular ON componente_curricular.compc_id = reservas.res_componente_atividade
                                                                 INNER JOIN conf_tipo_reserva ON conf_tipo_reserva.ctr_id = reservas.res_tipo_reserva
                                                                 INNER JOIN conf_tipo_aula ON conf_tipo_aula.cta_id = reservas.res_tipo_aula
                                                                 INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                                                                 INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                                                 LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao LEFT JOIN andares ON andares.and_id = espaco.esp_andar LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade LEFT JOIN recursos ON ',' + ISNULL(reservas.res_recursos_add, '') + ',' LIKE '%,' + CAST(recursos.rec_id AS VARCHAR) + ',%' INNER JOIN admin ON admin.admin_id = reservas.res_user_id LEFT JOIN ocorrencias ON ocorrencias.oco_res_id = reservas.res_id" . $sql_where;

                $stmt = $conn->prepare($sql);
                $stmt->execute($params);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                  $solic_id = $row['solic_id'];
                  $res_id = $row['res_id'];
                  $res_espaco_id = $row['res_espaco_id'];
                  $res_data = $row['res_data'];
                  $week_dias = $row['week_dias'];
                  $res_mes = $row['res_mes'];
                  $res_ano = $row['res_ano'];
                  $res_hora_inicio = $row['res_hora_inicio'];
                  $res_hora_fim = $row['res_hora_fim'];
                  $res_turno = $row['res_turno'];
                  $res_tipo_aula = $row['res_tipo_aula'];
                  $res_tipo_reserva = $row['res_tipo_reserva'];
                  $res_recursos = $row['res_recursos'];
                  $res_recursos_add = $row['res_recursos_add'];
                  $res_codigo = $row['res_codigo'];
                  $cta_tipo_aula = $row['cta_tipo_aula'];
                  $curs_curso = $row['curs_curso'];
                  $cs_semestre = $row['cs_semestre'];
                  $res_componente_atividade = $row['res_componente_atividade'];
                  $compc_componente = $row['compc_componente'];
                  $res_componente_atividade_nome = $row['res_componente_atividade_nome'];
                  $res_nome_atividade = $row['res_nome_atividade'];
                  $res_modulo = $row['res_modulo'];
                  $res_professor = $row['res_professor'];
                  $res_titulo_aula = $row['res_titulo_aula'];
                  $res_obs = $row['res_obs'];
                  $res_quant_pessoas = $row['res_quant_pessoas'];
                  $ctr_tipo_reserva = $row['ctr_tipo_reserva'];
                  $esp_codigo = $row['esp_codigo'];
                  $esp_nome_local = $row['esp_nome_local'];
                  $and_andar = $row['and_andar'];
                  $pav_pavilhao = $row['pav_pavilhao'];
                  $uni_unidade = $row['uni_unidade'];
                  $tipesp_tipo_espaco = $row['tipesp_tipo_espaco'];
                  $esp_quant_maxima = $row['esp_quant_maxima'];
                  $admin_nome = $row['admin_nome'];
                  $solic_data_cad = $row['solic_data_cad'];
                  $res_data_cad = $row['res_data_cad'];
                  $solic_codigo = $row['solic_codigo'];
                  $oco_codigo = $row['oco_codigo'];
                  $oco_hora_inicio_realizado = $row['oco_hora_inicio_realizado'];
                  $oco_hora_fim_realizado = $row['oco_hora_fim_realizado'];

                  ////////////////////////////////////////////
                  // TRATA OS DADOS DOS RECURSOS ADICIONAIS //
                  ////////////////////////////////////////////
                  $res_recursos_ids = trim($res_recursos_add ?? '');
                  $res_recursos_ids = rtrim($res_recursos_ids, ',');
                  if (empty($res_recursos_ids)) {
                    $row['recursos_formatados'] = '';
                  } else {
                    $ids_array = array_filter(array_map('trim', explode(',', $res_recursos_ids)), 'ctype_digit');
                    if (count($ids_array) === 0) {
                      $row['recursos_formatados'] = '';
                    } else {
                      $res_recursos_ids_sql = implode(',', $ids_array);
                      $sql_recursos = "SELECT rec_recurso FROM recursos WHERE rec_id IN ($res_recursos_ids_sql)";
                      $stmt_rec = $conn->prepare($sql_recursos);
                      $stmt_rec->execute();
                      $recursos = $stmt_rec->fetchAll(PDO::FETCH_COLUMN);
                      $row['recursos_formatados'] = implode(' / ', $recursos);
                    }
                  }
                  ///////////////////////
                  // FIM DO TRATAMENTO //
                  ///////////////////////
              
                  $hora_inicio = new DateTime($res_hora_inicio);
                  $hora_fim = new DateTime($res_hora_fim);
                  $conflito = false;
                  foreach ($reservas_analisadas as $r) {
                    if (
                      $r['data'] === $res_data &&
                      $r['espaco_id'] === $res_espaco_id &&
                      $r['esp_codigo'] === $esp_codigo &&
                      (
                        ($hora_inicio < $r['fim'] && $hora_fim > $r['inicio']) ||
                        ($hora_inicio == $r['inicio'] || $hora_fim == $r['fim'])
                      )
                    ) {
                      $conflito = true;
                      break;
                    }
                  }
                  $reservas_analisadas[] = [
                    'data' => $res_data,
                    'espaco_id' => $res_espaco_id,
                    'esp_codigo' => $esp_codigo,
                    'inicio' => $hora_inicio,
                    'fim' => $hora_fim,
                  ];
                  $conflito_class = $conflito ? 'conflict-cell' : '';
                  $recursos_formatados = $row['recursos_formatados'];
                  $tipo_aula_color = $res_tipo_aula == 1 ? 'bg_info_laranja' : 'bg_info_azul';
                  $tipo_reserva_color = $res_tipo_reserva == 1 ? 'bg_info_roxo' : 'bg_info_azul_escuro';
                  $recursos_color = $res_recursos == 'SIM' ? 'bg_info_verde' : 'bg_info_vermelho';
                  $ch_programada = calcularDiferencaHoras($res_hora_inicio, $res_hora_fim);
                  $inicio_real = $oco_hora_inicio_realizado ?: $res_hora_inicio;
                  $fim_real = $oco_hora_fim_realizado ?: $res_hora_fim;
                  $borda_inicio_realizado = $oco_hora_inicio_realizado ? 'borda_dado' : '';
                  $borda_fim_realizado = $oco_hora_fim_realizado ? 'borda_dado' : '';
                  $realizada = calcularDiferencaHoras($inicio_real, $fim_real);
                  $prog_min = paraMinutos($ch_programada);
                  $real_min = paraMinutos($realizada);
                  $faltante = $real_min < $prog_min ? paraHoraMin($prog_min - $real_min) : '00:00';
                  $a_mais = $real_min > $prog_min ? paraHoraMin($real_min - $prog_min) : '00:00';

                  if (!empty($res_componente_atividade)) {
                    $componente = $compc_componente;
                  } else if (!empty($res_componente_atividade_nome)) {
                    $componente = $res_componente_atividade_nome;
                  } else if (!empty($res_nome_atividade)) {
                    $componente = $res_nome_atividade;
                  }
                  ?>

                  <tr role="button" data-href='solicitacao_analise.php?i=<?= htmlspecialchars($solic_id) ?>'>
                    <th scope="row" nowrap="nowrap"
                      class="bg_table_fix_vermelho <?= $conflito_class ? 'bg_table_fix_vermelho_escuro' : '' ?>"><span
                        class="hide_data"><?= date('Ymd', strtotime($res_data)) ?></span><?= htmlspecialchars(date('d/m/Y', strtotime($res_data))) ?>
                    </th>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_laranja <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_laranja_escuro' : '' ?>">
                      <?= htmlspecialchars(substr($week_dias, 0, 3)) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_verde <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_verde_escuro' : '' ?>">
                      <?= htmlspecialchars($res_mes) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_azul <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_azul_escuro' : '' ?>">
                      <?= htmlspecialchars($res_ano) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_roxo <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_roxo_escuro' : '' ?>">
                      <?= htmlspecialchars(date("H:i", strtotime($res_hora_inicio))) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_rosa <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_rosa_escuro' : '' ?>">
                      <?= htmlspecialchars(date("H:i", strtotime($res_hora_fim))) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_cinza <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_cinza_escuro' : '' ?>">
                      <?= htmlspecialchars($res_turno) ?>
                    </td>
                    <th scope="row" nowrap="nowrap"><?= htmlspecialchars($res_codigo) ?></th>
                    <td scope="row" nowrap="nowrap"><span
                        class="badge <?= $tipo_aula_color ?>"><?= htmlspecialchars($cta_tipo_aula) ?></span></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($curs_curso) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($cs_semestre) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($componente) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_modulo) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_professor) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_titulo_aula) ?></td>
                    <td scope="row" nowrap="nowrap"><span
                        class="badge <?= $recursos_color ?>"><?= htmlspecialchars($res_recursos) ?></span></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($recursos_formatados) ?></td>
                    <td scope="row">
                      <?php if ($res_obs) { ?>
                        <button type="button" class="btn btn_soft_azul_escuro btn-sm" data-bs-toggle="modal"
                          data-bs-target="#modal_obs<?= $res_id ?>"><i class="fa-regular fa-comment-dots"></i></button>
                        <div id="modal_obs<?= $res_id ?>" class="modal zoomIn fade" tabindex="-1"
                          aria-labelledby="ModalObsLabel" aria-hidden="true" style="display: none;">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="ModalObsLabel">Observação</h5>
                                <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                              </div>
                              <div class="modal-body">
                                <p class="fs-14 m-0"><?= htmlspecialchars($res_obs) ?></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                    </td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_quant_pessoas) ?></td>
                    <td scope="row" nowrap="nowrap"><span
                        class="badge <?= $tipo_reserva_color ?>"><?= htmlspecialchars($ctr_tipo_reserva) ?></span></td>
                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($esp_codigo) ?></strong></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($esp_nome_local) ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($and_andar) ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($pav_pavilhao) ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($uni_unidade) ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($tipesp_tipo_espaco) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($esp_quant_maxima) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($admin_nome) ?></td>
                    <td scope="row" nowrap="nowrap"><span
                        class="hide_data"><?= date('Ymd', strtotime($solic_data_cad)) ?></span><?= htmlspecialchars(htmlspecialchars(date('d/m/Y', strtotime($solic_data_cad)))) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"><span
                        class="hide_data"><?= date('Ymd', strtotime($res_data_cad)) ?></span><?= htmlspecialchars(htmlspecialchars(date('d/m/Y', strtotime($res_data_cad)))) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($solic_codigo) ?></strong></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($ch_programada) ?></td>
                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($oco_codigo) ?></strong></td>
                    <td><span
                        class="<?= $borda_inicio_realizado ?>"><?= htmlspecialchars(date("H:i", strtotime($inicio_real))) ?></span>
                    </td>
                    <td><span
                        class="<?= $borda_fim_realizado ?>"><?= htmlspecialchars(date("H:i", strtotime($fim_real))) ?></span>
                    </td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($realizada) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($faltante) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($a_mais) ?></td>
                    <td scope="row" nowrap="nowrap"> <span
                        class="badge <?= $conflito_class ? 'bg_info_vermelho' : '' ?>"><?= $conflito_class ? 'CONFLITO' : '' ?></span>
                    </td>
                  </tr>

                <?php }
              } catch (PDOException $e) {
                echo "Erro ao tentar recuperar os dados" . $e->getMessage();
              } ?>

            </tbody>
          </table>
        </div>

        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const btnExportCsv = document.getElementById('btnExportCsv');
            const formFiltros = document.getElementById('formFiltros');
            const exportCsvInput = document.getElementById('export_csv');

            if (btnExportCsv && formFiltros && exportCsvInput) {
              btnExportCsv.addEventListener('click', function (event) {
                event.preventDefault();
                exportCsvInput.value = '1';
                formFiltros.submit();
              });
            }

            // O script para as linhas da tabela pode permanecer
            $('table').on('click', 'tr', function (e) {
              if ($(e.target).closest('.btn').length > 0 || $(e.target).closest('.btn-close').length > 0 || $(e.target).closest('.modal').length > 0) {
                return;
              }
              const href = $(this).data('href');
              if (href) {
                window.location.href = href;
              }
            });
            $(document).on('click', '.btn, .btn-close', function (e) {
              e.stopPropagation();
            });
            $(document).on('click', '.modal, .modal *', function (e) {
              e.stopPropagation();
            });
          });
        </script>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>