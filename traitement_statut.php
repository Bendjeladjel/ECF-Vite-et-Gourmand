<?php
// ... Config PHPMailer ...

if (isset($_POST['update_statut'])) {
    $id = $_POST['id_commande'];
    $nouveau_statut = $_POST['statut'];

    // Mise à jour BDD
    $pdo->prepare("UPDATE commande SET statut = ? WHERE numero_commande = ?")->execute([$nouveau_statut, $id]);

    // RÈGLE MÉTIER : Alerte Matériel 600€
    if ($nouveau_statut === 'en attente du retour de matériel') {
        $mail->Subject = "IMPORTANT : Retour de matériel - Vite & Gourmand";
        $mail->Body = "Merci de restituer le matériel sous 10 jours. En cas de non-restitution, des frais de 600€ seront appliqués.";
        $mail->send();
    }
    header("Location: admin_commandes.php?success=1");
}

// RÈGLE MÉTIER : Annulation avec motif
if (isset($_POST['annuler_commande'])) {
    $motif = $_POST['motif'];
    $mode = $_POST['mode_contact'];
    $id = $_POST['id_commande'];

    $pdo->prepare("UPDATE commande SET statut = 'annulé', motif_annulation = ?, mode_contact = ? WHERE numero_commande = ?")
        ->execute([$motif, $mode, $id]);
    header("Location: admin_commandes.php?msg=annule");
}