<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client - MobiCash</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <div class="auth">
        <div class="authcard">
            <div class="visual d-flex flex-column justify-content-center">
                <h1 style="font-size:2.2rem;font-weight:800;margin-bottom:12px"><i class="bi bi-wallet2 me-2"></i>MobiCash</h1>
                <p style="font-size:1rem;opacity:.9">Espace Client</p>
                <p style="opacity:.75;margin-top:16px">Connectez-vous avec votre numéro de téléphone pour effectuer vos opérations Mobile Money.</p>
                <div class="preview">
                    <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
                        <div class="ico green"><i class="bi bi-wallet2"></i></div>
                        <div><b>Dépôt</b><br><small style="opacity:.8">Alimentez votre compte</small></div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
                        <div class="ico orange"><i class="bi bi-cash-stack"></i></div>
                        <div><b>Retrait</b><br><small style="opacity:.8">Retirez en toute sécurité</small></div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:center">
                        <div class="ico purple"><i class="bi bi-send"></i></div>
                        <div><b>Transfert</b><br><small style="opacity:.8">Envoyez facilement</small></div>
                    </div>
                </div>
            </div>
            <div class="formside">
                <h2 style="font-weight:700;margin-bottom:8px">Connexion Client</h2>
                <p class="text-secondary mb-4">Saisissez votre numéro de téléphone</p>

                <?= $this->include('partials/alerts') ?>

                <?= form_open(site_url('client/login'), ['id' => 'loginForm']) ?>
                    <div class="mb-3">
                        <label for="numero_telephone" class="form-label fw-semibold">Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" class="form-control" id="numero_telephone" name="numero_telephone"
                                   pattern="0[0-9]{9}" maxlength="10" placeholder="0331234567"
                                   required autofocus value="<?= old('numero_telephone') ?>">
                        </div>
                        <small class="text-secondary">10 chiffres commençant par 0</small>
                    </div>
                    <button type="submit" class="btn btn-lg w-100" style="border-radius:14px;padding:14px;background:#16b77a;color:#fff;border:0;margin-top:12px">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
                    </button>
                <?= form_close() ?>

                <p class="text-center text-secondary mt-4">
                    <a href="<?= site_url('/') ?>" style="color:var(--p)"><i class="bi bi-arrow-left me-1"></i> Retour à l'accueil</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
