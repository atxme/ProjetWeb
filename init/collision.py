import re
import random
import string
import os

def corriger_collisions_insertion_sql(fichier_source, fichier_sortie):
    """
    Lit le fichier fichier_source (insertion5.sql), détecte d'éventuels doublons
    au niveau du champ 'login' dans les INSERT INTO Utilisateur,
    et crée un nouveau fichier fichier_sortie sans ces collisions.
    """
    # Expression régulière simple pour capturer les lignes d'insertion dans la table Utilisateur
    # On suppose une syntaxe de type :
    # INSERT INTO Utilisateur VALUES (..., 'login', 'mdp');
    # ou
    # INSERT INTO Utilisateur (liste de champs) VALUES (..., 'login', 'mdp');
    # Si le fichier contient une syntaxe différente, adapter la RegEx.
    pattern_insert_utilisateur = re.compile(
        r"^(INSERT\s+INTO\s+Utilisateur\s.*VALUES\s*\(([^)]*)\);)", 
        re.IGNORECASE
    )

    # Expression régulière pour capturer les différents champs dans le VALUES(...)
    # On scinde sur les virgules, mais on prend en compte d'éventuelles chaînes
    # entre apostrophes. C’est un petit parseur simplifié.
    # On va extraire le login (7e ou 8e élément selon la structure, en comptant depuis 0).
    # Il peut être plus fiable de repérer la position du champ login en scrutant le DB.
    # Ici on suppose un ordre standard comme dans :
    # INSERT INTO Utilisateur (...) VALUES ( numUtilisateur, numClub, 'nom', 'prenom', age, 'adresse', 'login', 'mdp' );
    pattern_values = re.compile(
        r"""
        ('[^']*'                # Une chaîne entre apostrophes
         |[^,]+                  # ou n'importe quel token séparé par des virgules
        )
        """,
        re.VERBOSE
    )

    logins_existants = set()
    contenu_corrige = []

    with open(fichier_source, "r", encoding="utf-8") as f:
        for ligne in f:
            match_insert = pattern_insert_utilisateur.match(ligne.strip())
            if not match_insert:
                # La ligne ne correspond pas à un INSERT INTO Utilisateur => on la conserve telle quelle
                contenu_corrige.append(ligne)
            else:
                # On a un INSERT INTO Utilisateur
                # Récupérons entièrement la portion entre parentheses
                contenu_values = match_insert.group(2).strip()
                # Séparons en plusieurs champs
                champs = pattern_values.findall(contenu_values)

                # Supposons que champs[6] = 'login', champs[7] = 'mdp' dans la structure standard
                # Vérifions la longueur pour plus de sûreté
                if len(champs) < 8:
                    # Si la structure attendue n'est pas respectée, on laisse comme tel
                    contenu_corrige.append(ligne)
                    continue

                # Déterminons l'index du champ "login" (sous forme de 'login')
                # Dans la plupart des cas, c'est l'index 6 (0,1,2,3,4,5,6 pour login, 7 pour mdp)
                indeks_login = 6
                login_brut = champs[indeks_login].strip()
                # Retirons les apostrophes autour et laissons tel quel si c'est un champ non quoted
                if login_brut.startswith("'") and login_brut.endswith("'"):
                    login = login_brut[1:-1]  # on retire les apostrophes
                else:
                    login = login_brut

                # Vérifions la collision
                if login in logins_existants:
                    # Générons un login unique
                    login_nouveau = generer_login_unique(login, logins_existants)
                    # MàJ du champ
                    champs[indeks_login] = f"'{login_nouveau}'"
                else:
                    # On ajoute le login au set existant s'il n'y est pas
                    logins_existants.add(login)

                # On recompose la ligne
                nouvelle_values = ", ".join(champs)
                nouvelle_ligne = f"INSERT INTO Utilisateur VALUES ({nouvelle_values});"
                contenu_corrige.append(nouvelle_ligne + "\n")

    # Écriture du nouveau fichier
    with open(fichier_sortie, "w", encoding="utf-8") as f:
        f.writelines(contenu_corrige)

def generer_login_unique(login_de_base, logins_existants):
    """
    Génère un login en partant d'un login_de_base, tant qu'il y a collision.
    Ex : on ajoute un suffixe aléatoire, ou on incrémente un compteur.
    """
    while True:
        suffixe = "".join(random.choices(string.digits, k=3))
        login_nouveau = f"{login_de_base}{suffixe}"
        if login_nouveau not in logins_existants:
            logins_existants.add(login_nouveau)
            return login_nouveau

def main():
    source = "insertion5.sql"
    cible = "insertion5_fixed.sql"
    if not os.path.exists(source):
        print(f"Le fichier {source} n'existe pas, script annulé.")
        return

    corriger_collisions_insertion_sql(source, cible)
    print(f"Fichier corrigé créé : {cible}")

if __name__ == "__main__":
    main()
