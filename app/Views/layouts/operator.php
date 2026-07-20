<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Opérateur - MobiCash</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body data-role="operator">

    <div id="sidebar"></div>

    <div class="main">
        <div id="topbar"></div>
        <?= $this->renderSection('content') ?>
    </div>

    <div class="overlay" id="ov"></div>

    <script>
    (function(){
        var menu = [
            ['<?= site_url('operator/dashboard') ?>', 'dashboard', 'bi-grid', 'Tableau de bord'],
            ['<?= site_url('operator/prefixes') ?>', 'prefixes', 'bi-telephone', 'Préfixes'],
            ['<?= site_url('operator/operations') ?>', 'operations', 'bi-arrow-left-right', 'Types d\'opérations'],
            ['<?= site_url('operator/fees') ?>', 'fees', 'bi-percent', 'Tranches de frais'],
            ['<?= site_url('operator/clients') ?>', 'clients', 'bi-people', 'Comptes clients'],
            ['<?= site_url('operator/transactions') ?>', 'transactions', 'bi-receipt', 'Transactions'],
            ['<?= site_url('operator/gains') ?>', 'gains', 'bi-graph-up-arrow', 'Gains opérateur']
        ];
        var page = '<?= current_url(true)->getSegment(2) ?? '' ?>';

        document.getElementById('sidebar').innerHTML =
            '<aside class="sidebar" id="side">' +
            '<div class="brand"><div class="logo"><i class="bi bi-wallet2"></i></div><div><b>MobiCash</b><br><small>Espace opérateur</small></div></div>' +
            '<nav class="navbox">' +
            '<div class="navlabel">Navigation</div>' +
            menu.map(function(x){
                return '<a class="navlink '+(page===x[1]?'active':'')+'" href="'+x[0]+'"><i class="bi '+x[2]+'"></i>'+x[3]+'</a>';
            }).join('') +
            '<div class="navlabel">Session</div>' +
            '<a class="navlink" href="<?= site_url('operator/logout') ?>"><i class="bi bi-box-arrow-left"></i>Déconnexion</a>' +
            '</nav></aside>' +
            '<div class="overlay" id="ov"></div>';

        document.getElementById('topbar').innerHTML =
            '<header class="topbar">' +
            '<div class="d-flex align-items-center gap-3">' +
            '<button id="mb" class="btn btn-light mobile"><i class="bi bi-list"></i></button>' +
            '<div><b>Administration Mobile Money</b><br><small class="text-secondary">Espace opérateur</small></div>' +
            '</div>' +
            '<div class="d-flex align-items-center gap-2">' +
            '<div class="rounded-circle bg-primary text-white d-grid" style="width:42px;height:42px;place-items:center"><i class="bi bi-person"></i></div>' +
            '</div></header>';

        var mb = document.getElementById('mb');
        var side = document.getElementById('side');
        var ov = document.getElementById('ov');
        if(mb) mb.addEventListener('click', function(){ side.classList.add('open'); ov.classList.add('show'); });
        if(ov) ov.addEventListener('click', function(){ side.classList.remove('open'); ov.classList.remove('show'); });
    })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
