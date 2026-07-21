# Tâches - Espace Client MobiCash
# Suite à partir de la section 12

## 12. Version 2 — Détection du type de transfert

- [ ] Identifier l'opérateur de l'expéditeur grâce à son préfixe
- [ ] Identifier l'opérateur du destinataire grâce à son préfixe
- [ ] Comparer les deux opérateurs
- [ ] Classer le transfert comme INTERNE ou EXTERNE
- [ ] Refuser un numéro dont le préfixe n'existe pas
- [ ] Refuser un préfixe désactivé
- [ ] Refuser un opérateur désactivé
- [ ] Enregistrer l'opérateur source dans la transaction
- [ ] Enregistrer l'opérateur destination dans la transaction
- [ ] Enregistrer le type de transfert dans la transaction

Règles :

- Même opérateur :
  - type_transfert = INTERNE
  - commission_inter_operateur = 0
  - total débité = montant + frais de transfert

- Opérateurs différents :
  - type_transfert = EXTERNE
  - commission_inter_operateur = 2 % des frais de transfert
  - total débité = montant + frais de transfert + commission

Exemple :

Montant : 50 000 Ar
Frais : 400 Ar
Commission : 400 × 2 % = 8 Ar
Total débité : 50 408 Ar

Attention :
La commission ne doit jamais être calculée sur le montant transféré.
Elle doit être calculée uniquement sur les frais de transfert.

## 13. Version 2 — Transfert externe

- [ ] Autoriser le transfert vers un autre opérateur
- [ ] Ne pas exiger qu'un destinataire externe existe dans la table clients
- [ ] Enregistrer le numéro externe dans la transaction
- [ ] Calculer les frais de transfert
- [ ] Calculer la commission de 2 % des frais
- [ ] Vérifier que le solde couvre montant + frais + commission
- [ ] Débiter l'expéditeur
- [ ] Ne pas créditer un compte local inexistant
- [ ] Enregistrer le montant à reverser à l'opérateur externe
- [ ] Mettre le statut de règlement à EN_ATTENTE
- [ ] Afficher un message de réussite clair
- [ ] Afficher le récapitulatif du transfert externe

Données à enregistrer :

- numero_destination_externe
- operateur_source_id
- operateur_destination_id
- type_transfert = EXTERNE
- frais_transfert
- pourcentage_commission = 2
- commission_inter_operateur
- montant_du_operateur
- statut_reglement = EN_ATTENTE

## 14. Migration Version 2 des transactions

- [ ] Créer une nouvelle migration CodeIgniter
- [ ] Ne pas modifier une ancienne migration déjà exécutée
- [ ] Ajouter operateur_source_id
- [ ] Ajouter operateur_destination_id
- [ ] Ajouter numero_destination_externe
- [ ] Ajouter type_transfert
- [ ] Ajouter frais_transfert
- [ ] Ajouter pourcentage_commission
- [ ] Ajouter commission_inter_operateur
- [ ] Ajouter montant_du_operateur
- [ ] Ajouter statut_reglement
- [ ] Ajouter date_reglement
- [ ] Ajouter les clés étrangères nécessaires
- [ ] Mettre à jour TransactionModel::$allowedFields

Valeurs possibles :

type_transfert :
- INTERNE
- EXTERNE
- NON_APPLICABLE

statut_reglement :
- NON_APPLICABLE
- EN_ATTENTE
- ENVOYE
- REGLE
- ANNULE

Pour un transfert interne :

- type_transfert = INTERNE
- commission_inter_operateur = 0
- montant_du_operateur = 0
- statut_reglement = NON_APPLICABLE

Pour un transfert externe :

- type_transfert = EXTERNE
- commission_inter_operateur = frais × 2 %
- montant_du_operateur = montant transféré
- statut_reglement = EN_ATTENTE

## 15. Service Mobile Money

Fichier conseillé :

app/Libraries/MobileMoneyService.php

Méthodes à créer ou compléter :

