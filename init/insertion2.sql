-- Nettoyage des tables

-- 1. Insertion des Clubs
INSERT INTO Club (numClub, nomClub, adresse, numTel, nbAdherents, ville, departement, region) VALUES
(1001, 'Club Arts Paris', '10 Rue des Arts', '0123456789', 50, 'Paris', '75', 'Ile-de-France'),
(1002, 'Atelier Lyon', '20 Rue du Dessin', '0234567890', 45, 'Lyon', '69', 'Rhône-Alpes'),
(1003, 'Studio Marseille', '30 Avenue des Peintres', '0345678901', 55, 'Marseille', '13', 'PACA'),
(1004, 'Art Toulouse', '40 Boulevard des Arts', '0456789012', 40, 'Toulouse', '31', 'Occitanie'),
(1005, 'Artistes de Bordeaux', '50 Rue des Beaux-Arts', '0567890123', 60, 'Bordeaux', '33', 'Nouvelle-Aquitaine'),
(1006, 'Club Nantes Creation', '60 Avenue de l''Art', '0678901234', 48, 'Nantes', '44', 'Pays de la Loire'),
(1007, 'Atelier Strasbourg', '70 Place des Artistes', '0789012345', 52, 'Strasbourg', '67', 'Grand Est');

-- 2. Insertion des Utilisateurs
INSERT INTO Utilisateur (numUtilisateur, numClub, nom, prenom, age, adresse, login, mdp) VALUES
-- Admin
(1001, 1001, 'Admin', 'System', 35, '123 Rue Admin', 'admin2', 'pwd123'),
-- Présidents
(1002, 1001, 'Dubois', 'Pierre', 45, '1 Rue Paris', 'pdubois2', 'pwd124'),
(1003, 1002, 'Martin', 'Marie', 42, '2 Rue Lyon', 'mmartin2', 'pwd125'),
(1004, 1003, 'Petit', 'Jean', 48, '3 Rue Marseille', 'jpetit2', 'pwd126'),
-- Evaluateurs
(1020, 1001, 'Leroy', 'Paul', 38, '15 Rue Nice', 'pleroy2', 'pwd140'),
(1021, 1002, 'Moreau', 'Claire', 42, '16 Rue Bordeaux', 'cmoreau2', 'pwd141'),
(1022, 1003, 'Dupont', 'Marc', 45, '17 Rue Lyon', 'mdupont2', 'pwd142'),
-- Compétiteurs
(1100, 1001, 'Bernard', 'Lucas', 25, '30 Rue Paris', 'lbernard2', 'pwd160'),
(1101, 1002, 'Thomas', 'Emma', 28, '31 Rue Lyon', 'ethomas2', 'pwd161'),
(1102, 1003, 'Richard', 'Sophie', 30, '32 Rue Marseille', 'srichard2', 'pwd162'),
-- Directeurs
(1010, 1001, 'Durand', 'Sophie', 40, '5 Rue Paris', 'sdurand2', 'pwd130'),
(1011, 1002, 'Lambert', 'Michel', 45, '6 Rue Lyon', 'mlambert2', 'pwd131'),
(1012, 1003, 'Garcia', 'Ana', 42, '7 Rue Marseille', 'agarcia2', 'pwd132'),
-- Nouveaux présidents
(1005, 1004, 'Roux', 'Philippe', 50, '4 Rue Toulouse', 'proux2', 'pwd127'),
(1006, 1005, 'Blanc', 'Catherine', 47, '8 Rue Bordeaux', 'cblanc2', 'pwd128'),
-- Nouveaux évaluateurs
(1023, 1004, 'Simon', 'Julie', 39, '18 Rue Toulouse', 'jsimon2', 'pwd143'),
(1024, 1005, 'Laurent', 'Thomas', 41, '19 Rue Bordeaux', 'tlaurent2', 'pwd144'),
-- Nouveaux compétiteurs
(1103, 1004, 'Girard', 'Alice', 27, '33 Rue Toulouse', 'agirard2', 'pwd163'),
(1104, 1005, 'Morel', 'Louis', 29, '34 Rue Bordeaux', 'lmorel2', 'pwd164'),
(1105, 1006, 'Fournier', 'Marie', 31, '35 Rue Nantes', 'mfournier2', 'pwd165');

-- 3. Insertion des rôles spécifiques
INSERT INTO Admin (numAdmin, dateDebut) VALUES (1001, '2023-01-01');

INSERT INTO President (numPresident, dateDebut, prime) VALUES
(1002, '2023-01-01', 1000.00),
(1003, '2023-01-01', 1200.00),
(1004, '2023-01-01', 1100.00),
(1005, '2023-01-01', 1150.00),
(1006, '2023-01-01', 1050.00);

INSERT INTO Evaluateur (numEvaluateur, specialite) VALUES
(1020, 'Peinture à l''huile'),
(1021, 'Aquarelle'),
(1022, 'Dessin numérique'),
(1023, 'Art contemporain'),
(1024, 'Sculpture numérique');

INSERT INTO Competiteur (numCompetiteur, datePremiereParticipation) VALUES
(1100, '2023-03-21'),
(1101, '2023-03-21'),
(1102, '2023-03-21'),
(1103, '2023-03-21'),
(1104, '2023-03-21'),
(1105, '2024-03-21'),
(20, '2024-09-21');

INSERT INTO Directeur (numDirecteur, numClub, dateDebut) VALUES
(1010, 1001, '2023-01-01'),
(1011, 1002, '2023-01-01'),
(1012, 1003, '2023-01-01');

