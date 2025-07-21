<?php
include '../conexao/conexao.php';

/* ---------------------------------------------------
  CADASTRAR
----------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['func']) && $_GET['func'] == "cad_esp") {

  $esp_id                  = bin2hex(random_bytes(16)); // GERA UM ID DE 32 CARACTERES HEXADECIMAIS
  $esp_codigo              = trim($_POST['esp_codigo']) !== '' ? trim($_POST['esp_codigo']) : NULL;
  $esp_nome_local          = trim($_POST['esp_nome_local']) !== '' ? trim($_POST['esp_nome_local']) : NULL;
  $esp_nome_local_resumido = trim($_POST['esp_nome_local_resumido']) !== '' ? trim($_POST['esp_nome_local_resumido']) : NULL;
  $esp_tipo_espaco         = trim($_POST['esp_tipo_espaco']) !== '' ? trim($_POST['esp_tipo_espaco']) : NULL;
  $esp_andar               = trim($_POST['esp_andar']) !== '' ? trim($_POST['esp_andar']) : NULL;
  $esp_pavilhao            = trim($_POST['esp_pavilhao']) !== '' ? trim($_POST['esp_pavilhao']) : NULL;
  $esp_quant_maxima        = trim($_POST['esp_quant_maxima']) !== '' ? trim($_POST['esp_quant_maxima']) : NULL;
  $esp_quant_media         = trim($_POST['esp_quant_media']) !== '' ? trim($_POST['esp_quant_media']) : NULL;
  $esp_quant_minima        = trim($_POST['esp_quant_minima']) !== '' ? trim($_POST['esp_quant_minima']) : NULL;
  $esp_unidade             = trim($_POST['esp_unidade']) !== '' ? trim($_POST['esp_unidade']) : NULL;
  // PROCESSA OS CHECKBOXES COMO STRING
  $esp_recursos = isset($_POST['esp_recursos']) && is_array($_POST['esp_recursos'])
    ? implode(', ', array_map('htmlspecialchars', $_POST['esp_recursos']))
    : null;
  //
  $esp_status              = trim(isset($_POST['esp_status'])) ? $_POST['esp_status'] : 0;
  $rvm_admin_id            = $_SESSION['reservm_admin_id'];
  // -------------------------------

  // VERIFICA DADOS DUPLICADOS
  $sqlVerifica = $conn->prepare("SELECT COUNT(*) as total FROM espaco WHERE esp_codigo = :esp_codigo OR (esp_nome_local = :esp_nome_local AND esp_unidade = :esp_unidade)");
  $sqlVerifica->execute([':esp_codigo' => $esp_codigo, ':esp_nome_local' => $esp_nome_local, ':esp_unidade' => $esp_unidade]);
  $resultado = $sqlVerifica->fetch(PDO::FETCH_ASSOC);
  if ($resultado['total'] > 0) {
    $_SESSION["erro"] = "Código já cadastrado ou nome do local já existe para esta unidade!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = $conn->prepare("INSERT INTO espaco (
                                                  esp_id,
                                                  esp_codigo,
                                                  esp_nome_local,
                                                  esp_nome_local_resumido,
                                                  esp_tipo_espaco,
                                                  esp_andar, esp_pavilhao,
                                                  esp_quant_maxima,
                                                  esp_quant_media,
                                                  esp_quant_minima,
                                                  esp_unidade,
                                                  esp_recursos,
                                                  esp_status, 
                                                  esp_cad_id,
                                                  esp_data_cad,
                                                  esp_data_upd
                                                ) VALUES (
                                                  :esp_id,
                                                  UPPER(:esp_codigo),
                                                  UPPER(:esp_nome_local),
                                                  UPPER(:esp_nome_local_resumido),
                                                  :esp_tipo_espaco,
                                                  :esp_andar,
                                                  :esp_pavilhao,
                                                  :esp_quant_maxima,
                                                  :esp_quant_media,
                                                  :esp_quant_minima,
                                                  :esp_unidade,
                                                  :esp_recursos,
                                                  :esp_status,
                                                  :esp_cad_id,
                                                  GETDATE(),
                                                  GETDATE()
                                                )");
      $sql->execute([
        ':esp_id'                  => $esp_id,
        ':esp_codigo'              => $esp_codigo,
        ':esp_nome_local'          => $esp_nome_local,
        ':esp_nome_local_resumido' => $esp_nome_local_resumido,
        ':esp_tipo_espaco'         => $esp_tipo_espaco,
        ':esp_andar'               => $esp_andar,
        ':esp_pavilhao'            => $esp_pavilhao,
        ':esp_quant_maxima'        => $esp_quant_maxima,
        ':esp_quant_media'         => $esp_quant_media,
        ':esp_quant_minima'        => $esp_quant_minima,
        ':esp_unidade'             => $esp_unidade,
        ':esp_recursos'            => $esp_recursos,
        ':esp_status'              => $esp_status,
        ':esp_cad_id'              => $rvm_admin_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'ESPAÇO',
        ':acao'    => 'CADASTRO',
        ':acao_id' => $esp_id,
        ':dados'   => 'Código: ' . $esp_codigo .
          '; Nome local: ' . $esp_nome_local .
          '; Nome local resumo: ' . $esp_nome_local_resumido .
          '; Tipo espaço: ' . $esp_tipo_espaco .
          '; Andar: ' . $esp_andar .
          '; Pavilhão: ' . $esp_pavilhao .
          '; Quant. Max: ' . $esp_quant_maxima .
          '; Quant. médio: ' . $esp_quant_media .
          '; Quant. mínimo: ' . $esp_quant_minima .
          '; Unidade: ' . $esp_unidade .
          '; Recursos: ' . $esp_recursos .
          '; Status: ' . $esp_status,
        ':user_id' => $rvm_admin_id
      ]);

      $conn->commit(); // SE NÃO HOUVER NENHUM ERRO, REALIZA A AÇÃO
      // -------------------------------

      $_SESSION["msg"] = "Cadastro realizado com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Cadastro não realizado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
}














/* ---------------------------------------------------
  EDITAR
----------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['func']) && $_GET['func'] == "edit_esp") {

  $esp_id                  = trim($_POST['esp_id']) !== '' ? trim($_POST['esp_id']) : NULL;
  $esp_codigo              = trim($_POST['esp_codigo']) !== '' ? trim($_POST['esp_codigo']) : NULL;
  $esp_nome_local          = trim($_POST['esp_nome_local']) !== '' ? trim($_POST['esp_nome_local']) : NULL;
  $esp_nome_local_resumido = trim($_POST['esp_nome_local_resumido']) !== '' ? trim($_POST['esp_nome_local_resumido']) : NULL;
  $esp_tipo_espaco         = trim($_POST['esp_tipo_espaco']) !== '' ? trim($_POST['esp_tipo_espaco']) : NULL;
  $esp_andar               = trim($_POST['esp_andar']) !== '' ? trim($_POST['esp_andar']) : NULL;
  $esp_pavilhao            = trim($_POST['esp_pavilhao']) !== '' ? trim($_POST['esp_pavilhao']) : NULL;
  $esp_quant_maxima        = trim($_POST['esp_quant_maxima']) !== '' ? trim($_POST['esp_quant_maxima']) : NULL;
  $esp_quant_media         = trim($_POST['esp_quant_media']) !== '' ? trim($_POST['esp_quant_media']) : NULL;
  $esp_quant_minima        = trim($_POST['esp_quant_minima']) !== '' ? trim($_POST['esp_quant_minima']) : NULL;
  $esp_unidade             = trim($_POST['esp_unidade']) !== '' ? trim($_POST['esp_unidade']) : NULL;
  // PROCESSA OS CHECKBOXES COMO STRING
  $esp_recursos = isset($_POST['esp_recursos']) && is_array($_POST['esp_recursos'])
    ? implode(', ', array_map('htmlspecialchars', $_POST['esp_recursos']))
    : null;
  //
  $esp_status              = trim(isset($_POST['esp_status'])) ? $_POST['esp_status'] : 0;
  $rvm_admin_id            = $_SESSION['reservm_admin_id'];
  // -------------------------------


  // VERIFICA DADOS DUPLICADOS
  $sqlVerifica = $conn->prepare("SELECT COUNT(*) as total FROM espaco WHERE (esp_codigo = :esp_codigo OR (esp_nome_local = :esp_nome_local AND esp_unidade = :esp_unidade)) AND esp_id != :esp_id");
  $sqlVerifica->execute([':esp_id' => $esp_id, ':esp_codigo' => $esp_codigo, ':esp_nome_local' => $esp_nome_local, ':esp_unidade' => $esp_unidade]);
  $resultado = $sqlVerifica->fetch(PDO::FETCH_ASSOC);
  if ($resultado['total'] > 0) {
    $_SESSION["erro"] = "Código já cadastrado ou nome do local já existe para esta unidade!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } else {

    try {
      $conn->beginTransaction(); // INICIA A TRANSAÇÃO
      $sql = $conn->prepare("UPDATE
                                    espaco
                                SET   
                                    esp_codigo              = UPPER(:esp_codigo),
                                    esp_nome_local          = UPPER(:esp_nome_local),
                                    esp_nome_local_resumido = UPPER(:esp_nome_local_resumido),
                                    esp_tipo_espaco         = :esp_tipo_espaco,
                                    esp_andar               = :esp_andar,
                                    esp_pavilhao            = :esp_pavilhao,
                                    esp_quant_maxima        = :esp_quant_maxima,
                                    esp_quant_media         = :esp_quant_media,
                                    esp_quant_minima        = :esp_quant_minima,
                                    esp_unidade             = :esp_unidade,
                                    esp_recursos            = :esp_recursos,
                                    esp_status              = :esp_status,
                                    esp_cad_id              = :esp_cad_id,
                                    esp_data_upd            = GETDATE()
                              WHERE
                                    esp_id = :esp_id");

      $sql->execute([
        ':esp_id'                  => $esp_id,
        ':esp_codigo'              => $esp_codigo,
        ':esp_nome_local'          => $esp_nome_local,
        ':esp_nome_local_resumido' => $esp_nome_local_resumido,
        ':esp_tipo_espaco'         => $esp_tipo_espaco,
        ':esp_andar'               => $esp_andar,
        ':esp_pavilhao'            => $esp_pavilhao,
        ':esp_quant_maxima'        => $esp_quant_maxima,
        ':esp_quant_media'         => $esp_quant_media,
        ':esp_quant_minima'        => $esp_quant_minima,
        ':esp_unidade'             => $esp_unidade,
        ':esp_recursos'            => $esp_recursos,
        ':esp_status'              => $esp_status,
        ':esp_cad_id'              => $rvm_admin_id
      ]);

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                              VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'  => 'ESPAÇO',
        ':acao'    => 'ATUALIZAÇÃO',
        ':acao_id' => $esp_id,
        ':dados'   => 'Código: ' . $esp_codigo .
          '; Nome local: ' . $esp_nome_local .
          '; Nome local resumo: ' . $esp_nome_local_resumido .
          '; Tipo espaço: ' . $esp_tipo_espaco .
          '; Andar: ' . $esp_andar .
          '; Pavilhão: ' . $esp_pavilhao .
          '; Quant. Max: ' . $esp_quant_maxima .
          '; Quant. médio: ' . $esp_quant_media .
          '; Quant. mínimo: ' . $esp_quant_minima .
          '; Unidade: ' . $esp_unidade .
          '; Recursos: ' . $esp_recursos .
          '; Status: ' . $esp_status,
        ':user_id' => $rvm_admin_id
      ]);

      $conn->commit(); // SE NÃO HOUVER NENHUM ERRO, REALIZA A AÇÃO
      // -------------------------------

      $_SESSION["msg"] = "Dados atualizados com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } catch (PDOException $e) {
      //echo 'Error: ' . $e->getMessage();
      $conn->rollBack();
      $_SESSION["erro"] = "Dados não atualizados!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
}










/* ---------------------------------------------------
  EXCLUIR
----------------------------------------------------- */
if (isset($_GET['func']) && $_GET['func'] == "exc_esp") {

  $esp_id        = $_GET["esp_id"];
  $rvm_admin_id = $_SESSION['reservm_admin_id'];

  // NÃO PERMITE EXCLUIR VEÍCULO SE HOUVER OCORRÊNCIA ATRELADA A ELE
  // $sql = $conn->prepare("SELECT COUNT(*) FROM ocorrencia WHERE ocor_placa = ?");
  // $sql->execute([$placa]);
  // if ($sql->fetchColumn()) {
  //   $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Estes dados não podem ser excluídos!";
  //   echo "<script> history.go(-1);</script>";
  //   return die;
  // }

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $stmt = $conn->prepare("DELETE FROM espaco WHERE esp_id = ?");
    $stmt->execute([$esp_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount()) {

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                              VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'     => 'ESPAÇO',
        ':acao'       => 'EXCLUSÃO',
        ':acao_id'    => $esp_id,
        ':user_id'    => $rvm_admin_id
      ]);

      $conn->commit(); // SE NÃO HOUVER NENHUM ERRO, REALIZA A AÇÃO
      // -------------------------------

      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check me-2\"></i> Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      $conn->rollBack();
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Erro ao tentar excluir o registro!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  } catch (PDOException $e) {
    $conn->rollBack();
    //echo "Erro: " . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Erro ao tentar excluir o registro!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}
