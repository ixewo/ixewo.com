<?php

    // Importer le fichier de connexion à la base de données
    require 'db_connect_users.php';

    // Importer le fichier de connexion à la base de données
    include 'db_connect_annonces.php';

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

?>

<html lang="fr">
  <head>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

    <title>ixewo - Le site spécialiste de l'achat vente jeux vidéo console ordinateur occasion</title>

    <meta name="description" content="Un grand choix de jeux vidéo d'occasion, accessoires et ses dérivés. Un grand choix de console d'occasion pas cher">

    <meta charset="UTF-8" />
    
    <link rel="stylesheet" type="text/css" href="../css/2-styles.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>
    
  </head>

  <body>
    
    <?php include '1-header.php';?>

    <section class="achat-vente-jeux-vidéo">

      <section class="occasion-consoles-web">
          <div class="header-container">
              <img src="/images/logo-ixewo-black.png" class="logo-ixewo-black"/>
              <h1 class="achat-vente-jeux-video-occasion">ixewo</h1>
          </div>
          <div class="subheader-container">
              <h2 class="achat-revente-occasion-france">Vos produits sont une mine d'or.</h2>
              <a href="le-projet" class="button-ixewo">Pourquoi ?</a>
          </div>
      </section>

      <div id="errorMessage"></div>

      <!--Section affichage consoles-->
      <section class="home-consoles">

        <div class="vers-consoles">
          <h1 class="achat-vente-occasion-console">Consoles</h1>
          <h2 class="achat-revente-occasion-consoles">Vous en cherchez une ?</h2>
          <a href="consoles" class="consoles-button">Votre bonheur est là</a>
        </div>

        <div class="produits-consoles">
        
          <?php
              // Chargement base de données
              include 'db_connect_annonces.php';

              // Requête pour obtenir tous les produits de la catégorie "consoles"
              $query = "SELECT * FROM annonces WHERE categorie = 'Consoles' ORDER BY RAND()";
              $stmt = $pdoAnnonces->prepare($query);
              $stmt->execute();

              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Formatage du prix pour chaque produit
                $prixFormatte = number_format($row['prix'], 2, '.', ' ');
                echo '<div class="product-card-consoles">';
                echo '<a href="produits-consoles?id=' .$row['id'].'"><img src="' . $row['image_path1'] . '"></a>';
                echo '<div class="price-favorite-consoles">';
                // Affichage du produit avec le prix formaté
                echo '<p>' . $prixFormatte . ' €</p>';
                // Check if the product is in the user's favorites list
                $favoritedClass = in_array($row['id'], $favorites) ? "favorited" : "";
                echo '<i class="favorite-icon ' . $favoritedClass . '" data-product-id="' . $row['id'] . '"></i>';
                echo '</div>';
                echo '</div>';
              }
          ?>


          <button id="prevPagec" class="consoles-prev-btn"><</button>
          
          <button id="nextPagec" class="consoles-next-btn">></button>
          
          <div class="progress-container-consoles">
                <div class="progress-bar-consoles"></div>
          </div>

        </div>

      </section>





      <!--Section affichage ordianteurs-->
      <section class="home-ordinateurs">

        <div class="produits-ordinateurs">
        
          <?php
              // Chargement base de données
              include 'db_connect_annonces.php';

              // Requête pour obtenir tous les produits de la catégorie "consoles"
              $query = "SELECT * FROM annonces WHERE categorie = 'Ordinateurs' ORDER BY RAND()";
              $stmt = $pdoAnnonces->prepare($query);
              $stmt->execute();
          
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Formatage du prix pour chaque produit
                $prixFormatte = number_format($row['prix'], 2, '.', ' ');
                echo '<div class="product-card-ordinateurs">';
                echo '<a href="produits-ordinateurs?id=' .$row['id'].'"><img src="' . $row['image_path1'] . '"></a>';
                echo '<div class="price-favorite-ordinateurs">';
                // Affichage du produit avec le prix formaté
                echo '<p>' . $prixFormatte . ' €</p>';
                // Check if the product is in the user's favorites list
                $favoritedClass = in_array($row['id'], $favorites) ? "favorited" : "";
                echo '<i class="favorite-icon ' . $favoritedClass . '" data-product-id="' . $row['id'] . '"></i>';
                echo '</div>';
                echo '</div>';
              }
          ?>


          <button id="prevPageo" class="ordinateurs-prev-btn"><</button>
          
          <button id="nextPageo" class="ordinateurs-next-btn">></button>
          
          <div class="progress-container-ordinateurs">
                <div class="progress-bar-ordinateurs"></div>
          </div>

        </div>

        <div class="vers-ordinateurs">
          <h1 class="achat-revente-occasion-ordinateurs">Ordinateurs</h1>
          <h2 class="achat-vente-occasion-ordinateurs">Vous voulez changer ?</h2>
          <a href="ordinateurs" class="ordinateurs-button">C'est par ici</a>
        </div>

      </section>







      <!--Section affichage jeux-videos-->
      <section class="home-jeux">

        <div class="vers-jeux">
          <h1 class="achat-vente-occasion-jeux-video">Jeux-Vidéo</h1>
          <h2 class="achat-revente-occasion-jeux-vidéo">Lesquels vous interessent ?</h2>
          <a href="jeux-videos" class="jeux-button">Ils sont tous là</a>
        </div>

        <div class="produits-jeux">
        
          <?php
              // Chargement base de données
              include 'db_connect_annonces.php';

              // Requête pour obtenir tous les produits de la catégorie "consoles"
              $query = "SELECT * FROM annonces WHERE categorie = 'Jeux-Vidéos' ORDER BY RAND()";
              $stmt = $pdoAnnonces->prepare($query);
              $stmt->execute();
          
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Formatage du prix pour chaque produit
                $prixFormatte = number_format($row['prix'], 2, '.', ' ');
                echo '<div class="product-card-jeux">';
                echo '<a href="produits-jeux-video?id=' .$row['id'].'"><img src="' . $row['image_path1'] . '"></a>';
                echo '<div class="price-favorite-jeux">';
                // Affichage du produit avec le prix formaté
                echo '<p>' . $prixFormatte . ' €</p>';
                // Check if the product is in the user's favorites list
                $favoritedClass = in_array($row['id'], $favorites) ? "favorited" : "";
                echo '<i class="favorite-icon ' . $favoritedClass . '" data-product-id="' . $row['id'] . '"></i>';
                echo '</div>';
                echo '</div>';
              }
          ?>


          <button id="prevPagej" class="jeux-prev-btn"><</button>
          
          <button id="nextPagej" class="jeux-next-btn">></button>
          
          <div class="progress-container-jeux">
                <div class="progress-bar-jeux"></div>
          </div>

        </div>

      </section>







      <!--Section affichage ordianteurs-->
      <section class="home-produits">

        <div class="produits-produits">
        
          <?php
              // Chargement base de données
              include 'db_connect_annonces.php';

              // Requête pour obtenir tous les produits de la catégorie "produits dérivés"
              $query = "SELECT * FROM annonces WHERE categorie = 'Produits-Dérivés' ORDER BY RAND()";
              $stmt = $pdoAnnonces->prepare($query);
              $stmt->execute();

              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Formatage du prix pour chaque produit
                $prixFormatte = number_format($row['prix'], 2, '.', ' ');
                echo '<div class="product-card-produits">';
                echo '<a href="produits-produits-derives?id=' .$row['id'].'"><img src="' . $row['image_path1'] . '"></a>';
                echo '<div class="price-favorite-produits">';
                // Affichage du produit avec le prix formaté
                echo '<p>' . $prixFormatte . ' €</p>';
                // Check if the product is in the user's favorites list
                $favoritedClass = in_array($row['id'], $favorites) ? "favorited" : "";
                echo '<i class="favorite-icon ' . $favoritedClass . '" data-product-id="' . $row['id'] . '"></i>';
                echo '</div>';
                echo '</div>';
              }
          ?>


          <button id="prevPagep" class="produits-prev-btn"><</button>
          
          <button id="nextPagep" class="produits-next-btn">></button>
          
          <div class="progress-container-produits">
                <div class="progress-bar-produits"></div>
          </div>

        </div>

        <div class="vers-produits">
          <h1 class="achat-vente-occasion-produits-derives">Produits Dérivés</h1>
          <h2 class="achat-revente-occasion-produits-dérivés">Votre collection évolue ?</h2>
          <a href="produits-derives" class="produits-button">Venez par ici</a>
        </div>

      </section>





      <section class="main-banner-contact">
        <h1 class="achat-vente-occasion-gaming">Contact</h1>
        <h2 class="achat-revente-occasion-gaming">Des retours positifs ou négatifs ?</h2>
        <a href="contact" class="button-contact">Vous serez écoutés ici</a>
      </section>

    </section>

    <?php include '../11-footer.html'; ?>

    <script src="../js/2-script.js"></script>
        
  </body>
  
</html>