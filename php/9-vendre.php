<?php
    session_start();

    // Importer le fichier de connexion à la base de données
    require 'db_connect_users.php';

    // Importer le fichier de connexion à la base de données
    include 'db_connect_annonces.php';

    $message ="";
    $message1 ="";
    $message2 =""; //violet
    $message3 ="";
    $message4 ="";
    $message5 ="";
    $message6 ="";
    $message7 ="";

    $hasErrors = false;  // Initialisé à false

    // Vérifiez si l'utilisateur est connecté
    if (isset($_COOKIE['phpauth_session_cookie'])) {
        $hash = $_COOKIE['phpauth_session_cookie'];
        $uid = $auth->getSessionUID($hash);
    } else {
        // Gérer l'absence du cookie, par exemple rediriger vers la page de connexion ou afficher un message d'erreur.
        header('Location: connection-vendre');
    }
    error_reporting(E_ALL);  // Display all errors

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $recaptcha_response = $_POST['g-recaptcha-response'];
      $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=***********&response=$recaptcha_response");
      $response_keys = json_decode($response, true);

      // Vérification de l'image principale
      if (!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] != 0) {
          $hasErrors = true;
          $message1 = "Vous devez avoir au moins une image principale";
      }

      
      if($response_keys["success"]) {

          // Le reCAPTCHA a été rempli avec succès, procédez à la connexion

          // Récupérer les données du formulaire
          $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_SPECIAL_CHARS);
          $prix = filter_input(INPUT_POST, 'prix', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
          $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
          $categorie = $_POST['categorie'];
          $etat = $_POST["etat"];  
          $uid = $auth->getSessionUID($hash);  
          
          if (!is_numeric($prix)) {
              $message1 = "Le prix doit être un chiffre.";
          } else {

              // Check if "fileToUpload" input is set and not empty
              $images = ['fileToUpload', 'fileToUpload2', 'fileToUpload3', 'fileToUpload4', 'fileToUpload5'];
              $image_paths = [];

              foreach ($images as $image) {
                  if (isset($_FILES[$image]) && $_FILES[$image]['error'] == 0) {
                      $uploadOk = 1;
                      $target_file = "uploads/" . basename($_FILES[$image]["name"]);
                      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                      // Vérifiez si le fichier est une image
                      $check = getimagesize($_FILES[$image]["tmp_name"]);
                      if ($check !== false) {
                          $uploadOk = 1;
                      } else {
                          $message3 = "Le fichier n'est pas une image.";
                          $uploadOk = 0;
                          $hasErrors = true;  // Ajouté
                      }

                      // Vérifiez la taille du fichier
                      if ($_FILES[$image]["size"] > 500000000) {
                          $message4 = "Désolé, votre fichier est trop volumineux.";
                          $uploadOk = 0;
                          $hasErrors = true;  // Ajouté
                      }

                      // Autoriser certains formats de fichiers
                      if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                          $message5 = "Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.";
                          $uploadOk = 0;
                          $hasErrors = true;  // Ajouté
                      }

                      // Vérifiez si $uploadOk est défini sur 0 pour une erreur
                      if ($uploadOk == 0) {
                          $message6 = "Désolé, votre fichier n'a pas été téléchargé.";
                          $hasErrors = true;  // Ajouté
                      } else {
                          if (move_uploaded_file($_FILES[$image]["tmp_name"], $target_file)) {
                              $image_paths[] = $target_file; // Ajoutez le chemin du fichier au tableau
                          } else {
                              $message7 = "Désolé, il y a eu une erreur lors du téléchargement de votre fichier.";
                              $hasErrors = true;  // Ajouté
                          }
                      }
                  }
              }
              // $uploaded_files contient maintenant les chemins de tous les fichiers téléchargés avec succès.
                  

              // Try to upload file
              if (!$hasErrors) {
                  // la logique d'insertion dans la base de données ici
                  $stmt = $pdoAnnonces->prepare("INSERT INTO annonces (titre, prix, description, categorie, etat, image_path1, image_path2, image_path3, image_path4, image_path5, uid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                  $stmt->bindParam(1, $titre);
                  $stmt->bindParam(2, $prix);
                  $stmt->bindParam(3, $description);
                  $stmt->bindParam(4, $categorie);
                  $stmt->bindParam(5, $etat);
                  $stmt->bindParam(6, $image_paths[0]);
                  $stmt->bindParam(7, $image_paths[1]);
                  $stmt->bindParam(8, $image_paths[2]);
                  $stmt->bindParam(9, $image_paths[3]);
                  $stmt->bindParam(10, $image_paths[4]);
                  $stmt->bindParam(11, $uid);
                  
                  $stmt->execute();  
                  // Redirection après l'insertion réussie
                  $_SESSION['message2'] = "Tout est bon, merci à vous !!!";
                  header('Location: vendre');
                  exit;                                
              }
          }
      } else {
      $message = "REMPLISSEZ le reCAPTCHA";// Le reCAPTCHA n'a pas été rempli, affichez un message d'erreur
      }
    }

