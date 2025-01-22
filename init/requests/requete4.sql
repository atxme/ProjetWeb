------------------------------------------------------
--Requête 4 - Participants à Tous les Concours
------------------------------------------------------
SELECT 
    u.nom,
    u.prenom,
    u.age
FROM 
    Utilisateur u
    INNER JOIN Competiteur comp ON u.numUtilisateur = comp.numCompetiteur
WHERE NOT EXISTS (
    SELECT numConcours 
    FROM Concours c
    WHERE NOT EXISTS (
        SELECT 1 
        FROM CompetiteurParticipe cp
        WHERE cp.numConcours = c.numConcours
        AND cp.numCompetiteur = comp.numCompetiteur
    )
)
ORDER BY 
    u.age ASC;
