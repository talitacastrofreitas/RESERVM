<?php
session_start();
ob_start(); // Limpa o buff de saida
include '../conexao/conexao.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
if (isset($dados['LoginUser'])) {

  $user_email = $dados['user_email'];
  $query_user = "SELECT * FROM usuarios WHERE user_email = :user_email";
  $result_user = $conn->prepare($query_user);
  $result_user->bindParam(':user_email', $user_email); // -> FORMA PARA QUE SEJA UMA STRING, MAS NÃO É NECESSÁRIO POR
  $result_user->execute();

  if (($result_user) and ($result_user->rowCount() != 0)) {
    $row_user = $result_user->fetch(PDO::FETCH_ASSOC);

    if ($row_user['user_status'] == 1) { // SE O STATUS DO USUÁRIO FOR 'INATIVO (0)', NÃO PODE LOGAR

      /*******************************************************
        SE LOGIN E SENHA ESTIVEREM CORRETOS, ENTRA NO SISTEMA
       *******************************************************/
      if ($user_email == $row_user['user_email'] && password_verify($dados['user_senha'], $row_user['user_senha']) == 1) {
        $_SESSION['reservm_admin_id']              = $row_user['id'];
        $_SESSION['brasileiro']      = $row_user['user_brasileiro'];
        $_SESSION['reservm_admin_nome']            = $row_user['user_nome'];
        $_SESSION['cpf']             = $row_user['user_cpf'];
        $_SESSION['passaporte']      = $row_user['user_passaporte'];
        $_SESSION['nacionalidade']   = $row_user['user_nacionalidade'];
        $_SESSION['email']           = $row_user['user_email'];
        $_SESSION['data_nascimento'] = $row_user['user_data_nascimento'];
        $_SESSION['vinculo']         = $row_user['user_vinculo'];
        $_SESSION['nivel_acesso']    = $row_user['nivel_acesso'];
        $_SESSION['termos']          = $row_user['user_termos'];
        $_SESSION['status']          = $row_user['user_status'];
        $_SESSION['cad']             = $row_user['user_cad'];
        $_SESSION['data_cad']        = $row_user['user_data_cad'];
        $_SESSION['data_upd']        = $row_user['user_data_upd'];
        $_SESSION['perfil']          = $row_user['user_perfil'];
        header("Location: ../painel.php");

        // REGISTRA AÇÃO NO LOG
        $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data ) VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
        $stmt->execute(array(
          ':modulo'    => 'USUÁRIOS',
          ':acao'      => 'ACESSO',
          ':acao_id'   => $_SESSION['reservm_admin_id'],
          ':user_id'   => $_SESSION['reservm_admin_id'],
          ':user_nome' => $_SESSION['reservm_admin_nome'],
          ':data'      => date('Y-m-d H:i:s')
        ));
        // -------------------------------

        // CADASTRA DA DATA QUE O USUÁRIO LOGA NO SISTEMA
        $id = $row_user['id'];
        try {
          $stmt = $conn->prepare('UPDATE usuarios SET user_data_acesso = :data_upd WHERE id = :id');
          $stmt->execute(array(
            ':id'       => $id,
            ':data_upd' => date('Y-m-d H:i:s')
          ));
          echo $stmt->rowCount();
        } catch (PDOException $e) {
          echo 'Error: ' . $e->getMessage();
        }
      } else {
        $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Dados incorretos!";
        header("Location: ../index.php");
        return die;
      }
    } else {
      $_SESSION["rest"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Acesso restrito!";
      header("Location: ../index.php");
    }
  } else {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Dados incorretos!";
    header("Location: ../index.php");
    return die;
  }
}
