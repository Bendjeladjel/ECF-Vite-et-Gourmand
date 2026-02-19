<?php
session_start();
require_once 'db.php';

// 1. Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Vérification de l'ID reçu (qui correspond à numero_commande)
if (isset($_GET['id'])) {
    $numero_commande = intval($_GET['id']);

    // 3. Sécurité : On vérifie que la commande appartient bien à l'utilisateur connecté
    // On récupère l'email de l'utilisateur pour comparer
    $stmtUser = $pdo->prepare("SELECT email FROM utilisateurs WHERE id = ?");
    $stmtUser->execute([$_SESSION['user_id']]);
    $userEmail = $stmtUser->fetchColumn();

    // 4. Mise à jour du statut en "Annulé"
    // On ne supprime pas (DELETE) pour garder une trace en compta, on change juste le statut
    $sql = "UPDATE commandes SET statut = 'Annulé' 
            WHERE numero_commande = ? AND email = ? AND statut = 'En attente'";
    
    $req = $pdo->prepare($sql);
    $req->execute([$numero_commande, $userEmail]);

    // Redirection vers le profil avec un message de succès (optionnel)
    header("Location: profil_utilisateur.php?message=annule");
    exit();
} else {
    // Si pas d'ID, retour au profil
    header("Location: profil_utilisateur.php");
    exit();
}