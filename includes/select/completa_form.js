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
        $("#cad_admin_nome").val(data.NOMESOCIAL);
        $("#cad_admin_email").val(data.EMAIL);
      },
    });
  });
});

// BUSCA DADOS PARA EDITAR ADMINISTRADOR
// $(document).ready(function () {
//   $("#edit_admin_matricula").change(function () {
//     var matricula = $(this).val();
//     $.ajax({
//       url: "includes/select/get_colaborador.php",
//       method: "POST",
//       data: {
//         matricula: matricula,
//       },
//       dataType: "json",
//       success: function (data) {
//         $("#edit_admin_email").val(data.EMAIL);
//       },
//     });
//   });
// });
// $(document).ready(function () {
//   $("#edit_sel_colaborador").change(function () {
//     var matricula = $(this).val();
//     $.ajax({
//       url: "includes/select/get_colaborador.php",
//       method: "POST",
//       data: {
//         matricula: matricula,
//       },
//       dataType: "json",
//       success: function (data) {
//         $("#edit_nome_completo").val(data.NOMESOCIAL);
//         $("#edit_user_email").val(data.EMAIL);
//         $("#edit_user_contato").val(data.TELEFONE1);
//       },
//     });
//   });
// });
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
