<?php

/* * ********************************************************************
 * Filename: Scontroller.php
 * Folder: controllers
 * Description: Calls score engine calculations for specified sections
 *              For more info refer the doc Points By Wireframe V 2.doc
 *              of leapscore
 * @author Subramanya HS (For TruGlobal Inc)
 * @editor Thayub J (For Myself)
 * @copied by Alex Thomas (For Myself)
 * @copyright (c) 2012 - 2015
 * Change History:
 * Version         Author               Change Description
 * ******************************************************************** */

class Bcontroller extends Scontroller {
    /**
     *  For Break Down Tab
     *
     */
    const BDAGE = "WfPoint12,WfPoint18,WfPoint22,WfPoint29,WfPoint31";
    const BDGOALS = "WfPoint12,WfPoint18,WfPoint31";
    const BDSAVINGS = "WfPoint12,WfPoint16,WfPoint17,WfPoint18";
    const BDASSET = "wfPoint3,wfPoint1,wfPoint4,wfPoint11,wfPoint12,wfPoint16,wfPoint17,wfPoint28,wfPoint29,wfPoint30,wfPoint38,wfPoint58";
    const BDDEBTS = "wfPoint1,wfPoint4,wfPoint5,wfPoint6,wfPoint7,wfPoint8,wfPoint9,wfPoint11,wfPoint29,wfPoint38";
    const BDLIVINGS = "wfPoint3,wfPoint1,wfPoint2,wfPoint4,wfPoint28,wfPoint30,wfPoint38,wfPoint58";
    const BDALL = "wfPoint3,wfPoint1,wfPoint2,wfPoint4,wfPoint5,wfPoint6,wfPoint7,wfPoint8,wfPoint9,wfPoint11,wfPoint12,wfPoint16,wfPoint17,wfPoint18,wfPoint22,wfPoint28,wfPoint29,wfPoint30,wfPoint31,wfPoint38,wfPoint58";

    public $bengine = null;
    public $user_id = 0;

    /**
     * get initial score
     */
    function init() {
        //if accessed from backend process
        if (isset(Yii::app()->getSession()->get('wsuser')->id)) {
            $this->user_id = Yii::app()->getSession()->get('wsuser')->id;

            //if user is not logged in
            if ($this->user_id == 0) {
                $this->sendResponse(200, CJSON::encode(array("status" => "ERROR")));
            } else {
                // for update user last access date & time
                $umodel = new User;
                $file = $umodel->find("id=:user_id", array("user_id" => Yii::app()->getSession()->get('wsuser')->id));
                $file->lastaccesstimestamp = new CDbExpression('NOW()');
                $file->saveAttributes(array('lastaccesstimestamp'));
            }
        }
    }

    /**
     * get the engine present in the
     * cache / session if present
     */
    function setBreakdownEngine($user_id = 0) {
        //get the score from cache
        // or table
        if (isset(Yii::app()->session["bengine"])) {
            $this->bengine = unserialize(Yii::app()->session["bengine"]);
        } else {
	        parent::setEngine($user_id);
	        $this->bengine = unserialize(Yii::app()->session["sengine"]);
            Yii::app()->session["bengine"] = serialize($this->bengine);
        }
    }

    function resetBreakdownEngine($user_id = 0) {
		parent::setEngine($user_id);
	    $this->bengine = unserialize(Yii::app()->session["sengine"]);
        Yii::app()->session["bengine"] = serialize($this->bengine);
    }

    function breakdownCalculateScore($section, $user_id = 0, $bdflag = NULL) {
        if (isset(Yii::app()->session['wsuser'])) {
            $user_id = Yii::app()->getSession()->get('wsuser')->id;
        } else {
            $user_id = $user_id;
        }

		if($section != "") {
        	$varNames = explode(",", constant("self::" . $section));
    	    foreach ($varNames as $varName) {
	            //echo $varName;
        		if(strtolower($varName) == "wfpoint29") {
					$this->CalculateBreakdownPoint29();
    	    	}
        		else
        		{
    	    	  	$callMethod = "set" . $varName;
	    		    $this->bengine->$callMethod();
    		    }
	        }
        }

		Yii::app()->session["bengine"] = serialize($this->bengine);
        $totalScore = $this->bengine->updateScore();
        $totalScore = ($totalScore < 0) ? 0 : $totalScore;
        $totalScore = ($totalScore > 1000) ? 1000 : $totalScore;
		return round($totalScore);
    }

