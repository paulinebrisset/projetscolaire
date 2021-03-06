<?php
namespace App\Models\Table;
use App\Models\Model;

class ModelUtilisateurs extends Model{

    private $nom;
    private $mail;
    private $mdp;
    private $droit;

    /*************CONSTRUCTEUR ********************/
    public function __construct(){
        $this->table = 'utilisateurs';
        $this->id_name='utilisateur';
    }

    /**************GETTERS **********/
    public function getNom(){
        return $this->nom;
    }
    public function getMail(){
        return $this->mail;
    }
    public function getMdp(){
        return $this->mdp;
    }
    public function getDroit(){
        return $this->droit;
    }
    
    /**************SETTERS **********/

    public function setNom(string $nom){
        $this->nom = $nom;
        return $this;
    }
    public function setMail(string $mail){
        $this->nom = $mail;
        return $this;
    }
    public function setMdp(string $mdp){
        $this->nom = $mdp;
        return $this;
        //https://www.youtube.com/watch?v=4wv2V4UuA-k&list=PLBq3aRiVuwyx6B9sJip_ZU1lt7jjCwsMJ&index=6&ab_channel=NouvelleTechno a 1:47 il fait le password hash
    }
    public function setDroit(string $droit){
        $this->nom = $droit;
        return $this;
    }

}
