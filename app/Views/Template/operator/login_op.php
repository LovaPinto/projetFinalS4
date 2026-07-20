<!doctype html>
<html lang="fr">

<head>
    <title>Connexion Opérateur - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body class="auth">
    <div class="authcard">
        <section class="visual"><span class="badge text-bg-light text-dark">Mobile Money</span>
            <h1 class="display-5 fw-bold mt-4">Gérez les opérations simplement.</h1>
            <p class="opacity-75">Interface d'administration de la plateforme Mobile Money.</p>
            <div class="preview"><i class="bi bi-shield-check fs-1"></i>
                <h3 class="h5 mt-3">Accès sécurisé</h3>
                <p class="mb-0 opacity-75">Connectez-vous en tant qu'opérateur pour gérer les comptes, frais et transactions.</p>
            </div>
        </section>
        <section class="formside">
            <div class="logo mb-4 text-white"><i class="bi bi-wallet2"></i></div>
            <h2 class="fw-bold">Connexion opérateur</h2>
            <p class="text-secondary mb-4">Entrez vos identifiants administrateur.</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-4">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= esc($error) ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="/operator/login">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Nom d'utilisateur</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input class="form-control" placeholder="admin" name="username" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input class="form-control" type="password" placeholder="admin" name="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3">Se connecter</button>
                <p class="text-center text-secondary mt-3"><small>Identifiants par défaut : admin / admin</small></p>
            </form>
        </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
