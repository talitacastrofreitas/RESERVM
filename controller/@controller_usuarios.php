<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                                    CADASTRAR USUÁRIO
 *****************************************************************************************/
// if (isset($dados['RegUsuario'])) {
if (isset($_GET['funcao']) && $_GET['funcao'] == "reg_user") {

  $user_id           = md5(uniqid(rand(), true)); // GERA UM ID UNICO
  $user_nome         = trim($_POST['user_nome']) !== '' ? trim($_POST['user_nome']) : NULL;
  $user_brasileiro   = trim($_POST['user_brasileiro']) !== '' ? trim($_POST['user_brasileiro']) : 0; // BRASILEIRO = 1
  //
  if ($_POST['user_cpf']) {
    $cpfDot          = trim($_POST['user_cpf']) !== '' ? trim($_POST['user_cpf']) : NULL;
    $user_doc        = str_replace(['.', '-'], '', $cpfDot); // RETIRA PONTOS E TRAÇOS
  } else {
    $user_doc        = trim($_POST['user_pass']) !== '' ? trim($_POST['user_pass']) : NULL;
  }
  //
  $user_email        = trim($_POST['user_email']) !== '' ? trim($_POST['user_email']) : NULL;
  $user_perfil       = trim($_POST['user_perfil']) !== '' ? trim($_POST['user_perfil']) : NULL;
  $user_outro_perfil = trim($_POST['user_outro_perfil']) !== '' ? trim($_POST['user_outro_perfil']) : NULL;
  $nivel_acesso      = 2; // NÍVEL DE ACESSO DO USUÁRIO
  $user_status       = 1;

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM usuarios WHERE user_email = :user_email OR user_doc = :user_doc";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':user_email' => $user_email,
    ':user_doc' => $user_doc
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Falha ao cadastrar os dados: E-mail ou documento já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  // CADASTRA O USUÁRIO
  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "INSERT INTO usuarios (
                                    user_id,
                                    user_brasileiro,
                                    user_nome,
                                    user_doc,
                                    user_email,
                                    user_perfil,
                                    user_outro_perfil,
                                    nivel_acesso,
                                    user_status,
                                    user_user_id,
                                    user_data_cad,
                                    user_data_upd
                                  ) VALUES (
                                    :user_id,
                                    :user_brasileiro,
                                    UPPER(:user_nome),
                                    UPPER(:user_doc),
                                    LOWER(:user_email),
                                    :user_perfil,
                                    UPPER(:user_outro_perfil),
                                    :nivel_acesso,
                                    :user_status,
                                    :user_user_id,
                                    GETDATE(),
                                    GETDATE()
                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':user_id' => $user_id,
      ':user_brasileiro' => $user_brasileiro,
      ':user_nome' => $user_nome,
      ':user_doc' => $user_doc,
      ':user_email' => $user_email,
      ':user_perfil' => $user_perfil,
      ':user_outro_perfil' => $user_outro_perfil,
      ':nivel_acesso' => $nivel_acesso,
      ':user_status' => $user_status,
      ':user_user_id' => $user_id
    ]);

    // CRIA O TOKEN DE VALIDAÇÃO
    $codigo      = str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT); // GERA UM CÓDIGO DE 7 DÍGITOS
    $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT); // CRIPTOGRAFA O CÓDIGO

    // CADASTRA O TOKEN PARA VALIDAÇÃO DO LINK DO EMAIL

    $stmt = $conn->prepare('INSERT INTO token (
                                                tok_codigo,
                                                tok_user_id,
                                                tok_data_valida
                                              ) VALUES (
                                                :tok_codigo,
                                                :tok_user_id,
                                                DATEADD(day, 1, GETDATE()) -- ADICIONA 1 DIA A DATA ATUAL
                                              )');
    $stmt->execute([
      ':tok_codigo'      => $codigo_hash,
      ':tok_user_id'     => $user_id
    ]);
    // -------------------------------

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'    => 'USUÁRIOS',
      ':acao'      => 'CADASTRO',
      ':acao_id'   => $user_id,
      ':dados'     =>
      'Brasileiro: ' . $user_brasileiro .
        '; Nome: ' . $user_nome .
        '; E-mail: ' . $user_email .
        '; Perfil: ' . $user_perfil .
        '; Outro Perfil: ' . $user_outro_perfil .
        '; Documento: ' . $user_doc .
        '; Vinculo: ' . $user_vinculo .
        '; Nível Acesso: ' . $nivel_acesso .
        '; Status: ' . $user_status,
      ':user_id'   => $user_id
    ]);
    // -------------------------------

    // DISPARA E-MAIL
    $mail = new PHPMailer(true);
    include '../controller/email_conf.php';
    $mail->addAddress($user_email, 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
    $mail->isHTML(true);
    $mail->Subject = 'Seu código de acesso é ' . $codigo; //TÍTULO DO E-MAIL

    // DADOS DO LINK
    $user_id_cript = base64_encode($user_id); // CRIPTOGRAFA ID
    // -------------------------------

    // CORPO DO EMAIL
    include '../includes/email/email_header.php';
    $email_conteudo .= "
      <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
        <td style='padding: 2em 2rem; display: inline-block;'>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Olá,
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Seu código do RESERVM chegou!
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Ao copiá-lo vá à página de acesso e insira o código abaixo para confirmar sua identidade.
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 50px 0px;'>
        Lembrando que essa etapa é muito importante para mantermos a segurança dos seus dados e cumprirmos nosso compromisso com você.
        </p>

        <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 15px 0px;'>
        Seu código de acesso é:
        </p>

        <span style='color: #285FAB; font-size: 2rem; letter-spacing: 0.20rem; font-weight: 600; margin-bottom: 20px; display: inline-block;'>$codigo</span><br>
        <a style='cursor: pointer;' href='$url_sistema/us-validcod.php?us-ident=$user_id_cript'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema e valide seu código.</button></a>
        </td>
      </tr>";
    include '../includes/email/email_footer.php';

    $mail->Body  = $email_conteudo;
    $mail->send();
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    header("Location: $url_sistema/us-validcod.php?us-ident=$user_id_cript");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Cadastro não realizado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}


















