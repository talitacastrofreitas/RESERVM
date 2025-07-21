<?php
session_start();
include '../../conexao/conexao.php';

try {
  $query_col = "SELECT CHAPA, NOMESOCIAL, EMAIL
                FROM $view_colaboradores ORDER BY NOMESOCIAL ASC";
  $stmt_col = $conn->prepare($query_col);
  $stmt_col->execute();
  $resultados_col = $stmt_col->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($resultados_col);
} catch (PDOException $e) {
  echo 'Erro: ' . $e->getMessage();
}
