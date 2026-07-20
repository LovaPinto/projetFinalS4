<?= $this->extend('layouts/client') ?>

<?= $this->section('content') ?>

<div class="content">
    <?= $this->include('partials/alerts') ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="title">Retrait</h1>
            <p class="subtitle">Retirer de l'argent de votre compte</p>
        </div>
        <a href="<?= site_url('client/dashboard') ?>" class="btn btn-light"><i class="bi bi-arrow-left me-2"></i> Retour</a>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="cardx pad">
                <h2 class="h5 fw-bold mb-3">Montant du retrait</h2>
                <?= form_open(site_url('client/withdraw'), ['id' => 'withdrawForm']) ?>
                    <div class="mb-3">
                        <label for="montant" class="form-label fw-semibold">Montant (Ar)</label>
                        <input type="number" class="form-control form-control-lg" id="montant" name="montant"
                               min="100" step="1" placeholder="20000" required
                               value="<?= old('montant') ?>">
                        <small class="text-secondary">Montant minimum : 100 Ar</small>
                    </div>
                    <button type="submit" class="btn btn-lg" style="background:var(--o);color:#fff;border:0;border-radius:14px;padding:14px;width:100%">
                        <i class="bi bi-cash-stack me-2"></i> Effectuer le retrait
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
                    <span class="text-secondary">Frais estimés</span>
                    <b id="displayFees" style="color:var(--o)">0 Ar</b>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="text-secondary">Total débité</span>
                    <b id="displayTotal" style="color:var(--r);font-size:1.2rem">0 Ar</b>
                </div>
                <div id="result"></div>
            </div>
        </div>
    </div>
</div>

<script>
var feeSchedule = {
    'RETRAIT': [
        [100, 1000, 50], [1001, 5000, 50], [5001, 10000, 100],
        [10001, 25000, 200], [25001, 50000, 400], [50001, 100000, 800],
        [100001, 250000, 1500], [250001, 500000, 1500],
        [500001, 1000000, 2500], [1000001, 2000000, 3000]
    ]
};
function calcFee(amount) {
    var schedule = feeSchedule['RETRAIT'];
    for (var i = 0; i < schedule.length; i++) {
        if (amount >= schedule[i][0] && amount <= schedule[i][1]) return schedule[i][2];
    }
    return -1;
}
document.getElementById('montant').addEventListener('input', function(){
    var val = parseInt(this.value) || 0;
    var fee = calcFee(val);
    var total = fee >= 0 ? val + fee : val;
    document.getElementById('displayMontant').textContent = val.toLocaleString('fr-FR') + ' Ar';
    document.getElementById('displayFees').textContent = fee >= 0 ? fee.toLocaleString('fr-FR') + ' Ar' : 'Aucun barème';
    document.getElementById('displayFees').style.color = fee >= 0 ? 'var(--o)' : 'var(--r)';
    document.getElementById('displayTotal').textContent = fee >= 0 ? total.toLocaleString('fr-FR') + ' Ar' : '-';
});
</script>

<?= $this->endSection() ?>
