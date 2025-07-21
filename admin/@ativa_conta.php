<?php
session_start();
include 'conexao/conexao.php';

$id_cript = $_GET['cod']; // ID DO USUÁRIO CRIPTOGRAFADO
$codigo   = $_GET['p']; // TOKEN

$user_id = base64_decode($id_cript); // DECODIFICA O ID DO USUÁRIO

// VERIFICA SE O TOKEN EXISTE. SE NÃO, IMPEDE O ACESSO PELO LINK E PEDE QUE O USUÁRIO LOGUE COM SUAS CREDENCIAIS
$sql = "SELECT COUNT(*) FROM token WHERE codigo = :codigo";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":codigo", $codigo);
$stmt->execute();
if ($stmt->fetchColumn() < 1) {
  $_SESSION["erro"] = "O link já foi utilizado para ativar esta conta!";
  header("Location: $url_sistema");
  return die;
}


// VERIFICA SE A DATA DO TOKEN NÃO EXPIROU
// $data  = date('Y-m-d H:i:s');
// $query = "SELECT * FROM token WHERE codigo = '$codigo' AND data > '$data'";
// $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
// $stmt->execute();
// $row_count = $stmt->rowCount();
// if (empty($row_count)) {
//   header("Location: ativa_conta-link-exp.php");
//   return die;
// }

// ATUALIZADO O STATUS DO USUARIO PARA "ATIVADO" = 1
try {
  $stmt = $conn->prepare('UPDATE usuarios SET user_status = :user_status, user_data_acesso = :user_data_acesso WHERE user_id = :user_id'); // REALIZA A ATUALIZAÇÃO
  $stmt->execute(array(
    ':user_id'          => $user_id, // IDO DO USUÁRIOS
    ':user_status'      => 1, // VALOR ENVIANDO
    ':user_data_acesso' => date('Y-m-d H:i:s') // DATA DO ACESSO
  ));
  echo $stmt->rowCount();
} catch (PDOException $e) {
  echo 'Error: ' . $e->getMessage();
}

// SELECIONA OS DADOS DO USUÁRIO E CRIA AS SESSÕES
$sql = "SELECT * FROM usuarios WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
// Ou use $dados = $stmt->fetchAll(PDO::FETCH_ASSOC); se você esperar mais de um resultado

// ENVIA OS DADOS PARA AS SESSÕES
if ($result && $result['user_status'] == 1) {
  $_SESSION['user_id']         = $result['user_id'];
  $_SESSION['brasileiro']      = $result['user_brasileiro'];
  $_SESSION['reservm_admin_nome']            = $result['user_nome'];
  $_SESSION['cpf']             = $result['user_cpf'];
  $_SESSION['passaporte']      = $result['user_passaporte'];
  $_SESSION['email']           = $result['user_email'];
  $_SESSION['data_nascimento'] = $result['user_data_nascimento'];
  $_SESSION['vinculo']         = $result['user_vinculo'];
  $_SESSION['nivel_acesso']    = $result['nivel_acesso'];
  $_SESSION['termos']          = $result['user_termos'];
  $_SESSION['status']          = $result['user_status'];
  $_SESSION['cad']             = $result['user_cad'];
  $_SESSION['data_cad']        = $result['user_data_cad'];
  $_SESSION['data_upd']        = $result['user_data_upd'];

  // DELETA O TOKEN APÓS A ATIVAÇÃO DA CONTA
  $sql = "DELETE FROM token WHERE codigo = '$codigo'";
  $conn->exec($sql);

  // REGISTRA AÇÃO NO LOG
  $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data ) VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
  $stmt->execute(array(
    ':modulo'    => 'USUÁRIOS',
    ':acao'      => 'PRIMEIRO ACESSO',
    ':acao_id'   => $user_id,
    ':user_id'   => $user_id,
    ':user_nome' => $_SESSION['reservm_admin_nome'],
    ':data'      => date('Y-m-d H:i:s')
  ));

  header("Location: painel.php"); // SE TUDO ESTIVER CORRETO ENCAMINHA O USUÁRIO PARA O DASHBOARD
} else {
  header("Location: sair.php"); // SE ENCONTRAR ALGUM ERRO, IMPEDE O ACESSO AO SISTEMA
}
