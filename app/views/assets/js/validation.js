/**
 * Fonctions de Validation Utilitaires
 * Pour formulaires et saisie utilisateur
 */

/**
 * Valide qu'une chaîne n'est pas vide
 */
function validerNonVide(valeur) {
  return valeur && valeur.trim().length > 0;
}

/**
 * Valide qu'une valeur est un nombre positif
 */
function validerNombrePositif(valeur) {
  const num = parseFloat(valeur);
  return !isNaN(num) && num > 0;
}

/**
 * Valide qu'une valeur est un nombre entier positif
 */
function validerNombreEntier(valeur) {
  const num = parseInt(valeur);
  return !isNaN(num) && num > 0 && Number.isInteger(num);
}

/**
 * Valide un email
 */
function validerEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

/**
 * Valide une date
 */
function validerDate(dateStr) {
  const date = new Date(dateStr);
  return date instanceof Date && !isNaN(date);
}

/**
 * Valide qu'une date n'est pas expirée
 */
function validerDatePasExpiree(dateStr) {
  const date = new Date(dateStr);
  return date > new Date();
}

/**
 * Affiche une notification de succès
 */
function afficherSucces(message) {
  console.log("✓ " + message);
  if (typeof showNotification === "function") {
    showNotification(message, "success");
  }
}

/**
 * Affiche une notification d'erreur
 */
function afficherErreur(message) {
  console.error("✗ " + message);
  if (typeof showNotification === "function") {
    showNotification(message, "error");
  }
}

/**
 * Nettoie une entrée utilisateur
 */
function nettoyerEntree(valeur) {
  return valeur.trim().replace(/[<>]/g, "");
}
