<?php
declare(strict_types=1);
session_start();
if (($_SESSION['role']??'')!=='admin'){header('Location: login.php');exit;}
if(!function_exists('h')){function h(?string $s):string{return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}}
require_once __DIR__ . '/../controller/metier.php';
$pdo = config::getConnexion();

Metier::repondreExportPdfSiDemande('recettes');

$m = new Metier();
$terme = Metier::termeBarreDepuisGet($_GET);
$tri = Metier::triRecetteDepuisGet($_GET);
$recettes = $m->rechercherRecettes($terme, $tri);

$total = (int) $pdo->query("SELECT COUNT(*) FROM rec_recette")->fetchColumn();
$avgCal = round((float) $pdo->query("SELECT AVG(calories_totales) FROM rec_recette")->fetchColumn());
$totalFacteurs = (int) $pdo->query("SELECT COUNT(*) FROM eco_facteur_emission")->fetchColumn();
$totalAnalyses = (int) $pdo->query("SELECT COUNT(*) FROM eco_analyse_carbone")->fetchColumn();

// Données pour le graphique : Répartition par difficulté
$diffDist = $pdo->query("SELECT difficulte as label, COUNT(*) as value FROM rec_recette GROUP BY difficulte")->fetchAll();

$pageTitle = 'Gestion des Recettes';
require __DIR__ . '/partials/header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.card{background:rgba(255,255,255,.08);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:22px;margin-bottom:22px;color:#fff;}
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:22px;}
.stat{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:16px;text-align:center;}
.stat .num{font-size:26px;font-weight:700;color:#b2f2bb;}.stat p{font-size:11px;color:#aaa;margin:3px 0 0}
.tab-nav{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:22px;}
.tab-btn{padding:9px 18px;border-radius:999px;border:1px solid rgba(178,242,187,.25);background:rgba(178,242,187,.08);color:#b2f2bb;font-weight:600;font-size:13px;cursor:pointer;transition:all .2s;}
.tab-btn.active,.tab-btn:hover{background:#b2f2bb;color:#0a3d2a;}
.tab-s{display:none;}.tab-s.active{display:block;animation:fi .3s ease}
@keyframes fi{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.tbl{width:100%;border-collapse:collapse;color:#fff;font-size:13px;}
.tbl th{background:rgba(178,242,187,.06);color:#b2f2bb;padding:11px 14px;text-align:left;font-weight:600;}
.tbl td{padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.04);}
.tbl tr:hover td{background:rgba(255,255,255,.03);}
.btn{padding:7px 12px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:none;transition:all .2s;display:inline-flex;align-items:center;gap:4px;}
.btn-g{background:linear-gradient(135deg,#10b981,#059669);color:#fff;}.btn-g:hover{transform:translateY(-1px);}
.btn-r{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.25);}.btn-r:hover{background:#ef4444;color:#fff;}
.btn-b{background:rgba(59,130,246,.15);color:#60a5fa;border:1px solid rgba(59,130,246,.25);}.btn-b:hover{background:#3b82f6;color:#fff;}
.btn-p{background:rgba(168,85,247,.15);color:#c084fc;border:1px solid rgba(168,85,247,.25);}.btn-p:hover{background:#a855f7;color:#fff;}
.fg{margin-bottom:13px;}.fg label{display:block;font-size:12px;color:#b2f2bb;font-weight:600;margin-bottom:5px;}
.fg input,.fg select,.fg textarea{width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none;font-size:13px;}
.fg2{display:grid;grid-template-columns:1fr 1fr;gap:13px;}
.badge{padding:3px 8px;border-radius:5px;font-size:11px;font-weight:700;}
.b-f{background:rgba(16,185,129,.15);color:#34d399;}.b-m{background:rgba(245,158,11,.15);color:#fbbf24;}.b-d{background:rgba(239,68,68,.15);color:#f87171;}
.alert{padding:10px 15px;border-radius:9px;font-size:13px;font-weight:600;margin-bottom:16px;}
.al-s{background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25);}
.al-e{background:rgba(239,68,68,.12);color:#f87171;border:1px solid rgba(239,68,68,.25);}
.ai-box{background:rgba(168,85,247,.08);border:1px solid rgba(168,85,247,.2);border-radius:12px;padding:18px;margin-top:14px;}
.detail-row{display:flex;gap:8px;align-items:center;background:rgba(255,255,255,.03);border-radius:8px;padding:9px 12px;margin-bottom:7px;}
</style>

<div id="msg"></div>

<div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:20px; align-items: start;">
  <div>
    <div class="stats-row">
      <div class="stat"><div class="num"><?=$total?></div><p>Recettes Gastronomie</p></div>
      <div class="stat"><div class="num"><?=$totalFacteurs?></div><p>Facteurs d'Émission</p></div>
      <div class="stat"><div class="num"><?=$totalAnalyses?></div><p>Analyses Carbone</p></div>
      <div class="stat"><div class="num"><?=$avgCal?> kcal</div><p>Calories moyennes</p></div>
    </div>

    <div class="tab-nav">
      <button class="tab-btn active" onclick="sw('list',this)">🍽️ Recettes (<?=$total?>)</button>
      <button class="tab-btn" onclick="sw('add',this)">➕ Ajouter</button>
      <button class="tab-btn" onclick="sw('ai',this)">🤖 Générer IA</button>
    </div>
  </div>

  <div class="card" style="margin-bottom:0; height: 100%; display: flex; flex-direction: column;">
    <h3 style="color:#b2f2bb;font-size:16px;margin-bottom:14px;font-weight:600">📊 Difficulté des Recettes</h3>
    <div style="flex:1; position: relative; min-height: 200px;">
      <canvas id="diffChart"></canvas>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('diffChart').getContext('2d');
    const data = <?= json_encode($diffDist) ?>;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(d => d.label.charAt(0).toUpperCase() + d.label.slice(1)),
            datasets: [{
                data: data.map(d => d.value),
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
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
                    labels: { color: '#aaa', font: { size: 11 }, padding: 15 }
                }
            },
            cutout: '70%'
        }
    });
});
</script>

<div id="tab-list" class="tab-s active"><div class="card">
<h2 style="color:#b2f2bb;font-size:18px;margin-bottom:14px">🍽️ Toutes les Recettes</h2>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:10px;flex-wrap:wrap">
  <form method="get" style="display:flex;gap:10px">
    <input type="text" name="q" value="<?= h($terme) ?>" placeholder="🔍 Rechercher..." style="padding:9px 14px;border:1px solid rgba(178,242,187,.25);border-radius:10px;background:rgba(255,255,255,.05);color:#fff;outline:none;width:280px;font-size:13px;">
    <button type="submit" class="btn btn-b">Filtrer</button>
    <?php if ($terme !== ''): ?>
        <a href="recettes_admin.php" class="btn btn-r">Annuler</a>
    <?php endif; ?>
  </form>
  
  <a href="?export=pdf&q=<?= rawurlencode($terme) ?>&tri=<?= h($tri) ?>" class="btn-export-pdf" target="_blank">📄 Exporter PDF</a>
</div>

<div style="overflow-x:auto">
<table class="tbl">
  <thead>
    <tr>
      <th>#</th>
      <th><a href="?tri=nom&q=<?= rawurlencode($terme) ?>" style="color:inherit;text-decoration:none">Nom <?= $tri === 'nom' ? '↓' : '↕' ?></a></th>
      <th><a href="?tri=difficulte&q=<?= rawurlencode($terme) ?>" style="color:inherit;text-decoration:none">Difficulté <?= $tri === 'difficulte' ? '↓' : '↕' ?></a></th>
      <th>⏱ Temps</th>
      <th>🔥 Cal</th>
      <th>👥 Pers.</th>
      <th><a href="?tri=date_creation&q=<?= rawurlencode($terme) ?>" style="color:inherit;text-decoration:none">Date <?= $tri === 'date_creation' ? '↓' : '↕' ?></a></th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody id="tbody">
<?php foreach($recettes as $i=>$r): $dc=['facile'=>'b-f','moyen'=>'b-m','difficile'=>'b-d'][$r['difficulte']]??'b-m'; ?>
<tr data-nom="<?=h(strtolower($r['nom']))?>">
<td><?=$i+1?></td>
<td style="font-weight:600;color:#b2f2bb"><?=h($r['nom'])?></td>
<td><?= h($r['difficulte']) ?></td>
<td><?= (int)$r['temps_preparation'] + (int)$r['temps_cuisson'] ?> min</td>
<td><?= (int)$r['calories_totales'] ?> kcal</td>
<td><?= (int)$r['nombre_personnes'] ?> pers.</td>
<td><?= h($r['date_creation'] ?? '') ?></td>
<td><div style="display:flex;gap:5px">
  <button class="btn btn-p" onclick="openDetails(<?=$r['id_recette']?>,'<?=h(addslashes($r['nom']))?>')">📋</button>
  <button class="btn btn-b" onclick="openEdit(<?=$r['id_recette']?>,'<?=h(addslashes($r['nom']))?>')">✏️</button>
  <button class="btn btn-r" onclick="del(<?=$r['id_recette']?>)">🗑️</button>
</div></td></tr>
<?php endforeach;?></tbody></table></div></div></div>

<!-- ADD -->
<div id="tab-add" class="tab-s"><div class="card">
<h2 style="color:#b2f2bb;font-size:18px;margin-bottom:16px">➕ Nouvelle Recette</h2>
<form onsubmit="addRec(event)">
<div class="fg2">
  <div class="fg"><label>Nom *</label><input name="nom" required></div>
  <div class="fg"><label>Difficulté</label><select name="difficulte"><option value="facile">🟢 Facile</option><option value="moyen" selected>🟡 Moyen</option><option value="difficile">🔴 Difficile</option></select></div>
  <div class="fg"><label>Personnes</label><input name="nombre_personnes" type="number" value="2" min="1"></div>
  <div class="fg"><label>Prep (min)</label><input name="temps_preparation" type="number" value="0" min="0"></div>
  <div class="fg"><label>Cuisson (min)</label><input name="temps_cuisson" type="number" value="0" min="0"></div>
  <div class="fg"><label>Calories</label><input name="calories_totales" type="number" value="0" min="0"></div>
</div>
<div class="fg"><label>Description</label><textarea name="description" rows="3"></textarea></div>
<div class="fg"><label>URL Image (optionnel)</label><input name="image_url" type="url" placeholder="https://..."></div>
<button type="submit" class="btn btn-g" style="padding:10px 22px;font-size:14px">➕ Créer la recette</button>
</form></div></div>

<!-- AI -->
<div id="tab-ai" class="tab-s"><div class="card">
<h2 style="color:#c084fc;font-size:18px;margin-bottom:16px">🤖 Génération IA (Groq)</h2>
<p style="color:#aaa;font-size:13px;margin-bottom:18px">Entrez le nom d'un plat et laissez l'IA générer la recette complète automatiquement.</p>
<div style="display:flex;gap:10px;margin-bottom:16px">
  <input id="ai-nom" type="text" placeholder="Ex: Tarte Tatin, Shakshuka, Ramen..." style="flex:1;padding:11px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(168,85,247,.3);border-radius:10px;color:#fff;outline:none;font-size:13px">
  <button onclick="genAI()" class="btn btn-p" style="padding:11px 20px;font-size:13px">🤖 Générer</button>
</div>
<div id="ai-result" style="display:none" class="ai-box">
  <div id="ai-loading" style="text-align:center;padding:14px;color:#aaa">🔄 Génération en cours...</div>
  <div id="ai-content" style="display:none">
    <h3 id="ai-title" style="color:#c084fc;margin:0 0 12px"></h3>
    <div class="fg2">
      <div><span style="font-size:12px;color:#aaa">Difficulté</span><div id="ai-diff" style="font-weight:600;margin-top:3px"></div></div>
      <div><span style="font-size:12px;color:#aaa">Calories</span><div id="ai-cal" style="font-weight:600;margin-top:3px"></div></div>
      <div><span style="font-size:12px;color:#aaa">Prep + Cuisson</span><div id="ai-time" style="font-weight:600;margin-top:3px"></div></div>
      <div><span style="font-size:12px;color:#aaa">Personnes</span><div id="ai-pers" style="font-weight:600;margin-top:3px"></div></div>
    </div>
    <p id="ai-desc" style="font-size:13px;color:#e0e0e0;margin:12px 0"></p>
    <h4 style="color:#b2f2bb;margin-bottom:8px">🥕 Ingrédients</h4>
    <ul id="ai-ings" style="color:#ccc;font-size:13px;padding-left:16px;margin-bottom:12px"></ul>
    <h4 style="color:#b2f2bb;margin-bottom:8px">📋 Étapes</h4>
    <ol id="ai-steps" style="color:#ccc;font-size:13px;padding-left:16px"></ol>
    <div style="display:flex;gap:10px;align-items:center;margin-top:14px;flex-wrap:wrap">
      <button onclick="saveAI()" class="btn btn-g" style="padding:10px 20px">💾 Enregistrer dans la base</button>
      <button onclick="genImage()" class="btn btn-p" style="padding:10px 20px" id="btn-img">🖼️ Générer Image IA</button>
    </div>
    <div id="ai-img-box" style="display:none;margin-top:14px;text-align:center">
      <div id="ai-img-loading" style="color:#aaa;font-size:13px;padding:10px">⏳ Génération de l'image...</div>
      <img id="ai-img-preview" src="" alt="" style="display:none;max-width:100%;border-radius:12px;border:1px solid rgba(168,85,247,.3);margin-top:8px">
      <p id="ai-img-prompt" style="font-size:11px;color:#888;margin-top:6px;font-style:italic"></p>
    </div>
  </div>
</div>
</div></div>

<!-- MODAL DETAILS -->
<div id="det-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.65);z-index:1000;align-items:center;justify-content:center;backdrop-filter:blur(6px)">
<div style="background:#0a1914;border:1px solid rgba(178,242,187,.2);border-radius:16px;width:90%;max-width:580px;padding:24px;position:relative;max-height:85vh;overflow-y:auto">
<span onclick="document.getElementById('det-modal').style.display='none'" style="position:absolute;top:14px;right:18px;font-size:22px;color:#aaa;cursor:pointer">&times;</span>
<h3 id="det-title" style="color:#b2f2bb;margin:0 0 16px"></h3>
<div id="det-list"></div>
<div style="margin-top:16px;border-top:1px solid rgba(255,255,255,.06);padding-top:14px">
  <h4 style="color:#b2f2bb;margin-bottom:10px">➕ Ajouter un ingrédient/étape</h4>
  <input type="hidden" id="det-rid">
  <div class="fg2" style="margin-bottom:10px">
    <div class="fg"><label>Ingrédient</label><input id="det-ing" placeholder="Ex: Tomates"></div>
    <div class="fg"><label>Quantité</label><input id="det-qte" placeholder="Ex: 200g"></div>
  </div>
  <div class="fg"><label>Étape / Instructions</label><textarea id="det-etape" rows="2" style="width:100%;padding:9px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none;font-size:13px"></textarea></div>
  <button onclick="addDetail()" class="btn btn-g">➕ Ajouter</button>
</div>
</div></div>

<!-- MODAL EDIT -->
<div id="edit-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.65);z-index:1000;align-items:center;justify-content:center;backdrop-filter:blur(6px)">
<div style="background:#0a1914;border:1px solid rgba(178,242,187,.2);border-radius:16px;width:90%;max-width:520px;padding:24px;position:relative">
<span onclick="document.getElementById('edit-modal').style.display='none'" style="position:absolute;top:14px;right:18px;font-size:22px;color:#aaa;cursor:pointer">&times;</span>
<h3 style="color:#b2f2bb;margin:0 0 16px">✏️ Modifier Recette</h3>
<form onsubmit="saveEdit(event)">
<input type="hidden" id="e-id">
<div class="fg"><label>Nom</label><input id="e-nom" required style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
<div class="fg2">
<div class="fg"><label>Difficulté</label><select id="e-diff" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"><option value="facile">Facile</option><option value="moyen">Moyen</option><option value="difficile">Difficile</option></select></div>
<div class="fg"><label>Personnes</label><input id="e-pers" type="number" min="1" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
<div class="fg"><label>Prep (min)</label><input id="e-prep" type="number" min="0" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
<div class="fg"><label>Cuisson (min)</label><input id="e-cuis" type="number" min="0" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
<div class="fg"><label>Calories</label><input id="e-cal" type="number" min="0" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(178,242,187,.25);border-radius:10px;color:#fff;outline:none"></div>
</div>
<button type="submit" class="btn btn-g">💾 Enregistrer</button>
</form></div></div>

<script>
const API='recettes_api.php';
let aiData=null, currentRid=0;

function sw(id,btn){document.querySelectorAll('.tab-s').forEach(s=>s.classList.remove('active'));document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));document.getElementById('tab-'+id).classList.add('active');btn.classList.add('active');}
function msg(m,ok){const a=document.getElementById('msg');a.innerHTML=`<div class="alert ${ok?'al-s':'al-e'}">${ok?'✅':'⚠️'} ${m}</div>`;setTimeout(()=>a.innerHTML='',4000);}

async function addRec(e){e.preventDefault();const fd=new FormData(e.target);fd.append('action','recettes_creer');const r=await fetch(API,{method:'POST',body:fd});const d=await r.json();msg(d.message||(d.success?'OK':'Erreur'),d.success);if(d.success){e.target.reset();setTimeout(()=>location.reload(),1200);}}
async function del(id){
    customConfirm({
        icon: '🍽️',
        title: 'Supprimer cette recette ?',
        message: 'Cette recette sera définitivement supprimée. Cette action est irréversible.',
        labelOk: '🗑️ Supprimer',
        onConfirm: async () => {
            const fd=new FormData();fd.append('action','recettes_supprimer');fd.append('id',id);
            const r=await fetch(API,{method:'POST',body:fd});const d=await r.json();
            msg(d.message,d.success);if(d.success)setTimeout(()=>location.reload(),800);
        }
    });
}

async function openEdit(id,nom){
  const r=await fetch(`${API}?action=recettes_getOne&id=${id}`);const d=await r.json();const rec=d.recette||{};
  document.getElementById('e-id').value=id;document.getElementById('e-nom').value=rec.nom||nom;
  document.getElementById('e-diff').value=rec.difficulte||'moyen';document.getElementById('e-pers').value=rec.nombre_personnes||2;
  document.getElementById('e-prep').value=rec.temps_preparation||0;document.getElementById('e-cuis').value=rec.temps_cuisson||0;
  document.getElementById('e-cal').value=rec.calories_totales||0;
  document.getElementById('edit-modal').style.display='flex';
}
async function saveEdit(e){e.preventDefault();const fd=new FormData();
  fd.append('action','recettes_modifier');fd.append('id',document.getElementById('e-id').value);
  fd.append('nom',document.getElementById('e-nom').value);fd.append('difficulte',document.getElementById('e-diff').value);
  fd.append('nombre_personnes',document.getElementById('e-pers').value);fd.append('temps_preparation',document.getElementById('e-prep').value);
  fd.append('temps_cuisson',document.getElementById('e-cuis').value);fd.append('calories_totales',document.getElementById('e-cal').value);
  const r=await fetch(API,{method:'POST',body:fd});const d=await r.json();msg(d.message,d.success);
  if(d.success){document.getElementById('edit-modal').style.display='none';setTimeout(()=>location.reload(),900);}
}

async function openDetails(id,nom){currentRid=id;document.getElementById('det-title').textContent='📋 '+nom;document.getElementById('det-rid').value=id;await loadDetails();}
async function loadDetails(){
  const r=await fetch(`${API}?action=details_getAll&id_recette=${currentRid}`);const d=await r.json();
  const el=document.getElementById('det-list');
  if(!d.data||!d.data.length){el.innerHTML='<p style="color:#aaa;font-size:13px">Aucun détail. Ajoutez des ingrédients ci-dessous.</p>';document.getElementById('det-modal').style.display='flex';return;}
  el.innerHTML=d.data.map(x=>`<div class="detail-row">
    <div style="flex:1"><span style="color:#b2f2bb;font-weight:600">${x.ingredient||''}</span> <span style="color:#fbbf24;font-size:12px">${x.quantite||''}</span>${x.etape?`<div style="font-size:12px;color:#ccc;margin-top:3px">${x.etape}</div>`:''}
    </div><button class="btn btn-r" onclick="delDetail(${x.id_detail})">🗑️</button></div>`).join('');
  document.getElementById('det-modal').style.display='flex';
}
async function addDetail(){
  const fd=new FormData();fd.append('action','details_ajouter');fd.append('id_recette',currentRid);
  fd.append('ingredient',document.getElementById('det-ing').value);fd.append('quantite',document.getElementById('det-qte').value);fd.append('etape',document.getElementById('det-etape').value);
  const r=await fetch(API,{method:'POST',body:fd});const d=await r.json();
  if(d.success){document.getElementById('det-ing').value='';document.getElementById('det-qte').value='';document.getElementById('det-etape').value='';await loadDetails();}
}
async function delDetail(id){const fd=new FormData();fd.append('action','details_supprimer');fd.append('id_detail',id);await fetch(API,{method:'POST',body:fd});await loadDetails();}

async function genAI(){
  const nom=document.getElementById('ai-nom').value.trim();if(!nom)return;
  document.getElementById('ai-result').style.display='block';document.getElementById('ai-loading').style.display='block';document.getElementById('ai-content').style.display='none';
  const fd=new FormData();fd.append('action','ai_generer');fd.append('nom_recette',nom);
  const r=await fetch(API,{method:'POST',body:fd});const d=await r.json();
  document.getElementById('ai-loading').style.display='none';
  if(!d.success){msg(d.message||'Erreur IA',false);document.getElementById('ai-result').style.display='none';return;}
  aiData=d.data;aiData.nom=nom;
  document.getElementById('ai-title').textContent='🍽️ '+nom;
  document.getElementById('ai-diff').textContent=aiData.difficulte||'moyen';
  document.getElementById('ai-cal').textContent=(aiData.calories_totales||0)+' kcal';
  document.getElementById('ai-time').textContent=(aiData.temps_preparation||0)+'+'+(aiData.temps_cuisson||0)+' min';
  document.getElementById('ai-pers').textContent=(aiData.nombre_personnes||4)+' pers.';
  document.getElementById('ai-desc').textContent=aiData.description||'';
  document.getElementById('ai-ings').innerHTML=(aiData.ingredients||[]).map(i=>`<li>${i.ingredient||i} — <em>${i.quantite||''}</em></li>`).join('');
  document.getElementById('ai-steps').innerHTML=(aiData.etapes||[]).map(e=>`<li style="margin-bottom:5px">${e}</li>`).join('');
  document.getElementById('ai-content').style.display='block';
}

async function saveAI(){
  if(!aiData)return;
  const fd=new FormData();fd.append('action','recettes_creer');fd.append('nom',aiData.nom);fd.append('description',aiData.description||'');
  fd.append('nombre_personnes',aiData.nombre_personnes||4);fd.append('temps_preparation',aiData.temps_preparation||0);
  fd.append('temps_cuisson',aiData.temps_cuisson||0);fd.append('difficulte',aiData.difficulte||'moyen');
  fd.append('calories_totales',aiData.calories_totales||0);
  if(aiData.image_url) fd.append('image_url', aiData.image_url);
  const ings=(aiData.ingredients||[]).map(i=>({ingredient:i.ingredient||i,quantite:i.quantite||'',etape:''}));
  const etapes=aiData.etapes||[];etapes.forEach((e,i)=>{if(ings[i])ings[i].etape=e;else ings.push({ingredient:'',quantite:'',etape:e});});
  fd.append('ingredients',JSON.stringify(ings));
  const r=await fetch(API,{method:'POST',body:fd});const d=await r.json();msg(d.message,d.success);
  if(d.success)setTimeout(()=>location.reload(),1200);
}

async function genImage(){
  if(!aiData||!aiData.nom){msg('Générez d\'abord une recette',false);return;}
  const btn=document.getElementById('btn-img');
  btn.disabled=true;btn.textContent='⏳ Génération...';
  document.getElementById('ai-img-box').style.display='block';
  document.getElementById('ai-img-loading').style.display='block';
  document.getElementById('ai-img-preview').style.display='none';
  const fd=new FormData();fd.append('action','ai_image');fd.append('nom_recette',aiData.nom);
  const r=await fetch(API,{method:'POST',body:fd});const d=await r.json();
  btn.disabled=false;btn.textContent='🖼️ Générer Image IA';
  document.getElementById('ai-img-loading').style.display='none';
  if(d.success){
    const img=document.getElementById('ai-img-preview');
    img.src=d.image_url;img.style.display='block';
    document.getElementById('ai-img-prompt').textContent='Prompt: '+d.debug_prompt;
    // Store URL so saveAI() can use it
    aiData.image_url=d.image_url;
    msg('Image générée avec succès ! 🎉',true);
  } else { msg(d.message||'Erreur image',false); }
}
</script>
<?php require __DIR__.'/partials/footer.php';?>
