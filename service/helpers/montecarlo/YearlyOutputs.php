<?php
/**
 * 
 */
class YearlyOutputs {
    
    public $medianTaxableBalance;
    public $medianTaxDeferredBalance;
    public $medianTaxFreeBalance;
    public $medianTotalBalance;
    public $top10PercentBalance;
    public $bottom10PercentBalance;
    public $medianWithdrawal;
    public $medianWithdrawalTaxes;
    public $medianPercentPPMaintained;
    public $medianRMD;
    public $medianRMDTaxes;
    public $medianRMDUsedForExpenses;
    public $avgReturn;
    public $avgInflation;
    public $numberOfFailures;
    public $probabilityOfSuccess;
    public $avgIraPenaltyPaid;
    public $medianTaxFreeIncome;

    function clear(){
      $this->medianTaxableBalance = 0.0;
      $this->medianTaxDeferredBalance = 0.0;
      $this->medianTaxFreeBalance = 0.0;
      $this->medianTotalBalance = 0.0;
      $this->top10PercentBalance = 0.0;
      $this->bottom10PercentBalance = 0.0;
      $this->medianPercentPPMaintained = 0.0;
      $this->medianWithdrawal = 0.0;
      $this->medianWithdrawalTaxes = 0.0;
      $this->medianRMD = 0.0;
      $this->medianRMDTaxes = 0.0;
      $this->medianRMDUsedForExpenses = 0.0;
      $this->avgReturn = 0.0;
      $this->avgInflation = 0.0;
      $this->numberOfFailures = 0.0;
      $this->probabilityOfSuccess = 0.0;
      $this->avgIraPenaltyPaid = 0; 
      $this->medianTaxFreeIncome = 0;
    }
  }
?>