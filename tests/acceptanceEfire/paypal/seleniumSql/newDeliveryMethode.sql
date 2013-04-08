
#sukure 3 naujus shiping metodus Test paypal
INSERT INTO `oxdeliveryset` (`OXID`,      `OXSHOPID`, `OXSHOPINCL`, `OXSHOPEXCL`, `OXACTIVE`, `OXTITLE`,      		   `OXTITLE_1`,  		 `OXPOS`) VALUES
                            ('testdelset1', 1,          1,            0,            1,         'Test Paypal:6hour', 	   'Test Paypal:6 hour', 	  0),
				('testdelset2', 1,          1,            0,            1,         'Test Paypal:12hour', 	   'Test Paypal:12 hour', 	  0),
				('testdelset3', 1,          1,            0,            1,         'Test Paypal:specproduct', 'Test Paypal:specproduct', 0);
							
							
							
#Sitam shiping metodui priskyre paypala:
INSERT INTO `oxobject2payment` (`OXID`,            `OXPAYMENTID`,    `OXOBJECTID`,      `OXTYPE`) VALUES
				   ('paymentmethode1', 'oxidpaypal',    'testdelset1',     'oxdelset'),
				   ('paymentmethode2', 'oxidpaypal',    'testdelset2',     'oxdelset'),
				   ('paymentmethode3', 'oxidpaypal',    'testdelset3',     'oxdelset');
						
								

#Sitiem 3 shiping metodam priskiriam sali austria:
INSERT INTO `oxobject2delivery` (`OXID`,      `OXDELIVERYID`,    `OXOBJECTID`,                `OXTYPE`) VALUES
                                ('country1', 'testdelset1',      'a7c40f6320aeb2ec2.72885259', 'oxdelset'),
				    ('country2', 'testdelset2',      'a7c40f6320aeb2ec2.72885259', 'oxdelset'),
				    ('country3', 'testdelset3',      'a7c40f6320aeb2ec2.72885259', 'oxdelset');
   
#sukuriam  shipping cost rulsus    
INSERT INTO `oxdelivery` (`OXID`,  `OXSHOPID`, `OXSHOPINCL`, `OXSHOPEXCL`, `OXACTIVE`, `OXTITLE`,       				 `OXTITLE_1`,    				`OXADDSUMTYPE`, `OXADDSUM`, `OXDELTYPE`, `OXPARAM`, `OXPARAMEND`, `OXFIXED`, `OXSORT`, `OXFINALIZE`) VALUES						
		           ('rules1', 1,          1,            0,            1,         'test paypal from 0  till 10:6hours',    'test paypal from 0  till 10:6hours',  'abs',           0.5,         'p',          0,        10,        0,         9999,     0),
			    ('rules2', 1,          1,            0,            1,         'test paypal from 10 till 20:6hours',    'test paypal from 10 till 20:6hours',  'abs',    	  0.4,         'p',         10,        20,        0,         9999,     0),
			    ('rules3', 1,          1,            0,            1,         'test paypal from 20 till 99:6hours',    'test paypal from 20 till 99:6hours',  'abs',      	  0.3,         'p',         20,        99,        0,         9999,     0),					
			    ('rules4', 1,          1,            0,            1,         'test paypal from 0  till 10:12hours',   'test paypal from 0  till 10:12hours', 'abs',           0.9,         'p',         0,         10,        0,         9999,     0),
			    ('rules5', 1,          1,            0,            1,         'test paypal from 10 till 20:12hours',   'test paypal from 10 till 20:12hours', 'abs',           0.8,         'p',         10,        20,        0,         9999,     0),
			    ('rules6', 1,          1,            0,            1,         'test paypal from 20 till 99:12hours',   'test paypal from 20 till 99:12hours', 'abs',           0.7,         'p',         20,        99,        0,         9999,     0),
			    ('rules7', 1,          1,            0,            1,         'test paypal with product',     	  'test paypal with product', 	       'abs',          0.15,        'p',         20,        30,        0,         9999,     0);
						  
						 
						 
						 
						
#3 shipping cost rulsams priskiriam Austria sali:
INSERT INTO `oxobject2delivery`     (`OXID`,    	`OXDELIVERYID`,   `OXOBJECTID`,                 `OXTYPE`)   VALUES
                                   ('rulescountry1', 'rules1',      'a7c40f6320aeb2ec2.72885259', 'oxcountry'),
					('rulescountry2', 'rules2',      'a7c40f6320aeb2ec2.72885259', 'oxcountry'),
					('rulescountry3', 'rules3',      'a7c40f6320aeb2ec2.72885259', 'oxcountry'),
					('rulescountry4', 'rules4',      'a7c40f6320aeb2ec2.72885259', 'oxcountry'),
					('rulescountry5', 'rules5',      'a7c40f6320aeb2ec2.72885259', 'oxcountry'),
					('rulescountry6', 'rules6',      'a7c40f6320aeb2ec2.72885259', 'oxcountry'),
					('rulescountry7', 'rules7',      'a7c40f6320aeb2ec2.72885259', 'oxcountry');
								
								
								
								
								
#3 shiping kostui prisikiriam produkta:
INSERT INTO `oxobject2delivery` (`OXID`,          `OXDELIVERYID`, 	`OXOBJECTID`,   `OXTYPE`) VALUES
                                ('rulesproduct1', 'rules7',      '1000', 		'oxarticles');
								
								

#priskiriam shiping metodui "Test paypal" situs 7 rulsus:

INSERT INTO `oxdel2delset` (`OXID`,     		`OXDELID`, `OXDELSETID`) VALUES
                           ('shipingtopayment1', 'rules1', 'testdelset1'),
			      ('shipingtopayment2', 'rules2', 'testdelset1'),
			      ('shipingtopayment3', 'rules3', 'testdelset1'),		
                           ('shipingtopayment4', 'rules4', 'testdelset2'),
			      ('shipingtopayment5', 'rules5', 'testdelset2'),
			      ('shipingtopayment6', 'rules6', 'testdelset2'),	
                           ('shipingtopayment7', 'rules7', 'testdelset3');
													   