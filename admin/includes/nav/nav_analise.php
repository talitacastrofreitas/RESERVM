<?php $nav_analise_ativo = basename($_SERVER['SCRIPT_NAME']); ?>

<div class="d-flex profile-wrapper">
  <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
    <li class="nav-item" role="presentation">
      <a class="nav-link fs-14 active" data-bs-toggle="tab" id="dados_solicitacao-tab" href="#dados_solicitacao" role="tab" aria-selected="true">
        <i class="fa-regular fa-file-lines d-inline-block d-md-none text-white"></i> <span class="d-none d-md-inline-block">Dados da Solicitação</span>
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link fs-14" data-bs-toggle="tab" id="atividades-tab" href="#atividades" role="tab" aria-selected="false" tabindex="-1">
        <i class="fa-solid fa-chart-line d-inline-block d-md-none text-white"></i> <span class="d-none d-md-inline-block">Atividades</span>
      </a>
    </li>
    <li class="nav-item" role="presentation">
      <a class="nav-link fs-14" data-bs-toggle="tab" id="ocorrencias-tab" href="#ocorrencias" role="tab" aria-selected="false" tabindex="-1">
        <i class="fa-solid fa-chart-line d-inline-block d-md-none text-white"></i> <span class="d-none d-md-inline-block">Ocorrências</span>
      </a>
    </li>
  </ul>
  <div class="flex-shrink-0 d-none">

    <?php
    $sta_solic = array(1, 5, 4, 6);
    if (!in_array($solic_sta_status, $sta_solic)) {
    ?>
      <button class="btn botao_w botao botao_vermelho waves-effect mb-2 mb-sm-0 ms-0 ms-sm-3" type="button" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_indeferir_solicitacao">Indeferir</button>
    <?php } else { ?>
      <a class="btn botao botao_vermelho waves-effect disabled ms-2">Indeferir</a>
    <?php } ?>

    <?php
    $sta_solic = array(1, 5, 4, 6);
    if (!in_array($solic_sta_status, $sta_solic)) {
    ?>
      <button class="btn botao botao_verde ms-2" data-bs-toggle="modal" data-bs-toggle="button" data-bs-target="#modal_deferir_solicitacao">Deferir</button>
    <?php } else { ?>
      <a class="btn botao botao_verde waves-effect disabled ms-2">Deferir</a>
    <?php } ?>
  </div>
</div>