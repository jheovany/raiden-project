<?php // Archivo Raiden/TestModels/Person.php

namespace Raiden\TestModels;

/**
 * @table person
 */
class Person {

	/**
	 * @PK
	 * @field id
	 */
	private $id;

	/**
	 * @field firstname
	 */
	private $firstname;

	/**
	 * @field lastname
	 */
	private $lastname;

	/**
	 * @field birthday
	 */
	private $birthday;

	/**
	 * @field gender
	 */
	private $gender;

	// Getters Setters
	public function getId ( ) { return $this->id; }
	public function getFirstname ( ) { return $this->firstname; }
	public function setFirstname ( $value ) { $this->firstname = $value; }
	public function getLastname ( ) { return $this->lastname; }
	public function setLastname ( $value ) { $this->lastname = $value; }
	public function getBirthday ( ) { return $this->birthday; }
	public function setBirthday ( $value ) { $this->birthday = $value; }
	public function getGender ( ) { return $this->gender; }
	public function setGender ( $value ) { $this->gender = $value; }
}