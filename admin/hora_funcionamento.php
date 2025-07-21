<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!empty($global_admin_id) && $global_admin_perfil != 1) {
  header("Location: sair.php");
}
?>
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-md-flex align-items-center justify-content-between">
      <h4 class="mb-md-0">Horários de Funcionamento</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Horários de Funcionamento</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-md-6 text-md-start text-center">
            <h5 class="card-title mb-0">Lista de Horários de Funcionamento</h5>
          </div>
        </div>
      </div>

      <style>
        .table tbody th,
        .table tbody td {
          border-color: var(--branco) !important;
        }
      </style>

      <div class="card-body p-0">
        <table id="tab_hora_func" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>Dia da Semana</th>
              <th>Turno</th>
              <th>Horário Inicial</th>
              <th>Horário Final</th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>
            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM conf_hora_funcionamento
                                      INNER JOIN conf_dias_semana ON conf_dias_semana.week_id = conf_hora_funcionamento.chf_dia
                                      INNER JOIN conf_turno ON conf_turno.cturn_id = conf_hora_funcionamento.chf_turno
                                      ORDER BY chf_dia");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                if ($chf_dia == 1) {
                  $color_table = 'bg_info_vermelho';
                }
                if ($chf_dia == 2) {
                  $color_table = 'bg_info_laranja';
                }
                if ($chf_dia == 3) {
                  $color_table = 'bg_info_verde';
                }
                if ($chf_dia == 4) {
                  $color_table = 'bg_info_azul';
                }
                if ($chf_dia == 5) {
                  $color_table = 'bg_info_roxo';
                }
                if ($chf_dia == 6) {
                  $color_table = 'bg_info_rosa';
                }
                if ($chf_dia == 7) {
                  $color_table = 'bg_info_cinza';
                }
            ?>

                <tr>
                  <th class="<?= $color_table ?>">
                    <nobr><?= htmlspecialchars($week_dias) ?></nobr>
                  </th>
                  <td class="<?= $color_table ?>"><?= htmlspecialchars($cturn_turno) ?></td>
                  <td class="<?= $color_table ?>"><?= htmlspecialchars($chf_hora_inicio ? date('H:i', strtotime($chf_hora_inicio)) : '') ?></td>
                  <td class="<?= $color_table ?>"><?= htmlspecialchars($chf_hora_fim ? date('H:i', strtotime($chf_hora_fim)) : '') ?></td>
                  <td class="<?= $color_table ?> text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_hora_funcionamento"
                            data-bs-chf_id="<?= htmlspecialchars($chf_id) ?>"
                            data-bs-chf_dia="<?= htmlspecialchars($chf_dia) ?>"
                            data-bs-chf_turno="<?= htmlspecialchars($chf_turno) ?>"
                            data-bs-chf_hora_inicio="<?= htmlspecialchars($chf_hora_inicio ? date('H:i', strtotime($chf_hora_inicio)) : '') ?>"
                            data-bs-chf_hora_fim="<?= htmlspecialchars($chf_hora_fim ? date('H:i', strtotime($chf_hora_fim)) : '') ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>

            <?php }
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar os dados";
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>



<div class="modal fade modal_padrao" id="modal_edit_hora_funcionamento" tabindex="-1" aria-labelledby="modal_edit_hora_funcionamento" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_hora_funcionamento">Editar Horário de Funcionamento</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=HoraFunc" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control chf_id" name="chf_id" required>
            <input type="hidden" name="acao" value="atualizar">

            <div class="col-md-6">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM conf_dias_semana ORDER BY week_id");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Dia da Semana</label>
              <select class="form-select text-uppercase chf_dia" disabled>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['week_id'] ?>"><?= $res['week_dias'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM conf_turno ORDER BY cturn_id");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div>
                <label class="form-label">Turno</label>
                <select class="form-select text-uppercase chf_turno" disabled>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['cturn_id'] ?>"><?= $res['cturn_turno'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Horário Início</label>
              <input type="time" class="form-control hora chf_hora_inicio" name="chf_hora_inicio" id="cad_chf_hora_inicio">
            </div>

            <script>
              flatpickr("#cad_chf_hora_inicio", {
                enableTime: true, // ativa o seletor de hora
                noCalendar: true, // oculta o calendário
                dateFormat: "H:i", // formato 24h: horas:minutos
                time_24hr: true, // garante o formato 24h
                allowInput: true // permite apagar e digitar manualmente
              });
            </script>

            <div class="col-md-6">
              <label class="form-label">Horário Final</label>
              <input type="time" class="form-control hora chf_hora_fim" name="chf_hora_fim" id="cad_chf_hora_fim">
            </div>

            <script>
              flatpickr("#cad_chf_hora_fim", {
                enableTime: true, // ativa o seletor de hora
                noCalendar: true, // oculta o calendário
                dateFormat: "H:i", // formato 24h: horas:minutos
                time_24hr: true, // garante o formato 24h
                allowInput: true // permite apagar e digitar manualmente
              });
            </script>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Atualizar</button>
              </div>
            </div>

          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const modal_edit_hora_funcionamento = document.getElementById('modal_edit_hora_funcionamento')
  if (modal_edit_hora_funcionamento) {
    modal_edit_hora_funcionamento.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const chf_id = button.getAttribute('data-bs-chf_id')
      const chf_dia = button.getAttribute('data-bs-chf_dia')
      const chf_turno = button.getAttribute('data-bs-chf_turno')
      const chf_hora_inicio = button.getAttribute('data-bs-chf_hora_inicio')
      const chf_hora_fim = button.getAttribute('data-bs-chf_hora_fim')
      //const cpp_status = button.getAttribute('data-bs-cpp_status')
      // 
      const modalTitle = modal_edit_hora_funcionamento.querySelector('.modal-title')
      const modal_chf_id = modal_edit_hora_funcionamento.querySelector('.chf_id')
      const modal_chf_dia = modal_edit_hora_funcionamento.querySelector('.chf_dia')
      const modal_chf_turno = modal_edit_hora_funcionamento.querySelector('.chf_turno')
      const modal_chf_hora_inicio = modal_edit_hora_funcionamento.querySelector('.chf_hora_inicio')
      const modal_chf_hora_fim = modal_edit_hora_funcionamento.querySelector('.chf_hora_fim')
      //const modal_cpp_status = modal_edit_hora_funcionamento.querySelector('.cpp_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_chf_id.value = chf_id
      modal_chf_dia.value = chf_dia
      modal_chf_turno.value = chf_turno
      modal_chf_hora_inicio.value = chf_hora_inicio
      modal_chf_hora_fim.value = chf_hora_fim
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      // if (cpp_status === '1') {
      //   modal_cpp_status.checked = true;
      // } else {
      //   modal_cpp_status.checked = false;
      // }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>