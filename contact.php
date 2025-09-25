<?php
// Configuration
$recipient_email = "jean.dupont@email.com"; // Remplacez par votre vraie adresse email
$subject_prefix = "[Portfolio] ";

// Vérification de la méthode POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

// Récupération et nettoyage des données
$name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? strip_tags(trim($_POST['subject'])) : '';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

// Validation des données
$errors = array();

if (empty($name)) {
    $errors[] = "Le nom est requis.";
}

if (empty($email)) {
    $errors[] = "L'email est requis.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'adresse email n'est pas valide.";
}

if (empty($subject)) {
    $errors[] = "Le sujet est requis.";
}

if (empty($message)) {
    $errors[] = "Le message est requis.";
}

// Si il y a des erreurs, redirection avec message d'erreur
if (!empty($errors)) {
    $error_message = implode("\\n", $errors);
    echo "<script>
        alert('Erreur:\\n" . $error_message . "');
        window.history.back();
    </script>";
    exit;
}

// Préparation de l'email
$email_subject = $subject_prefix . $subject;
$email_body = "Nouveau message depuis le portfolio\n\n";
$email_body .= "Nom: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Sujet: $subject\n\n";
$email_body .= "Message:\n$message\n";

// Headers de l'email
$headers = "From: $name <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Tentative d'envoi de l'email
if (mail($recipient_email, $email_subject, $email_body, $headers)) {
    // Succès - redirection avec message de confirmation
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Message envoyé - Portfolio</title>
        <link rel='stylesheet' href='styles.css'>
        <style>
            .success-message {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 20px;
            }
            .success-content {
                max-width: 500px;
            }
            .success-content h1 {
                font-size: 2rem;
                margin-bottom: 20px;
                font-weight: 300;
            }
            .success-content p {
                color: var(--gray-dark);
                margin-bottom: 30px;
                line-height: 1.6;
            }
        </style>
    </head>
    <body>
        <div class='success-message'>
            <div class='success-content'>
                <h1>Message envoyé avec succès</h1>
                <p>Merci $name, votre message a bien été envoyé. Je vous répondrai dans les plus brefs délais.</p>
                <a href='index.html' class='btn-primary'>Retour au portfolio</a>
            </div>
        </div>
        <script>
            // Redirection automatique après 5 secondes
            setTimeout(function() {
                window.location.href = 'index.html';
            }, 5000);
        </script>
    </body>
    </html>";
} else {
    // Erreur d'envoi
    echo "<script>
        alert('Erreur lors de l\\'envoi du message. Veuillez réessayer plus tard.');
        window.history.back();
    </script>";
}
?>