<?php

namespace Controller;

use Auth;
use DB\SQL\Mapper;
use Template;

class LoginController extends Controller {

	protected $auth;

	public function __construct() {
		parent::__construct();
		$this->auth = new Auth(new Mapper($this->db, 'users'), ['id' => 'username', 'pw' => 'password']);
	}

	/**
	 *
	 */
	public function login() {
		if (isset($_POST['user']) && isset($_POST['pass'])) {
			$u = htmlspecialchars($_POST['user']);
			$p = htmlspecialchars($_POST['pass']);
			if ($this->auth->login($u, md5($p))) {
				session_start();
				$this->app->set('SESSION.loggedIN', $u);
			}
		}
		echo Template::instance()->render('../views/login.htm');
		return;
	}

	/**
	 * for testing only
	 */
	public function createUser() {
		$user = new Mapper($this->db, 'users');
		$user->username = 'piggy';
		$user->password = md5('piggy');
		$user->save();
	}

	/**
	 *
	 */
	public function logout() {
		$this->app->clear('SESSION.loggedIN');
		session_commit();
		echo Template::instance()->render('../views/index.htm');
	}
}