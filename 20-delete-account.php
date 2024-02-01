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
    }
    error_reporting(E_ALL);  // Display all errors

    $message1 = '';
    $message2 = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $stmt = $dbh->prepare("SELECT * FROM phpauth_users WHERE id = :uid");
        $stmt->bindParam(':uid', $uid);
        $stmt->execute(array(":uid" => $uid));
        $acheteur = $stmt->fetch(PDO::FETCH_ASSOC);

        $email = $acheteur['email'];
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Tenter de se connecter avec l'email et le mot de passe
        $loginResult = $auth->login($email, $password, $remember = false);


        if (!$loginResult['error']) {

            // démarrage actions 
            $pdoAnnonces->beginTransaction();
            $dbh->beginTransaction();

            //supprimer les produits de l'utilisateur

            // Récupérer les chemins des images des produits
            $stmt = $pdoAnnonces->prepare("SELECT image_path1, image_path2, image_path3, image_path4, image_path5 FROM annonces WHERE uid = :uid");
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();

            while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Supprimer les images du serveur pour chaque produit
                $imagePaths = ['image_path1', 'image_path2', 'image_path3', 'image_path4', 'image_path5'];
                foreach ($imagePaths as $path) {
                    if (!empty($product[$path])) {
                        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . basename($product[$path]);
                        if (file_exists($filePath)) {
                            unlink($filePath); // Supprime le fichier
                        }
                    }
                }
            }

            // Supprimer tous les produits de la base de données après la suppression des fichiers
            $stmt = $pdoAnnonces->prepare("DELETE FROM annonces WHERE uid = :uid");
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();

            // supprimer l'utilisateur de la base de données
            $stmt = $dbh->prepare("DELETE FROM phpauth_users WHERE id = :uid");
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();

            //Confirmation de fin d'action 
            $pdoAnnonces->commit();
            $dbh->commit();
            
            // Rediriger vers la page d'acceuil
            $message1 = "Votre compte a bien été supprimé...";
            header('Refresh: 2; URL=index.php');

        } else {
            $message2 = "Mot de passe incorrect";
        }
    }
    
?>


<html lang="fr">

    <head>

        <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

        <title>Supprimer le compte - ixewo</title>

        <meta name="description" content="Supprimer votre compte et une histoire de seconde...">

        <meta property="og:url" content="https://www.ixewo.com" />

        <meta property="og:type" content="website"/>

        <meta property="og:title" content="ixewo"/>

        <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

        <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

        <meta charset="UTF-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="20-styles.css">
        
    </head>

    <body>

        <?php include '1-header.php'; ?>
        
        <section class="main">

            <section class="container-consoles">
                <div class="titre-consoles">
                    <h1>Supprimer le compte</h1>
                </div>
            </section>

            <section class="register-informations">

                <?php if ($message1): ?>
                    <p class="message-b"><?php echo $message1; ?></p>
                <?php endif; ?>    

                <?php if ($message2): ?>
                    <p class="message-r"><?php echo $message2; ?></p>
                <?php endif; ?>
            
                <form class="register-section" method="POST">
                    <input class="input" type="password" name="password" placeholder="Mot de passe" required>
            
                    <input class="login" type="submit" value="Supprimer le compte">
                </form>

            </section>
    
            <section class="container-consoles1">
                <div class="titre-consoles1">
                    <h1>supprimer le compte</h1>
                </div>
            </section>

        </section>

        <?php include '11-footer.html'; ?>

    </body>
    
</html>
