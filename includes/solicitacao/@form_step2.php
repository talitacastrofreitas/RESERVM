<div class="card-body p-sm-4 p-3">

  <form class="needs-validation" method="POST" action="controller/controller_propostas.php" autocomplete="off" novalidate>

    <input type="hidden" class="form-control" name="prop_id" value="<?= base64_encode($prop_id) ?>" required>
    <input type="hidden" class="form-control" name="prop_tipo" value="<?= base64_encode($propc_cat) ?>" required>

    <div class="tit_section" id="pcp_ancora">
      <h3>Coordenador(a) do Projeto</h3>
    </div>

    <div class="row grid gx-3">

      <div class="col-12">
        <div class="row grid g-3 align-items-center">
          <div class="col-md-8 d-flex">
            <p style="color: #0461AD; margin: 0; background: #D6E9F8; border-radius: 4px; padding: 10px 10px;">É indispensável que haja um coordenador(a) responsável pela organização e gestão do projeto. Cadastre pelo menos um(a) coordenador(a).</p>
          </div>

          <div class="col-md-4 text-end d-grid d-md-block">
            <a class="btn botao botao_azul_escuro waves-effect" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_coordenador_projeto">+ Cadastrar Coordenador(a) do Projeto</a>
          </div>
        </div>
      </div>

      <div class="tab-pane mt-3" id="profile1" role="tabpanel">
        <div class="accordion custom-accordionwithicon accordion-flush accordion-fill-success mb-4" id="accordionFill_cp">

          <?php
          $prop_id = base64_decode($_GET['i']);
          // SELECIONA O DADO COM A DATA DE CADASTRO MAIS ALTUAL - USANDO PARA DEIXAR O ACORDION ABERTO NESTA DATA
          $stmt = $conn->prepare("SELECT TOP 1 * FROM propostas_coordenador_projeto WHERE pcp_proposta_id = :pcp_proposta_id ORDER BY pcp_data_cad DESC");
          $stmt->bindParam(':pcp_proposta_id', $prop_id, PDO::PARAM_STR);
          $stmt->execute();
          $pcp = $stmt->fetch(PDO::FETCH_ASSOC);
          $id_max_data = isset($pcp['pcp_id']) ? $pcp['pcp_id'] : NULL;
          //

          // SE HOUVER APENAS UMA DISCIPLINA/MÓDULO CADASTRADA, O BOTÃO "EXCLUIR" DESAPARECE
          $sql = "SELECT COUNT(*) FROM propostas_coordenador_projeto WHERE pcp_proposta_id = :pcp_proposta_id";
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(":pcp_proposta_id", $prop_id);
          $stmt->execute();
          if ($stmt->fetchColumn() > 1) {
            $ativa_link_excluir = '';
          } else {
            $ativa_link_excluir = 'd-none';
          }
          // -------------------------------

          $sql = $conn->query("SELECT * FROM propostas_coordenador_projeto
                              INNER JOIN conf_cursos_coordenadores ON conf_cursos_coordenadores.cc_id = propostas_coordenador_projeto.pcp_area_atuacao
                              INNER JOIN usuario_perfil ON usuario_perfil.us_pe_id = propostas_coordenador_projeto.pcp_partic_perfil
                              WHERE pcp_proposta_id = '$prop_id' ORDER BY pcp_data_cad DESC");
          while ($pcp = $sql->fetch(PDO::FETCH_ASSOC)) {
            //extract($pcp);
            $pcp_id                  = $pcp['pcp_id'];
            $pcp_proposta_id         = $pcp['pcp_proposta_id'];
            $pcp_nome                = htmlspecialchars($pcp['pcp_nome'], ENT_QUOTES, 'UTF-8');
            $pcp_email               = $pcp['pcp_email'];
            $pcp_contato             = $pcp['pcp_contato'];
            $pcp_partic_perfil       = $pcp['pcp_partic_perfil'];
            $pcp_outro_partic_perfil = htmlspecialchars($pcp['pcp_outro_partic_perfil'], ENT_QUOTES, 'UTF-8');
            $pcp_carga_hora          = $pcp['pcp_carga_hora'];
            $pcp_area_atuacao        = $pcp['pcp_area_atuacao'];
            $pcp_nome_area_atuacao   = htmlspecialchars($pcp['pcp_nome_area_atuacao'], ENT_QUOTES, 'UTF-8');
            $pcp_formacao            = $pcp['pcp_formacao'];
            $pcp_lattes              = $pcp['pcp_lattes'];
            $pcp_user_id             = $pcp['pcp_user_id'];
            $pcp_data_cad            = $pcp['pcp_data_cad'];
            $pcp_data_upd            = $pcp['pcp_data_upd'];
            // PERFIL
            $us_pe_id                = $pcp['us_pe_id'];
            $us_pe_perfil            = $pcp['us_pe_perfil'];
            // CURSO
            $cc_id                   = $pcp['cc_id'];
            $cc_curso                = $pcp['cc_curso'];

            // CONFIGURAÇÃO DA FORMATAÇÃO
            $form_prof = nl2br($pcp_formacao);
            $form_prof = str_replace('<br />', '', $form_prof);
            $form_prof = str_replace('"', '&quot;', $form_prof); // MOSTRA ASPAS DUPLAS
          ?>

            <div class="accordion-item">
              <h2 class="accordion-header" id="accordionFill<?= $pcp_id ?>">
                <button class="accordion-button fw-semibold <?php if ($id_max_data != $pcp_id) {
                                                              echo 'collapsed';
                                                            } ?>" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill<?= $pcp_id ?>" aria-expanded="true" aria-controls="accor_fill<?= $pcp_id ?>"><?= $pcp_nome ?></button>
              </h2>
              <div id="accor_fill<?= $pcp_id ?>" class="accordion-collapse collapse <?php if ($id_max_data == $pcp_id) {
                                                                                      echo 'show';
                                                                                    } ?>" aria-labelledby="accordionFill<?= $pcp_id ?>" data-bs-parent="#accordionFill_cp">
                <div class="accordion-body">

                  <div class="row dados_user_tabela">

                    <div class="col-10 col-sm-11">

                      <div class="row">

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>E-mail</label>
                          <p><?= $pcp_email ?></p>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Contato</label>
                          <p><?= $pcp_contato ?></p>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Perfil do Participante</label>
                          <?php if ($pcp_outro_partic_perfil) { ?>
                            <p><?= $pcp_outro_partic_perfil ?></p>
                          <?php } else { ?>
                            <p><?= $us_pe_perfil ?></p>
                          <?php } ?>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Curso ou Área de Atuação</label>
                          <?php if ($pcp_nome_area_atuacao) { ?>
                            <p><?= $pcp_nome_area_atuacao ?></p>
                          <?php } else { ?>
                            <p><?= $cc_curso ?></p>
                          <?php } ?>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Horas Dedicadas</label>
                          <p class="text-uppercase"><?= $pcp_carga_hora ?> horas</p>
                        </div>

                      </div>

                    </div>

                    <div class="col-2 col-sm-1 text-end">
                      <div class="dropdown drop_tabela d-inline-block">
                        <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_coordenador_projeto" data-bs-pcp_id="<?= base64_encode($pcp_id) ?>" data-bs-pcp_proposta_id="<?= base64_encode($pcp_proposta_id) ?>" data-bs-pcp_nome="<?= $pcp_nome ?>" data-bs-pcp_email="<?= $pcp_email ?>" data-bs-pcp_contato="<?= $pcp_contato ?>" data-bs-pcp_partic_perfil="<?= $pcp_partic_perfil ?>" data-bs-pcp_outro_partic_perfil="<?= $pcp_outro_partic_perfil ?>" data-bs-pcp_carga_hora="<?= $pcp_carga_hora ?>" data-bs-pcp_area_atuacao="<?= $pcp_area_atuacao ?>" data-bs-pcp_nome_area_atuacao="<?= $pcp_nome_area_atuacao ?>" data-bs-pcp_formacao="<?= str_replace('<br />', '', $pcp_formacao) ?>" data-bs-pcp_lattes="<?= $pcp_lattes ?>" title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                          <li class="<?= $ativa_link_excluir ?>"><a href="controller/controller_propostas_coordenador_projeto.php?funcao=exc_coord&pcp_id=<?= base64_encode($pcp_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                        </ul>
                      </div>

                    </div>

                    <div class="col-12">
                      <label>Formação Profissional</label>
                      <p><?= $pcp_formacao ?></p>
                    </div>

                    <?php if ($pcp_lattes) { ?>
                      <div class="col-12">
                        <label>Link do Currículo Vitae</label>
                        <p class="link-underline"><a href="<?= $pcp_lattes ?>" target="_blank"><?= $pcp_lattes ?></a></p>
                      </div>
                    <?php } ?>

                  </div>

                </div>
              </div>
            </div>

          <?php } ?>

          <?php
          if (!isset($pcp_id)) { ?>
            <div>
              <p>Nenhuma Coordenador(a) do Projeto cadastrado</p>
            </div>
          <?php } ?>

        </div>
      </div>

      <div class="tit_section" id="pex_ancora">
        <h3>Equipe Executora</h3>
      </div>

      <div class="col-12">
        <div class="row grid g-3 align-items-center">
          <div class="col-md-12 text-end d-grid d-md-block">
            <a class="btn botao botao_azul_escuro waves-effect" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_equipe_executora">+ Cadastrar Equipe Executora</a>
          </div>
        </div>
      </div>

      <div class="tab-pane mt-3" id="profile1" role="tabpanel">
        <div class="accordion custom-accordionwithicon accordion-flush accordion-fill-success mb-4" id="accordionFill_ex">

          <?php
          $prop_id = base64_decode($_GET['i']);
          // SELECIONA O DADO COM A DATA DE CADASTRO MAIS ALTUAL - USANDO PARA DEIXAR O ACORDION ABERTO NESTA DATA
          $stmt = $conn->prepare("SELECT TOP 1 * FROM propostas_equipe_executora WHERE pex_proposta_id = :pex_proposta_id ORDER BY pex_data_cad DESC");
          $stmt->bindParam(':pex_proposta_id', $prop_id, PDO::PARAM_INT);
          $stmt->execute();
          $pex = $stmt->fetch(PDO::FETCH_ASSOC);
          $id_max_data = isset($pex['pex_id']) ? $pex['pex_id'] : NULL;
          //
          $sql = $conn->query("SELECT * FROM propostas_equipe_executora
                              INNER JOIN conf_cursos_coordenadores ON conf_cursos_coordenadores.cc_id = propostas_equipe_executora.pex_area_atuacao
                              INNER JOIN usuario_perfil ON usuario_perfil.us_pe_id = propostas_equipe_executora.pex_partic_perfil
                              INNER JOIN conf_categoria_participacao_projeto ON conf_categoria_participacao_projeto.cpp_id = propostas_equipe_executora.pex_partic_categ
                              WHERE pex_proposta_id = '$prop_id' ORDER BY pex_data_cad DESC");
          while ($pex = $sql->fetch(PDO::FETCH_ASSOC)) {
            $pex_id                  = $pex['pex_id'];
            $pex_proposta_id         = $pex['pex_proposta_id'];
            $pex_nome                = htmlspecialchars($pex['pex_nome'], ENT_QUOTES, 'UTF-8');
            $pex_email               = $pex['pex_email'];
            $pex_contato             = $pex['pex_contato'];
            $pex_partic_categ        = $pex['pex_partic_categ'];
            $pex_qual_partic_categ   = htmlspecialchars($pex['pex_qual_partic_categ'], ENT_QUOTES, 'UTF-8');
            $pex_partic_perfil       = $pex['pex_partic_perfil'];
            $pex_outro_partic_perfil = htmlspecialchars($pex['pex_outro_partic_perfil'], ENT_QUOTES, 'UTF-8');
            $pex_carga_hora          = $pex['pex_carga_hora'];
            $pex_area_atuacao        = $pex['pex_area_atuacao'];
            $pex_nome_area_atuacao   = htmlspecialchars($pex['pex_nome_area_atuacao'], ENT_QUOTES, 'UTF-8');
            $pex_formacao            = $pex['pex_formacao'];
            $pex_lattes              = $pex['pex_lattes'];
            // PERFIL PARTICIPANTE
            $us_pe_perfil            = $pex['us_pe_perfil'];
            // CURSO COORDENADOR
            $cc_curso                = $pex['cc_curso'];
            // CATEGORIA PARTICIPAÇÃO PROJETO
            $cpp_categoria           = $pex['cpp_categoria'];

            // CONFIGURAÇÃO DA FORMAÇÃO
            $form_prof = nl2br($pex_formacao);
            $form_prof = str_replace('<br />', '', $form_prof);
            $form_prof = str_replace('"', '&quot;', $form_prof); // MOSTRA ASPAS DUPLAS
          ?>

            <div class="accordion-item">
              <h2 class="accordion-header" id="accordionFill<?= $pex_id ?>">
                <button class="accordion-button fw-semibold <?php if ($id_max_data != $pex_id) {
                                                              echo 'collapsed';
                                                            } ?>" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill<?= $pex_id ?>" aria-expanded="true" aria-controls="accor_fill<?= $pex_id ?>"><?= $pex_nome ?></button>
              </h2>
              <div id="accor_fill<?= $pex_id ?>" class="accordion-collapse collapse <?php if ($id_max_data == $pex_id) {
                                                                                      echo 'show';
                                                                                    } ?>" aria-labelledby="accordionFill<?= $pex_id ?>" data-bs-parent="#accordionFill_ex">
                <div class="accordion-body">

                  <div class="row dados_user_tabela">

                    <div class="col-10 col-sm-11">

                      <div class="row">

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>E-mail</label>
                          <p><?= $pex_email ?></p>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Contato</label>
                          <p><?= $pex_contato ?></p>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Perfil do Participante</label>
                          <?php if ($pex_outro_partic_perfil) { ?>
                            <p><?= $pex_outro_partic_perfil ?></p>
                          <?php } else { ?>
                            <p><?= $us_pe_perfil ?></p>
                          <?php } ?>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Curso ou Área de Atuação</label>
                          <?php if ($pex_nome_area_atuacao) { ?>
                            <p><?= $pex_nome_area_atuacao ?></p>
                          <?php } else { ?>
                            <p><?= $cc_curso ?></p>
                          <?php } ?>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Categoria de Participação no Projeto</label>
                          <?php if ($pex_qual_partic_categ) { ?>
                            <p><?= $pex_qual_partic_categ ?></p>
                          <?php } else { ?>
                            <p><?= $cpp_categoria ?></p>
                          <?php } ?>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Horas Dedicadas</label>
                          <p class="text-uppercase"><?= $pex_carga_hora ?> horas</p>
                        </div>

                      </div>

                    </div>

                    <div class="col-2 col-sm-1 text-end">
                      <div class="dropdown drop_tabela d-inline-block">
                        <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_equipe_executora" data-bs-pex_id="<?= base64_encode($pex_id) ?>" data-bs-pex_proposta_id="<?= base64_encode($pex_proposta_id) ?>" data-bs-pex_nome="<?= $pex_nome ?>" data-bs-pex_email="<?= $pex_email ?>" data-bs-pex_contato="<?= $pex_contato ?>" data-bs-pex_partic_perfil="<?= $pex_partic_perfil ?>" data-bs-pex_outro_partic_perfil="<?= $pex_outro_partic_perfil ?>" data-bs-pex_carga_hora="<?= $pex_carga_hora ?>" data-bs-pex_area_atuacao="<?= $pex_area_atuacao ?>" data-bs-pex_nome_area_atuacao="<?= $pex_nome_area_atuacao ?>" data-bs-pex_partic_categ="<?= $pex_partic_categ ?>" data-bs-pex_qual_partic_categ="<?= $pex_qual_partic_categ ?>" data-bs-pex_formacao="<?= str_replace('<br />', '', $pex_formacao) ?>" data-bs-pex_lattes="<?= $pex_lattes ?>" title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                          <li><a href="controller/controller_propostas_equipe_executora.php?funcao=exc_equipe_exec&pex_id=<?= base64_encode($pex_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                        </ul>
                      </div>

                    </div>

                    <div class="col-12">
                      <label>Formação Profissional</label>
                      <p><?= $pex_formacao ?></p>
                    </div>

                    <?php if ($pex_lattes) { ?>
                      <div class="col-12">
                        <label>Link do Currículo Vitae</label>
                        <p class="link-underline"><a href="<?= $pex_lattes ?>" target="_blank"><?= $pex_lattes ?></a></p>
                      </div>
                    <?php } ?>

                  </div>

                </div>
              </div>
            </div>

          <?php } ?>

          <?php
          if (!isset($pex_id)) { ?>
            <div class="info_acordion">
              <p>Nenhuma Equipe Executora cadastrada</p>
            </div>
          <?php } ?>

        </div>
      </div>

      <div class="tit_section" id="ppe_ancora">
        <h3>Parceiros Externos / Patrocinadores</h3>
      </div>

      <div class="col-12">
        <div class="row grid g-3 align-items-center">
          <div class="col-md-12 text-end d-grid d-md-block">
            <a class="btn botao botao_azul_escuro waves-effect" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_cad_parceiro_patrocinador">+ Cadastrar Parceiro Externo/Patrocinador</a>
          </div>
        </div>
      </div>

      <div class="tab-pane my-3" id="profile1" role="tabpanel">
        <div class="accordion custom-accordionwithicon accordion-flush accordion-fill-success mb-4" id="accordionFill_ppe">

          <?php
          $prop_id = base64_decode($_GET['i']);
          // SELECIONA O DADO COM A DATA DE CADASTRO MAIS ALTUAL - USANDO PARA DEIXAR O ACORDION ABERTO NESTA DATA
          $stmt = $conn->prepare("SELECT TOP 1 * FROM propostas_parceiro_externo WHERE ppe_proposta_id = :ppe_proposta_id ORDER BY ppe_data_cad DESC");
          $stmt->bindParam(':ppe_proposta_id', $prop_id, PDO::PARAM_INT);
          $stmt->execute();
          $ppe = $stmt->fetch(PDO::FETCH_ASSOC);
          $id_max_data = isset($ppe['ppe_id']) ? $ppe['ppe_id'] : NULL;
          //
          $sql = $conn->query("SELECT * FROM propostas_parceiro_externo WHERE ppe_proposta_id = '$prop_id' ORDER BY ppe_data_cad DESC");
          while ($ppe = $sql->fetch(PDO::FETCH_ASSOC)) {
            $ppe_id           = $ppe['ppe_id'];
            $ppe_proposta_id  = $ppe['ppe_proposta_id'];
            $ppe_nome         = htmlspecialchars($ppe['ppe_nome'], ENT_QUOTES, 'UTF-8');
            $ppe_email        = $ppe['ppe_email'];
            $ppe_contato      = $ppe['ppe_contato'];
            $ppe_cnpj         = $ppe['ppe_cnpj'];
            $ppe_responsavel  = htmlspecialchars($ppe['ppe_responsavel'], ENT_QUOTES, 'UTF-8');
            $ppe_area_atuacao = htmlspecialchars($ppe['ppe_area_atuacao'], ENT_QUOTES, 'UTF-8');
            $ppe_obs          = $ppe['ppe_obs'];
            $ppe_convenio     = $ppe['ppe_convenio'];
          ?>

            <div class="accordion-item">
              <h2 class="accordion-header" id="accordionFill<?= $ppe_id ?>">
                <button class="accordion-button fw-semibold <?php if ($id_max_data != $ppe_id) {
                                                              echo 'collapsed';
                                                            } ?>" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill<?= $ppe_id ?>" aria-expanded="true" aria-controls="accor_fill<?= $ppe_id ?>"><?= $ppe_nome ?></button>
              </h2>
              <div id="accor_fill<?= $ppe_id ?>" class="accordion-collapse collapse <?php if ($id_max_data == $ppe_id) {
                                                                                      echo 'show';
                                                                                    } ?>" aria-labelledby="accordionFill<?= $ppe_id ?>" data-bs-parent="#accordionFill_ppe">
                <div class="accordion-body">

                  <div class="row dados_user_tabela">

                    <div class="col-10 col-sm-11">

                      <div class="row">

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>E-mail</label>
                          <p><?= $ppe_email ?></p>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>CNPJ</label>
                          <p><?= $ppe_cnpj ?></p>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Responsável</label>
                          <p><?= $ppe_responsavel ?></p>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Contato do Responsável</label>
                          <p><?= $ppe_contato ?></p>
                        </div>

                        <div class="col-md-6 col-lg-4 col-xxl-3">
                          <label>Áea de Atuação</label>
                          <p><?= $ppe_area_atuacao ?></p>
                        </div>

                      </div>

                    </div>

                    <div class="col-2 col-sm-1 text-end">
                      <div class="dropdown drop_tabela d-inline-block">
                        <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_edit_parceiro_patrocinador" data-bs-ppe_id="<?= base64_encode($ppe_id) ?>"
                              data-bs-ppe_proposta_id="<?= base64_encode($ppe_proposta_id) ?>"
                              data-bs-ppe_nome="<?= $ppe_nome ?>"
                              data-bs-ppe_email="<?= $ppe_email ?>"
                              data-bs-ppe_contato="<?= $ppe_contato ?>"
                              data-bs-ppe_cnpj="<?= $ppe_cnpj ?>"
                              data-bs-ppe_responsavel="<?= $ppe_responsavel ?>"
                              data-bs-ppe_area_atuacao="<?= $ppe_area_atuacao ?>"
                              data-bs-ppe_obs="<?= str_replace('<br />', '', $ppe_obs) ?>"
                              data-bs-ppe_convenio="<?= $ppe_convenio ?>"
                              title="Editar"><i class="fa-regular fa-pen-to-square me-2"></i> Editar</a></li>
                          <li><a href="controller/controller_parceiro_externo.php?funcao=exc_ppeo&ppe_id=<?= base64_encode($ppe_id) ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
                        </ul>
                      </div>

                    </div>

                    <?php if ($ppe_obs) { ?>
                      <div class="col-12">
                        <label>Negociação na parceria para emissão de termo de patrocínio/parceria</label>
                        <p><?= $ppe_obs ?></p>
                      </div>
                    <?php } ?>

                    <?php if ($ppe_convenio == 1) { ?>
                      <div class="col-12">
                        <div class="form-check">
                          <input class="form-check-input opacity-100" type="checkbox" id="" name="" checked disabled>
                          <label class="form-check-label opacity-100" for="">Existe convênio ou acordo formalizado</label>
                        </div>
                      </div>
                    <?php } ?>

                  </div>

                </div>
              </div>
            </div>

          <?php } ?>

          <?php
          if (!isset($ppe_id)) { ?>
            <div class="info_acordion">
              <p>Nenhum Parceiro Externo/Patrocinador cadastrado</p>
            </div>
          <?php } ?>

        </div>
      </div>

      <div class="col-lg-12">
        <div class="hstack gap-3 align-items-center justify-content-between mt-4">
          <a type="buttom" onclick="location.href='cad_proposta.php?tp=<?= base64_encode($propc_cat) ?>&st=<?= base64_encode(1) ?>&i=<?= $_GET['i'] ?>'" class="btn botao btn-light waves-effect">Voltar</a>

          <?php if (empty($pcp_id)) { ?>
            <button type="submit" class="btn botao botao_verde waves-effect" name="" disabled>Próximo</button>
          <?php } else { ?>

            <?php if ($prop_sta_status < 7) { ?>
              <button type="submit" class="btn botao botao_verde waves-effect" name="CadPropostaStep2">Próximo</button>
            <?php } else { ?>
              <a class="btn botao botao_disabled waves-effect">Próximo</a>
            <?php } ?>

          <?php } ?>

        </div>
      </div>

    </div>

  </form>

</div>

<?php include 'includes/modal/modal_coordenador_projeto.php'; ?>
<?php include 'includes/modal/modal_equipe_executora.php'; ?>
<?php include 'includes/modal/modal_parceiro_patrocinador.php'; ?>