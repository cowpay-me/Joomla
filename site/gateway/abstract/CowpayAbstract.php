<?php



/**

 * Base class for all cowpay gateways (methods)

 */

abstract class CowpayAbstract

{

    public $require_ssl = true;

    public $cp_admin_settings;



    function __construct()

    {

        $this->cp_admin_settings = JComponentHelper::getParams('com_cowpay');
    }



    /**

     * This function should be called from the concrete class after all fields initialization.

     */

    public function init() //TODO: rename this function

    {

        // This basically defines your settings which are then loaded with init_settings()



        // After init_settings() is called, you can get the settings and load them into variables, e.g:

        // $this->title = $this->get_option( 'title' );

    }



    public function handle_order_fail($response, $order)

    {

        // Transaction was not successful

        // Add notice to the cart

        // wc_add_notice($response->status_description, 'error');

        // Add note to the order for your reference

        // $order->add_order_note('Error:  Failure');

        //$customer_order->update_status("wc-cancelled",'The order was failed','cowpay');

        //echo "<pre>"; print_r($result);

    }



    function guidv4($data = null) {

        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.

        $data = $data ?$data: random_bytes(16);

        assert(strlen($data) == 16);



        // Set version to 0100

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);

        // Set bits 6-7 to 10

        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);



        // Output the 36 character UUID.

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

    }

    /**

     * Calculate and return the merchant reference id for cowpay(cp)

     * from the given order.

     * Note: this function generates random uuids. two consecutive calls will generate

     * different reference id.

     */

    public function get_cp_merchant_reference_id( $transaction_id)

    {

        return $transaction_id . '-' . $this->guidv4();

    }



    /**

     * Calculate and return the customer profile id for cowpay(cp)

     * from the given order

     */

    public function get_cp_customer_profile_id( $transaction)

    {

        $billing_name = $transaction->name;

        $guest_id = str_replace(" ", "-", "GUEST-$billing_name");

        return strval($guest_id); // id should be str

    }





    /**

     * Store cowpay related needed values to the customer order as meta. information granted from request and response.

     * These information is used to retrieve the order in the server notification.

     * @param  $order current processed order

     * @param array $req_params req params, should contains merchant_reference_id

     * @param Object? $response object if exists

     * @return void

     */

    public function set_cowpay_meta($order, $req_params, $response = null)

    {

        //* if meta key starts with underscore '_' character, it will be private

        //* and will not be shown in the order information in the dashboard.



        // $setOrderMeta = function ($k, $v) use ($order) {

        //     update_post_meta($order->get_id(), $k, $v);

        //     // don't use this right now, as it doesn't update in the database.

        //     //// $order->add_meta_data($k, $v);

        // };

        // $setOrderMeta("cp_merchant_reference_id", $req_params['merchant_reference_id']);

        // $setOrderMeta("cp_customer_merchant_profile_id", $req_params['customer_merchant_profile_id']);



        // if ($response == null) return;

        // // response meta

        // $setOrderMeta("cp_cowpay_reference_id", $response->cowpay_reference_id);

        // $is_3ds = true;

        // $setOrderMeta("cp_is_3ds", $is_3ds);

        // if (isset($response->payment_gateway_reference_id)) {

        //     $setOrderMeta("cp_payment_gateway_reference_id", $response->payment_gateway_reference_id);

        // }elseif (is_array($response) && @isset($response['payment_gateway_reference_id'])) {

        //     $setOrderMeta("cp_payment_gateway_reference_id", $response['payment_gateway_reference_id']);

        // }





        // TODO: do_action('cowpay_meta_after_update')

    }



    /**

     * parse an error and returns readable helpful user message

     * @param WP_Error|Object $maybe_error object that may contains error

     * @return string[] user error messages

     */

    public function get_user_error_messages($maybe_error)

    {

        $return_messages = array();

        if (

            isset($maybe_error->status_code)

            && isset($maybe_error->status_description)

            && $maybe_error->status_code != 200

            || !$maybe_error->success

        ) { // extract errors from the server response

            $errors = $maybe_error->errors;



            if (isset($errors)) {

                if (!is_array($errors))

                    $errors = get_object_vars($errors);

                $return_messages = array_values($errors);

            } else {

                // if we can't find detailed errors, return status description as the error

                $return_messages[] = $maybe_error->status_description;

            }

        } else if (!isset($maybe_error->status_code) && $maybe_error->responseDesc != "SUCCESS") { // server should return it in the response

            $return_messages[] = "Unexpected Cowpay response";

        }

        return $return_messages;

    }



    /**

     * Creates the request signature from dynamic params sent and admin settings (merchant code, merchant hash)

     */

    public function get_cp_signature($amount, $merchant_reference_id, $customer_profile_id)

    {

        $message = join("", array(

            $this->cp_admin_settings['merchant_code'],

            $merchant_reference_id,

            $customer_profile_id,

            $amount,

            $this->cp_admin_settings['merchant_hash_key']

        ));

        return hash('sha256', $message);

    }

}

