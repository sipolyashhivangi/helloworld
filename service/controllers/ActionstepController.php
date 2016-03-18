<?php

/* * ********************************************************************
 * Filename: ActionstepController.php
 * Folder: controllers
 * Description: Actionstep controller (points, steps, more)
 * @author Alex Thomas (For TruGlobal Inc)
 * Reviewed By Ganesh Manoharan(For TruGlobal Inc)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class ActionstepController extends Scontroller {

    public $connectAccountManual = 1;             //done
    public $increaseLifeInsurance = 2;            //done
    public $getLifeInsurance = 3;                 //done
    public $lifeInsuranceVideo = 4;               //done
    public $disabilityInsuranceVideo = 5;         //done
    public $diversifyInvestmentsForRisk = 6;
    public $completeRiskTolerance = 7;            //done
    public $investmentDiversificationVideo = 8;   //done
    public $reviewBeneficiary = 10;               //done
    public $addIraRothOrTraditional = 12;         // Deleted as of March 6th 2014.
    public $updateWillAndEstatePlanning = 14;     //done
    public $addGoal = 15;                         //done
    public $setGoal = 16;                         //done
    public $mortgageDebt = 17;                    //done
    public $debtImprovementOptions = 18;          //done
    public $knowledgeDebtsAndLiabilities = 19;    //done
    public $createEmergencyFund = 20;             //done
    public $connectAccountAuto = 21;              //done
    public $increaseSavings = 22;                 //done
    public $reviewCreditScore = 23;               //done
    public $inflationVideo = 24;                  //done
    public $moreAsset = 25;                       //done
    public $moreDebt = 26;                        //done
    public $fillMiscTax = 27;                     //done
    public $fillMiscEstatePlanning = 28;          //done
    public $moreInsurance = 29;                   //done
    public $moreWillAndTrust = 30;                //done
    public $detailedIncome = 31;                  //done
    public $detailedExpense = 32;                 //done
    public $increaseDisabilityInsurance = 35;     //done
    public $getDisabilityInsurance = 36;          //done
    public $reviewRiskTolerance = 38;             //done
    public $noncorrelatedAltInvestment = 41;      //done
    public $addAutoRebalance = 42;                //done
    public $maximizeTraditionalIra = 43;
    public $estatePlanning = 46;                  //done
    public $decreaseW4TaxWithholding = 47;
    public $taxPlanningVideo = 48;                //done
    public $maximizeRothIra = 49;
    public $savingsAccount = 50;
    public $retirementFundingAccount = 51;
    public $consolidateLoans = 52;
    public $refinanceConsumerDebts = 53;          //done
    public $evaluateConsumerDebtCosts = 54;       //done
    public $evaluateHousingCosts = 55;            //done
    public $improveCreditScore = 57;              //done
    public $setupGoal = 58;                       //done
    public $budgetingAndCashFlowVideo = 59;       //done
    public $highReturnsVideo = 62;     //done
    public $propertyAndCasualtyInsVideo = 63;     //done
    public $healthMedicalInsuranceArticle = 64;   //done
    public $umbrellaInsurance = 65;
    public $homeOwnersOrRentersInsurance = 66;
    public $propertyInsurance = 67;
    public $businessOwnersInsurance = 68;
    public $professionalLiabilityInsurance = 69;
    public $flexibilityOfAssets = 70;
    public $considerConcentrationOfAssets = 71;
    public $pensionEligibility = 73;              //done
    public $considerLifeexpectancyRisk = 74;      //done
    public $examineLifestyleCost = 75;            //done
    public $createInformationalSheet = 77;        //done
    public $considerCharitableDonations = 79;     //done
    public $refinanceCreditCard = 85;             //done
    public $healthInsurance = 86;                 //done
    public $reviewHealthInsurance = 87;           //done
    public $reviewLifeInsurance = 88;             //done
    public $reviewDisabilityInsurance = 89;       //done
    public $learningArticles = 90;                //done
    public $increaseW4TaxWithholding = 91;
    public $refinanceCarLoan = 92;                //done
    public $payOffDebts = 93;                     //done
    public $retirementGoalPlan = 94;
    public $estatePlanningVideo = 95;             //done
    public $actionNew = '0';
    public $actionCompleted = '1';
    public $actionViewed = '2';
    public $actionStarted = '3';
    public $actionHistory = '4';  //When any action done through Action Step
    public $actionDeleted = '5';
    public $iraAmount = 400;
    public $acGrossIncome = 10000;
    public $acGrossExpense = 5000;
    public $assetsTotal = 10000;
    public $debtsTotal = 5000;
    public $acSpouseIncome = 5000;
    public $acSpouseAge = 0;
    public $acSpouseRetAge = 0;
    public $rcFirstGoalEntryCheck = true;
    public $rcSumofAssests = 5000;
    public $rcRetirementGoalAmt = 800000; // default value for age 30
    public $rcRetirementAge = 65;
    public $iraMax = 5500;
    public $over50IraMax = 6500;
    public $crMax = 17500;
    public $over50CrMax = 23000;

    public function accessRules() {
        return array_merge(
                array(array('allow', 'users' => array('?'))),
                // Include parent access rules
                parent::accessRules()
        );
    }

    /*
     * This function is called by cron job on every 1 second.
     * This will run through each and every predefined conditions and create/update actionstep table.
     */

    function actionSteps($accTok = '', $user_id = '', $sendResponse = true) {
        $md5hashval = "b28159c334ecd24e2f8d17ad64407362";
        if (!isset($_GET['accTok']) || $_GET['accTok'] == '' || md5($_GET['accTok']) != $md5hashval) {
            if (!isset($accTok) || $accTok == '' || md5($accTok) != $md5hashval) {
                header('HTTP/1.1 403 Unauthorized');
                exit;
            }
        }

        if (isset($_GET['uid']) && $_GET['uid'] <> '') {
            $userid = $_GET['uid'];
        } else if (isset($user_id) && $user_id <> '') {
            $userid = $user_id;
        } else {
            $userid = 0;
        }
        $userObj = new User();
        $qu = new CDbCriteria();
        // Used to call specific users action step while sign up or login.
        if ($userid > 0) {
            $qu->condition = "isactive = '1' AND id = :user_id";
            $qu->params = array('user_id' => $userid);
            // Calling every 1 second by cron job.
        } else {
            $qu->condition = "isactive = '1' AND lastaccesstimestamp > NOW() - INTERVAL 15 MINUTE";
        }
        $userDetails = $userObj->findAll($qu);
        if (isset($userDetails) && !empty($userDetails)) {
            $queryuser = new UserScore();
            $queryaction = new Actionstep();

            foreach ($userDetails as $urow) {
                if (isset($urow->id) <> '') {
                    $this->CreateActionSteps($urow->id);

                    $this->StatusandRemove($urow->id);

                    $this->UpdateActionPoints($urow->id);

                    $actionObj = new ActionStep();
                    $qu = new CDbCriteria();
                    $qu->condition = "user_id = :user_id";
                    $qu->params = array('user_id' => $urow->id);
                    $qu->select = 'max(lastmodifiedtime) as maxmodifiedtime';
                    $actionSteps = $actionObj->find($qu);
                    $lastmodifiedtime = null;
                    if ($actionSteps) {
                        $lastmodifiedtime = $actionSteps->maxmodifiedtime;
                    }
                    Yii::app()->cache->set('actionstep' . $urow->id, $lastmodifiedtime);
                }
            }
            unset($userObj, $queryuser, $queryaction, $userDetails);
        }

        // Fetching Actionstep Points to Update
        if ($sendResponse) {
            if (isset($_GET['uid']) && $_GET['uid'] <> '') {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK", "update" => "Points")));
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "OK")));
            }
        }
    }

    /**
     * Update action steps points with live points
     * @return status
     */
    function UpdateActionPoints($userid) {
        try {
            if (isset($userid)) {
// Fetching data from SE as JSON
                parent::setSimulationEngine($userid, true);
                $currentScore = $this->sengine->updateScore();

                $queryaction = new Actionstep();
                $checkqry = $queryaction->findAll("user_id=:user_id AND (actionstatus IN ('0', '2') or actionid in (:actionid,:debt,:housing,:health,:credit,:noncor,:lerisk,:elifesylecost,:flexibility))", array("user_id" => $userid, "actionid" => $this->learningArticles, "debt" => $this->evaluateConsumerDebtCosts, "housing" => $this->evaluateHousingCosts, "health" => $this->healthMedicalInsuranceArticle, "credit" => $this->improveCreditScore, "noncor" => $this->noncorrelatedAltInvestment, "lerisk" => $this->considerLifeexpectancyRisk, "elifesylecost" => $this->examineLifestyleCost, "flexibility" => $this->flexibilityOfAssets));
                if ($checkqry) {
                    foreach ($checkqry as $vals) {
                        parent::setSimulationEngine($userid, false);
                        $point = $vals->points;

                        switch ($vals->actionid) {
                            case $this->connectAccountManual:
                                $originalFlag1 = $this->sengine->isUserDownloadAccount;
                                $originalFlag2 = $this->sengine->isUserEnteredAccount;
                                $originalFlag3 = $this->sengine->userProfilePoints_connectAccount;
                                $this->sengine->isUserDownloadAccount = true;
                                $this->sengine->isUserEnteredAccount = true;
                                $this->sengine->userProfilePoints_connectAccount = 1;
                                $increasepoints = parent::simulateCalculateScore("CONNECTACCOUNT", $userid);
                                $this->sengine->isUserDownloadAccount = $originalFlag1;
                                $this->sengine->isUserEnteredAccount = $originalFlag2;
                                $this->sengine->userProfilePoints_connectAccount = $originalFlag3;
                                $point = $increasepoints;
                                break;
                            case $this->increaseLifeInsurance:
                                $maxPoint29 = $this->sengine->maxPoint29;
                                if ($this->sengine->insuranceReviewYear29 > 0) {
                                    $currentYear = date('Y');
                                    $yearDifference = $currentYear - $this->sengine->insuranceReviewYear29;

                                    if ($yearDifference > 4 && $maxPoint29 > -24) {
                                        $maxPoint29 = -24;
                                    } elseif ($yearDifference > 3 && $yearDifference <= 4 && $maxPoint29 > -12) {
                                        $maxPoint29 = -12;
                                    } elseif ($yearDifference > 2 && $yearDifference <= 3 && $maxPoint29 > 0) {
                                        $maxPoint29 = 0;
                                    } elseif ($yearDifference > 1 && $yearDifference <= 2 && $maxPoint29 > 12) {
                                        $maxPoint29 = 12;
                                    } elseif ($yearDifference <= 1 && $maxPoint29 > 24) {
                                        $maxPoint29 = 24;
                                    }
                                }

                                $tempPoint = $maxPoint29 - $this->sengine->wfPoint29;
                                $tempPoint = ($tempPoint > 0) ? $tempPoint : 0;
                                $point = $currentScore + $tempPoint;
                                break;
                            case $this->getLifeInsurance:
                                $point = $currentScore + $this->sengine->maxPoint29;
                                break;
                            case $this->lifeInsuranceVideo:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            case $this->disabilityInsuranceVideo:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            //AS63//
                            case $this->highReturnsVideo:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            case $this->propertyAndCasualtyInsVideo:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            case $this->completeRiskTolerance:
                                $defaultRiskValue = $this->sengine->userRiskValue;
                                $this->sengine->userRiskValue = 5;
                                $increasepoints = parent::simulateCalculateScore("COMPLETERISKTOLERANCE", $userid);
                                $this->sengine->userRiskValue = $defaultRiskValue;
                                $point = $increasepoints;
                                break;
                            //AS24//
                            case $this->inflationVideo:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            //AS95 - Video Estate Planning//
                            case $this->estatePlanningVideo:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            //AS48 - Tax Planning Planning//
                            case $this->taxPlanningVideo:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            case $this->investmentDiversificationVideo:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            case $this->budgetingAndCashFlowVideo:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            case $this->reviewBeneficiary:
                                $originalFlag = $this->sengine->beneAssigned;
                                $this->sengine->beneAssigned = true;
                                $increasepoints = parent::simulateCalculateScore("REVIEWBENEFICIARY", $userid);
                                $this->sengine->beneAssigned = $originalFlag;
                                $point = $increasepoints;
                                break;
                            /*                            case $this->addIraRothOrTraditional:
                              $originalFlag1 = $this->sengine->investmentFactor;
                              $originalFlag2 = $this->sengine->point26cond;
                              $originalFlag3 = $this->sengine->retirementMonthlyContribution;
                              $originalFlag4 = $this->sengine->taxDeferredAnnualSavings;
                              $this->sengine->investmentFactor = 1;
                              $this->sengine->point26cond = 1;
                              $this->sengine->retirementMonthlyContribution = true;
                              $this->sengine->taxDeferredAnnualSavings = $this->sengine->taxDeferredAnnualSavings + 12 * 400;
                              $increasepoints = parent::simulateCalculateScore("RA_ASSET", $userid);
                              $this->sengine->investmentFactor = $originalFlag1;
                              $this->sengine->point26cond = $originalFlag2;
                              $this->sengine->retirementMonthlyContribution = $originalFlag3;
                              $this->sengine->taxDeferredAnnualSavings = $originalFlag4;
                              $point = $increasepoints;
                              break;
                             */
                            case $this->updateWillAndEstatePlanning:
                                $originalFlag = $this->sengine->reviewYearP40;
                                $this->sengine->reviewYearP40 = date('Y');
                                $increasepoints = parent::simulateCalculateScore("UPDATEWILLANDESTATEPLANNING", $userid);
                                $this->sengine->reviewYearP40 = $originalFlag;
                                $point = $increasepoints;
                                break;
                            case $this->addGoal:
                                $originalFlag = $this->sengine->firstGoalEntryCheck;
                                $this->sengine->firstGoalEntryCheck = $this->rcFirstGoalEntryCheck;
                                $increasepoints = parent::simulateCalculateScore("ADDGOAL", $userid);
                                $this->sengine->firstGoalEntryCheck = $originalFlag;
                                $point = $increasepoints;
                                break;
                            case $this->setGoal:
                                $originalFlag = $this->sengine->firstGoalEntryCheck;
                                $this->sengine->firstGoalEntryCheck = $this->rcFirstGoalEntryCheck;
                                $increasepoints = parent::simulateCalculateScore("SETGOAL", $userid);
                                $this->sengine->firstGoalEntryCheck = $originalFlag;
                                $point = $increasepoints;
                                break;
                            case $this->noncorrelatedAltInvestment:
                                $originalFlag = $this->sengine->mediaCount;
                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->noncorrelatedAltInvestment), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                $totalCount = count($articleIds);

                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag;
                                $point = $increasepoints;
                                if (count($articleIds) == 0) {
                                    $point = $currentScore + $totalCount * 5;
                                }
                                break;
                            case $this->mortgageDebt:
                                $originalFlag = $this->sengine->mortgageInfo;
                                $this->sengine->mortgageInfo = true;
                                $increasepoints = parent::simulateCalculateScore("MORTGAGEDEBT", $userid);
                                $point = $increasepoints;
                                $this->sengine->mortgageInfo = $originalFlag;
                                break;
                            case $this->debtImprovementOptions:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            case $this->knowledgeDebtsAndLiabilities:
                                $originalFlag2 = $this->sengine->mediaCount;
                                $this->sengine->mediaCount++;
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag2;
                                $point = $increasepoints;
                                break;
                            case $this->createEmergencyFund:
                                $originalFlag1 = $this->sengine->userSumOfAssets;
                                $originalFlag2 = $this->sengine->isUserEnteredAccount;
                                $originalFlag4 = $this->sengine->userSumOfGoalSettingAssets;
                                $originalFlag5 = $this->sengine->numeratorP14;
                                $originalFlag6 = $this->sengine->userProfilePoints_assets;

                                $assetData = parent::getAssetData($userid);
                                $assets = $assetData[0];
                                $insurance = $assetData[1];
                                $pertrack = $assetData[2];
                                $tickers = $assetData[3];

                                $amount = round($this->sengine->userExpensePerMonth * 3 - $this->sengine->userSumOfGoalSettingAssets, -2);
                                if ($amount < 0) {
                                    $amount = 0;
                                }

                                if ($assets == 0) {
                                    $assets = array();
                                }
                                $valueObj = new stdClass();
                                $valueObj->id = 0;
                                $valueObj->balance = $amount;
                                $valueObj->invpos = '';
                                $valueObj->type = "BANK";
                                $assets[count($assets)] = $valueObj;

                                $this->sengine->userSumOfAssets = $this->sengine->userSumOfAssets + $amount;
                                $this->sengine->userSumOfGoalSettingAssets = $this->sengine->userSumOfGoalSettingAssets + $amount;
                                $this->sengine->numeratorP14 = $this->sengine->numeratorP14 + $amount;
                                $this->sengine->isUserEnteredAccount = true;

                                parent::CalculatePoint10($assets, $insurance, $pertrack);
                                $increasepoints = parent::simulateCalculateScore("RA_ASSET", $userid);
                                $point = $increasepoints;

                                $this->sengine->userSumOfAssets = $originalFlag1;
                                $this->sengine->isUserEnteredAccount = $originalFlag2;
                                $this->sengine->userSumOfGoalSettingAssets = $originalFlag4;
                                $this->sengine->numeratorP14 = $originalFlag5;
                                $this->sengine->userProfilePoints_assets = $originalFlag6;
                                break;
                            case $this->connectAccountAuto:
                                $originalFlag1 = $this->sengine->isUserDownloadAccount;
                                $originalFlag2 = $this->sengine->isUserEnteredAccount;
                                $originalFlag3 = $this->sengine->userProfilePoints_connectAccount;
                                $this->sengine->isUserDownloadAccount = true;
                                $this->sengine->isUserEnteredAccount = true;
                                $this->sengine->userProfilePoints_connectAccount = 1;
                                $increasepoints = parent::simulateCalculateScore("CONNECTACCOUNT", $userid);
                                $this->sengine->isUserDownloadAccount = $originalFlag1;
                                $this->sengine->isUserEnteredAccount = $originalFlag2;
                                $this->sengine->userProfilePoints_connectAccount = $originalFlag3;
                                $point = $increasepoints;
                                break;
                            case $this->reviewCreditScore:
                                $originalFlag = $this->sengine->creditScoreApprox;
                                $this->sengine->creditScoreApprox = 1;
                                $increasepoints = parent::simulateCalculateScore("REVIEWCREDITSCORE", $userid);
                                $this->sengine->creditScoreApprox = $originalFlag;
                                $point = $increasepoints;
                                break;
                            case $this->flexibilityOfAssets:
                                $originalFlag = $this->sengine->mediaCount;
                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->flexibilityOfAssets), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                $totalCount = count($articleIds);

                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->mediaCount = $originalFlag;
                                $point = $increasepoints;
                                if (count($articleIds) == 0) {
                                    $point = $currentScore + $totalCount * 5;
                                }
                                break;
                            case $this->consolidateLoans:
                                $point = $currentScore + 13;
                                break;
                            case $this->moreAsset:
                                $point = $currentScore + 5;
                                break;
                            case $this->moreDebt:
                                $point = $currentScore + 5;
                                break;
                            case $this->fillMiscTax:
                                $defaultValue1 = $this->sengine->doGetMoneyBackPayMore;
                                $defaultValue2 = $this->sengine->taxBracketUser;
                                $defaultValue3 = $this->sengine->userRetirementContributionDeductible;
                                $this->sengine->doGetMoneyBackPayMore = true;
                                $this->sengine->taxBracketUser = 1;
                                $this->sengine->userRetirementContributionDeductible = true;
                                $increasepoints = parent::simulateCalculateScore("FILLMISCTAX", $userid);
                                //Later we can ignore this if this value again set by sengine.
                                $this->sengine->doGetMoneyBackPayMore = $defaultValue1;
                                $this->sengine->taxBracketUser = $defaultValue2;
                                $this->sengine->userRetirementContributionDeductible = $defaultValue3;
                                $point = $increasepoints;
                                break;
                            case $this->fillMiscEstatePlanning:
                                //$point = $point;
                                $defaultValue1 = $this->sengine->willOrTrust;
                                $defaultValue2 = $this->sengine->willTrustReviwed;
                                $defaultValue3 = $this->sengine->reviewYearP40;
                                $defaultValue4 = $this->sengine->informationListOfHiddenAsset;
                                $defaultValue5 = $this->sengine->liquidedOnDeath;
                                $defaultValue6 = $this->sengine->plannedForInability;

                                $miscObj = new Misc();
                                $mrow = $miscObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $userid), 'select' => array('misctrust', 'miscreviewmonth',
                                        'miscreviewyear', 'mischiddenasset', 'miscliquid', 'miscspouse', 'miscrightperson')));
                                if (isset($mrow)) {
                                    if ($mrow->misctrust == '') {
                                        $this->sengine->willOrTrust = true;
                                        $this->sengine->willTrustReviwed = 1;
                                        $this->sengine->reviewYearP40 = date('Y');
                                    }
                                    if ($mrow->misctrust == '1' && ($mrow->miscreviewyear == '' || $mrow->miscreviewmonth == '')) {
                                        $this->sengine->willTrustReviwed = 1;
                                        $this->sengine->reviewYearP40 = date('Y');
                                    }
                                    if ($mrow->mischiddenasset == '') {
                                        $this->sengine->informationListOfHiddenAsset = true;
                                    }
                                    if ($mrow->mischiddenasset == '1' && $mrow->miscrightperson == '') {
                                        $this->sengine->informationListOfHiddenAsset = true;
                                    }
                                    if ($mrow->miscliquid == '') {
                                        $this->sengine->liquidedOnDeath = true;
                                    }
                                    if ($mrow->miscspouse == '') {
                                        $this->sengine->plannedForInability = true;
                                    }
                                } else {
                                    $this->sengine->willOrTrust = true;
                                    $this->sengine->willTrustReviwed = 1;
                                    $this->sengine->reviewYearP40 = date('Y');
                                    $this->sengine->informationListOfHiddenAsset = true;
                                    $this->sengine->liquidedOnDeath = true;
                                    $this->sengine->plannedForInability = true;
                                }
                                $increasepoints = parent::simulateCalculateScore("FILLMISCESTATEPLANNING", $userid);
                                $point = $increasepoints;
                                //Later we can ignore this if this value again set by sengine.
                                $this->sengine->willOrTrust = $defaultValue1;
                                $this->sengine->willTrustReviwed = $defaultValue2;
                                $this->sengine->reviewYearP40 = $defaultValue3;
                                $this->sengine->informationListOfHiddenAsset = $defaultValue4;
                                $this->sengine->liquidedOnDeath = $defaultValue5;
                                $this->sengine->plannedForInability = $defaultValue6;
                                break;
                            case $this->moreInsurance:
                                $linkArray = array();
                                if ($vals->flexi1) {
                                    $links = explode(' id="', $vals->flexi1);
                                    foreach ($links as $key => $link) {
                                        $linkname = explode('"', $link);
                                        $linkArray[] = $linkname[0];
                                    }
                                }
                                $point = $currentScore;
                                if ($linkArray) {
                                    foreach ($linkArray as $linkname) {
                                        if ($linkname == "addInsurancelifeinsurance") {
                                            $point += $this->sengine->maxPoint29;
                                        }
                                        if ($linkname == "addInsurancedisabilityinsurance") {
                                            $point += $this->sengine->maxPoint30;
                                        }
                                        if ($linkname == "addInsurancelongtermcareinsurance") {
                                            $point += $this->sengine->maxPoint31;
                                        }
                                    }
                                }
                                break;

                            case $this->moreWillAndTrust:
                                $originalFlag = $this->sengine->willOrTrust;
                                $this->sengine->willOrTrust = true;
                                $increasepoints = parent::simulateCalculateScore("MOREWILLANDTRUST", $userid);
                                $point = $increasepoints;
                                $this->sengine->willOrTrust = $originalFlag;
                                break;
                            case $this->detailedIncome:
                                $defaultValue1 = $this->sengine->userProfilePoints_income;
                                $this->sengine->userProfilePoints_income = 1;
                                $increasepoints = parent::simulateCalculateScore("COMPLETENESS", $userid);
                                $this->sengine->userProfilePoints_income = $defaultValue1;
                                $point = $increasepoints;
                                break;
                            case $this->detailedExpense:
                                $defaultValue1 = $this->sengine->userProfilePoints_expense;
                                $this->sengine->userProfilePoints_expense = 1;
                                $increasepoints = parent::simulateCalculateScore("COMPLETENESS", $userid);
                                $this->sengine->userProfilePoints_expense = $defaultValue1;
                                $point = $increasepoints;
                                break;
                            case $this->increaseDisabilityInsurance:
                                $originalFlag = $this->sengine->incomeCoverage;
                                $this->sengine->incomeCoverage = ($this->sengine->disainsuranceNeededActionStep + $this->sengine->incomeCoverage * $this->sengine->grossIncome) / $this->sengine->grossIncome;
                                $increasepoints = parent::simulateCalculateScore("INCREASEDISABILITYINSURANCE", $userid);
                                $this->sengine->incomeCoverage = $originalFlag;
                                $point = $increasepoints;
                                break;
                            case $this->getDisabilityInsurance:
                                $originalFlag = $this->sengine->incomeCoverage;
                                $this->sengine->incomeCoverage = ($this->sengine->disainsuranceNeededActionStep + $this->sengine->incomeCoverage * $this->sengine->grossIncome) / $this->sengine->grossIncome;
                                $increasepoints = parent::simulateCalculateScore("GETDISABILITYINSURANCE", $userid);
                                $this->sengine->incomeCoverage = $originalFlag;
                                $point = $increasepoints;
                                break;
                            case $this->considerConcentrationOfAssets:
                                $point = $currentScore + 5;
                                break;
                            case $this->reviewRiskTolerance:
                                $point = $currentScore + 5;
                                break;
                            case $this->maximizeRothIra:
                                $originalFlag1 = $this->sengine->investmentFactor;
                                $originalFlag2 = $this->sengine->point26cond;
                                $originalFlag3 = $this->sengine->retirementMonthlyContribution;
                                $originalFlag4 = $this->sengine->taxFreeAnnualSavings;
                                $originalFlag5 = $this->sengine->mediaCount;

                                $this->sengine->investmentFactor = 1;
                                $this->sengine->point26cond = 1;
                                $this->sengine->retirementMonthlyContribution = true;
                                if ($vals->flexi5) {
                                    $rothRow = Assets::model()->find(array('condition' => 'user_id = :user_id AND type = "IRA" and status = 0',
                                        'params' => array('user_id' => $userid), 'select' => 'SUM(contribution) AS contribution'));
                                    $currentContribution = 0;
                                    if ($rothRow) {
                                        $currentContribution = $rothRow->contribution * 12;
                                    }
                                    $this->sengine->taxFreeAnnualSavings = $this->sengine->taxFreeAnnualSavings + (12 * $vals->flexi5 - $currentContribution);
                                }

                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->maximizeRothIra), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("RA_ASSET", $userid);
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->investmentFactor = $originalFlag1;
                                $this->sengine->point26cond = $originalFlag2;
                                $this->sengine->retirementMonthlyContribution = $originalFlag3;
                                $this->sengine->taxFreeAnnualSavings = $originalFlag4;
                                $this->sengine->mediaCount = $originalFlag5;
                                $point = $increasepoints;
                                break;
                            case $this->addAutoRebalance:
                                $defaultAutoRebalance = $this->sengine->investmentAutomatically;
                                $this->sengine->investmentAutomatically = true;
                                $increasepoints = parent::simulateCalculateScore("ADDAUTOREBALANCE", $userid);
                                $point = $increasepoints;
                                $this->sengine->investmentAutomatically = $defaultAutoRebalance;
                                break;
                            case $this->estatePlanning:
                                $point = $currentScore + 5;
                                break;
                            case $this->maximizeTraditionalIra:
                                $originalFlag1 = $this->sengine->investmentFactor;
                                $originalFlag2 = $this->sengine->point26cond;
                                $originalFlag3 = $this->sengine->retirementMonthlyContribution;
                                $originalFlag4 = $this->sengine->taxDeferredAnnualSavings;
                                $originalFlag5 = $this->sengine->mediaCount;

                                $this->sengine->investmentFactor = 1;
                                $this->sengine->point26cond = 1;
                                $this->sengine->retirementMonthlyContribution = true;
                                if ($vals->flexi5) {
                                    $rothRow = Assets::model()->find(array('condition' => 'user_id = :user_id AND type = "IRA" and status = 0',
                                        'params' => array('user_id' => $userid), 'select' => 'SUM(contribution) AS contribution'));
                                    $currentContribution = 0;
                                    if ($rothRow) {
                                        $currentContribution = $rothRow->contribution * 12;
                                    }
                                    $this->sengine->taxDeferredAnnualSavings = $this->sengine->taxDeferredAnnualSavings + (12 * $vals->flexi5 - $currentContribution);
                                }

                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->maximizeTraditionalIra), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("RA_ASSET", $userid);
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $this->sengine->investmentFactor = $originalFlag1;
                                $this->sengine->point26cond = $originalFlag2;
                                $this->sengine->retirementMonthlyContribution = $originalFlag3;
                                $this->sengine->taxDeferredAnnualSavings = $originalFlag4;
                                $this->sengine->mediaCount = $originalFlag5;
                                $point = $increasepoints;
                                break;
                            case $this->evaluateConsumerDebtCosts:
                                $originalFlag = $this->sengine->mediaCount;
                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->evaluateConsumerDebtCosts), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                $totalCount = count($articleIds);

                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $point = $increasepoints;
                                if (count($articleIds) == 0) {
                                    $point = $currentScore + $totalCount * 5;
                                }
                                $this->sengine->mediaCount = $originalFlag;
                                break;
                            case $this->evaluateHousingCosts:
                                $originalFlag = $this->sengine->mediaCount;
                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->evaluateHousingCosts), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                $totalCount = count($articleIds);

                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $point = $increasepoints;
                                if (count($articleIds) == 0) {
                                    $point = $currentScore + $totalCount * 5;
                                }
                                $this->sengine->mediaCount = $originalFlag;
                                break;
                            case $this->considerLifeexpectancyRisk:
                                $originalFlag = $this->sengine->mediaCount;
                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->considerLifeexpectancyRisk), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                $totalCount = count($articleIds);

                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $point = $increasepoints;
                                if (count($articleIds) == 0) {
                                    $point = $currentScore + $totalCount * 5;
                                }
                                $this->sengine->mediaCount = $originalFlag;
                                break;
                            case $this->examineLifestyleCost:
                                $originalFlag = $this->sengine->mediaCount;
                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->examineLifestyleCost), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                $totalCount = count($articleIds);

                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $point = $increasepoints;
                                if (count($articleIds) == 0) {
                                    $point = $currentScore + $totalCount * 5;
                                }
                                $this->sengine->mediaCount = $originalFlag;
                                break;
                            case $this->improveCreditScore:
                                $originalFlag = $this->sengine->mediaCount;
                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->improveCreditScore), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                $totalCount = count($articleIds);

                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $point = $increasepoints;
                                if (count($articleIds) == 0) {
                                    $point = $currentScore + $totalCount * 5;
                                }
                                $this->sengine->mediaCount = $originalFlag;
                                break;

                            case $this->setupGoal:
                                $originalFlag1 = $this->sengine->userSumOfAssets;
                                $originalFlag2 = $this->sengine->isUserEnteredAccount;
                                $originalFlag4 = $this->sengine->userSumOfGoalSettingAssets;
                                $originalFlag5 = $this->sengine->numeratorP14;
                                $originalFlag6 = $this->sengine->userProfilePoints_assets;

                                $assetData = parent::getAssetData($userid);
                                $assets = $assetData[0];
                                $insurance = $assetData[1];
                                $pertrack = $assetData[2];
                                $tickers = $assetData[3];

                                if ($assets == 0) {
                                    $assets = array();
                                }
                                $valueObj = new stdClass();
                                $valueObj->id = 0;
                                $valueObj->balance = 5000;
                                $valueObj->invpos = '';
                                $valueObj->type = "BANK";
                                $assets[count($assets)] = $valueObj;

                                $this->sengine->userSumOfAssets = $this->sengine->userSumOfAssets + 5000;
                                $this->sengine->userSumOfGoalSettingAssets = $this->sengine->userSumOfGoalSettingAssets + 5000;
                                $this->sengine->numeratorP14 = $this->sengine->numeratorP14 + 5000;
                                $this->sengine->isUserEnteredAccount = true;

                                parent::CalculatePoint10($assets, $insurance, $pertrack);
                                $increasepoints = parent::simulateCalculateScore("RA_ASSET", $userid);
                                $point = $increasepoints;

                                $this->sengine->userSumOfAssets = $originalFlag1;
                                $this->sengine->isUserEnteredAccount = $originalFlag2;
                                $this->sengine->userSumOfGoalSettingAssets = $originalFlag4;
                                $this->sengine->numeratorP14 = $originalFlag5;
                                $this->sengine->userProfilePoints_assets = $originalFlag6;
                                break;
                            case $this->healthMedicalInsuranceArticle:
                                $originalFlag = $this->sengine->mediaCount;
                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->healthMedicalInsuranceArticle), 'select' => array('articles')));
                                $articleIds = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                }
                                $totalCount = count($articleIds);

                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $userid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }
                                $this->sengine->mediaCount += count($articleIds);
                                if ($this->sengine->mediaCount > 10) {
                                    $this->sengine->mediaCount = 10;
                                }
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $point = $increasepoints;
                                if (count($articleIds) == 0) {
                                    $point = $currentScore + $totalCount * 5;
                                }
                                $this->sengine->mediaCount = $originalFlag;
                                break;
                            case $this->refinanceConsumerDebts:
                                $debtData = parent::getDebtData($userid);
                                $restructuringDebtsArr = $debtData[0];
                                $personalDebtLoanArr = $debtData[1];
                                foreach ($restructuringDebtsArr as $debt) {
                                    if ($debt->rate > 0.1 && $debt->type == 'LOAN' && $debt->subtype <> '37') {
                                        $debt->rate = 0.1;
                                    }
                                }
                                foreach ($personalDebtLoanArr as $debt) {
                                    if ($debt->rate > 0.1 && $debt->type == 'LOAN' && $debt->subtype <> '37') {
                                        $debt->rate = 0.1;
                                    }
                                }
                                parent::CalculatePoint5($restructuringDebtsArr, $personalDebtLoanArr);
                                $increasepoints = parent::simulateCalculateScore("REFINANCE", $userid);
                                $point = $increasepoints;
                                break;
                            case $this->refinanceCreditCard:
                                $debtData = parent::getDebtData($userid);
                                $restructuringDebtsArr = $debtData[0];
                                $personalDebtLoanArr = $debtData[1];
                                foreach ($restructuringDebtsArr as $debt) {
                                    if ($debt->rate > 0.1 && $debt->type == 'CC') {
                                        $debt->rate = 0.1;
                                    }
                                }
                                foreach ($personalDebtLoanArr as $debt) {
                                    if ($debt->rate > 0.1 && $debt->type == 'CC') {
                                        $debt->rate = 0.1;
                                    }
                                }
                                parent::CalculatePoint5($restructuringDebtsArr, $personalDebtLoanArr);
                                $increasepoints = parent::simulateCalculateScore("REFINANCE", $userid);
                                $point = $increasepoints;
                                break;
                            case $this->refinanceCarLoan:
                                $debtData = parent::getDebtData($userid);
                                $restructuringDebtsArr = $debtData[0];
                                $personalDebtLoanArr = $debtData[1];
                                foreach ($restructuringDebtsArr as $debt) {
                                    if ($debt->rate > 0.05 && $debt->type == 'LOAN' && $debt->subtype == '37') {
                                        $debt->rate = 0.05;
                                    }
                                }
                                foreach ($personalDebtLoanArr as $debt) {
                                    if ($debt->rate > 0.05 && $debt->type == 'LOAN' && $debt->subtype == '37') {
                                        $debt->rate = 0.05;
                                    }
                                }
                                parent::CalculatePoint5($restructuringDebtsArr, $personalDebtLoanArr);
                                $increasepoints = parent::simulateCalculateScore("REFINANCE", $userid);
                                $point = $increasepoints;
                                break;

                            case $this->healthInsurance:
                                $originalFlag1 = $this->sengine->healthInsuranceType;
                                $originalFlag2 = $this->sengine->insuranceReviewYear24;
                                $this->sengine->healthInsuranceType = 'Comprehensive';
                                $this->sengine->insuranceReviewYear24 = date('Y');
                                $increasepoints = parent::simulateCalculateScore("HEALTHINSURANCE", $userid);
                                $point = $increasepoints;
                                $this->sengine->healthInsuranceType = $originalFlag1;
                                $this->sengine->insuranceReviewYear24 = $originalFlag2;
                                break;
                            case $this->reviewHealthInsurance:
                                $originalFlag = $this->sengine->insuranceReviewYear24;
                                $this->sengine->insuranceReviewYear24 = date('Y');
                                $increasepoints = parent::simulateCalculateScore("REVIEWHEALTHINSURANCE", $userid);
                                $point = $increasepoints;
                                $this->sengine->insuranceReviewYear24 = $originalFlag;
                                break;
                            case $this->reviewLifeInsurance:
                                $denominator = $this->sengine->LifeInsurance + $this->sengine->insuranceNeededActionStep;
                                $maxPoint29 = ($denominator > 0) ? (24 * ($this->sengine->LifeInsurance / $denominator)) : 24;

                                if ($maxPoint29 > 24) {
                                    $maxPoint29 = 24;
                                } elseif ($maxPoint29 <= 0) {
                                    $maxPoint29 = 0;
                                }

                                $tempPoint = $maxPoint29 - $this->sengine->wfPoint29;
                                $tempPoint = ($tempPoint > 0) ? $tempPoint : 0;
                                $point = $currentScore + $tempPoint;
                                break;
                            case $this->reviewDisabilityInsurance:
                                $originalFlag = $this->sengine->insuranceReviewYear30;
                                $this->sengine->insuranceReviewYear30 = date('Y');
                                $increasepoints = parent::simulateCalculateScore("REVIEWDISABILITYINSURANCE", $userid);
                                $point = $increasepoints;
                                $this->sengine->insuranceReviewYear30 = $originalFlag;
                                break;
                            case $this->learningArticles:
                                $originalFlag = $this->sengine->mediaCount;
                                $this->sengine->mediaCount = 10;
                                $increasepoints = parent::simulateCalculateScore("LEARNING", $userid);
                                $point = $increasepoints;
                                if (round($point) == round($currentScore)) {
                                    $point = $currentScore + 50;
                                }
                                $this->sengine->mediaCount = $originalFlag;
                                break;
                            /*                             * ************************************************
                             * Increase Savings Action Step
                             * ************************************************ */
                            case $this->increaseSavings:
                                $originalFlag1 = $this->sengine->taxFreeAnnualSavings;
                                $originalFlag2 = $this->sengine->taxableAnnualSavings;
                                $originalFlag3 = $this->sengine->taxDeferredAnnualSavings;
                                $this->sengine->taxFreeAnnualSavings = 0;
                                $this->sengine->taxableAnnualSavings = 0;
                                $this->sengine->taxDeferredAnnualSavings = 0;
                                $assetObj = new Assets();
                                $allAssets = $assetObj->findAll("user_id=:user_id AND status=0 and type in ('BANK','CR', 'IRA', 'BROK')", array("user_id" => $userid));
                                foreach ($allAssets as $asset) {
                                    if ($asset->type == 'BANK') {
                                        $this->sengine->taxableAnnualSavings = $this->sengine->taxableAnnualSavings + (12 * $asset->contribution);
                                    } else if ($asset->type == 'BROK') {
                                        $this->sengine->taxableAnnualSavings = $this->sengine->taxableAnnualSavings + (12 * $asset->contribution);
                                    } else if ($asset->assettype == 51) {
                                        $empsavings = ($asset->empcontribution / 100) * $this->sengine->grossIncome;
                                        $this->sengine->taxFreeAnnualSavings = $this->sengine->taxFreeAnnualSavings + 12 * ($empsavings + $asset->contribution);
                                    } else {
                                        $empsavings = ($asset->empcontribution / 100) * $this->sengine->grossIncome;
                                        $this->sengine->taxDeferredAnnualSavings = $this->sengine->taxDeferredAnnualSavings + 12 * ($empsavings + $asset->contribution);
                                    }
                                }

                                $point = $currentScore + 5;
                                if ($vals->flexi1) {
                                    $contribution = "";
                                    $contributionInfo = explode("id='contributionInfo' type='hidden'", $vals->flexi1);
                                    $linkname = explode("'", $contributionInfo[1]);
                                    $contribution = $linkname[1];
                                    $contributionInfo = explode(",", $contribution);
                                    foreach ($contributionInfo as $key => $info) {
                                        $assetValue = explode("|", $info);
                                        foreach ($allAssets as $asset) {
                                            if ($asset->id == $assetValue[0] && $asset->type == 'BANK') {
                                                $this->sengine->taxableAnnualSavings = $this->sengine->taxableAnnualSavings + 12 * ($assetValue[1] - $asset->contribution);
                                                break;
                                            } else if ($asset->id == $assetValue[0] && $asset->type == 'BROK') {
                                                $this->sengine->taxableAnnualSavings = $this->sengine->taxableAnnualSavings + 12 * ($assetValue[1] - $asset->contribution);
                                                break;
                                            } else if ($asset->id == $assetValue[0] && $asset->assettype == 51) {
                                                $this->sengine->taxFreeAnnualSavings = $this->sengine->taxFreeAnnualSavings + 12 * ($assetValue[1] - $asset->contribution);
                                                break;
                                            } else if ($asset->id == $assetValue[0]) {
                                                $this->sengine->taxDeferredAnnualSavings = $this->sengine->taxDeferredAnnualSavings + 12 * ($assetValue[1] - $asset->contribution);
                                                break;
                                            }
                                        }
                                    }
                                    $increasepoints = parent::simulateCalculateScore("ASSET", $userid);
                                    $point = $increasepoints;
                                }
                                $this->sengine->taxFreeAnnualSavings = $originalFlag1;
                                $this->sengine->taxableAnnualSavings = $originalFlag2;
                                $this->sengine->taxDeferredAnnualSavings = $originalFlag3;
                                break;
                            /*                             * ********************************************** */
                            /*                             * *************************************************
                             * Set Up Appropriate Type of Savings Account
                             * ************************************************ */
                            case $this->savingsAccount:
                                $point = $currentScore + 5;
                                break;
                            /*                             * *********************************************** */
                            /*                             * ************************************************
                             * Set Up Appropriate Type of Retirement Funding Account
                             * ************************************************ */
                            case $this->retirementFundingAccount:
                                $point = $currentScore + 5;
                                break;
                            /*                             * ********************************************** */
                            /*                             * ************************************************
                             *  Consider Charitable Donations (#79)
                             * ************************************************ */
                            case $this->considerCharitableDonations:
                                $point = $currentScore + 5;
                                break;
                            /*                             * ********************************************** */

                            /*                             * ********************************************** */
                            /*                             * ************************************************
                             *  Inquire About Pension Eligibility and Elections (#73)
                             * ************************************************ */
                            case $this->pensionEligibility:
                                $originalFlag = $this->sengine->beneAssigned;
                                $this->sengine->beneAssigned = true;
                                $increasepoints = parent::simulateCalculateScore("REVIEWBENEFICIARY", $userid);
                                $this->sengine->beneAssigned = $originalFlag;
                                $point = $increasepoints;
                                break;
                            /*                             * *********************************************** */

                            /*                             * ********************************************** */
                            /*                             * ************************************************
                             *  Create Informational Sheet & Location of Hidden Assets (#79)
                             * ************************************************ */
                            case $this->createInformationalSheet:
                                $originalFlag = $this->sengine->informationListOfHiddenAsset;
                                $this->sengine->informationListOfHiddenAsset = true;
                                $increasepoints = parent::simulateCalculateScore("CREATEINFORMATIONLIST", $userid);
                                $this->sengine->informationListOfHiddenAsset = $originalFlag;
                                $point = $increasepoints;
                                break;

                            /*                             * ************************************************
                             *  Pay Off Debts (#93)
                             * ************************************************ */
                            case $this->payOffDebts:
                                $originalFlag3 = $this->sengine->rentMortgage;
                                $originalFlag4 = $this->sengine->emiLoanCC;
                                $debtObj = new Debts();
                                $allDebts = $debtObj->findAll("user_id=:user_id AND status=0 AND monthly_payoff_balances=0", array("user_id" => $userid));
                                $rentMortgage = 0;
                                $emiloan = 0;
                                $valueArr1 = array();
                                $valueArr = array();
                                $point = $currentScore + 5;
                                foreach ($allDebts as $debts) {
                                    if ($debts["type"] == 'MORT') {
                                        $rentMortgage += $debts["amtpermonth"];
                                    } else {
                                        $emiloan += $debts["amtpermonth"];
                                        $valueObj = new stdClass();
                                        $valueObj->id = $debts["id"];
                                        $valueObj->creditor = $debts["name"];
                                        $valueObj->balance = $debts["balowed"];
                                        $valueObj->minimumPayment = ($debts["type"] == 'CC') ? ($debts["balowed"] * 0.02) : $debts["amtpermonth"];
                                        $valueObj->actualPayment = $debts["amtpermonth"];

                                        $valueObj->rate = $debts["apr"] / 100;
                                        $valueObj->type = $debts["type"];
                                        $valueArr[] = $valueObj;

                                        $valueObj1 = new stdClass();
                                        $valueObj1->id = $debts["id"];
                                        $valueObj1->debtName = $debts["name"];
                                        $valueObj1->balance = $debts["balowed"];
                                        $valueObj1->minimum = ($debts["type"] == 'CC') ? ($debts["balowed"] * 0.02) : $debts["amtpermonth"];
                                        $valueObj1->payment = $debts["amtpermonth"];

                                        $valueObj1->rate = $debts["apr"] / 100;
                                        $valueArr1[] = $valueObj1;
                                    }
                                }
                                $restructuringDebtsArr = $valueArr1;
                                $personalDebtLoanArr = $valueArr;

                                if ($vals->flexi1) {
                                    $payment = "";
                                    $balance_or_amtepermonth = 0;
                                    $paymentInfo = explode("id='paymentInfo' type='hidden'", $vals->flexi1);
                                    $linkname = explode("'", $paymentInfo[1]);
                                    $payment = $linkname[1];
                                    $paymentInfo = explode(",", $payment);
                                    foreach ($paymentInfo as $key => $info) {
                                        $debtValue = explode("|", $info);
                                        foreach ($restructuringDebtsArr as $debt) {
                                            if ($debt->id == $debtValue[0]) {
                                                $debt->payment = $debtValue[1];
                                                break;
                                            }
                                        }
                                        foreach ($personalDebtLoanArr as $debt) {
                                            if ($debt->id == $debtValue[0]) {
                                                $debt->actualPayment = $debtValue[1];
                                                break;
                                            }
                                        }
                                        foreach ($allDebts as $debt) {
                                            if ($debt["id"] == $debtValue[0] && $debt["type"] == 'MORT') {
                                                $rentMortgage = $rentMortgage - $debt["amtpermonth"] + $debtValue[1];
                                            } else if ($debt["id"] == $debtValue[0]) {
                                                $emiloan = $emiloan - $balance_or_amtepermonth + $debtValue[1];
                                            }
                                        }
                                    }
                                    $this->sengine->rentMortgage = $rentMortgage;
                                    $this->sengine->emiLoanCC = $emiloan;
                                    parent::CalculatePoint5($restructuringDebtsArr, $personalDebtLoanArr);
                                    $increasepoints = parent::simulateCalculateScore("DEBTS", $userid);
                                    $point = $increasepoints;
                                }
                                $this->sengine->rentMortgage = $originalFlag3;
                                $this->sengine->emiLoanCC = $originalFlag4;
                                break;
                            case $this->decreaseW4TaxWithholding:
                                $point = $currentScore + 5;
                                break;
                            case $this->increaseW4TaxWithholding:
                                $point = $currentScore + 5;
                                break;
                            /*                             * ************************************************
                             * Retirement Goal Plan
                             * ************************************************ */
                            case $this->retirementGoalPlan:
                                $originalFlag1 = $this->sengine->taxFreeAnnualSavings;
                                $originalFlag2 = $this->sengine->taxableAnnualSavings;
                                $originalFlag3 = $this->sengine->taxDeferredAnnualSavings;
                                $this->sengine->taxFreeAnnualSavings = 0;
                                $this->sengine->taxableAnnualSavings = 0;
                                $this->sengine->taxDeferredAnnualSavings = 0;
                                $assetObj = new Assets();
                                $allAssets = $assetObj->findAll("user_id=:user_id AND status=0 and type in ('BANK','CR', 'IRA', 'BROK')", array("user_id" => $userid));
                                foreach ($allAssets as $asset) {
                                    if ($asset->type == 'BANK') {
                                        $this->sengine->taxableAnnualSavings = $this->sengine->taxableAnnualSavings + (12 * $asset->contribution);
                                    } else if ($asset->type == 'BROK') {
                                        $this->sengine->taxableAnnualSavings = $this->sengine->taxableAnnualSavings + (12 * $asset->contribution);
                                    } else if ($asset->assettype == 51) {
                                        $empsavings = ($asset->empcontribution / 100) * $this->sengine->grossIncome;
                                        $this->sengine->taxFreeAnnualSavings = $this->sengine->taxFreeAnnualSavings + 12 * ($empsavings + $asset->contribution);
                                    } else {
                                        $empsavings = ($asset->empcontribution / 100) * $this->sengine->grossIncome;
                                        $this->sengine->taxDeferredAnnualSavings = $this->sengine->taxDeferredAnnualSavings + 12 * ($empsavings + $asset->contribution);
                                    }
                                }

                                $point = $currentScore + 5;
                                if ($vals->flexi1) {
                                    $contribution = "";
                                    $contributionInfo = explode("id='contributionInfo' type='hidden'", $vals->flexi1);
                                    $linkname = explode("'", $contributionInfo[1]);
                                    $contribution = $linkname[1];
                                    $contributionInfo = explode(",", $contribution);
                                    foreach ($contributionInfo as $key => $info) {
                                        $assetValue = explode("|", $info);
                                        foreach ($allAssets as $asset) {
                                            if ($asset->id == $assetValue[0] && $asset->type == 'BANK') {
                                                $this->sengine->taxableAnnualSavings = $this->sengine->taxableAnnualSavings + 12 * ($assetValue[1] - $asset->contribution);
                                                break;
                                            } else if ($asset->id == $assetValue[0] && $asset->type == 'BROK') {
                                                $this->sengine->taxableAnnualSavings = $this->sengine->taxableAnnualSavings + 12 * ($assetValue[1] - $asset->contribution);
                                                break;
                                            } else if ($asset->id == $assetValue[0] && $asset->assettype == 51) {
                                                $this->sengine->taxFreeAnnualSavings = $this->sengine->taxFreeAnnualSavings + 12 * ($assetValue[1] - $asset->contribution);
                                                break;
                                            } else if ($asset->id == $assetValue[0]) {
                                                $this->sengine->taxDeferredAnnualSavings = $this->sengine->taxDeferredAnnualSavings + 12 * ($assetValue[1] - $asset->contribution);
                                                break;
                                            }
                                        }
                                    }
                                    $increasepoints = parent::simulateCalculateScore("ASSET", $userid);
                                    $point = $increasepoints;
                                }
                                $this->sengine->taxFreeAnnualSavings = $originalFlag1;
                                $this->sengine->taxableAnnualSavings = $originalFlag2;
                                $this->sengine->taxDeferredAnnualSavings = $originalFlag3;
                                break;
                            /*                             * ********************************************** */
                            /*                             * ************************************************
                             * Diversify Investments for Risk Adjustments (#6)
                             * ************************************************
                             */
                            case $this->diversifyInvestmentsForRisk:
                                $defaultPoint = $this->sengine->wfPoint13;
                                $this->sengine->wfPoint13 = 1;
                                $increasepoints = parent::simulateCalculateScore("INVESTMENTMULTIPLIER", $userid);
                                $this->sengine->wfPoint13 = $defaultPoint;
                                $point = $increasepoints;
                                break;
                            /*                             * ********************************************** */
                        }
                        $actualpoints = round($point) - round($currentScore);
                        $actualpoints = ($actualpoints < 5) ? 5 : $actualpoints;
                        if ($vals->points <> $actualpoints) {
                            $update = Yii::app()->db->createCommand()->update('actionstep', array('points' => $actualpoints, 'lastmodifiedtime' => date("Y-m-d H:i:s")), 'id=:id', array('id' => $vals->id));
                        }
                    }
                }
                unset($queryuser, $queryaction);
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Read action steps
     * @param type $id
     * @return actionsteps
     */
    function getActionstep($id) {
        if ($id) {
            return Actionstepmeta::model()->findByPk($id);
        }
    }

    /**
     * Update action steps
     * @param type $id, $actionid, $status
     * @return
     */
    function updateActionstep($uid, $actionid, $status, $setmodified = null) {
        if ($uid) {
            Yii::trace(":U pdate:Userid->" . $uid, "actionstepcategory");
            Yii::trace(":ActionId->" . $actionid, "actionstepcategory");
            Yii::trace(":Status->" . $status, "actionstepcategory");
            Yii::trace(":Time->" . date('Y-m-d h:i:s A') . "\n", "actionstepcategory");
            Yii::trace("END====================================================", "actionstepcategory");

            $asObj = new Actionstep();
            $asObj->updateRemoveActionStep($uid, $actionid, $status, $setmodified);
            unset($asObj);
        }
    }

    /**
     * Saving Action Steps
     * @param type $uid, $actionid, $amt, $title, $percent, $linkid.
     */
    function SaveASteps($uid, $actionid, $amt = '', $title = '', $percent = '', $linkid = '') {
        if ($uid) {
            $obj = new ActionstepController(1); // preparing object
            $getval = $obj->getActionstep($actionid);
            if (isset($getval->actionname) && $getval->status == '0') {
                Yii::trace(":Create:UserId->" . $uid, "actionstepcategory");
                Yii::trace(":ActionId->" . $actionid, "actionstepcategory");
                Yii::trace(":Time->" . date('Y-m-d h:i:s A') . "\n", "actionstepcategory");
                Yii::trace("END====================================================", "actionstepcategory");

                $getactiondetail = Actionstep::model()->find(array('condition' => "user_id=:user_id AND actionid=:advisor_id", 'params' => array("user_id" => $uid, "advisor_id" => $actionid)));

                if (!$getactiondetail) {
                    $steps = new Actionstep();
                    $steps->user_id = $uid;
                    $steps->actionstatus = $this->actionNew;
                    $steps->actionid = $actionid;
                    $steps->type = $getval->type;
                    $steps->flexi5 = $amt;
                    $steps->flexi1 = $title;
                    $steps->flexi2 = $percent;
                    $steps->flexi3 = $linkid;
                }
                $actiondetails = $getval->actionname;
                $actiondetails = str_replace('{{amt}}', $amt, $actiondetails);
                $flexi5val = $amt;
                $actiondetails = str_replace('{{title}}', $title, $actiondetails);
                $actiondetails = str_replace('{{percent}}', $percent . '%', $actiondetails);
                $flexi3val = $linkid;

                if (!$getactiondetail) {
                    $steps->actionsteps = $actiondetails;
                    $steps->points = $getval->points;
                    $steps->lastmodifiedtime = date("Y-m-d H:i:s");
                    $steps->save();
                } else {
                    if (($getactiondetail->actionstatus == $this->actionHistory || $getactiondetail->actionstatus == $this->actionCompleted) && ($actionid == $this->reviewRiskTolerance || $actionid == $this->estatePlanning)) {
                        // Re - activate after every 1 year.
                        if (strtotime($getactiondetail->lastmodifiedtime) < strtotime('-6 months')) {
                            $update = Yii::app()->db->createCommand()
                                    ->update('actionstep', array('actionsteps' => $actiondetails, 'actionstatus' => $this->actionNew, 'points' => $getval->points,
                                'lastmodifiedtime' => date("Y-m-d H:i:s")), 'id=:id', array(':id' => $getactiondetail->id)
                            );
                        }
                    } else if ($getactiondetail->actionstatus == $this->actionHistory && ($actionid == $this->considerCharitableDonations || $actionid == $this->decreaseW4TaxWithholding || $actionid == $this->increaseW4TaxWithholding || $actionid == $this->considerConcentrationOfAssets)) {
                        // Reactivate after every 6 month.
                        // Action Step resets as reminder of task in 6 months from the date of deactivation
                        if (strtotime($getactiondetail->lastmodifiedtime) < strtotime('-6 months')) {
                            $update = Yii::app()->db->createCommand()
                                    ->update('actionstep', array('actionsteps' => $actiondetails, 'actionstatus' => $this->actionNew, 'points' => $getval->points,
                                'lastmodifiedtime' => date("Y-m-d H:i:s")), 'id=:id', array(':id' => $getactiondetail->id)
                            );
                        }
                    } else {
// Completed, History, Deleted => need to make it a new action step
// New, Viewed, Started are intermediary states so they keep their status
                        $status = $getactiondetail->actionstatus;
                        if ($getactiondetail->actionstatus == $this->actionCompleted || $getactiondetail->actionstatus == $this->actionHistory || $getactiondetail->actionstatus == $this->actionDeleted) {
                            $status = $this->actionNew;
                        }

                        if ($getactiondetail->actionstatus != $status || $getactiondetail->actionsteps != $actiondetails || $getactiondetail->flexi1 != $title || $getactiondetail->flexi2 != $percent || $getactiondetail->flexi3 != $flexi3val || $getactiondetail->flexi5 != $flexi5val) {

                            $update = Yii::app()->db->createCommand()
                                    ->update('actionstep', array(
                                'actionsteps' => $actiondetails,
                                'actionstatus' => $status,
                                'flexi1' => $title,
                                'flexi2' => $percent,
                                'flexi3' => $flexi3val,
                                'flexi5' => $flexi5val,
                                'lastmodifiedtime' => date("Y-m-d H:i:s")
                                    ), 'id=:id', array(':id' => $getactiondetail->id)
                            );
                        }
                    }
                }
                unset($getactiondetail);
            } else if ($getval->status == '1') {
                $steps = new Actionstep();
                // If the action step has been deactivated, delete it
                $checkqry = $steps->find("user_id = :user_id AND actionid = :actionid", array("user_id" => $uid, "actionid" => $actionid));
                if ($checkqry) {
                    if ($checkqry->actionstatus != $this->actionDeleted) {
                        $checkqry->actionstatus = $this->actionDeleted;
                        $checkqry->lastmodifiedtime = date("Y-m-d H:i:s");
                        $checkqry->save();
                    }
                }
            }
        }
    }

    /**
     * Remove action steps - Not using
     */
    function DeleteActionstep() {

    }

    /**
     * Check how much percentage completed of Profile
     * @param type $uid
     * @return user, userinfo, assets, debts
     */
    function actionProfile() {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $uid = Yii::app()->getSession()->get('wsuser')->id;
                $value = 0;
                $total = 10;
                $userObj = new User();
                $userinfoObj = new Userpersonalinfo();
                $assetObj = new Assets();
                $debtObj = new Debts();
                $expObj = new Expense();
                $incObj = new Income();
//Checking Assets
                $q = new CDbCriteria();
                $q->condition = "user_id = :user_id AND status=0 GROUP BY context";
                $q->limit = 5;
                $q->params = array('user_id' => $uid);
                $assetDetails = $assetObj->findAll($q);
                if (isset($assetDetails) && !empty($assetDetails)) {
                    foreach ($assetDetails as $arow) {
                        if (isset($arow->name) <> '') {
                            $value++;
                        }
                    }
                }
//Checking Debts
                $debtDetails = $debtObj->findAll($q);
                if (isset($debtDetails) && !empty($debtDetails)) {
                    foreach ($debtDetails as $drow) {
                        if (isset($drow->name) <> '') {
                            $value++;
                        }
                    }
                }
//Checking User Expenses
                $ieq = new CDbCriteria();
                $ieq->condition = "user_id = :user_id";
                $ieq->limit = 1;
                $ieq->params = array('user_id' => $uid);
                $expenses = $expObj->findAll($ieq);
                if (isset($expenses) && !empty($expenses)) {
                    foreach ($expenses as $erow) {
                        if (isset($erow->utilities) <> '' && isset($erow->groceries) <> '' && isset($erow->taxes) <> '') {
                            $value++;
                        }
                    }
                }
// Checking User Incomes
                $incomes = $incObj->findAll($ieq);
                if (isset($incomes) && !empty($incomes)) {
                    foreach ($incomes as $irow) {
                        if (isset($irow->totaluserincome) <> '' && isset($irow->gross_income) <> '') {
                            $value++;
                        }
                    }
                }
//Checking User Personal Info
                $userInfo = $userinfoObj->findByPk($uid);
                if (isset($userInfo) && !empty($userInfo)) {
                    if (isset($userInfo->age) <> '' && isset($userInfo->maritalstatus) <> '') {
                        $value++;
                    }
                    if (!empty($userInfo->userpic)) {
                        $value = $value + 2;
                    }
                }
//Checking User basic details
                $user = $userObj->findByPk($uid);
                if (isset($user) && !empty($user)) {
                    if (isset($user->firstname) <> '' && isset($user->lastname) <> '' && isset($user->city) <> '') {
                        $value++;
                    }
                }
//Calculate percentage
                $percantage = ($value / $total) * 100;
                return number_format($percantage, 2) . "%";
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Check to create action steps
     * @param type $uid
     * @return what all are pending
     */
    function CreateActionSteps($uid) {
        try {
            $estate_planning_raised = false;
            $actMetaObj = new Actionstepmeta();
            if (isset($uid)) {
// Fetching data from SE as JSON
                $uscore = new UserScore();
                $checkqry = $uscore->findBySql("SELECT scoredetails FROM userscore WHERE user_id = :user_id", array("user_id" => $uid));
                if ($checkqry) {
                    $details = $checkqry->scoredetails;
                    $sengineObj = unserialize($details);
                    $acusrinsurCoverage = $sengineObj->insuranceNeededActionStep;
                    $disabilityInsurCoverage = $sengineObj->disainsuranceNeededActionStep;
                    $userRiskValue = $sengineObj->userRiskValue;
                    $userGrowthRate = $sengineObj->userGrowthRate;
                    $point5 = $sengineObj->wfPoint5;
                    $point10 = $sengineObj->wfPoint10;
                    $point14 = $sengineObj->wfPoint14;
                    $point12 = $sengineObj->wfPoint12;
                    $point22 = $sengineObj->wfPoint22;
                    $spouseAge = $sengineObj->spouseAge;
                    $child1Age = $sengineObj->child1Age;
                    $child2Age = $sengineObj->child2Age;
                    $child3Age = $sengineObj->child3Age;
                    $child4Age = $sengineObj->child4Age;
                    $investmentAutomatically = $sengineObj->investmentAutomatically;
                    $healthInsuranceReviewYear = $sengineObj->insuranceReviewYear24;
                    $lifeInsuranceReviewYear = $sengineObj->insuranceReviewYear29;
                    $disabilityInsuranceReviewYear = $sengineObj->insuranceReviewYear30;
                    $nonCoreelatedTicker = $sengineObj->nonCoreelatedTicker;

                    $mediaCount = $sengineObj->mediaCount;
                    $currentAge = $sengineObj->userCurrentAge;
                    $retired = $sengineObj->retired;
                    $userSumOfGoalSettingAssets = $sengineObj->userSumOfGoalSettingAssets;
                    $userSumOfAssets = $sengineObj->userSumOfAssets;
                    $userSumOfOtherAssets = $sengineObj->userSumOfOtherAssets;
                    $userSumOfDebts = $sengineObj->userSumOfDebts;

                    $contributions = $sengineObj->taxableAnnualSavings + $sengineObj->taxDeferredAnnualSavings + $sengineObj->taxFreeAnnualSavings;

                    $grossincome = $sengineObj->userIncomePerMonth;
                    $grossexpense = $sengineObj->userExpensePerMonth;
                    $informationListOfHiddenAsset = $sengineObj->informationListOfHiddenAsset;
                    $networthRatio = isset($sengineObj->wfPoint11) ? ($sengineObj->wfPoint11 / 100) : 0;

                    if ($acusrinsurCoverage > 0) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->lifeInsuranceVideo), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->lifeInsuranceVideo);
                        }
                    }

                    if ($disabilityInsurCoverage > 0) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->disabilityInsuranceVideo), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->disabilityInsuranceVideo);
                        }
                    }

                    //create Action Step: High Returns vs Savings (#62)
                    $assetValue = Assets::model()->find(array('condition' => 'user_id = :user_id AND type <> "EDUC" and status = 0',
                                        'params' => array('user_id' => $uid), 'select' => 'SUM(contribution) AS contribution'));
                    $contributionValue = 0;
                    if($assetValue) {
                        $contributionValue = $assetValue->contribution;
                    }
                    if ($contributionValue < $grossincome * 0.05 && $userRiskValue >= 7 ) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->highReturnsVideo), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->highReturnsVideo);
                        }
                    }

                    //create Action Step: Property And Casualty Insurance (#63)
                    $pacInsVidChk = 1;
                    //CASE 1&3: If User has Property in assets but does not have Home Owners Insurance /  If they havent reviewed home insurance in 3 years
                    $propAssetCount = Assets::model()->count("user_id=:user_id AND type IN ('PROP') and status=0", array("user_id" => $uid));
                    if ($propAssetCount > 0) {
                        $pacPropInsurance = Insurance::model()->findAllBySql("SELECT reviewyear FROM insurance WHERE type IN ('HOME') AND user_id=:user_id AND status=0", array("user_id" => $uid));
                        if ($pacPropInsurance) {
                            $year = date('Y');

                            foreach ($pacPropInsurance as $pacPropInsuranceDetail) {
                                $pacPropInsurancereviewYear = $pacPropInsuranceDetail->reviewyear;
                                if (($year - $pacPropInsurancereviewYear) >= 3) {
                                    $pacInsVidChk = 0;
                                }
                            }
                        } else {
                            $pacInsVidChk = 0;
                        }
                    }
                    //CASE 2&3: If User has Vehicle in assets but does not have vehicle Insurance /  If they havent reviewed vehcile insurance in 3 years
                    $vehiAssetCount = Assets::model()->count("user_id=:user_id AND type IN ('VEHI') and status=0", array("user_id" => $uid));
                    if ($vehiAssetCount > 0) {
                        $pacVehiInsurance = Insurance::model()->findAllBySql("SELECT reviewyear FROM insurance WHERE type IN ('VEHI') AND user_id=:user_id AND status=0", array("user_id" => $uid));
                        if ($pacVehiInsurance) {
                            $year = date('Y');
                            foreach ($pacVehiInsurance as $pacVehiInsuranceDetail) {
                                $pacVehiInsurancereviewYear = $pacVehiInsuranceDetail->reviewyear;
                                if (($year - $pacVehiInsurancereviewYear) >= 3) {
                                    $pacInsVidChk = 0;
                                }
                            }
                        } else {
                            $pacInsVidChk = 0;
                        }
                    }
                    if ($pacInsVidChk == 0) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->propertyAndCasualtyInsVideo), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->propertyAndCasualtyInsVideo);
                        }
                    }
                    //create Action Step: Inflation (#24)//
                    if ($point12 < 200) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->inflationVideo), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->inflationVideo);
                        }
                    }

                    // Create Action step : Investment Diversification (#8)
                    if ($point10 < 100) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->investmentDiversificationVideo), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->investmentDiversificationVideo);
                        }
                    }

                    // check if one of the user's goals shows "Needs Attention."
                    $goalObj = new Goal();
                    $goalCount = $goalObj->count(array('condition' => "user_id = :user_id and goalstatus = 1 and status = 'Needs Attention'", 'params' => array("user_id" => $uid)));

                    $highDebtRatio = false;
                    $debtObj = new Debts();
                    $mdebtDet = $debtObj->find(array('condition' => "user_id=:user_id AND type <> 'MORT' AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid), 'select' => 'SUM(amtpermonth) AS total'));
                    if (isset($mdebtDet->total)) {
                        $new_gross = (20 / 100) * $grossincome;
                        if ($mdebtDet->total >= $new_gross) {
                            $highDebtRatio = true;
                        }
                    }

                    if ($contributions == 0 || $goalCount > 0 || $highDebtRatio) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->budgetingAndCashFlowVideo), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->budgetingAndCashFlowVideo);
                        }
                    }

                    //create Action Step: Watch Video - Estate Planning (#95)//
                    $miscObj = new Misc();
                    $espvideoChk = 1;
                    $mrow = $miscObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid)));
                    if (isset($mrow) && $mrow->misctrust == '1') {
                        if (isset($mrow->miscreviewyear) && $mrow->miscreviewyear != 'Year' && $mrow->miscreviewyear != '') {
                            if ((date('Y') - $mrow->miscreviewyear) <= 4) {
                                $espvideoChk = 0;
                            }
                        }
                    }
                    if ($espvideoChk == 1) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->estatePlanningVideo), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->estatePlanningVideo);
                        }
                    }

                    //create Action Step: Watch Video - Tax Planning (#48)//
                    $tpvideoChk = 1;
                    if (isset($mrow)) {
                        if ($mrow->taxpay == '' || $mrow->taxbracket == '' || $mrow->taxbracket == '4' || $mrow->taxvalue == '' || $mrow->taxvalue == '3' || $mrow->taxcontri == '' || $mrow->taxStdOrItemDed == '') {
                            $tpvideoChk = 0;
                        }
                    } else {
                        $tpvideoChk = 0;
                    }

                    if ($tpvideoChk == 0) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->taxPlanningVideo), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->taxPlanningVideo);
                        }
                    }
                } else {
                    $grossincome = 5000;
                    $grossexpense = 0;
                    $nonCoreelatedTicker = false;
                    $mediaCount = 0;
                    $currentAge = 30;
                    $userGrowthRate = 7;
                    $userSumOfGoalSettingAssets = 0;
                    $healthInsuranceReviewYear = 0;
                    $lifeInsuranceReviewYear = 0;
                    $disabilityInsuranceReviewYear = 0;
                    $investmentAutomatically = false;
                    $informationListOfHiddenAsset = false;
                    $point5 = 0;
                    $point14 = 0;
                    $point22 = 0;
                }
