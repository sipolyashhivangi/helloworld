<?php

/**
 * This is the model class for table "account".
 *
 * The followings are the available columns in table 'account':
 * @property integer $user_id
 * @property string $connecttype
 * @property string $accttype
 * @property integer $itemrefid
 * @property integer $acctcategoryid
 * @property integer $actid
 * @property string $name
 * @property string $acthldrname
 * @property integer $balanceowed
 * @property integer $remainingyrs
 * @property string $mortgagetype
 * @property string $mortgagetypeforothers
 * @property string $IRAtype
 * @property integer $interestrate
 * @property integer $paypermonth
 * @property integer $amount
 * @property integer $yourcontribution
 * @property integer $employercontribution
 * @property string $beneficiarydesignated
 * @property integer $takeoutpermonth
 * @property integer $ageyoulive
 * @property string $symbolticker
 * @property integer $symboltickeramt
 * @property string $educationalacttype
 * @property string $realestateaddr1
 * @property string $realestateaddr2
 * @property string $realestatecity
 * @property string $realestatestate
 * @property integer $realestatezipcode
 * @property integer $netmonthlyincome
 * @property string $loanonthisproperty
 * @property integer $estmatedvaluecollectible
 * @property string $estmatedappriciationratecollectible
 * @property integer $pensionpayoutage
 * @property integer $estmatedssbenfitat62
 * @property string $businessowner
 * @property integer $worthamount
 * @property string $businessgrowthrate
 * @property integer $lifeinsuranceamt
 * @property integer $lifeinsurancespouseamt
 * @property integer $lifeinsurancebenefitamt
 * @property string $insurancepolicytype
 * @property string $insurancepolicyend
 * @property integer $insurancepremium
 * @property integer $dabilityannualincome
 * @property string $disabilitypolicytype
 * @property integer $dailybenefitamt
 * @property string $miscdetialjsonfmt
 * @property integer $dloadstatus
 * @property string $errormessage
 * @property string $lastdownloadtime
 * @property integer $isactive
 *
class Account extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @return Account the static model class
     *
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     *
    public function tableName() {
        return 'account';
    }

    /**
     * Includes cc, auto,mortagage,autoloan others
     * @param type $user_id
     * @return type
     *
    function getUserDebts($user_id) {

        // Checking if the data is there. !
        $userDebtsSql = "SELECT * FROM account WHERE user_id=:user_id and accttype in ('Loan','Autoloan','Mortgage','DebtOther','CreditCard')";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userDebtsSql)->bindValue('user_id', $user_id);

        $row = $command->queryAll();

        return $row;
    }

    /**
     *
     * @param type $user_id
     * @return type
     *
    function getUserCreditCards($user_id) {

        // Checking if the data is there. !
        $userDebtsSql = "SELECT * FROM account WHERE user_id=:user_id and acctype like 'CreditCard'";
        $connection = Yii::app()->db3;
        $command = $connection->createCommand($userDebtsSql)->bindValue('user_id', $user_id);

        $row = $command->queryRow();

        return $row;
    }

}

?>