    function CalculateBreakdownPoint5($restructuringDebtsArr, $personalDebtLoanArr) {
        $calcXMLObj = Yii::app()->calcxml;
        # CALC XML - Restructuring debts
        $calculatePoint1 = true;
        foreach ($restructuringDebtsArr as $each) {
            if (!isset($each->rate) || $each->rate <= 0) {
                $calculatePoint1 = false;
                break;
            }
        }

        if ($calculatePoint1) {
            $currentPayoffMonths = $calcXMLObj->restructuringDebtsAcceleratedPayoffHelper($restructuringDebtsArr);
            $goalEndD = new DateTime();
            $years = floor($currentPayoffMonths / 12);
            $months = $currentPayoffMonths % 12;
            $goalEndDN = $goalEndD->add(new DateInterval('P' . $years . 'Y' . $months . 'M'));

            if (strpos($this->bengine->maxGoalEndYear, "-") === false) {
                $goalEndU = new DateTime();
                $goalEndU = $goalEndU->add(new DateInterval('P' . $this->bengine->maxGoalEndYear . 'Y'));
            } else {
                $goalEndU = new DateTime($this->bengine->maxGoalEndYear);
            }

            if ($goalEndU < $goalEndDN) {
                $point1 = 0;
            } else {
                $point1 = 12;
            }
        } else {
            $point1 = 0;
        }

        # Calc XML - Should I consolidate my personal debt into a new loan?
        $calculatePoint2 = true;
        foreach ($personalDebtLoanArr as $each) {
            if (!isset($each->rate) || $each->rate <= 0) {
                $calculatePoint2 = false;
                break;
            }
        }

        if ($calculatePoint2) {
            $recommendTerm = $calcXMLObj->personalDebtLoanHelper($personalDebtLoanArr);

            if ($recommendTerm) {
                //not consolidate
                $point2 = 13;
            } else {
                //consolidate
                $point2 = 0;
            }
        } else {
            $point2 = 0;
        }
        unset($calcXMLObj);
        $this->bengine->wfPoint5 = $point1 + $point2;
    }

    function CalculateBreakdownPoint10($assetsObj, $insuranceObj, $pertrackObj) {
        $denominator = 0;
        // Calculate denominator
        if ($assetsObj) {
            foreach ($assetsObj as $each) {
                if ($each->invpos) {
                    $invPosArray = json_decode($each->invpos);
                    if ($invPosArray && !empty($invPosArray)) {
                        $total = 0;
                        foreach ($invPosArray as $invPos) {
                            $total += ($invPos->amount) ? $invPos->amount : 0;
                        }
                        $denominator += ($each->balance && $each->balance > $total) ? $each->balance : $total;
                    } else if ($each->balance) {
                        $denominator += $each->balance;
                    }
                } else if ($each->balance) {
                    $denominator += $each->balance;
                }
            }
        }
        if ($insuranceObj) {
            foreach ($insuranceObj as $each) {
                if ($each->cashvalue) {
                    $denominator += $each->cashvalue;
                }
            }
        }

        $percentAssets = round($denominator / 10);
        $granularLevel = 0;
        // Calculate numerator if denominator > 0
        if ($denominator > 0 && $assetsObj) {
            foreach ($assetsObj as $each) {
                if ($each->invpos) {
                    $invPosArray = json_decode($each->invpos);
                    if ($invPosArray && !empty($invPosArray)) {
                        $total = 0;
                        foreach ($invPosArray as $invPos) {
                            if ($invPos->amount && $invPos->amount < $percentAssets) {
                                $granularLevel += $invPos->amount;
                            } else if ($pertrackObj) {
                                foreach ($pertrackObj as $pertrack) {
                                    if ($pertrack->ticker == strtoupper($invPos->ticker)) {
                                        if ($pertrack->itemtype == 'MF' || $pertrack->itemtype == 'ETF') {
                                            $granularLevel += $invPos->amount;
                                        }
                                        break;
                                    }
                                }
                            }
                            $total += ($invPos->amount) ? $invPos->amount : 0;
                        }
                        $cashvalue = ($each->balance && $each->balance > $total) ? ($each->balance - $total) : 0;
                        $granularLevel += ($cashvalue < $percentAssets) ? $cashvalue : 0;
                    } else if ($each->balance && ($each->balance < $percentAssets || $each->type == 'BANK')) {
                        $granularLevel += $each->balance;
                    }
                } else if ($each->balance && ($each->balance < $percentAssets || $each->type == 'BANK')) {
                    $granularLevel += $each->balance;
                }
            }
        }

        if ($denominator > 0 && $insuranceObj) {
            foreach ($insuranceObj as $each) {
                if ($each->cashvalue && $each->cashvalue < $percentAssets) {
                    $granularLevel += $each->cashvalue;
                }
            }
        }

        $this->bengine->wfPoint10 = ($denominator > 0) ? (50 * ($granularLevel / $denominator)) : 0;
    }

