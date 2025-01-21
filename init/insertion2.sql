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
(1, 'Club Arts Paris', '10 Rue des Arts', '0123456789', 50, 'Paris', '75', 'Ile-de-France'),
(2, 'Atelier Lyon', '20 Rue du Dessin', '0234567890', 45, 'Lyon', '69', 'Rhône-Alpes'),
(3, 'Studio Marseille', '30 Avenue des Peintres', '0345678901', 55, 'Marseille', '13', 'PACA'),
(4, 'Art Toulouse', '40 Boulevard des Arts', '0456789012', 40, 'Toulouse', '31', 'Occitanie'),
(5, 'Artistes de Bordeaux', '50 Rue des Beaux-Arts', '0567890123', 60, 'Bordeaux', '33', 'Nouvelle-Aquitaine'),
(6, 'Club Nantes Creation', '60 Avenue de l''Art', '0678901234', 48, 'Nantes', '44', 'Pays de la Loire'),
(7, 'Atelier Strasbourg', '70 Place des Artistes', '0789012345', 52, 'Strasbourg', '67', 'Grand Est');

-- 2. Insertion des Utilisateurs
INSERT INTO Utilisateur (numUtilisateur, numClub, nom, prenom, age, adresse, login, mdp) VALUES
-- Admin
(1, 1, 'Admin', 'System', 35, '123 Rue Admin', 'admin', 'pwd123'),
-- Présidents
(2, 1, 'Dubois', 'Pierre', 45, '1 Rue Paris', 'pdubois', 'pwd124'),
(3, 2, 'Martin', 'Marie', 42, '2 Rue Lyon', 'mmartin', 'pwd125'),
(4, 3, 'Petit', 'Jean', 48, '3 Rue Marseille', 'jpetit', 'pwd126'),
-- Evaluateurs
(20, 1, 'Leroy', 'Paul', 38, '15 Rue Nice', 'pleroy', 'pwd140'),
(21, 2, 'Moreau', 'Claire', 42, '16 Rue Bordeaux', 'cmoreau', 'pwd141'),
(22, 3, 'Dupont', 'Marc', 45, '17 Rue Lyon', 'mdupont', 'pwd142'),
-- Compétiteurs
(100, 1, 'Bernard', 'Lucas', 25, '30 Rue Paris', 'lbernard', 'pwd160'),
(101, 2, 'Thomas', 'Emma', 28, '31 Rue Lyon', 'ethomas', 'pwd161'),
(102, 3, 'Richard', 'Sophie', 30, '32 Rue Marseille', 'srichard', 'pwd162'),
-- Directeurs
(10, 1, 'Durand', 'Sophie', 40, '5 Rue Paris', 'sdurand', 'pwd130'),
(11, 2, 'Lambert', 'Michel', 45, '6 Rue Lyon', 'mlambert', 'pwd131'),
(12, 3, 'Garcia', 'Ana', 42, '7 Rue Marseille', 'agarcia', 'pwd132'),
-- Nouveaux présidents
(5, 4, 'Roux', 'Philippe', 50, '4 Rue Toulouse', 'proux', 'pwd127'),
(6, 5, 'Blanc', 'Catherine', 47, '8 Rue Bordeaux', 'cblanc', 'pwd128'),
-- Nouveaux évaluateurs
(23, 4, 'Simon', 'Julie', 39, '18 Rue Toulouse', 'jsimon', 'pwd143'),
(24, 5, 'Laurent', 'Thomas', 41, '19 Rue Bordeaux', 'tlaurent', 'pwd144'),
-- Nouveaux compétiteurs
(103, 4, 'Girard', 'Alice', 27, '33 Rue Toulouse', 'agirard', 'pwd163'),
(104, 5, 'Morel', 'Louis', 29, '34 Rue Bordeaux', 'lmorel', 'pwd164'),
(105, 6, 'Fournier', 'Marie', 31, '35 Rue Nantes', 'mfournier', 'pwd165');

-- 3. Insertion des rôles spécifiques
INSERT INTO Admin (numAdmin, dateDebut) VALUES (1, '2023-01-01');

INSERT INTO President (numPresident, dateDebut, prime) VALUES
(2, '2023-01-01', 1000.00),
(3, '2023-01-01', 1200.00),
(4, '2023-01-01', 1100.00),
(5, '2023-01-01', 1150.00),
(6, '2023-01-01', 1050.00);

INSERT INTO Evaluateur (numEvaluateur, specialite) VALUES
(20, 'Peinture à l''huile'),
(21, 'Aquarelle'),
(22, 'Dessin numérique'),
(23, 'Art contemporain'),
(24, 'Sculpture numérique');

INSERT INTO Competiteur (numCompetiteur, datePremiereParticipation) VALUES
(100, '2023-03-21'),
(101, '2023-03-21'),
(102, '2023-03-21'),
(103, '2023-03-21'),
(104, '2023-03-21'),
(105, '2024-03-21');

