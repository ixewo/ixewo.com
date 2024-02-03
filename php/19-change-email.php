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
    error_reporting(E_ALL);  // Display all errors

    $message1 = '';
    $message2 = '';
    $message3 = '';
    $message4 = '';
    $message5 = ''; //bleu
    $message6 = '';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
        $newEmail = filter_input(INPUT_POST, 'newEmail', FILTER_SANITIZE_EMAIL);

        $email_min_length = $config->verify_email_min_length; // Ou une autre méthode pour obtenir cette valeur de configuration
        $email_max_length = $config->verify_email_max_length; // Ou une autre méthode pour obtenir cette valeur de configuration
        $result = $auth->changeEmail($uid, $_POST['newEmail'], $password);
        $blacklist = array("phpauth_emails_banned"); 



        // Vérifications supplémentaires avant de changer l'e-mail
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $message1 = "Adresse e-mail invalide.<br>";
        } else if (strlen($_POST['newEmail']) < $email_min_length) {
            $message2 = "L'e-mail est trop court.<br>";
            return false;
        } else if (strlen($_POST['newEmail']) > 150) {
            $message = "L'e-mail est trop long.<br>";
            return false;
        } else if (in_array($newEmail, $blacklist)) {
            $message3 = "L'e-mail est sur la liste noire.<br>";
        } else {            

            //nouveau
            $stmt = $dbh->prepare("UPDATE phpauth_users SET isactive = 0 WHERE id = :uid");
            $stmt->execute([':uid' => $uid]);
            
            // 3. Création du token
            $token = bin2hex(random_bytes(10));

            // 4. Préparez la requête pour insérer le token dans la base de données
            $stmt = $dbh->prepare("INSERT INTO phpauth_requests (uid, token, expire, type) VALUES (:uid, :token , DATE_ADD(NOW(), INTERVAL 1 DAY), 'activation')");
            $stmt->execute(['uid' => $uid, ':token' => $token]);

            try {
                $mail->isSMTP();
                $mail->Host = '*****';
                $mail->SMTPAuth = true;
                $mail->Username = '*****';
                $mail->Password = '*****'; // Remplacez par le mot de passe réel
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                // $mail->SMTPDebug = 2; // Activer le débogage SMTP

                $mail->setFrom('*****', 'ixewo - modification mail');
                $mail->addAddress($newEmail); // Utiliser l'email et le prénom de l'utilisateur
                  
                $mail->Subject = "Modification de votre adresse mail";
                $mail->Body = "
                Pour confirmer la modification de votre adresse e-mail, veuillez cliquer sur le lien ci-dessous :
                    
                'https://www.ixewo.com/modification-mail?token=$token'

                Cordialement
                    
                L'équipe ixewo";
                            
                $mail->send();

                $message5 = "E-mail modifié avec succès! Vérifiez vos e-mails pour le confirmer.";

            } catch (Exception $e) {
                $message6 = "Le message n'a pas pu être envoyé. Erreur de Mailer : {$mail->ErrorInfo}";
                echo "Erreur : " . $e->getMessage();
            }
            
        }
    }    

?>


<html lang="fr">

    <head>

        <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

        <title>Changer d'email - ixewo</title>

        <meta name="description" content="Changer d'email d'authentification c'est facile et c'est ici !!!">

        <meta property="og:url" content="https://www.ixewo.com" />

        <meta property="og:type" content="website"/>

        <meta property="og:title" content="ixewo"/>

        <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

        <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

        <meta charset="UTF-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="../css/19-styles.css">
        
    </head>

    <body>

        <?php include '1-header.php'; ?>

        <section class="main">

            <section class="container-consoles">
                <div class="titre-consoles">
                    <h1>Changer l'email</h1>
                </div>
            </section>

            <section class="register-informations">

                <?php if ($message1): ?>
                    <p class="message-r"><?php echo $message1; ?></p>
                <?php endif; ?>

                <?php if ($message2): ?>
                    <p class="message-r"><?php echo $message2; ?></p>
                <?php endif; ?>

                <?php if ($message3): ?>
                    <p class="message-r"><?php echo $message3; ?></p>
                <?php endif; ?>

                <?php if ($message4): ?>
                    <p class="message-r"><?php echo $message4; ?></p>
                <?php endif; ?>

                <?php if ($message5): ?>
                    <p class="message-v"><?php echo $message5; ?></p>
                <?php endif; ?>

                <?php if ($message6): ?>
                    <p class="message-r"><?php echo $message6; ?></p>
                <?php endif; ?>
            
                <form class="register-section" method="POST"><!-- HTML form for changing email -->

                    <input class="input" type="password" name="password" placeholder="Mot de passe" required>
                    <input class="input" type="email" name="newEmail" placeholder="Nouvelle adresse e-mail" required>

                    <input class="login" type="submit" value="Changer l'e-mail">

                </form>

            </section>
            

            <section class="container-consoles1">
                <div class="titre-consoles1">
                    <h1>changer l'email</h1>
                </div>
            </section>

        </section>

        <?php include '../11-footer.html'; ?>

        <script src="../js/19-script.js"></script>

    </body>
    
</html>
