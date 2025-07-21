<?php include 'includes/header.php'; ?>

<!-- start page title -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Nova Proposta</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Propostas</a></li>
          <li class="breadcrumb-item active">Nova Proposta</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<?php if (empty($global_user_data_nascimento)) { ?>
  <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert" id="alert">
    <i class="ri-alert-line label-icon"></i><strong>Aviso: </strong> <strong><a href="perfil.php">Clique aqui</a></strong> para completar o cadastro da sua conta!
    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
  </div>

  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-5 g-4 mb-5" id="job-list">

    <?php
    try {
      $stmt = $conn->prepare("SELECT * FROM propostas_categorias ORDER BY propc_id");
      $stmt->execute();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        if ($propc_id == 1) {
          $card_classe = 'card_verde';
        }

        if ($propc_id == 2) {
          $card_classe = 'card_laranja';
        }

        if ($propc_id == 3) {
          $card_classe = 'card_azul';
        }

        if ($propc_id == 4) {
          $card_classe = 'card_roxo';
        }

        if ($propc_id == 5) {
          $card_classe = 'card_rosa';
        }

    ?>

        <div class="col card_cad_proposta d-flex align-items-stretch <?= $card_classe ?>">
          <div class="card w-100">
            <div class="card-body">
              <div class="d-flex flex-column align-items-center">
                <div class="icon_card"></div>
                <p><?= $propc_categoria ?></p>
              </div>
            </div>
          </div>
        </div>

    <?php }
    } catch (PDOException $e) {
      // echo "Erro: " . $e->getMessage();
      echo "Erro ao tentar recuperar os dados";
    } ?>

  </div>

