<?php
// Inclui cabeçalho (deve ter session_start(), $conn e as variáveis globais de perfil)
include 'includes/header.php';

// IDs dos perfis para referência
$PERFIL_ADMIN = 1;
$PERFIL_OPERADOR = 2; // Assumindo que o ID do perfil de Operador é 2

$perfil_id = $global_admin_perfil ?? 0;
?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg"></div>
</div>

<div class="row breadcrumb_painel">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Ocorrências</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
                    <li class="breadcrumb-item active">Ocorrências</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Lista de Ocorrências</h5>
            </div>
            <div class="card-body p-0">
                <table id="tab_ocor" class="table dt-responsive align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Código Ocorrência</th>
                            <th>Data Ocorrência</th>
                            <th>Início Realizado</th>
                            <th>Término Realizado</th>
                            <th>Tipo Ocorrência</th>
                            <th>Local</th>
                            <th>Andar</th>
                            <th>Pavilhão</th>
                            <th>Campus</th>
                            <th>Tipo de espaço</th>
                            <th>Operador</th>
                            <th>Data cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $conn->prepare("
                                SELECT 
                                    o.*, 
                                    r.res_data, r.res_hora_inicio, r.res_solic_id,
                                    e.esp_nome_local, e.esp_pavilhao, e.esp_andar, e.esp_unidade,
                                    te.tipesp_tipo_espaco,
                                    u_and.and_andar,
                                    u_pav.pav_pavilhao,
                                    u_uni.uni_unidade,
                                    a.admin_nome
                                FROM ocorrencias o
                                INNER JOIN reservas r ON r.res_id = o.oco_res_id
                                INNER JOIN espaco e ON e.esp_id = r.res_espaco_id
                                INNER JOIN tipo_espaco te ON te.tipesp_id = e.esp_tipo_espaco
                                LEFT JOIN pavilhoes u_pav ON u_pav.pav_id = e.esp_pavilhao
                                LEFT JOIN andares u_and ON u_and.and_id = e.esp_andar
                                LEFT JOIN unidades u_uni ON u_uni.uni_id = e.esp_unidade
                                INNER JOIN admin a ON a.admin_id = o.oco_user_id
                                ORDER BY o.oco_data_cad DESC
                            ");

                            $stmt->execute();

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $oco_id = $row['oco_id'];
                                $solic_id = $row['res_solic_id'];

                                // ==============================================================================
                                // ALTERAÇÃO: Lógica de redirecionamento condicional baseada no perfil
                                // ==============================================================================
                                if ((int) $perfil_id === $PERFIL_OPERADOR) {
                                    // Operadores são redirecionados para a página da solicitação principal
                                    $link_destino = 'solicitacao_analise.php?i=' . urlencode($solic_id);
                                } else {
                                    // Admins e outros perfis vão para a análise da ocorrência específica
                                    $link_destino = 'ocorrencia_analise.php?i=' . urlencode($oco_id);
                                }
                                // ==============================================================================
                        
                                $tipo_ocorrencia_ids = trim($row['oco_tipo_ocorrencia'] ?? '');
                                $row['tipos_formatados'] = '';

                                if (!empty($tipo_ocorrencia_ids)) {
                                    $ids_array = array_filter(array_map('trim', explode(',', $tipo_ocorrencia_ids)));
                                    if (!empty($ids_array)) {
                                        $placeholders = implode(',', array_fill(0, count($ids_array), '?'));
                                        $sql_tipo_oco = "SELECT cto_tipo_ocorrencia FROM conf_tipo_ocorrencia WHERE cto_id IN ($placeholders)";
                                        $stmt_oco = $conn->prepare($sql_tipo_oco);
                                        $stmt_oco->execute($ids_array);
                                        $tipo_oco = $stmt_oco->fetchAll(PDO::FETCH_COLUMN);
                                        $row['tipos_formatados'] = '• ' . implode('<br>• ', $tipo_oco);
                                    }
                                }
                                extract($row);
                                ?>
                                <tr data-href="<?= $link_destino ?>" style="cursor: pointer;">
                                    <th scope="row"><?= $oco_codigo ?></th>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($res_data))) ?></td>

                                    <td><?= !empty($oco_hora_inicio_realizado) ? date('H:i', strtotime($oco_hora_inicio_realizado)) : 'N/A' ?>
                                    </td>
                                    <td><?= !empty($oco_hora_fim_realizado) ? date('H:i', strtotime($oco_hora_fim_realizado)) : 'N/A' ?>
                                    </td>
                                    <td class="text-uppercase"><?= $tipos_formatados ?></td>
                                    <td class="text-uppercase"><?= $esp_nome_local ?></td>
                                    <td class="text-uppercase"><?= $and_andar ?></td>
                                    <td class="text-uppercase"><?= $pav_pavilhao ?></td>
                                    <td class="text-uppercase"><?= $uni_unidade ?></td>
                                    <td class="text-uppercase"><?= $tipesp_tipo_espaco ?></td>
                                    <td class="text-uppercase"><?= $admin_nome ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($oco_data_cad)) ?></td>
                                </tr>
                            <?php }
                        } catch (PDOException $e) {
                            echo "Erro: " . $e->getMessage();
                        } ?>
                    </tbody>
                </table>
            </div>
            <script>
                $(document).ready(function () {
                    $('table').on('click', 'tr', function (e) {
                        if ($(e.target).closest('a, .btn, .no-link').length === 0) {
                            const href = $(this).data('href');
                            if (href) {
                                window.location.href = href;
                            }
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>