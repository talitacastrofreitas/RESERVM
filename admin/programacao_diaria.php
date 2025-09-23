<?php
// // Certifique-se de que a sessão foi iniciada no topo do seu arquivo PHP
// // Se o seu header.php já tem 'session_start()', pode pular esta linha.
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

include 'includes/header.php';


// BOTÃO LIMPAR

if (isset($_GET['limpar']) && $_GET['limpar'] === 'true') {
    // Limpa os filtros armazenados na sessão
    unset($_SESSION['filtros_programacao_diaria']);
    // Redireciona para a mesma página sem parâmetros na URL
    header("Location: programacao_diaria.php");
    exit();
}

// --- Lógica para gerenciar os filtros e manter o estado ---

// 1. Recebe os dados do formulário, se houver submissão
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET)) {
    $_SESSION['filtros_programacao_diaria'] = [
        'data' => $_GET['data'] ?? date('Y-m-d'),
        'unidade' => $_GET['unidade'] ?? '',
        'turno' => $_GET['turno'] ?? ''
    ];
}

// 2. Tenta pegar os valores na seguinte ordem:
//    a) da URL (se o formulário foi submetido);
//    b) da sessão (se a página foi atualizada);
//    c) a data de hoje como padrão.
$dataFiltro = $_SESSION['filtros_programacao_diaria']['data'] ?? $_GET['data'] ?? date('Y-m-d');
$unidadeFiltro = $_SESSION['filtros_programacao_diaria']['unidade'] ?? $_GET['unidade'] ?? '';
$turnoFiltro = $_SESSION['filtros_programacao_diaria']['turno'] ?? $_GET['turno'] ?? '';

?>

<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg">
    </div>
</div>

