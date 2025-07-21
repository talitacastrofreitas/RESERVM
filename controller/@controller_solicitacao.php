<?php
session_start();
include '../conexao/conexao.php';

/*****************************************************************************************
                                    CADASTRAR SOLICITAÇÃO
 *****************************************************************************************/
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  try {

    // Iniciar transação
    $conn->beginTransaction();

    $solic_acao = isset($_POST['solic_acao']) ? trim($_POST['solic_acao']) : null;

    // SE INSERIR OU ATUALIZAR
    if ($solic_acao === 'cadastrar_1' || $solic_acao === 'atualizar_1' || $solic_acao === 'atualizar_2' || $solic_acao === 'atualizar_3') {

      $solic_id              = bin2hex(random_bytes(16)); // GERA UM ID ÚNICO SEGURO
      $solic_codigo          = isset($_POST['solic_codigo']) ? trim($_POST['solic_codigo']) : null;
      $solic_etapa           = isset($_POST['solic_etapa']) ? trim($_POST['solic_etapa']) : null;


      ///////////////////
      // IDENTIFICAÇÃO //
      ///////////////////

      $solic_tipo_ativ       = trim($_POST['solic_tipo_ativ']);

      if ($solic_tipo_ativ == 2) {
        $solic_nome_atividade  = isset($_POST['solic_nome_atividade']) ? trim($_POST['solic_nome_atividade']) : null;
        $solic_curso           = null;
        $solic_comp_curric     = null;
        $solic_nome_curso      = null;
        $solic_semestre        = null;
        $solic_nome_curso_text = null;
        $solic_nome_comp_ativ  = null;
      } else {

        $solic_curso           = trim($_POST['solic_curso']);

        // 2	BIOMEDICINA
        // 5	EDUCAÇÃO FÍSICA
        // 6	ENFERMAGEM
        // 9	FISIOTERAPIA
        // 13	LIGA ACADÊMICA
        // 17	NÚCLEO COMUM
        // 18	ODONTOLOGIA
        // 21	PSICOLOGIA
        if (in_array($solic_curso, [2, 5, 6, 9, 13, 17, 18, 21])) {
          $solic_comp_curric = isset($_POST['solic_comp_curric']) ? trim($_POST['solic_comp_curric']) : null;
        }

        // 7	EXTENSÃO
        // 10	GRUPO DE PESQUISA
        // 19	PROGRAMA CANDEAL
        // 28	NIDD
        if (in_array($solic_curso, [7, 10, 19, 28])) {
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
      }

      // NOME PROFESSOR E CONTATO
      $solic_nome_prof_resp  = isset($_POST['solic_nome_prof_resp']) ? trim($_POST['solic_nome_prof_resp']) : null;
      $solic_contato         = isset($_POST['solic_contato']) ? trim($_POST['solic_contato']) : null;




      ////////////////////
      // AULAS PRÁTICAS //
      ////////////////////

      $solic_ap_aula_pratica   = trim($_POST['solic_ap_aula_pratica']);

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

        // PROCESSA OS CHECKBOXES COMO STRING
        if ($solic_ap_campus == 2) {
          $solic_ap_espaco = isset($_POST['solic_ap_espaco_brotas']) && is_array($_POST['solic_ap_espaco_brotas'])
            ? implode(', ', array_map('htmlspecialchars', $_POST['solic_ap_espaco_brotas'])) : null;
          //
        } else {
          // PROCESSA OS CHECKBOXES COMO STRING
          $solic_ap_espaco = isset($_POST['solic_ap_espaco_cabula']) && is_array($_POST['solic_ap_espaco_cabula'])
            ? implode(', ', array_map('htmlspecialchars', $_POST['solic_ap_espaco_cabula'])) : null;
          //
        }

        $solic_ap_quant_turma    = isset($_POST['solic_ap_quant_turma']) ? trim($_POST['solic_ap_quant_turma']) : null;
        $solic_ap_quant_particip = isset($_POST['solic_ap_quant_particip']) ? trim($_POST['solic_ap_quant_particip']) : null;
        $solic_ap_tipo_reserva   = isset($_POST['solic_ap_tipo_reserva']) ? trim($_POST['solic_ap_tipo_reserva']) : null;

        if ($solic_ap_tipo_reserva == 1) {
          $solic_ap_data_reserva  = trim($_POST['solic_ap_data_reserva']) !== '' ? nl2br(trim($_POST['solic_ap_data_reserva'])) : NULL;
        } else {
          // PROCESSA OS CHECKBOXES COMO STRING
          if (isset($_POST['solic_ap_dia_reserva'])) {
            $solic_ap_dia_reserva = isset($_POST['solic_ap_dia_reserva']) && is_array($_POST['solic_ap_dia_reserva'])
              ? implode(', ', array_map('htmlspecialchars', $_POST['solic_ap_dia_reserva']))
              : null;
            //
          }
        }

        $solic_ap_hora_inicio    = isset($_POST['solic_ap_hora_inicio']) ? trim($_POST['solic_ap_hora_inicio']) : null;
        $solic_ap_hora_fim       = isset($_POST['solic_ap_hora_fim']) ? trim($_POST['solic_ap_hora_fim']) : null;
        $solic_ap_tipo_material  = isset($_POST['solic_ap_tipo_material']) ? trim($_POST['solic_ap_tipo_material']) : null;

        if ($solic_ap_tipo_material == 1) {
        } elseif ($solic_ap_tipo_material == 2) {
          $solic_ap_tit_aulas = trim($_POST['solic_ap_tit_aulas']) !== '' ? nl2br(trim($_POST['solic_ap_tit_aulas'])) : NULL;
        } elseif ($solic_ap_tipo_material == 3) {
          $solic_ap_quant_material = trim($_POST['solic_ap_quant_material']) !== '' ? nl2br(trim($_POST['solic_ap_quant_material'])) : NULL;
        }

        $solic_ap_obs            = trim($_POST['solic_ap_obs']) !== '' ? nl2br(trim($_POST['solic_ap_obs'])) : NULL;
      }


      ////////////////////
      // AULAS TEÓRICAS //
      ////////////////////

      $solic_at_aula_teorica   = trim($_POST['solic_at_aula_teorica']);

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

        $solic_at_campus         = isset($_POST['solic_at_campus']) ? trim($_POST['solic_at_campus']) : null;
        $solic_at_quant_sala     = isset($_POST['solic_at_quant_sala']) ? trim($_POST['solic_at_quant_sala']) : null;
        $solic_at_quant_particip = isset($_POST['solic_at_quant_particip']) ? trim($_POST['solic_at_quant_particip']) : null;
        $solic_at_tipo_reserva   = isset($_POST['solic_at_tipo_reserva']) ? trim($_POST['solic_at_tipo_reserva']) : null;

        if ($solic_at_tipo_reserva == 1) {
          $solic_at_data_reserva  = trim($_POST['solic_at_data_reserva']) !== '' ? nl2br(trim($_POST['solic_at_data_reserva'])) : NULL;
        } else {
          // PROCESSA OS CHECKBOXES COMO STRING
          if (isset($_POST['solic_at_dia_reserva'])) {
            $solic_at_dia_reserva = isset($_POST['solic_at_dia_reserva']) && is_array($_POST['solic_at_dia_reserva'])
              ? implode(', ', array_map('htmlspecialchars', $_POST['solic_at_dia_reserva']))
              : null;
            //
          }
        }

        $solic_at_hora_inicio = isset($_POST['solic_at_hora_inicio']) ? trim($_POST['solic_at_hora_inicio']) : null;
        $solic_at_hora_fim    = isset($_POST['solic_at_hora_fim']) ? trim($_POST['solic_at_hora_fim']) : null;
        $solic_at_recursos    = trim($_POST['solic_at_recursos']) !== '' ? nl2br(trim($_POST['solic_at_recursos'])) : NULL;
        $solic_at_obs         = trim($_POST['solic_at_obs']) !== '' ? nl2br(trim($_POST['solic_at_obs'])) : NULL;
      }
      $solic_user_id           = $_SESSION['reservm_user_id'];
    }

    // -------------------------------
    // CADASTRAR ETAPA 1
    // -------------------------------
    if ($solic_acao === 'cadastrar_1') {

      $num_status  = 1; // CADASTRO EM ELABORAÇÃO
      $log_acao = 'Cadastro';

      $sql = "INSERT INTO solicitacao (
                                        solic_id,
                                        solic_codigo,
                                        solic_etapa,
                                        --solic_tipo_ativ,
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
                                        solic_data_cad
                                      ) VALUES (
                                        :solic_id,
                                        :solic_codigo,
                                        :solic_etapa,
                                        --:solic_tipo_ativ,
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
                                        GETDATE()
                                      )";

      $stmt = $conn->prepare($sql);

      // Bind dos parâmetros para evitar SQL Injection
      $stmt->bindParam(':solic_id', $solic_id, PDO::PARAM_STR);
      $stmt->bindParam(':solic_codigo', $solic_codigo, PDO::PARAM_STR);
      $stmt->bindParam(':solic_etapa', $solic_etapa, PDO::PARAM_INT);
      //$stmt->bindParam(':solic_tipo_ativ', $solic_tipo_ativ, PDO::PARAM_INT);
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


      // CADASTRA O STATUS DA SOLICITAÇÃO
      $sql = "INSERT INTO solicitacao_status (solic_sta_solic_id,solic_sta_status, solic_sta_user_id, solic_sta_data_cad) VALUES (:solic_sta_solic_id, :solic_sta_status, :solic_sta_user_id, GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':solic_sta_solic_id' => $solic_id, ':solic_sta_status'  => $num_status, ':solic_sta_user_id' => $solic_user_id]);

      // CADASTRA O STATUS DA ANÁLISE
      $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_user_id, GETDATE(), GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':sta_an_solic_id' => $solic_id, ':sta_an_status'  => $num_status, ':sta_an_user_id' => $solic_user_id]);


      // -------------------------------
      // ATUALIZAÇÃO ETAPA 1
      // -------------------------------
    } elseif ($solic_acao === 'atualizar_1') {

      if (empty($_POST['solic_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $solic_id = $_POST['solic_id'];
      $log_acao = 'Atualização';

      // VERIFICA O ADAMENTO DAS ETAPAS DO CADASTRO
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
                                      --solic_tipo_ativ       = :solic_tipo_ativ,
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
      // $stmt->bindParam(':solic_tipo_ativ', $solic_tipo_ativ, PDO::PARAM_INT);
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


      // -------------------------------
      // ATUALIZAÇÃO ETAPA 2
      // -------------------------------
    } elseif ($solic_acao === 'atualizar_2') {

      if (empty($_POST['solic_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $solic_id = $_POST['solic_id'];
      $log_acao = 'Atualização';

      // VERIFICA O ADAMENTO DAS ETAPAS DO CADASTRO
      $sqlVerifica = "SELECT solic_id, solic_etapa FROM solicitacao WHERE solic_id = :solic_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':solic_id' => $solic_id]);
      $result = $stmtVerifica->fetch(PDO::FETCH_ASSOC); // Pega apenas uma linha
      if ($result) { // Verifica se a consulta retornou resultado
        if ($result['solic_etapa'] == 3) {
          $solic_etapa = 3;
        } elseif ($result['solic_etapa'] == 1) {
          $solic_etapa = 2;
        }
      }

      $sql = "UPDATE solicitacao SET 
                                      solic_etapa             = :solic_etapa,
                                      solic_ap_aula_pratica   = :solic_ap_aula_pratica,
                                      solic_ap_campus         = :solic_ap_campus,
                                      solic_ap_espaco         = :solic_ap_espaco,
                                      solic_ap_quant_turma    = :solic_ap_quant_turma,
                                      solic_ap_quant_particip = :solic_ap_quant_particip,
                                      solic_ap_tipo_reserva   = :solic_ap_tipo_reserva,
                                      solic_ap_data_reserva   = :solic_ap_data_reserva,
                                      solic_ap_dia_reserva    = :solic_ap_dia_reserva,
                                      solic_ap_hora_inicio    = :solic_ap_hora_inicio,
                                      solic_ap_hora_fim       = :solic_ap_hora_fim,
                                      solic_ap_tipo_material  = :solic_ap_tipo_material,
                                      solic_ap_tit_aulas      = :solic_ap_tit_aulas,
                                      solic_ap_quant_material = :solic_ap_quant_material,
                                      solic_ap_obs            = :solic_ap_obs,
                                      solic_upd_por           = :solic_upd_por,
                                      solic_data_upd          = GETDATE()
                                WHERE 
                                      solic_id = :solic_id";
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


      ////

      $stmt = $conn->prepare("SELECT * FROM solicitacao WHERE solic_id = :solic_id");
      $stmt->bindParam(':solic_id', $solic_id, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($result) {
        $solic_codigo = $result['solic_codigo'];
      }

      if ($solic_ap_aula_pratica == 0) {

        // EXCLUIR OS ARQUIVOS DA SOLICITAÇÃO
        $sql = "DELETE FROM solicitacao_arq WHERE sarq_solic_id = :sarq_solic_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':sarq_solic_id' => $solic_id]);
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

      }

      // CADASTRA AS IMAGENS
      if (!empty($_FILES["arquivos"]["name"][0])) { // SE ALGUAMA IMAGEM FOR ENVIADA...

        $arquivos  = $_FILES["arquivos"]; // RECEBE O(S) ARQUIVO(S)

        // 1. Verifica quantos arquivos já estão cadastrados para a solicitacao
        $quantidadeNovosArquivos = count(array_filter($arquivos['name']));
        $sql = "SELECT COUNT(*) FROM solicitacao_arq WHERE sarq_solic_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$solic_id]);
        $quantidadeExistente = (int)$stmt->fetchColumn();

        // 2. Verifica se a soma ultrapassa o limite
        $total = $quantidadeExistente + $quantidadeNovosArquivos;
        if ($total > 10) {
          $_SESSION["erro"] = "O limite é de 10 arquivos por solicitacao. Atualmente há $quantidadeExistente arquivos e você tentou enviar $quantidadeNovosArquivos!";
          $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'nova_solicitacao.php';
          header("Location: $referer#file_ancora");
          exit;
        }


        $maxFileSize = 1 * 1024 * 1024 * 1024; // 1GB - TAMANHO DE ARQUIVOS PERMITIDO        
        $maxFiles = 10; // QUANTIDADE DE ARQUIVOS PERMITIDO

        //FORMATO DE ARQUIVOS PERMITIDOS
        $allowedTypes = [
          // Documentos
          "application/msword",
          "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
          "application/vnd.ms-excel",
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          "application/vnd.ms-powerpoint",
          "application/vnd.openxmlformats-officedocument.presentationml.presentation",
          "application/pdf",
          // Imagens
          "image/jpeg",
          "image/png",
          "image/gif",
          "image/bmp",
          // Vídeos
          "video/mp4",
          "video/x-msvideo",
          "video/quicktime",
          "video/x-matroska",
          // Áudios
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

            // Configuração do PHP
            // No php.ini, verifique e ajuste as diretivas:
            // --
            // upload_max_filesize = 1G
            // post_max_size = 1G
            // max_execution_time = 300
            // max_input_time = 300
            // memory_limit = 1G
          }

          if (!in_array($fileType, $allowedTypes)) {
            $_SESSION["erro"] = "O tipo do arquivo $fileName não é permitido!";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'nova_solicitacao.php';
            header("Location: $referer#file_ancora");
            exit;
          }
        }

        // CRIA AS PASTAS DOS ARQUIVOS
        $pastaPrincipal = "../uploads/solicitacoes/$solic_codigo";
        // CRIA A PASTA PRINCIPAL SE NÃO EXISTIR
        if (!file_exists($pastaPrincipal)) {
          mkdir($pastaPrincipal, 0777, true);
        }
        // -------------------------------

        $nomes = $arquivos['name']; //CAPTURA NOMES DOS ARQUIVOS

        for ($i = 0; $i < count($nomes); $i++) {
          $extensao = explode('.', $nomes[$i]);
          $extensao = end($extensao);
          $nomes[$i] = rand() . '-' . $nomes[$i];

          $sql = "INSERT INTO solicitacao_arq (
                                                sarq_solic_id,
                                                sarq_codigo,
                                                sarq_arquivo,
                                                sarq_user_id,
                                                sarq_data_cad
                                                ) VALUES (
                                                :sarq_solic_id,
                                                :sarq_codigo,
                                                :sarq_arquivo,
                                                :sarq_user_id,
                                                GETDATE()
                                                )";
          $stmt = $conn->prepare($sql);
          $stmt->execute([
            ':sarq_solic_id' => $solic_id,
            ':sarq_codigo' => $solic_codigo,
            ':sarq_arquivo' => $nomes[$i],
            ':sarq_user_id' => $solic_user_id
          ]);

          // REGISTRA AÇÃO NO LOG
          // $last_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
          // $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
          //                           VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
          // $stmt->execute([
          //   ':modulo'     => 'PROPOSTA - CURSOS - ARQUIVOS',
          //   ':acao'       => 'CADASTRO',
          //   ':acao_id'    => $prop_id,
          //   ':dados'      => 'ID: ' . $last_id . '; Código: ' . $prop_codigo . '; Categoria: ' . $parq_categoria . '; Arquivo: ' . $nomes[$i],
          //   ':user_id'    => $reservm_user_id
          // ]);
          // -------------------------------

          // MOVE AS IMAGENS PARA A PASTA
          $total_rowns = $stmt->rowCount();
          if ($total_rowns > 0) {
            $mover = move_uploaded_file($_FILES['arquivos']['tmp_name'][$i], '../uploads/solicitacoes/' . $solic_codigo . '/' . $nomes[$i]);
          }
          // -------------------------------

        }
      }





















      // -------------------------------
      // ATUALIZAÇÃO ETAPA 3
      // -------------------------------
    } elseif ($solic_acao === 'atualizar_3') {

      if (empty($_POST['solic_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $solic_id = $_POST['solic_id'];
      $log_acao = 'Atualização';

      // VERIFICA O ADAMENTO DAS ETAPAS DO CADASTRO
      $sqlVerifica = "SELECT solic_id, solic_etapa FROM solicitacao WHERE solic_id = :solic_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':solic_id' => $solic_id]);
      $result = $stmtVerifica->fetch(PDO::FETCH_ASSOC); // Pega apenas uma linha
      if ($result) { // Verifica se a consulta retornou resultado
        if ($result['solic_etapa'] == 3) {
          $solic_etapa = 3;
        } elseif ($result['solic_etapa'] == 2) {
          $solic_etapa = 3;
        }
      }

      $sql = "UPDATE solicitacao SET 
                                      solic_etapa             = :solic_etapa,
                                      solic_at_aula_teorica   = :solic_at_aula_teorica,
                                      solic_at_campus         = :solic_at_campus,
                                      solic_at_quant_sala     = :solic_at_quant_sala,
                                      solic_at_quant_particip = :solic_at_quant_particip,
                                      solic_at_tipo_reserva   = :solic_at_tipo_reserva,
                                      solic_at_data_reserva   = :solic_at_data_reserva,
                                      solic_at_dia_reserva    = :solic_at_dia_reserva,
                                      solic_at_hora_inicio    = :solic_at_hora_inicio,
                                      solic_at_hora_fim       = :solic_at_hora_fim,
                                      solic_at_recursos       = :solic_at_recursos,
                                      solic_at_obs            = :solic_at_obs,
                                      solic_upd_por           = :solic_upd_por,
                                      solic_data_upd          = GETDATE()
                                WHERE 
                                      solic_id = :solic_id";
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



      // STATUS DA ANÁLISE
      $num_status  = 2; // 2 = CONCLUÍDO
      $sql = "INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_user_id, sta_an_data_cad, sta_an_data_upd) VALUES (:sta_an_solic_id, :sta_an_status, :sta_an_user_id, GETDATE(), GETDATE())";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':sta_an_solic_id' => $solic_id, ':sta_an_status' => $num_status, ':sta_an_user_id' => $solic_user_id]);
      // -------------------------------

      // ALTERA O STATUS DA SOLICITAÇÃO
      $sql = "UPDATE solicitacao_status SET solic_sta_status = :solic_sta_status, solic_sta_user_id = :solic_sta_user_id, solic_sta_data_cad = GETDATE() WHERE solic_sta_solic_id = :solic_sta_solic_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':solic_sta_solic_id' => $solic_id, ':solic_sta_status' => $num_status, ':solic_sta_user_id' => $solic_user_id]);
      // -------------------------------


    } else {
      throw new Exception("Ação inválida!");
    }

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :dados, :user_id, GETDATE())');
    $stmt->execute(array(
      ':modulo'  => 'SOLICITAÇÃO',
      ':acao'    => $log_acao,
      ':acao_id' => $solic_id,
      ':dados'   => json_encode($_POST),
      ':user_id' => $solic_user_id
    ));
    // -------------------------------



    // $usuario_id = $conn->lastInsertId(); // ÚLTIMO ID CADASTRADO
    // $log_acao = 'Cadastro';

    // Confirmar a transação
    $conn->commit();

    // CONFIGURAÇÃO DE MENSAGEM
    if ($solic_acao === 'cadastrar_1' || $solic_acao === 'atualizar_1') {
      header(sprintf("location: ../nova_solicitacao.php?st=2&i={$solic_id}"));
    } elseif ($solic_acao === 'atualizar_2') {
      header(sprintf("location: ../nova_solicitacao.php?st=3&i={$solic_id}"));
    } elseif ($solic_acao === 'atualizar_3') {
      $_SESSION["msg"] = "Solicitação realizado com sucesso!" . $arquivos;
      header(sprintf("location: ../painel.php"));
    }

    // Redirecionar de volta para o formulário
    //$_SESSION["msg"] = "Cadastro realizado com sucesso!";
    //header(sprintf("location: ../nova_solicitacao.php?st=2&i={$solic_id}"));
    exit();
    // -------------------------------

  } catch (PDOException $e) {

    // Reverter a transação em caso de erro
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
    exit();
    // -------------------------------

  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  exit();
}





/*****************************************************************************************
                                    EDITAR DADOS
 *****************************************************************************************/
if (isset($dados['EditarCursoCoordenador'])) {

  $cc_id          = trim($_POST['cc_id']);
  $cc_curso       = trim($_POST['cc_curso']);
  $cc_coordenador = trim($_POST['cc_coordenador']);
  $cc_email       = trim($_POST['cc_email']);
  $cc_status      = isset($_POST['cc_status']) ? $_POST['cc_status'] : 2;
  $data_real      = date('Y-m-d H:i:s');
  // -------------------------------

  // IMPEDE CADASTRO DUPLICADO
  $sql = "SELECT COUNT(*) FROM conf_cursos_coordenadores WHERE cc_curso = :cc_curso AND cc_id != :cc_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":cc_id", $cc_id);
  $stmt->bindParam(":cc_curso", $cc_curso);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este curso já foi cadastrado!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "UPDATE
                    conf_cursos_coordenadores
              SET
                    cc_curso       = UPPER(:cc_curso),
                    cc_coordenador = UPPER(:cc_coordenador),
                    cc_email       = :cc_email,
                    cc_status      = :cc_status,
                    cc_user_id     = :cc_user_id,
                    cc_data_upd    = :cc_data_upd
              WHERE
                    cc_id = :cc_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":cc_id", $cc_id);
    //
    $stmt->bindParam(":cc_curso", $cc_curso);
    $stmt->bindParam(":cc_coordenador", $cc_coordenador);
    $stmt->bindParam(":cc_email", $cc_email);
    $stmt->bindParam(":cc_status", $cc_status);
    //
    $stmt->bindParam(":cc_user_id", $_SESSION['reservm_admin_id']);
    $stmt->bindParam(":cc_data_upd", $data_real);
    $stmt->execute();

    // REGISTRA AÇÃO NO LOG
    $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, UPPER(:dados), :user_id, UPPER(:user_nome), :data )');
    $stmt->execute(array(
      ':modulo'     => 'CURSO COORDENADOR',
      ':acao'       => 'ATUALIZAÇÃO',
      ':acao_id'    => $cc_id,
      ':dados'      => 'Curso: ' . $cc_curso . '; Coordenador: ' . $cc_coordenador . '; E-mail: ' . $cc_email . '; Status: ' . $cc_status,
      ':user_id'    => $_SESSION['reservm_admin_id'],
      ':user_nome'  => $_SESSION['reservm_admin_nome'],
      ':data'       => $data_real
    ));
    // -------------------------------

    $_SESSION["msg"] = "Dados atualizados com sucesso!";
    header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));
  } catch (PDOException $e) {
    //echo 'Error: ' . $e->getMessage();
    $_SESSION["erro"] = "Atualização não realizada!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
}








