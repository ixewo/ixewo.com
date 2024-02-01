<html lang="fr">

    <head>
      
        <title>Header</title>

        <meta charset="UTF-8"/>

        <meta name="viewport" content="width=device-width, initial-scale=1.0,viewport-fit=cover">

        <link rel="stylesheet" type="text/css" href="1-styles.css">

        

    </head>


    <header> 

      <div class="menu-hamburger-header">
          <img src="/images/logo-menu-hamburger.png" class="logo-menu-hamburger-header"/>
          <img src="/images/cross.png" class="logo-menu-close-header hidden"/>
      </div>

      <div class="logo-container-header">
          <a href="index.php">
              <img src="/images/logo-ixewo.png" class="header logo-ixewo-header"/>
          </a>
      </div>

      <nav class="main-nav-header">
          <?php
            if (isset($_COOKIE['phpauth_session_cookie'])) {
                echo '<a href="profil" class="profile-button">Profil</a>';
            } else {
                echo '<a href="profil" class="profile-button">Connectez-vous</a>';
            }
          ?>
          <a href="consoles">Consoles</a>
          <a href="ordinateurs">Ordinateurs</a>
          <a href="jeux-videos">Jeux-Vidéos</a>
          <a href="produits-derives">Produits Dérivés</a>
          <a href="le-projet">Le Projet</a>
          <a href="contact">Contact</a>
          <a href="vendre" class="vendre-button">Vendre</a>
      </nav>

      <form action="rechercher" method="get" class="search-bar-header">
          <input type="text" name="query" placeholder="Que cherchez vous ?" />
      </form>

      <div class="search-container-header"> 
          <img id="logo-search-header" src="/images/search.png" alt="Recherche" class="logo-search-header"></img>
      </div>

      <script src="1-script.js"></script>

    </header>

</html>