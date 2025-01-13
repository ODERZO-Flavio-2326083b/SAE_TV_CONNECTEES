<?php
/**
 * Fichier Location.php
 *
 * Ce fichier contient la classe 'Location', qui représente une localisation
 * géographique associée à un utilisateur dans l'application. Cette classe
 * permet de gérer les opérations CRUD (Créer, Lire, Mettre à jour, Supprimer)
 * sur les localisations dans la base de données. Chaque localisation est
 * liée à un utilisateur et contient des informations géographiques telles
 * que la latitude, la longitude et l'adresse.
 *
 * PHP version 8.3
 *
 * @category Entity
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  GIT: abcd1234abcd5678efgh9012ijkl3456mnop6789
 * @link     https://www.example.com/docs/Location
 * Documentation de la classe
 * @since    2025-01-07
 */
namespace models;

use models\Entity;
use models\Model;
use PDO;

/**
 * Classe Location
 *
 * Représente une localisation géographique d'un utilisateur. Permet de gérer
 * les opérations CRUD (Créer, Lire, Mettre à jour, Supprimer) sur les
 * localisations dans la base de données. Cette classe contient des informations
 * telles que la latitude, la longitude et l'adresse de la localisation d'un
 * utilisateur.
 *
 * @category Entity
 * @package  Models
 * @author   BUT Informatique, AMU <iut-aix-scol@univ-amu.fr>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  Release: 2.0.0
 * @link     https://www.example.com/docs/Location Documentation de la classe
 * @since    2025-01-07
 */
class Localisation extends Model implements \JsonSerializable, Entity
{

    /**
     * Identifiant unique de la localisation.
     *
     * @var int L'identifiant de la localisation,
     * utilisé pour référencer l'entité de localisation.
     */
    private $_localisation_id;

    /**
     * Latitude de la localisation.
     *
     * @var float La latitude géographique de la localisation.
     */
    private $_latitude;

    /**
     * Longitude de la localisation.
     *
     * @var float La longitude géographique de la localisation.
     */
    private $_longitude;

    /**
     * Adresse de la localisation.
     *
     * @var string L'adresse détaillée de la localisation,
     * généralement sous forme de chaîne de texte.
     */
    private $_adresse;

    /**
     * Identifiant de l'utilisateur associé à la localisation.
     *
     * @var int L'identifiant de
     * l'utilisateur auquel cette localisation est associée.
     */
    private $_user_id;


