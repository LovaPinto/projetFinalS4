<?php

namespace App\Controllers;

use App\Models\ClientsModel;
use App\Models\PrefixOperateurModel;
use App\Models\TransactionModel;

class ClientsController extends BaseController
{
    /**
     * Affiche le formulaire de saisie du numéro de téléphone.
     */
    public function index()
    {
        return view('Template/operator/login.php');
    }

    /**
     * Login automatique par numéro de téléphone.
     * - Si le numéro existe déjà -> connexion, mise à jour de la dernière connexion.
     * - Si le numéro n'existe pas -> création automatique du compte, puis connexion.
     * Dans les deux cas -> création de la session et redirection vers le dashboard.
     */
    public function login()
    {
        $numero = trim((string) $this->request->getPost('numero_telephone'));

        // Validation basique du format (ex: 0341234567 -> 10 chiffres commençant par 0)
        if (!preg_match('/^0[0-9]{9}$/', $numero)) {
            return redirect()->back()->withInput()
                ->with('error', 'Numéro de téléphone invalide.');
        }

        $clientModel = new ClientsModel();
        $client      = $clientModel->where('numero_telephone', $numero)->first();
        $now         = date('Y-m-d H:i:s');

        if ($client) {
            // ---- Le client existe déjà ----
            $clientModel->update($client['id'], [
                'date_derniere_connexion' => $now,
            ]);
        } else {
            // ---- Nouveau client -> création automatique du compte ----
            $operateurId = $this->detecterOperateurId($numero);

            if ($operateurId === null) {
                return redirect()->back()->withInput()
                    ->with('error', 'Ce préfixe n\'est rattaché à aucun opérateur pris en charge.');
            }

            $clientId = $clientModel->insert([
                'numero_telephone'        => $numero,
                'operateur_id'            => $operateurId,
                'solde'                   => 0,
                'statut'                  => 'ACTIF',
                'date_creation'           => $now,
                'date_derniere_connexion' => $now,
            ]);

            $client = $clientModel->find($clientId);
        }

        if ($client['statut'] !== 'ACTIF') {
            return redirect()->back()->withInput()
                ->with('error', 'Ce compte est actuellement ' . strtolower($client['statut']) . '.');
        }

        // ---- Création de la session ----
        session()->set([
            'client_id'        => $client['id'],
            'numero_telephone' => $client['numero_telephone'],
            'operateur_id'     => $client['operateur_id'],
            'isLoggedIn'       => true,
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    /**
     * Tableau de bord du client connecté : solde à jour + dernières opérations.
     */
    public function dashboard()
    {
        // ---- Protection : il faut être connecté ----
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'Veuillez vous connecter pour accéder à votre espace.');
        }

        $clientModel = new ClientsModel();
        $client      = $clientModel->find(session()->get('client_id'));

        // Si le compte a été supprimé/désactivé entre-temps -> on force la reconnexion
        if (!$client) {
            session()->destroy();
            return redirect()->to('/')->with('error', 'Votre session a expiré, merci de vous reconnecter.');
        }

        $transactionModel = new TransactionModel();
        $historique        = $transactionModel->getHistoriqueClient($client['id'], 10);

        return view('Template/client/dashboard', [
            'client'     => $client,
            'historique' => $historique,
        ]);
    }

    /**
     * Retrouve l'opérateur correspondant au préfixe des 3 premiers chiffres du numéro.
     */
    private function detecterOperateurId(string $numero): ?int
    {
        $prefixe      = substr($numero, 0, 3);
        $prefixeModel = new PrefixOperateurModel();

        $row = $prefixeModel
            ->where('prefixe', $prefixe)
            ->where('actif', 1)
            ->first();

        return $row['operateur_id'] ?? null;
    }
}