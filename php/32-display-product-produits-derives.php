<?php
    // Démarrer la session
    session_start();

    // Importer le fichier de connexion à la base de données
    require 'db_connect_users.php';

    // Importer le fichier de connexion à la base de données des annonces
    require 'db_connect_annonces.php';

    // affichage erreurs
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Get the list of favorite products for the logged-in user
    $favorites = [];

    if (isset($_COOKIE['phpauth_session_cookie'])) {
      $hash = $_COOKIE['phpauth_session_cookie'];
      $userId = $auth->getSessionUID($hash);
      $favoritesQuery = "SELECT product_id FROM UserFavorites WHERE user_id = ?";
      $favoritesStmt = $pdoAnnonces->prepare($favoritesQuery);
      $favoritesStmt->bindParam(1, $userId);
      $favoritesStmt->execute();
      $favorites = $favoritesStmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }   


    // Check if the user is logged in and set $isLoggedIn accordingly
    $isLoggedIn = isset($_COOKIE['phpauth_session_cookie']) && $_COOKIE['phpauth_session_cookie'] === true;


    // Récupération des informations du produit actuel
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

    $users = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$users) {
        // Handle the error appropriately
        $users = []; // Make sure $users is always an array to avoid warnings
    }

    $description = $product['description'];

    // Formatage du prix pour chaque produit
    $prixFormatte = number_format($product['prix'], 2, '.', ' ');

?>

<html lang="fr">

  <head>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

    <title>Produits Dérivés - ixewo</title>

    <meta name="description" content="Un produit de la catégorie Produits Dérivés en vedette pour vous permettre de découvrir notre Univers !!!">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

    <meta charset="UTF-8">
        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="../css/32-styles.css">
    
  </head>

  <body>

    <?php include '1-header.php'; ?>

    <section class="main">

      <div id="errorMessage"></div>

      <section class="container-titre-consoles1">
        <div class="titre-produits-consoles1">
          <h1 class="titre"><?php echo $product['titre']; ?></h1>
        </div>
      </section>

      <!-- Arrière-plan semi-transparent affichage photos-->
      <div class="photos-produits-backdrop"></div>

      <section class="container-photos">

        <?php
          echo '<div class="photos-produits">';

            for ($i = 1; $i <= 5; $i++) {
              $image_path = $product['image_path' . $i];
              if ($image_path) { // Si le chemin de l'image n'est pas vide
                echo '<img class="img' . $i . '" src="' . $image_path . '">';
              }
            }

          echo '</div>';
        ?>


        <div class="controls-container">

          <button id="previmg" class="prev-btn"><</button>
                        
          <div class="progress-img">
            <div class="progress-bar-img"></div>
          </div>

          <button id="nextimg" class="next-btn">></button>
            
        </div>

      

        <script type="text/javascript">
          var isUserLoggedIn = <?php echo json_encode(isset($_COOKIE['phpauth_session_cookie'])); ?>;
        </script>

            
        <div class="titre-prix">

          <div class="prix-favoris">

            <div class="favoris">

              <?php
                          
                echo '<div class="price-favorite-produits">';
                  // Check if the product is in the user's favorites list
                  $favoritedClass = in_array($product['id'], $favorites) ? "favorited" : "";
                  echo '<i class="favorite-icon ' . $favoritedClass . '" data-product-id="' . $product['id'] . '"></i>';
                echo '</div>';   

              ?>
                          
            </div>

            <h1 class="prix"><?php echo $prixFormatte;?> €</h1> 
          
          </div>
                
          <p class="ced"><span class="h2">Catégorie : </span><?php echo $product['categorie'];?></p>
          <p class="ced"><span class="h2">État : </span><?php echo $product['etat'];?></p>
          <p class="ced"><span class="h2">Lieu : </span><?php echo $users['CPostal'] ;?> (<?php echo $users['Ville'] ;?>)</p>
          <p class="ced"><span class="h2">Description: </span></p>
          <pre><?php echo htmlspecialchars($description); ?></pre>


          <a href="livraison?id=<?php echo $product['id']; ?>" class="acheter-button">Acheter</a>



          <a class="contacter-button">Contacter - <?php echo $users['userName'];?></a>
          <?php if (isset($_COOKIE['phpauth_session_cookie'])): ?>

            <p class="contact"><span class="h2">Mail :</span>
              <a class="mail" href="mailto:<?php echo isset($users['email']) ? $users['email'] : ''; ?>?subject=Prise%20de%20contact%20'<?php echo urlencode($product['titre']); ?>'%20sur%20ixewo.com">Envoyer un email</a>
            </p>

          <?php endif; ?>

        </div>

      
      </section>

      <section class="container-titre-consoles2">
        <div class="titre-produits-consoles2">
          <h1>- - -</h1>
        </div>
      </section>

    </section>

    <?php include '../11-footer.html'; ?>

    <script src="../js/32-script.js"></script>

  </body>
    
</html>