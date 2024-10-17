<?php

namespace Models;

use JsonSerializable;
use PDO;

/**
 * class Department
 *
 * Department entity
 *
 * @package Models
 */
class Department extends Model implements Entity, JsonSerializable {

	/**
	 * @var int
	 */
	private $id_department;
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var int
	 */
	private $longitude;
	/**
	 * @var int
	 */
	private $latitude;

	/**
	 * Insérer un département dans la base de données selon les attributs actuels
	 *
	 * @return string
	 */
	public function insert(): string {
		$database = $this->getDatabase();

		$request = $database->prepare('INSERT INTO ecran_departement (dept_nom, dept_longitude, dept_latitude) 
										VALUES (:name, :longitude, :latitude)');

		$request->bindValue(':name', $this->getName());
		$request->bindValue(':longitude', $this->getLongitude());
		$request->bindValue(':latitude', $this->getLatitude());

		$request->execute();

		return $database->lastInsertId();
	}

	/**
	 * Met à jour un département de la base de données selon les attributs actuels
	 * @return int
	 */
	public function update(): int {
		$database = $this->getDatabase();

		$request = $database->prepare(//TODO : UPDATE SQL)
		);

		$request->execute();

		return $request->rowCount();
	}

	/**
	 * Supprime un département de la base de données selon les attributs actuels
	 *
	 * @return int
	 */
	public function delete(): int {
		$request = $this->getDatabase()->prepare('DELETE FROM ecran_departement WHERE dept_id = :id');

		$request->bindValue(':id', $this->getIdDepartment(), PDO::PARAM_INT);

		$request->execute();

		return $request->rowCount();
	}

	/**
	 * Récupère le département en fonction de son id
	 * @param $id
	 *
	 * @return bool|null
	 */
	public function get( $id ): ?bool {
		$database = $this->getDatabase();

		$request = $database->prepare(// TODO GET SQL

		);

		$request->execute();

		if ( $request->rowCount() > 0 ) {
			return $this->setEntity( $request->fetch( PDO::FETCH_ASSOC ) );
		}

		return false;

	}

	public function getList( int $begin = 0, int $numberElement = 25 ): array {
		$request = $this->getDatabase()->prepare(// TODO GETLIST SQL
		);
		$request->bindValue( ':begin', $begin, PDO::PARAM_INT );
		$request->bindValue( ':numberElement', $numberElement, PDO::PARAM_INT );

		$request->execute();

		if ( $request->rowCount() > 0 ) {
			return $this->setEntityList( $request->fetchAll() );
		}

		return [];
	}

	/**
	 *
	 *
	 * @param $name
	 *
	 * @return array|mixed
	 */
	public function getDepartmentByName($name) {
		$request = $this->getDatabase()->prepare('SELECT dept_id, dept_nom, dept_longitude, dept_latitude 
														FROM ecran_departement WHERE dept_nom = :name LIMIT 2');
		$request->bindValue( ':name', $name);

		$request->execute();

		return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * @inheritDoc
	 */
	public function setEntity( $data ): Department {
		$entity = new Department();

		$entity->setIdDepartment( $data['dept_id'] );
		$entity->setName( $data['dept_nom'] );
		$entity->setLongitude( $data['dept_longitude'] );
		$entity->setLatitude( $data['dept_latitude'] );

		return $entity;
	}

	/**
	 * @inheritDoc
	 */
	public function setEntityList( $dataList, $adminSite = false ) {
		$listEntity = array();
		foreach ( $dataList as $data ) {
			$listEntity[] = $this->setEntity( $data );
		}

		return $listEntity;
	}

	/**
	 * @return int
	 */
	public function getIdDepartment(): int {
		return $this->id_department;
	}

	/**
	 * @param int $id_department
	 */
	public function setIdDepartment( int $id_department ): void {
		$this->id_department = $id_department;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( string $name ): void {
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getLongitude(): int {
		return $this->longitude;
	}

	/**
	 * @param int $longitude
	 */
	public function setLongitude( int $longitude ): void {
		$this->longitude = $longitude;
	}

	/**
	 * @return int
	 */
	public function getLatitude(): int {
		return $this->latitude;
	}

	/**
	 * @param int $latitude
	 */
	public function setLatitude( int $latitude ): void {
		$this->latitude = $latitude;
	}

	public function jsonSerialize() {
		return get_object_vars($this);
	}
}