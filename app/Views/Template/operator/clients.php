<!doctype html>
<html lang="fr">
<head>
    <title>Comptes clients - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body data-role="operator" data-page="clients">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Comptes clients</h1>
                <p class="subtitle">Consultez les soldes et les statuts des clients.</p>
            </div>
            <div class="cardx pad mb-4">
                <input id="search" class="form-control" placeholder="Rechercher par numéro ou opérateur...">
            </div>
            <div class="cardx pad">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Téléphone</th><th>Opérateur</th><th>Solde</th><th>Statut</th><th>Inscrit le</th><th>Dernière connexion</th></tr></thead>
                        <tbody id="clientTable">
                        <?php if (empty($clients)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Aucun client.</td></tr>
                        <?php else: foreach ($clients as $c): ?>
                            <tr>
                                <td><b><?= esc($c['numero_telephone']) ?></b></td>
                                <td><?= esc($c['operateur_nom'] ?? 'N/A') ?></td>
                                <td><b><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</b></td>
                                <td><?= $c['statut'] === 'ACTIF' ? '<span class="badge badge-ok rounded-pill">ACTIF</span>' : '<span class="badge badge-danger rounded-pill">' . esc($c['statut']) . '</span>' ?></td>
                                <td><?= date('d/m/Y', strtotime($c['date_creation'])) ?></td>
                                <td><?= $c['date_derniere_connexion'] ? date('d/m/Y H:i', strtotime($c['date_derniere_connexion'])) : '-' ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/operator.js"></script>
    <script>
        document.getElementById('search')?.addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#clientTable tr').forEach(tr => {
                if (tr.querySelector('td[colspan]')) return;
                tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
