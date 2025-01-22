-------------------------------------------------------
---Requête 8 - Details d'un concours
-------------------------------------------------------

SELECT 
    c.*,
    p.nom as president_nom,
    p.prenom as president_prenom,
    (SELECT COUNT(*) FROM ClubParticipe WHERE numConcours = c.numConcours) as nb_clubs,
    (SELECT COUNT(*) FROM CompetiteurParticipe WHERE numConcours = c.numConcours) as nb_competiteurs,
    (SELECT COUNT(*) FROM Jury WHERE numConcours = c.numConcours) as nb_evaluateurs,
    (
        SELECT COUNT(*) 
        FROM Dessin d 
        LEFT JOIN Evaluation e ON d.numDessin = e.numDessin
        WHERE d.numConcours = c.numConcours 
        AND e.numDessin IS NOT NULL
    ) as nb_dessins_evalues,
    (
        SELECT COUNT(*) 
        FROM Dessin 
        WHERE numConcours = c.numConcours
    ) as nb_dessins_total
FROM Concours c
JOIN Utilisateur u ON c.numPresident = u.numUtilisateur
JOIN President p ON p.numPresident = u.numUtilisateur
WHERE c.numConcours = :numConcours