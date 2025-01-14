------------------------------------------------------
--Requête 1 -Participants 2023
------------------------------------------------------
SELECT DISTINCT u.nom, u.prenom, u.adresse, 
       co.descriptif, co.date_debut, co.date_fin,
       cl.nom AS nom_club, cl.departement, cl.region
FROM UTILISATEUR u
JOIN COMPETITEUR comp ON u.id_utilisateur = comp.id_competiteur
JOIN DESSIN d ON comp.id_competiteur = d.id_competiteur
JOIN CONCOURS co ON d.id_concours = co.id_concours
JOIN CLUB cl ON cl.id_directeur = u.id_utilisateur
WHERE YEAR(co.date_debut) = 2023;