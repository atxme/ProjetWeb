------------------------------------------------------
--Requête 1 -Participants 2023
-- Récupérer les participants du concours
------------------------------------------------------
SELECT DISTINCT 
    u.nom,
    u.prenom, 
    u.adresse,
    u.age,
    c.descriptif AS description_concours,
    c.dateDeb AS date_debut,
    c.dateFin AS date_fin,
    cl.nomClub AS nom_club,
    cl.departement,
    cl.region
FROM 
    Utilisateur u
    INNER JOIN Competiteur comp ON u.numUtilisateur = comp.numCompetiteur
    INNER JOIN CompetiteurParticipe cp ON comp.numCompetiteur = cp.numCompetiteur
    INNER JOIN Concours c ON cp.numConcours = c.numConcours
    INNER JOIN Club cl ON u.numClub = cl.numClub
WHERE 
    YEAR(c.dateDeb) = 2023
ORDER BY 
    u.nom, u.prenom;
