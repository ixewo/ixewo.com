<?php

  session_start();

  // Set the content type to JSON
  header('Content-Type: application/json');  

  // Importer le fichier de connexion à la base de données
  require 'db_connect_users.php';

  // Importer le fichier de connexion à la base de données
  include 'db_connect_annonces.php';

  $response = ["status" => "success", "message" => ""];

  try {
    // Check if user is logged in
    if (!isset($_COOKIE['phpauth_session_cookie'])) {
      $response["status"] = "error";
      $response["message"] = "<a href='connection-profil'>Connectez-vous</a> pour ajouter aux favoris";
      echo json_encode($response);
      exit;
    }

    // Lier le paramètre userID
    $hash = $_COOKIE['phpauth_session_cookie'];
    $userId = $auth->getSessionUID($hash);

    // Récupérer le parametre productId
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);

    // Check if product is already in the favorites list
    $checkQuery = "SELECT * FROM UserFavorites WHERE user_id = ? AND product_id = ?";
    $checkStmt = $pdoAnnonces->prepare($checkQuery);
    $checkStmt->bindParam(1, $userId);
    $checkStmt->bindParam(2, $productId);
    $checkStmt->execute();
    $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
      $deleteQuery = "DELETE FROM UserFavorites WHERE user_id = ? AND product_id = ?";
      $deleteStmt = $pdoAnnonces->prepare($deleteQuery);
      $deleteStmt->bindParam(1, $userId);
      $deleteStmt->bindParam(2, $productId);
      $deleteStmt->execute();
    } else {
      $insertQuery = "INSERT INTO UserFavorites (user_id, product_id) VALUES (?, ?)";
      $insertStmt = $pdoAnnonces->prepare($insertQuery);
      $insertStmt->bindParam(1, $userId);
      $insertStmt->bindParam(2, $productId);
      $insertStmt->execute();
    }

  } catch (Exception $e) {
    $response["status"] = "error";
    $response["message"] = $e->getMessage();
  }

  echo json_encode($response);
    
?>
