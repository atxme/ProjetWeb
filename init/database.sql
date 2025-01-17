SET FOREIGN_KEY_CHECKS = 0;

-- Suppression des tables existantes
DROP TABLE IF EXISTS Utilisateur;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS President;
DROP TABLE IF EXISTS Competiteur;
DROP TABLE IF EXISTS Evaluateur;
DROP TABLE IF EXISTS Club;
DROP TABLE IF EXISTS Directeur;
DROP TABLE IF EXISTS Concours;
DROP TABLE IF EXISTS ClubParticipe;
DROP TABLE IF EXISTS CompetiteurParticipe;
DROP TABLE IF EXISTS Dessin;
DROP TABLE IF EXISTS Jury;
DROP TABLE IF EXISTS Evaluation;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE Club (
    numClub INT PRIMARY KEY,
    nomClub VARCHAR(100) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    numTel VARCHAR(15) NOT NULL,
    nbAdherents INT NOT NULL,
    ville VARCHAR(50) NOT NULL,
    departement VARCHAR(50) NOT NULL,
    region VARCHAR(50) NOT NULL
);

CREATE TABLE Utilisateur (
    numUtilisateur INT PRIMARY KEY,
    numClub INT NOT NULL DEFAULT 0,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    login VARCHAR(50) NOT NULL UNIQUE,
    mdp VARCHAR(60) NOT NULL,
    FOREIGN KEY (numClub) REFERENCES Club (numClub)
);

CREATE TABLE President (
    numPresident INT PRIMARY KEY,
    dateDebut DATE NOT NULL,
    prime DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (numPresident) REFERENCES Utilisateur(numUtilisateur)
);

CREATE TABLE Admin (
    numAdmin INT PRIMARY KEY,
    dateDebut DATE NOT NULL,
    FOREIGN KEY (numAdmin) REFERENCES Utilisateur(numUtilisateur)
);

CREATE TABLE Evaluateur (
    numEvaluateur INT PRIMARY KEY,
    specialite VARCHAR(50) NOT NULL,
    FOREIGN KEY (numEvaluateur) REFERENCES Utilisateur(numUtilisateur)
);

CREATE TABLE Competiteur (
    numCompetiteur INT PRIMARY KEY,
    datePremiereParticipation DATE NOT NULL,
    FOREIGN KEY (numCompetiteur) REFERENCES Utilisateur(numUtilisateur)
);

CREATE TABLE Directeur (
    numDirecteur INT PRIMARY KEY,
    numClub INT NOT NULL,
    dateDebut DATE NOT NULL,
    FOREIGN KEY (numDirecteur) REFERENCES Utilisateur(numUtilisateur),
    FOREIGN KEY (numClub) REFERENCES Club (numClub)
);

CREATE TABLE Concours (
    numConcours INT PRIMARY KEY,
    numPresident INT NOT NULL,
    theme VARCHAR(100) NOT NULL,
    dateDeb DATE NOT NULL,
    dateFin DATE NOT NULL,
    etat ENUM('pas commence', 'en cours', 'attente', 'resultat', 'evalue') NOT NULL,
    nbClub INT NOT NULL CHECK (nbClub >= 6),
    nbParticipant INT,
    descriptif TEXT NOT NULL,
    saison ENUM('printemps', 'ete', 'automne', 'hiver') NOT NULL,
    annee INT NOT NULL,
    FOREIGN KEY (numPresident) REFERENCES President (numPresident),
    UNIQUE(saison, annee),
    CHECK (dateFin > dateDeb)
);

CREATE TABLE ClubParticipe (
    numConcours INT NOT NULL,
    numClub INT NOT NULL,
    PRIMARY KEY (numConcours, numClub),
    FOREIGN KEY (numConcours) REFERENCES Concours (numConcours),
    FOREIGN KEY (numClub) REFERENCES Club (numClub)
);

