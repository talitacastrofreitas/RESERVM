<?php include 'includes/header.php'; ?>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row">
    <div class="col-12">
    <div class="page-title-box d-md-flex align-items-center justify-content-between">
      <h4 class="mb-md-0">Calendário de Eventos</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">Visualização</li>
          <li class="breadcrumb-item active">Calendário</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">

    <div id="containerBuscaLista" class="row mb-3" style="display: none;">
            <div class="col-md-4 ms-auto">
                <div >
             
                    <input type="text" class="form-control" id="inputBuscaLista" placeholder="BUSCA">
                </div>
            </div>
        </div>
   
        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#cal-academico-tab" role="tab" aria-selected="true">
                   Acadêmico
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#cal-admin-tab" role="tab" aria-selected="false">
                   Administrativo
                </a>
            </li>
        </ul>

        <div class="tab-content pt-3">
            <div class="tab-pane active" id="cal-academico-tab" role="tabpanel">
                <div id="calendar-academico"></div>
            </div>
            <div class="tab-pane" id="cal-admin-tab" role="tabpanel">
                <div id="calendar-administrativo"></div>
            </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales-all.global.min.js'></script>

<style>
  .fc-prev-button::before { font-family: "Font Awesome 6 Free"; content: "\f053"; font-weight: 900; margin-right: 5px; }
  .fc-next-button::before { font-family: "Font Awesome 6 Free"; content: "\f054"; font-weight: 900; margin-left: 5px; }
  .fc-today-button::before { font-family: "Font Awesome 6 Free"; content: "\f133"; font-weight: 900; margin-right: 5px; }

  .fc-multi-month-more-link { display: none !important; }
  .fc-multi-month-day-events { overflow: hidden !important; }
  
  .fc-event .title {
    text-transform: uppercase;
    display: block;
    font-size: 0.8em; 
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .select2-results__options {
    max-height: 250px;
    overflow-y: auto;
  }


  .flatpickr-day {

        margin-bottom: 8px !important; 
        margin-left: 0 !important;
        margin-right: 0 !important;
        height: 38px !important;
        line-height: 38px !important; 
        border-radius: 50% !important;
        border: 1px solid transparent; 
    }


  .flatpickr-day.evento-inativo {
      opacity: 0.6;
      text-decoration: line-through;
  }
  .btn-delete-event {
      padding: 0.1rem 0.4rem;
      font-size: 0.75rem;

  }

  .fc-list-event-time {
      color: #252525 !important;
      font-weight: 600;
     
  }


.hidden-by-filter {
    display: none !important;
}

.fc-list-event.hidden-by-filter {
    display: none !important;
}

.fc-list-day.hidden-by-filter, 
.fc-list-event-time.hidden-by-filter {
    display: none !important;
}
</style>



<div class="row mb-3">
    <div class="col-12">
        <div id="debugOutput" class="alert alert-info" style="display: none; font-size: 0.8em; white-space: pre-wrap;">
            </div>
    </div>
</div>




<script>
document.addEventListener('DOMContentLoaded', function() {

  // ==============================================
  // 0️⃣ VARIÁVEIS GLOBAIS E ELEMENTOS
  // ==============================================
  let picker = null;
  let eventMap = new Map();
  
  // Modal de Cadastro/Edição
  const modalEl = document.getElementById('modalCadastroEvento');
  const modalInstance = new bootstrap.Modal(modalEl);
  const modalLabel = document.getElementById('modalCadastroEventoLabel');
  const formCadastro = document.getElementById('formCadastroEvento');
  const submitButton = formCadastro.querySelector('button[type="submit"]');

  // Modal de Visualização (NOVO)
  const modalVisualizarEl = document.getElementById('modalVisualizarEvento');
  // Verifica se o modal existe no HTML antes de instanciar para evitar erros
  const modalVisualizarInstance = modalVisualizarEl ? new bootstrap.Modal(modalVisualizarEl) : null;
  
  // Botões do Modal de Visualização
  const btnEditarVisual = document.getElementById('btnEditarVisual');
  const btnExcluirVisual = document.getElementById('btnExcluirVisual');

  // ==============================================
  // 1️⃣ FUNÇÕES DE TOAST
  // ==============================================
  const toastConfig = {
      duration: 5000,
      close: true,
      gravity: "top",
      position: "center", 
      stopOnFocus: true,
  };

  function showToastSuccess(message) {
      Toastify({ ...toastConfig, text: message, backgroundColor: "#38C172" }).showToast();
  }
  function showToastError(message) {
      Toastify({ ...toastConfig, text: message, backgroundColor: "#C4453E" }).showToast();
  }
  function showToastWarning(message) {
      Toastify({ ...toastConfig, text: message, backgroundColor: "#F6993F" }).showToast();
  }

  // ==============================================
  // 2️⃣ HELPER: MAPA DE EVENTOS (PARA CORES NO FLATPICKR)
  // ==============================================
  function atualizarEventMap() {
      eventMap.clear();
      const activeTab = document.querySelector('.nav-tabs .nav-link.active');
      let calendarInstance = calendarAcademico; // Default
      if (activeTab && activeTab.getAttribute('href') === '#cal-admin-tab') {
          calendarInstance = calendarAdministrativo;
      }
      if (!calendarInstance) return;

      calendarInstance.getEvents().forEach(event => {
          if (!event.startStr) return;
          
          const dateStr = event.startStr.substring(0, 10);
          let cor = '';
          let isAtivo = true;
          let title = event.title;

          if (event.extendedProps) {
              isAtivo = (event.extendedProps.ativo == 1);
              const motivo = (event.extendedProps.motivo || 'normal').toLowerCase().trim();
              cor = isAtivo ? (coresMotivo[motivo] || coresMotivo['normal']) : coresMotivo['normal'];
          }
          
          if (cor) {
              eventMap.set(dateStr, { color: cor, active: isAtivo, title: title }); 
          }
      });
  }

  // ==============================================
  // 3️⃣ INICIALIZA FLATPICKR
  // ==============================================
  modalEl.addEventListener('shown.bs.modal', function () {
    if (!picker) {
      picker = flatpickr("#dataEvento", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        locale: "pt",
        allowInput: true,
        
        onDayCreate: function(dObj, dStr, fp, dayElem) {
            const dateStr = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
            const eventInfo = eventMap.get(dateStr);

            if (eventInfo) {
                dayElem.style.backgroundColor = eventInfo.color;
                dayElem.classList.add('evento-no-dia');
                dayElem.title = eventInfo.title;
                
                if (!eventInfo.active) {
                    dayElem.classList.add('evento-inativo');
                    dayElem.title += " (Inativo)"; 
                }
            }
        }
      });
    } else {
      picker.redraw();
    }
  });

  // ==============================================
  // 4️⃣ DEFINIÇÃO DE CORES
  // ==============================================
  const coresMotivo = {
    'domingo': 'rgba(255, 0, 0, 0.1)',
    'feriado': 'rgba(255, 0, 0, 0.1)',
    'liberação bahiana': 'rgba(255, 179, 71, 0.18)',
    'prosef medicina': 'rgba(195, 230, 203, 0.18)',
    'prosef saúde': 'rgba(179, 229, 252, 0.18)',
    'férias alunos': 'rgba(12, 238, 219, 0.18)',
    'início das aulas veteranos': 'rgba(253, 195, 19, 0.3)',
    'início das aulas calouros': 'rgba(253, 195, 19, 0.3)',
    'recesso carnaval': 'rgba(243, 51, 243, 0.25)',
    'recepção dos calouros': 'rgba(56, 56, 55, 0.18)',
    'recesso': 'rgba(243, 51, 243, 0.25)',
    'prova final veteranos': 'rgba(151, 11, 233, 0.34)',
    'início férias veteranos': 'rgba(12, 238, 219, 0.18)',
    'início do planejamento pedagógico': 'rgba(97, 97, 97, 0.28)',
    'prova final calouros': 'rgba(151, 11, 233, 0.34)',
    'início férias calouros': 'rgba(12, 238, 219, 0.18)',
    'fim do planejamento pedagógico': 'rgba(97, 97, 97, 0.28)',
    'fim das férias alunos': 'rgba(12, 238, 219, 0.18)',
    'início das aulas': 'rgba(240, 213, 131, 0.18)',
    'fórum pedagógico': 'rgba(0, 15, 151, 0.34)',
    'xxiv jornada de iniciação científica e tecnológica e xvi fórum de pesquisadores': 'rgba(110, 58, 9, 0.34)',
    'eleições': 'rgba(190, 145, 11, 0.37)',
    'xxvi mcc e xiii mostra de extensão': 'rgba(243, 184, 8, 0.32)',
    'prova final': 'rgba(151, 11, 233, 0.34)',
    'início / fim do planejamento pedagógico': 'rgba(97, 97, 97, 0.28)',
    'normal': 'rgba(207, 226, 255, 0.12)'
  };

  const coresEvento = {
    'feriado': { backgroundColor: 'rgba(255, 0, 0, 0.1)', textColor: '#dc3545' },
    'domingo': { backgroundColor:'rgba(255, 0, 0, 0.1)', textColor: '#dc3545'},
    'liberação bahiana': { backgroundColor: '#ffb347', textColor: '#ffb347' },
    'prosef medicina': { backgroundColor: '#c3e6cb', textColor: '#c3e6cb' },
    'prosef saúde':    { backgroundColor: '#b3e5fc', textColor: '#b3e5fc' },
    'férias alunos':   { backgroundColor: 'rgba(12, 238, 219, 0.18)', textColor: '#0cebda' },
    'início das aulas veteranos': { backgroundColor: 'rgba(253, 194, 19, 0.2)', textColor: '#aa8004ff' },
    'início das aulas calouros': { backgroundColor: 'rgba(253, 195, 19, 0.2)', textColor: '#aa8004ff' },
    'fórum pedagógico': { backgroundColor: 'rgba(0, 15, 155, 0.34)', textColor: '#021e9cff' },
    'início das aulas': { backgroundColor: 'rgba(253, 195, 19, 0.2)', textColor: '#aa8004ff' },
    'recesso carnaval': { backgroundColor: 'rgba(243, 51, 243, 0.25)', textColor: '#f333f3' },
    'recesso':         { backgroundColor: 'rgba(243, 51, 243, 0.25)', textColor: '#f333f3' },
    'prova final':     { backgroundColor: 'rgba(151, 11, 233, 0.34)', textColor: '#970be9' },
    'início férias calouros': { backgroundColor: 'rgba(12, 238, 219, 0.18)', textColor: 'rgba(12, 238, 219, 1)' },
    'início férias veteranos': { backgroundColor: 'rgba(12, 238, 219, 0.18)', textColor: 'rgba(12, 238, 219, 1)' },
    'fim do planejamento pedagógico': { backgroundColor: 'rgba(97, 97, 97, 0.28)', textColor: 'rgba(97, 97, 97, 1)' },
    'fim das férias alunos': { backgroundColor: 'rgba(12, 238, 219, 0.18)', textColor: 'rgba(12, 238, 219, 1)' },
    'início do planejamento pedagógico':{ backgroundColor: 'rgba(97, 97, 97, 0.28)', textColor: 'rgba(97, 97, 97, 1)' },
    'prova final veteranos': { backgroundColor: 'rgba(151, 11, 233, 0.34)', textColor: '#970be9' },
    'prova final calouros': { backgroundColor: 'rgba(151, 11, 233, 0.34)', textColor: '#970be9' },
    'xxiv jornada de iniciação científica e tecnológica e xvi fórum de pesquisadores': {backgroundColor:'rgba(110, 58, 9, 0.34)', textColor: '#aa4104ff'},
    'planejamento pedagógico': { backgroundColor: 'rgba(97, 97, 97, 0.28)', textColor: 'rgba(97, 97, 97, 1)' },
    'xxvi mcc e xiii mostra de extensão': { backgroundColor: 'rgba(243, 184, 8, 0.32)', textColor: '#b68a09ff' },
    'eleições': { backgroundColor: 'rgba(190, 145, 11, 0.32)', textColor: '#be920b' },
    'início / fim do planejamento pedagógico': { backgroundColor: 'rgba(97, 97, 97, 0.28)', textColor: 'rgba(97, 97, 97, 1)' },
    'normal': { backgroundColor: '#6c757d', textColor: '#ffffff' }
  };

  // ==============================================
  // 5️⃣ FUNÇÕES DOS MODAIS
  // ==============================================

  // --- A. Modal de Criação ---
  function prepararModalParaCriar(info) {
    formCadastro.reset(); 
    document.getElementById('dbloq_id').value = ''; 
    modalLabel.textContent = 'Cadastrar Data Bloqueada';
    submitButton.textContent = 'Cadastrar';

    const clickedDate = new Date(info.dateStr);
    const ano = clickedDate.getUTCFullYear();
    const mesIndex = clickedDate.getUTCMonth();
    const diaSemanaIndex = clickedDate.getUTCDay();
    const meses = ["JANEIRO", "FEVEREIRO", "MARÇO", "ABRIL", "MAIO", "JUNHO", "JULHO", "AGOSTO", "SETEMBRO", "OUTUBRO", "NOVEMBRO", "DEZEMBRO"];
    const dbWeekId = diaSemanaIndex;

    document.getElementById('ano').value = ano;
    document.getElementById('mes').value = meses[mesIndex];
    document.getElementById('diaSemanaId').value = dbWeekId;
    document.getElementById('diaSemana').value = dbWeekId;

    if (typeof $ !== 'undefined') {
        $('#tipoCalendarioSelect').val('').trigger('change');
        $('#motivoSelect').val('').trigger('change');
    }

    atualizarEventMap();
    modalInstance.show();

    modalEl.addEventListener('shown.bs.modal', function handler() {
        if (picker) picker.setDate(info.dateStr, true);
        else document.getElementById('dataEvento').value = info.dateStr;
        modalEl.removeEventListener('shown.bs.modal', handler);
    });
  }

  // --- B. Modal de Edição (Apenas dados) ---
  function prepararModalParaEditar(eventId) {
    formCadastro.reset(); 
    modalLabel.textContent = 'Carregando...';

    atualizarEventMap();
    modalInstance.show();

    if (typeof $ !== 'undefined') {
        $('#tipoCalendarioSelect').val('').trigger('change');
        $('#motivoSelect').val('').trigger('change');
        $('select[name="dbloq_cal_semestre"]').val('');
    }

    fetch(`../admin/controller/controller_calendario.php?id=${eventId}`)
      .then(response => {
        if (!response.ok) throw new Error('Falha ao buscar evento.');
        return response.json();
      })
      .then(result => {
        if (result.success && result.data) {
            const data = result.data;

            document.getElementById('dbloq_id').value = eventId; 
            document.getElementById('mes').value = data.dbloq_mes;
            document.getElementById('ano').value = data.dbloq_ano;
            document.getElementById('diaSemanaId').value = data.dbloq_dia;
            document.getElementById('diaSemana').value = data.dbloq_dia;
            document.getElementById('dbloq_status').checked = (data.dbloq_status == 1);
            
            if (picker) picker.setDate(data.dbloq_data, true);
            else document.getElementById('dataEvento').value = data.dbloq_data;

            if (typeof $ !== 'undefined') {
                const $tipoSelect = $('#tipoCalendarioSelect');
                const $motivoSelect = $('#motivoSelect');
                const $semestreSelect = $('select[name="dbloq_cal_semestre"]');
                
                $semestreSelect.val(String(data.dbloq_cal_semestre || ''));
                $tipoSelect.val(String(data.dbloq_cal_tipo || '')).trigger('change');
                $motivoSelect.val(String(data.dbloq_motivo)).trigger('change');
            }

            modalLabel.textContent = 'Editar Data Bloqueada';
            submitButton.textContent = 'Atualizar';
        } else {
          throw new Error(result.message || 'Erro ao carregar.');
        }
      })
      .catch(error => {
        console.error("Erro prepararModalParaEditar:", error);
        modalInstance.hide();
        showToastError(error.message);
      });
  }

  // --- C. NOVO: Modal de Visualização ---
  function prepararModalVisualizar(eventId) {
    if(!modalVisualizarInstance) return;

    // 1. Reseta os campos visuais
    document.getElementById('viewData').value = 'Carregando...';
    document.getElementById('viewDiaSemana').value = '';
    document.getElementById('viewMes').value = '';
    document.getElementById('viewAno').value = '';
    document.getElementById('viewMotivo').value = '';
    document.getElementById('viewStatus').value = '';
    document.getElementById('viewTipo').value = '';
    document.getElementById('viewSemestre').value = '';
    
    modalVisualizarInstance.show();

    fetch(`../admin/controller/controller_calendario.php?id=${eventId}`)
    .then(res => res.json())
    .then(result => {
        if (result.success && result.data) {
            const d = result.data;
            
            // --- DATA ---
            if (d.dbloq_data) {
                const parts = d.dbloq_data.split('-'); 
                document.getElementById('viewData').value = `${parts[2]}/${parts[1]}/${parts[0]}`;
            }

            // --- MÊS E ANO ---
            document.getElementById('viewMes').value = d.dbloq_mes || '';
            document.getElementById('viewAno').value = d.dbloq_ano || '';

            // --- DIA DA SEMANA ---

            let textoDia = 'Não identificado';

            const optionDia = document.querySelector(`#diaSemana option[value="${d.dbloq_dia}"]`);
            if(optionDia) textoDia = optionDia.textContent;
            
            document.getElementById('viewDiaSemana').value = textoDia;

            // --- MOTIVO ---
            let textoMotivo = 'Não identificado';
            const optionMotivo = document.querySelector(`#motivoSelect option[value="${d.dbloq_motivo}"]`);
            if(optionMotivo) textoMotivo = optionMotivo.textContent.trim();
            document.getElementById('viewMotivo').value = textoMotivo;

            // --- STATUS ---
            const inputStatus = document.getElementById('viewStatus');
            if (d.dbloq_status == 1) {
                inputStatus.value = 'DATA BLOQUEADA';
                inputStatus.style.color = '#198754';
      
            } else {
                inputStatus.value = 'DATA LIBERADA';
                inputStatus.style.color = '#6c757d';
          
            }

            // --- TIPO E SEMESTRE ---
            const tipos = { '1': 'Acadêmico', '2': 'Administrativo' };
            document.getElementById('viewTipo').value = tipos[d.dbloq_cal_tipo] || 'Todos';
            document.getElementById('viewSemestre').value = d.dbloq_cal_semestre ? `${d.dbloq_cal_semestre}º Semestre` : '-';

            // --- BOTÕES ---
            if(btnEditarVisual) btnEditarVisual.dataset.id = eventId;
            if(btnExcluirVisual) btnExcluirVisual.dataset.id = eventId;

        } else {
            showToastError('Erro ao carregar detalhes.');
            modalVisualizarInstance.hide();
        }
    })
    .catch(err => {
        console.error(err);
        showToastError('Erro de conexão.');
        modalVisualizarInstance.hide();
    });
  }

  // ==============================================
  // 6️⃣ LÓGICA DO CALENDÁRIO (FULLCALENDAR)
  // ==============================================
  let ultimoViewType = '';

  const commonCalendarOptions = {
    locale: 'pt-br',
    themeSystem: 'bootstrap5',
    initialView: 'dayGridMonth', 
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,dayGridWeek,multiMonthYear,listMonth'
    },
    buttonIcons: false,
    buttonText: {
      today: 'Hoje', month: 'Mês', year: 'Ano', week: 'Semana', day: 'Dia', list: 'Lista', prev: '', next:''
    },
    allDayText: 'Dia inteiro',
    moreLinkText: function(count) { return `+${count} mais`; },
    moreLinkHint: function(count) { return `Mostrar mais ${count} eventos`; },
    noEventsText: 'Nenhum evento para mostrar',
    dayMaxEvents: 1,
    
    // --- Campo de Busca e Filtro ---
    datesSet: function(info) {
        const containerBusca = document.getElementById('containerBuscaLista');
        const viewAtual = info.view.type;
        const isListaAtual = viewAtual.includes('list');
        const wasLista = ultimoViewType && ultimoViewType.includes('list');

        if (isListaAtual) {
            containerBusca.style.display = 'flex';
        } else {
            containerBusca.style.display = 'none';
            document.getElementById('inputBuscaLista').value = ''; 
        }
        if (isListaAtual && !wasLista && ultimoViewType !== '') {
            setTimeout(() => { info.view.calendar.refetchEvents(); }, 50);
        }
        ultimoViewType = viewAtual;

        // Reaplicar filtro
        const inputBusca = document.getElementById('inputBuscaLista');
        if (inputBusca && inputBusca.value.trim() !== '' && isListaAtual) {
             setTimeout(() => {
                 aplicarFiltroLocal(info.view.calendar, inputBusca.value);
             }, 200);
        }
    },

    dayCellDidMount: function(info) {
      var frame = info.el.querySelector('.fc-multimonth-day-frame') ||
                  info.el.querySelector('.fc-daygrid-day-frame') ||
                  info.el;
      if (info.date.getDay() === 0) {
        frame.style.backgroundColor = coresMotivo['domingo'];
      }
    },

   eventsSet: function(events) {
      const calendarEl = this.el;
      const allDayCells = calendarEl.querySelectorAll('td.fc-day[data-date]');
      
      allDayCells.forEach(cell => {
        const frame = cell.querySelector('.fc-multimonth-day-frame') || cell.querySelector('.fc-daygrid-day-frame') || cell;
        const date = new Date(cell.dataset.date + 'T12:00:00Z');
        if (date.getUTCDay() === 0) {
          frame.style.backgroundColor = coresMotivo['domingo'];
        } else {
          frame.style.backgroundColor = '';
        }
      });

      events.forEach(event => {
        if (!event.startStr || event.extendedProps.ativo != 1) return;
        const dateStr = event.startStr.substring(0, 10);
        const motivo = (event.extendedProps.motivo || 'normal').toLowerCase().trim();
        const cor = coresMotivo[motivo] || coresMotivo['normal'];
        const cellSelector = `td.fc-day[data-date="${dateStr}"]`;
        calendarEl.querySelectorAll(cellSelector).forEach(c => {
             let f = c.querySelector('.fc-multimonth-day-frame') || c.querySelector('.fc-daygrid-day-frame') || c;
             if(f) f.style.backgroundColor = cor;
        });
      });
    },

    // Clique na DATA (Vazia) -> Abre Criar
    dateClick: function(info) {
      const clickedDate = new Date(info.dateStr);
      if (clickedDate.getUTCDay() === 0) {
        showToastWarning('Domingo é bloqueado automaticamente.');
        return;
      }
      prepararModalParaCriar(info);
    },

    // Clique no EVENTO -> Abre Visualizar
    eventClick: function(info) {
      info.jsEvent.preventDefault(); 
      
      // Fecha popover do "+mais" se estiver aberto
      const popover = document.querySelector('.fc-popover');
      if (popover) popover.remove();
      
      if (info.jsEvent.target.closest('.btn-delete-event')) return;

      // ALTERADO: Chama Visualizar ao invés de Editar
      prepararModalVisualizar(info.event.id);
    },

    eventDidMount: function(arg) {
      const isAtivo = (arg.event.extendedProps.ativo == 1);
      const viewType = arg.view.type;
      
      // 1. Verifica filtro local (Busca)
      const isHiddenByFilter = arg.event.extendedProps.is_hidden_by_filter;
      if (isHiddenByFilter) {
          arg.el.style.display = 'none';
          return;
      }

      // 2. Lógica de Estilização
      if (isAtivo) {
          // --- EVENTO ATIVO (Cores normais) ---
          const motivo = (arg.event.extendedProps.motivo || 'normal').toLowerCase().trim();
          var cor = coresEvento[motivo] || coresEvento['normal'];
          aplicarEstilo(arg, cor);
          
          // Remove opacidade caso tenha sido reciclado
          arg.el.style.opacity = '1'; 

      } else {
          // --- EVENTO INATIVO (Cinza Fraco) ---
          
          // Define a cor cinza para inativos
          const corInativo = {
              backgroundColor: '#38BE8033', 
              textColor: '#38C172',       
              borderColor: '#dee2e6'      
          };

          aplicarEstilo(arg, corInativo);

          // Garante que está visível (remove o display:none antigo)
          arg.el.style.display = ''; 
          
          // Adiciona um efeito visual extra (opacidade e itálico)
          arg.el.style.opacity = '0.7';
          
   
      }
    },

    eventContent: function(arg) {
      const isAtivo = (arg.event.extendedProps.ativo == 1);
      let labelInativo = '';
      if (!isAtivo) {
          labelInativo = ' <span class="badge bg-success ms-1" style="font-size: 8px;">DATA LIBERADA</span>';
      }
      let titleHtml = `<small class="title" title="${arg.event.title}">
                          ${arg.event.title}${labelInativo}
                       </small>`;

      if (arg.view.type.includes('list')) {
    
          let deleteButtonHtml = `
              <button class="btn btn-sm btn-delete-event ms-auto" data-id="${arg.event.id}" title="Excluir este evento">
                  <i class="fa-regular fa-trash-can fa-lg" style="pointer-events: none;"></i>
              </button>
          `;
          return {
              html: `
                  <div class="d-flex align-items-center w-100">
                      ${titleHtml}
                      ${deleteButtonHtml}
                  </div>
              `
          };
      }
      return { html: titleHtml };
    }
  };

  function aplicarEstilo(arg, cor) {
      if (!cor) return;
      arg.el.style.backgroundColor = cor.backgroundColor;
      arg.el.style.borderColor = cor.backgroundColor;
      const textEl = arg.el.querySelector('.title'); 
      if (textEl) textEl.style.color = cor.textColor; 
      else arg.el.style.color = cor.textColor;
      
      const dotEl = arg.el.querySelector('.fc-list-event-dot');
      if (dotEl) dotEl.style.borderColor = cor.textColor;
      
      const deleteBtn = arg.el.querySelector('.btn-delete-event');
      if (deleteBtn) deleteBtn.style.color = cor.textColor;
  }

  // ==============================================
  // 7️⃣ INICIALIZAÇÃO INSTÂNCIAS
  // ==============================================
  let calendarAcademico = null;
  let calendarAdministrativo = null;

  function fetchEventos(fetchInfo, successCallback, failureCallback, tipoCalendario) {
    
let status = 'todos';

      const url = new URL('../router/web.php', window.location.origin);
      url.pathname = '/reservm/router/web.php'; 
      url.searchParams.append('r', 'EventosCalendario');
      url.searchParams.append('cal', tipoCalendario);
      url.searchParams.append('start', fetchInfo.startStr);
      url.searchParams.append('end', fetchInfo.endStr);
      url.searchParams.append('filtro_status', status); 
      
      fetch(url)
        .then(response => response.json())
        .then(data => successCallback(data))
        .catch(error => {
            console.error('Erro ao buscar eventos:', error);
            failureCallback(error);
            showToastError('Erro ao carregar eventos.');
        });
  }

  // -- Função de Excluir Genérica (Usada na Lista e no Modal Visualizar) --
  function confirmarExclusao(eventId, calendarInstance) {
      Swal.fire({
          title: 'Tem certeza?',
          text: "Esta ação não pode ser desfeita!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Sim, excluir!',
          cancelButtonText: 'Cancelar'
      }).then((result) => {
          if (result.isConfirmed) {
              fetch(`../admin/controller/controller_calendario.php?id=${eventId}`, {
                  method: 'DELETE'
              })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      showToastSuccess(data.message);
                   
                      if (calendarAcademico) calendarAcademico.refetchEvents();
                      if (calendarAdministrativo) calendarAdministrativo.refetchEvents();
                  } else {
                      showToastError(data.message);
                  }
              })
              .catch(error => {
                  console.error('Erro ao excluir:', error);
                  showToastError('Erro de conexão ao tentar excluir.');
              });
          } else {
              
          }
      });
  }

  // Listener Botões de Excluir da LISTA
  function addDeleteListener(calendarInstance) {
      calendarInstance.el.addEventListener('click', function(e) {
          const deleteButton = e.target.closest('.btn-delete-event');
          if (deleteButton) {
              e.preventDefault(); 
              e.stopPropagation(); 
              confirmarExclusao(deleteButton.dataset.id, calendarInstance);
          }
      });
  }

  // Inicializa Calendários
  const calendarElAcademico = document.getElementById('calendar-academico');
  calendarAcademico = new FullCalendar.Calendar(calendarElAcademico, {
    ...commonCalendarOptions,
    eventSources: [ function(fetchInfo, successCallback, failureCallback) {
        fetchEventos(fetchInfo, successCallback, failureCallback, 'academico');
    }]
  });
  calendarAcademico.render();
  addDeleteListener(calendarAcademico);

  const adminTab = document.querySelector('a[href="#cal-admin-tab"]');
  adminTab.addEventListener('shown.bs.tab', function () {
    if (!calendarAdministrativo) {
      const calendarElAdmin = document.getElementById('calendar-administrativo');
      calendarAdministrativo = new FullCalendar.Calendar(calendarElAdmin, {
        ...commonCalendarOptions,
        eventSources: [ function(fetchInfo, successCallback, failureCallback) {
            fetchEventos(fetchInfo, successCallback, failureCallback, 'administrativo');
        }]
      });
      calendarAdministrativo.render();
      addDeleteListener(calendarAdministrativo);
    }
  });

  // ==============================================
  // 8️⃣ EVENT LISTENERS (MODAL VISUALIZAR)
  // ==============================================
  
  // Botão EDITAR no modal de Visualização
  if(btnEditarVisual) {
      btnEditarVisual.addEventListener('click', function() {
          const id = this.dataset.id;
          if(!id) return;
          
   
          modalVisualizarInstance.hide();
          
        
          setTimeout(() => {
              prepararModalParaEditar(id);
          }, 200);
      });
  }

  // Botão EXCLUIR no modal de Visualização
  if(btnExcluirVisual) {
      btnExcluirVisual.addEventListener('click', function() {
          const id = this.dataset.id;
          if(!id) return;

          modalVisualizarInstance.hide();

        
          Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação não pode ser desfeita!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
              if (result.isConfirmed) {
               
                  fetch(`../admin/controller/controller_calendario.php?id=${id}`, { method: 'DELETE' })
                  .then(r => r.json())
                  .then(data => {
                      if(data.success) {
                          showToastSuccess(data.message);
                          if(calendarAcademico) calendarAcademico.refetchEvents();
                          if(calendarAdministrativo) calendarAdministrativo.refetchEvents();
                      } else {
                          showToastError(data.message);
                      }
                  });
              } else {
                  
                  modalVisualizarInstance.show();
              }
          });
      });
  }

  // ==============================================
  // 9️⃣ LÓGICA DO FORMULÁRIO (SALVAR)
  // ==============================================
  // Select2
  if (typeof $ !== 'undefined') {
    const $motivoSelect = $('#motivoSelect');
    const $tipoSelect = $('#tipoCalendarioSelect');
    const allMotivoOptions = [];
    $motivoSelect.find('option').each(function() {
      const $opt = $(this);
      if ($opt.val()) { 
          allMotivoOptions.push({
              value: $opt.val(),
              text: $opt.text(),
              tipo: $opt.data('tipo') || 'geral'
          });
      }
    });
    $motivoSelect.select2({
        theme: "bootstrap-5",
        dropdownParent: $('#modalCadastroEvento'), 
        placeholder: 'Selecione um motivo'
    });
    function filtrarMotivos() {
        const tipoSelecionado = $tipoSelect.val(); 
        let tiposPermitidos = [];
        if (tipoSelecionado === '1') {
            tiposPermitidos = ['academico', 'geral'];
        } else if (tipoSelecionado === '2') {
            tiposPermitidos = ['administrativo', 'geral'];
        } else {
            tiposPermitidos = ['academico', 'administrativo', 'geral'];
        }
        $motivoSelect.empty();
        $motivoSelect.append($('<option>', {
            value: '', text: 'Selecione um motivo', disabled: true, selected: true
        }));
        allMotivoOptions.forEach(function(opt) {
            if (tiposPermitidos.includes(opt.tipo)) {
                $motivoSelect.append($('<option>', {
                    value: opt.value, text: opt.text, 'data-tipo': opt.tipo
                }));
            }
        });
        $motivoSelect.trigger('change.select2');
    }
    $tipoSelect.on('change', function() { filtrarMotivos(); });
  }

  // Submit
  formCadastro.addEventListener('submit', function(e) {
    e.preventDefault(); 
    const formData = new FormData(formCadastro);

    if (picker && picker.selectedDates.length > 0) {
        formData.set('dataEvento', picker.formatDate(picker.selectedDates[0], "Y-m-d"));
    } else {
        const rawDate = document.getElementById('dataEvento').value;
        if (rawDate.includes('/')) {
            const parts = rawDate.split('/');
            formData.set('dataEvento', `${parts[2]}-${parts[1]}-${parts[0]}`);
        } else {
             formData.set('dataEvento', rawDate);
        }
    }

    fetch('../admin/controller/controller_calendario.php', { 
      method: 'POST', body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showToastSuccess(data.message); 
        modalInstance.hide(); 
        formCadastro.reset(); 
        if (calendarAcademico) calendarAcademico.refetchEvents();
        if (calendarAdministrativo) calendarAdministrativo.refetchEvents();
      } else {
        showToastError('Erro ao salvar: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Erro na requisição:', error);
      showToastError('Erro de conexão.');
    });
  });


 // ==============================================
 // 🔟 FILTRO LOCAL (DATA TABLES STYLE)
 // ==============================================
 const inputBusca = document.getElementById('inputBuscaLista');
 
 if (inputBusca) {
    inputBusca.addEventListener('input', function() {
        const termo = inputBusca.value.toLowerCase(); 
        if (calendarAcademico && calendarAcademico.view.type.includes('list')) {
            aplicarFiltroLocal(calendarAcademico, termo);
        }
        if (calendarAdministrativo && calendarAdministrativo.view.type.includes('list')) {
            aplicarFiltroLocal(calendarAdministrativo, termo);
        }
    });
 }

 function aplicarFiltroLocal(calendar, termoRaw) {
    if (!calendar.view.type.includes('list')) return;

    const buscaExata = termoRaw.endsWith(' ');
    let termo = termoRaw.trim();
    if (/^0[1-9]$/.test(termo)) termo = termo.replace(/^0/, '');

    const calendarEl = calendar.el;
    const allRows = calendarEl.querySelectorAll('.fc-list-table tbody tr');
    let textoDataAtual = ''; 

    allRows.forEach(row => {
        if (row.classList.contains('fc-list-day')) {
            textoDataAtual = row.textContent.toLowerCase().replace(/20\d{2}/g, ''); 
        } 
        else if (row.classList.contains('fc-list-event')) {
            const textoEvento = row.textContent.toLowerCase();
            const conteudo = (textoEvento + ' ' + textoDataAtual).normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            const busca = termo.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            
            let mostrar = false;
            if (busca === '') mostrar = true;
            else if (buscaExata) {
                try {
                    const regex = new RegExp(`\\b${busca}\\b`, 'i');
                    mostrar = regex.test(conteudo);
                } catch (e) { mostrar = conteudo.includes(busca); }
            } else {
                mostrar = conteudo.includes(busca);
            }

            if (mostrar) {
                row.style.display = ''; 
                row.classList.remove('hidden-by-filter');
            } else {
                row.style.display = 'none';
                row.classList.add('hidden-by-filter');
            }
        }
    });

    // Limpeza cabeçalhos
    const dayHeaders = calendarEl.querySelectorAll('.fc-list-day');
    dayHeaders.forEach(header => {
        let temEventoVisivel = false;
        let nextRow = header.nextElementSibling;
        while (nextRow && !nextRow.classList.contains('fc-list-day')) {
            if (nextRow.classList.contains('fc-list-event') && nextRow.style.display !== 'none') {
                temEventoVisivel = true;
                break;
            }
            nextRow = nextRow.nextElementSibling;
        }
        header.style.display = temEventoVisivel ? '' : 'none';
    });
 }
});
</script>

