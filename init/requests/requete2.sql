------------------------------------------------------
--Requête 2 -  Afficher par ordre croissant de la note tous les dessins qui ont été évalués en 2023. 
------------------------------------------------------
SELECT 
    d.numDessin, 
    e.note, 
    u.nom AS nom_competiteur,
    c.descriptif AS description_concours, 
    c.theme AS theme_concours
FROM Dessin d
JOIN Competiteur comp ON d.numCompetiteur = comp.numCompetiteur
JOIN Utilisateur u ON comp.numCompetiteur = u.numUtilisateur
JOIN Concours c ON d.numConcours = c.numConcours
JOIN Evaluation e ON d.numDessin = e.numDessin
WHERE YEAR(e.dateEvaluation) = 2023
ORDER BY e.note ASC;