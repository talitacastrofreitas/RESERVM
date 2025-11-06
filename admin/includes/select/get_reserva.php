<?php
include '../../../conexao/conexao.php';

$res_id = $_POST['res_id'];
$stmt = $conn->prepare("SELECT res_id, res_hora_inicio, res_hora_fim FROM reservas WHERE res_id = ?");
$stmt->execute([$res_id]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

// Formata para HH:MM
$reserva['res_hora_inicio'] = substr($reserva['res_hora_inicio'], 0, 5);
$reserva['res_hora_fim'] = substr($reserva['res_hora_fim'], 0, 5);

echo json_encode($reserva);
