<?php

namespace app\src;

use app\assets\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/phpmailer/phpmailer/src/Exception.php';
require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/phpmailer/src/SMTP.php';

class Register
{
    private $con;
    private $name;
    private $phoneNumber;
    private $email;
    private $password;

    public function __construct()
    {
        $this->con = DB::getInstance();
    }

    // Sets the name field of the form
    public function setName(): string
    {
        return $this->name = isset($_POST['name']) ? ucwords(trim(strip_tags($_POST['name']))) : "";
    }

    // Sets the phone number field of a form
    public function setPhoneNumber(): string
    {
        return $this->phoneNumber = isset($_POST['phoneNumber']) ? trim(strip_tags($_POST['phoneNumber'])) : "";
    }

    // Sets the email field of a form
    public function setEmail(): string
    {
        return $this->email = isset($_POST['email']) ? strtolower(trim(strip_tags($_POST['email']))) : "";
    }

    // Sets the password field of a form
    public function setPassword(): string
    {
        return $this->password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : "";
    }

    public function registerUser()
    {
        if (isset($_POST['submit'])) {

            // Validate form inputs
            $name = $this->setName();
            $phoneNumber = $this->setPhoneNumber();
            $email = $this->setEmail();
            $password = $this->setPassword();

            if (empty($name)) {
                displayMessage("<span class='font-bold'>Name</span> field is required.", "text-rose-500");
                return;
            }

            if (empty($phoneNumber)) {
                displayMessage("<span class='font-bold'>Phone Number</span> field is required.", "text-rose-500");
                return;
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                displayMessage("<span class='font-bold'>Email</span> is required or invalid.", "text-rose-500");
                return;
            }

            if (empty($password)) {
                displayMessage("<span class='font-bold'>Password</span> field is required.", "text-rose-500");
                return;
            }

            $params = [
                $name,
                $phoneNumber,
                $email,
                $password,
            ];

            // Params to check if the chosen phone number or email already exists and give appropriate feedback
            $userCheckParams = [
                $phoneNumber,
                $email,
            ];
            $checkIfUserExists = $this->con->select("phone, email", "landlords", "WHERE phone = ? OR email = ?", ...$userCheckParams);

            if ($checkIfUserExists->num_rows > 0) {
                $userExists = $checkIfUserExists->fetch_object();

                if ($userExists->phone === $phoneNumber && $userExists->email === $email) {
                    displayMessage("<span class='font-bold'>Phone Number and Email</span> already exists.", "text-rose-500");
                    return;
                } else if ($userExists->email === $email) {
                    displayMessage("<span class='font-bold'>Email</span> is already taken. Please use another one.", "text-rose-500");
                    return;
                } else {
                    if ($userExists->phone === $phoneNumber) {
                        displayMessage("This <span class='font-bold'>Phone Number</span> already exists.", "text-rose-500");
                        return;
                    }
                }
            }

            $this->con->insert("landlords", ["name", "phone", "email", "password"], ...$params);

            $setUserSession = $this->con->select("name, id", "landlords", "WHERE phone = ? OR email = ?", ...$userCheckParams)->fetch_object();

            $_SESSION['user'] = $setUserSession->name;
            $_SESSION['id'] = $setUserSession->id;
            $_SESSION['loggedUser'] = strtolower($setUserSession->name . $setUserSession->id);

            // Send a welcome mail to the newly registered user
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
                $mail->setFrom('easyboard@gmail.com', 'EasyBoard'); // Replace with your sender email and name
                $mail->addAddress($email, $name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Registration Successful";
                $mail->Body = "<html><head><title>Registration Successful</title></head><body><p>Registration was successful. Enjoy the HousingQuest platform from all of us at HousingQuest.</p></body></html>";

                // Send email
                $mail->send();
                displayMessage("Registration successful. You would be redirected to your dashboard shortly. Please check your mail for a confirmation message. If you can't find the mail, please check your spam or trash folder.", "text-green-500");
            } catch (Exception $e) {
                displayMessage("Registration successful. Email could not be sent. Mailer Error: {$mail->ErrorInfo}", "text-green-500");
            }

            header("Refresh: 3, /admin", true, 301);
        } else {
            displayMessage("Create a free account today");
        }
    }
}
