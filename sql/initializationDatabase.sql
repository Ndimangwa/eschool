-- MySQL dump 10.15  Distrib 10.0.23-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: collegeplus_init
-- ------------------------------------------------------
-- Server version	10.0.23-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `collegeplus_init`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `collegeplus_init` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `collegeplus_init`;

--
-- Table structure for table `accademicYear`
--

DROP TABLE IF EXISTS `accademicYear`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accademicYear` (
  `accademicYearId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accademicYear` varchar(16) NOT NULL,
  `accademicYearNumber` int(4) unsigned NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`accademicYearId`),
  UNIQUE KEY `accademicYear` (`accademicYear`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accademicYear`
--

LOCK TABLES `accademicYear` WRITE;
/*!40000 ALTER TABLE `accademicYear` DISABLE KEYS */;
INSERT INTO `accademicYear` VALUES (1,'2009/2010',1,NULL,NULL,15),(2,'2010/2011',2,NULL,NULL,15),(3,'2011/2012',3,NULL,NULL,15),(4,'2012/2013',4,NULL,NULL,15),(5,'2013/2014',5,NULL,NULL,15),(6,'2014/2015',6,NULL,NULL,15),(7,'2015/2016',7,NULL,NULL,15),(8,'2016/2017',8,NULL,NULL,15),(9,'2017/2018',9,NULL,NULL,15),(10,'2018/2019',10,NULL,NULL,15),(11,'2019/2020',11,NULL,NULL,15),(12,'2020/2021',12,NULL,NULL,15),(13,'2021/2022',13,NULL,NULL,15),(14,'2022/2023',14,NULL,NULL,15),(15,'2023/2024',15,NULL,NULL,15),(16,'2024/2025',16,NULL,NULL,15),(17,'2025/2026',17,NULL,NULL,15),(18,'2026/2027',18,NULL,NULL,15),(19,'2027/2028',19,NULL,NULL,15),(20,'2028/2029',20,NULL,NULL,15),(21,'2029/2030',21,NULL,NULL,15),(22,'2030/2031',22,NULL,NULL,15),(23,'2031/2032',23,NULL,NULL,15),(24,'2032/2033',24,NULL,NULL,15),(25,'2033/2034',25,NULL,NULL,15),(26,'2034/2035',26,NULL,NULL,15),(27,'2035/2036',27,NULL,NULL,15),(28,'2036/2037',28,NULL,NULL,15),(29,'2037/2038',29,NULL,NULL,15),(30,'2038/2039',30,NULL,NULL,15),(31,'2039/2040',31,NULL,NULL,15),(32,'2040/2041',32,NULL,NULL,15),(33,'2041/2042',33,NULL,NULL,15),(34,'2042/2043',34,NULL,NULL,15),(35,'2043/2044',35,NULL,NULL,15),(36,'2044/2045',36,NULL,NULL,15),(37,'2045/2046',37,NULL,NULL,15),(38,'2046/2047',38,NULL,NULL,15),(39,'2047/2048',39,NULL,NULL,15),(40,'2048/2049',40,NULL,NULL,15),(41,'2049/2050',41,NULL,NULL,15),(42,'2050/2051',42,NULL,NULL,15),(43,'2051/2052',43,NULL,NULL,15),(44,'2052/2053',44,NULL,NULL,15),(45,'2053/2054',45,NULL,NULL,15),(46,'2054/2055',46,NULL,NULL,15),(47,'2055/2056',47,NULL,NULL,15),(48,'2056/2057',48,NULL,NULL,15),(49,'2057/2058',49,NULL,NULL,15),(50,'2058/2059',50,NULL,NULL,15),(51,'2059/2060',51,NULL,NULL,15),(52,'2060/2061',52,NULL,NULL,15),(53,'2061/2062',53,NULL,NULL,15),(54,'2062/2063',54,NULL,NULL,15),(55,'2063/2064',55,NULL,NULL,15),(56,'2064/2065',56,NULL,NULL,15),(57,'2065/2066',57,NULL,NULL,15),(58,'2066/2067',58,NULL,NULL,15),(59,'2067/2068',59,NULL,NULL,15),(60,'2068/2069',60,NULL,NULL,15),(61,'2069/2070',61,NULL,NULL,15),(62,'2070/2071',62,NULL,NULL,15),(63,'2071/2072',63,NULL,NULL,15),(64,'2072/2073',64,NULL,NULL,15),(65,'2073/2074',65,NULL,NULL,15),(66,'2074/2075',66,NULL,NULL,15),(67,'2075/2076',67,NULL,NULL,15),(68,'2076/2077',68,NULL,NULL,15),(69,'2077/2078',69,NULL,NULL,15),(70,'2078/2079',70,NULL,NULL,15),(71,'2079/2080',71,NULL,NULL,15),(72,'2080/2081',72,NULL,NULL,15),(73,'2081/2082',73,NULL,NULL,15),(74,'2082/2083',74,NULL,NULL,15),(75,'2083/2084',75,NULL,NULL,15),(76,'2084/2085',76,NULL,NULL,15),(77,'2085/2086',77,NULL,NULL,15),(78,'2086/2087',78,NULL,NULL,15),(79,'2087/2088',79,NULL,NULL,15),(80,'2088/2089',80,NULL,NULL,15),(81,'2089/2090',81,NULL,NULL,15),(82,'2090/2091',82,NULL,NULL,15),(83,'2091/2092',83,NULL,NULL,15),(84,'2092/2093',84,NULL,NULL,15),(85,'2093/2094',85,NULL,NULL,15),(86,'2094/2095',86,NULL,NULL,15),(87,'2095/2096',87,NULL,NULL,15),(88,'2096/2097',88,NULL,NULL,15),(89,'2097/2098',89,NULL,NULL,15),(90,'2098/2099',90,NULL,NULL,15),(91,'2099/2100',91,NULL,NULL,15);
/*!40000 ALTER TABLE `accademicYear` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admissionProgress`
--

DROP TABLE IF EXISTS `admissionProgress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admissionProgress` (
  `progressId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `progressName` varchar(48) NOT NULL,
  `progressPage` int(2) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`progressId`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admissionProgress`
--

LOCK TABLES `admissionProgress` WRITE;
/*!40000 ALTER TABLE `admissionProgress` DISABLE KEYS */;
INSERT INTO `admissionProgress` VALUES (1,'Not Yet Started',0,NULL,NULL,15),(2,'Not Yet Started',1,NULL,NULL,15),(3,'Not Yet Started',2,NULL,NULL,15),(4,'Student Bio Data',3,NULL,NULL,15),(5,'Accademic History',4,NULL,NULL,15),(6,'Course Information',5,NULL,NULL,15),(7,'Photo Upload',6,NULL,NULL,15),(8,'Locality',7,NULL,NULL,15),(9,'Religion & Denomination',8,NULL,NULL,15),(10,'Student Addresses & Phones',9,NULL,NULL,15),(11,'Employment History',10,NULL,NULL,15),(12,'Sponsor Details',11,NULL,NULL,15),(13,'Bank Account',12,NULL,NULL,15),(14,'Next Of Kins',13,NULL,NULL,15),(15,'Security',14,NULL,NULL,15),(16,'Information Review',15,NULL,NULL,15),(17,'Policy and Agreement',16,NULL,NULL,15),(18,'Completed',17,NULL,NULL,15);
/*!40000 ALTER TABLE `admissionProgress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approvalSequenceData`
--

DROP TABLE IF EXISTS `approvalSequenceData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approvalSequenceData` (
  `dataId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `schemaId` int(10) unsigned NOT NULL,
  `requestedBy` int(10) unsigned NOT NULL,
  `nextJobToApprove` int(10) unsigned DEFAULT NULL,
  `alreadyApprovedList` varchar(108) DEFAULT NULL,
  `timeOfRegistration` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `specialInstruction` varchar(32) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(108) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`dataId`),
  KEY `schemaId` (`schemaId`),
  KEY `requestedBy` (`requestedBy`),
  KEY `nextJobToApprove` (`nextJobToApprove`),
  CONSTRAINT `approvalSequenceData_ibfk_1` FOREIGN KEY (`schemaId`) REFERENCES `approvalSequenceSchema` (`schemaId`),
  CONSTRAINT `approvalSequenceData_ibfk_2` FOREIGN KEY (`requestedBy`) REFERENCES `login` (`loginId`),
  CONSTRAINT `approvalSequenceData_ibfk_3` FOREIGN KEY (`nextJobToApprove`) REFERENCES `jobTitle` (`jobId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approvalSequenceData`
--

LOCK TABLES `approvalSequenceData` WRITE;
/*!40000 ALTER TABLE `approvalSequenceData` DISABLE KEYS */;
/*!40000 ALTER TABLE `approvalSequenceData` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approvalSequenceSchema`
--

DROP TABLE IF EXISTS `approvalSequenceSchema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `approvalSequenceSchema` (
  `schemaId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `schemaName` varchar(48) NOT NULL,
  `operationCode` int(4) unsigned NOT NULL,
  `approvedJobList` varchar(108) DEFAULT NULL,
  `approvingJobList` varchar(108) DEFAULT NULL,
  `validity` int(4) unsigned NOT NULL DEFAULT '7',
  `defaultApproveText` varchar(48) NOT NULL DEFAULT 'Approved By',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`schemaId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approvalSequenceSchema`
--

LOCK TABLES `approvalSequenceSchema` WRITE;
/*!40000 ALTER TABLE `approvalSequenceSchema` DISABLE KEYS */;
/*!40000 ALTER TABLE `approvalSequenceSchema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `attendanceId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attendanceName` varchar(48) NOT NULL,
  `courseId` int(10) unsigned DEFAULT NULL,
  `monthId` int(10) unsigned NOT NULL,
  `_ayear` int(4) unsigned NOT NULL,
  `attendanceFile` varchar(24) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`attendanceId`),
  UNIQUE KEY `courseId` (`courseId`,`monthId`,`_ayear`),
  KEY `monthId` (`monthId`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`),
  CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`monthId`) REFERENCES `monthOfAYear` (`monthId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `briefcase`
--

DROP TABLE IF EXISTS `briefcase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `briefcase` (
  `briefcaseId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `briefcaseName` varchar(48) NOT NULL,
  `groupId` int(10) unsigned NOT NULL,
  `weight` int(4) unsigned NOT NULL DEFAULT '1',
  `rawMarksCSVFile` varchar(32) DEFAULT NULL,
  `rawMarksCSVFileChecksum` char(32) DEFAULT NULL,
  `gradedMarksCSVFile` varchar(32) DEFAULT NULL,
  `gradedMarksCSVFileChecksum` char(32) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`briefcaseId`),
  KEY `groupId` (`groupId`),
  CONSTRAINT `briefcase_ibfk_1` FOREIGN KEY (`groupId`) REFERENCES `briefcaseGroup` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `briefcase`
--

LOCK TABLES `briefcase` WRITE;
/*!40000 ALTER TABLE `briefcase` DISABLE KEYS */;
/*!40000 ALTER TABLE `briefcase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `briefcaseGroup`
--

DROP TABLE IF EXISTS `briefcaseGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `briefcaseGroup` (
  `groupId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(48) NOT NULL,
  `courseId` int(10) unsigned NOT NULL,
  `_year` int(4) unsigned NOT NULL,
  `batchId` int(10) unsigned NOT NULL,
  `examinationId` int(10) unsigned NOT NULL,
  `loginId` int(10) unsigned NOT NULL,
  `listOfOwners` varchar(108) NOT NULL,
  `rawMarksCSVFile` varchar(32) DEFAULT NULL,
  `rawMarksCSVFileChecksum` char(32) DEFAULT NULL,
  `gradedMarksCSVFile` varchar(32) DEFAULT NULL,
  `gradedMarksCSVFileChecksum` char(32) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`groupId`),
  KEY `courseId` (`courseId`),
  KEY `batchId` (`batchId`),
  KEY `examinationId` (`examinationId`),
  KEY `loginId` (`loginId`),
  CONSTRAINT `briefcaseGroup_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`),
  CONSTRAINT `briefcaseGroup_ibfk_2` FOREIGN KEY (`batchId`) REFERENCES `accademicYear` (`accademicYearId`),
  CONSTRAINT `briefcaseGroup_ibfk_3` FOREIGN KEY (`examinationId`) REFERENCES `examination` (`examinationId`),
  CONSTRAINT `briefcaseGroup_ibfk_4` FOREIGN KEY (`loginId`) REFERENCES `login` (`loginId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `briefcaseGroup`
--

LOCK TABLES `briefcaseGroup` WRITE;
/*!40000 ALTER TABLE `briefcaseGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `briefcaseGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classOfAward`
--

DROP TABLE IF EXISTS `classOfAward`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classOfAward` (
  `awardId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `awardName` varchar(48) NOT NULL,
  `levelId` int(10) unsigned NOT NULL,
  `lowestGPA` varchar(8) NOT NULL,
  `highestGPA` varchar(8) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`awardId`),
  KEY `levelId` (`levelId`),
  CONSTRAINT `classOfAward_ibfk_1` FOREIGN KEY (`levelId`) REFERENCES `educationLevel` (`levelId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classOfAward`
--

LOCK TABLES `classOfAward` WRITE;
/*!40000 ALTER TABLE `classOfAward` DISABLE KEYS */;
/*!40000 ALTER TABLE `classOfAward` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contextDefinition`
--

DROP TABLE IF EXISTS `contextDefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contextDefinition` (
  `cId` int(10) unsigned NOT NULL,
  `cChar` char(1) NOT NULL,
  `cVal` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contextDefinition`
--

LOCK TABLES `contextDefinition` WRITE;
/*!40000 ALTER TABLE `contextDefinition` DISABLE KEYS */;
INSERT INTO `contextDefinition` VALUES (1,'0',0),(2,'1',1),(3,'2',2),(4,'3',3),(5,'4',4),(6,'5',5),(7,'6',6),(8,'7',7),(9,'8',8),(10,'9',9),(11,'A',10),(12,'B',11),(13,'C',12),(14,'D',13),(15,'E',14),(16,'F',15),(17,'G',16),(18,'H',17),(19,'I',18),(20,'J',19),(21,'K',20),(22,'L',21),(23,'M',22),(24,'N',23),(25,'O',24),(26,'P',25),(27,'Q',26),(28,'R',27),(29,'S',28),(30,'T',29),(31,'U',30),(32,'X',31);
/*!40000 ALTER TABLE `contextDefinition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contextManager`
--

DROP TABLE IF EXISTS `contextManager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contextManager` (
  `defaultXValue` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contextManager`
--

LOCK TABLES `contextManager` WRITE;
/*!40000 ALTER TABLE `contextManager` DISABLE KEYS */;
INSERT INTO `contextManager` VALUES (1);
/*!40000 ALTER TABLE `contextManager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contextPosition`
--

DROP TABLE IF EXISTS `contextPosition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contextPosition` (
  `cId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cName` varchar(56) NOT NULL,
  `cPosition` int(4) NOT NULL,
  `caption` varchar(96) NOT NULL,
  PRIMARY KEY (`cId`),
  UNIQUE KEY `cName` (`cName`),
  UNIQUE KEY `cPosition` (`cPosition`)
) ENGINE=InnoDB AUTO_INCREMENT=424 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contextPosition`
--

LOCK TABLES `contextPosition` WRITE;
/*!40000 ALTER TABLE `contextPosition` DISABLE KEYS */;
INSERT INTO `contextPosition` VALUES (1,'managephptimezone',1,'managephptimezone'),(2,'managephptimezone_add',2,'managephptimezone_add'),(3,'managephptimezone_detail',3,'managephptimezone_detail'),(4,'managephptimezone_edit',4,'managephptimezone_edit'),(5,'managephptimezone_delete',5,'managephptimezone_delete'),(6,'managephptimezone_csv',6,'managephptimezone_csv'),(7,'managedaysofaweek',7,'managedaysofaweek'),(8,'managedaysofaweek_add',8,'managedaysofaweek_add'),(9,'managedaysofaweek_detail',9,'managedaysofaweek_detail'),(10,'managedaysofaweek_edit',10,'managedaysofaweek_edit'),(11,'managedaysofaweek_delete',11,'managedaysofaweek_delete'),(12,'managedaysofaweek_csv',12,'managedaysofaweek_csv'),(13,'managemonthofayear',13,'managemonthofayear'),(14,'managemonthofayear_add',14,'managemonthofayear_add'),(15,'managemonthofayear_detail',15,'managemonthofayear_detail'),(16,'managemonthofayear_edit',16,'managemonthofayear_edit'),(17,'managemonthofayear_delete',17,'managemonthofayear_delete'),(18,'managemonthofayear_csv',18,'managemonthofayear_csv'),(19,'managetheme',19,'managetheme'),(20,'managetheme_add',20,'managetheme_add'),(21,'managetheme_detail',21,'managetheme_detail'),(22,'managetheme_edit',22,'managetheme_edit'),(23,'managetheme_delete',23,'managetheme_delete'),(24,'managetheme_csv',24,'managetheme_csv'),(25,'managecountry',25,'managecountry'),(26,'managecountry_add',26,'managecountry_add'),(27,'managecountry_detail',27,'managecountry_detail'),(28,'managecountry_edit',28,'managecountry_edit'),(29,'managecountry_delete',29,'managecountry_delete'),(30,'managecountry_csv',30,'managecountry_csv'),(31,'manageprofile',31,'manageprofile'),(32,'manageprofile_add',32,'manageprofile_add'),(33,'manageprofile_detail',33,'manageprofile_detail'),(34,'manageprofile_edit',34,'manageprofile_edit'),(35,'manageprofile_delete',35,'manageprofile_delete'),(36,'manageprofile_csv',36,'manageprofile_csv'),(37,'manageeducationlevel',37,'manageeducationlevel'),(38,'manageeducationlevel_add',38,'manageeducationlevel_add'),(39,'manageeducationlevel_detail',39,'manageeducationlevel_detail'),(40,'manageeducationlevel_edit',40,'manageeducationlevel_edit'),(41,'manageeducationlevel_delete',41,'manageeducationlevel_delete'),(42,'manageeducationlevel_csv',42,'manageeducationlevel_csv'),(43,'managedepartment',43,'managedepartment'),(44,'managedepartment_add',44,'managedepartment_add'),(45,'managedepartment_detail',45,'managedepartment_detail'),(46,'managedepartment_edit',46,'managedepartment_edit'),(47,'managedepartment_delete',47,'managedepartment_delete'),(48,'managedepartment_csv',48,'managedepartment_csv'),(49,'managecourse',49,'managecourse'),(50,'managecourse_add',50,'managecourse_add'),(51,'managecourse_detail',51,'managecourse_detail'),(52,'managecourse_edit',52,'managecourse_edit'),(53,'managecourse_delete',53,'managecourse_delete'),(54,'managecourse_csv',54,'managecourse_csv'),(55,'managesubject',55,'managesubject'),(56,'managesubject_add',56,'managesubject_add'),(57,'managesubject_detail',57,'managesubject_detail'),(58,'managesubject_edit',58,'managesubject_edit'),(59,'managesubject_delete',59,'managesubject_delete'),(60,'managesubject_csv',60,'managesubject_csv'),(61,'managecourseandsubjecttransaction',61,'managecourseandsubjecttransaction'),(62,'managecourseandsubjecttransaction_add',62,'managecourseandsubjecttransaction_add'),(63,'managecourseandsubjecttransaction_detail',63,'managecourseandsubjecttransaction_detail'),(64,'managecourseandsubjecttransaction_edit',64,'managecourseandsubjecttransaction_edit'),(65,'managecourseandsubjecttransaction_delete',65,'managecourseandsubjecttransaction_delete'),(66,'managecourseandsubjecttransaction_csv',66,'managecourseandsubjecttransaction_csv'),(67,'managesecurityquestion',67,'managesecurityquestion'),(68,'managesecurityquestion_add',68,'managesecurityquestion_add'),(69,'managesecurityquestion_detail',69,'managesecurityquestion_detail'),(70,'managesecurityquestion_edit',70,'managesecurityquestion_edit'),(71,'managesecurityquestion_delete',71,'managesecurityquestion_delete'),(72,'managesecurityquestion_csv',72,'managesecurityquestion_csv'),(73,'manageusertype',73,'manageusertype'),(74,'manageusertype_add',74,'manageusertype_add'),(75,'manageusertype_detail',75,'manageusertype_detail'),(76,'manageusertype_edit',76,'manageusertype_edit'),(77,'manageusertype_delete',77,'manageusertype_delete'),(78,'manageusertype_csv',78,'manageusertype_csv'),(79,'manageuserstatus',79,'manageuserstatus'),(80,'manageuserstatus_add',80,'manageuserstatus_add'),(81,'manageuserstatus_detail',81,'manageuserstatus_detail'),(82,'manageuserstatus_edit',82,'manageuserstatus_edit'),(83,'manageuserstatus_delete',83,'manageuserstatus_delete'),(84,'manageuserstatus_csv',84,'manageuserstatus_csv'),(85,'managesex',85,'managesex'),(86,'managesex_add',86,'managesex_add'),(87,'managesex_detail',87,'managesex_detail'),(88,'managesex_edit',88,'managesex_edit'),(89,'managesex_delete',89,'managesex_delete'),(90,'managesex_csv',90,'managesex_csv'),(91,'managemarital',91,'managemarital'),(92,'managemarital_add',92,'managemarital_add'),(93,'managemarital_detail',93,'managemarital_detail'),(94,'managemarital_edit',94,'managemarital_edit'),(95,'managemarital_delete',95,'managemarital_delete'),(96,'managemarital_csv',96,'managemarital_csv'),(97,'managegroup',97,'managegroup'),(98,'managegroup_add',98,'managegroup_add'),(99,'managegroup_detail',99,'managegroup_detail'),(100,'managegroup_edit',100,'managegroup_edit'),(101,'managegroup_delete',101,'managegroup_delete'),(102,'managegroup_csv',102,'managegroup_csv'),(103,'managejobtitle',103,'managejobtitle'),(104,'managejobtitle_add',104,'managejobtitle_add'),(105,'managejobtitle_detail',105,'managejobtitle_detail'),(106,'managejobtitle_edit',106,'managejobtitle_edit'),(107,'managejobtitle_delete',107,'managejobtitle_delete'),(108,'managejobtitle_csv',108,'managejobtitle_csv'),(109,'managereligion',109,'managereligion'),(110,'managereligion_add',110,'managereligion_add'),(111,'managereligion_detail',111,'managereligion_detail'),(112,'managereligion_edit',112,'managereligion_edit'),(113,'managereligion_delete',113,'managereligion_delete'),(114,'managereligion_csv',114,'managereligion_csv'),(115,'managedenomination',115,'managedenomination'),(116,'managedenomination_add',116,'managedenomination_add'),(117,'managedenomination_detail',117,'managedenomination_detail'),(118,'managedenomination_edit',118,'managedenomination_edit'),(119,'managedenomination_delete',119,'managedenomination_delete'),(120,'managedenomination_csv',120,'managedenomination_csv'),(121,'managelogin',121,'managelogin'),(122,'managelogin_add',122,'managelogin_add'),(123,'managelogin_detail',123,'managelogin_detail'),(124,'managelogin_edit',124,'managelogin_edit'),(125,'managelogin_delete',125,'managelogin_delete'),(126,'managelogin_csv',126,'managelogin_csv'),(127,'manageuser',127,'manageuser'),(128,'manageuser_add',128,'manageuser_add'),(129,'manageuser_detail',129,'manageuser_detail'),(130,'manageuser_edit',130,'manageuser_edit'),(131,'manageuser_delete',131,'manageuser_delete'),(132,'manageuser_csv',132,'manageuser_csv'),(133,'managecourseinstructor',133,'managecourseinstructor'),(134,'managecourseinstructor_add',134,'managecourseinstructor_add'),(135,'managecourseinstructor_detail',135,'managecourseinstructor_detail'),(136,'managecourseinstructor_edit',136,'managecourseinstructor_edit'),(137,'managecourseinstructor_delete',137,'managecourseinstructor_delete'),(138,'managecourseinstructor_csv',138,'managecourseinstructor_csv'),(139,'managestudent',139,'managestudent'),(140,'managestudent_add',140,'managestudent_add'),(141,'managestudent_detail',141,'managestudent_detail'),(142,'managestudent_edit',142,'managestudent_edit'),(143,'managestudent_delete',143,'managestudent_delete'),(144,'managestudent_csv',144,'managestudent_csv'),(145,'manageexaminationgroup',145,'manageexaminationgroup'),(146,'manageexaminationgroup_add',146,'manageexaminationgroup_add'),(147,'manageexaminationgroup_detail',147,'manageexaminationgroup_detail'),(148,'manageexaminationgroup_edit',148,'manageexaminationgroup_edit'),(149,'manageexaminationgroup_delete',149,'manageexaminationgroup_delete'),(150,'manageexaminationgroup_csv',150,'manageexaminationgroup_csv'),(151,'manageexamination',151,'manageexamination'),(152,'manageexamination_add',152,'manageexamination_add'),(153,'manageexamination_detail',153,'manageexamination_detail'),(154,'manageexamination_edit',154,'manageexamination_edit'),(155,'manageexamination_delete',155,'manageexamination_delete'),(156,'manageexamination_csv',156,'manageexamination_csv'),(157,'manageexaminationnumberscope',157,'manageexaminationnumberscope'),(158,'manageexaminationnumberscope_add',158,'manageexaminationnumberscope_add'),(159,'manageexaminationnumberscope_detail',159,'manageexaminationnumberscope_detail'),(160,'manageexaminationnumberscope_edit',160,'manageexaminationnumberscope_edit'),(161,'manageexaminationnumberscope_delete',161,'manageexaminationnumberscope_delete'),(162,'manageexaminationnumberscope_csv',162,'manageexaminationnumberscope_csv'),(163,'manageexaminationnumber',163,'manageexaminationnumber'),(164,'manageexaminationnumber_add',164,'manageexaminationnumber_add'),(165,'manageexaminationnumber_detail',165,'manageexaminationnumber_detail'),(166,'manageexaminationnumber_edit',166,'manageexaminationnumber_edit'),(167,'manageexaminationnumber_delete',167,'manageexaminationnumber_delete'),(168,'manageexaminationnumber_csv',168,'manageexaminationnumber_csv'),(169,'managegrade',169,'managegrade'),(170,'managegrade_add',170,'managegrade_add'),(171,'managegrade_detail',171,'managegrade_detail'),(172,'managegrade_edit',172,'managegrade_edit'),(173,'managegrade_delete',173,'managegrade_delete'),(174,'managegrade_csv',174,'managegrade_csv'),(175,'manageresults',175,'manageresults'),(176,'manageresults_add',176,'manageresults_add'),(177,'manageresults_detail',177,'manageresults_detail'),(178,'manageresults_edit',178,'manageresults_edit'),(179,'manageresults_delete',179,'manageresults_delete'),(180,'manageresults_csv',180,'manageresults_csv'),(181,'managecurrency',181,'managecurrency'),(182,'managecurrency_add',182,'managecurrency_add'),(183,'managecurrency_detail',183,'managecurrency_detail'),(184,'managecurrency_edit',184,'managecurrency_edit'),(185,'managecurrency_delete',185,'managecurrency_delete'),(186,'managecurrency_csv',186,'managecurrency_csv'),(187,'managefeestructure',187,'managefeestructure'),(188,'managefeestructure_add',188,'managefeestructure_add'),(189,'managefeestructure_detail',189,'managefeestructure_detail'),(190,'managefeestructure_edit',190,'managefeestructure_edit'),(191,'managefeestructure_delete',191,'managefeestructure_delete'),(192,'managefeestructure_csv',192,'managefeestructure_csv'),(193,'managefeeinvoice',193,'managefeeinvoice'),(194,'managefeeinvoice_add',194,'managefeeinvoice_add'),(195,'managefeeinvoice_detail',195,'managefeeinvoice_detail'),(196,'managefeeinvoice_edit',196,'managefeeinvoice_edit'),(197,'managefeeinvoice_delete',197,'managefeeinvoice_delete'),(198,'managefeeinvoice_csv',198,'managefeeinvoice_csv'),(199,'managefeepayer',199,'managefeepayer'),(200,'managefeepayer_add',200,'managefeepayer_add'),(201,'managefeepayer_detail',201,'managefeepayer_detail'),(202,'managefeepayer_edit',202,'managefeepayer_edit'),(203,'managefeepayer_delete',203,'managefeepayer_delete'),(204,'managefeepayer_csv',204,'managefeepayer_csv'),(205,'managefeepayment',205,'managefeepayment'),(206,'managefeepayment_add',206,'managefeepayment_add'),(207,'managefeepayment_detail',207,'managefeepayment_detail'),(208,'managefeepayment_edit',208,'managefeepayment_edit'),(209,'managefeepayment_delete',209,'managefeepayment_delete'),(210,'managefeepayment_csv',210,'managefeepayment_csv'),(211,'managevenue',211,'managevenue'),(212,'managevenue_add',212,'managevenue_add'),(213,'managevenue_detail',213,'managevenue_detail'),(214,'managevenue_edit',214,'managevenue_edit'),(215,'managevenue_delete',215,'managevenue_delete'),(216,'managevenue_csv',216,'managevenue_csv'),(217,'managetimetable',217,'managetimetable'),(218,'managetimetable_add',218,'managetimetable_add'),(219,'managetimetable_detail',219,'managetimetable_detail'),(220,'managetimetable_edit',220,'managetimetable_edit'),(221,'managetimetable_delete',221,'managetimetable_delete'),(222,'managetimetable_csv',222,'managetimetable_csv'),(223,'manageschedule',223,'manageschedule'),(224,'manageschedule_add',224,'manageschedule_add'),(225,'manageschedule_detail',225,'manageschedule_detail'),(226,'manageschedule_edit',226,'manageschedule_edit'),(227,'manageschedule_delete',227,'manageschedule_delete'),(228,'manageschedule_csv',228,'manageschedule_csv'),(229,'manageholiday',229,'manageholiday'),(230,'manageholiday_add',230,'manageholiday_add'),(231,'manageholiday_detail',231,'manageholiday_detail'),(232,'manageholiday_edit',232,'manageholiday_edit'),(233,'manageholiday_delete',233,'manageholiday_delete'),(234,'manageholiday_csv',234,'manageholiday_csv'),(235,'manageattendance',235,'manageattendance'),(236,'manageattendance_add',236,'manageattendance_add'),(237,'manageattendance_detail',237,'manageattendance_detail'),(238,'manageattendance_edit',238,'manageattendance_edit'),(239,'manageattendance_delete',239,'manageattendance_delete'),(240,'manageattendance_csv',240,'manageattendance_csv'),(241,'manageleavetype',241,'manageleavetype'),(242,'manageleavetype_add',242,'manageleavetype_add'),(243,'manageleavetype_detail',243,'manageleavetype_detail'),(244,'manageleavetype_edit',244,'manageleavetype_edit'),(245,'manageleavetype_delete',245,'manageleavetype_delete'),(246,'manageleavetype_csv',246,'manageleavetype_csv'),(247,'manageleaveapprovalschemagroup',247,'manageleaveapprovalschemagroup'),(248,'manageleaveapprovalschemagroup_add',248,'manageleaveapprovalschemagroup_add'),(249,'manageleaveapprovalschemagroup_detail',249,'manageleaveapprovalschemagroup_detail'),(250,'manageleaveapprovalschemagroup_edit',250,'manageleaveapprovalschemagroup_edit'),(251,'manageleaveapprovalschemagroup_delete',251,'manageleaveapprovalschemagroup_delete'),(252,'manageleaveapprovalschemagroup_csv',252,'manageleaveapprovalschemagroup_csv'),(253,'manageleaveapprovalschema',253,'manageleaveapprovalschema'),(254,'manageleaveapprovalschema_add',254,'manageleaveapprovalschema_add'),(255,'manageleaveapprovalschema_detail',255,'manageleaveapprovalschema_detail'),(256,'manageleaveapprovalschema_edit',256,'manageleaveapprovalschema_edit'),(257,'manageleaveapprovalschema_delete',257,'manageleaveapprovalschema_delete'),(258,'manageleaveapprovalschema_csv',258,'manageleaveapprovalschema_csv'),(259,'manageleaveapplication',259,'manageleaveapplication'),(260,'manageleaveapplication_add',260,'manageleaveapplication_add'),(261,'manageleaveapplication_detail',261,'manageleaveapplication_detail'),(262,'manageleaveapplication_edit',262,'manageleaveapplication_edit'),(263,'manageleaveapplication_delete',263,'manageleaveapplication_delete'),(264,'manageleaveapplication_csv',264,'manageleaveapplication_csv'),(265,'managemessagetype',265,'managemessagetype'),(266,'managemessagetype_add',266,'managemessagetype_add'),(267,'managemessagetype_detail',267,'managemessagetype_detail'),(268,'managemessagetype_edit',268,'managemessagetype_edit'),(269,'managemessagetype_delete',269,'managemessagetype_delete'),(270,'managemessagetype_csv',270,'managemessagetype_csv'),(271,'managemessagepolicytype',271,'managemessagepolicytype'),(272,'managemessagepolicytype_add',272,'managemessagepolicytype_add'),(273,'managemessagepolicytype_detail',273,'managemessagepolicytype_detail'),(274,'managemessagepolicytype_edit',274,'managemessagepolicytype_edit'),(275,'managemessagepolicytype_delete',275,'managemessagepolicytype_delete'),(276,'managemessagepolicytype_csv',276,'managemessagepolicytype_csv'),(277,'manageresultschangingschemagroup',277,'manageresultschangingschemagroup'),(278,'manageresultschangingschemagroup_add',278,'manageresultschangingschemagroup_add'),(279,'manageresultschangingschemagroup_detail',279,'manageresultschangingschemagroup_detail'),(280,'manageresultschangingschemagroup_edit',280,'manageresultschangingschemagroup_edit'),(281,'manageresultschangingschemagroup_delete',281,'manageresultschangingschemagroup_delete'),(282,'manageresultschangingschemagroup_csv',282,'manageresultschangingschemagroup_csv'),(283,'manageresultschangingschema',283,'manageresultschangingschema'),(284,'manageresultschangingschema_add',284,'manageresultschangingschema_add'),(285,'manageresultschangingschema_detail',285,'manageresultschangingschema_detail'),(286,'manageresultschangingschema_edit',286,'manageresultschangingschema_edit'),(287,'manageresultschangingschema_delete',287,'manageresultschangingschema_delete'),(288,'manageresultschangingschema_csv',288,'manageresultschangingschema_csv'),(289,'manageresultschangelog',289,'manageresultschangelog'),(290,'manageresultschangelog_add',290,'manageresultschangelog_add'),(291,'manageresultschangelog_detail',291,'manageresultschangelog_detail'),(292,'manageresultschangelog_edit',292,'manageresultschangelog_edit'),(293,'manageresultschangelog_delete',293,'manageresultschangelog_delete'),(294,'manageresultschangelog_csv',294,'manageresultschangelog_csv'),(295,'manageentrycriteria',295,'manageentrycriteria'),(296,'manageentrycriteria_add',296,'manageentrycriteria_add'),(297,'manageentrycriteria_detail',297,'manageentrycriteria_detail'),(298,'manageentrycriteria_edit',298,'manageentrycriteria_edit'),(299,'manageentrycriteria_delete',299,'manageentrycriteria_delete'),(300,'manageentrycriteria_csv',300,'manageentrycriteria_csv'),(301,'managegeneralapprovalschema',301,'managegeneralapprovalschema'),(302,'managegeneralapprovalschema_add',302,'managegeneralapprovalschema_add'),(303,'managegeneralapprovalschema_detail',303,'managegeneralapprovalschema_detail'),(304,'managegeneralapprovalschema_edit',304,'managegeneralapprovalschema_edit'),(305,'managegeneralapprovalschema_delete',305,'managegeneralapprovalschema_delete'),(306,'managegeneralapprovalschema_csv',306,'managegeneralapprovalschema_csv'),(307,'managegeneralapprovaldata',307,'managegeneralapprovaldata'),(308,'managegeneralapprovaldata_add',308,'managegeneralapprovaldata_add'),(309,'managegeneralapprovaldata_detail',309,'managegeneralapprovaldata_detail'),(310,'managegeneralapprovaldata_edit',310,'managegeneralapprovaldata_edit'),(311,'managegeneralapprovaldata_delete',311,'managegeneralapprovaldata_delete'),(312,'managegeneralapprovaldata_csv',312,'managegeneralapprovaldata_csv'),(313,'manageadmissionprogress',313,'manageadmissionprogress'),(314,'manageadmissionprogress_add',314,'manageadmissionprogress_add'),(315,'manageadmissionprogress_detail',315,'manageadmissionprogress_detail'),(316,'manageadmissionprogress_edit',316,'manageadmissionprogress_edit'),(317,'manageadmissionprogress_delete',317,'manageadmissionprogress_delete'),(318,'manageadmissionprogress_csv',318,'manageadmissionprogress_csv'),(319,'managesystemfirewall',319,'Manage System Firewall'),(320,'managesystemfirewall_graph',320,'Manage System Firewall Graphs'),(321,'menu_system',321,'System Menu'),(322,'menu_users',322,'Users Menu'),(323,'menu_courses',323,'Courses Menu'),(324,'menu_accademics',324,'Accademics Menu'),(325,'menu_examination',325,'Examination Menu'),(326,'menu_resources',326,'Resources Menu'),(327,'menu_accounts',327,'Accounts Menu'),(328,'menu_messaging',328,'Messaging Menu'),(329,'menu_attendance',329,'Attendance Menu'),(330,'menu_help',330,'Help Menu'),(331,'menu_registration',331,'Registration Menu'),(332,'managesystemlogs',332,'System Logs'),(333,'menu_tools',333,'System Tools'),(334,'manageclassofaward',334,'manageclassofaward'),(335,'manageclassofaward_add',335,'manageclassofaward_add'),(336,'manageclassofaward_detail',336,'manageclassofaward_detail'),(337,'manageclassofaward_edit',337,'manageclassofaward_edit'),(338,'manageclassofaward_delete',338,'manageclassofaward_delete'),(339,'manageclassofaward_csv',339,'manageclassofaward_csv'),(340,'managecontextmanager',340,'managecontextmanager'),(341,'manageaccademicyear',341,'manageaccademicyear'),(342,'menu_results',342,'menu_results'),(343,'manageresultsgroup',343,'manageresultsgroup'),(344,'manageresultsgroup_add',344,'manageresultsgroup_add'),(345,'manageresultsgroup_detail',345,'manageresultsgroup_detail'),(346,'manageresultsgroup_edit',346,'manageresultsgroup_edit'),(347,'manageresultsgroup_delete',347,'manageresultsgroup_delete'),(348,'manageresultsgroup_csv',348,'manageresultsgroup_csv'),(349,'managestudent_admit',349,'managestudent_admit'),(350,'manageadmission_open_close',350,'manageadmission_open_close'),(351,'menu_studentportal',351,'menu_studentportal'),(352,'studentportal_subject',352,'studentportal_subject'),(353,'studentportal_results',353,'studentportal_results'),(354,'studentportal_semester_registration',354,'studentportal_semester_registration'),(355,'manageresults_make_template',355,'make results template'),(356,'manageresults_upload',356,'Upload Results'),(357,'manageresults_download',357,'Download Results'),(358,'manageresults_publish',358,'Publish Results'),(359,'manageresults_change_policy',359,'Change Results Policy'),(360,'submenu_results_management',360,'Submenu Results Management'),(361,'submenu_results_upload',361,'Submenu Results Upload'),(362,'submenu_results_download',362,'Submenu Results Download'),(363,'submenu_results_tools',363,'Submenu Results Tools'),(364,'manageresults_change_maximum_score',364,'Change Maximum Score'),(365,'managemessagepolicy',365,'Manage Message Policy'),(366,'managemessagepolicy_add',366,'Add Message Policy'),(367,'managemessagepolicy_delete',367,'Remove Message Policy'),(368,'managemessagepolicy_detail',368,'Details of Message Policy'),(369,'managefeeinvoicegroup',369,'Fee Invoice Group'),(370,'managefeeinvoicegroup_add',370,'Add a New Fee Invoice Group'),(371,'managefeeinvoicegroup_detail',371,'Details of Fee Invoice Group'),(372,'managefeeinvoicegroup_edit',372,'Edit Fee Invoice Group'),(373,'managefeeinvoicegroup_delete',373,'Delete Fee Invoice Group'),(374,'managefeeinvoicegroup_csv',374,'Download CSV for Fee Invoice Group'),(375,'submenu_invoices',375,'Invoices Submenu'),(376,'managecourse_synchronize',376,'Synchronize courses'),(377,'manageresults_synchronize',377,'Synchronize results'),(378,'manageprofile_advance_accademicyear',378,'Advance Accademic Year'),(379,'managestudent_registration_progress',379,'Registration Progress Management'),(380,'manageresults_monitor_devel',380,'Monitor Results, Development'),(381,'manageapprovalsequenceschema',381,'manageapprovalsequenceschema'),(382,'manageapprovalsequenceschema_add',382,'manageapprovalsequenceschema_add'),(383,'manageapprovalsequenceschema_detail',383,'manageapprovalsequenceschema_detail'),(384,'manageapprovalsequenceschema_edit',384,'manageapprovalsequenceschema_edit'),(385,'manageapprovalsequenceschema_delete',385,'manageapprovalsequenceschema_delete'),(386,'manageapprovalsequenceschema_csv',386,'manageapprovalsequenceschema_csv'),(387,'manageapprovalsequencedata',387,'manageapprovalsequencedata'),(388,'manageapprovalsequencedata_add',388,'manageapprovalsequencedata_add'),(389,'manageapprovalsequencedata_detail',389,'manageapprovalsequencedata_detail'),(390,'manageapprovalsequencedata_edit',390,'manageapprovalsequencedata_edit'),(391,'manageapprovalsequencedata_delete',391,'manageapprovalsequencedata_delete'),(392,'manageapprovalsequencedata_csv',392,'manageapprovalsequencedata_csv'),(393,'manageprofile_change_semester',393,'Change System Semester'),(394,'studentportal_payment_history',394,'View Student Payment History'),(395,'studentportal_transcript',395,'View Transcript for a Graduated Student'),(396,'managehumanresource',396,'Human Resource Management'),(397,'managehumanresource_attendance',397,'Attendance for Human Resource'),(398,'manageresults_nulify',398,'Nulify Results'),(399,'managebriefcasegroup',399,'Briefcase Group Management for Results'),(400,'managebriefcasegroup_add',400,'Add a New Briefcase Group'),(401,'managebriefcasegroup_detail',401,'View an Existing Briefcase Group'),(402,'managebriefcasegroup_edit',402,'Edit an Existing Briefcase Group'),(403,'managebriefcasegroup_delete',403,'Delete an Existing Briefcase Group'),(404,'managebriefcasegroup_csv',404,'Download Excel/CSV Briefcase Group data'),(405,'managebriefcase',405,'Briefcase Management for Results'),(406,'managebriefcase_add',406,'Add a New Briefcase'),(407,'managebriefcase_detail',407,'View an Existing Briefcase'),(408,'managebriefcase_edit',408,'Edit an Existing Briefcase'),(409,'managebriefcase_delete',409,'Delete an Existing Briefcase'),(410,'managebriefcase_csv',410,'Download Excel/CSV Briefcase data'),(411,'managefiles_compare',411,'Compare Files'),(412,'managepaper',412,'Printing Paper Management'),(413,'managepaper_add',413,'Add a New Paper Definition'),(414,'managepaper_detail',414,'View an Existing Paper Definition'),(415,'managepaper_edit',415,'Edit an Existing Paper Definition'),(416,'managepaper_delete',416,'Delete an Existing Paper Definition'),(417,'managepaper_csv',417,'Download Excel/CSV Paper Definition'),(418,'managewireless',418,'Wireless Connection Management'),(419,'managewireless_connect',419,'Connect to a wireless Services'),(420,'managewireless_disconnect',420,'Disconnect from Wireless Services'),(421,'console_manage_results',421,'Results Management Console'),(422,'manageexamination_signing_sheet',422,'Manage Examination Signing Sheet'),(423,'managestudent_academic_progress_report',423,'Academic Progress Report for Student');
/*!40000 ALTER TABLE `contextPosition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `countryId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `countryName` varchar(48) NOT NULL,
  `abbreviation` varchar(4) NOT NULL,
  `code` int(3) NOT NULL,
  `timezone` varchar(5) NOT NULL DEFAULT '03:00',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`countryId`),
  UNIQUE KEY `countryName` (`countryName`)
) ENGINE=InnoDB AUTO_INCREMENT=259 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` VALUES (1,'Afghanistan','AF',255,'0300',NULL,NULL,15),(2,'Akrotiri','00',255,'0300',NULL,NULL,15),(3,'Albania','AL',255,'0300',NULL,NULL,15),(4,'Algeria','DZ',255,'0300',NULL,NULL,15),(5,'American Samoa','AS',255,'0300',NULL,NULL,15),(6,'Andorra','AD',255,'0300',NULL,NULL,15),(7,'Angola','AO',255,'0300',NULL,NULL,15),(8,'Anguilla','AI',255,'0300',NULL,NULL,15),(9,'Antarctica','AQ',255,'0300',NULL,NULL,15),(10,'Antigua and Barbuda','AG',255,'0300',NULL,NULL,15),(11,'Argentina','AR',255,'0300',NULL,NULL,15),(12,'Armenia','AM',255,'0300',NULL,NULL,15),(13,'Aruba','AW',255,'0300',NULL,NULL,15),(14,'Ashmore and Cartier Islands','00',255,'0300',NULL,NULL,15),(15,'Australia','AU',255,'0300',NULL,NULL,15),(16,'Austria','AT',255,'0300',NULL,NULL,15),(17,'Azerbaijan','00',255,'0300',NULL,NULL,15),(18,'Bahamas, The','BS',255,'0300',NULL,NULL,15),(19,'Bahrain','BH',255,'0300',NULL,NULL,15),(20,'Bangladesh','BD',255,'0300',NULL,NULL,15),(21,'Barbados','BB',255,'0300',NULL,NULL,15),(22,'Bassas da India','00',255,'0300',NULL,NULL,15),(23,'Belarus','BY',255,'0300',NULL,NULL,15),(24,'Belgium','BE',255,'0300',NULL,NULL,15),(25,'Belize','BZ',255,'0300',NULL,NULL,15),(26,'Benin','BJ',255,'0300',NULL,NULL,15),(27,'Bermuda','BM',255,'0300',NULL,NULL,15),(28,'Bhutan','BT',255,'0300',NULL,NULL,15),(29,'Bolivia','BO',255,'0300',NULL,NULL,15),(30,'Bosnia and Herzegovina','BA',255,'0300',NULL,NULL,15),(31,'Botswana','BW',255,'0300',NULL,NULL,15),(32,'Bouvet Island','BV',255,'0300',NULL,NULL,15),(33,'Brazil','BR',255,'0300',NULL,NULL,15),(34,'British Indian Ocean Territory','IO',255,'0300',NULL,NULL,15),(35,'British Virgin Islands','00',255,'0300',NULL,NULL,15),(36,'Brunei','BN',255,'0300',NULL,NULL,15),(37,'Bulgaria','BG',255,'0300',NULL,NULL,15),(38,'Burkina Faso','BF',255,'0300',NULL,NULL,15),(39,'Burma','MM',255,'0300',NULL,NULL,15),(40,'Burundi','BI',255,'0300',NULL,NULL,15),(41,'Cambodia','KH',255,'0300',NULL,NULL,15),(42,'Cameroon','CM',255,'0300',NULL,NULL,15),(43,'Canada','CA',255,'0300',NULL,NULL,15),(44,'Cape Verde','CV',255,'0300',NULL,NULL,15),(45,'Cayman Islands','KY',255,'0300',NULL,NULL,15),(46,'Central African Republic','CF',255,'0300',NULL,NULL,15),(47,'Chad','TD',255,'0300',NULL,NULL,15),(48,'Chile','CL',255,'0300',NULL,NULL,15),(49,'China','CN',255,'0300',NULL,NULL,15),(50,'Christmas Island','CX',255,'0300',NULL,NULL,15),(51,'Clipperton Island','00',255,'0300',NULL,NULL,15),(52,'Cocos (Keeling) Islands','CC',255,'0300',NULL,NULL,15),(53,'Colombia','CO',255,'0300',NULL,NULL,15),(54,'Comoros','KM',255,'0300',NULL,NULL,15),(55,'Congo, Democratic Republic of th','CD',255,'0300',NULL,NULL,15),(56,'Congo, Republic of the','CG',255,'0300',NULL,NULL,15),(57,'Cook Islands','CK',255,'0300',NULL,NULL,15),(58,'Coral Sea Islands','00',255,'0300',NULL,NULL,15),(59,'Costa Rica','CR',255,'0300',NULL,NULL,15),(60,'Cote d\'Ivoire','CI',255,'0300',NULL,NULL,15),(61,'Croatia','HR',255,'0300',NULL,NULL,15),(62,'Cuba','CU',255,'0300',NULL,NULL,15),(63,'Cyprus','CY',255,'0300',NULL,NULL,15),(64,'Czech Republic','CZ',255,'0300',NULL,NULL,15),(65,'Denmark','DK',255,'0300',NULL,NULL,15),(66,'Dhekelia','00',255,'0300',NULL,NULL,15),(67,'Djibouti','DJ',255,'0300',NULL,NULL,15),(68,'Dominica','DM',255,'0300',NULL,NULL,15),(69,'Dominican Republic','DO',255,'0300',NULL,NULL,15),(70,'Ecuador','EC',255,'0300',NULL,NULL,15),(71,'Egypt','EG',255,'0300',NULL,NULL,15),(72,'El Salvador','SV',255,'0300',NULL,NULL,15),(73,'Equatorial Guinea','GQ',255,'0300',NULL,NULL,15),(74,'Eritrea','ER',255,'0300',NULL,NULL,15),(75,'Estonia','EE',255,'0300',NULL,NULL,15),(76,'Ethiopia','ET',255,'0300',NULL,NULL,15),(77,'Europa Island','00',255,'0300',NULL,NULL,15),(78,'Falkland Islands (Islas Malvinas','FK',255,'0300',NULL,NULL,15),(79,'Faroe Islands','FO',255,'0300',NULL,NULL,15),(80,'Fiji','FJ',255,'0300',NULL,NULL,15),(81,'Finland','FI',255,'0300',NULL,NULL,15),(82,'France','FR',255,'0300',NULL,NULL,15),(83,'French Guiana','GF',255,'0300',NULL,NULL,15),(84,'French Polynesia','PF',255,'0300',NULL,NULL,15),(85,'French Southern and Antarctic La','TF',255,'0300',NULL,NULL,15),(86,'Gabon','GA',255,'0300',NULL,NULL,15),(87,'Gambia, The','GM',255,'0300',NULL,NULL,15),(88,'Gaza Strip','00',255,'0300',NULL,NULL,15),(89,'Georgia','GE',255,'0300',NULL,NULL,15),(90,'Germany','DE',255,'0300',NULL,NULL,15),(91,'Ghana','GH',255,'0300',NULL,NULL,15),(92,'Gibraltar','GI',255,'0300',NULL,NULL,15),(93,'Glorioso Islands','00',255,'0300',NULL,NULL,15),(94,'Greece','GR',255,'0300',NULL,NULL,15),(95,'Greenland','GL',255,'0300',NULL,NULL,15),(96,'Grenada','GD',255,'0300',NULL,NULL,15),(97,'Guadeloupe','GP',255,'0300',NULL,NULL,15),(98,'Guam','GU',255,'0300',NULL,NULL,15),(99,'Guatemala','GT',255,'0300',NULL,NULL,15),(100,'Guernsey','00',255,'0300',NULL,NULL,15),(101,'Guinea','GN',255,'0300',NULL,NULL,15),(102,'Guinea-Bissau','GW',255,'0300',NULL,NULL,15),(103,'Guyana','GY',255,'0300',NULL,NULL,15),(104,'Haiti','HT',255,'0300',NULL,NULL,15),(105,'Heard Island and McDonald Island','HM',255,'0300',NULL,NULL,15),(106,'Holy See (Vatican City)','VA',255,'0300',NULL,NULL,15),(107,'Honduras','HN',255,'0300',NULL,NULL,15),(108,'Hong Kong','HK',255,'0300',NULL,NULL,15),(109,'Hungary','HU',255,'0300',NULL,NULL,15),(110,'Iceland','IS',255,'0300',NULL,NULL,15),(111,'India','IN',255,'0300',NULL,NULL,15),(112,'Indonesia','ID',255,'0300',NULL,NULL,15),(113,'Iran','IR',255,'0300',NULL,NULL,15),(114,'Iraq','IQ',255,'0300',NULL,NULL,15),(115,'Ireland','IE',255,'0300',NULL,NULL,15),(116,'Isle of Man','00',255,'0300',NULL,NULL,15),(117,'Israel','IL',255,'0300',NULL,NULL,15),(118,'Italy','IT',255,'0300',NULL,NULL,15),(119,'Jamaica','JM',255,'0300',NULL,NULL,15),(120,'Jan Mayen','00',255,'0300',NULL,NULL,15),(121,'Japan','JP',255,'0300',NULL,NULL,15),(122,'Jersey','00',255,'0300',NULL,NULL,15),(123,'Jordan','JO',255,'0300',NULL,NULL,15),(124,'Juan de Nova Island','00',255,'0300',NULL,NULL,15),(125,'Kazakhstan','KZ',255,'0300',NULL,NULL,15),(126,'Kenya','KE',255,'0300',NULL,NULL,15),(127,'Kiribati','KI',255,'0300',NULL,NULL,15),(128,'Korea, North','00',255,'0300',NULL,NULL,15),(129,'Korea, South','00',255,'0300',NULL,NULL,15),(130,'Kuwait','KP',255,'0300',NULL,NULL,15),(131,'Kyrgyzstan','KG',255,'0300',NULL,NULL,15),(132,'Laos','LA',255,'0300',NULL,NULL,15),(133,'Latvia','LV',255,'0300',NULL,NULL,15),(134,'Lebanon','LB',255,'0300',NULL,NULL,15),(135,'Lesotho','LS',255,'0300',NULL,NULL,15),(136,'Liberia','LR',255,'0300',NULL,NULL,15),(137,'Libya','LY',255,'0300',NULL,NULL,15),(138,'Liechtenstein','LI',255,'0300',NULL,NULL,15),(139,'Lithuania','LT',255,'0300',NULL,NULL,15),(140,'Luxembourg','LU',255,'0300',NULL,NULL,15),(141,'Macau','MO',255,'0300',NULL,NULL,15),(142,'Macedonia','MK',255,'0300',NULL,NULL,15),(143,'Madagascar','MG',255,'0300',NULL,NULL,15),(144,'Malawi','MW',255,'0300',NULL,NULL,15),(145,'Malaysia','MY',255,'0300',NULL,NULL,15),(146,'Maldives','MV',255,'0300',NULL,NULL,15),(147,'Mali','ML',255,'0300',NULL,NULL,15),(148,'Malta','MT',255,'0300',NULL,NULL,15),(149,'Marshall Islands','MH',255,'0300',NULL,NULL,15),(150,'Martinique','MQ',255,'0300',NULL,NULL,15),(151,'Mauritania','MR',255,'0300',NULL,NULL,15),(152,'Mauritius','MU',255,'0300',NULL,NULL,15),(153,'Mayotte','YT',255,'0300',NULL,NULL,15),(154,'Mexico','MX',255,'0300',NULL,NULL,15),(155,'Micronesia, Federated States of','FM',255,'0300',NULL,NULL,15),(156,'Moldova','MD',255,'0300',NULL,NULL,15),(157,'Monaco','MC',255,'0300',NULL,NULL,15),(158,'Mongolia','MN',255,'0300',NULL,NULL,15),(159,'Montserrat','MS',255,'0300',NULL,NULL,15),(160,'Morocco','MA',255,'0300',NULL,NULL,15),(161,'Mozambique','MZ',255,'0300',NULL,NULL,15),(162,'Namibia','NA',255,'0300',NULL,NULL,15),(163,'Nauru','NR',255,'0300',NULL,NULL,15),(164,'Navassa Island','00',255,'0300',NULL,NULL,15),(165,'Nepal','NP',255,'0300',NULL,NULL,15),(166,'Netherlands','NL',255,'0300',NULL,NULL,15),(167,'Netherlands Antilles','AN',255,'0300',NULL,NULL,15),(168,'New Caledonia','NC',255,'0300',NULL,NULL,15),(169,'New Zealand','NZ',255,'0300',NULL,NULL,15),(170,'Nicaragua','NI',255,'0300',NULL,NULL,15),(171,'Niger','NE',255,'0300',NULL,NULL,15),(172,'Nigeria','NG',255,'0300',NULL,NULL,15),(173,'Niue','NU',255,'0300',NULL,NULL,15),(174,'Norfolk Island','NF',255,'0300',NULL,NULL,15),(175,'Northern Mariana Islands','MP',255,'0300',NULL,NULL,15),(176,'Norway','NO',255,'0300',NULL,NULL,15),(177,'Oman','OM',255,'0300',NULL,NULL,15),(178,'Pakistan','PK',255,'0300',NULL,NULL,15),(179,'Palau','PW',255,'0300',NULL,NULL,15),(180,'Panama','PA',255,'0300',NULL,NULL,15),(181,'Papua New Guinea','PG',255,'0300',NULL,NULL,15),(182,'Paracel Islands','00',255,'0300',NULL,NULL,15),(183,'Paraguay','PY',255,'0300',NULL,NULL,15),(184,'Peru','PE',255,'0300',NULL,NULL,15),(185,'Philippines','PH',255,'0300',NULL,NULL,15),(186,'Pitcairn Islands','PN',255,'0300',NULL,NULL,15),(187,'Poland','PL',255,'0300',NULL,NULL,15),(188,'Portugal','PT',255,'0300',NULL,NULL,15),(189,'Puerto Rico','PR',255,'0300',NULL,NULL,15),(190,'Qatar','QA',255,'0300',NULL,NULL,15),(191,'Reunion','RE',255,'0300',NULL,NULL,15),(192,'Romania','RO',255,'0300',NULL,NULL,15),(193,'Russia','RU',255,'0300',NULL,NULL,15),(194,'Rwanda','RW',255,'0300',NULL,NULL,15),(195,'Saint Helena','00',255,'0300',NULL,NULL,15),(196,'Saint Kitts and Nevis','KN',255,'0300',NULL,NULL,15),(197,'Saint Lucia','LC',255,'0300',NULL,NULL,15),(198,'Saint Pierre and Miquelon','00',255,'0300',NULL,NULL,15),(199,'Saint Vincent and the Grenadines','VC',255,'0300',NULL,NULL,15),(200,'Samoa','WS',255,'0300',NULL,NULL,15),(201,'San Marino','SM',255,'0300',NULL,NULL,15),(202,'Sao Tome and Principe','ST',255,'0300',NULL,NULL,15),(203,'Saudi Arabia','SA',255,'0300',NULL,NULL,15),(204,'Senegal','SN',255,'0300',NULL,NULL,15),(205,'Serbia and Montenegro','RS',255,'0300',NULL,NULL,15),(206,'Seychelles','SC',255,'0300',NULL,NULL,15),(207,'Sierra Leone','SL',255,'0300',NULL,NULL,15),(208,'Singapore','SG',255,'0300',NULL,NULL,15),(209,'Slovakia','SK',255,'0300',NULL,NULL,15),(210,'Slovenia','SI',255,'0300',NULL,NULL,15),(211,'Solomon Islands','SB',255,'0300',NULL,NULL,15),(212,'Somalia','SO',255,'0300',NULL,NULL,15),(213,'South Africa','ZA',255,'0300',NULL,NULL,15),(214,'South Georgia and the South Sand','GS',255,'0300',NULL,NULL,15),(215,'Spain','ES',255,'0300',NULL,NULL,15),(216,'Spratly Islands','00',255,'0300',NULL,NULL,15),(217,'Sri Lanka','LK',255,'0300',NULL,NULL,15),(218,'Sudan','SD',255,'0300',NULL,NULL,15),(219,'Suriname','SR',255,'0300',NULL,NULL,15),(220,'Svalbard','SJ',255,'0300',NULL,NULL,15),(221,'Swaziland','SZ',255,'0300',NULL,NULL,15),(222,'Sweden','SE',255,'0300',NULL,NULL,15),(223,'Switzerland','CH',255,'0300',NULL,NULL,15),(224,'Syria','SY',255,'0300',NULL,NULL,15),(225,'Taiwan','TW',255,'0300',NULL,NULL,15),(226,'Tajikistan','TJ',255,'0300',NULL,NULL,15),(227,'Tanzania','TZ',255,'0300',NULL,NULL,15),(228,'Thailand','TH',255,'0300',NULL,NULL,15),(229,'Timor-Leste','00',255,'0300',NULL,NULL,15),(230,'Togo','TG',255,'0300',NULL,NULL,15),(231,'Tokelau','TK',255,'0300',NULL,NULL,15),(232,'Tonga','TO',255,'0300',NULL,NULL,15),(233,'Trinidad and Tobago','TT',255,'0300',NULL,NULL,15),(234,'Tromelin Island','00',255,'0300',NULL,NULL,15),(235,'Tunisia','TN',255,'0300',NULL,NULL,15),(236,'Turkey','TR',255,'0300',NULL,NULL,15),(237,'Turkmenistan','TM',255,'0300',NULL,NULL,15),(238,'Turks and Caicos Islands','TC',255,'0300',NULL,NULL,15),(239,'Tuvalu','TV',255,'0300',NULL,NULL,15),(240,'Uganda','UG',255,'0300',NULL,NULL,15),(241,'Ukraine','UA',255,'0300',NULL,NULL,15),(242,'United Arab Emirates','AE',255,'0300',NULL,NULL,15),(243,'United Kingdom','GB',255,'0300',NULL,NULL,15),(244,'United States','US',255,'0300',NULL,NULL,15),(245,'Uruguay','UY',255,'0300',NULL,NULL,15),(246,'Uzbekistan','UZ',255,'0300',NULL,NULL,15),(247,'Vanuatu','VU',255,'0300',NULL,NULL,15),(248,'Venezuela','VE',255,'0300',NULL,NULL,15),(249,'Vietnam','VN',255,'0300',NULL,NULL,15),(250,'Virgin Islands','VI',255,'0300',NULL,NULL,15),(251,'Wake Island','00',255,'0300',NULL,NULL,15),(252,'Wallis and Futuna','WF',255,'0300',NULL,NULL,15),(253,'West Bank','00',255,'0300',NULL,NULL,15),(254,'Western Sahara','EH',255,'0300',NULL,NULL,15),(255,'Yemen','YE',255,'0300',NULL,NULL,15),(256,'Zambia','ZM',255,'0300',NULL,NULL,15),(257,'Zimbabwe','ZW',255,'0300',NULL,NULL,15),(258,'South Sudan','SS',255,'0300',NULL,NULL,15);
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course` (
  `courseId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `courseName` varchar(48) NOT NULL,
  `courseCode` varchar(12) NOT NULL,
  `duration` int(2) unsigned NOT NULL,
  `departmentId` int(10) unsigned NOT NULL,
  `nextRegistrationNumber` int(16) unsigned NOT NULL DEFAULT '1',
  `registrationNumberWidth` int(16) unsigned NOT NULL DEFAULT '4',
  `nextExaminationNumber` int(16) unsigned NOT NULL DEFAULT '1',
  `examinationNumberWidth` int(16) unsigned NOT NULL DEFAULT '4',
  `levelId` int(10) unsigned NOT NULL,
  `minimumPoints` int(4) unsigned NOT NULL DEFAULT '0',
  `maximumPoints` int(4) unsigned NOT NULL DEFAULT '1000',
  `minimumGPABeforeSupplimentary` varchar(8) NOT NULL DEFAULT '0.0',
  `minimumGPAAfterSupplimentary` varchar(8) NOT NULL DEFAULT '0.0',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`courseId`),
  UNIQUE KEY `courseName` (`courseName`),
  UNIQUE KEY `courseCode` (`courseCode`),
  KEY `departmentId` (`departmentId`),
  KEY `levelId` (`levelId`),
  CONSTRAINT `course_ibfk_1` FOREIGN KEY (`departmentId`) REFERENCES `departments` (`departmentId`),
  CONSTRAINT `course_ibfk_2` FOREIGN KEY (`levelId`) REFERENCES `educationLevel` (`levelId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course`
--

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;
INSERT INTO `course` VALUES (1,'Testing Course','TCODE',2,1,1,4,1,4,1,0,1000,'0.0','0.0',NULL,NULL,15);
/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courseAndSubjectTransaction`
--

DROP TABLE IF EXISTS `courseAndSubjectTransaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courseAndSubjectTransaction` (
  `transactionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `courseId` int(10) unsigned NOT NULL,
  `subjectId` int(10) unsigned NOT NULL,
  `lectureHours` varchar(8) NOT NULL DEFAULT '0',
  `seminarAndTutorialHours` varchar(8) NOT NULL DEFAULT '0',
  `assignmentHours` varchar(8) NOT NULL DEFAULT '0',
  `independentStudiesAndResearchHours` varchar(8) NOT NULL DEFAULT '0',
  `practicalTrainingHours` varchar(8) NOT NULL DEFAULT '0',
  `extraHours` varchar(8) NOT NULL DEFAULT '0',
  `creditHours` varchar(8) NOT NULL DEFAULT '0',
  `year` int(2) unsigned NOT NULL,
  `semester` int(2) unsigned NOT NULL,
  `compulsory` tinyint(1) NOT NULL DEFAULT '1',
  `minimumPassMarks` int(4) NOT NULL DEFAULT '40',
  `minimumCourseWork` int(4) NOT NULL DEFAULT '40',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`transactionId`),
  KEY `courseId` (`courseId`),
  KEY `subjectId` (`subjectId`),
  CONSTRAINT `courseAndSubjectTransaction_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`),
  CONSTRAINT `courseAndSubjectTransaction_ibfk_2` FOREIGN KEY (`subjectId`) REFERENCES `subject` (`subjectId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courseAndSubjectTransaction`
--

LOCK TABLES `courseAndSubjectTransaction` WRITE;
/*!40000 ALTER TABLE `courseAndSubjectTransaction` DISABLE KEYS */;
/*!40000 ALTER TABLE `courseAndSubjectTransaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courseInstructors`
--

DROP TABLE IF EXISTS `courseInstructors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courseInstructors` (
  `instructorId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loginId` int(10) unsigned NOT NULL,
  `courseAndSubjectTransactionIdList` varchar(108) DEFAULT NULL,
  `departmentId` int(10) unsigned NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`instructorId`),
  KEY `loginId` (`loginId`),
  KEY `departmentId` (`departmentId`),
  CONSTRAINT `courseInstructors_ibfk_1` FOREIGN KEY (`loginId`) REFERENCES `login` (`loginId`),
  CONSTRAINT `courseInstructors_ibfk_2` FOREIGN KEY (`departmentId`) REFERENCES `departments` (`departmentId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courseInstructors`
--

LOCK TABLES `courseInstructors` WRITE;
/*!40000 ALTER TABLE `courseInstructors` DISABLE KEYS */;
/*!40000 ALTER TABLE `courseInstructors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coursePointLimits`
--

DROP TABLE IF EXISTS `coursePointLimits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coursePointLimits` (
  `limitId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `courseId` int(10) unsigned NOT NULL,
  `_year` int(2) unsigned NOT NULL DEFAULT '0',
  `_semester` int(2) unsigned NOT NULL DEFAULT '0',
  `minimumPoints` int(4) unsigned NOT NULL DEFAULT '0',
  `maximumPoints` int(4) unsigned NOT NULL DEFAULT '0',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`limitId`),
  KEY `courseId` (`courseId`),
  CONSTRAINT `coursePointLimits_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coursePointLimits`
--

LOCK TABLES `coursePointLimits` WRITE;
/*!40000 ALTER TABLE `coursePointLimits` DISABLE KEYS */;
/*!40000 ALTER TABLE `coursePointLimits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency` (
  `currencyId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `currencyName` varchar(48) NOT NULL,
  `currencyCode` varchar(12) NOT NULL,
  `countryId` int(10) unsigned NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`currencyId`),
  UNIQUE KEY `currencyName` (`currencyName`),
  KEY `countryId` (`countryId`),
  CONSTRAINT `currency_ibfk_1` FOREIGN KEY (`countryId`) REFERENCES `country` (`countryId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currency`
--

LOCK TABLES `currency` WRITE;
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;
/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daysOfAWeek`
--

DROP TABLE IF EXISTS `daysOfAWeek`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daysOfAWeek` (
  `dayId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dayName` varchar(9) NOT NULL,
  `dayAbbreviation` varchar(4) NOT NULL,
  `offsetValue` int(1) NOT NULL DEFAULT '0',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`dayId`),
  UNIQUE KEY `dayName` (`dayName`),
  UNIQUE KEY `dayAbbreviation` (`dayAbbreviation`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daysOfAWeek`
--

LOCK TABLES `daysOfAWeek` WRITE;
/*!40000 ALTER TABLE `daysOfAWeek` DISABLE KEYS */;
INSERT INTO `daysOfAWeek` VALUES (1,'Sunday','Sun',0,NULL,NULL,15),(2,'Monday','Mon',1,NULL,NULL,15),(3,'Tuesday','Tue',2,NULL,NULL,15),(4,'Wednesday','Wed',3,NULL,NULL,15),(5,'Thursday','Thur',4,NULL,NULL,15),(6,'Friday','Fri',5,NULL,NULL,15),(7,'Saturday','Sat',6,NULL,NULL,15);
/*!40000 ALTER TABLE `daysOfAWeek` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `denomination`
--

DROP TABLE IF EXISTS `denomination`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `denomination` (
  `denominationId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `denominationName` varchar(48) NOT NULL,
  `religionId` int(10) unsigned NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`denominationId`),
  KEY `religionId` (`religionId`),
  CONSTRAINT `denomination_ibfk_1` FOREIGN KEY (`religionId`) REFERENCES `religion` (`religionId`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `denomination`
--

LOCK TABLES `denomination` WRITE;
/*!40000 ALTER TABLE `denomination` DISABLE KEYS */;
INSERT INTO `denomination` VALUES (1,'African Methodist Episcopal Zion ',1,NULL,NULL,15),(2,'African Orthodox Church ',1,NULL,NULL,15),(3,'American Baptist Churches USA ',1,NULL,NULL,15),(4,'Amish ',1,NULL,NULL,15),(5,'Anabaptist ',1,NULL,NULL,15),(6,'Anglican Catholic Church',1,NULL,NULL,15),(7,'Anglican Church ',1,NULL,NULL,15),(8,'Antiochian Orthodox',1,NULL,NULL,15),(9,'Armenian Evangelical Church ',1,NULL,NULL,15),(10,'Armenian Orthodox',1,NULL,NULL,15),(11,'Assemblies of God ',1,NULL,NULL,15),(12,'Associated Gospel Churches of Canada',1,NULL,NULL,15),(13,'Association of Vineyard Churches ',1,NULL,NULL,15),(14,'Baptist ',1,NULL,NULL,15),(15,'Baptist Bible Fellowship ',1,NULL,NULL,15),(16,'Branch Davidian ',1,NULL,NULL,15),(17,'Brethren in Christ ',1,NULL,NULL,15),(18,'Bruderhof Communities ',1,NULL,NULL,15),(19,'Byzantine Catholic Church',1,NULL,NULL,15),(20,'Calvary Chapel ',1,NULL,NULL,15),(21,'Calvinist ',1,NULL,NULL,15),(22,'Catholic ',1,NULL,NULL,15),(23,'Cell Church ',1,NULL,NULL,15),(24,'Celtic Orthodox',1,NULL,NULL,15),(25,'Charismatic Episcopal Church ',1,NULL,NULL,15),(26,'Christadelphian ',1,NULL,NULL,15),(27,'Christian and Missionary Alliance ',1,NULL,NULL,15),(28,'Christian Churches of God ',1,NULL,NULL,15),(29,'Christian Identity ',1,NULL,NULL,15),(30,'Christian Reformed Church ',1,NULL,NULL,15),(31,'Christian Science ',1,NULL,NULL,15),(32,'Church of God (Anderson) ',1,NULL,NULL,15),(33,'Church of God (Cleveland) ',1,NULL,NULL,15),(34,'Church of God (Seventh Day) ',1,NULL,NULL,15),(35,'Church of God in Christ ',1,NULL,NULL,15),(36,'Church of God of Prophecy ',1,NULL,NULL,15),(37,'Church of Jesus Christ of Latter-day Saints ',1,NULL,NULL,15),(38,'Church of Scotland',1,NULL,NULL,15),(39,'Church of South India ',1,NULL,NULL,15),(40,'Church of the Brethren ',1,NULL,NULL,15),(41,'Church of the Lutheran Brethren of America ',1,NULL,NULL,15),(42,'Church of the Nazarene ',1,NULL,NULL,15),(43,'Church of the New Jerusalem ',1,NULL,NULL,15),(44,'Church of the United Brethren in Christ ',1,NULL,NULL,15),(45,'Church Universal and Triumphant ',1,NULL,NULL,15),(46,'Churches of Christ ',1,NULL,NULL,15),(47,'Churches of God General Conference ',1,NULL,NULL,15),(48,'Congregational Christian Churches ',1,NULL,NULL,15),(49,'Coptic Orthodox',1,NULL,NULL,15),(50,'Cumberland Presbyterian Church ',1,NULL,NULL,15),(51,'Disciples of Christ ',1,NULL,NULL,15),(52,'Episcopal Church',1,NULL,NULL,15),(53,'Ethiopian Orthodox Tewahedo Church ',1,NULL,NULL,15),(54,'Evangelical Congregational Church ',1,NULL,NULL,15),(55,'Evangelical Covenant Church ',1,NULL,NULL,15),(56,'Evangelical Formosan Church ',1,NULL,NULL,15),(57,'Evangelical Free Church ',1,NULL,NULL,15),(58,'Evangelical Lutheran Church ',1,NULL,NULL,15),(59,'Evangelical Methodist Church ',1,NULL,NULL,15),(60,'Evangelical Presbyterian ',1,NULL,NULL,15),(61,'Family, The (aka Children of God) ',1,NULL,NULL,15),(62,'Fellowship of Christian Assemblies ',1,NULL,NULL,15),(63,'Fellowship of Grace Brethren ',1,NULL,NULL,15),(64,'Fellowship of Independent Evangelical Churches ',1,NULL,NULL,15),(65,'Free Church of Scotland ',1,NULL,NULL,15),(66,'Free Methodist ',1,NULL,NULL,15),(67,'Free Presbyterian ',1,NULL,NULL,15),(68,'Free Will Baptist ',1,NULL,NULL,15),(69,'Gnostic',1,NULL,NULL,15),(70,'Great Commission Association of Churches ',1,NULL,NULL,15),(71,'Greek Orthodox',1,NULL,NULL,15),(72,'Hutterian Brethren ',1,NULL,NULL,15),(73,'Independent Fundamental Churches of America ',1,NULL,NULL,15),(74,'Indian Orthodox',1,NULL,NULL,15),(75,'International Church of the Foursquare Gospel ',1,NULL,NULL,15),(76,'International Churches of Christ ',1,NULL,NULL,15),(77,'Jehovah\'s Witnesses ',1,NULL,NULL,15),(78,'Living Church of God ',1,NULL,NULL,15),(79,'Local Church ',1,NULL,NULL,15),(80,'Lutheran ',1,NULL,NULL,15),(81,'Lutheran Church - Missouri Synod ',1,NULL,NULL,15),(82,'Mar Thoma Syrian Church ',1,NULL,NULL,15),(83,'Mennonite ',1,NULL,NULL,15),(84,'Messianic Judaism',1,NULL,NULL,15),(85,'Methodist ',1,NULL,NULL,15),(86,'Moravian Church ',1,NULL,NULL,15),(87,'Nation of Yahweh ',1,NULL,NULL,15),(88,'New Frontiers International ',1,NULL,NULL,15),(89,'Old Catholic Church',1,NULL,NULL,15),(90,'Orthodox ',1,NULL,NULL,15),(91,'Orthodox Church in America',1,NULL,NULL,15),(92,'Orthodox Presbyterian ',1,NULL,NULL,15),(93,'Pentecostal ',1,NULL,NULL,15),(94,'Plymouth Brethren ',1,NULL,NULL,15),(95,'Presbyterian ',1,NULL,NULL,15),(96,'Presbyterian Church (USA) ',1,NULL,NULL,15),(97,'Presbyterian Church in America ',1,NULL,NULL,15),(98,'Primitive Baptist ',1,NULL,NULL,15),(99,'Protestant Reformed Church ',1,NULL,NULL,15),(100,'Reformed ',1,NULL,NULL,15),(101,'Reformed Baptist ',1,NULL,NULL,15),(102,'Reformed Church in America ',1,NULL,NULL,15),(103,'Reformed Church in the United States ',1,NULL,NULL,15),(104,'Reformed Churches of Australia',1,NULL,NULL,15),(105,'Reformed Episcopal Church',1,NULL,NULL,15),(106,'Reformed Presbyterian Church ',1,NULL,NULL,15),(107,'Reorganized Church of Jesus Christ of Latter Day',1,NULL,NULL,15),(108,'Revival Centres International ',1,NULL,NULL,15),(109,'Romanian Orthodox',1,NULL,NULL,15),(110,'Rosicrucian',1,NULL,NULL,15),(111,'Russian Orthodox',1,NULL,NULL,15),(112,'Serbian Orthodox',1,NULL,NULL,15),(113,'Seventh Day Baptist ',1,NULL,NULL,15),(114,'Seventh-Day Adventist ',1,NULL,NULL,15),(115,'Shaker ',1,NULL,NULL,15),(116,'Society of Friends ',1,NULL,NULL,15),(117,'Southern Baptist Convention ',1,NULL,NULL,15),(118,'Spiritist ',1,NULL,NULL,15),(119,'Syrian Orthodox',1,NULL,NULL,15),(120,'True and Living Church of Jesus Christ of Saints',1,NULL,NULL,15),(121,'Two-by-Twos ',1,NULL,NULL,15),(122,'Unification Church ',1,NULL,NULL,15),(123,'Unitarian-Universalism',1,NULL,NULL,15),(124,'United Church of Canada',1,NULL,NULL,15),(125,'United Church of Christ ',1,NULL,NULL,15),(126,'United Church of God ',1,NULL,NULL,15),(127,'United Free Church of Scotland',1,NULL,NULL,15),(128,'United Methodist Church ',1,NULL,NULL,15),(129,'United Reformed Church ',1,NULL,NULL,15),(130,'Uniting Church in Australia',1,NULL,NULL,15),(131,'Unity Church ',1,NULL,NULL,15),(132,'Unity Fellowship Church ',1,NULL,NULL,15),(133,'Universal Fellowship of Metropolitan Community C',1,NULL,NULL,15),(134,'Virtual Churches ',1,NULL,NULL,15),(135,'Waldensian Church ',1,NULL,NULL,15),(136,'Way International, The ',1,NULL,NULL,15),(137,'Web Directories ',1,NULL,NULL,15),(138,'Wesleyan ',1,NULL,NULL,15),(139,'Wesleyan Methodist',1,NULL,NULL,15),(140,'Worldwide Church of God',1,NULL,NULL,15),(141,'Others',1,NULL,NULL,15),(142,'Hanafi-Sunni',2,NULL,NULL,15),(143,'Hanbali-Sunni',2,NULL,NULL,15),(144,'Maliki-Sunni',2,NULL,NULL,15),(145,'ShafiÃ­-Sunni',2,NULL,NULL,15),(146,'Sunni',2,NULL,NULL,15),(147,'Qaramita-Sevener-Isma\'Ã­lism Shi\'ah',2,NULL,NULL,15),(148,'Sevener-Isma\'ilism Shi\'ah',2,NULL,NULL,15),(149,'Druze-Nizari-Isma\'ilism Shi\'ah',2,NULL,NULL,15),(150,'Nizari-Isma\'ilism Shi\'ah',2,NULL,NULL,15),(151,'Dawoodi Bohras-Tayyibi',2,NULL,NULL,15),(152,'Jafan Bohras-Tayyibi',2,NULL,NULL,15),(153,'Sulaimani Bohras-Tayyibi',2,NULL,NULL,15),(154,'Alavi Bohras-Tayyibi',2,NULL,NULL,15),(155,'Hebitahs Bohras-Tayyibi',2,NULL,NULL,15),(156,'Atba-I Malak Bohras-Tayyibi',2,NULL,NULL,15),(157,'Progressive Dawoodi Bohras-Tayyibi',2,NULL,NULL,15),(158,'Tayyibi-Musta\'li-Isma\'ilism Shi\'ah',2,NULL,NULL,15),(159,'Musta\'li-Isma\'ilism Shi\'ah',2,NULL,NULL,15),(160,'Isma\'ilism Shi\'ah',2,NULL,NULL,15),(161,'Shaykhi-Akbari-Twelver-Jafri-Shi\'ah',2,NULL,NULL,15),(162,'Akbari-Twelver-Jafri-Shi\'ah',2,NULL,NULL,15),(163,'Usuli-Twelver-Jafri-Shi\'ah',2,NULL,NULL,15),(164,'\'Alawi-Jafri-Shi\'ah',2,NULL,NULL,15),(165,'Alevi-Jafri-Shi\'ah',2,NULL,NULL,15),(166,'Zaidiyyah-Shi\'ah',2,NULL,NULL,15),(167,'Shi\'ah',2,NULL,NULL,15),(168,'Ibadiyya-Khawarij',2,NULL,NULL,15),(169,'Khawarij',2,NULL,NULL,15),(170,'Bektashi-Sufi',2,NULL,NULL,15),(171,'Chishti-Sufi',2,NULL,NULL,15),(172,'Mawlawi-Sufi',2,NULL,NULL,15),(173,'Naqshbandi-Sufi',2,NULL,NULL,15),(174,'Oveyssi-Sufi',2,NULL,NULL,15),(175,'Qudiriyyah-Sufi',2,NULL,NULL,15),(176,'Suhrawardiyya-Sufi',2,NULL,NULL,15),(177,'Tijaniyyah-Sufi',2,NULL,NULL,15),(178,'Muridiyya-Sufi',2,NULL,NULL,15),(179,'Shadhil-Sufi',2,NULL,NULL,15),(180,'Sufi',2,NULL,NULL,15),(181,'Others',2,NULL,NULL,15),(182,'Buddhist',3,NULL,NULL,15),(183,'Vaishnavism/Vishnuism',4,NULL,NULL,15),(184,'Shaivism/Shivaism',4,NULL,NULL,15),(185,'Shaktism',4,NULL,NULL,15),(186,'Smartism',4,NULL,NULL,15),(187,'Shrautism',4,NULL,NULL,15),(188,'Suryaism/Saurism',4,NULL,NULL,15),(189,'Ganapatism',4,NULL,NULL,15),(190,'Indonesia Hinduism',4,NULL,NULL,15),(191,'Others',4,NULL,NULL,15),(192,'Ethinic/Indigeneous',5,NULL,NULL,15),(193,'Indigeneous',6,NULL,NULL,15),(194,'Judaism',7,NULL,NULL,15),(195,'Baha\'i',8,NULL,NULL,15),(196,'Irreligious and Atheist',9,NULL,NULL,15),(197,'Sikhism',10,NULL,NULL,15),(198,'Taoists/Confucianists/Chinese tradional religion',11,NULL,NULL,15),(199,'Jainism',12,NULL,NULL,15),(200,'Mormonism',13,NULL,NULL,15),(201,'Spiritism',14,NULL,NULL,15),(202,'Others',15,NULL,NULL,15),(203,'Bareivi',2,NULL,NULL,15),(204,'Deoband movement',2,NULL,NULL,15),(205,'Roman Catholic',1,NULL,NULL,15),(206,'Infinity Glory Ministries',1,NULL,NULL,15);
/*!40000 ALTER TABLE `denomination` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `departmentId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `departmentName` varchar(48) NOT NULL,
  `departmentCode` varchar(12) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`departmentId`),
  UNIQUE KEY `departmentName` (`departmentName`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Testing Department','TESTCODE',NULL,NULL,15);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `educationLevel`
--

DROP TABLE IF EXISTS `educationLevel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `educationLevel` (
  `levelId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `levelName` varchar(48) NOT NULL,
  `levelCode` int(2) NOT NULL,
  `indexNumberExpression` varchar(64) NOT NULL DEFAULT '^(\\w|\\W){2,64}$',
  `indexNumberMessage` varchar(64) NOT NULL DEFAULT '2 to 64 characters',
  `gradeEarnedExpression` varchar(64) NOT NULL DEFAULT '^(\\w|\\W){2,64}$',
  `gradeEarnedMessage` varchar(64) NOT NULL DEFAULT '2 to 64 characters',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`levelId`),
  UNIQUE KEY `levelName` (`levelName`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `educationLevel`
--

LOCK TABLES `educationLevel` WRITE;
/*!40000 ALTER TABLE `educationLevel` DISABLE KEYS */;
INSERT INTO `educationLevel` VALUES (1,'Certificate of Primary Education',1,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(2,'National Vocational Certificate I',2,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(3,'National Vocational Certificate II',3,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(4,'Certificate of Secondary Education',4,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(5,'Basic Technician Certificate (NTA Level 4)',4,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(6,'National Vocational Certificate III',4,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(7,'Professional Technicial Level I Certificate',4,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(8,'Advanced Certificate of Secondary Education',5,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(9,'Technician Certificate (NTA Level 5)',5,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(10,'Professional Technician Level II Certificate',5,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(11,'Professional Level I Certificate',6,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(12,'Ordinary Diploma (NTA Level 6)',6,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(13,'Higher Diploma',7,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(14,'Professional Level II Certificate',7,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(15,'Bachelor Degree',8,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(16,'Professional Level III Certificate',8,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(17,'Masters Degree',9,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(18,'Postgraduate Diploma',9,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(19,'Postgraduate Certificate',9,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(20,'Professional Level IV Certificate',9,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15),(21,'Doctorate Degree',10,'^(w|W){2,64}$','2 to 64 characters','^(\\w|\\W){2,64}$','2 to 64 characters',NULL,NULL,15);
/*!40000 ALTER TABLE `educationLevel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entryCriteria`
--

DROP TABLE IF EXISTS `entryCriteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entryCriteria` (
  `criteriaId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `courseId` int(10) unsigned NOT NULL,
  `criteriaList` varchar(108) DEFAULT NULL,
  `exclusionCriteriaList` varchar(108) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`criteriaId`),
  UNIQUE KEY `courseId` (`courseId`),
  CONSTRAINT `entryCriteria_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entryCriteria`
--

LOCK TABLES `entryCriteria` WRITE;
/*!40000 ALTER TABLE `entryCriteria` DISABLE KEYS */;
/*!40000 ALTER TABLE `entryCriteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examination`
--

DROP TABLE IF EXISTS `examination`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `examination` (
  `examinationId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `examinationName` varchar(48) NOT NULL,
  `examinationCode` varchar(12) NOT NULL,
  `examinationWeight` int(2) unsigned NOT NULL DEFAULT '1',
  `groupId` int(10) unsigned NOT NULL,
  `semester` int(2) NOT NULL DEFAULT '0',
  `isprimary` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `supplimentary` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `minimumScorePercentage` int(4) NOT NULL DEFAULT '0',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`examinationId`),
  UNIQUE KEY `examinationCode` (`examinationCode`,`groupId`),
  KEY `groupId` (`groupId`),
  CONSTRAINT `examination_ibfk_1` FOREIGN KEY (`groupId`) REFERENCES `examinationGroup` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examination`
--

LOCK TABLES `examination` WRITE;
/*!40000 ALTER TABLE `examination` DISABLE KEYS */;
/*!40000 ALTER TABLE `examination` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examinationGroup`
--

DROP TABLE IF EXISTS `examinationGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `examinationGroup` (
  `groupId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(48) NOT NULL,
  `levelId` int(10) unsigned NOT NULL,
  `semester` int(2) NOT NULL DEFAULT '0',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`groupId`),
  KEY `examinationGroup_ibfk_1` (`levelId`),
  CONSTRAINT `examinationGroup_ibfk_1` FOREIGN KEY (`levelId`) REFERENCES `educationLevel` (`levelId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examinationGroup`
--

LOCK TABLES `examinationGroup` WRITE;
/*!40000 ALTER TABLE `examinationGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `examinationGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examinationNumber`
--

DROP TABLE IF EXISTS `examinationNumber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `examinationNumber` (
  `numberId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `examinationId` int(10) unsigned DEFAULT NULL,
  `examinationGroupId` int(10) unsigned DEFAULT NULL,
  `studentId` int(10) unsigned DEFAULT NULL,
  `scopeId` int(10) unsigned DEFAULT NULL,
  `examinationNumber` varchar(64) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`numberId`),
  KEY `examinationId` (`examinationId`),
  KEY `examinationGroupId` (`examinationGroupId`),
  KEY `studentId` (`studentId`),
  KEY `scopeId` (`scopeId`),
  CONSTRAINT `examinationNumber_ibfk_1` FOREIGN KEY (`examinationId`) REFERENCES `examination` (`examinationId`),
  CONSTRAINT `examinationNumber_ibfk_2` FOREIGN KEY (`examinationGroupId`) REFERENCES `examinationGroup` (`groupId`),
  CONSTRAINT `examinationNumber_ibfk_3` FOREIGN KEY (`studentId`) REFERENCES `students` (`studentId`),
  CONSTRAINT `examinationNumber_ibfk_4` FOREIGN KEY (`scopeId`) REFERENCES `examinationNumberScope` (`scopeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examinationNumber`
--

LOCK TABLES `examinationNumber` WRITE;
/*!40000 ALTER TABLE `examinationNumber` DISABLE KEYS */;
/*!40000 ALTER TABLE `examinationNumber` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examinationNumberScope`
--

DROP TABLE IF EXISTS `examinationNumberScope`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `examinationNumberScope` (
  `scopeId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scopeName` varchar(24) NOT NULL,
  `scopeCode` int(2) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`scopeId`),
  UNIQUE KEY `scopeName` (`scopeName`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examinationNumberScope`
--

LOCK TABLES `examinationNumberScope` WRITE;
/*!40000 ALTER TABLE `examinationNumberScope` DISABLE KEYS */;
INSERT INTO `examinationNumberScope` VALUES (1,'System',1,NULL,NULL,15),(2,'Group',2,NULL,NULL,15),(3,'Examination',3,NULL,NULL,15);
/*!40000 ALTER TABLE `examinationNumberScope` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feeInvoiceGroup`
--

DROP TABLE IF EXISTS `feeInvoiceGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feeInvoiceGroup` (
  `groupId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupName` varchar(48) NOT NULL,
  `courseId` int(10) unsigned NOT NULL,
  `_year` int(2) unsigned NOT NULL DEFAULT '0',
  `generationTime` varchar(19) NOT NULL,
  `structureIdList` varchar(108) NOT NULL,
  `currencyId` int(10) unsigned NOT NULL,
  `invoicePrefix` varchar(24) NOT NULL,
  `paymentPrefix` varchar(24) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`groupId`),
  UNIQUE KEY `invoicePrefix` (`invoicePrefix`),
  UNIQUE KEY `paymentPrefix` (`paymentPrefix`),
  KEY `currencyId` (`currencyId`),
  KEY `courseId` (`courseId`),
  CONSTRAINT `feeInvoiceGroup_ibfk_1` FOREIGN KEY (`currencyId`) REFERENCES `currency` (`currencyId`),
  CONSTRAINT `feeInvoiceGroup_ibfk_2` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feeInvoiceGroup`
--

LOCK TABLES `feeInvoiceGroup` WRITE;
/*!40000 ALTER TABLE `feeInvoiceGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `feeInvoiceGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feeInvoices`
--

DROP TABLE IF EXISTS `feeInvoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feeInvoices` (
  `invoiceId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoiceNumber` varchar(48) NOT NULL,
  `groupId` int(10) unsigned NOT NULL,
  `studentId` int(10) unsigned NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`invoiceId`),
  UNIQUE KEY `groupId_2` (`groupId`,`studentId`),
  KEY `groupId` (`groupId`),
  KEY `studentId` (`studentId`),
  CONSTRAINT `feeInvoices_ibfk_1` FOREIGN KEY (`groupId`) REFERENCES `feeInvoiceGroup` (`groupId`),
  CONSTRAINT `feeInvoices_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `students` (`studentId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feeInvoices`
--

LOCK TABLES `feeInvoices` WRITE;
/*!40000 ALTER TABLE `feeInvoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `feeInvoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feePayer`
--

DROP TABLE IF EXISTS `feePayer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feePayer` (
  `payerId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payerName` varchar(48) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`payerId`),
  UNIQUE KEY `payerName` (`payerName`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feePayer`
--

LOCK TABLES `feePayer` WRITE;
/*!40000 ALTER TABLE `feePayer` DISABLE KEYS */;
INSERT INTO `feePayer` VALUES (1,'Self',NULL,NULL,15),(2,'Loan Board',NULL,NULL,15),(3,'Parent/Guardian',NULL,NULL,15),(4,'Accademic Institution',NULL,NULL,15),(5,'Other Institutions',NULL,NULL,15),(6,'Friend',NULL,NULL,15),(7,'Relative',NULL,NULL,15),(8,'Non Profit Organisation',NULL,NULL,15),(9,'Company',NULL,NULL,15),(10,'Employer',NULL,NULL,15),(11,'Others',NULL,NULL,15);
/*!40000 ALTER TABLE `feePayer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feePayments`
--

DROP TABLE IF EXISTS `feePayments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feePayments` (
  `paymentId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoiceId` int(10) unsigned NOT NULL,
  `payerId` int(10) unsigned NOT NULL,
  `paymentNumber` varchar(48) NOT NULL,
  `paymentTime` varchar(19) NOT NULL,
  `amount` varchar(24) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`paymentId`),
  UNIQUE KEY `paymentNumber` (`paymentNumber`),
  KEY `invoiceId` (`invoiceId`),
  KEY `payerId` (`payerId`),
  CONSTRAINT `feePayments_ibfk_1` FOREIGN KEY (`invoiceId`) REFERENCES `feeInvoices` (`invoiceId`),
  CONSTRAINT `feePayments_ibfk_2` FOREIGN KEY (`payerId`) REFERENCES `feePayer` (`payerId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feePayments`
--

LOCK TABLES `feePayments` WRITE;
/*!40000 ALTER TABLE `feePayments` DISABLE KEYS */;
/*!40000 ALTER TABLE `feePayments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feeStructure`
--

DROP TABLE IF EXISTS `feeStructure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feeStructure` (
  `structureId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `courseId` int(10) unsigned NOT NULL,
  `year` int(2) NOT NULL,
  `validFrom` varchar(19) NOT NULL,
  `amount` varchar(24) NOT NULL,
  `currencyId` int(10) unsigned NOT NULL,
  `citizen` tinyint(1) unsigned NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`structureId`),
  KEY `courseId` (`courseId`),
  KEY `currencyId` (`currencyId`),
  CONSTRAINT `feeStructure_ibfk_1` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`),
  CONSTRAINT `feeStructure_ibfk_2` FOREIGN KEY (`currencyId`) REFERENCES `currency` (`currencyId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feeStructure`
--

LOCK TABLES `feeStructure` WRITE;
/*!40000 ALTER TABLE `feeStructure` DISABLE KEYS */;
/*!40000 ALTER TABLE `feeStructure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grades` (
  `gradeId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gradeName` varchar(4) NOT NULL,
  `gradePoint` int(2) NOT NULL DEFAULT '1',
  `levelId` int(10) unsigned NOT NULL,
  `lowestMarks` int(4) NOT NULL,
  `highestMarks` int(4) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`gradeId`),
  KEY `levelId` (`levelId`),
  CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`levelId`) REFERENCES `educationLevel` (`levelId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grades`
--

LOCK TABLES `grades` WRITE;
/*!40000 ALTER TABLE `grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `groupId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staticGroupId` int(10) unsigned DEFAULT NULL,
  `groupName` varchar(80) NOT NULL,
  `pId` int(10) unsigned DEFAULT NULL,
  `year` int(2) unsigned NOT NULL DEFAULT '0',
  `semester` int(2) unsigned NOT NULL DEFAULT '0',
  `minimumPoints` int(4) unsigned NOT NULL DEFAULT '10',
  `courseId` int(10) unsigned DEFAULT NULL,
  `context` text NOT NULL,
  `rootGroup` tinyint(1) NOT NULL DEFAULT '0',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`groupId`),
  UNIQUE KEY `groupName` (`groupName`),
  KEY `pId` (`pId`),
  KEY `courseId` (`courseId`),
  CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`pId`) REFERENCES `groups` (`groupId`),
  CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,NULL,'System',NULL,0,0,10,NULL,'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',1,NULL,NULL,15),(2,NULL,'Student',1,0,0,10,NULL,'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',1,NULL,NULL,15),(3,NULL,'Graduation',1,0,0,10,NULL,'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',1,NULL,NULL,15);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holiday`
--

DROP TABLE IF EXISTS `holiday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holiday` (
  `holidayId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `holidayName` varchar(48) NOT NULL,
  `holidayTime` varchar(19) NOT NULL DEFAULT '*:12:09:*:*:*',
  `textColor` varchar(6) NOT NULL DEFAULT '000000',
  `backgroundColor` varchar(6) NOT NULL DEFAULT 'ffffff',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`holidayId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holiday`
--

LOCK TABLES `holiday` WRITE;
/*!40000 ALTER TABLE `holiday` DISABLE KEYS */;
/*!40000 ALTER TABLE `holiday` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobTitle`
--

DROP TABLE IF EXISTS `jobTitle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobTitle` (
  `jobId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jobName` varchar(48) NOT NULL,
  `context` text NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`jobId`),
  UNIQUE KEY `jobName` (`jobName`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobTitle`
--

LOCK TABLES `jobTitle` WRITE;
/*!40000 ALTER TABLE `jobTitle` DISABLE KEYS */;
INSERT INTO `jobTitle` VALUES (1,'System Administrator','XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',NULL,NULL,31),(2,'Student','XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',NULL,NULL,31);
/*!40000 ALTER TABLE `jobTitle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaveApplication`
--

DROP TABLE IF EXISTS `leaveApplication`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaveApplication` (
  `leaveId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loginId` int(10) unsigned NOT NULL,
  `groupId` int(10) unsigned NOT NULL,
  `leaveName` varchar(48) NOT NULL,
  `timeOfApplication` varchar(19) DEFAULT NULL,
  `leaveApprovalSchemaList` varchar(108) DEFAULT NULL,
  `timeOfApproval` varchar(19) DEFAULT NULL,
  `startTime` varchar(19) DEFAULT NULL,
  `endTime` varchar(19) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`leaveId`),
  KEY `loginId` (`loginId`),
  KEY `groupId` (`groupId`),
  CONSTRAINT `leaveApplication_ibfk_1` FOREIGN KEY (`loginId`) REFERENCES `login` (`loginId`),
  CONSTRAINT `leaveApplication_ibfk_2` FOREIGN KEY (`groupId`) REFERENCES `leaveApprovalSchemaGroup` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaveApplication`
--

LOCK TABLES `leaveApplication` WRITE;
/*!40000 ALTER TABLE `leaveApplication` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaveApplication` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaveApprovalSchema`
--

DROP TABLE IF EXISTS `leaveApprovalSchema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaveApprovalSchema` (
  `schemaId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `schemaName` varchar(48) NOT NULL,
  `groupId` int(10) unsigned NOT NULL,
  `sequenceNumber` int(2) NOT NULL DEFAULT '1',
  `jobId` int(10) unsigned NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`schemaId`),
  KEY `groupId` (`groupId`),
  KEY `jobId` (`jobId`),
  CONSTRAINT `leaveApprovalSchema_ibfk_1` FOREIGN KEY (`groupId`) REFERENCES `leaveApprovalSchemaGroup` (`groupId`),
  CONSTRAINT `leaveApprovalSchema_ibfk_2` FOREIGN KEY (`jobId`) REFERENCES `jobTitle` (`jobId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaveApprovalSchema`
--

LOCK TABLES `leaveApprovalSchema` WRITE;
/*!40000 ALTER TABLE `leaveApprovalSchema` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaveApprovalSchema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaveApprovalSchemaGroup`
--

DROP TABLE IF EXISTS `leaveApprovalSchemaGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaveApprovalSchemaGroup` (
  `groupId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jobId` int(10) unsigned NOT NULL,
  `typeId` int(10) unsigned NOT NULL,
  `groupName` varchar(48) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`groupId`),
  UNIQUE KEY `groupName` (`groupName`),
  UNIQUE KEY `jobId` (`jobId`,`typeId`),
  KEY `typeId` (`typeId`),
  CONSTRAINT `leaveApprovalSchemaGroup_ibfk_1` FOREIGN KEY (`jobId`) REFERENCES `jobTitle` (`jobId`),
  CONSTRAINT `leaveApprovalSchemaGroup_ibfk_2` FOREIGN KEY (`typeId`) REFERENCES `leaveType` (`typeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaveApprovalSchemaGroup`
--

LOCK TABLES `leaveApprovalSchemaGroup` WRITE;
/*!40000 ALTER TABLE `leaveApprovalSchemaGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaveApprovalSchemaGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaveType`
--

DROP TABLE IF EXISTS `leaveType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaveType` (
  `typeId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `typeName` varchar(48) NOT NULL,
  `minDaysNotice` int(2) NOT NULL,
  `maxDaysAllowedPerYear` int(3) NOT NULL,
  `maxContinuousDaysPerLeave` int(3) NOT NULL,
  `numberOfTimesThisLeaveCanBeTakenInAYear` int(2) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`typeId`),
  UNIQUE KEY `typeName` (`typeName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaveType`
--

LOCK TABLES `leaveType` WRITE;
/*!40000 ALTER TABLE `leaveType` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaveType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login` (
  `loginId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loginName` varchar(64) DEFAULT NULL,
  `firstname` varchar(32) DEFAULT NULL,
  `middlename` varchar(32) DEFAULT NULL,
  `lastname` varchar(32) DEFAULT NULL,
  `fullname` varchar(64) DEFAULT NULL,
  `statusId` int(10) unsigned DEFAULT NULL,
  `dob` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `sexId` int(10) unsigned DEFAULT NULL,
  `maritalId` int(10) unsigned DEFAULT NULL,
  `jobId` int(10) unsigned DEFAULT NULL,
  `groupId` int(10) unsigned DEFAULT NULL,
  `context` text NOT NULL,
  `typeId` int(10) unsigned DEFAULT NULL,
  `email` varchar(32) DEFAULT NULL,
  `phone` varchar(16) DEFAULT NULL,
  `photo` varchar(64) DEFAULT NULL,
  `password` char(40) DEFAULT NULL,
  `root` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lastLoginTime` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `admissionTime` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `qnSecurity1` int(10) unsigned DEFAULT NULL,
  `ansSecurity1` varchar(32) DEFAULT NULL,
  `qnSecurity2` int(10) unsigned DEFAULT NULL,
  `ansSecurity2` varchar(32) DEFAULT NULL,
  `themeId` int(10) unsigned DEFAULT NULL,
  `firstDayOfAWeekId` int(10) unsigned DEFAULT NULL,
  `revisionNumber` int(8) unsigned NOT NULL DEFAULT '1',
  `revisionTime` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `admittedBy` varchar(48) DEFAULT NULL,
  `randomNumber` char(32) DEFAULT NULL,
  `virtualizationList` varchar(108) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`loginId`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `loginName` (`loginName`),
  KEY `statusId` (`statusId`),
  KEY `sexId` (`sexId`),
  KEY `maritalId` (`maritalId`),
  KEY `jobId` (`jobId`),
  KEY `groupId` (`groupId`),
  KEY `typeId` (`typeId`),
  KEY `qnSecurity1` (`qnSecurity1`),
  KEY `qnSecurity2` (`qnSecurity2`),
  KEY `themeId` (`themeId`),
  KEY `firstDayOfAWeekId` (`firstDayOfAWeekId`),
  CONSTRAINT `login_ibfk_1` FOREIGN KEY (`statusId`) REFERENCES `userStatus` (`statusId`),
  CONSTRAINT `login_ibfk_10` FOREIGN KEY (`firstDayOfAWeekId`) REFERENCES `daysOfAWeek` (`dayId`),
  CONSTRAINT `login_ibfk_2` FOREIGN KEY (`sexId`) REFERENCES `sex` (`sexId`),
  CONSTRAINT `login_ibfk_3` FOREIGN KEY (`maritalId`) REFERENCES `marital` (`maritalId`),
  CONSTRAINT `login_ibfk_4` FOREIGN KEY (`jobId`) REFERENCES `jobTitle` (`jobId`),
  CONSTRAINT `login_ibfk_5` FOREIGN KEY (`groupId`) REFERENCES `groups` (`groupId`),
  CONSTRAINT `login_ibfk_6` FOREIGN KEY (`typeId`) REFERENCES `userType` (`typeId`),
  CONSTRAINT `login_ibfk_7` FOREIGN KEY (`qnSecurity1`) REFERENCES `securityQuestion` (`questionId`),
  CONSTRAINT `login_ibfk_8` FOREIGN KEY (`qnSecurity2`) REFERENCES `securityQuestion` (`questionId`),
  CONSTRAINT `login_ibfk_9` FOREIGN KEY (`themeId`) REFERENCES `themes` (`themeId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES (1,'Init Person',NULL,NULL,NULL,NULL,NULL,'0000:00:00:00:00:00',NULL,NULL,NULL,NULL,'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',NULL,NULL,NULL,NULL,NULL,0,'0000:00:00:00:00:00','0000:00:00:00:00:00',NULL,NULL,NULL,NULL,NULL,NULL,1,'0000:00:00:00:00:00',NULL,NULL,NULL,NULL,NULL,79);
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `macOUILookup`
--

DROP TABLE IF EXISTS `macOUILookup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `macOUILookup` (
  `ouiId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendorName` varchar(48) NOT NULL,
  `vendorOUI` varchar(16) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`ouiId`),
  UNIQUE KEY `vendorOUI` (`vendorOUI`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `macOUILookup`
--

LOCK TABLES `macOUILookup` WRITE;
/*!40000 ALTER TABLE `macOUILookup` DISABLE KEYS */;
/*!40000 ALTER TABLE `macOUILookup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marital`
--

DROP TABLE IF EXISTS `marital`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marital` (
  `maritalId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `maritalName` varchar(24) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`maritalId`),
  UNIQUE KEY `maritalName` (`maritalName`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marital`
--

LOCK TABLES `marital` WRITE;
/*!40000 ALTER TABLE `marital` DISABLE KEYS */;
INSERT INTO `marital` VALUES (1,'Single',NULL,NULL,15),(2,'Married',NULL,NULL,15),(3,'Divorced',NULL,NULL,15),(4,'Widowed',NULL,NULL,15),(5,'Cohabiting',NULL,NULL,15),(6,'Civil Union',NULL,NULL,15),(7,'Domestic Partner',NULL,NULL,15),(8,'Unmarried Partner',NULL,NULL,15);
/*!40000 ALTER TABLE `marital` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messagePolicyType`
--

DROP TABLE IF EXISTS `messagePolicyType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messagePolicyType` (
  `typeId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `typeName` varchar(48) NOT NULL,
  `typeCode` int(2) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`typeId`),
  UNIQUE KEY `typeName` (`typeName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messagePolicyType`
--

LOCK TABLES `messagePolicyType` WRITE;
/*!40000 ALTER TABLE `messagePolicyType` DISABLE KEYS */;
/*!40000 ALTER TABLE `messagePolicyType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messageType`
--

DROP TABLE IF EXISTS `messageType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messageType` (
  `typeId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `typeName` varchar(48) NOT NULL,
  `typeCode` int(2) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`typeId`),
  UNIQUE KEY `typeName` (`typeName`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messageType`
--

LOCK TABLES `messageType` WRITE;
/*!40000 ALTER TABLE `messageType` DISABLE KEYS */;
INSERT INTO `messageType` VALUES (1,'Local',1,NULL,NULL,15),(2,'SMS',2,NULL,NULL,15),(3,'Email',3,NULL,NULL,15);
/*!40000 ALTER TABLE `messageType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monthOfAYear`
--

DROP TABLE IF EXISTS `monthOfAYear`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monthOfAYear` (
  `monthId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `monthName` varchar(9) NOT NULL,
  `monthAbbreviation` varchar(3) NOT NULL,
  `monthNumber` int(2) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`monthId`),
  UNIQUE KEY `monthName` (`monthName`),
  UNIQUE KEY `monthAbbreviation` (`monthAbbreviation`),
  UNIQUE KEY `monthNumber` (`monthNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monthOfAYear`
--

LOCK TABLES `monthOfAYear` WRITE;
/*!40000 ALTER TABLE `monthOfAYear` DISABLE KEYS */;
INSERT INTO `monthOfAYear` VALUES (1,'January','Jan',1,NULL,NULL,15),(2,'February','Feb',2,NULL,NULL,15),(3,'March','Mar',3,NULL,NULL,15),(4,'April','Apr',4,NULL,NULL,15),(5,'May','May',5,NULL,NULL,15),(6,'June','Jun',6,NULL,NULL,15),(7,'July','Jul',7,NULL,NULL,15),(8,'August','Aug',8,NULL,NULL,15),(9,'September','Sep',9,NULL,NULL,15),(10,'October','Oct',10,NULL,NULL,15),(11,'November','Nov',11,NULL,NULL,15),(12,'December','Dec',12,NULL,NULL,15);
/*!40000 ALTER TABLE `monthOfAYear` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pHPTimezone`
--

DROP TABLE IF EXISTS `pHPTimezone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pHPTimezone` (
  `zoneId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zoneName` varchar(48) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`zoneId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pHPTimezone`
--

LOCK TABLES `pHPTimezone` WRITE;
/*!40000 ALTER TABLE `pHPTimezone` DISABLE KEYS */;
INSERT INTO `pHPTimezone` VALUES (1,'Africa/Dar_es_Salaam',NULL,NULL,15);
/*!40000 ALTER TABLE `pHPTimezone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paper`
--

DROP TABLE IF EXISTS `paper`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paper` (
  `paperId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `paperName` varchar(48) NOT NULL,
  `width` int(4) NOT NULL,
  `height` int(4) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`paperId`),
  UNIQUE KEY `paperName` (`paperName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paper`
--

LOCK TABLES `paper` WRITE;
/*!40000 ALTER TABLE `paper` DISABLE KEYS */;
/*!40000 ALTER TABLE `paper` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `profileId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profileName` varchar(96) NOT NULL DEFAULT 'Default Profile',
  `systemName` varchar(108) NOT NULL DEFAULT 'Default SYS',
  `logo` varchar(64) DEFAULT NULL,
  `telephoneList` varchar(108) DEFAULT NULL,
  `emailList` varchar(108) DEFAULT NULL,
  `otherCommunication` varchar(108) DEFAULT NULL,
  `postalAddress` varchar(108) DEFAULT NULL,
  `website` varchar(64) DEFAULT NULL,
  `faxList` varchar(108) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `countryId` int(10) unsigned DEFAULT NULL,
  `pHPTimezoneId` int(10) unsigned DEFAULT NULL,
  `dob` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `currentAccademicYearId` int(10) unsigned DEFAULT NULL,
  `beginOfTheYearDate` varchar(19) NOT NULL DEFAULT '*:09:01:*:*:*',
  `nextGraduationYear` int(4) NOT NULL DEFAULT '2016',
  `nextSemester` int(1) NOT NULL DEFAULT '1',
  `numberOfSemestersPerYear` int(1) NOT NULL DEFAULT '2',
  `gpsCoordinate` varchar(72) DEFAULT NULL,
  `themeId` int(10) unsigned DEFAULT NULL,
  `firstDayOfAWeekId` int(10) unsigned DEFAULT NULL,
  `xmlFile` varchar(12) NOT NULL DEFAULT 'profile.xml',
  `xmlFileChecksum` char(32) DEFAULT NULL,
  `serverIpAddress` varchar(15) NOT NULL DEFAULT '192.168.1.1',
  `localAreaNetworkMask` varchar(15) NOT NULL DEFAULT '255.255.255.0',
  `maxNumberOfReturnedSearchRecords` int(4) NOT NULL DEFAULT '512',
  `maxNumberOfDisplayedRowsPerPage` int(4) NOT NULL DEFAULT '64',
  `minAgeCriteriaForStudents` int(4) NOT NULL DEFAULT '12',
  `minAgeCriteriaForUsers` int(4) NOT NULL DEFAULT '12',
  `nextRegistrationNumber` int(16) unsigned NOT NULL DEFAULT '1',
  `registrationNumberWidth` int(16) unsigned NOT NULL DEFAULT '4',
  `nextExaminationNumber` int(16) unsigned NOT NULL DEFAULT '1',
  `examinationNumberWidth` int(16) unsigned NOT NULL DEFAULT '4',
  `applicationCounter` int(2) unsigned NOT NULL DEFAULT '0',
  `revisionNumber` int(8) unsigned NOT NULL DEFAULT '1',
  `revisionTime` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `serverMACAddress` varchar(24) DEFAULT NULL,
  `systemHash` char(32) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`profileId`),
  KEY `countryId` (`countryId`),
  KEY `pHPTimezoneId` (`pHPTimezoneId`),
  KEY `themeId` (`themeId`),
  KEY `firstDayOfAWeekId` (`firstDayOfAWeekId`),
  KEY `currentAccademicYearId` (`currentAccademicYearId`),
  CONSTRAINT `currentAccademicYearId` FOREIGN KEY (`currentAccademicYearId`) REFERENCES `accademicYear` (`accademicYearId`),
  CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`countryId`) REFERENCES `country` (`countryId`),
  CONSTRAINT `profile_ibfk_2` FOREIGN KEY (`pHPTimezoneId`) REFERENCES `pHPTimezone` (`zoneId`),
  CONSTRAINT `profile_ibfk_3` FOREIGN KEY (`themeId`) REFERENCES `themes` (`themeId`),
  CONSTRAINT `profile_ibfk_4` FOREIGN KEY (`firstDayOfAWeekId`) REFERENCES `daysOfAWeek` (`dayId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES (1,'Default SYS','Default SYS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0000:00:00:00:00:00',1,'*:09:01:*:*:*',2016,1,2,NULL,NULL,NULL,'profile.xml',NULL,'192.168.1.1','255.255.255.0',512,64,12,12,1,4,1,4,0,1,'0000:00:00:00:00:00',NULL,NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `religion`
--

DROP TABLE IF EXISTS `religion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `religion` (
  `religionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `religionName` varchar(48) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`religionId`),
  UNIQUE KEY `religionName` (`religionName`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `religion`
--

LOCK TABLES `religion` WRITE;
/*!40000 ALTER TABLE `religion` DISABLE KEYS */;
INSERT INTO `religion` VALUES (1,'Christian',NULL,NULL,15),(2,'Muslim',NULL,NULL,15),(3,'Buddhist',NULL,NULL,15),(4,'Hindu',NULL,NULL,15),(5,'Ethinic/Indigeneous',NULL,NULL,15),(6,'Indigeneous',NULL,NULL,15),(7,'Judaism',NULL,NULL,15),(8,'Baha\'i',NULL,NULL,15),(9,'Irreligious and Atheist',NULL,NULL,15),(10,'Sikhism',NULL,NULL,15),(11,'Taoists/Confucianists/Chinese tradional religion',NULL,NULL,15),(12,'Jainism',NULL,NULL,15),(13,'Mormonism',NULL,NULL,15),(14,'Spiritism',NULL,NULL,15),(15,'Others',NULL,NULL,15);
/*!40000 ALTER TABLE `religion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `results` (
  `resultsId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resultsValid` tinyint(1) NOT NULL DEFAULT '0',
  `groupId` int(10) unsigned NOT NULL,
  `examinationId` int(10) unsigned NOT NULL,
  `rawMarksCSVFile` varchar(32) DEFAULT NULL,
  `rawMarksCSVFileChecksum` char(32) DEFAULT NULL,
  `gradedMarksCSVFile` varchar(32) DEFAULT NULL,
  `gradedMarksCSVFileChecksum` char(32) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`resultsId`),
  KEY `groupId` (`groupId`),
  KEY `examinationId` (`examinationId`),
  CONSTRAINT `results_ibfk_1` FOREIGN KEY (`groupId`) REFERENCES `resultsGroup` (`groupId`),
  CONSTRAINT `results_ibfk_2` FOREIGN KEY (`examinationId`) REFERENCES `examination` (`examinationId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `results`
--

LOCK TABLES `results` WRITE;
/*!40000 ALTER TABLE `results` DISABLE KEYS */;
/*!40000 ALTER TABLE `results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultsGroup`
--

DROP TABLE IF EXISTS `resultsGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resultsGroup` (
  `groupId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resultsValid` tinyint(1) NOT NULL DEFAULT '0',
  `examinationGroupId` int(10) unsigned NOT NULL,
  `courseId` int(10) unsigned NOT NULL,
  `_year` int(4) unsigned NOT NULL,
  `batchId` int(10) unsigned NOT NULL,
  `rawMarksCSVFile` varchar(32) DEFAULT NULL,
  `rawMarksCSVFileChecksum` char(32) DEFAULT NULL,
  `gradedMarksCSVFile` varchar(32) DEFAULT NULL,
  `gradedMarksCSVFileChecksum` char(32) DEFAULT NULL,
  `displayResultsEnable` tinyint(1) NOT NULL DEFAULT '0',
  `displayResultsSummaryEnable` tinyint(1) NOT NULL DEFAULT '0',
  `availableSemesterList` varchar(108) DEFAULT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`groupId`),
  KEY `examinationGroupId` (`examinationGroupId`),
  KEY `batchId` (`batchId`),
  KEY `resultsGroup_ibfk_2` (`courseId`),
  CONSTRAINT `resultsGroup_ibfk_1` FOREIGN KEY (`examinationGroupId`) REFERENCES `examinationGroup` (`groupId`),
  CONSTRAINT `resultsGroup_ibfk_2` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`),
  CONSTRAINT `resultsGroup_ibfk_3` FOREIGN KEY (`batchId`) REFERENCES `accademicYear` (`accademicYearId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultsGroup`
--

LOCK TABLES `resultsGroup` WRITE;
/*!40000 ALTER TABLE `resultsGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `resultsGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedule` (
  `scheduleId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scheduleName` varchar(48) NOT NULL,
  `venueId` int(10) unsigned DEFAULT NULL,
  `instructorList` varchar(108) DEFAULT NULL,
  `groupList` varchar(108) DEFAULT NULL,
  `startTime` varchar(19) DEFAULT NULL,
  `endTime` varchar(19) DEFAULT NULL,
  `textColor` varchar(6) NOT NULL DEFAULT '000000',
  `backgroundColor` varchar(6) NOT NULL DEFAULT 'ffffff',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`scheduleId`),
  UNIQUE KEY `scheduleName` (`scheduleName`),
  KEY `venueId` (`venueId`),
  CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`venueId`) REFERENCES `venue` (`venueId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule`
--

LOCK TABLES `schedule` WRITE;
/*!40000 ALTER TABLE `schedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `securityQuestion`
--

DROP TABLE IF EXISTS `securityQuestion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `securityQuestion` (
  `questionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `questionName` varchar(96) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`questionId`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `securityQuestion`
--

LOCK TABLES `securityQuestion` WRITE;
/*!40000 ALTER TABLE `securityQuestion` DISABLE KEYS */;
INSERT INTO `securityQuestion` VALUES (1,'What was your childhood nickname?',NULL,NULL,15),(2,'In what city did you meet your spouse/significant other?',NULL,NULL,15),(3,'What is the name of your favorite childhood friend?',NULL,NULL,15),(4,'What street did you live on in third grade?',NULL,NULL,15),(5,'What is your oldest sibling\'s birthday month and year? (e.g., January 1900)',NULL,NULL,15),(6,'What is the middle name of your oldest child?',NULL,NULL,15),(7,'What is your oldest sibling\'s middle name?',NULL,NULL,15),(8,'What school did you attend for sixth grade?',NULL,NULL,15),(9,'What was your childhood phone number including area code? (e.g., 000-000-0000)',NULL,NULL,15),(10,'What is your oldest cousin\'s first and last name?',NULL,NULL,15),(11,'What was the name of your first stuffed animal?',NULL,NULL,15),(12,'In what city or town did your mother and father meet?',NULL,NULL,15),(13,'Where were you when you had your first kiss?',NULL,NULL,15),(14,'What is the first name of the boy or girl that you first kissed?',NULL,NULL,15),(15,'What was the last name of your third grade teacher?',NULL,NULL,15),(16,'In what city does your nearest sibling live?',NULL,NULL,15),(17,'What is your oldest brother\'s birthday month and year? (e.g., January 1900)',NULL,NULL,15),(18,'What is your maternal grandmother\'s maiden name?',NULL,NULL,15),(19,'In what city or town was your first job?',NULL,NULL,15),(20,'What is the name of the place your wedding reception was held?',NULL,NULL,15),(21,'What is the name of a college you applied to but didn\'t attend?',NULL,NULL,15),(22,'Where were you when you first heard about 9/11?',NULL,NULL,15);
/*!40000 ALTER TABLE `securityQuestion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sex`
--

DROP TABLE IF EXISTS `sex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sex` (
  `sexId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sexName` varchar(8) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`sexId`),
  UNIQUE KEY `sexName` (`sexName`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sex`
--

LOCK TABLES `sex` WRITE;
/*!40000 ALTER TABLE `sex` DISABLE KEYS */;
INSERT INTO `sex` VALUES (1,'Male',NULL,NULL,15),(2,'Female',NULL,NULL,15);
/*!40000 ALTER TABLE `sex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `studentId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loginId` int(10) unsigned NOT NULL,
  `occupation` varchar(32) DEFAULT NULL,
  `registrationNumber` varchar(64) DEFAULT NULL,
  `examinationNumber` varchar(64) DEFAULT NULL,
  `citizenshipId` int(10) unsigned DEFAULT NULL,
  `nativeplace` varchar(48) DEFAULT NULL,
  `disability` varchar(32) DEFAULT NULL,
  `denominationId` int(10) unsigned DEFAULT NULL,
  `postalAddress` varchar(64) DEFAULT NULL,
  `physicalAddress` varchar(64) DEFAULT NULL,
  `employed` tinyint(1) NOT NULL DEFAULT '0',
  `courseId` int(10) unsigned DEFAULT NULL,
  `referenceNumber` varchar(16) DEFAULT NULL,
  `registrationTime` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `cyear` int(2) unsigned NOT NULL DEFAULT '0',
  `csemester` int(2) unsigned NOT NULL DEFAULT '0',
  `applicationCounter` int(2) NOT NULL DEFAULT '0',
  `xmlFile` varchar(24) DEFAULT NULL,
  `xmlFileChecksum` char(32) DEFAULT NULL,
  `batchList` varchar(108) DEFAULT NULL,
  `admissionBatch` int(10) unsigned DEFAULT NULL,
  `admissionBatchList` varchar(108) DEFAULT NULL,
  `currentAccademicYear` int(10) unsigned DEFAULT NULL,
  `currentBatch` int(10) unsigned DEFAULT NULL,
  `formFourYear` int(4) unsigned NOT NULL DEFAULT '0',
  `formFourIndex` varchar(32) DEFAULT NULL,
  `optionalTransactionList` varchar(108) DEFAULT NULL,
  `transcriptHash` char(32) DEFAULT NULL,
  `transcriptFile` varchar(32) DEFAULT NULL,
  `graduationYear` int(4) unsigned NOT NULL DEFAULT '0',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`studentId`),
  UNIQUE KEY `registrationNumber` (`registrationNumber`),
  KEY `loginId` (`loginId`),
  KEY `citizenshipId` (`citizenshipId`),
  KEY `denominationId` (`denominationId`),
  KEY `courseId` (`courseId`),
  KEY `admissionBatch` (`admissionBatch`),
  KEY `currentAccademicYear` (`currentAccademicYear`),
  KEY `currentBatch` (`currentBatch`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`loginId`) REFERENCES `login` (`loginId`),
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`citizenshipId`) REFERENCES `country` (`countryId`),
  CONSTRAINT `students_ibfk_3` FOREIGN KEY (`denominationId`) REFERENCES `denomination` (`denominationId`),
  CONSTRAINT `students_ibfk_4` FOREIGN KEY (`courseId`) REFERENCES `course` (`courseId`),
  CONSTRAINT `students_ibfk_5` FOREIGN KEY (`admissionBatch`) REFERENCES `accademicYear` (`accademicYearId`),
  CONSTRAINT `students_ibfk_6` FOREIGN KEY (`currentAccademicYear`) REFERENCES `accademicYear` (`accademicYearId`),
  CONSTRAINT `students_ibfk_7` FOREIGN KEY (`currentBatch`) REFERENCES `accademicYear` (`accademicYearId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject` (
  `subjectId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subjectName` varchar(48) NOT NULL,
  `subjectCode` varchar(12) NOT NULL,
  `lectureHours` int(4) NOT NULL DEFAULT '0',
  `lectureUnits` int(4) NOT NULL DEFAULT '0',
  `practicalHours` int(4) NOT NULL DEFAULT '0',
  `practicalUnits` int(4) NOT NULL DEFAULT '0',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`subjectId`),
  UNIQUE KEY `subjectCode` (`subjectCode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject`
--

LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `systemPolicy`
--

DROP TABLE IF EXISTS `systemPolicy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemPolicy` (
  `policyId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `className` varchar(64) NOT NULL,
  `policyCaption` varchar(96) NOT NULL,
  `root` tinyint(1) NOT NULL DEFAULT '0',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`policyId`),
  UNIQUE KEY `className` (`className`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systemPolicy`
--

LOCK TABLES `systemPolicy` WRITE;
/*!40000 ALTER TABLE `systemPolicy` DISABLE KEYS */;
INSERT INTO `systemPolicy` VALUES (1,'systemPolicy','systemPolicy',1,NULL,NULL,65535),(2,'Reserved','Reserved',1,NULL,NULL,65535),(3,'DaysOfAWeek','DaysOfAWeek',0,NULL,NULL,65535),(4,'MonthOfAYear','MonthOfAYear',0,NULL,NULL,65535),(5,'Theme','Theme',0,NULL,NULL,65535),(6,'Country','Country',0,NULL,NULL,65535),(7,'Profile','Profile',0,NULL,NULL,65535),(8,'EducationLevel','EducationLevel',0,NULL,NULL,65535),(9,'Department','Department',0,NULL,NULL,65535),(10,'Course','Course',0,NULL,NULL,65535),(11,'Subject','Subject',0,NULL,NULL,65535),(12,'CourseAndSubjectTransaction','CourseAndSubjectTransaction',0,NULL,NULL,65535),(13,'SecurityQuestion','SecurityQuestion',0,NULL,NULL,65535),(14,'UserType','UserType',0,NULL,NULL,65535),(15,'UserStatus','UserStatus',0,NULL,NULL,65535),(16,'Sex','Sex',0,NULL,NULL,65535),(17,'Marital','Marital',0,NULL,NULL,65535),(18,'Group','Group',0,NULL,NULL,65535),(19,'JobTitle','JobTitle',0,NULL,NULL,65535),(20,'Religion','Religion',0,NULL,NULL,65535),(21,'Denomination','Denomination',0,NULL,NULL,65535),(22,'Login','Login',0,NULL,NULL,65535),(23,'User','User',0,NULL,NULL,65535),(24,'CourseInstructor','CourseInstructor',0,NULL,NULL,65535),(25,'Student','Student',0,NULL,NULL,65535),(26,'ExaminationGroup','ExaminationGroup',0,NULL,NULL,65535),(27,'Examination','Examination',0,NULL,NULL,65535),(28,'ExaminationNumberScope','ExaminationNumberScope',0,NULL,NULL,65535),(29,'ExaminationNumber','ExaminationNumber',0,NULL,NULL,65535),(30,'Grade','Grade',0,NULL,NULL,65535),(31,'Results','Results',0,NULL,NULL,65535),(32,'Currency','Currency',0,NULL,NULL,65535),(33,'FeeStructure','FeeStructure',0,NULL,NULL,65535),(34,'FeeInvoice','FeeInvoice',0,NULL,NULL,65535),(35,'FeePayer','FeePayer',0,NULL,NULL,65535),(36,'FeePayment','FeePayment',0,NULL,NULL,65535),(37,'Venue','Venue',0,NULL,NULL,65535),(38,'Timetable','Timetable',0,NULL,NULL,65535),(39,'Schedule','Schedule',0,NULL,NULL,65535),(40,'Holiday','Holiday',0,NULL,NULL,65535),(41,'Attendance','Attendance',0,NULL,NULL,65535),(42,'LeaveType','LeaveType',0,NULL,NULL,65535),(43,'LeaveApprovalSchemaGroup','LeaveApprovalSchemaGroup',0,NULL,NULL,65535),(44,'LeaveApprovalSchema','LeaveApprovalSchema',0,NULL,NULL,65535),(45,'LeaveApplication','LeaveApplication',0,NULL,NULL,65535),(46,'MessageType','MessageType',0,NULL,NULL,65535),(47,'MessagePolicyType','MessagePolicyType',0,NULL,NULL,65535),(48,'ResultsChangingSchemaGroup','ResultsChangingSchemaGroup',0,NULL,NULL,65535),(49,'ResultsChangingSchema','ResultsChangingSchema',0,NULL,NULL,65535),(50,'ResultsChangeLog','ResultsChangeLog',0,NULL,NULL,65535),(51,'EntryCriteria','EntryCriteria',0,NULL,NULL,65535),(52,'ClassOfAward','ClassOfAward',0,NULL,NULL,65535);
/*!40000 ALTER TABLE `systemPolicy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `systemlogs`
--

DROP TABLE IF EXISTS `systemlogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systemlogs` (
  `logId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `logName` varchar(108) NOT NULL,
  PRIMARY KEY (`logId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systemlogs`
--

LOCK TABLES `systemlogs` WRITE;
/*!40000 ALTER TABLE `systemlogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `systemlogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `themes`
--

DROP TABLE IF EXISTS `themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `themes` (
  `themeId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `themeName` varchar(24) NOT NULL,
  `themeFolder` varchar(16) NOT NULL,
  `themeBackgroundColor` varchar(6) NOT NULL DEFAULT 'ffffff',
  `themeBackgroundImage` varchar(16) DEFAULT NULL,
  `themeFontFace` varchar(108) DEFAULT NULL,
  `themeFontSize` varchar(8) DEFAULT NULL,
  `themeFontColor` varchar(6) NOT NULL DEFAULT '000000',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`themeId`),
  UNIQUE KEY `themeName` (`themeName`),
  UNIQUE KEY `themeFolder` (`themeFolder`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `themes`
--

LOCK TABLES `themes` WRITE;
/*!40000 ALTER TABLE `themes` DISABLE KEYS */;
INSERT INTO `themes` VALUES (1,'Black Tie','black-tie','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(2,'Blitzer','blitzer','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(3,'Cupertino','cupertino','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(4,'Dark Hive','dark-hive','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(5,'Dot Luv','dot-luv','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(6,'Egg Plant','eggplant','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(7,'Excite Bike','excite-bike','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(8,'Flick','flick','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(9,'Hot Sneaks','hot-sneaks','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(10,'Humanity','humanity','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(11,'Le Frog','le-frog','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(12,'Mint Choc','mint-choc','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(13,'Overcast','overcast','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(14,'Pepper Grinder','pepper-grinder','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(15,'Redmond','redmond','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(16,'Smoothness','smoothness','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(17,'South Street','south-street','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(18,'Start','start','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(19,'Sunny','sunny','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(20,'Swanky Purse','swanky-purse','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(21,'Trontastic','trontastic','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(22,'UI Darkness','ui-darkness','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(23,'UI Lightness','ui-lightness','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15),(24,'Vader','vader','ffffff',NULL,NULL,NULL,'000000',NULL,NULL,15);
/*!40000 ALTER TABLE `themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timetable`
--

DROP TABLE IF EXISTS `timetable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timetable` (
  `ttId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ttName` varchar(48) NOT NULL,
  `venueId` int(10) unsigned DEFAULT NULL,
  `instructorList` varchar(108) DEFAULT NULL,
  `groupList` varchar(108) DEFAULT NULL,
  `subjectList` varchar(108) DEFAULT NULL,
  `validFrom` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `validTo` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `startTime` varchar(19) NOT NULL DEFAULT '*:*:*:13:00:00',
  `endTime` varchar(19) NOT NULL DEFAULT '*:*:*:14:00:00',
  `dayOfAWeekId` int(10) unsigned NOT NULL,
  `textColor` varchar(6) NOT NULL DEFAULT '000000',
  `backgroundColor` varchar(6) NOT NULL DEFAULT 'ffffff',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`ttId`),
  KEY `venueId` (`venueId`),
  KEY `dayOfAWeekId` (`dayOfAWeekId`),
  CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`venueId`) REFERENCES `venue` (`venueId`),
  CONSTRAINT `timetable_ibfk_2` FOREIGN KEY (`dayOfAWeekId`) REFERENCES `daysOfAWeek` (`dayId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timetable`
--

LOCK TABLES `timetable` WRITE;
/*!40000 ALTER TABLE `timetable` DISABLE KEYS */;
/*!40000 ALTER TABLE `timetable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userStatus`
--

DROP TABLE IF EXISTS `userStatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userStatus` (
  `statusId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `statusName` varchar(32) NOT NULL,
  `statusCode` int(2) unsigned NOT NULL,
  `alive` tinyint(1) NOT NULL DEFAULT '1',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`statusId`),
  UNIQUE KEY `statusName` (`statusName`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userStatus`
--

LOCK TABLES `userStatus` WRITE;
/*!40000 ALTER TABLE `userStatus` DISABLE KEYS */;
INSERT INTO `userStatus` VALUES (1,'Alive',1,1,NULL,NULL,15),(2,'Dead',2,0,NULL,NULL,15),(3,'Disqualified',3,0,NULL,NULL,15),(4,'Leave',4,1,NULL,NULL,15),(5,'Martenity',5,1,NULL,NULL,15);
/*!40000 ALTER TABLE `userStatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userType`
--

DROP TABLE IF EXISTS `userType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userType` (
  `typeId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `typeName` varchar(16) NOT NULL,
  `typeCode` int(2) NOT NULL DEFAULT '1',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`typeId`),
  UNIQUE KEY `typeName` (`typeName`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userType`
--

LOCK TABLES `userType` WRITE;
/*!40000 ALTER TABLE `userType` DISABLE KEYS */;
INSERT INTO `userType` VALUES (1,'User',1,NULL,NULL,15),(2,'CourseInstructor',2,NULL,NULL,15),(3,'Student',3,NULL,NULL,15);
/*!40000 ALTER TABLE `userType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loginId` int(10) unsigned NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`userId`),
  KEY `loginId` (`loginId`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`loginId`) REFERENCES `login` (`loginId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,NULL,NULL,15);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venue`
--

DROP TABLE IF EXISTS `venue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `venue` (
  `venueId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `venueName` varchar(48) NOT NULL,
  `venueCode` varchar(12) NOT NULL,
  `capacity` int(5) NOT NULL,
  `numberOfConcurrentAllocations` int(2) NOT NULL DEFAULT '1',
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`venueId`),
  UNIQUE KEY `venueName` (`venueName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venue`
--

LOCK TABLES `venue` WRITE;
/*!40000 ALTER TABLE `venue` DISABLE KEYS */;
/*!40000 ALTER TABLE `venue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wirelessConnection`
--

DROP TABLE IF EXISTS `wirelessConnection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wirelessConnection` (
  `connectionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loginId` int(10) unsigned NOT NULL,
  `username` varchar(64) NOT NULL,
  `connectionTime` varchar(19) NOT NULL DEFAULT '0000:00:00:00:00:00',
  `wirelessId` varchar(16) NOT NULL,
  `extraFilter` varchar(32) DEFAULT NULL,
  `extraInformation` varchar(64) DEFAULT NULL,
  `flags` int(5) NOT NULL DEFAULT '15',
  PRIMARY KEY (`connectionId`),
  UNIQUE KEY `username` (`username`),
  KEY `loginId` (`loginId`),
  CONSTRAINT `wirelessConnection_ibfk_1` FOREIGN KEY (`loginId`) REFERENCES `login` (`loginId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wirelessConnection`
--

LOCK TABLES `wirelessConnection` WRITE;
/*!40000 ALTER TABLE `wirelessConnection` DISABLE KEYS */;
/*!40000 ALTER TABLE `wirelessConnection` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-17 17:05:37
insert into contextPosition (cName, cPosition, caption) values('managestudent_overall_summary_results', '424', 'Overall Summary Results');
insert into contextPosition (cName, cPosition, caption) values('managestudent_view_transcript', '426', 'View Student Transcript');

