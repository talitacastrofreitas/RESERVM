<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// session_start();
include '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    try {

        $conn->beginTransaction();
        $acao = $_POST['acao'] ?? $_GET['acao'];
        $oco_id_redirecionamento = '';
        // Prioriza POST, depois GET
        $oco_id_original = $_POST['oco_id_original'] ?? $_POST['oco_id'] ?? $_GET['oco_id'] ?? NULL;
        $rvm_admin_id = $_SESSION['reservm_admin_id'];

        // =========================================================================
        // 1. BUSCA CENTRALIZADA DO ID DA SOLICITAÇÃO (RESGATA O ID CORRETO)
        // =========================================================================
        $oco_solic_id = $_POST['solic_id'] ?? $_GET['solic_id'] ?? NULL; // 1. ID da solicitação do formulário
        $oco_res_id = $_POST['oco_res_id'] ?? $_GET['oco_res_id'] ?? NULL; // 2. ID da reserva do formulário/GET

        // Se o ID da Solicitação não veio, mas temos o ID da Reserva, buscamos no DB.
        if (!empty($oco_res_id) && empty($oco_solic_id)) {
            $stmt_solic = $conn->prepare("SELECT res_solic_id FROM reservas WHERE res_id = :oco_res_id");
            $stmt_solic->execute([':oco_res_id' => $oco_res_id]);
            $solic_id_db = $stmt_solic->fetchColumn();

            if ($solic_id_db) {
                $oco_solic_id = $solic_id_db; // Define o ID correto
            }
        }

        // Se ainda não temos o ID da Solicitação, tentamos pegá-lo da Ocorrência Original (para atualizar/validar)
        if (empty($oco_solic_id) && !empty($oco_id_original) && $acao !== 'deletar') {
             $stmt_solic = $conn->prepare("SELECT oco_solic_id FROM ocorrencias WHERE oco_id = :oco_id");
             $stmt_solic->execute([':oco_id' => $oco_id_original]);
             $solic_id_db = $stmt_solic->fetchColumn();
             if ($solic_id_db) {
                 $oco_solic_id = $solic_id_db;
             }
        }

        // Validação de segurança para ações que exigem o ID da Solicitação
        if (in_array($acao, ['cadastrar', 'atualizar', 'validar', 'atualizar_admin']) && empty($oco_solic_id)) {
            throw new Exception("ID da Solicitação principal não pôde ser determinado para esta ação.");
        }
        // =========================================================================
        
        if ($acao === 'cadastrar' || $acao === 'atualizar' || $acao === 'atualizar_admin' || $acao === 'validar') {
            
            // Re-define $oco_solic_id e $oco_res_id para ter certeza, embora a busca acima seja preferível
            $oco_solic_id = $oco_solic_id ?? $_POST['solic_id'] ?? NULL;
            $oco_res_id = $_POST['oco_res_id'] ?? NULL;

            if ($acao !== 'validar' && $acao !== 'atualizar_admin') {
                // Definições específicas de cadastrar/atualizar (evita Warnings)
                if (isset($_POST['oco_tipo_ocorrencia']) && is_array($_POST['oco_tipo_ocorrencia'])) {
                    $oco_tipo_ocorrencia = implode(', ', array_map('htmlspecialchars', $_POST['oco_tipo_ocorrencia']));
                } else {
                    $oco_tipo_ocorrencia_raw = trim($_POST['oco_tipo_ocorrencia'] ?? '');
                    $oco_tipo_ocorrencia = ($oco_tipo_ocorrencia_raw !== '') ? $oco_tipo_ocorrencia_raw : NULL;
                }
                $oco_hora_inicio_realizado = !empty($_POST['oco_hora_inicio_realizado']) ? $_POST['oco_hora_inicio_realizado'] : NULL;
                $oco_hora_fim_realizado = !empty($_POST['oco_hora_fim_realizado']) ? $_POST['oco_hora_fim_realizado'] : NULL;
                $oco_obs = trim($_POST['oco_obs']) !== '' ? nl2br(trim($_POST['oco_obs'])) : NULL;
                $acao_tipo_edicao = $_POST['acao_tipo_edicao'] ?? 'update_direto';
            }
            
            if ($acao === 'cadastrar' || $acao === 'atualizar') {
                if (empty($oco_res_id) || empty($oco_tipo_ocorrencia) || is_null($oco_hora_inicio_realizado) || is_null($oco_hora_fim_realizado)) {
                    throw new Exception("Preencha os campos obrigatórios!");
                }
            }
        }


        if ($acao === 'cadastrar') {
            $stmt_check = $conn->prepare("SELECT COUNT(oco_id) FROM ocorrencias WHERE oco_res_id = :res_id");
            $stmt_check->execute([':res_id' => $oco_res_id]);
            if ($stmt_check->fetchColumn() > 0) {
                throw new Exception("Já existe uma ocorrência registrada para esta reserva. Não é possível criar uma nova.");
            }
            $oco_id = bin2hex(random_bytes(16));
            $oco_codigo = 'OC' . random_int(100000, 999999);
            $log_acao = 'Cadastro';
            // Redireciona para o ID da SOLICITAÇÃO
            $oco_id_redirecionamento = $oco_solic_id;
            $sql = "INSERT INTO ocorrencias (oco_id, oco_codigo, oco_solic_id, oco_res_id, oco_tipo_ocorrencia, oco_hora_inicio_realizado, oco_hora_fim_realizado, oco_obs, oco_user_id, oco_status, oco_data_cad, oco_data_upd) VALUES (:oco_id, :oco_codigo, :oco_solic_id, :oco_res_id, :oco_tipo_ocorrencia, :oco_hora_inicio_realizado, :oco_hora_fim_realizado, :oco_obs, :oco_user_id, 1, GETDATE(), GETDATE())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':oco_id' => $oco_id, ':oco_codigo' => $oco_codigo, ':oco_solic_id' => $oco_solic_id, ':oco_res_id' => $oco_res_id, ':oco_tipo_ocorrencia' => $oco_tipo_ocorrencia, ':oco_hora_inicio_realizado' => $oco_hora_inicio_realizado, ':oco_hora_fim_realizado' => $oco_hora_fim_realizado, ':oco_obs' => $oco_obs, ':oco_user_id' => $rvm_admin_id]);

        } elseif ($acao === 'atualizar') {
            if (empty($oco_id_original)) {
                throw new Exception("Erro ao obter o ID do registro.");
            }
            $log_acao = 'Atualização Operador';
            // Redireciona para o ID da SOLICITAÇÃO
            $oco_id_redirecionamento = $oco_solic_id;
            $sql = "UPDATE ocorrencias SET oco_res_id = :oco_res_id, oco_tipo_ocorrencia = :oco_tipo_ocorrencia, oco_hora_inicio_realizado = :oco_hora_inicio_realizado, oco_hora_fim_realizado = :oco_hora_fim_realizado, oco_obs = :oco_obs, oco_autor_edicao = :oco_autor_edicao, oco_data_edicao = GETDATE() WHERE oco_id = :oco_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':oco_id' => $oco_id_original, ':oco_res_id' => $oco_res_id, ':oco_tipo_ocorrencia' => $oco_tipo_ocorrencia, ':oco_hora_inicio_realizado' => $oco_hora_inicio_realizado, ':oco_hora_fim_realizado' => $oco_hora_fim_realizado, ':oco_obs' => $oco_obs, ':oco_autor_edicao' => $rvm_admin_id]);
            $oco_id = $oco_id_original;

        // } elseif ($acao === 'atualizar_admin') {
        //     if (empty($oco_id_original))
        //         throw new Exception("ID da ocorrência original não fornecido.");
        //     if (empty($_POST['oco_status']))
        //         throw new Exception("O Status é obrigatório.");
        //     if (empty($_POST['oco_parecer_tecnico']))
        //         throw new Exception("O Parecer Técnico é obrigatório.");
        //     $log_acao = 'Revisão (Admin)';
            
        //     // REDIRECIONAMENTO DE OCORRÊNCIA: Usa ID da OCORRÊNCIA (para voltar à tela de análise de Ocorrência)
        //     $oco_id_redirecionamento = $oco_id_original; 

        //     // ... (resto da lógica de histórico e atualização de admin) ...

        //     $stmt_leitura = $conn->prepare("SELECT * FROM ocorrencias WHERE oco_id = :id_original");
        //     $stmt_leitura->execute([':id_original' => $oco_id_original]);
        //     $dados_atuais = $stmt_leitura->fetch(PDO::FETCH_ASSOC);
        //     if (!$dados_atuais) {
        //         throw new Exception("Ocorrência a ser editada não encontrada.");
        //     }
        //     // ... (lógica completa de histórico e update) ...
        //     $oco_status = $_POST['oco_status'];
        //     $oco_parecer_tecnico = nl2br(trim($_POST['oco_parecer_tecnico']));
        //     $oco_tipo_ocorrencia = implode(',', array_map('htmlspecialchars', $_POST['oco_tipo_ocorrencia']));
        //     $oco_hora_inicio_realizado = !empty($_POST['oco_hora_inicio_realizado']) ? $_POST['oco_hora_inicio_realizado'] : NULL;
        //     $oco_hora_fim_realizado = !empty($_POST['oco_hora_fim_realizado']) ? $_POST['oco_hora_fim_realizado'] : NULL;
        //     $carga_horaria_post = $_POST['oco_carga_horaria_calculada'] ?? NULL;
        //     $oco_carga_horaria = ($oco_status == 2 && !empty($carga_horaria_post)) ? $carga_horaria_post : NULL;
        //     $sql_update = "UPDATE ocorrencias SET oco_status = :status, oco_tipo_ocorrencia = :tipo_ocorrencia, oco_hora_inicio_realizado = :hora_inicio, oco_hora_fim_realizado = :hora_fim, oco_parecer_tecnico = :parecer, oco_carga_horaria_calculada = :carga_horaria, oco_autor_edicao = :autor_edicao, oco_data_edicao = GETDATE() WHERE oco_id = :id_original";
        //     $stmt_update = $conn->prepare($sql_update);
        //     $stmt_update->execute([':status' => $oco_status, ':tipo_ocorrencia' => $oco_tipo_ocorrencia, ':hora_inicio' => $oco_hora_inicio_realizado, ':hora_fim' => $oco_hora_fim_realizado, ':parecer' => $oco_parecer_tecnico, ':carga_horaria' => $oco_carga_horaria, ':autor_edicao' => $rvm_admin_id, ':id_original' => $oco_id_original]);
        //     $oco_id = $oco_id_original;


        } elseif ($acao === 'atualizar_admin') {
      if (empty($oco_id_original))
        throw new Exception("ID da ocorrência original não fornecido.");
      if (empty($_POST['oco_status']))
        throw new Exception("O Status é obrigatório.");
      if (empty($_POST['oco_parecer_tecnico']))
        throw new Exception("O Parecer Técnico é obrigatório.");
      $log_acao = 'Revisão (Admin)';
      $oco_id_redirecionamento = $oco_id_original;
      $stmt_leitura = $conn->prepare("SELECT * FROM ocorrencias WHERE oco_id = :id_original");
      $stmt_leitura->execute([':id_original' => $oco_id_original]);
      $dados_atuais = $stmt_leitura->fetch(PDO::FETCH_ASSOC);
      if (!$dados_atuais) {
        throw new Exception("Ocorrência a ser editada não encontrada.");
      }
      $dados_atuais['oco_hora_inicio_realizado'] = !empty($dados_atuais['oco_hora_inicio_realizado']) ? $dados_atuais['oco_hora_inicio_realizado'] : NULL;
      $dados_atuais['oco_hora_fim_realizado'] = !empty($dados_atuais['oco_hora_fim_realizado']) ? $dados_atuais['oco_hora_fim_realizado'] : NULL;
      $dados_atuais['oco_carga_horaria_calculada'] = !empty($dados_atuais['oco_carga_horaria_calculada']) ? $dados_atuais['oco_carga_horaria_calculada'] : NULL;
      $data_cad_formatada = $dados_atuais['oco_data_cad'] ? (new DateTime($dados_atuais['oco_data_cad']))->format('Y-m-d H:i:s') : NULL;
      $data_edicao_formatada = !empty($dados_atuais['oco_data_edicao']) ? (new DateTime($dados_atuais['oco_data_edicao']))->format('Y-m-d H:i:s') : NULL;
      $sql_historico = "INSERT INTO ocorrencias_historico (hist_admin_id, oco_id, oco_codigo, oco_solic_id, oco_res_id, oco_tipo_ocorrencia, oco_hora_inicio_realizado, oco_hora_fim_realizado, oco_obs, oco_user_id, oco_status, oco_versao_anterior_id, oco_parecer_tecnico, oco_carga_horaria_calculada, oco_autor_edicao, oco_data_cad, oco_data_edicao) VALUES (:hist_admin_id, :oco_id, :oco_codigo, :oco_solic_id, :oco_res_id, :oco_tipo_ocorrencia, :oco_hora_inicio_realizado, :oco_hora_fim_realizado, :oco_obs, :oco_user_id, :oco_status, :oco_versao_anterior_id, :oco_parecer_tecnico, :oco_carga_horaria_calculada, :oco_autor_edicao, :oco_data_cad, :oco_data_edicao)";
      $stmt_historico = $conn->prepare($sql_historico);
      $stmt_historico->execute([':hist_admin_id' => $rvm_admin_id, ':oco_id' => $dados_atuais['oco_id'], ':oco_codigo' => $dados_atuais['oco_codigo'], ':oco_solic_id' => $dados_atuais['oco_solic_id'], ':oco_res_id' => $dados_atuais['oco_res_id'], ':oco_tipo_ocorrencia' => $dados_atuais['oco_tipo_ocorrencia'], ':oco_hora_inicio_realizado' => $dados_atuais['oco_hora_inicio_realizado'], ':oco_hora_fim_realizado' => $dados_atuais['oco_hora_fim_realizado'], ':oco_obs' => $dados_atuais['oco_obs'], ':oco_user_id' => $dados_atuais['oco_user_id'], ':oco_status' => $dados_atuais['oco_status'], ':oco_versao_anterior_id' => $dados_atuais['oco_versao_anterior_id'], ':oco_parecer_tecnico' => $dados_atuais['oco_parecer_tecnico'], ':oco_carga_horaria_calculada' => $dados_atuais['oco_carga_horaria_calculada'], ':oco_autor_edicao' => $dados_atuais['oco_autor_edicao'], ':oco_data_cad' => $data_cad_formatada, ':oco_data_edicao' => $data_edicao_formatada]);
      $oco_status = $_POST['oco_status'];
      $oco_parecer_tecnico = nl2br(trim($_POST['oco_parecer_tecnico']));
      $oco_tipo_ocorrencia = implode(',', array_map('htmlspecialchars', $_POST['oco_tipo_ocorrencia']));
      $oco_hora_inicio_realizado = !empty($_POST['oco_hora_inicio_realizado']) ? $_POST['oco_hora_inicio_realizado'] : NULL;
      $oco_hora_fim_realizado = !empty($_POST['oco_hora_fim_realizado']) ? $_POST['oco_hora_fim_realizado'] : NULL;
      $carga_horaria_post = $_POST['oco_carga_horaria_calculada'] ?? NULL;
      $oco_carga_horaria = ($oco_status == 2 && !empty($carga_horaria_post)) ? $carga_horaria_post : NULL;
      $sql_update = "UPDATE ocorrencias SET oco_status = :status, oco_tipo_ocorrencia = :tipo_ocorrencia, oco_hora_inicio_realizado = :hora_inicio, oco_hora_fim_realizado = :hora_fim, oco_parecer_tecnico = :parecer, oco_carga_horaria_calculada = :carga_horaria, oco_autor_edicao = :autor_edicao, oco_data_edicao = GETDATE() WHERE oco_id = :id_original";
      $stmt_update = $conn->prepare($sql_update);
      $stmt_update->execute([':status' => $oco_status, ':tipo_ocorrencia' => $oco_tipo_ocorrencia, ':hora_inicio' => $oco_hora_inicio_realizado, ':hora_fim' => $oco_hora_fim_realizado, ':parecer' => $oco_parecer_tecnico, ':carga_horaria' => $oco_carga_horaria, ':autor_edicao' => $rvm_admin_id, ':id_original' => $oco_id_original]);
      $oco_id = $oco_id_original;

        } elseif ($acao === 'validar') {
            if (empty($oco_id_original)) {
                throw new Exception("ID da ocorrência para validação não fornecido.");
            }
            $log_acao = 'Validação Rápida';
            
            // CORREÇÃO: Redireciona para o ID da OCORRÊNCIA
            $oco_id_redirecionamento = $oco_id_original;

            // 1. Pega os horários do registro atual para cálculo
            $stmt_tempos = $conn->prepare("SELECT oco_hora_inicio_realizado, oco_hora_fim_realizado FROM ocorrencias WHERE oco_id = :oco_id");

            $stmt_tempos->execute([':oco_id' => $oco_id_original]);
            $tempos = $stmt_tempos->fetch(PDO::FETCH_ASSOC);

            if (!$tempos || empty($tempos['oco_hora_inicio_realizado']) || empty($tempos['oco_hora_fim_realizado'])) {
                throw new Exception("Não é possível validar: horários de início ou fim não estão definidos.");
            }
            // 2. Calcula a carga horária
            $inicio = new DateTime($tempos['oco_hora_inicio_realizado']);
            $fim = new DateTime($tempos['oco_hora_fim_realizado']);
            $intervalo = $inicio->diff($fim);
            $carga_horaria = $intervalo->format('%H:%I:%S'); // <--- A CARGA HORÁRIA É CALCULADA AQUI

            // 3. Atualiza o status e a carga horária no registro principal
            $sql_update = "UPDATE ocorrencias SET oco_status = 2, oco_carga_horaria_calculada = :carga_horaria, oco_autor_edicao = :autor_edicao, oco_data_edicao = GETDATE() WHERE oco_id = :oco_id";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->execute([
                ':carga_horaria' => $carga_horaria,
                ':autor_edicao' => $rvm_admin_id,
                ':oco_id' => $oco_id_original
            ]);

            $oco_id = $oco_id_original; // Define o ID para o log

        } elseif ($acao === 'deletar') { // <-- DELETAR
            $oco_id_original = $_GET['oco_id'] ?? NULL;
            if (empty($oco_id_original)) {
                throw new Exception("ID da ocorrência para exclusão não fornecido.");
            }

            // BUSCA O ID DA SOLICITAÇÃO ASSOCIADA À OCORRÊNCIA ANTES DE DELETAR
            $stmt_solic_id = $conn->prepare("SELECT oco_solic_id FROM ocorrencias WHERE oco_id = :oco_id");
            $stmt_solic_id->execute([':oco_id' => $oco_id_original]);
            $solic_id_para_redirecionar = $stmt_solic_id->fetchColumn();
            
            // Lança exceção se não conseguir o ID da Solicitação para voltar
            if (empty($solic_id_para_redirecionar)) {
                 throw new Exception("Não foi possível identificar a Solicitação para redirecionamento após exclusão.");
            }

            $log_acao = 'Exclusão';
            // DEFINE O REDIRECIONAMENTO COM O ID RESGATADO (ID da SOLICITAÇÃO)
            $oco_id_redirecionamento = $solic_id_para_redirecionar; 

            // SQL para DELETAR
            $sql = "DELETE FROM ocorrencias WHERE oco_id = :oco_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':oco_id' => $oco_id_original]);
            
            $oco_id = $oco_id_original; // Define o ID para o log

        } else {
            throw new Exception("Ação inválida.");
        }

        $log_dados = ['POST' => $_POST, 'GET' => $_GET];
        $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data) VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->execute([':modulo' => 'OCORRÊNCIAS', ':acao' => $log_acao, ':acao_id' => $oco_id, ':dados' => json_encode($log_dados, JSON_UNESCAPED_UNICODE), ':user_id' => $rvm_admin_id]);

        $conn->commit();

        if ($acao === 'cadastrar') {
            $_SESSION["msg"] = "Ocorrência cadastrada com sucesso!";
        } elseif ($acao === 'atualizar' || $acao === 'atualizar_admin') {
            $_SESSION["msg"] = "Ocorrência atualizada com sucesso!";
        } elseif ($acao === 'validar') {
            $_SESSION["msg"] = "Ocorrência validada com sucesso!";
        } elseif ($acao === 'deletar') { 
            $_SESSION["msg"] = "Ocorrência excluída com sucesso!";
        }

        // TRATAMENTO DE REDIRECIONAMENTO FINAL
        
        // 1. Define o URL base
        if ($acao === 'atualizar_admin' || $acao === 'validar') { // Incluindo 'validar'
            // O destino é a análise da Ocorrência.
            $redirect_base = "../admin/ocorrencia_analise.php";
        } else {
            // Demais ações (cadastrar, atualizar, deletar) redirecionam para a Solicitação
            $redirect_base = "../admin/solicitacao_analise.php";
        }
        
        // 2. Monta a URL final
        if (empty($oco_id_redirecionamento)) {
            // Último recurso: Se falhou em tudo, volta para a lista principal sem ID.
            $redirect_url = $redirect_base;
        } else {
            // Aponta para a página de análise correta com o ID
            $redirect_url = $redirect_base . "?i=" . urlencode($oco_id_redirecionamento);
        }

        header("Location: " . $redirect_url);
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION["erro"] = $e->getMessage();

        // Lógica de redirecionamento de erro
        $redirect_base = '../admin/solicitacao_analise.php';
        
        // CORREÇÃO NO CATCH: Se for atualizar_admin ou validar e falhar, tenta voltar para a tela de Ocorrência.
        if ($acao === 'atualizar_admin' || $acao === 'validar') {
             $redirect_base = "../admin/ocorrencia_analise.php";
        }

        // Tenta usar o ID da solicitação principal resgatado ($oco_solic_id) ou $oco_id_original
        $id_erro_redirect = $oco_solic_id ?? $oco_id_original;

        if (!empty($id_erro_redirect)) {
            $redirect_url = $redirect_base . "?i=" . urlencode($id_erro_redirect);
        } else {
            $redirect_url = $redirect_base;
        }

        header("Location: " . $redirect_url);
        exit;
    }
} else {
    $_SESSION["erro"] = "Requisição inválida.";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../admin/solicitacao_analise.php'));
    exit;
}
?>