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

-- Insertion des Utilisateurs
INSERT INTO Utilisateur (numUtilisateur, numClub, nom, prenom, age, adresse, login, mdp) VALUES
(1, 1, 'Admin', 'System', 35, '123 Rue Admin', 'admin', 'pwd1231'),
(2, 2, 'Dubois', 'Pierre', 42, '1 Rue Paris', 'pdubois', 'pwd124'),
(3, 3, 'Martin', 'Marie', 38, '2 Rue Lyon', 'mmartin', 'pwd125'),
(10, 1, 'Dupont', 'Jean', 45, '10 Rue Paris', 'jdupont', 'pwd126'),
(11, 2, 'Durand', 'Michel', 50, '11 Rue Lyon', 'mdurand', 'pwd127'),
(12, 3, 'Lefevre', 'Paul', 39, '12 Rue Marseille', 'plefevre', 'pwd128'),
(13, 4, 'Moreau', 'Anne', 41, '13 Rue Nice', 'amoreau', 'pwd129'),
(14, 5, 'Roux', 'Catherine', 36, '14 Rue Bordeaux', 'croux', 'pwd130'),
(15, 6, 'Simon', 'Claude', 48, '15 Rue Nantes', 'csimon', 'pwd131'),
(20, 1, 'Leroy', 'Paul', 33, '15 Rue Nice', 'pleroy1', 'pwd140'),
(21, 1, 'Moreau', 'Claire', 29, '16 Rue Bordeaux', 'cmoreau1', 'pwd141'),
(100, 1, 'Bernard', 'Lucas', 25, '30 Rue Paris', 'lbernard1', 'pwd160'),
(101, 1, 'Thomas', 'Emma', 27, '31 Rue Lyon', 'ethomas1', 'pwd161'),
(102, 2, 'Petit', 'Sophie', 31, '32 Rue Marseille', 'spetit', 'pwd162'),
(103, 2, 'Robert', 'Marc', 44, '33 Rue Nice', 'mrobert', 'pwd163'),
(104, 3, 'Richard', 'Julie', 37, '34 Rue Bordeaux', 'jrichard', 'pwd164'),
(105, 3, 'Laurent', 'Thomas', 46, '35 Rue Nantes', 'tlaurent', 'pwd165'),
(106, 4, 'Garcia', 'Laura', 28, '36 Rue Paris', 'lgarcia', 'pwd166'),
(107, 4, 'Michel', 'Antoine', 34, '37 Rue Lyon', 'amichel', 'pwd167'),
(108, 5, 'David', 'Sarah', 39, '38 Rue Marseille', 'sdavid', 'pwd168'),
(109, 5, 'Bertrand', 'Nicolas', 43, '39 Rue Nice', 'nbertrand', 'pwd169'),
(110, 6, 'Roux', 'Isabelle', 32, '40 Rue Bordeaux', 'iroux', 'pwd170'),
(111, 6, 'Vincent', 'Philippe', 47, '41 Rue Nantes', 'pvincent', 'pwd171'),
(112, 1, 'Fournier', 'Alice', 30, '42 Rue Paris', 'afournier', 'pwd172'),
(113, 2, 'Morel', 'Eric', 35, '43 Rue Lyon', 'emorel', 'pwd173'),
(114, 3, 'Andre', 'Christine', 41, '44 Rue Marseille', 'candre', 'pwd174'),
(115, 4, 'Lefevre', 'Daniel', 38, '45 Rue Nice', 'dlefevre', 'pwd175'),
(116, 5, 'Mercier', 'Nathalie', 33, '46 Rue Bordeaux', 'nmercier', 'pwd176'),
(117, 6, 'Blanc', 'Stephane', 45, '47 Rue Nantes', 'sblanc', 'pwd177'),
(118, 1, 'Guerin', 'Caroline', 36, '48 Rue Paris', 'cguerin', 'pwd178'),
(119, 2, 'Boyer', 'Laurent', 40, '49 Rue Lyon', 'lboyer', 'pwd179'),
(120, 3, 'Garnier', 'Valerie', 42, '50 Rue Marseille', 'vgarnier', 'pwd180'),
(121, 4, 'Chevalier', 'Pascal', 37, '51 Rue Nice', 'pchevalier', 'pwd181'),
(122, 5, 'Francois', 'Sylvie', 44, '52 Rue Bordeaux', 'sfrancois', 'pwd182'),
(123, 6, 'Legrand', 'Jerome', 31, '53 Rue Nantes', 'jlegrand', 'pwd183'),
(124, 1, 'Rousseau', 'Sandrine', 39, '54 Rue Paris', 'srousseau', 'pwd184'),
(125, 2, 'Gauthier', 'Frederic', 43, '55 Rue Lyon', 'fgauthier', 'pwd185'),
(126, 3, 'Lopez', 'Celine', 34, '56 Rue Marseille', 'clopez', 'pwd186');