CREATE TABLE CompetiteurParticipe (
    numConcours INT NOT NULL,
    numCompetiteur INT NOT NULL,
    PRIMARY KEY (numConcours, numCompetiteur),
    FOREIGN KEY (numConcours) REFERENCES Concours (numConcours),
    FOREIGN KEY (numCompetiteur) REFERENCES Competiteur (numCompetiteur)
);

CREATE TABLE Dessin (
    numDessin INT PRIMARY KEY AUTO_INCREMENT,
    numCompetiteur INT NOT NULL,
    numConcours INT NOT NULL,
    classement INT,
    commentaire TEXT,
    dateRemise DATE NOT NULL,
    leDessin VARCHAR(255) NOT NULL,
    FOREIGN KEY (numCompetiteur) REFERENCES Competiteur (numCompetiteur),
    FOREIGN KEY (numConcours) REFERENCES Concours (numConcours)
);

CREATE TABLE Jury (
    numEvaluateur INT NOT NULL,
    numConcours INT NOT NULL,
    PRIMARY KEY (numEvaluateur, numConcours),
    FOREIGN KEY (numEvaluateur) REFERENCES Evaluateur (numEvaluateur),
    FOREIGN KEY (numConcours) REFERENCES Concours (numConcours)
);

CREATE TABLE Evaluation (
    numDessin INT NOT NULL,
    numEvaluateur INT NOT NULL,
    dateEvaluation DATE NOT NULL,
    note INT NOT NULL CHECK (note >= 0 AND note <= 20),
    commentaire TEXT,
    PRIMARY KEY (numDessin, numEvaluateur),
    FOREIGN KEY (numDessin) REFERENCES Dessin (numDessin),
    FOREIGN KEY (numEvaluateur) REFERENCES Evaluateur (numEvaluateur)
);

DELIMITER //

CREATE TRIGGER check_max_dessins
BEFORE INSERT ON Dessin
FOR EACH ROW
BEGIN
    DECLARE nb_dessins INT;
    SELECT COUNT(*) INTO nb_dessins
    FROM Dessin
    WHERE numCompetiteur = NEW.numCompetiteur AND numConcours = NEW.numConcours;
    IF nb_dessins >= 3 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Maximum 3 dessins par compétiteur par concours';
    END IF;
END//

CREATE TRIGGER check_max_evaluations
BEFORE INSERT ON Evaluation
FOR EACH ROW
BEGIN
    DECLARE nb_evaluations INT;
    SELECT COUNT(*) INTO nb_evaluations
    FROM Evaluation e
    JOIN Dessin d ON e.numDessin = d.numDessin
    WHERE e.numEvaluateur = NEW.numEvaluateur
    AND d.numConcours = (SELECT numConcours FROM Dessin WHERE numDessin = NEW.numDessin);
    IF nb_evaluations >= 8 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Maximum 8 évaluations par évaluateur par concours';
    END IF;
END//

CREATE TRIGGER check_double_evaluation
AFTER INSERT ON Evaluation
FOR EACH ROW
BEGIN
    DECLARE nb_evaluations INT;
    SELECT COUNT(*) INTO nb_evaluations
    FROM Evaluation
    WHERE numDessin = NEW.numDessin;
    IF nb_evaluations > 2 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Un dessin ne peut avoir que deux évaluations';
    END IF;
END//

CREATE TRIGGER check_president_role
BEFORE INSERT ON CompetiteurParticipe
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM Concours
        WHERE numConcours = NEW.numConcours
        AND numPresident = NEW.numCompetiteur
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Un président ne peut pas être compétiteur dans son concours';
    END IF;
END//

CREATE TRIGGER check_date_remise
BEFORE INSERT ON Dessin
FOR EACH ROW
BEGIN
    DECLARE date_debut DATE;
    DECLARE date_fin DATE;
    SELECT dateDeb, dateFin INTO date_debut, date_fin
    FROM Concours
    WHERE numConcours = NEW.numConcours;
    IF NEW.dateRemise < date_debut OR NEW.dateRemise > date_fin THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La date de remise doit être comprise entre la date de début et de fin du concours';
    END IF;
END//

DELIMITER ;
