<?php include 'includes/header.php'; ?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Submissão</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Propostas</a></li>
          <li class="breadcrumb-item active">Submissão</li>
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
          <div class="col-12 text-sm-start text-center">
            <h5 class="card-title mb-0">Lista de Solicitações</h5>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_submissao_lista" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>Nome</th>
              <th>E-mail</th>
              <th>Perfil</th>
              <th>Data Solicitação</th>
              <th>Status</th>
              <th>Validade</th>
              <th width="20px"></th>
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM usuarios
                                      LEFT JOIN usuario_perfil ON usuario_perfil.us_pe_id = usuarios.user_perfil
                                      LEFT JOIN paises ON paises.pais_id = usuarios.user_nacionalidade
                                      LEFT JOIN generos ON generos.gen_id = usuarios.user_genero
                                      LEFT JOIN racas ON racas.raca_id = usuarios.user_raca
                                      LEFT JOIN escolaridades ON escolaridades.esc_id = usuarios.user_escolaridade
                                      LEFT JOIN usuarios_arq ON usuarios_arq.arq_user_id = usuarios.user_id
                                      INNER JOIN submissao_permissao ON usuarios.user_id = submissao_permissao.subs_cad
                                      LEFT JOIN submissao_status ON submissao_status.subst_id = submissao_permissao.subs_status
                                      ORDER BY subst_status DESC
                                      ");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                // NOME PROPONENTE
                if (empty($user_nome_social)) {
                  $proponente = $user_nome;
                } else {
                  $proponente = $user_nome_social;
                }

                // LATTES
                if ($user_lattes) {
                  $user_lattes = "<a href=" . $user_lattes . " target='blank'>" . $user_lattes . "</a>";
                } else {
                  $user_lattes = NULL;
                }

                // ARQUIVO DO CURRÍCULO
                if ($arq_arquivo) {
                  $arq_arquivo = "<a href=../uploads/usuarios/" . $user_id . "/" . $arq_arquivo . " target='blank'>" . $arq_arquivo . "</a><br>";
                } else {
                  $arq_arquivo = NULL;
                }

                // SE O PERFIL DO USUÁRIO FOR "OUTRO", MOSTRAR O PERFIL DIGITADO
                if ($user_perfil == 9) {
                  $perfil_user = $user_outro_perfil;
                } else {
                  $perfil_user = $us_pe_perfil;
                }

                // CONFIGURAÇÃO DAS REDES SOCIAIS
                if (!empty($user_facebook)) {
                  $user_facebook = "<a href=" . $user_facebook . " target='blank'><i class='fa-brands fa-facebook-f'></i></a>";
                }
                if (!empty($user_instagram)) {
                  $user_instagram = "<a href=" . $user_instagram . " target='blank'><i class='fa-brands fa-instagram'></i></a><br>";
                }
                if (!empty($user_linkedin)) {
                  $user_linkedin = "<a href=" . $user_linkedin . " target='blank'><i class='fa-brands fa-linkedin-in'></i></a><br>";
                }

                // CONFIGURAÇÃO DO ENDEREÇO
                $user_endereco = nl2br($user_endereco);

                // CONFIGURAÇÃO DA SOLICITAÇÃO
                $subs_solicitacao = nl2br($subs_solicitacao);
                $subs_solicitacao = str_replace('<br />', '', $subs_solicitacao);
                $subs_solicitacao = str_replace('"', '\'', $subs_solicitacao);

                // CONFIGURAÇÃO DA OBSERVAÇÃO
                $subs_obs = nl2br($subs_obs);
                $subs_obs = str_replace('<br />', '', $subs_obs);
                $subs_obs = str_replace('"', '\'', $subs_obs);

                // DATA DA VALIDADE NÃO PODE SER MENOR QUE A DATA DE HOJE
                if (!empty($subs_data_validade) && strtotime($subs_data_validade) < strtotime('today')) {
                  $subst_status = 'EXPIRADO';
                  $subs_status_color = 'bg_info_roxo';
                } else {
                  if ($subs_status == 1) {
                    $subs_status_color = 'bg_info_verde';
                  } else if ($subs_status == 2) {
                    $subs_status_color = 'bg_info_vermelho';
                  } else {
                    $subs_status_color = 'bg_info_laranja';
                  }
                }

                // CONFIGURAÇÃO DO STATUS
                if ($user_brasileiro == 1) {
                  $tipo_doc = 'CPF';

                  // ADICIONA PONTUAÇÃO AO CPF
                  $nbr_cpf = $user_doc;
                  $parte_um     = substr($nbr_cpf, 0, 3);
                  $parte_dois   = substr($nbr_cpf, 3, 3);
                  $parte_tres   = substr($nbr_cpf, 6, 3);
                  $parte_quatro = substr($nbr_cpf, 9, 2);
                  $doc = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";
                } else {
                  $tipo_doc = 'Passaporte';
                  $doc = $user_brasileiro;
                }

            ?>

                <tr>
                  <th>
                    <nobr><?= $proponente ?></nobr>
                  </th>
                  <td>
                    <nobr><?= $user_email ?></nobr>
                  </td>
                  <td><?= $perfil_user ?></td>
                  <td nowrap="nowrap"><span class="hide_data">
                      <nobr><?= date('Ymd i:H', strtotime($subs_data_cad)) ?>
                    </span><?= date('d/m/Y - H:i', strtotime($subs_data_cad)) ?></nobr>
                  </td>
                  <td><span class="badge <?= $subs_status_color ?>"><?= $subst_status ?></span></td>
                  <?php if (!empty($subs_data_validade)) { ?>
                    <td><span class="hide_data"><?= date('Ymd', strtotime($subs_data_validade)) ?></span><?= date('d/m/Y', strtotime($subs_data_validade)) ?></td>
                  <?php } else { ?>
                    <td><span class="badge bg_info_cinza">SEM VALIDADE</span></td>
                  <?php } ?>
                  <td class="text-end">
                    <div class="dropdown drop_tabela d-inline-block">
                      <button class="btn btn_soft_azul_escuro btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-fill align-middle"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="" class="dropdown-item edit-item-btn" data-bs-toggle="modal" data-bs-target="#modal_dadosSubmissao" data-bs-user_id="<?= $user_id ?>"
                            data-bs-user_brasileiro="<?= $user_brasileiro ?>"
                            data-bs-user_nome="<?= $user_nome ?>"
                            data-bs-user_nome_social="<?= $user_nome_social ?>"
                            data-bs-user_cpf="<?= $doc ?>"
                            data-bs-user_email="<?= $user_email ?>"
                            data-bs-user_pass="<?= $user_doc ?>"
                            data-bs-user_nacionalidade="<?= $pais_nome ?>"
                            data-bs-user_rg="<?= $user_rg ?>"
                            data-bs-user_data_nascimento="<?= date('d/m/Y', strtotime($user_data_nascimento)) ?>"
                            data-bs-gen_genero="<?= $gen_genero ?>"
                            data-bs-raca_raca="<?= $raca_raca ?>"
                            data-bs-user_contato="<?= $user_contato ?>"
                            data-bs-us_pe_perfil="<?= $perfil_user ?>"
                            data-bs-user_cep="<?= $user_cep ?>"
                            data-bs-user_rua="<?= $user_rua ?>"
                            data-bs-user_numero="<?= $user_numero ?>"
                            data-bs-user_bairro="<?= $user_bairro ?>"
                            data-bs-user_municipio="<?= $user_municipio ?>"
                            data-bs-user_estado="<?= $user_estado ?>"
                            data-bs-user_endereco="<?= str_replace('<BR />', '', $user_endereco) ?>"
                            data-bs-esc_escolaridade="<?= $esc_escolaridade ?>"
                            data-bs-user_instituicao_ensino="<?= $user_instituicao_ensino ?>"
                            data-bs-user_lattes="<?= $user_lattes ?>"
                            data-bs-user_instagram="<?= $user_instagram ?>"
                            data-bs-user_facebook="<?= $user_facebook ?>"
                            data-bs-user_linkedin="<?= $user_linkedin ?>"
                            data-bs-arq_arquivo="<?= $arq_arquivo ?>"
                            data-bs-user_user_id="<?= $user_user_id ?>"
                            data-bs-subs_id="<?= $subs_id ?>"
                            data-bs-subs_solicitacao="<?= $subs_solicitacao ?>"
                            data-bs-subs_status="<?= $subs_status ?>"
                            data-bs-subs_data_validade="<?= $subs_data_validade ?>"
                            data-bs-subs_obs="<?= $subs_obs ?>"
                            data-bs-subst_id="<?= $subst_id ?>"
                            data-bs-subst_status="<?= $subst_status ?>"
                            title="Dados da Solicitação"><i class="fa-solid fa-check-double me-2"></i>Dados da Solicitação</a></li>
                        <li><a href="controller/controller_submissao.php?funcao=exc_subs&id=<?= $subs_id ?>" class="dropdown-item remove-item-btn del-btn" title="Excluir"><i class="fa-regular fa-trash-can me-2"></i> Excluir</a></li>
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
<div class="modal fade modal_padrao" id="modal_dadosSubmissao" tabindex="-1" aria-labelledby="modal_dadosSubmissao" aria-modal="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modal_dadosSubmissao">Solicitação</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <div class="card-header mt-3">
        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist" id="nav-tabs">
          <li class="nav-item link_nav_perfil">
            <a class="nav-link text-body active" data-bs-toggle="tab" href="#home1" role="tab">Solicitação</a>
          </li>
          <li class="nav-item link_nav_perfil">
            <a class="nav-link text-body" data-bs-toggle="tab" href="#profile1" role="tab">Dados do Usuário</a>
          </li>
        </ul>
      </div>

      <div class="modal-body">
        <div class="tab-content text-muted">
          <div class="tab-pane active" id="home1" role="tabpanel">

            <form method="POST" action="controller/controller_submissao.php?funcao=cad_subm_autoriza" class="needs-validation" id="ValidaBotaoProgressPadrao" novalidate>
              <div class="row g-3">

                <input type="hidden" class="form-control subs_id" name="subs_id" required>
                <input type="hidden" class="form-control user_user_id" name="user_user_id" required>
                <input type="hidden" class="form-control user_email" name="user_email" required>

                <div class="col-12">
                  <div>
                    <label class="form-label">Solicitação <span>*</span></label>
                    <textarea class="form-control subs_solicitacao" name="" id="subs_solicitacao" rows="4" readonly></textarea>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-12">
                  <div>
                    <label class="form-label">Autorização <span>*</span></label>
                    <select class="form-select subs_status" name="subs_status" id="subs_status" required>
                      <!-- <option selected disabled></option> -->
                      <?php $sql = $conn->query("SELECT subst_id, subst_status FROM submissao_status WHERE subst_id IN (1,2)");
                      while ($subst = $sql->fetch(PDO::FETCH_ASSOC)) {
                        extract($subst); ?>
                        <option value="<?= $subst_id ?>"><?= $subst_status ?></option>
                      <?php } ?>
                    </select>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-12" id="campo_data_validade" style="display: none;">
                  <div>
                    <label class="form-label">Data de validade da licença</label>
                    <input type="date" class="form-control subs_data_validade" name="subs_data_validade" id="subs_data_validade" onblur="checkDateInicio()">
                    <p class="label_info text-end mt-1 mb-n3">Se a data não for informada, a autorização não terá validade</p>
                  </div>
                </div>
                <script>
                  const subs_status = document.getElementById("subs_status");
                  const campo_data_validade = document.getElementById("campo_data_validade");
                  subs_status.addEventListener("change", function() {
                    if (subs_status.value === "1") {
                      campo_data_validade.style.display = "block";
                      document.getElementById('data_validade').required = true;
                    } else {
                      campo_data_validade.style.display = "none";
                      document.getElementById('data_validade').required = false;
                    }
                  });
                </script>
                <script>
                  // Função para verificar se a data é maior que a data atual
                  function validarData() {
                    var inputData = document.getElementById('subs_data_validade').value;
                    var subs_data_validade = new Date(inputData);
                    var dataAtual = new Date();

                    if (subs_data_validade < dataAtual) {
                      // Mostrar SweetAlert se a data de nascimento for maior que a data atual
                      Swal.fire({
                        title: 'Data Inválida!',
                        text: 'A data de validade não pode ser maior que a data de hoje!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                      });
                      // Limpar o campo após mostrar o alerta (opcional)
                      document.getElementById('subs_data_validade').value = '';
                    }
                  }

                  // Event listener para o evento blur no campo de data
                  document.getElementById('subs_data_validade').addEventListener('blur', validarData);
                </script>
                <div class="col-12">
                  <div>
                    <label class="form-label">Observação <span>*</span></label>
                    <textarea class="form-control subs_obs" name="subs_obs" id="" rows="4" required></textarea>
                    <div class="invalid-feedback">Este campo é obrigatório</div>
                  </div>
                </div>

                <div class="col-lg-12">
                  <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                    <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                    <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
                    <button type="submit" class="btn botao botao_verde waves-effect">Salvar</button>
                  </div>
                </div>
              </div>
            </form>

          </div>

          <div class="tab-pane" id="profile1" role="tabpanel">
            <div class="accordion custom-accordionwithicon accordion-flush accordion-fill-success" id="accordionFill">
              <div class="accordion-item">
                <h2 class="accordion-header" id="accordionFillExample1">
                  <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill1" aria-expanded="true" aria-controls="accor_fill1">
                    DADOS DO USUÁRIO
                  </button>
                </h2>
                <div id="accor_fill1" class="accordion-collapse collapse show" aria-labelledby="accordionFillExample1" data-bs-parent="#accordionFill">
                  <div class="accordion-body">

                    <div class="row dados_user_tabela">

                      <div class="col-lg-6">
                        <label>Nome Completo</label>
                        <p class="user_nome"></p>
                      </div>

                      <div class="col-lg-6">
                        <label>Nome Social</label>
                        <p class="user_nome_social"></p>
                      </div>

                      <div class="col-lg-6">
                        <label>E-mail</label>
                        <p class="user_email_dados"></p>
                      </div>

                      <div class="col-lg-6">
                        <label>Contato</label>
                        <p class="user_contato"></p>
                      </div>

                      <div class="col-lg-6" id="campo_user_cpf">
                        <label>CPF</label>
                        <p class="user_cpf"></p>
                      </div>

                      <div class="col-lg-6" id="campo_user_rg">
                        <label>RG</label>
                        <p class="user_rg"></p>
                      </div>

                      <div class="col-lg-6" id="campo_user_passaporte">
                        <label>Passaporte</label>
                        <p class="user_pass"></p>
                      </div>

                      <div class="col-lg-6" id="campo_user_nacionalidade">
                        <label>Nacionalidade</label>
                        <p class="user_nacionalidade"></p>
                      </div>

                      <div class="col-lg-6">
                        <label>Data de Nascimento</label>
                        <p class="user_data_nascimento"></p>
                      </div>

                      <div class="col-lg-6">
                        <label>Gênero</label>
                        <p class="gen_genero"></p>
                      </div>

                      <div class="col-lg-6">
                        <label>Raça/Cor</label>
                        <p class="raca_raca"></p>
                      </div>

                      <div class="col-lg-6">
                        <label>Perfil</label>
                        <p class="us_pe_perfil"></p>
                      </div>

                    </div>

                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header" id="accordionFillExample2">
                  <button class="accordion-button fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill2" aria-expanded="false" aria-controls="accor_fill2">
                    ENDEREÇO
                  </button>
                </h2>
                <div id="accor_fill2" class="accordion-collapse collapse" aria-labelledby="accordionFillExample2" data-bs-parent="#accordionFill">
                  <div class="accordion-body">

                    <div id="campo_user_enderece_all">

                      <div class="row dados_user_tabela">

                        <div class="col-lg-6">
                          <label>CEP</label>
                          <p class="user_cep"></p>
                        </div>

                        <div class="col-lg-6">
                          <label>Logradouro</label>
                          <p class="user_rua">O</p>
                        </div>

                        <div class="col-lg-6">
                          <label>Número</label>
                          <p class="user_numero"></p>
                        </div>

                        <div class="col-lg-6">
                          <label>Bairro</label>
                          <p class="user_bairro"></p>
                        </div>

                        <div class="col-lg-6">
                          <label>Município</label>
                          <p class="user_municipio"></p>
                        </div>

                        <div class="col-lg-6">
                          <label>Estado</label>
                          <p class="user_estado"></p>
                        </div>

                      </div>

                    </div>

                    <div id="campo_user_enderece">
                      <div class="row dados_user_tabela">

                        <div class="col-12">
                          <label>Endereço</label>
                          <p class="user_endereco"></p>
                        </div>

                      </div>
                    </div>

                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header" id="accordionFillExample3">
                  <button class="accordion-button fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill3" aria-expanded="false" aria-controls="accor_fill3">
                    ESCOLARIDADE
                  </button>
                </h2>
                <div id="accor_fill3" class="accordion-collapse collapse" aria-labelledby="accordionFillExample3" data-bs-parent="#accordionFill">
                  <div class="accordion-body">

                    <div class="row dados_user_tabela">

                      <div class="col-12">
                        <label>Nível de escolaridade</label>
                        <p class="esc_escolaridade"></p>
                      </div>

                      <div class="col-12">
                        <label>Instituição de Ensino</label>
                        <p class="user_instituicao_ensino"></p>
                      </div>

                      <div class="col-12" id="campo_user_lattes">
                        <label>Link do lattes</label>
                        <p class="link_url user_lattes"></p>
                      </div>

                      <div class="col-12" id="campo_arq_none">
                        <label>Currículo Vitae</label>
                        <p class="link_url arq_arquivo"></p>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header" id="accordionFillExample4">
                  <button class="accordion-button fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accor_fill4" aria-expanded="false" aria-controls="accor_fill4">
                    REDES SOCIAIS
                  </button>
                </h2>
                <div id="accor_fill4" class="accordion-collapse collapse" aria-labelledby="accordionFillExample4" data-bs-parent="#accordionFill">
                  <div class="accordion-body">

                    <div class="dados_user_tabela icon_social">
                      <div class="icon_social">

                        <p class="icon_social user_facebook"></p>
                        <p class="icon_social user_instagram"></p>
                        <p class="icon_social user_linkedin"></p>

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
  </div>
</div>


<script>
  const modal_dadosSubmissao = document.getElementById('modal_dadosSubmissao')
  if (modal_dadosSubmissao) {
    modal_dadosSubmissao.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget
      // EXTRAI DADOS DO data-bs-* 
      //const user_id = button.getAttribute('data-bs-user_id')
      //const user_brasileiro = button.getAttribute('data-bs-user_brasileiro')
      const user_nome = button.getAttribute('data-bs-user_nome')
      const user_nome_social = button.getAttribute('data-bs-user_nome_social')
      const user_cpf = button.getAttribute('data-bs-user_cpf')
      const user_pass = button.getAttribute('data-bs-user_pass')
      const user_email = button.getAttribute('data-bs-user_email')
      const user_email_dados = button.getAttribute('data-bs-user_email')
      const user_rg = button.getAttribute('data-bs-user_rg')
      const user_nacionalidade = button.getAttribute('data-bs-user_nacionalidade')
      const user_data_nascimento = button.getAttribute('data-bs-user_data_nascimento')
      const gen_genero = button.getAttribute('data-bs-gen_genero')
      const raca_raca = button.getAttribute('data-bs-raca_raca')
      const user_contato = button.getAttribute('data-bs-user_contato')
      const us_pe_perfil = button.getAttribute('data-bs-us_pe_perfil')
      const user_cep = button.getAttribute('data-bs-user_cep')
      const user_rua = button.getAttribute('data-bs-user_rua')
      const user_numero = button.getAttribute('data-bs-user_numero')
      const user_bairro = button.getAttribute('data-bs-user_bairro')
      const user_municipio = button.getAttribute('data-bs-user_municipio')
      const user_estado = button.getAttribute('data-bs-user_estado')
      const user_endereco = button.getAttribute('data-bs-user_endereco')
      const esc_escolaridade = button.getAttribute('data-bs-esc_escolaridade')
      const user_instituicao_ensino = button.getAttribute('data-bs-user_instituicao_ensino')
      const user_lattes = button.getAttribute('data-bs-user_lattes')
      const user_instagram = button.getAttribute('data-bs-user_instagram')
      const user_facebook = button.getAttribute('data-bs-user_facebook')
      const user_linkedin = button.getAttribute('data-bs-user_linkedin')
      const arq_arquivo = button.getAttribute('data-bs-arq_arquivo')
      const user_user_id = button.getAttribute('data-bs-user_user_id')
      const subs_id = button.getAttribute('data-bs-subs_id')
      const subs_solicitacao = button.getAttribute('data-bs-subs_solicitacao')
      const subs_status = button.getAttribute('data-bs-subs_status')
      const subs_data_validade = button.getAttribute('data-bs-subs_data_validade')
      const subs_obs = button.getAttribute('data-bs-subs_obs')
      // const subst_id = button.getAttribute('data-bs-subst_id')
      // const subst_status = button.getAttribute('data-bs-subst_status')
      // 
      const modalTitle = modal_dadosSubmissao.querySelector('.modal-title')
      //const modal_user_id = modal_dadosSubmissao.querySelector('.user_id')
      //const modal_user_brasileiro = modal_dadosSubmissao.querySelector('.user_brasileiro')
      const modal_user_nome = modal_dadosSubmissao.querySelector('.user_nome')
      const modal_user_nome_social = modal_dadosSubmissao.querySelector('.user_nome_social')
      const modal_user_cpf = modal_dadosSubmissao.querySelector('.user_cpf')
      const modal_user_pass = modal_dadosSubmissao.querySelector('.user_pass')
      const modal_user_email = modal_dadosSubmissao.querySelector('.user_email')
      const modal_user_email_dados = modal_dadosSubmissao.querySelector('.user_email_dados')
      const modal_user_rg = modal_dadosSubmissao.querySelector('.user_rg')
      const modal_user_nacionalidade = modal_dadosSubmissao.querySelector('.user_nacionalidade')
      const modal_user_data_nascimento = modal_dadosSubmissao.querySelector('.user_data_nascimento')
      const modal_gen_genero = modal_dadosSubmissao.querySelector('.gen_genero')
      const modal_raca_raca = modal_dadosSubmissao.querySelector('.raca_raca')
      const modal_user_contato = modal_dadosSubmissao.querySelector('.user_contato')
      const modal_us_pe_perfil = modal_dadosSubmissao.querySelector('.us_pe_perfil')
      const modal_user_cep = modal_dadosSubmissao.querySelector('.user_cep')
      const modal_user_rua = modal_dadosSubmissao.querySelector('.user_rua')
      const modal_user_numero = modal_dadosSubmissao.querySelector('.user_numero')
      const modal_user_bairro = modal_dadosSubmissao.querySelector('.user_bairro')
      const modal_user_municipio = modal_dadosSubmissao.querySelector('.user_municipio')
      const modal_user_estado = modal_dadosSubmissao.querySelector('.user_estado')
      const modal_user_endereco = modal_dadosSubmissao.querySelector('.user_endereco')
      const modal_esc_escolaridade = modal_dadosSubmissao.querySelector('.esc_escolaridade')
      const modal_user_instituicao_ensino = modal_dadosSubmissao.querySelector('.user_instituicao_ensino')
      const modal_user_lattes = modal_dadosSubmissao.querySelector('.user_lattes')
      const modal_user_instagram = modal_dadosSubmissao.querySelector('.user_instagram')
      const modal_user_facebook = modal_dadosSubmissao.querySelector('.user_facebook')
      const modal_user_linkedin = modal_dadosSubmissao.querySelector('.user_linkedin')
      const modal_arq_arquivo = modal_dadosSubmissao.querySelector('.arq_arquivo')
      const modal_user_user_id = modal_dadosSubmissao.querySelector('.user_user_id')
      const modal_subs_id = modal_dadosSubmissao.querySelector('.subs_id')
      const modal_subs_solicitacao = modal_dadosSubmissao.querySelector('.subs_solicitacao')
      const modal_subs_status = modal_dadosSubmissao.querySelector('.subs_status')
      const modal_subs_data_validade = modal_dadosSubmissao.querySelector('.subs_data_validade')
      const modal_subs_obs = modal_dadosSubmissao.querySelector('.subs_obs')
      // const modal_subst_id = modal_dadosSubmissao.querySelector('.subst_id')
      // const modal_subst_status = modal_dadosSubmissao.querySelector('.subst_status')
      //
      modalTitle.textContent = 'Solicitação'
      //modal_user_id.value = user_id
      //modal_user_brasileiro.innerHTML = user_brasileiro
      modal_user_nome.innerHTML = user_nome
      modal_user_nome_social.innerHTML = user_nome_social
      modal_user_cpf.innerHTML = user_cpf
      modal_user_pass.innerHTML = user_pass
      modal_user_email.value = user_email
      modal_user_email_dados.innerHTML = user_email_dados
      modal_user_rg.innerHTML = user_rg
      modal_user_nacionalidade.innerHTML = user_nacionalidade
      modal_user_data_nascimento.innerHTML = user_data_nascimento
      modal_gen_genero.innerHTML = gen_genero
      modal_raca_raca.innerHTML = raca_raca
      modal_user_contato.innerHTML = user_contato
      modal_us_pe_perfil.innerHTML = us_pe_perfil
      modal_user_cep.innerHTML = user_cep
      modal_user_rua.innerHTML = user_rua
      modal_user_numero.innerHTML = user_numero
      modal_user_bairro.innerHTML = user_bairro
      modal_user_municipio.innerHTML = user_municipio
      modal_user_estado.innerHTML = user_estado
      modal_user_endereco.innerHTML = user_endereco
      modal_esc_escolaridade.innerHTML = esc_escolaridade
      modal_user_instituicao_ensino.innerHTML = user_instituicao_ensino
      modal_user_lattes.innerHTML = user_lattes
      modal_user_instagram.innerHTML = user_instagram
      modal_user_facebook.innerHTML = user_facebook
      modal_user_linkedin.innerHTML = user_linkedin
      modal_arq_arquivo.innerHTML = arq_arquivo
      modal_user_user_id.value = user_user_id
      modal_subs_id.value = subs_id
      modal_subs_solicitacao.value = subs_solicitacao
      modal_subs_status.value = subs_status
      modal_subs_data_validade.value = subs_data_validade
      modal_subs_obs.value = subs_obs
      // modal_subst_id.value = subst_id
      // modal_subst_status.value = subst_status

      if (user_nacionalidade) {
        document.getElementById('campo_user_nacionalidade').style.display = "block";
        document.getElementById('campo_user_passaporte').style.display = "block";
        document.getElementById('campo_user_enderece').style.display = "block";

        document.getElementById('campo_user_enderece_all').style.display = "none";
        document.getElementById('campo_user_cpf').style.display = "none";
        document.getElementById('campo_user_rg').style.display = "none";
      } else {
        document.getElementById('campo_user_nacionalidade').style.display = "none";
        document.getElementById('campo_user_passaporte').style.display = "none";
        document.getElementById('campo_user_enderece').style.display = "none";

        document.getElementById('campo_user_enderece_all').style.display = "block";
        document.getElementById('campo_user_cpf').style.display = "block";
        document.getElementById('campo_user_rg').style.display = "block";
      }

      if (user_lattes) {
        document.getElementById('campo_user_lattes').style.display = "block";
      } else {
        document.getElementById('campo_user_lattes').style.display = "none";
      }

      if (subs_status == 1) {
        document.getElementById('campo_data_validade').style.display = "block";
      }

      if (arq_arquivo) {
        document.getElementById('campo_arq_none').style.display = "block";
      } else {
        document.getElementById('campo_arq_none').style.display = "none";
      }
    })
  }
</script>

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>