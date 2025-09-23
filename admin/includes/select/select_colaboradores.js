// USE UM ÚNICO jQuery.noConflict()
var SA = jQuery.noConflict();

// Variável global para armazenar todos os dados de colaboradores
window.colaboradoresData = null; // Inicializa como null

// DECLARE AS URLS AQUI, NO ESCOPO GLOBAL DO SCRIPT
// Caminhos relativos à página HTML (admin.php ou cursos.php) que está incluindo este script.
// Se admin.php está em 'admin/' e includes/select/ está em 'includes/select/' na raiz do projeto
var urlAllColaboradores = 'controller/select_colaborador.php';
// Se controller/ está em 'controller/' na raiz do projeto
var urlGetCourseCoordinators = 'controller/get_course_coordinators.php';


// =========================================================================
// FUNÇÃO AUXILIAR: LÓGICA PARA PRÉ-SELECIONAR VALORES E PREENCHER EMAILS NA EDIÇÃO (Cursos)
// =========================================================================
window.preloadEditCoordinators = function (curs_id_to_load) {
    console.log("preloadEditCoordinators chamado para curs_id:", curs_id_to_load);
    SA.ajax({
        url: urlGetCourseCoordinators, // Usa a variável global
        type: "GET",
        dataType: "json",
        data: { curs_id: curs_id_to_load },
        success: function (data) {
            const selectedCoordinatorsForPreload = [];
            SA.each(data, function (index, coordinator) {
                selectedCoordinatorsForPreload.push(
                    coordinator.CHAPA + " - " + coordinator.NOMESOCIAL
                );
            });
            console.log("Coordenadores para pré-selecionar no curso:", selectedCoordinatorsForPreload);
            SA("#edit_curs_matricula_prof").val(selectedCoordinatorsForPreload).trigger("change");
        },
        error: function (xhr, status, error) {
            console.error(
                "Erro ao buscar os coordenadores do curso para pré-seleção:",
                error, xhr.responseText
            );
        },
    });
};

// =========================================================================
// FUNÇÃO AUXILIAR: LÓGICA PARA PREENCHER E-MAIL BASEADO NA SELEÇÃO DE COLABORADORES
// =========================================================================
window.updateCoordinatorsEmailField = function (
    selectElement, // O elemento Select2 que foi alterado
    emailFieldElement // O campo de input de e-mail a ser preenchido
) {
    let selectedValues = selectElement.val(); // Retorna string (single) ou array (multiple)
    
    // Converte para array para processamento consistente, se for seleção simples
    if (selectedValues === null || selectedValues === "") { // Se nada selecionado ou campo limpo
        selectedValues = [];
    } else if (!Array.isArray(selectedValues)) { // Se é uma string (seleção única), transforme em array de 1 item
        selectedValues = [selectedValues];
    }

    console.log("Valores selecionados no select (para preenchimento de e-mail):", selectedValues);

    emailFieldElement.val(''); // Limpa o campo de e-mail por padrão

    if (selectedValues.length > 0 && window.colaboradoresData && window.colaboradoresData.length > 0) {
        let chapaArray = [];

        // Agora, iteramos sobre `selectedValues` (que é sempre um array aqui)
        SA.each(selectedValues, function (index, valueString) {
            // Cada `valueString` aqui é uma string "CHAPA - NOME"
            // O erro `selectedValue.split is not a function` foi corrigido aqui,
            // pois `valueString` sempre será uma string.
            chapaArray.push(valueString.split(" - ")[0]);
        });

        console.log("CHAPA(s) extraída(s) para buscar e-mail:", chapaArray);

        let emailsEncontrados = [];
        SA.each(chapaArray, function(index, chapa) {
            var foundColaborador = window.colaboradoresData.find(function(colab) {
                return colab.CHAPA === chapa;
            });

            if (foundColaborador && foundColaborador.EMAIL) {
                emailsEncontrados.push(foundColaborador.EMAIL);
            }
        });

        if (emailsEncontrados.length > 0) {
            emailFieldElement.val(emailsEncontrados.join(', ')); // Concatena todos os e-mails
            console.log("E-mail(s) preenchido(s) a partir dos dados em memória:", emailsEncontrados.join(', '));
        } else {
            console.log("Nenhum e-mail encontrado nos dados em memória para a(s) CHAPA(s) selecionada(s).");
        }
    } else {
        console.log("Nenhum valor selecionado no Select2 ou dados de colaboradores não disponíveis, e-mail limpo.");
    }
};


