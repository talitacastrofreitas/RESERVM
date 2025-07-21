<?php
session_start();


include '../../conexao/conexao.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

define('PAINEL_TV_API_BASE_URL', $_ENV['PAINEL_TV_API_FULL_URL']);

define('PAINEL_TV_API_KEY', $_ENV['PAINEL_TV_API_KEY']);

$url_sistema = $_ENV['RESERVM_BASE_URL'];

// Diretorio de upload local no Reservm
$uploadDirLocalReservm = '../includes/files/banners/';
error_log("Diretório de Upload LOCAL (Reservm) definido como: " . $uploadDirLocalReservm);

if (!is_dir($uploadDirLocalReservm)) {
    error_log("Diretório de upload LOCAL NÃO existe. Tentando criar: " . $uploadDirLocalReservm);
    if (!mkdir($uploadDirLocalReservm, 0777, true)) {
        $_SESSION['erro'] = 'Erro crítico: Não foi possível criar o diretório de upload LOCAL: ' . $uploadDirLocalReservm . ' - Verifique as permissões da pasta pai (admin/includes/files).';
        error_log($_SESSION['erro']);
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
        exit;
    }
    error_log("Diretório de upload LOCAL criado com sucesso: " . $uploadDirLocalReservm);
} else {
    error_log("Diretório de upload LOCAL já existe: " . $uploadDirLocalReservm);
}

