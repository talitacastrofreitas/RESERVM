<?php

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->CharSet = "UTF-8";
//$mail->isSMTP();
$mail->SMTPDebug = false;
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Host = 'smtp-mail.outlook.com';
$mail->SMTPAuth = true;
$mail->Username = 'dev-noreply@bahiana.edu.br';
$mail->Password = 'F#370732640306uz';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$mail->Port = 587;
$mail->setFrom('dev-noreply@bahiana.edu.br', 'RESERVM - Sistema de Reservas de Espaços');

// ==============================================================LOCALHOST

// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
// $mail->CharSet = "UTF-8";
// //$mail->isSMTP();
// $mail->SMTPDebug = false;
// $mail->SMTPAuth = true;
// $mail->SMTPSecure = 'tls';
// $mail->Host = 'smtp.gmail.com';
// $mail->SMTPAuth = true;
// $mail->Username = 'talitacastrofreitas@gmail.com';
// $mail->Password = 'vmvottyedfzursuw';
// $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
// $mail->Port = 587;
// $mail->setFrom('talitacastrofreitas@gmail.com', 'RESERVM - Sistema de Reservas de Espaços');
