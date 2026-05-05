// Language Management System
let currentLanguage = localStorage.getItem('selectedLanguage') || 'fr';

// Translation dictionary
const translations = {
    fr: {
        // Navigation
        accueil: "Accueil",
        utilisateurs: "Utilisateurs",
        liste: "Liste",
        ajout: "Ajout",
        statistiques: "Statistiques",
        deconnexion: "Déconnexion",
        navigation: "Navigation",
        modules: "Modules",
        
        // Dashboard
        bienvenue: "Bienvenue sur votre compte",
        mes_informations: "Mes informations",
        email: "Email",
        nom_prenom: "Nom complet",
        date_creation: "Date d'inscription",
        role: "Rôle",
        regime_alimentaire: "Régime alimentaire",
        objectif_sante: "Objectif santé",
        niveau_activite: "Niveau d'activité",
        bonjour_utilisateur: "Bonjour",
        bienvenue_compte: "Bienvenue sur votre compte",
        modifier: "Modifier",
        nom_complet: "Nom complet",
        date_inscription: "Date d'inscription",
        stock: "Stock",
        allergies: "Allergies",
        recettes: "Recettes",
        empreinte: "Empreinte",
        empreinte_carbone: "Empreinte Carbone",
        fil_actualite: "Fil d'actualité",
        
        // Cards
        publication: "Publication",
        carnet_recettes: "Ton carnet de recettes",
        reserve_durable: "Ta réserve durable",
        ton_empreinte: "Ton empreinte",
        
        // Chatbot
        assistant_ecosave: "Assistant ECOSAVE Pro",
        en_ligne_expert: "En ligne • Expert personnel",
        posez_question: "Posez-moi votre question...",
        recettes: "Recettes",
        sport: "Sport",
        conseils: "Conseils",
        objectifs: "Objectifs"
    },
    en: {
        // Navigation
        accueil: "Home",
        utilisateurs: "Users",
        liste: "List",
        ajout: "Add",
        statistiques: "Statistics",
        deconnexion: "Logout",
        navigation: "Navigation",
        modules: "Modules",
        
        // Dashboard
        bienvenue: "Welcome to your account",
        mes_informations: "My Information",
        email: "Email",
        nom_prenom: "Full Name",
        date_creation: "Registration Date",
        role: "Role",
        regime_alimentaire: "Dietary Regime",
        objectif_sante: "Health Objective",
        niveau_activite: "Activity Level",
        
        // Dashboard Additional
        bonjour_utilisateur: "Hello",
        bienvenue_compte: "Welcome to your account",
        modifier: "Edit",
        nom_complet: "Full Name",
        date_inscription: "Registration Date",
        stock: "Stock",
        allergies: "Allergies",
        recettes: "Recipes",
        empreinte: "Footprint",
        empreinte_carbone: "Carbon Footprint",
        fil_actualite: "News Feed",
        
        // Chatbot
        assistant_ecosave: "ECOSAVE Pro Assistant",
        en_ligne_expert: "Online • Personal Expert",
        posez_question: "Ask me your question...",
        recettes: "Recipes",
        sport: "Sport",
        conseils: "Tips",
        objectifs: "Goals"
    }
};

// Change language function
function changeLanguage(lang) {
    currentLanguage = lang;
    localStorage.setItem('selectedLanguage', lang);
    
    // Update button states
    document.querySelectorAll('.language-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.language-btn').classList.add('active');
    
    // Apply translations
    applyTranslations();
    
    // Update page direction for Arabic
    if (lang === 'ar') {
        document.documentElement.setAttribute('dir', 'rtl');
        document.documentElement.setAttribute('lang', 'ar');
    } else {
        document.documentElement.setAttribute('dir', 'ltr');
        document.documentElement.setAttribute('lang', lang);
    }
}

// Apply translations to the page
function applyTranslations() {
    const trans = translations[currentLanguage];
    
    // Update navigation
    updateText('[data-translate="accueil"]', trans.accueil);
    updateText('[data-translate="utilisateurs"]', trans.utilisateurs);
    updateText('[data-translate="liste"]', trans.liste);
    updateText('[data-translate="ajout"]', trans.ajout);
    updateText('[data-translate="statistiques"]', trans.statistiques);
    updateText('[data-translate="deconnexion"]', trans.deconnexion);
    updateText('[data-translate="navigation"]', trans.navigation);
    updateText('[data-translate="modules"]', trans.modules);
    
    // Update dashboard
    updateText('[data-translate="bienvenue"]', trans.bienvenue);
    updateText('[data-translate="mes_informations"]', trans.mes_informations);
    updateText('[data-translate="email"]', trans.email);
    updateText('[data-translate="nom_prenom"]', trans.nom_prenom);
    updateText('[data-translate="date_creation"]', trans.date_creation);
    updateText('[data-translate="role"]', trans.role);
    updateText('[data-translate="regime_alimentaire"]', trans.regime_alimentaire);
    updateText('[data-translate="objectif_sante"]', trans.objectif_sante);
    updateText('[data-translate="niveau_activite"]', trans.niveau_activite);
    
    // Update dashboard additional
    updateText('[data-translate="bonjour_utilisateur"]', trans.bonjour_utilisateur);
    updateText('[data-translate="bienvenue_compte"]', trans.bienvenue_compte);
    updateText('[data-translate="modifier"]', trans.modifier);
    updateText('[data-translate="nom_complet"]', trans.nom_complet);
    updateText('[data-translate="date_inscription"]', trans.date_inscription);
    updateText('[data-translate="stock"]', trans.stock);
    updateText('[data-translate="allergies"]', trans.allergies);
    updateText('[data-translate="recettes"]', trans.recettes);
    updateText('[data-translate="empreinte"]', trans.empreinte);
    updateText('[data-translate="empreinte_carbone"]', trans.empreinte_carbone);
    updateText('[data-translate="fil_actualite"]', trans.fil_actualite);
    
    // Update chatbot
    updateText('[data-translate="assistant_ecosave"]', trans.assistant_ecosave);
    updateText('[data-translate="en_ligne_expert"]', trans.en_ligne_expert);
    updateText('[data-translate="posez_question"]', trans.posez_question);
    updateText('[data-translate="recettes"]', trans.recettes);
    updateText('[data-translate="sport"]', trans.sport);
    updateText('[data-translate="conseils"]', trans.conseils);
    updateText('[data-translate="objectifs"]', trans.objectifs);
}

// Helper function to update text content
function updateText(selector, text) {
    const elements = document.querySelectorAll(selector);
    elements.forEach(element => {
        if (element) {
            element.textContent = text;
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set active language button
    document.querySelectorAll('.language-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('onclick').includes(`'${currentLanguage}'`)) {
            btn.classList.add('active');
        }
    });
    
    // Apply initial translations
    applyTranslations();
    
    // Set initial direction
    if (currentLanguage === 'ar') {
        document.documentElement.setAttribute('dir', 'rtl');
        document.documentElement.setAttribute('lang', 'ar');
    }
});
