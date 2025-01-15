-- Désactiver la vérification des clés étrangères
SET
    FOREIGN_KEY_CHECKS = 0;

drop table if exists Utilisateur;

drop table if exists Admin;

drop table if exists President;

drop table if exists Competiteur;

drop table if exists Evaluateur;

drop table if exists Club;

drop table if exists Directeur;

drop table if exists Concours;

drop table if exists ClubParticipe;

drop table if exists CompetiteurParticipe;

drop table if exists Dessin;

drop table if exists Jury;

drop table if exists Evaluation;

-- Réactiver la vérification des clés étrangères
SET
    FOREIGN_KEY_CHECKS = 1;

-- Creation des tables selon le schema logique
CREATE TABLE
    Club (
        numClub INT PRIMARY KEY,
        nomClub VARCHAR(100),
        adresse VARCHAR(255),
        numTel VARCHAR(15),
        nbAdherents INT,
        ville VARCHAR(50),
        departement VARCHAR(50),
        region VARCHAR(50)
    );

CREATE TABLE
    President (numPresident INT PRIMARY KEY, dateDebut DATE);

CREATE TABLE
    Admin (numAdmin INT PRIMARY KEY, dateDebut DATE);

CREATE TABLE
    Evaluateur (
        numEvaluateur INT PRIMARY KEY,
        specialite VARCHAR(50)
    );

CREATE TABLE
    Competiteur (
        numCompetiteur INT PRIMARY KEY,
        datePremiereParticipation DATE,
        classement INT,
        noteMoyenne FLOAT
    );

CREATE TABLE
    Utilisateur (
        numUtilisateur INT PRIMARY KEY,
        numClub INT,
        nom VARCHAR(50),
        prenom VARCHAR(50),
        age INT,
        adresse VARCHAR(255),
        login VARCHAR(50),
        mdp VARCHAR(60),
        FOREIGN KEY (numClub) REFERENCES Club (numClub)
    );

CREATE TABLE
    Directeur (
        numDirecteur INT PRIMARY KEY,
        numClub INT NOT NULL,
        dateDebut DATE,
        FOREIGN KEY (numClub) REFERENCES Club (numClub)
    );

CREATE TABLE
    Concours (
        numConcours INT PRIMARY KEY,
        numPresident INT NOT NULL,
        theme VARCHAR(100),
        dateDeb DATE,
        dateFin DATE,
        etat ENUM (
            'pas commence',
            'en cours',
            'attente',
            'resultat',
            'evalue'
        ),
        nbClub INT,
        nbParticipant INT,
        descriptif TEXT,
        FOREIGN KEY (numPresident) REFERENCES President (numPresident)
    );

CREATE TABLE
    ClubParticipe (
        numConcours INT NOT NULL,
        numClub INT NOT NULL,
        PRIMARY KEY (numConcours, numClub),
        FOREIGN KEY (numConcours) REFERENCES Concours (numConcours),
        FOREIGN KEY (numClub) REFERENCES Club (numClub)
    );

CREATE TABLE
    CompetiteurParticipe (
        numConcours INT NOT NULL,
        numCompetiteur INT NOT NULL,
        PRIMARY KEY (numConcours, numCompetiteur),
        FOREIGN KEY (numConcours) REFERENCES Concours (numConcours),
        FOREIGN KEY (numCompetiteur) REFERENCES Competiteur (numCompetiteur)
    );

CREATE TABLE
    Dessin (
        numDessin INT PRIMARY KEY AUTO_INCREMENT,
        numCompetiteur INT NOT NULL,
        numConcours INT NOT NULL,
        classement INT,
        commentaire TEXT,
        dateRemise DATE,
        leDessin VARCHAR(255),
        FOREIGN KEY (numCompetiteur) REFERENCES Competiteur (numCompetiteur),
        FOREIGN KEY (numConcours) REFERENCES Concours (numConcours)
    );

CREATE TABLE
    Jury (
        numEvaluateur INT NOT NULL,
        numConcours INT NOT NULL,
        PRIMARY KEY (numEvaluateur, numConcours),
        FOREIGN KEY (numEvaluateur) REFERENCES Evaluateur (numEvaluateur),
        FOREIGN KEY (numConcours) REFERENCES Concours (numConcours)
    );

CREATE TABLE
    Evaluation (
        numDessin INT NOT NULL,
        numEvaluateur INT NOT NULL,
        dateEvaluation DATE,
        note INT,
        commentaire TEXT,
        PRIMARY KEY (numDessin, numEvaluateur),
        FOREIGN KEY (numDessin) REFERENCES Dessin (numDessin),
        FOREIGN KEY (numEvaluateur) REFERENCES Evaluateur (numEvaluateur)
    );