if (!is_writable($uploadDirLocalReservm)) {
    $_SESSION['erro'] = 'Erro crítico: O diretório de upload LOCAL NÃO tem permissão de escrita: ' . $uploadDirLocalReservm . ' - Ajuste as permissões.';
    error_log($_SESSION['erro']);
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
    exit;
}
error_log("Diretório de upload LOCAL TEM permissão de escrita: " . $uploadDirLocalReservm);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    error_log("Ação recebida: " . $action);

    switch ($action) {
        case 'add_publicidade':
            error_log("Iniciando 'add_publicidade' case (Reservm).");
            if (isset($_POST['tituloPublicidade']) && isset($_FILES['uploadArquivo'])) {
                $titulo = trim($_POST['tituloPublicidade'] ?? '');
                $file = $_FILES['uploadArquivo'];

                // --- VALIDAÇÕES DO ARQUIVO (MANTIDAS NO RESERVM) ---
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $uploadErrors = [UPLOAD_ERR_INI_SIZE => 'O arquivo excede o tamanho máximo permitido no php.ini.', UPLOAD_ERR_FORM_SIZE => 'O arquivo excede o tamanho máximo especificado no formulário HTML.', UPLOAD_ERR_PARTIAL => 'O upload do arquivo foi feito apenas parcialmente.', UPLOAD_ERR_NO_FILE => 'Nenhum arquivo foi enviado.', UPLOAD_ERR_NO_TMP_DIR => 'Faltando uma pasta temporária para o upload.', UPLOAD_ERR_CANT_WRITE => 'Falha ao gravar o arquivo em disco.', UPLOAD_ERR_EXTENSION => 'Uma extensão do PHP interrompeu o upload do arquivo.'];
                    $errorMessage = $uploadErrors[$file['error']] ?? 'Erro de upload desconhecido (' . $file['error'] . ').';
                    $_SESSION['erro'] = 'Erro no upload do arquivo: ' . $errorMessage;
                    error_log("Erro de upload PHP ('file[error]'): " . $errorMessage);
                    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
                    exit;
                }

                $maxFileSize = 10 * 1024 * 1024; // 10 MB
                if ($file['size'] > $maxFileSize) {
                    $_SESSION['erro'] = 'O arquivo é muito grande. Tamanho máximo permitido é 10MB.';
                    error_log("Erro: Arquivo muito grande. Tamanho: " . $file['size'] . " bytes.");
                    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
                    exit;
                }

                $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $allowedVideoTypes = ['video/mp4', 'video/webm'];
                $allowedTypes = array_merge($allowedImageTypes, $allowedVideoTypes);

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                error_log("MIME Type detectado: " . $mimeType);
                if (!in_array($mimeType, $allowedTypes)) {
                    $_SESSION['erro'] = 'Tipo de arquivo não permitido. Apenas imagens (JPG, PNG, GIF) e Vídeos (MP4, WEBM) são aceitos. MIME detectado: ' . $mimeType;
                    error_log($_SESSION['erro']);
                    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
                    exit;
                }

                $mediaType = 'image';
                if (in_array($mimeType, $allowedVideoTypes)) {
                    $mediaType = 'video';
                }
                error_log("Media Type definido como: " . $mediaType);


                $originalFileNameBase = basename($file['name']);
                $finalFileName = uniqid() . '_' . $originalFileNameBase;

                $targetFilePathLocalReservm = $uploadDirLocalReservm . $finalFileName;
                error_log("Nome do arquivo ÚNICO gerado para todos: " . $finalFileName);
                error_log("Caminho de destino completo (local Reservm): " . $targetFilePathLocalReservm);
                error_log("Caminho temporário do arquivo: " . $file['tmp_name']);

                if (empty($titulo)) {
                    $titulo = pathinfo($originalFileNameBase, PATHINFO_FILENAME);
                    error_log("Título ajustado para: " . $titulo);
                }


                if (move_uploaded_file($file['tmp_name'], $targetFilePathLocalReservm)) {
                    error_log("SUCESSO: Arquivo movido para o local no Reservm: " . $targetFilePathLocalReservm);

                    try {

                        $caminho_para_bd_compartilhado = 'files/banners/' . $finalFileName;

                        $stmt = $conn->query("SELECT ISNULL(MAX(ordem_exibicao), 0) + 1 AS next_order FROM publicidades");
                        $nextOrder = $stmt->fetch(PDO::FETCH_ASSOC)['next_order'];
                        error_log("Próxima ordem de exibição (Reservm): " . $nextOrder);

                        $stmt = $conn->prepare("INSERT INTO publicidades (caminho_imagem, titulo, ativo, ordem_exibicao, media_type) VALUES (:caminho_imagem, :titulo, 1, :ordem_exibicao, :media_type)");
                        $stmt->bindParam(':caminho_imagem', $caminho_para_bd_compartilhado);
                        $stmt->bindParam(':titulo', $titulo);
                        $stmt->bindParam(':ordem_exibicao', $nextOrder, PDO::PARAM_INT);
                        $stmt->bindParam(':media_type', $mediaType);

                        if ($stmt->execute()) {
                            $_SESSION['msg'] = 'Publicidade adicionada com sucesso no Reservm e Painel TV!';
                            error_log("SUCESSO: Dados inseridos no banco de dados pelo Reservm. Caminho BD: " . $caminho_para_bd_compartilhado);

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, PAINEL_TV_API_BASE_URL);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta
                            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                                'operation' => 'add_or_update_file',
                                'file' => new CURLFile($targetFilePathLocalReservm, $mimeType, $finalFileName),
                                'final_file_name' => $finalFileName,
                                'api_key' => PAINEL_TV_API_KEY
                            ]);

                            $response_painel_tv = curl_exec($ch);
                            $http_code_painel_tv = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $curl_error = curl_error($ch);
                            curl_close($ch);

                            if ($http_code_painel_tv === 200) {
                                $data_response_painel_tv = json_decode($response_painel_tv, true);
                                if (isset($data_response_painel_tv['status']) && $data_response_painel_tv['status'] === 'success') {
                                    error_log("SUCESSO: Arquivo enviado e registrado no Painel TV. Resposta: " . print_r($data_response_painel_tv, true));
                                } else {
                                    $_SESSION['atencao'] = 'Publicidade adicionada no Reservm, mas erro ao sincronizar arquivo com Painel TV: ' . ($data_response_painel_tv['message'] ?? 'Erro desconhecido.');
                                    error_log("ERRO (Resposta Painel TV - Sincronização): " . print_r($data_response_painel_tv, true));
                                }
                            } else {
                                $_SESSION['atencao'] = 'Publicidade adicionada no Reservm, mas erro de comunicação com Painel TV: (HTTP Status: ' . $http_code_painel_tv . '). Detalhes cURL: ' . $curl_error . '. Resposta: ' . $response_painel_tv;
                                error_log("ERRO HTTP (Painel TV - Sincronização): Status " . $http_code_painel_tv . " Resposta: " . $response_painel_tv . " cURL Error: " . $curl_error);
                            }

                        } else {
                            $pdoErrorInfo = $stmt->errorInfo();
                            $_SESSION['erro'] = 'Erro ao salvar publicidade no banco de dados. Detalhes: ' . ($pdoErrorInfo[2] ?? 'Erro desconhecido.');
                            error_log("ERRO PDO (execute falhou): " . print_r($pdoErrorInfo, true));
                            if (file_exists($targetFilePathLocalReservm)) {
                                unlink($targetFilePathLocalReservm);
                                error_log("Arquivo local removido após falha na inserção no BD: " . $targetFilePathLocalReservm);
                            }
                        }
                    } catch (PDOException $e) {
                        $_SESSION['erro'] = 'Erro interno do servidor ao adicionar publicidade: ' . $e->getMessage();
                        error_log("ERRO PDO (Exception): " . $e->getMessage());
                        if (file_exists($targetFilePathLocalReservm)) {
                            unlink($targetFilePathLocalReservm);
                            error_log("Arquivo local removido após exceção PDO: " . $targetFilePathLocalReservm);
                        }
                    }
                } else {
                    $_SESSION['erro'] = 'Erro ao mover o arquivo para o diretório local do Reservm.';
                    error_log("FALHA: move_uploaded_file() local do Reservm falhou. Erro detalhado: " . (error_get_last()['message'] ?? 'N/A') . " Caminho: " . $targetFilePathLocalReservm);
                }
            } else {
                $_SESSION['erro'] = 'Dados incompletos para adicionar publicidade.';
                error_log($_SESSION['erro'] . " POST: " . print_r($_POST, true) . " FILES: " . print_r($_FILES, true));
            }
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
            exit;
            break;

        case 'toggle_status_single':
            error_log("Iniciando 'toggle_status_single' case (Reservm).");
            if (isset($_POST['id'])) {
                $id = (int) $_POST['id'];
                $ativo = isset($_POST['ativo']) ? 1 : 0;


                try {
                    $stmt = $conn->prepare("UPDATE publicidades SET ativo = :ativo WHERE id = :id");
                    $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $_SESSION['msg'] = 'Status da publicidade atualizado com sucesso!';
                        error_log("SUCESSO: Status atualizado para ID " . $id . " no Reservm.");

                    } else {
                        $pdoErrorInfo = $stmt->errorInfo();
                        $_SESSION['erro'] = 'Erro ao atualizar status da publicidade no banco de dados. Detalhes: ' . ($pdoErrorInfo[2] ?? 'Erro desconhecido.');
                        error_log("ERRO PDO (toggle_status_single): " . print_r($pdoErrorInfo, true));
                    }
                } catch (PDOException $e) {
                    $_SESSION['erro'] = 'Erro interno do servidor ao alternar status: ' . $e->getMessage();
                    error_log("ERRO PDO (Exception toggle_status_single): " . $e->getMessage());
                }
            } else {
                $_SESSION['atencao'] = 'Dados incompletos para alterar status (ID ausente).';
                error_log($_SESSION['atencao'] . " POST: " . print_r($_POST, true));
            }
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
            exit;
            break;

        case 'delete_publicidade_single':
            error_log("Iniciando 'delete_publicidade_single' case (Reservm).");
            if (isset($_POST['id']) && isset($_POST['caminho'])) {
                $id = (int) $_POST['id'];
                $caminho_imagem_no_bd = $_POST['caminho'];
                $fileName = basename($caminho_imagem_no_bd);

                $filePathLocalReservm = $uploadDirLocalReservm . $fileName;

                try {
                    $conn->beginTransaction();

                    $stmt = $conn->prepare("DELETE FROM publicidades WHERE id = :id");
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                    if ($stmt->execute()) {

                        if (file_exists($filePathLocalReservm)) {
                            if (!unlink($filePathLocalReservm)) {
                                error_log("AVISO: Falha ao excluir arquivo local no Reservm: " . $filePathLocalReservm);
                            } else {
                                error_log("SUCESSO: Arquivo local removido no Reservm: " . $filePathLocalReservm);
                            }
                        } else {
                            error_log("AVISO: Arquivo local não encontrado no Reservm para exclusão (já ausente ou caminho incorreto): " . $filePathLocalReservm);
                        }

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, PAINEL_TV_API_BASE_URL);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, [
                            'operation' => 'delete_file',
                            'file_name' => $fileName,
                            'api_key' => PAINEL_TV_API_KEY
                        ]);

                        $response_painel_tv = curl_exec($ch);
                        $http_code_painel_tv = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $curl_error = curl_error($ch);
                        curl_close($ch);

                        if ($http_code_painel_tv === 200) {
                            $data_response_painel_tv = json_decode($response_painel_tv, true);
                            if (isset($data_response_painel_tv['status']) && $data_response_painel_tv['status'] === 'success') {
                                $_SESSION['msg'] = 'Publicidade excluída com sucesso do Reservm e Painel TV!';
                                error_log("SUCESSO: Comando de exclusão enviado e processado pelo Painel TV. Resposta: " . print_r($data_response_painel_tv, true));
                            } else {
                                $_SESSION['atencao'] = 'Publicidade excluída do Reservm, mas erro ao sincronizar exclusão com Painel TV: ' . ($data_response_painel_tv['message'] ?? 'Erro desconhecido.');
                                error_log("ERRO (Resposta Painel TV - Exclusão): " . print_r($data_response_painel_tv, true));
                            }
                        } else {
                            $_SESSION['atencao'] = 'Publicidade excluída do Reservm, mas erro de comunicação com Painel TV para exclusão: (HTTP Status: ' . $http_code_painel_tv . '). Detalhes cURL: ' . $curl_error . '. Resposta: ' . $response_painel_tv;
                            error_log("ERRO HTTP (Painel TV - Exclusão): Status " . $http_code_painel_tv . " Resposta: " . $response_painel_tv . " cURL Error: " . $curl_error);
                        }
                        $conn->commit();
                    } else {
                        $conn->rollBack();
                        $pdoErrorInfo = $stmt->errorInfo();
                        $_SESSION['erro'] = 'Erro ao excluir publicidade do banco de dados. Detalhes: ' . ($pdoErrorInfo[2] ?? 'Erro desconhecido.');
                        error_log("ERRO PDO (delete_publicidade_single): " . print_r($pdoErrorInfo, true));
                    }
                } catch (PDOException $e) {
                    $conn->rollBack();
                    $_SESSION['erro'] = 'Erro interno do servidor ao excluir publicidade: ' . $e->getMessage();
                    error_log("ERRO PDO (Exception delete_publicidade_single): " . $e->getMessage());
                }
            } else {
                $_SESSION['atencao'] = 'Dados incompletos para excluir publicidade.';
                error_log($_SESSION['atencao']);
            }
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
            exit;
            break;

        case 'update_order_single':
            try {
                $id = (int) $_POST['id'];
                $newOrder = (int) $_POST['new_order'];

                $stmt = $conn->prepare("UPDATE publicidades SET ordem_exibicao = :new_order WHERE id = :id");
                $stmt->bindParam(':new_order', $newOrder, PDO::PARAM_INT);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $_SESSION['msg'] = 'Ordem de exibição atualizada com sucesso!';
                    error_log("SUCESSO: Ordem atualizada para ID " . $id . " no Reservm.");
                } else {
                    $pdoErrorInfo = $stmt->errorInfo();
                    $_SESSION['erro'] = 'Erro ao atualizar ordem de exibição. Detalhes: ' . ($pdoErrorInfo[2] ?? 'Erro desconhecido.');
                    error_log("ERRO PDO (update_order_single): " . print_r($pdoErrorInfo, true));
                }
            } catch (PDOException $e) {
                $_SESSION['erro'] = 'Erro interno do servidor ao atualizar ordem: ' . $e->getMessage();
                error_log("ERRO PDO (Exception update_order_single): " . $e->getMessage());
            }
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
            exit;
            break;

        case 'activate_multiple':
        case 'deactivate_multiple':
            error_log("Iniciando ação em massa (Reservm): " . $action);
            if (isset($_POST['selected_ids']) && is_array($_POST['selected_ids']) && !empty($_POST['selected_ids'])) {
                $ids = $_POST['selected_ids'];
                $status_to_set = ($action === 'activate_multiple') ? 1 : 0;
                $successCount = 0;
                $errors = [];

                try {
                    $conn->beginTransaction();
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $stmt = $conn->prepare("UPDATE publicidades SET ativo = ? WHERE id IN ($placeholders)");
                    $params = array_merge([$status_to_set], $ids);

                    if ($stmt->execute($params)) {
                        $successCount = $stmt->rowCount();
                        $conn->commit();
                        $_SESSION['msg'] = $successCount . ' publicidade(s) ' . ($status_to_set === 1 ? 'ativada(s)' : 'desativada(s)') . ' com sucesso no Reservm!';
                        error_log("SUCESSO: " . $successCount . " publicidade(s) " . ($status_to_set === 1 ? 'ativada(s)' : 'desativada(s)') . " no Reservm.");
                    } else {
                        $conn->rollBack();
                        $pdoErrorInfo = $stmt->errorInfo();
                        $_SESSION['erro'] = 'Erro ao atualizar status em massa no banco de dados. Detalhes: ' . ($pdoErrorInfo[2] ?? 'Erro desconhecido.');
                        error_log("ERRO PDO (activate/deactivate_multiple): " . print_r($pdoErrorInfo, true));
                    }
                } catch (PDOException $e) {
                    $conn->rollBack();
                    $_SESSION['erro'] = 'Erro interno do servidor ao ativar/desativar publicidades: ' . $e->getMessage();
                    error_log("ERRO PDO (Exception activate/deactivate_multiple): " . $e->getMessage());
                }
            } else {
                $_SESSION['atencao'] = 'Nenhum item selecionado para ativar/desativar.';
                error_log($_SESSION['atencao']);
            }
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
            exit;
            break;

        case 'delete_multiple':
            error_log("Iniciando 'delete_multiple' case (Reservm).");
            if (isset($_POST['selected_ids']) && is_array($_POST['selected_ids']) && !empty($_POST['selected_ids'])) {
                $idsToDelete = $_POST['selected_ids'];
                $deletedCount = 0;
                $fileErrors = [];

                try {
                    $conn->beginTransaction();

                    $placeholders = implode(',', array_fill(0, count($idsToDelete), '?'));
                    $stmt = $conn->prepare("SELECT id, caminho_imagem FROM publicidades WHERE id IN ($placeholders)");
                    $stmt->execute($idsToDelete);
                    $itemsToDelete = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $stmtDelete = $conn->prepare("DELETE FROM publicidades WHERE id = ?");

                    foreach ($itemsToDelete as $item) {
                        if ($stmtDelete->execute([$item['id']])) {
                            $deletedCount++;
                            $caminho_imagem_no_bd = $item['caminho_imagem'];


                            $fileName = basename($caminho_imagem_no_bd);

                            $filePathLocalReservm = $uploadDirLocalReservm . $fileName;

                            if (file_exists($filePathLocalReservm)) {
                                if (!unlink($filePathLocalReservm)) {
                                    $fileErrors[] = "Falha ao excluir arquivo local Reservm para ID {$item['id']}.";
                                    error_log("Falha ao excluir arquivo local Reservm: " . $filePathLocalReservm);
                                }
                            } else {
                                error_log("AVISO: Arquivo local Reservm não encontrado para ID {$item['id']}: " . $filePathLocalReservm);
                            }

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, PAINEL_TV_API_BASE_URL);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                                'operation' => 'delete_file',
                                'file_name' => $fileName,
                                'api_key' => PAINEL_TV_API_KEY
                            ]);
                            $response_painel_tv = curl_exec($ch);
                            $http_code_painel_tv = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            $curl_error = curl_error($ch);
                            curl_close($ch);

                            if ($http_code_painel_tv === 200) {
                                $data_response_painel_tv = json_decode($response_painel_tv, true);
                                if (!(isset($data_response_painel_tv['status']) && $data_response_painel_tv['status'] === 'success')) {
                                    $fileErrors[] = "Erro Painel TV ao excluir arquivo ID {$item['id']}: " . ($data_response_painel_tv['message'] ?? 'Erro desconhecido.');
                                    error_log("ERRO (Resposta Painel TV - Exclusão em Massa): " . print_r($data_response_painel_tv, true));
                                }
                            } else {
                                $fileErrors[] = "Erro de comunicação Painel TV ao excluir ID {$item['id']}: HTTP {$http_code_painel_tv}. Erro cURL: {$curl_error}.";
                                error_log("ERRO HTTP (Painel TV - Exclusão em Massa): Status " . $http_code_painel_tv . " Resposta: " . $response_painel_tv . " cURL Error: " . $curl_error);
                            }
                        } else {
                            $pdoErrorInfo = $stmtDelete->errorInfo();
                            $fileErrors[] = "Falha ao excluir ID {$item['id']} do BD: " . ($pdoErrorInfo[2] ?? 'Erro desconhecido.');
                            error_log("ERRO PDO (delete_multiple - delete): " . print_r($pdoErrorInfo, true));
                        }
                    }

                    $conn->commit();
                    $message = $deletedCount . ' publicidade(s) excluída(s) com sucesso do Reservm.';
                    if (!empty($fileErrors)) {
                        $message .= ' Alguns arquivos ou sincronizações com Painel TV falharam.';
                        $_SESSION['erro'] = $message . ' Detalhes: ' . implode('; ', $fileErrors);
                    } else {
                        $_SESSION['msg'] = $message;
                    }
                    error_log("SUCESSO/ERRO EXCLUSÃO EM MASSA: " . $message);

                } catch (PDOException $e) {
                    $conn->rollBack();
                    $_SESSION['erro'] = 'Erro interno do servidor ao excluir publicidades em massa: ' . $e->getMessage();
                    error_log("ERRO PDO (Exception delete_multiple): " . $e->getMessage());
                }
            } else {
                $_SESSION['atencao'] = 'Nenhum item selecionado para exclusão.';
                error_log($_SESSION['atencao']);
            }
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
            exit;
            break;

        default:
            $_SESSION['erro'] = 'Ação não reconhecida: ' . $action;
            error_log($_SESSION['erro']);
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? $url_sistema . '/admin/publicidades.php');
            exit;
            break;
    }
} else {
    $_SESSION['erro'] = 'Acesso inválido ao script de ações.';
    error_log($_SESSION['erro'] . " REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . " ACTION: " . ($_POST['action'] ?? 'N/A'));
    header('Location: ' . $url_sistema . '/admin/publicidades.php');
    exit;
}