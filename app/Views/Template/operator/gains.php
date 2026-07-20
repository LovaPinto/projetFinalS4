<!doctype html>
<html lang="fr">

<head>
    <title>Gains de l'opérateur - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body data-role="operator" data-page="gains">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Gains de l'opérateur</h1>
                <p class="subtitle">Suivez les frais gagnés sur les opérations.</p>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Gains totaux</small><strong><?= number_format($totalFrais, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico green"><i class="bi bi-cash-coin"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Opérations payantes</small><strong><?= $totalOps ?></strong></div>
                        <div class="ico red"><i class="bi bi-receipt"></i></div>
                    </div>
                </div>
            </div>

            <?php if (!empty($gainsParType)): ?>
                <div class="row g-4">
                    <div class="col-xl-6">
                        <div class="cardx pad">
                            <h2 class="h5 fw-bold mb-3">Détail par type d'opération</h2>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Nb opérations</th>
                                            <th>Total frais</th>
                                            <th>Part</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($gainsParType as $g): ?>
                                            <tr>
                                                <td><span class="badge badge-primary"><?= esc($g['libelle']) ?></span></td>
                                                <td><?= $g['nb_operations'] ?></td>
                                                <td><b><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</b></td>
                                                <td><?= $g['pourcentage'] ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="cardx pad">
                            <h2 class="h5 fw-bold mb-3">Répartition des gains</h2>
                            <p class="text-secondary">Les gains correspondent aux frais prélevés sur les retraits et les transferts.</p>
                            <?php foreach ($gainsParType as $g): ?>
                                <?php
                                    $barClass = 'bg-primary';
                                    if ($g['code'] === 'RETRAIT') $barClass = 'bg-warning';
                                    elseif ($g['code'] === 'TRANSFERT') $barClass = 'bg-success';
                                ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small><?= esc($g['libelle']) ?></small>
                                        <small><b><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</b> (<?= $g['pourcentage'] ?>%)</small>
                                    </div>
                                    <div class="progress" style="height:14px">
                                        <div class="progress-bar <?= $barClass ?>" style="width:<?= $g['pourcentage'] ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($gainsParType)): ?>
                                <p class="text-muted">Aucune donnée de gains disponible.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="cardx pad text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted">Aucune donnée de gains disponible pour le moment.</p>
                </div>
            <?php endif; ?>
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
