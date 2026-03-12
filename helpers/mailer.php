<?php
/**
 * Serviço de envio de e-mail via PHPMailer/SMTP
 * Configuração via constantes SMTP_* definidas em config.php
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Envia um e-mail HTML via SMTP.
 *
 * @param string $para      Endereço de destino
 * @param string $nomeDestinatario Nome do destinatário
 * @param string $assunto   Assunto do e-mail
 * @param string $htmlBody  Corpo HTML
 * @return bool
 */
function enviarEmailSMTP(string $para, string $nomeDestinatario, string $assunto, string $htmlBody): bool
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_ENCRYPTION === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($para, $nomeDestinatario);

        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('PHPMailer error: ' . $mail->ErrorInfo);
        return false;
    }
}
