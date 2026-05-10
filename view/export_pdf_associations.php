<?php
declare(strict_types=1);
/**
 * Point d'entrée dédié à l'exportation PDF des associations.
 */
session_start();

if (($_SESSION['role'] ?? '') !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Accès refusé');
}

$params = array_merge($_GET, $_POST);

require_once __DIR__ . '/../controller/metier.php';

$metier            = new Metier();
$terme             = Metier::termeBarreDepuisGet($params);
$tri               = Metier::triAssociationDepuisGet($params);
$idAllergieFiltre  = (int)($params['id_allergie']  ?? 0);
$idTraitFiltre     = (int)($params['id_traitement'] ?? 0);

$lignes = $metier->rechercherAssociations($idAllergieFiltre, $idTraitFiltre, $terme !== '' ? $terme : null, $tri);

$fpdfPath = __DIR__ . '/../fpdf/fpdf.php';
if (!file_exists($fpdfPath)) {
    header('Location: associations.php?pdf_err=' . rawurlencode('FPDF introuvable.'));
    exit;
}
require_once $fpdfPath;

function conv(string $s): string {
    return iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s) ?: $s;
}

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, conv('Associations Allergie / Traitement'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 7, conv('Tri : ' . $tri . ($terme !== '' ? '  |  Recherche : ' . $terme : '')), 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFillColor(16, 185, 129);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$w = [12, 12, 72, 72, 32];
$headers = ['Al.', 'Tr.', 'Allergie', 'Traitement', 'Type traitement'];
foreach ($headers as $i => $h_) {
    $pdf->Cell($w[$i], 8, conv($h_), 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(240, 255, 248);
$fill = false;
$pdf->SetFont('Arial', '', 7);
foreach ($lignes as $r) {
    $pdf->Cell($w[0], 6, (string)($r['id_allergie']   ?? ''), 1, 0, 'C', $fill);
    $pdf->Cell($w[1], 6, (string)($r['id_traitement'] ?? ''), 1, 0, 'C', $fill);
    $pdf->Cell($w[2], 6, conv(mb_substr((string)($r['allergie_nom']    ?? ''), 0, 56)), 1, 0, 'L', $fill);
    $pdf->Cell($w[3], 6, conv(mb_substr((string)($r['traitement_nom']  ?? ''), 0, 56)), 1, 0, 'L', $fill);
    $pdf->Cell($w[4], 6, conv(mb_substr((string)($r['type_traitement'] ?? ''), 0, 26)), 1, 1, 'L', $fill);
    $fill = !$fill;
}

$pdf->Ln(4);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, conv('Total : ' . count($lignes) . ' association(s)'), 0, 1, 'R');

$pdfData = $pdf->Output('S');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="associations.pdf"');
header('Content-Length: ' . strlen($pdfData));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
echo $pdfData;
exit;
