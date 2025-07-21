<?php
include '../../../conexao/conexao.php';

$local = $_POST['local'];
$stmt = $conn->prepare("SELECT * FROM espaco WHERE esp_id = ?");
$stmt->execute([$local]);
$esp = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($esp);
