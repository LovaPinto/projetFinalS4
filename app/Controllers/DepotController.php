<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use App\Models\BaremesFraisModel;
use App\Models\OperateurModel;
use App\Models\TransactionModel;
use App\Models\TypesOperationModel;

class DepotController extends BaseController
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

        return view('Template/client/deposit', [
            'client'    => $client,
            'operateur' => $operateur,
        ]);
    }

    public function executer()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'Veuillez vous connecter.');
        }

        $montant = (float) $this->request->getPost('montant');

        if ($montant <= 0) {
            return redirect()->back()->withInput()->with('error', 'Le montant doit être supérieur à 0.');
        }

        $clientModel = new ClientsModel();
        $client      = $clientModel->find($session->get('client_id'));

        $soldeAvant     = (float) $client['solde'];
        $soldeApres     = $soldeAvant + $montant;

        $typeModel = new TypesOperationModel();
        $type      = $typeModel->where('code', 'DEPOT')->first();

        $reference = 'DEP-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $transactionModel = new TransactionModel();
        $transactionModel->insert([
            'reference'             => $reference,
            'type_operation_id'     => $type['id'],
            'client_source_id'      => null,
            'client_destination_id' => $client['id'],
            'montant'               => $montant,
            'frais'                 => 0,
            'montant_total'         => $montant,
            'solde_avant'           => $soldeAvant,
            'solde_apres'           => $soldeApres,
            'statut'                => 'REUSSI',
            'date_creation'         => date('Y-m-d H:i:s'),
        ]);

        $clientModel->update($client['id'], ['solde' => $soldeApres]);

        $session->set('solde', $soldeApres);

        return redirect()->to('/dashboard')->with('success', "Dépôt de " . number_format($montant, 0, ',', ' ') . " Ar effectué. Nouveau solde : " . number_format($soldeApres, 0, ',', ' ') . " Ar.");
    }
}
