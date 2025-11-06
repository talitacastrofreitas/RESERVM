<?php
// session_start();
include '../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
  try {

    $conn->beginTransaction(); // INICIA A TRANSAÇÃO
    $acao = $_POST['acao']; // AÇÃO: CADASTRAR, ATUALIZAR, DELETAR

    // SE CADASTRAR OU ATUALIZAR
    if ($acao === 'cadastrar' || $acao === 'atualizar') {

      // VALIDA OS CAMPOS OBRIGATÓRIOS
      if (empty($_POST['curs_curso'])) {
        throw new Exception("Preencha os campos obrigatórios!");
      }

      // POST
      $curs_curso   = trim($_POST['curs_curso']);

      // MATRICULA - NOME
      // $curs_matricula_prof = trim($_POST['curs_matricula_prof']) !== '' ? trim($_POST['curs_matricula_prof']) : NULL;
      // $parts = explode(' - ', $curs_matricula_prof, 2);
      // $curs_matricula = trim($parts[0]) !== '' ? trim($parts[0]) : NULL;

      // COORDENADORES: RECEBER UM ARRAY DE MATRICULAS E NOMES
      $selected_coordinators_raw = isset($_POST['curs_matricula_prof']) ? (array) $_POST['curs_matricula_prof'] : [];
      $coordinators_to_save = []; // Array para armazenar apenas as matrículas
      $main_coordinator_matricula = NULL; // Opcional: para manter uma matrícula principal no curso original

      if (!empty($selected_coordinators_raw)) {
        // Pega a matrícula do primeiro coordenador para a coluna principal, se necessário.
        // Ou você pode decidir remover curs_matricula_prof da tabela cursos.
        $first_coordinator_parts = explode(' - ', $selected_coordinators_raw[0], 2);
        $main_coordinator_matricula = trim($first_coordinator_parts[0]) !== '' ? trim($first_coordinator_parts[0]) : NULL;

        foreach ($selected_coordinators_raw as $coordinator_data) {
          $parts = explode(' - ', $coordinator_data, 2);
          $matricula = trim($parts[0]);
          if (!empty($matricula)) {
            $coordinators_to_save[] = $matricula;
          }
        }
      }
      // -------------------------------

      $curs_status  = $_POST['curs_status'] === '1' ? 1 : 0;
    }

    $rvm_admin_id = $_SESSION['reservm_admin_id'];


    // -------------------------------
    // CADASTRO
    // -------------------------------
    // if ($acao === 'cadastrar') {

    //   $log_acao = 'Cadastro';

    //   // IMPEDE CADASTRO DUPLICADO
    //   $sqlVerifica = "SELECT COUNT(*) FROM cursos WHERE curs_curso = :curs_curso";
    //   $stmtVerifica = $conn->prepare($sqlVerifica);
    //   $stmtVerifica->execute([':curs_curso' => $curs_curso]);
    //   $existe = $stmtVerifica->fetchColumn();
    //   if ($existe > 0) {
    //     throw new Exception("Curso já cadastrada!");
    //   }

    //   $sql = "INSERT INTO cursos (
    //                                 curs_curso,
    //                                 curs_matricula_prof,
    //                                 curs_status, 
    //                                 curs_user_id,
    //                                 curs_data_cad,
    //                                 curs_data_upd
    //                               ) VALUES (
    //                                 UPPER(:curs_curso),
    //                                 :curs_matricula_prof,
    //                                 :curs_status,
    //                                 :curs_user_id,
    //                                 GETDATE(),
    //                                 GETDATE()
    //                               )";
    //   $stmt = $conn->prepare($sql);
    //   $stmt->execute([
    //     ':curs_curso'          => $curs_curso,
    //     ':curs_matricula_prof' => $curs_matricula,
    //     ':curs_status'         => $curs_status,
    //     ':curs_user_id'        => $rvm_admin_id
    //   ]);

    //   // ÚLTIMO ID CADASTRADO
    //   if ($stmt->rowCount() > 0) {
    //     $curs_id = $conn->lastInsertId();
    //   } else {
    //     throw new Exception('Erro ao obter o último ID inserido.');
    //   }

    if ($acao === 'cadastrar') {

      $log_acao = 'Cadastro';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM cursos WHERE curs_curso = :curs_curso";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':curs_curso' => $curs_curso]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Curso já cadastrada!");
      }

      $sql = "INSERT INTO cursos (
                                    curs_curso,
                                    curs_matricula_prof, -- Mantido para compatibilidade, mas pode ser removido
                                    curs_status,
                                    curs_user_id,
                                    curs_data_cad,
                                    curs_data_upd
                                  ) VALUES (
                                    UPPER(:curs_curso),
                                    :curs_matricula_prof,
                                    :curs_status,
                                    :curs_user_id,
                                    GETDATE(),
                                    GETDATE()
                                  )";
      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':curs_curso'          => $curs_curso,
        ':curs_matricula_prof' => $main_coordinator_matricula, // Usando a matrícula principal
        ':curs_status'         => $curs_status,
        ':curs_user_id'        => $rvm_admin_id
      ]);

      // ÚLTIMO ID CADASTRADO
      if ($stmt->rowCount() > 0) {
        $curs_id = $conn->lastInsertId();

        // INSERIR COORDENADORES NA NOVA TABELA curso_coordenador
        foreach ($coordinators_to_save as $matricula_coord) {
          $sqlInsertCoord = "INSERT INTO curso_coordenador (curs_id, coordenador_matricula) VALUES (:curs_id, :coordenador_matricula)";
          $stmtInsertCoord = $conn->prepare($sqlInsertCoord);
          $stmtInsertCoord->execute([
            ':curs_id' => $curs_id,
            ':coordenador_matricula' => $matricula_coord
          ]);
        }
      } else {
        throw new Exception('Erro ao obter o último ID inserido.');
      }


      // -------------------------------
      // ATUALIZAR
      // -------------------------------
      // } elseif ($acao === 'atualizar') {

      //   if (empty($_POST['curs_id'])) {
      //     throw new Exception("Erro ao obter o ID!");
      //   }
      //   $curs_id  = (int) $_POST['curs_id'];
      //   $log_acao = 'Atualização';

      //   // IMPEDE CADASTRO DUPLICADO
      //   $sqlVerifica = "SELECT COUNT(*) FROM cursos WHERE curs_curso = :curs_curso AND curs_id != :curs_id";
      //   $stmtVerifica = $conn->prepare($sqlVerifica);
      //   $stmtVerifica->execute([':curs_curso' => $curs_curso, ':curs_id' => $curs_id]);
      //   $existe = $stmtVerifica->fetchColumn();
      //   if ($existe > 0) {
      //     throw new Exception("Curso já cadastrada!");
      //   }

      //   $sql = "UPDATE cursos SET 
      //                             curs_curso          = UPPER(:curs_curso),
      //                             curs_matricula_prof = :curs_matricula_prof,
      //                             curs_status         = :curs_status,
      //                             curs_user_id        = :curs_user_id,
      //                             curs_data_upd       = GETDATE()
      //                       WHERE
      //                             curs_id = :curs_id";

      //   $stmt = $conn->prepare($sql);
      //   $stmt->execute([
      //     ':curs_id'             => $curs_id,
      //     ':curs_curso'          => $curs_curso,
      //     ':curs_matricula_prof' => $curs_matricula,
      //     ':curs_status'         => $curs_status,
      //     ':curs_user_id'        => $rvm_admin_id
      //   ]);

    } elseif ($acao === 'atualizar') {

      if (empty($_POST['curs_id'])) {
        throw new Exception("Erro ao obter o ID!");
      }
      $curs_id  = (int) $_POST['curs_id'];
      $log_acao = 'Atualização';

      // IMPEDE CADASTRO DUPLICADO
      $sqlVerifica = "SELECT COUNT(*) FROM cursos WHERE curs_curso = :curs_curso AND curs_id != :curs_id";
      $stmtVerifica = $conn->prepare($sqlVerifica);
      $stmtVerifica->execute([':curs_curso' => $curs_curso, ':curs_id' => $curs_id]);
      $existe = $stmtVerifica->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Curso já cadastrada!");
      }

      $sql = "UPDATE cursos SET
                                curs_curso          = UPPER(:curs_curso),
                                curs_matricula_prof = :curs_matricula_prof, -- Mantido para compatibilidade
                                curs_status         = :curs_status,
                                curs_user_id        = :curs_user_id,
                                curs_data_upd       = GETDATE()
                          WHERE
                                curs_id = :curs_id";

      $stmt = $conn->prepare($sql);
      $stmt->execute([
        ':curs_id'             => $curs_id,
        ':curs_curso'          => $curs_curso,
        ':curs_matricula_prof' => $main_coordinator_matricula, // Usando a matrícula principal
        ':curs_status'         => $curs_status,
        ':curs_user_id'        => $rvm_admin_id
      ]);

      // EXCLUIR COORDENADORES ATUAIS NA TABELA curso_coordenador
      $sqlDeleteCoords = "DELETE FROM curso_coordenador WHERE curs_id = :curs_id";
      $stmtDeleteCoords = $conn->prepare($sqlDeleteCoords);
      $stmtDeleteCoords->execute([':curs_id' => $curs_id]);

      // INSERIR NOVOS COORDENADORES NA TABELA curso_coordenador
      foreach ($coordinators_to_save as $matricula_coord) {
        $sqlInsertCoord = "INSERT INTO curso_coordenador (curs_id, coordenador_matricula) VALUES (:curs_id, :coordenador_matricula)";
        $stmtInsertCoord = $conn->prepare($sqlInsertCoord);
        $stmtInsertCoord->execute([
          ':curs_id' => $curs_id,
          ':coordenador_matricula' => $matricula_coord
        ]);
      }




      // -------------------------------
      // EXCLUIR
      // -------------------------------
    } elseif ($_GET['acao'] === 'deletar') {

      if (empty($_GET['curs_id'])) {
        throw new Exception("ID é obrigatório para exclusão.");
      }
      $curs_id  = (int) $_GET['curs_id'];
      $log_acao = 'Exclusão';

      // SE DADO ESTIVER SENDO USADO EM ALGUM CADASTRO, NÃO PODE SER EXCLUÍDO
      $sql = $conn->prepare("SELECT COUNT(*) FROM solicitacao WHERE CHARINDEX(:solic_curso, solic_curso) > 0");
      $sql->execute([':solic_curso' => $curs_id]);
      $existe = $sql->fetchColumn();
      if ($existe > 0) {
        throw new Exception("Este registro não pode ser excluído!");
      }
      // -------------------------------

      $sql = "DELETE FROM cursos WHERE curs_id = :curs_id";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':curs_id' => $curs_id]);




      // -------------------------------
      // AÇÃO INVÁLIDA
      // -------------------------------
    } else {
      throw new Exception("Ação inválida.");
    }

    // REGISTRA NO LOG
    $log_dados = ['POST' => $_POST, 'GET' => $_GET, 'FILES' => $_FILES];
    $sqlLog = "INSERT INTO log (log_modulo, log_acao, log_acao_id, log_dados, log_acao_user_id, log_data)
              VALUES (:modulo, upper(:acao), :acao_id, :dados, :user_id, GETDATE())";
    $stmtLog = $conn->prepare($sqlLog);
    $stmtLog->execute([
      ':modulo'  => 'CURSOS',
      ':acao'    => $log_acao,
      ':acao_id' => $curs_id,
      ':dados'   => json_encode($log_dados, JSON_UNESCAPED_UNICODE),
      ':user_id' => $rvm_admin_id
    ]);
    // -------------------------------

    $conn->commit(); // CONFIRMA A TRANSAÇÃO

    // CONFIGURAÇÃO DE MENSAGEM
    if ($acao === 'cadastrar') {
      $_SESSION["msg"] = "Dados cadastrados com sucesso!";
    } elseif ($acao === 'atualizar') {
      $_SESSION["msg"] = "Dados atualizados com sucesso!";
    } else {
      $_SESSION["msg"] = "Dados excluídos com sucesso!";
    }
    // -------------------------------
    header("Location: ../admin/cursos.php");
    exit;
    // -------------------------------

  } catch (Exception $e) {
    $conn->rollBack();
    $_SESSION["erro"] = $e->getMessage();
    $_SESSION["form_curs"] = $_POST; // CRIA SESSÃO PARA OS DADOS DO FORMULÁRIO QUE FORAM ENVIADOS
    header("Location: ../admin/cursos.php");
    exit;
  }
} else {
  $_SESSION["erro"] = "Requisição inválida.";
  header("Location: ../admin/cursos.php");
  exit;
}