- [ ] genererReference()
- [ ] trouverOperateurParNumero()
- [ ] determinerTypeTransfert()
- [ ] calculerFrais()
- [ ] calculerCommissionInterOperateur()
- [ ] executerDepot()
- [ ] executerRetrait()
- [ ] executerTransfertInterne()
- [ ] executerTransfertExterne()
- [ ] executerTransfertMultiple()

Contrôles :

- [ ] Ne jamais utiliser montant × 2 %
- [ ] Toujours utiliser frais × 2 %
- [ ] Vérifier que le type d'opération est actif
- [ ] Vérifier qu'un barème existe
- [ ] Vérifier que le solde est suffisant
- [ ] Utiliser une transaction SQL
- [ ] Annuler toutes les modifications en cas d'erreur
- [ ] Garantir qu'aucun solde ne devient négatif
- [ ] Retourner des messages d'erreur compréhensibles

## 16. Transfert multiple

Nouvelle page :

app/Views/client/multiple_transfer.php

Routes :

- [ ] GET /transfert-multiple
- [ ] POST /transfert-multiple/executer

Fonctionnalités :

- [ ] Ajouter plusieurs numéros destinataires
- [ ] Supprimer un numéro avant confirmation
- [ ] Saisir un montant total
- [ ] Diviser le montant entre les destinataires
- [ ] Afficher le montant par destinataire
- [ ] Calculer les frais
- [ ] Calculer la commission si les opérateurs sont différents
- [ ] Afficher un récapitulatif
- [ ] Confirmer avant exécution

Validations :

- [ ] Minimum deux destinataires
- [ ] Aucun numéro en double
- [ ] Aucun transfert vers son propre numéro
- [ ] Tous les numéros doivent contenir 10 chiffres
- [ ] Tous les préfixes doivent exister et être actifs
- [ ] Tous les destinataires doivent appartenir au même opérateur
- [ ] Refuser une liste mélangeant plusieurs opérateurs
- [ ] Vérifier que le montant est positif
- [ ] Vérifier que le montant peut être réparti correctement
- [ ] Vérifier que le solde couvre le montant, les frais et la commission
- [ ] Toutes les opérations doivent réussir ensemble
- [ ] Si une opération échoue, tout annuler

## 17. Option « Inclure les frais de retrait »

Cette option concerne uniquement les transferts internes.

- [ ] Ajouter une case à cocher dans le formulaire de transfert
- [ ] Détecter automatiquement si le transfert est interne
- [ ] Afficher l'option uniquement pour un transfert interne
- [ ] Calculer les futurs frais de retrait du destinataire
- [ ] Ajouter ces frais au montant crédité au destinataire
- [ ] Afficher le montant principal
- [ ] Afficher les frais de retrait inclus
- [ ] Afficher les frais de transfert
- [ ] Afficher le total débité
- [ ] Ne pas appliquer cette option aux transferts externes

Exemple :

Montant principal : 20 000 Ar
Frais de retrait inclus : 200 Ar
Montant crédité : 20 200 Ar
Frais de transfert : 200 Ar
Total débité : 20 400 Ar

## 18. Historique Version 2

Informations supplémentaires :

- [ ] Type de transfert : INTERNE ou EXTERNE
- [ ] Opérateur source
- [ ] Opérateur destination
- [ ] Numéro source
- [ ] Numéro destination
- [ ] Frais de transfert
- [ ] Commission inter-opérateur
- [ ] Total débité
- [ ] Sens : ENTRANT ou SORTANT
- [ ] Statut de la transaction
- [ ] Statut du règlement externe

Filtres :

- [ ] Dépôt
- [ ] Retrait
- [ ] Transfert
- [ ] Transfert interne
- [ ] Transfert externe
- [ ] Opérateur destination
- [ ] Date de début
- [ ] Date de fin
- [ ] Référence
- [ ] Statut
- [ ] Pagination

Exports :

- [ ] Export CSV
- [ ] Export PDF
- [ ] Vérifier que les exports respectent les filtres appliqués

## 19. Profil client

Routes :

- [ ] GET /profil
- [ ] POST /profil/update

Fonctionnalités :

