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
        line-height: 28px;
    }

    .flatpickr-day {
        margin-bottom: 8px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        height: 38px !important;
        line-height: 38px !important;
        border-radius: 50% !important;
        border: 1px solid transparent;
    }

    .flatpickr-day.evento-inativo {
        background-color: #d2d3d4 !important;
        color: #878a8d !important;
        opacity: 0.7 !important;
        text-decoration: line-through;

    }

    /* ESTILO VISUAL IGUAL AO CALENDÁRIO */
    .flatpickr-day.evento-no-dia {
        color: #333 !important;
    }

    .flatpickr-day.evento-no-dia:hover {
        opacity: 0.8;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Espelhamento</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
                    <li class="breadcrumb-item active">Espelhamento</li>
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
                        <h5 class="card-title mb-0">Histórico de Espelhamentos</h5>
                    </div>
                    <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
                        <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal"
                            data-bs-toggle="button" data-bs-target="#modal_espelhamento_medicina">+ Adicionar
                            Espelhamento</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="tab_espelhamento" class="table dt-responsive nowrap align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th><span class="me-3">Solicitação Origem</span></th>
                                <th><span class="me-3">Período Origem</span></th>
                                <th><span class="me-3">Solicitação Destino</span></th>
                                <th><span class="me-3">Período Destino</span></th>
                                <th><span class="me-3">Componente Curricular</span></th>
                                <th><span class="me-3">Espelhado Por</span></th>
                                <th><span class="me-3">Data</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                          
                                $sql = "SELECT 
                                            so.solic_id AS id_origem,
                                            so.solic_codigo AS cod_origem, 
                                            sd.solic_id AS id_destino,
                                            sd.solic_codigo AS cod_destino,
                                            eh.eh_periodo_origem,
                                            eh.eh_periodo_destino,
                                            comp.compc_componente,
                                            admin.admin_nome,
                                            eh.eh_data_cadastro
                                        FROM espelhamento_historico eh
                                        INNER JOIN solicitacao so ON so.solic_id = eh.eh_solic_origem_id
                                        INNER JOIN solicitacao sd ON sd.solic_id = eh.eh_solic_destino_id
                                        LEFT JOIN componente_curricular comp ON comp.compc_id = sd.solic_comp_curric
                                        LEFT JOIN admin ON admin.admin_id = eh.eh_realizado_por
                                        ORDER BY eh.eh_data_cadastro DESC";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute();

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                    <tr>
                                        <td>
                                            <strong>
                                                <a href="solicitacao_analise.php?i=<?= $row['id_origem'] ?>" target="_blank" class="text-decoration-none" title="Ver Solicitação Original" style="color: #212529;">
                                                    <?= $row['cod_origem'] ?> <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 10px; "></i>
                                                </a>
                                            </strong>
                                        </td>

                                        <td><?= $row['eh_periodo_origem'] ?></td>


                                        <td> <strong>
                                                <a href="solicitacao_analise.php?i=<?= $row['id_destino'] ?>" target="_blank" class="text-decoration-none" title="Ver Nova Solicitação" style="color: #212529;">
                                                    <?= $row['cod_destino'] ?> <i class="fa-solid fa-arrow-up-right-from-square ms-1" style="font-size: 10px;"></i>
                                                </a>
                                            </strong>
                                        </td>

                                        <td><?= $row['eh_periodo_destino'] ?></td>
                                        <td class="text-wrap" style="max-width: 250px;"><?= $row['compc_componente'] ?></td>
                                        <td><?= $row['admin_nome'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['eh_data_cadastro'])) ?></td>
                                    </tr>
                            <?php
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='8' class='text-center text-danger'>Erro ao carregar histórico: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!--  CADASTRAR -->
<div class="modal fade modal_padrao" id="modal_espelhamento_medicina" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title">Espelhamento</h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">

                <form method="POST" action="controller/controller_espelhamento_medicina.php" id="formEspelhamentoMedicina" class="form_solicitacao needs-validation" novalidate>

                    <div class="label_info label_info_verde mt-0 mb-3">
                        <i class="fa-solid fa-calendar-days me-2"></i> <strong class="text-uppercase">Espelhamento de solicitações do curso de medicina</strong><br>
                        A migração replica, para o período letivo de destino, as turmas existentes no período letivo de origem, considerando apenas os componentes selecionados e o intervalo de datas definido. As novas reservas são criadas exclusivamente dentro desse intervalo e seguem a mesma estrutura das reservas originais. Datas que coincidirem com feriados do período de destino são automaticamente ignoradas pelo sistema, evitando a criação de reservas nesses dias.
                        <br><br>
                        <strong>Obs.:</strong> Caso algum componente ou solicitação não exista no período letivo de origem, ele não será espelhado para o período letivo de destino</strong>
                    </div>


                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Período letivo de <strong>origem</strong><span>*</span></label>
                            <select class="form-select" name="periodo_origem" id="periodo_origem" required>
                                <option value="" selected disabled>Selecione</option>
                                <?php
                                $anoAtual = date('Y');
                                for ($i = 0; $i <= 2; $i++) {
                                    $ano = $anoAtual - $i;
                                    echo "<option value='{$ano}.2'>{$ano}.2</option>";
                                    echo "<option value='{$ano}.1'>{$ano}.1</option>";
                                }
                                ?>
                            </select>
                             <div class="invalid-feedback">Este campo é obrigatório</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Período letivo de <strong>destino</strong> <span>*</span></label>
                            <select class="form-select" name="periodo_destino" id="periodo_destino" required>
                                <option value="" selected disabled>Selecione</option>
                                <?php
                                $anoAtual = date('Y');
                                for ($i = 1; $i <= 3; $i++) {
                                    $ano = $anoAtual + $i;
                                    echo "<option value='{$ano}.1'>{$ano}.1</option>";
                                    echo "<option value='{$ano}.2'>{$ano}.2</option>";
                                }
                                ?>
                            </select>
                             <div class="invalid-feedback">Este campo é obrigatório</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                       
                        <div class="col-md-6">
                            <label class="form-label">Data Início (Destino)<span>*</span></label>
                            <input type="text" class="form-control flatpickr_data" name="data_inicio_custom" required>
                             <div class="invalid-feedback">Este campo é obrigatório</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data Fim (Destino)<span>*</span></label>
                            <input type="text" class="form-control flatpickr_data" name="data_fim_custom" required>
                             <div class="invalid-feedback">Este campo é obrigatório</div>
                        </div>



                    </div>


                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Componentes Curriculares <span>*</span></label>
                            <select class="form-select" id="filtro_tipo_aluno">
                                <option value="todos" selected>TODOS</option>
                                <option value="calouros">CALOUROS (1º SEMESTRE)</option>
                                <option value="veteranos">VETERANOS (2º A 12º SEMESTRE)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="mb-3" style=" height: 300px; overflow-y: auto; overflow-x: hidden;">
                                <div id="lista_componentes_loading" class="text-center text-muted mt-5" style="display:none;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Carregando componentes...
                                </div>
                                <div id="container_checkboxes">
                                    <div class="text-center text-muted mt-5">
                                        <small>Selecione um grupo acima para carregar a lista.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" id="marcar_todos">
                                <label class="form-check-label" for="marcar_todos"><small>Marcar/Desmarcar Todos</small></label>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="hstack gap-3 align-items-center justify-content-end mt-4">
                            <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn botao botao_verde waves-effect" id="btnConfirmarEspelhamento">
                                Cadastrar
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>





