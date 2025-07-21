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

//$dominio = 'http://' . $_SERVER['HTTP_HOST'] . '/siex';
$img_header = $url_sistema . '/assets/img/rel_logo_siex.jpg';
$img_footer = $url_sistema . '/assets/img/rel_logo_bahiana.jpg';

try {
  $prop_id = base64_decode($_GET['i']);
  $cat     = base64_decode($_GET['c']);
  $sql = "SELECT * FROM propostas
          INNER JOIN propostas_categorias ON propostas_categorias.propc_id = propostas.prop_tipo
          INNER JOIN usuarios ON usuarios.user_id = propostas.prop_user_id
          INNER JOIN inscricoes ON inscricoes.insc_prop_id COLLATE SQL_Latin1_General_CP1_CI_AI = propostas.prop_id
          INNER JOIN inscricoes_categorias ON inscricoes_categorias.inscc_id = inscricoes.insc_categoria
          WHERE inscc_id = :inscc_id AND prop_id = :prop_id";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':prop_id', $prop_id, PDO::PARAM_STR);
  $stmt->bindParam(':inscc_id', $cat, PDO::PARAM_INT);
  $stmt->execute();
  $prop = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($prop) {
    $prop_id                    = $prop['prop_id']; // MAIN
    $prop_tipo                  = $prop['prop_tipo'];
    $prop_codigo                = $prop['prop_codigo'];
    $prop_titulo                = $prop['prop_titulo'];
    $prop_descricao             = $prop['prop_descricao'];
    $prop_vinculo_programa      = $prop['prop_vinculo_programa'];
    $prop_qual_vinculo_programa = $prop['prop_qual_vinculo_programa'];
    $prop_curso_vinculo         = $prop['prop_curso_vinculo'];
    $prop_justificativa         = $prop['prop_justificativa'];
    $prop_obj_pedagogico        = $prop['prop_obj_pedagogico'];
    $prop_publico_alvo          = $prop['prop_publico_alvo'];
    $prop_area_conhecimento     = $prop['prop_area_conhecimento'];
    $prop_area_tematica         = $prop['prop_area_tematica'];
    $prop_user_id               = $prop['prop_user_id'];
    $prop_data_cad              = $prop['prop_data_cad'];
    $prop_data_upd              = $prop['prop_data_upd'];
    // PROPOSTA CATEGORIA
    $propc_id                   = $prop['propc_id'];
    $propc_categoria            = $prop['propc_categoria'];
    // USUÁRIOS
    $user_id                    = $prop['user_id'];
    $user_nome                  = $prop['user_nome'];
    $user_nome_social           = $prop['user_nome_social'];
    $user_email                 = $prop['user_email'];
    $user_contato               = $prop['user_contato'];
    // INSCRIÇÕES
    $inscc_id                   = $prop['inscc_id'];
    $inscc_categoria            = $prop['inscc_categoria'];
    // INSCRIÇÕES CATEGORIA
    $insc_id                    = $prop['insc_id'];
    $insc_prop_id               = $prop['insc_prop_id'];
    $insc_categoria             = $prop['insc_categoria'];
    $insc_codigo                = $prop['insc_codigo'];
    $insc_nome                  = $prop['insc_nome'];
    $insc_cpf                   = $prop['insc_cpf'];
    $insc_email                 = $prop['insc_email'];
    $insc_contato               = $prop['insc_contato'];
    $insc_tipo                  = $prop['insc_tipo'];
    $insc_titulo                = $prop['insc_titulo'];
    $insc_nome_coautor          = $prop['insc_nome_coautor'];
    $insc_user_id               = $prop['insc_user_id'];
    $insc_data_cad              = $prop['insc_data_cad'];
    $insc_data_upd              = $prop['insc_data_upd'];
  } else {
    echo '<script>window.close();</script>';
    exit;
  }

  // SE USUÁRIO CADASTROU "NOME SOCIAL", USA ESTE NOME
  if (!empty($user_nome_social)) {
    $nome = $user_nome_social;
  } else {
    $nome = $user_nome;
  }
} catch (PDOException $e) {
  echo "Erro: " . $e->getMessage();
}

// INFORMA O TOTAL DE REGISTROS
$prop_id = base64_decode($_GET['i']);
$cat = base64_decode($_GET['c']);
$sql_conta = "SELECT COUNT(*) FROM inscricoes WHERE insc_prop_id = '$prop_id' AND insc_categoria = $cat";
$sql_conta_cliente = $conn->query($sql_conta);
$row_cnt = $sql_conta_cliente->fetchColumn();

