// USE UM ÚNICO jQuery.noConflict()
var SA = jQuery.noConflict();

// =========================================================================
// FUNÇÃO AUXILIAR: LÓGICA PARA PRÉ-SELECIONAR VALORES E PREENCHER EMAILS NA EDIÇÃO
// ATRIBUÍDA A window. para ser global
// =========================================================================
window.preloadEditCoordinators = function (curs_id_to_load) {
 console.log("preloadEditCoordinators chamado para curs_id:", curs_id_to_load);
 SA.ajax({
  url: "controller/get_course_coordinators.php", // Caminho ajustado
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
   console.log(
    "Coordenadores para pré-selecionar:",
    selectedCoordinatorsForPreload
   );

      // AQUI É O PONTO CRÍTICO: Garantir que o Select2 esteja pronto
      // antes de tentar setar o valor.
      // Agora, esta função será chamada DEPOIS que o Select2 já foi inicializado no 'shown.bs.modal'
   SA("#edit_curs_matricula_prof")
    .val(selectedCoordinatorsForPreload)
    .trigger("change");
  },
  error: function (xhr, status, error) {
   console.error(
    "Erro ao buscar os coordenadores do curso para pré-seleção:",
    error
   );
  },
 });
};

// =========================================================================
// FUNÇÃO AUXILIAR: LÓGICA PARA PREENCHER E-MAIL(S) BASEADO NA SELEÇÃO DE COLABORADORES
// ATRIBUÍDA A window. para estar disponível globalmente
// =========================================================================
window.updateCoordinatorsEmailField = function (
 selectElement,
 emailFieldElement
) {
 var selectedValues = selectElement.val();
 console.log("Valores selecionados no select:", selectedValues);

 if (selectedValues && selectedValues.length > 0) {
  var matriculas = [];
  SA.each(selectedValues, function (index, value) {
   matriculas.push(value.split(" - ")[0]);
  });
  console.log("Matrículas extraídas para envio ao PHP:", matriculas);

  SA.ajax({
   url: "includes/select/get_colaborador.php", // Caminho ajustado
   method: "POST",
   data: {
    matriculas: matriculas,
   },
   dataType: "json",
   success: function (data) {
    console.log("Resposta AJAX para e-mails:", data);
    if (data && data.length > 0) {
     var allEmails = data.join(", ");
     emailFieldElement.val(allEmails);
     console.log("Emails concatenados e definidos no campo:", allEmails);
    } else {
     emailFieldElement.val("");
     console.log("Nenhum email retornado ou campo vazio.");
    }
   },
   error: function (xhr, status, error) {
    console.error(
     "Erro na chamada AJAX para buscar e-mails:",
     status,
     error,
     xhr.responseText
    );
    emailFieldElement.val("");
   },
  });
 } else {
  emailFieldElement.val("");
  console.log("Nenhum coordenador selecionado, campo de e-mail limpo.");
 }
};

