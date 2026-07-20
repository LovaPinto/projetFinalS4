<!doctype html>
<html lang="fr">

<head>
    <title>Faire un transfert - MobiCash</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body data-role="client" data-page="transfer">
    <div id="sidebar"></div>
    <div class="main">
        <div id="topbar"></div>
        <main class="content">
            <div class="mb-4">
                <h1 class="title">Faire un transfert</h1>
                <p class="subtitle">Envoyez de l'argent vers un ou plusieurs clients.</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger rounded-4">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="cardx pad">
                        <form method="POST" action="/transfert/executer" id="transferForm">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Montant total à envoyer</label>
                                <div class="input-group">
                                    <input type="number" name="montant" id="montant" min="1" class="form-control"
                                        placeholder="Ex. 60 000" value="<?= old('montant') ?>" required>
                                    <span class="input-group-text">Ar</span>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0">Bénéficiaires</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addDest">
                                    <i class="bi bi-plus-circle me-1"></i>Ajouter
                                </button>
                            </div>

                            <div id="destList">
                                <div class="input-group mb-2 dest-row">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="destinataires[]" class="form-control"
                                        placeholder="Numéro du destinataire (Ex. 0341234567)" required>
                                    <button type="button" class="btn btn-outline-danger btn-remove dest-remove d-none">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div id="opAlert" class="alert alert-warning rounded-4 mt-2 d-none">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="opAlertMsg"></span>
                            </div>

                            <hr>

                            <div class="mb-3" id="fraisRetraitBlock" style="display:none">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="inclus_frais_retrait" value="1" id="fraisRetraitCheck">
                                    <label class="form-check-label" for="fraisRetraitCheck">
                                        <b>Inclure les frais de retrait du bénéficiaire</b>
                                        <br><small class="text-secondary">L'expéditeur paie les frais de retrait à la place du bénéficiaire.</small>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-send me-2"></i>Confirmer le transfert
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="cardx pad">
                        <h2 class="h5 fw-bold">Résumé</h2>
                        <div class="d-flex justify-content-between py-3 border-bottom">
                            <span>Solde actuel</span>
                            <b><?= number_format($client['solde'], 0, ',', ' ') ?> Ar</b>
                        </div>
                        <div class="d-flex justify-content-between py-3 border-bottom">
                            <span>Montant</span>
                            <b id="summaryMontant">0 Ar</b>
                        </div>
                        <div class="d-flex justify-content-between py-3 border-bottom">
                            <span>Par bénéficiaire</span>
                            <b id="summaryParDest">—</b>
                        </div>
                        <div class="d-flex justify-content-between py-3 border-bottom">
                            <span>Frais transfert</span>
                            <b id="summaryFraisTx">—</b>
                        </div>
                        <div class="d-flex justify-content-between py-3 border-bottom" id="summaryFraisRetRow" style="display:none">
                            <span>Frais retrait</span>
                            <b id="summaryFraisRet">—</b>
                        </div>
                        <div class="d-flex justify-content-between py-3 border-bottom">
                            <span>Total débité</span>
                            <b class="text-danger" id="summaryTotal">—</b>
                        </div>
                        <div class="d-flex justify-content-between py-3">
                            <span>Votre numéro</span>
                            <b><?= esc($client['numero_telephone']) ?></b>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const menu = [
            ["/dashboard", "dashboard", "bi-house", "Accueil"],
            ["/depot", "deposit", "bi-wallet2", "Dépôt"],
            ["/retrait", "withdraw", "bi-cash-stack", "Retrait"],
            ["/transfert", "transfer", "bi-send", "Transfert"],
            ["/historique", "history", "bi-clock-history", "Historique"]
        ];
        const page = document.body.dataset.page;
        document.querySelector("#sidebar").innerHTML = `
            <aside class="sidebar" id="side">
                <div class="brand">
                    <div class="logo"><i class="bi bi-wallet2"></i></div>
                    <div><b>MobiCash</b><br><small>Espace client</small></div>
                </div>
                <nav class="navbox">
                    <div class="navlabel">Navigation</div>
                    ${menu.map(x => `<a class="navlink ${page === x[1] ? 'active' : ''}" href="${x[0]}"><i class="bi ${x[2]}"></i>${x[3]}</a>`).join('')}
                    <div class="navlabel">Session</div>
                    <a class="navlink" href="/logout"><i class="bi bi-box-arrow-left"></i>Déconnexion</a>
                </nav>
            </aside>
            <div class="overlay" id="ov"></div>`;
        document.querySelector("#topbar").innerHTML = `
            <header class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button id="mb" class="btn btn-light mobile"><i class="bi bi-list"></i></button>
                    <div>
                        <b>Bienvenue, <?= esc($client['numero_telephone']) ?></b><br>
                        <small class="text-secondary"><?= esc($operateur['nom'] ?? '') ?></small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-grid" style="width:42px;height:42px;place-items:center">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
            </header>`;
        document.getElementById('mb')?.addEventListener('click', () => {
            document.getElementById('side').classList.add('open');
            document.getElementById('ov').classList.add('show');
        });
        document.getElementById('ov')?.addEventListener('click', () => {
            document.getElementById('side').classList.remove('open');
            document.getElementById('ov').classList.remove('show');
        });
    </script>
    <script>
        const fraisRetraitParOp = <?= json_encode($fraisRetraitParOperateur) ?>;
        const montantInput = document.getElementById('montant');
        const destList = document.getElementById('destList');
        const addDestBtn = document.getElementById('addDest');
        const fraisRetraitBlock = document.getElementById('fraisRetraitBlock');
        const fraisRetraitCheck = document.getElementById('fraisRetraitCheck');
        const summaryMontant = document.getElementById('summaryMontant');
        const summaryParDest = document.getElementById('summaryParDest');
        const summaryFraisTx = document.getElementById('summaryFraisTx');
        const summaryFraisRet = document.getElementById('summaryFraisRet');
        const summaryFraisRetRow = document.getElementById('summaryFraisRetRow');
        const summaryTotal = document.getElementById('summaryTotal');
        const opAlert = document.getElementById('opAlert');
        const opAlertMsg = document.getElementById('opAlertMsg');

        function ar(n) { return new Intl.NumberFormat("fr-FR").format(+n || 0) + " Ar"; }

        function getDestRows() { return destList.querySelectorAll('.dest-row'); }

        function updateRemoveButtons() {
            const rows = getDestRows();
            rows.forEach((row, i) => {
                const btn = row.querySelector('.dest-remove');
                if (rows.length > 1) btn.classList.remove('d-none');
                else btn.classList.add('d-none');
            });
        }

        addDestBtn.addEventListener('click', () => {
            const tpl = destList.querySelector('.dest-row').cloneNode(true);
            tpl.querySelector('input').value = '';
            tpl.querySelector('input').classList.remove('is-invalid');
            destList.appendChild(tpl);
            updateRemoveButtons();
            checkOperateurs();
        });

        destList.addEventListener('click', e => {
            const btn = e.target.closest('.dest-remove');
            if (!btn) return;
            btn.closest('.dest-row').remove();
            updateRemoveButtons();
            checkOperateurs();
        });

        destList.addEventListener('input', e => {
            if (e.target.name === 'destinataires[]') checkOperateurs();
        });

        function prefixToOpId(num) {
            if (!num || num.length < 3) return null;
            const prefix = num.substring(0, 3);
            const map = { '034': 3, '038': 3, '033': 2, '032': 1 };
            return map[prefix] || null;
        }

        function checkOperateurs() {
            const rows = getDestRows();
            const nums = [];
            rows.forEach(r => {
                const v = r.querySelector('input').value.trim();
                if (v.length >= 3) nums.push(v);
            });

            if (nums.length === 0) {
                opAlert.classList.add('d-none');
                fraisRetraitBlock.style.display = 'none';
                fraisRetraitCheck.checked = false;
                updateSummary();
                return;
            }

            const opIds = [...new Set(nums.map(n => prefixToOpId(n)).filter(id => id !== null))];
            const validNums = nums.filter(n => prefixToOpId(n) !== null);

            if (nums.length > 1 && opIds.length > 1) {
                opAlertMsg.textContent = "Tous les bénéficiaires doivent appartenir au même opérateur.";
                opAlert.classList.remove('d-none');
                fraisRetraitBlock.style.display = 'none';
                fraisRetraitCheck.checked = false;
            } else {
                opAlert.classList.add('d-none');
                if (opIds.length === 1 && fraisRetraitParOp[opIds[0]] && validNums.length > 1) {
                    fraisRetraitBlock.style.display = 'block';
                } else {
                    fraisRetraitBlock.style.display = 'none';
                    fraisRetraitCheck.checked = false;
                }
            }
            updateSummary();
        }

        montantInput.addEventListener('input', updateSummary);
        fraisRetraitCheck.addEventListener('change', updateSummary);

        function getFraisTransfert(montant) {
            const tranches = [
                [100, 10000, 100],
                [10001, 50000, 300]
            ];
            for (const [min, max, frais] of tranches) {
                if (montant >= min && montant <= max) return frais;
            }
            return 0;
        }

        function getFraisRetrait(montant) {
            const tranches = [
                [100, 1000, 50],
                [1001, 5000, 50],
                [5001, 10000, 100],
                [10001, 25000, 200],
                [25001, 50000, 400]
            ];
            for (const [min, max, frais] of tranches) {
                if (montant >= min && montant <= max) return frais;
            }
            return 0;
        }

        function updateSummary() {
            const montant = +montantInput.value || 0;
            const nbDest = getDestRows().length;
            const parDest = nbDest > 0 ? montant / nbDest : 0;

            let fraisTx = 0, fraisRet = 0;
            for (let i = 0; i < nbDest; i++) {
                fraisTx += getFraisTransfert(parDest);
                if (fraisRetraitCheck.checked) fraisRet += getFraisRetrait(parDest);
            }

            const total = montant + fraisTx + fraisRet;

            summaryMontant.textContent = ar(montant);
            summaryParDest.textContent = nbDest > 0 ? ar(parDest) + " × " + nbDest : "—";
            summaryFraisTx.textContent = ar(fraisTx);
            summaryFraisRet.textContent = ar(fraisRet);
            summaryFraisRetRow.style.display = fraisRetraitCheck.checked && fraisRet > 0 ? "flex" : "none";
            summaryTotal.textContent = ar(total);
        }

        updateSummary();
    </script>
</body>

</html>
