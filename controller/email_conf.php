<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->CharSet = "UTF-8";
//$mail->isSMTP();
$mail->SMTPDebug  = false;
$mail->SMTPAuth   = true;
$mail->SMTPSecure = 'tls';
$mail->Host       = 'smtp-mail.outlook.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'sistema.sigaext@bahiana.edu.br';
$mail->Password   = 'F#370732640306uz';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port       = 587;
$mail->setFrom('sistema.sigaext@bahiana.edu.br', 'RESERVM'); // EMAIL QUE ENVIA (SERVIDOR)