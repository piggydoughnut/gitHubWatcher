<?php

namespace Controller;

use DB\SQL\Mapper;
use Pagination;
use Template;

class LogsController extends Controller {

	protected $model;
	const ORDER ='created DESC';

	public function __construct() {
		parent::__construct();
		$this->model = new Mapper($this->db, 'search_logs');
	}

	/**
	 * Returns index page for logs with pagination
	 */
	public function index() {
		$page = \Pagination::findCurrentPage();
		$result = $this->model->paginate($page - 1, $this->app->get('limit'), [], ['order'=> self::ORDER]);

		$pages = new Pagination($result['total'], $result['limit']);
		$pages->setTemplate('../views/pagebrowser.html');

		$this->app->set('pagebrowser', $pages->serve());
		$this->app->set('logs', $result);
		echo Template::instance()->render('../views/logs.htm');
	}

	/**
	 * Erases log entries which are older than a given amount of hours
	 */
	public function erase() {
		if ($this->app->get('SESSION.loggedIN')) {
			if (isset($_POST['hours'])) {
				$hours = htmlspecialchars($_POST['hours']);
				if (is_numeric($hours) && $hours > 0) {

					$hours_ago = time() - ($hours * 60 * 60);
					$logs = $this->model->select('id, created');

					foreach ($logs as $one) {
						$date = new \DateTime($one->created, new \DateTimeZone(date_default_timezone_get()));
						$timestamp = $date->format('U');
						if ((int)$timestamp < $hours_ago) {
							$one->erase();
						}
					}
					$this->app->set('msg', 'You deleted all log entries older than ' . $hours . ' hours');
				} else {
					$this->app->set('msg', 'You have to enter a valid numeric value !');
				}
			}
			return $this->index();
		} else {
			return $this->setErrorResponse(401);
		}
	}
}
