<?php
session_start();
require_once 'db.php';

// Sécurité : Seul l'admin peut créer des comptes
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Traitement du formulaire
if (isset($_POST['creer_employe'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $password_clair = $_POST['password'];
    
    // Hachage sécurisé du mot de passe
    $password_hash = password_hash($password_clair, PASSWORD_DEFAULT);
    $role = 'employe';

    try {
        $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $prenom, $email, $password_hash, $role]);
        $message = "<p style='color:green;'>✅ Le compte de l'employé $prenom a été créé avec succès !</p>";
    } catch (PDOException $e) {
        $message = "<p style='color:red;'>❌ Erreur : " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion Employés</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container" style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd;">
        <h1>Créer un profil Employé (Julie)</h1>
        
        <?= $message ?>

        <form method="POST">
            <div style="margin-bottom: 10px;">
                <label>Nom :</label><br>
                <input type="text" name="nom" required style="width:100%;">
            </div>
            <div style="margin-bottom: 10px;">
                <label>Prénom :</label><br>
                <input type="text" name="prenom" required style="width:100%;">
            </div>
            <div style="margin-bottom: 10px;">
                <label>Email :</label><br>
                <input type="email" name="email" required style="width:100%;">
            </div>
            <div style="margin-bottom: 10px;">
                <label>Mot de passe provisoire :</label><br>
                <input type="password" name="password" required style="width:100%;">
            </div>
            <button type="submit" name="creer_employe" style="background-color: #e67e22; color: white; padding: 10px; border: none; cursor: pointer; width: 100%;">
                Créer le compte employé
            </button>
        </form>

        <p style="margin-top: 20px;"><a href="admin_dashboard.php">⬅ Retour au tableau de bord</a></p>
    </div>
</body>
</html>