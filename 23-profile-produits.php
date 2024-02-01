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
        exit();
    }
    //error_reporting(E_ALL);  // Display all errors

    // Requête pour obtenir tous les produits de cet utilisateur
    $stmt = $pdoAnnonces->prepare("SELECT * FROM annonces WHERE uid = :uid");
    $stmt->bindParam(':uid', $uid);
    $stmt->execute(array(":uid" => $uid));
    $products = $stmt->fetchAll();
?>

<html lang="fr">

  <head>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

    <title>Vos produits - ixewo</title>

    <meta name="description" content="Vos consoles, ordinateurs, jeux-vidéo et produits dérivés ajoutés chez ixewo sont ici !!!">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

    <meta charset="UTF-8">
        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="23-styles.css">
          
  </head>

  <body>

    <?php include '1-header.php'; ?>

    <section class="main">

      <section class="container-consoles">
        <div class="titre-consoles">
          <h1>Vos produits</h1>
        </div>
      </section>


      <div class="produits-consoles">

        <?php foreach ($products as $product): ?>
          <div class="product-card-consoles">  
            <a href="produits-consoles?id=<?php echo $product['id']; ?>"> <img class="im" src="<?php echo $product['image_path1']; ?>"></a>

            <div class="price-favorite-consoles"> 
              <a class="modifier" href="mise-a-jour-produits?id=<?php echo $product['id']; ?>">Modifier</a>
              <a class="supprimer" href="28-delete-product.php?id=<?php echo $product['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?');"><img class="sup" src="/images/delete.png"></a>
            </div>
          </div>  
        <?php endforeach; ?>

      </div>

      <div class="controls-container">
        <button id="prevPagec" class="consoles-prev-btn"><</button>
                      
        <div class="progress-container-consoles">
          <div class="progress-bar-consoles"></div>
        </div>

        <button id="nextPagec" class="consoles-next-btn">></button>
            
      </div>

      <section class="container-consoles1">
        <div class="titre-consoles1">
          <h1>vos produits</h1>
        </div>
      </section>

    </section>

    <?php include '11-footer.html'; ?>

    <script src="23-script.js"></script>

  </body>
    
</html>