<?php

namespace Controller;

use Base;
use DB\SQL;
use Template;

abstract class Controller {

	protected $db;
	protected $app;
	protected $err_codes = ['400', '401', '404', '500'];

	public function __construct() {
		date_default_timezone_set('Europe/Prague');
		$this->app = Base::instance();
		$this->db = new SQL(
			$this->app->get('db'),
			$this->app->get('db_user'),
			$this->app->get('db_pass')
		);
	}

	/**
	 * Before each route checks if the user is logged in
	 */
	function beforeRoute() {
		if ($this->app->get('SESSION.user')) {
			$this->app->set('loggedIN', true);
		}
	}

	/**
	 * @param $message
	 */
	public function setErrorMessage($message) {
		$this->app->set('what', $message);
		echo Template::instance()->render('../views/not_found.htm');
		return;
	}

	/**
	 * Returns suggestins for error
	 * @param $status
	 * @return string
	 */
	public function getSuggestion($status) {
		return $this->app->get($status);
	}

	/**
	 * @param $user
	 */
	public function processError($user) {
		$this->app->set('ERROR.text', $user['body']['message'] . ' - ' . $user['headers']['Status']);
		$code = $this->getResponseCode($user);
		return $this->setErrorResponse($code);
	}

	public function setErrorResponse($code) {
		$this->app->set('suggestion', $this->getSuggestion($code));
		$this->app->set('code', $code);
		http_response_code($code);
		echo Template::instance()->render('../views/error.htm');
		return;
	}

	/**
	 * @param $info
	 * @return int
	 */
	public function getResponseCode($info) {
		$reponse_data = explode(' ', $info['headers'][0]);
		return (int)$reponse_data[1];
	}
}