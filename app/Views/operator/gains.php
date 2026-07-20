<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Gains opérateur</h1>
            <p class="subtitle">Analyse des frais perçus sur les opérations</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cardx metric d-flex justify-content-between">
                <div>
                    <small>Gains totaux</small>
                    <strong style="color:var(--g)"><?= number_format($gainsTotal, 0, ',', ' ') ?> Ar</strong>
                </div>
                <div class="ico green"><i class="bi bi-cash-coin"></i></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cardx metric d-flex justify-content-between">
                <div>
                    <small>Frais de retrait</small>
                    <strong style="color:var(--o)"><?= number_format($gainsRetraits, 0, ',', ' ') ?> Ar</strong>
                </div>
                <div class="ico orange"><i class="bi bi-cash-stack"></i></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cardx metric d-flex justify-content-between">
                <div>
                    <small>Frais de transfert</small>
                    <strong style="color:var(--p)"><?= number_format($gainsTransferts, 0, ',', ' ') ?> Ar</strong>
                </div>
                <div class="ico purple"><i class="bi bi-send"></i></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cardx metric d-flex justify-content-between">
                <div>
                    <small>Opérations payantes</small>
                    <strong><?= $nbPayantes ?></strong>
                </div>
                <div class="ico red"><i class="bi bi-receipt"></i></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Répartition par type</h2>
                <?php if (empty($parType)): ?>
                    <p class="text-secondary">Aucune donnée</p>
                <?php else: ?>
                    <?php
                    $totalGains = array_sum(array_column($parType, 'total_frais'));
                    ?>
                    <?php foreach ($parType as $pt): ?>
                        <?php $pct = $totalGains > 0 ? round(($pt['total_frais'] / $totalGains) * 100) : 0; ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><b><?= esc($pt['libelle']) ?></b> (<?= $pt['nb'] ?> ops)</span>
                                <span><b><?= number_format($pt['total_frais'], 0, ',', ' ') ?> Ar</b> (<?= $pct ?>%)</span>
                            </div>
                            <div class="progress" style="height:12px">
                                <div class="progress-bar" style="width:<?= $pct ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Gains par jour</h2>
                <?php if (empty($parJour)): ?>
                    <p class="text-secondary">Aucune donnée</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Frais</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($parJour as $j): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($j['jour'])) ?></td>
                                        <td class="text-end"><b><?= number_format($j['total'], 0, ',', ' ') ?> Ar</b></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Gains par mois</h2>
                <?php if (empty($parMois)): ?>
                    <p class="text-secondary">Aucune donnée</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th class="text-end">Frais</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($parMois as $m): ?>
                                    <tr>
                                        <td><b><?= esc($m['mois']) ?></b></td>
                                        <td class="text-end"><b><?= number_format($m['total'], 0, ',', ' ') ?> Ar</b></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