<script>
    // ==============================================================
    // 1️⃣ FUNÇÃO PARA CARREGAR COMPONENTES (AJAX)
    // ==============================================================
    function carregarComponentes() {
        var tipo = $('#filtro_tipo_aluno').val();
        var container = $('#container_checkboxes');
        var loading = $('#lista_componentes_loading');

        container.empty();
        loading.show();

        console.log("🔄 Buscando componentes para:", tipo); // Debug

        $.ajax({
            url: 'controller/get_componentes_medicina.php',
            type: 'GET',
            data: {
                tipo: tipo
            },
            dataType: 'json',
            success: function(data) {
                loading.hide();
                if (data.length > 0) {
                    var html = '<div class="row">';
                    $.each(data, function(index, item) {
                        html += `
                            <div class="col-md-4 mb-1"> 
                                <div class="form-check">
                                    <input class="form-check-input check-comp" type="checkbox" name="componentes_selecionados[]" value="${item.compc_id}" id="comp_${item.compc_id}">
                                    <label class="form-check-label text-truncate w-100" for="comp_${item.compc_id}" title="${item.compc_componente}">
                                        ${item.compc_componente} 
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.html(html);
                } else {
                    container.html('<p class="text-center text-muted mt-5">Nenhum componente encontrado.</p>');
                }
            },
            error: function(xhr, status, error) {
                loading.hide();
                console.error("Erro AJAX:", xhr.responseText);
                container.html('<p class="text-danger text-center mt-5">Erro ao carregar componentes.</p>');
            }
        });
    }

    // ==============================================================
    // 2️⃣ EVENTOS E INICIALIZAÇÃO (SELECT2 + MODAL)
    // ==============================================================

    $(document).ready(function() {

        // 1. Configura o ouvinte de mudança NO SELECT ORIGINAL
        // O Select2 repassa o evento change para o select original
        $('#filtro_tipo_aluno').on('change', function() {
            carregarComponentes();
        });

        // 2. Inicializa Select2 e Carrega Dados
        $('#modal_espelhamento_medicina').on('shown.bs.modal', function() {

            // Inicializa Select2 em todos os selects DENTRO deste modal
            $(this).find('select').each(function() {
                // Verifica se já não foi inicializado para evitar duplicidade
                if (!$(this).data('select2')) {
                    $(this).select2({
                        dropdownParent: $('#modal_espelhamento_medicina'),
                        width: '100%',
                        language: "pt-BR"
                    });
                }
            });

            // Se a lista estiver vazia, força o carregamento inicial
            if ($('#container_checkboxes').children().length <= 1) {
                console.log("⚡ Trigger inicial disparado");
                // Garante valor 'todos' e dispara o evento change (que chama o AJAX acima)
                $('#filtro_tipo_aluno').val('todos').trigger('change');
            }
        });

        // 3. Checkbox "Marcar Todos"
        $('#marcar_todos').change(function() {
            $('.check-comp').prop('checked', $(this).is(':checked'));
        });
    });

    // ==============================================================
    // 3️⃣ SUBMIT E VALIDAÇÃO
    // ==============================================================
    document.getElementById('btnConfirmarEspelhamento').addEventListener('click', function() {
        const form = document.getElementById('formEspelhamentoMedicina');
        if ($('input[name="componentes_selecionados[]"]:checked').length === 0) {
            Swal.fire('Atenção', 'Selecione pelo menos um componente curricular.', 'warning');
            return;
        }
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        // Pega o texto da opção selecionada no Select2 ou Select normal
        const origemText = $('#periodo_origem option:selected').text();
        const destinoText = $('#periodo_destino option:selected').text();

        Swal.fire({
            title: 'Confirmar Espelhamento?',
            html: `Você está copiando de <strong>${origemText}</strong> para <strong>${destinoText}</strong>.<br>Apenas os componentes selecionados serão processados.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0461AD',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sim, Espelhar!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processando...',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });


    // ==============================================================
    // 4️⃣ CALENDÁRIO FLATPICKR (MANTER LÓGICA DE CORES)
    // ==============================================================
    const coresMotivo = {
        'domingo': 'rgba(255, 0, 0, 0.1)',
        'feriado': 'rgba(255, 0, 0, 0.1)',
        'liberação bahiana': 'rgba(255, 179, 71, 0.18)',
        'prosef medicina': 'rgba(195, 230, 203, 0.18)',
        'prosef saúde': 'rgba(179, 229, 252, 0.18)',
        'férias alunos': 'rgba(12, 238, 219, 0.18)',
        'início das aulas veteranos': 'rgba(253, 195, 19, 0.3)',
        'início das aulas calouros': 'rgba(253, 195, 19, 0.3)',
        'recesso carnaval': 'rgba(243, 51, 243, 0.25)',
        'recepção dos calouros': 'rgba(56, 56, 55, 0.18)',
        'recesso': 'rgba(243, 51, 243, 0.25)',
        'prova final veteranos': 'rgba(151, 11, 233, 0.34)',
        'início férias veteranos': 'rgba(12, 238, 219, 0.18)',
        'início do planejamento pedagógico': 'rgba(97, 97, 97, 0.28)',
        'prova final calouros': 'rgba(151, 11, 233, 0.34)',
        'início férias calouros': 'rgba(12, 238, 219, 0.18)',
        'fim do planejamento pedagógico': 'rgba(97, 97, 97, 0.28)',
        'fim das férias alunos': 'rgba(12, 238, 219, 0.18)',
        'início das aulas': 'rgba(240, 213, 131, 0.18)',
        'fórum pedagógico': 'rgba(0, 15, 151, 0.34)',
        'xxiv jornada de iniciação científica e tecnológica e xvi fórum de pesquisadores': 'rgba(110, 58, 9, 0.34)',
        'eleições': 'rgba(190, 145, 11, 0.37)',
        'xxvi mcc e xiii mostra de extensão': 'rgba(243, 184, 8, 0.32)',
        'prova final': 'rgba(151, 11, 233, 0.34)',
        'início / fim do planejamento pedagógico': 'rgba(97, 97, 97, 0.28)',
        'normal': 'rgba(207, 226, 255, 0.12)'
    };


    fetch('controller/controller_flatpickr_eventos.php')
        .then(r => r.json())
        .then(data => {
            let eventosMap = {};
            if (data.success && data.events) {
                data.events.forEach(ev => {
                    let isAtivo = (ev.ativo === 1);

                    // 1. Define a cor
                    let cor = isAtivo ?
                        (coresMotivo[(ev.title || '').toLowerCase()] || '#ccc') :
                        '#d2d3d4';

                    // 2. Define a classe CSS
                    let cls = isAtivo ? 'evento-no-dia' : 'evento-inativo';

                    // 3. Define o Título (Tooltip) 
                    let titulo = ev.title;
                    if (!isAtivo) {
                        titulo += " (Data liberada)";
                    }

                    eventosMap[ev.date] = {
                        title: titulo,
                        color: cor,
                        className: cls
                    };
                });
            }
            iniciarFlatpickr(eventosMap);
        })
        .catch(() => iniciarFlatpickr({}));

    function iniciarFlatpickr(eventosMap) {
        // Pega o ano atual (ex: 2025) e monta "2025-01-01"
        const anoAtual = new Date().getFullYear();
        const dataMinima = anoAtual + "-01-01";

        flatpickr(".flatpickr_data", {
            locale: "pt",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            allowInput: true,
            
            // AQUI ESTÁ A TRAVA DO ANO ATUAL
            minDate: dataMinima, 
            
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                if (dayElem.classList.contains("prevMonthDay") || dayElem.classList.contains("nextMonthDay")) return;
                
                const dateStr = fp.formatDate(dayElem.dateObj, "Y-m-d");
                const info = eventosMap[dateStr];
                
                if (info) {
                    dayElem.style.backgroundColor = info.color;
                    dayElem.classList.add(info.className);
                    dayElem.title = info.title;
                }
            }
        });
       
    }
</script>



<?php include 'includes/footer.php'; ?>