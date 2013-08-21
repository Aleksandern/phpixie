<?php
namespace PHPixie;

use \PHPixie\Lang\Get;

class Lang
{
    private $get;
	
	public $pixie;
	
	/**
	 * Initializes the Lang module
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 */
    public function __construct($pixie) 
    {
        $this->pixie = $pixie;
        $this->get = new Get($pixie);
    }

    public function select($uri)
    {
        $choose = new \PHPixie\Lang\Choose($this->pixie, $uri);
        $uri = $choose->select();
        return $uri;
    }

    public function __call ($method, $args)
    {
        if ($args) {
            $args = implode(',', $args);
            return $this->get->$method($args);
        }
        else return $this->get->$method();
    }

    //public function get()
    //{
        //$hz =  get_called_class();
        ////echo $hz;
        //$get = $this->get;
        //return $get;
    //}

    //public function loadLang($name)
    //{
        //$this->get->loadLang($name);
    //}

}
