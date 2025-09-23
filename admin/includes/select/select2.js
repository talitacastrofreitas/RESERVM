// CADASTRA ADMINISTRADOR
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


// EDITA ADMINISTRADOR
// $("#edit_admin_matricula").select2({
//   dropdownParent: $("#modal_edit_admin"),
//   language: {
//     noResults: function (params) {
//       return "Dados não encontrado";
//     },
//   },
// });

$("#cad_user_matricula").select2({
  dropdownParent: $("#cad_emprestimo"),
  language: {
    noResults: function (params) {
      return "Usuário não encontrado";
    },
  },
});
//
$("#espaco_id").select2({
  language: {
    noResults: function (params) {
      return "Usuário não encontrado";
    },
  },
});

///
$("#cad_col_matricula").select2({
  dropdownParent: $("#cad_emprestimo"),
  language: {
    noResults: function (params) {
      return "Colaborador não encontrado";
    },
  },
});
//
$(document).ready(function () {
  $("#edit_emprestimo").on("shown.bs.modal", function () {
    $("#edit_sel_aluno").select2({
      dropdownParent: $("#edit_emprestimo"),
      language: {
        noResults: function (params) {
          return "Usuário não encontrado";
        },
      },
    });
  });
});
//
$(document).ready(function () {
  $("#edit_emprestimo").on("shown.bs.modal", function () {
    $("#edit_sel_colaborador").select2({
      dropdownParent: $("#edit_emprestimo"),
      language: {
        noResults: function (params) {
          return "Usuário não encontrado";
        },
      },
    });
  });
});

// $(document).ready(function () {
//   $("#modal_edit_admin").on("shown.bs.modal", function () {
//     $(".admin_matricula_nome").select2({
//       dropdownParent: $("#modal_edit_admin"),
//       language: {
//         noResults: function (params) {
//           return "Dados não encontrado";
//         },
//       },
//     });
//   });
// });

// CADASTRA NTI USUÁRIOS
$("#cad_user_nti_matricula").select2({
  dropdownParent: $("#cad_usuario"),
  language: {
    noResults: function (params) {
      return "Usuário não encontrado";
    },
  },
});
// EDITAR NTI USUÁRIOS
$("#edit_user_nti_matricula").select2({
  dropdownParent: $("#edit_usuario"),
  language: {
    noResults: function (params) {
      return "Usuário não encontrado";
    },
  },
});

// CADASTRA COMPONENTE CURRICULAR
$("#cad_c_curso").select2({
  dropdownParent: $("#modal_cad_componente_curricular"),
  placeholder: "Selecionar Curso",
  //allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// EDITAR COMPONENTE CURRICULAR
$("#edit_conf_curso").select2({
  dropdownParent: $("#modal_edit_area_conhecimento"),
  placeholder: "Selecionar Curso",
  //allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// CADASTRA RESERVA - CAMPO LOCAL - BROTAS
$("#cad_reserva_local_brotas").select2({
  dropdownParent: $("#modal_cad_espaco"),
  // allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// CADASTRA RESERVA - CAMPO LOCAL - CABULA
$("#cad_reserva_local_cabula").select2({
  dropdownParent: $("#modal_cad_espaco"),
  // allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// EDITAR RESERVA - CAMPO LOCAL - BROTAS
$("#edit_reserva_local_brotas").select2({
  dropdownParent: $("#modal_edit_espaco"),
  // allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// EDITAR RESERVA - CAMPO LOCAL - CABULA
$("#edit_reserva_local_cabula").select2({
  dropdownParent: $("#modal_edit_espaco"),
  // allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// CADASTRA CURSOS
$("#cad_curs_matricula_prof").select2({
  dropdownParent: $("#modal_cad_curso"),
  placeholder: "Selecionar professor(a)",
  allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// ATUALIZAR CURSOS
$("#edit_curs_matricula_prof").select2({
  dropdownParent: $("#modal_edit_curso"),
  placeholder: "Selecionar professor(a)",
  allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// CADASTRA OCORRÊNCIA
$("#cad_oco_res_codigo").select2({
  dropdownParent: $("#modal_cad_ocorrencia"),
  placeholder: "Selecionar reserva",
  //allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// ATUALIZAR OCORRÊNCIA
$("#edit_oco_res_codigo").select2({
  dropdownParent: $("#modal_edit_ocorrencia"),
  placeholder: "Selecionar reserva",
  //allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// ADMINISTRADOR - CADASTRO SOLICITAÇÃO - CURSO
$("#cad_solic_curso").select2({
  dropdownParent: $("#modal_cad_solicitacao"),
  // placeholder: "",
  //allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});

// ADMINISTRADOR - CADASTRO SOLICITAÇÃO - COMPONENTE
$("#cad_solic_comp_curric").select2({
  dropdownParent: $("#modal_cad_solicitacao"),
  // placeholder: "",
  //allowClear: true,
  language: {
    noResults: function (params) {
      return "Dados não encontrado";
    },
  },
});
