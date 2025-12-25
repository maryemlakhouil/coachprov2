### CoachConnect
Plateforme web développée en PHP orienté objet permettant de mettre en relation des sportifs avec des coachs sportifs professionnels.

### Objectifs du projet

Mettre en relation des sportifs et des coachs

Permettre la gestion des séances sportives

Implémenter un système de réservation simple

Appliquer les principes fondamentaux de la Programmation Orientée Objet (POO) en PHP

### Fonctionnalités principales
## Authentification

Inscription (coach ou sportif)

Connexion

Déconnexion

Sécurisation des pages par rôle

## Coach

Compléter et modifier son profil

Ajouter des séances (disponibilités)

Modifier ou supprimer ses séances

Voir les réservations le concernant

Accepter ou refuser une réservation

## Sportif

Consulter la liste des coachs

Voir les séances disponibles

Réserver une séance

Consulter ses réservations

Annuler une réservation

## Structure de Projet 
coachconnect/
├── config/
│   └── database.php
├── classes/
│   ├── Utilisateur.php
│   ├── Coach.php
│   ├── Sportif.php
│   ├── Seance.php
│   └── Reservation.php
├── pages/
│   ├── login.php
│   ├── register.php
│   ├── dashboard_coach.php
│   ├── dashboard_sportif.php
│   ├── disponibilites.php
│   └── mes_reservations.php
├── public/
│   └── index.php
└── README.md
## Concepts POO utilisés

Encapsulation (propriétés privées)

Héritage (Coach et Sportif héritent de Utilisateur)

Constructeurs

Getters / Setters

Séparation des responsabilités

Utilisation de PDO pour la base de données

## Contexte pédagogique

Projet réalisé dans le cadre d’un apprentissage de la Programmation Orientée Objet en PHP, incluant :

-UML

-Héritage

-Encapsulation

-Gestion des réservations

-Respect du cahier de charge

## Auteur

Projet réalisé par *Lakhouil Maryem*
