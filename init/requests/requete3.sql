------------------------------------------------------
--Requête 3 - Informations Complètes des Dessins
------------------------------------------------------
SELECT 
    c.numConcours,
    c.annee,
    c.descriptif AS description_concours,
    u_comp.nom AS nom_competiteur,
    d.numDessin,
    d.commentaire AS commentaire_dessin,
    e.note,
    e.commentaire AS commentaire_evaluation,
    u_eval.nom AS nom_evaluateur
FROM 
    Dessin d
    INNER JOIN Concours c ON d.numConcours = c.numConcours
    INNER JOIN Competiteur comp ON d.numCompetiteur = comp.numCompetiteur
    INNER JOIN Utilisateur u_comp ON comp.numCompetiteur = u_comp.numUtilisateur
    INNER JOIN Evaluation e ON d.numDessin = e.numDessin
    INNER JOIN Evaluateur eval ON e.numEvaluateur = eval.numEvaluateur
    INNER JOIN Utilisateur u_eval ON eval.numEvaluateur = u_eval.numUtilisateur
ORDER BY 
    c.annee DESC, 
    c.numConcours, 
    d.numDessin;
