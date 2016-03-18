<?php

/**
 * This is the model class for table "advisornotification".
 *
 */
class AdvisorSubscription extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Account the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'advisorsubscription';
    }

    /*
     * Update FlexScore database with values returned from Stripe.
     */

    function updateAdvisorSubscription($advisorId) {

        $returnMessage = array();
        $advisorSubscription = AdvisorSubscription::model()->find(array('condition' => 'advisor_id=:advisor_id', 'params' => array('advisor_id' => $advisorId)));

        if ($advisorSubscription && isset($advisorSubscription->stripecustomerid) && isset($advisorSubscription->processor) &&
                isset($advisorSubscription->currentperiodend) && isset($advisorSubscription->stripestatus)) {

            $today = new DateTime();
            $currentPeriodEnd = new DateTime($advisorSubscription->currentperiodend);
            if ($advisorSubscription->processor == "Stripe" && $today < $currentPeriodEnd && ($advisorSubscription->stripestatus == "trialing" || $advisorSubscription->stripestatus == "active")) {
                $returnMessage = array('status' => 'OK', 'message' => 'Your subscription is active.');
            } elseif ($advisorSubscription->processor == "Stripe" && ($today > $currentPeriodEnd)) {

                try {
                    Subscription::factory();
                    $customer = Stripe_Customer::retrieve($advisorSubscription->stripecustomerid);

                    if ($customer && isset($customer->subscriptions->data[0]->current_period_end)) {

                        $stripeCurrentPeriodEnd = new DateTime(date("Y-m-t H:i:s", $customer->subscriptions->data[0]->current_period_end));

                        $stripestatus = $customer->subscriptions->data[0]->status;

                        if ($today < $stripeCurrentPeriodEnd && ($stripestatus == 'trialing' || $stripestatus == 'active')) {
                            $expirationDate = date("Y-m-t H:i:s", strtotime($customer->cards->data[0]->exp_year . "-" . $customer->cards->data[0]->exp_month . "-01 23:59:59"));

                            $advisorSubscription->stripecustomerid = $customer->id;
                            $advisorSubscription->stripesubscriptionid = $customer->subscriptions->data[0]->id;
                            $advisorSubscription->stripecardid = $customer->cards->data[0]->id;
                            $advisorSubscription->cardexpirationdate = $expirationDate;
                            $advisorSubscription->planname = $customer->subscriptions->data[0]->plan->name;
                            $advisorSubscription->description = "Subscription updated " . date("Y-m-d H:i:s") . ".";
                            $advisorSubscription->subscriptionend = null;
                            $advisorSubscription->currentperiodstart = date("Y-m-d H:i:s", $customer->subscriptions->data[0]->current_period_start);
                            $advisorSubscription->currentperiodend = date("Y-m-d H:i:s", $customer->subscriptions->data[0]->current_period_end);
                            $advisorSubscription->cardlast4 = $customer->cards->data[0]->last4;
                            $advisorSubscription->cardtype = $customer->cards->data[0]->brand;
                            $advisorSubscription->stripestatus = $customer->subscriptions->data[0]->status;
                            $advisorSubscription->modifiedtimestamp = date("Y-m-d H:i:s");
                            $advisorSubscription->save();
                            $returnMessage = array('status' => 'OK', 'message' => 'Your subscription is active.');
                        } else if ($advisorSubscription->stripestatus != $customer->subscriptions->data[0]->status) {
                            $advisorSubscription->stripestatus = $customer->subscriptions->data[0]->status;
                            $advisorSubscription->modifiedtimestamp = date("Y-m-d H:i:s");
                            if ($advisorSubscription->stripestatus == "canceled") {
                                $advisorSubscription->subscriptionend = date("Y-m-d H:i:s");
                                $advisorSubscription->description = "Subscription cancelled due to non-payment on " . date("Y-m-d H:i:s");
                            }
                            $advisorSubscription->save();
                            $returnMessage = array('status' => 'ERROR', 'message' => 'Your subscription is delinquent.  Please renew your subscription.');
                        }
                    }
                } catch (Exception $e) {
                    $message = $this->getStripeErrorMessage($e);
                    $returnMessage = array('status' => 'ERROR', 'message' => $message);
                }
            }
        } else {
            $returnMessage = array("status" => "ERROR", "message" => "Not a valid advisor id");
        }
        return $returnMessage;
    }

    /**
     * Canceling a Customer's Subscription
     */
    function cancelAdvisorSubscription($advisorId) {

        $returnMessage = array();

        if ($advisorId) {

            $advisorSubscriptionObj = AdvisorSubscription::model()->find(array('condition' => "advisor_id=:advisor_id",
                'params' => array("advisor_id" => $advisorId)));

            try {
                if ($advisorSubscriptionObj && $advisorSubscriptionObj->processor == 'Stripe') {

                    Subscription::factory();
                    $stripeCustomer = Stripe_Customer::retrieve($advisorSubscriptionObj->stripecustomerid);

                    if ($stripeCustomer) {
                        $cancelSubscription = $stripeCustomer->subscriptions->retrieve($advisorSubscriptionObj->stripesubscriptionid)->cancel();
                        if ($cancelSubscription) {
                            $cardDelete = $stripeCustomer->cards->retrieve($advisorSubscriptionObj->stripecardid)->delete();
                            if ($cardDelete) {
                                $subscriptionend = date("Y-m-d H:i:s");
                                $advisorSubscriptionObj->stripesubscriptionid = NULL;
                                $advisorSubscriptionObj->stripecardid = NULL;
                                $advisorSubscriptionObj->subscriptionend = $subscriptionend;
                                $advisorSubscriptionObj->description = "Subscription cancelled on " . $subscriptionend;
                                $advisorSubscriptionObj->cardexpirationdate = NULL;
                                $advisorSubscriptionObj->cardlast4 = NULL;
                                $advisorSubscriptionObj->cardtype = NULL;
                                $advisorSubscriptionObj->stripestatus = "canceled";
                                $advisorSubscriptionObj->modifiedtimestamp = date("Y-m-d H:i:s");
                                $advisorSubscriptionObj->save();
                            }


                            $returnMessage = array('status' => 'OK', 'message' => 'Your Subscription has been cancelled and your credit card deleted.'
                                . ' You still have access until ' . date("F j, Y", strtotime($advisorSubscriptionObj->currentperiodend)) . ".");
                        }
                    }
                } else {
                    $returnMessage = array('status' => 'ERROR', 'message' => 'Sorry, there are no subscriptions to be cancelled.');
                }
            } catch (Exception $e) {
                $returnMessage = array('status' => 'ERROR', 'message' => 'Sorry, there was a processing error.  Try cancelling your subscription again.');
            }
        }
        return $returnMessage;
    }

    function generateAdvisorInvoicePdf($plan, $period, $invoice_date, $amount, $invoice_id) {
        $advisorId = Yii::app()->getSession()->get('wsadvisor')->id;
        $advisorEmail = Yii::app()->getSession()->get('wsadvisor')->email;
        $advisorName = Yii::app()->getSession()->get('wsadvisor')->firstname . " " . Yii::app()->getSession()->get('wsadvisor')->lastname;

        $pdf = new PdfCreator(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Invoice Summary Report");
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", "");
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetMargins(PDF_MARGIN_LEFT, 24, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->AddPage();
        $html = '<table width="100%">';
        $html .= '<tr>';
        $html .= '<td width="50%">&nbsp;&nbsp;156 2nd Street<br/>&nbsp;&nbsp;San Francisco, CA USA 94105</td>';
        $html .= '<td width="50%" align="right" valign="bottom"><b style="font-size:16px">Invoice</b></td>';
        $html .= '</tr>';
        $html .= '<tr><td width="50%">&nbsp;</td><td align="right" width="50%"><table width="100%" cellpadding="0" cellspacing="0" align="right"><tr><td style="height:2px;" colspan="2">&nbsp;</td></tr><tr><td align="right" width="60%">Date:</td><td width="40%">' . $invoice_date . '</td></tr><tr><td colspan="2" style="height:2px;">&nbsp;</td></tr><tr><td align="right">Invoice Number:</td><td>' . $invoice_id . '</td></tr></table></td></tr>';
        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';
        $html .= '<tr><td style="background-color:#D7CAF6;border-bottom: 1px solid #906EE7;"><table cellpadding="0" cellspacing="5" width="95%"><tr><td align="left"><b>Bill To</b></td></tr></table></td><td></td></tr>';
        $html .= '<tr><td><table cellpadding="0" cellspacing="5"><tr><td>&nbsp;&nbsp;Name:&nbsp;' . $advisorName . '</td></tr>';
        $html .= '<tr><td>&nbsp;&nbsp;Email:&nbsp;&nbsp;' . $advisorEmail . '</td></tr>';
        $html .= '</table></td><td></td></tr>';
        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';
        $html .= '<tr><td colspan="2" style="background-color:#D7CAF6;border-bottom: 1px solid #906EE7;"><table align="center" cellpadding="0" cellspacing="5" width="100%"><tr><td align="left" width="35%"><b>Subscription Plan</b></td><td align="left" width="35%"><b>&nbsp;&nbsp;Period</b></td><td align="right" width="30%"><b>Amount</b></td></tr></table></td></tr>';
        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';
        $html .= '<tr><td colspan="2"><table align="center" cellpadding="0" cellspacing="0" width="100%"><tr><td align="left" width="35%"><b>&nbsp;&nbsp;' . $plan . '</b></td><td align="left" width="35%"><b>&nbsp;&nbsp;&nbsp;' . $period . '</b></td><td align="right" style="border-bottom: 1px solid #AEC3A0" width="30%"><b>' . $amount . '</b><br/></td></tr></table></td></tr>';
        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';
        $html .= '<tr><td colspan="2"><table align="center" cellpadding="0" cellspacing="0" width="100%"><tr><td align="left" colspan="2" width="70%"><b>&nbsp;</b></td><td align="right" style="border-bottom: 1px solid #AEC3A0" width="30%"><table width="100%"><tr><td align="left"><b>Total</b></td><td align="right"><b>' . $amount . '</b><br/></td></tr></table></td></tr></table></td></tr>';
        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';
        $html .= '<tr><td colspan="2"><table align="center" cellpadding="0" cellspacing="0" width="100%"><tr><td align="left" colspan="2" width="70%"><b>&nbsp;</b></td><td align="right" style="border-bottom: 1px solid #AEC3A0" width="30%"><table width="100%"><tr><td align="left"><b>Paid</b></td><td align="right"><b>' . $amount . '</b><br/></td></tr></table></td></tr></table></td></tr>';
        $html .= '<tr><td colspan="2">&nbsp;</td></tr>';
        $html .= '</table>';

        $pdf->writeHTML($html, true, 0, true, true);
        // reset pointer to the last page
        $pdf->lastPage();
        //Close and output PDF document
        // create the hash for advisorid //
        $hasher = PasswordHasher::factory();
        $advisorHashObj = Advisor::model()->find(array('condition' => "id = :advisor_id", 'params' => array("advisor_id" => $advisorId), 'select' => 'advidhashvalue'));
        if ($advisorHashObj) {
            if($advisorHashObj->advidhashvalue=="") {
                $advisorHashObj->advidhashvalue = str_replace("/","",$hasher->HashPassword($advisorId));
                Advisor::model()->updateByPk($advisorId, array('advidhashvalue' => $advisorHashObj->advidhashvalue));
                $advidhashvalue = $advisorHashObj->advidhashvalue;
            } else {
                $advidhashvalue = $advisorHashObj->advidhashvalue;
            }
        }
        unset($hasher);
        $folderPath = realpath(dirname(__FILE__) . '/../..');
        if (is_dir($folderPath . '/ui/usercontent/advisor/' . $advisorId . '/')) {
            rename($folderPath . '/ui/usercontent/advisor/' . $advisorId, $folderPath . '/ui/usercontent/advisor/' . $advidhashvalue);
        } else if (!is_dir($folderPath . '/ui/usercontent/advisor/' . $advisorId . '/') && !is_dir($folderPath . '/ui/usercontent/advisor/' . $advidhashvalue . '/')) {
            mkdir($folderPath . '/ui/usercontent/advisor/' . $advidhashvalue . '/');
        }
        $filename = 'FlexScore_Invoice_'.$invoice_id.'.pdf';
        $pdflink = './ui/usercontent/advisor/' . $advidhashvalue . '/' . $filename;
        $pdf->Output(('../ui/usercontent/advisor/' . $advidhashvalue . '/' . $filename), 'F');
        //Close and output PDF document
        $file_path = 'ui/usercontent/advisor/' . $advidhashvalue . '/' . $filename;
        $pdflink = $file_path;
        return $pdflink;
    }

}

?>