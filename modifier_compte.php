<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: connexion.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom']);
    $id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ? WHERE id = ?");
    if ($stmt->execute([$nom, $prenom, $id])) {
        // On met à jour la session pour l'affichage
        $_SESSION['prenom'] = $prenom;
        $_SESSION['nom'] = $nom;
        header("Location: profil_utilisateur.php?success=1");
    } else {
        header("Location: profil_utilisateur.php?error=1");
    }
    exit();
}