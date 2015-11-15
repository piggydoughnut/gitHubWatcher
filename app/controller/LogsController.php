<?php

namespace Controller;

use DB\SQL\Mapper;
use Pagination;
use Template;

class LogsController extends Controller {

	protected $model;

	public function __construct() {
		parent::__construct();
		$this->model = new Mapper($this->db, 'search_logs');
	}

	public function index() {
		$page = \Pagination::findCurrentPage();
		$result = $this->model->paginate($page - 1, $this->app->get('limit'));

		$pages = new Pagination($result['total'], $result['limit']);
		$pages->setTemplate('../views/pagebrowser.html');

		$this->app->set('pagebrowser', $pages->serve());
		$this->app->set('logs', $result);
		echo Template::instance()->render('../views/logs.htm');
	}

	public function erase() {
		if (isset($_POST['hours'])) {
			$hours = htmlspecialchars($_POST['hours']);
			if (is_numeric($hours) && $hours > 0) {
				$hours_ago = strtotime('-' . $hours . ' hour');
				$logs = $this->model->select('id, created');
				foreach ($logs as $one) {
					if (strtotime($one->created) < $hours_ago) {
						$one->erase();
					}
				}
				$this->app->set('msg', 'You deleted all log entries older than ' . $hours . ' hours');
			} else {
				$this->app->set('msg', 'You have to enter a valid numeric value !');
			}
		}
		return $this->index();
	}
}