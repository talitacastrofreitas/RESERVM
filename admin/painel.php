<?php include 'includes/header.php'; ?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

<div class="row breadcrumb_painel">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Disponibilidade</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="">Disponibilidade</a></li>
          <li class="breadcrumb-item active">Disponibilidade</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <div class="row g-3">

            <div class="col-sm-8 col-xl-10">
              <input type="date" class="form-control flatpickr-input" name="data" id="data" value="<?= htmlspecialchars($_GET['data'] ?? date('Y-m-d')) ?>">
              <script>
                flatpickr("#data", {
                  dateFormat: "Y-m-d", // formato do valor REAL do input (enviado na query)
                  altInput: true, // ativa um input visível com formato alternativo
                  altFormat: "d/m/Y", // formato exibido para o usuário
                  locale: "pt" // idioma português
                });
              </script>
            </div>

            <div class="col-sm-4 col-xl-2">
              <div class="d-flex">
                <button type="submit" class="btn botao botao_azul_escuro waves-effect w-100">Filtrar</button>
                <div onclick="window.location.href='painel.php'" class="btn botao botao_cinza waves-effect w-100 ms-3">Limpar</div>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .header_time {
    width: 100px;
    font-size: 11px;
    padding: 0px !important;
    text-align: center;
  }

  @media (max-width: 1600px) {
    .header_time {
      display: none;
    }
  }

  .table_painel thead th,
  .table_painel thead td,
  .table_painel thead tr:hover {
    background: transparent !important;
    padding: 6px 2px 6px 6px !important;
  }

  .table_painel tbody tr td.barra {
    width: 100px !important;
    font-size: 12px !important;
    padding: 0px 5px !important;
    text-align: center !important;
    background: #2d9866 !important;
    color: #fff !important;
  }

  .table_painel tbody tr td.barra.conflito {
    background-color: var(--vermelho) !important;
  }


  .table_painel tbody tr td.barra.border_l {
    border-left: 6px solid #fff !important;
  }

  .table_painel tbody tr td.barra.border_r {
    border-right: 6px solid #fff !important;
  }


  .table_painel tbody tr {
    background: #F2F4F8;
  }

  .table_painel tbody tr:hover {
    background: #E1E9F1;
  }

  .table_painel tbody th,
  .table_painel tbody td {
    padding: 0 !important;
    border-bottom: 6px solid #fff !important;
    border-color: #fff !important;
  }

  .table_painel .col_cod {
    /* padding: 5px 10px !important; */
    text-align: center;
  }


  .table_painel .col_loc {
    padding: 0 10px;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    width: 250px;
    max-width: 250px;
    box-sizing: border-box !important;
  }

  .table_painel .box {
    width: 30px !important;
    padding: 0 12px !important;
  }

  .table_painel .box_s {
    background: #DAEDF3 !important;
    border-left: 6px solid #fff;
    border-right: 6px solid #fff;
    font-weight: 500;
    color: #44A6C4 !important;
    font-size: 11px !important;
    text-align: center !important;
    padding: 5px !important;
    margin: 0px -6px 0px 0px;
  }

  .table_painel .box_a {
    background: #D4DFEE !important;
    border-left: 6px solid #fff;
    border-right: 6px solid #fff;
    font-weight: 500;
    color: #285FAB !important;
    font-size: 11px !important;
    text-align: center !important;
    padding: 5px !important;
    margin: 0px -6px 0px 0px;
  }

  .table_painel .box_p {
    background: #F3DAD8 !important;
    border-left: 6px solid #fff;
    border-right: 6px solid #fff;
    font-weight: 500;
    color: #C4453E !important;
    font-size: 11px !important;
    text-align: center !important;
    padding: 5px !important;
    margin: 0px -6px 0px 0px;
  }

  .table_painel .box_c {
    background: #E7DCED !important;
    border-left: 6px solid #fff;
    border-right: 6px solid #fff;
    font-weight: 500;
    color: #8652A6 !important;
    font-size: 11px !important;
    text-align: center !important;
    padding: 5px !important;
    margin: 0px -6px 0px 0px;
  }


  .border_l {
    border-left: 6px solid #fff !important;
    padding: 5px 10px !important;
    margin: 0 5px !important;
  }


  .border_r {
    border-right: 0px solid #fff !important;
    padding: 5px 10px !important;
    margin: 0 5px !important;
  }


  @media (max-width: 1600px) {
    .border_l {
      border-left: 6px solid #fff !important;
    }

    .border_r {
      border-right: 0px solid #fff !important;
    }

    .table_painel tbody tr td.barra.border_r {
      border-right: 0px solid #fff !important;
    }
  }
</style>

