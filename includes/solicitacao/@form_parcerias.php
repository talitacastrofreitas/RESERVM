<style>
  .card_color_azul {
    background: var(--azul_alpha);

    & h5 {
      text-transform: uppercase;
      margin-right: 10px;
    }

    & span {
      background: var(--azul);
      padding: 4px 10px;
      font-size: 12px;
      border-radius: 3px;
      text-transform: uppercase;
      color: #fff;
    }
  }

  .card_color_roxo {
    background: var(--roxo_alpha);

    & h5 {
      text-transform: uppercase;
      margin-right: 10px;
    }

    & span {
      background: var(--roxo);
      padding: 4px 10px;
      font-size: 12px;
      border-radius: 3px;
      text-transform: uppercase;
      color: #fff;
    }
  }

  .form_solicitacao {}

  .form_solicitacao .form-label {
    font-size: .875rem !important;
    margin: 0;
  }

  .form_solicitacao .form_margem {
    margin-bottom: 30px;
  }

  .form_solicitacao .label_info {
    margin: 5px 0 0 0;
    font-size: 0.813rem;
    font-style: normal;
  }

  .form_solicitacao .form-select,
  .form_solicitacao .form-control,
  .form_solicitacao .select2-selection {
    margin: 8px 0 10px 0;
  }

  .form_solicitacao .check_container {
    margin: 20px 0 40px 0;
  }

  .form_solicitacao .form-check-label {
    font-size: 0.875rem;
  }
</style>

