<?= $this->extend('layouts/client') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Mon compte</h1>
            <p class="subtitle">Bienvenue, <?= esc($client['numero_telephone']) ?></p>
        </div>
    </div>

    <div class="balance mb-4">
        <span>Solde disponible</span>
        <strong><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</strong>
        <span>
            <i class="bi bi-telephone me-2"></i><?= esc($client['numero_telephone']) ?>
            &nbsp;|&nbsp;
            Statut : <b><?= esc($client['statut']) ?></b>
            &nbsp;|&nbsp;
            Membre depuis : <?= date('d/m/Y', strtotime($client['date_creation'])) ?>
        </span>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a class="action" href="<?= site_url('client/deposit') ?>">
                <div class="ico green mb-3"><i class="bi bi-wallet2"></i></div>
                <h3 class="h6 fw-bold">Faire un dépôt</h3>
                <p class="text-secondary mb-0">Ajouter de l'argent à votre compte.</p>
            </a>
        </div>
        <div class="col-md-4">
            <a class="action" href="<?= site_url('client/withdraw') ?>">
                <div class="ico orange mb-3"><i class="bi bi-cash-stack"></i></div>
                <h3 class="h6 fw-bold">Faire un retrait</h3>
                <p class="text-secondary mb-0">Retirez avec calcul des frais.</p>
            </a>
        </div>
        <div class="col-md-4">
            <a class="action" href="<?= site_url('client/transfer') ?>">
                <div class="ico purple mb-3"><i class="bi bi-send"></i></div>
                <h3 class="h6 fw-bold">Faire un transfert</h3>
                <p class="text-secondary mb-0">Envoyez vers un autre numéro.</p>
            </a>
        </div>
    </div>

    <div class="cardx pad">
        <div class="d-flex justify-content-between mb-3">
            <h2 class="h5 fw-bold">Dernières opérations</h2>
            <a class="btn soft btn-sm" href="<?= site_url('client/history') ?>">Tout voir</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Type</th>
                        <th>Sens</th>
                        <th>Montant</th>
                        <th>Frais</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historique)): ?>
                        <tr><td colspan="6" class="text-center text-secondary">Aucune transaction</td></tr>
                    <?php else: ?>
                        <?php foreach ($historique as $tx): ?>
                            <?php
                            $isSource = ($tx['client_source_id'] == $client['id']);
                            if ($tx['type_code'] === 'DEPOT') {
                                $sens = 'ENTRANT';
                            } elseif ($tx['type_code'] === 'RETRAIT') {
                                $sens = 'SORTANT';
                            } elseif ($isSource) {
                                $sens = 'SORTANT';
                            } else {
                                $sens = 'ENTRANT';
                            }
                            ?>
                            <tr>
                                <td><code><?= esc($tx['reference']) ?></code></td>
                                <td><span class="badge badge-primary"><?= esc($tx['type_libelle']) ?></span></td>
                                <td>
                                    <?php if ($sens === 'ENTRANT'): ?>
                                        <span class="badge badge-ok">ENTRANT</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">SORTANT</span>
                                    <?php endif; ?>
                                </td>
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

<?= $this->endSection() ?>
