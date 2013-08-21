<?php
namespace App;

class Page extends \PHPixie\Controller {
	
    protected $name_dir;
	protected $view;

    private function nameDir()
    {
        $class =  get_called_class();
        $last_sl = strripos($class, '\\');
        $this->name_dir = substr($class, $last_sl + 1);
    }

    public function loadLang()
    {
        $this->pixie->lang->loadLang($this->name_dir);
        $lang_data = $this->pixie->lang->getAll();
        foreach ($lang_data as $key => $val) {
            foreach ($val as $key_n => $val_n) {
                $str = 'lang_'.$key.'_'.$key_n;
                $this->view->$str = $val_n;
            }
        }
    }
	
	public function before() {
        $this->nameDir();
		$this->view = $this->pixie-> view('main');
        $this->loadLang();
	}
	
    public function after() {
		$this->response->body = $this->view->render();
	}
	
}
