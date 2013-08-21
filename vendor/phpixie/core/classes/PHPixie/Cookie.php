<?php

namespace PHPixie;

/**
 */
class Cookie
{

	/**
	 * Pixie Dependancy Container
	 * @var \PHPixie\Pixie
	 */
	protected $pixie;

    private $expire;
    private $host;
	
	/**
	 * Constructs session handler
	 *
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 */
	public function __construct($pixie) {
		$this->pixie=$pixie;
    }

    public function setConfig()
    {
        $this->expire = $this->pixie->config->get("cookie.expire");
        try {
            $this->host = $this->pixie->config->get('main.host'); 
        } catch(\Exception $e) {
            $this->host =  $_SERVER['HTTP_HOST'];
        }
    }
    
	public function get($key, $place = NULL)
	{
        $this->setConfig();

        $key = $key.self::getPlace($place);
        $key_res = '';
        $key_det =  filter_has_var(INPUT_COOKIE, $key);        
        if ($key_det) $key_res = filter_input(INPUT_COOKIE, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        if (!$key_res) $key_res = '';

        return $key_res;
	}

	public function set($key, $val, $place = NULL)
	{
        $this->setConfig();

        $key = $key.self::getPlace($place);
        $expire = time()+$this->expire;        
        setcookie($key, $val, $expire, '/', '.'.$this->host, false, true);
	}

	public function remove($key, $delall = false, $place = NULL)
	{
        $this->setConfig();

        if (!$delall) $key = $key.self::getPlace($place);
        $expire = time()-$this->expire;        
        setcookie($key, '', $expire, '/', '.'.$this->host, false, true);        
	}

    public function reset()
    {
        foreach ($_COOKIE as $key => $val) {
            $this->remove($key, true);
        }
    }

    private static function getPlace($place)
    {
        if ($place) {
            $place = '_'.$place;
        }

        return $place;
    }
}
