
/* =====================================================
   DATABASE
===================================================== */
DROP DATABASE IF EXISTS coach_platform;
CREATE DATABASE coach_platform CHARACTER SET utf8mb4;
USE coach_platform;

/* =====================================================
   USERS (PARENT - HERITAGE)
===================================================== */
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    role ENUM('coach', 'sportif') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* =====================================================
   COACHS (HERITAGE)
===================================================== */
CREATE TABLE coachs (
    user_id INT PRIMARY KEY,
    discipline VARCHAR(100),
    experience INT,
    description TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

/* =====================================================
   SPORTIFS (HERITAGE)
===================================================== */
CREATE TABLE sportifs (
    user_id INT PRIMARY KEY,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

/* =====================================================
   SEANCES
===================================================== */
CREATE TABLE seances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coach_id INT,
    date_seance DATE,
    heure TIME,
    duree INT, -- minutes
    statut ENUM('disponible', 'reservee') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coachs(user_id)
);

/* =====================================================
   RESERVATIONS
===================================================== */
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seance_id INT UNIQUE,
    sportif_id INT,
    reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seance_id) REFERENCES seances(id),
    FOREIGN KEY (sportif_id) REFERENCES sportifs(user_id)
);

/* =====================================================
   INSERT USERS
===================================================== */
INSERT INTO users (nom, prenom, email, role) VALUES
('El Amrani', 'Youssef', 'youssef@coach.com', 'coach'),
('Benali', 'Sara', 'sara@coach.com', 'coach'),
('Haddad', 'Karim', 'karim@coach.com', 'coach'),
('Ait Ali', 'Nadia', 'nadia@coach.com', 'coach'),
('Raji', 'Omar', 'omar@coach.com', 'coach'),

('Saidi', 'Amine', 'amine@sportif.com', 'sportif'),
('Lahcen', 'Rania', 'rania@sportif.com', 'sportif'),
('Fassi', 'Othmane', 'othmane@sportif.com', 'sportif'),
('Zahraoui', 'Salma', 'salma@sportif.com', 'sportif'),
('Kamal', 'Yassine', 'yassine@sportif.com', 'sportif'),
('Berrada', 'Imane', 'imane@sportif.com', 'sportif');

/* =====================================================
   INSERT COACHS
===================================================== */
INSERT INTO coachs (user_id, discipline, experience, description) VALUES
(1, 'Fitness', 8, 'Coach fitness certifié'),
(2, 'Yoga', 6, 'Spécialiste yoga et respiration'),
(3, 'Musculation', 10, 'Préparateur physique'),
(4, 'Pilates', 5, 'Coach pilates bien-être'),
(5, 'CrossFit', 7, 'CrossFit compétition');

/* =====================================================
   INSERT SPORTIFS
===================================================== */
INSERT INTO sportifs (user_id) VALUES
(6),(7),(8),(9),(10),(11);

/* =====================================================
   INSERT SEANCES
===================================================== */
INSERT INTO seances (coach_id, date_seance, heure, duree, statut) VALUES
(1, '2025-01-10', '10:00', 60, 'reservee'),
(1, '2025-01-11', '11:00', 90, 'reservee'),
(1, '2025-01-12', '10:30', 60, 'disponible'),

(2, '2025-02-05', '09:00', 60, 'reservee'),
(2, '2025-02-06', '09:30', 60, 'reservee'),
(2, '2025-02-07', '10:00', 60, 'disponible'),

-- Conflit horaire
(3, '2025-03-01', '14:00', 90, 'reservee'),
(3, '2025-03-01', '15:00', 60, 'reservee'),

-- Coach inactif
(4, '2024-11-01', '08:00', 60, 'disponible'),

(5, '2025-01-20', '18:00', 120, 'reservee'),
(5, '2025-01-22', '18:00', 120, 'reservee');

/* =====================================================
   INSERT RESERVATIONS
===================================================== */
INSERT INTO reservations (seance_id, sportif_id, reserved_at) VALUES
(1, 6, '2025-01-09 09:30'),
(2, 7, '2025-01-10 10:30'),
(4, 8, '2025-02-04 20:00'),
(5, 9, '2025-02-05 22:00'),
(7, 6, '2025-02-28 23:30'),
(8, 7, '2025-02-28 23:45'),
(10, 10, '2025-01-19 23:00'),
(11, 11, '2025-01-21 23:30');

/*============================================
    Les requettes sql *
=============================================*/

/* chalenge 1*/

-- Afficher pour chaque coach nombre total de séances créées
select coach_id,count(id) as total_seances
from seances 
GROUP BY coach_id;

-- Afficher pour chaque coach nombre de séances réservées

    select s.coach_id,count(s.id) as total_seances_Reservee
    from seances s 
    join reservations r on  s.id=seance_id
    group by s.coach_id;

