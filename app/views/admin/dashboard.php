<?php
/**
 * Vue - Dashboard Admin (BackOffice)
 */
$baseUrl = '/gestion-allergies';
$pageTitle = 'Dashboard Admin - Gestion des Allergies';
include __DIR__ . '/../layouts/header.php';
?>

<style>
    .circle-wrapper {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }
    .circle-card {
        background: #fff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 18px 40px rgba(34, 50, 80, 0.08);
        border: 1px solid rgba(46, 125, 50, 0.08);
        min-height: 260px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .circle-chart {
        width: 170px;
        height: 170px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        margin: 0 auto 18px;
        position: relative;
        color: #1f2937;
        font-weight: 700;
        box-shadow: inset 0 0 0 16px rgba(46, 125, 50, 0.08);
    }
    .circle-chart span {
        position: relative;
        z-index: 1;
        font-size: 30px;
    }
    .circle-chart::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: conic-gradient(var(--fill-color) 0deg, var(--fill-color) var(--percent), #e2e8f0 var(--percent), #e2e8f0 360deg);
        transform: rotate(-90deg);
    }
    .circle-card h5 {
        margin-bottom: 10px;
        font-weight: 700;
    }
    .circle-card p {
        margin-bottom: 0;
        color: #4b5563;
    }
</style>

<div class="container-fluid my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>👨‍💼 Dashboard Admin</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="admin.php?action=allergies" class="btn btn-primary me-2">Gérer Allergies</a>
            <a href="admin.php?action=traitements" class="btn btn-info me-2">Gérer Traitements</a>
            <a href="../client-dashboard.php" class="btn btn-success">Front Office</a>
        </div>
    </div>

    <?php if (isset($message) && $message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistiques -->
    <?php
        $totalAllergies = intval($data['total_allergies'] ?? 0);
        $totalTraitements = intval($data['total_traitements'] ?? 0);
        $critique = intval($data['allergies_critiques'] ?? 0);
        $critiquePercent = $totalAllergies > 0 ? round($critique * 100 / $totalAllergies) : 0;
        $remaining = max($totalAllergies - $critique, 0);
    ?>
    <div class="circle-wrapper">
        <div class="circle-card">
            <div class="circle-chart" style="--fill-color: #4f46e5; --percent: 360deg;">
                <span><?php echo $totalAllergies; ?></span>
            </div>
            <h5>Total Allergies</h5>
            <p>Nombre total d'allergies enregistrées</p>
        </div>

        <div class="circle-card">
            <div class="circle-chart" style="--fill-color: #ef4444; --percent: <?php echo $critiquePercent * 3.6; ?>deg;">
                <span><?php echo $critiquePercent; ?>%</span>
            </div>
            <h5>Critiques</h5>
            <p><?php echo $critique; ?> allergies critiques sur <?php echo $totalAllergies; ?></p>
        </div>

        <div class="circle-card">
            <div class="circle-chart" style="--fill-color: #0ea5e9; --percent: 360deg;">
                <span><?php echo $totalTraitements; ?></span>
            </div>
            <h5>Total Traitements</h5>
            <p>Nombre total de traitements</p>
        </div>

        <div class="circle-card">
            <div class="circle-chart" style="--fill-color: #22c55e; --percent: <?php echo ($totalAllergies > 0 ? round($remaining * 100 / $totalAllergies) * 3.6 : 0); ?>deg;">
                <span><?php echo $remaining; ?></span>
            </div>
            <h5>Allergies non critiques</h5>
            <p>Allergies restantes sans niveau critique</p>
        </div>
    </div>

    <!-- Dernières allergies -->
    <section class="mt-5">
        <h3 class="mb-3">📋 Dernières Allergies</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Danger</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['dernieres_allergies'])): ?>
                        <?php foreach ($data['dernieres_allergies'] as $allergie): ?>
                            <tr>
                                <td><?php echo $allergie->getId(); ?></td>
                                <td><?php echo htmlspecialchars($allergie->getNom()); ?></td>
                                <td><?php echo htmlspecialchars($allergie->getType()); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo match($allergie->getNiveauDanger()) {
                                            'faible' => 'success',
                                            'moyen' => 'warning',
                                            'élevé' => 'danger',
                                            'critique' => 'dark',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo htmlspecialchars($allergie->getNiveauDanger()); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="admin.php?action=editer_allergie&id=<?php echo $allergie->getId(); ?>" class="btn btn-sm btn-warning">✏️</a>
                                    <a href="admin.php?action=supprimer_allergie&id=<?php echo $allergie->getId(); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr?')">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Aucune allergie</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