<form class="needs-validation meuFormulario form_solicitacao" method="POST" action="cad_proposta.php?tp=NA==" id="ValidaBotaoProgressPadrao" enctype="multipart/form-data" autocomplete="off" novalidate>

  <div class="card">
    <div class="card-header px-sm-4 px-3">
      <div class="row align-items-center">
        <div class="col-sm-12">
          <h5 class="card-title mb-0">IDENTIFICAÇÃO</h5>
        </div>
      </div>
    </div>

    <div class="card-body p-sm-4 p-3">
      <div class="row grid gx-3">

        <div class="col-12">
          <div class="form_margem">
            <label class="form-label">Tipo de Atividade <span>*</span></label>
            <select class="form-select text-uppercase" name="cad_iden_tipo_atividade" id="cad_iden_tipo_atividade" required>
              <option selected disabled value=""></option>
              <option value="1">ATIVIDADE ACADÊMICA</option>
              <option value="2">ATIVIDADE ADMINISTRATIVA</option>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12" id="campo_ident_curso" style="display: none;">
          <div class="form_margem">
            <?php try {
              $sql = $conn->query("SELECT curs_id, curs_curso FROM cursos WHERE curs_status != 0 ORDER BY curs_curso");
              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar os dados";
            } ?>
            <label class="form-label">Curso <span>*</span></label>
            <select class="form-select text-uppercase" name="cad_iden_curso" id="cad_iden_curso">
              <option selected disabled value=""></option>
              <?php foreach ($result as $res) : ?>
                <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12" id="campo_ident_comp_curricular" style="display: none;">
          <div class="form_margem">
            <?php try {
              $sql = $conn->query("SELECT curs_id, curs_curso FROM cursos ORDER BY curs_curso");
              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar os dados";
            } ?>
            <label class="form-label mb-0">Componente Curricular <span>*</span></label>
            <div class="label_info mb-2 mt-0">Os componentes curriculares estão ordenados por semestre e ordem alfabética.</div>
            <select class="form-select text-uppercase" name="cad_iden_comp_curricular" id="cad_iden_comp_curricular">
              <option selected disabled value=""></option>
              <option value="0">OUTRO</option>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12" id="campo_ident_select_nome_curso" style="display: none;">
          <div class="form_margem">
            <?php try {
              $sql = $conn->query("SELECT curs_id, curs_curso FROM cursos WHERE curs_id IN (1,5,6,9,14,18,21)  ORDER BY curs_curso");
              $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
              // echo "Erro: " . $e->getMessage();
              echo "Erro ao tentar recuperar os dados";
            } ?>
            <label class="form-label">Nome do Curso <span>*</span></label>
            <select class="form-select text-uppercase" name="cad_iden_select_nome_curso" id="cad_iden_select_nome_curso">
              <option selected disabled value=""></option>
              <?php foreach ($result as $res) : ?>
                <option value="<?= $res['curs_id'] ?>"><?= $res['curs_curso'] ?></option>
              <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12" id="campo_ident_nome_curso" style="display: none;">
          <div class="form_margem">
            <label class="form-label">Nome do Curso <span>*</span></label>
            <input type="text" class="form-control text-uppercase" name="cad_iden_nome_curso" id="cad_iden_nome_curso" maxlength="100">
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12" id="campo_ident_nome_atividade" style="display: none;">
          <div class="form_margem">
            <label class="form-label">Nome da Atividade <span>*</span></label>
            <input type="text" class="form-control text-uppercase" name="cad_iden_nome_ativ" id="cad_iden_nome_ativ" maxlength="100">
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-12" id="campo_ident_nome_comp_ativ" style="display: none;">
          <div class="form_margem">
            <label class="form-label">Nome do Componente/Atividade <span>*</span></label>
            <input type="text" class="form-control text-uppercase" name="cad_iden_nome_comp_ativ" id="cad_iden_nome_comp_ativ" maxlength="100">
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-md-6" id="campo_ident_semestre" style="display: none;">
          <div class="form_margem">
            <label class="form-label">Semestre <span>*</span></label>
            <select class="form-select text-uppercase" name="cad_iden_semestre" id="cad_iden_semestre">
              <option selected disabled value=""></option>
              <option value="1">1º SEMESTRE</option>
              <option value="2">2º SEMESTRE</option>
              <option value="3">3º SEMESTRE</option>
            </select>
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <!-- <div class="col-md-6" id="campo_ident_nome_prof_resp" style="display: none;">
            <div class="mb-3">
              <label class="form-label">Nome do professor/responsável <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="" maxlength="100">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div> -->

        <div class="col-12" id="campo_ident_nome_ativ_comp" style="display: none;">
          <div class="form_margem">
            <label class="form-label">Nome da Atividade/Componente <span>*</span></label>
            <input type="text" class="form-control text-uppercase" name="" id="cad_iden_nome_atv_comp" maxlength="100">
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-md-6" id="campo_ident_prof" style="display: none;">
          <div class="form_margem">
            <label class="form-label">Nome do Professor/Responsável <span>*</span></label>
            <input type="text" class="form-control text-uppercase" name="" id="cad_iden_nome_prof" maxlength="100">
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <div class="col-md-6" id="campo_ident_tel" style="display: none;">
          <div class="form_margem">
            <label class="form-label">Telefone para contato <span>*</span></label>
            <input type="text" class="form-control cel_tel" name="" id="cad_iden_contato">
            <div class="invalid-feedback">Este campo é obrigatório</div>
          </div>
        </div>

        <!-- <div class="col-md-6" id="" style="display: none;">
            <div class="mb-3">
              <label class="form-label">Professor Responsável <span>*</span></label>
              <input type="text" class="form-control text-uppercase" name="">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div> -->

        <!-- <div class="col-md-6" id="" style="display: none;">
            <div class="mb-3">
              <label class="form-label">Telefone para contato <span>*</span></label>
              <input type="text" class="form-control cel_tel" name="">
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div> -->

      </div>

    </div>

  </div>

  <div class="card">
    <div class="card-header card_color_azul px-sm-4 px-3">
      <div class="row align-items-center">
        <div class="col-sm-12">
          <h5 class="card-title d-inline-block mb-2 mb-sm-0">Informações da Reserva</h5>
          <span>Aulas Práticas</span>
        </div>
      </div>
    </div>

    <div class="card-body p-sm-4 p-3">
      <div class="row grid gx-3">

        <div class="col-12">
          <label class="form-label">Deseja realizar a solicitação de reserva de espaços para aulas práticas? <span>*</span></label>
          <div class="label_info">Os espaços de ensino para atividades práticas são: Laboratórios de Ensino, Clínica de Fisioterapia e Espaços Externos. </div>

          <div class="check_container">
            <div class="form-check form_solicita">
              <input class="form-check-input form_solicita" type="radio" name="solic_esp_pratico" id="solic_esp_pratico_sim" value="1" required>
              <label class="form-check-label" for="solic_esp_pratico_sim">Sim</label>
            </div>

            <div class="form-check form_solicita">
              <input class="form-check-input form_solicita" type="radio" name="solic_esp_pratico" id="solic_esp_pratico_nao" value="0" required>
              <label class="form-check-label" for="solic_esp_pratico_nao">Não</label>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>
        </div>

        <div id="campo_info_pratic_campus" style="display: none;">

          <div class="col-sm-6">
            <div class="form_margem">
              <?php try {
                $sql = $conn->query("SELECT uni_id, uni_unidade FROM unidades ORDER BY uni_unidade ASC");
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) {
                // echo "Erro: " . $e->getMessage();
                echo "Erro ao tentar recuperar os dados";
              } ?>
              <label class="form-label">Campus <span>*</span></label>
              <select class="form-select text-uppercase" name="" id="cad_info_pratic_campus">
                <option selected disabled value=""></option>
                <?php foreach ($result as $res) : ?>
                  <option value="<?= $res['uni_id'] ?>"><?= $res['uni_unidade'] ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>
          </div>

          <div id="campo_info_pratic_espaco" style="display: none;">

            <div class="col-12" id="campo_info_pratic_espaco_brotas" style="display: none;">
              <div class="form_margem">
                <label class="form-label">Espaço sugerido <span>*</span></label>
                <div class="label_info">
                  • A alocação de alunos depende do cenário escolhido, o qual varia conforme organização de mesas,
                  cadeiras, bancadas e macas.<br>
                  • Caso seja necessário, o solicitante pode selecionar mais de um espaço para realizar a prática.</div>
                <div class="check_container">

                  <?php try {
                    $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local, esp_nome_local_resumido FROM espaco WHERE esp_unidade = 2");
                    $sql->execute();
                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                  } catch (PDOException $e) {
                    // echo "Erro: " . $e->getMessage();
                    echo "Erro ao tentar recuperar os dados";
                  } ?>
                  <?php foreach ($result as $res) : ?>
                    <div class="form-check form_solicita">
                      <input class="form-check-input form_solicita me-2" type="checkbox" name="cad_info_pratic_espaco_brotas[]" id="formCheck<?= $res['esp_id'] ?>">
                      <label class="form-check-label" for="formCheck<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></label>
                    </div>
                  <?php endforeach; ?>
                  <div id="msgCheckInfoPraticEspacoBrotas" class="invalid-feedback">Selecione pelo menos uma opção</div>
                </div>
              </div>
            </div>

            <div class="col-12" id="campo_info_pratic_espaco_cabula" style="display: none;">
              <div class="form_margem">
                <label class="form-label">Espaço sugerido <span>*</span></label>
                <div class="label_info">
                  • A alocação de alunos depende do cenário escolhido, o qual varia conforme organização de mesas,
                  cadeiras, bancadas e macas.<br>
                  • Caso seja necessário, o solicitante pode selecionar mais de um espaço para realizar a prática.</div>

                <div class="check_container">
                  <?php try {
                    $sql = $conn->prepare("SELECT esp_id, esp_codigo, esp_nome_local, esp_nome_local_resumido FROM espaco WHERE esp_unidade = 1");
                    $sql->execute();
                    $result = $sql->fetchAll(PDO::FETCH_ASSOC);
                  } catch (PDOException $e) {
                    // echo "Erro: " . $e->getMessage();
                    echo "Erro ao tentar recuperar os dados";
                  } ?>
                  <?php foreach ($result as $res) : ?>
                    <div class="form-check form_solicita mb-3">
                      <input class="form-check-input form_solicita me-2" type="checkbox" name="cad_info_pratic_espaco_cabula[]" id="formCheck<?= $res['esp_id'] ?>">
                      <label class="form-check-label" for="formCheck<?= $res['esp_id'] ?>"><?= $res['esp_codigo'] . ' - ' . $res['esp_nome_local'] ?></label>
                    </div>
                  <?php endforeach; ?>
                  <div id="msgCheckInfoPraticEspacoCabula" class="invalid-feedback">Selecione pelo menos uma opção</div>
                </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form_margem">
                <label class="form-label">Quantidade de turmas <span>*</span></label>
                <select class="form-select text-uppercase" name="cad_info_pratic_quant_turma" id="cad_info_pratic_quant_turma">
                  <option selected></option>
                  <option value="1">01 turma</option>
                  <option value="2">02 turmas</option>
                  <option value="3">03 turmas</option>
                  <option value="4">04 turmas</option>
                  <option value="5">05 turmas</option>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

          </div>

          <div id="campo_info_pratic_tipo_reserva" style="display: none;">

            <div class="col-sm-6">
              <div class="form_margem">
                <label class="form-label">Número estimado de participantes <span>*</span></label>
                <input type="text" class="form-control text-uppercase" name="cad_info_pratic_num_partic" id="cad_info_pratic_num_partic" maxlength="100">
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Tipo da reserva <span>*</span></label>

              <div class="check_container">
                <div class="form-check form_solicita">
                  <input type="radio" class="form-check-input form_solicita" id="cad_info_pratic_tipo_reserva1" name="cad_info_pratic_tipo_reserva" value="1">
                  <label class="form-check-label" for="cad_info_pratic_tipo_reserva1">Esporádica - Reserva em data(s) específica(s).</label>
                </div>
                <div class="form-check form_solicita">
                  <input type="radio" class="form-check-input form_solicita" id="cad_info_pratic_tipo_reserva2" name="cad_info_pratic_tipo_reserva" value="2">
                  <label class="form-check-label" for="cad_info_pratic_tipo_reserva2">Fixa - Reserva permanente em determinado(s) dia(s) da semana, durante todo o semestre de acordo com o calendário acadêmico.</label>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            </div>

          </div>

          <div id="campo_info_pratic_data_reserva" style="display: none;">

            <div class="col-12" id="campo_info_pratic_dias_semana" style="display: none;">
              <div class="form_margem">
                <label class="form-label">Dia(s) da semana <span>*</span></label>
                <div class="label_info">Caso seu componente seja encerrado antes da última semana de finalização das aulas pelo calendário acadêmico, ou haja alguma exceção para alguma data do(s) dia(s) da semana selecionado, favor descrever no campo observação, para que a reserva não seja efetivada.</div>

                <div class="check_container">
                  <div class="form-check form_solicita">
                    <input class="form-check-input form_solicita me-2" type="checkbox" name="cad_info_pratic_dias_semana[]" id="formCheck10">
                    <label class="form-check-label" for="formCheck10">Segunda-feira</label>
                  </div>

                  <div class="form-check form_solicita">
                    <input class="form-check-input form_solicita me-2" type="checkbox" name="cad_info_pratic_dias_semana[]" id="formCheck11">
                    <label class="form-check-label" for="formCheck11">Terça-feira</label>
                  </div>

                  <div class="form-check form_solicita">
                    <input class="form-check-input form_solicita me-2" type="checkbox" name="cad_info_pratic_dias_semana[]" id="formCheck12">
                    <label class="form-check-label" for="formCheck12">Quarta-feira</label>
                  </div>

                  <div class="form-check form_solicita">
                    <input class="form-check-input form_solicita me-2" type="checkbox" name="cad_info_pratic_dias_semana[]" id="formCheck13">
                    <label class="form-check-label" for="formCheck13">Quinta-feira</label>
                  </div>

                  <div class="form-check form_solicita">
                    <input class="form-check-input form_solicita me-2" type="checkbox" name="cad_info_pratic_dias_semana[]" id="formCheck14">
                    <label class="form-check-label" for="formCheck14">Sexta-feira</label>
                  </div>

                  <div class="form-check form_solicita">
                    <input class="form-check-input form_solicita me-2" type="checkbox" name="cad_info_pratic_dias_semana[]" id="formCheck15">
                    <label class="form-check-label" for="formCheck15">Sábado</label>
                  </div>
                  <div id="msgCheckInfoPraticDiasSemana" class="invalid-feedback">
                    Selecione pelo menos uma opção
                  </div>

                </div>
              </div>
            </div>

            <div class="col-12" id="campo_info_pratic_datas" style="display: none;">
              <div class="form_margem">
                <label class="form-label">Data(s) da reserva <span>*</span></label>
                <textarea class="form-control" name="" id="cad_info_pratic_datas" rows="3"></textarea>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 col-xl-4">
                <div class="form_margem">
                  <label class="form-label">Horário inicial <span>*</span></label>
                  <input type="time" class="form-control" name="" id="cad_info_pratic_hora_inicial">
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>

              <div class="col-md-6 col-xl-4">
                <div class="form_margem">
                  <label class="form-label">Horário final <span>*</span></label>
                  <input type="time" class="form-control" name="" id="cad_info_pratic_hora_final">
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Selecione como deseja informar quais serão os materiais, equipamentos e insumos necessários para a realização da aula nos espaços de prática <span>*</span></label>

              <div class="check_container">
                <div class="form-check form_solicita">
                  <input type="radio" class="form-check-input form_solicita" id="validationFormCheck2" name="cad_info_pratic_material" value="1">
                  <label class="form-check-label" for="validationFormCheck2">Anexar o formulário de planejamento de atividades de práticas nos laboratórios de ensino.</label>
                </div>
                <div class="form-check form_solicita">
                  <input type="radio" class="form-check-input form_solicita" id="validationFormCheck3" name="cad_info_pratic_material" value="2">
                  <label class="form-check-label" for="validationFormCheck3">Informar o título da aula (caso o formulário já esteja no banco de dados do laboratório de ensino).</label>
                </div>
                <div class="form-check form_solicita">
                  <input type="radio" class="form-check-input form_solicita" id="validationFormCheck4" name="cad_info_pratic_material" value="3">
                  <label class="form-check-label" for="validationFormCheck4">Descrevê-los com as respectivas quantidades.</label>
                  <!-- <div id="msgCheckInfoPraticMateriais" class="invalid-feedback">Este campo é obrigatório</div> -->
                  <div id="error-message" class="invalid-feedback" style="display: none;">Este campo é obrigatório</div>
                </div>
              </div>
            </div>

          </div>

          <div id="campo_info_pratic_anexar" style="display: none;">

            <div class="col-12">
              <div class="form_margem">
                <label class="form-label m-0">Formulário de planejamento de atividades de práticas nos laboratórios de ensino</label>
                <label class="label_info">
                  Anexe aqui o(s) Formulário(s) de Planejamento de Atividades de Práticas nos Laboratórios de Ensino, com a descrição e quantidade dos materiais, insumos e equipamentos necessários para a realização da aula prática. <br>
                  Caso seja solicitado reserva para mais de uma aula: <br><br>
                  • cada aula deve ter um formulário; <br>
                  • cada formulário deve ter a data que a aula será realizada.
                </label>
                <div class="input-group">
                  <?php
                  $sql = "SELECT COUNT(*) FROM propostas_arq WHERE parq_prop_id = :parq_prop_id AND parq_categoria = 3";
                  $stmt = $conn->prepare($sql);
                  $stmt->bindParam(":parq_prop_id", $prop_id, PDO::PARAM_STR);
                  $stmt->execute();
                  $quant_arq = $stmt->fetchColumn();
                  if ($quant_arq == 0) { ?>
                    <input type="file" class="form-control input_arquivo" name="arquivos[]" multiple id="cad_info_pratic_arquivo" onchange="imgCert(this)" accept=".pdf, .xlsx, .xls, .doc, .docx, .csv, .jpg, .JPG, .jpeg, .png, .PNG">
                  <?php } else if ($quant_arq <= 20) { ?>
                    <input type="file" class="form-control input_arquivo" name="arquivos[]" id="cad_info_pratic_arquivo" onchange="imgCert(this)" accept=".pdf, .xlsx, .xls, .doc, .docx, .csv, .jpg, .JPG, .jpeg, .png, .PNG">
                  <?php } else { ?>
                    <input type="file" class="form-control input_arquivo" name="" disabled>
                  <?php } ?>
                  <div class="invalid-feedback">Este campo é obrigatório</div>
                </div>
                <label class="label_info">
                  • Limite de número de arquivos: 10 <br>
                  • Limite de tamanho de arquivo único: 1GB<br>
                  • Tipos de arquivo permitidos: Word, Excel, PPT, PDF, Imagem, Vídeo, Áudio
                </label>
              </div>
            </div>

          </div>
          <div id="campo_info_pratic_titulo" style="display: none;">

            <div class="col-12">
              <div class="form_margem">
                <label class="form-label">Informe o título da(s) aula(s)</label>
                <label class="label_info">
                  O título da aula deve ser informado da mesma maneira que foi cadastrado no banco de dados no espaço de prática.
                  Obs.: Informar a quantidade de bancadas necessárias para a realização da aula. <br>
                  Caso seja solicitado reserva para mais de uma aula:<br><br>
                  • Cada título deve ter a data que a aula será realizada, conforme o exemplo abaixo:<br>
                  03/09/2020 - Isolamento do campo Operatório;<br>
                  10/09/2020 - Restauração de resina composta Classe II.
                </label>
                <textarea class="form-control" name="" id="cad_info_pratic_tit_aulas" rows="3"></textarea>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

          </div>
          <div id="campo_info_pratic_quant" style="display: none;">

            <div class="col-12">
              <div class="form_margem">
                <label class="form-label">Descreva os materiais, insumos e equipamentos, com suas respectivas quantidades, que serão necessários para a realização da aula no espaço de prática.</label>
                <label class="label_info">
                  Deve ser informada a quantidade com o respectivo nome do material, insumo ou equipamento.<br>
                  Obs.: Caso seja realizada mais de uma aula, informar quais materiais devem ser disponibilizados para cada uma.<br>
                  Por exemplo:<br>
                  20/09/2022<br>
                  05 macas;<br>
                  01 Neurodyn portátil.<br><br>
                  15/09/2022<br>
                  08 colchonetes grandes para ginástica;<br>
                  10 bolas suíças.
                </label>
                <textarea class="form-control" name="" id="cad_info_pratic_desc_material" rows="3"></textarea>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>
          </div>

          <div id="campo_info_pratic_obs" style="display: none;">
            <div class="col-12">
              <div class="form_margem">
                <label class="form-label">Observações</label>
                <textarea class="form-control" name="" rows="3"></textarea>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>



  <div class="card">
    <div class="card-body p-sm-4 p-3">
      <div class="hstack gap-3 align-items-center justify-content-end">
        <p class="label_asterisco me-auto my-0"><span>*</span> Campo obrigatório</p>
        <!-- <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button> -->
        <button type="submit" class="btn botao botao_verde waves-effect">Cadastrar</button>
      </div>
    </div>
  </div>

