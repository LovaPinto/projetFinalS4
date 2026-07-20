<!doctype html>
<html lang="fr">

<head>
    <title>Tranches de frais</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body data-role="operator" data-page="fees" data-action="">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Tranches de frais</h1>
                <p class="subtitle">Configurez les frais par opération et montant.</p>
            </div>
            <div class="cardx pad mb-4">
                <form id="feeForm" class="row g-3">
                    <div class="col-md-3"><label class="form-label">Opération</label><select id="feeOperation"
                            class="form-select">
                            <option>RETRAIT</option>
                            <option>TRANSFERT</option>
                        </select></div>
                    <div class="col-md-3"><label class="form-label">Minimum</label><input id="feeMin" type="number"
                            class="form-control" required></div>
                    <div class="col-md-3"><label class="form-label">Maximum</label><input id="feeMax" type="number"
                            class="form-control" required></div>
                    <div class="col-md-3"><label class="form-label">Frais</label><input id="feeAmount" type="number"
                            class="form-control" required></div>
                    <div class="col-12"><button class="btn btn-primary">Ajouter la tranche</button></div>
                </form>
            </div>
            <div class="cardx pad">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Opération</th>
                                <th>Minimum</th>
                                <th>Maximum</th>
                                <th>Frais</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="feeTable"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
</body>

</html>