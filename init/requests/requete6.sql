------------------------------------------------------
--Requête 6 - Top Évaluateurs
------------------------------------------------------   
SELECT u.nom, u.prenom, COUNT(e.id_evaluation) AS nb_evaluations
FROM UTILISATEUR u
JOIN EVALUATEUR ev ON u.id_utilisateur = ev.id_evaluateur
JOIN EVALUATION e ON ev.id_evaluateur = e.id_evaluateur
GROUP BY u.id_utilisateur, u.nom, u.prenom
HAVING COUNT(e.id_evaluation) > 5
ORDER BY nb_evaluations DESC;