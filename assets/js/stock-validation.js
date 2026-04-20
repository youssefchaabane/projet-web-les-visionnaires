/**
 * Validation Functions - Gestion du Stock
 * Validations côté client pour produits et catégories
 */

// ====================================================
// VALIDATIONS CATÉGORIES
// ====================================================

/**
 * Valider le nom d'une catégorie
 * - Minimum 3 caractères
 * - Ne pas être que des chiffres
 */
function validerNomCategorie(nom) {
  if (!nom || nom.trim().length === 0) {
    return false;
  }

  // Au moins 3 caractères
  if (nom.trim().length < 3) {
    return false;
  }

  // Ne pas être que des chiffres
  if (/^\d+$/.test(nom.trim())) {
    return false;
  }

  // Maximum 100 caractères
  if (nom.trim().length > 100) {
    return false;
  }

  return true;
}

// ====================================================
// VALIDATIONS PRODUITS
// ====================================================

/**
 * Valider le nom d'un produit
 * - Minimum 3 caractères
 * - Ne pas être que des chiffres
 */
function validerNomProduit(nom) {
  if (!nom || nom.trim().length === 0) {
    return false;
  }

  // Au moins 3 caractères
  if (nom.trim().length < 3) {
    return false;
  }

  // Ne pas être que des chiffres
  if (/^\d+$/.test(nom.trim())) {
    return false;
  }

  // Maximum 100 caractères
  if (nom.trim().length > 100) {
    return false;
  }

  return true;
}

/**
 * Valider le prix d'un produit
 * - Doit être un nombre positif
 * - Maximum 2 décimales
 */
function validerPrix(prix) {
  if (isNaN(prix) || prix < 0) {
    return false;
  }

  // Vérifier les décimales
  const decimalPlaces = (prix.toString().split(".")[1] || "").length;
  if (decimalPlaces > 2) {
    return false;
  }

  // Maximum 99999.99
  if (prix > 99999.99) {
    return false;
  }

  return true;
}

/**
 * Valider une quantité
 * - Nombre entier positif ou zéro
 */
function validerQuantite(quantite) {
  if (!Number.isInteger(quantite)) {
    return false;
  }

  if (quantite < 0) {
    return false;
  }

  // Maximum 999999
  if (quantite > 999999) {
    return false;
  }

  return true;
}

/**
 * Valider la quantité minimum
 * - Nombre entier positif
 */
function validerQuantiteMin(quantiteMin) {
  if (!Number.isInteger(quantiteMin)) {
    return false;
  }

  if (quantiteMin < 1) {
    return false;
  }

  // Maximum 999999
  if (quantiteMin > 999999) {
    return false;
  }

  return true;
}

/**
 * Valider une description
 * - Optionnelle
 * - Maximum 1000 caractères
 */
function validerDescription(description) {
  if (!description) {
    return true; // Optionnelle
  }

  // Maximum 1000 caractères
  if (description.length > 1000) {
    return false;
  }

  return true;
}

// ====================================================
// VALIDATIONS DE STOCK
// ====================================================

/**
 * Valider une quantité de mouvement (ajout/retrait)
 * - Nombre entier positif
 * - Minimum 1
 */
function validerMouvementStock(quantite) {
  if (!Number.isInteger(quantite)) {
    return false;
  }

  if (quantite < 1) {
    return false;
  }

  // Maximum 999999
  if (quantite > 999999) {
    return false;
  }

  return true;
}

/**
 * Vérifier qu'il y a assez de stock pour un retrait
 */
function verifierStockDisponible(quantiteActuelle, quantiteRetrait) {
  return quantiteActuelle >= quantiteRetrait;
}

// ====================================================
// VALIDATIONS GLOBALES DE FORMULAIRES
// ====================================================

/**
 * Valider le formulaire complet de création/édition de produit
 */
function validerFormulaireProduit(formData) {
  const errors = [];

  if (!validerNomProduit(formData.nom)) {
    errors.push(
      "Nom du produit invalide (3-100 caractères, pas seulement des chiffres)"
    );
  }

  if (!validerDescription(formData.description)) {
    errors.push("Description trop longue (maximum 1000 caractères)");
  }

  if (!validerQuantite(parseInt(formData.quantite))) {
    errors.push("Quantité invalide");
  }

  if (!validerPrix(parseFloat(formData.prix))) {
    errors.push("Prix invalide (format: 0.00 maximum)");
  }

  if (!validerQuantiteMin(parseInt(formData.quantite_min))) {
    errors.push("Quantité minimum invalide");
  }

  if (!formData.id_categorie) {
    errors.push("Catégorie obligatoire");
  }

  return {
    valid: errors.length === 0,
    errors: errors,
  };
}

/**
 * Valider le formulaire complet de création/édition de catégorie
 */
function validerFormulaireCategorie(formData) {
  const errors = [];

  if (!validerNomCategorie(formData.nom)) {
    errors.push(
      "Nom de la catégorie invalide (3-100 caractères, pas seulement des chiffres)"
    );
  }

  if (!validerDescription(formData.description)) {
    errors.push("Description trop longue (maximum 1000 caractères)");
  }

  return {
    valid: errors.length === 0,
    errors: errors,
  };
}

/**
 * Valider un mouvement de stock
 */
function validerMouvementStockForm(formData) {
  const errors = [];

  if (!formData.id_produit) {
    errors.push("Produit obligatoire");
  }

  if (!validerMouvementStock(parseInt(formData.quantite))) {
    errors.push("Quantité invalide (nombre entier positif requis)");
  }

  return {
    valid: errors.length === 0,
    errors: errors,
  };
}

// ====================================================
// Messages d'erreurs personnalisés
// ====================================================

/**
 * Obtenir un message d'erreur personnalisé par type de validation
 */
function getValidationErrorMessage(type, value) {
  const messages = {
    nom_invalide:
      "Le nom doit contenir au moins 3 caractères et ne pas être seulement des chiffres",
    prix_invalide:
      "Le prix doit être un nombre positif avec maximum 2 décimales",
    quantite_invalide: "La quantité doit être un nombre entier positif",
    quantite_min_invalide: "La quantité minimum doit être au moins 1",
    description_trop_longue:
      "La description ne doit pas dépasser 1000 caractères",
    categorie_requise: "Vous devez sélectionner une catégorie",
    produit_requis: "Vous devez sélectionner un produit",
    quantite_movement_invalide:
      "La quantité doit être un nombre entier positif",
    stock_insuffisant: "Stock insuffisant pour cette opération",
    formulaire_incomplet: "Veuillez remplir tous les champs obligatoires",
  };

  return messages[type] || "Erreur de validation";
}

// ====================================================
// HELPERS
// ====================================================

/**
 * Afficher les erreurs de validation
 */
function afficherErreurValidation(errors) {
  if (errors.length === 0) return;

  console.error("Erreurs de validation:", errors);

  // Vous pouvez afficher les erreurs dans une notification
  // showNotification(errors.join('\n'), 'error');
}

/**
 * Nettoyer les données de formulaire
 */
function nettoyerDonneesProduit(data) {
  return {
    nom: (data.nom || "").trim(),
    description: (data.description || "").trim(),
    quantite: parseInt(data.quantite) || 0,
    prix: parseFloat(data.prix) || 0,
    quantite_min: parseInt(data.quantite_min) || 10,
    id_categorie: parseInt(data.id_categorie) || null,
  };
}

function nettoyerDonneesCategorie(data) {
  return {
    nom: (data.nom || "").trim(),
    description: (data.description || "").trim(),
  };
}
