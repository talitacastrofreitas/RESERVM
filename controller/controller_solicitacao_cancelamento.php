<?php
// 1. GARANTE A SESSÃO E INICIA O BUFFERING
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Certifique-se de que a conexão ($conn) e o $global_user_id estão disponíveis

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['acao']) || $_POST['acao'] !== 'solicitar_cancelamento') {
    http_response_code(400);
    $_SESSION["erro"] = "Acesso inválido.";
    header("Location: ../canceladas.php");
    exit;
}

// 2. Coleta e Validação de Dados
$tipo_cancelamento = $_POST['solcanc_tipo'] ?? '';
$id_alvo = $_POST['solcanc_id_alvo'] ?? ''; // ID da solicitação ou da reserva
$motivo = trim($_POST['solcanc_motivo'] ?? '');
$usuario_logado_id = $global_user_id; // Assumindo que está disponível


if (empty($tipo_cancelamento) || empty($id_alvo) || empty($motivo)) {
    $_SESSION["erro"] = "Os campos são obrigatórios.";
    header("Location: ../canceladas.php");
    exit;
}

// 3. Determinação de IDs
$solic_id = ($tipo_cancelamento === 'Solicitacao') ? $id_alvo : null;
$res_id = ($tipo_cancelamento === 'Reserva') ? $id_alvo : null;
$solcanc_id = uniqid(true); // Geração de um ID único (simulação, use uma função real de geração de UUID no seu DB)

// Variável para armazenar o ID da solicitação associada à reserva (pode ser NULL)
$solic_id_associado = null;

