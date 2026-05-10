<?php
declare(strict_types=1);
session_start();
if (($_SESSION['role'] ?? '') !== 'admin') { header('Location: login.php'); exit; }
if (!function_exists('h')) { function h(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); } }
require_once __DIR__ . '/../config/config.php';
$pdo = config::getConnexion();

// Export PDF si demandé
Metier::repondreExportPdfSiDemande('facteurs');
Metier::repondreExportPdfSiDemande('analyses');

$m = new Metier();
$terme = Metier::termeBarreDepuisGet($_GET);
$triFac = Metier::triFacteurDepuisGet($_GET);
$triAna = Metier::triAnalyseDepuisGet($_GET);

$totalRec = (int) $pdo->query("SELECT COUNT(*) FROM eco_recette")->fetchColumn();
$totalFac = (int) $pdo->query("SELECT COUNT(*) FROM eco_facteur_emission")->fetchColumn();
$totalAna = (int) $pdo->query("SELECT COUNT(*) FROM eco_analyse_carbone")->fetchColumn();
$totalGastro = (int) $pdo->query("SELECT COUNT(*) FROM rec_recette")->fetchColumn();
$totalProduits = (int) $pdo->query("SELECT COUNT(*) FROM produit")->fetchColumn();
$avgScore = round((float) ($pdo->query("SELECT AVG(score_co2_total) FROM eco_analyse_carbone")->fetchColumn() ?? 0), 2);

$recettes = $pdo->query("SELECT * FROM eco_recette ORDER BY nom ASC")->fetchAll();
$facteurs = $pdo->query("SELECT * FROM eco_facteur_emission ORDER BY co2_par_kg DESC")->fetchAll();
$analyses = $pdo->query("SELECT a.*, r.nom as nom_recette FROM eco_analyse_carbone a LEFT JOIN eco_recette r ON a.id_recette=r.id_recette ORDER BY a.date_calcul DESC")->fetchAll();

// Données pour le graphique : Répartition par impact
$impactDist = $pdo->query("SELECT niveau_impact as label, COUNT(*) as value FROM eco_analyse_carbone GROUP BY niveau_impact")->fetchAll();

