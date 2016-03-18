/* PLEASE NOTE:  This file is for historical purposes only.  It is not to be run in order to create a db. */
/* Use the sql.gz files to create the db */

ALTER TABLE `notification` ADD `refid` INT( 11 ) NOT NULL COMMENT 'ref id to parent table ' AFTER `context`;

ALTER TABLE `assets` ADD `invpos` TEXT NOT NULL COMMENT 'stores json format of invpos ticker' AFTER `ticker`;

ALTER TABLE `assets` MODIFY `loan` int(11) NOT NULL COMMENT 'The loan on the asset if any on the PROPERTY or VEHICLE section';

ALTER TABLE `goal` ADD `payoffdebts` text NOT NULL COMMENT 'stores debt ids incase of pay off debt goal';

ALTER TABLE `goal` ADD `monthlyincome` INT( 11 ) NOT NULL DEFAULT '0' AFTER `payoffdebts` ;

ALTER TABLE `insurance` ADD `beneficiary` VARCHAR( 100 ) NOT NULL AFTER `grouppolicy`;

ALTER TABLE `insurance` ADD `insurancefor` VARCHAR( 25 ) NOT NULL COMMENT 'Who Owns Policy' AFTER `grouppolicy` ;

ALTER TABLE `assets` ADD `withdrawal` int(11) NOT NULL COMMENT 'The monthly withdrawal amount for this asset type' AFTER `empcontribution`;

