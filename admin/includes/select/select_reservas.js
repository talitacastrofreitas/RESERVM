// CADASTRAR OCORRÊNCIA
var SA = jQuery.noConflict();
SA(document).ready(function () {
  SA.ajax({
    url: "controller/select_reservas.php",
    type: "GET",
    dataType: "json",
    success: function (data) {
      SA.each(data, function (key, value) {
        SA("#cad_oco_res_codigo").append(
          '<option value="' +
            value.res_codigo +
            '">' +
            value.res_codigo +
            " - " +
            value.res_data +
            "</option>",
        );
      });
    },
  });
});

// ATUALIZAR CURSO
// var SA = jQuery.noConflict();
// SA(document).ready(function () {
//   SA.ajax({
//     url: "controller/select_colaborador.php",
//     type: "GET",
//     dataType: "json",
//     success: function (data) {
//       SA.each(data, function (key, value) {
//         SA("#edit_curs_matricula_prof").append(
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
