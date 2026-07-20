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

        $baremesModel = new BaremesFraisModel();
        $fraisRetraitParOperateur = [];
        $operateurs = $operateurModel->where('actif', 1)->findAll();
        foreach ($operateurs as $op) {
            $fraisRetraitParOperateur[$op['id']] = $baremesModel->operateurAFraisRetrait($op['id']);
        }

        return view('Template/client/transfer', [
            'client'                => $client,
            'operateur'             => $operateur,
            'fraisRetraitParOperateur' => $fraisRetraitParOperateur,
            'operateurs'            => $operateurs,
        ]);
    }

    public function executer()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'Veuillez vous connecter.');
        }

        $montantTotal = (float) $this->request->getPost('montant');
        $destinataires = $this->request->getPost('destinataires');
        $inclusFraisRetrait = $this->request->getPost('inclus_frais_retrait') === '1';

        if ($montantTotal <= 0) {
            return redirect()->back()->withInput()->with('error', 'Le montant doit être supérieur à 0.');
        }

        if (empty($destinataires) || !is_array($destinataires)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez ajouter au moins un destinataire.');
        }

        $destinataires = array_filter(array_map('trim', $destinataires), fn($n) => $n !== '');

        if (empty($destinataires)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez ajouter au moins un destinataire.');
        }

        $destinataires = array_values($destinataires);

        foreach ($destinataires as $dest) {
            if (!preg_match('/^0[0-9]{9}$/', $dest)) {
                return redirect()->back()->withInput()->with('error', 'Numéro invalide : ' . esc($dest) . '. Format attendu : 0XXXXXXXXX.');
            }
        }

        if (count($destinataires) !== count(array_unique($destinataires))) {
            return redirect()->back()->withInput()->with('error', 'Des numéros en double ont été détectés.');
        }

        $clientModel   = new ClientsModel();
        $clientSource  = $clientModel->find($session->get('client_id'));

        $clientsDest = [];
        $operateurIdsDest = [];

        foreach ($destinataires as $num) {
            $clientDest = $clientModel->where('numero_telephone', $num)->first();
            if (!$clientDest) {
                return redirect()->back()->withInput()->with('error', 'Le numéro ' . esc($num) . ' n\'est associé à aucun compte.');
            }
            if ($clientDest['id'] == $clientSource['id']) {
                return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas transférer vers votre propre compte (' . esc($num) . ').');
            }
            $clientsDest[] = $clientDest;
            $operateurIdsDest[] = $clientDest['operateur_id'];
        }

        $operateurIdsDest = array_unique($operateurIdsDest);
        if (count($operateurIdsDest) > 1) {
            return redirect()->back()->withInput()->with('error', 'Tous les bénéficiaires doivent appartenir au même opérateur.');
        }

        $operateurDestId = $operateurIdsDest[0];

        $baremesModel = new BaremesFraisModel();
        $nbDest = count($clientsDest);
        $montantParDest = $montantTotal / $nbDest;

        $fraisTransfertTotal = 0;
        $fraisRetraitTotal   = 0;

        for ($i = 0; $i < $nbDest; $i++) {
            $fraisTransfertTotal += $baremesModel->calculerFraisTransfert($montantParDest);
            if ($inclusFraisRetrait) {
                $fraisRetraitTotal += $baremesModel->calculerFraisRetrait($operateurDestId, $montantParDest);
            }
        }

        $total = $montantTotal + $fraisTransfertTotal + $fraisRetraitTotal;

        if ((float) $clientSource['solde'] < $total) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant. Vous avez ' . number_format($clientSource['solde'], 0, ',', ' ') . ' Ar mais le total débité est ' . number_format($total, 0, ',', ' ') . ' Ar (montant + frais transfert' . ($inclusFraisRetrait ? ' + frais retrait' : '') . ').');
        }

        $typeModel = new TypesOperationModel();
        $typeTransfert = $typeModel->where('code', 'TRANSFERT')->first();

        $soldeAvantSource = (float) $clientSource['solde'];
        $soldeApresSource = $soldeAvantSource - $total;

        $referenceBase = 'TRA-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
        $transactionModel = new TransactionModel();
        $now = date('Y-m-d H:i:s');

        for ($i = 0; $i < $nbDest; $i++) {
            $dest       = $clientsDest[$i];
            $montantEnvoi = $montantParDest;
            $fraisTx     = $baremesModel->calculerFraisTransfert($montantEnvoi);
            $fraisRet    = $inclusFraisRetrait ? $baremesModel->calculerFraisRetrait($operateurDestId, $montantEnvoi) : 0;
            $totalTx     = $montantEnvoi + $fraisTx + $fraisRet;

            $soldeAvantDest = (float) $dest['solde'];
            $soldeApresDest = $soldeAvantDest + $montantEnvoi;

            $reference = $referenceBase . '-' . ($i + 1);

            $transactionModel->insert([
                'reference'             => $reference,
                'type_operation_id'     => $typeTransfert['id'],
                'client_source_id'      => $clientSource['id'],
                'client_destination_id' => $dest['id'],
                'montant'               => $montantEnvoi,
                'frais'                 => $fraisTx + $fraisRet,
                'montant_total'         => $totalTx,
                'solde_avant'           => $soldeAvantSource,
                'solde_apres'           => $soldeApresSource,
                'statut'                => 'REUSSI',
                'date_creation'         => $now,
            ]);

            $clientModel->update($dest['id'], ['solde' => $soldeApresDest]);

            $soldeApresSource -= $totalTx;
        }

        $clientModel->update($clientSource['id'], ['solde' => $soldeApresSource]);
        $session->set('solde', $soldeApresSource);

        $msg = "Transfert de " . number_format($montantTotal, 0, ',', ' ') . " Ar vers " . $nbDest . " bénéficiaire" . ($nbDest > 1 ? 's' : '') . " effectué.";
        $msg .= " (frais : " . number_format($fraisTransfertTotal, 0, ',', ' ') . " Ar";
        if ($inclusFraisRetrait) {
            $msg .= " + " . number_format($fraisRetraitTotal, 0, ',', ' ') . " Ar de frais retrait";
        }
        $msg .= "). Nouveau solde : " . number_format($soldeApresSource, 0, ',', ' ') . " Ar.";

        return redirect()->to('/dashboard')->with('success', $msg);
    }
}
