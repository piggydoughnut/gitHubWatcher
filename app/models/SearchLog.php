<?php

namespace Models;

use DB\SQL;
use DB\SQL\Mapper;

class SearchLog extends Mapper {

	protected $ip;
	protected $term;
	protected $created;

	// Instantiate mapper
	function __construct(SQL $db) {
		// This is where the mapper and DB structure synchronization occurs
		parent::__construct($db, 'search_logs');
	}

	/**
	 * @return mixed
	 */
	public function getIp() {
		return $this->ip;
	}

	/**
	 * @param mixed $ip
	 */
	public function setIp($ip) {
		$this->ip = $ip;
	}

	/**
	 * @return mixed
	 */
	public function getTerm() {
		return $this->term;
	}

	/**
	 * @param mixed $term
	 */
	public function setTerm($term) {
		$this->term = $term;
	}

	/**
	 * @return mixed
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param mixed $created
	 */
	public function setCreated($created) {
		$this->created = $created;
	}

}