<?php
include 'conexao/conexao.php';






if (isset($_POST['curso_id'])) {
  $cursoId = $_POST['curso_id'];
  $componenteSelecionado = $_POST['componente_id'] ?? null;

  try {
    $stmt = $conn->prepare("SELECT compc_id, compc_componente FROM componente_curricular WHERE compc_curso = ?");
    $stmt->execute([$cursoId]);
    $componentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($componentes) {
      echo '<option selected disabled value=""></option>';
      foreach ($componentes as $componente) {
        $selected = ($componente['compc_id'] == $componenteSelecionado) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($componente['compc_id']) . "' $selected>" . htmlspecialchars($componente['compc_componente']) . "</option>";
      }
      echo "<option value='0'>OUTRO</option>";
    } else {
      echo '<option value="">Nenhum componente encontrado</option>';
    }
  } catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
  }
}
