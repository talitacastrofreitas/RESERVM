<?php
// session_start();
include '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
  try {

    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao']; // AÇÃO: CADASTRAR, ATUALIZAR, DELETAR

    // SE CADASTRAR OU ATUALIZAR
    if ($acao === 'cadastrar' || $acao === 'atualizar') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (empty($_POST['esp_codigo']) || empty($_POST['esp_nome_local']) || empty($_POST['esp_nome_local_resumido']) || empty($_POST['esp_tipo_espaco'])) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $esp_id                  = bin2hex(random_bytes(16)); // GERA UM ID DE 32 CARACTERES HEXADECIMAIS
      $esp_codigo              = trim($_POST['esp_codigo']);
      $esp_nome_local          = trim($_POST['esp_nome_local']);
      $esp_nome_local_resumido = trim($_POST['esp_nome_local_resumido']);
      $esp_tipo_espaco         = trim($_POST['esp_tipo_espaco']);
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
      $esp_status             = $_POST['esp_status'] === '1' ? 1 : 0;
    }
    $rvm_admin_id = $_SESSION['reservm_admin_id'];


    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) as total FROM espaco WHERE esp_codigo = :esp_codigo OR (esp_nome_local = :esp_nome_local AND esp_unidade = :esp_unidade)";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':esp_codigo' => $esp_codigo, ':esp_nome_local' => $esp_nome_local, ':esp_unidade' => $esp_unidade]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Código já cadastrado ou nome do local já existe para esta unidade!");
      }

      $sql = "INSERT INTO espaco (
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
                                )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
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




      // -------------------------------
      // ATUALIZAR
      // -------------------------------
    } elseif ($acao === 'atualizar') {

      if (empty($_POST['esp_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $esp_id = $_POST['esp_id'];
      $log_acao = 'Atualização';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) as total FROM espaco WHERE esp_codigo = :esp_codigo OR (esp_nome_local = :esp_nome_local AND esp_unidade = :esp_unidade) AND esp_id != :esp_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':esp_codigo' => $esp_codigo, ':esp_nome_local' => $esp_nome_local, ':esp_unidade' => $esp_unidade, ':esp_id' => $esp_id]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Código já cadastrado ou nome do local já existe para esta unidade!");
      }

      $sql = "UPDATE espaco SET 
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
                                esp_id = :esp_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
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




      // -------------------------------
      // EXCLUIR
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      if (empty($_GET['esp_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $esp_id = $_GET['esp_id'];
      $log_acao = 'Exclusão';

      // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
      $sql = $conn->prepare("SELECT COUNT(*) FROM reservas WHERE CHARINDEX(:res_espaco_id, res_espaco_id) > 0");
      $sql->execute([':res_espaco_id' => $esp_id]);
      $existe = $sql->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Este registro não pode ser excluído!");
      }
      // -------------------------------
      $sql = $conn->prepare("SELECT COUNT(*) FROM solicitacao WHERE ',' + REPLACE(solic_ap_espaco, ' ', '') + ',' LIKE ?");
      $sql->execute(['%,' . $esp_id . ',%']);
      $existe = $sql->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Este registro não pode ser excluído!");
      }
      // -------------------------------

      $sql = "DELETE FROM espaco WHERE esp_id = :esp_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':esp_id' => $esp_id]);




      // -------------------------------
      // AÇÃO INVÁLIDA
      // -------------------------------
    } else {
      throw new Exception("Ação inválida.");
    }

    // REGISTRA NO LOG
    $log_dados = ['POST' => $_POST, 'GET' => $_GET, 'FILES' => $_FILES];
    $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
              VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
    $stmtLog = $conn->prepare($sqlLog);
    $stmtLog->execute([
      ':modulo'  => 'ESPAÇO',
      ':acao'    => $log_acao,
      ':acao_id' => $esp_id,
      ':dados'   => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $rvm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    if ($acao === 'cadastrar') {
      $_SESSION["msg"] = "Dados cadastrados com sucesso!";
    } elseif ($acao === 'atualizar') {
      $_SESSION["msg"] = "Dados atualizados com sucesso!";
    } else {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
    }
    // -------------------------------
    header("Location: ../admin/espacos.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    $_SESSION["form_esp"] = $_POST; // CRIA SESSÃO PARA OS DADOS DO FORMULÁRIO QUE FORAM ENVIADOS
    header("Location: ../admin/espacos.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/espacos.php");
  exit;
}