/*****************************************************************************************
                                    EXCLUIR DADOS
 *****************************************************************************************/
if (isset($_GET['funcao']) && $_GET['funcao'] == "exc_curso") {

  $cc_id     = $_GET['cc_id'];
  $data_real = date('Y-m-d H:i:s');

  // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXLUÍDO
  $sql = "SELECT COUNT(*) FROM propostas WHERE prop_curso_vinculo = :prop_curso_vinculo";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":prop_curso_vinculo", $cc_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_area_atuacao = :pcp_area_atuacao";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pcp_area_atuacao", $cc_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_equipe_executora WHERE pex_area_atuacao = :pex_area_atuacao";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":pex_area_atuacao", $cc_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  $sql = "SELECT COUNT(*) FROM propostas_parceiro_externo WHERE ppe_area_atuacao = :ppe_area_atuacao";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":ppe_area_atuacao", $cc_id);
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
    $_SESSION["erro"] = "Este registro não pode ser excluído!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  // -------------------------------

  try {
    $sql = "DELETE FROM conf_cursos_coordenadores WHERE cc_id = :cc_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cc_id', $cc_id);
    $stmt->execute();

    // VERIFICA SE A CONSULTA FOI BEM SUCEDIDA
    if ($stmt->rowCount() > 0) {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
      header(sprintf('location: %s', $_SERVER['HTTP_REFERER']));

      // REGISTRA AÇÃO NO LOG
      $stmt = $conn->prepare('INSERT INTO log (log_modulo, log_acao, log_acao_id, log_acao_user_id, log_acao_user_nome, log_data )
                            VALUES ( :modulo, :acao, :acao_id, :user_id, :user_nome, :data )');
      $stmt->execute(array(
        ':modulo'    => 'CURSO COORDENADOR',
        ':acao'      => 'EXCLUSÃO',
        ':acao_id'   => $cc_id,
        ':user_id'   => $_SESSION['reservm_admin_id'],
        ':user_nome' => $_SESSION['reservm_admin_nome'],
        ':data'      => $data_real
      ));
      // -------------------------------

    } else {
      $_SESSION["erro"] = "Dados não excluídos!";
      echo "<script> history.go(-1);</script>";
      return die;
    }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
    $_SESSION["erro"] = "Dados não excluídos!";
    echo "<script> history.go(-1);</script>";
    return die;
  }
  $conn = null;
}
