<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) { header("Location: connexion.php"); exit(); }

$id = $_SESSION['user_id'];

// Suppression en base de données
$stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
if ($stmt->execute([$id])) {
    // On vide la session et on détruit tout
    session_destroy();
    header("Location: index.php?account_deleted=1");
    exit();
} else {
    header("Location: profil_utilisateur.php?error=delete");
    exit();
}
// Dans supprimer_compte.php, avant de supprimer l'utilisateur :
$email_user = $_SESSION['email'];
$delOrders = $pdo->prepare("DELETE FROM commandes WHERE email = ?");
$delOrders->execute([$email_user]);

// Ensuite, tu supprimes l'utilisateur comme tu le faisais déjà