<?php
  session_start();

  // Autoriser les domaines que vous souhaitez
  header("Access-Control-Allow-Origin: *****");

  // Méthodes autorisées
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

  // En-têtes autorisés
  header("Access-Control-Allow-Headers: Content-Type, Authorization");

  header("Content-Type: application/json"); // Indique que le contenu renvoyé est du JSON
  
  require_once('Login/vendor/autoload.php');
  require_once 'secrets.php';

  \Stripe\Stripe::setApiKey($stripeSecretKey);

  $YOUR_DOMAIN = '*****';

  try {
      // Assurez-vous que la valeur existe et est valide
      $prixTotal = $_SESSION['prixTotal'] * 100; // Convertir en centimes pour Stripe
      $titreProduit = $_SESSION['titreProduit'];
      $imageProduit = $_SESSION['imageProduit'] ?? null;

      if (!$imageProduit) {
          // Gérer l'erreur si l'image n'est pas trouvée dans la session
          die("Erreur : URL de l'image non trouvée.");
      }

      $checkout_session = \Stripe\Checkout\Session::create([
          'line_items' => [[
              'price_data' => [
                  'currency' => 'eur',
                  'product_data' => [
                      'name' => 'ixewo - achat "' . $titreProduit . '"',
                      'images' => [$imageProduit],
                  ],
                  'unit_amount' => $prixTotal,
              ],
              'quantity' => 1,
          ]],
          'mode' => 'payment',
          'success_url' => $YOUR_DOMAIN . '/commande-confirmee',
          'cancel_url' => $YOUR_DOMAIN . '/commande-annulee',
      ]);

      header("Location: " . $checkout_session->url);
      exit;

  } catch (Exception $e) {
      header('Content-Type: application/json');
      http_response_code(500);
      echo json_encode(['error' => $e->getMessage()]);
      exit;
  }
?>
