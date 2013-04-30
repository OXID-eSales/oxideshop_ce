#change payment methode price 
UPDATE `oxpayments` SET `OXADDSUM` = '10.5' WHERE `OXID` = 'oxidpaypal';
#change shipping  methode price 
UPDATE `oxdelivery` SET `OXADDSUM` = '13' WHERE `OXID` = 'testdel';