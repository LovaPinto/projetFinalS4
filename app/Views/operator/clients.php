<?= $this->extend('layouts/operator') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Comptes clients</h1>
            <p class="subtitle">Gérer les comptes des clients Mobile Money</p>
        </div>
    </div>

    <div class="cardx pad mb-4">
        <form method="GET" action="<?= site_url('operator/clients') ?>" class="d-flex gap-3 flex-wrap">
            <input type="text" class="form-control" name="search" placeholder="Rechercher par numéro..."
                   value="<?= esc($search) ?>" style="max-width:300px">
            <select name="statut" class="form-select" style="max-width:200px" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="ACTIF" <?= $statut === 'ACTIF' ? 'selected' : '' ?>>Actif</option>
                <option value="BLOQUE" <?= $statut === 'BLOQUE' ? 'selected' : '' ?>>Bloqué</option>
                <option value="SUSPENDU" <?= $statut === 'SUSPENDU' ? 'selected' : '' ?>>Suspendu</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Rechercher</button>
        </form>
    </div>

    <div class="cardx pad">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Opérateur</th>
                        <th>Solde</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th>Dernière connexion</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clients)): ?>
                        <tr><td colspan="7" class="text-center text-secondary">Aucun client trouvé</td></tr>
                    <?php else: ?>
                        <?php foreach ($clients as $c): ?>
                            <tr>
                                <td><b><?= esc($c['numero_telephone']) ?></b></td>
                                <td><?= esc($c['operateur_nom']) ?></td>
                                <td><b><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</b></td>
                                <td>
                                    <?php if ($c['statut'] === 'ACTIF'): ?>
                                        <span class="badge badge-ok rounded-pill">ACTIF</span>
                                    <?php elseif ($c['statut'] === 'BLOQUE'): ?>
                                        <span class="badge badge-danger rounded-pill">BLOQUÉ</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary rounded-pill">SUSPENDU</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($c['date_creation'])) ?></td>
                                <td><?= $c['date_derniere_connexion'] ? date('d/m/Y H:i', strtotime($c['date_derniere_connexion'])) : '-' ?></td>
                                <td class="text-end">
                                    <a href="<?= site_url('operator/clients/' . $c['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($c['statut'] === 'ACTIF'): ?>
                                        <?= form_open(site_url('operator/clients/' . $c['id'] . '/status'), ['style' => 'display:inline', 'onsubmit' => 'return confirm("Bloquer ce client ?")']) ?>
                                            <input type="hidden" name="statut" value="BLOQUE">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Bloquer">
                                                <i class="bi bi-lock"></i>
                                            </button>
                                        <?= form_close() ?>
                                    <?php else: ?>
                                        <?= form_open(site_url('operator/clients/' . $c['id'] . '/status'), ['style' => 'display:inline']) ?>
                                            <input type="hidden" name="statut" value="ACTIF">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Réactiver">
                                                <i class="bi bi-unlock"></i>
                                            </button>
                                        <?= form_close() ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($lastPage > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= site_url('operator/clients?page=' . $i . '&search=' . urlencode($search) . '&statut=' . urlencode($statut)) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
