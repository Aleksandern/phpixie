<?php
namespace PHPixie\Lang;

class Get
{
    private $pixie;
    private $lang_data;
    private $name_contr;
    private static $loaded = false;

    public function __construct($pixie)
    {
        $this->pixie = $pixie;
    }

    public function get($val)
    {
        $keys = explode('.', $val);
        if (isset($keys[1])) {
            $group = $keys[0];
            $val_get = $keys[1];
        } else {
            $group = $this->name_contr;
            $val_get = $keys[0];
        }
        $this->getGroup($group, false);

        if (isset($this->lang_data[$group][$val_get])) return $this->lang_data[$group][$val_get];
        return '';
    }

    public function getAll()
    {
        return $this->lang_data;
    }

    private function getGroup($group)
    {
        if (!isset($this->lang_data[$group])) $this->loadLang($group, false);
    }

    public function loadLang($name, $save_name = true)
    {
        $name_low = strtolower($name);
        $name = ucfirst($name_low);
        if ($save_name) $this->name_contr = $name_low;

        $dir = $this->pixie->root_dir.'classes'.DIRECTORY_SEPARATOR.'App'.DIRECTORY_SEPARATOR.'Lang'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
        $file_curr = $dir.$this->pixie->config->get('lang.curr').'.php';
        $file_def = $dir.$this->pixie->config->get('lang.default').'.php';
        if (is_file($file_curr)) {
            $this->lang_data[$name_low] = include($file_curr);
        } else if (is_file($file_def)) {
            $this->lang_data[$name_low] = include($file_def);
        } else $this->lang_data[$name_low] = Array();

        if (!is_array($this->lang_data[$name_low])) $this->lang_data[$name_low] = Array();
    }
}