$pageTitle = 'Gestion Empreinte Carbone';
require __DIR__ . '/partials/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.tab-nav { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:24px; }
.tab-btn { padding:10px 20px; border-radius:999px; border:1px solid rgba(178,242,187,.25); background:rgba(178,242,187,.08); color:#b2f2bb; font-weight:600; font-size:13px; cursor:pointer; transition:all .2s; }
.tab-btn.active, .tab-btn:hover { background:#b2f2bb; color:#0a3d2a; }
.tab-section { display:none; } .tab-section.active { display:block; animation:fadeIn .3s ease; }
@keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
.card { background:rgba(255,255,255,.08); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,.12); border-radius:16px; padding:22px; margin-bottom:22px; color:#fff; }
.card h2 { color:#b2f2bb; font-size:20px; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
.stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:24px; }
.stat { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); border-radius:12px; padding:18px; text-align:center; }
.stat .num { font-size:28px; font-weight:700; color:#b2f2bb; } .stat p { font-size:12px; color:#aaa; margin:4px 0 0; }
.tbl { width:100%; border-collapse:collapse; color:#fff; font-size:13px; }
.tbl th { background:rgba(178,242,187,.06); color:#b2f2bb; padding:12px 14px; text-align:left; font-weight:600; }
.tbl td { padding:11px 14px; border-bottom:1px solid rgba(255,255,255,.04); }
.tbl tr:hover td { background:rgba(255,255,255,.03); }
.btn { padding:7px 13px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; border:none; transition:all .2s; display:inline-flex; align-items:center; gap:5px; }
.btn-g { background:linear-gradient(135deg,#10b981,#059669); color:#fff; }
.btn-g:hover { background:linear-gradient(135deg,#059669,#047857); transform:translateY(-1px); }
.btn-r { background:rgba(239,68,68,.15); color:#f87171; border:1px solid rgba(239,68,68,.25); }
.btn-r:hover { background:#ef4444; color:#fff; }
.btn-b { background:rgba(59,130,246,.15); color:#60a5fa; border:1px solid rgba(59,130,246,.25); }
.btn-b:hover { background:#3b82f6; color:#fff; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:12px; color:#b2f2bb; font-weight:600; margin-bottom:5px; }
.form-group input, .form-group select, .form-group textarea { width:100%; padding:10px 14px; background:rgba(255,255,255,.05); border:1px solid rgba(178,242,187,.25); border-radius:10px; color:#fff; outline:none; font-size:13px; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.alert { padding:11px 16px; border-radius:10px; font-size:13px; font-weight:600; margin-bottom:18px; }
.alert-s { background:rgba(16,185,129,.12); color:#34d399; border:1px solid rgba(16,185,129,.25); }
.alert-e { background:rgba(239,68,68,.12); color:#f87171; border:1px solid rgba(239,68,68,.25); }
.badge { padding:3px 9px; border-radius:6px; font-size:11px; font-weight:700; }
.badge-bas { background:rgba(16,185,129,.15); color:#34d399; } 
.badge-moyen { background:rgba(245,158,11,.15); color:#fbbf24; }
.badge-eleve { background:rgba(239,68,68,.15); color:#f87171; }
</style>

<div id="msg-area"></div>

<div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:20px; align-items: start; margin-bottom:24px;">
  <div>
    <div class="tab-nav" style="margin-bottom:16px;">
        <button class="tab-btn active" onclick="switchTab('recettes',this)">🥗 Recettes (<?= $totalRec ?>)</button>
        <button class="tab-btn" onclick="switchTab('facteurs',this)">🌱 Facteurs (<?= $totalFac ?>)</button>
        <button class="tab-btn" onclick="switchTab('analyses',this)">📊 Analyses (<?= $totalAna ?>)</button>
        <button class="tab-btn" onclick="switchTab('ajouter',this)">➕ Ajouter</button>
    </div>

    <div class="stats-row" style="margin-bottom:0;">
        <div class="stat"><div class="num"><?= $totalFac ?></div><p>Facteurs</p></div>
        <div class="stat"><div class="num"><?= $totalAna ?></div><p>Analyses</p></div>
        <div class="stat"><div class="num"><?= $totalGastro ?></div><p>Gastro</p></div>
        <div class="stat"><div class="num" style="color:#fbbf24"><?= $avgScore ?> kg</div><p>Score CO2</p></div>
    </div>
  </div>

  <div class="card" style="margin-bottom:0; height: 100%; display: flex; flex-direction: column; padding: 18px;">
    <h3 style="color:#b2f2bb;font-size:16px;margin-bottom:12px;font-weight:600">📊 Niveau d'Impact</h3>
    <div style="flex:1; position: relative; min-height: 180px;">
      <canvas id="impactChart"></canvas>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('impactChart').getContext('2d');
    const data = <?= json_encode($impactDist) ?>;
    const labelsMap = { 'bas': 'Bas (Écolo)', 'moyen': 'Moyen', 'eleve': 'Élevé' };
    const colorsMap = { 'bas': '#10b981', 'moyen': '#f59e0b', 'eleve': '#ef4444' };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => labelsMap[d.label] || d.label),
            datasets: [{
                data: data.map(d => d.value),
                backgroundColor: data.map(d => colorsMap[d.label] || '#3b82f6'),
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#aaa', font: { size: 11 }, padding: 10 }
                }
            },
            cutout: '70%'
        }
    });
});
</script>

<!-- RECETTES -->
<div id="tab-recettes" class="tab-section active">
<div class="card">
<h2>🥗 Recettes</h2>
<div style="margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
    <input type="text" id="search-rec" placeholder="🔍 Rechercher localement..." oninput="filterTable('tbl-rec','search-rec',[0,1])" style="padding:9px 14px;border:1px solid rgba(178,242,187,.25);border-radius:10px;background:rgba(255,255,255,.05);color:#fff;outline:none;width:280px;font-size:13px;">
    <button class="btn btn-g" onclick="switchTab('ajouter', document.querySelector('.tab-btn[onclick*=\'ajouter\']'))">➕ Ajouter une recette</button>
</div>
<div style="overflow-x:auto"><table class="tbl"><thead><tr><th>#</th><th>Nom</th><th>Description</th><th>Créée le</th><th>Actions</th></tr></thead><tbody id="tbl-rec">
<?php foreach($recettes as $i => $r): ?>
<tr data-0="<?= h(strtolower($r['nom'])) ?>" data-1="<?= h(strtolower($r['description'] ?? '')) ?>">
<td><?= $i+1 ?></td>
<td style="font-weight:600;color:#b2f2bb"><?= h($r['nom']) ?></td>
<td style="color:#ccc;max-width:260px"><?= h(substr($r['description'] ?? '', 0, 60)) ?>...</td>
<td><?= ($r['date_creation'] ?? null) ? date('d/m/Y', strtotime($r['date_creation'])) : 'N/A' ?></td>
<td><div style="display:flex;gap:6px">
<button class="btn btn-b" onclick="editRecette(<?= $r['id_recette'] ?>,'<?= h(addslashes($r['nom'])) ?>','<?= h(addslashes($r['description'] ?? '')) ?>')">✏️</button>
<button class="btn btn-r" onclick="deleteItem('recettes_supprimer',<?= $r['id_recette'] ?>)">🗑️</button>
</div></td></tr>
<?php endforeach; ?>
</tbody></table></div></div></div>

<!-- FACTEURS -->
<div id="tab-facteurs" class="tab-section">
<div class="card">
<h2>🌱 Facteurs d'Émission (kg CO2 / kg aliment)</h2>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:10px;flex-wrap:wrap">
  <form method="get" style="display:flex;gap:10px">
    <input type="hidden" name="tab" value="facteurs">
    <input type="text" name="q" value="<?= h($terme) ?>" placeholder="🔍 Rechercher..." style="padding:9px 14px;border:1px solid rgba(178,242,187,.25);border-radius:10px;background:rgba(255,255,255,.05);color:#fff;outline:none;width:280px;font-size:13px;">
    <button type="submit" class="btn btn-b">Filtrer</button>
    <button type="button" class="btn btn-g" onclick="switchTab('ajouter', document.querySelector('.tab-btn[onclick*=\'ajouter\']'))">➕ Nouveau Facteur</button>
  </form>
  <a href="?export=pdf&q=<?= rawurlencode($terme) ?>&tri=<?= h($triFac) ?>&page=facteurs" class="btn btn-p" target="_blank">📄 Exporter PDF</a>
</div>
<div style="overflow-x:auto"><table class="tbl"><thead><tr>
    <th><a href="?tri=categorie_aliment&q=<?= rawurlencode($terme) ?>&tab=facteurs" style="color:inherit;text-decoration:none">Catégorie <?= $triFac === 'categorie_aliment' ? '↓' : '↕' ?></a></th>
    <th><a href="?tri=co2_par_kg&q=<?= rawurlencode($terme) ?>&tab=facteurs" style="color:inherit;text-decoration:none">CO2 / kg <?= $triFac === 'co2_par_kg' ? '↓' : '↕' ?></a></th>
    <th>Source</th><th>Mis à jour</th><th>Actions</th></tr></thead><tbody id="tbl-fac">
<?php foreach($facteurs as $f): 
    $co2 = (float)$f['co2_par_kg'];
    $barColor = $co2 >= 20 ? '#ef4444' : ($co2 >= 8 ? '#f59e0b' : '#10b981');
?>
<tr data-0="<?= h(strtolower($f['categorie_aliment'])) ?>">
<td style="font-weight:600"><?= h($f['categorie_aliment']) ?></td>
<td>
  <div style="display:flex;align-items:center;gap:8px">
    <span style="color:<?= $barColor ?>;font-weight:700"><?= $co2 ?></span>
    <div style="height:6px;width:<?= min(100, (int)($co2/27*100)) ?>px;background:<?= $barColor ?>;border-radius:3px"></div>
  </div>
</td>
<td style="color:#aaa"><?= h($f['source_donnee'] ?? '') ?></td>
<td><?= $f['date_derniere_maj'] ? date('d/m/Y', strtotime($f['date_derniere_maj'])) : '-' ?></td>
<td><div style="display:flex;gap:6px">
<button class="btn btn-b" onclick="editFacteur(<?= $f['id_facteur'] ?>,'<?= h(addslashes($f['categorie_aliment'])) ?>',<?= $co2 ?>,'<?= h(addslashes($f['source_donnee'] ?? '')) ?>','<?= $f['date_derniere_maj'] ?? '' ?>')">✏️</button>
<button class="btn btn-r" onclick="deleteItem('facteurs_supprimer',<?= $f['id_facteur'] ?>)">🗑️</button>
</div></td></tr>
<?php endforeach; ?>
</tbody></table></div></div></div>

<!-- ANALYSES -->
<div id="tab-analyses" class="tab-section">
<div class="card">
<h2>📊 Analyses Carbone</h2>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:10px;flex-wrap:wrap">
  <form method="get" style="display:flex;gap:10px">
    <input type="hidden" name="tab" value="analyses">
    <input type="text" name="q" value="<?= h($terme) ?>" placeholder="🔍 Rechercher..." style="padding:9px 14px;border:1px solid rgba(178,242,187,.25);border-radius:10px;background:rgba(255,255,255,.05);color:#fff;outline:none;width:280px;font-size:13px;">
    <button type="submit" class="btn btn-b">Filtrer</button>
    <button type="button" class="btn btn-g" onclick="switchTab('ajouter', document.querySelector('.tab-btn[onclick*=\'ajouter\']'))">➕ Nouvelle Analyse</button>
  </form>
  <a href="?export=pdf&q=<?= rawurlencode($terme) ?>&tri=<?= h($triAna) ?>&page=analyses" class="btn btn-p" target="_blank">📄 Exporter PDF</a>
</div>
<div style="overflow-x:auto"><table class="tbl"><thead><tr>
    <th>Recette</th>
    <th><a href="?tri=score_co2_total&q=<?= rawurlencode($terme) ?>&tab=analyses" style="color:inherit;text-decoration:none">Score CO2 <?= $triAna === 'score_co2_total' ? '↓' : '↕' ?></a></th>
    <th><a href="?tri=niveau_impact&q=<?= rawurlencode($terme) ?>&tab=analyses" style="color:inherit;text-decoration:none">Impact <?= $triAna === 'niveau_impact' ? '↓' : '↕' ?></a></th>
    <th>Méthode</th>
    <th><a href="?tri=date_calcul&q=<?= rawurlencode($terme) ?>&tab=analyses" style="color:inherit;text-decoration:none">Date <?= $triAna === 'date_calcul' ? '↓' : '↕' ?></a></th>
    <th>Actions</th></tr></thead><tbody id="tbl-ana">
<?php foreach($analyses as $a): 
    $impact = $a['niveau_impact'] ?? 'moyen';
    $labels = ['bas'=>'🟢 Bas','moyen'=>'🟡 Moyen','eleve'=>'🔴 Élevé'];
?>
<tr data-0="<?= h(strtolower($a['nom_recette'] ?? '')) ?>" data-1="<?= h(strtolower($impact)) ?>" data-2="<?= h(strtolower($a['methode_calcul'] ?? '')) ?>">
<td style="font-weight:600;color:#b2f2bb"><?= h($a['nom_recette'] ?? 'N/A') ?></td>
<td style="font-weight:700;color:<?= $impact==='eleve' ? '#f87171' : ($impact==='moyen' ? '#fbbf24' : '#34d399') ?>"><?= $a['score_co2_total'] ?> kg</td>
<td><span class="badge badge-<?= $impact ?>"><?= $labels[$impact] ?? $impact ?></span></td>
<td style="color:#ccc"><?= h($a['methode_calcul'] ?? '') ?></td>
<td><?= $a['date_calcul'] ? date('d/m/Y', strtotime($a['date_calcul'])) : '-' ?></td>
<td><div style="display:flex;gap:6px">
<button class="btn btn-b" onclick="editAnalyse(<?= $a['id_analyse'] ?>,<?= $a['score_co2_total'] ?>,'<?= $impact ?>','<?= h(addslashes($a['methode_calcul'] ?? '')) ?>','<?= $a['date_calcul'] ?? date('Y-m-d') ?>',<?= $a['id_recette'] ?>)">✏️</button>
<button class="btn btn-r" onclick="deleteItem('analyses_supprimer',<?= $a['id_analyse'] ?>)">🗑️</button>
</div></td></tr>
<?php endforeach; ?>
</tbody></table></div></div></div>

<!-- AJOUTER -->
<div id="tab-ajouter" class="tab-section">
<div class="card">
<h2>🥗 Nouvelle Recette</h2>
<form onsubmit="ajouterItem(event,'recettes_creer')">
<div class="form-grid">
  <div class="form-group"><label>Nom de la recette *</label><input type="text" name="nom" required></div>
  <div class="form-group"><label>Description</label><input type="text" name="description"></div>
</div>
<button type="submit" class="btn btn-g">➕ Ajouter la recette</button>
</form>
</div>

<div class="card">
<h2>🌱 Nouveau Facteur d'Émission</h2>
<form onsubmit="ajouterItem(event,'facteurs_creer')">
<div class="form-grid">
  <div class="form-group"><label>Catégorie aliment *</label><input type="text" name="categorie_aliment" required></div>
  <div class="form-group"><label>CO2 par kg *</label><input type="number" name="co2_par_kg" step="0.01" required></div>
  <div class="form-group"><label>Source données</label><input type="text" name="source_donnee" placeholder="Ex: ADEME"></div>
  <div class="form-group"><label>Date dernière MAJ</label><input type="date" name="date_derniere_maj" value="<?= date('Y-m-d') ?>"></div>
</div>
<button type="submit" class="btn btn-g">➕ Ajouter le facteur</button>
</form>
</div>

<div class="card">
<h2>📊 Nouvelle Analyse Carbone</h2>
<form onsubmit="ajouterItem(event,'analyses_creer')">
<div class="form-grid">
  <div class="form-group"><label>Recette *</label>
    <select name="id_recette" required>
      <option value="">-- Choisir --</option>
      <?php foreach($recettes as $r): ?>
      <option value="<?= $r['id_recette'] ?>"><?= h($r['nom']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="form-group"><label>Score CO2 total *</label><input type="number" name="score_co2_total" step="0.01" required></div>
  <div class="form-group"><label>Niveau d'impact *</label>
    <select name="niveau_impact" required>
      <option value="bas">🟢 Bas</option>
      <option value="moyen" selected>🟡 Moyen</option>
      <option value="eleve">🔴 Élevé</option>
    </select>
  </div>
  <div class="form-group"><label>Méthode de calcul</label><input type="text" name="methode_calcul"></div>
  <div class="form-group"><label>Date de calcul</label><input type="date" name="date_calcul" value="<?= date('Y-m-d') ?>"></div>
</div>
<button type="submit" class="btn btn-g">➕ Ajouter l'analyse</button>
</form>
</div>
</div>

<!-- MODAL EDIT -->
<div id="edit-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.6);z-index:1000;align-items:center;justify-content:center;backdrop-filter:blur(6px)">
<div style="background:#0a1914;border:1px solid rgba(178,242,187,.2);border-radius:16px;width:90%;max-width:520px;padding:24px;position:relative">
<span onclick="document.getElementById('edit-modal').style.display='none'" style="position:absolute;top:14px;right:18px;font-size:22px;color:#aaa;cursor:pointer">&times;</span>
<h3 id="edit-modal-title" style="color:#b2f2bb;margin-bottom:20px">Modifier</h3>
<form id="edit-form" onsubmit="submitEdit(event)">
<input type="hidden" name="action" id="edit-action">
<input type="hidden" name="id" id="edit-id">
<div id="edit-fields"></div>
<button type="submit" class="btn btn-g" style="margin-top:8px">💾 Enregistrer</button>
</form>
</div></div>

<script>
const API = 'empreinte_api.php';

function switchTab(id, btn) {
    document.querySelectorAll('.tab-section').forEach(s=>s.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById('tab-'+id).classList.add('active');
    btn.classList.add('active');
}

function filterTable(tbodyId, inputId, cols) {
    const q = document.getElementById(inputId).value.toLowerCase();
    document.querySelectorAll('#'+tbodyId+' tr').forEach(tr=>{
        const match = cols.some(c=>(tr.getAttribute('data-'+c)||'').includes(q));
        tr.style.display = match ? '' : 'none';
    });
}

function showMsg(msg, ok) {
    const a = document.getElementById('msg-area');
    a.innerHTML = `<div class="alert ${ok?'alert-s':'alert-e'}">${ok?'✅':'⚠️'} ${msg}</div>`;
    setTimeout(()=>a.innerHTML='', 4000);
}

async function ajouterItem(e, action) {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('action', action);
    const r = await fetch(API, {method:'POST', body:fd});
    const d = await r.json();
    showMsg(d.message || (d.success?'Succès':'Erreur'), d.success);
    if (d.success) { e.target.reset(); setTimeout(()=>location.reload(), 1200); }
}

async function deleteItem(action, id) {
    customConfirm({
        icon: '🌱',
        title: 'Confirmer la suppression ?',
        message: 'Cet élément sera définitivement supprimé de la base de données.',
        labelOk: '🗑️ Supprimer',
        onConfirm: async () => {
            const fd = new FormData(); fd.append('action', action); fd.append('id', id);
            const r = await fetch(API, {method:'POST', body:fd});
            const d = await r.json();
            showMsg(d.message || (d.success?'Supprimé':'Erreur'), d.success);
            if (d.success) setTimeout(()=>location.reload(), 800);
        }
    });
}

function openEditModal(title, action, id, fields) {
    document.getElementById('edit-modal-title').textContent = title;
    document.getElementById('edit-action').value = action;
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-fields').innerHTML = fields;
    document.getElementById('edit-modal').style.display = 'flex';
}

function editRecette(id, nom, desc) {
    openEditModal('✏️ Modifier Recette', 'recettes_modifier', id,
        `<div class="form-group"><label>Nom</label><input name="nom" value="${nom}" required style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
         <div class="form-group"><label>Description</label><input name="description" value="${desc}" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>`);
}

function editFacteur(id, cat, co2, src, date) {
    openEditModal('✏️ Modifier Facteur', 'facteurs_modifier', id,
        `<div class="form-group"><label>Catégorie</label><input name="categorie_aliment" value="${cat}" required style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
         <div class="form-group"><label>CO2/kg</label><input name="co2_par_kg" type="number" step="0.01" value="${co2}" required style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
         <div class="form-group"><label>Source</label><input name="source_donnee" value="${src}" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
         <div class="form-group"><label>Date MAJ</label><input name="date_derniere_maj" type="date" value="${date}" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>`);
}

function editAnalyse(id, score, impact, methode, date, idR) {
    const recOpts = <?= json_encode(array_map(fn($r)=>['id'=>$r['id_recette'],'nom'=>$r['nom']], $recettes)) ?>;
    const recSel = recOpts.map(r=>`<option value="${r.id}" ${r.id==idR?'selected':''}>${r.nom}</option>`).join('');
    openEditModal('✏️ Modifier Analyse', 'analyses_modifier', id,
        `<div class="form-group"><label>Recette</label><select name="id_recette" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none">${recSel}</select></div>
         <div class="form-group"><label>Score CO2</label><input name="score_co2_total" type="number" step="0.01" value="${score}" required style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
         <div class="form-group"><label>Impact</label><select name="niveau_impact" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"><option value="bas" ${impact==='bas'?'selected':''}>🟢 Bas</option><option value="moyen" ${impact==='moyen'?'selected':''}>🟡 Moyen</option><option value="eleve" ${impact==='eleve'?'selected':''}>🔴 Élevé</option></select></div>
         <div class="form-group"><label>Méthode</label><input name="methode_calcul" value="${methode}" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
         <div class="form-group"><label>Date</label><input name="date_calcul" type="date" value="${date}" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>`);
}

async function submitEdit(e) {
    e.preventDefault();
    const fd = new FormData(document.getElementById('edit-form'));
    const r = await fetch(API, {method:'POST', body:fd});
    const d = await r.json();
    showMsg(d.message || (d.success?'Modifié':'Erreur'), d.success);
    if (d.success) { document.getElementById('edit-modal').style.display='none'; setTimeout(()=>location.reload(), 900); }
}

// ─── CHATBOT ───────────────────────────────────────────
const chatHistory = [];

function toggleChat() {
    const panel = document.getElementById('chat-panel');
    const isOpen = panel.style.display !== 'none';
    panel.style.display = isOpen ? 'none' : 'flex';
    if (!isOpen && chatHistory.length === 0) appendMsg('bot', '👋 Bonjour ! Je suis ECOSAVE Assistant. Posez-moi vos questions sur les recettes, facteurs d\'émission ou analyses carbone de la plateforme.');
}

function appendMsg(sender, text) {
    const box = document.getElementById('chat-messages');
    const isBot = sender === 'bot';
    const div = document.createElement('div');
    div.style.cssText = `display:flex;justify-content:${isBot?'flex-start':'flex-end'};margin-bottom:10px`;
    div.innerHTML = `<div style="max-width:80%;padding:10px 14px;border-radius:${isBot?'4px 14px 14px 14px':'14px 4px 14px 14px'};background:${isBot?'rgba(178,242,187,.1)':'rgba(16,185,129,.2)'};border:1px solid ${isBot?'rgba(178,242,187,.2)':'rgba(16,185,129,.3)'};font-size:13px;color:#e0e0e0;line-height:1.5;white-space:pre-line">${text}</div>`;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
    chatHistory.push({role: isBot?'assistant':'user', content: text});
}

async function sendChat() {
    const inp = document.getElementById('chat-input');
    const msg = inp.value.trim();
    if (!msg) return;
    inp.value = '';
    appendMsg('user', msg);

    const typingDiv = document.createElement('div');
    typingDiv.id = 'typing';
    typingDiv.style.cssText = 'display:flex;justify-content:flex-start;margin-bottom:10px';
    typingDiv.innerHTML = '<div style="padding:10px 14px;border-radius:4px 14px 14px 14px;background:rgba(178,242,187,.06);border:1px solid rgba(178,242,187,.12);font-size:13px;color:#888">🤔 Réflexion en cours...</div>';
    document.getElementById('chat-messages').appendChild(typingDiv);

    const fd = new FormData();
    fd.append('action', 'ai_chat');
    fd.append('message', msg);
    try {
        const r = await fetch(API, {method:'POST', body:fd});
        const d = await r.json();
        typingDiv.remove();
        appendMsg('bot', d.success ? d.reply : '⚠️ Service IA indisponible. Vérifiez votre connexion.');
    } catch(e) {
        typingDiv.remove();
        appendMsg('bot', '⚠️ Erreur réseau. Veuillez réessayer.');
    }
}

document.addEventListener('DOMContentLoaded', ()=>{
    document.getElementById('chat-input').addEventListener('keydown', e=>{ if (e.key==='Enter' && !e.shiftKey) { e.preventDefault(); sendChat(); } });
});
</script>

<!-- FLOATING CHAT BUTTON -->
<button onclick="toggleChat()" id="chat-fab" style="position:fixed;bottom:24px;right:24px;width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#a855f7,#7c3aed);border:none;color:#fff;font-size:24px;cursor:pointer;z-index:999;box-shadow:0 4px 20px rgba(168,85,247,.5);transition:all .3s;display:flex;align-items:center;justify-content:center" onmouseenter="this.style.transform='scale(1.1)'" onmouseleave="this.style.transform='scale(1)'">🤖</button>

<!-- CHAT PANEL -->
<div id="chat-panel" style="display:none;position:fixed;bottom:92px;right:24px;width:360px;height:480px;background:#0a1914;border:1px solid rgba(168,85,247,.3);border-radius:16px;flex-direction:column;z-index:998;box-shadow:0 8px 40px rgba(0,0,0,.5);overflow:hidden">
    <div style="background:linear-gradient(135deg,rgba(168,85,247,.2),rgba(124,58,237,.1));padding:14px 18px;border-bottom:1px solid rgba(168,85,247,.2);display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:10px">
            <span style="font-size:20px">🤖</span>
            <div>
                <div style="color:#c084fc;font-weight:700;font-size:14px">ECOSAVE Assistant</div>
                <div style="color:#888;font-size:11px">Expert Empreinte Carbone • Groq AI</div>
            </div>
        </div>
        <div style="width:8px;height:8px;border-radius:50%;background:#10b981;box-shadow:0 0 6px #10b981"></div>
    </div>
    <div id="chat-messages" style="flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:2px"></div>
    <div style="padding:12px;border-top:1px solid rgba(255,255,255,.06);display:flex;gap:8px">
        <input id="chat-input" type="text" placeholder="Posez votre question..." style="flex:1;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(168,85,247,.25);border-radius:10px;color:#fff;outline:none;font-size:13px">
        <button onclick="sendChat()" style="padding:10px 14px;background:linear-gradient(135deg,#a855f7,#7c3aed);border:none;border-radius:10px;color:#fff;cursor:pointer;font-size:16px;transition:all .2s" onmouseenter="this.style.opacity='.85'" onmouseleave="this.style.opacity='1'">➤</button>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
