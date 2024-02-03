<?php

  //Load Composer's autoloader
  require 'Login/vendor/autoload.php';

  //Import PHPMailer classes into the global namespace
  use phpmailer\phpmailer\PHPMailer;

  //Import PHPMailer classes into the global namespace
  require 'Login/vendor/phpmailer/phpmailer/src/PHPMailer.php';

  // Création de la connexion
  $dbh = new PDO('mysql:host=localhost;dbname=*****', '*****', '*****');

  $config = new \PHPAuth\Config($dbh);
  $auth   = new \PHPAuth\Auth($dbh, $config);

  //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer();
  $mail->setLanguage('fr', 'Login/vendor/phpmailer/phpmailer/language/phpmailer.lang-fr.php');


?>