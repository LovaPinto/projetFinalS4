<!doctype html>
<html lang="fr">

<head>
    <title>Mon tableau de bord</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>

<body>
    <?= view('Template/client/partials/sidebar', ['activePage' => 'dashboard']) ?>
    <div class="main">
        <?= view('Template/client/partials/topbar', ['client' => $client]) ?>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Mon tableau de bord</h1>
                <p class="subtitle">Consultez votre solde et vos dernières opérations.</p>
            </div>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger rounded-4"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <!-- Solde -->
            <div class="balance mb-4">
                <span>Solde disponible</span>
                <strong><?= number_format((float) $client['solde'], 0, ',', ' ') ?> Ar</strong>
                <span><i class="bi bi-telephone me-2"></i><?= esc($client['numero_telephone']) ?></span>
            </div>

            <!-- Actions rapides -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <a class="action" href="/deposit">
                        <div class="ico green mb-3"><i class="bi bi-wallet2"></i></div>
                        <h3 class="h6 fw-bold">Faire un dépôt</h3>
                        <p class="text-secondary mb-0">Ajouter de l'argent au compte.</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="action" href="/withdraw">
                        <div class="ico orange mb-3"><i class="bi bi-cash-stack"></i></div>
                        <h3 class="h6 fw-bold">Faire un retrait</h3>
                        <p class="text-secondary mb-0">Retirer avec calcul des frais.</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="action" href="/transfer">
                        <div class="ico purple mb-3"><i class="bi bi-send"></i></div>
                        <h3 class="h6 fw-bold">Faire un transfert</h3>
                        <p class="text-secondary mb-0">Envoyer vers un autre numéro.</p>
                    </a>
                </div>
            </div>

            <!-- Historique -->
            <div class="cardx pad">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold mb-0">Dernières opérations</h2>
                    <a class="btn soft btn-sm" href="/history">Tout voir</a>
                </div>

                <?php if (empty($historique)) : ?>
                    <p class="text-secondary mb-0">Aucune opération pour le moment.</p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Détail</th>
                                    <th>Montant</th>
                                    <th>Frais</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historique as $t) :
                                    $estSource = ((int) $t['client_source_id'] === (int) $client['id']);

                                    if ($t['type_code'] === 'TRANSFERT') {
                                        $detail = $estSource
                                            ? 'Vers ' . esc($t['destination_numero'] ?? '—')
                                            : 'De ' . esc($t['source_numero'] ?? '—');
                                    } else {
                                        $detail = '—';
                                    }

                                    $badge = $t['statut'] === 'REUSSI' ? 'badge-ok' : 'badge-danger';
                                ?>
                                    <tr>
                                        <td><b><?= esc($t['type_libelle']) ?></b></td>
                                        <td><?= $detail ?></td>
                                        <td><b><?= number_format((float) $t['montant'], 0, ',', ' ') ?> Ar</b></td>
                                        <td><?= number_format((float) $t['frais'], 0, ',', ' ') ?> Ar</td>
                                        <td><span class="badge <?= $badge ?> rounded-pill"><?= esc($t['statut']) ?></span></td>
                                        <td><?= date('d/m/Y H:i', strtotime($t['date_creation'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>