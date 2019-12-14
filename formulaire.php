<?php
require_once 'vendor/autoload.php';

$to = 'morganejarry1@gmail.com';
$subject = 'Contact de mon CV en ligne';

$name = htmlentities($_POST['name']);
$company = htmlentities($_POST['company']);
$email = $_POST['email'];
$message = htmlentities($_POST['message']);

/**
 * Permet de vérifier la validité d'une adresse mail
 */
if (!preg_match('/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/', $email) || empty($email)) { 
    echo 'EMAIL PAS OK';
    exit;
}

if(empty($name) || !preg_match('/^[\w \-\']{2,40}$/', $name)) {
    echo 'NOM PAS OK';
    exit;
}

if(!preg_match('/^[\w \_()\-\']{2,40}$/', $company)) {
    echo 'ENTREPRISE PAS OK';
    exit;
}

if(empty($message)) {
    echo 'MESSAGE PAS OK';
    exit;
}

$content = "<html>
                <head>
                    <title>Contact de mon CV en ligne</title>
                </head>
                <body>
                    <p>Voici le message de $name ($company) : </p>
                    <p>$message</p>
                </body>
            </html>";

// Create the Transport
$transport = (new Swift_SmtpTransport('localhost', 25));

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// Create a message
$message = (new Swift_Message($subject))
->setContentType('text/html')
->setFrom([$email => $email])
->setTo($to)
->setBody($content)
;

// Send the message
$result = $mailer->send($message);