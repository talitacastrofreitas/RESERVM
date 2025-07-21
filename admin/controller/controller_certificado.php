<?php
session_start();
include '../../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// if ($_SERVER["REQUEST_METHOD"] === "POST") {}

/*****************************************************************************************
                          CADASTRAR CERTIFICADO
 *****************************************************************************************/
if (isset($dados['CadCertificado'])) {

  $cert_id                = md5(uniqid(rand(), true)); // GERA UM ID ÚNICO
  $cert_prop_id           = base64_decode($_POST['cert_prop_id']);
  $cert_categoria         = base64_decode($_POST['cert_categoria']);
  //
  $cert_prop_codigo       = trim($_POST['cert_prop_codigo']) !== '' ? trim($_POST['cert_prop_codigo']) : NULL;
  $cert_nome_comissao     = trim($_POST['cert_nome_comissao']) !== '' ? trim($_POST['cert_nome_comissao']) : NULL;
  $cert_texto             = trim($_POST['cert_texto']) !== '' ? trim($_POST['cert_texto']) : NULL;
  $cert_titulo_trabalho   = trim($_POST['cert_titulo_trabalho']) !== '' ? trim($_POST['cert_titulo_trabalho']) : NULL;
  $cert_area_tematica     = trim($_POST['cert_area_tematica']) !== '' ? trim($_POST['cert_area_tematica']) : NULL;
  $cert_autores           = trim($_POST['cert_autores']) !== '' ? trim($_POST['cert_autores']) : NULL;
  $cert_modalidade        = trim($_POST['cert_modalidade']) !== '' ? trim($_POST['cert_modalidade']) : NULL;
  $cert_conteudo_programa = trim($_POST['cert_conteudo_programa']) !== '' ? trim($_POST['cert_conteudo_programa']) : NULL;
  $cert_data_inicio       = trim($_POST['cert_data_inicio']) !== '' ? trim($_POST['cert_data_inicio']) : NULL;
  $cert_data_fim          = trim($_POST['cert_data_fim']) !== '' ? trim($_POST['cert_data_fim']) : NULL;
  $cert_carga             = trim($_POST['cert_carga']) !== '' ? trim($_POST['cert_carga']) : NULL;
  $cert_status            = trim(isset($_POST['cert_status'])) ? $_POST['cert_status'] : 0;
  $reservm_admin_id          = $_SESSION['reservm_admin_id'];
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO certificado (
                                      cert_id,
                                      cert_prop_id,
                                      cert_categoria,
                                      cert_nome_comissao,
                                      cert_texto,
                                      cert_titulo_trabalho,
                                      cert_area_tematica,
                                      cert_autores,
                                      cert_coautores,
                                      cert_modalidade,
                                      cert_conteudo_programa,
                                      cert_data_inicio,
                                      cert_data_fim,
                                      cert_carga,
                                      cert_status,
                                      cert_user_id,
                                      cert_data_cad,
                                      cert_data_upd
                                    ) VALUES (
                                      :cert_id,
                                      :cert_prop_id,
                                      :cert_categoria,
                                      :cert_nome_comissao,
                                      :cert_texto,
                                      :cert_titulo_trabalho,
                                      :cert_area_tematica,
                                      :cert_autores,
                                      :cert_coautores,
                                      :cert_modalidade,
                                      :cert_conteudo_programa,
                                      :cert_data_inicio,
                                      :cert_data_fim,
                                      :cert_carga,
                                      :cert_status,
                                      :cert_user_id,
                                      GETDATE(),
                                      GETDATE()
                                    )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cert_id' => $cert_id,
      ':cert_prop_id' => $cert_prop_id,
      ':cert_categoria' => $cert_categoria,
      ':cert_nome_comissao' => $cert_nome_comissao,
      ':cert_texto' => $cert_texto,
      ':cert_titulo_trabalho' => $cert_titulo_trabalho,
      ':cert_area_tematica' => $cert_area_tematica,
      ':cert_autores' => $cert_autores,
      ':cert_coautores' => $cert_coautores,
      ':cert_modalidade' => $cert_modalidade,
      ':cert_conteudo_programa' => $cert_conteudo_programa,
      ':cert_data_inicio' => $cert_data_inicio,
      ':cert_data_fim' => $cert_data_fim,
      ':cert_carga' => $cert_carga,
      ':cert_status' => $cert_status,
      ':cert_user_id' => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'CERTIFICADO',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $cert_id,
      ':dados'      =>
      'ID Proposta: ' . $cert_prop_id .
        '; Categoria: ' . $cert_categoria .
        '; Comissão: ' . $cert_nome_comissao .
        '; Texto: ' . $cert_texto .
        '; Título Trabalho: ' . $cert_titulo_trabalho .
        '; Área Temática: ' . $cert_area_tematica .
        '; Autores: ' . $cert_autores .
        '; Coautores: ' . $cert_coautores .
        '; Modalidade: ' . $cert_modalidade .
        '; Conteúdo Programa: ' . $cert_conteudo_programa .
        '; Data Início: ' . $cert_data_inicio .
        '; Data Fim: ' . $cert_data_fim .
        '; Carga Horária: ' . $cert_carga .
        '; Status: ' . $cert_status,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------

    // UPLOAD DE ARQUIVO
    if (!empty($_FILES['arquivo']['name'])) {
      if ($_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $nomeArquivo = date('YmdHis') . '_' . $_FILES['arquivo']['name']; // GERA O NOME DO ARQUVO
        $caminhoTemporario = $_FILES['arquivo']['tmp_name'];

        // CRIA UMA PASTA COM O NÚMERO DO CÓDIGO
        $dir = "../../uploads/certificado/$cert_prop_codigo";
        if (!is_dir($dir)) {
          mkdir($dir, 0777);
        }
        // -------------------------------

        // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
        $tamanhoMaximo = 2 * 1024 * 1024; // 2MB
        if ($_FILES['arquivo']['size'] >= $tamanhoMaximo) {
          $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
        // -------------------------------

        //FORMATO DE ARQUIVOS PERMITIDOS
        $extensoesPermitidas = ['jpg', 'JPG', 'jpeg', 'JPEG'];
        $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
        if (!in_array($extensao, $extensoesPermitidas)) {
          $_SESSION["erro"] = "O arquivo não está no formato permitido.";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
        // -------------------------------

        $sql = "INSERT INTO certificado_arquivo (
                                                  cert_arq_id,
                                                  cert_arq_prop_id,
                                                  cert_arq_categoria,
                                                  cert_arq_arquivo,
                                                  cert_arq_user_id,
                                                  cert_arq_data_cad
                                                  ) VALUES (
                                                  :cert_arq_id,
                                                  :cert_arq_prop_id,
                                                  :cert_arq_categoria,
                                                  :cert_arq_arquivo,
                                                  :cert_arq_user_id,
                                                  GETDATE()
                                                  )";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':cert_arq_id' => $cert_id,
          ':cert_arq_prop_id' => $cert_prop_id,
          ':cert_arq_categoria' => $cert_categoria,
          ':cert_arq_arquivo' => $nomeArquivo,
          ':cert_arq_user_id' => $reservm_admin_id
        ]);

        // MOVE AS IMAGENS PARA A PASTA
        $mover = move_uploaded_file($caminhoTemporario, $dir . '/' . $nomeArquivo);

        // REGISTRA AÇÃO NO LOG
        $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
        $stmt->execute([
          ':modulo'     => 'CERTIFICADO - IMAGEM',
          ':acao'       => 'CADASTRO',
          ':acao_id'    => $cert_id,
          ':dados'      =>
          'ID Proposta: ' . $cert_prop_id .
            '; Categoria: ' . $cert_categoria .
            '; Imagem: ' . $nomeArquivo,
          ':user_id'    => $reservm_admin_id
        ]);
      } else {
        $_SESSION["erro"] = "Erro no upload do arquivo!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      }
    }
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Certificado cadastrado com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}


