// =========================================================================
// INÍCIO DO SA(document).ready() - Lógicas que dependem do DOM estar pronto
// =========================================================================
SA(document).ready(function () {
    // A Promise de carregamento de dados dos colaboradores
    const loadColaboradoresDataPromise = new Promise((resolve, reject) => {
        console.log("Iniciando carregamento global de colaboradores de:", urlAllColaboradores);
        SA.ajax({
            url: urlAllColaboradores, // Usa a variável global
            type: "GET",
            dataType: "json",
            success: function (data) {
                window.colaboradoresData = data;
                console.log("Dados de colaboradores carregados globalmente:", window.colaboradoresData);
                resolve();
            },
            error: function (xhr, status, error) {
                console.error(
                    "ERRO GRAVE: Falha ao carregar a lista completa de colaboradores (GET). Verifique o caminho e o backend:",
                    error, xhr.responseText, "URL usada:", urlAllColaboradores
                );
                window.colaboradoresData = [];
                reject(error);
            },
        });
    });

    // =========================================================================
    // LÓGICA ESPECÍFICA PARA O MODAL DE CADASTRO DE ADMINISTRADOR
    // =========================================================================
    const modal_cad_admin_element = document.getElementById("modal_cad_admin");
    if (modal_cad_admin_element) {
        SA(modal_cad_admin_element).on("shown.bs.modal", function () {
            console.log("Modal de cadastro de Administrador aberto. Aguardando dados de colaboradores para popular o Select2...");
            loadColaboradoresDataPromise.then(() => {
                try {
                    // SE o Select2 já foi inicializado (ex: em uma abertura anterior do modal)
                    // DESTRUA-O ANTES DE REPOPULAR E RE-INICIALIZAR. Isso é vital para um estado limpo.
                    if (SA("#cad_admin_matricula").data("select2")) {
                        SA("#cad_admin_matricula").select2("destroy");
                        console.log("Select2 #cad_admin_matricula destruído para re-inicialização.");
                    }

                    SA("#cad_admin_matricula").empty();
                    SA("#cad_admin_matricula").append('<option selected disabled value=""></option>');

                    if (window.colaboradoresData && window.colaboradoresData.length > 0) {
                        SA.each(window.colaboradoresData, function (key, value) {
                            if (value.CHAPA && value.NOMESOCIAL) {
                                SA("#cad_admin_matricula").append(
                                    '<option value="' + value.CHAPA + " - " + value.NOMESOCIAL + '">' + value.CHAPA + " - " + value.NOMESOCIAL + "</option>"
                                );
                            }
                        });
                        console.log("Opções adicionadas ao #cad_admin_matricula. Total de colaboradores:", window.colaboradoresData.length);
                        SA("#cad_admin_matricula").val(null).trigger('change');

                    } else {
                        console.warn("window.colaboradoresData está vazio ou não disponível para popular #cad_admin_matricula. Verifique se há dados no backend.");
                    }

                    // INICIALIZA O SELECT2 AQUI, AGORA COM OS DADOS CARREGADOS
                    SA("#cad_admin_matricula").select2({
                        dropdownParent: SA("#modal_cad_admin"),
                        placeholder: "Selecionar usuário",
                        allowClear: true,
                        language: {
                            noResults: function () {
                                return "Dados não encontrado";
                            },
                        },
                    });
                    console.log("Select2 para #cad_admin_matricula inicializado E populado.");

                } catch (e) {
                    console.error("Erro ao popular Select2 em #cad_admin_matricula após carregamento de dados:", e);
                }
            }).catch(error => {
                console.error("Não foi possível popular #cad_admin_matricula porque o carregamento inicial de dados falhou.", error);
            });
        });

        SA('#cad_admin_matricula').on('change', function() {
            console.log("Evento 'change' disparado para #cad_admin_matricula.");
            window.updateCoordinatorsEmailField(SA(this), SA("#cad_admin_email"));
        });
    }

    // =========================================================================
    // LÓGICA PARA MODAIS DE CURSOS (CADASTRO E EDIÇÃO)
    // =========================================================================
    const modal_cad_curso = document.getElementById("modal_cad_curso");
    if (modal_cad_curso) {
        SA(modal_cad_curso).on("shown.bs.modal", function () {
            console.log("Modal de cadastro de curso aberto. Aguardando dados de colaboradores para #cad_curs_matricula_prof...");
            loadColaboradoresDataPromise.then(() => {
                try {
                    // Adicione aqui a lógica de destruir/re-inicializar o Select2 para cursos se for necessário
                    if (SA("#cad_curs_matricula_prof").data("select2")) {
                        SA("#cad_curs_matricula_prof").select2("destroy");
                        console.log("Select2 #cad_curs_matricula_prof destruído para re-inicialização.");
                    }

                    SA("#cad_curs_matricula_prof").empty();
                    SA("#cad_curs_matricula_prof").append('<option value=""></option>');

                    if (window.colaboradoresData) {
                        SA.each(window.colaboradoresData, function (key, value) {
                            if (value.CHAPA && value.NOMESOCIAL) {
                                SA("#cad_curs_matricula_prof").append(
                                    '<option value="' + value.CHAPA + " - " + value.NOMESOCIAL + '">' + value.CHAPA + " - " + value.NOMESOCIAL + "</option>"
                                );
                            }
                        });
                    }
                    // Inicializa o Select2 para cursos aqui
                    SA("#cad_curs_matricula_prof").select2({
                        dropdownParent: SA("#modal_cad_curso"),
                        placeholder: "Selecione um(s) Coordenador(es)",
                        allowClear: true,
                        width: "100%",
                        tags: false,
                    });
                    console.log("Select2 para #cad_curs_matricula_prof inicializado e populado.");
                } catch (e) {
                    console.error("Erro ao inicializar Select2 em #cad_curs_matricula_prof:", e);
                }
            }).catch(error => {
                console.error("Não foi possível popular #cad_curs_matricula_prof devido a erro no carregamento dos dados de colaboradores:", error);
            });
        });
        SA("#cad_curs_matricula_prof").on("change", function () {
            console.log("Evento change disparado para #cad_curs_matricula_prof");
            window.updateCoordinatorsEmailField(SA(this), SA("#cad_curs_email_prof"));
        });
    }

    const modal_edit_curso = document.getElementById("modal_edit_curso");
    if (modal_edit_curso) {
        SA(modal_edit_curso).on("shown.bs.modal", (event) => {
            console.log("Modal de edição de curso aberto. Aguardando dados de colaboradores para #edit_curs_matricula_prof...");
            loadColaboradoresDataPromise.then(() => {
                const button = event.relatedTarget;
                const curs_id = button.getAttribute("data-bs-curs_id");
                const curs_curso = button.getAttribute("data-bs-curs_curso");
                const curs_status = button.getAttribute("data-bs-curs_status");

                const modalTitle = modal_edit_curso.querySelector(".modal-title");
                const modal_curs_id = modal_edit_curso.querySelector(".curs_id");
                const modal_curs_curso = modal_edit_curso.querySelector(".curs_curso");
                const modal_curs_status = modal_edit_curso.querySelector(".curs_status");

                modalTitle.textContent = "Atualizar Dados";
                modal_curs_id.value = curs_id;
                modal_curs_curso.value = curs_curso;

                if (curs_status === "1") {
                    modal_curs_status.checked = true;
                } else {
                    modal_curs_status.checked = false;
                }

                // Adicione aqui a lógica de destruir/re-inicializar o Select2 para cursos se for necessário
                if (SA("#edit_curs_matricula_prof").data("select2")) {
                    SA("#edit_curs_matricula_prof").select2("destroy");
                    console.log("Select2 #edit_curs_matricula_prof destruído para re-inicialização.");
                }

                SA("#edit_curs_matricula_prof").empty();
                SA("#edit_curs_matricula_prof").append('<option value=""></option>');

                if (window.colaboradoresData && window.colaboradoresData.length > 0) {
                    SA.each(window.colaboradoresData, function (key, value) {
                        if (value.CHAPA && value.NOMESOCIAL) {
                            SA("#edit_curs_matricula_prof").append(
                                '<option value="' + value.CHAPA + " - " + value.NOMESOCIAL + '">' + value.CHAPA + " - " + value.NOMESOCIAL + "</option>"
                            );
                        }
                    });
                } else {
                    console.warn("window.colaboradoresData não disponível para popular #edit_curs_matricula_prof.");
                }

                // Inicializa o Select2 para cursos aqui
                SA("#edit_curs_matricula_prof").select2({
                    dropdownParent: SA("#modal_edit_curso"),
                    placeholder: "Selecione um(s) Coordenador(es)",
                    allowClear: true,
                    width: "100%",
                    tags: false,
                });
                console.log("Select2 para #edit_curs_matricula_prof inicializado e populado.");

                if (typeof window.preloadEditCoordinators === "function") {
                    window.preloadEditCoordinators(curs_id);
                } else {
                    console.error("Função preloadEditCoordinators não encontrada (fallback).");
                }
            }).catch(error => {
                console.error("Não foi possível popular #edit_curs_matricula_prof devido a erro no carregamento dos dados de colaboradores:", error);
            });
        });
        SA("#edit_curs_matricula_prof").on("change", function () {
            console.log("Evento change disparado para #edit_curs_matricula_prof");
            window.updateCoordinatorsEmailField(SA(this), SA("#edit_curs_email_prof"));
        });
    }

}); // Fim do SA(document).ready(function () {