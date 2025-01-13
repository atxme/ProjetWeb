-- Insertion des UTILISATEURS
INSERT INTO UTILISATEUR (nom, prenom, adresse, login, password, type_utilisateur) VALUES
-- Administrateur
('Admin', 'System', '123 Rue Admin', 'admin', 'hash_password', 'ADMIN'),
-- Présidents (8 présidents pour 8 concours)
('Dubois', 'Pierre', '1 Rue Paris', 'pdubois', 'hash_password', 'PRESIDENT'),
('Martin', 'Marie', '2 Rue Lyon', 'mmartin', 'hash_password', 'PRESIDENT'),
-- 12 Directeurs de clubs
('Durand', 'Jean', '3 Rue Nantes', 'jdurand', 'hash_password', 'DIRECTEUR'),
('Petit', 'Sophie', '4 Rue Lille', 'spetit', 'hash_password', 'DIRECTEUR'),
-- 48 Évaluateurs (minimum 3 par club × 12 clubs)
('Leroy', 'Paul', '5 Rue Nice', 'pleroy', 'hash_password', 'EVALUATEUR'),
('Moreau', 'Claire', '6 Rue Bordeaux', 'cmoreau', 'hash_password', 'EVALUATEUR'),
-- 72 Compétiteurs (minimum 6 par club × 12 clubs)
('Bernard', 'Lucas', '7 Rue Marseille', 'lbernard', 'hash_password', 'COMPETITEUR'),
('Thomas', 'Emma', '8 Rue Toulouse', 'ethomas', 'hash_password', 'COMPETITEUR');

-- Insertion des CLUBS (12 clubs minimum)
INSERT INTO CLUB (nom, adresse, telephone, nb_adherents, ville, departement, region, id_directeur) VALUES
('Club Arts Paris', '10 Rue Arts', '0123456789', 50, 'Paris', 'Paris', 'Ile-de-France', 3),
('Club Dessin Lyon', '20 Rue Dessin', '0234567890', 45, 'Lyon', 'Rhône', 'Rhône-Alpes', 4);

-- Insertion des CONCOURS (8 concours sur 2023-2024)
INSERT INTO CONCOURS (theme, descriptif, date_debut, date_fin, etat, id_president) VALUES
-- 2023
('Printemps', 'Concours Printemps 2023', '2023-03-21', '2023-06-20', 'EVALUE', 1),
('Été', 'Concours Été 2023', '2023-06-21', '2023-09-20', 'EVALUE', 2),
('Automne', 'Concours Automne 2023', '2023-09-21', '2023-12-20', 'EVALUE', 1),
('Hiver', 'Concours Hiver 2023', '2023-12-21', '2024-03-19', 'EVALUE', 2),
-- 2024
('Printemps', 'Concours Printemps 2024', '2024-03-21', '2024-06-20', 'EN_ATTENTE', 1),
('Été', 'Concours Été 2024', '2024-06-21', '2024-09-20', 'EN_COURS', 2),
('Automne', 'Concours Automne 2024', '2024-09-21', '2024-12-20', 'NON_COMMENCE', 1),
('Hiver', 'Concours Hiver 2024', '2024-12-21', '2025-03-19', 'NON_COMMENCE', 2);

-- Insertion des DESSINS (max 3 par compétiteur par concours)
INSERT INTO DESSIN (commentaire, date_remise, dessin_svg, id_competiteur, id_concours) VALUES
('Nature morte', '2023-04-15', 'dessin1.svg', 7, 1),
('Paysage urbain', '2023-04-20', 'dessin2.svg', 8, 1);

-- Insertion des EVALUATIONS (2 évaluateurs par dessin)
INSERT INTO EVALUATION (commentaire, note, date_evaluation, id_dessin, id_evaluateur) VALUES
('Très bonne composition', 18.5, '2023-06-25', 1, 5),
('Bonne technique', 17.0, '2023-06-25', 1, 6);

-- Insertion des spécialisations
INSERT INTO PRESIDENT (id_president, prime) VALUES
(1, 1000.00),
(2, 1000.00);

INSERT INTO EVALUATEUR (id_evaluateur, specialite) VALUES
(5, 'Peinture à l''huile'),
(6, 'Aquarelle');

INSERT INTO COMPETITEUR (id_competiteur, date_premiere_participation) VALUES
(7, '2023-03-21'),
(8, '2023-03-21');

-- Insertion des participations CLUB_CONCOURS (minimum 6 clubs par concours)
INSERT INTO CLUB_CONCOURS (id_club, id_concours) VALUES
(1, 1), (2, 1);
