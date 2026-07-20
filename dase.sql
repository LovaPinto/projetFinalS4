-- Opérateurs télécom (Telma, Orange, Airtel, ...)
CREATE TABLE operateurs (
    id              SERIAL PRIMARY KEY,
    nom             VARCHAR(50) NOT NULL UNIQUE,
    code            VARCHAR(10) NOT NULL UNIQUE,
    actif           BOOLEAN NOT NULL DEFAULT TRUE,
    date_creation   TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Préfixes
CREATE TABLE prefixes_operateur (
    id              SERIAL PRIMARY KEY,
    operateur_id    INT NOT NULL REFERENCES operateurs(id),
    prefixe         VARCHAR(5) NOT NULL UNIQUE,
    actif           BOOLEAN NOT NULL DEFAULT TRUE,
    date_creation   TIMESTAMP NOT NULL DEFAULT NOW()
);
-- Opérateurs (Telma, Orange, Airtel, ...)
CREATE TABLE operateurs (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    nom             TEXT NOT NULL UNIQUE,
    code            TEXT NOT NULL UNIQUE,
    actif           INTEGER NOT NULL DEFAULT 1,       
    date_creation   TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Préfixes valables par opérateur (ex: 033, 037)
CREATE TABLE prefixes_operateur (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_id    INTEGER NOT NULL REFERENCES operateurs(id),
    prefixe         TEXT NOT NULL UNIQUE,
    actif           INTEGER NOT NULL DEFAULT 1,
    date_creation   TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Types d'opérations (dépôt, retrait, transfert)
CREATE TABLE types_operation (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    code     TEXT NOT NULL UNIQUE,   -- DEPOT, RETRAIT, TRANSFERT
    libelle  TEXT NOT NULL,
    actif    INTEGER NOT NULL DEFAULT 1
);

-- Barèmes de frais par tranche de montant (modifiable, historisé)
CREATE TABLE baremes_frais (
    id                  INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id   INTEGER NOT NULL REFERENCES types_operation(id),
    montant_min         NUMERIC NOT NULL,
    montant_max         NUMERIC NOT NULL,
    frais               NUMERIC NOT NULL,
    actif               INTEGER NOT NULL DEFAULT 1,
    date_debut          TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_fin            TEXT,                
    CHECK (montant_max > montant_min)
);

-- ==================== CÔTÉ CLIENT ====================

-- Comptes clients (créés automatiquement au 1er login par numéro)
CREATE TABLE clients (
    id                       INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone         TEXT NOT NULL UNIQUE,
    operateur_id             INTEGER NOT NULL REFERENCES operateurs(id),
    solde                    NUMERIC NOT NULL DEFAULT 0 CHECK (solde >= 0),
    statut                   TEXT NOT NULL DEFAULT 'ACTIF',
    date_creation            TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_derniere_connexion  TEXT
);

-- Historique de toutes les opérations (dépôt / retrait / transfert)
CREATE TABLE transactions (
    id                      INTEGER PRIMARY KEY AUTOINCREMENT,
    reference               TEXT NOT NULL UNIQUE,
    type_operation_id       INTEGER NOT NULL REFERENCES types_operation(id),
    client_source_id        INTEGER REFERENCES clients(id),      -- débité (retrait, transfert)
    client_destination_id   INTEGER REFERENCES clients(id),      -- crédité (dépôt, transfert)
    montant                 NUMERIC NOT NULL CHECK (montant > 0),
    frais                   NUMERIC NOT NULL DEFAULT 0,
    montant_total           NUMERIC NOT NULL,          -- montant +/- frais
    solde_avant             NUMERIC NOT NULL,
    solde_apres             NUMERIC NOT NULL,
    statut                  TEXT NOT NULL DEFAULT 'REUSSI',  -- REUSSI, ECHEC, ANNULE
    date_creation           TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ==================== INDEX ====================
CREATE INDEX idx_transactions_source ON transactions(client_source_id);
CREATE INDEX idx_transactions_dest   ON transactions(client_destination_id);
CREATE INDEX idx_transactions_date   ON transactions(date_creation);
CREATE INDEX idx_clients_telephone   ON clients(numero_telephone);
CREATE INDEX idx_baremes_type        ON baremes_frais(type_operation_id);

-- ==================== VUES POUR L'OPÉRATEUR ====================

-- Situation des gains par type de frais (retrait, transfert...)
CREATE VIEW vue_gains_par_type AS
SELECT t.code,
       t.libelle,
       COUNT(tr.id)  AS nb_operations,
       SUM(tr.frais) AS total_frais_encaisses
FROM transactions tr
JOIN types_operation t ON t.id = tr.type_operation_id
WHERE tr.statut = 'REUSSI'
GROUP BY t.code, t.libelle;

-- Situation des comptes clients
CREATE VIEW vue_situation_clients AS
SELECT c.id,
       c.numero_telephone,
       o.nom AS operateur,
       c.solde,
       c.statut,
       c.date_creation,
       c.date_derniere_connexion
FROM clients c
JOIN operateurs o ON o.id = c.operateur_id;

-- ==================== DONNÉES DE BASE (SEED) ====================
INSERT INTO types_operation (code, libelle) VALUES
    ('DEPOT', 'Dépôt'),
    ('RETRAIT', 'Retrait'),
    ('TRANSFERT', 'Transfert');

-- Exemple de barème (à adapter / dupliquer pour retrait et transfert)
-- INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES
--   (2, 100,        1000,      50),
--   (2, 1001,       5000,      50),
--   (2, 5001,       10000,     100),
--   (2, 10001,      25000,     200),
--   (2, 25001,      50000,     400),
--   (2, 50001,      100000,    800),
--   (2, 100001,     250000,    1500),
--   (2, 250001,     500000,    1500),
--   (2, 500001,     1000000,   2500),
--   (2, 1000001,    2000000,   3000);

-- Types d'opérations (dépôt, retrait, transfert)
CREATE TABLE types_operation (
    id       SERIAL PRIMARY KEY,
    code     VARCHAR(20) NOT NULL UNIQUE,   -- DEPOT, RETRAIT, TRANSFERT
);

-- Barèmes de frais par tranche de montant (modifiable, historisé)
CREATE TABLE baremes_frais (
    id                  SERIAL PRIMARY KEY,
    type_operation_id   INT NOT NULL REFERENCES types_operation(id),
    montant_min         NUMERIC(14,2) NOT NULL,
    montant_max         NUMERIC(14,2) NOT NULL,
    frais               NUMERIC(14,2) NOT NULL,
    actif               BOOLEAN NOT NULL DEFAULT TRUE,
    date_debut          TIMESTAMP NOT NULL DEFAULT NOW(),
    date_fin            TIMESTAMP,          -- NULL = barème toujours en vigueur
    CONSTRAINT chk_tranche CHECK (montant_max > montant_min)
);

-- ==================== CÔTÉ CLIENT ====================

-- Comptes clients (créés automatiquement au 1er login par numéro)
CREATE TABLE clients (
    id                       SERIAL PRIMARY KEY,
    numero_telephone         VARCHAR(15) NOT NULL UNIQUE,
    operateur_id             INT NOT NULL REFERENCES operateurs(id),
    solde                    NUMERIC(14,2) NOT NULL DEFAULT 0 CHECK (solde >= 0),
    statut                   VARCHAR(20) NOT NULL DEFAULT 'ACTIF', -- ACTIF, BLOQUE, SUSPENDU
    date_creation            TIMESTAMP NOT NULL DEFAULT NOW(),
    date_derniere_connexion  TIMESTAMP
);

-- Historique de toutes les opérations (dépôt / retrait / transfert)
CREATE TABLE transactions (
    id                      SERIAL PRIMARY KEY,
    reference               VARCHAR(30) NOT NULL UNIQUE,
    type_operation_id       INT NOT NULL REFERENCES types_operation(id),
    client_source_id        INT REFERENCES clients(id),      -- débité (retrait, transfert)
    client_destination_id   INT REFERENCES clients(id),      -- crédité (dépôt, transfert)
    montant                 NUMERIC(14,2) NOT NULL CHECK (montant > 0),
    frais                   NUMERIC(14,2) NOT NULL DEFAULT 0,
    montant_total            NUMERIC(14,2) NOT NULL,          -- montant +/- frais
    solde_avant             NUMERIC(14,2) NOT NULL,
    solde_apres             NUMERIC(14,2) NOT NULL,
    statut                  VARCHAR(20) NOT NULL DEFAULT 'REUSSI', -- REUSSI, ECHEC, ANNULE
    date_creation            TIMESTAMP NOT NULL DEFAULT NOW()
);

-- ==================== INDEX ====================
CREATE INDEX idx_transactions_source ON transactions(client_source_id);
CREATE INDEX idx_transactions_dest   ON transactions(client_destination_id);
CREATE INDEX idx_transactions_date   ON transactions(date_creation);
CREATE INDEX idx_clients_telephone   ON clients(numero_telephone);
CREATE INDEX idx_baremes_type        ON baremes_frais(type_operation_id);

-- ==================== VUES UTILES POUR L'OPÉRATEUR ====================

-- Situation des gains par type de frais (retrait, transfert...)
CREATE VIEW vue_gains_par_type AS
SELECT t.code,
       t.libelle,
       COUNT(tr.id)        AS nb_operations,
       SUM(tr.frais)       AS total_frais_encaisses
FROM transactions tr
JOIN types_operation t ON t.id = tr.type_operation_id
WHERE tr.statut = 'REUSSI'
GROUP BY t.code, t.libelle;

-- Situation des comptes clients
CREATE VIEW vue_situation_clients AS
SELECT c.id,
       c.numero_telephone,
       o.nom AS operateur,
       c.solde,
       c.statut,
       c.date_creation,
       c.date_derniere_connexion
FROM clients c
JOIN operateurs o ON o.id = c.operateur_id;

-- ==================== DONNÉES DE BASE (SEED) ====================
INSERT INTO types_operation (code, libelle) VALUES
    ('DEPOT', 'Dépôt'),
    ('RETRAIT', 'Retrait'),
    ('TRANSFERT', 'Transfert');

-- Exemple de barème (à adapter / dupliquer pour retrait et transfert)
-- INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES
--   (2, 100,        1000,      50),
--   (2, 1001,       5000,      50),
--   (2, 5001,       10000,     100),
--   (2, 10001,      25000,     200),
--   (2, 25001,      50000,     400),
--   (2, 50001,      100000,    800),
--   (2, 100001,     250000,    1500),
--   (2, 250001,     500000,    1500),
--   (2, 500001,     1000000,   2500),
--   (2, 1000001,    2000000,   3000);