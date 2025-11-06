<?php
// AVISO: Este arquivo é incluído por canceladas.php.
// As variáveis $conn e $global_user_id devem estar disponíveis no escopo do include.

// Garante o acesso às variáveis globais
global $conn;
global $global_user_id;

$solicitacoes_ativas = [];
$reservas_ativas = [];

try {
    // 1. CONSULTA: Solicitações Ativas (Para Cancelamento Completo)
    $stmt_solic = $conn->prepare("
SELECT DISTINCT 
            s.solic_id, 
            s.solic_codigo, 
            s.solic_nome_prof_resp, 
            s.solic_nome_comp_ativ, 
            s.solic_nome_atividade,
            cc.compc_componente -- Adicionando a coluna do componente
        FROM solicitacao s
        -- JUNTA COM A TABELA DE COMPONENTE CURRICULAR
        LEFT JOIN componente_curricular cc ON cc.compc_id = s.solic_comp_curric 
        -- JUNTA COM STATUS ATUAL (ROW_NUMBER)
        INNER JOIN (
            SELECT 
                solic_sta_solic_id, 
                solic_sta_status,
                ROW_NUMBER() OVER (PARTITION BY solic_sta_solic_id ORDER BY solic_sta_data_cad DESC) AS rn
            FROM solicitacao_status
        ) AS st ON st.solic_sta_solic_id = s.solic_id AND st.rn = 1 

        -- LEFT JOIN reservas para verificar pendências (Filtro NOT IN abaixo)
        LEFT JOIN reservas r ON r.res_solic_id = s.solic_id 

        WHERE s.solic_cad_por = :user_id
        AND st.solic_sta_status IN (3, 4, 5, 7) 
        
        -- Filtro de pendência (Mantido como NOT IN para evitar exclusão de solicitações sem reservas)
        AND s.solic_id NOT IN (
            SELECT res_solic_id 
            FROM reservas 
            WHERE LTRIM(RTRIM(res_solic_cancelamento_id)) <> '' OR res_solic_cancelamento_id IS NOT NULL
        )
        ORDER BY s.solic_codigo DESC
    ");
    $stmt_solic->execute([':user_id' => $global_user_id]);
    $solicitacoes_ativas = $stmt_solic->fetchAll(PDO::FETCH_ASSOC);

    // 2. CONSULTA: Reservas Ativas (Para Cancelamento Único)
    $stmt_res = $conn->prepare("
 
         SELECT 
            r.res_id, 
            r.res_codigo, 
            r.res_data, 
            r.res_hora_inicio, 
            cc.compc_componente AS nome_componente -- PEGA O NOME REAL DA TABELA DE COMPONENTE
            -- Coluna r.res_componente_atividade_nome removida ou substituída.
        FROM reservas r
        -- NOVO JOIN: Liga o ID do componente da reserva com a tabela componente_curricular
        INNER JOIN componente_curricular cc ON cc.compc_id = r.res_componente_atividade
        -- Junta com a solicitação para buscar o ID do usuário cadastrado
        INNER JOIN solicitacao s ON s.solic_id = r.res_solic_id 
        WHERE s.solic_cad_por = :user_id
        AND (r.res_solic_cancelamento_id IS NULL OR LTRIM(RTRIM(r.res_solic_cancelamento_id)) = '') 
        ORDER BY r.res_data ASC, r.res_hora_inicio ASC
    ");
    $stmt_res->execute([':user_id' => $global_user_id]);
    $reservas_ativas = $stmt_res->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao popular selects no modal (DB): " . $e->getMessage());
}
?>

<div class="modal fade modal_padrao" id="modal_nova_solicitacao_cancelamento" tabindex="-1" aria-labelledby="modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal_padrao_cinza">
                <h5 class="modal-title" id="modalLabel">Solicitar Cancelamento</h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>


            <div class="modal-body">
                <form action="router/web.php?r=SolicCanc" method="POST" class="form_solicitacao needs-validation"
                    novalidate>
                    <input type="hidden" name="acao" value="solicitar_cancelamento">
                    <div class="form_margem mb-3">
                        <label for="solcanc_tipo" class="form-label">Tipo de Cancelamento <span
                                class="text-danger">*</span></label>
                        <div class="label_info label_info_verde mt-0">Ao selecionar "Solicitação", todas as reservas
                            vinculadas à solicitação serão canceladas automaticamente.
                            Ao selecionar "Reserva", o cancelamento será aplicado somente à reserva selecionada.</div>
                        <select class="form-select" id="solcanc_tipo" name="solcanc_tipo" required>
                            <option value=""></option>
                            <option value="Solicitacao">Solicitação</option>
                            <option value="Reserva">Reserva</option>
                        </select>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                    </div>

                    <div class="mb-3 campo_alvo" id="solic_container" style="display:none;">
                        <label for="solcanc_id_solicitacao" class="form-label">Selecione a Solicitação <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="solcanc_id_solicitacao" required>
                            <option value=""></option>
                            <?php foreach ($solicitacoes_ativas as $solic) {
                                $componente_detalhe = $solic['compc_componente'] ?: $solic['solic_nome_comp_ativ'] ?: $solic['solic_nome_atividade'];
                                $texto = "{$solic['solic_codigo']} - {$componente_detalhe} ({$solic['solic_nome_prof_resp']})";
                                echo '<option value="' . htmlspecialchars($solic['solic_id']) . '">' . htmlspecialchars($texto) . '</option>';
                            } ?>
                        </select>

                        <script>
                            $(document).ready(function () {
                                $('#solcanc_id_solicitacao').select2({
                                    placeholder: "Selecione uma solicitação",
                                    allowClear: true,
                                    width: '100%',
                                    dropdownParent: $('#modal_nova_solicitacao_cancelamento') // 👈 substitua pelo ID do modal
                                });
                            });
                        </script>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                    </div>
                    <div class="mb-3 campo_alvo" id="reserva_container" style="display:none;">
                        <label for="solcanc_id_reserva" class="form-label">Selecione a Reserva <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="solcanc_id_reserva" required>
                            <option value=""></option>
                            <?php foreach ($reservas_ativas as $res) {
                                // Formatação
                                $data_formatada = date('d/m/Y', strtotime($res['res_data']));
                                $hora_formatada = substr($res['res_hora_inicio'], 0, 5);
                                // Usa o novo ALIAS 'nome_componente'
                                $texto = "{$res['res_codigo']} - {$res['nome_componente']} ({$data_formatada} {$hora_formatada})";
                                echo '<option value="' . htmlspecialchars($res['res_id']) . '">' . htmlspecialchars($texto) . '</option>';
                            } ?>
                        </select>


                        <script>
                            $(document).ready(function () {
                                $('#solcanc_id_reserva').select2({
                                    placeholder: "Selecione uma reserva",
                                    allowClear: true,
                                    width: '100%',
                                    dropdownParent: $('#modal_nova_solicitacao_cancelamento') // 👈 substitua pelo ID do modal
                                });
                            });
                        </script>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                    </div>

                    <input type="hidden" name="solcanc_id_alvo" id="solcanc_id_alvo_final">

                    <div class="mb-3">
                        <label for="solcanc_motivo" class="form-label">Motivo do Cancelamento <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="solcanc_motivo" name="solcanc_motivo" rows="4"
                            required></textarea>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                    </div>


                    <div class="col-lg-12">
                        <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                            <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span>
                                Campo obrigatório
                            </p>

                            <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal"
                                data-bs-toggle="button">Cancelar</button>

                            <button type="submit" class="btn botao botao_verde waves-effect">
                                Solicitar Cancelamento
                            </button>



                        </div>
                    </div>

            </div>

            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        const $tipoSelect = $('#solcanc_tipo');
        const $solicSelect = $('#solcanc_id_solicitacao');
        const $reservaSelect = $('#solcanc_id_reserva');
        const $solicContainer = $('#solic_container');
        const $reservaContainer = $('#reserva_container');
        const $inputAlvoFinal = $('#solcanc_id_alvo_final');
        const $form = $('.form_solicitacao'); // Seleciona o seu formulário

        // 1. Lógica de Visibilidade e Requisito (Ajustada)
        $tipoSelect.on('change', function () {
            const tipo = $(this).val();

            // Reseta containers e requisitos
            $('.campo_alvo').hide();
            $solicSelect.prop('required', false).removeClass('is-invalid');
            $reservaSelect.prop('required', false).removeClass('is-invalid');
            $inputAlvoFinal.val('');

            // Limpa a seleção do select que está sendo escondido
            $solicSelect.val('');
            $reservaSelect.val('');

            // Aplica requisitos ao campo visível
            if (tipo === 'Solicitacao') {
                $solicContainer.show();
                $solicSelect.prop('required', true);
            } else if (tipo === 'Reserva') {
                $reservaContainer.show();
                $reservaSelect.prop('required', true);
            }
        }).trigger('change');

        // 2. Select2 Initialization (ATUALIZADO PARA INCLUIR #solcanc_tipo)
        const initSelect2 = (selector, placeholderText) => {
            $(selector).select2({
                placeholder: placeholderText,
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_nova_solicitacao_cancelamento')
            });
        };
        // Inicializa todos os selects, incluindo o principal
        initSelect2('#solcanc_tipo', "Selecione o tipo de cancelamento");
        initSelect2('#solcanc_id_solicitacao', "Selecione uma solicitação");
        initSelect2('#solcanc_id_reserva', "Selecione uma reserva");

        // 3. Capturar o valor e remover classes de validação no change (MANTIDO)
        // 3. Capturar o valor e remover classes de validação no change (MANTIDO)
        const updateAlvoAndClearValidation = ($select) => {
            $inputAlvoFinal.val($select.val());
            $select.removeClass('is-invalid');
            // Remove a validação do elemento visual do Select2
            $select.next('.select2-container').find('.select2-selection--single').removeClass('is-invalid');
        };

        $tipoSelect.on('change', function () {
            // Chamada da lógica de visibilidade (já faz parte do trigger)
            updateAlvoAndClearValidation($(this));
        });
        $solicSelect.on('change', function () {
            updateAlvoAndClearValidation($(this));
        });

        $reservaSelect.on('change', function () {
            updateAlvoAndClearValidation($(this));
        });

        // 4. FORÇAR VALIDAÇÃO NO SUBMIT (MANTIDO)
        $form.on('submit', function (event) {
            let formValid = true;

            // 4a. Limpa classes de Select2 para evitar falsos positivos
            $('.select2-container--default .select2-selection--single').removeClass('is-invalid');

            // --- Verifica Select Principal (#solcanc_tipo) ---
            if ($tipoSelect.val() === "") {
                $tipoSelect.addClass('is-invalid');
                $tipoSelect.next('.select2-container').find('.select2-selection--single').addClass('is-invalid');
                formValid = false;
            } else {
                $tipoSelect.removeClass('is-invalid');
            }


            // 4b. Verifica o campo Condicional Visível (Solicitação ou Reserva)
            const campoAlvoSelect = $tipoSelect.val() === 'Solicitacao' ? $solicSelect : $reservaSelect;

            if (campoAlvoSelect.is(':visible') && campoAlvoSelect.prop('required') && (campoAlvoSelect.val() === null || campoAlvoSelect.val() === "")) {
                // Falha na validação: o campo obrigatório visível está vazio
                campoAlvoSelect.addClass('is-invalid');
                campoAlvoSelect.next('.select2-container').find('.select2-selection--single').addClass('is-invalid');
                formValid = false;
            }


            // 4c. Verifica os campos restantes e adiciona a classe 'was-validated'
            if (!formValid || !$form[0].checkValidity()) {
                event.preventDefault(); // Impede o envio
                event.stopPropagation();
                formValid = false;
            }

            $form.addClass('was-validated');

            return formValid;
        });
    });
</script>