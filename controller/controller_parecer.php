<?php
session_start();
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                            CADASTRAR PARECER
 *****************************************************************************************/
if (isset($dados['CadParecerProposta'])) {

  $prop_parecer_prop_id = trim($_POST['prop_parecer_prop_id']);
  $prop_parecer_obs     = nl2br(trim($_POST['prop_parecer_obs']));
  // -------------------------------
  try {
    $sql = "INSERT INTO propostas_analise_parecer (
                                                    prop_parecer_prop_id,
                                                    prop_parecer_obs,
                                                    prop_parecer_user_id,
                                                    prop_parecer_data_cad,
                                                    prop_parecer_data_upd
                                                  ) VALUES (
                                                    :prop_parecer_prop_id,
                                                    :prop_parecer_obs,
                                                    :prop_parecer_user_id,
                                                    :prop_parecer_data_cad,
                                                    :prop_parecer_data_upd
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_parecer_prop_id", $prop_parecer_prop_id);
    $stmt->bindParam(":prop_parecer_obs", $prop_parecer_obs);
    //
    $stmt->bindParam(":prop_parecer_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_parecer_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":prop_parecer_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - PARECER',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $last_id,
      ':dados'      => 'Parecer: ' . $prop_parecer_obs,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    // CADASTRA AS IMAGENS
    if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

      $prop_parecer_prop_codigo = trim($_POST['prop_parecer_prop_codigo']);
      $parq_categoria           = 5; // CATEGORIA DE ARQUIVO - PARECER DA ANÁLISE = 5
      $arquivos                 = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

      // 10MB
      $maxFileSize = 10 * 1024 * 1024;
      foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
        if (!empty($fileName)) {
          $fileSize = $_FILES["arquivos"]["size"][$key];
          if ($fileSize > $maxFileSize) {
            $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> O arquivo excede o tamanho máximo permitido de 10MB.";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER'] . '#ancora_parecer'));
            return die;
          }
        }
      }
      // -------------------------------

      // CRIA AS PASTAS DOS ARQUIVOS
      $pastaPrincipal = "../uploads/propostas/$prop_parecer_prop_codigo";
      $SubPasta = "../uploads/propostas/$prop_parecer_prop_codigo/$parq_categoria";
      // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
      if (!file_exists($pastaPrincipal)) {
        mkdir($pastaPrincipal, 0777, true);
      }
      // -------------------------------

      // CRIA A SUBPASTA
      if (!file_exists($SubPasta)) {
        mkdir($SubPasta, 0777, true);
      }
      // -------------------------------

      $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

      for ($i = 0; $i < count($nomes); $i++) {
        $extensao = explode('.', $nomes[$i]);
        $extensao = end($extensao);
        $nomes[$i] = rand() . '-' . $nomes[$i];

        $arquivos_permitidos = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG', 'txt', 'TXT']; //FORMATO DE ARQUIVOS PERMITIDOS
        if (in_array($extensao, $arquivos_permitidos)) { // VERIFICAR EXTENSÃO DOS ARQUIVOS
          try {
            $sql = "INSERT INTO propostas_arq (
                                                parq_prop_id,
                                                parq_codigo,
                                                parq_categoria,
                                                parq_arquivo,
                                                parq_user_id,
                                                parq_data_cad
                                                ) VALUES (
                                                :parq_prop_id,
                                                :parq_codigo,
                                                :parq_categoria,
                                                :parq_arquivo,
                                                :parq_user_id,
                                                :parq_data_cad
                                                )";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":parq_prop_id", $prop_parecer_prop_id);
            $stmt->bindParam(":parq_codigo", $prop_parecer_prop_codigo);
            $stmt->bindParam(":parq_categoria", $parq_categoria);
            $stmt->bindParam(":parq_arquivo", $nomes[$i]);
            $stmt->bindParam(":parq_user_id", $_SESSION['reservm_admin_id']);
            $stmt->bindParam(":parq_data_cad", date('Y-m-d H:i:s'));
            $stmt->execute();

            // REGISTRA AÇÃO NO LOG
            $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
            $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                                    VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
            $stmt->execute(array(
              ':modulo'     => 'PROPOSTA - PARECER - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $last_id,
              ':dados'      => 'Código: ' . $prop_parecer_prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
              ':user_id'    => $_SESSION['reservm_admin_id'],
              ':user_nome'  => $_SESSION['reservm_admin_nome'],
              ':data'       => date('Y-m-d H:i:s')
            ));
            // -------------------------------

          } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
          }

          // MOVE AS IMAGENS PARA A PASTA
          $total_rowns = $stmt->rowCount();
          if ($total_rowns > 0) {
            $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_parecer_prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
          }
          // -------------------------------

        } else {
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Formato de arquivo inválido!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER'] . '#ancora_parecer'));
          return die;
        }
      }
    }

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER'] . '#ancora_parecer'));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}










