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
INSERT INTO Club VALUES
(1, 'Club Arts Paris', '10 Rue des Arts, Paris', '0123456789', 50, 'Paris', '75', 'Ile-de-France'),
(2, 'Atelier Lyon', '20 Rue du Dessin', '0234567890', 45, 'Lyon', '69', 'Rhône-Alpes'),
(3, 'Studio Marseille', '30 Avenue des Peintres', '0345678901', 55, 'Marseille', '13', 'PACA'),
(4, 'Galerie Lille', '40 Rue des Artistes', '0456789012', 40, 'Lille', '59', 'Hauts-de-France'),
(5, 'Atelier Nantes', '50 Boulevard des Arts', '0567890123', 48, 'Nantes', '44', 'Pays de la Loire'),
(6, 'Club Bordeaux', '60 Rue de la Création', '0678901234', 52, 'Bordeaux', '33', 'Nouvelle-Aquitaine'),
(7, 'Studio Toulouse', '70 Avenue des Arts', '0789012345', 47, 'Toulouse', '31', 'Occitanie'),
(8, 'Galerie Nice', '80 Rue des Peintres', '0890123456', 43, 'Nice', '06', 'PACA'),
(9, 'Atelier Strasbourg', '90 Place de l''Art', '0901234567', 51, 'Strasbourg', '67', 'Grand Est'),
(10, 'Club Montpellier', '100 Rue du Design', '0012345678', 46, 'Montpellier', '34', 'Occitanie'),
(11, 'Studio Rennes', '110 Avenue des Créateurs', '0123456789', 49, 'Rennes', '35', 'Bretagne'),
(12, 'Galerie Dijon', '120 Boulevard Artistique', '0234567890', 44, 'Dijon', '21', 'Bourgogne');

-- 2. Insertion des rôles
-- Admin
INSERT INTO Admin VALUES (1, '2023-01-01');

-- Présidents
INSERT INTO President VALUES 
(2, '2023-01-01'),
(3, '2023-01-01');

-- Evaluateurs
INSERT INTO Evaluateur VALUES
(20, 'Peinture à l''huile'),
(21, 'Aquarelle'),
(22, 'Dessin numérique');

-- Compétiteurs
INSERT INTO Competiteur VALUES
(100, '2023-03-21', 1, 15.5),
(101, '2023-03-21', 2, 16.0),
(102, '2023-03-21', 3, 14.5),
(103, '2023-03-21', 4, 17.0),
(104, '2023-03-21', 5, 15.0),
(105, '2023-03-21', 6, 16.5);

-- 3. Insertion des Utilisateurs
-- Admin
INSERT INTO Utilisateur VALUES
(1, 1, 'Admin', 'System', 35, '123 Rue Admin', 'admin', 'pwd123');

-- Présidents
INSERT INTO Utilisateur VALUES
(2, 2, 'Dubois', 'Pierre', 45, '1 Rue Paris', 'pdubois', 'pwd124'),
(3, 3, 'Martin', 'Marie', 42, '2 Rue Lyon', 'mmartin', 'pwd125');

-- Directeurs
INSERT INTO Utilisateur VALUES
(4, 1, 'Dupont', 'Jean', 48, '3 Rue Paris', 'jdupont', 'pwd126'),
(5, 2, 'Durand', 'Marie', 52, '4 Rue Lyon', 'mdurand', 'pwd127'),
(6, 3, 'Lefevre', 'Paul', 45, '5 Rue Marseille', 'plefevre', 'pwd128'),
(7, 4, 'Moreau', 'Anne', 50, '6 Rue Lille', 'amoreau', 'pwd129'),
(8, 5, 'Roux', 'Pierre', 47, '7 Rue Nantes', 'proux', 'pwd130'),
(9, 6, 'Simon', 'Claire', 49, '8 Rue Bordeaux', 'csimon', 'pwd131'),
(10, 7, 'Michel', 'Luc', 51, '9 Rue Toulouse', 'lmichel', 'pwd132'),
(11, 8, 'Bertrand', 'Sophie', 46, '10 Rue Nice', 'sbertrand', 'pwd133'),
(12, 9, 'Petit', 'Marc', 53, '11 Rue Strasbourg', 'mpetit', 'pwd134'),
(13, 10, 'Laurent', 'Julie', 44, '12 Rue Montpellier', 'jlaurent', 'pwd135'),
(14, 11, 'Girard', 'Thomas', 48, '13 Rue Rennes', 'tgirard', 'pwd136'),
(15, 12, 'Morel', 'Alice', 50, '14 Rue Dijon', 'amorel', 'pwd137');

