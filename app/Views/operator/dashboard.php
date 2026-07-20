<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Tableau de bord</h1>
            <p class="subtitle">Vue d'ensemble de l'activité Mobile Money</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cardx metric d-flex justify-content-between">
                <div>
                    <small>Comptes clients</small>
                    <strong><?= $totalClients ?></strong>
                </div>
                <div class="ico purple"><i class="bi bi-people"></i></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cardx metric d-flex justify-content-between">
                <div>
                    <small>Solde cumulé</small>
                    <strong><?= number_format($soldeCumule, 0, ',', ' ') ?> Ar</strong>
                </div>
                <div class="ico green"><i class="bi bi-wallet2"></i></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cardx metric d-flex justify-content-between">
                <div>
                    <small>Frais collectés</small>
                    <strong><?= number_format($totalFrais, 0, ',', ' ') ?> Ar</strong>
                </div>
                <div class="ico orange"><i class="bi bi-graph-up"></i></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cardx metric d-flex justify-content-between">
                <div>
                    <small>Transactions réussies</small>
                    <strong><?= $totalTx ?></strong>
                </div>
                <div class="ico red"><i class="bi bi-receipt"></i></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-4">
            <div class="cardx metric text-center">
                <small>Total dépôts</small>
                <strong style="color:var(--g)"><?= number_format($totalDepots, 0, ',', ' ') ?> Ar</strong>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="cardx metric text-center">
                <small>Total retraits</small>
                <strong style="color:var(--o)"><?= number_format($totalRetraits, 0, ',', ' ') ?> Ar</strong>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="cardx metric text-center">
                <small>Total transferts</small>
                <strong style="color:var(--p)"><?= number_format($totalTransferts, 0, ',', ' ') ?> Ar</strong>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="cardx pad">
                <div class="d-flex justify-content-between mb-3">
                    <h2 class="h5 fw-bold">Dernières transactions</h2>
                    <a class="btn soft btn-sm" href="<?= site_url('operator/transactions') ?>">Tout voir</a>
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
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lastTx)): ?>
                                <tr><td colspan="7" class="text-center text-secondary">Aucune transaction</td></tr>
                            <?php else: ?>
                                <?php foreach ($lastTx as $tx): ?>
                                    <tr>
                                        <td><code><?= esc($tx['reference']) ?></code></td>
                                        <td><span class="badge badge-primary"><?= esc($tx['type_libelle']) ?></span></td>
                                        <td><?= $tx['source_numero'] ? esc($tx['source_numero']) : '-' ?></td>
                                        <td><?= $tx['destination_numero'] ? esc($tx['destination_numero']) : '-' ?></td>
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
                    <a class="btn btn-light text-start" href="<?= site_url('operator/prefixes') ?>">
                        <i class="bi bi-telephone me-2"></i> Gérer les préfixes
                    </a>
                    <a class="btn btn-light text-start" href="<?= site_url('operator/fees') ?>">
                        <i class="bi bi-percent me-2"></i> Configurer les frais
                    </a>
                    <a class="btn btn-light text-start" href="<?= site_url('operator/clients') ?>">
                        <i class="bi bi-people me-2"></i> Voir les clients
                    </a>
                    <a class="btn btn-light text-start" href="<?= site_url('operator/gains') ?>">
                        <i class="bi bi-graph-up-arrow me-2"></i> Consulter les gains
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
