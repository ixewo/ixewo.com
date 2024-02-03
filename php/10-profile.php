<?php
    session_start();

    // Importer le fichier de connexion à la base de données
    require 'db_connect_users.php';

    // Vérifiez si l'utilisateur est connecté
    if (isset($_COOKIE['phpauth_session_cookie'])) {
        $hash = $_COOKIE['phpauth_session_cookie'];
        $uid = $auth->getSessionUID($hash);
    } else {
        // Gérer l'absence du cookie, par exemple rediriger vers la page de connexion ou afficher un message d'erreur.
        header('Location: connection-profil');
    }
    error_reporting(E_ALL);  // Display all errors

?>

<html lang="fr">

    <head>

        <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

        <title>Profil - ixewo</title>

        <meta name="description" content="Pour rejoindre 'ixewo' et commencer une nouvelle mission, c'est ici !!!">

        <meta property="og:url" content="https://www.ixewo.com" />

        <meta property="og:type" content="website"/>

        <meta property="og:title" content="ixewo"/>

        <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

        <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

        <meta charset="UTF-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="../css/10-styles.css">
        
    </head>

    <body>

        <?php include '1-header.php'; ?>

        <section class="main">

            <section class="container-consoles">
                <div class="titre-consoles">
                    <h1>Profil</h1>
                </div>
            </section>

            <section class="profile-informations">

                <a class="but1" href="produits">Mes produits</a>
                <a class="but5" href="porte-monnaie">Mon porte-monnaie</a>

                <a class="but3" href="changement-mail">Changer - Email</a>
                <a class="but4" href="changement-mot-de-passe">Changer - Mot de passe</a>

                <a class="but2" href="favoris">Mes favoris</a>
                <a class="but0" href="vendre">Vendre</a>
                
                <a class="but6" href="deconnection">Se déconnecter</a>
                <a class="but7" href="supprimer-profil">Supprimer le compte</a>

            </section>

            <section class="container-consoles1">
                <div class="titre-consoles1">
                    <h1>Activation</h1>
                </div>
            </section>

        </section>

        <?php include '../11-footer.html'; ?>

        <script src="../js/10-script.js"></script>

    </body>
    
</html>