<!-- MODAL DE VISUALIZAÇÃO DO EVENTO -->
<div class="modal fade modal_padrao" id="modalVisualizarEvento" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title">Detalhes do Evento</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        
      <div class="row g-3 mb-4">
    <div class="col-6">
        <label class="form-label text-muted small mb-1">Data</label>
        <input type="text" class="form-control" id="viewData" readonly>
    </div>
    <div class="col-6">
        <label class="form-label text-muted small mb-1">Dia da Semana</label>
        <input type="text" class="form-control text-uppercase" id="viewDiaSemana" readonly>
    </div>

    <div class="col-6">
        <label class="form-label text-muted small mb-1">Mês</label>
        <input type="text" class="form-control text-uppercase" id="viewMes" readonly>
    </div>
    <div class="col-6">
        <label class="form-label text-muted small mb-1">Ano</label>
        <input type="text" class="form-control" id="viewAno" readonly>
    </div>
    
    <div class="col-12">
        <label class="form-label text-muted small mb-1">Tipo de Calendário</label>
        <input type="text" class="form-control text-uppercase" id="viewTipo" readonly>
    </div>

       <div class="col-12">
        <label class="form-label text-muted small mb-1">Motivo</label>
        <input type="text" class="form-control text-uppercase" id="viewMotivo" readonly>
    </div>
    
 
    <div class="col-6">
        <label class="form-label text-muted small mb-1">Semestre</label>
        <input type="text" class="form-control text-uppercase" id="viewSemestre" readonly>
    </div>
    <div class="col-6">
        <label class="form-label text-muted small mb-1">Status</label>
        <input type="text" class="form-control" id="viewStatus" readonly>
    </div>