    /**
     * Insère une nouvelle localisation dans la base de données avec les valeurs
     * actuelles des attributs.
     *
     * Cette méthode insère une nouvelle entrée de localisation dans la base de
     * données à partir des attributs de l'objet actuel. Elle renvoie l'ID de la
     * localisation insérée.
     *
     * @return string L'ID de la localisation récemment insérée.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function insert(): string
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            'INSERT INTO ecran_localisation (latitude, longitude, user_id) 
                                       VALUES (:latitude, :longitude, :user_id)'
        );
        $request->bindValue(':latitude', $this->getLatitude());
        $request->bindValue(':longitude', $this->getLongitude());
        $request->bindValue(':user_id', $this->getUserId());
        $request->execute();
        return $database->lastInsertId();
    }

    /**
     * Met à jour une localisation existante dans la base de données en fonction des
     * attributs actuels.
     *
     * Cette méthode permet de mettre à jour une localisation déjà présente dans la
     * base de données avec les nouvelles valeurs de latitude, longitude, adresse et
     * identifiant de l'utilisateur.
     * Elle renvoie le nombre de lignes affectées par la mise à jour.
     *
     * @return int Nombre de lignes mises à jour dans la table.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function update(): int
    {
        $database = $this->getDatabase();
        $request = $database->prepare(
            'UPDATE ecran_localisation 
             SET latitude = :latitude, 
                 longitude = :longitude, 
                 adresse = :adresse, 
                 user_id = :user_id 
             WHERE localisation_id = :id'
        );
        $request->bindValue(':latitude', $this->getLatitude());
        $request->bindValue(':longitude', $this->getLongitude());
        $request->bindValue(':adresse', $this->getAdresse());
        $request->bindValue(':user_id', $this->getUserId());
        $request->bindValue(':id', $this->getLocalisationId());
        $request->execute();
        return $request->rowCount();
    }

    /**
     * Supprime une localisation de la base de données en fonction de son ID.
     *
     * Cette méthode supprime la localisation correspondant à l'ID fourni dans la
     * base de données.
     * Elle renvoie le nombre de lignes supprimées.
     *
     * @return int Nombre de lignes supprimées dans la table.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function delete(): int
    {
        $request = $this->getDatabase()->prepare(
            'DELETE FROM ecran_localisation 
             WHERE localisation_id = :id'
        );
        $request->bindValue(':id', $this->getLocalisationId(), PDO::PARAM_INT);
        $request->execute();
        return $request->rowCount();
    }

    /**
     * Récupère une localisation en fonction de son identifiant.
     *
     * Cette méthode récupère une localisation spécifique en fonction de l'ID fourni
     * et renvoie un objet 'Localisation' avec les données correspondantes.
     * Si aucune localisation n'est trouvée, la méthode retourne 'false'.
     *
     * @param int $id Identifiant de la localisation à récupérer.
     *
     * @return false|Localisation Objet Localisation si trouvé, sinon false.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function get($id)
    {
        $request = $this->getDatabase()->prepare(
            'SELECT localisation_id, latitude, longitude, adresse, user_id 
             FROM ecran_localisation 
             WHERE localisation_id = :id'
        );
        $request->bindValue(':id', $id, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    /**
     * Récupère une liste paginée de localisations à partir de la base de données.
     *
     * Cette méthode récupère une liste de localisations en fonction des paramètres
     * de pagination (début et nombre d'éléments).
     * Elle retourne une liste d'objets 'Localisation'.
     *
     * @param int $begin         Début de la
     *                           liste.
     * @param int $numberElement Nombre d'éléments à récupérer.
     *
     * @return array Liste d'objets Localisation.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getList(int $begin = 0, int $numberElement = 25): array
    {
        $request = $this->getDatabase()->prepare(
            'SELECT localisation_id, latitude, longitude, adresse, user_id 
             FROM ecran_localisation 
                 LIMIT :begin, :numberElement'
        );
        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', $numberElement, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     * Crée un objet Localisation à partir des données récupérées de la base de
     * données.
     *
     * Cette méthode prend les résultats d'une requête SQL et crée un objet
     * 'Localisation' avec les données associées à chaque attribut de la classe.
     *
     * @param mixed $data Données de la base de données.
     *
     * @return Localisation L'objet Localisation créé.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function setEntity($data): Localisation
    {
        $entity = new Localisation();
        $entity->setLocalisationId($data['localisation_id']);
        $entity->setLatitude($data['latitude']);
        $entity->setLongitude($data['longitude']);
        $entity->setAdresse($data['adresse']);
        $entity->setUserId($data['user_id']);
        return $entity;
    }

    /**
     * Crée une liste d'objets Localisation à partir des résultats d'une requête SQL.
     *
     * Cette méthode transforme une liste de données de la base de données en une
     * liste d'objets 'Localisation'.
     *
     * @param mixed $dataList Liste des données de la base de données.
     *
     * @return array Liste d'objets Localisation.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function setEntityList($dataList) : array
    {
        $listEntity = [];
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data);
        }
        return $listEntity;
    }

    /**
     * Récupère une localisation basée sur l'identifiant de l'utilisateur.
     *
     * Cette méthode permet de récupérer une localisation spécifique en fonction de
     * l'identifiant d'un utilisateur.
     * Elle est utilisée pour obtenir la localisation d'un utilisateur spécifique
     * pour des fonctionnalités comme la météo.
     *
     * @param int $userId Identifiant de l'utilisateur.
     *
     * @return false|Localisation Retourne l'objet Localisation si trouvé, sinon
     * false.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getLocFromUserId($userId) : false|Localisation
    {
        $request = $this->getDatabase()->prepare(
            'SELECT localisation_id, latitude, longitude, adresse, user_id 
             FROM ecran_localisation 
             WHERE user_id = :id'
        );
        $request->bindValue(':id', $userId, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    /**
     * Getter pour l'identifiant de la localisation.
     *
     * @return int L'identifiant de la localisation.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getLocalisationId(): int
    {
        return $this->_localisation_id;
    }

    /**
     * Setter pour l'identifiant de la localisation.
     *
     * @param int $_localisation_id L'identifiant de la localisation.
     *
     * @return void
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function setLocalisationId( int $_localisation_id ): void
    {
        $this->_localisation_id = $_localisation_id;
    }

    /**
     * Getter pour la latitude de la localisation.
     *
     * @return float La latitude de la localisation.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getLatitude(): float
    {
        return $this->_latitude;
    }

    /**
     * Setter pour la latitude de la localisation.
     *
     * @param float $_latitude La latitude de la localisation.
     *
     * @return void
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function setLatitude( float $_latitude ): void
    {
        $this->_latitude = $_latitude;
    }

    /**
     * Getter pour la longitude de la localisation.
     *
     * @return float La longitude de la localisation.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getLongitude(): float
    {
        return $this->_longitude;
    }

    /**
     * Setter pour la longitude de la localisation.
     *
     * @param float $_longitude La longitude de la localisation.
     *
     * @return void
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function setLongitude( float $_longitude ): void
    {
        $this->_longitude = $_longitude;
    }

    /**
     * Getter pour l'adresse de la localisation.
     *
     * @return string L'adresse de la localisation.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getAdresse(): string
    {
        return $this->_adresse;
    }

    /**
     * Setter pour l'adresse de la localisation.
     *
     * @param string $_adresse L'adresse de la localisation.
     *
     * @return void
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function setAdresse( string $_adresse ): void
    {
        $this->_adresse = $_adresse;
    }

    /**
     * Récupère l'ID de l'utilisateur associé à la localisation.
     *
     * Cette méthode retourne l'ID de l'utilisateur auquel la localisation est liée.
     * Elle est utilisée pour accéder à l'identifiant de l'utilisateur dans le cadre
     * de la gestion des localisations des utilisateurs.
     *
     * @return int L'ID de l'utilisateur.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function getUserId(): int
    {
        return $this->_user_id;
    }

    /**
     * Définit l'ID de l'utilisateur pour la localisation.
     *
     * Cette méthode permet d'assigner un identifiant d'utilisateur à la
     * localisation.
     * Elle est utilisée pour lier la localisation à un utilisateur spécifique.
     *
     * @param int $_user_id L'ID de l'utilisateur à associer à la localisation.
     *
     * @return void
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function setUserId( int $_user_id ): void
    {
        $this->_user_id = $_user_id;
    }

    /**
     * Sérialise l'objet Localisation en tableau pour le format JSON.
     *
     * Cette méthode permet de sérialiser l'objet Localisation en un tableau
     * associatif afin de le rendre compatible avec le format JSON. Elle est utilisée
     * lorsque l'objet doit être converti en JSON pour une API ou une réponse HTTP.
     *
     * @return array Tableau associatif des attributs de l'objet.
     *
     * @version 1.0
     * @date    07-01-2025
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
