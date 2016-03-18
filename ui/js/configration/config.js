var loginCheckUrl= baseUrl+"/service/api/authcheck";
var loginUrl= baseUrl+"/service/api/userlogin";
var loginCheckIdemnification= baseUrl+"/service/api/idemnificationcheck";//Idemnification check
var signupUrl= baseUrl+"/service/api/usersignup";
var usersetpasswordUrl=baseUrl+"/service/api/usersetpasswordlogin";
var searchAccUrl=baseUrl+"/service/api/searchaccountsbytimezone";
var getItemFieldsUrl=baseUrl+"/service/api/getitemfields";
var transactionPageUrl = baseUrl+"/service/api/transactionpagination";
var assignAdvisor = baseUrl+"/service/api/assignadvisor";
var releaseAdvisor = baseUrl+"/service/api/releaseadvisor";
var unassignedAdvisorCount = baseUrl+"/service/api/unassignedadvisorcount";
var deletec = baseUrl+"/service/api/delete";
var refreshMonteCarlo = baseUrl+"/service/api/refreshmontecarlo"
//accept user connection request
var connectionRequest = baseUrl+"/service/api/connectionRequest";
//advisor signup
var advisorSignupUrl = baseUrl+"/service/api/advisorsignup";//step 1
var advisorStepTwoDetails = baseUrl+"/service/api/advisorStepTwoDetails";//step 2
var advisorProfileUrl = baseUrl+"/service/api/advisorprofile";
var advisorViewProfileUrl = baseUrl+"/service/api/viewadvisorprofile";
var advisorDesignationVerification = baseUrl+"/service/api/advdesignationverification";
var revokeAdvisor = baseUrl+"/service/api/revokeadvisor";
var advisorSettings = baseUrl+"/service/api/advisorsettings";
var notifySettings = baseUrl+"/service/api/notifysettings";
var getNotifySettings = baseUrl+"/service/api/getnotifysettings";
var cropPhoto = baseUrl+"/service/api/cropphoto?refresh="+new Date().valueOf();
var searchAdvisor = baseUrl+"/service/api/searchadvisor";
var saveConnectModeUrl = baseUrl+"/service/api/saveconnectmode";
//advisor login
var loginAdvUrl = baseUrl+"/service/api/advisorhome";
//insert into database createNewClientByAdvisor
var createNewClientByAdvisor = baseUrl+"/service/api/createnewclientbyadvisor";
//dashboard
var advisorDetails = baseUrl+"/service/api/advisordetails";
var uploadPicUrl = baseUrl+"/service/api/uploadprofilepic?refresh="+new Date().valueOf();
//advisor list
var getAdvisorList = baseUrl+"/service/api/advisorlist";

var advisorverify = baseUrl+"/service/api/advisorverify";
var getCreatenewclient = baseUrl+"/service/api/createnewclient";
var deleteclient = baseUrl+"/service/api/deleteclient";
var deleteadvisor = baseUrl+"/service/api/deleteadvisor";
var getUseradvisorlist = baseUrl+"/service/api/useradvisorlist";
var getDeleteuseradvisor = baseUrl+"/service/api/Deleteuseradvisor";
var getUpdatedAdvisorPermission = baseUrl+"/service/api/Updateadvisorpermission";
var getUpdateleadadvisor = baseUrl+"/service/api/Updateleadadvisor";
//advisorrelated client
var advisorClientRelated = baseUrl+"/service/api/advisorclientrelated";
var createNewClientSignup = baseUrl+"/service/api/createnewclientsignup";
var logoutUrl = baseUrl+"/service/api/userlogout";
var refreshAllUrl = baseUrl+"/service/api/refreshaccounts";
var addUserInfo1URL = baseUrl+"/service/api/adduserinfoone";
var addUserInfo2URL = baseUrl+"/service/api/addupdateprofile";
var learningCenterURL = baseUrl+"/service/api/lcmain";
var learningCenterPressRelease = baseUrl+"/service/api/lcpresscms";
var learningCenterPostURL = baseUrl+"/service/api/lcpost";
var learningCenterPressURL = baseUrl+"/service/api/lcpress";
var learningCenterGlossaryURL = baseUrl+"/service/api/lcglossary";
var learningCenterPostSearchURL = baseUrl+"/service/api/lcpostsearch";
var learningCenterSearchByCatURL = baseUrl+"/service/api/lccatsearch";
var blogURL = baseUrl+"/service/api/blogsummary";
var blogviewURL = baseUrl+"/service/api/blogpost";
var blogSearchURL = baseUrl+"/service/api/blogsearch";
var blogCatURL = baseUrl+"/service/api/blogcat";

var jobURL = baseUrl+"/service/api/jobsummary";
var jobviewURL = baseUrl+"/service/api/jobpost";
var jobSearchURL = baseUrl+"/service/api/jopsearch";
var jobCatURL = baseUrl+"/service/api/jobcat";