$html = '<html>
    <head>
      <style>
        @page {
            margin: 0cm 0cm;
        }
        body {
            margin-top: 1.8cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 2cm;
            font-family: sans-serif;
        }

        h1, h2 ,h3, h4, h5 {
          margin: 0px;
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
          background: #DDD;
        }

        /** Define the header rules **/
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

        header img{
            margin: 0 10px 0 0;
            width: 250px;
        }

        .dados_header h3 {
          font-size: 16px;
          text-transform: uppercase;
        }

        .dados_proposta {
          margin-bottom: 20px;
        }

        .dados_proposta h5 {
          font-size: 16px;
          padding: 5px 5px;
        }

        .dados_proposta td{
          padding: 3px 5px;
          font-size: 12px;
        }

        .dados_proposta strong{
          font-size: 12px;
        }

        /** Define the footer rules **/
        footer {
            position: fixed; 
            bottom: 0cm; 
            left: 1cm; 
            right: 1cm;
            height: 1cm;

            /** Extra personal styles **/
            // background-color: #03a9f4;
            padding: 20px 0;
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

        <!-- Define header and footer blocks before your content -->
        <header>
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td style="text-align: left;"><img src="' . $img_header . '" width="100%"></td>
              <td style="text-align: center;" width="100%">
                <div class="dados_header">
                  <h3>' . $inscc_categoria . '</h3>
                </div>
              </td>
              <td style="text-align: right;" width="100%">COD: ' . $prop_codigo . '</td>
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


        <!-- Wrap the content of your PDF inside a main tag -->
        <main>

          <table class="dados_proposta" width="100%" border="1" cellpadding="0" cellspacing="0">
            <tr>
              <td colspan="2"><h5>' . $prop_titulo . '</h5></td>
            </tr>
            <tr>
              <td><strong>Tipo: </strong>' . $propc_categoria . '</td>
              <td><strong>Total de Participantes: </strong>' . $row_cnt . '</td>
            </tr>
            <tr>
              <td><strong>Proponente: </strong>' . $nome . '</td>
              <td><strong>E-mail: </strong>' . $user_email . '</td>
            </tr>
          </table>

        <table style="font-size: 11px; margin-top: 0px; class="table" border="1" cellpadding="0" cellspacing="0" width="100%">
          <thead>
            <tr style="background: #CCC;">
              <th style="text-align: left; padding: 4px;" scope="col">Código</th>
              <th style="text-align: left; padding: 4px;" scope="col">Nome</th>';

if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) {
  $html .= '<th style="text-align: left; padding: 4px;" scope="col">Tipo</th>';
}

if (base64_decode($_GET['c']) == 3) {
  $html .= '<th style="text-align: left; padding: 4px;" scope="col">Título da Atividade</th>';
}

if (base64_decode($_GET['c']) == 4) {
  $html .= '<th style="text-align: left; padding: 4px;" scope="col">Título do Trabalho</th>';
}

$html .= '<th style="text-align: left; padding: 4px;" scope="col">CPF</th>
          <th style="text-align: left; padding: 4px;" scope="col">E-mail</th>
          <th style="text-align: left; padding: 4px;" scope="col">Contato</th>';

if (base64_decode($_GET['c']) == 4) {
  $html .= '<th style="text-align: left; padding: 4px;" scope="col">Coautor</th>';
}
$html .= '<th style="text-align: left; padding: 4px;" scope="col">Credenciamento</th>';
$html .= '</tr>
          </thead>
          <tbody>';

$cat = base64_decode($_GET['c']);
$sql = $conn->query("SELECT *,
                    CASE 
                        WHEN insc_credenciamento = 1 THEN 'CREDENCIADO' 
                        ELSE '' 
                    END AS has_credenciamento
                    FROM inscricoes
                    INNER JOIN inscricoes_categorias ON inscricoes_categorias.inscc_id = inscricoes.insc_categoria
                    WHERE insc_prop_id = '$prop_id' AND insc_categoria = $cat ORDER BY insc_nome ASC");
while ($insc = $sql->fetch(PDO::FETCH_ASSOC)) {
  $insc_id             = $insc['insc_id'];
  $insc_prop_id        = $insc['insc_prop_id'];
  $insc_categoria      = $insc['insc_categoria'];
  $insc_codigo         = $insc['insc_codigo'];
  $insc_nome           = $insc['insc_nome'];
  $insc_cpf            = $insc['insc_cpf'];
  $insc_email          = $insc['insc_email'];
  $insc_contato        = $insc['insc_contato'];
  $insc_tipo           = $insc['insc_tipo'];
  $insc_titulo         = $insc['insc_titulo'];
  $insc_nome_coautor   = $insc['insc_nome_coautor'];
  $insc_credenciamento = $insc['has_credenciamento'];
  $insc_user_id        = $insc['insc_user_id'];
  $insc_data_cad       = $insc['insc_data_cad'];
  $insc_data_upd       = $insc['insc_data_upd'];

  // ADICIONA PONTUAÇÃO AO CPF
  if (!empty($insc_cpf)) {
    $nbr_cpf = "$insc_cpf";
    $parte_um     = substr($nbr_cpf, 0, 3);
    $parte_dois   = substr($nbr_cpf, 3, 3);
    $parte_tres   = substr($nbr_cpf, 6, 3);
    $parte_quatro = substr($nbr_cpf, 9, 2);
    $insc_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";
  } else {
    $insc_cpf = '';
  }

  $html .= '<tr>
            <th style="text-align: left; padding: 5px 4px;">' . $insc_codigo . '</th>
            <td style="text-align: left; padding: 5px 4px;">' . $insc_nome . '</td>';

  if (base64_decode($_GET['c']) == 4 || base64_decode($_GET['c']) == 6) {
    $html .= '<td style="text-align: left; padding: 5px 4px;">' . $insc_tipo . '</td>';
  }

  if (base64_decode($_GET['c']) == 3 || base64_decode($_GET['c']) == 4) {
    $html .= '<td style="text-align: left; padding: 5px 4px;">' . $insc_titulo . '</td>';
  }

  $html .= '<td style="text-align: left; padding: 5px 4px;">' . $insc_cpf . '</td>
            <td style="text-align: left; padding: 5px 4px;">' . $insc_email . '</td>
            <td style="text-align: left; padding: 5px 4px;">' . $insc_contato . '</td>';

  if (base64_decode($_GET['c']) == 4) {
    $html .= '<td style="text-align: left; padding: 5px 4px;">' . $insc_nome_coautor . '</td>';
  }
  $html .= '<td style="text-align: left; padding: 5px 4px;">' . $insc_credenciamento . '</td>';
  $html .= '</tr>';
}

$html .= '</tbody>
			</table>
      </main>
    </body>
	</html>';

$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

$dompdf->render();
$dompdf->stream(
  "inscricao_proposta_" . $prop_codigo . ".pdf",
  array(
    "Attachment" => false
  )
);
