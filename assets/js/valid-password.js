var myInput = document.getElementById("pwd");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var numb = document.getElementById("numb");
var special = document.getElementById("special");
var length = document.getElementById("length");

myInput.onkeyup = function () {
  var lowerCaseLetters = /[a-z]/g;
  if (myInput.value.match(lowerCaseLetters)) {
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");
  }

  var upperCaseLetters = /[A-Z]/g;
  if (myInput.value.match(upperCaseLetters)) {
    capital.classList.remove("invalid");
    capital.classList.add("valid");
  } else {
    capital.classList.remove("valid");
    capital.classList.add("invalid");
  }

  var upperCasenumb = /[0-9]/g;
  if (myInput.value.match(upperCasenumb)) {
    numb.classList.remove("invalid");
    numb.classList.add("valid");
  } else {
    numb.classList.remove("valid");
    numb.classList.add("invalid");
  }

  var specialCharacters = /[!@#$%^&*]/g;
  if (myInput.value.match(specialCharacters)) {
    special.classList.remove("invalid");
    special.classList.add("valid");
  } else {
    special.classList.remove("valid");
    special.classList.add("invalid");
  }

  if (myInput.value.length >= 8) {
    length.classList.remove("invalid");
    length.classList.add("valid");
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
  }
};

// VALIDA SENHA NO REGISTRO DO USUÁRIO
var form = document.getElementById("UserRegistro");
const submitButton = form.querySelector('button[type="submit"]');
form.addEventListener("submit", function (evt) {
  if (
    (letter.classList.contains("invalid") ||
      capital.classList.contains("invalid") ||
      numb.classList.contains("invalid") ||
      special.classList.contains("invalid") ||
      length.classList.contains("invalid")) &&
    myInput.value != ""
  ) {
    evt.preventDefault();
    //alert("A senha não atende a todos os critérios!");
    document.getElementById("criterios").style.display = "block";
  } else {
    document.getElementById("criterios").style.display = "none";

    if (myInput.value != document.getElementById("cpwd").value) {
      evt.preventDefault();
      //alert("As senhas não correspondem!");
      document.getElementById("senha").style.display = "block";
    } else {
      document.getElementById("senha").style.display = "none";

      // submitButton.innerHTML =
      //   '<span class="spinner-border" role="status" aria-hidden="true"></span> Aguarde...';
      // submitButton.disabled = true;
    }
  }
});
