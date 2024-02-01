<?php
    session_start();

    // Importer le fichier de connexion à la base de données
    require 'db_connect_users.php';
    include 'db_connect_annonces.php';

    // Vérifier si l'utilisateur est connecté
    if (!isset($_COOKIE['phpauth_session_cookie'])) {
        header('Location: connection-profil');
        exit();
    }

    error_reporting(E_ALL); // Afficher toutes les erreurs

    // Récupérer l'ID du produit
    $productID = $_GET['id'];

    // Récupérer les chemins des images du produit
    $stmt = $pdoAnnonces->prepare("SELECT image_path1, image_path2, image_path3, image_path4, image_path5 FROM annonces WHERE id = :id");
    $stmt->bindParam(':id', $productID);
    $stmt->execute();
    $product = $stmt->fetch();

    if ($product) {
        // Supprimer les images du serveur
        $imagePaths = ['image_path1', 'image_path2', 'image_path3', 'image_path4', 'image_path5'];
        foreach ($imagePaths as $path) {
            if (!empty($product[$path])) {
                $filePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . basename($product[$path]);
                if (file_exists($filePath)) {
                    unlink($filePath); // Supprime le fichier
                }
            }
        }

        // Supprimer l'entrée de la base de données
        $stmt = $pdoAnnonces->prepare("DELETE FROM annonces WHERE id = :id");
        $stmt->bindParam(':id', $productID);
        $stmt->execute();

        // Rediriger vers la page des produits
        header('Location: produits');
        exit();
    } else {
        echo "Produit introuvable.";
    }
?>
