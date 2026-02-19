<?php
require_once 'db.php';

// 1. On récupère des données agrégées (ex: total des ventes par statut)
$stmt = $pdo->query("SELECT statut, COUNT(*) as nombre, SUM(total) as CA FROM commandes GROUP BY statut");
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. On prépare la structure NoSQL
$donnees_nosql = [
    "date_export" => date('Y-m-d H:i:s'),
    "restaurant" => "Vite & Gourmand",
    "statistiques" => $stats,
    "code_agent" => "7624" // Ton code mémorisé
];

// 3. On enregistre au format JSON (Format NoSQL par excellence)
file_put_contents('stats_ventes.json', json_encode($donnees_nosql, JSON_PRETTY_PRINT));

echo "Le fichier NoSQL 'stats_ventes.json' a été généré avec succès !";
?>