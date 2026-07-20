<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Opérateur - MobiCash</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <div class="auth">
        <div class="authcard">
            <div class="visual d-flex flex-column justify-content-center">
                <h1 style="font-size:2.2rem;font-weight:800;margin-bottom:12px"><i class="bi bi-wallet2 me-2"></i>MobiCash</h1>
                <p style="font-size:1rem;opacity:.9">Espace Opérateur</p>
                <p style="opacity:.75;margin-top:16px">Connectez-vous pour gérer les opérations Mobile Money, les préfixes, les frais et les comptes clients.</p>
                <div class="preview">
                    <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px">
                        <div class="ico green"><i class="bi bi-shield-check"></i></div>
                        <div><b>Sécurisé</b><br><small style="opacity:.8">Accès protégé</small></div>
                    </div>
                    <div style="display:flex;gap:12px;align-items:center">
                        <div class="ico purple"><i class="bi bi-gear"></i></div>
                        <div><b>Administration</b><br><small style="opacity:.8">Gestion complète</small></div>
                    </div>
                </div>
            </div>
            <div class="formside">
                <h2 style="font-weight:700;margin-bottom:8px">Connexion Opérateur</h2>
                <p class="text-secondary mb-4">Entrez vos identifiants pour accéder à l'administration</p>

                <?= $this->include('partials/alerts') ?>

                <?= form_open(site_url('operator/login'), ['id' => 'loginForm']) ?>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Adresse e-mail</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= old('email') ?>" placeholder="admin@mobile.mg" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="mot_de_passe" class="form-label fw-semibold">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe"
                               placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100" style="border-radius:14px;padding:14px">
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
