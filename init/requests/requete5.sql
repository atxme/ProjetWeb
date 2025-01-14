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