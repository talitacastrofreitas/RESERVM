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

          // CONFIGURA A DATA POR EXTENSO
          $cert_data_hoje = date('d') . ' de ' . traduzirMes(date('m')) . ' de ' . date('Y');
          function traduzirMes($mes)
          {
            $meses = array(
              '01' => 'janeiro',
              '02' => 'fevereiro',
              '03' => 'março',
              '04' => 'abril',
              '05' => 'maio',
              '06' => 'junho',
              '07' => 'julho',
              '08' => 'agosto',
              '09' => 'setembro',
              '10' => 'outubro',
              '11' => 'novembro',
              '12' => 'dezembro'
            );
            return $meses[$mes];
          }

          ?>

          <div class="card-header px-3">
            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" id="myTab" role="tablist">
              <?php foreach ($menuItems as $item) : ?>
                <li class="nav-item link_nav_perfil text-center">
                  <a href="certificado.php?c=<?= base64_encode($item['inscc_id']); ?>&i=<?= $_GET['i'] ?>" class="nav-link text-body <?= isItemAtivo($item['inscc_id']) ? 'active' : ''; ?>"><span class="<?php echo ($item['cert_status'] == 1) ? 'info_cat_cred' : ''; ?>" title="Certificado liberado"></span><span class="<?php echo ($item['has_certificate'] == 1) ? 'info_cat' : ''; ?>" title="Certificado criado"></span> <?= $item['inscc_categoria']; ?></a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <?php
          $cat = base64_decode($_GET['c']);
          try {
            $sql_cert = "SELECT * FROM certificado
                        LEFT JOIN certificado_arquivo ON certificado_arquivo.cert_arq_id = certificado.cert_id
                        WHERE cert_categoria = :cert_categoria AND cert_prop_id = :cert_prop_id";
            $stmt = $conn->prepare($sql_cert);
            $stmt->bindParam(':cert_prop_id', $prop_id);
            $stmt->bindParam(':cert_categoria', $cat);
            $stmt->execute();
            $cert = $stmt->fetch(PDO::FETCH_ASSOC);
            $cert_id                = isset($cert['cert_id']) ? $cert['cert_id'] : NULL;
            $cert_prop_id           = isset($cert['cert_prop_id']) ? $cert['cert_prop_id'] : NULL;
            $cert_prop_tipo         = isset($cert['cert_prop_tipo']) ? $cert['cert_prop_tipo'] : NULL;
            $cert_prop_codigo       = isset($cert['cert_prop_codigo']) ? $cert['cert_prop_codigo'] : NULL;
            $cert_categoria         = isset($cert['cert_categoria']) ? $cert['cert_categoria'] : NULL;
            $cert_titulo            = isset($cert['cert_titulo']) ? $cert['cert_titulo'] : NULL;
            $cert_nome              = isset($cert['cert_nome']) ? $cert['cert_nome'] : NULL;
            $cert_nome_comissao     = isset($cert['cert_nome_comissao']) ? $cert['cert_nome_comissao'] : NULL;
            $cert_texto             = isset($cert['cert_texto']) ? $cert['cert_texto'] : NULL;
            $cert_titulo_trabalho   = isset($cert['cert_titulo_trabalho']) ? $cert['cert_titulo_trabalho'] : NULL;
            $cert_area_tematica     = isset($cert['cert_area_tematica']) ? $cert['cert_area_tematica'] : NULL;
            $cert_autores           = isset($cert['cert_autores']) ? $cert['cert_autores'] : NULL;
            $cert_coautores         = isset($cert['cert_coautores']) ? $cert['cert_coautores'] : NULL;
            $cert_modalidade        = isset($cert['cert_modalidade']) ? $cert['cert_modalidade'] : NULL;
            $cert_conteudo_programa = isset($cert['cert_conteudo_programa']) ? $cert['cert_conteudo_programa'] : NULL;
            $cert_data_inicio       = isset($cert['cert_data_inicio']) ? $cert['cert_data_inicio'] : NULL;
            $cert_data_fim          = isset($cert['cert_data_fim']) ? $cert['cert_data_fim'] : NULL;
            $cert_carga             = isset($cert['cert_carga']) ? $cert['cert_carga'] : NULL;
            $cert_status            = isset($cert['cert_status']) ? $cert['cert_status'] : NULL;
            //ARQUIVO
            $cert_arq_id            = isset($cert['cert_arq_id']) ? $cert['cert_arq_id'] : NULL;
            $cert_arq_prop_id       = isset($cert['cert_arq_prop_id']) ? $cert['cert_arq_prop_id'] : NULL;
            $cert_arq_categoria     = isset($cert['cert_arq_categoria']) ? $cert['cert_arq_categoria'] : NULL;
            $cert_arq_arquivo       = isset($cert['cert_arq_arquivo']) ? $cert['cert_arq_arquivo'] : NULL;

            // SE DATA PREVISTA PARA INÍCIO NÃO FOR CADASTRADA NO CERTIFICADO, USA A DATA PREVISTA PARA INÍCIO CADASTRADA NA PROPOSTA
            if (empty($cert_data_inicio)) {
              $data_inicio = $prop_data_inicio;
            } else {
              $data_inicio = $cert_data_inicio;
            }

            // SE DATA PREVISTA PARA FINALIZAÇÃO NÃO FOR CADASTRADA NO CERTIFICADO, USA A DATA PREVISTA PARA FINALIZAÇÃO CADASTRADA NA PROPOSTA
            if (empty($cert_data_fim)) {
              $data_fim = $prop_data_fim;
            } else {
              $data_fim = $cert_data_fim;
            }

            // SE CARGA HORÁRIA NÃO FOR CADASTRADA NO CERTIFICADO, USA A CARGA HORÁRIA CADASTRADA NA PROPOSTA
            if (empty($cert_carga)) {
              $carga_hora = $prop_carga_hora;
            } else {
              $carga_hora = $cert_carga;
            }
          } catch (PDOException $e) {
            echo "Erro: " . $e->getMessage();
          }
          ?>

          <div class="modal-body">

            <!-- <div class="card-header border-0 mb-md-n3 mb-auto p-md-4 p-3"> -->
            <div class="card-header">
              <div class="row align-items-center">
                <div class="col-lg-5 text-lg-start text-center mb-3 mb-lg-0">

                  <?php
                  $category = base64_decode($_GET['c']);
                  $stmt = $conn->prepare("SELECT inscc_id, inscc_categoria FROM inscricoes_categorias WHERE inscc_id = $category");
                  $stmt->execute();
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  ?>
                    <h5 class="card-title m-0 ps-2">Certificado <?= $row['inscc_categoria'] ?></h5>
                  <?php } ?>

                </div>

                <?php if ($cert_id) { ?>

                  <div class="col-lg-7 d-block d-sm-flex align-items-center justify-content-lg-end justify-content-center">
                    <nav class="navbar navbar_analise p-0">
                      <div class="nav nav-pills animation-nav profile-nav gap-2 gap-md-3 flex-grow-1">
                        <a href="controller/controller_certificado.php?funcao=exc_cert&prop_codigo=<?= $prop_codigo ?>&cert_arq_arquivo=<?= $cert_arq_arquivo ?>&cert_id=<?= $cert_id ?>" class="btn botao botao_vermelho_transparente waves-effect delCert">Excluir Certificado</a>
                        <button onClick="javascript:window.open('../relatorios/certificado_pdf.php?c=<?= $_GET['c'] ?>&cert=<?= base64_encode($cert_id) ?>', '_blank');" class="btn botao botao_azul_escuro waves-effect">Visualizar Modelo do Certificado</button>
                      </div>
                    </nav>
                  </div>

                <?php } else { ?>

                  <div class="col-lg-7 d-block d-sm-flex align-items-center justify-content-lg-end justify-content-center">
                    <nav class="navbar navbar_analise p-0">
                      <div class="nav nav-pills animation-nav profile-nav gap-2 gap-md-3 flex-grow-1">
                        <a class="btn botao botao_disabled waves-effect" disabled>Excluir Certificado</a>
                        <a class="btn botao botao_disabled waves-effect" disabled>Visualizar Modelo do Certificado</a>
                      </div>
                    </nav>
                  </div>

                <?php } ?>

              </div>
            </div>

            <?php if (base64_decode($_GET['c']) == 1) { ?>

              <div class="card-body p-md-4 p-3">

                <form class="needs-validation" method="POST" action="controller/controller_certificado.php" enctype="multipart/form-data" autocomplete="off" novalidate="">

                  <input type="hidden" class="form-control" name="cert_id" value="<?= base64_encode($cert_id) ?>">
                  <input type="hidden" class="form-control" name="cert_prop_id" value="<?= $_GET['i'] ?>" required>
                  <input type="hidden" class="form-control" name="cert_prop_codigo" value="<?= $prop_codigo ?>" required>
                  <input type="hidden" class="form-control" name="cert_categoria" value="<?= $_GET['c'] ?>" required>

                  <div class="mb-3">
                    <label class="form-label">Dados do Certificado <span>*</span></label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_texto">
                        <?php if (isset($cert_texto)) {
                          echo $cert_texto;
                        } else { ?>
                        <p style="font-size: 20px;">Certificamos que {nome} participou do {nome_evento}, promovido pela
                        Escola Bahiana de Medicina e Saúde Pública, realizado no dia {data_inicio_evento} a {data_termino_evento}, com carga horária total de {carga_horaria} horas.</p>	
                        <br>
                        <p style="font-size: 20px; float: right;">Salvador, <?= $cert_data_hoje ?></p>
                        <?php  } ?>
                    </textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="row">
                    <div class="col-xl-12 col-xxl-4">
                      <p class="mt-4">Você pode utilizar as seguintes tags para carregar informações do certificado.</p>
                      <div class="card_info mb-4">
                        <p><strong>{nome}</strong> Nome do participante.</p>
                        <!-- <p><strong>{nome_social}</strong> Nome Social do participante.</p> -->
                        <p><strong>{tipo_proposta}</strong> Tipo da proposta (Cursos, Programas, Parcerias, etc).</p>
                        <p><strong>{nome_evento}</strong> Nome do evento.</p>
                        <p><strong>{categoria_certificado}</strong> Categoria do Certificado (Participantes, Organizadores, etc).</p>
                        <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
                          <p><strong>{insc_tipo}</strong> Tipo de apresentação ou de atividade extra.</p>
                        <?php } ?>
                        <?php if (base64_decode($_GET['c']) == 4) { ?>
                          <p><strong>{insc_titulo}</strong> Título do trabalho apresentado.</p>
                          <p><strong>{insc_nome_coautor}</strong> Nome do coauto do trabalho.</p>
                        <?php } ?>
                        <p><strong>{data_inicio_evento}</strong> Data de início do evento.</p>
                        <p><strong>{data_termino_evento}</strong> Data de término do evento.</p>
                        <p><strong>{carga_horaria}</strong> Carga horária.</p>
                      </div>
                    </div>
                  </div>

                  <div class="row">

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para início <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_inicio" value="<?= date("Y-m-d", strtotime($data_inicio)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para finalização <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_fim" value="<?= date("Y-m-d", strtotime($data_fim)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Carga horária <span>*</span></label>
                        <input type="text" class="form-control" name="cert_carga" value="<?= $carga_hora ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                  </div>

                  <div class="tit_section">
                    <h3>Conteúdo Programático</h3>
                  </div>

                  <div class="mb-5">
                    <label class="form-label">Dados do Conteúdo Programático</label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_conteudo_programa"><?= $cert_conteudo_programa ?></textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="tit_section">
                    <h3>Imagem de Fundo do Certificado</h3>
                  </div>

                  <p>Para que o upload da imagem seja realizado, alguns requisitos precisam ser atendidos.</p>

                  <div class="card_info_modal mb-4">
                    <p>1. As dimensões mais adequadas para a imagem é  <strong>(1170 x 830)</strong>.</p>
                    <p>2. A imagem precisar ter o formato <strong>.jpg</strong> ou <strong>.jpeg</strong>.</p>
                    <p>3. A imagem precisar ter o tamanho máximo de  <strong>2MB</strong>.</p>
                  </div>

                  <?php try {
                    $cat = base64_decode($_GET['c']);
                    $sql = "SELECT * FROM certificado_arquivo WHERE cert_arq_prop_id = :cert_arq_prop_id AND cert_arq_categoria = :cert_arq_categoria";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':cert_arq_prop_id', $prop_id);
                    $stmt->bindParam(':cert_arq_categoria', $cat);
                    $stmt->execute();
                    $cert_arq = $stmt->fetch(PDO::FETCH_ASSOC);
                    $cert_arq_id          = isset($cert_arq['cert_arq_id']) ? $cert_arq['cert_arq_id'] : null;
                    $cert_arq_prop_id     = isset($cert_arq['cert_arq_prop_id']) ? $cert_arq['cert_arq_prop_id'] : null;
                    $cert_arq_categoria   = isset($cert_arq['cert_arq_categoria']) ? $cert_arq['cert_arq_categoria'] : null;
                    $cert_arq_arquivo     = isset($cert_arq['cert_arq_arquivo']) ? $cert_arq['cert_arq_arquivo'] : null;
                    $cert_arq_user_id     = isset($cert_arq['cert_arq_user_id']) ? $cert_arq['cert_arq_user_id'] : null;
                    $cert_arq_data_cad    = isset($cert_arq['cert_arq_data_cad']) ? $cert_arq['cert_arq_data_cad'] : null;
                  } catch (PDOException $e) {
                    echo "Erro: " . $e->getMessage();
                  }
                  if (empty($cert_arq)) { ?>

                    <div class="col-12">
                      <div class="mb-3">
                        <label class="form-label">Imagem de fundo <span>*</span></label>
                        <input type="file" class="form-control input_arquivo" name="arquivo" id="arquivos" onchange="imgCert(this)" accept=".jpg, .JPG, .jpeg, .JPEG" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <script>
                      // ARQUIVO NÃO PODE ULTRAPASSAR 10MB
                      document.addEventListener("DOMContentLoaded", function() {
                        const inputFile = document.getElementById("arquivos");

                        inputFile.addEventListener("change", function() {
                          const arquivo = inputFile.files[0];

                          if (arquivo && arquivo.size > 2 * 1024 * 1024) {
                            Swal.fire({
                              icon: 'error',
                              title: 'Erro encontrado!',
                              text: 'O arquivo deve ter menos de 2MB.',
                            })
                            inputFile.value = ""; // Limpar o valor do campo de arquivo
                          }
                        });
                      });

                      // FORMATOS DE ARQUIVOS PERMITIDOS
                      document.getElementById("arquivos").addEventListener("change", function() {
                        var inputElement = this;
                        var allowedExtensions = ["jpg", "JPG", "jpeg", "JPEG"];
                        var fileExtension = inputElement.value.split(".").pop().toLowerCase();
                        if (allowedExtensions.indexOf(fileExtension) === -1) {
                          Swal.fire({
                            icon: 'error',
                            title: 'Erro encontrado!',
                            text: 'Formato de arquivo inválido.',
                            //text: 'Formato de arquivo inválido. Apenas arquivos JPG, JPEG ou PNG são permitidos.',
                          })
                          inputElement.value = ""; // Limpa a seleção de arquivo inválido
                        }
                      });
                    </script>

                  <?php } else { ?>

                    <label class="form-label mb-0">Imagem de fundo</label>
                    <div class="result_file">
                      <div class="result_file_name"><a href="../uploads/certificado/<?= $prop_codigo ?>/<?= $cert_arq_arquivo ?>" target="_blank"><?= $cert_arq_arquivo ?></a></div>
                      <span class="item_bt_row">
                        <a href="controller/controller_certificado.php?funcao=exc_img_cert&cert_arq_id=<?= $cert_arq_id ?>&cert_arq_arquivo=<?= $cert_arq_arquivo ?>&prop_codigo=<?= $prop_codigo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                      </span>
                    </div>

                  <?php } ?>

                  <div class="col-12 mt-4">
                    <div>
                      <div class="form-check">
                        <input class="form-check-input insc_credenciamento" type="checkbox" id="checkCredenciado" name="cert_status" value="1" <?php echo ($cert_status == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="checkCredenciado">Liberar acesso ao certificado</label>
                      </div>
                    </div>
                  </div>


                  <div class="col-lg-12 mt-5">
                    <div class="hstack gap-3 align-items-center justify-content-end">
                      <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                      <?php if (!$cert_id) { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="CadCertificado">Cadastrar</button>
                      <?php } else { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="EditCertificado">Salvar</button>
                      <?php } ?>
                    </div>
                  </div>
                </form>

              </div>

            <?php } ?>

            <?php if (base64_decode($_GET['c']) == 2) { ?>

              <div class="card-body p-md-4 p-3">

                <form class="needs-validation" method="POST" action="controller/controller_certificado.php" enctype="multipart/form-data" autocomplete="off" novalidate="">

                  <input type="hidden" class="form-control" name="cert_id" value="<?= base64_encode($cert_id) ?>">
                  <input type="hidden" class="form-control" name="cert_prop_id" value="<?= $_GET['i'] ?>" required>
                  <input type="hidden" class="form-control" name="cert_prop_codigo" value="<?= $prop_codigo ?>" required>
                  <input type="hidden" class="form-control" name="cert_categoria" value="<?= $_GET['c'] ?>" required>

                  <div class="mb-3">
                    <label class="form-label">Dados do Certificado <span>*</span></label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_texto">
                        <?php if (isset($cert_texto)) {
                          echo $cert_texto;
                        } else { ?>
                        <p style="font-size: 20px;">Certificamos que {nome} participou do {nome_evento}, promovido pela
                        Escola Bahiana de Medicina e Saúde Pública, realizado no dia {data_inicio_evento} a {data_termino_evento}, com carga horária total de {carga_horaria} horas.</p>	
                        <br>
                        <p style="font-size: 20px; float: right;">Salvador, <?= $cert_data_hoje ?></p>
                        <?php  } ?>
                    </textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="row">
                    <div class="col-xl-12 col-xxl-4">
                      <p class="mt-4">Você pode utilizar as seguintes tags para carregar informações do certificado.</p>
                      <div class="card_info mb-4">
                        <p><strong>{nome}</strong> Nome do participante.</p>
                        <!-- <p><strong>{nome_social}</strong> Nome Social do participante.</p> -->
                        <p><strong>{tipo_proposta}</strong> Tipo da proposta (Cursos, Programas, Parcerias, etc).</p>
                        <p><strong>{nome_evento}</strong> Nome do evento.</p>
                        <p><strong>{categoria_certificado}</strong> Categoria do Certificado (Participantes, Organizadores, etc).</p>
                        <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
                          <p><strong>{insc_tipo}</strong> Tipo de apresentação ou de atividade extra.</p>
                        <?php } ?>
                        <?php if (base64_decode($_GET['c']) == 4) { ?>
                          <p><strong>{insc_titulo}</strong> Título do trabalho apresentado.</p>
                          <p><strong>{insc_nome_coautor}</strong> Nome do coauto do trabalho.</p>
                        <?php } ?>
                        <p><strong>{data_inicio_evento}</strong> Data de início do evento.</p>
                        <p><strong>{data_termino_evento}</strong> Data de término do evento.</p>
                        <p><strong>{carga_horaria}</strong> Carga horária.</p>
                      </div>
                    </div>
                  </div>

                  <div class="row">

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para início <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_inicio" value="<?= date("Y-m-d", strtotime($data_inicio)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para finalização <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_fim" value="<?= date("Y-m-d", strtotime($data_fim)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Carga horária <span>*</span></label>
                        <input type="text" class="form-control" name="cert_carga" value="<?= $carga_hora ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                  </div>

                  <div class="tit_section">
                    <h3>Conteúdo Programático</h3>
                  </div>

                  <div class="mb-5">
                    <label class="form-label">Dados do Conteúdo Programático</label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_conteudo_programa"><?= $cert_conteudo_programa ?></textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="tit_section">
                    <h3>Imagem de Fundo do Certificado</h3>
                  </div>

                  <p>Para que o upload da imagem seja realizado, alguns requisitos precisam ser atendidos.</p>

                  <div class="card_info_modal mb-4">
                    <p>1. As dimensões mais adequadas para a imagem é  <strong>(1170 x 830)</strong>.</p>
                    <p>2. A imagem precisar ter o formato <strong>.jpg</strong> ou <strong>.jpeg</strong>.</p>
                    <p>3. A imagem precisar ter o tamanho máximo de  <strong>2MB</strong>.</p>
                  </div>

                  <?php try {
                    $cat = base64_decode($_GET['c']);
                    $sql = "SELECT * FROM certificado_arquivo WHERE cert_arq_prop_id = :cert_arq_prop_id AND cert_arq_categoria = :cert_arq_categoria";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':cert_arq_prop_id', $prop_id);
                    $stmt->bindParam(':cert_arq_categoria', $cat);
                    $stmt->execute();
                    $cert_arq = $stmt->fetch(PDO::FETCH_ASSOC);
                    $cert_arq_id          = isset($cert_arq['cert_arq_id']) ? $cert_arq['cert_arq_id'] : null;
                    $cert_arq_prop_id     = isset($cert_arq['cert_arq_prop_id']) ? $cert_arq['cert_arq_prop_id'] : null;
                    $cert_arq_categoria   = isset($cert_arq['cert_arq_categoria']) ? $cert_arq['cert_arq_categoria'] : null;
                    $cert_arq_arquivo     = isset($cert_arq['cert_arq_arquivo']) ? $cert_arq['cert_arq_arquivo'] : null;
                    $cert_arq_user_id     = isset($cert_arq['cert_arq_user_id']) ? $cert_arq['cert_arq_user_id'] : null;
                    $cert_arq_data_cad    = isset($cert_arq['cert_arq_data_cad']) ? $cert_arq['cert_arq_data_cad'] : null;
                  } catch (PDOException $e) {
                    echo "Erro: " . $e->getMessage();
                  }
                  if (empty($cert_arq)) { ?>

                    <div class="col-12">
                      <div class="mb-3">
                        <label class="form-label">Imagem de fundo <span>*</span></label>
                        <input type="file" class="form-control input_arquivo" name="arquivo" id="arquivos" onchange="imgCert(this)" accept=".jpg, .JPG, .jpeg, .JPEG" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <script>
                      // ARQUIVO NÃO PODE ULTRAPASSAR 10MB
                      document.addEventListener("DOMContentLoaded", function() {
                        const inputFile = document.getElementById("arquivos");

                        inputFile.addEventListener("change", function() {
                          const arquivo = inputFile.files[0];

                          if (arquivo && arquivo.size > 2 * 1024 * 1024) {
                            Swal.fire({
                              icon: 'error',
                              title: 'Erro encontrado!',
                              text: 'O arquivo deve ter menos de 2MB.',
                            })
                            inputFile.value = ""; // Limpar o valor do campo de arquivo
                          }
                        });
                      });

                      // FORMATOS DE ARQUIVOS PERMITIDOS
                      document.getElementById("arquivos").addEventListener("change", function() {
                        var inputElement = this;
                        var allowedExtensions = ["jpg", "JPG", "jpeg", "JPEG"];
                        var fileExtension = inputElement.value.split(".").pop().toLowerCase();
                        if (allowedExtensions.indexOf(fileExtension) === -1) {
                          Swal.fire({
                            icon: 'error',
                            title: 'Erro encontrado!',
                            text: 'Formato de arquivo inválido.',
                            //text: 'Formato de arquivo inválido. Apenas arquivos JPG, JPEG ou PNG são permitidos.',
                          })
                          inputElement.value = ""; // Limpa a seleção de arquivo inválido
                        }
                      });
                    </script>

                  <?php } else { ?>

                    <label class="form-label mb-0">Imagem de fundo</label>
                    <div class="result_file">
                      <div class="result_file_name"><a href="../uploads/certificado/<?= $prop_codigo ?>/<?= $cert_arq_arquivo ?>" target="_blank"><?= $cert_arq_arquivo ?></a></div>
                      <span class="item_bt_row">
                        <a href="controller/controller_certificado.php?funcao=exc_img_cert&cert_arq_id=<?= $cert_arq_id ?>&cert_arq_arquivo=<?= $cert_arq_arquivo ?>&prop_codigo=<?= $prop_codigo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                      </span>
                    </div>

                  <?php } ?>

                  <div class="col-12 mt-4">
                    <div>
                      <div class="form-check">
                        <input class="form-check-input insc_credenciamento" type="checkbox" id="checkCredenciado" name="cert_status" value="1" <?php echo ($cert_status == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="checkCredenciado">Liberar acesso ao certificado</label>
                      </div>
                    </div>
                  </div>


                  <div class="col-lg-12 mt-5">
                    <div class="hstack gap-3 align-items-center justify-content-end">
                      <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                      <?php if (!$cert_id) { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="CadCertificado">Cadastrar</button>
                      <?php } else { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="EditCertificado">Salvar</button>
                      <?php } ?>
                    </div>
                  </div>
                </form>

              </div>

            <?php } ?>

            <?php if (base64_decode($_GET['c']) == 3) { ?>

              <div class="card-body p-md-4 p-3">

                <form class="needs-validation" method="POST" action="controller/controller_certificado.php" enctype="multipart/form-data" autocomplete="off" novalidate="">

                  <input type="hidden" class="form-control" name="cert_id" value="<?= base64_encode($cert_id) ?>">
                  <input type="hidden" class="form-control" name="cert_prop_id" value="<?= $_GET['i'] ?>" required>
                  <input type="hidden" class="form-control" name="cert_prop_codigo" value="<?= $prop_codigo ?>" required>
                  <input type="hidden" class="form-control" name="cert_categoria" value="<?= $_GET['c'] ?>" required>

                  <div class="mb-3">
                    <label class="form-label">Dados do Certificado <span>*</span></label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_texto">
                        <?php if (isset($cert_texto)) {
                          echo $cert_texto;
                        } else { ?>
                        <p style="font-size: 20px;">Certificamos que {nome} participou do {nome_evento}, promovido pela
                        Escola Bahiana de Medicina e Saúde Pública, realizado no dia {data_inicio_evento} a {data_termino_evento}, com carga horária total de {carga_horaria} horas.</p>	
                        <br>
                        <p style="font-size: 20px; float: right;">Salvador, <?= $cert_data_hoje ?></p>
                        <?php  } ?>
                    </textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="row">
                    <div class="col-xl-12 col-xxl-4">
                      <p class="mt-4">Você pode utilizar as seguintes tags para carregar informações do certificado.</p>
                      <div class="card_info mb-4">
                        <p><strong>{nome}</strong> Nome do participante.</p>
                        <!-- <p><strong>{nome_social}</strong> Nome Social do participante.</p> -->
                        <p><strong>{tipo_proposta}</strong> Tipo da proposta (Cursos, Programas, Parcerias, etc).</p>
                        <p><strong>{nome_evento}</strong> Nome do evento.</p>
                        <p><strong>{categoria_certificado}</strong> Categoria do Certificado (Participantes, Organizadores, etc).</p>
                        <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
                          <p><strong>{insc_tipo}</strong> Tipo de apresentação ou de atividade extra.</p>
                        <?php } ?>
                        <?php if (base64_decode($_GET['c']) == 3) { ?>
                          <p><strong>{insc_titulo}</strong> Título do trabalho apresentado.</p>
                        <?php } ?>
                        <?php if (base64_decode($_GET['c']) == 4) { ?>
                          <p><strong>{insc_titulo}</strong> Título do trabalho apresentado.</p>
                          <p><strong>{insc_nome_coautor}</strong> Nome do coauto do trabalho.</p>
                        <?php } ?>
                        <p><strong>{data_inicio_evento}</strong> Data de início do evento.</p>
                        <p><strong>{data_termino_evento}</strong> Data de término do evento.</p>
                        <p><strong>{carga_horaria}</strong> Carga horária.</p>
                      </div>
                    </div>
                  </div>

                  <div class="row">

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para início <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_inicio" value="<?= date("Y-m-d", strtotime($data_inicio)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para finalização <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_fim" value="<?= date("Y-m-d", strtotime($data_fim)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Carga horária <span>*</span></label>
                        <input type="text" class="form-control" name="cert_carga" value="<?= $carga_hora ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                  </div>

                  <div class="tit_section">
                    <h3>Conteúdo Programático</h3>
                  </div>

                  <div class="mb-5">
                    <label class="form-label">Dados do Conteúdo Programático</label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_conteudo_programa"><?= $cert_conteudo_programa ?></textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="tit_section">
                    <h3>Imagem de Fundo do Certificado</h3>
                  </div>

                  <p>Para que o upload da imagem seja realizado, alguns requisitos precisam ser atendidos.</p>

                  <div class="card_info_modal mb-4">
                    <p>1. As dimensões mais adequadas para a imagem é  <strong>(1170 x 830)</strong>.</p>
                    <p>2. A imagem precisar ter o formato <strong>.jpg</strong> ou <strong>.jpeg</strong>.</p>
                    <p>3. A imagem precisar ter o tamanho máximo de  <strong>2MB</strong>.</p>
                  </div>

                  <?php try {
                    $cat = base64_decode($_GET['c']);
                    $sql = "SELECT * FROM certificado_arquivo WHERE cert_arq_prop_id = :cert_arq_prop_id AND cert_arq_categoria = :cert_arq_categoria";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':cert_arq_prop_id', $prop_id);
                    $stmt->bindParam(':cert_arq_categoria', $cat);
                    $stmt->execute();
                    $cert_arq = $stmt->fetch(PDO::FETCH_ASSOC);
                    $cert_arq_id          = isset($cert_arq['cert_arq_id']) ? $cert_arq['cert_arq_id'] : null;
                    $cert_arq_prop_id     = isset($cert_arq['cert_arq_prop_id']) ? $cert_arq['cert_arq_prop_id'] : null;
                    $cert_arq_categoria   = isset($cert_arq['cert_arq_categoria']) ? $cert_arq['cert_arq_categoria'] : null;
                    $cert_arq_arquivo     = isset($cert_arq['cert_arq_arquivo']) ? $cert_arq['cert_arq_arquivo'] : null;
                    $cert_arq_user_id     = isset($cert_arq['cert_arq_user_id']) ? $cert_arq['cert_arq_user_id'] : null;
                    $cert_arq_data_cad    = isset($cert_arq['cert_arq_data_cad']) ? $cert_arq['cert_arq_data_cad'] : null;
                  } catch (PDOException $e) {
                    echo "Erro: " . $e->getMessage();
                  }
                  if (empty($cert_arq)) { ?>

                    <div class="col-12">
                      <div class="mb-3">
                        <label class="form-label">Imagem de fundo <span>*</span></label>
                        <input type="file" class="form-control input_arquivo" name="arquivo" id="arquivos" onchange="imgCert(this)" accept=".jpg, .JPG, .jpeg, .JPEG" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <script>
                      // ARQUIVO NÃO PODE ULTRAPASSAR 10MB
                      document.addEventListener("DOMContentLoaded", function() {
                        const inputFile = document.getElementById("arquivos");

                        inputFile.addEventListener("change", function() {
                          const arquivo = inputFile.files[0];

                          if (arquivo && arquivo.size > 2 * 1024 * 1024) {
                            Swal.fire({
                              icon: 'error',
                              title: 'Erro encontrado!',
                              text: 'O arquivo deve ter menos de 2MB.',
                            })
                            inputFile.value = ""; // Limpar o valor do campo de arquivo
                          }
                        });
                      });

                      // FORMATOS DE ARQUIVOS PERMITIDOS
                      document.getElementById("arquivos").addEventListener("change", function() {
                        var inputElement = this;
                        var allowedExtensions = ["jpg", "JPG", "jpeg", "JPEG"];
                        var fileExtension = inputElement.value.split(".").pop().toLowerCase();
                        if (allowedExtensions.indexOf(fileExtension) === -1) {
                          Swal.fire({
                            icon: 'error',
                            title: 'Erro encontrado!',
                            text: 'Formato de arquivo inválido.',
                            //text: 'Formato de arquivo inválido. Apenas arquivos JPG, JPEG ou PNG são permitidos.',
                          })
                          inputElement.value = ""; // Limpa a seleção de arquivo inválido
                        }
                      });
                    </script>

                  <?php } else { ?>

                    <label class="form-label mb-0">Imagem de fundo</label>
                    <div class="result_file">
                      <div class="result_file_name"><a href="../uploads/certificado/<?= $prop_codigo ?>/<?= $cert_arq_arquivo ?>" target="_blank"><?= $cert_arq_arquivo ?></a></div>
                      <span class="item_bt_row">
                        <a href="controller/controller_certificado.php?funcao=exc_img_cert&cert_arq_id=<?= $cert_arq_id ?>&cert_arq_arquivo=<?= $cert_arq_arquivo ?>&prop_codigo=<?= $prop_codigo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                      </span>
                    </div>

                  <?php } ?>

                  <div class="col-12 mt-4">
                    <div>
                      <div class="form-check">
                        <input class="form-check-input insc_credenciamento" type="checkbox" id="checkCredenciado" name="cert_status" value="1" <?php echo ($cert_status == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="checkCredenciado">Liberar acesso ao certificado</label>
                      </div>
                    </div>
                  </div>


                  <div class="col-lg-12 mt-5">
                    <div class="hstack gap-3 align-items-center justify-content-end">
                      <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                      <?php if (!$cert_id) { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="CadCertificado">Cadastrar</button>
                      <?php } else { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="EditCertificado">Salvar</button>
                      <?php } ?>
                    </div>
                  </div>
                </form>

              </div>

            <?php } ?>

            <?php if (base64_decode($_GET['c']) == 4) { ?>

              <div class="card-body p-md-4 p-3">

                <form class="needs-validation" method="POST" action="controller/controller_certificado.php" enctype="multipart/form-data" autocomplete="off" novalidate="">

                  <input type="hidden" class="form-control" name="cert_id" value="<?= base64_encode($cert_id) ?>">
                  <input type="hidden" class="form-control" name="cert_prop_id" value="<?= $_GET['i'] ?>" required>
                  <input type="hidden" class="form-control" name="cert_prop_codigo" value="<?= $prop_codigo ?>" required>
                  <input type="hidden" class="form-control" name="cert_categoria" value="<?= $_GET['c'] ?>" required>

                  <div class="mb-3">
                    <label class="form-label">Dados do Certificado <span>*</span></label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_texto">
                        <?php if (isset($cert_texto)) {
                          echo $cert_texto;
                        } else { ?>
                        <p style="font-size: 20px;">Certificamos que {nome} participou do {nome_evento}, promovido pela
                        Escola Bahiana de Medicina e Saúde Pública, realizado no dia {data_inicio_evento} a {data_termino_evento}, com carga horária total de {carga_horaria} horas.</p>	
                        <br>
                        <p style="font-size: 20px; float: right;">Salvador, <?= $cert_data_hoje ?></p>
                        <?php  } ?>
                    </textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="row">
                    <div class="col-xl-12 col-xxl-4">
                      <p class="mt-4">Você pode utilizar as seguintes tags para carregar informações do certificado.</p>
                      <div class="card_info mb-4">
                        <p><strong>{nome}</strong> Nome do participante.</p>
                        <!-- <p><strong>{nome_social}</strong> Nome Social do participante.</p> -->
                        <p><strong>{tipo_proposta}</strong> Tipo da proposta (Cursos, Programas, Parcerias, etc).</p>
                        <p><strong>{nome_evento}</strong> Nome do evento.</p>
                        <p><strong>{categoria_certificado}</strong> Categoria do Certificado (Participantes, Organizadores, etc).</p>
                        <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
                          <p><strong>{insc_tipo}</strong> Tipo de apresentação ou de atividade extra.</p>
                        <?php } ?>
                        <?php if (base64_decode($_GET['c']) == 4) { ?>
                          <p><strong>{insc_titulo}</strong> Título do trabalho apresentado.</p>
                          <p><strong>{insc_nome_coautor}</strong> Nome do coauto do trabalho.</p>
                        <?php } ?>
                        <p><strong>{data_inicio_evento}</strong> Data de início do evento.</p>
                        <p><strong>{data_termino_evento}</strong> Data de término do evento.</p>
                        <p><strong>{carga_horaria}</strong> Carga horária.</p>
                      </div>
                    </div>
                  </div>

                  <div class="row">

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para início <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_inicio" value="<?= date("Y-m-d", strtotime($data_inicio)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para finalização <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_fim" value="<?= date("Y-m-d", strtotime($data_fim)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Carga horária <span>*</span></label>
                        <input type="text" class="form-control" name="cert_carga" value="<?= $carga_hora ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                  </div>

                  <div class="tit_section">
                    <h3>Conteúdo Programático</h3>
                  </div>

                  <div class="mb-5">
                    <label class="form-label">Dados do Conteúdo Programático</label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_conteudo_programa"><?= $cert_conteudo_programa ?></textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="tit_section">
                    <h3>Imagem de Fundo do Certificado</h3>
                  </div>

                  <p>Para que o upload da imagem seja realizado, alguns requisitos precisam ser atendidos.</p>

                  <div class="card_info_modal mb-4">
                    <p>1. As dimensões mais adequadas para a imagem é  <strong>(1170 x 830)</strong>.</p>
                    <p>2. A imagem precisar ter o formato <strong>.jpg</strong> ou <strong>.jpeg</strong>.</p>
                    <p>3. A imagem precisar ter o tamanho máximo de  <strong>2MB</strong>.</p>
                  </div>

                  <?php try {
                    $cat = base64_decode($_GET['c']);
                    $sql = "SELECT * FROM certificado_arquivo WHERE cert_arq_prop_id = :cert_arq_prop_id AND cert_arq_categoria = :cert_arq_categoria";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':cert_arq_prop_id', $prop_id);
                    $stmt->bindParam(':cert_arq_categoria', $cat);
                    $stmt->execute();
                    $cert_arq = $stmt->fetch(PDO::FETCH_ASSOC);
                    $cert_arq_id          = isset($cert_arq['cert_arq_id']) ? $cert_arq['cert_arq_id'] : null;
                    $cert_arq_prop_id     = isset($cert_arq['cert_arq_prop_id']) ? $cert_arq['cert_arq_prop_id'] : null;
                    $cert_arq_categoria   = isset($cert_arq['cert_arq_categoria']) ? $cert_arq['cert_arq_categoria'] : null;
                    $cert_arq_arquivo     = isset($cert_arq['cert_arq_arquivo']) ? $cert_arq['cert_arq_arquivo'] : null;
                    $cert_arq_user_id     = isset($cert_arq['cert_arq_user_id']) ? $cert_arq['cert_arq_user_id'] : null;
                    $cert_arq_data_cad    = isset($cert_arq['cert_arq_data_cad']) ? $cert_arq['cert_arq_data_cad'] : null;
                  } catch (PDOException $e) {
                    echo "Erro: " . $e->getMessage();
                  }
                  if (empty($cert_arq)) { ?>

                    <div class="col-12">
                      <div class="mb-3">
                        <label class="form-label">Imagem de fundo <span>*</span></label>
                        <input type="file" class="form-control input_arquivo" name="arquivo" id="arquivos" onchange="imgCert(this)" accept=".jpg, .JPG, .jpeg, .JPEG" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <script>
                      // ARQUIVO NÃO PODE ULTRAPASSAR 10MB
                      document.addEventListener("DOMContentLoaded", function() {
                        const inputFile = document.getElementById("arquivos");

                        inputFile.addEventListener("change", function() {
                          const arquivo = inputFile.files[0];

                          if (arquivo && arquivo.size > 2 * 1024 * 1024) {
                            Swal.fire({
                              icon: 'error',
                              title: 'Erro encontrado!',
                              text: 'O arquivo deve ter menos de 2MB.',
                            })
                            inputFile.value = ""; // Limpar o valor do campo de arquivo
                          }
                        });
                      });

                      // FORMATOS DE ARQUIVOS PERMITIDOS
                      document.getElementById("arquivos").addEventListener("change", function() {
                        var inputElement = this;
                        var allowedExtensions = ["jpg", "JPG", "jpeg", "JPEG"];
                        var fileExtension = inputElement.value.split(".").pop().toLowerCase();
                        if (allowedExtensions.indexOf(fileExtension) === -1) {
                          Swal.fire({
                            icon: 'error',
                            title: 'Erro encontrado!',
                            text: 'Formato de arquivo inválido.',
                            //text: 'Formato de arquivo inválido. Apenas arquivos JPG, JPEG ou PNG são permitidos.',
                          })
                          inputElement.value = ""; // Limpa a seleção de arquivo inválido
                        }
                      });
                    </script>

                  <?php } else { ?>

                    <label class="form-label mb-0">Imagem de fundo</label>
                    <div class="result_file">
                      <div class="result_file_name"><a href="../uploads/certificado/<?= $prop_codigo ?>/<?= $cert_arq_arquivo ?>" target="_blank"><?= $cert_arq_arquivo ?></a></div>
                      <span class="item_bt_row">
                        <a href="controller/controller_certificado.php?funcao=exc_img_cert&cert_arq_id=<?= $cert_arq_id ?>&cert_arq_arquivo=<?= $cert_arq_arquivo ?>&prop_codigo=<?= $prop_codigo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                      </span>
                    </div>

                  <?php } ?>

                  <div class="col-12 mt-4">
                    <div>
                      <div class="form-check">
                        <input class="form-check-input insc_credenciamento" type="checkbox" id="checkCredenciado" name="cert_status" value="1" <?php echo ($cert_status == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="checkCredenciado">Liberar acesso ao certificado</label>
                      </div>
                    </div>
                  </div>


                  <div class="col-lg-12 mt-5">
                    <div class="hstack gap-3 align-items-center justify-content-end">
                      <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                      <?php if (!$cert_id) { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="CadCertificado">Cadastrar</button>
                      <?php } else { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="EditCertificado">Salvar</button>
                      <?php } ?>
                    </div>
                  </div>
                </form>

              </div>

            <?php } ?>

            <?php if (base64_decode($_GET['c']) == 5) { ?>

              <div class="card-body p-md-4 p-3">

                <form class="needs-validation" method="POST" action="controller/controller_certificado.php" enctype="multipart/form-data" autocomplete="off" novalidate="">

                  <input type="hidden" class="form-control" name="cert_id" value="<?= base64_encode($cert_id) ?>">
                  <input type="hidden" class="form-control" name="cert_prop_id" value="<?= $_GET['i'] ?>" required>
                  <input type="hidden" class="form-control" name="cert_prop_codigo" value="<?= $prop_codigo ?>" required>
                  <input type="hidden" class="form-control" name="cert_categoria" value="<?= $_GET['c'] ?>" required>

                  <div class="mb-3">
                    <label class="form-label">Dados do Certificado <span>*</span></label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_texto">
                        <?php if (isset($cert_texto)) {
                          echo $cert_texto;
                        } else { ?>
                        <p style="font-size: 20px;">Certificamos que {nome} participou do {nome_evento}, promovido pela
                        Escola Bahiana de Medicina e Saúde Pública, realizado no dia {data_inicio_evento} a {data_termino_evento}, com carga horária total de {carga_horaria} horas.</p>	
                        <br>
                        <p style="font-size: 20px; float: right;">Salvador, <?= $cert_data_hoje ?></p>
                        <?php  } ?>
                    </textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="row">
                    <div class="col-xl-12 col-xxl-4">
                      <p class="mt-4">Você pode utilizar as seguintes tags para carregar informações do certificado.</p>
                      <div class="card_info mb-4">
                        <p><strong>{nome}</strong> Nome do participante.</p>
                        <!-- <p><strong>{nome_social}</strong> Nome Social do participante.</p> -->
                        <p><strong>{tipo_proposta}</strong> Tipo da proposta (Cursos, Programas, Parcerias, etc).</p>
                        <p><strong>{nome_evento}</strong> Nome do evento.</p>
                        <p><strong>{categoria_certificado}</strong> Categoria do Certificado (Participantes, Organizadores, etc).</p>
                        <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
                          <p><strong>{insc_tipo}</strong> Tipo de apresentação ou de atividade extra.</p>
                        <?php } ?>
                        <?php if (base64_decode($_GET['c']) == 4) { ?>
                          <p><strong>{insc_titulo}</strong> Título do trabalho apresentado.</p>
                          <p><strong>{insc_nome_coautor}</strong> Nome do coauto do trabalho.</p>
                        <?php } ?>
                        <p><strong>{data_inicio_evento}</strong> Data de início do evento.</p>
                        <p><strong>{data_termino_evento}</strong> Data de término do evento.</p>
                        <p><strong>{carga_horaria}</strong> Carga horária.</p>
                      </div>
                    </div>
                  </div>

                  <div class="row">

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para início <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_inicio" value="<?= date("Y-m-d", strtotime($data_inicio)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para finalização <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_fim" value="<?= date("Y-m-d", strtotime($data_fim)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Carga horária <span>*</span></label>
                        <input type="text" class="form-control" name="cert_carga" value="<?= $carga_hora ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                  </div>

                  <div class="tit_section">
                    <h3>Conteúdo Programático</h3>
                  </div>

                  <div class="mb-5">
                    <label class="form-label">Dados do Conteúdo Programático</label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_conteudo_programa"><?= $cert_conteudo_programa ?></textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="tit_section">
                    <h3>Imagem de Fundo do Certificado</h3>
                  </div>

                  <p>Para que o upload da imagem seja realizado, alguns requisitos precisam ser atendidos.</p>

                  <div class="card_info_modal mb-4">
                    <p>1. As dimensões mais adequadas para a imagem é  <strong>(1170 x 830)</strong>.</p>
                    <p>2. A imagem precisar ter o formato <strong>.jpg</strong> ou <strong>.jpeg</strong>.</p>
                    <p>3. A imagem precisar ter o tamanho máximo de  <strong>2MB</strong>.</p>
                  </div>

                  <?php try {
                    $cat = base64_decode($_GET['c']);
                    $sql = "SELECT * FROM certificado_arquivo WHERE cert_arq_prop_id = :cert_arq_prop_id AND cert_arq_categoria = :cert_arq_categoria";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':cert_arq_prop_id', $prop_id);
                    $stmt->bindParam(':cert_arq_categoria', $cat);
                    $stmt->execute();
                    $cert_arq = $stmt->fetch(PDO::FETCH_ASSOC);
                    $cert_arq_id          = isset($cert_arq['cert_arq_id']) ? $cert_arq['cert_arq_id'] : null;
                    $cert_arq_prop_id     = isset($cert_arq['cert_arq_prop_id']) ? $cert_arq['cert_arq_prop_id'] : null;
                    $cert_arq_categoria   = isset($cert_arq['cert_arq_categoria']) ? $cert_arq['cert_arq_categoria'] : null;
                    $cert_arq_arquivo     = isset($cert_arq['cert_arq_arquivo']) ? $cert_arq['cert_arq_arquivo'] : null;
                    $cert_arq_user_id     = isset($cert_arq['cert_arq_user_id']) ? $cert_arq['cert_arq_user_id'] : null;
                    $cert_arq_data_cad    = isset($cert_arq['cert_arq_data_cad']) ? $cert_arq['cert_arq_data_cad'] : null;
                  } catch (PDOException $e) {
                    echo "Erro: " . $e->getMessage();
                  }
                  if (empty($cert_arq)) { ?>

                    <div class="col-12">
                      <div class="mb-3">
                        <label class="form-label">Imagem de fundo <span>*</span></label>
                        <input type="file" class="form-control input_arquivo" name="arquivo" id="arquivos" onchange="imgCert(this)" accept=".jpg, .JPG, .jpeg, .JPEG" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <script>
                      // ARQUIVO NÃO PODE ULTRAPASSAR 10MB
                      document.addEventListener("DOMContentLoaded", function() {
                        const inputFile = document.getElementById("arquivos");

                        inputFile.addEventListener("change", function() {
                          const arquivo = inputFile.files[0];

                          if (arquivo && arquivo.size > 2 * 1024 * 1024) {
                            Swal.fire({
                              icon: 'error',
                              title: 'Erro encontrado!',
                              text: 'O arquivo deve ter menos de 2MB.',
                            })
                            inputFile.value = ""; // Limpar o valor do campo de arquivo
                          }
                        });
                      });

                      // FORMATOS DE ARQUIVOS PERMITIDOS
                      document.getElementById("arquivos").addEventListener("change", function() {
                        var inputElement = this;
                        var allowedExtensions = ["jpg", "JPG", "jpeg", "JPEG"];
                        var fileExtension = inputElement.value.split(".").pop().toLowerCase();
                        if (allowedExtensions.indexOf(fileExtension) === -1) {
                          Swal.fire({
                            icon: 'error',
                            title: 'Erro encontrado!',
                            text: 'Formato de arquivo inválido.',
                            //text: 'Formato de arquivo inválido. Apenas arquivos JPG, JPEG ou PNG são permitidos.',
                          })
                          inputElement.value = ""; // Limpa a seleção de arquivo inválido
                        }
                      });
                    </script>

                  <?php } else { ?>

                    <label class="form-label mb-0">Imagem de fundo</label>
                    <div class="result_file">
                      <div class="result_file_name"><a href="../uploads/certificado/<?= $prop_codigo ?>/<?= $cert_arq_arquivo ?>" target="_blank"><?= $cert_arq_arquivo ?></a></div>
                      <span class="item_bt_row">
                        <a href="controller/controller_certificado.php?funcao=exc_img_cert&cert_arq_id=<?= $cert_arq_id ?>&cert_arq_arquivo=<?= $cert_arq_arquivo ?>&prop_codigo=<?= $prop_codigo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                      </span>
                    </div>

                  <?php } ?>

                  <div class="col-12 mt-4">
                    <div>
                      <div class="form-check">
                        <input class="form-check-input insc_credenciamento" type="checkbox" id="checkCredenciado" name="cert_status" value="1" <?php echo ($cert_status == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="checkCredenciado">Liberar acesso ao certificado</label>
                      </div>
                    </div>
                  </div>


                  <div class="col-lg-12 mt-5">
                    <div class="hstack gap-3 align-items-center justify-content-end">
                      <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                      <?php if (!$cert_id) { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="CadCertificado">Cadastrar</button>
                      <?php } else { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="EditCertificado">Salvar</button>
                      <?php } ?>
                    </div>
                  </div>
                </form>

              </div>

            <?php } ?>

            <?php if (base64_decode($_GET['c']) == 6) { ?>

              <div class="card-body p-md-4 p-3">

                <form class="needs-validation" method="POST" action="controller/controller_certificado.php" enctype="multipart/form-data" autocomplete="off" novalidate="">

                  <input type="hidden" class="form-control" name="cert_id" value="<?= base64_encode($cert_id) ?>">
                  <input type="hidden" class="form-control" name="cert_prop_id" value="<?= $_GET['i'] ?>" required>
                  <input type="hidden" class="form-control" name="cert_prop_codigo" value="<?= $prop_codigo ?>" required>
                  <input type="hidden" class="form-control" name="cert_categoria" value="<?= $_GET['c'] ?>" required>

                  <div class="mb-3">
                    <label class="form-label">Dados do Certificado <span>*</span></label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_texto">
                        <?php if (isset($cert_texto)) {
                          echo $cert_texto;
                        } else { ?>
                        <p style="font-size: 20px;">Certificamos que {nome} participou do {nome_evento}, promovido pela
                        Escola Bahiana de Medicina e Saúde Pública, realizado no dia {data_inicio_evento} a {data_termino_evento}, com carga horária total de {carga_horaria} horas.</p>	
                        <br>
                        <p style="font-size: 20px; float: right;">Salvador, <?= $cert_data_hoje ?></p>
                        <?php  } ?>
                    </textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="row">
                    <div class="col-xl-12 col-xxl-4">
                      <p class="mt-4">Você pode utilizar as seguintes tags para carregar informações do certificado.</p>
                      <div class="card_info mb-4">
                        <p><strong>{nome}</strong> Nome do participante.</p>
                        <!-- <p><strong>{nome_social}</strong> Nome Social do participante.</p> -->
                        <p><strong>{tipo_proposta}</strong> Tipo da proposta (Cursos, Programas, Parcerias, etc).</p>
                        <p><strong>{nome_evento}</strong> Nome do evento.</p>
                        <p><strong>{categoria_certificado}</strong> Categoria do Certificado (Participantes, Organizadores, etc).</p>
                        <?php if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) { ?>
                          <p><strong>{insc_tipo}</strong> Tipo de apresentação ou de atividade extra.</p>
                        <?php } ?>
                        <?php if (base64_decode($_GET['c']) == 4) { ?>
                          <p><strong>{insc_titulo}</strong> Título do trabalho apresentado.</p>
                          <p><strong>{insc_nome_coautor}</strong> Nome do coauto do trabalho.</p>
                        <?php } ?>
                        <p><strong>{data_inicio_evento}</strong> Data de início do evento.</p>
                        <p><strong>{data_termino_evento}</strong> Data de término do evento.</p>
                        <p><strong>{carga_horaria}</strong> Carga horária.</p>
                      </div>
                    </div>
                  </div>

                  <div class="row">

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para início <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_inicio" value="<?= date("Y-m-d", strtotime($data_inicio)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Data prevista para finalização <span>*</span></label>
                        <input type="date" class="form-control" name="cert_data_fim" value="<?= date("Y-m-d", strtotime($data_fim)) ?>" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label class="form-label">Carga horária <span>*</span></label>
                        <input type="text" class="form-control" name="cert_carga" value="<?= $carga_hora ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" maxlength="10" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                  </div>

                  <div class="tit_section">
                    <h3>Conteúdo Programático</h3>
                  </div>

                  <div class="mb-5">
                    <label class="form-label">Dados do Conteúdo Programático</label>
                    <textarea id="cert_texto" class="corpo_certificado" name="cert_conteudo_programa"><?= $cert_conteudo_programa ?></textarea>
                    <script>
                      tinymce.init({
                        selector: 'textarea',
                        height: 300,
                        menubar: false,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                      });
                    </script>
                  </div>

                  <div class="tit_section">
                    <h3>Imagem de Fundo do Certificado</h3>
                  </div>

                  <p>Para que o upload da imagem seja realizado, alguns requisitos precisam ser atendidos.</p>

                  <div class="card_info_modal mb-4">
                    <p>1. As dimensões mais adequadas para a imagem é  <strong>(1170 x 830)</strong>.</p>
                    <p>2. A imagem precisar ter o formato <strong>.jpg</strong> ou <strong>.jpeg</strong>.</p>
                    <p>3. A imagem precisar ter o tamanho máximo de  <strong>2MB</strong>.</p>
                  </div>

                  <?php try {
                    $cat = base64_decode($_GET['c']);
                    $sql = "SELECT * FROM certificado_arquivo WHERE cert_arq_prop_id = :cert_arq_prop_id AND cert_arq_categoria = :cert_arq_categoria";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':cert_arq_prop_id', $prop_id);
                    $stmt->bindParam(':cert_arq_categoria', $cat);
                    $stmt->execute();
                    $cert_arq = $stmt->fetch(PDO::FETCH_ASSOC);
                    $cert_arq_id          = isset($cert_arq['cert_arq_id']) ? $cert_arq['cert_arq_id'] : null;
                    $cert_arq_prop_id     = isset($cert_arq['cert_arq_prop_id']) ? $cert_arq['cert_arq_prop_id'] : null;
                    $cert_arq_categoria   = isset($cert_arq['cert_arq_categoria']) ? $cert_arq['cert_arq_categoria'] : null;
                    $cert_arq_arquivo     = isset($cert_arq['cert_arq_arquivo']) ? $cert_arq['cert_arq_arquivo'] : null;
                    $cert_arq_user_id     = isset($cert_arq['cert_arq_user_id']) ? $cert_arq['cert_arq_user_id'] : null;
                    $cert_arq_data_cad    = isset($cert_arq['cert_arq_data_cad']) ? $cert_arq['cert_arq_data_cad'] : null;
                  } catch (PDOException $e) {
                    echo "Erro: " . $e->getMessage();
                  }
                  if (empty($cert_arq)) { ?>

                    <div class="col-12">
                      <div class="mb-3">
                        <label class="form-label">Imagem de fundo <span>*</span></label>
                        <input type="file" class="form-control input_arquivo" name="arquivo" id="arquivos" onchange="imgCert(this)" accept=".jpg, .JPG, .jpeg, .JPEG" required>
                        <div class="invalid-feedback">Este campo é obrigatório</div>
                      </div>
                    </div>

                    <script>
                      // ARQUIVO NÃO PODE ULTRAPASSAR 10MB
                      document.addEventListener("DOMContentLoaded", function() {
                        const inputFile = document.getElementById("arquivos");

                        inputFile.addEventListener("change", function() {
                          const arquivo = inputFile.files[0];

                          if (arquivo && arquivo.size > 2 * 1024 * 1024) {
                            Swal.fire({
                              icon: 'error',
                              title: 'Erro encontrado!',
                              text: 'O arquivo deve ter menos de 2MB.',
                            })
                            inputFile.value = ""; // Limpar o valor do campo de arquivo
                          }
                        });
                      });

                      // FORMATOS DE ARQUIVOS PERMITIDOS
                      document.getElementById("arquivos").addEventListener("change", function() {
                        var inputElement = this;
                        var allowedExtensions = ["jpg", "JPG", "jpeg", "JPEG"];
                        var fileExtension = inputElement.value.split(".").pop().toLowerCase();
                        if (allowedExtensions.indexOf(fileExtension) === -1) {
                          Swal.fire({
                            icon: 'error',
                            title: 'Erro encontrado!',
                            text: 'Formato de arquivo inválido.',
                            //text: 'Formato de arquivo inválido. Apenas arquivos JPG, JPEG ou PNG são permitidos.',
                          })
                          inputElement.value = ""; // Limpa a seleção de arquivo inválido
                        }
                      });
                    </script>

                  <?php } else { ?>

                    <label class="form-label mb-0">Imagem de fundo</label>
                    <div class="result_file">
                      <div class="result_file_name"><a href="../uploads/certificado/<?= $prop_codigo ?>/<?= $cert_arq_arquivo ?>" target="_blank"><?= $cert_arq_arquivo ?></a></div>
                      <span class="item_bt_row">
                        <a href="controller/controller_certificado.php?funcao=exc_img_cert&cert_arq_id=<?= $cert_arq_id ?>&cert_arq_arquivo=<?= $cert_arq_arquivo ?>&prop_codigo=<?= $prop_codigo ?>" class="bt_table del-btn" title="Excluir"><i class="fa-regular fa-trash-can"></i></a>
                      </span>
                    </div>

                  <?php } ?>

                  <div class="col-12 mt-4">
                    <div>
                      <div class="form-check">
                        <input class="form-check-input insc_credenciamento" type="checkbox" id="checkCredenciado" name="cert_status" value="1" <?php echo ($cert_status == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="checkCredenciado">Liberar acesso ao certificado</label>
                      </div>
                    </div>
                  </div>


                  <div class="col-lg-12 mt-5">
                    <div class="hstack gap-3 align-items-center justify-content-end">
                      <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                      <?php if (!$cert_id) { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="CadCertificado">Cadastrar</button>
                      <?php } else { ?>
                        <button type="submit" class="btn botao botao_verde waves-effect waves-light" name="EditCertificado">Salvar</button>
                      <?php } ?>
                    </div>
                  </div>
                </form>

              </div>

            <?php } ?>

            <!-- </form> -->

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

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>