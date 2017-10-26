-- MySQL dump 10.13  Distrib 5.6.33, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: temp
-- ------------------------------------------------------
-- Server version	5.6.33-0ubuntu0.14.04.1

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
-- Table structure for table `domain`
--

DROP TABLE IF EXISTS `domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` char(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` char(20) DEFAULT NULL,
  `gw_ip` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `port` int(11) NOT NULL,
  `user` char(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwd` char(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_name` char(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domain`
--

LOCK TABLES `domain` WRITE;
/*!40000 ALTER TABLE `domain` DISABLE KEYS */;
INSERT INTO `domain` VALUES (1,'master','318','localhost',60000,'root','xidian','xidian',0);
/*!40000 ALTER TABLE `domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `gname` varchar(10) DEFAULT NULL,
  `gtype` int(2) DEFAULT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group`
--

LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
INSERT INTO `group` VALUES (1,'管理员',0),(2,'普通用户',1);
/*!40000 ALTER TABLE `group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `new_vm_apply`
--

DROP TABLE IF EXISTS `new_vm_apply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `new_vm_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gateway_ip` varchar(20) NOT NULL,
  `vm_id` int(11) NOT NULL,
  `os_version` varchar(50) NOT NULL,
  `cpu_cores` int(2) NOT NULL,
  `memory` int(10) NOT NULL,
  `disk` int(10) NOT NULL,
  `password` varchar(50) NOT NULL,
  `lid` int(11) NOT NULL,
  `apply_time` int(11) NOT NULL,
  `state` int(2) NOT NULL COMMENT '0：未处理 1：已同意 2：已拒绝',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `new_vm_apply`
--

LOCK TABLES `new_vm_apply` WRITE;
/*!40000 ALTER TABLE `new_vm_apply` DISABLE KEYS */;
/*!40000 ALTER TABLE `new_vm_apply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `usbkey` varchar(30) NOT NULL,
  `kind` int(2) NOT NULL COMMENT '0:正常;1:锁定',
  `email` varchar(40) NOT NULL,
  `phone_num` varchar(12) NOT NULL,
  `group_type` int(8) DEFAULT '1',
  `reg_time` int(11) DEFAULT NULL,
  `lastlogin` int(11) DEFAULT NULL,
  `fullname` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','xidian','',0,'admin@126.com','',0,1457605869,1503672872,'admin'),(2,'student','123456','',0,'student@163.com','',1,1462193633,1505918787,'student');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_log` (
  `lid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `start_time` int(11) DEFAULT NULL,
  `end_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`lid`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_log`
--

LOCK TABLES `user_log` WRITE;
/*!40000 ALTER TABLE `user_log` DISABLE KEYS */;
INSERT INTO `user_log` VALUES (1,2,1498035340,1498035342),(2,1,1498038355,1498038375),(3,2,1498038391,1498038393),(4,2,1498468499,1498468500),(5,2,1498470706,1498470746),(6,2,1498825946,1498826049),(7,1,1498826187,1498826206),(8,1,1498907879,1498907881),(9,1,1498909630,1498909633),(10,2,1498909641,1498909648),(11,1,1498915680,1498915682),(12,2,1499065225,1499066151),(13,1,1499066739,1499066790),(14,2,1499067703,1499067706),(15,1,1499067850,1499067872),(16,2,1499071354,1499071358),(17,1,1499071719,1499072479),(18,2,1501564793,1501564798),(19,2,1502167348,1502167350),(20,1,1502167358,1502167361),(21,1,1503672872,1503672879);
/*!40000 ALTER TABLE `user_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vm`
--

DROP TABLE IF EXISTS `vm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `belong` int(10) DEFAULT '0',
  `uuid` char(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_label` char(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` int(5) DEFAULT '1',
  `cpu_cores` int(3) NOT NULL DEFAULT '0',
  `memory` int(3) NOT NULL DEFAULT '0',
  `disk` int(10) NOT NULL DEFAULT '0',
  `ip` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `vm_ip` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `gateway_ip` char(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `os_version` char(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `user` int(10) DEFAULT NULL,
  `apply_time` int(11) DEFAULT '0',
  `start_time` int(11) DEFAULT '0',
  `power_state` int(20) DEFAULT NULL COMMENT '0关机 1开机',
  `state` tinyint(1) DEFAULT '0' COMMENT '0:未使用或将回收',
  `delete` tinyint(1) DEFAULT '1' COMMENT '1代表不删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vm`
--

LOCK TABLES `vm` WRITE;
/*!40000 ALTER TABLE `vm` DISABLE KEYS */;
INSERT INTO `vm` VALUES (22,1,'5c62fb24-0089-4592-9960-007dfdb4746e','DragonStack22',2,1,1024,15,'192.168.122.DragonStack22','192.168.122.89','localhost','ubuntu 14.4(64bits)','111111',2,0,0,1,1,1),(21,1,'f9a2c42e-78ce-4b48-9efa-2d25686caefc','DragonStack21',1,1,1024,15,'192.168.122.DragonStack21','192.168.122.229','localhost','centos 6.3(64bits)','111111',2,0,0,1,1,1),(20,0,NULL,NULL,1,1,1024,15,NULL,NULL,'localhost','centos 6.3(64bits)','111111',2,0,0,NULL,0,1);
/*!40000 ALTER TABLE `vm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vm_conf`
--

DROP TABLE IF EXISTS `vm_conf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vm_conf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `os_name` varchar(30) NOT NULL,
  `os_version` char(30) DEFAULT NULL,
  `os_path` varchar(200) NOT NULL,
  `upload_time` int(11) NOT NULL,
  `os_state` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vm_conf`
--

LOCK TABLES `vm_conf` WRITE;
/*!40000 ALTER TABLE `vm_conf` DISABLE KEYS */;
INSERT INTO `vm_conf` VALUES (1,1,'Centos','centos 6.3(64bits)','/var/lib/libvirt/images/iso/centos.iso',0,1),(3,3,'Windows','Windows 7 (64bit)','/var/lib/libvirt/images/iso/win7-64bit.iso',0,1),(2,2,'Ubuntu','ubuntu 14.4(64bits)','/var/lib/libvirt/images/iso/ubuntu-64.iso',0,1),(9,4,'Windows Server','Windows Server','/var/lib/libvirt/images/iso/WindowsServer.iso',0,0);
/*!40000 ALTER TABLE `vm_conf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vm_list`
--

DROP TABLE IF EXISTS `vm_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vm_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `vm_id` varchar(32) DEFAULT NULL,
  `des_ip` varchar(32) DEFAULT NULL,
  `local_port` int(10) DEFAULT NULL,
  `gw_ip` char(32) DEFAULT NULL,
  `port` int(10) DEFAULT NULL,
  `state` tinyint(1) DEFAULT '0' COMMENT '0:待审核;1:通过;2:拒绝',
  `timeout` tinyint(1) DEFAULT '0' COMMENT '0:时间未到;1:时间到',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vm_list`
--

LOCK TABLES `vm_list` WRITE;
/*!40000 ALTER TABLE `vm_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `vm_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vm_system`
--

DROP TABLE IF EXISTS `vm_system`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vm_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `os_name` varchar(30) NOT NULL,
  `cpu` int(11) NOT NULL,
  `ram` int(11) NOT NULL,
  `disk` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vm_system`
--

LOCK TABLES `vm_system` WRITE;
/*!40000 ALTER TABLE `vm_system` DISABLE KEYS */;
INSERT INTO `vm_system` VALUES (1,'Centos OS',1,1024,15),(2,'Ubuntu OS',1,1024,15),(3,'Windows 7',1,2048,30);
/*!40000 ALTER TABLE `vm_system` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workstation`
--

DROP TABLE IF EXISTS `workstation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workstation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host_name` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` char(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gateway_ip` char(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pwd` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `belong` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `belong` (`belong`)
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workstation`
--

LOCK TABLES `workstation` WRITE;
/*!40000 ALTER TABLE `workstation` DISABLE KEYS */;
INSERT INTO `workstation` VALUES (1,'master','localhost','localhost','root','xidian',1);
/*!40000 ALTER TABLE `workstation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ws_conf`
--

DROP TABLE IF EXISTS `ws_conf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ws_conf` (
  `id` int(11) NOT NULL,
  `cpuNumber` int(32) NOT NULL,
  `totalMemory` int(32) NOT NULL,
  `physicalDisk` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ws_conf`
--

LOCK TABLES `ws_conf` WRITE;
/*!40000 ALTER TABLE `ws_conf` DISABLE KEYS */;
INSERT INTO `ws_conf` VALUES (1,4,130995,'380G');
/*!40000 ALTER TABLE `ws_conf` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-21  7:21:08
