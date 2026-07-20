<!doctype html>
<html lang="fr">

<head>
    <title>Mon historique - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body data-role="client" data-page="history">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Mon historique</h1>
                <p class="subtitle">Consultez toutes vos opérations.</p>
            </div>

            <div class="cardx pad">
                <div class="d-flex justify-content-between mb-3">
                    <h2 class="h5 fw-bold">Toutes les opérations</h2>
                    <span class="badge badge-primary rounded-pill"><?= count($historique) ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Frais</th>
                                <th>Solde après</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($historique)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
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
                                        <td><?= number_format($tx['solde_apres'], 0, ',', ' ') ?> Ar</td>
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
            ["/dashboard", "dashboard", "bi-house", "Accueil"],
            ["/depot", "deposit", "bi-wallet2", "Dépôt"],
            ["/retrait", "withdraw", "bi-cash-stack", "Retrait"],
            ["/transfert", "transfer", "bi-send", "Transfert"],
            ["/historique", "history", "bi-clock-history", "Historique"]
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
