<?php
namespace App\Controller;

class Hello extends \App\Page {

	public function action_index(){
        $lang_curr = $this->pixie->config->get('lang.curr');
        $lang_def = $this->pixie->config->get('lang.default');


        if ($lang_curr == 'ru') $lang_ch = 'eng';
        else $lang_ch = 'ru';

        $lang_msg = '<hr> Default language: '.$lang_def .' - <a href="/">main page</a>';
        $lang_msg .= '<br> The selected language: '.$lang_curr.' - <a href="/'.$lang_ch.'">change</a>';

        $lang_msg .= '<br><br>Text: <br>';
        $lang_msg .= $this->pixie->lang->get('login');
        $lang_msg .= '<br>'.$this->pixie->lang->get('auth_wrong');

        $lang_msg .= '<br><br>'.$this->pixie->lang->get('other.login');
        $lang_msg .= '<br>'.$this->pixie->lang->get('other.auth_wrong');

		$this->view->subview = 'hello';
		$this->view->message = "Have fun coding.".$lang_msg;
	}
	
}
