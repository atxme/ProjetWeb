------------------------------------------------------
--Requête 1 -Participants 2023
------------------------------------------------------
SELECT DISTINCT u.nom, u.prenom, u.adresse, 
       co.descriptif, co.date_debut, co.date_fin,
       cl.nom AS nom_club, cl.departement, cl.region
FROM UTILISATEUR u
JOIN COMPETITEUR comp ON u.id_utilisateur = comp.id_competiteur
JOIN DESSIN d ON comp.id_competiteur = d.id_competiteur
JOIN CONCOURS co ON d.id_concours = co.id_concours
JOIN CLUB cl ON cl.id_directeur = u.id_utilisateur
WHERE YEAR(co.date_debut) = 2023;

------------------------------------------------------
--Requête 2 -  Dessins Évalués 2022
------------------------------------------------------
SELECT d.id_dessin, e.note, u.nom AS nom_competiteur,
       c.descriptif, c.theme
FROM DESSIN d
JOIN EVALUATION e ON d.id_dessin = e.id_dessin
JOIN UTILISATEUR u ON d.id_competiteur = u.id_utilisateur
JOIN CONCOURS c ON d.id_concours = c.id_concours
WHERE YEAR(e.date_evaluation) = 2022
ORDER BY e.note ASC;

------------------------------------------------------
--Requête 3 - Informations Complètes des Dessins
------------------------------------------------------
SELECT c.id_concours, YEAR(c.date_debut) AS annee, c.descriptif,
       u1.nom AS nom_competiteur, 
       d.id_dessin, d.commentaire AS commentaire_dessin,
       e.note, e.commentaire AS commentaire_evaluation,
       u2.nom AS nom_evaluateur
FROM CONCOURS c
JOIN DESSIN d ON c.id_concours = d.id_concours
JOIN UTILISATEUR u1 ON d.id_competiteur = u1.id_utilisateur
JOIN EVALUATION e ON d.id_dessin = e.id_dessin
JOIN UTILISATEUR u2 ON e.id_evaluateur = u2.id_utilisateur;

------------------------------------------------------
--Requête 4 - Participants à Tous les Concours
------------------------------------------------------
SELECT DISTINCT u.nom, u.prenom
FROM UTILISATEUR u
JOIN COMPETITEUR c ON u.id_utilisateur = c.id_competiteur
WHERE NOT EXISTS (
    SELECT co.id_concours
    FROM CONCOURS co
    WHERE NOT EXISTS (
        SELECT d.id_dessin
        FROM DESSIN d
        WHERE d.id_competiteur = c.id_competiteur
        AND d.id_concours = co.id_concours
    )
)
ORDER BY u.nom ASC;

------------------------------------------------------
--Requête 5 - Meilleure Région
------------------------------------------------------
SELECT cl.region, AVG(e.note) AS moyenne_notes
FROM CLUB cl
JOIN CLUB_CONCOURS cc ON cl.id_club = cc.id_club
JOIN DESSIN d ON d.id_concours = cc.id_concours
JOIN EVALUATION e ON d.id_dessin = e.id_dessin
GROUP BY cl.region
ORDER BY moyenne_notes DESC
LIMIT 1;

------------------------------------------------------
--Requête 6 - Top Évaluateurs
------------------------------------------------------   
SELECT u.nom, u.prenom, COUNT(e.id_evaluation) AS nb_evaluations
FROM UTILISATEUR u
JOIN EVALUATEUR ev ON u.id_utilisateur = ev.id_evaluateur
JOIN EVALUATION e ON ev.id_evaluateur = e.id_evaluateur
GROUP BY u.id_utilisateur, u.nom, u.prenom
HAVING COUNT(e.id_evaluation) > 5
ORDER BY nb_evaluations DESC;


------------------------------------------------------
--Requête 7 - Statistiques des Clubs
------------------------------------------------------
SELECT c.nom AS nom_club, 
       COUNT(DISTINCT d.id_competiteur) AS nb_competiteurs,
       COUNT(DISTINCT e.id_evaluateur) AS nb_evaluateurs
FROM CLUB c
JOIN CLUB_CONCOURS cc ON c.id_club = cc.id_club
LEFT JOIN DESSIN d ON cc.id_concours = d.id_concours
LEFT JOIN EVALUATION e ON d.id_dessin = e.id_dessin
GROUP BY c.id_club, c.nom;


------------------------------------------------------
--Requête 8 - Concour non évalué
------------------------------------------------------
SELECT c.theme, c.date_debut, c.date_fin,
       COUNT(d.id_dessin) AS nb_dessins_soumis,
       COUNT(e.id_evaluation) AS nb_evaluations
FROM CONCOURS c
LEFT JOIN DESSIN d ON c.id_concours = d.id_concours
LEFT JOIN EVALUATION e ON d.id_dessin = e.id_dessin
WHERE c.etat != 'EVALUE'
GROUP BY c.id_concours, c.theme, c.date_debut, c.date_fin;
