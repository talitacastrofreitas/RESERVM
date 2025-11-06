<?php
include '../conexao/conexao.php';

// Buscar as datas (suponha que seja da tabela 'conf_dias_bloqueadas' na coluna 'dbloq_data')
$sql = "SELECT dbloq_data FROM conf_dias_bloqueadas WHERE dbloq_status = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();

$datas = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  // Formatar para o padrão do Flatpickr (YYYY-MM-DD)
  $datas[] = date('Y-m-d', strtotime($row['dbloq_data']));
}

// Retornar como JSON para o JavaScript
header('Content-Type: application/json');
echo json_encode($datas);
exit;
