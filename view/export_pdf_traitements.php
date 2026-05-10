<?php
declare(strict_types=1);
/**
 * Point d'entrée dédié à l'exportation PDF des traitements.
 */
session_start();

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Accès refusé');
}

$params = array_merge($_GET, $_POST);

require_once __DIR__ . '/../controller/metier.php';

$metier = new Metier();
$terme  = Metier::termeBarreDepuisGet($params);
$tri    = Metier::triTraitementDepuisGet($params);

$lignes = $metier->rechercherTraitements($terme !== '' ? $terme : null, $tri);

$fpdfPath = __DIR__ . '/../fpdf/fpdf.php';
if (!file_exists($fpdfPath)) {
    header('Location: traitements.php?pdf_err=' . rawurlencode('FPDF introuvable.'));
    exit;
}
require_once $fpdfPath;

$mapTypes = [
    'antihistaminique'   => 'Antihistaminique',
    'corticoide'         => 'Corticoide',
    'bronchodilatateur'  => 'Bronchodilatateur',
    'decongestionnant'   => 'Decongestionnant',
    'adrenaline'         => 'Adrenaline (urgence)',
    'immunotherapie'     => 'Immunotherapie',
    'autre'              => 'Autre',
];

function conv(string $s): string {
    return iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s) ?: $s;
}

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, conv('Liste des Traitements'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 7, conv('Tri : ' . $tri . ($terme !== '' ? '  |  Recherche : ' . $terme : '')), 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFillColor(16, 185, 129);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$w = [8, 40, 30, 22, 18, 72];
$headers = ['#', 'Nom', 'Type', 'Dosage', 'Duree', 'Effets secondaires'];
foreach ($headers as $i => $h_) {
    $pdf->Cell($w[$i], 8, conv($h_), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(240, 255, 248);
$fill = false;
$pdf->SetFont('Arial', '', 7);
foreach ($lignes as $r) {
    $type = (string)($r['type_traitement'] ?? '');
    $type = $mapTypes[strtolower($type)] ?? $type;
    $pdf->Cell($w[0], 6, (string)($r['id_traitement'] ?? ''), 1, 0, 'C', $fill);
    $pdf->Cell($w[1], 6, conv(mb_substr((string)($r['nom'] ?? ''), 0, 32)), 1, 0, 'L', $fill);
    $pdf->Cell($w[2], 6, conv(mb_substr($type, 0, 24)), 1, 0, 'L', $fill);
    $pdf->Cell($w[3], 6, conv(mb_substr((string)($r['dosage'] ?? ''), 0, 16)), 1, 0, 'L', $fill);
    $pdf->Cell($w[4], 6, conv(mb_substr((string)($r['duree'] ?? ''), 0, 12)), 1, 0, 'C', $fill);
    $pdf->Cell($w[5], 6, conv(mb_substr((string)($r['effets_secondaires'] ?? ''), 0, 58)), 1, 1, 'L', $fill);
    $fill = !$fill;
}

$pdf->Ln(4);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, conv('Total : ' . count($lignes) . ' traitement(s)'), 0, 1, 'R');

$pdfData = $pdf->Output('S');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="traitements.pdf"');
header('Content-Length: ' . strlen($pdfData));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
echo $pdfData;
exit;
