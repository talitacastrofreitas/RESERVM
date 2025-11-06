<?php
// session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// === CORREÇÃO DE CAMINHO (Assumindo 'conexao' está dois níveis acima) ===
include '../../conexao/conexao.php'; // Inclui a conexão com o banco

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// === CORREÇÃO DE CAMINHO (Assumindo 'vendor' está dois níveis acima) ===
require '../../vendor/autoload.php'; // Carrega o autoloader do Composer (PHPMailer)


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['acao'], $_POST['solcanc_id'])) {
    // Redireciona se não for um POST válido
    header('Location: ../canceladas.php'); // Redireciona para o nível acima
    exit;
}

// $solcanc_id = filter_var($_POST['solcanc_id'], FILTER_SANITIZE_STRING);
$solcanc_id = trim(filter_var($_POST['solcanc_id'], FILTER_SANITIZE_STRING));
$acao = $_POST['acao'];

// Verifica se $conn foi inicializado após a inclusão
if (!isset($conn)) {
    $_SESSION["erro"] = "Erro de conexão com o banco de dados. Verifique o arquivo de inclusão.";
    header('Location: ../canceladas.php');
    exit;
}
$conn->beginTransaction();

// Variáveis do Admin Logado (para usar no email e logs)
$admin_id = $_SESSION['reservm_admin_id'] ?? $_SESSION['user_id'] ?? null;
$admin_nome = '';



// Busca o nome do administrador logado (adapte conforme sua tabela de admin/usuário)
$admin_row = $conn->prepare("SELECT admin_nome FROM admin WHERE admin_id = :admin_id");
if (!$admin_row->execute([':admin_id' => $admin_id])) {
    // Tenta a tabela 'admin' se 'Usuarios' não funcionar ou se o ID estiver errado
    $admin_row = $conn->prepare("SELECT admin_nome FROM admin WHERE admin_id = :admin_id");
    if ($admin_row->execute([':admin_id' => $admin_id])) {
        $admin_fetch = $admin_row->fetch(PDO::FETCH_ASSOC);
        if ($admin_fetch) {
            $admin_nome = htmlspecialchars($admin_fetch['admin_nome']);
        }
    }
} else {
    $admin_fetch = $admin_row->fetch(PDO::FETCH_ASSOC);
    if ($admin_fetch) {
        $admin_nome = htmlspecialchars($admin_fetch['admin_nome']);
    }
}

// Verifica se o ID do admin foi obtido
if (empty($admin_id)) {
    $_SESSION["erro"] = "ID do Admin/Usuário logado ausente.";
    header('Location: ../canceladas.php');
    exit;
}


echo "Administrador Logado: " . $admin_nome;

