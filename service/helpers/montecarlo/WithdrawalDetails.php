<?php
/**
 * 
 */
class WithdrawalDetails
  {
    public $grossRMD;
    public $RMDTaxes;
    public $netRMD;
    public $netWithdrawalNeeded;
    public $RMDWithdrawalUsedForExpenses;
    public $grossWithdrawalAmount;
    public $taxesOnGrossWithdrawal;
    public $afterTaxIncome;

    function clear()
    {
      $this->grossRMD = 0.0;
      $this->RMDTaxes = 0.0;
      $this->netRMD = 0.0;
      $this->netWithdrawalNeeded = 0.0;
      $this->RMDWithdrawalUsedForExpenses = 0.0;
      $this->grossWithdrawalAmount = 0.0;
      $this->taxesOnGrossWithdrawal = 0.0;
      $this->afterTaxIncome = 0.0;
    }
  }
?>
