<?php
// Certifique-se de que a sessão é iniciada, se ainda não for
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
ob_start(); // Limpa o buffer de saída, se necessário para redirecionamentos

// Caminho de conexao.php a partir de admin/controller/
// Se conexao.php está em 'reservm/conexao/', e este controlador em 'reservm/admin/controller/',
// o caminho correto é '../../conexao/conexao.php'
include '../conexao/conexao.php';

// Caminho de send_course_email.php a partir de admin/controller/
// Se send_course_email.php está em 'reservm/includes/', e este controlador em 'reservm/admin/controller/',
// o caminho correto é '../../includes/send_course_email.php'
include '../conexao/send_course_email.php';

// NECESSÁRIO PARA ENVIAR O EMAIL (Já está no seu código)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Caminho para o autoload do Composer a partir de admin/controller/
// Se vendor/ está em 'reservm/vendor/', e este controlador em 'reservm/admin/controller/',
// o caminho correto é '../../vendor/autoload.php'
require '../vendor/autoload.php';

// Variável $url_sistema precisa estar definida. Se ela vem de um arquivo de configuração,
// inclua-o aqui se ainda não foi feito em 'conexao.php'.
// Exemplo (se não vier de outro lugar): $url_sistema = 'http://localhost/reservm';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {

  try {

    $conn->beginTransaction();

    $solic_acao = isset($_POST['solic_acao']) ? trim($_POST['solic_acao']) : null;

    // Se o usuário logado e o nome do usuário não estiverem na sessão, você pode precisar buscá-los do banco de dados
    // ou garantir que a sessão esteja populada corretamente antes de chegar aqui.
    $solic_user_id = $_SESSION['reservm_user_id'] ?? null;
    $user_nome = $_SESSION['reservm_user_nome'] ?? 'Usuário Desconhecido'; // Nome do usuário para o e-mail

    // Validação inicial do usuário
    if (empty($solic_user_id)) {
      throw new Exception("ID de usuário não encontrado na sessão. Faça login novamente.");
    }

    // SE INSERIR, ATUALIZAR OU DELETAR
    if ($solic_acao === 'cadastrar_1' || $solic_acao === 'atualizar_1' || $solic_acao === 'atualizar_2' || $solic_acao === 'atualizar_3') {

      $solic_id_post = $_POST['solic_id'] ?? bin2hex(random_bytes(16));
      $solic_id = $solic_id_post;

      // FUNÇÃO PARA VERIFICAR SE O CÓDIGO JÁ EXISTE
      function verificarCodigoNoBanco($solic_codigo, $conn)
      {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM solicitacao WHERE solic_codigo = :solic_codigo");
        $stmt->bindParam(':solic_codigo', $solic_codigo);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
      }

      // Gerar código único com tentativa de inserção (apenas se for cadastrar_1 e o ID for novo)
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
      $solic_curso = trim($_POST['solic_curso']); // ID do curso - essencial para o e-mail

      // Variáveis de IDENTIFICAÇÃO (manter como no seu código)
      if (in_array($solic_curso, [2, 5, 6, 9, 13, 14, 17, 18, 21])) {
        $solic_comp_curric = isset($_POST['solic_comp_curric']) ? trim($_POST['solic_comp_curric']) : null;
      } else {
        $solic_comp_curric = null;
      }
      if (in_array($solic_curso, [7, 10, 19, 28, 31])) {
        $solic_nome_atividade = isset($_POST['solic_nome_atividade']) ? trim($_POST['solic_nome_atividade']) : null;
      } else {
        $solic_nome_atividade = null;
      }
      if (in_array($solic_curso, [8])) {
        $solic_nome_curso = isset($_POST['solic_nome_curso']) ? trim($_POST['solic_nome_curso']) : null;
        $solic_nome_atividade = isset($_POST['solic_nome_atividade']) ? trim($_POST['solic_nome_atividade']) : null;
        $solic_semestre = isset($_POST['solic_semestre']) ? trim($_POST['solic_semestre']) : null;
      } else {
        $solic_nome_curso = null;
        $solic_semestre = null;
      }
      if (in_array($solic_curso, [11, 22])) {
        $solic_nome_curso_text = isset($_POST['solic_nome_curso_text']) ? trim($_POST['solic_nome_curso_text']) : null;
        $solic_nome_comp_ativ = isset($_POST['solic_nome_comp_ativ']) ? trim($_POST['solic_nome_comp_ativ']) : null;
        $solic_semestre = isset($_POST['solic_semestre']) ? trim($_POST['solic_semestre']) : null;
      } else {
        $solic_nome_curso_text = null;
        $solic_nome_comp_ativ = null;
      }

      $solic_nome_prof_resp = isset($_POST['solic_nome_prof_resp']) ? trim($_POST['solic_nome_prof_resp']) : null;
      $solic_contato = isset($_POST['solic_contato']) ? trim($_POST['solic_contato']) : null;

      // Inicializa variáveis para aulas práticas e teóricas
      $solic_ap_aula_pratica = $_POST['solic_ap_aula_pratica'] ?? null;
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
      $solic_at_aula_teorica = $_POST['solic_at_aula_teorica'] ?? null;
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
      $stmt->bindParam(':solic_curso', $solic_curso, PDO::PARAM_INT);
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

      // -------------------------------
      // ATUALIZAÇÃO ETAPA 1
      // -------------------------------
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
      $stmt->bindParam(':solic_curso', $solic_curso, PDO::PARAM_INT);
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

      $sqlVerifica = "SELECT solic_id, solic_etapa FROM solicitacao WHERE solic_id = :solic_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':solic_id' => $solic_id]);
      $result_etapa = $stmtVerifica->fetch(PDO::FETCH_ASSOC);
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
        $num_status = 2; // 2 = CONCLUÍDO
        $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_user_id, GETDATE(), GETDATE())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':sta_an_solic_id' => $solic_id, ':sta_an_status' => $num_status, ':sta_an_user_id' => $solic_user_id]);

        $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE() WHERE solic_sta_solic_id = :solic_sta_solic_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':solic_sta_solic_id' => $solic_id, ':solic_sta_status' => $num_status, ':solic_sta_user_id' => $solic_user_id]);

        // DISPARA E-MAIL PARA OS COORDENADORES DO CURSO
        $course_name = '';
        $stmt_course_name = $conn->prepare("SELECT curs_curso FROM cursos WHERE curs_id = :curs_id");
        $stmt_course_name->execute([':curs_id' => $solic_curso]);
        $course_result = $stmt_course_name->fetch(PDO::FETCH_ASSOC);
        if ($course_result) {
          $course_name = $course_result['curs_curso'];
        }

        if (!empty($solic_curso) && !empty($course_name)) { // Se o curso está selecionado e o nome foi encontrado
          $email_subject = 'Nova solicitação de espaço para o curso: ' . htmlspecialchars($course_name);
          $email_message = "<html><body>" .
            "<p>Prezados Coordenadores do Curso " . htmlspecialchars($course_name) . ",</p>" .
            "<p>Uma nova solicitação de espaço (Código: <strong>" . htmlspecialchars($solic_codigo) . "</strong>) foi cadastrada por <strong>" . htmlspecialchars($user_nome) . "</strong> e está aguardando sua análise.</p>" .
            "<p>Acesse o sistema através do botão abaixo para revisar e deferir/indeferir a solicitação.</p>" .
            "<p><strong>Link da Solicitação:</strong> <a href='" . $url_sistema . "/nova_solicitacao.php?i=" . htmlspecialchars($solic_id) . "&st=3'>Visualizar Solicitação</a></p>" .
            "<p>Atenciosamente,<br>Sua Equipe de Gestão de Atividades de Extensão Bahiana</p>" .
            "</body></html>";

          // Chamada da função de envio de e-mail para os coordenadores do curso
          // Certifique-se que $view_colaboradores está disponível (vem de conexao.php)
          sendCourseNotificationEmail($conn, $solic_curso, $email_subject, $email_message, $view_colaboradores);
        } else {
          error_log("Email para coordenadores não enviado: solic_curso vazio ou nome do curso não encontrado. Solicitação ID: " . $solic_id);
          // Opcional: registrar em $_SESSION['aviso'] para o usuário
        }

        // Se você ainda precisa notificar o ADMIN, você deve adaptar essa lógica
        // para chamar sendCourseNotificationEmail com os dados do admin, ou ter uma função separada para isso.
        // O bloco original do admin foi removido/comentado aqui para evitar duplicação e confusão.
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
