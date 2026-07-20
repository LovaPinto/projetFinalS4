# MobiCash - Application Mobile Money

Application Mobile Money complète développée avec CodeIgniter 4, SQLite, Bootstrap 5.

## Prérequis

- PHP 8.2+ avec les extensions `sqlite3` et `pdo_sqlite`
- Wamp64 (ou tout serveur PHP)
- Composer

### Activer SQLite dans Wamp

1. Ouvrir le menu Wamp dans la barre des tâches
2. Cliquer sur PHP > PHP extensions
3. Cocher `sqlite3` et `pdo_sqlite`
4. Redémarrer les services Apache

## Installation

### Méthode automatique

Double-cliquer sur `setup.bat` :

```
setup.bat
```

### Méthode manuelle

```bash
# 1. Créer le dossier pour la base
mkdir writable\database

# 2. Installer les dépendances
composer install

# 3. Lancer les migrations
php spark migrate

# 4. Insérer les données initiales
php spark db:seed AppelToutes
```

> Si `php` n'est pas reconnu, utilisez le chemin complet :
> `C:\wamp64\bin\php\php8.2.13\php.exe spark migrate`

## Démarrage

```bash
php spark serve
```

Ou double-cliquer sur `start.bat`.

Le serveur démarre sur : **http://localhost:8081**

## URLs

| Page | URL |
|------|-----|
| Accueil | http://localhost:8081/ |
| Connexion opérateur | http://localhost:8081/operator/login |
| Connexion client | http://localhost:8081/client/login |

## Identifiants de test

### Opérateur
- **E-mail** : admin@mobile.mg
- **Mot de passe** : admin123

### Clients
- **0331234567** (solde : 125 000 Ar)
- **0379876543** (solde : 70 000 Ar)

## Structure du projet

```
app/
├── Config/          Routes, Database, Filters, Autoload
├── Controllers/     Home, OperatorController, ClientController
├── Filters/         ClientAuthFilter, OperatorAuthFilter
├── Libraries/       MobileMoneyService
├── Models/          Operateur, Prefix, TypeOperation, BaremeFrais, Clients, Transaction
├── Database/
│   ├── Migrations/  6 migrations (toutes les tables)
│   └── Seeds/       DatabaseSeeder complet
└── Views/
    ├── layouts/     operator.php, client.php
    ├── partials/    alerts.php
    ├── operator/    login, dashboard, prefixes, operations, fees, clients, transactions, gains
    └── client/      login, dashboard, deposit, withdraw, transfer, history
```

## Scénarios de test

### 1. Connexion client
- Se connecter avec `0331234567`
- Le tableau de bord affiche le solde réel depuis SQLite

### 2. Dépôt (125 000 + 50 000 = 175 000 Ar)
- Aller sur Dépôt
- Saisir 50000
- Solde attendu : 175 000 Ar, Frais : 0 Ar

### 3. Retrait (175 000 - 20 200 = 154 800 Ar)
- Aller sur Retrait
- Saisir 20000
- Frais : 200 Ar, Total débité : 20 200 Ar
- Nouveau solde : 154 800 Ar

### 4. Transfert
- Source : 0331234567 → Destination : 0379876543
- Montant : 10 000 Ar, Frais : 100 Ar
- Source débitée : 10 100 Ar, Destinataire crédité : 10 000 Ar

### 5. Solde insuffisant
- Tenter un retrait supérieur au solde + frais → refusé

### 6. Transfert vers soi-même
- Tenter de s'envoyer de l'argent → refusé

### 7. Préfixe invalide
- Connexion avec un numéro dont le préfixe n'est pas actif → refusé

### 8. Compte bloqué
- L'opérateur bloque un client → le client ne peut plus se connecter

### 9. Gains opérateur
- Le total des gains = somme des frais des transactions réussies
