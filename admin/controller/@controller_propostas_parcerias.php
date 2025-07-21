<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
// -------------------------------

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

/*****************************************************************************************
                                CADASTRAR PROPOSTA - PARCERIAS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "cad_prop_parc") {
  // if (isset($dados['CadPropostasParcerias'])) {

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($_POST['prop_tipo'], $tipos)) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Cadastro não realizado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  // GERA UM ID UNICO
  $prop_id = md5(uniqid(rand(), true));
  // -------------------------------

  // GERA UM CÓDIGO
  $prop_codigo = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT); // GERA UM CÓDIGO COM 6 DÍGITOS 
  // -------------------------------

  $prop_tipo                        = $_POST['prop_tipo'];
  $prop_status_etapa                = 5; // NÚMERO DA ETAPA 
  //
  $prop_parc_nome_empresa           = trim($_POST['prop_parc_nome_empresa']);
  $prop_parc_tipo_empresa           = $_POST['prop_parc_tipo_empresa'];
  //
  if ($prop_parc_tipo_empresa == '1') {
    $prop_parc_tipo_outro           = trim($_POST['prop_parc_tipo_outro']);
  } else {
    $prop_parc_tipo_outro           = NULL;
  }
  //
  if ($prop_parc_tipo_empresa == '4') {
    $prop_parc_orgao_empresa        = trim($_POST['prop_parc_orgao_empresa']);
  } else {
    $prop_parc_orgao_empresa        = NULL;
  }
  //  
  $prop_parc_email                  = $_POST['prop_parc_email'];
  $prop_parc_telefone               = $_POST['prop_parc_telefone'];
  $prop_parc_cep                    = $_POST['prop_parc_cep'];
  $prop_parc_logradouro             = trim($_POST['prop_parc_logradouro']);
  $prop_parc_numero                 = $_POST['prop_parc_numero'];
  $prop_parc_bairro                 = trim($_POST['prop_parc_bairro']);
  $prop_parc_municipio              = trim($_POST['prop_parc_municipio']);
  $prop_parc_estado                 = trim($_POST['prop_parc_estado']);
  $prop_parc_pais                   = trim($_POST['prop_parc_pais']);
  $prop_parc_responsavel            = trim($_POST['prop_parc_responsavel']);
  $prop_parc_cargo                  = trim($_POST['prop_parc_cargo']);
  $prop_parc_contato_referencia     = trim($_POST['prop_parc_contato_referencia']);
  $prop_parc_possui_convenio        = isset($_POST['prop_parc_possui_convenio']) ? $_POST['prop_parc_possui_convenio'] : 0;
  $prop_parc_tipo_parceria          = $_POST['prop_parc_tipo_parceria'];
  $prop_parc_titulo_atividade       = trim($_POST['prop_parc_titulo_atividade']);
  $prop_parc_objetivo_atividade     = nl2br(trim($_POST['prop_parc_objetivo_atividade']));
  $prop_parc_carga_hora             = $_POST['prop_parc_carga_hora'];
  $prop_parc_data_atividade         = isset($_POST['prop_parc_data_atividade']) ? $_POST['prop_parc_data_atividade'] : NULL;
  $prop_parc_hora_atividade_inicial = $_POST['prop_parc_hora_atividade_inicial'];
  $prop_parc_hora_atividade_final   = $_POST['prop_parc_hora_atividade_final'];
  $prop_parc_local_atividade        = trim($_POST['prop_parc_local_atividade']);
  $prop_parc_tipo_espaco            = $_POST['prop_parc_tipo_espaco'];
  $prop_parc_campus_atividade       = $_POST['prop_parc_campus_atividade'];
  $prop_parc_numero_participantes   = $_POST['prop_parc_numero_participantes'];
  $prop_parc_recursos_necessarios   = nl2br(trim($_POST['prop_parc_recursos_necessarios']));
  $prop_parc_organizacao_espaco     = $_POST['prop_parc_organizacao_espaco'];
  $prop_parc_beneficios             = $_POST['prop_parc_beneficios'];
  //
  if ($prop_parc_beneficios == 'SIM') {
    $prop_parc_beneficios_quantidade = nl2br(trim($_POST['prop_parc_beneficios_quantidade']));
  } else {
    $prop_parc_beneficios_quantidade = NULL;
  }
  //  
  $prop_parc_comentarios            = nl2br(trim($_POST['prop_parc_comentarios']));
  // -------------------------------

  try {
    $sql = "INSERT INTO propostas (
                                    prop_id,
                                    prop_tipo,
                                    prop_codigo,
                                    prop_status_etapa,
                                    prop_titulo,
                                    prop_parc_nome_empresa,
                                    prop_parc_tipo_empresa,
                                    prop_parc_tipo_outro,
                                    prop_parc_orgao_empresa,
                                    prop_parc_email,
                                    prop_parc_telefone,
                                    prop_parc_cep,
                                    prop_parc_logradouro,
                                    prop_parc_numero,
                                    prop_parc_bairro,
                                    prop_parc_municipio,
                                    prop_parc_estado,
                                    prop_parc_pais,
                                    prop_parc_responsavel,
                                    prop_parc_cargo,
                                    prop_parc_contato_referencia,
                                    prop_parc_possui_convenio,
                                    prop_parc_tipo_parceria,
                                    prop_parc_titulo_atividade,
                                    prop_parc_objetivo_atividade,
                                    prop_parc_carga_hora,
                                    prop_parc_data_atividade,
                                    prop_parc_hora_atividade_inicial,
                                    prop_parc_hora_atividade_final,
                                    prop_parc_local_atividade,
                                    prop_parc_tipo_espaco,
                                    prop_parc_campus_atividade,
                                    prop_parc_numero_participantes,
                                    prop_parc_recursos_necessarios,
                                    prop_parc_beneficios,
                                    prop_parc_beneficios_quantidade,
                                    prop_parc_organizacao_espaco,
                                    prop_parc_comentarios,
                                    prop_user_id,
                                    prop_data_cad,
                                    prop_data_upd
                                  ) VALUES (
                                    :prop_id,
                                    :prop_tipo,
                                    :prop_codigo,
                                    :prop_status_etapa,
                                    UPPER(:prop_titulo),
                                    UPPER(:prop_parc_nome_empresa),
                                    UPPER(:prop_parc_tipo_empresa),
                                    :prop_parc_tipo_outro,
                                    UPPER(:prop_parc_orgao_empresa),
                                    LOWER(:prop_parc_email),
                                    :prop_parc_telefone,
                                    :prop_parc_cep,
                                    UPPER(:prop_parc_logradouro),
                                    UPPER(:prop_parc_numero),
                                    UPPER(:prop_parc_bairro),
                                    UPPER(:prop_parc_municipio),
                                    UPPER(:prop_parc_estado),
                                    UPPER(:prop_parc_pais),
                                    UPPER(:prop_parc_responsavel),
                                    UPPER(:prop_parc_cargo),
                                    UPPER(:prop_parc_contato_referencia),
                                    :prop_parc_possui_convenio,
                                    :prop_parc_tipo_parceria,
                                    UPPER(:prop_parc_titulo_atividade),
                                    :prop_parc_objetivo_atividade,
                                    :prop_parc_carga_hora,
                                    :prop_parc_data_atividade,
                                    :prop_parc_hora_atividade_inicial,
                                    :prop_parc_hora_atividade_final,
                                    UPPER(:prop_parc_local_atividade),
                                    UPPER(:prop_parc_tipo_espaco),
                                    :prop_parc_campus_atividade,
                                    :prop_parc_numero_participantes,
                                    :prop_parc_recursos_necessarios,
                                    UPPER(:prop_parc_beneficios),
                                    :prop_parc_beneficios_quantidade,
                                    :prop_parc_organizacao_espaco,
                                    :prop_parc_comentarios,
                                    :prop_user_id,
                                    :prop_data_cad,
                                    :prop_data_upd
                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_id", $prop_id);
    $stmt->bindParam(":prop_tipo", $prop_tipo);
    $stmt->bindParam(":prop_codigo", $prop_codigo);
    $stmt->bindParam(":prop_status_etapa", $prop_status_etapa);
    $stmt->bindParam(":prop_titulo", $prop_parc_nome_empresa);
    //
    $stmt->bindParam(":prop_parc_nome_empresa", $prop_parc_nome_empresa);
    $stmt->bindParam(":prop_parc_tipo_empresa", $prop_parc_tipo_empresa);
    $stmt->bindParam(":prop_parc_tipo_outro", $prop_parc_tipo_outro);
    $stmt->bindParam(":prop_parc_orgao_empresa", $prop_parc_orgao_empresa);
    $stmt->bindParam(":prop_parc_email", $prop_parc_email);
    $stmt->bindParam(":prop_parc_telefone", $prop_parc_telefone);
    $stmt->bindParam(":prop_parc_cep", $prop_parc_cep);
    $stmt->bindParam(":prop_parc_logradouro", $prop_parc_logradouro);
    $stmt->bindParam(":prop_parc_numero", $prop_parc_numero);
    $stmt->bindParam(":prop_parc_bairro", $prop_parc_bairro);
    $stmt->bindParam(":prop_parc_municipio", $prop_parc_municipio);
    $stmt->bindParam(":prop_parc_estado", $prop_parc_estado);
    $stmt->bindParam(":prop_parc_pais", $prop_parc_pais);
    $stmt->bindParam(":prop_parc_responsavel", $prop_parc_responsavel);
    $stmt->bindParam(":prop_parc_cargo", $prop_parc_cargo);
    $stmt->bindParam(":prop_parc_contato_referencia", $prop_parc_contato_referencia);
    $stmt->bindParam(":prop_parc_possui_convenio", $prop_parc_possui_convenio);
    $stmt->bindParam(":prop_parc_tipo_parceria", $prop_parc_tipo_parceria);
    $stmt->bindParam(":prop_parc_titulo_atividade", $prop_parc_titulo_atividade);
    $stmt->bindParam(":prop_parc_objetivo_atividade", $prop_parc_objetivo_atividade);
    $stmt->bindParam(":prop_parc_carga_hora", $prop_parc_carga_hora);
    $stmt->bindParam(":prop_parc_data_atividade", $prop_parc_data_atividade);
    $stmt->bindParam(":prop_parc_hora_atividade_inicial", $prop_parc_hora_atividade_inicial);
    $stmt->bindParam(":prop_parc_hora_atividade_final", $prop_parc_hora_atividade_final);
    $stmt->bindParam(":prop_parc_local_atividade", $prop_parc_local_atividade);
    $stmt->bindParam(":prop_parc_tipo_espaco", $prop_parc_tipo_espaco);
    $stmt->bindParam(":prop_parc_campus_atividade", $prop_parc_campus_atividade);
    $stmt->bindParam(":prop_parc_numero_participantes", $prop_parc_numero_participantes);
    $stmt->bindParam(":prop_parc_recursos_necessarios", $prop_parc_recursos_necessarios);
    $stmt->bindParam(":prop_parc_beneficios", $prop_parc_beneficios);
    $stmt->bindParam(":prop_parc_beneficios_quantidade", $prop_parc_beneficios_quantidade);
    $stmt->bindParam(":prop_parc_organizacao_espaco", $prop_parc_organizacao_espaco);
    $stmt->bindParam(":prop_parc_comentarios", $prop_parc_comentarios);
    //
    $stmt->bindParam(":prop_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":prop_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - PARCERIAS',
      ':acao'       => 'CADASTRO',
      ':acao_id'    => $prop_id,
      ':dados'      => 'Empresa: ' . $prop_parc_nome_empresa .
        '; Tipo empresa: ' . $prop_parc_tipo_empresa .
        '; Tipo (outro): ' . $prop_parc_tipo_outro .
        '; Órgão: ' . $prop_parc_orgao_empresa .
        '; E-amil: ' . $prop_parc_email .
        '; Contato: ' . $prop_parc_telefone .
        '; prop_parc_cep: ' . $prop_parc_cep .
        '; CEP: ' . $prop_parc_logradouro .
        '; Rua: ' . $prop_parc_numero .
        '; Bairro: ' . $prop_parc_bairro .
        '; Município: ' . $prop_parc_municipio .
        '; Estado: ' . $prop_parc_estado .
        '; PAis: ' . $prop_parc_pais .
        '; Responsável: ' . $prop_parc_responsavel .
        '; Cargo: ' . $prop_parc_cargo .
        '; Cont. referência: ' . $prop_parc_contato_referencia .
        '; Possui convêncio: ' . $prop_parc_possui_convenio .
        '; Tipo parceria: ' . $prop_parc_tipo_parceria .
        '; Tit. atividade: ' . $prop_parc_titulo_atividade .
        '; Objetivo Atividade: ' . $prop_parc_objetivo_atividade .
        '; Local atividade: ' . $prop_parc_local_atividade .
        '; Tipo Espaço: ' . $prop_parc_tipo_espaco .
        '; Campus: ' . $prop_parc_campus_atividade .
        '; Carga horária: ' . $prop_parc_carga_hora .
        '; Data Atividade: ' . $prop_parc_data_atividade .
        '; Hora Inicial: ' . $prop_parc_hora_atividade_inicial .
        '; Hora Final: ' . $prop_parc_hora_atividade_final .
        '; Numero Participantes: ' . $prop_parc_numero_participantes .
        '; Recursos Necessários: ' . $prop_parc_recursos_necessarios .
        '; Benefícios: ' . $prop_parc_beneficios .
        '; Quant. Benefícios: ' . $prop_parc_beneficios_quantidade .
        '; Organização: ' . $prop_parc_organizacao_espaco .
        '; Comentários: ' . $prop_parc_comentarios,

      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));
    // -------------------------------

    // CADASTRA AS IMAGENS
    if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

      $parq_categoria    = 3; // CATEGORIA DE ARQUIVO - PROPOSTA = 3
      $arquivos = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

      // 10MB
      $maxFileSize = 10 * 1024 * 1024;
      foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
        if (!empty($fileName)) {
          $fileSize = $_FILES["arquivos"]["size"][$key];
          if ($fileSize > $maxFileSize) {
            $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> O arquivo excede o tamanho máximo permitido de 2MB.";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#parq_ancora");
            return die;
          }
        }
      }
      // -------------------------------

      // IMPEDE QUE MAIS DE 20 ARQUIVOS SEJAM ENVIADOS
      $uploadedFilesCount = count($_FILES["arquivos"]["name"]);
      if ($uploadedFilesCount > 20) {
        $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Você pode enviar no máximo 5 arquivos.";
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
        header("Location: $referer#parq_ancora");
        return die;
      }
      // -------------------------------

      // CRIA AS PASTAS DOS ARQUIVOS
      $pastaPrincipal = "../uploads/propostas/$prop_codigo";
      $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
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

        $arquivos_permitidos = ['pdf', 'PDF', 'xlsx', 'xls', 'XLSX', 'XLS', 'doc', 'DOC', 'docx', 'DOCX', 'csv', 'CSV', 'jpg', 'JPG', 'jpeg', 'png', 'PNG']; //FORMATO DE ARQUIVOS PERMITIDOS
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
            $stmt->bindParam(":parq_prop_id", $prop_id);
            $stmt->bindParam(":parq_codigo", $prop_codigo);
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
              ':modulo'     => 'PROPOSTA - PARCERIAS - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $last_id,
              ':dados'      => 'Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
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
            $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
          }
          // -------------------------------

        } else {
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Formato de arquivo inválido!";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'painel.php';
          header("Location: $referer");
          return die;
        }
      }
    }

    // CADASTRA O STATUS DA PROPOSTA
    $num_status  = 2; // CADASTRO CONCLUÍDO
    $sql = "INSERT INTO propostas_status (
                                          prop_sta_prop_id,
                                          prop_sta_status,
                                          prop_sta_user_id,
                                          prop_sta_data_cad
                                          ) VALUES (
                                          :prop_sta_prop_id,
                                          :prop_sta_status,
                                          :prop_sta_user_id,
                                          :prop_sta_data_cad
                                          )";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_sta_prop_id", $prop_id); // ID DA PROPOSTA
    $stmt->bindParam(":prop_sta_status", $num_status);
    $stmt->bindParam(":prop_sta_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":prop_sta_data_cad", date('Y-m-d H:i:s'));
    $stmt->execute();
    // -------------------------------

    // CADASTRA O STATUS DA ANÁLISE
    $sql = "INSERT INTO propostas_analise_status (
                                                  sta_an_prop_id,
                                                  sta_an_status,
                                                  sta_an_user_id,
                                                  sta_an_data_cad,
                                                  sta_an_data_upd
                                                  ) VALUES (
                                                  :sta_an_prop_id,
                                                  :sta_an_status,
                                                  :sta_an_user_id,
                                                  :sta_an_data_cad,
                                                  :sta_an_data_upd
                                                  )";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sta_an_prop_id", $prop_id);
    $stmt->bindParam(":sta_an_status", $num_status);
    $stmt->bindParam(":sta_an_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":sta_an_data_cad", date('Y-m-d H:i:s'));
    $stmt->bindParam(":sta_an_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();
    // -------------------------------

    try {
      // ENVIA E-MAIL
      $mail = new PHPMailer(true);
      include '../controller/email_conf.php';
      include '../includes/email/send_email.php'; // CONFIGURAÇÃO DE E-MAILS
      $mail->addAddress($email_extensao); // E-MAIL DA EXTENSÃO
      $mail->addAddress($_SESSION['email']); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
      $mail->isHTML(true);
      $mail->Subject = 'COD:' . $prop_codigo . ' - Proposta Iniciada'; //TÍTULO DO E-MAIL

      //RECUPERA URL PARA O LINK DO EMAIL
      $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
      $url = $_SERVER['REQUEST_URI'];
      $path = parse_url($url, PHP_URL_PATH);
      $directories = explode('/', $path);
      array_shift($directories);
      $pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0];

      // CORPO DO EMAIL
      include '../includes/email/email_header_800.php';
      $email_conteudo .= "
      <p style='width: 100%; font-size: 1.188rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px; display: inline-block;'>
      Cadastro da proposta Concluída!
      </p>

      <p style='font-size: 1rem;'>
      A equipe de extensão irá analisar a sua proposta em breve.<br>
      Depois que a análise começar, você não poderá alterar os dados.
      </p>

      <a style='cursor: pointer;' href='$pagina'><button style='background: #62B790; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 13px 20px; margin-top: 20px;' target='_blank'>ACESSE O SISTEMA</button></a>";
      include '../includes/email/email_footer.php';

      $mail->Body  = $email_conteudo;
      $mail->send();
    } catch (Exception $e) {
      //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Erro no enviar o e-mail!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
      return die;
    }

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> O cadastro da proposta foi concluído!";
    header("Location: ../painel.php");
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}











/*****************************************************************************************
                                EDITAR PROPOSTA PARCERIAS
 *****************************************************************************************/
