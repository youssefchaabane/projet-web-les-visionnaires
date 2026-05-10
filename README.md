# Projet Web Les Visionnaires - Gestion & Écologie Alimentaire 🥗🌱

Bienvenue dans le projet **Les Visionnaires**, une plateforme web complète conçue pour la gestion intelligente du stock alimentaire, la création de recettes assistée par IA, et l'analyse de l'empreinte carbone.

## 🌟 Fonctionnalités Principales

### 📦 Gestion de Stock Intelligent
- **Tableau de Bord Global** : Statistiques en temps réel sur les produits, catégories et alertes.
- **Visualisation de Données** : Graphiques dynamiques (Doughnut Charts) montrant la répartition du stock par catégorie.
- **Alertes de Péremption** : Suivi automatique des dates d'expiration et des stocks bas.
- **QR Codes** : Génération de QR Codes pour chaque produit afin d'en faciliter le suivi.
- **Export PDF** : Rapports complets du stock générés instantanément.

### 🍽️ Gestion des Recettes & IA
- **Création Assistée par IA** : Intégration de l'API Groq (Llama 3) pour générer des recettes complètes à partir d'un simple nom de plat.
- **Génération d'Images** : Utilisation de l'IA pour créer des visuels de plats.
- **Difficulté & Calories** : Analyse automatique de la difficulté et du contenu calorique.
- **Export PDF** : Fiches recettes professionnelles prêtes à l'impression.

### 🌱 Empreinte Carbone (EcoSave)
- **Analyse Carbone** : Calcul du score CO2 pour chaque recette.
- **Facteurs d'Émission** : Base de données des facteurs d'émission (kg CO2 / kg aliment).
- **Tableau de Bord Écologique** : Suivi de l'impact environnemental global de votre alimentation.
- **Assistant IA (Chatbot)** : Un chatbot dédié aux questions sur l'empreinte carbone et l'écologie.

### 💬 Publications & Communauté
- **Gestion des Articles** : Module complet de blog et de partage.
- **Système de Commentaires** : Interaction entre les utilisateurs sur les publications.

---

## 🛠️ Stack Technique

- **Backend** : PHP 8.x (Architecture MVC simplifiée)
- **Base de données** : MySQL / MariaDB
- **Frontend** : HTML5, CSS3 (Design Moderne Glassmorphism), Vanilla JavaScript
- **Graphiques** : Chart.js
- **Intelligence Artificielle** : API Groq (LLM), API de génération d'images
- **Utilitaires** : QRCode.js, jsPDF, Lucide Icons

---

## 🚀 Installation & Configuration

### Prérequis
- Serveur local (XAMPP, WAMP, ou Laragon)
- PHP >= 7.4
- MySQL

### Étapes d'installation
1. **Clonage du projet** :
   Déposez les fichiers dans votre dossier `htdocs` (ou équivalent).

2. **Base de données** :
   - Importez le fichier `database.sql` dans votre serveur MySQL (ex: via phpMyAdmin).
   - **Important** : Si vous rencontrez des erreurs de tables manquantes pour le module de publication, exécutez le script `reparer_pub.php` depuis votre navigateur.

3. **Configuration** :
   - Modifiez le fichier `config/config.php` pour ajuster vos identifiants de connexion à la base de données.
   - Si vous utilisez l'IA, assurez-vous de configurer votre clé API dans les fichiers correspondants.

4. **Accès** :
   Ouvrez votre navigateur sur `http://localhost/votre-dossier-projet/view/login.php`.

---

## 📁 Structure du Projet

- `/view` : Contient toutes les pages d'administration et de client (UI).
- `/controller` : Logique métier et contrôleurs principaux.
- `/model` : Modèles de données et classes d'accès à la base.
- `/config` : Fichiers de configuration de la base de données.
- `/pub` : Module spécifique pour les publications et commentaires.
- `database.sql` : Script principal de création de la base de données.

---

## 👨‍💻 Les Visionnaires
Ce projet a été développé avec une attention particulière portée à l'expérience utilisateur (UX) et à l'esthétique visuelle (UI), en utilisant des effets de flou, des dégradés modernes et des animations fluides.
