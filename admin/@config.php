<?php include 'includes/header.php'; ?>

<?php
// ACESSO RESTRITO A PÁGINA CASO NÃO TENHA NÍVEL DE ACESSO
if (!empty($global_admin_id) && $global_admin_perfil != 1) {
  header("Location: sair.php");
}
?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Configurações</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="javascript: void(0);">Configurações</a></li>
          <li class="breadcrumb-item active">Configurações</li>
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
            <h5 class="card-title mb-0">Cadastro de Propostas</h5>
          </div>
        </div>
      </div>
      <div class="card-body p-4 px-3 px-md-4">
        <form class="needs-validation" method="POST" action="controller/controller_configuracoes.php?funcao=CadConfig" novalidate>

          <p>Desative a categoria quando desejar bloquear seu cadastro de proposta extensionista</p>

          <div class="row grid gx-3">

            <div class="col-12">
              <div class="mb-4">

                <?php try {
                  $stmt = $conn->query("SELECT * FROM propostas_categorias ORDER BY propc_id");
                  $result_itens  = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                  // echo "Erro: " . $e->getMessage();
                  echo "Erro ao tentar recuperar materiais";
                } ?>

                <?php foreach ($result_itens as $result_iten) : ?>
                  <div class="row_conf_cat d-flex justify-content-between align-items-center">
                    <p><?= $result_iten['propc_categoria'] ?></p>
                    <div class="form-check form-switch m-0 ms-3">
                      <input class="form-check-input" type="checkbox" role="switch" name="propc[<?= $result_iten['propc_id'] ?>]" id="<?= $result_iten['propc_id'] ?>" value="1" <?php echo ($result_iten['propc_status'] == 1) ? 'checked' : ''; ?>>
                      <!-- <label class="form-check-label" for="SwitchCheck1">Switch Default</label> -->
                    </div>
                  </div>
                <?php endforeach; ?>

              </div>
            </div>

          </div>

          <div class="tit_section">
            <h3>Mensagem de Aviso</h3>
          </div>

          <div class="row grid gx-3">

            <?php if ($result_iten['propc_msg']) { ?>

              <p class="mb-0">Como o usuário irá visualizar a mensagem que será exibida.</p>

              <div class="container-fluid px-4 py-3">
                <div class="row info_card mb-0 justify-content-center">
                  <div class="col-md-12 col-lg-4 text-center">

                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chat-right-text mb-2" viewBox="0 0 16 16">
                      <path d="M2 1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h9.586a2 2 0 0 1 1.414.586l2 2V2a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z" />
                      <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6m0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                    </svg>

                    <h3>Aviso Importante!</h3>
                    <p><?= $result_iten['propc_msg'] ?></p>
                  </div>
                </div>
              </div>

            <?php } ?>

            <div class="col-12">
              <label class="form-label mb-0">Mensagem</label>
              <div class="label_info mb-2 mt-0">Caso queira, cadastre uma mensagem de aviso</div>
              <textarea class="form-control" name="prop_conf_msg" id="myTextarea" rows="3"><?= str_replace('<br />', '', $result_iten['propc_msg']) ?></textarea>
              <p class="label_info text-end mt-1">Caracteres restantes: <span id="charCount">500</span></p>
              <script type="text/javascript">
                const textarea = document.getElementById('myTextarea');
                const charCountSpan = document.getElementById('charCount');
                const maxCharLimit = 500;
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
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-4">
                <!-- <p class="label_asterisco me-auto my-0"><span>*</span> Campo obrigatório</p> -->
                <button type="submit" class="btn botao botao_verde waves-effect">Salvar</button>
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