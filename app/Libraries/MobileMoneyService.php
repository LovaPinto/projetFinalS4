<?php

namespace App\Libraries;

use App\Models\ClientsModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\TransactionModel;

class MobileMoneyService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Génère une référence unique pour une transaction.
     */
    public function generateReference(): string
    {
        $date = date('Ymd-His');
        $random = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        return "MM-{$date}-{$random}";
    }

    /**
     * Trouve un type d'opération par son code.
     */
    public function findOperationByCode(string $code): ?array
    {
        $model = new TypeOperationModel();
        return $model->where('code', $code)->where('actif', 1)->first();
    }

    /**
     * Calcule les frais selon le barème actif.
     */
    public function calculateFee(int $typeOperationId, float $montant): float
    {
        $baremeModel = new BaremeFraisModel();
        $bareme = $baremeModel
            ->where('type_operation_id', $typeOperationId)
            ->where('actif', 1)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();

        return $bareme ? (float) $bareme['frais'] : -1;
    }

    /**
     * Dépôt d'argent sur le compte d'un client.
     */
    public function deposit(int $clientId, float $montant): array
    {
        if ($montant < 100) {
            throw new \RuntimeException('Le montant minimum de dépôt est de 100 Ar.');
        }

        $op = $this->findOperationByCode('DEPOT');
        if (!$op) {
            throw new \RuntimeException('L\'opération DEPOT n\'est pas disponible.');
        }

        $clientModel = new ClientsModel();
        $client = $clientModel->find($clientId);
        if (!$client) {
            throw new \RuntimeException('Client introuvable.');
        }
        if ($client['statut'] !== 'ACTIF') {
            throw new \RuntimeException('Votre compte est ' . strtolower($client['statut']) . '.');
        }

        $this->db->transStart();

        $soldeAvant = (float) $client['solde'];
        $soldeApres = $soldeAvant + $montant;

        $clientModel->update($clientId, ['solde' => $soldeApres]);

        $transactionModel = new TransactionModel();
        $ref = $this->generateReference();
        $transactionModel->insert([
            'reference'              => $ref,
            'type_operation_id'      => $op['id'],
            'client_source_id'       => null,
            'client_destination_id'  => $clientId,
            'montant'                => $montant,
            'frais'                  => 0,
            'montant_total'          => $montant,
            'solde_avant'            => $soldeAvant,
            'solde_apres'            => $soldeApres,
            'statut'                 => 'REUSSI',
            'date_creation'          => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Erreur lors du dépôt. Transaction annulée.');
        }

        return [
            'reference'    => $ref,
            'montant'      => $montant,
            'frais'        => 0,
            'montant_total'=> $montant,
            'solde_avant'  => $soldeAvant,
            'solde_apres'  => $soldeApres,
        ];
    }

    /**
     * Retrait d'argent du compte d'un client.
     */
    public function withdraw(int $clientId, float $montant): array
    {
        if ($montant < 100) {
            throw new \RuntimeException('Le montant minimum de retrait est de 100 Ar.');
        }

        $op = $this->findOperationByCode('RETRAIT');
        if (!$op) {
            throw new \RuntimeException('L\'opération RETRAIT n\'est pas disponible.');
        }

        $frais = $this->calculateFee($op['id'], $montant);
        if ($frais < 0) {
            throw new \RuntimeException('Aucun barème de frais ne correspond à ce montant. Opération impossible.');
        }

        $totalDebit = $montant + $frais;

        $clientModel = new ClientsModel();
        $client = $clientModel->find($clientId);
        if (!$client) {
            throw new \RuntimeException('Client introuvable.');
        }
        if ($client['statut'] !== 'ACTIF') {
            throw new \RuntimeException('Votre compte est ' . strtolower($client['statut']) . '.');
        }

        $soldeAvant = (float) $client['solde'];
        if ($soldeAvant < $totalDebit) {
            throw new \RuntimeException(
                'Solde insuffisant. Solde disponible : ' . number_format($soldeAvant, 0, ',', ' ') .
                ' Ar. Total débité : ' . number_format($totalDebit, 0, ',', ' ') . ' Ar.'
            );
        }

        $this->db->transStart();

        $soldeApres = $soldeAvant - $totalDebit;
        $clientModel->update($clientId, ['solde' => $soldeApres]);

        $transactionModel = new TransactionModel();
        $ref = $this->generateReference();
        $transactionModel->insert([
            'reference'              => $ref,
            'type_operation_id'      => $op['id'],
            'client_source_id'       => $clientId,
            'client_destination_id'  => null,
            'montant'                => $montant,
            'frais'                  => $frais,
            'montant_total'          => $totalDebit,
            'solde_avant'            => $soldeAvant,
            'solde_apres'            => $soldeApres,
            'statut'                 => 'REUSSI',
            'date_creation'          => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Erreur lors du retrait. Transaction annulée.');
        }

        return [
            'reference'     => $ref,
            'montant'       => $montant,
            'frais'         => $frais,
            'montant_total' => $totalDebit,
            'solde_avant'   => $soldeAvant,
            'solde_apres'   => $soldeApres,
        ];
    }

    /**
     * Transfert d'argent d'un client à un autre.
     */
    public function transfer(int $sourceId, string $destNumero, float $montant): array
    {
        if ($montant < 100) {
            throw new \RuntimeException('Le montant minimum de transfert est de 100 Ar.');
        }

        $op = $this->findOperationByCode('TRANSFERT');
        if (!$op) {
            throw new \RuntimeException('L\'opération TRANSFERT n\'est pas disponible.');
        }

        $frais = $this->calculateFee($op['id'], $montant);
        if ($frais < 0) {
            throw new \RuntimeException('Aucun barème de frais ne correspond à ce montant. Opération impossible.');
        }

        $totalDebit = $montant + $frais;

        $clientModel = new ClientsModel();
        $source = $clientModel->find($sourceId);
        if (!$source) {
            throw new \RuntimeException('Client source introuvable.');
        }
        if ($source['statut'] !== 'ACTIF') {
            throw new \RuntimeException('Votre compte est ' . strtolower($source['statut']) . '.');
        }

        $destination = $clientModel->where('numero_telephone', $destNumero)->first();
        if (!$destination) {
            throw new \RuntimeException('Le numéro destinataire ' . esc($destNumero) . ' n\'existe pas.');
        }
        if ($destination['statut'] !== 'ACTIF') {
            throw new \RuntimeException('Le compte destinataire est ' . strtolower($destination['statut']) . '.');
        }
        if ((int) $destination['id'] === (int) $sourceId) {
            throw new \RuntimeException('Vous ne pouvez pas effectuer un transfert vers votre propre numéro.');
        }

        $soldeAvantSource = (float) $source['solde'];
        if ($soldeAvantSource < $totalDebit) {
            throw new \RuntimeException(
                'Solde insuffisant. Solde disponible : ' . number_format($soldeAvantSource, 0, ',', ' ') .
                ' Ar. Total débité : ' . number_format($totalDebit, 0, ',', ' ') . ' Ar.'
            );
        }

        $this->db->transStart();

        $soldeApresSource = $soldeAvantSource - $totalDebit;
        $clientModel->update($sourceId, ['solde' => $soldeApresSource]);

        $soldeAvantDest = (float) $destination['solde'];
        $soldeApresDest = $soldeAvantDest + $montant;
        $clientModel->update($destination['id'], ['solde' => $soldeApresDest]);

        $transactionModel = new TransactionModel();
        $ref = $this->generateReference();
        $transactionModel->insert([
            'reference'              => $ref,
            'type_operation_id'      => $op['id'],
            'client_source_id'       => $sourceId,
            'client_destination_id'  => $destination['id'],
            'montant'                => $montant,
            'frais'                  => $frais,
            'montant_total'          => $totalDebit,
            'solde_avant'            => $soldeAvantSource,
            'solde_apres'            => $soldeApresSource,
            'statut'                 => 'REUSSI',
            'date_creation'          => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new \RuntimeException('Erreur lors du transfert. Transaction annulée.');
        }

        return [
            'reference'            => $ref,
            'montant'              => $montant,
            'frais'                => $frais,
            'montant_total'        => $totalDebit,
            'solde_avant'          => $soldeAvantSource,
            'solde_apres'          => $soldeApresSource,
            'destinataire_numero'  => $destNumero,
            'destinataire_solde'   => $soldeApresDest,
        ];
    }
}
