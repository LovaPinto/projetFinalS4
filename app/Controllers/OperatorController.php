<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use App\Models\OperateurModel;
use App\Models\PrefixOperateurModel;
use App\Models\TypesOperationModel;
use App\Models\BaremesFraisModel;
use App\Models\TransactionModel;
use App\Models\ReversmentModel;

class OperatorController extends BaseController
{
    private function requireAuth()
    {
        if (!session()->get('operator_logged_in')) {
            return redirect()->to('/operator/login');
        }
        return null;
    }

    private function getOperateurPrincipal(): ?array
    {
        $opModel = new OperateurModel();
        return $opModel->where('est_principal', 1)->first();
    }

    public function login()
    {
        if (session()->get('operator_logged_in')) {
            return redirect()->to('/operator/dashboard');
        }
        return view('Template/operator/login_op', [
            'error' => session()->getFlashdata('error'),
        ]);
    }

    public function doLogin()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = trim((string) $this->request->getPost('password'));

        if ($username === 'admin' && $password === 'admin') {
            $opModel    = new OperateurModel();
            $principal  = $opModel->where('est_principal', 1)->first();
            session()->set([
                'operator_logged_in' => true,
                'operator_name'      => 'Administrateur',
                'operator_id'        => $principal['id'] ?? null,
            ]);
            return redirect()->to('/operator/dashboard');
        }

