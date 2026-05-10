        </div>
    </div>
    <div style="text-align:center;padding:16px;color:#b2f2bb;font-size:13px;background:rgba(10, 61, 42, 0.9);backdrop-filter:blur(10px);border-top:1px solid rgba(178, 242, 187, 0.2);">
        © <?php echo (int) date('Y'); ?> Plateforme ECOSAVE
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════
     MODAL DE CONFIRMATION PREMIUM — ECOSAVE (footer partagé)
     Usage : customConfirm({ type, icon, title, message, labelOk, onConfirm })
     Types : 'danger' | 'warning' | 'info' | 'success'
     ═══════════════════════════════════════════════════════════════ -->
<style>
/* ── Overlay ──────────────────────────────────────────────────── */
#ecosave-confirm-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 999999;
    background: rgba(2, 8, 6, 0.78);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}
#ecosave-confirm-modal.show { display: flex; opacity: 1; }

/* ── Boîte principale ─────────────────────────────────────────── */
#ecosave-confirm-box {
    background: linear-gradient(160deg, #0d1f18 0%, #0a1a14 100%);
    border-radius: 24px;
    width: 92%;
    max-width: 430px;
    position: relative;
    overflow: hidden;
    transform: scale(0.82) translateY(20px);
    transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow:
        0 30px 80px rgba(0,0,0,0.7),
        0 0 0 1px rgba(255,255,255,0.05),
        inset 0 1px 0 rgba(255,255,255,0.06);
}
#ecosave-confirm-modal.show #ecosave-confirm-box {
    transform: scale(1) translateY(0);
}

/* ── Bande colorée dynamique (haut de modal) ──────────────────── */
#ecosave-confirm-band {
    height: 5px;
    width: 100%;
    background: linear-gradient(90deg, #ef4444, #dc2626);
    transition: background 0.3s ease;
}

/* ── Cercle icône animé ───────────────────────────────────────── */
#ecosave-confirm-icon-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 24px 16px;
}
#ecosave-confirm-icon-circle {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    background: rgba(239, 68, 68, 0.12);
    border: 2px solid rgba(239, 68, 68, 0.3);
    box-shadow: 0 0 0 8px rgba(239, 68, 68, 0.06), 0 0 0 16px rgba(239, 68, 68, 0.03);
    position: relative;
    transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
    animation: ecoPulse 2.4s ease-in-out infinite;
}
@keyframes ecoPulse {
    0%, 100% { box-shadow: 0 0 0 8px rgba(239,68,68,0.06), 0 0 0 16px rgba(239,68,68,0.03); }
    50%       { box-shadow: 0 0 0 12px rgba(239,68,68,0.1), 0 0 0 22px rgba(239,68,68,0.05); }
}

/* ── Badge type ───────────────────────────────────────────────── */
#ecosave-confirm-badge {
    display: inline-block;
    margin: 0 auto 10px;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    background: rgba(239, 68, 68, 0.15);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.25);
    font-family: 'Segoe UI', Roboto, sans-serif;
    transition: all 0.3s ease;
}

/* ── Textes ───────────────────────────────────────────────────── */
#ecosave-confirm-body {
    text-align: center;
    padding: 0 28px 8px;
}
#ecosave-confirm-title {
    color: #f1f5f9;
    font-size: 19px;
    font-weight: 700;
    margin: 0 0 10px;
    line-height: 1.3;
    font-family: 'Segoe UI', Roboto, sans-serif;
    letter-spacing: -0.01em;
}
#ecosave-confirm-message {
    color: #94a3b8;
    font-size: 13.5px;
    line-height: 1.65;
    margin: 0;
    font-family: 'Segoe UI', Roboto, sans-serif;
}

/* ── Séparateur ───────────────────────────────────────────────── */
#ecosave-confirm-sep {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.07), transparent);
    margin: 22px 0 0;
}

