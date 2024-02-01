<?php
  session_start();

  // Importer le fichier de connexion à la base de données
  require 'db_connect_users.php';

  // Importer le fichier de connexion à la base de données
  include 'db_connect_annonces.php';

  $message = "";

  // Initialisation de la variable $favorites
  $favorites = [];

  // Vérifiez si l'utilisateur est connecté
  if (isset($_COOKIE['phpauth_session_cookie'])) {
      $hash = $_COOKIE['phpauth_session_cookie'];
      $userId = $auth->getSessionUID($hash);

      // Récupérez les produits favoris de l'utilisateur
      $query = "SELECT annonces.* FROM annonces
                INNER JOIN UserFavorites ON annonces.id = UserFavorites.product_id
                WHERE UserFavorites.user_id = ?";
      $stmt = $pdoAnnonces->prepare($query);
      $stmt->bindParam(1, $userId);
      $stmt->execute();
      $favoritedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Remplir $favorites avec les identifiants des produits
      foreach ($favoritedProducts as $product) {
      $favorites[] = $product['id'];
    
    }
  
  } else {

    // Gérer l'absence du cookie, par exemple rediriger vers la page de connexion ou afficher un message d'erreur.
    header('Location: connection-profil');

  }

  error_reporting(E_ALL);  // Display all errors

?>

<html lang="fr">

    <head>

        <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

        <title>Favoris - ixewo</title>

        <meta name="description" content="Vos produits enregistrés tels que les consoles, les ordinateurs, les jeux-vidéo et plus encore sont ici !!!">

        <meta property="og:url" content="https://www.ixewo.com" />

        <meta property="og:type" content="website"/>

        <meta property="og:title" content="ixewo"/>

        <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

        <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

        <meta charset="UTF-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="22-styles.css">
        
    </head>

    <body>

        <?php include '1-header.php'; ?>

        <section class="main">
        
          <section class="container-consoles">
            <div class="titre-consoles">
              <h1>Vos favoris</h1>
            </div>
          </section>


          <div class="produits-consoles">

            <?php
                // Chargement base de données
                include 'db_connect_annonces.php';
            ?>

            <?php foreach ($favoritedProducts as $product): ?>
                <?php // Formatage du prix pour chaque produit
                $prixFormatte = number_format($product['prix'], 2, '.', ' '); ?>
                <div class="product-card-consoles">
                  <a href="produits-ordinateurs?id=<?php echo htmlspecialchars($product['id']); ?>"><img src="<?php echo htmlspecialchars($product['image_path1']); ?>"></a>
                    <div class="price-favorite-consoles">
                        <p><?php echo htmlspecialchars($prixFormatte); ?> €</p>
                        <?php
                            // Vérifier si le produit est dans la liste des favoris de l'utilisateur
                            $favoritedClass = in_array($product['id'], $favorites) ? "favorited" : "";
                        ?>
                        <i class="favorite-icon <?php echo $favoritedClass; ?>" data-product-id="<?php echo $product['id']; ?>"></i>
                    </div>
                </div>
            <?php endforeach; ?>


            <?php if ($message): ?>
              <p><?php echo $message; ?></p>
            <?php endif; ?>
            
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
              <h1>vos favoris</h1>
            </div>
          </section>

        </section>
    
        <?php include '11-footer.html'; ?>

        <script src="22-script.js"></script>

    </body>
    
</html>
