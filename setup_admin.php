<?php
require_once 'db.php';

$email = 'jose@vite-gourmand.fr';
$password_clair = 'Admin@Vite2026'; // Respecte les 10 caractères + maj + spécial
$password_hash = password_hash($password_clair, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO utilisateur (email, password, role, prenom, nom, est_actif) 
            VALUES (?, ?, 'admin', 'José', 'Admin', 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email, $password_hash]);
    
    echo "Le compte de José a été créé avec succès !<br>";
    echo "Identifiant : " . $email . "<br>";
    echo "Mot de passe : " . $password_clair . "<br>";
    echo "<strong>IMPORTANT : Supprimez ce fichier immédiatement !</strong>";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>