-- Evaluateurs
INSERT INTO Utilisateur VALUES
(20, 1, 'Leroy', 'Paul', 38, '15 Rue Nice', 'pleroy1', 'pwd140'),
(21, 1, 'Moreau', 'Claire', 42, '16 Rue Bordeaux', 'cmoreau1', 'pwd141'),
(22, 1, 'Dupont', 'Marc', 45, '17 Rue Lyon', 'mdupont1', 'pwd142');

-- Compétiteurs
INSERT INTO Utilisateur VALUES
(100, 1, 'Bernard', 'Lucas', 25, '30 Rue Paris', 'lbernard1', 'pwd160'),
(101, 1, 'Thomas', 'Emma', 28, '31 Rue Lyon', 'ethomas1', 'pwd161'),
(102, 1, 'Robert', 'Jules', 30, '32 Rue Marseille', 'jrobert1', 'pwd162'),
(103, 1, 'Michel', 'Laura', 27, '33 Rue Bordeaux', 'lmichel1', 'pwd163'),
(104, 1, 'Durand', 'Hugo', 32, '34 Rue Nice', 'hdurand1', 'pwd164'),
(105, 1, 'Lefebvre', 'Alice', 29, '35 Rue Lille', 'alefebvre1', 'pwd165');

-- 4. Insertion des Directeurs
INSERT INTO Directeur VALUES
(4, 1, '2023-01-01'),
(5, 2, '2023-01-01'),
(6, 3, '2023-01-01'),
(7, 4, '2023-01-01'),
(8, 5, '2023-01-01'),
(9, 6, '2023-01-01'),
(10, 7, '2023-01-01'),
(11, 8, '2023-01-01'),
(12, 9, '2023-01-01'),
(13, 10, '2023-01-01'),
(14, 11, '2023-01-01'),
(15, 12, '2023-01-01');

-- 5. Insertion des Concours
INSERT INTO Concours VALUES
(1, 2, 'Printemps 2023', '2023-03-21', '2023-06-20', 'evalue', 8, 48, 'Concours Printemps 2023'),
(2, 3, 'Été 2023', '2023-06-21', '2023-09-20', 'evalue', 8, 48, 'Concours Été 2023'),
(3, 2, 'Automne 2023', '2023-09-21', '2023-12-20', 'evalue', 8, 48, 'Concours Automne 2023'),
(4, 3, 'Hiver 2023', '2023-12-21', '2024-03-19', 'evalue', 8, 48, 'Concours Hiver 2023'),
(5, 2, 'Printemps 2024', '2024-03-21', '2024-06-20', 'en cours', 8, 48, 'Concours Printemps 2024'),
(6, 3, 'Été 2024', '2024-06-21', '2024-09-20', 'attente', 8, 48, 'Concours Été 2024'),
(7, 2, 'Automne 2024', '2024-09-21', '2024-12-20', 'pas commence', 8, 48, 'Concours Automne 2024'),
(8, 3, 'Hiver 2024', '2024-12-21', '2025-03-19', 'pas commence', 8, 48, 'Concours Hiver 2024');

-- 6. Insertion des participations
-- ClubParticipe
INSERT INTO ClubParticipe VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8),
(2, 2), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7), (2, 8), (2, 9),
(3, 3), (3, 4), (3, 5), (3, 6), (3, 7), (3, 8), (3, 9), (3, 10),
(4, 4), (4, 5), (4, 6), (4, 7), (4, 8), (4, 9), (4, 10), (4, 11);

-- CompetiteurParticipe
INSERT INTO CompetiteurParticipe VALUES
(1, 100), (1, 101), (1, 102), (1, 103), (1, 104), (1, 105),
(2, 100), (2, 101), (2, 102);

-- 7. Insertion des Dessins
INSERT INTO Dessin VALUES
(1, 100, 1, 1, 'Belle composition', '2023-04-15', NULL),
(2, 100, 1, 2, 'Technique innovante', '2023-04-20', NULL),
(3, 101, 1, 3, 'Créativité remarquable', '2023-04-18', NULL),
(4, 102, 1, 4, 'Bonne utilisation des couleurs', '2023-04-19', NULL),
(5, 103, 1, 5, 'Expression intéressante', '2023-04-21', NULL);

-- 8. Insertion des jurys
INSERT INTO Jury VALUES
(20, 1), (21, 1), (22, 2),
(20, 2), (21, 3), (22, 3);

-- 9. Insertion des évaluations
INSERT INTO Evaluation VALUES
(1, 20, '2023-06-25', 18, 'Excellent travail'),
(1, 21, '2023-06-25', 17, 'Très bonne technique'),
(2, 20, '2023-06-26', 16, 'Bonne composition'),
(2, 21, '2023-06-26', 15, 'Peut être amélioré'),
(3, 22, '2023-06-27', 19, 'Exceptionnel'),
(3, 20, '2023-06-27', 18, 'Très créatif');
