<?php include 'includes/header.php'; ?>

<?php
if (!isset($_SESSION['reservm_admin_id']) || $_SESSION['reservm_admin_perfil'] != 1) {
  header("Location: sair.php");
  exit;
}
?>
<style>
  .select2-container {
    display: block !important;
    height: auto !important;
    width: 100% !important;
    opacity: 1 !important;
    visibility: visible !important;
    z-index: 9999 !important;

  }

  .select2-dropdown {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    z-index: 9999 !important;
  }
</style>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Componentes Curriculares</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Componentes Curriculares</li>
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
          <div class="col-sm-6 text-sm-start text-center">
            <h5 class="card-title mb-0">Lista de Componentes Curriculares</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-target="#modal_cad_componente_curricular">+ Cadastrar Componente</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_comp_curricular" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>Componente Curricular</th>
              <th>Curso</th>
              <th>Semestre</th>
              <th>Professores Vinculados</th>
              <th>Status</th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
 
            try {
              $stmt = $conn->prepare("SELECT compc_id, compc_componente, compc_curso, compc_semestre, compc_status, curs_curso, st_id, st_status 
                                      FROM componente_curricular
                                      INNER JOIN cursos ON cursos.curs_id = componente_curricular.compc_curso
                                      INNER JOIN status ON status.st_id = componente_curricular.compc_status");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $compc_id = $row['compc_id'];
                
                // --- CORREÇÃO AQUI: NOME DA COLUNA E SEM TRIM ---
                $sqlProfs = "SELECT c.CHAPA, c.NOMESOCIAL, c.EMAIL 
                             FROM componente_professores cp
                             JOIN colaboradores c ON c.CHAPA = cp.cp_colaborador_matricula
                             WHERE cp.cp_compc_id = :cid";
                
                $stmtP = $conn->prepare($sqlProfs);
                $stmtP->execute([':cid' => $compc_id]);
                $professores = $stmtP->fetchAll(PDO::FETCH_ASSOC);

                $nomes_profs = [];
                $chapas_profs = [];
                
                foreach($professores as $p) {
                    $nomes_profs[] = $p['NOMESOCIAL'];
                    $chapas_profs[] = $p['CHAPA']; 
                }
                
                $texto_profs = implode(', ', $nomes_profs);
                $json_chapas = json_encode($chapas_profs);
                
                $status_color = ($row['st_id'] == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= htmlspecialchars($row['compc_componente']) ?></th>
                  <td><?= htmlspecialchars($row['curs_curso']) ?></td>
                  <td class="text-center">
      <?= $row['compc_semestre'] ? $row['compc_semestre'] . 'º' : '<span class="text-muted small">Geral</span>' ?>
  </td>
                  <td class="text-wrap" style="max-width: 300px;">
                      <?= !empty($texto_profs) ? $texto_profs : '<span class="text-muted fst-italic">Nenhum vinculado</span>' ?>
                  </td>
                  <td><span class="badge <?= $status_color ?>"><?= htmlspecialchars($row['st_status']) ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_componente_curricular"
                            data-bs-compc_id="<?= htmlspecialchars($compc_id) ?>"
                            data-bs-compc_componente="<?= htmlspecialchars($row['compc_componente']) ?>"
                            data-bs-compc_curso="<?= htmlspecialchars($row['compc_curso']) ?>"
                            data-bs-compc_semestre="<?= htmlspecialchars($row['compc_semestre']) ?>"
                            data-bs-compc_status="<?= htmlspecialchars($row['compc_status']) ?>"
                            data-bs-professores='<?= $json_chapas ?>'
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="../router/web.php?r=CompC&acao=deletar&compc_id=<?= $compc_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>

            <?php }
            } catch (PDOException $e) {
              echo "Erro ao tentar recuperar os dados";
            } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal_padrao" id="modal_cad_componente_curricular" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title">Cadastrar Componente Curricular</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=CompC" class="needs-validation" novalidate>
          <div class="row g-3">
            <input type="hidden" name="acao" value="cadastrar">

            <div class="col-12">
              <label class="form-label">Componente Curricular <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="compc_componente" required>
            </div>

            <div class="col-12">
              <label class="form-label">Curso <span>*</span></label>
              <select class="form-select text-uppercase compc_curso" name="compc_curso" id="compc_curso" required>
                <option selected disabled value=""></option>
                <?php 
                  $sql = $conn->query("SELECT * FROM cursos WHERE curs_status = 1 ORDER BY curs_curso");
                  while($res = $sql->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$res['curs_id']}'>{$res['curs_curso']}</option>";
                  }
                ?>
              </select>

            </div>

            <div class="col-12">
  <label class="form-label">Semestre</label>
  <select class="form-select compc_semestre" name="compc_semestre" id="compc_semestre">
    <option value="">Geral / Todos (Vazio)</option>
    <?php for($i=1; $i<=12; $i++): ?>
        <option value="<?= $i ?>"><?= $i ?>º Semestre</option>
    <?php endfor; ?>
  </select>

              <script>
                $(document).ready(function () {
                  $('.compc_semestre').select2({
                    placeholder: "Selecione o curso",
                    tags: false,
                    allowClear: true,
                    dropdownParent: $('#modal_cad_componente_curricular'),
                    width: '100%'
                  });
                });
              </script>
