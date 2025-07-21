<?php include 'includes/header.php'; ?>

<?php include 'includes/nav/header_analise.php'; ?>

<div class="row">
  <div class="col-lg-12">
    <div>
      <?php include 'includes/nav/nav_analise.php'; ?>

      <div class="pt-4">

        <div class="card">
          <?php
          // CONFIGURAÇÃO DA PAGINA ATIVA
          $category = array(1, 2, 3, 4, 5, 6);
          if (!in_array(base64_decode($_GET['c']), $category)) {
            header("Location: painel.php");
          }

          // $stmt = $conn->query("SELECT 
          //                             ic.inscc_id, 
          //                             ic.inscc_categoria, 
          //                             c.cert_prop_id,
          //                             CASE 
          //                                 WHEN c.cert_categoria IS NOT NULL THEN 1 
          //                                 ELSE 0 
          //                             END AS has_certificate
          //                         FROM 
          //                             inscricoes_categorias ic
          //                         LEFT JOIN 
          //                             certificado c
          //                         ON 
          //                             ic.inscc_id = c.cert_categoria AND c.cert_prop_id = '$prop_id'");
          $stmt = $conn->query("SELECT 
                                      DISTINCT ic.inscc_id, 
                                      ic.inscc_categoria, 
                                      c.cert_prop_id,
                                      c.cert_status,
                                      CASE 
                                          WHEN c.cert_categoria IS NOT NULL THEN 1 
                                          ELSE 0 
                                      END AS has_certificate
                                  FROM 
                                      inscricoes_categorias ic
                                  LEFT JOIN 
                                      certificado c
                                  ON 
                                      ic.inscc_id = c.cert_categoria AND c.cert_prop_id = '$prop_id'
                                      --AND c.cert_status = 1
                                      ");
          $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
          function isItemAtivo($itemId)
          {
            return isset($_GET['c']) && base64_decode($_GET['c']) == $itemId;
          }
          ?>

          <div class="card-header px-3">
            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" id="myTab" role="tablist">
              <?php foreach ($menuItems as $item) : ?>
                <li class="nav-item link_nav_perfil">
                  <a href="inscricoes.php?c=<?= base64_encode($item['inscc_id']); ?>&i=<?= $_GET['i'] ?>" class="nav-link text-body <?= isItemAtivo($item['inscc_id']) ? 'active' : ''; ?>"><span class="<?php echo ($item['cert_status'] == 1) ? 'info_cat_cred' : ''; ?>" title="Certificado liberado"></span><span class="<?php echo ($item['has_certificate'] == 1) ? 'info_cat' : ''; ?>" title="Certificado criado"></span> <?= $item['inscc_categoria']; ?></a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <div class="modal-body">

            <form action="controller/controller_inscricoes.php?funcao=adm_inscricoes&i=<?= $_GET['i'] ?>" method="POST" id="myForm">

              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col-lg-4 text-lg-start text-center mb-3 mb-lg-0">

                    <?php
                    $category = base64_decode($_GET['c']);
                    $stmt = $conn->prepare("SELECT inscc_id, inscc_categoria FROM inscricoes_categorias WHERE inscc_id = $category");
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                      <h5 class="card-title m-0 ps-2">Lista de <?= $row['inscc_categoria'] ?></h5>
                    <?php } ?>

                  </div>
                  <div class="col-lg-8 d-block d-sm-flex align-items-center justify-content-lg-end justify-content-center">
                    <nav class="navbar navbar_analise p-0">
                      <div class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1">
                        <button type="submit" class="btn botao botao_vermelho_transparente waves-effect">Excluir Itens Selecionados</button>
                        <!-- <button type="button" class="btn botao botao_roxo waves-effect">Exportar</button> -->
                        <button type="button" class="btn botao botao_azul_escuro waves-effect" data-bs-toggle="modal" data-bs-target="#modal_import_lista">Importar Lista</button>
                        <button type="button" class="btn botao botao_azul_escuro waves-effect" data-bs-toggle="modal" data-bs-target="#modal_cad_participante">+ Cadastrar Inscrito</button>
                      </div>
                    </nav>
                  </div>
                </div>
              </div>

              <?php if (base64_decode($_GET['c']) == 1) { ?>

                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table id="tab_insc_part" class="table dt-responsive nowrap align-middle" style="width:100%">
                      <thead>
                        <tr>
                          <th width="20px" class="rounded-start sorting_disabled" rowspan="1" colspan="1" aria-label=""><input type="checkbox" class="form-check-input" id="marcarTodos"></th>
                          <th>Código</th>
                          <th>Nome Completo</th>
                          <th>CPF</th>
                          <th>E-mail</th>
                          <th>Contato</th>
                          <th width="20px"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        try {
                          $cat = base64_decode($_GET['c']);
                          $id  = base64_decode($_GET['i']);
                          $sql = $conn->query("SELECT * FROM inscricoes WHERE insc_prop_id = '$id' AND insc_categoria = $cat");
                          while ($insc = $sql->fetch(PDO::FETCH_ASSOC)) {
                            extract($insc);

                            //CONFIGURAÇÃO DO CREDENCIAMENTO
                            if ($insc_credenciamento == 1) {
                              $credenciamento_color = 'bg_info_verde';
                            } else {
                              $credenciamento_color = 'bg_info_cinza';
                            }

                            // ADICIONA PONTUAÇÃO AO CPF
                            if (!empty($insc_cpf)) {
                              $nbr_cpf = "$insc_cpf";
                              $parte_um     = substr($nbr_cpf, 0, 3);
                              $parte_dois   = substr($nbr_cpf, 3, 3);
                              $parte_tres   = substr($nbr_cpf, 6, 3);
                              $parte_quatro = substr($nbr_cpf, 9, 2);
                              $insc_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";
                            } else {
                              $insc_cpf = '';
                            }
                        ?>

                            <tr>
                              <td><input type="checkbox" class="form-check-input checkbox" name="exc_selecionados[]" value="<?= $insc_id ?>"></td>
                              <th>
                                <nobr><?= $insc_codigo  ?></nobr>
                              </th>
                              <td>
                                <nobr><?= $insc_nome  ?></nobr>
                              </td>
                              <td>
                                <nobr><?= $insc_cpf  ?></nobr>
                              </td>
                              <td>
                                <nobr><?= $insc_email  ?></nobr>
                              </td>
                              <td>
                                <nobr><?= $insc_contato  ?></nobr>
                              </td>
                              <td class="text-end">
                                <div class="dropdown drop_tabela d-inline-block">
                                  <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ri-more-fill align-middle"></i>
                                  </button>
                                  <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_participante" data-bs-insc_id="<?= base64_encode($insc_id) ?>"
                                        data-bs-insc_prop_id="<?= base64_encode($insc_prop_id) ?>"
                                        data-bs-insc_categoria="<?= base64_encode($insc_categoria) ?>"
                                        data-bs-insc_tipo="<?= $insc_tipo ?>"
                                        data-bs-insc_titulo="<?= $insc_titulo ?>"
                                        data-bs-insc_nome="<?= $insc_nome ?>"
                                        data-bs-insc_nome_coautor="<?= $insc_nome_coautor ?>"
                                        data-bs-insc_cpf="<?= $insc_cpf ?>"
                                        data-bs-insc_contato="<?= $insc_contato ?>"
                                        data-bs-insc_email="<?= $insc_email ?>"
                                        title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                    <li><a href="controller/controller_inscricoes.php?funcao=exc_insc&insc_id=<?= base64_encode($insc_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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

              <?php } ?>

              <?php if (base64_decode($_GET['c']) == 2) { ?>

                <div class="card-body p-0">
                  <table id="tab_insc_part" class="table dt-responsive nowrap align-middle" style="width:100%">
                    <thead>
                      <tr>
                        <th width="20px" class="rounded-start sorting_disabled" rowspan="1" colspan="1" aria-label=""><input type="checkbox" class="form-check-input" id="marcarTodos"></th>
                        <th>Código</th>
                        <th>Nome Completo</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Contato</th>
                        <th width="20px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      try {
                        $cat = base64_decode($_GET['c']);
                        $id = base64_decode($_GET['i']);
                        $sql = $conn->query("SELECT * FROM inscricoes WHERE insc_prop_id = '$id' AND insc_categoria = $cat");
                        while ($insc = $sql->fetch(PDO::FETCH_ASSOC)) {
                          extract($insc);

                          //CONFIGURAÇÃO DO CREDENCIAMENTO
                          if ($insc_credenciamento == 1) {
                            $credenciamento_color = 'bg_info_verde';
                          } else {
                            $credenciamento_color = 'bg_info_cinza';
                          }

                          // ADICIONA PONTUAÇÃO AO CPF
                          if (!empty($insc_cpf)) {
                            $nbr_cpf = "$insc_cpf";
                            $parte_um     = substr($nbr_cpf, 0, 3);
                            $parte_dois   = substr($nbr_cpf, 3, 3);
                            $parte_tres   = substr($nbr_cpf, 6, 3);
                            $parte_quatro = substr($nbr_cpf, 9, 2);
                            $insc_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";
                          } else {
                            $insc_cpf = '';
                          }
                      ?>

                          <tr>
                            <td><input type="checkbox" class="form-check-input checkbox" name="exc_selecionados[]" value="<?= $insc_id ?>"></td>
                            <th>
                              <nobr><?= $insc_codigo  ?></nobr>
                            </th>
                            <td>
                              <nobr><?= $insc_nome  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_cpf  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_email  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_contato  ?></nobr>
                            </td>
                            <td class="text-end">
                              <div class="dropdown drop_tabela d-inline-block">
                                <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                  <i class="ri-more-fill align-middle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                  <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_participante" data-bs-insc_id="<?= base64_encode($insc_id) ?>"
                                      data-bs-insc_prop_id="<?= base64_encode($insc_prop_id) ?>"
                                      data-bs-insc_categoria="<?= base64_encode($insc_categoria) ?>"
                                      data-bs-insc_tipo="<?= $insc_tipo ?>"
                                      data-bs-insc_titulo="<?= $insc_titulo ?>"
                                      data-bs-insc_nome="<?= $insc_nome ?>"
                                      data-bs-insc_nome_coautor="<?= $insc_nome_coautor ?>"
                                      data-bs-insc_cpf="<?= $insc_cpf ?>"
                                      data-bs-insc_contato="<?= $insc_contato ?>"
                                      data-bs-insc_email="<?= $insc_email ?>"
                                      title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                  <li><a href="controller/controller_inscricoes.php?funcao=exc_insc&insc_id=<?= base64_encode($insc_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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

              <?php } ?>

              <?php if (base64_decode($_GET['c']) == 3) { ?>

                <div class="card-body p-0">
                  <table id="tab_insc_minist" class="table dt-responsive nowrap align-middle" style="width:100%">
                    <thead>
                      <tr>
                        <th width="20px" class="rounded-start sorting_disabled" rowspan="1" colspan="1" aria-label=""><input type="checkbox" class="form-check-input" id="marcarTodos"></th>
                        <th>Código</th>
                        <th>Nome Completo</th>
                        <th>Título Atividade</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Contato</th>
                        <th width="20px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      try {
                        $cat = base64_decode($_GET['c']);
                        $id = base64_decode($_GET['i']);
                        $sql = $conn->query("SELECT * FROM inscricoes WHERE insc_prop_id = '$id' AND insc_categoria = $cat");
                        while ($insc = $sql->fetch(PDO::FETCH_ASSOC)) {
                          extract($insc);

                          //CONFIGURAÇÃO DO CREDENCIAMENTO
                          if ($insc_credenciamento == 1) {
                            $credenciamento_color = 'bg_info_verde';
                          } else {
                            $credenciamento_color = 'bg_info_cinza';
                          }

                          // ADICIONA PONTUAÇÃO AO CPF
                          if (!empty($insc_cpf)) {
                            $nbr_cpf = "$insc_cpf";
                            $parte_um     = substr($nbr_cpf, 0, 3);
                            $parte_dois   = substr($nbr_cpf, 3, 3);
                            $parte_tres   = substr($nbr_cpf, 6, 3);
                            $parte_quatro = substr($nbr_cpf, 9, 2);
                            $insc_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";
                          } else {
                            $insc_cpf = '';
                          }
                      ?>

                          <tr>
                            <td><input type="checkbox" class="form-check-input checkbox" name="exc_selecionados[]" value="<?= $insc_id ?>"></td>
                            <th>
                              <nobr><?= $insc_codigo  ?></nobr>
                            </th>
                            <td>
                              <nobr><?= $insc_nome  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_titulo  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_cpf  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_email  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_contato  ?></nobr>
                            </td>
                            <td class="text-end">
                              <div class="dropdown drop_tabela d-inline-block">
                                <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                  <i class="ri-more-fill align-middle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                  <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_participante" data-bs-insc_id="<?= base64_encode($insc_id) ?>"
                                      data-bs-insc_prop_id="<?= base64_encode($insc_prop_id) ?>"
                                      data-bs-insc_categoria="<?= base64_encode($insc_categoria) ?>"
                                      data-bs-insc_tipo="<?= $insc_tipo ?>"
                                      data-bs-insc_titulo="<?= $insc_titulo ?>"
                                      data-bs-insc_nome="<?= $insc_nome ?>"
                                      data-bs-insc_nome_coautor="<?= $insc_nome_coautor ?>"
                                      data-bs-insc_cpf="<?= $insc_cpf ?>"
                                      data-bs-insc_contato="<?= $insc_contato ?>"
                                      data-bs-insc_email="<?= $insc_email ?>"
                                      title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                  <li><a href="controller/controller_inscricoes.php?funcao=exc_insc&insc_id=<?= base64_encode($insc_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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

              <?php } ?>

              <?php if (base64_decode($_GET['c']) == 4) { ?>

                <div class="card-body p-0">
                  <table id="tab_insc_part_apres" class="table dt-responsive nowrap align-middle" style="width:100%">
                    <thead>
                      <tr>
                        <th width="20px" class="rounded-start sorting_disabled" rowspan="1" colspan="1" aria-label=""><input type="checkbox" class="form-check-input" id="marcarTodos"></th>
                        <th>Código</th>
                        <th>Nome Completo</th>
                        <th>Título do Trabalho</th>
                        <th>Tipo</th>
                        <th>Nome do Coautor</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Contato</th>
                        <th width="20px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      try {
                        $cat = base64_decode($_GET['c']);
                        $id = base64_decode($_GET['i']);
                        $sql = $conn->query("SELECT * FROM inscricoes WHERE insc_prop_id = '$id' AND insc_categoria = $cat");
                        while ($insc = $sql->fetch(PDO::FETCH_ASSOC)) {
                          extract($insc);

                          //CONFIGURAÇÃO DO CREDENCIAMENTO
                          if ($insc_credenciamento == 1) {
                            $credenciamento_color = 'bg_info_verde';
                          } else {
                            $credenciamento_color = 'bg_info_cinza';
                          }

                          // ADICIONA PONTUAÇÃO AO CPF
                          if (!empty($insc_cpf)) {
                            $nbr_cpf = "$insc_cpf";
                            $parte_um     = substr($nbr_cpf, 0, 3);
                            $parte_dois   = substr($nbr_cpf, 3, 3);
                            $parte_tres   = substr($nbr_cpf, 6, 3);
                            $parte_quatro = substr($nbr_cpf, 9, 2);
                            $insc_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";
                          } else {
                            $insc_cpf = '';
                          }
                      ?>

                          <tr>
                            <td><input type="checkbox" class="form-check-input checkbox" name="exc_selecionados[]" value="<?= $insc_id ?>"></td>
                            <th>
                              <nobr><?= $insc_codigo  ?></nobr>
                            </th>
                            <td>
                              <nobr><?= $insc_nome  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_titulo  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_tipo  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_nome_coautor  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_cpf  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_email  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_contato  ?></nobr>
                            </td>
                            <td class="text-end">
                              <div class="dropdown drop_tabela d-inline-block">
                                <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                  <i class="ri-more-fill align-middle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                  <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_participante" data-bs-insc_id="<?= base64_encode($insc_id) ?>"
                                      data-bs-insc_prop_id="<?= base64_encode($insc_prop_id) ?>"
                                      data-bs-insc_categoria="<?= base64_encode($insc_categoria) ?>"
                                      data-bs-insc_tipo="<?= $insc_tipo ?>"
                                      data-bs-insc_titulo="<?= $insc_titulo ?>"
                                      data-bs-insc_nome="<?= $insc_nome ?>"
                                      data-bs-insc_nome_coautor="<?= $insc_nome_coautor ?>"
                                      data-bs-insc_cpf="<?= $insc_cpf ?>"
                                      data-bs-insc_contato="<?= $insc_contato ?>"
                                      data-bs-insc_email="<?= $insc_email ?>"
                                      title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                  <li><a href="controller/controller_inscricoes.php?funcao=exc_insc&insc_id=<?= base64_encode($insc_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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

              <?php } ?>

              <?php if (base64_decode($_GET['c']) == 5) { ?>

                <div class="card-body p-0">
                  <table id="tab_insc_part" class="table dt-responsive nowrap align-middle" style="width:100%">
                    <thead>
                      <tr>
                        <th width="20px" class="rounded-start sorting_disabled" rowspan="1" colspan="1" aria-label=""><input type="checkbox" class="form-check-input" id="marcarTodos"></th>
                        <th>Código</th>
                        <th>Nome Completo</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Contato</th>
                        <th width="20px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      try {
                        $cat = base64_decode($_GET['c']);
                        $id = base64_decode($_GET['i']);
                        $sql = $conn->query("SELECT * FROM inscricoes WHERE insc_prop_id = '$id' AND insc_categoria = $cat");
                        while ($insc = $sql->fetch(PDO::FETCH_ASSOC)) {
                          extract($insc);

                          //CONFIGURAÇÃO DO CREDENCIAMENTO
                          if ($insc_credenciamento == 1) {
                            $credenciamento_color = 'bg_info_verde';
                          } else {
                            $credenciamento_color = 'bg_info_cinza';
                          }

                          // ADICIONA PONTUAÇÃO AO CPF
                          if (!empty($insc_cpf)) {
                            $nbr_cpf = "$insc_cpf";
                            $parte_um     = substr($nbr_cpf, 0, 3);
                            $parte_dois   = substr($nbr_cpf, 3, 3);
                            $parte_tres   = substr($nbr_cpf, 6, 3);
                            $parte_quatro = substr($nbr_cpf, 9, 2);
                            $insc_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";
                          } else {
                            $insc_cpf = '';
                          }
                      ?>

                          <tr>
                            <td><input type="checkbox" class="form-check-input checkbox" name="exc_selecionados[]" value="<?= $insc_id ?>"></td>
                            <th>
                              <nobr><?= $insc_codigo  ?></nobr>
                            </th>
                            <td>
                              <nobr><?= $insc_nome  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_cpf  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_email  ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_contato  ?></nobr>
                            </td>
                            <td class="text-end">
                              <div class="dropdown drop_tabela d-inline-block">
                                <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                  <i class="ri-more-fill align-middle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                  <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_participante" data-bs-insc_id="<?= base64_encode($insc_id) ?>"
                                      data-bs-insc_prop_id="<?= base64_encode($insc_prop_id) ?>"
                                      data-bs-insc_categoria="<?= base64_encode($insc_categoria) ?>"
                                      data-bs-insc_tipo="<?= $insc_tipo ?>"
                                      data-bs-insc_titulo="<?= $insc_titulo ?>"
                                      data-bs-insc_nome="<?= $insc_nome ?>"
                                      data-bs-insc_nome_coautor="<?= $insc_nome_coautor ?>"
                                      data-bs-insc_cpf="<?= $insc_cpf ?>"
                                      data-bs-insc_contato="<?= $insc_contato ?>"
                                      data-bs-insc_email="<?= $insc_email ?>"
                                      title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                  <li><a href="controller/controller_inscricoes.php?funcao=exc_insc&insc_id=<?= base64_encode($insc_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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

              <?php } ?>

              <?php if (base64_decode($_GET['c']) == 6) { ?>

                <div class="card-body p-0">
                  <table id="tab_insc_part_atividade" class="table dt-responsive nowrap align-middle" style="width:100%">
                    <thead>
                      <tr>
                        <th width="20px" class="rounded-start sorting_disabled" rowspan="1" colspan="1" aria-label=""><input type="checkbox" class="form-check-input" id="marcarTodos"></th>
                        <th>Código</th>
                        <th>Nome Completo</th>
                        <th>Tipo</th>
                        <th>CPF</th>
                        <th>E-mail</th>
                        <th>Contato</th>
                        <th width="20px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      try {
                        $cat = base64_decode($_GET['c']);
                        $id = base64_decode($_GET['i']);
                        $sql = $conn->query("SELECT * FROM inscricoes WHERE insc_prop_id = '$id' AND insc_categoria = $cat");
                        while ($insc = $sql->fetch(PDO::FETCH_ASSOC)) {
                          extract($insc);

                          //CONFIGURAÇÃO DO CREDENCIAMENTO
                          if ($insc_credenciamento == 1) {
                            $credenciamento_color = 'bg_info_verde';
                          } else {
                            $credenciamento_color = 'bg_info_cinza';
                          }

                          // ADICIONA PONTUAÇÃO AO CPF
                          if (!empty($insc_cpf)) {
                            $nbr_cpf = "$insc_cpf";
                            $parte_um     = substr($nbr_cpf, 0, 3);
                            $parte_dois   = substr($nbr_cpf, 3, 3);
                            $parte_tres   = substr($nbr_cpf, 6, 3);
                            $parte_quatro = substr($nbr_cpf, 9, 2);
                            $insc_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";
                          } else {
                            $insc_cpf = '';
                          }
                      ?>

                          <tr>
                            <td><input type="checkbox" class="form-check-input checkbox" name="exc_selecionados[]" value="<?= $insc_id ?>"></td>
                            <th>
                              <nobr><?= $insc_codigo ?></nobr>
                            </th>
                            <td>
                              <nobr><?= $insc_nome ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_tipo ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_cpf ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_email ?></nobr>
                            </td>
                            <td>
                              <nobr><?= $insc_contato ?></nobr>
                            </td>
                            <td class="text-end">
                              <div class="dropdown drop_tabela d-inline-block">
                                <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                  <i class="ri-more-fill align-middle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                  <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_participante" data-bs-insc_id="<?= base64_encode($insc_id) ?>"
                                      data-bs-insc_prop_id="<?= base64_encode($insc_prop_id) ?>"
                                      data-bs-insc_categoria="<?= base64_encode($insc_categoria) ?>"
                                      data-bs-insc_tipo="<?= $insc_tipo ?>"
                                      data-bs-insc_titulo="<?= $insc_titulo ?>"
                                      data-bs-insc_nome="<?= $insc_nome ?>"
                                      data-bs-insc_nome_coautor="<?= $insc_nome_coautor ?>"
                                      data-bs-insc_cpf="<?= $insc_cpf ?>"
                                      data-bs-insc_contato="<?= $insc_contato ?>"
                                      data-bs-insc_email="<?= $insc_email ?>"
                                      title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                                  <li><a href="controller/controller_inscricoes.php?funcao=exc_insc&insc_id=<?= base64_encode($insc_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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

              <?php } ?>

            </form>

            <script>
              // MARCA TODOS OS CHECKBOX NA TABELA
              document.getElementById('marcarTodos').addEventListener('click', function() {
                let checkboxes = document.querySelectorAll('input[name="exc_selecionados[]"]');
                for (let checkbox of checkboxes) {
                  checkbox.checked = this.checked;
                }
              });
              // BOTÃO PARA EXCLUIR SÓ HABILITA SE MARCAR ALGUM CHECKBOX
              const myForm = document.getElementById('myForm');
              const checkboxes = document.querySelectorAll('.checkbox');
              myForm.addEventListener('submit', (event) => {
                const checkedCheckboxes = Array.from(checkboxes).some(checkbox => checkbox.checked);
                if (!checkedCheckboxes) {
                  event.preventDefault(); // Impede o envio do formulário
                  Swal.fire({
                    icon: 'error',
                    title: 'Nada Selecionado',
                    confirmButtonText: 'Ok',
                    text: 'Por favor, selecione pelo menos um item da tabela.'
                  });
                } else {
                  event.preventDefault();
                  Swal.fire({
                    text: 'Deseja excluir os itens selecionados?',
                    // title: "You won't be able to revert this!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0461AD',
                    cancelButtonColor: '#C4453E',
                    confirmButtonText: 'Excluir',
                    cancelButtonText: 'Cancelar',
                    // reverseButtons: true
                  }).then((result) => {
                    if (result.isConfirmed) {
                      document.getElementById('myForm').submit();
                    }
                  })
                }
              });
            </script>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- CADASTRAR INSCRIÇÃO -->
<div class="modal fade modal_padrao" id="modal_cad_participante" tabindex="-1" aria-labelledby="modal_cad_participante" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_cad_participante">Cadastrar Inscrito</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_inscricoes.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control" name="insc_prop_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="insc_categoria" value="<?= $_GET['c'] ?>" required>

            <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
              <div class="col-12">
                <div>
                  <label class="form-label">Tipo <span>*</span></label>
                  <input type="text" class="form-control text-uppercase" name="insc_tipo" maxlength="100" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            <?php } ?>

            <?php if (base64_decode($_GET['c']) == 3) { ?>
              <div class="col-12">
                <div>
                  <label class="form-label">Título da Atividade <span>*</span></label>
                  <input type="text" class="form-control text-uppercase" name="insc_titulo" maxlength="200" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            <?php } ?>

            <?php if (base64_decode($_GET['c']) == 4) { ?>
              <div class="col-12">
                <div>
                  <label class="form-label">Título do Trabalho <span>*</span></label>
                  <input type="text" class="form-control text-uppercase" name="insc_titulo" maxlength="200" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            <?php } ?>

            <div class="col-12">
              <div>
                <label class="form-label">Nome Completo <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="insc_nome" maxlength="100" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <?php if (base64_decode($_GET['c']) == 4) { ?>
              <div class="col-12">
                <div>
                  <label class="form-label">Nome do Coautor <span>*</span></label>
                  <input type="text" class="form-control text-uppercase" name="insc_nome_coautor" maxlength="100" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            <?php } ?>

            <div class="col-md-6">
              <div>
                <label class="form-label">CPF <span>*</span></label>
                <input type="text" class="form-control text-uppercase cpf" name="insc_cpf" id="idCpfInscCad" onblur="return valCPFInsc(this.value)" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-md-6">
              <div>
                <label class="form-label">Contato</label>
                <input type="text" class="form-control text-uppercase cel_tel" name="insc_contato">
              </div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">E-mail <span>*</span></label>
                <input type="email" class="form-control text-lowercase" name="insc_email" maxlength="100" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="CadInscricao">Cadastrar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- EDITAR INSCRIÇÃO -->
<div class="modal fade modal_padrao" id="modal_edit_participante" tabindex="-1" aria-labelledby="modal_edit_participante" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_edit_participante">Editar Participante</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="controller/controller_inscricoes.php" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control insc_id" name="insc_id" required>
            <input type="hidden" class="form-control insc_prop_id" name="insc_prop_id" required>
            <input type="hidden" class="form-control insc_categoria" name="insc_categoria" required>

            <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
              <div class="col-12">
                <div>
                  <label class="form-label">Tipo <span>*</span></label>
                  <input type="text" class="form-control text-uppercase insc_tipo" name="insc_tipo" maxlength="100" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            <?php } ?>

            <?php if (base64_decode($_GET['c']) == 3) { ?>
              <div class="col-12">
                <div>
                  <label class="form-label">Título da Atividade <span>*</span></label>
                  <input type="text" class="form-control text-uppercase insc_titulo" name="insc_titulo" maxlength="200" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            <?php } ?>

            <?php if (base64_decode($_GET['c']) == 4) { ?>
              <div class="col-12">
                <div>
                  <label class="form-label">Título do Trabalho <span>*</span></label>
                  <input type="text" class="form-control text-uppercase insc_titulo" name="insc_titulo" maxlength="200" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            <?php } ?>

            <div class="col-12">
              <div>
                <label class="form-label">Nome Completo <span>*</span></label>
                <input type="text" class="form-control text-uppercase insc_nome" name="insc_nome" maxlength="100" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <?php if (base64_decode($_GET['c']) == 4) { ?>
              <div class="col-12">
                <div>
                  <label class="form-label">Nome do Coautor <span>*</span></label>
                  <input type="text" class="form-control text-uppercase insc_nome_coautor" name="insc_nome_coautor" maxlength="100" required>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            <?php } ?>

            <div class="col-md-6">
              <div>
                <label class="form-label">CPF <span>*</span></label>
                <input type="text" class="form-control cpf insc_cpf" name="insc_cpf" id="idCpfInscEdit" onblur="return valCPFInscEdit(this.value)" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-md-6">
              <div>
                <label class="form-label">Contato</label>
                <input type="text" class="form-control insc_contato cel_tel" name="insc_contato">
              </div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">E-mail <span>*</span></label>
                <input type="email" class="form-control text-lowercase insc_email" name="insc_email" maxlength="100" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect" name="EditInscricao">Atualizar</button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const modal_edit_participante = document.getElementById('modal_edit_participante')
  if (modal_edit_participante) {
    modal_edit_participante.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      const insc_id = button.getAttribute('data-bs-insc_id')
      const insc_prop_id = button.getAttribute('data-bs-insc_prop_id')
      const insc_categoria = button.getAttribute('data-bs-insc_categoria')
      <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
        const insc_tipo = button.getAttribute('data-bs-insc_tipo')
      <?php } ?>
      <?php if (base64_decode($_GET['c']) == 3 || base64_decode($_GET['c']) == 4) { ?>
        const insc_titulo = button.getAttribute('data-bs-insc_titulo')
      <?php } ?>
      const insc_nome = button.getAttribute('data-bs-insc_nome')
      <?php if (base64_decode($_GET['c']) == 4) { ?>
        const insc_nome_coautor = button.getAttribute('data-bs-insc_nome_coautor')
      <?php } ?>
      const insc_cpf = button.getAttribute('data-bs-insc_cpf')
      const insc_contato = button.getAttribute('data-bs-insc_contato')
      const insc_email = button.getAttribute('data-bs-insc_email')
      // 
      const modalTitle = modal_edit_participante.querySelector('.modal-title')
      const modal_insc_id = modal_edit_participante.querySelector('.insc_id')
      const modal_insc_prop_id = modal_edit_participante.querySelector('.insc_prop_id')
      const modal_insc_categoria = modal_edit_participante.querySelector('.insc_categoria')
      <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
        const modal_insc_tipo = modal_edit_participante.querySelector('.insc_tipo')
      <?php } ?>
      <?php if (base64_decode($_GET['c']) == 3 || base64_decode($_GET['c']) == 4) { ?>
        const modal_insc_titulo = modal_edit_participante.querySelector('.insc_titulo')
      <?php } ?>
      const modal_insc_nome = modal_edit_participante.querySelector('.insc_nome')
      <?php if (base64_decode($_GET['c']) == 4) { ?>
        const modal_insc_nome_coautor = modal_edit_participante.querySelector('.insc_nome_coautor')
      <?php } ?>
      const modal_insc_cpf = modal_edit_participante.querySelector('.insc_cpf')
      const modal_insc_contato = modal_edit_participante.querySelector('.insc_contato')
      const modal_insc_email = modal_edit_participante.querySelector('.insc_email')
      //
      modalTitle.textContent = 'Atualizar Dados'
      modal_insc_id.value = insc_id
      modal_insc_prop_id.value = insc_prop_id
      modal_insc_categoria.value = insc_categoria
      <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
        modal_insc_tipo.value = insc_tipo
      <?php } ?>
      <?php if (base64_decode($_GET['c']) == 3 || base64_decode($_GET['c']) == 4) { ?>
        modal_insc_titulo.value = insc_titulo
      <?php } ?>
      modal_insc_nome.value = insc_nome
      <?php if (base64_decode($_GET['c']) == 4) { ?>
        modal_insc_nome_coautor.value = insc_nome_coautor
      <?php } ?>
      modal_insc_cpf.value = insc_cpf
      modal_insc_contato.value = insc_contato
      modal_insc_email.value = insc_email
    })
  }
