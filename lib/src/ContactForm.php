<?php

namespace app\src;

use app\assets\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/phpmailer/phpmailer/src/Exception.php';
require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/phpmailer/src/SMTP.php';

class ContactForm
{
    private $con;
    private $name;
    private $subject;
    private $email;
    private $message;

    public function __construct()
    {
        $this->con = DB::getInstance();
    }

    // Sets the name field of the form
    public function setName(): string
    {
        return $this->name = isset($_POST['name']) ? ucwords(trim(strip_tags($_POST['name']))) : "";
    }

    // Sets the email field of a form
    public function setEmail(): string
    {
        return $this->email = isset($_POST['email']) ? strtolower(trim(strip_tags($_POST['email']))) : "";
    }

    // Sets the subject field of a form
    public function setSubject(): string
    {
        return $this->subject = isset($_POST['subject']) ? ucwords(strtolower(trim(strip_tags($_POST['subject'])))) : "";
    }

    // Sets the message content field of a form
    public function setMessage(): string
    {
        return $this->message = isset($_POST['messageContent']) ? ucfirst(trim($_POST['messageContent'])) : "";
    }

    public function sendContactMail()
    {
        if (isset($_POST['send-message'])) {

            // Validate form inputs
            $name = $this->setName();
            $email = $this->setEmail();
            $subject = $this->setSubject();
            $messageBody = wordwrap($this->setMessage(), 70);

            if (empty($name)) {
                displayMessage("<span class='font-bold'>Name</span> field is required.", "text-rose-500");
                return;
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                displayMessage("<span class='font-bold'>Email</span> is required or invalid.", "text-rose-500");
                return;
            }

            if (empty($subject)) {
                displayMessage("<span class='font-bold'>Subject</span> field is required.", "text-rose-500");
                return;
            }

            if (empty($messageBody)) {
                displayMessage("Please type in your message content.", "text-rose-500");
                return;
            }

            try {
                $mail = new PHPMailer(true);

                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'neilicatan3@gmail.com'; // Replace with your email
                $mail->Password = 'ppgf axvm mbnb tszs';   // Replace with your email password or app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom($email, $name);
                $mail->addAddress('neilicatan@gmail.com'); // Replace with recipient email
                $mail->addReplyTo($email, $name); // Add Reply-To header with sender's email and name

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = "<html><head><title>{$subject}</title></head><body>{$messageBody}</body></html>";   

                // Send email
                $mail->send();
                displayMessage("Your message has been sent successfully. Please be assured that we would address the issues raised ASAP!", "text-green-500");
            } catch (Exception $e) {
                displayMessage("Message could not be sent. Mailer Error: {$mail->ErrorInfo}", "text-rose-500");
            }
        } else {
            displayMessage("Get In Touch With Us", "text-center header text-2xl", "h3");
        }
    }
}
