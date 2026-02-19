<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="login-container" style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h1>Connexion</h1>
        
        <?php
        // Affichage des messages d'erreur provenant de traitement_login.php
        if (isset($_SESSION['erreur'])) {
            echo "<p style='color:red;'>" . $_SESSION['erreur'] . "</p>";
            unset($_SESSION['erreur']);
        }
        ?>

        <form action="traitement_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required style="width:100%; padding:8px; margin-bottom:15px;">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required style="width:100%; padding:8px; margin-bottom:15px;">
            </div>

            <button type="submit" style="background-color: #e67e22; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; width: 100%;">
                Se connecter
            </button>
        </form>

        <div style="margin-top: 15px; text-align: center;">
            <a href="recuperation.php" style="color: #e67e22; font-size: 0.9em;">Mot de passe oublié ?</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>