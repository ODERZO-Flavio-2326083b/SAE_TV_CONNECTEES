<?php

namespace models;

use models\Entity;
use models\Model;
use PDO;

class Localisation extends Model implements \JsonSerializable, Entity {

    /**
     * @var int
     */
    private $localisation_id;
    /**
     * @var float
     */
    private $latitude;
    /**
     * @var float
     */
    private $longitude;
    /**
     * @var string
     */
    private $adresse;
    /**
     * @var int
     */
    private $user_id;

    /**
     * Insère une localisation en fonction des attributs actuels
     *
     * @return string Le dernier id inséré
     */
    public function insert(): string {
        $database = $this->getDatabase();
        $request = $database->prepare('INSERT INTO ecran_localisation (latitude, longitude, user_id) 
                                       VALUES (:latitude, :longitude, :user_id)');
        $request->bindValue(':latitude', $this->getLatitude());
        $request->bindValue(':longitude', $this->getLongitude());
        $request->bindValue(':user_id', $this->getUserId());
        $request->execute();
        return $database->lastInsertId();
    }

    /**
     * Met à jour une localisation existante en fonction des attributs actuels
     *
     * @return int Nombre de lignes dans la table
     */
    public function update(): int {
        $database = $this->getDatabase();
        $request = $database->prepare('UPDATE ecran_localisation 
                                       SET latitude = :latitude, longitude = :longitude, adresse = :adresse, user_id = :user_id 
                                       WHERE localisation_id = :id');
        $request->bindValue(':latitude', $this->getLatitude());
        $request->bindValue(':longitude', $this->getLongitude());
        $request->bindValue(':adresse', $this->getAdresse());
        $request->bindValue(':user_id', $this->getUserId());
        $request->bindValue(':id', $this->getLocalisationId());
        $request->execute();
        return $request->rowCount();
    }

    /**
     * Supprime une localisation existante en fonction des attributs actuels
     *
     * @return int Nombre de lignes dans la table
     */
    public function delete(): int {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_localisation WHERE localisation_id = :id');
        $request->bindValue(':id', $this->getLocalisationId(), PDO::PARAM_INT);
        $request->execute();
        return $request->rowCount();
    }

    /**
     * Récupère une localisation existante en fonction d'un id
     * @param $id int id de localisation à chercher
     *
     * @return false|Localisation objet Localisation si succès, false sinon
     */
    public function get($id) {
        $request = $this->getDatabase()->prepare('SELECT localisation_id, latitude, longitude, adresse, user_id 
                                                  FROM ecran_localisation WHERE localisation_id = :id');
        $request->bindValue(':id', $id, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    /**
     * Récupère toutes les localisations et retourne une liste de toutes les localisations
     *
     * @param int $begin début de la liste
     * @param int $numberElement fin de la liste
     *
     * @return array liste d'objets Localisation
     */
    public function getList(int $begin = 0, int $numberElement = 25): array {
        $request = $this->getDatabase()->prepare('SELECT localisation_id, latitude, longitude, adresse, user_id 
                                                  FROM ecran_localisation 
                                                  LIMIT :begin, :numberElement');
        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', $numberElement, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     * À partir de données de la bd, crée un objet Localisation avec ses attributs
     * définis comme étant les informations de la bd
     *
     * @param $data mixed données de la bd
     *
     * @return Localisation l'objet créé
     */
    public function setEntity($data): Localisation {
        $entity = new Localisation();
        $entity->setLocalisationId($data['localisation_id']);
        $entity->setLatitude($data['latitude']);
        $entity->setLongitude($data['longitude']);
        $entity->setAdresse($data['adresse']);
        $entity->setUserId($data['user_id']);
        return $entity;
    }

    /**
     * Permet de créer une liste d'entités Localisation à partir
     * des résultats d'une requête SQL
     *
     * @param $dataList mixed données de la bd
     *
     * @return array liste d'objets Localisation
     */
    public function setEntityList($dataList) : array {
        $listEntity = [];
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data);
        }
        return $listEntity;
    }

    /**
     * Récupère une localisation à partir d'un id utilisateur,
     * on utilisera cette fonction pour la météo des TV.
     *
     * @param $userId int id utilisateur wordpress
     *
     * @return false|Localisation objet Localisation si donnée trouvée, sinon false
     */
    public function getLocFromUserId($userId) : false|Localisation {
        $request = $this->getDatabase()->prepare('SELECT localisation_id, latitude, longitude, adresse, user_id 
                                                  FROM ecran_localisation WHERE user_id = :id');
        $request->bindValue(':id', $userId, PDO::PARAM_INT);
        $request->execute();
        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    /**
     * @return int
     */
    public function getLocalisationId(): int {
        return $this->localisation_id;
    }

    /**
     * @param int $localisation_id
     */
    public function setLocalisationId( int $localisation_id ): void {
        $this->localisation_id = $localisation_id;
    }

    /**
     * @return float
     */
    public function getLatitude(): float {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude( float $latitude ): void {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude( float $longitude ): void {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getAdresse(): string {
        return $this->adresse;
    }

    /**
     * @param string $adresse
     */
    public function setAdresse( string $adresse ): void {
        $this->adresse = $adresse;
    }

    /**
     * @return int
     */
    public function getUserId(): int {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId( int $user_id ): void {
        $this->user_id = $user_id;
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}