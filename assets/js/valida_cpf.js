function validarCPF(cpf) {
  cpf = cpf.replace(/[^\d]+/g, ""); // Remove caracteres não numéricos

  if (cpf.length !== 11 || /^(.)\1+$/.test(cpf)) return false;

  let sum = 0;
  for (let i = 0; i < 9; i++) sum += parseInt(cpf.charAt(i)) * (10 - i);
  let rest = (sum * 10) % 11;

  if (rest === 10 || rest === 11) rest = 0;
  if (rest !== parseInt(cpf.charAt(9))) return false;

  sum = 0;
  for (let i = 0; i < 10; i++) sum += parseInt(cpf.charAt(i)) * (11 - i);
  rest = (sum * 10) % 11;

  if (rest === 10 || rest === 11) rest = 0;
  if (rest !== parseInt(cpf.charAt(10))) return false;

  return true;
}

// Função para exibir SweetAlert2
function mostrarAlertaCadEmp() {
  Swal.fire({
    icon: "error",
    title: "CPF Inválido",
    text: "Por favor, digite um CPF válido.",
    confirmButtonText: "Ok",
    confirmButtonColor: "#8652A6",
  });
  document.getElementById("idCpf").value = ""; // Limpar o input
}

// Adiciona um listener para o evento 'blur' (quando o input perde o foco)
document.getElementById("idCpf").addEventListener("blur", function () {
  const cpf = this.value;
  if (cpf && !validarCPF(cpf)) mostrarAlertaCadEmp();
});

// CADASTRAR CREDENCIAMENTO
function valCPFInsc(c) {
  //Para retirar os '.' e o '-', para fazer as verificações
  c = c.replace(/\.|-/g, "");

  if (c == "") return false;
  // Elimina CPFs invalidos conhecidos
  if (
    c.length != 11 ||
    c == "00000000000" ||
    c == "11111111111" ||
    c == "22222222222" ||
    c == "33333333333" ||
    c == "44444444444" ||
    c == "55555555555" ||
    c == "66666666666" ||
    c == "77777777777" ||
    c == "88888888888" ||
    c == "99999999999"
  ) {
    Swal.fire({
      icon: "error",
      title: "CPF Inválido",
      text: "Por favor, digite um CPF válido.",
      confirmButtonText: "Ok",
      confirmButtonColor: "#8652A6",
    });
    c = true;
    document.getElementById("idCpfInscCad").value = "";
    return false;
  }

  var i;
  s = c;
  var c = s.substr(0, 9);
  var dv = s.substr(9, 2);
  var d1 = 0;
  var v = false;

  for (i = 0; i < 9; i++) {
    d1 += c.charAt(i) * (10 - i);
  }
  if (d1 == 0) {
    // alert("CPF Inválido")
    Swal.fire({
      icon: "error",
      title: "CPF Inválido",
      text: "Por favor, digite um CPF válido.",
      confirmButtonText: "Ok",
      confirmButtonColor: "#8652A6",
    });
    v = true;
    document.getElementById("idCpfInscCad").value = "";
    return false;
  }
  d1 = 11 - (d1 % 11);
  if (d1 > 9) d1 = 0;
  if (dv.charAt(0) != d1) {
    // alert("CPF Inválido")
    Swal.fire({
      icon: "error",
      title: "CPF Inválido",
      text: "Por favor, digite um CPF válido.",
      confirmButtonText: "Ok",
      confirmButtonColor: "#8652A6",
    });
    v = true;
    document.getElementById("idCpfInscCad").value = "";
    return false;
  }

  d1 *= 2;
  for (i = 0; i < 9; i++) {
    d1 += c.charAt(i) * (11 - i);
  }
  d1 = 11 - (d1 % 11);
  if (d1 > 9) d1 = 0;
  if (dv.charAt(1) != d1) {
    // alert("CPF Inválido")
    Swal.fire({
      icon: "error",
      title: "CPF Inválido",
      text: "Por favor, digite um CPF válido.",
      confirmButtonText: "Ok",
      confirmButtonColor: "#8652A6",
    });
    v = true;
    document.getElementById("idCpfInscCad").value = "";
    return false;
  }
  // if (!v) {
  //   alert(c + "nCPF Válido")
  // }
}

// EDITAR CREDENCIAMENTO
function valCPFInscEdit(c) {
  //Para retirar os '.' e o '-', para fazer as verificações
  c = c.replace(/\.|-/g, "");

  if (c == "") return false;
  // Elimina CPFs invalidos conhecidos
  if (
    c.length != 11 ||
    c == "00000000000" ||
    c == "11111111111" ||
    c == "22222222222" ||
    c == "33333333333" ||
    c == "44444444444" ||
    c == "55555555555" ||
    c == "66666666666" ||
    c == "77777777777" ||
    c == "88888888888" ||
    c == "99999999999"
  ) {
    Swal.fire({
      icon: "error",
      title: "CPF Inválido",
      text: "Por favor, digite um CPF válido.",
      confirmButtonText: "Ok",
      confirmButtonColor: "#8652A6",
    });
    c = true;
    document.getElementById("idCpfInscEdit").value = "";
    return false;
  }

  var i;
  s = c;
  var c = s.substr(0, 9);
  var dv = s.substr(9, 2);
  var d1 = 0;
  var v = false;

  for (i = 0; i < 9; i++) {
    d1 += c.charAt(i) * (10 - i);
  }
  if (d1 == 0) {
    // alert("CPF Inválido")
    Swal.fire({
      icon: "error",
      title: "CPF Inválido",
      text: "Por favor, digite um CPF válido.",
      confirmButtonText: "Ok",
      confirmButtonColor: "#8652A6",
    });
    v = true;
    document.getElementById("idCpfInscEdit").value = "";
    return false;
  }
  d1 = 11 - (d1 % 11);
  if (d1 > 9) d1 = 0;
  if (dv.charAt(0) != d1) {
    // alert("CPF Inválido")
    Swal.fire({
      icon: "error",
      title: "CPF Inválido",
      text: "Por favor, digite um CPF válido.",
      confirmButtonText: "Ok",
      confirmButtonColor: "#8652A6",
    });
    v = true;
    document.getElementById("idCpfInscEdit").value = "";
    return false;
  }

  d1 *= 2;
  for (i = 0; i < 9; i++) {
    d1 += c.charAt(i) * (11 - i);
  }
  d1 = 11 - (d1 % 11);
  if (d1 > 9) d1 = 0;
  if (dv.charAt(1) != d1) {
    // alert("CPF Inválido")
    Swal.fire({
      icon: "error",
      title: "CPF Inválido",
      text: "Por favor, digite um CPF válido.",
      confirmButtonText: "Ok",
      confirmButtonColor: "#8652A6",
    });
    v = true;
    document.getElementById("idCpfInscEdit").value = "";
    return false;
  }
  // if (!v) {
  //   alert(c + "nCPF Válido")
  // }
}
