<?php include 'includes/header.php'; ?>

<?php include 'includes/nav/header_analise.php'; ?>

<div class="row">
  <div class="col-lg-12">
    <div>
      <?php include 'includes/nav/nav_analise.php'; ?>

      <div class="pt-4">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">

              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col-lg-4 text-lg-start text-center mb-3 mb-lg-0">
                    <h5 class="card-title m-0 ps-2">Linha do Tempo das Atividades</h5>
                  </div>
                  <div class="col-lg-8 d-flex align-items-center d-flex justify-content-lg-end justify-content-center">
                    <nav class="navbar navbar_analise p-0">
                      <form class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1">

                        <?php if ($prop_sta_status == 9) { ?>
                          <button class="btn botao botao_azul_escuro waves-effect" type="button" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_finalizar_proposta">Finalizar</button>
                        <?php } else { ?>
                          <button class="btn botao botao_disabled waves-effect" type="button">Finalizar</button>
                        <?php } ?>

                        <?php if ($prop_sta_status == 7) { ?>
                          <button class="btn botao botao_azul_escuro waves-effect" type="button" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_executar_proposta">Executar Proposta</button>
                        <?php } else { ?>
                          <a class="btn botao botao_disabled waves-effect">Executar Proposta</a>
                        <?php } ?>

                        <?php if ($prop_sta_status == 2 || $prop_sta_status == 3 || $prop_sta_status == 8) { ?>
                          <button class="btn botao botao_verde waves-effect d-block" type="button" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_deferir_proposta">Deferir Proposta</button>
                        <?php } else { ?>
                          <a class="btn botao botao_disabled waves-effect">Deferir Proposta</a>
                        <?php } ?>

                        <?php if ($prop_sta_status == 2 || $prop_sta_status == 3 || $prop_sta_status == 7) { ?>
                          <button class="btn botao botao_vermelho_transparente waves-effect" type="button" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_indeferir_proposta">Indeferir Proposta</button>
                        <?php } else { ?>
                          <a class="btn botao botao_disabled waves-effect">Indeferir Proposta</a>
                        <?php } ?>

                      </form>
                    </nav>
                  </div>
                </div>
              </div>

              <div class="card-body p-4">
                <div class="tab-content">
                  <div class="acitivity-timeline">

                    <?php
                    try {
                      $stmt = $conn->prepare("SELECT * FROM propostas_analise_status
                                              INNER JOIN status_propostas ON status_propostas.stprop_id = propostas_analise_status.sta_an_status
                                              LEFT JOIN admin ON admin.admin_id COLLATE SQL_Latin1_General_CP1_CI_AI = propostas_analise_status.sta_an_user_id
                                              WHERE sta_an_prop_id = '$prop_id'
                                              ORDER BY sta_an_data_upd DESC");
                      $stmt->execute();
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);

                        if (empty($admin_nome)) {
                          $nome_atv  = $user_nome;
                          $color_atv = 'icon_avatar_roxo';
                        } else {
                          $nome_atv  = $admin_nome;
                          $color_atv = 'icon_avatar_azul';
                        }

                        // PEGA O PRIMEIRO NOME E ÚLTIMO NOME
                        $partesNome = explode(" ", $nome_atv);
                        $primeiroNome = $partesNome[0];
                        $ultimoNome = end($partesNome);

                        // PEGA A PRIMEIRO E ÚLTIMO LETRA
                        $firstNameInitial = strtoupper(substr($partesNome[0], 0, 1)); // PEGA A PRIMEIRA LETRA DO PRIMEIRO NOME
                        $lastNameInitial = strtoupper(substr(end($partesNome), 0, 1)); // PEGA A PRIMEIRA LETRA DO ÚLTIMO NOME
                        $iniciais_analise = $firstNameInitial . $lastNameInitial; // RETORNA AS INICIAIS

                        // CONFIGURAÇÃO DO STATUS
                        if ($sta_an_status == 1) {
                          $prop_status_color = 'bg_info_laranja';
                        }
                        if ($sta_an_status == 2) {
                          $prop_status_color = 'bg_info_azul';
                        }
                        if ($sta_an_status == 3) {
                          $prop_status_color = 'bg_info_azul_escuro';
                        }
                        if ($sta_an_status == 4) {
                          $prop_status_color = 'bg_info_azul_escuro';
                        }
                        if ($sta_an_status == 5) {
                          $prop_status_color = 'bg_info_azul_escuro';
                        }
                        if ($sta_an_status == 7) {
                          $prop_status_color = 'bg_info_verde';
                        }
                        if ($sta_an_status == 8) {
                          $prop_status_color = 'bg_info_vermelho';
                        }
                        if ($sta_an_status == 9) {
                          $prop_status_color = 'bg_info_roxo';
                        }
                        if ($sta_an_status == 10) {
                          $prop_status_color = 'bg_info_cinza';
                        }
                    ?>

                        <div class="line_time acitivity-item pb-3 d-flex">
                          <div class="flex-shrink-0">
                            <div class="icon_avatar <?= $color_atv ?>"><?= $iniciais_analise ?></div>
                          </div>
                          <div class="flex-grow-1 ms-3">
                            <span class="badge align-middle mb-2 <?= $prop_status_color ?>"><?= $stprop_status ?></span>
                            <h6 class="mb-1"><?= date("d/m/Y H:i", strtotime($sta_an_data_upd)) . ' ' . $primeiroNome . '&nbsp;' . $ultimoNome ?></h6>
                            <p class="text-muted mt-2"><?= $sta_an_obs ?></p>
                          </div>
                        </div>

                    <?php }
                    } catch (PDOException $e) {
                      // echo "Erro: " . $e->getMessage();
                      echo "Erro ao tentar recuperar os dados";
                    } ?>

                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- DEFERIR PROPOSTA -->