-- Insertion Admin
INSERT INTO Admin (numAdmin, dateDebut) VALUES
(1, '2023-01-01'),
(102, '2023-01-01'),
(103, '2023-01-01');

-- Insertion Présidents
INSERT INTO President (numPresident, dateDebut, prime) VALUES
(2, '2023-01-01', 1000.00),
(3, '2023-01-01', 1200.00),
(104, '2023-01-01', 1100.00),
(105, '2023-01-01', 1150.00),
(106, '2023-01-01', 1050.00),
(107, '2023-01-01', 1250.00);

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


-- Insertion des Evaluateurs
INSERT INTO Evaluateur (numEvaluateur, specialite) VALUES
(20, 'Peinture à l''huile'),
(21, 'Aquarelle'),
(22, 'Dessin au crayon'),
(23, 'Art numérique'),
(24, 'Pastel'),
(25, 'Acrylique'),
(26, 'Encre de Chine'),
(27, 'Fusain'),
(28, 'Techniques mixtes'),
(29, 'Art abstrait');

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

-- Insertion des participations des clubs aux concours
INSERT INTO ClubParticipe (numConcours, numClub) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6),
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6),
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6),
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 6),
(5, 1), (5, 2), (5, 3), (5, 4), (5, 5), (5, 6),
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6);

-- Insertion des participations des compétiteurs aux concours
INSERT INTO CompetiteurParticipe (numConcours, numCompetiteur) VALUES
(1, 100), (1, 101), (1, 114), (1, 115), (1, 116),
(2, 117), (2, 118), (2, 119), (2, 120), (2, 121),
(3, 122), (3, 123), (3, 124), (3, 125), (3, 126),
(4, 100), (4, 114), (4, 118), (4, 122), (4, 125),
(5, 101), (5, 115), (5, 119), (5, 123), (5, 126),
(6, 116), (6, 117), (6, 120), (6, 121), (6, 124);

-- Insertion des jurys
INSERT INTO Jury (numEvaluateur, numConcours) VALUES
(20, 1), (21, 1), (22, 1), (23, 1),
(24, 2), (25, 2), (26, 2), (27, 2),
(28, 3), (29, 3), (20, 3), (21, 3),
(22, 4), (23, 4), (24, 4), (25, 4),
(26, 5), (27, 5), (28, 5), (29, 5),
(20, 6), (21, 6), (22, 6), (23, 6);

-- Insertion des Dessins
INSERT INTO Dessin (numDessin, numCompetiteur, numConcours, classement, commentaire, dateRemise, leDessin) VALUES
(1, 100, 1, 1, 'Belle composition', '2024-10-15', 'dessin1.svg'),
(2, 100, 1, 2, 'Technique innovante', '2024-10-20', 'dessin2.svg'),
(3, 101, 1, 3, 'Créativité remarquable', '2024-10-18', 'dessin3.svg'),
(4, 114, 1, 4, 'Bonne utilisation des couleurs', '2024-10-19', 'dessin4.svg'),
(5, 115, 1, 5, 'Expression artistique unique', '2024-10-21', 'dessin5.svg'),
(6, 116, 2, 1, 'Maîtrise technique exceptionnelle', '2024-11-15', 'dessin6.svg'),
(7, 117, 2, 2, 'Perspective bien gérée', '2024-11-16', 'dessin7.svg'),
(8, 118, 2, 3, 'Originalité dans l''approche', '2024-11-17', 'dessin8.svg'),
(9, 119, 3, 1, 'Harmonie des couleurs', '2024-12-15', 'dessin9.svg'),
(10, 120, 3, 2, 'Détails soignés', '2024-12-16', 'dessin10.svg');

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
