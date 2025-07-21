<div class="card-body p-sm-4 p-3">

  <form class="needs-validation" method="POST" action="controller/controller_propostas.php" autocomplete="off" novalidate>

    <input type="hidden" class="form-control" name="prop_id" value="<?= base64_encode($prop_id) ?>" required>
    <input type="hidden" class="form-control" name="prop_tipo" value="<?= base64_encode($propc_cat) ?>" required>

    <div class="tit_section" id="pmc_ancora">
      <h3>Materiais de Consumo / Laboratório / Equipamentos / Mobiliário</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="row grid g-3 align-items-center">
          <div class="col-md-12 text-end d-grid d-md-block">
            <a class="btn botao botao_azul_escuro waves-effect" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_material">+ Cadastrar Material</a>
          </div>
        </div>
      </div>

      <table class="table mt-3">
        <thead>
          <tr>
            <th>Material de Consumo</th>
            <th>Quantidade</th>
            <th width="20px"></th>
          </tr>
        </thead>
        <tbody>

          <?php
          try {
            $stmt = $conn->prepare("SELECT pmc_id, pmc_proposta_id, pmc_material_consumo, pmc_quantidade, cms_id, cms_material_servico
                                    FROM propostas_material_consumo
                                    INNER JOIN conf_material_servico ON conf_material_servico.cms_id = propostas_material_consumo.pmc_material_consumo
                                    WHERE pmc_proposta_id = '$prop_id'");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              extract($row);
          ?>

              <tr>
                <th><?= $cms_material_servico ?></th>
                <td><?= $pmc_quantidade ?></td>
                <td class="text-end">
                  <div class="dropdown drop_tabela d-inline-block">
                    <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="ri-more-fill align-middle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_material"
                          data-bs-pmc_id="<?= base64_encode($pmc_id) ?>"
                          data-bs-pmc_proposta_id="<?= base64_encode($pmc_proposta_id) ?>"
                          data-bs-pmc_material_consumo="<?= $pmc_material_consumo ?>"
                          data-bs-pmc_quantidade="<?= $pmc_quantidade ?>"
                          data-bs-cms_id="<?= $cms_id ?>"
                          data-bs-cms_material_servico="<?= $cms_material_servico ?>"
                          title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                      <li><a href="controller/controller_propostas_material.php?funcao=exc_material&pmc_id=<?= base64_encode($pmc_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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

    <div class="tit_section" id="ps_ancora">
      <h3>Serviços</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="row grid g-3 align-items-center">
          <div class="col-md-12 text-end d-grid d-md-block">
            <a class="btn botao botao_azul_escuro waves-effect" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_servico">+ Cadastrar Serviço</a>
          </div>
        </div>
      </div>

      <table class="table mt-3">
        <thead>
          <tr>
            <th>Serviço</th>
            <th>Quantidade</th>
            <th width="20px"></th>
          </tr>
        </thead>
        <tbody>

          <?php
          try {
            $stmt = $conn->prepare("SELECT ps_id, ps_proposta_id, ps_mat_serv_id, ps_quantidade, cms_id, cms_material_servico
                                    FROM propostas_servico
                                    INNER JOIN conf_material_servico ON conf_material_servico.cms_id = propostas_servico.ps_mat_serv_id
                                    WHERE ps_proposta_id = '$prop_id'");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              extract($row);
          ?>

              <tr>
                <th><?= $cms_material_servico ?></th>
                <td><?= $ps_quantidade ?></td>
                <td class="text-end">
                  <div class="dropdown drop_tabela d-inline-block">
                    <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="ri-more-fill align-middle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_servico" data-bs-ps_id="<?= base64_encode($ps_id) ?>" data-bs-ps_proposta_id="<?= base64_encode($ps_proposta_id) ?>" data-bs-ps_mat_serv_id="<?= $ps_mat_serv_id ?>" data-bs-ps_quantidade="<?= $ps_quantidade ?>" data-bs-cms_id="<?= $cms_id ?>" data-bs-cms_material_servico="<?= $cms_material_servico ?>" title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                      <li><a href="controller/controller_propostas_servico.php?funcao=exc_servico&ps_id=<?= base64_encode($ps_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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

    <div class="row grid gx-3">

      <div class="col-12 mt-3">
        <div class="mb-3">
          <label class="form-label">Descreva outros custos envolvidos para implantação de seu projeto</label>
          <textarea class="form-control" name="prop_custos" rows="3"><?= str_replace('<br />', '', $prop_custos) ?></textarea>
        </div>
      </div>

    </div>

    <div class="tit_section" id="rn_ancora">
      <h3>Recursos Necessários</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12 mb-3">
        <!-- <label class="form-label">Área do Conhecimento <span>*</span></label> -->
        <div class="check_item_container">
          <?php
          $recurso = explode(",", $prop_recursos);
          $sql = $conn->query("SELECT * FROM conf_recursos WHERE rec_categoria = $prop_tipo ORDER BY rec_recurso");
          while ($rec = $sql->fetch(PDO::FETCH_ASSOC)) {
            if (in_array($rec['rec_id'], $recurso)) {
              $prop_recursos_checked = "checked";
            } else {
              $prop_recursos_checked = "";
            }
          ?>
            <input type="checkbox" class="btn-check" name="prop_recursos[]" id="checkRecursos<?= $rec['rec_id'] ?>" value="<?= $rec['rec_id'] ?>" <?= $prop_recursos_checked ?>>
            <label class="btn btn-outline-primary check_item" for="checkRecursos<?= $rec['rec_id'] ?>"><?= $rec['rec_recurso'] ?></label>
          <?php } ?>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Descrever a organização do espaço e a dinâmica da atividade</label>
          <textarea class="form-control" name="prop_desc_atividade" rows="3"><?= str_replace('<br />', '', $prop_desc_atividade) ?></textarea>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Recursos áudio e vídeo <span class="label_info">(Todas as salas de aula são equipadas com som, computador e datashow)</span></label>
          <textarea class="form-control" name="prop_rec_audio_video" rows="3"><?= str_replace('<br />', '', $prop_rec_audio_video) ?></textarea>
        </div>
      </div>

      <div class="col-12">
        <div class="mb-3">
          <label class="form-label">Outros</label>
          <textarea class="form-control" name="prop_outros" rows="3"><?= str_replace('<br />', '', $prop_outros) ?></textarea>
        </div>
      </div>

    </div>

    <div class="col-lg-12">
      <div class="hstack gap-3 align-items-center justify-content-between mt-4">
        <a type="buttom" onclick="location.href='cad_proposta.php?tp=<?= base64_encode($propc_cat) ?>&st=<?= base64_encode(2) ?>&i=<?= $_GET['i'] ?>'" class="btn botao btn-light waves-effect">Voltar</a>

        <?php if ($prop_sta_status < 7) { ?>
          <button type="submit" class="btn botao botao_verde waves-effect" name="CadPropostaStep3">Próximo</button>
        <?php } else { ?>
          <a class="btn botao botao_disabled waves-effect">Próximo</a>
        <?php } ?>

      </div>
    </div>


  </form>

</div>

<?php include 'includes/modal/modal_material.php'; ?>
<?php include 'includes/modal/modal_servico.php'; ?>