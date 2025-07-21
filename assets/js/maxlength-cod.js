document.addEventListener("DOMContentLoaded", function() {
  const codigoInputs = document.querySelectorAll(".cod_jump");

  codigoInputs.forEach(function(input, index) {
    input.addEventListener("input", function() {
      if (input.value.length === 1 && index < codigoInputs.length - 1) {
        codigoInputs[index + 1].focus();
      }
    });
  });

  document.getElementById("codigoForm").addEventListener("paste", function(e) {
    e.preventDefault();
    const pasteData = e.clipboardData.getData("text/plain");
    const codes = pasteData.trim().split("");

    codes.forEach(function(code, index) {
      if (index < codigoInputs.length) {
        codigoInputs[index].value = code;
      }
    });
  });
});