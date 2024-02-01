<?php
  $servername = "*****"; 
  $dbUsername = "*****"; 
  $dbPassword = "*****";
  $dbName = "*****";
  $port = *****;

  // Création de la connexion
  try {

    // Connexion à la base de données `*****` - annonces
    $dsnAnnonces = "mysql:host=*****; dbname=*****; port=*****";
    $pdoAnnonces = new PDO($dsnAnnonces, $dbUsername, $dbPassword);

  } catch (Exception $e) {
    die("Exception attrapée : " . $e->getMessage());
  }
?>
