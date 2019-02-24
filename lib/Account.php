<?php 

require_once dirname(__FILE__) . '/Define.php';
/**
 * アカウントに関する処理をするクラス
 */
class Account {

    /**
     * ランダムに名を取得する 
     * 
     * @return string
     */
    public static function getFirstName() : string {
        $firstname_examples = file(PATH_FIRSTNAME_EXAMPLES);
        $random = self::getRandomNumByRange(0, count($firstname_examples));
        return trim($firstname_examples[$random]);
    }

    /**
     * ランダムに姓を取得する 
     * 
     * @return string
     */
    public static function getLastName() : string {
        $firstname_examples = file(PATH_LASTNAME_EXAMPLES);
        $random = self::getRandomNumByRange(0, count($firstname_examples));
        return trim($firstname_examples[$random]);
    }

    /**
     * ユーザー名を生成 
     *
     * 予め用意したユーザー名辞書にランダムな4桁の数字文字列を付与したものを返す
     *
     * @return string ユーザー名 ex.) 'Tommy7652'
     */
    public static function createUsername() : string {
        $usernames = file(PATH_USERNAME_EXAMPLES);
        $index = rand(0, count($usernames));
        $suffix = self::getRandomNum();
        return trim($usernames[$index]) . $suffix;
    }

    /**
     * エイリアス付きのメールアドレスを生成
     * 
     * @param string $service_key account.jsonのサービス名キー
     * @return string
     */
    public static function createEmail($service_key) : string {
        $json = file_get_contents(PATH_ACCOUNT_JSON);
        $account = json_decode($json, true);
        $email = $account[$service_key]['user_id'];
        $exploded_email = self::explodeEmail($email);
        $host = $exploded_email['host'];
        $domain = $exploded_email['domain'];
        $alias = '+' . self::getRandomNum();
        return $host . $alias . '@' . $domain;
    }

    /**
     * パスワードを生成
     * 
     * @param int $length 文字列長
     * @return string 
     */
    public static function createPassword($length = 8) : string {
        return substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, $length);
    }

    /**
     * メールアドレスをホストとドメインに分ける
     * 
     * @param string $email
     * @return array ['host' => {host}, 'domain' => {domain}]
     */
    private static function explodeEmail($email) {
        $explode_email = explode('@', $email);
        return ['host' => $explode_email[0], 'domain' => $explode_email[1]];
    }

    /**
     * ランダムな数字文字列を取得
     * 
     * '03'のような数値文字列も取得するため、単純にランダムな数字が欲しい場合はgetRandomNumByRange()を使用すること
     * 
     * @param int $length 文字列長
     * @return string
     */
    private static function getRandomNum($length = 4) : string {
        return substr(str_shuffle('1234567890'), 0, $length);
    }

    /**
     * 指定した範囲のランダムな数字を取得
     * 
     * @param int $start 
     * @param int $end 
     * @return int
     */
    private static function getRandomNumByRange($start, $end) {
        return rand($start, $end);
    }
}
