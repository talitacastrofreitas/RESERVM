///////////////////////////////////
// VALIDAÇÃO PADRÃO DO BOOTSTRAP //
///////////////////////////////////
(() => {
  "use strict";

  const forms = document.querySelectorAll(".needs-validation");

  Array.from(forms).forEach((form) => {
    form.addEventListener(
      "submit",
      (event) => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();

          // ENCONTRA O PRIMEIRO CAMPO INVÁLIDO PARA QUE SEJA PREENCHIDO
          const firstInvalidField = form.querySelector(":invalid");

          if (firstInvalidField) {
            firstInvalidField.scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
            firstInvalidField.focus(); // Foca no campo inválido
          }
        }

        form.classList.add("was-validated");
      },
      false,
    );
  });
})();

/////////////////////////////////////////
// VALIDAÇÃO PADRÃO COM BOTÃO PROGRESS //
/////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("ValidaBotaoProgressPadrao");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", function (event) {
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();

      // ENCONTRA O PRIMEIRO CAMPO INVÁLIDO PARA QUE SEJA PREENCHIDO
      const firstInvalidField = form.querySelector(":invalid");

      if (firstInvalidField) {
        firstInvalidField.scrollIntoView({
          behavior: "smooth",
          block: "center",
        });
        firstInvalidField.focus(); // Foca no campo inválido
      }
    }

    form.classList.add("was-validated");

    if (form.checkValidity() === true) {
      submitButton.innerHTML = '<span class="spinner-border" role="status" aria-hidden="true"></span> Aguarde...';
      submitButton.disabled = true;
    }
  });
});

/////////////////////////////////////////////
// VALIDAÇÃO CHECKBOXES DA SOLICITAÇÃO COM BOTÃO PROGRESS //
/////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("BotaoProgressCheck");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", function (event) {
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();

      // ENCONTRA O PRIMEIRO CAMPO INVÁLIDO PARA QUE SEJA PREENCHIDO
      const firstInvalidField = form.querySelector(":invalid");

      if (firstInvalidField) {
        firstInvalidField.scrollIntoView({
          behavior: "smooth",
          block: "center",
        });
        firstInvalidField.focus(); // Foca no campo inválido
      }
    }

    // VALIDA OS CHECKBOXES
    //
    // VALIDA CHECKBOXES ESPAÇO SUGERIDO - BROTAS (INFORMAÇÕES DA RESERVA - PRÁTICA)
    var checkboxInfoPraticEspacoBrotas = document.querySelectorAll(
      'input[name="cad_info_pratic_espaco_brotas[]"]',
    );
    var checkedcheckboxInfoPraticEspacoBrotas = false;

    checkboxInfoPraticEspacoBrotas.forEach(function (checkbox) {
      if (checkbox.checked) {
        checkedcheckboxInfoPraticEspacoBrotas = true;
      }
    });

    // VALIDA CHECKBOXES ESPAÇO SUGERIDO - CABULA (INFORMAÇÕES DA RESERVA - PRÁTICA)
    var checkboxInfoPraticEspacoCabula = document.querySelectorAll(
      'input[name="cad_info_pratic_espaco_cabula[]"]',
    );
    var checkedcheckboxInfoPraticEspacoCabula = false;

    checkboxInfoPraticEspacoCabula.forEach(function (checkbox) {
      if (checkbox.checked) {
        checkedcheckboxInfoPraticEspacoCabula = true;
      }
    });

    if (!checkedcheckboxInfoPraticEspacoBrotas) {
      var msgCheckInfoPraticEspacoBrotas = document.getElementById(
        "msgCheckInfoPraticEspacoBrotas",
      );
      msgCheckInfoPraticEspacoBrotas.style.display = "block";

      event.preventDefault(); // IMPEDE ENVIO DO FORMULÁRIO

      // APAGA A MENSAGEM DE ERRO QUANDO UM CKECKBOX É MARCADO
      // var checkboxInfoPraticEspacoBrotas = document.querySelectorAll('input[name="cad_info_pratic_espaco_brotas[]"]');
      checkboxInfoPraticEspacoBrotas.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
          // var msgCheckInfoPraticEspacoBrotas = document.getElementById("msgCheckInfoPraticEspacoBrotas");

          // VERIFICA NOVAMENTE SE PELO MENOS UM CHECKBOX ESTÁ MARCADO
          var checked = false;
          checkboxInfoPraticEspacoBrotas.forEach(function (brotas) {
            if (brotas.checked) {
              checked = true;
            }
          });

          if (checked) {
            msgCheckInfoPraticEspacoBrotas.style.display = "none";
          } else {
            msgCheckInfoPraticEspacoBrotas.style.display = "block";
          }
        });
      });
    } else {
      form.classList.add("was-validated");

      // ACIONA O BOTÃO PROGRESS
      if (form.checkValidity() === true) {
        submitButton.innerHTML =
          '<span class="spinner-border" role="status" aria-hidden="true"></span> Aguarde...';
        submitButton.disabled = true;
      }
      //
    }

    if (!checkedcheckboxInfoPraticEspacoCabula) {
      var msgCheckInfoPraticEspacoCabula = document.getElementById(
        "msgCheckInfoPraticEspacoCabula",
      );
      msgCheckInfoPraticEspacoCabula.style.display = "block";

      event.preventDefault(); // IMPEDE ENVIO DO FORMULÁRIO

      // APAGA A MENSAGEM DE ERRO QUANDO UM CKECKBOX É MARCADO
      checkboxInfoPraticEspacoCabula.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
          // VERIFICA NOVAMENTE SE PELO MENOS UM CHECKBOX ESTÁ MARCADO
          var checked = false;
          checkboxInfoPraticEspacoCabula.forEach(function (cabula) {
            if (cabula.checked) {
              checked = true;
            }
          });

          if (checked) {
            msgCheckInfoPraticEspacoCabula.style.display = "none";
          } else {
            msgCheckInfoPraticEspacoCabula.style.display = "block";
          }
        });
      });
    } else {
      form.classList.add("was-validated");

      // ACIONA O BOTÃO PROGRESS
      if (form.checkValidity() === true) {
        submitButton.innerHTML =
          '<span class="spinner-border" role="status" aria-hidden="true"></span> Aguarde...';
        submitButton.disabled = true;
      }
      //
    }
  });
});

