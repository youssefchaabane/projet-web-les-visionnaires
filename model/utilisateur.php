<?php
declare(strict_types=1);

/**
 * MODEL (Entity pure) — aucune requête SQL ici.
 * Boîte à données: attributs privés + constructeur + getters/setters.
 */
class UtilisateurEntity
{
    private ?int $id_user = null;
    private string $nom_prenom = '';
    private string $email = '';
    private string $mot_de_passe = '';
    private string $niveau_activite = '';
    private string $regime_alimentaire = '';
    private string $objectif_sante = '';
    private string $objectif_eco = '';
    private string $role = 'utilisateur';
    private int $est_actif = 1;
    private ?string $date_creation = null; // YYYY-MM-DD

    public function __construct(
        ?int $id_user = null,
        string $nom_prenom = '',
        string $email = '',
        string $mot_de_passe = '',
        string $niveau_activite = '',
        string $regime_alimentaire = '',
        string $objectif_sante = '',
        string $objectif_eco = '',
        string $role = 'utilisateur',
        int $est_actif = 1,
        ?string $date_creation = null
    ) {
        $this->id_user = $id_user;
        $this->nom_prenom = $nom_prenom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->niveau_activite = $niveau_activite;
        $this->regime_alimentaire = $regime_alimentaire;
        $this->objectif_sante = $objectif_sante;
        $this->objectif_eco = $objectif_eco;
        $this->role = $role;
        $this->est_actif = $est_actif;
        $this->date_creation = $date_creation;
    }

    public function getId_user(): ?int { return $this->id_user; }
    public function setId_user(?int $id_user): void { $this->id_user = $id_user; }

    public function getNom_prenom(): string { return $this->nom_prenom; }
    public function setNom_prenom(string $nom_prenom): void { $this->nom_prenom = $nom_prenom; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getMot_de_passe(): string { return $this->mot_de_passe; }
    public function setMot_de_passe(string $mot_de_passe): void { $this->mot_de_passe = $mot_de_passe; }

    public function getNiveau_activite(): string { return $this->niveau_activite; }
    public function setNiveau_activite(string $niveau_activite): void { $this->niveau_activite = $niveau_activite; }

    public function getRegime_alimentaire(): string { return $this->regime_alimentaire; }
    public function setRegime_alimentaire(string $regime_alimentaire): void { $this->regime_alimentaire = $regime_alimentaire; }

    public function getObjectif_sante(): string { return $this->objectif_sante; }
    public function setObjectif_sante(string $objectif_sante): void { $this->objectif_sante = $objectif_sante; }

    public function getObjectif_eco(): string { return $this->objectif_eco; }
    public function setObjectif_eco(string $objectif_eco): void { $this->objectif_eco = $objectif_eco; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    public function getEst_actif(): int { return $this->est_actif; }
    public function setEst_actif(int $est_actif): void { $this->est_actif = $est_actif; }

    public function getDate_creation(): ?string { return $this->date_creation; }
    public function setDate_creation(?string $date_creation): void { $this->date_creation = $date_creation; }
}

