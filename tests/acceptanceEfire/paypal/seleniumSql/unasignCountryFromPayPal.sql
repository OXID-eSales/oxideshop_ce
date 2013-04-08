# unassign Germany from Paypal payment method
DELETE FROM `oxobject2payment` WHERE `oxobject2payment`.`OXID` = '6604d679fpaypal11518196d95f45fd3';
# assigne US to Paypal payment method
INSERT INTO `oxobject2payment` (`OXID`, `OXPAYMENTID`, `OXOBJECTID`, `OXTYPE`) VALUES ('f0d4f35c10ababfc57a22aeda37f30a3', 'oxidpaypal', '8f241f11096877ac0.98748826', 'oxcountry');

#assigne US to Standard shppingmethode 
INSERT INTO `oxobject2delivery` (`OXID`,        		 `OXDELIVERYID`, 	`OXOBJECTID`,                 `OXTYPE`) VALUES ('157ff126b33b2b82b575d73acd3b4b43', 'oxidstandard',     '8f241f11096877ac0.98748826', 'oxdelset');					
								
#assigne US to Shipping Cost rule								
INSERT INTO `oxobject2delivery` (`OXID`,    	`OXDELIVERYID`,   `OXOBJECTID`,                 `OXTYPE`)   VALUES
                                (' 	157c621cfe87bfa8b847479faa50fa38', '1b842e7352422a708.01472527',      '8f241f11096877ac0.98748826', 'oxcountry');
#assigne Paypal paypment methode to Standard delivery methode
INSERT INTO `oxobject2payment` (`OXID`, `OXPAYMENTID`, `OXOBJECTID`, `OXTYPE`) VALUES ('660f0f63epaypald63a78af0a4b44', 'oxidpaypal', 'oxidstandard',                 'oxdelset');
