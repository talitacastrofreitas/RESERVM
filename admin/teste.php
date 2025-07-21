<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<form id="form-reserva" action="cadastro.php" method="POST">
  <input type="date" id="cad_data_reserva" name="res_data" required>
  <input type="time" id="cad_res_hora_inicio" name="res_hora_inicio" required>
  <input type="time" id="cad_res_hora_fim" name="res_hora_fim" required>
  <button type="submit" id="btn-submit">Cadastrar</button>
</form>

<script>
  const dataInput = document.getElementById("cad_data_reserva");
  const inicioInput = document.getElementById("cad_res_hora_inicio");
  const fimInput = document.getElementById("cad_res_hora_fim");
  const btnSubmit = document.getElementById("btn-submit");

  // function verificarConflito() {
  //   const data = dataInput.value;
  //   const inicio = inicioInput.value;
  //   const fim = fimInput.value;

  //   // Só verifica se os três campos estiverem preenchidos
  //   if (data && inicio && fim) {
  //     fetch(`verificar_conflito.php?data=${data}&hora_inicio=${inicio}&hora_fim=${fim}`)
  //       .then(response => response.json())
  //       .then(res => {
  //         if (res.conflito) {
  //           Swal.fire({
  //             icon: 'error',
  //             title: 'Conflito de Horário!',
  //             text: 'Já existe uma reserva nesse intervalo!',
  //           });
  //           btnSubmit.disabled = true;
  //         } else {
  //           btnSubmit.disabled = false;
  //         }
  //       });
  //   }
  // }



  function verificarConflito() {
    const data = dataInput.value;
    const inicio = inicioInput.value;
    const fim = fimInput.value;

    if (data && inicio && fim) {
      fetch(`verificar_conflito.php?data=${data}&hora_inicio=${inicio}&hora_fim=${fim}`)
        .then(response => response.json())
        .then(res => {
          if (res.conflito) {
            Swal.fire({
              icon: 'warning',
              title: 'Horário em Conflito',
              text: 'Já existe uma reserva nesse intervalo. Deseja continuar?',
              showCancelButton: false,
              confirmButtonText: 'OK',
            });
            // 👉 NÃO desativa o botão
          }
        });
    }
  }


  // Detecta mudanças nos campos
  dataInput.addEventListener("change", verificarConflito);
  inicioInput.addEventListener("change", verificarConflito);
  fimInput.addEventListener("change", verificarConflito);
</script>