<?php
namespace PHPixie\Lang;

use \PHPixie\Request;

class Choose
{
    private $uri;
    private $list_langs;
    private $lang_set;
    private $lang_conf;
    private $request;

    const COOK_NAME = 'lang';

	public $pixie;

    public function __construct($pixie, $uri)
    {
        $this->pixie = $pixie;

        $uri = parse_url($uri);
        $uri['path'] = ltrim($uri['path'], '/');
        $uri = explode('/', $uri['path']);
        $this->uri = $uri;
        $this->list_langs = $this->getList();
        $this->request = new Request($pixie, Array(), "GET", Array(), Array(), Array(), $_SERVER);
        $this->lang_set = $this->pixie->config->get('lang.default');
        $this->lang_conf = $this->pixie->config->get('lang.default');
    }

    public function select()
    {
        $lang_cook = $this->pixie->cookie->get(self::COOK_NAME, self::COOK_NAME);
        $lang_cook = strtolower($lang_cook);        
        $uri_lang = $this->uri[0];
        $uri_lang = strtolower($uri_lang);

        // если в адресной строке нету указания языка, то ищем к кукисах
        if (empty($uri_lang)) {
            $this->checkCook ($lang_cook);
        } else {
        // если в адресной строке есть указание языка, то проверяем наличие такого языка в конфиге
            if (in_array($uri_lang, $this->list_langs)) {
                $this->set($uri_lang);
            } else {
            // если в конфиге нету языка, который указан в адресной строке
                // если кукисы пустые, то указываем язык, установленный по-умолчанию
                if (empty($lang_cook)) $this->set($this->lang_conf);
                else {
                // если кукисы не пустые, то перенаправляем на страницу указанного в кукисах языка (проверив есть ли такой язык)
                    if (($lang_cook != $this->lang_conf) && (in_array($lang_cook, $this->list_langs))) {
                         $this->redirPage($lang_cook, true);
                    } else $this->lang_set = $lang_cook;
                }
            
            }
        }

        // устанавливаем в конфигурации какой текущий язык
        $this->pixie->config->set('lang.curr', $this->lang_set);


        $uri_new = array_diff ($this->uri, $this->list_langs);
        $uri_new = array_values($uri_new);
        $uri_new = '/'.implode('/', $uri_new);

        return $uri_new;
    }
    
    private function set ($lang)
    {
        $this->pixie->cookie->set(self::COOK_NAME, $lang, self::COOK_NAME);        
        $this->lang_set = $lang;
    }

    // список языков
    private function getList () 
    {
        $list = $this->pixie->config->get('lang.list');
        $list = explode(',', $list);

        return $list;
    }

    private function checkCook ($lang_cook) 
    {
        if (empty($lang_cook)) {
            $this->set($this->lang_conf);
        } else {
            if (($lang_cook != $this->lang_conf) && (in_array($lang_cook, $this->list_langs))) {
                $this->redirPage($lang_cook);
            } else $this->lang_set = $lang_cook;
        }
    }

    private function redirPage ($lang, $save_uri= false)
    {
        $uri = '';
        $url = $this->request->url(false, true);
        if ($save_uri) $uri = '/'.implode('/', $this->uri);
        header ('Location: '.$url.$lang.$uri);
        die();
    }
}
