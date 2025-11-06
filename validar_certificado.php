<?php include 'includes/header_val_cert.php'; ?>

<?php
$search = '';
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['search'])) {
    $tipo_search   = $_POST['tipo_search'];
    $search        = $_POST['search'];
    $search_codigo = $_POST['search_codigo'];


    if ($tipo_search == 1) {
      $sql = 'SELECT prop_titulo, insc_id, insc_categoria, inscc_categoria, cert_data_inicio, insc_nome FROM propostas
            INNER JOIN propostas_categorias ON propostas_categorias.propc_id = propostas.prop_tipo
            INNER JOIN usuarios ON usuarios.user_id = propostas.prop_user_id
            INNER JOIN inscricoes ON inscricoes.insc_prop_id = propostas.prop_id
            INNER JOIN inscricoes_categorias ON inscricoes_categorias.inscc_id = inscricoes.insc_categoria
            INNER JOIN certificado ON certificado.cert_prop_id COLLATE SQL_Latin1_General_CP1_CI_AI = inscricoes.insc_prop_id AND certificado.cert_categoria = inscricoes.insc_categoria
            WHERE insc_email LIKE :search';

      $stmt = $conn->prepare($sql);
      // $stmt->execute(['search' => '%' . $search . '%']);
      $stmt->execute(['search' => $search]);
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $sql = 'SELECT prop_titulo, insc_id, insc_categoria, inscc_categoria, cert_data_inicio, insc_nome, cert_carga FROM propostas
            INNER JOIN propostas_categorias ON propostas_categorias.propc_id = propostas.prop_tipo
            INNER JOIN usuarios ON usuarios.user_id = propostas.prop_user_id
            INNER JOIN inscricoes ON inscricoes.insc_prop_id = propostas.prop_id
            INNER JOIN inscricoes_categorias ON inscricoes_categorias.inscc_id = inscricoes.insc_categoria
            INNER JOIN certificado ON certificado.cert_prop_id COLLATE SQL_Latin1_General_CP1_CI_AI = inscricoes.insc_prop_id AND certificado.cert_categoria = inscricoes.insc_categoria
            WHERE insc_codigo LIKE :search';

      $stmt = $conn->prepare($sql);
      // $stmt->execute(['search' => '%' . $search_codigo . '%']);
      $stmt->execute(['search' => $search_codigo]);
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  }
}
?>

