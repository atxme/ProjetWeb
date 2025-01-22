------------------------------------------------------
--Requête 5 - Meilleure Région
------------------------------------------------------
WITH RegionMoyenne AS (
    SELECT 
        cl.region,
        AVG(e.note) AS moyenne_notes
    FROM 
        Club cl
        INNER JOIN Utilisateur u ON cl.numClub = u.numClub
        INNER JOIN Competiteur comp ON u.numUtilisateur = comp.numCompetiteur
        INNER JOIN Dessin d ON comp.numCompetiteur = d.numCompetiteur
        INNER JOIN Evaluation e ON d.numDessin = e.numDessin
    GROUP BY 
        cl.region
)
SELECT 
    region,
    ROUND(moyenne_notes, 2) AS moyenne_notes
FROM 
    RegionMoyenne
WHERE 
    moyenne_notes = (SELECT MAX(moyenne_notes) FROM RegionMoyenne);
