------------------------------------------------------
--Requête 2 -  Afficher par ordre croissant de la note tous les dessins qui ont été évalués en 2023. 
------------------------------------------------------
SELECT 
    d.numDessin,
    e.note,
    u.nom AS nom_competiteur,
    c.descriptif AS description_concours,
    c.theme AS theme_concours
FROM 
    Dessin d
    INNER JOIN Evaluation e ON d.numDessin = e.numDessin
    INNER JOIN Competiteur comp ON d.numCompetiteur = comp.numCompetiteur
    INNER JOIN Utilisateur u ON comp.numCompetiteur = u.numUtilisateur
    INNER JOIN Concours c ON d.numConcours = c.numConcours
WHERE 
    YEAR(e.dateEvaluation) = 2022
ORDER BY 
    e.note ASC;
