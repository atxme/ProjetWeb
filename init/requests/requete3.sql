------------------------------------------------------
--Requête 3 - Informations Complètes des Dessins
------------------------------------------------------
SELECT c.id_concours, YEAR(c.date_debut) AS annee, c.descriptif,
       u1.nom AS nom_competiteur, 
       d.id_dessin, d.commentaire AS commentaire_dessin,
       e.note, e.commentaire AS commentaire_evaluation,
       u2.nom AS nom_evaluateur
FROM CONCOURS c
JOIN DESSIN d ON c.id_concours = d.id_concours
JOIN UTILISATEUR u1 ON d.id_competiteur = u1.id_utilisateur
JOIN EVALUATION e ON d.id_dessin = e.id_dessin
JOIN UTILISATEUR u2 ON e.id_evaluateur = u2.id_utilisateur;