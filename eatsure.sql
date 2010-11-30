-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.0.77


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;



--
-- Definition of table `inspection`
--

DROP TABLE IF EXISTS `inspection`;
CREATE TABLE `inspection` (
  `inspection_id` int(10) unsigned NOT NULL auto_increment,
  `restaurant_id` int(10) unsigned NOT NULL,
  `severity` varchar(80) default '0',
  `inspection_type` varchar(255) NOT NULL,
  `category` varchar(255) default NULL,
  `description` varchar(255) NOT NULL,
  `inspected` datetime default NULL,
  `active` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`inspection_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5632 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inspection`
--

/*!40000 ALTER TABLE `inspection` DISABLE KEYS */;
INSERT INTO `inspection` (`inspection_id`,`restaurant_id`,`severity`,`inspection_type`,`category`,`description`,`inspected`,`active`) VALUES 
 (5576,2239,'Non-Critical','No','Food contact surfaces properly designed, constructed, installed, maintained, located (smooth, non-absorbent, cleanable, corrosion resistant and non-toxic)','Fail to maintain hardwood cutting boards, blocks, tables or bowls in a clean and sanitary manner','2010-10-13 00:00:00',1),
 (5577,2239,'Note','No','Correction(s) Are / Were Required ',' ','2010-10-13 00:00:00',1),
 (5578,2239,'Non-Critical','No','Floors clean and in good repair','Fail to ensure floor of food-handling room kept clean and in good repair','2010-10-12 00:00:00',0),
 (5579,2239,'Non-Critical','CDI','Equipment, non-food contact surfaces and linen are maintained, designed, constructed, installed and accessible for cleaning','Fail to maintain all surfaces clean and in good repair','2010-10-12 00:00:00',0),
 (5580,2239,'Non-Critical','No','Food contact surfaces properly designed, constructed, installed, maintained, located (smooth, non-absorbent, cleanable, corrosion resistant and non-toxic)','Fail to maintain hardwood cutting boards, blocks, tables or bowls in a clean and sanitary manner','2010-10-12 00:00:00',0),
 (5581,2239,'Note','No','Correction(s) Are / Were Required ',' ','2010-10-12 00:00:00',0),
 (5582,2239,'Non-Critical','No','Mechanical ventilation operable where required','Fail to ensure ventilation system is maintained in a cleanly manner and free from grease accumulation','2010-10-07 00:00:00',0),
 (5583,2239,'Non-Critical','No','Mechanical dishwashing: Wash / rinse water clean, water temperature, timing cycles, sanitizer','Fail to ensure that wash water is maintained between 60&deg;C and 71&deg;C','2010-10-07 00:00:00',0),
 (5584,2239,'Non-Critical','CDI','Food contact surfaces washed / rinsed / sanitized after each use and following any operations when contamination may have occurred','Fail to ensure that wiping cloths are handled properly (sanitizing solution used)','2010-10-07 00:00:00',0),
 (5585,2239,'Non-Critical','No','Floors clean and in good repair','Fail to ensure floor of food-handling room kept clean and in good repair','2010-10-07 00:00:00',0),
 (5586,2239,'Non-Critical','No','Food contact surfaces properly designed, constructed, installed, maintained, located (smooth, non-absorbent, cleanable, corrosion resistant and non-toxic)','Fail to maintain hardwood cutting boards, blocks, tables or bowls in a clean and sanitary manner','2010-10-07 00:00:00',0),
 (5587,2239,'Critical','CDI','Food is held at 4&deg;C (40&deg;F) or less','Fail to hold hazardous food items at 4&deg; C (40&deg;F) or less','2010-10-07 00:00:00',0),
 (5588,2239,'Non-Critical','No','Frequency of garbage removal adequate to maintain the premises in a sanitary condition','Fail to remove garbage / wastes when necessary to maintain sanitary condition','2010-10-07 00:00:00',0),
 (5589,2239,'Non-Critical','No','Equipment, non-food contact surfaces and linen are maintained, designed, constructed, installed and accessible for cleaning','Fail to maintain all surfaces as readily cleanable and non-absorbentFail to maintain all surfaces clean and in good repair','2010-10-07 00:00:00',0),
 (5590,2239,'Non-Critical','CDI','Separate hand washing basin provided for food handlers','Fail to use basin only for hand washing of employees','2010-10-07 00:00:00',0),
 (5591,2239,'Non-Critical','No','Proper storage of clean utensils (including single service utensils)','Fail to store utensils in manner preventing contamination','2010-10-07 00:00:00',0),
 (5592,2239,'Critical','No','Separate raw foods from ready-to-eat foods during storage and handling','Fail to store raw foods below cooked / ready to eat foods','2010-10-07 00:00:00',0),
 (5593,2239,'Note','No','Correction(s) Are / Were Required ',' ','2010-10-07 00:00:00',0),
 (5594,2239,'Note','No','Satisfactory - No Action Required ',' ','2010-07-27 00:00:00',0),
 (5595,2239,'Note','No','Satisfactory - No Action Required, Food Handler Education on Site ',' ','2010-06-09 00:00:00',0),
 (5596,2239,'Note','No','Satisfactory - No Action Required, Food Handler Education on Site ',' ','2010-05-05 00:00:00',0),
 (5597,2239,'Critical','No','At the time of this inspection, the premises is maintained free from any obvious condition that may be a health hazard, adversely affect the sanitary operation of the premises or adversely affect the wholesomeness of the food','Fail to operate food premise in a safe manner preventing a health hazard','2010-05-04 00:00:00',0),
 (5598,2239,'Non-Critical','No','Adequate protection against the entrance of insects, vermin, rodents, dust and fumes','Fail to provide tightly sealed entrances and / or screened entrances to prevent insect / pest entryFail to maintain premises free from pest infestation','2010-05-04 00:00:00',0),
 (5599,2239,'Critical','No','Food is held at 4&deg;C (40&deg;F) or less','Fail to hold hazardous food items at 4&deg; C (40&deg;F) or less','2010-05-04 00:00:00',0),
 (5600,2239,'Closed','No','Order 13 Served','Correction(s) Required, Food Handler Education on Site, Section 13 Order Served ','2010-05-04 00:00:00',0),
 (5601,2239,'Critical','CDI','Food protected from potential contamination (e.g. food covered, labeled, off floor, stored on racks, sneeze guard, physical hazards, during display, during transport)','Fail to store food 15 cm off the floor to prevent contamination','2010-02-25 00:00:00',0),
 (5602,2239,'Non-Critical','No','Adequate protection against the entrance of insects, vermin, rodents, dust and fumes','Fail to provide tightly sealed entrances and / or screened entrances to prevent insect / pest entry','2010-02-25 00:00:00',0),
 (5603,2239,'Critical','CDI','Food is held at 4&deg;C (40&deg;F) or less','Fail to hold hazardous food items at 4&deg; C (40&deg;F) or less','2010-02-25 00:00:00',0),
 (5604,2239,'Critical','CDI','','Hazardous food products cannot be in the temperature danger zone between 4&deg;C (40&deg;F) and 60&deg;C (140&deg;F) (except during periods of preparation)','2010-02-25 00:00:00',0),
 (5605,2239,'Note','No','Correction(s) Required, Food Handler Education on Site, Product Seized & Destroyed ',' ','2010-02-25 00:00:00',0),
 (5606,2239,'Critical','No','Food is held at 4&deg;C (40&deg;F) or less','Fail to hold hazardous food items at 4&deg; C (40&deg;F) or less','2009-09-09 00:00:00',0),
 (5607,2239,'Note','No','Correction(s) Are / Were Required ',' ','2009-09-09 00:00:00',0),
 (5608,2239,'Critical','No','Food is held at 4&deg;C (40&deg;F) or less','Fail to hold hazardous food items at 4&deg; C (40&deg;F) or less','2009-08-18 00:00:00',0),
 (5609,2239,'Non-Critical','No','','General housekeeping is satisfactory','2009-08-18 00:00:00',0),
 (5610,2239,'Critical','No','Food is frozen at -18&deg;C (0&deg;F) or less','Fail to maintain frozen food at temperature of -18&deg; C (0&deg;F) or less','2009-08-18 00:00:00',0),
 (5611,2239,'Critical','No','Food protected from potential contamination (e.g. food covered, labeled, off floor, stored on racks, sneeze guard, physical hazards, during display, during transport)','Fail to store food 15 cm off the floor to prevent contamination','2009-08-18 00:00:00',0),
 (5612,2239,'Note','No','Correction(s) Required, Food Handler Education on Site ',' ','2009-08-18 00:00:00',0),
 (5613,2240,'Non-Critical','No','Manual dishwashing: Wash, rinse, sanitize technique','Fail to provide sufficient detergent or chemicals for washing or sanitizing','2009-08-14 00:00:00',1),
 (5614,2240,'Critical','CDI','Food protected from potential contamination (e.g. food covered, labeled, off floor, stored on racks, sneeze guard, physical hazards, during display, during transport)* ','Fail to protect food from potential contamination','2009-08-14 00:00:00',1),
 (5615,2240,'Non-Critical','No','Hand washing basin with supplies of soap in a dispenser and paper towels','Fail to provide soap in a dispenser in the food preparation area','2009-08-14 00:00:00',1),
 (5616,2240,'Note','No','Correction(s) Are / Were Required ',' ','2009-08-14 00:00:00',1),
 (5617,2241,'Critical','CDI','Separate raw foods from ready-to-eat foods during storage and handling','Fail to store raw foods below cooked / ready to eat foods','2010-08-06 00:00:00',1),
 (5618,2241,'Critical','CDI','Hot holding: minimum of 60&deg;C (140&deg;F) after cooking / rapid re-heating','Fail to hold food at or above 60&deg;C (140&deg;F) after cooking / rapid re-heating','2010-08-06 00:00:00',1),
 (5619,2241,'Note','No','Correction(s) Required, Food Handler Education on Site ',' ','2010-08-06 00:00:00',1),
 (5620,2241,'Critical','CDI','Separate raw foods from ready-to-eat foods during storage and handling','Fail to store raw foods below cooked / ready to eat foods','2010-04-20 00:00:00',0),
 (5621,2241,'Non-Critical','CDI','Proper storage of clean utensils (including single service utensils)','Fail to store utensils in manner preventing contamination','2010-04-20 00:00:00',0),
 (5622,2241,'Note','No','Correction(s) Required, Food Handler Education on Site ',' ','2010-04-20 00:00:00',0),
 (5623,2241,'Critical','CDI','Food protected from potential contamination (e.g. food covered, labeled, off floor, stored on racks, sneeze guard, physical hazards, during display, during transport)','Fail to cover all food in storage','2009-09-02 00:00:00',0),
 (5624,2241,'Non-Critical','No','General housekeeping is satisfactory','Fail to maintain the premises in a clean and sanitary condition','2009-09-02 00:00:00',0),
 (5625,2241,'Note','No','Correction(s) Are / Were Required ',' ','2009-09-02 00:00:00',0),
 (5626,2242,'Note','No','Satisfactory - No Action Required ',' ','2010-05-31 00:00:00',1),
 (5627,2242,'Non-Critical','No','Sanitize test kit / thermometer readily available for verifying dishwashing and sanitizing temperatures','Fail to provide an easily readable thermometer for the dishwashing and sanitizing temperatures','2009-08-10 00:00:00',0),
 (5628,2242,'Note','No','Correction(s) Are / Were Required ',' ','2009-08-10 00:00:00',0),
 (5629,2243,'Note','No','Satisfactory - No Action Required ',' ','2010-06-07 00:00:00',1),
 (5630,2243,'Non-Critical','No','Exclusion of live animals on the premises, subject to exemptions','Fail to ensure room kept free from birds / animals','2010-06-04 00:00:00',0),
 (5631,2243,'Note','No','Correction(s) Are / Were Required ',' ','2010-06-04 00:00:00',0);
/*!40000 ALTER TABLE `inspection` ENABLE KEYS */;


--
-- Definition of table `restaurant`
--

DROP TABLE IF EXISTS `restaurant`;
CREATE TABLE `restaurant` (
  `restaurant_id` int(10) unsigned NOT NULL auto_increment,
  `location` varchar(120) NOT NULL,
  `address` varchar(120) NOT NULL,
  `city` varchar(80) NOT NULL,
  `latitude` decimal(10,6) default NULL,
  `longitude` decimal(10,6) default NULL,
  `inspected` date default NULL,
  `critical` int(10) unsigned default NULL,
  `noncritical` int(10) unsigned default NULL,
  `updated` datetime default NULL,
  `active` tinyint(1) default '1',
  `closed` int(11) NOT NULL default '0',
  `closed_date` datetime default NULL,
  PRIMARY KEY  (`restaurant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2245 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `restaurant`
--

/*!40000 ALTER TABLE `restaurant` DISABLE KEYS */;
INSERT INTO `restaurant` (`restaurant_id`,`location`,`address`,`city`,`latitude`,`longitude`,`inspected`,`critical`,`noncritical`,`updated`,`active`,`closed`,`closed_date`) VALUES 
 (2239,'Tony\'s Pizza and Tavern','980 Dundas St','London','42.992711','-81.218558','2010-10-13',0,1,'2010-11-03 09:56:08',1,0,NULL),
 (2240,'29 Park','359 Talbot St','London',NULL,NULL,'2009-08-14',1,2,'2010-11-03 10:50:22',1,0,NULL),
 (2241,'3M Company','300 Tartan Dr','London',NULL,NULL,'2010-08-06',2,0,'2010-11-03 10:50:29',1,0,NULL),
 (2242,'3M Company Cafeteria','1840 Oxford St E','London',NULL,NULL,'2010-05-31',0,0,'2010-11-03 10:50:41',1,0,NULL),
 (2243,'4-U Variety','1570 Highbury Ave','London',NULL,NULL,'2010-06-07',0,0,'2010-11-03 10:50:50',1,0,NULL),
 (2244,'5 Star Variety','57 York St','London',NULL,NULL,'2010-06-04',0,0,'2010-11-03 10:51:00',1,0,NULL);
/*!40000 ALTER TABLE `restaurant` ENABLE KEYS */;


--
-- Definition of table `updated`
--

DROP TABLE IF EXISTS `updated`;
CREATE TABLE `updated` (
  `update_id` int(10) unsigned NOT NULL auto_increment,
  `update` datetime NOT NULL,
  PRIMARY KEY  (`update_id`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `updated`
--

/*!40000 ALTER TABLE `updated` DISABLE KEYS */;
INSERT INTO `updated` (`update_id`,`update`) VALUES 
 (193,'2010-11-03 09:56:36'),
 (194,'2010-11-03 10:01:16'),
 (195,'2010-11-03 10:03:49'),
 (196,'2010-11-03 10:05:23'),
 (197,'2010-11-03 10:11:19');
/*!40000 ALTER TABLE `updated` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
