<?php include 'includes/header.php'; ?>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0">Usuários</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="painel.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Usuários</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<style>
  table.dataTable>tbody>tr.child ul.dtr-details>li:last-child {
    text-align: left;
    padding: 0.5em 0 !important;
  }
</style>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-sm-12 text-sm-start text-center">
            <h5 class="card-title mb-0">Lista de Usuários</h5>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <table id="tab_user" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th><span class="me-3">Matrícula</span></th>
              <th><span class="me-3">Nome</span></th>
              <th><span class="me-3">E-mail</span></th>
              <th><span class="me-3">Função</span></th>
              <th><span class="me-3">Setor</span></th>
              <th><span class="me-3">Contato</span></th>
              <th><span class="me-3">Perfil</span></th>
              <th><span class="me-3">Status</span></th>
              
            </tr>
          </thead>
          <tbody>

            <?php
            try {
              $stmt = $conn->prepare("SELECT * FROM $view_colaboradores
                                      INNER JOIN usuarios ON usuarios.user_email = $view_colaboradores.EMAIL
                                      LEFT JOIN cursos ON cursos.curs_matricula_prof = $view_colaboradores.CHAPA
                                        --WHERE user_status NOT IN (2)
                                        ");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);


                // CONFIGURAÇÃO DO STATUS
                // Se a matrícula for desativada da base, o usuário deve aparecer como restrito
                $status_color = (empty($CHAPA) ) ? 'bg_info_cinza' : 'bg_info_verde';
                $status_nome = (empty($CHAPA) ) ? 'RESTRITO' : 'LIBERADO';

                // CONFIGURAÇÃO DO PERFIL
                $perfil_color = (empty($curs_id)) ? 'bg_info_azul' : 'bg_info_roxo';
                $perfil_nome = (empty($curs_id)) ? 'USUÁRIO' : 'COORDENADOR';

            ?>

                <tr>
                  <th class="text-uppercase"><?= htmlspecialchars($CHAPA) ?></th>
                  <td class="text-uppercase"><?= htmlspecialchars($NOMESOCIAL) ?></td>
                  <td class="text-lowercase"><?= htmlspecialchars($EMAIL) ?></td>
                  <td class="text-uppercase"><?= htmlspecialchars($FUNCAO) ?></td>
                  <td class="text-uppercase"><?= htmlspecialchars($SETOR) ?></td>
                  <td class="text-uppercase"><?= htmlspecialchars($TELEFONE1) ?></td>
                  <td><span class="badge <?= $perfil_color ?>"><?= htmlspecialchars($perfil_nome) ?></span></td>
                  <td><span class="badge <?= $status_color ?>"><?= htmlspecialchars($status_nome) ?></span></td>
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

<!-- FOOTER -->
<?php include 'includes/footer.php'; ?>