</form>



<script>
  ////////////////////////
  // CARD IDENTIFICAÇÃO //
  ////////////////////////
  document.addEventListener("DOMContentLoaded", function() {

    // CAMPOS
    const campo_ident_curso = document.getElementById("campo_ident_curso");
    const campo_ident_comp_curricular = document.getElementById("campo_ident_comp_curricular");
    const campo_ident_nome_ativ_comp = document.getElementById("campo_ident_nome_ativ_comp");
    const campo_ident_nome_comp_ativ = document.getElementById("campo_ident_nome_comp_ativ");
    const campo_ident_nome_atividade = document.getElementById("campo_ident_nome_atividade");
    const campo_ident_prof = document.getElementById("campo_ident_prof");
    const campo_ident_semestre = document.getElementById("campo_ident_semestre");
    const campo_ident_tel = document.getElementById("campo_ident_tel");
    const campo_ident_select_nome_curso = document.getElementById("campo_ident_select_nome_curso");
    const campo_ident_nome_curso = document.getElementById("campo_ident_nome_curso");
    // IDs
    const cad_iden_tipo_atividade = document.getElementById("cad_iden_tipo_atividade");
    const cad_iden_comp_curricular = document.getElementById("cad_iden_comp_curricular");
    const cad_iden_nome_comp_ativ = document.getElementById("cad_iden_nome_comp_ativ");
    const cad_iden_nome_ativ = document.getElementById("cad_iden_nome_ativ");
    const cad_iden_select_nome_curso = document.getElementById("cad_iden_select_nome_curso");
    const cad_iden_nome_curso = document.getElementById("cad_iden_nome_curso");
    const cad_iden_nome_atv_comp = document.getElementById("cad_iden_nome_atv_comp");
    const cad_iden_semestre = document.getElementById("cad_iden_semestre");
    const cad_iden_nome_prof = document.getElementById("cad_iden_nome_prof");
    const cad_iden_contato = document.getElementById("cad_iden_contato");

    // INICIA O SELECT2 // NOVA SOLICITAÇÃO - CURSOS
    $(document).ready(function() {
      $("#cad_iden_curso").select2({
        language: {
          noResults: function(params) {
            return "Nenhum resultado encontrado";
          },
        },
      });
    });
    const cad_iden_curso = $("#cad_iden_curso"); //SE COMPO FOR SELECT2
    //

    cad_iden_tipo_atividade.addEventListener("change", function() {
      if (cad_iden_tipo_atividade.value === "1") {

        campo_ident_curso.style.display = "block"; // CAMPO CURSO
        cad_iden_curso.prop("required", true);

        campo_ident_nome_ativ_comp.style.display = "none"; // CAMPO ATIVIDADE/COMPONENTE
        cad_iden_nome_atv_comp.required = false;

        campo_ident_prof.style.display = "none"; // CAMPO NOME PROFESSOR
        cad_iden_nome_prof.required = false;

        campo_ident_tel.style.display = "none"; // CAMPO CONTATO
        cad_iden_contato.required = false;

      } else {

        campo_ident_curso.style.display = "none"; // CAMPO CURSO
        cad_iden_curso.prop("required", false);
        cad_iden_curso.val("").trigger("change.select2"); // RESETA CAMPO SELECT2
        cad_iden_curso.prop("selectedIndex", 0); // GARANTE QUE A SELEÇÃO DESAPAREÇA

        campo_ident_comp_curricular.style.display = "none"; // CAMPO COMPONENTE CURRICULAR
        cad_iden_comp_curricular.required = false;

        campo_ident_select_nome_curso.style.display = "none"; // CAMPO NOME DO CURSO
        cad_iden_select_nome_curso.required = false;

        campo_ident_nome_atividade.style.display = "none"; // CAMPO NOME ATIVIDADE
        cad_iden_nome_ativ.required = false;

        campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
        cad_iden_nome_comp_ativ.required = false;

        campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
        cad_iden_semestre.required = false;

        campo_ident_nome_ativ_comp.style.display = "block"; // CAMPO ATIVIDADE/COMPONENTE
        cad_iden_nome_atv_comp.required = true;

        campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
        cad_iden_nome_prof.required = true;

        campo_ident_tel.style.display = "block"; // CAMPO CONTATO
        cad_iden_contato.required = true;
      }
    });



    // CAMPO CURSO
    $(document).ready(function() {
      $('#cad_iden_curso').on('change', function() {

        const numMostraComp = [1, 7, 8, 10, 11, 19, 22, 28];
        const numeNomeAtiv = [1, 7, 8, 10, 19, 22];

        if (!numMostraComp.includes(parseInt($(this).val()))) {

          campo_ident_comp_curricular.style.display = "block"; // CAMPO COMPONENTE CURRICULAR
          cad_iden_comp_curricular.required = true;

        } else if (numeNomeAtiv.includes(parseInt($(this).val()))) {

          campo_ident_comp_curricular.style.display = "none"; // CAMPO COMPONENTE CURRICULAR
          cad_iden_comp_curricular.required = false;

        } else {
          //campo_ident_comp_curricular.style.display = "none";
          //campo_ident_nome_atividade.style.display = "none";
        }

        // SE 1: RESERVAS ADMINISTRATIVAS
        // SE 7: EXTENSÃO
        // SE 10: GRUPO DE PESQUISA
        // SE 19: PROGRAMA CADEAL
        // SE 28: NIDD
        if ($(this).val() === "1" || $(this).val() === "7" || $(this).val() === "10" || $(this).val() === "19" || $(this).val() === "28") {

          campo_ident_select_nome_curso.style.display = "none"; // CAMPO NOME CURSO SELECT
          cad_iden_select_nome_curso.required = false;

          campo_ident_nome_atividade.style.display = "block"; // CAMPO NOME ATIVIDADE
          cad_iden_nome_ativ.required = true;

          campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
          cad_iden_nome_prof.required = true;
          cad_iden_nome_ativ.required = true;

          campo_ident_tel.style.display = "block"; // CAMPO CONTATO
          cad_iden_contato.required = true;

          campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
          cad_iden_nome_comp_ativ.required = false;

          campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
          cad_iden_semestre.required = false;

          campo_ident_comp_curricular.style.display = "none"; // CAMPO COMPONENTE CURRICULAR
          cad_iden_comp_curricular.required = false;

          campo_ident_nome_curso.style.display = "none"; // CAMPO NOME CURSO
          cad_iden_nome_curso.required = false;

          // SE 8: EXTENSÃO CURRICULARIZADA
        } else if ($(this).val() === "8") {

          campo_ident_select_nome_curso.style.display = "block"; // CAMPO NOME CURSO SELECT
          cad_iden_select_nome_curso.required = true;
          cad_iden_select_nome_curso.value = '';

          campo_ident_nome_curso.style.display = "none"; // CAMPO NOME CURSO
          cad_iden_nome_curso.required = false;

          campo_ident_nome_atividade.style.display = "none"; // CAMPO NOME ATIVIDADE
          cad_iden_nome_ativ.required = false;

          campo_ident_prof.style.display = "none"; // CAMPO NOME PROFESSOR
          cad_iden_nome_prof.required = false;
          cad_iden_nome_ativ.required = false;

          campo_ident_tel.style.display = "none"; // CAMPO CONTATO
          cad_iden_contato.required = false;

          campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
          cad_iden_nome_comp_ativ.required = false;

          campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
          cad_iden_semestre.required = false;

          // SE 11: LATO SENSU
          // SE 22: STRICTO SENSU
        } else if ($(this).val() === "11" || $(this).val() === "22") {

          campo_ident_nome_curso.style.display = "block"; // CAMPO NOME CURSO
          cad_iden_nome_curso.required = true;

          campo_ident_comp_curricular.style.display = "none"; // CAMPO COMPONENTE CURRICULAR
          cad_iden_comp_curricular.required = false;

          campo_ident_select_nome_curso.style.display = "none"; // CAMPO NOME CURSO SELECT

          campo_ident_nome_comp_ativ.style.display = "block"; // CAMPO COMPONENTE ATIVIDADE
          cad_iden_nome_comp_ativ.required = true;

          campo_ident_semestre.style.display = "block"; // CAMPO SEMESTRE
          cad_iden_semestre.required = true;

          campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
          cad_iden_nome_prof.required = true;

          campo_ident_tel.style.display = "block"; // CAMPO CONTATO
          cad_iden_contato.required = true;

          campo_ident_nome_atividade.style.display = "none"; // CAMPO NOME ATIVIDADE
          cad_iden_nome_ativ.required = false;
        } else {
          campo_ident_nome_atividade.style.display = "none"; // CAMPO NOME ATIVIDADE
          cad_iden_nome_ativ.required = false;

          campo_ident_select_nome_curso.style.display = "none"; // CAMPO NOME CURSO SELECT
          cad_iden_nome_curso.required = false;
          cad_iden_nome_curso.value = '';

          campo_ident_nome_curso.style.display = "none"; // CAMPO NOME CURSO
          cad_iden_nome_curso.required = false;

          campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
          cad_iden_nome_comp_ativ.required = false;

          campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
          cad_iden_semestre.required = false;

          campo_ident_prof.style.display = "none"; // CAMPO NOME PROFESSOR
          cad_iden_nome_prof.required = false;

          campo_ident_tel.style.display = "none"; // CAMPO CONTATO
          cad_iden_contato.required = false;
        }
      });
    });




    // CAMPO COMPONENTE CURRICULAR
    $(document).ready(function() {
      $('#cad_iden_comp_curricular').on('change', function() {
        if ($(this).val()) {
          campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
          cad_iden_nome_prof.required = true;

          campo_ident_tel.style.display = "block"; // CAMPO CONTATO
          cad_iden_contato.required = true;
        } else {
          campo_ident_prof.style.display = "none"; // CAMPO NOME PROFESSOR
          cad_iden_nome_prof.required = false;

          campo_ident_tel.style.display = "none"; // CAMPO CONTATO
          cad_iden_contato.required = false;
        }

        // SE 0: OUTRO
        if ($(this).val() === "0") {
          campo_ident_nome_comp_ativ.style.display = "block"; // CAMPO COMPONENTE ATIVIDADE
          cad_iden_nome_comp_ativ.required = true;

          campo_ident_semestre.style.display = "block"; // CAMPO SEMESTRE
          cad_iden_semestre.required = true;

          campo_ident_prof.style.display = "block"; // CAMPO NOME PROFESSOR
          cad_iden_nome_prof.required = true;

          campo_ident_tel.style.display = "block"; // CAMPO CONTATO
          cad_iden_contato.required = true;
        } else {
          campo_ident_nome_comp_ativ.style.display = "none"; // CAMPO COMPONENTE ATIVIDADE
          cad_iden_nome_comp_ativ.required = false;

          campo_ident_semestre.style.display = "none"; // CAMPO SEMESTRE
          cad_iden_semestre.required = false;
        }
      });
    });

    // CAMPO NOME CURSO
    // const cad_iden_select_nome_curso = document.getElementById("cad_iden_select_nome_curso");
    // const campo_ident_semestre = document.getElementById("campo_ident_semestre");
    // const campo_ident_nome_atividade = document.getElementById("campo_ident_nome_atividade");
    // const campo_ident_prof = document.getElementById("campo_ident_prof");
    // const campo_ident_tel = document.getElementById("campo_ident_tel");

    cad_iden_select_nome_curso.addEventListener("change", function() {
      if (cad_iden_select_nome_curso.value) {
        campo_ident_nome_atividade.style.display = "block";
        campo_ident_semestre.style.display = "block";
        campo_ident_prof.style.display = "block";
        campo_ident_tel.style.display = "block";

        document.getElementById("cad_iden_nome_ativ").required = true;
        document.getElementById("cad_iden_semestre").required = true;
        document.getElementById("cad_iden_nome_prof").required = true;
        document.getElementById("cad_iden_contato").required = true;
      } else {
        campo_ident_nome_atividade.style.display = "none";
        campo_ident_semestre.style.display = "none";
        campo_ident_prof.style.display = "none";
        campo_ident_tel.style.display = "none";

        document.getElementById("cad_iden_nome_ativ").required = false;
        document.getElementById("cad_iden_semestre").required = false;
        document.getElementById("cad_iden_nome_prof").required = false;
        document.getElementById("cad_iden_contato").required = false;
      }
    });
  });

  /////////////////////////////////////////////
  // INFORMAÇÕES DA RESERVA - AULAS PRÁTICAS //
  /////////////////////////////////////////////

  // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - SOLICITA RESERVA PARA AULAS PRÉTICAS
  document.addEventListener("DOMContentLoaded", function() {
    // const form = document.getElementById("meuFormulario");
    const form = document.querySelector(".meuFormulario");
    const radios_solic_esp_pratico = document.querySelectorAll('input[name="solic_esp_pratico"]');
    const campo_info_pratic_campus = document.getElementById("campo_info_pratic_campus");

    radios_solic_esp_pratico.forEach(radio => {
      radio.addEventListener("change", function() {
        if (this.value === "1") {
          campo_info_pratic_campus.style.display = "block"; // CAMPO CAMPUS
          cad_info_pratic_campus.required = true;
        } else {
          campo_info_pratic_campus.style.display = "none"; // CAMPO CAMPUS
          cad_info_pratic_campus.required = false;
        }
      });
    });





    // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - CAMPUS / ESPAÇO SUGERIDO E QUANTIDADE DE TURMAS
    const cad_info_pratic_campus = document.getElementById("cad_info_pratic_campus");
    const cad_info_pratic_quant_turma = document.getElementById("cad_info_pratic_quant_turma");
    const campo_info_pratic_espaco = document.getElementById("campo_info_pratic_espaco");
    const campo_info_pratic_espaco_brotas = document.getElementById("campo_info_pratic_espaco_brotas");
    const campo_info_pratic_espaco_cabula = document.getElementById("campo_info_pratic_espaco_cabula");

    function validarCheckboxesPorNome(nome, erroId) {
      const checkboxes = document.querySelectorAll(`input[name="${nome}[]"]`);
      const erroCheckbox = document.getElementById(erroId);
      const algumMarcado = Array.from(checkboxes).some(checkbox => checkbox.checked);

      if (!algumMarcado) {
        erroCheckbox.style.display = "block";
        checkboxes.forEach(checkbox => checkbox.classList.add("is-invalid"));
        return false;
      } else {
        erroCheckbox.style.display = "none";
        checkboxes.forEach(checkbox => checkbox.classList.remove("is-invalid"));
        return true;
      }
    }

    function limparValidacao(nome, erroId) {
      const checkboxes = document.querySelectorAll(`input[name="${nome}[]"]`);
      const erroCheckbox = document.getElementById(erroId);

      erroCheckbox.style.display = "none";
      checkboxes.forEach(checkbox => checkbox.classList.remove("is-invalid"));
    }

    cad_info_pratic_campus.addEventListener("change", function() {
      if (cad_info_pratic_campus.value === "2") {
        campo_info_pratic_espaco.style.display = "block";
        campo_info_pratic_espaco_brotas.style.display = "block";
        campo_info_pratic_espaco_cabula.style.display = "none";
        limparValidacao("cad_info_pratic_espaco_cabula", "msgCheckInfoPraticEspacoCabula");
      } else {
        campo_info_pratic_espaco.style.display = "block";
        campo_info_pratic_espaco_brotas.style.display = "none";
        campo_info_pratic_espaco_cabula.style.display = "block";
        limparValidacao("cad_info_pratic_espaco_brotas", "msgCheckInfoPraticEspacoBrotas");
      }
      cad_info_pratic_quant_turma.required = true;
    });

    form.addEventListener("submit", function(event) {
      let valido = true;
      if (cad_info_pratic_campus.value === "2") {
        if (!validarCheckboxesPorNome("cad_info_pratic_espaco_brotas", "msgCheckInfoPraticEspacoBrotas")) {
          valido = false;
        }
      } else {
        if (!validarCheckboxesPorNome("cad_info_pratic_espaco_cabula", "msgCheckInfoPraticEspacoCabula")) {
          valido = false;
        }
      }

      // if (!valido) {
      //   event.preventDefault();
      //   event.stopPropagation();
      // }
    });

    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
      checkbox.addEventListener("change", function() {
        if (cad_info_pratic_campus.value === "2") {
          validarCheckboxesPorNome("cad_info_pratic_espaco_brotas", "msgCheckInfoPraticEspacoBrotas");
        } else {
          validarCheckboxesPorNome("cad_info_pratic_espaco_cabula", "msgCheckInfoPraticEspacoCabula");
        }
      });
    });






    // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - NÚMERO DE PARTICIPANTES / TIPO RESERVA
    document.getElementById('cad_info_pratic_quant_turma').addEventListener('change', function() {
      const campo_info_pratic_tipo_reserva = document.getElementById('campo_info_pratic_tipo_reserva');

      // NÚMERO DE PARTICIPANTES
      if (this.value) {
        campo_info_pratic_tipo_reserva.style.display = 'block';
        cad_info_pratic_num_partic.required = true;
      } else {
        campo_info_pratic_tipo_reserva.style.display = 'none';
        cad_info_pratic_num_partic.required = false;
      }
    });

    // TIPO RESERVA
    form.addEventListener('submit', function(event) {
      const campo_info_pratic_tipo_reserva = document.getElementById('campo_info_pratic_tipo_reserva');
      const radios_info_pratic_tipo_reserva = document.querySelectorAll('input[name="cad_info_pratic_tipo_reserva"]');
      let radioSelecionado = false;

      radios_info_pratic_tipo_reserva.forEach(radio => {
        if (radio.checked) {
          radioSelecionado = true;
        }
      });

      if (campo_info_pratic_tipo_reserva.style.display === 'block' && !radioSelecionado) {
        event.preventDefault();
        campo_info_pratic_tipo_reserva.classList.add('was-validated');
        radios_info_pratic_tipo_reserva.forEach(radio => {
          radio.classList.add('is-invalid');
        });
      }
    });

    // Adiciona um evento para remover a validação quando um radio é selecionado
    document.querySelectorAll('input[name="cad_info_pratic_tipo_reserva"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const campo_info_pratic_tipo_reserva = document.getElementById('campo_info_pratic_tipo_reserva');
        const radios = document.querySelectorAll('input[name="cad_info_pratic_tipo_reserva"]');

        // Remove a classe is-invalid de todos os radios
        radios.forEach(radio => {
          radio.classList.remove('is-invalid');
        });

        // Remove a classe was-validated do campo oculto
        campo_info_pratic_tipo_reserva.classList.remove('was-validated');
      });
    });



    // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - TIPO DE RESERVA
    const radio = document.querySelectorAll('input[name="cad_info_pratic_tipo_reserva"]');
    const campo_info_pratic_data_reserva = document.getElementById("campo_info_pratic_data_reserva");
    const checkboxes = document.querySelectorAll("input[name='cad_info_pratic_dias_semana[]']");
    const feedback = document.getElementById("msgCheckInfoPraticDiasSemana");

    radio.forEach(radio => {
      radio.addEventListener("change", function() {
        if (this.value === "1") {
          campo_info_pratic_data_reserva.style.display = "block";
          cad_info_pratic_datas.required = true;

          campo_info_pratic_dias_semana.style.display = "none";
          cad_info_pratic_hora_inicial.required = true;

          campo_info_pratic_datas.style.display = "block";
          cad_info_pratic_hora_final.required = true;

        } else {
          campo_info_pratic_data_reserva.style.display = "block";
          cad_info_pratic_datas.required = false;

          campo_info_pratic_dias_semana.style.display = "block";
          campo_info_pratic_datas.style.display = "none";

          cad_info_pratic_hora_inicial.required = true;
          cad_info_pratic_hora_final.required = true;
        }
      });
    });


    form.addEventListener("submit", function(event) {
      const radios = document.querySelectorAll('input[name="cad_info_pratic_material"]');
      let checked = false;

      radios.forEach(radio => {
        if (radio.checked) {
          checked = true;
        }
      });

      if (!checked) {
        event.preventDefault(); // Impede o envio do formulário
        document.getElementById("error-message").style.display = "block"; // Mostra a mensagem de erro
      } else {
        document.getElementById("error-message").style.display = "none"; // Esconde a mensagem de erro
      }
    });

    //


    const opcao1 = document.getElementById("cad_info_pratic_tipo_reserva1");
    const opcao2 = document.getElementById("cad_info_pratic_tipo_reserva2");
    const textareaContainer = document.getElementById("campo_info_pratic_datas");
    const checkboxContainer = document.getElementById("campo_info_pratic_dias_semana");
    const textarea = document.getElementById("cad_info_pratic_datas");
    const checkboxes_info_pratic_dias_semana = document.querySelectorAll("input[name='cad_info_pratic_dias_semana[]']");
    const checkboxFeedback = document.getElementById("msgCheckInfoPraticDiasSemana");

    function validarCheckboxes() {
      let algumMarcado = Array.from(checkboxes_info_pratic_dias_semana).some(checkbox => checkbox.checked);
      checkboxFeedback.style.display = algumMarcado ? "none" : "block";
      return algumMarcado;
    }

    opcao1.addEventListener("change", function() {
      if (this.checked) {
        textareaContainer.style.display = "block";
        checkboxContainer.style.display = "none";
        textarea.classList.remove("is-invalid");
      }
    });

    opcao2.addEventListener("change", function() {
      if (this.checked) {
        checkboxContainer.style.display = "block";
        textareaContainer.style.display = "none";
        checkboxFeedback.style.display = "none";
      }
    });

    textarea.addEventListener("input", function() {
      if (this.value.trim() !== "") {
        this.classList.remove("is-invalid");
      }
    });

    checkboxes_info_pratic_dias_semana.forEach(checkbox => {
      checkbox.addEventListener("change", validarCheckboxes);
    });

    form.addEventListener("submit", function(event) {
      let valido = true;

      if (opcao1.checked && textarea.value.trim() === "") {
        textarea.classList.add("is-invalid");
        valido = false;
      }

      if (opcao2.checked && !validarCheckboxes()) {
        valido = false;
      }

      if (!valido) {
        event.preventDefault();
        event.stopPropagation();
      }
    });


    // CAMPO INFO. DA RESERVA - AULAS PRÁTICAS - MAETERIAIS E EQUIPAMENTOS
    const cad_info_pratic_material = document.querySelectorAll('input[name="cad_info_pratic_material"]');
    const campo_info_pratic_anexar = document.getElementById("campo_info_pratic_anexar");
    const campo_info_pratic_titulo = document.getElementById("campo_info_pratic_titulo");
    const campo_info_pratic_quant = document.getElementById("campo_info_pratic_quant");



    // MATERIAIS
    const radios_info_pratic_material = document.querySelectorAll('input[name="cad_info_pratic_material"]');
    let radioSelecionado = false;

    radios_info_pratic_material.forEach(radio => {
      if (radio.checked) {
        radioSelecionado = true;
      }
    });

    if (!radioSelecionado) {
      event.preventDefault();
      radios_info_pratic_material.forEach(radio => {
        radio.classList.add('is-invalid');
      });
    }

    // Adiciona um evento para remover a validação quando um radio é selecionado
    document.querySelectorAll('input[name="cad_info_pratic_material"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const radios = document.querySelectorAll('input[name="cad_info_pratic_material"]');

        // Remove a classe is-invalid de todos os radios
        radios.forEach(radio => {
          radio.classList.remove('is-invalid');
        });

      });
    });

    cad_info_pratic_material.forEach(radio => {
      radio.addEventListener("change", function() {
        if (this.value === "1") {
          campo_info_pratic_anexar.style.display = "block";
          cad_info_pratic_arquivo.required = true;
          //
          campo_info_pratic_titulo.style.display = "none";
          cad_info_pratic_tit_aulas.required = false;
          //
          campo_info_pratic_quant.style.display = "none";
          cad_info_pratic_desc_material.required = false;
          //
          campo_info_pratic_obs.style.display = "block";

        } else if (this.value === "2") {
          campo_info_pratic_anexar.style.display = "none";
          cad_info_pratic_arquivo.required = false;
          //
          campo_info_pratic_titulo.style.display = "block";
          cad_info_pratic_tit_aulas.required = true;
          //
          campo_info_pratic_quant.style.display = "none";
          cad_info_pratic_desc_material.required = false;
          //
          campo_info_pratic_obs.style.display = "block";

        } else {
          campo_info_pratic_anexar.style.display = "none";
          cad_info_pratic_arquivo.required = false;
          //
          campo_info_pratic_titulo.style.display = "none";
          cad_info_pratic_tit_aulas.required = false;
          //
          campo_info_pratic_quant.style.display = "block";
          cad_info_pratic_desc_material.required = true;
          //
          campo_info_pratic_obs.style.display = "block";
        }
      });
    });
  });
</script>







<script>
  $(document).ready(function() {
    // Quando o curso for alterado
    $('#cad_iden_curso').change(function() {
      var cursoId = $(this).val();
      if (cursoId !== "") {
        $.ajax({
          url: 'buscar_componentes.php',
          type: 'POST',
          data: {
            curso_id: cursoId
          },
          success: function(data) {
            $('#cad_iden_comp_curricular').html(data);
          }
        });
      } else {
        $('#cad_iden_comp_curricular').html('<option value="">Selecione um componente</option>');
      }
    });
  });
</script>