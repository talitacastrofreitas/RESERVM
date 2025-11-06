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
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Espaços</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
          <li class="breadcrumb-item active">Espaços</li>
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
            <h5 class="card-title mb-0">Lista de Espaços</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <button class="btn botao botao_amarelo waves-effect mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_espaco">+ Cadastrar Espaço</button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_espaco" class="table dt-responsive align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-2">Código</span></th>
              <th><span class="me-2">Nome do Espaço</span></th>
              <th><span class="me-2">Nome Resumido</span></th>
              <th><span class="me-2">Tipo de Espaço</span></th>
              <th><span class="me-2">Campus</span></th>
              <th><span class="me-2">Pavilhão</span></th>
              <th><span class="me-2">Andar</span></th>
              <th nowrap="nowrap"><span class="me-2">Cap. Máx.</span></th>
              <th nowrap="nowrap"><span class="me-2">Cap. Méd.</span></th>
              <th nowrap="nowrap"><span class="me-2">Cap. Mín.</span></th>
              <th><span class="me-2">Status</span></th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local, esp_nome_local_resumido, esp_tipo_espaco, UPPER(tipesp_tipo_espaco) AS tipesp_tipo_espaco, esp_andar, esp_pavilhao, UPPER(uni_unidade) AS uni_unidade, UPPER(pav_pavilhao) AS pav_pavilhao, UPPER(and_andar) AS and_andar, esp_quant_maxima, esp_quant_media, esp_quant_minima, esp_unidade, esp_recursos, esp_status, st_status
                                      FROM espaco
                                      INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                      INNER JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                      LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                      LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                                      INNER JOIN status ON status.st_id = espaco.esp_status");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //extract($row);

                $esp_id                  = $row['esp_id'];
                $esp_codigo              = $row['esp_codigo'];
                $esp_nome_local          = $row['esp_nome_local'];
                $esp_nome_local_resumido = $row['esp_nome_local_resumido'];
                $esp_tipo_espaco         = $row['esp_tipo_espaco'];
                $tipesp_tipo_espaco      = $row['tipesp_tipo_espaco'];
                $esp_andar               = $row['esp_andar'];
                $esp_pavilhao            = $row['esp_pavilhao'];
                $uni_unidade             = $row['uni_unidade'];
                $pav_pavilhao            = $row['pav_pavilhao'];
                $and_andar               = $row['and_andar'];
                $esp_quant_maxima        = $row['esp_quant_maxima'];
                $esp_quant_media         = $row['esp_quant_media'];
                $esp_quant_minima        = $row['esp_quant_minima'];
                $esp_unidade             = $row['esp_unidade'];
                $esp_recursos            = $row['esp_recursos'];
                $esp_status              = $row['esp_status'];
                $st_status               = $row['st_status'];

                // CONFIGURAÇÃO DO STATUS
                $esp_status == 1 ?  $status_color = 'bg_info_verde' : $status_color = 'bg_info_cinza';
            ?>
                <tr>
                  <th scope="row"><?= htmlspecialchars($esp_codigo) ?></th>
                  <td scope="row"><?= htmlspecialchars($esp_nome_local) ?></td>
                  <td scope="row"><?= htmlspecialchars($esp_nome_local_resumido) ?></td>
                  <td scope="row" nowrap="nowrap"><?= htmlspecialchars($tipesp_tipo_espaco) ?></td>
                  <td scope="row"><?= htmlspecialchars($uni_unidade) ?></td>
                  <td scope="row" nowrap="nowrap"><?= htmlspecialchars($pav_pavilhao) ?></td>
                  <td scope="row" nowrap="nowrap"><?= htmlspecialchars($and_andar) ?></td>
                  <td scope="row"><?= htmlspecialchars($esp_quant_maxima) ?></td>
                  <td scope="row"><?= htmlspecialchars($esp_quant_media) ?></td>
                  <td scope="row"><?= htmlspecialchars($esp_quant_minima) ?></td>
                  <td scope="row"><span class="badge <?= $status_color ?>"><?= htmlspecialchars($st_status) ?></span></td>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_verde_musgo btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_espaco"
                            data-bs-esp_id="<?= htmlspecialchars($esp_id) ?>"
                            data-bs-esp_codigo="<?= htmlspecialchars($esp_codigo) ?>"
                            data-bs-esp_nome_local="<?= htmlspecialchars($esp_nome_local) ?>"
                            data-bs-esp_nome_local_resumido="<?= htmlspecialchars($esp_nome_local_resumido) ?>"
                            data-bs-esp_tipo_espaco="<?= htmlspecialchars($esp_tipo_espaco) ?>"
                            data-bs-esp_andar="<?= htmlspecialchars($esp_andar) ?>"
                            data-bs-esp_pavilhao="<?= htmlspecialchars($esp_pavilhao) ?>"
                            data-bs-esp_quant_maxima="<?= htmlspecialchars($esp_quant_maxima) ?>"
                            data-bs-esp_quant_media="<?= htmlspecialchars($esp_quant_media) ?>"
                            data-bs-esp_quant_minima="<?= htmlspecialchars($esp_quant_minima) ?>"
                            data-bs-esp_unidade="<?= htmlspecialchars($esp_unidade) ?>"
                            data-bs-esp_recursos="<?= htmlspecialchars($esp_recursos) ?>"
                            data-bs-esp_status="<?= htmlspecialchars($esp_status) ?>"
                            title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                        <li><a href="../router/web.php?r=Espac&acao=deletar&esp_id=<?= $esp_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_cad_espaco" tabindex="-1" aria-labelledby="modal_cad_espaco" aria-modal="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_espaco">Cadastrar Espaço</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Espac" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" name="acao" value="cadastrar">

            <div class="col-lg-4">
              <label class="form-label">Código <span>*</span></label>
              <input class="form-control text-uppercase" name="esp_codigo" value="<?= htmlspecialchars($_SESSION['form_esp']['esp_codigo'] ?? '') ?>" maxlength="30" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-8">
              <label class="form-label">Nome do local <span>*</span></label>
              <input class="form-control text-uppercase" name="esp_nome_local" value="<?= htmlspecialchars($_SESSION['form_esp']['esp_nome_local'] ?? '') ?>" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-8">
              <label class="form-label">Nome do local resumido <span>*</span></label>
              <input class="form-control text-uppercase" name="esp_nome_local_resumido" value="<?= htmlspecialchars($_SESSION['form_esp']['esp_nome_local_resumido'] ?? '') ?>" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-md-6 col-lg-4">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM tipo_espaco ORDER BY tipesp_tipo_espaco");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Tipo de espaço <span>*</span></label>
              <select class="form-select text-uppercase" name="esp_tipo_espaco" required>
                <option selected disabled value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['tipesp_id'] ?>" <?= ($_SESSION['form_esp']['esp_tipo_espaco'] ?? '') == $res['tipesp_id'] ? 'selected' : '' ?>><?= $res['tipesp_tipo_espaco'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-md-6 col-lg-4">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM andares ORDER BY and_andar");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Andar</label>
              <select class="form-select text-uppercase" name="esp_andar">
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['and_id'] ?>" <?= ($_SESSION['form_esp']['esp_andar'] ?? '') == $res['and_id'] ? 'selected' : '' ?>><?= $res['and_andar'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM pavilhoes ORDER BY pav_pavilhao");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Pavilhão</label>
              <select class="form-select text-uppercase" name="esp_pavilhao">
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['pav_id'] ?>" <?= ($_SESSION['form_esp']['esp_pavilhao'] ?? '') == $res['pav_id'] ? 'selected' : '' ?>><?= $res['pav_pavilhao'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM unidades");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Unidade</label>
              <select class="form-select text-uppercase" name="esp_unidade">
                <option selected disabled value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['uni_id'] ?>" <?= ($_SESSION['form_esp']['esp_unidade'] ?? '') == $res['uni_id'] ? 'selected' : '' ?>><?= $res['uni_unidade'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Capacidade Máxima</label>
              <input class="form-control" name="esp_quant_maxima" value="<?= htmlspecialchars($_SESSION['form_esp']['esp_quant_maxima'] ?? '') ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="5">
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Capacidade Média</label>
              <input class="form-control" name="esp_quant_media" value="<?= htmlspecialchars($_SESSION['form_esp']['esp_quant_media'] ?? '') ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="5">
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Capacidade Mínima</label>
              <input class="form-control" name="esp_quant_minima" value="<?= htmlspecialchars($_SESSION['form_esp']['esp_quant_minima'] ?? '') ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="5">
            </div>

            <div class="col-12 mb-2">
              <label class="form-label">Recursos disponíveis</label>
              <div class="check_item_container hstack gap-2 flex-wrap">
                <?php try {
                  $sql = $conn->prepare("SELECT rec_id, rec_recurso FROM recursos ORDER BY rec_recurso");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <?php foreach ($result as $res) : ?>
                  <input type="checkbox" class="btn-check check_formulario_check" name="esp_recursos[]" id="checkRecurso<?= $res['rec_id'] ?>" value="<?= $res['rec_id'] ?>">
                  <label class="check_item check_formulario" for="checkRecurso<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></label>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="esp_status" name="esp_status" value="1" <?= (!isset($_SESSION['form_esp']) || !empty($_SESSION['form_esp']['esp_status'])) ? 'checked' : '' ?>>
                <label class="form-check-label" for="esp_status">Espaço disponível</label>
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
$formData = $_SESSION['form_esp'] ?? [];
unset($_SESSION['form_esp']);
?>


<!-- EDITAR -->
<div class="modal fade modal_padrao" id="modal_edit_espaco" tabindex="-1" aria-labelledby="modal_edit_espaco" aria-modal="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_espaco">Atualizar Espaço</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../router/web.php?r=Espac" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control esp_id" name="esp_id" required>
            <input type="hidden" name="acao" value="atualizar">

            <div class="col-lg-4">
              <label class="form-label">Código <span>*</span></label>
              <input class="form-control text-uppercase esp_codigo" name="esp_codigo" maxlength="30" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-8">
              <label class="form-label">Nome do local <span>*</span></label>
              <input class="form-control text-uppercase esp_nome_local" name="esp_nome_local" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-lg-8">
              <label class="form-label">Nome do local resumido <span>*</span></label>
              <input class="form-control text-uppercase esp_nome_local_resumido" name="esp_nome_local_resumido" maxlength="100" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-md-6 col-lg-4">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM tipo_espaco ORDER BY tipesp_tipo_espaco");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Tipo de espaço <span>*</span></label>
              <select class="form-select text-uppercase esp_tipo_espaco" name="esp_tipo_espaco" required>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['tipesp_id'] ?>"><?= $res['tipesp_tipo_espaco'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-md-6 col-lg-4">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM andares ORDER BY and_andar");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Andar</label>
              <select class="form-select text-uppercase esp_andar" name="esp_andar">
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['and_id'] ?>"><?= $res['and_andar'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM pavilhoes ORDER BY pav_pavilhao");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Pavilhão</label>
              <select class="form-select text-uppercase esp_pavilhao" name="esp_pavilhao">
                <option selected value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['pav_id'] ?>"><?= $res['pav_pavilhao'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM unidades");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar o perfil";
              } ?>
              <label class="form-label">Unidade <span>*</span></label>
              <select class="form-select text-uppercase esp_unidade" name="esp_unidade">
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Capacidade Máxima</label>
              <input class="form-control esp_quant_maxima" name="esp_quant_maxima" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="5">
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Capacidade Média</label>
              <input class="form-control esp_quant_media" name="esp_quant_media" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="5">
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Capacidade Mínima</label>
              <input class="form-control esp_quant_minima" name="esp_quant_minima" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="5">
            </div>

            <div class="col-12 mb-2">
              <label class="form-label">Recursos disponíveis</label>
              <div class="check_item_container hstack gap-2 flex-wrap">
                <?php try {
                  $sql = $conn->prepare("SELECT rec_id, rec_recurso FROM recursos ORDER BY rec_recurso");
                  $sql->execute();
                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar o perfil";
                } ?>
                <?php foreach ($result as $res) : ?>
                  <input type="checkbox" class="btn-check check_formulario_check esp_recursos" name="esp_recursos[]" id="checkRecursoEdit<?= $res['rec_id'] ?>" value="<?= $res['rec_id'] ?>">
                  <label class="check_item check_formulario" for="checkRecursoEdit<?= $res['rec_id'] ?>"><?= $res['rec_recurso'] ?></label>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input esp_status" type="checkbox" id="esp_status_edit" name="esp_status" value="1" checked>
                <label class="form-check-label" for="esp_status_edit">Espaço disponível</label>
              </div>
            </div>

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
  const modal_edit_espaco = document.getElementById('modal_edit_espaco')
  if (modal_edit_espaco) {
    modal_edit_espaco.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const esp_id = button.getAttribute('data-bs-esp_id')
      const esp_codigo = button.getAttribute('data-bs-esp_codigo')
      const esp_nome_local = button.getAttribute('data-bs-esp_nome_local')
      const esp_nome_local_resumido = button.getAttribute('data-bs-esp_nome_local_resumido')
      const esp_tipo_espaco = button.getAttribute('data-bs-esp_tipo_espaco')
      const esp_andar = button.getAttribute('data-bs-esp_andar')
      const esp_pavilhao = button.getAttribute('data-bs-esp_pavilhao')
      const esp_quant_maxima = button.getAttribute('data-bs-esp_quant_maxima')
      const esp_quant_media = button.getAttribute('data-bs-esp_quant_media')
      const esp_quant_minima = button.getAttribute('data-bs-esp_quant_minima')
      const esp_unidade = button.getAttribute('data-bs-esp_unidade')
      //
      const esp_recursos = button.getAttribute('data-bs-esp_recursos');
      const recursosSelecionados = esp_recursos ? esp_recursos.split(', ') : [];
      //
      const esp_status = button.getAttribute('data-bs-esp_status')
      // 
      const modalTitle = modal_edit_espaco.querySelector('.modal-title')
      const modal_esp_id = modal_edit_espaco.querySelector('.esp_id')
      const modal_esp_codigo = modal_edit_espaco.querySelector('.esp_codigo')
      const modal_esp_nome_local = modal_edit_espaco.querySelector('.esp_nome_local')
      const modal_esp_nome_local_resumido = modal_edit_espaco.querySelector('.esp_nome_local_resumido')
      const modal_esp_tipo_espaco = modal_edit_espaco.querySelector('.esp_tipo_espaco')
      const modal_esp_andar = modal_edit_espaco.querySelector('.esp_andar')
      const modal_esp_pavilhao = modal_edit_espaco.querySelector('.esp_pavilhao')
      const modal_esp_quant_maxima = modal_edit_espaco.querySelector('.esp_quant_maxima')
      const modal_esp_quant_media = modal_edit_espaco.querySelector('.esp_quant_media')
      const modal_esp_quant_minima = modal_edit_espaco.querySelector('.esp_quant_minima')
      const modal_esp_unidade = modal_edit_espaco.querySelector('.esp_unidade')
      const modal_esp_recursos = modal_edit_espaco.querySelectorAll('.esp_recursos');
      const modal_esp_status = modal_edit_espaco.querySelector('.esp_status')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_esp_id.value = esp_id
      modal_esp_codigo.value = esp_codigo
      modal_esp_nome_local.value = esp_nome_local
      modal_esp_nome_local_resumido.value = esp_nome_local_resumido
      modal_esp_tipo_espaco.value = esp_tipo_espaco
      modal_esp_andar.value = esp_andar
      modal_esp_pavilhao.value = esp_pavilhao
      modal_esp_quant_maxima.value = esp_quant_maxima
      modal_esp_quant_media.value = esp_quant_media
      modal_esp_quant_minima.value = esp_quant_minima
      modal_esp_unidade.value = esp_unidade

      // VERIFICA DE OS CHECKBOXES RECURSOS ESTÁ MARCADO
      modal_esp_recursos.forEach(checkbox => {
        if (recursosSelecionados.includes(checkbox.value)) {
          checkbox.checked = true;
        } else {
          checkbox.checked = false;
        }
      });

      // VERIFICA DE O CHECKBOX ESTÁ MARCADO
      if (esp_status === '1') {
        modal_esp_status.checked = true;
      } else {
        modal_esp_status.checked = false;
      }
    })
  }
</script>

<!-- ITENS DOS SELECTS -->
<script src="../assets/js/351.jquery.min.js"></script>
<script src="includes/select/select_colaboradores.js"></script>
<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- COMPLETO FORM -->
<script src="includes/select/completa_form.js"></script>
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>