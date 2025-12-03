<?php
// controller_calendario.php
// (VERSÃO CORRIGIDA ESTRUTURALMENTE E COM FIX DO INSERT)

ob_start();
header('Content-Type: application/json; charset=utf-8');

session_start();
include __DIR__ . '/../../conexao/conexao.php'; // $conn

$method = $_SERVER['REQUEST_METHOD'];

try {

    // ==============================================
    // MÉTODO GET - Retorna dados de um evento (preencher modal)
    // ==============================================
    if ($method === 'GET') {
        
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID não fornecido.']);
            exit;
        }

        
        $sql = 'SELECT 
                    e.dbloq_id, 
                    e.dbloq_data, 
                    e.dbloq_dia, 
                    e.dbloq_mes, 
                    e.dbloq_ano, 
                    e.dbloq_motivo,  
                    e.dbloq_status,
                    e.dbloq_cal_tipo,
                    e.dbloq_cal_semestre
                FROM conf_dias_bloqueadas e
                WHERE e.dbloq_id = :id';
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Evento não encontrado.']);
            exit;
        }

        // Formata o retorno
        $data = [
            'dbloq_id' => (int)$row['dbloq_id'],
            'dbloq_data' => $row['dbloq_data'],
            'dbloq_dia' => (int)$row['dbloq_dia'],
            'dbloq_mes' => $row['dbloq_mes'],
            'dbloq_ano' => (int)$row['dbloq_ano'],
            'dbloq_motivo' => (int)$row['dbloq_motivo'],
            'dbloq_status' => (int)$row['dbloq_status'],
            'dbloq_cal_tipo' => $row['dbloq_cal_tipo'] ? (int)$row['dbloq_cal_tipo'] : '',
            'dbloq_cal_semestre' => $row['dbloq_cal_semestre'] ? (int)$row['dbloq_cal_semestre'] : ''
        ];

        ob_clean();
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;

    } 
    
    // ==============================================
    // MÉTODO POST - Criar ou Atualizar Evento
    // ==============================================
    elseif ($method === 'POST') {
        
       
        $input = $_POST;

        // Pega o ID do usuário da sessão
        $userId = $_SESSION['reservm_admin_id'] ?? null; 
        if (!$userId) {
            http_response_code(403); 
            echo json_encode(['success' => false, 'message' => 'Usuário não autenticado. Faça login novamente.']);
            exit;
        }

        $id = $input['dbloq_id'] ?? null;
        
        // Dados do formulário
        $dataEvento = $input['dataEvento'] ?? null;
        $mes = $input['dbloq_mes'] ?? null;
        $ano = $input['dbloq_ano'] ?? null;
        $diaSemanaId = $input['dbloq_dia'] ?? null;
        $motivoId = $input['dbloq_motivo'] ?? null;
        $tipoCalendario = $input['dbloq_cal_tipo'] ?: null; 
        $semestre = $input['dbloq_cal_semestre'] ?: null; 
        $status = isset($input['dbloq_status']) ? 1 : 0; 

        if (!$dataEvento || !$motivoId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Data e Motivo são obrigatórios.']);
            exit;
        }

        $isUpdate = !empty($id);

        if ($isUpdate) {
            // LÓGICA DE UPDATE
            $sql = "UPDATE conf_dias_bloqueadas SET
                        dbloq_data = :data,
                        dbloq_dia = :dia,
                        dbloq_mes = :mes,
                        dbloq_ano = :ano,
                        dbloq_motivo = :motivo,
                        dbloq_status = :status,
                        dbloq_cal_tipo = :tipo,
                        dbloq_cal_semestre = :semestre,
                        dbloq_data_upd = GETDATE()
                    WHERE dbloq_id = :id";
        } else {
            // LÓGICA DE INSERT 
            $sql = "INSERT INTO conf_dias_bloqueadas 
                        (dbloq_data, dbloq_dia, dbloq_mes, dbloq_ano, dbloq_motivo, dbloq_status, dbloq_cal_tipo, dbloq_cal_semestre, dbloq_data_cad, dbloq_data_upd, dbloq_user_id)
                    VALUES 
                        (:data, :dia, :mes, :ano, :motivo, :status, :tipo, :semestre, GETDATE(), GETDATE(), :user_id)";
        }

        $stmt = $conn->prepare($sql);
        
        // Binds
        $stmt->bindValue(':data', $dataEvento);
        $stmt->bindValue(':dia', $diaSemanaId, PDO::PARAM_INT);
        $stmt->bindValue(':mes', $mes);
        $stmt->bindValue(':ano', $ano, PDO::PARAM_INT);
        $stmt->bindValue(':motivo', $motivoId, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipoCalendario, $tipoCalendario ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':semestre', $semestre, $semestre ? PDO::PARAM_INT : PDO::PARAM_NULL);

        if ($isUpdate) {
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
           
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR); 
        }
        
        $stmt->execute();
        
        $message = $isUpdate ? 'Evento atualizado com sucesso!' : 'Evento cadastrado com sucesso!';
        
        ob_clean();
        echo json_encode(['success' => true, 'message' => $message]);
        exit;

    } 

    // ==============================================
    // MÉTODO DELETE - Excluir um Evento
    // ==============================================
    elseif ($method === 'DELETE') {
        
        // Segurança: Verifique o usuário
        $userId = $_SESSION['reservm_admin_id'] ?? null;
        if (!$userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
            exit;
        }

        // Pega o ID da URL 
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID não fornecido.']);
            exit;
        }

        $sql = "DELETE FROM conf_dias_bloqueadas WHERE dbloq_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Evento excluído com sucesso!']);
        } else {
            ob_clean();
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Evento não encontrado ou já excluído.']);
        }
        exit;
    }

    
    // Se não for GET nem POST
    else {
        http_response_code(405); 
        echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
        exit;
    }

} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
    exit;
}
?>