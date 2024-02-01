<?php
    // Importer le fichier de connexion à la base de données
    require 'db_connect_users.php';

    // Importer le fichier de connexion à la base de données
    include 'db_connect_annonces.php';

    $message1 ="";
    $message2 ="";
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
        header('Location: connection-profil');
        exit();
    }
    error_reporting(E_ALL);  // Display all errors


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

    // Si le formulaire est soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $titre = $_POST['titre'];
        $prix = $_POST['prix'];
        $description = $_POST['description'];
        $categorie = $_POST['categorie'];
        $etat = $_POST['etat'];
        
        // Vérifie si une image principale existe déjà
        $isImagePresent = false;
        if (!empty($product['image_path1']) && file_exists($product['image_path1'])) {
            $isImagePresent = true;
        }

        // Vérifie si une nouvelle image a été téléchargée
        if (isset($_FILES['image_path1']) && $_FILES['image_path1']['error'] == 0 && $_FILES['image_path1']['size'] > 0) {
            $isImagePresent = true;
        }

        // Vérifie si au moins une image principale est présente
        if (!$isImagePresent) {
            $message6 = "Vous devez avoir au moins une image principale.";
            $hasErrors = true;
        }



        // Vérification que le prix est un chiffre
        if (!is_numeric($prix)) {
           $message1 = "Le prix doit être un chiffre.";
        } else {

            // Définir un tableau pour stocker les chemins des images
            // Check if "fileToUpload" input is set and not empty
            $images = ['image_path1', 'image_path2', 'image_path3', 'image_path4', 'image_path5'];

            $image_paths = [
                'image_path1' => $_POST['image_path1'] ?? null,
                'image_path2' => $_POST['image_path2'] ?? null,
                'image_path3' => $_POST['image_path3'] ?? null,
                'image_path4' => $_POST['image_path4'] ?? null,
                'image_path5' => $_POST['image_path5'] ?? null,
            ];

            // Boucle à travers chaque champ d'image
            foreach ($images as $image) {
                // Vérifier si l'image doit être supprimée
                if (isset($_POST['delete_' . $image]) && $_POST['delete_' . $image] === 'true') {
                    // Supprime le chemin de l'image et le fichier lui-même si nécessaire
                    $image_paths[$image] = null;
                    if (!empty($product[$image]) && file_exists($product[$image])) {
                        unlink($product[$image]); // Supprime le fichier de l'image
                    }
                    
                } elseif (isset($_FILES[$image]) && is_uploaded_file($_FILES[$image]['tmp_name'])) {

                    $target_file = "uploads/" . $_FILES[$image]["name"];
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

                    // Si tout est OK, déplacer le fichier téléchargé vers le répertoire cible
                    if (move_uploaded_file($_FILES[$image]["tmp_name"], $target_file)) {
                        // Stocker le chemin du fichier dans le tableau $image_paths
                        $image_paths[$image] = $target_file;
                    }

                } else {
                    // Si aucune image n'a été téléchargée, conservez l'ancien chemin
                    $image_paths[$image] = isset($product[$image]) ? $product[$image] : null;
                }

            }

            // Mise à jour de la base de données
            $stmt = $pdoAnnonces->prepare("UPDATE annonces SET titre = ?, prix = ?, description = ?, categorie = ?, etat = ?, image_path1 = ?, image_path2 = ?, image_path3 = ?, image_path4 = ?, image_path5 = ? WHERE id = ?");
            $stmt->execute([$titre, $prix, $description, $categorie, $etat, $image_paths['image_path1'], $image_paths['image_path2'], $image_paths['image_path3'], $image_paths['image_path4'], $image_paths['image_path5'], $product_id]);

            if (!$hasErrors) {
                // Redirigez l'utilisateur vers une page de confirmation ou vers leur profil
                header("Location: produits");
                exit;
            }

        }

    }
?>