/*****************************************************************************************
                              EDITAR PARECER
 *****************************************************************************************/
if (isset($dados['EditParecerProposta'])) {

  $prop_parecer_id      = trim($_POST['prop_parecer_id']);
  $prop_parecer_prop_id = trim($_POST['prop_parecer_prop_id']);
  $prop_parecer_obs     = nl2br(trim($_POST['prop_parecer_obs']));
  // -------------------------------

  try {
    $sql = "UPDATE
                    propostas_analise_parecer
              SET
                    prop_parecer_obs      = :prop_parecer_obs,
                    prop_parecer_user_id  = :prop_parecer_user_id,
                    prop_parecer_data_upd = :prop_parecer_data_upd
              WHERE
                    prop_parecer_id = :prop_parecer_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_parecer_id", $prop_parecer_id);
    //
    $stmt->bindParam(":prop_parecer_obs", $prop_parecer_obs);
    //
    $stmt->bindParam(":prop_parecer_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_parecer_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - PARECER',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $prop_parecer_id,
      ':dados'      => 'Parecer: ' . $prop_parecer_obs,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    // CADASTRA AS IMAGENS
    if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

      $prop_parecer_prop_codigo = trim($_POST['prop_parecer_prop_codigo']);
      $parq_categoria           = 5; // CATEGORIA DE ARQUIVO - PARECER DA ANÁLISE = 5
      $arquivos                 = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

      // 10MB
      $maxFileSize = 10 * 1024 * 1024;
      foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
        if (!empty($fileName)) {
          $fileSize = $_FILES["arquivos"]["size"][$key];
          if ($fileSize > $maxFileSize) {
            $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> O arquivo excede o tamanho máximo permitido de 10MB.";
            header(sprintf('location: %s', $_SERVER['HTTP_REFERER'] . '#ancora_parecer'));
            return die;
          }
        }
      }
      // -------------------------------

      // CRIA AS PASTAS DOS ARQUIVOS
      $pastaPrincipal = "../uploads/propostas/$prop_parecer_prop_codigo";
      $SubPasta = "../uploads/propostas/$prop_parecer_prop_codigo/$parq_categoria";
      // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
      if (!file_exists($pastaPrincipal)) {
        mkdir($pastaPrincipal, 0777, true);
      }
      // -------------------------------

      // CRIA A SUBPASTA
      if (!file_exists($SubPasta)) {
        mkdir($SubPasta, 0777, true);
      }
      // -------------------------------

      $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

      for ($i = 0; $i < count($nomes); $i++) {
        $extensao = explode('.', $nomes[$i]);
        $extensao = end($extensao);
        $nomes[$i] = rand() . '-' . $nomes[$i];

        $arquivos_permitidos = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG', 'txt', 'TXT']; //FORMATO DE ARQUIVOS PERMITIDOS
        if (in_array($extensao, $arquivos_permitidos)) { // VERIFICAR EXTENSÃO DOS ARQUIVOS
          try {
            $sql = "INSERT INTO propostas_arq (
                                                parq_prop_id,
                                                parq_codigo,
                                                parq_categoria,
                                                parq_arquivo,
                                                parq_user_id,
                                                parq_data_cad
                                                ) VALUES (
                                                :parq_prop_id,
                                                :parq_codigo,
                                                :parq_categoria,
                                                :parq_arquivo,
                                                :parq_user_id,
                                                :parq_data_cad
                                                )";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":parq_prop_id", $prop_parecer_prop_id);
            $stmt->bindParam(":parq_codigo", $prop_parecer_prop_codigo);
            $stmt->bindParam(":parq_categoria", $parq_categoria);
            $stmt->bindParam(":parq_arquivo", $nomes[$i]);
            $stmt->bindParam(":parq_user_id", $_SESSION['reservm_admin_id']);
            $stmt->bindParam(":parq_data_cad", date('Y-m-d H:i:s'));
            $stmt->execute();

            // REGISTRA AÇÃO NO LOG
            $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
            $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                                    VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
            $stmt->execute(array(
              ':modulo'     => 'PROPOSTA - PARECER - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $last_id,
              ':dados'      => 'Código: ' . $prop_parecer_prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
              ':user_id'    => $_SESSION['reservm_admin_id'],
              ':user_nome'  => $_SESSION['reservm_admin_nome'],
              ':data'       => date('Y-m-d H:i:s')
            ));
            // -------------------------------

          } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
          }

          // MOVE AS IMAGENS PARA A PASTA
          $total_rowns = $stmt->rowCount();
          if ($total_rowns > 0) {
            $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_parecer_prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
          }
          // -------------------------------

        } else {
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Formato de arquivo inválido!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER'] . '#ancora_parecer'));
          return die;
        }
      }
    }

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER'] . '#ancora_parecer'));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não atualizados!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}





