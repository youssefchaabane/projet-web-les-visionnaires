<?php
/**
 * PdfGenerator - Générateur de PDF simple sans dépendance externe.
 */
class PdfGenerator
{
    public static function outputPdf(string $title, array $lines)
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        $pdf = self::buildPdf($title, $lines);

        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=\"recettes.pdf\"; filename*=UTF-8''recettes.pdf");
        header('Content-Length: ' . strlen($pdf));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Expires: 0');

        echo $pdf;
        flush();
        exit;
    }

    private static function buildPdf(string $title, array $lines): string
    {
        $title = self::toLatin1($title);
        $content = "BT /F1 18 Tf 50 800 Td (" . self::escapeString($title) . ") Tj ET\n";
        $content .= "BT /F1 10 Tf 50 780 Td (" . self::escapeString('Date: ' . date('d/m/Y H:i')) . ") Tj ET\n";
        $content .= "BT /F1 10 Tf 50 766 Td (" . self::escapeString(str_repeat('-', 90)) . ") Tj ET\n";

        $y = 748;
        $lineHeight = 14;

        foreach ($lines as $line) {
            $line = self::toLatin1($line);
            $wrapped = self::wrapText($line, 92);

            foreach ($wrapped as $row) {
                if ($y < 60) {
                    break;
                }
                $content .= "BT /F1 10 Tf 50 $y Td (" . self::escapeString($row) . ") Tj ET\n";
                $y -= $lineHeight;
            }
            if ($y < 60) {
                break;
            }
        }

        return self::createPdfStream($content);
    }

    private static function toLatin1(string $text): string
    {
        if (function_exists('mb_convert_encoding')) {
            $converted = @mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
            if ($converted !== false) {
                return $converted;
            }
        }
        return utf8_decode($text);
    }

    private static function escapeString(string $value): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }

    private static function wrapText(string $text, int $maxLength): array
    {
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $lines = [''];

        foreach ($words as $word) {
            $current = end($lines);
            if ($current === '') {
                $lines[key($lines)] = $word;
            } elseif (strlen($current . ' ' . $word) <= $maxLength) {
                $lines[key($lines)] = $current . ' ' . $word;
            } else {
                $lines[] = $word;
            }
        }

        return $lines;
    }

    private static function createPdfStream(string $content): string
    {
        $header = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream\nendobj\n";
        $objects[] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $pdf = $header;
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= sprintf("%010d 65535 f \n", 0);

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPos . "\n%%EOF";

        return $pdf;
    }
}