<html lang="fr">

  <head>

    <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

    <title>Modifier produits - ixewo</title>

    <meta name="description" content="Une modification à apporter sur un produit, tout se passe ici !!!">

    <meta property="og:url" content="https://www.ixewo.com" />

    <meta property="og:type" content="website"/>

    <meta property="og:title" content="ixewo"/>

    <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

    <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

    <meta charset="UTF-8">
        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="24-styles.css">
    
  </head>

  <body>

    <?php include '1-header.php'; ?>

    <section class="main">

      <section class="container-consoles">
        <div class="titre-consoles">
          <h1>Modifier</h1>
        </div>
      </section>

      <!-- Affichage du message d'information ou d'erreur -->
      <?php if ($message1): ?>
        <p class="message"><?php echo $message1; ?></p>
      <?php endif; ?>

      <?php if ($message3): ?>
        <p class="message"><?php echo $message3; ?></p>
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

            
      <form class="login-section" action="mise-a-jour-produits?id=<?php echo $product['id']; ?>" method="post" enctype="multipart/form-data">

        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

        <label for="titre">
          <h2>
            Titre :
          </h2>
        </label>
        <input id="titre" type="text" name="titre" value="<?php echo $product['titre']; ?>">
                  
                
        <label for="prix">
          <h2>
            Prix :
          </h2>
          <h2>
            (utilisez seulement des "chiffres" et la séparation ".")
          </h2>
        </label>
        <input id="prix" type="text" name="prix" value="<?php echo $product['prix']; ?>">
                
                
        <label for="description">
          <h2>
            Description :
          </h2>
          <h2>
            (au moins 30 caractères)
          </h2>
        </label>
        <textarea id="description" type="text" name="description"><?php echo $product['description']; ?></textarea>


        <label for="categorie">
          <h2>
            Catégorie :
          </h2>
        </label>
        <select name="categorie" id="categorie" required>
          <option value="Consoles" <?php if($product['categorie'] == 'Consoles') echo 'selected'; ?>>Consoles</option>
          <option value="Ordinateurs" <?php if($product['categorie'] == 'Ordinateurs') echo 'selected'; ?>>Ordinateurs</option>
          <option value="Jeux-Vidéos" <?php if($product['categorie'] == 'Jeux-Vidéos') echo 'selected'; ?>>Jeux-Videos</option>
          <option value="Produits-Dérivés" <?php if($product['categorie'] == 'Produits-Dérivés') echo 'selected'; ?>>Produits Dérivés</option>
          <option value="Autres" <?php if($product['categorie'] == 'Autres') echo 'selected'; ?>>Autres</option>
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
          <option value="Clinique" <?php if($product['etat'] == 'Clinique') echo 'selected'; ?>><h1>"Clinique"</h1></option>
          <option value="Relique" <?php if($product['etat'] == 'Relique') echo 'selected'; ?>><h1>"Relique"</h1></option>
          <option value="Peu-utilisé" <?php if($product['etat'] == 'Peu-utilisé') echo 'selected'; ?>><h1>"Peu utilisé"</h1></option>
          <option value="Utilisé" <?php if($product['etat'] == 'Utilisé') echo 'selected'; ?>><h1>"Utilisé"</h1></option>
          <option value="Saigné" <?php if($product['etat'] == 'Saigné') echo 'selected'; ?>><h1>"Saigné"</h1></option>
        </select>
        
        <label for="fileToUpload1">
          <h2>
            Image principale :
          </h2>
          <h2>
            (fichiers autorisés ".jpg", ".png", ".jpeg" , ".gif")
          </h2>
        </label>


        <div class="image-container1">
            <img id="image-principale" src="<?php echo htmlspecialchars($product['image_path1']); ?>" alt="Image du produit" style="<?php echo !empty($product['image_path1']) && file_exists($product['image_path1']) ? 'display: block;' : 'display: none;'; ?>">
            <img id="image-defaut1" style="display: none;">
            <?php if (!empty($product['image_path1']) && file_exists($product['image_path1'])): ?>
                <button onclick="removeImage(event, 'image-principale', 'image-defaut1', 'nouveau-texte1', 'ancien-texte1', 'delete-button1', 'delete-flag1', 'image_path1')" class="delete-button" id="delete-button1">
                    <img src="/images/delete.png" alt="Supprimer" />
                </button>
            <?php endif; ?>
        </div>
        <h6 id="ancien-texte1" style="<?php echo !empty($product['image_path1']) && file_exists($product['image_path1']) ? 'display: block;' : 'display: none;'; ?>">1/5 - Image actuelle</h6>
        <h6 id="nouveau-texte1" style="display: none;">1/5 - Nouvelle image</h6>
        <input type="file" name="image_path1" id="image_path1" onchange="createFileUploadHandler('image-principale', 'image-defaut1', 'nouveau-texte1', 'ancien-texte1', 'delete-button1', 'delete-flag1', 'image_path1')(event)" style="<?php echo empty($product['image_path1']) || !file_exists($product['image_path1']) ? 'display: block;' : 'display: none;'; ?>">
        <input type="hidden" name="delete_image_path1" id="delete-flag1" value="false">


                            

        <label for="fileToUpload0">
          <h2>
            Images secondaires :
          </h2>
          <h2>
            (fichiers autorisés ".jpg", ".png", ".jpeg" , ".gif")
          </h2>
        </label>

        <div class="image-container2">
            <img id="image-secondaires2" src="<?php echo htmlspecialchars($product['image_path2']); ?>" alt="Image du produit" style="<?php echo !empty($product['image_path2']) && file_exists($product['image_path2']) ? 'display: block;' : 'display: none;'; ?>">
            <img id="image-defaut2" style="display: none;">
            <?php if (!empty($product['image_path2']) && file_exists($product['image_path2'])): ?>
                <button onclick="removeImage(event, 'image-secondaires2', 'image-defaut2', 'nouveau-texte2', 'ancien-texte2', 'delete-button2', 'delete-flag2', 'image_path2')" class="delete-button" id="delete-button2">
                    <img src="/images/delete.png" alt="Supprimer" />
                </button>
            <?php endif; ?>
        </div>
        <h6 id="ancien-texte2" style="<?php echo !empty($product['image_path2']) && file_exists($product['image_path2']) ? 'display: block;' : 'display: none;'; ?>">2/5 - Image actuelle</h6>
        <h6 id="nouveau-texte2" style="display: none;">2/5 - Nouvelle image</h6>
        
        <input type="file" name="image_path2" id="image_path2" onchange="createFileUploadHandler('image-secondaires2', 'image-defaut2', 'nouveau-texte2', 'ancien-texte2', 'delete-button2', 'delete-flag2', 'image_path2')(event)" style="<?php echo empty($product['image_path2']) || !file_exists($product['image_path2']) ? 'display: block;' : 'display: none;'; ?>">
        <input type="hidden" name="delete_image_path2" id="delete-flag2" value="false">




        <div class="image-container3">
            <img id="image-secondaires3" src="<?php echo htmlspecialchars($product['image_path3']); ?>" alt="Image du produit" style="<?php echo !empty($product['image_path3']) && file_exists($product['image_path3']) ? 'display: block;' : 'display: none;'; ?>">
            <img id="image-defaut3" style="display: none;">
            <?php if (!empty($product['image_path3']) && file_exists($product['image_path3'])): ?>
                
                <button onclick="removeImage(event, 'image-secondaires3', 'image-defaut3', 'nouveau-texte3', 'ancien-texte3', 'delete-button3', 'delete-flag3', 'image_path3')" class="delete-button" id="delete-button3">
                    <img src="/images/delete.png" alt="Supprimer" />
                </button>
            <?php endif; ?>
        </div>
        <h6 id="ancien-texte3" style="<?php echo !empty($product['image_path3']) && file_exists($product['image_path3']) ? 'display: block;' : 'display: none;'; ?>">3/5 - Image actuelle</h6>
        <h6 id="nouveau-texte3" style="display: none;">3/5 - Nouvelle image</h6>

        <input type="file" name="image_path3" id="image_path3" onchange="createFileUploadHandler('image-secondaires3', 'image-defaut3', 'nouveau-texte3', 'ancien-texte3', 'delete-button3', 'delete-flag3', 'image_path3')(event)" style="<?php echo empty($product['image_path3']) || !file_exists($product['image_path3']) ? 'display: block;' : 'display: none;'; ?>">
        <input type="hidden" name="delete_image_path3" id="delete-flag3" value="false">


        <div class="image-container4">
            <img id="image-secondaires4" src="<?php echo htmlspecialchars($product['image_path4']); ?>" alt="Image du produit" style="<?php echo !empty($product['image_path4']) && file_exists($product['image_path4']) ? 'display: block;' : 'display: none;'; ?>">
            <img id="image-defaut4" style="display: none;">
            <?php if (!empty($product['image_path4']) && file_exists($product['image_path4'])): ?>
                <button onclick="removeImage(event, 'image-secondaires4', 'image-defaut4', 'nouveau-texte4', 'ancien-texte4', 'delete-button4', 'delete-flag4', 'image_path4')" class="delete-button" id="delete-button4">
                    <img src="/images/delete.png" alt="Supprimer" />
                </button>
            <?php endif; ?>
        </div>
        <h6 id="ancien-texte4" style="<?php echo !empty($product['image_path4']) && file_exists($product['image_path4']) ? 'display: block;' : 'display: none;'; ?>">4/5 - Image actuelle</h6>
        <h6 id="nouveau-texte4" style="display: none;">4/5 - Nouvelle image</h6>
        <input type="file" name="image_path4" id="image_path4" onchange="createFileUploadHandler('image-secondaires4', 'image-defaut4', 'nouveau-texte4', 'ancien-texte4', 'delete-button4', 'delete-flag4', 'image_path4')(event)" style="<?php echo empty($product['image_path4']) || !file_exists($product['image_path4']) ? 'display: block;' : 'display: none;'; ?>">        
        <input type="hidden" name="delete_image_path4" id="delete-flag4" value="false">


        <div class="image-container5">
            <img id="image-secondaires5" src="<?php echo htmlspecialchars($product['image_path5']); ?>" alt="Image du produit" style="<?php echo !empty($product['image_path5']) && file_exists($product['image_path5']) ? 'display: block;' : 'display: none;'; ?>">
            <img id="image-defaut5" style="display: none;">
            <?php if (!empty($product['image_path5']) && file_exists($product['image_path5'])): ?>
                <button onclick="removeImage(event, 'image-secondaires5', 'image-defaut5', 'nouveau-texte5', 'ancien-texte5', 'delete-button5', 'delete-flag5', 'image_path5')" class="delete-button" id="delete-button5">
                    <img src="/images/delete.png" alt="Supprimer" />
                </button>
            <?php endif; ?>
        </div>
        <h6 id="ancien-texte5" style="<?php echo !empty($product['image_path5']) && file_exists($product['image_path5']) ? 'display: block;' : 'display: none;'; ?>">5/5 - Image actuelle</h6>
        <h6 id="nouveau-texte5" style="display: none;">5/5 - Nouvelle image</h6>
        <input type="file" name="image_path5" id="image_path5" onchange="createFileUploadHandler('image-secondaires5', 'image-defaut5', 'nouveau-texte5', 'ancien-texte5', 'delete-button5', 'delete-flag5', 'image_path5')(event)" style="<?php echo empty($product['image_path5']) || !file_exists($product['image_path5']) ? 'display: block;' : 'display: none;'; ?>">        


        <input type="hidden" name="delete_image_path5" id="delete-flag5" value="false">

        <input class="login" type="submit" value="Mettre à jour">
                             
      </form>

      <section class="container-consoles1">
        <div class="titre-consoles1">
          <h1>modifier</h1>
        </div>
      </section>
            

    </section>

    <?php include '11-footer.html'; ?>

    <script src="24-script.js"></script>

  </body>
    
</html>