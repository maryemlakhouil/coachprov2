/**********************************************
Creation De La Base De Donnée ET Les Tableaux 
**********************************************/

/* 1 - La Base De Donnée */

CREATE DATABASE coachsport;
USE coachsport;

/* 2- Les Tableaux */

create table users(
    id int AUTO_INCREMENT primary key,
    nom varchar(50) not null,
    prenom varchar(50) not null ,
    email varchar(100) not null UNIQUE,
    password varchar(255) not null,
    role ENUM('sportif','coach') NOT NULL,
    cree_a datetime DEFAULT CURRENT_TIMESTAMP
);

create table coach_profile(
    id int AUTO_INCREMENT primary key,
    biographie text ,
    experience int not null,
    certification text,
    photo varchar(255),
    user_id int not null,
    constraint fk_users FOREIGN key (user_id) references users(id) on delete cascade
);

create table sports(
    id int AUTO_INCREMENT primary key ,
    sport_nom varchar(50) not null unique
);

create table coach_sports(
    coach_id int not null,
    sport_id int not null,
    constraint fk_coach1 foreign key (coach_id) references users (id),
    constraint fk_sport1 foreign key (sport_id) references sports (id),
    primary key (coach_id,sport_id)
);

create table disponibilites(
    id int auto_increment primary key,
    coach_id int not null,
    heure_debut time not null ,
    heure_fin time not null,
    date date not null,
    status enum ('libre','reserve') DEFAULT 'libre',
    constraint fk_dispo foreign key (coach_id) references users(id),
    UNIQUE(coach_id, date, heure_debut, heure_fin)
);

create table reservations(
    id int AUTO_INCREMENT PRIMARY KEY,
    sportif_id INT NOT NULL,
    coach_id INT NOT NULL,
    availability_id INT NOT NULL,
    status ENUM('en_attente', 'acceptee', 'refusee', 'annulee') DEFAULT 'en_attente',
    cree_a datetime default CURRENT_TIMESTAMP,
    constraint fk_res_sport foreign key (sportif_id) references users(id),
    constraint fk_res_coach foreign key (coach_id) references users(id),
    constraint fk_res_dispo foreign key (availability_id) references disponibilites(id),
    unique(sportif_id,coach_id,availability_id)
);

/*************************
    Insertion De Données 
*************************/

/* 1 - users */
insert into users(nom,prenom,email,password,role,cree_a) values('lakhouil','asaad','lakhouil@gmail.com','1234567','coach','2025-11-12');
insert into users(nom,prenom,email,password,role,cree_a) values('banani','sanae','banani@gmail.com','00000000','sportif','2025-11-12');

/* 2 - coach_profile : user_id doit être un coach*/
insert into coach_profile (biographie, experience, certification, photo, user_id)values ('Coach sportif certifié, spécialisé en fitness et football',5,'certificat_fitness.pdf','https://mockmind-api.uifaces.co/content/human/125.jpg',1);

/* 3 - sports */
INSERT INTO sports (sport_nom) VALUES ('Football'),
                                      ('Fitness'),
                                      ('Natation'),
                                      ('Tennis'),
                                      ('Boxe'),
                                      ('Athlétisme');

/* 4 - coach_sports : Lier le coach aux sports existants*/
INSERT INTO coach_sports (coach_id, sport_id) VALUES(1, 1), 
                                                    (1, 2); 


/* 5 - disponibilies*/
INSERT INTO disponibilites (coach_id, heure_debut, heure_fin, date) VALUES (1, '10:00:00', '11:00:00', '2025-12-20'),
                                                                            (1, '15:00:00', '16:00:00', '2025-12-21');

/* 6 - reservations*/
insert  reservations (sportif_id, coach_id, availability_id, status) VALUES (2, 1, 1, 'en_attente');

/***************************
    Recuperation De Données
****************************/ 
select * from users;
select * from sports;
select * from coach_profile where id =1 ;
select * from coach_sports;
select * from disponibilites;
select * from reservations;

/***************************
 Modifier Les Donnes 
****************************/ 

update reservations
    set status ='accepte'
    where id=1 and coach_id = 2;

update coach_profile 
    set biographie ='jai passe 4 années a la salle fetness',experience='4'
    where user_id =8 ;

UPDATE disponibilites
SET status = 'reserve'
WHERE id = 1;

-- Voir les coachs

SELECT * FROM users WHERE role = 'coach';

-- Profil coach

SELECT * FROM coach_profile WHERE user_id = 1;

-- Sports du coach
SELECT s.sport_nom
FROM coach_sports cs
JOIN sports s ON cs.sport_id = s.id
WHERE cs.coach_id = 1;

-- Réservations du coach
SELECT * FROM reservations WHERE coach_id = 1;

-- Réservations du sportif
SELECT * FROM reservations WHERE sportif_id = 2;

SELECT id, date, heure_debut, heure_fin
FROM disponibilites
WHERE coach_id = 1
AND status = 'libre'
AND date >= CURDATE()
ORDER BY date, heure_debut;

/*************************
    DELETE les Donnes 
*************************/
delete from users ;
delete from coach_profile;
delete from sports;
delete from coach_sports;
delete from disponibilites;
delete from reservations;






