<!doctype html>
<html lang="fr">
<head>
    <title>Modifier opérateur - MobiCash</title>
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
                <h1 class="title">Modifier l'opérateur</h1>
                <p class="subtitle">Modifiez les informations de <?= esc($operateur['nom']) ?>.</p>
            </div>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-4"><i class="bi bi-exclamation-circle me-1"></i><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <div class="cardx pad" style="max-width:700px">
                <form method="POST" action="/operator/operators/update/<?= $operateur['id'] ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3"><label class="form-label">Nom</label><input name="nom" class="form-control" value="<?= esc($operateur['nom']) ?>" required></div>
                    <div class="mb-3"><label class="form-label">Code</label><input name="code" class="form-control" value="<?= esc($operateur['code']) ?>" maxlength="10" required></div>
                    <div class="mb-3"><label class="form-label">Commission inter-opérateur (%)</label><input name="commission_pct" type="number" step="0.01" class="form-control" value="<?= $operateur['commission_pct'] ?>" min="0" max="100" required></div>
                    <div class="mb-3">
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="est_principal" value="1" <?= $operateur['est_principal'] ? 'checked' : '' ?>><label class="form-check-label">Opérateur principal</label></div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="actif" value="1" <?= $operateur['actif'] ? 'checked' : '' ?>><label class="form-check-label">Actif</label></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a href="/operator/operators" class="btn btn-light">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/operator.js"></script>
</body>
</html>
