<?php
require_once 'db.php';

// Liste des comptes à créer
$comptes = [
    [
        'nom' => 'Gourmand',
        'prenom' => 'Julie',
        'email' => 'julie@vite-gourmand.fr',
        'mdp' => 'Julie2026!', // À changer après la première connexion
        'role' => 'admin',
        'poste' => 'Gérante'
    ],
    [
        'nom' => 'Cuisto',
        'prenom' => 'José',
        'email' => 'jose@vite-gourmand.fr',
        'mdp' => 'Jose2026!',
        'role' => 'admin',
        'poste' => 'Chef de cuisine'
    ]
];

try {
    foreach ($comptes as $c) {
        // On hache le mot de passe
        $hash = password_hash($c['mdp'], PASSWORD_DEFAULT);

        // Insertion dans 'utilisateurs'
        $req = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
        $req->execute([$c['nom'], $c['prenom'], $c['email'], $hash, $c['role']]);
        
        $lastId = $pdo->lastInsertId();

        // Insertion dans 'employes'
        $reqEmp = $pdo->prepare("INSERT INTO employes (utilisateur_id, poste) VALUES (?, ?)");
        $reqEmp->execute([$lastId, $c['poste']]);

        echo "Compte pour {$c['prenom']} créé avec succès !<br>";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>