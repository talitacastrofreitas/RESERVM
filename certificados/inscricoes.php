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
  $insc_id = base64_decode($_GET['insc_id']);
  $cat     = base64_decode($_GET['cat']);
  $sql = "SELECT * FROM propostas
          INNER JOIN propostas_categorias ON propostas_categorias.propc_id = propostas.prop_tipo
          INNER JOIN usuarios ON usuarios.user_id = propostas.prop_user_id
          INNER JOIN inscricoes ON inscricoes.insc_prop_id COLLATE SQL_Latin1_General_CP1_CI_AI = propostas.prop_id
          INNER JOIN inscricoes_categorias ON inscricoes_categorias.inscc_id = inscricoes.insc_categoria
          INNER JOIN certificado ON certificado.cert_prop_id COLLATE SQL_Latin1_General_CP1_CI_AI = inscricoes.insc_prop_id
          LEFT JOIN certificado_arquivo ON certificado_arquivo.cert_arq_prop_id = certificado.cert_prop_id
          WHERE cert_categoria =:cert_categoria AND insc_id = :insc_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':insc_id', $insc_id);
  $stmt->bindParam(':cert_categoria', $cat);
  $stmt->execute();
  $prop = $stmt->fetch(PDO::FETCH_ASSOC);

  // SE DADOS NÃO FOREM ENCONTRADOS OU (STATUS DO CERTIFICADO FOR VAZIO E CATEGORIA FOR DIFERENTE DE 1), FECHA JANELA
  if (empty($prop) || (empty($prop['cert_status']) || $prop['cert_categoria'] != $cat)) {
    echo '<script>window.close();</script>';
    exit;
  } else {
    extract($prop);
  }
} catch (PDOException $e) {
  echo "Erro: " . $e->getMessage();
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

// Substitua as palavras entre {chaves} pelo valor do banco de dados
$cert_texto = str_replace('{nome}', $insc_nome, $cert_texto);
$cert_texto = str_replace('{tipo_proposta}', $propc_categoria, $cert_texto);
$cert_texto = str_replace('{nome_evento}', $prop_titulo, $cert_texto);
$cert_texto = str_replace('{categoria_certificado}', $inscc_categoria, $cert_texto);
$cert_texto = str_replace('{insc_tipo}', $insc_tipo, $cert_texto);
$cert_texto = str_replace('{insc_titulo}', $insc_titulo, $cert_texto);
$cert_texto = str_replace('{insc_nome_coautor}', $insc_nome_coautor, $cert_texto);
$cert_texto = str_replace('{data_inicio_evento}', $cert_data_inicio, $cert_texto);
$cert_texto = str_replace('{data_termino_evento}', $cert_data_fim, $cert_texto);
$cert_texto = str_replace('{carga_horaria}', $cert_carga, $cert_texto);

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
  <p class="link_valida_certificado">' . $url_sistema . '/validar_certificado.php - Código de validação: ' . $insc_codigo . '</p>
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
  "certificado_$insc_codigo.pdf",
  array(
    "Attachment" => false
  )
);
