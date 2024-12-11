<?php

namespace app\src;

use app\assets\DB;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/phpmailer/phpmailer/src/Exception.php';
require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/phpmailer/src/SMTP.php';


class PropertyDetails
{
    private $propertyID;
    private $propertyName;
    private $con;
    private $name;
    private $subject;
    private $email;
    private $message;

    public function __construct()
    {
        $this->propertyID = $_GET['propertyID'];
        $this->propertyName = $_GET['propertyName'];
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


    /**
     * Get the details for a particular property
     */
    public function showProperty()
    {

        // Check if the required GET properties are set
        if (is_empty($this->propertyID) || is_empty($this->propertyName)) {
            header("Location: /404", true, 301);
        }

        $getHouse = $this->con->select("id, img_1, img_2, img_3, img_4, img_5, title, price, description, location, type, owner_id, latitude, longitude", "properties", "WHERE id = ? AND link = ? AND status = 'available'", ...[$this->propertyID, $this->propertyName]);

        // Check if there is any available apartment
        if ($getHouse->num_rows < 1) {
            header("Location: /404", true, 301);
        }

        while ($house = $getHouse->fetch_object()) : ?>
            <div class="min-h-[60vh] lg:min-h-[70vh] grid place-content-center text-center bg-details-banner  px-4 bg-fixed bg-center bg-cover text-slate-200 p-4 lg:p-8">
                <h1 class="header text-3xl">
                    <?= $house->title ?>
                </h1>
            </div>

            <main class="px-4 py-12 lg:px-[10%] bg-slate-200 dark:bg-slate-900">
                <a class="text-sky-500 hover:text-sky-600 focus:text-sky-600 dark:text-sky-600 dark:hover:text-sky-700" href="/">
                    <i class="fr fi-rr-arrow-small-left"></i>
                    Go back
                </a>

                <div class="grid gap-4 sm:grid-rows-4 grid-cols-12 mt-8 mb-8">
                    <img class="h-[200px] col-span-12 rounded-xl sm:row-start-1 sm:row-end-5 sm:h-[calc(1035px/2)] sm:col-span-6" src="./assets/img/<?= $house->img_1 ?>" alt="<?= $house->title ?>" />

                    <img class="h-[200px] col-span-12 rounded-xl sm:row-span-2 sm:col-span-6 md:col-span-3 sm:h-[250px]" src="./assets/img/<?= $house->img_2 ?>" alt="<?= $house->title ?>" />

                    <img class="h-[200px] col-span-12 rounded-xl sm:row-span-2 sm:col-span-6 md:col-span-3 sm:h-[250px]" src="./assets/img/<?= $house->img_3 ?>" alt="<?= $house->title ?>" />

                    <img class="h-[200px] col-span-12 rounded-xl sm:row-span-2 sm:col-span-6 md:col-span-3 sm:h-[250px]" src="./assets/img/<?= $house->img_4 ?>" alt="<?= $house->title ?>" />

                    <img class="h-[200px] col-span-12 rounded-xl sm:row-span-2 sm:col-span-6 md:col-span-3 sm:h-[250px]" src="./assets/img/<?= $house->img_5 ?>" alt="<?= $house->title ?>" />
                </div>

                <div class="grid gap-8 sm:grid-cols-12">
                    <div class="sm:col-span-7 space-y-4">
                        <div class="bg-white space-y-1.5 rounded-xl p-4 dark:bg-slate-800 dark:text-slate-300">
                            <span class=<?= $house->type === 'For Rent' ? "text-green-500 dark:text-green-400" : "text-rose-500 dark:text-rose-400" ?>>
                                <i class="fr <?= $house->type === 'For Rent' ? 'fi-rr-recycle' : 'fi-rr-thumbtack' ?>"></i>
                                <?= $house->type ?>
                            </span>
                            <h3 class="header text-3xl">
                                Details of building
                            </h3>
                            <p>
                                <i class="fr fi-rr-map-marker-home"></i>
                                <?= $house->location ?>
                            </p>
                            <span class="text-sky-500 lining-nums font-semibold tracking-widest text-xl inline-block">
                                ₱ <?= number_format($house->price) ?>
                            </span>
                        </div>

                        <div class="bg-white rounded-xl p-4 space-y-2 dark:bg-slate-800 dark:text-slate-300">
                            <h3 class="header text-2xl">
                                Description
                            </h3>
                            <?= $house->description ?>
                        </div>


                        <p class="grid place-content-center">
                            <a class="rounded-lg py-1.5 px-4 bg-sky-500 text-white hover:bg-sky-600 border border-sky-500 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800"
                                href="/map.php?propertyID=<?= $house->id ?>">
                                Check Map
                            </a>
                        </p>
                    </div>


                    <div class="sm:col-span-5 max-w-full [display:unset]">
                        <div class="sticky top-20">
                            <div class="rounded-t-xl bg-emerald-500 text-white p-4">
                                <h4 class="header text-xl">
                                    Signify Interest
                                </h4>
                                <?php $this->sendRequest() ?>
                            </div>

                            <form class="bg-white grid gap-4 rounded-b-xl p-4 dark:bg-slate-800" method="POST">
                                <label class="flex items-center bg-slate-200 text-slate-900 rounded-lg dark:bg-slate-900 dark:text-slate-400 border-1 border-slate-100" for="name">
                                    <span class="rounded-l-lg pl-4">
                                        <i class="fr fi-rr-user relative top-0.5"></i>
                                    </span>

                                    <input class="rounded-r-lg input pl-2 bg-slate-200" type="text" placeholder="Name" name="name" id="name" required autocomplete="off" value="<?= $this->setName() ?>" />
                                </label>

                                <label class="flex items-center bg-slate-200 text-slate-900 rounded-lg dark:bg-slate-900 dark:text-slate-400 border-1 border-slate-100" for="email">
                                    <span class="rounded-l-lg pl-4">
                                        <i class="fr fi-rr-envelope relative top-0.5"></i>
                                    </span>

                                    <input class="rounded-r-lg input pl-2 bg-slate-200" type="email" placeholder="Email" name="email" id="email" required autocomplete="off" value="<?= $this->setEmail() ?>" />
                                </label>

                                <label class="flex items-center bg-slate-200 text-slate-900 rounded-lg dark:bg-slate-900 dark:text-slate-400 border-1 border-slate-100" for="subject">
                                    <span class="rounded-l-lg pl-4">
                                        <i class="fr fi-rr-edit relative top-0.5"></i>
                                    </span>

                                    <input class="rounded-r-lg input pl-2 bg-slate-200" type="subject" placeholder="Subject" name="subject" id="subject" required autocomplete="off" value="<?= $this->setSubject() ?>" />
                                </label>

                                <label class="bg-slate-200 text-slate-900 rounded-lg border-1 border-slate-100" for="messageContent">
                                    <textarea class="input block rounded-lg" name="messageContent" id="messageContent" rows="4" placeholder="Message Content"><?= $this->setMessage() ?></textarea>
                                </label>

                                <button class="bg-sky-500 hover:bg-sky-600 focus:bg-sky-600 py-2 w-auto px-4 text-white rounded-lg dark:bg-sky-600 dark:hover:bg-sky-700 dark:focus:bg-sky-700 hover:ring-1 hover:ring-sky-500 ring-offset-2 active:ring-1 active:ring-sky-500 dark:ring-offset-slate-800" type="submit" name="submit-request">
                                    Submit Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
<?php

        endwhile;
    }

    public function sendRequest()
    {
        $sql = "SELECT email FROM landlords l JOIN properties p WHERE p.id = ? AND p.link = ? AND p.owner_id = l.id";

        $ownerDetails = $this->con->prepare($sql, "ss", ...[$this->propertyID, $this->propertyName])->fetch_object()->email;

        $this->sendRequestMessage($ownerDetails);
    }

    public function sendRequestMessage(string $recepientEmail)
{
    if (isset($_POST['submit-request'])) {

        // Validate form inputs
        $name = $this->setName();
        $email = $this->setEmail();
        $subject = $this->setSubject();
        $messageBody = wordwrap($this->setMessage(), 70);

        if (empty($name)) {
            displayMessage("<span class='font-bold'>Name</span> field is required.");
            return;
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            displayMessage("<span class='font-bold'>Email</span> is required or invalid.");
            return;
        }
        if (empty($subject)) {
            displayMessage("<span class='font-bold'>Subject</span> field is required.");
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
            $mail->Username = 'neilicatan3@gmail.com';
            $mail->Password = 'ppgf axvm mbnb tszs';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress($recepientEmail);
            $mail->addReplyTo($email, $name); // Add Reply-To header with sender's email and name

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $buildingName = ucwords(str_replace('-', ' ', $this->propertyName));
            $message = "
                <html>
                <head>
                    <title>{$subject}</title>
                </head>
                <body>
                    <p>
                        This is a message for {$buildingName} property with property ID of $this->propertyID.
                        <br><br> Reply to {$email}.
                    </p>
                    {$messageBody}
                </body>
                </html>
            ";
            $mail->Body = $message;

            // Send email
            $mail->send();
            displayMessage("Your message has been sent successfully. You would be contacted by the property owner as soon as possible. Thanks for using EasyBoard!");
        } catch (Exception $e) {
            displayMessage("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}}
