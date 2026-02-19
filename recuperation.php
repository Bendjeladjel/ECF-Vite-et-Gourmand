<?php
session_start();
require_once 'db.php';

$step = 1; // Par défaut, on demande l'email
$error = "";

// LOGIQUE DE TRAITEMENT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // CAS 1 : L'utilisateur vient d'entrer son email
    if (isset($_POST['email_check'])) {
        $email = htmlspecialchars($_POST['email_check']);
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['temp_user_id'] = $user['id'];
            $step = 2; // On passe à l'étape du nouveau MDP
        } else {
            $error = "Cet email n'existe pas.";
        }
    }

    // CAS 2 : L'utilisateur vient d'entrer son nouveau MDP
    if (isset($_POST['nouveau_mdp']) && isset($_SESSION['temp_user_id'])) {
        $mdp_hache = password_hash($_POST['nouveau_mdp'], PASSWORD_DEFAULT);
        $id = $_SESSION['temp_user_id'];

        $update = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
        if ($update->execute([$mdp_hache, $id])) {
            unset($_SESSION['temp_user_id']);
            echo "<script>alert('Mot de passe mis à jour !'); window.location.href='login.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récupération - Vite & Gourmand</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
     <?php include 'header.php'; ?>
</head>
<body>
    <div class="container" style="max-width: 450px; margin: 80px auto; background: rgba(0,0,0,0.85); padding: 30px; border-radius: 15px; color: white; text-align: center; border: 1px solid #e67e22;">
        
        <?php if ($step == 1): ?>
            <h2>Mot de passe oublié ?</h2>
            <p>Entrez votre email pour continuer.</p>
            <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
            
            <form method="POST">
                <input type="email" name="email_check" placeholder="votre@email.com" required style="width:90%; padding:12px; margin: 15px 0; border-radius: 5px; border:none;">
                <button type="submit" style="background: #e67e22; color: white; border: none; padding: 12px 25px; cursor: pointer; border-radius: 5px; width: 95%;">Vérifier mon compte</button>
            </form>

        <?php else: ?>
            <h2>Nouveau mot de passe</h2>
            <p>Choisissez votre nouveau code secret.</p>
            
            <form method="POST">
                <input type="password" name="nouveau_mdp" placeholder="Nouveau mot de passe" required style="width:90%; padding:12px; margin: 15px 0; border-radius: 5px; border:none;">
                <button type="submit" style="background: #27ae60; color: white; border: none; padding: 12px 25px; cursor: pointer; border-radius: 5px; width: 95%;">Enregistrer le mot de passe</button>
            </form>
        <?php endif; ?>

        <br>
        <a href="login.php" style="color: #bbb; text-decoration: none; font-size: 0.9em;">Retour à la connexion</a>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>