if (isset($dados['EditPropostasParcerias'])) {

  // SE TIPO NÃO FOR ENCONTRADO IMPEDE O CADASTRO
  $tipos = array(1, 2, 3, 4, 5);
  if (!in_array($_POST['prop_tipo'], $tipos)) {
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark\"></i> Cadastro não realizado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $prop_id                         = $_POST['prop_id'];
  $prop_tipo                       = $_POST['prop_tipo'];
  $prop_codigo                     = $_POST['prop_codigo'];

  // NÚMERO DA ETAPA 
  if ($_POST['prop_status_etapa'] != 5) {
    $prop_status_etapa = 5;
  } else {
    $prop_status_etapa = 5;
  }
  // -------------------------------

  $prop_parc_nome_empresa           = trim($_POST['prop_parc_nome_empresa']);
  $prop_parc_tipo_empresa           = $_POST['prop_parc_tipo_empresa'];
  //
  if ($prop_parc_tipo_empresa == '1') {
    $prop_parc_tipo_outro           = trim($_POST['prop_parc_tipo_outro']);
  } else {
    $prop_parc_tipo_outro           = NULL;
  }
  //
  if ($prop_parc_tipo_empresa == '4') {
    $prop_parc_orgao_empresa        = trim($_POST['prop_parc_orgao_empresa']);
  } else {
    $prop_parc_orgao_empresa        = NULL;
  }
  //  
  $prop_parc_email                  = $_POST['prop_parc_email'];
  $prop_parc_telefone               = $_POST['prop_parc_telefone'];
  $prop_parc_cep                    = $_POST['prop_parc_cep'];
  $prop_parc_logradouro             = trim($_POST['prop_parc_logradouro']);
  $prop_parc_numero                 = $_POST['prop_parc_numero'];
  $prop_parc_bairro                 = trim($_POST['prop_parc_bairro']);
  $prop_parc_municipio              = trim($_POST['prop_parc_municipio']);
  $prop_parc_estado                 = trim($_POST['prop_parc_estado']);
  $prop_parc_pais                   = trim($_POST['prop_parc_pais']);
  $prop_parc_responsavel            = trim($_POST['prop_parc_responsavel']);
  $prop_parc_cargo                  = trim($_POST['prop_parc_cargo']);
  $prop_parc_contato_referencia     = trim($_POST['prop_parc_contato_referencia']);
  $prop_parc_possui_convenio        = isset($_POST['prop_parc_possui_convenio']) ? $_POST['prop_parc_possui_convenio'] : 0;
  $prop_parc_tipo_parceria          = $_POST['prop_parc_tipo_parceria'];
  $prop_parc_titulo_atividade       = trim($_POST['prop_parc_titulo_atividade']);
  $prop_parc_objetivo_atividade     = nl2br(trim($_POST['prop_parc_objetivo_atividade']));
  $prop_parc_carga_hora             = $_POST['prop_parc_carga_hora'];
  $prop_parc_data_atividade         = isset($_POST['prop_parc_data_atividade']) ? $_POST['prop_parc_data_atividade'] : NULL;
  $prop_parc_hora_atividade_inicial = $_POST['prop_parc_hora_atividade_inicial'];
  $prop_parc_hora_atividade_final   = $_POST['prop_parc_hora_atividade_final'];
  $prop_parc_local_atividade        = trim($_POST['prop_parc_local_atividade']);
  $prop_parc_tipo_espaco            = $_POST['prop_parc_tipo_espaco'];
  $prop_parc_campus_atividade       = $_POST['prop_parc_campus_atividade'];
  $prop_parc_numero_participantes   = $_POST['prop_parc_numero_participantes'];
  $prop_parc_recursos_necessarios   = nl2br(trim($_POST['prop_parc_recursos_necessarios']));
  $prop_parc_organizacao_espaco     = $_POST['prop_parc_organizacao_espaco'];
  $prop_parc_beneficios             = $_POST['prop_parc_beneficios'];
  //
  if ($prop_parc_beneficios == 'SIM') {
    $prop_parc_beneficios_quantidade = nl2br(trim($_POST['prop_parc_beneficios_quantidade']));
  } else {
    $prop_parc_beneficios_quantidade = NULL;
  }
  //  
  $prop_parc_comentarios            = nl2br(trim($_POST['prop_parc_comentarios']));
  // -------------------------------

  try {
    $sql = "UPDATE
                    propostas
              SET
                    prop_status_etapa                = :prop_status_etapa,
                    prop_titulo                      = UPPER(:prop_titulo),
                    prop_parc_nome_empresa           = UPPER(:prop_parc_nome_empresa),
                    prop_parc_tipo_empresa           = :prop_parc_tipo_empresa,
                    prop_parc_tipo_outro             = UPPER(:prop_parc_tipo_outro),
                    prop_parc_orgao_empresa          = UPPER(:prop_parc_orgao_empresa),
                    prop_parc_email                  = LOWER(:prop_parc_email),
                    prop_parc_telefone               = :prop_parc_telefone,
                    prop_parc_cep                    = :prop_parc_cep,
                    prop_parc_logradouro             = UPPER(:prop_parc_logradouro),
                    prop_parc_numero                 = UPPER(:prop_parc_numero),
                    prop_parc_bairro                 = UPPER(:prop_parc_bairro),
                    prop_parc_municipio              = UPPER(:prop_parc_municipio),
                    prop_parc_estado                 = UPPER(:prop_parc_estado),
                    prop_parc_pais                   = UPPER(:prop_parc_pais),
                    prop_parc_responsavel            = UPPER(:prop_parc_responsavel),
                    prop_parc_cargo                  = UPPER(:prop_parc_cargo),
                    prop_parc_contato_referencia     = UPPER(:prop_parc_contato_referencia),
                    prop_parc_possui_convenio        = :prop_parc_possui_convenio,
                    prop_parc_tipo_parceria          = :prop_parc_tipo_parceria,
                    prop_parc_titulo_atividade       = UPPER(:prop_parc_titulo_atividade),
                    prop_parc_objetivo_atividade     = :prop_parc_objetivo_atividade,
                    prop_parc_carga_hora             = :prop_parc_carga_hora,
                    prop_parc_data_atividade         = :prop_parc_data_atividade,
                    prop_parc_hora_atividade_inicial = :prop_parc_hora_atividade_inicial,
                    prop_parc_hora_atividade_final   = :prop_parc_hora_atividade_final,
                    prop_parc_local_atividade        = UPPER(:prop_parc_local_atividade),
                    prop_parc_tipo_espaco            = UPPER(:prop_parc_tipo_espaco),
                    prop_parc_campus_atividade       = :prop_parc_campus_atividade,
                    prop_parc_numero_participantes   = :prop_parc_numero_participantes,
                    prop_parc_recursos_necessarios   = :prop_parc_recursos_necessarios,
                    prop_parc_beneficios             = UPPER(:prop_parc_beneficios),
                    prop_parc_beneficios_quantidade  = :prop_parc_beneficios_quantidade,
                    prop_parc_organizacao_espaco     = :prop_parc_organizacao_espaco,
                    prop_parc_comentarios            = :prop_parc_comentarios,
                    prop_data_upd                    = :prop_data_upd
              WHERE
                    prop_id                          = :prop_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":prop_id", $prop_id);
    $stmt->bindParam(":prop_status_etapa", $prop_status_etapa);
    $stmt->bindParam(":prop_titulo", $prop_parc_nome_empresa);
    //
    $stmt->bindParam(":prop_parc_nome_empresa", $prop_parc_nome_empresa);
    $stmt->bindParam(":prop_parc_tipo_empresa", $prop_parc_tipo_empresa);
    $stmt->bindParam(":prop_parc_tipo_outro", $prop_parc_tipo_outro);
    $stmt->bindParam(":prop_parc_orgao_empresa", $prop_parc_orgao_empresa);
    $stmt->bindParam(":prop_parc_email", $prop_parc_email);
    $stmt->bindParam(":prop_parc_telefone", $prop_parc_telefone);
    $stmt->bindParam(":prop_parc_cep", $prop_parc_cep);
    $stmt->bindParam(":prop_parc_logradouro", $prop_parc_logradouro);
    $stmt->bindParam(":prop_parc_numero", $prop_parc_numero);
    $stmt->bindParam(":prop_parc_bairro", $prop_parc_bairro);
    $stmt->bindParam(":prop_parc_municipio", $prop_parc_municipio);
    $stmt->bindParam(":prop_parc_estado", $prop_parc_estado);
    $stmt->bindParam(":prop_parc_pais", $prop_parc_pais);
    $stmt->bindParam(":prop_parc_responsavel", $prop_parc_responsavel);
    $stmt->bindParam(":prop_parc_cargo", $prop_parc_cargo);
    $stmt->bindParam(":prop_parc_contato_referencia", $prop_parc_contato_referencia);
    $stmt->bindParam(":prop_parc_possui_convenio", $prop_parc_possui_convenio);
    $stmt->bindParam(":prop_parc_tipo_parceria", $prop_parc_tipo_parceria);
    $stmt->bindParam(":prop_parc_titulo_atividade", $prop_parc_titulo_atividade);
    $stmt->bindParam(":prop_parc_objetivo_atividade", $prop_parc_objetivo_atividade);
    $stmt->bindParam(":prop_parc_carga_hora", $prop_parc_carga_hora);
    $stmt->bindParam(":prop_parc_data_atividade", $prop_parc_data_atividade);
    $stmt->bindParam(":prop_parc_hora_atividade_inicial", $prop_parc_hora_atividade_inicial);
    $stmt->bindParam(":prop_parc_hora_atividade_final", $prop_parc_hora_atividade_final);
    $stmt->bindParam(":prop_parc_local_atividade", $prop_parc_local_atividade);
    $stmt->bindParam(":prop_parc_tipo_espaco", $prop_parc_tipo_espaco);
    $stmt->bindParam(":prop_parc_campus_atividade", $prop_parc_campus_atividade);
    $stmt->bindParam(":prop_parc_numero_participantes", $prop_parc_numero_participantes);
    $stmt->bindParam(":prop_parc_recursos_necessarios", $prop_parc_recursos_necessarios);
    $stmt->bindParam(":prop_parc_beneficios", $prop_parc_beneficios);
    $stmt->bindParam(":prop_parc_beneficios_quantidade", $prop_parc_beneficios_quantidade);
    $stmt->bindParam(":prop_parc_organizacao_espaco", $prop_parc_organizacao_espaco);
    $stmt->bindParam(":prop_parc_comentarios", $prop_parc_comentarios);
    //
    $stmt->bindParam(":prop_data_upd", date('Y-m-d H:i:s'));
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'PROPOSTA - PARCERIAS',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $prop_id,
      ':dados'      => 'Empresa: ' . $prop_parc_nome_empresa .
        '; Tipo empresa: ' . $prop_parc_tipo_empresa .
        '; Tipo (outro): ' . $prop_parc_tipo_outro .
        '; Órgão: ' . $prop_parc_orgao_empresa .
        '; E-amil: ' . $prop_parc_email .
        '; Contato: ' . $prop_parc_telefone .
        '; prop_parc_cep: ' . $prop_parc_cep .
        '; CEP: ' . $prop_parc_logradouro .
        '; Rua: ' . $prop_parc_numero .
        '; Bairro: ' . $prop_parc_bairro .
        '; Município: ' . $prop_parc_municipio .
        '; Estado: ' . $prop_parc_estado .
        '; PAis: ' . $prop_parc_pais .
        '; Responsável: ' . $prop_parc_responsavel .
        '; Cargo: ' . $prop_parc_cargo .
        '; Cont. referência: ' . $prop_parc_contato_referencia .
        '; Possui convêncio: ' . $prop_parc_possui_convenio .
        '; Tipo parceria: ' . $prop_parc_tipo_parceria .
        '; Tit. atividade: ' . $prop_parc_titulo_atividade .
        '; Objetivo Atividade: ' . $prop_parc_objetivo_atividade .
        '; Local atividade: ' . $prop_parc_local_atividade .
        '; Tipo Espaço: ' . $prop_parc_tipo_espaco .
        '; Campus: ' . $prop_parc_campus_atividade .
        '; Carga horária: ' . $prop_parc_carga_hora .
        '; Data Atividade: ' . $prop_parc_data_atividade .
        '; Hora Inicial: ' . $prop_parc_hora_atividade_inicial .
        '; Hora Final: ' . $prop_parc_hora_atividade_final .
        '; Numero Participantes: ' . $prop_parc_numero_participantes .
        '; Recursos Necessários: ' . $prop_parc_recursos_necessarios .
        '; Benefícios: ' . $prop_parc_beneficios .
        '; Quant. Benefícios: ' . $prop_parc_beneficios_quantidade .
        '; Organização: ' . $prop_parc_organizacao_espaco .
        '; Comentários: ' . $prop_parc_comentarios,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => date('Y-m-d H:i:s')
    ));

    // CADASTRA AS IMAGENS
    if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

      $parq_categoria    = 3; // CATEGORIA DE ARQUIVO - PROPOSTA = 3
      $arquivos = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

      // 10MB
      $maxFileSize = 10 * 1024 * 1024;
      foreach ($_FILES["arquivos"]["name"] as $key => $fileName) { // CALCULA O TAMANHO DOS ARQUIVOS ENVIADOS
        if (!empty($fileName)) {
          $fileSize = $_FILES["arquivos"]["size"][$key];
          if ($fileSize > $maxFileSize) {
            $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> O arquivo excede o tamanho máximo permitido de 2MB.";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
            header("Location: $referer#parq_ancora");
            return die;
          }
        }
      }
      // -------------------------------

      // IMPEDE QUE MAIS DE 20 ARQUIVOS SEJAM ENVIADOS
      $uploadedFilesCount = count($_FILES["arquivos"]["name"]);
      if ($uploadedFilesCount > 20) {
        $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Você pode enviar no máximo 5 arquivos.";
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cad_proposta.php';
        header("Location: $referer#parq_ancora");
        return die;
      }
      // -------------------------------

      // CRIA AS PASTAS DOS ARQUIVOS
      $pastaPrincipal = "../uploads/propostas/$prop_codigo";
      $SubPasta = "../uploads/propostas/$prop_codigo/$parq_categoria";
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

        $arquivos_permitidos = ['pdf', 'xlsx', 'xls', 'doc', 'docx', 'csv', 'jpg', 'JPG', 'jpeg', 'png', 'PNG']; //FORMATO DE ARQUIVOS PERMITIDOS
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
            $stmt->bindParam(":parq_prop_id", $prop_id);
            $stmt->bindParam(":parq_codigo", $prop_codigo);
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
              ':modulo'     => 'PROPOSTA - PARCERIAS - ARQUIVOS',
              ':acao'       => 'CADASTRO',
              ':acao_id'    => $last_id,
              ':dados'      => 'Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
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
            $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/propostas/' . $prop_codigo . '/' . $parq_categoria . '/' . $nomes[$i]);
          }
          // -------------------------------

        } else {
          $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-xmark me-2\"></i> Formato de arquivo inválido!";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'painel.php';
          header("Location: $referer");
          return die;
        }
      }
    }

    // SE 'STATUS_ETAPA' FOR DIFERENTE DE 5, QUER DIZER QUE O STATUS AINDA NÃO FOI ATUALIZADO
    if ($_POST['prop_status_etapa'] != 5) {

      // ALTERA O STATUS DA PROPOSTA
      $num_status = 2; // CADASTRO CONCLUÍDO
      $sql = "UPDATE
                    propostas_status
              SET        
                    prop_sta_status   = :prop_sta_status,
                    prop_sta_user_id  = :prop_sta_user_id,
                    prop_sta_data_cad = :prop_sta_data_cad
              WHERE
                    prop_sta_prop_id  = :prop_sta_prop_id";

      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":prop_sta_prop_id", $prop_id);
      $stmt->bindParam(":prop_sta_status", $num_status);
      $stmt->bindParam(":prop_sta_user_id", $_SESSION['reservm_admin_id']);
      $stmt->bindParam(":prop_sta_data_cad", date('Y-m-d H:i:s'));
      $stmt->execute();
      // -------------------------------

      // CADASTRA O STATUS DA ANÁLISE
      $sql = "INSERT INTO propostas_analise_status (
                                                  sta_an_prop_id,
                                                  sta_an_status,
                                                  sta_an_user_id,
                                                  sta_an_data_cad,
                                                  sta_an_data_upd
                                                  ) VALUES (
                                                  :sta_an_prop_id,
                                                  :sta_an_status,
                                                  :sta_an_user_id,
                                                  :sta_an_data_cad,
                                                  :sta_an_data_upd
                                                  )";

      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":sta_an_prop_id", $prop_id);
      $stmt->bindParam(":sta_an_status", $num_status);
      $stmt->bindParam(":sta_an_user_id", $_SESSION['reservm_admin_id']);
      $stmt->bindParam(":sta_an_data_cad", date('Y-m-d H:i:s'));
      $stmt->bindParam(":sta_an_data_upd", date('Y-m-d H:i:s'));
      $stmt->execute();
    }
    // -------------------------------

    $_SESSION["msg"] = "<i class=\"fa-solid fa-circle-check\"></i> O cadastro da proposta foi concluído!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "<i class=\"fa-solid fa-circle-check\"></i> Cadastro não realizado!" . $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    return die;
  }
}
