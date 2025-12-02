<?php
// session_start();
include '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
  try {

    $conn->beginTransaction(); 
    $acao = $_POST['acao'] ?? $_GET['acao']; 

    // DADOS COMUNS
    if ($acao === 'cadastrar' || $acao === 'atualizar') {
        if (empty($_POST['compc_componente']) || empty($_POST['compc_curso'])) {
            throw new Exception("Preencha os campos obrigatórios!");
        }

        $compc_componente = trim($_POST['compc_componente']);
        $compc_curso      = trim($_POST['compc_curso']);
        $compc_semestre   = !empty($_POST['compc_semestre']) ? $_POST['compc_semestre'] : null;
        $compc_status     = isset($_POST['compc_status']) ? 1 : 0;
        $professores      = $_POST['compc_professores'] ?? []; 

    }
    
    $rvm_admin_id = $_SESSION['reservm_admin_id'];

    // --- CADASTRAR ---
    if ($acao === 'cadastrar') {
        $log_acao = 'Cadastro';

        // Valida duplicidade
        $sqlVerifica = "SELECT COUNT(*) FROM componente_curricular WHERE compc_componente = :comp AND compc_curso = :curso";
        $stmtV = $conn->prepare($sqlVerifica);
        $stmtV->execute([':comp' => $compc_componente, ':curso' => $compc_curso]);
        if ($stmtV->fetchColumn() > 0) throw new Exception("Componente já cadastrado!");

        // Insere Componente
        $sql = "INSERT INTO componente_curricular (compc_componente, compc_curso, compc_semestre, compc_status, compc_user_id, compc_data_cad, compc_data_upd) 
                VALUES (UPPER(:comp), :curso, :sem, :st, :user, GETDATE(), GETDATE())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':comp'=>$compc_componente, ':curso'=>$compc_curso, ':sem'=>$compc_semestre,':st'=>$compc_status, ':user'=>$rvm_admin_id]);
        $compc_id = $conn->lastInsertId();

        // Insere Professores
        if (!empty($professores)) {
            $sqlProf = "INSERT INTO componente_professores (cp_compc_id, cp_colaborador_matricula) VALUES (:id, :chapa)";
            $stmtProf = $conn->prepare($sqlProf);
            foreach ($professores as $chapa) {
                $stmtProf->execute([':id' => $compc_id, ':chapa' => $chapa]);
            }
        }

    // --- ATUALIZAR ---
    } elseif ($acao === 'atualizar') {
        $compc_id = $_POST['compc_id'];
        $log_acao = 'Atualização';

        // Atualiza Componente
        $sql = "UPDATE componente_curricular SET compc_componente = UPPER(:comp), compc_curso = :curso, compc_semestre = :sem, compc_status = :st, compc_user_id = :user, compc_data_upd = GETDATE() WHERE compc_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':comp'=>$compc_componente, ':curso'=>$compc_curso, ':sem'=>$compc_semestre, ':st'=>$compc_status, ':user'=>$rvm_admin_id, ':id'=>$compc_id]);

        // Atualiza Professores
        $conn->prepare("DELETE FROM componente_professores WHERE cp_compc_id = ?")->execute([$compc_id]);

        if (!empty($professores)) {
            $sqlProf = "INSERT INTO componente_professores (cp_compc_id, cp_colaborador_matricula) VALUES (:id, :chapa)";
            $stmtProf = $conn->prepare($sqlProf);
            foreach ($professores as $chapa) {
                $stmtProf->execute([':id' => $compc_id, ':chapa' => $chapa]);
            }
        }

    // --- DELETAR ---
    } elseif ($_GET['acao'] === 'deletar') {
        $compc_id = $_GET['compc_id'];
        $log_acao = 'Exclusão';

        $conn->prepare("DELETE FROM componente_professores WHERE cp_compc_id = ?")->execute([$compc_id]);
        $conn->prepare("DELETE FROM componente_curricular WHERE compc_id = ?")->execute([$compc_id]);
    }

    // LOG SIMPLIFICADO
    $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data) VALUES ('COMPONENTE', :acao, :id, 'Dados atualizados', :user, GETDATE())";
    $conn->prepare($sqlLog)->execute([':acao'=>$log_acao, ':id'=>$compc_id ?? 0, ':user'=>$rvm_admin_id]);

    $conn->commit();
    $_SESSION["msg"] = "Operação realizada com sucesso!";
    header("Location: ../admin/componente_curricular.php");
    exit;

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = "Erro: " . $e->getMessage();
    header("Location: ../admin/componente_curricular.php");
    exit;
  }
}
?>