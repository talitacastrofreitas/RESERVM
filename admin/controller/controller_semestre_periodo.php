<?php
session_start();
include '../../conexao/conexao.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['acao'])) {
    try {
        $conn->beginTransaction();
        
        $acao = $_REQUEST['acao'];
        $admin_id = $_SESSION['reservm_admin_id'] ?? 1;

        if ($acao == 'cadastrar') {
            $dt_ini = $_POST['data_inicio'];
            $dt_fim = $_POST['data_fim'];

            if($dt_ini >= $dt_fim) throw new Exception("Data de início deve ser menor que o fim.");

            $stmt = $conn->prepare("INSERT INTO conf_semestre_periodo (semp_data_inicio, semp_data_fim, semp_cad_id, semp_data_upd) VALUES (?, ?, ?, GETDATE())");
            $stmt->execute([$dt_ini, $dt_fim, $admin_id]);
            $_SESSION['msg'] = "Período cadastrado!";

        } elseif ($acao == 'editar') {
            $id = $_POST['semp_id'];
            $dt_ini = $_POST['data_inicio'];
            $dt_fim = $_POST['data_fim'];

            $stmt = $conn->prepare("UPDATE conf_semestre_periodo SET semp_data_inicio = ?, semp_data_fim = ?, semp_cad_id = ?, semp_data_upd = GETDATE() WHERE semp_id = ?");
            $stmt->execute([$dt_ini, $dt_fim, $admin_id, $id]);
            $_SESSION['msg'] = "Período atualizado!";

        } elseif ($acao == 'deletar') {
            $id = $_GET['id'];
            $conn->prepare("DELETE FROM conf_semestre_periodo WHERE semp_id = ?")->execute([$id]);
            $_SESSION['msg'] = "Período excluído!";
        }

        $conn->commit();
        header("Location: ../conf_semestre_periodo.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['erro'] = "Erro: " . $e->getMessage();
        header("Location: ../conf_semestre_periodo.php");
        exit();
    }
}
?>