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
 * HTML View class for the HelloWorld Component
 *
 * @since  0.0.1
 */
// require_once(JPATH_COMPONENT_SITE .'/gateway/CowpayApi.php');
require_once(JPATH_COMPONENT_SITE .'/gateway/payments/CowpayCreditCard.php');
require_once(JPATH_COMPONENT_SITE .'/gateway/payments/CowpayFawry.php');
require_once(JPATH_COMPONENT_SITE .'/gateway/payments/CowpayCashCollection.php');

use Joomla\CMS\Http\HttpFactory;

class CowpayViewAjax extends JViewLegacy
{

    public $params = [];

    function display($tpl = null)
    {
        // Assign data to the view
        $input = JFactory::getApplication()->input;
//		$input = JFactory::getApplication()->db;
        $db = JFactory::getDbo();
        $transaction = new stdClass();
        $transaction->name = $input->post->get('name');
        $transaction->email = $input->post->get('email','','String');
        $transaction->phone = $input->post->get('mobile');
        $transaction->amount = $input->post->get('amount');
        $transaction->method = $input->post->get('method');
        $transaction->address = $input->post->get('address');
        $transaction->district = $input->post->get('district');
        $transaction->city_code = $input->post->get('city_code');
        $transaction->card = $input->post->get('card');
        $transaction->desc = $input->post->get('payment_description');
        $transaction->created_at = JFactory::getDate('now')->toSql();

        $transaction->apartment = $input->post->get('apartment');
        $transaction->status = 'pending';

        $params  =JComponentHelper::getParams('com_cowpay');
        $this->params = $params->toArray();
        switch($input->post->get('method')){
            case 'card':
                $paymentMethod = new CowpayCreditCard();
                break;
            case 'fawry':
                $paymentMethod = new CowpayFawry();
                break;
            case "cash":
                $paymentMethod = new CowpayCashCollection();
                break;
            default:
                $paymentMethod = new CowpayCreditCard();
        }
        $data = $this->makeTransaction($paymentMethod,$transaction,time().rand());
        $transaction->transaction_id = @$data['merchant_reference_id'];
        $transaction->reference_id =  @$data['transaction']->cowpay_reference_id;
        // okay now we have transaction and referebce
        $db->insertObject('#__cowpay', $transaction);

        // equivalent of $app = JFactory::getApplication();
        echo new JResponseJson($data);
        return null;
    }
    public function makeTransaction($paymentMethod,$transaction,$transaction_id){
        $response = $paymentMethod->process_payment($transaction,$transaction_id);
        return $response;
    }
}