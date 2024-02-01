<?php
    session_start();

    header("Access-Control-Allow-Origin: *****");
    
    // Importer le fichier de connexion à la base de données
    require 'db_connect_users.php';

    // Importer le fichier de connexion à la base de données
    include 'db_connect_annonces.php';

    // Importer PHP Mailer 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'Login/vendor/phpmailer/phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    
    // Check if the user is logged in and set $isLoggedIn accordingly
    $isLoggedIn = isset($_COOKIE['phpauth_session_cookie']) && $_COOKIE['phpauth_session_cookie'] === true;

    // Requête pour obtenir les infomations de l'acheteur 
    $hash = $_COOKIE['phpauth_session_cookie'];
    $uid = $auth->getSessionUID($hash);
    $stmt = $dbh->prepare("SELECT * FROM phpauth_users WHERE id = :uid");
    $stmt->bindParam(':uid', $uid);
    $stmt->execute(array(":uid" => $uid));
    $acheteur = $stmt->fetch(PDO::FETCH_ASSOC);

    // Récupération des informations du produit souhaité
    if (!isset($_GET['id'])) {
        die("Erreur : ID du produit non spécifié.");
    }

    $product_id = $_GET['id'];

    $stmt = $pdoAnnonces->prepare("SELECT * FROM annonces WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        die("Erreur : Impossible de récupérer les détails du produit.");
    }

    // récupérer l'uid du produit
    $annonce_uid = $product['uid'];
    
    
    // récupérer l'uid du déposeur d'annonces
    $stmt = $dbh->prepare("SELECT * FROM phpauth_users WHERE id = ?");
    $stmt->execute([$annonce_uid]); 

    $vendeur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vendeur) {
        // Handle the error appropriately
        $vendeur = []; // Make sure $users is always an array to avoid warnings
    }

    $prixProduit = $product['prix'];
    $fraisLivraison = 5.00;
    $fraisSupplementaires = $prixProduit * 0.05;
    $prixTotal = $prixProduit + $fraisLivraison + $fraisSupplementaires;
    $_SESSION['prixTotal'] = $prixTotal;
    $_SESSION['titreProduit'] = $product['titre'];
    $_SESSION['imageProduit'] = $product['image_path1'];


    // Répétez pour tout autre paramètre nécessaire


    // Formatage du prix
    $prixFormatte1 = number_format($prixTotal, 2, '.', ' ');
    $prixFormatte2 = number_format($product['prix'], 2, '.', ' ');
    $prixFormatte3 = number_format($fraisLivraison, 2, '.', ' ');
    $prixFormatte4 = number_format($fraisSupplementaires, 2, '.', ' ');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      
      try {
        $mail->isSMTP();
        $mail->Host = '*****';
        $mail->SMTPAuth = true;
        
        $mail->Username = '*****'; 
        $mail->Password = '*****'; 
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('*****', 'enregistrement - ixewo');
        $mail->addAddress('paiement.completed@ixewo.com');

        $mail->Subject = "Achat effectue par " . $acheteur['userName'] . " !!! ";
        $mail->Body = "

        Expediteur : 
        Utilisateur : " . $vendeur['userName'] . "
        NOM : " . $vendeur['lastName'] . "
        Prenom : " . $vendeur['firstName'] . "
        Mail : " . $vendeur['email'] . "
        Numero de rue : " . $vendeur['NdeRue'] . "
        Rue : " . $vendeur['Rue'] . "
        Code postal : " . $vendeur['CPostal'] . "
        Ville : " . $vendeur['Ville'] . "

        Informations bancaires :
        Nom : " . $vendeur['Nom'] . "
        RIB : " . $vendeur['RIB'] . "
        IBAN : " . $vendeur['IBAN'] . "
        BIC : " . $vendeur['BIC'] . "

        Destinataire : 
        Utilisateur : " . $acheteur['userName'] . "
        Nom : " . $acheteur['lastName'] . "
        Prenom : " . $acheteur['firstName'] . "
        Mail : " . $acheteur['email'] . "
        Numero de rue : " . $acheteur['NdeRue'] . "
        Rue : " . $acheteur['Rue'] . "
        Code postal : " . $acheteur['CPostal'] . "
        Ville : " . $acheteur['Ville'] . "

        Destination (Point-Relay) : 
        Nom du point relay : " . $_POST['cb_Nom'] . "
        Numero de rue / Rue : " . $_POST['cb_Adresse'] . "
        Code Postal : " . $_POST['cb_CP'] . "
        Ville : " . $_POST['cb_Ville'] . "
        Pays : " . $_POST['cb_Pays'] . "
        

        Produit : 
        Titre : " . $product['titre']. "
        Prix : " . $prixFormatte2 . " euros

        Montant final : 
        " . $prixFormatte1 . " euros ";

        $mail->send();

        // Redirection vers checkout.php après l'envoi de l'email
        header('Location: paiement');


      } catch (Exception $e) {
        http_response_code(500); // Envoyer un code d'état HTTP 500 pour indiquer une erreur serveur
        echo json_encode(["success" => false, "message" => "Mailer Error: " . $e->getMessage()]);
      }

    }

