<?php

include '../conexao/conexao.php';


if (!isset($conn) || $conn === null) {
    error_log("Erro crítico: \$conn é null em publicidades.php. Conexão não estabelecida.");
    die("Problema interno: Conexão com o banco de dados não estabelecida. Contate o administrador.");
} else {
    error_log("Conexão \$conn estabelecida com sucesso em publicidades.php.");
}


include 'includes/header.php';
?>

<style>
    .desc {
        background: #dfecff;
        padding: 25px;
        border-radius: 6px;
        /* margin: 20px 0 20px 0; */
        line-height: 28px;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Publicidades</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
                    <li class="breadcrumb-item active">Publicidades</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-sm-6 text-sm-start text-center">
                        <h5 class="card-title mb-0">Lista de Publicidade</h5>
                    </div>
                    <div
                        class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
                        <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal"
                            data-bs-toggle="button" data-bs-target="#modalAdicionarPublicidade">+ Adicionar
                            Publicidade</button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">

                <table id="tabelaPublicidades" class="table dt-responsive nowrap align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Título</th>
                            <th>Visualizar</th>
                            <th class="text-end">Ativo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="listaPublicidades">
                        <?php
                        try {
                            $stmt = $conn->query("SELECT * FROM publicidades ORDER BY ordem_exibicao ASC");
                            $publicidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($publicidades) > 0) {
                                foreach ($publicidades as $pub) {

                                    $baseReservmPublicUrl = $url_sistema . "/admin/includes/";

                                    $mediaPath = $baseReservmPublicUrl . htmlspecialchars($pub['caminho_imagem']);

                                    $previewHtml = '';
                                    if (isset($pub['caminho_imagem']) && isset($pub['media_type'])) {
                                        if ($pub['media_type'] === 'image') {
                                            $previewHtml = '<img src="' . $mediaPath . '" alt="Preview" class="img-thumbnail" style="max-width: 80px; max-height: 80px;">';
                                        } elseif ($pub['media_type'] === 'video') {
                                            $previewHtml = '<video controls class="img-thumbnail" style="max-width: 80px; max-height: 80px;"><source src="' . $mediaPath . '" type="video/mp4"></video>';
                                        } else {
                                            $previewHtml = '<span class="text-danger">Tipo de mídia desconhecido</span>';
                                        }
                                    } else {
                                        $previewHtml = '<span class="text-danger">Dados ausentes</span>';
                                    }
                                    ?>
                                    <tr data-id="<?= $pub['id'] ?>">
                                        <td>
                                            <?= htmlspecialchars($pub['media_type'] == 'image' ? 'Imagem' : 'Vídeo') ?>
                                        </td>
                                        <td><?= htmlspecialchars($pub['titulo']) ?></td>
                                        <td>
                                            <?php if ($pub['media_type'] === 'image'): ?>
                                                <a href="#" class="view-media" data-bs-toggle="modal" data-bs-target="#mediaViewerModal"
                                                    data-media-type="image" data-media-src="<?= $mediaPath ?>">
                                                    <?= $previewHtml ?> </a>
                                            <?php elseif ($pub['media_type'] === 'video'): ?>
                                                <a href="#" class="view-media" data-bs-toggle="modal" data-bs-target="#mediaViewerModal"
                                                    data-media-type="video" data-media-src="<?= $mediaPath ?>">
                                                    <?= $previewHtml ?> </a>
                                            <?php else: ?>
                                                <?= $previewHtml ?>             <?php endif; ?>
                                        </td>
                                        <td class="">
                                            <form action="controller/controller_publicidades.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="toggle_status_single">
                                                <input type="hidden" name="id" value="<?= $pub['id'] ?>">
                                                <div class="form-check form-switch form-switch-success text-end">
                                                    <input class="form-check-input" type="checkbox" id="switch-<?= $pub['id'] ?>"
                                                        name="ativo" value="1" <?= $pub['ativo'] ? 'checked' : '' ?>
                                                        onchange="this.form.submit()">
                                                    <label class="form-check-label" for="switch-<?= $pub['id'] ?>"></label>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <form action="controller/controller_publicidades.php" method="POST"
                                                class="d-inline form-delete-publicidade">
                                                <input type="hidden" name="action" value="delete_publicidade_single">
                                                <input type="hidden" name="id" value="<?= $pub['id'] ?>">
                                                <input type="hidden" name="caminho"
                                                    value="<?= htmlspecialchars($pub['caminho_imagem']) ?>">
                                                <button type="submit" class="btn text-danger">
                                                    <i class="ri-delete-bin-line fa-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="8" class="text-center">Nenhuma publicidade cadastrada.</td></tr>';
                            }
                        } catch (PDOException $e) {
                            error_log("Erro ao carregar publicidades: " . $e->getMessage());
                            echo '<tr><td colspan="8" class="text-center text-danger">Erro ao carregar publicidades.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


<!--  CADASTRAR -->
<div class="modal fade modal_padrao" id="modalAdicionarPublicidade" tabindex="-1"
    aria-labelledby="modalAdicionarPublicidade" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title" id="modalAdicionarPublicidadeLabel">Cadastrar Publicidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row justify-content-center m-0">
                    <div class="col-md-12">

                        <div class="desc">
                            <h6 class="text-uppercase">Instruções para Upload de Publicidade</h6>

                            <ol>
                                <li class="mb-2">O preenchimento do título é <strong>opcional</strong>.</li>
                                <li class="mb-2">Formatos aceitos: <strong>PNG, JPG, JPEG, GIF, MP4, WEBM</strong>.</li>
                                <li>Tamanho máximo por arquivo: <strong>10 MB</strong>.</li>

                            </ol>

                        </div>
                    </div>
                </div>

                <form id="formAdicionarPublicidadeModal" action="controller/controller_publicidades.php" method="POST"
                    enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_publicidade">
                    <div class="modal-body">
                        <div class="col-12 mb-3">
                            <label for="tituloPublicidadeModal" class="form-label">Título</label>
                            <input type="text" class="form-control" id="tituloPublicidadeModal"
                                name="tituloPublicidade">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="uploadArquivoModal" class="form-label">Arquivo <small
                                    class="text-danger">*</small></label>
                            <input class="form-control" type="file" id="uploadArquivoModal" name="uploadArquivo"
                                accept="image/*,video/mp4,video/webm" required>
                            <!-- <small class="text-muted">Formatos aceitos: Imagens (JPG, PNG, GIF) e Vídeos (MP4).</small> -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn botao btn-light waves-effect"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn botao botao_verde waves-effect">Enviar</button>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>

<!-- MODAL DE VISUALIZAÇÃO -->
<div class="modal fade" id="mediaViewerModal" tabindex="-1" aria-labelledby="mediaViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaViewerModalLabel">Visualizar Publicidade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="mediaContent">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<?php include 'includes/footer.php'; ?>


<script>
    $(document).ready(function () {

        $(document).on('click', '.view-media', function () {
            const mediaType = $(this).data('media-type');
            const mediaSrc = $(this).data('media-src');
            const mediaContentDiv = $('#mediaContent');

            mediaContentDiv.empty(); // Limpa qualquer conteúdo anterior

            if (mediaType === 'image') {
                mediaContentDiv.html('<img src="' + mediaSrc + '" class="img-fluid" alt="Visualização de Imagem">');
            } else if (mediaType === 'video') {
                mediaContentDiv.html('<video controls autoplay loop class="img-fluid"><source src="' + mediaSrc + '" type="video/mp4"></video>');

                // mediaContentDiv.html('<video controls autoplay loop class="img-fluid"><source src="' + mediaSrc + '" type="video/mp4"><source src="' + mediaSrc + '" type="video/webm"></video>');
            }

        });
    });
</script>