<div class="modal fade modal_padrao" id="modal_deferir_proposta" tabindex="-1" aria-labelledby="modal_deferir_proposta" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_verde">
        <h5 class="modal-title" id="modal_deferir_proposta">Deferir Proposta</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_analise_status.php?funcao=cad_prop_def" class="needs-validation" id="ValidaBotaoProgressDeferir" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control" name="prop_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="prop_codigo" value="<?= $prop_codigo ?>" required>
            <input type="hidden" class="form-control" name="prop_titulo" value="<?= $prop_titulo ?>" required>
            <input type="hidden" class="form-control" name="sta_an_user_email" value="<?= $user_email ?>" required>

            <div class="col-12">
              <div>
                <label class="form-label">Observação <span>*</span></label>
                <textarea class="form-control" name="sta_an_obs" id="" rows="10" required></textarea>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="PropDeferir">Deferir</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- INDEFERIR PROPOSTA -->
<div class="modal fade modal_padrao" id="modal_indeferir_proposta" tabindex="-1" aria-labelledby="modal_indeferir_proposta" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_vermelho">
        <h5 class="modal-title" id="modal_indeferir_proposta">Indeferir Proposta</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_analise_status.php?funcao=cad_prop_indef" class="needs-validation" id="ValidaBotaoProgressIndeferir" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control" name="prop_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="prop_codigo" value="<?= $prop_codigo ?>" required>
            <input type="hidden" class="form-control" name="prop_titulo" value="<?= $prop_titulo ?>" required>
            <input type="hidden" class="form-control" name="sta_an_user_email" value="<?= $user_email ?>" required>

            <div class="col-12">
              <div>
                <label class="form-label">Observação <span>*</span></label>
                <textarea class="form-control" name="sta_an_obs" id="" rows="10" required></textarea>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_vermelho waves-effect">Indeferir</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- EXECUTAR PROPOSTA -->
<div class="modal fade modal_padrao" id="modal_executar_proposta" tabindex="-1" aria-labelledby="modal_executar_proposta" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_executar_proposta">Executar Proposta</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_analise_status.php?funcao=cad_prop_exec" class="needs-validation" id="ValidaBotaoProgressExecuta" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control" name="prop_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="prop_codigo" value="<?= $prop_codigo ?>" required>
            <input type="hidden" class="form-control" name="prop_titulo" value="<?= $prop_titulo ?>" required>
            <input type="hidden" class="form-control" name="sta_an_user_email" value="<?= $user_email ?>" required>

            <div class="col-12">
              <div>
                <label class="form-label">Observação</label>
                <textarea class="form-control" name="sta_an_obs" id="" rows="10"></textarea>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Executar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- FINALIZAR PROPOSTA -->
<div class="modal fade modal_padrao" id="modal_finalizar_proposta" tabindex="-1" aria-labelledby="modal_finalizar_proposta" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_finalizar_proposta">Finalizar Proposta</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_propostas_analise_status.php?funcao=cad_prop_final" class="needs-validation" id="ValidaBotaoProgressFinal" novalidate>

          <div class="row g-3">

            <input type="hidden" class="form-control" name="prop_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="prop_codigo" value="<?= $prop_codigo ?>" required>
            <input type="hidden" class="form-control" name="prop_titulo" value="<?= $prop_titulo ?>" required>
            <input type="hidden" class="form-control" name="sta_an_user_email" value="<?= $user_email ?>" required>

            <div class="col-12">
              <div>
                <label class="form-label">Observação</label>
                <textarea class="form-control" name="sta_an_obs" id="" rows="10"></textarea>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Finalizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>