<?php
/**
 * Connexion à la base de données (PDO)
 */
class Database{

    private static ?PDO $pdo = null;

    // Paramètres de connexion
    private const HOST = "localhost";
    private const DB   = "coachsport";
    private const USER = "root";
    private const PASS = "";

    /**
     * Retourne une instance PDO unique
    */
    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . self::HOST .
                       ";dbname=" . self::DB .
                       ";charset=utf8";

                self::$pdo = new PDO(
                    $dsn,
                    self::USER,
                    self::PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );

            } catch (PDOException $e) {
                // Gestion simple des erreurs
                die("Erreur de connexion à la base de données.");
            }
        }

        return self::$pdo;
    }

    // Empêcher l'instanciation
    private function __construct() {}
}

?>

