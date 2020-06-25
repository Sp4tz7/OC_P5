<?php

namespace Core;

use Config\config;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{

    protected $user;
    protected $template;
    protected $subject;
    protected $vars = [];
    protected $message;

    public function sendEmail()
    {
        if ( ! $this->user or ! $this->template or empty($this->vars)) {
            throw new Exception('Not all params are set correctly to send an email');

            return;
        }
        $mail   = new PHPMailer(true);
        $loader = new \Twig\Loader\FilesystemLoader([APP_DIR.'Templates/Mail']);
        $twig   = new \Twig\Environment($loader);
        $vars   = array_merge($this->vars, ['USER' => $this->user]);
        $data   = Config::getSmtpSettings();

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                // Enable verbose debug output
            $mail->isSMTP();
            $mail->Host       = $data['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $data['username'];
            $mail->Password   = $data['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $data['port'];
            $mail->setLanguage('fr');

            //Recipients
            $mail->setFrom($data['mail_from']['mail'], $data['mail_from']['name']);
            $mail->addAddress($this->user->getEmail());
            $mail->addReplyTo($data['mail_reply_to']['mail'], $data['mail_reply_to']['name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body    = $twig->render(
                $this->template,
                $vars
            );

            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            return $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function setEmailTemplate($template)
    {
        $directory         = APP_DIR.'Templates/mail/';
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        if (in_array($template, $scanned_directory)) {
            $this->template = $template;
        }
    }

    public function setEmailSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setEmailData(\Entity\User $user)
    {
        $this->user = $user;
    }

    public function setVars($vars)
    {
        if (is_array($vars)) {
            $this->vars = $vars;
        }
    }


}