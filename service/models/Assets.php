<?php

/**
 * This is the model class for table "expense".
 */
class Assets extends CActiveRecord {

    public $lstype;
    public $aavg;
    public $acount;

    /**
     * Returns the static model of the specified AR class.
     * @return expense the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'assets';
    }

    public $totalAssets = 0;
    public $assetsProf = 0;
    public $count_assets = 0;
    public $total = 0;
    public $diff = 0;
    public $totalOverallAssets = 0;
    public $overallAssets = 0;
    public $investment_sum = 0;
    public $foo = 0;
    public $diff_insurnace = 0;
    public $totalAssets_insu = 0;
    public $total_goal = 0;
    public $socialsecurityTotal = 0;
    public $pensionTotal = 0;
    public $investSum = 0;
    public $retireSum = 0;
    public $contriCRSum = 0;
    public $totalMC = 0;
    public $taxBalSum = 0;
    public $retSum = 0;
    public $sumDeferred = 0;
    public $retireContriSum = 0;
    public $startingTaxFreeBal = 0;
    public $assetValuePension = 0;
    public $sumCurrentSavings = 0;
    public $sumAssetsBreak = 0;
    public $sumCurrentSavingsGoal = 0;


    public function getDefaultName( $type ) {
        $name = 'Other';
        switch ($type)
        {
            case 'VEHI':
                $name = 'Vehicle';
                break;
            case 'BANK':
                $name = 'Bank Account';
                break;
            case 'IRA':
                $name = 'IRA';
                break;
            case 'CR':
                $name = 'Company Retirement Plan';
                break;
            case 'BROK':
                $name = 'Brokerage';
                break;
            case 'EDUC':
                $name = 'Educational Account';
                break;
            case 'PROP':
                $name = 'Property';
                break;
            case 'PENS':
                $name = 'Pension';
                break;
            case 'SS':
                $name = 'Social Security';
                break;
            case 'BUSI':
                $name = 'Business';
                break;
            case 'OTHE':
                $name = 'Other';
                break;
            default:
                $name = 'Other';
        }
        return $name;
    }
}
?>