<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use App\Models\OperateurModel;
use App\Models\PrefixOperateurModel;
use App\Models\TypesOperationModel;
use App\Models\BaremesFraisModel;
use App\Models\TransactionModel;

class OperatorController extends BaseController
{
    private function requireAuth()
    {
        if (!session()->get('operator_logged_in')) {
            return redirect()->to('/operator/login');
        }
        return null;
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
            session()->set([
                'operator_logged_in' => true,
                'operator_name'      => 'Administrateur',
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
        return redirect()->to('/operator/login');
    }

    public function dashboard()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $clientModel     = new ClientsModel();
        $transactionModel = new TransactionModel();
        $operateurModel   = new OperateurModel();

        $totalClients   = $clientModel->countAllResults();
        $totalBalance   = $clientModel->selectSum('solde')->first()['solde'] ?? 0;
        $totalTx        = $transactionModel->countAllResults();
        $totalFrais     = $transactionModel->selectSum('frais')->first()['frais'] ?? 0;

        $recentTx = $transactionModel
            ->select('transactions.*, types_operation.libelle AS type_libelle, clients.numero_telephone')
            ->join('types_operation', 'types_operation.id = transactions.type_operation_id')
            ->join('clients', 'clients.id = transactions.client_source_id', 'left')
            ->orderBy('date_creation', 'DESC')
            ->findAll(10);

        return view('Template/operator/dashboard', [
            'totalClients' => $totalClients,
            'totalBalance' => $totalBalance,
            'totalTx'      => $totalTx,
            'totalFrais'   => $totalFrais,
            'recentTx'     => $recentTx,
        ]);
    }

    public function clients()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $clientModel   = new ClientsModel();
        $operateurModel = new OperateurModel();

        $allClients = $clientModel
            ->select('clients.*, operateurs.nom AS operateur_nom')
            ->join('operateurs', 'operateurs.id = clients.operateur_id')
            ->orderBy('clients.date_creation', 'DESC')
            ->findAll();

        return view('Template/operator/clients', [
            'clients' => $allClients,
        ]);
    }

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

        $prefixe      = trim((string) $this->request->getPost('prefixe'));
        $operateurId  = (int) $this->request->getPost('operateur_id');

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

    public function fees()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $baremeModel  = new BaremesFraisModel();
        $typeModel    = new TypesOperationModel();

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

    public function transactions()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $transactionModel = new TransactionModel();

        $allTx = $transactionModel
            ->select('transactions.*, types_operation.libelle AS type_libelle, 
                      cs.numero_telephone AS client_source_tel, 
                      cd.numero_telephone AS client_dest_tel')
            ->join('types_operation', 'types_operation.id = transactions.type_operation_id')
            ->join('clients cs', 'cs.id = transactions.client_source_id', 'left')
            ->join('clients cd', 'cd.id = transactions.client_destination_id', 'left')
            ->orderBy('transactions.date_creation', 'DESC')
            ->findAll();

        return view('Template/operator/transactions', [
            'transactions' => $allTx,
        ]);
    }

    public function gains()
    {
        $redir = $this->requireAuth();
        if ($redir) return $redir;

        $db = \Config\Database::connect();

        $gainsParType = $db->query(
            "SELECT t.code, t.libelle, 
                    COUNT(tr.id) AS nb_operations, 
                    COALESCE(SUM(tr.frais), 0) AS total_frais_encaisses
             FROM types_operation t
             LEFT JOIN transactions tr ON tr.type_operation_id = t.id AND tr.statut = 'REUSSI'
             GROUP BY t.code, t.libelle
             ORDER BY total_frais_encaisses DESC"
        )->getResultArray();

        $totalFrais = array_sum(array_column($gainsParType, 'total_frais_encaisses'));
        $totalOps   = array_sum(array_column($gainsParType, 'nb_operations'));

        $gainsParTypeDetail = [];
        foreach ($gainsParType as $row) {
            $gainsParTypeDetail[] = [
                'code'          => $row['code'],
                'libelle'       => $row['libelle'],
                'nb_operations' => (int) $row['nb_operations'],
                'total_frais'   => (float) $row['total_frais_encaisses'],
                'pourcentage'   => $totalFrais > 0 ? round(((float) $row['total_frais_encaisses'] / $totalFrais) * 100, 1) : 0,
            ];
        }

        return view('Template/operator/gains', [
            'gainsParType' => $gainsParTypeDetail,
            'totalFrais'   => $totalFrais,
            'totalOps'     => $totalOps,
        ]);
    }
}
