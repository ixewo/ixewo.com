<?php

    // Importer le fichier de connexion à la base de données
    require 'db_connect_users.php';

    $message = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {

        // 1. Récupérez l'adresse e-mail
        $stmt = $dbh->prepare("SELECT email FROM phpauth_users WHERE email = :email");
        $stmt->execute([':email' => $_POST['email']]);
        $user = $stmt->fetch();

        // 2. Récupérez l'ID de l'adresse mail
        $stmt = $dbh->prepare("SELECT id FROM phpauth_users WHERE email = :email");
        $stmt->execute([':email' => $_POST['email']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $uid = $result['id'];
        } else {
            // Adresse e-mail non trouvée
            echo "mail non trouvé" . "<br>";
            echo "Email saisi : " . $_POST['email'];
            exit;
        }

        // 3. Création du token
        $token = bin2hex(random_bytes(10));

        // 4. Préparez la requête pour insérer le token dans la base de données
        $stmt = $dbh->prepare("INSERT INTO phpauth_requests (uid, token, expire, type) VALUES (:uid, :token , DATE_ADD(NOW(), INTERVAL 1 DAY), 'reset')");
        $stmt->execute(['uid' => $uid, ':token' => $token]);

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        try {
            $mail->isSMTP(); // Utiliser SMTP pour l'envoi
            $mail->Host = '*****';
            $mail->SMTPAuth = true;
            $mail->Username = '*****';
            $mail->Password = '*****'; // Remplacez par le mot de passe réel
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            // $mail->SMTPDebug = 2; // Activer le débogage SMTP

            $mail->setFrom('*****', 'ixewo - mot de passe');
                                        
            $mail->Subject = 'Re-initialisation du mot de passe - ixewo.com';
            $mail->Body = "
            Bon retour chez nous !
                    
            Cliquez sur le lien suivant pour réinitialiser votre mot de passe: 
                    
            https://ixewo.com/nouveau-mot-de-passe?token=$token

            Cordialement
            L'équipe ixewo";
            $mail->addAddress($email, $firstName);
        
            $mail->send();

            $message = "Vérifiez vos e-mails, mail de re-initilaisation de mot de passe envoyé !!! ";

        } catch (Exception $e) {
            // Gérer l'exception ici
            echo "Erreur : " . $e->getMessage();
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
?>


<html lang="fr">

    <head>

        <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

        <title>Mot de passe oublié - ixewo</title>

        <meta name="description" content="Vous avez oubié votre mot de passe ? Pour le modifier c'est ici !!!">

        <meta property="og:url" content="https://www.ixewo.com" />

        <meta property="og:type" content="website"/>

        <meta property="og:title" content="ixewo"/>

        <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

        <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

        <meta charset="UTF-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="25-styles.css">
        
    </head>

    <body>

        <?php include '1-header.php'; ?>

        <section class="main">

          <section class="container-consoles">
              <div class="titre-consoles">
                  <h1>Nouveau mot de passe</h1>
              </div>
          </section>

          <section class="register-informations">

              <form class="register-section" action="mot-de-passe-oublie" method="post">
                  <input class="email" type="email" name="email" placeholder="votre email d'utilisateur"required>

                  <input class="login" type="submit" value="Réinitialiser le mot de passe">
              </form>
          
          </section>

          <?php if ($message): ?>
              <p><?php echo $message; ?></p>
          <?php endif; ?>

          <section class="container-consoles1">
              <div class="titre-consoles1">
                  <h1>nouveau mot de passe</h1>
              </div>
          </section>

        </section>

        <?php include '11-footer.html'; ?>

        <script src="25-script.js"></script>

    </body>
    
</html>