<?php









/**

 * Cowpay Payment Gateway for credit card method

 */

require_once(JPATH_COMPONENT_SITE .'/gateway/CowpayApi.php');

require_once(JPATH_COMPONENT_SITE .'/gateway/abstract/CowpayAbstract.php');



class CowpayCreditCard extends CowpayAbstract

{



    public $notify_url;



    // Setup our Gateway's id, description and other values

    function __construct()

    {

        parent::__construct();

    }





    /**

     * builds the credit card request params

     */

    private function create_payment_request($transaction,$transaction_id)

    {

        $merchant_ref_id = $this->get_cp_merchant_reference_id($transaction_id);

        $customer_profile_id = $this->get_cp_customer_profile_id($transaction);

        $amount = $transaction->amount; // TODO: format it like 10.00;

        $signature = $this->get_cp_signature($amount, $merchant_ref_id, $customer_profile_id);



        $request_params = array(

            // redirect user to our controller to check otp response

            'return_url' => $this->notify_url,

            'merchant_reference_id' => $merchant_ref_id,

            'customer_merchant_profile_id' => $customer_profile_id,

            'customer_name' => $transaction->name,

            'customer_email' => $transaction->email,

            'customer_mobile' => $transaction->phone,

            'amount' => $amount,

            'signature' => $signature,

            'description' => @$transaction->description?$transaction->description:"cowpay payment desc"

        );

        return $request_params;

    }



    /**

     * @inheritdoc

     */

    public function process_payment($transaction,$transaction_id)

    {


        $customer_order = $transaction;

        $request_params = $this->create_payment_request($transaction,$transaction_id);
        // $cowpay = new CowpayApi();
        $response = CowpayApi::get_instance()->charge_cc($request_params);
        $messages = $this->get_user_error_messages($response);

        if (empty($messages)) { // success

            // update order meta

            $this->set_cowpay_meta($transaction, $request_params, $response);



            // display to the admin

            if (isset($response->token) && $response->token == true) {

                // TODO: add option to use OTP plugin when return_url is not exist

                $res = array(

                    'result' => 'success',

                    'token' =>$response->token,

                    "transaction" => $response,

                    "merchant_reference_id" => $request_params['merchant_reference_id']

                );

                return $res;

            }

            // wait server-to-server notification

            //// $customer_order->payment_complete();



            // Redirect to thank you page

            return array(

                'result'   => 'fail',

                // 'redirect' => $this->get_return_url($customer_order),

            );

        } else { // error

            // update order meta

            $this->set_cowpay_meta($customer_order, $request_params);



            // display to the customer

            foreach ($messages as $m) {

                // wc_add_notice($m, "error");

            }



            // display to the admin

            $one_line_message = join(', ', $messages);

            // $customer_order->add_order_note("Error: $one_line_message");

        }

    }







    // Validate fields

    public function validate_fields()

    {

        /**

         * Return true if the form passes validation or false if it fails.

         * You can use the wc_add_notice() function if you want to add an error and display it to the user.

         * TODO: validate and display to the user useful information

         */

        return true;

    }

}