/*****************************************************************************************
                                    EDITAR USUÁRIO
 *****************************************************************************************/
if (isset($dados['EditUsuario'])) {

  $user_id                   = base64_decode($_POST['user_id']);
  //
  $user_nome                 = trim($_POST['user_nome']) !== '' ? trim($_POST['user_nome']) : NULL;
  $user_nome_social          = trim($_POST['user_nome_social']) !== '' ? trim($_POST['user_nome_social']) : NULL;
  // CPF/PASSAPORTE
  if ($_POST['user_cpf']) {
    $cpfDot                  = trim($_POST['user_cpf']) !== '' ? trim($_POST['user_cpf']) : NULL;
    $user_doc                = str_replace(['.', '-'], '', $cpfDot); // RETIRA PONTOS E TRAÇOS
  } else {
    $user_doc                = trim($_POST['user_pass']) !== '' ? trim($_POST['user_pass']) : NULL;
  }
  //
  $user_nacionalidade        = trim($_POST['user_nacionalidade']) !== '' ? trim($_POST['user_nacionalidade']) : NULL;
  $user_email                = trim($_POST['user_email']) !== '' ? trim($_POST['user_email']) : NULL;
  $user_data_nascimento      = trim($_POST['user_data_nascimento']) !== '' ? trim($_POST['user_data_nascimento']) : NULL;
  $user_rg                   = trim($_POST['user_rg']) !== '' ? trim($_POST['user_rg']) : NULL;
  $user_genero               = trim($_POST['user_genero']) !== '' ? trim($_POST['user_genero']) : NULL;
  $user_raca                 = trim($_POST['user_raca']) !== '' ? trim($_POST['user_raca']) : NULL;
  $user_contato              = trim($_POST['user_contato']) !== '' ? trim($_POST['user_contato']) : NULL;
  // PERFIL
  $user_perfil               = trim($_POST['user_perfil']) !== '' ? trim($_POST['user_perfil']) : NULL;
  if ($user_perfil == 9) {
    $user_outro_perfil       = trim($_POST['user_outro_perfil']);
  } else {
    $user_outro_perfil       = NULL;
  }
  //
  $user_vinculo              = trim($_POST['user_vinculo']) !== '' ? trim($_POST['user_vinculo']) : 0;
  // EDNDEREÇO
  $user_cep                  = trim($_POST['user_cep']) !== '' ? trim($_POST['user_cep']) : NULL;
  $user_rua                  = trim($_POST['user_rua']) !== '' ? trim($_POST['user_rua']) : NULL;
  $user_numero               = trim($_POST['user_numero']) !== '' ? trim($_POST['user_numero']) : NULL;
  $user_bairro               = trim($_POST['user_bairro']) !== '' ? trim($_POST['user_bairro']) : NULL;
  $user_municipio            = trim($_POST['user_municipio']) !== '' ? trim($_POST['user_municipio']) : NULL;
  $user_estado               = trim($_POST['user_estado']) !== '' ? trim($_POST['user_estado']) : NULL;
  $user_endereco             = trim($_POST['user_endereco']) !== '' ? nl2br(trim($_POST['user_endereco'])) : NULL;
  // ESCOLARIDADE
  $user_escolaridade         = trim($_POST['user_escolaridade']) !== '' ? trim($_POST['user_escolaridade']) : NULL;
  $user_instituicao_ensino   = trim($_POST['user_instituicao_ensino']) !== '' ? trim($_POST['user_instituicao_ensino']) : NULL;
  $user_lattes               = trim($_POST['user_lattes']) !== '' ? trim($_POST['user_lattes']) : NULL;
  // REDES SOCIAIS
  $user_facebook             = trim($_POST['user_facebook']) !== '' ? trim($_POST['user_facebook']) : NULL;
  $user_instagram            = trim($_POST['user_instagram']) !== '' ? trim($_POST['user_instagram']) : NULL;
  $user_linkedin             = trim($_POST['user_linkedin']) !== '' ? trim($_POST['user_linkedin']) : NULL;
  //
  $user_vinculo_atividade    = trim($_POST['user_vinculo_atividade']) !== '' ? trim($_POST['user_vinculo_atividade']) : 0;
  $user_receber_notificacoes = trim($_POST['user_receber_notificacoes']) !== '' ? trim($_POST['user_receber_notificacoes']) : 0;
  $reservm_user_id              = $_SESSION['reservm_user_id'];

  /* -------------------------------
      IMPEDE CADASTRO DUPLICADO
  ------------------------------- */
  $sql = "SELECT COUNT(*) FROM usuarios WHERE user_email = :user_email AND user_id != :user_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':user_email' => $user_email,
    ':user_id' => $user_id
  ]);
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este e-mail já foi cadastrado!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
  // -------------------------------

  if ($_POST['user_cpf']) {
    $sql = "SELECT COUNT(*) FROM usuarios WHERE user_doc = :user_doc AND user_doc != '' AND user_id != :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':user_doc' => $user_doc,
      ':user_id' => $user_id
    ]);
    if ($stmt->fetchColumn() > 0) {
      $_SESSION["erro"] = "Este CPF já foi cadastrado!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }
  }
  // -------------------------------

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $sql = "UPDATE
                  usuarios
              SET        
                  user_nome                 = UPPER(:user_nome),
                  user_nome_social          = UPPER(:user_nome_social),
                  user_doc                  = :user_doc,
                  user_nacionalidade        = :user_nacionalidade,
                  user_email                = LOWER(:user_email),
                  user_data_nascimento      = :user_data_nascimento,
                  user_vinculo              = :user_vinculo,
                  user_rg                   = :user_rg,
                  user_genero               = :user_genero,
                  user_raca                 = :user_raca,
                  user_contato              = :user_contato,
                  user_perfil               = :user_perfil,
                  user_outro_perfil         = UPPER(:user_outro_perfil),
                  user_cep                  = :user_cep,
                  user_rua                  = UPPER(:user_rua),
                  user_numero               = :user_numero,
                  user_bairro               = UPPER(:user_bairro),
                  user_municipio            = UPPER(:user_municipio),
                  user_estado               = UPPER(:user_estado),
                  user_endereco             = UPPER(:user_endereco),
                  user_escolaridade         = :user_escolaridade,
                  user_instituicao_ensino   = UPPER(:user_instituicao_ensino),
                  user_lattes               = :user_lattes,
                  user_facebook             = :user_facebook,
                  user_instagram            = :user_instagram,
                  user_linkedin             = :user_linkedin,
                  user_vinculo_atividade    = :user_vinculo_atividade,
                  user_receber_notificacoes = :user_receber_notificacoes,
                  user_data_upd             = GETDATE()
            WHERE
                  user_id = :user_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':user_id' => $user_id,
      ':user_nome' => $user_nome,
      ':user_nome_social' => $user_nome_social,
      ':user_doc' => $user_doc,
      ':user_nacionalidade' => $user_nacionalidade,
      ':user_email' => $user_email,
      ':user_data_nascimento' => $user_data_nascimento,
      ':user_vinculo' => $user_vinculo,
      ':user_rg' => $user_rg,
      ':user_genero' => $user_genero,
      ':user_raca' => $user_raca,
      ':user_contato' => $user_contato,
      ':user_perfil' => $user_perfil,
      ':user_outro_perfil' => $user_outro_perfil,
      ':user_cep' => $user_cep,
      ':user_rua' => $user_rua,
      ':user_numero' => $user_numero,
      ':user_bairro' => $user_bairro,
      ':user_municipio' => $user_municipio,
      ':user_estado' => $user_estado,
      ':user_endereco' => $user_endereco,
      ':user_escolaridade' => $user_escolaridade,
      ':user_instituicao_ensino' => $user_instituicao_ensino,
      ':user_lattes' => $user_lattes,
      ':user_facebook' => $user_facebook,
      ':user_instagram' => $user_instagram,
      ':user_linkedin' => $user_linkedin,
      ':user_vinculo_atividade' => $user_vinculo_atividade,
      ':user_receber_notificacoes' => $user_receber_notificacoes
    ]);


    // CADASTRAR ARQUIVO
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === 0) {
      $diretorio = '../uploads/usuarios/' . $user_id; // CAMINHO DO ARQUIVO
      $extensao = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION); // OBTER EXTENSÃO DO ARQUIVO
      //$novoNome = time() . '_' . $_FILES['arquivo']['name'];
      $novoNome = time() . '_curriculo.' . $extensao; // GERA UM NOVO NOME PARA O ARQUIVO

      if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
      }

      $caminhoCompleto = $diretorio . '/' . $novoNome;

      if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminhoCompleto)) {
        //echo 'Arquivo enviado e renomeado com sucesso.';
      } else {
        $_SESSION["erro"] = "Erro ao mover o arquivo para o diretório de destino!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        return die;
      }

      $sql = "INSERT INTO usuarios_arq (arq_arquivo, arq_user_id, arq_data_upd) VALUES (:arq_arquivo, :arq_user_id, GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':arq_arquivo' => $novoNome,
        ':arq_user_id' => $user_id
      ]);

      if ($stmt->rowCount() > 0) {
        //echo 'Registro inserido no banco de dados com sucesso.';

        // REGISTRA AÇÃO NO LOG
        $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                                VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
        $stmt->execute([
          ':modulo'     => 'USUÁRIOS - ARQUIVO',
          ':acao'       => 'ATUALIZAÇÃO',
          ':acao_id'    => $user_id,
          ':dados'      => 'Arquivo: ' . $novoNome,
          ':user_id'    => $user_id,
        ]);
        // -------------------------------

      } else {
        $_SESSION["erro"] = "Erro ao inserir o registro no banco de dados!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        return die;
      }
    }
    // -------------------------------

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'USUÁRIOS',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $user_id,
      ':dados'      => 'Nome: ' . $user_nome .
        '; Nome Social: ' . $user_nome_social .
        '; CPF/Passaporte: ' . $user_doc .
        '; Nacionalidade: ' . $user_nacionalidade .
        '; E-mail: ' . $user_email .
        '; Data Nascimento: ' . $user_data_nascimento .
        '; Vinculo: ' . $user_vinculo .
        '; RG: ' . $user_rg .
        '; Genero: ' . $user_genero .
        '; Raca: ' . $user_raca .
        '; Contato: ' . $user_contato .
        '; Perfil: ' . $user_perfil .
        '; Outro Perfil: ' . $user_outro_perfil .
        '; CEP: ' . $user_cep .
        '; Rua: ' . $user_rua .
        '; Numero: ' . $user_numero .
        '; Bairro: ' . $user_bairro .
        '; Cidade: ' . $user_municipio .
        '; Estado: ' . $user_estado .
        '; Endereco: ' . $user_endereco .
        '; Escolaridade: ' . $user_escolaridade .
        '; Instituicao Ensino: ' . $user_instituicao_ensino .
        '; Lattes: ' . $user_lattes .
        '; Facebook: ' . $user_facebook .
        '; Instagram: ' . $user_instagram .
        '; Linkedin: ' . $user_linkedin .
        '; Vinculo Atividade: ' . $user_vinculo_atividade .
        '; Receber Notificacao: ' . $user_receber_notificacoes,
      ':user_id'    => $user_id,
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

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