- [ ] Afficher le numéro
- [ ] Afficher l'opérateur
- [ ] Afficher le statut
- [ ] Afficher la date de création
- [ ] Afficher la dernière connexion
- [ ] Modifier les informations autorisées
- [ ] Interdire la modification directe du solde
- [ ] Interdire la modification directe de l'opérateur
- [ ] Ajouter une validation serveur
- [ ] Ajouter CSRF
- [ ] Afficher un message de succès ou d'erreur

## 20. Middleware et protection des routes

- [ ] Créer ClientAuthFilter
- [ ] Vérifier client_logged_in
- [ ] Vérifier client_id
- [ ] Rediriger vers la connexion si la session est absente
- [ ] Appliquer le filtre à /dashboard
- [ ] Appliquer le filtre à /depot
- [ ] Appliquer le filtre à /retrait
- [ ] Appliquer le filtre à /transfert
- [ ] Appliquer le filtre à /transfert-multiple
- [ ] Appliquer le filtre à /historique
- [ ] Appliquer le filtre à /profil
- [ ] Empêcher un client connecté d'accéder de nouveau à la page de connexion
- [ ] Détruire correctement la session à la déconnexion
- [ ] Empêcher l'accès aux opérations pour un client BLOQUE ou SUSPENDU

## 21. Sécurité complète

- [ ] Activer CSRF dans CodeIgniter
- [ ] Ajouter csrf_field() à tous les formulaires
- [ ] Ajouter une validation serveur pour chaque champ
- [ ] Refuser les montants nuls ou négatifs
- [ ] Refuser les montants non numériques
- [ ] Refuser les montants hors barème
- [ ] Refuser un solde insuffisant
- [ ] Refuser un numéro invalide
- [ ] Refuser un opérateur inconnu
- [ ] Refuser un opérateur désactivé
- [ ] Refuser un transfert vers soi-même
- [ ] Refuser les doublons dans un transfert multiple
- [ ] Utiliser esc() dans les vues
- [ ] Utiliser les modèles CodeIgniter pour les requêtes
- [ ] Utiliser les transactions SQL pour les opérations financières
- [ ] Générer des références uniques
- [ ] Ajouter une contrainte unique sur la référence
- [ ] Empêcher les doubles soumissions
- [ ] Ne jamais faire confiance aux frais calculés uniquement en JavaScript
- [ ] Recalculer les frais côté serveur avant l'opération

## 22. Améliorations UI/UX finales

- [ ] Affichage dynamique des frais de retrait
- [ ] Affichage dynamique des frais de transfert
- [ ] Affichage dynamique de la commission externe
- [ ] Détection visuelle de l'opérateur du destinataire
- [ ] Badge INTERNE / EXTERNE
- [ ] Récapitulatif dans une modal Bootstrap
- [ ] Bouton de confirmation finale
- [ ] Loader pendant le traitement
- [ ] Désactiver le bouton pendant la soumission
- [ ] Messages d'erreur près des champs
- [ ] Notifications toast Bootstrap
- [ ] Mise à jour du solde après opération
- [ ] Interface responsive mobile
- [ ] Thème sombre optionnel
- [ ] Améliorer l'accessibilité des formulaires

## 23. Tests fonctionnels obligatoires

### Connexion

- [ ] Connexion avec un numéro valide
- [ ] Refus d'un numéro invalide
- [ ] Refus d'un préfixe inconnu
- [ ] Refus d'un client bloqué
- [ ] Déconnexion
- [ ] Protection des routes

### Dépôt

- [ ] Dépôt de 50 000 Ar
- [ ] Solde mis à jour
- [ ] Frais égaux à 0
- [ ] Transaction enregistrée
- [ ] Référence unique
- [ ] Solde avant/après correct

### Retrait

- [ ] Retrait avec barème valide
- [ ] Frais corrects
- [ ] Total débité correct
- [ ] Refus si solde insuffisant
- [ ] Aucun changement si échec

### Transfert interne

- [ ] Même opérateur détecté
- [ ] Commission égale à 0
- [ ] Expéditeur débité
- [ ] Destinataire crédité
- [ ] Transaction enregistrée
- [ ] Solde des deux clients correct

