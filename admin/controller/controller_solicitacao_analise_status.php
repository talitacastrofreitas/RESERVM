<?php
// session_start();
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // VARIÁVEIS CRÍTICAS INICIAIS (Para garantir scope e checagem)
  $solic_id = trim($_POST['solic_id'] ?? null);
  $acao = $_POST['acao'] ?? null;
  $log_acao = 'Ação não mapeada';
  $rvm_admin_id = $_SESSION['reservm_admin_id'] ?? null;

  // VALIDACAO RÁPIDA DE REQUISITOS MÍNIMOS
  if (empty($solic_id) || empty($acao) || empty($rvm_admin_id)) {
    $_SESSION["erro"] = "Parâmetros de requisição ou ID do Admin estão ausentes.";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit;
  }


  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO

    // --- Variáveis de POST ---
    $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo'] ?? '');
    $sta_an_obs = trim($_POST['sta_an_obs'] ?? '') !== '' ? nl2br(trim($_POST['sta_an_obs'] ?? '')) : NULL;


    // --- BUSCA CONTEXTO ADMIN/SAAP LOGADO ---
    $coordenador_nome = 'SAAP';
    $coordenador_row = $conn->prepare("SELECT admin_nome FROM admin WHERE admin_id = :admin_id");
    $coordenador_row->execute([':admin_id' => $rvm_admin_id]);
    $coordenador_fetch = $coordenador_row->fetch(PDO::FETCH_ASSOC);
    if ($coordenador_fetch) {
      $coordenador_nome = htmlspecialchars($coordenador_fetch['admin_nome']);
    }
    // ----------------------------------------


    // SE FOR UMA AÇÃO QUE REQUER NOTIFICAÇÃO AO SOLICITANTE, BUSCAR O E-MAIL
    $sta_an_user_email = $_SESSION['reservm_admin_email'] ?? 'admin@sistema.com'; // Default para evitar erro
    if (in_array($acao, ['deferir', 'indeferir', 'iniciar_analise', 'iniciar_analise_saap'])) {
      $sql_solicitante_email = "SELECT u.user_email
                                      FROM solicitacao s
                                      JOIN usuarios u ON u.user_id = s.solic_cad_por
                                      WHERE s.solic_id = :solic_id";
      $stmt_email = $conn->prepare($sql_solicitante_email);
      $stmt_email->execute([':solic_id' => $solic_id]);
      $solicitante_email = $stmt_email->fetchColumn();

      if (!empty($solicitante_email)) {
        $sta_an_user_email = $solicitante_email;
      }
    }
    // ---------------------------------------------------------------------

    $num_status_defere = 4; // RESERVADO
    $num_status_indefere = 6; // INDEFERIDO


    // -------------------------------
    // DEFERIR SOLICITAÇÃO (Finaliza como RESERVADO - 4)
    // -------------------------------
    if ($acao === 'deferir') {

      $log_acao = 'Deferido SAAP';
      $num_status_final = $num_status_defere; // ID 4 (RESERVADO)

      // 1. REGISTRA O NOVO STATUS NA TABELA DE HISTÓRICO
      $sql = "INSERT INTO solicitacao_analise_status (
                                                            sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd
                                                            ) VALUES (
                                                            :sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE()
                                                            )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':sta_an_solic_id' => $solic_id, ':sta_an_status' => $num_status_final, ':sta_an_obs' => $sta_an_obs, ':sta_an_user_id' => $rvm_admin_id]);

      // 2. ALTERA O STATUS DA SOLICITAÇÃO PRINCIPAL
      $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE()
                    WHERE solic_sta_solic_id = :solic_sta_solic_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([':solic_sta_solic_id' => $solic_id, ':solic_sta_status' => $num_status_final, ':solic_sta_user_id' => $rvm_admin_id]);

      // [Bloco de Busca e Formatação de Reservas - Início]
      $sql_reservas = "SELECT res.res_data, res.res_hora_inicio, res.res_hora_fim, u.uni_unidade AS campus, e.esp_nome_local AS local_reservado, tr.ctr_tipo_reserva AS tipo_reserva, pav.pav_pavilhao, a.and_andar, dw.week_dias AS dia_semana FROM reservas res JOIN solicitacao s ON s.solic_id = res.res_solic_id LEFT JOIN unidades u ON u.uni_id = res.res_campus JOIN espaco e ON e.esp_id = res.res_espaco_id JOIN conf_tipo_reserva tr ON tr.ctr_id = res.res_tipo_reserva LEFT JOIN pavilhoes pav ON pav.pav_id = e.esp_pavilhao LEFT JOIN andares a ON a.and_id = e.esp_andar LEFT JOIN conf_dias_semana dw ON dw.week_id = CASE WHEN res.res_tipo_reserva = 2 THEN res.res_dia_semana ELSE DATEPART(WEEKDAY, res.res_data) END WHERE res.res_solic_id = :solic_id ORDER BY res.res_data, res.res_hora_inicio";
      $stmt_reservas = $conn->prepare($sql_reservas);
      $stmt_reservas->execute([':solic_id' => $solic_id]);
      $reservas_confirmadas = $stmt_reservas->fetchAll(PDO::FETCH_ASSOC);
      $reservas_tabela = '';

      if (!empty($reservas_confirmadas)) {
        $reservas_tabela = '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                                        <thead>
                                            <tr style="background-color: #f2f2f2;">
                                                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Data</th>
                                                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Dia Sem.</th>
                                                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Início</th>
                                                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Fim</th>
                                                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Local</th>
                                                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Campus</th>
                                                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Tipo Reserva</th>
                                            </tr>
                                        </thead>
                                        <tbody>';

        foreach ($reservas_confirmadas as $reserva) {
          $data_formatada = date('d/m/Y', strtotime($reserva['res_data']));
          $hora_inicio_formatada = date('H:i', strtotime($reserva['res_hora_inicio']));
          $hora_fim_formatada = date('H:i', strtotime($reserva['res_hora_fim']));

          $localizacao_completa = htmlspecialchars($reserva['local_reservado']);
          if (!empty($reserva['and_andar'])) {
            $localizacao_completa .= ' / ' . htmlspecialchars($reserva['and_andar']);
          }
          if (!empty($reserva['pav_pavilhao'])) {
            $localizacao_completa .= ' / ' . htmlspecialchars($reserva['pav_pavilhao']);
          }

          $dia_semana_display = htmlspecialchars($reserva['dia_semana']) ?: '-';

          $reservas_tabela .= '<tr>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . $data_formatada . '</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . $dia_semana_display . '</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . $hora_inicio_formatada . '</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . $hora_fim_formatada . '</td>
                                            <td style="padding: 8px; border: 1px solid #ddd; text-transform: uppercase">' . $localizacao_completa . '</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($reserva['campus']) . '</td>
                                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($reserva['tipo_reserva']) . '</td>
                                        </tr>';
        }
        $reservas_tabela .= '</tbody></table>';
      } else {
        $reservas_tabela = '<p style="font-size: 1rem; color: #515050;">A solicitação foi deferida, mas nenhuma reserva foi encontrada ou cadastrada. Verifique o sistema.</p>';
      }
      // [Bloco de Busca e Formatação de Reservas - Fim]

      // ENVIO DE E-MAIL PARA O SOLICITANTE (USUÁRIO)
      $mail = new PHPMailer(true);
      include '../conexao/email.php';
      $mail->addAddress($sta_an_user_email, 'RESERVM');
      $mail->isHTML(true);
      $mail->Subject = 'CONFIRMAÇÃO: Solicitação Deferida e Reservada - ' . $sta_an_solic_codigo;

      $email_conteudo = ''; // Reinicializa $email_conteudo
      include '../includes/email/email_header.php';

      $email_conteudo .= "
            <tr style='background-color: #ffffff; text-align: center; color: #515050; display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
                <td style='padding: 2em 2rem; display: inline-block; width:100%;'>
                    <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                        <strong>SOLICITAÇÃO RESERVADA</strong>
                    </p>
                    <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
                    Prezado(a) solicitante,
                    </p>
                    <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
                    Sua solicitação de código <strong>" . $sta_an_solic_codigo . "</strong> foi deferida e as reservas de espaço foram confirmadas por <strong>" . $coordenador_nome . "</strong>.
                    </p>
                    
                    <p style='font-size: 1rem; font-weight: 500; margin: 25px 0px 10px 0px; text-align: left;'>
                    Detalhes das Reservas:
                    </p>
                    
                    " . $reservas_tabela . "
                    
                    <p style='font-size: 1rem; text-align: left; background: #F3F6F9; padding: 20px; margin: 20px 0px'>
                    Observação: " . ($sta_an_obs ?: 'Nenhuma observação registrada.') . "
                    </p>

                    <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
                </td>
            </tr>";

      include '../includes/email/email_footer.php';
      $mail->Body = $email_conteudo;

      try {
        $mail->send();
      } catch (Exception $e) {
        error_log("Erro ao enviar e-mail de deferimento para usuário: " . $e->getMessage());
      }
    }
    // --- Fim Lógica Deferimento SAAP ---

    // -------------------------------
    // INDEFERIR SOLICITAÇÃO
    // -------------------------------
    elseif ($acao === 'indeferir') {
      $log_acao = 'Indeferido SAAP';
      $num_status_final = $num_status_indefere; // ID 6

      $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':sta_an_solic_id' => $solic_id, ':sta_an_status' => $num_status_final, ':sta_an_obs' => $sta_an_obs, ':sta_an_user_id' => $rvm_admin_id]);

      // ALTERA O STATUS DA SOLICITAÇÃO
      $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE() WHERE solic_sta_solic_id = :solic_sta_solic_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':solic_sta_solic_id' => $solic_id, ':solic_sta_status' => $num_status_final, ':solic_sta_user_id' => $rvm_admin_id]);

      // ENVIO DE E-MAIL PARA O SOLICITANTE (USUÁRIO)
      $mail = new PHPMailer(true);
      include '../conexao/email.php';
      $mail->addAddress($sta_an_user_email, 'RESERVM');
      $mail->isHTML(true);
      $mail->Subject = 'Solicitação indeferida: ' . $sta_an_solic_codigo;
      include '../includes/email/email_header.php';
      $email_conteudo .= "
            <tr style='background-color: #ffffff; text-align: center; color: #515050; display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
                <td style='padding: 2em 2rem; display: inline-block;  width:100%;'>
                    <p style='font-size: 1.188rem; font-weight: 500; margin: 0px 0px 20px 0px;'>
                        <strong>SOLICITAÇÃO INDEFERIDA</strong>
                    </p>
                    <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
                        Prezado(a) solicitante,
                    </p>
                    <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 15px 0px; text-align: left;'>
                        A solicitação de código <strong>" . $sta_an_solic_codigo . "</strong> foi indeferida por <strong>" . $coordenador_nome . "</strong>.
                    </p>
                    <p style='font-size: 1rem; text-align: left; background: #F3F6F9; padding: 20px; margin: 20px 0px'>
                        <strong>Motivo do Indeferimento:</strong><br>" . ($sta_an_obs ?: 'Nenhuma observação foi fornecida.') . "
                    </p>
                    <a style='cursor: pointer;' href='$url_sistema'><button style='background: #C4453E; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 20px;' target='_blank'>Acesse o sistema</button></a>
                </td>
            </tr>";
      include '../includes/email/email_footer.php';
      $mail->Body = $email_conteudo;

      try {
        $mail->send();
      } catch (Exception $e) {
        error_log("Erro ao enviar e-mail de indeferimento para usuário: " . $e->getMessage());
      }
    }
    // --- Fim Lógica Indeferimento SAAP ---


    // -------------------------------
    // INICIAR ANÁLISE SAAP (Status 2/5 -> Status 7)
    // -------------------------------
    elseif ($acao === 'iniciar_analise_saap') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (empty($_POST['solic_id']) || empty($_POST['sta_an_solic_codigo'])) {
        throw new Exception("ID da Solicitação e Código são obrigatórios!");
      }

      // POST
      $solic_id = trim($_POST['solic_id']);
      $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo']);
      $sta_an_obs = trim($_POST['sta_an_obs'] ?? '') !== '' ? nl2br(trim($_POST['sta_an_obs'] ?? '')) : NULL;

      $num_status_novo = 7; // EM ANÁLISE PELO SAAP
      $log_acao = 'Iniciada Análise SAAP';

      // 1. REGISTRA O NOVO STATUS NA TABELA DE HISTÓRICO
      $sql = "INSERT INTO solicitacao_analise_status (
                                                            sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd
                                                            ) VALUES (
                                                            :sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE()
                                                            )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':sta_an_solic_id' => $solic_id,
        ':sta_an_status' => $num_status_novo,
        ':sta_an_obs' => $sta_an_obs,
        ':sta_an_user_id' => $rvm_admin_id
      ]);

      // 2. ATUALIZA O STATUS NA TABELA PRINCIPAL
      $sql = "UPDATE solicitacao_status
                    SET        
                          solic_sta_status   = :solic_sta_status,
                          solic_sta_user_id  = :solic_sta_user_id,
                          solic_sta_data_cad = GETDATE()
                    WHERE
                          solic_sta_solic_id = :solic_sta_solic_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':solic_sta_solic_id' => $solic_id,
        ':solic_sta_status' => $num_status_novo,
        ':solic_sta_user_id' => $rvm_admin_id
      ]);
      // -------------------------------

      // -------------------------------
      // INICIAR ANÁLISE COORDENADOR
      // -------------------------------
    } elseif ($acao === 'iniciar_analise') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (empty($_POST['solic_id']) || empty($_POST['sta_an_solic_codigo'])) {
        throw new Exception("Preencha os campos obrigatórios da solicitação!");
      }

      // POST
      $solic_id = trim($_POST['solic_id']);
      $sta_an_solic_codigo = trim($_POST['sta_an_solic_codigo']);
      $sta_an_obs = trim($_POST['sta_an_obs'] ?? '') !== '' ? nl2br(trim($_POST['sta_an_obs'] ?? '')) : NULL;

      // ** AÇÃO DO COORDENADOR **
      $num_status_novo = 3; // EM ANÁLISE PELO COORDENADOR
      $log_acao = 'Iniciada Análise Coordenador';

      // 1. REGISTRA O NOVO STATUS NA TABELA DE HISTÓRICO
      $sql = "INSERT INTO solicitacao_analise_status (
                                                            sta_an_solic_id, sta_an_status, sta_an_obs, sta_an_user_id, sta_an_data_cad, sta_an_data_upd
                                                            ) VALUES (
                                                            :sta_an_solic_id, :sta_an_status, :sta_an_obs, :sta_an_user_id, GETDATE(), GETDATE()
                                                            )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':sta_an_solic_id' => $solic_id,
        ':sta_an_status' => $num_status_novo,
        ':sta_an_obs' => $sta_an_obs,
        ':sta_an_user_id' => $rvm_admin_id // Usa o ID do admin/SAAP logado
      ]);

      // 2. ATUALIZA O STATUS NA TABELA PRINCIPAL
      $sql = "UPDATE solicitacao_status
                    SET        
                          solic_sta_status   = :solic_sta_status,
                          solic_sta_user_id  = :solic_sta_user_id,
                          solic_sta_data_cad = GETDATE()
                    WHERE
                          solic_sta_solic_id = :solic_sta_solic_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':solic_sta_solic_id' => $solic_id,
        ':solic_sta_status' => $num_status_novo,
        ':solic_sta_user_id' => $rvm_admin_id
      ]);
      // -------------------------------

    } // FIM DO IF PRINCIPAL

    // REGISTRA NO LOG
    $log_dados = ['POST' => $_POST, 'GET' => $_GET, 'FILES' => $_FILES];
    $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
                  VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
    $stmtLog = $conn->prepare($sqlLog);
    $stmtLog->execute([
      ':modulo' => 'SOLICITAÇÃO STATUS',
      ':acao' => $log_acao,
      ':acao_id' => $solic_id,
      ':dados' => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $rvm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    if ($acao === 'deferir') {
      $_SESSION["msg"] = "Solicitação deferida!";
    } elseif ($acao === 'indeferir') {
      $_SESSION["msg"] = "Solicitação indeferida!";
    } elseif ($acao === 'iniciar_analise_saap') {
      $_SESSION["msg"] = "Análise iniciada pelo SAAP!";
    } elseif ($acao === 'iniciar_analise') {
      $_SESSION["msg"] = "Análise iniciada pelo Coordenador!";
    }
    // -------------------------------
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  exit;
}
