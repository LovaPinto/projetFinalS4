<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientsModel;
use App\Models\OperateurModel;
use App\Models\TransactionModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashbordController extends BaseController
{
    public function index()
    {
        $session = session();

        // ---- Protection : accessible uniquement si connecté ----
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'Veuillez vous connecter.');
        }

        $clientId = $session->get('client_id');
        $clientSolde = $session->get('solde');

        // ---- Données du client connecté ----
        $clientModel = new ClientsModel();
        $client      = $clientModel->find($clientId);

        // Sécurité : si le client a été supprimé entre-temps, on force la déconnexion
        if (!$client) {
            $session->destroy();
            return redirect()->to('/')->with('error', 'Compte introuvable, veuillez vous reconnecter.');
        }

        // ---- Opérateur associé ----
        $operateurModel = new OperateurModel();
        $operateur      = $operateurModel->find($client['operateur_id']);

        // ---- Historique des opérations du client (dépôt, retrait, transfert) ----
        $transactionModel = new TransactionModel();
        $historique        = $transactionModel->getHistoriqueClient($clientId, 20);

        $data = [
            'client'     => $client,
            'operateur'  => $operateur,
            'solde' => $clientSolde,
            'historique' => $historique,
        ];

        return view('Template/client/dashboard', $data);
    }
}