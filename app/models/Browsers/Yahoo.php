<?php 
require_once dirname(__FILE__) . '/../Selenium/Base.php';
require_once dirname(__FILE__) . '/../Accounts/Yahoo.php';
require_once dirname(__FILE__) . '/../../../lib/Account.php';

use Facebook\WebDriver\WebDriverBy;

/**
 * ブラウザのYahooを扱うクラス
 */
class Models_Browser_Yahoo extends Models_Selenium_Base {

    const URL_LOGIN = 'https://login.yahoo.co.jp/config/login';
    const URL_MAIL_BOX = 'https://jp.mg5.mail.yahoo.co.jp/neo/launch';
    const PATTERN_TWITTER_AUTH = '/Twitterを使い始めるにはメールアドレスを確認してください/';

    private $email;

    public function signup() {
        $this->driver->get(self::URL_MAIL_BOX);
        $this->findElementById('jp_opt_menu')->click();
        $this->findElementById('option-mailoption')->click();
        $this->waitClickable(WebDriverBy::id('moption-safty'));
        sleep(1);
        $this->findElementById('moption-safty')->click();
        $this->waitClickable(WebDriverBy::id('add'));
        $this->findElementById('add')->click();
        // エイリアスには小文字しか設定できないので変換
        $keyword = mb_strtolower(Account::createUsername());
        $this->findElementByCssSelector('input.option-safty-width')->sendKeys($keyword);
        $this->findElementsByClass('save-button')[0]->click();
    }
    
    /**
     * ログイン
     * 
     * @return void
     */
    public function login() {
        $this->driver->get(self::URL_LOGIN);
        $yahoo = Models_Account_Yahoo::create();
        $this->findElementById('username')->sendKeys($yahoo->user_id);
        $this->findElementByName('btnNext')->click();
        $this->findElementById('passwd')->sendKeys($yahoo->password);
        $this->findElementById('btnSubmit')->click();
        $this->waitTitleContains('Yahoo! JAPAN');
    }

    /**
     * 認証コードを取得する
     * 
     * @return string
     */
    public function getAuthCode() {
        $this->driver->get(self::URL_MAIL_BOX);
        $mail_elements = $this->findElementsByClass('list-view-item');
        // 先頭はテーブルのヘッダ行なので削除する
        array_shift($mail_elements);
        // 認証メールを取得しクリックする(プレビューする)
        foreach ($mail_elements as $mail_element) {
            $mail_text = $mail_element->getText();
            if(empty($mail_text)) continue;
            preg_match(self::PATTERN_TWITTER_AUTH , $mail_text, $matches);
            if(count($matches) > 0) {
                $mail_element->click();
                sleep(2);
                break;
            }
        }
        // メール本文から認証コードを取得
        $preview = $this->findElementById('msg-preview')->getText();
        preg_match('/[0-9]{6}/', $preview, $auth_code);
        return array_shift($auth_code);
    }
}
