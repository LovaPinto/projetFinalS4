<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Détails du client</h1>
            <p class="subtitle"><?= esc($client['numero_telephone']) ?> - <?= esc($client['operateur_nom']) ?></p>
        </div>
        <a href="<?= site_url('operator/clients') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i> Retour</a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="cardx metric">
                <small>Numéro</small>
                <strong style="font-size:1.2rem"><?= esc($client['numero_telephone']) ?></strong>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cardx metric">
                <small>Solde</small>
                <strong style="font-size:1.2rem;color:var(--g)"><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</strong>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cardx metric">
                <small>Statut</small>
                <strong style="font-size:1.2rem">
                    <?php if ($client['statut'] === 'ACTIF'): ?>
                        <span class="badge badge-ok">ACTIF</span>
                    <?php elseif ($client['statut'] === 'BLOQUE'): ?>
                        <span class="badge badge-danger">BLOQUÉ</span>
                    <?php else: ?>
                        <span class="badge badge-primary">SUSPENDU</span>
                    <?php endif; ?>
                </strong>
            </div>
        </div>
        <div class="col-md-3">
            <div class="cardx metric">
                <small>Créé le</small>
                <strong style="font-size:1.1rem"><?= date('d/m/Y', strtotime($client['date_creation'])) ?></strong>
            </div>
        </div>
    </div>

    <div class="cardx pad">
        <h2 class="h5 fw-bold mb-3">Transactions du client</h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Type</th>
                        <th>Sens</th>
                        <th>Montant</th>
                        <th>Frais</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historique)): ?>
                        <tr><td colspan="8" class="text-center text-secondary">Aucune transaction</td></tr>
                    <?php else: ?>
                        <?php foreach ($historique as $tx): ?>
                            <?php
                            $isSource = ($tx['client_source_id'] == $client['id']);
                            $isDestination = ($tx['client_destination_id'] == $client['id']);

                            if ($tx['type_code'] === 'DEPOT') {
                                $sens = 'ENTRANT';
                            } elseif ($tx['type_code'] === 'RETRAIT') {
                                $sens = 'SORTANT';
                            } elseif ($isSource) {
                                $sens = 'SORTANT';
                            } else {
                                $sens = 'ENTRANT';
                            }

                            $autreNumero = $isSource ? ($tx['destination_numero'] ?? '-') : ($tx['source_numero'] ?? '-');
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
                                <td><b><?= number_format($tx['montant_total'], 0, ',', ' ') ?> Ar</b></td>
                                <td><span class="badge badge-ok"><?= esc($tx['statut']) ?></span></td>
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
