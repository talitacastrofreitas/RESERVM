<?php
session_start();
include '../conexao/conexao.php';

try {
  $query = "SELECT TOP 200 Nome, Matricula FROM $view_alunos ORDER BY Nome ASC";
  $stmt = $conn->prepare($query);
  $stmt->execute();
  $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($resultados);
} catch (PDOException $e) {
  echo 'Erro: ' . $e->getMessage();
}