/*****************************************************************************************
                              EDITAR CERTIFICADO
 *****************************************************************************************/
if (isset($dados['EditCertificado'])) {

  $cert_id                = base64_decode($_POST['cert_id']);
  $cert_prop_id           = base64_decode($_POST['cert_prop_id']);
  $cert_categoria         = base64_decode($_POST['cert_categoria']);
  //
  $cert_prop_codigo       = trim($_POST['cert_prop_codigo']) !== '' ? trim($_POST['cert_prop_codigo']) : NULL;
  $cert_nome_comissao     = trim($_POST['cert_nome_comissao']) !== '' ? trim($_POST['cert_nome_comissao']) : NULL;
  $cert_texto             = trim($_POST['cert_texto']) !== '' ? trim($_POST['cert_texto']) : NULL;
  $cert_titulo_trabalho   = trim($_POST['cert_titulo_trabalho']) !== '' ? trim($_POST['cert_titulo_trabalho']) : NULL;
  $cert_area_tematica     = trim($_POST['cert_area_tematica']) !== '' ? trim($_POST['cert_area_tematica']) : NULL;
  $cert_autores           = trim($_POST['cert_autores']) !== '' ? trim($_POST['cert_autores']) : NULL;
  $cert_modalidade        = trim($_POST['cert_modalidade']) !== '' ? trim($_POST['cert_modalidade']) : NULL;
  $cert_conteudo_programa = trim($_POST['cert_conteudo_programa']) !== '' ? trim($_POST['cert_conteudo_programa']) : NULL;
  $cert_data_inicio       = trim($_POST['cert_data_inicio']) !== '' ? trim($_POST['cert_data_inicio']) : NULL;
  $cert_data_fim          = trim($_POST['cert_data_fim']) !== '' ? trim($_POST['cert_data_fim']) : NULL;
  $cert_carga             = trim($_POST['cert_carga']) !== '' ? trim($_POST['cert_carga']) : NULL;
  $cert_status            = trim(isset($_POST['cert_status'])) ? $_POST['cert_status'] : 0;
  $reservm_admin_id          = $_SESSION['reservm_admin_id'];
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                    certificado
              SET
                    cert_prop_id           = :cert_prop_id,
                    cert_categoria         = :cert_categoria,
                    cert_nome_comissao     = :cert_nome_comissao,
                    cert_texto             = :cert_texto,
                    cert_titulo_trabalho   = :cert_titulo_trabalho,
                    cert_area_tematica     = :cert_area_tematica,
                    cert_autores           = :cert_autores,
                    cert_coautores         = :cert_coautores,
                    cert_modalidade        = :cert_modalidade,
                    cert_conteudo_programa = :cert_conteudo_programa,
                    cert_data_inicio       = :cert_data_inicio,
                    cert_data_fim          = :cert_data_fim,
                    cert_carga             = :cert_carga,
                    cert_status            = :cert_status,
                    cert_user_id           = :cert_user_id,
                    cert_data_upd          = GETDATE()
              WHERE
                    cert_id = :cert_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':cert_id' => $cert_id,
      ':cert_prop_id' => $cert_prop_id,
      ':cert_categoria' => $cert_categoria,
      ':cert_nome_comissao' => $cert_nome_comissao,
      ':cert_texto' => $cert_texto,
      ':cert_titulo_trabalho' => $cert_titulo_trabalho,
      ':cert_area_tematica' => $cert_area_tematica,
      ':cert_autores' => $cert_autores,
      ':cert_coautores' => $cert_coautores,
      ':cert_modalidade' => $cert_modalidade,
      ':cert_conteudo_programa' => $cert_conteudo_programa,
      ':cert_data_inicio' => $cert_data_inicio,
      ':cert_data_fim' => $cert_data_fim,
      ':cert_carga' => $cert_carga,
      ':cert_status' => $cert_status,
      ':cert_user_id' => $reservm_admin_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'CERTIFICADO',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $cert_id,
      ':dados'      =>
      'ID Proposta: ' . $cert_prop_id .
        '; Categoria: ' . $cert_categoria .
        '; Comissão: ' . $cert_nome_comissao .
        '; Texto: ' . $cert_texto .
        '; Título Trabalho: ' . $cert_titulo_trabalho .
        '; Área Temática: ' . $cert_area_tematica .
        '; Autores: ' . $cert_autores .
        '; Coautores: ' . $cert_coautores .
        '; Modalidade: ' . $cert_modalidade .
        '; Conteúdo Programa: ' . $cert_conteudo_programa .
        '; Data Início: ' . $cert_data_inicio .
        '; Data Fim: ' . $cert_data_fim .
        '; Carga Horária: ' . $cert_carga .
        '; Status: ' . $cert_status,
      ':user_id'    => $reservm_admin_id
    ]);
    // -------------------------------


    // UPLOAD DE ARQUIVO
    if (!empty($_FILES['arquivo']['name'])) {
      if ($_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        $nomeArquivo = date('YmdHis') . '_' . $_FILES['arquivo']['name']; // GERA O NOME DO ARQUVO
        $caminhoTemporario = $_FILES['arquivo']['tmp_name'];

        // CRIA UMA PASTA COM O NÚMERO DO CÓDIGO
        $dir = "../../uploads/certificado/$cert_prop_codigo";
        if (!is_dir($dir)) {
          mkdir($dir, 0777);
        }
        // -------------------------------

        // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
        $tamanhoMaximo = 2 * 1024 * 1024; // 2MB
        if ($_FILES['arquivo']['size'] >= $tamanhoMaximo) {
          $_SESSION["erro"] = "O arquivo excede o tamanho máximo permitido de 2MB.";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
        // -------------------------------

        //FORMATO DE ARQUIVOS PERMITIDOS
        $extensoesPermitidas = ['jpg', 'JPG', 'jpeg', 'JPEG'];
        $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
        if (!in_array($extensao, $extensoesPermitidas)) {
          $_SESSION["erro"] = "O arquivo não está no formato permitido.";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
        // -------------------------------

        // IMPEDE CADASTRAR DUPLICADO
        $sql = "SELECT COUNT(*) FROM certificado_arquivo WHERE cert_arq_prop_id = :cert_arq_prop_id AND cert_arq_categoria = :cert_arq_categoria";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':cert_arq_prop_id' => $cert_arq_prop_id,
          ':cert_arq_categoria' => $cert_arq_categoria
        ]);
        if ($stmt->fetchColumn() > 0) {
          $_SESSION["erro"] = "Uma imagem já foi cadastrada para este certificado!";
          echo "<script> history.go(-1);</script>";
          return die;
        }
        // -------------------------------

        $sql = "INSERT INTO certificado_arquivo (
                                                  cert_arq_id,
                                                  cert_arq_prop_id,
                                                  cert_arq_categoria,
                                                  cert_arq_arquivo,
                                                  cert_arq_user_id,
                                                  cert_arq_data_cad
                                                  ) VALUES (
                                                  :cert_arq_id,
                                                  :cert_arq_prop_id,
                                                  :cert_arq_categoria,
                                                  :cert_arq_arquivo,
                                                  :cert_arq_user_id,
                                                  GETDATE()
                                                  )";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':cert_arq_id' => $cert_id,
          ':cert_arq_prop_id' => $cert_prop_id,
          ':cert_arq_categoria' => $cert_categoria,
          ':cert_arq_arquivo' => $nomeArquivo,
          ':cert_arq_user_id' => $reservm_admin_id
        ]);

        // MOVE AS IMAGENS PARA A PASTA
        $mover = move_uploaded_file($caminhoTemporario, $dir . '/' . $nomeArquivo);

        // REGISTRA AÇÃO NO LOG
        $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
        $stmt->execute([
          ':modulo'     => 'CERTIFICADO - IMAGEM',
          ':acao'       => 'CADASTRO',
          ':acao_id'    => $cert_id,
          ':dados'      =>
          'ID Proposta: ' . $cert_prop_id .
            '; Categoria: ' . $cert_categoria .
            '; Imagem: ' . $nomeArquivo,
          ':user_id'    => $reservm_admin_id
        ]);
      } else {
        $_SESSION["erro"] = "Erro no upload do arquivo!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      }
    }
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Certificado atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}















