<?php

use Dompdf\Dompdf;
use Dompdf\Options;

require_once '../conexao/conexao.php';
require '../vendor/autoload.php';

$options = new Options();
$options->set('defaultFont', 'Courier');
$options->set('isRemoteEnabled', true); // Para carregar as Imagens
$options->set('isPhpEnabled', true); // Permite a execução de PHP no HTML

$dompdf = new Dompdf($options);

date_default_timezone_set('America/Sao_Paulo');
$data_relatorio = date('d/m/Y \à\s H:i:s');

//RECUPERA URL PARA O LINK DO EMAIL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$url = $_SERVER['REQUEST_URI'];
$path = parse_url($url, PHP_URL_PATH);
$directories = explode('/', $path);
array_shift($directories);
$pagina = $protocol . $_SERVER['HTTP_HOST'] . '/' . $directories[0];

//$dominio = 'http://' . $_SERVER['HTTP_HOST'] . '/siex';
$img_header = $url_sistema . '/assets/img/rel_logo_siex.jpg';
$img_footer = $url_sistema . '/assets/img/rel_logo_bahiana.jpg';


try {
  $prop_id = base64_decode($_GET['i']);
  $sql = "SELECT * FROM propostas
          INNER JOIN propostas_categorias ON propostas_categorias.propc_id = propostas.prop_tipo
          LEFT JOIN usuarios ON usuarios.user_id = propostas.prop_user_id
          LEFT JOIN conf_cursos_coordenadores ON conf_cursos_coordenadores.cc_id = propostas.prop_curso_vinculo --INNER
          LEFT JOIN forma_acesso ON forma_acesso.for_acess_id = propostas.prop_forma_acesso
          LEFT JOIN modalidade_encontro ON modalidade_encontro.mod_en_id = propostas.prop_modalidade
          LEFT JOIN unidades ON unidades.uni_id = propostas.prop_campus
          -- PROGRAMAS
          LEFT JOIN tipo_programa ON tipo_programa.tipprog_id = propostas.prop_prog_tipo
          LEFT JOIN conf_tipo_programa ON conf_tipo_programa.ctp_id = propostas.prop_prog_categoria
          LEFT JOIN conf_areas_tematicas ON conf_areas_tematicas.at_id = propostas.prop_prog_area_atuacao
          -- PARCERIAS
          LEFT JOIN tipo_empresa ON tipo_empresa.tipemp_id = propostas.prop_parc_tipo_empresa
          LEFT JOIN tipo_parceria ON tipo_parceria.tiparc_id = propostas.prop_parc_tipo_parceria
          LEFT JOIN tipo_espaco ON tipo_espaco.tipesp_id = propostas.prop_parc_tipo_espaco
          LEFT JOIN conf_tipo_espaco_organizacao ON conf_tipo_espaco_organizacao.esporg_id = propostas.prop_parc_organizacao_espaco
          -- EXTENSÃO COMUNITÁRIA
          LEFT JOIN conf_tipo_evento_social ON conf_tipo_evento_social.tes_id = propostas.prop_ext_categoria_evento
          LEFT JOIN tipo_ingresso_participante ON tipo_ingresso_participante.tip_id = propostas.prop_ext_forma_ingresso
          --
          WHERE prop_id = :prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':prop_id', $prop_id);
  $stmt->execute();
  $prop = $stmt->fetch(PDO::FETCH_ASSOC);
  extract($prop);

  // CONFIG. "CURSO ONDE ESTA VINCULADO"
  if (!empty($prop_nome_curso_vinculo)) {
    $prop_nome_curso_vinculo = $prop_nome_curso_vinculo;
  } else {
    $prop_nome_curso_vinculo = $cc_curso;
  }

  // CONFIG. "TIPO DO EVENTO SOCIAL"
  if (!empty($prop_ext_categoria_evento_outro)) {
    $tes_evento_social = $prop_ext_categoria_evento_outro;
  } else {
    $tes_evento_social = $tes_evento_social;
  }
  // SE NÃO HOUVER NOME SOCIAL, EXIBA O NOME COMPLETO
  if (empty($user_nome_social)) {
    $nome_usuario = $user_nome;
  } else {
    $nome_usuario = $user_nome_social;
  }
  //
} catch (PDOException $e) {
  echo "Erro: " . $e->getMessage();
}

if ($prop_tipo == 1) {
  $nome_tipo_proposta = 'CURSOS';
}
if ($prop_tipo == 2) {
  $nome_tipo_proposta = 'EVENTOS CIENTÍFICOS';
}
if ($prop_tipo == 3) {
  $nome_tipo_proposta = 'PROGRAMAS';
}
if ($prop_tipo == 4) {
  $nome_tipo_proposta = 'PARCERIAS';
}
if ($prop_tipo == 5) {
  $nome_tipo_proposta = 'EXTENSÃO COMUNITÁRIA E OUTROS PROJETOS SOCIAIS';
}

// INFORMA O TOTAL DE REGISTROS
$sql_conta = "SELECT COUNT(*) FROM inscricoes WHERE insc_prop_id = '$prop_id'";
$sql_conta_cliente = $conn->query($sql_conta);
$row_cnt = $sql_conta_cliente->fetchColumn();

$html = '
    <html>
      <head>
        <style>
          @page {
              margin: 0.4cm 0cm 0.3cm 0cm;
          }

          body {
              margin-top: 2cm;
              margin-left: .7cm;
              margin-right: .7cm;
              margin-bottom: 2cm;
              font-family: sans-serif;
          }

          h1, h2 ,h3, h4, h5 {
            margin: 0px;
          }

          hr {
            border-bottom: 1px solid #DDD;
            border-top: 0px;
          }

          .no-break {
            page-break-inside: avoid; /* Propriedade para evitar quebra de página */
            page-break-after: auto; /* Propriedade para evitar que a página atual seja quebrada após a div */
          }

          .conteudo {
            font-size: 12px;
            padding: 0px;          
          }

          .conteudo_tabela {
            margin: 0px 0px 20px 0px;
          }

          .conteudo p {
            margin: 0px;
            font-size: .875rem;
            line-height: 1.2rem;
          }

          .conteudo .titulo_single_proposta {
            font-size: 1rem;
            font-weight: 400;
            border-bottom: 1px solid;
            border-left: 3px solid;
            margin: 20px 0px 8px 0px;
            padding: 5px 0px 5px 10px;
          }

          .conteudo_dados p{
            font-size: .75rem;
            line-height: 1rem;
          }


          .conteudo .conteudo_dados {
            padding: 5px 0px 10px 0px;
          }

          .conteudo .conteudo_dados_tabela {
            padding: 0px 0px 5px 0px;
            margin: 0px 0px 0px 0px;
            min-height: 25px
          }

          .conteudo .conteudo_dados_tabela p {
            word-wrap: break-word;
            font-size: .75rem;
            line-height: 1rem;
            margin: 0px 0px 5px 0px;
          }

          .conteudo .conteudo_dados_tabela span{
            margin: 5px 0px 0px 0px;
            display: block;
            font-size: .875rem;
            line-height: 1.2rem;
          }

          .conteudo .titulo_dados_tabela {
            background: #ccc;
            padding: 0px 0px;
            margin: 0px 0px 0px 0px;
          }

          .conteudo .titulo_dados_tabela p{
            padding: 0px 0px;
            margin: 0px 0px 2px 0px;
            font-weight: 700;
          }

          .conteudo .itens_array{
            margin: 0px 0px 0px 0px;
            padding: 0px 0px 0px 0px;
            font-size: .75rem;
          }

          .conteudo li.itens_array{
            margin: 0px 0px 0px 16px;
            padding: 3px 5px 8px 5px;
            font-size: 12px;
          }

          .itens_array{
            font-size: 12px;
          }

          .conteudo .itens_upper{
            margin: 0px 0px 0px 0px;
            font-size: 12px;
          }

          .conteudo .label {
            text-transform: uppercase;
            background: #DDD;
            font-size: 10px;
            margin: 0px 0px 8px 0px;
            padding: 3px 5px;
          }

          .conteudo .label_dados_tabela {
            text-transform: uppercase;
            font-size: 10px;
            margin: 0px;
            padding: 3px 5px;
          }       

          .titulo {
              // text-align: center;
              font-size: 20px;
              margin-bottom: -10px;
              
          }

          .total {
              font-size: 12px;
              text-align: right;
              margin-bottom: 10px;
          }

          tr:nth-child(2n+2) {
          // background: #DDD;
          }

          header {
              position: fixed;
              top: .3cm;
              left: .7cm;
              right: .7cm;
              bottom: 0;
              color: #000;
              padding: 0px;
          }

          header img{
              margin: 0 10px 0 0;
              width: 250px;
          }

          fieldset {
            padding: 20px 0px 10px 0px;
            border-top: 1px solid #919191;
            border-bottom: 0;
            border-left: 0;
            border-right: 0;
            margin: 20px 0 10px 0;
          }

          .cont_acordion {
            padding: 10px 0px 10px 0px;
            margin: 0px 0 10px 0;
          }

          .cont_acordion h5 {
            margin: 0px 0 10px 0;
            line-height: 1.2rem;
          }

          .dados_header h3 {
            font-size: 16px;
          }

          .dados_proposta {
            margin-bottom: 0px;
          }

          .dados_proposta h5 {
            font-size: 16px;
            padding: 5px 5px;
          }


          .dados_proposta strong{
            font-size: 12px;
          }

          .dados_proposta {
            margin-bottom: 5px;
          }

          .dados_proposta tr{
            padding: 3px 5px;
            font-size: 10px;
            text-align: left;
          }

          .dados_proposta th {
            padding: 3px 5px;
            font-size: 10px;
            text-align: left;
          }

          .dados_proposta td{
            padding: 3px 0px;
            font-size: 12px;
            text-align: left;
          }

          .dados_proposta_tabela tr{
            padding: 3px 5px;
            font-size: 10px;
            text-align: left;
          }

          .dados_proposta_tabela th {
            padding: 3px 5px;
            font-size: 10px;
            text-align: left;
          }

          .dados_proposta_tabela td {
            padding: 3px 5px;
            font-size: 12px;
            text-align: left;
          }

          .dados_proposta_header h2{
            font-size: 1rem;
            line-height: 1.2rem;
          }

          .dados_proposta_header tr{
            padding: 3px 5px;
            font-size: 10px;
            text-align: left;
          }

          .dados_proposta_header th {
            padding: 3px 5px;
            font-size: 10px;
            text-align: left;
          }

          .dados_proposta_header td {
            padding: 5px 8px;
            font-size: 12px;
            text-align: left;
          }

          footer {
              position: fixed; 
              bottom: 0cm; 
              left: .7cm; 
              right: .7cm;
              height: 1cm;
              // background-color: #03a9f4;
              padding: 20px 0 20px 0;
              color: #000;
              font-size: 10px;
              text-align: right;
              margin-right: 0cm;
          }

          footer img{
            margin: 0 10px 0 0;
            width: 130px;
          }

          .campo_footer {
            display: flex;
            justify-content: space-between;
          }
        </style>
      </head>
      <body>

        <header>
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td style="text-align: left;"><img src="' . $img_header . '"></td>
              <td style="text-align: center;">
              </td>
              <td style="text-align: right;">COD: ' . $prop_codigo . '</td>
            </tr>
          </table>
        </header>
        
        <footer>
          <table width="100%">
            <tr>
              <td><div class=""> <img src="' . $img_footer . '"></div></td>
              <td style="text-align: right;><div class="">' . $data_relatorio . '</div></td>
            </tr>
          </table>
        </footer>

        <main>

          <table class="dados_proposta_header" width="100%" border="1" cellpadding="0" cellspacing="0" style="padding-bottom: 15px;">
            <tr>
              <td colspan="2"><h2>' . $prop_titulo . '</h2></td>
            </tr>
            <tr>
              <td><strong>Tipo: </strong>' . $nome_tipo_proposta . '</td>
              <td><strong>Data Cadastro: </strong>' . date("d/m/Y H:i", strtotime($prop_data_upd)) . '</td>
            </tr>
            <tr>
              <td><strong>Proponente: </strong>' . $nome_usuario . '</td>
              <td></td>
            </tr>
          </table>';

