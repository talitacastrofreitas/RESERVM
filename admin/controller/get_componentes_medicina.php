<?php

include '../../conexao/conexao.php'; 

header('Content-Type: application/json');

if (!isset($conn) || $conn === null) {
    echo json_encode(['error' => 'Erro interno: Falha na conexão com o banco.']);
    exit;
}

$tipo = $_GET['tipo'] ?? ''; 
$curso_medicina = 14; // <--- ID FIXO DO CURSO DE MEDICINA

// A query base já começa filtrando pelo curso 14
$sql = "SELECT compc_id, compc_componente, compc_semestre 
        FROM componente_curricular 
        WHERE compc_curso = :curso AND compc_status = 1 ";

// Adiciona os filtros de semestre mantendo o filtro do curso
if ($tipo == 'calouros') {
    // Traz o curso 14 E (semestre 1 OU sem semestre)
    $sql .= " AND (compc_semestre = 1 OR compc_semestre IS NULL)";
} elseif ($tipo == 'veteranos') {
    // Traz o curso 14 E (semestre maior que 1 OU sem semestre)
    $sql .= " AND (compc_semestre > 1 OR compc_semestre IS NULL)";
}

$sql .= " ORDER BY compc_componente ASC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([':curso' => $curso_medicina]);
    $componentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($componentes ?: []);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro SQL: ' . $e->getMessage()]);
}
?>