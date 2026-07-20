<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use App\Models\TransactionModel;
use App\Models\OperateurModel;

class HistoriqueController extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'Veuillez vous connecter.');
        }

        $clientModel = new ClientsModel();
        $client      = $clientModel->find($session->get('client_id'));

        $operateurModel = new OperateurModel();
        $operateur      = $operateurModel->find($client['operateur_id']);

        $transactionModel = new TransactionModel();
        $historique       = $transactionModel->getHistoriqueClient($client['id'], 50);

        return view('Template/client/history', [
            'client'     => $client,
            'operateur'  => $operateur,
            'historique' => $historique,
        ]);
    }
}
