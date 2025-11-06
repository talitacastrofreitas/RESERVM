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

try {
  $cert_id        = base64_decode($_GET['cert']);
  $cert_categoria = base64_decode($_GET['c']);
  $sql = "SELECT * FROM propostas
          INNER JOIN propostas_categorias ON propostas_categorias.propc_id = propostas.prop_tipo
          INNER JOIN usuarios ON usuarios.user_id = propostas.prop_user_id
          LEFT JOIN inscricoes ON inscricoes.insc_prop_id COLLATE SQL_Latin1_General_CP1_CI_AI = propostas.prop_id
          --LEFT JOIN inscricoes_categorias ON inscricoes_categorias.inscc_id = inscricoes.insc_categoria
          INNER JOIN certificado ON certificado.cert_prop_id COLLATE SQL_Latin1_General_CP1_CI_AI = propostas.prop_id
          INNER JOIN certificado_arquivo ON certificado_arquivo.cert_arq_id = certificado.cert_id
          INNER JOIN inscricoes_categorias ON inscricoes_categorias.inscc_id = certificado.cert_categoria
          WHERE cert_categoria = :cert_categoria AND cert_id = :cert_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':cert_id', $cert_id, PDO::PARAM_STR);
  $stmt->bindParam(':cert_categoria', $cert_categoria, PDO::PARAM_INT);
  $stmt->execute();
  $prop = $stmt->fetch(PDO::FETCH_ASSOC);

  // SE DADOS NÃO FOREM ENCONTRADOS, FECHA JANELA
  if (empty($prop)) {
    echo '<script>window.close();</script>';
    exit;
  } else {
    // PROPOSTA
    $prop_id                = $prop['prop_id'];
    $prop_tipo              = $prop['prop_tipo'];
    $prop_codigo            = $prop['prop_codigo'];
    $prop_titulo            = $prop['prop_titulo'];
    // PROPOSTA CATEGORIAS
    $propc_id               = $prop['propc_id'];
    $propc_categoria        = $prop['propc_categoria'];
    // USUARIOS
    $user_nome              = $prop['user_nome'];
    $user_nome_social       = $prop['user_nome_social'];
    // INSCRIÇÃO
    $insc_id                = $prop['insc_id'];
    $insc_prop_id           = $prop['insc_prop_id'];
    $insc_categoria         = $prop['insc_categoria'];
    $insc_codigo            = $prop['insc_codigo'];
    $insc_nome              = $prop['insc_nome'];
    $insc_cpf               = $prop['insc_cpf'];
    $insc_email             = $prop['insc_email'];
    $insc_contato           = $prop['insc_contato'];
    $insc_tipo              = $prop['insc_tipo'];
    $insc_titulo            = $prop['insc_titulo'];
    $insc_nome_coautor      = $prop['insc_nome_coautor'];
    $insc_user_id           = $prop['insc_user_id'];
    $insc_data_cad          = $prop['insc_data_cad'];
    $insc_data_upd          = $prop['insc_data_upd'];
    // CERTIFICADO
    $cert_id                = $prop['cert_id'];
    $cert_prop_id           = $prop['cert_prop_id'];
    $cert_categoria         = $prop['cert_categoria'];
    $cert_nome_comissao     = $prop['cert_nome_comissao'];
    $cert_texto             = $prop['cert_texto'];
    $cert_titulo_trabalho   = $prop['cert_titulo_trabalho'];
    $cert_area_tematica     = $prop['cert_area_tematica'];
    $cert_autores           = $prop['cert_autores'];
    $cert_coautores         = $prop['cert_coautores'];
    $cert_modalidade        = $prop['cert_modalidade'];
    $cert_conteudo_programa = $prop['cert_conteudo_programa'];
    $cert_data_inicio       = $prop['cert_data_inicio'];
    $cert_data_fim          = $prop['cert_data_fim'];
    $cert_carga             = $prop['cert_carga'];
    $cert_user_id           = $prop['cert_user_id'];
    $cert_data_cad          = $prop['cert_data_cad'];
    $cert_data_upd          = $prop['cert_data_upd'];
    // INSCRIÇÕES CARTEGORIAS
    $inscc_id               = $prop['inscc_id'];
    $inscc_categoria        = $prop['inscc_categoria'];
    // ARQUIVO
    $cert_arq_id            = $prop['cert_arq_id'];
    $cert_arq_prop_id       = $prop['cert_arq_prop_id'];
    $cert_arq_categoria     = $prop['cert_arq_categoria'];
    $cert_arq_arquivo       = $prop['cert_arq_arquivo'];
    $cert_arq_user_id       = $prop['cert_arq_user_id'];
    $cert_arq_data_cad      = $prop['cert_arq_data_cad'];
  }
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

$cert_data_inicio_dia = date("d", strtotime($cert_data_inicio));
$cert_data_inicio_mes = date("m", strtotime($cert_data_inicio));
$cert_data_inicio_ano = date("Y", strtotime($cert_data_inicio));
//
$cert_data_fim_dia = date("d", strtotime($cert_data_fim));
$cert_data_fim_mes = date("m", strtotime($cert_data_fim));
$cert_data_fim_ano = date("Y", strtotime($cert_data_fim));
//
$cert_data_inicio = $cert_data_inicio_dia . ' de ' . traduzirMes($cert_data_inicio_mes) . ' de ' . $cert_data_inicio_ano;
$cert_data_fim = $cert_data_fim_dia . ' de ' . traduzirMes($cert_data_fim_mes) . ' de ' . $cert_data_fim_ano;
function traduzirMes($mes)
{
  $meses = array(
    '01' => 'janeiro',
    '02' => 'fevereiro',
    '03' => 'março',
    '04' => 'abril',
    '05' => 'maio',
    '06' => 'junho',
    '07' => 'julho',
    '08' => 'agosto',
    '09' => 'setembro',
    '10' => 'outubro',
    '11' => 'novembro',
    '12' => 'dezembro'
  );
  return $meses[$mes];
}


