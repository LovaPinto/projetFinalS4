const menu=[
    ["/operator/dashboard","dashboard","bi-grid","Tableau de bord"],
    ["/operator/operators","operators","bi-diagram-3","Opérateurs"],
    ["/operator/prefixes","prefixes","bi-telephone","Préfixes"],
    ["/operator/operations","operations","bi-arrow-left-right","Types d'opérations"],
    ["/operator/fees","fees","bi-percent","Tranches de frais"],
    ["/operator/clients","clients","bi-people","Comptes clients"],
    ["/operator/transactions","transactions","bi-receipt","Transactions"],
    ["/operator/gains","gains","bi-graph-up-arrow","Gains opérateur"],
    ["/operator/reversments","reversments","bi-cash-stack","Montants à reverser"]
];
const page=document.body.dataset.page;
const sb=document.querySelector("#sidebar");
if(sb)sb.innerHTML=`<aside class="sidebar" id="side"><div class="brand"><div class="logo"><i class="bi bi-wallet2"></i></div><div><b>MobiCash</b><br><small>Espace opérateur</small></div></div><nav class="navbox"><div class="navlabel">Navigation</div>${menu.map(x=>`<a class="navlink ${page===x[1]?"active":""}" href="${x[0]}"><i class="bi ${x[2]}"></i>${x[3]}</a>`).join("")}<div class="navlabel">Session</div><a class="navlink" href="/operator/logout"><i class="bi bi-box-arrow-left"></i>Déconnexion</a></nav></aside><div class="overlay" id="ov"></div>`;
const tb=document.querySelector("#topbar");
if(tb)tb.innerHTML=`<header class="topbar"><div class="d-flex align-items-center gap-3"><button id="mb" class="btn btn-light mobile"><i class="bi bi-list"></i></button><div><b>Administration Mobile Money</b><br><small class="text-secondary">Interface de gestion</small></div></div><div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-primary text-white d-grid" style="width:42px;height:42px;place-items:center"><i class="bi bi-person-gear"></i></div></div></header>`;
document.getElementById('mb')?.addEventListener('click',()=>{document.getElementById('side').classList.add('open');document.getElementById('ov').classList.add('show')});
document.getElementById('ov')?.addEventListener('click',()=>{document.getElementById('side').classList.remove('open');document.getElementById('ov').classList.remove('show')});