// Initialize Classes
                $assetObj = new Assets();
                $debtObj = new Debts();
                $expenObj = new Expense();
                $incomObj = new Income();
                $goalsObj = new Goal();
                $insurObj = new Insurance();
                $miscObj = new Misc();
                $usrinfoObj = new Userpersonalinfo();
                $estiObj = new Estimation();
                $actMetaObj = new Actionstepmeta();

// Create Action Step : Health & Medical Insurance (#64)
// Create Action Step : Health Insurance - Get Coverage (#86)
                $getHealthInsDetail = $insurObj->findAll(array('condition' => "user_id=:user_id AND status=0 AND type = 'HEAL'", 'params' => array("user_id" => $uid), 'select' => array('status')));
                if (isset($getHealthInsDetail) && !empty($getHealthInsDetail)) {
                    $year = date('Y');
                    $diff = $year - $healthInsuranceReviewYear;
                    if ($healthInsuranceReviewYear > 0 && $diff > 1) {
                        $this->SaveASteps($uid, $this->reviewHealthInsurance);
                    }
                } else {
                    $this->SaveASteps($uid, $this->healthInsurance);
                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->healthMedicalInsuranceArticle), 'select' => array('articles')));
                    $articleIds = array();
                    $articleUrl = array();
                    $articleName = array();
                    $narray = explode('|', $actionStepMeta->articles);
                    // Pull the article information from the articles column of actionstepmeta table
                    foreach ($narray as $k => $nval) {
                        $artdiv = explode('#', $nval);
                        $articleIds[] = $artdiv[2];
                        $articleUrl[$artdiv[2]] = $artdiv[1];
                        $articleName[$artdiv[2]] = $artdiv[0];
                    }

                    // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                    if (!empty($articleIds)) {
                        $artids = implode(',', $articleIds);
                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                        if (isset($mediaObj) && !empty($mediaObj)) {
                            foreach ($mediaObj as $media) {
                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                    unset($articleIds[$key]);
                                }
                            }
                        }
                    }

                    // Show the articles not read to user
                    if (!empty($articleIds)) {
                        $articleNames = array();
                        $articleNum = 1;
                        foreach ($articleIds as $articleId) {
                            $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->healthMedicalInsuranceArticle .
                                    '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                            $articleNum++;
                        }
                        $artnames = implode(' <br>', $articleNames);
                        $this->SaveASteps($uid, $this->healthMedicalInsuranceArticle, '', '<br>' . $artnames . '<br>');
                    }
                }

// Create Action Step : #41
                if (!$nonCoreelatedTicker) {
                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->noncorrelatedAltInvestment), 'select' => array('articles')));
                    $articleIds = array();
                    $articleUrl = array();
                    $articleName = array();
                    $narray = explode('|', $actionStepMeta->articles);
                    // Pull the article information from the articles column of actionstepmeta table
                    foreach ($narray as $k => $nval) {
                        $artdiv = explode('#', $nval);
                        $articleIds[] = $artdiv[2];
                        $articleUrl[$artdiv[2]] = $artdiv[1];
                        $articleName[$artdiv[2]] = $artdiv[0];
                    }

                    // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                    if (!empty($articleIds)) {
                        $artids = implode(',', $articleIds);
                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                        if (isset($mediaObj) && !empty($mediaObj)) {
                            foreach ($mediaObj as $media) {
                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                    unset($articleIds[$key]);
                                }
                            }
                        }
                    }

                    // Show the articles not read to user
                    if (!empty($articleIds)) {
                        $articleNames = array();
                        $articleNum = 1;
                        foreach ($articleIds as $articleId) {
                            $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->noncorrelatedAltInvestment .
                                    '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                            $articleNum++;
                        }
                        $artnames = implode(' <br>', $articleNames);
                        $this->SaveASteps($uid, $this->noncorrelatedAltInvestment, '', '<br>' . $artnames . '<br>');
                    }
                }

                if ($point14 < 37.5) {
                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->flexibilityOfAssets), 'select' => array('articles')));
                    $articleIds = array();
                    $articleUrl = array();
                    $articleName = array();
                    $narray = explode('|', $actionStepMeta->articles);
                    // Pull the article information from the articles column of actionstepmeta table
                    foreach ($narray as $k => $nval) {
                        $artdiv = explode('#', $nval);
                        $articleIds[] = $artdiv[2];
                        $articleUrl[$artdiv[2]] = $artdiv[1];
                        $articleName[$artdiv[2]] = $artdiv[0];
                    }

                    // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                    if (!empty($articleIds)) {
                        $artids = implode(',', $articleIds);
                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                        if (isset($mediaObj) && !empty($mediaObj)) {
                            foreach ($mediaObj as $media) {
                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                    unset($articleIds[$key]);
                                }
                            }
                        }
                    }

                    // Show the articles not read to user
                    if (!empty($articleIds)) {
                        $articleNames = array();
                        $articleNum = 1;
                        foreach ($articleIds as $articleId) {
                            $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->flexibilityOfAssets .
                                    '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                            $articleNum++;
                        }
                        $artnames = implode(' <br>', $articleNames);
                        $this->SaveASteps($uid, $this->flexibilityOfAssets, '', '<br>' . $artnames . '<br>');
                    }
                }


// Create Action step : Complete Risk Tolerance Preference (#7) & Review Risk Tolerance Preference (#38)
                if (isset($userRiskValue)) {
                    if ($userRiskValue == 0) {
                        $this->SaveASteps($uid, $this->completeRiskTolerance);
                    } else if ($userRiskValue == 1 || $userRiskValue == 2) {
                        $this->SaveASteps($uid, $this->reviewRiskTolerance, '', 'averse to');
                    } else if ($userRiskValue == 9 || $userRiskValue == 10) {
                        $this->SaveASteps($uid, $this->reviewRiskTolerance, '', 'tolerant of');
                    }
                }
// Create Action step : Adding IRA - Traditional or Roth (#12)
                /*
                  $qic = new CDbCriteria();
                  $qic->condition = "uid = :uid AND status=0";
                  $qic->select = 'balance';
                  $qic->limit = 1;
                  $qic->params = array('uid' => $uid);
                  $qic->addInCondition('type', array('CR', 'IRA'));
                  $astDetails = $assetObj->findAll($qic);
                  if (empty($astDetails)) {
                  $this->SaveASteps($uid, $this->addIraRothOrTraditional, $this->iraAmount);
                  }
                 */

// Create Action step : Review Beneficiary Designations and Update if Needed (#10)
                $q = new CDbCriteria();
                $q->condition = "user_id = :user_id AND status=0 AND beneficiary <> 1";
                $q->select = 'id, name, type';
                $q->limit = 10;
                $q->params = array('user_id' => $uid);
                $q->addInCondition('type', array('CR', 'IRA', 'EDUC'));
                $assetDetails = $assetObj->findAll($q);
                $title = '';
                $assetNames = array();
                if (count($assetDetails)) {
                    foreach ($assetDetails as $arow) {
                        if (!isset($arow->name) || $arow->name == '') {
                            $arow->name = $arow->getDefaultName($arow->type);
                        }
                        $assetNames[] = '<a href="#" id="' . $arow->id . 'addAssetsInvestment" class="addAssets actionStep">' . $arow->name . '</a>';
                    }
                    if (count($assetNames)) {
                        $title = implode(' <br>', $assetNames);
                        $this->SaveASteps($uid, $this->reviewBeneficiary, '', '<br>' . $title . '<br>');
                    }
                }

