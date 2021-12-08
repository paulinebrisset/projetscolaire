<?php
namespace App\Models;
use App\Main\Database;
use PDO;
/*
GENIUS
Ici je met tout ce qui va me servir à manipuler les données. C'est un modèle général, je 
vais créer un modèle pour chaque table de la bdd qui contiendra 
les informations qui lui sont spécifiques.
*/

class Model extends Database{
    // Table de la base de données. Va nous permettre d'avoir un propriété qu'on va pouvoir écrire depuis les classes qui vont hériter du modèle, pour cela que l'on met protected 
    protected $table;
    // Instance de connexion
    private $db;

/*
M Méthode principale qui va préparer les requêtes dans tous les cas de figure, elle va aussi vérifier si elle doit préparer ou non la requête
O: PDOStatement|false (ce retour va être récupéré pour faire un fetchAll dessus)
I: string $sql Requête SQL à exécuter + array $attributes Attributs à ajouter à la requête 
*/
    public function executerRequete(string $sql, array $attributs = null){
        // On récupère l'instance de Database, (instance d'instance de PDO, ça aurait pu être juste instance de PDO)
        $this->db = Database::getInstance();

        // On vérifie si on a des attributs
        if($attributs !== null){
            // Si il y a des attributs envoyés, ce qu'il nous faut c'est une requête préparée
            $query = $this->db->prepare($sql);
            //exécution de la requête avec mes paramètres
            $query->execute($attributs);
            //les résultats sont récupérés dans un tableau associatif puisque c'est comme ça que c'est paramétré dans Database
            return $query;
        } else {
            // Requête simple. renvoie un booléen. findAll() passera par là
            return $this->db->query($sql);
        }
    }
/*****************ON FAIT LE CRUD *******************/
/*********************PARTIE LECTURE DES DONNEES *********************/

/* findAll
M : Sélection de tous les enregistrements d'une table, retourne un tableau 
O : Tableau des enregistrements trouvés
I: rien
*/
     
    public function findAll() {
        $query = $this->executerRequete('SELECT * FROM '.$this->table); //TODO : à vérifier 
        return $query->fetchAll();
    }

/* findBy
M : Sélection de plusieurs enregistrements suivant un tableau de critères
M: écrire des choses du style : $item = new ModelItem / $resultats = $item->findBy(['admin'=>'true'])
 va donner par ex SELECT * FROM utilisateurs WHERE admin =? AND id=2;
 & parametres (1, valeur)
O : return array Tableau des enregistrements trouvés
I: array $criteres Tableau de critères
*/
 
    public function findBy(array $criteres) {
        $champs = [];
        $valeurs = [];

        // On boucle pour récupérer les paramètres de la requête et séparer les noms de champs des valeurs
        foreach($criteres as $champ => $valeur){

            $champs[] = "$champ = ?";
            $valeurs[]= $valeur;
        }

        // On transforme le tableau "champs" en chaîne de caractères séparée par des AND
       // implode : méthode php qui rassemble les éléments d'un tableau en une chaîne
        $liste_champs = implode(' AND ', $champs);

        // On exécute la requête
        $query = $this->executerRequete("SELECT * FROM ".$this->table." WHERE ".$liste_champs.", ".$valeurs);
        return $query->fetchAll();
    }

/* find 
M : Sélection d'un enregistrement directement avec son id
O : Tableau contenant l'enregistrement trouvé
I : $id id de l'enregistrement
*/

    public function find(int $id) {
    // On exécute la requête
        $query = $this->executerRequete("SELECT * FROM ".$this->table." WHERE id = ".$id);
        return $query->fetch();
    }
/*********************PARTIE UPDATE DES DONNEES *********************/
/*
M : Mise à jour d'un enregistrement suivant un tableau de données
O : booléen (requête éxécutée ou non)
I : int $id id de l'enregistrement à modifier
I: Model $model Objet à modifier
Exemple d'utilisation
donneesDeMonItemModifie = [
    'titre'=>'Item modifié'
    'description'=>'description modifiée'
    'publie'=>true
    ] 
    $itemDejaInstancieDepuisModelItem->update(2,$memeItemDejaCree)
*/
    public function update(int $id, Model $model) {
        $champs = [];
        $valeurs = [];

        // On réorganise le tableau des paramètres pour l'exploiter
        foreach($model as $champ => $valeur){
            // UPDATE annonces SET titre = ?, description = ?, actif = ? WHERE id= ?
            if($valeur !== null && $champ != 'db' && $champ != 'table'){
                $champs[] = "$champ = ?";
                $valeurs[] = $valeur;
            }
        }
        $valeurs[] = $id;

        // On transforme le tableau "champs" en une chaine de caractères
        $liste_champs = implode(', ', $champs);

        // On exécute la requête (retour vrai ou faux)
        return $this->executerRequete('UPDATE '.$this->table.' SET '. $liste_champs.' WHERE id = ?', $valeurs);
    }

/*********************PARTIE SUPPRESSION DES DONNEES *********************/    
/*
M : Suppression d'un enregistrement
O : bool 
I : int $id id de l'enregistrement à supprimer
exemple d'utilisation $machin->delete(6);
*/
    public function delete(int $id){
        return $this->executerRequete("DELETE FROM ".$this->table." WHERE id = ?", [$id]);
    }
/*********************PARTIE HYDRATATION = CREATE*********************/  

/* 
M: Hydrater les données. Hydrater le modèle c'est passer d'un tableau 
donneesDeMonNouvelItem = [
    'titre'='Item hydraté'
    'description'=>'très bel item'
    'publie'=>true
    ] 
    de là, il va voir si setTitre, setDescription existent, et les appliquer si oui en leur filant les valeurs renseignées
I: array $donnees Tableau associatif des données
O: self Retourne l'objet hydraté
 */

    public function hydrate(array $donnees){
        foreach ($donnees as $key => $value){
            // On récupère le nom du setter correspondant à l'attribut.
            //par ex : titre donne setTitre, donc attention à l'écriture homogène des setters
            //cad retrouver le nom du setter à partir du tableau
            $method = 'set'.ucfirst($key);
            // Je vérifie si le setter correspondant existe.
            if (method_exists($this, $method)){
                // On appelle le setter.
                $this->$method($value);
            }
        }
        //return de l'objet hydraté
        return $this;
    }
}
?>