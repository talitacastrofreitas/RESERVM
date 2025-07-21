<?php include 'includes/header.php'; ?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

<!-- start page title -->
<div class="row breadcrumb_painel">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Propostas</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="">Propostas</a></li>
          <li class="breadcrumb-item active">Propostas</li>
        </ol>
      </div>
    </div>
  </div>
</div>
<!-- end page title -->

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">

        <form id="formBusca">
          <div class="row g-3">
            <div class="col-xl-7 col-lg-12 col-sm-12">
              <div class="search-box">
                <input type="search" class="form-control text-uppercase" id="inputBusca" autocomplete="off">
                <i class="ri-search-line search-icon"></i>
              </div>
            </div>
            <div class="col-xl-2 col-lg-5 col-md-5">
              <div>
                <select class="form-select" name="admin_perfil" id="inputCat">
                  <option selected disabled value="all">CATEGORIAS</option>
                  <option value="all">TODAS</option>
                  <?php $sql = $conn->query("SELECT propc_id, propc_categoria FROM propostas_categorias ORDER BY propc_id");
                  while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $propc['propc_categoria'] ?>"><?= $propc['propc_categoria'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-xl-2 col-lg-5 col-md-5">
              <div>
                <select class="form-select" name="admin_perfil" id="inputStatus">
                  <option selected disabled value="all">STATUS</option>
                  <option value="all">TODOS</option>
                  <?php $sql = $conn->query("SELECT stprop_id, stprop_status FROM status_propostas ORDER BY stprop_id");
                  while ($stprop = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $stprop['stprop_id'] ?>"><?= $stprop['stprop_status'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
            <div class="col-xl-1 col-lg-2 col-md-2">
              <button type="submit" class="btn botao botao_azul_escuro waves-effect w-100">Filtrar</button>
            </div>
            <!--end col-->
          </div>
          <!--end row-->
        </form>

        <!-- filter card -->
        <script src="../assets/js/filter_card_admin.js"></script>

      </div>
    </div>
  </div>
</div>
<!-- end row -->


<?php
// QUANTIDADE DE PROPOSTA CADASTRADAS PARA O USUÁRIO LOGADO
$query = "SELECT prop_id FROM propostas";
$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
$stmt->execute();
$total_prop = $stmt->rowCount();

// QUANTIDADE DE PROPOSTA CADASTRADAS PARA O USUÁRIO LOGADO POR CATEGORIA
$query = "SELECT prop_id FROM propostas WHERE prop_tipo = 1";
$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
$stmt->execute();
$total_prop_curso = $stmt->rowCount();
//
$query = "SELECT prop_id FROM propostas WHERE prop_tipo = 2";
$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
$stmt->execute();
$total_prop_ec = $stmt->rowCount();
//
$query = "SELECT prop_id FROM propostas WHERE prop_tipo = 3";
$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
$stmt->execute();
$total_prop_prog = $stmt->rowCount();
//
$query = "SELECT prop_id FROM propostas WHERE prop_tipo = 4";
$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
$stmt->execute();
$total_prop_parc = $stmt->rowCount();
//
$query = "SELECT prop_id FROM propostas WHERE prop_tipo = 5";
$stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
$stmt->execute();
$total_prop_ecops = $stmt->rowCount();
?>

<div class="row">
  <div class="col-lg-12">
    <div class="d-flex align-items-center mb-3">
      <div class="flex-grow-1">
        <p class="fs-13 mb-0 text-white">Total de Propostas: <span id="resultCount"><?= $total_prop ?></span></p>
      </div>
      <div class="legenda">
        <div class="item_legenda legend_verde"><?= $total_prop_curso ?></div>
        <div class="item_legenda legend_laranja"><?= $total_prop_ec ?></div>
        <div class="item_legenda legend_azul"><?= $total_prop_prog ?></div>
        <div class="item_legenda legend_roxo"><?= $total_prop_parc ?></div>
        <div class="item_legenda legend_rosa"><?= $total_prop_ecops ?></div>
      </div>
    </div>
  </div>
</div>
<!-- end row -->

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-4" id="job-list">

  <?php
  try {
    $sql = "SELECT * FROM propostas
            INNER JOIN propostas_categorias ON propostas_categorias.propc_id = propostas.prop_tipo
            LEFT JOIN propostas_status ON propostas_status.prop_sta_prop_id = propostas.prop_id
            LEFT JOIN status_propostas ON status_propostas.stprop_id = propostas_status.prop_sta_status
            INNER JOIN usuarios ON usuarios.user_id = propostas.prop_user_id
            ORDER BY prop_data_upd DESC";
    $stmt = $conn->query($sql);
    while ($prop = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($prop);

      // NOME PROPONENTE
      if (empty($user_nome_social)) {
        $proponente = $user_nome;
      } else {
        $proponente = $user_nome_social;
      }

      // CONFIGURAÇÃO DO STATUS
      if ($prop_sta_status == 1) {
        $prop_status_color = 'bg_info_laranja';
      }
      if ($prop_sta_status == 2) {
        $prop_status_color = 'bg_info_azul';
      }
      if ($prop_sta_status == 3) {
        $prop_status_color = 'bg_info_azul_escuro';
      }
      if ($prop_sta_status == 4) {
        $prop_status_color = 'bg_info_azul_escuro';
      }
      if ($prop_sta_status == 5) {
        $prop_status_color = 'bg_info_azul_escuro';
      }
      if ($prop_sta_status == 7) {
        $prop_status_color = 'bg_info_verde';
      }
      if ($prop_sta_status == 8) {
        $prop_status_color = 'bg_info_vermelho';
      }
      if ($prop_sta_status == 9) {
        $prop_status_color = 'bg_info_roxo';
      }
      if ($prop_sta_status == 10) {
        $prop_status_color = 'bg_info_cinza';
      }

      // CONFIGURAÇÃO DOS ÍCONES DAS CATEGORIAS
      if ($prop_tipo == 1) {
        $color_card = 'dados_categoria_verde';
        $icone_card = 'icon_proj_curso_branco.svg';
      } else if ($prop_tipo == 2) {
        $color_card = 'dados_categoria_laranja';
        $icone_card = 'icon_proj_evento_branco.svg';
      } else if ($prop_tipo == 3) {
        $color_card = 'dados_categoria_azul';
        $icone_card = 'icon_prog_ext_branco.svg';
      } else if ($prop_tipo == 4) {
        $color_card = 'dados_categoria_roxo';
        $icone_card = 'icon_parceria_branco.svg';
      } else if ($prop_tipo == 5) {
        $color_card = 'dados_categoria_rosa';
        $icone_card = 'icon_prest_serv_branco.svg';
      } else {
        header("Location: sair.php");
      }
  ?>

      <div class="col filter_card" data-categoria="<?= $propc_categoria ?>" data-codigo="<?= $prop_codigo ?>" data-titulo="<?= $prop_titulo ?>" data-proponente="<?= $proponente ?>" data-data_upd="<?= date('d/m/Y', strtotime($prop_data_upd)) ?>" data-status="<?= $prop_sta_status ?>">
        <div class="card card_propostas card-animate d-flex align-items-end flex-column" onclick="location.href='atividades.php?i=<?= base64_encode($prop_id) ?>'">
          <div class="d-flex justify-content-between w-100">
            <div class="dados_categoria">
              <div class="dados_categoria_icone <?= $color_card ?>">
                <img src="../assets/img/icones/<?= $icone_card ?>" alt="">
              </div>
              <p class="limita_texto" title="<?= $propc_categoria ?>"><?= $propc_categoria ?></p>
            </div>

            <div class="dropdown drop_tabela">
              <button class="btn btn_soft_azul_escuro btn-sm" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text-uppercase"><i class="ri-more-fill align-middle"></i></span>
              </button>
              <div class="icon_card_nav dropdown-menu dropdown-menu-end">
                <a href="atividades.php?c=<?= base64_encode(1) ?>&i=<?= base64_encode($prop_id) ?>" class="dropdown-item" title="Atividades"><i class="fa-solid fa-chart-line"></i> Atividades</a>

                <a href="proposta_analise.php?c=<?= base64_encode(1) ?>&i=<?= base64_encode($prop_id) ?>" class="dropdown-item" title="Atividades"><i class="fa-regular fa-file-lines"></i> Dados da Proposta</a>

                <a href="inscricoes.php?c=<?= base64_encode(1) ?>&i=<?= base64_encode($prop_id) ?>" class="dropdown-item" title="Inscrições"><i class="fa-solid fa-user-group"></i> Inscrições</a>

                <a href="credenciamento.php?c=<?= base64_encode(1) ?>&i=<?= base64_encode($prop_id) ?>" class="dropdown-item" title="Credenciamento"><i class="fa-regular fa-id-badge"></i> Credenciamento</a>

                <a href="certificado.php?c=<?= base64_encode(1) ?>&i=<?= base64_encode($prop_id) ?>" class="dropdown-item" title="Certificado"><i class="fa-solid fa-award fs-5"></i> Certificado</a>

                <a href="controller/controller_propostas_excluir.php?i=<?= base64_encode($prop_id) ?>&cod=<?= base64_encode($prop_codigo) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i> Excluir</a>
              </div>
            </div>
          </div>

          <div class="dados_proposta">
            <p><?= $prop_codigo ?></p>
            <?php if ($prop_tipo == 4) { ?>
              <h5 class="limita_texto" title="<?= $prop_parc_titulo_atividade ?>"><?= $prop_parc_titulo_atividade ?></h5>
            <?php } else { ?>
              <h5 class="limita_texto" title="<?= $prop_titulo ?>"><?= $prop_titulo ?></h5>
            <?php } ?>
          </div>

          <div class="dados_proposta_proponente">
            <p>Proponente</p>
            <h4 class="text-truncate" title="<?= $proponente ?>"><?= $proponente ?></h4>
          </div>

          <div class="mt-auto w-100">
            <div class="dados_proposta_data_status">
              <p><i class="ri-time-line"></i> <?= date('d/m/Y H:i', strtotime($prop_data_upd)) ?></p>
              <span class="badge badge <?= $prop_status_color ?>"><?= $stprop_status ?></span>
            </div>

            <div class="dados_proposta_etapas">
              <div class="dados_proposta_etapas_status">

                <?php if ($prop_tipo != 4) { ?>

                  <div class="<?php if ($prop_status_etapa) {
                                echo 'ativo';
                              } ?>" title="INFORMAÇÕES PRELIMINARES"></div>

                  <div class="<?php if ($prop_status_etapa >= 2) {
                                echo 'ativo';
                              } ?>" title="EQUIPE EXECUTORA"></div>

                  <div class="<?php if ($prop_status_etapa >= 3) {
                                echo 'ativo';
                              } ?>" title="INFRAESTRUTURA E RECURSOS NECESSÁRIOS"></div>

                  <div class="<?php if ($prop_status_etapa >= 4) {
                                echo 'ativo';
                              } ?>" title="DIVULGAÇÃO E PROMOÇÃO DA ATIVIDADE"></div>

                  <div class="<?php if ($prop_status_etapa >= 5) {
                                echo 'ativo';
                              } ?>" title="CONCLUIR"></div>

                <?php } else { ?>

                  <div class="<?php if ($prop_status_etapa > 4) {
                                echo 'ativo';
                              } ?>" title="CONCLUIR"></div>

                <?php } ?>
              </div>
            </div>
          </div>

        </div>

        <script>
          // function cardLinkClick(event) {
          //   // Only trigger if the click event is not on the dropdown
          //   if (!event.target.closest('.dropdown')) {
          //     window.location.href = 'https://example.com';
          //   }
          // }

          // Stop propagation on dropdown elements
          $(document).ready(function() {
            $('.dropdown').on('click', function(event) {
              event.stopPropagation();
            });
          });
        </script>

      </div>


  <?php }
  } catch (PDOException $e) {
    //echo "Erro: " . $e->getMessage();
  }  ?>

</div>

</div>
</div>

<?php include 'includes/footer.php'; ?>