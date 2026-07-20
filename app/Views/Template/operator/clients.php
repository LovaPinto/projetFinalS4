<!doctype html>
<html lang="fr">

<head>
    <title>Comptes clients</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body data-role="operator" data-page="clients" data-action="">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Comptes clients</h1>
                <p class="subtitle">Consultez les soldes et les statuts des clients.</p>
            </div>
            <div class="cardx pad mb-4"><input id="search" class="form-control" placeholder="Rechercher par nom ou numéro..."></div>
            <div class="cardx pad">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Solde</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="clientTable"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>

</html>