</div>

            <div class="col-12">
               <label class="form-label">Professores Vinculados</label>
               <select class="form-select text-uppercase" name="compc_professores[]" id="cad_professores" multiple="multiple" style="width: 100%;">
                 <?php 
                   // BUSCA DA TABELA COLABORADORES
                   $sqlUsers = $conn->query("SELECT CHAPA, NOMESOCIAL, EMAIL FROM colaboradores ORDER BY NOMESOCIAL");
                   while($u = $sqlUsers->fetch(PDO::FETCH_ASSOC)) {
                     echo "<option value='{$u['CHAPA']}' data-email='{$u['EMAIL']}'>{$u['NOMESOCIAL']}</option>";
                   }
                 ?>
               </select>
            </div>

            <div class="col-12">
                <label class="form-label">E-mails dos Responsáveis</label>
                <textarea class="form-control" id="cad_emails_visual" rows="2" readonly style="background-color: #e9ecef; font-size: 0.85em;"></textarea>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="compc_status" value="1" checked>
                <label class="form-check-label">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12 text-end">
                <button type="button" class="btn botao btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn botao botao_verde">Cadastrar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal_padrao" id="modal_edit_componente_curricular" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title">Editar Componente Curricular</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=CompC" class="needs-validation" novalidate>
          <div class="row g-3">
            <input type="hidden" class="form-control compc_id" name="compc_id">
            <input type="hidden" name="acao" value="atualizar">

            <div class="col-12">
              <label class="form-label">Componente Curricular <span>*</span></label>
              <input type="text" class="form-control text-uppercase compc_componente" name="compc_componente" required>
            </div>

            <div class="col-12">
              <label class="form-label">Curso <span>*</span></label>
              <select class="form-select text-uppercase compc_curso" name="compc_curso" id="edit_compc_curso" required>
                <?php 
                  $sql = $conn->query("SELECT * FROM cursos WHERE curs_status = 1 ORDER BY curs_curso");
                  while($res = $sql->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$res['curs_id']}'>{$res['curs_curso']}</option>";
                  }
                ?>
              </select>
            </div>

            <div class="col-12">
  <label class="form-label">Semestre</label>
  <select class="form-select compc_semestre" name="compc_semestre" id="compc_semestre_edit">
    <option value="">Geral / Todos (Vazio)</option>
    <?php for($i=1; $i<=12; $i++): ?>
        <option value="<?= $i ?>"><?= $i ?>º Semestre</option>
    <?php endfor; ?>
  </select>

              <script>
                $(document).ready(function () {
                  $('#compc_semestre_edit').select2({
                    placeholder: "Selecione as opções",
                    tags: false,
                    allowClear: true,
                    dropdownParent: $('#modal_edit_componente_curricular'),
                    width: '100%'
                  });
                });
              </script>
</div>

            <div class="col-12">
               <label class="form-label">Professores Vinculados</label>
               <select class="form-select text-uppercase" name="compc_professores[]" id="edit_professores" multiple="multiple" style="width: 100%;">
                 <?php 
        
                   $sqlUsers->execute(); 
                   while($u = $sqlUsers->fetch(PDO::FETCH_ASSOC)) {
                     echo "<option value='{$u['CHAPA']}' data-email='{$u['EMAIL']}'>{$u['NOMESOCIAL']}</option>";
                   }
                 ?>
               </select>
            </div>

            <div class="col-12">
                <label class="form-label">E-mails dos Responsáveis</label>
                <textarea class="form-control" id="edit_emails_visual" rows="2" readonly style="background-color: #e9ecef; font-size: 0.85em;"></textarea>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input compc_status" type="checkbox" name="compc_status" value="1">
                <label class="form-check-label">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12 text-end">
                <button type="button" class="btn botao btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn botao botao_verde">Atualizar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<?php include 'includes/footer.php'; ?>
<script src="includes/select/select2.js"></script>




<!-- PREENCHIMENTO DO EMAIL -->
 <script>
  $(document).ready(function() {

    // --- FUNÇÃO PARA ATUALIZAR EMAILS ---
    function atualizarEmails(selectElement, textareaId) {
      var emails = [];
      var selectedOptions = $(selectElement).find('option:selected');
      selectedOptions.each(function() {
        var email = $(this).data('email');
        if (email) emails.push(email);
      });
      $('#' + textareaId).val(emails.join('; '));
    }

    // --- PREENCHIMENTO DO MODAL DE EDIÇÃO ---
    $('#modal_edit_componente_curricular').on('shown.bs.modal', function(event) {
      const button = event.relatedTarget;

      // 1. Pega dados do botão
      const id = button.getAttribute('data-bs-compc_id');
      const comp = button.getAttribute('data-bs-compc_componente');
      const curso = button.getAttribute('data-bs-compc_curso');
      const status = button.getAttribute('data-bs-compc_status');
      const profsJson = button.getAttribute('data-bs-professores');
     const semestre = button.getAttribute('data-bs-compc_semestre');
      // 2. Preenche campos normais
      const modalEdit = document.getElementById('modal_edit_componente_curricular');
      modalEdit.querySelector('.compc_id').value = id;
      modalEdit.querySelector('.compc_componente').value = comp;
      modalEdit.querySelector('.compc_status').checked = (status === '1');
      $('#edit_compc_curso').val(curso).trigger('change');

 

      // 2. Preenche campos normais (Adicionar esta linha)
      $('#compc_semestre_edit').val(semestre).trigger('change');

      // 3. Seleciona os valores vindos do banco (Professores)
      let profsArray = [];
      try {
        profsArray = JSON.parse(profsJson);
      } catch (e) {}

      // Define os valores selecionados e dispara o trigger para atualizar os emails e o visual do Select2 (se já estiver iniciado globalmente)
      $('#edit_professores').val(profsArray).trigger('change');
    });

    // --- EVENTOS DE MUDANÇA (PARA ATUALIZAR E-MAIL) ---
    $('#cad_professores').on('change', function() {
      atualizarEmails(this, 'cad_emails_visual');
    });
    $('#edit_professores').on('change', function() {
      atualizarEmails(this, 'edit_emails_visual');
    });

  });
</script>