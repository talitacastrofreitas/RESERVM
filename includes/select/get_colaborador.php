<?php
include '../../conexao/conexao.php';

header('Content-Type: application/json');

$matricula = $_POST['matricula'] ?? '';

if (!$matricula) {
  echo json_encode(['success' => false]);
  exit;
}

try {
  // Consulta
  $stmt = $conn->prepare("SELECT CHAPA, NOMESOCIAL, EMAIL FROM $view_colaboradores WHERE CHAPA = :CHAPA");
  $stmt->bindParam(':CHAPA', $matricula);
  $stmt->execute();

  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($usuario) {
    echo json_encode([
      'success' => true,
      'nome' => $usuario['NOMESOCIAL'],
      'email' => $usuario['EMAIL']
    ]);
  } else {
    echo json_encode(['success' => false]);
  }
} catch (PDOException $e) {
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