?>

<html lang="fr">

  <head>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

    <title>Vendre -ixewo</title>

    <meta name="description" content="Pour vendre une console, un ordinateur, un jeux-vidéo et leurs produits dérivés c'est facile et c'est chez ixewo !">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

    <meta charset="UTF-8">
        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="../css/9-styles.css">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        
  </head>

  <body>

    <?php include '1-header.php'; ?>

    <section class="main">

      <section class="container-consoles">
        <div class="titre-consoles">
          <h1>Vendre</h1>
        </div>
      </section>

      <!-- Affichage du message d'information ou d'erreur -->
      <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
      <?php endif; ?>

      <?php if ($message1): ?>
        <p class="message"><?php echo $message1; ?></p>
      <?php endif; ?>

      <?php if ($message3): ?>
        <p> class="message"<?php echo $message3; ?></p>
      <?php endif; ?>

      <?php if ($message4): ?>
        <p class="message"><?php echo $message4; ?></p>
      <?php endif; ?>

      <?php if ($message5): ?>
        <p class="message"><?php echo $message5; ?></p>
      <?php endif; ?>

      <?php if ($message6): ?>
        <p class="message"><?php echo $message6; ?></p>
      <?php endif; ?>

      <?php if ($message7): ?>
        <p class="message"><?php echo $message7; ?></p>
      <?php endif; ?>

      <?php
        if (isset($_SESSION['message2'])) {
          echo "<p class='success-message'>" . $_SESSION['message2'] . "</p>";
          unset($_SESSION['message2']);
        }
      ?>

            
      <form class="login-section" action="vendre" method="post" enctype="multipart/form-data">
        <label for="titre">
          <h2>
            Titre :
          </h2>
        </label>
        <input type="text" name="titre" id="titre" required>

        <label for="prix">
          <h2>
            Prix :
          </h2>
          <h2>
            (utilisez seulement des "chiffres" et la séparation ".")
          </h2>
        </label>
        <input type="text" name="prix" id="prix" required>

        <label for="description">
          <h2>
            Description :
          </h2>
          <h2>
            (au moins 30 caractères)
          </h2>
        </label>
        <textarea name="description" id="description" required></textarea>

        <label for="categorie">
          <h2>
            Catégorie :
          </h2>
        </label>
        <select name="categorie" id="categorie" required>
          <option value="Consoles">Consoles</option>
          <option value="Ordinateurs">Ordinateurs</option>
          <option value="Jeux-Vidéos">Jeux-Vidéos</option>
          <option value="Produits-Dérivés">Produits Dérivés</option>
          <option value="Autres">Autres</option>
        </select>

        <label for="etat">
          <h2>
            État :
          </h2>
          <h4>
            <span class="h6">"Clinique"</span> - La collection c'est ma passion
          </h4>
          <h5>
            (sous blister + facture + certificat d'authentification)
          </h5>
          <h4>
            <span class="h6">"Relique"</span> - Acheté mais jamais utilisé
          </h4>
          <h5>
            (sous blister avec ou sans facture)
          </h5>
          <h4>
            <span class="h6">"Peu utilisé"</span> - Utilisé mais maniaque de l'entretien
          </h4>
          <h5>
            (peu, voir pas d'usure)
          </h5>
          <h4>
            <span class="h6">"Utilisé"</span> - Un produit il faut l'utiliser
          </h4>
          <h5>
            (usures visibles qui n'empêchent pas l'utilisation)
          </h5>
          <h4>
            <span class="h6">"Saigné"</span>- Un produit il faut le rentabiliser
          </h4>
          <h5>
            (usures visibles et invisibles qui empêchent l'utilisation)
          </h5>
        </label>

        <select name="etat" id="etat" required>
          <option value="Clinique"><h1>"Clinique"</h1></option>
          <option value="Relique"><h1>"Relique"</h1></option>
          <option value="Peu-utilisé"><h1>"Peu utilisé"</h1></option>
          <option value="Utilisé"><h1>"Utilisé"</h1></option>
          <option value="Saigné"><h1>"Saigné"</h1></option>
        </select>

                  
        <label for="fileToUpload1">
          <h2>
            Image principale :
          </h2>
          <h2>
            (fichiers autorisés ".jpg", ".png", ".jpeg" , ".gif")
          </h2>
        </label>

        <!-- Existing structure for image upload -->
        <div class="image-container1">
            <img id="image-principale" style="display: none;">
            <!-- Ensure this button is within the image-container1 div -->
            <button onclick="removeImage(event, 'image-principale', 'nouveau-texte1', 'fileToUpload', 'delete-button1')" class="delete-button" id="delete-button1" style="display: none;">
                <img src="/images/delete.png" alt="Supprimer" />
            </button>
        </div>
        <h6 id="nouveau-texte1" style="display: none;">1/5 - Nouvelle image</h6>
        <input type="file" name="fileToUpload" id="fileToUpload">



        <label for="fileToUpload0">
          <h2>
            Images secondaires :
          </h2>
          <h2>
            (fichiers autorisés ".jpg", ".png", ".jpeg" , ".gif")
          </h2>
        </label>

        <div class="image-container2">
            <img id="image-secondaires2" style="display: none;">
            <!-- Ensure this button is within the image-container1 div -->
            <button onclick="removeImage(event, 'image-secondaires2', 'nouveau-texte2', 'fileToUpload2', 'delete-button2')" class="delete-button" id="delete-button2" style="display: none;">
                <img src="/images/delete.png" alt="Supprimer" />
            </button>
        </div>
        <h6 id="nouveau-texte2" style="display: none;">2/5 - Nouvelle image</h6>
        <input type="file" name="fileToUpload2" id="fileToUpload2">
        

        <div class="image-container3">
            <img id="image-secondaires3" style="display: none;">
            <!-- Ensure this button is within the image-container1 div -->
            <button onclick="removeImage(event, 'image-secondaires3', 'nouveau-texte3', 'fileToUpload3', 'delete-button3')" class="delete-button" id="delete-button3" style="display: none;">
                <img src="/images/delete.png" alt="Supprimer" />
            </button>
        </div>
        <h6 id="nouveau-texte3" style="display: none;">3/5 - Nouvelle image</h6>
        <input type="file" name="fileToUpload3" id="fileToUpload3">

        <div class="image-container4">
            <img id="image-secondaires4" style="display: none;">
            <!-- Ensure this button is within the image-container1 div -->
            <button onclick="removeImage(event, 'image-secondaires4', 'nouveau-texte4', 'fileToUpload4', 'delete-button4')" class="delete-button" id="delete-button4" style="display: none;">
                <img src="/images/delete.png" alt="Supprimer" />
            </button>
        </div>
        <h6 id="nouveau-texte4" style="display: none;">4/5 - Nouvelle image</h6>
        <input type="file" name="fileToUpload4" id="fileToUpload4">

        <div class="image-container5">
            <img id="image-secondaires5" style="display: none;">
            <!-- Ensure this button is within the image-container1 div -->
            <button onclick="removeImage(event, 'image-secondaires5', 'nouveau-texte5', 'fileToUpload5', 'delete-button5')" class="delete-button" id="delete-button5" style="display: none;">
                <img src="/images/delete.png" alt="Supprimer" />
            </button>
        </div>
        <h6 id="nouveau-texte5" style="display: none;">5/5 - Nouvelle image</h6>
        <input type="file" name="fileToUpload5" id="fileToUpload5">

        <div class="g-recaptcha" data-sitekey="6LdR_xkpAAAAALq2lge1goZ1fZ7NE5waGSLzst9Q"></div>

        <input class="login" type="submit" value="Publier l'annonce" name="submit">
      </form>

      <section class="container-consoles1">
        <div class="titre-consoles1">
          <h1>vendre</h1>
        </div>
      </section>

    </section>

    <?php include '../11-footer.html'; ?>

    <script src="../js/9-script.js"></script>

  </body>
    
</html>