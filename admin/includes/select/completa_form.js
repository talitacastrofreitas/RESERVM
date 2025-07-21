// BUSCA DADOS PARA CADASTRAR ADMINISTRADOR
$(document).ready(function () {
  $("#cad_admin_matricula").change(function () {
    var matricula = $(this).val();
    $.ajax({
      url: "includes/select/get_colaborador.php",
      method: "POST",
      data: {
        matricula: matricula,
      },
      dataType: "json",
      success: function (data) {
        $("#cad_admin_email").val(data.EMAIL);
      },
    });
  });
});

// BUSCA DADOS PARA CADASTRAR A RESERVA - CABULA
$(document).ready(function () {
  $("#cad_reserva_local_cabula").change(function () {
    var local = $(this).val();
    $.ajax({
      url: "includes/select/get_espaco.php",
      method: "POST",
      data: {
        local: local,
      },
      dataType: "json",
      success: function (data) {
        $("#cad_reserva_tipo_sala").val(data.esp_tipo_espaco);
        $("#cad_reserva_andar").val(data.esp_andar);
        $("#cad_reserva_pavilhao").val(data.esp_pavilhao);
        $("#esp_quant_maxima").val(data.esp_quant_maxima);
        $("#cad_reserva_camp_media").val(data.esp_quant_media);
        $("#esp_quant_minima").val(data.esp_quant_minima);
      },
    });
  });
});

// BUSCA DADOS PARA CADASTRAR A RESERVA - BROTAS
$(document).ready(function () {
  $("#cad_reserva_local_brotas").change(function () {
    var local = $(this).val();
    $.ajax({
      url: "includes/select/get_espaco.php",
      method: "POST",
      data: {
        local: local,
      },
      dataType: "json",
      success: function (data) {
        $("#cad_reserva_tipo_sala").val(data.esp_tipo_espaco);
        $("#cad_reserva_andar").val(data.esp_andar);
        $("#cad_reserva_pavilhao").val(data.esp_pavilhao);
        $("#esp_quant_maxima").val(data.esp_quant_maxima);
        $("#cad_reserva_camp_media").val(data.esp_quant_media);
        $("#esp_quant_minima").val(data.esp_quant_minima);
      },
    });
  });
});

// BUSCA DADOS PARA EDITAR A RESERVA
$(document).ready(function () {
  function preencherCamposEspaco(local) {
    $.ajax({
      url: "includes/select/get_espaco.php",
      method: "POST",
      data: { local: local },
      dataType: "json",
      success: function (data) {
        $("#edit_reserva_tipo_sala").val(data.esp_tipo_espaco);
        $("#edit_reserva_andar").val(data.esp_andar);
        $("#edit_reserva_pavilhao").val(data.esp_pavilhao);
        $("#edit_reserva_camp_maximo").val(data.esp_quant_maxima);
        $("#edit_reserva_camp_media").val(data.esp_quant_media);
        $("#edit_reserva_camp_minima").val(data.esp_quant_minima);
      },
      error: function () {
        console.error("Erro ao buscar os dados do espaço.");
      },
    });
  }

  // BROTAS
  $("#edit_reserva_local_cabula").change(function () {
    const local = $(this).val();
    if (local) preencherCamposEspaco(local);
  });

  // CABULA
  $("#edit_reserva_local_brotas").change(function () {
    const local = $(this).val();
    if (local) preencherCamposEspaco(local);
  });
});

// CADASTRO CURSOS
$(document).ready(function () {
  $("#cad_curs_matricula_prof").change(function () {
    var matricula = $(this).val();

    if (!matricula) {
      // Se o valor foi apagado, limpa o campo de e-mail
      $("#cad_curs_email_prof").val("");
      return; // Encerra aqui, sem fazer AJAX
    }

    $.ajax({
      url: "includes/select/get_colaborador.php",
      method: "POST",
      data: {
        matricula: matricula,
      },
      dataType: "json",
      success: function (data) {
        $("#cad_curs_email_prof").val(data.EMAIL);
      },
    });
  });
});

// ATUALIZAR CURSOS
$(document).ready(function () {
  $("#edit_curs_matricula_prof").change(function () {
    var matricula = $(this).val();

    if (!matricula) {
      // Se o valor foi apagado, limpa o campo de e-mail
      $("#edit_curs_email_prof").val("");
      return; // Encerra aqui, sem fazer AJAX
    }

    $.ajax({
      url: "includes/select/get_colaborador.php",
      method: "POST",
      data: {
        matricula: matricula,
      },
      dataType: "json",
      success: function (data) {
        $("#edit_curs_email_prof").val(data.EMAIL);
      },
    });
  });
});

// BUSCA DADOS PARA CADASTRAR A OCORRÊNCIA
$(document).ready(function () {
  $("#cad_oco_res_codigo").change(function () {
    var res_id = $(this).val();
    $.ajax({
      url: "includes/select/get_reserva.php",
      method: "POST",
      data: {
        res_id: res_id,
      },
      dataType: "json",
      success: function (data) {
        $("#cad_oco_res_id").val(data.res_id);
        $("#cad_oco_res_hora_inicio").val(data.res_hora_inicio);
        $("#cad_oco_res_hora_fim").val(data.res_hora_fim);
      },
    });
  });
});

