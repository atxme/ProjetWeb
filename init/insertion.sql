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

-- 1 Insertion des Clubs
INSERT INTO Club (numClub, nomClub, adresse, numTel, nbAdherents, ville, departement, region) VALUES
(1, 'Club Arts Paris', '10 Rue des Arts, Paris', '0123456789', 50, 'Paris', '75', 'Ile-de-France'),
(2, 'Atelier Lyon', '20 Rue du Dessin', '0234567890', 45, 'Lyon', '69', 'Rhône-Alpes'),
(3, 'Studio Marseille', '30 Avenue des Peintres', '0345678901', 55, 'Marseille', '13', 'PACA'),
(4, 'Studio Nice', '40 Rue Art', '0456789012', 40, 'Nice', '06', 'PACA'),
(5, 'Atelier Bordeaux', '50 Rue Peinture', '0567890123', 35, 'Bordeaux', '33', 'Nouvelle-Aquitaine'),
(6, 'Club Nantes', '60 Rue Création', '0678901234', 45, 'Nantes', '44', 'Pays de la Loire'),
(7, 'Pixel Art Studio', '15 Rue Numérique', '0789012345', 60, 'Toulouse', '31', 'Occitanie'),
(8, 'Manga Workshop', '25 Avenue Japon', '0890123456', 70, 'Strasbourg', '67', 'Grand Est'),
(9, 'Street Art Lab', '35 Rue Graffiti', '0901234567', 40, 'Lille', '59', 'Hauts-de-France'),
(10, 'Aquarelle & Co', '45 Boulevard Pinceau', '0112233445', 30, 'Rennes', '35', 'Bretagne'),
(11, 'Digital Dreams', '55 Rue Virtuelle', '0223344556', 65, 'Montpellier', '34', 'Occitanie'),
(12, 'Sculpture Paradise', '65 Avenue Pierre', '0334455667', 40, 'Dijon', '21', 'Bourgogne-Franche-Comté'),
(13, 'Pop Art Factory', '75 Rue Warhol', '0445566778', 55, 'Grenoble', '38', 'Auvergne-Rhône-Alpes'),
(14, 'Zen Art Dojo', '85 Chemin Bambou', '0556677889', 35, 'Angers', '49', 'Pays de la Loire'),
(15, 'Steampunk Workshop', '95 Rue Engrenage', '0667788990', 45, 'Clermont-Ferrand', '63', 'Auvergne-Rhône-Alpes'),
(16, 'Art Fusion Lab', '105 Avenue Mélange', '0778899001', 50, 'Reims', '51', 'Grand Est'),
(17, 'Neon Dreams Studio', '115 Rue Lumière', '0889900112', 40, 'Le Havre', '76', 'Normandie'),
(18, 'Eco-Art Collective', '125 Rue Nature', '0990011223', 55, 'Perpignan', '66', 'Occitanie'),
(19, 'Virtual Reality Arts', '135 Boulevard Digital', '0100112234', 65, 'Metz', '57', 'Grand Est'),
(20, 'Renaissance Studio', '145 Rue Histoire', '0211223345', 45, 'Besançon', '25', 'Bourgogne-Franche-Comté'),
(21, 'Urban Sketch Lab', '155 Avenue Ville', '0322334456', 50, 'Orléans', '45', 'Centre-Val de Loire'),
(22, 'Cosmic Art Space', '165 Rue Galaxie', '0433445567', 40, 'Limoges', '87', 'Nouvelle-Aquitaine'),
(23, 'Retro Gaming Art', '175 Rue Pixel', '0544556678', 60, 'Amiens', '80', 'Hauts-de-France'),
(24, 'Bio Art Lab', '185 Avenue Nature', '0655667789', 35, 'Le Mans', '72', 'Pays de la Loire');

-- 2. Insertion des rôles
-- Admin
INSERT INTO Admin VALUES (1, '2023-01-01');

-- Insertion Présidents
INSERT INTO President (numPresident, dateDebut, prime) VALUES
(2, '2023-01-01', 1000.00),
(3, '2023-01-01', 1200.00),
(104, '2023-01-01', 1100.00),
(105, '2023-01-01', 1150.00),
(106, '2023-01-01', 1050.00),
(107, '2023-01-01', 1250.00);
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

-- Insertion Directeurs
INSERT INTO Directeur (numDirecteur, numClub, dateDebut) VALUES
(10, 1, '2023-01-01'),
(11, 2, '2023-01-01'),
(12, 3, '2023-01-01'),
(13, 4, '2023-01-01'),
(14, 5, '2023-01-01'),
(15, 6, '2023-01-01'),
(108, 1, '2023-01-01'),
(109, 2, '2023-01-01'), 
(110, 3, '2023-01-01'),
(111, 4, '2023-01-01'),
(112, 5, '2023-01-01'),
(113, 6, '2023-01-01');

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

