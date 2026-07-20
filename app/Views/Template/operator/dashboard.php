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
                <p class="subtitle">Vue générale de l'activité de la plateforme MobiCash.</p>
            </div>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success rounded-4"><i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?></div>
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
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Opérateurs externes</small><strong><?= $opExterneCount ?></strong></div>
                        <div class="ico purple"><i class="bi bi-diagram-3"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Transferts internes</small><strong><?= $txInternes ?></strong></div>
                        <div class="ico green"><i class="bi bi-arrow-left-right"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Transferts externes</small><strong><?= $txExternes ?></strong></div>
                        <div class="ico orange"><i class="bi bi-globe"></i></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="cardx metric d-flex justify-content-between">
                        <div><small>Commissions externes</small><strong><?= number_format($totalCommission, 0, ',', ' ') ?> Ar</strong></div>
                        <div class="ico red"><i class="bi bi-cash-coin"></i></div>
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
                                <thead><tr><th>Référence</th><th>Type</th><th>Source</th><th>Dest</th><th>Montant</th><th>Frais</th><th>Commission</th><th>Type Tx</th><th>Date</th></tr></thead>
                                <tbody>
                                <?php if (empty($recentTx)): ?>
                                    <tr><td colspan="9" class="text-center text-muted py-4">Aucune transaction.</td></tr>
                                <?php else: foreach ($recentTx as $tx): ?>
                                    <tr>
                                        <td><code><?= esc($tx['reference']) ?></code></td>
                                        <td><?= esc($tx['type_libelle']) ?></td>
                                        <td><?= esc($tx['tel_source'] ?? '-') ?></td>
                                        <td><?= esc($tx['tel_dest'] ?? '-') ?></td>
                                        <td><b><?= number_format($tx['montant'], 0, ',', ' ') ?> Ar</b></td>
                                        <td><?= number_format($tx['frais'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= number_format($tx['commission'], 0, ',', ' ') ?> Ar</td>
                                        <td>
                                            <?php if ($tx['type_transfert'] === 'EXTERNE'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill">EXTERNE</span>
                                            <?php elseif ($tx['type_transfert'] === 'INTERNE'): ?>
                                                <span class="badge badge-ok rounded-pill">INTERNE</span>
                                            <?php else: ?>
                                                <span class="badge badge-primary rounded-pill">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($tx['date_creation'])) ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="cardx pad mb-4">
                        <h2 class="h5 fw-bold mb-3">Montant à reverser</h2>
                        <div class="balance" style="background:linear-gradient(135deg,#ff6b35,#d63031);box-shadow:0 18px 40px #d6303140">
                            <span>Reste à reverser</span>
                            <strong style="font-size:2rem"><?= number_format($montantRestantReverser, 0, ',', ' ') ?> Ar</strong>
                        </div>
                        <a class="btn btn-light text-start mt-3 w-100" href="/operator/reversments"><i class="bi bi-cash-stack me-2"></i>Voir les reversments</a>
                    </div>
                    <div class="cardx pad">
                        <h2 class="h5 fw-bold mb-3">Actions rapides</h2>
                        <div class="d-grid gap-2">
                            <a class="btn btn-light text-start" href="/operator/operators"><i class="bi bi-diagram-3 me-2"></i>Gérer les opérateurs</a>
                            <a class="btn btn-light text-start" href="/operator/prefixes"><i class="bi bi-telephone me-2"></i>Gérer les préfixes</a>
                            <a class="btn btn-light text-start" href="/operator/fees"><i class="bi bi-percent me-2"></i>Configurer les frais</a>
                            <a class="btn btn-light text-start" href="/operator/gains"><i class="bi bi-graph-up-arrow me-2"></i>Voir les gains</a>
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
