<?php 
require_once dirname(__FILE__) . '/../Selenium/Base.php';
require_once dirname(__FILE__) . '/../Accounts/Gmail.php';
require_once dirname(__FILE__) . '/../../../lib/Account.php';

/**
 * ブラウザのGmailを扱うクラス
 */
class Models_Browser_Gmail extends Models_Selenium_Base {
    const URL_LOGIN = 'https://mail.google.com/';
    const URL_SIGNUP = 'https://accounts.google.com/signup';
    const TITLE_GMAIL_TOP = '受信トレイ';
    const TEXT_RE_SETTING_EMAIL = '再設定用のメールアドレスを確認してください';
    const TEXT_NEXT = '次へ';

    /**
     * 新規登録
     * 
     * @return void
     */
    public function signup() {
        $this->driver->get(self::URL_SIGNUP);
        $last_name = Account::getLastName();
        $first_name = Account::getFirstName();
        // gmailの場合使用されている名前が多いので2つ繋げる
        $user_name = Account::createUsername().Account::createUsername();
        $password = Account::createPassword();
        $this->findElementByName('lastName')->sendKeys($last_name);
        $this->findElementByName('firstName')->sendKeys($first_name);
        $this->findElementById('username')->sendKeys($user_name);
        $this->findElementByName('Passwd')->sendKeys($password);
        $this->findElementByName('ConfirmPasswd')->sendKeys($password);
        $result = [
            'firstname' => $first_name,
            'lastname'  => $last_name,
            'username'  => $user_name,
            'password'  => $password,
        ];
        $this->clickNext();
        return $result;
    }

    /**
     * ログインして受信トレイを表示
     * 
     * @return void
     */
    public function login() {
        $this->driver->get(self::URL_LOGIN);
        $gmail = Models_Account_Gmail::create();
        $this->findElementById('identifierId')->sendKeys($gmail->user_id);
        $this->clickNext();
        $this->findElementByName('password')->sendKeys($gmail->password);
        $this->clickNext();
        $this->waitTitleContains(self::TITLE_GMAIL_TOP);
    }

    /**
     * アカウントの認証処理をする
     *
     * 端末で一度もログインしていない場合に必要になる
     * 
     * @return void
     */
    private function auth() {
        sleep(2);
        $gmail = Models_Account_Gmail::create();
        $re_setting_email = $gmail->getReSettingEmail();
        $this->findElementByXpathText(self::TEXT_RE_SETTING_EMAIL)->click();
        $this->findElementById('identifierId')->sendKeys($re_setting_email);
        $this->clickNext();
    }

    /**
     * 「次へ」ボタンをクリック
     * 
     * @return void
     */
    private function clickNext() {
        $this->findElementByXpathText(self::TEXT_NEXT)->click();
    }
}
