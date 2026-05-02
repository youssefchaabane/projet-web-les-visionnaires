<?php
$ch = curl_init('http://localhost/gestion-recette1/ai.php');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(["nom_recette" => "salade"]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
]);
$response = curl_exec($ch);
echo "RESPONSE FROM AI.PHP:\n";
echo $response;
?>
