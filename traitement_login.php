<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password_saisi = $_POST['password'];

    // On cherche l'utilisateur dans la table 'utilisateurs'
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Vérification avec la colonne 'mot_de_passe'
    if ($user && password_verify($password_saisi, $user['mot_de_passe'])) {
        
        // Stockage des informations en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role']; 
        $_SESSION['nom'] = $user['nom'];

        // Redirection vers le dashboard
        if ($user['role'] === 'admin' || $user['role'] === 'employe') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
if ($user && password_verify($password_saisi, $user['mot_de_passe'])) {
    // Connexion réussie : on remplit la session
    $_SESSION['user_id'] = $user['id'];
    // ... redirection vers profil
} else {
    // Échec : message générique pour ne pas aider les pirates
    $_SESSION['erreur'] = "Email ou mot de passe incorrect.";
    header("Location: login.php");
}
    } else {
        // En cas d'échec, retour au login avec message d'erreur
        $_SESSION['erreur'] = "Email ou mot de passe incorrect.";
        header("Location: login.php");
        exit();
    }
}