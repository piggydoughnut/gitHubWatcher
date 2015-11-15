<?php

namespace Controller;

use DB\SQL;
use DB\SQL\Mapper;
use Template;

class SearchController extends Controller {

	const GITHUB_API = 'https://api.github.com';
	const SORT = 'created';
	const DIRECTION = 'desc';

	protected $access_token;

	public function __construct() {
		parent::__construct();
		$this->access_token = $this->app->get('access_token');
	}

	public function  index() {
		echo Template::instance()->render('../views/index.htm');
	}

	/**
	 * Searches for user's public repositories in BitBucket
	 */
	public function  search() {
		if (isset($_POST['username'])) {

			$username = htmlspecialchars($_POST['username']);
			$this->logSearch($username);
			$user = $this->getUserNameInfo($username);

			if (!isset($user['login'])) {
				return $this->setErrorMessage('The user ' . $username . ' does not exist on GitHub');
			}

			$repositories = $this->makeCurlRequest(self::GITHUB_API . "/users/{$username}/repos");
			$this->app->set('repos', $repositories);
			$this->app->set('user', $user);

			echo Template::instance()->render('../views/search_results.htm');
		} else {
			echo 'The only request allowed to this endpoint is POST ';
		}
	}


	/**
	 * Retrieves a user info
	 *
	 * @param $username
	 * @return array
	 */
	public function getUserNameInfo($username) {
		return $this->makeCurlRequest(self::GITHUB_API . "/users/{$username}");
	}

	/**
	 * Creates a simple curl request
	 *
	 * @param $url
	 * @return array
	 */
	public function makeCurlRequest($url) {
		$curl = curl_init();
		curl_setopt_array($curl, [
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $url . "?access_token=" . $this->access_token . '&sort=' . self::SORT . '&direction=' . self::DIRECTION,
				CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']    // GitHub requires to set User Agent header for all requests
			]
		);
		$result = curl_exec($curl);
		curl_close($curl);
		return json_decode($result, true);
	}

	/**
	 * Logs the search details
	 * search term, date, IP address
	 * @param $username
	 */
	public function logSearch($username){
		$log = new Mapper($this->db, 'search_logs');
		$log->term = $username;
		$log->ip = $_SERVER['REMOTE_ADDR'];
		$log->created = date("Y-m-d h:i:sa");
		$log->save();
	}

	public function about(){
		echo Template::instance()->render('../views/about.htm');
	}
}