<div class="row breadcrumb_painel">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Programação Diária</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item active">Programação Diária</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <form method="GET">
                    <div class="row g-3">

                        <div class="col-md-3 col-lg-3 col-xl-4">
                            <input type="date" class="form-control flatpickr-input" name="data" id="data"
                                value="<?= htmlspecialchars($dataFiltro) ?>">
                            <script>
                                flatpickr("#data", {
                                    dateFormat: "Y-m-d", // formato do valor REAL do input (enviado na query)
                                    altInput: true, // ativa um input visível com formato alternativo
                                    altFormat: "d/m/Y", // formato exibido para o usuário
                                    locale: "pt" // idioma português
                                });
                            </script>
                        </div>

                        <div class="col-md-3 col-lg-3">
                            <div>
                                <select class="form-select text-uppercase" name="unidade">
                                    <option value="" <?= ($unidadeFiltro === '') ? 'selected' : '' ?>>TODOS OS CAMPUS
                                    </option>
                                    <?php
                                    $sql = $conn->query("SELECT uni_id, uni_unidade FROM unidades ORDER BY uni_unidade");
                                    while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($unidadeFiltro == $propc['uni_id']) ? 'selected' : '';
                                        echo "<option value='{$propc['uni_id']}' $selected>{$propc['uni_unidade']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-3">
                            <div>
                                <select class="form-select" name="turno">
                                    <option value="" <?= ($turnoFiltro === '') ? 'selected' : '' ?>>TODOS OS TURNOS
                                    </option>
                                    <?php
                                    $sql = $conn->query("SELECT cturn_id, cturn_turno FROM conf_turno ORDER BY cturn_id");
                                    while ($propc = $sql->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($turnoFiltro == $propc['cturn_turno']) ? 'selected' : '';
                                        echo "<option value='{$propc['cturn_turno']}' $selected>{$propc['cturn_turno']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-3 col-xl-2 d-flex gap-1">
                            <button type="submit" class="btn botao botao_azul_escuro w-100">Filtrar</button>
                            <a href="programacao_diaria.php?limpar=true"
                                class="btn botao botao_cinza waves-effect w-100 ms-2">Limpar</a>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-sm-6 text-sm-start text-center">
                        <h5 class="card-title mb-0">Lista de Programação</h5>
                    </div>
                    <div
                        class="col-sm-6 d-flex align-items-center d-flex justify-content-sm-end justify-content-center mt-2 mt-sm-0">

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
                            'Sunday' => 'Domingo',
                            'Monday' => 'Segunda-feira',
                            'Tuesday' => 'Terça-feira',
                            'Wednesday' => 'Quarta-feira',
                            'Thursday' => 'Quinta-feira',
                            'Friday' => 'Sexta-feira',
                            'Saturday' => 'Sábado'
                        ];

                        // Usa a variável de filtro que já armazena o valor da sessão
                        $data = $dataFiltro;

                        // Cria objeto DateTime com a data informada ou atual
                        $dataObj = new DateTime($data);

                        // Extrai partes da data
                        $dia = $dataObj->format('d');
                        $mes = (int) $dataObj->format('m');
                        $ano = $dataObj->format('Y');
                        $diaSemanaIngles = $dataObj->format('l');
                        $diaSemana = $diasSemana[$diaSemanaIngles];

                        // Monta a data formatada
                        $data_formatada = "{$diaSemana}, {$dia} de {$meses[$mes]} de {$ano}";
                        ?>

                        <span class="fw-semibold fs-14"><?= $data_formatada ?></span>
                    </div>
                </div>
            </div>

            <style>
                table.dataTable>tbody>tr.child ul.dtr-details>li:last-child {
                    text-align: left;
                    padding: 0.5em 0 !important;
                }
            </style>

            <div class="card-body p-0">
                <table id="tab_prog_diaria" class="table dt-responsive align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th><span class="me-2">Início</span></th>
                            <th><span class="me-2">Fim</span></th>
                            <th><span class="me-2">Curso</span></th>
                            <th><span class="me-2">Semestre</span></th>
                            <th><span class="me-2">Componente Curricular</span></th>
                            <th><span class="me-2">Módulo</span></th>
                            <th><span class="me-2">Professor</span></th>
                            <th><span class="me-2">Local</span></th>
                            <th><span class="me-2">Pavilhão</span></th>
                            <th><span class="me-2">Andar</span></th>
                            <th><span class="me-2">Campus</span></th>
                            <th><span class="me-2">Observação</span></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        try {
                            // Parâmetros iniciais
                            $params = [];

                            // Data do filtro que já está guardada na variável $dataFiltro
                            $params[':data'] = $dataFiltro;

                            // Construção da query SQL em partes
                            $sql = "SELECT res_hora_inicio, res_hora_fim, curs_curso, cs_semestre, compc_componente, res_componente_atividade, res_componente_atividade_nome, res_nome_atividade, res_modulo, res_professor, esp_nome_local_resumido,pav_pavilhao, and_andar, uni_unidade, res_obs
           FROM reservas
           LEFT JOIN cursos ON cursos.curs_id = reservas.res_curso
           LEFT JOIN conf_semestre ON conf_semestre.cs_id = reservas.res_semestre
           LEFT JOIN componente_curricular ON componente_curricular.compc_id = reservas.res_componente_atividade
           INNER JOIN espaco ON espaco.esp_id = reservas.res_espaco_id
           INNER JOIN tipo_espaco ON tipo_espaco.tipesp_id = espaco.esp_tipo_espaco
           LEFT JOIN pavilhoes ON pavilhoes.pav_id = espaco.esp_pavilhao
           LEFT JOIN andares ON andares.and_id = espaco.esp_andar
           LEFT JOIN unidades ON unidades.uni_id = espaco.esp_unidade
           WHERE res_data = :data";

                            // Filtro por unidade
                            if (!empty($unidadeFiltro)) {
                                $sql .= " AND esp_unidade = :unidade";
                                $params[':unidade'] = $unidadeFiltro;
                            }

                            // Filtro por turno
                            if (!empty($turnoFiltro)) {
                                $sql .= " AND res_turno = :turno";
                                $params[':turno'] = $turnoFiltro;
                            }

                            // Ordenação
                            $sql .= " ORDER BY res_hora_inicio, uni_unidade, esp_nome_local_resumido";


                            // Agora sim: preparar e executar com a SQL montada
                            $stmt = $conn->prepare($sql);
                            $stmt->execute($params);
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                                $res_hora_inicio = $row['res_hora_inicio'];
                                $res_hora_fim = $row['res_hora_fim'];
                                $curs_curso = $row['curs_curso'];
                                $cs_semestre = $row['cs_semestre'];
                                $compc_componente = $row['compc_componente'];
                                $res_componente_atividade = $row['res_componente_atividade'];
                                $res_componente_atividade_nome = $row['res_componente_atividade_nome'];
                                $res_nome_atividade = $row['res_nome_atividade'];
                                $res_modulo = $row['res_modulo'];
                                $res_professor = $row['res_professor'];
                                $esp_nome_local_resumido = $row['esp_nome_local_resumido'];
                                $pav_pavilhao = $row['pav_pavilhao'];
                                $and_andar = $row['and_andar'];
                                $uni_unidade = $row['uni_unidade'];
                                $res_obs = $row['res_obs'];

                                // CONFIGURAÇÃO COMPONENTES
                                if (!empty($res_componente_atividade)) {
                                    $componente = $compc_componente;
                                } else if (!empty($res_componente_atividade_nome)) {
                                    $componente = $res_componente_atividade_nome;
                                } else if (!empty($res_nome_atividade)) {
                                    $componente = $res_nome_atividade;
                                }

                                ?>
                                <tr>
                                    <th scope="row" class="fw-bold">
                                        <?= htmlspecialchars(date('H:i', strtotime($res_hora_inicio))) ?>
                                    </th>
                                    <th scope="row" class="fw-bold">
                                        <?= htmlspecialchars(date('H:i', strtotime($res_hora_fim))) ?>
                                    </th>
                                    <td scope="row"><?= htmlspecialchars($curs_curso) ?></td>
                                    <td scope="row" nowrap="nowrap"><?= htmlspecialchars($cs_semestre) ?></td>
                                    <td scope="row"><?= htmlspecialchars($componente) ?></td>
                                    <td scope="row"><?= htmlspecialchars($res_modulo) ?></td>
                                    <td scope="row"><?= htmlspecialchars($res_professor) ?></td>
                                    <td scope="row" class="text-uppercase"><?= htmlspecialchars($esp_nome_local_resumido) ?>
                                    </td>
                                    <td scope="row" nowrap="nowrap" class="text-uppercase">
                                        <?= htmlspecialchars($pav_pavilhao) ?>
                                    </td>
                                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($and_andar) ?>
                                    </td>
                                    <td scope="row" nowrap="nowrap" class="text-uppercase"><?= htmlspecialchars($uni_unidade) ?>
                                    </td>
                                    <td scope="row" class="text-uppercase"><?= htmlspecialchars($res_obs) ?></td>
                                </tr>
                            <?php }
                        } catch (PDOException $e) {
                            // echo "Erro: " . $e->getMessage();
                            echo "Erro ao tentar recuperar os dados" . $e->getMessage();
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>
</div>

<?php include 'includes/footer.php'; ?>