</script>

<!-- IMPORTAR LISTA -->
<div class="modal fade modal_padrao" id="modal_import_lista" tabindex="-1" aria-labelledby="modal_import_lista" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_import_lista">Importar Lista de Inscritos</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">

        <p>Para que o upload do arquivo seja realizado, alguns requisitos precisam ser atendidos.</p>

        <div class="card_info_modal">
          <p>1. O arquivo precisar ser do formato  <strong>.CSV</strong>.</p>
          <p>2. O arquivo não pode ultrapassar <strong>2MB</strong>.</p>

          <?php if (base64_decode($_GET['c']) == 1) { ?>
            <p>3. Utilize o arquivo modelo para listar os dados dos participantes. Para baixar o arquivo, <a href="../assets/images/arquivos/participantes.csv">clique aqui</a>.</p>
          <?php } ?>

          <?php if (base64_decode($_GET['c']) == 2) { ?>
            <p>3. Utilize o arquivo modelo para listar os dados os organizadores. Para baixar o arquivo, <a href="../assets/images/arquivos/organizadores.csv">clique aqui</a>.</p>
          <?php } ?>

          <?php if (base64_decode($_GET['c']) == 3) { ?>
            <p>3. Utilize o arquivo modelo para listar os dados dos ministrantes. Para baixar o arquivo, <a href="../assets/images/arquivos/ministrantes.csv">clique aqui</a>.</p>
          <?php } ?>

          <?php if (base64_decode($_GET['c']) == 4) { ?>
            <p>3. Utilize o arquivo modelo para listar os dados das apresentações de trabalho. Para baixar o arquivo, <a href="../assets/images/arquivos/apresentacao_trabalho.csv">clique aqui</a>.</p>
          <?php } ?>

          <?php if (base64_decode($_GET['c']) == 5) { ?>
            <p>3. Utilize o arquivo modelo para listar os dados dos avaliadores. Para baixar o arquivo, <a href="../assets/images/arquivos/avaliadores.csv">clique aqui</a>.</p>
          <?php } ?>

          <?php if (base64_decode($_GET['c']) == 6) { ?>
            <p>3. Utilize o arquivo modelo para listar os dados das atividades extras. Para baixar o arquivo, <a href="../assets/images/arquivos/atividade_extra.csv">clique aqui</a>.</p>
          <?php } ?>

          <p>4. Insira os dados com a mesma formatação apresentada no arquivo modelo.</p>
          <?php if (base64_decode($_GET['c']) == 3) { ?>
            <p>5. As colunas <strong>NOME</strong>, <strong>CPF</strong>, <strong>E-MAIL</strong> e <strong>TÍTULO</strong> não podem conter campos vazios ou dados duplicados.</p>
          <?php } else if (base64_decode($_GET['c']) == 4) { ?>
            <p>5. As colunas <strong>NOME</strong>, <strong>CPF</strong>, <strong>E-MAIL</strong>, <strong>TIPO</strong>, <strong>TÍTULO</strong> e <strong>COAUTOR</strong> não podem conter campos vazios ou dados duplicados.</p>
          <?php } else if (base64_decode($_GET['c']) == 6) { ?>
            <p>5. As colunas <strong>NOME</strong>, <strong>CPF</strong>, <strong>E-MAIL</strong> e <strong>TIPO</strong> não podem conter campos vazios ou dados duplicados.</p>
          <?php } else { ?>
            <p>5. As colunas <strong>NOME</strong>, <strong>CPF</strong> e <strong>E-MAIL</strong> não podem conter campos vazios ou dados duplicados.</p>
          <?php } ?>
          <p>6. Para importar com os inscritos já credenciados, adicione o valor um (1) na coluna <strong>CREDENCIAMENTO</strong>.</p>
        </div>

        <form method="POST" action="controller/controller_inscricoes.php" enctype="multipart/form-data" class="needs-validation" novalidate>
          <div class="row g-3">

            <input type="hidden" class="form-control" name="insc_prop_id" value="<?= $_GET['i'] ?>" required>
            <input type="hidden" class="form-control" name="insc_categoria" value="<?= $_GET['c'] ?>" required>

            <div class="col-12">
              <div>
                <label class="form-label">Anexar arquivo <span>*</span></label>
                <input type="file" class="form-control" name="arquivo" aria-describedby="inputFile" aria-label="Upload" id="inputFile" accept=".csv" required>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                <button type="submit" class="btn botao botao_verde waves-effect">Importar</button>
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
<!-- VALIDA CPF -->
<script src="../assets/js/valida_cpf.js"></script>