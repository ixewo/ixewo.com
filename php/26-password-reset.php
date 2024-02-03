<?php 
    require 'db_connect_users.php';
    use PHPAuth\Config as PHPAuthConfig;
    use PHPAuth\Auth as PHPAuthAuth;

    $message = "";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        
        // Vérifiez le token dans la base de données
        $stmt = $dbh->prepare("SELECT uid, expire FROM phpauth_requests WHERE token = :token AND type = 'reset'");
        $stmt->execute([':token' => $token]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($request && !empty($request['expire'])) {
            $expireDate = new DateTime($request['expire']);
            $currentDate = new DateTime();

            // Vérifiez si le token n'a pas expiré
            if ($currentDate < $expireDate) {
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
                $repeatpassword = filter_input(INPUT_POST, 'repeatpassword', FILTER_SANITIZE_STRING);

                if ($password === $repeatpassword) {
                    // Assurez-vous d'avoir initialisé l'objet PHPAuthAuth correctement ici
                    $auth = new PHPAuthAuth($dbh, new PHPAuthConfig($dbh));
                    
                    $result = $auth->resetPass($token, $password, $repeatpassword);
                    
                    if ($result['error']) {
                        $message = $result['message'];
                    } else {
                        $message = "Mot de passe modifié avec succès!";
                    }
                } else {
                    $message = "Les mots de passe ne correspondent pas!";
                }
            } else {
                $message = "Token invalide ou expiré!";
            }
        } else {
            $message = "Token invalide ou inexistant!";
        }
    }
?>

<html lang="fr">

    <head>

        <link rel="icon" type="image/png" href="/images/logo-ixewo-black-back.png">

        <title>Mot de passe oublié - ixewo</title>

        <meta name="description" content="Vous avez oubié votre mot de passe ? Pour le modifier c'est ici !!!">

        <meta property="og:url" content="https://www.ixewo.com" />

        <meta property="og:type" content="website"/>

        <meta property="og:title" content="ixewo"/>

        <meta property="og:description" content="ixewo - le site spécialiste de l'achat vente de l'Univers des jeux-vidéo" />

        <meta property="og:image" content="https://www.ixewo.com/images/logo-m-head.png"/>

        <meta charset="UTF-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="../css/26-styles.css">
        
    </head>

    <body>

        <?php include '1-header.php'; ?>

        <section class="main">

            <section class="container-consoles">
                <div class="titre-consoles">
                    <h1>Nouveau mot de passe</h1>
                </div>
            </section>

            <section class="register-informations">

                <form class="register-section" method="post" action="nouveau-mot-de-passe?token=<?php echo $token; ?>" >
                    <input id="token" type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                    
                    <input id="password" type="password" name="password" placeholder="Entrez le nouveau mot de passe" required>
                    <input id="repeatpassword" type="password" name="repeatpassword" placeholder="Répétez le nouveau mot de passe" required>

                    <input id="submit" type="submit" value="Changer le mot de passe">
                </form>

            </section>

            <?php if ($message): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>

            <section class="container-consoles1">
                <div class="titre-consoles1">
                    <h1>nouveau mot de passe</h1>
                </div>
            </section>

        </section>

        <?php include '../11-footer.html'; ?>

        <script src="../js/26-script.js"></script>

    </body>
    
</html>     