-- Insertion des Concours
INSERT INTO Concours (numConcours, numPresident, theme, dateDeb, dateFin, etat, nbClub, nbParticipant, descriptif, saison, annee) VALUES
(1, 2, 'Printemps 2023', '2023-03-21', '2023-06-20', 'evalue', 6, 36, 'Concours Printemps 2023', 'printemps', 2023),
(2, 3, 'Été 2023', '2023-06-21', '2023-09-20', 'evalue', 6, 36, 'Concours Été 2023', 'ete', 2023),
(3, 104, 'Automne 2023', '2023-09-21', '2023-12-20', 'evalue', 6, 36, 'Concours Automne 2023', 'automne', 2023),
(4, 105, 'Hiver 2023', '2023-12-21', '2024-03-20', 'evalue', 6, 36, 'Concours Hiver 2023', 'hiver', 2023),
(5, 106, 'Printemps 2024', '2024-03-21', '2024-06-20', 'resultat', 6, 36, 'Concours Printemps 2024', 'printemps', 2024),
(6, 107, 'Été 2024', '2024-06-21', '2024-09-20', 'en cours', 6, 36, 'Concours Été 2024', 'ete', 2024),
(7, 2, 'Automne 2024', '2024-09-21', '2024-12-20', 'pas commence', 6, 36, 'Concours Automne 2024', 'automne', 2024),
(8, 3, 'Hiver 2024', '2024-12-21', '2025-03-20', 'pas commence', 6, 36, 'Concours Hiver 2024', 'hiver', 2024),
(9, 104, 'Printemps 2025', '2025-03-21', '2025-06-20', 'pas commence', 6, 36, 'Concours Printemps 2025', 'printemps', 2025),
(10, 105, 'Été 2025', '2025-06-21', '2025-09-20', 'pas commence', 6, 36, 'Concours Été 2025', 'ete', 2025),
(11, 106, 'Automne 2025', '2025-09-21', '2025-12-20', 'pas commence', 6, 36, 'Concours Automne 2025', 'automne', 2025),
(12, 107, 'Hiver 2025', '2025-12-21', '2026-03-20', 'pas commence', 6, 36, 'Concours Hiver 2025', 'hiver', 2025);
-- Evaluateurs
INSERT INTO Utilisateur VALUES
(20, 1, 'Leroy', 'Paul', 38, '15 Rue Nice', 'pleroy1', 'pwd140'),
(21, 1, 'Moreau', 'Claire', 42, '16 Rue Bordeaux', 'cmoreau1', 'pwd141'),
(22, 1, 'Dupont', 'Marc', 45, '17 Rue Lyon', 'mdupont1', 'pwd142');


-- Insertion des Evaluateurs
INSERT INTO Evaluateur (numEvaluateur, specialite) VALUES
(20, 'Peinture à l''huile'),
(21, 'Aquarelle'),


-- Insertion des Compétiteurs
INSERT INTO Competiteur (numCompetiteur, datePremiereParticipation) VALUES
(100, '2024-09-21'),
(101, '2024-09-21'),
(114, '2024-09-21'),
(115, '2024-09-21'),
(116, '2024-09-21'),
(117, '2024-09-21'),
(118, '2024-09-21'),
(119, '2024-09-21'),
(120, '2024-09-21'),
(121, '2024-09-21'),
(122, '2024-09-21'),
(123, '2024-09-21'),
(124, '2024-09-21'),
(125, '2024-09-21'),
(126, '2024-09-21');
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

-- Insertion des participations des clubs aux concours
INSERT INTO ClubParticipe (numConcours, numClub) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6),
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6),
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 6),
(5, 1), (5, 2), (5, 3), (5, 4), (5, 5), (5, 6),
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6);
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

-- Insertion des évaluations
INSERT INTO Evaluation (numDessin, numEvaluateur, dateEvaluation, note, commentaire) VALUES
(1, 20, '2024-12-25', 18, 'Excellent travail'),
(1, 21, '2024-12-25', 17, 'Très bonne technique'),
(2, 20, '2024-12-26', 16, 'Bonne composition'),
(2, 21, '2024-12-26', 15, 'Peut être amélioré'),
(3, 20, '2024-12-27', 16, 'Belle réalisation'),
(3, 21, '2024-12-27', 17, 'Technique maîtrisée'),
(4, 22, '2024-12-28', 19, 'Travail exceptionnel'),
(4, 23, '2024-12-28', 18, 'Grande maîtrise artistique'),
(5, 22, '2024-12-29', 15, 'Bon potentiel'),
(5, 23, '2024-12-29', 16, 'Créativité intéressante'),
(6, 24, '2024-12-30', 17, 'Expression unique'),
(6, 25, '2024-12-30', 18, 'Technique innovante'),
(7, 24, '2024-12-31', 16, 'Bonne progression'),
(7, 25, '2024-12-31', 15, 'Effort remarquable'),
(8, 26, '2025-01-01', 18, 'Excellent rendu'),
(8, 27, '2025-01-01', 17, 'Maîtrise technique');
-- 9. Insertion des évaluations
INSERT INTO Evaluation VALUES
(1, 20, '2023-06-25', 18, 'Excellent travail'),
(1, 21, '2023-06-25', 17, 'Très bonne technique'),
(2, 20, '2023-06-26', 16, 'Bonne composition'),
(2, 21, '2023-06-26', 15, 'Peut être amélioré'),
(3, 22, '2023-06-27', 19, 'Exceptionnel'),
(3, 20, '2023-06-27', 18, 'Très créatif');
