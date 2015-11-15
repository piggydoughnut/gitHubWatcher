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

	public function getSuggestion($status) {
		return $this->app->get($status);
	}

	public function processError($user) {
		$this->app->set('msg', $user['body']['message'] . ' - ' . $user['headers']['Status']);
		$code = $this->getResponseCode($user);
		$this->app->set('suggestion', $this->getSuggestion($code));
		http_response_code($code);
		echo Template::instance()->render('../views/error.htm');
		return;
	}

	public function getResponseCode($info) {
		$reponse_data = explode(' ', $info['headers'][0]);
		return $reponse_data[1];
	}
}