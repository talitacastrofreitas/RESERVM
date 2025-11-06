<?php
// Retorna JSON
header('Content-Type: application/json');

include '../conexao/conexao.php';

// Recebe dados enviados via GET
$localCabula   = $_GET['localCabula'];
$localBrotas   = $_GET['localBrotas'];
$res_espaco_id = !empty($localCabula) ? $localCabula : $localBrotas;
//
$data          = $_GET['data'] ?? null;
$hora_inicio   = $_GET['hora_inicio'] ?? null;
$hora_fim      = $_GET['hora_fim'] ?? null;

// Verifica se os 3 campos vieram preenchidos
if ($data && $hora_inicio && $hora_fim) {
  // Completa o formato SQL Server: HH:MM:SS.0000000
  $hora_inicio .= ":00.0000000";
  $hora_fim .= ":00.0000000";

  $sql = "SELECT COUNT(*) FROM reservas 
            WHERE res_data = :data AND res_espaco_id = :res_espaco_id
              AND (:hora_inicio < res_hora_fim AND :hora_fim > res_hora_inicio)";

  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':res_espaco_id' => $res_espaco_id,
    ':data' => $data,
    ':hora_inicio' => $hora_inicio,
    ':hora_fim' => $hora_fim
  ]);

  $conflito = $stmt->fetchColumn();

  echo json_encode(['conflito' => $conflito > 0]);
} else {
  echo json_encode(['erro' => 'Dados incompletos']);
}
