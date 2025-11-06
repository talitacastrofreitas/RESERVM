<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!empty($global_admin_id) && $global_admin_perfil != 1) {
  header("Location: sair.php");
}
?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Cursos e Coordenadores</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Cadastros</a></li>
          <li class="breadcrumb-item active">Cursos e Coordenadores</li>
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
            <h5 class="card-title mb-0">Lista de Cursos e Coordenadores</h5>
          </div>
          <div class="col-md-6 d-flex align-items-center d-flex justify-content-md-end justify-content-center">
            <button class="btn botao botao_azul_escuro waves-effect mt-3 mt-md-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_curso_coordenador">+ Cadastrar Curso</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_curso_coordenador" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>Curso</th>
              <th>Responsável</th>
              <th>Email</th>
              <th>Status</th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM conf_cursos_coordenadores
                                      INNER JOIN status ON status.st_id = conf_cursos_coordenadores.cc_status
                                      WHERE cc_id NOT IN (20, 21)");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $cc_curso       = htmlspecialchars($cc_curso, ENT_QUOTES, 'UTF-8'); // EXIBE PALAVRAS COM ASPAS DUPLAS
                $cc_coordenador = htmlspecialchars($cc_coordenador, ENT_QUOTES, 'UTF-8'); // EXIBE PALAVRAS COM ASPAS DUPLAS

                //CONFIGURAÇÃO DO STATUS  
                $status_color = ($st_id == 1) ? 'bg_info_verde' : 'bg_info_cinza';
            ?>

                <tr>
                  <th><?= $cc_curso ?></th>
                  <td><?= $cc_coordenador ?></td>
                  <td><?= $cc_email ?></td>
                  <td><span class="badge <?= $status_color ?>"><?= $st_status ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_curso_coordenador"  data-bs-cc_id="<?= $cc_id ?>"
                            data-bs-cc_curso="<?= $cc_curso ?>"
                            data-bs-cc_coordenador="<?= $cc_coordenador ?>"
                            data-bs-cc_email="<?= $cc_email ?>"
                            data-bs-cc_status="<?= $cc_status ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="controller/controller_cursos_coordenadores.php?funcao=exc_curso&cc_id=<?= $cc_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_cad_curso_coordenador" tabindex="-1" aria-labelledby="modal_cad_curso_coordenador" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_curso_coordenador">Cadastrar Curso</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_cursos_coordenadores.php" class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-12">
              <div>
                <label class="form-label">Curso <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="cc_curso" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div>
                <label class="form-label">Responsável</label>
                <input type="text" class="form-control text-uppercase" name="cc_coordenador" maxlength="50">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div>
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control text-lowercase" name="cc_email" maxlength="100">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="cc_status" name="cc_status" value="1" checked>
                <label class="form-check-label" for="cc_status">Ativo</label>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadCursoCoordenador">Cadastrar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<div class="modal fade modal_padrao" id="modal_edit_curso_coordenador" tabindex="-1" aria-labelledby="modal_edit_curso_coordenador" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_curso_coordenador">Editar Curso</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_cursos_coordenadores.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control cc_id" name="cc_id">

            <div class="col-12">
              <div>
                <label class="form-label">Curso <span>*</span></label>
                <input type="text" class="form-control text-uppercase cc_curso" name="cc_curso" maxlength="50" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div>
                <label class="form-label">Responsável</label>
                <input type="text" class="form-control text-uppercase cc_coordenador" name="cc_coordenador" maxlength="50">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div>
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control text-lowercase cc_email" name="cc_email" maxlength="100">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input cc_status" type="checkbox" id="cc_status" name="cc_status" value="1" checked>
                <label class="form-check-label" for="cc_status">Ativo</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditarCursoCoordenador">Atualizar</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const modal_edit_curso_coordenador = document.getElementById('modal_edit_curso_coordenador')
  if (modal_edit_curso_coordenador) {
    modal_edit_curso_coordenador.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const cc_id = button.getAttribute('data-bs-cc_id')
      const cc_curso = button.getAttribute('data-bs-cc_curso')
      const cc_coordenador = button.getAttribute('data-bs-cc_coordenador')
      const cc_email = button.getAttribute('data-bs-cc_email')
      const cc_status = button.getAttribute('data-bs-cc_status')
      // 
      const modalTitle = modal_edit_curso_coordenador.querySelector('.modal-title')
      const modal_cc_id = modal_edit_curso_coordenador.querySelector('.cc_id')
      const modal_cc_curso = modal_edit_curso_coordenador.querySelector('.cc_curso')
      const modal_cc_coordenador = modal_edit_curso_coordenador.querySelector('.cc_coordenador')
      const modal_cc_email = modal_edit_curso_coordenador.querySelector('.cc_email')
      const modal_cc_status = modal_edit_curso_coordenador.querySelector('.cc_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_cc_id.value = cc_id
      modal_cc_curso.value = cc_curso
      modal_cc_coordenador.value = cc_coordenador
      modal_cc_email.value = cc_email
      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (cc_status === '1') {
        modal_cc_status.checked = true;
      } else {
        modal_cc_status.checked = false;
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>