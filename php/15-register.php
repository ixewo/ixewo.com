<?php
  // Importer le fichier de connexion à la base de données
  require 'db_connect_users.php';

  // Importer PHP Mailer 
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require 'Login/vendor/phpmailer/phpmailer/src/SMTP.php';

  $mail = new PHPMailer(true);


  $message1 = "";
  $message2 = "";
  $message3 = ""; //vert
  $message4 = "";
  $message5 = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Traitement du reCAPTCHA
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=*****&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if ($response_keys["success"]) {
      // Nettoyer et valider les données du formulaire
      $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
      $firstName = htmlspecialchars(stripslashes(trim($_POST['firstName'])));
      $lastName = htmlspecialchars(stripslashes(trim($_POST['lastName'])));
      $userName = htmlspecialchars(stripslashes(trim($_POST['userName'])));
      $NdeRue = htmlspecialchars(stripslashes(trim($_POST['NdeRue'])));
      $Rue = htmlspecialchars(stripslashes(trim($_POST['Rue'])));
      $CPostal = htmlspecialchars(stripslashes(trim($_POST['CPostal'])));
      $Ville = htmlspecialchars(stripslashes(trim($_POST['Ville'])));

      $password = $_POST['password'];  // Le mot de passe sera haché par PHPAuth, pas besoin de le filtrer
      $repeatPassword = $_POST['repeatPassword'];
      
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Format d'e-mail invalide.";
        exit;
      }

      // Liste des domaines de messagerie autorisés
      //$allowed_domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'live.com', 'icloud.com', 'aol.com', 'mail.com', 'protonmail.com', 'zohomail.com', 'gmx.com', 'gmx.net', 'yandex.com', 'xboxlive.com', 'playstation.com', 'orange.fr', 'bbox.fr', 'sfr.fr'];
      //$email_domain = substr(strrchr($email, "@"), 1);

      // if (!in_array($email_domain, $allowed_domains)) {
          // $message = "Adresse e-mail non autorisée.";
      // } else {
      
      // Vérifier si l'e-mail ou le nom d'utilisateur existent déjà
      // Récupérer l'email et le nom d'utilisateur depuis la requête POST
      $email = $_POST['email'];
      $userName = $_POST['userName'];

      // Préparer et exécuter la requête pour vérifier l'email
      $stmtEmail = $dbh->prepare("SELECT COUNT(*) FROM phpauth_users WHERE email = ?");
      $stmtEmail->execute([$email]);

      // Vérifier si l'email existe déjà
      if ($stmtEmail->fetchColumn() > 0) {
        $message1 = "E-mail déjà utilisé.";
      } else {
        // Préparer et exécuter la requête pour vérifier le nom d'utilisateur
        $stmtUser = $dbh->prepare("SELECT COUNT(*) FROM phpauth_users WHERE userName = ?");
        $stmtUser->execute([$userName]);

        // Vérifier si le nom d'utilisateur existe déjà
        if ($stmtUser->fetchColumn() > 0) {
          $message2 = "Nom d'utilisateur déjà utilisé.";
        } else {
          // Inscription de l'utilisateur

          // 1. Insérez l'utilisateur
          $result = $auth->register($email, $_POST['password'], $_POST['repeatPassword'], [
            'firstName' => $_POST['firstName'],
            'lastName' => $_POST['lastName'],
            'userName' => $_POST['userName'],
            'NdeRue' => $_POST['NdeRue'],
            'Rue' => $_POST['Rue'],
            'CPostal' => $_POST['CPostal'],
            'Ville' => $_POST['Ville']
          ]);
                          
          // 2. Récupérez l'ID de la dernière insertion
          $stmt = $dbh->prepare("SELECT id FROM phpauth_users WHERE email = :email");
          $stmt->execute([':email' => $_POST['email']]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);

          //nouveau
          $stmt = $dbh->prepare("UPDATE phpauth_users SET isactive = 0 WHERE id = :uid");
          $stmt->execute([':uid' => $result['uid']]);

          // Ancien
          if ($row) {
            $uid = $row['id'];
          } else {
            // Adresse e-mail non trouvée
            echo "mail non trouvé";
          }
                          

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
            $mail->Password = '*****';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            // $mail->SMTPDebug = 2; // Activer le débogage SMTP

            $mail->setFrom('*****', 'ixewo - enregistrement');
            $mail->addAddress($email, $firstName); // Utiliser l'email et le prénom de l'utilisateur
            
            $mail->Subject = "Confirmation de votre inscription";
            $mail->Body = "
            Bienvenue sur notre site !

            Merci de vous être inscrit. Veuillez cliquer sur le lien ci-dessous pour vérifier votre compte :
              
            'https://www.ixewo.com/activation?token=$token'

            Cordialement
              
            L'équipe ixewo";
                      
            $mail->send();

            $message3 = "Inscription réussie! Vérifiez votre e-mail pour activer votre compte.";
            header('Refresh: 3; URL=index.php');

          } catch (Exception $e) {
            $message4 = "Le message n'a pas pu être envoyé. Erreur de Mailer : {$mail->ErrorInfo}";
            echo "Erreur : " . $e->getMessage();
          }
        }      
      }
    } else {
      $message5 = "REMPLISSEZ le ReCAPTCHA.";
    }
  }

