<?php
session_start();
include 'header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande Confirmée - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>

<main class="panier-card" style="text-align: center; margin-top: 50px; padding: 40px;">
    <div style="font-size: 50px; color: #27ae60;">✔️</div>
    <h1 style="color: #333;">Merci pour votre commande !</h1>
    <p style="font-size: 1.2rem; color: #555; margin: 20px 0;">
        Votre demande a bien été enregistrée. Julie et José préparent déjà vos plateaux-repas avec soin.
    </p>
    <p style="color: #777;">
        Un email de confirmation vient de vous être envoyé.
    </p>
    
    <div style="margin-top: 30px;">
        <a href="index.php" class="btn-commande" style="background-color: #e67e22; text-decoration: none; padding: 12px 25px; border-radius: 5px; color: white; font-weight: bold;">
            Retour à l'accueil
        </a>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>