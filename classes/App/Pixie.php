<?php

namespace App;

/**
 * Pixie dependency container
 *
 * @property-read \PHPixie\DB $db Database module
 * @property-read \PHPixie\ORM $orm ORM module
 */
class Pixie extends \PHPixie\Pixie {
	protected $modules = array(
        'cookie' => '\PHPixie\Cookie',
        'config' => '\PHPixie\Config',
        'lang' => '\PHPixie\Lang',
		'db' => '\PHPixie\DB',
		'orm' => '\PHPixie\ORM'
	);
	
	protected function after_bootstrap(){
		//Whatever code you want to run after bootstrap is done.		
        $this->config->set('main.host',  $_SERVER['HTTP_HOST']);
	}

	public function http_request()
	{
		$uri = $_SERVER['REQUEST_URI'];

        $lang = '\PHPixie\Lang';
        if (($key = array_search($lang, $this->modules)) !== false) {
            $uri = $this->$key->select($uri);
        }

		$uri = preg_replace("#^{$this->basepath}(?:index\.php/)?#i", '/', $uri);
		$url_parts = parse_url($uri);
		$route_data = $this->router->match($url_parts['path'], $_SERVER['REQUEST_METHOD']);
		return $this->request($route_data['route'], $_SERVER['REQUEST_METHOD'], $_POST, $_GET, $route_data['params'], $_SERVER);
	}
}
