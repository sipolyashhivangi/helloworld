<?php

/* * ********************************************************************
 * Filename: StripeController.php
 * Folder: controllers
 * Description: Stripe Related controller action class
 * @author Dan Tormey
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */
require_once(realpath(dirname(__FILE__) . '/../helpers/email/Email.php'));

class AdvisorSubscriptionController extends AdvisorController {

    function actionCheckAdvisorSubscription() {
        if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;

        $advisorSubscription = AdvisorSubscription::model()->find(array('condition' => 'advisor_id=:advisor_id', 'params' => array('advisor_id' => $advisorId)));

        $message = "";
        if (isset($advisorSubscription->processor) && $advisorSubscription->processor == "FlexScore") {
            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'FlexScore account access.')));
        }
        if ($advisorSubscription && isset($advisorSubscription->processor) && isset($advisorSubscription->currentperiodend) && isset($advisorSubscription->stripestatus)) {
            $today = new DateTime();

            $enddate = new DateTime($advisorSubscription->currentperiodend);
            $currentPeriodEnd = $enddate->add(new DateInterval('P1D'));

            if ($advisorSubscription->processor == "Stripe" && !isset($subscriptionEnd)) {
                if ($advisorSubscription->stripestatus == "trialing" ||
                    $advisorSubscription->stripestatus == "active" ||
                    $advisorSubscription->stripestatus == "canceled") {
                        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'subscription_status' => 'active',
                        'message' =>  'Your subscription is active.')));
                }
                else if ($advisorSubscription->stripestatus == "past_due") {
                    $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'subscription_status' => 'past_due',
                        'message' =>  'Your subscription is expired or has not been processed.')));
                }
            } else if ($advisorSubscription->stripestatus == "canceled") {
                    $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'subscription_status' => 'canceled',
                        'message' =>  'Your subscription has been canceled.')));
            }
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'subscription_status' => 'inactive',
            'message' =>  "There is no advisor account access.  Please complete your subscription.")));
    }

    /*
     * Create an advisor subscription based on FlexScore subscription plans.
     */

    function actionCreateAdvisorSubscription() {
        if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;

        $advisorDetails = array();
        $email = "";
        $zipcode = "";
        $plan = "";
        $planname = "";
        $description = "";
        $message = "";

        if ($advisorId) {
            $advisorDetails = Advisor::model()->find("id = :advisor_id", array("advisor_id" => $advisorId));
        }
        if ($advisorDetails) {
            $email = $advisorDetails->email;

            if (isset($_POST['zipCode'])) {
                $zipcode = $_POST['zipCode'];
                $advisorDetails->zip = $zipcode;
                $advisorDetails->save();
            }
        }

        $subscription = AdvisorSubscription::model()->find(array('condition' => "advisor_id=:advisor_id",'params' => array("advisor_id" => $advisorId)));

        if ($subscription && isset($subscription->stripesubscriptionid) && $subscription->stripesubscriptionid != "") {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'subscription_status' => 'already_exists',
                'message' => 'Our records show that you already have an active advisor subscription.  If your credit card needs to be updated in order to renew your
                FlexScore Pro Subscription, please use the <a href="javascript:window.parent.openCreditCardDialog()">update credit card form</a>.')));
        }

        if (!$subscription) {
            $subscription = new AdvisorSubscription();
            $subscription->subscriptionstart = date("Y-m-d H:i:s");
        }

        $trialEndString = "";
        $trialPeriod = false;
        $earlyBirdEndDate = new DateTime("2015-01-01 00:00:00");
        $currentPeriodEndString = "";
        $currentPeriodStartString = date("Y-m-d H:i:s");

        // Early bird subscriptions will be in effect until December 31, 2014.
        // Early bird subscriptions will receive a 60 day trial period.
        $created = new DateTime($advisorDetails->createdtimestamp);
        $today = new DateTime();
        $startTime = strtotime(date($advisorDetails->createdtimestamp));
        $trialPeriod = true;
        if ($created < $earlyBirdEndDate) {
            $plan = "advisor-earlybird";
            $planname = "FlexScore Advisor Early Bird";
            $description = "FlexScore Early Bird subscription for " . $email;
            $trialEndString = date("Y-m-d H:i:s", strtotime("+60 day", $startTime));
        } else {
            $plan = "advisor-pro";
            $planname = "FlexScore Advisor Pro";
            $description = "FlexScore Pro subscription for " . $email;
            $trialEndString = date("Y-m-d H:i:s", strtotime("+7 day", $startTime));
        }

        /***************************************************************************/
         /* Two slashes to test failed payments using two minute trial period
            $plan = "advisor-test";
            $planname = "FlexScore Advisor Testing";
            $description = "FlexScore Pro subscription for " . $email;
            $trialEndString = date("Y-m-d H:i:s", strtotime("+5 minute", $startTime));
         /*
        /***************************************************************************/

        $currentPeriodEndString = $trialEndString;
        $trialDate = new DateTime($trialEndString);
        if ($trialDate < $today) {
            $trialPeriod = false;
            $currentPeriodEndString = date("Y-m-d H:i:s", strtotime("+1 month", strtotime(date("Y-m-d H:i:s"))));
        }

        try {
            $errors = array();

            if (isset($_POST['stripeToken'])) {
                $token = $_POST['stripeToken'];

                Subscription::factory();
                if ($subscription && isset($subscription->stripecustomerid) && $subscription->stripecustomerid != "") {
                    $customer = Stripe_Customer::retrieve($subscription->stripecustomerid);
                    if ($trialPeriod == true) {
                        $stripeSubscription = $customer->subscriptions->create(array(
                                    "card" => $token,
                                    "plan" => $plan,
                                    "trial_end" => strtotime($trialEndString))
                        );
                    }
                    else {
                        $stripeSubscription = $customer->subscriptions->create(array(
                                    "card" => $token,
                                    "plan" => $plan)
                        );
                    }
                    Subscription::factory();
                    $customer = Stripe_Customer::retrieve($subscription->stripecustomerid);
                }
                else if ($trialPeriod == true) {
                    $customer = Stripe_Customer::create(array(
                                "card" => $token,
                                "plan" => $plan,
                                "trial_end" => strtotime($trialEndString),
                                "email" => $email)
                    );
                }
                else {
                    $customer = Stripe_Customer::create(array(
                                "card" => $token,
                                "plan" => $plan,
                                "email" => $email)
                    );
                }
                if ($customer) {
                    $expirationDate = date("Y-m-t H:i:s", strtotime($customer->cards->data[0]->exp_year . "-" . $customer->cards->data[0]->exp_month . "-01 23:59:59"));

                    $subscription->advisor_id = $advisorId;
                    $subscription->stripecustomerid = $customer->id;
                    $subscription->stripesubscriptionid = $customer->subscriptions->data[0]->id;
                    $subscription->stripecardid = $customer->cards->data[0]->id;
                    $subscription->cardexpirationdate = $expirationDate;
                    $subscription->planname = $planname;
                    $subscription->description = $description;
                    $subscription->subscriptionend = null;
                    $subscription->currentperiodstart = $currentPeriodStartString;
                    $subscription->currentperiodend = $currentPeriodEndString;
                    $subscription->cardlast4 = $customer->cards->data[0]->last4;
                    $subscription->cardtype = $customer->cards->data[0]->brand;
                    $subscription->processor = "Stripe";
                    $subscription->stripestatus = $customer->subscriptions->data[0]->status;
                    $subscription->modifiedtimestamp = date("Y-m-d H:i:s");
                    $subscription->save();

                    //data is valid and is successfully inserted/updated
                    //send the email for verification
                    $part = 'advisor-account-active';
                    $emailToSend = new Email();
                    $emailToSend->subject = 'Advisor Subscription Now Active';
                    $emailToSend->recipient['email'] = $email;
                    $emailToSend->recipient['name'] = "{$advisorDetails->firstname} {$advisorDetails->lastname}";
                    $emailToSend->data[$part] = [
                        'email' => $email
                    ];
                    $emailToSend->send();
                }
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => 'Thank you! Your FlexScore Pro account is now active.')));
            }
        } catch (Exception $e) {
            $message = $this->getStripeErrorMessage($e);

            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => $message)));
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'email' => $email, 'firstname' => $firstname,
                    'lastname' => $lastname, 'message' => 'Sorry, your subscription has not been processed. Please try again later.')));
    }

    /**
     * *Retrieve Credit Card Information.
     */
    function actionGetSubscription() {
        if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;

        $advisorSubscriptionObj = AdvisorSubscription::model()->find(array('condition' => "advisor_id=:advisor_id", 'params' => array("advisor_id" => $advisorId)));

        $hasSubscription = false;
        if ($advisorSubscriptionObj && $advisorSubscriptionObj->stripesubscriptionid != NULL) {
            $hasSubscription = true;
            $subscription['name'] = $advisorSubscriptionObj->planname;
            $subscription['created'] = date("D M d Y H:i:s", strtotime($advisorSubscriptionObj->subscriptionstart)) . " UTC";
            $subscription['status'] = $advisorSubscriptionObj->stripestatus;
            $subscription['start'] = date("D M d Y H:i:s", strtotime($advisorSubscriptionObj->currentperiodstart)) . " UTC";
            $subscription['end'] = date("D M d Y H:i:s", strtotime($advisorSubscriptionObj->currentperiodend)) . " UTC";
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'There are no subscriptions.')));
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'hasSubscription' => $hasSubscription, 'subscription' => $subscription)));
    }

    /**
     * *Retrieve Credit Card Information.
     */
    function actionGetCreditCard() {
        if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        $advisorEmail = Yii::app()->getSession()->get('wsadvisor')->email;

        $hasSubscription = false;
        $advisorSubscriptionObj = AdvisorSubscription::model()->find(array('condition' => "advisor_id=:advisor_id", 'params' => array("advisor_id" => $advisorId)));

        if ($advisorSubscriptionObj && $advisorSubscriptionObj->stripesubscriptionid != NULL) {
            $hasSubscription = true;
            $creditcard['email'] = $advisorEmail;
            $creditcard['type'] = $advisorSubscriptionObj->cardtype;
            $creditcard['last4'] = $advisorSubscriptionObj->cardlast4;
            $creditcard['expiration_date'] = date("F Y", strtotime($advisorSubscriptionObj->cardexpirationdate));
            $d1 = date("Y-m-d");
            $d2 = date("Y-m-d", strtotime($advisorSubscriptionObj->cardexpirationdate));
            $expirystatus = "";
            $expirydays = (strtotime($d2) - strtotime($d1)) / 86400;
            if ($expirydays >= 0 && $expirydays <= 10) {
                $expirystatus = "expiring soon!";
            } else if ($expirydays < 0) {
                $expirystatus = "expired!";
            }
            $creditcard['expiration_days'] = $expirydays;
            $creditcard['expirystatus'] = $expirystatus;
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'hasSubscription' => $hasSubscription, 'message' => 'There are no credit cards.')));
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'hasSubscription' => $hasSubscription, 'creditcard' => $creditcard)));
    }

    /**
     * * Update Customer Subscription
     */
    function actionUpdateCreditCard() {
        if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        $advisorEmail = Yii::app()->getSession()->get('wsadvisor')->email;
        $creditcard = array();
        $advisorSubscriptionObj = AdvisorSubscription::model()->find("advisor_id = :advisor_id", array("advisor_id" => $advisorId));

        try {
            if ($advisorSubscriptionObj && $advisorSubscriptionObj->stripecustomerid && $advisorSubscriptionObj->processor == 'Stripe') {

                $errors = array();

                if (isset($_POST['stripeToken'])) {
                    $token = $_POST['stripeToken'];

                    // First create the new card
                    Subscription::factory();
                    $stripeCustomerCreateCard = Stripe_Customer::retrieve($advisorSubscriptionObj->stripecustomerid);
                    if ($stripeCustomerCreateCard) {
                        $cardCreate = $stripeCustomerCreateCard->cards->create(array("card" => $token));
                        if ($cardCreate) {

                            Subscription::factory();
                            $customer = Stripe_Customer::retrieve($advisorSubscriptionObj->stripecustomerid);

                            if ($customer && isset($customer->subscriptions->data[0]->current_period_end)) {

                                $stripeCurrentPeriodEnd = new DateTime(date("Y-m-t H:i:s", $customer->subscriptions->data[0]->current_period_end));
                                $stripestatus = $customer->subscriptions->data[0]->status;
                                $expirationDate = date("Y-m-t H:i:s", strtotime($cardCreate->exp_year . "-" . $cardCreate->exp_month . "-01 23:59:59"));

                                $advisorSubscriptionObj->stripecustomerid = $customer->id;
                                $advisorSubscriptionObj->stripesubscriptionid = $customer->subscriptions->data[0]->id;
                                $advisorSubscriptionObj->stripecardid = $cardCreate->id;
                                $advisorSubscriptionObj->cardexpirationdate = $expirationDate;
                                $advisorSubscriptionObj->planname = $customer->subscriptions->data[0]->plan->name;
                                $advisorSubscriptionObj->description = "Subscription updated " . date("Y-m-d H:i:s") . ".";
                                $advisorSubscriptionObj->subscriptionend = null;
                                $advisorSubscriptionObj->currentperiodstart = date("Y-m-d H:i:s", $customer->subscriptions->data[0]->current_period_start);
                                $advisorSubscriptionObj->currentperiodend = date("Y-m-d H:i:s", $customer->subscriptions->data[0]->current_period_end);
                                $advisorSubscriptionObj->cardtype = $cardCreate->brand;
                                $advisorSubscriptionObj->cardlast4 = $cardCreate->last4;
                                $advisorSubscriptionObj->stripestatus = $customer->subscriptions->data[0]->status;
                                $advisorSubscriptionObj->modifiedtimestamp = date("Y-m-d H:i:s");
                                $advisorSubscriptionObj->save();

                                $creditcard['email'] = $advisorEmail;
                                $creditcard['type'] = $cardCreate->brand;
                                $creditcard['last4'] = $cardCreate->last4;
                                $creditcard['expiration_date'] = date("F Y", strtotime($expirationDate));

                                // After creating the new card, remove all cards except the newly created card
                                Subscription::factory();
                                $stripeCustomerDeleteCard = Stripe_Customer::retrieve($advisorSubscriptionObj->stripecustomerid);
                                $stripeCardsToDelete = CJSON::decode($stripeCustomerDeleteCard->retrieve($advisorSubscriptionObj->stripecustomerid)->cards->all());
                                if ($stripeCardsToDelete) {
                                    foreach ($stripeCardsToDelete['data'] as $key => $card) {
                                        if ($card['id'] !=  $advisorSubscriptionObj->stripecardid) {
                                            Subscription::factory();
                                            $stripeCustomerDeleteCard = Stripe_Customer::retrieve($advisorSubscriptionObj->stripecustomerid);
                                            $cardDelete = $stripeCustomerDeleteCard->cards->retrieve($card['id'])->delete();
                                        }
                                    }
                                }

                                // If the subscription is past due, get the latest invoice and if it is unpaid, pay it and update the db.
                                if ($stripestatus == 'past_due') {
                                    Subscription::factory();
                                    $latestInvoice = CJSON::decode(Stripe_Invoice::all(array(
                                        "customer" => $advisorSubscriptionObj->stripecustomerid,
                                        "limit" => 1
                                    )));
                                    if ($latestInvoice['data'][0]['paid'] == false) {
                                        Subscription::factory();
                                        $invoiceToPay = Stripe_Invoice::retrieve($latestInvoice['data'][0]['id']);
                                        $invoiceToPay->pay();

                                        $advisorPaidSubscriptionObj = AdvisorSubscription::model()->find("advisor_id = :advisor_id", array("advisor_id" => $advisorId));
                                        Subscription::factory();
                                        $customerPaid = Stripe_Customer::retrieve($advisorPaidSubscriptionObj->stripecustomerid);

                                        if ($customerPaid && isset($customerPaid->subscriptions->data[0]->current_period_end)) {

                                            $stripeCurrentPeriodEnd = new DateTime(date("Y-m-t H:i:s", $customerPaid->subscriptions->data[0]->current_period_end));
                                            $stripestatus = $customerPaid->subscriptions->data[0]->status;
                                            $stripestatus = $customerPaid->subscriptions->data[0]->status;

                                            $expirationDate = date("Y-m-t H:i:s", strtotime($customerPaid->cards->data[0]->exp_year . "-" . $customerPaid->cards->data[0]->exp_month . "-01 23:59:59"));

                                            $advisorPaidSubscriptionObj->stripecustomerid = $customerPaid->id;
                                            $advisorPaidSubscriptionObj->stripesubscriptionid = $customerPaid->subscriptions->data[0]->id;
                                            $advisorPaidSubscriptionObj->stripecardid = $customerPaid->cards->data[0]->id;
                                            $advisorPaidSubscriptionObj->planname = $customerPaid->subscriptions->data[0]->plan->name;
                                            $advisorPaidSubscriptionObj->description = "Subscription updated " . date("Y-m-d H:i:s") . ".";
                                            $advisorPaidSubscriptionObj->currentperiodstart = date("Y-m-d H:i:s", $customerPaid->subscriptions->data[0]->current_period_start);
                                            $advisorPaidSubscriptionObj->currentperiodend = date("Y-m-d H:i:s", $customerPaid->subscriptions->data[0]->current_period_end);
                                            $advisorPaidSubscriptionObj->cardexpirationdate = $expirationDate;
                                            $advisorPaidSubscriptionObj->cardlast4 = $customerPaid->cards->data[0]->last4;
                                            $advisorPaidSubscriptionObj->cardtype = $customerPaid->cards->data[0]->brand;
                                            $advisorPaidSubscriptionObj->stripestatus = $customerPaid->subscriptions->data[0]->status;
                                            $advisorPaidSubscriptionObj->modifiedtimestamp = date("Y-m-d H:i:s");
                                            $advisorPaidSubscriptionObj->subscriptionend = null;
                                            $advisorPaidSubscriptionObj->save();
                                        }
                                    }
                                }
                            }


                            $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'creditcard' => $creditcard,
                                        'message' => 'Your credit card has been updated.')));
                        }
                    }
                }
            }
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'creditcard' => $creditcard, 'message' => 'We are sorry but an error has occurred processing your credit card. Please try again.')));
        } catch (Exception $e) {
            $message = $this->getStripeErrorMessage($e);
            $creditcard['email'] = $advisorEmail;
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'creditcard' => $creditcard, 'message' => $message)));
        }
    }

    /**
     * * Retrieving a List of Invoices
     */
    function actionRetrieveInvoiceList() {
        if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        $advisorEmail = Yii::app()->getSession()->get('wsadvisor')->email;
        $advisorName = Yii::app()->getSession()->get('wsadvisor')->firstname . " " . Yii::app()->getSession()->get('wsadvisor')->lastname;

        Subscription::factory();
        $advisorSubscriptionObj = AdvisorSubscription::model()->find(array('condition' => "advisor_id=:advisor_id", 'params' => array("advisor_id" => $advisorId),
            'select' => array('stripecustomerid', 'processor')));

        $customerInvoices = array();
        $invoices = array();
        $has_more = true;
        try {
            $count = 0;
            if ($advisorSubscriptionObj && $advisorSubscriptionObj->processor == 'Stripe') {
                $customerInvoices = CJSON::decode(Stripe_Invoice::all(array(
                    "customer" => $advisorSubscriptionObj->stripecustomerid,
                    "count" => 12
                        )
                ));

                $has_more = $customerInvoices['has_more'];
                foreach ($customerInvoices['data'] as $key => $invoice) {
                    $invoices[$key]['invoice_date'] = date('D M d Y H:i:s', $invoice['date']) . " UTC";
                    $invoices[$key]['period_start'] = date('D M d Y H:i:s', $invoice['lines']['data'][0]['period']['start']) . " UTC";
                    $invoices[$key]['period_end'] = date('D M d Y H:i:s', $invoice['lines']['data'][0]['period']['end']) . " UTC";
                    $invoices[$key]['plan'] = $invoice['lines']['data'][0]['plan']['name'];
                    $invoices[$key]['amount'] = number_format($invoice['lines']['data'][0]['amount'] / 100, 2);
                    $invoices[$key]['invoice_id'] = $invoice['id'];

                    /* generate pdf for invoice */
                    $advisorSubscriptionInvoiceChk = AdvisorSubscriptionInvoice::model()->find(array('condition' => "advisor_id=:advisor_id AND stripeinvoicenumber=:stripeinvoicenumber",
                        'params' => array("advisor_id" => $advisorId, "stripeinvoicenumber" => $invoice['id'])));
                    if(count($advisorSubscriptionInvoiceChk) > 0){
                        $invoices[$key]['flexscoreinvoiceid'] = $advisorSubscriptionInvoiceChk['flexscoreinvoicenumber'];
                    }else{
                        $invoices[$key]['flexscoreinvoiceid'] = $invoice['id'];
                    }
                    //print_($invoices);

                    $plan = $invoices[$key]['plan'];
                    $period = date('F j, Y', strtotime($invoices[$key]['period_start'])) . " - " . date('F j, Y', strtotime($invoices[$key]['period_end']));
                    $invoice_date = date('F j, Y', strtotime($invoices[$key]['invoice_date']));
                    $amount = "$" . $invoices[$key]['amount'];
                    $invoice_id = $invoices[$key]['flexscoreinvoiceid'];
                    $pdflink = AdvisorSubscription::model()->generateAdvisorInvoicePdf($plan, $period, $invoice_date, $amount, $invoice_id);
                    $invoices[$key]['pdflink'] = $pdflink;
                    $count++;
                }
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'count' => $count, 'invoices' => $invoices)));
            }
        } catch (Exception $e) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'There is no payment history.')));
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'There is no payment history.')));
    }

    function actionCreateFlexScoreInvoiceList() {
        if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        Subscription::factory();

        $advisorSubscriptionObj = AdvisorSubscription::model()->find(array('condition' => "advisor_id=:advisor_id", 'params' => array("advisor_id" => $advisorId),
            'select' => array('stripecustomerid', 'processor')));

        $customerInvoices = array();
        $invoices = array();
        $has_more = true;
        try {
            $count = 0;
            if ($advisorSubscriptionObj && $advisorSubscriptionObj->processor == 'Stripe') {
                $customerInvoices = CJSON::decode(Stripe_Invoice::all(array(
                                    "customer" => $advisorSubscriptionObj->stripecustomerid,
                                    "count" => 12
                                        )
                ));
                $has_more = $customerInvoices['has_more'];
                foreach ($customerInvoices['data'] as $key => $invoice) {
                    $advisorSubscriptionInvoiceChk = AdvisorSubscriptionInvoice::model()->find(array('condition' => "advisor_id=:advisor_id AND stripeinvoicenumber=:stripeinvoicenumber",
                        'params' => array("advisor_id" => $advisorId, "stripeinvoicenumber" => $invoice['id'])));
                    if (count($advisorSubscriptionInvoiceChk) == 0) {
                        $advisorSubscriptionInvoiceObj = new AdvisorSubscriptionInvoice();
                        /* for json response only */
                        $invoices[$key]['invoice_date'] = date('D M d Y H:i:s', $invoice['date']) . " UTC";
                        $invoices[$key]['period_start'] = date('D M d Y H:i:s', $invoice['lines']['data'][0]['period']['start']) . " UTC";
                        $invoices[$key]['period_end'] = date('D M d Y H:i:s', $invoice['lines']['data'][0]['period']['end']) . " UTC";
                        $invoices[$key]['plan'] = $invoice['lines']['data'][0]['plan']['name'];
                        $invoices[$key]['amount'] = number_format($invoice['lines']['data'][0]['amount'] / 100, 2);
                        $invoices[$key]['invoice_id'] = $invoice['id'];
                        if($invoice['paid']==1){
                            $status = "Paid";
                        }else{
                            $status = "Open";
                        }
                        $invoices[$key]['status'] = $status;
                        /* generate flexscore invoice against stripeinvoice */
                        $advisorSubscriptionInvoiceObj->advisor_id = $advisorId;
                        $advisorSubscriptionInvoiceObj->flexscoreinvoicenumber = AdvisorSubscriptionInvoice::model()->generateFlexscoreInvoiceNumber();
                        $advisorSubscriptionInvoiceObj->stripeinvoicenumber = $invoice['id'];
                        $advisorSubscriptionInvoiceObj->status = $status;
                        $advisorSubscriptionInvoiceObj->invoicedate = date("Y-m-d H:i:s", $invoice['date']);
                        $advisorSubscriptionInvoiceObj->save();
                    }else{
                        /* for json response only */
                        $invoices[$key] = $advisorSubscriptionInvoiceChk;
                    }

                    $count++;
                }
                $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'count' => $count, 'invoices' => $invoices)));
            }
        } catch (Exception $e) {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'There is no payment history.')));
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'There is no payment history.')));
    }

    /**
     * Canceling a Customer's Subscription
     */
    function actionCancelAdvisorSubscription() {
        if (!isset(Yii::app()->getSession()->get('wsadvisor')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        if ($advisorId) {
            $advisorSubscription = AdvisorSubscription::model()->find(array('condition' => "advisor_id=:advisor_id",
                'params' => array("advisor_id" => $advisorId)));

            if ($advisorSubscription) {
                $advisorSubscription = new AdvisorSubscription();
                $subscriptionResponse = $advisorSubscription->CancelAdvisorSubscription($advisorId);
                if ($subscriptionResponse) {
                    $this->sendResponse(200, CJSON::encode(array('status' => $subscriptionResponse['status'], 'message' => $subscriptionResponse['message'])));
                } else {
                    $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'CSorry, there was a processing error.  Try cancelling your subscription again.')));
                }
            } else {
                $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'CSorry, there are no subscriptions to be cancelled.')));
            }
        } else {
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR', 'message' => 'CSorry, there was a processing error.  Try cancelling your subscription again.')));
        }
    }

    function createFreeSubscription($advisorObj) {
        $result = "";
        if ($advisorObj) {
            try {
                $advisorId = $advisorObj->id;
                $email = $advisorObj->email;
                $firstname = $advisorObj->firstname;
                $lastname = $advisorObj->lastname;
                $description = "advisor-free subscription for " . $email;
                $subscriptionstart = date("Y-m-d H:i:s");
                $today = new DateTime();
                $subscriptionend = $today->add(new DateInterval('P50Y'));
                $subscriptionend = $subscriptionend->format("Y-m-d H:i:s");

                $subscription = new AdvisorSubscription();
                $subscription->id = $advisorId;
                $subscription->planname = "FlexScore Free";
                $subscription->description = "Free introductory offer";
                $subscription->subscriptionstart = $subscriptionstart;
                $subscription->subscriptionend = $subscriptionend;
                $subscription->processor = 'FlexScore';
                $subscription->save();
                $result = "success";
            } catch (Exception $e) {
                $result = $e->getMessage();
            }
        }
        return $result;
    }

    function getStripeErrorMessage(Exception $e) {
        $message = "";
        if ($e instanceof Stripe_CardError) {
            // Since it's a decline, Stripe_CardError will be caught
            $body = $e->getJsonBody();
        } else if ($e instanceof Stripe_InvalidRequestError) {
            // Invalid parameters were supplied to Stripe's API
            $body = $e->getJsonBody();
        } else if ($e instanceof Stripe_AuthenticationError) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $body = $e->getJsonBody();
        } else if ($e instanceof Stripe_ApiConnectionError) {
            // Network communication with Stripe failed
            $body = $e->getJsonBody();
        } else if ($e instanceof Stripe_Error) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            $body = $e->getJsonBody();
        } else {
            // Something else happened, completely unrelated to Stripe
            $this->sendResponse(200, CJSON::encode(array('status' => 'ERROR',
                        'message' => "Sorry, your subscription has not been processed. Please try again later.")));
        }
        $err = $body['error'];
        $status = $e->getHttpStatus();
        $type = $err['type'];
        if (isset($err['code'])) {
            $code = $err['code'];
        }
        if (!isset($code)) {
            $message = "We are sorry but an error has occurred processing your credit card. Please try again.";
        } else if ($code == "card_declined") {
            $message = "We're sorry but the credit card you entered has been declined and we are unable to process payment.";
        } else if ($code == "expired_card") {
            $message = "The expiration date entered was incorrect and did not match the other information provided for your credit card.  Please try again.";
        } else if ($code == "incorrect_zip") {
            $message = "The zip code entered was incorrect and did not match the other information provided for your credit card.  Please try again.";
        } else if ($code == "invalid_cvc" || $code == "incorrect_cvc") {
            $message = "The 3-digit CVC code entered was incorrect and did not match the other information provided for your credit card. Please try again.";
        } else {
            $message = "Sorry, but an error has occurred processing your credit card. Please try again.";
        }
        return $message;
    }

    function actionRunSubscriptionUpdates() {

        $md5val = "b28159c334ecd24e2f8d17ad64407362";
        if (!isset($_GET['accTok']) || $_GET['accTok'] == '' || md5($_GET['accTok']) != $md5val) {
            header('HTTP/1.1 403 Unauthorized');
            exit;
        }
        $reportObj = array();

        $advisorSubscriptions = AdvisorSubscription::model()->findAll('processor = "Stripe" AND subscriptionend IS NULL');
        $subscriptionCount = 0;
        foreach ($advisorSubscriptions as $advisorSubscription) {
            $subscriptionCount++;

            $advisorDetails = Advisor::model()->find("id=:advisor_id", array("advisor_id" => $advisorSubscription->advisor_id));
            if ($advisorDetails) {

                if ($advisorSubscription && isset($advisorSubscription->stripecustomerid) && isset($advisorSubscription->processor) &&
                        isset($advisorSubscription->currentperiodend) && isset($advisorSubscription->stripestatus)) {

                    $today = new DateTime();
                    $currentPeriodEnd = new DateTime($advisorSubscription->currentperiodend);

                    // Check for an update on Stripe only for subscriptions that have a currentPeriodEnd in the past.
                    if ($advisorSubscription->processor == "Stripe" && $today > $currentPeriodEnd) {

                        try {
                            Subscription::factory();
                            $customer = Stripe_Customer::retrieve($advisorSubscription->stripecustomerid);

                            if ($customer && isset($customer->subscriptions->data[0]->current_period_end)) {

                                $stripeCurrentPeriodEnd = new DateTime(date("Y-m-t H:i:s", $customer->subscriptions->data[0]->current_period_end));

                                $stripestatus = $customer->subscriptions->data[0]->status;

                                if ($stripestatus == 'trialing' || $stripestatus == 'active') {
                                    $expirationDate = date("Y-m-t H:i:s", strtotime($customer->cards->data[0]->exp_year . "-" . $customer->cards->data[0]->exp_month . "-01 23:59:59"));

                                    $advisorSubscription->stripecustomerid = $customer->id;
                                    $advisorSubscription->stripesubscriptionid = $customer->subscriptions->data[0]->id;
                                    $advisorSubscription->stripecardid = $customer->cards->data[0]->id;
                                    $advisorSubscription->planname = $customer->subscriptions->data[0]->plan->name;
                                    $advisorSubscription->description = "Subscription updated " . date("Y-m-d H:i:s") . ".";
                                    $advisorSubscription->currentperiodstart = date("Y-m-d H:i:s", $customer->subscriptions->data[0]->current_period_start);
                                    $advisorSubscription->currentperiodend = date("Y-m-d H:i:s", $customer->subscriptions->data[0]->current_period_end);
                                    $advisorSubscription->cardexpirationdate = $expirationDate;
                                    $advisorSubscription->cardlast4 = $customer->cards->data[0]->last4;
                                    $advisorSubscription->cardtype = $customer->cards->data[0]->brand;
                                    $advisorSubscription->stripestatus = $customer->subscriptions->data[0]->status;
                                    $advisorSubscription->modifiedtimestamp = date("Y-m-d H:i:s");
                                    $advisorSubscription->subscriptionend = null;
                                    $advisorSubscription->save();
                                    $reportObj[] = "Active subscription updated for advisor " . $advisorSubscription->advisor_id . " on " . $advisorSubscription->modifiedtimestamp . ".\n";

                                } else if ($stripestatus == 'past_due') {
                                    $sendSubscriptionPastDueEmail = true;
                                    if (isset($advisorSubscription->cardnotauthorizedemail) && $advisorSubscription->cardnotauthorizedemail != '') {
                                        $sendSubscriptionString = date("Y-m-d H:i:s", strtotime('+20 day' . $advisorSubscription->cardnotauthorizedemail));
                                        $sendSubscriptionDate = new DateTime($sendSubscriptionString);
                                        if ($today < $sendSubscriptionDate) {
                                            $sendSubscriptionPastDueEmail = false;
                                        }
                                    }
                                    if ($sendSubscriptionPastDueEmail == true) {
                                        $part = 'advisor-credit-card-denied';
                                        $emailToSend = new Email();
                                        $emailToSend->subject = 'Subscription is Past Due';
                                        $emailToSend->recipient['email'] = $advisorDetails->email;
                                        $emailToSend->recipient['name'] = "{$advisorDetails->firstname} {$advisorDetails->lastname}";
                                        $emailToSend->data['unsubscribe'] = true;
                                        $emailToSend->data['recipient_type'] = 'ad';
                                        $emailToSend->data['unsubscribe_code'] = $advisorDetails->unsubscribecode;
                                        $emailToSend->data[$part] = [
                                            'email' => $advisorDetails->email
                                        ];
                                        $emailToSend->send();

                                        $advisorSubscription->cardnotauthorizedemail = date("Y-m-d H:i:s");
                                        $advisorSubscription->save();
                                        $reportObj[] = "Subscription past due email sent to advisor " . $advisorSubscription->advisor_id;
                                    }
                                } else if ($stripestatus == "canceled" || $stripestatus == "unpaid") {
                                    $advisorSubscription->subscriptionend = date("Y-m-d H:i:s");
                                    $advisorSubscription->description = "Subscription cancelled due to non-payment on " . date("Y-m-d H:i:s");
                                    $advisorSubscription->save();
                                    $reportObj[] = "Subscription for advisor " . $advisorSubscription->advisor_id . " canceled on " . $advisorSubscription->modifiedtimestamp . ".\n";
                                } else if ($advisorSubscription->stripestatus != $stripestatus) {
                                    $advisorSubscription->stripestatus = $stripestatus;
                                    $advisorSubscription->description = "Status changed on " . date("Y-m-d H:i:s");
                                    $advisorSubscription->modifiedtimestamp = date("Y-m-d H:i:s");
                                    $advisorSubscription->save();
                                    $reportObj[] = "Status changed for advisor " . $advisorSubscription->advisor_id . " on " . $advisorSubscription->modifiedtimestamp . ".\n";
                                }
                            }
                        } catch (Exception $e) {
                            continue;
                        }
                    }

                    if ($advisorSubscription->processor == "Stripe" && isset($advisorSubscription->advisor_id) && isset($advisorSubscription->cardexpirationdate) && !is_null($advisorSubscription->cardexpirationdate)) {
                        $sendCardExpirationEmail = true;
                        if (isset($advisorSubscription->cardexpiredemail) && $advisorSubscription->cardexpiredemail != '') {
                            $sendCardExpirationString = date("Y-m-d H:i:s", strtotime('+20 day' . $advisorSubscription->cardexpiredemail));
                            $sendCardExpirationDate = new DateTime($sendCardExpirationString);
                            if ($today < $sendCardExpirationDate) {
                                $sendCardExpirationEmail = false;
                            }
                        }
                        if ($sendCardExpirationEmail == true) {

                            $creditCardExpirationString = date("Y-m-d H:i:s", strtotime('-10 day' . $advisorSubscription->cardexpirationdate));
                            $creditCardExpirationCheck = new DateTime($creditCardExpirationString);

                            if ($today > $creditCardExpirationCheck) {
                                $part = 'advisor-credit-card-expiring';
                                $emailToSend = new Email();
                                $emailToSend->subject = 'Credit Card about to Expire';
                                $emailToSend->recipient['email'] = $advisorDetails->email;
                                $emailToSend->recipient['name'] = "{$advisorDetails->firstname} {$advisorDetails->lastname}";
                                $emailToSend->data['unsubscribe'] = true;
                                $emailToSend->data['recipient_type'] = 'ad';
                                $emailToSend->data['unsubscribe_code'] = $advisorDetails->unsubscribecode;

                                $emailToSend->data[$part] = [
                                    'email' => $advisorDetails->email
                                ];
                                $emailToSend->send();

                                $advisorSubscription->cardexpiredemail = date("Y-m-d H:i:s");
                                $advisorSubscription->save();
                                $createdDate = date('D M d Y H:i:s') . " UTC";
                                $notificationInfo = new AdvisorNotification();
                                $notificationInfo->advisor_id = $advisorDetails->id;
                                $notificationInfo->message = "Please update your credit card information.";
                                $notificationInfo->context = 'Credit Card Expiring';
                                $notificationInfo->template = 'creditcard';
                                $notificationInfo->status = 0;
                                $notificationInfo->lastmodified = date("Y-m-d H:i:s");
                                $notificationInfo->save();

                                $reportObj[] = "Credit card expiring email sent to " . $advisorSubscription->id;
                            }
                        }
                    }
                }
            }
        }
        $this->sendResponse(200, CJSON::encode(array('status' => 'OK', 'message' => $reportObj, 'subscriptionCount' => $subscriptionCount)));
    }

}

?>