/*****************************************************************************************
                          EXCLUIR ARQUIVO DO PERFIL DO USUÁRIO
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_arq") {

  $arq_id       = $_GET["arq_id"];
  $arquivo      = $_GET["arq"];
  $reservm_user_id = $_SESSION['reservm_user_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    unlink('../uploads/usuarios/' . $reservm_user_id . '/' . $arquivo); // EXCLUI O ARQUIVO DA PASTA

    $stmt = $conn->prepare("DELETE FROM usuarios_arq WHERE arq_id = :arq_id");
    $stmt->execute([
      ':arq_id' => $arq_id
    ]);

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'USUÁRIOS - ARQUIVO',
      ':acao'       => 'EXCLUSÃO',
      ':acao_id'    => $arq_id,
      ':user_id'    => $reservm_user_id,
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "Arquivo excluído!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo "Erro ao excluir o registro: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao excluir o arquivo!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}













/*****************************************************************************************
                            VALIDA CÓDIGO PARA CRIAR SENHA
 *****************************************************************************************/
// if (isset($dados['ValCodReg'])) {

//   try {
//     $user_id = base64_decode($dados['cod']); // DECODIFICA O ID DO USUÁRIO
//     $query_tok = "SELECT * FROM token
//                   INNER JOIN usuarios ON usuarios.user_id = token.tok_user_id
//                   WHERE tok_user_id = :tok_user_id";
//     $result_tok = $conn->prepare($query_tok);
//     $result_tok->bindParam(':tok_user_id', $user_id);
//     $result_tok->execute();
//     $row_tok = $result_tok->fetch(PDO::FETCH_ASSOC);

