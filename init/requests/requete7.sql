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