try {
    // 1. Obter detalhes da solicitação de cancelamento e do alvo (reserva/solicitação)
    $sql_detalhes = "
        SELECT 
            c.solcanc_tipo, 
            c.solcanc_res_id, 
            c.solcanc_solic_id, 
            c.solcanc_data_solic, /* <<< CORREÇÃO DE NOME DE COLUNA (ERA solcanc_data_cad) */
            u.user_email, 
            u.user_nome,
            
            /* Se for Solicitação, busca a data mais FUTURA da RESERVA. Se for Reserva, usa a data da reserva. */
            CASE 
                WHEN c.solcanc_tipo = 'Solicitacao' THEN (SELECT MAX(res_data) FROM Reservas WHERE res_solic_id = c.solcanc_solic_id)
                ELSE r.res_data
            END AS data_alvo,
            
            ISNULL(r.res_codigo, s.solic_codigo) AS codigo_alvo
        FROM solicitacao_cancelamento c
        LEFT JOIN Usuarios u ON u.user_id = c.solcanc_usuario_id
        LEFT JOIN Reservas r ON r.res_id = c.solcanc_res_id
        LEFT JOIN Solicitacao s ON s.solic_id = c.solcanc_solic_id
        WHERE c.solcanc_id = :solcanc_id
    ";
    $stmt_detalhes = $conn->prepare($sql_detalhes);
    $stmt_detalhes->execute([':solcanc_id' => $solcanc_id]);
    $detalhes = $stmt_detalhes->fetch(PDO::FETCH_ASSOC);

    if (!$detalhes) {
        throw new Exception("Solicitação de cancelamento não encontrada.");
    }

    // Email do Solicitante
    $solicitante_email = $detalhes['user_email'] ?? 'usuario@sistema.com';
    $solicitante_nome = $detalhes['user_nome'] ?? 'Solicitante';


    $data_alvo = new DateTime($detalhes['data_alvo']);

    // Usar a data de criação do cancelamento, NÃO a data/hora atual
    $data_solicitacao_canc = new DateTime($detalhes['solcanc_data_solic']); // CORREÇÃO AQUI

    // Calcula a diferença em segundos entre a data ALVO e a data de SOLICITAÇÃO DE CANCELAMENTO
    $diff_seconds = $data_alvo->getTimestamp() - $data_solicitacao_canc->getTimestamp();

    $tipo_alvo = strtolower($detalhes['solcanc_tipo']); // Garante minúsculo para a comparação
    $id_alvo = ($tipo_alvo === 'reserva') ? $detalhes['solcanc_res_id'] : $detalhes['solcanc_solic_id'];

    $log_acao = ''; // Inicializa log_acao
    $dentro_do_prazo = false; // Inicializa

    // === LÓGICA DE PRAZO (48h para Solicitação, 24h/48h para Reserva) ===
    if ($tipo_alvo === 'solicitacao') {
        // Regra Solicitação: Dentro do prazo se a data mais futura for >= 48h da data da solicitação de cancelamento.
        $limite_solic_em_segundos = 48 * 3600; // 48 horas
        $dentro_do_prazo = $diff_seconds >= $limite_solic_em_segundos;
    } else { // Reserva
        // Regra Reserva: Dentro do prazo se a reserva ocorrer em 48h ou mais.
        $limite_48h = 48 * 3600;
        $dentro_do_prazo = $diff_seconds >= $limite_48h;
    }
    // FIM DA LÓGICA DE PRAZO

    if ($acao === 'confirmar') {
        $obs_admin = filter_var($_POST['obs_admin'] ?? '', FILTER_SANITIZE_STRING);
        $log_acao = 'Cancelamento Aprovado';

        // 2. Atualizar tabela solicitacao_cancelamento (solcanc_status = 2 (Aprovado))
        $sql_update_canc = "
            UPDATE solicitacao_cancelamento SET 
                solcanc_status = 2, 
                solcanc_admin_id = :admin_id, 
                solcanc_data_proc = GETDATE(), 
                solcanc_obs_admin = :obs_admin,
                solcanc_motivo_negacao = NULL
            WHERE solcanc_id = :solcanc_id
        ";
        $stmt_update_canc = $conn->prepare($sql_update_canc);
        $stmt_update_canc->execute([
            ':admin_id' => $admin_id,
            ':obs_admin' => $obs_admin,
            ':solcanc_id' => $solcanc_id
        ]);

        // === AJUSTE DE STATUS AQUI (9 = Dentro do Prazo, 8 = Fora do Prazo) ===
        $novo_status_alvo = $dentro_do_prazo ? 9 : 8;
        $motivo_cancelamento = $dentro_do_prazo ? "Cancelado dentro do prazo (Aprovado por {$admin_nome})." : "Cancelado fora do prazo (Aprovado por {$admin_nome}).";

        // 3. Atualizar tabela de destino
        if ($tipo_alvo === 'reserva') {
            // Atualiza a tabela Reservas
            $sql_update_alvo = "
                UPDATE Reservas SET 
                    res_status = :status_alvo,
                    res_motivo_cancelamento = :motivo_canc 
                WHERE res_id = :id_alvo
            ";
            $stmt_update_alvo = $conn->prepare($sql_update_alvo);
            $stmt_update_alvo->execute([
                ':status_alvo' => $novo_status_alvo,
                ':motivo_canc' => $motivo_cancelamento,
                ':id_alvo' => $id_alvo
            ]);
            // Verifica se a atualização da RESERVA ocorreu
            if ($stmt_update_alvo->rowCount() === 0) {
                // Se for uma reserva e NENHUMA linha foi atualizada, algo está errado com o ID
                throw new Exception("Falha ao atualizar o status da Reserva (ID: {$id_alvo}). Verifique o ID alvo e o tipo.");
            }
        } else {
            // Atualiza a tabela solicitacao_status
            $sql_update_alvo = "
                UPDATE solicitacao_status SET 
                    solic_sta_status = :status_alvo,
                    solic_sta_user_id = :admin_id,
                    solic_sta_data_cad = GETDATE(),
                    solic_motivo_cancelamento = :motivo_canc
                WHERE solic_sta_solic_id = :id_alvo
            ";
            $stmt_update_alvo = $conn->prepare($sql_update_alvo);
            $stmt_update_alvo->execute([
                ':status_alvo' => $novo_status_alvo,
                ':admin_id' => $admin_id,
                ':motivo_canc' => $motivo_cancelamento,
                ':id_alvo' => $id_alvo
            ]);
        }
        // Fim da atualização da tabela de destino

        // 4. REGISTRAR O STATUS DE CANCELAMENTO NA TABELA DE HISTÓRICO VISÍVEL (solicitacao_analise_status)
        $solic_id_para_historico = $detalhes['solcanc_solic_id'];

        if (!empty($solic_id_para_historico)) {
            $sql_insert_analise_status = "
        INSERT INTO solicitacao_analise_status (
            sta_an_solic_id, 
            sta_an_status, 
            sta_an_obs, 
            sta_an_user_id, 
            sta_an_data_cad, /* <<< COLUNA ADICIONADA */
            sta_an_data_upd
        ) VALUES (
            :solic_id, 
            :status_alvo, 
            :motivo_canc, 
            :admin_id, 
            GETDATE(),   /* <<< VALOR ADICIONADO */
            GETDATE()
        )";
            $stmt_insert_analise = $conn->prepare($sql_insert_analise_status);
            $stmt_insert_analise->execute([
                ':solic_id' => $solic_id_para_historico,
                ':status_alvo' => $novo_status_alvo, // Será 8 ou 9
                ':motivo_canc' => $motivo_cancelamento,
                ':admin_id' => $admin_id
            ]);
        }
        // Fim da inserção em solicitacao_analise_status

        // --- ENVIAR E-MAIL DE CONFIRMAÇÃO ---
        $mail = new PHPMailer(true);

        // === CORREÇÃO DE CAMINHO (Assumindo 'conexao' está dois níveis acima) ===
        include '../../conexao/email.php'; // Configurações do SMTP

        $mail->addAddress($solicitante_email, $solicitante_nome);
        $mail->isHTML(true);
        $mail->Subject = 'RESERVM: Solicitação de Cancelamento Aprovada - ' . $detalhes['codigo_alvo'];

        $email_conteudo = '';

        // === CAMINHO DO TEMPLATE (Assumindo 'includes' está um nível acima) ===
        include '../../includes/email/email_header.php'; // Header do e-mail

        $email_conteudo .= "
            <tr style='background-color: #ffffff; text-align: center; color: #515050; display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
              <td style='padding: 2em 2rem; display: inline-block;  width:100%;'>
                    <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                        <strong>CANCELAMENTO APROVADO</strong>
                    </p>
                    <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
                    Prezado(a) {$solicitante_nome},
                    </p>
                    <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
                    Sua solicitação de cancelamento de código <strong>" . $detalhes['codigo_alvo'] . "</strong> foi APROVADA.
                    </p>
                    
                    
                    <p style='font-size: 1rem; background: #F3F6F9; padding: 20px; margin: 20px 0px; text-align: left;'>
                    <strong>Observação:</strong><br>  " . ($obs_admin ?: 'Nenhuma observação registrada.') . "
                    </p>

                    <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
                </td>
            </tr>";

        include '../../includes/email/email_footer.php'; // Footer do e-mail
        $mail->Body = $email_conteudo;

        try {
            $mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de confirmação de cancelamento para usuário: " . $e->getMessage());
        }
        // --- FIM E-MAIL ---

        $mensagem_sucesso = "Cancelamento **aprovado** com sucesso! Status atualizado para o alvo: " . ($dentro_do_prazo ? "Dentro do Prazo" : "Fora do Prazo") . ".";

    } elseif ($acao === 'negar') {
        $motivo_negacao = filter_var($_POST['motivo_negacao'] ?? '', FILTER_SANITIZE_STRING);
        $log_acao = 'Cancelamento Negado';

        if (empty($motivo_negacao)) {
            throw new Exception("O motivo da negação é obrigatório.");
        }

        // 2. Atualizar tabela solicitacao_cancelamento (solcanc_status = 3 (Negado))
        $sql_update_canc = "
            UPDATE solicitacao_cancelamento SET 
                solcanc_status = 3, 
                solcanc_admin_id = :admin_id, 
                solcanc_data_proc = GETDATE(), 
                solcanc_motivo_negacao = :motivo_negacao,
                solcanc_obs_admin = NULL
            WHERE solcanc_id = :solcanc_id
        ";
        $stmt_update_canc = $conn->prepare($sql_update_canc);
        $stmt_update_canc->execute([
            ':admin_id' => $admin_id,
            ':motivo_negacao' => $motivo_negacao,
            ':solcanc_id' => $solcanc_id
        ]);

        // O status da RESERVA/SOLICITAÇÃO não muda, permanece como estava antes.

        // --- ENVIAR E-MAIL DE NEGAÇÃO ---
        $mail = new PHPMailer(true);
        // === CORREÇÃO DE CAMINHO (Assumindo 'conexao' está dois níveis acima) ===
        include '../../conexao/email.php'; // Configurações do SMTP

        $mail->addAddress($solicitante_email, $solicitante_nome);
        $mail->isHTML(true);
        $mail->Subject = 'Cancelamento Negado - ' . $detalhes['codigo_alvo'];

        $email_conteudo = '';
        // === CAMINHO DO TEMPLATE (Assumindo 'includes' está um nível acima) ===
        include '../../includes/email/email_header.php'; // Header do e-mail

        $email_conteudo .= "
            <tr style='background-color: #ffffff; text-align: center; color: #515050; display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
             <td style='padding: 2em 2rem; display: inline-block;  width:100%;'>
                    <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                        <strong>SOLICITAÇÃO DE CANCELAMENTO NEGADA</strong>
                    </p>
                    <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
                        Prezado(a) {$solicitante_nome},
                    </p>
                    <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
                        Sua solicitação de cancelamento de código <strong>" . $detalhes['codigo_alvo'] . "</strong> foi NEGADA.
                    </p>
                    <p style='font-size: 1rem; text-align: left; background: #F3F6F9; padding: 20px; margin: 20px 0px'>
                        <strong>Motivo:</strong><br>" . $motivo_negacao . "
                    </p>
                    <a style='cursor: pointer;' href='$url_sistema'><button style='background: #C4453E; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
                </td>
            </tr>";

        include '../../includes/email/email_footer.php'; // Footer do e-mail
        $mail->Body = $email_conteudo;

        try {
            $mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de negação de cancelamento para usuário: " . $e->getMessage());
        }
        // --- FIM E-MAIL ---

        $mensagem_sucesso = "Cancelamento negado com sucesso! O usuário foi notificado.";
    }

    // REGISTRA NO LOG
    $log_dados = ['POST' => $_POST, 'GET' => $_GET]; // Não inclui FILES, como no seu exemplo
    $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
                  VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
    $stmtLog = $conn->prepare($sqlLog);
    $stmtLog->execute([
        ':modulo' => 'CANCELAMENTO',
        ':acao' => $log_acao,
        ':acao_id' => $solcanc_id,
        ':dados' => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
        ':user_id' => $admin_id
    ]);
    // -------------------------------

    $conn->commit();

    // Redireciona com mensagem de sucesso
    $_SESSION['sucesso'] = $mensagem_sucesso;
    header('Location: ../canceladas.php'); // Redirecionar para a página da lista
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    // Redireciona com mensagem de erro
    $_SESSION['erro'] = "Erro ao processar o cancelamento: " . $e->getMessage();
    header('Location: ../canceladas.php');
    exit;
}
?>