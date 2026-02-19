<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $mdp = $_POST['mdp'];

    // 1. Vérifier si l'email existe déjà
    $verif = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $verif->execute([$email]);
    
    if ($verif->fetch()) {
        $_SESSION['erreur'] = "Cet email est déjà utilisé.";
        header("Location: inscription.php");
        exit();
    }

    // 2. Hachage du mot de passe
    $mdp_hache = password_hash($mdp, PASSWORD_DEFAULT);
// ... (après le hachage du mot de passe)
$numero = htmlspecialchars($_POST['numero'] ?? ''); // Récupération sécurisée

try {
    // 3. Insertion (Vérifie bien que le nom de la colonne est 'numero' sans accent en DB)
    $ins = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, numero, role) VALUES (?, ?, ?, ?, ?, 'client')");
    $ins->execute([$nom, $prenom, $email, $mdp_hache, $numero]);

    // 4. Connexion automatique
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['prenom'] = $prenom;
    $_SESSION['nom'] = $nom; 
    $_SESSION['role'] = 'client';
    $_SESSION['email'] = $email;
    $_SESSION['numero'] = $numero; // On stocke le numéro en session

    header("Location: profil_utilisateur.php");
    exit();

} catch (PDOException $e) {
    // En cas d'erreur orange "Inscription", décommente la ligne suivante pour voir le coupable :
    // die("Erreur SQL : " . $e->getMessage()); 
    $_SESSION['erreur'] = "Une erreur est survenue lors de l'inscription.";
    header("Location: inscription.php");
    exit();
}
}