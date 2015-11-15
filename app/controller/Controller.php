<?php

namespace Controller;

use Base;
use DB\SQL;
use Template;

abstract class Controller {

	protected $db;
	protected $app;

	public function __construct() {
		$this->app = Base::instance();
		$this->db = new SQL(
			$this->app->get('db'),
			$this->app->get('db_user'),
			$this->app->get('db_pass')
		);
	}

	function beforeRoute() {
		if ($this->app->get('SESSION.user')) {
			$this->app->set('loggedIN', true);
		}
	}

	public function setErrorMessage($message) {
		$this->app->set('what', $message);
		echo Template::instance()->render('../views/not_found.htm');
		return;
	}
}