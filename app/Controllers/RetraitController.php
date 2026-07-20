<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use App\Models\BaremesFraisModel;
use App\Models\TransactionModel;
use App\Models\OperateurModel;
use App\Models\TypesOperationModel;

class RetraitController extends BaseController
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

        $baremesModel = new BaremesFraisModel();
        $baremes      = $baremesModel->where('actif', 1)->findAll();

        return view('Template/client/withdraw', [
            'client'    => $client,
            'operateur' => $operateur,
            'baremes'   => $baremes,
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

        $baremesModel = new BaremesFraisModel();
        $frais        = $baremesModel->calculerFrais('RETRAIT', $montant);
        $total        = $montant + $frais;

        if ((float) $client['solde'] < $total) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant. Vous avez ' . number_format($client['solde'], 0, ',', ' ') . ' Ar mais le total débité est ' . number_format($total, 0, ',', ' ') . ' Ar (montant + frais).');
        }

        $soldeAvant = (float) $client['solde'];
        $soldeApres = $soldeAvant - $total;

        $typeModel = new TypesOperationModel();
        $type      = $typeModel->where('code', 'RETRAIT')->first();

        $reference = 'RET-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $transactionModel = new TransactionModel();
        $transactionModel->insert([
            'reference'             => $reference,
            'type_operation_id'     => $type['id'],
            'client_source_id'      => $client['id'],
            'client_destination_id' => null,
            'montant'               => $montant,
            'frais'                 => $frais,
            'montant_total'         => $total,
            'solde_avant'           => $soldeAvant,
            'solde_apres'           => $soldeApres,
            'statut'                => 'REUSSI',
            'date_creation'         => date('Y-m-d H:i:s'),
        ]);

        $clientModel->update($client['id'], ['solde' => $soldeApres]);

        $session->set('solde', $soldeApres);

        return redirect()->to('/dashboard')->with('success', "Retrait de " . number_format($montant, 0, ',', ' ') . " Ar effectué (frais : " . number_format($frais, 0, ',', ' ') . " Ar). Nouveau solde : " . number_format($soldeApres, 0, ',', ' ') . " Ar.");
    }
}
