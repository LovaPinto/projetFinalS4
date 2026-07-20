<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Tranches de frais</h1>
            <p class="subtitle">Gérer les barèmes de frais par type d'opération</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Ajouter une tranche</h2>
                <?= form_open(site_url('operator/fees')) ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type d'opération</label>
                        <select class="form-select" name="type_operation_id" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= old('type_operation_id') == $t['id'] ? 'selected' : '' ?>>
                                    <?= esc($t['libelle']) ?> (<?= esc($t['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Montant minimum (Ar)</label>
                        <input type="number" class="form-control" name="montant_min" min="0" step="1"
                               required value="<?= old('montant_min') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Montant maximum (Ar)</label>
                        <input type="number" class="form-control" name="montant_max" min="0" step="1"
                               required value="<?= old('montant_max') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Frais (Ar)</label>
                        <input type="number" class="form-control" name="frais" min="0" step="1"
                               required value="<?= old('frais') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-2"></i> Ajouter
                    </button>
                <?= form_close() ?>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="cardx pad">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 fw-bold">Liste des tranches</h2>
                    <form method="GET" action="<?= site_url('operator/fees') ?>" class="d-flex gap-2">
                        <select name="type" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= ($typeFilter ?? '') == $t['id'] ? 'selected' : '' ?>>
                                    <?= esc($t['libelle']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Frais</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($baremes)): ?>
                                <tr><td colspan="6" class="text-center text-secondary">Aucune tranche</td></tr>
                            <?php else: ?>
                                <?php foreach ($baremes as $b): ?>
                                    <tr>
                                        <td><span class="badge badge-primary"><?= esc($b['type_libelle']) ?></span></td>
                                        <td><?= number_format($b['montant_min'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= number_format($b['montant_max'], 0, ',', ' ') ?> Ar</td>
                                        <td><b><?= number_format($b['frais'], 0, ',', ' ') ?> Ar</b></td>
                                        <td>
                                            <?php if ($b['actif']): ?>
                                                <span class="badge badge-ok rounded-pill">ACTIF</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger rounded-pill">INACTIF</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?= form_open(site_url('operator/fees/' . $b['id'] . '/toggle'), ['style' => 'display:inline']) ?>
                                                <button type="submit" class="btn btn-sm <?= $b['actif'] ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                                                    <i class="bi <?= $b['actif'] ? 'bi-pause' : 'bi-play' ?>"></i>
                                                </button>
                                            <?= form_close() ?>

                                            <?= form_open(site_url('operator/fees/' . $b['id'] . '/delete'), ['style' => 'display:inline', 'onsubmit' => 'return confirm("Supprimer cette tranche ?")']) ?>
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
