<!doctype html>
<html lang="fr">

<head>
    <title>Comptes clients - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body data-role="operator" data-page="clients">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Comptes clients</h1>
                <p class="subtitle">Consultez les soldes et les statuts des clients.</p>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded-4">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="cardx pad mb-4">
                <input id="search" class="form-control" placeholder="Rechercher par nom ou numéro...">
            </div>
            <div class="cardx pad">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Téléphone</th>
                                <th>Opérateur</th>
                                <th>Solde</th>
                                <th>Statut</th>
                                <th>Inscrit le</th>
                                <th>Dernière connexion</th>
                            </tr>
                        </thead>
                        <tbody id="clientTable">
                            <?php if (empty($clients)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Aucun client enregistré.</td></tr>
                            <?php else: ?>
                                <?php foreach ($clients as $c): ?>
                                    <tr>
                                        <td><b><?= esc($c['numero_telephone']) ?></b></td>
                                        <td><?= esc($c['operateur_nom'] ?? 'N/A') ?></td>
                                        <td><b><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</b></td>
                                        <td>
                                            <?php if ($c['statut'] === 'ACTIF'): ?>
                                                <span class="badge badge-ok rounded-pill">ACTIF</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger rounded-pill"><?= esc($c['statut']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($c['date_creation'])) ?></td>
                                        <td><?= $c['date_derniere_connexion'] ? date('d/m/Y H:i', strtotime($c['date_derniere_connexion'])) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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

        document.getElementById('search')?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#clientTable tr').forEach(tr => {
                if (tr.querySelector('td[colspan]')) return;
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
</body>

</html>
