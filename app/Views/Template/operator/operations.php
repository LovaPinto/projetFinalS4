<!doctype html>
<html lang="fr">
<head>
    <title>Types d'opérations - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body data-role="operator" data-page="operations">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Types d'opérations</h1>
                <p class="subtitle">Gérez les opérations disponibles.</p>
            </div>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded-4"><i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-4"><i class="bi bi-exclamation-circle me-1"></i><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <div class="cardx pad mb-4">
                <h2 class="h5 fw-bold mb-3">Ajouter un type d'opération</h2>
                <form method="POST" action="/operator/operations/add" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-3"><label class="form-label">Code</label><input name="code" class="form-control" placeholder="Ex: DEPOT" required></div>
                    <div class="col-md-5"><label class="form-label">Libellé</label><input name="libelle" class="form-control" placeholder="Ex: Dépôt" required></div>
                    <div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100">Ajouter</button></div>
                </form>
            </div>
            <div class="cardx pad">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Nom</th><th>Code</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php if (empty($types)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Aucun type.</td></tr>
                        <?php else: foreach ($types as $t): ?>
                            <tr>
                                <td><b><?= esc($t['libelle']) ?></b></td>
                                <td><code><?= esc($t['code']) ?></code></td>
                                <td><?= $t['actif'] ? '<span class="badge badge-ok rounded-pill">ACTIF</span>' : '<span class="badge badge-danger rounded-pill">INACTIF</span>' ?></td>
                                <td class="text-end"><a href="/operator/operations/toggle/<?= $t['id'] ?>" class="btn btn-sm btn-outline-secondary"><?= $t['actif'] ? 'Désactiver' : 'Activer' ?></a></td>
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
