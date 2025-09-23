<?php
header('Content-Type: application/json');

// Inclua a conexão com o banco de dados
require '../conexao/conexao.php';

// Verifique se os IDs foram enviados
if (!isset($_GET['ids']) || empty($_GET['ids'])) {
    echo json_encode(['success' => false, 'message' => 'Nenhum ID de reserva fornecido.']);
    exit;
}

$ids = explode(',', $_GET['ids']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

try {
    // Consulta para pegar os dados das reservas selecionadas
    $stmt = $conn->prepare("SELECT res_tipo_reserva, res_espaco_id, res_hora_inicio, res_hora_fim, res_campus, res_recursos FROM reservas WHERE res_id IN ({$placeholders})");
    $stmt->execute($ids);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 1. Verificar se todas as reservas são do tipo 'Fixa' (ID 2)
    foreach ($reservas as $reserva) {
        if ($reserva['res_tipo_reserva'] != 2) {
            echo json_encode(['success' => false, 'message' => 'A edição em massa é permitida somente para reservas fixas.']);
            exit;
        }
    }

    // 2. Verificar se o local reservado, horário de início e fim são iguais
    if (count($reservas) > 1) {
        $primeiraReserva = $reservas[0];
        foreach ($reservas as $reserva) {
            if (
                $reserva['res_espaco_id'] != $primeiraReserva['res_espaco_id'] ||
                $reserva['res_hora_inicio'] != $primeiraReserva['res_hora_inicio'] ||
                $reserva['res_hora_fim'] != $primeiraReserva['res_hora_fim']
            ) {
                echo json_encode(['success' => false, 'message' => 'As reservas selecionadas possuem horários ou locais diferentes. Por favor, selecione reservas com o mesmo horário e local para editar em massa.']);
                exit;
            }
        }
    }

    // Se tudo estiver certo, retorne os dados da primeira reserva para preencher o modal
    // Como a lógica acima garante que os dados são iguais, podemos usar os da primeira
    echo json_encode(['success' => true, 'message' => 'Validação bem-sucedida.', 'data' => $reservas[0]]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao validar as reservas: ' . $e->getMessage()]);
    exit;
}
?>