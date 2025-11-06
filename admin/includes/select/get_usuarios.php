<?php
include '../../conexao/conexao.php';

$matricula = $_POST['matricula'];
$stmt = $conn->prepare("SELECT Nome, Matricula, [E-mail] AS Email, [Telefone 1] AS Telefone FROM $view_alunos WHERE Matricula = ?");
$stmt->execute([$matricula]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($user);
