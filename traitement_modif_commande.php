<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commande'])) {
    $id_commande = $_POST['id_commande'];
    $nouvelle_adresse = htmlspecialchars($_POST['adresse']);
    $nouvelle_date = $_POST['date'];
    $user_email = $_SESSION['email'];

    // Double vérification du statut avant modification
    $check = $pdo->prepare("SELECT statut FROM commandes WHERE id = ? AND email = ?");
    $check->execute([$id_commande, $user_email]);
    $commande = $check->fetch();

    if ($commande && trim(strtolower($commande['statut'])) === 'en attente') {
        $update = $pdo->prepare("UPDATE commandes SET adresse = ?, date_commande = ? WHERE id = ?");
        $update->execute([$nouvelle_adresse, $nouvelle_date, $id_commande]);

        header("Location: profil_utilisateur.php?msg=Commande modifiée !");
    } else {
        die("Erreur : La commande ne peut plus être modifiée.");
    }
} else {
    header("Location: profil_utilisateur.php");
}
exit();