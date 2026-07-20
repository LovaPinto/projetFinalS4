<?= $this->extend('layouts/client') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Dépôt</h1>
            <p class="subtitle">Ajouter de l'argent à votre compte</p>
        </div>
        <a href="<?= site_url('client/dashboard') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i> Retour</a>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Montant du dépôt</h2>
                <?= form_open(site_url('client/deposit'), ['id' => 'depositForm']) ?>
                    <div class="mb-3">
                        <label for="montant" class="form-label fw-semibold">Montant (Ar)</label>
                        <input type="number" class="form-control form-control-lg" id="montant" name="montant"
                               min="100" step="1" placeholder="50000" required
                               value="<?= old('montant') ?>">
                        <small class="text-secondary">Montant minimum : 100 Ar</small>
                    </div>
                    <button type="submit" class="btn btn-lg" style="background:#16b77a;color:#fff;border:0;border-radius:14px;padding:14px;width:100%">
                        <i class="bi bi-wallet2 me-2"></i> Effectuer le dépôt
                    </button>
                <?= form_close() ?>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Récapitulatif</h2>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Montant</span>
                    <b id="displayMontant">0 Ar</b>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Frais</span>
                    <b style="color:var(--g)">0 Ar</b>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-secondary">Total à déposer</span>
                    <b id="displayTotal" style="color:var(--g);font-size:1.2rem">0 Ar</b>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('montant').addEventListener('input', function(){
    var val = parseInt(this.value) || 0;
    document.getElementById('displayMontant').textContent = val.toLocaleString('fr-FR') + ' Ar';
    document.getElementById('displayTotal').textContent = val.toLocaleString('fr-FR') + ' Ar';
});
</script>

<?= $this->endSection() ?>
