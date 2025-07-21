<?php
require_once '../conexao/conexao.php'; // ajuste conforme necessário

// PARA VERIFICAR CONFLITOS ANTERIORES
$reservas_analisadas = [];
$reservas = [];

// CALCULO DAS CARGAS HORÁRIAS
function calcularDiferencaHoras($inicio, $fim)
{
  $inicioMin = paraMinutos(substr($inicio, 0, 5)); // "10:00"
  $fimMin = paraMinutos(substr($fim, 0, 5));       // "11:30"

  if ($fimMin > $inicioMin) {
    return paraHoraMin($fimMin - $inicioMin);
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

// Consulta SQL para obter os dados do banco de dados
$sql = "SELECT
res_espaco_id,
res_data,
week_dias,
UPPER(res_mes) AS res_mes,
res_ano,
res_hora_inicio,
res_hora_fim,
res_turno,
res_codigo,
UPPER(cta_tipo_aula) AS cta_tipo_aula,
curs_curso,
cs_semestre,
res_componente_atividade,
compc_componente,
res_componente_atividade_nome,
res_nome_atividade,
res_modulo,
res_professor,
res_titulo_aula,
--res_tipo_aula,
res_recursos,
res_recursos_add,
res_tipo_reserva,
res_obs,
res_quant_pessoas,
UPPER(ctr_tipo_reserva) AS ctr_tipo_reserva,
esp_codigo,
esp_nome_local,
UPPER(and_andar) AS and_andar, 
UPPER(pav_pavilhao) AS pav_pavilhao, 
UPPER(uni_unidade) AS uni_unidade, 
UPPER(tipesp_tipo_espaco) AS tipesp_tipo_espaco, 
esp_quant_maxima,
admin_nome,
solic_data_cad,
res_data_cad,
solic_codigo,
oco_codigo,
oco_hora_inicio_realizado,oco_hora_fim_realizado 

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
LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
LEFT JOIN andares ON andares.and_id = espaco.esp_andar
LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
LEFT JOIN recursos ON ',' + ISNULL(reservas.res_recursos_add, '') + ',' LIKE '%,' + CAST(recursos.rec_id AS VARCHAR) + ',%'
INNER JOIN admin ON admin.admin_id = reservas.res_user_id
LEFT JOIN ocorrencias ON ocorrencias.oco_res_id = reservas.res_id
ORDER BY res_data DESC";

// Prepara e executa a consulta
$stmt = $conn->prepare($sql);
$stmt->execute();

// Define o nome do arquivo CSV que será baixado
$filename = 'arquivo.csv';
$delimiter = ';'; // delimitador do CSV (pode ser ponto e vírgula, por exemplo)

// Define os cabeçalhos HTTP para download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Cria um ponteiro de arquivo temporário
$output = fopen('php://output', 'w');

// Escreve o BOM (Byte Order Mark) UTF-8 para garantir a correta codificação
fputs($output, "\xEF\xBB\xBF");

// Escreve o cabeçalho do CSV (opcional)
$header = array('CONFLITO', 'DATA', 'DIA', 'MÊS', 'ANO', 'INÍCIO', 'FIM', 'TURNO', 'ID RESERVA', 'TIPO ATIVIDADE', 'CURSO', 'SEMESTRE', 'COMPONENTE CURRICULAR/ ATIVIDADE', 'MÓDULO', 'PROFESSOR', 'TÍTULO AULA', 'RECURSOS AUDIOVISUAIS', 'RECURSOS AUDIOVISUAIS ADICIONAIS', 'OBSERVAÇÃO', 'NÚMERO DE PESSOAS', 'TIPO RESERVA', 'ID DO LOCAL', 'LOCAL RESERVADO', 'ANDAR', 'PAVILHÃO', 'CAMPUS', 'TIPO SALA', 'CAPACIDADE', 'COLABORADOR QUE CONFIRMOU A RESERVA', 'DATA DO RECEBIMENTO DA SOLICITAÇÃO DE RESERVA', 'DATA DO LANÇAMENTO DA RESERVA', 'ID DA SOLICITAÇÃO', 'CARGA HORÁRIA PROGRAMADA', 'ID OCORRÊNCIA', 'INICIO REALIZADO', 'FIM REALIZADO', 'CARGA HORÁRIA REALIZADA', 'CARGA HORÁRIA FALTANTE', 'CARGA HORÁRIA A MAIS');
fputcsv($output, $header, $delimiter);

// Escreve os dados no CSV
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

  ////////////////////////////////////////////
  // TRATA OS DADOS DOS RECURSOS ADICIONAIS //
  ////////////////////////////////////////////

  // Pegue o campo res_recursos_add e limpe para o formato certo
  $res_recursos_ids = trim($row['res_recursos_add'] ?? '');
  $res_recursos_ids = rtrim($res_recursos_ids, ','); // Remove vírgula final, se existir

  if (empty($res_recursos_ids)) {
    // Sem recursos adicionados
    $row['recursos_formatados'] = '';
  } else {
    // Explode e filtra só ids numéricos
    $ids_array = array_filter(array_map('trim', explode(',', $res_recursos_ids)), 'ctype_digit');

    if (count($ids_array) === 0) {
      $row['recursos_formatados'] = '';
    } else {
      $res_recursos_ids_sql = implode(',', $ids_array);

      // Busca nomes dos recursos para esses IDs
      $sql_recursos = "SELECT rec_recurso FROM recursos WHERE rec_id IN ($res_recursos_ids_sql)";
      $stmt_rec = $conn->prepare($sql_recursos);
      $stmt_rec->execute();
      $recursos = $stmt_rec->fetchAll(PDO::FETCH_COLUMN);

      // Monta string separada por ' / '
      $row['recursos_formatados'] = implode(' / ', $recursos);
    }
  }

  ///////////////////////
  // FIM DO TRATAMENTO //
  ///////////////////////

  // Converte res_hora_inicio para o formato 00:00
  if (!empty($row['res_hora_inicio'])) {
    $hora_inicio = DateTime::createFromFormat('H:i:s.u', substr($row['res_hora_inicio'], 0, 15));
    $row['res_hora_inicio'] = $hora_inicio ? $hora_inicio->format('H:i') : $row['res_hora_inicio'];
  }

  // Converte res_hora_fim para o mesmo padrão (opcional)
  if (!empty($row['res_hora_fim'])) {
    $hora_fim = DateTime::createFromFormat('H:i:s.u', substr($row['res_hora_fim'], 0, 15));
    $row['res_hora_fim'] = $hora_fim ? $hora_fim->format('H:i') : $row['res_hora_fim'];
  }


  $hora_inicio = new DateTime($row['res_hora_inicio']);
  $hora_fim = new DateTime($row['res_hora_fim']);

  // Verificação de conflito
  $conflito = false;
  foreach ($reservas_analisadas as $r) {
    if (
      $r['data'] === $row['res_data'] &&
      $r['espaco_id'] === $row['res_espaco_id'] &&
      $r['esp_codigo'] === $row['esp_codigo'] &&
      (
        ($hora_inicio < $r['fim'] && $hora_fim > $r['inicio']) ||  // sobreposição geral
        ($hora_inicio == $r['inicio'] || $hora_fim == $r['fim'])   // mesmo horário exato
      )
    ) {
      $conflito = true;
      break;
    }
  }

  // Armazena a reserva para futuras comparações
  $reservas_analisadas[] = [
    'data' => $row['res_data'],
    'espaco_id' => $row['res_espaco_id'],
    'esp_codigo' => $row['esp_codigo'],
    'inicio' => $hora_inicio,
    'fim' => $hora_fim,
  ];


  // extract($row);


  // CARGA HORÁRIA PROGRAMADA
  $ch_programada = calcularDiferencaHoras($row['res_hora_inicio'], $row['res_hora_fim']);

  // INÍCIO e FIM REALIZADO (preferencialmente os da tabela ocorrencias)
  $inicio_real = $row['oco_hora_inicio_realizado'] ?: $row['res_hora_inicio'];
  $fim_real = $row['oco_hora_fim_realizado'] ?: $row['res_hora_fim'];
  //
  $borda_inicio_realizado = $row['oco_hora_inicio_realizado'] ? 'borda_dado' : '';
  $borda_fim_realizado = $row['oco_hora_fim_realizado'] ? 'borda_dado' : '';

  // CARGA HORÁRIA REALIZADA
  $realizada = calcularDiferencaHoras($inicio_real, $fim_real);

  // Convertendo para minutos
  $prog_min = paraMinutos($ch_programada);
  $real_min = paraMinutos($realizada);

  // CARGA HORÁRIA FALTANTE
  $faltante = $real_min < $prog_min ? paraHoraMin($prog_min - $real_min) : '00:00';

  // CARGA HORÁRIA A MAIS
  $a_mais = $real_min > $prog_min ? paraHoraMin($real_min - $prog_min) : '00:00';

  //

  if (!empty($row['res_componente_atividade'])) {
    $componente = $row['compc_componente'];
  } else if (!empty($row['res_componente_atividade_nome'])) {
    $componente = $row['res_componente_atividade_nome'];
  } else if (!empty($row['res_nome_atividade'])) {
    $componente = $row['res_nome_atividade'];
  }

  // Tratar quebra de linha na observação e texto uppercase
  $row['res_obs'] = mb_strtoupper(str_replace(["\r\n", "\r"], "\n", $row['res_obs'] ?? ''), 'UTF-8');

  $conflito_name = $conflito ? 'CONFLITO' : '';

  fputcsv($output, [
    $conflito_name,
    $row['res_data'] ?? '',
    $row['week_dias'] ?? '',
    $row['res_mes'] ?? '',
    $row['res_ano'] ?? '',
    $row['res_hora_inicio'] ?? '',
    $row['res_hora_fim'] ?? '',
    $row['res_turno'] ?? '',
    $row['res_codigo'] ?? '',
    $row['cta_tipo_aula'] ?? '',
    $row['curs_curso'] ?? '',
    $row['cs_semestre'] ?? '',
    $componente,
    $row['res_modulo'] ?? '',
    $row['res_professor'] ?? '',
    $row['res_titulo_aula'] ?? '',
    $row['res_recursos'] ?? '',
    $row['recursos_formatados'] ?? '',
    $row['res_obs'],
    $row['res_quant_pessoas'] ?? '',
    $row['ctr_tipo_reserva'] ?? '',
    $row['esp_codigo'] ?? '',
    $row['esp_nome_local'] ?? '',
    $row['and_andar'] ?? '',
    $row['pav_pavilhao'] ?? '',
    $row['uni_unidade'] ?? '',
    $row['tipesp_tipo_espaco'] ?? '',
    $row['esp_quant_maxima'] ?? '',
    $row['admin_nome'] ?? '',
    date('d/m/Y', strtotime($row['solic_data_cad'])) ?? '',
    date('d/m/Y', strtotime($row['res_data_cad'])) ?? '',
    $row['solic_codigo'] ?? '',
    $ch_programada,
    $row['oco_codigo'] ?? '',
    date("H:i", strtotime($inicio_real)),
    date("H:i", strtotime($fim_real)),
    date("H:i", strtotime($realizada)),
    date("H:i", strtotime($faltante)),
    date("H:i", strtotime($a_mais)),
  ], $delimiter);
}

// Fecha o ponteiro do arquivo
fclose($output);
