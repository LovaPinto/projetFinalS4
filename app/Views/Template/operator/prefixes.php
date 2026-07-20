<!doctype html>
<html lang="fr">
<head>
    <title>Gestion des préfixes - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body data-role="operator" data-page="prefixes">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Gestion des préfixes</h1>
                <p class="subtitle">Associez chaque préfixe téléphonique à un opérateur.</p>
            </div>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded-4"><i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-4"><i class="bi bi-exclamation-circle me-1"></i><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="cardx pad">
                        <h2 class="h5 fw-bold">Ajouter un préfixe</h2>
                        <form method="POST" action="/operator/prefixes/add">
                            <?= csrf_field() ?>
                            <label class="form-label mt-3">Préfixe</label>
                            <input name="prefixe" class="form-control" placeholder="Ex. 034" maxlength="3" pattern="\d{3}" required>
                            <label class="form-label mt-3">Opérateur</label>
                            <select name="operateur_id" class="form-select" required>
                                <option value="">-- Choisir --</option>
                                <?php foreach ($operateurs as $op): ?>
                                    <option value="<?= $op['id'] ?>"><?= esc($op['nom']) ?><?= $op['est_principal'] ? ' (Principal)' : '' ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-primary w-100 mt-3">Ajouter</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="cardx pad">
                        <div class="table-responsive">
                            <table class="table">
                                <thead><tr><th>Préfixe</th><th>Opérateur</th><th>Statut</th><th>Ajouté le</th><th>Actions</th></tr></thead>
                                <tbody>
                                <?php if (empty($prefixes)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">Aucun préfixe.</td></tr>
                                <?php else: foreach ($prefixes as $p): ?>
                                    <tr>
                                        <td><span class="badge badge-primary rounded-pill fs-6"><?= esc($p['prefixe']) ?></span></td>
                                        <td><?= esc($p['operateur_nom']) ?></td>
                                        <td><?= $p['actif'] ? '<span class="badge badge-ok rounded-pill">ACTIF</span>' : '<span class="badge badge-danger rounded-pill">INACTIF</span>' ?></td>
                                        <td><?= date('d/m/Y', strtotime($p['date_creation'])) ?></td>
                                        <td class="text-end"><a href="/operator/prefixes/delete/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce préfixe ?')"><i class="bi bi-trash"></i></a></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/operator.js"></script>
</body>
</html>
