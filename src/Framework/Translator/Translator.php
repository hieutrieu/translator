<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 10/2/2015
 * Time: 3:03 PM
 */

namespace Framework\Translator;


/**
 * Class Config
 *
 * @package Framework
 */

class Translator
{
    private static $instance = null;
    private $language   = 'vi';
    private $lang       = [];
    private $langDir = 'resources/lang';
    private $module = 'backend';

    public static function getInstance($language = 'vi', $module = 'backend')
    {
        if (self::$instance == null) {
            self::$instance = new Translator($language, $module);
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     */
    public function __construct($language, $module)
    {
        $this->language = $language;
        $this->module = $module;
    }

    public function getLang() {
        return $this->lang;
    }


    private function findString($str) {
        if (array_key_exists($str, $this->lang[$this->language])) {
            return $this->lang[$this->language][$str];
        }
        return $str;
    }

    private function replacePlaceholder($message, $placeholders) {
        preg_match_all('/:([0-9A-Za-z_]+)/', $message, $matches);
        foreach($matches[1] as $match) {
            if(isset($placeholders[$match])) {
                $rep = $placeholders[$match];
            } else {
                $rep = '';
            }
            $message = str_replace(':'.$match, $rep, $message);
        }
        return $message;
    }

    public function get($str, $placeholders = false) {
        if (defined('__APP__')) {
            $basePath = __APP__;
        } else {
            $basePath = "";
        }
        $filePath = $basePath . '/' . $this->langDir . '/' . $this->module . '/' . $this->language . '.php';
        if (!array_key_exists($this->language, $this->lang)) {
            if (file_exists($filePath)) {
                $strings = include_once($filePath);
                $this->lang[$this->language] = $strings;
                $str = $this->findString($str);
                if(is_array($placeholders)) {
                    $str = $this->replacePlaceholder($str, $placeholders);
                }
                return $str;
            }
            else {
                return $str;
            }
        }
        else {
            $str = $this->findString($str);
            if(is_array($placeholders)) {
                $str = $this->replacePlaceholder($str, $placeholders);
            }
            return $str;
        }
    }
}
