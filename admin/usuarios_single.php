<?php include 'includes/header.php'; ?>

<?php
// DADOS DO ADMINISTRADOR
$user_id = base64_decode($_GET['i']);
$sql = $conn->prepare("SELECT * FROM usuarios
                        LEFT JOIN usuario_perfil ON usuario_perfil.us_pe_id = usuarios.user_perfil
                        LEFT JOIN generos ON generos.gen_id = usuarios.user_genero
                        LEFT JOIN racas ON racas.raca_id = usuarios.user_raca                                  
                        LEFT JOIN escolaridades ON escolaridades.esc_id = usuarios.user_escolaridade
                        LEFT JOIN paises ON paises.pais_id = usuarios.user_nacionalidade
                        LEFT JOIN usuarios_arq ON usuarios_arq.arq_user_id = usuarios.user_id
                        WHERE usuarios.user_id = :user_id");
$sql->bindParam(':user_id', $user_id);
$sql->execute();
$row = $sql->fetch(PDO::FETCH_ASSOC);

$user_id                   = trim(isset($row['user_id'])) ? $row['user_id'] : NULL;
$user_brasileiro           = trim(isset($row['user_brasileiro'])) ? $row['user_brasileiro'] : NULL;
$user_nome_completo        = trim(isset($row['user_nome'])) ? $row['user_nome'] : NULL;
$user_nome_social          = trim(isset($row['user_nome_social'])) ? $row['user_nome_social'] : NULL;
$user_doc                  = trim(isset($row['user_doc'])) ? $row['user_doc'] : NULL;
$user_nacionalidade        = trim(isset($row['user_nacionalidade'])) ? $row['user_nacionalidade'] : NULL;
$user_email                = trim(isset($row['user_email'])) ? $row['user_email'] : NULL;
$user_data_nascimento      = trim(isset($row['user_data_nascimento'])) ? $row['user_data_nascimento'] : NULL;
$user_vinculo              = trim(isset($row['user_vinculo'])) ? $row['user_vinculo'] : NULL;
$user_rg                   = trim(isset($row['user_rg'])) ? $row['user_rg'] : NULL;
$user_genero               = trim(isset($row['user_genero'])) ? $row['user_genero'] : NULL;
$user_raca                 = trim(isset($row['user_raca'])) ? $row['user_raca'] : NULL;
$user_contato              = trim(isset($row['user_contato'])) ? $row['user_contato'] : NULL;
$user_perfil               = trim(isset($row['user_perfil'])) ? $row['user_perfil'] : NULL;
$user_outro_perfil         = trim(isset($row['user_outro_perfil'])) ? $row['user_outro_perfil'] : NULL;
$user_cep                  = trim(isset($row['user_cep'])) ? $row['user_cep'] : NULL;
$user_rua                  = trim(isset($row['user_rua'])) ? $row['user_rua'] : NULL;
$user_numero               = trim(isset($row['user_numero'])) ? $row['user_numero'] : NULL;
$user_bairro               = trim(isset($row['user_bairro'])) ? $row['user_bairro'] : NULL;
$user_municipio            = trim(isset($row['user_municipio'])) ? $row['user_municipio'] : NULL;
$user_estado               = trim(isset($row['user_estado'])) ? $row['user_estado'] : NULL;
$user_endereco             = trim(isset($row['user_endereco'])) ? $row['user_endereco'] : NULL;
$user_escolaridade         = trim(isset($row['user_escolaridade'])) ? $row['user_escolaridade'] : NULL;
$user_instituicao_ensino   = trim(isset($row['user_instituicao_ensino'])) ? $row['user_instituicao_ensino'] : NULL;
$user_lattes               = trim(isset($row['user_lattes'])) ? $row['user_lattes'] : NULL;
$user_facebook             = trim(isset($row['user_facebook'])) ? $row['user_facebook'] : NULL;
$user_instagram            = trim(isset($row['user_instagram'])) ? $row['user_instagram'] : NULL;
$user_linkedin             = trim(isset($row['user_linkedin'])) ? $row['user_linkedin'] : NULL;
$user_vinculo_atividade    = trim(isset($row['user_vinculo_atividade'])) ? $row['user_vinculo_atividade'] : NULL;
$user_receber_notificacoes = trim(isset($row['user_receber_notificacoes'])) ? $row['user_receber_notificacoes'] : NULL;
$user_nivel_acesso         = trim(isset($row['nivel_acesso'])) ? $row['nivel_acesso'] : NULL;
$user_status                      = trim(isset($row['user_status'])) ? $row['user_status'] : NULL;
// USUÁRIO NACIONALIDADE
$pais_id                   = trim(isset($row['pais_id'])) ? $row['pais_id'] : NULL;
$pais_nome                 = trim(isset($row['pais_nome'])) ? $row['pais_nome'] : NULL;
// USUÁRIO GÊNERO
$gen_id                    = trim(isset($row['gen_id'])) ? $row['gen_id'] : NULL;
$gen_genero                = trim(isset($row['gen_genero'])) ? $row['gen_genero'] : NULL;
// USUÁRIO RAÇA
$raca_id                   = trim(isset($row['raca_id'])) ? $row['raca_id'] : NULL;
$raca_raca                 = trim(isset($row['raca_raca'])) ? $row['raca_raca'] : NULL;
// USUÁRIO PERFIL
$us_pe_id                  = trim(isset($row['us_pe_id'])) ? $row['us_pe_id'] : NULL;
$us_pe_perfil              = trim(isset($row['us_pe_perfil'])) ? $row['us_pe_perfil'] : NULL;
// USUÁRIO ESCOLARIDADE
$esc_id                    = trim(isset($row['esc_id'])) ? $row['esc_id'] : NULL;
$esc_escolaridade          = trim(isset($row['esc_escolaridade'])) ? $row['esc_escolaridade'] : NULL;
// ARQUIVO
$arq_id                    = trim(isset($row['arq_id'])) ? $row['arq_id'] : NULL;
$arq_arquivo               = trim(isset($row['arq_arquivo'])) ? $row['arq_arquivo'] : NULL;
$arq_user_id               = trim(isset($row['arq_user_id'])) ? $row['arq_user_id'] : NULL;


// SE UM NOME SOCIAL FOR CADASTRO, ESTE DEVERÁ SER O NOME PRINCIPAL
if (empty($row['user_nome_social'])) {
  $user_nome = $row['user_nome'];
} else {
  $user_nome = $row['user_nome_social'];
}

// SE UM "DESCRIÇÃO DO PERFIL" FOR CADASTRO, ESTE DEVERÁ SER O EXIBIDO
if ($user_perfil == 9) {
  $user_perfil = $row['user_outro_perfil'];
} else {
  $user_perfil = $row['us_pe_perfil'];
}

// PEGA O PRIMEIRO NOME E ÚLTIMO NOME
$partesNome = explode(" ", $user_nome);
$primeiroNome = $partesNome[0];
$ultimoNome = end($partesNome);

// PEGA A PRIMEIRO E ÚLTIMO LETRA
$firstNameInitial = strtoupper(substr($partesNome[0], 0, 1)); // PEGA A PRIMEIRA LETRA DO PRIMEIRO NOME
$lastNameInitial = strtoupper(substr(end($partesNome), 0, 1)); // PEGA A PRIMEIRA LETRA DO ÚLTIMO NOME
$iniciais = $firstNameInitial . $lastNameInitial; // RETORNA AS INICIAIS
?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg"></div>
</div>

<div class="pt-4 mb-4 mb-lg-3 pb-lg-3 profile-wrapper header_perfil">
  <div class="row g-4 align-items-center">
    <div class="col-auto">
      <div class="avatar_perfil"><?= $iniciais ?></div>
    </div>
    <div class="col">
      <div>
        <h3 class="text-white mb-1"><?= $primeiroNome . '&nbsp;&nbsp;' . $ultimoNome ?></h3>
        <p><?= $user_perfil ?></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div>
      <div class="d-flex profile-wrapper header_perfil_nav">
        <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#dados-tab" role="tab" aria-selected="true" title="Dados do Usuário">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person d-inline-block d-sm-none" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
              </svg> <span class="d-none d-sm-inline-block">Dados do Usuário</span>
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link fs-14" data-bs-toggle="tab" href="#proj-tab" role="tab" aria-selected="false" tabindex="-1" title="Projetos">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-clipboard-data d-inline-block d-sm-none" viewBox="0 0 16 16">
                <path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0z" />
                <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z" />
                <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z" />
              </svg><span class="d-none d-sm-inline-block">Projetos</span>
            </a>
          </li>
        </ul>
        <div class="social_icon_header">
          <?php if ($user_facebook) { ?><a href="<?= $user_facebook ?>" target="_blank"><i class="fa-brands fa-facebook-f"></i></a><?php } ?>
          <?php if ($user_instagram) { ?><a href="<?= $user_instagram ?>" target="_blank"><i class="fa-brands fa-instagram"></i></a><?php } ?>
          <?php if ($user_linkedin) { ?><a href="<?= $user_linkedin ?>" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a><?php } ?>
        </div>
        <!-- <div class="flex-shrink-0">
          <a href="pages-profile-settings.html" class="btn btn-success"><i class="ri-edit-box-line align-bottom"></i> Edit Profile</a>
        </div> -->
      </div>
      <!-- Tab panes -->
      <div class="tab-content pt-4 text-muted">
        <div class="tab-pane active" id="dados-tab" role="tabpanel">
          <div class="row">
            <div class="col-lg-12">
              <div class="card tabs_perfil">

                <div class="card-header">
                  <div class="row align-items-center">
                    <div class="col-sm-12">
                      <h5 class="card-title mb-0">Meus Dados</h5>
                    </div>
                  </div>
                </div>

                <div class="card-body p-4">

                  <div class="tab-content">

                    <form class="needs-validation" method="POST" action="controller/controller_usuarios.php" enctype="multipart/form-data" autocomplete="off" novalidate>

                      <div class="tab-pane active" id="tabDados" role="tabpanel">
                        <div class="row grid gx-3">

                          <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3">
                              <label class="form-label">Nome Completo</label>
                              <input type="text" class="form-control text-uppercase" value="<?= $user_nome_completo ?>" disabled>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3">
                              <label class="form-label">Nome Social</label>
                              <input type="text" class="form-control text-uppercase" value="<?= $user_nome_social ?>" disabled>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3">
                              <label class="form-label">E-mail</label>
                              <input type="email" class="form-control text-lowercase" value="<?= $user_email ?>" disabled>
                            </div>
                          </div>

                          <?php if ($user_brasileiro == 1) { ?>

                            <div class="col-md-6 col-lg-4 col-xl-3">
                              <div class="mb-3">
                                <label class="form-label">Contato</label>
                                <input type="text" class="form-control text-uppercase" value="<?= $user_contato ?>" disabled>
                              </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-3">
                              <div class="mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" class="form-control text-uppercase cpf" value="<?= $user_doc ?>" disabled>
                              </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-3">
                              <div class="mb-3">
                                <label class="form-label">RG</label>
                                <input type="text" class="form-control text-uppercase" value="<?= $user_rg ?>" disabled>
                              </div>
                            </div>

                          <?php } else { ?>

                            <div class="col-md-6 col-lg-4 col-xl-3">
                              <div class="mb-3">
                                <label class="form-label">Contato</label>
                                <input type="text" class="form-control" value="<?= $user_contato ?>" disabled>
                              </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-3">
                              <div class="mb-3">
                                <label class="form-label">Passaporte</label>
                                <input type="text" class="form-control text-uppercase" value="<?= $user_doc ?>" disabled>
                              </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-3">
                              <div class="mb-3">
                                <?php try {
                                  $sql = $conn->query("SELECT * FROM paises ORDER BY pais_nome");
                                  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                                } catch (PDOException $e) {
                                  // echo "Erro: " . $e->getMessage();
                                  echo "Erro ao tentar recuperar o perfil";
                                } ?>
                                <label class="form-label">Nacionalidade</label>
                                <select class="form-select" disabled>
                                  <option selected disabled value="<?= $pais_id ?>"><?= $pais_nome ?></option>
                                  <?php foreach ($result as $res) : ?>
                                    <option value="<?= $res['pais_id'] ?>"><?= $res['pais_nome'] ?></option>
                                  <?php endforeach; ?>
                                </select>
                              </div>
                            </div>

                          <?php } ?>

                          <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3">
                              <label class="form-label">Data de Nascimento</label>
                              <input type="date" class="form-control" value="<?= $user_data_nascimento ?>" disabled>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3">
                              <?php try {
                                $sql = $conn->query("SELECT * FROM generos");
                                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                              } catch (PDOException $e) {
                                // echo "Erro: " . $e->getMessage();
                                echo "Erro ao tentar recuperar o perfil";
                              } ?>
                              <label class="form-label">Com qual gênero você se identifica</label>
                              <select class="form-select text-uppercase" value="<?= $user_genero ?>" disabled>
                                <option selected value="<?= $gen_id ?>"><?= $gen_genero ?></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['gen_id'] ?>"><?= $res['gen_genero'] ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3">
                              <?php try {
                                $sql = $conn->query("SELECT * FROM racas");
                                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                              } catch (PDOException $e) {
                                // echo "Erro: " . $e->getMessage();
                                echo "Erro ao tentar recuperar o perfil";
                              } ?>
                              <label class="form-label">Raça/Cor</label>
                              <select class="form-select text-uppercase" value="<?= $user_raca ?>" disabled>
                                <option selected value="<?= $raca_id ?>"><?= $raca_raca ?></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['raca_id'] ?>"><?= $res['raca_raca'] ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3">
                              <?php try {
                                $sql = $conn->query("SELECT * FROM usuario_perfil ORDER BY CASE WHEN us_pe_perfil = 'OUTRO' THEN 1 ELSE 0 END, us_pe_perfil");
                                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                              } catch (PDOException $e) {
                                // echo "Erro: " . $e->getMessage();
                                echo "Erro ao tentar recuperar o perfil";
                              } ?>
                              <label class="form-label">Em qual perfil você se encaixaria melhor</label>
                              <select class="form-select text-uppercase" disabled>
                                <option selected value="<?= $us_pe_id ?>"><?= $us_pe_perfil ?></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['us_pe_id'] ?>"><?= $res['us_pe_perfil'] ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-4 col-xl-3" id="campo_user_outro_perfil" style="display: none;">
                            <div class="mb-3">
                              <label class="form-label">Descreva o perfil</label>
                              <input type="text" class="form-control text-uppercase" id="user_outro_perfil" value="<?= $user_outro_perfil ?>" disabled>
                              <div class="invalid-feedback">Este campo é obrigatório</div>
                            </div>
                            <script>
                              const user_perfil = document.getElementById("user_perfil");
                              const campo_user_outro_perfil = document.getElementById("campo_user_outro_perfil");

                              user_perfil.addEventListener("change", function() {
                                if (user_perfil.value == 9) {
                                  campo_user_outro_perfil.style.display = "block";
                                  document.getElementById("user_outro_perfil").required = true;
                                } else {
                                  campo_user_outro_perfil.style.display = "none";
                                  document.getElementById("user_outro_perfil").required = false;
                                }
                              });

                              if (user_perfil.value == 9) {
                                campo_user_outro_perfil.style.display = "block";
                                document.getElementById("user_outro_perfil").required = true;
                              } else {
                                campo_user_outro_perfil.style.display = "none";
                                document.getElementById("user_outro_perfil").required = false;
                              }
                            </script>
                          </div>

                          <div class="col-12 mb-2">
                            <div class="form-check">
                              <input class="form-check-input opacity-100" type="checkbox" id="vinculo" value="1" <?php echo ($user_vinculo == 1) ? 'checked' : ''; ?> disabled>
                              <label class="form-check-label opacity-100" for="vinculo">Possuo vínculo com a instituição</label>
                            </div>
                          </div>

                        </div>

                        <div class="tit_section">
                          <h3>Endereço</h3>
                        </div>

                        <div class="row grid gx-3">

                          <?php if ($user_brasileiro == 1) { ?>

                            <div class="col-md-6 col-lg-4 col-xl-2">
                              <div class="mb-3">
                                <label class="form-label">CEP</label>
                                <input type="text" class="form-control text-uppercase cep" id="cep" value="<?= $user_cep ?>" disabled>
                              </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-3">
                              <div class="mb-3">
                                <label class="form-label">Logradouro</label>
                                <input type="text" class="form-control text-uppercase" id="endereco" value="<?= $user_rua ?>" disabled>
                              </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-1">
                              <div class="mb-3">
                                <label class="form-label">Número</label>
                                <input type="text" class="form-control text-uppercase" value="<?= $user_numero ?>" disabled>
                              </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-2">
                              <div class="mb-3">
                                <label class="form-label">Bairro</label>
                                <input type="text" class="form-control text-uppercase" id="bairro" value="<?= $user_bairro ?>" disabled>
                              </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-2">
                              <div class="mb-3">
                                <label class="form-label">Município</label>
                                <input type="text" class="form-control text-uppercase" id="cidade" value="<?= $user_municipio ?>" disabled>
                              </div>
                            </div>

                            <div class="col-md-6 col-lg-4 col-xl-2">
                              <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <input type="text" class="form-control text-uppercase" id="estado" value="<?= $user_estado ?>" disabled>
                              </div>
                            </div>

                          <?php } else { ?>

                            <div class="col-12">
                              <label class="form-label">Endereço</label>
                              <textarea class="form-control text-uppercase" id="myTextarea" rows="3" disabled><?= $user_endereco ?></textarea>
                              <p class="label_info text-end mt-1">Caracteres restantes: <span id="charCount">200</span></p>
                            </div>
                            <script type="text/javascript">
                              const textarea = document.getElementById('myTextarea');
                              const charCountSpan = document.getElementById('charCount');
                              const maxCharLimit = 200;
                              // Função para atualizar o contador de caracteres
                              function updateCharCount() {
                                const currentCharCount = textarea.value.length;
                                const charsRemaining = maxCharLimit - currentCharCount;
                                charCountSpan.textContent = charsRemaining;
                                // Verifica se o número de caracteres excede o limite e ajusta o valor se necessário
                                if (charsRemaining < 0) {
                                  textarea.value = textarea.value.substring(0, maxCharLimit);
                                  charCountSpan.textContent = 0;
                                }
                              }
                              // Evento de escuta para o input no textarea
                              textarea.addEventListener('input', updateCharCount);
                              // Chama a função inicialmente para exibir o contador de caracteres atualizado
                              updateCharCount();
                            </script>

                          <?php } ?>

                        </div>

                        <div class="tit_section">
                          <h3>Formação</h3>
                        </div>

                        <div class="row grid gx-3">

                          <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="mb-3">
                              <?php try {
                                $sql = $conn->query("SELECT * FROM escolaridades");
                                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                              } catch (PDOException $e) {
                                // echo "Erro: " . $e->getMessage();
                                echo "Erro ao tentar recuperar o perfil";
                              } ?>
                              <label class="form-label">Nível de Escolaridade</label>
                              <select class="form-select text-uppercase" disabled>
                                <option selected value="<?= $esc_id ?>"><?= $esc_escolaridade ?></option>
                                <?php foreach ($result as $res) : ?>
                                  <option value="<?= $res['esc_id'] ?>"><?= $res['esc_escolaridade'] ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-6 col-lg-8 col-xl-9">
                            <div class="mb-3">
                              <label class="form-label">Instituição de Ensino</label>
                              <input type="text" class="form-control text-uppercase" value="<?= $user_instituicao_ensino ?>" disabled>
                            </div>
                          </div>

                          <div class="col-12">
                            <div class="mb-3">
                              <label class="form-label">Link do Lattes</label>
                              <input type="text" class="form-control text-lowercase" value="<?= $user_lattes ?>" disabled>
                            </div>
                          </div>

                          <?php if (!empty($arq_id)) { ?>
                            <div class="col-12">
                              <div class="mb-3">
                                <label class="form-label">Currículo Vitae</label>
                                <div class="result_file py-2" style="margin-top: 1px;">
                                  <div class="result_file_name"><a href="uploads/usuarios/<?= $arq_user_id . '/' . $arq_arquivo ?>" target="_blank"><?= $arq_arquivo ?></a></div>
                                </div>
                              </div>
                            </div>
                          <?php } ?>

                        </div>

                        <div class="row grid gx-3">

                          <div class="col-12 mt-4">
                            <div class="mb-2">
                              <div class="form-check">
                                <input class="form-check-input  opacity-100" type="checkbox" id="vinculo_atividade" value="1" <?php echo ($user_vinculo_atividade == 1) ? 'checked' : ''; ?> disabled>
                                <label class="form-check-label  opacity-100" for="vinculo_atividade">Aceito vínculo em atividade extensionista</label>
                              </div>
                            </div>
                          </div>

                          <div class="col-12">
                            <div class="mb-3">
                              <div class="form-check">
                                <input class="form-check-input  opacity-100" type="checkbox" id="receber_notificacoes" value="1" <?php echo ($user_receber_notificacoes == 1) ? 'checked' : ''; ?> disabled>
                                <label class="form-check-label  opacity-100" for="receber_notificacoes">Aceito receber notificações de novos produtos</label>
                              </div>
                            </div>
                          </div>

                        </div>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ALTERAR SENHA -->
        <div class="tab-pane" id="proj-tab" role="tabpanel">
          <div class="card">

            <div class="card-header">
              <div class="row align-items-center">
                <div class="col-sm-12">
                  <h5 class="card-title mb-0">Projetos</h5>
                </div>
              </div>
            </div>

            <div class="card-body p-0">

              <table id="tab_proj_user" class="table dt-responsive nowrap align-middle" style="width:100%">
                <thead>
                  <tr>
                    <th>Código</th>
                    <th>Título da Proposta</th>
                    <th>Categoria</th>
                    <th>Data Cadastro</th>
                    <th>Etapa</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  try {
                    $sql = "SELECT * FROM propostas
                            INNER JOIN propostas_categorias ON propostas_categorias.propc_id = propostas.prop_tipo
                            LEFT JOIN propostas_status ON propostas_status.prop_sta_prop_id = propostas.prop_id
                            LEFT JOIN status_propostas ON status_propostas.stprop_id = propostas_status.prop_sta_status
                            INNER JOIN usuarios ON usuarios.user_id = propostas.prop_user_id
                            WHERE user_user_id = '$user_id'";
                    $stmt = $conn->query($sql);
                    while ($prop = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      extract($prop);

                      // CONFIGURAÇÃO DA CATEGORIA
                      if ($prop_tipo == 1) {
                        $prop_cat_color = 'bg_info_verde';
                      }
                      if ($prop_tipo == 2) {
                        $prop_cat_color = 'bg_info_laranja';
                      }
                      if ($prop_tipo == 3) {
                        $prop_cat_color = 'bg_info_azul';
                      }
                      if ($prop_tipo == 4) {
                        $prop_cat_color = 'bg_info_roxo';
                      }
                      if ($prop_tipo == 5) {
                        $prop_cat_color = 'bg_info_rosa';
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

                  ?>

                      <tr role="button" onclick="location.href='atividades.php?i=<?= base64_encode($prop_id) ?>'">
                        <th><?= $prop_codigo ?></th>
                        <td width="50%"><?= $prop_titulo ?></td>
                        <td><span class="badge text-truncate <?= $prop_cat_color ?>" style="max-width: 80%;" title="<?= $propc_categoria ?>"><?= $propc_categoria ?></span></td>
                        <td nowrap="nowrap"><span class="hide_data"><?= date('Ymd i:H', strtotime($prop_data_cad)) ?></span>
                          <nobr><?= date('d/m/Y - H:i', strtotime($prop_data_cad)) ?></nobr>
                        </td>
                        <td>
                          <div class="dados_proposta_etapas_tabela">
                            <div class="dados_proposta_etapas_tabela_status">

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
                        </td>
                        <td><span class="badge <?= $prop_status_color ?>"><?= $stprop_status ?></span></td>
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
    </div>
  </div>
</div>




<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>
<!-- VALIDA CPF -->
<script src="assets/js/valida_cpf.js"></script>
<!-- VALIDA SENHA -->
<script src="assets/js/valid-password.js"></script>
<!-- PASSWORD ADDON INIT -->
<script src="assets/js/pages/password-addon.init.js"></script>
<!-- CEP -->
<!-- <script src="assets/js/1120_jquery.min.js"></script> -->
<script src="assets/js/CEP.js"></script>
<!-- SELECT2 FORM -->
<script src="includes/select/select2.js"></script>


<!-- FUNÇÃO PARA SALVAR A ABA SELECIONADA NO LOCALSTORAGE -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
    var activeTab = $(e.target).attr('href');
    localStorage.setItem('activeTab', activeTab);
  });

  $(document).ready(function() {
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
      $('#myTab a[href="' + activeTab + '"]').tab('show');
    }
  });
</script>