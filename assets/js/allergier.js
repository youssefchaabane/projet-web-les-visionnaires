/**
 * Contrôle de saisie (sans validation HTML5) + confirmation suppression (modale).
 * Formulaires : class="allergier-form" + option data-form-type="allergie|traitement|association"
 */
(function () {
  "use strict";

  function trim(s) {
    return String(s || "").replace(/\s+/g, " ").trim();
  }

  function showErrors(box, messages) {
    if (!box) return;
    box.innerHTML = "";
    if (!messages.length) {
      box.style.display = "none";
      return;
    }
    var ul = document.createElement("ul");
    messages.forEach(function (msg) {
      var li = document.createElement("li");
      li.textContent = msg;
      ul.appendChild(li);
    });
    box.appendChild(ul);
    box.style.display = "block";
  }

  var RE_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var RE_LETTERS = /^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/;
  var RE_DOSAGE = /^\d+\s*mg$/i;
  var RE_DUREE = /^\d+\s*[A-Za-zÀ-ÖØ-öø-ÿ]+$/;

  function valOf(form, name) {
    var el = form.querySelector('[name="' + name + '"]');
    return el ? trim(el.value) : "";
  }

  function validateField(el) {
    var errors = [];
    var v = trim(el.value);
    var min = el.getAttribute("data-minlength");
    var max = el.getAttribute("data-maxlength");
    var kind = (el.getAttribute("data-kind") || el.type || "").toLowerCase();
    var name = el.name || "";

    if (el.hasAttribute("required") && v === "") {
      var lab =
        el.labels && el.labels[0]
          ? trim(el.labels[0].textContent).replace(/:$/, "")
          : "";
      errors.push(lab ? "Le champ « " + lab + " » est obligatoire." : "Un champ obligatoire est vide.");
      return errors;
    }
    if (v === "") return errors;

    if (min && v.length < parseInt(min, 10)) {
      if (name === "symptomes") {
        errors.push("Symptômes : minimum " + min + " caractères.");
      } else if (name === "effets_secondaires") {
        errors.push("Effets secondaires : minimum " + min + " caractères.");
      } else {
        errors.push("Minimum " + min + " caractères.");
      }
    }
    if (max && v.length > parseInt(max, 10)) {
      errors.push("Maximum " + max + " caractères.");
    }
    if (kind === "email" && !RE_EMAIL.test(v)) {
      errors.push("Adresse e-mail invalide.");
    }

    return errors;
  }

  function validateForm(form) {
    var all = [];
    var fields = form.querySelectorAll("input, textarea, select");
    Array.prototype.forEach.call(fields, function (el) {
      if (el.disabled || el.type === "hidden" || el.type === "button" || el.type === "submit") return;
      validateField(el).forEach(function (e) {
        all.push(e);
      });
    });
    return all;
  }

  function normalizeDosage(s) {
    return trim(s).replace(/\s+/g, "");
  }

  function validateTyped(form) {
    var t = form.getAttribute("data-form-type") || "";
    var err = [];
    if (t === "allergie") {
      var nom = valOf(form, "nom");
      if (nom && !RE_LETTERS.test(nom)) err.push("Nom : lettres et espaces uniquement (pas de chiffres).");
    }
    if (t === "traitement") {
      var nomt = valOf(form, "nom");
      if (nomt && !RE_LETTERS.test(nomt)) err.push("Nom : lettres et espaces uniquement (pas de chiffres).");
      var dosage = normalizeDosage(valOf(form, "dosage"));
      if (dosage && !RE_DOSAGE.test(dosage)) err.push('Dosage : format attendu comme "10mg" ou "500mg".');
      var duree = valOf(form, "duree");
      if (duree && !RE_DUREE.test(duree)) err.push('Durée : nombre + texte, ex. "7 jours", "1mois".');
    }
    return err;
  }

  function wireForm(form) {
    var errBox = form.querySelector(".allergier-form-errors");
    form.setAttribute("novalidate", "novalidate");
    form.addEventListener("submit", function (e) {
      var msgs = validateForm(form).concat(validateTyped(form));
      showErrors(errBox, msgs);
      if (msgs.length) e.preventDefault();
    });
  }

  /**
   * wireDeleteConfirm — Remplace l'ancien modal blanc par customConfirm()
   * (même design glassmorphism que stock_admin.php)
   * Écoute les boutons [type="submit"][data-confirm] dans tous les formulaires.
   */
  function wireDeleteConfirm() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('button[type="submit"][data-confirm]');
      if (!btn) return;

      var form = btn.closest('form');
      if (!form) return;

      /* Si déjà validé (drapeau posé par customConfirm), laisser passer */
      if (form.getAttribute('data-allergier-ok') === '1') {
        form.removeAttribute('data-allergier-ok');
        return;
      }

      e.preventDefault();

      /* Déterminer l'icône et le titre selon la page/contexte */
      var icon = '🗑️';
      var title = 'Confirmer la suppression';
      var path = window.location.pathname;

      if (path.indexOf('allergies') !== -1) {
          icon = '🌿';
          title = 'Supprimer cette allergie ?';
      } else if (path.indexOf('traitements') !== -1) {
          icon = '💊';
          title = 'Supprimer ce traitement ?';
      } else if (path.indexOf('associations') !== -1) {
          icon = '🔗';
          title = 'Supprimer cette association ?';
      }

      /* Utilise customConfirm() du footer */
      if (typeof window.customConfirm === 'function') {
        window.customConfirm({
          type: 'danger',
          icon: icon,
          title: title,
          message: btn.getAttribute('data-confirm') || 'Cette action est irréversible. L\'élément sera définitivement supprimé.',
          labelOk: '🗑️ Supprimer',
          onConfirm: function () {
            form.setAttribute('data-allergier-ok', '1');
            if (typeof form.requestSubmit === 'function') {
              form.requestSubmit(btn);
            } else {
              form.submit();
            }
          }
        });
      } else {
        /* Fallback si customConfirm non disponible */
        if (window.confirm(btn.getAttribute('data-confirm') || 'Confirmer la suppression ?')) {
          form.setAttribute('data-allergier-ok', '1');
          form.submit();
        }
      }
    });
  }

  function wireFrontTabs() {
    var radios = {
      allergies: document.getElementById("front-tab-allergies"),
      traitements: document.getElementById("front-tab-traitements"),
      associations: document.getElementById("front-tab-associations")
    };
    if (!radios.allergies) return;

    function keyFromHash() {
      var h = (window.location.hash || "").replace(/^#/, "");
      if (h === "traitements" || h === "associations" || h === "allergies") return h;
      return "allergies";
    }

    function applyHashToRadios() {
      var k = keyFromHash();
      var el = radios[k];
      if (el) el.checked = true;
    }

    Object.keys(radios).forEach(function (k) {
      var r = radios[k];
      if (!r) return;
      r.addEventListener("change", function () {
        if (!r.checked) return;
        if (window.history && typeof window.history.replaceState === "function") {
          window.history.replaceState(null, "", "#" + k);
        }
      });
    });

    window.addEventListener("hashchange", applyHashToRadios);
    applyHashToRadios();
  }

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("form.allergier-form").forEach(wireForm);
    wireDeleteConfirm();
    wireFrontTabs();
  });
})();