// 4. Inicia a Transação
try {
    $conn->beginTransaction();

    if ($tipo_cancelamento === 'Reserva') {

        // 1. CHECAGEM SIMPLES DE EXISTÊNCIA, STATUS E NÃO CANCELAMENTO
        $sql_reserva_detalhes = "
     


        SELECT 
            r.res_solic_id, 
            r.res_user_id, 
            r.res_status 
        FROM reservas r
        -- Adiciona LEFT JOIN para checar o status do pedido de cancelamento (se houver)
        LEFT JOIN solicitacao_cancelamento sc ON sc.solcanc_id = r.res_solic_cancelamento_id
        WHERE r.res_id = :res_id
        -- 1. Status da Reserva deve ser Ativo/Pendente (1 a 5)
        AND (r.res_status IN (1, 2, 3, 4, 5) OR r.res_status IS NULL)
        
        -- 2. [CORREÇÃO AQUI] Permite se: A) Nunca houve pedido de cancelamento OU B) O último pedido foi NEGADO (Status 3)
        AND (
            (r.res_solic_cancelamento_id IS NULL OR LTRIM(RTRIM(r.res_solic_cancelamento_id)) = '')
            OR sc.solcanc_status = 3
        )
    ";

        $stmt_reserva = $conn->prepare($sql_reserva_detalhes);
        $stmt_reserva->bindParam(':res_id', $res_id, PDO::PARAM_STR);
        $stmt_reserva->execute();

        $reserva_detalhes = $stmt_reserva->fetch(PDO::FETCH_ASSOC);

        if (!$reserva_detalhes) {
            throw new Exception("A reserva selecionada não pode ser cancelada (Status não ativo, já em cancelamento, ou não encontrada).");
        }

        // 2. CHECAGEM DE PROPRIEDADE DO USUÁRIO
        $res_user_id_db = $reserva_detalhes['res_user_id'];
        $solic_id_associado = $reserva_detalhes['res_solic_id'];
        $is_owner = false;

        // Checa se o usuário logado é dono da Reserva ou da Solicitação PAI
        if ($res_user_id_db === $usuario_logado_id) {
            $is_owner = true;
        }

        if (!$is_owner && $solic_id_associado) {
            $stmt_solic_owner = $conn->prepare("SELECT solic_cad_por FROM solicitacao WHERE solic_id = :solic_id AND solic_cad_por = :user_id_check");
            $stmt_solic_owner->bindParam(':solic_id', $solic_id_associado, PDO::PARAM_STR);
            $stmt_solic_owner->bindParam(':user_id_check', $usuario_logado_id, PDO::PARAM_STR);
            $stmt_solic_owner->execute();

            if ($stmt_solic_owner->fetchColumn()) {
                $is_owner = true;
            }
        }

        if (!$is_owner) {
            throw new Exception("A reserva selecionada não pode ser cancelada (Você não é o proprietário).");
        }

        // O ID da solicitação para o INSERT é o res_solic_id retornado.
        $solic_id = $solic_id_associado;
        // CORREÇÃO: O fluxo segue para a inserção e update abaixo.

    } elseif ($tipo_cancelamento === 'Solicitacao') {
        // Status permitidos para SOLICITACAO:
        $stmt_check = $conn->prepare("SELECT COUNT(solic_id) FROM solicitacao WHERE solic_id = :solic_id AND solic_cad_por = :user_id");
        $stmt_check->bindParam(':solic_id', $solic_id, PDO::PARAM_STR);
        $stmt_check->bindParam(':user_id', $usuario_logado_id, PDO::PARAM_STR);
        $stmt_check->execute();

        if (!$stmt_check->fetchColumn()) {
            throw new Exception("Erro ao solicitar cancelamento de solicitação (Dono inválido).");
        }
        $solic_id_associado = $solic_id;
    }

    // 5. Inserção na Tabela solicitacao_cancelamento
    $stmt_insert = $conn->prepare("
        INSERT INTO solicitacao_cancelamento (solcanc_id, solcanc_tipo, solcanc_solic_id, solcanc_res_id, solcanc_usuario_id, solcanc_motivo, solcanc_status)
        VALUES (:id, :tipo, :solic_id, :res_id, :user_id, :motivo, 1) -- Status 1: Aguardando Aprovação
    ");
    $stmt_insert->bindValue(':id', $solcanc_id, PDO::PARAM_STR);
    $stmt_insert->bindValue(':tipo', $tipo_cancelamento, PDO::PARAM_STR);
    $stmt_insert->bindValue(':solic_id', $solic_id_associado, $solic_id_associado ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt_insert->bindValue(':res_id', $res_id, $res_id ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt_insert->bindValue(':user_id', $usuario_logado_id, PDO::PARAM_STR);
    $stmt_insert->bindValue(':motivo', $motivo, PDO::PARAM_STR);
    $stmt_insert->execute();

    // 6. Atualização da tabela alvo
    if ($tipo_cancelamento === 'Reserva' && $res_id) {
        $stmt_update_res = $conn->prepare("UPDATE reservas SET res_solic_cancelamento_id = :solcanc_id WHERE res_id = :res_id");
        $stmt_update_res->bindParam(':solcanc_id', $solcanc_id, PDO::PARAM_STR);
        $stmt_update_res->bindParam(':res_id', $res_id, PDO::PARAM_STR);
        $stmt_update_res->execute();
    } elseif ($tipo_cancelamento === 'Solicitacao' && $solic_id_associado) {
        $stmt_update_solic = $conn->prepare("
            UPDATE reservas 
            SET res_solic_cancelamento_id = :solcanc_id 
            WHERE res_solic_id = :solic_id AND res_status IN (2, 3, 4, 5)
        ");
        $stmt_update_solic->bindParam(':solcanc_id', $solcanc_id, PDO::PARAM_STR);
        $stmt_update_solic->bindParam(':solic_id', $solic_id_associado, PDO::PARAM_STR);
        $stmt_update_solic->execute();

        $stmt_update_solic_main = $conn->prepare("UPDATE solicitacao SET solic_solic_cancelamento_id = :solcanc_id WHERE solic_id = :solic_id");
        $stmt_update_solic_main->bindParam(':solcanc_id', $solcanc_id, PDO::PARAM_STR);
        $stmt_update_solic_main->bindParam(':solic_id', $solic_id_associado, PDO::PARAM_STR);
        $stmt_update_solic_main->execute();
    }

    // 7. COMMIT FINAL
    $conn->commit();

    // Sucesso
    $_SESSION["msg"] = "Solicitação de cancelamento enviada com sucesso.";
    header("Location: ../canceladas.php");
    exit;

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    error_log("Erro ao solicitar cancelamento: " . $e->getMessage());
    // Erro
    $_SESSION["erro"] = $e->getMessage();
    header("Location: ../canceladas.php");
    exit;
}
?>