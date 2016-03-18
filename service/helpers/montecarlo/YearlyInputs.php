<?php

class YearlyInputs
  {
    public $incomeTaxRate;
    public $investmentTaxRate;
    public $compoundedIncomeTaxRate;
    public $compoundedTaxAndIraPenaltyRate;
    public $inflationAvg;
    public $inflationStdev;
    public $returnAvg;
    public $returnStdev;
    public $newTaxableInvestment;
    public $newTaxDeferredInvestment;
    public $newTaxFreeInvestment;
    public $spendingRequested;
    public $extraTaxableIncome;
    public $extraTaxFreeIncome;

    function clear()
    {
      $this->incomeTaxRate = 0.0;
      $this->investmentTaxRate = 0.0;
      $this->compoundedIncomeTaxRate = 0.0;
      $this->compoundedTaxAndIraPenaltyRate = 0.0;
      $this->inflationAvg = 0.0;
      $this->inflationStdev = 0.0;
      $this->returnAvg = 0.0;
      $this->returnStdev = 0.0;
      $this->newTaxableInvestment = 0.0;
      $this->newTaxDeferredInvestment = 0.0;
      $this->newTaxFreeInvestment = 0.0;
      $this->spendingRequested = 0.0;
      $this->extraTaxableIncome = 0.0;
      $this->extraTaxFreeIncome = 0.0; 
    }
}
?>