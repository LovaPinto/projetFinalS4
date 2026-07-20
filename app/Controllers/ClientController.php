<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use App\Models\PrefixOperateurModel;
use App\Models\TransactionModel;
use App\Libraries\MobileMoneyService;

class ClientController extends BaseController
{
    // ══════════════════════════════════════════════
    // AUTH
    // ══════════════════════════════════════════════

    public function loginForm()
    {
        if (session()->get('client_logged_in')) {
            return redirect()->to(site_url('client/dashboard'));
        }
        return view('client/login');
    }

    public function login()
    {
        $numero = trim((string) $this->request->getPost('numero_telephone'));

        $rules = [
            'numero_telephone' => 'required|exact_length[10]|regex_match[/^0[0-9]{9}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Le numéro de téléphone doit contenir exactement 10 chiffres et commencer par 0.');
        }

        $prefixeModel = new PrefixOperateurModel();
        $prefixe = substr($numero, 0, 3);
        $row = $prefixeModel->where('prefixe', $prefixe)->where('actif', 1)->first();

        if (!$row) {
            return redirect()->back()->withInput()->with('error', 'Ce préfixe n\'est pas pris en charge par l\'opérateur.');
        }

        $clientModel = new ClientsModel();
        $client = $clientModel->where('numero_telephone', $numero)->first();
        $now = date('Y-m-d H:i:s');

        if ($client) {
            if ($client['statut'] === 'BLOQUE') {
                return redirect()->back()->withInput()->with('error', 'Votre compte est bloqué. Contactez l\'opérateur.');
            }
            if ($client['statut'] === 'SUSPENDU') {
                return redirect()->back()->withInput()->with('error', 'Votre compte est suspendu. Contactez l\'opérateur.');
            }

            $clientModel->update($client['id'], [
                'date_derniere_connexion' => $now,
            ]);
        } else {
            $clientId = $clientModel->insert([
                'numero_telephone'        => $numero,
                'operateur_id'            => $row['operateur_id'],
                'solde'                   => 0,
                'statut'                  => 'ACTIF',
                'date_creation'           => $now,
                'date_derniere_connexion' => $now,
            ]);
            $client = $clientModel->find($clientId);
        }

        session()->set([
            'client_id'         => $client['id'],
            'numero_telephone'  => $client['numero_telephone'],
            'operateur_id'      => $client['operateur_id'],
            'client_logged_in'  => true,
        ]);

        return redirect()->to(site_url('client/dashboard'));
    }

    public function logout()
    {
        session()->remove([
            'client_id', 'numero_telephone', 'operateur_id', 'client_logged_in'
        ]);
        session()->destroy();
        return redirect()->to(site_url('client/login'));
    }

    // ══════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════

    public function dashboard()
    {
        $clientModel = new ClientsModel();
        $client = $clientModel->find(session()->get('client_id'));

        if (!$client) {
            session()->destroy();
            return redirect()->to(site_url('client/login'))->with('error', 'Session expirée.');
        }

        $transactionModel = new TransactionModel();
        $historique = $transactionModel->getHistoriqueClient($client['id'], null, null, null, 5, 1);

        return view('client/dashboard', [
            'client'     => $client,
            'historique' => $historique['data'],
        ]);
    }

    // ══════════════════════════════════════════════
    // DÉPÔT
    // ══════════════════════════════════════════════

    public function depositForm()
    {
        return view('client/deposit');
    }

    public function deposit()
    {
        $montant = (float) $this->request->getPost('montant');

        if ($montant < 100) {
            return redirect()->back()->withInput()->with('error', 'Le montant minimum de dépôt est de 100 Ar.');
        }

        try {
            $service = new MobileMoneyService();
            $result = $service->deposit(session()->get('client_id'), $montant);

            return redirect()->back()->with('success',
                'Dépôt réussi. Référence : ' . $result['reference'] .
                ' | Nouveau solde : ' . number_format($result['solde_apres'], 0, ',', ' ') . ' Ar.'
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════
    // RETRAIT
    // ══════════════════════════════════════════════

    public function withdrawForm()
    {
        return view('client/withdraw');
    }

    public function withdraw()
    {
        $montant = (float) $this->request->getPost('montant');

        if ($montant < 100) {
            return redirect()->back()->withInput()->with('error', 'Le montant minimum de retrait est de 100 Ar.');
        }

        try {
            $service = new MobileMoneyService();
            $result = $service->withdraw(session()->get('client_id'), $montant);

            return redirect()->back()->with('success',
                'Retrait réussi. Référence : ' . $result['reference'] .
                ' | Montant : ' . number_format($result['montant'], 0, ',', ' ') . ' Ar' .
                ' | Frais : ' . number_format($result['frais'], 0, ',', ' ') . ' Ar' .
                ' | Total débité : ' . number_format($result['montant_total'], 0, ',', ' ') . ' Ar' .
                ' | Nouveau solde : ' . number_format($result['solde_apres'], 0, ',', ' ') . ' Ar.'
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════
    // TRANSFERT
    // ══════════════════════════════════════════════

    public function transferForm()
    {
        return view('client/transfer');
    }

    public function transfer()
    {
        $destNumero = trim((string) $this->request->getPost('numero_destinataire'));
        $montant = (float) $this->request->getPost('montant');

        if (!preg_match('/^0[0-9]{9}$/', $destNumero)) {
            return redirect()->back()->withInput()->with('error', 'Le numéro du destinataire doit contenir exactement 10 chiffres.');
        }

        if ($montant < 100) {
            return redirect()->back()->withInput()->with('error', 'Le montant minimum de transfert est de 100 Ar.');
        }

        try {
            $service = new MobileMoneyService();
            $result = $service->transfer(session()->get('client_id'), $destNumero, $montant);

            return redirect()->back()->with('success',
                'Transfert réussi. Référence : ' . $result['reference'] .
                ' | Montant : ' . number_format($result['montant'], 0, ',', ' ') . ' Ar' .
                ' | Frais : ' . number_format($result['frais'], 0, ',', ' ') . ' Ar' .
                ' | Total débité : ' . number_format($result['montant_total'], 0, ',', ' ') . ' Ar' .
                ' | Nouveau solde : ' . number_format($result['solde_apres'], 0, ',', ' ') . ' Ar.'
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ══════════════════════════════════════════════
    // HISTORIQUE
    // ══════════════════════════════════════════════

    public function history()
    {
        $transactionModel = new TransactionModel();
        $type = $this->request->getGet('type') ?: '';
        $dateStart = $this->request->getGet('date_start') ?: '';
        $dateEnd = $this->request->getGet('date_end') ?: '';
        $page = max(1, (int) ($this->request->getGet('page') ?: 1));

        $result = $transactionModel->getHistoriqueClient(
            session()->get('client_id'),
            $type !== '' ? $type : null,
            $dateStart !== '' ? $dateStart : null,
            $dateEnd !== '' ? $dateEnd : null,
            10,
            $page
        );

        return view('client/history', [
            'transactions' => $result['data'],
            'total'        => $result['total'],
            'currentPage'  => $result['currentPage'],
            'lastPage'     => $result['lastPage'],
            'typeFilter'   => $type,
            'dateStart'    => $dateStart,
            'dateEnd'      => $dateEnd,
        ]);
    }
}