?>

<html lang="fr">
  <head>

    <title>Finalisation - ixewo</title>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

    <meta  charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="Pour acheter une console, un ordinateur, un jeux-vidéo et plus il faut passer par là !!!">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>


    <link rel="stylesheet" type="text/css" href="40-styles.css">

    <!-- JQuery required -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    
    <!-- Leaflet dependency is not required since it is loaded by the plugin -->
    <script src="//unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" type="text/css" href="//unpkg.com/leaflet/dist/leaflet.css">

    <!-- JS plugin for the Parcelshop Picker Widget MR using JQuery -->    
    <script src="//widget.mondialrelay.com/parcelshop-picker/jquery.plugin.mondialrelay.parcelshoppicker.min.js"></script>

    <!-- Stripe plugin -->
    <script src="https://js.stripe.com/v3/"></script>

  </head>
  
  <body>

    <?php include '1-header.php'; ?>

    <section class="main">

      <section class="container-consoles">
        <div class="titre-consoles">
          <h1>1/3 : Livraison</h1>
        </div>
      </section>

      <!-- Section 1: Choix du Point Relay -->
      <div id="point-relay-choice">
        
        <!-- HTML Element in which the Parcelshop Picker Widget is loaded -->
        <div id="Zone_Widget"></div>

        <input type="hidden" id="ParcelCode" name="ParcelCode"/>

      </div>

      <section class="container-consoles2">
        <div class="titre-consoles2">
          <h1>2/3 : Vérification</h1>
        </div>
      </section>

      
      <section class="adresses">

        <div class="expediteur">
              <h2>Expéditeur :</h2>
              
              <h4><?php echo $vendeur['userName'];?></h4>

              <label class="checkbox-exp">
                <input class="ckeckbox" type="checkbox" id="exp" required> Je confirme avoir contacté <span class="h4"><?php echo $vendeur['userName'];?><span class="h5">*</span></input>
              </label>
              
        </div>

        <div class="destinataire">
              <!-- Balises HTML utilisées dans la fonction de CallBack pour reçevoir des données à afficher -->
              <h2>Destination : </h2>
        
              <h1>Nom : <span class="h3" id="cb_Nom"></span></h1>
              <h1>Adresse : <span class="h3" id="cb_Adresse"></span></h1>
              <h1>Adresse : <span class="h3" id="cb_CP"></span> <span class="h3" id="cb_Ville"></span> <span class="h3" id="cb_Pays"></span></h1>

              <label class="checkbox-des">
                <input class="ckeckbox" type="checkbox" id="des" required><span class="h4">Je confirme cette adresse de livraison</span><span class="h5">*</span></input>
              </label>
        </div>

      </section>

      <section class="container-consoles2">
        <div class="titre-consoles2">
          <h1>3/3 : Paiement</h1>
        </div>
      </section>

      <section class="paiement">

        <div class="Fprix">

          <h2>Détail du prix :</h2>
            
            <h1 id="pp">Prix du produit : <span class="h3"><?php echo $prixFormatte2;?> €</span></h1>
            <h1 id="l">Livraison : <span class="h3"><?php echo $prixFormatte3; ?> €</span></h1>
            <h1 id="f">Frais : <span class="h3"><?php echo $prixFormatte4; ?> €</h1>
            <h3>Ses 5% de frais vous offre :<h3>
            <h3>- Une protection en cas de litiges,</h3>
            <h3>- Un site fiable et sécurisé.</h3>

        </div>

        <div class="Dprix">
    
            <h2>Prix final :</h2>
            
            <h4><?php echo $prixFormatte1; ?> €</h4>

            <form class="formpaiement" method="post" action="livraison?id=<?php echo $product['id']; ?>">

              <input type="hidden" id="cb_Adresse" name="cb_Adresse">
              <input type="hidden" id="cb_CP" name="cb_CP">
              <input type="hidden" id="cb_Ville" name="cb_Ville">
              <input type="hidden" id="cb_Pays" name="cb_Pays">
              <input type="hidden" id="cb_Nom" name="cb_Nom">
              <!-- Plus de champs pour cb_Ville et cb_Pays -->


              <button type="submit" class="boutton-paiement" id="stripePaymentButton">Acheter</button>

            </form>

        </div>

      </section>

      <section class="container-consoles1">
          <div class="titre-consoles1">
            <h1>contact</h1>
          </div>
      </section>

    </section>

    <?php include '11-footer.html'; ?>

    <script src="40-script.js"></script>

  </body>

</html>