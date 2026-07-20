<?php

namespace App\Controllers;

use App\Models\OperateurModel;
use App\Models\PrefixOperateurModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\ClientsModel;
use App\Models\TransactionModel;

class OperatorController extends BaseController
{
    // ══════════════════════════════════════════════
    // AUTH
    // ══════════════════════════════════════════════

    public function loginForm()
    {
        if (session()->get('operator_logged_in')) {
            return redirect()->to(site_url('operator/dashboard'));
        }
        return view('operator/login');
    }

    public function login()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('mot_de_passe');

        $rules = [
            'email'         => 'required|valid_email',
            'mot_de_passe'  => 'required|min_length[4]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new OperateurModel();
        $operateur = $model->where('email', $email)->first();

        if (!$operateur) {
            return redirect()->back()->withInput()->with('error', 'Aucun compte trouvé avec cet e-mail.');
        }

        if (!password_verify($password, $operateur['mot_de_passe'])) {
            return redirect()->back()->withInput()->with('error', 'Mot de passe incorrect.');
        }

        if (!$operateur['actif']) {
            return redirect()->back()->withInput()->with('error', 'Votre compte opérateur est désactivé.');
        }

        session()->set([
            'operator_id'       => $operateur['id'],
            'operator_email'    => $operateur['email'],
            'operator_name'     => $operateur['nom'],
            'operator_logged_in'=> true,
        ]);

        return redirect()->to(site_url('operator/dashboard'));
    }

    public function logout()
    {
        session()->remove([
            'operator_id', 'operator_email', 'operator_name', 'operator_logged_in'
        ]);
        session()->destroy();
        return redirect()->to(site_url('operator/login'));
    }

    // ══════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════

    public function dashboard()
    {
        $clientModel = new ClientsModel();
        $transactionModel = new TransactionModel();
        $db = \Config\Database::connect();

        $totalClients = $clientModel->countAllResults();
        $activeClients = $clientModel->where('statut', 'ACTIF')->countAllResults();

        $soldeQuery = $db->query("SELECT COALESCE(SUM(solde), 0) AS total FROM clients")->getRow();
        $soldeCumule = $soldeQuery->total;

        $types = $db->query("SELECT id, code FROM types_operation")->getResultArray();
        $typesById = [];
        foreach ($types as $t) {
            $typesById[$t['id']] = $t['code'];
        }

        $totalTx = $transactionModel->where('statut', 'REUSSI')->countAllResults();

        $depotQuery = $db->query("SELECT COALESCE(SUM(t.montant), 0) AS total FROM transactions t JOIN types_operation to2 ON to2.id = t.type_operation_id WHERE to2.code = 'DEPOT' AND t.statut = 'REUSSI'")->getRow();
        $totalDepots = $depotQuery->total;

        $retraitQuery = $db->query("SELECT COALESCE(SUM(t.montant), 0) AS total FROM transactions t JOIN types_operation to2 ON to2.id = t.type_operation_id WHERE to2.code = 'RETRAIT' AND t.statut = 'REUSSI'")->getRow();
        $totalRetraits = $retraitQuery->total;

        $transfertQuery = $db->query("SELECT COALESCE(SUM(t.montant), 0) AS total FROM transactions t JOIN types_operation to2 ON to2.id = t.type_operation_id WHERE to2.code = 'TRANSFERT' AND t.statut = 'REUSSI'")->getRow();
        $totalTransferts = $transfertQuery->total;

        $fraisQuery = $db->query("SELECT COALESCE(SUM(frais), 0) AS total FROM transactions WHERE statut = 'REUSSI'")->getRow();
        $totalFrais = $fraisQuery->total;

        $lastTx = $transactionModel
            ->select('transactions.*, types_operation.libelle AS type_libelle, types_operation.code AS type_code, src.numero_telephone AS source_numero, dst.numero_telephone AS destination_numero')
            ->join('types_operation', 'types_operation.id = transactions.type_operation_id')
            ->join('clients AS src', 'src.id = transactions.client_source_id', 'left')
            ->join('clients AS dst', 'dst.id = transactions.client_destination_id', 'left')
            ->orderBy('transactions.date_creation', 'DESC')
            ->limit(10)
            ->find();

        return view('operator/dashboard', [
            'totalClients'   => $totalClients,
            'activeClients'  => $activeClients,
            'soldeCumule'    => $soldeCumule,
            'totalTx'        => $totalTx,
            'totalDepots'    => $totalDepots,
            'totalRetraits'  => $totalRetraits,
            'totalTransferts'=> $totalTransferts,
            'totalFrais'     => $totalFrais,
            'lastTx'         => $lastTx,
            'typesById'      => $typesById,
        ]);
    }