    function CalculateBreakdownPoint29()
    {
		$calcXMLObj = Yii::app()->calcxml;
		$valueObj = new stdClass();
		$valueObj->clientIncome = $this->bengine->grossIncome * 12;
		$valueObj->spouseIncome = $this->bengine->spouseIncome * 12;
		$valueObj->spouseAge = $this->bengine->spouseAge;
		$valueObj->spouseRetAge = $this->bengine->spouseRetAge;
		$valueObj->beforeTaxReturn = $this->bengine->userGrowthRate / 100;
		$valueObj->inflation = 0.034;
		$valueObj->funeral = 0;
		$valueObj->finalExpenses = 6560;
		$valueObj->mortgageBalance = $this->bengine->mortgageBalance;
		$valueObj->otherDebts = $this->bengine->otherDebts;
		if ($this->bengine->spouseAge == 0 && $this->bengine->child1Age == 0 && $this->bengine->child2Age == 0 && $this->bengine->child3Age == 0 && $this->bengine->child4Age == 0) {
			$valueObj->desiredIncome = 0;
		} else {
			$valueObj->desiredIncome = $this->bengine->userIncomePerMonth * 12 * 0.80; // 80 % of total income
		}
		if ($this->bengine->spouseAge > 0) {
			$valueObj->term = $this->bengine->spouseLifeEC - $this->bengine->spouseAge;
		} else {
			$valueObj->term = $this->bengine->lifeEC - $this->bengine->userCurrentAge;
		}
		$valueObj->collegeNeeds = $this->bengine->collegeAmount;
		$valueObj->investmentAssets = ($this->bengine->numeratorP14 - $this->bengine->insuranceCashValue);
		$valueObj->lifeInsurance = $this->bengine->LifeInsurance;
		$valueObj->includeSocsec = "Y";
		$valueObj->child1Age = $this->bengine->child1Age;
		$valueObj->child2Age = $this->bengine->child2Age;
		$valueObj->child3Age = $this->bengine->child3Age;
		$valueObj->child4Age = $this->bengine->child4Age;

		$outputCalc = $calcXMLObj->lifeInsuranceINeedHelper($valueObj);

		if ($outputCalc != -1) {
			$this->bengine->insuranceNeededActionStep = $outputCalc;
			$denominator = $this->bengine->LifeInsurance + $outputCalc;
			$this->bengine->wfPoint29 = ($denominator > 0) ? (24 * ($this->bengine->LifeInsurance / $denominator)) : 24;
		}

		// unset($outputCalc);
		if ($this->bengine->wfPoint29 > 24) {
			$this->bengine->wfPoint29 = 24;
		} elseif ($this->bengine->wfPoint29 <= 0) {
			$this->bengine->wfPoint29 = 0;
		}

		// Degrade points
		if ($this->bengine->insuranceReviewYear29 > 0) {
			$currentYear = date('Y');
			$yearDifference = $currentYear - $this->bengine->insuranceReviewYear29;

			if ($yearDifference > 4 && $this->bengine->wfPoint29 > -24) {
				$this->bengine->wfPoint29 = -24;
			} elseif ($yearDifference > 3 && $yearDifference <= 4 && $this->bengine->wfPoint29 > -12) {
				$this->bengine->wfPoint29 = -12;
			} elseif ($yearDifference > 2 && $yearDifference <= 3 && $this->bengine->wfPoint29 > 0) {
				$this->bengine->wfPoint29 = 0;
			} elseif ($yearDifference > 1 && $yearDifference <= 2 && $this->bengine->wfPoint29 > 12) {
				$this->bengine->wfPoint29 = 12;
			} elseif ($yearDifference <= 1 && $this->bengine->wfPoint29 > 24) {
				$this->bengine->wfPoint29 = 24;
			}
		}
		unset($calcXMLObj);
    }

}

?>
