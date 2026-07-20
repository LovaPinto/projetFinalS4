<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use App\Models\BaremesFraisModel;
use App\Models\TransactionModel;
use App\Models\OperateurModel;
use App\Models\TypesOperationModel;

class TransfertController extends BaseController
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

        return view('Template/client/transfer', [
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

        $numeroDest = trim((string) $this->request->getPost('numero_destinataire'));
        $montant    = (float) $this->request->getPost('montant');

        if (!preg_match('/^0[0-9]{9}$/', $numeroDest)) {
            return redirect()->back()->withInput()->with('error', 'Numéro du destinataire invalide.');
        }

        if ($montant <= 0) {
            return redirect()->back()->withInput()->with('error', 'Le montant doit être supérieur à 0.');
        }

        $clientModel   = new ClientsModel();
        $clientSource  = $clientModel->find($session->get('client_id'));
        $clientDest    = $clientModel->where('numero_telephone', $numeroDest)->first();

        if (!$clientDest) {
            return redirect()->back()->withInput()->with('error', 'Ce numéro n\'est associé à aucun compte.');
        }

        if ($clientDest['id'] == $clientSource['id']) {
            return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas transférer vers votre propre compte.');
        }

        $baremesModel = new BaremesFraisModel();
        $frais        = $baremesModel->calculerFrais('TRANSFERT', $montant);
        $total        = $montant + $frais;

        if ((float) $clientSource['solde'] < $total) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant. Vous avez ' . number_format($clientSource['solde'], 0, ',', ' ') . ' Ar mais le total débité est ' . number_format($total, 0, ',', ' ') . ' Ar (montant + frais).');
        }

        $soldeAvantSource = (float) $clientSource['solde'];
        $soldeApresSource = $soldeAvantSource - $total;
        $soldeAvantDest   = (float) $clientDest['solde'];
        $soldeApresDest   = $soldeAvantDest + $montant;

        $typeModel = new TypesOperationModel();
        $type      = $typeModel->where('code', 'TRANSFERT')->first();

        $reference = 'TRA-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $transactionModel = new TransactionModel();
        $transactionModel->insert([
            'reference'             => $reference,
            'type_operation_id'     => $type['id'],
            'client_source_id'      => $clientSource['id'],
            'client_destination_id' => $clientDest['id'],
            'montant'               => $montant,
            'frais'                 => $frais,
            'montant_total'         => $total,
            'solde_avant'           => $soldeAvantSource,
            'solde_apres'           => $soldeApresSource,
            'statut'                => 'REUSSI',
            'date_creation'         => date('Y-m-d H:i:s'),
        ]);

        $clientModel->update($clientSource['id'], ['solde' => $soldeApresSource]);
        $clientModel->update($clientDest['id'], ['solde' => $soldeApresDest]);

        $session->set('solde', $soldeApresSource);

        return redirect()->to('/dashboard')->with('success', "Transfert de " . number_format($montant, 0, ',', ' ') . " Ar vers " . $numeroDest . " effectué (frais : " . number_format($frais, 0, ',', ' ') . " Ar). Nouveau solde : " . number_format($soldeApresSource, 0, ',', ' ') . " Ar.");
    }
}
