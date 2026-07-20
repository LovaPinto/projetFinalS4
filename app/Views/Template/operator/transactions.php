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
                <h1 class="title">Toutes les transactions</h1>
                <p class="subtitle">Consultez toutes les opérations effectuées.</p>
            </div>

            <div class="cardx pad">
                <div class="d-flex justify-content-between mb-3">
                    <h2 class="h5 fw-bold">Historique complet</h2>
                    <span class="badge badge-primary rounded-pill"><?= count($transactions) ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Type</th>
                                <th>Source</th>
                                <th>Destination</th>
                                <th>Montant</th>
                                <th>Frais</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">Aucune transaction enregistrée.</td></tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $tx): ?>
                                    <tr>
                                        <td><code><?= esc($tx['reference']) ?></code></td>
                                        <td><?= esc($tx['type_libelle']) ?></td>
                                        <td><?= esc($tx['client_source_tel'] ?? '-') ?></td>
                                        <td><?= esc($tx['client_dest_tel'] ?? '-') ?></td>
                                        <td><b><?= number_format($tx['montant'], 0, ',', ' ') ?> Ar</b></td>
                                        <td><?= number_format($tx['frais'], 0, ',', ' ') ?> Ar</td>
                                        <td>
                                            <?php if ($tx['statut'] === 'REUSSI'): ?>
                                                <span class="badge badge-ok rounded-pill">Réussi</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger rounded-pill"><?= esc($tx['statut']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($tx['date_creation'])) ?></td>
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
    </script>
</body>

</html>
