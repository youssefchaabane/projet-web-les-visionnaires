<?php
declare(strict_types=1);
/**
 * Point d'entrée dédié à l'exportation PDF des allergies.
 * Accepte GET et POST — aucun HTML émis, uniquement le PDF binaire.
 */
session_start();

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Accès refusé');
}

$params = array_merge($_GET, $_POST);

if (!function_exists('h')) {
    function h(?string $s): string {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}

require_once __DIR__ . '/../controller/metier.php';

$metier = new Metier();
$terme  = Metier::termeBarreDepuisGet($params);
$tri    = Metier::triAllergieDepuisGet($params);

$lignes = $metier->rechercherAllergies($terme !== '' ? $terme : null, $tri);

// Charger FPDF localement
$fpdfPath = __DIR__ . '/../fpdf/fpdf.php';
if (!file_exists($fpdfPath)) {
    header('Location: allergies.php?pdf_err=' . rawurlencode('FPDF introuvable.'));
    exit;
}
require_once $fpdfPath;

$mapTypes = [
    'medicament'    => 'Medicament',
    'environnement' => 'Environnementale',
    'alimentaire'   => 'Alimentaire',
    'contact'       => 'De contact (peau)',
    'animale'       => 'Animale',
    'insecte'       => 'Piqures d insectes',
    'autre'         => 'Autre',
];
$mapNiveaux = [
    'tres_leger' => 'Tres leger',
    'leger'      => 'Leger',
    'modere'     => 'Modere',
    'eleve'      => 'Eleve',
    'critique'   => 'Critique',
];

function conv(string $s): string {
    return iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s) ?: $s;
}

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, conv('Liste des Allergies'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 7, conv('Tri : ' . $tri . ($terme !== '' ? '  |  Recherche : ' . $terme : '')), 0, 1, 'C');
$pdf->Ln(3);

// En-têtes du tableau
$pdf->SetFillColor(16, 185, 129);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$w = [8, 38, 28, 22, 52, 52];
$headers = ['#', 'Nom', 'Type', 'Niveau', 'Description', 'Symptomes'];
foreach ($headers as $i => $h_) {
    $pdf->Cell($w[$i], 8, conv($h_), 1, 0, 'C', true);
}
$pdf->Ln();

// Lignes de données
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(240, 255, 248);
$fill = false;
$pdf->SetFont('Arial', '', 7);
foreach ($lignes as $r) {
    $type = (string)($r['type'] ?? '');
    $type = $mapTypes[strtolower($type)] ?? $type;
    $niveau = (string)($r['niveau_danger'] ?? '');
    $niveau = $mapNiveaux[strtolower($niveau)] ?? $niveau;
    $pdf->Cell($w[0], 6, (string)($r['id_allergie'] ?? ''), 1, 0, 'C', $fill);
    $pdf->Cell($w[1], 6, conv(mb_substr((string)($r['nom'] ?? ''), 0, 28)), 1, 0, 'L', $fill);
    $pdf->Cell($w[2], 6, conv(mb_substr($type, 0, 20)), 1, 0, 'L', $fill);
    $pdf->Cell($w[3], 6, conv(mb_substr($niveau, 0, 14)), 1, 0, 'L', $fill);
    $pdf->Cell($w[4], 6, conv(mb_substr((string)($r['description'] ?? ''), 0, 42)), 1, 0, 'L', $fill);
    $pdf->Cell($w[5], 6, conv(mb_substr((string)($r['symptomes'] ?? ''), 0, 42)), 1, 1, 'L', $fill);
    $fill = !$fill;
}

$pdf->Ln(4);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, conv('Total : ' . count($lignes) . ' allergie(s)'), 0, 1, 'R');

// Envoi propre
$pdfData = $pdf->Output('S');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="allergies.pdf"');
header('Content-Length: ' . strlen($pdfData));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
echo $pdfData;
exit;
