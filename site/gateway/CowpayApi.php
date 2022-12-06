<?php

/**

 * @package     Joomla.Administrator

 * @subpackage  com_helloworld

 *

 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.

 * @license     GNU General Public License version 2 or later; see LICENSE.txt

 */

// No direct access to this file

defined('_JEXEC') or die('Restricted access');

/**

 * Hello World Component Controller

 *

 * @since  0.0.1

 */

use Joomla\CMS\Http\HttpFactory;





class CowpayApi {

    private static $instance = null;

    protected $production_host = 'cowpay.me';

    protected $staging_host = 'staging.cowpay.me';

    protected $endpoint_charge_fawry = 'api/v2/charge/fawry';

    protected $endpoint_charge_cc = 'api/v2/charge/card/init';

    protected $endpoint_install_valu_enquiry = 'api/v2/installment/valu/enquiry';

    protected $endpoint_install_valu_verify = 'api/v2/installment/valu/verify';

    protected $endpoint_install_valu_purchase = 'api/v2/installment/valu/purchase';

    protected $endpoint_charge_cash_collection = 'api/v2/charge/cash-collection';

    protected $endpoint_load_iframe_token = 'api/v2/iframe/token';

    protected $endpoint_checkout_url = 'api/v2/iframe/load';

    protected $settings = [];



    private function __construct()

    {

        // self::$instance = new CowpayApi();

        $this->settings = JComponentHelper::getParams('com_cowpay')->toArray();

    }

    public static function get_instance()

    {

        if (self::$instance == null) {

            self::$instance = new CowpayApi();

        }

        return self::$instance;

    }

    /**

     * Make fawry charge request

     * @param array $fawry_params fawry request params to send

     * @link https://docs.cowpay.me/api/fawry

     */

    public function charge_fawry($fawry_params)

    {

        $url = $this->make_url($this->endpoint_charge_fawry);

        $auth_token = $this->settings['merchant_token'];

        return $this->cowpayCurl($url, $fawry_params, $auth_token);

    }



    /**

     * Make card charge request

     * @param array $cc_params credit card request params to send

     * @link https://docs.cowpay.me/api/card

     */

    public function charge_cc($cc_params)

    {


        $url = $this->make_url($this->endpoint_charge_cc);

        $auth_token = $this->settings['merchant_token'];

        return $this->cowpayCurl($url, $cc_params, $auth_token);

    }



    /**

     * Make card charge request

     * @param array $cc_params credit card request params to send

     * @link https://docs.cowpay.me/api/card

     */

    public function charge_cash_collection($cash_collection_params)

    {

        $url = $this->make_url($this->endpoint_charge_cash_collection);

        $auth_token = $this->settings['merchant_token'];

        return $this->cowpayCurl($url, $cash_collection_params, $auth_token);
    }





    /**

     * Returns checkout url of cowpay servers

     * @param string $token token generated from load_iframe_token.

     * @param string $referer_url when checkout process is done, cowpay redirect user to this url

     */

    public function get_checkout_url($token, $referer_url)

    {

        return $this->make_url($this->endpoint_checkout_url . "/$token?referer=$referer_url");

    }



    /**

     * Returns cowpay host depending on selected environment in admin settings

     */

    public function get_active_host()

    {

        return $this->settings['is_live'] == 1 ? $this->production_host : $this->staging_host;

    }



    /**

     * return url from endpoint path, take into

     * account http/https and production/staging selection

     */

    public function make_url($path)

    {

        $host = $this->get_active_host();

        $schema =  "https";

        $url = "$schema://$host/$path";

        return $url;

    }

    public function cowpayCurl($url, $cc_params,$auth_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($cc_params),
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Authorization: Bearer ".$auth_token,
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }
}