/* ── Footer boutons ───────────────────────────────────────────── */
#ecosave-confirm-footer {
    display: flex;
    gap: 10px;
    padding: 16px 24px 20px;
    justify-content: flex-end;
}
.ecosave-btn-cancel {
    padding: 11px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 13.5px;
    cursor: pointer;
    background: rgba(255,255,255,0.06);
    color: #94a3b8;
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.2s ease;
    font-family: 'Segoe UI', Roboto, sans-serif;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ecosave-btn-cancel:hover {
    background: rgba(255,255,255,0.11);
    color: #e2e8f0;
    transform: translateY(-1px);
    border-color: rgba(255,255,255,0.18);
}
.ecosave-btn-confirm {
    padding: 11px 22px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 13.5px;
    cursor: pointer;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #fff;
    border: none;
    box-shadow: 0 4px 18px rgba(239,68,68,0.4), inset 0 1px 0 rgba(255,255,255,0.15);
    transition: all 0.22s ease;
    font-family: 'Segoe UI', Roboto, sans-serif;
    display: flex;
    align-items: center;
    gap: 6px;
    position: relative;
    overflow: hidden;
}
.ecosave-btn-confirm::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
    pointer-events: none;
}
.ecosave-btn-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(239,68,68,0.5), inset 0 1px 0 rgba(255,255,255,0.2);
    filter: brightness(1.08);
}
.ecosave-btn-confirm:active { transform: translateY(0); }

/* ── Thèmes de couleur ────────────────────────────────────────── */
/* warning (orange) */
#ecosave-confirm-modal[data-type="warning"] #ecosave-confirm-band {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}
#ecosave-confirm-modal[data-type="warning"] #ecosave-confirm-icon-circle {
    background: rgba(245,158,11,0.12); border-color: rgba(245,158,11,0.3);
    box-shadow: 0 0 0 8px rgba(245,158,11,0.06), 0 0 0 16px rgba(245,158,11,0.03);
    animation: ecoPulseWarn 2.4s ease-in-out infinite;
}
@keyframes ecoPulseWarn {
    0%,100%{ box-shadow:0 0 0 8px rgba(245,158,11,0.06),0 0 0 16px rgba(245,158,11,0.03); }
    50%    { box-shadow:0 0 0 12px rgba(245,158,11,0.1),0 0 0 22px rgba(245,158,11,0.05); }
}
#ecosave-confirm-modal[data-type="warning"] #ecosave-confirm-badge {
    background:rgba(245,158,11,0.15); color:#fbbf24; border-color:rgba(245,158,11,0.3);
}
#ecosave-confirm-modal[data-type="warning"] .ecosave-btn-confirm {
    background: linear-gradient(135deg,#f59e0b,#d97706);
    box-shadow: 0 4px 18px rgba(245,158,11,0.4), inset 0 1px 0 rgba(255,255,255,0.15);
}
#ecosave-confirm-modal[data-type="warning"] .ecosave-btn-confirm:hover {
    box-shadow: 0 8px 24px rgba(245,158,11,0.5), inset 0 1px 0 rgba(255,255,255,0.2);
}
/* info (bleu) */
#ecosave-confirm-modal[data-type="info"] #ecosave-confirm-band {
    background: linear-gradient(90deg, #3b82f6, #2563eb);
}
#ecosave-confirm-modal[data-type="info"] #ecosave-confirm-icon-circle {
    background: rgba(59,130,246,0.12); border-color: rgba(59,130,246,0.3);
    animation: ecoPulseInfo 2.4s ease-in-out infinite;
}
@keyframes ecoPulseInfo {
    0%,100%{ box-shadow:0 0 0 8px rgba(59,130,246,0.06),0 0 0 16px rgba(59,130,246,0.03); }
    50%    { box-shadow:0 0 0 12px rgba(59,130,246,0.1),0 0 0 22px rgba(59,130,246,0.05); }
}
#ecosave-confirm-modal[data-type="info"] #ecosave-confirm-badge {
    background:rgba(59,130,246,0.15); color:#60a5fa; border-color:rgba(59,130,246,0.3);
}
#ecosave-confirm-modal[data-type="info"] .ecosave-btn-confirm {
    background: linear-gradient(135deg,#3b82f6,#2563eb);
    box-shadow: 0 4px 18px rgba(59,130,246,0.4), inset 0 1px 0 rgba(255,255,255,0.15);
}
/* success (vert) */
#ecosave-confirm-modal[data-type="success"] #ecosave-confirm-band {
    background: linear-gradient(90deg, #10b981, #059669);
}
#ecosave-confirm-modal[data-type="success"] #ecosave-confirm-icon-circle {
    background: rgba(16,185,129,0.12); border-color: rgba(16,185,129,0.3);
    animation: ecoPulseSuccess 2.4s ease-in-out infinite;
}
@keyframes ecoPulseSuccess {
    0%,100%{ box-shadow:0 0 0 8px rgba(16,185,129,0.06),0 0 0 16px rgba(16,185,129,0.03); }
    50%    { box-shadow:0 0 0 12px rgba(16,185,129,0.1),0 0 0 22px rgba(16,185,129,0.05); }
}
#ecosave-confirm-modal[data-type="success"] #ecosave-confirm-badge {
    background:rgba(16,185,129,0.15); color:#34d399; border-color:rgba(16,185,129,0.3);
}
#ecosave-confirm-modal[data-type="success"] .ecosave-btn-confirm {
    background: linear-gradient(135deg,#10b981,#059669);
    box-shadow: 0 4px 18px rgba(16,185,129,0.4), inset 0 1px 0 rgba(255,255,255,0.15);
}
</style>

