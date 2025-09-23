<?php
//session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {

  try {

    $conn->beginTransaction();

    $solic_acao = isset($_POST['solic_acao']) ? trim($_POST['solic_acao']) : null;

    if ($solic_acao === 'cadastrar' || $solic_acao === 'atualizar') {

      $solic_id    = bin2hex(random_bytes(16)); // GERA UM ID ÚNICO SEGURO

      // FUNÇÃO PARA VERIFICAR SE O CÓDIGO JÁ EXISTE
      function verificarCodigoNoBanco($solic_codigo, $conn)
      {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM solicitacao WHERE solic_codigo = :solic_codigo");
        $stmt->bindParam(':solic_codigo', $solic_codigo);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
      }

      // Gerar código único com tentativa de inserção
      $tentativas = 0;
      do {
        $tentativas++;
        if ($tentativas > 5) {
          throw new Exception("Não foi possível gerar um código único após várias tentativas.");
        }

        $solic_codigo = 'SO' . random_int(100000, 999999);
        $existe = verificarCodigoNoBanco($solic_codigo, $conn);
      } while ($existe);
      // ----------------------------------------------

      $solic_etapa = 3;


      ///////////////////
      // IDENTIFICAÇÃO //
      ///////////////////

      $solic_curso = trim($_POST['solic_curso']);

      // 2	BIOMEDICINA
      // 5	EDUCAÇÃO FÍSICA
      // 6	ENFERMAGEM
      // 9	FISIOTERAPIA
      // 13	LIGA ACADÊMICA
      // 14	MEDICINA
      // 17	NÚCLEO COMUM
      // 18	ODONTOLOGIA
      // 21	PSICOLOGIA
      if (in_array($solic_curso, [2, 5, 6, 9, 13, 14, 17, 18, 21])) {
        $solic_comp_curric = isset($_POST['solic_comp_curric']) ? trim($_POST['solic_comp_curric']) : null;
      }

      // 7	EXTENSÃO
      // 10	GRUPO DE PESQUISA
      // 19	PROGRAMA CANDEAL
      // 28	NIDD
      // 31	RESERVAS ADMINISTRATIVAS
      if (in_array($solic_curso, [7, 10, 19, 28, 31])) {
        $solic_nome_atividade  = isset($_POST['solic_nome_atividade']) ? trim($_POST['solic_nome_atividade']) : null;
      }

      // 8	EXTENSÃO CURRICULARIZADA
      if (in_array($solic_curso, [8])) {
        $solic_nome_curso      = isset($_POST['solic_nome_curso']) ? trim($_POST['solic_nome_curso']) : null;
        $solic_nome_atividade  = isset($_POST['solic_nome_atividade']) ? trim($_POST['solic_nome_atividade']) : null;
        $solic_semestre        = isset($_POST['solic_semestre']) ? trim($_POST['solic_semestre']) : null;
      }

      // 11	LATO SENSU
      // 22	STRICTO SENSU
      if (in_array($solic_curso, [11, 22])) {
        $solic_nome_curso_text = isset($_POST['solic_nome_curso_text']) ? trim($_POST['solic_nome_curso_text']) : null;
        $solic_nome_comp_ativ  = isset($_POST['solic_nome_comp_ativ']) ? trim($_POST['solic_nome_comp_ativ']) : null;
        $solic_semestre        = isset($_POST['solic_semestre']) ? trim($_POST['solic_semestre']) : null;
      }


      // NOME PROFESSOR E CONTATO
      $solic_nome_prof_resp  = isset($_POST['solic_nome_prof_resp']) ? trim($_POST['solic_nome_prof_resp']) : null;
      $solic_contato         = isset($_POST['solic_contato']) ? trim($_POST['solic_contato']) : null;
    }

    $solic_admin_id = $_SESSION['reservm_admin_id'];

    // -------------------------------
    // CADASTRAR
    // -------------------------------
    if ($solic_acao === 'cadastrar') {

      $num_status  = 5; // AGUARDANDO ANÁLISE (OU QUALQUER QUE SEJA O ID DO SEU STATUS PENDENTE)
      $log_acao = 'Cadastro';

      $sql = "INSERT INTO solicitacao (
                                        solic_id,
                                        solic_codigo,
                                        solic_etapa,
                                        solic_curso,
                                        solic_comp_curric,
                                        solic_nome_curso,
                                        solic_nome_curso_text,
                                        solic_nome_atividade,
                                        solic_nome_comp_ativ,
                                        solic_semestre,
                                        solic_nome_prof_resp,
                                        solic_contato,
                                        solic_cad_por,
                                        solic_data_cad,
                                        solic_upd_por,
                                        solic_data_upd
                                      ) VALUES (
                                        :solic_id,
                                        :solic_codigo,
                                        :solic_etapa,
                                        :solic_curso,
                                        :solic_comp_curric,
                                        :solic_nome_curso,
                                        upper(:solic_nome_curso_text),
                                        upper(:solic_nome_atividade),
                                        upper(:solic_nome_comp_ativ),
                                        :solic_semestre,
                                        upper(:solic_nome_prof_resp),
                                        :solic_contato,
                                        :solic_cad_por,
                                        GETDATE(),
                                        :solic_upd_por,
                                        GETDATE()
                                      )";

      $stmt = $conn->prepare($sql);

      // Bind dos parâmetros para evitar SQL Injection
      $stmt->bindParam(':solic_id', $solic_id, PDO::PARAM_STR);
      $stmt->bindParam(':solic_codigo', $solic_codigo, PDO::PARAM_STR);
      $stmt->bindParam(':solic_etapa', $solic_etapa, PDO::PARAM_INT);
      $stmt->bindParam(':solic_curso', $solic_curso, PDO::PARAM_INT);
      $stmt->bindParam(':solic_comp_curric', $solic_comp_curric, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_curso', $solic_nome_curso, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_curso_text', $solic_nome_curso_text, PDO::PARAM_STR);
      $stmt->bindParam(':solic_nome_atividade', $solic_nome_atividade, PDO::PARAM_STR);
      $stmt->bindParam(':solic_nome_comp_ativ', $solic_nome_comp_ativ, PDO::PARAM_STR);
      $stmt->bindParam(':solic_semestre', $solic_semestre, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_prof_resp', $solic_nome_prof_resp, PDO::PARAM_STR);
      $stmt->bindParam(':solic_contato', $solic_contato, PDO::PARAM_STR);
      $stmt->bindParam(':solic_cad_por', $solic_admin_id, PDO::PARAM_STR);
      $stmt->bindParam(':solic_upd_por', $solic_admin_id, PDO::PARAM_STR);
      $stmt->execute();


      // CADASTRA O STATUS DA SOLICITAÇÃO
      $sql = "INSERT INTO solicitacao_status (solic_sta_solic_id,solic_sta_status, solic_sta_user_id, solic_sta_data_cad) VALUES (:solic_sta_solic_id, :solic_sta_status, :solic_sta_user_id, GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':solic_sta_solic_id' => $solic_id, ':solic_sta_status'  => $num_status, ':solic_sta_user_id' => $solic_admin_id]);

      // CADASTRA O STATUS DA ANÁLISE
      $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_user_id, GETDATE(), GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':sta_an_solic_id' => $solic_id, ':sta_an_status'  => $num_status, ':sta_an_user_id' => $solic_admin_id]);


      // -------------------------------
      // ATUALIZAÇÃO
      // -------------------------------
    } elseif ($solic_acao === 'atualizar') {

      if (empty($_POST['solic_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $solic_id = $_POST['solic_id'];
      $log_acao = 'Atualização';

      // VERIFICA O ANDAMENTO DAS ETAPAS DO CADASTRO
      $sqlVerifica = "SELECT solic_id, solic_etapa FROM solicitacao WHERE solic_id = :solic_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':solic_id' => $solic_id]);
      $result = $stmtVerifica->fetch(PDO::FETCH_ASSOC); // Pega apenas uma linha
      if ($result) { // Verifica se a consulta retornou resultado
        if ($result['solic_etapa'] == 3) {
          $solic_etapa = 3;
        } elseif ($result['solic_etapa'] == 2) {
          $solic_etapa = 2;
        } else {
          $solic_etapa = 1;
        }
      }

      $sql = "UPDATE solicitacao SET
                                      solic_etapa           = :solic_etapa,
                                      solic_curso           = :solic_curso,
                                      solic_comp_curric     = :solic_comp_curric,
                                      solic_nome_curso      = :solic_nome_curso,
                                      solic_nome_curso_text = upper(:solic_nome_curso_text),
                                      solic_nome_atividade  = upper(:solic_nome_atividade),
                                      solic_nome_comp_ativ  = upper(:solic_nome_comp_ativ),
                                      solic_semestre        = :solic_semestre,
                                      solic_nome_prof_resp  = upper(:solic_nome_prof_resp),
                                      solic_contato         = :solic_contato,
                                      solic_upd_por         = :solic_upd_por,
                                      solic_data_upd        = GETDATE()
                                WHERE
                                      solic_id = :solic_id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':solic_id', $solic_id, PDO::PARAM_STR);
      $stmt->bindParam(':solic_etapa', $solic_etapa, PDO::PARAM_INT);
      $stmt->bindParam(':solic_curso', $solic_curso, PDO::PARAM_INT);
      $stmt->bindParam(':solic_comp_curric', $solic_comp_curric, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_curso', $solic_nome_curso, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_curso_text', $solic_nome_curso_text, PDO::PARAM_STR);
      $stmt->bindParam(':solic_nome_atividade', $solic_nome_atividade, PDO::PARAM_STR);
      $stmt->bindParam(':solic_nome_comp_ativ', $solic_nome_comp_ativ, PDO::PARAM_STR);
      $stmt->bindParam(':solic_semestre', $solic_semestre, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_prof_resp', $solic_nome_prof_resp, PDO::PARAM_STR);
      $stmt->bindParam(':solic_contato', $solic_contato, PDO::PARAM_STR);
      $stmt->bindParam(':solic_upd_por', $solic_admin_id, PDO::PARAM_STR);
      $stmt->execute();

      // -------------------------------
      // EXCLUIR ARQUIVO
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar_arq') {

      if (empty($_GET['sarq_id']) || empty($_GET['sarq_codigo']) || empty($_GET['sarq_arquivo'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }

      $sarq_id      = $_GET['sarq_id'];
      $sarq_codigo = $_GET['sarq_codigo'];
      $sarq_arquivo = $_GET['sarq_arquivo'];
      $log_acao     = 'Exclusão Arquivo';
      $solic_id     = $sarq_id; // PARA O LOG

      // EXCLUIR A PROPOSTA PRINCIPAL
      $sql = "DELETE FROM solicitacao_arq WHERE sarq_id = :sarq_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':sarq_id' => $sarq_id]);
      // -------------------------------

      $apaga_img = unlink("../uploads/solicitacoes/$sarq_codigo/$sarq_arquivo"); //APAGA O ARQUIVO ANTIGO
      // -------------------------------

      // -------------------------------
      // EXCLUIR SOLICITAÇÃO
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      if (empty($_GET['solic_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $solic_id     = $_GET['solic_id'];
      $solic_codigo = $_GET['solic_codigo'];
      $log_acao = 'Exclusão';

      // LISTAR TODAS AS TABELAS DEPENDENTES
      $tabelas = [
        'reservas'                   => 'res_solic_id',
        'solicitacao_analise_status' => 'sta_an_solic_id',
        'solicitacao_arq'            => 'sarq_solic_id',
        'solicitacao_status'         => 'solic_sta_solic_id',
        'ocorrencias'                => 'oco_solic_id'
      ];

      // EXCLUIR REGISTROS DE TABELAS RELACIONADAS
      foreach ($tabelas as $tabela => $coluna) {
        $sql = "DELETE FROM $tabela WHERE $coluna = :solic_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':solic_id' => $solic_id]);
      }
      // -------------------------------

      // EXCLUIR A PROPOSTA PRINCIPAL
      $sql = "DELETE FROM solicitacao WHERE solic_id = :solic_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':solic_id' => $solic_id]);
      // -------------------------------

      // FUNÇÃO PARA EXCLUIR ARQUIVOS E PASTAS
      function excluirArquivos($caminho)
      {
        // VERIFICA SE O DIRETÓRIO EXISTE
        // • A função recebe o caminho da pasta ($caminho).
        // • Se o diretório não existir, a função simplesmente retorna e não faz nada.
        // • Isso evita erros ao tentar excluir algo que já foi apagado ou nunca existiu.
        if (!is_dir($caminho)) {
          return;
        }

        // OBTÉM TODOS OS ARQUIVOS DA PASTA
        // • A função glob("$caminho/*") retorna um array com todos os arquivos e subpastas dentro do diretório.
        // • Se a pasta estiver vazia, glob() pode retornar false.
        $files = glob("$caminho/*");

        //EXCLUI OS ARQUIVOS ENCONTRADOS
        // • Se a pasta não estiver vazia, percorremos cada item encontrado.
        // • O is_file($file) garante que estamos excluindo apenas arquivos (e não pastas).
        // • O unlink($file) remove cada arquivo individualmente.
        if ($files) {
          foreach ($files as $file) {
            if (is_file($file)) {
              unlink($file);
            }
          }
        }

        // REMOVE A PASTA DEPOIS DE ESVAZIÁ-LA
        // • Após remover todos os arquivos, chamamos rmdir($caminho) para excluir a pasta.
        // • O rmdir() só funciona se a pasta estiver vazia.
        rmdir($caminho);
      }
      // -------------------------------

      // CAMINHO DAS PASTAS A SEREM EXCLUÍDAS
      $pastas = [
        "../uploads/solicitacoes/$solic_codigo"
      ];

      // EXCLUIR ARQUIVOS E DIRETÓRIOS
      foreach ($pastas as $pasta) {
        excluirArquivos($pasta);
      }
      // -------------------------------

    } else {
      throw new Exception("Ação inválida!");
    }

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute(array(
      ':modulo'  => 'SOLICITAÇÃO ADMIN',
      ':acao'    => $log_acao,
      ':acao_id' => $solic_id,
      ':dados'   => json_encode($_POST),
      ':user_id' => $solic_admin_id
    ));
    // -------------------------------

    // Confirmar a transação
    $conn->commit();

    // CONFIGURAÇÃO DE MENSAGEM
    if ($solic_acao === 'cadastrar') {
      header(sprintf("location: ../admin/solicitacao_analise.php?i={$solic_id}"));
    } elseif ($solic_acao === 'atualizar_2') {
      header(sprintf("location: ../nova_solicitacao.php?st=3&i={$solic_id}"));
    } elseif ($solic_acao === 'atualizar_3') {
      $_SESSION["msg"] = "Solicitação realizado com sucesso!" . $arquivos;
      header(sprintf("location: ../painel.php"));
    } elseif ($_GET['acao'] === 'deletar_arq') {
      $_SESSION["msg"] = "Arquivo excluído com sucesso!";
      header(sprintf('location: %s#file_ancora', $_SERVER['HTTP_REFERER']));
    } else {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf("location: ../painel.php"));
    }

    exit();
  } catch (PDOException $e) {

    // Reverter a transação em caso de erro
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit();
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  exit();
}
