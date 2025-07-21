<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!isset($_SESSION['reservm_admin_id']) || $_SESSION['reservm_admin_perfil'] != 1) {
  header("Location: sair.php");
  exit;
}
?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-md-flex align-items-center justify-content-between">
      <h4 class="mb-md-0">Datas Bloqueadas</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Datas Bloqueadas</li>
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
            <h5 class="card-title mb-0">Lista de Datas Bloqueadas</h5>
          </div>
          <div class="col-md-6 d-flex align-items-center d-flex justify-content-md-end justify-content-center">
            <button class="btn botao botao_amarelo waves-effect mt-3 mt-md-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_dia_bloqueado">+ Cadastrar Data Bloqueada</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_dias_bloqueados" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-2">Data</span></th>
              <th><span class="me-2">Mês</span></th>
              <th><span class="me-2">Ano</span></th>
              <th><span class="me-2">Dia da Semana</span></th>
              <th><span class="me-2">Motivo</span></th>
              <th><span class="me-2">Status</span></th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT dbloq_id, dbloq_data, dbloq_dia, dbloq_mes, dbloq_ano, dbloq_motivo, dbloq_status, dbloqm_motivo, week_id, week_dias, st_status FROM conf_dias_bloqueadas
                                      INNER JOIN conf_dias_semana ON conf_dias_semana.week_id = conf_dias_bloqueadas.dbloq_dia
                                      INNER JOIN conf_dias_bloqueadas_motivo ON conf_dias_bloqueadas_motivo.dbloqm_id = conf_dias_bloqueadas.dbloq_motivo
                                      INNER JOIN status ON status.st_id = conf_dias_bloqueadas.dbloq_status");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $dbloq_id      = $row['dbloq_id'];
                $dbloq_data    = $row['dbloq_data'];
                $dbloq_dia     = $row['dbloq_dia'];
                $dbloq_mes     = $row['dbloq_mes'];
                $dbloq_ano     = $row['dbloq_ano'];
                $dbloq_motivo  = $row['dbloq_motivo'];
                $dbloq_status  = $row['dbloq_status'];
                $dbloqm_motivo = $row['dbloqm_motivo'];
                $week_dias     = $row['week_dias'];
                $st_status     = $row['st_status'];

                //CONFIGURAÇÃO DO STATUS
                $status_color = ($dbloq_status == 1) ? 'bg_info_verde' : 'bg_info_cinza';
                $st_status = ($dbloq_status == 1) ? 'BLOQUEADO' : 'LIBERADO';
            ?>

                <tr>
                  <th><span class="hide_data"><?= date('Ymd', strtotime($dbloq_data)) ?></span><?= htmlspecialchars(date('d/m/Y', strtotime($dbloq_data))) ?></th>
                  <td class="text-uppercase"><?= htmlspecialchars($dbloq_mes) ?></td>
                  <td><?= htmlspecialchars($dbloq_ano) ?></td>
                  <td class="text-uppercase"><?= htmlspecialchars($week_dias) ?></td>
                  <td class="text-uppercase"><?= htmlspecialchars($dbloqm_motivo) ?></td>
                  <td><span class="badge <?= $status_color ?>"><?= htmlspecialchars($st_status) ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_dia_bloqueado"
                            data-bs-dbloq_id="<?= htmlspecialchars($dbloq_id) ?>"
                            data-bs-dbloq_data="<?= htmlspecialchars($dbloq_data) ?>"
                            data-bs-dbloq_dia="<?= htmlspecialchars($dbloq_dia) ?>"
                            data-bs-dbloq_dia_id="<?= htmlspecialchars($dbloq_dia) ?>"
                            data-bs-dbloq_mes="<?= htmlspecialchars($dbloq_mes) ?>"
                            data-bs-dbloq_ano="<?= htmlspecialchars($dbloq_ano) ?>"
                            data-bs-dbloq_motivo="<?= htmlspecialchars($dbloq_motivo) ?>"
                            data-bs-dbloq_status="<?= htmlspecialchars($dbloq_status) ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="../router/web.php?r=DataBloq&acao=deletar&dbloq_id=<?= $dbloq_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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




