<?php
  // Importer le fichier de connexion à la base de données
  require 'db_connect_users.php';

  $message1 = ""; //bleu
  $message2 = "";
  $message3 = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      // 1. Rechercher l'ID de l'utilisateur
      $stmt = $dbh->prepare("SELECT id FROM phpauth_users WHERE email = :email");
      $stmt->execute([':email' => $email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user) {
        $uid = $user['id'];

        // 2. Générer un nouveau token
        $token = bin2hex(random_bytes(10));

        // 3. Insérer le token dans la base de données
        $stmt = $dbh->prepare("INSERT INTO phpauth_requests (uid, token, expire, type) VALUES (:uid, :token, DATE_ADD(NOW(), INTERVAL 1 DAY), 'activation')");
        $stmt->execute([':uid' => $uid, ':token' => $token]);

        //4. mise à 0 de l'activation
        $stmt = $dbh->prepare("UPDATE phpauth_users SET isactive = 0 WHERE id = :uid");
        $stmt->execute([':uid' => $uid]);

        // 5. Préparer l'e-mail 
        try {
          $mail->isSMTP();
          $mail->Host = '*****';
          $mail->SMTPAuth = true;
          $mail->Username = '*****';
          $mail->Password = '*****'; // Remplacez par le mot de passe réel
          $mail->SMTPSecure = 'ssl';
          $mail->Port = 465;
          // $mail->SMTPDebug = 2; // Activer le débogage SMTP

          $mail->setFrom('*****', 'ixewo - enregistrement');
          $mail->addAddress($email); // Utiliser l'email et le prénom de l'utilisateur
                
          $mail->Subject = "Confirmation de votre inscription";
          $mail->Body = "
          Bienvenue sur notre site !

          Merci de vous être inscrit. 
          
          Veuillez nous excuser pour la première vérification ayant échouée. 
          
          Cliquer sur le lien ci-dessous pour vérifier votre compte :
                  
          'https://www.ixewo.com/activation?token=$token'

          Cordialement
                  
          L'équipe ixewo";
                          
          $mail->send();
              
          $message1 = "Le mail d'activation a été renvoyé! (vérifier aussi vos spams)";
          
        } catch (Exception $e) {
          $message4 = "Le message n'a pas pu être envoyé. Erreur de Mailer : {$mail->ErrorInfo}";
          echo "Erreur : " . $e->getMessage();
        }
      } else {
        $message2 = "Utilisateur non trouvé avec cet e-mail. Veuillez créer un compte";
      }
    } else {
      $message3 = "Adresse e-mail invalide";
    }
  }

?>


<html lang="fr">

    <head>

            <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

            <title>Renvoyer mail - ixewo</title>

            <meta name="description" content="Renvoyer un mail de configuration n'a jamais été si facile.">

            <meta property="og:url" content="https://www.ixewo.com" />

            <meta property="og:type" content="website"/>

            <meta property="og:title" content="ixewo"/>

            <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

            <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>
            
            <meta charset="UTF-8">

            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <link rel="stylesheet" type="text/css" href="17-styles.css">

            <script src="17-script.js"></script>
                        
    </head>


    <body>

        <?php include '1-header.php'; ?>

        <section class="main">
            
            <section class="container-consoles">
                <div class="titre-consoles">
                    <h1>Renvoyer le mail d'activation</h1>
                </div>
            </section>

            <section class="register-informations">

                <?php if ($message1): ?>
                    <p class="message-b"><?php echo $message1; ?></p>
                <?php endif; ?>

                <?php if ($message2): ?>
                    <p class="message-r"><?php echo $message2; ?></p>
                <?php endif; ?>

                <?php if ($message3): ?>
                    <p class="message-r"><?php echo $message3; ?></p>
                <?php endif; ?>

                <form class="register-section" method="post">
                    <input class="email" type="email" name="email" placeholder="Votre adresse e-mail" required>

                    <button class="login" type="submit">Renvoyer</button>
                </form>
            
            </section>

            <section class="container-consoles1">
                <div class="titre-consoles1">
                    <h1>renvoyer le mail d'activation</h1>
                </div>
            </section>

        </section>

        <?php include '11-footer.html'; ?>

    </body>
</html>