/*****************************************************************************************
                              EXCLUIR CERTIFICADO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_cert") {

  $cert_id          = $_GET['cert_id'];
  $prop_codigo      = $_GET['prop_codigo'];
  $cert_arq_arquivo = $_GET['cert_arq_arquivo'];
  $reservm_admin_id    = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM certificado WHERE cert_id = :cert_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cert_id' => $cert_id]);

    $sql_step2 = "DELETE FROM certificado_arquivo WHERE cert_arq_id = :cert_arq_id";
    $stmt_step2 = $conn->prepare($sql_step2);
    $stmt_step2->execute([':cert_arq_id' => $cert_id]);

    $apaga_img = unlink("../../uploads/certificado/$prop_codigo/$cert_arq_arquivo"); //APAGA O ARQUIVO ANTIGO

    // REGISTRA AÇÃO NO LOG DO CERTIFICADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'    => 'CERTIFICADO',
      ':acao'      => 'EXCLUSÃO',
      ':acao_id'   => $cert_id,
      ':user_id'   => $reservm_admin_id
    ]);
    // -------------------------------

    // REGISTRA AÇÃO NO LOG DA IMAGEM
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                            VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'    => 'CERTIFICADO - IMAGEM',
      ':acao'      => 'EXCLUSÃO',
      ':acao_id'   => $cert_id,
      ':user_id'   => $reservm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Dados excluídos com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo "Erro ao excluir registros: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar excluir os dados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}














/*****************************************************************************************
                                EXCLUIR IMAGEM CERTIFICADO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_img_cert") {

  $cert_arq_id      = $_GET['cert_arq_id'];
  $cert_arq_arquivo = $_GET['cert_arq_arquivo'];
  $prop_codigo      = $_GET['prop_codigo'];
  $reservm_admin_id    = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "DELETE FROM certificado_arquivo WHERE cert_arq_id = :cert_arq_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':cert_arq_id' => $cert_arq_id]);

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {

      $apaga_img = unlink("../../uploads/certificado/$prop_codigo/$cert_arq_arquivo"); //APAGA O ARQUIVO ANTIGO

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id,  log_data )
                              VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
      $stmt->execute([
        ':modulo'    => 'CERTIFICADO - IMAGEM',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $cert_arq_id,
        ':user_id'   => $reservm_admin_id
      ]);
      // -------------------------------

      $_SESSION["msg"] = "Dados excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    } else {
      $_SESSION["erro"] = "Dados não excluídos!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    }
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["msg"] = "Dados excluídos!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
  $conn = null;
}
