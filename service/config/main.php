<?php

// Set paths
$root = realpath(dirname(__FILE__) . '/..');

$configRoot = dirname(__FILE__);
// Load params
$params = require($configRoot . '/params.php');
// Load local config
$configLocal = file_exists($configRoot . '/main-local.php') ? require($configRoot . '/main-local.php') : array();
// define a path alias
Yii::setPathOfAlias('root', $root);
if ($_SERVER['SERVER_NAME'] != 'flexscore.com') {
    defined('YII_DEBUG') or define('YII_DEBUG', true);
} else {
    defined('YII_DEBUG') or define('YII_DEBUG', false);
}
echo YII_DEBUG;die;
define('QUERY_CACHE_TIMEOUT', 900); // 900 seconds
//Yii::app()->getBaseUrl(true);
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return CMap::mergeArray(array(
            'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
            'name' => 'FlexScore',
            'params' => $params,
            // preloading 'log' component
            'preload' => array('log'),
            // autoloading model and component classes
            'import' => array(
                'application.models.*',
                'application.components.*',
                'application.helpers.*',
                'application.controllers.*',
                'application.helpers.*',
                'application.extensions.*',
            ),
            // application components
            'components' => array(
                'user' => array(
                    'class' => 'WebUser',
                    // enable cookie-based authentication
                    'allowAutoLogin' => true,
                #'authTimeout'=>180, //ONE MINUTE.
                ),
                'securityManager' => array(
                    'cryptAlgorithm' => 'flexscore', // or whatever
                    'encryptionKey' => '1234567890123456789012345678901234567890123456789012345678901234', // or whatever
                ),
                'urlManager' => array(
                    'urlFormat' => 'path',
                    'rules' => array(
/* Two slashes = Enable API. One slash = Disable API.

                        array(
                            'api/token',
                            'pattern' => 'api/v2/token',
                            'verb' => 'POST'
                        ),
                        array(
                            'api/login',
                            'pattern' => 'api/v2/login',
                            'verb' => 'POST'
                        ),
                        array(
                            'api/logout',
                            'pattern' => 'api/v2/login',
                            'verb' => 'DELETE'
                        ),
                        array(
                            'api/view',
                            'pattern' => 'api/v2/<model:\w+>/<id:[a-zA-Z0-9]+>',
                            'verb' => 'GET'
                        ),
                        array(
                            'api/list',
                            'pattern' => 'api/v2/<model:\w+>',
                            'verb' => 'GET'
                        ),
                        array(
                            'api/list',
                            'pattern' => 'api/v2/<model:\w+>/<id:[a-zA-Z0-9]+>/<submodel:\w+>',
                            'verb' => 'GET'
                        ),
                        array(
                            'api/update',
                            'pattern' => 'api/v2/<model:\w+>/<id:[a-zA-Z0-9]+>',
                            'verb' => 'PUT'
                        ),
                        array(
                            'api/delete',
                            'pattern' => 'api/v2/<model:\w+>/<id:[a-zA-Z0-9]+>',
                            'verb' => 'DELETE'
                        ),
                        array(
                            'api/create',
                            'pattern' => 'api/v2/<model:\w+>',
                            'verb' => 'POST'
                        ),
                        array(
                            'api/create',
                            'pattern' => 'api/v2/<model:\w+>/<id:[a-zA-Z0-9]+>/<submodel:\w+>',
                            'verb' => 'POST'
                        ),
*/
################################################################################
# API Version 3 Settings
################################################################################
/*                        array(
                            'apiNew/token',
                            'pattern' => 'api/v3/token',
                            'verb' => 'POST'
                        ),
                        array(
                            'apiNew/login',
                            'pattern' => 'api/v3/login',
                            'verb' => 'POST'
                        ),
                        array(
                            'apiNew/logout',
                            'pattern' => 'api/v3/login',
                            'verb' => 'DELETE'
                        ),
                        array(
                            'apiNew/view',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>',
                            'verb' => 'GET'
                        ),
                        array(
                            'apiNew/view',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>/<submodel:\w+>/<submodel_id:\d+>',
                            'verb' => 'GET'
                        ),
                        array(
                            'apiNew/list',
                            'pattern' => 'api/v3/<model:\w+>',
                            'verb' => 'GET'
                        ),
                        array(
                            'apiNew/list',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>/<submodel:\w+>',
                            'verb' => 'GET'
                        ),
                        array(
                            'apiNew/update',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>',
                            'verb' => 'PATCH'
                        ),
                        array(
                            'apiNew/update',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>/<submodel:\w+>/<submodel_id:\d+>',
                            'verb' => 'PATCH'
                        ),
                        array(
                            'apiNew/replace',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>',
                            'verb' => 'PUT'
                        ),
                        array(
                            'apiNew/replace',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>/<submodel:\w+>/<submodel_id:\d+>',
                            'verb' => 'PUT'
                        ),
                        array(
                            'apiNew/delete',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>',
                            'verb' => 'DELETE'
                        ),
                        array(
                            'apiNew/delete',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>/<submodel:\w+>/<submodel_id:\d+>',
                            'verb' => 'DELETE'
                        ),
                        array(
                            'apiNew/create',
                            'pattern' => 'api/v3/<model:\w+>',
                            'verb' => 'POST'
                        ),
                        array(
                            'apiNew/create',
                            'pattern' => 'api/v3/<model:\w+>/<model_id:\d+>/<submodel:\w+>',
                            'verb' => 'POST'
                        ),
################################################################################
# End of API Version 3 Settings
################################################################################
*/
                        // REST patterns
                        array('site/login', 'pattern' => 'api/userlogin', 'verb' => 'POST'),
                        array('site/loginbypin', 'pattern' => 'api/userloginbypin', 'verb' => 'POST'),
                        array('site/signup', 'pattern' => 'api/usersignup', 'verb' => 'POST'),
                        array('site/idemnificationcheck', 'pattern' => 'api/idemnificationcheck', 'verb' => 'GET'),
                        array('site/userverify', 'pattern' => 'api/userverify', 'verb' => 'GET'),
                        array('site/checklogin', 'pattern' => 'api/authcheck', 'verb' => 'GET'),
                        array('site/resetVerify', 'pattern' => 'api/resetverify', 'verb' => 'GET'),
                        array('site/resetVerifyAdvisor', 'pattern' => 'api/resetverifyadvisor', 'verb' => 'GET'),
                        array('site/logout', 'pattern' => 'api/userlogout', 'verb' => 'GET'),
                        array('site/adduserbyadv', 'pattern' => 'api/adduserbyadv', 'verb' => 'POST'),
                        array('site/resetpassword', 'pattern' => 'api/resetpassword', 'verb' => 'POST'),
                        array('site/updatepassword', 'pattern' => 'api/updatepassword', 'verb' => 'POST'),
                        array('site/advisorresetpassword', 'pattern' => 'api/advisorresetpassword', 'verb' => 'POST'),
                        array('site/advisorupdatepassword', 'pattern' => 'api/advisorupdatepassword', 'verb' => 'POST'),
                        array('site/deleteuseraccount', 'pattern' => 'api/deleteuseraccount', 'verb' => 'GET'),
                        // Role Based access
                        array('site/listroleactivities', 'pattern' => 'api/listroleactivities', 'verb' => 'GET'),
                        //Password Change
                        array('site/changepwd', 'pattern' => 'api/changepwd', 'verb' => 'POST'),
                        array('site/deactivateuser', 'pattern' => 'api/deactivateuser', 'verb' => 'POST'),
                        array('site/getcachedata', 'pattern' => 'api/getcachedata', 'verb' => 'GET'),
                        array('site/sendverificationemail', 'pattern' => 'api/sendverificationemail', 'verb' => 'POST'),
                        //user related
                        array('user/adduserinfoone', 'pattern' => 'api/adduserinfoone', 'verb' => 'POST'),
                        array('user/adduserinfotwo', 'pattern' => 'api/adduserinfotwo', 'verb' => 'POST'),
                        array('learningcenter/lcmain', 'pattern' => 'api/lcmain', 'verb' => 'GET'),
                        array('learningcenter/lcpost', 'pattern' => 'api/lcpost', 'verb' => 'POST'),
                        array('user/updateechouseragreement', 'pattern' => 'api/updateechouseragreement', 'verb' => 'POST'),
                        array('user/ResetMonteCarloFailedRuns', 'pattern' => 'api/resetmontecarlofailedruns', 'verb' => 'GET'),
                        array('learningcenter/lcpress', 'pattern' => 'api/lcpress', 'verb' => 'POST'),
                        array('learningcenter/lcglossary', 'pattern' => 'api/lcglossary', 'verb' => 'POST'),
                        array('learningcenter/lcpostsearch', 'pattern' => 'api/lcpostsearch', 'verb' => 'POST'),
                        array('learningcenter/lccatsearch', 'pattern' => 'api/lccatsearch', 'verb' => 'POST'),
                        array('learningcenter/lccatsearchname', 'pattern' => 'api/lccatsearchname', 'verb' => 'GET'),
                        array('learningcenter/getpress', 'pattern' => 'api/getpress', 'verb' => 'GET'),
                        array('learningcenter/lcpresscms', 'pattern' => 'api/lcpresscms', 'verb' => 'GET'),
                        array('learningcenter/blogsummary', 'pattern' => 'api/blogsummary', 'verb' => 'GET'),
                        array('learningcenter/blogpost', 'pattern' => 'api/blogpost', 'verb' => 'POST'),
                        array('learningcenter/blogsearch', 'pattern' => 'api/blogsearch', 'verb' => 'POST'),
                        array('learningcenter/blogcat', 'pattern' => 'api/blogcat', 'verb' => 'POST'),
                        array('learningcenter/jobsummary', 'pattern' => 'api/jobsummary', 'verb' => 'GET'),
                        array('learningcenter/jobpost', 'pattern' => 'api/jobpost', 'verb' => 'POST'),
                        array('learningcenter/jobsearch', 'pattern' => 'api/jobsearch', 'verb' => 'POST'),
                        array('learningcenter/jobcat', 'pattern' => 'api/jobcat', 'verb' => 'POST'),
                        array('learningcenter/getrecommended', 'pattern' => 'api/getrecommended', 'verb' => 'GET'),
                        array('learningcenter/gettopics', 'pattern' => 'api/gettopics', 'verb' => 'GET'),
                        array('learningcenter/getblogs', 'pattern' => 'api/getblogs', 'verb' => 'GET'),
                        array('user/setsubscriptionstatus', 'pattern' => 'api/setsubscriptionstatus', 'verb' => 'POST'),
                        array('user/getsubscriptionstatus', 'pattern' => 'api/getsubscriptionstatus', 'verb' => 'GET'),
                        // For Actionsteps
                        array('actionstep/steps', 'pattern' => 'api/steps', 'verb' => 'GET'),
                        array('actionstep/profile', 'pattern' => 'api/profile', 'verb' => 'GET'),
                        array('actionstep/addtrackuser', 'pattern' => 'api/addtrackuser', 'verb' => 'POST'),
                        array('actionstep/updateactionorder', 'pattern' => 'api/updateactionsort', 'verb' => 'POST'),
                        array('actionstep/getuserreport', 'pattern' => 'api/getuserreport', 'verb' => 'POST'),
                        array('actionstep/updatearticle', 'pattern' => 'api/updatearticle', 'verb' => 'POST'),
                        array('actionstep/updateactionpoints', 'pattern' => 'api/updatepoints', 'verb' => 'GET'),
                        array('actionstep/checkactionstep', 'pattern' => 'api/checkactionstep', 'verb' => 'GET'),
                        // For Score Engine
                        array('scoreengine/calculatescore', 'pattern' => 'api/calculatescore', 'verb' => 'POST'),
                        array('debt/debtcrud', 'pattern' => 'api/addupdatedebts', 'verb' => 'POST'),
                        array('debt/getdebts', 'pattern' => 'api/getdebts', 'verb' => 'GET'),
                        array('goal/addupdategoal', 'pattern' => 'api/addupdategoal', 'verb' => 'POST'),
                        array('goal/getgoals', 'pattern' => 'api/getgoals', 'verb' => 'GET'),
                        array('goal/reprioritizegoals', 'pattern' => 'api/reprioritizegoals', 'verb' => 'POST'),
                        array('expense/expensecrud', 'pattern' => 'api/addupdateexpense', 'verb' => 'POST'),
                        array('expense/getexpense', 'pattern' => 'api/getexpense', 'verb' => 'GET'),
                        array('asset/assetcrud', 'pattern' => 'api/addupdateasset', 'verb' => 'POST'),
                        array('asset/getassets', 'pattern' => 'api/getassets', 'verb' => 'GET'),
                        array('login/getscore', 'pattern' => 'api/getscore', 'verb' => 'GET'),
                        array('miscellaneous/misccrud', 'pattern' => 'api/addupdatemiscellaneous', 'verb' => 'POST'),
                        array('peerranking/peerrank', 'pattern' => 'api/peerrank', 'verb' => 'GET'),
                        array('peerranking/getpeerrank', 'pattern' => 'api/getpeerrank', 'verb' => 'GET'),
                        array('peerranking/updatepeerrank', 'pattern' => 'api/updatepeerrank', 'verb' => 'GET'),
                        array('estimation/setuserestimates', 'pattern' => 'api/setuserestimates', 'verb' => 'POST'),
                        array('estimation/setestimates', 'pattern' => 'api/setestimates', 'verb' => 'POST'),
                        array('estimation/getuserestimates', 'pattern' => 'api/getuserestimates', 'verb' => 'GET'),
                        array('insurance/insurancecrud', 'pattern' => 'api/addupdateinsurance', 'verb' => 'POST'),
                        array('income/incomecrud', 'pattern' => 'api/addupdateincome', 'verb' => 'POST'),
                        array('risk/riskcrud', 'pattern' => 'api/addupdaterisk', 'verb' => 'POST'),
                        array('risk/riskgetdata', 'pattern' => 'api/getriskdata', 'verb' => 'GET'),
                        array('risk/riskfactorsgetdata', 'pattern' => 'api/getriskfactorsdata', 'verb' => 'GET'),
                        array('learning/learningupdate', 'pattern' => 'api/addupdatelearning', 'verb' => 'POST'),
                        // Re Calculate Score calls
                        array('asset/recalculatescoreassets', 'pattern' => 'api/recalcasset', 'verb' => 'GET'),
                        array('debt/recalculatescoredebts', 'pattern' => 'api/recalcdebt', 'verb' => 'GET'),
                        array('insurance/recalculatescoreinsurance', 'pattern' => 'api/recalcinsu', 'verb' => 'GET'),
                        // For intraction with cashedge
                        array('cashedge/addupdatecashedgeuser', 'pattern' => 'api/addupdatecuser', 'verb' => 'GET'),
                        array('cashedge/searchfidetails', 'pattern' => 'api/searchfidetails', 'verb' => 'GET'),
                        array('cashedge/addfiitem', 'pattern' => 'api/addfiitem', 'verb' => 'POST'),
                        array('cashedge/deletefiloginacctid', 'pattern' => 'api/deletefiloginacctid', 'verb' => 'GET'),
                        array('cashedge/checkitemstatus', 'pattern' => 'api/checkitemstatus', 'verb' => 'GET'),
                        array('cashedge/useritem', 'pattern' => 'api/useritem', 'verb' => 'GET'),
                        array('cashedge/additemls', 'pattern' => 'api/additemls', 'verb' => 'POST'),
                        array('cashedge/addmfals', 'pattern' => 'api/addmfals', 'verb' => 'POST'),
                        array('cashedge/getinvestmentpos', 'pattern' => 'api/getinvestmentpos', 'verb' => 'GET'),
                        array('cashedge/updateaccounts', 'pattern' => 'api/refreshaccounts', 'verb' => 'POST'),
                        array('cashedge/checkstatus', 'pattern' => 'api/retryaccount', 'verb' => 'GET'),
                        array('cashedge/deleteaccount', 'pattern' => 'api/deleteaccount', 'verb' => 'GET'),
                        array('cashedge/retryaccount', 'pattern' => 'api/addaccount', 'verb' => 'POST'),
                        array('cashedge/deletefiacctid', 'pattern' => 'api/cedeleteacct', 'verb' => 'GET'),
                        array('cashedge/updateaccountsinternal', 'pattern' => 'api/ceupdateticker', 'verb' => 'GET'),
                        array('cashedge/pendingaccts', 'pattern' => 'api/pendingaccts', 'verb' => 'POST'),
                        //get user data
                        array('user/getallitem', 'pattern' => 'api/getallitem', 'verb' => 'GET'),
                        array('user/getuserdetails', 'pattern' => 'api/getuserdetails', 'verb' => 'GET'),
                        array('user/getnotificationdata', 'pattern' => 'api/getnotificationdata', 'verb' => 'GET'),
                        array('user/getactionstep', 'pattern' => 'api/getactionstep', 'verb' => 'GET'),
                        array('user/getactionstepdetail', 'pattern' => 'api/getactionstepdetails', 'verb' => 'GET'),
                        array('user/updatenotification', 'pattern' => 'api/updatenotification', 'verb' => 'GET'),
                        array('user/updateechouseragreement', 'pattern' => 'api/updateechouseragreement', 'verb' => 'POST'),
                        array('cashedge/ficonnect', 'pattern' => 'api/ficonnect', 'verb' => 'GET'),
                        array('cashedge/getaccountdetails', 'pattern' => 'api/getaccountdetails', 'verb' => 'GET'),
                        array('cashedge/itemupdatestatus', 'pattern' => 'api/itemupdatestatus', 'verb' => 'GET'),
                        array('cashedge/cedeleteuser', 'pattern' => 'api/deleteuser', 'verb' => 'GET'),
                        array('miscellaneous/justcheck', 'pattern' => 'api/justcheck', 'verb' => 'GET'),
                        array('user/reports', 'pattern' => 'api/reports', 'verb' => 'GET'),
                        array('user/usersbystatereport', 'pattern' => 'api/usersbystatereport', 'verb' => 'GET'),
                        array('user/scorechanges', 'pattern' => 'api/calcreports', 'verb' => 'GET'),
                        array('user/ceuserreport', 'pattern' => 'api/getceuserreport', 'verb' => 'POST'),
                        //get breakdown data
                        array('breakdown/breakdowntabs', 'pattern' => 'api/breakdowntabs', 'verb' => 'POST'),
                        array('cashedge/updatecashedgefipriority', 'pattern' => 'api/updatecashedgefipriority', 'verb' => 'GET'),
                        array('batchfile/processbatchfiles', 'pattern' => 'api/processbatchfiles', 'verb' => 'GET'),
                        array('user/lifeinsuranceparams', 'pattern' => 'api/getlifeinsuranceparams', 'verb' => 'GET'),
                        //List of advisor api
                        array('advisor/advdetails', 'pattern' => 'api/advisordetails', 'verb' => 'POST'),
                        array('advisor/getprofile', 'pattern' => 'api/advisorprofile', 'verb' => 'POST'),
                        array('advisor/viewprofile', 'pattern' => 'api/viewadvisorprofile', 'verb' => 'GET'),
                        array('advisor/getadvisorprofiledetails', 'pattern' => 'api/getadvisorprofiledetails', 'verb' => 'GET'),
                        array('advisor/updateAdvisorProfile', 'pattern' => 'api/updateAdvisorProfile', 'verb' => 'POST'),
                        //notifications
                        array('advisor/advisornotificationdata', 'pattern' => 'api/getadvisornotificationdata', 'verb' => 'GET'),
                        array('advisor/updateadvisornotification', 'pattern' => 'api/updateadvisornotification', 'verb' => 'GET'),
                        //admin notifications
                        array('advisor/unassignedadvisorcount', 'pattern' => 'api/unassignedadvisorcount', 'verb' => 'GET'),
                        //advisor url back to dashboard , destroy client session , view finances, upload profile pic.
                        array('advisor/backtodashboard', 'pattern' => 'api/backtodashboard', 'verb' => 'GET'),
                        array('advisor/destroyclientsession', 'pattern' => 'api/destroyclientsession', 'verb' => 'GET'),
                        array('advisor/viewfinances', 'pattern' => 'api/getviewFinances', 'verb' => 'POST'),
                        array('advisor/uploadprofilepic', 'pattern' => 'api/uploadprofilepic', 'verb' => 'POST'),
                        //end back to dashboard , destroy client session , view finances, upload profile pic.
                        //Password Change
                        //advisor urls
                        array('advisor/signup', 'pattern' => 'api/advisorsignup', 'verb' => 'POST'),
                        array('advisor/advisorStepTwoDetails', 'pattern' => 'api/advisorStepTwoDetails', 'verb' => 'POST'),
                        array('advisor/login', 'pattern' => 'api/advisorhome', 'verb' => 'POST'),
                        array('advisor/advisorcreateclient', 'pattern' => 'api/createnewclientbyadvisor', 'verb' => 'POST'),
                        array('advisor/createnewclientsignup', 'pattern' => 'api/createnewclientsignup', 'verb' => 'POST'),
                        array('advisor/list', 'pattern' => 'api/advisorlist', 'verb' => 'POST'),
                        array('advisor/pagination', 'pattern' => 'api/pagination', 'verb' => 'POST'),
                        array('advisor/sorting', 'pattern' => 'api/sorting', 'verb' => 'GET'),
                        array('advisor/advisorrelatedclient', 'pattern' => 'api/advisorclientrelated', 'verb' => 'GET'),
                        array('advisor/deleteclient', 'pattern' => 'api/deleteclient', 'verb' => 'GET'),
                        array('advisor/useradvisorlist', 'pattern' => 'api/useradvisorlist', 'verb' => 'GET'),
                        array('advisor/Deleteuseradvisor', 'pattern' => 'api/Deleteuseradvisor', 'verb' => 'GET'),
                        array('advisor/Updateadvisorpermission', 'pattern' => 'api/Updateadvisorpermission', 'verb' => 'GET'),
                        array('advisor/Updateleadadvisor', 'pattern' => 'api/Updateleadadvisor', 'verb' => 'GET'),
                        array('advisor/deleteadvisor', 'pattern' => 'api/deleteadvisor', 'verb' => 'POST'),
                        array('advisor/revokeadvisor', 'pattern' => 'api/revokeadvisor', 'verb' => 'POST'),
                        array('advisor/savepassword', 'pattern' => 'api/advisorsettings', 'verb' => 'POST'),
                        array('advisor/notifysettings', 'pattern' => 'api/notifysettings', 'verb' => 'POST'),
                        array('advisor/getnotifysettings', 'pattern' => 'api/getnotifysettings', 'verb' => 'POST'),
                        array('advisor/getprofilepic', 'pattern' => 'api/getprofilepic', 'verb' => 'POST'),
                        array('advisor/cropphoto', 'pattern' => 'api/cropphoto', 'verb' => 'POST'),
                        array('advisor/searchadvisor', 'pattern' => 'api/searchadvisor', 'verb' => 'POST'),
                        array('advisor/saveconnectmode', 'pattern' => 'api/saveconnectmode', 'verb' => 'POST'),
                        array('advisor/delete', 'pattern' => 'api/delete', 'verb' => 'GET'),
                        array('advisor/validateemails', 'pattern' => 'api/validateemails', 'verb' => 'POST'),
                        array('advisor/advdesignationverification', 'pattern' => 'api/advdesignationverification', 'verb' => 'POST'),
                        array('advisor/assignadvisor', 'pattern' => 'api/assignadvisor', 'verb' => 'POST'),
                        array('advisor/removeadvisor', 'pattern' => 'api/releaseadvisor', 'verb' => 'POST'),
                        array('advisor/displaydesignation', 'pattern' => 'api/displaydesignation', 'verb' => 'POST'),
                        array('advisor/connectionRequest', 'pattern' => 'api/connectionRequest', 'verb' => 'GET'),
                        ## Advisor verification Link
                        array('advisor/advisorverify', 'pattern' => 'api/advisorverify', 'verb' => 'GET'),
                        array('advisor/showAllAdvisors', 'pattern' => 'api/showAllAdvisors', 'verb' => 'POST'),
                        // Stripe advisor subscriptions
                        array('AdvisorSubscription/createadvisorsubscription', 'pattern' => 'api/createadvisorsubscription', 'verb' => 'POST'),
                        array('AdvisorSubscription/checkadvisorsubscription', 'pattern' => 'api/checkadvisorsubscription', 'verb' => 'GET'),
                        array('AdvisorSubscription/getsubscription', 'pattern' => 'api/getsubscription', 'verb' => 'GET'),
                        array('AdvisorSubscription/updatesubscription', 'pattern' => 'api/updatesubscription', 'verb' => 'POST'),
                        array('AdvisorSubscription/getcreditcard', 'pattern' => 'api/getcreditcard', 'verb' => 'GET'),
                        array('AdvisorSubscription/updatecreditcard', 'pattern' => 'api/updatecreditcard', 'verb' => 'POST'),
                        array('AdvisorSubscription/retrieveinvoicelist', 'pattern' => 'api/retrieveinvoicelist', 'verb' => 'GET'),
                        array('AdvisorSubscription/createflexscoreinvoicelist', 'pattern' => 'api/createinvoicelist', 'verb' => 'GET'),
                        array('AdvisorSubscription/canceladvisorsubscription', 'pattern' => 'api/cancelsubscription', 'verb' => 'POST'),
                        array('AdvisorSubscription/runSubscriptionUpdates', 'pattern' => 'api/runsubscriptionupdates', 'verb' => 'GET'),
                        array('user/montecarloparams', 'pattern' => 'api/getmontecarloparams', 'verb' => 'GET'),
                        array('advisor/getadvisorhelp', 'pattern' => 'api/getadvisorhelp', 'verb' => 'POST'),
                        array('site/advisorhelpnotification', 'pattern' => 'api/getadvisornotify', 'verb' => 'GET'),
                        array('user/montecarloparams', 'pattern' => 'api/getmontecarloparams', 'verb' => 'GET'),
                        array('user/runmontecarlo', 'pattern' => 'api/runmontecarlo', 'verb' => 'GET'),
                        //Update Connecting Account, Debt and Insurance checkbox status
                        array('user/updatepreferences', 'pattern' => 'api/updatepreferences', 'verb' => 'POST'),
                        array('user/getuserpreferences', 'pattern' => 'api/getuserpreferences', 'verb' => 'GET'),
                        array('user/getuserprofiledata', 'pattern' => 'api/getuserprofiledata', 'verb' => 'GET'),
                        //networth score//
                        array('login/networthscore', 'pattern' => 'api/getnetworthscore', 'verb' => 'GET'),
                        //action steps with external links//
                        array('advisor/getexternallinkas', 'pattern' => 'api/getexternallinkas', 'verb' => 'POST'),
                        array('advisor/getadminexternallinkas', 'pattern' => 'api/getadminexternallinkas', 'verb' => 'POST'),
                        array('advisor/updateasdescription', 'pattern' => 'api/updateexternallinkasdesc', 'verb' => 'POST'),
                        array('advisor/addspecificproductforas', 'pattern' => 'api/addproduct', 'verb' => 'POST'),
                        array('advisor/updatespecificproductforas', 'pattern' => 'api/updateproduct', 'verb' => 'POST'),
                        array('advisor/deletespecificproductforas', 'pattern' => 'api/deleteproduct', 'verb' => 'POST'),
                        //users by state report//
                        array('user/usersbystate', 'pattern' => 'api/usersbystate', 'verb' => 'GET'),
                        array('report/userfinancesreport', 'pattern' => 'api/getuserfinancesreport', 'verb' => 'GET'),
                        //upload client list by advisor//
                        array('advisor/uploadclients', 'pattern' => 'api/uploadclients', 'verb' => 'POST'),
                        array('user/getfinancialdetails', 'pattern' => 'api/getfinancialdetails', 'verb' => 'GET'),
                        array('insurance/getinsurance', 'pattern' => 'api/getinsurance', 'verb' => 'GET'),
                        array('cashedge/getpendinglinks', 'pattern' => 'api/getpendinglinks', 'verb' => 'GET'),
                        array('breakdown/getbreakdown', 'pattern' => 'api/getbreakdown', 'verb' => 'GET'),
                        array('user/getfinancialhighlights', 'pattern' => 'api/getfinancialhighlights', 'verb' => 'GET'),
                        array('site/addpin', 'pattern' => 'api/addpin', 'verb' => 'POST'),
                        array('site/editpin', 'pattern' => 'api/editpin', 'verb' => 'POST'),
                        // user_id / email, pin, oAuth / sessid ..
                        array('site/loginbypin', 'pattern' => 'api/loginbypin', 'verb' => 'POST'),
                        // Oauth api calls
// [DD](2014-AUG-11): I believe this is no longer used.
// I'm commenting it out for now, but it can probably be deleted on or after 2014-AUG-17.
//                        array('fswrapi/requesttoken', 'pattern' => 'api/requesttoken', 'verb' => 'POST'),
                        //advisor send invitation to users//
                        array('advisor/sendinvitation', 'pattern' => 'api/sendinvitation', 'verb' => 'POST'),
                        // This is the page that Mandrill/Mailchimp redirect to after an unsubscribe.
                        array('unsubscribe/unsub', 'pattern' => 'api/unsub', 'verb' => 'GET'),
                        array('emailsubscriptioncontroller/list', 'pattern' => 'api/unsubscribe', 'verb' => 'GET POST'),
                        array('user/unsubscribeemail', 'pattern' => 'api/unsubscribeemail', 'verb' => 'POST'),
                        // Temporary patch to allow mobile app to create/delete devices using legacy API.
                        array('device/create', 'pattern' => 'api/createdevice', 'verb' => 'POST PUT'),
                        array('device/delete', 'pattern' => 'api/deletedevice/<id:[a-zA-Z0-9]+>', 'verb' => 'POST DELETE'),
                        // Temporary patch to allow mobile app to create notifications using legacy API.
                        array('notification/create', 'pattern' => 'api/createnotification', 'verb' => 'POST'),
                        //reprioritize assets//
                        array('asset/reprioritizeassets', 'pattern' => 'api/reprioritizeassets', 'verb' => 'POST'),
                        array('debt/reprioritizedebts', 'pattern' => 'api/reprioritizedebts', 'verb' => 'POST'),
                        array('insurance/reprioritizeinsurance', 'pattern' => 'api/reprioritizeinsurance', 'verb' => 'POST'),

                        array('breakdown/savebreakdowntabs', 'pattern' => 'api/savebreakdowntabs', 'verb' => 'POST'),
                        array('breakdown/updatebreakdowntabs', 'pattern' => 'api/updatebreakdowntabs', 'verb' => 'POST'),
                        array('breakdown/deletebreakdowntabs', 'pattern' => 'api/deletebreakdowntabs', 'verb' => 'POST'),
 
                        array('user/moveuserassetdata', 'pattern' => 'api/moveuserassetdata', 'verb' => 'GET'),
                         
                    ),
                ),
                // MySQL database connection settings
                'db' => array(
                    'connectionString' => "mysql:host={$params['db.host']};dbname={$params['db.name']}",
                    'username' => $params['db.username'],
                    'password' => $params['db.password'],
                    'charset' => 'utf8',
                    'enableParamLogging' => YII_DEBUG,
                    'emulatePrepare' => true,
                    //'enableProfiling'=> YII_DEBUG,
                    'schemaCachingDuration' => YII_DEBUG ? 0 : 86400000, // 1000 days
                ),
                // MySQL database connection 2 settings
                'metadb' => array(
                    'connectionString' => "mysql:host={$params['db.host']};dbname={$params['db.name2']}",
                    'username' => $params['db.username'],
                    'password' => $params['db.password'],
                    'charset' => 'utf8',
                    //'enableParamLogging' => YII_DEBUG,
                    'enableParamLogging' => true,
                    'emulatePrepare' => true,
                    //'enableProfiling'=> YII_DEBUG,
                    'schemaCachingDuration' => YII_DEBUG ? 0 : 86400000, // 1000 days
                    'class' => 'CDbConnection'          // DO NOT FORGET THIS!
                ),
                'cms' => array(
                    'connectionString' => "mysql:host={$params['db.host']};dbname={$params['db.cms']}",
                    'username' => $params['db.username'],
                    'password' => $params['db.password'],
                    'charset' => 'utf8',
                    'enableParamLogging' => YII_DEBUG,
                    'emulatePrepare' => true,
                    //'enableProfiling'=> YII_DEBUG,
                    'schemaCachingDuration' => YII_DEBUG ? 0 : 86400000, // 1000 days
                    'class' => 'CDbConnection'          // DO NOT FORGET THIS!
                ),
                'session' => array(
                    'class' => 'system.web.CDbHttpSession',
                    'sessionName' => base64_encode($params['applicationUrl']),
                    'connectionID' => 'db',
                    'autoCreateSessionTable' => true,
                    'sessionTableName' => 'yii_session',
                    'timeout' => 900,
                ),
                  'cache' => /*extension_loaded('apc') ?*/
                  array(
                  'class' => 'CDbCache',
                  'connectionID' => 'metadb',
                  'autoCreateCacheTable' => true,
                  'cacheTableName' => 'cache',
                  ),
               /* 'cache' => array(
                    'class' => 'CMemCache',
                    'useMemcached' => true,
                    'servers' => array(
                        array(
                            'host' => $params['db.host'],
                            'port' => 11211,
                        ),
                    ),
                ), */
                'mailer' => array(
                    'class' => 'application.extensions.mailer.EMailer',
                ),
                'errorHandler' => array(
                    // use 'site/error' action to display errors
                    'errorAction' => 'site/error',
                ),
                'cashedge' => array(
                    'class' => 'CashEdge',
                    'partnerId' => $params['cashedge.partnerId'],
                    'homeId' => $params['cashedge.homeId'],
                    'adminUserId' => $params['cashedge.adminUserId'],
                    'adminUserPassword' => $params['cashedge.adminUserPassword'],
                    'url' => $params['cashedge.url'],
                ),
                'calcxml' => array(
                    'class' => 'CalcXML',
                    'user' => $params['calcXML.username'],
                    'password' => $params['calcXML.password'],
                    'serviceUrl' => $params['calcXML.serviceURL']
                ),
                'wordpressclient' => array(
                    'class' => 'WordpressClient',
                    'username' => $params['wordpress.username'],
                    'password' => $params['wordpress.password'],
                    'xmlrpcUrl' => $params['wordpress.xmlrpcUrl']
                ),
                'sengine' => array(
                    'class' => 'Sengine'
                ),
                'log' => array(
                    'class' => 'CLogRouter',
                    'routes' => array(
                        array(
                            'class' => 'CFileLogRoute',
                            'levels' => 'error, warning, trace, info',
                        ),
                    ),
                ),
                'mailer' => array(
                    'class' => 'application.extensions.mailer.EMailer',
                ),
                'errorHandler' => array(
                    // use 'site/error' action to display errors
                    'errorAction' => 'site/error',
                ),
                'cashedge' => array(
                    'class' => 'CashEdge',
                    'partnerId' => $params['cashedge.partnerId'],
                    'homeId' => $params['cashedge.homeId'],
                    'adminUserId' => $params['cashedge.adminUserId'],
                    'adminUserPassword' => $params['cashedge.adminUserPassword'],
                    'url' => $params['cashedge.url'],
                ),
                'calcxml' => array(
                    'class' => 'CalcXML',
                    'user' => $params['calcXML.username'],
                    'password' => $params['calcXML.password'],
                    'serviceUrl' => $params['calcXML.serviceURL']
                ),
                'wordpressclient' => array(
                    'class' => 'WordpressClient',
                    'username' => $params['wordpress.username'],
                    'password' => $params['wordpress.password'],
                    'xmlrpcUrl' => $params['wordpress.xmlrpcUrl']
                ),
                'sengine' => array(
                    'class' => 'Sengine'
                ),
                'log' => array(
                    'class' => 'CLogRouter',
                    'routes' => array(
                        array(
                            'class' => 'CFileLogRoute',
                            'levels' => 'error, warning, trace, info',
                        ),
                        /* array(
                          'class' => 'CWebLogRoute',
                          ), */
                        'cashedgelog' => array(
                            'class' => 'CFileLogRoute',
                            'logFile' => 'cashedge.log',
                            'categories' => 'cashedgecategory.*',
                        ),
                        'cashedgedatalog' => array(
                            'class' => 'CFileLogRoute',
                            'logFile' => 'cashedgedata.log',
                            'categories' => 'cashedgedata.*',
                        ),
                        'cashedgeapilog' => array(
                            'class' => 'CFileLogRoute',
                            'logFile' => 'cashedgeapi.log',
                            'categories' => 'cashedgeapi.*',
                        ),
                        'calcxmllog' => array(
                            'class' => 'CFileLogRoute',
                            'logFile' => 'calcxml.log',
                            'categories' => 'calcxmlcategory.*',
                        ),
                        'actionsteplog' => array(
                            'class' => 'CFileLogRoute',
                            'logFile' => 'actionstep.log',
                            'categories' => 'actionstepcategory.*',
                        ),
                        'MClog' => array(
                            'class' => 'CFileLogRoute',
                            'logFile' => 'MClog.log',
                            'categories' => 'MCcategory.*'
                        ),
                        array(
                            'class' => 'CEmailLogRoute',
                            'levels' => 'error',
                            'filter' => 'CLogFilter',
                            'emails' => array('alex.t@truglobal.com'),
                            'enabled' => !YII_DEBUG,
                        ),
                    ),
                ),
                'ePdf' => array(
                    'class' => 'ext.yii-pdf',
                    'params' => array(
                        'HTML2PDF' => array(
                            'librarySourcePath' => 'application.extensions.html2pdf.*',
                            'classFile' => 'html2pdf.class.php', // For adding to Yii::$classMap
                        )
                    )
                ),
                'curl' => array(
                    'class' => 'ext.curl.Curl',
                    'options' => array(/* additional curl options */),
                ),
            ),
                ), $configLocal);
