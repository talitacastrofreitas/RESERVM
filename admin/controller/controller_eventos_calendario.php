<?php
// controller_eventos_calendario.php
// (VERSÃO DATA TABLES: BUSCA MULTI-COLUNA ESTÁVEL)

ob_start();
header('Content-Type: application/json; charset=utf-8');

// Função para garantir UTF-8
function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string($d)) {
        if (!mb_check_encoding($d, 'UTF-8')) {
            return mb_convert_encoding($d, 'UTF-8', 'ISO-8859-1');
        }
    }
    return $d;
}

try {
    include __DIR__ . '/../../conexao/conexao.php'; 

    $calendario_pedido = isset($_GET['cal']) ? $_GET['cal'] : 'academico';
    $filtro_status = isset($_GET['filtro_status']) ? $_GET['filtro_status'] : '1';
    $filtro_busca = isset($_GET['filtro_busca']) ? trim($_GET['filtro_busca']) : ''; 

    $params = []; 

    // 1. Filtro Tipo
    $sql_tipo = '';
    if ($calendario_pedido === 'academico') {
        $sql_tipo = "AND (e.dbloq_cal_tipo = 1 OR e.dbloq_cal_tipo IS NULL OR e.dbloq_cal_tipo = 0)"; 
    } elseif ($calendario_pedido === 'administrativo') {
        $sql_tipo = "AND (e.dbloq_cal_tipo = 2 OR e.dbloq_cal_tipo IS NULL OR e.dbloq_cal_tipo = 0)"; 
    }

    // 2. Filtro Status
    $sql_status = '';
    if ($filtro_status === '1') {
        $sql_status = "AND e.dbloq_status = 1";
    } elseif ($filtro_status === '0') {
        $sql_status = "AND e.dbloq_status = 0";
    }

    // 3. Filtro de Busca MULTI-TERMOS (DataTables Style)
    // COMENTADO: A BUSCA AGORA É FEITA NO JAVASCRIPT
    $sql_busca = '';
    
    /*
    if (!empty($filtro_busca)) {
        $termo_limpo = str_replace(['/', '-'], ' ' , $filtro_busca);
        $termos = explode(' ', $termo_limpo);
        
        foreach ($termos as $index => $termo) {
            $termo = trim($termo);
            if (empty($termo)) continue; 

            $paramName = ":busca{$index}";
            
            // ESTRATÉGIA: Busca o termo em todas as colunas textuais/convertidas.
            $sql_busca .= " AND (
                motivo.dbloqm_motivo LIKE $paramName
                OR e.dbloq_mes LIKE $paramName
                OR CAST(e.dbloq_dia AS VARCHAR(20)) LIKE $paramName
                OR CAST(e.dbloq_ano AS VARCHAR(20)) LIKE $paramName
                OR CONVERT(VARCHAR(20), e.dbloq_data, 103) LIKE $paramName
            )";
            
            $params[$paramName] = ['value' => '%' . $termo . '%', 'type' => PDO::PARAM_STR];
        }
    }
    */

    $sql = "SELECT 
                e.dbloq_id, 
                e.dbloq_data, 
                e.dbloq_status, 
                e.dbloq_cal_tipo,
                motivo.dbloqm_motivo,
                motivo.dbloqm_tipo
            FROM conf_dias_bloqueadas e
            LEFT JOIN conf_dias_bloqueadas_motivo AS motivo ON e.dbloq_motivo = motivo.dbloqm_id
            WHERE 1=1
            $sql_tipo
            $sql_status
            $sql_busca"; 

    $stmt = $conn->prepare($sql);
    
    // O loop de binding ainda é necessário caso outros filtros usem $params
    foreach ($params as $name => $data) { 
        $stmt->bindValue($name, $data['value'], $data['type']);
    }
    
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $eventos = [];
    foreach ($rows as $r) {
        $motivo = $r['dbloqm_motivo'] ?? 'Evento Desconhecido';
        $tipo = $r['dbloqm_tipo'] ?? 'geral';

        $eventos[] = [
            'id' => (int) $r['dbloq_id'],
            'title' => $motivo,
            'start' => $r['dbloq_data'],
            'end' => $r['dbloq_data'],
            'allDay' => true,
            'editable' => true,
            'extendedProps' => [
                'tipo' => $tipo,
                'motivo' => $motivo, 
                'ativo' => (int) $r['dbloq_status'],
                'origem' => 'local'
            ]
        ];
    }

    // Usar ob_end_clean() para limpar o buffer antes do output.
    ob_end_clean(); 
    echo json_encode(utf8ize($eventos), JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Garantir que a limpeza do buffer ocorre mesmo em caso de erro fatal
    ob_end_clean(); 
    http_response_code(500);
    echo json_encode(['error' => 'Erro SQL/Execução: ' . $e->getMessage()]); 
}
exit;