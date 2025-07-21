<?php
session_start();
include '../conexao/conexao.php';

// NECESSÁRIO PARA ENVIAR O EMAIL
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
// -------------------------------

if ($_SERVER["REQUEST_METHOD"] === "GET") {

  $solic_id      = $_GET['i'];
  $novo_solic_id = bin2hex(random_bytes(16)); // GERA UM ID ÚNICO SEGURO
  $solic_codigo  = 'SO' . random_int(100000, 999999);
  $reservm_user_id  = $_SESSION['reservm_user_id'];


  try {
    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_solic = "SELECT * FROM solicitacao WHERE solic_id = ?";
    $stmt = $conn->prepare($sql_solic);
    $stmt->execute([$solic_id]);
    $rows_solic = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_solic) > 0) {
      foreach ($rows_solic as $solic) {

        $solic_etapa  = 1;

        $sql_insert_solic = "INSERT INTO solicitacao (
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
                                                      solic_ap_aula_pratica,
                                                      solic_ap_campus,
                                                      solic_ap_espaco,
                                                      solic_ap_quant_turma,
                                                      solic_ap_quant_particip,
                                                      solic_ap_tipo_reserva,
                                                      solic_ap_data_reserva,
                                                      solic_ap_dia_reserva,
                                                      solic_ap_hora_inicio,
                                                      solic_ap_hora_fim,
                                                      solic_ap_tipo_material,
                                                      solic_ap_tit_aulas,
                                                      solic_ap_quant_material,
                                                      solic_ap_obs,
                                                      solic_at_aula_teorica,
                                                      solic_at_campus,
                                                      solic_at_quant_sala,
                                                      solic_at_quant_particip,
                                                      solic_at_tipo_reserva,
                                                      solic_at_data_reserva,
                                                      solic_at_dia_reserva,
                                                      solic_at_hora_inicio,
                                                      solic_at_hora_fim,
                                                      solic_at_recursos,
                                                      solic_at_obs,
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
                                                      :solic_nome_curso_text,
                                                      :solic_nome_atividade,
                                                      :solic_nome_comp_ativ,
                                                      :solic_semestre,
                                                      :solic_nome_prof_resp,
                                                      :solic_contato,
                                                      :solic_ap_aula_pratica,
                                                      :solic_ap_campus,
                                                      :solic_ap_espaco,
                                                      :solic_ap_quant_turma,
                                                      :solic_ap_quant_particip,
                                                      :solic_ap_tipo_reserva,
                                                      :solic_ap_data_reserva,
                                                      :solic_ap_dia_reserva,
                                                      :solic_ap_hora_inicio,
                                                      :solic_ap_hora_fim,
                                                      :solic_ap_tipo_material,
                                                      :solic_ap_tit_aulas,
                                                      :solic_ap_quant_material,
                                                      :solic_ap_obs,
                                                      :solic_at_aula_teorica,
                                                      :solic_at_campus,
                                                      :solic_at_quant_sala,
                                                      :solic_at_quant_particip,
                                                      :solic_at_tipo_reserva,
                                                      :solic_at_data_reserva,
                                                      :solic_at_dia_reserva,
                                                      :solic_at_hora_inicio,
                                                      :solic_at_hora_fim,
                                                      :solic_at_recursos,
                                                      :solic_at_obs,
                                                      :solic_cad_por,
                                                      GETDATE(),
                                                      :solic_upd_por,
                                                      GETDATE()
                                                    )";

        $stmt_insert = $conn->prepare($sql_insert_solic);
        $stmt_insert->execute([
          ':solic_id' => $novo_solic_id,
          ':solic_codigo' => $solic_codigo,
          ':solic_etapa' => $solic_etapa,
          ':solic_curso' => $solic['solic_curso'],
          ':solic_comp_curric' => $solic['solic_comp_curric'],
          ':solic_nome_curso' => $solic['solic_nome_curso'],
          ':solic_nome_curso_text' => $solic['solic_nome_curso_text'],
          ':solic_nome_atividade' => $solic['solic_nome_atividade'],
          ':solic_nome_comp_ativ' => $solic['solic_nome_comp_ativ'],
          ':solic_semestre' => $solic['solic_semestre'],
          ':solic_nome_prof_resp' => $solic['solic_nome_prof_resp'],
          ':solic_contato' => $solic['solic_contato'],
          ':solic_ap_aula_pratica' => $solic['solic_ap_aula_pratica'],
          ':solic_ap_campus' => $solic['solic_ap_campus'],
          ':solic_ap_espaco' => $solic['solic_ap_espaco'],
          ':solic_ap_quant_turma' => $solic['solic_ap_quant_turma'],
          ':solic_ap_quant_particip' => $solic['solic_ap_quant_particip'],
          ':solic_ap_tipo_reserva' => $solic['solic_ap_tipo_reserva'],
          ':solic_ap_data_reserva' => $solic['solic_ap_data_reserva'],
          ':solic_ap_dia_reserva' => $solic['solic_ap_dia_reserva'],
          ':solic_ap_hora_inicio' => $solic['solic_ap_hora_inicio'],
          ':solic_ap_hora_fim' => $solic['solic_ap_hora_fim'],
          ':solic_ap_tipo_material' => $solic['solic_ap_tipo_material'],
          ':solic_ap_tit_aulas' => $solic['solic_ap_tit_aulas'],
          ':solic_ap_quant_material' => $solic['solic_ap_quant_material'],
          ':solic_ap_obs' => $solic['solic_ap_obs'],
          ':solic_at_aula_teorica' => $solic['solic_at_aula_teorica'],
          ':solic_at_campus' => $solic['solic_at_campus'],
          ':solic_at_quant_sala' => $solic['solic_at_quant_sala'],
          ':solic_at_quant_particip' => $solic['solic_at_quant_particip'],
          ':solic_at_tipo_reserva' => $solic['solic_at_tipo_reserva'],
          ':solic_at_data_reserva' => $solic['solic_at_data_reserva'],
          ':solic_at_dia_reserva' => $solic['solic_at_dia_reserva'],
          ':solic_at_hora_inicio' => $solic['solic_at_hora_inicio'],
          ':solic_at_hora_fim' => $solic['solic_at_hora_fim'],
          ':solic_at_recursos' => $solic['solic_at_recursos'],
          ':solic_at_obs' => $solic['solic_at_obs'],
          ':solic_cad_por' => $reservm_user_id,
          ':solic_upd_por' => $reservm_user_id
        ]);
      }
    }





    // SELECIONA A TABELA A SER CLONADA CONFORME ID
    $sql_select_parq = "SELECT * FROM solicitacao_arq WHERE sarq_solic_id = ?";
    $stmt = $conn->prepare($sql_select_parq);
    $stmt->execute([$solic_id]);
    $rows_parq = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows_parq) > 0) {
      foreach ($rows_parq as $parq) {

        $sql_insert_parq = "INSERT INTO solicitacao_arq (
                                                          sarq_solic_id,
                                                          sarq_codigo,
                                                          sarq_categoria,
                                                          sarq_arquivo,
                                                          sarq_user_id,
                                                          sarq_data_cad
                                                        ) VALUES (
                                                          :sarq_solic_id,
                                                          :sarq_codigo,
                                                          :sarq_categoria,
                                                          :sarq_arquivo,
                                                          :sarq_user_id,
                                                          GETDATE()
                                                        )";

        $stmt_insert = $conn->prepare($sql_insert_parq);
        $stmt_insert->execute([
          ':sarq_solic_id' => $novo_solic_id,
          ':sarq_codigo' => $solic_codigo,
          ':sarq_categoria' => $parq['sarq_categoria'],
          ':sarq_arquivo' => $parq['sarq_arquivo'],
          ':sarq_user_id' => $reservm_user_id
        ]);
      }

      // CLONA A PASTA COM OS ARQUIVOS DA PROPOSTA
      $origem = "../uploads/solicitacoes/" . $parq['sarq_codigo'];
      $destino = "../uploads/solicitacoes/" . $solic_codigo;

      function clonarPasta($origem, $destino)
      {
        // VERIFICA SE A PASTA DE ORIGEM EXISTE
        if (!is_dir($origem)) {
          return false;
        }

        // CRIA A PASTA DE DESTINO SE ELA NÃO EXISTIR
        if (!is_dir($destino)) {
          mkdir($destino, 0777, true);
        }

        $items = scandir($origem);
        foreach ($items as $item) {
          if ($item == "." || $item == "..") {
            continue; // Ignora as pastas . e ..
          }

          $origemItem = $origem . '/' . $item;
          $destinoItem = $destino . '/' . $item;

          if (is_dir($origemItem)) {
            // Se for um diretório, chame a função recursiva
            clonarPasta($origemItem, $destinoItem);
          } else {
            // Se for um arquivo, copie-o para o destino
            copy($origemItem, $destinoItem);
          }
        }
      }
      clonarPasta($origem, $destino);
    }











    /*****************************************************************************************
                                          STATUS DA ANÁLISE
     *****************************************************************************************/
    $num_status  = 1; // CADASTRO PENDENTE
    $sql = "INSERT INTO solicitacao_analise_status (
                                                    sta_an_solic_id,
                                                    sta_an_status,
                                                    sta_an_user_id,
                                                    sta_an_data_cad,
                                                    sta_an_data_upd
                                                    ) VALUES (
                                                    :sta_an_solic_id,
                                                    :sta_an_status,
                                                    :sta_an_user_id,
                                                    GETDATE(),
                                                    GETDATE()
                                                    )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':sta_an_solic_id' => $novo_solic_id,
      ':sta_an_status' => $num_status,
      ':sta_an_user_id' => $reservm_user_id
    ]);
    // -------------------------------



    /*****************************************************************************************
                                  STATUS DA SOLICITAÇÃO
     *****************************************************************************************/
    $num_status  = 1; // CADASTRO PENDENTE
    $sql = "INSERT INTO solicitacao_status (
                                            solic_sta_solic_id,
                                            solic_sta_status,
                                            solic_sta_user_id,
                                            solic_sta_data_cad
                                            ) VALUES (
                                            :solic_sta_solic_id,
                                            :solic_sta_status,
                                            :solic_sta_user_id,
                                            GETDATE()
                                            )";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
      ':solic_sta_solic_id' => $novo_solic_id, // ID DA PROPOSTA
      ':solic_sta_status' => $num_status,
      ':solic_sta_user_id' => $reservm_user_id
    ]);
    // -------------------------------











































    // ENVIA E-MAIL PARA USUÁRIO
    // $mail = new PHPMailer(true);
    // include '../conexao/email.php';
    // $mail->addAddress($email_saap); // E-MAIL DO ADMINISTRADOR
    // // $mail->addAddress($_SESSION['reservm_user_email'], 'RESERVM'); // E-MAIL DO USUÁRIO QUE RECEBERÁ A MENSAGEM
    // $mail->isHTML(true);
    // $mail->Subject = $solic_codigo . ' - Solicitação duplicada'; //TÍTULO DO E-MAIL

    // // CORPO DO EMAIL
    // include '../includes/email/email_header.php';
    // $email_conteudo .= "
    //     <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
    //       <td style='padding: 2em 2rem; display: inline-block; width: 100%'>

    //       <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da solicitação duplicado!</p>

    //       <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>O novo código da solicitação é: <strong> $solic_codigo </strong>. <br> Atualize o cadastro para que nossa equipe inicie a análise da solicitação.</p>

    //       <a style='cursor: pointer;' href='$url_sistema'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
    //       </td>
    //     </tr>";
    // include '../includes/email/email_footer.php';

    // $mail->Body  = $email_conteudo;
    // $mail->send();
    // -------------------------------

    // ENVIA E-MAIL PARA ADMINISTRADOR
    // $mail = new PHPMailer(true);
    // include '../conexao/email.php';
    // $mail->addAddress($email_saap); // E-MAIL DA EXTENSÃO
    // $mail->isHTML(true);
    // $mail->Subject = $solic_codigo . ' - Solicitação duplicada'; //TÍTULO DO E-MAIL

    // // CORPO DO EMAIL
    // include '../includes/email/email_header.php';
    // $email_conteudo .= "
    //     <tr style='background-color: #ffffff; text-align: center; color: #515050;  display: flex; justify-content: center; padding:10px 50px 0 50px; line-height: 23px;'>
    //       <td style='padding: 2em 2rem; display: inline-block; width: 100%'>

    //       <p style='font-size: 1.3rem; font-weight: 600; margin: 0px 0px 40px 0px;'>Cadastro da solicitação duplicado!</p>

    //       <p style='font-size: 1rem; font-weight: 400; margin: 0px 0px 10px 0px;'>Um usuário duplicou o cadastro de uma solicitação.<br>O novo código da solicitação é: <strong> $solic_codigo </strong>.</p>

    //       <a style='cursor: pointer;' href='$url_sistema/admin'><button style='background: #38BE80; display: inline-block; text-decoration: none; border-radius: 4px; color: #fff; border: none; cursor: pointer; padding: 10px 15px; margin-top: 30px;' target='_blank'>Acesse o sistema</button></a>
    //       </td>
    //     </tr>";
    // include '../includes/email/email_footer.php';

    // $mail->Body  = $email_conteudo;
    // $mail->send();
    // -------------------------------

    // REGISTRA AÇÃO NO LOG
    $log_dados = ['POST' => $_POST, 'GET' => $_GET, 'FILES' => $_FILES, 'SOLICITAÇÃO'  => $rows_solic];
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
                            VALUES (:modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute([
      ':modulo'  => 'SOLICITAÇÃO',
      ':acao'    => 'CLONE',
      ':acao_id' => $novo_solic_id,
      ':dados'   => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $reservm_user_id
    ]);
    // -------------------------------

    $conn->commit(); // SE LOG FOR REGISTRADO CORRETAMENTE, REALIZA A AÇÃO

    $_SESSION["msg"] = "A solicitação foi duplicada com sucesso!";
    // header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    header("Location: ../nova_solicitacao.php?st=1&i=" . $novo_solic_id);
    exit();
  } catch (PDOException $e) {
    // echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    $conn->rollBack();
    $_SESSION["erro"] = "Erro ao tentar duplicada a solicitação!";
    // header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    header("Location: ../nova_solicitacao.php?st=1&i=" . $novo_solic_id);
    exit();
  }
} // FIM REQUEST_METHOD