var validateEmailUrl= baseUrl+"/service/api/validateemails";
//for score engine and cashedge
var userExpenseAddUpdateURL = baseUrl+"/service/api/addupdateexpense";
var userDebtsAddUpdateURL = baseUrl+"/service/api/addupdatedebts";
var userGoalAddUpdateURL = baseUrl+"/service/api/addupdategoal";
var userAssetAddUpdateURL = baseUrl+"/service/api/addupdateasset";
var userMiscAddUpdateURL = baseUrl+"/service/api/addupdatemiscellaneous";
var userGetScoreURL = baseUrl+"/service/api/getscore";
var userGetActionStepURL = baseUrl+"/service/api/getactionstep";
var userProfileAddUpdateURL = baseUrl+"/service/api/addupdateprofile";
var userAddUpdateInsuranceURL = baseUrl+"/service/api/addupdateinsurance";
var userIncomeAddUpdateURL = baseUrl+"/service/api/addupdateincome";
var userRiskAddUpdateURL = baseUrl+"/service/api/addupdaterisk";
var userRiskGetDataURL = baseUrl+"/service/api/getriskdata";
var riskFactorsGetDataURL = baseUrl+"/service/api/getriskfactorsdata";
//cashedge
var userSearchFiURL = baseUrl+"/service/api/searchfidetails";
var userAddFiURL = baseUrl+"/service/api/addfiitem";
var useritemUrl = baseUrl+"/service/api/useritem";
var accountRemoveUrl = baseUrl+"/service/api/deletefiloginacctid";
var userAccountCheckURL = baseUrl+"/service/api/checkitemstatus";
var accountAddLSURL = baseUrl+"/service/api/additemls";
var mfaSendLSURL = baseUrl+"/service/api/addmfals";
var getNotificationDataURL = baseUrl+"/service/api/getnotificationdata";
var getAdvisorNotificationDataURL = baseUrl+"/service/api/getadvisornotificationdata";//Advisor Notification.
var getAllItem = baseUrl+"/service/api/getallitem";
var getUserDetails = baseUrl+"/service/api/getuserdetails";

var getAdvisorprofiledetails = baseUrl+"/service/api/getadvisorprofiledetails";
var checkAdvisorSubscriptionURL = baseUrl+"/service/api/checkadvisorsubscription";
var createAdvisorSubscriptionURL = baseUrl+"/service/api/createadvisorsubscription";
var getSubscriptionURL = baseUrl+"/service/api/getsubscription";
var updateSubscriptionUrl = baseUrl+"/service/api/updatesubscription";
var getCreditCardUrl = baseUrl+"/service/api/getcreditcard";//retrieve card information
var updateCreditCardUrl = baseUrl+"/service/api/updatecreditcard";//update card information
var retrieveinvoicelistURL = baseUrl+"/service/api/retrieveinvoicelist";//retrieve invoice list
var createinvoicelistURL = baseUrl+"/service/api/createinvoicelist";//retrieve invoice list
var cancelSubscriptionURL = baseUrl+"/service/api/cancelsubscription";//cancel subscription
var updateAdvisorProfile = baseUrl+"/service/api/updateAdvisorProfile";
var showAllAdvisors = baseUrl+"/service/api/showAllAdvisors";
var LCSearchByCatNameURL = baseUrl+"/service/api/lccatsearchname";
var notificationUpdateUrl = baseUrl+"/service/api/updatenotification";
var notificationAdvisorUpdateUrl = baseUrl+"/service/api/updateadvisornotification";
var retryAccountUrl = baseUrl+"/service/api/retryaccount";
var deleteAccountUrl = baseUrl+"/service/api/deleteaccount";
var getfiConnectUrl = baseUrl+"/service/api/ficonnect";
var actionOverlayURL = baseUrl+"/service/api/getactionstepdetails";
var addeditlearningURL = baseUrl+"/service/api/addupdatelearning";
var addTrackuserURL = baseUrl+"/service/api/addtrackuser";
var updateactionstepsortURL = baseUrl+"/service/api/updateactionsort";
var finalscoreURL = baseUrl+"/service/api/getuserreport";
var runActionStepURL = baseUrl+"/service/api/steps";
var updateArticleViewURL = baseUrl+"/service/api/updatearticle";
var accountAddContinueURL = baseUrl+"/service/api/addaccount";
var getReportsURL = baseUrl+"/service/api/reports";
var getUsersByStateReportURL = baseUrl+"/service/api/usersbystatereport";
var updatePeerURL = baseUrl+"/service/api/updatepeerrank";
var updateTickerURL = baseUrl+"/service/api/ceupdateticker";
var updateCashEdgeFIPriorityURL = baseUrl+"/service/api/updatecashedgefipriority";
var processBatchfilesURL = baseUrl+"/service/api/processbatchfiles";
var getCeUserReportURL = baseUrl+"/service/api/getceuserreport";
var getUserFinancesReportURL = baseUrl+"/service/api/getuserfinancesreport";

