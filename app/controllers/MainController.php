<?php
require_once dirname(__FILE__) .'/../models/Selenium/Webdriver.php';
require_once dirname(__FILE__) .'/../models/Browsers/Gmail.php';

class MainController {

    const GENERATE_ACCOUNT_NUM = 2;

    private $is_headless;

    function __construct($is_headless) {
        $this->is_headless = $is_headless;
    }

    public function main() {
        // ブラウザ起動
        $driver = Models_Webdriver::create($this->is_headless);
        $gmail = new Models_Browser_Gmail($driver);
        for ($i=1; $i < self::GENERATE_ACCOUNT_NUM; $i++) {
            $result = $gmail->signup();
            $this->writeLog($result);
        }
        // $driver->quit();
    }

    private function writeLog($result) {
        $log  = "\nfirstname:" . $result['firstname'];
        $log .= "\nlastname:"  . $result['lastname'];
        $log .= "\nusername:"  . $result['username'];
        $log .= "\npassword:"  . $result['password'];
        $log .= "\n\n";
        file_put_contents(PATH_GENERATED_ACCOUNT, $log, FILE_APPEND | LOCK_EX);
    }
}
