document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const rememberMeCheckbox = document.getElementById("rememberMe");

  // Função para verificar se os dados de login devem ser lembrados
  function rememberLogin() {
    if (rememberMeCheckbox.checked) {
      const matricula = document.getElementById("matricula").value;
      const senha = document.getElementById("senha").value;

      // Salvar os dados de login no armazenamento local do navegador
      localStorage.setItem("rememberedUsername", matricula);
      localStorage.setItem("rememberedPassword", senha);
      localStorage.setItem("rememberMeChecked", "true");
    } else {
      // Limpar os dados salvos, caso o usuário não queira ser lembrado
      localStorage.removeItem("rememberedUsername");
      localStorage.removeItem("rememberedPassword");
      localStorage.removeItem("rememberMeChecked");
    }
  }

  // Verificar se os dados de login devem ser lembrados ao enviar o formulário
  loginForm.addEventListener("submit", function (event) {
    rememberLogin();
    // Outras ações de login aqui...
  });

  // Preencher os campos de login se o usuário optou por lembrar
  const rememberedUsername = localStorage.getItem("rememberedUsername");
  const rememberedPassword = localStorage.getItem("rememberedPassword");
  const rememberMeChecked = localStorage.getItem("rememberMeChecked");

  if (
    rememberedUsername &&
    rememberedPassword &&
    rememberMeChecked === "true"
  ) {
    document.getElementById("matricula").value = rememberedUsername;
    document.getElementById("senha").value = rememberedPassword;
    rememberMeCheckbox.checked = true;
  }
});
