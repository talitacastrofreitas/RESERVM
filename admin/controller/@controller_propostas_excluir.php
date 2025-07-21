<?php
session_start();
include '../../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {

  $prop_id       = base64_decode($_GET['i']);
  $prop_codigo   = base64_decode($_GET['cod']);
  $reservm_admin_id = $_SESSION['reservm_admin_id'];

  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    $sql = "DELETE FROM propostas WHERE prop_id = :prop_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':prop_id' => $prop_id]);

    $sql_pcp = "DELETE FROM propostas_coordenador_projeto WHERE pcp_proposta_id = :prop_id";
    $stmt_pcp = $conn->prepare($sql_pcp);
    $stmt_pcp->execute([':prop_id' => $prop_id]);

    $sql_pex = "DELETE FROM propostas_equipe_executora WHERE pex_proposta_id = :prop_id";
    $stmt_pex = $conn->prepare($sql_pex);
    $stmt_pex->execute([':prop_id' => $prop_id]);

    $sql_ppe = "DELETE FROM propostas_parceiro_externo WHERE ppe_proposta_id = :prop_id";
    $stmt_ppe = $conn->prepare($sql_ppe);
    $stmt_ppe->execute([':prop_id' => $prop_id]);

    $sql_pmc = "DELETE FROM propostas_material_consumo WHERE pmc_proposta_id = :prop_id";
    $stmt_pmc = $conn->prepare($sql_pmc);
    $stmt_pmc->execute([':prop_id' => $prop_id]);

    $sql_ps = "DELETE FROM propostas_servico WHERE ps_proposta_id = :prop_id";
    $stmt_ps = $conn->prepare($sql_ps);
    $stmt_ps->execute([':prop_id' => $prop_id]);

    $sql_pimg = "DELETE FROM propostas_arq WHERE parq_prop_id = :prop_id";
    $stmt_pimg = $conn->prepare($sql_pimg);
    $stmt_pimg->execute([':prop_id' => $prop_id]);

    $sql_prop_cmod = "DELETE FROM propostas_cursos_modulo WHERE prop_cmod_prop_id = :prop_id";
    $stmt_prop_cmod = $conn->prepare($sql_prop_cmod);
    $stmt_prop_cmod->execute([':prop_id' => $prop_id]);

    $sql_prc = "DELETE FROM propostas_extensao_responsavel_contato WHERE prc_proposta_id = :prop_id";
    $stmt_prc = $conn->prepare($sql_prc);
    $stmt_prc->execute([':prop_id' => $prop_id]);

    $sql_insc = "DELETE FROM inscricoes WHERE insc_prop_id = :prop_id";
    $stmt_insc = $conn->prepare($sql_insc);
    $stmt_insc->execute([':prop_id' => $prop_id]);

    $sql_prop_status = "DELETE FROM propostas_status WHERE prop_sta_prop_id = :prop_id";
    $stmt_prop_status = $conn->prepare($sql_prop_status);
    $stmt_prop_status->execute([':prop_id' => $prop_id]);

    $sql_prop_sta_analise = "DELETE FROM propostas_analise_status WHERE sta_an_prop_id = :prop_id";
    $stmt_sta_analise = $conn->prepare($sql_prop_sta_analise);
    $stmt_sta_analise->execute([':prop_id' => $prop_id]);

    $sql_prop_sta_analise_parecer = "DELETE FROM propostas_analise_parecer WHERE prop_parecer_prop_id = :prop_id";
    $stmt_sta_analise = $conn->prepare($sql_prop_sta_analise_parecer);
    $stmt_sta_analise->execute([':prop_id' => $prop_id]);

    $sql_cert = "DELETE FROM certificado WHERE cert_prop_id = :prop_id";
    $stmt_cert = $conn->prepare($sql_cert);
    $stmt_cert->execute([':prop_id' => $prop_id]);

    $sql_cert_arq = "DELETE FROM certificado_arquivo WHERE cert_arq_prop_id = :prop_id";
    $stmt_cert_arq = $conn->prepare($sql_cert_arq);
    $stmt_cert_arq->execute([':prop_id' => $prop_id]);

    // EXCLUIR ARQUIVOS PASTA 1
    $folderPath_1 = '../uploads/propostas/' . $prop_codigo . '/1';
    is_dir($folderPath_1);
    $files = glob($folderPath_1 . '/*');
    if ($files !== false) {
      foreach ($files as $file) {
        if (is_file($file)) {
          unlink($file); // EXCLUI CADA ARQUIVO NA PASTA
        }
      }
    }
    // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA
    rmdir($folderPath_1);
    ///

    // EXCLUIR ARQUIVOS PASTA 2
    $folderPath_2 = '../uploads/propostas/' . $prop_codigo . '/2';
    is_dir($folderPath_2);
    $files = glob($folderPath_2 . '/*');
    if ($files !== false) {
      foreach ($files as $file) {
        if (is_file($file)) {
          unlink($file); // EXCLUI CADA ARQUIVO NA PASTA
        }
      }
    }
    // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA
    rmdir($folderPath_2);
    ///


    // EXCLUIR ARQUIVOS PASTA 3
    $folderPath_3 = '../uploads/propostas/' . $prop_codigo . '/3';
    is_dir($folderPath_3);
    $files = glob($folderPath_3 . '/*');
    if ($files !== false) {
      foreach ($files as $file) {
        if (is_file($file)) {
          unlink($file); // EXCLUI CADA ARQUIVO NA PASTA
        }
      }
    }
    // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA
    rmdir($folderPath_3);
    ///


    // EXCLUIR ARQUIVOS PASTA 4
    $folderPath_4 = '../uploads/propostas/' . $prop_codigo . '/4';
    is_dir($folderPath_4);
    $files = glob($folderPath_4 . '/*');
    if ($files !== false) {
      foreach ($files as $file) {
        if (is_file($file)) {
          unlink($file); // EXCLUI CADA ARQUIVO NA PASTA
        }
      }
    }
    // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA
    rmdir($folderPath_4);
    ///

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

    // EXCLUIR ARQUIVOS
    $folderPath = '../uploads/propostas/' . $prop_codigo;
    is_dir($folderPath);
    $files = glob($folderPath . '/*');
    if ($files !== false) {
      foreach ($files as $file) {
        if (is_file($file)) {
          unlink($file); // EXCLUI CADA ARQUIVO NA PASTA
        }
      }
    }
    // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA
    rmdir($folderPath);
    ///

    // EXCLUIR ARQUIVOS CERTIFICADO
    $folderPathCertArq = '../uploads/certificado/' . $prop_codigo;
    is_dir($folderPathCertArq);
    $files = glob($folderPathCertArq . '/*');
    if ($files !== false) {
      foreach ($files as $file) {
        if (is_file($file)) {
          unlink($file); // EXCLUI CADA ARQUIVO NA PASTA
        }
      }
    }
    // APÓS EXCLUIR ARQUIVOS, EXCLUI A PASTA
    rmdir($folderPathCertArq);
    ///

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_data )
    VALUES (:modulo, :acao, :acao_id, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'    => 'PROPOSTA',
      ':acao'      => 'EXCLUSÃO',
      ':acao_id'   => $prop_id,
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
    return die;
  }
}