//     $val_token = $conn->query("SELECT COUNT(*) FROM token WHERE tok_user_id = '$user_id'")->fetchColumn(); // VERIFICA SE O ID EXISTE NA TABELA

//     // SE TOKEN EXISTIR E O TOKEN FOR DO ID INFORMADO
//     if ((!empty($row_tok)) && ($val_token != 0)) {

//       // O TOKEN TEM VALIDADE DE 24 HORAS
//       $dataReal    = new DateTime(); // DATA EM TEMPO REAL
//       $data_valida = new DateTime($row_tok['tok_data_valida']); // DATA DE VALIDADE DO TOKEN

//       // SE A DATA DE VALIDADE DO TOKEN FOR MAIOR QUE A DATA REAL, SEGUE A VALIDAÇÃO
//       if ($data_valida >= $dataReal) {

//         $tok_codigo = $dados['cod1'] . $dados['cod2'] . $dados['cod3'] . $dados['cod4'] . $dados['cod5'] . $dados['cod6'] . $dados['cod7'];

//         // SE O CÓDIGO ESTIVER CORRETO, SEGUE PARA FORMULÁRIO DE CRIAÇÃO DE SENHA
//         if (password_verify($tok_codigo, $row_tok['tok_codigo']) == 1) {
//           header("Location: ../ad-creatpass.php?ad-creat-pass=" . $dados['cod']);

