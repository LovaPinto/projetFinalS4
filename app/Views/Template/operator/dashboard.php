<!doctype html>
<html lang="fr">

<head>
    <title>Tableau de bord - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body data-role="operator" data-page="dashboard">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Tableau de bord</h1>
                <p class="subtitle">Vue générale de l'activité de l'opérateur.</p>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded-4">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Comptes clients</small><strong><?= $totalClients ?></strong></div>
                        <div class="ico purple"><i class="bi bi-people"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Solde total</small><strong><?= number_format($totalBalance, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico green"><i class="bi bi-wallet2"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Frais collectés</small><strong><?= number_format($totalFrais, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico orange"><i class="bi bi-graph-up"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Transactions</small><strong><?= $totalTx ?></strong></div>
                        <div class="ico red"><i class="bi bi-receipt"></i></div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-8">
                    <div class="cardx pad">
                        <div class="d-flex justify-content-between mb-3">
                            <h2 class="h5 fw-bold">Dernières transactions</h2>
                            <a class="btn soft btn-sm" href="/operator/transactions">Tout voir</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Type</th>
                                        <th>Client</th>
                                        <th>Montant</th>
                                        <th>Frais</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentTx)): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-4">Aucune transaction.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recentTx as $tx): ?>
                                            <tr>
                                                <td><code><?= esc($tx['reference']) ?></code></td>
                                                <td><?= esc($tx['type_libelle']) ?></td>
                                                <td><?= esc($tx['numero_telephone'] ?? '-') ?></td>
                                                <td><b><?= number_format($tx['montant'], 0, ',', ' ') ?> Ar</b></td>
                                                <td><?= number_format($tx['frais'], 0, ',', ' ') ?> Ar</td>
                                                <td><?= date('d/m/Y H:i', strtotime($tx['date_creation'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="cardx pad">
                        <h2 class="h5 fw-bold mb-3">Actions rapides</h2>
                        <div class="d-grid gap-2">
                            <a class="btn btn-light text-start" href="/operator/prefixes"><i class="bi bi-telephone me-2"></i>Gérer les préfixes</a>
                            <a class="btn btn-light text-start" href="/operator/fees"><i class="bi bi-percent me-2"></i>Configurer les frais</a>
                            <a class="btn btn-light text-start" href="/operator/clients"><i class="bi bi-people me-2"></i>Voir les clients</a>
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