<!-- CADASTRAR -->
<div class="modal fade modal_padrao" id="modal_cad_dia_bloqueado" tabindex="-1" aria-labelledby="modal_cad_dia_bloqueado" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_dia_bloqueado">Cadastrar Data Bloqueada</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=DataBloq" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" name="acao" value="cadastrar">

            <div class="col-md-6">
              <div>
                <label class="form-label">Data <span>*</span></label>
                <input type="date" class="form-control flatpickr-input" name="dbloq_data" id="data" onchange="preencherCampos()" value="<?= htmlspecialchars($_SESSION['form_dbloq']['dbloq_data'] ?? '') ?>" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
              <script>
                flatpickr("#data", {
                  dateFormat: "Y-m-d", // formato do valor REAL do input (enviado na query)
                  altInput: true, // ativa um input visível com formato alternativo
                  altFormat: "d/m/Y", // formato exibido para o usuário
                  locale: "pt", // idioma português
                  allowInput: true,
                  onClose: function() {
                    // Validação automática quando o usuário seleciona uma data
                    const input = document.getElementById('data');
                    input.classList.toggle('is-invalid', !input.value);
                  }
                });

                // Validação integrada com o Bootstrap
                document.getElementById('data').addEventListener('input', function() {
                  this.classList.toggle('is-invalid', !this.value);
                });
              </script>
            </div>

            <div class="col-md-6">
              <div>
                <label class="form-label">Mês</label>
                <input type="text" class="form-control text-uppercase" name="dbloq_mes" id="mes" value="<?= htmlspecialchars($_SESSION['form_dbloq']['dbloq_mes'] ?? '') ?>" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <div>
                <label class="form-label">Ano</label>
                <input type="text" class="form-control text-uppercase" name="dbloq_ano" id="ano" value="<?= htmlspecialchars($_SESSION['form_dbloq']['dbloq_ano'] ?? '') ?>" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <?php try {
                $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div>
                <label class="form-label">Dia da semana</label>
                <input type="hidden" class="form-control text-uppercase" name="dbloq_dia" id="diaSemanaId" value="<?= htmlspecialchars($_SESSION['form_dbloq']['dbloq_dia'] ?? '') ?>">
                <select class="form-select text-uppercase" id="diaSemana" disabled>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['week_id'] ?>" <?= ($_SESSION['form_dbloq']['dbloq_dia'] ?? '') == $res['week_id'] ? 'selected' : '' ?>><?= htmlspecialchars($res['week_dias']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-12">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM conf_dias_bloqueadas_motivo ORDER BY dbloqm_motivo");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div>
                <label class="form-label">Motivo <span>*</span></label>
                <select class="form-select text-uppercase" name="dbloq_motivo" required>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['dbloqm_id'] ?>" <?= ($_SESSION['form_dbloq']['dbloq_motivo'] ?? '') == $res['dbloqm_id'] ? 'selected' : '' ?>><?= htmlspecialchars($res['dbloqm_motivo']) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="dbloq_status" name="dbloq_status" value="1" <?= (!isset($_SESSION['form_dbloq']) || !empty($_SESSION['form_dbloq']['dbloq_status'])) ? 'checked' : '' ?>>
                <label class="form-check-label" for="dbloq_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Cadastrar</button>
              </div>
            </div>

          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<?php
// APAGA AS SESSÕES QUE PREENCHEM O FORMULÁRIO
$formData = $_SESSION['form_dbloq'] ?? [];
unset($_SESSION['form_dbloq']);
?>

<script>
  function preencherCampos() {
    const dataInput = document.getElementById('data').value;
    if (dataInput) {
      // Pega a data e cria um objeto Date no fuso UTC
      const partes = dataInput.split('-');
      const ano = parseInt(partes[0], 10);
      const mes = parseInt(partes[1], 10) - 1; // Meses começam do zero
      const dia = parseInt(partes[2], 10);
      const data = new Date(Date.UTC(ano, mes, dia));

      const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
      const diasSemana = ["7", "1", "2", "3", "4", "5", "6"];

      document.getElementById('mes').value = meses[data.getUTCMonth()];
      document.getElementById('ano').value = data.getUTCFullYear();
      document.getElementById('diaSemana').value = diasSemana[data.getUTCDay()];
      document.getElementById('diaSemanaId').value = diasSemana[data.getUTCDay()];
    }
  }
</script>



<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_dia_bloqueado" tabindex="-1" aria-labelledby="modal_edit_dia_bloqueado" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_dia_bloqueado">Cadastrar Data Bloqueada</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=DataBloq" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control dbloq_id" name="dbloq_id">
            <input type="hidden" name="acao" value="atualizar">

            <div class="col-md-6">
              <div>
                <label class="form-label">Data <span>*</span></label>
                <input type="date" class="form-control flatpickr-input dbloq_data" name="dbloq_data" id="edit_data" onchange="preencherCamposEdit()" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
              <script>
                flatpickr("#edit_data", {
                  dateFormat: "Y-m-d", // formato do valor REAL do input (enviado na query)
                  altInput: true, // ativa um input visível com formato alternativo
                  altFormat: "d/m/Y", // formato exibido para o usuário
                  locale: "pt", // idioma português
                  allowInput: true,
                  onClose: function() {
                    // Validação automática quando o usuário seleciona uma data
                    const input = document.getElementById('edit_data');
                    input.classList.toggle('is-invalid', !input.value);
                  }
                });

                // Validação integrada com o Bootstrap
                document.getElementById('edit_data').addEventListener('input', function() {
                  this.classList.toggle('is-invalid', !this.value);
                });
              </script>
            </div>

            <div class="col-md-6">
              <div>
                <label class="form-label">Mês</label>
                <input type="text" class="form-control text-uppercase dbloq_mes" name="dbloq_mes" id="edit_mes" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <div>
                <label class="form-label">Ano</label>
                <input type="text" class="form-control text-uppercase dbloq_ano" name="dbloq_ano" id="edit_ano" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <?php try {
                $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div>
                <label class="form-label">Dia da semana</label>
                <input type="hidden" class="form-control text-uppercase dbloq_dia_id" name="dbloq_dia" id="edit_diaSemanaId" value="<?= htmlspecialchars($_SESSION['form_dbloq']['dbloq_dia'] ?? '') ?>">
                <select class="form-select text-uppercase dbloq_dia" id="edit_diaSemana" disabled>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['week_id'] ?>" <?= ($_SESSION['form_dbloq']['dbloq_dia'] ?? '') == $res['week_id'] ? 'selected' : '' ?>><?= htmlspecialchars($res['week_dias']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-12">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM conf_dias_bloqueadas_motivo ORDER BY dbloqm_motivo");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <div>
                <label class="form-label">Motivo <span>*</span></label>
                <select class="form-select text-uppercase dbloq_motivo" name="dbloq_motivo" required>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['dbloqm_id'] ?>" <?= ($_SESSION['form_dbloq']['dbloq_motivo'] ?? '') == $res['dbloqm_id'] ? 'selected' : '' ?>><?= htmlspecialchars($res['dbloqm_motivo']) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input dbloq_status" type="checkbox" id="dbloq_status" name="dbloq_status" value="1">
                <label class="form-check-label" for="dbloq_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Cadastrar</button>
              </div>
            </div>

          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function preencherCamposEdit() {
    const edit_dataInput = document.getElementById('edit_data').value;
    if (edit_dataInput) {
      // Pega a data e cria um objeto Date no fuso UTC
      const edit_partes = edit_dataInput.split('-');
      const edit_ano = parseInt(edit_partes[0], 10);
      const edit_mes = parseInt(edit_partes[1], 10) - 1; // Meses começam do zero
      const edit_dia = parseInt(edit_partes[2], 10);
      const edit_data = new Date(Date.UTC(edit_ano, edit_mes, edit_dia));

      const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
      const diasSemana = ["7", "1", "2", "3", "4", "5", "6"];

      document.getElementById('edit_mes').value = meses[edit_data.getUTCMonth()];
      document.getElementById('edit_ano').value = edit_data.getUTCFullYear();
      document.getElementById('edit_diaSemana').value = diasSemana[edit_data.getUTCDay()];
      document.getElementById('edit_diaSemanaId').value = diasSemana[edit_data.getUTCDay()];
    }
  }
</script>





<script>
  const modal_edit_dia_bloqueado = document.getElementById('modal_edit_dia_bloqueado')
  if (modal_edit_dia_bloqueado) {
    modal_edit_dia_bloqueado.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const dbloq_id = button.getAttribute('data-bs-dbloq_id')
      const dbloq_data = button.getAttribute('data-bs-dbloq_data')
      const dbloq_dia = button.getAttribute('data-bs-dbloq_dia')
      const dbloq_dia_id = button.getAttribute('data-bs-dbloq_dia_id')
      const dbloq_mes = button.getAttribute('data-bs-dbloq_mes')
      const dbloq_ano = button.getAttribute('data-bs-dbloq_ano')
      const dbloq_motivo = button.getAttribute('data-bs-dbloq_motivo')
      const dbloq_status = button.getAttribute('data-bs-dbloq_status')
      // 
      const modalTitle = modal_edit_dia_bloqueado.querySelector('.modal-title')
      const modal_dbloq_id = modal_edit_dia_bloqueado.querySelector('.dbloq_id')
      const modal_dbloq_data = modal_edit_dia_bloqueado.querySelector('.dbloq_data')
      const modal_dbloq_dia = modal_edit_dia_bloqueado.querySelector('.dbloq_dia')
      const modal_dbloq_dia_id = modal_edit_dia_bloqueado.querySelector('.dbloq_dia_id')
      const modal_dbloq_mes = modal_edit_dia_bloqueado.querySelector('.dbloq_mes')
      const modal_dbloq_ano = modal_edit_dia_bloqueado.querySelector('.dbloq_ano')
      const modal_dbloq_motivo = modal_edit_dia_bloqueado.querySelector('.dbloq_motivo')
      const modal_dbloq_status = modal_edit_dia_bloqueado.querySelector('.dbloq_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_dbloq_id.value = dbloq_id

      // modal_dbloq_data.value = dbloq_data
      // FLATPICKR - SETA A DATA CORRETAMENTE USANDO A API
      const fp = modal_dbloq_data._flatpickr;
      if (fp && dbloq_data) {
        fp.setDate(dbloq_data); // ex: "2025-05-29"
      } //

      modal_dbloq_dia.value = dbloq_dia
      modal_dbloq_dia_id.value = dbloq_dia_id
      modal_dbloq_mes.value = dbloq_mes
      modal_dbloq_ano.value = dbloq_ano
      modal_dbloq_motivo.value = dbloq_motivo
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (dbloq_status === '1') {
        modal_dbloq_status.checked = true;
      } else {
        modal_dbloq_status.checked = false;
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>