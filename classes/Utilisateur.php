<?php

require_once __DIR__ . '/../config/database.php';

class Utilisateur{

    protected int $id;
    protected string $nom;
    protected string $prenom;
    protected string $email;
    protected string $password;
    protected string $role;
    protected PDO $pdo;

    // constructeur
    public function __construct($id,$nom,$prenom,$email){
        $pdo=Database::getConnection();
        $this->id = id;
        $this->email=email;
        $this->prenom=prenom;
        $this->nom=nom;
    }
   
    // getters

    public function getId(){
        return $this->id;
    }

    public function getNom(){
        return $this->nom;
    }

    public function getPrenom(){
        return $this->prenom;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getRole(){
        return $this->role;
    }

    // setters

    public function setNom($nom){
        $this->nom=$nom;
    }

    public function setPrenom($prenom){
        $this->prenom=$prenom;
    }

    public function setEmail($email){
        $this->email=$email;
    }

    public function setId($id){
        $this->id=$id;
    }

    public function setRol($role){
        $this->role=$role;
    }

    // Function Register
    public function register(string $nom,string $prenom,string $email,string $password,string $role){
        // Vérifier email déjà existant
        $check = $this->pdo->prepare("SELECT id From users where email=? ");
        $check ->execute([$email]);
        
        if ($check->rowCount() > 0) {
            return false;
        }
        // hash du mot de passe 

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insertion
        $sql = " INSERT INTO users (nom, prenom, email, password, role)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([$nom,$prenom,$email,$hashedPassword,$role]);
    }
    // fonction de login    
    public function login(string $email, string $password){

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
           return false;
        }
        return $user;
    }
}
?>
