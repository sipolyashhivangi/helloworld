<?php

/**
 * This is the model class for table "State".
 *
 */
class State extends CActiveRecord {

//    Remove if not needed
//    public $advisor_id;
//    public $stateregistered;

    public $states_name = array(
        1 => array('name' => 'Alabama',
        'title' => 'AL',
        'id' => 1),
        2 => array('name' => 'Alaska',
        'title' => 'AK',
        'id' => 2),
        3 => array('name' => 'Arizona',
        'title' => 'AZ',
        'id' => 3),
        4 => array('name' => 'Arkansas',
        'title' => 'AR',
        'id' => 4),
        5 => array('name' => 'California',
        'title' => 'CA',
        'id' => 5),
        6 => array('name' => 'Colorado',
        'title' => 'CO',
        'id' => 6),
        7 => array('name' => 'Connecticut',
        'title' => 'CT',
        'id' => 7),
        8 => array('name' => 'Delaware',
        'title' => 'DE',
        'id' => 8),
        9 => array('name' => 'Florida',
        'title' => 'FL',
        'id' => 9),
        10 => array('name' => 'Georgia',
        'title' => 'GA',
        'id' => 10),
        11 => array('name' => 'Hawaii',
        'title' => 'HI',
        'id' => 11),
        12 => array('name' => 'Idaho',
        'title' => 'ID',
        'id' => 12),
        13 => array('name' => 'Illinois',
        'title' => 'IL',
        'id' => 13),
        14 => array('name' => 'Indiana',
        'title' => 'IN',
        'id' => 14),
        15 => array('name' => 'Iowa',
        'title' => 'IA',
        'id' => 15),
        16 => array('name' => 'Kansas',
        'title' => 'KS',
        'id' => 16),
        17 => array('name' => 'Kentucky',
        'title' => 'KY',
        'id' => 17),
        18 => array('name' => 'Louisiana',
        'title' => 'LA',
        'id' => 18),
        19 => array('name' => 'Maine',
        'title' => 'ME',
        'id' => 19),
        20 => array('name' => 'Maryland',
        'title' => 'MD',
        'id' => 20),
        21 => array('name' => 'Massachusetts',
        'title' => 'MA',
        'id' => 21),
        22 => array('name' => 'Michigan',
        'title' => 'MI',
        'id' => 22),
        23 => array('name' => 'Minnesota',
        'title' => 'MN',
        'id' => 23),
        24 => array('name' => 'Mississippi',
        'title' => 'MS',
        'id' => 24),
        25 => array('name' => 'Missouri',
        'title' => 'MO',
        'id' => 25),
        26 => array('name' => 'Montana',
        'title' => 'MT',
        'id' => 26),
        27 => array('name' => 'Nebraska',
        'title' => 'NE',
        'id' => 27),
        28 => array('name' => 'Nevada',
        'title' => 'NV',
        'id' => 28),
        29 => array('name' => 'New Hampshire',
        'title' => 'NH',
        'id' => 29),
        30 => array('name' => 'New Jersey',
        'title' => 'NJ',
        'id' => 30),
        31 => array('name' => 'New Mexico',
        'title' => 'NM',
        'id' => 31),
        32 => array('name' => 'New York',
        'title' => 'NY',
        'id' => 32),
        33 => array('name' => 'North Carolina',
        'title' => 'NC',
        'id' => 33),
        34 => array('name' => 'North Dakota',
        'title' => 'ND',
        'id' => 34),
        35 => array('name' => 'Ohio',
        'title' => 'OH',
        'id' => 35),
        36 => array('name' => 'Oklahoma',
        'title' => 'OK',
        'id' => 36),
        37 => array('name' => 'Oregon',
        'title' => 'OR',
        'id' => 37),
        38 => array('name' => 'Pennsylvania',
        'title' => 'PA',
        'id' => 38),
        39 => array('name' => 'Rhode Island',
        'title' => 'RI',
        'id' => 39),
        40 => array('name' => 'South Carolina',
        'title' => 'SC',
        'id' => 40),
        41 => array('name' => 'South Dakota',
        'title' => 'SD',
        'id' => 41),
        42 => array('name' => 'Tennessee',
        'title' => 'TN',
        'id' => 42),
        43 => array('name' => 'Texas',
        'title' => 'TX',
        'id' => 43),
        44 => array('name' => 'Utah',
        'title' => 'UT',
        'id' => 44),
        45 => array('name' => 'Vermont',
        'title' => 'VT',
        'id' => 45),
        46 => array('name' => 'Virginia',
        'title' => 'VA',
        'id' => 46),
        47 => array('name' => 'Washington',
        'title' => 'WA',
        'id' => 47),
        48 => array('name' => 'Washington D.C.',
        'title' => 'DC ',
        'id' => 48),
        49 => array('name' => 'West Virginia',
        'title' => 'WV',
        'id' => 49),
        50 => array('name' => 'Wisconsin',
        'title' => 'WI',
        'id' => 50),
        51 => array('name' => 'Wyoming',
        'title' => 'WY',
        'id' => 51),
    );


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }


    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'advisorstates';
    }

}
