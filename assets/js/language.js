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
    ar: {
        // Navigation
        accueil: "الصفحة الرئيسية",
        utilisateurs: "المستخدمون",
        liste: "القائمة",
        ajout: "إضافة",
        statistiques: "الإحصائيات",
        deconnexion: "تسجيل الخروج",
        
        // Dashboard
        bienvenue: "مرحباً بكم في حسابكم",
        mes_informations: "معلوماتي",
        email: "البريد الإلكتروني",
        nom_prenom: "الاسم الكامل",
        date_creation: "تاريخ التسجيل",
        role: "الدور",
        regime_alimentaire: "النظام الغذائي",
        objectif_sante: "الهدف الصحي",
        niveau_activite: "مستوى النشاط",
        
        // Cards
        publication: "النشر",
        carnet_recettes: "دفتر وصفاتي",
        reserve_durable: "مخزوني المستدام",
        ton_empreinte: "بصمتك البيئية",
        
        // Chatbot
        assistant_ecosave: "مساعد ECOSAVE Pro",
        en_ligne_expert: "متصل • خبير شخصي",
        posez_question: "اطرح سؤالك...",
        recettes: "وصفات",
        sport: "رياضة",
        conseils: "نصائح",
        objectifs: "أهداف"
    },
    en: {
        // Navigation
        accueil: "Home",
        utilisateurs: "Users",
        liste: "List",
        ajout: "Add",
        statistiques: "Statistics",
        deconnexion: "Logout",
        
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
        
        // Cards
        publication: "Publication",
        carnet_recettes: "My Recipe Book",
        reserve_durable: "My Sustainable Reserve",
        ton_empreinte: "Your Carbon Footprint",
        
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
    
    // Update cards
    updateText('[data-translate="publication"]', trans.publication);
    updateText('[data-translate="carnet_recettes"]', trans.carnet_recettes);
    updateText('[data-translate="reserve_durable"]', trans.reserve_durable);
    updateText('[data-translate="ton_empreinte"]', trans.ton_empreinte);
    
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
