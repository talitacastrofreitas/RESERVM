<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Períodos Letivos (Datas)</h4>
            <div class="page-title-right">
                <button type="button" class="btn botao botao_verde waves-effect" 
                        data-bs-toggle="modal" data-bs-target="#modal_periodo" onclick="limparModal()">
                    <i class="fa-solid fa-plus me-1"></i> Novo Período
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info fs-13">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Cadastre aqui os intervalos de datas (Início e Fim das aulas). Esses períodos serão selecionados na hora de realizar o espelhamento de turmas.
                </div>
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data Início</th>
                            <th>Data Término</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM conf_semestre_periodo ORDER BY semp_data_inicio DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $dt_ini = date('d/m/Y', strtotime($row['semp_data_inicio']));
                            $dt_fim = date('d/m/Y', strtotime($row['semp_data_fim']));
                        ?>
                            <tr>
                                <td><?= $row['semp_id'] ?></td>
                                <td class="fw-bold text-success"><?= $dt_ini ?></td>
                                <td class="fw-bold text-danger"><?= $dt_fim ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-soft-primary" 
                                            onclick="editarPeriodo('<?= $row['semp_id'] ?>', '<?= $row['semp_data_inicio'] ?>', '<?= $row['semp_data_fim'] ?>')"
                                            data-bs-toggle="modal" data-bs-target="#modal_periodo">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="controller/controller_periodo.php?acao=deletar&id=<?= $row['semp_id'] ?>" 
                                       class="btn btn-sm btn-soft-danger" onclick="return confirm('Tem certeza?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal_padrao" id="modal_periodo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title" id="tituloModal">Novo Período</h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <form action="controller/controller_semestre_periodo.php" method="POST">
                    <input type="hidden" name="acao" id="acao" value="cadastrar">
                    <input type="hidden" name="semp_id" id="semp_id">

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Início das Aulas <span>*</span></label>
                            <input type="date" class="form-control" name="data_inicio" id="data_inicio" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Término das Aulas <span>*</span></label>
                            <input type="date" class="form-control" name="data_fim" id="data_fim" required>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn botao btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn botao botao_verde">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function limparModal() {
        document.getElementById('tituloModal').innerText = 'Novo Período';
        document.getElementById('acao').value = 'cadastrar';
        document.getElementById('semp_id').value = '';
        document.getElementById('data_inicio').value = '';
        document.getElementById('data_fim').value = '';
    }
    function editarPeriodo(id, ini, fim) {
        document.getElementById('tituloModal').innerText = 'Editar Período';
        document.getElementById('acao').value = 'editar';
        document.getElementById('semp_id').value = id;
        document.getElementById('data_inicio').value = ini;
        document.getElementById('data_fim').value = fim;
    }
</script>

<?php include 'includes/footer.php'; ?>