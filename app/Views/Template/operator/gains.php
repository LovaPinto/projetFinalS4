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
                <p class="subtitle">Frais internes et commissions inter-opérateurs (transactions REUSSI uniquement).</p>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Gains totaux</small><strong><?= number_format($gainsTotaux, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico green"><i class="bi bi-cash-coin"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Frais retrait</small><strong><?= number_format($totalRetrait, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico orange"><i class="bi bi-cash-stack"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Frais transfert interne</small><strong><?= number_format($gainsInternes, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico purple"><i class="bi bi-arrow-left-right"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Frais transfert externe</small><strong><?= number_format($gainsExternesFrais, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico red"><i class="bi bi-globe"></i></div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Commissions versées (externes)</small><strong><?= number_format($gainsExternesCommission, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico red"><i class="bi bi-cash-coin"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Transferts internes (nb)</small><strong><?= $nbInternes ?></strong></div>
                        <div class="ico green"><i class="bi bi-arrow-left-right"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Transferts externes (nb)</small><strong><?= $nbExternes ?></strong></div>
                        <div class="ico orange"><i class="bi bi-globe"></i></div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="cardx pad">
                        <h2 class="h5 fw-bold mb-3">Détail des gains</h2>
                        <div class="table-responsive">
                            <table class="table">
                                <thead><tr><th>Source de gains</th><th>Montant</th></tr></thead>
                                <tbody>
                                    <tr><td>Frais de retrait</td><td><b><?= number_format($totalRetrait, 0, ',', ' ') ?> Ar</b></td></tr>
                                    <tr><td>Frais de transfert interne</td><td><b><?= number_format($gainsInternes, 0, ',', ' ') ?> Ar</b></td></tr>
                                    <tr><td>Frais de transfert externe</td><td><b><?= number_format($gainsExternesFrais, 0, ',', ' ') ?> Ar</b></td></tr>
                                    <tr class="table-danger"><td>Commissions versées (externe)</td><td><b>-<?= number_format($gainsExternesCommission, 0, ',', ' ') ?> Ar</b></td></tr>
                                    <tr class="table-primary"><td><b>Gains nets</b></td><td><b><?= number_format($gainsTotaux, 0, ',', ' ') ?> Ar</b></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="cardx pad">
                        <h2 class="h5 fw-bold mb-3">Répartition</h2>
                        <p class="text-secondary">Seuls les frais sont des gains. Le montant transféré n'est pas un gain.</p>
                        <?php
                        $totalP = $gainsTotaux > 0 ? $gainsTotaux : 1;
                        $pRetrait = round(($totalRetrait / $totalP) * 100, 1);
                        $pInt = round(($gainsInternes / $totalP) * 100, 1);
                        $pExt = round(($gainsExternesFrais / $totalP) * 100, 1);
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1"><small>Retraits</small><small><b><?= number_format($totalRetrait, 0, ',', ' ') ?> Ar</b> (<?= $pRetrait ?>%)</small></div>
                            <div class="progress" style="height:14px"><div class="progress-bar bg-warning" style="width:<?= $pRetrait ?>%"></div></div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1"><small>Transferts internes</small><small><b><?= number_format($gainsInternes, 0, ',', ' ') ?> Ar</b> (<?= $pInt ?>%)</small></div>
                            <div class="progress" style="height:14px"><div class="progress-bar" style="width:<?= $pInt ?>%"></div></div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1"><small>Transferts externes</small><small><b><?= number_format($gainsExternesFrais, 0, ',', ' ') ?> Ar</b> (<?= $pExt ?>%)</small></div>
                            <div class="progress" style="height:14px"><div class="progress-bar bg-success" style="width:<?= $pExt ?>%"></div></div>
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
