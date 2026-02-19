<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="login-container" style="max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h1>Inscription Client</h1>
        
        <?php
        if (isset($_SESSION['erreur'])) {
            echo "<p style='color:red;'>" . $_SESSION['erreur'] . "</p>";
            unset($_SESSION['erreur']);
        }
        ?>

        <form action="traitement_inscription.php" method="POST">
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label>Prénom :</label>
                    <input type="text" name="prenom" required style="width:100%; padding:8px; margin-bottom:15px;">
                </div>
                <div style="flex: 1;">
                    <label>Nom :</label>
                    <input type="text" name="nom" required style="width:100%; padding:8px; margin-bottom:15px;">
                </div>
            </div>

            <label>Email :</label>
            <input type="email" name="email" required style="width:100%; padding:8px; margin-bottom:15px;">
            
            <label>Mot de passe :</label>
            <input type="password" name="mdp" required style="width:100%; padding:8px; margin-bottom:15px;">

            <label>Numéro :</label>
            <input type="numero" name="numero" required style="width:100%; padding:8px; margin-bottom:15px;">
            
<button type="submit" style="background-color: #27ae60; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; width: 100%;">
    Créer mon compte
</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            Déjà client ? <a href="login.php">Connectez-vous ici</a>
        </p>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>