// CRUD Speeding Up:
var reCalculateScoreAssetsURL = baseUrl+"/service/api/recalcasset";
var reCalculateScoreDebtsURL = baseUrl+"/service/api/recalcdebt";
var reCalculateScoreInsuURL = baseUrl+"/service/api/recalcinsu";

// CE deleting accounts
var ceDeleteAcctURL = baseUrl+"/service/api/cedeleteacct";

// deleting own account
var userDeleteAcctURL = baseUrl+"/service/api/deleteuseraccount";

// Password change
var changePwdURL = baseUrl+"/service/api/changepwd";
var getPeerRankUrl = baseUrl+"/service/api/getpeerrank";
var forgotPasswordUrl = baseUrl+"/service/api/resetpassword";
var advisorForgotPasswordUrl = baseUrl+"/service/api/advisorresetpassword";
var resetPasswordUrl = baseUrl+"/service/api/updatepassword";
var advisorResetPasswordUrl = baseUrl+"/service/api/advisorupdatepassword";

// Role Access
var roleActvities= baseUrl+"/service/api/listroleactivities";
var breakdownURL = baseUrl+"/service/api/breakdowntabs";
var savebreakdownURL = baseUrl+"/service/api/savebreakdowntabs";
var updatebreakdownURL = baseUrl+"/service/api/updatebreakdowntabs";
var deletebreakdownURL = baseUrl+"/service/api/deletebreakdowntabs";


//back to dashboard , destroy new client seesion , view finances
var backtoadvDashboard= baseUrl+"/service/api/backtodashboard";
var destroyclientsession= baseUrl+"/service/api/destroyclientsession";
var getviewFinances= baseUrl+"/service/api/getviewFinances";

// Node Js
//var nodeModules = baseUrl+"/service/node/node_modules/socket.io/lib/socket.io.js";
var nodeModules = nodeUrl+":8080/socket.io/socket.io.js";
var nodeNotificationUrl = nodeUrl+":8080";

// Testing
var getLifeInsuranceParamsURL = baseUrl+"/service/api/getlifeinsuranceparams";
var getMonteCarloParamsURL = baseUrl+"/service/api/getmontecarloparams";
var reprioritizeGoalsURL = baseUrl+"/service/api/reprioritizegoals";

var getadvisorhelp = baseUrl+"/service/api/getadvisorhelp";
// Update Connecting Account, Debt and Insurance checkbox status

var updateUserCheckboxPreferences = baseUrl+"/service/api/updatepreferences";
var getUserCheckboxPreferences = baseUrl+"/service/api/getuserpreferences";

var getuserprofiledata = baseUrl+"/service/api/getuserprofiledata";

var riskdata = null; // Storing Risk data

//networth api url//
var getnetworthscore = baseUrl+"/service/api/getnetworthscore";

//action steps with external links//
var getexternallinkas = baseUrl+"/service/api/getexternallinkas";
var getadminexternallinkas = baseUrl+"/service/api/getadminexternallinkas";
var updateexternallinkasDesc = baseUrl+"/service/api/updateexternallinkasdesc";
var addproduct = baseUrl+"/service/api/addproduct";
var updateproduct = baseUrl+"/service/api/updateproduct";
var deleteproduct = baseUrl+"/service/api/deleteproduct";

// Email Verification Link
var sendverificationemailURL = baseUrl+"/service/api/sendverificationemail"

//upload client//
var uploadClients = baseUrl+"/service/api/uploadclients?refresh="+new Date().valueOf();


//All Mobile URL goes here
var mgetAllItemDebtsAssetsInsurance = baseUrl+"/service/api/mgetallitemdebtsassetsinsurance";
var mgetAllItemGoals = baseUrl+"/service/api/mgetallitemgoals";
var mgetallitemLinkedAccounts = baseUrl+"/service/api/mgetallitemlinkedaccounts";
var mgetallitemEstimation = baseUrl+"/service/api/mgetallitemestimation";
var mgetallitemBreakdown = baseUrl+"/service/api/mgetallitembreakdown";

var userVerifyUrl = baseUrl+"/service/api/userverify";
var getSubscriptionStatusUrl = baseUrl + "/service/api/getsubscriptionstatus";
var setSubscriptionStatusUrl = baseUrl + "/service/api/setsubscriptionstatus";

//advisor send invitation to users//
var sendinvitation = baseUrl+"/service/api/sendinvitation";

var updateEchoUserAgreement = baseUrl + "/service/api/updateechouseragreement";

var reprioritizeAssetsURL = baseUrl+"/service/api/reprioritizeassets";
var reprioritizeDebtsURL = baseUrl+"/service/api/reprioritizedebts";
var reprioritizeInsuranceURL = baseUrl+"/service/api/reprioritizeinsurance";


