$(document).ready(function () {
  var navListItems = $("div.setup-panel div a"),
    allWells = $(".setup-content"),
    allNextBtn = $(".nextBtn"),
    allPrevBtn = $(".prevBtn");

  allWells.hide();

  navListItems.click(function (e) {
    e.preventDefault();
    var $target = $($(this).attr("href")),
      $item = $(this);

    if (!$item.hasClass("disabled")) {
      navListItems.removeClass("botao_azul").addClass("btn-light");
      $item.addClass("botao_azul");
      allWells.hide();
      $target.show();
      $target.find("input:eq(0)").focus();
    }
  });

  allNextBtn.click(function () {
    var curStep = $(this).closest(".setup-content"),
      curStepBtn = curStep.attr("id"),
      nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]')
        .parent()
        .next()
        .children("a"),
      curInputs = curStep.find("input"),
      isValid = true;

    $(".form-control").removeClass("is-invalid");

    for (var i = 0; i < curInputs.length; i++) {
      if (!curInputs[i].validity.valid) {
        isValid = false;
        $(curInputs[i]).addClass("is-invalid");
      }
    }

    if (isValid) nextStepWizard.removeAttr("disabled").trigger("click");
  });

  allPrevBtn.click(function () {
    var curStep = $(this).closest(".setup-content"),
      curStepBtn = curStep.attr("id"),
      prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]')
        .parent()
        .prev()
        .children("a");

    prevStepWizard.removeAttr("disabled").trigger("click");
  });

  $("div.setup-panel div a.botao_azul").trigger("click");

  $("input").on("input", function () {
    if ($(this)[0].checkValidity()) {
      $(this).removeClass("is-invalid");
    }
  });

  $("#UserRegistro").submit(function (event) {
    var form = $(this)[0];
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }
    $(this).addClass("was-validated");
  });
});

// DESABILITA BOTAO SE SENHA E CHECKBOX NAO FOR PREENCHIDO
document.addEventListener("DOMContentLoaded", function () {
  const senha = document.getElementById("pwd");
  const termos = document.getElementById("check");
  const enviarBtn = document.getElementById("entrar");

  function validarFormulario() {
    if (senha.value.trim() !== "" && termos.checked) {
      enviarBtn.disabled = false;
    } else {
      enviarBtn.disabled = true;
    }
  }

  senha.addEventListener("input", validarFormulario);
  termos.addEventListener("change", validarFormulario);
});
