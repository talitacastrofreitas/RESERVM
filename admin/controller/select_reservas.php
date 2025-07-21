<?php
session_start();
include '../../conexao/conexao.php';

try {
  $query_res = "SELECT * FROM reservas";
  $stmt_res = $conn->prepare($query_res);
  $stmt_res->execute();
  $resultados_res = $stmt_res->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($resultados_res);
} catch (PDOException $e) {
  echo 'Erro: ' . $e->getMessage();
}
