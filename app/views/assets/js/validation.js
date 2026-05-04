/**
 * Validation JavaScript - Contrôle complet de saisie
 * app/views/assets/js/validation.js
 *
 * ARCHITECTURE: Validation UNIQUEMENT côté client (JavaScript)
 * - La validation est appliquée avant l'envoi au serveur
 * - Les contrôleurs PHP font confiance aux données validées
 */

// ===== VALIDATION RECETTE =====
function validerNomRecette(nom) {
  nom = nom.trim();
  if (!nom) return { valid: false, message: "❌ Le nom est requis" };
  if (/^\d+$/.test(nom)) return { valid: false, message: "❌ Le nom ne doit pas être un nombre" };
  if (nom.length < 3) return { valid: false, message: "❌ Le nom doit avoir au moins 3 caractères" };
  return { valid: true, message: "" };
}

function validerFormulaireRecette() {
  const erreurs = {};

  // Valider NOM
  const nom = document.querySelector('input[name="nom"]')?.value.trim();
  const vNom = validerNomRecette(nom || "");
  if (!vNom.valid) erreurs.nom = vNom.message;

  // Valider DESCRIPTION
  const description = document.querySelector('textarea[name="description"]')?.value.trim();
  if (!description) {
    erreurs.description = "❌ La description est requise";
  } else if (description.length < 10) {
    erreurs.description = "❌ La description doit avoir au moins 10 caractères";
  }

  // Valider NOMBRE PERSONNES
  const nbPers = document.querySelector('input[name="nombre_personnes"]')?.value;
  if (!nbPers || parseInt(nbPers) <= 0) {
    erreurs.nombre_personnes = "❌ Le nombre de personnes doit être supérieur à 0";
  }

  // Valider TEMPS PRÉPARATION
  const tPrep = document.querySelector('input[name="temps_preparation"]')?.value;
  if (!tPrep || parseInt(tPrep) < 0) {
    erreurs.temps_preparation = "❌ Le temps de préparation ne peut pas être négatif";
  }

  // Valider TEMPS CUISSON
  const tCuiss = document.querySelector('input[name="temps_cuisson"]')?.value;
  if (!tCuiss || parseInt(tCuiss) < 0) {
    erreurs.temps_cuisson = "❌ Le temps de cuisson ne peut pas être négatif";
  }

  // Valider DIFFICULTÉ
  const difficulte = document.querySelector('select[name="difficulte"]')?.value;
  const diffValides = ["facile", "moyen", "difficile"];
  if (!difficulte || !diffValides.includes(difficulte)) {
    erreurs.difficulte = "❌ Veuillez sélectionner une difficulté valide";
  }

  // Valider CALORIES
  const calories = document.querySelector('input[name="calories_totales"]')?.value;
  if (!calories || parseInt(calories) < 0) {
    erreurs.calories_totales = "❌ Les calories ne peuvent pas être négatives";
  }

  // Afficher les erreurs et retourner le résultat
  afficherErreurs(erreurs, "recette-form");
  return Object.keys(erreurs).length === 0;
}

// ===== VALIDATION DÉTAIL RECETTE =====
function validerFormulaireDetail() {
  const erreurs = {};

  // Valider RECETTE (select)
  const recette = document.querySelector('select[name="id_recette"]')?.value;
  if (!recette) {
    erreurs.id_recette = "❌ Veuillez sélectionner une recette";
  }

  // Valider INGRÉDIENT
  const ingredient = document.querySelector('input[name="ingredient"]')?.value.trim();
  if (!ingredient) {
    erreurs.ingredient = "❌ L'ingrédient est requis";
  } else if (ingredient.length < 2) {
    erreurs.ingredient = "❌ L'ingrédient doit avoir au moins 2 caractères";
  }

  // Valider QUANTITÉ
  const quantite = document.querySelector('input[name="quantite"]')?.value.trim();
  if (!quantite) {
    erreurs.quantite = "❌ La quantité est requise";
  }

  // Valider ÉTAPE
  const etape = document.querySelector('textarea[name="etape"]')?.value.trim();
  if (!etape) {
    erreurs.etape = "❌ L'étape de préparation est requise";
  } else if (etape.length < 5) {
    erreurs.etape = "❌ L'étape doit avoir au moins 5 caractères";
  }

  afficherErreurs(erreurs, "detail-form");
  return Object.keys(erreurs).length === 0;
}

// ===== AFFICHER ERREURS =====
function afficherErreurs(erreurs, formId = null) {
  // Déterminer le parent pour isoler les erreurs (formulaire spécifique ou document entier)
  const parent = formId ? document.getElementById(formId) : document;
  
  if (!parent) return;

  // Nettoyer les anciens messages d'erreur dans ce parent
  parent.querySelectorAll(".error-message").forEach((el) => el.remove());
  parent.querySelectorAll(".form-control.is-invalid").forEach((el) => {
    el.classList.remove("is-invalid");
  });

  // Afficher les nouvelles erreurs
  Object.keys(erreurs).forEach((champ) => {
    const element = parent.querySelector(
      `input[name="${champ}"], textarea[name="${champ}"], select[name="${champ}"]`
    );

    if (element) {
      element.classList.add("is-invalid");
      const messageDiv = document.createElement("div");
      messageDiv.className = "error-message text-danger small mt-1";
      messageDiv.style.color = "#e53e3e"; // Force styling based on admin.php styles
      messageDiv.style.fontSize = "12px";
      messageDiv.style.marginTop = "6px";
      messageDiv.textContent = erreurs[champ];
      element.parentNode.appendChild(messageDiv);
    }
  });

  // Scroll vers première erreur
  const premiereErreur = document.querySelector(".is-invalid");
  if (premiereErreur) {
    premiereErreur.scrollIntoView({ behavior: "smooth", block: "center" });
    premiereErreur.focus();
  }
}

// ===== ÉVÉNEMENTS AU CHARGEMENT =====
document.addEventListener("DOMContentLoaded", function () {
  const formRecette = document.getElementById("recette-form") || document.querySelector(
    'form[action*="ajouter_recette"], form[action*="editer_recette"], #form-recette'
  );
  if (formRecette) {
    formRecette.addEventListener("submit", function (e) {
      if (!validerFormulaireRecette()) {
        e.preventDefault();
        e.stopImmediatePropagation();
      }
    });
  }

  const formDetail = document.getElementById("detail-form");
  if (formDetail) {
    formDetail.addEventListener("submit", function (e) {
      if (!validerFormulaireDetail()) {
        e.preventDefault();
        e.stopImmediatePropagation();
      }
    });
  }

  // Validation en temps réel
  document.querySelectorAll("input, textarea, select").forEach((element) => {
    element.addEventListener("input", function () {
      this.classList.remove("is-invalid");
      const erreur = this.parentNode.querySelector(".error-message");
      if (erreur) {
        erreur.remove();
      }
    });
  });
});
