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

		$request = $database->prepare(// TODO: INSERT SQL )
		);

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
		$database = $this->getDatabase();

		$request = $database->prepare(// TODO: DELETE SQL
		);

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
	 * @inheritDoc
	 */
	public function setEntity( $data ): Department {
		$entity = new Department();

		$entity->setIdDepartment( $data['id_department'] );
		$entity->setName( $data['name'] );
		$entity->setLongitude( $data['longitude'] );
		$entity->setLatitude( $data['latitude'] );

		return $entity;
	}

	/**
	 * @inheritDoc
	 */
	public function setEntityList( $dataList, $adminSite = false ) {
		$listEntity = array();
		foreach ( $dataList as $data ) {
			$listEntity[] = $this->setEntity( $data, $adminSite );
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