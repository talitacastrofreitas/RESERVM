<?php include 'includes/header.php'; ?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Reservas Confirmadas</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
          <li class="breadcrumb-item active">Reservas Confirmadas</li>
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
          <div class="col-sm-8 text-sm-start text-center">
            <h5 class="card-title mb-0">Lista de Reservas Confirmadas</h5>
          </div>
          <div class="col-sm-4 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <a href="../relatorios/reservas.php" class="btn botao botao_roxo waves-effect mt-3 mt-sm-0"
              style="padding: 7.5px 13px !important">CSV</a>
          </div>
        </div>
      </div>



      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="tab_reserva_confirm" class="table align-middle" style="width:100%">
            <thead>
              <tr>
                <th nowrap="nowrap"><span class="me-3">Data</span></th>
                <th nowrap="nowrap"><span class="me-3">Dia</span></th>
                <th nowrap="nowrap"><span class="me-3">Mês</span></th>
                <th nowrap="nowrap"><span class="me-3">Ano</span></th>
                <th nowrap="nowrap"><span class="me-3">Início</span></th>
                <th nowrap="nowrap"><span class="me-3">Fim</span></th>
                <th nowrap="nowrap"><span class="me-3">Turno</span></th>
                <th nowrap="nowrap"><span class="me-3">ID Solicitação</span></th>
                <th nowrap="nowrap"><span class="me-3">ID da Reserva</span></th>
                <th nowrap="nowrap"><span class="me-3">Tipo de Aula</span></th>
                <th nowrap="nowrap"><span class="me-3">Curso</span></th>
                <th nowrap="nowrap"><span class="me-3">Semestre</span></th>
                <th nowrap="nowrap"><span class="me-3">Componente Curricular/Atividade</span></th>
                <th nowrap="nowrap"><span class="me-3">Módulo</span></th>
                <th nowrap="nowrap"><span class="me-3">Professor</span></th>
                <th nowrap="nowrap"><span class="me-3">Título Aula</span></th>
                <th nowrap="nowrap"><span class="me-3">Recursos</span></th>
                <th nowrap="nowrap"><span class="me-3">Recursos Audiovisuais Add</span></th>
                <th nowrap="nowrap"><span class="me-3">Obs</span></th>
                <th nowrap="nowrap"><span class="me-3">Nº Pessoas</span></th>
                <th nowrap="nowrap"><span class="me-3">Tipo Reserva</span></th>
                <th nowrap="nowrap"><span class="me-3">ID Local</span></th>
                <th nowrap="nowrap"><span class="me-3">Local Reservado</span></th>
                <th nowrap="nowrap"><span class="me-3">Andar</span></th>
                <th nowrap="nowrap"><span class="me-3">Pavilhão</span></th>
                <th nowrap="nowrap"><span class="me-3">Campus</span></th>
                <th nowrap="nowrap"><span class="me-3">Tipo de Sala</span></th>
                <th nowrap="nowrap"><span class="me-3">Capacidade</span></th>
                <th nowrap="nowrap"><span class="me-3">Confirmado por</span></th>
                <th nowrap="nowrap"><span class="me-3">Data Solicitação</span></th>
                <th nowrap="nowrap"><span class="me-3">Data Reserva</span></th>

                <th nowrap="nowrap"><span class="me-3">CH Programada</span></th>

              </tr>

            </thead>
            <tbody>


              <?php
              try {
                // PARA VERIFICAR CONFLITOS ANTERIORES
                $reservas_analisadas = [];
                $reservas = [];

                // CALCULO DAS CARGAS HORÁRIAS
                function calcularDiferencaHoras($inicio, $fim)
                {
                  if (!$inicio || !$fim)
                    return null;
                  $inicio = DateTime::createFromFormat('H:i:s.u', substr($inicio, 0, 15));
                  $fim = DateTime::createFromFormat('H:i:s.u', substr($fim, 0, 15));
                  if ($inicio && $fim && $fim > $inicio) {
                    $diff = $fim->diff($inicio);
                    return $diff->format('%H:%I');
                  }
                  return '00:00';
                }

                function paraMinutos($hora)
                {
                  list($h, $m) = explode(':', $hora);
                  return ($h * 60) + $m;
                }

                function paraHoraMin($minutos)
                {
                  $h = floor($minutos / 60);
                  $m = $minutos % 60;
                  return sprintf('%02d:%02d', $h, $m);
                }

                $user_id_logado = $global_user_id;

                $stmt = $conn->prepare("SELECT solic_id, res_id, res_espaco_id, res_data, week_dias, res_mes, res_ano, res_hora_inicio, res_hora_fim, res_turno, res_tipo_aula, res_tipo_reserva, res_recursos, res_recursos_add, res_codigo, cta_tipo_aula, curs_curso, cs_semestre,res_componente_atividade, compc_componente, res_componente_atividade_nome, res_nome_atividade, res_modulo, res_professor, res_titulo_aula, res_obs, res_quant_pessoas, ctr_tipo_reserva, esp_codigo, esp_nome_local, and_andar, pav_pavilhao, uni_unidade, tipesp_tipo_espaco, esp_quant_maxima, admin_nome, solic_data_cad, res_data_cad, solic_codigo
                        FROM reservas
                        INNER JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
                        INNER JOIN solicitacao_status AS ss ON ss.solic_sta_solic_id = solicitacao.solic_id
                        INNER JOIN conf_dias_semana ON conf_dias_semana.week_id = reservas.res_dia_semana
                        LEFT JOIN cursos ON cursos.curs_id = reservas.res_curso
                        LEFT JOIN conf_semestre ON conf_semestre.cs_id = reservas.res_semestre
                        LEFT JOIN componente_curricular ON componente_curricular.compc_id = reservas.res_componente_atividade
                        INNER JOIN conf_tipo_reserva ON conf_tipo_reserva.ctr_id = reservas.res_tipo_reserva
                        INNER JOIN conf_tipo_aula ON conf_tipo_aula.cta_id = reservas.res_tipo_aula
                        INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                        INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                        LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                        LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                        LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                        LEFT JOIN recursos ON ',' + ISNULL(reservas.res_recursos_add, '') + ',' LIKE '%,' + CAST(recursos.rec_id AS VARCHAR) + ',%'
                        INNER JOIN admin ON admin.admin_id = reservas.res_user_id
                        -- REMOVIDO: LEFT JOIN ocorrencias
                        
                        -- FILTROS DE STATUS E USUÁRIO
                        WHERE ss.solic_sta_status = 4
                        AND solicitacao.solic_cad_por = :user_id 
                        
                        -- ORDENAÇÃO POR CÓDIGO DA SOLICITAÇÃO E DATA
                        ORDER BY solicitacao.solic_codigo ASC, reservas.res_data ASC");

                $stmt->execute([':user_id' => $user_id_logado]);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                  $solic_id = $row['solic_id'];
                  $res_id = $row['res_id'];
                  $res_espaco_id = $row['res_espaco_id'];
                  $res_data = $row['res_data'];
                  $week_dias = $row['week_dias'];
                  $res_mes = $row['res_mes'];
                  $res_ano = $row['res_ano'];
                  $res_hora_inicio = $row['res_hora_inicio'];
                  $res_hora_fim = $row['res_hora_fim'];
                  $res_turno = $row['res_turno'];
                  $res_tipo_aula = $row['res_tipo_aula'];
                  $res_tipo_reserva = $row['res_tipo_reserva'];
                  $res_recursos = $row['res_recursos'];
                  $res_recursos_add = $row['res_recursos_add'];
                  $res_codigo = $row['res_codigo'];
                  $cta_tipo_aula = $row['cta_tipo_aula'];
                  $curs_curso = $row['curs_curso'];
                  $cs_semestre = $row['cs_semestre'];
                  $res_componente_atividade = $row['res_componente_atividade'];
                  $compc_componente = $row['compc_componente'];
                  $res_componente_atividade_nome = $row['res_componente_atividade_nome'];
                  $res_nome_atividade = $row['res_nome_atividade'];
                  $res_modulo = $row['res_modulo'];
                  $res_professor = $row['res_professor'];
                  $res_titulo_aula = $row['res_titulo_aula'];
                  $res_obs = $row['res_obs'];
                  $res_quant_pessoas = $row['res_quant_pessoas'];
                  $ctr_tipo_reserva = $row['ctr_tipo_reserva'];
                  $esp_codigo = $row['esp_codigo'];
                  $esp_nome_local = $row['esp_nome_local'];
                  $and_andar = $row['and_andar'];
                  $pav_pavilhao = $row['pav_pavilhao'];
                  $uni_unidade = $row['uni_unidade'];
                  $tipesp_tipo_espaco = $row['tipesp_tipo_espaco'];
                  $esp_quant_maxima = $row['esp_quant_maxima'];
                  $admin_nome = $row['admin_nome'];
                  $solic_data_cad = $row['solic_data_cad'];
                  $res_data_cad = $row['res_data_cad'];
                  $solic_codigo = $row['solic_codigo'];

                  ////////////////////////////////////////////
                  // TRATA OS DADOS DOS RECURSOS ADICIONAIS //
                  ////////////////////////////////////////////
                  $res_recursos_ids = trim($res_recursos_add ?? '');
                  $res_recursos_ids = rtrim($res_recursos_ids, ',');

                  if (empty($res_recursos_ids)) {
                    $row['recursos_formatados'] = '';
                  } else {
                    $ids_array = array_filter(array_map('trim', explode(',', $res_recursos_ids)), 'ctype_digit');

                    if (count($ids_array) === 0) {
                      $row['recursos_formatados'] = '';
                    } else {
                      $res_recursos_ids_sql = implode(',', $ids_array);
                      $sql_recursos = "SELECT rec_recurso FROM recursos WHERE rec_id IN ($res_recursos_ids_sql)";
                      $stmt_rec = $conn->prepare($sql_recursos);
                      $stmt_rec->execute();
                      $recursos = $stmt_rec->fetchAll(PDO::FETCH_COLUMN);
                      $row['recursos_formatados'] = implode(' / ', $recursos);
                    }
                  }

                  $recursos_formatados = $row['recursos_formatados'];

                  // Lógica de Conflito (Apenas a Classificação para o HTML, sem cálculo de CH)
                  $hora_inicio = new DateTime($res_hora_inicio);
                  $hora_fim = new DateTime($res_hora_fim);
                  $conflito = false;
                  foreach ($reservas_analisadas as $r) {
                    if (
                      $r['data'] === $res_data &&
                      $r['espaco_id'] === $res_espaco_id &&
                      $r['esp_codigo'] === $esp_codigo &&
                      (
                        ($hora_inicio < $r['fim'] && $hora_fim > $r['inicio']) ||
                        ($hora_inicio == $r['inicio'] || $hora_fim == $r['fim'])
                      )
                    ) {
                      $conflito = true;
                      break;
                    }
                  }
                  $reservas_analisadas[] = [
                    'data' => $res_data,
                    'espaco_id' => $res_espaco_id,
                    'esp_codigo' => $esp_codigo,
                    'inicio' => $hora_inicio,
                    'fim' => $hora_fim,
                  ];
                  $conflito_class = $conflito ? 'conflict-cell' : '';

                  // Classificações de Cores
                  $tipo_aula_color = $res_tipo_aula == 1 ? 'bg_info_laranja' : 'bg_info_azul';
                  $tipo_reserva_color = $res_tipo_reserva == 1 ? 'bg_info_roxo' : 'bg_info_azul_escuro';
                  $recursos_color = $res_recursos == 'SIM' ? 'bg_info_verde' : 'bg_info_vermelho';

                  // Carga Horária Programada
                  $ch_programada = calcularDiferencaHoras($res_hora_inicio, $res_hora_fim);

                  // Montagem do Componente
                  if (!empty($res_componente_atividade)) {
                    $componente = $compc_componente;
                  } else if (!empty($res_componente_atividade_nome)) {
                    $componente = $res_componente_atividade_nome;
                  } else if (!empty($res_nome_atividade)) {
                    $componente = $res_nome_atividade;
                  }
                  ?>


                  <tr>
                    <th scope="row" nowrap="nowrap"
                      class="bg_table_fix_vermelho <?= $conflito_class ? 'bg_table_fix_vermelho_escuro' : '' ?>"><span
                        class="hide_data"><?= date('Ymd', strtotime($res_data)) ?></span><?= htmlspecialchars(date('d/m/Y', strtotime($res_data))) ?>
                    </th>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_laranja <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_laranja_escuro' : '' ?>">
                      <?= htmlspecialchars(substr($week_dias, 0, 3)) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_verde <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_verde_escuro' : '' ?>">
                      <?= htmlspecialchars($res_mes) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_azul <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_azul_escuro' : '' ?>">
                      <?= htmlspecialchars($res_ano) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_roxo <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_roxo_escuro' : '' ?>">
                      <?= htmlspecialchars(date("H:i", strtotime($res_hora_inicio))) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_rosa <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_rosa_escuro' : '' ?>">
                      <?= htmlspecialchars(date("H:i", strtotime($res_hora_fim))) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"
                      class="bg_table_fix_cinza <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_cinza_escuro' : '' ?>">
                      <?= htmlspecialchars($res_turno) ?>
                    </td>

                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($solic_codigo) ?></strong></td>

                    <th scope="row" nowrap="nowrap"><?= htmlspecialchars($res_codigo) ?></th>

                    <td scope="row" nowrap="nowrap"><span
                        class="badge <?= $tipo_aula_color ?>"><?= htmlspecialchars($cta_tipo_aula) ?></span></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($curs_curso) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($cs_semestre) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($componente) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_modulo) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_professor) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_titulo_aula) ?></td>
                    <td scope="row" nowrap="nowrap"><span
                        class="badge <?= $recursos_color ?>"><?= htmlspecialchars($res_recursos) ?></span></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($recursos_formatados) ?></td>
                    <td scope="row">

                      <?php if ($res_obs) { ?>
                        <button type="button" class="btn btn_soft_azul_escuro btn-sm" data-bs-toggle="modal"
                          data-bs-target="#modal_obs<?= $res_id ?>"><i class="fa-regular fa-comment-dots"></i></button>
                        <div id="modal_obs<?= $res_id ?>" class="modal zoomIn fade" tabindex="-1"
                          aria-labelledby="ModalObsLabel" aria-hidden="true" style="display: none;">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="ModalObsLabel">Observação</h5>
                                <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
                              </div>
                              <div class="modal-body">
                                <p class="fs-14 m-0"><?= htmlspecialchars($res_obs) ?></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php } ?>

                    </td>

                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_quant_pessoas) ?></td>
                    <td scope="row" nowrap="nowrap"><span
                        class="badge <?= $tipo_reserva_color ?>"><?= htmlspecialchars($ctr_tipo_reserva) ?></span></td>
                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($esp_codigo) ?></strong></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($esp_nome_local) ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($and_andar) ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($pav_pavilhao) ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($uni_unidade) ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($tipesp_tipo_espaco) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($esp_quant_maxima) ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($admin_nome) ?></td>

                    <td scope="row" nowrap="nowrap"><span
                        class="hide_data"><?= date('Ymd', strtotime($solic_data_cad)) ?></span><?= htmlspecialchars(htmlspecialchars(date('d/m/Y', strtotime($solic_data_cad)))) ?>
                    </td>
                    <td scope="row" nowrap="nowrap"><span
                        class="hide_data"><?= date('Ymd', strtotime($res_data_cad)) ?></span><?= htmlspecialchars(htmlspecialchars(date('d/m/Y', strtotime($res_data_cad)))) ?>
                    </td>

                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($ch_programada) ?></td>

                  </tr>

                <?php }
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar os dados" . $e->getMessage();
              } ?>

            </tbody>
          </table>
        </div>

        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const marcarTodos = document.getElementById('marcarTodos');
            const checkboxes = document.querySelectorAll('.checkbox');
            const btnExcluir = document.getElementById('btnExcluirSelecionados');
            const form = document.getElementById('formExcluirSelecionados');

            if (!marcarTodos || !checkboxes.length || !btnExcluir || !form) return;

            function atualizarBotaoExcluir() {
              const algumMarcado = document.querySelectorAll('.checkbox:checked').length > 0;
              btnExcluir.style.display = algumMarcado ? 'inline-block' : 'none';
            }

            marcarTodos.addEventListener('change', function () {
              checkboxes.forEach(cb => cb.checked = this.checked);
              atualizarBotaoExcluir();
            });

            checkboxes.forEach(cb => cb.addEventListener('change', atualizarBotaoExcluir));

            form.addEventListener('submit', function (e) {
              e.preventDefault(); // Impede envio imediato

              Swal.fire({
                text: 'Deseja excluir os itens selecionados?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0461AD',
                cancelButtonColor: '#C4453E',
                confirmButtonText: 'Excluir',
                cancelButtonText: 'Cancelar',
              }).then((result) => {
                if (result.isConfirmed) {
                  form.submit(); // Envia o formulário manualmente após confirmação
                }
              });
            });

          });
        </script>

        <script>
          $(document).ready(function () {
            // Quando clicar numa linha da tabela
            $('table').on('click', 'tr', function (e) {
              // Ignora cliques em botões, ícones, botões de fechar modal
              if (
                $(e.target).closest('.btn').length > 0 ||
                $(e.target).closest('.btn-close').length > 0 ||
                $(e.target).closest('.modal').length > 0
              ) {
                return;
              }

              // Vai para o link da linha (data-href)
              const href = $(this).data('href');
              if (href) {
                window.location.href = href;
              }
            });

            // Impede propagação de clique em botões ou ícones dentro da tabela
            $(document).on('click', '.btn, .btn-close', function (e) {
              e.stopPropagation();
            });

            // Impede que clique fora do modal acione a linha da tabela
            $(document).on('click', '.modal, .modal *', function (e) {
              e.stopPropagation();
            });
          });
        </script>
      </div>
    </div>

  </div>
</div>


<?php include 'includes/footer.php'; ?>