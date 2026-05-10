<?php
$features = [
    [
        'icon' => '🌍',
        'title' => 'Ton Empreinte Éco',
        'desc' => 'Suivez ton impact environnemental au quotidien et découvre comment réduire tes émissions pour protéger la planète.'
    ],
    [
        'icon' => '❤️',
        'title' => 'Ton Espace Santé',
        'desc' => 'Configure tes allergies et préférences. Nous veillons à ce que chaque suggestion soit sûre et adaptée à tes besoins.'
    ],
    [
        'icon' => '🥬',
        'title' => 'Ton Garde-Manger',
        'desc' => 'Garde un œil sur tes réserves. Évite le gaspillage en sachant exactement ce que tu as en stock et ce qui va expirer.'
    ],
    [
        'icon' => '📖',
        'title' => 'Ton Carnet de Recettes',
        'desc' => 'Accède à des idées de repas créatives et durables, spécialement séléctionnées selon tes goûts et ce qu\'il reste dans ta cuisine.'
    ],
    [
        'icon' => '📢',
        'title' => 'Ton Fil d\'Actualité',
        'desc' => 'Partage tes astuces éco-responsables, inspire la communauté et découvre les meilleures pratiques des autres membres.'
    ]
];
?>

<style>
.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}
.feature-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    text-align: center;
}
.feature-card:hover {
    transform: translateY(-10px);
    border-color: #5EEAD4;
    box-shadow: 0 24px 48px rgba(0,0,0,0.45);
}
.feature-card .icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    display: block;
}
.feature-card h3 {
    color: #b2f2bb;
    margin-bottom: 15px;
    font-size: 20px;
}
.feature-card p {
    color: #e0e0e0;
    line-height: 1.6;
    font-size: 14px;
}
</style>

<section class="features" id="features">
<?php foreach ($features as $feature): ?>
    <div class="feature-card">
        <span class="icon"><?= htmlspecialchars($feature['icon']) ?></span>
        <h3><?= htmlspecialchars($feature['title']) ?></h3>
        <p><?= htmlspecialchars($feature['desc']) ?></p>
    </div>
<?php endforeach; ?>
</section>
