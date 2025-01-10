-- Table UTILISATEUR
CREATE TABLE UTILISATEUR (
    id_utilisateur INT IDENTITY(1,1) PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    adresse VARCHAR(200) NOT NULL,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    type_utilisateur VARCHAR(20) CHECK (type_utilisateur IN ('ADMIN', 'PRESIDENT', 'EVALUATEUR', 'COMPETITEUR', 'DIRECTEUR'))
);

-- Table CLUB
CREATE TABLE CLUB (
    id_club INT IDENTITY(1,1) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    adresse VARCHAR(200) NOT NULL,
    telephone VARCHAR(15) NOT NULL,
    nb_adherents INT NOT NULL CHECK (nb_adherents >= 0),
    ville VARCHAR(50) NOT NULL,
    departement VARCHAR(50) NOT NULL,
    region VARCHAR(50) NOT NULL,
    id_directeur INT NOT NULL,
    FOREIGN KEY (id_directeur) REFERENCES UTILISATEUR(id_utilisateur)
);

-- Table CONCOURS
CREATE TABLE CONCOURS (
    id_concours INT IDENTITY(1,1) PRIMARY KEY,
    theme VARCHAR(100) NOT NULL,
    descriptif TEXT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    etat VARCHAR(20) CHECK (etat IN ('NON_COMMENCE', 'EN_COURS', 'EN_ATTENTE', 'EVALUE')),
    id_president INT NOT NULL,
    FOREIGN KEY (id_president) REFERENCES UTILISATEUR(id_utilisateur),
    CHECK (date_fin > date_debut)
);

-- Table DESSIN
CREATE TABLE DESSIN (
    id_dessin INT IDENTITY(1,1) PRIMARY KEY,
    commentaire TEXT,
    classement INT,
    date_remise DATETIME NOT NULL,
    dessin_svg TEXT NOT NULL,
    id_competiteur INT NOT NULL,
    id_concours INT NOT NULL,
    FOREIGN KEY (id_competiteur) REFERENCES UTILISATEUR(id_utilisateur),
    FOREIGN KEY (id_concours) REFERENCES CONCOURS(id_concours)
);

-- Table EVALUATION
CREATE TABLE EVALUATION (
    id_evaluation INT IDENTITY(1,1) PRIMARY KEY,
    commentaire TEXT,
    note DECIMAL(4,2) CHECK (note >= 0 AND note <= 20),
    date_evaluation DATETIME NOT NULL,
    id_dessin INT NOT NULL,
    id_evaluateur INT NOT NULL,
    FOREIGN KEY (id_dessin) REFERENCES DESSIN(id_dessin),
    FOREIGN KEY (id_evaluateur) REFERENCES UTILISATEUR(id_utilisateur)
);

-- Table CLUB_CONCOURS
CREATE TABLE CLUB_CONCOURS (
    id_club INT NOT NULL,
    id_concours INT NOT NULL,
    PRIMARY KEY (id_club, id_concours),
    FOREIGN KEY (id_club) REFERENCES CLUB(id_club),
    FOREIGN KEY (id_concours) REFERENCES CONCOURS(id_concours)
);

-- Tables de spécialisation
CREATE TABLE PRESIDENT (
    id_president INT PRIMARY KEY,
    prime DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_president) REFERENCES UTILISATEUR(id_utilisateur)
);

CREATE TABLE EVALUATEUR (
    id_evaluateur INT PRIMARY KEY,
    specialite VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_evaluateur) REFERENCES UTILISATEUR(id_utilisateur)
);

CREATE TABLE COMPETITEUR (
    id_competiteur INT PRIMARY KEY,
    date_premiere_participation DATE NOT NULL,
    FOREIGN KEY (id_competiteur) REFERENCES UTILISATEUR(id_utilisateur)
);

-- Index pour optimiser les performances
CREATE INDEX idx_concours_dates ON CONCOURS(date_debut, date_fin);
CREATE INDEX idx_dessin_concours ON DESSIN(id_concours);
CREATE INDEX idx_evaluation_dessin ON EVALUATION(id_dessin);
