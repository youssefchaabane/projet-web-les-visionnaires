/**
 * Validation JavaScript - Contrôle complet de saisie
 * app/views/assets/js/validation.js
 *
 * ARCHITECTURE: Validation UNIQUEMENT côté client (JavaScript)
 * - Aucune validation PHP n'est effectuée
 * - La validation est appliquée avant l'envoi au serveur
 * - Les contrôleurs PHP font confiance aux données validées
 */

// ===== VALIDATION RECETTE =====
function validerRecette() {
  const erreurs = {};
  const nom = document.getElementById("recette-nom")?.value.trim();
  const description = document.getElementById("recette-description")?.value.trim();

  if (!nom) erreurs.nom = "❌ Le nom est requis";
  else if (nom.length < 3) erreurs.nom = "❌ Minimum 3 caractères";

  afficherErreurs(erreurs, "recette");
  return Object.keys(erreurs).length === 0;
}

// ===== VALIDATION FACTEUR EMISSION =====
function validerFacteur() {
  const erreurs = {};
  const categorie = document.getElementById("facteur-categorie")?.value.trim();
  const co2 = document.getElementById("facteur-co2")?.value;
  const source = document.getElementById("facteur-source")?.value.trim();
  const dateMaj = document.getElementById("facteur-date-maj")?.value;

  if (!categorie) erreurs.categorie_aliment = "❌ La catégorie est requise";
  if (!co2 || isNaN(co2) || parseFloat(co2) < 0) erreurs.co2_par_kg = "❌ CO2 invalide (doit être ≥ 0)";
  if (!source) erreurs.source_donnee = "❌ La source est requise";
  if (!dateMaj) erreurs.date_derniere_maj = "❌ Date requise";

  afficherErreurs(erreurs, "facteur");
  return Object.keys(erreurs).length === 0;
}

// ===== VALIDATION ANALYSE CARBONE =====
function validerAnalyse() {
  const erreurs = {};
  const recipe = document.getElementById("analyse-recette")?.value;
  const score = document.getElementById("analyse-score")?.value;
  const impact = document.getElementById("analyse-impact")?.value;
  const methode = document.getElementById("analyse-methode")?.value.trim();
  const dateCalc = document.getElementById("analyse-date")?.value;

  if (!recipe) erreurs.id_recette = "❌ Recette requise";
  if (!score || isNaN(score) || parseFloat(score) < 0) erreurs.score_co2_total = "❌ Score CO2 invalide";
  if (!impact) erreurs.niveau_impact = "❌ Niveau d'impact requis";
  if (!methode) erreurs.methode_calcul = "❌ Méthode de calcul requise";
  if (!dateCalc) erreurs.date_calcul = "❌ Date de calcul requise";

  afficherErreurs(erreurs, "analyse");
  return Object.keys(erreurs).length === 0;
}

// ===== AFFICHER ERREURS =====
function afficherErreurs(erreurs, prefix) {
  // Nettoyer les anciens messages d'erreur
  document.querySelectorAll(".error-message").forEach((el) => el.remove());
  document.querySelectorAll(".form-control.is-invalid, .form-select.is-invalid").forEach((el) => {
    el.classList.remove("is-invalid");
  });

  // Afficher les nouvelles erreurs
  Object.keys(erreurs).forEach((champ) => {
    // Le champ ID dans le DOM correspond à prefix-champ (souvent simplifié)
    // On va chercher l'élément par son ID s'il existe ou par son nom
    let element = document.getElementById(`${prefix}-${champ.replace(/_/g, "-")}`);
    if(!element) {
        // Fallback search by name inside the active modal
        element = document.querySelector(`#${prefix}-modal [name="${champ}"]`);
    }

    if (element) {
      element.classList.add("is-invalid");

      // Ajouter message d'erreur sous le champ
      const messageDiv = document.createElement("div");
      messageDiv.className = "error-message text-danger small mt-1";
      messageDiv.textContent = erreurs[champ];
      element.parentNode.appendChild(messageDiv);
    }
  });
}

// ===== RECHERCHE VALIDATION =====
function validerRecherche(terme) {
  if (!terme) {
    alert("⚠️ Veuillez entrer un terme de recherche");
    return false;
  }
  if (terme.length < 2) {
    alert("⚠️ Minimum 2 caractères pour la recherche");
    return false;
  }
  return true;
}
