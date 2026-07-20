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

- [x] Affichage du solde (lu depuis la base de données)
- [x] Affichage du numéro de téléphone et de l'opérateur
- [x] Liste des 20 dernières opérations
- [x] Messages flash (succès / erreur)
- [x] Sidebar et topbar dynamiques
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

- [x] Page de transfert avec numéro destinataire + montant
- [x] Validation du numéro destinataire
- [x] Vérification que le destinataire existe
- [x] Empêcher le transfert vers soi-même
- [x] Calcul automatique des frais (barème TRANSFERT)
- [x] Vérification du solde suffisant (montant + frais)
- [x] Débit de l'expéditeur + Crédit du destinataire
- [x] Création de la transaction (TRANSFERT)
- [x] Génération de référence unique (TRA-...)
- [ ] Affichage dynamique des frais en temps réel (JavaScript)
- [ ] Affichage d'un récapitulatif avant confirmation

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
- [x] BaremesFraisModel (avec calculerFrais)
- [x] TypesOperationModel
- [ ] OperateurModel (à vérifier les allowedFields)
- [ ] PrefixOperateurModel (à vérifier les allowedFields)

## 8. Seeds / Données de test

- [x] OperateursSeeder (Orange, Airtel, Yas)
- [x] PrefixesOperateurSeeder (034, 038, 033, 032)
- [x] TypesOperationSeeder (DEPOT, RETRAIT, TRANSFERT)
- [x] BaremesFraisSeeder (tranches de frais)
- [x] ClientsSeeder (6 clients de test)
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
- [x] POST /transfert/executer → Exécuter le transfert
- [x] GET  /historique     → Page d'historique
- [ ] GET  /profil         → Page profil client
- [ ] POST /profil/update  → Modifier le profil

## 10. Sécurité

- [ ] Protection CSRF sur tous les formulaires
- [ ] Validation côté serveur sur toutes les opérations
- [ ] Vérification de session sur chaque requête
- [ ] Empêcher les montants négatifs
- [ ] Empêcher les références en double
- [ ] Protection contre les injections SQL (already via CI4 Model)

## 11. Améliorations UI/UX

- [ ] Affichage dynamique des frais en JS (avant soumission)
- [ ] Confirmation avant soumission (modal)
- [ ] Loader pendant le traitement
- [ ] Notifications push (optionnel)
- [ ] Page profil client (voir/modifier ses infos)
- [ ] Thème sombre (optionnel)
