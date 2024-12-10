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
	 * Insérer un département dans la base de données selon les attributs actuels
	 *
	 * @return string
	 */
	public function insert(): string {
		$database = $this->getDatabase();

		$request = $database->prepare('INSERT INTO ecran_departement (dept_nom) VALUES (:name)');
		$request->bindValue(':name', $this->getName());
		$request->execute();

		return $database->lastInsertId();
	}

	/**
	 * Met à jour un département de la base de données selon les attributs actuels
	 * @return int
	 */
	public function update(): int {
		$database = $this->getDatabase();

		$request = $database->prepare( 'UPDATE ecran_departement SET dept_nom = :name WHERE dept_id = :id' );

		$request->bindValue(':name', $this->getName());
		$request->bindValue(':id', $this->getIdDepartment());

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
	public function get( $id ) {
		$request = $this->getDatabase()->prepare('SELECT dept_id, dept_nom FROM ecran_departement WHERE dept_id = :id');

		$request->bindValue(':id', $id, PDO::PARAM_INT);

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
		$request = $this->getDatabase()->prepare('SELECT dept_id, dept_nom FROM ecran_departement WHERE dept_nom = :name LIMIT 1');
		$request->bindValue( ':name', $name);

		$request->execute();

		return $this->setEntity($request->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * @inheritDoc
	 */
	public function setEntity( $data ): Department {
		$entity = new Department();

		$entity->setIdDepartment( $data['dept_id'] );
		$entity->setName( $data['dept_nom'] );

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
	 * Renvoie tous les départements stockés dans la base de données
	 *
	 * @return void
	 */
	public function getAllDepts() {
		$request = $this->getDatabase()->prepare('SELECT dept_id, dept_nom FROM ecran_departement ORDER BY dept_id');

		$request->execute();

		return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
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

	public function jsonSerialize(): array {
		return get_object_vars($this);
	}
}