// Create Action step :  Inquire About Pension Eligibility and Elections (#73)
                $q = new CDbCriteria();
                $q->condition = "user_id = :user_id AND status=0 AND beneficiary <> 1";
                $q->select = 'id, name, type';
                $q->limit = 10;
                $q->params = array('user_id' => $uid);
                $q->addInCondition('type', array('PENS'));
                $assetDetails = $assetObj->findAll($q);
                $title = '';
                $assetNames = array();
                if (count($assetDetails)) {
                    foreach ($assetDetails as $arow) {
                        if (!isset($arow->name) || $arow->name == '') {
                            $arow->name = $arow->getDefaultName($arow->type);
                        }
                        $assetNames[] = '<a href="#" id="' . $arow->id . 'addAssetsSilent" class="addAssets actionStep">' . $arow->name . '</a>';
                    }
                    if (count($assetNames)) {
                        $title = implode(' <br>', $assetNames);
                        $this->SaveASteps($uid, $this->pensionEligibility, '', '<br>' . $title . '<br>');
                    }
                }
// Create Action step : Evaluate Amount of Consumer Debt Costs Compared to Income (#54)
                $consumerDebt = $debtObj->find(array('condition' => "user_id=:user_id AND type <> 'MORT' AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid), 'select' => 'SUM(amtpermonth) AS total'));
                if (isset($consumerDebt->total)) {
                    $new_gross = (22 / 100) * $grossincome;
                    if ($consumerDebt->total >= $new_gross) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->evaluateConsumerDebtCosts), 'select' => array('articles')));
                        $articleIds = array();
                        $articleUrl = array();
                        $articleName = array();
                        $narray = explode('|', $actionStepMeta->articles);
                        // Pull the article information from the articles column of actionstepmeta table
                        foreach ($narray as $k => $nval) {
                            $artdiv = explode('#', $nval);
                            $articleIds[] = $artdiv[2];
                            $articleUrl[$artdiv[2]] = $artdiv[1];
                            $articleName[$artdiv[2]] = $artdiv[0];
                        }

                        // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                        if (!empty($articleIds)) {
                            $artids = implode(',', $articleIds);
                            $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if (isset($mediaObj) && !empty($mediaObj)) {
                                foreach ($mediaObj as $media) {
                                    if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                        unset($articleIds[$key]);
                                    }
                                }
                            }
                        }
                        // Show the articles not read to user
                        if (!empty($articleIds)) {
                            $articleNames = array();
                            $articleNum = 1;
                            foreach ($articleIds as $articleId) {
                                $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->evaluateConsumerDebtCosts .
                                        '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                                $articleNum++;
                            }
                            $artnames = implode(' <br>', $articleNames);
                            $this->SaveASteps($uid, $this->evaluateConsumerDebtCosts, '', '<br>' . $artnames . '<br>');
                        }
                    }
                }
// Create Action step : Evaluate Amount of Housing Costs Compared to Income (#55)
                $mdebtDet = $debtObj->find(array('condition' => "user_id=:user_id AND type='MORT' AND status=0", 'params' => array("user_id" => $uid), 'select' => 'SUM(amtpermonth) AS total'));
                if (isset($mdebtDet->total)) {
                    $new_gross = (30 / 100) * $grossincome;
                    if ($mdebtDet->total >= $new_gross) {
                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->evaluateHousingCosts), 'select' => array('articles')));
                        $articleIds = array();
                        $articleUrl = array();
                        $articleName = array();
                        $narray = explode('|', $actionStepMeta->articles);
                        // Pull the article information from the articles column of actionstepmeta table
                        foreach ($narray as $k => $nval) {
                            $artdiv = explode('#', $nval);
                            $articleIds[] = $artdiv[2];
                            $articleUrl[$artdiv[2]] = $artdiv[1];
                            $articleName[$artdiv[2]] = $artdiv[0];
                        }

                        // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                        if (!empty($articleIds)) {
                            $artids = implode(',', $articleIds);
                            $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if (isset($mediaObj) && !empty($mediaObj)) {
                                foreach ($mediaObj as $media) {
                                    if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                        unset($articleIds[$key]);
                                    }
                                }
                            }
                        }

                        // Show the articles not read to user
                        if (!empty($articleIds)) {
                            $articleNames = array();
                            $articleNum = 1;
                            foreach ($articleIds as $articleId) {
                                $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->evaluateHousingCosts .
                                        '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                                $articleNum++;
                            }
                            $artnames = implode(' <br>', $articleNames);
                            $this->SaveASteps($uid, $this->evaluateHousingCosts, '', '<br>' . $artnames . '<br>');
                        }
                    }
                }

// Create Action step : Consider Life Expectancy Risk (#74)
                if ($retired && ($currentAge < 60 || $point22 < 20)) {
                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->considerLifeexpectancyRisk), 'select' => array('articles')));
                    $articleIds = array();
                    $articleUrl = array();
                    $articleName = array();
                    $narray = explode('|', $actionStepMeta->articles);
                    // Pull the article information from the articles column of actionstepmeta table
                    foreach ($narray as $k => $nval) {
                        $artdiv = explode('#', $nval);
                        $articleIds[] = $artdiv[2];
                        $articleUrl[$artdiv[2]] = $artdiv[1];
                        $articleName[$artdiv[2]] = $artdiv[0];
                    }

                    // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                    if (!empty($articleIds)) {
                        $artids = implode(',', $articleIds);
                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                        if (isset($mediaObj) && !empty($mediaObj)) {
                            foreach ($mediaObj as $media) {
                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                    unset($articleIds[$key]);
                                }
                            }
                        }
                    }
                    // Show the articles not read to user
                    if (!empty($articleIds)) {
                        $articleNames = array();
                        $articleNum = 1;
                        foreach ($articleIds as $articleId) {
                            $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->considerLifeexpectancyRisk .
                                    '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                            $articleNum++;
                        }
                        $artnames = implode(' <br>', $articleNames);
                        $this->SaveASteps($uid, $this->considerLifeexpectancyRisk, '', '<br>' . $artnames . '<br>');
                    }
                }
// Create Action step : Examine Your Lifestyle Costs to Make Certain You Aren't Overspending (#75)
                if ($retired && $point22 < 20) {
                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->examineLifestyleCost), 'select' => array('articles')));
                    $articleIds = array();
                    $articleUrl = array();
                    $articleName = array();
                    $narray = explode('|', $actionStepMeta->articles);
                    // Pull the article information from the articles column of actionstepmeta table
                    foreach ($narray as $k => $nval) {
                        $artdiv = explode('#', $nval);
                        $articleIds[] = $artdiv[2];
                        $articleUrl[$artdiv[2]] = $artdiv[1];
                        $articleName[$artdiv[2]] = $artdiv[0];
                    }

                    // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                    if (!empty($articleIds)) {
                        $artids = implode(',', $articleIds);
                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                        if (isset($mediaObj) && !empty($mediaObj)) {
                            foreach ($mediaObj as $media) {
                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                    unset($articleIds[$key]);
                                }
                            }
                        }
                    }
                    // Show the articles not read to user
                    if (!empty($articleIds)) {
                        $articleNames = array();
                        $articleNum = 1;
                        foreach ($articleIds as $articleId) {
                            $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->examineLifestyleCost .
                                    '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                            $articleNum++;
                        }
                        $artnames = implode(' <br>', $articleNames);
                        $this->SaveASteps($uid, $this->examineLifestyleCost, '', '<br>' . $artnames . '<br>');
                    }
                }

// Create Action step : Create Emergency Fund for Unplanned Costs (#20)
                if ($userSumOfGoalSettingAssets < ($grossexpense * 3)) {
                    $emergencyAmount = ceil(($grossexpense * 3 - $userSumOfGoalSettingAssets) / 1000) * 1000;
                    $emergencyAmount = number_format($emergencyAmount);
                    $this->SaveASteps($uid, $this->createEmergencyFund, $emergencyAmount);
                }
// Create Action step : Give us more detailed Income (#31) & Give us more detailed Expenses (#32)
                $incDetails = $incomObj->findAll(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid), 'select' => 'totaluserincome'));
                $expDetails = $expenObj->findAll(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid), 'select' => 'actualexpense'));
                $showIncomeStep = false;
                if (empty($incDetails)) {
                    $showIncomeStep = true;
                } else {
                    foreach ($incDetails as $irow) {
                        if ($irow->totaluserincome <= 0.00) {
                            $showIncomeStep = true;
                        }
                    }
                }
                if ($showIncomeStep) {
                    $this->SaveASteps($uid, $this->detailedIncome);
                } else {
                    if (empty($expDetails)) {
                        $this->SaveASteps($uid, $this->detailedExpense);
                    } else {
                        foreach ($expDetails as $erow) {
                            if ($erow->actualexpense <= 0.00) {
                                $this->SaveASteps($uid, $this->detailedExpense);
                            }
                        }
                    }
                }

                /*                 * *************************************************
                 * Set Up Appropriate Type of Savings Account
                 * Triggering the save action step if no savings account
                 * account is added.
                 * ************************************************ */
                $savings_account_exists = false;
                $education_account_exists = false;
                $action_step_needed = false;
                $qic = new CDbCriteria();
                $qic->condition = "user_id = :user_id AND status=0";
                $qic->select = 'balance,type,contribution';
                $qic->params = array('user_id' => $uid);
                $qic->addInCondition('type', array('BANK', 'EDUC', 'BROK'));
                $astDetails = $assetObj->findAll($qic);
                $goalObj = new Goal();
                $newgoalDetails = $goalObj->findAll(array('condition' => "user_id = :user_id AND goalstatus=1 and goaltype <> 'RETIREMENT' and goalpriority > 0 ORDER by goalpriority", 'params' => array("user_id" => $uid), 'select' => 'goalname, goaltype, payoffdebts, goalamount,saved, permonth, goalstartdate, goalenddate, downpayment'));

                foreach ($astDetails as $irow) {
                    switch ($irow->type) {
                        case "BANK":
                            $savings_account_exists = true;
                            break;
                        case "EDUC":
                            $education_account_exists = true;
                            break;
                        case "BROK":
                            $savings_account_exists = true;
                            break;
                    }
                }

                if (isset($newgoalDetails) && !empty($newgoalDetails)) {
                    foreach ($newgoalDetails as $irow) {
                        switch ($irow->goaltype) {
                            case "HOUSE":
                                if (!$savings_account_exists) {
                                    $action_step_needed = true;
                                }
                                break;
                            case "COLLEGE":
                                if (!$savings_account_exists && !$education_account_exists) {
                                    $action_step_needed = true;
                                }
                                break;
                            case "CUSTOM":
                                if (!$savings_account_exists) {
                                    $action_step_needed = true;
                                }
                                break;
                        }
                    }
                }

                if ($action_step_needed) {
                    $this->SaveASteps($uid, $this->savingsAccount);
                }
                /*                 * *********************************************** */

                /*                 * *************************************************
                 * Set Up Appropriate Type of Retirement Funding Account
                 * Triggering the save action step if no retirement fund
                 * account is added. Retirement fund includes
                 * IRA account and Company Retirement Plan
                 *
                 * ************************************************ */
                $assetCount = $assetObj->count("user_id=:user_id and status=0 and type in ('CR','IRA')", array("user_id" => $uid));
                $goalCount = $goalObj->count("user_id=:user_id and goalstatus=1 and goaltype = 'RETIREMENT'", array("user_id" => $uid));
                if ($goalCount > 0 && $assetCount <= 0) {
                    $this->SaveASteps($uid, $this->retirementFundingAccount);
                }
                /*                 * *********************************************** */

                /*                 * ************************************************
                 * Payoff Debt Plan
                  Once an action step has started (where a user clicks on a link), we keep the advice based on the following scenarios
                  Scenario 1: A goal update => new advice
                  Scenario 2: A new debt => new advice only if the new debt is now part of the goal (if goal has all debts selected)
                  Scenario 3: Existing debt update => old advice
                  Scenario 4: Hiding/Deleting debt that affects goal => new advice (if goal had that debt included)
                  Scenario 5: Balance goes below suggested payment amount => new advice
                  Scenario 6: If balance order changes => we currently do lowest to highest balance but if that order changes => new advice
                 * ************************************************ */
                $goal = $goalObj->find(array('condition' => "user_id = :user_id AND goalstatus=1 and goaltype = 'DEBT'", 'params' => array("user_id" => $uid), 'select' => 'id,goalname, goaltype, payoffdebts, goalamount,saved, permonth,goalenddate,modifiedtimestamp,goalassumptions_1,goalassumptions_2'));
                $sortOrder = "balowed asc, apr desc";
                if (isset($goal)) {
                    if ($goal->goalassumptions_2 == "72") {
                        $sortOrder = "balowed desc, apr desc";
                    }
                    if ($goal->goalassumptions_2 == "73") {
                        $sortOrder = "apr desc, balowed asc";
                    }
                    if ($goal->goalassumptions_2 == "74") {
                        $sortOrder = "apr asc, balowed asc";
                    }
                }
                $debts = $debtObj->findAll(array('condition' => "user_id = :user_id AND status=0 AND monthly_payoff_balances = 0 order by " . $sortOrder, 'params' => array("user_id" => $uid), 'select' => 'id,name,balowed,type,amtpermonth,apr'));
                if (isset($goal) && isset($debts) && !empty($debts)) {
                    $newAdvice = false;
                    $actionstep = Actionstep::model()->find(array('condition' => "user_id=:user_id AND actionid=:advisor_id and actionstatus in ('0','2','3')", 'params' => array("user_id" => $uid, "advisor_id" => $this->payOffDebts)));
                    $debtInfo = '';
                    if (!$actionstep || $actionstep->actionstatus <> '3') {
                        // If an action step has not started
                        $newAdvice = true;
                    } else if ($goal->modifiedtimestamp > $actionstep->lastmodifiedtime) {
                        // Scenario 1: A goal update => new advice
                        $newAdvice = true;
                    } else if ($actionstep->flexi1) {
                        $debtArray = array();
                        if (!isset($goal->payoffdebts) || $goal->payoffdebts == '') {
                            // Load All Active Debts that affect the goal
                            $debtArray = $debts;
                        } else {
                            // Load Active Custom Debts that affect the goal
                            $debtDetails = explode(",", $goal->payoffdebts);
                            foreach ($debts as $debt) {
                                foreach ($debtDetails as $debtId) {
                                    if ($debt->id == $debtId) {
                                        $debtArray[] = $debt;
                                    }
                                }
                            }
                        }

                        $paymentInfo = explode("id='paymentInfo' type='hidden'", $actionstep->flexi1);
                        $linkname = explode("'", $paymentInfo[1]);
                        $payment = $linkname[1];
                        $paymentInfo = explode(",", $payment);
                        if (count($paymentInfo) != count($debtArray)) {
                            // Scenario 2: A new debt => new advice only if the new debt is now part of the goal (if goal has all debts selected)
                            // Scenario 4: Hiding/Deleting debt that affects goal => new advice (if goal had that debt included)
                            $newAdvice = true;
                        } else {
                            $index = 0;
                            foreach ($debtArray as $debt) {
                                $debtValue = explode("|", $paymentInfo[$index]);
                                if ($debt->id != $debtValue[0]) {
                                    // Scenario 6: If balance order changes => we currently do lowest to highest balance but if that order changes => new advice
                                    $newAdvice = true;
                                    break;
                                } else if ($debt->balowed < $debtValue[1]) {
                                    // Scenario 5: Balance goes below suggested payment amount => new advice
                                    $newAdvice = true;
                                    break;
                                } else if ($debt->amtpermonth != $debtValue[1]) {
                                    $name = $debt->name;
                                    if (!isset($debt->name) || $debt->name == '') {
                                        $name = $debt->getDefaultName($debt->type);
                                    }
                                    $debtName = '<a href="#" id="' . $debt->id . 'addDebts" class="addDebts actionStep">' . $name . '</a>';
                                    if ($debt->amtpermonth > $debtValue[1]) {
                                        $debtInfo .= "Reduce monthly payments to $" . number_format($debtValue[1]) . " for " . $debtName . " ($" . number_format($debt->balowed) . ").<br>";
                                    } else {
                                        $debtInfo .= "Increase monthly payments to $" . number_format($debtValue[1]) . " for " . $debtName . " ($" . number_format($debt->balowed) . ").<br>";
                                    }
                                }
                                $index++;
                            }
                        }
                    }
                    if ($newAdvice) {
                        $result = $this->CheckDebtGoals($goal, $debts);
                        if ($result["needsActionStep"]) {
                            $this->SaveASteps($uid, $this->payOffDebts, '', $result["title"]);
                        }
                    } else if ($actionstep && $actionstep->flexi1) {
                        $newText = '';
                        $paymentInfo = explode("<br><br>", $actionstep->flexi1);
                        $paymentInfo[0] = $paymentInfo[0] . "<br>";
                        $paymentInfo[1] = $debtInfo;
                        $newText = implode("<br>", $paymentInfo);
                        $this->SaveASteps($uid, $this->payOffDebts, '', $newText);
                    }
                }

                /*                 * *********************************************** */

                /*                 * *************************************************
                 * Retirement Goal Plan / Increase Savings
                 * ************************************************ */
                $newgoalDetails = $goalObj->findAll(array('condition' => "user_id = :user_id AND goalstatus=1 and goaltype <> 'DEBT' ORDER by goalpriority", 'params' => array("user_id" => $uid), 'select' => 'id,goalname, goaltype, goalamount,goalpriority,saved,monthlyincome, permonth, goalstartdate, goalenddate, goalassumptions_1'));
                $assets = $assetObj->findAll(array('condition' => "user_id = :user_id and type in('BANK','IRA','CR','BROK','EDUC') AND status=0 order by type desc", 'params' => array("user_id" => $uid), 'select' => 'id,name,balance,type,assettype,contribution,empcontribution'));

                if (isset($newgoalDetails) && !empty($newgoalDetails) && isset($assets) && !empty($assets)) {
                    $personal = $usrinfoObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('age', 'retirementage')));
                    $result = $this->checkGoals($newgoalDetails, $assets, $currentAge, $userGrowthRate, $personal, $grossincome, $uid);
                    if ($result["needsActionStep"] && $result["goaltype"] == 'RETIREMENT') {
                        $this->SaveASteps($uid, $this->retirementGoalPlan, '', $result['title']);
                    } else if ($result["needsActionStep"]) {
                        $actionstep = Actionstep::model()->find(array('condition' => "user_id=:user_id AND actionid=:advisor_id and actionstatus in ('0','1','2','3')", 'params' => array("user_id" => $uid, "advisor_id" => $this->increaseSavings)));
                        $goalid = 0;
                        $showSavingsStep = true;
                        if ($actionstep && $actionstep->flexi1) {
                            $goalInfo = explode('addGoals"', $actionstep->flexi1);
                            $linkname = explode('"', $goalInfo[0]);
                            $goalid = $linkname[3];
                        }
                        if ($actionstep && $goalid != 0 && $goalid != $result["goalid"]) {
                            $showSavingsStep = false;
                        }
                        if ($showSavingsStep) {
                            $this->SaveASteps($uid, $this->increaseSavings, '', $result['title']);
                        }
                    }
                } else if (isset($newgoalDetails) && !empty($newgoalDetails)) {
                    $goals = Goal::model()->findAll(array('condition' => "user_id = :user_id AND goalstatus=1 and goaltype <> 'DEBT' ORDER by goalpriority", 'params' => array("user_id" => $uid), 'select' => 'id, goalamount, saved, status'));
                    foreach ($goals as $goal) {
                        $status = 'Needs Attention';
                        if (round($goal->saved) >= round($goal->goalamount)) {
                            $status = 'On Track';
                        }
                        if ($goal->status != $status) {
                            $goal->status = $status;
                            $goal->save();
                        }
                    }
                }

                /*                 * *********************************************** */

// Create Action step : Tell us more about Insurance (#29)
                $title = '';
                $insuranceNames = array();
                $accounts = array('LIFE' => false, 'DISA' => false, 'LONG' => false);
                $insurDet = $insurObj->findAll("user_id=:user_id AND status=0 and type in ('LIFE', 'DISA', 'LONG')", array("user_id" => $uid));
                if ($insurDet) {
                    foreach ($insurDet as $irow) {
                        $accounts[$irow->type] = true;
                    }
                }

                $moreLifeInsurance = false;
                $moreDisabilityInsurance = false;
                $moreLongTermCareInsurance = false;
                $esrow = $estiObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('whichyouhave')));
                foreach ($accounts as $key => $value) {
                    switch ($key) {
                        case 'LIFE':
                            if (isset($esrow->whichyouhave) && strpos($esrow->whichyouhave, 'Life') !== false && !$accounts[$key]) {
                                //$irow = new Insurance();
                                $irow = $insurObj;
                                $name = $irow->getDefaultName($key);
                                $insuranceNames[] = '<a href="#" id="addInsurancelifeinsurance" class="addInsurance actionStep">' . $name . '</a>';
                                $moreLifeInsurance = true;
                            }
                            break;
                        case 'DISA':
                            if (isset($esrow->whichyouhave) && strpos($esrow->whichyouhave, 'Disability') !== false && !$accounts[$key]) {
                                //$irow = new Insurance();
                                $irow = $insurObj;
                                $name = $irow->getDefaultName($key);
                                $insuranceNames[] = '<a href="#" id="addInsurancedisabilityinsurance" class="addInsurance actionStep">' . $name . '</a>';
                                $moreDisabilityInsurance = true;
                            }
                            break;
                        case 'LONG':
                            if (isset($esrow->whichyouhave) && strpos($esrow->whichyouhave, 'Long Term Care') !== false && !$accounts[$key]) {
                                //$irow = new Insurance();
                                $irow = $insurObj;
                                $name = $irow->getDefaultName($key);
                                $insuranceNames[] = '<a href="#" id="addInsurancelongtermcareinsurance" class="addInsurance actionStep">' . $name . '</a>';
                                $moreLongTermCareInsurance = true;
                            }
                            break;
                    }
                }

                if (count($insuranceNames)) {
                    $title = implode('  <br>', $insuranceNames);
                    $this->SaveASteps($uid, $this->moreInsurance, '', '<br>' . $title . '<br>');
                }

// Create Action step : Life Insurance - Increase Coverage by $amount (#2) & Life Insurance - Get Policy for $amount of Coverage & (#3)
                $qfc = new CDbCriteria();
                $qfc->condition = "user_id=:user_id AND status=0 AND type=:type AND insurancefor=:insur";
                $qfc->limit = 1;
                $qfc->params = array('user_id' => $uid, 'type' => 'LIFE', 'insur' => '80');
                $qfc->addInCondition('beneficiary', array('81', '82', '83'));
                $insurDetails = $insurObj->findAll($qfc);
                if (isset($acusrinsurCoverage)) {
                    $acusrinsurCoverage = ceil($acusrinsurCoverage / 10000) * 10000;
                    $acusrinsurCoverage = number_format($acusrinsurCoverage);

                    if (isset($insurDetails) && !empty($insurDetails)) {
                        if ($acusrinsurCoverage > 0) {
                            $this->SaveASteps($uid, $this->increaseLifeInsurance, $acusrinsurCoverage);
                        }
                        $year = date('Y');
                        $diff = $year - $lifeInsuranceReviewYear;
                        if ($lifeInsuranceReviewYear > 0 && $diff > 1) {
                            $this->SaveASteps($uid, $this->reviewLifeInsurance);
                        }
                    } else {
                        if ($acusrinsurCoverage > 0 && !$moreLifeInsurance) {
                            $this->SaveASteps($uid, $this->getLifeInsurance, $acusrinsurCoverage);
                        }
                    }
                }

// Create Action step : Disability Insurance - Increase Coverage by $amount (#35) & Disability Insurance - Get Policy for $amount of Coverage (#36)
                $qfc->condition = "user_id=:user_id AND status=0 AND type=:type"; #
                $qfc->params = array('user_id' => $uid, 'type' => 'DISA');
                $insurDetails = $insurObj->findAll($qfc);

                if (isset($disabilityInsurCoverage)) {
                    $disabilityInsurCoverage = ceil($disabilityInsurCoverage / 100) * 100;
                    $disabilityInsurCoverage = number_format($disabilityInsurCoverage);

                    if (isset($insurDetails) && !empty($insurDetails)) {
                        if ($disabilityInsurCoverage > 0) {
                            $this->SaveASteps($uid, $this->increaseDisabilityInsurance, $disabilityInsurCoverage);
                        }
                        $year = date('Y');
                        $diff = $year - $disabilityInsuranceReviewYear;
                        if ($disabilityInsuranceReviewYear > 0 && $diff > 1) {
                            $this->SaveASteps($uid, $this->reviewDisabilityInsurance);
                        }
                    } else {
                        if ($disabilityInsurCoverage > 0 && !$moreDisabilityInsurance) {
                            $this->SaveASteps($uid, $this->getDisabilityInsurance, $disabilityInsurCoverage);
                        }
                    }
                }

// Create Action step : Consider Your Estate Planning Needs (#46)
                $mrow = $miscObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('misctrust')));
                if (isset($spouseAge) && isset($child1Age) && isset($child2Age) && isset($child3Age) && isset($child4Age)) {
                    if ($spouseAge > 0 || $child1Age > 0 || $child2Age > 0 || $child3Age > 0 || $child4Age > 0) {
                        if ((!isset($mrow->misctrust) || $mrow->misctrust != '1')) {
                            if (!isset($esrow->whichyouhave) || (strpos($esrow->whichyouhave, 'Will') === false && strpos($esrow->whichyouhave, 'Trust') === false)) {
                                $this->SaveASteps($uid, $this->estatePlanning);
                            }
                        }
                    } else {
                        if (isset($userSumOfAssets) && isset($userSumOfOtherAssets)) {
                            $sumOfAssets = $userSumOfAssets + $userSumOfOtherAssets;
                            $netWorth = $sumOfAssets - $userSumOfDebts;
                            if ($netWorth > 100000) {
                                if ((!isset($mrow->misctrust) || $mrow->misctrust != '1')) {
                                    if (!isset($esrow->whichyouhave) || (strpos($esrow->whichyouhave, 'Will') === false && strpos($esrow->whichyouhave, 'Trust') === false)) {
                                        $this->SaveASteps($uid, $this->estatePlanning);
                                    }
                                }
                            }
                        }
                    }
                }

                $estatePlanning_step = false;
                $estatePlanningStep = ActionStep::model()->count("user_id=:user_id AND actionstatus IN ('0', '2', '3') AND actionid=:actionid", array("user_id" => $uid, "actionid" => $this->estatePlanning));
                if ($estatePlanningStep > 0) {
                    $estatePlanning_step = true;
                }

// Miscellaneous section
                $mrow = $miscObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid)));

// Create Action step : Tell us more about Will & Trust (#30)
                $miscTest = isset($mrow) ? $mrow->misctrust : -1;
                if (isset($esrow->whichyouhave) && (strpos($esrow->whichyouhave, 'Will') !== false || strpos($esrow->whichyouhave, 'Trust') !== false) && (($miscTest == '') || ($miscTest == -1))) {
                    $this->SaveASteps($uid, $this->moreWillAndTrust);
                }
                if (isset($mrow->user_id)) {
                    // Create Action step : Update Will and Other Estate Planning Docs (#14)
                    if (isset($mrow->miscreviewyear) && $mrow->miscreviewyear != 'Year' && $mrow->miscreviewyear != '') {
                        if ((date('Y') - $mrow->miscreviewyear) > 4) {
                            $this->SaveASteps($uid, $this->updateWillAndEstatePlanning);
                        }
                    }
                    // Create Action step : Obtain and Review Current Credit Score (#23) & Consider Strategies to Improve Credit Score (#57)
                    if (isset($mrow->morecreditscore)) {
                        if ($mrow->morecreditscore == 0 || $mrow->morecreditscore == '') {
                            $this->SaveASteps($uid, $this->reviewCreditScore);
                        } else if ($mrow->morecreditscore <= 699 && $mrow->morecreditscore >= 1) {
                            $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->improveCreditScore), 'select' => array('articles')));
                            $articleIds = array();
                            $articleUrl = array();
                            $articleName = array();
                            $narray = explode('|', $actionStepMeta->articles);
                            // Pull the article information from the articles column of actionstepmeta table
                            foreach ($narray as $k => $nval) {
                                $artdiv = explode('#', $nval);
                                $articleIds[] = $artdiv[2];
                                $articleUrl[$artdiv[2]] = $artdiv[1];
                                $articleName[$artdiv[2]] = $artdiv[0];
                            }

                            // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                            if (!empty($articleIds)) {
                                $artids = implode(',', $articleIds);
                                $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                if (isset($mediaObj) && !empty($mediaObj)) {
                                    foreach ($mediaObj as $media) {
                                        if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                            unset($articleIds[$key]);
                                        }
                                    }
                                }
                            }

                            // Show the articles not read to user
                            if (!empty($articleIds)) {
                                $articleNames = array();
                                $articleNum = 1;
                                foreach ($articleIds as $articleId) {
                                    $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->improveCreditScore .
                                            '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                                    $articleNum++;
                                }
                                $artnames = implode(' <br>', $articleNames);
                                $this->SaveASteps($uid, $this->improveCreditScore, '', '<br>' . $artnames . '<br>');
                            }
                        }
                    } else {
                        $this->SaveASteps($uid, $this->reviewCreditScore);
                    }
                    // Create Action step : Fill in Tax Section (#27)
                    if ($mrow->taxpay == '' || $mrow->taxbracket == '' || $mrow->taxbracket == '4' || $mrow->taxvalue == '' || $mrow->taxvalue == '3' || $mrow->taxcontri == '' || $mrow->taxStdOrItemDed == '') {
                        $this->SaveASteps($uid, $this->fillMiscTax);
                    }


                    /**                     * ******************************************************
                     * Create Action step : Fill in Estate Planning (#28)
                     * ****************************************************** */
                    if (!$estatePlanning_step) {
                        $resultArray_EstatePlanning = $this->fillMiscEstatePlanningStepNeeded($mrow, $sengineObj);
                        if (!$resultArray_EstatePlanning['completeActionStep'] && $resultArray_EstatePlanning['needsActionStep']) {
                            $this->SaveASteps($uid, $this->fillMiscEstatePlanning);
                        }
                    }
                    /*                     * ***************************************************** */

                    /* Added by Rajeev for Consider Charitable Donations
                     * Create Action step : Consider Charitable Donations (#79)
                     */
                    if ($mrow->taxpay == '0' && $mrow->morecharity == '1' && $mrow->taxStdOrItemDed != '0' && ($mrow->taxvalue == '1' || $mrow->taxvalue == '2')) {
                        $this->SaveASteps($uid, $this->considerCharitableDonations);
                    }
                } else {
                    $this->SaveASteps($uid, $this->reviewCreditScore);
                }


                // Create Action step :  Create Informational Sheet & Location of Hidden Assets (#77)
                if (!$informationListOfHiddenAsset) {
                    $this->SaveASteps($uid, $this->createInformationalSheet);
                }

                // Create Action step : Consider Setting Up Your Investment Portfolios on Auto-Rebalance (#42)
                if (!$investmentAutomatically) {
                    $this->SaveASteps($uid, $this->addAutoRebalance);
                }

                $mdDet = $debtObj->findAll(array('condition' => "user_id = :user_id AND context = 'MANUAL' AND status=0", 'params' => array("user_id" => $uid), 'select' => 'balowed,name,type'));
                $maDet = $assetObj->findAll(array('condition' => "user_id = :user_id AND context = 'MANUAL' AND status=0 AND type in ('BANK', 'IRA', 'CR', 'BROK', 'EDUC')", 'params' => array("user_id" => $uid), 'select' => 'balance,name,type'));
                $miDet = $insurObj->findAll(array('condition' => "user_id = :user_id AND context = 'MANUAL' AND status=0 AND type in ('LIFE', 'DISA', 'LONG')", 'params' => array("user_id" => $uid), 'select' => 'annualpremium,name,type'));

                $aaDet = $assetObj->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0 AND type in ('BANK', 'IRA', 'CR', 'BROK', 'EDUC')", 'params' => array("user_id" => $uid), 'select' => 'balance'));
                $adDet = $debtObj->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $uid), 'select' => 'balowed'));
                $aiDet = $insurObj->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0 AND type in ('LIFE', 'DISA', 'LONG')", 'params' => array("user_id" => $uid), 'select' => 'annualpremium'));

                $userPerDetails = Userpersonalinfo::model()->find(array("condition" => "user_id=:user_id", "params" => array("user_id" => $uid), "select" => 'connectAccountPreference'));
                if ($userPerDetails->connectAccountPreference != '1' && empty($mdDet) && empty($maDet) && empty($miDet) && empty($adDet) && empty($aaDet) && empty($aiDet)) {
// Create Action step : Consider Auto-Linking All Financial Accounts (#21)
                    $this->SaveASteps($uid, $this->connectAccountAuto);
                } else if ($userPerDetails->connectAccountPreference != '1' && empty($adDet) && empty($aaDet) && empty($aiDet)) {
// Create Action step : Connect Accounts (#1)
                    $accountNames = array();
                    if (!empty($mdDet)) {
                        foreach ($mdDet as $debt) {
                            if (!isset($debt->name) || $debt->name == '') {
                                $debt->name = $debt->getDefaultName($debt->type);
                            }
                            $accountNames[] = '<div>' . $debt->name . '</div>';
                        }
                    }
                    if (!empty($maDet)) {
                        foreach ($maDet as $asset) {
                            if (!isset($asset->name) || $asset->name == '') {
                                $asset->name = $asset->getDefaultName($asset->type);
                            }
                            $accountNames[] = '<div>' . $asset->name . '</div>';
                        }
                    }
                    if (!empty($miDet)) {
                        foreach ($miDet as $insurance) {
                            if (!isset($insurance->name) || $insurance->name == '') {
                                $insurance->name = $insurance->getDefaultName($insurance->type);
                            }
                            $accountNames[] = '<div>' . $insurance->name . '</div>';
                        }
                    }

                    $count = count($accountNames);
                    $title = '<div class="clearOnly twentypx"></div>';
                    if ($count > 10) {
                        $slice = ceil($count / 3);

                        $title .= '<div class="floatL" style="width:225px">' . implode('', array_slice($accountNames, 0, $slice)) . '</div>';
                        $title .= '<div class="floatL" style="width:225px">' . implode('', array_slice($accountNames, $slice, $slice)) . '</div>';
                        $title .= '<div class="floatL" style="width:225px">' . implode('', array_slice($accountNames, $slice * 2, $slice)) . '</div>';
                        $title .= '<div class="clearOnly"></div>';
                    } else {
                        $title .= implode('', $accountNames);
                    }

                    $this->SaveASteps($uid, $this->connectAccountManual, '', '<br>' . $title . '<br>');
                }

                $assetData = parent::getAssetData($uid);
                $assets = $assetData[0];
                $insurances = $assetData[1];
                $pertrack = $assetData[2];

                $excessAssets = parent::CalculatePoint10($assets, $insurances, $pertrack);
                if (count($excessAssets) > 0) {
                    $assetNames = array();
                    foreach ($excessAssets as $excessAsset) {
                        $found = false;
                        if($assets != 0) {
                            foreach ($assets as $asset) {
                                if ($asset->id == $excessAsset["id"] && $asset->balance) {
                                    $found = true;
                                    if (!isset($asset->name) || $asset->name == '') {
                                        $asset->name = $asset->getDefaultName($asset->type);
                                    }
                                    if (!isset($excessAsset["ticker"])) {
                                        $assetNames[] = $asset->name . '<br>';
                                    } else {
                                        $assetNames[] = 'Ticker ' . $excessAsset["ticker"] . " for " . $asset->name . '<br>';
                                    }
                                    break;
                                }
                            }
                        }
                        if (!$found) {
                            if($insurances != 0) {
                                foreach ($insurances as $insurance) {
                                    if ($insurance->id == $excessAsset["id"] && $insurance->cashvalue) {
                                        $found = true;
                                        if (!isset($insurance->name) || $insurance->name == '') {
                                            $insurance->name = 'Life Insurance';
                                        }
                                        $assetNames[] = $insurance->name . '<br>';
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if (count($assetNames)) {
                        $title = implode('', $assetNames);
                        $this->SaveASteps($uid, $this->considerConcentrationOfAssets, '', '<br>' . $title . '<br>');
                    }
                }


// Create Action step : Give us more information on an asset (#25)
                $assetNames = array();
                $title = '';
                $infoDetail = $usrinfoObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('retirementstatus')));
                $fillmaDet = $assetObj->findAll(array('condition' => "user_id = :user_id AND status=0", 'params' => array("user_id" => $uid), 'select' => array('id', 'name', 'type', 'balance', 'invpos')));
                if (count($fillmaDet) >= 1) {
                    foreach ($fillmaDet as $farow) {
                        switch ($farow->type) {
                            case 'BANK':
                                if (!isset($farow->balance) || $farow->balance == '') {
                                    if (!isset($farow->name) || $farow->name == '') {
                                        $farow->name = $farow->getDefaultName($farow->type);
                                    }
                                    $assetNames[] = '<a href="#" id="' . $farow->id . 'addAssetsCash" class="addAssets actionStep">' . $farow->name . '</a>';
                                }
                                break;
                            case 'IRA':
                            case 'CR':
                            case 'BROK':
                                if (!isset($farow->balance) || $farow->balance == '' || !isset($farow->invpos) || $farow->invpos == '' || $farow->invpos == '[]') {
                                    if (!isset($farow->name) || $farow->name == '') {
                                        $farow->name = $farow->getDefaultName($farow->type);
                                    }
                                    $assetNames[] = '<a href="#" id="' . $farow->id . 'addAssetsInvestment" class="addAssets actionStep">' . $farow->name . '</a>';
                                }
                                break;
                            case 'EDUC':
                            case 'PROP':
                            case 'VEHI':
                            case 'PENS':
                            case 'SS':
                            case 'BUSI':
                            case 'OTHE':
                                if (!isset($farow->balance) || $farow->balance == '') {
                                    if (!isset($farow->name) || $farow->name == '') {
                                        $farow->name = $farow->getDefaultName($farow->type);
                                    }
                                    $assetNames[] = '<a href="#" id="' . $farow->id . 'addAssetsOther" class="addAssets actionStep">' . $farow->name . '</a>';
                                }
                                break;
                        }
                    }
                    if (count($assetNames)) {
                        $title = implode('  <br>', $assetNames);
                        $this->SaveASteps($uid, $this->moreAsset, '', '<br>' . $title . '<br>');
                    }
                }
// Create Action step : Give us more information on a debt (#26)
                $debtNames = array();
                $title = '';
                $fillmdDet = $debtObj->findAll(array('condition' => 'user_id = :user_id AND status=0 AND monthly_payoff_balances=0', 'params' => array('user_id' => $uid), 'select' => array('id', 'type', 'name', 'balowed', 'apr')));
                if (count($fillmdDet) >= 1) {
                    foreach ($fillmdDet as $fdrow) {
                        if (!isset($fdrow->balowed) || $fdrow->balowed == '' || !isset($fdrow->apr) || $fdrow->apr == '') {
                            if (!isset($fdrow->name) || $fdrow->name == '') {
                                $fdrow->name = $fdrow->getDefaultName($fdrow->type);
                            }
                            $debtNames[] = '<a href="#" id="' . $fdrow->id . 'addDebts" class="addDebts actionStep">' . $fdrow->name . '</a>';
                        }
                    }
                    if (count($debtNames)) {
                        $title = implode('  <br>', $debtNames);
                        $this->SaveASteps($uid, $this->moreDebt, '', '<br>' . $title . '<br>');
                    }
                }
// Create Action step : Identify Financial Goals, Cost of Goal, Time Period to achieve goal (#15) & Set up other accounts like - CDs, Checking, etc. (#58)
                $aaDet = $assetObj->findAll(array('condition' => "user_id = :user_id AND status=0", 'params' => array("user_id" => $uid), 'select' => 'balance'));
                $gochk = 0;
                $qgc = new CDbCriteria();
                $qgc->condition = "user_id = :user_id AND goalstatus=1 AND goalpriority > 0";
                $qgc->select = 'goalpriority,goalamount';
                $qgc->limit = 10;
                $qgc->params = array('user_id' => $uid);
                $goalDetails = $goalsObj->findAll($qgc);
                if (isset($goalDetails) && !empty($goalDetails)) {
                    if (empty($aaDet)) {
                        $this->SaveASteps($uid, $this->setupGoal);
                    }
                    foreach ($goalDetails as $grow) {
                        if ($grow->goalpriority > 0) {
                            $gochk = 1;
                        }
                    }
                    if ($gochk == 0) {
                        $this->SaveASteps($uid, $this->addGoal);
                    }
                } else {
                    $this->SaveASteps($uid, $this->addGoal);
                }
// Create Action step : Consider Setting Up a Goal of Paying Off Consumer Debt (#16) & Debt Improvement Options (#18) & Knowledge of Debts and Liabilities (#19)
                $qgc->condition = "user_id = :user_id AND goaltype=:type AND goalstatus=1";
                $qgc->params = array('user_id' => $uid, 'type' => 'DEBT');
                $goalDetails = $goalsObj->findAll($qgc);
                $mdebtDet = $debtObj->find(array('condition' => "user_id=:user_id AND type <> 'MORT' AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid), 'select' => 'SUM(amtpermonth) AS total'));
                if (isset($mdebtDet->total)) {
                    $new_gross = (20 / 100) * $grossincome;
                    if ($mdebtDet->total >= $new_gross) {
                        if (!isset($goalDetails) || empty($goalDetails)) {
                            $this->SaveASteps($uid, $this->setGoal);
                        }

                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->debtImprovementOptions), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->debtImprovementOptions);
                        }

                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->knowledgeDebtsAndLiabilities), 'select' => array('articles')));

                        // Pull the video information from the articles column of actionstepmeta table
                        $articleId = "";
                        $narray = $actionStepMeta->articles;
                        $artdiv = explode('#', $narray);
                        if (isset($artdiv[2])) {
                            $articleId = $artdiv[2];
                        }

                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                        $showVideo = false;
                        if ($articleId != "") {
                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                            if ($articleCount == 0) {
                                $showVideo = true;
                            }
                        }

                        // Show the video not watched to the user
                        if ($showVideo) {
                            $this->SaveASteps($uid, $this->knowledgeDebtsAndLiabilities);
                        }
                    }
                }

                // Create Step for #52 - Consolidate Loans
                if ($point5 != 13 && $point5 != 25) {
                    $this->SaveASteps($uid, $this->consolidateLoans);
                }

