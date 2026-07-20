<!doctype html>
<html lang="fr">
<head>
    <title>Montants à reverser - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body data-role="operator" data-page="reversments">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Montants à reverser</h1>
                <p class="subtitle">Montants à envoyer aux opérateurs externes (montant transféré uniquement).</p>
            </div>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded-4"><i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-4">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>En attente</small><strong><?= number_format($totalEnAttente, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico orange"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Envoyé</small><strong><?= number_format($totalEnvoye, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico purple"><i class="bi bi-send"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small> Réglé</small><strong><?= number_format($totalRegle, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico green"><i class="bi bi-check-circle"></i></div>
                    </div>
                </div>
            </div>
            <div class="cardx pad">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>#</th><th>Réf. transaction</th><th>Op. source</th><th>Op. destination</th><th>Tél. source</th><th>Tél. dest</th><th>Montant transféré</th><th>Frais</th><th>Commission</th><th>Date transfert</th><th>Statut</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php if (empty($reversments)): ?>
                            <tr><td colspan="12" class="text-center text-muted py-4">Aucun reversment enregistré.</td></tr>
                        <?php else: foreach ($reversments as $r): ?>
                            <tr>
                                <td><?= $r['id'] ?></td>
                                <td><code><?= esc($r['tx_reference']) ?></code></td>
                                <td><span class="badge badge-primary rounded-pill"><?= esc($r['op_source_nom']) ?></span></td>
                                <td><span class="badge badge-primary rounded-pill"><?= esc($r['op_dest_nom']) ?></span></td>
                                <td><?= esc($r['tel_source'] ?? '-') ?></td>
                                <td><?= esc($r['tel_dest'] ?? '-') ?></td>
                                <td><b><?= number_format($r['tx_montant'], 0, ',', ' ') ?> Ar</b></td>
                                <td><?= number_format($r['tx_frais'], 0, ',', ' ') ?> Ar</td>
                                <td><?= number_format($r['tx_commission'] ?? 0, 0, ',', ' ') ?> Ar</td>
                                <td><?= date('d/m/Y H:i', strtotime($r['tx_date'])) ?></td>
                                <td>
                                    <?php if ($r['statut'] === 'EN_ATTENTE'): ?>
                                        <span class="badge bg-warning text-dark rounded-pill">En attente</span>
                                    <?php elseif ($r['statut'] === 'ENVOYE'): ?>
                                        <span class="badge bg-info text-dark rounded-pill">Envoyé</span>
                                    <?php else: ?>
                                        <span class="badge badge-ok rounded-pill">Réglé</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" action="/operator/reversments/update/<?= $r['id'] ?>" class="d-flex gap-1">
                                        <?= csrf_field() ?>
                                        <select name="statut" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                                            <option value="EN_ATTENTE" <?= $r['statut'] === 'EN_ATTENTE' ? 'selected' : '' ?>>En attente</option>
                                            <option value="ENVOYE" <?= $r['statut'] === 'ENVOYE' ? 'selected' : '' ?>>Envoyé</option>
                                            <option value="REGLE" <?= $r['statut'] === 'REGLE' ? 'selected' : '' ?>>Réglé</option>
                                        </select>
                                    </form>
                                </td>
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
