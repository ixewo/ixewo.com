<?php

    session_start();

    // Importer le fichier de connexion à la base de données
    require 'db_connect_users.php';

    // Importer le fichier de connexion à la base de données
    include 'db_connect_annonces.php';

    // Vérifiez si l'utilisateur est connecté
    if (isset($_COOKIE['phpauth_session_cookie'])) {
        $hash = $_COOKIE['phpauth_session_cookie'];
        $uid = $auth->getSessionUID($hash);
    } else {
        // Gérer l'absence du cookie, par exemple rediriger vers la page de connexion ou afficher un message d'erreur.
        header('Location: connection-profil');
    }
    error_reporting(E_ALL);  // Display all errorsrequire 'config.php';
    
    $message1 = '';
    $message2 = ''; //bleu
    $message3 = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $currentPassword = filter_input(INPUT_POST, 'currentPassword', FILTER_SANITIZE_SPECIAL_CHARS);
        $newPassword = filter_input(INPUT_POST, 'newPassword', FILTER_SANITIZE_SPECIAL_CHARS);
        $repeatNewPassword = filter_input(INPUT_POST, 'repeatNewPassword', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($newPassword === $repeatNewPassword) {
            $result = $auth->changePassword($uid, $currentPassword, $newPassword, $repeatNewPassword);
            if ($result['error']) {
                $message1 = $result['message'];
            } else {
                $message2 = "Mot de passe modifié avec succès !";
            }
        } else {
            $message3 = "Les mots de passe ne correspondent pas !";
        }
    }
?>


<html lang="fr">

    <head>

        <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

        <title>Changer de mot de passe - ixewo</title>

        <meta name="description" content="Vous voulez améliorer la sécurité de votre compte ? Modifier votre mot de passe ici !!!">

        <meta property="og:url" content="https://www.ixewo.com" />

        <meta property="og:type" content="website"/>

        <meta property="og:title" content="ixewo"/>

        <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

        <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>
            
        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="18-styles.css">
                    
    </head>


    <body>

        <?php include '1-header.php'; ?>

        <section class="main">

            <section class="container-consoles">
                <div class="titre-consoles">
                    <h1>Changer de mot de passe</h1>
                </div>
            </section>

            <section class="register-informations">

                <?php if ($message1): ?>
                    <p class="message-r"><?php echo $message1; ?></p>
                <?php endif; ?>

                <?php if ($message2): ?>
                    <p class="message-b"><?php echo $message2; ?></p>
                <?php endif; ?>

                <?php if ($message3): ?>
                    <p class="message-r"><?php echo $message3; ?></p>
                <?php endif; ?>
            
                <form class="register-section" method="POST">

                    <input class="input" type="password" name="currentPassword" placeholder="Mot de passe actuel" required>
                    <input class="input" type="password" name="newPassword" placeholder="Nouveau mot de passe" required>
                    <input class="input" type="password" name="repeatNewPassword" placeholder="Répétez le nouveau mot de passe" required>

                    <input class="login" type="submit" value="Changer le mot de passe">

                </form>

            </section>

            <section class="container-consoles1">
                <div class="titre-consoles1">
                    <h1>changer de mot de passe</h1>
                </div>
            </section>

        </section>

        <?php include '11-footer.html'; ?>

        <script src="18-script.js"></script>
        
    </body>

</html>