//           /* -------------------------------
//             REGISTRA AÇÃO NO LOG
//           ------------------------------- */
//           $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
//                             VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
//           $stmt->execute(array(
//             ':modulo'     => 'AUTENTICAÇÃO',
//             ':acao'       => 'VALIDA TOKEN',
//             ':acao_id'    => $user_id,
//             ':user_id'    => $user_id
//           ));
//           // -------------------------------

//         } else {
//           $_SESSION["erro"] = "Código inválido!";
//           header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//           return die;
//         }
//       } else {

//         // APÓS ATUALIZAR A SENHA, O TOKEN REFERENTE AO USUÁRIO É EXCLUÍDO
//         $sql = "DELETE FROM token WHERE tok_user_id = '$user_id'";
//         $conn->exec($sql);
//         // -------------------------------

//         $_SESSION["erro"] = "O código informado expirou!";
//         header("Location: " . $url_sistema);
//       }
//     } else {
//       $_SESSION["erro"] = "Código inválido!";
//       header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//     }
//   } catch (PDOException $e) {
//     // echo "Erro: " . $e->getMessage();
//     $_SESSION["erro"] = "Não foi possível validar o código!";
//     header("Location: " . $url_sistema);
//   }
// }







/*****************************************************************************************
                      CRIA A SENHA DO USUÁRIO
 *****************************************************************************************/