<div class="profile-foreground position-relative mx-n4">
  <div class="profile-wid-bg position-relative">


    <div class="container z-1 position-relative">
      <div class="cont_form">
        <div class="row d-flex justify-content-center">
          <div class="col-lg-10 text-center">
            <h1>Certificação de Participação em Projetos de Extensão Bahiana</h1>
            <p>Obtenha e valide seus certificados de participação em projetos de extensão Bahiana. Facilite o reconhecimento de suas contribuições acadêmicas com nosso serviço de emissão e verificação de certificados.</p>
          </div>
        </div>


        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="needs-validation d-none d-lg-block">

          <div class="input-group">
            <select aria-label="First name" class="form-select form-input-valida" id="acao_certificado" name="tipo_search" aria-label="Default select example" style="max-width: 30%" required>
              <option value="1">Imprimir Certificado</option>
              <option value="2">Validar Certificado</option>
            </select>

            <input type="email" aria-label="Last name" class="form-control form-input-valida" id="input_email" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Seu e-mail" required>

            <input type="text" aria-label="Last name" class="form-control form-input-valida" id="input_codigo" name="search_codigo" placeholder="Código de validação" style="display: none;">

            <button type="submit" class="btn botao botao_verde">
              <div id="bt_imprimir">Imprimir certificado</div>
              <div id="bt_validar" style="display: none;">Validar certificado</div>
            </button>

          </div>

          <script>
            const acao_certificado = document.getElementById("acao_certificado");
            const input_email = document.getElementById("input_email");
            const input_codigo = document.getElementById("input_codigo");
            const bt_imprimir = document.getElementById("bt_imprimir");
            const bt_validar = document.getElementById("bt_validar");

            acao_certificado.addEventListener("change", function() {
              if (acao_certificado.value === "2") {
                bt_imprimir.style.display = "none";
                bt_validar.style.display = "block";
                //
                input_codigo.style.display = "block";
                document.getElementById("input_codigo").required = true;

                input_email.style.display = "none";
                document.getElementById("input_email").required = false;
              } else {
                bt_imprimir.style.display = "block";
                bt_validar.style.display = "none";
                //
                input_codigo.style.display = "none";
                document.getElementById("input_codigo").required = false;

                input_email.style.display = "block";
                document.getElementById("input_email").required = true;
              }
            });
          </script>

        </form>


        <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="needs-validation d-block d-lg-none">

          <select class="form-select form-input-valida w-100 mb-2" id="acao_certificado_sm" name="tipo_search" required>
            <option value="1">Imprimir Certificado</option>
            <option value="2">Validar Certificado</option>
          </select>

          <input type="email" class="form-control form-input-valida w-100 mb-2" id="input_email_sm" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Seu e-mail" required>

          <input type="text" class="form-control form-input-valida w-100 mb-2" id="input_codigo_sm" name="search_codigo" placeholder="Código de validação" style="display: none;">

          <button type="submit" class="btn botao botao_verde w-100">
            <div id="bt_imprimir_sm">Imprimir certificado</div>
            <div id="bt_validar_sm" style="display: none;">Validar certificado</div>
          </button>

          <script>
            const acao_certificado_sm = document.getElementById("acao_certificado_sm");
            const input_email_sm = document.getElementById("input_email_sm");
            const input_codigo_sm = document.getElementById("input_codigo_sm");
            const bt_imprimir_sm = document.getElementById("bt_imprimir_sm");
            const bt_validar_sm = document.getElementById("bt_validar_sm");

            acao_certificado_sm.addEventListener("change", function() {
              if (acao_certificado_sm.value === "2") {
                bt_imprimir_sm.style.display = "none";
                bt_validar_sm.style.display = "block";
                //
                input_codigo_sm.style.display = "block";
                document.getElementById("input_codigo_sm").required = true;

                input_email_sm.style.display = "none";
                document.getElementById("input_email_sm").required = false;
              } else {
                bt_imprimir_sm.style.display = "block";
                bt_validar_sm.style.display = "none";
                //
                input_codigo_sm.style.display = "none";
                document.getElementById("input_codigo_sm").required = false;

                input_email_sm.style.display = "block";
                document.getElementById("input_email_sm").required = true;
              }
            });
          </script>

        </form>

      </div>
    </div>

  </div>


  <div class="container result_valida">

    <div class="campo_card">

      <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>

        <?php if ($results): ?>

          <?php if ($tipo_search == 1) { ?>
            <h3>Certificados de Participação</h3>
          <?php } ?>

          <?php foreach ($results as $row): ?>

            <?php if ($tipo_search == 1) { ?>

              <a href="certificados/inscricoes.php?cat=<?= base64_encode($row['insc_categoria']) ?>&insc_id=<?= base64_encode($row['insc_id']) ?>" target="_blank">
                <div class="card card-animate" role="button">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12 mb-2 text-start">
                        <p>Título: <strong><?= htmlspecialchars($row['prop_titulo']) ?></strong></p>
                      </div>
                      <div class="col-lg-5 mb-2 mb-lg-0 text-start">
                        <p>Participante: <strong><?= htmlspecialchars($row['insc_nome']) ?></strong></p>
                      </div>
                      <div class="col-lg-4 mb-2 mb-lg-0 text-start">
                        <p>Categoria: <strong class="text-uppercase"><?= htmlspecialchars($row['inscc_categoria']) ?></strong></p>
                      </div>
                      <div class="col-lg-3 text-lg-end text-start">
                        <p>Data: <strong><?= date('d/m/Y', strtotime($row['cert_data_inicio'])) ?></strong></p>
                      </div>
                    </div>
                  </div>
                </div>
              </a>

            <?php } else { ?>

              <div class="card card_result_valida mt-5">
                <div class="card-body">
                  <div class="row">

                    <h5>Este certificado é válido</h5>

                    <div class="col-12 mb-2 text-start">
                      <p>Título: <strong><?= htmlspecialchars($row['prop_titulo']) ?></strong></p>
                    </div>
                    <div class="col-lg-5 mb-2 mb-lg-0 text-start">
                      <p>Participante: <strong><?= htmlspecialchars($row['insc_nome']) ?></strong></p>
                    </div>

                    <div class="col-lg-3 mb-2 mb-lg-0 text-lg-end text-start">
                      <p>Data: <strong><?= date('d/m/Y', strtotime($row['cert_data_inicio'])) ?></strong></p>
                    </div>

                    <div class="col-lg-4 text-lg-end text-start">
                      <p>Carga Horária: <strong class="text-uppercase"><?= htmlspecialchars($row['cert_carga']) ?> Horas</strong></p>
                    </div>
                  </div>
                </div>
              </div>

            <?php } ?>

          <?php endforeach; ?>
        <?php else: ?>
          <!-- <p>Nenhum resultado encontrado.</p> -->

          <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show text-start mt-4" role="alert">
            <i class="ri-error-warning-line label-icon"></i>Nenhum resultado encontrado!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>

        <?php endif; ?>
      <?php endif; ?>

    </div>
  </div>
</div>

<!-- VLIBRAS -->
<div vw class="enabled">
  <div vw-access-button class="active"></div>
  <div vw-plugin-wrapper>
    <div class="vw-plugin-top-wrapper"></div>
  </div>
</div>

<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>
  new window.VLibras.Widget('https://vlibras.gov.br/app');
</script>


<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>