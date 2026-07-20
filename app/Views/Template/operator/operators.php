<!doctype html>
<html lang="fr">
<head>
    <title>Gestion des opérateurs - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body data-role="operator" data-page="operators">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Gestion des opérateurs</h1>
                <p class="subtitle">Ajoutez, modifiez et gérez les opérateurs télécom.</p>
            </div>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded-4"><i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-4"><i class="bi bi-exclamation-circle me-1"></i><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <div class="cardx pad mb-4">
                <h2 class="h5 fw-bold mb-3">Ajouter un opérateur</h2>
                <form method="POST" action="/operator/operators/add" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-3"><label class="form-label">Nom</label><input name="nom" class="form-control" placeholder="Ex: Telma" required></div>
                    <div class="col-md-2"><label class="form-label">Code</label><input name="code" class="form-control" placeholder="Ex: TEL" maxlength="10" required></div>
                    <div class="col-md-2"><label class="form-label">Commission (%)</label><input name="commission_pct" type="number" step="0.01" class="form-control" value="2.00" min="0" max="100" required></div>
                    <div class="col-md-2"><label class="form-label">&nbsp;</label>
                        <div class="form-check form-switch mt-2"><input class="form-check-input" type="checkbox" name="est_principal" value="1"><label class="form-check-label">Opérateur principal</label></div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end"><button class="btn btn-primary w-100">Ajouter</button></div>
                </form>
            </div>
            <div class="cardx pad">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Nom</th><th>Code</th><th>Commission</th><th>Préfixes</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php if (empty($operateurs)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Aucun opérateur enregistré.</td></tr>
                        <?php else: foreach ($operateurs as $op): ?>
                            <tr>
                                <td><b><?= esc($op['nom']) ?></b></td>
                                <td><code><?= esc($op['code']) ?></code></td>
                                <td><?= $op['commission_pct'] ?>%</td>
                                <td><span class="badge badge-primary rounded-pill"><?= $op['nb_prefixes'] ?></span></td>
                                <td>
                                    <?php if ($op['est_principal']): ?>
                                        <span class="badge bg-primary rounded-pill"><i class="bi bi-star-fill me-1"></i>Principal</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill">Externe</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($op['actif']): ?>
                                        <span class="badge badge-ok rounded-pill">ACTIF</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger rounded-pill">INACTIF</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="/operator/operators/edit/<?= $op['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    <a href="/operator/operators/toggle/<?= $op['id'] ?>" class="btn btn-sm btn-outline-<?= $op['actif'] ? 'danger' : 'success' ?>" onclick="return confirm('Changer le statut ?')"><i class="bi bi-<?= $op['actif'] ? 'pause' : 'play' ?>"></i></a>
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
