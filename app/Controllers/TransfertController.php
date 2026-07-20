<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use App\Models\BaremesFraisModel;
use App\Models\TransactionModel;
use App\Models\OperateurModel;
use App\Models\TypesOperationModel;
use App\Models\PrefixOperateurModel;
use App\Models\ReversmentModel;

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
        $frais        = $baremesModel->calculerFrais('TRANSFERT', $montant);

        $prefixModel   = new PrefixOperateurModel();
        $operateurModel = new OperateurModel();

        $prefixeSource = substr($clientSource['numero_telephone'], 0, 3);
        $prefixeDest   = substr($clientDest['numero_telephone'], 0, 3);

        $rowSource = $prefixModel->where('prefixe', $prefixeSource)->where('actif', 1)->first();
        $rowDest   = $prefixModel->where('prefixe', $prefixeDest)->where('actif', 1)->first();

        $opSourceId = $rowSource['operateur_id'] ?? $clientSource['operateur_id'];
        $opDestId   = $rowDest['operateur_id'] ?? $clientDest['operateur_id'];

        $memeOperateur = ($opSourceId === $opDestId);

        if ($memeOperateur) {
            $typeTransfert = 'INTERNE';
            $commission    = 0;
        } else {
            $typeTransfert = 'EXTERNE';
            $opSource = $operateurModel->find($opSourceId);
            $pct      = (float) ($opSource['commission_pct'] ?? 2.0);
            $commission = round($frais * $pct / 100, 2);
        }

        $total = $montant + $frais + $commission;

        if ((float) $clientSource['solde'] < $total) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant. Vous avez ' . number_format($clientSource['solde'], 0, ',', ' ') . ' Ar mais le total débité est ' . number_format($total, 0, ',', ' ') . ' Ar (montant + frais + commission).');
        }

        $typeModel = new TypesOperationModel();
        $typeTransfert = $typeModel->where('code', 'TRANSFERT')->first();

        $soldeAvantSource = (float) $clientSource['solde'];
        $soldeApresSource = $soldeAvantSource - $total;

        $referenceBase = 'TRA-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
        $transactionModel = new TransactionModel();
        $txId = $transactionModel->insert([
            'reference'             => $reference,
            'type_operation_id'     => $type['id'],
            'client_source_id'      => $clientSource['id'],
            'client_destination_id' => $clientDest['id'],
            'montant'               => $montant,
            'frais'                 => $frais,
            'commission'            => $commission,
            'type_transfert'        => $typeTransfert,
            'montant_total'         => $total,
            'solde_avant'           => $soldeAvantSource,
            'solde_apres'           => $soldeApresSource,
            'statut'                => 'REUSSI',
            'date_creation'         => date('Y-m-d H:i:s'),
        ]);

        if (!$memeOperateur && $txId) {
            $reversmentModel = new ReversmentModel();
            $reversmentModel->insert([
                'transaction_id'       => $txId,
                'operateur_source_id'  => $opSourceId,
                'operateur_dest_id'    => $opDestId,
                'montant'              => $montant,
                'statut'               => 'EN_ATTENTE',
                'date_creation'        => date('Y-m-d H:i:s'),
            ]);
        }

        $clientModel->update($clientSource['id'], ['solde' => $soldeApresSource]);
        $session->set('solde', $soldeApresSource);

        $msg = "Transfert de " . number_format($montant, 0, ',', ' ') . " Ar vers " . $numeroDest . " effectué";
        $msg .= " (type : " . $typeTransfert . ", frais : " . number_format($frais, 0, ',', ' ') . " Ar";
        if ($commission > 0) {
            $msg .= ", commission : " . number_format($commission, 0, ',', ' ') . " Ar";
        }
        $msg .= "). Nouveau solde : " . number_format($soldeApresSource, 0, ',', ' ') . " Ar.";

        return redirect()->to('/dashboard')->with('success', $msg);
    }
}