?>

<html lang="fr">
    <head>

        <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

        <title>Enregistrement - ixewo</title>

        <meta name="description" content="Pour acheter et vendre des consoles, des ordinateurs, des jeux-vidéos et plus enregistrez-vous.">

        <meta property="og:url" content="https://www.ixewo.com" />

        <meta property="og:type" content="website"/>

        <meta property="og:title" content="ixewo"/>

        <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

        <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>
            
        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="../css/15-styles.css">

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            
    </head>


    <body>

        <?php include '1-header.php'; ?>

        <section class="main">
            
            <section class="container-consoles">
                <div class="titre-consoles">
                    <h1>1/2 - Profil</h1>
                </div>
            </section>

            <?php if ($message1): ?>
              <p class="message-r"><?php echo $message1; ?></p>
            <?php endif; ?>

            <?php if ($message2): ?>
              <p class="message-r"><?php echo $message2; ?></p>
            <?php endif; ?>

            <?php if ($message3): ?>
              <p class="message-v"><?php echo $message3; ?></p>
            <?php endif; ?>

            <?php if ($message4): ?>
              <p class="message-r"><?php echo $message4; ?></p>
            <?php endif; ?>

            <?php if ($message5): ?>
              <p class="message-r"><?php echo $message5; ?></p>
            <?php endif; ?>
            
            <section class="register-informations">

                <form class="register-section" method="post" action="inscription">

                    <input class="name" type="text" name="firstName" placeholder="Prénom" required>
                    <input class="lastname" type="text" name="lastName" placeholder="Nom" required>
                    <input class="username" type="text" name="userName" placeholder="Nom d'utilisateur" required>
                    <input class="email" type="email" name="email" placeholder="Email" required>
                    <input class="password" type="password" name="password" placeholder="Mot de passe" required>
                    <input class="repaetpassword" type="password" name="repeatPassword" placeholder="Répétez le mot de passe" required>


                    <div class="titre-consoles3">
                      <h1>2/2 - Adresse</h1>
                    </div>

                    <input class="NdeRue" type="text" name="NdeRue" placeholder="Numéro de Rue" required>
                    <input class="Rue" type="text" name="Rue" placeholder="Rue" required>
                    <input class="CPostal" type="text" name="CPostal" placeholder="Code Postal" required>
                    <input class="Ville" type="text" name="Ville" placeholder="Ville" required>

                    <label class="checkbox-inline">
                          <input class="ckeckbox" type="checkbox" required> J'accepte les <a href="cgv">CGV</a> et les <a href="cgu">CGU</a></input>
                    </label>

                    <div class="g-recaptcha" data-sitekey="6LdR_xkpAAAAALq2lge1goZ1fZ7NE5waGSLzst9Q"></div>
                            
                    <button class="login" type="submit">S'enregistrer</button>

                </form>
                
            </section>

            <section class="container-consoles2">
                <div class="titre-consoles2">
                    <h1>2/2 - Adresse : </h1>
                </div>
            </section>

            <section class="bout">

              <a class="but" href="nouvelle-activation">Renvoyer mail d'activation</a>

            </section>

            
            <section class="container-consoles1">
                <div class="titre-consoles1">
                    <h1>Se connecter</h1>
                </div>
            </section>

        </section>

        <?php include '../11-footer.html'; ?>

        <script src="../js/15-script.js"></script>
        
    </body>

</html>