<?php } else {  ?>

  <?php
  $sql = $conn->query("SELECT * FROM usuarios WHERE user_id = '$global_user_id'");
  while ($linha = $sql->fetch(PDO::FETCH_ASSOC)) {
    $perfil_pb = $linha['user_perfil']; // PERFIL DO USUÁRIO

    // SE O USUÁRIO TIVER O PERFIL DE PROFESSOR BAHIANA (ID 3 NA TABELA "USUARIO_PERFIL"), NÃO PRECISARÁ PEDIR PERMISSÃO PRA SUBMETER
    if ($perfil_pb == 3) { ?>

      <div class="envia_proposta">
        <div class="row">
          <div class="col">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-8">
                    <h5 class="mb-1">Cadastre sua proposta extensionista!</h5>
                    <p class="mb-0">Escolha a categoria que mais se adéqua ao seu projeto.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-5 g-4 mb-5" id="job-list">

        <?php
        try {
          $stmt = $conn->prepare("SELECT * FROM propostas_categorias ORDER BY propc_id");
          $stmt->execute();
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            if ($propc_id == 1) {
              $card_classe = 'card_verde';
            }

            if ($propc_id == 2) {
              $card_classe = 'card_laranja';
            }

            if ($propc_id == 3) {
              $card_classe = 'card_azul';
            }

            if ($propc_id == 4) {
              $card_classe = 'card_roxo';
            }

            if ($propc_id == 5) {
              $card_classe = 'card_rosa';
            }

        ?>

            <?php if ($propc_status == 1) { ?>

              <div class="col card_cad_proposta_color d-flex align-items-stretch <?= $card_classe ?>" onclick="location.href='cad_proposta.php?tp=<?= base64_encode($propc_id) ?>'" role="button">
                <div class="card card-animate w-100">
                  <div class="card-body">
                    <div class="d-flex flex-column align-items-center">
                      <div class="icon_card"></div>
                      <p><?= $propc_categoria ?></p>
                    </div>
                  </div>
                </div>
              </div>

            <?php } else { ?>

              <div class="col card_cad_proposta d-flex align-items-stretch <?= $card_classe ?>">
                <div class="card w-100">
                  <div class="card-body">
                    <div class="d-flex flex-column align-items-center">
                      <div class="icon_card"></div>
                      <p><?= $propc_categoria ?></p>
                    </div>
                  </div>
                </div>
              </div>

            <?php } ?>

        <?php }
        } catch (PDOException $e) {
          // echo "Erro: " . $e->getMessage();
          echo "Erro ao tentar recuperar os dados";
        } ?>

      </div>

      <?php if ($propc_msg) { ?>
        <div class="container-fluid">
          <div class="row info_card justify-content-center">
            <div class="col-md-12 col-lg-4 p-0 text-center">

              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chat-right-text mb-2" viewBox="0 0 16 16">
                <path d="M2 1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h9.586a2 2 0 0 1 1.414.586l2 2V2a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z" />
                <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6m0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
              </svg>

              <h3>Aviso Importante!</h3>
              <p><?= $propc_msg ?></p>
            </div>
          </div>
        </div>
      <?php } ?>

      <?php } else {

      // SE NÃO HOUVER NENHUM PEDIDO DE SOLICITAÇÃO DO USUÁRIO, MOSTRA BOTÃO PARA SOLICITAR PERMISSÃO
      $query = "SELECT * FROM submissao_permissao WHERE subs_cad = '$global_user_id'";
      $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
      $stmt->execute();
      $row_count = $stmt->rowCount();
      if (empty($row_count)) { ?>

        <div class="envia_proposta">
          <div class="row">
            <div class="col">
              <div class="card">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-md-8">
                      <h5 class="mb-1">Você não possui licença para cadastrar propostas.</h5>
                      <p class="mb-3 mb-md-0">Para enviar uma proposta de ação extensionista solicite uma licença ao RESERVM.</p>
                    </div>
                    <div class="col-md-4 text-start text-md-end">
                      <a class="btn botao botao_azul_escuro btn-light waves-effect" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Solicitar Licença</a>
                    </div>
                  </div>

                  <div class="collapse" id="collapseExample">
                    <form method="POST" action="controller/controller_submissao_usuario.php?funcao=cad_subm" class="needs-validation mt-4" id="ValidaBotaoProgressPadrao" novalidate>

                      <input type="hidden" class="form-control text-lowercase" name="user_email" value="<?= $global_user_email ?>" required>

                      <div class="mb-3">
                        <label class="form-label">Em poucas palavras, informe detalhes da sua proposta <span>*</span></label>
                        <textarea class="form-control" name="subs_solicitacao" rows="4" required></textarea>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>

                      <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                        <span class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</span>
                        <button type="submit" class="btn botao botao_verde waves-effect">Enviar</button>
                      </div>

                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-5 g-4 mb-5" id="job-list">

          <?php
          try {
            $stmt = $conn->prepare("SELECT * FROM propostas_categorias ORDER BY propc_id");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              extract($row);

              if ($propc_id == 1) {
                $card_classe = 'card_verde';
              }

              if ($propc_id == 2) {
                $card_classe = 'card_laranja';
              }

              if ($propc_id == 3) {
                $card_classe = 'card_azul';
              }

              if ($propc_id == 4) {
                $card_classe = 'card_roxo';
              }

              if ($propc_id == 5) {
                $card_classe = 'card_rosa';
              }

          ?>

              <div class="col card_cad_proposta d-flex align-items-stretch <?= $card_classe ?>">
                <div class="card w-100">
                  <div class="card-body">
                    <div class="d-flex flex-column align-items-center">
                      <div class="icon_card"></div>
                      <p><?= $propc_categoria ?></p>
                    </div>
                  </div>
                </div>
              </div>

          <?php }
          } catch (PDOException $e) {
            // echo "Erro: " . $e->getMessage();
            echo "Erro ao tentar recuperar os dados";
          } ?>

        </div>

        <?php } else {

        // SE HOUVER PERMISSÃO APROVADA(1) SEM DATA DE VALIDADE, MOSTRA CARDS
        $query = "SELECT * FROM submissao_permissao WHERE subs_cad = '$global_user_id' AND subs_status IN (1) AND subs_data_validade IS NULL";
        $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        $row_count = $stmt->rowCount();
        while ($subs_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          extract($subs_row);
        }
        if (!empty($row_count)) { ?>

          <div class="envia_proposta">
            <div class="row">
              <div class="col">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-8">
                        <h5 class="mb-1">Cadastre sua proposta extensionista!</h5>
                        <p class="mb-0">Escolha a categoria que mais se adéqua ao seu projeto. teste
                          <?php if (!empty($subs_data_validade)) { ?>
                            Sua licença para submeter projetos está ativa até <strong><?= $subs_data_validade ?></strong>
                          <?php } ?>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-5 g-4 mb-4" id="job-list">

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM propostas_categorias ORDER BY propc_id");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                if ($propc_id == 1) {
                  $card_classe = 'card_verde';
                }

                if ($propc_id == 2) {
                  $card_classe = 'card_laranja';
                }

                if ($propc_id == 3) {
                  $card_classe = 'card_azul';
                }

                if ($propc_id == 4) {
                  $card_classe = 'card_roxo';
                }

                if ($propc_id == 5) {
                  $card_classe = 'card_rosa';
                }

                if (isset($propc_msg)) {
                  $propc_msg = $propc_msg;
                }

            ?>

                <?php if ($propc_status == 1) { ?>

                  <div class="col card_cad_proposta_color d-flex align-items-stretch <?= $card_classe ?>" onclick="location.href='cad_proposta.php?tp=<?= base64_encode($propc_id) ?>'" role="button">
                    <div class="card card-animate w-100">
                      <div class="card-body">
                        <div class="d-flex flex-column align-items-center">
                          <div class="icon_card"></div>
                          <p><?= $propc_categoria ?></p>
                        </div>
                      </div>
                    </div>
                  </div>

                <?php } else { ?>

                  <div class="col card_cad_proposta d-flex align-items-stretch <?= $card_classe ?>">
                    <div class="card w-100">
                      <div class="card-body">
                        <div class="d-flex flex-column align-items-center">
                          <div class="icon_card"></div>
                          <p><?= $propc_categoria ?></p>
                        </div>
                      </div>
                    </div>
                  </div>

                <?php } ?>

            <?php }
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar os dados";
            } ?>

          </div>

          <?php if ($propc_msg) { ?>
            <div class="container-fluid">
              <div class="row info_card justify-content-center">
                <div class="col-md-12 col-lg-4 p-0 text-center">

                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chat-right-text mb-2" viewBox="0 0 16 16">
                    <path d="M2 1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h9.586a2 2 0 0 1 1.414.586l2 2V2a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z" />
                    <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6m0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                  </svg>

                  <h3>Aviso Importante!</h3>
                  <p><?= $propc_msg ?></p>
                </div>
              </div>
            </div>
          <?php } ?>

          <?php } else {

          // SE HOUVER ALGUMA PERMISSÃO APROVADA(1) COM DATA DE VALIDADE VÁLIDA, MOSTRA CARDS
          $query = "SELECT * FROM submissao_permissao WHERE subs_cad = '$global_user_id' AND subs_status IN (1) AND subs_data_validade IS NOT NULL AND subs_data_validade >= GETDATE()";
          $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
          $stmt->execute();
          while ($subs_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($subs_row);
          }
          $row_count = $stmt->rowCount();
          if (!empty($row_count)) { ?>

            <div class="envia_proposta">
              <div class="row">
                <div class="col">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-8">
                          <h5 class="mb-1">Cadastre sua proposta extensionistas</h5>
                          <p class="mb-0">Escolha a categoria que mais se adéqua ao seu projeto.
                            <?php if (!empty($subs_data_validade)) { ?>
                              Sua licença para submeter projetos está ativa até <strong><?= date('d/m/Y', strtotime($subs_data_validade)) ?></strong>
                            <?php } ?>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-5 g-4 mb-5" id="job-list">

              <?php
              try {
                $stmt = $conn->prepare("SELECT * FROM propostas_categorias ORDER BY propc_id");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  extract($row);

                  if ($propc_id == 1) {
                    $card_classe = 'card_verde';
                  }

                  if ($propc_id == 2) {
                    $card_classe = 'card_laranja';
                  }

                  if ($propc_id == 3) {
                    $card_classe = 'card_azul';
                  }

                  if ($propc_id == 4) {
                    $card_classe = 'card_roxo';
                  }

                  if ($propc_id == 5) {
                    $card_classe = 'card_rosa';
                  }

              ?>

                  <?php if ($propc_status == 1) { ?>

                    <div class="col card_cad_proposta_color d-flex align-items-stretch <?= $card_classe ?>" onclick="location.href='cad_proposta.php?tp=<?= base64_encode($propc_id) ?>'" role="button">
                      <div class="card card-animate w-100">
                        <div class="card-body">
                          <div class="d-flex flex-column align-items-center">
                            <div class="icon_card"></div>
                            <p><?= $propc_categoria ?></p>
                          </div>
                        </div>
                      </div>
                    </div>

                  <?php } else { ?>

                    <div class="col card_cad_proposta d-flex align-items-stretch <?= $card_classe ?>">
                      <div class="card w-100">
                        <div class="card-body">
                          <div class="d-flex flex-column align-items-center">
                            <div class="icon_card"></div>
                            <p><?= $propc_categoria ?></p>
                          </div>
                        </div>
                      </div>
                    </div>

                  <?php } ?>

              <?php }
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar os dados";
              } ?>

            </div>

            <?php if ($propc_msg) { ?>
              <div class="container-fluid">
                <div class="row info_card justify-content-center">
                  <div class="col-md-12 col-lg-4 p-0 text-center">

                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chat-right-text mb-2" viewBox="0 0 16 16">
                      <path d="M2 1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h9.586a2 2 0 0 1 1.414.586l2 2V2a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z" />
                      <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6m0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                    </svg>

                    <h3>Aviso Importante!</h3>
                    <p><?= $propc_msg ?></p>
                  </div>
                </div>
              </div>
            <?php } ?>

            <?php } else {

            // SE HOUVER ALGUMA SOLICITAÇÃO DE PERMISSÃO(0), MOSTRA AVISO PARA ESPERAR RESULTADO
            $query = "SELECT * FROM submissao_permissao WHERE subs_cad = '$global_user_id' AND subs_status IN (0)";
            $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            $row_count = $stmt->rowCount();
            if ($row_count >= 1) { ?>

              <div class="envia_proposta">
                <div class="row">
                  <div class="col">
                    <div class="card">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-md-8">
                            <h5 class="mb-1">Sua solicitação foi enviada!</h5>
                            <p class="mb-3 mb-md-0">Em breve enviaremos o resultado.</p>
                          </div>
                          <div class="col-md-4 text-start text-md-end">
                            <a class="btn botao disabled">Solicitar Licença</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-5 g-4 mb-5" id="job-list">

                <?php
                try {
                  $stmt = $conn->prepare("SELECT * FROM propostas_categorias ORDER BY propc_id");
                  $stmt->execute();
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);

                    if ($propc_id == 1) {
                      $card_classe = 'card_verde';
                    }

                    if ($propc_id == 2) {
                      $card_classe = 'card_laranja';
                    }

                    if ($propc_id == 3) {
                      $card_classe = 'card_azul';
                    }

                    if ($propc_id == 4) {
                      $card_classe = 'card_roxo';
                    }

                    if ($propc_id == 5) {
                      $card_classe = 'card_rosa';
                    }

                ?>

                    <div class="col card_cad_proposta d-flex align-items-stretch <?= $card_classe ?>">
                      <div class="card w-100">
                        <div class="card-body">
                          <div class="d-flex flex-column align-items-center">
                            <div class="icon_card"></div>
                            <p><?= $propc_categoria ?></p>
                          </div>
                        </div>
                      </div>
                    </div>

                <?php }
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar os dados";
                } ?>

              </div>


              <?php } else {

              $query = "SELECT * FROM submissao_permissao WHERE subs_cad = '$global_user_id' AND subs_status = 1 AND subs_data_validade = ''";
              $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
              $stmt->execute();
              $row_count = $stmt->rowCount();
              if ($row_count != 1) { ?>

                <div class="envia_proposta">
                  <div class="row">
                    <div class="col">
                      <div class="card">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-md-8">
                              <h5 class="mb-1">Você não possui licença para cadastrar propostas.</h5>
                              <p class="mb-3 mb-md-0">Para enviar uma proposta de ação extensionista solicite uma licença ao RESERVM.</p>
                            </div>
                            <div class="col-md-4 text-start text-md-end">
                              <a class="btn botao botao_azul_escuro btn-light waves-effect" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Solicitar Licença</a>
                            </div>
                          </div>

                          <div class="collapse" id="collapseExample">
                            <form method="POST" action="controller/controller_submissao_usuario.php?funcao=cad_subm" class="needs-validation mt-4" id="ValidaBotaoProgressPadrao" novalidate>

                              <input type="hidden" class="form-control text-lowercase" name="user_email" value="<?= $global_user_email ?>" required>

                              <div class="mb-3">
                                <label class="form-label">Em poucas palavras, informe detalhes da sua proposta <span>*</span></label>
                                <textarea class="form-control" name="subs_solicitacao" rows="4" required></textarea>
                                <div class="invalid-feedback">Este campo é obrigatório</div>
                              </div>

                              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                                <span class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</span>
                                <button type="submit" class="btn botao botao_verde waves-effect">Enviar</button>
                              </div>

                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-5 g-4 mb-5" id="job-list">

                  <?php
                  try {
                    $stmt = $conn->prepare("SELECT * FROM propostas_categorias ORDER BY propc_id");
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      extract($row);

                      if ($propc_id == 1) {
                        $card_classe = 'card_verde';
                      }

                      if ($propc_id == 2) {
                        $card_classe = 'card_laranja';
                      }

                      if ($propc_id == 3) {
                        $card_classe = 'card_azul';
                      }

                      if ($propc_id == 4) {
                        $card_classe = 'card_roxo';
                      }

                      if ($propc_id == 5) {
                        $card_classe = 'card_rosa';
                      }

                  ?>

                      <div class="col card_cad_proposta d-flex align-items-stretch <?= $card_classe ?>">
                        <div class="card w-100">
                          <div class="card-body">
                            <div class="d-flex flex-column align-items-center">
                              <div class="icon_card"></div>
                              <p><?= $propc_categoria ?></p>
                            </div>
                          </div>
                        </div>
                      </div>

                  <?php }
                  } catch (PDOException $e) {
                    // echo "Erro: " . $e->getMessage();
                    echo "Erro ao tentar recuperar os dados";
                  } ?>

                </div>

                <?php } else {

                // SE HOUVER ALGUMA SOLICITAÇÃO APROVADA(1) MAS A DATA DE VALIDADE JÁ EXPIROU, MOSTRAR BOTÃO PARA SOLICITAR NOVA PERMISSÃO
                $query = "SELECT * FROM submissao_permissao WHERE subs_cad = '$global_user_id' AND subs_status IN (1) AND subs_data_validade IS NOT NULL AND subs_data_validade <= GETDATE()";
                $stmt = $conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->execute();
                $row_count = $stmt->rowCount();
                if ($row_count >= 1) { ?>

                  <div class="envia_proposta">
                    <div class="row">
                      <div class="col">
                        <div class="card">
                          <div class="card-body">
                            <div class="row align-items-center">
                              <div class="col-md-8">
                                <h5 class="mb-1">Você não possui licença para cadastrar propostas.</h5>
                                <p class="mb-3 mb-md-0">Para enviar uma proposta de ação extensionista solicite uma licença ao RESERVM.</p>
                              </div>
                              <div class="col-md-4 text-start text-md-end">
                                <a class="btn botao botao_azul_escuro btn-light waves-effect" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Solicitar Licença</a>
                              </div>
                            </div>

                            <div class="collapse" id="collapseExample">
                              <form method="POST" action="controller/controller_submissao_usuario.php?funcao=cad_subm" class="needs-validation mt-4" id="ValidaBotaoProgressPadrao" novalidate>

                                <input type="hidden" class="form-control text-lowercase" name="user_email" value="<?= $global_user_email ?>" required>

                                <div class="mb-3">
                                  <label class="form-label">Em poucas palavras, informe detalhes da sua proposta <span>*</span></label>
                                  <textarea class="form-control" name="subs_solicitacao" rows="4" required></textarea>
                                  <div class="invalid-feedback">Este campo é obrigatório</div>
                                </div>

                                <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                                  <span class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</span>
                                  <button type="submit" class="btn botao botao_verde waves-effect">Enviar</button>
                                </div>

                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-5 row-cols-xxl-5 g-4 mb-5" id="job-list">

                    <?php
                    try {
                      $stmt = $conn->prepare("SELECT * FROM propostas_categorias ORDER BY propc_id");
                      $stmt->execute();
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row);

                        if ($propc_id == 1) {
                          $card_classe = 'card_verde';
                        }

                        if ($propc_id == 2) {
                          $card_classe = 'card_laranja';
                        }

                        if ($propc_id == 3) {
                          $card_classe = 'card_azul';
                        }

                        if ($propc_id == 4) {
                          $card_classe = 'card_roxo';
                        }

                        if ($propc_id == 5) {
                          $card_classe = 'card_rosa';
                        }

                    ?>

                        <div class="col card_cad_proposta d-flex align-items-stretch <?= $card_classe ?>">
                          <div class="card w-100">
                            <div class="card-body">
                              <div class="d-flex flex-column align-items-center">
                                <div class="icon_card"></div>
                                <p><?= $propc_categoria ?></p>
                              </div>
                            </div>
                          </div>
                        </div>

                    <?php }
                    } catch (PDOException $e) {
                      // echo "Erro: " . $e->getMessage();
                      echo "Erro ao tentar recuperar os dados";
                    } ?>

                  </div>

<?php
                }
              }
            }
          }
        }
      }
    }
  }
}
?>



</div>
</div>

<?php include 'includes/footer.php'; ?>