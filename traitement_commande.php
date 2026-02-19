<?php
session_start();
require_once 'db.php'; 
require 'PHPMailer/PHPMailer/src/Exception.php';
require 'PHPMailer/PHPMailer/src/PHPMailer.php';
require 'PHPMailer/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. FONCTION DE CALCUL DE LA DISTANCE
function calculerDistanceBordeaux($ville_client) {
    $distances = [
        'bordeaux' => 0, 'cenon' => 5, 'floirac' => 6, 'merignac' => 8,
        'pessac' => 7, 'talence' => 4, 'begles' => 5, 'lormont' => 7, 'roubaix' => 800
    ];
    $ville = strtolower(trim($ville_client));
    return $distances[$ville] ?? 15; // 15km par défaut si ville inconnue
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. RÉCUPÉRATION ET SÉCURISATION DES DONNÉES
    $nom = htmlspecialchars($_POST['nom'] ?? 'Client');
    $prenom = htmlspecialchars($_POST['prenom'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $ville = htmlspecialchars($_POST['ville'] ?? 'Bordeaux');
    $nb_personnes = intval($_POST['nb_personnes'] ?? 10);
    $adresse = htmlspecialchars($_POST['adresse'] ?? '');
    $cp = htmlspecialchars($_POST['codepostal'] ?? '');
    $adresse_complete = $adresse . " " . $cp . " " . $ville;

    // 3. CALCULS RÈGLES MÉTIER
    $prix_unitaire = 21.00; 
    $total_ht_menus = $nb_personnes * $prix_unitaire;

    // Réduction de 10% si 15 personnes ou plus
    if ($nb_personnes >= 15) {
        $total_ht_menus *= 0.9;
    }

    // Calcul des frais de livraison
    $distance = calculerDistanceBordeaux($ville);
    $frais_livraison = 5 + ($distance * 0.59);
    
    $total_final = $total_ht_menus + $frais_livraison;

    // Calcul de la TVA (10%) pour la facture
    $taux_tva = 0.10;
    $montant_ht_total = $total_final / (1 + $taux_tva);
    $montant_tva = $total_final - $montant_ht_total;

    // 4. INSERTION EN BASE DE DONNÉES
    $ins = $pdo->prepare("INSERT INTO commandes (nom, prenom, email, adresse, total, statut, date_commande) VALUES (?, ?, ?, ?, ?, 'en attente', NOW())");
    $ins->execute([$nom, $prenom, $email, $adresse_complete, $total_final]);

    // 5. PRÉPARATION ET ENVOI DE L'EMAIL (FORMAT FACTURE)
    $mail = new PHPMailer(true);
    try {
        // Configuration Mailtrap
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '3e513c7983a9f8'; 
        $mail->Password   = '94811270281aec'; 
        $mail->Port       = 2525;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('cuisine@vite-gourmand.fr', 'Vite & Gourmand');
        $mail->addAddress($email, $nom); 
        $mail->isHTML(true);
        $mail->Subject = 'VOTRE FACTURE - Vite & Gourmand'; // Objet modifié pour vérifier la mise à jour

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; padding: 20px;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h2 style='color: #e67e22; margin-bottom: 5px;'>VITE & GOURMAND</h2>
                <p style='font-size: 12px; color: #666;'>12 rue de la Gourmandise, 33000 Bordeaux</p>
            </div>

            <h3 style='border-bottom: 2px solid #e67e22; padding-bottom: 10px;'>Confirmation de Commande</h3>
            
            <p><strong>Client :</strong> $prenom $nom</p>
            <p><strong>Lieu de prestation :</strong> $adresse_complete</p>

            <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
                <thead>
                    <tr style='background-color: #f8f8f8;'>
                        <th style='padding: 10px; text-align: left; border: 1px solid #ddd;'>Description</th>
                        <th style='padding: 10px; text-align: right; border: 1px solid #ddd;'>Total TTC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd;'>Prestation gastronomique ($nb_personnes convives)</td>
                        <td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>" . number_format($total_ht_menus, 2, ',', ' ') . " €</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd;'>Livraison et logistique ($distance km)</td>
                        <td style='padding: 10px; text-align: right; border: 1px solid #ddd;'>" . number_format($frais_livraison, 2, ',', ' ') . " €</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style='font-weight: bold;'>
                        <td style='padding: 10px; text-align: right;'>Total HT</td>
                        <td style='padding: 10px; text-align: right;'>" . number_format($montant_ht_total, 2, ',', ' ') . " €</td>
                    </tr>
                    <tr style='font-weight: bold;'>
                        <td style='padding: 10px; text-align: right;'>TVA (10%)</td>
                        <td style='padding: 10px; text-align: right;'>" . number_format($montant_tva, 2, ',', ' ') . " €</td>
                    </tr>
                    <tr style='background-color: #e67e22; color: white; font-weight: bold;'>
                        <td style='padding: 10px; text-align: right;'>TOTAL TTC À RÉGLER</td>
                        <td style='padding: 10px; text-align: right; font-size: 18px;'>" . number_format($total_final, 2, ',', ' ') . " €</td>
                    </tr>
                </tfoot>
            </table>

            <div style='margin-top: 30px; font-size: 11px; color: #777; text-align: center;'>
                <p>Julie et José vous remercient ! Code de suivi : 7624</p>
                <p><em>Ceci est une confirmation automatique valant note d'honoraire.</em></p>
            </div>
        </div>";

        $mail->send();
        
        // Nettoyage et redirection
        $_SESSION['panier'] = [];
        header("Location: confirmation.php?status=success");
        exit();

    } catch (Exception $e) {
        echo "Erreur lors de l'envoi : " . $mail->ErrorInfo;
    }
} else {
    header("Location: index.php");
}
?>