// BUSCA DADOS PARA ATUALIZAR A OCORRÊNCIA
$(document).ready(function () {
  $("#edit_oco_res_codigo").change(function () {
    var res_id = $(this).val();
    $.ajax({
      url: "includes/select/get_reserva.php",
      method: "POST",
      data: {
        res_id: res_id,
      },
      dataType: "json",
      success: function (data) {
        $("#edit_oco_res_id").val(data.res_id);
        $("#edit_oco_res_hora_inicio").val(data.res_hora_inicio);
        $("#edit_oco_res_hora_fim").val(data.res_hora_fim);
      },
    });
  });
});
// //
// $(document).ready(function () {
//   $("#cad_user_matricula").change(function () {
//     var matricula = $(this).val();
//     $.ajax({
//       url: "includes/select/get_usuarios.php",
//       method: "POST",
//       data: {
//         matricula: matricula,
//       },
//       dataType: "json",
//       success: function (data) {
//         $("#cad_nome_completo").val(data.Nome);
//         $("#cad_user_email").val(data.Email);
//         $("#cad_user_contato").val(data.Telefone);
//       },
//     });
//   });
// });
// //
// $(document).ready(function () {
//   $("#edit_sel_aluno").change(function () {
//     var matricula = $(this).val();
//     $.ajax({
//       url: "includes/select/get_usuarios.php",
//       method: "POST",
//       data: {
//         matricula: matricula,
//       },
//       dataType: "json",
//       success: function (data) {
//         $("#edit_nome_completo").val(data.Nome);
//         $("#edit_user_email").val(data.Email);
//         $("#edit_user_contato").val(data.Telefone);
//       },
//     });
//   });
// });
// //
// // $(document).ready(function () {
// //   $("#cad_user_matricula").change(function () {
// //     var matricula = $(this).val();
// //     $.ajax({
// //       url: "includes/select/get_colaborador.php",
// //       method: "POST",
// //       data: {
// //         matricula: matricula,
// //       },
// //       dataType: "json",
// //       success: function (data) {
// //         $("#cad_user_nome").val(data.NOMESOCIAL);
// //         $("#cad_user_email").val(data.EMAIL);
// //         $("#cad_user_contato").val(data.TELEFONE1);
// //       },
// //     });
// //   });
// // });
// //
// $(document).ready(function () {
//   $("#edit_user_matricula").change(function () {
//     var matricula = $(this).val();
//     $.ajax({
//       url: "includes/select/get_colaborador.php",
//       method: "POST",
//       data: {
//         matricula: matricula,
//       },
//       dataType: "json",
//       success: function (data) {
//         $("#edit_user_nome").val(data.NOMESOCIAL);
//         $("#edit_user_email").val(data.EMAIL);
//         $("#edit_user_contato").val(data.TELEFONE1);
//       },
//     });
//   });
// });
// // NTI - BUSCA DADOS PARA CADASTRAR USUÁRIO
// $(document).ready(function () {
//   $("#cad_user_nti_matricula").change(function () {
//     var matricula = $(this).val();
//     $.ajax({
//       url: "includes/select/get_colaborador.php",
//       method: "POST",
//       data: {
//         matricula: matricula,
//       },
//       dataType: "json",
//       success: function (data) {
//         $("#cad_user_nome").val(data.NOMESOCIAL);
//         $("#cad_user_email").val(data.EMAIL);
//         $("#cad_user_contato").val(data.TELEFONE1);
//       },
//     });
//   });
// });
// // NTI - BUSCA DADOS PARA EDITAR USUÁRIO
// $(document).ready(function () {
//   $("#edit_user_nti_matricula").change(function () {
//     var matricula = $(this).val();
//     $.ajax({
//       url: "includes/select/get_colaborador.php",
//       method: "POST",
//       data: {
//         matricula: matricula,
//       },
//       dataType: "json",
//       success: function (data) {
//         $("#edit_user_nome").val(data.NOMESOCIAL);
//         $("#edit_user_email").val(data.EMAIL);
//         $("#edit_user_contato").val(data.TELEFONE1);
//       },
//     });
//   });
// });
// // BUSCA DADOS PARA CADASTRAR USUÁRIO
// $(document).ready(function () {
//   $("#user_matricula").change(function () {
//     var matricula = $(this).val();
//     $.ajax({
//       url: "includes/select/get_colaborador.php",
//       method: "POST",
//       data: {
//         matricula: matricula,
//       },
//       dataType: "json",
//       success: function (data) {
//         $("#cad_user_nome").val(data.NOMESOCIAL);
//         $("#cad_user_email").val(data.EMAIL);
//         $("#cad_user_contato").val(data.TELEFONE1);
//       },
//     });
//   });
// });
// // BUSCA DADOS PARA EDITAR USUÁRIO
// $(document).ready(function () {
//   $("#edit_user_matricula").change(function () {
//     var matricula = $(this).val();
//     $.ajax({
//       url: "includes/select/get_colaborador.php",
//       method: "POST",
//       data: {
//         matricula: matricula,
//       },
//       dataType: "json",
//       success: function (data) {
//         $("#edit_user_nome").val(data.NOMESOCIAL);
//         $("#edit_user_email").val(data.EMAIL);
//         $("#edit_user_contato").val(data.TELEFONE1);
//       },
//     });
//   });
// });
