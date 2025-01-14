------------------------------------------------------
--Requête 8 - Concour non évalué
------------------------------------------------------
SELECT c.theme, c.date_debut, c.date_fin,
       COUNT(d.id_dessin) AS nb_dessins_soumis,
       COUNT(e.id_evaluation) AS nb_evaluations
FROM CONCOURS c
LEFT JOIN DESSIN d ON c.id_concours = d.id_concours
LEFT JOIN EVALUATION e ON d.id_dessin = e.id_dessin
WHERE c.etat != 'EVALUE'
GROUP BY c.id_concours, c.theme, c.date_debut, c.date_fin;
