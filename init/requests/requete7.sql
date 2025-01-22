-------------------------------------------------------
---Requête 7 - Selection des participants disponible d'un club pour un concours
-------------------------------------------------------

SELECT u.numUtilisateur, u.nom, u.prenom
FROM Utilisateur u
WHERE u.numClub = :club
AND NOT EXISTS (
    SELECT 1 
    FROM CompetiteurParticipe cp 
    WHERE cp.numCompetiteur = u.numUtilisateur 
    AND cp.numConcours = :concours
)
AND NOT EXISTS (
    SELECT 1 
    FROM Jury j 
    WHERE j.numEvaluateur = u.numUtilisateur 
    AND j.numConcours = :concours
)
AND NOT EXISTS (
    SELECT 1 
    FROM Concours c 
    WHERE c.numPresident = u.numUtilisateur 
    AND c.numConcours = :concours
)