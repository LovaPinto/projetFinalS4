<!doctype html>
<html lang="fr">

<head>
    <title>Faire un transfert - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body data-role="client" data-page="transfer">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Faire un transfert</h1>
                <p class="subtitle">Envoyez de l'argent vers un autre client.</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-4">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="cardx pad">
                        <form method="POST" action="/transfert/executer">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Numéro du destinataire</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="numero_destinataire" class="form-control"
                                        placeholder="Ex. 0341234567" value="<?= old('numero_destinataire') ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Montant à envoyer</label>
                                <div class="input-group">
                                    <input type="number" name="montant" min="1" class="form-control"
                                        placeholder="Ex. 20 000" value="<?= old('montant') ?>" required>
                                    <span class="input-group-text">Ar</span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-send me-2"></i>Confirmer le transfert
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="cardx pad">
                        <h2 class="h5 fw-bold">Résumé</h2>
                        <div class="d-flex justify-content-between py-3 border-bottom">
                            <span>Solde actuel</span>
                            <b><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</b>
                        </div>
                        <div class="d-flex justify-content-between py-3 border-bottom">
                            <span>Frais</span>
                            <b>Calculés selon le barème</b>
                        </div>
                        <div class="d-flex justify-content-between py-3">
                            <span>Votre numéro</span>
                            <b><?= esc($client['numero_telephone']) ?></b>
                        </div>
                    </div>
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
