<?php
declare(strict_types=1);
session_start();
if(!isset($_SESSION['user_id'])){header('Location: login.php');exit;}
if(!function_exists('h')){function h(?string $s):string{return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}}
require_once __DIR__.'/../config/config.php';
$pdo=config::getConnexion();
$recettes=$pdo->query("SELECT r.*,(SELECT COUNT(*) FROM rec_detail_recette d WHERE d.id_recette=r.id_recette) as nb_details FROM rec_recette r ORDER BY date_creation DESC")->fetchAll();
$pageTitle='Mes Recettes';
require __DIR__.'/partials/header.php';
?>
<style>
.rec-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;margin-top:18px;}
.rec-card{background:rgba(255,255,255,.07);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.1);border-radius:16px;overflow:hidden;transition:all .3s;cursor:pointer;}
.rec-card:hover{transform:translateY(-5px);border-color:rgba(178,242,187,.3);box-shadow:0 12px 35px rgba(16,185,129,.18);}
.rec-img{width:100%;height:160px;object-fit:cover;background:linear-gradient(135deg,#0a3d2a,#059669);display:flex;align-items:center;justify-content:center;font-size:52px;}
.rec-body{padding:18px;}
.badge{padding:3px 8px;border-radius:5px;font-size:11px;font-weight:700;}
.b-f{background:rgba(16,185,129,.15);color:#34d399;}.b-m{background:rgba(245,158,11,.15);color:#fbbf24;}.b-d{background:rgba(239,68,68,.15);color:#f87171;}
.meta{font-size:11px;color:#aaa;display:flex;gap:12px;margin:8px 0;}
.det-panel{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.65);z-index:1000;align-items:center;justify-content:center;backdrop-filter:blur(6px);}
.det-box{background:#0a1914;border:1px solid rgba(178,242,187,.2);border-radius:16px;width:90%;max-width:600px;padding:24px;position:relative;max-height:88vh;overflow-y:auto;}
.ing-item{display:flex;gap:10px;padding:9px 12px;border-radius:8px;background:rgba(255,255,255,.03);margin-bottom:6px;font-size:13px;align-items:center;}
.step-item{padding:10px 14px;border-left:3px solid #10b981;background:rgba(255,255,255,.03);border-radius:0 8px 8px 0;margin-bottom:8px;font-size:13px;color:#e0e0e0;line-height:1.5;}
.btn{padding:8px 14px;border-radius:9px;font-size:12px;font-weight:600;cursor:pointer;border:none;transition:all .2s;}
.btn-g{background:linear-gradient(135deg,#10b981,#059669);color:#fff;}.btn-g:hover{transform:translateY(-1px);}
.btn-p{background:rgba(168,85,247,.15);color:#c084fc;border:1px solid rgba(168,85,247,.25);}.btn-p:hover{background:#a855f7;color:#fff;}
.ai-conseil{background:rgba(168,85,247,.08);border:1px solid rgba(168,85,247,.2);border-radius:12px;padding:16px;margin-top:14px;font-size:13px;color:#e0e0e0;line-height:1.6;white-space:pre-line;display:none;}
</style>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px">
  <h1 style="color:#fff;font-size:24px;font-weight:700;margin:0">🍽️ Livre de Recettes</h1>
  <div style="display:flex;gap:10px">
    <input id="srch" type="text" placeholder="🔍 Rechercher..." oninput="filterR()" style="padding:9px 15px;border:1px solid rgba(178,242,187,.25);border-radius:10px;background:rgba(255,255,255,.05);color:#fff;outline:none;font-size:13px;width:220px">
    <select id="fdiff" onchange="filterR()" style="padding:9px 13px;border:1px solid rgba(178,242,187,.25);border-radius:10px;background:rgba(0,20,10,.8);color:#fff;outline:none;font-size:13px">
      <option value="">Toutes</option><option value="facile">🟢 Facile</option><option value="moyen">🟡 Moyen</option><option value="difficile">🔴 Difficile</option>
    </select>
  </div>
</div>

<div class="rec-grid" id="grid">
<?php if(empty($recettes)):?>
<p style="color:#aaa;text-align:center;grid-column:1/-1">Aucune recette disponible.</p>
<?php else: foreach($recettes as $r):
  $dc=['facile'=>'b-f','moyen'=>'b-m','difficile'=>'b-d'][$r['difficulte']]??'b-m';
  $total=(int)$r['temps_preparation']+(int)$r['temps_cuisson'];
?>
<div class="rec-card" data-nom="<?=h(strtolower($r['nom']))?>" data-diff="<?=h($r['difficulte'])?>" onclick="openRec(<?=$r['id_recette']?>,'<?=h(addslashes($r['nom']))?>')">
  <?php if($r['image_url']):?>
    <img src="<?=h($r['image_url'])?>" alt="<?=h($r['nom'])?>" style="width:100%;height:160px;object-fit:cover">
  <?php else:?>
    <div class="rec-img">🍽️</div>
  <?php endif;?>
  <div class="rec-body">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
      <h3 style="color:#b2f2bb;font-size:16px;font-weight:700;margin:0;flex:1"><?=h($r['nom'])?></h3>
      <span class="badge <?=$dc?>"><?=h($r['difficulte'])?></span>
    </div>
    <?php if($r['description']):?>
    <p style="font-size:12px;color:#aaa;margin:0 0 10px;line-height:1.4"><?=h(substr($r['description'],0,80))?>...</p>
    <?php endif;?>
    <div class="meta">
      <?php if($total):?><span>⏱ <?=$total?> min</span><?php endif;?>
      <?php if($r['calories_totales']):?><span>🔥 <?=(int)$r['calories_totales']?> kcal</span><?php endif;?>
      <?php if($r['nombre_personnes']):?><span>👥 <?=(int)$r['nombre_personnes']?> pers.</span><?php endif;?>
      <?php if($r['nb_details']):?><span>📋 <?=(int)$r['nb_details']?> détails</span><?php endif;?>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,.06);padding-top:10px;display:flex;justify-content:flex-end">
      <span style="font-size:12px;color:#b2f2bb;font-weight:600">Voir la recette →</span>
    </div>
  </div>
</div>
<?php endforeach; endif;?>
</div>

<!-- DETAIL PANEL -->
<div id="det-panel" class="det-panel">
<div class="det-box">
<span onclick="close_()" style="position:absolute;top:14px;right:18px;font-size:22px;color:#aaa;cursor:pointer">&times;</span>
<div id="det-loading" style="text-align:center;padding:20px;color:#aaa">🔄 Chargement...</div>
<div id="det-content" style="display:none">
  <div style="margin-bottom:14px">
    <h2 id="d-nom" style="color:#b2f2bb;font-size:20px;margin:0 0 6px"></h2>
    <div class="meta" id="d-meta"></div>
    <p id="d-desc" style="font-size:13px;color:#ccc;line-height:1.5;margin:0 0 14px"></p>
  </div>
  <div id="d-ings-block" style="margin-bottom:16px">
    <h4 style="color:#b2f2bb;margin-bottom:8px">🥕 Ingrédients</h4>
    <div id="d-ings"></div>
  </div>
  <div id="d-steps-block" style="margin-bottom:16px">
    <h4 style="color:#b2f2bb;margin-bottom:8px">📋 Étapes</h4>
    <div id="d-steps"></div>
  </div>
  <button class="btn btn-p" onclick="getConseil()" style="width:100%">✨ Conseils Chef IA (Groq)</button>
  <div id="ai-conseil-box" class="ai-conseil"></div>
</div>
</div></div>

<script>
const API='recettes_api.php';
let curId=0;
function filterR(){const q=document.getElementById('srch').value.toLowerCase();const df=document.getElementById('fdiff').value;document.querySelectorAll('.rec-card').forEach(c=>{const mn=(c.dataset.nom||'').includes(q);const md=!df||c.dataset.diff===df;c.style.display=mn&&md?'':'none';});}
async function openRec(id,nom){
  curId=id;document.getElementById('det-content').style.display='none';document.getElementById('det-loading').style.display='block';
  document.getElementById('det-panel').style.display='flex';document.getElementById('ai-conseil-box').style.display='none';
  const r=await fetch(`${API}?action=recettes_getOne&id=${id}`);const d=await r.json();
  document.getElementById('det-loading').style.display='none';
  if(!d.success)return;
  const rec=d.recette;const dets=d.details||[];
  document.getElementById('d-nom').textContent=rec.nom;
  document.getElementById('d-desc').textContent=rec.description||'';
  document.getElementById('d-meta').innerHTML=`<span>⏱ ${(+rec.temps_preparation||0)+(+rec.temps_cuisson||0)} min</span><span>🔥 ${rec.calories_totales||0} kcal</span><span>👥 ${rec.nombre_personnes||0} pers.</span><span class="badge ${'facile'=>'b-f','moyen'=>'b-m','difficile'=>'b-d'}[rec.difficulte]||'b-m'">${rec.difficulte||''}</span>`;
  const ings=dets.filter(x=>x.ingredient);const steps=dets.filter(x=>x.etape&&!x.ingredient);
  document.getElementById('d-ings-block').style.display=ings.length?'block':'none';
  document.getElementById('d-ings').innerHTML=ings.map(i=>`<div class="ing-item"><span style="color:#b2f2bb;font-weight:600">${i.ingredient}</span><span style="color:#fbbf24;font-size:12px">${i.quantite||''}</span></div>`).join('');
  document.getElementById('d-steps-block').style.display=steps.length?'block':'none';
  document.getElementById('d-steps').innerHTML=steps.map(s=>`<div class="step-item">${s.etape}</div>`).join('');
  document.getElementById('det-content').style.display='block';
}
function close_(){document.getElementById('det-panel').style.display='none';}
async function getConseil(){
  const box=document.getElementById('ai-conseil-box');
  box.style.display='block';box.textContent='🔄 Génération des conseils chef en cours...';
  const fd=new FormData();fd.append('action','ai_conseil');fd.append('id_recette',curId);
  const r=await fetch(API,{method:'POST',body:fd});const d=await r.json();
  box.textContent=d.success?d.conseil:'⚠️ Service IA temporairement indisponible.';
}
document.getElementById('det-panel').addEventListener('click',function(e){if(e.target===this)close_();});
</script>
<?php require __DIR__.'/partials/footer.php';?>
