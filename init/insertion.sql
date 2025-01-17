-- Nettoyage des tables
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE Evaluation;
TRUNCATE TABLE Jury;
TRUNCATE TABLE Dessin;
TRUNCATE TABLE CompetiteurParticipe;
TRUNCATE TABLE ClubParticipe;
TRUNCATE TABLE Concours;
TRUNCATE TABLE Evaluateur;
TRUNCATE TABLE Competiteur;
TRUNCATE TABLE President;
TRUNCATE TABLE Admin;
TRUNCATE TABLE Directeur;
TRUNCATE TABLE Utilisateur;
TRUNCATE TABLE Club;
SET FOREIGN_KEY_CHECKS = 1;

-- Insertion des Clubs
INSERT INTO Club VALUES
(1, 'Club Arts Paris', '10 Rue des Arts, Paris', '0123456789', 50, 'Paris', '75', 'Ile-de-France'),
(2, 'Atelier Lyon', '20 Rue du Dessin', '0234567890', 45, 'Lyon', '69', 'Rhône-Alpes'),
(3, 'Studio Marseille', '30 Avenue des Peintres', '0345678901', 55, 'Marseille', '13', 'PACA'),
(4, 'Studio Nice', '40 Rue Art', '0456789012', 40, 'Nice', '06', 'PACA'),
(5, 'Atelier Bordeaux', '50 Rue Peinture', '0567890123', 35, 'Bordeaux', '33', 'Nouvelle-Aquitaine'),
(6, 'Club Nantes', '60 Rue Création', '0678901234', 45, 'Nantes', '44', 'Pays de la Loire');

-- Insertion des Utilisateurs avec leurs rôles
INSERT INTO Utilisateur VALUES
(1, 1, 'Admin', 'System', 35, '123 Rue Admin', 'admin', 'pwd123', 'admin'),
(2, 2, 'Dubois', 'Pierre', 45, '1 Rue Paris', 'pdubois', 'pwd124', 'president'),
(3, 3, 'Martin', 'Marie', 42, '2 Rue Lyon', 'mmartin', 'pwd125', 'president'),
(20, 1, 'Leroy', 'Paul', 38, '15 Rue Nice', 'pleroy1', 'pwd140', 'evaluateur'),
(21, 1, 'Moreau', 'Claire', 42, '16 Rue Bordeaux', 'cmoreau1', 'pwd141', 'evaluateur'),
(100, 1, 'Bernard', 'Lucas', 25, '30 Rue Paris', 'lbernard1', 'pwd160', 'competiteur'),
(101, 1, 'Thomas', 'Emma', 28, '31 Rue Lyon', 'ethomas1', 'pwd161', 'competiteur');

-- Insertion Admin
INSERT INTO Admin VALUES 
(1, '2023-01-01');

-- Insertion Présidents
INSERT INTO President VALUES
(2, '2023-01-01', 1000.00),
(3, '2023-01-01', 1200.00);

-- Insertion des Concours
INSERT INTO Concours VALUES
(1, 2, 'Automne 2024', '2024-09-21', '2024-12-20', 'evalue', 6, 36, 'Concours Automne 2024', 'automne', 2024),
(2, 3, 'Hiver 2024', '2024-12-21', '2025-01-15', 'resultat', 6, 36, 'Concours Hiver 2024', 'hiver', 2024);

-- Insertion des Evaluateurs
INSERT INTO Evaluateur VALUES
(20, 'Peinture à l''huile'),
(21, 'Aquarelle');

-- Insertion des Compétiteurs
INSERT INTO Competiteur VALUES
(100, '2024-09-21'),
(101, '2024-09-21');

-- Insertion des participations des clubs aux concours
INSERT INTO ClubParticipe VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6);

-- Insertion des Dessins
INSERT INTO Dessin VALUES
(1, 100, 1, 1, 'Belle composition', '2024-10-15', 'dessin1.svg'),
(2, 100, 1, 2, 'Technique innovante', '2024-10-20', 'dessin2.svg'),
(3, 101, 1, 3, 'Créativité remarquable', '2024-10-18', 'dessin3.svg');

-- Insertion des jurys
INSERT INTO Jury VALUES
(20, 1),
(21, 1);

-- Insertion des évaluations
INSERT INTO Evaluation VALUES
(1, 20, '2024-12-25', 18, 'Excellent travail'),
(1, 21, '2024-12-25', 17, 'Très bonne technique'),
(2, 20, '2024-12-26', 16, 'Bonne composition'),
(2, 21, '2024-12-26', 15, 'Peut être amélioré'),
(3, 20, '2024-12-27', 16, 'Belle réalisation'),
(3, 21, '2024-12-27', 17, 'Technique maîtrisée');

-- Insertion des participations des compétiteurs aux concours
INSERT INTO CompetiteurParticipe VALUES
(1, 100),
(1, 101);
