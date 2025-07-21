// BUSCA OS DADOS DA TABELA USANDO AJAX
var SA = jQuery.noConflict();
SA(document).ready(function () {
  SA.ajax({
    url: "controller/select_colaborador.php",
    type: "GET",
    dataType: "json",
    success: function (data) {
      SA.each(data, function (key, value) {
        SA("#cad_admin_matricula").append(
          '<option value="' +
            value.CHAPA +
            " - " +
            value.NOMESOCIAL +
            '">' +
            value.CHAPA +
            " - " +
            value.NOMESOCIAL +
            "</option>",
        );
      });
    },
  });
});

// CADASTRAR CURSO
var SA = jQuery.noConflict();
SA(document).ready(function () {
  SA.ajax({
    url: "controller/select_colaborador.php",
    type: "GET",
    dataType: "json",
    success: function (data) {
      SA.each(data, function (key, value) {
        SA("#cad_curs_matricula_prof").append(
          '<option value="' +
            value.CHAPA +
            " - " +
            value.NOMESOCIAL +
            '">' +
            value.CHAPA +
            " - " +
            value.NOMESOCIAL +
            "</option>",
        );
      });
    },
  });
});

// ATUALIZAR CURSO
var SA = jQuery.noConflict();
SA(document).ready(function () {
  SA.ajax({
    url: "controller/select_colaborador.php",
    type: "GET",
    dataType: "json",
    success: function (data) {
      SA.each(data, function (key, value) {
        SA("#edit_curs_matricula_prof").append(
          '<option value="' +
            value.CHAPA +
            " - " +
            value.NOMESOCIAL +
            '">' +
            value.CHAPA +
            " - " +
            value.NOMESOCIAL +
            "</option>",
        );
      });
    },
  });
});


// SA(document).ready(function () {
//   SA.ajax({
//     url: "controller/select_colaborador.php",
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       SA.each(data, function (key, value) {
//         SA("#edit_admin_matricula").append(
//           '<option value="' +
//             value.CHAPA +
//             " - " +
//             value.NOMESOCIAL +
//             '">' +
//             value.CHAPA +
//             " - " +
//             value.NOMESOCIAL +
//             "</option>",
//         );
//       });
//     },
//   });
// });

//
// BUSCA OS DADOS DA TABELA USANDO AJAX
// var SA = jQuery.noConflict();
// SA(document).ready(function () {
//   SA.ajax({
//     url: "controller/select_alunos.php", // Script PHP para recuperar os dados
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       // Atualizar o select com os dados retornados
//       SA.each(data, function (key, value) {
//         SA("#cad_user_matricula").append(
//           '<option value="' +
//             value.Matricula +
//             '">' +
//             value.Matricula +
//             " - " +
//             value.Nome +
//             "</option>",
//         );
//       });
//     },
//   });
// });
// //
// SA(document).ready(function () {
//   SA.ajax({
//     url: "controller/select_colaborador.php", // Script PHP para recuperar os dados
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       // Atualizar o select com os dados retornados
//       SA.each(data, function (key, value) {
//         SA("#cad_col_matricula").append(
//           '<option value="' +
//             value.CHAPA +
//             '">' +
//             value.CHAPA +
//             " - " +
//             value.NOMESOCIAL +
//             "</option>",
//         );
//       });
//     },
//   });
// });
// // NTI - BUSCA OS DADOS PARA O CADASTRO DOS USUÁRIOS
// var SA = jQuery.noConflict();
// SA(document).ready(function () {
//   SA.ajax({
//     url: "controller/select_colaborador.php", // Script PHP para recuperar os dados
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       // Atualizar o select com os dados retornados
//       SA.each(data, function (key, value) {
//         SA("#cad_user_nti_matricula").append(
//           '<option value="' +
//             value.CHAPA +
//             '">' +
//             value.CHAPA +
//             " - " +
//             value.NOMESOCIAL +
//             "</option>",
//         );
//       });
//     },
//   });
// });
// // NTI - BUSCA OS DADOS PARA A EDIÇÃO DOS USUÁRIOS
// var SA = jQuery.noConflict();
// SA(document).ready(function () {
//   SA.ajax({
//     url: "controller/select_colaborador.php", // Script PHP para recuperar os dados
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       // Atualizar o select com os dados retornados
//       SA.each(data, function (key, value) {
//         SA("#edit_user_nti_matricula").append(
//           '<option value="' +
//             value.CHAPA +
//             '">' +
//             value.CHAPA +
//             " - " +
//             value.NOMESOCIAL +
//             "</option>",
//         );
//       });
//     },
//   });
// });
// // BUSCA OS DADOS PARA O CADASTRO DOS USUÁRIOS
// var SA = jQuery.noConflict();
// SA(document).ready(function () {
//   SA.ajax({
//     url: "controller/select_colaborador.php", // Script PHP para recuperar os dados
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       // Atualizar o select com os dados retornados
//       SA.each(data, function (key, value) {
//         SA("#user_matricula").append(
//           '<option value="' +
//             value.CHAPA +
//             '">' +
//             value.CHAPA +
//             " - " +
//             value.NOMESOCIAL +
//             "</option>",
//         );
//       });
//     },
//   });
// });
// // BUSCA OS DADOS PARA A EDIÇÃO DOS USUÁRIOS
// var SA = jQuery.noConflict();
// SA(document).ready(function () {
//   SA.ajax({
//     url: "controller/select_colaborador.php", // Script PHP para recuperar os dados
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       // Atualizar o select com os dados retornados
//       SA.each(data, function (key, value) {
//         SA("#edit_user_matricula").append(
//           '<option value="' +
//             value.CHAPA +
//             '">' +
//             value.CHAPA +
//             " - " +
//             value.NOMESOCIAL +
//             "</option>",
//         );
//       });
//     },
//   });
// });