// Create Action step : Consider Refinance Options on Consumer Debt (#53)
                $consumerDebtDetails = $debtObj->findAll(array('condition' => "user_id=:user_id AND type=:debttype AND apr > 10 AND mortgagetype <> '37' AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid, "debttype" => "LOAN"), 'select' => array('id', 'name', 'type')));
                $cardNames = array();
                if (isset($consumerDebtDetails) && !empty($consumerDebtDetails)) {
                    foreach ($consumerDebtDetails as $consumerDebtRow) {
                        if (!isset($consumerDebtRow->name) || $consumerDebtRow->name == '') {
                            $consumerDebtRow->name = $consumerDebtRow->getDefaultName($consumerDebtRow->type);
                        }
                        $cardNames[] = '<a href="#" id="' . $consumerDebtRow->id . 'addDebts" class="addDebts actionStep">' . $consumerDebtRow->name . '</a>';
                    }
                    if ($cardNames) {
                        $cnam = implode(' <br>', $cardNames);
                        $this->SaveASteps($uid, $this->refinanceConsumerDebts, '', '<br>' . $cnam . '<br>');
                    }
                }


// Create Action step : Refinance Credit Card(s) (#85)
                $ccDetails = $debtObj->findAll(array('condition' => "user_id=:user_id AND type=:debttype AND apr > 10 AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid, "debttype" => "CC"), 'select' => array('id', 'name', 'type')));
                $cardNames = array();
                if (isset($ccDetails) && !empty($ccDetails)) {
                    foreach ($ccDetails as $crow) {
                        if (!isset($crow->name) || $crow->name == '') {
                            $crow->name = $crow->getDefaultName($crow->type);
                        }
                        $cardNames[] = '<a href="#" id="' . $crow->id . 'addDebts" class="addDebts actionStep">' . $crow->name . '</a>';
                    }
                    if ($cardNames) {
                        $cnam = implode(' <br>', $cardNames);
                        $this->SaveASteps($uid, $this->refinanceCreditCard, '', '<br>' . $cnam . '<br>');
                    }
                }

// Create Action step : Consider Refinance Options on Car Loan(s) (#95)
                $carLoanDetails = $debtObj->findAll(array('condition' => "user_id=:user_id AND type=:debttype AND apr > 5 AND status=0 AND mortgagetype='37' AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid, "debttype" => "LOAN"), 'select' => array('id', 'name', 'type')));

                $carLoanNames = array();
                if (isset($carLoanDetails) && !empty($carLoanDetails)) {
                    foreach ($carLoanDetails as $carrow) {
                        if (!isset($carrow->name) || $carrow->name == '') {
                            $carrow->name = $carrow->getDefaultName($carrow->type);
                        }
                        $carLoanNames[] = '<a href="#" id="' . $carrow->id . 'addDebts" class="addDebts actionStep">' . $carrow->name . '</a>';
                    }
                    if ($carLoanNames) {
                        $carnam = implode(' <br>', $carLoanNames);
                        $this->SaveASteps($uid, $this->refinanceCarLoan, '', '<br>' . $carnam . '<br>');
                    }
                }

// Create Action step : Consider Refinance Options on Mortgage Debt (#17)
//  30  year should be 4.75%, 15 year 3.75%, and 5/1 should be 3.75%.  This is all based on the average rates compiled by BankRate.
                $mortDetails = $debtObj->findAll(array('condition' => "user_id=:user_id AND type=:debttype AND status=0 AND ((mortgagetype=33 AND apr > 4.75) OR (mortgagetype=34 AND apr > 3.75) OR (mortgagetype=35 AND apr > 3.75))", 'params' => array("user_id" => $uid, "debttype" => "MORT"), 'select' => array('id', 'name', 'type')));
                $mortNames = array();
                if (isset($mortDetails) && !empty($mortDetails)) {
                    foreach ($mortDetails as $mrow) {
                        if (!isset($mrow->name) || $mrow->name == '') {
                            $mrow->name = $mrow->getDefaultName($mrow->type);
                        }
                        $mortNames[] = '<a href="#" id="' . $mrow->id . 'addDebts" class="addDebts actionStep">' . $mrow->name . '</a>';
                    }
                    if ($mortNames) {
                        $mnam = implode(' <br>', $mortNames);
                        $this->SaveASteps($uid, $this->mortgageDebt, '', '<br>' . $mnam . '<br>');
                    }
                }

                // Check if user has CR, and and his total contributions. If not maxed, then can't show the next 4 steps
                $showSteps = true;
                $crRows = $assetObj->findAll(array('condition' => 'user_id = :user_id AND type = "CR" and status = 0',
                    'params' => array('user_id' => $uid), 'select' => 'contribution'));
                $currentContribution = 0;
                if (isset($crRows) && !empty($crRows)) {
                    foreach ($crRows as $crRow) {
                        $currentContribution += $crRow->contribution * 12;
                    }
                    if ($currentAge <= 49 && $currentContribution < $this->crMax) {
                        $showSteps = false;
                    } else if ($currentAge >= 50 && $currentContribution < $this->over50CrMax) {
                        $showSteps = false;
                    }
                }

                if ($showSteps) {
                    // miscRow is used for four steps: maximizeRothIra (#49), maximizeTraditionalIra (#43),
                    // decreaseW4TaxWithholding (#47), increaseW4TaxWithholding (#91)
                    $miscRow = $miscObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid),
                        'select' => array('user_id', 'taxpay', 'taxvalue')));

                    $maximizeStep = false;
                    $married = $usrinfoObj->count(array('condition' => "user_id=:user_id AND maritalstatus=:mstat", 'params' => array("user_id" => $uid, "mstat" => "Married")));
                    // Check marital status and income levels.
                    $spouseIncomeCheck = false;
                    $householdIncome = $grossincome * 12;
                    if ($married > 0 && $householdIncome < 178000) {
                        $spouseIncomeCheck = true;
                    }
                    if ($married <= 0 && $householdIncome < 112000) {
                        $spouseIncomeCheck = true;
                    }
                    if ($spouseIncomeCheck == true) {
                        // Create Action step: Maximize Contribution to Roth IRA (#49)
                        if ($miscRow && ($miscRow->taxpay != 0 || ($miscRow->taxvalue != '1' && $miscRow->taxvalue != '2'))) {

                            $rothRow = $assetObj->find(array('condition' => 'user_id = :user_id AND type = "IRA" and status = 0',
                                'params' => array('user_id' => $uid), 'select' => 'SUM(contribution) AS contribution'));
                            $currentContribution = 0;
                            if ($rothRow) {
                                $currentContribution = $rothRow->contribution * 12;
                            }

                            $incDetails = $incomObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid), 'select' => array('gross_income', 'spouse_income')));
                            $currentIncome = 0;
                            if ($incDetails) {
                                $currentIncome = ($incDetails->gross_income + $incDetails->spouse_income) * 12;
                            }

                            $recommendedContribution = 0;
                            if ($currentAge <= 49) {
                                if ($currentIncome > $this->iraMax) {
                                    $recommendedContribution = $this->iraMax - $currentContribution;
                                } else {
                                    $recommendedContribution = $currentIncome - $currentContribution;
                                }
                            } else {
                                if ($currentIncome > $this->over50IraMax) {
                                    $recommendedContribution = $this->over50IraMax - $currentContribution;
                                } else {
                                    $recommendedContribution = $currentIncome - $currentContribution;
                                }
                            }
                            $monthlyRecommendedContribution = number_format(round($recommendedContribution / 120) * 10);
                            if ($monthlyRecommendedContribution > 0) {
                                $monthlyRecommendedContribution = number_format(round(($recommendedContribution + $currentContribution) / 120) * 10);
                                $actMetaObj = new Actionstepmeta();
                                $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->maximizeRothIra), 'select' => array('articles')));
                                $articleIds = array();
                                $articleUrl = array();
                                $articleName = array();
                                $narray = explode('|', $actionStepMeta->articles);
                                // Pull the article information from the articles column of actionstepmeta table
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    $articleIds[] = $artdiv[2];
                                    $articleUrl[$artdiv[2]] = $artdiv[1];
                                    $articleName[$artdiv[2]] = $artdiv[0];
                                }

                                // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                                if (!empty($articleIds)) {
                                    $artids = implode(',', $articleIds);
                                    $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    if (isset($mediaObj) && !empty($mediaObj)) {
                                        foreach ($mediaObj as $media) {
                                            if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                unset($articleIds[$key]);
                                            }
                                        }
                                    }
                                }

                                // Show the articles not read to user
                                $articleNames = array();
                                if (!empty($articleIds)) {
                                    $articleNum = 1;
                                    foreach ($articleIds as $articleId) {
                                        $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->maximizeRothIra .
                                                '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                                        $articleNum++;
                                    }
                                }
                                $artnames = implode(' <br>', $articleNames);
                                $this->SaveASteps($uid, $this->maximizeRothIra, $monthlyRecommendedContribution, '<br>' . $artnames . '<br>');
                                $maximizeStep = true;
                            }
                        }
                    }

                    // Create Action step: Consider Decreasing Tax Withholding Amount using W4 Form at Work (#47)
                    if (!$maximizeStep) {
                        if ($miscRow && $miscRow->taxpay == 1 && ($miscRow->taxvalue == '1' || $miscRow->taxvalue == '2')) {
                            $this->SaveASteps($uid, $this->decreaseW4TaxWithholding);
                        }
                    }

                    $maximizeStep = false;
                    // Create Action step: Maximize Contribution to Traditional IRA (#43)
                    if ($miscRow && $miscRow->taxpay == 0 && ($miscRow->taxvalue == '1' || $miscRow->taxvalue == '2')) {
                        $iraSum = $assetObj->find(array('condition' => 'user_id = :user_id AND type = "IRA" and status = 0',
                            'params' => array('user_id' => $uid), 'select' => 'SUM(contribution) AS contribution'));
                        $currentContribution = 0;
                        if ($iraSum) {
                            $currentContribution = $iraSum->contribution * 12;
                        }

                        $incDetails = $incomObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid), 'select' => array('gross_income', 'spouse_income')));
                        $currentIncome = 0;
                        if ($incDetails) {
                            $currentIncome = ($incDetails->gross_income + $incDetails->spouse_income) * 12;
                        }

                        $recommendedContribution = 0;
                        if ($currentAge <= 49) {
                            if ($currentIncome > $this->iraMax) {
                                $recommendedContribution = $this->iraMax - $currentContribution;
                            } else {
                                $recommendedContribution = $currentIncome - $currentContribution;
                            }
                        } else {
                            if ($currentIncome > $this->over50IraMax) {
                                $recommendedContribution = $this->over50IraMax - $currentContribution;
                            } else {
                                $recommendedContribution = $currentIncome - $currentContribution;
                            }
                        }
                        $monthlyRecommendedContribution = number_format(round($recommendedContribution / 120) * 10);
                        if ($monthlyRecommendedContribution > 0) {
                            $monthlyRecommendedContribution = number_format(round(($recommendedContribution + $currentContribution) / 120) * 10);
                            $actMetaObj = new Actionstepmeta();
                            $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->maximizeTraditionalIra), 'select' => array('articles')));
                            $articleIds = array();
                            $articleUrl = array();
                            $articleName = array();
                            $narray = explode('|', $actionStepMeta->articles);
                            // Pull the article information from the articles column of actionstepmeta table
                            foreach ($narray as $k => $nval) {
                                $artdiv = explode('#', $nval);
                                $articleIds[] = $artdiv[2];
                                $articleUrl[$artdiv[2]] = $artdiv[1];
                                $articleName[$artdiv[2]] = $artdiv[0];
                            }

                            // see if any of the articles have been read in last 90 days. If yes, then remove it from the list
                            if (!empty($articleIds)) {
                                $artids = implode(',', $articleIds);
                                $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                if (isset($mediaObj) && !empty($mediaObj)) {
                                    foreach ($mediaObj as $media) {
                                        if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                            unset($articleIds[$key]);
                                        }
                                    }
                                }
                            }

                            // Show the articles not read to user
                            $articleNames = array();
                            if (!empty($articleIds)) {
                                $articleNum = 1;
                                foreach ($articleIds as $articleId) {
                                    $articleNames[] = '<a href="' . $articleUrl[$articleId] . '" id="' . $articleId . '" name="' . $this->maximizeTraditionalIra .
                                            '"target="_blank" class="articlelink">' . $articleNum . '. ' . $articleName[$articleId] . '</a>';
                                    $articleNum++;
                                }
                            }
                            $artnames = implode(' <br>', $articleNames);
                            $this->SaveASteps($uid, $this->maximizeTraditionalIra, $monthlyRecommendedContribution, '<br>' . $artnames . '<br>');
                            $maximizeStep = true;
                        }
                    }

                    if (!$maximizeStep) {
                        // Create Action step: Consider Increasing Tax Withholding Amount using W4 Form at Work (#91)
                        if ($miscRow && $miscRow->taxpay == 0 && ($miscRow->taxvalue == '1' || $miscRow->taxvalue == '2')) {
                            $this->SaveASteps($uid, $this->increaseW4TaxWithholding);
                        }
                    }
                }

                /*                 * *******************************************************
                 * Create Action step : Diversify Investments for
                 * Risk Adjustments (#6)
                 * ****************************************************** */
                $resultArray_RiskTolerance = $this->checkForkRiskTolerance($sengineObj);
                if ($resultArray_RiskTolerance['needsActionStep']) {
                    $this->SaveASteps($uid, $this->diversifyInvestmentsForRisk, '', $resultArray_RiskTolerance['title']);
                }
                /*                 * ***************************************************** */

                // Update the count of learning center articles read.
                $articleCount = 10 - $mediaCount;
                if ($articleCount > 0) {
                    parent::UpdateLearningActionStep($uid, $mediaCount);
                }
                unset($uscore, $assetObj, $debtObj, $expenObj, $incomObj, $goalsObj, $insurObj, $irow, $miscObj, $usrinfoObj, $estiObj);
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Will check all existing actionstep for status update and remove
     */

    function StatusandRemove($uid) {
        try {
            if (isset($uid)) {
                $steps = new Actionstep();
                $checkqry = $steps->findAll("user_id=:user_id AND actionstatus IN ('0','2','3')", array("user_id" => $uid));
                if ($checkqry) {
// Initialize Classes
                    $assetObj = new Assets();
                    $debtObj = new Debts();
                    $expObj = new Expense();
                    $incObj = new Income();
                    $goalObj = new Goal();
                    $insurObj = new Insurance();
                    $miscObj = new Misc();
                    $uinfoObj = new Userpersonalinfo();
                    $estiObj = new Estimation();
                    $asObj = new Actionstep();
                    $actMetaObj = new Actionstepmeta();

                    // Fetch SE variables
                    $uscore = new UserScore();
                    $chkscoreqry = $uscore->findBySql("SELECT scoredetails FROM userscore WHERE user_id = :user_id", array("user_id" => $uid));
                    if ($chkscoreqry) {
                        $sdetails = $chkscoreqry->scoredetails;
                        $sengineObj = unserialize($sdetails);

                        $beneficiaryAdd = $sengineObj->beneAssigned;
                        $userRiskValue = $sengineObj->userRiskValue;

                        $spouseAge = $sengineObj->spouseAge;
                        $child1Age = $sengineObj->child1Age;
                        $child2Age = $sengineObj->child2Age;
                        $child3Age = $sengineObj->child3Age;
                        $child4Age = $sengineObj->child4Age;
                        $investmentAutomatically = $sengineObj->investmentAutomatically;

                        $nonCoreelatedTicker = $sengineObj->nonCoreelatedTicker;
                        $userSumOfGoalSettingAssets = $sengineObj->userSumOfGoalSettingAssets;
                        $userSumOfAssets = $sengineObj->userSumOfAssets;
                        $userSumOfOtherAssets = $sengineObj->userSumOfOtherAssets;
                        $userSumOfDebts = $sengineObj->userSumOfDebts;

                        $healthInsuranceReviewYear = $sengineObj->insuranceReviewYear24;
                        $lifeInsuranceReviewYear = $sengineObj->insuranceReviewYear29;
                        $disabilityInsuranceReviewYear = $sengineObj->insuranceReviewYear30;
                        $userGrowthRate = $sengineObj->userGrowthRate;

                        $mediaCount = $sengineObj->mediaCount;
                        $currentAge = $sengineObj->userCurrentAge;
                        $retired = $sengineObj->retired;

                        $grossincome = $sengineObj->userIncomePerMonth;
                        $grossexpense = $sengineObj->userExpensePerMonth;

                        $contributions = $sengineObj->taxableAnnualSavings + $sengineObj->taxDeferredAnnualSavings + $sengineObj->taxFreeAnnualSavings;

                        $acusrinsurCoverage = $sengineObj->insuranceNeededActionStep;
                        $disabilityInsurCoverage = $sengineObj->disainsuranceNeededActionStep;

                        $networthRatio = isset($sengineObj->wfPoint11) ? ($sengineObj->wfPoint11 / 100) : 0;
                        $informationListOfHiddenAsset = $sengineObj->informationListOfHiddenAsset;

                        $point5 = $sengineObj->wfPoint5;
                        $point10 = $sengineObj->wfPoint10;
                        $point14 = $sengineObj->wfPoint14;
                        $point12 = $sengineObj->wfPoint12;
                        $point22 = $sengineObj->wfPoint22;
                    } else {
                        $grossincome = 5000;
                        $grossexpense = 0;
                        $mediaCount = 0;
                        $point5 = 0;
                        $point10 = 0;
                        $point14 = 0;
                        $point22 = 0;
                        $userRiskValue = 0;
                        $investmentAutomatically = false;
                        $userGrowthRate = 7;
                        $currentAge = 30;
                        $contributions = 0;
                        $informationListOfHiddenAsset = false;
                        $nonCoreelatedTicker = false;
                        $acusrinsurCoverage = 0;
                        $healthInsuranceReviewYear = 0;
                        $lifeInsuranceReviewYear = 0;
                        $disabilityInsuranceReviewYear = 0;
                    }

                    // Main loop
                    foreach ($checkqry as $vals) {
                        $getval = $this->getActionstep($vals->actionid);
                        if ($getval->linktype == 'action') {
// Update or Remove Action step : Consider Auto-Linking All Financial Accounts (#21)
                            if ($getval->link == 'connectaccount' && ($vals->actionid == $this->connectAccountAuto || $vals->actionid == $this->connectAccountManual)) {
                                $adDet = $debtObj->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $uid), 'select' => 'balowed'));
                                $aaDet = $assetObj->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0 AND type in ('BANK', 'IRA', 'CR', 'BROK', 'EDUC')", 'params' => array("user_id" => $uid), 'select' => 'balance'));
                                $aiDet = $insurObj->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0 AND type in ('LIFE', 'DISA', 'LONG')", 'params' => array("user_id" => $uid), 'select' => 'annualpremium'));

                                $mdDet = $debtObj->findAll(array('condition' => "user_id = :user_id AND context = 'MANUAL' AND status=0", 'params' => array("user_id" => $uid), 'select' => 'balowed'));
                                $maDet = $assetObj->findAll(array('condition' => "user_id = :user_id AND context = 'MANUAL' AND status=0 AND type in ('BANK', 'IRA', 'CR', 'BROK', 'EDUC')", 'params' => array("user_id" => $uid), 'select' => 'balance'));
                                $miDet = $insurObj->findAll(array('condition' => "user_id = :user_id AND context = 'MANUAL' AND status=0 AND type in ('LIFE', 'DISA', 'LONG')", 'params' => array("user_id" => $uid), 'select' => 'annualpremium'));

                                $userPerDetails = Userpersonalinfo::model()->find(array("condition" => "user_id=:user_id", "params" => array("user_id" => $uid), "select" => 'connectAccountPreference'));
                                if ($userPerDetails->connectAccountPreference == '1') {
                                    if ($vals->actionid == $this->connectAccountAuto) {
                                        $this->updateActionstep($uid, $this->connectAccountAuto, $this->actionNew);
                                    }
                                    if ($vals->actionid == $this->connectAccountManual) {
                                        $this->updateActionstep($uid, $this->connectAccountManual, $this->actionNew);
                                    }
                                } else if (!empty($adDet) || !empty($aaDet) || !empty($aiDet)) {
                                    if ($vals->actionid == $this->connectAccountManual) {
                                        $this->updateActionstep($uid, $this->connectAccountManual, $vals->actionstatus);
                                    }
                                    if ($vals->actionid == $this->connectAccountAuto) {
                                        $this->updateActionstep($uid, $this->connectAccountAuto, $vals->actionstatus);
                                    }
                                } else if (!empty($mdDet) || !empty($maDet) || !empty($miDet)) {
                                    if ($vals->actionid == $this->connectAccountAuto) {
                                        $this->updateActionstep($uid, $this->connectAccountAuto, $this->actionNew);
                                    }
                                } else {
                                    if ($vals->actionid == $this->connectAccountManual) {
                                        $this->updateActionstep($uid, $this->connectAccountManual, $this->actionNew);
                                    }
                                }
                            }
// Update or Remove Action step : Review Beneficiary Designations and Update if Needed (#10)
                            if ($getval->link == 'addasset' && $vals->actionid == $this->reviewBeneficiary) {
                                $aaDetails = $assetObj->findAll(array('condition' => "user_id = :user_id AND status=0 AND type IN ('CR','EDUC','IRA') AND beneficiary <> 1", 'params' => array("user_id" => $uid), 'select' => array('balance')));
                                if (empty($aaDetails)) {
                                    $this->updateActionstep($uid, $this->reviewBeneficiary, $vals->actionstatus);
                                }
                            }
                            if ($getval->link == 'addasset' && $vals->actionid == $this->pensionEligibility) {
                                $aaDetails = $assetObj->findAll(array('condition' => "user_id = :user_id AND status=0 AND type IN ('PENS') AND beneficiary <> 1", 'params' => array("user_id" => $uid), 'select' => array('balance')));
                                if (empty($aaDetails)) {
                                    $this->updateActionstep($uid, $this->pensionEligibility, $vals->actionstatus);
                                }
                            }
                            // Update or Remove Action step : Set up other accounts like - CDs, Checking, etc. (#58)
                            if ($getval->link == 'addasset' && $vals->actionid == $this->setupGoal) {
                                $aaDetails = $assetObj->findAll(array('condition' => "user_id = :user_id AND status=0", 'params' => array("user_id" => $uid), 'select' => 'balance'));
                                $goalDetails = $goalObj->findAll(array('condition' => "user_id = :user_id AND goalstatus=1 and goalpriority > 0", 'params' => array("user_id" => $uid), 'select' => 'goalamount'));
                                if (!empty($goalDetails)) {
                                    if (!empty($aaDetails)) {
// Added an asset, so need to mark action step complete.
                                        $this->updateActionstep($uid, $this->setupGoal, $vals->actionstatus);
                                    }
                                } else {
// No longer has any goals, so need to delete actionstep
                                    $this->updateActionstep($uid, $this->setupGoal, $this->actionNew);
                                }
                            }

// Check Heath Insurance active in insurance table, than Complete the actionstep record for health insurance per user
                            if ($vals->actionid == $this->healthMedicalInsuranceArticle && $getval->link == 'learnmore') {

                                $getHealthInsDetail = $insurObj->findAll(array('condition' => "user_id=:user_id AND status=0 AND type = 'HEAL'", 'params' => array("user_id" => $uid), 'select' => array('status')));
                                if (isset($getHealthInsDetail) && !empty($getHealthInsDetail)) {
                                    $this->updateActionstep($uid, $this->healthMedicalInsuranceArticle, $this->actionNew, true);
                                } else {
                                    $articleIds = array();
                                    $narray = explode('|', $getval->articles);
                                    foreach ($narray as $k => $nval) {
                                        $artdiv = explode('#', $nval);
                                        $articleIds[] = $artdiv[2];
                                    }

                                    if (!empty($articleIds)) {
                                        $artids = implode(',', $articleIds);
                                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        if (isset($mediaObj) && !empty($mediaObj)) {
                                            foreach ($mediaObj as $media) {
                                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                    unset($articleIds[$key]);
                                                }
                                            }
                                        }
                                    }
                                    if (empty($articleIds)) {
                                        $this->updateActionstep($uid, $this->healthMedicalInsuranceArticle, $vals->actionstatus, true);
                                    }
                                }
                            }

                            // #70 Flexibility of Assets
                            if ($vals->actionid == $this->flexibilityOfAssets && $getval->link == 'learnmore') {
                                if ($point14 >= 37.5) {
                                    $this->updateActionstep($uid, $this->flexibilityOfAssets, $this->actionNew, true);
                                } else {
                                    $articleIds = array();
                                    $narray = explode('|', $getval->articles);
                                    foreach ($narray as $k => $nval) {
                                        $artdiv = explode('#', $nval);
                                        $articleIds[] = $artdiv[2];
                                    }

                                    if (!empty($articleIds)) {
                                        $artids = implode(',', $articleIds);
                                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        if (isset($mediaObj) && !empty($mediaObj)) {
                                            foreach ($mediaObj as $media) {
                                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                    unset($articleIds[$key]);
                                                }
                                            }
                                        }
                                    }
                                    if (empty($articleIds)) {
                                        $this->updateActionstep($uid, $this->flexibilityOfAssets, $vals->actionstatus, true);
                                    }
                                }
                            }

                            // Update or Remove Action step : Evaluate Amount of Consumer Debt Costs Compared to Income (#74)
                            if ($vals->actionid == $this->considerLifeexpectancyRisk && $getval->link == 'learnmore') {
                                if (!$retired || ($currentAge >= 60 && $point22 >= 20)) {
                                    $this->updateActionstep($uid, $this->considerLifeexpectancyRisk, $this->actionNew, true);
                                } else {
                                    $articleIds = array();
                                    $narray = explode('|', $getval->articles);
                                    foreach ($narray as $k => $nval) {
                                        $artdiv = explode('#', $nval);
                                        $articleIds[] = $artdiv[2];
                                    }

                                    if (!empty($articleIds)) {
                                        $artids = implode(',', $articleIds);
                                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        if (isset($mediaObj) && !empty($mediaObj)) {
                                            foreach ($mediaObj as $media) {
                                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                    unset($articleIds[$key]);
                                                }
                                            }
                                        }
                                    }
                                    if (empty($articleIds)) {
                                        $this->updateActionstep($uid, $this->considerLifeexpectancyRisk, $vals->actionstatus, true);
                                    }
                                }
                            }

                            // Update or Remove Action step : Retired - Examine Your Lifestyle Costs to Make Certain You Aren't Overspending (#75)
                            if ($vals->actionid == $this->examineLifestyleCost && $getval->link == 'learnmore') {
                                if (!$retired || $point22 >= 20) {
                                    $this->updateActionstep($uid, $this->examineLifestyleCost, $this->actionNew, true);
                                } else {
                                    $articleIds = array();
                                    $narray = explode('|', $getval->articles);
                                    foreach ($narray as $k => $nval) {
                                        $artdiv = explode('#', $nval);
                                        $articleIds[] = $artdiv[2];
                                    }

                                    if (!empty($articleIds)) {
                                        $artids = implode(',', $articleIds);
                                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        if (isset($mediaObj) && !empty($mediaObj)) {
                                            foreach ($mediaObj as $media) {
                                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                    unset($articleIds[$key]);
                                                }
                                            }
                                        }
                                    }
                                    if (empty($articleIds)) {
                                        $this->updateActionstep($uid, $this->examineLifestyleCost, $vals->actionstatus, true);
                                    }
                                }
                            }

                            if ($vals->actionid == $this->improveCreditScore && $getval->link == 'learnmore') {

                                $mrow = $miscObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid)));
                                if (!isset($mrow->morecreditscore) || $mrow->morecreditscore > 699 || $mrow->morecreditscore < 1) {
                                    $this->updateActionstep($uid, $this->improveCreditScore, $this->actionNew, true);
                                } else {
                                    $articleIds = array();
                                    $narray = explode('|', $getval->articles);
                                    foreach ($narray as $k => $nval) {
                                        $artdiv = explode('#', $nval);
                                        $articleIds[] = $artdiv[2];
                                    }

                                    if (!empty($articleIds)) {
                                        $artids = implode(',', $articleIds);
                                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        if (isset($mediaObj) && !empty($mediaObj)) {
                                            foreach ($mediaObj as $media) {
                                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                    unset($articleIds[$key]);
                                                }
                                            }
                                        }
                                    }
                                    if (empty($articleIds)) {
                                        $this->updateActionstep($uid, $this->improveCreditScore, $vals->actionstatus, true);
                                    }
                                }
                            }
// Check Heath Insurance active in insurance table, than Complete the actionstep record for health insurance per user
                            if ($vals->actionid == $this->learningArticles && $getval->link == 'learnmore') {
                                $articleCount = 10 - $mediaCount;
                                if ($articleCount <= 0) {
                                    $this->updateActionstep($uid, $this->learningArticles, $vals->actionstatus);
                                }
                            }
                            // Update or Remove Action step : Evaluate Amount of Consumer Debt Costs Compared to Income (#54)
                            if ($vals->actionid == $this->evaluateConsumerDebtCosts && $getval->link == 'learnmore') {
                                $consumerDebt = $debtObj->find(array('condition' => "user_id=:user_id AND type <> 'MORT' AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid), 'select' => 'SUM(amtpermonth) AS total'));
                                $goodRatio = true;
                                if (isset($consumerDebt->total)) {
                                    $new_gross = (22 / 100) * $grossincome;
                                    if ($consumerDebt->total >= $new_gross) {
                                        $goodRatio = false;
                                    }
                                }

                                if ($goodRatio) {
                                    $this->updateActionstep($uid, $this->evaluateConsumerDebtCosts, $this->actionNew, true);
                                } else {
                                    $articleIds = array();
                                    $narray = explode('|', $getval->articles);
                                    foreach ($narray as $k => $nval) {
                                        $artdiv = explode('#', $nval);
                                        $articleIds[] = $artdiv[2];
                                    }

                                    if (!empty($articleIds)) {
                                        $artids = implode(',', $articleIds);
                                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        if (isset($mediaObj) && !empty($mediaObj)) {
                                            foreach ($mediaObj as $media) {
                                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                    unset($articleIds[$key]);
                                                }
                                            }
                                        }
                                    }
                                    if (empty($articleIds)) {
                                        $this->updateActionstep($uid, $this->evaluateConsumerDebtCosts, $vals->actionstatus, true);
                                    }
                                }
                            }
// Update or Remove Action step : Evaluate Amount of Housing Costs Compared to Income (#55)
                            if ($vals->actionid == $this->evaluateHousingCosts && $getval->link == 'learnmore') {
                                $mdebtDet = $debtObj->find(array('condition' => "user_id=:user_id AND type='MORT' AND status=0", 'params' => array("user_id" => $uid), 'select' => 'SUM(amtpermonth) AS total'));
                                $goodRatio = true;
                                if (isset($mdebtDet->total)) {
                                    $new_gross = (30 / 100) * $grossincome;
                                    if ($mdebtDet->total >= $new_gross) {
                                        $goodRatio = false;
                                    }
                                }

                                if ($goodRatio) {
                                    $this->updateActionstep($uid, $this->evaluateHousingCosts, $this->actionNew, true);
                                } else {
                                    $articleIds = array();
                                    $narray = explode('|', $getval->articles);
                                    foreach ($narray as $k => $nval) {
                                        $artdiv = explode('#', $nval);
                                        $articleIds[] = $artdiv[2];
                                    }

                                    if (!empty($articleIds)) {
                                        $artids = implode(',', $articleIds);
                                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        if (isset($mediaObj) && !empty($mediaObj)) {
                                            foreach ($mediaObj as $media) {
                                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                    unset($articleIds[$key]);
                                                }
                                            }
                                        }
                                    }
                                    if (empty($articleIds)) {
                                        $this->updateActionstep($uid, $this->evaluateHousingCosts, $vals->actionstatus, true);
                                    }
                                }
                            }
// Update or Remove Action step : Give us more information on an asset (#25)
                            if ($getval->link == 'editasset' && $vals->actionid == $this->moreAsset) {
                                $assetChk = 0;
                                $infoDetail = $uinfoObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('retirementstatus')));
                                $fillmaDet = $assetObj->findAll(array('condition' => "user_id = :user_id AND status=0", 'params' => array("user_id" => $uid), 'select' => array('type', 'balance', 'invpos')));
                                if (count($fillmaDet) >= 1) {
                                    foreach ($fillmaDet as $farow) {
                                        switch ($farow->type) {
                                            case 'BANK':
                                                if (!isset($farow->balance) || $farow->balance == '') {
                                                    $assetChk = 1;
                                                }
                                                break;
                                            case 'IRA':
                                            case 'CR':
                                            case 'BROK':
                                                if (!isset($farow->balance) || $farow->balance == '' || !isset($farow->invpos) || $farow->invpos == '' || $farow->invpos == '[]') {
                                                    $assetChk = 1;
                                                }
                                                break;
                                            case 'EDUC':
                                            case 'PROP':
                                            case 'VEHI':
                                            case 'PENS':
                                            case 'SS':
                                            case 'BUSI':
                                            case 'OTHE':
                                                if (!isset($farow->balance) || $farow->balance == '') {
                                                    $assetChk = 1;
                                                }
                                                break;
                                        }
                                    }
                                    if ($assetChk == 0) {
                                        $this->updateActionstep($uid, $this->moreAsset, $vals->actionstatus);
                                    }
                                }
                            }


                            /*                            if ($getval->link == 'addasset' && $vals->actionid == 12) {
                              $qic = new CDbCriteria();
                              $qic->condition = "user_id = :user_id AND status=0";
                              $qic->select = 'balance';
                              $qic->limit = 1;
                              $qic->params = array('user_id' => $uid);
                              $qic->addInCondition('type', array('CR', 'IRA'));
                              $uinfoDetails = $assetObj->findAll($qic);
                              if (!empty($uinfoDetails)) {
                              $this->updateActionstep($uid, $this->addIraRothOrTraditional, $vals->actionstatus);
                              }
                              }
                             */
// Update or Remove Action step : Give us more information on a debt (#26)
                            if ($getval->link == 'editdebt' && $vals->actionid == $this->moreDebt) {
                                $debtChk = 0;
                                $fillmdDet = $debtObj->findAll(array('condition' => 'user_id = :user_id AND status=0 AND monthly_payoff_balances=0 ', 'params' => array('user_id' => $uid), 'select' => array('type', 'balowed', 'apr')));
                                if (count($fillmdDet) >= 1) {
                                    foreach ($fillmdDet as $fdrow) {
                                        if (!isset($fdrow->balowed) || $fdrow->balowed == '' || !isset($fdrow->apr) || $fdrow->apr == '') {
                                            $debtChk = 1;
                                        }
                                    }
                                    if ($debtChk == 0) {
                                        $this->updateActionstep($uid, $this->moreDebt, $vals->actionstatus);
                                    }
                                }
                            }
