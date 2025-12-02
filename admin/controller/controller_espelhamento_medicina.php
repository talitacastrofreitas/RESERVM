<?php
session_start();
include '../../conexao/conexao.php';

set_time_limit(600); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        if (!isset($conn)) throw new Exception("Erro de conexão.");

        $conn->beginTransaction();

        // 1. CONFIGURAÇÕES
        $CURSO_MEDICINA = 14;
        $TIPO_RESERVA_FIXA = 2; 
        $admin_id = $_SESSION['reservm_admin_id'] ?? 1;

        $periodo_origem = $_POST['periodo_origem'];   
        $periodo_destino = $_POST['periodo_destino']; 
        
        // NOVOS CAMPOS
        $dt_inicio_custom = $_POST['data_inicio_custom'];
        $dt_fim_custom = $_POST['data_fim_custom'];
        $componentes_selecionados = $_POST['componentes_selecionados'] ?? []; 

        if($periodo_origem == $periodo_destino) throw new Exception("A origem e o destino não podem ser iguais.");
        if(empty($componentes_selecionados)) throw new Exception("Nenhum componente foi selecionado.");
        if(empty($dt_inicio_custom) || empty($dt_fim_custom)) throw new Exception("Datas de início e fim são obrigatórias.");

        // Função Datas
        function calcularDatasSemestre($textoPeriodo) {
            $partes = explode('.', $textoPeriodo);
            $ano = $partes[0]; $sem = $partes[1];
            // Ajuste conforme sua lógica antiga ou melhorada
            if ($sem == '1') { return ['ini' => $ano . '0101', 'fim' => $ano . '0630']; } 
            else { return ['ini' => $ano . '0701', 'fim' => $ano . '1231']; }
        }

        $datasOrigem = calcularDatasSemestre($periodo_origem);
        
        // DATAS DESTINO
        $datasDestino = [
            'ini' => str_replace('-', '', $dt_inicio_custom),
            'fim' => str_replace('-', '', $dt_fim_custom)
        ];

        // Feriados (Mantido igual)
        $sqlBloq = "SELECT REPLACE(dbloq_data, '-', '') 
                    FROM conf_dias_bloqueadas 
                    WHERE dbloq_status = 1 
                    AND (dbloq_cal_tipo = 1 OR dbloq_cal_tipo IS NULL) 
                    AND dbloq_data BETWEEN :ini AND :fim";     
        $stmtBloq = $conn->prepare($sqlBloq);
        $stmtBloq->execute([':ini' => $datasDestino['ini'], ':fim' => $datasDestino['fim']]);
        $feriados = $stmtBloq->fetchAll(PDO::FETCH_COLUMN);

        // ==========================================================
        // BUSCA SOLICITAÇÕES ORIGEM (FILTRANDO POR COMPONENTES SELECIONADOS)
        // ==========================================================
        
       
        $placeholders = implode(',', array_fill(0, count($componentes_selecionados), '?'));

        $sqlBuscaIds = "SELECT DISTINCT s.solic_id FROM solicitacao s
            INNER JOIN solicitacao_status ss ON ss.solic_sta_solic_id = s.solic_id
            INNER JOIN reservas r ON r.res_solic_id = s.solic_id
            WHERE s.solic_curso = ? 
              AND (s.solic_ap_tipo_reserva = ? OR s.solic_at_tipo_reserva = ?)
              AND ss.solic_sta_status NOT IN (1, 2, 3, 5, 6, 7, 8, 9) 
              AND r.res_data BETWEEN ? AND ?
              AND s.solic_comp_curric IN ($placeholders)"; // <--- FILTRO NOVO
        
        $stmtOrigem = $conn->prepare($sqlBuscaIds);
        
        // Monta o array de parametros na ordem exata dos ?
        $params = [
            $CURSO_MEDICINA, 
            $TIPO_RESERVA_FIXA, 
            $TIPO_RESERVA_FIXA, 
            $datasOrigem['ini'], 
            $datasOrigem['fim']
        ];
        // Adiciona os IDs dos componentes ao array de parametros
        $params = array_merge($params, $componentes_selecionados);

        $stmtOrigem->execute($params);
        $ids_solicitacoes = $stmtOrigem->fetchAll(PDO::FETCH_COLUMN);

        if (empty($ids_solicitacoes)) throw new Exception("Nenhuma solicitação encontrada na origem para os componentes selecionados.");

        $total_reservas_criadas = 0;

        // --- PROCESSAMENTO ---
        foreach ($ids_solicitacoes as $id_original) {
            
            $stmtDados = $conn->prepare("SELECT * FROM solicitacao WHERE solic_id = ?");
            $stmtDados->execute([$id_original]);
            $solic = $stmtDados->fetch(PDO::FETCH_ASSOC);
            if (!$solic) continue;

           
            $novo_dono_id = $solic['solic_cad_por']; 
            $novo_nome_professor = $solic['solic_nome_prof_resp']; 

            if (!empty($solic['solic_comp_curric'])) {
                $stmtComp = $conn->prepare("SELECT TOP 1 cp_colaborador_matricula FROM componente_professores WHERE cp_compc_id = ?");
                $stmtComp->execute([$solic['solic_comp_curric']]);
                $chapa_professor = $stmtComp->fetchColumn();

                if ($chapa_professor) {
                    $stmtUser = $conn->prepare("SELECT u.user_id, u.user_nome FROM usuarios u JOIN colaboradores c ON c.EMAIL = u.user_email WHERE c.CHAPA = ? AND u.user_status = 1");
                    $stmtUser->execute([$chapa_professor]);
                    $dados_novo_prof = $stmtUser->fetch(PDO::FETCH_ASSOC);
                    if ($dados_novo_prof) {
                        $novo_dono_id = $dados_novo_prof['user_id']; 
                        $novo_nome_professor = $dados_novo_prof['user_nome'];
                    }
                }
            }

            // GERAÇÃO DE CÓDIGO (Mantido)
            $novo_solic_id = bin2hex(random_bytes(16));
            do {
                $novo_cod = 'SO' . random_int(100000, 999999);
                $check = $conn->prepare("SELECT COUNT(*) FROM solicitacao WHERE solic_codigo = ?");
                $check->execute([$novo_cod]);
            } while ($check->fetchColumn() > 0);

            // DATAS HEADER 
            $ap_ini = ($solic['solic_ap_tipo_reserva'] == $TIPO_RESERVA_FIXA) ? $datasDestino['ini'] : null;
            $ap_fim = ($solic['solic_ap_tipo_reserva'] == $TIPO_RESERVA_FIXA) ? $datasDestino['fim'] : null;
            $at_ini = ($solic['solic_at_tipo_reserva'] == $TIPO_RESERVA_FIXA) ? $datasDestino['ini'] : null;
            $at_fim = ($solic['solic_at_tipo_reserva'] == $TIPO_RESERVA_FIXA) ? $datasDestino['fim'] : null;

            // INSERT SOLICITACAO 
            
             $sqlIns = "INSERT INTO solicitacao (
                solic_id, solic_codigo, solic_etapa, solic_curso, solic_comp_curric, 
                solic_nome_curso, solic_nome_curso_text, solic_nome_atividade, solic_nome_comp_ativ, 
                solic_semestre, solic_nome_prof_resp, solic_contato, 
                solic_cad_por, solic_data_cad, 
                solic_ap_campus, solic_ap_espaco, solic_at_campus, 
                solic_ap_aula_pratica, solic_at_aula_teorica,
                solic_ap_tipo_reserva, solic_at_tipo_reserva, 
                solic_ap_quant_particip, solic_at_quant_particip,
                solic_ap_data_inicio, solic_ap_data_fim, solic_at_data_inicio, solic_at_data_fim,
                solic_ap_quant_turma, solic_at_quant_sala,
                
                solic_ap_hora_inicio, solic_ap_hora_fim, 
                solic_at_hora_inicio, solic_at_hora_fim,
                solic_ap_tipo_material, solic_ap_tit_aulas, solic_ap_quant_material, solic_ap_obs,
                solic_at_recursos, solic_at_obs,
                solic_ap_dia_reserva, solic_at_dia_reserva

            ) VALUES (
                :id, :cod, 3, :curso, :comp, 
                :nm_c, :nm_ct, :nm_a, :nm_ca, 
                :sem, :prof, :cont, 
                :dono, GETDATE(), 
                :ap_camp, :ap_espaco, :at_camp, 
                :ap_aula, :at_aula,
                :ap_tp, :at_tp, 
                :ap_qtd, :at_qtd,
                :ap_di, :ap_df, :at_di, :at_df,
                :ap_turma, :at_sala,

                :ap_h_ini, :ap_h_fim,
                :at_h_ini, :at_h_fim,
                :ap_mat_type, :ap_tit, :ap_qtd_mat, :ap_obs,
                :at_rec, :at_obs,
                :ap_dias, :at_dias
            )";

            $conn->prepare($sqlIns)->execute([
                ':id' => $novo_solic_id, ':cod' => $novo_cod, 
                ':curso' => $solic['solic_curso'], ':comp' => $solic['solic_comp_curric'], 
                ':nm_c' => $solic['solic_nome_curso'], ':nm_ct' => $solic['solic_nome_curso_text'], 
                ':nm_a' => $solic['solic_nome_atividade'], ':nm_ca' => $solic['solic_nome_comp_ativ'], 
                ':sem' => $solic['solic_semestre'], 
                ':prof' => $novo_nome_professor, 
                ':cont' => $solic['solic_contato'], 
                ':dono' => $novo_dono_id, 
                ':ap_camp' => $solic['solic_ap_campus'], ':ap_espaco' => $solic['solic_ap_espaco'], 
                ':at_camp' => $solic['solic_at_campus'], 
                ':ap_aula' => $solic['solic_ap_aula_pratica'], ':at_aula' => $solic['solic_at_aula_teorica'], 
                ':ap_tp' => $TIPO_RESERVA_FIXA, ':at_tp' => $TIPO_RESERVA_FIXA, 
                ':ap_qtd' => $solic['solic_ap_quant_particip'], ':at_qtd' => $solic['solic_at_quant_particip'],
                ':ap_di' => $ap_ini, ':ap_df' => $ap_fim, ':at_di' => $at_ini, ':at_df' => $at_fim,
                ':ap_turma' => $solic['solic_ap_quant_turma'], ':at_sala'  => $solic['solic_at_quant_sala'],
                ':ap_h_ini' => $solic['solic_ap_hora_inicio'], ':ap_h_fim' => $solic['solic_ap_hora_fim'],
                ':at_h_ini' => $solic['solic_at_hora_inicio'], ':at_h_fim' => $solic['solic_at_hora_fim'],
                ':ap_mat_type' => $solic['solic_ap_tipo_material'], ':ap_tit' => $solic['solic_ap_tit_aulas'],
                ':ap_qtd_mat' => $solic['solic_ap_quant_material'], ':ap_obs' => $solic['solic_ap_obs'],
                ':at_rec' => $solic['solic_at_recursos'], ':at_obs' => $solic['solic_at_obs'],
                ':ap_dias' => $solic['solic_ap_dia_reserva'], ':at_dias' => $solic['solic_at_dia_reserva']
            ]);


            // STATUS 7 
            $conn->prepare("INSERT INTO solicitacao_status (solic_sta_solic_id, solic_sta_status, solic_sta_user_id, solic_sta_data_cad) VALUES (?, 7, ?, GETDATE())")->execute([$novo_solic_id, $admin_id]);
            $conn->prepare("INSERT INTO solicitacao_analise_status (sta_an_solic_id, sta_an_status, sta_an_user_id, sta_an_data_cad, sta_an_data_upd, sta_an_obs) VALUES (?, 7, ?, GETDATE(), GETDATE(), ?)")->execute([$novo_solic_id, $admin_id, "Espelhamento Custom ($periodo_origem -> $periodo_destino)"]);
            
             $sqlHist = "INSERT INTO espelhamento_historico 
                        (eh_solic_origem_id, eh_solic_destino_id, eh_periodo_origem, eh_periodo_destino, eh_realizado_por, eh_data_cadastro) 
                        VALUES (:origem, :destino, :p_orig, :p_dest, :user, GETDATE())";
            $stmtHist = $conn->prepare($sqlHist);
            $stmtHist->execute([':origem' => $id_original, ':destino' => $novo_solic_id, ':p_orig' => $periodo_origem, ':p_dest' => $periodo_destino, ':user' => $admin_id]);


            // REPLICAÇÃO DE RESERVAS
            $sqlRegras = "SELECT DISTINCT res_dia_semana, res_tipo_aula, LEFT(CAST(res_hora_inicio AS VARCHAR), 5) as res_hora_inicio, LEFT(CAST(res_hora_fim AS VARCHAR), 5) as res_hora_fim, res_turno, res_espaco_id, res_campus, res_componente_atividade, res_professor, res_titulo_aula, res_recursos, CAST(res_recursos_add AS VARCHAR(MAX)) as res_recursos_add, CAST(res_obs AS VARCHAR(MAX)) as res_obs, res_quant_pessoas, res_modulo, res_nome_atividade FROM reservas WHERE res_solic_id = :sid AND res_tipo_reserva = :fixa";
            $stmtRegras = $conn->prepare($sqlRegras);
            $stmtRegras->execute([':sid' => $solic['solic_id'], ':fixa' => $TIPO_RESERVA_FIXA]);

            while($regra = $stmtRegras->fetch(PDO::FETCH_ASSOC)) {
                $dia_semana_alvo = $regra['res_dia_semana']; 
                // CRIAÇÃO DO INTERVALO DE DATAS COM AS NOVAS DATAS CUSTOMIZADAS
                $dt_ini_obj = DateTime::createFromFormat('Ymd', $datasDestino['ini']);
                $dt_fim_obj = DateTime::createFromFormat('Ymd', $datasDestino['fim']);
                
                // Validação de segurança para não rodar infinito se data vier errada
                if(!$dt_ini_obj || !$dt_fim_obj) continue;

                $periodo = new DatePeriod($dt_ini_obj, new DateInterval('P1D'), $dt_fim_obj->modify('+1 day'));

                foreach ($periodo as $dt) {
                    $data_banco = $dt->format('Ymd');
                    $dia_semana_corrente = $dt->format('N'); 

                    if ($dia_semana_corrente == $dia_semana_alvo && !in_array($data_banco, $feriados)) {
                        $novo_res_id = bin2hex(random_bytes(16)); 
                        $novo_res_cod = 'RE' . random_int(100000, 999999);
                        $mes_pt = [1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril', 5=>'Maio', 6=>'Junho', 7=>'Julho', 8=>'Agosto', 9=>'Setembro', 10=>'Outubro', 11=>'Novembro', 12=>'Dezembro'];
                        $mes_texto = $mes_pt[(int)$dt->format('m')];
                        $ano_atual = $dt->format('Y');

                        $sqlInsRes = "INSERT INTO reservas (res_id, res_solic_id, res_codigo, res_tipo_aula, res_curso, res_semestre, res_componente_atividade, res_nome_atividade, res_modulo, res_professor, res_titulo_aula, res_recursos, res_recursos_add, res_obs, res_quant_pessoas, res_tipo_reserva, res_espaco_id, res_campus, res_data, res_dia_semana, res_data_inicio_semanal, res_data_fim_semanal, res_hora_inicio, res_hora_fim, res_turno, res_user_id, res_data_cad, res_mes, res_ano) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), ?, ?)";

                        $conn->prepare($sqlInsRes)->execute([
                            $novo_res_id, $novo_solic_id, $novo_res_cod, $regra['res_tipo_aula'], $CURSO_MEDICINA, $solic['solic_semestre'], $regra['res_componente_atividade'], $regra['res_nome_atividade'], $regra['res_modulo'], 
                            $novo_nome_professor, 
                            $regra['res_titulo_aula'], $regra['res_recursos'], $regra['res_recursos_add'], $regra['res_obs'], $regra['res_quant_pessoas'], $TIPO_RESERVA_FIXA, $regra['res_espaco_id'], $regra['res_campus'], $data_banco, $dia_semana_corrente, $data_banco, $data_banco, 
                            $regra['res_hora_inicio'], $regra['res_hora_fim'], 
                            $regra['res_turno'], 
                            $admin_id, 
                            $mes_texto, $ano_atual
                        ]);
                        $total_reservas_criadas++;
                    }
                }
            }
        }

        $conn->commit();
        $_SESSION["msg"] = "Sucesso! $total_reservas_criadas aulas geradas.";
        header("Location: ../espelhamento.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION["erro"] = "Erro: " . $e->getMessage();
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>