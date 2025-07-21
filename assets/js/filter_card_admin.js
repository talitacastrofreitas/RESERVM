document
  .getElementById("formBusca")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    var inputBusca = document.getElementById("inputBusca").value.toLowerCase();
    var cards = document.querySelectorAll(".filter_card");
    var selectCat = document.getElementById("inputCat").value.toLowerCase();
    var inputStatus = document
      .getElementById("inputStatus")
      .value.toLowerCase();
    var resultCount = 0;

    cards.forEach(function (card) {
      var cardCat = card.dataset.categoria.toLowerCase();
      var cardCod = card.dataset.codigo.toLowerCase();
      var cardTit = card.dataset.titulo.toLowerCase();
      var cardProp = card.dataset.proponente.toLowerCase();
      var cardDat = card.dataset.data_upd.toLowerCase();
      var cardSta = card.dataset.status.toLowerCase();

      if (
        (cardCod.includes(inputBusca) ||
          cardTit.includes(inputBusca) ||
          cardProp.includes(inputBusca) ||
          cardDat.includes(inputBusca)) &&
        (selectCat === "all" || cardCat === selectCat) &&
        (inputStatus === "all" || cardSta === inputStatus)
      ) {
        card.style.display = "block";
        resultCount++;
      } else {
        card.style.display = "none";
      }
    });

    document.getElementById("resultCount").textContent = resultCount;
  });