// Update or Remove Action step : Give us more detailed Expenses (#32)
                            if ($getval->link == 'addexpense' && $vals->actionid == $this->detailedExpense) {
                                $expensesCheck = 1;
                                $expDetails = $expObj->findAll(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('actualexpense')));
// If we have expenses
                                if (!empty($expDetails)) {
                                    foreach ($expDetails as $erow) {
                                        if ($erow->actualexpense <= 0.00) {
// still not completed
                                            $expensesCheck = 0;
                                        }
                                    }
                                } else {
// empty expenses. still not completed.
                                    $expensesCheck = 0;
                                }

                                if ($expensesCheck == 1) {
// completed. mark as done.
                                    $this->updateActionstep($uid, $this->detailedExpense, $vals->actionstatus);
                                } else {
// if not completed, then check if income is not completed, because both steps can't be active at same time.
                                    $incDetails = $incObj->findAll(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('totaluserincome')));
                                    if (empty($incDetails)) {
// there is no income so there exists an income step, so we should delete the expense step quietly
                                        $this->updateActionstep($uid, $this->detailedExpense, $this->actionNew);
                                    } else {
                                        foreach ($incDetails as $irow) {
                                            if ($irow->totaluserincome <= 0.00) {
// there is no income so there exists an income step, so we should delete the expense step quietly
                                                $this->updateActionstep($uid, $this->detailedExpense, $this->actionNew);
                                            }
                                        }
                                    }
                                }
                            }
// Update or Remove Action step : Give us more detailed Income (#31)
                            if ($getval->link == 'addincome' && $vals->actionid == $this->detailedIncome) {
                                $incomeCheck = 1;
                                $incoDetails = $incObj->findAll(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('totaluserincome')));
                                if (!empty($incoDetails)) {
                                    foreach ($incoDetails as $irow) {
                                        if ($irow->totaluserincome <= 0.00) {
                                            $incomeCheck = 0;
                                        }
                                    }
                                    if ($incomeCheck == 1) {
                                        $this->updateActionstep($uid, $this->detailedIncome, $vals->actionstatus);
                                    }
                                }
                            }

                            /*                             * ************************************************
                             * Increase Savings Action Step
                             * ************************************************ */
                            if ($vals->actionid == $this->increaseSavings) {
                                $newgoalDetails = $goalObj->findAll(array('condition' => "user_id = :user_id AND goalstatus=1 and goaltype <> 'DEBT' ORDER by goalpriority", 'params' => array("user_id" => $uid), 'select' => 'id,goalname, goaltype, goalpriority, goalamount,saved,monthlyincome, permonth, goalstartdate, goalenddate, goalassumptions_1'));
                                $assets = $assetObj->findAll(array('condition' => "user_id = :user_id and type in('BANK','IRA','CR','BROK','EDUC') AND status=0 order by type desc", 'params' => array("user_id" => $uid), 'select' => 'id,name,balance,type,assettype,contribution,empcontribution'));

                                if (isset($newgoalDetails) && !empty($newgoalDetails) && isset($assets) && !empty($assets)) {
                                    $personal = $uinfoObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('age', 'retirementage')));
                                    $result = $this->checkGoals($newgoalDetails, $assets, $currentAge, $userGrowthRate, $personal, $grossincome, $uid);
                                    $goalid = 0;
                                    if ($vals->flexi1) {
                                        $goalInfo = explode('addGoals"', $vals->flexi1);
                                        $linkname = explode('"', $goalInfo[0]);
                                        $goalid = $linkname[3];
                                    }
                                    // Retrieve goal priority from existing action step
                                    $goalpriority = -1;
                                    foreach ($newgoalDetails as $goal) {
                                        if ($goal->id == $goalid) {
                                            $goalpriority = $goal->goalpriority;
                                        }
                                    }
                                    $completeStep = true;
                                    if ($result["needsActionStep"] && $result["goaltype"] == 'RETIREMENT') {
                                        // Check if goal was deleted or priorities were changed
                                        if ($goalpriority == -1 || intval($goalpriority) > intval($result["goalpriority"])) {
                                            $completeStep = false;
                                        }
                                        if ($completeStep) {
                                            $this->updateActionstep($uid, $this->increaseSavings, $vals->actionstatus);
                                        } else {
                                            $this->updateActionstep($uid, $this->increaseSavings, $this->actionNew);
                                        }
                                    } else if (!$result["needsActionStep"]) {
                                        // Check if goal was deleted
                                        if ($goalpriority == -1) {
                                            $completeStep = false;
                                        }
                                        if ($completeStep) {
                                            $this->updateActionstep($uid, $this->increaseSavings, $vals->actionstatus);
                                        } else {
                                            $this->updateActionstep($uid, $this->increaseSavings, $this->actionNew);
                                        }
                                    } else {
                                        if ($goalpriority == -1 || intval($goalpriority) > intval($result["goalpriority"])) {
                                            $completeStep = false;
                                        }
                                        if ($completeStep && $goalid != $result["goalid"]) {
                                            $this->updateActionstep($uid, $this->increaseSavings, $vals->actionstatus);
                                        } else if ($goalid != $result["goalid"]) {
                                            $this->updateActionstep($uid, $this->increaseSavings, $this->actionNew);
                                        }
                                    }
                                } else {
                                    $this->updateActionstep($uid, $this->increaseSavings, $this->actionNew);
                                }
                            }
                            /*                             * ********************************************** */

                            /*                             * *************************************************
                             * Set Up Appropriate Type of Savings Account
                             * Updating the action step if atleast one Savings
                             * account is added.
                             * ************************************************ */
                            if ($vals->actionid == $this->savingsAccount) {
                                $qic = new CDbCriteria();
                                $savings_account_exists = false;
                                $education_account_exists = false;
                                $action_step_needed = false;
                                $qic->condition = "user_id = :user_id AND status=0";
                                $qic->select = 'balance,type,contribution';
                                $qic->params = array('user_id' => $uid);
                                $qic->addInCondition('type', array('BANK', 'EDUC', 'BROK'));
                                $astDetails = $assetObj->findAll($qic);
                                foreach ($astDetails as $irow) {
                                    switch ($irow->type) {
                                        case "BANK":
                                            $savings_account_exists = true;
                                            break;
                                        case "EDUC":
                                            $education_account_exists = true;
                                            break;
                                        case "BROK":
                                            $savings_account_exists = true;
                                            break;
                                    }
                                }

                                $newgoalDetails = $goalObj->findAll(array('condition' => "user_id = :user_id AND goalstatus=1 and goaltype <> 'RETIREMENT'", 'params' => array("user_id" => $uid), 'select' => 'goaltype'));

                                if (isset($newgoalDetails) && !empty($newgoalDetails)) {
                                    foreach ($newgoalDetails as $irow) {
                                        switch ($irow->goaltype) {
                                            case "HOUSE":
                                                if (!$savings_account_exists) {
                                                    $action_step_needed = true;
                                                }
                                                break;
                                            case "COLLEGE":
                                                if (!$savings_account_exists && !$education_account_exists) {
                                                    $action_step_needed = true;
                                                }
                                                break;
                                            case "CUSTOM":
                                                if (!$savings_account_exists) {
                                                    $action_step_needed = true;
                                                }
                                                break;
                                        }
                                    }
                                    if (!$action_step_needed) {
                                        $this->updateActionstep($uid, $this->savingsAccount, $vals->actionstatus);
                                    }
                                } else {
                                    $this->updateActionstep($uid, $this->savingsAccount, $this->actionNew);
                                }
                            }
                            /*                             * *********************************************** */


                            /*                             * *************************************************
                             * Set Up Appropriate Type of Retirement Funding Account
                             * Updating the action step if atleast one retirement fund
                             * account is added. Retirement fund includes Social Security
                             * func, IRA account, Pension Account and Company Retirement
                             * Plan
                             * ************************************************ */
                            if ($vals->actionid == $this->retirementFundingAccount) {
                                $assetCount = $assetObj->count("user_id=:user_id and status=0 and type in ('CR','IRA')", array("user_id" => $uid));
                                $goalCount = $goalObj->count("user_id=:user_id and goalstatus=1 and goaltype = 'RETIREMENT'", array("user_id" => $uid));
                                if ($assetCount > 0 && $goalCount > 0) {
                                    $this->updateActionstep($uid, $this->retirementFundingAccount, $vals->actionstatus);
                                } else if ($goalCount <= 0) {
                                    $this->updateActionstep($uid, $this->retirementFundingAccount, $this->actionNew);
                                }
                            }
                            /*                             * *********************************************** */


                            /*                             * *************************************************
                             * Payoff Debt Plan
                              Once an action step has started (where a user clicks on a link), we keep the advice based on the following scenarios
                              Scenario 1: A goal update => new advice
                              Scenario 2: A new debt => new advice only if the new debt is now part of the goal (if goal has all debts selected)
                              Scenario 3: Existing debt update => old advice
                              Scenario 4: Hiding/Deleting debt that affects goal => new advice (if goal had that debt included)
                              Scenario 5: Balance goes below suggested payment amount => new advice
                              Scenario 6: If balance order changes => we currently do lowest to highest balance but if that order changes => new advice
                             * ************************************************ */
                            if ($vals->actionid == $this->payOffDebts) {
                                $goal = $goalObj->find(array('condition' => "user_id = :user_id AND goalstatus=1 and goaltype = 'DEBT'", 'params' => array("user_id" => $uid), 'select' => 'id,goalname, goaltype, payoffdebts, goalamount,saved, permonth,goalenddate,modifiedtimestamp,goalassumptions_1,goalassumptions_2'));
                                $sortOrder = "balowed asc, apr desc";
                                if (isset($goal)) {
                                    if ($goal->goalassumptions_2 == "72") {
                                        $sortOrder = "balowed desc, apr desc";
                                    }
                                    if ($goal->goalassumptions_2 == "73") {
                                        $sortOrder = "apr desc, balowed asc";
                                    }
                                    if ($goal->goalassumptions_2 == "74") {
                                        $sortOrder = "apr asc, balowed asc";
                                    }
                                }
                                $debts = $debtObj->findAll(array('condition' => "user_id = :user_id AND status=0 AND monthly_payoff_balances=0 order by " . $sortOrder, 'params' => array("user_id" => $uid), 'select' => 'id,name,balowed,type,amtpermonth,apr'));
                                if (isset($goal) && isset($debts) && !empty($debts)) {
                                    $newAdvice = false;
                                    $debtInfo = '';
                                    if ($vals->actionstatus <> '3') {
                                        // If an action step has not started
                                        $newAdvice = true;
                                    } else if ($goal->modifiedtimestamp > $vals->lastmodifiedtime) {
                                        // Scenario 1: A goal update => new advice
                                        $newAdvice = true;
                                    } else if ($vals->flexi1) {
                                        $debtArray = array();
                                        if (!isset($goal->payoffdebts) || $goal->payoffdebts == '') {
                                            // Load All Active Debts that affect the goal
                                            $debtArray = $debts;
                                        } else {
                                            // Load Active Custom Debts that affect the goal
                                            $debtDetails = explode(",", $goal->payoffdebts);
                                            foreach ($debts as $debt) {
                                                foreach ($debtDetails as $debtId) {
                                                    if ($debt->id == $debtId) {
                                                        $debtArray[] = $debt;
                                                    }
                                                }
                                            }
                                        }

                                        $paymentInfo = explode("id='paymentInfo' type='hidden'", $vals->flexi1);
                                        $linkname = explode("'", $paymentInfo[1]);
                                        $payment = $linkname[1];
                                        $paymentInfo = explode(",", $payment);
                                        if (count($paymentInfo) != count($debtArray)) {
                                            // Scenario 2: A new debt => new advice only if the new debt is now part of the goal (if goal has all debts selected)
                                            // Scenario 4: Hiding/Deleting debt that affects goal => new advice (if goal had that debt included)
                                            $newAdvice = true;
                                        } else {
                                            $index = 0;
                                            foreach ($debtArray as $debt) {
                                                $debtValue = explode("|", $paymentInfo[$index]);
                                                if ($debt->id != $debtValue[0]) {
                                                    // Scenario 6: If balance order changes => we currently do lowest to highest balance but if that order changes => new advice
                                                    $newAdvice = true;
                                                    break;
                                                } else if ($debt->balowed < $debtValue[1]) {
                                                    // Scenario 5: Balance goes below suggested payment amount => new advice
                                                    $newAdvice = true;
                                                    break;
                                                } else if ($debt->amtpermonth != $debtValue[1]) {
                                                    $name = $debt->name;
                                                    if (!isset($debt->name) || $debt->name == '') {
                                                        $name = $debt->getDefaultName($debt->type);
                                                    }
                                                    $debtName = '<a href="#" id="' . $debt->id . 'addDebts" class="addDebts actionStep">' . $name . '</a>';
                                                    if ($debt->amtpermonth > $debtValue[1]) {
                                                        $debtInfo .= "Reduce monthly payments to $" . number_format($debtValue[1]) . " for " . $debtName . " ($" . number_format($debt->balowed) . ").<br>";
                                                    } else {
                                                        $debtInfo .= "Increase monthly payments to $" . number_format($debtValue[1]) . " for " . $debtName . " ($" . number_format($debt->balowed) . ").<br>";
                                                    }
                                                }
                                                $index++;
                                            }
                                        }
                                    }
                                    if ($newAdvice) {
                                        $result = $this->CheckDebtGoals($goal, $debts);
                                        if (!$result["needsActionStep"]) {
                                            $this->updateActionstep($uid, $this->payOffDebts, $this->actionNew);
                                        }
                                    } else {
                                        if (strlen($debtInfo) == 0) {
                                            $this->updateActionstep($uid, $this->payOffDebts, $vals->actionstatus);
                                        }
                                    }
                                } else {
                                    $this->updateActionstep($uid, $this->payOffDebts, $this->actionNew);
                                }
                            }
                            /*                             * *********************************************** */

                            /*                             * *******************************************************
                             * Create Action step : Diversify Investments for
                             * Risk Adjustments (#6)
                             * ****************************************************** */
                            if ($vals->actionid == $this->diversifyInvestmentsForRisk) {
                                $resultArray_RiskTolerance = $this->checkForkRiskTolerance($sengineObj);
                                if (!$resultArray_RiskTolerance['needsActionStep']) {
                                    $this->updateActionstep($uid, $this->diversifyInvestmentsForRisk, $vals->actionstatus);
                                }
                            }
                            /*                             * ***************************************************** */

                            /*                             * *************************************************
                             * Retirement Goal Plan
                             * ************************************************ */
                            if ($vals->actionid == $this->retirementGoalPlan) {
                                $newgoalDetails = $goalObj->findAll(array('condition' => "user_id = :user_id AND goalstatus=1 and goaltype <> 'DEBT' ORDER by goalpriority", 'params' => array("user_id" => $uid), 'select' => 'id,goalname, goaltype, goalamount,goalpriority,saved,monthlyincome, permonth, goalstartdate, goalenddate, goalassumptions_1'));
                                $assets = $assetObj->findAll(array('condition' => "user_id = :user_id and type in('BANK','IRA','CR','BROK','EDUC') AND status=0 order by type desc", 'params' => array("user_id" => $uid), 'select' => 'id,name,balance,type,assettype,contribution,empcontribution'));

                                if (isset($newgoalDetails) && !empty($newgoalDetails) && isset($assets) && !empty($assets)) {
                                    $personal = $uinfoObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('age', 'retirementage')));
                                    $result = $this->checkGoals($newgoalDetails, $assets, $currentAge, $userGrowthRate, $personal, $grossincome, $uid);
                                    // Retrieve goal id from existing action step
                                    $goalid = 0;
                                    if ($vals->flexi1) {
                                        $goalInfo = explode('addGoals"', $vals->flexi1);
                                        $linkname = explode('"', $goalInfo[0]);
                                        $goalid = $linkname[3];
                                    }
                                    // Retrieve goal priority from existing action step
                                    $goalpriority = -1;
                                    foreach ($newgoalDetails as $goal) {
                                        if ($goal->id == $goalid) {
                                            $goalpriority = $goal->goalpriority;
                                        }
                                    }
                                    $completeStep = true;
                                    if ($result["needsActionStep"] && $result["goaltype"] <> 'RETIREMENT') {
                                        // Check if goal was deleted or priorities were changed
                                        if ($goalpriority == -1 || intval($goalpriority) > intval($result["goalpriority"])) {
                                            $completeStep = false;
                                        }
                                        if ($completeStep) {
                                            $this->updateActionstep($uid, $this->retirementGoalPlan, $vals->actionstatus);
                                        } else {
                                            $this->updateActionstep($uid, $this->retirementGoalPlan, $this->actionNew);
                                        }
                                    } else if (!$result["needsActionStep"]) {
                                        // Check if goal was deleted
                                        if ($goalpriority == -1) {
                                            $completeStep = false;
                                        }
                                        if ($completeStep) {
                                            $this->updateActionstep($uid, $this->retirementGoalPlan, $vals->actionstatus);
                                        } else {
                                            $this->updateActionstep($uid, $this->retirementGoalPlan, $this->actionNew);
                                        }
                                    }
                                } else {
                                    $this->updateActionstep($uid, $this->retirementGoalPlan, $this->actionNew);
                                }
                            }
                            /*                             * *********************************************** */

                            /*                             * ********************************************** */
                            // Update or Remove Action step : Identify Financial Goals, Cost of Goal, Time Period to achieve goal (#15)
                            if ($getval->link == 'addgoal' && $vals->actionid == $this->addGoal) {
                                $goalDetails = $goalObj->findAll(array('condition' => "user_id = :user_id AND goalstatus=1", 'params' => array("user_id" => $uid), 'select' => array('goalpriority')));
                                if (!empty($goalDetails)) {
                                    $gochk = 0;
                                    foreach ($goalDetails as $grow) {
                                        if ($grow->goalpriority > 0) {
                                            $gochk = 1;
                                        }
                                    }
                                    if ($gochk == 1) {
                                        $this->updateActionstep($uid, $this->addGoal, $vals->actionstatus);
                                    }
                                }
                            }
                            // Update or Remove Action step : Consider Setting Up a Goal of Paying Off Consumer Debt (#16)
                            if ($getval->link == 'addgoal' && $vals->actionid == $this->setGoal) {
                                $qgc = new CDbCriteria();
                                $qgc->condition = "user_id=:user_id AND goaltype=:type AND goalstatus=1";
                                $qgc->limit = 10;
                                $qgc->params = array('user_id' => $uid, 'type' => 'DEBT');
                                $goalDetails = $goalObj->findAll($qgc);
                                $consumerDebtDetails = $debtObj->find(array('condition' => "user_id=:user_id AND type <> :debttype AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid, "debttype" => "MORT"), 'select' => 'SUM(amtpermonth) AS total'));
                                if (isset($consumerDebtDetails->total)) {
                                    $new_gross = (20 / 100) * $grossincome;
                                    if ($consumerDebtDetails->total >= $new_gross) {
                                        if (isset($goalDetails) && !empty($goalDetails)) {
                                            $this->updateActionstep($uid, $this->setGoal, $vals->actionstatus);
                                        }
                                    } else {
                                        $this->updateActionstep($uid, $this->setGoal, $this->actionNew);
                                    }
                                } else {
                                    $this->updateActionstep($uid, $this->setGoal, $this->actionNew);
                                }
                            }
                            // Update or Remove Action step : Tell us more about Insurance (#29)
                            if ($getval->link == 'addinsurance' && $vals->actionid == $this->moreInsurance) {
                                $accounts = array('LIFE' => false, 'DISA' => false, 'LONG' => false);
                                $insurDet = $insurObj->findAll("user_id=:user_id AND status=0 AND type IN ('LIFE', 'DISA', 'LONG')", array("user_id" => $uid));
                                foreach ($insurDet as $irow) {
                                    if ($irow->type == 'LIFE') {
                                        $accounts['LIFE'] = true;
                                    }
                                    if ($irow->type == 'DISA') {
                                        $accounts['DISA'] = true;
                                    }
                                    if ($irow->type == 'LONG') {
                                        $accounts['LONG'] = true;
                                    }
                                }
                                $checkInsurance = 0;
                                $esrow = $estiObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('whichyouhave')));
                                if (isset($esrow->whichyouhave) && strpos($esrow->whichyouhave, 'Life') !== false && !$accounts['LIFE']) {
                                    $checkInsurance = 1;
                                }
                                if (isset($esrow->whichyouhave) && strpos($esrow->whichyouhave, 'Disability') !== false && !$accounts['DISA']) {
                                    $checkInsurance = 1;
                                }
                                if (isset($esrow->whichyouhave) && strpos($esrow->whichyouhave, 'Long Term Care') !== false && !$accounts['LONG']) {
                                    $checkInsurance = 1;
                                }
                                if ($checkInsurance == 0) {
                                    $this->updateActionstep($uid, $this->moreInsurance, $vals->actionstatus);
                                }
                            }
