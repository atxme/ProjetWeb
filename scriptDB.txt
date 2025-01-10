drop table if exists Utilisateur;
drop table if exists Admin;
drop table if exists President;
drop table if exists Competiteur;
drop table if exists Evaluateur;
drop table if exists Club;
drop table if exists Directeur;
drop table if exists Concours;
drop table if exists ParticipeClub;
drop table if exists ParticipeCompetiteur;
drop table if exists Dessin;
drop table if exists Jury;
drop table if exists Evaluation;


-- Creation des tables selon le schema logique

CREATE TABLE Utilisateur (
    numUtilisateur INT PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    adresse VARCHAR(255),
    login VARCHAR(50),
    mdp VARCHAR(50)
);


CREATE TABLE Admin (
    numAdmin INT PRIMARY KEY,
    dateDebut DATE
);

CREATE TABLE President (
    numPresident INT PRIMARY KEY,
    dateDebut DATE
);

CREATE TABLE Competiteur (
    numCompetiteur INT PRIMARY KEY,
    numUtilisateur INT NOT NULL,
    datePremiereParticipation DATE,
    nbCompetitionConsecutive INT,
    FOREIGN KEY (numUtilisateur) REFERENCES Utilisateur(numUtilisateur)
);

CREATE TABLE Evaluateur (
    numEvaluateur INT PRIMARY KEY,
    specialite VARCHAR(50)
);

CREATE TABLE Club (
    numClub INT PRIMARY KEY,
    nomClub VARCHAR(100),
    adresse VARCHAR(255),
    numTel VARCHAR(15),
    nbAdherents INT,
    ville VARCHAR(50),
    departement VARCHAR(50),
    region VARCHAR(50)
);

CREATE TABLE Directeur (
    numDirecteur INT PRIMARY KEY,
    numClub INT NOT NULL,
    dateDebut DATE,
    FOREIGN KEY (numClub) REFERENCES Club(numClub)
);

CREATE TABLE Concours (
    numConcours INT PRIMARY KEY,
    numPresident INT NOT NULL,
    theme VARCHAR(100),
    dateDeb DATE,
    dateFin DATE,
    etat ENUM('pas commence', 'en cours', 'attente', 'resultat', 'evalue'),
    FOREIGN KEY (numPresident) REFERENCES President(numPresident)
);

CREATE TABLE ParticipeClub (
    numConcours INT NOT NULL,
    numClub INT NOT NULL,
    PRIMARY KEY (numConcours, numClub),
    FOREIGN KEY (numConcours) REFERENCES Concours(numConcours),
    FOREIGN KEY (numClub) REFERENCES Club(numClub)
);

CREATE TABLE ParticipeCompetiteur (
    numConcours INT NOT NULL,
    numCompetiteur INT NOT NULL,
    PRIMARY KEY (numConcours, numCompetiteur),
    FOREIGN KEY (numConcours) REFERENCES Concours(numConcours),
    FOREIGN KEY (numCompetiteur) REFERENCES Competiteur(numCompetiteur)
);

CREATE TABLE Dessin (
    numDessin INT PRIMARY KEY,
    numCompetiteur INT NOT NULL,
    numConcours INT NOT NULL,
    classement INT,
    commentaire TEXT,
    dateRemise DATE,
    leDessin BLOB,
    FOREIGN KEY (numCompetiteur) REFERENCES Competiteur(numCompetiteur),
    FOREIGN KEY (numConcours) REFERENCES Concours(numConcours)
);

CREATE TABLE Jury (
    numEvaluateur INT NOT NULL,
    numConcours INT NOT NULL,
    PRIMARY KEY (numEvaluateur, numConcours),
    FOREIGN KEY (numEvaluateur) REFERENCES Evaluateur(numEvaluateur),
    FOREIGN KEY (numConcours) REFERENCES Concours(numConcours)
);

CREATE TABLE Evaluation (
    numDessin INT NOT NULL,
    numEvaluateur INT NOT NULL,
    dateEvaluation DATE,
    note INT,
    commentaire TEXT,
    PRIMARY KEY (numDessin, numEvaluateur),
    FOREIGN KEY (numDessin) REFERENCES Dessin(numDessin),
    FOREIGN KEY (numEvaluateur) REFERENCES Evaluateur(numEvaluateur)
);