</div>
      

        <div class="hstack gap-2 justify-content-end">
            <button type="button" class="btn botao botao_vermelho waves-effect" id="btnExcluirVisual">
                <i class="fa-regular fa-trash-can me-1"></i> Excluir
            </button>
            <button type="button" class="btn botao botao_amarelo waves-effect" id="btnEditarVisual">
                <i class="fa-regular fa-pen-to-square me-1"></i> Editar
            </button>
          
        </div>

      </div>
    </div>
  </div>
</div>

<!-- MODAL DE CADASTRO -->
<div class="modal fade modal_padrao" id="modalCadastroEvento" tabindex="-1" aria-labelledby="modalCadastroEventoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal_padrao_cinza">
        <h5 class="modal-title" id="modalCadastroEventoLabel">Cadastrar Data Bloqueada</h5>
        <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <form id="formCadastroEvento">
          <input type="hidden" id="dbloq_id" name="dbloq_id" value="">
         
          
          <div class="row g-3">
            <div class="col-md-6">
              <label for="dataEvento" class="form-label">Data <span>*</span></label>
              <input type="text" class="form-control" id="dataEvento" name="dataEvento" required>
              <div class="invalid-feedback">Este campo é obrigatório</div>
            </div>

            <div class="col-md-6">
              <div>
                <label class="form-label">Mês</label>
                <input type="text" class="form-control text-uppercase" name="dbloq_mes" id="mes" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <div>
                <label class="form-label">Ano</label>
                <input type="text" class="form-control text-uppercase" name="dbloq_ano" id="ano" readonly>
              </div>
            </div>

            <div class="col-md-6">
              <?php try {
                $sql = $conn->prepare("SELECT week_id, week_dias FROM conf_dias_semana");
                $sql->execute();
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) { echo "Erro ao tentar recuperar o perfil"; } ?>
              <div>
                <label class="form-label">Dia da semana</label>
                <input type="hidden" class="form-control text-uppercase" name="dbloq_dia" id="diaSemanaId">
                <select class="form-select text-uppercase" id="diaSemana" disabled>
                  <option selected disabled value=""></option>
                  <?php foreach ($result as $res) : ?>
                    <option value="<?= $res['week_id'] ?>"><?= htmlspecialchars($res['week_dias']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">Tipo de Calendário</label>
                <select class="form-select text-uppercase" name="dbloq_cal_tipo" id="tipoCalendarioSelect">
                  <option selected value="">TODOS OS TIPOS</option>
                  <option value="1">ACADÊMICO</option>
                  <option value="2">ADMINISTRATIVO</option>
                </select>
              </div>
            </div>

            <div class="col-12">
              <?php try {
                $sql = $conn->prepare("SELECT * FROM conf_dias_bloqueadas_motivo ORDER BY dbloqm_motivo");
                $sql->execute();
                $resultMotivo = $sql->fetchAll(PDO::FETCH_ASSOC);
              } catch (PDOException $e) { echo "Erro ao tentar recuperar o perfil"; } ?>
              <div>
                <label class="form-label">Motivo <span>*</span></label>
                <select class="form-select text-uppercase" name="dbloq_motivo" id="motivoSelect" required>
                  <option selected disabled value="">Selecione um tipo de calendário</option>
                  <?php foreach ($resultMotivo as $res) : ?>
                    <option value="<?= $res['dbloqm_id'] ?>" 
                            data-tipo="<?= strtolower(htmlspecialchars($res['dbloqm_tipo'] ?? 'geral')) ?>">
                      <?= htmlspecialchars($res['dbloqm_motivo']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Este campo é obrigatório</div>
              </div>
            </div>

            <div class="col-12">
              <div>
                <label class="form-label">Semestre</label>
                <select class="form-select text-uppercase" name="dbloq_cal_semestre">
                  <option selected value=""></option>
                  <option value="1">1º SEMESTRE</option>
                  <option value="2">2º SEMESTRE</option>
                </select>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="dbloq_status" name="dbloq_status" value="1" checked>
                <label class="form-check-label" for="dbloq_status">Data bloqueada</label>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="hstack gap-3 align-items-center justify-content-end mt-2">
                <p class="label_asterisco me-auto my-0 d-sm-block d-none"><span>*</span> Campo obrigatório</p>
                <button type="button" class="btn botao btn-light waves-effect" data-bs-dismiss="modal" data-bs-toggle="button">Cancelar</button>
             
                <button type="submit" class="btn botao botao_verde waves-effect">Cadastrar</button>
              </div>
            </div>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>