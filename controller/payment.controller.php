<?php
/**
 * Description of payment
 *
 * @author denganimax
 */
class payment extends TFController {
    public function StripePaymentTest() {
        require_once 'StripeLib/Stripe.php';
        
        //sk_test_4XCCsEhTT1fdjdIdpHHQhq2T
        //pk_test_4XCCfBlY7tQz5srkjxQAGqs9
        
        $apiKey = 'sk_test_4XCCsEhTT1fdjdIdpHHQhq2T';
        Stripe::setApiKey($apiKey);
        
        //Create customer
        
        $customer = Stripe_Customer::create(
            array(
              'card' => array(
                'number'    => '4242424242424242',
                'exp_month' => 5,
                'exp_year'  => date('Y') + 3,
                "cvc" => "314"
              ),
            )
        );
        $customer->email = 'abc@123.com';
        $customer->save();
        
        return "";
    }
    
    
    
    public function PaymentPlan() {
        require_once 'StripeLib/Stripe.php';
        
        $apiKey = 'sk_test_4XCCsEhTT1fdjdIdpHHQhq2T';
        Stripe::setApiKey($apiKey);
        
        $plans = Stripe_Plan::all()->__get("data");
        $first_plan_id = $plans[0]->__get("id");
        
        $cusomters = Stripe_Customer::all()->__get("data");
        $first_cusomter = $cusomters[0];
        
        
        $subscriptions = $first_cusomter->subscriptions;
        var_dump($subscriptions);
        
        $sub = $subscriptions->create(array('plan' => $first_plan_id));
        $sub->quantity = 2;
        $sub->save();
        
    }
}
