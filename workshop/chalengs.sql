
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

SELECT u.nom, u.prenom, (COUNT(s.id) / count(s.id) * 100) AS taux
    FROM seances s
    JOIN coachs c ON s.coach_id = c.user_id
    JOIN users u ON c.user_id = u.id
    GROUP BY u.nom, u.prenom;

-- afficher seulement les coachs ayant ≥3 séances

 select  u.nom, u.prenom,count(s.id) as total_seances_Reservee
    from seances s 
    join reservations r on  s.id=seance_id
    group by s.coach_id
    having total_seances_Reservee>=3;
    SELECT 
    u.nom, u.prenom,
    COUNT(s.id) AS total_seances,
    COUNT(r.id) AS seances_reservees,
    ROUND(COUNT(r.id)/COUNT(s.id)*100,2) AS taux_reservation
FROM coachs c
JOIN users u ON u.id = c.user_id
JOIN seances s ON s.coach_id = c.user_id
LEFT JOIN reservations r ON r.seance_id = s.id
GROUP BY c.user_id
HAVING COUNT(s.id) >= 3;
