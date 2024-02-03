<?php
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

  // Requête pour obtenir les infomations de l'acheteur 
  $stmt = $dbh->prepare("SELECT * FROM phpauth_users WHERE id = :uid");
  $stmt->bindParam(':uid', $uid);
  $stmt->execute(array(":uid" => $uid));
  $acheteur = $stmt->fetch(PDO::FETCH_ASSOC);
    

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Nettoyer et valider les données du formulaire
    $Nom = htmlspecialchars(stripslashes(trim($_POST['Nom'])));
    $RIB = htmlspecialchars(stripslashes(trim($_POST['RIB'])));
    $IBAN = htmlspecialchars(stripslashes(trim($_POST['IBAN'])));
    $BIC = htmlspecialchars(stripslashes(trim($_POST['BIC-SWIFT'])));

    // Préparation de la requête SQL
    $stmt = $dbh->prepare("UPDATE phpauth_users SET Nom = :nom, RIB = :rib, IBAN = :iban, BIC = :bic WHERE id = :uid");

    // Liaison des paramètres
    $stmt->bindParam(':nom', $Nom);
    $stmt->bindParam(':rib', $RIB);
    $stmt->bindParam(':iban', $IBAN);
    $stmt->bindParam(':bic', $BIC);
    $stmt->bindParam(':uid', $uid);

    // Exécution de la requête
    $stmt->execute();

    // Exécution de la requête
    $executeSuccess = $stmt->execute();

    if ($executeSuccess) {
      $message1 = "Informations bancaires enregistrées !";

      // Recharger les informations de l'utilisateur après l'enregistrement
      $stmt = $dbh->prepare("SELECT * FROM phpauth_users WHERE id = :uid");
      $stmt->bindParam(':uid', $uid);
      $stmt->execute();
      $acheteur = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      // Gestion des erreurs
      $errorInfo = $stmt->errorInfo();
      $message2 = "Erreur lors de l'enregistrement : " . $errorInfo[2];
    }
  } else {
    // Chargement initial des informations de l'utilisateur
    $stmt = $dbh->prepare("SELECT * FROM phpauth_users WHERE id = :uid");
    $stmt->bindParam(':uid', $uid);
    $stmt->execute();
    $acheteur = $stmt->fetch(PDO::FETCH_ASSOC);
  }

?>


<html lang="fr">

    <head>

        <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

        <title>Porte-monnaie - ixewo</title>

        <meta charset="UTF-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="../css/38-styles.css">
        
    </head>

    <body>

        <?php include '1-header.php'; ?>

        <section class="main">

            <section class="container-consoles">
                <div class="titre-consoles">
                    <h1>Porte-Monnaie</h1>
                </div>
            </section>

            <section class="register-informations">

                <?php if ($message1): ?>
                    <p class="message-v"><?php echo $message1; ?></p>
                <?php endif; ?>

                <?php if ($message2): ?>
                    <p class="message-r"><?php echo $message2; ?></p>
                <?php endif; ?>
            
                <form class="register-section" method="POST">

                    <input class="input" type="text" name="Nom" placeholder="NOM - Prénom" value="<?php echo $acheteur['Nom']; ?>" required>
                    <input class="input" type="text" name="RIB" placeholder="RIB" value="<?php echo $acheteur['RIB']; ?>"required>
                    <input class="input" type="text" name="IBAN" placeholder="IBAN" value="<?php echo $acheteur['IBAN']; ?>" required>
                    <input class="input" type="text" name="BIC-SWIFT" placeholder="BIC-SWIFT" value="<?php echo $acheteur['BIC']; ?>" required>

                    <input class="login" type="submit" value="Enregistrer">

                </form>

            </section>

            <section class="container-consoles1">
                <div class="titre-consoles1">
                    <h1> ixewo</h1>
                </div>
            </section>

        </section>

        <?php include '../11-footer.html'; ?>

    </body>
    
</html>
