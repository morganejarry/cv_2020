<?php
session_start();
require_once 'vendor/autoload.php';

$to = 'morganejarry1@gmail.com';
$subject = 'Contact de mon CV en ligne';
$erreurs = array();

$_SESSION['name'] = htmlentities($_POST['name']);
$_SESSION['company'] = htmlentities($_POST['company']);
$_SESSION['email'] = $_POST['email'];
$_SESSION['message'] = htmlentities($_POST['message']);

/**
 * Permet de vérifier la validité d'une adresse mail, grace aux regex (verifier (ou récupérer valeur dans) une chaine de caractères)
 * Pour tester la validité de son regex, on utilise regex101.com
 */
if(!preg_match('/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/', $_SESSION['email']) || empty($_SESSION['email'])) { // !(=false, permet d'inverser le if)preg_match = fonction pour utiliser regex 
    $erreurs['erreurEmail'] = 'L\'email n\'est pas valide ou n\'est pas renseigné.'; // on affecte une variable dans le tableau erreur
}

if(empty($_SESSION['name']) || !preg_match('/^[\w \-\']{2,40}$/', $_SESSION['name'])) { // || = OU 
    $erreurs['erreurName'] = 'Ce champ doit être renseigné. Le nom doit être compris entre 2 et 40 caractères, seuls les symboles - et \' peuvent être inscrits.';
}

if(!empty($_SESSION['company'])) {
    if(!preg_match('/^[\w \_(),\-\']{2,40}$/', $_SESSION['company'])) {
        $erreurs['erreurCompany'] = 'Le nom de l\'entreprise doit être compris entre 2 et 40 caractères, seuls les symboles "-", "_", ",", "()" et "\'" peuvent être inscrits.';
    }
} else {
    $_SESSION['company'] = "";
}

if(empty($_SESSION['message'])) {
    $erreurs['erreurMessage'] = 'Le message doit être renseigné.';
}

if(count($erreurs) > 0) {
    $_SESSION['erreurs'] = $erreurs;
    header('Location: index.php#conteneur_contact');
    exit;
}
/**
 * Variable qui contient le contenu du mail
 */
$content = "<html>
                <head>
                    <title>Contact de mon CV en ligne</title>
                </head>
                <body>
                    <p>Voici le message de " . $_SESSION['name'] . " ( entreprise :" . ($_SESSION['company']) . ") : </p>
                    <p>" . $_SESSION['message'] . "</p>
                </body>
            </html>";


/**
 * installation biblitothèque "swift_mailer" permettant de faciliter l'envoi d'email 
 */

// Create the Transport
$transport = (new Swift_SmtpTransport('localhost', 25));

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// Create a message
$message = (new Swift_Message($subject))
->setContentType('text/html')
->setFrom([$_SESSION['email'] => $_SESSION['email']])
->setTo($to)
->setBody($content)
;

// Send the message
$result = $mailer->send($message);

if ($result === 1) {
    session_destroy();
    header('Location: index.php?ok=1');
    exit;
} else {
    $erreurs['erreurEnvoi'] = 'Une erreur c\'est produit lors de l\'envoi du mail, veuillez réessayer ultérieurement.';
    header('Location: index.php#conteneur_contact');
    exit;
}
