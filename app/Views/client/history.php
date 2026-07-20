<?= $this->extend('layouts/client') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Historique des transactions</h1>
            <p class="subtitle">Consultez toutes vos opérations</p>
        </div>
        <a href="<?= site_url('client/dashboard') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i> Retour</a>
    </div>

    <div class="cardx pad mb-4">
        <form method="GET" action="<?= site_url('client/history') ?>" class="d-flex gap-3 flex-wrap align-items-end">
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
                <label class="form-label fw-semibold small">Date début</label>
                <input type="date" name="date_start" class="form-control form-control-sm" value="<?= esc($dateStart) ?>">
            </div>
            <div>
                <label class="form-label fw-semibold small">Date fin</label>
                <input type="date" name="date_end" class="form-control form-control-sm" value="<?= esc($dateEnd) ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i> Filtrer</button>
        </form>
    </div>

    <div class="cardx pad">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Sens</th>
                        <th>Autre client</th>
                        <th>Montant</th>
                        <th>Frais</th>
                        <th>Total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="9" class="text-center text-secondary">Aucune transaction</td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                            <?php
                            $clientId = session()->get('client_id');
                            $isSource = ($tx['client_source_id'] == $clientId);

                            if ($tx['type_code'] === 'DEPOT') {
                                $sens = 'ENTRANT';
                                $autreClient = '-';
                            } elseif ($tx['type_code'] === 'RETRAIT') {
                                $sens = 'SORTANT';
                                $autreClient = '-';
                            } elseif ($isSource) {
                                $sens = 'SORTANT';
                                $autreClient = $tx['destination_numero'] ?? '-';
                            } else {
                                $sens = 'ENTRANT';
                                $autreClient = $tx['source_numero'] ?? '-';
                            }
                            ?>
                            <tr>
                                <td><code><?= esc($tx['reference']) ?></code></td>
                                <td><?= date('d/m/Y H:i', strtotime($tx['date_creation'])) ?></td>
                                <td><span class="badge badge-primary"><?= esc($tx['type_libelle']) ?></span></td>
                                <td>
                                    <?php if ($sens === 'ENTRANT'): ?>
                                        <span class="badge badge-ok">ENTRANT</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">SORTANT</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($autreClient) ?></td>
                                <td><b><?= number_format($tx['montant'], 0, ',', ' ') ?> Ar</b></td>
                                <td><?= number_format($tx['frais'], 0, ',', ' ') ?> Ar</td>
                                <td><b><?= number_format($tx['montant_total'], 0, ',', ' ') ?> Ar</b></td>
                                <td>
                                    <?php if ($tx['statut'] === 'REUSSI'): ?>
                                        <span class="badge badge-ok rounded-pill">RÉUSSI</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger rounded-pill"><?= esc($tx['statut']) ?></span>
                                    <?php endif; ?>
                                </td>
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
                            <a class="page-link" href="<?= site_url('client/history?page=' . $i . '&type=' . urlencode($typeFilter) . '&date_start=' . urlencode($dateStart) . '&date_end=' . urlencode($dateEnd)) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
