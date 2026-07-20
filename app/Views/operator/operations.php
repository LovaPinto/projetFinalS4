<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Types d'opérations</h1>
            <p class="subtitle">Gérer les types d'opérations disponibles</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Ajouter un type</h2>
                <?= form_open(site_url('operator/operations')) ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Code</label>
                        <input type="text" class="form-control" name="code" maxlength="20"
                               placeholder="EXEMPLE" required value="<?= old('code') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Libellé</label>
                        <input type="text" class="form-control" name="libelle" maxlength="50"
                               placeholder="Exemple d'opération" required value="<?= old('libelle') ?>">
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="avec_frais" name="avec_frais" value="1" <?= old('avec_frais') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="avec_frais">Avec frais</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-2"></i> Ajouter
                    </button>
                <?= form_close() ?>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Liste des types d'opérations</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Libellé</th>
                                <th>Code</th>
                                <th>Avec frais</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($operations)): ?>
                                <tr><td colspan="5" class="text-center text-secondary">Aucun type</td></tr>
                            <?php else: ?>
                                <?php foreach ($operations as $op): ?>
                                    <tr>
                                        <td><b><?= esc($op['libelle']) ?></b></td>
                                        <td><code><?= esc($op['code']) ?></code></td>
                                        <td><?= $op['avec_frais'] ? '<span class="badge badge-primary">OUI</span>' : '<span class="text-secondary">NON</span>' ?></td>
                                        <td>
                                            <?php if ($op['actif']): ?>
                                                <span class="badge badge-ok rounded-pill">ACTIF</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger rounded-pill">INACTIF</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?= form_open(site_url('operator/operations/' . $op['id'] . '/toggle'), ['style' => 'display:inline']) ?>
                                                <button type="submit" class="btn btn-sm <?= $op['actif'] ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                                                    <i class="bi <?= $op['actif'] ? 'bi-pause' : 'bi-play' ?>"></i>
                                                </button>
                                            <?= form_close() ?>

                                            <?= form_open(site_url('operator/operations/' . $op['id'] . '/delete'), ['style' => 'display:inline', 'onsubmit' => 'return confirm("Supprimer ce type ?")']) ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?= form_close() ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