// if (isset($_GET['funcao']) && $_GET['funcao'] == "alterSenhaReg") {

//   if (!empty($_POST['cod'])) {
//     $user_id    = base64_decode($_POST['cod']); // DECOFICIA O ID DO USUÁRIO
//     $user_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA

//     // EDITA SENHA 
//     try {
//       $sql = "UPDATE
//                     usuarios
//               SET
//                     user_senha            = :user_senha,
//                     user_data_reset_senha = GETDATE()
//               WHERE
//                     user_id = :user_id";

//       $stmt = $conn->prepare($sql);
//       $stmt->bindParam(":user_id", $user_id);
//       $stmt->bindParam(":user_senha", $user_senha);
//       $stmt->execute();

//       // APÓS ATUALIZAR A SENHA, O TOKEN REFERENTE AO USUÁRIO É EXCLUÍDO
//       $sql = "DELETE FROM token WHERE tok_user_id = '$user_id'";
//       $conn->exec($sql);
//       // -------------------------------

//       // REGISTRA AÇÃO NO LOG
//       $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
//                           VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
//       $stmt->execute(array(
//         ':modulo'     => 'AUTENTICAÇÃO',
//         ':acao'       => 'CRIA NOVA SENHA',
//         ':acao_id'    => $user_id,
//         ':user_id'    => $user_id
//       ));
//       // -------------------------------

//       //ENVIA MENSAGEM
//       $_SESSION["msg"] = "Senha redefinida!";
//       header("Location: $url_sistema");
//     } catch (PDOException $e) {
//       // echo 'Error: ' . $e->getMessage();
//       $_SESSION["erro"] = "Erro ao tentar criar a senha!";
//       header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//       return die;
//     }
//   } else {
//     $_SESSION["erro"] = "Erro ao tentar criar a senha!";
//     header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//     return die;
//   }
// }










/*****************************************************************************************
                            ALTERA A SENHA QUE FOI PERDIDA
 *****************************************************************************************/
// if (isset($dados['AdminResetPassLink'])) {

//   $id_cript = $_GET['cod']; // ID DO USUÁRIO CRIPTOGRAFADO
//   $token    = $_GET['t']; // TOKEN

//   $id = base64_decode($id_cript); // DECODIFICA O ID DO USUÁRIO

//   // RECUPERA DADOS DA TABELA "USUARIOS"
//   $query = 'SELECT id, user_nome, user_email, user_cpf, user_passaporte FROM usuarios WHERE id = :id';
//   $stmt = $conn->prepare($query);
//   $stmt->bindParam(':id', $id);
//   $stmt->execute();
//   $result = $stmt->fetch(PDO::FETCH_ASSOC);
//   if ($result) {
//     $user_nome  = $result['user_nome'];
//     $user_email = $result['user_email'];
//     $user_cpf   = $result['user_cpf'];
//   } else {
//     $_SESSION["rest"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Acesso restrito!";
//     header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//   }

//   // VERIFICA SE O ID DO USUARIOS EXISTE
//   $query = "SELECT id FROM usuarios WHERE id = '$id'";
//   $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
//   $stmt->execute();
//   $row_count = $stmt->rowCount();
//   if (empty($row_count)) {
//     $_SESSION["rest"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Acesso restrito!";
//     header("Location: ../sair.php");
//     return die;
//   }

//   $user_senha         = password_hash($_POST['user_senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA
//   $user_senha_confirm = password_verify($_POST['user_senha_confirm'], $user_senha); //VERIFICA SE A SENHA DE CONFIRMAÇÃO ESTÁ IGUAL A DIGITADA.

//   // AS DUAS SENHAS GIGITADAS PRECISAM SER IGUAIS
//   if ($user_senha_confirm != 1) {
//     $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> As senhas digitadas estão diferentes!";
//     header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//   } else {

//     // EDITAR DADOS 
//     try {
//       $stmt = $conn->prepare('UPDATE usuarios SET user_senha = :user_senha, user_data_reset_senha = :user_data_reset_senha, user_data_acesso = :user_data_acesso WHERE id = :id');
//       $stmt->execute(array(':id' => $id, ':user_senha' => $user_senha, ':user_data_reset_senha' => date('Y-m-d H:i:s'), ':user_data_acesso' => date('Y-m-d H:i:s')));
//       echo $stmt->rowCount();

//       $sql = "DELETE FROM token WHERE codigo = '$token'";
//       $conn->exec($sql);