<div id="ecosave-confirm-modal" data-type="danger">
    <div id="ecosave-confirm-box">
        <!-- Bande colorée dynamique -->
        <div id="ecosave-confirm-band"></div>

        <!-- Icône dans cercle animé -->
        <div id="ecosave-confirm-icon-wrap">
            <div id="ecosave-confirm-icon-circle">
                <span id="ecosave-confirm-icon">⚠️</span>
            </div>
        </div>

        <!-- Corps -->
        <div id="ecosave-confirm-body">
            <div id="ecosave-confirm-badge">Action requise</div>
            <h3 id="ecosave-confirm-title">Confirmation</h3>
            <p id="ecosave-confirm-message">Êtes-vous sûr de vouloir effectuer cette action ?</p>
        </div>

        <!-- Séparateur -->
        <div id="ecosave-confirm-sep"></div>

        <!-- Boutons -->
        <div id="ecosave-confirm-footer">
            <button class="ecosave-btn-cancel" onclick="closeEcosaveConfirm()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                Annuler
            </button>
            <button class="ecosave-btn-confirm" id="ecosave-confirm-ok">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Confirmer
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    let _ecoCb = null;

    /* Libellés et icônes des badges par type */
    const _typeConfig = {
        danger:  { badge: '⛔ Action irréversible', btnClass: '' },
        warning: { badge: '⚠️ Attention requise',   btnClass: '' },
        info:    { badge: 'ℹ️ Information',          btnClass: '' },
        success: { badge: '✅ Confirmation',          btnClass: '' },
    };

    window.customConfirm = function({
        type     = 'danger',
        icon     = '⚠️',
        title    = 'Confirmation',
        message  = '',
        badge    = null,
        labelOk  = '✔ Confirmer',
        onConfirm
    } = {}) {
        const modal = document.getElementById('ecosave-confirm-modal');
        const cfg   = _typeConfig[type] || _typeConfig.danger;

        /* Appliquer le type (couleurs CSS via attribut) */
        modal.setAttribute('data-type', type);

        /* Contenu */
        document.getElementById('ecosave-confirm-icon').textContent    = icon;
        document.getElementById('ecosave-confirm-title').textContent   = title;
        document.getElementById('ecosave-confirm-message').textContent = message;
        document.getElementById('ecosave-confirm-badge').textContent   = badge || cfg.badge;
        document.getElementById('ecosave-confirm-ok').innerHTML        =
            `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> ${labelOk}`;

        /* Callback */
        _ecoCb = function() {
            closeEcosaveConfirm();
            if (typeof onConfirm === 'function') onConfirm();
        };
        document.getElementById('ecosave-confirm-ok').onclick = _ecoCb;

        /* Afficher */
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('show'));
    };

    window.closeEcosaveConfirm = function() {
        const modal = document.getElementById('ecosave-confirm-modal');
        modal.classList.remove('show');
        setTimeout(() => { modal.style.display = 'none'; _ecoCb = null; }, 300);
    };

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('ecosave-confirm-modal').addEventListener('click', function(e) {
            if (e.target === this) closeEcosaveConfirm();
        });
    });
})();
</script>

</body>
</html>
