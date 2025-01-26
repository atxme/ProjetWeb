import random
from datetime import datetime, timedelta
import names  # pip install names
import string

class DataGenerator:
    def __init__(self, scale_factor=30):
        self.scale_factor = scale_factor
        self.current_user_id = 1
        self.current_dessin_id = 1
        self.users_by_role = {'admin': [], 'president': [], 'evaluateur': [], 'competiteur': []}
        self.clubs = []
        self.concours = []
        # Ajout d'un ensemble pour stocker les logins déjà générés afin d'éviter les doublons
        self.generated_logins = set()
        
    def generate_phone(self):
        return f"0{''.join(str(random.randint(0,9)) for _ in range(9))}"
    
    def generate_address(self):
        return f"{random.randint(1,100)} Rue {names.get_last_name()}"
    
    def generate_login(self, nom, prenom):
        """
        Génère un login unique en réessayant tant qu'une collision survient.
        """
        while True:
            candidate = f"{prenom[0].lower()}{nom.lower()}{random.randint(1,999)}"
            if candidate not in self.generated_logins:
                self.generated_logins.add(candidate)
                return candidate

    def generate_password(self):
        return ''.join(random.choices(string.ascii_letters + string.digits, k=8))

    def generate_clubs(self):
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
            ("Guadeloupe", "971", "Basse-Terre"),
            ("Martinique", "972", "Fort-de-France"),
            ("Guyane", "973", "Cayenne"),
            ("La Réunion", "974", "Saint-Denis"),
            ("Mayotte", "976", "Mamoudzou"),
            ("Saint-Pierre-et-Miquelon", "975", "Saint-Pierre"),
            ("Saint-Barthélemy", "977", "Gustavia"),
            ("Saint-Martin", "978", "Marigot"),
            ("Wallis-et-Futuna", "986", "Mata-Utu"),
            ("Polynésie française", "987", "Papeete"),
            ("Nouvelle-Calédonie", "988", "Nouméa")
        ]
        
        for i in range(1, self.scale_factor + 1):
            for region, dept, ville in regions:
                club_id = len(self.clubs) + 1
                self.clubs.append(club_id)
                sql_lines.append(
                    f"INSERT INTO Club VALUES ({club_id}, 'Club {ville} {i}', "
                    f"'{self.generate_address()}', '{self.generate_phone()}', "
                    f"{random.randint(20,100)}, '{ville}', '{dept}', '{region}');"
                )
        return sql_lines

    def generate_user_sql(self, user_id, club_id, nom=None, prenom=None):
        nom = nom or names.get_last_name()
        prenom = prenom or names.get_first_name()
        address = self.generate_address()
        user_login = self.generate_login(nom, prenom)
        user_password = self.generate_password()
        age = random.randint(18,70)
        return (
            f"INSERT INTO Utilisateur VALUES ({user_id}, {club_id}, '{nom}', '{prenom}', "
            f"{age}, '{address}', '{user_login}', '{user_password}');"
        )

    def generate_users(self):
        sql_lines = ["-- Insertion des Utilisateurs"]
        
        # Admin (1 par défaut)
        admin_id = self.current_user_id
        self.users_by_role['admin'].append(admin_id)
        sql_lines.append(self.generate_user_sql(admin_id, random.choice(self.clubs), "Admin", "System"))
        self.current_user_id += 1

        # Présidents (1 par 5 clubs)
        num_presidents = len(self.clubs) // 5
        for _ in range(num_presidents):
            self.users_by_role['president'].append(self.current_user_id)
            sql_lines.append(self.generate_user_sql(self.current_user_id, random.choice(self.clubs)))
            self.current_user_id += 1

        # Évaluateurs (2 par concours prévu : 4 saisons * scale_factor => on met *2)
        num_evaluateurs = (self.scale_factor * 4) * 2
        for _ in range(num_evaluateurs):
            self.users_by_role['evaluateur'].append(self.current_user_id)
            sql_lines.append(self.generate_user_sql(self.current_user_id, random.choice(self.clubs)))
            self.current_user_id += 1

        # Compétiteurs (10 par club)
        for club_id in self.clubs:
            for _ in range(10):
                self.users_by_role['competiteur'].append(self.current_user_id)
                sql_lines.append(self.generate_user_sql(self.current_user_id, club_id))
                self.current_user_id += 1

        return sql_lines

    def generate_roles(self):
        sql_lines = ["-- Insertion des rôles"]
        
        # Admin
        sql_lines.append(
            f"INSERT INTO Admin VALUES ({self.users_by_role['admin'][0]}, '2023-01-01');"
        )

        # Présidents
        for president_id in self.users_by_role['president']:
            sql_lines.append(
                f"INSERT INTO President VALUES ({president_id}, "
                f"'2023-01-01', {random.randint(800,1500)}.00);"
            )

        # Évaluateurs
        specialites = ["Peinture", "Dessin", "Sculpture", "Art numérique", "Photographie"]
        for evaluateur_id in self.users_by_role['evaluateur']:
            sql_lines.append(
                f"INSERT INTO Evaluateur VALUES ({evaluateur_id}, '{random.choice(specialites)}');"
            )

        # Compétiteurs
        for competiteur_id in self.users_by_role['competiteur']:
            sql_lines.append(
                f"INSERT INTO Competiteur VALUES ({competiteur_id}, '2024-01-01');"
            )

        return sql_lines

    def generate_concours(self):
        sql_lines = ["-- Insertion des Concours"]
        seasons = ['printemps', 'ete', 'automne', 'hiver']
        
        for year in range(2024, 2026):
            for season in seasons:
                concours_id = len(self.concours) + 1
                self.concours.append(concours_id)
                
                president_id = random.choice(self.users_by_role['president'])
                dates = self.get_season_dates(season, year)
                
                sql_lines.append(
                    f"INSERT INTO Concours VALUES ({concours_id}, {president_id}, "
                    f"'Concours {season.capitalize()} {year}', '{dates['start']}', '{dates['end']}', "
                    f"'en cours', 6, {random.randint(20,50)}, 'Description du concours', "
                    f"'{season}', {year});"
                )
        return sql_lines

    def get_season_dates(self, season, year):
        dates = {
            'printemps': {'start': f'{year}-03-21', 'end': f'{year}-06-20'},
            'ete': {'start': f'{year}-06-21', 'end': f'{year}-09-20'},
            'automne': {'start': f'{year}-09-21', 'end': f'{year}-12-20'},
            'hiver': {'start': f'{year}-12-21', 'end': f'{year+1}-03-20'}
        }
        return dates[season]

    def generate_participations(self):
        sql_lines = ["-- Insertion des participations"]
        
        # ClubParticipe
        for concours_id in self.concours:
            selected_clubs = random.sample(self.clubs, min(len(self.clubs), 6))
            for club_id in selected_clubs:
                sql_lines.append(
                    f"INSERT INTO ClubParticipe VALUES ({concours_id}, {club_id});"
                )

        # CompetiteurParticipe
        for concours_id in self.concours:
            selected_competiteurs = random.sample(
                self.users_by_role['competiteur'],
                min(len(self.users_by_role['competiteur']), 20)
            )
            for comp_id in selected_competiteurs:
                sql_lines.append(
                    f"INSERT INTO CompetiteurParticipe VALUES ({concours_id}, {comp_id});"
                )

        return sql_lines

    def generate_dessins(self):
        sql_lines = ["-- Insertion des Dessins"]
        for concours_id in self.concours:
            competiteurs = random.sample(self.users_by_role['competiteur'], 10)
            for comp_id in competiteurs:
                for _ in range(random.randint(1, 3)):  # Max 3 dessins par compétiteur
                    sql_lines.append(
                        f"INSERT INTO Dessin VALUES ({self.current_dessin_id}, {comp_id}, {concours_id}, NULL, 'Commentaire', '2024-01-15', 'dessin_{self.current_dessin_id}.jpg');"
                    )
                    self.current_dessin_id += 1
        return sql_lines

    def generate_jury_and_evaluations(self):
        sql_lines = ["-- Insertion des Jury et Evaluations"]
        
        # Jury
        for concours_id in self.concours:
            evaluateurs = random.sample(
                self.users_by_role['evaluateur'],
                min(len(self.users_by_role['evaluateur']), 5)
            )
            for eval_id in evaluateurs:
                sql_lines.append(
                    f"INSERT INTO Jury VALUES ({eval_id}, {concours_id});"
                )

        # Évaluations (si besoin, on peut les ajouter ici)
        # On n'en génère pas dans cet exemple, mais vous pouvez ajouter des lignes
        # pour Evaluation dans la même logique en tenant compte des triggers
        # et du nombre maximum d'évaluations

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
    
    generation_order = [
        generator.generate_clubs,
        generator.generate_users,
        generator.generate_roles,
        generator.generate_concours,
        generator.generate_participations,
        generator.generate_dessins,
        generator.generate_jury_and_evaluations
    ]
    
    for func in generation_order:
        sql_content.extend(func())
        sql_content.append("")

    with open("insertion5.sql", "w", encoding="utf-8") as f:
        f.write("\n".join(sql_content))

if __name__ == "__main__":
    main()
