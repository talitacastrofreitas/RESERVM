<?php include 'includes/header.php'; ?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Reservas Canceladas</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Painel</a></li>
          <li class="breadcrumb-item active">Reservas Canceladas</li>
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
            <h5 class="card-title mb-0">Lista de Reservas Canceladas</h5>
          </div>
          <div class="col-sm-4 d-flex align-items-center d-flex justify-content-sm-end justify-content-center">
            <a href="../relatorios/reservas.php" class="btn botao botao_roxo waves-effect mt-3 mt-sm-0" style="padding: 7.5px 13px !important">CSV</a>
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
                <th nowrap="nowrap"><span class="me-3">Data Solicitação</span></th>
                <th nowrap="nowrap"><span class="me-3">Data Reserva</span></th>
                <th nowrap="nowrap"><span class="me-3">ID Solicitação</span></th>
                <th nowrap="nowrap"><span class="me-3">Motivo do cancelamento</span></th>
                <th nowrap="nowrap"><span class="me-3">Cancelado por</span></th>
                <th nowrap="nowrap"><span class="me-3">CH Programada</span></th>
              </tr>
            </thead>
            <tbody>
              <?php
              try {
                $reservas_analisadas = [];

                function calcularDiferencaHoras($inicio, $fim)
                {
                  if (!$inicio || !$fim) return null;
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

                $stmt = $conn->prepare("SELECT
                                  reservas.*,
                                  solicitacao.solic_codigo, solicitacao.solic_data_cad,
                                  solicitacao_status.solic_motivo_cancelamento, solicitacao_status.solic_sta_user_id,
                                  conf_dias_semana.week_dias,
                                  cursos.curs_curso,
                                  conf_semestre.cs_id, conf_semestre.cs_semestre,
                                  componente_curricular.compc_componente,
                                  conf_tipo_reserva.ctr_tipo_reserva,
                                  conf_tipo_aula.cta_tipo_aula,
                                  espaco.esp_codigo, espaco.esp_nome_local, espaco.esp_quant_maxima,
                                  tipo_espaco.tipesp_tipo_espaco,
                                  pavilhoes.pav_pavilhao,
                                  andares.and_andar,
                                  unidades.uni_unidade,
                                  recursos.rec_recurso,
                                  admin_solic.admin_nome,
                                  admin_cancel.admin_nome AS nome_cancelado,
                                  ISNULL(reservas.res_motivo_cancelamento, solicitacao_status.solic_motivo_cancelamento) AS motivo_final,
                                  ISNULL(reservas.res_user_id, solicitacao_status.solic_sta_user_id) AS user_id_final
                                FROM reservas
                                LEFT JOIN solicitacao ON solicitacao.solic_id = reservas.res_solic_id
                                LEFT JOIN solicitacao_status ON solicitacao_status.solic_sta_solic_id = solicitacao.solic_id
                                LEFT JOIN conf_dias_semana ON conf_dias_semana.week_id = reservas.res_dia_semana
                                LEFT JOIN cursos ON cursos.curs_id = reservas.res_curso
                                LEFT JOIN conf_semestre ON conf_semestre.cs_id = reservas.res_semestre
                                LEFT JOIN componente_curricular ON componente_curricular.compc_id = reservas.res_componente_atividade
                                LEFT JOIN conf_tipo_reserva ON conf_tipo_reserva.ctr_id = reservas.res_tipo_reserva
                                LEFT JOIN conf_tipo_aula ON conf_tipo_aula.cta_id = reservas.res_tipo_aula
                                LEFT JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
                                LEFT JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
                                LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
                                LEFT JOIN andares ON andares.and_id = espaco.esp_andar
                                LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
                                LEFT JOIN recursos ON ',' + ISNULL(reservas.res_recursos_add, '') + ',' LIKE '%,' + CAST(recursos.rec_id AS VARCHAR) + ',%'
                                LEFT JOIN admin AS admin_solic ON admin_solic.admin_id = solicitacao.solic_cad_por
                                LEFT JOIN admin AS admin_cancel ON admin_cancel.admin_id = ISNULL(reservas.res_user_id, solicitacao_status.solic_sta_user_id)
                                WHERE reservas.res_status = 8 OR solicitacao_status.solic_sta_status = 8");

                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $solic_id = $row['res_solic_id'] ?? null;
                  $solic_codigo = $row['solic_codigo'] ?? null;
                  $res_id = $row['res_id'] ?? null;
                  $res_espaco_id = $row['res_espaco_id'] ?? null;
                  $res_data = $row['res_data'] ?? null;
                  $week_dias = $row['week_dias'] ?? null;
                  $res_mes = $row['res_mes'] ?? null;
                  $res_ano = $row['res_ano'] ?? null;
                  $res_hora_inicio = $row['res_hora_inicio'] ?? null;
                  $res_hora_fim = $row['res_hora_fim'] ?? null;
                  $res_turno = $row['res_turno'] ?? null;
                  $res_tipo_aula = $row['res_tipo_aula'] ?? null;
                  $res_tipo_reserva = $row['res_tipo_reserva'] ?? null;
                  $res_recursos = $row['res_recursos'] ?? null;
                  $res_recursos_add = $row['res_recursos_add'] ?? null;
                  $res_codigo = $row['res_codigo'] ?? null;
                  $cta_tipo_aula = $row['cta_tipo_aula'] ?? null;
                  $curs_curso = $row['curs_curso'] ?? null;
                  $cs_semestre = $row['cs_semestre'] ?? null;
                  $res_componente_atividade = $row['res_componente_atividade'] ?? null;
                  $compc_componente = $row['compc_componente'] ?? null;
                  $res_componente_atividade_nome = $row['res_componente_atividade_nome'] ?? null;
                  $res_nome_atividade = $row['res_nome_atividade'] ?? null;
                  $res_modulo = $row['res_modulo'] ?? null;
                  $res_professor = $row['res_professor'] ?? null;
                  $res_titulo_aula = $row['res_titulo_aula'] ?? null;
                  $res_obs = $row['res_obs'] ?? null;
                  $res_quant_pessoas = $row['res_quant_pessoas'] ?? null;
                  $ctr_tipo_reserva = $row['ctr_tipo_reserva'] ?? null;
                  $esp_codigo = $row['esp_codigo'] ?? null;
                  $esp_nome_local = $row['esp_nome_local'] ?? null;
                  $and_andar = $row['and_andar'] ?? null;
                  $pav_pavilhao = $row['pav_pavilhao'] ?? null;
                  $uni_unidade = $row['uni_unidade'] ?? null;
                  $tipesp_tipo_espaco = $row['tipesp_tipo_espaco'] ?? null;
                  $esp_quant_maxima = $row['esp_quant_maxima'] ?? null;
                  $admin_nome = $row['admin_nome'] ?? null;
                  $solic_data_cad = $row['solic_data_cad'] ?? null;
                  $res_data_cad = $row['res_data_cad'] ?? null;
                  $cre_motivo = $row['motivo_final'] ?? null;
                  $nome_cancelado = $row['nome_cancelado'] ?? null;

                  ////////////////////////////////////////////
                  // TRATA OS DADOS DOS RECURSOS ADICIONAIS //
                  ////////////////////////////////////////////
                  $res_recursos_ids = trim($res_recursos_add ?? '');
                  $res_recursos_ids = rtrim($res_recursos_ids, ',');
                  $recursos_formatados = '';
                  if (!empty($res_recursos_ids)) {
                    $ids_array = array_filter(array_map('trim', explode(',', $res_recursos_ids)), 'ctype_digit');
                    if (count($ids_array) > 0) {
                      $res_recursos_ids_sql = implode(',', $ids_array);
                      $sql_recursos = "SELECT rec_recurso FROM recursos WHERE rec_id IN ($res_recursos_ids_sql)";
                      $stmt_rec = $conn->prepare($sql_recursos);
                      $stmt_rec->execute();
                      $recursos = $stmt_rec->fetchAll(PDO::FETCH_COLUMN);
                      $recursos_formatados = implode(' / ', $recursos);
                    }
                  }
                  ///////////////////////
                  // FIM DO TRATAMENTO //
                  ///////////////////////
                  $conflito_class = ''; // Lógica de conflito pode ser removida ou adaptada, pois não se aplica a registros de solicitações sem reserva

                  $tipo_aula_color = ($res_tipo_aula == 1) ? 'bg_info_laranja' : 'bg_info_azul';
                  $tipo_reserva_color = ($res_tipo_reserva == 1) ? 'bg_info_roxo' : 'bg_info_azul_escuro';
                  $recursos_color = ($res_recursos == 'SIM') ? 'bg_info_verde' : 'bg_info_vermelho';

                  $ch_programada = ($res_hora_inicio && $res_hora_fim) ? calcularDiferencaHoras($res_hora_inicio, $res_hora_fim) : null;
                  $componente = '';
                  if (!empty($res_componente_atividade)) {
                    $componente = $compc_componente;
                  } else if (!empty($res_componente_atividade_nome)) {
                    $componente = $res_componente_atividade_nome;
                  } else if (!empty($res_nome_atividade)) {
                    $componente = $res_nome_atividade;
                  }
              ?>
                  <tr role="button" data-href='solicitacao_analise.php?i=<?= htmlspecialchars($solic_id) ?>'>
                    <th scope="row" nowrap="nowrap" class="bg_table_fix_vermelho <?= $conflito_class ? 'bg_table_fix_vermelho_escuro' : '' ?>"><span class="hide_data"><?= ($res_data) ? date('Ymd', strtotime($res_data)) : '' ?></span><?= ($res_data) ? htmlspecialchars(date('d/m/Y', strtotime($res_data))) : '---' ?></th>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_laranja <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_laranja_escuro' : '' ?>"><?= htmlspecialchars($week_dias ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_verde <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_verde_escuro' : '' ?>"><?= htmlspecialchars($res_mes ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_azul <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_azul_escuro' : '' ?>"><?= htmlspecialchars($res_ano ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_roxo <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_roxo_escuro' : '' ?>"><?= ($res_hora_inicio) ? htmlspecialchars(date("H:i", strtotime($res_hora_inicio))) : '---' ?></td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_rosa <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_rosa_escuro' : '' ?>"><?= ($res_hora_fim) ? htmlspecialchars(date("H:i", strtotime($res_hora_fim))) : '---' ?></td>
                    <td scope="row" nowrap="nowrap" class="bg_table_fix_cinza <?= $conflito_class ?> <?= $conflito_class ? 'bg_table_fix_cinza_escuro' : '' ?>"><?= htmlspecialchars($res_turno ?? '---') ?></td>
                    <th scope="row" nowrap="nowrap"><?= htmlspecialchars($res_codigo ?? '---') ?></th>
                    <td scope="row" nowrap="nowrap"><span class="badge <?= $tipo_aula_color ?>"><?= htmlspecialchars($cta_tipo_aula ?? '---') ?></span></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($curs_curso ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($cs_semestre ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($componente ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_modulo ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_professor ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_titulo_aula ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><span class="badge <?= $recursos_color ?>"><?= htmlspecialchars($res_recursos ?? '---') ?></span></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($recursos_formatados) ?></td>
                    <td scope="row">
                      <?php if ($res_obs) { ?>
                        <button type="button" class="btn btn_soft_azul_escuro btn-sm" data-bs-toggle="modal" data-bs-target="#modal_obs<?= $res_id ?>"><i class="fa-regular fa-comment-dots"></i></button>
                        <div id="modal_obs<?= $res_id ?>" class="modal zoomIn fade" tabindex="-1" aria-labelledby="ModalObsLabel" aria-hidden="true" style="display: none;">
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
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($res_quant_pessoas ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><span class="badge <?= $tipo_reserva_color ?>"><?= htmlspecialchars($ctr_tipo_reserva ?? '---') ?></span></td>
                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($esp_codigo ?? '---') ?></strong></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($esp_nome_local ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($and_andar ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($pav_pavilhao ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($uni_unidade ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($tipesp_tipo_espaco ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($esp_quant_maxima ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><span class="hide_data"><?= ($solic_data_cad) ? date('Ymd', strtotime($solic_data_cad)) : '' ?></span><?= ($solic_data_cad) ? htmlspecialchars(date('d/m/Y', strtotime($solic_data_cad))) : '---' ?></td>
                    <td scope="row" nowrap="nowrap"><span class="hide_data"><?= ($res_data_cad) ? date('Ymd', strtotime($res_data_cad)) : '' ?></span><?= ($res_data_cad) ? htmlspecialchars(date('d/m/Y', strtotime($res_data_cad))) : '---' ?></td>
                    <td scope="row" nowrap="nowrap"><strong><?= htmlspecialchars($solic_codigo ?? '---') ?></strong></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($cre_motivo ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($nome_cancelado ?? '---') ?></td>
                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($ch_programada ?? '---') ?></td>
                  </tr>
              <?php }
              } catch (PDOException $e) {
                echo "Erro ao tentar recuperar os dados" . $e->getMessage();
              } ?>
            </tbody>
          </table>
        </div>

        <script>
          $(document).ready(function() {
            $('table').on('click', 'tr', function(e) {
              if (
                $(e.target).closest('.btn').length > 0 ||
                $(e.target).closest('.btn-close').length > 0 ||
                $(e.target).closest('.modal').length > 0
              ) {
                return;
              }
              const href = $(this).data('href');
              if (href) {
                window.location.href = href;
              }
            });

            $(document).on('click', '.btn, .btn-close', function(e) {
              e.stopPropagation();
            });

            $(document).on('click', '.modal, .modal *', function(e) {
              e.stopPropagation();
            });
          });
        </script>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>