<?php
try {
  // $date = date('Y-m-d');
  $date = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
  $diaSemanaNumero = (new DateTime($date))->format('N');

  $stmt = $conn->prepare("SELECT esp_id, esp_nome_local, esp_codigo, tipesp_tipo_espaco_icone, and_andar_icone, pav_pavilhao_icone, uni_unidade_icone, res_data, res_hora_inicio, res_hora_fim, res_solic_id
                          FROM espaco
                          LEFT JOIN reservas 
                            ON reservas.res_espaco_id = espaco.esp_id
                            AND (
                              (reservas.res_tipo_reserva = 1 AND reservas.res_data = :res_data)
                              OR 
                              (reservas.res_tipo_reserva = 2 AND reservas.res_dia_semana = :res_dia_semana)
                            )
                          INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                          LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                          LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                          INNER JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                          ORDER BY esp_nome_local");

  $stmt->execute([
    ':res_data' => $date,
    ':res_dia_semana' => $diaSemanaNumero
  ]);

  $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
}
?>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-sm-6 text-sm-start text-center">
            <h5 class="card-title mb-sm-0 mb-2">Lista de Disponibilidade</h5>
          </div>
          <div class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">

            <?php
            $meses = [
              1 => 'Janeiro',
              2 => 'Fevereiro',
              3 => 'Março',
              4 => 'Abril',
              5 => 'Maio',
              6 => 'Junho',
              7 => 'Julho',
              8 => 'Agosto',
              9 => 'Setembro',
              10 => 'Outubro',
              11 => 'Novembro',
              12 => 'Dezembro'
            ];

            $diasSemana = [
              'Sunday'    => 'Domingo',
              'Monday'    => 'Segunda-feira',
              'Tuesday'   => 'Terça-feira',
              'Wednesday' => 'Quarta-feira',
              'Thursday'  => 'Quinta-feira',
              'Friday'    => 'Sexta-feira',
              'Saturday'  => 'Sábado'
            ];

            // Usa data do GET, se existir, senão usa a atual
            $data = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');

            // Cria objeto DateTime com a data informada ou atual
            $dataObj = new DateTime($data);

            // Extrai partes da data
            $dia = $dataObj->format('d');
            $mes = (int)$dataObj->format('m');
            $ano = $dataObj->format('Y');
            $diaSemanaIngles = $dataObj->format('l');
            $diaSemana = $diasSemana[$diaSemanaIngles];

            // Monta a data formatada
            echo "{$diaSemana}, {$dia} de {$meses[$mes]} de {$ano}";
            ?>
          </div>
        </div>
      </div>
      <div class="card-body px-3 py-1">
        <div class="table-responsive">
          <table class="table align-middle table_painel">
            <thead>
              <tr>
                <th>Código</th>
                <th>Local</th>
                <th class="box"></th>
                <th class="box"></th>
                <th class="box"></th>
                <th class="box"></th>
                <?php
                // Geração dinâmica das colunas de horário
                $start = strtotime("07:00");
                $end = strtotime("22:00");
                for ($i = $start; $i <= $end; $i += 1800) { // 1800 segundos = 30 minutos 
                ?>
                  <!-- <th class="header_time"></th> -->
                  <th class="header_time"><?= date("H:i", $i) ?></th>
                <?php } ?>

              </tr>
            </thead>
            <tbody>
              <?php
              $salas = [];
              foreach ($reservas as $reserva) {
                $chave = $reserva['esp_id'] . '|' . $reserva['esp_nome_local'];
                $salas[$chave][] = $reserva;
              }

              foreach ($salas as $chave => $reservas_sala) {
                list($esp_id, $sala) = explode('|', $chave);
              ?>
                <tr>
                  <th class="col_cod text-start">
                    <div class="px-2"><?= htmlspecialchars($reservas_sala[0]['esp_codigo']) ?></div>
                  </th>
                  <td>
                    <div class="col_loc" title="<?= htmlspecialchars($sala) ?>"><?= htmlspecialchars($sala) ?></div>
                  </td>
                  <td>
                    <div role="button" data-bs-toggle="modal" data-bs-target="#modal_legenda" class="box_s"><?= htmlspecialchars($reservas_sala[0]['tipesp_tipo_espaco_icone']) ?></div>
                  </td>
                  <td>
                    <?php if ($reservas_sala[0]['and_andar_icone']) { ?>
                      <div role="button" data-bs-toggle="modal" data-bs-target="#modal_legenda" class="box_a"><?= htmlspecialchars($reservas_sala[0]['and_andar_icone']) ?></div>
                    <?php } ?>
                  </td>
                  <td>
                    <?php if ($reservas_sala[0]['pav_pavilhao_icone']) { ?>
                      <div role="button" data-bs-toggle="modal" data-bs-target="#modal_legenda" class="box_p"><?= htmlspecialchars($reservas_sala[0]['pav_pavilhao_icone']) ?></div>
                    <?php } ?>
                  </td>
                  <td>
                    <div role="button" data-bs-toggle="modal" data-bs-target="#modal_legenda" class="box_c"><?= htmlspecialchars($reservas_sala[0]['uni_unidade_icone']) ?></div>
                  </td>

                  <?php for ($i = $start; $i <= $end; $i += 1800): ?>
                    <?php
                    $hora_coluna = date("H:i", $i);
                    $reservas_no_horario = [];

                    foreach ($reservas_sala as $reserva) {

                      if ($reserva['res_data'] !== $date) continue;

                      $hora_inicio_real = $reserva['res_hora_inicio'];
                      $hora_fim_real = $reserva['res_hora_fim'];

                      $hora_inicio_arred = date("H:i", floor(strtotime($hora_inicio_real) / 1800) * 1800);
                      $hora_fim_arred = date("H:i", ceil(strtotime($hora_fim_real) / 1800) * 1800);

                      if ($hora_coluna >= $hora_inicio_arred && $hora_coluna < $hora_fim_arred) {
                        $reservas_no_horario[] = $reserva;
                      }
                    }

                    if (count($reservas_no_horario) > 0):
                      $conflito = count($reservas_no_horario) > 1;
                      $reserva = $reservas_no_horario[0];

                      $hora_inicio_real = $reserva['res_hora_inicio'];
                      $hora_fim_real = $reserva['res_hora_fim'];

                      $hora_inicio_arred = date("H:i", floor(strtotime($hora_inicio_real) / 1800) * 1800);
                      $hora_fim_arred = date("H:i", ceil(strtotime($hora_fim_real) / 1800) * 1800);

                      $mostra_inicio = ($hora_coluna == $hora_inicio_arred) ? date("H:i", strtotime($hora_inicio_real)) : '';
                      $mostra_fim = ($hora_coluna == date("H:i", strtotime($hora_fim_arred) - 1800)) ? date("H:i", strtotime($hora_fim_real)) : '';

                      $classe = 'barra' . ($conflito ? ' conflito' : '');
                    ?>
                      <td role="button" onclick="location.href='solicitacao_analise.php?i=<?= $reserva['res_solic_id'] ?>'" class="<?= $classe ?> <?= ($hora_coluna == $hora_inicio_arred) ? 'border_l' : ''; ?> <?= ($hora_coluna == date("H:i", strtotime($hora_fim_arred) - 1800)) ? 'border_r' : ''; ?>">
                        <?= $mostra_inicio ?> <?= $mostra_fim ?>
                      </td>
                    <?php else: ?>
                      <td></td>
                    <?php endif; ?>
                  <?php endfor; ?>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL LEGENDA -->
<div id="modal_legenda" class="modal zoomIn fade" tabindex="-1" aria-labelledby="ModalLegLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header d-sm-none d-inline text-end">
        <!-- <h5 class="modal-title" id="ModalLegLabel">SUMÁRIO</h5> -->
        <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        <div class="row dados_legenda">
          <div class="col-sm-6 col-lg-4">
            <div class="bloco_legenda">
              <h5>Tipo de Espaço</h5>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul">SA</span>
                <p>SALA DE AULA</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul">AU</span>
                <p>AUDITÓRIO</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul">EE</span>
                <p>ESPAÇO EXTERNO</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul">LE</span>
                <p>LABORATÓRIO DE ENSINO</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul">LI</span>
                <p>LABORATÓRIO DE INFORMÁTICA</p>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-lg-3">
            <div class="bloco_legenda">
              <h5>Andar</h5>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul_escuro">A1</span>
                <p>1º ANDAR</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul_escuro">A2</span>
                <p>2º ANDAR</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul_escuro">A3</span>
                <p>3º ANDAR</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul_escuro">SS</span>
                <p>SUBSOLO</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_azul_escuro">TE</span>
                <p>TÉRREO</p>
              </div>

            </div>
          </div>

          <div class="col-sm-6 col-lg-3">
            <div class="bloco_legenda">
              <h5>Pavilhão</h5>

              <div class="box_legenda">
                <span class="box_icon box_icon_vermelho">P1</span>
                <p>PAVILHÃO I</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_vermelho">P2</span>
                <p>PAVILHÃO II</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_vermelho">P3</span>
                <p>PAVILHÃO III</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_vermelho">P4</span>
                <p>PAVILHÃO IV</p>
              </div>

            </div>
          </div>

          <div class="col-sm-6 col-lg-2">
            <div class="bloco_legenda">
              <h5>Campus</h5>

              <div class="box_legenda">
                <span class="box_icon box_icon_roxo">B</span>
                <p>BROTAS</p>
              </div>

              <div class="box_legenda">
                <span class="box_icon box_icon_roxo">C</span>
                <p>CABULA</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>