// SE PARCERIAS
if ($prop_tipo != 4) {

  $html .= '<fieldset class="no-break">
            <legend><h4>SOBRE O PROJETO</h4></legend>
            <div class="conteudo"> 

              <div class="conteudo_dados">
                <div class="label">DESCRIÇÃO DA ATIVIDADE</div>
                  <div class="conteudo_dados_tabela"><p>' . $prop_descricao . '</p></div>';

  /////////////////////////////////////////////////////////////
  if (!empty($prop_vinculo_programa)) {
    $html .= '<hr>
              <p class="itens_array">A ATIVIDADE É VINCULADA A UM PROGRAMA OU A UM COMPONENTE CURRICULAR</p>';
  }
  ////////////////////////////

  $html .= '</div>';

  /////////////////////////////////////////////////////////////
  if (!empty($prop_qual_vinculo_programa)) {
    $html .= '<div class="conteudo_dados">
              <div class="label">ATIVIDADE VINCULADA</div>
                <div class="conteudo_dados_tabela">
                  <p>' . $prop_qual_vinculo_programa . '</p>
                </div>  
              </div>';
  }
  ///////////////////////////

  $html .= '<div class="conteudo_dados">
              <div class="label">CURSO ONDE ESTA VINCULADO</div>
                <div class="conteudo_dados_tabela">
                  <div>' . $prop_nome_curso_vinculo . '</div>
                </div>
              </div>
            </div>
          </fieldset>

          <fieldset class="no-break">
          <legend><h4>JUSTIFICATIVA</h4></legend>
          <div class="conteudo">
            <div class="conteudo_dados_tabela">
              <p>' . $prop_justificativa . '</p>
            </div>
          </div>
          </fieldset>

          <fieldset class="no-break">
          <legend><h4>OBJETIVOS PEDAGÓGICOS</h4></legend>
            <div class="conteudo">
              <div class="conteudo_dados_tabela">
                <p>' . $prop_obj_pedagogico . '</p>
              </div>
            </div>
          </fieldset>

          <fieldset class="no-break">
          <legend><h4>PÚBLICO ALVO</h4></legend>
            <div class="conteudo">
              <div class="conteudo_dados_tabela">
                <p>' . $prop_publico_alvo . '</p>
              </div>
            </div>
          </fieldset>

          <fieldset class="no-break">
          <legend><h4>ÁREA DO CONHECIMENTO</h4></legend>
          <div class="conteudo">';

  try {
    $prop_area_conhecimento = rtrim($prop_area_conhecimento, ', '); // RETIRA A ULTIMA VIRGULA DO ARRAY
    $consulta = "SELECT ac_id, ac_area_conhecimento FROM conf_areas_conhecimento WHERE ac_id IN ($prop_area_conhecimento)";
    $stmt = $conn->prepare($consulta);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($resultados) > 0) {
      foreach ($resultados as $row) {
        $html .= '<li class="itens_array">' . $row['ac_area_conhecimento']  . '</li>';
      }
    } else {
      $html .= '<div class="conteudo_dados">
                <p>Nenhuma área tematica selecionada!</p>
              </div>';
    }
  } catch (PDOException $e) {
    $e->getMessage();
  }

  $html .= '</div>
          </fieldset>';

  $html .= '<fieldset class="no-break">
          <legend><h4>ÁREAS TEMÁTICAS</h4></legend>
          <div class="conteudo">';

  try {
    $prop_area_tematica = rtrim($prop_area_tematica, ', '); // RETIRA A ULTIMA VIRGULA DO ARRAY
    $consulta = "SELECT at_id, at_area_tematica FROM conf_areas_tematicas WHERE at_id IN ($prop_area_tematica)";
    $stmt = $conn->prepare($consulta);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($resultados) > 0) {
      foreach ($resultados as $row) {
        $html .= '<li class="itens_array">' . $row['at_area_tematica']  . '</li>';
      }
    } else {
      $html .= '<div class="conteudo_dados">
                <p>Nenhuma área tematica selecionada!</p>
              </div>';
    }
  } catch (PDOException $e) {
    $e->getMessage();
  }

  $html .= '</div>
          </fieldset>';












  /*****************************************************************************************
                              INFORMAÇÕES COMPLEMENTARES
   *****************************************************************************************/

  $html .= '<fieldset class="no-break">
            <legend><h4>INFORMAÇÕES COMPLEMENTARES</h4></legend>
            <div class="conteudo">

            <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
            
            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">DIAS DA SEMANA</div>
                </div>
              </td>
            </tr>';

  try {
    $prop_semana = rtrim($prop_semana, ', '); // RETIRA A ULTIMA VIRGULA DO ARRAY
    $consulta = "SELECT week_id, week_dias FROM dias_semana WHERE week_id IN ($prop_semana)";
    $stmt = $conn->prepare($consulta);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($resultados) > 0) {
      foreach ($resultados as $row) {
        $html .= '<li class="itens_array">' . $row['week_dias']  . '</li>';
      }
    }
  } catch (PDOException $e) {
    $e->getMessage();
  }

  $html .= '
            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">HORÁRIOS</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_horario . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">DATA PREVISTA PARA INÍCIO</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . date("d/m/Y", strtotime($prop_data_inicio)) . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">DATA PREVISTA PARA FINALIZAÇÃO</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . date("d/m/Y", strtotime($prop_data_fim)) . '</p>
                  </div>
                </div>
              </td>
            </tr> 

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">CARGA HORÁRIA</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_carga_hora . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">QUANTIDADE TOTAL DE VAGAS</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_total_vaga . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">QUANTIDADE MÍNIMA PARA FORMA TURMA</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_quant_turma . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">MODALIDADE DO ENCONTRO</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $mod_en_modalidade . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">CAMPUS</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $uni_unidade . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">LOCAL</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $prop_local . '</p>
                  </div>
                </div>
              </td>
            </tr>
            </table>

          <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr> 
              <td width="40%">
                <div class="conteudo">
                  <div class="label">FORMA DE ACESSO DOS PARTICIPANTES</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $for_acess_forma_acesso . '</p>
                  </div>
                </div>
              </td>
              <td width="30%">
                <div class="conteudo">
                  <div class="label">VALOR</div>
                  <div class="conteudo_dados_tabela">
                    <p> R$ ' . $prop_preco . '</p>
                  </div>
                </div>
              </td>
              <td width="30%">
                <div class="conteudo">
                  <div class="label">PARCELAS</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_preco_parcelas . '</p>
                  </div>
                </div>
              </td>
            </tr>
          </table>

          </div>
          </fieldset>';















  /*****************************************************************************************
                      AÇÕES DE ACESSIBILIDADE VINCULADAS AO PROJETO
   *****************************************************************************************/

  $html .= '<fieldset class="no-break">
            <legend><h4>AÇÕES DE ACESSIBILIDADE VINCULADAS AO PROJETO</h4></legend>';

  if (!empty($prop_acao_acessibilidade)) {
    $html .= '<div class="conteudo"> 
              <div class="conteudo_dados_tabela">  
                <p class="itens_array">O PROJETO PROPÕE ALGUMA AÇÃO DE ACESSIBILIDADE PARA PÚBLICOS ESPECÍFICOS (PESSOAS PRETAS, MULHERES, CRIANÇAS, PCD, LGBTQIA+, QUILOMBOLAS)</p>  
              </div>
            </div>

            <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr>
                <td>
                  <div class="conteudo">
                    <div class="label">DESCRIÇÃO</div>
                      <div class="conteudo_dados_tabela">
                      <p>' . $prop_desc_acao_acessibilidade . '</p>
                    </div>
                  </div>
                </td>
              </tr>

            </table>';
  } else {
    $html .= '<div class="conteudo">
              <p class="itens_array">NENHUMA AÇÃO DE ACESSIBILIDADE INFORMADO</p>
              <hr>
            <div>';
  }

  if (!empty($prop_ofertas_vagas)) {
    $html .= '
            <div class="conteudo"> 
              <div class="conteudo_dados_tabela">
                <hr>
                <p class="itens_array">SERÃO OFERTADAS VAGAS</p>  
              </div>
            </div>

            <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr>
                <td>
                  <div class="conteudo">
                    <div class="label">QUANTIDADE DE VAGAS</div>
                      <div class="conteudo_dados_tabela">
                      <p>' . $prop_quant_beneficios . '</p>
                    </div>
                  </div>
                </td>
              </tr>

            </table>';
  } else {
    $html .= '<div class="conteudo">
              <p class="itens_array">NENHUMA VAGA OFERTADA</p>
              <hr>
            <div>';
  }

  if (!empty($prop_atendimento_doacao)) {
    $html .= '
            <div class="conteudo"> 
              <div class="conteudo_dados_tabela">
              <hr>
                <p class="itens_array">SERÃO REALIZADOS ATENDIMENTOS/DOAÇÕES</p>
  
              </div>
            </div>

            <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr>
                <td>
                  <div class="conteudo">
                    <div class="label">DESCRIÇÃO DO PÚBLICO QUE SERÁ ATENDIDO</div>
                      <div class="conteudo_dados_tabela">
                      <p>' . $prop_desc_beneficios . '</p>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="conteudo">
                    <div class="label">INFORME QUAL É A INSTITUIÇÃO OU COMUNIDADE ATENDIDA</div>
                      <div class="conteudo_dados_tabela">
                      <p>' . $prop_comunidade . '</p>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="conteudo">
                    <div class="label">LOCALIDADE/TERRÍTÓRIO</div>
                      <div class="conteudo_dados_tabela">
                      <p>' . $prop_localidade . '</p>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="conteudo">
                    <div class="label">RESPONSÁVEL NA COMUNIDADE/INSTITUIÇÃO/TERRITÓRIO</div>
                      <div class="conteudo_dados_tabela">
                      <p>' . $prop_responsavel . '</p>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="conteudo">
                    <div class="label">CONTATO DO RESPONSÁVEL</div>
                      <div class="conteudo_dados_tabela">
                      <p>' . $prop_responsavel_contato . '</p>
                    </div>
                  </div>
                </td>
              </tr>

            </table>';
  } else {
    $html .= '<div class="conteudo">
              <p class="itens_array">NENHUM ATENDIMENTO/DOAÇÃO INFORMADO</p>
            <div>';
  }

  $html .= '</fieldset>';

  /*****************************************************************************************
                      INFORMAÇÕES ADICIONAIS
   *****************************************************************************************/

  $html .= '<fieldset class="no-break">
            <legend><h4>INFORMAÇÕES ADICIONAIS</h4></legend>';

  if (!empty($prop_info_complementar)) {
    $html .= '<table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">

                <tr>
                  <td>
                    <div class="conteudo">
                      <div class="label">Comentários e solicitações complementares</div>
                        <div class="conteudo_dados_tabela">
                        <p>' . $prop_info_complementar . '</p>
                      </div>
                    </div>
                  </td>
                </tr>

              </table>';
  } else {
    $html .= '<div class="conteudo">
              <p class="itens_array">NENHUMA INFORMAÇÃO ADICIONAL</p>
            <div>';
  }

  $html .= '</fieldset>';

  /*****************************************************************************************
                              COORDENADOR(A) DO PROJETO
   *****************************************************************************************/

  $html .= '<fieldset class="no-break">
            <legend><h4>COORDENADOR(A) DO PROJETO</h4></legend>';

  $sql = $conn->query("SELECT * FROM propostas_coordenador_projeto
                    INNER JOIN conf_cursos_coordenadores ON conf_cursos_coordenadores.cc_id = propostas_coordenador_projeto.pcp_area_atuacao
                    INNER JOIN usuario_perfil ON usuario_perfil.us_pe_id = propostas_coordenador_projeto.pcp_partic_perfil
                    WHERE pcp_proposta_id = '$prop_id' ORDER BY pcp_data_cad DESC");
  while ($pcp = $sql->fetch(PDO::FETCH_ASSOC)) {
    $pcp_id                  = $pcp['pcp_id'];
    $pcp_proposta_id         = $pcp['pcp_proposta_id'];
    $pcp_nome                = $pcp['pcp_nome'];
    $pcp_email               = $pcp['pcp_email'];
    $pcp_contato             = $pcp['pcp_contato'];
    $pcp_partic_perfil       = $pcp['pcp_partic_perfil'];
    $pcp_outro_partic_perfil = $pcp['pcp_outro_partic_perfil'];
    $pcp_carga_hora          = $pcp['pcp_carga_hora'];
    $pcp_area_atuacao        = $pcp['pcp_area_atuacao'];
    $pcp_nome_area_atuacao   = $pcp['pcp_nome_area_atuacao'];
    $pcp_formacao            = $pcp['pcp_formacao'];
    $pcp_lattes              = $pcp['pcp_lattes'];
    $pcp_user_id             = $pcp['pcp_user_id'];
    $pcp_data_cad            = $pcp['pcp_data_cad'];
    $pcp_data_upd            = $pcp['pcp_data_upd'];
    // PERFIL PARTICIPANTE
    $us_pe_perfil            = $pcp['us_pe_perfil'];
    // CURSO COORDENADOR
    $cc_curso                = $pcp['cc_curso'];

    // CONFIG. "PERFIL DO PARTICIPANTE"
    if ($pcp_partic_perfil == 9) {
      $pcp_partic_perfil = $pcp_outro_partic_perfil;
    } else {
      $pcp_partic_perfil = $us_pe_perfil;
    }

    // CONFIG. "CURSO OU ÁREA DE ATUAÇÃO"
    if ($pcp_area_atuacao == 20) {
      $pcp_area_atuacao = $pcp_nome_area_atuacao;
    } else {
      $pcp_area_atuacao = $cc_curso;
    }

    $html .= '<div class="cont_acordion">
              <legend><h5>' . $pcp_nome . '</h5></legend> 
                <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">E-mail</div>
                        <div class="conteudo_dados_tabela">
                          <p>' . $pcp_email . '</p>
                        </div>
                      </div>
                    </td>
                    
                    <td>
                      <div class="conteudo">
                        <div class="label">Contato</div>
                        <div class="conteudo_dados_tabela">
                          <p>' . $pcp_contato . '</p>
                        </div>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">PERFIL DO PARTICIPANTE</div>
                        <div class="conteudo_dados_tabela">
                          <p class="itens_upper">' . $pcp_partic_perfil . '</p>
                        </div>
                      </div>
                    </td>
                    <td>
                    <div class="conteudo">
                      <div class="label">CURSO OU ÁREA DE ATUAÇÃO</div>
                      <div class="conteudo_dados_tabela">
                        <p class="itens_upper">' . $pcp_area_atuacao . '</p>
                      </div>
                    </div>
                  </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">HORAS DEDICADAS AO PROJETO</div>
                        <div class="conteudo_dados_tabela">
                          <p>' . $pcp_carga_hora . '</p>
                        </div>
                      </div>
                    </td>
                  </tr> 

                  <tr>
                    <td colspan="2">
                      <div class="conteudo">
                        <div class="label">FORMAÇÃO PROFISSIONAL</div>
                        <div class="conteudo_dados_tabela">
                          <p>' . $pcp_formacao . '</p>
                        </div>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td colspan="2">
                      <div class="conteudo">
                        <div class="label">LINK DO CURRÍCULO LATTES</div>
                        <div class="conteudo_dados_tabela">
                          <p>' . $pcp_lattes . '</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                </table>
                
              </fieldset>';
  }

  $html .= '</div>';


  /*****************************************************************************************
                              EQUIPE EXECUTORA
   *****************************************************************************************/

  $html .= '<fieldset class="no-break">
          <legend><h4>EQUIPE EXECUTORA</h4></legend>';

  $sql = $conn->query("SELECT * FROM propostas_equipe_executora
                    INNER JOIN conf_cursos_coordenadores ON conf_cursos_coordenadores.cc_id = propostas_equipe_executora.pex_area_atuacao
                    INNER JOIN usuario_perfil ON usuario_perfil.us_pe_id = propostas_equipe_executora.pex_partic_perfil
                    INNER JOIN conf_categoria_participacao_projeto ON conf_categoria_participacao_projeto.cpp_id = propostas_equipe_executora.pex_partic_categ
                    WHERE pex_proposta_id = '$prop_id' ORDER BY pex_data_cad DESC");
  while ($pex = $sql->fetch(PDO::FETCH_ASSOC)) {
    $pex_id                  = $pex['pex_id'];
    $pex_proposta_id         = $pex['pex_proposta_id'];
    $pex_nome                = $pex['pex_nome'];
    $pex_email               = $pex['pex_email'];
    $pex_contato             = $pex['pex_contato'];
    $pex_partic_categ        = $pex['pex_partic_categ'];
    $pex_qual_partic_categ   = $pex['pex_qual_partic_categ'];
    $pex_qual_partic_categ   = $pex['pex_qual_partic_categ'];
    $pex_partic_perfil       = $pex['pex_partic_perfil'];
    $pex_outro_partic_perfil = $pex['pex_outro_partic_perfil'];
    $pex_carga_hora          = $pex['pex_carga_hora'];
    $pex_area_atuacao        = $pex['pex_area_atuacao'];
    $pex_nome_area_atuacao   = $pex['pex_nome_area_atuacao'];
    $pex_formacao            = $pex['pex_formacao'];
    $pex_lattes              = $pex['pex_lattes'];
    $pex_user_id             = $pex['pex_user_id'];
    $pex_data_cad            = $pex['pex_data_cad'];
    $pex_data_upd            = $pex['pex_data_upd'];
    // PERFIL PARTICIPANTE
    $us_pe_perfil            = $pex['us_pe_perfil'];
    // CURSO COORDENADOR
    $curso                   = $pex['cc_curso'];
    // CATEGORIA PARTICIPAÇÃO PROJETO
    $cpp_categoria           = $pex['cpp_categoria'];

    // CONFIGURAÇÃO DA FORMAÇÃO
    $form_prof = nl2br($pex_formacao);
    $form_prof = str_replace('<br />', '', $form_prof);

    // CONFIG. "CATEGORIA DE PARTICIPAÇÃO NO PROJETO"
    if ($pex_partic_categ == 11) {
      $pex_partic_categ = $pex_qual_partic_categ;
    } else {
      $pex_partic_categ = $cpp_categoria;
    }

    // CONFIG. "PERFIL DO PARTICIPANTE"
    if ($pex_partic_perfil == 9) {
      $pex_partic_perfil = $pex_outro_partic_perfil;
    } else {
      $pex_partic_perfil = $us_pe_perfil;
    }

    // CONFIG. "CURSO OU ÁREA DE ATUAÇÃO"
    if ($pex_area_atuacao == 20) {
      $pex_area_atuacao = $pex_nome_area_atuacao;
    } else {
      $pex_area_atuacao = $curso;
    }

    $html .= '<div class="cont_acordion">
          <legend><h5>' . $pex_nome . '</h5></legend> 

          <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">  
            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">E-mail</div>
                    <div class="conteudo_dados_tabela">
                    <p>' . $pex_email . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">Contato</div>
                    <div class="conteudo_dados_tabela">
                    <p>' . $pex_contato . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">CATEGORIA DE PARTICIPAÇÃO NO PROJETO</div>
                    <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $pex_partic_categ . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">PERFIL DO PARTICIPANTE</div>
                    <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $pex_partic_perfil . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">CURSO OU ÁREA DE ATUAÇÃO</div>
                    <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $pex_area_atuacao . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">HORAS DEDICADAS AO PROJETO</div>
                    <div class="conteudo_dados_tabela">
                    <p>' . $pex_carga_hora . ' hs</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">FORMAÇÃO PROFISSIONAL</div>
                    <div class="conteudo_dados_tabela">
                    <p>' . $pex_formacao . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">LINK DO CURRÍCULO LATTES</div>
                    <div class="conteudo_dados_tabela">
                    <p>' . $pex_lattes . '</p>
                  </div>
                </div>
              </td>
            </tr>

            </table>
            </div>';
  }
  if (empty($pex_id)) {
    $html .= '<div class="conteudo" style="padding: 10px 0px 10px 0px;">
                <p class="itens_array">NENHUMA EQUIPE EXECUTARO INFORMADA</p>
              <div>';
  }

  $html .= '</fieldset>';



  /*****************************************************************************************
                              PARCEIROS EXTERNOS / PATROCINADORES
   *****************************************************************************************/

  $html .= '<fieldset class="no-break">
            <legend><h4>PARCEIROS EXTERNOS / PATROCINADORES</h4></legend>';

  $sql = $conn->query("SELECT * FROM propostas_parceiro_externo WHERE ppe_proposta_id = '$prop_id' ORDER BY ppe_data_cad DESC");
  while ($pcp = $sql->fetch(PDO::FETCH_ASSOC)) {
    $ppe_id           = $pcp['ppe_id'];
    $ppe_proposta_id  = $pcp['ppe_proposta_id'];
    $ppe_nome         = $pcp['ppe_nome'];
    $ppe_email        = $pcp['ppe_email'];
    $ppe_contato      = $pcp['ppe_contato'];
    $ppe_cnpj         = $pcp['ppe_cnpj'];
    $ppe_responsavel  = $pcp['ppe_responsavel'];
    $ppe_area_atuacao = $pcp['ppe_area_atuacao'];
    $ppe_obs          = $pcp['ppe_obs'];
    $ppe_convenio     = $pcp['ppe_convenio'];


    $html .= '<div class="cont_acordion">
    <legend><h5>' . $ppe_nome . '</h5></legend> 
    <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td>
          <div class="conteudo">
            <div class="label">E-mail</div>
            <div class="conteudo_dados_tabela">
              <p>' . $ppe_email . '</p>
            </div>
          </div>
        </td>
        
        <td>
          <div class="conteudo">
            <div class="label">Contato</div>
            <div class="conteudo_dados_tabela">
              <p>' . $ppe_contato . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <td>
          <div class="conteudo">
            <div class="label">RESPONSÁVEL</div>
            <div class="conteudo_dados_tabela">
              <p class="itens_upper">' . $ppe_responsavel . '</p>
            </div>
          </div>
        </td>
        <td>
        <div class="conteudo">
          <div class="label">CURSO OU ÁREA DE ATUAÇÃO</div>
          <div class="conteudo_dados_tabela">
            <p class="itens_upper">' . $ppe_area_atuacao . '</p>
          </div>
        </div>
      </td>
      </tr>

      <tr>
        <td>
          <div class="conteudo">
            <div class="label">CNPJ</div>
            <div class="conteudo_dados_tabela">
              <p>' . $ppe_cnpj . '</p>
            </div>
          </div>
        </td>
      </tr> 

      <tr>
        <td colspan="2">
          <div class="conteudo">
            <div class="label">OBSERVAÇÃO</div>
            <div class="conteudo_dados_tabela">
              <p>' . $ppe_obs . '</p>
            </div>
          </div>
        </td>
      </tr>
    </table>';


    if (!empty($ppe_convenio)) {
      $html .= '<div class="conteudo">
                <hr>
                <p class="itens_array">EXISTÊNCIA DE CONVÊNIO OU ACORDO FORMALIZADO</p>
              <div>';
    }
    $html .= '</div>';
  }
  if (empty($ppe_id)) {
    $html .= '<div class="conteudo" style="padding: 10px 0px 10px 0px;">
              <p class="itens_array">NENHUM PARCEIRO/PATROCINADOR INFORMADO</p>
            <div>';
  }

  $html .= '</fieldset>';















  /*****************************************************************************************
                      MATERIAIS DE CONSUMO / LABORATÓRIO / EQUIPAMENTOS / MOBILIÁRIO
   *****************************************************************************************/

  $html .= '<fieldset class="no-break">
            <legend><h4>MATERIAIS DE CONSUMO / LABORATÓRIO / EQUIPAMENTOS / MOBILIÁRIO</h4></legend>

            <table class="dados_proposta_tabela" width="100%" border="1" cellpadding="0" cellspacing="0">
            <thead>
              <tr>
                <th scope="col">ITEM</th>
                <th scope="col">QUANTIDADE</th>
                <th scope="col">VALOR UNITÁRIO</th>
                <th scope="col">VALOR TOTAL</th>
              </tr>
            </thead>
            <tbody>';


  $total = 0;
  $sql = $conn->query("SELECT * FROM propostas_material_consumo
                      INNER JOIN conf_material_servico ON conf_material_servico.cms_id = propostas_material_consumo.pmc_material_consumo
                      WHERE pmc_proposta_id = '$prop_id' AND cms_natureza = 1 ORDER BY pmc_material_consumo ASC");
  while ($pmc = $sql->fetch(PDO::FETCH_ASSOC)) {
    $pmc_id               = $pmc['pmc_id'];
    $pmc_proposta_id      = $pmc['pmc_proposta_id'];
    $pmc_material_consumo = $pmc['pmc_material_consumo'];
    $pmc_quantidade       = $pmc['pmc_quantidade'];
    $pmc_user_id          = $pmc['pmc_user_id'];
    $pmc_data_cad         = $pmc['pmc_data_cad'];
    $pmc_data_upd         = $pmc['pmc_data_upd'];
    // MATERIAL SERVIÇO
    $cms_id               = $pmc['cms_id'];
    $cms_material_servico = $pmc['cms_material_servico'];
    $cms_natureza         = $pmc['cms_natureza'];
    //$cms_valor            = $pmc['cms_valor'];
    $cms_user_id          = $pmc['cms_user_id'];
    $cms_data_cad         = $pmc['cms_data_cad'];
    $cms_data_upd         = $pmc['cms_data_upd'];

    if (!empty($pmc['cms_valor'])) {
      $cms_valor = $pmc['cms_valor'];
      $cms_valor = str_replace(',', '', $cms_valor);
      $cms_valor = str_replace('.', '', $cms_valor);
    } else {
      $cms_valor = 0;
    }

    $pmc_total_unit = ($pmc_quantidade * floatval($cms_valor));
    $total += $pmc_total_unit; // Soma ao total
    //
    $valor_cms = substr_replace($cms_valor, '.', -2, 0);
    $valor_cms = number_format($valor_cms, 2, ",", ".");
    //
    $pmc_total_val_unit = substr_replace($pmc_total_unit, '.', -2, 0);
    $pmc_total_val_unit = number_format($pmc_total_val_unit, 2, ",", ".");
    //
    $valor_total = substr_replace($total, '.', -2, 0);
    $valor_total = number_format($valor_total, 2, ",", ".");



    $html .= '<tr>
                <td>' . $cms_material_servico . '</td>
                <td>' . $pmc_quantidade . '</td>
                <td>' . $valor_cms . '</td>
                <td>' . $pmc_total_val_unit . '</td>
              </tr>';
  }
  $html .= '<tr>';
  if (!empty($valor_total)) {
    $html .= '<td colspan="4" style="text-align: right;">Total: R$ ' . $valor_total . '</td>';
  }

  $html .= '</tr>
    </tbody>
  </table>

  </fieldset>';


  /*****************************************************************************************
                                      SERVIÇOS
   *****************************************************************************************/

  $html .= '<fieldset class="no-break">
            <legend><h4>SERVIÇOS</h4></legend>

            <table class="dados_proposta_tabela" width="100%" border="1" cellpadding="0" cellspacing="0">
              <thead>
                <tr>
                  <th scope="col">SERVIÇO</th>
                  <th scope="col">QUANTIDADE</th>
                  <th scope="col">VALOR UNITÁRIO</th>
                  <th scope="col">VALOR TOTAL</th>
                </tr>
              </thead>
              <tbody>';

  $total = 0;
  $sql = $conn->query("SELECT * FROM propostas_servico
                      INNER JOIN conf_material_servico ON conf_material_servico.cms_id = propostas_servico.ps_mat_serv_id
                      WHERE ps_proposta_id = '$prop_id' AND cms_natureza = 2 ORDER BY cms_material_servico ASC");
  while ($ps = $sql->fetch(PDO::FETCH_ASSOC)) {
    $ps_id               = $ps['ps_id'];
    $ps_proposta_id      = $ps['ps_proposta_id'];
    $ps_mat_serv_id      = $ps['ps_mat_serv_id'];
    $ps_quantidade       = $ps['ps_quantidade'];
    $ps_user_id          = $ps['ps_user_id'];
    $ps_data_cad         = $ps['ps_data_cad'];
    $ps_data_upd         = $ps['ps_data_upd'];
    // MATERIAL SERVIÇO
    $cms_id               = $ps['cms_id'];
    $cms_material_servico = $ps['cms_material_servico'];
    $cms_natureza         = $ps['cms_natureza'];
    //$cms_valor            = $pmc['cms_valor'];
    $cms_user_id          = $ps['cms_user_id'];
    $cms_data_cad         = $ps['cms_data_cad'];
    $cms_data_upd         = $ps['cms_data_upd'];

    if (!empty($ps['cms_valor'])) {
      $cms_valor = $ps['cms_valor'];
      $cms_valor = str_replace(',', '', $cms_valor);
      $cms_valor = str_replace('.', '', $cms_valor);
    } else {
      $cms_valor = 0;
    }

    $ps_total_unit = ($ps_quantidade * floatval($cms_valor));
    $total += $ps_total_unit; // Soma ao total
    //
    $valor_cms = substr_replace($cms_valor, '.', -2, 0);
    $valor_cms = number_format($valor_cms, 2, ",", ".");
    //
    $ps_total_val_unit = substr_replace($ps_total_unit, '.', -2, 0);
    $ps_total_val_unit = number_format($ps_total_val_unit, 2, ",", ".");
    //
    $valor_total = substr_replace($total, '.', -2, 0);
    $valor_total = number_format($valor_total, 2, ",", ".");

    $html .= '<tr>
      <td>' . $cms_material_servico . '</td>
      <td>' . $ps_quantidade . '</td>
      <td>' . $valor_cms . '</td>
      <td>' . $ps_total_val_unit . '</td>
    </tr>';
  }

  $html .= '<tr>';
  if (!empty($valor_total)) {
    $html .= '<td colspan="4" style="text-align: right;">Total: R$ ' . $valor_total . '</td>';
  }
  $html .= '</tr>
    </tbody>
  </table>

      <div class="conteudo" style="padding: 12px 0;">
        <div class="label">DESCREVA OUTROS CUSTOS ENVOLVIDOS PARA IMPLANTAÇÃO DE SEU PROJETO</div>
        <div class="conteudo_dados_tabela">
          <p>' . $prop_custos . '</p>
        </div>
      </div>
      

  </fieldset>';



  /*****************************************************************************************
                            RECURSOS NECESSÁRIOS
   *****************************************************************************************/

  $html .= '
  <fieldset class="no-break">
  <legend><h4>RECURSOS NECESSÁRIOS</h4></legend>
  <div class="conteudo">
    <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0"> 
      <tr>
        <td">
          <div class="conteudo">
            <div class="label">DIAS DA SEMANA</div>
          </div>
        </td>
      </tr>

      <div class="conteudo_dados">';
  try {
    $prop_recursos = rtrim($prop_recursos, ', '); // RETIRA A ULTIMA VIRGULA DO ARRAY
    $sql_rec = "SELECT rec_id, rec_recurso FROM conf_recursos WHERE rec_id IN ($prop_recursos) ORDER BY rec_recurso ASC";
    $stmt = $conn->prepare($sql_rec);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($resultados) > 0) {
      foreach ($resultados as $result) {
        $html .= '<li class="itens_array">' . $result['rec_recurso']  . '</li>';
      }
    } else {
      $html .= '<p>Nenhuma recurso selecionada!</p>';
    }
  } catch (PDOException $e) {
    $e->getMessage();
  }

  $html .= '
      </div>

        <tr> 
          <td>
            <div class="conteudo">
              <div class="label">Descrever a organização do espaço e a dinâmica da atividade</div>
              <div class="conteudo_dados_tabela">
                <p>' . $prop_desc_atividade . '</p>
              </div>
            </div>
            
          </td>
        </tr>
        <tr> 
          <td>
            <div class="conteudo">
              <div class="label">Recursos áudio e vídeo</div>
              <div class="conteudo_dados_tabela">
                <p>' . $prop_rec_audio_video . '</p>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <div class="conteudo">
              <div class="label">Outros</div>
              <div class="conteudo_dados_tabela">
                <p>' . $prop_outros . '</p>
              </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </fieldset>';


  /*****************************************************************************************
                      DIVULGAÇÃO E PROMOÇÃO DA ATIVIDADE
   *****************************************************************************************/

  $html .= '<fieldset class="no-break">
            <legend><h4>DIVULGAÇÃO E PROMOÇÃO DA ATIVIDADE</h4></legend>';

  if (!empty($prop_card)) {
    $html .= '<div class="conteudo"> 
              <div class="conteudo_dados">
                <p class="itens_array">SERÁ NECESSÁRIO CARD PARA DIVULGAÇÃO</p>
              </div>
            </div>

  <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>
        <div class="conteudo">
          <div class="label">Descreva o texto para divulgação dessas atividades nas redes sociais</div>
            <div class="conteudo_dados_tabela">
            <p>' . $prop_texto_divulgacao . '</p>
          </div>
        </div>
      </td>
    </tr>
    <tr>
      <td>
        <div class="conteudo">
          <div class="label">Quais os diferencias do curso ofertado</div>
            <div class="conteudo_dados_tabela">
            <p>' . $prop_diferenciais . '</p>
          </div>
        </div>
      </td>
    </tr>  
  </table>';
  }

  if (!empty($prop_parceria)) {
    $html .= '<hr>
              <div class="conteudo"> 
                <div class="conteudo_dados">
                  <p class="itens_array">O PROJETO TEM PARCERIAS</p>
                </div>
              </div>';
  }

  $html .= '<table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">

              <tr>
                <td>
                  <div class="conteudo">
                    <div class="label">Demais informações</div>
                      <div class="conteudo_dados_tabela">
                      <p>' . $prop_informacoes . '</p>
                    </div>
                  </div>
                </td>
              </tr>

            </table>
          </fieldset>';


  /*****************************************************************************************
                              CADASTRO DE DISCIPLINAS / MÓDULO
   *****************************************************************************************/
  if ($prop_tipo == 1) {

    $html .= '<fieldset class="no-break">
            <legend><h4>CADASTRO DE DISCIPLINAS / MÓDULO</h4></legend>';

    $sql = $conn->query("SELECT * FROM propostas_cursos_modulo
                        INNER JOIN conf_tipo_espaco_organizacao ON conf_tipo_espaco_organizacao.esporg_id = propostas_cursos_modulo.prop_cmod_organizacao
                        INNER JOIN forma_pagamento ON forma_pagamento.for_pag_id = propostas_cursos_modulo.prop_cmod_forma_pagamento
                        WHERE prop_cmod_prop_id = '$prop_id' ORDER BY prop_cmod_data_cad DESC");
    while ($prop_cmod = $sql->fetch(PDO::FETCH_ASSOC)) {
      $prop_cmod_id                = $prop_cmod['prop_cmod_id'];
      $prop_cmod_prop_id           = $prop_cmod['prop_cmod_prop_id'];
      $prop_cmod_tipo_docente      = $prop_cmod['prop_cmod_tipo_docente'];
      $prop_cmod_nome_docente      = $prop_cmod['prop_cmod_nome_docente'];
      $prop_cmod_titulo            = $prop_cmod['prop_cmod_titulo'];
      $prop_cmod_assunto           = $prop_cmod['prop_cmod_assunto'];
      $prop_cmod_data_hora         = $prop_cmod['prop_cmod_data_hora'];
      $prop_cmod_organizacao       = $prop_cmod['prop_cmod_organizacao'];
      $prop_cmod_outra_organizacao = $prop_cmod['prop_cmod_outra_organizacao'];
      $prop_cmod_forma_pagamento   = $prop_cmod['prop_cmod_forma_pagamento'];
      $prop_cmod_curriculo         = $prop_cmod['prop_cmod_curriculo'];
      $prop_cmod_user_id           = $prop_cmod['prop_cmod_user_id'];
      $prop_cmod_data_cad          = $prop_cmod['prop_cmod_data_cad'];
      $prop_cmod_data_upd          = $prop_cmod['prop_cmod_data_upd'];
      // ORGANIZAÇÃO
      $esporg_id                   = $prop_cmod['esporg_id'];
      $esporg_espaco_organizacao   = $prop_cmod['esporg_espaco_organizacao'];
      // PAGAMENTO
      $for_pag_id                  = $prop_cmod['for_pag_id'];
      $for_pag_forma_pagamento     = $prop_cmod['for_pag_forma_pagamento'];

      if ($prop_cmod_tipo_docente == 1) {
        $prop_cmod_tipo_docente_titulo = 'Docente Interno';
      } else {
        $prop_cmod_tipo_docente_titulo = 'Docente externo';
      }

      // SE TENTAR VISULIZAR PAGINA SEM O CADASTRO DA PROPOSTAR TER SIDO CONCLUÍDA, DESLOGA USUÁRIO
      if (empty($prop_cmod_id)) {
        header("Location: sair.php");
      }

      $html .= '<div class="cont_acordion">
                <legend><h5>' . $prop_cmod_titulo . '</h5></legend> 
                <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0" style="margin: 12px 0px 0px 0px;">
                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">' . $prop_cmod_tipo_docente_titulo . '</div>
                        <div class="conteudo_dados_tabela">
                          <p class="itens_upper">' . $prop_cmod_nome_docente . '</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">ASSUNTOS</div>
                        <div class="conteudo_dados_tabela">
                          <p>' . $prop_cmod_assunto . '</p>
                        </div>
                      </div>
                    </td>    
                  </tr>
                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">Datas e Horários</div>
                        <div class="conteudo_dados_tabela">
                          <p>' . $prop_cmod_data_hora . '</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">FORMATO DE ORGANIZAÇÃO DA SALA</div>
                        <div class="conteudo_dados_tabela">
                          <p class="itens_upper">' . $esporg_espaco_organizacao . '</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">Descreva a Organização da Sala</div>
                        <div class="conteudo_dados_tabela">
                          <p>' . $prop_cmod_outra_organizacao . '</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">Forma de Pagamento Docente</div>
                        <div class="conteudo_dados_tabela">
                          <p class="itens_upper">' . $for_pag_forma_pagamento . '</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <div class="conteudo">
                        <div class="label">Currículo Resumido</div>
                        <div class="conteudo_dados_tabela">
                          <p>' . $prop_cmod_curriculo . '</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                </table>';

      $html .= '</div>';
    }
    $html .= '</fieldset>';
  }









  /*****************************************************************************************
                                  EVENTOS CIENTÍFICOS
   *****************************************************************************************/
  if ($prop_tipo == 2) {

    $html .= '<fieldset class="no-break">
              <legend><h4>SOBRE O EVENTO</h4></legend>';

    $html .= '
    <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">

    <tr>
        <td>
          <div class="conteudo">
            <div class="label">O EVENTO TERÁ PATROCÍNIO?</div>
              <div class="conteudo_dados_tabela">
              <p>' . $prop_event_patrocinio . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <td>
          <div class="conteudo">
            <div class="label">QUAIS PATROCÍNIO?</div>
              <div class="conteudo_dados_tabela">
              <p>' . $prop_event_qual_patrocinio . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <td>
          <div class="conteudo">
            <div class="label">O evento terá parceria?</div>
              <div class="conteudo_dados_tabela">
              <p>' . $prop_event_parceria . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <td>
          <div class="conteudo">
            <div class="label">QUAIS PARCERIAS?</div>
              <div class="conteudo_dados_tabela">
              <p>' . $prop_event_qual_parceria . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <td>
          <div class="conteudo">
            <div class="label">NOME E CONTATO ADICIONAL (PESSOA DE CONTATO EM CASO DE INDISPONIBILIDADE DO COORDENADOR GERAL)</div>
              <div class="conteudo_dados_tabela">
              <p>' . $prop_event_contatos . '</p>
            </div>
          </div>
        </td>
      </tr>';

    if (!empty($prop_event_sorteio)) {
      $html .= '<hr>
              <p class="itens_array">HAVERÁ SORTEIO DURANTE O EVENTO</p>';
    }

    $html .= '</table>
            </fieldset>';
  }

















  /*****************************************************************************************
                                  PROGRAMAS
   *****************************************************************************************/
  if ($prop_tipo == 3) {
    $html .= '<fieldset class="no-break">
            <legend><h4>TIPO DO PROGRAMA</h4></legend>
            <div class="conteudo">
                <div class="conteudo">
              <p class="itens_array">' . $tipprog_programa . '</p>
              </div>
            </div>
          </fieldset>';

    $html .= '<div class="conteudo">
                <div class="label">TIPO DE ' . $ctp_tipo . '</div>
                <div class="conteudo_dados_tabela">
                  <p class="itens_upper">' . $ctp_categoria . '</p>
                </div>
              </div>';

    if ($prop_prog_tipo == 2) {
      $html .= '<fieldset class="no-break">
          <legend><h4>PROGRAMAS INTEGRADO AO ENSINO</h4></legend>
          <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">            
            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">DOCENTE ORIENTADOR</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $prop_prog_docente . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">ÁREA DE ATUAÇÃO</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $at_area_tematica . '</p>
                  </div>
                </div>
              </td>
            </tr> 
            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">LOCAL DE ATUAÇÃO</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $prop_prog_local_atuacao . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">VALOR DE INSCRIÇÃO</div>
                  <div class="conteudo_dados_tabela">
                    <p>R$ ' . $prop_prog_valor_inscricao . '</p>
                  </div>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">DATA DE INÍCIO</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . date("d/m/Y", strtotime($prop_prog_data_inicio)) . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">DATA DE FIM</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . date("d/m/Y", strtotime($prop_prog_data_fim)) . '</p>
                  </div>
                </div>
              </td>
            </tr>
            </table>
        </fieldset>';
    }

    $html .= '<fieldset class="no-break">
          <legend><h4>INFORMAÇÕES ADICIONAIS</h4></legend>
          <div class="conteudo">
            <div class="conteudo_dados">
              <p>' . $prop_prog_obs . '</p>
            </div>
          </div>
        </fieldset>';
  }
}








/*****************************************************************************************
                                      PARCERIAS
 *****************************************************************************************/
if ($prop_tipo == 4) {
  $sql = $conn->query("SELECT * FROM propostas
                      LEFT JOIN tipo_empresa ON tipo_empresa.tipemp_id = propostas.prop_parc_tipo_empresa
                      LEFT JOIN tipo_parceria ON tipo_parceria.tiparc_id = propostas.prop_parc_tipo_parceria
                      LEFT JOIN unidades ON unidades.uni_id = propostas.prop_parc_campus_atividade
                      LEFT JOIN tipo_espaco ON tipo_espaco.tipesp_id = propostas.prop_parc_tipo_espaco
                      LEFT JOIN conf_tipo_espaco_organizacao ON conf_tipo_espaco_organizacao.esporg_id = propostas.prop_parc_organizacao_espaco
                      WHERE prop_id = '$prop_id'");
  while ($prop_parc = $sql->fetch(PDO::FETCH_ASSOC)) {
    $prop_parc_nome_empresa = $prop_parc['prop_parc_nome_empresa'];
    $prop_parc_tipo_empresa = $prop_parc['prop_parc_tipo_empresa'];
    $prop_parc_tipo_outro = $prop_parc['prop_parc_tipo_outro'];
    $prop_parc_orgao_empresa = $prop_parc['prop_parc_orgao_empresa'];
    $prop_parc_email = $prop_parc['prop_parc_email'];
    $prop_parc_telefone = $prop_parc['prop_parc_telefone'];
    $prop_parc_cep = $prop_parc['prop_parc_cep'];
    $prop_parc_logradouro = $prop_parc['prop_parc_logradouro'];
    $prop_parc_numero = $prop_parc['prop_parc_numero'];
    $prop_parc_bairro = $prop_parc['prop_parc_bairro'];
    $prop_parc_municipio = $prop_parc['prop_parc_municipio'];
    $prop_parc_estado = $prop_parc['prop_parc_estado'];
    $prop_parc_pais = $prop_parc['prop_parc_pais'];
    $prop_parc_responsavel = $prop_parc['prop_parc_responsavel'];
    $prop_parc_cargo = $prop_parc['prop_parc_cargo'];
    $prop_parc_contato_referencia = $prop_parc['prop_parc_contato_referencia'];
    $prop_parc_possui_convenio = $prop_parc['prop_parc_possui_convenio'];
    $prop_parc_tipo_parceria = $prop_parc['prop_parc_tipo_parceria'];
    $prop_parc_titulo_atividade = $prop_parc['prop_parc_titulo_atividade'];
    $prop_parc_objetivo_atividade = $prop_parc['prop_parc_objetivo_atividade'];
    $prop_parc_local_atividade = $prop_parc['prop_parc_local_atividade'];
    $prop_parc_tipo_espaco = $prop_parc['prop_parc_tipo_espaco'];
    $prop_parc_campus_atividade = $prop_parc['prop_parc_campus_atividade'];
    $prop_parc_carga_hora = $prop_parc['prop_parc_carga_hora'];
    $prop_parc_data_atividade = $prop_parc['prop_parc_data_atividade'];
    $prop_parc_hora_atividade_inicial = $prop_parc['prop_parc_hora_atividade_inicial'];
    $prop_parc_hora_atividade_final = $prop_parc['prop_parc_hora_atividade_final'];
    $prop_parc_numero_participantes = $prop_parc['prop_parc_numero_participantes'];
    $prop_parc_recursos_necessarios = $prop_parc['prop_parc_recursos_necessarios'];
    $prop_parc_beneficios = $prop_parc['prop_parc_beneficios'];
    $prop_parc_beneficios_quantidade = $prop_parc['prop_parc_beneficios_quantidade'];
    $prop_parc_organizacao_espaco = $prop_parc['prop_parc_organizacao_espaco'];
    $prop_parc_comentarios = $prop_parc['prop_parc_comentarios'];
    // TIPO EMPRESA
    $tipemp_tipo_empresa             = $prop_parc['tipemp_tipo_empresa'];
    // TIPO PARCERIA
    $tiparc_tipo_parceria            = $prop_parc['tiparc_tipo_parceria'];
    // TIPO UNIDADE
    $uni_unidade                     = $prop_parc['uni_unidade'];
    // TIPO ESPAÇO
    $tipesp_tipo_espaco              = $prop_parc['tipesp_tipo_espaco'];
    // PARCERIA ORGANIZAÇÃO
    $esporg_espaco_organizacao       = $prop_parc['esporg_espaco_organizacao'];
  }

  $html .= '<fieldset class="no-break">
            <legend><h4>IDENTIFICAÇÃO DO SOLICITANTE / PARCEIRO</h4></legend>
            <div class="conteudo">

            <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">NOME DA EMPRESA/INSTITUIÇÃO</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $prop_parc_nome_empresa . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">E-MAIL</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_email . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">CONTATO</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_telefone . '</p>
                  </div>
                </div>
              </td>
            </tr> 

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">TIPO DE EMPRESA</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $tipemp_tipo_empresa . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">Tipo</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $prop_parc_tipo_outro . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
            <td>
              <div class="conteudo">
                <div class="label">Orgão</div>
                <div class="conteudo_dados_tabela">
                  <p class="itens_array">' . $prop_parc_orgao_empresa . '</p>
                </div>
              </div>
            </td>
            <td>
              <div class="conteudo">
                <div class="label">CEP</div>
                <div class="conteudo_dados_tabela">
                  <p>' . $prop_parc_cep . '</p>
                </div>
              </div>
            </td>

            </tr>

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">LOGRADOURO</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $prop_parc_logradouro . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">NÚMERO</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_numero . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">BAIRRO</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $prop_parc_bairro . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">MUNICÍPIO</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $prop_parc_municipio . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
            <td>
              <div class="conteudo">
                <div class="label">Estado</div>
                <div class="conteudo_dados_tabela">
                  <p class="itens_array">' . $prop_parc_estado . '</p>
                </div>
              </div>
            </td>
            <td>
              <div class="conteudo">
                <div class="label">País</div>
                <div class="conteudo_dados_tabela">
                  <p class="itens_upper">' . $prop_parc_pais . '</p>
                </div>
              </div>
            </td>
            </tr>

            <tr>
            <td>
              <div class="conteudo">
                <div class="label">RESPONSÁVEL PELA SOLICITAÇÃO</div>
                <div class="conteudo_dados_tabela">
                  <p class="itens_array">' . $prop_parc_responsavel . '</p>
                </div>
              </div>
            </td>
            <td>
              <div class="conteudo">
                <div class="label">INFORME O SEU CARGO NA INSTITUIÇÃO</div>
                <div class="conteudo_dados_tabela">
                  <p class="itens_upper">' . $prop_parc_cargo . '</p>
                </div>
              </div>
            </td>
            </tr>

            <tr>
            <td>
              <div class="conteudo">
                <div class="label">PESSOA DE REFERÊNCIA NA BAHIANA</div>
                <div class="conteudo_dados_tabela">
                  <p class="itens_array">' . $prop_parc_contato_referencia . '</p>
                </div>
              </div>
            </td>
            </tr>';

  if (empty($prop_parc_possui_convenio)) {
    $html .= '<td colspan="2">
              <hr>
                <div class="conteudo">
                  <div class="conteudo_dados_tabela">
                  <p class="itens_array">EXISTÊNCIA DE CONVÊNIO OU ACORDO FORMALIZADO</p>
                  </div>
                </div>
              </td>';
  }

  $html .= '</div>
            </fieldset>';



  $html .= '<fieldset class="no-break">
            <legend><h4>IDENTIFICAÇÃO DA PARCERIA</h4></legend>
            <div class="conteudo">

            <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
            
            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">TIPO DE PARCERIA</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $tiparc_tipo_parceria . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">TÍTULO DA ATIVIDADE</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $prop_parc_titulo_atividade . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">INFORME O OBJETIVO DA ATIVIDADE</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_objetivo_atividade . '</p>
                  </div>
                </div>
              </td>
            </tr> 

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">SE ATIVIDADE EXTERNA INFORME O LOCAL</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $prop_parc_local_atividade . '</p>
                  </div>
                </div>
              </td>
              </tr> 

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">INFORME O TIPO DE ESPAÇO QUE VOCÊ DESEJA RESERVAR</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $tipesp_tipo_espaco . '</p>
                  </div>
                </div>
              </td>
            </tr> 

            <tr>
              <td colspan="2">
                <div class="conteudo">
                  <div class="label">SE A ATIVIDADE FOR PRESENCIAL, INFORME O CAMPUS PREFERIDO</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_upper">' . $uni_unidade . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>
              <td>
                <div class="conteudo">
                  <div class="label">CARGA HORÁRIA (HORA)</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_carga_hora . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">NÚMERO ESTIMADO DE PARTICIPANTES?</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_numero_participantes . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr>              
              <td>
                <div class="conteudo">
                  <div class="label">DATA DA ATIVIDADE</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . date("d/m/Y", strtotime($prop_parc_data_atividade)) . '</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="conteudo">
                  <div class="label">HORA INICIAL</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_hora_atividade_inicial . '</p>
                  </div>
                </div>
              </td>
            </tr>
            <tr>              
              <td>
                <div class="conteudo">
                  <div class="label">HORA FINAL</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_hora_atividade_final . '</p>
                  </div>
                </div>
              </td>
            </tr>
            </table>

          <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr> 
              <td>
                <div class="conteudo">
                  <div class="label">DESCREVA OS RECURSOS NECESSÁRIOS PARA REALIZAÇÃO DA ATIVIDADE</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_recursos_necessarios . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr> 
              <td>
                <div class="conteudo">
                  <div class="label">NO CASO DE ATIVIDADES PRESENCIAIS, SELECIONE COMO SERÁ A ORGANIZAÇÃO DO ESPAÇO</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $esporg_espaco_organizacao . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr> 
              <td>
                <div class="conteudo">
                  <div class="label">HAVERÁ DISPONIBILIDADE DE BENEFÍCIOS (EX: BOLSAS OU VAGAS) PARA A COMUNIDADE ACADÊMICA DA ESCOLA BAHIANA?</div>
                  <div class="conteudo_dados_tabela">
                    <p class="itens_array">' . $prop_parc_beneficios . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr> 
              <td>
                <div class="conteudo">
                  <div class="label">ESPECIFIQUE AQUI A QUANTIDADE DE BENEFÍCIOS (EX: BOLSAS OU VAGAS) PARA A COMUNIDADE ACADÊMICA DA ESCOLA BAHIANA?</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_beneficios_quantidade . '</p>
                  </div>
                </div>
              </td>
            </tr>

            <tr> 
              <td>
                <div class="conteudo">
                  <div class="label">DESCREVA AQUI OUTRAS INFORMAÇÕES NECESSÁRIAS PARA O ATENDIMENTO DA SUA SOLICITAÇÃO</div>
                  <div class="conteudo_dados_tabela">
                    <p>' . $prop_parc_comentarios . '</p>
                  </div>
                </div>
              </td>
            </tr>
          </table>

          </div>
          </fieldset>';
}






















/*****************************************************************************************
                                EXTENSÃO COMUNITÁRIA
 *****************************************************************************************/
if ($prop_tipo == 5) {
  $html .= '<fieldset class="no-break">
            <legend><h4>RESPONSÁVEIS</h4></legend>
            
            <table class="dados_proposta" width="100%" border="1" cellpadding="0" cellspacing="0">
            <thead>
              <tr>
                <th scope="col">NOME</th>
                <th scope="col">CONTATO</th>
                <th scope="col">E-MAIL</th>
              </tr>
            </thead>
            <tbody>';

  $total = 0;
  $sql = $conn->query("SELECT * FROM propostas_extensao_responsavel_contato WHERE prc_proposta_id = '$prop_id' ORDER BY prc_nome DESC");
  while ($prc = $sql->fetch(PDO::FETCH_ASSOC)) {
    $prc_id          = $prc['prc_id'];
    $prc_proposta_id = $prc['prc_proposta_id'];
    $prc_nome        = $prc['prc_nome'];
    $prc_contato     = $prc['prc_contato'];
    $prc_email       = $prc['prc_email'];


    $html .= '<tr>
              <td>' . $prc_nome . '</td>
              <td>' . $prc_contato . '</td>
              <td>' . $prc_email . '</td>
            </tr>';
  }

  $html .= '</tr>
      </tbody>
    </table>
    
    </fieldset>
    
    <fieldset class="no-break">
    <legend><h4>PROGRAMAS DE EXTENSÃO COMUNITÁRIA</h4></legend>
      <div class="conteudo">
        <div class="conteudo_dados_tabela">';

  $sql = $conn->query("SELECT cec_id, cec_extensao_comunitaria, cec_desc FROM conf_extensao_comunitaria WHERE cec_id = $prop_ext_tipo_programa");
  while ($cec = $sql->fetch(PDO::FETCH_ASSOC)) {

    $html .= '<p>• ' . $cec['cec_extensao_comunitaria'] . '</p>';
    $html .= '<span>' . $cec['cec_desc'] . '</span>';
  }
  $html .= '</div>
      </div>
    </fieldset>    
    
    <fieldset class="no-break">
    <legend><h4>PROGRAMAS DE EXTENSÃO COMUNITÁRIA</h4></legend>
      <div class="conteudo">
        <div class="conteudo_dados_tabela">
          <p>' . $tes_evento_social . '</p>
        </div>
      </div>
    </fieldset>    
    
    <fieldset class="no-break">
      <legend><h4>INFORMAÇÕES ADICIONAIS</h4></legend>
      <div class="conteudo">

      <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
      
      <tr>
        <td colspan="2">
          <div class="conteudo">
            <div class="label">INSTITUIÇÃO/COMUNIDADE ATENDIDA</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_inst_atendida . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <div class="conteudo">
            <div class="label">ATIVIDADES QUE SERÃO DESENVOLVIDAS DURANTE A AÇÃO</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_atividades . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <div class="conteudo">
            <div class="label">DATAS E HORÁRIOS AS ATIVIDADES</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_datas_horas . '</p>
            </div>
          </div>
        </td>
      </tr> 

      <tr>
        <td colspan="2">
          <div class="conteudo">
            <div class="label">MOBILIÁRIO E EQUIPAMENTOS</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_mob_equipamento . '</p>
            </div>
          </div>
        </td>
        </tr> 

      <tr>
        <td colspan="2">
          <div class="conteudo">
            <div class="label">DESCREVA COMO SERÁ A DINÂMICA DO ESPAÇO</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_dinamica . '</p>
            </div>
          </div>
        </td>
      </tr> 

      <tr>
        <td>
          <div class="conteudo">
            <div class="label">QUANTIDADE PREVISTA DE ATENDIMENTOS</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_quant_atendimento . '</p>
            </div>
          </div>
        </td>
        <td>
          <div class="conteudo">
            <div class="label">FORMA DE INGRESSO DO ALUNO PARTICIPANTE</div>
            <div class="conteudo_dados_tabela">
              <p class="itens_array">' . $tip_tipo_ingresso . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr>
        <td>
          <div class="conteudo">
            <div class="label">VALOR DA BOLSA</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_valor_bolsa . '</p>
            </div>
          </div>
        </td>
      </tr>

      </table>

    <table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr> 
        <td>
          <div class="conteudo">
            <div class="label">QUAL ATENDIMENTO SERÁ OFERTADO</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_atendimento_ofertado . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr> 
        <td>
          <div class="conteudo">
            <div class="label">QUAL IMPACTO SOCIAL ESPERADO</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_impacto_social . '</p>
            </div>
          </div>
        </td>
      </tr>

      <tr> 
        <td>
          <div class="conteudo">
            <div class="label">COMENTÁRIOS E SOLICITAÇÕES COMPLEMENTARES</div>
            <div class="conteudo_dados_tabela">
              <p>' . $prop_ext_obs . '</p>
            </div>
          </div>
        </td>
      </tr>
    </table>

    </div>
    </fieldset>';
}






























/*****************************************************************************************
                              PARECER DA ANÁLISE
 *****************************************************************************************/

$html .= '<fieldset class="no-break">
            <legend><h4>PARECER DA ANÁLISE</h4></legend>
          <div class="conteudo">';

try {
  $query = "SELECT * FROM propostas_analise_parecer
            INNER JOIN admin ON admin.admin_id = propostas_analise_parecer.prop_parecer_user_id
            WHERE prop_parecer_prop_id = :prop_id";
  $statement = $conn->prepare($query);
  $statement->bindParam(':prop_id', $prop_id);
  $statement->execute();
  $prop_parecer = $statement->fetch(PDO::FETCH_ASSOC);
  if ($prop_parecer) {
    $prop_parecer_id       = $prop_parecer['prop_parecer_id'];
    $prop_parecer_prop_id  = $prop_parecer['prop_parecer_prop_id'];
    $prop_parecer_obs      = $prop_parecer['prop_parecer_obs'];
    $prop_parecer_user_id  = $prop_parecer['prop_parecer_user_id'];
    $prop_parecer_data_cad = $prop_parecer['prop_parecer_data_cad'];
    $prop_parecer_data_upd = $prop_parecer['prop_parecer_data_upd'];
    // ADMIN
    $admin_nome            = $prop_parecer['admin_nome'];

    // CONFIGURAÇÃO DA FORMATAÇÃO
    $prop_parecer_obs_trat = nl2br($prop_parecer_obs);
    $prop_parecer_obs_trat = str_replace('<br />', '', $prop_parecer_obs_trat);
    $prop_parecer_obs_trat = str_replace('"', '&quot;', $prop_parecer_obs_trat); // MOSTRA ASPAS DUPLAS

    $html .= '<table class="dados_proposta" width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td>
                    <div class="conteudo">
                      <div class="label">ÚLTIMA ATUALIZAÇÃO</div>
                      <div class="conteudo_dados_tabela">
                        <p>' . date("d/m/Y H:i", strtotime($prop_parecer_data_upd)) . '</p>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="conteudo">
                      <div class="label">POR</div>
                      <div class="conteudo_dados_tabela">
                        <p class="itens_upper">' . $admin_nome . '</p>
                      </div>
                    </div>
                  </td>
                </tr> 
                <tr>
                  <td colspan="2">
                    <div class="conteudo">
                      <div class="label">Parecer</div>
                      <div class="conteudo_dados_tabela">
                        <p>' . $prop_parecer_obs . '</p>
                      </div>
                    </div>
                  </td>
                </tr>
              </table>';
  } else {
    $html .= '<div class="conteudo">
                <p class="itens_array">NENHUM PARECER</p>
              <div>';
  }
} catch (PDOException $e) {
  echo "Erro: " . $e->getMessage();
}

$html .= '</div>
</fieldset>';




















$html .= ' 
          </main>
              </body>
            </html>';

$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4');

$dompdf->render();
$dompdf->stream(
  "proposta_" . $prop_codigo . ".pdf",
  array(
    "Attachment" => false
  )
);