//       // REGISTRA AÇÃO NO LOG
//       $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data ) VALUES ( :modulo, :acao, :acao_id, :user_id, UPPER(:user_nome), :data )');
//       $stmt->execute(array(
//         ':modulo'    => 'USUÁRIOS',
//         ':acao'      => 'ATUALIZAÇÃO SENHA',
//         ':acao_id'   => $id,
//         ':user_id'   => $id,
//         ':user_nome' => $user_nome,
//         ':data'      => date('Y-m-d H:i:s')
//       ));

//       //ENVIA MENSAGEM
//       $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> Senha atualizada com sucesso!";
//       header("Location: ../");
//     } catch (PDOException $e) {
//       //echo 'Error: ' . $e->getMessage();
//       $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Erro ao tentar atualizado os dados!";
//       header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
//     }
//     $conn = null;
//   }
// }














/*****************************************************************************************
                    ALTERAR A SENHA DO USUÁRIO PELO PERFIL
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "alterSenhaPerfil") {

  $user_id         = base64_decode($_POST['cod']); // DECODIFICA O ID DO USUÁRIO
  $user_senha      = password_hash($_POST['senha'], PASSWORD_DEFAULT); //CODIFICA A NOVA SENHA DIGITADA
  $user_conf_senha = password_verify($_POST['conf_senha'], $user_senha); //VERIFICA SE A SENHA DE CONFIRMAÇÃO ESTÁ IGUAL A DIGITADA.

  // NOVA SENHA PRECISAR SER DIFERENTE DA ATUAL
  if ($_POST['senha_atual'] == $_POST['senha']) {
    $_SESSION["erro"] = "A nova senha precisa ser diferente da atual!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
    // -------------------------------
  } else {

    // VALIDA SENHA ATUAL
    $stmt = $conn->prepare("SELECT user_id, user_senha FROM usuarios WHERE user_id = :user_id");
    $stmt->execute([
      ':user_id' => $user_id
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_senha_atual  = password_verify($_POST['senha_atual'], $result['user_senha']); //CONFERE SE A SENHA ATUAL DIGITADA ESTÁ IGUAL A DO BANCO
    if ($user_senha_atual != 1) {
      $_SESSION["erro"] = "A senha atual está incorreta!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
      // -------------------------------

    } else {
      // AS DUAS SENHAS DIGITADAS PRECISAM SER IGUAIS
      if ($user_conf_senha != 1) {
        $_SESSION["erro"] = "As senhas digitadas estão diferentes!";
        header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        return die;
        // -------------------------------

      } else {

        // EDITA SENHA 
        try {
          $conn->beginTransaction(); // INICIA A TRANSAÇÃO
          $stmt = $conn->prepare('UPDATE
                                          usuarios
                                    SET 
                                          user_senha = :user_senha, 
                                          user_data_reset_senha = GETDATE()
                                    WHERE
                                          user_id = :user_id
                                    ');
          $stmt->execute([
            ':user_id' => $user_id,
            ':user_senha' => $user_senha
          ]);

          // REGISTRA AÇÃO NO LOG
          $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                                  VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
          $stmt->execute([
            ':modulo'     => 'AUTENTICAÇÃO',
            ':acao'       => 'ALTERA A SENHA PELO PERFIL',
            ':acao_id'    => $user_id,
            ':user_id'    => $user_id
          ]);
          // -------------------------------

          $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

          //ENVIA MENSAGEM
          $_SESSION["msg"] = "Senha atualizada!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
        } catch (PDOException $e) {
          // echo 'Error: ' . $e->getMessage();
          $conn->rollBack();
          $_SESSION["erro"] = "Erro ao tentar atualizar a senha!";
          header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
          return die;
        }
      }
    }
  }
}













/*****************************************************************************************
                          EXCLUIR CONTA DO USUÁRIO PELO PERFIL
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_user_conta") {

  $id_cript = $_GET['cod']; // ID DO USUÁRIO CRIPTOGRAFADO
  $user_id  = base64_decode($id_cript); // DECODIFICA O ID DO USUÁRIO

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $stmt = $conn->prepare('UPDATE
                                  usuarios
                            SET
                                  user_nome_social          = :user_nome_social,
                                  user_doc                  = :user_doc,
                                  user_nacionalidade        = :user_nacionalidade,
                                  user_email                = :user_email,
                                  user_data_nascimento      = :user_data_nascimento,
                                  user_vinculo              = :user_vinculo,
                                  user_rg                   = :user_rg,
                                  user_genero               = :user_genero,
                                  user_raca                 = :user_raca,
                                  user_contato              = :user_contato,
                                  user_perfil               = :user_perfil,
                                  user_outro_perfil         = :user_outro_perfil,
                                  user_cep                  = :user_cep,
                                  user_rua                  = :user_rua,
                                  user_numero               = :user_numero,
                                  user_bairro               = :user_bairro,
                                  user_municipio            = :user_municipio,
                                  user_estado               = :user_estado,
                                  user_endereco             = :user_endereco,
                                  user_escolaridade         = :user_escolaridade,
                                  user_instituicao_ensino   = :user_instituicao_ensino,
                                  user_lattes               = :user_lattes,
                                  user_facebook             = :user_facebook,
                                  user_instagram            = :user_instagram,
                                  user_linkedin             = :user_linkedin,
                                  user_vinculo_atividade    = :user_vinculo_atividade,
                                  user_receber_notificacoes = :user_receber_notificacoes,
                                  user_senha                = :user_senha,
                                  user_data_reset_senha     = :user_data_reset_senha,
                                  user_data_acesso          = :user_data_acesso,
                                  nivel_acesso              = :nivel_acesso,
                                  user_status               = :user_status,
                                  user_data_upd             = GETDATE()
                            WHERE
                                  user_id = :user_id');
    $stmt->execute([
      ':user_id' => $user_id,
      ':user_nome_social' => NULL,
      ':user_doc' => NULL,
      ':user_nacionalidade' => NULL,
      ':user_email' => NULL,
      ':user_data_nascimento' => NULL,
      ':user_vinculo' => NULL,
      ':user_rg' => NULL,
      ':user_genero' => NULL,
      ':user_raca' => NULL,
      ':user_contato' => NULL,
      ':user_perfil' => NULL,
      ':user_outro_perfil' => NULL,
      ':user_cep' => NULL,
      ':user_rua' => NULL,
      ':user_numero' => NULL,
      ':user_bairro' => NULL,
      ':user_municipio' => NULL,
      ':user_estado' => NULL,
      ':user_endereco' => NULL,
      ':user_escolaridade' => NULL,
      ':user_instituicao_ensino' => NULL,
      ':user_lattes' => NULL,
      ':user_facebook' => NULL,
      ':user_instagram' => NULL,
      ':user_linkedin' => NULL,
      ':user_vinculo_atividade' => NULL,
      ':user_receber_notificacoes' => NULL,
      ':user_senha' => NULL,
      ':user_data_reset_senha' => NULL,
      ':user_data_acesso' => NULL,
      ':nivel_acesso' => NULL,
      ':user_status' => 0
    ]);

    // EXCLUI DADOS DO ARQUIVO DA TABELA
    $stmt = $conn->prepare("DELETE FROM usuarios_arq WHERE arq_user_id = :arq_user_id");
    $stmt->bindParam(':arq_user_id', $user_id);
    $stmt->execute();
    // -------------------------------

    // EXCLUI DADOS DA SOLICITÃO DE SUBMISSÃO
    $stmt_subm = $conn->prepare("DELETE FROM submissao_permissao WHERE subs_cad = :subs_cad");
    $stmt_subm->bindParam(':subs_cad', $user_id);
    $stmt_subm->execute();
    // -------------------------------

    // EXCLUI OS ARQUIVOS DA PASTA
    $dir = '../uploads/usuarios/' . $user_id; // Substitua pelo caminho da pasta que você deseja limpar

    if (is_dir($dir)) {
      $files = scandir($dir);

      foreach ($files as $file) {
        if ($file != "." && $file != "..") {
          $filepath = $dir . '/' . $file;

          if (is_file($filepath)) {
            if (unlink($filepath)) {
              //echo "Arquivo $file excluído com sucesso.<br>";
            } else {
              //echo "Falha ao excluir o arquivo $file.<br>";
            }
          }
          // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA
          rmdir($dir);
        }
      }
    } else {
      //echo "O diretório não existe.";
    }
    // -------------------------------

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'     => 'USUÁRIOS',
      ':acao'       => 'EXCLUSÃO PERFIL',
      ':acao_id'    => $user_id,
      ':user_id'    => $user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    header("Location: ../sair.php");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Dados não atualizados!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  }
}