// Update or Remove Action step : (#3,#2) For Life Insurance section
                            if ($getval->link == 'addinsurance' && in_array($vals->actionid, array(3, 2))) {
                                $qfc = new CDbCriteria();
                                $qfc->condition = "user_id=:user_id AND status=0 AND type=:type AND insurancefor=:insur";
                                $qfc->select = 'annualpremium';
                                $qfc->limit = 1;
                                $qfc->params = array('user_id' => $uid, 'type' => 'LIFE', 'insur' => '80');
                                $qfc->addInCondition('beneficiary', array('81', '82', '83'));
                                $insurDetails = $insurObj->findAll($qfc);

                                $acusrinsurCoverage = ceil($acusrinsurCoverage / 10000) * 10000;
                                if (isset($insurDetails) && !empty($insurDetails)) {
// This is where you have an insurance and you were asked to increase coverage (action id = 2),
// but coverage needed is now 0 ($acusrinsurCoverage = 0), so congratulations you are done
                                    if ($acusrinsurCoverage == 0 && $vals->actionid == $this->increaseLifeInsurance) {
                                        $this->updateActionstep($uid, $this->increaseLifeInsurance, $vals->actionstatus);
                                    }
// This is where It said get a new insurance (action id = 3), and now we found an insurance policy
// so congratulations you are done
                                    if ($vals->actionid == $this->getLifeInsurance) {
                                        $this->updateActionstep($uid, $this->getLifeInsurance, $vals->actionstatus);
                                    }
                                } else {

// Checking if life insurance more information step already exists.
                                    $moreLifeInsurance = false;
                                    foreach ($checkqry as $searchqry) {
                                        if ($searchqry->actionid == $this->moreInsurance) {
                                            if ($searchqry->flexi1) {
                                                $linkArray = array();
                                                $links = explode(' id="', $searchqry->flexi1);
                                                foreach ($links as $key => $link) {
                                                    $linkname = explode('"', $link);
                                                    $linkArray[] = $linkname[0];
                                                }
                                                if ($linkArray) {
                                                    foreach ($linkArray as $linkname) {
                                                        if ($linkname == "addInsurancelifeinsurance") {
                                                            $moreLifeInsurance = true;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

// This is where you no longer need coverage, but we had an action step that say get a new insurance
// policy for X Coverage (action id = 3), so we just quietly delete this
                                    if (($acusrinsurCoverage == 0 || $moreLifeInsurance) && $vals->actionid == $this->getLifeInsurance) {
                                        $this->updateActionstep($uid, $this->getLifeInsurance, $this->actionNew);
                                    }
// This is where he had an action step to increase the coverage of an existing insurance (action id = 2),
// and now we found he no longer has the insurance so we should quietly delete this
                                    if ($vals->actionid == $this->increaseLifeInsurance) {
                                        $this->updateActionstep($uid, $this->increaseLifeInsurance, $this->actionNew);
                                    }
                                }
                            }
// Update or Remove Action step : (#35, #36) For Disability Insurance section
                            if ($getval->link == 'addinsurance' && in_array($vals->actionid, array(35, 36))) {
                                $qfc = new CDbCriteria();
                                $qfc->condition = "user_id=:user_id AND status=0 AND type=:type";
                                $qfc->limit = 1;
                                $qfc->select = 'annualpremium';
                                $qfc->params = array('user_id' => $uid, 'type' => 'DISA');
                                $insurDetails = $insurObj->findAll($qfc);

                                $disabilityInsurCoverage = ceil($disabilityInsurCoverage / 100) * 100;
                                if (isset($insurDetails) && !empty($insurDetails)) {
// This is where you have an insurance and you were asked to increase coverage (action id = 35),
// but coverage needed is now 0 ($disabilityInsurCoverage = 0), so congratulations you are done
                                    if ($disabilityInsurCoverage == 0 && $vals->actionid == $this->increaseDisabilityInsurance) {
                                        $this->updateActionstep($uid, $this->increaseDisabilityInsurance, $vals->actionstatus);
                                    }
// This is where It said get a new insurance (action id = 36), and now we found an insurance policy
// so congratulations you are done
                                    if ($vals->actionid == $this->getDisabilityInsurance) {
                                        $this->updateActionstep($uid, $this->getDisabilityInsurance, $vals->actionstatus);
                                    }
                                } else {

// Checking if disability insurance more information step already exists.
                                    $moreDisabilityInsurance = false;
                                    foreach ($checkqry as $searchqry) {
                                        if ($searchqry->actionid == $this->moreInsurance) {
                                            if ($searchqry->flexi1) {
                                                $linkArray = array();
                                                $links = explode(' id="', $searchqry->flexi1);
                                                foreach ($links as $key => $link) {
                                                    $linkname = explode('"', $link);
                                                    $linkArray[] = $linkname[0];
                                                }
                                                if ($linkArray) {
                                                    foreach ($linkArray as $linkname) {
                                                        if ($linkname == "addInsurancedisabilityinsurance") {
                                                            $moreDisabilityInsurance = true;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

// This is where you no longer need coverage, but we had an action step that say get a new insurance
// policy for X Coverage (action id = 36), so we just quietly delete this
                                    if (($disabilityInsurCoverage == 0 || $moreDisabilityInsurance) && $vals->actionid == $this->getDisabilityInsurance) {
                                        $this->updateActionstep($uid, $this->getDisabilityInsurance, $this->actionNew);
                                    }
// This is where he had an action step to increase the coverage of an existing insurance (action id = 35),
// and now we found he no longer has the insurance so we should quietly delete this
                                    if ($vals->actionid == $this->increaseDisabilityInsurance) {
                                        $this->updateActionstep($uid, $this->increaseDisabilityInsurance, $this->actionNew);
                                    }
                                }
                            }
// Update or Remove Action step : Consider Your Estate Planning Needs (#46)
                            if ($getval->link == 'planestate' && $vals->actionid == $this->estatePlanning) {
                                $esrow = $estiObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => 'whichyouhave'));
                                $mrow = $miscObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => 'misctrust'));
                                if (isset($spouseAge) && isset($child1Age) && isset($child2Age) && isset($child3Age) && isset($child4Age)) {
                                    if ($spouseAge > 0 || $child1Age > 0 || $child2Age > 0 || $child3Age > 0 || $child4Age > 0) {
                                        if (isset($mrow->misctrust) && $mrow->misctrust == '1') {
                                            $this->updateActionstep($uid, $this->estatePlanning, $this->actionNew);
                                        } else if (isset($esrow->whichyouhave) && (strpos($esrow->whichyouhave, 'Will') !== false || strpos($esrow->whichyouhave, 'Trust') !== false)) {
                                            $this->updateActionstep($uid, $this->estatePlanning, $this->actionNew);
                                        }
                                    } else {
                                        if (isset($userSumOfAssets) && isset($userSumOfOtherAssets) && isset($userSumOfDebts)) {
                                            $sumOfAssets = $userSumOfAssets + $userSumOfOtherAssets;
                                            $netWorth = $sumOfAssets - $userSumOfDebts;
                                            if ($netWorth <= 100000) {
                                                // delete action step
                                                $this->updateActionstep($uid, $this->estatePlanning, $this->actionNew);
                                            } else if (isset($mrow->misctrust) && $mrow->misctrust == '1') {
                                                $this->updateActionstep($uid, $this->estatePlanning, $this->actionNew);
                                            } else if (isset($esrow->whichyouhave) && (strpos($esrow->whichyouhave, 'Will') !== false || strpos($esrow->whichyouhave, 'Trust') !== false)) {
                                                $this->updateActionstep($uid, $this->estatePlanning, $this->actionNew);
                                            }
                                        }
                                    }
                                }
                            }
                            // Update or Remove Action step : Tell us more about Will & Trust (#30)
                            if ($getval->link == 'addestate' && $vals->actionid == $this->moreWillAndTrust) {
                                $esrow = $estiObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('whichyouhave')));
                                $miscDetails = $miscObj->findAll(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('misctrust', 'miscreviewmonth', 'miscreviewyear')));
                                if (!empty($miscDetails)) {
                                    foreach ($miscDetails as $mrow) {
                                        if (isset($esrow->whichyouhave) && (strpos($esrow->whichyouhave, 'Will') !== false || strpos($esrow->whichyouhave, 'Trust') !== false) && $mrow->misctrust <> '') {
                                            if ($mrow->misctrust == 1 && $mrow->miscreviewmonth != '' && $mrow->miscreviewyear != 'Year') {
                                                $this->updateActionstep($uid, $this->moreWillAndTrust, $vals->actionstatus);
                                            } else if ($mrow->misctrust == 0) {
                                                $this->updateActionstep($uid, $this->moreWillAndTrust, $vals->actionstatus);
                                            }
                                        }
                                    }
                                }
                            }
// Update or Remove Action step : Obtain and Review Current Credit Score (#23)
                            if ($getval->link == 'addmore' && $vals->actionid == $this->reviewCreditScore) {
                                $miscDetails = $miscObj->findAll(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('morecreditscore', 'moreinvrebal')));
                                if (!empty($miscDetails)) {
                                    foreach ($miscDetails as $mrow) {
                                        if ($mrow->morecreditscore > 0) {
                                            $this->updateActionstep($uid, $this->reviewCreditScore, $vals->actionstatus);
                                        }
                                    }
                                }
                            }
// Update or Remove Action step : Consider Setting Up Your Investment Portfolios on Auto-Rebalance (#42)
                            if ($getval->link == 'addmore' && $vals->actionid == $this->addAutoRebalance) {
                                if ($investmentAutomatically) {
                                    $this->updateActionstep($uid, $this->addAutoRebalance, $vals->actionstatus);
                                }
                            }
                            if ($getval->link == 'addestate' && $vals->actionid == $this->createInformationalSheet) {
                                if ($informationListOfHiddenAsset) {
                                    $this->updateActionstep($uid, $this->createInformationalSheet, $vals->actionstatus);
                                }
                            }

// Update or Remove Action step : Fill in Estate Planning (#28) & Update Will and Other Estate Planning Docs (#14) For miscellaneous estate section
                            if ($getval->link == 'addestate' && ($vals->actionid == $this->fillMiscEstatePlanning || $vals->actionid == $this->updateWillAndEstatePlanning)) {
                                $miscDetails = $miscObj->findAll(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('misctrust', 'miscreviewmonth',
                                        'miscreviewyear', 'mischiddenasset', 'miscliquid', 'miscspouse', 'miscrightperson')));
                                if (!empty($miscDetails)) {
                                    foreach ($miscDetails as $mrow) {
                                        if ($vals->actionid == $this->fillMiscEstatePlanning) {
                                            $resultArray_EstatePlanning = $this->fillMiscEstatePlanningStepNeeded($mrow, $sengineObj);
                                            if ($resultArray_EstatePlanning['completeActionStep']) {
                                                $this->updateActionstep($uid, $this->fillMiscEstatePlanning, $vals->actionstatus);
                                            } elseif (!$resultArray_EstatePlanning['needsActionStep']) {
                                                $this->updateActionstep($uid, $this->fillMiscEstatePlanning, $this->actionNew);
                                            }
                                        }
                                        /* if ((($mrow->mischiddenasset == '1' && $mrow->miscrightperson <> '') || $mrow->mischiddenasset == '0') &&
                                          (($mrow->misctrust == '1' && $mrow->miscreviewyear <> '' && $mrow->miscreviewmonth <> '') || $mrow->misctrust == '0') &&
                                          $mrow->miscliquid <> '' && $mrow->miscspouse <> '' && $vals->actionid == $this->fillMiscEstatePlanning) {
                                          $this->updateActionstep($uid, $this->fillMiscEstatePlanning, $vals->actionstatus);
                                          } */
                                        if ($vals->actionid == $this->updateWillAndEstatePlanning) {
                                            if (isset($mrow->miscreviewyear) && $mrow->miscreviewyear != 'Year' && $mrow->miscreviewyear != '' && ((date('Y') - $mrow->miscreviewyear) <= 4) && $vals->actionid == $this->updateWillAndEstatePlanning) {
                                                $this->updateActionstep($uid, $this->updateWillAndEstatePlanning, $vals->actionstatus);
                                            }
                                        }
                                    }
                                }
                            }


// Update or Remove Action step : Fill in Tax Section (#27)
                            if ($getval->link == 'addtax' && $vals->actionid == $this->fillMiscTax) {
                                $miscDetails = $miscObj->findAll(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('taxpay', 'taxbracket',
                                        'taxvalue', 'taxcontri', 'taxStdOrItemDed')));
                                if (!empty($miscDetails)) {
                                    foreach ($miscDetails as $mrow) {
                                        if ($mrow->taxpay <> '' && $mrow->taxbracket <> '' && $mrow->taxbracket <> '4' && $mrow->taxvalue <> '' && $mrow->taxvalue <> '3' && $mrow->taxcontri <> '' && $mrow->taxStdOrItemDed <> '' && $vals->actionid == $this->fillMiscTax) {
                                            $this->updateActionstep($uid, $this->fillMiscTax, $vals->actionstatus);
                                        }
                                    }
                                }
                            }

// Update or Remove Action step : Complete Risk Tolerance Preference (#7) & Review Risk Tolerance Preference (#38)
                            if ($getval->link == 'addrisk' && $vals->actionid == $this->completeRiskTolerance && $userRiskValue > 0) {
                                $this->updateActionstep($uid, $this->completeRiskTolerance, $vals->actionstatus);
                            }
                            if ($getval->link == 'reviewrisk' && $vals->actionid == $this->reviewRiskTolerance && $userRiskValue >= 3 && $userRiskValue <= 8) {
                                $this->updateActionstep($uid, $this->reviewRiskTolerance, $this->actionNew);
                            }
                            if ($vals->actionid == $this->noncorrelatedAltInvestment) {
                                if ($nonCoreelatedTicker) {
                                    $this->updateActionstep($uid, $this->noncorrelatedAltInvestment, $this->actionNew, true);
                                } else {
                                    $articleIds = array();
                                    $narray = explode('|', $getval->articles);
                                    foreach ($narray as $k => $nval) {
                                        $artdiv = explode('#', $nval);
                                        $articleIds[] = $artdiv[2];
                                    }

                                    if (!empty($articleIds)) {
                                        $artids = implode(',', $articleIds);
                                        $mediaObj = UserMedia::model()->findAll("user_id=:user_id and media_id in ($artids) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        if (isset($mediaObj) && !empty($mediaObj)) {
                                            foreach ($mediaObj as $media) {
                                                if (($key = array_search($media->media_id, $articleIds)) !== false) {
                                                    unset($articleIds[$key]);
                                                }
                                            }
                                        }
                                    }
                                    if (empty($articleIds)) {
                                        $this->updateActionstep($uid, $this->noncorrelatedAltInvestment, $vals->actionstatus, true);
                                    }
                                }
                            }
                        } else {

                            // Update or Remove Action step : Life Insurance(#4), Disability Insurance(#5), Investment Diversification(#8), Debt Improvement Options(#18), Knowledge of Debts and Liabilities(#19) - Videos Section.
                            if ($getval->linktype == 'video') {

                                if ($vals->actionid == $this->investmentDiversificationVideo) {
// Delete this action step if the user has watched the video, no longer needs Investment Diversification, or has completed the learning center articles.
                                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->investmentDiversificationVideo), 'select' => array('articles')));

                                    // Pull the video information from the articles column of actionstepmeta table
                                    $articleId = "";
                                    $narray = $actionStepMeta->articles;
                                    $artdiv = explode('#', $narray);
                                    if (isset($artdiv[2])) {
                                        $articleId = $artdiv[2];
                                    }

                                    // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                    $articleCount = -1;
                                    if ($articleId != "") {
                                        $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    }

                                    if ($articleCount > 0) {
                                        $this->updateActionstep($uid, $this->investmentDiversificationVideo, $vals->actionstatus);
                                    } else if ($articleCount == -1 || ($articleCount == 0 && $point10 >= 100)) {
                                        $this->updateActionstep($uid, $this->investmentDiversificationVideo, $this->actionNew);
                                    }
                                }
                                if ($vals->actionid == $this->lifeInsuranceVideo) {
// Delete this action step if the user has watched the video, has insurance, or has completed the learning center articles.
                                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->lifeInsuranceVideo), 'select' => array('articles')));

                                    // Pull the video information from the articles column of actionstepmeta table
                                    $articleId = "";
                                    $narray = $actionStepMeta->articles;
                                    $artdiv = explode('#', $narray);
                                    if (isset($artdiv[2])) {
                                        $articleId = $artdiv[2];
                                    }

                                    // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                    $articleCount = -1;
                                    if ($articleId != "") {
                                        $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    }

                                    if ($articleCount > 0) {
                                        $this->updateActionstep($uid, $this->lifeInsuranceVideo, $vals->actionstatus);
                                    } else if ($articleCount == -1 || ($articleCount == 0 && $acusrinsurCoverage <= 0)) {
                                        $this->updateActionstep($uid, $this->lifeInsuranceVideo, $this->actionNew);
                                    }
                                }
                                if ($vals->actionid == $this->disabilityInsuranceVideo) {
// Delete this action step if the user has watched the video, has disability insurance, or has completed the learning center articles.
                                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->disabilityInsuranceVideo), 'select' => array('articles')));

                                    // Pull the video information from the articles column of actionstepmeta table
                                    $articleId = "";
                                    $narray = $actionStepMeta->articles;
                                    $artdiv = explode('#', $narray);
                                    if (isset($artdiv[2])) {
                                        $articleId = $artdiv[2];
                                    }

                                    // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                    $articleCount = -1;
                                    if ($articleId != "") {
                                        $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    }

                                    if ($articleCount > 0) {
                                        $this->updateActionstep($uid, $this->disabilityInsuranceVideo, $vals->actionstatus);
                                    } else if ($articleCount == -1 || ($articleCount == 0 && $disabilityInsurCoverage <= 0)) {
                                        $this->updateActionstep($uid, $this->disabilityInsuranceVideo, $this->actionNew);
                                    }
                                }
                                // Delete the High Returns vs Savings Action Step if if the user has watched the video, or has completed the learning center articles //
                                if ($vals->actionid == $this->highReturnsVideo) {
                                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->highReturnsVideo), 'select' => array('articles')));
                                    // Pull the video information from the articles column of actionstepmeta table
                                    $articleId = "";
                                    $narray = $actionStepMeta->articles;
                                    $artdiv = explode('#', $narray);
                                    if (isset($artdiv[2])) {
                                        $articleId = $artdiv[2];
                                    }
                                    // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                    $articleCount = -1;
                                    if ($articleId != "") {
                                        $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    }

                                    $assetValue = Assets::model()->find(array('condition' => 'user_id = :user_id AND type <> "EDUC" and status = 0',
                                        'params' => array('user_id' => $uid), 'select' => 'SUM(contribution) AS contribution'));
                                    $contributionValue = 0;
                                    if($assetValue) {
                                        $contributionValue = $assetValue->contribution;
                                    }
                                    if ($articleCount > 0) {
                                        $this->updateActionstep($uid, $this->highReturnsVideo, $vals->actionstatus);
                                    } else if ($articleCount == -1 || ($contributionValue >= $grossincome * 0.05 || $userRiskValue < 7)) {
                                        $this->updateActionstep($uid, $this->highReturnsVideo, $this->actionNew);
                                    }
                                }

                                // Delete the Property and Casualty Insurance Action Step if if the user has watched the video, or has completed the learning center articles //
                                if ($vals->actionid == $this->propertyAndCasualtyInsVideo) {
                                    $pacPropInsVidChk = 1;
                                    $propAssetCount = Assets::model()->count("user_id=:user_id AND type IN ('PROP') and status=0", array("user_id" => $uid));
                                    if ($propAssetCount > 0) {
                                        $pacPropInsurance = Insurance::model()->findAllBySql("SELECT reviewyear FROM insurance WHERE type IN ('HOME') AND user_id=:user_id AND status=0", array("user_id" => $uid));
                                        if ($pacPropInsurance) {
                                            $year = date('Y');
                                            foreach ($pacPropInsurance as $pacPropInsuranceDetail) {
                                                $pacPropInsurancereviewYear = $pacPropInsuranceDetail->reviewyear;
                                                if (($year - $pacPropInsurancereviewYear) < 3) {
                                                    $pacPropInsVidChk = 1;
                                                } else {
                                                    $pacPropInsVidChk = 0;
                                                }
                                            }
                                        } else {
                                            $pacPropInsVidChk = 0;
                                        }
                                    }
                                    $pacVehiInsVidChk = 1;
                                    $vehiAssetCount = Assets::model()->count("user_id=:user_id AND type IN ('VEHI') and status=0", array("user_id" => $uid));
                                    if ($vehiAssetCount > 0) {
                                        $pacVehiInsurance = Insurance::model()->findAllBySql("SELECT reviewyear FROM insurance WHERE type IN ('VEHI') AND user_id=:user_id AND status=0", array("user_id" => $uid));
                                        if ($pacVehiInsurance) {
                                            $year = date('Y');
                                            foreach ($pacVehiInsurance as $pacVehiInsuranceDetail) {
                                                $pacVehiInsurancereviewYear = $pacVehiInsuranceDetail->reviewyear;
                                                if ($year - $pacVehiInsurancereviewYear < 3) {
                                                    $pacVehiInsVidChk = 1;
                                                } else {
                                                    $pacVehiInsVidChk = 0;
                                                }
                                            }
                                        } else {
                                            $pacVehiInsVidChk = 0;
                                        }
                                    }
                                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->propertyAndCasualtyInsVideo), 'select' => array('articles')));
                                    // Pull the video information from the articles column of actionstepmeta table
                                    $articleId = "";
                                    $narray = $actionStepMeta->articles;
                                    $artdiv = explode('#', $narray);
                                    if (isset($artdiv[2])) {
                                        $articleId = $artdiv[2];
                                    }
                                    // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                    $articleCount = -1;
                                    if ($articleId != "") {
                                        $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    }

                                    if ($articleCount > 0) {
                                        $this->updateActionstep($uid, $this->propertyAndCasualtyInsVideo, $vals->actionstatus);
                                    } else if ($articleCount == -1 || ($articleCount == 0 && $pacPropInsVidChk == 1 && $pacVehiInsVidChk == 1)) {
                                        $this->updateActionstep($uid, $this->propertyAndCasualtyInsVideo, $this->actionNew);
                                    }
                                }

                                if ($vals->actionid == $this->inflationVideo) {
                                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->inflationVideo), 'select' => array('articles')));

                                    // Pull the video information from the articles column of actionstepmeta table
                                    $articleId = "";
                                    $narray = $actionStepMeta->articles;
                                    $artdiv = explode('#', $narray);
                                    if (isset($artdiv[2])) {
                                        $articleId = $artdiv[2];
                                    }

                                    // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                    $articleCount = -1;
                                    if ($articleId != "") {
                                        $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    }

                                    if ($articleCount > 0) {
                                        $this->updateActionstep($uid, $this->inflationVideo, $vals->actionstatus);
                                    } else if ($articleCount == -1 || ($articleCount == 0 && $point12 >= 200)) {
                                        $this->updateActionstep($uid, $this->inflationVideo, $this->actionNew);
                                    }
                                }

                                if ($vals->actionid == $this->budgetingAndCashFlowVideo) {
// Delete this action step if the user has watched the video, Budget and Cash Flow.
                                    $goalCount = $goalObj->count(array('condition' => "user_id = :user_id and goalstatus = 1 and status = 'Needs Attention'", 'params' => array("user_id" => $uid)));

                                    $highDebtRatio = false;
                                    $mdebtDet = $debtObj->find(array('condition' => "user_id=:user_id AND type <> 'MORT' AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid), 'select' => 'SUM(amtpermonth) AS total'));
                                    if (isset($mdebtDet->total)) {
                                        $new_gross = (20 / 100) * $grossincome;
                                        if ($mdebtDet->total >= $new_gross) {
                                            $highDebtRatio = true;
                                        }
                                    }

                                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->budgetingAndCashFlowVideo), 'select' => array('articles')));

                                    // Pull the video information from the articles column of actionstepmeta table
                                    $articleId = "";
                                    $narray = $actionStepMeta->articles;
                                    $artdiv = explode('#', $narray);
                                    if (isset($artdiv[2])) {
                                        $articleId = $artdiv[2];
                                    }

                                    // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                    $articleCount = -1;
                                    if ($articleId != "") {
                                        $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    }

                                    if ($articleCount > 0) {
                                        $this->updateActionstep($uid, $this->budgetingAndCashFlowVideo, $vals->actionstatus);
                                    } else if ($articleCount == -1 || ($articleCount == 0 && ($contributions > 0 && $goalCount == 0 && !$highDebtRatio))) {
                                        $this->updateActionstep($uid, $this->budgetingAndCashFlowVideo, $this->actionNew);
                                    }
                                }

                                // Delete this action step if the user has watched the video, has willortrust and reviewd within 4 years.
                                if ($vals->actionid == $this->estatePlanningVideo) {
                                    $miscObj = new Misc();
                                    $espvideoChk = 0;
                                    $mrow = $miscObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid)));
                                    if (isset($mrow) && $mrow->misctrust == '1') {
                                        if (isset($mrow->miscreviewyear) && $mrow->miscreviewyear != 'Year' && $mrow->miscreviewyear != '') {
                                            if ((date('Y') - $mrow->miscreviewyear) <= 4) {
                                                $espvideoChk = 1;
                                            }
                                        }
                                    }

                                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->estatePlanningVideo), 'select' => array('articles')));

                                    // Pull the video information from the articles column of actionstepmeta table
                                    $articleId = "";
                                    $narray = $actionStepMeta->articles;
                                    $artdiv = explode('#', $narray);
                                    if (isset($artdiv[2])) {
                                        $articleId = $artdiv[2];
                                    }

                                    // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                    $articleCount = -1;
                                    if ($articleId != "") {
                                        $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    }

                                    if ($articleCount > 0) {
                                        $this->updateActionstep($uid, $this->estatePlanningVideo, $vals->actionstatus);
                                    } else if ($articleCount == -1 || ($articleCount == 0 && $espvideoChk == 1)) {
                                        $this->updateActionstep($uid, $this->estatePlanningVideo, $this->actionNew);
                                    }
                                }

                                // Delete this action step if the user has watched video, or fill information in  Miscellaneous Tax Section
                                if ($vals->actionid == $this->taxPlanningVideo) {
                                    $tpvideoChk = 0;
                                    $miscDetails = $miscObj->findAll(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('taxpay', 'taxbracket',
                                            'taxvalue', 'taxcontri', 'taxStdOrItemDed')));
                                    if (!empty($miscDetails)) {
                                        foreach ($miscDetails as $mrow) {
                                            if ($mrow->taxpay <> '' && $mrow->taxbracket <> '' && $mrow->taxbracket <> '4' && $mrow->taxvalue <> '' && $mrow->taxvalue <> '3' && $mrow->taxcontri <> '' && $mrow->taxStdOrItemDed <> '') {
                                                $tpvideoChk = 1;
                                            }
                                        }
                                    }

                                    $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->taxPlanningVideo), 'select' => array('articles')));

                                    // Pull the video information from the articles column of actionstepmeta table
                                    $articleId = "";
                                    $narray = $actionStepMeta->articles;
                                    $artdiv = explode('#', $narray);
                                    if (isset($artdiv[2])) {
                                        $articleId = $artdiv[2];
                                    }

                                    // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                    $articleCount = -1;
                                    if ($articleId != "") {
                                        $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                    }

                                    if ($articleCount > 0) {
                                        $this->updateActionstep($uid, $this->taxPlanningVideo, $vals->actionstatus);
                                    } else if ($articleCount == -1 || ($articleCount == 0 && $tpvideoChk == 1)) {
                                        $this->updateActionstep($uid, $this->taxPlanningVideo, $this->actionNew);
                                    }
                                }

                                if (in_array($vals->actionid, array($this->debtImprovementOptions, $this->knowledgeDebtsAndLiabilities))) {
                                    $consumerDebtDetails = $debtObj->find(array('condition' => "user_id=:user_id AND type <> :debttype AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid, "debttype" => "MORT"), 'select' => 'SUM(amtpermonth) AS total'));
                                    $consumerDebtTrigger = true;
                                    if (isset($consumerDebtDetails->total)) {
                                        $new_gross = (20 / 100) * $grossincome;
                                        $consumerDebtTrigger = ($consumerDebtDetails->total < $new_gross);
                                    }

                                    if ($vals->actionid == $this->debtImprovementOptions) {
                                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->debtImprovementOptions), 'select' => array('articles')));

                                        // Pull the video information from the articles column of actionstepmeta table
                                        $articleId = "";
                                        $narray = $actionStepMeta->articles;
                                        $artdiv = explode('#', $narray);
                                        if (isset($artdiv[2])) {
                                            $articleId = $artdiv[2];
                                        }

                                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                        $articleCount = -1;
                                        if ($articleId != "") {
                                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        }

                                        if ($articleCount > 0) {
                                            $this->updateActionstep($uid, $this->debtImprovementOptions, $vals->actionstatus);
                                        } else if ($articleCount == -1 || ($articleCount == 0 && $consumerDebtTrigger)) {
                                            $this->updateActionstep($uid, $this->debtImprovementOptions, $this->actionNew);
                                        }
                                    }

                                    if ($vals->actionid == $this->knowledgeDebtsAndLiabilities) {
                                        $actionStepMeta = $actMetaObj->find(array('condition' => "actionid=:actionid", 'params' => array("actionid" => $this->knowledgeDebtsAndLiabilities), 'select' => array('articles')));

                                        // Pull the video information from the articles column of actionstepmeta table
                                        $articleId = "";
                                        $narray = $actionStepMeta->articles;
                                        $artdiv = explode('#', $narray);
                                        if (isset($artdiv[2])) {
                                            $articleId = $artdiv[2];
                                        }

                                        // see if any of the videos have been watched in last 90 days. If yes, then remove it from the list
                                        $articleCount = -1;
                                        if ($articleId != "") {
                                            $articleCount = UserMedia::model()->count("user_id=:user_id and media_id in ($articleId) and modified >= NOW() - INTERVAL 90 DAY", array("user_id" => $uid));
                                        }

                                        if ($articleCount > 0) {
                                            $this->updateActionstep($uid, $this->knowledgeDebtsAndLiabilities, $vals->actionstatus);
                                        } else if ($articleCount == -1 || ($articleCount == 0 && $consumerDebtTrigger)) {
                                            $this->updateActionstep($uid, $this->knowledgeDebtsAndLiabilities, $this->actionNew);
                                        }
                                    }
                                }
                            }


                            // Update or Remove Action step : Consider Refinance Options on Consumer Debt (#53)
                            if ($vals->actionid == $this->refinanceConsumerDebts) {
                                $loanDetails = $debtObj->findAll(array('condition' => "user_id=:user_id AND type=:debttype AND apr > 10 AND status=0 AND mortgagetype<>'37' AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid, "debttype" => "LOAN"), 'select' => 'balowed'));
                                if (empty($loanDetails)) {
                                    $this->updateActionstep($uid, $this->refinanceConsumerDebts, $vals->actionstatus);
                                }
                            }

                            if ($vals->actionid == $this->consolidateLoans) {
                                if ($point5 == 13 || $point5 == 25) {
                                    $this->updateActionstep($uid, $this->consolidateLoans, $vals->actionstatus);
                                }
                            }

                            // Update or Remove Action step : Refinance Credit Card(s) (#85)
                            if ($vals->actionid == $this->refinanceCreditCard) {
                                $ccDetails = $debtObj->findAll(array('condition' => "user_id=:user_id AND type=:debttype AND apr > 10 AND status=0 AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid, "debttype" => "CC"), 'select' => 'balowed'));
                                if (empty($ccDetails)) {
                                    $this->updateActionstep($uid, $this->refinanceCreditCard, $vals->actionstatus);
                                }
                            }

                            // Update or Remove Action step : Refinance Car Loan(s) (#95)
                            if ($vals->actionid == $this->refinanceCarLoan) {
                                $carLoanDetails = $debtObj->findAll(array('condition' => "user_id=:user_id AND type=:debttype AND apr > 5 AND status=0 AND mortgagetype='37' AND monthly_payoff_balances=0", 'params' => array("user_id" => $uid, "debttype" => "LOAN"), 'select' => 'balowed'));
                                if (empty($carLoanDetails)) {
                                    $this->updateActionstep($uid, $this->refinanceCarLoan, $vals->actionstatus);
                                }
                            }

                            // Update or Remove Action step : Consider Refinance Options on Mortgage Debt (#17)
                            if ($vals->actionid == $this->mortgageDebt) {
                                $mortDetails = $debtObj->findAll(array('condition' => "user_id=:user_id AND type=:debttype AND status=0 AND ((mortgagetype=33 AND apr > 4.75) OR (mortgagetype=34 AND apr > 3.75) OR (mortgagetype=35 AND apr > 3.75))", 'params' => array("user_id" => $uid, "debttype" => "MORT"), 'select' => 'balowed'));
                                if (empty($mortDetails)) {
                                    $this->updateActionstep($uid, $this->mortgageDebt, $vals->actionstatus);
                                }
                            }
                            // Update or Remove Action step : Review Health insurance (#87)
                            if ($vals->actionid == $this->reviewHealthInsurance) {
                                $year = date('Y');
                                $diff = $year - $healthInsuranceReviewYear;
                                if ($healthInsuranceReviewYear > 0 && $diff <= 1) {
                                    $this->updateActionstep($uid, $this->reviewHealthInsurance, $vals->actionstatus);
                                } else if ($healthInsuranceReviewYear == 0) {
                                    $this->updateActionstep($uid, $this->reviewHealthInsurance, $this->actionNew);
                                }
                            }
                            if ($vals->actionid == $this->reviewLifeInsurance) {
                                $year = date('Y');
                                $diff = $year - $lifeInsuranceReviewYear;
                                if ($lifeInsuranceReviewYear > 0 && $diff <= 1) {
                                    $this->updateActionstep($uid, $this->reviewLifeInsurance, $vals->actionstatus);
                                } else if ($lifeInsuranceReviewYear == 0) {
                                    $this->updateActionstep($uid, $this->reviewLifeInsurance, $this->actionNew);
                                }
                            }
                            if ($vals->actionid == $this->reviewDisabilityInsurance) {
                                $year = date('Y');
                                $diff = $year - $disabilityInsuranceReviewYear;
                                if ($disabilityInsuranceReviewYear > 0 && $diff <= 1) {
                                    $this->updateActionstep($uid, $this->reviewDisabilityInsurance, $vals->actionstatus);
                                } else if ($disabilityInsuranceReviewYear == 0) {
                                    $this->updateActionstep($uid, $this->reviewDisabilityInsurance, $this->actionNew);
                                }
                            }
                            // Update or Remove Action step : Create Emergency Fund for Unplanned Costs (#20)
                            if ($vals->actionid == $this->createEmergencyFund) {
                                if ($userSumOfGoalSettingAssets > 0 && $userSumOfGoalSettingAssets >= ($grossexpense * 3)) { // if assets and assets > gross expenses * 3
                                    $this->updateActionstep($uid, $this->createEmergencyFund, $vals->actionstatus);
                                } else if ($userSumOfGoalSettingAssets == 0 && $grossexpense == 0) { // if no assets and no expenses
                                    $this->updateActionstep($uid, $this->createEmergencyFund, $this->actionNew);
                                }
                            }
                            //Health Insurance - Get Coverage
                            if ($vals->actionid == $this->healthInsurance && $getval->link == 'addinsurance') {
                                $getHealthInsDetail = $insurObj->findAll(array('condition' => "user_id=:user_id AND status=0 AND type = 'HEAL'", 'params' => array("user_id" => $uid), 'select' => array('status')));
                                if (isset($getHealthInsDetail) && !empty($getHealthInsDetail)) {
                                    $this->updateActionstep($uid, $this->healthInsurance, $vals->actionstatus);
                                }
                            }

                            /*                             * ***************************************************** */
                            // Update or Remove Action step: Maximize Contribution to Roth IRA (#49)
                            if ($vals->actionid == $this->maximizeRothIra) {
                                $showSteps = true;
                                $crRows = $assetObj->findAll(array('condition' => 'user_id = :user_id AND type = "CR" and status = 0',
                                    'params' => array('user_id' => $uid), 'select' => 'contribution'));
                                $currentContribution = 0;
                                if (isset($crRows) && !empty($crRows)) {
                                    foreach ($crRows as $crRow) {
                                        $currentContribution += $crRow->contribution * 12;
                                    }
                                    if ($currentAge <= 49 && $currentContribution < $this->crMax) {
                                        $showSteps = false;
                                    } else if ($currentAge >= 50 && $currentContribution < $this->over50CrMax) {
                                        $showSteps = false;
                                    }
                                }

                                if ($showSteps) {
                                    $married = $uinfoObj->count(array('condition' => "user_id=:user_id AND maritalstatus=:mstat", 'params' => array("user_id" => $uid, "mstat" => "Married")));
                                    // Check marital status and income levels.
                                    $spouseIncomeCheck = false;
                                    $householdIncome = $grossincome * 12;
                                    if ($married > 0 && $householdIncome < 178000) {
                                        $spouseIncomeCheck = true;
                                    }
                                    if ($married <= 0 && $householdIncome < 112000) {
                                        $spouseIncomeCheck = true;
                                    }
                                    if ($spouseIncomeCheck == true) {
                                        $miscRow = $miscObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid),
                                            'select' => array('user_id', 'taxpay', 'taxvalue')));
                                        if ($miscRow && ($miscRow->taxpay != 0 || ($miscRow->taxvalue != '1' && $miscRow->taxvalue != '2'))) {
                                            $rothRow = $assetObj->find(array('condition' => 'user_id = :user_id AND type = "IRA" and status = 0',
                                                'params' => array('user_id' => $uid), 'select' => 'SUM(contribution) AS contribution'));
                                            $currentContribution = 0;
                                            if ($rothRow) {
                                                $currentContribution = $rothRow->contribution * 12;
                                            }

                                            $incDetails = $incObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid), 'select' => array('gross_income', 'spouse_income')));
                                            $currentIncome = 0;
                                            if ($incDetails) {
                                                $currentIncome = ($incDetails->gross_income + $incDetails->spouse_income) * 12;
                                            }
                                            $recommendedContribution = 0;
                                            if ($currentAge <= 49) {
                                                if ($currentIncome > $this->iraMax) {
                                                    $recommendedContribution = $this->iraMax - $currentContribution;
                                                } else {
                                                    $recommendedContribution = $currentIncome - $currentContribution;
                                                }
                                            } else {
                                                if ($currentIncome > $this->over50IraMax) {
                                                    $recommendedContribution = $this->over50IraMax - $currentContribution;
                                                } else {
                                                    $recommendedContribution = $currentIncome - $currentContribution;
                                                }
                                            }
                                            $monthlyRecommendedContribution = number_format(round($recommendedContribution / 120) * 10);
                                            if ($monthlyRecommendedContribution <= 0) {
                                                $this->updateActionstep($uid, $this->maximizeRothIra, $vals->actionstatus);
                                            }
                                        } else {
                                            $this->updateActionstep($uid, $this->maximizeRothIra, $this->actionNew);
                                        }
                                    } else {
                                        $this->updateActionstep($uid, $this->maximizeRothIra, $this->actionNew);
                                    }
                                } else {
                                    $this->updateActionstep($uid, $this->maximizeRothIra, $this->actionNew);
                                }
                            }
                            // Update or Remove Action step: Maximize Contribution to Traditional IRA (#43)
                            if ($vals->actionid == $this->maximizeTraditionalIra) {
                                $showSteps = true;
                                $crRows = $assetObj->findAll(array('condition' => 'user_id = :user_id AND type = "CR" and status = 0',
                                    'params' => array('user_id' => $uid), 'select' => 'contribution'));
                                $currentContribution = 0;
                                if (isset($crRows) && !empty($crRows)) {
                                    foreach ($crRows as $crRow) {
                                        $currentContribution += $crRow->contribution * 12;
                                    }
                                    if ($currentAge <= 49 && $currentContribution < $this->crMax) {
                                        $showSteps = false;
                                    } else if ($currentAge >= 50 && $currentContribution < $this->over50CrMax) {
                                        $showSteps = false;
                                    }
                                }

                                if ($showSteps) {
                                    $miscRow = $miscObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid),
                                        'select' => array('user_id', 'taxpay', 'taxvalue')));
                                    if ($miscRow && $miscRow->taxpay == 0 && ($miscRow->taxvalue == '1' || $miscRow->taxvalue == '2')) {
                                        $iraSum = $assetObj->find(array('condition' => 'user_id = :user_id AND type = "IRA" and status = 0',
                                            'params' => array('user_id' => $uid), 'select' => 'SUM(contribution) AS contribution'));
                                        $currentContribution = 0;
                                        if ($iraSum) {
                                            $currentContribution = $iraSum->contribution * 12;
                                        }

                                        $incDetails = $incObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid), 'select' => array('gross_income', 'spouse_income')));
                                        $currentIncome = 0;
                                        if ($incDetails) {
                                            $currentIncome = ($incDetails->gross_income + $incDetails->spouse_income) * 12;
                                        }

                                        $recommendedContribution = 0;
                                        if ($currentAge <= 49) {
                                            if ($currentIncome > $this->iraMax) {
                                                $recommendedContribution = $this->iraMax - $currentContribution;
                                            } else {
                                                $recommendedContribution = $currentIncome - $currentContribution;
                                            }
                                        } else {
                                            if ($currentIncome > $this->over50IraMax) {
                                                $recommendedContribution = $this->over50IraMax - $currentContribution;
                                            } else {
                                                $recommendedContribution = $currentIncome - $currentContribution;
                                            }
                                        }
                                        $monthlyRecommendedContribution = number_format(round($recommendedContribution / 120) * 10);
                                        if ($monthlyRecommendedContribution <= 0) {
                                            $this->updateActionstep($uid, $this->maximizeTraditionalIra, $vals->actionstatus);
                                        }
                                    } else {
                                        $this->updateActionstep($uid, $this->maximizeTraditionalIra, $this->actionNew);
                                    }
                                } else {
                                    $this->updateActionstep($uid, $this->maximizeTraditionalIra, $this->actionNew);
                                }
                            }

                            // Update or Remove Action step: Consider Decreasing Tax Withholding Amount using W4 Form at Work (#47)
                            if ($vals->actionid == $this->decreaseW4TaxWithholding) {
                                $hasMaximizeStep = false;
                                foreach ($checkqry as $currentstep) {
                                    if ($currentstep->actionid == $this->maximizeRothIra) {
                                        $hasMaximizeStep = true;
                                    }
                                }
                                if (!$hasMaximizeStep) {
                                    $showSteps = true;
                                    $crRows = $assetObj->findAll(array('condition' => 'user_id = :user_id AND type = "CR" and status = 0',
                                        'params' => array('user_id' => $uid), 'select' => 'contribution'));
                                    $currentContribution = 0;
                                    if (isset($crRows) && !empty($crRows)) {
                                        foreach ($crRows as $crRow) {
                                            $currentContribution += $crRow->contribution * 12;
                                        }
                                        if ($currentAge <= 49 && $currentContribution < $this->crMax) {
                                            $showSteps = false;
                                        } else if ($currentAge >= 50 && $currentContribution < $this->over50CrMax) {
                                            $showSteps = false;
                                        }
                                    }

                                    if ($showSteps) {
                                        $miscRow = $miscObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid),
                                            'select' => array('user_id', 'taxpay', 'taxvalue')));
                                        if (!$miscRow || $miscRow->taxpay != 1 || ($miscRow->taxvalue != '1' && $miscRow->taxvalue != '2')) {
                                            $this->updateActionstep($uid, $this->decreaseW4TaxWithholding, $this->actionNew);
                                        }
                                    } else {
                                        $this->updateActionstep($uid, $this->decreaseW4TaxWithholding, $this->actionNew);
                                    }
                                } else {
                                    $this->updateActionstep($uid, $this->decreaseW4TaxWithholding, $this->actionNew);
                                }
                            }
                            // Update or Remove Action step: Consider Increasing Tax Withholding Amount using W4 Form at Work (#91)
                            if ($vals->actionid == $this->increaseW4TaxWithholding) {
                                $hasMaximizeStep = false;
                                foreach ($checkqry as $currentstep) {
                                    if ($currentstep->actionid == $this->maximizeTraditionalIra) {
                                        $hasMaximizeStep = true;
                                    }
                                }
                                if (!$hasMaximizeStep) {
                                    $showSteps = true;
                                    $crRows = $assetObj->findAll(array('condition' => 'user_id = :user_id AND type = "CR" and status = 0',
                                        'params' => array('user_id' => $uid), 'select' => 'contribution'));
                                    $currentContribution = 0;
                                    if (isset($crRows) && !empty($crRows)) {
                                        foreach ($crRows as $crRow) {
                                            $currentContribution += $crRow->contribution * 12;
                                        }
                                        if ($currentAge <= 49 && $currentContribution < $this->crMax) {
                                            $showSteps = false;
                                        } else if ($currentAge >= 50 && $currentContribution < $this->over50CrMax) {
                                            $showSteps = false;
                                        }
                                    }

                                    if ($showSteps) {
                                        $miscRow = $miscObj->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid),
                                            'select' => array('user_id', 'taxpay', 'taxvalue')));
                                        if (!$miscRow || $miscRow->taxpay != 0 || ($miscRow->taxvalue != '1' && $miscRow->taxvalue != '2')) {
                                            $this->updateActionstep($uid, $this->increaseW4TaxWithholding, $this->actionNew);
                                        }
                                    } else {
                                        $this->updateActionstep($uid, $this->increaseW4TaxWithholding, $this->actionNew);
                                    }
                                } else {
                                    $this->updateActionstep($uid, $this->increaseW4TaxWithholding, $this->actionNew);
                                }
                            }

                            /*                             * ************************************************
                             * Added By Rajeev - Check Consider Charitable Donations
                             * action step is completed,  then update the table (#79)
                             * ************************************************* */
                            if ($vals->actionid == $this->considerCharitableDonations) {
                                $mrow = $miscObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $uid), 'select' => array('taxpay', 'morecharity', 'taxStdOrItemDed', 'taxvalue')));
                                if (isset($mrow)) {
                                    if ($mrow->taxpay != '0' || $mrow->morecharity != '1' || $mrow->taxStdOrItemDed == '0' || ($mrow->taxvalue != '1' && $mrow->taxvalue != '2')) {
                                        $this->updateActionstep($uid, $this->considerCharitableDonations, $this->actionNew);
                                    }
                                }
                            }
                            /*                             * *********************************************** */
                            if ($vals->actionid == $this->considerConcentrationOfAssets) {
                                $assetData = parent::getAssetData($uid);
                                $assets = $assetData[0];
                                $insurances = $assetData[1];
                                $pertrack = $assetData[2];

                                $excessAssets = parent::CalculatePoint10($assets, $insurances, $pertrack);
                                if (count($excessAssets) == 0) {
                                    $this->updateActionstep($uid, $this->considerConcentrationOfAssets, $this->actionNew);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Here we set the session to track user completes an action.
     */

    function actionAddtrackuser() {
        $updatedPermission = "";
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
                if (isset(Yii::app()->getSession()->get('wsuser')->permission)) {
                    $updatedPermission = Yii::app()->getSession()->get('wsuser')->permission;
                }

                if ($updatedPermission == "RO") {
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK")));
                } else {
                    $id = $_POST['id'];
                    $event = $_POST['event'];
                    $asObj = new Actionstep();
                    $asObj->updateActionStepStatus($id, $this->actionStarted);  // 3 => action is started
                    // Action IDs 20(Create Emergency Fund for Unplanned Costs), 38(Review Risk Tolerance Preference), 46(Consider Your Estate Planning Needs)
                    // Completes when Green button clicked in the overlay
                    if (isset($event) && ($event == 'reviewrisk' || $event == 'planestate' || $event == 'reviewasset')) {
                        $asObj->updateActionStepStatus($id, $this->actionCompleted); // 1 => action is completed
                    }
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK")));
                }
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Update order of action step by User
     */

    function actionUpdateactionorder() {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $uid = Yii::app()->getSession()->get('wsuser')->id;
                $values = $_POST['values'];
                if ($values) {
                    $int = 0;
                    $idArr = explode('|', $values);
                    foreach ($idArr as $value) {
                        $asObj = new Actionstep();
                        $asObj->updateActionUserOrder($uid, $value, $int);
                        $int++;
                    }
                }
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Here we check the session for checking user done the correct action.
     */

    function actionGetuserreport() {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $p = 0;
                $uid = Yii::app()->getSession()->get('wsuser')->id;
                $asObj = new Actionstep();
                $getall = $asObj->getActionStepForDisplay($uid);

                $uscore = new UserScore();
                $checkqry = $uscore->findBySql("SELECT scoredetails FROM userscore WHERE user_id = :user_id", array("user_id" => $uid));

                if ($getall && $checkqry) {

                    $details = $checkqry->scoredetails;
                    $sengineObj = unserialize($details);

                    foreach ($getall as $ky => $v) {
                        $actionstepdetail = new CDbCacheDependency('SELECT * FROM actionstepmeta');
                        $metadata = Actionstepmeta::model()->cache(QUERY_CACHE_TIMEOUT, $actionstepdetail)->find('actionid = ' . $v['actionid']);

                        $point = $v['points']; // Used to replace above if/else condition
                        $getall[$ky]['points'] = $point;
                        $p = $p + $point;
                        $asObj->updateActionStepStatus($v['id'], $this->actionHistory);
                    }
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'uid' => $uid, 'all' => $getall, 'p' => $p)));
                } else {
                    $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "No action steps completed.")));
                }
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Update actionstep table whenever Learning Center article link clicked on Action steps.  Stored as  articleid1|articleid2...
     */

    function actionUpdatearticle() {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $uid = Yii::app()->getSession()->get('wsuser')->id;
            if (isset($uid)) {
                $artstatus = '';
                $actionid = $_POST['actionid'];
                $articleid = $_POST['articleid'];
                $media_id = $_POST['articleid'];
                $mediaType = "article";

                $actionObj = Actionstep::model()->find("user_id = :user_id and actionid = :actionid", array("user_id" => $uid, "actionid" => $actionid));
                $metaid = $actionObj->actionid;
                $actionObj->actionstatus = $this->actionStarted;
                $actionObj->save();

                if ($metaid != $this->learningArticles && $metaid != $this->noncorrelatedAltInvestment && $metaid != $this->maximizeRothIra && $metaid != $this->maximizeTraditionalIra && $metaid != $this->healthMedicalInsuranceArticle && $metaid != $this->evaluateConsumerDebtCosts && $metaid != $this->evaluateHousingCosts && $metaid != $this->improveCreditScore && $metaid != $this->considerLifeexpectancyRisk && $metaid != $this->examineLifestyleCost && $metaid != $this->flexibilityOfAssets) {
                    $update = Yii::app()->db->createCommand()->update('userscore', array('timestamp' => date("Y-m-d H:i:s")), 'user_id=:user_id', array('user_id' => $uid));
                    $this->sendResponse(200, CJSON::encode(array("status" => "OK", "read" => "Updatedtime")));
                } else {
                    $mediaObj = UserMedia::model()->find("user_id=:user_id and media_id=:media_id", array("media_id" => $articleid, "user_id" => $uid));
                    $increaseMediaCount = true;
                    if (!$mediaObj) {
                        $mediaObj = new UserMedia();
                        $mediaObj->created = new CDbExpression('NOW()');
                    } else {
                        $modified = new DateTime($mediaObj->modified);
                        $interval = $modified->diff(new DateTime());
                        if ($interval->days <= 90) {
                            $increaseMediaCount = false;
                        }
                    }

                    $mediaObj->modified = new CDbExpression('NOW()');
                    $mediaObj->user_id = $uid;
                    $mediaObj->media_id = $articleid;
                    $mediaObj->media_type = $mediaType;
                    $mediaObj->save();

                    $status = "Pending";
                    if ($metaid != $this->learningArticles) {
                        $actionstepmeta = Actionstepmeta::model()->find("actionid =:actionid", array("actionid" => $actionid));
                        $params = "";
                        $mediaArray = array();
                        if ($actionstepmeta) {
                            $narray = explode('|', $actionstepmeta->articles);
                            if ($narray) {
                                foreach ($narray as $k => $nval) {
                                    $artdiv = explode('#', $nval);
                                    if ($params != "") {
                                        $params .= ",";
                                    }
                                    $params .= $artdiv[2];
                                    $mediaArray[$artdiv[2]] = false;
                                }
                            }
                        }

                        $todayDate = new DateTime();
                        $lapseDate = $todayDate->sub(new DateInterval('P90D'));
                        $oldestMedia = UserMedia::model()->findAll("modified > :lapseDate and user_id=:user_id and media_id in (" . $params . ")", array("lapseDate" => $lapseDate->format('Y-m-d H:i:s'), "user_id" => $uid));
                        $count = 0;
                        if ($oldestMedia) {
                            foreach ($oldestMedia as $media) {
                                $mediaArray[$media->media_id] = true;
                                $count++;
                            }
                        }
                        if (count($mediaArray) <= $count) {
                            $status = "Completed";
                            $actionObj = Actionstep::model()->find("user_id = :user_id and actionid = :actionid", array("user_id" => $uid, "actionid" => $actionid));
                            $actionObj->actionstatus = $this->actionCompleted;
                            $actionObj->save();
                        }
                    }

                    if ($increaseMediaCount) {
                        parent::setEngine();
                        $this->sengine->mediaCount++;
                        if ($this->sengine->mediaCount > 10) {
                            $this->sengine->mediaCount = 10;
                        }
                        if ($this->sengine->oldestMediaDate == null) {
                            $todayDate = new DateTime();
                            $this->sengine->oldestMediaDate = $todayDate->format('Y-m-d H:i:s');
                        }
                        parent::saveEngine();

                        if ($this->sengine->mediaCount >= 10) {
                            $asObj = new Actionstep();

                            $learningActionObj = Actionstep::model()->find("user_id = :user_id and actionid = :actionid and actionstatus in ('0','2','3')", array("user_id" => $uid, "actionid" => $this->learningArticles));
                            if (isset($learningActionObj) && $learningActionObj->actionstatus == $this->actionStarted) {
                                $asObj->updateActionStepStatus($learningActionObj->id, $this->actionCompleted);
                            } else if (isset($learningActionObj)) {
                                $asObj->updateActionStepStatus($learningActionObj->id, $this->actionDeleted);
                            }
                            parent::calculateScore("LEARNING", $uid);
                            if ($metaid == $this->learningArticles) {
                                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'totalscore' => $this->totalScore, "message" => "Completed")));
                            } else {
                                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'totalscore' => $this->totalScore, "message" => $status)));
                            }
                        } else {
                            parent::UpdateLearningActionStep($uid, $this->sengine->mediaCount);
                            parent::calculateScore("LEARNING", $uid);
                            $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'totalscore' => $this->totalScore, "message" => $status)));
                        }
                    } else {
                        parent::setEngine();
                        if ($metaid == $this->learningArticles) {
                            if ($this->sengine->mediaCount >= 10) {
                                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'totalscore' => $this->totalScore, "message" => "Completed")));
                            } else {
                                $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'totalscore' => $this->totalScore, "message" => "Pending")));
                            }
                        } else {
                            $this->sendResponse(200, CJSON::encode(array("status" => "OK", 'totalscore' => $this->totalScore, "message" => $status)));
                        }
                    }
                }
            } else {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Could not update the article at this time.")));
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Generic function for fetch all tables details
     */

    function AsqueryResult($modelName, $whereCondition, $isarray = 0) {
        try {
            if ($isarray == 0) {
                $result = $modelName::model()->find(array('condition' => $whereCondition));
            } else {
                $result = $modelName::model()->findAll(array('condition' => $whereCondition));
            }
            if (isset($result)) {
                return $result;
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /**
     * Calculates the current age if the function is called in YYYY-MM-DD format.
     */
    public function CurrentAge($dateTime, $symbol) {
        if (!(isset($symbol))) {
            return false;
        }
        $dateTimeSplit = explode($symbol, $dateTime);
        $currentYear = date('Y');
        $currentAge = $currentYear - $dateTimeSplit[0];
        if ($currentAge > 0) {
            return $currentAge;
        } else {
            return -1;
        }
    }

    /*
     * Read if income/expense set for the user who cuurently logged in, otherwise get it from peer-rank (or) use hard coded value.
     * Works in fall back style
     * From Peer Rank
     *  Income = Average Weighted Income
     *  Expense = Average Debt-Residential+Average Debt-Equity Line+Average Debt-Installment+Average Debt-Credit Card+Average Debt-LOC-Unsecured+Average Debt-Other
     */

    function getIncomeandExpense($type) {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $userid = Yii::app()->getSession()->get('wsuser')->id;
            if (isset($userid)) {
                if ($type == 'INCOME') {
                    $incObj = new Income();
                    $incDetails = $incObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $userid), 'select' => array('totaluserincome')));
                    if (isset($incDetails->totaluserincome)) {
                        $tempIncome = $incDetails->totaluserincome;
                    } else {
                        $infoObj = new Userpersonalinfo();
                        $info = $infoObj->find(array('select' => array('age', 'user_id'), 'condition' => "user_id=:user_id AND age IS NOT NULL", 'params' => array("user_id" => $userid)));
                        if (isset($info->age) && $info->age <> '' && $info->age <> '0000-00-00') {
                            $ageSplit = explode("-", $info->age);
                            $ageInYears = date('Y') - $ageSplit[0];
                            $peerObj = new Peerranking();
                            $pDet = $peerObj->find(array('condition' => "baseage = :baseage", 'params' => array("baseage" => $ageInYears), 'select' => array('income')));
                            if (isset($pDet->income)) {
                                $tempIncome = $pDet->income;
                            } else {
                                $tempIncome = $this->acGrossIncome;
                            }
                        } else {
                            $tempIncome = $this->acGrossIncome;
                        }
                    }
                    return array('income' => $tempIncome);
                }
                if ($type == 'EXPENSE') {
                    $expObj = new Expense();
                    $expDetails = $expObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $userid), 'select' => array('actualexpense')));
                    if (isset($expDetails->actualexpense)) {
                        $tempExp = $expDetails->actualexpense;
                    } else {
                        $infoObj = new Userpersonalinfo();
                        $info = $infoObj->find(array('select' => array('age', 'user_id'), 'condition' => "user_id=:user_id AND age IS NOT NULL", 'params' => array("user_id" => $userid)));
                        if (isset($info->age) && $info->age <> '' && $info->age <> '0000-00-00') {
                            $ageSplit = explode("-", $info->age);
                            $ageInYears = date('Y') - $ageSplit[0];
                            $peerObj = new Peerranking();
                            $pDet = $peerObj->find(array('condition' => "baseage = :baseage", 'params' => array("baseage" => $ageInYears), 'select' => array('debtresi', 'debtequityline', 'debtinstallment', 'debtcc', 'debtloc', 'debtother')));
                            if (isset($pDet)) {
                                $tempExp = $pDet->debtresi + $pDet->debtequityline + $pDet->debtinstallment + $pDet->debtcc + $pDet->debtloc + $pDet->debtother;
                            } else {
                                $tempExp = $this->acGrossExpense;
                            }
                        } else {
                            $tempExp = $this->acGrossExpense;
                        }
                    }
                    return array('expense' => $tempExp);
                }
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    /*
     * Read if asset/debt set for the user who cuurently logged in, otherwise get it from peer-rank (or) use hard coded value.
     * Works in fall back style
     * From Peer Rank
     *  Asset = Average Weighted Assets
     *  Debt = Average Debt-Equity Line+Average Debt-Installment    Average+Debt-Credit Card+Average Debt-LOC-Unsecured+Average Debt-Other
     */

    function getAssetandDebt($type) {
        try {
            if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
            }
            $userid = Yii::app()->getSession()->get('wsuser')->id;
            if (isset($userid)) {
                if ($type == 'ASSET') {
                    $aObj = new Assets();
                    $aDetails = $aObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $userid), 'select' => 'SUM(balance) AS total'));
                    if (isset($aDetails->total)) {
                        $tempBal = $aDetails->total;
                    } else {
                        $infoObj = new Userpersonalinfo();
                        $info = $infoObj->find(array('select' => array('age', 'user_id'), 'condition' => "user_id=:user_id AND age IS NOT NULL", 'params' => array("user_id" => $userid)));
                        if (isset($info->age) && $info->age <> '' && $info->age <> '0000-00-00') {
                            $ageSplit = explode("-", $info->age);
                            $ageInYears = date('Y') - $ageSplit[0];
                            $peerObj = new Peerranking();
                            $pDet = $peerObj->find(array('condition' => "baseage = :baseage", 'params' => array("baseage" => $ageInYears), 'select' => array('assets')));
                            if (isset($pDet->assets)) {
                                $tempBal = $pDet->assets;
                            } else {
                                $tempBal = $this->assetsTotal;
                            }
                        } else {
                            $tempBal = $this->assetsTotal;
                        }
                    }
                    return array('assets' => $tempBal);
                }
                if ($type == 'DEBT') {
                    $dObj = new Debts();
                    $dDetails = $dObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $userid), 'select' => array('SUM(balowed) AS total')));
                    if (isset($dDetails->total)) {
                        $tempDebt = $dDetails->total;
                    } else {
                        $infoObj = new Userpersonalinfo();
                        $info = $infoObj->find(array('select' => array('age', 'user_id'), 'condition' => "user_id=:user_id AND age IS NOT NULL", 'params' => array("user_id" => $userid)));
                        if (isset($info->age) && $info->age <> '' && $info->age <> '0000-00-00') {
                            $ageSplit = explode("-", $info->age);
                            $ageInYears = date('Y') - $ageSplit[0];
                            $peerObj = new Peerranking();
                            $pDet = $peerObj->find(array('condition' => "baseage = :baseage", 'params' => array("baseage" => $ageInYears), 'select' => array('debtequityline', 'debtinstallment', 'debtcc', 'debtloc', 'debtother')));
                            if (isset($pDet)) {
                                $tempDebt = $pDet->debtequityline + $pDet->debtinstallment + $pDet->debtcc + $pDet->debtloc + $pDet->debtother;
                            } else {
                                $tempDebt = $this->debtsTotal;
                            }
                        } else {
                            $tempDebt = $this->debtsTotal;
                        }
                    }
                    return array('debts' => $tempDebt);
                }
            }
        } catch (Exception $E) {
            echo $E;
        }
    }

    function actionCheckactionstep() {

        // sql satisfies once reviewstatus =1 and modifies date is lesser than current date with six month interval
        $actionstep = Actionstep::model()->findAllBySql("SELECT mas.* FROM leapscoremaster.actionstep as mas inner join leapscoremeta.actionstepmeta as metas on mas.actionid = metas.actionid WHERE mas.lastmodifiedtime < CURRENT_DATE() - INTERVAL 6 MONTH and mas.actionstatus = '3' and metas.reviewstatus = '0'");
        if (isset($actionstep)) {

            foreach ($actionstep as $actionStepValue) {
                $actionStepValue->actionstatus = '1';
                $actionStepValue->save();
            }

            Yii::trace("Time:" . date('m-d-Y'));
            Yii::trace(":API call:" . "actionCheckactionstep", "Total action steps status changed: " . count($actionstep) . "");
            $this->sendResponse(200, CJSON::encode(array("status" => "OK")));
        } else {
            $this->sendResponse(200, CJSON::encode(array("status" => "No Records Founds.")));
        }
    }

    function updateComponents() {

        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $todayDate = new DateTime();
        $lapseDate = $todayDate->sub(new DateInterval('P90D'));
        $lastModified = new DateTime();

        parent::setEngine();
        if ($this->sengine->batchFileUpdate) {
            $this->sengine->batchFileUpdate = false;
            parent::saveEngine();
            parent::calculateScore("CALCXML", $user_id);
        }

        $year = date('Y');
        if ($this->sengine->currentYear < $year) {
            $this->sengine->currentYear = $year;
            parent::calculateScore("DEGRADABLEPOINTS", $user_id);
        }

        if (isset($this->sengine->oldestMediaDate->expression)) {
            $oldDate = new DateTime();
            $date = $oldDate->sub(new DateInterval('P91D'));
            $this->sengine->oldestMediaDate = $date->format('Y-m-d H:i:s');
        }

        // Check each constant in the ConstantsLastUpdated table against the constant dates
        // in the score engine, to see if any score sections need updating.
        $constantsNeedingUpdate = ConstantsLastUpdated::model()->findAll(array('condition' => 'lastupdated >= :last_updated', 'params' =>
            array("last_updated" => $this->sengine->lastConstantUpdate), 'select' => 'constant, lastupdated'));

        // Update the lastConstantUpdate date in score engine and recalculate score as needed.
        if ($constantsNeedingUpdate) {
            foreach ($constantsNeedingUpdate as $constant) {
                if ($constant->constant == 'Risk') {
                    parent::setEngine();
                    $this->updateRiskInformation();
                    $this->sengine->lastConstantUpdate = date('Y-m-d');
                    parent::saveEngine();
                    parent::calculateScore("RISK", $user_id);
                } elseif ($constant->constant == 'LifeExpectancy') {
                    parent::setEngine();
                    $this->updateLEInformation();
                    $this->sengine->lastConstantUpdate = date('Y-m-d');
                    parent::saveEngine();
                    parent::calculateScore("PROFILE", $user_id);
                } elseif ($constant->constant == 'Profile') {
                    parent::setEngine();
                    $this->updateProfileScore($user_id);
                    $this->sengine->lastConstantUpdate = date('Y-m-d');
                    parent::saveEngine();
                    parent::calculateScore("COMPLETENESS", $user_id);
                } elseif ($constant->constant == 'Special') {
                    parent::setEngine();
                    $assetData = parent::getAssetData($user_id);
                    $assets = $assetData[0];
                    $insurance = $assetData[1];
                    $pertrack = $assetData[2];
                    parent::CalculatePoint10($assets, $insurance, $pertrack);
                    $this->sengine->lastConstantUpdate = date('Y-m-d');
                    parent::saveEngine();
                    parent::calculateScore("SPECIAL", $user_id);
                } elseif ($constant->constant == 'Media') {
                    parent::setEngine();
                    $this->sengine->lastConstantUpdate = date('Y-m-d');
                    $today = new DateTime();
                    $todayMinus92Days = $today->sub(new DateInterval('P92D'));
                    $this->sengine->oldestMediaDate = $todayMinus92Days->format('Y-m-d H:i:s');
                    parent::saveEngine();
                } elseif ($constant->constant == 'Priority') {
                    parent::setEngine();
                    $this->updatePriorityInformation();
                    $this->sengine->lastConstantUpdate = date('Y-m-d');
                    parent::saveEngine();
                    parent::calculateScore("LEARNING", $user_id);
                } elseif ($constant->constant == 'MonteCarlo') {
                    parent::setEngine();
                    $this->sengine->lastConstantUpdate = date('Y-m-d');
                    parent::saveEngine();
                    parent::calculateScore("SAVINGSACCOUNT", $user_id);
                }
            }
        }


        if (isset($this->sengine->oldestMediaDate) && $this->sengine->oldestMediaDate != null &&
                $this->sengine->oldestMediaDate < $lapseDate->format('Y-m-d H:i:s')) {

            $lastTenMedia = UserMedia::model()->findAllBySql("select modified from usermedia
                where modified > :lapseDate and user_id=:user_id order by modified desc limit 10", array("lapseDate" => $lapseDate->format('Y-m-d H:i:s'), "user_id" => $user_id));

            if ($lastTenMedia) {
                foreach ($lastTenMedia as $media) {
                    if ($media->modified < $lastModified) {
                        $lastModified = $media->modified;
                    }
                }
                parent::setEngine();
                $this->sengine->mediaCount = count($lastTenMedia);
                parent::saveEngine();
            }

            parent::setEngine();
            $this->sengine->oldestMediaDate = $lastModified;
            parent::saveEngine();
            parent::calculateScore("LEARNING", $user_id);
        }

        // Data elements sent to mixpanel for analytics
        $mixPanelData = array();

        $userPerObj = Userpersonalinfo::model()->find(array('condition' => "user_id=:user_id",
            'params' => array("user_id" => $user_id), 'select' => 'user_id, noofchildren, age'));
        if (!$userPerObj) {
            $userPerObj = new Userpersonalinfo();
            $userPerObj->user_id = $user_id;
            $userPerObj->retirementstatus = 0;
            $userPerObj->retirementage = 65;
            $userPerObj->maritalstatus = 'Single';
            $userPerObj->noofchildren = 0;
            $userPerObj->save();
        }
        if ($userPerObj) {
            $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
            if (preg_match($date_regex, $userPerObj->age)) {
                $mixPanelData['age'] = $this->sengine->userCurrentAge;
            } else {
                $mixPanelData['age'] = "not set";
            }
            $mixPanelData['noofchildren'] = $userPerObj->noofchildren;
        } else {
            $mixPanelData['age'] = "not set";
            $mixPanelData['noofchildren'] = "not set";
        }

        // User has cashedge accounts
        if ($this->sengine->isUserDownloadAccount) {
            $mixPanelData['connectedAccounts'] = "Yes";
        } else {
            $mixPanelData['connectedAccounts'] = "No";
        }

        // User Miscellaneous - Credit Score
        $miscObj = new Misc();
        $misrow = $miscObj->find(array('condition' => "user_id=:user_id", 'params' => array("user_id" => $user_id), 'select' => array('morecreditscore')));

        if ($this->sengine->userIncomePerMonth) {
            $mixPanelData['monthlyIncome'] = number_format((float) $this->sengine->userIncomePerMonth, 2, '.', '');
        }
        if (isset($misrow->morecreditscore)) {
            $mixPanelData['creditScore'] = $misrow->morecreditscore;
        } else {
            $mixPanelData['creditScore'] = '';
        }
        if ($this->sengine->userSumOfAssets) {
            $mixPanelData['totalAssets'] = number_format((float) ($this->sengine->userSumOfAssets + $this->sengine->userSumOfOtherAssets), 2, '.', '');
        } else {
            $mixPanelData['totalAssets'] = '0.00';
        }
        if ($this->sengine->userSumOfDebts) {
            $mixPanelData['totalDebts'] = number_format((float) $this->sengine->userSumOfDebts, 2, '.', '');
        } else {
            $mixPanelData['totalDebts'] = '0.00';
        }
        if ($this->sengine->willOrTrust) {
            $mixPanelData['hasWill'] = $this->sengine->willOrTrust ? 'Yes' : 'No';
        }
        if ($this->sengine->willTrustReviwed) {
            $mixPanelData['willReviewed'] = ($this->sengine->willTrustReviwed == 1 ? 'Yes' : 'No');
        }
        if ($this->sengine->userRiskValue) {
            $mixPanelData['riskValue'] = $this->sengine->userRiskValue;
        }
        if ($this->sengine->wfPoint38) {
            $mixPanelData['profileCompleteness'] = ((int) round($this->sengine->wfPoint38 * 2)) . '%';
        }
        if ($this->sengine->collegeAmount) {
            $mixPanelData['collegeAmount'] = number_format((float) $this->sengine->collegeAmount, 2, '.', '');
        }
        if ($this->sengine->insuranceNeededActionStep) {
            $mixPanelData['lifeInsuranceNeeded'] = number_format((float) $this->sengine->insuranceNeededActionStep, 2, '.', '');
        }
        if ($this->sengine->disainsuranceNeededActionStep) {
            $mixPanelData['disabilityInsuranceNeeded'] = number_format((float) $this->sengine->disainsuranceNeededActionStep, 2, '.', '');
        }

        //  $riskInformationUpdated = false;
        // Calculating Ticker Risk Value For later implementation
        //$this->calculateAndUpdateTickerValue();

        $mixPanelData['currentScore'] = $this->sengine->updateScore();

        $debts = Debts::model()->findAll(array('condition' => "user_id=:user_id AND monthly_payoff_balances=0 AND apr>0 AND status=0",
            'params' => array("user_id" => $user_id), 'select' => 'user_id, balowed, mortgagetype, type, apr'));

        $autoLoanNumerator = 0;
        $mortgageNumerator = 0;
        $ccNumerator = 0;
        $loanNumerator = 0;
        $autoLoanDenominator = 0;
        $mortgageDenominator = 0;
        $ccDenominator = 0;
        $loanDenominator = 0;

        if ($debts) {
            foreach ($debts as $debt) {
                switch ($debt->type) {
                    case "CC":
                        $ccNumerator += $debt->balowed * $debt->apr;
                        $ccDenominator += $debt->balowed;
                        break;
                    case "LOAN":
                        if ($debt->mortgagetype == 37) {
                            $autoLoanNumerator += $debt->balowed * $debt->apr;
                            $autoLoanDenominator += $debt->balowed;
                        } else {
                            $loanNumerator += $debt->balowed * $debt->apr;
                            $loanDenominator += $debt->balowed;
                        }
                        break;
                    case "MORT":
                        $mortgageNumerator += $debt->balowed * $debt->apr;
                        $mortgageDenominator += $debt->balowed;
                        break;
                }
            }
        }

        if ($autoLoanDenominator > 0) {
            $mixPanelData['autoLoanRate'] = number_format($autoLoanNumerator / $autoLoanDenominator, 2, '.', '');
        } else {
            $mixPanelData['autoLoanRate'] = "0.00";
        }

        if ($mortgageDenominator > 0) {
            $mixPanelData['wmortrate'] = number_format($mortgageNumerator / $mortgageDenominator, 2, '.', '');
        } else {
            $mixPanelData['wmortrate'] = "0.00";
        }

        if ($ccDenominator > 0) {
            $mixPanelData['wccrate'] = number_format($ccNumerator / $ccDenominator, 2, '.', '');
        } else {
            $mixPanelData['wccrate'] = "0.00";
        }

        if ($loanDenominator > 0) {
            $mixPanelData['wloanrate'] = number_format($loanNumerator / $loanDenominator, 2, '.', '');
        } else {
            $mixPanelData['wloanrate'] = "0.00";
        }

        $IRAcontribution = Assets::model()->find(array('condition' => "user_id=:user_id AND type='IRA' AND status=0",
            'params' => array("user_id" => $user_id), 'select' => 'SUM(contribution) AS contribution'));

        if ($IRAcontribution) {
            $mixPanelData['IRAcontribution'] = number_format((float) $IRAcontribution->contribution, 2, '.', '');
        } else {
            $mixPanelData['IRAcontribution'] = "0.00";
        }

        $CRcontribution = Assets::model()->find(array('condition' => "user_id=:user_id AND type='CR' AND status=0",
            'params' => array("user_id" => $user_id), 'select' => 'SUM(contribution) AS contribution, SUM(empcontribution) AS empcontribution'));

        if ($CRcontribution) {
            $mixPanelData['CRcontribution'] = number_format((float) $CRcontribution->contribution, 2, '.', '');
            $mixPanelData['CREmpContribution'] = number_format((float) $CRcontribution->empcontribution * $this->sengine->userIncomePerMonth / 100, 2, '.', '');
        } else {
            $mixPanelData['CRcontribution'] = "0.00";
            $mixPanelData['CREmpContribution'] = "0.00";
        }

        return $mixPanelData;
    }

    private function updateProfileScore($uid) {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $userDetails = User::model()->findByPk($uid);
        $userPerDetails = Userpersonalinfo::model()->find("user_id=:user_id", array("user_id" => $uid));
        if (!$userPerDetails) {
            $userPerDetails = new Userpersonalinfo();
            $userPerDetails->retirementage = 65;
            $userPerDetails->retirementstatus = "";
            $userPerDetails->maritalstatus = "";
        }

        parent::setEngine();

        //about you wfpoint38 calculation
        $count = 0;
        $count1 = 0;
        $profileScore = array(); // it is used to show the values in json data only
        if ($userDetails->firstname != "" && $userDetails->lastname != "") {
            $count++;
        }
        $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
        if (preg_match($date_regex, $userPerDetails->age)) {
            $count1++;
        }
        if ($userDetails->zip > 0) {
            $count++;
        }
        if ($userPerDetails->maritalstatus != "") {
            $count++;
        }
        if ($userPerDetails->noofchildren >= 0) {
            $count++;
        }
        if ($userPerDetails->noofchildren > 0) {
            $childrensAge = explode(",", $userPerDetails->childrensage);
            if (preg_match($date_regex, $childrensAge[0])) {
                $count++;
            }
        } else {
            $count++;
        }
        if ($userPerDetails->retirementstatus != "") {
            $count++;
        }
        $this->sengine->userProfilePoints_aboutyou = ($count * 1.25 + $count1 * 2.50);

        if (!isset($userPerDetails->retirementage) || intval($userPerDetails->retirementage) <= 0) {
            $userPerDetails->retirementage = 65;
        }
        $retirementage = intval($userPerDetails->retirementage);

        $ratesDependency = new CDbCacheDependency('SELECT * FROM sustainablerates');
        $rates = Sustainablerates::model()->cache(QUERY_CACHE_TIMEOUT, $ratesDependency)->find('age = ' . $retirementage);
        $sustainablewithdrawalpercent = isset($rates->sustainablewithdrawalpercent) ? $rates->sustainablewithdrawalpercent : 4;
        $this->sengine->sustainablewithdrawalpercent = $sustainablewithdrawalpercent;

        //If the user is in retirement, retirement age = current age.
        if ($this->sengine->userCurrentAge < $retirementage) {
            $this->sengine->userRetirementAge = $retirementage;
        } else {
            $this->sengine->userRetirementAge = $this->sengine->userCurrentAge;
        }
        $this->sengine->yearToRetire = $this->sengine->userRetirementAge - $this->sengine->userCurrentAge;

        if ($this->sengine->spouseAge > 0) {
            $this->sengine->spouseRetAge = $this->sengine->yearToRetire + $this->sengine->spouseAge;
        } else {
            $this->sengine->spouseRetAge = 0;
        }

        //connecting account//
        $aaDet = Assets::model()->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $uid), 'select' => 'balance'));
        $adDet = Debts::model()->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $uid), 'select' => 'balowed'));
        $aiDet = Insurance::model()->findAll(array('condition' => "user_id = :user_id AND context = 'AUTO' AND status=0", 'params' => array("user_id" => $uid), 'select' => 'annualpremium'));

        if ((!empty($adDet) || !empty($aaDet) || !empty($aiDet)) || $userPerDetails["connectAccountPreference"]) {
            $this->sengine->userProfilePoints_connectAccount = 1;
        } else {
            $this->sengine->userProfilePoints_connectAccount = 0;
        }
        //income
        $income = Income::model()->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid), 'select' => 'totaluserincome'));
        if ($income && $income->totaluserincome > 0) {
            $this->sengine->userProfilePoints_income = 1;
        } else {
            $this->sengine->userProfilePoints_income = 0;
        }
        //expenses
        $expense = Expense::model()->find(array('condition' => "user_id = :user_id", 'params' => array("user_id" => $uid), 'select' => 'actualexpense'));
        if ($expense && $expense->actualexpense > 0) {
            $this->sengine->userProfilePoints_expense = 1;
        } else {
            $this->sengine->userProfilePoints_expense = 0;
        }
        //debts
        $debts = Debts::model()->findAll("user_id=:user_id and status=0", array("user_id" => $uid));
        if ($debts || $userPerDetails["debtsPreference"] == "1") {
            $this->sengine->userProfilePoints_debts = 1;
        } else {
            $this->sengine->userProfilePoints_debts = 0;
        }
        //assets
        $assets = Assets::model()->findAll("user_id=:user_id and status=0", array("user_id" => $uid));
        if ($assets) {
            $this->sengine->userProfilePoints_assets = 10;
        } else {
            $this->sengine->userProfilePoints_assets = 0;
        }
        //insurance
        $insurance = Insurance::model()->findAll("user_id=:user_id and status=0", array("user_id" => $uid));
        if ($insurance || $userPerDetails["insurancePreference"] == "1") {
            $this->sengine->userProfilePoints_insurance = 1;
        } else {
            $this->sengine->userProfilePoints_insurance = 0;
        }
        //risk tolerance
        if ($userPerDetails->risk != "") {
            $this->sengine->userProfilePoints_userRisk = 1;
        } else {
            $this->sengine->userProfilePoints_userRisk = 0;
        }

        //Miscellaneous
        $misc_prof = Miscellaneous::model()->find("user_id=:user_id", array("user_id" => $uid));
        //for profile completeness points//
        $cnt1 = 0; // for taxes 1.5
        $cnt2 = 0; // for estate planning 1.25
        $cnt3 = 0; // for more 1.5
        $cnt4 = 0; // for more 1.0
        //taxes//
        if ($misc_prof) {
            if ($misc_prof->taxpay != "") {
                $cnt1++;
            }
            if ($misc_prof->taxbracket != "" && $misc_prof->taxbracket != "4") {
                $cnt1++;
            }
            if ($misc_prof->taxvalue != "" && $misc_prof->taxvalue != "3") {
                $cnt1++;
            }
            if ($misc_prof->taxcontri != "") {
                $cnt1++;
            }
            if ($misc_prof->taxStdOrItemDed != "") {
                $cnt1++;
            }
            //estate planning
            if ($misc_prof->misctrust != "") {
                $cnt2++;
            }
            if ($misc_prof->miscreviewmonth != "" && $misc_prof->miscreviewyear != "Year") {
                $cnt2++;
            }
            if ($misc_prof->mischiddenasset != "") {
                $cnt2 = ($misc_prof->mischiddenasset == '0') ? $cnt2 + 2 : $cnt2 + 1;
            }
            if ($misc_prof->miscrightperson != "") {
                $cnt2++;
            }
            if ($misc_prof->miscliquid != "") {
                $cnt2++;
            }
            if ($misc_prof->miscspouse != "") {
                $cnt2++;
            }

            //more//
            if ($misc_prof->moremoney != "") {
                $cnt3++;
            }
            if ($misc_prof->moreinvrebal != "") {
                $cnt4++;
            }
            if ($misc_prof->moreautoinvest != "") {
                $cnt4++;
            }
            if ($misc_prof->moreliquidasset != "") {
                $cnt4++;
            }
            if ($misc_prof->morecharity != "") {
                $cnt4++;
            }
            if ($misc_prof->morecreditscore != "") {
                $cnt4++;
            }
            if ($misc_prof->morereviewmonth != "" && $misc_prof->morescorereviewyear != 'Year') {
                $cnt4++;
            }
        }
        $this->sengine->userProfilePoints_misc = ($cnt1 * 1.5 + $cnt2 * 1.25 + $cnt3 * 1.5 + $cnt4 * 1);

        //goal
        $goals = Goal::model()->findAll("user_id=:user_id and goalstatus=1 AND goalname!='Retirement Goal'", array("user_id" => $uid));
        if ($goals) {
            $this->sengine->userProfilePoints_goals = 1;
        } else {
            $this->sengine->userProfilePoints_goals = 0;
        }

        parent::saveEngine();
        $sections = "";
        parent::setupDefaultRetirementGoal();
        parent::calculateScore("GOAL|ASSET|INSURANCE|PROFILE|COMPLETENESS", $uid);

        $total_profile_score = array_sum($profileScore);
        return $total_profile_score;
