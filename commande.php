<?php
session_start();
require_once 'db.php';

require 'PHPMailer/PHPMailer/src/Exception.php';
require 'PHPMailer/PHPMailer/src/PHPMailer.php';
require 'PHPMailer/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupération des infos
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $ville = trim(strtolower($_POST['ville']));
    $adresse_complete = htmlspecialchars($_POST['adresse']) . " " . htmlspecialchars($_POST['ville']) . " " . htmlspecialchars($_POST['codepostal']);
    $nb_personnes = intval($_POST['nb_personnes']);
    $distance_km = intval($_POST['distance_km'] ?? 0);

    // 2. Calcul du total avec Règles Métier
    $total_menus = 0;
    if (!empty($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $id => $qte) {
            $req = $pdo->prepare('SELECT prix_ttc FROM menus WHERE id = ?');
            $req->execute([$id]);
            $menu = $req->fetch();
            
            // Calcul de base
            $prix_ligne = $menu['prix_ttc'] * $nb_personnes;

            // RÈGLE : Réduction 10% si > 5 personnes par rapport au min (ici min supposé 10)
            if ($nb_personnes >= 15) {
                $prix_ligne = $prix_ligne * 0.9;
            }
            $total_menus += $prix_ligne;
        }
    }

    // RÈGLE : Frais de livraison (5€ + 0.59€/km hors Bordeaux)
    $frais_port = 0;
    if ($ville !== 'bordeaux') {
        $frais_port = 5 + ($distance_km * 0.59);
    }

    $total_final = $total_menus + $frais_port;

    // 3. Insertion en BDD (On ajoute nb_personnes et statut par défaut)
    $ins = $pdo->prepare("INSERT INTO commandes (nom, prenom, email, adresse, total, statut) VALUES (?, ?, ?, ?, ?, 'en attente')");
    $ins->execute([$nom, $prenom, $email, $adresse_complete, $total_final]);

    // 4. ENVOI DE L'EMAIL
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '3e513c7983a9f8'; 
        $mail->Password = '94811270281aec'; 
        $mail->Port = 2525;
        $mail->setFrom('cuisine@vite-gourmand.fr', 'Vite & Gourmand');
        $mail->CharSet = 'UTF-8';
        $mail->addAddress($email, $nom); 
        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de commande - Vite & Gourmand';

        $mail->Body = "
            <div style='font-family: Arial, sans-serif;'>
                <h1 style='color: #e67e22;'>Merci $prenom !</h1>
                <p>Julie et José ont reçu votre commande pour <strong>$nb_personnes convives</strong>.</p>
                <p><strong>Détails :</strong> $adresse_complete</p>
                <p>Livraison : " . number_format($frais_port, 2) . " €</p>
                <p style='font-size: 18px;'><strong>Total Final : " . number_format($total_final, 2) . " €</strong></p>
            </div>";
        $mail->send();
    } catch (Exception $e) {}

    $_SESSION['panier'] = array();
    header("Location: confirmation.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre commande</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container" style="max-width: 600px; margin: auto; padding: 20px;">
    <h1>Finaliser votre commande</h1>
    
    <form action="" method="post">
        <div style="background: #333; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <label>Nombre de convives (min 10) :</label>
            <input type="number" name="nb_personnes" min="10" value="10" required style="width:100%; margin-bottom:10px;">

            <label>Distance de Bordeaux (en km) :</label>
            <input type="number" name="distance_km" value="0" required style="width:100%;">
            <small style="color: #aaa;">(Laissez 0 si vous êtes à Bordeaux centre)</small>
        </div>

        <div class="form-group">
            <label>Nom :</label>
            <input type="text" name="nom" required>
        </div>
        <div class="form-group">
            <label>Prénom :</label>
            <input type="text" name="prenom" required>
        </div>
        <div class="form-group">
            <label>Email :</label>
            <input type="email" name="email" value="<?= $_SESSION['email'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label>Adresse :</label>
            <input type="text" name="adresse" required>
        </div>
        <div class="form-group">
            <label>Ville :</label>
            <input type="text" name="ville" placeholder="Bordeaux, Cenon..." required>
        </div>
        <div class="form-group">
            <label>Code Postal :</label>
            <input type="text" name="codepostal" required>
        </div>

        <button type="submit" style="background: #e67e22; width: 100%; padding: 15px; font-size: 1.2em; cursor:pointer;">
            Valider et Payer
        </button>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>