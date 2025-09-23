<?php
// session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
  try {


    // $acao = $_POST['acao']; // AÇÃO: CADASTRAR, ATUALIZAR, DELETAR
    $acao = $_POST['acao'] ?? $_GET['acao'] ?? null;

    if (is_null($acao)) {
      throw new Exception("Ação inválida ou não especificada.");
    }

    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // SE CADASTRAR
    if ($acao === 'cadastrar') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (empty($_POST['user_email'])) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $user_id = bin2hex(random_bytes(16)); // GERA ID COM 32 CARACTERES HEXADECIMAIS
      $user_email = filter_var(trim($_POST['user_email']), FILTER_SANITIZE_EMAIL);
    }


    // -------------------------------
    // CADASTRO
    // -------------------------------
    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM usuarios WHERE user_email = :user_email AND user_status = :user_status";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':user_email' => $user_email, ':user_status' => 1]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Falha ao cadastrar os dados: E-mail já foi cadastrado!");
      }

      // BUSCA OS DADOS DO USUÁRIO COM O EMAIL INFORMADO
      $result_colab = $conn->prepare("SELECT CHAPA, NOMESOCIAL, EMAIL FROM $view_colaboradores WHERE EMAIL = ?");
      $result_colab->execute([$user_email]);
      $row_user = $result_colab->fetch(PDO::FETCH_ASSOC);

      if (!$row_user) {
        throw new Exception("Erro ao realizar o cadastro!");
      }

      $user_matricula = $row_user['CHAPA'];
      $user_nome = $row_user['NOMESOCIAL'];
      $user_email = $row_user['EMAIL'];
      $nivel_acesso = 2; // NÍVEL DE ACESSO DO USUÁRIO
      $user_status = 0; // REALIZA O CADASTRO COMO PENDENTE DE VALIDAÇÃO DO CÓDIGO


      // GERA UM TOKEN DE VALIDAÇÃO
      $codigo = str_pad(random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT); // GERA UM CÓDIGO DE 7 DÍGITOS
      $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT); // CRIPTOGRAFA O CÓDIGO
      $expira_em = date('Y-m-d H:i:s', strtotime('+2 minutes'));

      // VERIFICA SE CADASTRO FOI REALIZADO MAIS AINDA ESTÁ INCOMPLETO
      $result_usuario_pendente = $conn->prepare("SELECT * FROM usuarios WHERE user_email = :user_email AND user_status = :user_status");
      $result_usuario_pendente->execute([':user_email' => $user_email, ':user_status' => 0]);
      $row_verifica = $result_usuario_pendente->fetch(PDO::FETCH_ASSOC);
      if ($row_verifica) {

        $user_id = $row_verifica['user_id'];

        // VERIFICA SE JÁ EXISTE UM TOKEN PARA O ID DO USUÁRIO
        $stmt = $conn->prepare("SELECT COUNT(*) FROM token WHERE tok_user_id = ?");
        $stmt->execute([$user_id]);
        if ($stmt->fetchColumn() > 0) {
          // SE HOUVER, ATUALIZA O TOKEN E A DATA DE VALIDADE
          $stmt = $conn->prepare("UPDATE token SET tok_codigo = :tok_codigo, tok_data_valida = :tok_data_valida WHERE tok_user_id = :tok_user_id");
        } else {
          // CRIA UM TOKEN PARA O ID DO USUÁRIO, CASO NÃO EXISTA
          $stmt = $conn->prepare('INSERT INTO token (tok_codigo, tok_user_id, tok_data_valida) VALUES (:tok_codigo, :tok_user_id, :tok_data_valida)');
        }
        $stmt->execute([':tok_codigo' => $codigo_hash, ':tok_user_id' => $user_id, ':tok_data_valida' => $expira_em]);
      } else {

        $sql = "INSERT INTO usuarios (
                                      user_id,
                                      user_matricula,
                                      user_nome,
                                      user_email,
                                      nivel_acesso,
                                      user_status,
                                      user_user_id,
                                      user_data_cad,
                                      user_data_upd
                                    ) VALUES (
                                      :user_id,
                                      :user_matricula,
                                      UPPER(:user_nome),
                                      LOWER(:user_email),
                                      :nivel_acesso,
                                      :user_status,
                                      :user_user_id,
                                      GETDATE(),
                                      GETDATE()
                                    )";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':user_id' => $user_id,
          ':user_matricula' => $user_matricula,
          ':user_nome' => $user_nome,
          ':user_email' => $user_email,
          ':nivel_acesso' => $nivel_acesso,
          ':user_status' => $user_status,
          ':user_user_id' => $user_id
        ]);

        // CRIA UM TOKEN PARA O ID DO USUÁRIO
        $stmt = $conn->prepare('INSERT INTO token (tok_codigo, tok_user_id, tok_data_valida) VALUES (:tok_codigo, :tok_user_id, :tok_data_valida)');
        $stmt->execute([':tok_codigo' => $codigo_hash, ':tok_user_id' => $user_id, ':tok_data_valida' => $expira_em]);
        // -------------------------------

      }
      // DISPARA E-MAIL
      $user_id_cript = $user_id; // CRIPTOGRAFA ID

      $mail = new PHPMailer(true);
      include '../conexao/email.php';
      $mail->addAddress($user_email); // E-MAIL DO ADMINISTRADOR
      $mail->isHTML(true);
      $mail->Subject = 'Seu código de acesso chegou'; //TÍTULO DO E-MAIL

      // CORPO DO EMAIL
      include '../includes/email/email_header.php';
      $email_conteudo .= "
      <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
        <td style='padding: 2em 2rem; display: inline-block;'>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Olá,
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Seu código do SIGAEXT chegou!
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Ao copiá-lo vá à página de acesso e insira o código abaixo para confirmar sua identidade.
        </p>

        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
        Lembrando que essa etapa é muito importante para mantermos a segurança dos seus dados e cumprirmos nosso compromisso com você.
        </p>

        <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 15px 0px;'>
        Seu código de acesso é:
        </p>

        <span style='color: #285FAB; font-size: 2rem; letter-spacing: 0.20rem; font-weight: 600; margin-bottom: 20px; display: inline-block;'>$codigo</span><br>
        <a style='cursor: pointer;' href='$url_sistema/us-validcod.php?i=$user_id_cript'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema e valide seu código.</button></a>
        </td>
      </tr>";
      include '../includes/email/email_footer.php';

      $mail->Body = $email_conteudo;

      try {
        $mail->send();
      } catch (Exception $e) {
        $conn->rollBack();
        throw new Exception("Erro ao enviar o e-mail. Tente novamente!");
      }
      // -------------------------------






      // -------------------------------
      // ATUALIZAR
      // -------------------------------
    } elseif ($acao === 'atualizar') {

      $log_acao = 'Atualização';

      if (empty($_POST['user_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }

      $user_id = base64_decode($_POST['user_id']);
      $user_nome = trim($_POST['user_nome']);
      $user_nome_social = trim($_POST['user_nome_social']) !== '' ? trim($_POST['user_nome_social']) : NULL;
      // CPF/PASSAPORTE
      if ($_POST['user_cpf']) {
        $cpfDot = trim($_POST['user_cpf']) !== '' ? trim($_POST['user_cpf']) : NULL;
        $user_doc = preg_replace('/[^0-9]/', '', $cpfDot); // RETIRA PONTOS E TRAÇOS
      } else {
        $user_doc = trim($_POST['user_pass']) !== '' ? trim($_POST['user_pass']) : NULL;
      }
      //
      $user_nacionalidade = trim($_POST['user_nacionalidade']) !== '' ? trim($_POST['user_nacionalidade']) : NULL;
      $user_email = filter_var(trim($_POST['user_email']), FILTER_SANITIZE_EMAIL);
      $user_data_nascimento = trim($_POST['user_data_nascimento']);
      $user_rg = trim($_POST['user_rg']);
      $user_genero = trim($_POST['user_genero']);
      $user_raca = trim($_POST['user_raca']);
      $user_contato = trim($_POST['user_contato']);
      // PERFIL
      $user_perfil = trim($_POST['user_perfil']);
      $user_outro_perfil = ($user_perfil == 9) ? trim($_POST['user_outro_perfil']) : NULL;
      //
      $user_vinculo = $_POST['user_vinculo'] === '1' ? 1 : 0;
      // EDNDEREÇO
      $user_cep = trim($_POST['user_cep']) !== '' ? trim($_POST['user_cep']) : NULL;
      $user_rua = trim($_POST['user_rua']) !== '' ? trim($_POST['user_rua']) : NULL;
      $user_numero = trim($_POST['user_numero']) !== '' ? trim($_POST['user_numero']) : NULL;
      $user_bairro = trim($_POST['user_bairro']) !== '' ? trim($_POST['user_bairro']) : NULL;
      $user_municipio = trim($_POST['user_municipio']) !== '' ? trim($_POST['user_municipio']) : NULL;
      $user_estado = trim($_POST['user_estado']) !== '' ? trim($_POST['user_estado']) : NULL;
      $user_endereco = trim($_POST['user_endereco']) !== '' ? nl2br(trim($_POST['user_endereco'])) : NULL;
      // ESCOLARIDADE
      $user_escolaridade = trim($_POST['user_escolaridade']);
      $user_instituicao_ensino = trim($_POST['user_instituicao_ensino']);
      $user_lattes = trim($_POST['user_lattes']) !== '' ? trim($_POST['user_lattes']) : NULL;
      // REDES SOCIAIS
      $user_facebook = trim($_POST['user_facebook']) !== '' ? trim($_POST['user_facebook']) : NULL;
      $user_instagram = trim($_POST['user_instagram']) !== '' ? trim($_POST['user_instagram']) : NULL;
      $user_linkedin = trim($_POST['user_linkedin']) !== '' ? trim($_POST['user_linkedin']) : NULL;
      //
      $user_vinculo_atividade = $_POST['user_vinculo_atividade'] === '1' ? 1 : 0;
      $user_receber_notificacoes = $_POST['user_receber_notificacoes'] === '1' ? 1 : 0;


      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM usuarios WHERE user_email = :user_email AND user_id != :user_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':user_email' => $user_email, ':user_id' => $user_id]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Este e-mail já foi cadastrado!");
      }
      // -------------------------------

      if ($_POST['user_cpf']) {
        $sqlVerifica = "SELECT COUNT(*) FROM usuarios WHERE user_doc = :user_doc AND user_doc != '' AND user_id != :user_id";
        $stmtVerifica = $conn->prepare($sqlVerifica);
        $stmtVerifica->execute([':user_doc' => $user_doc, ':user_id' => $user_id]);
        $existe = $stmtVerifica->fetchColumn();
        if ($existe > 0) {
          throw new Exception("Este CPF já foi cadastrado!333");
        }
      }
      // -------------------------------


      $sql = "UPDATE usuarios SET 
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
                                  user_endereco             = :user_endereco,
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
          exit();
        }

        $sql = "INSERT INTO usuarios_arq (arq_arquivo, arq_user_id, arq_data_upd) VALUES (:arq_arquivo, :arq_user_id, GETDATE())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
          ':arq_arquivo' => $novoNome,
          ':arq_user_id' => $user_id
        ]);
      }







      // -------------------------------
      // EXCLUIR ARQUIVO
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar_arq') {

      if (empty($_GET['arq_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }

      $arq_id = base64_decode($_GET['arq_id']);
      $arq_arquivo = $_GET['arq'];
      $user_id = $_SESSION['reservm_user_id'];

      $sql = "DELETE FROM usuarios_arq WHERE arq_id = :arq_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':arq_id' => $arq_id]);

      $apaga_img = unlink("../uploads/usuarios/$user_id/$arq_arquivo"); //APAGA O ARQUIVO ANTIGO

      // LOG
      $log_acao = 'Exclusão Arquivo';
      $user_id = $arq_id;







      // -------------------------------
      // ALTERAR A SENHA PELO PERFIL
      // -------------------------------
    } elseif ($acao === 'atualizar_senha') {

      $log_acao = 'Alterar Senha';

      $user_id = base64_decode($_POST['i']); // DECODIFICA O ID DO USUÁRIO
      $user_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // CODIFICA A NOVA SENHA DIGITADA
      $user_conf_senha = password_verify($_POST['conf_senha'], $user_senha); // VERIFICA SE A SENHA DE CONFIRMAÇÃO ESTÁ IGUAL A DIGITADA.

      // NOVA SENHA PRECISAR SER DIFERENTE DA ATUAL
      if ($_POST['senha_atual'] == $_POST['senha']) {
        throw new Exception("A nova senha precisa ser diferente da atual!");
      }

      // VALIDA SENHA ATUAL
      $stmt = $conn->prepare("SELECT user_id, user_senha FROM usuarios WHERE user_id = :user_id");
      $stmt->execute([':user_id' => $user_id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $user_senha_atual = password_verify($_POST['senha_atual'], $result['user_senha']); //CONFERE SE A SENHA ATUAL DIGITADA ESTÁ IGUAL A DO BANCO
      if ($user_senha_atual != 1) {
        throw new Exception("A senha atual está incorreta!");
      }

      // AS DUAS SENHAS DIGITADAS PRECISAM SER IGUAIS
      if ($user_conf_senha != 1) {
        throw new Exception("As senhas digitadas estão diferentes!");
      }

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








      // -------------------------------
      // EXCLUIR PELO PERFIL
      // -------------------------------
    } elseif ($acao === 'deletar_conta') {

      if (empty($_POST['i'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }

      $user_id = base64_decode($_POST['i']);
      $user_senha = $_POST['user_senha'];
      $log_acao = 'Exclusão Conta';

      // BUSCA OS DADOS DO USUÁRIO COM O ID INFORMADO
      $result_user = $conn->prepare("SELECT user_id, user_senha FROM usuarios WHERE user_id = ?");
      $result_user->execute([$user_id]);
      $row_user = $result_user->fetch(PDO::FETCH_ASSOC);

      // SENHA INCORRETO
      if (!password_verify($user_senha, $row_user['user_senha'])) {
        throw new Exception("Senha incorretos!");
      }

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
                                      --user_perfil               = :user_perfil,
                                      --user_outro_perfil         = :user_outro_perfil,
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
                                      --nivel_acesso              = :nivel_acesso,
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
        //':user_perfil' => 0,
        //':user_outro_perfil' => NULL,
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
        //':nivel_acesso' => NULL,
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
      // AÇÃO INVÁLIDA
      // -------------------------------
    } else {
      throw new Exception("Ação inválida.");
    }

    // REGISTRA NO LOG
    // MODIFICAÇÃO PARA O LOG NÃO REGISTRAR A SENHA DIGITADA
    $log_post = $_POST;
    $log_post['senha_atual'] = '****'; // MÁSCARA DA SENHA
    $log_post['senha'] = '****'; // MÁSCARA DA SENHA
    $log_post['conf_senha'] = '****'; // MÁSCARA DA SENHA
    $log_dados = ['POST' => $log_post, 'GET' => $_GET, 'FILES' => $_FILES];
    //
    $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
              VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
    $stmtLog = $conn->prepare($sqlLog);
    $stmtLog->execute([
      ':modulo' => 'USUÁRIOS',
      ':acao' => $log_acao,
      ':acao_id' => $user_id,
      ':dados' => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $user_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    if ($acao === 'cadastrar') {
      session_start();
      $_SESSION['user_val_cod'] = $user_id_cript;
      header("Location: ../us-validcod.php");
      exit();
    } elseif ($acao === 'atualizar') {
      $_SESSION["msg"] = "Dados atualizados com sucesso!";
      header("Location: ../perfil.php");
      exit();
    } elseif ($_GET['acao'] === 'deletar_arq') {
      header("Location: ../perfil.php");
      exit();
    } elseif ($acao === 'deletar_conta') {
      header("Location: ../sair.php");
      exit;
    } elseif ($acao === 'atualizar_senha') {
      $_SESSION["msg"] = "Senha atualizada com sucesso!";
      header("Location: ../perfil.php");
      exit;
    } else {
      // QUANDO CONTA EXCLUÍDA PELO PERFIL!
      header("Location: ../sair.php");
      exit;
    }
    // -------------------------------
    //header("Location: ../admin/admin.php");
    //header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    //exit;
    // -------------------------------

  } catch (Exception $e) {
    if ($conn->inTransaction()) {
      $conn->rollBack();
    }
    $_SESSION["erro"] = $e->getMessage();
    $_SESSION["form_user"] = $_POST;
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  exit;
}