// =========================================================================
// INÍCIO DO SA(document).ready() - Lógicas que dependem do DOM estar pronto
// =========================================================================
SA(document).ready(function () {
 // Carrega a lista completa de colaboradores na inicialização da página
 SA.ajax({
  url: "includes/select/get_colaborador.php", // Caminho ajustado
  type: "GET", // Requisição GET para buscar todos
  dataType: "json",
  success: function (data) {
    window.colaboradoresData = data; // Armazena globalmente
    console.log("Dados de colaboradores carregados globalmente:", window.colaboradoresData);
  },
  error: function (xhr, status, error) {
    console.error(
      "Erro ao carregar a lista completa de colaboradores:",
      error
    );
  },
});
 // =========================================================================
 // ACIONADORES DOS EVENTOS CHANGE PARA PREENCHIMENTO DE E-MAIL(S)
 // = ESTES CHAMAM AS FUNÇÕES GLOBAIS updateCoordinatorsEmailField
 // =========================================================================

 SA("#cad_curs_matricula_prof").on("change", function () {
  console.log("Evento change disparado para #cad_curs_matricula_prof");
  window.updateCoordinatorsEmailField(SA(this), SA("#cad_curs_email_prof"));
 });

 SA("#edit_curs_matricula_prof").on("change", function () {
  console.log("Evento change disparado para #edit_curs_matricula_prof");
  window.updateCoordinatorsEmailField(SA(this), SA("#edit_curs_email_prof"));
 });

 // =========================================================================
 // LÓGICA DO MODAL DE EDIÇÃO (MOVIDA DE cursos.php)
 // = Esta lógica deve ser executada quando o DOM estiver pronto e as funções globais definidas.
 // =========================================================================
 const modal_edit_curso = document.getElementById("modal_edit_curso");
 if (modal_edit_curso) {
  // Alterado de 'show.bs.modal' para 'shown.bs.modal'
  SA(modal_edit_curso).on("shown.bs.modal", (event) => {
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

      // Sempre destrua e re-inicialize o Select2 para garantir um estado limpo
      if (SA("#edit_curs_matricula_prof").data("select2")) {
          SA("#edit_curs_matricula_prof").select2("destroy");
      }
      SA("#edit_curs_matricula_prof").empty(); // Limpa opções antigas
      SA("#edit_curs_matricula_prof").append('<option value=""></option>'); // Adiciona opção vazia

      if (window.colaboradoresData && window.colaboradoresData.length > 0) {
          SA.each(window.colaboradoresData, function (key, value) {
              if (value.CHAPA && value.NOMESOCIAL) {
                  SA("#edit_curs_matricula_prof").append(
                      '<option value="' +
                          value.CHAPA +
                          " - " +
                          value.NOMESOCIAL +
                          '">' +
                          value.CHAPA +
                          " - " +
                          value.NOMESOCIAL +
                          "</option>"
                  );
              }
          });
      } else {
          console.warn(
              "window.colaboradoresData não disponível para popular #edit_curs_matricula_prof."
          );
      }

      // Inicializa Select2
      SA("#edit_curs_matricula_prof").select2({
          placeholder: "Selecione um(s) Coordenador(es)",
          allowClear: true,
          width: "100%",
          tags: false,
      });
      console.log(
          "Select2 inicializado e populado para #edit_curs_matricula_prof no modal."
      );


   // Chama a função global para pré-selecionar e preencher os e-mails, AGORA QUE O Select2 ESTÁ PRONTO.
   if (typeof window.preloadEditCoordinators === "function") {
    window.preloadEditCoordinators(curs_id);
   } else {
    console.error(
     "Função preloadEditCoordinators não encontrada (fallback)."
    );
   }
  });
 }



 // =========================================================================
    // NOVA LÓGICA PARA O MODAL DE CADASTRO DE ADMINISTRADOR
    // =========================================================================
    const modal_cad_admin_element = document.getElementById("modal_cad_admin");
    if (modal_cad_admin_element) {
        SA(modal_cad_admin_element).on("shown.bs.modal", function () {
            console.log("Modal de cadastro de Administrador aberto. Inicializando Select2 para #cad_admin_matricula.");
            try {
                if (SA("#cad_admin_matricula").data("select2")) {
                    SA("#cad_admin_matricula").select2("destroy");
                }
                SA("#cad_admin_matricula").empty();
                SA("#cad_admin_matricula").append('<option selected disabled value=""></option>');

                if (window.colaboradoresData) {
                    SA.each(window.colaboradoresData, function (key, value) {
                        if (value.CHAPA && value.NOMESOCIAL) {
                            // AQUI: O valor da opção é a string "CHAPA - NOMESOCIAL"
                            SA("#cad_admin_matricula").append(
                                '<option value="' +
                                value.CHAPA + " - " + value.NOMESOCIAL + // <--- AQUI ESTÁ A MUDANÇA CRÍTICA
                                '">' +
                                value.CHAPA +
                                " - " +
                                value.NOMESOCIAL +
                                "</option>"
                            );
                        }
                    });
                } else {
                    console.warn("window.colaboradoresData não disponível para popular #cad_admin_matricula.");
                }

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
                console.log("Select2 inicializado e populado para #cad_admin_matricula no modal.");
            } catch (e) {
                console.error("Erro ao inicializar Select2 em #cad_admin_matricula:", e);
            }
        });

        // Lida com o evento change para #cad_admin_matricula para popular o campo de e-mail e o nome oculto
        SA('#cad_admin_matricula').on('change', function() {
            var selectedValue = SA(this).val(); // selectedValue será "CHAPA - NOMESOCIAL"
            console.log("Evento change disparado para #cad_admin_matricula. Valor selecionado:", selectedValue);

            // Limpa o campo de nome oculto e e-mail por padrão
            SA('#cad_admin_email').val(''); // Não temos o campo oculto 'cad_admin_nome_oculto' no HTML
            // Removendo a linha do campo oculto, pois o HTML do admin.php não o possui.
            // A informação do nome já vai no campo admin_matricula_nome para o controller.

            if (selectedValue) {
                // Extrai a matrícula da string "CHAPA - NOMESOCIAL"
                var matricula = selectedValue.split(" - ")[0];
                console.log("Matrícula extraída para AJAX de e-mail:", matricula);

                // Chama a função global para preencher o e-mail
                // updateCoordinatorsEmailField espera o elemento select e o elemento email
                // e ele próprio faz a requisição AJAX para get_colaborador.php com as matriculas
                // SA(this) é o elemento select, SA("#cad_admin_email") é o campo de email
                window.updateCoordinatorsEmailField(SA(this), SA("#cad_admin_email"));
            } else {
                console.log("Nenhum valor selecionado, campo de e-mail limpo.");
            }
        });
    }


 // =========================================================================
 // LÓGICA DO MODAL DE CADASTRO (SIMILAR AO DE EDIÇÃO)
 // =========================================================================
 const modal_cad_curso = document.getElementById("modal_cad_curso");
 if (modal_cad_curso) {
  SA(modal_cad_curso).on("shown.bs.modal", function () {
   console.log(
    "Modal de cadastro de curso aberto. Inicializando Select2 para #cad_curs_matricula_prof."
   );
   try {
          // Sempre destrua e re-inicialize o Select2 para garantir um estado limpo
          if (SA("#cad_curs_matricula_prof").data("select2")) {
              SA("#cad_curs_matricula_prof").select2("destroy");
          }
          SA("#cad_curs_matricula_prof").empty(); // Limpa opções antigas
          SA("#cad_curs_matricula_prof").append('<option value=""></option>'); // Adiciona opção vazia

     if (window.colaboradoresData) {
      // Popula se os dados já foram carregados
      SA.each(window.colaboradoresData, function (key, value) {
       if (value.CHAPA && value.NOMESOCIAL) {
        SA("#cad_curs_matricula_prof").append(
         '<option value="' +
          value.CHAPA +
          " - " +
          value.NOMESOCIAL +
          '">' +
          value.CHAPA +
          " - " +
          value.NOMESOCIAL +
          "</option>"
        );
       }
      });
     }
     SA("#cad_curs_matricula_prof").select2({
      placeholder: "Selecione um(s) Coordenador(es)",
      allowClear: true,
      width: "100%",
      tags: false,
     });
     console.log(
      "Select2 inicializado e populado para #cad_curs_matricula_prof no modal."
     );
   } catch (e) {
    console.error(
     "Erro ao inicializar Select2 em #cad_curs_matricula_prof:",
     e
    );
   }
  });
 }
 // =========================================================================
}); // Fim do SA(document).ready(function () {