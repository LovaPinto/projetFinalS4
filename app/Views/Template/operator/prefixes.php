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
                <p class="subtitle">Ajoutez les préfixes téléphoniques autorisés.</p>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded-4">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-4">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
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
                                    <option value="<?= $op['id'] ?>"><?= esc($op['nom']) ?></option>
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
                                <thead>
                                    <tr>
                                        <th>Préfixe</th>
                                        <th>Opérateur</th>
                                        <th>Statut</th>
                                        <th>Ajouté le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($prefixes)): ?>
                                        <tr><td colspan="5" class="text-center text-muted py-4">Aucun préfixe enregistré.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($prefixes as $p): ?>
                                            <tr>
                                                <td><span class="badge badge-primary rounded-pill fs-6"><?= esc($p['prefixe']) ?></span></td>
                                                <td><?= esc($p['operateur_nom']) ?></td>
                                                <td>
                                                    <?php if ($p['actif']): ?>
                                                        <span class="badge badge-ok rounded-pill">ACTIF</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger rounded-pill">INACTIF</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($p['date_creation'])) ?></td>
                                                <td class="text-end">
                                                    <a href="/operator/prefixes/delete/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce préfixe ?')"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const menu = [
            ["/operator/dashboard", "dashboard", "bi-grid", "Tableau de bord"],
            ["/operator/prefixes", "prefixes", "bi-telephone", "Préfixes"],
            ["/operator/operations", "operations", "bi-arrow-left-right", "Types d'opérations"],
            ["/operator/fees", "fees", "bi-percent", "Tranches de frais"],
            ["/operator/clients", "clients", "bi-people", "Comptes clients"],
            ["/operator/transactions", "transactions", "bi-receipt", "Transactions"],
            ["/operator/gains", "gains", "bi-graph-up-arrow", "Gains opérateur"]
        ];
        const page = document.body.dataset.page;
        document.querySelector("#sidebar").innerHTML = `
            <aside class="sidebar" id="side">
                <div class="brand">
                    <div class="logo"><i class="bi bi-wallet2"></i></div>
                    <div><b>MobiCash</b><br><small>Espace opérateur</small></div>
                </div>
                <nav class="navbox">
                    <div class="navlabel">Navigation</div>
                    ${menu.map(x => `<a class="navlink ${page === x[1] ? 'active' : ''}" href="${x[0]}"><i class="bi ${x[2]}"></i>${x[3]}</a>`).join('')}
                    <div class="navlabel">Session</div>
                    <a class="navlink" href="/operator/logout"><i class="bi bi-box-arrow-left"></i>Déconnexion</a>
                </nav>
            </aside>
            <div class="overlay" id="ov"></div>`;
        document.querySelector("#topbar").innerHTML = `
            <header class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button id="mb" class="btn btn-light mobile"><i class="bi bi-list"></i></button>
                    <div>
                        <b>Administration Mobile Money</b><br>
                        <small class="text-secondary">Interface de gestion</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-grid" style="width:42px;height:42px;place-items:center">
                        <i class="bi bi-person-gear"></i>
                    </div>
                </div>
            </header>`;
        document.getElementById('mb')?.addEventListener('click', () => {
            document.getElementById('side').classList.add('open');
            document.getElementById('ov').classList.add('show');
        });
        document.getElementById('ov')?.addEventListener('click', () => {
            document.getElementById('side').classList.remove('open');
            document.getElementById('ov').classList.remove('show');
        });
    </script>
</body>

</html>