//////////////////////////////////
// VALIDAÇÃO COM BOTÃO PROGRESS //
//////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("ValidaBotaoProgress");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", function (event) {
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }

    form.classList.add("was-validated");

    if (form.checkValidity() === true) {
      submitButton.innerHTML =
        '<span class="spinner-border" role="status" aria-hidden="true"></span> Aguarde...';
      submitButton.disabled = true;
    }
  });
});

////////////////////////////////////////////////////////////
// VALIDAÇÃO COM BOTÃO PROGRESS DO MODAL DEFERIR PROPOSTA //
////////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("ValidaBotaoProgressDeferir");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", function (event) {
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }

    form.classList.add("was-validated");

    if (form.checkValidity() === true) {
      submitButton.innerHTML =
        '<span class="spinner-border" role="status" aria-hidden="true"></span> Aguarde...';
      submitButton.disabled = true;
    }
  });
});

//////////////////////////////////////////////////////////////
// VALIDAÇÃO COM BOTÃO PROGRESS DO MODAL INDEFERIR PROPOSTA //
//////////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("ValidaBotaoProgressIndeferir");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", function (event) {
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }

    form.classList.add("was-validated");

    if (form.checkValidity() === true) {
      submitButton.innerHTML =
        '<span class="spinner-border" role="status" aria-hidden="true"></span> Aguarde...';
      submitButton.disabled = true;
    }
  });
});

/////////////////////////////////////////////////////////////
// VALIDAÇÃO COM BOTÃO PROGRESS DO MODAL EXECUTAR PROPOSTA //
/////////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("ValidaBotaoProgressExecuta");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", function (event) {
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }

    form.classList.add("was-validated");

    if (form.checkValidity() === true) {
      submitButton.innerHTML =
        '<span class="spinner-border" role="status" aria-hidden="true"></span> Aguarde...';
      submitButton.disabled = true;
    }
  });
});

//////////////////////////////////////////////////////////////
// VALIDAÇÃO COM BOTÃO PROGRESS DO MODAL FINALIZAR PROPOSTA //
//////////////////////////////////////////////////////////////
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("ValidaBotaoProgressFinal");
  const submitButton = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", function (event) {
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }

    form.classList.add("was-validated");

    if (form.checkValidity() === true) {
      submitButton.innerHTML =
        '<span class="spinner-border" role="status" aria-hidden="true"></span> Aguarde...';
      submitButton.disabled = true;
    }
  });
});
