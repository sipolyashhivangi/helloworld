****02/10/2014*****
1. Run sixm_scorechange.php for removing extra data except last 180 days
2. Run change_data_correction.php for updating the change value according to old incorrect – and + data
3. Run master-patch to create newscorechange table
4. Run scorechange_table_update.php to movescorechange data from userscore to scorechange
5. Run mast-patch to removescorechange column from userscore table.


makesure values.ini having correct configuration
