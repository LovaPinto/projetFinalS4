<!doctype html>
<html lang="fr">
<head>
    <title>Toutes les transactions - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body data-role="operator" data-page="transactions">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Transactions</h1>
                <p class="subtitle">Consultez toutes les opérations avec filtres avancés.</p>
            </div>
            <div class="cardx pad mb-4">
                <form method="GET" action="/operator/transactions" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Type d'opération</label>
                        <select name="type" class="form-select"><option value="">Tous</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= esc($t['code']) ?>" <?= $filterType === $t['code'] ? 'selected' : '' ?>><?= esc($t['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Opérateur</label>
                        <select name="operateur" class="form-select"><option value="">Tous</option>
                            <?php foreach ($operateurs as $op): ?>
                                <option value="<?= $op['id'] ?>" <?= $filterOp == $op['id'] ? 'selected' : '' ?>><?= esc($op['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type de transfert</label>
                        <select name="type_transfert" class="form-select">
                            <option value="">Tous</option>
                            <option value="INTERNE" <?= $filterTxType === 'INTERNE' ? 'selected' : '' ?>>Interne</option>
                            <option value="EXTERNE" <?= $filterTxType === 'EXTERNE' ? 'selected' : '' ?>>Externe</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filtrer</button>
                    </div>
                </form>
            </div>
            <div class="cardx pad">
                <div class="d-flex justify-content-between mb-3">
                    <h2 class="h5 fw-bold">Résultats</h2>
                    <span class="badge badge-primary rounded-pill"><?= count($transactions) ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Référence</th><th>Type</th><th>Source</th><th>Op. Source</th><th>Destination</th><th>Op. Dest</th><th>Montant</th><th>Frais</th><th>Commission</th><th>Total débité</th><th>Interne/Externe</th><th>Statut</th><th>Date</th></tr></thead>
                        <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr><td colspan="13" class="text-center text-muted py-4">Aucune transaction trouvée.</td></tr>
                        <?php else: foreach ($transactions as $tx): ?>
                            <tr>
                                <td><code><?= esc($tx['reference']) ?></code></td>
                                <td><?= esc($tx['type_libelle']) ?></td>
                                <td><?= esc($tx['tel_source'] ?? '-') ?></td>
                                <td><span class="badge badge-primary rounded-pill"><?= esc($tx['op_source'] ?? '-') ?></span></td>
                                <td><?= esc($tx['tel_dest'] ?? '-') ?></td>
                                <td><span class="badge badge-primary rounded-pill"><?= esc($tx['op_dest'] ?? '-') ?></span></td>
                                <td><b><?= number_format($tx['montant'], 0, ',', ' ') ?> Ar</b></td>
                                <td><?= number_format($tx['frais'], 0, ',', ' ') ?> Ar</td>
                                <td><?= number_format($tx['commission'] ?? 0, 0, ',', ' ') ?> Ar</td>
                                <td><b><?= number_format($tx['montant_total'], 0, ',', ' ') ?> Ar</b></td>
                                <td>
                                    <?php if (($tx['type_transfert'] ?? '') === 'EXTERNE'): ?>
                                        <span class="badge bg-warning text-dark rounded-pill">EXTERNE</span>
                                    <?php elseif (($tx['type_transfert'] ?? '') === 'INTERNE'): ?>
                                        <span class="badge badge-ok rounded-pill">INTERNE</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($tx['statut'] === 'REUSSI'): ?>
                                        <span class="badge badge-ok rounded-pill">Réussi</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger rounded-pill"><?= esc($tx['statut']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($tx['date_creation'])) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/operator.js"></script>
</body>
</html>