ALTER TABLE `actionstep` CHANGE  `actionstatus`  `actionstatus` ENUM(  '0',  '1',  '2',  '3' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '0=new,1=completed,2=viewed,3=started';

ALTER TABLE  `actionstep` CHANGE  `priority`  `points` INT( 2 ) NOT NULL;

ALTER TABLE  `actionstep` ADD  `userorder` INT( 11 ) NOT NULL AFTER  `points`;

ALTER TABLE `assets` ADD `livehere` int(11) NOT NULL COMMENT 'Does user live at this asset' AFTER `withdrawal`;

ALTER TABLE  `goal` CHANGE  `goalenddate`  `goalenddate` DATE NOT NULL;
ALTER TABLE  `goal` CHANGE  `goalstartdate`  `goalstartdate` DATE NOT NULL;

ALTER TABLE  `actionstep` CHANGE  `actionstatus`  `actionstatus` ENUM(  '0',  '1',  '2',  '3',  '4' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '0=new,1=completed,2=viewed,3=started,4=history';

/*  06/20/2013  */
ALTER TABLE `actionstep` CHANGE `actionstatus` `actionstatus` ENUM('0','1','2','3','4','5') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '0=new,1=completed,2=viewed,3=started,4=history,5=deleted';

/*  06/24/2013 */
ALTER TABLE `actionstep` ADD `flexi1` TEXT NOT NULL COMMENT 'store actiondetails' AFTER `userorder`;
ALTER TABLE `actionstep` ADD `flexi2` TEXT NOT NULL COMMENT 'store percentage values' AFTER `flexi1`;

ALTER TABLE `actionstep` CHANGE  `lastmodifiedtime` `lastmodifiedtime` datetime NOT NULL;

/*  07/02/2013  */
ALTER TABLE `actionstep` ADD `flexi3` TEXT NOT NULL COMMENT 'store link id values' AFTER `flexi2`;


/*  07/05/2013  */
ALTER TABLE `actionstep` ADD `flexi4` TEXT NOT NULL COMMENT 'store article view status', ADD `flexi5` TEXT NOT NULL COMMENT 'store $amount from SE';

/*  07/08/2013  */
ALTER TABLE `actionstep` ADD `type` ENUM('short', 'instant', 'mid') NOT NULL AFTER `actionstatus`;

/*  07/11/2013   */
ALTER TABLE  `assets` CHANGE  `balance`  `balance` FLOAT NULL DEFAULT NULL COMMENT  'This field also stores the balance and the worth of the asset type';

ALTER TABLE  `debts` CHANGE  `balowed`  `balowed` FLOAT NULL DEFAULT NULL COMMENT  'The balance amount owed totally',
CHANGE  `apr`  `apr` FLOAT NULL DEFAULT NULL COMMENT  'It is the interest rate of the debt per month';

/*** Till this Pushed to Producton on 07/12/2013 **/

/* 07/23/2013 */

ALTER TABLE `user` ADD `restpasswordtokenkey` VARCHAR( 50 ) NOT NULL AFTER `isactive`;
/* 07/25/2013 - Upto this patch applied on staging */


/* 07/31/2013 - Added by Thayub */

ALTER TABLE  `assets` ADD  `FILoginAcctId` INT( 100 ) NOT NULL COMMENT  'This is the FILoginAcctId from the leapscoremeta.cashedgeitem table' AFTER  `context`;
ALTER TABLE  `debts` ADD  `FILoginAcctId` INT( 100 ) NOT NULL COMMENT  'This is the FILoginAcctId from the leapscoremeta.cashedgeitem table' AFTER  `context`;
ALTER TABLE  `insurance` ADD  `FILoginAcctId` INT( 100 ) NOT NULL COMMENT  'This is the FILoginAcctId from the leapscoremeta.cashedgeitem table' AFTER  `context`;



/* 08/01/2013 */ ---- Sql Optimization

ALTER TABLE `assets` ADD INDEX ( `status` );
ALTER TABLE `assets` ADD INDEX ( `type` );
ALTER TABLE `assets` ADD INDEX ( `livehere` );
ALTER TABLE `assets` ADD INDEX ( `uid` );
ALTER TABLE `assets` ADD INDEX ( `balance` );

ALTER TABLE `insurance` ADD INDEX ( `type` );
ALTER TABLE `insurance` ADD INDEX ( `cashvalue` );
ALTER TABLE `insurance` ADD INDEX ( `status` );
ALTER TABLE `insurance` ADD INDEX ( `uid` );
ALTER TABLE `insurance` ADD INDEX ( `lifeinstype` );

/* Till this Pushed to Producton on 08/06/2013 */

DROP TABLE IF EXISTS user_meta_info;


/* THAYUB FOR CASHEDGE */

    ALTER TABLE `assets` ADD `accttype` VARCHAR( 100 ) NOT NULL COMMENT 'This is the classification code from CE' AFTER `FILoginAcctId`;
    ALTER TABLE `debts` ADD `accttype` VARCHAR( 100 ) NOT NULL COMMENT 'This is the classification code from CE' AFTER `FILoginAcctId`;
    ALTER TABLE `insurance` ADD `accttype` VARCHAR( 100 ) NOT NULL COMMENT 'This is the classification code from CE' AFTER `FILoginAcctId`;



    CREATE TABLE `celog` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `action` varchar(10) NOT NULL,
      `flloginacctid` int(11) NOT NULL,
      `acctid` int(11) NOT NULL,
      `status` tinyint(4) NOT NULL,
      `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

    /* THAYUB - aug 28*/
    ALTER TABLE  `celog` ADD  `uid` INT NOT NULL COMMENT  'The UID of the user' AFTER `acctid`;
    /* Till This we pused to Staging  - 8/28/2013 */

    /* 08/29/2013 */
    DELETE FROM `actionstep` WHERE actionid = '12' AND actionstatus IN ('0','2','3');
    /* Till This we pused to Staging  - 8/31/2013 */

    /* 08/31/2013 */
    ALTER TABLE `user` ADD `requestinvitetokenkey` VARCHAR( 255 ) NOT NULL AFTER `lastaccesstimestamp`;
    ALTER TABLE `user` CHANGE `isactive` `isactive` ENUM( '0', '1', '2' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '0->Inactive, 1-Active and 2-Disabled'



   /* Sep 03 2013 */
   /*Added by Thayub for revamped addupdate to DB function*/
   ALTER TABLE  `insurance` ADD  `ticker` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT  'ticker from investment account from getpos call' AFTER  `accttype`;
   ALTER TABLE  `assets` CHANGE  `ticker`  `ticker` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT  'ticker from investment account from getpos call';

/* Sep 04 2013 */
ALTER TABLE  `debts` CHANGE  `balowed`  `balowed` VARCHAR( 44 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT  '0' COMMENT  'The balance amount owed totally';
ALTER TABLE  `assets` CHANGE  `balance`  `balance` VARCHAR( 44 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT  '0' COMMENT  'The balance amount that you have';


#ALTER TABLE  `user` CHANGE  `createdtimestamp`  `createdtimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT  'The time stamp';


/* Sep 16 2013 Vinoth */

ALTER TABLE  `user` CHANGE  `createdtimestamp`  `createdtimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT  'The time stamp';


/*Till this we pushed to production 9.10.2013*/

/*Sep 16 2013*/
ALTER TABLE `debts` ADD INDEX ( `uid` );
ALTER TABLE `goal` ADD INDEX ( `uid` );
ALTER TABLE `income` ADD INDEX ( `uid` );

ALTER TABLE  `userscore` CHANGE  `scoredetails`  `scoredetails` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT  'SENGINE serialized object';

/*Thayub - Sep 26 2013*/
ALTER TABLE  `assets` ADD  `classified` TINYINT NOT NULL DEFAULT  '0' COMMENT  'This column specifies if this investment type is classified or not' AFTER  `invpos`;


/*Dan - 10/1/2013 new table for user media*/
CREATE TABLE IF NOT EXISTS `usermedia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `media_type` varchar(50) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

/*Thayub - 10/11/2013 fix for error by HONG*/

ALTER TABLE `userpersonalinfo` CHANGE `retirementage` `retirementage` INT( 3 ) NOT NULL DEFAULT '65';

/* Till this we pushed to Production 10/14/2013 */
 /* 10/23/2013 */
CREATE TABLE IF NOT EXISTS `analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `details` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/* Till this we pushed to Production 10/31/2013 */

# ** History created on 1/8/2014 *****
ALTER TABLE `userscore` ADD `nintydays` INT( 11 ) NOT NULL AFTER `scorechange`;


# 11/12/13 Change zip code in the user table to varchar so that leading zeroes
# and zip+4 strings are accommodated.
ALTER TABLE `user` CHANGE zip zip VARCHAR(15) NOT NULL;



# 11/19/13 Move the cashedgeaccount and cashedgeitem tables from the
# leapscoremeta database to the leapscoremaster database.
CREATE TABLE `leapscoremaster`.`cashedgeaccount` LIKE `leapscoremeta`.`cashedgeaccount`;
INSERT `leapscoremaster`.`cashedgeaccount` SELECT * FROM `leapscoremeta`.`cashedgeaccount`;

CREATE TABLE `leapscoremaster`.`cashedgeitem` LIKE `leapscoremeta`.`cashedgeitem`;
INSERT `leapscoremaster`.`cashedgeitem` SELECT * FROM `leapscoremeta`.`cashedgeitem`;

DROP TABLE `leapscoremeta`.`cashedgeaccount`;
DROP TABLE `leapscoremeta`.`cashedgeitem`;

# 11/22/13 Added by Alex to prevent Duplication in Actionstep
ALTER TABLE `actionstep` ADD UNIQUE (`uid`, `actionid`);


# 11/25/2013 Change request int(10) to int(16) - Please test it

ALTER TABLE `insurance` CHANGE `uid` `uid` INT( 16 ) NOT NULL COMMENT 'This is the user ID of the user ';
ALTER TABLE `insurance` CHANGE `uid` `uid` INT( 16 ) NOT NULL COMMENT 'This is the user ID of the user ';
ALTER TABLE `insurance` CHANGE `uid` `uid` INT( 16 ) NOT NULL COMMENT 'This is the user ID of the user ';
ALTER TABLE `estimation` CHANGE `uid` `uid` INT( 16 ) NOT NULL;
ALTER TABLE `income` CHANGE `uid` `uid` INT( 16 ) NOT NULL COMMENT 'The user id from the leapscoremaster DB ';
ALTER TABLE `actionstep` CHANGE `uid` `uid` INT( 16 ) NOT NULL ;
ALTER TABLE `usermedia` CHANGE `user_id` `user_id` INT( 16 ) NOT NULL ;
ALTER TABLE `filemanagement` CHANGE `uid` `uid` INT( 16 ) NOT NULL;
ALTER TABLE `filemanagement` CHANGE `uid` `uid` INT( 16 ) NOT NULL ;
ALTER TABLE `cashedgeitem` CHANGE `uid` `uid` INT( 16 ) NOT NULL ;
ALTER TABLE `cashedgeaccount` CHANGE `uid` `uid` INT( 16 ) NOT NULL ;
ALTER TABLE `networth` CHANGE `uid` `uid` INT( 16 ) NULL DEFAULT NULL;
ALTER TABLE `expense` CHANGE `uid` `uid` INT( 16 ) NOT NULL ;
ALTER TABLE `miscellaneous` CHANGE `uid` `uid` INT( 16 ) NOT NULL;
ALTER TABLE `learning` CHANGE `uid` `uid` INT( 16 ) NOT NULL COMMENT 'The User ID of the user';
ALTER TABLE `celog` CHANGE `uid` `uid` INT( 16 ) NOT NULL COMMENT 'The UID of the user';
ALTER TABLE `notification` CHANGE `uid` `uid` INT( 16 ) NOT NULL ;
ALTER TABLE `userpersonalinfo` CHANGE `uid` `uid` INT( 16 ) NOT NULL ;
ALTER TABLE `userscore` CHANGE `user_id` `user_id` INT( 16 ) NOT NULL;
ALTER TABLE `consumervsadvisor` CHANGE `userid` `userid` INT( 16 ) NOT NULL;
ALTER TABLE `feedback` CHANGE `id` `id` INT( 16 ) NOT NULL;
ALTER TABLE `logdb` CHANGE `uid` `uid` INT( 16 ) NOT NULL;
ALTER TABLE `profileactionsteps` CHANGE `uid` `uid` INT( 16 ) NOT NULL;
ALTER TABLE `user` CHANGE `uid` `uid` INT( 16 ) NOT NULL AUTO_INCREMENT;



# 12/02/2013  changed to differential first downloaded and lastmodified time stamp for auto account in assets, debts, and insurance tables.
ALTER TABLE `assets` CHANGE `lastdownload` `createdtimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'downloaded timestamp for auto account';
ALTER TABLE `assets` ADD `modifiedtimestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `createdtimestamp`;
ALTER TABLE `assets` CHANGE `modifiedtimestamp` `modifiedtimestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'This get updated when Batch file update this account.';

ALTER TABLE `debts` CHANGE `lastdownload` `createdtimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'downloaded timestamp for auto account';
ALTER TABLE `debts` ADD `modifiedtimestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `createdtimestamp`;
ALTER TABLE `debts` CHANGE `modifiedtimestamp` `modifiedtimestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'This get updated when Batch file update this account.';

ALTER TABLE `insurance` CHANGE `lastdownload` `createdtimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'downloaded timestamp for auto account';
ALTER TABLE `insurance` ADD `modifiedtimestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `createdtimestamp`;
ALTER TABLE `insurance` CHANGE `modifiedtimestamp` `modifiedtimestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'This get updated when Batch file update this account.';

# 11/14/13 Increase size of the accountpending field to resolve offset error.
ALTER TABLE `cashedgeitem` MODIFY `accountpending` LONGTEXT;
# Created and modified timestamp for Cashedgeitem row:
ALTER TABLE cashedgeitem ADD modified timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

# 12/06/2013
ALTER TABLE `notification` ADD `batchflag` TINYINT( 1 ) NOT NULL DEFAULT '0' COMMENT '0 - Onfly Notification 1 - Batch File Notification' AFTER `created`;



DROP TABLE `notification`;


CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The row ID',
  `uid` int(11) NOT NULL COMMENT 'The user ID of the user',
  `refid` int(11) NOT NULL COMMENT 'The FILOGINACCT ID',
  `rowid` int(11) NOT NULL COMMENT 'This wil be row ID from CashEdge Item table, and will keep changing if the row is deleted',
  `info` varchar(100) NOT NULL COMMENT 'This will contain the FI Name, and FI id',
  `msg` varchar(100) NOT NULL COMMENT 'The message that is displayed in the notification bar and this will be declared as public variables in CashEdge Controller',
  `context` varchar(25) NOT NULL COMMENT 'The context in which they are sent, they will also be declared as public variabled in CashEdge controller',
  `template` varchar(25) NOT NULL COMMENT 'The template that is needed to render the message',
  `stat` int(1) NOT NULL COMMENT 'Will be either 0 or 1 , based on the condition',
  `lastmodified` datetime NOT NULL COMMENT 'last modified timestamp ($timestamp = date("Y-m-d H:i:s")) through code',
  `batchcode` INT(10) NOT NULL COMMENT 'The error code from batchfile ',
  `batchmodified` datetime NOT NULL COMMENT 'The timestamp of the last batchcode update ($timestamp = date("Y-m-d H:i:s"))',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



# 12/30/2013 change currency amounts and percentages in the assets table to decimal data type, change zip code to varchar.
ALTER TABLE `assets` CHANGE `balance` `balance` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The balance amount that you have';
ALTER TABLE `assets` CHANGE `contribution` `contribution` DECIMAL(16,4) NOT NULL COMMENT 'The amount contributed by you in the case of this asset type';
ALTER TABLE `assets` CHANGE `withdrawal` `withdrawal` DECIMAL(16,4) NOT NULL COMMENT 'The monthly withdrawal amount for this asset type';
ALTER TABLE `assets` CHANGE `netincome` `netincome` DECIMAL(16,4) NOT NULL COMMENT 'The net income for the PROPERTY asset type';
ALTER TABLE `assets` CHANGE `loan` `loan` DECIMAL(16,4) NOT NULL COMMENT 'The loan on the asset if any on the PROPERTY or VEHICLE section';

ALTER TABLE `assets` CHANGE `zipcode` `zipcode` VARCHAR(15) NOT NULL COMMENT 'The zipcode of the address of the user';
ALTER TABLE `assets` CHANGE `growthrate` `growthrate` DECIMAL(8,4) NOT NULL COMMENT 'The growth rate in percentage per year';
ALTER TABLE `assets` CHANGE `empcontribution` `empcontribution` DECIMAL(8,4) NOT NULL COMMENT 'The percentage amount contributed by the employer for this asset type';

# 12/30/2013 change currency amounts and percentages in the debts table to decimal data type.
ALTER TABLE `debts` CHANGE `balowed` `balowed` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The balance amount owed totally';
ALTER TABLE `debts` CHANGE `amtpermonth` `amtpermonth` DECIMAL(16,4) NOT NULL COMMENT 'Amount payable per month';
ALTER TABLE `debts` CHANGE `apr` `apr` DECIMAL(8,4) NOT NULL COMMENT 'It is the interest rate of the debt per month';

# 12/30/2013 change currency amounts and percentages in the insurance table to decimal data type.
ALTER TABLE `insurance` CHANGE `annualpremium` `annualpremium` DECIMAL(16,4) NOT NULL COMMENT 'This is the annual premium amount paid by the user';
ALTER TABLE `insurance` CHANGE `cashvalue` `cashvalue` DECIMAL(16,4) NOT NULL COMMENT 'is the cash-value of the policy';
ALTER TABLE `insurance` CHANGE `dailybenfitamt` `dailybenfitamt` DECIMAL(16,4) NOT NULL COMMENT 'The daily benefit amount related to Long term insurance';
ALTER TABLE `insurance` CHANGE `coverageamt` `coverageamt` DECIMAL(16,4) NOT NULL COMMENT 'This is the insurance coverage amount on your annual income / percentage';
ALTER TABLE `insurance` CHANGE `amtupondeath` `amtupondeath` DECIMAL(16,4) NOT NULL COMMENT 'This is the amount that you get upon your death from the insurance that you have : Life insurance only';
ALTER TABLE `insurance` CHANGE `deductible` `deductible` DECIMAL(16,4) NOT NULL COMMENT 'This is the deductible in the Vehicle insurance';
ALTER TABLE `insurance` CHANGE `dailyamtindexed` `dailyamtindexed` DECIMAL(16,4) NOT NULL COMMENT 'Daily amt indexed for inflation T/F : Long Term';

# ***** Until this pushed to production on 1.3.2014 ************

ALTER TABLE `cashedgeitem` ADD `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-active, 1-deleted' AFTER `lsstatus`;

ALTER TABLE `cashedgeitem` CHANGE `lsstatus` `lsstatus` smallint NOT NULL DEFAULT '0';

/* 01/27/2014   */
ALTER TABLE `userscore` CHANGE `timestamp` `timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

ALTER TABLE `assets` CHANGE `modifiedtimestamp` `modifiedtimestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'This get updated when record will be updated.';
ALTER TABLE `debts` CHANGE `modifiedtimestamp` `modifiedtimestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'This get updated when record will be updated.';
ALTER TABLE `insurance` CHANGE `modifiedtimestamp` `modifiedtimestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'This get updated when record will be updated.';
ALTER TABLE `income` CHANGE `modified_on` `modified_on` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The last modified time of this row';
ALTER TABLE `expense` ADD `modifiedtimestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `miscellaneous` ADD `createdtimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
ADD `modifiedtimestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `userpersonalinfo` CHANGE `timestamp` `timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;

ALTER TABLE `goal` ADD `modifiedtimestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `goal` CHANGE `monthlyincome` `monthlyincome` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The monthly income that you have';
ALTER TABLE `goal` CHANGE `goalamount` `goalamount` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The goal amount that you have';
ALTER TABLE `goal` CHANGE `permonth` `permonth` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The permonth amount that you have';
ALTER TABLE `goal` CHANGE `saved` `saved` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The amount saved for the goal';
ALTER TABLE `goal` CHANGE `downpayment` `downpayment` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The downpayment saved for the goal';

/* 05-Feb-2014 By Rajeev Ranjan: Table structure for create new table `scorechange` to store  scorechange data separately */

    CREATE TABLE IF NOT EXISTS `scorechange` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `uid` int(11) NOT NULL,
      `scorechange` text NOT NULL COMMENT 'score change for 180 days',
      `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/* 05-Feb-2014 By Rajeev Ranjan: Sql statment for removing scorechange column from userscore table to new scorechange table after migrating the data only */

ALTER TABLE `userscore` DROP `scorechange` ;

/* 02/14/2014   To fix the issue with learning center */
UPDATE actionstep SET flexi1 = REPLACE(flexi1, 'https://staging.', 'https://www.') WHERE flexi1 LIKE '%https://staging.%' AND  actionsteps LIKE '%Recom%';

/* 24-Feb-2014 By Rajeev Ranjan*/

/* Assets table - modifiedtimestamp field get updated when column is updated by user + ce + batchfiles.*/

CREATE TRIGGER `update_assets_trigger` BEFORE UPDATE ON `assets` FOR EACH ROW SET NEW.`modifiedtimestamp` = NOW();

/* Debts table - modifiedtimestamp field get updated when column is updated by user + ce + batchfiles.*/

CREATE TRIGGER `update_debts_trigger` BEFORE UPDATE ON `debts` FOR EACH ROW SET NEW.`modifiedtimestamp` = NOW();

/*Insurance table - modifiedtimestamp field get updated when column is updated by user + ce + batchfiles.*/

CREATE TRIGGER `update_insurance_trigger` BEFORE UPDATE ON `insurance` FOR EACH ROW SET NEW.`modifiedtimestamp` = NOW();
# ***** Until this pushed to production on 3.3.2014 ************

/* 03/05/14 add index to user_id column in userscore table */
ALTER TABLE `userscore` ADD INDEX `user_id` (`user_id`);

/* 03/05/14 update status descriptions in assets, debts, and insurance tables */
ALTER TABLE `assets` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0-active, 1-deleted, 2-hidden';
ALTER TABLE `debts` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0-active, 1-deleted, 2-hidden';
ALTER TABLE `insurance` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0-active, 1-deleted, 2-hidden';

/* 03/05/14 removing IRA action step because of new steps that we added */
delete from actionstep where actionid=12;

/* 03/10/14 -- FOR PRODUCTION ONLY -- add modifiedtimestamp to goal table */
ALTER TABLE `goal` ADD `modifiedtimestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `monthlyincome`;
# ***** Until this pushed to production on 3.28.2014 ************

# ***** From this pushed to production on 5.09.2014 ************
/* 03/05/14 add index to user_id column in userscore table */
ALTER TABLE `userscore` ADD INDEX `user_id` (`user_id`);

/* 03/05/14 update status descriptions in assets, debts, and insurance tables */
ALTER TABLE `assets` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0-active, 1-deleted, 2-hidden';
ALTER TABLE `debts` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0-active, 1-deleted, 2-hidden';
ALTER TABLE `insurance` CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0-active, 1-deleted, 2-hidden';

/* 03/05/14 removing IRA action step because of new steps that we added */
delete from actionstep where actionid=12;

/* 03/10/14 -- FOR PRODUCTION ONLY -- add modifiedtimestamp to goal table */
ALTER TABLE `goal` ADD `modifiedtimestamp` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `monthlyincome`;

/*for advisor help notification status*/

ALTER TABLE `actionstep` ADD `advisorhelpstatus` ENUM( '0', '1' ) NOT NULL DEFAULT '0';

ALTER TABLE `actionstep` CHANGE `advisorhelpstatus` `advisorhelpstatus` ENUM( '0', '1', '2' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 = New, 1 = Advisor Notified, 2 = Sent mail to Advisor';

/* 04162014 To fix  Action step advisor Help Mail */
ALTER TABLE `emailmaster` CHANGE COLUMN `toaddress` `toaddress` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL  ;

/* Chetu Team */

/* March-15-2014 Changes*/

/*  Create Query for advisor */
CREATE TABLE IF NOT EXISTS `advisor` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `roleid` int(4) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `city` varchar(15) NOT NULL,
  `state` varchar(15) NOT NULL,
  `zip` int(6) NOT NULL,
  `isactive` enum('0','1','2') NOT NULL COMMENT '0->Inactive, 1-Active and 2-Disabled',
  `restpasswordtokenkey` varchar(50) NOT NULL,
  `createdby` varchar(225) NOT NULL,
  `verificationcode` varchar(50) NOT NULL,
  `createdtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastaccesstimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requestinvitetokenkey` varchar(225) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `adv_designations` (
  `advid` int(10) NOT NULL,
  `desig_name` varchar(250) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `createdat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `adv_desig_id` int(10) NOT NULL AUTO_INCREMENT,
  `other` int(5) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`adv_desig_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

CREATE TABLE IF NOT EXISTS `advisorstates` (
  `advid` int(10) NOT NULL,
  `stateregistered` int(10) NOT NULL,
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `productandservice` (
  `advid` int(10) NOT NULL,
  `productserviceid` varchar(250) NOT NULL,
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `other` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `adminadvisors` (
  `advisor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


/* March-15-2014 Changes ends */
/* 03/24/14 Create a separate notifications table for advisors */
CREATE TABLE `advisornotification` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'The row ID',
  `uid` INT(11) NOT NULL COMMENT 'The user ID of the user',
  `refid` INT(11) NOT NULL COMMENT 'The FILOGINACCT ID',
  `rowid` INT(11) NOT NULL COMMENT 'This wil be row ID from CashEdge Item table, and will keep changing if the row is deleted',
  `info` VARCHAR(100) NOT NULL COMMENT 'This will contain the FI Name, and FI id',
  `msg` VARCHAR(100) NOT NULL COMMENT 'The message that is displayed in the notification bar and this will be declared as public variables in CashEdge Controller',
  `context` VARCHAR(25) NOT NULL COMMENT 'The context in which they are sent, they will also be declared as public variabled in CashEdge controller',
  `template` VARCHAR(25) NOT NULL COMMENT 'The template that is needed to render the message',
  `stat` INT(1) NOT NULL COMMENT 'Will be either 0 or 1 , based on the condition',
  `lastmodified` DATETIME NOT NULL COMMENT 'last modified timestamp ($timestamp = date("Y-m-d H:i:s")) through code',
  `batchcode` INT(10) NOT NULL COMMENT 'The error code from batchfile ',
  `batchmodified` DATETIME NOT NULL COMMENT 'The timestamp of the last batchcode update ($timestamp = date("Y-m-d H:i:s"))',
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=128 DEFAULT CHARSET=latin1


/* 04/08/14 Create a subscriptions table to track subscriptions for advisors */
CREATE TABLE `advisorsubscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The row ID',
  `advid` int(11) NOT NULL COMMENT 'The advisor UID from the advisor table',
  `stripecustomerid` varchar(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `stripeplanid` varchar(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `planname` varchar(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `startdate` datetime NOT NULL COMMENT 'date subscription begins',
  `enddate` datetime NOT NULL COMMENT 'date subscription ends',
  `amountpaid` decimal(20,2) DEFAULT NULL,
  `created` datetime NOT NULL COMMENT 'last modified timestamp ($timestamp = date("Y-m-d H:i:s")) through code',
  `lastmodified` datetime NOT NULL COMMENT 'last modified timestamp ($timestamp = date("Y-m-d H:i:s")) through code',
  PRIMARY KEY (`id`),
  KEY `advid` (`advid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1

DROP TABLE `consumervsadvisor`;
CREATE TABLE IF NOT EXISTS `consumervsadvisor` (
  `userid` int(16) NOT NULL,
  `advid` int(10) NOT NULL,
  `permission` enum('RO','RW','N') COLLATE utf8_unicode_ci NOT NULL,
  `dateconnect` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-pending 1-accept',
  `indemnification_check` int(3) NOT NULL DEFAULT '0',
  `phone` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-pending 1-accept',
  `topic` int(1) NOT NULL,
  `mode` int(1) NOT NULL,
  `message` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`userid`,`advid`),
  KEY `advid` (`advid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE `advisorpersonalinfo`;
CREATE TABLE IF NOT EXISTS `advisorpersonalinfo` (
  `advid` int(10) NOT NULL,
  `firstname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `advisortype` enum('Broker','Advisor','Both') COLLATE utf8_unicode_ci NOT NULL,
  `firmname` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `designation` text COLLATE utf8_unicode_ci NOT NULL,
  `areaofspez` text COLLATE utf8_unicode_ci NOT NULL,
  `stateregistered` text COLLATE utf8_unicode_ci NOT NULL,
  `avgacntbalanceperclnt` decimal(20,2) NOT NULL,
  `minasstsforpersclient` decimal(20,2) NOT NULL,
  `typeofcharge` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flexi1` text COLLATE utf8_unicode_ci NOT NULL,
  `flexi2` text COLLATE utf8_unicode_ci NOT NULL,
  `flexi3` text COLLATE utf8_unicode_ci NOT NULL,
  `profilepic` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `individualcrd` int(11) DEFAULT NULL,
  PRIMARY KEY (`advid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


/* March-21-2014 Changes*/

ALTER TABLE `user` ADD `phone` INT( 10 );

/* March-24-2014 Changes*/

ALTER TABLE `advisor` ADD UNIQUE (`email`);



/*chetu Team*/
/* profile completeness - 04/04/2014 */
ALTER TABLE `userpersonalinfo`
ADD `connectAccountPreference` ENUM( '0', '1' ) NOT NULL DEFAULT '0',
ADD `debtsPreference` ENUM( '0', '1' ) NOT NULL DEFAULT '0',
ADD `insurancePreference` ENUM( '0', '1' ) NOT NULL DEFAULT '0';


/* 04/24/14 Add processor and status fields to advisorsubcscription table to track subscriptions. */
ALTER TABLE `advisorsubscription` ADD `processor` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Payment processor, currently stripe or flexscore';
ALTER TABLE `advisorsubscription` ADD `delinquent` tinyint(1) NOT NULL DEFAULT '0', COMMENT 'Used for access to advisor system';

/* 04/27/14 Add verified by admin property. */
ALTER TABLE `advisor` ADD `verified` tinyint(1) NOT NULL DEFAULT '0', COMMENT 'Used to check if admin has verified this advisor';


/* 04/30/14 Increasing size. */
ALTER TABLE `consumervsadvisor` CHANGE `message` `message` VARCHAR(650) COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE `consumervsadvisor` CHANGE `phone` `phone` VARCHAR(55) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `consumervsadvisor` CHANGE `topic` `topic` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `consumervsadvisor` ADD `email` VARCHAR(100) NOT NULL AFTER `phone`;
# ***** Until this pushed to production on 5.09.2014 ************

# ***** From  this pushed to production on 05.26.2014 ************
/* 05/12/14 Revisions to advisor subscription table. */
ALTER TABLE advisorsubscription DROP amountpaid;
ALTER TABLE advisorsubscription DROP created;
ALTER TABLE advisorsubscription DROP lastmodified;
ALTER TABLE advisorsubscription DROP stripeplanid;
ALTER TABLE advisorsubscription CHANGE `startdate` `startdate` DATETIME COMMENT 'date most recent subscription began';
ALTER TABLE advisorsubscription CHANGE `enddate` `enddate` DATETIME COMMENT 'date most recent subscription ended, active subscriptions are null';
ALTER TABLE advisorsubscription CHANGE `delinquent` `delinquent` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '0-false, 1-true';
ALTER TABLE advisorsubscription ADD `stripesubscriptionid` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL AFTER stripecustomerid;
ALTER TABLE advisorsubscription ADD `stripecardid` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL AFTER stripesubscriptionid;
UPDATE advisorsubscription SET enddate = NULL;  /* this should only be run once */

/* 05/13/14 */
ALTER TABLE `advisor` CHANGE `uid` `advid` INT( 10 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `advisornotification` CHANGE `uid` `advid` INT( 11 ) NOT NULL COMMENT 'The Advisor ID of the user';
ALTER TABLE `adminadvisors` CHANGE `advisor_id` `advid` INT( 11 ) NOT NULL COMMENT 'Assign Advisor to Consumer';
# ***** Until this pushed to production on 05.26.2014 ************

# ***** Until this pushed to production on 06.15.2014 ************

ALTER TABLE `debts` ADD `monthly_payoff_balances` BOOLEAN NOT NULL DEFAULT FALSE ;

/* advisor recommendation for as table scheme */

CREATE TABLE IF NOT EXISTS `advisorasrecommendation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advid` int(11) NOT NULL,
  `actionid` int(11) NOT NULL,
  `description` text NOT NULL,
  `createdtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastaccesstimestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
);

/* Everything before this has been run on production ON June 15 2014 */
# ***** Until this pushed to production on 06.15.2014 ************

# ***** Until this pushed to production on 06.23.2014 ************

/* add monte carlo flag in userscore table */
ALTER TABLE `userscore` ADD `montecarlo` BOOLEAN NOT NULL DEFAULT FALSE;

/* add monte carlo flag in userscore table */
ALTER TABLE `userscore` DROP `montecarlo`;

/* add table to track monte carlo requests */
CREATE TABLE `montecarlouser` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`uid` INT(16) DEFAULT NULL,
`createdtimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'The time of the first MC request',
`modifiedtimestamp` TIMESTAMP DEFAULT '0000-00-00 00:00:00' COMMENT 'Latest MC request',
`lastruntimestamp` TIMESTAMP DEFAULT '0000-00-00 00:00:00' COMMENT 'Last time MC ran for the user',
`failedruns` INT(4) DEFAULT 0 COMMENT 'Counts consecutive failed runs',
PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

ALTER TABLE `goal` ADD `goalassumptions_1` VARCHAR( 10 ) NULL ;
ALTER TABLE `goal` ADD `goalassumptions_2` VARCHAR( 50 ) NULL ;
ALTER TABLE `goal` ADD `contributions` decimal(16,4) NOT NULL DEFAULT '0.0000';
ALTER TABLE `goal` ADD `minimumContributions` decimal(16,4) NOT NULL DEFAULT '0.0000';
ALTER TABLE `goal` ADD `status` VARCHAR( 50 ) NULL ;

update goal set goalassumptions_1 = '3.4' where goaltype not in ('DEBT', 'COLLEGE');
update goal set goalassumptions_1 = '5.8' where goaltype = 'COLLEGE';
update goal set goalassumptions_1 = '2' where goaltype  = 'DEBT';
update goal set goalassumptions_2 = '71' where goaltype  = 'DEBT';
# ***** Until this pushed to production on 06.23.2014 ************

# ***** Until this pushed to production on 07.02.2014 ************

ALTER TABLE `user` ADD `verified` TINYINT NULL DEFAULT '0';


CREATE TABLE `usersecurityanswer` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `uid` int(16) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `createdtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modifiedtimestamp` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*pin code column in user table*/
ALTER TABLE `user` ADD `pin` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `password` ;
# ***** Until this pushed to production on 07.02.2014 ************

# ***** Until this pushed to production on 07.07.2014 ************
ALTER TABLE `user` ADD `verified` TINYINT NULL DEFAULT '0';


CREATE TABLE `usersecurityanswer` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `uid` int(16) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `createdtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `modifiedtimestamp` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*pin code column in user table*/
ALTER TABLE `user` ADD `pin` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `password` ;

update user set verificationcode=md5(concat(email,roleid));

-- July 2, 2014 3:00 PM PST [Daphne]
-- Swaps out Life Insurance owner and beneficiary with numeric values
UPDATE `insurance` SET `insurancefor` = 80
WHERE `type` = 'LIFE' AND `insurancefor` LIKE '%self%'
COLLATE utf8_general_ci;

UPDATE `insurance` SET `beneficiary` = 80
WHERE `type` = 'LIFE' AND `beneficiary` LIKE '%self%'
COLLATE utf8_general_ci;

UPDATE `insurance` SET `insurancefor` = 81
WHERE `type` = 'LIFE' AND `insurancefor` LIKE '%spouse%'
COLLATE utf8_general_ci;

UPDATE `insurance` SET `beneficiary` = 81
WHERE `type` = 'LIFE' AND `beneficiary` LIKE '%spouse%'
COLLATE utf8_general_ci;

UPDATE `insurance` SET `insurancefor` = 83
WHERE `type` = 'LIFE' AND `insurancefor` LIKE '%joint%'
COLLATE utf8_general_ci;

UPDATE `insurance` SET `beneficiary` = 83
WHERE `type` = 'LIFE' AND `beneficiary` LIKE '%joint%'
COLLATE utf8_general_ci;

update insurance set beneficiary = 81 where beneficiary = '';
update insurance set insurancefor = 80 where insurancefor = '';
# ***** Until this pushed to production on 07.07.2014 ************


DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oauth_authorization_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) NOT NULL,
  `redirect_uri` varchar(2000) NOT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(100) DEFAULT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oauth_jwt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `oauth_scopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oauth_scopes` (
  `scope` text,
  `is_default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- APPENDING AND NOT REPLACING, PER DAN'S INSTRUCTIONS
-- DD/2014-07-11

LOCK TABLES `oauth_clients` WRITE;
INSERT INTO `oauth_clients` VALUES ('flexscore_mobile','a07042b4ce11e95d19977c354bebb1abdc036cba','http://fake/',NULL,NULL,NULL);
UNLOCK TABLES;

/* July 15, 2014 Change dailyamtindexed field to tiny int boolean. */
ALTER TABLE  `insurance` CHANGE  `dailyamtindexed` `dailyamtindexed` TINYINT(1) NOT NULL COMMENT 'Daily amt indexed for inflation T/F : Long Term';

/* July 21, 2014 Change name of msg and stat fields in notification and advisornotification tables. */
ALTER TABLE `notification` CHANGE  `msg` `message` VARCHAR(100) NOT NULL COMMENT 'The message description that is displayed in the notification bar.';
ALTER TABLE `notification` CHANGE  `stat` `status` INT(1) NOT NULL COMMENT 'Will be either 0 or 1 , based on the condition';
ALTER TABLE `advisornotification` CHANGE  `msg` `message` VARCHAR(100) NOT NULL COMMENT 'The message description that is displayed in the notification bar.';
ALTER TABLE `advisornotification` CHANGE  `stat` `status` INT(1) NOT NULL COMMENT 'Will be either 0 or 1 , based on the condition';

/* add video12 column in learning table*/
ALTER TABLE `learning` ADD `vid12` TINYINT( 4 ) NOT NULL DEFAULT '0' COMMENT 'Estate Planning';

/* July 23, 2014 fix beneficiary misspelling*/
ALTER TABLE `assets` CHANGE  `benefitiary` `beneficiary` TINYINT(1) NOT NULL COMMENT 'Beneficiaries Listed and Up to Date: Yes-1, No-0, Not Sure-2';

/* July 25, 2014 implement new advisor subscription plans */
ALTER TABLE `advisorsubscription` DROP delinquent;
ALTER TABLE `advisorsubscription` ADD  `cardexpirationdate` DATETIME DEFAULT NULL;
ALTER TABLE `advisorsubscription` ADD `currentperiodstart` DATETIME DEFAULT NULL COMMENT 'subscription current period start date';
ALTER TABLE `advisorsubscription` ADD `currentperiodend` DATETIME DEFAULT NULL COMMENT 'subscription current period end date';
ALTER TABLE `advisorsubscription` ADD `stripestatus` VARCHAR(55) DEFAULT NULL COMMENT 'Status returned from Stripe.';
ALTER TABLE `advisorsubscription` ADD `modifiedtimestamp` TIMESTAMP NULL DEFAULT NULL;
ALTER TABLE `advisorsubscription` CHANGE `startdate` `subscriptionstart` DATETIME DEFAULT NULL COMMENT 'overall subscription start date';
ALTER TABLE `advisorsubscription` CHANGE `enddate` `subscriptionend` DATETIME DEFAULT NULL COMMENT 'overall subscription end date';

/* July 25, 2014 data changes for existing subscribers */
UPDATE `advisorsubscription` SET `currentperiodstart` = `subscriptionstart`;
UPDATE `advisorsubscription` SET `currentperiodend` = '2014-08-01 23:59:59', `stripestatus` = 'trialing' WHERE `processor` = 'Stripe' AND `subscriptionend` IS NULL;
UPDATE `advisorsubscription` SET `currentperiodend` = `subscriptionend`, `stripestatus` = 'canceled' WHERE `processor` = 'Stripe' AND `subscriptionend` IS NOT NULL;

-- [DD]: New device table for mobile devices.
CREATE TABLE `device` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(45) NOT NULL COMMENT 'User logged in to device',
  `token` varchar(45) NOT NULL COMMENT 'Device token for Urban Airship',
  `os` varchar(45) NOT NULL COMMENT 'Device OS',
  PRIMARY KEY (`id`)
);

-- [DD]: Adding is_unsubscribed to user table
ALTER TABLE `user` ADD COLUMN `is_unsubscribed` TINYINT(4) NULL COMMENT 'This is set to 1 when a user unsubscribes to our emails and should prevent us from emailing opted-out users.' AFTER `verified`;

/* Aug 06 - This is going to be a temporary column just to run the one time script to change the ScoreObj Array.*/
ALTER TABLE `scorechange` ADD `patchstatus` INT(1) NOT NULL;
DELETE FROM scorechange WHERE uid = 0;

/*Aug 08 - Change userpic field in the userpersonalinfo table from blob to varchar*/
ALTER TABLE `userpersonalinfo` CHANGE `userpic` `userpic` VARCHAR( 255 ) NULL DEFAULT NULL;

/* DT 08-13-14 Rerun insurance scripts to fix remaining records */
UPDATE `insurance` SET `insurancefor` = 81
WHERE `type` = 'LIFE' AND `insurancefor` LIKE '%spouse%';

UPDATE `insurance` SET `beneficiary` = 81
WHERE `type` = 'LIFE' AND `beneficiary` LIKE '%spouse%';

UPDATE `insurance` SET `insurancefor` = 80
WHERE `type` = 'LIFE' AND `insurancefor` LIKE '%self%';

update insurance set beneficiary = '' where beneficiary = 81 and `type` <> 'LIFE';
update insurance set insurancefor = '' where insurancefor = 80 and `type` <> 'LIFE';

update insurance set beneficiary = 81 where beneficiary = '' and `type` = 'LIFE';
update insurance set insurancefor = 80 where insurancefor = '' and `type` = 'LIFE';

ALTER TABLE advisorsubscription ADD `cardexpiredemail` datetime DEFAULT NULL COMMENT "Indicates when most recent email re expiring credit card was sent.";
ALTER TABLE advisorsubscription ADD `cardnotauthorizedemail` datetime DEFAULT NULL COMMENT "Indicates when most recent email re unauthorized credit card was sent.";

/* Ran 1-3 scripts below on production August 17, 2014 to update scorechange table */
/* 1. add patchstatus column */
ALTER TABLE `scorechange` ADD `patchstatus` INT(1) NOT NULL ;
delete from scorechange where uid = 0;
delete from userscore where user_id = 0;

/* 2. add index and clean up data in scorechange table */
create table tmp like scorechange;
alter table tmp add unique (UID);
insert into tmp SELECT max(id), uid, scorechange, timestamp, patchstatus FROM scorechange WHERE patchstatus = 0 group by uid;
rename table scorechange to deleteme, tmp to scorechange;
drop table deleteme;

/* 3. ran scorechange script to minimize data in scorechange table */
/* php scripts/scorechange/scorechangepatch.php  */

ALTER TABLE `device` MODIFY `token` VARCHAR(255);


/* 21-08-2014 - Specific Products AS for Users */
CREATE TABLE IF NOT EXISTS `adminasrecommendation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `action_id` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `createdtimestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
);

/* 25-08-2014 - Add column to adminasrecommendation */
ALTER TABLE `adminasrecommendation` ADD `product_link` varchar(255) NOT NULL;

/* August 25, 2014 DT Add columns to enable Settings forms to pull from db instead of Stripe */
ALTER TABLE `advisorsubscription` ADD `cardlast4` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Credit card last four digits' AFTER cardexpirationdate;
ALTER TABLE `advisorsubscription` ADD `cardtype` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Credit card type (Visa, Amex, etc.)' AFTER cardlast4;


/* August 28th 2014 - added field for storing advisor client upload PDF report filename */
ALTER TABLE `advisornotification` ADD `file` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;

/* August 29th 2014 - Added by Melroy */
update advisorsubscription set processor = 'FlexScore' where processor = 'Flexscore';

/* September 1st 2014 */
CREATE TABLE `echouser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `permission` enum('0','1','2') DEFAULT NULL COMMENT '0 = not answered, 1 = accepted, or 2 = declined',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* 08-Sep-2014 - Replace strings with otlt ids in insurance table "insurancefor" field. */
UPDATE insurance SET insurancefor = '88' WHERE insurancefor = 'Comprehensive';
UPDATE insurance SET insurancefor = '89' WHERE insurancefor = 'Limited';

/* 17-Sep-2014 - added status field and removing transactiontype field in advisorsubscriptioninvoice table */
DROP TABLE IF EXISTS `advisorsubscriptioninvoice`;
CREATE TABLE IF NOT EXISTS `advisorsubscriptioninvoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `advid` int(11) NOT NULL,
  `flexscoreinvoicenumber` varchar(55) NOT NULL,
  `stripeinvoicenumber` varchar(55) NOT NULL,
  `status` varchar(10) NOT NULL,
  `invoicedate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/* 17-Sep-2014 - add field for future income and monte carlo */
ALTER TABLE `userscore` ADD `montecarloprobability` decimal(8,4) NOT NULL DEFAULT '0.0000' AFTER `totalscore`;
ALTER TABLE `userscore` ADD `futureincome` decimal(16,4) NOT NULL DEFAULT '0.0000' AFTER `montecarloprobability`;

/* add a field that records a timestamp for the last failed run */
ALTER TABLE `montecarlouser` ADD `lastfailedtimestamp` TIMESTAMP DEFAULT '0000-00-00 00:00:00' COMMENT 'Time of last failed run';



/*************************** Production Push October 26, 2014  START ********************/

/* 14/10/2014 - ACTION STEP 48 TAX PLANNING - ADD vid13 IN LEARNING TABLE */
ALTER TABLE `learning` ADD `vid13` TINYINT( 4 )  NOT NULL DEFAULT '0' COMMENT 'Tax Planning Video';


ALTER TABLE actionstep DROP COLUMN flexi4;
DROP TABLE learning;


/* 22/10/2014 - ADD flag column to show passwords updated to salted passwords */
ALTER TABLE `user` ADD `passwordupdated` tinyint(4) DEFAULT '0' NOT NULL AFTER `is_unsubscribed`;
ALTER TABLE `advisor` ADD `passwordupdated` tinyint(4) DEFAULT '0' NOT NULL AFTER `verified`;

/*************************** Production Push October 26, 2014  END ********************/



/***************************  Production Push November 09, 2014  START ********************/

/* 21/10/2014 - ADD COLUMN TO STORE HASH VALUE OF ADVID in ADVISOR TABLE */
ALTER TABLE `advisor` ADD `advidhashvalue` varchar( 255 );
/* 29/10/2014 - ADD COLUMN TO STORE HASH VALUE OF USERID in USER TABLE */
ALTER TABLE `user` ADD `uidhashvalue` VARCHAR( 255 );
ALTER TABLE `advisorpersonalinfo` CHANGE `profilepic` `profilepic` VARCHAR( 255 );


/* 31/10/2014 - Increase length of advisor notification file column due to storing salted file name */
ALTER TABLE `advisornotification` CHANGE `file` `file` VARCHAR( 255 );

/* 31/10/2014 - Add Sort column in assets / debts / insurance TABLE */
ALTER TABLE `assets` ADD `priority` INT( 2 ) NOT NULL ;
ALTER TABLE `debts` ADD `priority` INT( 2 ) NOT NULL ;
ALTER TABLE `insurance` ADD `priority` INT( 2 ) NOT NULL ;

/* 10/29/2014 - Added for Restrict number of password attempts */
CREATE TABLE IF NOT EXISTS `useraccess` (
  `id` int(16) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `attempt` tinyint(2) NOT NULL COMMENT '0=failed, 1=success',
  `accesstimestamp` datetime NOT NULL,
  `current` tinyint(2) NOT NULL COMMENT '0=false, 1=true',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `advisoraccess` (
  `id` int(16) unsigned NOT NULL AUTO_INCREMENT,
  `advisor_id` int(11) unsigned NOT NULL,
  `attempt` tinyint(2) NOT NULL COMMENT '0=failed, 1=success',
  `accesstimestamp` datetime NOT NULL,
  `current` tinyint(2) NOT NULL COMMENT '0=false, 1=true',
  PRIMARY KEY (`id`),
  KEY `advisor_id` (`advisor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;

/* 11/03/2014 - Added for saving breakdown changes */
CREATE TABLE IF NOT EXISTS `breakdownchange` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `breakdownchange` text NOT NULL,
  `timestamp` datetime,
  PRIMARY KEY (`id`)
);


/*************************** Production Push November 09, 2014  END ********************/


/***************************  Production Push November 17, 2014  START ********************/

/* 11/13/2014 - add field to track separate failed attempts for pins versus passwords. */
ALTER TABLE `useraccess` ADD `authentication` tinyint(2) DEFAULT '0' NOT NULL COMMENT '0-password, 1-pin' AFTER `attempt`;

/* 11/13/2014 - increase size of verificationcode fields and add verificationcode timestamps. */
ALTER TABLE `user` CHANGE  `verificationcode` `verificationcode` VARCHAR(100) NOT NULL;
ALTER TABLE `user` CHANGE  `restpasswordtokenkey` `resetpasswordcode` VARCHAR(100) NOT NULL;
ALTER TABLE `advisor` CHANGE  `restpasswordtokenkey` `resetpasswordcode` VARCHAR(100) NOT NULL;

ALTER TABLE `user` ADD `verificationexpiration` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'time that the verification code will expire' AFTER `verificationcode`;
ALTER TABLE `user` ADD `resetpasswordexpiration` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'time that the reset password code will expire' AFTER `resetpasswordcode`;
ALTER TABLE `advisor` ADD `resetpasswordexpiration` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'time that the reset password code will expire' AFTER `resetpasswordcode`;

DROP TABLE `breakdownchange`;

CREATE TABLE `breakdownchange` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `goal` int(11) DEFAULT NULL,
  `savings` int(11) DEFAULT NULL,
  `assets` int(11) DEFAULT NULL,
  `debts` int(11) DEFAULT NULL,
  `living` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*************************** Production Push November 17, 2014  END ********************/



/*************************** Production Push December 15, 2014 START ********************/

/*
 * 11/23/14 Change uid column in user table to id, change to user_id everywhere else
 */

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE feedback;

/* Drop the foreign key constraints*/
ALTER TABLE logdb DROP FOREIGN KEY logdb_ibfk_1;
ALTER TABLE profileactionsteps DROP FOREIGN KEY profileactionsteps_ibfk_1;


/* Change user table primary key */
ALTER TABLE  `user` CHANGE  `uid` `id` INT(11) NOT NULL AUTO_INCREMENT;

/* Change column names of related columns*/
ALTER TABLE  `actionstep` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `assets` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `breakdownchange` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `cashedgeaccount` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `cashedgeitem` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `celog` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `consumervsadvisor` CHANGE  `userid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `debts` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `device` CHANGE  `user_id` `user_id` INT(11) NOT NULL;
ALTER TABLE  `estimation` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `expense` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `filemanagement` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `goal` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `income` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `insurance` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `logdb` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `miscellaneous` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `montecarlouser` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `networth` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `notification` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `profileactionsteps` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `scorechange` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `userpersonalinfo` CHANGE  `uid` `user_id` INT(11) NOT NULL;
ALTER TABLE  `usersecurityanswer` CHANGE  `uid` `user_id` INT(11) NOT NULL;

/* Add back the foreign key constraints*/
ALTER TABLE logdb ADD CONSTRAINT logdb_ibfk_1 FOREIGN KEY (user_id) references user(id);
ALTER TABLE profileactionsteps ADD CONSTRAINT profileactionsteps_ibfk_1 FOREIGN KEY (user_id) references user(id);


ALTER TABLE `actionstep` DROP INDEX uid; CREATE UNIQUE INDEX `user_id` ON `actionstep`(`user_id`, `actionid`);
ALTER TABLE `assets` DROP INDEX uid; CREATE INDEX `user_id` ON `assets`(`user_id`);
CREATE INDEX `user_id` ON `breakdownchange`(`user_id`);
CREATE INDEX `user_id` ON `cashedgeaccount`(`user_id`);
CREATE INDEX `user_id` ON `celog`(`user_id`);
ALTER TABLE `debts` DROP INDEX uid; ALTER TABLE debts DROP INDEX uid_2; CREATE INDEX `user_id` ON `debts`(`user_id`);
ALTER TABLE `estimation` DROP INDEX uid; CREATE UNIQUE INDEX `user_id` ON `estimation`(`user_id`);
ALTER TABLE `goal` DROP INDEX uid; ALTER TABLE goal DROP INDEX uid_2; CREATE INDEX `user_id` ON `goal`(`user_id`);
ALTER TABLE `income` DROP INDEX uid; ALTER TABLE income DROP INDEX uid_2; CREATE INDEX `user_id` ON `income`(`user_id`);
ALTER TABLE `insurance` DROP INDEX id; ALTER TABLE insurance DROP INDEX uid; CREATE INDEX `user_id` ON `insurance`(`user_id`);
ALTER TABLE `miscellaneous` DROP INDEX uid; CREATE UNIQUE INDEX `user_id` ON `miscellaneous`(`user_id`);
CREATE UNIQUE INDEX `user_id` ON `scorechange`(`user_id`);
CREATE INDEX `user_id` ON `usersecurityanswer`(`user_id`);

SET FOREIGN_KEY_CHECKS = 1;




/*
 * 11/23/14 Change advid column in advisor table to id, change to advisor_id everywhere else
 */

SET FOREIGN_KEY_CHECKS = 0;

/* Change user table primary key */
ALTER TABLE  `advisor` CHANGE `advid` `id` INT(11) NOT NULL AUTO_INCREMENT;

/* Change column names of related columns*/
ALTER TABLE  `adminadvisors` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;
ALTER TABLE  `adv_designations` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;
ALTER TABLE  `advisorasrecommendation` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;
ALTER TABLE  `advisornotification` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;
ALTER TABLE  `advisorpersonalinfo` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;
ALTER TABLE  `advisorstates` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;
ALTER TABLE  `advisorsubscription` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;
ALTER TABLE  `advisorsubscriptioninvoice` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;
ALTER TABLE  `consumervsadvisor` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;
ALTER TABLE  `productandservice` CHANGE  `advid` `advisor_id` INT(11) NOT NULL;


CREATE INDEX `advisor_id` ON `adminadvisors`(`advisor_id`);
CREATE INDEX `advisor_id` ON `adv_designations`(`advisor_id`);
CREATE INDEX `advisor_id` ON `advisorasrecommendation`(`advisor_id`);
ALTER TABLE `advisorsubscription` DROP INDEX advid; CREATE INDEX `advisor_id` ON `advisorsubscription`(`advisor_id`);
ALTER TABLE `consumervsadvisor` DROP INDEX advid; CREATE INDEX `advisor_id` ON `consumervsadvisor`(`advisor_id`);
CREATE INDEX `advisor_id` ON `productandservice`(`advisor_id`);


SET FOREIGN_KEY_CHECKS = 1;



/*************************** Production Push December 15, 2014  END ********************/


/*************************** Production Patch January 22, 2015 START ********************/

/* 01/12/15 Add probability and futureincome fields to the montecarlouser table */
ALTER TABLE `montecarlouser` ADD `montecarloprobability` decimal(8,4) NOT NULL DEFAULT '0' AFTER `user_id`;
ALTER TABLE `montecarlouser` ADD `futureincome` decimal(16,4) NOT NULL DEFAULT '0' AFTER `montecarloprobability`;

/* 01/12/15 add product detail fields to advisorasrecommendation table */
ALTER TABLE `advisorasrecommendation` ADD `product_name` varchar(255) NOT NULL AFTER actionid;
ALTER TABLE `advisorasrecommendation` ADD `product_image` varchar(255) NOT NULL AFTER product_name;
ALTER TABLE `advisorasrecommendation` ADD `product_link` varchar(255) NOT NULL AFTER product_image;


/* 01/15/15 Removing probability and futureincome fields from the userscore table */
ALTER TABLE `userscore` DROP `montecarloprobability`;
ALTER TABLE `userscore` DROP `futureincome`;

/*************************** Production Patch January 22, 2015 END ********************/

/*************************** Production Push February 8, 2015 START ********************/

/* 01/29/15 Add unsubscribecode to use for unsubscribe authentication. Drop is_unsubscribed because it is not being used. */
ALTER TABLE `user` ADD `unsubscribecode` VARCHAR( 255 ) AFTER verified;
ALTER TABLE `user` DROP `is_unsubscribed`;

ALTER TABLE `advisor` ADD `unsubscribecode` VARCHAR( 255 ) AFTER verified;

/*************************** Production Push February 8, 2015 END ********************/

/*************************** Production Push March 30, 2015 START ********************/

ALTER TABLE `userscore` ADD `montecarloprobability` decimal(8,4) NOT NULL DEFAULT '0.0000' AFTER `totalscore`;

/*************************** Production Push March 30, 2015 END ********************/


/*************************** Production Push May 31, 2015 START ********************/

/* 2015-04-22  Add field in user table for tracking mailing list status of mailchimp users */
ALTER TABLE `user` ADD COLUMN `mailchimpstatus` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '0 - subscribed, 1 - unsubscribed, 2 - cleaned, 3 - deleted' AFTER `verified`;

/*************************** Production Push May 31, 2015 END ********************/
