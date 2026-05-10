<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if (!function_exists('h')) { function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }
require_once __DIR__ . '/../config/config.php';
$pdo = config::getConnexion();

$facteurs = $pdo->query("SELECT * FROM eco_facteur_emission ORDER BY co2_par_kg DESC")->fetchAll();
$recettes = $pdo->query("SELECT r.*, a.score_co2_total, a.niveau_impact FROM eco_recette r LEFT JOIN eco_analyse_carbone a ON r.id_recette=a.id_recette ORDER BY a.score_co2_total ASC")->fetchAll();
$avgScore = round((float)($pdo->query("SELECT AVG(score_co2_total) FROM eco_analyse_carbone")->fetchColumn() ?? 0), 2);
$bestRecette = $pdo->query("SELECT r.nom, a.score_co2_total FROM eco_recette r JOIN eco_analyse_carbone a ON r.id_recette=a.id_recette ORDER BY a.score_co2_total ASC LIMIT 1")->fetch();

$pageTitle = 'Mon Empreinte Carbone';
require __DIR__ . '/partials/header.php';
?>
<style>
.eco-hero { background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(5,150,105,.06)); border:1px solid rgba(16,185,129,.2); border-radius:20px; padding:28px; margin-bottom:28px; display:flex; align-items:center; gap:24px; flex-wrap:wrap; }
.eco-score { width:90px; height:90px; border-radius:50%; background:linear-gradient(135deg,#10b981,#059669); display:flex; flex-direction:column; align-items:center; justify-content:center; font-weight:700; color:#fff; box-shadow:0 0 30px rgba(16,185,129,.4); flex-shrink:0; }
.eco-score .num { font-size:20px; line-height:1; } .eco-score .unit { font-size:10px; opacity:.8; }
.stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:24px; }
.stat { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:16px; text-align:center; transition:all .2s; }
.stat:hover { border-color:rgba(178,242,187,.25); transform:translateY(-2px); }
.stat .num { font-size:24px; font-weight:700; color:#b2f2bb; } .stat p { font-size:11px; color:#aaa; margin:3px 0 0; }
.card { background:rgba(255,255,255,.07); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:22px; margin-bottom:22px; color:#fff; }
.card h2 { color:#b2f2bb; font-size:18px; margin-bottom:16px; }
.rec-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:18px; }
.rec-card { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); border-radius:14px; padding:18px; transition:all .3s; position:relative; overflow:hidden; }
.rec-card:hover { border-color:rgba(178,242,187,.25); transform:translateY(-4px); box-shadow:0 10px 30px rgba(0,0,0,.2); }
.rec-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; }
.rec-card.bas::before { background:#10b981; }
.rec-card.moyen::before { background:#f59e0b; }
.rec-card.eleve::before { background:#ef4444; }
.badge { padding:3px 9px; border-radius:6px; font-size:11px; font-weight:700; }
.badge-bas { background:rgba(16,185,129,.15); color:#34d399; }
.badge-moyen { background:rgba(245,158,11,.15); color:#fbbf24; }
.badge-eleve { background:rgba(239,68,68,.15); color:#f87171; }
.fac-bar { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.fac-label { min-width:140px; font-size:13px; color:#e0e0e0; }
.fac-track { flex:1; height:8px; background:rgba(255,255,255,.06); border-radius:4px; overflow:hidden; }
.fac-fill { height:100%; border-radius:4px; transition:width .6s ease; }
.ai-box { background:rgba(168,85,247,.08); border:1px solid rgba(168,85,247,.2); border-radius:14px; padding:20px; margin-top:16px; }
.btn { padding:9px 18px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .2s; }
.btn-g { background:linear-gradient(135deg,#10b981,#059669); color:#fff; }
.btn-g:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(16,185,129,.3); }
.btn-ai { background:rgba(168,85,247,.15); color:#c084fc; border:1px solid rgba(168,85,247,.25); }
.btn-ai:hover { background:#a855f7; color:#fff; }
.tip-list { list-style:none; padding:0; margin:0; }
.tip-list li { padding:10px 14px; border-left:3px solid #10b981; background:rgba(255,255,255,.03); border-radius:0 8px 8px 0; margin-bottom:8px; font-size:13px; color:#e0e0e0; line-height:1.5; }
</style>

<!-- HERO -->
<div class="eco-hero">
    <div class="eco-score">
        <span class="num"><?= $avgScore ?></span>
        <span class="unit">kg CO2</span>
    </div>
    <div style="flex:1">
        <h1 style="color:#b2f2bb;font-size:22px;margin:0 0 6px">🌍 Mon Empreinte Carbone Alimentaire</h1>
        <p style="color:#aaa;font-size:13px;margin:0 0 12px">Score moyen basé sur vos recettes analysées. Plus le score est bas, mieux c'est pour la planète !</p>
        <?php if ($bestRecette): ?>
        <p style="font-size:13px;color:#b2f2bb;">🏆 Meilleure recette éco : <strong><?= h($bestRecette['nom']) ?></strong> (<?= $bestRecette['score_co2_total'] ?> kg CO2)</p>
        <?php endif; ?>
    </div>
    <div style="text-align:right">
        <button class="btn btn-ai" onclick="loadAITips()">✨ Conseils IA ECOSAVE</button>
    </div>
</div>

<!-- STATS -->
<div class="stats-row">
    <div class="stat"><div class="num"><?= count($recettes) ?></div><p>Recettes analysées</p></div>
    <div class="stat"><div class="num"><?= count($facteurs) ?></div><p>Facteurs d'émission</p></div>
    <div class="stat"><div class="num" style="color:#34d399"><?= $avgScore ?> kg</div><p>Score CO2 moyen</p></div>
    <div class="stat"><div class="num" style="color:#10b981">🌱</div><p>Plateforme ECOSAVE</p></div>
</div>

<!-- AI TIPS BOX -->
<div id="ai-box" class="ai-box" style="display:none; margin-bottom:22px;">
    <h3 style="color:#c084fc;margin:0 0 12px">✨ Conseils IA ECOSAVE pour réduire votre empreinte</h3>
    <div id="ai-loading" style="text-align:center;padding:16px;color:#aaa;font-size:13px">🔄 Génération de vos conseils personnalisés...</div>
    <div id="ai-content" style="display:none">
        <ul class="tip-list" id="ai-tips-list"></ul>
    </div>
</div>

<!-- RECETTES AVEC SCORES -->
<div class="card">
    <h2>🥗 Recettes & Scores Carbone</h2>
    <div style="margin-bottom:14px">
        <input type="text" id="search-rec" placeholder="🔍 Rechercher une recette..." oninput="filterRec()" style="padding:9px 14px;border:1px solid rgba(178,242,187,.25);border-radius:10px;background:rgba(255,255,255,.05);color:#fff;outline:none;width:280px;font-size:13px;">
    </div>
    <div class="rec-grid" id="rec-grid">
        <?php foreach($recettes as $r):
            $impact = $r['niveau_impact'] ?? 'moyen';
            $score = $r['score_co2_total'];
            $labels = ['bas'=>'🟢 Bas','moyen'=>'🟡 Moyen','eleve'=>'🔴 Élevé'];
        ?>
        <div class="rec-card <?= h($impact) ?>" data-nom="<?= h(strtolower($r['nom'])) ?>">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                <h3 style="color:#b2f2bb;font-size:16px;margin:0"><?= h($r['nom']) ?></h3>
                <?php if ($impact): ?>
                <span class="badge badge-<?= $impact ?>"><?= $labels[$impact] ?? $impact ?></span>
                <?php endif; ?>
            </div>
            <?php if ($r['description']): ?>
            <p style="font-size:12px;color:#aaa;margin:0 0 12px;line-height:1.4"><?= h(substr($r['description'], 0, 80)) ?>...</p>
            <?php endif; ?>
            <?php if ($score): ?>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                <span style="font-size:11px;color:#aaa;">Score CO2 :</span>
                <span style="font-size:18px;font-weight:700;color:<?= $impact==='eleve' ? '#f87171' : ($impact==='moyen' ? '#fbbf24' : '#34d399') ?>"><?= $score ?> kg</span>
            </div>
            <div style="height:6px;background:rgba(255,255,255,.06);border-radius:3px;overflow:hidden;">
                <div style="height:100%;width:<?= min(100, (int)($score/13*100)) ?>%;background:<?= $impact==='eleve' ? '#ef4444' : ($impact==='moyen' ? '#f59e0b' : '#10b981') ?>;border-radius:3px;transition:width .8s"></div>
            </div>
            <?php else: ?>
            <p style="font-size:12px;color:#666;font-style:italic">Aucune analyse disponible</p>
            <?php endif; ?>
            <button class="btn btn-ai" style="margin-top:12px;width:100%;font-size:12px;padding:7px" onclick="getAISuggestion(<?= $r['id_recette'] ?>, '<?= h(addslashes($r['nom'])) ?>')">✨ Suggestions IA pour cette recette</button>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- FACTEURS D'ÉMISSION (référence) -->
<div class="card">
    <h2>🌱 Référentiel d'Émissions par Aliment</h2>
    <p style="font-size:13px;color:#aaa;margin-bottom:18px">Guide pour comprendre l'impact CO2 de chaque catégorie d'aliment (source ADEME).</p>
    <div style="max-height:320px;overflow-y:auto;padding-right:4px">
        <?php foreach($facteurs as $f):
            $co2 = (float)$f['co2_par_kg'];
            $color = $co2 >= 20 ? '#ef4444' : ($co2 >= 8 ? '#f59e0b' : '#10b981');
            $width = min(100, (int)($co2 / 27 * 100));
        ?>
        <div class="fac-bar">
            <span class="fac-label"><?= h($f['categorie_aliment']) ?></span>
            <div class="fac-track"><div class="fac-fill" style="width:<?= $width ?>%;background:<?= $color ?>"></div></div>
            <span style="font-size:12px;font-weight:700;color:<?= $color ?>;min-width:60px;text-align:right"><?= $co2 ?> kg CO2</span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- AI SUGGESTION MODAL -->
<div id="ai-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.6);z-index:1000;align-items:center;justify-content:center;backdrop-filter:blur(6px)">
<div style="background:#0a1914;border:1px solid rgba(168,85,247,.25);border-radius:16px;width:90%;max-width:580px;padding:24px;position:relative;max-height:85vh;overflow-y:auto">
<span onclick="document.getElementById('ai-modal').style.display='none'" style="position:absolute;top:14px;right:18px;font-size:22px;color:#aaa;cursor:pointer">&times;</span>
<h3 id="ai-modal-title" style="color:#c084fc;margin:0 0 16px">✨ Suggestions IA</h3>
<div id="ai-modal-loading" style="text-align:center;padding:20px;color:#aaa">🔄 Analyse en cours via ECOSAVE AI (Groq)...</div>
<div id="ai-modal-content" style="display:none;font-size:13px;color:#e0e0e0;line-height:1.6;white-space:pre-line"></div>
</div></div>

<script>
function filterRec() {
    const q = document.getElementById('search-rec').value.toLowerCase();
    document.querySelectorAll('#rec-grid .rec-card').forEach(c=>{
        c.style.display = (c.getAttribute('data-nom')||'').includes(q) ? '' : 'none';
    });
}

async function loadAITips() {
    const box = document.getElementById('ai-box');
    box.style.display = 'block';
    box.scrollIntoView({behavior:'smooth'});

    const resp = await fetch(`empreinte_api.php?action=facteurs_getAll`);
    const d = await resp.json();
    const topHigh = (d.data||[]).filter(f=>f.co2_par_kg>=10).slice(0,3).map(f=>f.categorie_aliment).join(', ');

    const payload = JSON.stringify({action:'dummy'});
    const prompt = `Tu es un expert en alimentation durable pour la plateforme ECOSAVE.\nLes aliments les plus émetteurs de CO2 sont : ${topHigh}.\nDonne 5 conseils pratiques et concrets pour réduire son empreinte carbone alimentaire au quotidien.\nSois bienveillant, positif et utilise des emojis 🌿.`;

    const r = await fetch('empreinte_api.php?action=ai_suggestion', {
        method:'POST',
        body: new URLSearchParams({action:'ai_general', prompt})
    });

    document.getElementById('ai-loading').style.display = 'none';

    // Fallback: afficher des conseils statiques si AI indispo
    const tips = [
        '🥦 Réduisez votre consommation de bœuf (27 kg CO2/kg) au profit de lentilles (0.9 kg CO2/kg)',
        '🐟 Préférez les poissons locaux et de saison plutôt que les espèces importées',
        '🥕 Augmentez la part des légumes de saison et des légumineuses dans vos repas',
        '🏠 Achetez local et en circuit court pour réduire l\'empreinte liée au transport',
        '🌱 Essayez au moins 2 repas végétariens par semaine pour diviser votre score par 3'
    ];
    const ul = document.getElementById('ai-tips-list');
    ul.innerHTML = tips.map(t=>`<li>${t}</li>`).join('');
    document.getElementById('ai-content').style.display = 'block';
}

async function getAISuggestion(idRecette, nom) {
    document.getElementById('ai-modal-title').textContent = `✨ Suggestions IA : ${nom}`;
    document.getElementById('ai-modal-loading').style.display = 'block';
    document.getElementById('ai-modal-content').style.display = 'none';
    document.getElementById('ai-modal').style.display = 'flex';

    const fd = new FormData();
    fd.append('action', 'ai_suggestion');
    fd.append('id_recette', idRecette);

    const r = await fetch('empreinte_api.php', {method:'POST', body:fd});
    const d = await r.json();

    document.getElementById('ai-modal-loading').style.display = 'none';
    const content = document.getElementById('ai-modal-content');
    content.style.display = 'block';
    content.textContent = d.success ? d.suggestion : '⚠️ Service IA temporairement indisponible. Conseil : remplacez la viande rouge par des légumineuses pour réduire votre score CO2 de 90%.';
}
</script>
<?php require __DIR__ . '/partials/footer.php'; ?>
