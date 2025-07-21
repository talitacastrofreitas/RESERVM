function initializeTables() {
  new DataTable("#tab_admin", {
    order: [[1, "asc"]],
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [6], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_user", {
    order: [[1, "asc"]],
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    // aoColumnDefs: [
    //   {
    //     bSortable: false,
    //     aTargets: [6], // Desativa a ordenação coluna 3
    //   },
    // ],
  });

  new DataTable("#tabelaPublicidades", {
    order: [[1, "asc"]],
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    // aoColumnDefs: [
    //   {
    //     bSortable: false,
    //     aTargets: [6], // Desativa a ordenação coluna 3
    //   },
    // ],
  });

  //
  new DataTable("#tab_area_tematica", {
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [2], // Desativa a ordenação coluna 3
      },
    ],
  });

  //
  new DataTable("#tab_curso_coordenador", {
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [4], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_material_servico", {
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [4], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_extensao_comunitaria", {
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [3], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_recursos", {
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [3], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_tipo_evento_social", {
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [2], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_tipo_organizacao_espaco", {
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [2], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_tipo_programa", {
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [3], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_submissao_lista", {
    order: [[3, "desc"]],
    //order: [[ 4, "desc" ], [ 3, "desc" ]],
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [6], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_proj_user", {
    order: [[3, "desc"]],
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    // aoColumnDefs: [
    //   {
    //     bSortable: false,
    //     aTargets: [6], // Desativa a ordenação coluna 3
    //   },
    // ],
  });
  //
  new DataTable("#tab_responsavel", {
    // order: [[3, "desc"]],
    paging: false, // Desativa a paginação
    searching: false, // Desativa a busca
    info: false, // Desativa a exibição de informações
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [3], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_resp_analise", {
    // order: [[3, "desc"]],
    paging: false, // Desativa a paginação
    searching: false, // Desativa a busca
    info: false, // Desativa a exibição de informações
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    // aoColumnDefs: [
    //   {
    //     bSortable: false,
    //     aTargets: [3], // Desativa a ordenação coluna 3
    //   },
    // ],
  });
  //
  new DataTable("#tab_insc_part", {
    order: [[2, "asc"]],
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [0, 6], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_insc_minist", {
    order: [[2, "asc"]],
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [0, 7], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_insc_part_apres", {
    order: [[2, "asc"]],
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [0, 9], // Desativa a ordenação coluna
      },
    ],
  });
  //
  new DataTable("#tab_cred_part_apres", {
    order: [[2, "asc"]],
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [0, 10], // Desativa a ordenação coluna
      },
    ],
  });
  //

  new DataTable("#tab_cred_part_atividade", {
    order: [[2, "asc"]],
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [0, 8], // Desativa a ordenação coluna
      },
    ],
  });
  //
  new DataTable("#tab_insc_part_atividade", {
    order: [[2, "asc"]],
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [0, 7], // Desativa a ordenação coluna
      },
    ],
  });
  //
  new DataTable("#tab_cred_part", {
    order: [[2, "asc"]],
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Todos"],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [0, 7], // Desativa a ordenação coluna 3
      },
    ],
  });
  // ESPAÇOS
  new DataTable("#tab_espaco", {
    order: [[1, "asc"]],
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [10], // Desativa a ordenação coluna 3
      },
    ],
  });
  //
  new DataTable("#tab_solic_user", {
    order: [
      [5, "desc"], // 2ª prioridade: coluna 5 crescente
      [4, "desc"], // 1ª prioridade: coluna 4 decrescente
    ],
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    columnDefs: [
      { orderable: false, targets: -1 }, // -1 indica a última coluna
    ],
  });
  //
  new DataTable("#tab_aprova", {
    // order: [[3, "desc"]],

    order: [
      [4, "desc"], // 2ª prioridade: coluna 5 crescente
      [3, "desc"], // 1ª prioridade: coluna 4 decrescente
    ],

    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    columnDefs: [
      { orderable: false, targets: -1 }, // -1 indica a última coluna
    ],
  });
  //
  //
  //
  //
  //
  //
  //
  //

  // RECURSO
  new DataTable("#tab_recurso", {
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [2], // Desativa a ordenação coluna 3
      },
    ],
  });

  // RESERVA - ESPAÇO SUGERIDO
  new DataTable("#res_esp_sugerido", {
    paging: false, // Desativa paginação
    searching: false, // Desativa barra de busca global
    // ordering: false, // Desativa ordenação nas colunas (filtros)
    info: false, // Remove a informação de "Mostrando x de y"
    order: [[1, "asc"]], // Ordena pela segunda coluna (índice 1), em ordem crescente
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  // RESERVA ALOCAMENTO
  new DataTable("#res_alocamento", {
    paging: false, // Desativa paginação
    searching: false, // Desativa barra de busca global
    // ordering: false, // Desativa ordenação nas colunas (filtros)
    info: false, // Remove a informação de "Mostrando x de y"
    order: [[1, "asc"]], // Ordena pela segunda coluna (índice 1), em ordem crescente
    columnDefs: [
      {
        targets: -1, // Última coluna
        orderable: false, // Desativa ordenação
      },
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  // HORA DE FUNCIONAMENTO
  new DataTable("#tab_hora_func", {
    // paging: false, // Desativa paginação
    //searching: false, // Desativa barra de busca global
    ordering: false, // Desativa ordenação nas colunas (filtros)
    // info: false, // Remove a informação de "Mostrando x de y"
    lengthMenu: [[21], [21]],
    columnDefs: [
      {
        targets: -1, // Última coluna
        orderable: false, // Desativa ordenação
      },
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  // DIAS BLOQUEADOS
  new DataTable("#tab_dias_bloqueados", {
    // paging: false, // Desativa paginação
    // searching: false, // Desativa barra de busca global
    // ordering: false, // Desativa ordenação nas colunas (filtros)
    // info: false, // Remove a informação de "Mostrando x de y"
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    columnDefs: [
      {
        targets: -1, // Última coluna
        orderable: false, // Desativa ordenação
      },
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  // RESERVAS CONFIRMADAS
  new DataTable("#tab_reserva_confirm", {
    scrollX: true,
    scrollCollapse: true,
    paging: true,
    fixedColumns: {
      leftColumns: 7,
    },
    order: [[0, "desc"]],
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    columnDefs: [
      {
        targets: [0, -1], // 0 = primeira coluna, -1 = última
        orderable: false, // Desativa ordenação
      },
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  // RESERVAS CONFIRMADAS SINGLE
  new DataTable("#tab_reserva_confirm_single", {
    scrollX: true,
    scrollCollapse: true,
    paging: true,
    fixedColumns: {
      leftColumns: 8,
    },
    order: [[1, "desc"]],
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    columnDefs: [
      {
        targets: [0, -1], // 0 = primeira coluna, -1 = última
        orderable: false, // Desativa ordenação
      },
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  // COMPONENTE CURRICULAR
  new DataTable("#tab_comp_curricular", {
    // paging: false, // Desativa paginação
    // searching: false, // Desativa barra de busca global
    // ordering: false, // Desativa ordenação nas colunas (filtros)
    // info: false, // Remove a informação de "Mostrando x de y"
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    columnDefs: [
      {
        targets: -1, // Última coluna
        orderable: false, // Desativa ordenação
      },
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  // CURSOS
  new DataTable("#tab_cursos", {
    // paging: false, // Desativa paginação
    // searching: false, // Desativa barra de busca global
    // ordering: false, // Desativa ordenação nas colunas (filtros)
    // info: false, // Remove a informação de "Mostrando x de y"
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    columnDefs: [
      {
        targets: -1, // Última coluna
        orderable: false, // Desativa ordenação
      },
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  // PROGRAMAÇÃO DIÁRIA
  new DataTable("#tab_prog_diaria", {
    // paging: false, // Desativa paginação
    // searching: false, // Desativa barra de busca global
    // ordering: false, // Desativa ordenação nas colunas (filtros)
    // info: false, // Remove a informação de "Mostrando x de y"
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    columnDefs: [
      {
        targets: -1, // Última coluna
        orderable: false, // Desativa ordenação
      },
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  // OCORRÊNCIAS
  new DataTable("#tab_ocor", {
    // paging: false, // Desativa paginação
    // searching: false, // Desativa barra de busca global
    // ordering: false, // Desativa ordenação nas colunas (filtros)
    // info: false, // Remove a informação de "Mostrando x de y"
    lengthMenu: [
      [10, 25, 50, 100],
      [10, 25, 50, 100],
    ],
    order: [[11, "asc"]],
    columnDefs: [
      {
        targets: -1, // Última coluna
        orderable: false, // Desativa ordenação
      },
    ],
    language: {
      sProcessing: "Procurando...",
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "Nenhum registro encontrado",
      search: "",
      info: "Mostrar _START_ até _END_ de _TOTAL_ registros",
      infoEmpty: "",
      infoFiltered: "(filtrado de _MAX_ registros totais)",
      searchPlaceholder: "Busca",
      paginate: {
        first: "Primeiro",
        last: "Último",
        next: "Próximo",
        previous: "Anterior",
      },
    },
  });

  //
}
document.addEventListener("DOMContentLoaded", function () {
  initializeTables();
});
