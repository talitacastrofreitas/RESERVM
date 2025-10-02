<?php
// Certifique-se de que a sessão é iniciada, se ainda não for
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
ob_start();

include '../conexao/conexao.php';
include '../conexao/send_course_email.php'; // Verifique o caminho se é '../includes/send_course_email.php'

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Verifique o caminho se é '../../vendor/autoload.php'

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {

  try {
    $conn->beginTransaction();

    $solic_acao = isset($_POST['solic_acao']) ? trim($_POST['solic_acao']) : null;
    $solic_user_id = $_SESSION['reservm_user_id'] ?? null;
    $user_nome = $_SESSION['reservm_user_nome'] ?? 'Usuário Desconhecido';

    if (empty($solic_user_id)) {
      throw new Exception("ID de usuário não encontrado na sessão. Faça login novamente.");
    }

    if ($solic_acao === 'cadastrar_1' || $solic_acao === 'atualizar_1' || $solic_acao === 'atualizar_2' || $solic_acao === 'atualizar_3') {

      $solic_id_post = $_POST['solic_id'] ?? bin2hex(random_bytes(16));
      $solic_id = $solic_id_post;

      function verificarCodigoNoBanco($solic_codigo, $conn)
      {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM solicitacao WHERE solic_codigo = :solic_codigo");
        $stmt->bindParam(':solic_codigo', $solic_codigo);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
      }

      $solic_codigo = '';
      if ($solic_acao === 'cadastrar_1') {
        $tentativas = 0;
        do {
          $tentativas++;
          if ($tentativas > 5) {
            throw new Exception("Não foi possível gerar um código único após várias tentativas.");
          }
          $solic_codigo = 'SO' . random_int(100000, 999999);
          $existe = verificarCodigoNoBanco($solic_codigo, $conn);
        } while ($existe);
      } else {
        $stmt_codigo = $conn->prepare("SELECT solic_codigo FROM solicitacao WHERE solic_id = :solic_id");
        $stmt_codigo->execute([':solic_id' => $solic_id]);
        $result_codigo = $stmt_codigo->fetch(PDO::FETCH_ASSOC);
        if ($result_codigo) {
          $solic_codigo = $result_codigo['solic_codigo'];
        } else {
          throw new Exception("Solicitação não encontrada para atualização.");
        }
      }

      $solic_etapa = isset($_POST['solic_etapa']) ? trim($_POST['solic_etapa']) : null;

      // =========================================================================
      // CÓDIGO ESSENCIAL: GARANTE QUE $solic_curso SEMPRE TENHA O ID DO CURSO
      // =========================================================================
      $solic_curso = null; // Inicializa como nulo

      if ($solic_acao === 'cadastrar_1' || $solic_acao === 'atualizar_1') {
        // Na primeira etapa, ele vem do POST do formulário
        $solic_curso = trim($_POST['solic_curso'] ?? null);
      }
      // Para as etapas 2 e 3 (e também 1, se não veio no POST, ou se a solicitação já existe)
      // Buscamos o solic_curso do banco de dados, que é a fonte definitiva.
      if (!empty($solic_id)) { // Se a solicitação já tem um ID
        $stmt_get_solic_curso = $conn->prepare("SELECT solic_curso FROM solicitacao WHERE solic_id = :solic_id");
        $stmt_get_solic_curso->execute([':solic_id' => $solic_id]);
        $result_solic_curso_db = $stmt_get_solic_curso->fetch(PDO::FETCH_ASSOC);
        if ($result_solic_curso_db && !empty($result_solic_curso_db['solic_curso'])) {
          $solic_curso = $result_solic_curso_db['solic_curso'];
        }
      }

      // =========================================================================


      // Variáveis de IDENTIFICAÇÃO (usar ?? null para evitar Undefined array key)
      $solic_comp_curric = trim($_POST['solic_comp_curric'] ?? null);
      $solic_nome_atividade = trim($_POST['solic_nome_atividade'] ?? null);
      $solic_nome_curso = trim($_POST['solic_nome_curso'] ?? null);
      $solic_nome_curso_text = trim($_POST['solic_nome_curso_text'] ?? null);
      $solic_semestre = trim($_POST['solic_semestre'] ?? null);
      $solic_nome_comp_ativ = trim($_POST['solic_nome_comp_ativ'] ?? null); // Certifique-se de que essa variável está sendo populada pelo post
      $solic_nome_prof_resp = isset($_POST['solic_nome_prof_resp']) ? trim($_POST['solic_nome_prof_resp']) : null;
      $solic_contato = isset($_POST['solic_contato']) ? trim($_POST['solic_contato']) : null;


      // Inicializa variáveis para aulas práticas e teóricas
      $solic_ap_aula_pratica = $_POST['solic_ap_aula_pratica'] ?? null;
      $solic_ap_campus = $_POST['solic_ap_campus'] ?? null;
      $solic_ap_espaco = isset($_POST['solic_ap_espaco_brotas']) ? implode(', ', array_map('htmlspecialchars', $_POST['solic_ap_espaco_brotas'])) : (isset($_POST['solic_ap_espaco_cabula']) ? implode(', ', array_map('htmlspecialchars', $_POST['solic_ap_espaco_cabula'])) : null);
      $solic_ap_quant_turma = $_POST['solic_ap_quant_turma'] ?? null;
      $solic_ap_quant_particip = $_POST['solic_ap_quant_particip'] ?? null;
      $solic_ap_tipo_reserva = $_POST['solic_ap_tipo_reserva'] ?? null;
      $solic_ap_data_reserva = trim($_POST['solic_ap_data_reserva'] ?? '') !== '' ? nl2br(trim($_POST['solic_ap_data_reserva'] ?? '')) : NULL;
      $solic_ap_dia_reserva = isset($_POST['solic_ap_dia_reserva']) && is_array($_POST['solic_ap_dia_reserva']) ? implode(', ', array_map('htmlspecialchars', $_POST['solic_ap_dia_reserva'])) : null;
      $solic_ap_hora_inicio = $_POST['solic_ap_hora_inicio'] ?? null;
      $solic_ap_hora_fim = $_POST['solic_ap_hora_fim'] ?? null;
      $solic_ap_tipo_material = $_POST['solic_ap_tipo_material'] ?? null;
      $solic_ap_tit_aulas = trim($_POST['solic_ap_tit_aulas'] ?? '') !== '' ? nl2br(trim($_POST['solic_ap_tit_aulas'] ?? '')) : NULL;
      $solic_ap_quant_material = trim($_POST['solic_ap_quant_material'] ?? '') !== '' ? nl2br(trim($_POST['solic_ap_quant_material'] ?? '')) : NULL;
      $solic_ap_obs = trim($_POST['solic_ap_obs'] ?? '') !== '' ? nl2br(trim($_POST['solic_ap_obs'] ?? '')) : NULL;
      $solic_at_aula_teorica = $_POST['solic_at_aula_teorica'] ?? null;
      $solic_at_campus = $_POST['solic_at_campus'] ?? null;
      $solic_at_quant_sala = $_POST['solic_at_quant_sala'] ?? null;
      $solic_at_quant_particip = $_POST['solic_at_quant_particip'] ?? null;
      $solic_at_tipo_reserva = $_POST['solic_at_tipo_reserva'] ?? null;
      $solic_at_data_reserva = trim($_POST['solic_at_data_reserva'] ?? '') !== '' ? nl2br(trim($_POST['solic_at_data_reserva'] ?? '')) : NULL;
      $solic_at_dia_reserva = isset($_POST['solic_at_dia_reserva']) && is_array($_POST['solic_at_dia_reserva']) ? implode(', ', array_map('htmlspecialchars', $_POST['solic_at_dia_reserva'])) : null;
      $solic_at_hora_inicio = $_POST['solic_at_hora_inicio'] ?? null;
      $solic_at_hora_fim = $_POST['solic_at_hora_fim'] ?? null;
      $solic_at_recursos = trim($_POST['solic_at_recursos'] ?? '') !== '' ? nl2br(trim($_POST['solic_at_recursos'] ?? '')) : NULL;
      $solic_at_obs = trim($_POST['solic_at_obs'] ?? '') !== '' ? nl2br(trim($_POST['solic_at_obs'] ?? '')) : NULL;
    }

    // -------------------------------
    // CADASTRAR ETAPA 1
    // -------------------------------
    if ($solic_acao === 'cadastrar_1') {
      $num_status = 1; // CADASTRO EM ELABORAÇÃO
      $log_acao = 'Cadastro';

      $sql = "INSERT INTO solicitacao (solic_id, solic_codigo, solic_etapa, solic_curso, solic_comp_curric, solic_nome_curso, solic_nome_curso_text, solic_nome_atividade, solic_nome_comp_ativ, solic_semestre, solic_nome_prof_resp, solic_contato, solic_cad_por, solic_data_cad) VALUES (:solic_id, :solic_codigo, :solic_etapa, :solic_curso, :solic_comp_curric, :solic_nome_curso, upper(:solic_nome_curso_text), upper(:solic_nome_atividade), upper(:solic_nome_comp_ativ), :solic_semestre, upper(:solic_nome_prof_resp), :solic_contato, :solic_cad_por, GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':solic_id', $solic_id, PDO::PARAM_STR);
      $stmt->bindParam(':solic_codigo', $solic_codigo, PDO::PARAM_STR);
      $stmt->bindParam(':solic_etapa', $solic_etapa, PDO::PARAM_INT);
      $stmt->bindParam(':solic_curso', $solic_curso, PDO::PARAM_INT); // $solic_curso já foi tratado acima
      $stmt->bindParam(':solic_comp_curric', $solic_comp_curric, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_curso', $solic_nome_curso, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_curso_text', $solic_nome_curso_text, PDO::PARAM_STR);
      $stmt->bindParam(':solic_nome_atividade', $solic_nome_atividade, PDO::PARAM_STR);
      $stmt->bindParam(':solic_nome_comp_ativ', $solic_nome_comp_ativ, PDO::PARAM_STR);
      $stmt->bindParam(':solic_semestre', $solic_semestre, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_prof_resp', $solic_nome_prof_resp, PDO::PARAM_STR);
      $stmt->bindParam(':solic_contato', $solic_contato, PDO::PARAM_STR);
      $stmt->bindParam(':solic_cad_por', $solic_user_id, PDO::PARAM_STR);
      $stmt->execute();

      $sql = "INSERT INTO solicitacao_status (solic_sta_solic_id,solic_sta_status, solic_sta_user_id, solic_sta_data_cad) VALUES (:solic_sta_solic_id, :solic_sta_status, :solic_sta_user_id, GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':solic_sta_solic_id' => $solic_id, ':solic_sta_status' => $num_status, ':solic_sta_user_id' => $solic_user_id]);

      $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_user_id, GETDATE(), GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':sta_an_solic_id' => $solic_id, ':sta_an_status' => $num_status, ':sta_an_user_id' => $solic_user_id]);
    } elseif ($solic_acao === 'atualizar_1') {
      if (empty($_POST['solic_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $solic_id = $_POST['solic_id'];
      $log_acao = 'Atualização';

      $sqlVerifica = "SELECT solic_id, solic_etapa FROM solicitacao WHERE solic_id = :solic_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':solic_id' => $solic_id]);
      $result = $stmtVerifica->fetch(PDO::FETCH_ASSOC);
      if ($result) {
        if ($result['solic_etapa'] == 3) {
          $solic_etapa = 3;
        } elseif ($result['solic_etapa'] == 2) {
          $solic_etapa = 2;
        } else {
          $solic_etapa = 1;
        }
      }

      $sql = "UPDATE solicitacao SET solic_etapa = :solic_etapa, solic_curso = :solic_curso, solic_comp_curric = :solic_comp_curric, solic_nome_curso = :solic_nome_curso, solic_nome_curso_text = upper(:solic_nome_curso_text), solic_nome_atividade = upper(:solic_nome_atividade), solic_nome_comp_ativ = upper(:solic_nome_comp_ativ), solic_semestre = :solic_semestre, solic_nome_prof_resp = upper(:solic_nome_prof_resp), solic_contato = :solic_contato, solic_upd_por = :solic_upd_por, solic_data_upd = GETDATE() WHERE solic_id = :solic_id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':solic_id', $solic_id, PDO::PARAM_STR);
      $stmt->bindParam(':solic_etapa', $solic_etapa, PDO::PARAM_INT);
      $stmt->bindParam(':solic_curso', $solic_curso, PDO::PARAM_INT); // $solic_curso já foi tratado acima
      $stmt->bindParam(':solic_comp_curric', $solic_comp_curric, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_curso', $solic_nome_curso, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_curso_text', $solic_nome_curso_text, PDO::PARAM_STR);
      $stmt->bindParam(':solic_nome_atividade', $solic_nome_atividade, PDO::PARAM_STR);
      $stmt->bindParam(':solic_nome_comp_ativ', $solic_nome_comp_ativ, PDO::PARAM_STR);
      $stmt->bindParam(':solic_semestre', $solic_semestre, PDO::PARAM_INT);
      $stmt->bindParam(':solic_nome_prof_resp', $solic_nome_prof_resp, PDO::PARAM_STR);
      $stmt->bindParam(':solic_contato', $solic_contato, PDO::PARAM_STR);
      $stmt->bindParam(':solic_upd_por', $solic_user_id, PDO::PARAM_STR);
      $stmt->execute();
    } elseif ($solic_acao === 'atualizar_2') {
      if (empty($_POST['solic_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $solic_id = $_POST['solic_id'];
      $log_acao = 'Atualização';

      $sqlVerifica = "SELECT solic_id, solic_etapa FROM solicitacao WHERE solic_id = :solic_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':solic_id' => $solic_id]);
      $result = $stmtVerifica->fetch(PDO::FETCH_ASSOC);
      if ($result) {
        if ($result['solic_etapa'] == 3) {
          $solic_etapa = 3;
        } elseif ($result['solic_etapa'] == 1) {
          $solic_etapa = 2;
        }
      }

      $solic_ap_aula_pratica = trim($_POST['solic_ap_aula_pratica']);
      $_SESSION['solic_ap_aula_pratica_choice'] = $solic_ap_aula_pratica;

      if ($solic_ap_aula_pratica == 0) {
        $solic_ap_campus = null;
        $solic_ap_espaco = null;
        $solic_ap_quant_turma = null;
        $solic_ap_quant_particip = null;
        $solic_ap_tipo_reserva = null;
        $solic_ap_data_reserva = null;
        $solic_ap_dia_reserva = null;
        $solic_ap_hora_inicio = null;
        $solic_ap_hora_fim = null;
        $solic_ap_tipo_material = null;
        $solic_ap_tit_aulas = null;
        $solic_ap_quant_material = null;
        $solic_ap_obs = null;
      } else {
        $solic_ap_campus = isset($_POST['solic_ap_campus']) ? trim($_POST['solic_ap_campus']) : null;
        if ($solic_ap_campus == 2) {
          $solic_ap_espaco = isset($_POST['solic_ap_espaco_brotas']) && is_array($_POST['solic_ap_espaco_brotas'])
            ? implode(', ', array_map('htmlspecialchars', $_POST['solic_ap_espaco_brotas'])) : null;
        } else {
          $solic_ap_espaco = isset($_POST['solic_ap_espaco_cabula']) && is_array($_POST['solic_ap_espaco_cabula'])
            ? implode(', ', array_map('htmlspecialchars', $_POST['solic_ap_espaco_cabula'])) : null;
        }
        $solic_ap_quant_turma = isset($_POST['solic_ap_quant_turma']) ? trim($_POST['solic_ap_quant_turma']) : null;
        $solic_ap_quant_particip = isset($_POST['solic_ap_quant_particip']) ? trim($_POST['solic_ap_quant_particip']) : null;
        $solic_ap_tipo_reserva = isset($_POST['solic_ap_tipo_reserva']) ? trim($_POST['solic_ap_tipo_reserva']) : null;
        if ($solic_ap_tipo_reserva == 1) {
          $solic_ap_data_reserva = trim($_POST['solic_ap_data_reserva']) !== '' ? nl2br(trim($_POST['solic_ap_data_reserva'])) : NULL;
        } else {
          if (isset($_POST['solic_ap_dia_reserva'])) {
            $solic_ap_dia_reserva = isset($_POST['solic_ap_dia_reserva']) && is_array($_POST['solic_ap_dia_reserva'])
              ? implode(', ', array_map('htmlspecialchars', $_POST['solic_ap_dia_reserva'])) : null;
          }
        }
        $solic_ap_hora_inicio = isset($_POST['solic_ap_hora_inicio']) ? trim($_POST['solic_ap_hora_inicio']) : null;
        $solic_ap_hora_fim = isset($_POST['solic_ap_hora_fim']) ? trim($_POST['solic_ap_hora_fim']) : null;
        $solic_ap_tipo_material = isset($_POST['solic_ap_tipo_material']) ? trim($_POST['solic_ap_tipo_material']) : null;
        if ($solic_ap_tipo_material == 1) {
        } elseif ($solic_ap_tipo_material == 2) {
          $solic_ap_tit_aulas = trim($_POST['solic_ap_tit_aulas']) !== '' ? nl2br(trim($_POST['solic_ap_tit_aulas'])) : NULL;
        } elseif ($solic_ap_tipo_material == 3) {
          $solic_ap_quant_material = trim($_POST['solic_ap_quant_material']) !== '' ? nl2br(trim($_POST['solic_ap_quant_material'])) : NULL;
        }
        $solic_ap_obs = trim($_POST['solic_ap_obs']) !== '' ? nl2br(trim($_POST['solic_ap_obs'])) : NULL;
      }

      $sql = "UPDATE solicitacao SET solic_etapa = :solic_etapa, solic_ap_aula_pratica = :solic_ap_aula_pratica, solic_ap_campus = :solic_ap_campus, solic_ap_espaco = :solic_ap_espaco, solic_ap_quant_turma = :solic_ap_quant_turma, solic_ap_quant_particip = :solic_ap_quant_particip, solic_ap_tipo_reserva = :solic_ap_tipo_reserva, solic_ap_data_reserva = :solic_ap_data_reserva, solic_ap_dia_reserva = :solic_ap_dia_reserva, solic_ap_hora_inicio = :solic_ap_hora_inicio, solic_ap_hora_fim = :solic_ap_hora_fim, solic_ap_tipo_material = :solic_ap_tipo_material, solic_ap_tit_aulas = :solic_ap_tit_aulas, solic_ap_quant_material = :solic_ap_quant_material, solic_ap_obs = :solic_ap_obs, solic_upd_por = :solic_upd_por, solic_data_upd = GETDATE() WHERE solic_id = :solic_id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':solic_id', $solic_id, PDO::PARAM_STR);
      $stmt->bindParam(':solic_etapa', $solic_etapa, PDO::PARAM_INT);
      $stmt->bindParam(':solic_ap_aula_pratica', $solic_ap_aula_pratica, PDO::PARAM_INT);
      $stmt->bindParam(':solic_ap_campus', $solic_ap_campus, PDO::PARAM_INT);
      $stmt->bindParam(':solic_ap_espaco', $solic_ap_espaco, PDO::PARAM_STR);
      $stmt->bindParam(':solic_ap_quant_turma', $solic_ap_quant_turma, PDO::PARAM_INT);
      $stmt->bindParam(':solic_ap_quant_particip', $solic_ap_quant_particip, PDO::PARAM_STR);
      $stmt->bindParam(':solic_ap_tipo_reserva', $solic_ap_tipo_reserva, PDO::PARAM_STR);
      $stmt->bindParam(':solic_ap_data_reserva', $solic_ap_data_reserva, PDO::PARAM_STR);
      $stmt->bindParam(':solic_ap_dia_reserva', $solic_ap_dia_reserva, PDO::PARAM_STR);
      $stmt->bindParam(':solic_ap_hora_inicio', $solic_ap_hora_inicio, PDO::PARAM_STR);
      $stmt->bindParam(':solic_ap_hora_fim', $solic_ap_hora_fim, PDO::PARAM_STR);
      $stmt->bindParam(':solic_ap_tipo_material', $solic_ap_tipo_material, PDO::PARAM_INT);
      $stmt->bindParam(':solic_ap_tit_aulas', $solic_ap_tit_aulas, PDO::PARAM_STR);
      $stmt->bindParam(':solic_ap_quant_material', $solic_ap_quant_material, PDO::PARAM_STR);
      $stmt->bindParam(':solic_ap_obs', $solic_ap_obs, PDO::PARAM_STR);
      $stmt->bindParam(':solic_upd_por', $solic_user_id, PDO::PARAM_STR);
      $stmt->execute();

      if ($solic_ap_aula_pratica == 0) {
        $sql = "DELETE FROM solicitacao_arq WHERE sarq_solic_id = :sarq_solic_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':sarq_solic_id' => $solic_id]);

        function excluirArquivos($caminho)
        {
          if (!is_dir($caminho)) {
            return;
          }
          $files = glob("$caminho/*");
          if ($files) {
            foreach ($files as $file) {
              if (is_file($file)) {
                unlink($file);
              }
            }
          }
          rmdir($caminho);
        }
        $pastas = ["../uploads/solicitacoes/$solic_codigo"];
        foreach ($pastas as $pasta) {
          excluirArquivos($pasta);
        }
      }
      if (!empty($_FILES["arquivos"]["name"][0])) {
        $arquivos = $_FILES["arquivos"];
        $quantidadeNovosArquivos = count(array_filter($arquivos['name']));
        $sql = "SELECT COUNT(*) FROM solicitacao_arq WHERE sarq_solic_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$solic_id]);
        $quantidadeExistente = (int) $stmt->fetchColumn();
        $total = $quantidadeExistente + $quantidadeNovosArquivos;
        if ($total > 10) {
          $_SESSION["erro"] = "O limite é de 10 arquivos por solicitacao. Atualmente há $quantidadeExistente arquivos e você tentou enviar $quantidadeNovosArquivos!";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'nova_solicitacao.php';
          header("Location: $referer#file_ancora");
          exit;
        }
        $maxFileSize = 1 * 1024 * 1024 * 1024;
        $maxFiles = 10;
        $allowedTypes = [
          "application/msword",
          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          "application/vnd.ms-excel",
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          "application/vnd.ms-powerpoint",
          "application/vnd.openxmlformats-officedocument.presentationml.presentation",
          "application/pdf",
          "image/jpeg",
          "image/png",
          "image/gif",
          "image/bmp",
          "video/mp4",
          "video/x-msvideo",
          "video/quicktime",
          "video/x-matroska",
          "audio/mpeg",
          "audio/wav",
          "audio/ogg"
        ];
        if (count($_FILES['arquivos']['name']) > $maxFiles) {
          $_SESSION["erro"] = "Você só pode enviar até 10 arquivos!";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'nova_solicitacao.php';
          header("Location: $referer#file_ancora");
          exit;
        }
        foreach ($_FILES['arquivos']['name'] as $key => $fileName) {
          $fileSize = $_FILES['arquivos']['size'][$key];
          $fileType = $_FILES['arquivos']['type'][$key];
          if ($fileSize > $maxFileSize) {
            $_SESSION["erro"] = "O arquivo $fileName excede 1GB!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'nova_solicitacao.php';
            header("Location: $referer#file_ancora");
            exit;
          }
          if (!in_array($fileType, $allowedTypes)) {
            $_SESSION["erro"] = "O tipo do arquivo $fileName não é permitido!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'nova_solicitacao.php';
            header("Location: $referer#file_ancora");
            exit;
          }
        }
        $pastaPrincipal = "../uploads/solicitacoes/$solic_codigo";
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        $nomes = $arquivos['name'];
        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];
          $sql = "INSERT INTO solicitacao_arq (sarq_solic_id, sarq_codigo, sarq_arquivo, sarq_user_id, sarq_data_cad) VALUES (:sarq_solic_id, :sarq_codigo, :sarq_arquivo, :sarq_user_id, GETDATE())";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
            ':sarq_solic_id' => $solic_id,
            ':sarq_codigo' => $solic_codigo,
            ':sarq_arquivo' => $nomes[$i],
            ':sarq_user_id' => $solic_user_id
          ]);
          $total_rowns = $stmt->rowCount();
          if ($total_rowns > 0) {
            $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/solicitacoes/' . $solic_codigo . '/' . $nomes[$i]);
          }
        }
      }
    } elseif ($solic_acao === 'atualizar_3') {
      if (empty($_POST['solic_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $solic_id = $_POST['solic_id'];
      $log_acao = 'Atualização';

      $sqlVerifica = "SELECT solic_id, solic_etapa, solic_curso FROM solicitacao WHERE solic_id = :solic_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':solic_id' => $solic_id]);
      $result_etapa = $stmtVerifica->fetch(PDO::FETCH_ASSOC);

      // Garante que $solic_curso é a FK do curso (essencial para a busca do coordenador)
      $solic_curso = $result_etapa['solic_curso'] ?? $solic_curso;

      if ($result_etapa) {
        if ($result_etapa['solic_etapa'] == 3) {
          $solic_etapa = 3;
        } elseif ($result_etapa['solic_etapa'] == 2) {
          $solic_etapa = 3;
        }
      }

      $solic_at_aula_teorica = trim($_POST['solic_at_aula_teorica']);
      $solic_ap_aula_pratica_choice_prev = $_SESSION['solic_ap_aula_pratica_choice'] ?? null;

      if ($solic_ap_aula_pratica_choice_prev === '0' && $solic_at_aula_teorica === '0') {
        $_SESSION['error_message'] = 'É obrigatório selecionar "Sim" para Aulas Práticas ou "Sim" para Aulas Teóricas. Por favor, corrija sua seleção.';
        header("Location: ../nova_solicitacao.php?st=3&i=" . $solic_id);
        exit();
      }
      unset($_SESSION['solic_ap_aula_pratica_choice']);

      if ($solic_at_aula_teorica == 0) {
        $solic_at_campus = null;
        $solic_at_quant_sala = null;
        $solic_at_quant_particip = null;
        $solic_at_tipo_reserva = null;
        $solic_at_data_reserva = null;
        $solic_at_dia_reserva = null;
        $solic_at_hora_inicio = null;
        $solic_at_hora_fim = null;
        $solic_at_recursos = null;
        $solic_at_obs = null;
      } else {
        $solic_at_campus = isset($_POST['solic_at_campus']) ? trim($_POST['solic_at_campus']) : null;
        $solic_at_quant_sala = isset($_POST['solic_at_quant_sala']) ? trim($_POST['solic_at_quant_sala']) : null;
        $solic_at_quant_particip = isset($_POST['solic_at_quant_particip']) ? trim($_POST['solic_at_quant_particip']) : null;
        $solic_at_tipo_reserva = isset($_POST['solic_at_tipo_reserva']) ? trim($_POST['solic_at_tipo_reserva']) : null;
        if ($solic_at_tipo_reserva == 1) {
          $solic_at_data_reserva = trim($_POST['solic_at_data_reserva']) !== '' ? nl2br(trim($_POST['solic_at_data_reserva'])) : NULL;
        } else {
          if (isset($_POST['solic_at_dia_reserva'])) {
            $solic_at_dia_reserva = isset($_POST['solic_at_dia_reserva']) && is_array($_POST['solic_at_dia_reserva'])
              ? implode(', ', array_map('htmlspecialchars', $_POST['solic_at_dia_reserva'])) : null;
          }
        }
        $solic_at_hora_inicio = isset($_POST['solic_at_hora_inicio']) ? trim($_POST['solic_at_hora_inicio']) : null;
        $solic_at_hora_fim = isset($_POST['solic_at_hora_fim']) ? trim($_POST['solic_at_hora_fim']) : null;
        $solic_at_recursos = trim($_POST['solic_at_recursos']) !== '' ? nl2br(trim($_POST['solic_at_recursos'])) : NULL;
        $solic_at_obs = trim($_POST['solic_at_obs']) !== '' ? nl2br(trim($_POST['solic_at_obs'])) : NULL;
      }

      $sql = "UPDATE solicitacao SET solic_etapa = :solic_etapa, solic_at_aula_teorica = :solic_at_aula_teorica, solic_at_campus = :solic_at_campus, solic_at_quant_sala = :solic_at_quant_sala, solic_at_quant_particip = :solic_at_quant_particip, solic_at_tipo_reserva = :solic_at_tipo_reserva, solic_at_data_reserva = :solic_at_data_reserva, solic_at_dia_reserva = :solic_at_dia_reserva, solic_at_hora_inicio = :solic_at_hora_inicio, solic_at_hora_fim = :solic_at_hora_fim, solic_at_recursos = :solic_at_recursos, solic_at_obs = :solic_at_obs, solic_upd_por = :solic_upd_por, solic_data_upd = GETDATE() WHERE solic_id = :solic_id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':solic_id', $solic_id, PDO::PARAM_STR);
      $stmt->bindParam(':solic_etapa', $solic_etapa, PDO::PARAM_INT);
      $stmt->bindParam(':solic_at_aula_teorica', $solic_at_aula_teorica, PDO::PARAM_INT);
      $stmt->bindParam(':solic_at_campus', $solic_at_campus, PDO::PARAM_INT);
      $stmt->bindParam(':solic_at_quant_sala', $solic_at_quant_sala, PDO::PARAM_INT);
      $stmt->bindParam(':solic_at_quant_particip', $solic_at_quant_particip, PDO::PARAM_STR);
      $stmt->bindParam(':solic_at_tipo_reserva', $solic_at_tipo_reserva, PDO::PARAM_INT);
      $stmt->bindParam(':solic_at_data_reserva', $solic_at_data_reserva, PDO::PARAM_STR);
      $stmt->bindParam(':solic_at_dia_reserva', $solic_at_dia_reserva, PDO::PARAM_STR);
      $stmt->bindParam(':solic_at_hora_inicio', $solic_at_hora_inicio, PDO::PARAM_STR);
      $stmt->bindParam(':solic_at_hora_fim', $solic_at_hora_fim, PDO::PARAM_STR);
      $stmt->bindParam(':solic_at_recursos', $solic_at_recursos, PDO::PARAM_STR);
      $stmt->bindParam(':solic_at_obs', $solic_at_obs, PDO::PARAM_STR);
      $stmt->bindParam(':solic_upd_por', $solic_user_id, PDO::PARAM_STR);
      $stmt->execute();

      if ($result_etapa['solic_etapa'] != 3) {
        $num_status = 2; // 2 = SOLICITADO (Status de entrada)

        // 1. Busca a matrícula do coordenador para o curso desta solicitação
        $sql_coord = "SELECT c.curs_matricula_prof, c.curs_curso
                              FROM solicitacao s
                              JOIN cursos c ON c.curs_id = s.solic_curso
                              WHERE s.solic_id = :solic_id";
        $stmt_coord = $conn->prepare($sql_coord);
        $stmt_coord->execute([':solic_id' => $solic_id]);
        $course_info = $stmt_coord->fetch(PDO::FETCH_ASSOC);

        $matricula_coordenador = $course_info['curs_matricula_prof'] ?? null;
        $course_name = $course_info['curs_curso'] ?? 'Curso Desconhecido';

        // 2. Define o destinatario do email de notificação 
        if (!empty($matricula_coordenador)) {
          $email_subject = 'PENDÊNCIA: Análise de Solicitação - ' . htmlspecialchars($course_name);
          $is_pendente_coordenador = true;
          $pendencia_para = 'Coordenador do Curso de <strong>' . htmlspecialchars($course_name) . '</strong>';
          $link_destino = $url_sistema . "/admin/solicitacoes_emAnalise.php"; // Link para a fila do coordenador
        } else {
          $email_subject = 'PENDÊNCIA: Nova Solicitação para Análise (SAAP)';
          $is_pendente_coordenador = false;
          $pendencia_para = 'a Central de Análise (SAAP)';
          $link_destino = $url_sistema . "/admin/solicitacoes_submetidas.php"; // Link para a fila do SAAP
        }

        // 3. Atualiza o Status para 2 (SOLICITADO)
        $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_user_id, GETDATE(), GETDATE())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':sta_an_solic_id' => $solic_id, ':sta_an_status' => $num_status, ':sta_an_user_id' => $solic_user_id]);

        $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE() WHERE solic_sta_solic_id = :solic_sta_solic_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':solic_sta_solic_id' => $solic_id, ':solic_sta_status' => $num_status, ':solic_sta_user_id' => $solic_user_id]);

        // 4. Dispara E-mail com a notificação

        $email_conteudo = '';
        include '../includes/email/email_header.php';
        $email_conteudo .= "
                <tr style='background-color: #ffffff; text-align: center; color: #515050; display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
                    <td align='center' width='800' style='padding: 2em 2rem; display: inline-block;'>
                        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
                        Uma nova solicitação de espaço (Código: <strong>" . htmlspecialchars($solic_codigo) . "</strong>) foi cadastrada e está aguardando análise do " . $pendencia_para . ".
                        </p>
                        <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 20px 0px;'>
                        Acesse o sistema através do botão abaixo para revisar e deferir/indeferir a solicitação.
                        </p>
                        <a style='cursor: pointer;' href='" . $link_destino . "'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema.</button></a>
                    </td>
                </tr>";
        include '../includes/email/email_footer.php';

        // A função sendCourseNotificationEmail DEVE receber a flag de roteamento
        sendCourseNotificationEmail($conn, $solic_curso, $email_subject, $email_conteudo, $is_pendente_coordenador);
      }
    } elseif ($_GET['acao'] === 'deletar_arq') {
      if (empty($_GET['sarq_id']) || empty($_GET['sarq_codigo']) || empty($_GET['sarq_arquivo'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $sarq_id = $_GET['sarq_id'];
      $sarq_codigo = $_GET['sarq_codigo'];
      $sarq_arquivo = $_GET['sarq_arquivo'];
      $log_acao = 'Exclusão Arquivo';
      $solic_id = $sarq_id;

      $sql = "DELETE FROM solicitacao_arq WHERE sarq_id = :sarq_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':sarq_id' => $sarq_id]);

      $apaga_img = unlink("../uploads/solicitacoes/$sarq_codigo/$sarq_arquivo");
    } elseif ($_GET['acao'] === 'deletar') {
      if (empty($_GET['solic_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $solic_id = $_GET['solic_id'];
      $solic_codigo = $_GET['solic_codigo'];
      $log_acao = 'Exclusão';

      $tabelas = [
        'reservas' => 'res_solic_id',
        'solicitacao_analise_status' => 'sta_an_solic_id',
        'solicitacao_arq' => 'sarq_solic_id',
        'solicitacao_status' => 'solic_sta_solic_id',
        'ocorrencias' => 'oco_solic_id'
      ];

      foreach ($tabelas as $tabela => $coluna) {
        $sql = "DELETE FROM $tabela WHERE $coluna = :solic_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':solic_id' => $solic_id]);
      }

      $sql = "DELETE FROM solicitacao WHERE solic_id = :solic_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':solic_id' => $solic_id]);

      function excluirArquivos($caminho)
      {
        if (!is_dir($caminho)) {
          return;
        }
        $files = glob("$caminho/*");
        if ($files) {
          foreach ($files as $file) {
            if (is_file($file)) {
              unlink($file);
            }
          }
        }
        rmdir($caminho);
      }
      $pastas = ["../uploads/solicitacoes/$solic_codigo"];
      foreach ($pastas as $pasta) {
        excluirArquivos($pasta);
      }
    } else {
      throw new Exception("Ação inválida!");
    }

    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data ) VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute(array(
      ':modulo' => 'SOLICITAÇÃO',
      ':acao' => $log_acao,
      ':acao_id' => $solic_id,
      ':dados' => json_encode($_POST),
      ':user_id' => $solic_user_id
    ));

    $conn->commit();

    if ($solic_acao === 'cadastrar_1' || $solic_acao === 'atualizar_1') {
      header(sprintf("location: ../nova_solicitacao.php?st=2&i={$solic_id}"));
    } elseif ($solic_acao === 'atualizar_2') {
      header(sprintf("location: ../nova_solicitacao.php?st=3&i={$solic_id}"));
    } elseif ($solic_acao === 'atualizar_3') {
      $_SESSION["msg"] = "Solicitação realizada com sucesso!";
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
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit();
  } catch (Exception $e) { // Capturar outras exceções (como as de email)
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