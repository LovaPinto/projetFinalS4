<!doctype html>
<html lang="fr">

<head>
    <title>Mon tableau de bord - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body data-role="client" data-page="dashboard">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Mon tableau de bord</h1>
                <p class="subtitle">Bienvenue, <?= esc($client['numero_telephone']) ?> — <?= esc($operateur['nom'] ?? 'N/A') ?></p>
            </div>

            <div class="balance mb-4">
                <span>Solde disponible</span>
                <strong><?= number_format($client['solde']) ?> Ar</strong>
                <span><i class="bi bi-telephone me-2"></i><?= esc($client['numero_telephone']) ?></span>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <a class="action" href="#">
                        <div class="ico green mb-3"><i class="bi bi-wallet2"></i></div>
                        <h3 class="h6 fw-bold">Faire un dépôt</h3>
                        <p class="text-secondary mb-0">Ajouter de l'argent au compte.</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="action" href="#">
                        <div class="ico orange mb-3"><i class="bi bi-cash-stack"></i></div>
                        <h3 class="h6 fw-bold">Faire un retrait</h3>
                        <p class="text-secondary mb-0">Retirer avec calcul des frais.</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="action" href="#">
                        <div class="ico purple mb-3"><i class="bi bi-send"></i></div>
                        <h3 class="h6 fw-bold">Faire un transfert</h3>
                        <p class="text-secondary mb-0">Envoyer vers un autre numéro.</p>
                    </a>
                </div>
            </div>

            <div class="cardx pad">
                <div class="d-flex justify-content-between mb-3">
                    <h2 class="h5 fw-bold">Dernières opérations</h2>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Frais</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historique)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Aucune opération pour le moment.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historique as $tx): ?>
                                    <tr>
                                        <td><code><?= esc($tx['reference']) ?></code></td>
                                        <td><?= esc($tx['type_libelle']) ?></td>
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
        // Sidebar dynamique côté client
        const menu = [
            ["#", "dashboard", "bi-house", "Accueil"],
            ["#", "deposit", "bi-wallet2", "Dépôt"],
            ["#", "withdraw", "bi-cash-stack", "Retrait"],
            ["#", "transfer", "bi-send", "Transfert"],
            ["#", "history", "bi-clock-history", "Historique"]
        ];
        const page = document.body.dataset.page;
        document.querySelector("#sidebar").innerHTML = `
            <aside class="sidebar" id="side">
                <div class="brand">
                    <div class="logo"><i class="bi bi-wallet2"></i></div>
                    <div><b>MobiCash</b><br><small>Espace client</small></div>
                </div>
                <nav class="navbox">
                    <div class="navlabel">Navigation</div>
                    ${menu.map(x => `<a class="navlink ${page === x[1] ? 'active' : ''}" href="${x[0]}"><i class="bi ${x[2]}"></i>${x[3]}</a>`).join('')}
                    <div class="navlabel">Session</div>
                    <a class="navlink" href="/logout"><i class="bi bi-box-arrow-left"></i>Déconnexion</a>
                </nav>
            </aside>
            <div class="overlay" id="ov"></div>`;
        document.querySelector("#topbar").innerHTML = `
            <header class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button id="mb" class="btn btn-light mobile"><i class="bi bi-list"></i></button>
                    <div>
                        <b>Bienvenue, <?= esc($client['numero_telephone']) ?></b><br>
                        <small class="text-secondary"><?= esc($operateur['nom'] ?? '') ?></small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-grid" style="width:42px;height:42px;place-items:center">
                        <i class="bi bi-person"></i>
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
