<!doctype html>
<html lang="fr">

<head>
    <title>Connexion - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body class="auth">
    <div class="authcard">
        <section class="visual"><span class="badge text-bg-light text-dark">Mobile Money</span>
            <h1 class="display-5 fw-bold mt-4">Gérez votre argent simplement.</h1>
            <p class="opacity-75">Interface moderne, responsive et réalisée avec Bootstrap.</p>
            <div class="preview"><i class="bi bi-shield-check fs-1"></i>
                <h3 class="h5 mt-3">Accès sécurisé</h3>
                <p class="mb-0 opacity-75">Simulation pédagogique d'un opérateur Mobile Money.</p>
            </div>
        </section>
        <section class="formside">
            <div class="logo mb-4 text-white"><i class="bi bi-wallet2"></i></div>
            <h2 class="fw-bold">Connexion client</h2>
            <p class="text-secondary mb-4">Entrez votre numéro de téléphone pour vous connecter.</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-4">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="/login">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Numéro de téléphone</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input class="form-control" placeholder="Ex: 0341234567" name="numero_telephone"
                            value="<?= old('numero_telephone') ?>" required>
                    </div>
                    <small class="text-secondary">Préfixes acceptés : 032 (Orange), 033 (Airtel), 034/038 (Yas)</small>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3">Se connecter</button>
            </form>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
