// document.addEventListener("DOMContentLoaded", function () {
//   var cancelaSolicitacaoModal = document.getElementById(
//     "modal_cancelar_solicitacao"
//   );
//   cancelaSolicitacaoModal.addEventListener("show.bs.modal", function (event) {
//     var button = event.relatedTarget;
//     var solicId = button.getAttribute("data-solic-id");
//     var actionUrl = button.getAttribute("data-action");

//     var modalSolicIdInput =
//       cancelaSolicitacaoModal.querySelector("#solic_id_cancelar");
//     modalSolicIdInput.value = solicId;

//     // Define a ação do formulário dinamicamente
//     var form = cancelaSolicitacaoModal.querySelector("form");
//     form.action = actionUrl;
//   });
// });

document.addEventListener("DOMContentLoaded", function () {
  var cancelaSolicitacaoModal = document.getElementById(
    "modal_cancelar_solicitacao"
  );
  cancelaSolicitacaoModal.addEventListener("show.bs.modal", function (event) {
    var button = event.relatedTarget;
    var solicId = button.getAttribute("data-solic-id");
    var actionUrl = button.getAttribute("data-action");

    // --- ADICIONE ESTA LINHA PARA DEPURAR ---
    console.log("Action URL capturada:", actionUrl);

    // Aqui está o ajuste: buscar pelo ID correto do campo
    var modalSolicIdInput =
      cancelaSolicitacaoModal.querySelector("#solic_id_cancelar");
    modalSolicIdInput.value = solicId;

    // Define a ação do formulário dinamicamente
    var form = cancelaSolicitacaoModal.querySelector("form");
    form.action = actionUrl;
  });
});