-- afficher pour chaque coach taux de réservation (%)

SELECT c.user_id, (COUNT(r.id) / count(s.id) * 100) AS taux_reservation
    FROM seances s
    JOIN coachs c ON s.coach_id = c.user_id
    JOIN reservations r ON r.seance_id = s.id
    GROUP BY c.user_id;

-- afficher seulement les coachs ayant ≥3 séances
select u.nom,u.prenom,count(s.id) as total_seances
from  coachs c 
join users u on u.id=c.user_id 
join seances s on c.user_id = s.coach_id
GROUP BY u.nom,u.prenom
HAVING total_seances>=3;

/********************
      chalenge 2 
*********************/

-- Lister les sportifs qui ont réservé le plus de séances par mois, avec :nom, prénom
select u.nom,u.prenom,count(*) as total_reservations,DATE_FORMAT(r.reserved_at,'%M')as mois
from reservations r 
join users u on u.id= r.sportif_id
group by u.id,mois
order by total_reservations DESC;

-- Lister les sportifs qui ont réservé le plus de séances par mois, avec nombre de réservations par mois
SELECT date_format(r.reserved_at,'%M') as mois ,r.sportif_id ,count(*) as total_reservations
from reservations r 
join users u on u.id=r.sportif_id
group by r.sportif_id,mois;

-- mois et année et ordre décroissant par nombre de réservations
select u.nom,u.prenom,count(*) as total_reservations,DATE_FORMAT(r.reserved_at,'%Y-%m')as année_mois
from reservations r 
join users u on u.id= r.sportif_id
group by u.id,année_mois
order by total_reservations DESC;

/********************
      chalenge 3
*********************/

-- Trouver les séances du même coach qui se chevauchent dans le temps :afficher coach, date, heure début, heure fin, id séance

-- début A < fin B -- Et -- début B < fin A

select u.nom as Nom_coach,u.prenom as Prenom_coach,s1.date_seance,s1.heure as Heure_Debut,DATE_ADD(s1.heure,INTERVAL s1.duree MINUTE) as heure_fin,s1.id as seance_id
from seances s1 
join seances s2 on s1.coach_id=s2.coach_id
and s1.date_seance=s2.date_seance
and s1.heure < DATE_ADD(s2.heure,INTERVAL s2.duree MINUTE)
and s2.heure < DATE_ADD(s1.heure,INTERVAL s1.duree MINUTE)
and  s1.id <>s2.id
join users u on u.id=s1.coach_id;   

/********************
      chalenge 4
*********************/
-- Lister les coachs :qui n’ont aucune réservation depuis 60 jours

select u.nom as nom_coach,u.prenom as Prenom_coach
from coachs c 
join users u on u.id=c.user_id
left join seances s on c.user_id=s.coach_id
left join  reservations r on s.id=r.seance_id
group by u.nom,u.prenom
having  max(r.reserved_at)is null or DATEDIFF(CURDATE(),MAX(r.reserved_at))>60;

-- Lister les coachs :mais dont les sportifs ont réservé des séances récemment
select u.nom as nom_coach,u.prenom as Prenom_coach
from coachs c 
join users u on u.id=c.user_id
join seances s on c.user_id=s.coach_id
join  reservations r on s.id=r.seance_id
having  DATEDIFF(CURDATE(),r.reserved_at)<=60;

/********************
      chalenge 5
*********************/

-- Pour chaque discipline, afficher :les 3 coachs avec le plus de séances réservées inclure le nombre de réservations

select c.discipline,u.nom as nom_coach,u.prenom as prenom_coach,count(r.id) as nb_reservations
from coachs c 
join users u on u.id = c.user_id
join seances s on s.coach_id=c.user_id
join reservations r on s.id=r.seance_id
group by c.discipline,c.user_id
order by nb_reservations desc ;


/********************
      chalenge 6
*********************/

-- Lister les sportifs : qui réservent toujours moins de 24h avant la séance

select us.nom as nom_sportif,
      us.prenom as prenom_sportif,
      uc.nom as nom_coach,
      us.prenom as prenom_coach,
      count(r.id) as nb_reservations 
from reservations r 
join users us on r.sportif_id = us.id
join seances s on s.id=r.seance_id
join users uc on s.coach_id= uc.id
group by r.sportif_id,s.coach_id
having MAX( TIMESTAMPDIFF(HOUR,r.reserved_at,TIMESTAMP(s.date_seance,s.heure))) < 24;

/********************
      chalenge 7
*********************/

-- Trouver les plages horaires les plus réservées :regrouper par heure ou tranche 1h afficher nombre de séances réservées

select FLOOR(HOUR(s.heure)/1) as tranche ,count(*) as seances_reservees
from seances s 
join reservations r on s.id = r.seance_id
group by tranche
order by seances_reservees desc;

/********************
      chalenge 8
*********************/