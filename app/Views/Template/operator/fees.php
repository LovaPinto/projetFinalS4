<!doctype html>
<html lang="fr">
<head>
    <title>Tranches de frais - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body data-role="operator" data-page="fees">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Tranches de frais</h1>
                <p class="subtitle">Configurez les frais par opération et montant.</p>
            </div>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded-4"><i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-4"><i class="bi bi-exclamation-circle me-1"></i><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <div class="cardx pad mb-4">
                <h2 class="h5 fw-bold mb-3">Ajouter une tranche</h2>
                <form method="POST" action="/operator/fees/add" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-3"><label class="form-label">Opération</label>
                        <select name="type_operation_id" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= esc($t['libelle']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2"><label class="form-label">Minimum</label><input name="montant_min" type="number" class="form-control" required min="0"></div>
                    <div class="col-md-2"><label class="form-label">Maximum</label><input name="montant_max" type="number" class="form-control" required min="0"></div>
                    <div class="col-md-3"><label class="form-label">Frais</label><input name="frais" type="number" class="form-control" required min="0"></div>
                    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Ajouter</button></div>
                </form>
            </div>
            <div class="cardx pad">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Opération</th><th>Minimum</th><th>Maximum</th><th>Frais</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                        <?php if (empty($fees)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Aucune tranche.</td></tr>
                        <?php else: foreach ($fees as $f): ?>
                            <tr>
                                <td><span class="badge badge-primary"><?= esc($f['type_libelle']) ?></span></td>
                                <td><?= number_format($f['montant_min'], 0, ',', ' ') ?> Ar</td>
                                <td><?= number_format($f['montant_max'], 0, ',', ' ') ?> Ar</td>
                                <td><b><?= number_format($f['frais'], 0, ',', ' ') ?> Ar</b></td>
                                <td><?= $f['actif'] ? '<span class="badge badge-ok rounded-pill">ACTIF</span>' : '<span class="badge badge-danger rounded-pill">INACTIF</span>' ?></td>
                                <td class="text-end"><a href="/operator/fees/delete/<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a></td>
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
