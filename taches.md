# Tâches - Espace Client MobiCash

## 1. Authentification

- [x] Page de connexion par numéro de téléphone
- [x] Validation du format du numéro (0XX, 10 chiffres)
- [x] Création automatique du compte si inexistant
- [x] Détection de l'opérateur via le préfixe
- [x] Stockage des informations en session
- [x] Déconnexion et destruction de la session
- [ ] Protéger les routes clients (middleware)
- [ ] Empêcher l'accès au dashboard si déjà connecté

## 2. Tableau de bord

- [x] Affichage du solde (lu depuis la base de données, mis à jour après chaque opération)
- [x] Affichage du numéro de téléphone et de l'opérateur
- [x] Liste des 20 dernières opérations
- [x] Messages flash (succès / erreur)
- [x] Sidebar et topbar dynamiques avec liens fonctionnels
- [ ] Affichage du solde en temps réel après opération

## 3. Dépôt

- [x] Page de dépôt avec formulaire de montant
- [x] Validation du montant (> 0)
- [x] Création de la transaction (DEPOT, frais = 0)
- [x] Mise à jour du solde client dans la base
- [x] Génération de référence unique (DEP-...)
- [x] Enregistrement du solde avant/après
- [ ] Affichage d'un récapitulatif avant confirmation

## 4. Retrait

- [x] Page de retrait avec formulaire de montant
- [x] Calcul automatique des frais (barème RETRAIT)
- [x] Vérification du solde suffisant (montant + frais)
- [x] Création de la transaction (RETRAIT)
- [x] Mise à jour du solde client
- [x] Génération de référence unique (RET-...)
- [ ] Affichage dynamique des frais en temps réel (JavaScript)
- [ ] Affichage d'un récapitulatif avant confirmation

## 5. Transfert

### 5.1 Transfert simple
- [x] Page de transfert avec formulaire destinataire + montant
- [x] Validation du numéro destinataire (format 0XX)
- [x] Vérification que le destinataire existe dans la base
- [x] Empêcher le transfert vers soi-même
- [x] Calcul automatique des frais de transfert (barème TRANSFERT)
- [x] Vérification du solde suffisant (montant + frais transfert)
- [x] Débit de l'expéditeur + Crédit du destinataire
- [x] Création de la transaction (TRANSFERT)
- [x] Génération de référence unique (TRA-...)

### 5.2 Transfert multiple (envoi vers plusieurs numéros)
- [x] Formulaire dynamique avec ajout/suppression de destinataires
- [x] Le montant total est divisé équitablement entre les bénéficiaires
- [x] Validation que tous les numéros sont au format correct
- [x] Détection et rejet des numéros en double
- [x] Calcul des frais de transfert par destinataire (montant / nbDest)
- [x] Une seule transaction créée par destinataire
- [x] Débit total unique de l'expéditeur

### 5.3 Validation même opérateur
- [x] Vérification que tous les destinataires appartiennent au même opérateur
- [x] Message d'erreur si opérateurs mixtes : "Tous les bénéficiaires doivent appartenir au même opérateur."
- [x] Détection de l'opérateur via le préfixe du numéro (034/038=Yas, 033=Airtel, 032=Orange)

### 5.4 Option inclure frais de retrait
- [x] Case à cocher "Inclure les frais de retrait du bénéficiaire"
- [x] L'expéditeur paie les frais de retrait à la place du bénéficiaire
- [x] Le bénéficiaire reçoit le montant complet sans frais
- [x] Si non coché, le bénéficiaire paiera les frais lors du retrait

### 5.5 Frais de retrait par opérateur
- [x] BaremesFraisModel : nouvelle méthode `calculerFraisRetrait(operateurId, montant)`
- [x] BaremesFraisModel : nouvelle méthode `operateurAFraisRetrait(operateurId)`
- [x] BaremesFraisModel : nouvelle méthode `calculerFraisTransfert(montant)`
- [x] Colonne `operateur_id` ajoutée à la table `baremes_frais` (nullable, null = générique)
- [x] Seul l'opérateur Yas (034/038) a des frais de retrait configurés
- [x] Orange (032) et Airtel (033) → pas de frais de retrait
- [x] La case frais retrait n'apparaît que si l'opérateur destinataire facture des frais

### 5.6 UI transfert
- [x] Résumé dynamique en JS (montant, par bénéficiaire, frais, total)
- [x] Ajout/suppression dynamique de destinataires
- [x] Alerte si opérateurs mixtes détectés
- [x] Affichage du solde actuel dans le résumé

## 6. Historique

- [x] Page d'historique avec toutes les opérations
- [x] Affichage : référence, type, montant, frais, solde après, statut, date
- [x] Badge coloré selon le statut (Réussi / Échoué)
- [x] Message si aucune opération
- [ ] Filtrage par type d'opération
- [ ] Filtrage par date (période)
- [ ] Pagination
- [ ] Export CSV / PDF

## 7. Modèles (Models)

- [x] ClientsModel
- [x] TransactionModel (avec getHistoriqueClient)
- [x] BaremesFraisModel (avec calculerFrais, calculerFraisTransfert, calculerFraisRetrait, operateurAFraisRetrait)
- [x] TypesOperationModel
- [x] OperateurModel
- [x] PrefixOperateurModel

## 8. Seeds / Données de test

- [x] OperateursSeeder (Orange, Airtel, Yas)
- [x] PrefixesOperateurSeeder (034, 038, 033, 032)
- [x] TypesOperationSeeder (DEPOT, RETRAIT, TRANSFERT)
- [x] BaremesFraisSeeder (tranches de frais retrait par opérateur + frais transfert)
- [x] ClientsSeeder (6 clients de test avec soldes)
- [x] TransactionsSeeder (dépôts, retrait, transfert de test)

## 9. Routes

- [x] GET  /               → Page de connexion
- [x] POST /login          → Traitement connexion
- [x] GET  /logout         → Déconnexion
- [x] GET  /dashboard      → Tableau de bord
- [x] GET  /depot          → Page de dépôt
- [x] POST /depot/executer → Exécuter le dépôt
- [x] GET  /retrait        → Page de retrait
- [x] POST /retrait/executer → Exécuter le retrait
- [x] GET  /transfert      → Page de transfert
- [x] POST /transfert/executer → Exécuter le transfert (multiple + frais retrait)
- [x] GET  /historique     → Page d'historique
- [ ] GET  /profil         → Page profil client
- [ ] POST /profil/update  → Modifier le profil

## 10. Sécurité

- [x] Protection CSRF sur tous les formulaires (csrf_field())
- [x] Validation côté serveur sur toutes les opérations
- [x] Vérification de session sur chaque requête (isLoggedIn)
- [x] Empêcher les montants négatifs
- [x] Empêcher les références en double (génération unique)
- [x] Vérification solde suffisant avant chaque opération
- [x] Vérification que le destinataire existe
- [x] Empêcher transfert vers soi-même
- [x] Vérification même opérateur pour transfert multiple
- [ ] Protection contre les injections SQL (via CI4 Model)

## 11. Améliorations UI/UX

- [x] Affichage dynamique des frais en JS (transfert)
- [x] Résumé dynamique avant soumission (transfert)
- [ ] Confirmation avant soumission (modal)
- [ ] Loader pendant le traitement
- [ ] Notifications push (optionnel)
- [ ] Page profil client (voir/modifier ses infos)
- [ ] Thème sombre (optionnel)

promotion frais de transfert meme operateur
