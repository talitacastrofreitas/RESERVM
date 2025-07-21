<?php include 'includes/header.php'; ?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
  <div class="profile-wid-bg">
  </div>
</div>

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

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <div class="row g-3">

            <!-- <div class="col-md-8">
              <div class="search-box">
                <input type="search" class="form-control text-uppercase" id="inputBusca" autocomplete="off">
                <i class="ri-search-line search-icon"></i>
              </div>
            </div> -->

            <!-- <div class="col">
              <div>
                <select class="form-select text-uppercase" name="admin_perfil" id="inputCat">
                  <option selected disabled value="all">Código</option>
                  <option value="all">TODAS</option>
                  <?php $sql = $conn->query("SELECT propc_id, propc_categoria FROM propostas_categorias ORDER BY propc_id");
                  while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $propc['propc_categoria'] ?>"><?= $propc['propc_categoria'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-md-8 col-xxl-8">
              <div>
                <select class="form-select text-uppercase" name="admin_perfil" id="inputCat">
                  <option selected disabled value="all">Local</option>
                  <?php $sql = $conn->query("SELECT DISTINCT sala FROM classe ORDER BY sala");
                  while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $propc['sala'] ?>"><?= $propc['sala'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col">
              <div>
                <select class="form-select text-uppercase" name="admin_perfil" id="inputCat">
                  <option selected disabled value="all">Tipo de espaço</option>
                  <?php $sql = $conn->query("SELECT propc_id, propc_categoria FROM propostas_categorias ORDER BY propc_id");
                  while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $propc['propc_categoria'] ?>"><?= $propc['propc_categoria'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col">
              <div>
                <select class="form-select text-uppercase" name="admin_perfil" id="inputCat">
                  <option selected disabled value="all">Andar</option>
                  <?php $sql = $conn->query("SELECT and_id, and_andar FROM andares ORDER BY and_andar");
                  while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $propc['and_id'] ?>"><?= $propc['and_andar'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col">
              <div>
                <select class="form-select text-uppercase" name="admin_perfil" id="inputCat">
                  <option selected disabled value="all">Pavilhão</option>
                  <?php $sql = $conn->query("SELECT pav_id, pav_pavilhao FROM pavilhoes ORDER BY pav_pavilhao");
                  while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $propc['pav_id'] ?>"><?= $propc['pav_pavilhao'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col">
              <div>
                <select class="form-select text-uppercase" name="admin_perfil" id="inputCat">
                  <option selected disabled value="all">Campus</option>
                  <?php $sql = $conn->query("SELECT uni_id, uni_unidade FROM unidades ORDER BY uni_id");
                  while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                    <option value="<?= $propc['uni_id'] ?>"><?= $propc['uni_unidade'] ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col">
              <div>
                <select class="form-select" name="admin_perfil" id="inputCat">
                  <option selected disabled value="all">Capacidade</option>
                  <option value="all">TODAS</option>
                  <option value="">20</option>
                  <option value="">30</option>
                  <option value="">40</option>
                  <option value="">50</option>
                  <option value="">60</option>
                  <option value="">70</option>
                </select>
              </div>
            </div> -->

            <div class="col-sm-8 col-xl-10">
              <input type="date" class="form-control" name="data">
            </div>

            <div class="col-sm-4 col-xl-2">
              <div class="d-flex">
                <button type="submit" class="btn botao botao_azul_escuro waves-effect w-100">Filtrar</button>
                <div onclick="window.location.href='painel.php'" class="btn botao botao_cinza waves-effect w-100 ms-3">Limpar</div>
              </div>
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

<!-- <div class="row">
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
</div> -->
<!-- end row -->

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
    padding: 6px 2px 6px 9px !important;
  }

  .table_painel tbody tr td.barra {
    width: 100px !important;
    font-size: 12px !important;
    padding: 0px 5px !important;
    text-align: center !important;
    background: #2d9866 !important;
    color: #fff !important;
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
    width: 300px;
    max-width: 300px;
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
  }
</style>

<?php
try {
  // $date = date('Y-m-d');
  $date = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
  // $diaSemanaNumero = date('N'); // 1 (Segunda) a 7 (Domingo)

  // Pega o número do dia da semana com base na data informada
  $diaSemanaNumero = (new DateTime($date))->format('N');

  $stmt = $conn->prepare("SELECT * FROM espaco
                          LEFT JOIN reservas 
                            ON reservas.res_espaco_id = espaco.esp_id
                          INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                          LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                          LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                          INNER JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                          WHERE reservas.res_id IS NULL 
                            OR (
                                  (reservas.res_tipo_reserva = 1 AND reservas.res_data = :res_data)
                                  OR 
                                  (reservas.res_tipo_reserva = 2 AND reservas.res_dia_semana = :res_dia_semana)
                            )
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

            // Data atual
            // $dia = date('d');
            // $mes = (int)date('m');
            // $ano = date('Y');
            // $diaSemanaIngles = date('l');
            // $diaSemana = $diasSemana[$diaSemanaIngles];

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
                // $salas[$reserva['esp_nome_local']][] = $reserva;
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
                    <div class="box_s"><?= htmlspecialchars($reservas_sala[0]['tipesp_tipo_espaco_icone']) ?></div>
                  </td>
                  <td>
                    <?php if ($reservas_sala[0]['and_andar_icone']) { ?>
                      <div class="box_a"><?= htmlspecialchars($reservas_sala[0]['and_andar_icone']) ?></div>
                    <?php } ?>
                  </td>
                  <td>
                    <?php if ($reservas_sala[0]['pav_pavilhao_icone']) { ?>
                      <div class="box_p"><?= htmlspecialchars($reservas_sala[0]['pav_pavilhao_icone']) ?></div>
                    <?php } ?>
                  </td>
                  <td>
                    <div class="box_c"><?= htmlspecialchars($reservas_sala[0]['uni_unidade_icone']) ?></div>
                  </td>

                  <?php for ($i = $start; $i <= $end; $i += 1800) {
                    $hora_coluna = date("H:i", $i);
                    $ocupado = false;

                    foreach ($reservas_sala as $reserva) {
                      $hora_inicio = date("H:i", strtotime($reserva['res_hora_inicio']));
                      $hora_fim = date("H:i", strtotime($reserva['res_hora_fim']));

                      if ($hora_coluna >= $hora_inicio && $hora_coluna < $hora_fim) {
                        $ocupado = true; ?>

                        <td role="button" class="barra <?= ($hora_coluna == $hora_inicio) ? 'border_l' : ''; ?> <?= ($hora_coluna == date("H:i", strtotime($hora_fim) - 1800)) ? 'border_r' : ''; ?>">
                          <?= ($hora_coluna == $hora_inicio) ? $hora_inicio : ""; ?>
                          <?= ($hora_coluna == date("H:i", strtotime($hora_fim) - 1800)) ? $hora_fim : ""; ?>
                        </td>
                <?php break;
                      }
                    }
                    if (!$ocupado) {
                      echo "<td></td>";
                    }
                  }

                  echo "</tr>";
                }
                ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
</div>

<?php include 'includes/footer.php'; ?>