    // ══════════════════════════════════════════════
    // PREFIXES
    // ══════════════════════════════════════════════

    public function prefixes()
    {
        $model = new PrefixOperateurModel();
        $prefixes = $model->select('prefixes_operateur.*, operateurs.nom AS operateur_nom')
            ->join('operateurs', 'operateurs.id = prefixes_operateur.operateur_id')
            ->orderBy('prefixes_operateur.id', 'ASC')
            ->findAll();

        $operateurModel = new OperateurModel();
        $operateurs = $operateurModel->where('actif', 1)->findAll();

        return view('operator/prefixes', [
            'prefixes'   => $prefixes,
            'operateurs' => $operateurs,
        ]);
    }

    public function prefixesStore()
    {
        $rules = [
            'prefixe'      => 'required|exact_length[3]|is_unique[prefixes_operateur.prefixe]',
            'operateur_id' => 'required|is_not_unique[operateurs.id]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new PrefixOperateurModel();
        $model->insert([
            'prefixe'       => $this->request->getPost('prefixe'),
            'operateur_id'  => $this->request->getPost('operateur_id'),
            'actif'         => 1,
            'date_creation' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('operator/prefixes'))->with('success', 'Préfixe ajouté avec succès.');
    }

    public function prefixesUpdate(int $id)
    {
        $model = new PrefixOperateurModel();
        $existing = $model->find($id);
        if (!$existing) {
            return redirect()->to(site_url('operator/prefixes'))->with('error', 'Préfixe introuvable.');
        }

        $rules = [
            'prefixe'      => 'required|exact_length[3]|is_unique[prefixes_operateur.prefixe,id,' . $id . ']',
            'operateur_id' => 'required|is_not_unique[operateurs.id]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'prefixe'      => $this->request->getPost('prefixe'),
            'operateur_id' => $this->request->getPost('operateur_id'),
        ]);

        return redirect()->to(site_url('operator/prefixes'))->with('success', 'Préfixe modifié avec succès.');
    }

    public function prefixesToggle(int $id)
    {
        $model = new PrefixOperateurModel();
        $existing = $model->find($id);
        if (!$existing) {
            return redirect()->to(site_url('operator/prefixes'))->with('error', 'Préfixe introuvable.');
        }

        $model->update($id, ['actif' => $existing['actif'] ? 0 : 1]);

        $etat = $existing['actif'] ? 'désactivé' : 'activé';
        return redirect()->to(site_url('operator/prefixes'))->with('success', "Préfixe {$etat} avec succès.");
    }

    public function prefixesDelete(int $id)
    {
        $model = new PrefixOperateurModel();
        $existing = $model->find($id);
        if (!$existing) {
            return redirect()->to(site_url('operator/prefixes'))->with('error', 'Préfixe introuvable.');
        }

        $db = \Config\Database::connect();
        $count = $db->table('clients')
            ->like('numero_telephone', $existing['prefixe'], 'after')
            ->countAllResults();

        if ($count > 0) {
            return redirect()->to(site_url('operator/prefixes'))
                ->with('error', 'Impossible de supprimer ce préfixe : ' . $count . ' client(s) l\'utilisent.');
        }

        $model->delete($id);
        return redirect()->to(site_url('operator/prefixes'))->with('success', 'Préfixe supprimé avec succès.');
    }

    // ══════════════════════════════════════════════
    // TYPES D'OPÉRATIONS
    // ══════════════════════════════════════════════

    public function operations()
    {
        $model = new TypeOperationModel();
        $operations = $model->orderBy('id', 'ASC')->findAll();

        return view('operator/operations', [
            'operations' => $operations,
        ]);
    }

    public function operationsStore()
    {
        $rules = [
            'code'    => 'required|max_length[20]|is_unique[types_operation.code]',
            'libelle' => 'required|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new TypeOperationModel();
        $model->insert([
            'code'        => strtoupper($this->request->getPost('code')),
            'libelle'     => $this->request->getPost('libelle'),
            'avec_frais'  => $this->request->getPost('avec_frais') ? 1 : 0,
            'actif'       => 1,
        ]);

        return redirect()->to(site_url('operator/operations'))->with('success', 'Type d\'opération ajouté avec succès.');
    }

    public function operationsUpdate(int $id)
    {
        $model = new TypeOperationModel();
        $existing = $model->find($id);
        if (!$existing) {
            return redirect()->to(site_url('operator/operations'))->with('error', 'Opération introuvable.');
        }

        $rules = [
            'libelle' => 'required|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model->update($id, [
            'libelle'    => $this->request->getPost('libelle'),
            'avec_frais' => $this->request->getPost('avec_frais') ? 1 : 0,
        ]);

        return redirect()->to(site_url('operator/operations'))->with('success', 'Opération modifiée avec succès.');
    }

    public function operationsToggle(int $id)
    {
        $model = new TypeOperationModel();
        $existing = $model->find($id);
        if (!$existing) {
            return redirect()->to(site_url('operator/operations'))->with('error', 'Opération introuvable.');
        }

        $model->update($id, ['actif' => $existing['actif'] ? 0 : 1]);

        $etat = $existing['actif'] ? 'désactivée' : 'activée';
        return redirect()->to(site_url('operator/operations'))->with('success', "Opération {$etat} avec succès.");
    }

    public function operationsDelete(int $id)
    {
        $model = new TypeOperationModel();
        $existing = $model->find($id);
        if (!$existing) {
            return redirect()->to(site_url('operator/operations'))->with('error', 'Opération introuvable.');
        }

        $codesProteges = ['DEPOT', 'RETRAIT', 'TRANSFERT'];
        if (in_array($existing['code'], $codesProteges)) {
            $db = \Config\Database::connect();
            $count = $db->table('transactions')->where('type_operation_id', $id)->countAllResults();
            if ($count > 0) {
                return redirect()->to(site_url('operator/operations'))
                    ->with('error', 'Impossible de supprimer cette opération : des transactions existent.');
            }
        }

        $model->delete($id);
        return redirect()->to(site_url('operator/operations'))->with('success', 'Opération supprimée avec succès.');
    }

    // ══════════════════════════════════════════════
    // BARÈMES DE FRAIS
    // ══════════════════════════════════════════════

    public function fees()
    {
        $model = new BaremeFraisModel();
        $typeModel = new TypeOperationModel();

        $typeFilter = $this->request->getGet('type');

        $builder = $model->select('baremes_frais.*, types_operation.code AS type_code, types_operation.libelle AS type_libelle')
            ->join('types_operation', 'types_operation.id = baremes_frais.type_operation_id');

        if ($typeFilter !== null && $typeFilter !== '') {
            $builder->where('baremes_frais.type_operation_id', $typeFilter);
        }

        $baremes = $builder->orderBy('baremes_frais.type_operation_id', 'ASC')
            ->orderBy('baremes_frais.montant_min', 'ASC')
            ->findAll();

        $types = $typeModel->orderBy('code', 'ASC')->findAll();

        return view('operator/fees', [
            'baremes'       => $baremes,
            'types'         => $types,
            'typeFilter'    => $typeFilter,
        ]);
    }

    public function feesStore()
    {
        $rules = [
            'type_operation_id' => 'required|is_not_unique[types_operation.id]',
            'montant_min'       => 'required|decimal|greater_than_equal_to[0]',
            'montant_max'       => 'required|decimal|greater_than_equal_to[montant_min]',
            'frais'             => 'required|decimal|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $typeId = (int) $this->request->getPost('type_operation_id');
        $min = (float) $this->request->getPost('montant_min');
        $max = (float) $this->request->getPost('montant_max');

        $baremeModel = new BaremeFraisModel();
        if ($baremeModel->hasOverlap($typeId, $min, $max)) {
            return redirect()->back()->withInput()->with('error', 'Cette tranche chevauche une tranche active existante pour ce type d\'opération.');
        }

        $baremeModel->insert([
            'type_operation_id' => $typeId,
            'montant_min'       => $min,
            'montant_max'       => $max,
            'frais'             => (float) $this->request->getPost('frais'),
            'actif'             => 1,
            'date_debut'        => date('Y-m-d H:i:s'),
            'date_fin'          => null,
        ]);

        return redirect()->to(site_url('operator/fees'))->with('success', 'Tranche de frais ajoutée avec succès.');
    }

    public function feesUpdate(int $id)
    {
        $model = new BaremeFraisModel();
        $existing = $model->find($id);
        if (!$existing) {
            return redirect()->to(site_url('operator/fees'))->with('error', 'Tranche introuvable.');
        }

        $rules = [
            'montant_min' => 'required|decimal|greater_than_equal_to[0]',
            'montant_max' => 'required|decimal|greater_than_equal_to[montant_min]',
            'frais'       => 'required|decimal|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $typeId = (int) $existing['type_operation_id'];
        $min = (float) $this->request->getPost('montant_min');
        $max = (float) $this->request->getPost('montant_max');

        $baremeModel = new BaremeFraisModel();
        if ($baremeModel->hasOverlap($typeId, $min, $max, $id)) {
            return redirect()->back()->withInput()->with('error', 'Cette tranche chevauche une tranche active existante pour ce type d\'opération.');
        }

        $model->update($id, [
            'montant_min' => $min,
            'montant_max' => $max,
            'frais'       => (float) $this->request->getPost('frais'),
        ]);

        return redirect()->to(site_url('operator/fees'))->with('success', 'Tranche modifiée avec succès.');
    }

    public function feesToggle(int $id)
    {
        $model = new BaremeFraisModel();
        $existing = $model->find($id);
        if (!$existing) {
            return redirect()->to(site_url('operator/fees'))->with('error', 'Tranche introuvable.');
        }

        $model->update($id, ['actif' => $existing['actif'] ? 0 : 1]);

        $etat = $existing['actif'] ? 'désactivée' : 'activée';
        return redirect()->to(site_url('operator/fees'))->with('success', "Tranche {$etat} avec succès.");
    }

    public function feesDelete(int $id)
    {
        $model = new BaremeFraisModel();
        $existing = $model->find($id);
        if (!$existing) {
            return redirect()->to(site_url('operator/fees'))->with('error', 'Tranche introuvable.');
        }

        $db = \Config\Database::connect();
        $count = $db->table('transactions')->where('type_operation_id', $existing['type_operation_id'])->countAllResults();

        if ($count > 0) {
            return redirect()->to(site_url('operator/fees'))
                ->with('error', 'Impossible de supprimer cette tranche : des transactions existent pour ce type.');
        }

        $model->delete($id);
        return redirect()->to(site_url('operator/fees'))->with('success', 'Tranche supprimée avec succès.');
    }

    // ══════════════════════════════════════════════
    // CLIENTS
    // ══════════════════════════════════════════════

    public function clients()
    {
        $model = new ClientsModel();
        $search = $this->request->getGet('search') ?: '';
        $statut = $this->request->getGet('statut') ?: '';
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $perPage = 10;

        $builder = $model->select('clients.*, operateurs.nom AS operateur_nom, operateurs.code AS operateur_code')
            ->join('operateurs', 'operateurs.id = clients.operateur_id');

        if ($search !== '') {
            $builder->like('clients.numero_telephone', $search);
        }
        if ($statut !== '' && in_array($statut, ['ACTIF', 'BLOQUE', 'SUSPENDU'])) {
            $builder->where('clients.statut', $statut);
        }

        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;

        $clients = $builder->orderBy('clients.id', 'ASC')
            ->limit($perPage, $offset)
            ->find();

        return view('operator/clients', [
            'clients'      => $clients,
            'total'        => $total,
            'currentPage'  => $page,
            'lastPage'     => (int) ceil($total / $perPage),
            'search'       => $search,
            'statut'       => $statut,
        ]);
    }

    public function clientDetail(int $id)
    {
        $clientModel = new ClientsModel();
        $client = $clientModel->select('clients.*, operateurs.nom AS operateur_nom')
            ->join('operateurs', 'operateurs.id = clients.operateur_id')
            ->find($id);

        if (!$client) {
            return redirect()->to(site_url('operator/clients'))->with('error', 'Client introuvable.');
        }

        $transactionModel = new TransactionModel();
        $historique = $transactionModel
            ->select('transactions.*, types_operation.libelle AS type_libelle, types_operation.code AS type_code, src.numero_telephone AS source_numero, dst.numero_telephone AS destination_numero')
            ->join('types_operation', 'types_operation.id = transactions.type_operation_id')
            ->join('clients AS src', 'src.id = transactions.client_source_id', 'left')
            ->join('clients AS dst', 'dst.id = transactions.client_destination_id', 'left')
            ->groupStart()
                ->where('transactions.client_source_id', $id)
                ->orWhere('transactions.client_destination_id', $id)
            ->groupEnd()
            ->orderBy('transactions.date_creation', 'DESC')
            ->limit(50)
            ->find();

        return view('operator/client_detail', [
            'client'     => $client,
            'historique' => $historique,
        ]);
    }

    public function clientStatus(int $id)
    {
        $model = new ClientsModel();
        $client = $model->find($id);
        if (!$client) {
            return redirect()->to(site_url('operator/clients'))->with('error', 'Client introuvable.');
        }

        $statut = (string) $this->request->getPost('statut');
        if (!in_array($statut, ['ACTIF', 'BLOQUE', 'SUSPENDU'])) {
            return redirect()->back()->with('error', 'Statut invalide.');
        }

        $model->update($id, ['statut' => $statut]);

        $label = strtolower($statut);
        return redirect()->to(site_url('operator/clients/' . $id))
            ->with('success', "Le compte client a été {$label}.");
    }

    // ══════════════════════════════════════════════
    // TRANSACTIONS
    // ══════════════════════════════════════════════

    public function transactions()
    {
        $model = new TransactionModel();
        $typeFilter = $this->request->getGet('type') ?: '';
        $statutFilter = $this->request->getGet('statut') ?: '';
        $search = $this->request->getGet('search') ?: '';
        $dateStart = $this->request->getGet('date_start') ?: '';
        $dateEnd = $this->request->getGet('date_end') ?: '';
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));
        $perPage = 15;

        $builder = $model->select('transactions.*, types_operation.libelle AS type_libelle, types_operation.code AS type_code, src.numero_telephone AS source_numero, dst.numero_telephone AS destination_numero')
            ->join('types_operation', 'types_operation.id = transactions.type_operation_id')
            ->join('clients AS src', 'src.id = transactions.client_source_id', 'left')
            ->join('clients AS dst', 'dst.id = transactions.client_destination_id', 'left');

        if ($typeFilter !== '') {
            $builder->where('types_operation.code', $typeFilter);
        }
        if ($statutFilter !== '') {
            $builder->where('transactions.statut', $statutFilter);
        }
        if ($search !== '') {
            $builder->groupStart();
            $builder->like('transactions.reference', $search);
            $builder->orWhereLike('src.numero_telephone', $search);
            $builder->orWhereLike('dst.numero_telephone', $search);
            $builder->groupEnd();
        }
        if ($dateStart !== '') {
            $builder->where('transactions.date_creation >=', $dateStart . ' 00:00:00');
        }
        if ($dateEnd !== '') {
            $builder->where('transactions.date_creation <=', $dateEnd . ' 23:59:59');
        }

        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $perPage;

        $transactions = $builder->orderBy('transactions.date_creation', 'DESC')
            ->limit($perPage, $offset)
            ->find();

        return view('operator/transactions', [
            'transactions' => $transactions,
            'total'        => $total,
            'currentPage'  => $page,
            'lastPage'     => (int) ceil($total / $perPage),
            'typeFilter'   => $typeFilter,
            'statutFilter' => $statutFilter,
            'search'       => $search,
            'dateStart'    => $dateStart,
            'dateEnd'      => $dateEnd,
        ]);
    }

    // ══════════════════════════════════════════════
    // GAINS
    // ══════════════════════════════════════════════

    public function gains()
    {
        $db = \Config\Database::connect();

        $gainsTotal = $db->query("SELECT COALESCE(SUM(frais), 0) AS total FROM transactions WHERE statut = 'REUSSI'")->getRow()->total;

        $gainsRetraits = $db->query("SELECT COALESCE(SUM(t.frais), 0) AS total FROM transactions t JOIN types_operation to2 ON to2.id = t.type_operation_id WHERE to2.code = 'RETRAIT' AND t.statut = 'REUSSI'")->getRow()->total;

        $gainsTransferts = $db->query("SELECT COALESCE(SUM(t.frais), 0) AS total FROM transactions t JOIN types_operation to2 ON to2.id = t.type_operation_id WHERE to2.code = 'TRANSFERT' AND t.statut = 'REUSSI'")->getRow()->total;

        $nbPayantes = $db->query("SELECT COUNT(*) AS total FROM transactions WHERE statut = 'REUSSI' AND frais > 0")->getRow()->total;

        $parType = $db->query("SELECT to2.code, to2.libelle, COUNT(*) AS nb, COALESCE(SUM(t.frais), 0) AS total_frais FROM transactions t JOIN types_operation to2 ON to2.id = t.type_operation_id WHERE t.statut = 'REUSSI' GROUP BY to2.id")->getResultArray();

        $parJour = $db->query("SELECT date(date_creation) AS jour, COALESCE(SUM(frais), 0) AS total FROM transactions WHERE statut = 'REUSSI' GROUP BY date(date_creation) ORDER BY jour DESC LIMIT 30")->getResultArray();

        $parMois = $db->query("SELECT strftime('%Y-%m', date_creation) AS mois, COALESCE(SUM(frais), 0) AS total FROM transactions WHERE statut = 'REUSSI' GROUP BY strftime('%Y-%m', date_creation) ORDER BY mois DESC LIMIT 12")->getResultArray();

        return view('operator/gains', [
            'gainsTotal'     => $gainsTotal,
            'gainsRetraits'  => $gainsRetraits,
            'gainsTransferts'=> $gainsTransferts,
            'nbPayantes'     => $nbPayantes,
            'parType'        => $parType,
            'parJour'        => $parJour,
            'parMois'        => $parMois,
        ]);
    }
}