//        $this->sendResponse(200, CJSON::encode(array("status" => 'Ok')));
    }

    /*
     *  If yes for Risk => You have to get the updated risk row from Risk
     * table and update userGrowthRate, riskStdDev, riskMetric in the score
     * engine and calculate CalculateScore("RISK"). This is similar to
     * whats done in Risk Controller on update.
     */

    private function updateRiskInformation() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $id = Yii::app()->getSession()->get('wsuser')->id;
        $profile = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $id));

        if ($profile && $profile->risk > 0) {
            $riskObj = Risk::model()->find("risk = :risk", array(':risk' => $profile->risk));
            if ($riskObj) {
                $this->sengine->userRiskValue = $profile->risk;
                $this->sengine->userGrowthRate = $riskObj->returnrate;
                $this->sengine->riskStdDev = $riskObj->stddev;
                $this->sengine->riskMetric = $riskObj->metric;
                return true;
            }
        }

        $this->sengine->userRiskValue = 0;
        $this->sengine->userGrowthRate = 7.0;
        $this->sengine->riskStdDev = 8.7;
        $this->sengine->riskMetric = 0.43;
        return true;
    }


    private function updatePriorityInformation() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $id = Yii::app()->getSession()->get('wsuser')->id;

        $assets = Assets::model()->findAll("user_id=:user_id", array("user_id" => $id));
        $count = 1;
        foreach($assets as $asset) {
            if($asset->priority == 0) {
                $asset->priority = $count;
                $asset->save();
            }
            $count++;
        }

        $count = 1;
        $debts = Debts::model()->findAll("user_id=:user_id", array("user_id" => $id));
        foreach($debts as $debt) {
            if($debt->priority == 0) {
                $debt->priority = $count;
                $debt->save();
            }
            $count++;
        }

        $count = 1;
        $insurances = Insurance::model()->findAll("user_id=:user_id", array("user_id" => $id));
        foreach($insurances as $insurance) {
            if($insurance->priority == 0) {
                $insurance->priority = $count;
                $insurance->save();
            }
            $count++;
        }
    }
    /*
     *  If yes for LifeExpectancy => Get the User's age and spouse's age,
     * and compare against life expectancy table to update lifeEC, spouseLifeEC
     * in score engine. Make sure to imitate how its done in
     * UserController on about you update. You should call
     * CalculateScore("PROFILE") at the end of this.
     */

    private function updateLEInformation() {
        if (!isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->sendResponse(200, CJSON::encode(array("status" => "ERROR", "message" => "Session Expired")));
        }
        $user_id = Yii::app()->getSession()->get('wsuser')->id;
        $userPerDetails = Userpersonalinfo::model()->find("user_id = :user_id", array(':user_id' => $user_id));

        if ($userPerDetails) {
            // Comparing the Life Expectancy of user
            $ageSplit = explode("-", $userPerDetails->age);

            if ($userPerDetails->age != "" && $ageSplit[0] != "0000") {

                // If user gives us an age, then we look for life expectancy
                // Life expectancy values are based on users' ages in 2010
                $lifeexpAge = 2010 - $ageSplit[0];
                if ($lifeexpAge < 0)
                    $lifeexpAge = 0;

                $lifeExpdependency = new CDbCacheDependency('SELECT FLifeExpectancy, baseyearage FROM lifeexpectancy');
                $lifeexp = Lifeexpectancy::model()->cache(QUERY_CACHE_TIMEOUT, $lifeExpdependency)->find('baseyearage = ' . $lifeexpAge);
            }

            // Comparing the life expectancy of users spouse.
            $ageSplit = explode("-", $userPerDetails->spouseage);

            if ($userPerDetails->spouseage != "") {
                // X--, -X-, --X, X-X, -XX, XX-, --- <= Partial age dates get stored as 0000-00-00 in DB
                if (($userPerDetails->maritalstatus == 'Married' || $userPerDetails->maritalstatus == 'Domestic Union') && $ageSplit[0] != "0000") {
                    // If user gives us an age, then we look for life expectancy
                    // Life expectancy values are based on users' ages in 2010
                    $lifeexpAge = 2010 - $ageSplit[0];
                    if ($lifeexpAge < 0)
                        $lifeexpAge = 0;

                    $spouseLifeExpdependency = new CDbCacheDependency('SELECT FLifeExpectancy, baseyearage FROM lifeexpectancy');
                    $spouselifeexp = Lifeexpectancy::model()->cache(QUERY_CACHE_TIMEOUT, $spouseLifeExpdependency)->find('baseyearage = ' . $lifeexpAge);
                }
                else if ($userPerDetails->maritalstatus == 'Married' || $userPerDetails->maritalstatus == 'Domestic Union') {
                    if (isset($lifeexp)) {
                        $spouselifeexp = $lifeexp;
                    }
                }
            }
        }

        // Life Expectancy check
        if (isset($lifeexp)) {
            $this->sengine->lifeEC = $lifeexp->FLifeExpectancy;
        } else {
            $this->sengine->lifeEC = 82;
        }

        // Spouse Life Expectancy check
        if (isset($spouselifeexp)) {
            $this->sengine->spouseLifeEC = $spouselifeexp->FLifeExpectancy;
        } else {
            $this->sengine->spouseLifeEC = 82;
        }

        return true;
    }

    private function getMonthDifference($endDate) {
        $today = new DateTime();
        $end_date = new DateTime($endDate);
        $interval = date_diff($today, $end_date);
        $month_difference = $interval->m + ($interval->y * 12);
        if ($interval->d > 0) {
            $month_difference += 1;
        }

        return $month_difference;
    }

    private function getYearMonthDifference($endDate) {
        $today = new DateTime();
        $end_date = new DateTime($endDate);
        $interval = date_diff($today, $end_date);
        return $interval;
    }

    private function getDiffBetweenDates($startDate, $endDate) {
        $interval = date_diff($startDate, $endDate);
        return $interval;
    }

    function restructDebt($debtArray, $payments = null, $maxmonths = null, $oldIncrease = null, $sign = null) {
        $maxPayment = 0;
        $minPayment = 0;
        $totalDebt = 0;
        $paymentArray = array();

        for ($i = 0; $i < count($debtArray); $i++) {
            $debt = $debtArray[$i];
            $minimumPayment = $debt['minimum'];
            $monthlyPayment = $debt['payment'];
            $totalDebt += $debt['balance'];
            $paymentArray[] = $monthlyPayment;

            if ($minimumPayment > $monthlyPayment) {
                $maxPayment += $minimumPayment;
            } else {
                $maxPayment += $monthlyPayment;
            }
            $minPayment += $minimumPayment;
        }

        if ($payments != null) {
            if ($maxPayment < $minPayment) {
                $maxPayment = $minPayment;
            }
            if ($payments < $maxPayment) {
                $payments = $maxPayment;
            }
        }

        $resultDebtArray = array();
        $debtPaidOff = false;
        $totalInterest = 0;
        $months = 0;
        $extraRollover = 0;
        while (!$debtPaidOff) {
            $currentDebtPaidOff = true;
            $index = 0;
            $rolloverPayment = $payments - $minPayment + $extraRollover;
            $rolloverAdded = false;

            for ($i = 0; $i < count($debtArray); $i++) {
                $debt = $debtArray[$i];
                $monthlyRate = $debt['rate'] / 12;
                $amount = $debt['balance'];
                $monthlyPayment = $debt['minimum'];

                if (!isset($resultDebtArray[$index])) {
                    // Setting Default Value
                    $resultDebtArray[$index] = array();
                    $resultDebtArray[$index]['amount'] = $amount;
                } else if ($resultDebtArray[$index]['amount'] > 0) {
                    if (!$rolloverAdded) {
                        $monthlyPayment += $rolloverPayment;
                    }
                    // Calculating new amount and interest
                    $previousAmount = $resultDebtArray[$index]['amount'];
                    $previousInterest = $resultDebtArray[$index]['interest'];
                    $resultDebtArray[$index]['amount'] = ($previousAmount + $previousInterest - $monthlyPayment);
                    if ($resultDebtArray[$index]['amount'] < 0) {
                        $rolloverPayment = abs($resultDebtArray[$index]['amount']);
                        $extraRollover += $debt['minimum'];
                        $monthlyPayment -= abs($resultDebtArray[$index]['amount']);
                    } else {
                        $rolloverAdded = true;
                    }
                }
                if ($months == 1) {
                    $paymentArray[$index] = $monthlyPayment;
                }
                $resultDebtArray[$index]['interest'] = $resultDebtArray[$index]['amount'] * $monthlyRate;
                if ($resultDebtArray[$index]['interest'] > 0) {
                    $totalInterest += $resultDebtArray[$index]['interest'];
                    $currentDebtPaidOff = false;
                }
                $index++;
            }

            $debtPaidOff = $currentDebtPaidOff;
            if ($months > 1001 || ($maxmonths != '' && $maxmonths < $months)) {
                break;
            }
            $months++;
        }
        $months--;
        if ($months < 0) {
            $months = 0;
        }

        if ($maxmonths == '') {
            return array("months" => $months, "interest" => $totalInterest, "firstPayment" => $paymentArray);
        } else {
            if (count($debtArray) == 0 || $totalDebt <= 0) {
                return array("payments" => 0, "interest" => 0, "increasePayment" => 0, "sign" => $sign, "firstPayment" => $paymentArray, "months" => 0);
            }

            $newtotalDebt = 0;
            for ($i = 0; $i < count($resultDebtArray); $i++) {
                $debt = $resultDebtArray[$i];
                $newtotalDebt += $debt["amount"];
            }
            $increasePayment = $oldIncrease;
            if ($newtotalDebt < 0) {
                if ($sign == "+") {
                    $sign = "-";
                    $increasePayment = 0 - $increasePayment / 10;
                }
            } else {
                if ($sign == "-") {
                    $sign = "+";
                    $increasePayment = 0 - $increasePayment / 10;
                }
            }

            $newPayment = $payments + $increasePayment;
            if ($newPayment < $maxPayment) {
                $newPayment = $maxPayment;
            }
            return array('payments' => $newPayment, 'interest' => $totalInterest, 'increasePayment' => $increasePayment, 'sign' => $sign, "firstPayment" => $paymentArray, "months" => $months);
        }
    }

    function CheckDebtGoals($goal, $debts) {
        $resultArray = array();
        $calcPayments = array("needsActionStep" => false);
        $goalName = $goal->goalname;
        if (!isset($goal->goalname) || $goal->goalname == '') {
            $goalName = 'Pay Off Debt';
        }
        $goalName = '<a href="#" id="' . $goal->id . 'addGoals" class="addGoals actionStep">' . $goalName . '</a>';
        $debtArray = array();
        $selectedDebts = array();
        $totalDebt = 0;
        $currentPayment = 0;
        if (!isset($goal->payoffdebts) || empty($goal->payoffdebts) || $goal->payoffdebts == "") {
            foreach ($debts as $debt) {
                $selectedDebts[] = $debt->id;
            }
        } else {
            $selectedDebts = explode(",", $goal->payoffdebts);
        }
        foreach ($debts as $debt) {
            if (in_array($debt->id, $selectedDebts)) {
                $currentDebt = array();
                $currentDebt['id'] = $debt->id;
                $currentDebt['name'] = $debt->name;
                if (!isset($debt->name) || $debt->name == '') {
                    $currentDebt['name'] = $debt->getDefaultName($debt->type);
                }
                $currentDebt['type'] = $debt->type;
                // Accumalating the balance value for comparision.
                $currentDebt['balance'] = $debt->balowed * 1;
                $totalDebt += $debt->balowed * 1;
                // Accumalating the permonth value for comparision.
                $currentDebt['payment'] = $debt->amtpermonth * 1;
                $currentPayment += $debt->amtpermonth * 1;

                if ($currentDebt['payment'] == "") {
                    $currentDebt['payment'] = $currentDebt['balance'] * ($goal->goalassumptions_1 / 100);
                }
                $currentDebt['minimum'] = ($currentDebt['type'] == 'CC') ? ($currentDebt['balance'] * ($goal->goalassumptions_1 / 100)) : $currentDebt['payment'];
                $currentDebt['rate'] = $debt->apr / 100;
                if ($currentDebt['rate'] == "") {
                    $currentDebt['rate'] = 0.1;
                }
                $debtArray[] = $currentDebt;
            }
        }
        $monthlyPayments = round($goal->permonth);
        $months = $this->getMonthDifference($goal->goalenddate);
        if ($months < 0) {
            $months = 0;
        }
        $interest = 0;

        if ($monthlyPayments > 0) {
            $resultArray = $this->restructDebt($debtArray, $monthlyPayments);
            if ($resultArray["months"] < 1001) {
                $months = $resultArray["months"];
                $interest = round($resultArray["interest"]);
            } else {
                $months = -1;
                $interest = -1;
            }
        } else {
            if ($months == 0) {
                $monthlyPayments = $totalDebt;
            } else {
                $payments = 10000;
                $oldpayments = 0;
                $increasePayment = 10000;
                $resultArray = array("payment" => 0, "interest" => 0);
                $sign = '+';
                $i = 0;
                while (abs($payments - $oldpayments) >= 1) {
                    $oldpayments = $payments;
                    $resultArray = $this->restructDebt($debtArray, $payments, $months, $increasePayment, $sign);
                    $payments = $resultArray["payments"];
                    $increasePayment = $resultArray["increasePayment"];
                    $sign = $resultArray["sign"];
                    $i++;
                }
                $months = $resultArray["months"];
                $monthlyPayments = round($payments);
                $interest = round($resultArray["interest"]);
            }
        }

        $index = 0;
        $title = "";
        $debtInfo = "";
        $paymentInfo = "";
        foreach ($debtArray as $debt) {
            if (strlen($paymentInfo) > 0) {
                $paymentInfo .= ",";
            }
            $paymentInfo .= $debt["id"] . "|" . $resultArray['firstPayment'][$index];
            if (round($debt["payment"]) != round($resultArray["firstPayment"][$index])) {
                $calcPayments["needsActionStep"] = true;
                $debtName = '<a href="#" id="' . $debt["id"] . 'addDebts" class="addDebts actionStep">' . $debt['name'] . '</a>';
                if (round($debt["payment"]) > round($resultArray["firstPayment"][$index])) {
                    $debtInfo .= "Reduce monthly payments to $" . number_format($resultArray['firstPayment'][$index]) . " for " . $debtName . " ($" . number_format($debt['balance']) . ").<br>";
                } else {
                    $debtInfo .= "Increase monthly payments to $" . number_format($resultArray['firstPayment'][$index]) . " for " . $debtName . " ($" . number_format($debt['balance']) . ").<br>";
                }
            }
            $index++;
        }
        if ($calcPayments["needsActionStep"]) {
            $years = floor($months / 12);
            $months = $months % 12;
            $duration = "";
            if ($years > 0) {
                $duration = $years . " years";
            }
            if ($months > 0) {
                if (strlen($duration) > 0) {
                    $duration .= " and ";
                }
                $duration .= $months . " months";
            }

            $title = "To achieve your goal of " . $goalName . ", we recommend you pay the following amounts for these debts:<br>";
            $title .= "<br>" . $debtInfo . "<br>";

            $title .= "If you follow our plan, you'll be out of debt in " . $duration;
            $title .= ". Once your first debt is paid off, we'll update our advice with a new payment plan.<br>";
            $title .= "<input id='paymentInfo' type='hidden' value='" . $paymentInfo . "'>";
            $calcPayments['title'] = $title;
        }
        return $calcPayments;
    }

    function checkGoals($goals, $assets, $currentAge, $growth, $personal, $grossIncome, $user_id) {
        // Calculate Contribution Amounts
        $crContribution = 0;
        $empCrContribution = 0;
        $iraContribution = 0;
        $educContribution = 0;
        $brokContribution = 0;
        $bankContribution = 0;
        // Balances
        $crBalance = 0;
        $bankBalance = 0;
        $iraBalance = 0;
        $educBalance = 0;
        $brokBalance = 0;
        // CR Found
        $crFound = false;
        foreach ($assets as $asset) {
            switch ($asset->type) {
                case "BANK":
                    $bankBalance = $bankBalance + $asset->balance;
                    $bankContribution = $bankContribution + $asset->contribution;
                    break;
                case "CR":
                    $crBalance = $crBalance + $asset->balance;
                    $crContribution = $crContribution + $asset->contribution;
                    $empCrContribution = $empCrContribution + ($asset->empcontribution / 100) * $grossIncome;
                    $crFound = true;
                    break;
                case "IRA":
                    $iraBalance = $iraBalance + $asset->balance;
                    $iraContribution = $iraContribution + $asset->contribution;
                    break;
                case "EDUC":
                    $educBalance = $educBalance + $asset->balance;
                    $educContribution = $educContribution + $asset->contribution;
                    break;
                case "BROK":
                    $brokBalance = $brokBalance + $asset->balance;
                    $brokContribution = $brokContribution + $asset->contribution;
                    break;
            }
        }

        $assetBalances = array("bank" => round($bankBalance), "cr" => round($crBalance), "ira" => round($iraBalance), "educ" => round($educBalance), "brok" => round($brokBalance));

        // Clean up maximum contribution amounts for CR / IRA
        $bankContribution = round($bankContribution);
        $crContribution = round($crContribution);
        $empCrContribution = round($empCrContribution);
        $iraContribution = round($iraContribution);
        $educContribution = round($educContribution);
        $brokContribution = round($brokContribution);
        $extraEducContribution = 0;
        $extraBankContribution = 0;
        $extraBrokContribution = 0;
        $extraCrContribution = 0;
        $extraIraContribution = 0;
        if ($currentAge <= 49) {
            if ($crContribution * 12 > $this->crMax) {
                $crContribution = round($this->crMax / 12);
            }
            if ($iraContribution * 12 > $this->iraMax) {
                $iraContribution = round($this->iraMax / 12);
            }
            $extraCrContribution = round($this->crMax / 12) - $crContribution;
            $extraIraContribution = round($this->iraMax / 12) - $iraContribution;
        } else if ($currentAge >= 50) {
            if ($crContribution * 12 > $this->over50CrMax) {
                $crContribution = round($this->over50CrMax / 12);
            }
            if ($iraContribution * 12 > $this->over50IraMax) {
                $iraContribution = round($this->over50IraMax / 12);
            }
            $extraCrContribution = round($this->over50CrMax / 12) - $crContribution;
            $extraIraContribution = round($this->over50IraMax / 12) - $iraContribution;
        }
        $assetContributions = array("bank" => $bankContribution, "cr" => $crContribution, "ira" => $iraContribution, "brok" => $brokContribution, "educ" => $educContribution, "crEmp" => $empCrContribution, "extraCr" => $extraCrContribution, "extraIra" => $extraIraContribution);

        $goalContributions = 0;
        foreach ($goals as $goal) {
            if ($goal->goaltype == 'HOUSE' || $goal->goaltype == 'CUSTOM') {
                $goalContributions = $goalContributions + $goal->permonth;
            }
        }
        $goalContributions = round($goalContributions);

        $goals = $this->RecalculateGoalAmounts($goals, $assets, $personal, $assetContributions, $assetBalances, $goalContributions, $growth, $grossIncome);

        // Update specific amounts
        $newgoals = Goal::model()->findAll(array('condition' => "user_id = :user_id AND goalstatus=1 and goaltype <> 'DEBT' ORDER by goalpriority", 'params' => array("user_id" => $user_id), 'select' => 'id,contributions, minimumContributions, saved,status'));
        foreach ($newgoals as $newgoal) {
            foreach ($goals as $goal) {
                if ($goal->id == $newgoal->id) {
                    if (round($goal->contributions) != round($newgoal->contributions) || round($goal->minimumContributions) != round($newgoal->minimumContributions) || round($goal->saved) != round($newgoal->saved) || $goal->status != $newgoal->status) {
                        $newgoal->contributions = $goal->contributions;
                        $newgoal->minimumContributions = $goal->minimumContributions;
                        $newgoal->saved = $goal->saved;
                        $newgoal->status = $goal->status;
                        $newgoal->save();
                    }
                    break;
                }
            }
        }

        $resultArray = array("needsActionStep" => false);
        $goalName = "";
        $contributionInfo = array();
        $assetInfo = "";
        $months = 0;
        foreach ($goals as $goal) {
            $assetInfo = "";
            $goalName = "";
            $contributionInfo = array();
            $resultArray["goaltype"] = $goal->goaltype;
            $resultArray["goalid"] = $goal->id;
            $resultArray["goalpriority"] = $goal->goalpriority;
            if (!isset($goal->goalname) || $goal->goalname == '') {
                $goalName = $goal->getDefaultName($goal->goaltype);
            } else {
                $goalName = $goal->goalname;
            }
            $goalName = '<a href="#" id="' . $goal->id . 'addGoals" class="addGoals actionStep">' . $goalName . '</a>';

            $contributionNeeded = round($goal->minimumContributions) - round($goal->contributions);
            if ($goal->goaltype == 'RETIREMENT') {
                // Retirement Contributions
                if ($contributionNeeded <= 0) {
                    continue;
                }
                $contribution = 0;
                $contributionNeeded = $contributionNeeded - $extraCrContribution;
                if ($contributionNeeded <= 0) {
                    $contribution = $contributionNeeded + $extraCrContribution;
                } else {
                    $contribution = $extraCrContribution;
                }
                $assetFound = false;
                if ($contribution > 0) {
                    foreach ($assets as $asset) {
                        if ($asset->type == 'CR') {
                            $assetName = "";
                            if (!isset($asset->name) || $asset->name == '') {
                                $assetName = $asset->getDefaultName($asset->type);
                            } else {
                                $assetName = $asset->name;
                            }
                            $assetName = '<a href="#" id="' . $asset->id . 'addAssets" class="addAssets actionStep">' . $assetName . '</a>';
                            $assetInfo .= "Increase how much you contribute monthly to $" . number_format($contribution + $asset->contribution) . " for " . $assetName . " ($" . number_format($asset->balance) . ").<br>";
                            $contributionInfo[] = $asset->id . "|" . round($contribution + $asset->contribution);
                            $resultArray["needsActionStep"] = true;
                            $assetFound = true;
                            break;
                        }
                    }
                    if (!$assetFound) {
                        $contributionNeeded = $contributionNeeded + $extraCrContribution;
                    }
                }
                if ($contributionNeeded <= 0) {
                    break;
                }
                $contribution = 0;
                $contributionNeeded = $contributionNeeded - $extraIraContribution;
                if ($contributionNeeded <= 0) {
                    $contribution = $contributionNeeded + $extraIraContribution;
                } else {
                    $contribution = $extraIraContribution;
                }
                $assetFound = false;
                if ($contribution > 0) {
                    foreach ($assets as $asset) {
                        if (($asset->type == 'IRA' && $asset->assettype <> 51 && !$crFound && $grossIncome * 12 >= 160000) || ($asset->type == 'IRA' && $asset->assettype == 51 && $grossIncome * 12 < 160000)) {
                            $assetName = "";
                            if (!isset($asset->name) || $asset->name == '') {
                                $assetName = $asset->getDefaultName($asset->type);
                            } else {
                                $assetName = $asset->name;
                            }
                            $assetName = '<a href="#" id="' . $asset->id . 'addAssets" class="addAssets actionStep">' . $assetName . '</a>';
                            $assetInfo .= "Increase how much you contribute monthly to $" . number_format($contribution + $asset->contribution) . " for " . $assetName . " ($" . number_format($asset->balance) . ").<br>";
                            $contributionInfo[] = $asset->id . "|" . round($contribution + $asset->contribution);
                            $resultArray["needsActionStep"] = true;
                            $assetFound = true;
                            break;
                        }
                    }
                    if (!$assetFound) {
                        $contributionNeeded = $contributionNeeded + $extraIraContribution;
                    }
                }
                if ($contributionNeeded <= 0) {
                    break;
                }
            } else if ($goal->goaltype == 'COLLEGE') {
                // Calculate College Contributions
                if ($contributionNeeded <= 0) {
                    continue;
                }
                $contribution = $contributionNeeded;
                $contributionNeeded = 0;
                $assetFound = false;
                if ($contribution > 0) {
                    foreach ($assets as $asset) {
                        if ($asset->type == 'EDUC') {
                            $assetName = "";
                            if (!isset($asset->name) || $asset->name == '') {
                                $assetName = $asset->getDefaultName($asset->type);
                            } else {
                                $assetName = $asset->name;
                            }
                            $assetName = '<a href="#" id="' . $asset->id . 'addAssets" class="addAssets actionStep">' . $assetName . '</a>';
                            $assetInfo .= "Increase how much you contribute monthly to $" . number_format($contribution + $asset->contribution) . " for " . $assetName . " ($" . number_format($asset->balance) . ").<br>";
                            $contributionInfo[] = $asset->id . "|" . round($contribution + $asset->contribution);
                            $resultArray["needsActionStep"] = true;
                            $assetFound = true;
                            break;
                        }
                    }
                    if (!$assetFound) {
                        $contributionNeeded = $contribution;
                    }
                }
                if ($contributionNeeded <= 0) {
                    break;
                }
            }

            if ($contributionNeeded <= 0) {
                if ($resultArray["needsActionStep"]) {
                    break;
                } else {
                    continue;
                }
            }

            $contribution = $contributionNeeded;
            $contributionNeeded = 0;
            $assetFound = false;
            if ($contribution > 0) {
                foreach ($assets as $asset) {
                    if ($asset->type == 'BROK' || $asset->type == 'BANK') {
                        $assetName = "";
                        if (!isset($asset->name) || $asset->name == '') {
                            $assetName = $asset->getDefaultName($asset->type);
                        } else {
                            $assetName = $asset->name;
                        }
                        $assetName = '<a href="#" id="' . $asset->id . 'addAssets" class="addAssets actionStep">' . $assetName . '</a>';
                        $assetInfo .= "Increase how much you contribute monthly to $" . number_format($contribution + $asset->contribution) . " for " . $assetName . " ($" . number_format($asset->balance) . ").<br>";
                        $contributionInfo[] = $asset->id . "|" . round($contribution + $asset->contribution);
                        $resultArray["needsActionStep"] = true;
                        $assetFound = true;
                        break;
                    }
                }
                if (!$assetFound) {
                    $contributionNeeded = $contribution;
                    $resultArray["needsSavingsStep"] = true;
                }
            }
            if ($contributionNeeded <= 0) {
                break;
            }
            if ($resultArray["needsActionStep"]) {
                break;
            }
        }
        // Raising the Action Step.
        if ($resultArray["needsActionStep"]) {
            $asset = new Goal();
            $title = "You’ll need to increase your savings amount if you want to meet your " . $asset->getDefaultName($resultArray["goaltype"]) . " Goal. Let’s set you up with a plan that’s easy to manage and gives some room to breathe. To achieve your goal of " . $goalName . ", we recommend you increase the amount of your monthly savings for these assets:<br>";
            $title .= "<br>" . $assetInfo . "<br>";
            $contributionInfo = implode(',', $contributionInfo);
            $title .= "<input id='contributionInfo' type='hidden' value='" . $contributionInfo . "'>";
            $resultArray['title'] = $title;
        }
        return $resultArray;
    }

    private function calculateMonthlyContribution($goal, $growth = 7, $personal) {
        $contributionCheck = false;
        $inflation = 3.4;
        if ($goal->goaltype == 'COLLEGE') {
            $inflation = 5.8;
        }
        if ($goal->goaltype == 'RETIREMENT') {
            $retirementage = 65;
            if (isset($personal->retirementage) && intval($personal->retirementage) > 0) {
                $retirementage = intval($personal->retirementage);
            }
            $age = '0000-00-00';
            if (isset($personal->age)) {
                $age = $personal->age;
            }
            $ageArray = explode('-', $age);
            if ($ageArray[0] == "0000") {
                $ageArray[0] = date('Y') - 30;
            }
            if ($ageArray[1] == "00") {
                $ageArray[1] = "01";
            }
            if ($ageArray[2] == "00") {
                $ageArray[2] = "01";
            }
            $ageArray[0] = intval($ageArray[0]) + $retirementage;
            $goal->goalenddate = implode('-', $ageArray);

            $today = new DateTime();
            $endDate = new DateTime($goal->goalenddate);
            if ($today > $endDate) {
                $goal->goalenddate = date('Y') . "-" . date('m') . "-" . date('d');
            }
        }
        if ($goal->permonth > 0 && $goal->goaltype != 'RETIREMENT' && $goal->goaltype != 'COLLEGE') {
            $contributionCheck = true;
        }
        $savedDollars = $goal->saved;
        $monthlyContributions = 0;
        $months = 0;

        if (!$contributionCheck) {
            $months = $this->getMonthDifference($goal->goalenddate);
            if ($months < 0) {
                $months = 0;
            }

            $totalFuture = $goal->goalamount * pow(1 + $inflation / 1200, $months);
            $amountNeeded = $totalFuture - $savedDollars;
            $checkAmount = (($amountNeeded / 1000000) > 1) ? ($amountNeeded / 1000000) : 1;

            if ($months == 0) {
                $monthlyContributions = $amountNeeded;
            }

            $increment = 1;
            while ($amountNeeded > $increment * 100) {
                $increment *= 10;
            }
            $sign = '+';
            while (abs($amountNeeded) >= $checkAmount && $totalFuture > $savedDollars && $months > 0) {
                for ($i = 1; $i <= $months; $i++) {
                    $savedDollars = $savedDollars * (1 + $growth / 1200) + $monthlyContributions;
                }
                $amountNeeded = $totalFuture - $savedDollars;
                if (abs($amountNeeded) >= $checkAmount) {
                    if ($amountNeeded > 0 && $sign == '-') {
                        $sign = '+';
                        $increment = 0 - $increment / 10;
                    }
                    if ($amountNeeded < 0 && $sign == '+') {
                        $sign = '-';
                        $increment = 0 - $increment / 10;
                    }
                    $monthlyContributions += $increment;
                }
                $savedDollars = $goal->saved;
            }
            if ($monthlyContributions < 0) {
                $monthlyContributions = 0;
            }
        } else {
            $monthlyContributions = $goal->permonth;
            $totalFuture = $goal->goalamount;
            while ($totalFuture > $savedDollars && $months <= 1000) {
                $savedDollars = $savedDollars * (1 + $growth / 1200) + $monthlyContributions;
                $totalFuture = $totalFuture * (1 + $inflation / 1200);
                $months++;
            }

            if ($months > 1001) {
                $months = -1;
            }
        }
        return array('contribution' => round($monthlyContributions), 'months' => $months);
    }

    private function fillMiscEstatePlanningStepNeeded($mrow, $sengineObj) {
        $resultArray = array("needsActionStep" => false, 'completeActionStep' => false);

        $userSumOfAssets = $sengineObj->userSumOfAssets;
        $userSumOfOtherAssets = $sengineObj->userSumOfOtherAssets;
        $userSumOfDebts = $sengineObj->userSumOfDebts;
        $sumOfAssets = $userSumOfAssets + $userSumOfOtherAssets;
        $netWorth = $sumOfAssets - $userSumOfDebts;

        $spouseAge = $sengineObj->spouseAge;
        $child1Age = $sengineObj->child1Age;
        $child2Age = $sengineObj->child2Age;
        $child3Age = $sengineObj->child3Age;
        $child4Age = $sengineObj->child4Age;
        $user_has_dependents = false;
        if ($spouseAge > 0 || $child1Age > 0 || $child2Age > 0 || $child3Age > 0 || $child4Age > 0) {
            $user_has_dependents = true;
        }

        if (!$mrow || $mrow->misctrust == '' || ($mrow->misctrust == '1' && ($mrow->miscreviewyear == '' || $mrow->miscreviewmonth == '')) || $mrow->mischiddenasset == '' || ($mrow->mischiddenasset == '1' && $mrow->miscrightperson == '') || $mrow->miscliquid == '' || $mrow->miscspouse == '') {
            if ($user_has_dependents || $netWorth > 100000) {
                $resultArray['needsActionStep'] = true;
            }
        } else {
            $resultArray['completeActionStep'] = true;
        }
        return $resultArray;
    }

    /*
     * Action Step for #6
     * When total % of risk assets in asset allocation is more than 5% off of
     * their chosen risk tolerance which corresponds to a max risk asset %.
     */

    private function checkForkRiskTolerance($sengineObj) {
        $resultArray = array("needsActionStep" => false, 'title' => '');
        $currentStdDev = $sengineObj->tickerRiskValue; // user calculated risk standard deviation
        $userStdDev = $sengineObj->riskStdDev; // user selected risk standard deviation
        $userRiskValue = ($sengineObj->userRiskValue > 0) ? $sengineObj->userRiskValue : 5; // user selected risk

        if ($sengineObj->wfPoint13 == 1) {
            // Investment Multiplier is the highest possible, no need for actionstep
            return $resultArray;
        }

        $risk_difference = $currentStdDev - $userStdDev;
        $percentage_risk = ($risk_difference / $userStdDev);
        $percentage_taking_risk = abs($percentage_risk);
        // Converting into percentage
        $percentage_taking_risk = $percentage_taking_risk * 100;
        $title = "";

        /*
         * Check whether the current risk is greater than 5% of
         * Risk Tolerance choosen by User.
         */
        if ($risk_difference < 0) {
            $info_text = "<div style='padding-top:0px'>Your chosen risk tolerance of " . round($userRiskValue, 2) . " out of 10 calls for " .
                    "your combined investment mix to have a certain level of risk." .
                    " At the moment, you are taking " . round($percentage_taking_risk, 2) . "% <i>less</i> risk than " .
                    "you indicated you wanted. We suggest <i>raising</i> the amount of investment risk you are " .
                    "taking so it matches your chosen investment risk tolerance." .
                    " FlexScore recommends the following allocation:</div>";
            $resultArray['needsActionStep'] = true;
        } elseif ($risk_difference > 0) {
            $info_text = "<div style='padding-top:0px'>Your chosen risk tolerance of " . round($userRiskValue, 2) . " out of 10 calls for " .
                    "your combined investment mix to have a certain level of risk." .
                    " At the moment, you are taking " . round($percentage_taking_risk, 2) . "% <i>more</i> risk than " .
                    "you indicated you wanted. We suggest <i>lowering</i> the amount of investment risk you are " .
                    "taking so it matches your chosen investment risk tolerance." .
                    " FlexScore recommends the following allocation:</div>";
            $resultArray['needsActionStep'] = true;
        }

        $title = $info_text;

        $title .= "<div id='riskChartWrapper' class='round vPad' style='position:relative;margin:0 auto'>";
        $title .= "<div class=' myScoreTopicDiv floatL' style='height: 320px; position: relative'>";
        $title .= "<div id='riskPieChart'></div></div><div class='floatL' style='padding-top:60px'>";
        $title .= "<table cellpadding='5'><tr><td><div class='round pbYellowBg'></div></td><td id='pbRow1'></td></tr>";
        $title .= "<tr><td><div class='round pbOrangeBg'></div></td><td id='pbRow2'></td></tr>";
        $title .= "<tr><td><div class='round pbBlueBg'></div></td><td id='pbRow3'></td></tr>";
        $title .= "<tr><td><div class='round pbTurquoiseBg'></div></td><td id='pbRow4'></td></tr>";
        $title .= "<tr><td><div class='round pbPinkBg'></div></td><td id='pbRow5'></td></tr></table>";
        $title .= "</div><div class='clearOnly'></div></div><script language='javascript'>drawRiskChart(" . $userRiskValue . ");</script>";

        $resultArray['title'] = $title;

        return $resultArray;
    }

    // Calculating the Total Ticker Value
    private function calculateAndUpdateTickerValue() {
        $user_id = "";

        if (isset($_GET['uid']) && $_GET['uid'] <> '') {
            $user_id = $_GET['uid'];
        }

        if ($user_id == "")
            return false;

        $assetObj = new Assets();
        $total = 0;
        $pertrackObj = "";
        $total_std_deviation = 0;
        $ticker_percentage = 0;
        $utilityObj = new Utility();
        $pertrackObj = new Pertrack();

        $allAssets = $assetObj->findAllBySql("select balance,invpos from assets where type in ('BROK','CR','IRA') and user_id=:user_id and status=0", array("user_id" => $user_id));
        print_r($allAssets);
        if ($allAssets) {
            foreach ($allAssets as $asset) {
                $invArr = json_decode($asset->invpos);
                if ($invArr) {
                    // Calculating the total ticker value
                    foreach ($invArr as $inv) {
                        if (isset($inv) && isset($inv->amount) && isset($inv->ticker)) {
                            $total += $utilityObj->tickerAmountToDB($inv->amount);
                        }
                    }
                }
            }

            // If total is zero then we dont need to update sengine.
            if ($total == 0)
                return true;
            foreach ($allAssets as $asset) {
                $invArr = json_decode($asset->invpos);
                if ($invArr) {
                    // Calculating the total ticker value
                    foreach ($invArr as $inv) {
                        if (isset($inv) && isset($inv->amount) && isset($inv->ticker)) {
                            $pertrackObj = new Pertrack();
                            $pertrackS = $pertrackObj->find("ticker=:ticker", array("ticker" => strtoupper($inv->ticker)));

                            if ($pertrackS) {
                                $ticker_percentage = ($inv->amount / $total) * 100;
                                $total_std_deviation += $ticker_percentage * $pertrackS->std_deviation;
                            }
                        }
                    }
                }
            }
        } else {
            $this->sengine->tickerRiskValue = 0;
        }

        // Updating sengine with ticker value.
        parent::setEngine();
        $this->sengine->tickerRiskValue = $total_std_deviation;
        parent::saveEngine();
    }

    private function RecalculateGoalAmounts($goals, $assets, $personal, $assetContributions, $assetBalances, $goalContributions, $growthrate, $grossIncome) {
        $costIncrease = array("RETIREMENT" => "3.4", "COLLEGE" => "5.8", "CUSTOM" => "3.4", "HOUSE" => "3.4");

        // Calculate Minimum Saved Amount needed based on End Date and Contribution of 0
        foreach ($goals as $goal) {
            if ($goal->goaltype == 'RETIREMENT') {
                $retirementage = 65;
                if (isset($personal->retirementage) && intval($personal->retirementage) > 0) {
                    $retirementage = intval($personal->retirementage);
                }

                $age = '0000-00-00';
                if (isset($personal->age)) {
                    $age = $personal->age;
                }
                $ageArray = explode('-', $age);
                if ($ageArray[0] == "0000") {
                    $ageArray[0] = date('Y') - 30;
                }
                if ($ageArray[1] == "00") {
                    $ageArray[1] = "01";
                }
                if ($ageArray[2] == "00") {
                    $ageArray[2] = "01";
                }
                $ageArray[0] = intval($ageArray[0]) + $retirementage;
                $goal->goalenddate = implode('-', $ageArray);

                $today = new DateTime();
                $endDate = new DateTime($goal->goalenddate);
                if ($today > $endDate) {
                    $goal->goalenddate = date('Y') . "-" . date('m') . "-" . date('d');
                }
            }

            $amount = $goal->goalamount;
            // Calculate Months to End Date
            $months = 120;
            if ($goal->permonth <= 0 || $goal->goaltype == 'RETIREMENT' || $goal->goaltype == 'COLLEGE') {
                $months = $this->getMonthDifference($goal->goalenddate);
                if ($months < 0) {
                    $months = 0;
                }
            }

            $cost = $costIncrease[$goal->goaltype];
            if ($goal->goalassumptions_1 != null && $goal->goalassumptions_1 != "") {
                $cost = $goal->goalassumptions_1;
            }
            // Goal Amount in Future Dollars, accounting for cost increase / inflation
            $totalFuture = $amount * pow(1 + $cost / 1200, $months);
            // Goal Amount in Current Dollars, accounting for growth rate
            $savedDollars = $totalFuture / pow(1 + $growthrate / 1200, $months);
            if ($savedDollars < 0) {
                $savedDollars = 0;
            }

            $goal->saved = 0;
            // Add Retirement Amounts
            if ($goal->goaltype == 'RETIREMENT') {
                $goal->saved += $assetBalances["cr"] + $assetBalances["ira"];
                $savedDollars = $savedDollars - $assetBalances["cr"] - $assetBalances["ira"];
                $assetBalances["cr"] = 0;
                $assetBalances["ira"] = 0;
            }
            // Add Educational Account Amounts
            if ($goal->goaltype == 'COLLEGE') {
                $goal->saved += $assetBalances["educ"];
                $savedDollars = $savedDollars - $assetBalances["educ"];
                if ($savedDollars < 0) {
                    $goal->saved += $savedDollars;
                    $assetBalances["educ"] = abs($savedDollars);
                } else {
                    $assetBalances["educ"] = 0;
                }
            }
            // Add Bank Amount
            if ($savedDollars > 0) {
                $goal->saved += $assetBalances["bank"];
                $savedDollars = $savedDollars - $assetBalances["bank"];
                if ($savedDollars < 0) {
                    $goal->saved += $savedDollars;
                    $assetBalances["bank"] = abs($savedDollars);
                } else {
                    $assetBalances["bank"] = 0;
                }
            }
            // Add Brokerage Amount
            if ($savedDollars > 0) {
                $goal->saved += $assetBalances["brok"];
                $savedDollars = $savedDollars - $assetBalances["brok"];
                if ($savedDollars < 0) {
                    $goal->saved += $savedDollars;
                    $assetBalances["brok"] = abs($savedDollars);
                } else {
                    $assetBalances["brok"] = 0;
                }
            }
            $goal->saved = round($goal->saved);
        }

        // Add remaining saved amount to each goal
        foreach ($goals as $goal) {
            $amount = $goal->goalamount;
            if ($goal->saved >= $amount) {
                continue;
            }
            if ($goal->goaltype == 'COLLEGE') {
                $goal->saved += $assetBalances["educ"];
                if ($goal->saved > $amount) {
                    $saved = ($amount - $goal->saved);
                    $goal->saved += $saved;
                    $assetBalances["educ"] = abs($saved);
                } else {
                    $assetBalances["educ"] = 0;
                }
            }
            // Add Bank Amount
            if ($goal->saved < $amount) {
                $goal->saved += $assetBalances["bank"];
                if ($goal->saved > $amount) {
                    $saved = ($amount - $goal->saved);
                    $goal->saved += $saved;
                    $assetBalances["bank"] = abs($saved);
                } else {
                    $assetBalances["bank"] = 0;
                }
            }
            // Add Brokerage Amount
            if ($goal->saved < $amount) {
                $goal->saved += $assetBalances["brok"];
                if ($goal->saved > $amount) {
                    $saved = ($amount - $goal->saved);
                    $goal->saved += $saved;
                    $assetBalances["brok"] = abs($saved);
                } else {
                    $assetBalances["brok"] = 0;
                }
            }
        }

        // Calculate contribution needed per saved amount / end date of goals
        // Calculate status
        foreach ($goals as $goal) {
            $amount = $goal->goalamount;
            $saved = $goal->saved;
            // Calculate Months to End Date
            $months = 120;
            $monthlyContributions = $goal->permonth;
            $minimumContributions = $monthlyContributions;
            if ($goal->permonth <= 0 || $goal->goaltype == 'RETIREMENT' || $goal->goaltype == 'COLLEGE') {
                $months = $this->getMonthDifference($goal->goalenddate);
                if ($months < 0) {
                    $months = 0;
                }

                $cost = $costIncrease[$goal->goaltype];
                if ($goal->goalassumptions_1 != null && $goal->goalassumptions_1 != "") {
                    $cost = $goal->goalassumptions_1;
                }
                // Goal Amount in Future Dollars, accounting for cost increase / inflation
                $totalFuture = $amount * pow(1 + $cost / 1200, $months);
                $savedDollars = $saved;
                $amountNeeded = $totalFuture - $savedDollars;
                $checkAmount = (($amountNeeded / 1000000) > 1) ? ($amountNeeded / 1000000) : 1;

                $monthlyContributions = 0;
                if ($months == 0) {
                    $monthlyContributions = $amountNeeded;
                }

                $increment = 1;
                while ($amountNeeded > $increment * 100) {
                    $increment *= 10;
                }
                $sign = '+';
                while (abs($amountNeeded) >= $checkAmount && $totalFuture > $savedDollars && $months > 0) {
                    for ($j = 1; $j <= $months; $j++) {
                        $savedDollars = $savedDollars * (1 + $growthrate / 1200) + $monthlyContributions;
                    }
                    $amountNeeded = $totalFuture - $savedDollars;
                    if (abs($amountNeeded) >= $checkAmount) {
                        if ($amountNeeded > 0 && $sign == '-') {
                            $sign = '+';
                            $increment = 0 - $increment / 10;
                        }
                        if ($amountNeeded < 0 && $sign == '+') {
                            $sign = '-';
                            $increment = 0 - $increment / 10;
                        }
                        $monthlyContributions += $increment;
                    }
                    $savedDollars = $saved;
                }

                if ($monthlyContributions < 0) {
                    $monthlyContributions = 0;
                }

                $goal->minimumContributions = $monthlyContributions;
                $goal->contributions = 0;
                // Add Retirement Contributions
                if ($goal->goaltype == 'RETIREMENT') {
                    $goal->contributions += $assetContributions["cr"] + $assetContributions["ira"] + $assetContributions["crEmp"];
                    $monthlyContributions = $monthlyContributions - $assetContributions["cr"] - $assetContributions["ira"] - $assetContributions["crEmp"];

                    $crFound = false;
                    foreach ($assets as $asset) {
                        if ($asset->type == 'CR') {
                            $monthlyContributions = $monthlyContributions - $assetContributions["extraCr"];
                            $crFound = true;
                            break;
                        }
                    }
                    foreach ($assets as $asset) {
                        if (($asset->type == 'IRA' && $asset->assettype <> 51 && !$crFound && $grossIncome * 12 >= 160000) || ($asset->type == 'IRA' && $asset->assettype == 51 && $grossIncome * 12 < 160000)) {
                            $monthlyContributions = $monthlyContributions - $assetContributions["extraIra"];
                            break;
                        }
                    }
                    $assetContributions["cr"] = 0;
                    $assetContributions["crEmp"] = 0;
                    $assetContributions["ira"] = 0;
                }
                // Add Educational Account Contributions
                if ($goal->goaltype == 'COLLEGE') {
                    $goal->contributions += $assetContributions["educ"];
                    $monthlyContributions = $monthlyContributions - $assetContributions["educ"];
                    if ($monthlyContributions < 0) {
                        $goal->contributions += $monthlyContributions;
                        $assetContributions["educ"] = abs($monthlyContributions);
                    } else {
                        $assetContributions["educ"] = 0;
                    }
                }
                // Add Bank Contributions
                if ($monthlyContributions > 0) {
                    $goal->contributions += $assetContributions["bank"];
                    $monthlyContributions = $monthlyContributions - $assetContributions["bank"];
                    if ($monthlyContributions < 0) {
                        $goal->contributions += $monthlyContributions;
                        $assetContributions["bank"] = abs($monthlyContributions);
                    } else {
                        $assetContributions["bank"] = 0;
                    }
                }
                // Add Brokerage Contributions
                if ($monthlyContributions > 0) {
                    $goal->contributions += $assetContributions["brok"];
                    $monthlyContributions = $monthlyContributions - $assetContributions["brok"];
                    if ($monthlyContributions < 0) {
                        $goal->contributions += $monthlyContributions;
                        $assetContributions["brok"] = abs($monthlyContributions);
                    } else {
                        $assetContributions["brok"] = 0;
                    }
                }
            } else {
                $goal->minimumContributions = $monthlyContributions;
                $goal->contributions = 0;
                // Add Bank Contributions
                if ($monthlyContributions > 0) {
                    $goal->contributions += $assetContributions["bank"];
                    $monthlyContributions = $monthlyContributions - $assetContributions["bank"];
                    if ($monthlyContributions < 0) {
                        $goal->contributions += $monthlyContributions;
                        $assetContributions["bank"] = abs($monthlyContributions);
                    } else {
                        $assetContributions["bank"] = 0;
                    }
                }
                // Add Brokerage Contributions
                if ($monthlyContributions > 0) {
                    $goal->contributions += $assetContributions["brok"];
                    $monthlyContributions = $monthlyContributions - $assetContributions["brok"];
                    if ($monthlyContributions < 0) {
                        $goal->contributions += $monthlyContributions;
                        $assetContributions["brok"] = abs($monthlyContributions);
                    } else {
                        $assetContributions["brok"] = 0;
                    }
                }
            }
        }

        // Add remaining contribution amount to each goal
        foreach ($goals as $goal) {
            $amount = $goal->minimumContributions;
            if ($goal->contributions >= $amount) {
                continue;
            }
            if ($goal->goaltype == 'COLLEGE') {
                $goal->contributions += $assetContributions["educ"];
                if ($goal->contributions > $amount) {
                    $contributions = ($amount - $goal->contributions);
                    $goal->contributions += $contributions;
                    $assetContributions["educ"] = abs($contributions);
                } else {
                    $assetContributions["educ"] = 0;
                }
            }
            // Add Bank Amount
            if ($goal->contributions < $amount) {
                $goal->contributions += $assetContributions["bank"];
                if ($goal->contributions > $amount) {
                    $contributions = ($amount - $goal->contributions);
                    $goal->contributions += $contributions;
                    $assetContributions["bank"] = abs($contributions);
                } else {
                    $assetContributions["bank"] = 0;
                }
            }
            // Add Brokerage Amount
            if ($goal->contributions < $amount) {
                $goal->contributions += $assetContributions["brok"];
                if ($goal->contributions > $amount) {
                    $contributions = ($amount - $goal->contributions);
                    $goal->contributions += $contributions;
                    $assetContributions["brok"] = abs($contributions);
                } else {
                    $assetContributions["brok"] = 0;
                }
            }
        }

        // Add remaining contribution amount to each goal
        foreach ($goals as $goal) {
            if ($goal->goaltype == 'COLLEGE') {
                $goal->contributions += $assetContributions["educ"];
                $assetContributions["educ"] = 0;
            }
            // Add Bank Amount
            $goal->contributions += $assetContributions["bank"];
            $assetContributions["bank"] = 0;

            // Add Brokerage Amount
            $goal->contributions += $assetContributions["brok"];
            $assetContributions["brok"] = 0;

            if (round($goal->minimumContributions) > round($goal->contributions)) {
                $goal->status = "Needs Attention";
            } else {
                $goal->status = "On Track";
            }
        }

        return $goals;
    }

}

?>