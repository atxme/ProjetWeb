import random
import string
from datetime import datetime, timedelta
import names  # pip install names

class DataGenerator:
    """
    Génère des données d'insertion SQL pour la base "dessin" en respectant :
     - Logins uniques
     - Dates de remise de dessins comprises entre dateDeb et dateFin du concours
     - Maximum 3 dessins par compétiteur par concours
     - Principales contraintes de la base (FK, triggers)
    """
    def __init__(self, scale_factor=30):
        self.scale_factor = scale_factor

        # Compteurs pour générer des identifiants uniques
        self.current_user_id = 1
        self.current_dessin_id = 1

        # Ensembles et listes pour stocker les données
        self.users_by_role = {
            'admin': [],
            'president': [],
            'evaluateur': [],
            'competiteur': []
        }
        self.clubs = []
        self.concours = []  # liste de concours_id

        # Stocke, pour chaque concours_id, son intervalle de dates
        # ex. concours_info[concours_id] = { 'start': '2024-03-21', 'end': '2024-06-20' }
        self.concours_info = {}

        # Pour gérer l'unicité des logins
        self.generated_logins = set()

    def generate_phone(self):
        return "0" + "".join(str(random.randint(0, 9)) for _ in range(9))

    def generate_address(self):
        return f"{random.randint(1, 100)} Rue {names.get_last_name()}"

    def generate_unique_login(self, last_name, first_name):
        """
        Génère un login unique depuis un nom/prénom, en réessayant tant qu'une collision survient.
        """
        while True:
            candidate = f"{first_name[0].lower()}{last_name.lower()}{random.randint(1,999)}"
            if candidate not in self.generated_logins:
                self.generated_logins.add(candidate)
                return candidate

    def generate_password(self):
        return "".join(random.choices(string.ascii_letters + string.digits, k=8))

    def generate_clubs(self):
        """
        Génère un ensemble de clubs, en se basant sur un panel de régions.
        """
        sql_lines = ["-- Insertion des Clubs"]
        regions = [
            ("Ile-de-France", "75", "Paris"), 
            ("PACA", "13", "Marseille"),
            ("Rhône-Alpes", "69", "Lyon"), 
            ("Occitanie", "31", "Toulouse"),
            ("Bretagne", "35", "Rennes"), 
            ("Nouvelle-Aquitaine", "33", "Bordeaux"),
            ("Hauts-de-France", "59", "Lille"), 
            ("Auvergne-Rhône-Alpes", "74", "Annecy"),
            ("Grand Est", "67", "Strasbourg"), 
            ("Normandie", "76", "Rouen"),
            ("Centre-Val de Loire", "45", "Orléans"), 
            ("Pays de la Loire", "44", "Nantes"),
            ("Bourgogne-Franche-Comté", "21", "Dijon"), 
            ("Corse", "20", "Ajaccio"),
            ("Nouvelle-Calédonie", "988", "Nouméa"),
            ("Guadeloupe", "971", "Pointe-à-Pitre"),
            ("Martinique", "972", "Fort-de-France"),
            ("Guyane", "973", "Cayenne"),
            ("Réunion", "974", "Saint-Denis"),
            ("Mayotte", "976", "Mamoudzou")
        ]
        # On génère scale_factor * len(regions) clubs
        for i in range(1, self.scale_factor + 1):
            for (region, dept, ville) in regions:
                club_id = len(self.clubs) + 1
                self.clubs.append(club_id)
                sql_lines.append(
                    f"INSERT INTO Club VALUES ("
                    f"{club_id}, 'Club {ville} {i}', '{self.generate_address()}', "
                    f"'{self.generate_phone()}', {random.randint(20,100)}, '{ville}', '{dept}', '{region}');"
                )
        return sql_lines

    def generate_user_sql(self, user_id, club_id, last_name=None, first_name=None):
        """
        Construit l'instruction INSERT de l'utilisateur user_id dans un club club_id,
        avec un login unique et un mot de passe aléatoire.
        """
        last_name = last_name or names.get_last_name()
        first_name = first_name or names.get_first_name()
        address = self.generate_address()
        user_login = self.generate_unique_login(last_name, first_name)
        user_password = self.generate_password()
        age = random.randint(18, 70)
        return (
            f"INSERT INTO Utilisateur VALUES ("
            f"{user_id}, {club_id}, '{last_name}', '{first_name}', "
            f"{age}, '{address}', '{user_login}', '{user_password}');"
        )

    def generate_users(self):
        """
        Génère divers utilisateurs : 1 admin, plusieurs présidents, évaluateurs, compétiteurs.
        """
        sql_lines = ["-- Insertion des Utilisateurs"]

        # Admin (1 par défaut)
        admin_id = self.current_user_id
        self.users_by_role['admin'].append(admin_id)
        sql_lines.append(
            self.generate_user_sql(
                admin_id, random.choice(self.clubs),
                last_name="Admin", first_name="System"
            )
        )
        self.current_user_id += 1

        # Présidents (1 par 5 clubs)
        num_presidents = len(self.clubs) // 5
        for _ in range(num_presidents):
            self.users_by_role['president'].append(self.current_user_id)
            sql_lines.append(
                self.generate_user_sql(self.current_user_id, random.choice(self.clubs))
            )
            self.current_user_id += 1

        # Évaluateurs (2 par concours prévu : 4 saisons * scale_factor => *2)
        num_evaluateurs = (self.scale_factor * 4) * 2
        for _ in range(num_evaluateurs):
            self.users_by_role['evaluateur'].append(self.current_user_id)
            sql_lines.append(
                self.generate_user_sql(self.current_user_id, random.choice(self.clubs))
            )
            self.current_user_id += 1

        # Compétiteurs (10 par club)
        for club_id in self.clubs:
            for _ in range(10):
                self.users_by_role['competiteur'].append(self.current_user_id)
                sql_lines.append(
                    self.generate_user_sql(self.current_user_id, club_id)
                )
                self.current_user_id += 1

        return sql_lines

    def generate_roles(self):
        """
        Génère les rôles Admin, President, Evaluateur, Competiteur,
        en liant les identifiants d'utilisateurs déjà créés.
        """
        sql_lines = ["-- Insertion des rôles"]

        # Admin
        admin_id = self.users_by_role['admin'][0]
        sql_lines.append(
            f"INSERT INTO Admin VALUES ({admin_id}, '2023-01-01');"
        )

        # Présidents
        for pres_id in self.users_by_role['president']:
            prime = random.randint(800, 1500)
            sql_lines.append(
                f"INSERT INTO President VALUES ({pres_id}, '2023-01-01', {prime}.00);"
            )

        # Évaluateurs
        specialites = ["Peinture", "Dessin", "Sculpture", "Art numérique", "Photographie"]
        for eval_id in self.users_by_role['evaluateur']:
            sql_lines.append(
                f"INSERT INTO Evaluateur VALUES ({eval_id}, '{random.choice(specialites)}');"
            )

        # Compétiteurs
        for comp_id in self.users_by_role['competiteur']:
            sql_lines.append(
                f"INSERT INTO Competiteur VALUES ({comp_id}, '2024-01-01');"
            )

        return sql_lines

    def get_season_dates(self, season, year):
        """
        Retourne le dictionnaire { 'start': 'YYYY-MM-DD', 'end': 'YYYY-MM-DD' }
        en fonction de la saison et de l'année.
        """
        dates = {
            'printemps': {'start': f'{year}-03-21', 'end': f'{year}-06-20'},
            'ete':       {'start': f'{year}-06-21', 'end': f'{year}-09-20'},
            'automne':   {'start': f'{year}-09-21', 'end': f'{year}-12-20'},
            'hiver':     {'start': f'{year}-12-21', 'end': f'{year+1}-03-20'}
        }
        return dates[season]

    def generate_concours(self):
        """
        Génère les concours pour 2 années (ex. 2024, 2025) et 4 saisons par an.
        Stocke dans self.concours_info le start/end de chaque concours_id.
        """
        sql_lines = ["-- Insertion des Concours"]
        seasons = ['printemps', 'ete', 'automne', 'hiver']

        for year in range(2024, 2026):
            for season in seasons:
                concours_id = len(self.concours) + 1
                self.concours.append(concours_id)

                president_id = random.choice(self.users_by_role['president'])
                date_info = self.get_season_dates(season, year)
                start_date_str = date_info['start']
                end_date_str = date_info['end']

                sql_lines.append(
                    f"INSERT INTO Concours VALUES ("
                    f"{concours_id}, {president_id}, "
                    f"'Concours {season.capitalize()} {year}', "
                    f"'{start_date_str}', '{end_date_str}', "
                    f"'en cours', 6, {random.randint(20,50)}, "
                    f"'Description du concours', '{season}', {year});"
                )

                # On stocke l'intervalle de dates dans un dict
                self.concours_info[concours_id] = {
                    'start': start_date_str,
                    'end':   end_date_str
                }

        return sql_lines
    
    def generate_directeurs(self):
        sql_lines = ["-- Insertion des Directeurs"]
        
        # On assigne un directeur par club
        # On prend des utilisateurs qui ne sont pas déjà admin/président/évaluateur/compétiteur
        for club_id in self.clubs:
            # Créer un nouvel utilisateur spécifiquement pour être directeur
            sql_lines.append(self.generate_user_sql(self.current_user_id, club_id))
            
            # Ajouter l'entrée dans la table Directeur
            sql_lines.append(
                f"INSERT INTO Directeur VALUES ({self.current_user_id}, {club_id}, '2023-01-01');"
            )
            
            self.current_user_id += 1
        
        return sql_lines


    def generate_participations(self):
        """
        Génère les participations ClubParticipe et CompetiteurParticipe
        sur une base aléatoire raisonnable.
        """
        sql_lines = ["-- Insertion des participations"]

        # ClubParticipe (chaque concours, 6 clubs aléatoires)
        for concours_id in self.concours:
            # Au moins 6 clubs (la table Concours impose nbClub >= 6)
            how_many = min(len(self.clubs), 6)
            selected_clubs = random.sample(self.clubs, how_many)
            for club_id in selected_clubs:
                sql_lines.append(
                    f"INSERT INTO ClubParticipe VALUES ({concours_id}, {club_id});"
                )

        # CompetiteurParticipe (on met par ex. 20 compétiteurs au hasard par concours)
        for concours_id in self.concours:
            how_many = min(len(self.users_by_role['competiteur']), 20)
            selected_competiteurs = random.sample(self.users_by_role['competiteur'], how_many)
            for comp_id in selected_competiteurs:
                sql_lines.append(
                    f"INSERT INTO CompetiteurParticipe VALUES ({concours_id}, {comp_id});"
                )

        return sql_lines

    def generate_dessins(self):
        """
        Génère entre 1 et 3 dessins par compétiteur pour chaque concours,
        en veillant à ce que dateRemise soit dans l'intervalle [dateDeb, dateFin].
        """
        sql_lines = ["-- Insertion des Dessins"]

        for concours_id in self.concours:
            # Récupération des dates de début/fin pour ce concours
            info = self.concours_info[concours_id]
            start_date = datetime.strptime(info['start'], '%Y-%m-%d')
            end_date = datetime.strptime(info['end'], '%Y-%m-%d')

            # On choisit un petit lot de compétiteurs (par ex. 10)
            competiteurs = random.sample(self.users_by_role['competiteur'], 10)

            for comp_id in competiteurs:
                # Chaque compétiteur peut soumettre 1 à 3 dessins
                num_dessins = random.randint(1, 3)
                for _ in range(num_dessins):
                    # Génération d'une date de remise random dans l'intervalle
                    days_between = (end_date - start_date).days
                    offset = random.randint(0, max(days_between, 0))
                    remise_date = start_date + timedelta(days=offset)
                    remise_str = remise_date.strftime('%Y-%m-%d')

                    sql_lines.append(
                        f"INSERT INTO Dessin VALUES ("
                        f"{self.current_dessin_id}, {comp_id}, {concours_id}, NULL, "
                        f"'Commentaire', '{remise_str}', 'dessin_{self.current_dessin_id}.jpg');"
                    )
                    self.current_dessin_id += 1

        return sql_lines

    def generate_jury_and_evaluations(self):
        """
        Génère aléatoirement quelques entrées dans la table Jury (qui évalue quel concours).
        Ne génère pas forcèment d'Evaluation (on peut en ajouter si besoin).
        """
        sql_lines = ["-- Insertion des Jury et Evaluations"]

        # Jury
        for concours_id in self.concours:
            # On sélectionne 5 évaluateurs arbitrairement pour chaque concours
            how_many = min(len(self.users_by_role['evaluateur']), 5)
            selected_evaluateurs = random.sample(self.users_by_role['evaluateur'], how_many)
            for eval_id in selected_evaluateurs:
                sql_lines.append(
                    f"INSERT INTO Jury VALUES ({eval_id}, {concours_id});"
                )

        # Si vous le souhaitez, vous pouvez générer ici des Evaluations
        # en veillant à ne pas dépasser 8 par évaluateur et 2 par dessin,
        # en respectant la contrainte de dateEvaluation, etc.

        return sql_lines

def main():
    generator = DataGenerator(scale_factor=30)

    sql_content = [
        "SET FOREIGN_KEY_CHECKS = 0;",
        "-- Nettoyage des tables",
        *[f"TRUNCATE TABLE {table};" for table in [
            "Evaluation", "Jury", "Dessin", "CompetiteurParticipe",
            "ClubParticipe", "Concours", "Evaluateur", "Competiteur",
            "President", "Admin", "Directeur", "Utilisateur", "Club"
        ]],
        "SET FOREIGN_KEY_CHECKS = 1;\n"
    ]

    # Ordre logique de génération
    generation_order = [
        generator.generate_clubs,
        generator.generate_users,
        generator.generate_roles,
        generator.generate_directeurs,
        generator.generate_concours,
        generator.generate_participations,
        generator.generate_dessins,
        generator.generate_jury_and_evaluations
    ]

    for func in generation_order:
        print("Generating", func)
        sql_content.extend(func())
        sql_content.append("")  # Ligne vide pour la lisibilité


    with open("insertion5.sql", "w", encoding="utf-8") as f:
        f.write("\n".join(sql_content))

    print("Le fichier insertion5.sql a été généré.")

if __name__ == "__main__":
    main()
