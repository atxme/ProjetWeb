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

-- 1. Insertion des Clubs
INSERT INTO Club (numClub, nomClub, adresse, numTel, nbAdherents, ville, departement, region) VALUES
(1, 'Club Arts Paris', '10 Rue des Arts, Paris', '0123456789', 50, 'Paris', '75', 'Ile-de-France'),
(2, 'Atelier Lyon', '20 Rue du Dessin', '0234567890', 45, 'Lyon', '69', 'Rhône-Alpes'),
(3, 'Studio Marseille', '30 Avenue des Peintres', '0345678901', 55, 'Marseille', '13', 'PACA');

-- 2. Insertion des Utilisateurs
INSERT INTO Utilisateur (numUtilisateur, numClub, nom, prenom, age, adresse, login, mdp) VALUES
-- Admin
(1, 1, 'Admin', 'System', 35, '123 Rue Admin', 'admin', 'pwd123'),
-- Présidents
(2, 1, 'Dubois', 'Pierre', 45, '1 Rue Paris', 'pdubois', 'pwd124'),
(3, 1, 'Martin', 'Marie', 42, '2 Rue Lyon', 'mmartin', 'pwd125'),
-- Evaluateurs
(20, 1, 'Leroy', 'Paul', 38, '15 Rue Nice', 'pleroy1', 'pwd140'),
(21, 1, 'Moreau', 'Claire', 42, '16 Rue Bordeaux', 'cmoreau1', 'pwd141'),
(22, 1, 'Dupont', 'Marc', 45, '17 Rue Lyon', 'mdupont1', 'pwd142'),
-- Compétiteurs
(100, 1, 'Bernard', 'Lucas', 25, '30 Rue Paris', 'lbernard1', 'pwd160'),
(101, 1, 'Thomas', 'Emma', 28, '31 Rue Lyon', 'ethomas1', 'pwd161');

-- 3. Insertion des rôles
-- Admin
INSERT INTO Admin (numAdmin, dateDebut) VALUES 
(1, '2023-01-01');

-- Présidents
INSERT INTO President (numPresident, dateDebut, prime) VALUES 
(2, '2023-01-01', 1000.00),
(3, '2023-01-01', 1200.00);

-- Evaluateurs
INSERT INTO Evaluateur (numEvaluateur, specialite) VALUES
(20, 'Peinture à l''huile'),
(21, 'Aquarelle'),
(22, 'Dessin numérique');

-- Compétiteurs
INSERT INTO Competiteur (numCompetiteur, datePremiereParticipation) VALUES
(100, '2024-09-21'),
(101, '2024-09-21');

-- 4. Insertion des Concours
INSERT INTO Concours (numConcours, numPresident, theme, dateDeb, dateFin, etat, nbClub, nbParticipant, descriptif, saison, annee) VALUES
(1, 2, 'Printemps 2024', '2024-03-21', '2024-06-20', 'resultat', 6, 36, 'Concours Printemps 2024', 'printemps', 2024),
(2, 3, 'Été 2024', '2024-06-21', '2024-09-20', 'en cours', 6, 36, 'Concours Été 2024', 'ete', 2024),
(3, 2, 'L''art et la culture, un chemin vers la paix', '2025-01-01', '2025-01-31', 'en cours', 6, 36, 'Concours international d''arts plastiques ouvert aux jeunes de 3 à 25 ans', 'hiver', 2025);

-- 5. Insertion des participations
-- ClubParticipe
INSERT INTO ClubParticipe (numConcours, numClub) VALUES
(1, 1), (1, 2), (1, 3),
(2, 1), (2, 2), (2, 3),
(3, 1), (3, 2), (3, 3);

-- CompetiteurParticipe
INSERT INTO CompetiteurParticipe (numConcours, numCompetiteur) VALUES
(1, 100), (1, 101),
(2, 100), (2, 101),
(3, 100), (3, 101);

-- 6. Insertion des Dessins
INSERT INTO Dessin (numDessin, numCompetiteur, numConcours, classement, commentaire, dateRemise, leDessin) VALUES
(1, 100, 1, 1, 'Belle composition', '2024-04-15', 'dessin1.jpg'),
(2, 101, 1, 2, 'Technique innovante', '2024-04-20', 'dessin2.jpg');

-- 7. Insertion des jurys
INSERT INTO Jury (numEvaluateur, numConcours) VALUES
(20, 1), (21, 1),
(20, 2), (21, 2),
(20, 3), (21, 3), (22, 3);

-- 8. Insertion des évaluations
INSERT INTO Evaluation (numDessin, numEvaluateur, dateEvaluation, note, commentaire) VALUES
(1, 20, '2024-12-25', 18, 'Excellent travail'),
(1, 21, '2024-12-25', 17, 'Très bonne technique'),
(2, 20, '2024-12-26', 16, 'Bonne composition'),
(2, 21, '2024-12-26', 15, 'Peut être amélioré');
