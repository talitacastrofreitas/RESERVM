<?php
include '../../../conexao/conexao.php';

$matricula_nome = $_POST['matricula']; // RECEBE A 'MATRICULA - NOME'
$parts = explode(' - ', $matricula_nome, 2); // DIVIDE A MATRICULA DO NOME
$matricula = trim($parts[0]); // RECEBE APENAS A MATRICULA
$nome = trim($parts[1]); // RECEBE APENAS O NOME

$stmt = $conn->prepare("SELECT CHAPA, NOMESOCIAL, EMAIL
                        FROM $view_colaboradores WHERE CHAPA = ?");
$stmt->execute([$matricula]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($admin);
