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
  
  // Requête de base pour obtenir tous les produits de la recherche
  $query = "SELECT * FROM annonces  WHERE categorie = 'Consoles'";
  

  // Initialisation du tableau des clauses WHERE et des paramètres
  $whereClauses = [];
  $params = [];

  // Initialisation des variables de filtrage
  $selectedPriceRange = $_GET['price_range'] ?? "";
  $selectedetat = $_GET['product_state'] ?? "";
  $selectedorder = $_GET['sort_order'] ?? "";
  $filtersApplied = !empty($selectedPriceRange) || !empty($selectedetat) || !empty($selectedorder);


  // Application des filtres si présents
  if ($filtersApplied) {
    // Filtre de prix
    if (!empty($selectedPriceRange)) {
        list($minPrice, $maxPrice) = explode('-', $selectedPriceRange);
        $whereClauses[] = "prix BETWEEN :minPrice AND :maxPrice";
        $params[":minPrice"] = $minPrice;
        $params[":maxPrice"] = $maxPrice;
    }

    // Filtre d'état
    if (!empty($selectedetat)) {
        $whereClauses[] = "etat = :etat";
        $params[":etat"] = $selectedetat;
    }

    // Combinaison des clauses WHERE
    if (!empty($whereClauses)) {
      $query .= " AND " . implode(" AND ", $whereClauses);
    }

    // Application du tri
    switch ($selectedorder) {
      case 'price_asc':
        $query .= " ORDER BY prix ASC";
        break;
      case 'price_desc':
        $query .= " ORDER BY prix DESC";
        break;
      case 'title_az':
        $query .= " ORDER BY titre ASC";
        break;
      case 'title_za':
        $query .= " ORDER BY titre DESC";
        break;
    }
  } else {
    // Tri aléatoire si aucun filtre n'est appliqué
    $query .= " ORDER BY RAND()";
  }

  $stmt = $pdoAnnonces->prepare($query);
  $stmt->execute($params);

?>

<html lang="fr">
  <head>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">
  
    <title>Consoles - Achetez ou vendez une console occasion en toute sérénité - ixewo</title>

    <meta name="description" content="Découvrez une sélection de consoles d'occasion. Trouvez la console de vos rêves et explorez une vaste gamme de jeux d'occasion, le tout à des prix compétitifs">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" type="text/css" href="3-styles.css">
    
  </head>

  <body>
    
    <?php include '1-header.php'; ?>

    <div id="errorMessage"></div>

    <section class="main">

      <section class="consoles-d-occasion">
        <div class="console-d-occasion">
          <h1 class="consoles-d-occasion-de-qualité">Consoles</h1>
        </div>
      </section>



      <div class="explorez-notre-sélection-et-trouvez-votre-console-de-rêve">
        <form class="filtres" action="consoles" method="get">
        
          <!-- Price Range Filter -->
          <select class="price_range" name="price_range">

              <option value="">Quel prix ? (€)</option>
              <option value="0-50" <?php echo ($selectedPriceRange == "0-50" ? "selected" : ""); ?>>0 - 50</option>
              <option value="50-100" <?php echo ($selectedPriceRange == "50-100" ? "selected" : ""); ?>>50 - 100</option>
              <option value="100-300" <?php echo ($selectedPriceRange == "100-300" ? "selected" : ""); ?>>100 - 300</option>
              <option value="300-600" <?php echo ($selectedPriceRange == "300-600" ? "selected" : ""); ?>>300 - 600</option>
              <option value="600-900" <?php echo ($selectedPriceRange == "600-900" ? "selected" : ""); ?>>600 - 900</option>
              <option value="900-100000" <?php echo ($selectedPriceRange == "900 et plus" ? "selected" : ""); ?>>900 et plus</option>

          </select>

          <!-- Etat Filter -->
          <select class="product_state" name="product_state">
          
            <option value="">Quel état ?</option>     
            <option value="Clinique" <?php echo ($selectedetat == "Clinique" ? "selected" : ""); ?>> Clinique </option>
            <option value="Relique" <?php echo ($selectedetat == "Relique" ? "selected" : ""); ?>> Relique </option>
            <option value="Peu-utilisé" <?php echo ($selectedetat == "Peu-utilisé" ? "selected" : ""); ?>> Peu utilisé </option>
            <option value="Utilisé" <?php echo ($selectedetat == "Utilisé" ? "selected" : ""); ?>> Utilisé </option>
            <option value="Saigné" <?php echo ($selectedetat == "Saigné" ? "selected" : ""); ?>> Saigné </option>

          </select>

          <!-- Sort Order Filter -->
          <select class="sort_order" name="sort_order">
            <option value="">Quel ordre ?</option>
            <option value="price_asc" <?php echo ($selectedorder == "price_asc" ? "selected" : ""); ?>> Prix croissant </option>
            <option value="price_desc" <?php echo ($selectedorder == "price_desc" ? "selected" : ""); ?>> Prix décroissant </option>
            <option value="title_az" <?php echo ($selectedorder == "title_az" ? "selected" : ""); ?>> Titre - Alphabétique A->Z </option>
            <option value="title_za" <?php echo ($selectedorder == "title_za" ? "selected" : ""); ?>> Titre - Alphabétique Z->A </option>
          </select>


          <!-- Hidden input to keep the search query -->
          <input type="hidden" name="query" value="<?php echo htmlspecialchars($_GET['query'] ?? ''); ?>">

          <!-- Submit Button -->
          <input class="button" type="submit" value="Appliquer les filtres">
        </form>
      </div>



      <div class="consoles-d-occasion-à-prix-compétitifs-plongez-dans-l-expérience-gaming">
          <?php
              // Chargement base de données
              include 'db_connect_annonces.php';

              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Formatage du prix pour chaque produit
                $prixFormatte = number_format($row['prix'], 2, '.', ' ');
                echo '<div class="product-card-consoles">';
                echo '<a href="produits-consoles?id=' .$row['id'].'"><img src="' . $row['image_path1'] . '"></a>';
                echo '<div class="price-favorite-consoles">';
                echo '<p>' . $prixFormatte . ' €</p>';
                // Check if the product is in the user's favorites list
                $favoritedClass = in_array($row['id'], $favorites) ? "favorited" : "";
                echo '<i class="favorite-icon ' . $favoritedClass . '" data-product-id="' . $row['id'] . '"></i>';
                echo '</div>';
                echo '</div>';
              }
          ?>

      </div>

      
      <div class="la-console-de-mes-rêves">
        <button id="prevPagec" class="consoles-prev-btn"><</button>
                  
        <div class="progress-container-consoles">
          <div class="progress-bar-consoles"></div>
        </div>

        <button id="nextPagec" class="consoles-next-btn">></button>
        
      </div>

      <section class="trouvez-votre-console-de-rêve">
        <div class="achat-revente-consoles-occasion">
          <h1 class="console-d-occasion-de-qualité">consoles</h1>
        </div>
      </section>

    </section>
    
    <?php include '11-footer.html'; ?>

    <script src="3-script.js"></script>

  </body>

</html>
