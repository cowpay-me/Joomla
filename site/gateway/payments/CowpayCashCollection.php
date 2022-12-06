<?php




/**
 * Cowpay Payment Gateway for Cash Collection method
 */
class CowpayCashCollection extends CowpayAbstract
{

    public $notify_url;

    // Setup our Gateway's id, description and other values
    function __construct()
    {
        parent::__construct();
        // get notify url for our payment.
        // when this url is entered, an action is called from WooCommerce => woocommerce_api_<class_name>

        parent::init();
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
        $city_code = $this->get_city_code($transaction);

        $request_params = array(
            // redirect user to our controller to check otp response
            'return_url' => $this->notify_url,
            'merchant_reference_id' => $merchant_ref_id,
            'customer_merchant_profile_id' => $customer_profile_id,
            'customer_name' => $transaction->name,
            'customer_email' => $transaction->email,
            'customer_mobile' => $transaction->phone,
            'address'=>$transaction->address,
            'district'=>$transaction->district,
            'apartment'=>$transaction->apartment,
            'floor'=>$transaction->floor,
            'city_code'=>$city_code,
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
        $response = CowpayApi::get_instance()->charge_cash_collection($request_params);
        $messages = $this->get_user_error_messages($response);

        if (empty($messages)) { // success
            // update order meta
            $this->set_cowpay_meta($transaction, $request_params, $response);

            // display to the admin

            if (isset($response->success) && $response->success == true) {
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

    public function get_city_code($transaction)
    {
        $avilableCountries = [
            "EGC" => "EG-01", //cairo => Downtown Cairo
            "EGGZ"=>"EG-01", //Giza & Haram	=> Giza
            "EGALX"=>"EG-02", //Downtown Alex => Downtown Alexandria
            // ""=>"EG-03", //Sahel => Sahel
            "EGBH"=>"EG-04", //Behira => Damanhour
            "EGDK"=>"EG-05", //Dakahlia => Al Mansoura
            "EGKB"=>"EG-06", //El Kalioubia	=> Sheben Alkanater
            "EGGH"=>"EG-07", //Gharbia => Tanta
            "EGKFS"=>"EG-08", //Kafr Alsheikh => Kafr Alsheikh
            "EGMNF"=>"EG-09", //Monufia => Shebin El Koom
            "EGSHR"=>"EG-10", //Sharqia => Zakazik
            "EGIS"=>"EG-11", //Isamilia => Hay 1
            "EGSUZ"=>"EG-12", //Suez => Al Suez District
            "EGPTS"=>"EG-13", //Port Said => Sharq
            "EGDT"=>"EG-14", //Damietta => Damietta
            "EGFYM"=>"EG-15", //Fayoum => Fayoum
            "EGBNS"=>"EG-16", //Bani Suif => Bani Suif
            "EGAST"=>"EG-17", //Asyut => Asyut
            "EGSHG"=>"EG-18", //Sohag => Sohag
            "EGMN"=>"EG-19", //Menya => Menya
            "EGKN"=>"EG-20", //Qena => Qena
            "EGASN"=>"EG-21", //Aswan => Aswan
            "EGLX"=>"EG-22", //Luxor => Luxor
        ];
        $avilableKeys = array_keys($avilableCountries);
        return in_array($transaction->state,$avilableKeys)?$avilableCountries[$transaction->state]:false;
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
