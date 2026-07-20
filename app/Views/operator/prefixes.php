<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Gestion des préfixes</h1>
            <p class="subtitle">Gérer les préfixes téléphoniques des opérateurs</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Ajouter un préfixe</h2>
                <?= form_open(site_url('operator/prefixes')) ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Préfixe (3 chiffres)</label>
                        <input type="text" class="form-control" name="prefixe" maxlength="3"
                               pattern="[0-9]{3}" placeholder="034" required
                               value="<?= old('prefixe') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Opérateur</label>
                        <select class="form-select" name="operateur_id" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($operateurs as $op): ?>
                                <option value="<?= $op['id'] ?>" <?= old('operateur_id') == $op['id'] ? 'selected' : '' ?>>
                                    <?= esc($op['nom']) ?> (<?= esc($op['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-2"></i> Ajouter
                    </button>
                <?= form_close() ?>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Liste des préfixes</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Préfixe</th>
                                <th>Opérateur</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($prefixes)): ?>
                                <tr><td colspan="5" class="text-center text-secondary">Aucun préfixe</td></tr>
                            <?php else: ?>
                                <?php foreach ($prefixes as $p): ?>
                                    <tr>
                                        <td><span class="badge badge-primary rounded-pill fs-6"><?= esc($p['prefixe']) ?></span></td>
                                        <td><?= esc($p['operateur_nom']) ?></td>
                                        <td>
                                            <?php if ($p['actif']): ?>
                                                <span class="badge badge-ok rounded-pill">ACTIF</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger rounded-pill">INACTIF</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($p['date_creation'])) ?></td>
                                        <td class="text-end">
                                            <?= form_open(site_url('operator/prefixes/' . $p['id'] . '/toggle'), ['style' => 'display:inline']) ?>
                                                <button type="submit" class="btn btn-sm <?= $p['actif'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $p['actif'] ? 'Désactiver' : 'Activer' ?>">
                                                    <i class="bi <?= $p['actif'] ? 'bi-pause' : 'bi-play' ?>"></i>
                                                </button>
                                            <?= form_close() ?>

                                            <?= form_open(site_url('operator/prefixes/' . $p['id'] . '/delete'), ['style' => 'display:inline', 'onsubmit' => 'return confirm("Supprimer ce préfixe ?")']) ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
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
