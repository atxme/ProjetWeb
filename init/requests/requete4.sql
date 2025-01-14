------------------------------------------------------
--Requête 4 - Participants à Tous les Concours
------------------------------------------------------
SELECT DISTINCT u.nom, u.prenom
FROM UTILISATEUR u
JOIN COMPETITEUR c ON u.id_utilisateur = c.id_competiteur
WHERE NOT EXISTS (
    SELECT co.id_concours
    FROM CONCOURS co
    WHERE NOT EXISTS (
        SELECT d.id_dessin
        FROM DESSIN d
        WHERE d.id_competiteur = c.id_competiteur
        AND d.id_concours = co.id_concours
    )
)
ORDER BY u.nom ASC;