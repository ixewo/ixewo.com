<?php

  // Importer le fichier de connexion à la base de données
  require 'db_connect_users.php';

  // Importer le fichier de connexion à la base de données
  include 'db_connect_annonces.php';

  //Affichage erreurs 
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
  $query = "SELECT * FROM annonces";

  // Nettoyage et validation des entrées GET
  $searchQuery = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_SPECIAL_CHARS);
  $priceRange = filter_input(INPUT_GET, 'price_range', FILTER_SANITIZE_SPECIAL_CHARS);
  $productState = filter_input(INPUT_GET, 'product_state', FILTER_SANITIZE_SPECIAL_CHARS);
  $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
  $sortOrder = filter_input(INPUT_GET, 'sort_order', FILTER_SANITIZE_SPECIAL_CHARS);

  // Initialisation du tableau des clauses WHERE et des paramètres
  $whereClauses = [];
  $params = [];

  // Condition de base pour le titre et la description
  $whereClauses[] = "(titre LIKE CONCAT('%', :search, '%') OR description LIKE CONCAT('%', :search, '%'))";
  $params[":search"] = $searchQuery;

  //initialisation prix de recherches
  $selectedPriceRange = $_GET['price_range'] ?? "";

  // Vérification du filtre de prix
  if (isset($_GET['price_range']) && $_GET['price_range']) {
      list($minPrice, $maxPrice) = explode('-', $_GET['price_range']);
      $whereClauses[] = "prix BETWEEN :minPrice AND :maxPrice";
      $params[":minPrice"] = $minPrice;
      $params[":maxPrice"] = $maxPrice;
  }

  //initialisation etat de recherches
  $selectedetat = $_GET['product_state'] ?? "";

  // Vérification du filtre d'état
  if (isset($_GET['product_state']) && $_GET['product_state']) {
      $whereClauses[] = "etat = :etat";
      $params[":etat"] = $_GET['product_state'];
  }


  //initialisation etat de recherches
  $selectedcat = $_GET['category'] ?? "";

  // Récupération filtre de catégories :
  if (isset($_GET['category']) && $_GET['category']) {
    $whereClauses[] = "categorie = :categorie";
    $params[":categorie"] = $_GET['category'];
  }

  // Combinaison des clauses WHERE
  if (!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
  }

  //initialisation etat de recherches
  $selectedorder = $_GET['sortOrder'] ?? "";

  // Ajout de la clause ORDER BY en fonction de sort_order
  if (!empty($sortOrder)) {
    switch ($sortOrder) {
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
  }

  $stmt = $pdoAnnonces->prepare($query);
  $stmt->execute($params);

?>

<html lang="fr">

  <head>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

    <title>Rechercher - ixewo</title>

    <meta name="description" content="Trouver votre console, ordinateur, jeux-vidéo et produits dérivés rapidement grâce à nos recherches personalisées">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

    <meta charset="UTF-8">
        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="12-styles.css">
    
  </head>

  <body>

    <?php include '1-header.php'; ?>

    <div id="errorMessage"></div>

    <section class="main">

      <section class="container-ordinateurs">
        <div class="titre-ordinateurs">
          <h1>Résultats pour "<?php echo htmlspecialchars($_GET['query']); ?>" </h1>
        </div>
      </section>


      <div class="filter-section">
        <form class="filtres" action="rechercher" method="get">
        
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
            <option value="clinique" <?php echo ($selectedetat == "clinique" ? "selected" : ""); ?>> Clinique </option>
            <option value="relique" <?php echo ($selectedetat == "relique" ? "selected" : ""); ?>> Relique </option>
            <option value="peu-utilise" <?php echo ($selectedetat == "peu-utilise" ? "selected" : ""); ?>> Peu utilisé </option>
            <option value="utilise" <?php echo ($selectedetat == "utilise" ? "selected" : ""); ?>> Utilisé </option>
            <option value="saigne" <?php echo ($selectedetat == "saigne" ? "selected" : ""); ?>> Saigné </option>

          </select>

          <!-- Sort Order Filter -->
          <select class="sort_order" name="sort_order">
            <option value="">Quel ordre ?</option>
            <option value="price_asc" <?php echo ($sortOrder == "price_asc" ? "selected" : ""); ?>> Prix croissant </option>
            <option value="price_desc" <?php echo ($sortOrder == "price_desc" ? "selected" : ""); ?>> Prix décroissant </option>
            <option value="title_az" <?php echo ($sortOrder == "title_az" ? "selected" : ""); ?>> Titre - Alphabétique A->Z </option>
            <option value="title_za" <?php echo ($sortOrder == "title_za" ? "selected" : ""); ?>> Titre - Alphabétique Z->A </option>
          </select>



          <!-- Hidden input to keep the search query -->
          <input type="hidden" name="query" value="<?php echo htmlspecialchars($_GET['query'] ?? ''); ?>">

          <!-- Submit Button -->
          <input class="button" type="submit" value="Appliquer les filtres">
        </form>
      </div>



      <div class="produits-ordinateurs">

        <?php
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

      </div>

      <div class="controls-container">
        <button id="prevPageo" class="ordinateurs-prev-btn"><</button>
                  
        <div class="progress-container-ordinateurs">
          <div class="progress-bar-ordinateurs"></div>
        </div>

        <button id="nextPageo" class="ordinateurs-next-btn">></button>
        
      </div>

      <section class="container-ordinateurs1">
        <div class="titre-ordinateurs1">
          <h1>votre recherche</h1>
        </div>
      </section>

    </section>

    <?php include '11-footer.html'; ?>

    <script src="12-script.js"></script>

  </body>

</html>