        return redirect()->back()->withInput()
            ->with('error', 'Identifiant ou mot de passe incorrect.');
    }

    public function doLogout()
    {
        session()->remove('operator_logged_in');
        session()->remove('operator_name');
        session()->remove('operator_id');
        return redirect()->to('/operator/login');
    }

    // ─── DASHBOARD V2 ───────────────────────────────────────

    public function dashboard()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $db = \Config\Database::connect();

        $totalClients    = $db->table('clients')->countAllResults();
        $totalBalance    = (float) ($db->table('clients')->selectSum('solde')->get()->getRowArray()['solde'] ?? 0);
        $totalTx         = $db->table('transactions')->countAllResults();
        $totalFrais      = (float) ($db->table('transactions')->selectSum('frais')->get()->getRowArray()['frais'] ?? 0);
        $totalCommission = (float) ($db->table('transactions')->selectSum('commission')->get()->getRowArray()['commission'] ?? 0);
        $opExterneCount  = $db->table('operateurs')->where('est_principal', 0)->where('actif', 1)->countAllResults();

        $txInternes = $db->table('transactions')
            ->where('type_transfert', 'INTERNE')
            ->where('statut', 'REUSSI')
            ->countAllResults();
        $txExternes = $db->table('transactions')
            ->where('type_transfert', 'EXTERNE')
            ->where('statut', 'REUSSI')
            ->countAllResults();

        $montantRestantReverser = (float) ($db->table('reversments')
            ->where('statut', 'EN_ATTENTE')
            ->selectSum('montant')
            ->get()
            ->getRowArray()['montant'] ?? 0);

        $recentTx = $db->query(
            "SELECT tr.*, to2.libelle AS type_libelle, cs.numero_telephone AS tel_source, cd.numero_telephone AS tel_dest
             FROM transactions tr
             JOIN types_operation to2 ON to2.id = tr.type_operation_id
             LEFT JOIN clients cs ON cs.id = tr.client_source_id
             LEFT JOIN clients cd ON cd.id = tr.client_destination_id
             ORDER BY tr.date_creation DESC LIMIT 10"
        )->getResultArray();

        return view('Template/operator/dashboard', [
            'totalClients'          => $totalClients,
            'totalBalance'          => $totalBalance,
            'totalTx'               => $totalTx,
            'totalFrais'            => $totalFrais,
            'totalCommission'       => $totalCommission,
            'opExterneCount'        => $opExterneCount,
            'txInternes'            => $txInternes,
            'txExternes'            => $txExternes,
            'montantRestantReverser'=> $montantRestantReverser,
            'recentTx'              => $recentTx,
        ]);
    }

    // ─── GESTION DES OPERATEURS ─────────────────────────────

    public function operators()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $opModel    = new OperateurModel();
        $prefixModel = new PrefixOperateurModel();

        $allOps = $opModel->orderBy('est_principal', 'DESC')->orderBy('nom', 'ASC')->findAll();

        foreach ($allOps as &$op) {
            $op['nb_prefixes'] = $prefixModel->where('operateur_id', $op['id'])->countAllResults();
        }

        return view('Template/operator/operators', [
            'operateurs' => $allOps,
        ]);
    }

    public function operatorAdd()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $nom      = trim((string) $this->request->getPost('nom'));
        $code     = strtoupper(trim((string) $this->request->getPost('code')));
        $pct      = (float) $this->request->getPost('commission_pct');
        $principal = (int) $this->request->getPost('est_principal');

        if (empty($nom) || empty($code)) {
            return redirect()->back()->with('error', 'Nom et code sont requis.');
        }

        if ($pct < 0 || $pct > 100) {
            return redirect()->back()->with('error', 'Le pourcentage de commission doit être entre 0 et 100.');
        }

        $opModel = new OperateurModel();
        $exists = $opModel->where('code', $code)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Ce code existe déjà.');
        }

        if ($principal) {
            $opModel->where('est_principal', 1)->set('est_principal', 0)->update();
        }

        $opModel->insert([
            'nom'            => $nom,
            'code'           => $code,
            'actif'          => 1,
            'commission_pct' => $pct,
            'est_principal'  => $principal,
            'date_creation'  => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/operator/operators')->with('success', 'Opérateur ajouté avec succès.');
    }

    public function operatorEdit($id)
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $opModel = new OperateurModel();
        $op = $opModel->find($id);
        if (!$op) {
            return redirect()->to('/operator/operators')->with('error', 'Opérateur introuvable.');
        }

        return view('Template/operator/operator_edit', [
            'operateur' => $op,
        ]);
    }

    public function operatorUpdate($id)
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $opModel = new OperateurModel();
        $op = $opModel->find($id);
        if (!$op) {
            return redirect()->to('/operator/operators')->with('error', 'Opérateur introuvable.');
        }

        $nom      = trim((string) $this->request->getPost('nom'));
        $code     = strtoupper(trim((string) $this->request->getPost('code')));
        $pct      = (float) $this->request->getPost('commission_pct');
        $actif    = (int) $this->request->getPost('actif');
        $principal = (int) $this->request->getPost('est_principal');

        if (empty($nom) || empty($code)) {
            return redirect()->back()->with('error', 'Nom et code sont requis.');
        }

        if ($pct < 0 || $pct > 100) {
            return redirect()->back()->with('error', 'Le pourcentage de commission doit être entre 0 et 100.');
        }

        $existsCode = $opModel->where('code', $code)->where('id !=', $id)->first();
        if ($existsCode) {
            return redirect()->back()->with('error', 'Ce code est déjà utilisé par un autre opérateur.');
        }

        if ($principal) {
            $opModel->where('est_principal', 1)->where('id !=', $id)->set('est_principal', 0)->update();
        }

        $opModel->update($id, [
            'nom'            => $nom,
            'code'           => $code,
            'actif'          => $actif,
            'commission_pct' => $pct,
            'est_principal'  => $principal,
        ]);

        return redirect()->to('/operator/operators')->with('success', 'Opérateur mis à jour.');
    }

    public function operatorToggle($id)
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $opModel = new OperateurModel();
        $op = $opModel->find($id);
        if ($op) {
            $opModel->update($id, ['actif' => $op['actif'] ? 0 : 1]);
        }

        return redirect()->to('/operator/operators')->with('success', 'Statut mis à jour.');
    }

    // ─── GESTION DES PREFIXES ───────────────────────────────

    public function prefixes()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $prefixModel   = new PrefixOperateurModel();
        $operateurModel = new OperateurModel();

        $allPrefixes = $prefixModel
            ->select('prefixes_operateur.*, operateurs.nom AS operateur_nom')
            ->join('operateurs', 'operateurs.id = prefixes_operateur.operateur_id')
            ->orderBy('prefixes_operateur.prefixe', 'ASC')
            ->findAll();

        $operateurs = $operateurModel->where('actif', 1)->findAll();

        return view('Template/operator/prefixes', [
            'prefixes'   => $allPrefixes,
            'operateurs' => $operateurs,
        ]);
    }

    public function prefixAdd()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $prefixe     = trim((string) $this->request->getPost('prefixe'));
        $operateurId = (int) $this->request->getPost('operateur_id');

        if (!preg_match('/^\d{3}$/', $prefixe)) {
            return redirect()->back()->with('error', 'Le préfixe doit contenir exactement 3 chiffres.');
        }

        $prefixModel = new PrefixOperateurModel();
        $exists = $prefixModel->where('prefixe', $prefixe)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Ce préfixe existe déjà.');
        }

        $prefixModel->insert([
            'prefixe'       => $prefixe,
            'operateur_id'  => $operateurId,
            'actif'         => 1,
            'date_creation' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/operator/prefixes')->with('success', 'Préfixe ajouté avec succès.');
    }

    public function prefixDelete($id)
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $prefixModel = new PrefixOperateurModel();
        $prefixModel->delete($id);

        return redirect()->to('/operator/prefixes')->with('success', 'Préfixe supprimé.');
    }

    // ─── TYPES D'OPERATIONS ─────────────────────────────────

    public function operations()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $typeModel = new TypesOperationModel();
        $types = $typeModel->findAll();

        return view('Template/operator/operations', [
            'types' => $types,
        ]);
    }

    public function operationAdd()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $code    = strtoupper(trim((string) $this->request->getPost('code')));
        $libelle = trim((string) $this->request->getPost('libelle'));

        if (empty($code) || empty($libelle)) {
            return redirect()->back()->with('error', 'Code et libellé sont requis.');
        }

        $typeModel = new TypesOperationModel();
        $exists = $typeModel->where('code', $code)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Ce code existe déjà.');
        }

        $typeModel->insert([
            'code'    => $code,
            'libelle' => $libelle,
            'actif'   => 1,
        ]);

        return redirect()->to('/operator/operations')->with('success', 'Type d\'opération ajouté.');
    }

    public function operationToggle($id)
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $typeModel = new TypesOperationModel();
        $type = $typeModel->find($id);
        if ($type) {
            $typeModel->update($id, ['actif' => $type['actif'] ? 0 : 1]);
        }

        return redirect()->to('/operator/operations')->with('success', 'Statut mis à jour.');
    }

    // ─── TRANCHES DE FRAIS ──────────────────────────────────

    public function fees()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $baremeModel = new BaremesFraisModel();
        $typeModel   = new TypesOperationModel();

        $allFees = $baremeModel
            ->select('baremes_frais.*, types_operation.code AS type_code, types_operation.libelle AS type_libelle')
            ->join('types_operation', 'types_operation.id = baremes_frais.type_operation_id')
            ->orderBy('baremes_frais.montant_min', 'ASC')
            ->findAll();

        $types = $typeModel->where('actif', 1)->findAll();

        return view('Template/operator/fees', [
            'fees'  => $allFees,
            'types' => $types,
        ]);
    }

    public function feeAdd()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $typeOpId = (int) $this->request->getPost('type_operation_id');
        $min      = (float) $this->request->getPost('montant_min');
        $max      = (float) $this->request->getPost('montant_max');
        $frais    = (float) $this->request->getPost('frais');

        if ($min >= $max) {
            return redirect()->back()->with('error', 'Le minimum doit être inférieur au maximum.');
        }

        $baremeModel = new BaremesFraisModel();
        $baremeModel->insert([
            'type_operation_id' => $typeOpId,
            'montant_min'       => $min,
            'montant_max'       => $max,
            'frais'             => $frais,
            'actif'             => 1,
            'date_debut'        => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/operator/fees')->with('success', 'Tranche de frais ajoutée.');
    }

    public function feeDelete($id)
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $baremeModel = new BaremesFraisModel();
        $baremeModel->delete($id);

        return redirect()->to('/operator/fees')->with('success', 'Tranche de frais supprimée.');
    }

    // ─── COMPTES CLIENTS ────────────────────────────────────

    public function clients()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $clientModel = new ClientsModel();

        $allClients = $clientModel
            ->select('clients.*, operateurs.nom AS operateur_nom')
            ->join('operateurs', 'operateurs.id = clients.operateur_id')
            ->orderBy('clients.date_creation', 'DESC')
            ->findAll();

        return view('Template/operator/clients', [
            'clients' => $allClients,
        ]);
    }

    // ─── TRANSACTIONS V2 (filtres + commission) ──────────────

    public function transactions()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $db = \Config\Database::connect();

        $filterType   = $this->request->getGet('type');
        $filterOp     = $this->request->getGet('operateur');
        $filterTxType = $this->request->getGet('type_transfert');

        $builder = $db->table('transactions tr');
        $builder->select(
            'tr.*, to2.libelle AS type_libelle, 
             cs.numero_telephone AS tel_source, cd.numero_telephone AS tel_dest,
             os.nom AS op_source, od.nom AS op_dest'
        );
        $builder->join('types_operation to2', 'to2.id = tr.type_operation_id');
        $builder->join('clients cs', 'cs.id = tr.client_source_id', 'left');
        $builder->join('clients cd', 'cd.id = tr.client_destination_id', 'left');
        $builder->join('prefixes_operateur ps', "ps.prefixe = SUBSTR(cs.numero_telephone, 1, 3)", 'left');
        $builder->join('operateurs os', 'os.id = ps.operateur_id', 'left');
        $builder->join('prefixes_operateur pd', "pd.prefixe = SUBSTR(cd.numero_telephone, 1, 3)", 'left');
        $builder->join('operateurs od', 'od.id = pd.operateur_id', 'left');

        if (!empty($filterType)) {
            $builder->where('to2.code', $filterType);
        }
        if (!empty($filterTxType)) {
            $builder->where('tr.type_transfert', $filterTxType);
        }
        if (!empty($filterOp)) {
            $builder->groupStart();
            $builder->where('os.id', $filterOp);
            $builder->orWhere('od.id', $filterOp);
            $builder->groupEnd();
        }

        $builder->orderBy('tr.date_creation', 'DESC');
        $allTx = $builder->get()->getResultArray();

        $opModel      = new OperateurModel();
        $allOperateurs = $opModel->where('actif', 1)->findAll();
        $typeModel    = new TypesOperationModel();
        $allTypes     = $typeModel->findAll();

        return view('Template/operator/transactions', [
            'transactions'   => $allTx,
            'operateurs'     => $allOperateurs,
            'types'          => $allTypes,
            'filterType'     => $filterType,
            'filterOp'       => $filterOp,
            'filterTxType'   => $filterTxType,
        ]);
    }

    // ─── GAINS V2 (frais internes + commissions externes) ────

    public function gains()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $db = \Config\Database::connect();

        $gainsInternes = (float) ($db->query(
            "SELECT COALESCE(SUM(tr.frais), 0) AS total
             FROM transactions tr
             WHERE tr.type_transfert = 'INTERNE' AND tr.statut = 'REUSSI'"
        )->getRowArray()['total'] ?? 0);

        $gainsExternesFrais = (float) ($db->query(
            "SELECT COALESCE(SUM(tr.frais), 0) AS total
             FROM transactions tr
             WHERE tr.type_transfert = 'EXTERNE' AND tr.statut = 'REUSSI'"
        )->getRowArray()['total'] ?? 0);

        $gainsExternesCommission = (float) ($db->query(
            "SELECT COALESCE(SUM(tr.commission), 0) AS total
             FROM transactions tr
             WHERE tr.type_transfert = 'EXTERNE' AND tr.statut = 'REUSSI'"
        )->getRowArray()['total'] ?? 0);

        $totalRetrait = (float) ($db->query(
            "SELECT COALESCE(SUM(tr.frais), 0) AS total
             FROM transactions tr
             JOIN types_operation to2 ON to2.id = tr.type_operation_id
             WHERE to2.code = 'RETRAIT' AND tr.statut = 'REUSSI'"
        )->getRowArray()['total'] ?? 0);

        $totalDepot = (float) ($db->query(
            "SELECT COALESCE(SUM(tr.frais), 0) AS total
             FROM transactions tr
             JOIN types_operation to2 ON to2.id = tr.type_operation_id
             WHERE to2.code = 'DEPOT' AND tr.statut = 'REUSSI'"
        )->getRowArray()['total'] ?? 0);

        $nbInternes = $db->table('transactions')
            ->where('type_transfert', 'INTERNE')
            ->where('statut', 'REUSSI')
            ->countAllResults();
        $nbExternes = $db->table('transactions')
            ->where('type_transfert', 'EXTERNE')
            ->where('statut', 'REUSSI')
            ->countAllResults();

        $gainsTotaux = $totalRetrait + $gainsInternes + $gainsExternesFrais - $gainsExternesCommission;

        return view('Template/operator/gains', [
            'gainsInternes'          => $gainsInternes,
            'gainsExternesFrais'     => $gainsExternesFrais,
            'gainsExternesCommission'=> $gainsExternesCommission,
            'totalRetrait'           => $totalRetrait,
            'totalDepot'             => $totalDepot,
            'nbInternes'             => $nbInternes,
            'nbExternes'             => $nbExternes,
            'gainsTotaux'            => $gainsTotaux,
        ]);
    }

    // ─── MONTANTS A REVERSER ────────────────────────────────

    public function reversments()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $db = \Config\Database::connect();

        $allReversments = $db->query(
            "SELECT r.*, tr.reference AS tx_reference, tr.montant AS tx_montant,
                    tr.frais AS tx_frais, tr.commission AS tx_commission,
                    tr.date_creation AS tx_date,
                    os.nom AS op_source_nom, od.nom AS op_dest_nom,
                    cs.numero_telephone AS tel_source, cd.numero_telephone AS tel_dest
             FROM reversments r
             JOIN transactions tr ON tr.id = r.transaction_id
             JOIN operateurs os ON os.id = r.operateur_source_id
             JOIN operateurs od ON od.id = r.operateur_dest_id
             LEFT JOIN clients cs ON cs.id = tr.client_source_id
             LEFT JOIN clients cd ON cd.id = tr.client_destination_id
             ORDER BY r.date_creation DESC"
        )->getResultArray();

        $totalEnAttente = (float) ($db->table('reversments')
            ->where('statut', 'EN_ATTENTE')
            ->selectSum('montant')
            ->get()->getRowArray()['montant'] ?? 0);
        $totalEnvoye = (float) ($db->table('reversments')
            ->where('statut', 'ENVOYE')
            ->selectSum('montant')
            ->get()->getRowArray()['montant'] ?? 0);
        $totalRegle = (float) ($db->table('reversments')
            ->where('statut', 'REGLE')
            ->selectSum('montant')
            ->get()->getRowArray()['montant'] ?? 0);

        return view('Template/operator/reversments', [
            'reversments'     => $allReversments,
            'totalEnAttente'  => $totalEnAttente,
            'totalEnvoye'     => $totalEnvoye,
            'totalRegle'      => $totalRegle,
        ]);
    }

    public function reversmentUpdateStatut($id)
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $statut = trim((string) $this->request->getPost('statut'));
        $allowed = ['EN_ATTENTE', 'ENVOYE', 'REGLE'];

        if (!in_array($statut, $allowed)) {
            return redirect()->back()->with('error', 'Statut invalide.');
        }

        $revModel = new ReversmentModel();
        $revModel->update($id, [
            'statut'            => $statut,
            'date_modification' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/operator/reversments')->with('success', 'Statut du reversment mis à jour.');
    }
}
