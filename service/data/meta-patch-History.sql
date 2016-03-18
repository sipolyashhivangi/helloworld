  --
-- Table structure for table `lifeexpectancy`
--

CREATE TABLE `lifeexpectancy` (
  `2007age` tinyint(4) NOT NULL,
  `MYearsToLive` tinyint(4) NOT NULL,
  `MLifeExpectancy` tinyint(4) NOT NULL,
  `FYearsToLive` tinyint(4) NOT NULL,
  `FLifeExpectancy` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lifeexpectancy`
--

INSERT INTO `lifeexpectancy` (`2007age`, `MYearsToLive`, `MLifeExpectancy`, `FYearsToLive`, `FLifeExpectancy`) VALUES
(0, 75, 75, 80, 80),
(1, 75, 76, 80, 81),
(2, 74, 76, 79, 81),
(3, 73, 76, 78, 81),
(4, 72, 76, 77, 81),
(5, 71, 76, 76, 81),
(6, 70, 76, 75, 81),
(7, 69, 76, 74, 81),
(8, 68, 76, 73, 81),
(9, 67, 76, 72, 81),
(10, 66, 76, 71, 81),
(11, 65, 76, 70, 81),
(12, 64, 76, 69, 81),
(13, 63, 76, 68, 81),
(14, 62, 76, 67, 81),
(15, 61, 76, 66, 81),
(16, 60, 76, 65, 81),
(17, 59, 76, 64, 81),
(18, 58, 76, 63, 81),
(19, 57, 76, 62, 81),
(20, 56, 76, 61, 81),
(21, 55, 76, 60, 81),
(22, 55, 77, 59, 81),
(23, 54, 77, 58, 81),
(24, 53, 77, 57, 81),
(25, 52, 77, 56, 81),
(26, 51, 77, 55, 81),
(27, 50, 77, 54, 81),
(28, 49, 77, 53, 81),
(29, 48, 77, 52, 81),
(30, 47, 77, 52, 82),
(31, 46, 77, 51, 82),
(32, 45, 77, 50, 82),
(33, 44, 77, 49, 82),
(34, 43, 77, 48, 82),
(35, 42, 77, 47, 82),
(36, 42, 78, 46, 82),
(37, 41, 78, 45, 82),
(38, 40, 78, 44, 82),
(39, 39, 78, 43, 82),
(40, 38, 78, 42, 82),
(41, 37, 78, 41, 82),
(42, 36, 78, 40, 82),
(43, 35, 78, 39, 82),
(44, 34, 78, 38, 82),
(45, 33, 78, 37, 82),
(46, 32, 78, 36, 82),
(47, 32, 79, 35, 82),
(48, 31, 79, 35, 83),
(49, 30, 79, 34, 83),
(50, 29, 79, 33, 83),
(51, 28, 79, 32, 83),
(52, 27, 79, 31, 83),
(53, 26, 79, 30, 83),
(54, 26, 80, 29, 83),
(55, 25, 80, 28, 83),
(56, 24, 80, 27, 83),
(57, 23, 80, 27, 84),
(58, 22, 80, 26, 84),
(59, 22, 81, 25, 84),
(60, 21, 81, 24, 84),
(61, 20, 81, 23, 84),
(62, 19, 81, 22, 84),
(63, 19, 82, 21, 84),
(64, 18, 82, 21, 85),
(65, 17, 82, 20, 85),
(66, 16, 82, 19, 85),
(67, 16, 83, 18, 85),
(68, 15, 83, 18, 86),
(69, 14, 83, 17, 86),
(70, 14, 84, 16, 86),
(71, 13, 84, 15, 86),
(72, 12, 84, 15, 87),
(73, 12, 85, 14, 87),
(74, 11, 85, 13, 87),
(75, 11, 86, 13, 88),
(76, 10, 86, 12, 88),
(77, 9, 86, 11, 88),
(78, 9, 87, 11, 89),
(79, 8, 87, 10, 89),
(80, 8, 88, 9, 89),
(81, 7, 88, 9, 90),
(82, 7, 89, 8, 90),
(83, 6, 89, 8, 91),
(84, 6, 90, 7, 91),
(85, 6, 91, 7, 92),
(86, 5, 91, 6, 92),
(87, 5, 92, 6, 93),
(88, 5, 93, 5, 93),
(89, 4, 93, 5, 94),
(90, 4, 94, 5, 95),
(91, 4, 95, 4, 95),
(92, 3, 95, 4, 96),
(93, 3, 96, 4, 97),
(94, 3, 97, 4, 98),
(95, 3, 98, 3, 98),
(96, 3, 99, 3, 99),
(97, 2, 99, 3, 100),
(98, 2, 100, 3, 101),
(99, 2, 101, 3, 102),
(100, 2, 102, 2, 102),
(101, 2, 103, 2, 103),
(102, 2, 104, 2, 104),
(103, 2, 105, 2, 105),
(104, 2, 106, 2, 106),
(105, 2, 107, 2, 107),
(106, 1, 107, 2, 108),
(107, 1, 108, 2, 109),
(108, 1, 109, 1, 109),
(109, 1, 110, 1, 110),
(110, 1, 111, 1, 111),
(111, 1, 112, 1, 112),
(112, 1, 113, 1, 113),
(113, 1, 114, 1, 114),
(114, 1, 115, 1, 115),
(115, 1, 116, 1, 116),
(116, 1, 117, 1, 117),
(117, 1, 118, 1, 118),
(118, 1, 119, 1, 119),
(119, 1, 120, 1, 120);


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE otlt;

CREATE TABLE IF NOT EXISTS `otlt` (
  `id` int(10) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `otlt` (`id`, `name`, `description`, `startdate`, `enddate`) VALUES
(30, 'Credit Card', '', '0000-00-00', '0000-00-00'),
(31, 'Mortgage', '', '0000-00-00', '0000-00-00'),
(32, 'Loans', '', '0000-00-00', '0000-00-00'),
(33, '30 Years Fixed', '', '0000-00-00', '0000-00-00'),
(34, '15 Years Fixed', '', '0000-00-00', '0000-00-00'),
(35, '5/1 ADjustable Rate', '', '0000-00-00', '0000-00-00'),
(36, 'Personal', '', '0000-00-00', '0000-00-00'),
(37, 'Auto', '', '0000-00-00', '0000-00-00'),
(38, 'Student', '', '0000-00-00', '0000-00-00'),
(39, 'Business', '', '0000-00-00', '0000-00-00'),
(40, 'Bank Accounts', '', '0000-00-00', '0000-00-00'),
(41, 'IRA', '', '0000-00-00', '0000-00-00'),
(42, '401K', '', '0000-00-00', '0000-00-00'),
(43, 'Brokerage', '', '0000-00-00', '0000-00-00'),
(44, 'Property', '', '0000-00-00', '0000-00-00'),
(45, 'Vehicle', '', '0000-00-00', '0000-00-00'),
(46, 'Pension', '', '0000-00-00', '0000-00-00'),
(47, 'Cash', '', '0000-00-00', '0000-00-00'),
(48, 'Stock', '', '0000-00-00', '0000-00-00'),
(49, 'Mutual funds', '', '0000-00-00', '0000-00-00'),
(50, 'Bonds', '', '0000-00-00', '0000-00-00'),
(51, 'Roth', '', '0000-00-00', '0000-00-00'),
(52, 'Regular', '', '0000-00-00', '0000-00-00'),
(53, 'Savings Account', '', '0000-00-00', '0000-00-00'),
(54, 'Educational Savings', '', '0000-00-00', '0000-00-00'),
(55, 'Custodial Account', '', '0000-00-00', '0000-00-00'),
(56, 'Regular Brokerage', '', '0000-00-00', '0000-00-00'),
(57, 'Savings Account', '', '0000-00-00', '0000-00-00'),
(60, 'Life Insurance', '', '0000-00-00', '0000-00-00'),
(61, 'Disability Insurance', '', '0000-00-00', '0000-00-00'),
(62, 'Longterm Care Insurance', '', '0000-00-00', '0000-00-00'),
(63, 'Other', '', '0000-00-00', '0000-00-00'),
(64, 'Term', '', '0000-00-00', '0000-00-00'),
(65, 'Whole Life', '', '0000-00-00', '0000-00-00'),
(66, 'Universal Life', '', '0000-00-00', '0000-00-00'),
(67, 'Variable Life', '', '0000-00-00', '0000-00-00'),
(68, 'Variable Universal Life', '', '0000-00-00', '0000-00-00'),
(69, 'Private', '', '0000-00-00', '0000-00-00'),
(70, 'Group', '', '0000-00-00', '0000-00-00'),
(121, 'CONSUMER MANAGEMENT', '', '0000-00-00', '0000-00-00'),
(122, 'ADVISOR MANAGEMENT', '', '0000-00-00', '0000-00-00'),
(123, 'PRODUCT MANAGEMENT', '', '0000-00-00', '0000-00-00'),
(124, 'REPORT MANAGEMENT', '', '0000-00-00', '0000-00-00'),
(125, 'LEARNING MANAGEMENT', '', '0000-00-00', '0000-00-00'),
(777, 'ADMIN', '', '0000-00-00', '0000-00-00'),
(888, 'CONSUMER', '', '0000-00-00', '0000-00-00'),
(999, 'ADVISOR', '', '0000-00-00', '0000-00-00'),
(2001, 'vid1', 'ZC4M73WnwOk', '0000-00-00', '0000-00-00'),
(2002, 'vid2', 'nXf3LP5uMoE', '0000-00-00', '0000-00-00'),
(2003, 'vid3', '', '0000-00-00', '0000-00-00'),
(2004, 'vid4', '', '0000-00-00', '0000-00-00'),
(2005, 'vid5', '', '0000-00-00', '0000-00-00'),
(2006, 'vid6', '--baF9i18Z0', '0000-00-00', '0000-00-00'),
(2007, 'vid7', 'avODsBYgALc', '0000-00-00', '0000-00-00'),
(2008, 'vid8', '', '0000-00-00', '0000-00-00'),
(2009, 'vid9', '', '0000-00-00', '0000-00-00'),
(2010, 'vid10', '', '0000-00-00', '0000-00-00'),
(2011, 'vid11', '', '0000-00-00', '0000-00-00'),
(2012, 'vid12', '', '0000-00-00', '0000-00-00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Table structure for table `lifeexpectancy`
--

UPDATE `actionstepmeta` SET `link` = 'addasset', `linktype` = 'action', `buttonstep2` = 'Mark as Done', `linkstep2` = 'Connect' WHERE `actionid` =12;
UPDATE `actionstepmeta` SET `category` = 'Financial Planning' WHERE `category` LIKE 'General Financial Planning & Cash Flow';
ALTER TABLE `actionstepmeta` DROP `status`;
TRUNCATE TABLE `actionstepmeta`;

INSERT INTO `actionstepmeta` VALUES
(1, 'Connect Accounts', 'Profile Completeness', 35, 'connectaccount', '', '', 'action', 'instant', 'Maybe this account doesn''t have any online access, and that''s why you entered it manually. But, if you can figure out whether or not there''s website access, and if so, your username and password, that''d be great. It would help us help you.  {{lnk}} to link your account.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(2, ' Life Insurance - Increase Coverage to ${{amt}}', 'Protection Planning', 15, 'addinsurance', '', '', 'action', 'short', 'So you don''t believe you/your spouse will ever die. Okay. But just in case you are mortal, we HIGHLY recommend getting yourself covered by at least ${{amt}}. {{lnk}} for help with that.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 8),
(3, 'Life Insurance - Get Policy for ${{amt}} of Coverage', 'Protection Planning', 24, 'addinsurance', '', '', 'action', 'mid', 'Looks like you need to increase your Life Insurance coverage for yourself/ your spouse to ${{amt}}. You can contact your current provider to see how they can help.  Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(4, 'Video - Life Insurance', 'Protection Planning', 5, '', 'Life Insurance', '', 'video', 'instant', '{{lnk}} to watch a short video on Life Insurance', 'Learn from Video', '', 'I''m done', '', '', 0),
(5, 'Video - Disability Insurance', 'Protection Planning', 5, 'ZC4M73WnwOk', 'Disability Insurance', 'vid1', 'video', 'instant', 'Watch a short video on Disability Insurance', 'Learn from Video', '', 'I''m done', '', '', 10),
(6, 'Diversify Investments - Adjust to Match Risk Tolerance', 'Investment Planning', 30, '', '', '', 'other', 'short', 'Your chosen Risk Tolerance of 5 out of 10 calls for 35% in Risk Assets.  Currently you have 30%.  To better meet your goals please increase / decrease the amount you have in risk assets by 56000.  <a href="#">{{lnk}}</a> to see your investments and their respective risk amounts.', 'Get Started', '', 'I read this', '', '', 9),
(7, 'Complete Risk Tolerance Preference', 'Investment Planning', 15, '', '', '', 'other', 'short', 'You want high returns with low risk...we all do!  We’ll do our best to help with that. That''s why it''s so important for you to complete this.  {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(8, 'Watch Video - Investment Diversification', 'Investment Planning', 0, 'avODsBYgALc', 'Investment Diversification', 'vid7', 'video', 'instant', '{{lnk}} to watch short video on Investment Diversification', 'Learn from Video', '', 'I''m done', '', '', 0),
(9, 'Increase Monthly Contribution to ${{amt}} in your Asset Account', 'Retirement Planning', 0, '', '', '', 'other', 'instant', 'In your __________ account, increase your monthly contributions to $__________ per month in order to meet your retirement goal.', 'Get Started', '', 'I read this', '', '', 0),
(10, 'Review Beneficiary Designations and Update if Needed', 'Retirement Planning', 5, '', '', '', 'other', 'instant', 'Beneficiaries are those people (or charities) who will inherit your account upon your death.  You should make sure you have chosen beneficiaries for your ___________ account, or at least review to make sure they are up to date.', 'Get Started', '', 'I read this', '', '', 0),
(11, 'Inquire with Employer to Determine Maximum Employer Contribution Amounts', 'Retirement Planning', 8, '', '', '', 'other', 'short', 'Ask your work if they''ll provide a matching contribution plan. If they do that, find out how much you need to put away to get the most benefit. You don''t want to leave any money on the table.', 'Get Started', '', 'I read this', '', '', 0),
(12, 'Adding IRA - Traditional or ROTH', 'Retirement Planning', 10, 'addasset', '', '', 'action', 'short', 'Looks like you don''t contribute to a company retirement plan account. That might not be available where you work. But check with Human Resources just in case you might be missing out.  Otherwise, you should open an Individual Retirement Account (IRA) by {{lnk}}.', 'Get Started', 'Mark as Done', 'I read this', 'Connect', '', 0),
(13, 'Complete Will and Other Estate Planning Docs', 'Estate Planning', 35, '', '', '', 'other', 'mid', 'Please complete the appropriate estate planning documents for your household.  A will or a trust, medical directives, and guardianship directions if you have dependents are all considered responsible documents to create. <a href="#">{{lnk}}</a> to begin.', 'Get Started', '', 'I read this', '', '', 0),
(14, 'Update Will and Other Estate Planning Docs', 'Estate Planning', 15, '', '', '', 'other', 'mid', 'Now it’s time to review your estate plan docs to make sure they are still relevant today.  If you don’t already have each of these, you may want to consider them: a will or a trust, medical directives, and guardianship directions if you have dependents. To create any or all of the above, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(15, 'Identify Financial Goals, Cost of Goal, Time Period to achieve goal', 'Goal Planning', 5, 'addgoal', '', '', 'action', 'short', 'Having realistic financial goals to work toward is the basis for improving your situation. Considering the cost, time horizon, and priority of your goals is the first step toward improving your FlexScore...and your life! {{lnk}} to set up goals.', 'Set a goal', 'Mark as Done', 'I did this', 'Connect', '', 0),
(16, 'Consider Setting Up a Goal of Paying Off Consumer Debt', 'Goal Planning', 10, '', '', '', 'other', 'short', 'We can help you pay off that debt in no time. To set up a goal to pay off your debt sooner, <a href="#">{{lnk}}</a>.', 'Get Started', '', 'I read this', '', '', 0),
(17, 'Consider Refinance Options on Mortgage Debt', 'Goal Planning', 12, 'http://track.flexlinks.com/a.aspx?foid=32954365&fot=9999&foc=1', '', '', 'link', 'short', 'Refinancing your mortgage can likely help you to pay off that debt sooner. <a href="#">{{lnk}}</a> to begin.', 'Get Started', '', 'I read this', '', '', 0),
(18, 'Watch Video - Debt Optimization', 'Goal Planning', 5, 'ZC4M73WnwOk', 'Debt Optimization', 'vid1', 'video', 'instant', '{{lnk}} to watch a short video on Debt Optimization.', 'Learn from Video', '', 'I''m done', '', '', 0),
(19, 'Watch Video - Total Debt Management', 'Goal Planning', 5, 'ZC4M73WnwOk', 'Total Debt Management', 'vid1', 'video', 'instant', '{{lnk}} to watch a short video on Knowledge of Debt', 'Learn from Video', '', 'I''m done', '', '', 0),
(20, 'Create Emergency Fund for Unplanned Costs', 'Financial Planning', 0, '', '', '', 'other', 'short', 'Putting an emergency fund in place is a key to personal financial success. {{lnk}} to start.', 'Get Started', '', 'I read this', '', '', 0),
(21, 'Consider Auto-Linking All Financial Accounts', 'Financial Planning', 10, '', '', '', 'other', 'short', 'Let''s connect all your accounts for a better overview and easier ability for us to keep you up to date. {{lnk}} to start.', 'Get Started', '', 'I read this', '', '', 0),
(22, 'Increase Savings', 'Financial Planning', 0, '', '', '', 'other', 'short', 'You don''t have much in savings right now. Let''s set you up with a plan that''s easy to manage and gives you room to breathe. {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(23, 'Obtain and Review Current Credit Score', 'Financial Planning', 5, '', '', '', 'other', 'short', 'Need a better credit score? We''re here to help with that!  To monitor your score and protect your identity, <a href="#">{{lnk}}</a>.', 'Get Started', '', 'I read this', '', '', 0),
(24, 'Watch Video - Inflation', 'Financial Planning', 8, '--baF9i18Z0', 'Inflation', 'vid6', 'video', 'instant', '{{lnk}} to watch short video on Inflation.', 'Learn from Video', '', 'I''m done', '', '', 0),
(25, 'Give us more information on an asset', 'Profile Completeness', 25, 'addasset', '', '', 'action', 'instant', 'Please give us more details on this asset, “Bank account.” The more details you give us, the better we can help you.  {{lnk}} to do it.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(26, 'Give us more information on a debt', 'Profile Completeness', 18, 'adddebt', '', '', 'action', 'instant', 'Please give us more details on this debt. The more details you give us, the better we can help you. {{lnk}} to do it.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(27, 'Fill in Tax Section', 'Profile Completeness', 14, 'addmisc', '', '', 'action', 'instant', 'Yeah, we know taxes can be boring...but we still need to deal with them. Please complete the Tax Planning portion of FlexScore by {{lnk}}', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(28, 'Fill in Estate Planning', 'Profile Completeness', 35, 'addmisc', '', '', 'action', 'instant', 'Do you think if you start pondering a will that you''ll suddenly die (presumably of boredom!)?  Umm, that''s not how it works. Please complete the Estate Planning portion of FlexScore by {{lnk}}.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(29, 'Tell us more about Insurance (if checked in about you section)', 'Profile Completeness', 15, '', '', '', 'other', 'instant', 'We know your insurance situation isn''t exactly something you talk about much (let alone think about!) unless you have a pending issue. Nonetheless, you should tell us as much information about it as possible. Please do that by {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(30, 'Tell us more about Will & Trust  (if checked in about you section)', 'Profile Completeness', 5, '', '', '', 'other', 'instant', 'Do you think if you start pondering a will that you''ll suddenly die (presumably of boredom!)?  Umm, that''s not how it works. Please complete the Estate Planning portion of FlexScore by {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(31, 'Give us more detailed Income', 'Profile Completeness', 35, 'addincome', '', '', 'action', 'instant', 'Simply telling us an estimate of how much you earn is not exact enough. Please give us more details by {{lnk}}.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(32, 'Give us more detail Expenses', 'Profile Completeness', 35, 'addexpense', '', '', 'action', 'instant', 'Simply telling us an estimate of how much you spend is not exact enough. Please give us more details by {{lnk}}.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(33, 'Be more accurate on Risk Tolerance Slider (1-10)', 'Profile Completeness', 15, '', '', '', 'other', 'instant', 'We know you want high returns with low risk...we all do!  Risk and reward are closely related and so important that you need to complete this. {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(34, 'Add a Goal', 'Profile Completeness', 10, 'addgoal', '', '', 'action', 'instant', 'Have you ever jumped in your car and set off on a journey without a destination in mind?  Yeah, we haven''t either. That''s why it''s important that you set financial goals. Without a goal to work toward, you are just aimlessly floating in the financial seas. {{lnk}} to set up goals.', 'Set a Goal', 'Mark as Done', 'I did this', 'Connect', '', 0),
(35, 'Disability Insurance - Increase Coverage to $X', 'Protection Planning', 10, 'addinsurance', '', '', 'action', 'short', 'Looks like your Disability Insurance coverage is fairly low. We recommend increasing that for you/ your spouse to $ {{amt}}. Contact your current provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(36, 'Disability Insurance - Get Policy for $X of Coverage', 'Protection Planning', 10, 'addinsurance', '', '', 'action', 'mid', 'Looks like you need Disability Income insurance coverage for yourself / your spouse of ${{amt}} month.  We can help if you {{lnk}}.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0),
(37, 'Pick Similar Investments that perform better', 'Investment Planning', 0, '', '', '', 'other', 'short', 'One or more of your investments are ranked below average.  To determine which investments they are and receive advice to improve, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(38, '?? Review Risk Tolerance Preference (for those users who are outliers)', 'Investment Planning', 0, '', '', '', 'other', 'short', 'You’ve indicated that you are very Tolerant of / Averse to risk.  That’s great, as long as you know what this means.  Please review your risk tolerance to make sure you are certain you like being at the very edge of the scale when it comes to risk.', 'Get Started', '', 'I read this', '', '', 0),
(39, 'Consider a Dollar Cost Averaging Strategy (Recurring Deposits)', 'Investment Planning', 0, '', '', '', 'other', 'short', 'Consider contributing to your retirement and savings accounts more frequently to take advantage of [link]Dollar Cost Averaging[link]', 'Get Started', '', 'I read this', '', '', 0),
(40, 'Consider Using Various Investment Styles to Help Diversify Risk (Investment Strategies)', 'Investment Planning', 0, '', '', '', 'other', 'short', 'It seems as if most of your investments are diversified across only a few investment styles.  Please consider broader diversification to help achieve your goals.  {{lnk}} for help.', 'Get Started', '', 'I read this', '', '', 0),
(41, 'Consider Using Non-Correlated, Alternative Investment Strategies for a Portion of Your Portfolio', 'Investment Planning', 0, '', '', '', 'other', 'short', 'Not having all of your eggs in one basket is important.  One of the easiest ways to do that is to include ', 'Get Started', '', 'I read this', '', '', 0),
(42, 'Consider Setting Up Your Investment Portfolios on Auto-Rebalance', 'Investment Planning', 0, '', '', '', 'other', 'short', '“Buying low and selling high” is rather important - or so we’ve heard.  If you’d like to do that with your money, you should consider setting your investment accounts up to automatically rebalance.  {{lnk}} to have us mange your investments for you.', 'Get Started', '', 'I read this', '', '', 0),
(43, 'Maximize Contribution to All Eligible Retirement Account Types (Roth IRA or Traditional IRA)', 'Retirement Planning', 0, '', '', '', 'other', 'short', 'You may be eligible to contribute to more retirement accounts.  {{lnk}} to find out why utlizing a Roth IRA / IRA Account makes sense for you.', 'Get Started', '', 'I read this', '', '', 0),
(44, 'Watch Video - Social Security and Your Future', 'Retirement Planning', 0, '', '', '', 'other', 'instant', '{{lnk}} to watch a short video on Social Security income', 'Get Started', '', 'I read this', '', '', 0),
(45, 'Watch Video - Total Return vs. Income or Gains', 'Retirement Planning', 0, '', '', '', 'other', 'instant', '{{lnk}} to watch short video on Total Return vs. Income or Gains', 'Get Started', '', 'I read this', '', '', 0),
(46, 'Consider Your Estate Planning Needs', 'Estate Planning', 0, '', '', '', 'other', 'mid', 'Having a plan in place that tells others how you', 'Get Started', '', 'I read this', '', '', 0),
(47, 'Consider Increasing / Decreasing Tax Withholding Amount using W4 Form at Work', 'Tax Planning', 0, '', '', '', 'other', 'short', 'You’re tax refunds are over $1,000 so you may want to consider decreasing your tax withholding from your wages and salary.  Ask your human resource department for a new W-4 form to decrease the amount of tax withholding from pay checks.', 'Get Started', '', 'I read this', '', '', 0),
(48, 'Watch Video - Tax Planning', 'Tax Planning', 0, '', '', '', 'other', 'instant', '{{lnk}} to watch short video on Tax Planning', 'Get Started', '', 'I read this', '', '', 0),
(49, 'Consider Using Roth IRA for Non-Deductible Retirement Account Funding', 'Tax Planning', 0, '', '', '', 'other', 'short', 'Since you already have enough tax deductions, you should consider opening a Roth Individual Retirement Account (IRA) and begin making non-tax-deductible contributions toward future savings goals.  {{lnk}} to do that.', 'Get Started', '', 'I read this', '', '', 0),
(50, 'Set Up Appropriate Type of Savings Account', 'Goal Planning', 0, '', '', '', 'other', 'short', 'It doesn''t appear you have many non-retirement accounts set up on FlexScore. Are you sure there aren''t any more accounts you want us to help monitor and manage for you?  If there are, {{lnk}} to set them up.', 'Get Started', '', 'I read this', '', '', 0),
(51, 'Set Up Appropriate Type of Retirement Funding Account', 'Goal Planning', 0, '', '', '', 'other', 'short', 'It doesn''t appear you have many retirement accounts set up on FlexScore. Are you sure there aren''t any more accounts you want us to help monitor and manage for you?  If there are, {{lnk}} to set them up.', 'Get Started', '', 'I read this', '', '', 0),
(52, 'Consider Debt Consolidation into Fewer, Lower Cost Loans', 'Goal Planning', 0, '', '', '', 'other', 'short', 'Your debt is split up into many different accounts with variable interest rates. We''ve detected that you may be able to conveniently consolidate many of your accounts into one or more loans with lower, set interest rates and a fixed payment schedule.  {{lnk}} to find out how.', 'Get Started', '', 'I read this', '', '', 0),
(53, 'Consider Refinance Options on Consumer Debt', 'Goal Planning', 0, '', '', '', 'other', 'short', 'Refinancing one or more of your credit cards can likely help you to pay off debt sooner. {{lnk}} to find out how.', 'Get Started', '', 'I read this', '', '', 0),
(54, 'Evaluate Amount of Consumer Debt Costs Compared to Income', 'Goal Planning', 0, '', '', '', 'other', 'short', 'It appears you may be spending too large of a chunk of your monthly income on consumer debt payments. You should seriously evaluate how to pay down your debts so that you can shift more cash flow into savings and retirement accounts. {{lnk}} to learn how.', 'Get Started', '', 'I read this', '', '', 0),
(55, 'Evaluate Amount of Housing Costs Compared to Income', 'Goal Planning', 0, '', '', '', 'other', 'short', 'It appears you may be spending too large of a chunk of your monthly income on housing costs. You should seriously consider how you might shift more cash flow into savings and retirement accounts. {{lnk}} to learn how.', 'Get Started', '', 'I read this', '', '', 0),
(56, 'Set-Up Systematic Savings Plan', 'Financial Planning', 0, '', '', '', 'other', 'short', 'It''s better to be consistent instead of sporadic in socking money away. How much do you think you can set aside monthly? Let''s set you up with this good habit. {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(57, 'Consider Strategies to Improve Credit Score', 'Financial Planning', 0, '', '', '', 'other', 'short', 'Consider these strategies to help improve your credit score. {{lnk}} to learn more.', 'Get Started', '', 'I read this', '', '', 0),
(58, '?? Set up other accounts like - CDs, Checking, etc', 'Financial Planning', 0, '', '', '', 'other', 'short', 'It doesn''t look like you have accounts set up for all your goals. If you do have accounts that you''re funding but haven''t linked them to FlexScore, {{lnk}}. If you still need to set those accounts up, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(59, 'Watch Video - Budgeting and Cash Flow', 'Financial Planning', 0, '', '', '', 'other', 'instant', '{{lnk}} to watch short video on Budgeting and Cash Flow.', 'Get Started', '', 'I read this', '', '', 0),
(60, 'Long Term Care Insurance - Increase Coverage to $X', 'Protection Planning', 0, '', '', '', 'other', 'short', 'Looks like your Long Term Care Insurance is fairly low. We recommend increasing that for you/ your spouse to $ ______ / day. Contact your current provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(61, 'Long Term Care Insurance - Get Policy for $X of Coverage', 'Protection Planning', 0, '', '', '', 'other', 'mid', 'Looks like you need Long Term Care Insurance. We recommend $ ______ / day for you/your spouse. To get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(62, 'Watch Video - Long Term Care Insurance', 'Protection Planning', 0, '', '', '', 'other', 'instant', '{{lnk}} to watch a short video on Long Term Care Insurance', 'Get Started', '', 'I read this', '', '', 0),
(63, 'Watch Video - Property & Casualty Insurance', 'Protection Planning', 0, '', '', '', 'other', 'instant', '{{lnk}} to watch a short a video on Property & Casualty Insurance', 'Get Started', '', 'I read this', '', '', 0),
(64, 'Watch Video - Health & Medical Insurance', 'Protection Planning', 0, '', '', '', 'other', 'instant', '{{lnk}} to watch a short video on Health & Medical Insurance', 'Get Started', '', 'I read this', '', '', 0),
(65, '?? Umbrella Insurance -', 'Protection Planning', 0, '', '', '', 'other', 'mid', 'You need some Umbrella Liability Insurance coverage for $ ______. You can contact your current insurance provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(66, '?? Home Owners/Renters Insurance -', 'Protection Planning', 0, '', '', '', 'other', 'mid', 'We''ve determined you do not live in a cardboard box; which means you need $ ______ home owners/ renters insurance. You can contact a current insurance provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(67, '?? Property Insurance -', 'Protection Planning', 0, '', '', '', 'other', 'mid', 'Looks like you need property insurance coverage in the amount of $ ________ for ', 'Get Started', '', 'I read this', '', '', 0),
(68, '?? Business Owners Insurance -', 'Protection Planning', 0, '', '', '', 'other', 'mid', 'Looks like you need business owners insurance for $ ______. You can contact your provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(69, '?? Professional Liability Insurance -', 'Protection Planning', 0, '', '', '', 'other', 'mid', 'Unless you relish a good lawsuit and the possibility of paying through your nose, its time to get yourself some professional liability insurance for $_________________. You can contact a current insurance provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0),
(70, 'Consider Flexibility of Assets Being Used to Fund Future Retirement (Liquidity Considerations)', 'Investment Planning', 0, '', '', '', 'other', 'short', 'Consider that more than 25% of  your ', 'Get Started', '', 'I read this', '', '', 0),
(71, 'Consider Concentration of Assets Being Used to Fund Future Retirement (Less than 10% in any individual asset)', 'Investment Planning', 0, '', '', '', 'other', 'short', 'Consider that more than 10% of  your ', 'Get Started', '', 'I read this', '', '', 0),
(72, 'Consider Using Investment Vehicles such as Mutual Funds and ETFs to Fulfill Your Goals', 'Investment Planning', 0, '', '', '', 'other', 'short', 'Because most of your nest egg assets are invested in individual stock holdings, please make sure that you have a good set of eyes overseeing them.  Individual stocks present a different set of risks and opportunities compared to mutual funds and exchange traded funds.  {{lnk}} to read about the advantages of mutual funds and exchange traded funds.', 'Get Started', '', 'I read this', '', '', 0),
(73, 'Inquire About Pension Eligibility and Elections', 'Retirement Planning', 0, '', '', '', 'other', 'short', 'You’ve indicated you have a pension plan that would pay you a retirement income guaranteed by an employer.  Have you recently looked at your beneficiaries listed on this pension?  This would be a good idea.', 'Get Started', '', 'I read this', '', '', 0),
(74, 'Retired - Consider Life Expectancy Risk', 'Retirement Planning', 0, '', '', '', 'other', 'short', 'The biggest financial risk a retiree faces is the potential of running out of money before running out of life.  It’s important to withdraw a sustainable amount to live on, and not any more. {{lnk}} to find out.', 'Get Started', '', 'I read this', '', '', 0),
(75, 'Retired - Examine Your Lifestyle Costs to Make Certain You Aren''t Overspending', 'Retirement Planning', 0, '', '', '', 'other', 'short', 'It appears that over the last 90 days you have been withdrawing more money from your portfolio that what is sustainable.  In other words, you don', 'Get Started', '', 'I read this', '', '', 0),
(76, 'Retired - Decrease current retirement account withdrawals', 'Retirement Planning', 0, '', '', '', 'other', 'short', 'Decrease current retirement account withdrawals by $________ / month to maintain your nest egg and ensure you', 'Get Started', '', 'I read this', '', '', 0),
(77, 'Create Informational Sheet & Location of Hidden Assets', 'Estate Planning', 0, '', '', '', 'other', 'short', 'You may have some very valuable things hidden in places that you only know about (cash in the mattress, treasure in the backyard). {{lnk}} to create an important document to protect those assets. This document can be kept in a safe-deposit box at a bank, or safe place of your choice.', 'Get Started', '', 'I read this', '', '', 0),
(78, '?? Consider Using More Tax Efficient Investments in your Taxable Investment Accounts', 'Tax Planning', 0, '', '', '', 'other', 'short', 'Here are some more tax efficient investments to consider. They can help you save quite a bit of money on unnecessary taxes.  {{lnk}} to see suggestions.  Or {{lnk}} to have us mange your investments for you.', 'Get Started', '', 'I read this', '', '', 0),
(79, 'Consider Charitable Donations', 'Tax Planning', 0, '', '', '', 'other', 'short', 'Giving to charities is a noble endeavor. Because you stand to benefit from a little extra tax savings, you might want to increase your charitable giving to help in the tax department.', 'Get Started', '', 'I read this', '', '', 0),
(80, 'Consider Feasibility of Achieving Goal X - We Predict a Less Than X% Chance of Success', 'Goal Planning', 0, '', '', '', 'other', 'mid', 'We detect a less than 50% chance of you achieving goal, "___________," in the time and manner you desire. You may need to make some serious changes to your stated goal or the actions you''re taking to achieve that goal.  {{lnk}} to change your goal.  Or you might want to {{lnk}} to use our ', 'Get Started', '', 'I read this', '', '', 0),
(81, 'Set-up Automatic Bill-Pay (Video?)', 'Financial Planning', 0, '', '', '', 'other', 'short', 'Handle your cash flow and get a handle on your bills by setting up auto-pay through your bank. {{lnk}} now.', 'Get Started', '', 'I read this', '', '', 0),
(82, '?? Develop and Stick to Monthly Spending Plan', 'Financial Planning', 0, '', '', '', 'other', 'mid', 'Uh-oh!  It looks like you might be spending more than you make. That''s going to happen every once in a while, but if it becomes a habit, you''re going in the wrong direction. To help you get a better hold on the situation, {{lnk}} to learn ways to improve.', 'Get Started', '', 'I read this', '', '', 0),
(83, 'NEW Set-Up College Savings Account for _________ with $X monthly contributions.', 'Financial Planning', 0, '', '', '', 'other', 'short', 'Good for you that you', 'Get Started', '', 'I read this', '', '', 0),
(84, 'Open Account to start Funding “New House Down Payment” Goal', 'Goal Planning', 10, 'addgoal', '', '', 'action', 'short', 'Good for you that you have a goal of "New House Down Payment."  To help save money for this cost, you should open a designated account and begin saving $600 monthly to meet your goal in time. {{lnk}} to begin', 'Set a Goal', 'Mark as Done', 'I did this', 'Connect', '', 1);


ALTER TABLE `actionstepmeta` ADD `status` ENUM('0', '1') NOT NULL DEFAULT '0' COMMENT '0=enable,1=disable' AFTER `priority`;

DROP TABLE risk;
CREATE TABLE IF NOT EXISTS `risk` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `risk` tinyint(4) NOT NULL,
  `stddev` float NOT NULL,
  `returnrate` float NOT NULL,
  `metric` float NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `risk` (`risk`, `stddev`, `returnrate`, `metric`) VALUES
(1, 4.7, 5.1, 0.11),
(2, 6.1, 5.8, 0.17),
(3, 7.2, 6.2, 0.22),
(4, 8.8, 6.8, 0.31),
(5, 10.4, 7.3, 0.43),
(6, 11.8, 7.8, 0.54),
(7, 12.9, 8.2, 0.64),
(8, 14.3, 8.7, 0.76),
(9, 15.7, 9.1, 0.86),
(10, 17.2, 9.3, 0.97);

UPDATE `leapscoremeta`.`otlt` SET `description` = 'tpbmtPDVPQs' WHERE `otlt`.`id` = 2003;
UPDATE `leapscoremeta`.`otlt` SET `description` = 'ctIeZ8s6RQI' WHERE `otlt`.`id` = 2004;
UPDATE `leapscoremeta`.`otlt` SET `description` = 'zlbYlgfAoB0' WHERE `otlt`.`id` = 2005;

UPDATE  `leapscoremeta`.`actionstepmeta` SET  `link` =  'ctIeZ8s6RQI' WHERE `actionstepmeta`.`actionid` = 5;
UPDATE  `leapscoremeta`.`actionstepmeta` SET  `link` =  'zlbYlgfAoB0' WHERE `actionstepmeta`.`actionid` = 8;
UPDATE  `leapscoremeta`.`actionstepmeta` SET  `link` =  'tpbmtPDVPQs' WHERE `actionstepmeta`.`actionid` = 18;
UPDATE  `leapscoremeta`.`actionstepmeta` SET  `link` =  'tpbmtPDVPQs' WHERE `actionstepmeta`.`actionid` = 19;

/* 06/14/2013 */
UPDATE `actionstepmeta` SET `link` = 'addmisc', linktype = 'action', `buttonstep1` = 'Set an Action', `buttonstep2` = 'Mark as Done', `linkstep1` = 'I did this', `linkstep2` = 'Connect' WHERE `actionid` IN (30,10,28,27,14,23,57,42);
UPDATE `actionstepmeta` SET `link` = 'addgoal', linktype = 'action', `buttonstep1` = 'Set a Goal', `buttonstep2` = 'Mark as Done', `linkstep1` = 'I did this', `linkstep2` = 'Connect' WHERE `actionid` IN (34,15,22);
UPDATE `actionstepmeta` SET `link` = 'addinsurance', linktype = 'action', `buttonstep1` = 'Set an Action', `buttonstep2` = 'Mark as Done', `linkstep1` = 'I did this', `linkstep2` = 'Connect' WHERE `actionid` IN (29,35,36,46);
UPDATE `actionstepmeta` SET `link` = 'connectaccount', linktype = 'action', `buttonstep1` = 'Set an Action', `buttonstep2` = 'Mark as Done', `linkstep1` = 'I did this', `linkstep2` = 'Connect' WHERE `actionid` IN (21,1,58,20);
UPDATE `actionstepmeta` SET `link` = 'addrisk', linktype = 'action', `buttonstep1` = 'Set an Action', `buttonstep2` = 'Mark as Done', `linkstep1` = 'I did this', `linkstep2` = 'Connect' WHERE `actionid` IN (33,38);
UPDATE `actionstepmeta` SET `link` = 'addasset', linktype = 'action', `buttonstep1` = 'Set an Action', `buttonstep2` = 'Mark as Done', `linkstep1` = 'I did this', `linkstep2` = 'Connect' WHERE `actionid` IN (12);
UPDATE `actionstepmeta` SET `link` = '', linktype = 'video', `buttonstep1` = 'Learn from Video', `buttonstep2` = '', `linkstep1` = "I'm done", `linkstep2` = '' WHERE `actionid` IN (64);
UPDATE `actionstepmeta` SET `link` = 'adddebt', linktype = 'action', `buttonstep1` = 'Set an Action', `buttonstep2` = 'Mark as Done', `linkstep1` = 'I did this', `linkstep2` = 'Connect' WHERE `actionid` IN (54,55);

/*  06/19/2013  */
UPDATE  `actionstepmeta` SET  `actionname` =  'Consider Using More Tax Efficient Investments in your Taxable Investment Accounts' WHERE  `actionstepmeta`.`actionid` =78;
UPDATE  `actionstepmeta` SET  `actionname` =  'Umbrella Insurance' WHERE  `actionstepmeta`.`actionid` =65;
UPDATE  `actionstepmeta` SET  `actionname` =  'Home Owners/Renters Insurance' WHERE  `actionstepmeta`.`actionid` =66;
UPDATE  `actionstepmeta` SET  `actionname` =  'Property Insurance' WHERE  `actionstepmeta`.`actionid` =67;
UPDATE  `actionstepmeta` SET  `actionname` =  'Business Owners Insurance' WHERE  `actionstepmeta`.`actionid` =68;
UPDATE  `actionstepmeta` SET  `actionname` =  'Professional Liability Insurance' WHERE  `actionstepmeta`.`actionid` =69;
UPDATE  `actionstepmeta` SET  `actionname` =  'Consider Using More Tax Efficient Investments in your Taxable Investment Accounts' WHERE  `actionstepmeta`.`actionid` =78;
UPDATE  `actionstepmeta` SET  `actionname` =  'Develop and Stick to Monthly Spending Plan' WHERE  `actionstepmeta`.`actionid` =82;
UPDATE  `actionstepmeta` SET  `actionname` =  'Set Up Appropriate Type of Savings Account' WHERE  `actionstepmeta`.`actionid` =50;
UPDATE  `actionstepmeta` SET  `actionname` =  'Set up other accounts like - CDs, Checking, etc.' WHERE  `actionstepmeta`.`actionid` =58;

UPDATE  `actionstepmeta` SET  `points` =  '10' WHERE `actionstepmeta`.`actionid` =8;
UPDATE  `actionstepmeta` SET  `points` =  '5' WHERE `actionstepmeta`.`actionid` =9;

UPDATE  `actionstepmeta` SET  `status` =  '1' WHERE `actionid` IN(4,64,34,22,19);

/** BEGIN - Melroy's Cleaning Up of Learning Videos */
DELETE FROM `actionstepmeta` where actionid in (4,5,8,62,63,64,59,44,45,18,19);
INSERT INTO `actionstepmeta` VALUES
(4, 'Life Insurance', 'Protection Planning', 5, '', 'Life Insurance', '', 'video', 'instant', 'Watch a short video on Life Insurance', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(5, 'Disability Insurance', 'Protection Planning', 5, 'ctIeZ8s6RQI', 'Disability Insurance', 'vid9', 'video', 'instant', 'Watch a short video on Disability Insurance', 'Watch Video', '', 'I''m done', '', '', 0, '0'),
(8, 'Investment Diversification', 'Investment Planning', 10, 'zlbYlgfAoB0', 'Investment Diversification', 'vid7', 'video', 'instant', 'Watch a short video on Investment Diversification', 'Watch Video', '', 'I''m done', '', '', 0, '0'),
(62, 'Inflation Considerations', 'Retirement Planning', 8, 'xCjZ1V4rhlw', 'Inflation Considerations', 'vid6', 'video', 'instant', 'Watch a short video on Inflation Considerations', 'Watch Video', '', 'I''m done', '', '', 0, '0'),
(63, 'Property & Casualty Insurance', 'Protection Planning', 5, '', 'Property & Casualty Insurance', 'vid10', 'video', 'instant', 'Watch a short a video on Property & Casualty Insurance', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(64, 'Health & Medical Insurance', 'Protection Planning', 5, '', 'Health & Medical Insurance', 'vid4', 'video', 'instant', 'Watch a short video on Health & Medical Insurance', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(59, 'Budgeting and Cash Flow', 'Financial Planning', 20, '', 'Budgeting and Cash Flow', 'vid8', 'video', 'instant', 'Watch a short video on Budgeting and Cash Flow.', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(44, 'Social Security', 'Retirement Planning', 6, '', 'Social Security', 'vid3', 'video', 'instant', 'Watch a short video on Social Security', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(45, 'Total Return vs. Income or Gains', 'Retirement Planning', 8, '', 'Total Return vs. Income or Gains', 'vid5', 'video', 'instant', 'Watch a short video on Total Return vs. Income or Gains', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(18, 'Debt Improvement Options', 'Debt Optimization', 5, 'tpbmtPDVPQs', 'Debt Improvement Options', 'vid2', 'video', 'instant', 'Watch a short video on Debt Improvement Options.', 'Watch Video', '', 'I''m done', '', '', 0, '0'),
(19, 'Knowledge of Debts and Liabilities', 'Debt Optimization', 5, 'qDCC-9z4cl8', 'Knowledge of Debts and Liabilities', 'vid1', 'video', 'instant', 'Watch a short video on Knowledge of Debts and Liabilites', 'Watch Video', '', 'I''m done', '', '', 0, '0');
/** END - Melroy's Cleaning Up of Learning Videos **/

/** BEGIN - Melroy's Cleaning Up of Connect Accounts - This is still incomplete */
UPDATE `actionstepmeta` set status='1' where actionid=1;
UPDATE `actionstepmeta` set points=10 where actionid=1;
/** END - Melroy's Cleaning Up of Connect Accounts **/

/** BEGIN - Melroy's Cleaning Up of Life Insurance - This is still incomplete */
UPDATE `actionstepmeta` set description='Looks like you need to increase your Life Insurance coverage for yourself to ${{amt}}. You can contact your current provider to see how they can help.' where actionid=2;
UPDATE `actionstepmeta` set points=24 where actionid=2;
UPDATE `actionstepmeta` set actionname='Life Insurance - Increase Coverage to ${{amt}}' where actionid=2;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=2;
UPDATE `actionstepmeta` set linkstep1='Update Insurance' where actionid=2;
UPDATE `actionstepmeta` set linkstep2='Update Insurance' where actionid=2;
UPDATE `actionstepmeta` set priority=1 where actionid=2;
UPDATE `actionstepmeta` set description='So you don''t believe you will ever die. Okay. But just in case you are mortal, we HIGHLY recommend getting yourself covered by at least ${{amt}}.' where actionid=3;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=3;
UPDATE `actionstepmeta` set linkstep1='Add Insurance' where actionid=3;
UPDATE `actionstepmeta` set linkstep2='Add Insurance' where actionid=3;
UPDATE `actionstepmeta` set priority=1 where actionid=3;
/** END - Melroy's Cleaning Up of Life Insurance  **/

/*  06/21/2013  */
TRUNCATE TABLE  `actionstepmeta`;


INSERT INTO `actionstepmeta` VALUES
(1, 'Connect Accounts', 'Profile Completeness', 35, 'connectaccount', '', '', 'action', 'instant', 'Maybe this account doesn''t have any online access, and that''s why you entered it manually. But, if you can figure out whether or not there''s website access, and if so, your username and password, that''d be great. It would help us help you.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '1'),
(2, 'Life Insurance - Increase Coverage to ${{amt}}', 'Protection Planning', 24, 'addinsurance', '', '', 'action', 'short', 'Looks like you need to increase your Life Insurance coverage for yourself to ${{amt}}. You can contact your current provider to see how they can help.', 'Get Started', 'Mark as Done', 'Update Insurance', 'Update Insurance', '', 1, '0'),
(3, 'Life Insurance - Get Policy for ${{amt}} of Coverage', 'Protection Planning', 24, 'addinsurance', '', '', 'action', 'mid', 'So you don''t believe you will ever die. Okay. But just in case you are mortal, we HIGHLY recommend getting yourself covered by at least ${{amt}}.', 'Get Started', 'Mark as Done', 'Add Insurance', 'Add Insurance', '', 1, '0'),
(4, 'Video - Life Insurance', 'Protection Planning', 5, '', 'Life Insurance', '', 'video', 'instant', 'Watch a short video on Life Insurance', 'Learn from Video', '', 'I''m done', '', '', 0, '1'),
(5, 'Video - Disability Insurance', 'Protection Planning', 5, 'ctIeZ8s6RQI', 'Disability Insurance', 'vid1', 'video', 'instant', 'Watch a short video on Disability Insurance', 'Learn from Video', '', 'I''m done', '', '', 10, '0'),
(6, 'Diversify Investments - Adjust to Match Risk Tolerance', 'Investment Planning', 30, '', '', '', 'other', 'short', 'Your chosen Risk Tolerance of 5 out of 10 calls for 35% in Risk Assets.  Currently you have 30%.  To better meet your goals please increase / decrease the amount you have in risk assets by 56000. See your investments and their respective risk amounts.', 'Get Started', '', 'I read this', '', '', 9, '0'),
(7, 'Complete Risk Tolerance Preference', 'Investment Planning', 15, '', '', '', 'other', 'short', 'You want high returns with low risk...we all do!  We’ll do our best to help with that. That''s why it''s so important for you to complete this.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(8, 'Video - Investment Diversification', 'Investment Planning', 10, 'zlbYlgfAoB0', 'Investment Diversification', 'vid7', 'video', 'instant', 'Watch short video on Investment Diversification', 'Learn from Video', '', 'I''m done', '', '', 0, '0'),
(9, 'Increase Monthly Contribution to ${{amt}} in your Asset Account', 'Retirement Planning', 5, '', '', '', 'other', 'instant', 'In your {{title}} account, increase your monthly contributions to {{amt}} per month in order to meet your retirement goal.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(10, 'Review Beneficiary Designations and Update if Needed', 'Retirement Planning', 5, 'addmisc', '', '', 'action', 'instant', 'Beneficiaries are those people (or charities) who will inherit your account upon your death.  You should make sure you have chosen beneficiaries for your {{title}} account, or at least review to make sure they are up to date.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(11, 'Inquire with Employer to Determine Maximum Employer Contribution Amounts', 'Retirement Planning', 8, '', '', '', 'other', 'short', 'Ask your work if they''ll provide a matching contribution plan. If they do that, find out how much you need to put away to get the most benefit. You don''t want to leave any money on the table.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(12, 'Adding IRA - Traditional or ROTH', 'Retirement Planning', 10, 'addasset', '', '', 'action', 'short', 'Looks like you don''t contribute to a company retirement plan account. That might not be available where you work. But check with Human Resources just in case you might be missing out.  Otherwise, you should open an Individual Retirement Account (IRA).', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(13, 'Complete Will and Other Estate Planning Docs', 'Estate Planning', 35, '', '', '', 'other', 'mid', 'Please complete the appropriate estate planning documents for your household.  A will or a trust, medical directives, and guardianship directions if you have dependents are all considered responsible documents to create.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(14, 'Update Will and Other Estate Planning Docs', 'Estate Planning', 15, 'addmisc', '', '', 'action', 'mid', 'Now it’s time to review your estate plan docs to make sure they are still relevant today.  If you don’t already have each of these, you may want to consider them: a will or a trust, medical directives, and guardianship directions if you have dependents.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(15, 'Identify Financial Goals, Cost of Goal, Time Period to achieve goal', 'Goal Planning', 5, 'addgoal', '', '', 'action', 'short', 'Having realistic financial goals to work toward is the basis for improving your situation. Considering the cost, time horizon, and priority of your goals is the first step toward improving your FlexScore...and your life!', 'Set a Goal', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(16, 'Consider Setting Up a Goal of Paying Off Consumer Debt', 'Goal Planning', 10, '', '', '', 'other', 'short', 'We can help you pay off that debt in no time. To set up a goal to pay off your debt sooner.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(17, 'Consider Refinance Options on Mortgage Debt', 'Goal Planning', 12, 'http://track.flexlinks.com/a.aspx?foid=32954365&fot=9999&foc=1', '', '', 'link', 'short', 'Refinancing your mortgage can likely help you to pay off that debt sooner. {{lnk}} to do it now.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(18, 'Video - Debt Optimization', 'Goal Planning', 5, 'tpbmtPDVPQs', 'Debt Optimization', 'vid1', 'video', 'instant', 'Watch a short video on Debt Optimization.', 'Learn from Video', '', 'I''m done', '', '', 0, '0'),
(19, 'Video - Total Debt Management', 'Goal Planning', 5, 'tpbmtPDVPQs', 'Total Debt Management', 'vid1', 'video', 'instant', 'Watch a short video on Knowledge of Debt', 'Learn from Video', '', 'I''m done', '', '', 0, '1'),
(20, 'Create Emergency Fund for Unplanned Costs', 'Financial Planning', 2, 'connectaccount', '', '', 'action', 'short', 'Putting an emergency fund in place is a key to personal financial success.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(21, 'Consider Auto-Linking All Financial Accounts', 'Financial Planning', 10, 'connectaccount', '', '', 'action', 'short', 'Let''s connect all your accounts for a better overview and easier ability for us to keep you up to date.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(22, 'Increase Savings', 'Financial Planning', 2, 'addgoal', '', '', 'action', 'short', 'You don''t have much in savings right now. Let''s set you up with a plan that''s easy to manage and gives you room to breathe.', 'Set a Goal', 'Mark as Done', 'I did this', 'Connect', '', 0, '1'),
(23, 'Obtain and Review Current Credit Score', 'Financial Planning', 5, 'addmisc', '', '', 'action', 'short', 'Need a better credit score? We''re here to help with that!  To monitor your score and protect your identity.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(24, 'Video - Inflation', 'Financial Planning', 8, '', 'Inflation', 'vid6', 'video', 'instant', 'Watch short video on Inflation.', 'Learn from Video', '', 'I''m done', '', '', 0, '0'),
(25, 'Give us more information on an asset', 'Profile Completeness', 25, 'addasset', '', '', 'action', 'instant', 'Please give us more details on this asset, “Bank account.” The more details you give us, the better we can help you.\n', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(26, 'Give us more information on a debt', 'Profile Completeness', 18, 'adddebt', '', '', 'action', 'instant', 'Please give us more details on this debt. The more details you give us, the better we can help you.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(27, 'Fill in Tax Section', 'Profile Completeness', 14, 'addmisc', '', '', 'action', 'instant', 'Yeah, we know taxes can be boring...but we still need to deal with them. Please complete the Tax Planning portion of FlexScore.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(28, 'Fill in Estate Planning', 'Profile Completeness', 35, 'addmisc', '', '', 'action', 'instant', 'Do you think if you start pondering a will that you''ll suddenly die (presumably of boredom!)?  Umm, that''s not how it works. Please complete the Estate Planning portion of FlexScore.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(29, 'Tell us more about Insurance (if checked in about you section)', 'Profile Completeness', 15, 'addinsurance', '', '', 'action', 'instant', 'We know your insurance situation isn''t exactly something you talk about much (let alone think about!) unless you have a pending issue. Nonetheless, you should tell us as much information about it as possible.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(30, 'Tell us more about Will & Trust  (if checked in about you section)', 'Profile Completeness', 5, 'addmisc', '', '', 'action', 'instant', 'Do you think if you start pondering a will that you''ll suddenly die (presumably of boredom!)?  Umm, that''s not how it works. Please complete the Estate Planning portion of FlexScore.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(31, 'Give us more detailed Income', 'Profile Completeness', 35, 'addincome', '', '', 'action', 'instant', 'Simply telling us an estimate of how much you earn is not exact enough.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(32, 'Give us more detail Expenses', 'Profile Completeness', 35, 'addexpense', '', '', 'action', 'instant', 'Simply telling us an estimate of how much you spend is not exact enough.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(33, 'Be more accurate on Risk Tolerance Slider (1-10)', 'Profile Completeness', 15, 'addrisk', '', '', 'action', 'instant', 'We know you want high returns with low risk...we all do!  Risk and reward are closely related and so important that you need to complete this.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(34, 'Add a Goal', 'Profile Completeness', 10, 'addgoal', '', '', 'action', 'instant', 'Have you ever jumped in your car and set off on a journey without a destination in mind?  Yeah, we haven''t either. That''s why it''s important that you set financial goals. Without a goal to work toward, you are just aimlessly floating in the financial seas.', 'Set a Goal', 'Mark as Done', 'I did this', 'Connect', '', 0, '1'),
(35, 'Disability Insurance - Increase Coverage to ${{amt}}', 'Protection Planning', 10, 'addinsurance', '', '', 'action', 'short', 'Looks like your Disability Insurance coverage is fairly low. We recommend increasing that for you/ your spouse to $ {{amt}}. Contact your current provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(36, 'Disability Insurance - Get Policy for ${{amt}} of Coverage', 'Protection Planning', 10, 'addinsurance', '', '', 'action', 'mid', 'Looks like you need Disability Income insurance coverage for yourself / your spouse of ${{amt}} month.  We can help if you {{lnk}}.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(37, 'Pick Similar Investments that perform better', 'Investment Planning', 2, '', '', '', 'other', 'short', 'One or more of your investments are ranked below average.  To determine which investments they are and receive advice to improve.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(38, 'Review Risk Tolerance Preference (for those users who are outliers)', 'Investment Planning', 2, 'addrisk', '', '', 'action', 'short', 'You\'ve indicated that you are very Tolerant of / Averse to risk.  That’s great, as long as you know what this means.  Please review your risk tolerance to make sure you are certain you like being at the very edge of the scale when it comes to risk.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(39, 'Consider a Dollar Cost Averaging Strategy (Recurring Deposits)', 'Investment Planning', 2, '', '', '', 'other', 'short', 'Consider contributing to your retirement and savings accounts more frequently to take advantage of [link]Dollar Cost Averaging[link]', 'Get Started', '', 'I read this', '', '', 0, '0'),
(40, 'Consider Using Various Investment Styles to Help Diversify Risk (Investment Strategies)', 'Investment Planning', 2, '', '', '', 'other', 'short', 'It seems as if most of your investments are diversified across only a few investment styles.  Please consider broader diversification to help achieve your goals.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(41, 'Consider Using Non-Correlated, Alternative Investment Strategies for a Portion of Your Portfolio', 'Investment Planning', 2, '', '', '', 'other', 'short', 'Not having all of your eggs in one basket is important.  One of the easiest ways to do that is to include ', 'Get Started', '', 'I read this', '', '', 0, '0'),
(42, 'Consider Setting Up Your Investment Portfolios on Auto-Rebalance', 'Investment Planning', 2, 'addmisc', '', '', 'action', 'short', '“Buying low and selling high” is rather important - or so we’ve heard.  If you’d like to do that with your money, you should consider setting your investment accounts up to automatically rebalance.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(43, 'Maximize Contribution to All Eligible Retirement Account Types (Roth IRA or Traditional IRA)', 'Retirement Planning', 2, '', '', '', 'other', 'short', 'You may be eligible to contribute to more retirement accounts.  Find out why utlizing a Roth IRA / IRA Account makes sense for you.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(44, 'Video - Social Security and Your Future', 'Retirement Planning', 2, '', '', '', 'other', 'instant', 'Watch a short video on Social Security income', 'Get Started', '', 'I read this', '', '', 0, '0'),
(45, 'Watch Video - Total Return vs. Income or Gains', 'Retirement Planning', 2, '', '', '', 'other', 'instant', 'Watch short video on Total Return vs. Income or Gains', 'Get Started', '', 'I read this', '', '', 0, '0'),
(46, 'Consider Your Estate Planning Needs', 'Estate Planning', 2, 'addinsurance', '', '', 'action', 'mid', 'Having a plan in place that tells others how you', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(47, 'Consider Increasing / Decreasing Tax Withholding Amount using W4 Form at Work', 'Tax Planning', 2, '', '', '', 'other', 'short', 'You’re tax refunds are over ${{amt}} so you may want to consider decreasing your tax withholding from your wages and salary.  Ask your human resource department for a new W-4 form to decrease the amount of tax withholding from pay checks.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(48, 'Video - Tax Planning', 'Tax Planning', 2, '', '', '', 'other', 'instant', 'Watch short video on Tax Planning', 'Get Started', '', 'I read this', '', '', 0, '0'),
(49, 'Consider Using Roth IRA for Non-Deductible Retirement Account Funding', 'Tax Planning', 2, '', '', '', 'other', 'short', 'Since you already have enough tax deductions, you should consider opening a Roth Individual Retirement Account (IRA) and begin making non-tax-deductible contributions toward future savings goals.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(50, 'Set Up Appropriate Type of Savings Account', 'Goal Planning', 2, '', '', '', 'other', 'short', 'It doesn''t appear you have many non-retirement accounts set up on FlexScore. Are you sure there aren''t any more accounts you want us to help monitor and manage for you?', 'Get Started', '', 'I read this', '', '', 0, '0'),
(51, 'Set Up Appropriate Type of Retirement Funding Account', 'Goal Planning', 2, '', '', '', 'other', 'short', 'It doesn''t appear you have many retirement accounts set up on FlexScore. Are you sure there aren''t any more accounts you want us to help monitor and manage for you?', 'Get Started', '', 'I read this', '', '', 0, '0'),
(52, 'Consider Debt Consolidation into Fewer, Lower Cost Loans', 'Goal Planning', 2, '', '', '', 'other', 'short', 'Your debt is split up into many different accounts with variable interest rates. We''ve detected that you may be able to conveniently consolidate many of your accounts into one or more loans with lower, set interest rates and a fixed payment schedule.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(53, 'Consider Refinance Options on Consumer Debt', 'Goal Planning', 2, '', '', '', 'other', 'short', 'Refinancing one or more of your credit cards can likely help you to pay off debt sooner.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(54, 'Evaluate Amount of Consumer Debt Costs Compared to Income', 'Goal Planning', 2, 'adddebt', '', '', 'action', 'short', 'It appears you may be spending too large of a chunk of your monthly income on consumer debt payments. You should seriously evaluate how to pay down your debts so that you can shift more cash flow into savings and retirement accounts.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(55, 'Evaluate Amount of Housing Costs Compared to Income', 'Goal Planning', 2, 'adddebt', '', '', 'action', 'short', 'It appears you may be spending too large of a chunk of your monthly income on housing costs. You should seriously consider how you might shift more cash flow into savings and retirement accounts.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(56, 'Set-Up Systematic Savings Plan', 'Financial Planning', 2, '', '', '', 'other', 'short', 'It''s better to be consistent instead of sporadic in socking money away. How much do you think you can set aside monthly? Let''s set you up with this good habit.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(57, 'Consider Strategies to Improve Credit Score', 'Financial Planning', 2, 'addmisc', '', '', 'action', 'short', 'Consider these strategies to help improve your credit score.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(58, 'Set up other accounts like - CDs, Checking, etc.', 'Financial Planning', 2, 'connectaccount', '', '', 'action', 'short', 'It doesn''t look like you have accounts set up for all your goals. If you do have accounts that you''re funding but haven''t linked them to FlexScore.', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '', 0, '0'),
(59, 'Video - Budgeting and Cash Flow', 'Financial Planning', 2, '', '', '', 'other', 'instant', 'Watch short video on Budgeting and Cash Flow.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(60, 'Long Term Care Insurance - Increase Coverage to ${{amt}}', 'Protection Planning', 2, '', '', '', 'other', 'short', 'Looks like your Long Term Care Insurance is fairly low. We recommend increasing that for you/ your spouse to ${{amt}}/ day. Contact your current provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(61, 'Long Term Care Insurance - Get Policy for ${{amt}} of Coverage', 'Protection Planning', 2, '', '', '', 'other', 'mid', 'Looks like you need Long Term Care Insurance. We recommend ${{amt}} / day for you/your spouse. To get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(62, 'Video - Long Term Care Insurance', 'Protection Planning', 2, '', '', '', 'video', 'instant', 'Watch a short video on Long Term Care Insurance', 'Learn from Video', '', 'I''m done', '', '', 0, '0'),
(63, 'Video - Property & Casualty Insurance', 'Protection Planning', 2, '', '', '', 'video', 'instant', 'Watch a short a video on Property & Casualty Insurance', 'Learn from Video', '', 'I''m done', '', '', 0, '0'),
(64, 'Video - Health & Medical Insurance', 'Protection Planning', 2, '', '', '', 'video', 'instant', 'Watch a short video on Health & Medical Insurance', 'Learn from Video', '', 'I''m done', '', '', 0, '1'),
(65, 'Umbrella Insurance', 'Protection Planning', 2, '', '', '', 'other', 'mid', 'You need some Umbrella Liability Insurance coverage for ${{amt}}. You can contact your current insurance provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(66, 'Home Owners/Renters Insurance', 'Protection Planning', 2, '', '', '', 'other', 'mid', 'We''ve determined you do not live in a cardboard box; which means you need ${{amt}} home owners/ renters insurance. You can contact a current insurance provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(67, 'Property Insurance', 'Protection Planning', 2, '', '', '', 'other', 'mid', 'Looks like you need property insurance coverage in the amount of ${{amt}} for ', 'Get Started', '', 'I read this', '', '', 0, '0'),
(68, 'Business Owners Insurance', 'Protection Planning', 2, '', '', '', 'other', 'mid', 'Looks like you need business owners insurance for ${{amt}}. You can contact your provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(69, 'Professional Liability Insurance', 'Protection Planning', 2, '', '', '', 'other', 'mid', 'Unless you relish a good lawsuit and the possibility of paying through your nose, its time to get yourself some professional liability insurance for ${{amt}}. You can contact a current insurance provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(70, 'Consider Flexibility of Assets Being Used to Fund Future Retirement (Liquidity Considerations)', 'Investment Planning', 2, '', '', '', 'other', 'short', 'Consider that more than 25% of  your ', 'Get Started', '', 'I read this', '', '', 0, '0'),
(71, 'Consider Concentration of Assets Being Used to Fund Future Retirement (Less than 10% in any individual asset)', 'Investment Planning', 2, '', '', '', 'other', 'short', 'Consider that more than 10% of  your ', 'Get Started', '', 'I read this', '', '', 0, '0'),
(72, 'Consider Using Investment Vehicles such as Mutual Funds and ETFs to Fulfill Your Goals', 'Investment Planning', 2, '', '', '', 'other', 'short', 'Because most of your nest egg assets are invested in individual stock holdings, please make sure that you have a good set of eyes overseeing them.  Individual stocks present a different set of risks and opportunities compared to mutual funds and exchange traded funds.  {{lnk}} to read about the advantages of mutual funds and exchange traded funds.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(73, 'Inquire About Pension Eligibility and Elections', 'Retirement Planning', 2, '', '', '', 'other', 'short', 'Youve indicated you have a pension plan that would pay you a retirement income guaranteed by an employer.  Have you recently looked at your beneficiaries listed on this pension?  This would be a good idea.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(74, 'Retired - Consider Life Expectancy Risk', 'Retirement Planning', 2, '', '', '', 'other', 'short', 'The biggest financial risk a retiree faces is the potential of running out of money before running out of life.  It’s important to withdraw a sustainable amount to live on, and not any more.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(75, 'Retired - Examine Your Lifestyle Costs to Make Certain You Aren''t Overspending', 'Retirement Planning', 2, '', '', '', 'other', 'short', 'It appears that over the last 90 days you have been withdrawing more money from your portfolio that what is sustainable.  In other words, you don', 'Get Started', '', 'I read this', '', '', 0, '0'),
(76, 'Retired - Decrease current retirement account withdrawals', 'Retirement Planning', 2, '', '', '', 'other', 'short', 'Decrease current retirement account withdrawals by ${{amt}} / month to maintain your nest egg and ensure you', 'Get Started', '', 'I read this', '', '', 0, '0'),
(77, 'Create Informational Sheet & Location of Hidden Assets', 'Estate Planning', 2, '', '', '', 'other', 'short', 'You may have some very valuable things hidden in places that you only know about (cash in the mattress, treasure in the backyard). {{lnk}} to create an important document to protect those assets. This document can be kept in a safe-deposit box at a bank, or safe place of your choice.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(78, 'Consider Using More Tax Efficient Investments in your Taxable Investment Accounts', 'Tax Planning', 2, '', '', '', 'other', 'short', 'Here are some more tax efficient investments to consider. They can help you save quite a bit of money on unnecessary taxes.  {{lnk}} to see suggestions.  Or {{lnk}} to have us mange your investments for you.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(79, 'Consider Charitable Donations', 'Tax Planning', 2, '', '', '', 'other', 'short', 'Giving to charities is a noble endeavor. Because you stand to benefit from a little extra tax savings, you might want to increase your charitable giving to help in the tax department.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(80, 'Consider Feasibility of Achieving Goal {{amt}} - We Predict a Less Than {{percent}}% Chance of Success', 'Goal Planning', 2, '', '', '', 'other', 'mid', 'We detect a less than 50% chance of you achieving goal, "{{title}}," in the time and manner you desire. You may need to make some serious changes to your stated goal or the actions you''re taking to achieve that goal.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(81, 'Set-up Automatic Bill-Pay (Video?)', 'Financial Planning', 2, '', '', '', 'other', 'short', 'Handle your cash flow and get a handle on your bills by setting up auto-pay through your bank.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(82, 'Develop and Stick to Monthly Spending Plan', 'Financial Planning', 2, '', '', '', 'other', 'mid', 'Uh-oh!  It looks like you might be spending more than you make. That''s going to happen every once in a while, but if it becomes a habit, you''re going in the wrong direction. To help you get a better hold on the situation, {{lnk}} to learn ways to improve.', 'Get Started', '', 'I read this', '', '', 0, '0'),
(83, 'Set-Up College Savings Account for {{title}} with ${{amt}} monthly contributions.', 'Financial Planning', 2, '', '', '', 'other', 'short', 'Good for you that you', 'Get Started', '', 'I read this', '', '', 0, '0'),
(84, 'Open Account to start Funding “New House Down Payment” Goal', 'Goal Planning', 10, 'addgoal', '', '', 'action', 'short', 'Good for you that you have a goal of "New House Down Payment."  To help save money for this cost, you should open a designated account and begin saving ${{amt}} monthly to meet your goal in time.', 'Set a Goal', 'Mark as Done', 'I did this', 'Connect', '', 1, '0');




/** BEGIN - Melroy's Cleaning Up of Learning Videos **/
DELETE FROM `actionstepmeta` where actionid in (4,5,8,62,63,64,59,44,45,18,19);
INSERT INTO `actionstepmeta` VALUES
(4, 'Life Insurance', 'Protection Planning', 5, '', 'Life Insurance', '', 'video', 'instant', 'Watch a short video on Life Insurance', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(5, 'Disability Insurance', 'Protection Planning', 5, 'ctIeZ8s6RQI', 'Disability Insurance', 'vid9', 'video', 'instant', 'Watch a short video on Disability Insurance', 'Watch Video', '', 'I''m done', '', '', 0, '0'),
(8, 'Investment Diversification', 'Investment Planning', 10, 'zlbYlgfAoB0', 'Investment Diversification', 'vid7', 'video', 'instant', 'Watch a short video on Investment Diversification', 'Watch Video', '', 'I''m done', '', '', 0, '0'),
(62, 'Inflation Considerations', 'Retirement Planning', 8, 'xCjZ1V4rhlw', 'Inflation Considerations', 'vid6', 'video', 'instant', 'Watch a short video on Inflation Considerations', 'Watch Video', '', 'I''m done', '', '', 0, '0'),
(63, 'Property & Casualty Insurance', 'Protection Planning', 5, '', 'Property & Casualty Insurance', 'vid10', 'video', 'instant', 'Watch a short a video on Property & Casualty Insurance', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(64, 'Health & Medical Insurance', 'Protection Planning', 5, '', 'Health & Medical Insurance', 'vid4', 'video', 'instant', 'Watch a short video on Health & Medical Insurance', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(59, 'Budgeting and Cash Flow', 'Financial Planning', 20, '', 'Budgeting and Cash Flow', 'vid8', 'video', 'instant', 'Watch a short video on Budgeting and Cash Flow.', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(44, 'Social Security', 'Retirement Planning', 6, '', 'Social Security', 'vid3', 'video', 'instant', 'Watch a short video on Social Security', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(45, 'Total Return vs. Income or Gains', 'Retirement Planning', 8, '', 'Total Return vs. Income or Gains', 'vid5', 'video', 'instant', 'Watch a short video on Total Return vs. Income or Gains', 'Watch Video', '', 'I''m done', '', '', 0, '1'),
(18, 'Debt Improvement Options', 'Debt Optimization', 5, 'tpbmtPDVPQs', 'Debt Improvement Options', 'vid2', 'video', 'instant', 'Watch a short video on Debt Improvement Options.', 'Watch Video', '', 'I''m done', '', '', 0, '0'),
(19, 'Knowledge of Debts and Liabilities', 'Debt Optimization', 5, 'qDCC-9z4cl8', 'Knowledge of Debts and Liabilities', 'vid1', 'video', 'instant', 'Watch a short video on Knowledge of Debts and Liabilites', 'Watch Video', '', 'I''m done', '', '', 0, '0');
/** END - Melroy's Cleaning Up of Learning Videos **/

/** BEGIN - Melroy's Cleaning Up of Connect Accounts - This is still incomplete **/
UPDATE `actionstepmeta` set status='1' where actionid=1;
UPDATE `actionstepmeta` set points=10 where actionid=1;
/** END - Melroy's Cleaning Up of Connect Accounts **/

/** BEGIN - Melroy's Cleaning Up of Life Insurance **/
UPDATE `actionstepmeta` set description='Looks like you need to increase your Life Insurance coverage for yourself to ${{amt}}. You can contact your current provider to see how they can help.' where actionid=2;
UPDATE `actionstepmeta` set points=24 where actionid=2;
UPDATE `actionstepmeta` set actionname='Life Insurance - Increase Coverage to ${{amt}}' where actionid=2;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=2;
UPDATE `actionstepmeta` set linkstep1='Update Insurance' where actionid=2;
UPDATE `actionstepmeta` set linkstep2='Update Insurance' where actionid=2;
UPDATE `actionstepmeta` set priority=1 where actionid=2;
UPDATE `actionstepmeta` set description='So you don''t believe you will ever die. Okay. But just in case you are mortal, we HIGHLY recommend getting yourself covered by at least ${{amt}}.' where actionid=3;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=3;
UPDATE `actionstepmeta` set linkstep1='Add Insurance' where actionid=3;
UPDATE `actionstepmeta` set linkstep2='Add Insurance' where actionid=3;
UPDATE `actionstepmeta` set priority=1 where actionid=3;
/** END - Melroy's Cleaning Up of Life Insurance  **/


/** BEGIN - Melroy's Cleaning Up of Disability Insurance **/
UPDATE `actionstepmeta` set description='Looks like your Disability Insurance coverage is fairly low. We recommend increasing that for you to ${{amt}}. Contact your current provider to see how they can help.' where actionid=35;
UPDATE `actionstepmeta` set points=18 where actionid=35;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=35;
UPDATE `actionstepmeta` set linkstep1='Update Insurance' where actionid=35;
UPDATE `actionstepmeta` set linkstep2='Update Insurance' where actionid=35;
UPDATE `actionstepmeta` set priority=1 where actionid=35;
UPDATE `actionstepmeta` set description='Looks like you need Disability Income insurance coverage for yourself of ${{amt}} month.' where actionid=36;
UPDATE `actionstepmeta` set points=18 where actionid=36;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=36;
UPDATE `actionstepmeta` set linkstep1='Add Insurance' where actionid=36;
UPDATE `actionstepmeta` set linkstep2='Add Insurance' where actionid=36;
UPDATE `actionstepmeta` set priority=1 where actionid=36;
/** END - Melroy's Cleaning Up of Disability Insurance  **/

/** BEGIN - Melroy's Cleaning Up of Risk **/
UPDATE `actionstepmeta` set link='addrisk' where actionid=7;
UPDATE `actionstepmeta` set linktype='action' where actionid=7;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=7;
UPDATE `actionstepmeta` set buttonstep2='Mark as Done' where actionid=7;
UPDATE `actionstepmeta` set linkstep1='Fill In Risk' where actionid=7;
UPDATE `actionstepmeta` set linkstep2='Fill In Risk' where actionid=7;
UPDATE `actionstepmeta` set priority=1 where actionid=7;
UPDATE `actionstepmeta` set actionname='Review Risk Tolerance Preference' where actionid=38;
UPDATE `actionstepmeta` set points=15 where actionid=38;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=38;
UPDATE `actionstepmeta` set buttonstep2='Mark as Done' where actionid=38;
UPDATE `actionstepmeta` set linkstep1='Review Risk' where actionid=38;
UPDATE `actionstepmeta` set linkstep2='Review Risk' where actionid=38;
UPDATE `actionstepmeta` set priority=1 where actionid=38;
/** END - Melroy's Cleaning Up of Risk  **/

/** BEGIN - Melroy's Cleaning Up of Consumer Debt **/
UPDATE `actionstepmeta` set linktype='action' where actionid=16;
UPDATE `actionstepmeta` set link='addgoal' where actionid=16;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=16;
UPDATE `actionstepmeta` set buttonstep2='Mark as Done' where actionid=16;
UPDATE `actionstepmeta` set priority=1 where actionid=16;
UPDATE `actionstepmeta` set linkstep1='Set Goal' where actionid=16;
UPDATE `actionstepmeta` set linkstep2='Set Goal' where actionid=16;
/** END - Melroy's Cleaning Up of Consumer Debt  **/


/** BEGIN - Melroy's Cleaning Up of #58 **/
UPDATE `actionstepmeta` set points=10 where actionid=58;
UPDATE `actionstepmeta` set link='addasset' where actionid=58;
UPDATE `actionstepmeta` set priority=2 where actionid=58;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=58;
UPDATE `actionstepmeta` set buttonstep2='Mark as Done' where actionid=58;
UPDATE `actionstepmeta` set linkstep1='Add Account' where actionid=58;
UPDATE `actionstepmeta` set linkstep2='Add Account' where actionid=58;
UPDATE `actionstepmeta` set description='It doesn''t look like you have accounts set up for all your goals. If you do have accounts that you''re funding but haven''t linked them to FlexScore, click the Add Account button found below.' where actionid=58;
/** END - Melroy's Cleaning Up of #58 **/

/** BEGIN - Melroy's Cleaning Up of #15 **/
UPDATE `actionstepmeta` set points=10 where actionid=15;
UPDATE `actionstepmeta` set priority=2 where actionid=15;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=15;
UPDATE `actionstepmeta` set buttonstep2='Mark as Done' where actionid=15;
UPDATE `actionstepmeta` set linkstep1='Set A Goal' where actionid=15;
UPDATE `actionstepmeta` set linkstep2='Set A Goal' where actionid=15;
/** END - Melroy's Cleaning Up of #15 **/

/*  06/24/2013   */
INSERT INTO `actionstepmeta` VALUES (85, 'Refinance Credit Card(s)', 'Debt Optimization', 2, 'https://www.capitalone.com/', '', '', 'link', 'short', 'Credit Card debt is always better when you are charged a lower interest rate. Make that happen by refinancing your card(s) listed below.<br>{{title}}.<br> {{lnk}} to begin.  ', 'Get Started', '', 'Check this', '', '', 0, '0');

/** BEGIN - Melroy's Cleaning Up of #31,32 **/
UPDATE `actionstepmeta` set priority=1 where actionid=31;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=31;
UPDATE `actionstepmeta` set linkstep1='Add Income' where actionid=31;
UPDATE `actionstepmeta` set linkstep2='Add Income' where actionid=31;
UPDATE `actionstepmeta` set description='Simply telling us an estimate of how much you earn is not exact enough. Please give us more details.' where actionid=31;
UPDATE `actionstepmeta` set priority=1 where actionid=32;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=32;
UPDATE `actionstepmeta` set linkstep1='Add Expenses' where actionid=32;
UPDATE `actionstepmeta` set linkstep2='Add Expenses' where actionid=32;
UPDATE `actionstepmeta` set description='Simply telling us an estimate of how much you spend is not exact enough. Please give us more details.' where actionid=32;
/** END - Melroy's Cleaning Up of #31,32 **/

UPDATE `actionstepmeta` set points=25 where actionid=85;
UPDATE `actionstepmeta` set points=50 where actionid=17;
UPDATE `actionstepmeta` set points=10 where points=2;

/** BEGIN - Melroy's Cleaning Up of #85 **/
UPDATE `actionstepmeta` set priority=1 where actionid=85;
UPDATE `actionstepmeta` set description='Credit Card debt is always better when you are charged a lower interest rate. Make that happen by refinancing your card(s) listed below.<br>{{title}}<br>{{lnk}} to begin.' where actionid=85;
/** END - Melroy's Cleaning Up of #85 **/

/** BEGIN - Melroy's Cleaning Up of #17 **/
UPDATE `actionstepmeta` set priority=1 where actionid=17;
UPDATE `actionstepmeta` set description='You are likely to pay off that mortgage debt sooner with a lower interest rate. Make that happen by refinancing your mortgage(s) listed below.<br>{{title}}<br>{{lnk}} to begin.' where actionid=17;
/** END - Melroy's Cleaning Up of #17 **/

/*  06/25/2013 */
UPDATE `actionstepmeta` SET `buttonstep2` =  'Get Started', `linkstep1` =  'I did this', `linkstep2` =  'I did this' WHERE `actionid` =16;
UPDATE `actionstepmeta` SET `linkstep1` =  'I did this', `linkstep2` =  'I did this' WHERE `actionid` =17;

UPDATE `actionstepmeta` SET `actionname` =  'Give us more detailed Expenses' WHERE `actionid` =32;
UPDATE `actionstepmeta` SET `link` =  'https://www.bankofamerica.com/deposits/savings/savings-accounts.go', `linktype` =  'link', `description` =  'Putting an emergency fund in place is a key to personal financial success.<br>{{lnk}} to begin.' WHERE `actionid` =20;

/*  06/26/2013 */
ALTER TABLE `actionstepmeta` ADD `externallink` VARCHAR( 255 ) NOT NULL AFTER `link`;

UPDATE `actionstepmeta` SET `link` =  'adddebt', `linkstep1` = 'Update Debts', `externallink` = 'http://track.flexlinks.com/a.aspx?foid=32954365&fot=9999&foc=1' WHERE `actionid` =17;
UPDATE `actionstepmeta` SET `link` =  'adddebt', `linkstep1` = 'Update Debts', `externallink` = 'https://www.capitalone.com/' WHERE `actionid` =85;
UPDATE `actionstepmeta` SET `link` =  'addasset', `linkstep1` = 'Update Assets', `externallink` = 'https://www.bankofamerica.com/deposits/savings/savings-accounts.go' WHERE `actionid` =20;

UPDATE `actionstepmeta` SET `actionname` = 'Life Insurance - Increase Coverage by ${{amt}}' WHERE `actionid` =2;
UPDATE `actionstepmeta` SET `description` = 'Looks like you need to increase your Life Insurance coverage for yourself by ${{amt}}. You can contact your current provider to see how they can help.' WHERE `actionid` =2;
UPDATE `actionstepmeta` SET `actionname` = 'Adding IRA - Traditional or Roth' WHERE `actionid` =12;

UPDATE `actionstepmeta` set actionname='Life Insurance - Increase Coverage by ${{amt}}' where actionid=2;
UPDATE `actionstepmeta` set actionname='Disability Insurance - Increase Coverage by ${{amt}}' where actionid=35;

UPDATE `actionstepmeta` set link='addasset' where actionid=10;
UPDATE `actionstepmeta` set description='Beneficiaries are those people (or charities) who will inherit your account upon your death. You should make sure you have chosen beneficiaries for your <b>{{title}}</b> account, or at least review to make sure they are up to date.' where actionid=10;
UPDATE `actionstepmeta` set linktype='action' where actionid=10;
UPDATE `actionstepmeta` set priority=1 where actionid=10;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=10;
UPDATE `actionstepmeta` set linkstep1='Update Account' where actionid=10;
UPDATE `actionstepmeta` set linkstep2='Update Account' where actionid=10;


UPDATE `actionstepmeta` set actionname='Adding IRA - Traditional or Roth' where actionid=12;
UPDATE `actionstepmeta` set priority=1 where actionid=12;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=12;
UPDATE `actionstepmeta` set linkstep1='Add IRA' where actionid=12;
UPDATE `actionstepmeta` set linkstep2='Add IRA' where actionid=12;

UPDATE `actionstepmeta` set link='addestate' where actionid=14;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=14;
UPDATE `actionstepmeta` set linkstep1='Update' where actionid=14;
UPDATE `actionstepmeta` set linkstep2='Update' where actionid=14;

UPDATE `actionstepmeta` set points=25 where actionid=17;

UPDATE `actionstepmeta` set link='addmore' where actionid=23;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=23;
UPDATE `actionstepmeta` set linkstep1='Update' where actionid=23;
UPDATE `actionstepmeta` set linkstep2='Update' where actionid=23;

UPDATE `actionstepmeta` set link='addtax' where actionid=27;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=27;
UPDATE `actionstepmeta` set linkstep1='Update' where actionid=27;
UPDATE `actionstepmeta` set linkstep2='Update' where actionid=27;

UPDATE `actionstepmeta` set actionname='Tell us more about Insurance' where actionid=29;
UPDATE `actionstepmeta` set actionname='Tell us more about Will & Trust' where actionid=30;
UPDATE `actionstepmeta` set link='addestate' where actionid=30;

UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=29;
UPDATE `actionstepmeta` set linkstep1='Update' where actionid=29;
UPDATE `actionstepmeta` set linkstep2='Update' where actionid=29;
UPDATE `actionstepmeta` set buttonstep1='Get Started' where actionid=30;
UPDATE `actionstepmeta` set linkstep1='Update' where actionid=30;
UPDATE `actionstepmeta` set linkstep2='Update' where actionid=30;

/*  06/27/2013  */

UPDATE `actionstepmeta` SET `description` = 'You''ve indicated that you are very {{title}} risk. That’s great, as long as you know what this means. Please review your risk tolerance to make sure you are certain you like being at the very edge of the scale when it comes to risk.' WHERE `actionid` =38;
UPDATE `actionstepmeta` SET `description` = 'Please give us more details on this asset, “{{title}}”. The more details you give us, the better we can help you.' WHERE `actionid` =25;
UPDATE `actionstepmeta` SET `description` = 'Please give us more details on this debt, “{{title}}”. The more details you give us, the better we can help you.' WHERE `actionid` =26;

UPDATE `actionstepmeta` SET `points` = '35' WHERE `actionid` =31;
UPDATE `actionstepmeta` SET `points` = '35' WHERE `actionid` =32;

/*  06/28/2013  */
UPDATE `actionstepmeta` SET `status` =  '1' WHERE `actionid` =21;


UPDATE `actionstepmeta` SET `status` =  '0' WHERE `actionid` = 4;
UPDATE `actionstepmeta` SET `link` =  'rWn_cLvaO5c' WHERE `actionid` = 4;
UPDATE `actionstepmeta` SET `vkey` =  'vid4' WHERE `actionid` = 4;
UPDATE `actionstepmeta` SET `vkey` =  '' WHERE `actionid` = 64;

/*  07/01/2013   */
UPDATE `actionstepmeta` SET `description` = 'You''ve indicated that you are very {{title}} risk. That''s great, as long as you know what this means. Please review your risk tolerance to make sure you are certain you like being at the very edge of the scale when it comes to risk.' WHERE `actionid` =38;
UPDATE `actionstepmeta` SET `description` = 'Looks like your Disability Insurance coverage is fairly low. We recommend increasing that by ${{amt}}. Contact your current provider to see how they can help. Or, if you want to get a quote from someone we recommend, Click Here.' WHERE `actionstepmeta`.`actionid` =35;
UPDATE `actionstepmeta` SET `actionname` = 'Disability Insurance' WHERE `actionid` =35;
UPDATE `actionstepmeta` SET `link` = 'addestate' WHERE `actionid` =28;

ALTER TABLE `actionstepmeta` ADD `articles` TEXT NOT NULL AFTER `description`;
UPDATE `actionstepmeta` SET `articles` = 'How Much Can You Afford?#https://www.flexscore.com/learningcenter?type=post&id=1677|Homeownership#https://www.flexscore.com/learningcenter?type=post&id=1675' WHERE `actionid` =55;
UPDATE `actionstepmeta` SET `articles` = 'Repairing Poor Credit#https://www.flexscore.com/learningcenter?type=post&id=255|How Can I Repair My Poor Credit?#https://www.flexscore.com/learningcenter?type=post&id=874|Establishing a Credit History#https://www.flexscore.com/learningcenter?type=post&id=868' WHERE `actionid` =57;
UPDATE `actionstepmeta` SET `articles` = 'Group Health Insurance#https://www.flexscore.com/learningcenter?type=post&id=903|Individual Health Insurance#https://www.flexscore.com/learningcenter?type=post&id=914' WHERE `actionid` =64;

/*  07/02/2013  */
UPDATE `actionstepmeta` SET `link` = 'editdebt' WHERE `actionid` =26;
UPDATE `actionstepmeta` SET `link` = 'editasset' WHERE `actionid` =25;

ALTER TABLE `actionstepmeta` ADD `wfpointlink` TEXT NOT NULL COMMENT 'Map point to SE' AFTER `articles`;
UPDATE `actionstepmeta` SET `wfpointlink` = '15', `points` = '12' WHERE `actionid` =10;

/*  07/03/2013  */
UPDATE `actionstepmeta` SET `link` = 'addmore' WHERE `actionid` IN (42,57);
UPDATE `actionstepmeta` SET `actionname` =  'Disability Insurance - Increase Coverage by ${{amt}}' WHERE `actionid` =35;
UPDATE `actionstepmeta` SET `status` =  '0' WHERE `actionid` =64;

UPDATE `actionstepmeta` SET `link` =  'learnmore', `buttonstep1` =  'Get Started', `linkstep1` =  'Learn more', `linkstep2` =  'Learn more' WHERE `actionid` IN (55,57);
UPDATE `actionstepmeta` SET `link` =  'learnmore', `linktype` =  'action', `buttonstep1` =  'Get Started', `buttonstep2` =  'Mark as Done', `linkstep1` =  'Learn more', `linkstep2` =  'Learn more' WHERE `actionid` =64;

UPDATE `actionstepmeta` SET `description` = 'Health insurance is important and one of the ingredients to both a healthy body and healthy finances.  Read these articles to help you understand why you should consider getting health insurance.' WHERE `actionid` =64;
UPDATE `actionstepmeta` SET `points` = '0' WHERE `actionid` =64;

/*  07/09/2013  */
UPDATE `actionstepmeta` SET `link` =  'addestate', `description` = 'Having a plan in place that tells others how you’d like to have your assets managed and dependents cared for is very important. Consider your situation and the legacy you’d leave today if you don’t have a plan in place.' WHERE `actionid` =46;
UPDATE `actionstepmeta` SET `description` = 'Please give us more details on this asset. <br>{{title}}<br> The more details you give us, the better we can help you.' WHERE `actionid` =25;
UPDATE `actionstepmeta` SET `description` = 'Please give us more details on this debt.<br>{{title}}<br>. The more details you give us, the better we can help you.' WHERE `actionid` =26;

/*   07/10/2013  */
UPDATE `actionstepmeta` SET `description` = 'Beneficiaries are those people (or charities) who will inherit your account upon your death.  You should make sure you have chosen beneficiaries for your account.<br>{{title}}<br> Or at least review to make sure they are up to date.' WHERE `actionid` =10;
UPDATE `actionstepmeta` SET `vkey` =  'vid10' WHERE `actionid` = 4;
UPDATE `actionstepmeta` SET `link` =  'reviewrisk' WHERE `actionid` =38;

/*   07/11/2013  */
UPDATE `actionstepmeta` SET `points` = '5' WHERE `actionid` =64;
UPDATE `actionstepmeta` SET `link` = 'reviewasset' WHERE `actionid` =20;
UPDATE `actionstepmeta` SET `link` = 'planestate' WHERE `actionid` =46;
/*** Till this Pushed to Producton on 07/12/2013 **/
/*   07/16/2013  */
UPDATE `actionstepmeta` SET `wfpointlink` = '53' WHERE `actionid` =64;

/*  07/18/2013 */
UPDATE `actionstepmeta` SET `externallink` = 'http://www.bankrate.com/credit-cards.asp' WHERE `actionid` =85;
UPDATE `actionstepmeta` SET `externallink` = 'http://track.flexlinks.com/a.aspx?foid=44321988&fot=9999&foc=1' WHERE `actionid` =20;
/*  07/22/2013 */
UPDATE `actionstepmeta` SET `externallink` = 'http://www.bankrate.com/credit-cards.aspx' WHERE `actionid` =85;
/*** Till this Pushed to Producton on 07/22/2013 **/

/*  07/22/2013 */
UPDATE `actionstepmeta` SET `wfpointlink` = '28' WHERE `actionid` =7;
UPDATE `actionstepmeta` SET `wfpointlink` = '39' WHERE `actionid` =30;

/*  07/23/2013  */
/* https://staging.flexscore.com/service/api/peerrank */

DROP TABLE IF EXISTS `regiondetails`;
CREATE TABLE IF NOT EXISTS `regiondetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `region` int(11) NOT NULL COMMENT '1-Northeast,2-Midwest,3-South,4-West',
  `division` int(11) NOT NULL,
  `state` varchar(200) NOT NULL,
  `statecode` varchar(100) NOT NULL,
  `zipcoderangeprefix` varchar(100) NOT NULL COMMENT 'Range1|Range2',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `regiondetails` (`id`, `region`, `division`, `state`, `statecode`, `zipcoderangeprefix`) VALUES
(1, 1, 1, 'Maine', 'ME', '039-049'),
(2, 1, 1, 'New Hampshire', 'NH', '030-038'),
(3, 1, 1, 'Vermont', 'VT', '050-054|056-059'),
(4, 1, 1, 'Massachussets', 'MA', '010-027|055-055'),
(5, 1, 1, 'Rhode Island', 'RI', '028-029'),
(6, 1, 1, 'Connecticut', 'CT', '060-069'),
(7, 1, 2, 'New York', 'NY', '100-149'),
(8, 1, 2, 'Pennsylvania', 'PA', '150-196'),
(9, 1, 2, 'New Jersey', 'NJ', '070-089'),
(10, 2, 3, 'Wisconsin', 'WI', '530-549'),
(11, 2, 3, 'Michigan', 'MI', '480-499'),
(12, 2, 3, 'Illinois', 'IL', '600-629'),
(13, 2, 3, 'Indiana', 'IN', '460-479'),
(14, 2, 3, 'Ohio', 'OH', '430-459'),
(15, 2, 4, 'Missori', 'MO', '630-658'),
(16, 2, 4, 'North Dakota', 'ND', '580-588'),
(17, 2, 4, 'South Dakota', 'SD', '570-577'),
(18, 2, 4, 'Nebraska', 'NE', '680-693'),
(19, 2, 4, 'Kansas', 'KS', '660-679'),
(20, 2, 4, 'Minnesota', 'MN', '550-567'),
(21, 2, 4, 'Iowa', 'IA', '500-528'),
(22, 3, 5, 'Delaware', 'DE', '197-199'),
(23, 3, 5, 'Maryland', 'MD', '206-219'),
(24, 3, 5, 'Columbia', 'DC', '569-569|202-205'),
(25, 3, 5, 'Virginia', 'VA', '201-201|220-246'),
(26, 3, 5, 'West Virginia', 'WV', '247-268'),
(27, 3, 5, 'North Carolina', 'NC', '270-279'),
(28, 3, 5, 'South Carolina', 'SC', '290-299'),
(29, 3, 5, 'Gerogia', 'GA', '300-319'),
(30, 3, 5, 'Florida', 'FL', '320-349'),
(31, 3, 6, 'Kentucky', 'KY', '400-427'),
(32, 3, 6, 'Tennessee', 'TN', '370-385'),
(33, 3, 6, 'Mississippi', 'MS', '386-397'),
(34, 3, 6, 'Alabama', 'AL', '350-369'),
(35, 3, 7, 'Oklahama', 'OK', '730-749'),
(36, 3, 7, 'Texas', 'TX', '733|750-799'),
(37, 3, 7, 'Arkansas', 'AR', '716-729'),
(38, 3, 7, 'Louisiana', 'LA', '700-714'),
(39, 4, 8, 'Idaho', 'ID', '832-838'),
(40, 4, 8, 'Montana', 'MT', '590-599'),
(41, 4, 8, 'Wyoming', 'WY', '820-831'),
(42, 4, 8, 'Nevada', 'NV', '889-898'),
(43, 4, 8, 'Utah', 'UT', '840-847'),
(44, 4, 8, 'Colorado', 'CO', '800-816'),
(45, 4, 8, 'Arizona', 'AZ', '855-865'),
(46, 4, 8, 'New Mexico', 'NM', '870-884'),
(47, 4, 9, 'Alaska', 'AK', '995-999'),
(48, 4, 9, 'Washington', 'WA', '980-994'),
(49, 4, 9, 'Oregon', 'OR', '970-979'),
(50, 4, 9, 'California', 'CA', '900-961'),
(51, 4, 9, 'Hawaii', 'HI', '967-968');

/*  07/24/2013  */
UPDATE `actionstepmeta` SET `externallink` =  'https://www.trustedid.com/registration.php?promoRefCode=TIDBZDV5823',
`description` =  'Need a better credit score? We''re here to help with that!  To monitor your score and protect your identity, {{lnk}}.' WHERE `actionid` =23;
UPDATE `actionstepmeta` SET `externallink` =  'http://www.insure.com/',
`description` = 'Looks like you need to increase your Life Insurance coverage for yourself/ your spouse to ${{amt}}. You can contact your current provider to see how they can help.  Or, if you want to get a quote from someone we recommend, {{lnk}}.' WHERE `actionid` =2;
UPDATE `actionstepmeta` SET `externallink` =  'http://www.insure.com/',
`description` = 'So you don''t believe you/your spouse will ever die. Okay. But just in case you are mortal, we HIGHLY recommend getting yourself covered by at least ${{amt}}. {{lnk}} for help with that.' WHERE `actionid` =3;
UPDATE `actionstepmeta` SET `externallink` =  'http://lztrk.com/?a=4931&c=300&p=r&s1=',
`description` = 'Please complete the appropriate estate planning documents for your household.  A will or a trust, medical directives, and guardianship directions if you have dependents are all considered responsible documents to create. {{lnk}} to begin.' WHERE `actionid` =13;
UPDATE `actionstepmeta` SET `externallink` =  'http://lztrk.com/?a=4931&c=300&p=r&s1=',
`description` = 'Now it’s time to review your estate plan docs to make sure they are still relevant today.  If you don’t already have each of these, you may want to consider them: a will or a trust, medical directives, and guardianship directions if you have dependents. To create any or all of the above, {{lnk}}.' WHERE `actionid` =14;
UPDATE `actionstepmeta` SET `externallink` =  'http://www.insure.com/',
`description` =  'Looks like you need Disability Income insurance coverage for yourself / your spouse of ${{amt}} month.  We can help if you {{lnk}}.' WHERE `actionid` =36;
UPDATE `actionstepmeta` SET `externallink` =  'http://www.insure.com/',
`description` = 'Looks like your Disability Insurance coverage is fairly low. We recommend increasing that by ${{amt}}. Contact your current provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.' WHERE `actionid` =35;
UPDATE `actionstepmeta` SET `externallink` =  'https://client.schwab.com/Login/AccountOpen/GAOLaunch.aspx?application_type=IRA&ira_type=Roth',
`description` = 'Since you already have enough tax deductions, you should consider opening a Roth Individual Retirement Account (IRA) and begin making non-tax-deductible contributions toward future savings goals.  {{lnk}} to do that.' WHERE `actionid` =49;
UPDATE `actionstepmeta` SET `externallink` =  'http://track.flexlinks.com/a.aspx?foid=24025405&fot=9999&foc=1',
`description` = 'Your debt is split up into many different accounts with variable interest rates. We''ve detected that you may be able to conveniently consolidate many of your accounts into one or more loans with lower, set interest rates and a fixed payment schedule. {{lnk}} to find out how.' WHERE `actionid` =52;
UPDATE `actionstepmeta` SET `externallink` =  'http://www.bankrate.com/credit-cards.aspx',
`description` =  'Refinancing one or more of your credit cards can likely help you to pay off debt sooner. {{lnk}} to find out how.' WHERE `actionid` =53;
UPDATE `actionstepmeta` SET `externallink` =  'http://www.insure.com/' WHERE `actionid` =60;
UPDATE `actionstepmeta` SET `externallink` =  'http://www.insure.com/' WHERE `actionid` =61;
UPDATE `actionstepmeta` SET `externallink` =  'http://www.geico.com/getaquote/umbrella/' WHERE `actionid` =65;
UPDATE `actionstepmeta` SET `externallink` =  'http://track.flexlinks.com/a.aspx?foid=32523840&fot=9999&foc=1' WHERE `actionid` =66;
UPDATE `actionstepmeta` SET `externallink` =  'http://track.flexlinks.com/a.aspx?foid=32523838&fot=9999&foc=1',
`description` = 'Looks like you need property insurance coverage in the amount of ${{amt}} for “{{title}}”. You can contact a current insurance provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.' WHERE `actionid` =67;

UPDATE `regiondetails` SET `zipcoderangeprefix` =  '733-733|750-799' WHERE `statecode` ='TX';
/* 07/25/2013 - Upto this patch applied on staging */

/* 07/30/2013 */
DROP TABLE IF EXISTS `peerranking`;

CREATE TABLE IF NOT EXISTS `peerranking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `region` int(11) NOT NULL COMMENT '1-Northeast,2-Midwest,3-South,4-West',
  `baseage` int(11) NOT NULL,
  `agegroup` varchar(150) NOT NULL,
  `weight` decimal(10,2) NOT NULL COMMENT 'Weighted Score for Group',
  `size` int(11) NOT NULL COMMENT 'Total Sample Size',
  `income` int(11) NOT NULL COMMENT 'Average Weighted Income',
  `assets` int(11) NOT NULL COMMENT 'Average Weighted Assets ',
  `savingsrate` decimal(10,1) NOT NULL COMMENT 'Average Weighted Savings Rate ',
  `debtresi` int(11) NOT NULL COMMENT 'Average Debt-Residential',
  `debtequityline` int(11) NOT NULL COMMENT 'Average Debt-Equity Line',
  `debtinstallment` int(11) NOT NULL COMMENT 'Average Debt-Installment',
  `debtcc` int(11) NOT NULL COMMENT 'Average Debt-Credit Card',
  `debtloc` int(11) NOT NULL COMMENT 'Average Debt-LOC-Unsecured',
  `debtother` int(11) NOT NULL COMMENT 'Average Debt-Other',
  `score` int(11) NOT NULL COMMENT 'Score',
  `weightage1` int(11) NOT NULL,
  `weightage2` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `peerranking` (`id`, `region`, `baseage`, `agegroup`, `weight`, `size`, `income`, `assets`, `savingsrate`, `debtresi`, `debtequityline`, `debtinstallment`, `debtcc`, `debtloc`, `debtother`, `score`, `weightage1`, `weightage2`) VALUES
(1, 1, 21, '21|22|23', '13.00', 7, 39222, 49010, '5.8', 40800, 2581, 8666, 619, 36, 110, 365, 0, 0),
(2, 1, 22, '21|22|23|24', '21.64', 11, 46686, 45939, '4.8', 40800, 2581, 8666, 619, 36, 110, 358, 0, 0),
(3, 1, 23, '21|22|23|24|25', '37.55', 17, 46949, 39314, '4.6', 40800, 2581, 8666, 619, 36, 110, 339, 0, 0),
(4, 1, 24, '22|23|24|25|26', '44.82', 21, 48147, 41089, '4.3', 40800, 2581, 8666, 619, 36, 110, 345, 0, 0),
(5, 1, 25, '23|24|25|26|27', '55.40', 24, 51767, 39316, '4.0', 40800, 2581, 8666, 619, 36, 110, 339, 0, 0),
(6, 1, 26, '24|25|26|27|28', '60.91', 26, 54931, 44402, '4.6', 40800, 2581, 8666, 619, 36, 110, 355, 0, 0),
(7, 1, 27, '25|26|27|28|29', '69.99', 32, 50319, 44035, '4.5', 40800, 2581, 8666, 619, 36, 110, 353, 0, 0),
(8, 1, 28, '26|27|28|29|30', '71.59', 38, 54977, 61253, '5.2', 40800, 2581, 8666, 619, 36, 110, 387, 0, 0),
(9, 1, 29, '27|28|29|30|31', '69.72', 36, 57738, 63029, '4.9', 40800, 2581, 8666, 619, 36, 110, 390, 0, 0),
(10, 1, 30, '28|29|30|31|32', '75.01', 46, 62707, 78040, '5.6', 40800, 2581, 8666, 619, 36, 110, 406, 0, 0),
(11, 1, 31, '29|30|31|32|33', '65.89', 46, 68339, 84334, '5.5', 40800, 2581, 8666, 619, 36, 110, 411, 0, 0),
(12, 1, 32, '30|31|32|33|34', '61.69', 44, 76936, 95473, '5.8', 40800, 2581, 8666, 619, 36, 110, 419, 0, 0),
(13, 1, 33, '31|32|33|34|35', '56.92', 40, 77518, 85111, '4.8', 48756, 2932, 8697, 815, 40, 164, 402, 0, 0),
(14, 1, 34, '32|33|34|35|36', '80.83', 55, 73108, 84505, '4.2', 59606, 3410, 8739, 1081, 45, 237, 387, 0, 0),
(15, 1, 35, '33|34|35|36|37', '77.40', 49, 69204, 75097, '2.7', 68404, 3798, 8773, 1297, 49, 296, 364, 0, 0),
(16, 1, 36, '34|35|36|37|38', '108.00', 61, 67794, 68632, '2.5', 75365, 4105, 8800, 1468, 53, 343, 368, 0, 0),
(17, 1, 37, '35|36|37|38|39', '115.75', 63, 64658, 67840, '2.1', 80582, 4335, 8820, 1596, 55, 378, 358, 0, 0),
(18, 1, 38, '36|37|38|39|40', '124.94', 70, 63361, 69433, '2.3', 80582, 4335, 8820, 1596, 55, 378, 361, 0, 0),
(19, 1, 39, '37|38|39|40|41', '118.44', 71, 68250, 127644, '2.7', 80582, 4335, 8820, 1596, 55, 378, 424, 0, 0),
(20, 1, 40, '38|39|40|41|42', '125.92', 82, 70211, 139532, '2.8', 80582, 4335, 8820, 1596, 55, 378, 431, 0, 0),
(21, 1, 41, '39|40|41|42|43', '106.15', 81, 74330, 207831, '3.4', 80582, 4335, 8820, 1596, 55, 378, 453, 0, 0),
(22, 1, 42, '40|41|42|43|44', '116.36', 95, 74279, 239204, '3.9', 80582, 4335, 8820, 1596, 55, 378, 460, 0, 0),
(23, 1, 43, '41|42|43|44|45', '108.13', 95, 80854, 314646, '3.8', 78731, 5031, 8371, 1599, 72, 395, 470, 0, 0),
(24, 1, 44, '42|43|44|45|46', '105.08', 95, 77915, 300290, '4.0', 76386, 5912, 7802, 1604, 93, 417, 469, 0, 0),
(25, 1, 45, '43|44|45|46|47', '94.86', 90, 76662, 332801, '4.2', 74328, 6684, 7303, 1607, 112, 435, 472, 0, 0),
(26, 1, 46, '44|45|46|47|48', '109.12', 96, 73344, 286647, '3.5', 71788, 7639, 6687, 1612, 135, 458, 495, 0, 0),
(27, 1, 47, '45|46|47|48|49', '100.18', 92, 77521, 278809, '3.5', 68856, 8740, 5976, 1617, 162, 485, 495, 0, 0),
(28, 1, 48, '46|47|48|49|50', '114.37', 106, 75780, 254461, '3.9', 68856, 8740, 5976, 1617, 162, 485, 492, 0, 0),
(29, 1, 49, '47|48|49|50|51', '126.66', 115, 73982, 240810, '3.6', 68856, 8740, 5976, 1617, 162, 485, 490, 0, 0),
(30, 1, 50, '48|49|50|51|52', '134.31', 124, 76197, 257498, '3.6', 68856, 8740, 5976, 1617, 162, 485, 492, 0, 0),
(31, 1, 51, '49|50|51|52|53', '125.08', 131, 75118, 270645, '3.8', 68856, 8740, 5976, 1617, 162, 485, 494, 0, 0),
(32, 1, 52, '50|51|52|53|54', '119.91', 138, 76632, 338687, '3.9', 68856, 8740, 5976, 1617, 162, 485, 500, 0, 0),
(33, 1, 53, '51|52|53|54|55', '114.22', 144, 74796, 346568, '3.5', 64757, 8426, 5641, 1505, 203, 465, 502, 0, 0),
(34, 1, 54, '52|53|54|55|56', '100.74', 150, 79389, 422938, '3.7', 61099, 8146, 5343, 1405, 239, 447, 508, 0, 0),
(35, 1, 55, '53|54|55|56|57', '95.23', 153, 75225, 420058, '3.8', 58385, 7938, 5121, 1331, 266, 433, 508, 0, 0),
(36, 1, 56, '54|55|56|57|58', '92.39', 152, 75255, 415447, '4.3', 54988, 7678, 4844, 1238, 300, 417, 536, 0, 0),
(37, 1, 57, '55|56|57|58|59', '98.58', 166, 72412, 351562, '4.0', 51992, 7448, 4599, 1156, 330, 402, 534, 0, 0),
(38, 1, 58, '56|57|58|59|60', '99.51', 169, 71890, 388062, '4.2', 51992, 7448, 4599, 1156, 330, 402, 536, 0, 0),
(39, 1, 59, '57|58|59|60|61', '102.64', 175, 67468, 380228, '4.3', 51992, 7448, 4599, 1156, 330, 402, 535, 0, 0),
(40, 1, 60, '58|59|60|61|62', '114.62', 196, 68905, 381496, '3.9', 51992, 7448, 4599, 1156, 330, 402, 535, 0, 0),
(41, 1, 61, '59|60|61|62|63', '113.49', 194, 69229, 401843, '4.0', 51992, 7448, 4599, 1156, 330, 402, 536, 0, 0),
(42, 1, 62, '60|61|62|63|64', '112.39', 187, 66493, 419148, '4.0', 51992, 7448, 4599, 1156, 330, 402, 537, 0, 0),
(43, 1, 63, '61|62|63|64|65', '127.53', 199, 64354, 355095, '3.6', 46052, 7147, 4207, 1042, 250, 336, 536, 0, 0),
(44, 1, 64, '62|63|64|65|66', '130.25', 194, 65952, 345298, '3.4', 41633, 6923, 3916, 957, 191, 286, 537, 0, 0),
(45, 1, 65, '63|64|65|66|67', '122.42', 177, 66640, 384390, '3.5', 36631, 6670, 3586, 861, 123, 230, 542, 0, 0),
(46, 1, 66, '64|65|66|67|68', '124.24', 177, 67457, 396655, '3.1', 32891, 6480, 3339, 789, 73, 189, 543, 0, 0),
(47, 1, 67, '65|66|67|68|69', '119.40', 169, 70076, 424566, '3.4', 28350, 6250, 3040, 702, 12, 138, 545, 0, 0),
(48, 1, 68, '66|67|68|69|70', '95.09', 140, 73077, 500998, '3.4', 28350, 6250, 3040, 702, 12, 138, 547, 0, 0),
(49, 1, 69, '67|68|69|70|71', '78.11', 118, 70727, 517531, '3.1', 28350, 6250, 3040, 702, 12, 138, 547, 0, 0),
(50, 1, 70, '68|69|70|71|72', '75.15', 111, 67511, 465753, '2.9', 28350, 6250, 3040, 702, 12, 138, 546, 0, 0),
(51, 1, 71, '69|70|71|72|73', '64.77', 94, 62398, 443012, '2.9', 28350, 6250, 3040, 702, 12, 138, 545, 0, 0),
(52, 1, 72, '70|71|72|73|74', '56.15', 81, 62264, 376414, '1.9', 28350, 6250, 3040, 702, 12, 138, 544, 0, 0),
(53, 1, 73, '71|72|73|74|75', '49.31', 69, 58531, 311521, '1.7', 26294, 5718, 2769, 661, 10, 154, 543, 0, 0),
(54, 1, 74, '72|73|74|75', '41.40', 56, 56928, 281546, '1.5', 25816, 5594, 2706, 652, 10, 158, 542, 0, 0),
(55, 1, 75, '73|74|75', '24.52', 33, 55150, 227284, '1.5', 24050, 5137, 2473, 617, 9, 171, 540, 0, 0),
(56, 2, 21, '21|22|23', '34.73', 11, 47043, 40095, '6.0', 40800, 2581, 8666, 619, 36, 110, 341, 0, 0),
(57, 2, 22, '21|22|23|24', '53.01', 16, 46941, 40794, '4.7', 40800, 2581, 8666, 619, 36, 110, 344, 0, 0),
(58, 2, 23, '21|22|23|24|25', '76.62', 25, 43179, 39870, '4.9', 40800, 2581, 8666, 619, 36, 110, 341, 0, 0),
(59, 2, 24, '22|23|24|25|26', '90.08', 31, 43547, 37876, '4.4', 40800, 2581, 8666, 619, 36, 110, 334, 0, 0),
(60, 2, 25, '23|24|25|26|27', '97.24', 34, 42312, 37001, '4.5', 40800, 2581, 8666, 619, 36, 110, 330, 0, 0),
(61, 2, 26, '24|25|26|27|28', '105.69', 40, 40251, 38511, '4.4', 40800, 2581, 8666, 619, 36, 110, 336, 0, 0),
(62, 2, 27, '25|26|27|28|29', '105.58', 42, 40385, 40601, '4.8', 40800, 2581, 8666, 619, 36, 110, 343, 0, 0),
(63, 2, 28, '26|27|28|29|30', '105.95', 45, 44777, 44746, '4.3', 40800, 2581, 8666, 619, 36, 110, 355, 0, 0),
(64, 2, 29, '27|28|29|30|31', '108.58', 47, 46444, 52739, '4.4', 40800, 2581, 8666, 619, 36, 110, 373, 0, 0),
(65, 2, 30, '28|29|30|31|32', '115.80', 51, 50166, 54522, '3.8', 40800, 2581, 8666, 619, 36, 110, 376, 0, 0),
(66, 2, 31, '29|30|31|32|33', '101.62', 48, 53957, 61325, '3.6', 40800, 2581, 8666, 619, 36, 110, 387, 0, 0),
(67, 2, 32, '30|31|32|33|34', '102.52', 53, 56042, 69176, '3.7', 40800, 2581, 8666, 619, 36, 110, 397, 0, 0),
(68, 2, 33, '31|32|33|34|35', '109.10', 56, 54023, 68777, '3.5', 51456, 3051, 8707, 881, 41, 182, 380, 0, 0),
(69, 2, 34, '32|33|34|35|36', '113.17', 63, 53179, 76182, '3.5', 61007, 3472, 8744, 1115, 46, 246, 375, 0, 0),
(70, 2, 35, '33|34|35|36|37', '117.13', 67, 55750, 82943, '3.8', 68707, 3811, 8774, 1304, 49, 298, 373, 0, 0),
(71, 2, 36, '34|35|36|37|38', '129.78', 73, 56924, 83452, '3.6', 74043, 4047, 8795, 1435, 52, 334, 393, 0, 0),
(72, 2, 37, '35|36|37|38|39', '138.52', 73, 52424, 72356, '2.8', 80582, 4335, 8820, 1596, 55, 378, 367, 0, 0),
(73, 2, 38, '36|37|38|39|40', '126.54', 72, 55002, 84951, '3.2', 80582, 4335, 8820, 1596, 55, 378, 386, 0, 0),
(74, 2, 39, '37|38|39|40|41', '119.14', 70, 58189, 89807, '3.3', 80582, 4335, 8820, 1596, 55, 378, 393, 0, 0),
(75, 2, 40, '38|39|40|41|42', '118.73', 74, 55222, 90251, '2.9', 80582, 4335, 8820, 1596, 55, 378, 393, 0, 0),
(76, 2, 41, '39|40|41|42|43', '125.73', 86, 56117, 133662, '3.0', 80582, 4335, 8820, 1596, 55, 378, 427, 0, 0),
(77, 2, 42, '40|41|42|43|44', '127.49', 91, 59567, 136153, '3.2', 80582, 4335, 8820, 1596, 55, 378, 429, 0, 0),
(78, 2, 43, '41|42|43|44|45', '140.42', 101, 59346, 151033, '3.2', 77796, 5382, 8144, 1601, 80, 404, 437, 0, 0),
(79, 2, 44, '42|43|44|45|46', '153.25', 112, 59094, 149350, '2.6', 75347, 6302, 7550, 1605, 103, 426, 438, 0, 0),
(80, 2, 45, '43|44|45|46|47', '143.20', 110, 62908, 168328, '2.8', 73440, 7018, 7088, 1609, 120, 443, 446, 0, 0),
(81, 2, 46, '44|45|46|47|48', '141.65', 114, 61659, 144787, '3.1', 70605, 8083, 6400, 1614, 146, 469, 465, 0, 0),
(82, 2, 47, '45|46|47|48|49', '140.15', 126, 71104, 172549, '3.6', 68856, 8740, 5976, 1617, 162, 485, 476, 0, 0),
(83, 2, 48, '46|47|48|49|50', '138.67', 127, 70994, 155682, '3.6', 68856, 8740, 5976, 1617, 162, 485, 470, 0, 0),
(84, 2, 49, '47|48|49|50|51', '142.33', 133, 69399, 196782, '3.8', 68856, 8740, 5976, 1617, 162, 485, 482, 0, 0),
(85, 2, 50, '48|49|50|51|52', '173.03', 151, 63153, 177518, '3.5', 68856, 8740, 5976, 1617, 162, 485, 477, 0, 0),
(86, 2, 51, '49|50|51|52|53', '162.45', 154, 62633, 188358, '3.3', 68856, 8740, 5976, 1617, 162, 485, 480, 0, 0),
(87, 2, 52, '50|51|52|53|54', '155.03', 158, 57037, 200075, '3.3', 68856, 8740, 5976, 1617, 162, 485, 482, 0, 0),
(88, 2, 53, '51|52|53|54|55', '154.04', 175, 55917, 229910, '3.3', 64809, 8430, 5646, 1506, 202, 465, 490, 0, 0),
(89, 2, 54, '52|53|54|55|56', '146.34', 183, 56263, 215613, '3.4', 61299, 8161, 5359, 1411, 237, 448, 489, 0, 0),
(90, 2, 55, '53|54|55|56|57', '136.30', 198, 59608, 230658, '3.9', 57613, 7879, 5058, 1310, 274, 430, 494, 0, 0),
(91, 2, 56, '54|55|56|57|58', '140.56', 211, 61855, 280586, '3.9', 54629, 7650, 4814, 1228, 304, 415, 528, 0, 0),
(92, 2, 57, '55|56|57|58|59', '149.50', 226, 61756, 274446, '3.9', 51992, 7448, 4599, 1156, 330, 402, 528, 0, 0),
(93, 2, 58, '56|57|58|59|60', '152.65', 238, 61881, 288800, '3.9', 51992, 7448, 4599, 1156, 330, 402, 530, 0, 0),
(94, 2, 59, '57|58|59|60|61', '149.07', 240, 61404, 299348, '3.9', 51992, 7448, 4599, 1156, 330, 402, 530, 0, 0),
(95, 2, 60, '58|59|60|61|62', '140.27', 238, 61828, 356587, '4.0', 51992, 7448, 4599, 1156, 330, 402, 534, 0, 0),
(96, 2, 61, '59|60|61|62|63', '141.93', 238, 59206, 310965, '4.1', 51992, 7448, 4599, 1156, 330, 402, 531, 0, 0),
(97, 2, 62, '60|61|62|63|64', '141.20', 237, 56927, 322243, '3.8', 51992, 7448, 4599, 1156, 330, 402, 532, 0, 0),
(98, 2, 63, '61|62|63|64|65', '145.66', 226, 56267, 317878, '3.3', 47494, 7220, 4302, 1070, 269, 352, 533, 0, 0),
(99, 2, 64, '62|63|64|65|66', '149.89', 227, 56564, 318803, '3.3', 43035, 6994, 4008, 984, 210, 302, 535, 0, 0),
(100, 2, 65, '63|64|65|66|67', '151.33', 216, 53214, 289419, '3.0', 38529, 6766, 3711, 898, 149, 252, 536, 0, 0),
(101, 2, 66, '64|65|66|67|68', '142.85', 201, 53459, 302153, '2.6', 33878, 6530, 3405, 808, 86, 200, 539, 0, 0),
(102, 2, 67, '65|66|67|68|69', '136.59', 184, 52587, 292244, '2.8', 28350, 6250, 3040, 702, 12, 138, 541, 0, 0),
(103, 2, 68, '66|67|68|69|70', '118.54', 162, 53344, 266334, '2.7', 28350, 6250, 3040, 702, 12, 138, 539, 0, 0),
(104, 2, 69, '67|68|69|70|71', '100.02', 135, 52010, 276642, '2.7', 28350, 6250, 3040, 702, 12, 138, 540, 0, 0),
(105, 2, 70, '68|69|70|71|72', '81.52', 110, 54389, 271645, '2.6', 28350, 6250, 3040, 702, 12, 138, 540, 0, 0),
(106, 2, 71, '69|70|71|72|73', '70.80', 89, 51633, 252210, '2.6', 28350, 6250, 3040, 702, 12, 138, 539, 0, 0),
(107, 2, 72, '70|71|72|73|74', '58.92', 73, 52188, 267061, '1.6', 28350, 6250, 3040, 702, 12, 138, 539, 0, 0),
(108, 2, 73, '71|72|73|74|75', '51.92', 68, 51742, 277802, '1.9', 24640, 5290, 2550, 629, 9, 167, 542, 0, 0),
(109, 2, 74, '72|73|74|75', '40.54', 52, 52582, 233436, '1.5', 23499, 4994, 2400, 606, 8, 176, 541, 0, 0),
(110, 2, 75, '73|74|75', '30.08', 40, 54044, 247947, '1.7', 22044, 4618, 2208, 577, 7, 187, 542, 0, 0),
(111, 3, 21, '21|22|23', '57.84', 20, 48295, 23051, '1.0', 40800, 2581, 8666, 619, 36, 110, 273, 0, 0),
(112, 3, 22, '21|22|23|24', '84.83', 28, 44700, 27593, '1.6', 40800, 2581, 8666, 619, 36, 110, 282, 0, 0),
(113, 3, 23, '21|22|23|24|25', '115.13', 37, 41528, 31728, '2.4', 40800, 2581, 8666, 619, 36, 110, 306, 0, 0),
(114, 3, 24, '22|23|24|25|26', '139.58', 48, 39186, 31415, '2.1', 40800, 2581, 8666, 619, 36, 110, 305, 0, 0),
(115, 3, 25, '23|24|25|26|27', '140.01', 48, 40858, 38638, '3.1', 40800, 2581, 8666, 619, 36, 110, 336, 0, 0),
(116, 3, 26, '24|25|26|27|28', '147.05', 52, 39983, 40428, '3.9', 40800, 2581, 8666, 619, 36, 110, 342, 0, 0),
(117, 3, 27, '25|26|27|28|29', '137.34', 50, 39691, 38048, '4.1', 40800, 2581, 8666, 619, 36, 110, 334, 0, 0),
(118, 3, 28, '26|27|28|29|30', '124.54', 49, 40053, 37593, '3.5', 40800, 2581, 8666, 619, 36, 110, 332, 0, 0),
(119, 3, 29, '27|28|29|30|31', '105.21', 45, 43755, 45024, '4.3', 40800, 2581, 8666, 619, 36, 110, 356, 0, 0),
(120, 3, 30, '28|29|30|31|32', '113.09', 58, 40685, 50234, '3.7', 40800, 2581, 8666, 619, 36, 110, 368, 0, 0),
(121, 3, 31, '29|30|31|32|33', '114.20', 61, 41794, 57303, '3.2', 40800, 2581, 8666, 619, 36, 110, 381, 0, 0),
(122, 3, 32, '30|31|32|33|34', '126.25', 74, 45509, 65042, '2.7', 40800, 2581, 8666, 619, 36, 110, 392, 0, 0),
(123, 3, 33, '31|32|33|34|35', '144.72', 88, 46440, 68819, '3.1', 50746, 3020, 8705, 863, 41, 177, 381, 0, 0),
(124, 3, 34, '32|33|34|35|36', '161.48', 100, 49510, 72759, '2.8', 57906, 3335, 8732, 1039, 44, 225, 375, 0, 0),
(125, 3, 35, '33|34|35|36|37', '160.81', 102, 53765, 73084, '2.6', 66542, 3716, 8766, 1251, 48, 284, 363, 0, 0),
(126, 3, 36, '34|35|36|37|38', '143.16', 95, 52575, 73926, '2.5', 72626, 3984, 8789, 1401, 51, 325, 381, 0, 0),
(127, 3, 37, '35|36|37|38|39', '143.96', 92, 55523, 88744, '2.5', 80582, 4335, 8820, 1596, 55, 378, 391, 0, 0),
(128, 3, 38, '36|37|38|39|40', '137.17', 90, 60352, 95633, '2.5', 80582, 4335, 8820, 1596, 55, 378, 399, 0, 0),
(129, 3, 39, '37|38|39|40|41', '142.28', 96, 60010, 102788, '2.6', 80582, 4335, 8820, 1596, 55, 378, 406, 0, 0),
(130, 3, 40, '38|39|40|41|42', '151.96', 102, 63172, 105164, '2.8', 80582, 4335, 8820, 1596, 55, 378, 408, 0, 0),
(131, 3, 41, '39|40|41|42|43', '171.92', 123, 70149, 190173, '3.9', 80582, 4335, 8820, 1596, 55, 378, 449, 0, 0),
(132, 3, 42, '40|41|42|43|44', '171.44', 133, 73050, 211409, '4.4', 80582, 4335, 8820, 1596, 55, 378, 454, 0, 0),
(133, 3, 43, '41|42|43|44|45', '160.30', 129, 73896, 235197, '4.4', 79128, 4881, 8467, 1599, 68, 392, 459, 0, 0),
(134, 3, 44, '42|43|44|45|46', '159.42', 128, 74553, 232469, '4.4', 76735, 5780, 7887, 1603, 90, 413, 460, 0, 0),
(135, 3, 45, '43|44|45|46|47', '156.69', 129, 74034, 243051, '4.8', 74037, 6794, 7233, 1608, 115, 438, 462, 0, 0),
(136, 3, 46, '44|45|46|47|48', '156.41', 125, 68442, 170139, '3.6', 71295, 7824, 6568, 1613, 140, 463, 474, 0, 0),
(137, 3, 47, '45|46|47|48|49', '168.84', 134, 61320, 150401, '3.1', 68856, 8740, 5976, 1617, 162, 485, 468, 0, 0),
(138, 3, 48, '46|47|48|49|50', '200.74', 153, 59916, 129729, '2.8', 68856, 8740, 5976, 1617, 162, 485, 459, 0, 0),
(139, 3, 49, '47|48|49|50|51', '213.21', 169, 61228, 149563, '2.9', 68856, 8740, 5976, 1617, 162, 485, 468, 0, 0),
(140, 3, 50, '48|49|50|51|52', '218.84', 175, 60846, 170741, '2.6', 68856, 8740, 5976, 1617, 162, 485, 475, 0, 0),
(141, 3, 51, '49|50|51|52|53', '213.05', 189, 62301, 169948, '2.8', 68856, 8740, 5976, 1617, 162, 485, 475, 0, 0),
(142, 3, 52, '50|51|52|53|54', '199.48', 194, 65628, 190755, '3.0', 68856, 8740, 5976, 1617, 162, 485, 480, 0, 0),
(143, 3, 53, '51|52|53|54|55', '191.92', 218, 66451, 211456, '3.2', 64292, 8390, 5603, 1492, 207, 463, 487, 0, 0),
(144, 3, 54, '52|53|54|55|56', '179.83', 230, 65394, 220744, '3.3', 60571, 8105, 5300, 1391, 245, 444, 491, 0, 0),
(145, 3, 55, '53|54|55|56|57', '180.73', 250, 62411, 235458, '3.1', 57456, 7867, 5045, 1306, 276, 429, 495, 0, 0),
(146, 3, 56, '54|55|56|57|58', '180.05', 255, 61367, 255294, '3.1', 54637, 7651, 4815, 1229, 304, 415, 525, 0, 0),
(147, 3, 57, '55|56|57|58|59', '180.11', 261, 60957, 252908, '3.1', 51992, 7448, 4599, 1156, 330, 402, 526, 0, 0),
(148, 3, 58, '56|57|58|59|60', '165.94', 250, 63368, 302497, '3.1', 51992, 7448, 4599, 1156, 330, 402, 531, 0, 0),
(149, 3, 59, '57|58|59|60|61', '166.61', 247, 61767, 309729, '3.3', 51992, 7448, 4599, 1156, 330, 402, 531, 0, 0),
(150, 3, 60, '58|59|60|61|62', '148.81', 234, 66711, 347006, '3.6', 51992, 7448, 4599, 1156, 330, 402, 534, 0, 0),
(151, 3, 61, '59|60|61|62|63', '151.25', 237, 64680, 320368, '3.9', 51992, 7448, 4599, 1156, 330, 402, 532, 0, 0),
(152, 3, 62, '60|61|62|63|64', '165.75', 253, 66160, 365499, '3.9', 51992, 7448, 4599, 1156, 330, 402, 534, 0, 0),
(153, 3, 63, '61|62|63|64|65', '170.62', 246, 65427, 335128, '3.9', 48052, 7248, 4339, 1081, 277, 358, 534, 0, 0),
(154, 3, 64, '62|63|64|65|66', '178.36', 255, 66223, 333167, '3.8', 42628, 6973, 3982, 976, 204, 297, 536, 0, 0),
(155, 3, 65, '63|64|65|66|67', '178.03', 247, 64178, 307456, '3.7', 38975, 6788, 3741, 906, 155, 257, 537, 0, 0),
(156, 3, 66, '64|65|66|67|68', '171.61', 232, 64584, 331779, '3.1', 34668, 6570, 3457, 823, 97, 209, 540, 0, 0),
(157, 3, 67, '65|66|67|68|69', '163.66', 215, 59879, 275723, '2.7', 28350, 6250, 3040, 702, 12, 138, 540, 0, 0),
(158, 3, 68, '66|67|68|69|70', '161.08', 212, 58221, 268944, '3.1', 28350, 6250, 3040, 702, 12, 138, 540, 0, 0),
(159, 3, 69, '67|68|69|70|71', '142.71', 182, 56462, 253156, '2.8', 28350, 6250, 3040, 702, 12, 138, 539, 0, 0),
(160, 3, 70, '68|69|70|71|72', '140.68', 181, 56084, 253373, '2.7', 28350, 6250, 3040, 702, 12, 138, 539, 0, 0),
(161, 3, 71, '69|70|71|72|73', '130.90', 170, 56109, 249566, '2.9', 28350, 6250, 3040, 702, 12, 138, 539, 0, 0),
(162, 3, 72, '70|71|72|73|74', '117.96', 153, 56941, 272527, '3.2', 28350, 6250, 3040, 702, 12, 138, 540, 0, 0),
(163, 3, 73, '71|72|73|74|75', '104.42', 139, 56081, 314004, '2.5', 25628, 5545, 2681, 648, 10, 159, 543, 0, 0),
(164, 3, 74, '72|73|74|75', '80.19', 109, 56637, 345877, '2.6', 24879, 5351, 2582, 633, 9, 165, 544, 0, 0),
(165, 3, 75, '73|74|75', '55.62', 75, 53765, 357628, '2.5', 23305, 4944, 2374, 602, 8, 177, 545, 0, 0),
(166, 4, 21, '21|22|23', '31.78', 11, 59357, 38916, '5.3', 40800, 2581, 8666, 619, 36, 110, 338, 0, 0),
(167, 4, 22, '21|22|23|24', '60.17', 20, 57557, 35231, '5.3', 40800, 2581, 8666, 619, 36, 110, 324, 0, 0),
(168, 4, 23, '21|22|23|24|25', '87.12', 27, 49032, 30962, '5.0', 40800, 2581, 8666, 619, 36, 110, 303, 0, 0),
(169, 4, 24, '22|23|24|25|26', '107.85', 33, 41854, 28748, '4.8', 40800, 2581, 8666, 619, 36, 110, 289, 0, 0),
(170, 4, 25, '23|24|25|26|27', '96.73', 31, 40024, 28457, '4.9', 40800, 2581, 8666, 619, 36, 110, 287, 0, 0),
(171, 4, 26, '24|25|26|27|28', '112.05', 39, 44902, 33053, '5.0', 40800, 2581, 8666, 619, 36, 110, 313, 0, 0),
(172, 4, 27, '25|26|27|28|29', '104.93', 40, 41477, 37571, '4.8', 40800, 2581, 8666, 619, 36, 110, 332, 0, 0),
(173, 4, 28, '26|27|28|29|30', '92.82', 42, 45397, 42099, '4.9', 40800, 2581, 8666, 619, 36, 110, 348, 0, 0),
(174, 4, 29, '27|28|29|30|31', '90.12', 47, 52838, 57039, '5.4', 40800, 2581, 8666, 619, 36, 110, 381, 0, 0),
(175, 4, 30, '28|29|30|31|32', '121.11', 63, 56314, 62675, '5.4', 40800, 2581, 8666, 619, 36, 110, 389, 0, 0),
(176, 4, 31, '29|30|31|32|33', '128.06', 69, 53006, 67119, '5.2', 40800, 2581, 8666, 619, 36, 110, 395, 0, 0),
(177, 4, 32, '30|31|32|33|34', '128.76', 71, 57249, 74286, '5.2', 40800, 2581, 8666, 619, 36, 110, 402, 0, 0),
(178, 4, 33, '31|32|33|34|35', '134.06', 75, 59420, 81714, '4.9', 47696, 2885, 8693, 789, 39, 157, 400, 0, 0),
(179, 4, 34, '32|33|34|35|36', '136.31', 74, 58141, 77215, '4.3', 54778, 3197, 8720, 962, 43, 204, 385, 0, 0),
(180, 4, 35, '33|34|35|36|37', '122.67', 74, 60481, 82784, '3.9', 66067, 3695, 8764, 1240, 48, 280, 377, 0, 0),
(181, 4, 36, '34|35|36|37|38', '130.59', 79, 61234, 82524, '3.5', 74540, 4069, 8797, 1448, 52, 338, 391, 0, 0),
(182, 4, 37, '35|36|37|38|39', '129.52', 85, 61412, 83741, '3.2', 80582, 4335, 8820, 1596, 55, 378, 385, 0, 0),
(183, 4, 38, '36|37|38|39|40', '122.32', 83, 61632, 87964, '3.4', 80582, 4335, 8820, 1596, 55, 378, 390, 0, 0),
(184, 4, 39, '37|38|39|40|41', '112.09', 86, 62280, 109985, '3.3', 80582, 4335, 8820, 1596, 55, 378, 412, 0, 0),
(185, 4, 40, '38|39|40|41|42', '114.91', 89, 60392, 120120, '3.4', 80582, 4335, 8820, 1596, 55, 378, 419, 0, 0),
(186, 4, 41, '39|40|41|42|43', '114.03', 92, 60834, 138245, '3.7', 80582, 4335, 8820, 1596, 55, 378, 430, 0, 0),
(187, 4, 42, '40|41|42|43|44', '105.02', 82, 60071, 142121, '3.9', 80582, 4335, 8820, 1596, 55, 378, 432, 0, 0),
(188, 4, 43, '41|42|43|44|45', '104.49', 82, 59158, 145107, '3.9', 79009, 4926, 8438, 1599, 69, 393, 434, 0, 0),
(189, 4, 44, '42|43|44|45|46', '96.81', 75, 62660, 199566, '4.1', 77455, 5510, 8062, 1602, 84, 407, 453, 0, 0),
(190, 4, 45, '43|44|45|46|47', '85.08', 64, 63924, 197827, '4.3', 74536, 6606, 7354, 1607, 110, 433, 453, 0, 0),
(191, 4, 46, '44|45|46|47|48', '80.53', 67, 69473, 198277, '4.0', 70256, 8214, 6316, 1614, 149, 472, 481, 0, 0),
(192, 4, 47, '45|46|47|48|49', '98.56', 82, 65431, 184289, '4.3', 68856, 8740, 5976, 1617, 162, 485, 479, 0, 0),
(193, 4, 48, '46|47|48|49|50', '112.30', 92, 66065, 170600, '4.2', 68856, 8740, 5976, 1617, 162, 485, 475, 0, 0),
(194, 4, 49, '47|48|49|50|51', '135.91', 110, 60210, 148144, '3.8', 68856, 8740, 5976, 1617, 162, 485, 467, 0, 0),
(195, 4, 50, '48|49|50|51|52', '144.63', 120, 58568, 154161, '3.7', 68856, 8740, 5976, 1617, 162, 485, 469, 0, 0),
(196, 4, 51, '49|50|51|52|53', '128.77', 114, 56364, 179307, '3.8', 68856, 8740, 5976, 1617, 162, 485, 477, 0, 0),
(197, 4, 52, '50|51|52|53|54', '118.17', 117, 56892, 188775, '3.4', 68856, 8740, 5976, 1617, 162, 485, 479, 0, 0),
(198, 4, 53, '51|52|53|54|55', '108.32', 126, 61695, 280668, '3.6', 64841, 8432, 5648, 1507, 202, 465, 496, 0, 0),
(199, 4, 54, '52|53|54|55|56', '97.21', 133, 70510, 276294, '4.1', 60741, 8118, 5313, 1395, 243, 445, 498, 0, 0),
(200, 4, 55, '53|54|55|56|57', '92.65', 145, 73996, 301036, '4.1', 57342, 7858, 5036, 1303, 277, 428, 502, 0, 0),
(201, 4, 56, '54|55|56|57|58', '110.96', 170, 71014, 281711, '3.9', 54571, 7646, 4810, 1227, 304, 415, 528, 0, 0),
(202, 4, 57, '55|56|57|58|59', '123.28', 191, 70666, 286894, '4.1', 51992, 7448, 4599, 1156, 330, 402, 530, 0, 0),
(203, 4, 58, '56|57|58|59|60', '129.23', 196, 64376, 229901, '3.7', 51992, 7448, 4599, 1156, 330, 402, 524, 0, 0),
(204, 4, 59, '57|58|59|60|61', '125.67', 195, 60917, 283934, '3.5', 51992, 7448, 4599, 1156, 330, 402, 529, 0, 0),
(205, 4, 60, '58|59|60|61|62', '122.61', 189, 62388, 307995, '3.4', 51992, 7448, 4599, 1156, 330, 402, 531, 0, 0),
(206, 4, 61, '59|60|61|62|63', '116.69', 182, 63629, 337157, '3.5', 51992, 7448, 4599, 1156, 330, 402, 533, 0, 0),
(207, 4, 62, '60|61|62|63|64', '104.91', 169, 63521, 381950, '3.1', 51992, 7448, 4599, 1156, 330, 402, 535, 0, 0),
(208, 4, 63, '61|62|63|64|65', '112.66', 169, 66359, 369408, '3.3', 47096, 7200, 4276, 1062, 264, 347, 536, 0, 0),
(209, 4, 64, '62|63|64|65|66', '122.51', 173, 63669, 295707, '3.3', 42153, 6949, 3950, 967, 198, 292, 534, 0, 0),
(210, 4, 65, '63|64|65|66|67', '144.81', 194, 62010, 303570, '3.1', 37124, 6695, 3619, 871, 130, 236, 538, 0, 0),
(211, 4, 66, '64|65|66|67|68', '145.27', 189, 61603, 282724, '3.0', 32603, 6466, 3320, 784, 69, 185, 539, 0, 0),
(212, 4, 67, '65|66|67|68|69', '155.97', 190, 59928, 244287, '2.9', 28350, 6250, 3040, 702, 12, 138, 538, 0, 0),
(213, 4, 68, '66|67|68|69|70', '142.96', 179, 60150, 252936, '2.8', 28350, 6250, 3040, 702, 12, 138, 539, 0, 0),
(214, 4, 69, '67|68|69|70|71', '130.53', 163, 61476, 271352, '2.8', 28350, 6250, 3040, 702, 12, 138, 540, 0, 0),
(215, 4, 70, '68|69|70|71|72', '109.79', 138, 59634, 221964, '2.7', 28350, 6250, 3040, 702, 12, 138, 537, 0, 0),
(216, 4, 71, '69|70|71|72|73', '102.51', 123, 56037, 187042, '2.5', 28350, 6250, 3040, 702, 12, 138, 533, 0, 0),
(217, 4, 72, '70|71|72|73|74', '84.53', 105, 56202, 207055, '2.7', 28350, 6250, 3040, 702, 12, 138, 535, 0, 0),
(218, 4, 73, '71|72|73|74|75', '82.95', 98, 52592, 207839, '2.6', 25615, 5542, 2679, 648, 10, 159, 537, 0, 0),
(219, 4, 74, '72|73|74|75', '66.05', 77, 50706, 192907, '2.3', 24869, 5349, 2581, 633, 9, 165, 537, 0, 0),
(220, 4, 75, '73|74|75', '46.31', 52, 48463, 201101, '2.5', 23196, 4916, 2360, 600, 8, 178, 539, 0, 0);


/* 08/01/2013 */

ALTER TABLE `sustainablerates` ADD INDEX ( `age` ); -- SQL optimzation
ALTER TABLE `lifeexpectancy` ADD INDEX ( `2007age` );

/*** Till this Pushed to Producton on 08/06/2013 **/

/* 08/16/2013  */
ALTER TABLE `cashedgeitem` ADD `check` TINYINT NOT NULL COMMENT 'This is for answering the security question wrongly' AFTER `lsupdate`;

/* 08/19/2013  */
UPDATE `actionstepmeta` SET `description` = 'We know your insurance situation isn''t exactly something you talk about much (let alone think about!) unless you have a pending issue. <br>{{title}}<br>Nonetheless, you should tell us as much information about it as possible.' WHERE `actionid` = 29;

CREATE TABLE IF NOT EXISTS `roleactivities` (
  `usermetaid` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` varchar(45) DEFAULT NULL,
  `activitykey` varchar(45) DEFAULT NULL,
  `activityvalue` varchar(45) DEFAULT NULL,
  `subactivitykey` varchar(45) DEFAULT NULL,
  `subactivityvalue` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`usermetaid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `roleactivities` (`usermetaid`, `roleid`, `activitykey`, `activityvalue`, `subactivitykey`, `subactivityvalue`) VALUES
(1, '777', 'reports', 'yes', NULL, NULL),
(2, '888', 'reports', 'no', NULL, NULL);


/*THAYUB*/

ALTER TABLE  `cashedgeitem` ADD  `deleted` TINYINT NOT NULL DEFAULT  '0' COMMENT  'This is to check if the account is deleted or not.' AFTER  `check`;

/*  08/27/2013 */
UPDATE `actionstepmeta` SET `actionname` =  'Open an IRA for ${{amt}}', `description` = 'Looks like you don''t contribute to a company retirement plan account. That might not be available where you work. But check with Human Resources just in case you might be missing out.  Otherwise, you should open an Individual Retirement Account (IRA) for ${{amt}}.' WHERE `actionid` =12;
/* Till This we pused to Staging  - 8/27/2013 */



/*THAYUB*/
/*ADDED ON AUG 29 IST*/

ALTER TABLE  `otlt` CHANGE  `id`  `id` INT( 10 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE  `otlt` CHANGE  `startdate`  `startdate` DATE NULL;
ALTER TABLE  `otlt` CHANGE  `enddate`  `enddate` DATE NULL;

/* Till This we pused to Staging  - 8/31/2013 */

/* 09/02/2013 */

DELETE FROM `otlt` WHERE `description` = 'INV';
ALTER TABLE  `otlt` CHANGE  `startdate`  `startdate` DATE NULL;
ALTER TABLE  `otlt` CHANGE  `enddate`  `enddate` DATE NULL;

INSERT INTO `leapscoremeta`.`otlt` VALUES (3001, '529 / Education Savings', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3002, 'Brokerage', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3003, '401 (k)', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3004, '403 (b)', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3005, '457', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3006, 'Deferred Comp', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3007, 'KEOGH', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3008, 'Pension', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3009, 'Profit Sharing Plan', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3010, 'IRA', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3011, 'Rollover IRA', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3012, 'Roth IRA', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3013, 'SEP IRA', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3014, 'Simple IRA', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3015, 'Employer Stock Account', 'INV', NULL, NULL);

DELETE FROM `otlt` WHERE `description` = 'INV';
INSERT INTO `leapscoremeta`.`otlt` VALUES (3001, 'Non Retirement&529 / Education Savings&Custodial', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3002, 'Non Retirement&Brokerage&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3003, 'Retirement&401 (k)&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3004, 'Retirement&403 (b)&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3005, 'Retirement&457&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3006, 'Retirement&Deferred Comp&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3007, 'Retirement&KEOGH&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3008, 'Retirement&Pension&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3009, 'Retirement&Profit Sharing Plan&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3010, 'Retirement&IRA&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3011, 'Retirement&Rollover IRA&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3012, 'Retirement&Roth IRA&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3013, 'Retirement&SEP IRA&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3014, 'Retirement&Simple IRA&Individual', 'INV', NULL, NULL);
INSERT INTO `leapscoremeta`.`otlt` VALUES (3015, 'Non Retirement&Employer Stock Account&Individual', 'INV', NULL, NULL);

ALTER TABLE  `cashedgeaccount` ADD `timemodified` TIMESTAMP NOT NULL COMMENT 'The time in which this table was last updated.';
/*Till this we pushed to production  9.10.2013 */

/* For the newly added video */
UPDATE  `otlt` SET  `description` = 'qDCC-9z4cl8' WHERE `otlt`.`id` =2001;

/* 09/27/2013  */
UPDATE `actionstepmeta` SET `articles` = 'How Much Can You Afford?#https://www.flexscore.com/learningcenter/how-much-can-you-afford|Homeownership#https://www.flexscore.com/learningcenter/homeownership' WHERE `actionid` =55;
UPDATE `actionstepmeta` SET `articles` = 'Repairing Poor Credit#https://www.flexscore.com/learningcenter/repairing-poor-credit|How Can I Repair My Poor Credit?#https://www.flexscore.com/learningcenter/how-can-i-repair-my-poor-credit|Establishing a Credit History#https://www.flexscore.com/learningcenter/establishing-a-credit-history' WHERE `actionid` =57;
UPDATE `actionstepmeta` SET `articles` = 'Group Health Insurance#https://www.flexscore.com/learningcenter/group-health-insurance|Individual Health Insurance#https://www.flexscore.com/learningcenter/individual-health-insurance' WHERE `actionid` =64;

INSERT INTO `actionstepmeta` VALUES (86, 'Health Insurance - Get Coverage', 'Protection Planning', 15, 'addinsurance', 'http://track.flexlinks.com/a.aspx?foid=26122159&fot=9999&foc=1', '', '', 'link', 'short', 'It''s hard to know when you''re going to need medical care. That''s why not having health insurance is an enormous risk. If you think it''s expensive to have health insurance, you may not have considered how costly having no insurance can be. Don''t chance it...do the right thing. Inquire with your employer or we''ll help you get coverage.<br>{{lnk}} to begin.', '', '', 'Get Started', '', 'Update Insurance', '', '', 1, '0');

UPDATE actionstepmeta set actionname ='Start Funding an IRA', description = "Looks like you don't contribute to a company retirement plan account. That might not be available where you work. But check with Human Resources just in case you're missing out. <br><br>Otherwise, you should open an Individual Retirement Account (IRA) and start contributing ${{amt}} each month.  If you need to, start with $100 each month and every six months increase the amount by another $100." where actionid=12;

/* 09/30/2013  */
UPDATE actionstepmeta SET  `linkstep1` =  'Add Insurance' WHERE `actionid` =86;

    INSERT INTO `actionstepmeta` VALUES(87, 'Health Insurance - Review', 'Protection Planning', 15, 'addinsurance', 'http://track.flexlinks.com/a.aspx?foid=26122159&fot=9999&foc=1', '', '', 'link', 'short', 'It is a good idea to periodically review your health insurance coverage, especially if you’ve experienced a major life event or change in your health. Contact your current provider as a first step.  If you would like for us to help {{lnk}}.', '', '', 'Get Started', '', 'Update Insurance', '', '', 1, '0');
    INSERT INTO `actionstepmeta` VALUES(88, 'Life Insurance - Review', 'Protection Planning', 15, 'addinsurance', 'http://track.flexlinks.com/a.aspx?foid=26122159&fot=9999&foc=1', '', '', 'link', 'short', 'It is a good idea to periodically review your life insurance coverage, especially if you’ve experienced a major life event or change in your lifestyle costs. Contact your current provider as a first step.  If you would like for us to help {{lnk}}.', '', '', 'Get Started', '', 'Update Insurance', '', '', 1, '0');
    INSERT INTO `actionstepmeta` VALUES(89, 'Disability Insurance - Review', 'Protection Planning', 15, 'addinsurance', 'http://track.flexlinks.com/a.aspx?foid=26122159&fot=9999&foc=1', '', '', 'link', 'short', 'It is a good idea to periodically review your disability insurance coverage, especially if you’ve experienced a major life event or change in your income. Contact your current provider as a first step.  If you would like for us to help {{lnk}}', '', '', 'Get Started', '', 'Update Insurance', '', '', 1, '0');

/* 09/30/2013 table altered for reviewstatus */
/* ALTER TABLE `actionstepmeta` ADD `reviewstatus` ENUM( '0', '1' ) NOT NULL DEFAULT '0' COMMENT '0-No review 1-Review. Once in 6 monts this action step will be reviewed.' AFTER `status` */
/* 10/01/2013 */
UPDATE `actionstepmeta` SET `description` = 'Please give us more details on this asset.<br>{{title}}<br>The more details you give us, the better we can help you.' WHERE `actionid` =25;
UPDATE `actionstepmeta` SET `description` = 'Please give us more details on this debt.<br>{{title}}<br>The more details you give us, the better we can help you.' WHERE `actionid` =26;


/*10/1/2013 reciewsatus setted to 1 for actionid */

update actionstepmeta set reviewstatus = '1' where actionid in (10,16,17,21,23,38,39,40,41,42,46,47,49,52,53,57,62,70,71,72,74,78,79,80,87,88,89);


/*10/1/2013 Adding new action step for reading Learning Center articles. */
INSERT INTO `actionstepmeta` VALUES(90, 'Recommended Articles', 'Learning Center', 5, 'learnmore', '', '', '', 'action', 'instant', 'Read these articles to learn more about financial planning. Each article is worth 5 points. <br/>{{title}}', '', '', 'Get Started', 'Mark as Done', 'Learn more', 'Learn more', '', 0, '0', 0);


/*10/1/2013 Update articles in actionstepmeta per mappings from Jeff */
UPDATE actionstepmeta SET articles = "Protecting Your Loved Ones with Life Insurance#https://www.flexscore.com/learningcenter/protecting-your-loved-ones-with-life-insurance#92|Why I Don't Want to Buy Life Insurance#https://www.flexscore.com/learningcenter/why-i-dont-want-to-buy-life-insurance#193|Why Women Need Life Insurance#https://www.flexscore.com/learningcenter/why-women-need-life-insurance#613|Fundamental Needs for Life Insurance#https://www.flexscore.com/learningcenter/fundamental-needs-for-life-insurance#616" WHERE actionid = 2;
UPDATE actionstepmeta SET articles = "Protecting Your Loved Ones with Life Insurance#https://www.flexscore.com/learningcenter/protecting-your-loved-ones-with-life-insurance#92|Why I Don't Want to Buy Life Insurance#https://www.flexscore.com/learningcenter/why-i-dont-want-to-buy-life-insurance#193|Why Women Need Life Insurance#https://www.flexscore.com/learningcenter/why-women-need-life-insurance#613|Fundamental Needs for Life Insurance#https://www.flexscore.com/learningcenter/fundamental-needs-for-life-insurance#616" WHERE actionid = 3;
UPDATE actionstepmeta SET articles = "Life Insurance: What you need to know now!#https://www.flexscore.com/learningcenter/diversity-your-investments#1687" WHERE actionid = 4;
UPDATE actionstepmeta SET articles = "Get the Facts About Disability Insurance#https://www.flexscore.com/learningcenter/get-the-facts-about-disability-insurance#1647" WHERE actionid = 5;
UPDATE actionstepmeta SET articles = "Asset Allocation#https://www.flexscore.com/learningcenter/asset-allocation#202|Understanding Risk#https://www.flexscore.com/learningcenter/understanding-risk-topic-discussion#165|How to Measure Your Risk Tolerance#https://www.flexscore.com/learningcenter/how-to-measure-your-risk-tolerance#111" WHERE actionid = 6;
UPDATE actionstepmeta SET articles = "How to Measure Your Risk Tolerance#https://www.flexscore.com/learningcenter/how-to-measure-your-risk-tolerance#111|Measuring Risk#https://www.flexscore.com/learningcenter/measuring-risk#717|How can I gauge my risk tolerance?#https://www.flexscore.com/learningcenter/how-can-i-gauge-my-risk-tolerance#708" WHERE actionid = 7;
UPDATE actionstepmeta SET articles = "Diversify Your Investments#https://www.flexscore.com/learningcenter/diversity-your-investments#1641" WHERE actionid = 8;
UPDATE actionstepmeta SET articles = "Retirement Planning: The Basics#https://www.flexscore.com/learningcenter/retirement-planning-the-basics#28|What is a 401(k) plan?#https://www.flexscore.com/learningcenter/what-is-a-401k-plan#73|Lump Sum vs. Dollar Cost Averaging: Which Is Better#https://www.flexscore.com/learningcenter/lump-sum-vs-dollar-cost-averaging-which-is-better#646" WHERE actionid = 9;
UPDATE actionstepmeta SET articles = "Beneficiary Designations#https://www.flexscore.com/learningcenter/beneficiary-designations#204" WHERE actionid = 10;
UPDATE actionstepmeta SET articles = "Taking Advantage of Employer-Sponsored Retirement Plans#https://www.flexscore.com/learningcenter/taking-advantage-of-employer-sponsored-retirement-plans#71" WHERE actionid = 11;
UPDATE actionstepmeta SET articles = "Reducing the Cost of Debt#https://www.flexscore.com/learningcenter/reducing-the-cost-of-debt#258|Other Options When You Can't Meet Your Financial Obligations#https://www.flexscore.com/learningcenter/other-options-when-you-cant-meet-your-financial-obligations#809" WHERE actionid = 16;
UPDATE actionstepmeta SET articles = "How to Reduce Debts and Pay Off Credit Cards Super Fast!#https://www.flexscore.com/learningcenter/how-to-reduce-debts-and-pay-off-credit-cards-super-fast#1651#1651" WHERE actionid = 18;
UPDATE actionstepmeta SET articles = "Knowledge of Debt and Liabilities#https://www.flexscore.com/learningcenter/knowledge-of-debt-and-liabilities#1358" WHERE actionid = 19;
UPDATE actionstepmeta SET articles = "Getting Started: Establishing a Financial Safety Net#https://www.flexscore.com/learningcenter/getting-started-establishing-a-financial-safety-net#664" WHERE actionid = 20;
UPDATE actionstepmeta SET articles = "Understanding Your Credit Report#https://www.flexscore.com/learningcenter/understanding-your-credit-report#838|Credit Reports#https://www.flexscore.com/learningcenter/credit-reports#844|The Effects of Credit Cards on Your Credit Report#https://www.flexscore.com/learningcenter/the-effects-of-credit-cards-on-your-credit-report#849|Interpreting the Information on Your Credit Report#https://www.flexscore.com/learningcenter/interpreting-the-information-on-your-credit-report#854|Requesting a Copy of Your Credit Report#https://www.flexscore.com/learningcenter/requesting-a-copy-of-your-credit-report#858" WHERE actionid = 23;
UPDATE actionstepmeta SET articles = "How Inflation Works#https://www.flexscore.com/learningcenter/inflation-considerations#1355" WHERE actionid = 24;
UPDATE actionstepmeta SET articles = "Disability Income Insurance#https://www.flexscore.com/learningcenter/disability-income-insurance#184|Determining the Need for Disability Income Insurance: How Much Is Enough?#https://www.flexscore.com/learningcenter/determining-the-need-for-disability-income-insurance-how-much-is-enough#186|Evaluating Disability Income Insurance Policies#https://www.flexscore.com/learningcenter/evaluating-disability-income-insurance-policies#622|Ten Ways to Lower the Cost of Disability Income Insurance#https://www.flexscore.com/learningcenter/ten-ways-to-lower-the-cost-of-disability-income-insurance-2#636" WHERE actionid = 35;
UPDATE actionstepmeta SET articles = "Disability Income Insurance#https://www.flexscore.com/learningcenter/disability-income-insurance#184|Determining the Need for Disability Income Insurance: How Much Is Enough?#https://www.flexscore.com/learningcenter/determining-the-need-for-disability-income-insurance-how-much-is-enough#186|Evaluating Disability Income Insurance Policies#https://www.flexscore.com/learningcenter/evaluating-disability-income-insurance-policies#622|Ten Ways to Lower the Cost of Disability Income Insurance#https://www.flexscore.com/learningcenter/ten-ways-to-lower-the-cost-of-disability-income-insurance-2#636" WHERE actionid = 36;
UPDATE actionstepmeta SET articles = "Six Keys to More Successful Investing#https://www.flexscore.com/learningcenter/six-keys-to-more-successful-investing#64|Improving Portfolio Performance with Asset Allocation#https://www.flexscore.com/learningcenter/improving-portfolio-performance-with-asset-allocation#1142|Alternative Asset Classes: An Introduction#https://www.flexscore.com/learningcenter/alternative-asset-classes-an-introduction#690" WHERE actionid = 37;
UPDATE actionstepmeta SET articles = "Understanding Risk#https://www.flexscore.com/learningcenter/understanding-risk-topic-discussion#165" WHERE actionid = 38;
UPDATE actionstepmeta SET articles = "Dollar Cost Averaging#https://www.flexscore.com/learningcenter/dollar-cost-averaging-2#175|Lump Sum vs. Dollar Cost Averaging: Which Is Better#https://www.flexscore.com/learningcenter/lump-sum-vs-dollar-cost-averaging-which-is-better#646" WHERE actionid = 39;
UPDATE actionstepmeta SET articles = "Types of Stock Mutual Funds#https://www.flexscore.com/learningcenter/types-of-stock-mutual-funds#729|Types of Bond Mutual Funds#https://www.flexscore.com/learningcenter/types-of-bond-mutual-funds#722|Combination Funds#https://www.flexscore.com/learningcenter/combination-funds#697|Asset Allocation Funds: Lifestyle, Lifecycle, and Distribution#https://www.flexscore.com/learningcenter/asset-allocation-funds-lifestyle-lifecycle-and-distribution#694" WHERE actionid = 40;
UPDATE actionstepmeta SET articles = "Beyond Traditional Asset Classes: Exploring Alternatives#https://www.flexscore.com/learningcenter/beyond-traditional-asset-classes-exploring-alternatives#656|Alternative Asset Classes: An Introduction#https://www.flexscore.com/learningcenter/alternative-asset-classes-an-introduction#690" WHERE actionid = 41;
UPDATE actionstepmeta SET articles = "Rebalancing a Portfolio vs. Redesigning#https://www.flexscore.com/learningcenter/rebalancing-a-portfolio-vs-redesigning#181" WHERE actionid = 42;
UPDATE actionstepmeta SET articles = "How Much Can You Afford?#https://www.flexscore.com/learningcenter/how-much-can-you-afford#1677|Homeownership#https://www.flexscore.com/learningcenter/homeownership#1675" WHERE actionid = 55;
UPDATE actionstepmeta SET articles = "Repairing Poor Credit#https://www.flexscore.com/learningcenter/repairing-poor-credit#255|Correcting Errors on Your Credit Report#https://www.flexscore.com/learningcenter/correcting-errors-on-your-credit-report#874|How Can I Repair My Poor Credit?#https://www.flexscore.com/learningcenter/how-can-i-repair-my-poor-credit#874" WHERE actionid = 57;
UPDATE actionstepmeta SET articles = "Five Questions about Long-Term Care#https://www.flexscore.com/learningcenter/five-questions-about-long-term-care#641|Long-Term Care Insurance (LTCI)#https://www.flexscore.com/learningcenter/long-term-care-insurance-ltci#738|Determining the Need for Disability Income Insurance: How Much Is Enough?#https://www.flexscore.com/learningcenter/determining-the-need-for-disability-income-insurance-how-much-is-enough#186" WHERE actionid = 60;
UPDATE actionstepmeta SET articles = "Five Questions about Long-Term Care#https://www.flexscore.com/learningcenter/five-questions-about-long-term-care#641|Long-Term Care Insurance (LTCI)#https://www.flexscore.com/learningcenter/long-term-care-insurance-ltci#738|Determining the Need for Disability Income Insurance: How Much Is Enough?#https://www.flexscore.com/learningcenter/determining-the-need-for-disability-income-insurance-how-much-is-enough#186" WHERE actionid = 61;
UPDATE actionstepmeta SET articles = "How Inflation Works#https://www.flexscore.com/learningcenter/inflation-considerations#1355" WHERE actionid = 62;
UPDATE actionstepmeta SET articles = "Group Health Insurance#https://www.flexscore.com/learningcenter/group-health-insurance#903|Individual Health Insurance#https://www.flexscore.com/learningcenter/individual-health-insurance#914|Making the Most of Your Group Health Benefits#https://www.flexscore.com/learningcenter/making-the-most-of-your-group-health-benefits#908" WHERE actionid = 64;
UPDATE actionstepmeta SET articles = "Concentrated Stock Positions: Considerations and Strategies#https://www.flexscore.com/learningcenter/concentrated-stock-positions-considerations-and-strategies#650|Designing an Investment Portfolio#https://www.flexscore.com/learningcenter/designing-an-investment-portfolio#1140" WHERE actionid = 70;
UPDATE actionstepmeta SET articles = "Managing a Concentrated Stock Position#https://www.flexscore.com/learningcenter/managing-a-concentrated-stock-position#712|Concentrated Stock Positions: Considerations and Strategies#https://www.flexscore.com/learningcenter/concentrated-stock-positions-considerations-and-strategies#650" WHERE actionid = 71;
UPDATE actionstepmeta SET articles = "Mutual Funds#https://www.flexscore.com/learningcenter/mutual-funds#178|Exchange Traded Funds#https://www.flexscore.com/learningcenter/exchange-traded-funds#699" WHERE actionid = 72;
UPDATE actionstepmeta SET articles = "Traditional Pension Plans#https://www.flexscore.com/learningcenter/traditional-pension-plans#755" WHERE actionid = 73;
UPDATE actionstepmeta SET articles = "Sustainable Withdrawal Rates#https://www.flexscore.com/learningcenter/sustainable-withdrawal-rates#196|Investment Planning throughout Retirement#https://www.flexscore.com/learningcenter/investment-planning-throughout-retirement#199|Asset Allocation: Projecting a Glide Path#https://www.flexscore.com/learningcenter/asset-allocation-projecting-a-glide-path#202" WHERE actionid = 74;
UPDATE actionstepmeta SET articles = "Budgeting#https://www.flexscore.com/learningcenter/budgeting#674|Use of Cash Flow Analysis in Creating Your Budget#https://www.flexscore.com/learningcenter/use-of-cash-flow-analysis-in-creating-your-budget#668|The Spending Plan: Setting and Prioritizing Your Budget Goals#https://www.flexscore.com/learningcenter/the-spending-plan-setting-and-prioritizing-your-budget-goals#239" WHERE actionid = 75;
UPDATE actionstepmeta SET articles = "Budgeting#https://www.flexscore.com/learningcenter/budgeting#674|Use of Cash Flow Analysis in Creating Your Budget#https://www.flexscore.com/learningcenter/use-of-cash-flow-analysis-in-creating-your-budget#668|Establishing a Budget#https://www.flexscore.com/learningcenter/establishing-a-budget#671" WHERE actionid = 82;

/*10/4/2013 Update articles in actionstepmeta per mappings from Joel and Jeff */
UPDATE actionstepmeta SET articles = "Mortgage Refinancing#https://www.flexscore.com/learningcenter/refinancing#265" WHERE actionid = 17;
UPDATE actionstepmeta SET articles = "Debt Consolidation#https://www.flexscore.com/learningcenter/debt-consolidation#763|Will debt consolidation hurt or help my credit rating?#https://www.flexscore.com/learningcenter/will-debt-consolidation-hurt-or-help-my-credit-rating#767|How can I lower the interest rate on my credit card?#https://www.flexscore.com/learningcenter/how-can-i-lower-the-interest-rate-on-my-credit-card#776" WHERE actionid = 52;
UPDATE actionstepmeta SET articles = "Credit Card Balance Transfers#https://www.flexscore.com/learningcenter/credit-card-balance-transfers#796" WHERE actionid = 53;
UPDATE actionstepmeta SET articles = "Debt Service Ratio versus Debt Safety Ratio#https://www.flexscore.com/learningcenter/debt-service-ratio-versus-debt-safety-ratio#760|Borrowing Options#https://www.flexscore.com/learningcenter/borrowing-options#787|Borrowing Options: Credit Cards#https://www.flexscore.com/learningcenter/borrowing-options-credit-cards#792" WHERE actionid = 54;

/*10/4/2013 Update Learning Center actionstep description per text from Jeff */
UPDATE actionstepmeta SET description = "The following articles form our Learning Center are recommended based on areas of your profile where you might need some help.  The more financially literate you are, the more likely you'll meet your goals (a bazillion studies prove it)!  Every article you read is worth up to 5 points. <br/>{{title}}" WHERE actionid = 90;

/*10/4/2013 Fix constants found in otlt related the videos */
UPDATE otlt SET description = '' WHERE NAME LIKE 'vid%';
UPDATE otlt SET description = 'qDCC-9z4cl8' WHERE NAME = 'vid1';
UPDATE otlt SET description = 'tpbmtPDVPQs' WHERE NAME = 'vid2';
UPDATE otlt SET description = 'xCjZ1V4rhlw' WHERE NAME = 'vid6';
UPDATE otlt SET description = 'zlbYlgfAoB0' WHERE NAME = 'vid7';
UPDATE otlt SET description = 'ctIeZ8s6RQI' WHERE NAME = 'vid9';
UPDATE otlt SET description = 'rWn_cLvaO5c' WHERE NAME = 'vid10';

/*10/7/2013 Update Learning Center actionstep buttons to "Learn More" */
UPDATE actionstepmeta SET buttonstep1 = "Learn More", buttonstep2 = "Learn More" WHERE actionid = 90;

/* Fix for health insurance / life insurance / disability insurance review steps. 10/08/13 */
update actionstepmeta set actionname = 'Review Health Insurance', externallink = 'https://www.healthcare.gov/' where actionid=87;
update actionstepmeta set actionname = 'Review Life Insurance', externallink = 'https://www.healthcare.gov/' where actionid=88;
update actionstepmeta set actionname = 'Review Disability Insurance', externallink = 'https://www.healthcare.gov/' where actionid=89;

/* Fix for default points for  insurance. 10/08/13 */
update actionstepmeta set points = 5 where actionid = 8;
update actionstepmeta set points = 5 where actionid = 44;
update actionstepmeta set points = 5 where actionid = 45;
update actionstepmeta set points = 5 where actionid = 59;
update actionstepmeta set points = 5 where actionid = 62;
update actionstepmeta set points = 15 where actionid = 64;
update actionstepmeta set points = 50 where actionid = 90;

/* 10/9/2013 */
update actionstepmeta set externallink = 'https://www.healthcare.gov/' where actionid=86;



/* 10/9/2013 Disable Health and Medical actionstep*/
UPDATE actionstepmeta SET status = '1' WHERE actionid = 64;

/* 10/11/2013 */
update actionstepmeta set actionname = 'Review Life Insurance', externallink = 'http://track.flexlinks.com/a.aspx?foid=26122159&fot=9999&foc=1' where actionid=88;
update actionstepmeta set actionname = 'Review Disability Insurance', externallink = 'http://track.flexlinks.com/a.aspx?foid=26122159&fot=9999&foc=1' where actionid=89;

/* 10/11/2013 above one is out of date*/
update actionstepmeta set actionname = 'Review Life Insurance', externallink = 'http://www.insure.com/' where actionid=88;
update actionstepmeta set actionname = 'Review Disability Insurance', externallink = 'http://www.insure.com/' where actionid=89;


/* 11/9/2013 */
UPDATE actionstepmeta SET status = '1' WHERE actionid = 90;

/* 10/11/2013 */
UPDATE `leapscoremeta`.`actionstepmeta` SET `status` = '1' WHERE `actionstepmeta`.`actionid` =1;

/* 10/14/2013 table altered for reviewstatus Disable - 1 and Active - 0*/
ALTER TABLE `actionstepmeta` CHANGE `reviewstatus` `reviewstatus` SMALLINT( 1 ) NOT NULL DEFAULT '1';

UPDATE actionstepmeta SET status = '0' WHERE actionid = 90;

/* 10/14/2013 */
ALTER TABLE `actionstepmeta` CHANGE `reviewstatus` `reviewstatus` ENUM( '0', '1' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '0-Review 1- No Review. Once in 6 monts this action step will be reviewed.';

/* Till this we pushed to Production 10/14/2013 */
/* 10/17/2013 */
UPDATE `leapscoremeta`.`actionstepmeta` SET `status` = '0' WHERE `actionstepmeta`.`actionid` =1;

/*10/17/2013 Edit text for more information on asset or debt action steps */
UPDATE actionstepmeta SET actionname = "Please Provide More Asset Details", description = "We need Ticker Symbols, Values, and Contribution Amounts for the following accounts:<br>{{title}}<br>" WHERE actionid = 25;
UPDATE actionstepmeta SET actionname = "Please Provide More Debt Details", description = "We need APR %, Monthly Payment, and Balance Owed for the following accounts:<br>{{title}}<br>" WHERE actionid = 26;

/* 10/23/2013 Disable actionstep Consider Strategies to Improve Credit Score*/
UPDATE actionstepmeta SET status = '1' WHERE actionid = 57;

/* 10/30/2013 */
ALTER TABLE  `cashedgeitem` CHANGE  `check`  `cecheck` INT( 4 ) NOT NULL COMMENT  'This is for answering the security question wrongly';

/*10/30/2013 Fix typo in Learning Center actionstep description */
UPDATE actionstepmeta SET description = "The following articles from our Learning Center are recommended based on areas of your profile where you might need some help.  The more financially literate you are, the more likely you'll meet your goals (a bazillion studies prove it)!  Every article you read is worth up to 5 points. <br/>{{title}}" WHERE actionid = 90;


/*10/31/2013 Change text in Emergency Fund actionstep description, Emergency fund is no longer a review step. */
UPDATE actionstepmeta SET description = "Putting an emergency fund in place is key to personal financial success. We recommend you have at least 3 months of your normal monthly expenses saved up. These funds should be liquid and available when needed (i.e. not invested in stocks or other volatile investments).<br><br>We suggest you add ${{amt}} to your emergency fund." WHERE actionid = 20;
UPDATE `actionstepmeta` SET `link` = 'addasset' WHERE `actionid` =20;
UPDATE `actionstepmeta` SET `status` = '1' WHERE `actionstepmeta`.`actionid` =1;

/* Till this we pushed to Production 10/31/2013 */
/*11/7/2013*/
ALTER TABLE  `cashedgeitem` CHANGE  `lsupdate`  `lsupdate` INT NOT NULL DEFAULT  '0' COMMENT  'update account status 0 - updated, 1 - tobe updated'

# ** History created on 1/8/2014 *****
# 11/11/13 Table to store user searches for cashedge. If a search is repeated and has
# less than ten results, cashedge search will not be done. Dropped and revised 11/11/13.
CREATE TABLE IF NOT EXISTS `cashedgesearchterm` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `searchterm` VARCHAR(256) DEFAULT NULL,
  `firesults` TEXT COMMENT 'Stored ids from past searches',
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;



# 11/12/13 Update the regiondetails table with zip codes based on new research by Fong
# Fixed some state spellings.
UPDATE regiondetails SET state = "Alaska", zipcoderangeprefix = "995-999" WHERE id = "47";
UPDATE regiondetails SET state = "Alabama", zipcoderangeprefix = "350-352|354-368" WHERE id = "34";
UPDATE regiondetails SET state = "Arkansas", zipcoderangeprefix = "716-717|719-722|725-729" WHERE id = "37";
UPDATE regiondetails SET state = "Arizona", zipcoderangeprefix = "850-853|855-857|859-860|863" WHERE id = "45";
UPDATE regiondetails SET state = "California", zipcoderangeprefix = "900-908|910-928|930-960" WHERE id = "50";
UPDATE regiondetails SET state = "Colorado", zipcoderangeprefix = "800-812|814-816" WHERE id = "44";
UPDATE regiondetails SET state = "Connecticut", zipcoderangeprefix = "010-012|060-065|067" WHERE id = "6";
UPDATE regiondetails SET state = "Washington, D.C.", zipcoderangeprefix = "200|202-205" WHERE id = "24";
UPDATE regiondetails SET state = "Delaware", zipcoderangeprefix = "197-199" WHERE id = "22";
UPDATE regiondetails SET state = "Florida", zipcoderangeprefix = "313-317|320-339|341-342|344-347|349|398" WHERE id = "30";
UPDATE regiondetails SET state = "Georgia", zipcoderangeprefix = "298|300-306|308-312|318-319|399" WHERE id = "29";
UPDATE regiondetails SET state = "Hawaii", zipcoderangeprefix = "967-969" WHERE id = "51";
UPDATE regiondetails SET state = "Iowa", zipcoderangeprefix = "500-509|514|520-525|528" WHERE id = "21";
UPDATE regiondetails SET state = "Idaho", zipcoderangeprefix = "832-834|836-837|979" WHERE id = "39";
UPDATE regiondetails SET state = "Illinois", zipcoderangeprefix = "526-527|600-619|623-627" WHERE id = "12";
UPDATE regiondetails SET state = "Indiana", zipcoderangeprefix = "460-469|472-479" WHERE id = "13";
UPDATE regiondetails SET state = "Kansas", zipcoderangeprefix = "662|669-676|678" WHERE id = "19";
UPDATE regiondetails SET state = "Kentucky", zipcoderangeprefix = "400-406|413-414|420|427|471" WHERE id = "31";
UPDATE regiondetails SET state = "Louisiana", zipcoderangeprefix = "700-701|703-708|710-714|718|755-756" WHERE id = "38";
UPDATE regiondetails SET state = "Massachusetts", zipcoderangeprefix = "013-024|055" WHERE id = "4";
UPDATE regiondetails SET state = "Maryland", zipcoderangeprefix = "206-212|214-219|254|267" WHERE id = "23";
UPDATE regiondetails SET state = "Maine", zipcoderangeprefix = "039-049" WHERE id = "1";
UPDATE regiondetails SET state = "Michigan", zipcoderangeprefix = "480-499" WHERE id = "11";
UPDATE regiondetails SET state = "Minnesota", zipcoderangeprefix = "540|550-551|553-564|566" WHERE id = "20";
UPDATE regiondetails SET state = "Missouri", zipcoderangeprefix = "620|622|628-631|633-641|644-658|660-661|664-668" WHERE id = "15";
UPDATE regiondetails SET state = "Mississippi", zipcoderangeprefix = "369|387|389-397" WHERE id = "33";
UPDATE regiondetails SET state = "Montana", zipcoderangeprefix = "590-599" WHERE id = "40";
UPDATE regiondetails SET state = "North Carolina", zipcoderangeprefix = "270-289|297" WHERE id = "27";
UPDATE regiondetails SET state = "North Dakota", zipcoderangeprefix = "565|567|576|580-588" WHERE id = "16";
UPDATE regiondetails SET state = "Nebraska", zipcoderangeprefix = "515-516|677|680-681|683-693" WHERE id = "18";
UPDATE regiondetails SET state = "New Hampshire", zipcoderangeprefix = "030-034|038" WHERE id = "2";
UPDATE regiondetails SET state = "New Jersey", zipcoderangeprefix = "070-089" WHERE id = "9";
UPDATE regiondetails SET state = "New Mexico", zipcoderangeprefix = "813|865|870-871|873-879|884" WHERE id = "46";
UPDATE regiondetails SET state = "Nevada", zipcoderangeprefix = "864|889-891|893-895|897-898|961" WHERE id = "42";
UPDATE regiondetails SET state = "New York", zipcoderangeprefix = "005|066|068-069|100-149" WHERE id = "7";
UPDATE regiondetails SET state = "Ohio", zipcoderangeprefix = "410|430-438|440-459|470" WHERE id = "14";
UPDATE regiondetails SET state = "Oklahoma", zipcoderangeprefix = "730-731|734-738|740-741|743-749" WHERE id = "35";
UPDATE regiondetails SET state = "Oregon", zipcoderangeprefix = "970-978|986" WHERE id = "49";
UPDATE regiondetails SET state = "Pennsylvania", zipcoderangeprefix = "150-196|260|265|439" WHERE id = "8";
UPDATE regiondetails SET state = "Rhode Island", zipcoderangeprefix = "025-029" WHERE id = "5";
UPDATE regiondetails SET state = "South Carolina", zipcoderangeprefix = "290-296|299" WHERE id = "28";
UPDATE regiondetails SET state = "South Dakota", zipcoderangeprefix = "510-513|570-575|577" WHERE id = "17";
UPDATE regiondetails SET state = "Tennessee", zipcoderangeprefix = "242|307|370-386|388|407-409|417-418|421-426|723-724" WHERE id = "32";
UPDATE regiondetails SET state = "Texas", zipcoderangeprefix = "679|733|739|750-754|757-770|772-799|880-883|885" WHERE id = "36";
UPDATE regiondetails SET state = "Utah", zipcoderangeprefix = "840-847" WHERE id = "43";
UPDATE regiondetails SET state = "Virginia", zipcoderangeprefix = "201|220-241|243-245|268" WHERE id = "25";
UPDATE regiondetails SET state = "Vermont", zipcoderangeprefix = "035-037|050-054|056-059" WHERE id = "3";
UPDATE regiondetails SET state = "Washington", zipcoderangeprefix = "835|838|980-985|988-994" WHERE id = "48";
UPDATE regiondetails SET state = "Wisconsin", zipcoderangeprefix = "530-532|534-535|537-539|541-549" WHERE id = "10";
UPDATE regiondetails SET state = "West Virginia", zipcoderangeprefix = "246-253|255-259|261-264|266|411-412|415-416" WHERE id = "26";
UPDATE regiondetails SET state = "Wyoming", zipcoderangeprefix = "820-831" WHERE id = "41";



# 12/17/13 Update the regiondetails table with zip codes based on new information from Fong
UPDATE regiondetails SET zipcoderangeprefix = "242|307|370-386|388|407-409|417-418|421-422|425-426|723-724" WHERE statecode = "TN";
UPDATE regiondetails SET zipcoderangeprefix = "423-424|460-469|472-479" WHERE statecode = "IN";


# ***** Until this pushed to production on 1.3.2014 ************

﻿/*  1/10/13 Update Debts action step link */
UPDATE `actionstepmeta` SET `externallink` = 'https://www.lendingtree.com/' WHERE `actionid` =17;


/*  1/29/14 Remove learning center article "Why Women Need Life Insurance" from action steps */
UPDATE actionstepmeta SET articles = "Protecting Your Loved Ones with Life Insurance#https://staging.flexscore.com/learningcenter/protecting-your-loved-ones-with-life-insurance#92|Why I Don't Want to Buy Life Insurance#https://staging.flexscore.com/learningcenter/why-i-dont-want-to-buy-life-insurance#193|Fundamental Needs for Life Insurance#https://staging.flexscore.com/learningcenter/fundamental-needs-for-life-insurance#616" WHERE actionid = 2;
UPDATE actionstepmeta SET articles = "Protecting Your Loved Ones with Life Insurance#https://staging.flexscore.com/learningcenter/protecting-your-loved-ones-with-life-insurance#92|Why I Don't Want to Buy Life Insurance#https://staging.flexscore.com/learningcenter/why-i-dont-want-to-buy-life-insurance#193|Fundamental Needs for Life Insurance#https://staging.flexscore.com/learningcenter/fundamental-needs-for-life-insurance#616" WHERE actionid = 3;

ALTER TABLE `peerranking` CHANGE `income` `income` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The average weighted income that you have';
ALTER TABLE `peerranking` CHANGE `assets` `assets` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The average weighted assets that you have';
ALTER TABLE `peerranking` CHANGE `debtresi` `debtresi` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The average debt residential';
ALTER TABLE `peerranking` CHANGE `debtequityline` `debtequityline` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The average debt equity line';
ALTER TABLE `peerranking` CHANGE `debtinstallment` `debtinstallment` DECIMAL(16,4) NOT NULL DEFAULT '0' COMMENT 'The average debt installment';

/* 02-10-2014  Risk Table Patch */
ALTER TABLE  `risk` CHANGE  `stddev`  `stddev` DECIMAL(6,2) NOT NULL DEFAULT  '0.00' COMMENT 'Projected 5 Year Standard Deviation %';
ALTER TABLE  `risk` CHANGE  `returnrate`  `returnrate` DECIMAL(6,2) NOT NULL DEFAULT  '0.00' COMMENT 'Projected 5 Year Average Return %';
ALTER TABLE  `risk` CHANGE  `metric`  `metric` FLOAT NOT NULL COMMENT  'Risk Metric';
ALTER TABLE  `risk` ADD  `high_range_of_returns` DECIMAL(6,2) NOT NULL DEFAULT '0.00' COMMENT '68% Chance Any 1 Year Return Could be this High';
ALTER TABLE  `risk` ADD  `low_range_of_returns` DECIMAL(6,2) NOT NULL DEFAULT '0.00' COMMENT '68% Chance Any 1 Year Return Could be this Low';
ALTER TABLE  `risk` ADD  `modeled_loss_expectation` DECIMAL(6,2) NOT NULL DEFAULT '0.00' COMMENT '2.5% Chance of Suffering a Large Loss Equal to This or Greater';
update  risk set stddev = '4.5', returnrate ='5.0', high_range_of_returns = '9.5', low_range_of_returns = '0.5', modeled_loss_expectation ='-4.1' where id =1;
update  risk set stddev = '5.0', returnrate ='5.5', high_range_of_returns = '10.5', low_range_of_returns = '0.5', modeled_loss_expectation ='-4.5'  where id = 2;
update  risk set stddev = '5.9', returnrate ='6.0', high_range_of_returns = '11.9', low_range_of_returns = '0.1', modeled_loss_expectation ='-5.8' where id = 3;
update  risk set stddev = '7.0', returnrate ='6.5', high_range_of_returns = '13.5', low_range_of_returns = '-0.5', modeled_loss_expectation ='-7.6'  where id = 4;
update  risk set stddev = '8.3', returnrate ='7.0', high_range_of_returns = '15.3', low_range_of_returns = '-1.3', modeled_loss_expectation ='-9.6'  where id = 5;
update  risk set stddev = '9.7', returnrate ='7.5', high_range_of_returns = '17.2', low_range_of_returns = '-2.2', modeled_loss_expectation ='-11.8'  where id = 6;
update  risk set stddev = '10.9', returnrate ='8.0', high_range_of_returns = '18.9', low_range_of_returns = '-2.9', modeled_loss_expectation ='-13.8'  where id = 7;
update  risk set stddev = '12.4', returnrate ='8.5', high_range_of_returns = '20.9', low_range_of_returns = '-3.9', modeled_loss_expectation ='-16.2'  where id = 8;
update  risk set stddev = '13.7', returnrate ='9.0', high_range_of_returns = '22.7', low_range_of_returns = '-4.7', modeled_loss_expectation ='-18.3'  where id = 9;
update  risk set stddev = '15.4', returnrate ='9.6', high_range_of_returns = '25', low_range_of_returns = '-5.8', modeled_loss_expectation ='-21.2'  where id = 10;

/*  1/29/14 Remove wrong domain name removal  staging.=>www. */
UPDATE actionstepmeta SET articles = "Protecting Your Loved Ones with Life Insurance#https://www.flexscore.com/learningcenter/protecting-your-loved-ones-with-life-insurance#92|Why I Don't Want to Buy Life Insurance#https://www.flexscore.com/learningcenter/why-i-dont-want-to-buy-life-insurance#193|Fundamental Needs for Life Insurance#https://www.flexscore.com/learningcenter/fundamental-needs-for-life-insurance#616" WHERE actionid = 2;
UPDATE actionstepmeta SET articles = "Protecting Your Loved Ones with Life Insurance#https://www.flexscore.com/learningcenter/protecting-your-loved-ones-with-life-insurance#92|Why I Don't Want to Buy Life Insurance#https://www.flexscore.com/learningcenter/why-i-dont-want-to-buy-life-insurance#193|Fundamental Needs for Life Insurance#https://www.flexscore.com/learningcenter/fundamental-needs-for-life-insurance#616" WHERE actionid = 3;


/*  02/18/14 Phase 1: Update life expectancy table based 2009 values. */
ALTER TABLE `lifeexpectancy` CHANGE `2007age` `2009age` TINYINT(4);

/*  02/18/14 Phase 2: Update life expectancy table based 2009 values. */
UPDATE `lifeexpectancy` SET `MYearsToLive` = 76, `MLifeExpectancy` = 76, `FYearsToLive` = 81, `FLifeExpectancy` = 81 WHERE `2009age` = 0;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 75, `MLifeExpectancy` = 76, `FYearsToLive` = 80, `FLifeExpectancy` = 81 WHERE `2009age` = 1;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 74, `MLifeExpectancy` = 76, `FYearsToLive` = 79, `FLifeExpectancy` = 81 WHERE `2009age` = 2;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 73, `MLifeExpectancy` = 76, `FYearsToLive` = 78, `FLifeExpectancy` = 81 WHERE `2009age` = 3;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 73, `MLifeExpectancy` = 77, `FYearsToLive` = 77, `FLifeExpectancy` = 81 WHERE `2009age` = 4;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 72, `MLifeExpectancy` = 77, `FYearsToLive` = 76, `FLifeExpectancy` = 81 WHERE `2009age` = 5;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 71, `MLifeExpectancy` = 77, `FYearsToLive` = 75, `FLifeExpectancy` = 81 WHERE `2009age` = 6;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 70, `MLifeExpectancy` = 77, `FYearsToLive` = 74, `FLifeExpectancy` = 81 WHERE `2009age` = 7;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 69, `MLifeExpectancy` = 77, `FYearsToLive` = 73, `FLifeExpectancy` = 81 WHERE `2009age` = 8;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 68, `MLifeExpectancy` = 77, `FYearsToLive` = 72, `FLifeExpectancy` = 81 WHERE `2009age` = 9;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 67, `MLifeExpectancy` = 77, `FYearsToLive` = 71, `FLifeExpectancy` = 81 WHERE `2009age` = 10;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 66, `MLifeExpectancy` = 77, `FYearsToLive` = 70, `FLifeExpectancy` = 81 WHERE `2009age` = 11;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 65, `MLifeExpectancy` = 77, `FYearsToLive` = 69, `FLifeExpectancy` = 81 WHERE `2009age` = 12;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 64, `MLifeExpectancy` = 77, `FYearsToLive` = 68, `FLifeExpectancy` = 81 WHERE `2009age` = 13;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 63, `MLifeExpectancy` = 77, `FYearsToLive` = 67, `FLifeExpectancy` = 81 WHERE `2009age` = 14;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 62, `MLifeExpectancy` = 77, `FYearsToLive` = 66, `FLifeExpectancy` = 81 WHERE `2009age` = 15;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 61, `MLifeExpectancy` = 77, `FYearsToLive` = 65, `FLifeExpectancy` = 81 WHERE `2009age` = 16;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 60, `MLifeExpectancy` = 77, `FYearsToLive` = 64, `FLifeExpectancy` = 81 WHERE `2009age` = 17;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 59, `MLifeExpectancy` = 77, `FYearsToLive` = 63, `FLifeExpectancy` = 81 WHERE `2009age` = 18;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 58, `MLifeExpectancy` = 77, `FYearsToLive` = 63, `FLifeExpectancy` = 82 WHERE `2009age` = 19;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 57, `MLifeExpectancy` = 77, `FYearsToLive` = 62, `FLifeExpectancy` = 82 WHERE `2009age` = 20;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 56, `MLifeExpectancy` = 77, `FYearsToLive` = 61, `FLifeExpectancy` = 82 WHERE `2009age` = 21;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 55, `MLifeExpectancy` = 77, `FYearsToLive` = 60, `FLifeExpectancy` = 82 WHERE `2009age` = 22;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 54, `MLifeExpectancy` = 77, `FYearsToLive` = 59, `FLifeExpectancy` = 82 WHERE `2009age` = 23;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 53, `MLifeExpectancy` = 77, `FYearsToLive` = 58, `FLifeExpectancy` = 82 WHERE `2009age` = 24;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 52, `MLifeExpectancy` = 77, `FYearsToLive` = 57, `FLifeExpectancy` = 82 WHERE `2009age` = 25;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 51, `MLifeExpectancy` = 77, `FYearsToLive` = 56, `FLifeExpectancy` = 82 WHERE `2009age` = 26;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 50, `MLifeExpectancy` = 77, `FYearsToLive` = 55, `FLifeExpectancy` = 82 WHERE `2009age` = 27;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 49, `MLifeExpectancy` = 77, `FYearsToLive` = 54, `FLifeExpectancy` = 82 WHERE `2009age` = 28;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 48, `MLifeExpectancy` = 77, `FYearsToLive` = 53, `FLifeExpectancy` = 82 WHERE `2009age` = 29;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 48, `MLifeExpectancy` = 78, `FYearsToLive` = 52, `FLifeExpectancy` = 82 WHERE `2009age` = 30;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 47, `MLifeExpectancy` = 78, `FYearsToLive` = 51, `FLifeExpectancy` = 82 WHERE `2009age` = 31;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 46, `MLifeExpectancy` = 78, `FYearsToLive` = 50, `FLifeExpectancy` = 82 WHERE `2009age` = 32;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 45, `MLifeExpectancy` = 78, `FYearsToLive` = 49, `FLifeExpectancy` = 82 WHERE `2009age` = 33;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 44, `MLifeExpectancy` = 78, `FYearsToLive` = 48, `FLifeExpectancy` = 82 WHERE `2009age` = 34;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 43, `MLifeExpectancy` = 78, `FYearsToLive` = 47, `FLifeExpectancy` = 82 WHERE `2009age` = 35;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 42, `MLifeExpectancy` = 78, `FYearsToLive` = 46, `FLifeExpectancy` = 82 WHERE `2009age` = 36;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 41, `MLifeExpectancy` = 78, `FYearsToLive` = 45, `FLifeExpectancy` = 82 WHERE `2009age` = 37;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 40, `MLifeExpectancy` = 78, `FYearsToLive` = 44, `FLifeExpectancy` = 82 WHERE `2009age` = 38;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 39, `MLifeExpectancy` = 78, `FYearsToLive` = 43, `FLifeExpectancy` = 82 WHERE `2009age` = 39;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 38, `MLifeExpectancy` = 78, `FYearsToLive` = 42, `FLifeExpectancy` = 82 WHERE `2009age` = 40;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 37, `MLifeExpectancy` = 78, `FYearsToLive` = 41, `FLifeExpectancy` = 82 WHERE `2009age` = 41;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 36, `MLifeExpectancy` = 78, `FYearsToLive` = 40, `FLifeExpectancy` = 82 WHERE `2009age` = 42;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 36, `MLifeExpectancy` = 79, `FYearsToLive` = 39, `FLifeExpectancy` = 82 WHERE `2009age` = 43;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 35, `MLifeExpectancy` = 79, `FYearsToLive` = 38, `FLifeExpectancy` = 82 WHERE `2009age` = 44;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 34, `MLifeExpectancy` = 79, `FYearsToLive` = 38, `FLifeExpectancy` = 83 WHERE `2009age` = 45;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 33, `MLifeExpectancy` = 79, `FYearsToLive` = 37, `FLifeExpectancy` = 83 WHERE `2009age` = 46;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 32, `MLifeExpectancy` = 79, `FYearsToLive` = 36, `FLifeExpectancy` = 83 WHERE `2009age` = 47;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 31, `MLifeExpectancy` = 79, `FYearsToLive` = 35, `FLifeExpectancy` = 83 WHERE `2009age` = 48;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 30, `MLifeExpectancy` = 79, `FYearsToLive` = 34, `FLifeExpectancy` = 83 WHERE `2009age` = 49;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 29, `MLifeExpectancy` = 79, `FYearsToLive` = 33, `FLifeExpectancy` = 83 WHERE `2009age` = 50;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 29, `MLifeExpectancy` = 80, `FYearsToLive` = 32, `FLifeExpectancy` = 83 WHERE `2009age` = 51;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 28, `MLifeExpectancy` = 80, `FYearsToLive` = 31, `FLifeExpectancy` = 83 WHERE `2009age` = 52;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 27, `MLifeExpectancy` = 80, `FYearsToLive` = 30, `FLifeExpectancy` = 83 WHERE `2009age` = 53;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 26, `MLifeExpectancy` = 80, `FYearsToLive` = 29, `FLifeExpectancy` = 83 WHERE `2009age` = 54;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 25, `MLifeExpectancy` = 80, `FYearsToLive` = 29, `FLifeExpectancy` = 84 WHERE `2009age` = 55;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 24, `MLifeExpectancy` = 80, `FYearsToLive` = 28, `FLifeExpectancy` = 84 WHERE `2009age` = 56;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 24, `MLifeExpectancy` = 81, `FYearsToLive` = 27, `FLifeExpectancy` = 84 WHERE `2009age` = 57;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 23, `MLifeExpectancy` = 81, `FYearsToLive` = 26, `FLifeExpectancy` = 84 WHERE `2009age` = 58;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 22, `MLifeExpectancy` = 81, `FYearsToLive` = 25, `FLifeExpectancy` = 84 WHERE `2009age` = 59;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 21, `MLifeExpectancy` = 81, `FYearsToLive` = 24, `FLifeExpectancy` = 84 WHERE `2009age` = 60;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 21, `MLifeExpectancy` = 82, `FYearsToLive` = 23, `FLifeExpectancy` = 84 WHERE `2009age` = 61;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 20, `MLifeExpectancy` = 82, `FYearsToLive` = 23, `FLifeExpectancy` = 85 WHERE `2009age` = 62;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 19, `MLifeExpectancy` = 82, `FYearsToLive` = 22, `FLifeExpectancy` = 85 WHERE `2009age` = 63;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 18, `MLifeExpectancy` = 82, `FYearsToLive` = 21, `FLifeExpectancy` = 85 WHERE `2009age` = 64;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 18, `MLifeExpectancy` = 83, `FYearsToLive` = 20, `FLifeExpectancy` = 85 WHERE `2009age` = 65;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 17, `MLifeExpectancy` = 83, `FYearsToLive` = 19, `FLifeExpectancy` = 85 WHERE `2009age` = 66;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 16, `MLifeExpectancy` = 83, `FYearsToLive` = 19, `FLifeExpectancy` = 86 WHERE `2009age` = 67;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 15, `MLifeExpectancy` = 83, `FYearsToLive` = 18, `FLifeExpectancy` = 86 WHERE `2009age` = 68;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 15, `MLifeExpectancy` = 84, `FYearsToLive` = 17, `FLifeExpectancy` = 86 WHERE `2009age` = 69;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 14, `MLifeExpectancy` = 84, `FYearsToLive` = 16, `FLifeExpectancy` = 86 WHERE `2009age` = 70;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 13, `MLifeExpectancy` = 84, `FYearsToLive` = 16, `FLifeExpectancy` = 87 WHERE `2009age` = 71;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 13, `MLifeExpectancy` = 85, `FYearsToLive` = 15, `FLifeExpectancy` = 87 WHERE `2009age` = 72;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 12, `MLifeExpectancy` = 85, `FYearsToLive` = 14, `FLifeExpectancy` = 87 WHERE `2009age` = 73;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 11, `MLifeExpectancy` = 85, `FYearsToLive` = 13, `FLifeExpectancy` = 87 WHERE `2009age` = 74;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 11, `MLifeExpectancy` = 86, `FYearsToLive` = 13, `FLifeExpectancy` = 88 WHERE `2009age` = 75;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 10, `MLifeExpectancy` = 86, `FYearsToLive` = 12, `FLifeExpectancy` = 88 WHERE `2009age` = 76;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 10, `MLifeExpectancy` = 87, `FYearsToLive` = 11, `FLifeExpectancy` = 88 WHERE `2009age` = 77;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 9, `MLifeExpectancy` = 87, `FYearsToLive` = 11, `FLifeExpectancy` = 89 WHERE `2009age` = 78;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 9, `MLifeExpectancy` = 88, `FYearsToLive` = 10, `FLifeExpectancy` = 89 WHERE `2009age` = 79;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 8, `MLifeExpectancy` = 88, `FYearsToLive` = 10, `FLifeExpectancy` = 90 WHERE `2009age` = 80;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 8, `MLifeExpectancy` = 89, `FYearsToLive` = 9, `FLifeExpectancy` = 90 WHERE `2009age` = 81;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 7, `MLifeExpectancy` = 89, `FYearsToLive` = 9, `FLifeExpectancy` = 91 WHERE `2009age` = 82;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 7, `MLifeExpectancy` = 90, `FYearsToLive` = 8, `FLifeExpectancy` = 91 WHERE `2009age` = 83;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 6, `MLifeExpectancy` = 90, `FYearsToLive` = 7, `FLifeExpectancy` = 91 WHERE `2009age` = 84;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 6, `MLifeExpectancy` = 91, `FYearsToLive` = 7, `FLifeExpectancy` = 92 WHERE `2009age` = 85;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 5, `MLifeExpectancy` = 91, `FYearsToLive` = 6, `FLifeExpectancy` = 92 WHERE `2009age` = 86;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 5, `MLifeExpectancy` = 92, `FYearsToLive` = 6, `FLifeExpectancy` = 93 WHERE `2009age` = 87;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 5, `MLifeExpectancy` = 93, `FYearsToLive` = 6, `FLifeExpectancy` = 94 WHERE `2009age` = 88;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 4, `MLifeExpectancy` = 93, `FYearsToLive` = 5, `FLifeExpectancy` = 94 WHERE `2009age` = 89;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 4, `MLifeExpectancy` = 94, `FYearsToLive` = 5, `FLifeExpectancy` = 95 WHERE `2009age` = 90;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 4, `MLifeExpectancy` = 95, `FYearsToLive` = 5, `FLifeExpectancy` = 96 WHERE `2009age` = 91;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 3, `MLifeExpectancy` = 95, `FYearsToLive` = 4, `FLifeExpectancy` = 96 WHERE `2009age` = 92;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 3, `MLifeExpectancy` = 96, `FYearsToLive` = 4, `FLifeExpectancy` = 97 WHERE `2009age` = 93;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 3, `MLifeExpectancy` = 97, `FYearsToLive` = 4, `FLifeExpectancy` = 98 WHERE `2009age` = 94;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 3, `MLifeExpectancy` = 98, `FYearsToLive` = 3, `FLifeExpectancy` = 98 WHERE `2009age` = 95;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 3, `MLifeExpectancy` = 99, `FYearsToLive` = 3, `FLifeExpectancy` = 99 WHERE `2009age` = 96;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 99, `FYearsToLive` = 3, `FLifeExpectancy` = 100 WHERE `2009age` = 97;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 100, `FYearsToLive` = 3, `FLifeExpectancy` = 101 WHERE `2009age` = 98;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 101, `FYearsToLive` = 3, `FLifeExpectancy` = 102 WHERE `2009age` = 99;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 102, `FYearsToLive` = 2, `FLifeExpectancy` = 102 WHERE `2009age` = 100;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 103, `FYearsToLive` = 2, `FLifeExpectancy` = 103 WHERE `2009age` = 101;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 104, `FYearsToLive` = 2, `FLifeExpectancy` = 104 WHERE `2009age` = 102;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 105, `FYearsToLive` = 2, `FLifeExpectancy` = 105 WHERE `2009age` = 103;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 106, `FYearsToLive` = 2, `FLifeExpectancy` = 106 WHERE `2009age` = 104;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 107, `FYearsToLive` = 2, `FLifeExpectancy` = 107 WHERE `2009age` = 105;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 2, `MLifeExpectancy` = 108, `FYearsToLive` = 2, `FLifeExpectancy` = 108 WHERE `2009age` = 106;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 108, `FYearsToLive` = 2, `FLifeExpectancy` = 109 WHERE `2009age` = 107;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 109, `FYearsToLive` = 1, `FLifeExpectancy` = 109 WHERE `2009age` = 108;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 110, `FYearsToLive` = 1, `FLifeExpectancy` = 110 WHERE `2009age` = 109;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 111, `FYearsToLive` = 1, `FLifeExpectancy` = 111 WHERE `2009age` = 110;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 112, `FYearsToLive` = 1, `FLifeExpectancy` = 112 WHERE `2009age` = 111;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 113, `FYearsToLive` = 1, `FLifeExpectancy` = 113 WHERE `2009age` = 112;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 114, `FYearsToLive` = 1, `FLifeExpectancy` = 114 WHERE `2009age` = 113;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 115, `FYearsToLive` = 1, `FLifeExpectancy` = 115 WHERE `2009age` = 114;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 116, `FYearsToLive` = 1, `FLifeExpectancy` = 116 WHERE `2009age` = 115;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 117, `FYearsToLive` = 1, `FLifeExpectancy` = 117 WHERE `2009age` = 116;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 118, `FYearsToLive` = 1, `FLifeExpectancy` = 118 WHERE `2009age` = 117;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 119, `FYearsToLive` = 1, `FLifeExpectancy` = 119 WHERE `2009age` = 118;
UPDATE `lifeexpectancy` SET `MYearsToLive` = 1, `MLifeExpectancy` = 120, `FYearsToLive` = 1, `FLifeExpectancy` = 120 WHERE `2009age` = 119;

/*  02/18/14 Update cut-off actionstep description and add link. */
UPDATE  `actionstepmeta` SET  `description` =  'Not having all of your eggs in one basket is important. One of the easiest ways to do that is to include “alternative asset” classes to your portfolio. Read these article(s) to help you understand why.<br>{{title}}' WHERE  `actionstepmeta`.`actionid` =41;

update actionstepmeta set link = 'learnmore', linktype = 'action', type ='instant', reviewstatus = '0', buttonstep1 = 'Learn more', buttonstep2 = 'Learn more', linkstep1 = 'Learn more', linkstep2 = 'Learn more' where actionid=41;

update actionstepmeta set points = 12, link = 'addasset', type ='instant', linktype='action', buttonstep1 = 'Get Started', buttonstep2 = 'Mark as Done', linkstep1 = 'Update Account', linkstep2 = 'Update Account', description = "You've indicated you have a pension plan that would pay you a retirement income guaranteed by an employer. Have you recently looked at your beneficiaries listed on this pension?<br>{{title}}<br>This would be a good idea." where actionid=73;

/* 02/25/2014 - Added by manju column update for action step id #22*/

UPDATE `actionstepmeta` SET `status` = '0', `buttonstep1` = 'Set an Action', `link` = 'addasset'  WHERE `actionid` = 22;

/* 02/25/2014 - Added By Rajeev - Column update for Pension action step #77 */

UPDATE `actionstepmeta` SET `link` = 'addestate', `linktype` = 'action', `buttonstep1` = 'Set an Action', `buttonstep2` = 'Mark as Done', `linkstep1` = 'I did this', `linkstep2` = 'Connect' WHERE `actionid` = 77;

/* New Action Step Payoff Debts Implemnentation*/
INSERT INTO `actionstepmeta` (
`actionid` ,
`actionname` ,
`category` ,
`points` ,
`link` ,
`externallink` ,
`vtitle` ,
`vkey` ,
`linktype` ,
`type` ,
`description` ,
`articles` ,
`wfpointlink` ,
`buttonstep1` ,
`buttonstep2` ,
`linkstep1` ,
`linkstep2` ,
`priority` ,
`status` ,
`reviewstatus`
)
VALUES (
'93', 'Pay Off Debt Plan', 'Goal Planning', '10', '', '', '', '', 'other', 'instant', '{{title}}', '', '', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '0', '0', '0');

/*  added pdf path in description for action step #77 */

UPDATE `actionstepmeta` SET `description` = 'You may have some very valuable things hidden in places that you only know about (cash in the mattress, treasure in the backyard). <a href="ui/content/ConfidentialDocsandCredentialLocator.pdf" target="_blank">Click here</a> to create an important document to protect those assets. This document can be kept in a safe-deposit box at a bank, or safe place of your choice.' WHERE `actionid` =77;

/* Updating the Action ID 50 and 51 to match 12 */
UPDATE actionstepmeta SET link = 'addasset',buttonstep1 = 'Get Started',buttonstep2 = 'Mark as Done',linktype = 'action',linkstep1 = 'Add IRA',linkstep2 = 'Add IRA' WHERE `actionid` =51;
UPDATE actionstepmeta SET link = 'addasset',buttonstep1 = 'Get Started',buttonstep2 = 'Mark as Done',linktype = 'action',linkstep1 = 'Add Account',linkstep2 = 'Add Account' WHERE `actionid` =50;


/* 02/27/2014 Update actionstepmeta for steps 49, 43, 47 and add new 91 */
UPDATE `actionstepmeta` SET `actionname` = 'Maximize Contribution to Traditional IRA', `link` = 'learnmore',
    `link` = 'addasset', `linktype` = 'link', `points` = 15,
    `externallink` = 'http://www.schwab.com/public/schwab/investing/accounts_products/accounts/ira/traditional_ira',
    `description` = 'You may be eligible to contribute ${{amt}} per month to more retirement accounts.  {{lnk}} to find out why utlizing an IRA Account makes sense for you.<br>{{title}}<br>{{lnk}} to get a Traditional IRA.',
    `articles` = 'Tax Planning for Income#https://www.flexscore.com/learningcenter/tax-planning-for-income#221|Traditional IRAs and Roth IRAs#https://www.flexscore.com/learningcenter/traditional-iras-and-roth-iras#1144|Am I Having Enough Withheld?#https://www.flexscore.com/learningcenter/am-i-having-enough-withheld#1221',
    `externallink` = 'https://client.schwab.com/Login/AccountOpen/GAOLaunch.aspx?application_type=IRA&ira_type=Traditional',
    `linkstep1` = 'Add IRA', `linkstep2` = 'Add IRA'
 WHERE `actionid` = 43;

UPDATE `actionstepmeta` SET `actionname` = 'Maximize Contribution to Roth IRA', `link` = 'learnmore',
    `link` = 'addasset', `linktype` = 'link', `points` = 15,
    `externallink` = 'http://www.schwab.com/public/schwab/investing/accounts_products/accounts/ira/roth_ira',
    `description` = 'Since you already have enough tax deductions, you should consider opening a Roth Individual Retirement Account (IRA) and begin making a monthly non-tax-deductible contribution of ${{amt}} towards your future savings goals. <br>{{title}}<br>{{lnk}} to get a Roth IRA.',
    `articles` = 'Tax Planning for Income#https://www.flexscore.com/learningcenter/tax-planning-for-income#221|Traditional IRAs and Roth IRAs#https://www.flexscore.com/learningcenter/traditional-iras-and-roth-iras#1144|Am I Having Enough Withheld?#https://www.flexscore.com/learningcenter/am-i-having-enough-withheld#1221',
    `category` = 'Retirement Planning',  `linkstep1` = 'Add IRA', `linkstep2` = 'Add IRA'
WHERE `actionid` = 49;

UPDATE `actionstepmeta` SET `actionname` = 'Consider Decreasing Tax Withholding Amount using W4 Form at Work',
    `description` = 'Your tax payments are over $1,000 so you may want to consider increasing your tax withholding from your wages and salary.  Ask your human resource department for a new W-4 form to increase the amount of tax withholding from pay checks.'
    WHERE `actionid` = 47;

INSERT INTO `actionstepmeta` VALUES(91, 'Consider Increasing Tax Withholding Amount using W4 Form at Work', 'Tax Planning', 10, '', '', '', '', 'other', 'short',
    'Your tax refunds are over $1,000 so you may want to consider decreasing your tax withholding from your wages and salary.  Ask your human resource department for a new W-4 form to decrease the amount of tax withholding from pay checks.',
    '', '', 'Get Started', '', 'I read this', '', '', 0, '0', 1);


UPDATE `actionstepmeta` SET `description` = 'You may have some very valuable things hidden in places that you only know about (cash in the mattress, treasure in the backyard). <br><br><a href="ui/content/ConfidentialDocsandCredentialLocator.pdf" target="_blank">Click here</a> to create an important document to protect those assets. This document can be kept in a safe-deposit box at a bank, or safe place of your choice.' WHERE `actionid` =77;
UPDATE actionstepmeta SET buttonstep1 = 'Get Started',linkstep1 = 'Update',linkstep2 = 'Update' WHERE `actionid` =77;

/*Update Action Step Title*/

UPDATE `actionstepmeta` SET `actionname` = 'Give Us More Income Details' WHERE `actionid` = 31;
UPDATE `actionstepmeta` SET `actionname` = 'Give Us More Expense Details' WHERE `actionid` = 32;

/*Make pdflink url dynamic by adding {{pdflink}} in Create Informational Sheet action step */

UPDATE `actionstepmeta` SET `description` = 'You may have some very valuable things hidden in places that you only know about (cash in the mattress, treasure in the backyard). <br><br> {{pdflink}} to create an important document to protect those assets. This document can be kept in a safe-deposit box at a bank, or safe place of your choice.' WHERE `actionid` =77;

UPDATE actionstepmeta SET linkstep1 = 'Add Account',linkstep2 = 'Add Account' WHERE `actionid` =51;

UPDATE actionstepmeta SET link = 'adddebt',buttonstep1 = 'Get Started',buttonstep2 = 'Mark as Done',linktype = 'action',linkstep1 = 'Update Debt',linkstep2 = 'Update Debt' WHERE `actionid` = 93;
# ***** Untill this pushed to production on 03.3.2014 ************

/* New Action Step Payoff Debts Implemnentation*/
INSERT INTO `actionstepmeta` (
`actionid` ,
`actionname` ,
`category` ,
`points` ,
`link` ,
`externallink` ,
`vtitle` ,
`vkey` ,
`linktype` ,
`type` ,
`description` ,
`articles` ,
`wfpointlink` ,
`buttonstep1` ,
`buttonstep2` ,
`linkstep1` ,
`linkstep2` ,
`priority` ,
`status` ,
`reviewstatus`
)
VALUES (
'94', 'Retirement Goal Plan', 'Financial Planning', '10', 'addassets', '', '', '', 'action', 'short', 'You don\'t have much in savings right now. Let\'s set you up with a plan that\'s easy to manage and gives you room to breathe.', '', '', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '0', '0', '0');

UPDATE actionstepmeta SET `description` = 'You don''t have much in savings right now. Let''s set you up with a plan that''s easy to manage and gives you room to breathe.{{title}} ' WHERE actionid=94;

/*Must Have: Car Finance Action Step #92*/
INSERT INTO `actionstepmeta` (`actionid`, `actionname`, `category`, `points`, `link`, `externallink`, `vtitle`, `vkey`, `linktype`, `type`, `description`, `articles`, `wfpointlink`, `buttonstep1`, `buttonstep2`, `linkstep1`, `linkstep2`, `priority`, `status`, `reviewstatus`) VALUES (92, 'Car Finance', 'Debt Optimization', '13', 'adddebt', 'http://carfinance.com/apply/?aid=40549', '', '', 'link', 'short', 'Having a loan on depreciating assets like vehicles works best when the loan interest rate is as low as possible. You might be able to get a lower rate and re-finance the following loans.<br>{{title}}<br>This might help you get out of debt sooner. {{lnk}} to begin.', '', '', 'Get Started', '', 'Update Debts', '', '1', '0', '0');

UPDATE actionstepmeta SET `link` = 'addasset' WHERE `actionid` =94;

UPDATE actionstepmeta SET `status` = '1' WHERE `actionid` =6;

update actionstepmeta set description = 'You may be eligible to contribute ${{amt}} per month to retirement accounts. {{lnk}} to get a Traditional IRA.<br><br>Read below articles to find out why utlizing an IRA Account makes sense for you.<br>{{title}}<br>' where actionid=43;

update actionstepmeta set status='1' where actionid=12;

/* 03/07/14 change green button for Retirement Goal Plan step */
UPDATE actionstepmeta SET `linkstep1` = 'Update Assets', `linkstep2` = 'Update Assets' WHERE `actionid`=94;

/* 03/24/14 change green button for Retirement Goal Plan / Increase Savings step */
update actionstepmeta set description = '{{title}}' where actionid in (22,94);
update actionstepmeta set buttonstep1 = 'Get Started', buttonstep2 = 'Mark as Done', linkstep1 = 'Update Assets', linkstep2 = 'Update Assets' where actionid in (22,94);

/* 03/25/14 */
update actionstepmeta set  description = 'Your tax refunds are over $1,000 so you may want to consider decreasing your tax withholding from your wages and salary.  Ask your human resource department for a new W-4 form to decrease the amount of tax withholding from pay checks.' where actionid=47;

update actionstepmeta set  description = 'Your tax payments are over $1,000 so you may want to consider increasing your tax withholding from your wages and salary.  Ask your human resource department for a new W-4 form to increase the amount of tax withholding from pay checks.' where actionid=91;

# ***** Untill this pushed to production on 03.28.2014 ************

# ***** From this pushed to production on 5.09.2014 ************
/* New Action Step Payoff Debts Implemnentation*/
INSERT INTO `actionstepmeta` (
`actionid` ,
`actionname` ,
`category` ,
`points` ,
`link` ,
`externallink` ,
`vtitle` ,
`vkey` ,
`linktype` ,
`type` ,
`description` ,
`articles` ,
`wfpointlink` ,
`buttonstep1` ,
`buttonstep2` ,
`linkstep1` ,
`linkstep2` ,
`priority` ,
`status` ,
`reviewstatus`
)
VALUES (
'94', 'Retirement Goal Plan', 'Financial Planning', '10', 'addassets', '', '', '', 'action', 'short', 'You don\'t have much in savings right now. Let\'s set you up with a plan that\'s easy to manage and gives you room to breathe.', '', '', 'Set an Action', 'Mark as Done', 'I did this', 'Connect', '0', '0', '0');

UPDATE actionstepmeta SET `description` = 'You don''t have much in savings right now. Let''s set you up with a plan that''s easy to manage and gives you room to breathe.{{title}} ' WHERE actionid=94;

/*Must Have: Car Finance Action Step #92*/
INSERT INTO `actionstepmeta` (`actionid`, `actionname`, `category`, `points`, `link`, `externallink`, `vtitle`, `vkey`, `linktype`, `type`, `description`, `articles`, `wfpointlink`, `buttonstep1`, `buttonstep2`, `linkstep1`, `linkstep2`, `priority`, `status`, `reviewstatus`) VALUES (92, 'Car Finance', 'Debt Optimization', '13', 'adddebt', 'http://carfinance.com/apply/?aid=40549', '', '', 'link', 'short', 'Having a loan on depreciating assets like vehicles works best when the loan interest rate is as low as possible. You might be able to get a lower rate and re-finance the following loans.<br>{{title}}<br>This might help you get out of debt sooner. {{lnk}} to begin.', '', '', 'Get Started', '', 'Update Debts', '', '1', '0', '0');

UPDATE actionstepmeta SET `link` = 'addasset' WHERE `actionid` =94;

UPDATE actionstepmeta SET `status` = '1' WHERE `actionid` =6;

update actionstepmeta set description = 'You may be eligible to contribute ${{amt}} per month to retirement accounts. {{lnk}} to get a Traditional IRA.<br><br>Read below articles to find out why utlizing an IRA Account makes sense for you.<br>{{title}}<br>' where actionid=43;

update actionstepmeta set status='1' where actionid=12;

/* 03/07/14 change green button for Retirement Goal Plan step */
UPDATE actionstepmeta SET `linkstep1` = 'Update Assets', `linkstep2` = 'Update Assets' WHERE `actionid`=94;

/* 03/24/14 change green button for Retirement Goal Plan / Increase Savings step */
update actionstepmeta set description = '{{title}}' where actionid in (22,94);
update actionstepmeta set buttonstep1 = 'Get Started', buttonstep2 = 'Mark as Done', linkstep1 = 'Update Assets', linkstep2 = 'Update Assets' where actionid in (22,94);

/* 03/25/14 */
update actionstepmeta set  description = 'Your tax refunds are over $1,000 so you may want to consider decreasing your tax withholding from your wages and salary.  Ask your human resource department for a new W-4 form to decrease the amount of tax withholding from pay checks.' where actionid=47;

update actionstepmeta set  description = 'Your tax payments are over $1,000 so you may want to consider increasing your tax withholding from your wages and salary.  Ask your human resource department for a new W-4 form to increase the amount of tax withholding from pay checks.' where actionid=91;

/* Above is not in meta-patch.sql in master */
/* /04/03/2014 - For Make Score engine update for all constants we updated (risk / life expectancy)  */
CREATE TABLE IF NOT EXISTS `constantslastupdated` (
  `constant` varchar(50) NOT NULL,
  `lastupdated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `constantslastupdated` (`constant`, `lastupdated`) VALUES
('Risk', '2014-01-01'),
('Life  Expectancy', '2014-01-01');



-- Database: `leapscoremeta`
--

-- --------------------------------------------------------

--
-- Table structure for table `riskfactors`
--

CREATE TABLE IF NOT EXISTS `riskfactors` (
  `risk` smallint(6) NOT NULL COMMENT 'Risk Number',
  `domestic_equity` float NOT NULL COMMENT 'Domestic Equity',
  `international_equity` float NOT NULL COMMENT 'International Equity',
  `altr_non_corelated_assets` float NOT NULL COMMENT 'Alternative / Non-Correlated Assets',
  `income_bonds` float NOT NULL COMMENT 'Income or Bonds',
  `market_cash` float NOT NULL COMMENT 'Market or Cash',
  PRIMARY KEY (`risk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `riskfactors`
--

INSERT INTO `riskfactors` (`risk`, `domestic_equity`, `international_equity`, `altr_non_corelated_assets`, `income_bonds`, `market_cash`) VALUES
(1, 5, 3, 15, 72, 5),
(2, 10, 6, 12, 67, 5),
(3, 16, 10, 9, 60, 5),
(4, 22, 15, 9, 50, 4),
(5, 26, 20, 9, 41, 4),
(6, 31, 24, 9, 32, 4),
(7, 36, 28, 10, 23, 3),
(8, 41, 32, 10, 15, 2),
(9, 47, 36, 8, 8, 1),
(10, 53, 39, 5, 3, 0);


UPDATE `risk` SET `stddev` = '5.6' WHERE `risk` =1;
UPDATE `risk` SET `stddev` = '5.9' WHERE `risk` =2;
UPDATE `risk` SET `stddev` = '6.6' WHERE `risk` =3;
UPDATE `risk` SET `stddev` = '7.6' WHERE `risk` =4;
UPDATE `risk` SET `stddev` = '8.7' WHERE `risk` =5;
UPDATE `risk` SET `stddev` = '9.9' WHERE `risk` =6;
UPDATE `risk` SET `stddev` = '11.2' WHERE `risk` =7;
UPDATE `risk` SET `stddev` = '12.25' WHERE `risk` =8;
UPDATE `risk` SET `stddev` = '13.9' WHERE `risk` =9;
UPDATE `risk` SET `stddev` = '15.2' WHERE `risk` =10;


UPDATE `actionstepmeta` SET `description` = '{{title}}' , linktype='action',`vtitle` = 'Investment Diversification' ,`link` = 'addasset',`buttonstep2` = 'Mark as Done',`linkstep1` = 'Update Assets',`linkstep2` = 'Connect' WHERE `actionid` = 6;

/*description fix for action step 46 Consider Your Estate Planning Needs*/
UPDATE `actionstepmeta` SET `description` = 'Having a plan in place that tells others how you\'d like to have your assets managed and dependents cared for is very important. Consider your situation and the legacy you\'d leave today if you don\'t have a plan in place.' WHERE `actionid` = 46;
drop table `tickerinfo`;


/* 04/24/14 update constants table */
UPDATE `constantslastupdated` SET constant = 'LifeExpectancy', lastupdated = '2014-04-24' WHERE constant = "Life  Expectancy";
UPDATE `constantslastupdated` SET lastupdated = '2014-04-24' WHERE constant = "Risk";
INSERT INTO `constantslastupdated` (`constant`, `lastupdated`) VALUES ('Profile', '2014-04-24');

/* May 6 2014 - making actionstep inactive */
update actionstepmeta set status = '1' where actionid=6;
UPDATE `risk` SET `stddev` = '12.5' WHERE `risk` =8;

UPDATE `actionstepmeta` SET `description` = 'It is a good idea to periodically review your health insurance coverage, especially if you\'ve experienced a major life event or change in your health. Contact your current provider as a first step. If you would like for us to help {{lnk}}.' WHERE `actionid` = 87;
UPDATE `actionstepmeta` SET `description` = 'It is a good idea to periodically review your life insurance coverage, especially if you\'ve experienced a major life event or change in your lifestyle costs. Contact your current provider as a first step. If you would like for us to help {{lnk}}.' WHERE `actionid` = 88;
UPDATE `actionstepmeta` SET `description` = 'It is a good idea to periodically review your disability insurance coverage, especially if you\'ve experienced a major life event or change in your income. Contact your current provider as a first step. If you would like for us to help {{lnk}}.' WHERE `actionid` = 89;
# ***** Until this pushed to production on 5.09.2014 ************

# ***** From Until this pushed to production on 5.26.2014 ************
update actionstepmeta set status = '0' where actionid=6;
# ***** Until this pushed to production on 5.26.2014 ************

# ***** Until this pushed to production on 6.15.2014 ************


/* addition of column for external institution logo */
ALTER TABLE `actionstepmeta` ADD `external_institution_logo` VARCHAR( 255 ) NULL AFTER `externallink` ;

/* logo paths*/
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.insure.com/images/logo.png' WHERE `actionid` =2;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.insure.com/images/logo.png' WHERE `actionid` =3;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.legalzoom.com/sem/images/header/lz_logo_amypages.png' WHERE `actionid` =13;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.legalzoom.com/sem/images/header/lz_logo_amypages.png' WHERE `actionid` =14;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.lendingtree.com/assets/img/logo.png' WHERE `actionid` =17;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.flexoffers.com/images/newimages/logo.gif' WHERE `actionid` =20;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.trustedid.com/images/tid/logo.png' WHERE `actionid` =23;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.insure.com/images/logo.png' WHERE `actionid` =35;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.insure.com/images/logo.png' WHERE `actionid` =36;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://upload.wikimedia.org/wikipedia/en/9/9c/Charles_Schwab_logo.png' WHERE `actionid` =43;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://upload.wikimedia.org/wikipedia/en/9/9c/Charles_Schwab_logo.png' WHERE `actionid` =49;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.flexoffers.com/images/newimages/logo.gif' WHERE `actionid` =52;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://upload.wikimedia.org/wikipedia/en/2/21/Bankrate-logo.jpg' WHERE `actionid` =53;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.insure.com/images/logo.png' WHERE `actionid` =60;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.insure.com/images/logo.png' WHERE `actionid` =61;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.geico.com/public/layout_images/homepage/design4/logo.png' WHERE `actionid` =65;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.flexoffers.com/images/newimages/logo.gif' WHERE `actionid` =66;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.flexoffers.com/images/newimages/logo.gif' WHERE `actionid` =67;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://upload.wikimedia.org/wikipedia/en/2/21/Bankrate-logo.jpg' WHERE `actionid` =85;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.healthcare.gov/images/logo.png' WHERE `actionid` =86;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.healthcare.gov/images/logo.png' WHERE `actionid` =87;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.insure.com/images/logo.png' WHERE `actionid` =88;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'http://www.insure.com/images/logo.png' WHERE `actionid` =89;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://carfinance.com/apply/assets/img/Logo.gif' WHERE `actionid` =92;

UPDATE `actionstepmeta` SET `description` = 'Need a better credit score? We''re here to help with that!. To monitor your score and protect your identity, {{lnk}}.' WHERE `actionid` =23;

/* 06/02/2014 update and confirm correct 2014 values for risk table*/
UPDATE `risk` SET `stddev` ='5.60', `returnrate` ='5.40', `high_range_of_returns` ='11.00', `low_range_of_returns` ='-0.20', `modeled_loss_expectation` ='-5.80' where `risk` ='1';
UPDATE `risk` SET `stddev` ='5.90', `returnrate` ='5.90', `high_range_of_returns` ='11.80', `low_range_of_returns` ='0.00', `modeled_loss_expectation` ='-5.90' where `risk` ='2';
UPDATE `risk` SET `stddev` ='6.60', `returnrate` ='6.40', `high_range_of_returns` ='13.00', `low_range_of_returns` ='-0.20', `modeled_loss_expectation` ='-6.80' where `risk` ='3';
UPDATE `risk` SET `stddev` ='7.60', `returnrate` ='7.00', `high_range_of_returns` ='14.60', `low_range_of_returns` ='-0.60', `modeled_loss_expectation` ='-8.20' where `risk` ='4';
UPDATE `risk` SET `stddev` ='8.70', `returnrate` ='7.50', `high_range_of_returns` ='16.20', `low_range_of_returns` ='-1.20', `modeled_loss_expectation` ='-9.80' where `risk` ='5';
UPDATE `risk` SET `stddev` ='9.90', `returnrate` ='8.00', `high_range_of_returns` ='17.90', `low_range_of_returns` ='-1.90', `modeled_loss_expectation` ='-11.70' where `risk` ='6';
UPDATE `risk` SET `stddev` ='11.20', `returnrate` ='8.50', `high_range_of_returns` ='19.70', `low_range_of_returns` ='-2.70', `modeled_loss_expectation` ='-13.90' where `risk` ='7';
UPDATE `risk` SET `stddev` ='12.50', `returnrate` ='9.00', `high_range_of_returns` ='21.50', `low_range_of_returns` ='-3.50', `modeled_loss_expectation` ='-15.90' where `risk` ='8';
UPDATE `risk` SET `stddev` ='13.90', `returnrate` ='9.60', `high_range_of_returns` ='23.50', `low_range_of_returns` ='-4.30', `modeled_loss_expectation` ='-18.10' where `risk` ='9';
UPDATE `risk` SET `stddev` ='15.20', `returnrate` ='10.10', `high_range_of_returns` ='25.30', `low_range_of_returns` ='-5.10', `modeled_loss_expectation` ='-20.30' where `risk` ='10';

/* 06/03/2014 set the risk constants to be update as of 6/3/14 */
UPDATE constantslastupdated SET lastupdated = "2014-06-03" WHERE constant = "Risk";
/* 06/04/2014 adding a column to have external link name and description 06/04/14 */
ALTER TABLE `actionstepmeta` ADD `externallinkname`  VARCHAR( 255 ) NOT NULL COMMENT 'Externl (3rd party) link short description.' AFTER `external_institution_logo`;
ALTER TABLE `actionstepmeta` ADD `externallinkdescription` TEXT NOT NULL COMMENT 'Externl (3rd party) product name.' AFTER `externallinkname`;
UPDATE `actionstepmeta` SET `externallinkname` = 'Insurance' , `externallinkdescription` = 'Insure.com provides a comprehensive array of information on life, health, auto, and home insurance. We offer a vast library of originally authored insurance articles and decision-making tools that are not available from any other single source.' WHERE `actionid` in (2,3,35,36,60,61,88,89);
UPDATE `actionstepmeta` SET `externallinkname` = 'Business and Personal Service' , `externallinkdescription` = 'Our online service that helped people create their own legal documents. We brought together some of the best minds in the legal and technological fields to make this vision a reality. The result is LegalZoom, the leading, nationally recognized legal brand for small business and consumers in the United States.' WHERE `actionid` in (13,14);
UPDATE `actionstepmeta` SET `externallinkname` = 'Mortgage Loan Service' , `externallinkdescription` = 'LendingTree, LLC is the leading online lender exchange. LendingTree provides a marketplace that connects consumers with multiple lenders, as well as an array of online tools to aid consumers in their financial decisions. Since inception, LendingTree has facilitated more than 30 million loan requests and $214 billion in closed loan transactions. LendingTree provides access to lenders offering mortgages and refinance loans, home equity loans/lines of credit, and more. LendingTree, LLC is a subsidiary of Tree.com, Inc. (NASDAQ: TREE).' WHERE `actionid` =17;
UPDATE `actionstepmeta` SET `externallinkname` = 'Instant Funding' , `externallinkdescription` = 'FlexOffers.com is a premiere affiliate network building mutually profitable relationships between strategic, skilled and trustworthy online publishers and a robust portfolio of over 4,000 popular advertiser companies spanning all verticals. With over 10+ years of experience in the affiliate marketing industry, we offer unparalleled customer service, an array of optimized data delivery tools, and fast and dependable payments – proving that flexibility is the key to affiliate success.' WHERE `actionid` in (20,52,66,67);
UPDATE `actionstepmeta` SET `externallinkname` = 'Credit and Identity Protection' , `externallinkdescription` = 'TrustedID delivers identity protection, credit monitoring, privacy and reputation management products to help protect against credit and identity theft--safeguarding individuals, families, and businesses.' WHERE `actionid` =23;
UPDATE `actionstepmeta` SET `externallinkname` = 'Online Trading & Investing' , `externallinkdescription` = 'Charles Schwab is a different kind of financial services firm with a 40-year history of advocating for the individual investor. Seeing our business "through clients’ eyes" makes all the difference.' WHERE `actionid` in (43,49);
UPDATE `actionstepmeta` SET `externallinkname` = 'General Insurance' , `externallinkdescription` = 'The Government Employees Insurance Company is an auto insurance company. It is the second largest auto insurer in the United States. It is a wholly owned subsidiary of Berkshire Hathaway that as of 2007 provided coverage for more than 13 million motor vehicles owned by more than 12 million policy holders.' WHERE `actionid` =65;
UPDATE `actionstepmeta` SET `externallinkname` = 'Mortgage and Credit Card Refinance',`externallinkdescription` = 'Bankrate, Inc. is the Web`s leading aggregator of financial rate information, offering an unparalleled depth and breadth of rate data and financial content.' WHERE `actionid` in (53,85);
UPDATE `actionstepmeta` SET `externallinkname` = 'Health Insurance',`externallinkdescription` = 'HealthCare.gov is a health insurance exchange website operated under the United States federal government under the provisions of the Patient Protection and Affordable Care Act (ACA, often known as `Obamacare`), designed to serve the residents of the thirty-six U.S. states that opted not to create their own state exchanges.' WHERE `actionid` in (86,87);
UPDATE `actionstepmeta` SET `externallinkname` = 'Auto Loan',`externallinkdescription` = 'CarFinance.com is the direct lending solution from CarFinance Capital, LLC, specializing in new, used and refinance auto loans for non-prime customers. Licensed in 46 states covering 80% of the nation’s car purchasing population.' WHERE `actionid` =92;

/* 06/13/2014 Adding update points change */
INSERT INTO `constantslastupdated` (`constant`, `lastupdated`) VALUES ('Special', '2014-06-13');

/* Melroy cleaning up IRA action steps */
UPDATE `actionstepmeta` SET `externallinkname` = 'Charles Schwab' , `externallinkdescription` = 'Charles Schwab is a different kind of financial services firm with a 40-year history of advocating for the individual investor.' WHERE `actionid` in (43,49);
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/charles_schwab_logo.png' WHERE `actionid` =43;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/charles_schwab_logo.png' WHERE `actionid` =49;

/* Melroy cleaning up emergency account action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'EverBank' , `externallinkdescription` = 'We have a long history of creating innovative products that provide exceptional value in banking, lending and investing.' WHERE `actionid` in (20);
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/everbank.png' WHERE `actionid` =20;

/* Melroy cleaning up will and trust action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'LegalZoom' , `externallinkdescription` = 'LegalZoom is the leading, nationally recognized legal brand for small business and consumers in the United States.' WHERE `actionid` in (13,14);
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/legalzoom.png' WHERE `actionid` =13;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/legalzoom.png' WHERE `actionid` =14;

/* Melroy cleaning up health insurance action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'HealthCare.gov',`externallinkdescription` = 'HealthCare.gov is a health insurance exchange website operated under the United States federal government under the provisions of the Patient Protection and Affordable Care Act.' WHERE `actionid` in (86,87);
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/healthcare.png' WHERE `actionid` =86;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/healthcare.png' WHERE `actionid` =87;

/* Melroy cleaning up life/disa/long insurance action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'Insure.com' , `externallinkdescription` = 'Insure.com provides a comprehensive array of information on life, health, auto, and other types of insurance.' WHERE `actionid` in (2,3,35,36,60,61,88,89);
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/insure.png' WHERE `actionid` =2;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/insure.png' WHERE `actionid` =3;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/insure.png' WHERE `actionid` =35;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/insure.png' WHERE `actionid` =36;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/insure.png' WHERE `actionid` =60;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/insure.png' WHERE `actionid` =61;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/insure.png' WHERE `actionid` =88;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/insure.png' WHERE `actionid` =89;

/* Melroy cleaning up car finance action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'CarFinance.com',`externallinkdescription` = 'CarFinance.com specializes in new, used and refinance auto loans for non-prime customers.' WHERE `actionid` =92;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/carfinance.gif' WHERE `actionid` =92;

/* Melroy cleaning up DOES NOT EXIST/ umbrella insurance action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'GEICO' , `externallinkdescription` = 'GEICO is a wholly owned subsidiary of Berkshire Hathaway that as of 2007 provided coverage for more than 13 million motor vehicles owned by more than 12 million policy holders.' WHERE `actionid` =65;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/geico.png' WHERE `actionid` =65;
UPDATE `actionstepmeta` SET `status` = '1' WHERE `actionid` =65;

/* Melroy cleaning up refinance consumer debt / credit cards action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'Bankrate',`externallinkdescription` = 'Bankrate, Inc. is the Web`s leading aggregator of financial rate information, offering an unparalleled depth and breadth of rate data and financial content.' WHERE `actionid` in (53,85);
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/bankrate.jpg' WHERE `actionid` =53;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/bankrate.jpg' WHERE `actionid` =85;

/* Melroy cleaning up refinance mortgage debt action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'LendingTree' , `externallinkdescription` = 'LendingTree provides a marketplace that connects consumers with multiple lenders, as well as an array of online tools to aid consumers in their financial decisions.' WHERE `actionid` =17;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/lendingtree.png' WHERE `actionid` =17;

/* Melroy cleaning up review credit score action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'TrustedID' , `externallinkdescription` = 'TrustedID delivers identity protection, credit monitoring, privacy and reputation management products to help protect against credit and identity theft.' WHERE `actionid` =23;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/trustedid.png' WHERE `actionid` =23;

/* Melroy cleaning up DOES NOT EXIST YET action step */
UPDATE `actionstepmeta` SET `externallinkname` = 'FlexOffers' , `externallinkdescription` = 'We have a long history of creating innovative products that provide exceptional value in banking, lending and investing.' WHERE `actionid` in (52,66,67);
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/flexoffers.gif' WHERE `actionid` =52;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/flexoffers.gif' WHERE `actionid` =66;
UPDATE `actionstepmeta` SET `external_institution_logo` = 'https://www.flexscore.com/ui/images/flexoffers.gif' WHERE `actionid` =67;
UPDATE `actionstepmeta` SET `status` = '1' WHERE `actionid` =52;
UPDATE `actionstepmeta` SET `status` = '1' WHERE `actionid` =66;
UPDATE `actionstepmeta` SET `status` = '1' WHERE `actionid` =67;

/* Everything before this has been run on production ON June 15 2014 */
# ***** Until this pushed to production on 6.15.2014 ************

# ***** Until this pushed to production on 6.23.2014 ************
/* Melroy June 18 2014 */
INSERT INTO `otlt` (`id`, `name`, `description`, `startdate`, `enddate`) VALUES
(71, 'Lowest To Highest Balance', '', '0000-00-00', '0000-00-00'),
(72, 'Highest To Lowest Balance', '', '0000-00-00', '0000-00-00'),
(73, 'Highest To Lowest APR', '', '0000-00-00', '0000-00-00'),
(74, 'Lowest To Highest APR', '', '0000-00-00', '0000-00-00');
# ***** Until this pushed to production on 6.23.2014 ************

# ***** Until this pushed to production on 07.07.2014 ************
CREATE TABLE `securityquestion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(255) DEFAULT NULL,
  `createdtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;


INSERT INTO `securityquestion` (`question`, createdtimestamp) VALUES
("What is the first name of the boy or girl that you first kissed?", NOW()),
("What is the middle name of your oldest child?", NOW()),
("What school did you attend for sixth grade?", NOW()),
("In what city did you meet your spouse/significant other?", NOW()),
("In what city or town did your mother and father meet?", NOW()),
("What is the first name of your first boss?", NOW()),
("What is your main frequent flyer number?", NOW()),
("Where did you go the first time you flew on an airplane?", NOW()),
("What was the last name of your third grade teacher?", NOW()),
("In what city does your nearest sibling live?", NOW());

/* add column "info" for grouping the action steps*/
ALTER TABLE `actionstepmeta` ADD `info` ENUM( 'info', 'lc', 'act' ) NULL DEFAULT NULL ;

/*update info column of actionstep meta. below should run by order */
UPDATE actionstepmeta SET `info` = 'act' WHERE linktype = 'action';/*1*/
UPDATE actionstepmeta SET `info` = 'info' WHERE linktype = 'other';/*2*/
UPDATE `actionstepmeta` SET `info` = 'lc' WHERE info is NULL;/*3*/

/*update info column change status*/
/*#41,#90 => learning center even though its a type ‘action'*/
UPDATE `actionstepmeta` SET info = 'lc' WHERE actionid IN ( 41, 90 ) ;
/*#38,#46 is an 'info' step*/
UPDATE `actionstepmeta` SET info='info' WHERE actionid IN (38,46);
/*#17, #20, #43, #49, #85, #86, #87, #88, #89, #92 is an 'action' step*/
UPDATE `actionstepmeta` SET info='act' WHERE actionid IN (17,20,43,49,85,86,87,88,89,92);

/* update status column - set inactive
/*#16, #33,#55,#56,#84 isn't implemented or doesnt exist anymore in UI => we should set it to inactive status*/
UPDATE `actionstepmeta` SET status='1' WHERE actionid IN (16,33,55,56,84);
/*#9, #11, #13, #37, #39, #40, #48, #53, #56, #60, #61, #68, #69, #70, #71, #72, #74, #75, #76, #78, #80, #81, #82, #83 is not implemented, so should be set to inactive status*/
UPDATE `actionstepmeta` SET status='1' WHERE actionid IN (9,11,13,37,39,40,48,53,56,60,61,68,69,70,71,72,74,75,76,78,80,81,82,83);
/*#18, #19, #24, #62 is not implemented or doesn’t exist in UI right now, so should be set to inactive*/
UPDATE `actionstepmeta` SET status='1' WHERE actionid IN (18,19,24,62);

/* Fixes June 26, 2014 */
update actionstepmeta set description = 'Credit Card debt is always better when you are charged a lower interest rate. Make that happen by refinancing your card(s) listed below.<br>{{title}}<br>{{lnk}} to begin' where actionid=85;
update actionstepmeta set description = 'You are likely to pay off that mortgage debt sooner with a lower interest rate. Make that happen by refinancing your mortgage(s) listed below.<br>{{title}}<br>{{lnk}} to begin' where actionid=17;

/* Fix actionsteps articles June 27, 2014 */
update actionstepmeta set articles = 'Repairing Poor Credit#https://www.flexscore.com/learningcenter/repairing-poor-credit#255|Correcting Errors on Your Credit Report#https://www.flexscore.com/learningcenter/correcting-errors-on-your-credit-report#862|How Can I Repair My Poor Credit?#https://www.flexscore.com/learningcenter/how-can-i-repair-my-poor-credit#874' where actionid=57;
update actionstepmeta set articles = 'How to Reduce Debts and Pay Off Credit Cards Super Fast!#https://www.flexscore.com/learningcenter/how-to-reduce-debts-and-pay-off-credit-cards-super-fast#1651' where actionid=18;


/* June 27, 2014 New table for action step to learning center articles mapping */
CREATE TABLE `actionsteparticle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actionid` int(11) DEFAULT NULL,
  `articleid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* June 27, 2014 Insert action steps and articles into new actionsteparticle table  */
INSERT INTO `actionsteparticle` (`actionid`, `articleid`) VALUES (23,838),(23,844),(23,849),(23,854),(23,858),(38,165),
(40,729),(40,722),(40,697),(40,694),(73,755),(43,221),(43,1144),(43,1221),(49,221),(49,1144),(49,1221),(11,71),(74,196),
(74,199),(74,202),(37,64),(37,1142),(37,690),(9,28),(9,73),(9,646),(57,255),(57,862),(57,874),(16,258),(16,809),(42,181),
(2,92),(2,193),(2,616),(3,92),(3,193),(3,616),(72,178),(72,699),(17,265),(71,712),(71,650),(4,1687),(19,1358),(18,1651),
(7,111),(7,717),(7,708),(55,1677),(55,1675),(24,1355),(62,1355),(64,903),(64,914),(64,908),(20,664),(5,1647),(60,641),
(60,738),(60,186),(61,641),(61,738),(61,186),(39,175),(39,646),(8,1641),(35,184),(35,186),(35,622),(35,636),(36,184),
(36,186),(36,622),(36,636),(54,760),(54,787),(54,792),(52,763),(52,767),(52,776),(53,796),(70,650),(70,1140),(75,674),
(75,668),(75,239),(82,674),(82,668),(82,671),(41,656),(41,690),(10,204),(6,202),(6,165),(6,111);

-- July 2, 2014 3:00 PM PST [Daphne]
-- Adds Life Insurance owner and beneficiary numeric values
INSERT INTO `otlt` (
	`id`,
	`name`,
	`description`,
	`startdate`,
	`enddate`
)
VALUES
(80,'Self','','0000-00-00','0000-00-00'),
(81,'Spouse/Partner','','0000-00-00','0000-00-00'),
(82,'Child','','0000-00-00','0000-00-00'),
(83,'Revocable Family Trust','','0000-00-00','0000-00-00'),
(84,'Irevocable Family Trust','','0000-00-00','0000-00-00'),
(85,'Business Partner','','0000-00-00','0000-00-00'),
(86,'Non-Profit','','0000-00-00','0000-00-00'),
(87,'Other','','0000-00-00','0000-00-00');
# ***** Until this pushed to production on 07.07.2014 ************


/* July 8th 2014  */
update actionstepmeta set status='0' where actionid in (1,21,16,18,19,55,57,64);
update actionstepmeta set info='lc' where actionid in (18,19,55,57,64);
update actionstepmeta set description = 'It appears you may be spending too large of a chunk of your monthly income on housing costs. You should seriously consider how you might shift more cash flow into savings and retirement accounts.<br>{{title}}' where actionid = 55;
update actionstepmeta set description = "Your credit score is something you should not only be aware of, but always trying to improve. It's one of those things that will be connected to you for the rest of your life.  Better credit scores give people lower rates on loans. In other words, the better your credit score, the cheaper it is to borrow other people's money! Below are some strategies that will help you improve that score:<br>{{title}}" where actionid = 57;
update actionstepmeta set description = 'Health insurance is important and one of the ingredients to both a healthy body and healthy finances.  Read these articles to help you understand why you should consider getting health insurance.<br>{{title}}' where actionid = 64;

update actionstepmeta set buttonstep1 = 'Get Started', buttonstep2 = 'Mark as Done', linkstep1 = 'Set A Goal', linkstep2 = 'Set A Goal' where actionid=16;
update actionstepmeta set description = "It looks like you've got some debt to pay off. Don’t worry…you’re not alone. So many Flexers are in your same shoes, we know exactly what to do. First, you need to set up a Goal of paying off your debt.  Once you've done that, we'll give you specific Action Steps to get you out of debt in the best way possible. To get started, click \"Set A Goal\" below and chose \"Pay Off Debt\" on the Goals screen." where actionid=16;

update actionstepmeta set articles = 'Repairing Poor Credit#https://www.flexscore.com/learningcenter/repairing-poor-credit#255|Correcting Errors on Your Credit Report#https://www.flexscore.com/learningcenter/correcting-errors-on-your-credit-report#862|How Can I Repair My Poor Credit?#https://www.flexscore.com/learningcenter/how-can-i-repair-my-poor-credit#874' where actionid=57;
update actionstepmeta set articles = 'How to Reduce Debts and Pay Off Credit Cards Super Fast!#https://www.flexscore.com/learningcenter/how-to-reduce-debts-and-pay-off-credit-cards-super-fast#1651' where actionid=18;

/* July 9th 2014 Fix for Action Steps #1, 21 */
update actionstepmeta set buttonstep1 = 'Get Started', buttonstep2 = 'Mark as Done', linkstep1 = 'Connect', linkstep2 = 'Connect' where actionid in (1,21);
update actionstepmeta set type = 'instant' where actionid in (1,21);
update actionstepmeta set description = "Do you have online access to any of the accounts below? If there is a website where you can log in to each account, it would be best to use those login credentials (username and password) to automatically connect these accounts to your profile. It would help us help you.<br>{{title}}" where actionid=1;
update actionstepmeta set description = "You want us to make your financial life better, right? Good. Then, let's connect all of your financial accounts to your profile. This means we can automatically keep an eye on your finances and let you know which Action Steps to take to improve your FlexScore. Click \"Connect\" below to get started." where actionid=21;

/* Fix for Action Step 24 */
UPDATE actionstepmeta SET link='xCjZ1V4rhlw', buttonstep1='Watch Video', actionname ='Inflation', status = '0'  WHERE actionid =24;

/* New Action Step 95 - Watch Video Estate Planning*/
INSERT INTO `actionstepmeta` (`actionid`, `actionname`, `category`, `points`, `link`, `externallink`, `external_institution_logo`, `externallinkname`, `externallinkdescription`, `vtitle`, `vkey`, `linktype`, `type`, `description`, `articles`, `wfpointlink`, `buttonstep1`, `buttonstep2`, `linkstep1`, `linkstep2`, `priority`, `status`, `reviewstatus`, `info`) VALUES ('95', 'Video - Estate Planning', 'Estate Planning', '5', '-W-KkOX6KRk', '', NULL, '', '', 'Estate Planning', 'vid12', 'video', 'instant', 'Click Here to watch short video on Estate Planning', '', '', 'Watch Video', '', 'I''m done', '', '', '0', '0', 'lc');
UPDATE `otlt` SET `description` = '-W-KkOX6KRk' WHERE `otlt`.`id` =2012;
/* July 22 */
UPDATE actionstepmeta SET buttonstep1='Get Started', linkstep1 ='Consider Needs', linkstep2 = 'Consider Needs'  WHERE actionid =46;
/* July23 - Update Action Step 59 */
UPDATE `actionstepmeta` SET `link` = 'VxeCu_p8TD4', `status` = '0' , `description` = 'Click Here to watch short video on Budgeting and Cash Flow.' WHERE `actionid` = 59;
UPDATE `otlt` SET `description` = 'VxeCu_p8TD4' WHERE `otlt`.`id` =2008;
update actionstepmeta set status='1' where actionid in (59,95);

/* July 24 - Update articles for video action steps */
UPDATE actionstepmeta SET articles = 'Budgeting and Cash Flow#https://www.flexscore.com/learningcenter/budgeting-cash-flow#2953' WHERE actionid=59;
UPDATE actionstepmeta SET articles = 'Estate Planning#https://www.flexscore.com/learningcenter/estate-planning#2957' WHERE actionid=95;

/* July 25 - Activating videos */
update actionstepmeta set status='0' where actionid in (59,95);
update actionstepmeta set actionname = 'Estate Planning', description = 'Watch a short video on Estate Planning' where actionid=95;
update actionstepmeta set description = 'Watch a short video on Budgeting and Cash Flow' where actionid=59;

/* July 25 - simple description addition */
ALTER TABLE `actionstepmeta` ADD `simpledescription`  text NOT NULL DEFAULT '' COMMENT 'Description without links';
update actionstepmeta set simpledescription = "The following articles from our Learning Center are recommended based on areas of your profile where you might need some help.  The more financially literate you are, the more likely you'll meet your goals (a bazillion studies prove it)!  Every article you read is worth up to 5 points." where actionid = 90;
update actionstepmeta set simpledescription = description where linktype='video';
update actionstepmeta set simpledescription = "Health insurance is important and one of the ingredients to both a healthy body and healthy finances.  Read these articles to help you understand why you should consider getting health insurance." where actionid = 64;
update actionstepmeta set simpledescription = "Your credit score is something you should not only be aware of, but always trying to improve. It's one of those things that will be connected to you for the rest of your life.  Better credit scores give people lower rates on loans. In other words, the better your credit score, the cheaper it is to borrow other people's money! Below are some strategies that will help you improve that score." where actionid = 57;
update actionstepmeta set simpledescription = "It appears you may be spending too large of a chunk of your monthly income on housing costs. You should seriously consider how you might shift more cash flow into savings and retirement accounts." where actionid = 55;
update actionstepmeta set simpledescription = "You want high returns with low risk...we all do!  We’ll do our best to help with that. That's why it's so important for you to complete this." where actionid = 7;
update actionstepmeta set simpledescription = "You want us to make your financial life better, right? Good. Then, let's connect all of your financial accounts to your profile. This means we can automatically keep an eye on your finances and let you know which Action Steps to take to improve your FlexScore. Click \"Connect\" below to get started." where actionid = 21;
update actionstepmeta set simpledescription = "It appears you may be spending too large of a chunk of your monthly income on consumer debt payments. You should seriously evaluate how to pay down your debts so that you can shift more cash flow into savings and retirement accounts." where actionid=54;

/* July 28 - Update Action Step - 54 */
UPDATE `actionstepmeta` SET `link` = 'learnmore', `description` = 'It appears you may be spending too large of a chunk of your monthly income on consumer debt payments. You should seriously evaluate how to pay down your debts so that you can shift more cash flow into savings and retirement accounts.<br>{{title}}', `buttonstep1` = 'Get Started', `linkstep1` = 'Learn more', `linkstep2` = 'Learn more', `info` = 'lc' WHERE `actionid` =54;

/* July 29 - Update Action Step - 53 */
UPDATE actionstepmeta SET description = "Refinancing one or more of your loans can likely help you to pay off debt sooner. <br>{{title}}<br>{{lnk}} to find out how", articles = '', status='0', link='adddebt', linkstep1='Update Debts', info='act', points=13, priority=1, reviewstatus='0', linktype='link', category='Debt Optimization' where actionid=53;
UPDATE actionstepmeta SET articles = "Reducing the Cost of Debt#https://www.flexscore.com/learningcenter/reducing-the-cost-of-debt#258|Debt Management#https://www.flexscore.com/learningcenter/debt-management#252" where actionid=53;
update actionstepmeta set externallink='http://www.bankrate.com/funnel/personal-loans/' where actionid=53;
update actionsteparticle set articleid = 258 where actionid = 53;
INSERT INTO actionsteparticle (actionid, articleid) VALUES (53, 252);

/* July 29 - Update Action Step - 85 */
UPDATE actionstepmeta SET articles = "Comparing Credit Card Finance Charges, Fees, and Benefits#https://www.flexscore.com/learningcenter/comparing-credit-card-finance-charges-fees-and-benefits#262|How can I lower the interest rate on my credit card?#https://www.flexscore.com/learningcenter/how-can-i-lower-the-interest-rate-on-my-credit-card#776" where actionid=85;
INSERT INTO actionsteparticle (actionid, articleid) VALUES (85, 262);
INSERT INTO actionsteparticle (actionid, articleid) VALUES (85, 776);

/* July 29 - Update Action Step - 92 */
UPDATE actionstepmeta SET articles = "Reducing the Cost of Debt#https://www.flexscore.com/learningcenter/reducing-the-cost-of-debt#258" where actionid=92;
INSERT INTO actionsteparticle (actionid, articleid) VALUES (92, 258);

/*July30 - Update Action Step - 74,75*/
UPDATE `actionstepmeta` SET `description` = 'The biggest financial risk a retiree faces is the potential of running out of money before running out of life. It is important to withdraw a sustainable amount to live on, and not any more.<br>{{title}}', `link` = 'learnmore', `buttonstep2` = 'Mark as Done', `linkstep1` = 'Learn more', `linkstep2` = 'Learn more', `info` = 'lc' WHERE `actionid` = 74;
INSERT INTO actionsteparticle (actionid, articleid) VALUES (74, 196);
INSERT INTO actionsteparticle (actionid, articleid) VALUES (74, 199);
INSERT INTO actionsteparticle (actionid, articleid) VALUES (74, 202);

UPDATE `actionstepmeta` SET `link` = 'learnmore', `description` = 'It appears that over the last 90 days you have been withdrawing more money from your portfolio that what is sustainable.  In other words, you don''t want to "eat into your nest egg" because you won’t be able to easily grow it back since you’re retired.<br>{{title}}', `buttonstep2` = 'Mark as Done', `linkstep1` = 'Learn more', `linkstep2` = 'Learn more', `info` = 'lc' WHERE `actionid` = 75;
INSERT INTO actionsteparticle (actionid, articleid) VALUES (75, 674);
INSERT INTO actionsteparticle (actionid, articleid) VALUES (75, 668);
INSERT INTO actionsteparticle (actionid, articleid) VALUES (75, 239);

/*July30 - Temporarily set action step inactive - 74,75*/
update actionstepmeta set status='1' where actionid in (74,75);

update actionstepmeta set info='lc',simpledescription = 'Not having all of your eggs in one basket is important. One of the easiest ways to do that is to include “alternative asset” classes to your portfolio. Read these article(s) to help you understand why.' where actionid=41;
update actionstepmeta set actionname='Consider Life Expectancy Risk', reviewstatus='0', linktype='action', type='instant', simpledescription='The biggest financial risk a retiree faces is the potential of running out of money before running out of life. It is important to withdraw a sustainable amount to live on, and not any more.' where actionid=74;
update actionstepmeta set actionname='Examine Your Lifestyle Costs to Make Certain You Aren\'t Overspending', linktype='action', type='instant', description='It appears that you have been withdrawing more money from your portfolio than what is sustainable. In other words, you don\'t want to "eat into your nest egg" because you won’t be able to easily grow it back since you’re retired.<br>{{title}}', simpledescription="It appears that you have been withdrawing more money from your portfolio than what is sustainable. In other words, you don\\'t want to "eat into your nest egg" because you won’t be able to easily grow it back since you’re retired." where actionid=75;
update actionstepmeta set status='0' where actionid in (74,75);

/* July 31 - Update to Consolidate Loans Step */
update actionstepmeta set category = 'Debt Optimization', points =13, link='adddebt', externallink='http://www.bankrate.com/funnel/personal-loans/', external_institution_logo = 'https://www.flexscore.com/ui/images/bankrate.jpg', externallinkname ='Bankrate', externallinkdescription='Bankrate, Inc. is the Web`s leading aggregator of financial rate information, offering an unparalleled depth and breadth of rate data and financial content.', linktype='link', priority = 1, status='0', reviewstatus='0', info='act', linkstep1 = 'Update Debts' where actionid=52;

/* July 31 - Update to Flexibility of Assets Step */
update actionstepmeta set link='learnmore', linktype='action', type='instant', description = 'Consider that more than 25% of your nest egg assets are "inflexible". This means that if you needed quick access to it’s full value for any number of reasons, you’d have a hard time. Learn more about having a flexible nest egg below.<br>{{title}}', simpledescription ='Consider that more than 25% of your nest egg assets are "inflexible." This means that if you needed quick access to it’s full value for any number of reasons, you’d have a hard time. Learn more about having a flexible nest egg below.', buttonstep2 = 'Mark as Done', linkstep1 = 'Learn more', linkstep2 = 'Learn more', status = '0', reviewstatus = '1', info = 'lc' where actionid=70;

/* July 31 - Update to Concentration of Assets Step */
update actionstepmeta set description = 'Consider that more than 10% of your nest egg assets are invested in one thing. This means that if some financial craziness occurred, and that one “thing” loses a lot of value, your ability to meet future goals may be impaired. These investments each make up more than 10% of your portfolio.<br>{{title}}',status='0' where actionid=71;

/* August 1 - Fixes */
update actionstepmeta set buttonstep2 ='Mark as Done', buttonstep1 ='Get Started', linkstep1 = 'Update Debts', linkstep2 = 'Update Debts' where link='adddebt' and status='0';
/* August 1 - Fixes for adddebt */
update actionstepmeta set simpledescription='You are likely to pay off that mortgage debt sooner with a lower interest rate. Make that happen by refinancing your mortgage(s).' where actionid=17;
update actionstepmeta set simpledescription='Your debt is split up into many different accounts with variable interest rates. We\'ve detected that you may be able to conveniently consolidate many of your accounts into one or more loans with lower, set interest rates and a fixed payment schedule.' where actionid=52;
update actionstepmeta set simpledescription='Refinancing one or more of your loans can likely help you to pay off debt sooner.' where actionid=53;
update actionstepmeta set simpledescription='Credit Card debt is always better when you are charged a lower interest rate. Make that happen by refinancing your card(s).' where actionid=85;
update actionstepmeta set simpledescription='Having a loan on depreciating assets like vehicles works best when the loan interest rate is as low as possible. You might be able to get a lower rate and re-finance one or more of your loans. This might help you get out of debt sooner.' where actionid=92;
/* August 1 - Fixes for addasset */
update actionstepmeta set simpledescription='Beneficiaries are those people (or charities) who will inherit your account upon your death. You should make sure you have chosen beneficiaries for your account. Or at least review to make sure they are up to date.' where actionid=10;
update actionstepmeta set simpledescription = 'You may be eligible to contribute ${{amt}} per month to retirement accounts.<br><br>Read below articles to find out why utilizing an IRA Account makes sense for you.' where actionid=43;
update actionstepmeta set simpledescription ='Since you already have enough tax deductions, you should consider opening a Roth Individual Retirement Account (IRA) and begin making a monthly non-tax-deductible contribution of ${{amt}} towards your future savings goals.' where actionid=49;
update actionstepmeta set simpledescription = 'You\'ve indicated you have a pension plan that would pay you a retirement income guaranteed by an employer. Have you recently looked at your beneficiaries listed on your pension? This would be a good idea.' where actionid=73;

/* TD Ameritrade */
update actionstepmeta set externallinkname = 'TD Ameritrade', externallink = 'https://invest.tdameritrade.com/grid/p/accountApplication', externallinkdescription='TD Ameritrade provides investing and trading services for nearly six million client accounts that total more than $400 billion in assets, and custodial services for more than 4,000 independent registered investment advisors.', external_institution_logo = 'https://www.flexscore.com/ui/images/tdameritrade.png' where actionid in (43,49);

/* August 4th Patch made on production  */
update actionstepmeta set description = 'Since you already have enough tax deductions, you should consider opening a Roth Individual Retirement Account (IRA) and begin making a monthly non-tax-deductible contribution of ${{amt}} towards your future savings goals. <br>{{title}}<br>{{lnk}} to get a Roth IRA' where actionid=49;

/* August 8 2014 DT Fix text in action step 16 Consider Setting Up a Goal of Paying Off Consumer Debt*/
update actionstepmeta set description = "It looks like you\'ve got some debt to pay off. Don\'t worry, you\'re not alone. So many Flexers are in your same shoes, we know exactly what to do. First, you need to set up a Goal of paying off your debt.  Once you\'ve done that, we\'ll give you specific Action Steps to get you out of debt in the best way possible. To get started, click" 'Set A Goal" below and choose "Pay Off Debt" on the Goals screen.' where actionid=16;

ALTER TABLE `actionstepmeta` ADD `image` VARCHAR( 255 ) NULL DEFAULT NULL;

/* Action ID 1 - 10 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Goal-Planning_Mortgage-Refinancing.jpg' where actionid=1;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Five-Questions-about-Long-Term-Care.jpg' where actionid=2;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Evaluating-Disability-Income-Insurance-Policies.jpg' where actionid=3;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Use-of-Cash-Flow-Analysis-in-Creating-Budget.jpg' where actionid=4;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_Dollar-Cost-Averaging.jpg' where actionid=5;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_Rebalancing-a-Portfolio-vs-Redesigning.jpg' where actionid=6;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Goal-Planning_Setting-Financial-+-Investment-Goals.jpg' where actionid=7;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Requesting-Copy-of-Credit-Report.jpg' where actionid=8;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Debt-Optimization_Paying-Off-Outstanding-Credit-Card-Balances.jpg' where actionid=9;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_Evaluating-an-Early-Retirement-Offer.jpg' where actionid=10;

/* Action ID 11 - 20 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Financial-Planning_Reducing-Discretionary-Spending.jpg' where actionid=11;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Financial-Planning_Reducing-Discretionary-Spending.jpg' where actionid=12;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Goal-Planning_Debt-Service-Ratio-versus-Debt-Safety-Ratio.jpg' where actionid=13;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Group-Health-Insurance.jpg' where actionid=14;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Goal-Planning_Debt-Consolidation.jpg' where actionid=15;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Shopping-for-Insurance.jpg' where actionid=16;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Conducting-Review-of-Your-Estate-Plan.jpg' where actionid=17;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Why-Buy-Insurance.jpg' where actionid=18;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Checking-Account.jpg' where actionid=19;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Ten-Ways-to-Lower-the-Cost-of-Disability-Income-Insurance.jpg' where actionid=20;

/* Action ID 21 - 30 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Debt-Optimization_Reducing-the-Cost-of-Debt.jpg' where actionid=21;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Goal-Planning_How-can-I-lower-interest-rate-on-credit-card.jpg' where actionid=22;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Who-Is-Covered-under-Your-Homeowners-Policy.jpg' where actionid=23;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Disability-Income-Insurance.jpg' where actionid=24;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Savings-Accounts.jpg' where actionid=25;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Tax-Planning_Should-I-invest-in-Roth-IRA-or-traditional-IRA.jpg' where actionid=26;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Is-estate-planning-for-rich.jpg' where actionid=27;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Combination-Funds.jpg' where actionid=28;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Opening-a-529-Account.jpg' where actionid=29;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Types-of-Bond-Mutual-Funds.jpg' where actionid=30;

/* Action ID 31 - 40 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Tax-Planning_Tax-Planning-for-Income.jpg' where actionid=31;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Tax-Planning_Federal-Income-Tax-Withholding-Requirements.jpg' where actionid=32;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Estate-Planning-An-Introduction.jpg' where actionid=33;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Wills-The-Cornerstone-of-Your-Estate-Plan.jpg' where actionid=34;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Beyond-Traditional-Asset-Classes.jpg' where actionid=35;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Designing-an-Investment-Portfolio.jpg' where actionid=36;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/10/Protection-Planning_Business-Liability-Insurance.jpg' where actionid=37;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Financial-Planning_How-to-Cut-Costs-Spending-Too-Much.jpg' where actionid=38;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Debt-Optimization_Debt-Management.jpg' where actionid=39;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Goal-Planning_Factors-to-Consider-Cash-Reserve-Goal.jpg' where actionid=40;

/* Action ID 41 - 50 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_Understanding-Risk.jpg' where actionid=41;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Introduction-to-Estate-Planning-Topic-Discussion.jpg' where actionid=42;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Individual-Health-Insurance.jpg' where actionid=43;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Use-of-Cash-Flow-Analysis-in-Creating-Budget.jpg' where actionid=44;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Making-the-Most-of-Group-Health-Benefits.jpg' where actionid=45;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Determining-Need-for-Disability-Income-Insurance.jpg' where actionid=46;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Debt-Optimization_Options-When-You-Can%E2%80%99t-Meet-Your-Financial-Obligations.jpg' where actionid=47;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Debt-Optimization_Reducing-the-Cost-of-Debt.jpg' where actionid=48;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Why-I-Don%E2%80%99t-Want-to-Buy-Life-Insurance.jpg' where actionid=49;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Conducting-a-Home-Inventory.jpg' where actionid=50;

/* Action ID 51 - 60 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Establishing-a-Budget.jpg' where actionid=51;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Savings-Accounts.jpg' where actionid=52;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Tax-Planning_Tax-Planning-for-Income.jpg' where actionid=53;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/09/Retirement-Planning_IRA-and-Retirement-Plan-Distributions.jpg' where actionid=54;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Retirement-Planning_Understanding-Social-Security.jpg' where actionid=55;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Types-of-Bond-Mutual-Funds.jpg' where actionid=56;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Cash.jpg' where actionid=57;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Tax-Planning_Other-Tax-Advantaged-Strategies.jpg' where actionid=58;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Fundamental-Needs-for-Life-Insurance.jpg' where actionid=59;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Property-+-Casualty-+-Liability-Insurance.jpg' where actionid=60;

/* Action ID 61 - 70 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Long-Term-Care-Insurance.jpg' where actionid=61;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Goal-Planning_Setting-Financial-+-Investment-Goals.jpg' where actionid=62;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Tax-Planning_Traditional-IRAs-and-Roth-IRAs.jpg' where actionid=63;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Direct-Withdrawal-Payment-Arrangement.jpg' where actionid=64;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/09/Retirement-Planning_IRA-and-Retirement-Plan-Distributions.jpg' where actionid=65;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_Sustainable-Withdrawal-Rates.jpg' where actionid=66;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_What-is-401k-plan.jpg' where actionid=67;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_Understanding-Risk.jpg' where actionid=68;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Beneficiary-Designations.jpg' where actionid=69;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Alternative-Asset-Classes-Introduction.jpg' where actionid=70;

/* Action ID 71 - 80 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Direct-Withdrawal-Payment-Arrangement.jpg' where actionid=71;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Use-of-Cash-Flow-Analysis-in-Creating-Budget.jpg' where actionid=72;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Goal-Planning_Mortgage-Refinancing.jpg' where actionid=73;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Concentrated-Stock-Positions.jpg' where actionid=74;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Retirement-Planning_Understanding-Defined-Benefit-Plans.jpg' where actionid=75;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Exchange-Traded-Funds.jpg' where actionid=76;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_AssetAllocation.jpg' where actionid=77;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Measuring-Risk.jpg' where actionid=78;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Financial-Planning_Reducing-Discretionary-Spending.jpg' where actionid=79;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Financial-Planning_How-to-Cut-Costs-Spending-Too-Much.jpg' where actionid=80;

/* Action ID Remaining */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Tax-Planning_Charitable-Gifting.jpg' where actionid=81;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_How-can-I-gauge-risk-tolerance.jpg' where actionid=82;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Umbrella-Liability-Insurance.jpg' where actionid=83;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_Sustainable-Withdrawal-Rates.jpg' where actionid=85;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_Retirement-Planning-The-Basics.jpg' where actionid=86;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Personal-Liability-Insurance-Policy-Types.jpg' where actionid=87;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Do-You-Need-Disability-Income-Insurance.jpg' where actionid=88;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Fundamental-Needs-for-Life-Insurance.jpg' where actionid=89;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/10/Protection-Planning_Professional-Liability-Coverage.jpg' where actionid=90;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Requesting-Copy-of-Credit-Report.jpg' where actionid=91;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Property-and-Casualty-Insurance.jpg' where actionid=93;


/* August 19th Action ID 1 - 10 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Savings-Accounts.jpg' where actionid=1;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Why-I-Don%E2%80%99t-Want-to-Buy-Life-Insurance.jpg' where actionid=2;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Why-Women-Need-Life-Insurance.jpg' where actionid=3;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Personal-Liability-Insurance-Policy-Types.jpg' where actionid=4;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Disability-Income-Insurance.jpg' where actionid=5;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_Rebalancing-a-Portfolio-vs-Redesigning.jpg' where actionid=6;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_How-to-Measure-Your-Risk-Tolerance.jpg' where actionid=7;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_Understanding-Investment-Terms-and-Concepts.jpg' where actionid=8;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/09/Retirement-Planning_What-is-an-IRA.jpg' where actionid=9;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Beneficiary-Designations.jpg' where actionid=10;

/* August 19th Action ID 11 - 20 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_What-is-401k-plan.jpg' where actionid=11;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/09/Retirement-Planning_IRA-and-Retirement-Plan-Distributions.jpg' where actionid=12;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Wills-The-Cornerstone-of-Your-Estate-Plan.jpg' where actionid=13;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Estate-Planning-An-Introduction.jpg' where actionid=14;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Goal-Planning_Setting-Financial-+-Investment-Goals.jpg' where actionid=15;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Goal-Planning_Debt-Consolidation.jpg' where actionid=16;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Goal-Planning_Mortgage-Refinancing.jpg' where actionid=17;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Debt-Optimization_Reducing-the-Cost-of-Debt.jpg' where actionid=18;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Debt-Optimization_Debt-Management.jpg' where actionid=19;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Getting-Started-Establishing-Financial-Safety-Net.jpg' where actionid=20;

/* August 19th Action ID 21 - 30 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Direct-Withdrawal-Payment-Arrangement.jpg' where actionid=21;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Financial-Planning_Reducing-Discretionary-Spending.jpg' where actionid=22;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Requesting-Copy-of-Credit-Report.jpg' where actionid=23;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Financial-Planning_How-to-Cut-Costs-Spending-Too-Much.jpg' where actionid=24;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Alternative-Asset-Classes-Introduction.jpg' where actionid=25;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Goal-Planning_Debt-Service-Ratio-versus-Debt-Safety-Ratio.jpg' where actionid=26;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Tax-Planning_Tax-Planning-for-Income.jpg' where actionid=27;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Introduction-to-Estate-Planning-Topic-Discussion.jpg' where actionid=28;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Shopping-for-Insurance.jpg' where actionid=29;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Understanding-Probate.jpg' where actionid=30;

/* August 19th Action ID 31 - 40 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Cash.jpg' where actionid=31;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Use-of-Cash-Flow-Analysis-in-Creating-Budget.jpg' where actionid=32;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Measuring-Risk.jpg' where actionid=33;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Goal-Planning_The-Spending-Plan-Prioritizing-Budget-Goals.jpg' where actionid=34;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Evaluating-Disability-Income-Insurance-Policies.jpg' where actionid=35;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Do-You-Need-Disability-Income-Insurance.jpg' where actionid=36;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Designing-an-Investment-Portfolio.jpg' where actionid=37;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_How-can-I-gauge-risk-tolerance.jpg' where actionid=38;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_Dollar-Cost-Averaging.jpg' where actionid=39;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Improving-Portfolio-Performance-Asset-Allocation.jpg' where actionid=40;

/* August 19th Action ID 41 - 50 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Combination-Funds.jpg' where actionid=41;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Types-of-Stock-Mutual-Funds.jpg' where actionid=42;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_Sustainable-Withdrawal-Rates.jpg' where actionid=43;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Retirement-Planning_Understanding-Social-Security.jpg' where actionid=44;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Lump-Sum-vs-Dollar-Cost-Averaging.jpg' where actionid=45;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Is-estate-planning-for-rich.jpg' where actionid=46;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Tax-Planning_Projecting-Federal-Income-Taxt.jpg' where actionid=47;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Tax-Planning_Choosing-an-Income-Tax-Filing-Status.jpg' where actionid=48;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Tax-Planning_Should-I-invest-in-Roth-IRA-or-traditional-IRA.jpg' where actionid=49;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Financial-Planning_Reducing-Discretionary-Spending.jpg' where actionid=50;

/* August 19th Action ID 51 - 60 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_Retirement-Planning-The-Basics.jpg' where actionid=51;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Goal-Planning_How-can-I-lower-interest-rate-on-credit-card.jpg' where actionid=52;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Debt-Optimization_Comparing-Credit-Card-Finance-Charges.jpg' where actionid=53;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Establishing-a-Credit-History.jpg' where actionid=54;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/06/Financial-Planning_Homeownership.jpg' where actionid=55;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_Six-Keys-to-More-Successful-Investing.jpg' where actionid=56;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Correcting-Errors-on-Your-Credit-Report.jpg' where actionid=57;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Checking-Account.jpg' where actionid=58;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Budgeting.jpg' where actionid=59;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Long-Term-Care-Insurance.jpg' where actionid=60;

/* August 19th Action ID 61 - 70 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Five-Questions-about-Long-Term-Care.jpg' where actionid=61;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Making-the-Most-of-Group-Health-Benefits.jpg' where actionid=62;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Step_Watch-Video-Property-Casualty-Insurance.jpg' where actionid=63;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Individual-Health-Insurance.jpg' where actionid=64;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Umbrella-Liability-Insurance.jpg' where actionid=65;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Who-Is-Covered-under-Your-Homeowners-Policy.jpg' where actionid=66;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Protection-Planning_Why-Buy-Insurance.jpg' where actionid=67;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/10/Protection-Planning_Business-Liability-Insurance.jpg' where actionid=68;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/10/Protection-Planning_Professional-Liability-Coverage.jpg' where actionid=69;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_Investment-Planning-throughout-Retirement.jpg' where actionid=70;

/* August 19th Action ID 71 - 80 */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_Estimating-Retirement-Income-Needs.jpg' where actionid=71;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Investment-Planning_Exchange-Traded-Funds.jpg' where actionid=72;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Retirement-Planning_Understanding-Defined-Benefit-Plans.jpg' where actionid=73;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Retirement-Planning_Borrowing-or-Withdrawing-Money-from-Your-401k-Plan.jpg' where actionid=74;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Retirement-Planning_Asset-Allocation-Projecting-a-Glide-Path.jpg' where actionid=75;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Step_Retired-Decrease-current-retirement-account-withdrawals.jpg' where actionid=76;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Step_Create-Informational-Sheet.jpg' where actionid=77;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Tax-Planning_Am-I-Having-Enough-Withheld.jpg' where actionid=78;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Tax-Planning_Charitable-Gifting.jpg' where actionid=79;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Investment-Planning_Understanding-Risk.jpg' where actionid=80;

/* August 19th Action ID Remaining */
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Step_Automatic-Bill-Pay.jpg' where actionid=81;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Establishing-a-Budget.jpg' where actionid=82;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Financial-Planning_Opening-a-529-Account.jpg' where actionid=83;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Debt-Optimization_Borrowing-Options-Mortgages.jpg' where actionid=84;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Debt-Optimization_How-can-I-pay-off-my-credit-card-debt.jpg' where actionid=85;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Protection-Planning_Group-Health-Insurance.jpg' where actionid=86;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Step_Review-Health-Insurance.jpg' where actionid=87;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/10/Protection-Planning_Understanding-Your-Business-Owners-Policy.jpg' where actionid=88;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Step_Review-Disability-Insurance.jpg' where actionid=89;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Step_Learning-Center-Articles.jpg' where actionid=90;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Step_Consider-Decreasing-Tax-Withholding.jpg' where actionid=91;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Step_Car-Finance.jpg' where actionid=92;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/04/Debt-Optimization_I%E2%80%99ve-finally-paid-off-credit-cards.jpg' where actionid=93;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2014/08/Action-Steps_Retirement-Goal-Plan.jpg' where actionid=94;
update actionstepmeta set image = 'https://www.flexscore.com/cms/wp-content/uploads/2013/03/Estate-Planning_Conducting-Review-of-Your-Estate-Plan.jpg' where actionid=95;


/* 08-Sep-2014 - Added Ids for Limited and Comprehensive Health Insurance Types */
INSERT INTO `otlt` (`id`, `name`, `description`, `startdate`, `enddate`) VALUES ('88', 'Comprehensive', '', NULL, NULL), ('89', 'Limited', '', NULL, NULL);


/* 2014-09-22  Change Learning Center article titles */
UPDATE actionstepmeta SET articles = "How Much Can You Afford When You Buy a House?#https://www.flexscore.com/learningcenter/how-much-can-you-afford#1677|Home Ownership#https://www.flexscore.com/learningcenter/homeownership#1675" WHERE actionid = 55;
UPDATE actionstepmeta SET articles = "Understanding Your Credit Report#https://www.flexscore.com/learningcenter/understanding-your-credit-report#838|Credit Reports#https://www.flexscore.com/learningcenter/credit-reports#844|The Effects of Credit Cards on Your Credit Report#https://www.flexscore.com/learningcenter/the-effects-of-credit-cards-on-your-credit-report#849|How to Interpret the Information on Your Credit Report#https://www.flexscore.com/learningcenter/interpreting-the-information-on-your-credit-report#854|Requesting a Copy of Your Credit Report#https://www.flexscore.com/learningcenter/requesting-a-copy-of-your-credit-report#858" WHERE actionid = 23;
UPDATE actionstepmeta SET articles = "Beyond Traditional Asset Classes: Exploring Alternative Assets#https://www.flexscore.com/learningcenter/beyond-traditional-asset-classes-exploring-alternatives#656|Alternative Asset Classes: An Introduction#https://www.flexscore.com/learningcenter/alternative-asset-classes-an-introduction#690" WHERE actionid = 41;
UPDATE actionstepmeta SET articles = "How to Measure Your Risk Tolerance#https://www.flexscore.com/learningcenter/how-to-measure-your-risk-tolerance#111|Measuring Investment Risk#https://www.flexscore.com/learningcenter/measuring-risk#717|How can I gauge my risk tolerance?#https://www.flexscore.com/learningcenter/how-can-i-gauge-my-risk-tolerance#708" WHERE actionid = 7;
UPDATE actionstepmeta SET articles = "Asset Allocation#https://www.flexscore.com/learningcenter/asset-allocation#202|Understanding Investment Risk#https://www.flexscore.com/learningcenter/understanding-risk-topic-discussion#165|How to Measure Your Risk Tolerance#https://www.flexscore.com/learningcenter/how-to-measure-your-risk-tolerance#111" WHERE actionid = 6;
UPDATE actionstepmeta SET articles = "Understanding Investment Risk#https://www.flexscore.com/learningcenter/understanding-risk-topic-discussion#165" WHERE actionid = 38;
UPDATE actionstepmeta SET articles = "Sustainable Withdrawal Rates#https://www.flexscore.com/learningcenter/sustainable-withdrawal-rates#196|Investment Planning During Retirement#https://www.flexscore.com/learningcenter/investment-planning-throughout-retirement#199|Asset Allocation: Projecting a Glide Path#https://www.flexscore.com/learningcenter/asset-allocation-projecting-a-glide-path#202" WHERE actionid = 74;
UPDATE actionstepmeta SET articles = "Tax Planning for Income#https://www.flexscore.com/learningcenter/tax-planning-for-income#221|Traditional IRAs and Roth IRAs#https://www.flexscore.com/learningcenter/traditional-iras-and-roth-iras#1144|Am I Having Enough Withheld on my W-4 Form?#https://www.flexscore.com/learningcenter/am-i-having-enough-withheld#1221" WHERE actionid = 43;
UPDATE actionstepmeta SET articles = "Tax Planning for Income#https://www.flexscore.com/learningcenter/tax-planning-for-income#221|Traditional IRAs and Roth IRAs#https://www.flexscore.com/learningcenter/traditional-iras-and-roth-iras#1144|Am I Having Enough Withheld on my W-4 Form?#https://www.flexscore.com/learningcenter/am-i-having-enough-withheld#1221" WHERE actionid = 49;

/* Improve testing of Learning Center action steps */
INSERT INTO `constantslastupdated` (`constant`, `lastupdated`) VALUES ('Media', '2014-10-12');


/*************************** Production Push October 26, 2014  START ********************/

/* 14/10/2014 - ACTION STEP 48 TAX PLANNING VIDEO ROW UPDATE */
UPDATE `actionstepmeta` SET `points` = '5', `actionname` = 'Tax Planning', `status` = '0', `link` = 'kAux08cwwLY', `vtitle` = 'Tax Planning', `vkey` = 'vid13', `linktype` = 'video', `type` = 'instant', `simpledescription` = 'Watch a short video on Tax Planning', `description` = 'Watch a short video on Tax Planning', `articles` = 'Tax Planning#https://www.flexscore.com/learningcenter/tax-planning#3202', `buttonstep1` = 'Watch Video', `linkstep1` = 'I''m done', `info` = 'lc'  WHERE `actionid` = 48;


/* INSERT TAX PLANNING VIDEO DETAILS INTO OTLT TABLE*/
INSERT INTO `otlt` (`id`, `name`, `description`, `startdate`, `enddate`) VALUES ('2013', 'vid13', 'kAux08cwwLY', NULL, NULL);
ALTER TABLE actionstepmeta DROP COLUMN vkey;
delete from otlt where id in (2001,2002,2003,2004,2005,2006,2007,2008,2009,2010,2011,2012,2013);

/*************************** Production Push October 26, 2014  END ********************/



/***************************  Production Push November 09, 2014  START ********************/

/* 28/10/2014 - ACTION STEP ID 2 - DESCRIPTION UPDATE */
UPDATE `actionstepmeta` SET `description` = 'Looks like you need to increase your Life Insurance coverage for yourself/your spouse by ${{amt}}. You can contact your current provider to see how they can help. Or, if you want to get a quote from someone we recommend, {{lnk}}.' WHERE `actionid` =2;

/* 28/10/2014 - ACTION STEP ID 63 IMPLEMENTATION */

UPDATE `actionstepmeta` SET `link` = 'C-0PyD01Xs8', `articles` = 'Property and Casualty Insurance#https://www.flexscore.com/learningcenter/property-casualty-insurance#3207', `status` = '0' WHERE `actionid` =63;

/* Nov 3rd */
insert into constantslastupdated (constant,lastupdated) values('Priority','2014-11-03');

/* Nov 5th */
UPDATE `actionstepmeta` SET `link` = 'xCjZ1V4rhlw', `articles` = 'High Investment Returns vs Savings#https://www.flexscore.com/learningcenter/high-investment-returns-vs-savings#3217', `status` = '0', actionname='High Returns vs. Saving', category = 'Financial Planning', vtitle='High Returns vs. Saving', description = 'There are many paths’s to get from here to there. If you desire to become financially independent, you may want to focus more on savings and not just rely on investment return to grow your nest egg.', simpledescription = 'There are many paths’s to get from here to there. If you desire to become financially independent, you may want to focus more on savings and not just rely on investment return to grow your nest egg.', reviewstatus = '1' WHERE `actionid` =62;

UPDATE `actionstepmeta` SET `link` = 'HKyjIv5oJNs' WHERE `actionid` =62;

/*************************** Production Push November 09, 2014  END ********************/


/*************************** Production Push December 15, 2014 START ********************/

ALTER TABLE actionsteparticle ADD UNIQUE `user_article`(`actionid`, `articleid`);

/*************************** Production Push December 15, 2014 END ********************/

/*************************** Production Patch December 17, 2014 START ********************/

/* 12/16/14 Update descriptions for actionsteps involving rates */
UPDATE  `actionstepmeta` SET  `description` =  'You’re likely to pay off that mortgage debt sooner with a lower interest rate. Your interest rate will vary depending on the length of the mortgage. We recommend the following rates:<br><br>30 Year Fixed: 4.75% or below<br>15 Year Fixed: 3.75% or below<br>5/1 Adjustable Rate: 3.75% or below<br><br>Make that happen by refinancing your mortgage(s) listed below. <br>{{title}}<br>{{lnk}} to begin',
    simpledescription = 'You’re likely to pay off that mortgage debt sooner with a lower interest rate. Your interest rate will vary depending on the length of the mortgage. We recommend the following rates: 30 Year Fixed to 4.75% or below, 15 Year Fixed to 3.75% or below, 5/1 Adjustable Rate to 3.75% or below. Make that happen by refinancing your mortgage(s) listed below.' WHERE `actionid` = 17;
UPDATE  `actionstepmeta` SET  `description` = 'Refinancing one or more of your loans can help you pay off debt sooner. If possible, we recommend refinancing the following loan(s) so that your APR is below 10%. <br>{{title}}<br>{{lnk}} to find out how',
    simpledescription = 'Refinancing one or more of your loans can help you pay off debt sooner. If possible, we recommend refinancing the following loan(s) so that your APR is below 10%.' WHERE `actionid` = 53;
UPDATE  `actionstepmeta` SET  `description` = 'Credit card debt is easier to manage when you pay a lower interest rate. Make that happen by refinancing your card(s). The card(s) listed below have an APR higher than 10%, which we consider high. <br>{{title}}<br>{{lnk}} to begin',
    simpledescription = 'Credit card debt is easier to manage when you pay a lower interest rate. Make that happen by refinancing your card(s). The card(s) listed below have an APR higher than 10%, which we consider high.' WHERE `actionid` = 85;
UPDATE  `actionstepmeta` SET  `description` = 'A loan on a depreciating asset like a vehicle works best when the interest rate is as low as possible. We recommend a rate lower than 5%, if possible. You might be able to get a lower rate and re-finance the following loans. <br>{{title}}<br>{{lnk}} to begin',
    simpledescription = 'A loan on a depreciating asset like a vehicle works best when the interest rate is as low as possible. We recommend a rate lower than 5%, if possible. You might be able to get a lower rate and re-finance the following loans.' WHERE `actionid` = 92;

/*************************** Production Patch December 17, 2014 END ********************/

/*************************** Production Patch January 22, 2015 START ********************/

/* Add MC constant */
insert into constantslastupdated (constant,lastupdated) values ('MonteCarlo', '2015-01-22');

/*************************** Production Patch January 22, 2015 END ********************/


/*************************** Production Push May 31, 2015 START ********************/

/*  May 8, 2015 change lifeexpectancy table to use a generic "baseyearage" that is not specific to a year.*/
ALTER TABLE `lifeexpectancy` DROP INDEX `2007age_2`;
ALTER TABLE `lifeexpectancy` DROP INDEX `2007age`;
ALTER TABLE `lifeexpectancy` CHANGE `2009age` `baseyearage` TINYINT(4);
ALTER TABLE `lifeexpectancy` ADD INDEX `baseyearage` (`baseyearage`);

/*  May 8, 2015 update lifeexpectancy table with 2010 base year values. */
update `lifeexpectancy` set `MYearsToLive` = 76, `MLifeExpectancy` = 76, `FYearsToLive` = 81, `FLifeExpectancy` = 81  where `baseyearage` = 0;
update `lifeexpectancy` set `MYearsToLive` = 76, `MLifeExpectancy` = 77, `FYearsToLive` = 80, `FLifeExpectancy` = 81  where `baseyearage` = 1;
update `lifeexpectancy` set `MYearsToLive` = 75, `MLifeExpectancy` = 77, `FYearsToLive` = 79, `FLifeExpectancy` = 81  where `baseyearage` = 2;
update `lifeexpectancy` set `MYearsToLive` = 74, `MLifeExpectancy` = 77, `FYearsToLive` = 78, `FLifeExpectancy` = 81  where `baseyearage` = 3;
update `lifeexpectancy` set `MYearsToLive` = 73, `MLifeExpectancy` = 77, `FYearsToLive` = 77, `FLifeExpectancy` = 81  where `baseyearage` = 4;
update `lifeexpectancy` set `MYearsToLive` = 72, `MLifeExpectancy` = 77, `FYearsToLive` = 76, `FLifeExpectancy` = 81  where `baseyearage` = 5;
update `lifeexpectancy` set `MYearsToLive` = 71, `MLifeExpectancy` = 77, `FYearsToLive` = 75, `FLifeExpectancy` = 81  where `baseyearage` = 6;
update `lifeexpectancy` set `MYearsToLive` = 70, `MLifeExpectancy` = 77, `FYearsToLive` = 74, `FLifeExpectancy` = 81  where `baseyearage` = 7;
update `lifeexpectancy` set `MYearsToLive` = 69, `MLifeExpectancy` = 77, `FYearsToLive` = 73, `FLifeExpectancy` = 81  where `baseyearage` = 8;
update `lifeexpectancy` set `MYearsToLive` = 68, `MLifeExpectancy` = 77, `FYearsToLive` = 73, `FLifeExpectancy` = 82  where `baseyearage` = 9;
update `lifeexpectancy` set `MYearsToLive` = 67, `MLifeExpectancy` = 77, `FYearsToLive` = 72, `FLifeExpectancy` = 82  where `baseyearage` = 10;
update `lifeexpectancy` set `MYearsToLive` = 66, `MLifeExpectancy` = 77, `FYearsToLive` = 71, `FLifeExpectancy` = 82  where `baseyearage` = 11;
update `lifeexpectancy` set `MYearsToLive` = 65, `MLifeExpectancy` = 77, `FYearsToLive` = 70, `FLifeExpectancy` = 82  where `baseyearage` = 12;
update `lifeexpectancy` set `MYearsToLive` = 64, `MLifeExpectancy` = 77, `FYearsToLive` = 69, `FLifeExpectancy` = 82  where `baseyearage` = 13;
update `lifeexpectancy` set `MYearsToLive` = 63, `MLifeExpectancy` = 77, `FYearsToLive` = 68, `FLifeExpectancy` = 82  where `baseyearage` = 14;
update `lifeexpectancy` set `MYearsToLive` = 62, `MLifeExpectancy` = 77, `FYearsToLive` = 67, `FLifeExpectancy` = 82  where `baseyearage` = 15;
update `lifeexpectancy` set `MYearsToLive` = 61, `MLifeExpectancy` = 77, `FYearsToLive` = 66, `FLifeExpectancy` = 82  where `baseyearage` = 16;
update `lifeexpectancy` set `MYearsToLive` = 60, `MLifeExpectancy` = 77, `FYearsToLive` = 65, `FLifeExpectancy` = 82  where `baseyearage` = 17;
update `lifeexpectancy` set `MYearsToLive` = 59, `MLifeExpectancy` = 77, `FYearsToLive` = 64, `FLifeExpectancy` = 82  where `baseyearage` = 18;
update `lifeexpectancy` set `MYearsToLive` = 58, `MLifeExpectancy` = 77, `FYearsToLive` = 63, `FLifeExpectancy` = 82  where `baseyearage` = 19;
update `lifeexpectancy` set `MYearsToLive` = 57, `MLifeExpectancy` = 77, `FYearsToLive` = 62, `FLifeExpectancy` = 82  where `baseyearage` = 20;
update `lifeexpectancy` set `MYearsToLive` = 56, `MLifeExpectancy` = 77, `FYearsToLive` = 61, `FLifeExpectancy` = 82  where `baseyearage` = 21;
update `lifeexpectancy` set `MYearsToLive` = 55, `MLifeExpectancy` = 77, `FYearsToLive` = 60, `FLifeExpectancy` = 82  where `baseyearage` = 22;
update `lifeexpectancy` set `MYearsToLive` = 54, `MLifeExpectancy` = 77, `FYearsToLive` = 59, `FLifeExpectancy` = 82  where `baseyearage` = 23;
update `lifeexpectancy` set `MYearsToLive` = 53, `MLifeExpectancy` = 77, `FYearsToLive` = 58, `FLifeExpectancy` = 82  where `baseyearage` = 24;
update `lifeexpectancy` set `MYearsToLive` = 52, `MLifeExpectancy` = 77, `FYearsToLive` = 57, `FLifeExpectancy` = 82  where `baseyearage` = 25;
update `lifeexpectancy` set `MYearsToLive` = 51, `MLifeExpectancy` = 77, `FYearsToLive` = 56, `FLifeExpectancy` = 82  where `baseyearage` = 26;
update `lifeexpectancy` set `MYearsToLive` = 50, `MLifeExpectancy` = 77, `FYearsToLive` = 55, `FLifeExpectancy` = 82  where `baseyearage` = 27;
update `lifeexpectancy` set `MYearsToLive` = 50, `MLifeExpectancy` = 78, `FYearsToLive` = 54, `FLifeExpectancy` = 82  where `baseyearage` = 28;
update `lifeexpectancy` set `MYearsToLive` = 49, `MLifeExpectancy` = 78, `FYearsToLive` = 53, `FLifeExpectancy` = 82  where `baseyearage` = 29;
update `lifeexpectancy` set `MYearsToLive` = 48, `MLifeExpectancy` = 78, `FYearsToLive` = 52, `FLifeExpectancy` = 82  where `baseyearage` = 30;
update `lifeexpectancy` set `MYearsToLive` = 47, `MLifeExpectancy` = 78, `FYearsToLive` = 51, `FLifeExpectancy` = 82  where `baseyearage` = 31;
update `lifeexpectancy` set `MYearsToLive` = 46, `MLifeExpectancy` = 78, `FYearsToLive` = 50, `FLifeExpectancy` = 82  where `baseyearage` = 32;
update `lifeexpectancy` set `MYearsToLive` = 45, `MLifeExpectancy` = 78, `FYearsToLive` = 49, `FLifeExpectancy` = 82  where `baseyearage` = 33;
update `lifeexpectancy` set `MYearsToLive` = 44, `MLifeExpectancy` = 78, `FYearsToLive` = 48, `FLifeExpectancy` = 82  where `baseyearage` = 34;
update `lifeexpectancy` set `MYearsToLive` = 43, `MLifeExpectancy` = 78, `FYearsToLive` = 47, `FLifeExpectancy` = 82  where `baseyearage` = 35;
update `lifeexpectancy` set `MYearsToLive` = 42, `MLifeExpectancy` = 78, `FYearsToLive` = 46, `FLifeExpectancy` = 82  where `baseyearage` = 36;
update `lifeexpectancy` set `MYearsToLive` = 41, `MLifeExpectancy` = 78, `FYearsToLive` = 45, `FLifeExpectancy` = 82  where `baseyearage` = 37;
update `lifeexpectancy` set `MYearsToLive` = 40, `MLifeExpectancy` = 78, `FYearsToLive` = 44, `FLifeExpectancy` = 82  where `baseyearage` = 38;
update `lifeexpectancy` set `MYearsToLive` = 39, `MLifeExpectancy` = 78, `FYearsToLive` = 43, `FLifeExpectancy` = 82  where `baseyearage` = 39;
update `lifeexpectancy` set `MYearsToLive` = 38, `MLifeExpectancy` = 78, `FYearsToLive` = 42, `FLifeExpectancy` = 82  where `baseyearage` = 40;
update `lifeexpectancy` set `MYearsToLive` = 37, `MLifeExpectancy` = 78, `FYearsToLive` = 41, `FLifeExpectancy` = 82  where `baseyearage` = 41;
update `lifeexpectancy` set `MYearsToLive` = 37, `MLifeExpectancy` = 79, `FYearsToLive` = 40, `FLifeExpectancy` = 82  where `baseyearage` = 42;
update `lifeexpectancy` set `MYearsToLive` = 36, `MLifeExpectancy` = 79, `FYearsToLive` = 40, `FLifeExpectancy` = 83  where `baseyearage` = 43;
update `lifeexpectancy` set `MYearsToLive` = 35, `MLifeExpectancy` = 79, `FYearsToLive` = 39, `FLifeExpectancy` = 83  where `baseyearage` = 44;
update `lifeexpectancy` set `MYearsToLive` = 34, `MLifeExpectancy` = 79, `FYearsToLive` = 38, `FLifeExpectancy` = 83  where `baseyearage` = 45;
update `lifeexpectancy` set `MYearsToLive` = 33, `MLifeExpectancy` = 79, `FYearsToLive` = 37, `FLifeExpectancy` = 83  where `baseyearage` = 46;
update `lifeexpectancy` set `MYearsToLive` = 32, `MLifeExpectancy` = 79, `FYearsToLive` = 36, `FLifeExpectancy` = 83  where `baseyearage` = 47;
update `lifeexpectancy` set `MYearsToLive` = 31, `MLifeExpectancy` = 79, `FYearsToLive` = 35, `FLifeExpectancy` = 83  where `baseyearage` = 48;
update `lifeexpectancy` set `MYearsToLive` = 30, `MLifeExpectancy` = 79, `FYearsToLive` = 34, `FLifeExpectancy` = 83  where `baseyearage` = 49;
update `lifeexpectancy` set `MYearsToLive` = 29, `MLifeExpectancy` = 79, `FYearsToLive` = 33, `FLifeExpectancy` = 83  where `baseyearage` = 50;
update `lifeexpectancy` set `MYearsToLive` = 29, `MLifeExpectancy` = 80, `FYearsToLive` = 32, `FLifeExpectancy` = 83  where `baseyearage` = 51;
update `lifeexpectancy` set `MYearsToLive` = 28, `MLifeExpectancy` = 80, `FYearsToLive` = 31, `FLifeExpectancy` = 83  where `baseyearage` = 52;
update `lifeexpectancy` set `MYearsToLive` = 27, `MLifeExpectancy` = 80, `FYearsToLive` = 30, `FLifeExpectancy` = 83  where `baseyearage` = 53;
update `lifeexpectancy` set `MYearsToLive` = 26, `MLifeExpectancy` = 80, `FYearsToLive` = 30, `FLifeExpectancy` = 84  where `baseyearage` = 54;
update `lifeexpectancy` set `MYearsToLive` = 25, `MLifeExpectancy` = 80, `FYearsToLive` = 29, `FLifeExpectancy` = 84  where `baseyearage` = 55;
update `lifeexpectancy` set `MYearsToLive` = 24, `MLifeExpectancy` = 80, `FYearsToLive` = 28, `FLifeExpectancy` = 84  where `baseyearage` = 56;
update `lifeexpectancy` set `MYearsToLive` = 24, `MLifeExpectancy` = 81, `FYearsToLive` = 27, `FLifeExpectancy` = 84  where `baseyearage` = 57;
update `lifeexpectancy` set `MYearsToLive` = 23, `MLifeExpectancy` = 81, `FYearsToLive` = 26, `FLifeExpectancy` = 84  where `baseyearage` = 58;
update `lifeexpectancy` set `MYearsToLive` = 22, `MLifeExpectancy` = 81, `FYearsToLive` = 25, `FLifeExpectancy` = 84  where `baseyearage` = 59;
update `lifeexpectancy` set `MYearsToLive` = 21, `MLifeExpectancy` = 81, `FYearsToLive` = 24, `FLifeExpectancy` = 84  where `baseyearage` = 60;
update `lifeexpectancy` set `MYearsToLive` = 21, `MLifeExpectancy` = 82, `FYearsToLive` = 23, `FLifeExpectancy` = 84  where `baseyearage` = 61;
update `lifeexpectancy` set `MYearsToLive` = 20, `MLifeExpectancy` = 82, `FYearsToLive` = 23, `FLifeExpectancy` = 85  where `baseyearage` = 62;
update `lifeexpectancy` set `MYearsToLive` = 19, `MLifeExpectancy` = 82, `FYearsToLive` = 22, `FLifeExpectancy` = 85  where `baseyearage` = 63;
update `lifeexpectancy` set `MYearsToLive` = 18, `MLifeExpectancy` = 82, `FYearsToLive` = 21, `FLifeExpectancy` = 85  where `baseyearage` = 64;
update `lifeexpectancy` set `MYearsToLive` = 18, `MLifeExpectancy` = 83, `FYearsToLive` = 20, `FLifeExpectancy` = 85  where `baseyearage` = 65;
update `lifeexpectancy` set `MYearsToLive` = 17, `MLifeExpectancy` = 83, `FYearsToLive` = 19, `FLifeExpectancy` = 85 where `baseyearage` = 66;
update `lifeexpectancy` set `MYearsToLive` = 16, `MLifeExpectancy` = 83, `FYearsToLive` = 19, `FLifeExpectancy` = 86 where `baseyearage` = 67;
update `lifeexpectancy` set `MYearsToLive` = 15, `MLifeExpectancy` = 83, `FYearsToLive` = 18, `FLifeExpectancy` = 86 where `baseyearage` = 68;
update `lifeexpectancy` set `MYearsToLive` = 15, `MLifeExpectancy` = 84, `FYearsToLive` = 17, `FLifeExpectancy` = 86 where `baseyearage` = 69;
update `lifeexpectancy` set `MYearsToLive` = 14, `MLifeExpectancy` = 84, `FYearsToLive` = 16, `FLifeExpectancy` = 86 where `baseyearage` = 70;
update `lifeexpectancy` set `MYearsToLive` = 13, `MLifeExpectancy` = 84, `FYearsToLive` = 16, `FLifeExpectancy` = 87 where `baseyearage` = 71;
update `lifeexpectancy` set `MYearsToLive` = 13, `MLifeExpectancy` = 85, `FYearsToLive` = 15, `FLifeExpectancy` = 87 where `baseyearage` = 72;
update `lifeexpectancy` set `MYearsToLive` = 12, `MLifeExpectancy` = 85, `FYearsToLive` = 14, `FLifeExpectancy` = 87 where `baseyearage` = 73;
update `lifeexpectancy` set `MYearsToLive` = 11, `MLifeExpectancy` = 85, `FYearsToLive` = 13, `FLifeExpectancy` = 87 where `baseyearage` = 74;
update `lifeexpectancy` set `MYearsToLive` = 11, `MLifeExpectancy` = 86, `FYearsToLive` = 13, `FLifeExpectancy` = 88 where `baseyearage` = 75;
update `lifeexpectancy` set `MYearsToLive` = 10, `MLifeExpectancy` = 86, `FYearsToLive` = 12, `FLifeExpectancy` = 88 where `baseyearage` = 76;
update `lifeexpectancy` set `MYearsToLive` = 10, `MLifeExpectancy` = 87, `FYearsToLive` = 11, `FLifeExpectancy` = 88 where `baseyearage` = 77;
update `lifeexpectancy` set `MYearsToLive` = 9, `MLifeExpectancy` = 87, `FYearsToLive` = 11, `FLifeExpectancy` = 89 where `baseyearage` = 78;
update `lifeexpectancy` set `MYearsToLive` = 9, `MLifeExpectancy` = 88, `FYearsToLive` = 10, `FLifeExpectancy` = 89 where `baseyearage` = 79;
update `lifeexpectancy` set `MYearsToLive` = 8, `MLifeExpectancy` = 88, `FYearsToLive` = 10, `FLifeExpectancy` = 90 where `baseyearage` = 80;
update `lifeexpectancy` set `MYearsToLive` = 8, `MLifeExpectancy` = 89, `FYearsToLive` = 9, `FLifeExpectancy` = 90 where `baseyearage` = 81;
update `lifeexpectancy` set `MYearsToLive` = 7, `MLifeExpectancy` = 89, `FYearsToLive` = 8, `FLifeExpectancy` = 90 where `baseyearage` = 82;
update `lifeexpectancy` set `MYearsToLive` = 7, `MLifeExpectancy` = 90, `FYearsToLive` = 8, `FLifeExpectancy` = 91 where `baseyearage` = 83;
update `lifeexpectancy` set `MYearsToLive` = 6, `MLifeExpectancy` = 90, `FYearsToLive` = 7, `FLifeExpectancy` = 91 where `baseyearage` = 84;
update `lifeexpectancy` set `MYearsToLive` = 6, `MLifeExpectancy` = 91, `FYearsToLive` = 7, `FLifeExpectancy` = 92 where `baseyearage` = 85;
update `lifeexpectancy` set `MYearsToLive` = 5, `MLifeExpectancy` = 91, `FYearsToLive` = 6, `FLifeExpectancy` = 92 where `baseyearage` = 86;
update `lifeexpectancy` set `MYearsToLive` = 5, `MLifeExpectancy` = 92, `FYearsToLive` = 6, `FLifeExpectancy` = 93 where `baseyearage` = 87;
update `lifeexpectancy` set `MYearsToLive` = 5, `MLifeExpectancy` = 93, `FYearsToLive` = 6, `FLifeExpectancy` = 94 where `baseyearage` = 88;
update `lifeexpectancy` set `MYearsToLive` = 4, `MLifeExpectancy` = 93, `FYearsToLive` = 5, `FLifeExpectancy` = 94 where `baseyearage` = 89;
update `lifeexpectancy` set `MYearsToLive` = 4, `MLifeExpectancy` = 94, `FYearsToLive` = 5, `FLifeExpectancy` = 95 where `baseyearage` = 90;
update `lifeexpectancy` set `MYearsToLive` = 4, `MLifeExpectancy` = 95, `FYearsToLive` = 4, `FLifeExpectancy` = 95 where `baseyearage` = 91;
update `lifeexpectancy` set `MYearsToLive` = 3, `MLifeExpectancy` = 95, `FYearsToLive` = 4, `FLifeExpectancy` = 96 where `baseyearage` = 92;
update `lifeexpectancy` set `MYearsToLive` = 3, `MLifeExpectancy` = 96, `FYearsToLive` = 4, `FLifeExpectancy` = 97 where `baseyearage` = 93;
update `lifeexpectancy` set `MYearsToLive` = 3, `MLifeExpectancy` = 97, `FYearsToLive` = 4, `FLifeExpectancy` = 98 where `baseyearage` = 94;
update `lifeexpectancy` set `MYearsToLive` = 3, `MLifeExpectancy` = 98, `FYearsToLive` = 3, `FLifeExpectancy` = 98 where `baseyearage` = 95;
update `lifeexpectancy` set `MYearsToLive` = 3, `MLifeExpectancy` = 99, `FYearsToLive` = 3, `FLifeExpectancy` = 99 where `baseyearage` = 96;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 99, `FYearsToLive` = 3, `FLifeExpectancy` = 100 where `baseyearage` = 97;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 100, `FYearsToLive` = 3, `FLifeExpectancy` = 101 where `baseyearage` = 98;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 101, `FYearsToLive` = 3, `FLifeExpectancy` = 102 where `baseyearage` = 99;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 102, `FYearsToLive` = 2, `FLifeExpectancy` = 102 where `baseyearage` = 100;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 103, `FYearsToLive` = 2, `FLifeExpectancy` = 103 where `baseyearage` = 101;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 104, `FYearsToLive` = 2, `FLifeExpectancy` = 104 where `baseyearage` = 102;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 105, `FYearsToLive` = 2, `FLifeExpectancy` = 105 where `baseyearage` = 103;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 106, `FYearsToLive` = 2, `FLifeExpectancy` = 106 where `baseyearage` = 104;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 107, `FYearsToLive` = 2, `FLifeExpectancy` = 107 where `baseyearage` = 105;
update `lifeexpectancy` set `MYearsToLive` = 2, `MLifeExpectancy` = 108, `FYearsToLive` = 2, `FLifeExpectancy` = 108 where `baseyearage` = 106;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 108, `FYearsToLive` = 2, `FLifeExpectancy` = 109 where `baseyearage` = 107;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 109, `FYearsToLive` = 1, `FLifeExpectancy` = 109 where `baseyearage` = 108;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 110, `FYearsToLive` = 1, `FLifeExpectancy` = 110 where `baseyearage` = 109;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 111, `FYearsToLive` = 1, `FLifeExpectancy` = 111 where `baseyearage` = 110;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 112, `FYearsToLive` = 1, `FLifeExpectancy` = 112 where `baseyearage` = 111;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 113, `FYearsToLive` = 1, `FLifeExpectancy` = 113 where `baseyearage` = 112;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 114, `FYearsToLive` = 1, `FLifeExpectancy` = 114 where `baseyearage` = 113;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 115, `FYearsToLive` = 1, `FLifeExpectancy` = 115 where `baseyearage` = 114;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 116, `FYearsToLive` = 1, `FLifeExpectancy` = 116 where `baseyearage` = 115;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 117, `FYearsToLive` = 1, `FLifeExpectancy` = 117 where `baseyearage` = 116;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 118, `FYearsToLive` = 1, `FLifeExpectancy` = 118 where `baseyearage` = 117;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 119, `FYearsToLive` = 1, `FLifeExpectancy` = 119 where `baseyearage` = 118;
update `lifeexpectancy` set `MYearsToLive` = 1, `MLifeExpectancy` = 120, `FYearsToLive` = 1, `FLifeExpectancy` = 120 where `baseyearage` = 119;

/* May 28 2015: Update Priorities for users on log in  */
UPDATE `constantslastupdated` SET lastupdated = '2015-06-01' WHERE constant = "Priority";

/*************************** Production Push May 31, 2015 END ********************/