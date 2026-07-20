<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobiCash - Mobile Money</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="auth">
        <div class="authcard">
            <div class="visual d-flex flex-column justify-content-center">
                <h1 style="font-size:2.5rem;font-weight:800;margin-bottom:16px">MobiCash</h1>
                <p style="font-size:1.1rem;opacity:.9">La solution Mobile Money pour tous vos besoins financiers. Rapide, sécurisé et disponible 24h/24.</p>
                <div class="preview" style="margin-top:40px">
                    <div style="display:flex;gap:16px;align-items:center;margin-bottom:16px">
                        <div class="ico green"><i class="bi bi-shield-check"></i></div>
                        <div><b>Sécurisé</b><br><small style="opacity:.8">Transactions chiffrées</small></div>
                    </div>
                    <div style="display:flex;gap:16px;align-items:center;margin-bottom:16px">
                        <div class="ico orange"><i class="bi bi-lightning"></i></div>
                        <div><b>Instantané</b><br><small style="opacity:.8">Transferts en temps réel</small></div>
                    </div>
                    <div style="display:flex;gap:16px;align-items:center">
                        <div class="ico purple"><i class="bi bi-phone"></i></div>
                        <div><b>Mobile</b><br><small style="opacity:.8">Accessible partout</small></div>
                    </div>
                </div>
            </div>
            <div class="formside d-flex flex-column justify-content-center align-items-center text-center">
                <h2 style="font-weight:700;margin-bottom:12px">Bienvenue</h2>
                <p class="text-secondary mb-4">Choisissez votre espace pour commencer</p>
                <a href="<?= site_url('operator/login') ?>" class="btn btn-primary btn-lg mb-3" style="width:280px;border-radius:14px;padding:14px">
                    <i class="bi bi-person-gear me-2"></i> Espace Opérateur
                </a>
                <a href="<?= site_url('client/login') ?>" class="btn btn-lg mb-3" style="width:280px;border-radius:14px;padding:14px;background:#16b77a;color:#fff;border:0">
                    <i class="bi bi-person me-2"></i> Espace Client
                </a>
                <small class="text-secondary mt-3">Admin: admin@mobile.mg / admin123<br>Client: 0331234567</small>
            </div>
        </div>
    </div>
</body>
</html>
