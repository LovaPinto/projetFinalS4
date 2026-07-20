<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Toutes les transactions</h1>
            <p class="subtitle">Historique complet des opérations</p>
        </div>
    </div>

    <div class="cardx pad mb-4">
        <form method="GET" action="<?= site_url('operator/transactions') ?>" class="d-flex gap-3 flex-wrap align-items-end">
            <div>
                <label class="form-label fw-semibold small">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="DEPOT" <?= $typeFilter === 'DEPOT' ? 'selected' : '' ?>>Dépôt</option>
                    <option value="RETRAIT" <?= $typeFilter === 'RETRAIT' ? 'selected' : '' ?>>Retrait</option>
                    <option value="TRANSFERT" <?= $typeFilter === 'TRANSFERT' ? 'selected' : '' ?>>Transfert</option>
                </select>
            </div>
            <div>
                <label class="form-label fw-semibold small">Statut</label>
                <select name="statut" class="form-select form-select-sm">
                    <option value="">Tous</option>
                    <option value="REUSSI" <?= $statutFilter === 'REUSSI' ? 'selected' : '' ?>>Réussi</option>
                    <option value="ECHEC" <?= $statutFilter === 'ECHEC' ? 'selected' : '' ?>>Échec</option>
                    <option value="ANNULE" <?= $statutFilter === 'ANNULE' ? 'selected' : '' ?>>Annulé</option>
                </select>
            </div>
            <div>
                <label class="form-label fw-semibold small">Date début</label>
                <input type="date" name="date_start" class="form-control form-control-sm" value="<?= esc($dateStart) ?>">
            </div>
            <div>
                <label class="form-label fw-semibold small">Date fin</label>
                <input type="date" name="date_end" class="form-control form-control-sm" value="<?= esc($dateEnd) ?>">
            </div>
            <div>
                <label class="form-label fw-semibold small">Recherche</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Réf. ou numéro" value="<?= esc($search) ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i> Filtrer</button>
        </form>
    </div>

    <div class="cardx pad">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Type</th>
                        <th>Source</th>
                        <th>Destination</th>
                        <th>Montant</th>
                        <th>Frais</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="9" class="text-center text-secondary">Aucune transaction</td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td><code><?= esc($tx['reference']) ?></code></td>
                                <td><span class="badge badge-primary"><?= esc($tx['type_libelle']) ?></span></td>
                                <td><?= $tx['source_numero'] ? esc($tx['source_numero']) : '-' ?></td>
                                <td><?= $tx['destination_numero'] ? esc($tx['destination_numero']) : '-' ?></td>
                                <td><b><?= number_format($tx['montant'], 0, ',', ' ') ?> Ar</b></td>
                                <td><?= number_format($tx['frais'], 0, ',', ' ') ?> Ar</td>
                                <td><b><?= number_format($tx['montant_total'], 0, ',', ' ') ?> Ar</b></td>
                                <td>
                                    <?php if ($tx['statut'] === 'REUSSI'): ?>
                                        <span class="badge badge-ok rounded-pill">RÉUSSI</span>
                                    <?php elseif ($tx['statut'] === 'ECHEC'): ?>
                                        <span class="badge badge-danger rounded-pill">ÉCHEC</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary rounded-pill">ANNULÉ</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($tx['date_creation'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($lastPage > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= site_url('operator/transactions?page=' . $i . '&type=' . urlencode($typeFilter) . '&statut=' . urlencode($statutFilter) . '&search=' . urlencode($search) . '&date_start=' . urlencode($dateStart) . '&date_end=' . urlencode($dateEnd)) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
