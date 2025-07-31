<?php
require_once '../libs/fpdf.php';

if (isset($_GET['reference']) && isset($_GET['telephone']) && isset($_GET['montant']) && isset($_GET['operateur'])) {
    $reference = $_GET['reference'];
    $telephone = $_GET['telephone'];
    $montant = $_GET['montant'];
    $operateur = $_GET['operateur'];

    // Créer une nouvelle instance de FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Ajouter le titre
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Facture de Paiement', 0, 1, 'C');

    // Ajouter les détails de la facture
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Reference: ' . $reference, 0, 1);
    $pdf->Cell(0, 10, 'Telephone: +229' . $telephone, 0, 1);
    $pdf->Cell(0, 10, 'Operateur: ' . ucfirst($operateur), 0, 1);
    $pdf->Cell(0, 10, 'Montant: ' . number_format($montant, 0, ',', ' ') . ' FCFA', 0, 1);

    // Sortie du PDF
    $pdf->Output('D', 'Facture_' . $reference . '.pdf');
} else {
    echo "Paramètres manquants.";
}
?>