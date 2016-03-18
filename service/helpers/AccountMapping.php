<?php

/**
 * Description of AccountMapping
 *
 * @author Subramanya
 */
class AccountMapping {

    //account group from cashedge to LS
    //make sure if we add more account group
    // should also be added in account table
    public function getAccountMapping($actType, $category) {

        switch ($category) {
            case "AcctType":
                $accountTypeMapping = array(
                    "DDA" => array("table" => "Assets", "type" => "BANK", "subtype" => "Checking"),
                    "SDA" => array("table" => "Assets", "type" => "BANK", "subtype" => "Saving"),
                    "CCA" => array("table" => "Debts", "type" => "CC", "subtype" => "CC"),
                    "INV" => array("table" => "Assets", "type" => "BROK", "subtype" => "Investment"),
                    "OAA" => array("table" => "Assets", "type" => "OTHE", "subtype" => "Otherassets"),
                    "OLA" => array("table" => "Debts", "type" => "LOAN", "subtype" => "OtherLiability"),
                    "INS" => array("table" => "Insurance", "type" => "insurance", "subtype" => "insurance")
                );
                return $accountTypeMapping[$actType];
                break;
            case "ExtAcctType":
                $extAccountTypeMapping = array(
                    "DDA" => array("table" => "Assets", "type" => "BANK", "subtype" => "checking"),
                    "CMA" => array("table" => "Assets", "type" => "BANK", "subtype" => "cashmanagement"),
                    "OVD" => array("table" => "Debts", "type" => "LOAN", "subtype" => "Overdraftaccount"),
                    "SDA" => array("table" => "Assets", "type" => "BANK", "subtype" => "saving"),
                    "MMA" => array("table" => "Assets", "type" => "BANK", "subtype" => "moneymarket"),
                    "CCA" => array("table" => "Debts", "type" => "CC", "subtype" => "creditcard"),
                    "INV" => array("table" => "Assets", "type" => "BROK", "subtype" => "brokerage"),
                    "MTF" => array("table" => "Assets", "type" => "BROK", "subtype" => "mutualfund"),
                    "OAA" => array("table" => "Assets", "type" => "OTHE", "subtype" => "other"),
                    "GIC" => array("table" => "Assets", "type" => "BROK", "subtype" => "terminvestment"),
                    "CDA" => array("table" => "Assets", "type" => "BANK", "subtype" => "certificateofdeposit"),
                    "OLA" => array("table" => "Debts", "type" => "LOAN", "subtype" => "liabilities"),
                    "LOC" => array("table" => "Debts", "type" => "LOAN", "subtype" => "lineofcredit"),
                    "EQU" => array("table" => "Debts", "type" => "MORT", "subtype" => "homeequityloan"),
                    "ILA" => array("table" => "Debts", "type" => "LOAN", "subtype" => "loan"),
                    "MLA" => array("table" => "Debts", "type" => "MORT", "subtype" => "mortgage"),
                    "ILC" => array("table" => "Debts", "type" => "LOAN", "subtype" => "autoloan"),
                    "ILI" => array("table" => "Debts", "type" => "LOAN", "subtype" => "investmentloan"),
                    "ILS" => array("table" => "Debts", "type" => "LOAN", "subtype" => "studentloan"),
                    "WLI" => array("table" => "Insurance", "type" => "LIFE", "subtype" => "wholelifeinsurance"),
                    "ULI" => array("table" => "Insurance", "type" => "LIFE", "subtype" => "universallifeinsurance"),
                    "TLI" => array("table" => "Insurance", "type" => "LIFE", "subtype" => "terminsurance"),
                    "DIS" => array("table" => "Insurance", "type" => "DISA", "subtype" => "disabilityinsurance"),
                    "LTC" => array("table" => "Insurance", "type" => "LONG", "subtype" => "longtermcareinsurance"),
                    "PAC" => array("table" => "Insurance", "type" => "HOME", "subtype" => "homeownersinsurance"),
                    "VEH" => array("table" => "Insurance", "type" => "VEHI", "subtype" => "vehicleinsurance"),
                    "HLT" => array("table" => "Insurance", "type" => "HEAL", "subtype" => "healthinsurance"),
                    "ALI" => array("table" => "Assets", "type" => "BROK", "subtype" => "annui"),
                    "BPA" => array("table" => "Debts", "type" => "LOAN", "subtype" => "Bill"),

                );
                return $extAccountTypeMapping[$actType];
                break;
            case "AcctGroup":
                $acctGroupTypeMapping = array(
                    "Cash" => array("table" => "Assets", "type" => "OTHE", "subtype" => "cash"),
                    "Credit" => array("table" => "Debts", "type" => "CC", "subtype" => "Credit"),
                    "Investment" => array("table" => "Assets", "type" => "BROK", "subtype" => "Investment"),
                    "Bill" => array("table" => "Debts", "type" => "LOAN", "subtype" => "Bill"),
                    "OTHE" => array("table" => "Debts", "type" => "OTHE", "subtype" => "OTHE"),
                    "insurance" => array("table" => "Insurance", "type" => "LIFE", "subtype" => "LIFE"),
                );
                return $acctGroupTypeMapping[$actType];
                break;
        }
    }
}

?>