// if (!empty($user_nome_social)) {
//   $nome = $user_nome_social;
// } else {
//   $nome = $user_nome;
// }

// Substitua as palavras entre {chaves} pelo valor do banco de dados
$cert_texto = str_replace('{nome}', $user_nome, $cert_texto);
// $cert_texto = str_replace('{nome_social}', $user_nome_social, $cert_texto);
$cert_texto = str_replace('{tipo_proposta}', $propc_categoria, $cert_texto);
$cert_texto = str_replace('{nome_evento}', $prop_titulo, $cert_texto);
$cert_texto = str_replace('{categoria_certificado}', $inscc_categoria, $cert_texto);
$cert_texto = str_replace('{insc_tipo}', $insc_tipo, $cert_texto);
$cert_texto = str_replace('{insc_titulo}', $insc_titulo, $cert_texto);
$cert_texto = str_replace('{insc_nome_coautor}', $insc_nome_coautor, $cert_texto);
$cert_texto = str_replace('{data_inicio_evento}', $cert_data_inicio, $cert_texto);
$cert_texto = str_replace('{data_termino_evento}', $cert_data_fim, $cert_texto);
$cert_texto = str_replace('{carga_horaria}', $cert_carga, $cert_texto);
//
$cert_conteudo_programa = str_replace('{nome}', $user_nome, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{nome_social}', $user_nome_social, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{tipo_proposta}', $propc_categoria, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{nome_evento}', $prop_titulo, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{categoria_certificado}', $inscc_categoria, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{insc_tipo}', $insc_tipo, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{insc_titulo}', $insc_titulo, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{insc_nome_coautor}', $insc_nome_coautor, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{data_inicio_evento}', $cert_data_inicio, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{data_termino_evento}', $cert_data_fim, $cert_conteudo_programa);
$cert_conteudo_programa = str_replace('{carga_horaria}', $cert_carga, $cert_conteudo_programa);

$html = ' 

<link rel="stylesheet" href="pasta/bootstrap.min.css">
<style>
@page {
  margin: 0cm 0cm;
}

body {  
  font-family: sans-serif;
}

.page {
  page-break-before: always; /* Inicia uma nova página antes deste elemento */
}

h1, h2 ,h3, h4, h5 {
  margin: 0px;
}

ul, li {
  margin: 0px;
}

p {
  margin: 0px;
  // line-height: 26px !important;
  // font-size: 26px;
}

header {
  position: fixed;
  top: .5cm;
  left: 1cm;
  right: 1cm;
  bottom: 0;

  /** Extra personal styles **/
  color: #000;
  padding: 0px;
}


  .bg_certificado {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
  }

  .bg_certificado img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .container_certificado {
    margin-top: 6cm;
    margin-left: 4.5cm;
    margin-right: 2cm;
    margin-bottom: 2cm;
  }

  .container {
    margin-top: 1cm;
    margin-left: 1cm;
    margin-right: 1cm;
    margin-bottom: 1cm;
    font-size: 14px;
}

.link_valida_certificado {
  font-size: .75rem;
  float: right;
  margin: -8px -10px 0 0;
  }

 .cont_programa {
  margin: 20px 0 0 0;
}
footer {
        position: fixed; 
        bottom: .3cm; 
        left: 0cm; 
        right: .9cm;
        height: 0cm;

        /** Extra personal styles **/
        // background-color: #03a9f4;
        padding: 20px 0;
        color: #000;
        font-size: .75rem;
        text-align: right;
        margin-right: 0cm;
    }
</style>

<!DOCTYPE html>
<html lang="pt_br">
<head>
  <title>Certificado</title>
</head>
<body>

<header>
  
</header>

<footer>
  <p class="link_valida_certificado">' . $url_sistema . '/validar_certificado.php - Código de validação: XXXXXX</p>
</footer>
';

// SE HOUVER "IMAGEM DE FUNDO", EXIBIR
if (!empty($cert_arq_id)) {
  $html .= '<div class="bg_certificado">
  <img src="' . $url_sistema . '/uploads/certificado/' . $prop_codigo . '/' . $cert_arq_arquivo . '">
</div>';
}

$html .= '<div class="container_certificado">
  <p>' . $cert_texto . '</p>
</div>';

// SE HOUVER "CONTEÚDO PROGRAMATICO", EXIBE NA PRÓXIMA PÁGINA
if (!empty($cert_conteudo_programa)) {
  $html .= '<div class="page cont_programa"></div>
<div class="container">
  <p>' . $cert_conteudo_programa . '</p>
</div>';
}

$html .= '</body>
      </html>';

//$html = file_get_contents('seu_documento.php');
$dompdf->loadHtml($html);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

$dompdf->render();
$dompdf->stream(
  "certificado_proposta_" . $prop_codigo . ".pdf",
  array(
    "Attachment" => false
  )
);
