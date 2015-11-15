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

			$this->createlogEntry($username);
			$user = $this->getUserNameInfo($username);

			if (isset($user['body']['message'])) {
				return $this->processError($user);
			}
			$repositories = $this->makeCurlRequest(self::GITHUB_API . "/users/{$username}/repos");

			$this->app->set('repos', $repositories['body']);
			$this->app->set('user', $user['body']);

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
				CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],    // GitHub requires to set User Agent header for all requests
				CURLOPT_HEADER => 1
			]
		);
		$result = curl_exec($curl);
		$res = $this->processCurlOutput($result, $curl);
		curl_close($curl);
		return $res;
	}

	/**
	 * Logs the search details
	 * search term, date, IP address
	 * @param $username
	 */
	public function createlogEntry($username) {
		$log = new Mapper($this->db, 'search_logs');
		$log->term = $username;
		$log->ip = $_SERVER['REMOTE_ADDR'];
		$log->created = date("Y-m-d h:i:sa");
		$log->save();
	}

	/**
	 * Renders About page
	 */
	public function about() {
		echo Template::instance()->render('../views/about.htm');
	}

	/**
	 * Parses curl output
	 * Returns array of response headers and array of body
	 *
	 * @param $result
	 * @param $curl
	 * @return mixed
	 */
	public function processCurlOutput($result, $curl) {
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

		$header = substr($result, 0, $header_size);
		$body = substr($result, $header_size);

		$res['headers'] = $this->http_parse_headers($header);
		$res['body'] = json_decode($body, true);
		return $res;
	}

	/**
	 * Parses http headers
	 * borrowed from http://stackoverflow.com/a/21227489
	 * @param $raw
	 * @return array
	 */
	function http_parse_headers($raw) {
		$res = [];
		foreach (explode("\n", $raw) as $h) {
			$h = explode(':', $h, 2);
			$first = trim($h[0]);
			$last = trim($h[1]);
			if (array_key_exists($first, $res)) {
				$res[$first] .= ", " . $last;
			} else if (isset($h[1])) {
				$res[$first] = $last;
			} else {
				$res[] = $first;
			}
		}
		return $res;
	}
}