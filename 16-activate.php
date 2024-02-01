<?php

  // Importer le fichier de connexion à la base de données
  require 'db_connect_users.php';
    
  use PHPAuth\Config as PHPAuthConfig;
  use PHPAuth\Auth as PHPAuthAuth;

  $message1 = "";
  $message2 = "";
  $message3 = "";

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {

    $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Vérifiez le token dans la base de données
    $stmt = $dbh->prepare("SELECT * FROM phpauth_requests WHERE token = :token AND type = 'activation'");
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
      $expiryDate = new DateTime($request['expire']);
      $currentDate = new DateTime();

      // Vérifiez si le token n'a pas expiré
      if ($currentDate < $expiryDate) {
        // Activez le compte utilisateur
        $auth->activate($request['uid']);

        // Mettez à jour is_active
        $updateStmt = $dbh->prepare("UPDATE phpauth_users SET isactive = 1 WHERE id = :uid");
        $updateStmt->execute([':uid' => $request['uid']]);
        
        // Activez le compte utilisateur
        $auth->activate($request['uid']);

        // Supprimez le token de la base de données
        $stmt = $dbh->prepare("DELETE FROM phpauth_requests WHERE token = :token");
        $stmt->execute([':token' => $token]);

        $message1 = "Votre compte a été activé avec succès";
                
      } else {
        $message2 = "Token invalide ou expiré";
      }
    } else {
      $message3 = "Token invalide ou expiré";
    }
  }

?>


<html lang="fr">
  <head>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

    <title>Activation - ixewo</title>

    <meta name="description" content="L'activation du compte prend deux secondes, vous pourrez maintenant faire partie de l'équipe !">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>
        
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="16-styles.css">
            
  </head>


  <body>

    <?php include '1-header.php'; ?>

    <section class="main">
        
      <section class="container-consoles">
        <div class="titre-consoles">
          <h1>Activation</h1>
        </div>
      </section>

      <?php if ($message1): ?>
        <h2 class="message-b"><?php echo $message1; ?></h2>
      <?php endif; ?>

      <?php if ($message2): ?>
        <h2 class="message-r"><?php echo $message2; ?></h2>
      <?php endif; ?>

      <?php if ($message3): ?>
        <h2 class="message-r"><?php echo $message3; ?></h2>
      <?php endif; ?>
      
      <section class="activate-informations">
      
        <a class="home-but" href="index.php">Accueil</a>

        <a class="profile-but" href="profil">Profil</a>

        <a class="vendre-but" href="vendre">Vendre</a>

      </section>

      <section class="container-consoles1">
        <div class="titre-consoles1">
          <h1>Activation</h1>
        </div>
      </section>

    </section>

    <?php include '11-footer.html'; ?>
        
  </body>

</html>