### Transfert externe

- [ ] Opérateurs différents détectés
- [ ] Commission égale à 2 % des frais
- [ ] Commission non calculée sur le montant
- [ ] Expéditeur débité du bon total
- [ ] Montant à reverser correct
- [ ] Statut de règlement EN_ATTENTE
- [ ] Aucun compte interne inexistant crédité

### Transfert multiple

- [ ] Deux destinataires minimum
- [ ] Refus des doublons
- [ ] Refus du numéro personnel
- [ ] Refus du mélange d'opérateurs
- [ ] Répartition correcte
- [ ] Frais corrects
- [ ] Commission correcte
- [ ] Annulation totale si une opération échoue

### Historique

- [ ] Toutes les opérations visibles
- [ ] Sens ENTRANT / SORTANT correct
- [ ] Filtres fonctionnels
- [ ] Pagination fonctionnelle
- [ ] Export CSV
- [ ] Export PDF

## 24. Tests techniques

- [ ] php spark migrate fonctionne
- [ ] php spark migrate:status affiche toutes les migrations
- [ ] php spark db:seed fonctionne
- [ ] php spark routes affiche toutes les routes client
- [ ] php spark serve démarre sans erreur
- [ ] Aucune erreur 404
- [ ] Aucune erreur 500
- [ ] Aucun warning PHP
- [ ] Aucune table manquante
- [ ] Aucun champ manquant
- [ ] Aucun bouton principal sans action
- [ ] Aucun calcul financier stocké uniquement dans JavaScript
- [ ] Aucune donnée métier stockée dans localStorage

## 25. Documentation

- [ ] Mettre à jour README.md
- [ ] Expliquer l'installation
- [ ] Expliquer l'activation de SQLite
- [ ] Expliquer les migrations
- [ ] Expliquer les seeders
- [ ] Donner les comptes clients de test
- [ ] Donner les URL client
- [ ] Expliquer les règles de transfert interne
- [ ] Expliquer les règles de transfert externe
- [ ] Expliquer le calcul de la commission
- [ ] Expliquer l'envoi multiple
- [ ] Expliquer l'option frais de retrait inclus
- [ ] Ajouter les commandes de test

Commandes finales :

composer install
php spark migrate
php spark db:seed DatabaseSeeder
php spark routes
php spark serve

## 26. Git et livraison

- [ ] Vérifier git status
- [ ] Ajouter uniquement les fichiers utiles
- [ ] Ne pas envoyer writable/session
- [ ] Ne pas envoyer les fichiers temporaires
- [ ] Vérifier .gitignore
- [ ] Créer un commit clair
- [ ] Pousser la branche client
- [ ] Créer une Pull Request
- [ ] Résoudre les conflits avec la partie opérateur
- [ ] Tester le projet après fusion
- [ ] Créer le tag final v2.0.0

Commandes :

git status
git add .
git commit -m "Finalisation espace client MobiCash version 2"
git push origin client-Lova

Après fusion :

git checkout main
git pull origin main
php spark migrate
php spark db:seed DatabaseSeeder
php spark serve

Créer le tag :

git tag -a v2.0.0 -m "Version 2 MobiCash"
git push origin v2.0.0

## 27. Critères de fin de l'espace client

- [ ] Authentification client complète
- [ ] Routes protégées
- [ ] Dashboard dynamique
- [ ] Dépôt fonctionnel
- [ ] Retrait fonctionnel
- [ ] Transfert interne fonctionnel
- [ ] Transfert externe fonctionnel
- [ ] Commission correcte : 2 % des frais
- [ ] Transfert multiple fonctionnel
- [ ] Option frais de retrait incluse fonctionnelle
- [ ] Historique filtrable et paginé
- [ ] Export CSV/PDF fonctionnel
- [ ] Profil client fonctionnel
- [ ] CSRF et validations actives
- [ ] Toutes les données enregistrées dans SQLite
- [ ] Aucun bouton principal sans action
- [ ] Aucune erreur 404 ou 500
- [ ] Tests validés après fusion avec l'espace opérateur