-- 4. Insertion des Concours
INSERT INTO Concours (numConcours, numPresident, theme, dateDeb, dateFin, etat, nbClub, nbParticipant, descriptif, saison, annee) VALUES
-- 2023
(1001, 1002, 'Nature Morte', '2023-03-21', '2023-06-20', 'resultat', 6, 36, 'Concours Printemps 2023 - Nature Morte', 'printemps', 2023),
(1002, 1003, 'Paysages Urbains', '2023-06-21', '2023-09-20', 'resultat', 6, 36, 'Concours Été 2023 - Paysages', 'ete', 2023),
-- 2025
(1003, 1002, 'Art Moderne', '2025-03-21', '2025-06-20', 'pas commence', 6, 36, 'Concours Printemps 2025 - Art Moderne', 'printemps', 2025),
(1004, 1003, 'Portrait', '2025-06-21', '2025-09-20', 'pas commence', 6, 36, 'Concours Été 2025 - Portrait', 'ete', 2025),
-- 2026
(1005, 1004, 'Abstract', '2026-03-21', '2026-06-20', 'pas commence', 6, 36, 'Concours Printemps 2026 - Abstract', 'printemps', 2026),
(1006, 1002, 'Nature', '2026-06-21', '2026-09-20', 'pas commence', 6, 36, 'Concours Été 2026 - Nature', 'ete', 2026),
-- 2024
(1007, 1005, 'Art Digital', '2024-03-21', '2024-06-20', 'en cours', 6, 36, 'Concours Printemps 2024 - Digital', 'hiver', 2024),
(1008, 1006, 'Mythologie', '2024-06-21', '2024-09-20', 'pas commence', 6, 36, 'Concours Été 2024 - Mythologie', 'automne', 2024);
-- New Concours for January 2025
-- (1009, 1002, 'Winter Wonderland', '2025-01-01', '2025-01-31', 'pas commence', 6, 36, 'Concours Hiver 2025 - Winter Wonderland', 'hiver', 2025);

-- 5. Insertion des participations
INSERT INTO ClubParticipe (numConcours, numClub) VALUES
(1001, 1001), (1001, 1002), (1001, 1003), (1001, 1004),
(1002, 1001), (1002, 1002), (1002, 1003), (1002, 1004),
(1003, 1001), (1003, 1002), (1003, 1003), (1003, 1004),
(1004, 1001), (1004, 1002), (1004, 1003), (1004, 1004),
(1005, 1001), (1005, 1002), (1005, 1003), (1005, 1004),
(1006, 1001), (1006, 1002), (1006, 1003), (1006, 1004),
(1007, 1001), (1007, 1002), (1007, 1003), (1007, 1004), (1007, 1005), (1007, 1006),
(1008, 1001), (1008, 1002), (1008, 1003), (1008, 1004), (1008, 1005), (1008, 1006);
-- (1009, 1001), (1009, 1002), (1009, 1003), (1009, 1004);
INSERT INTO CompetiteurParticipe (numConcours, numCompetiteur) VALUES
(1001, 1100), (1001, 1101), (1001, 1102),
(1002, 1100), (1002, 1101), (1002, 1102),
(1003, 1100), (1003, 1101), (1003, 1102),
(1004, 1100), (1004, 1101), (1004, 1102),
(1005, 1100), (1005, 1101), (1005, 1102),
(1006, 1100), (1006, 1101), (1006, 1102),
(1007, 1100), (1007, 1101), (1007, 1102), (1007, 1103), (1007, 1104),
(1008, 1101), (1008, 1102), (1008, 1103), (1008, 1104), (1008, 1105);
-- (1009, 1100); -- Ensure Bernard Lucas is a competitor in the new concours

-- 6. Insertion des Dessins (uniquement pour 2023 car terminé)
INSERT INTO Dessin (numDessin, numCompetiteur, numConcours, classement, commentaire, dateRemise, leDessin) VALUES
(1001, 1100, 1001, 1, 'Excellente composition', '2023-04-15', 'dessin2023_1.jpg'),
(1002, 1101, 1001, 2, 'Très bonne technique', '2023-04-20', 'dessin2023_2.jpg'),
(1003, 1100, 1002, 1, 'Créativité remarquable', '2023-07-15', 'dessin2023_3.jpg'),
(1004, 1102, 1002, 2, 'Belle utilisation des couleurs', '2023-07-20', 'dessin2023_4.jpg');

-- 7. Insertion des jurys
INSERT INTO Jury (numEvaluateur, numConcours) VALUES
(1020, 1001), (1021, 1001), (1022, 1001),
(1020, 1002), (1021, 1002), (1022, 1002),
(1020, 1003), (1021, 1003), (1022, 1003),
(1020, 1004), (1021, 1004), (1022, 1004),
(1020, 1005), (1021, 1005), (1022, 1005),
(1020, 1006), (1021, 1006), (1022, 1006),
(1020, 1007), (1021, 1007), (1022, 1007), (1023, 1007),
(1021, 1008), (1022, 1008), (1023, 1008), (1024, 1008);

-- 8. Insertion des évaluations (uniquement pour 2023 car terminé)
INSERT INTO Evaluation (numDessin, numEvaluateur, dateEvaluation, note, commentaire) VALUES
(1001, 1020, '2023-06-15', 18, 'Excellent travail'),
(1001, 1021, '2023-06-15', 17, 'Très bonne technique'),
(1002, 1020, '2023-06-16', 16, 'Bonne composition'),
(1002, 1021, '2023-06-16', 15, 'Peut être amélioré'),
(1003, 1020, '2023-09-15', 19, 'Exceptionnel'),
(1003, 1021, '2023-09-15', 18, 'Très créatif'),
(1004, 1020, '2023-09-16', 17, 'Belle réalisation'),
(1004, 1021, '2023-09-16', 16, 'Bon travail');
