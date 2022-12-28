<?php

namespace Component {

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception as MailerException; 
    use Singleton\Registry;

    /**
     * Odpowiada za wysyłanie e-mailów
     */
    final class Mailer extends \Engine\Component {

        /**
         * Przechowuje obiekt klasy PHPMailer
         * @var PHPMailer
         */
        private $mail;

        protected function init() : void {
            try {
                $this->mail = new PHPMailer(true);
                (!$this->getConfig('debug')) ?: $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;    // Enable verbose debug output
                
                $settings = $this->getConfig('mailerConfig');
                $this->mail->isSMTP();
                $this->mail->Host       = $settings['Host'];                // SMTP server example
                $this->mail->SMTPAuth   = $settings['SMTPAuth'];            // enable SMTP authentication
                $this->mail->Username   = $settings['Username'];            // SMTP account username example
                $this->mail->Password   = $settings['Password'];            // SMTP account password example
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
                $this->mail->Port       = $settings['Port'];
                $this->mail->CharSet    = $settings["CharSet"];
            } catch (MailerException $e) {
                Registry::getInstance()->getExceptionHandler()->catchException($e);
            }
        }

        /**
         * Wysyła maila o zadanym temacie i wiadomości dla adresów
         * @param string $subject
         * @param string $content
         * @param array $addresses
         * @return void
         */
        public function sendEmail(string $subject, string $content, array $addresses = array()) : void {
            if ($this->mail) {
                try {
                    $this->mail->setFrom($this->mail->Username);
                    if (empty($addresses)) {
                        $this->mail->addAddress($this->mail->Username);     // Add a recipient
                    } else {
                        foreach ($addresses as $address) {
                            $this->mail->addAddress($address);      // Add a recipient
                        }
                    }
                    // Content
                    $this->mail->isHTML(false);                     // Set email format to HTML
                    $this->mail->Subject = $subject;
                    $this->mail->Body = $content;
                    //$this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                    $this->mail->send();
                    $this->mail->clearAddresses();
                } catch (MailerException $e) {
                    Registry::getInstance()->getExceptionHandler()->catchException($e);
                }
            }
        }
    }
}

?>