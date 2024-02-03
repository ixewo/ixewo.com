<?php
      
  session_start();

  // Importer le fichier de connexion à la base de données
  require 'db_connect_users.php';

  $message = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=*********&response=$recaptcha_response");
    $response_keys = json_decode($response, true);

    if($response_keys["success"]) {
      // Ajouter une vérification pour s'assurer que le compte est activé
      $checkActive = $dbh->prepare("SELECT isactive FROM phpauth_users WHERE email = :email");
      $checkActive->execute([':email' => $email]);
      $isActive = $checkActive->fetchColumn();

      if (!$isActive) {
          $message = "Votre compte n'a pas été activé. Veuillez vérifier votre e-mail.";
      }
      
      // Le reCAPTCHA a été rempli avec succès, procédez à la connexion
      $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
      $password = $_POST['password'];
      $remember = isset($_POST['remember']);

      $result = $auth->login($email, $password, $remember);
      if ($result['error']) {
        $message = $result['message'];
      } else {
        $_SESSION['loggedin'] = true;
        header('Location: vendre');
        exit();
      }
    } else {
      $message = "REMPLISSEZ le reCAPTCHA";// Le reCAPTCHA n'a pas été rempli, affichez un message d'erreur
    }
  }


?>

<html lang="fr">
  <head>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

    <title>Se connecter - ixewo</title>

    <meta name="description" content="Pour vendre vos consoles, ordinateurs, jeux-vidéos et plus, c'est ici !!!">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>
        
    <meta charset="UTF-8">

    <link rel="stylesheet" type="text/css" href="../css/13-styles.css">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
  </head>


  <body>

    <?php include '1-header.php'; ?>

    <script src="https://www.google.com/recaptcha/api.js"></script>    

    <section class="main">

      <section class="container-consoles">
        <div class="titre-consoles">
          <h1>Connectez-vous</h1>
        </div>
      </section>

      <section class="login-informations">

        <h2>Pour vendre, il faut faire partie de l'équipe !!!</h2>
        
        <?php if ($message): ?>
          <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
                  
        <form class="login-section" method="post" action="connection-vendre">
          <input class="email" type="email" name="email" placeholder="Email" required>
          <input class="password" type="password" name="password" placeholder="Mot de passe" required>

          <div class="g-recaptcha" data-sitekey="6LdR_xkpAAAAALq2lge1goZ1fZ7NE5waGSLzst9Q"></div>

          <button class="login" type="submit">Se connecter</button>

          <div class="form-remember-me">
            <label class="checkbox-inline">
              <input class="ckeckbox" type="checkbox" name="remember"> Se souvenir de moi </input>
            </label>
            <a class="oublie" href="mot-de-passe-oublie">Changer le mot de passe </a>
          </div>
                  
        </form>

      </section>

      <section class="container-consoles2">
        <div class="titre-consoles2">
          <h1>Se connecter</h1>
        </div>
      </section>

      <section class="register-informations">

        <h2>Pas d'inquiétudes, vous pouvez nous rejoindre !!!</h2>

        <div class="register-form">

          <a class="register" href="inscription">C'est par ici !!!</a>

        </div>

      </section>

      <section class="container-consoles1">
        <div class="titre-consoles1">
          <h1>Se connecter</h1>
        </div>
      </section>

    </section>

    <?php include '../11-footer.html'; ?>

    <script src="../js/13-script.js"></script>

    


        
  </body>

</html>
