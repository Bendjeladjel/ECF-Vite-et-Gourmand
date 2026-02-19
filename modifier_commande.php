<?php
session_start();
require_once 'db.php';

// 1. On vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$numero_commande = null;
$commande = null;

// 2. RÉCUPÉRATION DE LA COMMANDE
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $numero_commande = intval($_GET['id']);
    
// On récupère l'email de l'utilisateur connecté
$stmtUser = $pdo->prepare("SELECT email FROM utilisateurs WHERE id = ?");
$stmtUser->execute([$_SESSION['user_id']]);
$userEmail = $stmtUser->fetchColumn();

// On cherche la commande MAIS on vérifie qu'elle appartient bien à cet email
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE numero_commande = ? AND email = ?");
$stmt->execute([$numero_commande, $userEmail]);
$commande = $stmt->fetch();

    // Si la commande n'existe pas, on retourne au profil
    if (!$commande) {
        header("Location: profil_utilisateur.php?error=notfound");
        exit();
    }
} else {
    header("Location: profil_utilisateur.php");
    exit();
}

// 3. TRAITEMENT DU FORMULAIRE (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maj_commande'])) {
    $nouvelle_adresse = htmlspecialchars($_POST['adresse']);
    
    // Mise à jour de l'adresse
    $update = $pdo->prepare("UPDATE commandes SET adresse = ? WHERE numero_commande = ?");
    $update->execute([$nouvelle_adresse, $numero_commande]);

    header("Location: profil_utilisateur.php?msg=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier ma commande</title>
    <link rel="stylesheet" href="CSS-Accueil.css">
    <style>
        /* Style pour forcer l'affichage par dessus le fond forêt */
        .edit-container { 
            background: rgba(0, 0, 0, 0.9); 
            color: white; 
            padding: 30px; 
            max-width: 500px; 
            margin: 100px auto; 
            border-radius: 10px; 
            border: 2px solid #27ae60;
            text-align: center;
        }
        textarea { width: 100%; height: 80px; margin: 15px 0; padding: 10px; border-radius: 5px; }
        .btn { background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Modifier la commande n°<?= $numero_commande ?></h2>
        
        <form method="POST">
            <label>Indiquez la nouvelle adresse :</label>
            <textarea name="adresse" required><?= htmlspecialchars($commande['adresse'] ?? '') ?></textarea>
            
            <button type="submit" name="maj_commande" class="btn">Valider la modification</button>
            <br><br>
            <a href="profil_utilisateur.php" style="color: #bbb;">Retour sans modifier</a>
        </form>
    </div>
</body>
</html>