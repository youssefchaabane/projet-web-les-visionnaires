<?php
declare(strict_types=1);

require_once __DIR__ . '/Gestion_pub/services/AIContentAssistant.php';

$pub = [
    'titre' => 'Test de publication',
    'contenu' => 'Ceci est une superbe publication sur le compostage et le recyclage des déchets alimentaires.',
    'date_publication' => '2026-05-07'
];

$comments = [
    [
        'contenu' => 'Très intéressant, merci pour ce partage !',
        'note' => 5,
        'likes_count' => 3
    ]
];

echo "Appel à AIContentAssistant::summarizePublication...\n";
$res = AIContentAssistant::summarizePublication($pub, $comments);

print_r($res);
