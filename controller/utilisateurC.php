<?php
declare(strict_types=1);

/**
 * CONTROLLER — TOUT le SQL est ici (SELECT/INSERT/UPDATE/DELETE)
 * Connexion : config::getConnexion()
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/utilisateur.php';

class UtilisateurC
{
    public function afficher(array $filtres = [], string $tri = 'nom_asc'): array
    {
        $pdo = config::getConnexion();
        $sql = 'SELECT id_user, nom_prenom, email, date_creation, role, est_actif, niveau_activite, regime_alimentaire, objectif_sante, objectif_eco
                FROM utilisateur';
        $where = [];
        $params = [];

        $nom = trim((string) ($filtres['nom'] ?? ''));
        if ($nom !== '') {
            $where[] = 'nom_prenom LIKE ?';
            $params[] = '%' . $nom . '%';
        }

        $email = trim((string) ($filtres['email'] ?? ''));
        if ($email !== '') {
            $where[] = 'email LIKE ?';
            $params[] = '%' . $email . '%';
        }

        $role = trim((string) ($filtres['role'] ?? ''));
        if ($role !== '') {
            $where[] = 'role = ?';
            $params[] = $role;
        }

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $trisAutorises = [
            'nom_asc' => 'nom_prenom ASC, id_user DESC',
            'email_asc' => 'email ASC, id_user DESC',
            'role_asc' => 'role ASC, id_user DESC',
        ];
        $ordre = $trisAutorises[$tri] ?? $trisAutorises['nom_asc'];
        $sql .= ' ORDER BY ' . $ordre;

        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function statistiques(): array
    {
        $pdo = config::getConnexion();
        $sql = 'SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN est_actif = 1 THEN 1 ELSE 0 END) AS actifs,
                    SUM(CASE WHEN role = "admin" THEN 1 ELSE 0 END) AS admins,
                    SUM(CASE WHEN role = "utilisateur" AND est_actif = 1 THEN 1 ELSE 0 END) AS utilisateurs_actifs,
                    SUM(CASE WHEN role = "utilisateur" AND est_actif = 0 THEN 1 ELSE 0 END) AS utilisateurs_inactifs
                FROM utilisateur';
        $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total' => (int) ($row['total'] ?? 0),
            'actifs' => (int) ($row['actifs'] ?? 0),
            'inactifs' => (int) (($row['total'] ?? 0) - ($row['actifs'] ?? 0)),
            'admins' => (int) ($row['admins'] ?? 0),
            'utilisateurs_actifs' => (int) ($row['utilisateurs_actifs'] ?? 0),
            'utilisateurs_inactifs' => (int) ($row['utilisateurs_inactifs'] ?? 0),
        ];
    }

    public function recuperer(int $id): ?array
    {
        $pdo = config::getConnexion();
        $st = $pdo->prepare('SELECT * FROM utilisateur WHERE id_user = ?');
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function ajouter(UtilisateurEntity $u): void
    {
        $pdo = config::getConnexion();
        $sql = 'INSERT INTO utilisateur
                (nom_prenom, email, mot_de_passe, date_creation, niveau_activite, regime_alimentaire, objectif_sante, objectif_eco, role, est_actif)
                VALUES (?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?)';
        $st = $pdo->prepare($sql);
        $hash = password_hash($u->getMot_de_passe(), PASSWORD_DEFAULT);
        $st->execute([
            trim($u->getNom_prenom()),
            trim($u->getEmail()),
            $hash,
            trim($u->getNiveau_activite()),
            trim($u->getRegime_alimentaire()),
            trim($u->getObjectif_sante()),
            trim($u->getObjectif_eco()),
            $u->getRole() ?: 'utilisateur',
            (int) $u->getEst_actif(),
        ]);
    }

    public function modifier(UtilisateurEntity $u, bool $changerMdp = false): void
    {
        $pdo = config::getConnexion();
        $id = (int) ($u->getId_user() ?? 0);
        if ($id <= 0) {
            throw new InvalidArgumentException('ID utilisateur invalide.');
        }

        if ($changerMdp) {
            $sql = 'UPDATE utilisateur
                    SET nom_prenom=?, email=?, mot_de_passe=?, niveau_activite=?, regime_alimentaire=?, objectif_sante=?, objectif_eco=?, role=?, est_actif=?
                    WHERE id_user=?';
            $hash = password_hash($u->getMot_de_passe(), PASSWORD_DEFAULT);
            $params = [
                trim($u->getNom_prenom()),
                trim($u->getEmail()),
                $hash,
                trim($u->getNiveau_activite()),
                trim($u->getRegime_alimentaire()),
                trim($u->getObjectif_sante()),
                trim($u->getObjectif_eco()),
                $u->getRole() ?: 'utilisateur',
                (int) $u->getEst_actif(),
                $id,
            ];
        } else {
            $sql = 'UPDATE utilisateur
                    SET nom_prenom=?, email=?, niveau_activite=?, regime_alimentaire=?, objectif_sante=?, objectif_eco=?, role=?, est_actif=?
                    WHERE id_user=?';
            $params = [
                trim($u->getNom_prenom()),
                trim($u->getEmail()),
                trim($u->getNiveau_activite()),
                trim($u->getRegime_alimentaire()),
                trim($u->getObjectif_sante()),
                trim($u->getObjectif_eco()),
                $u->getRole() ?: 'utilisateur',
                (int) $u->getEst_actif(),
                $id,
            ];
        }

        $pdo->prepare($sql)->execute($params);
    }

    public function supprimer(int $id): void
    {
        $pdo = config::getConnexion();
        $pdo->prepare('DELETE FROM utilisateur WHERE id_user = ?')->execute([$id]);
    }

    /**
     * Used by login: returns row including mot_de_passe hash, or false.
     */
    public function findByEmailForAuth(string $email): ?array
    {
        $pdo = config::getConnexion();
        $st = $pdo->prepare('SELECT id_user, nom_prenom, email, mot_de_passe, role, est_actif FROM utilisateur WHERE email = ? LIMIT 1');
        $st->execute([$email]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Register a new user
     */
    public function register(array $data): int
    {
        $pdo = config::getConnexion();
        $hash = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        $sql = 'INSERT INTO utilisateur
                (nom_prenom, email, mot_de_passe, date_creation, niveau_activite, regime_alimentaire, objectif_sante, objectif_eco, role, est_actif)
                VALUES (?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?)';
        $st = $pdo->prepare($sql);
        $st->execute([
            trim($data['nom_prenom']),
            trim($data['email']),
            $hash,
            trim($data['niveau_activite'] ?? ''),
            trim($data['regime_alimentaire'] ?? ''),
            trim($data['objectif_sante'] ?? ''),
            trim($data['objectif_eco'] ?? ''),
            $data['role'] ?? 'utilisateur',
            1, // est_actif
        ]);
        return (int) $pdo->lastInsertId();
    }
}