/*****************************************************************************************
                              EXCLUIR ARQUIVO PARECER
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_arq_parq") {

  $parq_id      = $_GET['parq_id'];
  $parq_arquivo = $_GET['parq_arquivo'];
  $parq_codigo  = $_GET['parq_codigo'];

  try {
    $sql = "DELETE FROM propostas_arq WHERE parq_id = :parq_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':parq_id', $parq_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      $apaga_img = unlink("../uploads/propostas/$parq_codigo/5/$parq_arquivo"); //APAGA O ARQUIVO ANTIGO

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'PROPOSTA - PARECER - IMAGENS',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $parq_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER'] . '#ancora_parecer'));
    } else {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER'] . '#ancora_parecer'));
    }
    // -------------------------------

  } catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
  }
  $conn = null;
}









/*****************************************************************************************
                              EXCLUIR PARECER
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "prop_parecer") {

  $prop_parecer_id = $_GET['prop_parecer_id'];
  $prop_codigo     = $_GET['prop_codigo'];

  try {
    $sql = "DELETE FROM propostas_analise_parecer WHERE prop_parecer_id = :prop_parecer_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':prop_parecer_id', $prop_parecer_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i>Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'PROPOSTA - PARECER',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $prop_parecer_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => date('Y-m-d H:i:s')
      ));
      // -------------------------------

      // EXCLUIR TODOS OS ARQUIVOS
      $sql = "DELETE FROM propostas_arq WHERE parq_codigo = :parq_codigo AND parq_categoria = 5";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':parq_codigo', $prop_codigo);
      $stmt->execute();

      // EXCLUIR ARQUIVOS PASTA 5
      $folderPath_5 = '../uploads/propostas/' . $prop_codigo . '/5';
      is_dir($folderPath_5);
      $files = glob($folderPath_5 . '/*');
      if ($files !== false) {
        foreach ($files as $file) {
          if (is_file($file)) {
            unlink($file); // EXCLUI CADA ARQUIVO NA PASTA
          }
        }
      }
      // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA
      rmdir($folderPath_5);
      ///

    } else {
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não excluídos!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Dados não excluídos!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  $conn = null;
}
