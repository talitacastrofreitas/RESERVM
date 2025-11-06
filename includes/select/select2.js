// // CADASTRA ADMINISTRADOR
// $("#cad_admin_matricula").select2({
//   dropdownParent: $("#modal_cad_admin"),
//   placeholder: "Selecionar usuário",
//   //allowClear: true,
//   language: {
//     noResults: function (params) {
//       return "Dados não encontrado";
//     },
//   },
// });
// // EDITA ADMINISTRADOR
// $("#edit_admin_matricula").select2({
//   dropdownParent: $("#modal_edit_admin"),
//   language: {
//     noResults: function (params) {
//       return "Dados não encontrado";
//     },
//   },
// });
// // USUÁRIO
// $(document).ready(function () {
//   $("#cad_user_nacionalidade").select2({
//     language: {
//       noResults: function (params) {
//         return "Nenhum resultado encontrado";
//       },
//     },
//   });
// });

// // METERIAL
// $("#cad_pmc_material_consumo").select2({
//   dropdownParent: $("#modal_cad_material"),
//   placeholder: "Selecionar Material",
//   //allowClear: true,
//   language: {
//     noResults: function (params) {
//       return "Nenhum resultado encontrado";
//     },
//   },
// });

// $("#edit_pmc_material_consumo").select2({
//   dropdownParent: $("#modal_edit_material"),
//   placeholder: "Selecionar Material",
//   //allowClear: true,
//   language: {
//     noResults: function (params) {
//       return "Nenhum resultado encontrado";
//     },
//   },
// });

// // SERVIÇOS
// $("#cad_ps_mat_serv_id").select2({
//   dropdownParent: $("#modal_cad_servico"),
//   placeholder: "Selecionar Serviço",
//   //allowClear: true,
//   language: {
//     noResults: function (params) {
//       return "Nenhum resultado encontrado";
//     },
//   },
// });

// $("#edit_ps_mat_serv_id").select2({
//   dropdownParent: $("#modal_edit_servico"),
//   placeholder: "Selecionar Serviço",
//   //allowClear: true,
//   language: {
//     noResults: function (params) {
//       return "Nenhum resultado encontrado";
//     },
//   },
// });

// // DISCIPLINA / MÓDULO
// $("#prop_cmod_tipo_docente_cad_sim").select2({
//   dropdownParent: $("#modal_cad_disciplina_modulo"),
//   placeholder: "Selecionar Docente",
//   //allowClear: true,
//   language: {
//     noResults: function (params) {
//       return "Nenhum resultado encontrado";
//     },
//   },
// });

// $("#prop_cmod_tipo_docente_edit_sim").select2({
//   dropdownParent: $("#modal_edit_disciplina_modulo"),
//   placeholder: "Selecionar Docente",
//   //allowClear: true,
//   language: {
//     noResults: function (params) {
//       return "Nenhum resultado encontrado";
//     },
//   },
// });

// // PROGRAMAS
// $(document).ready(function () {
//   $("#prop_prog_docente").select2({
//     language: {
//       noResults: function (params) {
//         return "Nenhum resultado encontrado";
//       },
//     },
//   });
// });

// // PARCERIAS - PAÍSES
// $(document).ready(function () {
//   $("#prop_parc_pais").select2({
//     language: {
//       noResults: function (params) {
//         return "Nenhum resultado encontrado";
//       },
//     },
//   });
// });

// COMPONENTE CURRICULAR
$("#cad_compc_curso").select2({
  dropdownParent: $("#modal_cad_componente_curricular"),
  // placeholder: "",
  //allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});
//
$("#edit_compc_curso").select2({
  dropdownParent: $("#modal_edit_componente_curricular"),
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// NOVA SOLICITAÇÃO - CURSOS
$(document).ready(function () {
  $("#cad_solic_curso").select2({
    language: {
      noResults: function (params) {
        return "Nenhum resultado encontrado";
      },
    },
  });
});

// NOVA SOLICITAÇÃO - COMPONENTE CURRICULAR
$(document).ready(function () {
  $("#cad_solic_comp_curric").select2({
    language: {
      noResults: function (params) {
        return "Nenhum resultado encontrado";
      },
    },
  });
});