INSERT INTO Directeur (numDirecteur, numClub, dateDebut) VALUES
(10, 1, '2023-01-01'),
(11, 2, '2023-01-01'),
(12, 3, '2023-01-01');

-- 4. Insertion des Concours
INSERT INTO Concours (numConcours, numPresident, theme, dateDeb, dateFin, etat, nbClub, nbParticipant, descriptif, saison, annee) VALUES
-- 2023
(1, 2, 'Nature Morte', '2023-03-21', '2023-06-20', 'resultat', 6, 36, 'Concours Printemps 2023', 'printemps', 2023),
(2, 3, 'Paysages Urbains', '2023-06-21', '2023-09-20', 'resultat', 6, 36, 'Concours Été 2023', 'ete', 2023),
-- 2025
(3, 2, 'Art Moderne', '2025-03-21', '2025-06-20', 'pas commence', 6, 36, 'Concours Printemps 2025', 'printemps', 2025),
(4, 3, 'Portrait', '2025-06-21', '2025-09-20', 'pas commence', 6, 36, 'Concours Été 2025', 'ete', 2025),
-- 2026
(5, 4, 'Abstract', '2026-03-21', '2026-06-20', 'pas commence', 6, 36, 'Concours Printemps 2026', 'printemps', 2026),
(6, 2, 'Nature', '2026-06-21', '2026-09-20', 'pas commence', 6, 36, 'Concours Été 2026', 'ete', 2026),
-- 2024
(7, 5, 'Art Digital', '2024-03-21', '2024-06-20', 'en cours', 6, 36, 'Concours Printemps 2024', 'printemps', 2024),
(8, 6, 'Mythologie', '2024-06-21', '2024-09-20', 'pas commence', 6, 36, 'Concours Été 2024', 'ete', 2024);

-- 5. Insertion des participations
INSERT INTO ClubParticipe (numConcours, numClub) VALUES
(1, 1), (1, 2), (1, 3), (1, 4),
(2, 1), (2, 2), (2, 3), (2, 4),
(3, 1), (3, 2), (3, 3), (3, 4),
(4, 1), (4, 2), (4, 3), (4, 4),
(5, 1), (5, 2), (5, 3), (5, 4),
(6, 1), (6, 2), (6, 3), (6, 4),
(7, 1), (7, 2), (7, 3), (7, 4), (7, 5), (7, 6),
(8, 1), (8, 2), (8, 3), (8, 4), (8, 5), (8, 6);

INSERT INTO CompetiteurParticipe (numConcours, numCompetiteur) VALUES
(1, 100), (1, 101), (1, 102),
(2, 100), (2, 101), (2, 102),
(3, 100), (3, 101), (3, 102),
(4, 100), (4, 101), (4, 102),
(5, 100), (5, 101), (5, 102),
(6, 100), (6, 101), (6, 102),
(7, 100), (7, 101), (7, 102), (7, 103), (7, 104),
(8, 101), (8, 102), (8, 103), (8, 104), (8, 105);

-- 6. Insertion des Dessins (uniquement pour 2023 car terminé)
INSERT INTO Dessin (numDessin, numCompetiteur, numConcours, classement, commentaire, dateRemise, leDessin) VALUES
(1, 100, 1, 1, 'Excellente composition', '2023-04-15', 'dessin2023_1.jpg'),
(2, 101, 1, 2, 'Très bonne technique', '2023-04-20', 'dessin2023_2.jpg'),
(3, 100, 2, 1, 'Créativité remarquable', '2023-07-15', 'dessin2023_3.jpg'),
(4, 102, 2, 2, 'Belle utilisation des couleurs', '2023-07-20', 'dessin2023_4.jpg');

-- 7. Insertion des jurys
INSERT INTO Jury (numEvaluateur, numConcours) VALUES
(20, 1), (21, 1), (22, 1),
(20, 2), (21, 2), (22, 2),
(20, 3), (21, 3), (22, 3),
(20, 4), (21, 4), (22, 4),
(20, 5), (21, 5), (22, 5),
(20, 6), (21, 6), (22, 6),
(20, 7), (21, 7), (22, 7), (23, 7),
(21, 8), (22, 8), (23, 8), (24, 8);

-- 8. Insertion des évaluations (uniquement pour 2023 car terminé)
INSERT INTO Evaluation (numDessin, numEvaluateur, dateEvaluation, note, commentaire) VALUES
(1, 20, '2023-06-15', 18, 'Excellent travail'),
(1, 21, '2023-06-15', 17, 'Très bonne technique'),
(2, 20, '2023-06-16', 16, 'Bonne composition'),
(2, 21, '2023-06-16', 15, 'Peut être amélioré'),
(3, 20, '2023-09-15', 19, 'Exceptionnel'),
(3, 21, '2023-09-15', 18, 'Très créatif'),
(4, 20, '2023-09-16', 17, 'Belle réalisation'),
(4, 21, '2023-09-16', 16, 'Bon travail');
