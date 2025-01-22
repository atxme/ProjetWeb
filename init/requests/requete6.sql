-------------------------------------------------------
---Requête 6 - Clubs eligible pour un concours
-------------------------------------------------------   

SELECT DISTINCT c.numClub, c.nomClub 
FROM Club c 
INNER JOIN Utilisateur u ON c.numClub = u.numClub
WHERE NOT EXISTS (
    SELECT 1 FROM ClubParticipe cp 
    WHERE cp.numClub = c.numClub 
    AND cp.numConcours = :concours
)
AND (
    EXISTS (
        SELECT 1 FROM Utilisateur u2 
        WHERE u2.numClub = c.numClub
        AND NOT EXISTS (
            SELECT 1 FROM CompetiteurParticipe cp 
            WHERE cp.numCompetiteur = u2.numUtilisateur 
            AND cp.numConcours = :concours
        )
        AND NOT EXISTS (
            SELECT 1 FROM Jury j 
            WHERE j.numEvaluateur = u2.numUtilisateur 
            AND j.numConcours = :concours
        )
    )
)