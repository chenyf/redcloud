-- MySQL dump 10.13  Distrib 5.6.33, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: tlc_lab
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
-- Table structure for table `about_us`
--

DROP TABLE IF EXISTS `about_us`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `about_us` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sequence` tinyint(4) unsigned NOT NULL COMMENT '显示顺序',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `about_us`
--

LOCK TABLES `about_us` WRITE;
/*!40000 ALTER TABLE `about_us` DISABLE KEYS */;
INSERT INTO `about_us` VALUES (18,0,'','','tlc','-');
/*!40000 ALTER TABLE `about_us` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_node`
--

DROP TABLE IF EXISTS `admin_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `remark` varchar(255) DEFAULT '',
  `pid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `level` (`level`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_node`
--

LOCK TABLES `admin_node` WRITE;
/*!40000 ALTER TABLE `admin_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role`
--

DROP TABLE IF EXISTS `admin_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '角色名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1.启用 2.禁用',
  `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `operator` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作人id',
  `remark` varchar(255) DEFAULT '' COMMENT '角色描述',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '角色编码',
  PRIMARY KEY (`id`),
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role`
--

LOCK TABLES `admin_role` WRITE;
/*!40000 ALTER TABLE `admin_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role_node`
--

DROP TABLE IF EXISTS `admin_role_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role_node` (
  `role_id` smallint(6) unsigned NOT NULL COMMENT '角色id',
  `node_id` smallint(6) unsigned NOT NULL COMMENT '节点id',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '节点等级',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`node_id`,`role_id`),
  KEY `groupId` (`role_id`) USING BTREE,
  KEY `nodeId` (`node_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role_node`
--

LOCK TABLES `admin_role_node` WRITE;
/*!40000 ALTER TABLE `admin_role_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_role_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role_user`
--

DROP TABLE IF EXISTS `admin_role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role_user` (
  `role_id` mediumint(9) unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
  `user_id` int(11) unsigned NOT NULL COMMENT '用户id',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  `operator` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作人',
  `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`role_id`,`user_id`),
  KEY `group_id` (`role_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role_user`
--

LOCK TABLES `admin_role_user` WRITE;
/*!40000 ALTER TABLE `admin_role_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_info_cfg`
--

DROP TABLE IF EXISTS `app_info_cfg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_info_cfg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cfgKey` varchar(50) NOT NULL DEFAULT '' COMMENT 'app信息配置键名',
  `cfgValue` varchar(500) NOT NULL DEFAULT '' COMMENT 'app信息配置键值',
  `ctm` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `mtm` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '最后修改人id',
  `webCode` char(15) NOT NULL DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) NOT NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_info_cfg`
--

LOCK TABLES `app_info_cfg` WRITE;
/*!40000 ALTER TABLE `app_info_cfg` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_info_cfg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_manager`
--

DROP TABLE IF EXISTS `app_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_manager` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appName` varchar(255) NOT NULL DEFAULT '' COMMENT '学校简称',
  `schoolUrl` text NOT NULL COMMENT '学校原网址',
  `newSchoolUrl` text NOT NULL COMMENT '学校新网址',
  `appInfo` text NOT NULL COMMENT 'App简介',
  `keyWord` varchar(255) NOT NULL COMMENT 'App关键词',
  `appTitle` varchar(255) NOT NULL COMMENT '上传显示的App Title（线上名称（有运营人员自定义））',
  `authorization` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否有授权书 2=>无 1=>有',
  `powerBook` varchar(500) NOT NULL COMMENT '授权书地址',
  `appType` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'app类型 1=> iPhone 2=>iPad 3=>android 4=>aPad',
  `bundle_Identifier_1` varchar(255) NOT NULL,
  `bundle_Identifier_2` varchar(255) NOT NULL,
  `bundle_Identifier_1_oc` varchar(255) NOT NULL,
  `bundle_Identifier_2_oc` varchar(255) NOT NULL,
  `signatory` varchar(50) NOT NULL COMMENT '签约人',
  `upLocation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '上传位置 0=》应用商店  1=》官方商店',
  `upSpecial` tinyint(1) NOT NULL DEFAULT '0' COMMENT '特殊需求 1=》上官网紧急使用',
  `apple_id_1` varchar(255) NOT NULL,
  `apple_id_2` varchar(255) NOT NULL,
  `SKU_Number_1` varchar(255) NOT NULL,
  `SKU_Number_2` varchar(255) NOT NULL,
  `iTunes_url_1` varchar(255) NOT NULL,
  `iTunes_url_2` varchar(255) NOT NULL,
  `downloadLink_1` text NOT NULL COMMENT 'iPhone应用商店下载地址',
  `downloadLink_2` text NOT NULL COMMENT 'iPad应用商店下载地址',
  `downloadLink_1_oc` text NOT NULL COMMENT 'iPhone官方商店下载地址',
  `downloadLink_2_oc` text NOT NULL COMMENT 'iPad官方商店下载地址',
  `downloadLink_3` text NOT NULL COMMENT 'android下载地址',
  `downloadLink_4` text NOT NULL COMMENT 'aPad下载地址',
  `manager_1` varchar(255) NOT NULL COMMENT 'iPhone负责人',
  `manager_2` varchar(255) NOT NULL COMMENT 'iPad负责人',
  `manager_1_oc` varchar(255) NOT NULL COMMENT 'iPhone官方商店负责人',
  `manager_2_oc` varchar(255) NOT NULL COMMENT 'iPad官方商店负责人',
  `manager_3` varchar(255) NOT NULL COMMENT 'android负责人',
  `manager_4` varchar(255) NOT NULL COMMENT 'aPad负责人',
  `online_1` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'iPhone是否上线0=>未上线 1=>上线',
  `online_2` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'iPad是否上线0=>未上线 1=>上线',
  `online_1_oc` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'iPhone官方商店是否上线0=>未上线 1=>上线',
  `online_2_oc` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'iPad官方商店是否上线0=>未上线 1=>上线',
  `online_3` tinyint(1) NOT NULL DEFAULT '0' COMMENT ' android是否上线 0=>未上线 1=>上线',
  `online_4` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'aPad是否上线 0=>未上线 1=>上线',
  `appEnv_1` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'iPhone应用商店应用环境  0=》生产环境 1=》开发环境',
  `appEnv_2` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'iPad应用商店应用环境  0=》生产环境 1=》开发环境',
  `appEnv_1_oc` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'iPhone官方商店应用环境  0=》生产环境 1=》开发环境',
  `appEnv_2_oc` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'iPad官方商店应用环境  0=》生产环境 1=》开发环境',
  `appEnv_3` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'android官方商店应用环境  0=》生产环境 1=》开发环境',
  `appEnv_4` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'apad官方商店应用环境  0=》生产环境 1=》开发环境',
  `webCode` varchar(50) NOT NULL DEFAULT '',
  `version_1` varchar(255) NOT NULL COMMENT '上线版本',
  `version_2` varchar(255) NOT NULL COMMENT '上线版本',
  `version_3` varchar(255) NOT NULL COMMENT '上线版本',
  `version_4` varchar(255) NOT NULL COMMENT '上线版本',
  `online_date_1` int(11) NOT NULL DEFAULT '0' COMMENT '上线日期',
  `online_date_2` int(11) NOT NULL DEFAULT '0' COMMENT '上线日期',
  `owebPriv` char(3) NOT NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  `online_date_3` int(11) NOT NULL DEFAULT '0' COMMENT '上线日期',
  `online_date_4` int(11) NOT NULL DEFAULT '0' COMMENT '上线日期',
  `onlineWebsite_1` varchar(255) NOT NULL COMMENT '上线网站',
  `onlineWebsite_2` varchar(255) NOT NULL COMMENT '上线网站',
  `onlineWebsite_3` varchar(255) NOT NULL COMMENT '上线网站',
  `onlineWebsite_4` varchar(255) NOT NULL COMMENT '上线网站',
  `baiduStatisticsKey_1` varchar(255) NOT NULL COMMENT '百度统计Key',
  `baiduStatisticsKey_2` varchar(255) NOT NULL COMMENT '百度统计Key',
  `baiduStatisticsKey_1_oc` varchar(255) NOT NULL COMMENT 'iPhone官方商店百度统计Key',
  `baiduStatisticsKey_2_oc` varchar(255) NOT NULL COMMENT 'iPad官方商店百度统计Key',
  `baiduStatisticsKey_3` varchar(255) NOT NULL COMMENT '百度统计Key',
  `baiduStatisticsKey_4` varchar(255) NOT NULL COMMENT '百度统计Key',
  `projectName_3` varchar(255) NOT NULL COMMENT '工程名称',
  `projectName_4` varchar(255) NOT NULL COMMENT '工程名称',
  `packageName_3` varchar(255) NOT NULL COMMENT '工程包名',
  `packageName_4` varchar(255) NOT NULL COMMENT '工程包名',
  `ctm` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `mtm` int(11) NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  `opUid` int(11) NOT NULL DEFAULT '0' COMMENT '操作人',
  `mainColor` char(7) DEFAULT '#5BAEF5' COMMENT '主题颜色',
  `subColor` char(7) DEFAULT '#6388AE' COMMENT '第二主题色',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_manager`
--

LOCK TABLES `app_manager` WRITE;
/*!40000 ALTER TABLE `app_manager` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_manager_log`
--

DROP TABLE IF EXISTS `app_manager_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_manager_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL COMMENT '学校id',
  `uid` int(11) NOT NULL COMMENT '操作人uid',
  `platform` tinyint(1) NOT NULL COMMENT '信息管理 1=>基本  2=>平台 3=>版本',
  `appType` tinyint(1) NOT NULL COMMENT '应用平台类型 1=> iPhone 2=>iPad 3=>android 4=>aPad 5=>H5',
  `behavior` tinyint(1) NOT NULL COMMENT '行为 0=>修改  1=>添加   2=>删除  3=>上传  4=>下载 ',
  `field` varchar(50) NOT NULL COMMENT '字段名',
  `content` varchar(500) NOT NULL COMMENT '修改内容',
  `ctm` int(11) NOT NULL COMMENT '创建时间',
  `webCode` char(15) NOT NULL DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) NOT NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_manager_log`
--

LOCK TABLES `app_manager_log` WRITE;
/*!40000 ALTER TABLE `app_manager_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_manager_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_version`
--

DROP TABLE IF EXISTS `app_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_version` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `managerId` int(11) NOT NULL COMMENT 'app开发管理Id',
  `version` varchar(50) NOT NULL COMMENT '版本号',
  `appType` tinyint(1) NOT NULL COMMENT '应用平台类型 1=> iPhone 2=>iPad 3=>android 4=>aPad 5=>H5',
  `upLocation` tinyint(1) NOT NULL COMMENT '上架位置 0=》应用商店  1=》官方商店',
  `isVerify` tinyint(1) NOT NULL COMMENT '是否通过审核 0=>No 1=>YES',
  `upTime` int(11) NOT NULL COMMENT '上线时间',
  `depict` text NOT NULL COMMENT '版本描述',
  `opUid` int(11) NOT NULL COMMENT '编辑者Uid',
  `ctm` int(11) NOT NULL COMMENT '创建时间',
  `mtm` int(11) NOT NULL COMMENT '编辑时间',
  `webCode` char(15) NOT NULL DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) NOT NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_version`
--

LOCK TABLES `app_version` WRITE;
/*!40000 ALTER TABLE `app_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '缓存ID',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '缓存名称',
  `data` longblob COMMENT '缓存数据',
  `serialized` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '缓存是否为序列化的标记位',
  `expiredTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缓存过期时间',
  `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '缓存创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `expiredTime` (`expiredTime`) USING BTREE,
  KEY `name_webCode` (`name`,`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `code` varchar(64) NOT NULL DEFAULT '' COMMENT '分类编码',
  `name` varchar(255) NOT NULL COMMENT '分类名称',
  `courseCode` varchar(10) NOT NULL COMMENT '学院课程前缀',
  `iconFromType` enum('system','define') NOT NULL DEFAULT 'define',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '分类完整路径',
  `weight` int(11) NOT NULL DEFAULT '0' COMMENT '分类权重',
  `groupId` int(10) unsigned NOT NULL COMMENT '分类组ID',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父分类ID',
  `level` tinyint(3) NOT NULL DEFAULT '1' COMMENT '层级：顶级=1 次级=2 重级=3 依次类推',
  `isLeafNode` tinyint(1) DEFAULT '0' COMMENT '是否叶结点: 0=>非叶结点  1=>叶结点',
  `isSupper` tinyint(1) DEFAULT '0' COMMENT '是否超管建',
  `description` text,
  `createUid` int(11) NOT NULL COMMENT '创建人',
  `ctm` int(11) NOT NULL COMMENT '创建时间',
  `isDelete` tinyint(1) DEFAULT '0' COMMENT '是否已删除',
  PRIMARY KEY (`id`),
  KEY `uri_webCode` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'test1','计算机学院','11','system','_信息工程系.png','',0,1,0,1,0,1,'',1,1461306566,0),(4,'test2','信息工程学院','10','system','','',0,1,0,1,0,1,NULL,1,146130586,0);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_group`
--

DROP TABLE IF EXISTS `category_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类组ID',
  `code` varchar(64) NOT NULL COMMENT '分类组编码',
  `name` varchar(255) NOT NULL COMMENT '分类组名称',
  `depth` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '该组下分类允许的最大层级数',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_group`
--

LOCK TABLES `category_group` WRITE;
/*!40000 ALTER TABLE `category_group` DISABLE KEYS */;
INSERT INTO `category_group` VALUES (1,'course','è¯¾ç¨‹åˆ†ç±»',2,'test','r');
/*!40000 ALTER TABLE `category_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_list`
--

DROP TABLE IF EXISTS `cms_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT '' COMMENT '落地页标题',
  `keywords` varchar(300) DEFAULT '' COMMENT '落地页keywords',
  `description` varchar(500) DEFAULT '' COMMENT '落地页description',
  `statisticalCode` varchar(300) DEFAULT '' COMMENT '统计代码',
  `consultCode` varchar(300) DEFAULT '' COMMENT '咨询代码',
  `userId` int(11) DEFAULT '0' COMMENT '创建人id',
  `updUid` int(11) DEFAULT '0' COMMENT '修改人id',
  `createdTime` int(11) DEFAULT '0' COMMENT '创建时间',
  `updTime` int(11) DEFAULT '0' COMMENT '修改时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '0代表为发布。1代表已经发布',
  `agstatus` tinyint(1) DEFAULT '0' COMMENT '0代表未删除，1代表已删除',
  `webCode` varchar(20) DEFAULT '' COMMENT 'webCode',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_list`
--

LOCK TABLES `cms_list` WRITE;
/*!40000 ALTER TABLE `cms_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_modular`
--

DROP TABLE IF EXISTS `cms_modular`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_modular` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cmsId` int(11) DEFAULT '0' COMMENT '落地页id',
  `cmsType` tinyint(1) DEFAULT '0' COMMENT '0导航1轮播图2图片模块3购买模块',
  `title` varchar(30) DEFAULT '' COMMENT '模块名称',
  `sort` tinyint(4) DEFAULT '0' COMMENT '排序',
  `price` float(10,2) DEFAULT '0.00' COMMENT '原始价格',
  `preferentialPrice` float(10,2) DEFAULT '0.00' COMMENT '优惠价格',
  `buyUrl` varchar(300) DEFAULT '' COMMENT '购买地址',
  `createdTime` int(11) DEFAULT '0' COMMENT '创建时间',
  `webCode` varchar(20) DEFAULT '' COMMENT 'webCode',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  `CourseName` varchar(30) DEFAULT '' COMMENT '课程名称',
  `ButtonDescribe` varchar(30) DEFAULT '' COMMENT '按钮描述',
  `color` varchar(10) DEFAULT '' COMMENT '背景颜色',
  `height` varchar(4) DEFAULT '' COMMENT '模块高度',
  `direction` varchar(10) DEFAULT '' COMMENT '图片轮播切换方向',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_modular`
--

LOCK TABLES `cms_modular` WRITE;
/*!40000 ALTER TABLE `cms_modular` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_modular` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_modular_config`
--

DROP TABLE IF EXISTS `cms_modular_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cms_modular_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cmsModId` int(11) DEFAULT '0' COMMENT '模块id',
  `picPath` varchar(300) DEFAULT '' COMMENT '图片地址',
  `url` varchar(300) DEFAULT '' COMMENT '链接地址',
  `videoPath` varchar(300) DEFAULT '' COMMENT '视频地址',
  `title` varchar(300) DEFAULT '' COMMENT '模块名称',
  `anchorId` int(11) DEFAULT '0' COMMENT '模块锚点id',
  `sort` tinyint(4) DEFAULT '0' COMMENT '排序',
  `createdTime` int(11) DEFAULT '0' COMMENT '创建时间',
  `webCode` varchar(20) DEFAULT '' COMMENT 'webCode',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  `color` char(10) DEFAULT '',
  `mediaSource` char(10) DEFAULT '',
  `polyvVid` varchar(300) DEFAULT '' COMMENT '保利威视视频id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cms_modular_config`
--

LOCK TABLES `cms_modular_config` WRITE;
/*!40000 ALTER TABLE `cms_modular_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_modular_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_anonymous`
--

DROP TABLE IF EXISTS `cmt_anonymous`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_anonymous` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `nickName` char(50) NOT NULL COMMENT '匿名',
  `cmtStatId` int(11) NOT NULL COMMENT '评论统计id',
  `ctm` int(11) NOT NULL COMMENT '创建时间',
  `isDel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1=>已删除',
  `IP` char(15) NOT NULL COMMENT '匿名评论者ip地址',
  `webCode` char(15) NOT NULL DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) NOT NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_anonymous`
--

LOCK TABLES `cmt_anonymous` WRITE;
/*!40000 ALTER TABLE `cmt_anonymous` DISABLE KEYS */;
/*!40000 ALTER TABLE `cmt_anonymous` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_list`
--

DROP TABLE IF EXISTS `cmt_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `cmtStatId` int(11) NOT NULL COMMENT '评论统计id',
  `comment` varchar(500) NOT NULL COMMENT '评论内容',
  `sendUid` int(11) NOT NULL DEFAULT '0' COMMENT '发送人',
  `sendName` char(50) NOT NULL COMMENT '发送人姓名',
  `replyCnt` int(11) NOT NULL DEFAULT '0' COMMENT '回复数',
  `ctm` int(11) NOT NULL COMMENT '创建时间',
  `isDel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1=>已删除',
  `IP` char(15) NOT NULL COMMENT '评论者ip地址',
  `webCode` char(15) NOT NULL DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) NOT NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_list`
--

LOCK TABLES `cmt_list` WRITE;
/*!40000 ALTER TABLE `cmt_list` DISABLE KEYS */;
INSERT INTO `cmt_list` VALUES (1,1,'好美的花儿。',4,'斯路游戏',0,1466144007,0,'192.168.30.1','tlc','-');
/*!40000 ALTER TABLE `cmt_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_reply_list`
--

DROP TABLE IF EXISTS `cmt_reply_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_reply_list` (
  `id` int(1) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `cmtStatId` int(11) NOT NULL COMMENT '评论统计id',
  `cmtId` int(11) NOT NULL COMMENT '评论id',
  `pid` int(11) NOT NULL COMMENT '所回复的id(父id）',
  `pidSta` int(11) NOT NULL COMMENT '回复所在的一级回复id',
  `reply` varchar(500) NOT NULL COMMENT '回复',
  `replyUid` int(11) NOT NULL COMMENT '回复人',
  `replyName` char(50) NOT NULL COMMENT '回复人姓名',
  `ctm` int(11) NOT NULL COMMENT '创建时间',
  `isDel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 1=>已删除',
  `IP` char(15) NOT NULL COMMENT '评论者ip地址',
  `webCode` char(15) NOT NULL DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) NOT NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_reply_list`
--

LOCK TABLES `cmt_reply_list` WRITE;
/*!40000 ALTER TABLE `cmt_reply_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `cmt_reply_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_stat`
--

DROP TABLE IF EXISTS `cmt_stat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_stat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `createUid` char(20) NOT NULL COMMENT '页面创建者uid（如海报创建者uid）',
  `cmtType` char(20) NOT NULL COMMENT '组合主键1',
  `cmtIdStr` char(50) NOT NULL COMMENT '组合主键2',
  `cmtCnt` int(11) NOT NULL DEFAULT '0' COMMENT '评论条数',
  `userCnt` int(11) NOT NULL DEFAULT '0' COMMENT '参与人数',
  `ctm` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `webCode` char(15) NOT NULL DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_stat`
--

LOCK TABLES `cmt_stat` WRITE;
/*!40000 ALTER TABLE `cmt_stat` DISABLE KEYS */;
INSERT INTO `cmt_stat` VALUES (1,'1','Poster','1',1,1,1466144007,'tlc','-');
/*!40000 ALTER TABLE `cmt_stat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colleges`
--

DROP TABLE IF EXISTS `colleges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `colleges` (
  `college_id` char(20) NOT NULL,
  `college_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`college_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colleges`
--

LOCK TABLES `colleges` WRITE;
/*!40000 ALTER TABLE `colleges` DISABLE KEYS */;
INSERT INTO `colleges` VALUES ('31','信息工程学院'),('32','计算机学院'),('33','理学院');
/*!40000 ALTER TABLE `colleges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `objectType` varchar(32) NOT NULL,
  `objectId` int(10) unsigned NOT NULL,
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `createdTime` int(10) unsigned NOT NULL,
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `objectType` (`objectType`,`objectId`) USING BTREE,
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '内容ID',
  `title` varchar(255) NOT NULL COMMENT '内容标题',
  `editor` enum('richeditor','none') NOT NULL DEFAULT 'richeditor' COMMENT '编辑器选择类型字段',
  `type` varchar(255) NOT NULL COMMENT '内容类型',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT '内容别名',
  `summary` text COMMENT '内容摘要',
  `body` text COMMENT '内容正文',
  `picture` varchar(255) NOT NULL DEFAULT '' COMMENT '内容头图',
  `template` varchar(255) NOT NULL DEFAULT '' COMMENT '内容模板',
  `status` enum('published','unpublished','trash') NOT NULL COMMENT '内容状态',
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容分类ID',
  `tagIds` tinytext COMMENT '内容标签ID',
  `hits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容点击量',
  `featured` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '是否头条',
  `promoted` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否推荐',
  `sticky` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `userId` int(10) unsigned NOT NULL COMMENT '发布人ID',
  `field1` text COMMENT '扩展字段',
  `field2` text COMMENT '扩展字段',
  `field3` text COMMENT '扩展字段',
  `field4` text COMMENT '扩展字段',
  `field5` text COMMENT '扩展字段',
  `field6` text COMMENT '扩展字段',
  `field7` text COMMENT '扩展字段',
  `field8` text COMMENT '扩展字段',
  `field9` text COMMENT '扩展字段',
  `field10` text COMMENT '扩展字段',
  `publishedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `createdTime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content`
--

LOCK TABLES `content` WRITE;
/*!40000 ALTER TABLE `content` DISABLE KEYS */;
INSERT INTO `content` VALUES (1,'测试页面','richeditor','page','test',NULL,'<p>这是一个测试页面</p>','','default','trash',0,NULL,0,0,0,0,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1474116806,1474116832,'','-');
/*!40000 ALTER TABLE `content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程ID',
  `title` varchar(1024) NOT NULL COMMENT '课程标题',
  `subtitle` varchar(1024) NOT NULL DEFAULT '' COMMENT '课程副标题',
  `number` char(20) NOT NULL DEFAULT '0' COMMENT '课程编号',
  `lesson_address` varchar(255) NOT NULL DEFAULT '',
  `lesson_time` varchar(255) NOT NULL DEFAULT '',
  `status` enum('draft','published','closed') NOT NULL DEFAULT 'draft' COMMENT '课程状态',
  `type` enum('cooperation','normal') NOT NULL DEFAULT 'normal' COMMENT '课程类型',
  `lessonNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '课时数',
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `tags` text COMMENT '标签IDs',
  `selectPicture` varchar(255) NOT NULL DEFAULT '' COMMENT '所选课程图片路径',
  `smallPicture` varchar(255) NOT NULL DEFAULT '' COMMENT '小图',
  `middlePicture` varchar(255) NOT NULL DEFAULT '' COMMENT '中图',
  `largePicture` varchar(255) NOT NULL DEFAULT '' COMMENT '大图',
  `about` text COMMENT '简介',
  `teacherIds` text COMMENT '显示的课程教师IDs',
  `recommended` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为推荐课程',
  `recommendedSeq` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推荐序号',
  `recommendedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推荐时间',
  `userId` int(10) unsigned NOT NULL COMMENT '课程发布人ID',
  `createdTime` int(10) unsigned NOT NULL COMMENT '课程创建时间',
  `isCourse` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1=>大课,0=>小课',
  `isDeleted` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否已被删除',
  `viewCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量，最大10000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course`
--

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;
INSERT INTO `course` VALUES (2,'大数据基础课程','','0','','','closed','normal',16,1,'','','public://course/2017/06-01/000219bc2305420004.jpg','public://course/2017/06-01/000219bc16af362783.jpg','public://course/2017/06-01/000219bbccec512735.jpg','<p>本课程主要介绍大数据基础入门知识，以及相关基础实验环境搭建与使用。</p>','|3|',0,0,0,3,1492308082,1,0,37),(3,'Hadoop基础框架','','0001','','','published','normal',16,1,'','','public://course/2017/05-31/190652c8db49724458.jpg','public://course/2017/05-31/190652c8ced4959601.jpg','public://course/2017/05-31/190652c89e05259390.jpg','','|3|',0,0,0,3,1496044074,1,0,28),(4,'HBase基础和进阶','','0002','','','published','normal',10,1,'','','public://course/2017/05-31/19151642168c007266.jpg','public://course/2017/05-31/191516420656422951.jpg','public://course/2017/05-31/19151641cdfc728985.jpg','','|3|',0,0,0,3,1496045767,1,0,8),(5,'Hive基础和进阶','','0003','','','published','normal',0,1,'','','public://course/2017/05-31/191732c7d1f6881432.jpg','public://course/2017/05-31/191732c7c5ed616441.jpg','public://course/2017/05-31/191732c79f1d037256.jpg','','|3|',0,0,0,3,1496046405,1,0,9),(6,'Spark高级应用和实战','','0004','','','published','normal',0,1,'','','public://course/2017/05-31/19181026254c553196.png','public://course/2017/05-31/191810261506726582.png','public://course/2017/05-31/19181025c82a939960.png','','|3|',0,0,0,3,1496046682,1,0,0),(7,'大数据协同框架','','0005','','','published','normal',0,1,'','','public://course/2017/05-31/192858a6cbdf085513.jpg','public://course/2017/05-31/192858a6bb9b361489.jpg','public://course/2017/05-31/192858a674bb290907.jpg','','|3|',0,0,0,3,1496046759,1,0,7),(8,'流式数据处理','','0006','','','published','normal',0,1,'','','public://course/2017/05-31/192428c90277195099.png','public://course/2017/05-31/192428c8eedf857236.png','public://course/2017/05-31/192428c89db5260112.png','','|3|',0,0,0,3,1496046906,1,0,0),(9,'内存计算框架技术&Spark','','0007','','','published','normal',0,1,'','','public://course/2017/05-31/1930560680dd767316.jpg','public://course/2017/05-31/193056066949636457.jpg','public://course/2017/05-31/193056063405250728.jpg','','|3|',0,0,0,3,1496047058,1,0,37),(10,'OpenStack','','0008','','','published','normal',0,1,'','','public://course/2017/06-21/225555b2a2ab573095.jpg','public://course/2017/06-21/225555b29356522658.jpg','public://course/2017/06-21/225555b26af5097281.jpg','','|3|',0,0,0,3,1498056846,1,0,13),(11,'test','','0001','','','draft','normal',0,4,'','','','','','','|3|',0,0,0,3,1503583461,1,0,0);
/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_announcement`
--

DROP TABLE IF EXISTS `course_announcement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_announcement` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '课程公告ID',
  `userId` int(10) NOT NULL COMMENT '公告发布人ID',
  `courseId` int(10) NOT NULL COMMENT '公告所属课程ID',
  `content` text NOT NULL COMMENT '公告内容',
  `createdTime` int(10) NOT NULL COMMENT '公告创建时间',
  `updatedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公告最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_announcement`
--

LOCK TABLES `course_announcement` WRITE;
/*!40000 ALTER TABLE `course_announcement` DISABLE KEYS */;
INSERT INTO `course_announcement` VALUES (1,12,5,'<p>毛泽东概论开卷考试，请奔走相告！</p>',1468929760,0),(2,16,9,'<p>课程资料稍后上传！啦啦啦</p>',1469101477,0),(3,12,21,'<p>数据结构与算法本周五晚上7点10分主楼A106考试</p>',1471419826,0),(4,12,21,'啦啦啦啦，我是卖报的小当家',1471420061,1474986001),(6,1,23,'<p>试一下，我这是被邀请的课，看能不能添加公告</p>',1476005210,0),(7,80,23,'这是来自接口添加的课程公告！！！',1480914386,1480914386),(8,230,20,'<p>管理员修改公告啦，各部门注意啦</p>',1490706205,0);
/*!40000 ALTER TABLE `course_announcement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_category_rel`
--

DROP TABLE IF EXISTS `course_category_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_category_rel` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `courseId` int(10) NOT NULL,
  `categoryId` int(10) NOT NULL,
  `ctm` int(11) NOT NULL,
  `uid` int(10) NOT NULL,
  `microTm` varchar(32) NOT NULL DEFAULT '' COMMENT '微秒',
  `isTest` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否初始数据',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_category_rel`
--

LOCK TABLES `course_category_rel` WRITE;
/*!40000 ALTER TABLE `course_category_rel` DISABLE KEYS */;
INSERT INTO `course_category_rel` VALUES (1,1,0,1461301216,1,'1461301216.298101',0),(3,4,2,1461306606,1,'1461306606.991202',0),(4,3,2,1461306627,1,'1461306627.175327',0),(7,5,2,1468929405,12,'1468929405.658132',0),(6,6,2,1468462724,1,'1468462724.972002',0),(8,7,2,1468929992,12,'1468929992.120764',0),(11,8,5,1469508067,16,'1469508067.595318',0),(10,9,2,1469101376,16,'1469101376.265151',0);
/*!40000 ALTER TABLE `course_category_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_chapter`
--

DROP TABLE IF EXISTS `course_chapter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_chapter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程章节ID',
  `courseId` int(10) unsigned NOT NULL COMMENT '章节所属课程ID',
  `type` enum('chapter','unit') NOT NULL DEFAULT 'chapter' COMMENT '章节类型：chapter为章节，unit为单元。',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'parentId大于０时为单元',
  `number` int(10) unsigned NOT NULL COMMENT '章节编号',
  `seq` int(10) unsigned NOT NULL COMMENT '章节序号',
  `title` varchar(255) NOT NULL COMMENT '章节名称',
  `createdTime` int(10) unsigned NOT NULL COMMENT '章节创建时间',
  `isTest` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否初始数据',
  `description` text COMMENT '章节描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_chapter`
--

LOCK TABLES `course_chapter` WRITE;
/*!40000 ALTER TABLE `course_chapter` DISABLE KEYS */;
INSERT INTO `course_chapter` VALUES (7,2,'chapter',0,1,1,'Linux简介',1494726868,0,'本章主要介绍 \r\n  1. 什么是Linux；2 Linux的主要版本；3 Linux的优缺点；4 RedCloudLab云盘的简单使用'),(8,2,'unit',7,1,2,'什么是Linux',1494726885,0,''),(10,2,'unit',7,2,4,'Linux的主要版本',1494726916,0,''),(11,2,'chapter',0,2,8,'Hadoop入门',1494727435,0,''),(12,2,'unit',11,1,9,'Hadoop概述',1494727658,0,''),(13,2,'unit',7,3,11,'Linux的优缺点',1494727796,0,''),(15,2,'chapter',0,3,17,'Hdfs基本使用',1494728066,0,''),(16,2,'unit',15,1,18,'Hdfs的基本使用',1494728120,0,''),(17,2,'chapter',0,4,19,'HBase 介绍',1494728248,0,''),(18,2,'unit',17,1,20,'Hbase概述',1494728307,0,''),(19,2,'unit',17,2,22,'HBase术语及数据模型',1494728428,0,''),(20,2,'unit',17,3,24,'HBase 环境安装配置',1494728482,0,''),(21,2,'chapter',0,5,25,'HBase shell 基本使用',1494728550,0,'本章主要介绍Hbase shell 的语法与简单使用'),(22,2,'unit',21,1,26,'HBase shell 操作',1494728586,0,''),(23,2,'chapter',0,6,27,'Hive初探',1494728649,0,'本章主要介绍\r\n1 Hive的简介；2 Hive与HBase的关系; 3 Hive环境的搭建'),(24,2,'unit',23,1,28,'Hive简介',1494728661,0,''),(25,2,'unit',23,2,29,'Hive与HBase的关系',1494728675,0,''),(26,2,'unit',23,3,30,'Hive环境搭建',1494728687,0,''),(27,2,'chapter',0,7,33,'Hive Sql的基本操作',1494728769,0,''),(28,2,'unit',27,1,34,'Hive Sql基本命令',1494728792,0,''),(29,2,'chapter',0,8,35,'Spark简介',1494728842,0,'本章主要介绍\r\n1 Spark的基本概念；2 Spark的计算模型'),(30,2,'unit',29,1,36,'Spark简介',1494728904,0,''),(31,2,'unit',29,2,37,'Spark计算模型',1494728918,0,''),(32,2,'unit',29,3,38,'Spark环境搭建',1494728930,0,''),(33,2,'unit',11,2,41,'Hadoop安装配置',1494745302,0,''),(34,3,'chapter',0,1,1,'初识Hadoop',1496044295,0,''),(35,3,'unit',34,1,2,'初识Hadoop（上）',1496044379,0,''),(36,3,'unit',34,2,3,'初识Hadoop（下）',1496044394,0,''),(37,3,'chapter',0,2,6,'Hadoop集群伪分布式搭建',1496044635,0,''),(38,3,'unit',37,1,7,'Hadoop集群伪分布式搭建指南',1496044665,0,''),(39,3,'unit',37,2,8,'Hadoop集群伪分布式搭建视频案例',1496044687,0,''),(40,3,'chapter',0,3,13,'HDFS文件系统',1496044827,0,''),(41,3,'unit',40,1,14,'Hdfs文件系统（上）',1496044847,0,''),(42,3,'unit',40,2,15,'Hdfs文件系统（下）',1496044865,0,''),(43,3,'chapter',0,4,18,'Hadoop基本操作',1496044970,0,''),(44,3,'unit',43,1,19,'Hadoop基本操作指南',1496045007,0,''),(45,3,'chapter',0,5,22,'MapReduce计算框架',1496045136,0,''),(46,3,'unit',45,1,23,'MapReduce计算框架（上）',1496045151,0,''),(47,3,'unit',45,2,24,'MapReduce计算框架（下）',1496045208,0,''),(48,3,'chapter',0,6,27,'MapReduce基本实验',1496045333,0,''),(49,3,'unit',48,1,28,'MapReduce实验（一）',1496045370,0,''),(50,3,'unit',48,2,29,'MapReduce实验（二）',1496045382,0,''),(51,3,'unit',43,2,32,'HDFS操作视频案例',1496045460,0,''),(52,3,'chapter',0,7,34,'Hadoop2.x集群维护',1496045631,0,''),(53,4,'chapter',0,1,1,'HBase概述',1496045848,0,''),(54,4,'unit',53,1,2,'HBase概述',1496045861,0,''),(55,4,'chapter',0,2,4,'HBase体系架构',1496045910,0,''),(56,4,'chapter',0,3,5,'HBase常用操作',1496045921,0,''),(57,4,'chapter',0,4,6,'HBase架构详解',1496045943,0,''),(58,4,'chapter',0,5,7,'HBase高级特性',1496045957,0,''),(59,4,'chapter',0,6,8,'HBase表操作',1496045975,0,''),(60,4,'unit',55,1,9,'HBase体系架构',1496046050,0,''),(61,4,'unit',56,1,11,'HBase常用操作',1496046100,0,''),(62,4,'unit',57,1,13,'HBase架构详解',1496046148,0,''),(63,4,'unit',58,1,15,'HBase高级特性',1496046221,0,''),(64,4,'unit',59,1,17,'HBase表操作',1496046250,0,''),(65,5,'chapter',0,1,1,'Hive概述',1496046440,0,''),(66,5,'chapter',0,2,2,'Hive的体系结构',1496046455,0,''),(67,5,'chapter',0,3,3,'Hive数据类型和文件格式',1496046476,0,''),(68,5,'chapter',0,4,4,'Hive常用操作方式',1496046486,0,''),(69,5,'chapter',0,5,5,'HiveQL数据定义',1496046498,0,''),(70,5,'chapter',0,6,6,'HiveQL查询',1496046516,0,''),(71,5,'chapter',0,7,7,'自定义函数（Hive进阶）',1496046532,0,''),(72,5,'chapter',0,8,8,'数据文件存储（Hive进阶）',1496046548,0,''),(73,5,'chapter',0,9,9,'Join优化（Hive进阶）',1496046565,0,''),(74,5,'chapter',0,10,10,'数据倾斜（Hive进阶）',1496046580,0,''),(75,5,'chapter',0,11,11,'Hive中的MapReduce任务数控制（Hive进阶）',1496046611,0,''),(76,6,'chapter',0,1,1,'Spark on Yarn',1496046699,0,''),(77,6,'chapter',0,2,2,'Spark HistoryServer',1496046713,0,''),(78,6,'chapter',0,3,3,'Spark Streaming',1496046722,0,''),(79,6,'chapter',0,4,4,'Spark 项目实战',1496046734,0,''),(80,7,'chapter',0,1,1,'大数据协作框架_Sqoop',1496046791,0,''),(81,7,'chapter',0,2,2,'大数据协作框架_Flume',1496046837,0,''),(82,7,'chapter',0,3,3,'大数据协作框架_Kafka',1496046856,0,''),(83,7,'chapter',0,4,4,'大数据协作框架_Phoenix',1496046872,0,''),(84,8,'chapter',0,1,1,'实时流式计算概述',1496046936,0,''),(85,8,'chapter',0,2,2,'实时流式计算的应用市场及关键技术',1496046957,0,''),(86,8,'chapter',0,3,3,'Storm概述',1496046968,0,''),(87,8,'chapter',0,4,4,'Storm的架构和原理',1496046983,0,''),(88,8,'chapter',0,5,5,'Storm集群搭建部署',1496046999,0,''),(89,8,'chapter',0,6,6,'Storm常见问题与解决方案',1496047023,0,''),(90,9,'chapter',0,1,1,'Spark以及Spark生态圈的介绍',1496047084,0,''),(91,9,'chapter',0,2,2,'Spark集群搭建',1496047094,0,''),(92,9,'chapter',0,3,3,'Spark应用提交工具',1496047106,0,''),(93,9,'chapter',0,4,4,'Scala基本知识讲解',1496047122,0,''),(94,9,'chapter',0,5,5,'RDD特性、常见操作、缓存策略',1496047161,0,''),(95,9,'chapter',0,6,6,'RDD Dependency、Stage',1496047176,0,''),(96,9,'chapter',0,7,7,'Spark核心组件概述及案例分析',1496047203,0,''),(97,4,'unit',55,2,20,'HBase环境搭建视频案例',1496061757,0,''),(98,4,'unit',56,2,22,'HBase基本操作视频案例',1496061931,0,''),(99,5,'unit',65,1,12,'Hive概述',1496062007,0,''),(100,5,'unit',66,1,14,'Hive的体系结构',1496062044,0,''),(101,5,'unit',67,1,16,'Hive的数据类型和文件格式',1496062112,0,''),(102,5,'unit',68,1,18,'Hive的常用操作方式',1496062156,0,''),(103,5,'unit',69,1,20,'HiveQL数据定义',1496062237,0,''),(104,5,'unit',70,1,22,'HiveQL查询',1496062277,0,''),(105,5,'unit',66,2,24,'Hive环境搭建视频案例',1496062340,0,''),(106,5,'unit',69,2,26,'Hive Sql基本操作视频案例',1496062407,0,''),(107,6,'unit',76,1,5,'Spark on Yarn',1496062896,0,''),(108,6,'unit',77,1,7,'Spark HistoryServer',1496062955,0,''),(109,6,'unit',78,1,9,'Spark Streaming',1496062995,0,''),(110,6,'unit',79,1,11,'Spark 项目实战',1496063059,0,''),(111,7,'unit',80,1,5,'大数据协作框架_Sqoop（上）',1496063178,0,''),(112,7,'unit',80,2,7,'大数据协作框架_Sqoop （下）',1496063242,0,''),(113,7,'unit',81,1,9,'大数据协作框架_Flume',1496063291,0,''),(114,7,'unit',82,1,11,'大数据协作框架_Kafka',1496063336,0,''),(115,7,'unit',83,1,13,'大数据协作框架_Phoenix',1496063378,0,''),(116,8,'unit',84,1,7,'实时流式计算概述',1496063710,0,''),(117,8,'unit',85,1,9,'实时流式计算的应用市场及关键技术',1496063762,0,''),(118,8,'unit',86,1,11,'Storm概述',1496063807,0,''),(119,8,'unit',87,1,13,'Storm的架构和原理',1496063842,0,''),(120,8,'unit',88,1,15,'Storm集群搭建部署',1496063879,0,''),(121,8,'unit',89,1,17,'Storm常见问题与解决方案',1496063921,0,''),(122,8,'unit',89,2,19,'流式大数据处理（讲义）',1496063989,0,''),(123,9,'unit',90,1,8,'Spark以及Spark生态圈的介绍',1496064036,0,''),(124,9,'unit',91,1,10,'Spark集群搭建',1496064073,0,''),(126,9,'unit',92,1,12,'Spark应用提交工具',1496064162,0,''),(127,9,'unit',93,1,14,'Scala基本只是讲解',1496064208,0,''),(128,9,'unit',93,2,16,'Scala基本操作视频案例',1496064259,0,''),(129,9,'unit',94,1,18,'RDD特性、常见操作、缓存策略',1496064325,0,''),(130,9,'unit',95,1,20,'RDD Dependency、Stage',1496064394,0,''),(131,9,'unit',96,1,22,'Spark核心组件概述及案例分析',1496064432,0,''),(132,9,'unit',96,2,24,'Spark程序开发实验指南',1496064486,0,''),(133,10,'chapter',0,1,1,'初识OpenStack',1498056991,0,''),(134,10,'unit',133,1,2,'OpenStack的主要组件',1498057018,0,''),(135,10,'unit',133,2,3,'DevStack部署OpenStack',1498057030,0,''),(136,11,'chapter',0,1,1,'test',1503583492,0,'');
/*!40000 ALTER TABLE `course_chapter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_cooperation`
--

DROP TABLE IF EXISTS `course_cooperation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_cooperation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程协作表，多个教师共同上同一门课',
  `courseId` int(10) unsigned NOT NULL COMMENT '课程ID',
  `teacherId` int(10) unsigned NOT NULL COMMENT '教师ID',
  `createTime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_cooperation`
--

LOCK TABLES `course_cooperation` WRITE;
/*!40000 ALTER TABLE `course_cooperation` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_cooperation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_draft`
--

DROP TABLE IF EXISTS `course_draft`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_draft` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `summary` text COMMENT '摘要',
  `courseId` int(10) unsigned NOT NULL COMMENT '课程ID',
  `content` text COMMENT '内容',
  `userId` int(10) unsigned NOT NULL COMMENT '用户ID',
  `lessonId` int(10) unsigned NOT NULL COMMENT '课时ID',
  `createdTime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_draft`
--

LOCK TABLES `course_draft` WRITE;
/*!40000 ALTER TABLE `course_draft` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_draft` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_favorite`
--

DROP TABLE IF EXISTS `course_favorite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏ID',
  `courseId` int(10) unsigned NOT NULL COMMENT '收藏课程的ID',
  `userId` int(10) unsigned NOT NULL COMMENT '收藏人的ID',
  `createdTime` int(10) NOT NULL COMMENT '创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_favorite`
--

LOCK TABLES `course_favorite` WRITE;
/*!40000 ALTER TABLE `course_favorite` DISABLE KEYS */;
INSERT INTO `course_favorite` VALUES (1,5,16,1468905316,'','-'),(2,8,16,1469195105,'','-'),(3,4,16,1469332718,'','-'),(4,20,1,1472040748,'','-'),(5,20,288,1490534323,'','-'),(6,9,1,1502174706,'','-');
/*!40000 ALTER TABLE `course_favorite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_introduce`
--

DROP TABLE IF EXISTS `course_introduce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_introduce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `courseid` int(11) DEFAULT '0' COMMENT '课程id',
  `title` varchar(35) DEFAULT '',
  `imgSrc` varchar(100) NOT NULL COMMENT '图片的上传地址',
  `sort` tinyint(225) DEFAULT '0' COMMENT '图片排序',
  `createdTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `webCode` varchar(20) DEFAULT '',
  `owebPriv` char(3) DEFAULT '-',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_introduce`
--

LOCK TABLES `course_introduce` WRITE;
/*!40000 ALTER TABLE `course_introduce` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_introduce` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_lesson`
--

DROP TABLE IF EXISTS `course_lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_lesson` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '课时ID',
  `courseId` int(10) unsigned NOT NULL COMMENT '课时所属课程ID',
  `chapterId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '课时所属章节ID',
  `number` int(10) unsigned NOT NULL COMMENT '课时编号',
  `seq` int(10) unsigned NOT NULL COMMENT '课时在课程中的序号',
  `status` enum('unpublished','published') NOT NULL DEFAULT 'published' COMMENT '课时状态',
  `title` varchar(512) NOT NULL COMMENT '课时标题',
  `summary` text COMMENT '课时摘要',
  `tags` text COMMENT '课时标签',
  `type` varchar(64) NOT NULL DEFAULT 'text' COMMENT '课时类型',
  `content` text COMMENT '课时正文',
  `giveCredit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学完课时获得的学分',
  `requireCredit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学习课时前，需达到的学分',
  `mediaId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '媒体文件ID',
  `mediaSource` varchar(32) NOT NULL DEFAULT '' COMMENT '媒体文件来源(self:本站上传,youku:优酷)',
  `mediaName` varchar(255) NOT NULL DEFAULT '' COMMENT '媒体文件名称',
  `mediaUri` text COMMENT '媒体文件资源名',
  `polyvVid` varchar(100) DEFAULT NULL COMMENT '保利威视视频id',
  `polyvVideoSize` int(11) DEFAULT '0' COMMENT '保利威视视频长度',
  `polyvExtra` text COMMENT '保利威视视频信息',
  `length` int(11) unsigned DEFAULT NULL COMMENT '时长',
  `materialNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传的资料数量',
  `quizNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '测验题目数量',
  `learnedNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已学的学员数',
  `viewedNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '查看数',
  `startTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '直播课时开始时间',
  `endTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '直播课时结束时间',
  `replayStatus` enum('ungenerated','generating','generated') NOT NULL DEFAULT 'ungenerated',
  `userId` int(10) unsigned NOT NULL COMMENT '发布人ID',
  `testCount` int(11) DEFAULT '0' COMMENT '题目需要添加的数量',
  `createdTime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `isTest` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否初始数据',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_lesson`
--

LOCK TABLES `course_lesson` WRITE;
/*!40000 ALTER TABLE `course_lesson` DISABLE KEYS */;
INSERT INTO `course_lesson` VALUES (5,2,8,1,5,'published','什么是Linux','',NULL,'video','',0,0,67,'self','1.什么是Linux.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494726943,0),(11,2,10,3,13,'published','Linux的主要版本','',NULL,'video','',0,0,73,'self','2.Linux主要版本.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494727988,0),(12,2,13,3,14,'published','Linux的优缺点','',NULL,'video','',0,0,74,'self','3.Linux的优缺点.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494728009,0),(13,2,12,4,15,'published','Hadoop概述','',NULL,'video','',0,0,75,'self','1.Hadoop概述.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494728036,0),(14,2,18,5,21,'published','HBase概述','',NULL,'video','',0,0,76,'self','2.HBase概述.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494728387,0),(15,2,19,6,23,'published','HBase术语及数据模型','',NULL,'video','',0,0,77,'self','2.HBase术语及数据模型.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494728466,0),(16,2,24,7,31,'published','Hive简介','',NULL,'video','',0,0,78,'self','3.Hive简介.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494728705,0),(17,2,25,8,32,'published','Hive与HBase的关系','',NULL,'video','',0,0,79,'self','3.Hive与HBase关系.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494728724,0),(18,2,30,9,39,'published','Spark简介','',NULL,'video','',0,0,80,'self','4.Spark简介.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494728947,0),(19,2,31,10,40,'published','Spark计算模型','',NULL,'video','',0,0,81,'self','4.Spark计算模型.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494728966,0),(20,2,11,11,42,'published','Hadoop安装配置','',NULL,'document','',0,0,82,'self','Hadoop安装配置.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494745327,0),(21,2,15,12,43,'published','HDFS的基本使用','',NULL,'document','',0,0,83,'self','HDFS的基本使用.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494745368,0),(22,2,17,13,44,'published','HBase 环境安装配置','',NULL,'document','',0,0,84,'self','HBase安装配置 .pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494745405,0),(23,2,21,14,45,'published','HBase Shell的基本使用','',NULL,'document','',0,0,85,'self','HBase Shell的基本使用 .pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494745448,0),(26,2,29,17,48,'published','Spark安装与操作','',NULL,'document','',0,0,88,'self','Spark安装与操作.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1494745560,0),(27,2,23,17,49,'published','Hive安装配置','',NULL,'document','',0,0,89,'self','Hive安装配置.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1495545827,0),(28,2,27,17,50,'published','Hive Sql基本命令','',NULL,'document','',0,0,90,'self','Hive Sql的基本命令 .pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1495545858,0),(29,3,35,1,4,'published','初识Hadoop （上）','',NULL,'document','',0,0,91,'self','第4章_第1节_初识Hadoop(上）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496044474,0),(30,3,36,2,5,'published','初识Hadoop（下）','',NULL,'document','',0,0,93,'self','第4章_第1节_初识Hadoop（下）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496044541,0),(31,3,38,3,9,'published','Hadoop集群伪分布式搭建指南','',NULL,'document','',0,0,94,'self','课程3实验5 HADOOP集群伪分布式搭建-实验指南.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496044719,0),(35,3,41,7,16,'published','HDFS文件系统（上）','',NULL,'document','',0,0,98,'self','第4章_第2节_HDFS文件系统（上）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496044894,0),(36,3,42,8,17,'published','HDFS文件系统（下）','',NULL,'document','',0,0,99,'self','第4章_第2节_HDFS文件系统（下）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496044916,0),(37,3,44,9,20,'published','Hadoop基本操作指南','',NULL,'document','',0,0,100,'self','课程3实验6HADOOP基本操作-实验指南.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496045071,0),(38,3,43,10,21,'published','Hadoop Shell 学习资料','',NULL,'document','',0,0,101,'self','hadoop-shell-学习资料.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496045107,0),(39,3,46,11,25,'published','MapReduce计算框架（上）','',NULL,'document','',0,0,102,'self','第4章_第3节_Map-Reduce计算框架（上）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496045230,0),(40,3,47,12,26,'published','MapReduce计算框架（下）','',NULL,'document','',0,0,103,'self','第4章_第3节_Map-Reduce计算框架(下）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496045256,0),(41,3,49,13,30,'published','MapReduce实验（一）','',NULL,'document','',0,0,104,'self','Hadoop实验-MapReduce(1).pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496045405,0),(42,3,50,14,31,'published','MapReduce实验（二）','',NULL,'document','',0,0,105,'self','Hadoop实验-MapReduce(2).pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496045435,0),(45,3,52,16,35,'published','Hadoop2.x集群维护','',NULL,'document','',0,0,108,'self','第4章_第4节_Hadoop2.x集群维护（上）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496045671,0),(46,3,52,17,36,'published','Hadoop2.x框架基础（讲义）','',NULL,'document','',0,0,109,'self','第四章讲义- Hadoop2.x框架基础.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496045720,0),(47,4,54,1,3,'published','HBase概述','',NULL,'document','',0,0,110,'self','第9章第1节_HBase概述.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496045879,0),(48,4,60,2,10,'published','HBase体系架构','',NULL,'document','',0,0,111,'self','第9章第2节_HBase体系架构.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496046080,0),(49,4,61,3,12,'published','HBase常用操作','',NULL,'document','',0,0,112,'self','第9章第3节_HBase常用操作.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496046137,0),(51,4,62,4,14,'published','HBase架构详解','',NULL,'document','',0,0,114,'self','第10章第1节_HBase架构详解.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496046212,0),(52,4,63,5,16,'published','HBase高级特性','',NULL,'document','',0,0,115,'self','第10章第2节_HBase高级特性.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496046240,0),(53,4,64,6,18,'published','HBase表操作','',NULL,'document','',0,0,116,'self','第10章第3节_HBase表操作.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496046268,0),(54,4,55,7,19,'published','HBase安装和系列实验','',NULL,'document','',0,0,117,'self','HBase的安装和系列实验.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496046303,0),(55,4,97,8,21,'published','HBase环境搭建视频','',NULL,'video','',0,0,118,'self','Hbase_1.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496061795,0),(57,5,99,1,13,'published','Hive概述','',NULL,'document','',0,0,120,'self','第5章第1节-Hive概述.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062033,0),(58,5,100,2,15,'published','Hive的体系结构','',NULL,'document','',0,0,121,'self','第5章第2节-Hive的体系结构.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062073,0),(59,5,101,3,17,'published','Hive数据类型和文件格式','',NULL,'document','',0,0,122,'self','第5章第3节-Hive数据类型和文件格式.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062139,0),(60,5,102,4,19,'published','Hive常用操作方式','',NULL,'document','',0,0,123,'self','第5章第4节-Hive常用操作方式.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062213,0),(61,5,103,5,21,'published','HiveQL数据定义','',NULL,'document','',0,0,124,'self','第5章第5节-HiveQL数据定义.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062264,0),(62,5,104,6,23,'published','HiveQL查询','',NULL,'document','',0,0,125,'self','第5章第6节-HiveQL查询.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062307,0),(63,5,105,7,25,'published','Hive环境搭建','',NULL,'video','',0,0,126,'self','hive_1.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062388,0),(64,5,106,8,27,'published','Hive Sql基本操作视频案例','',NULL,'video','',0,0,127,'self','hive_2.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062429,0),(65,6,107,1,6,'published','Spark on Yarn','',NULL,'document','',0,0,128,'self','第12章_第1节_Spark on Yarn.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062926,0),(66,6,108,2,8,'published','Spark HistoryServer','',NULL,'document','',0,0,129,'self','第12章_第2节_Spark HistoryServer.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496062982,0),(67,6,109,3,10,'published','Spark Streaming','',NULL,'document','',0,0,130,'self','第12章_第3节_Spark Streaming.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063018,0),(68,6,110,4,12,'published','Spark 项目实战','',NULL,'document','',0,0,131,'self','第12章_第5节_Spark 项目实战.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063088,0),(69,6,79,5,13,'published','Spark高阶应用&实战（讲义）','',NULL,'document','',0,0,132,'self','讲义-第十二章 Spark高阶应用&实战.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063151,0),(70,7,111,1,6,'published','大数据协作框架_Sqoop （上）','',NULL,'document','',0,0,133,'self','第7章_第1节大数据协作框架_Sqoop（上）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063226,0),(71,7,112,2,8,'published','大数据协作框架_Sqoop （下）','',NULL,'document','',0,0,134,'self','第7章_第1节大数据协作框架_Sqoop（下）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063266,0),(72,7,113,3,10,'published','大数据协作框架_Flume','',NULL,'document','',0,0,135,'self','第7章_第2节大数据协作框架_Flume.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063317,0),(73,7,114,4,12,'published','大数据协作框架_Kafka','',NULL,'document','',0,0,136,'self','第7章_第3节大数据协作框架_Kafka.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063362,0),(74,7,115,5,14,'published','大数据协作框架_Phoenix','',NULL,'document','',0,0,137,'self','第7章_第4节大数据协作框架_Phoenix.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063407,0),(75,7,83,6,15,'published','大数据协作框架（讲义）','',NULL,'document','',0,0,138,'self','第七章 大数据协作框架（讲义）.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063440,0),(76,8,116,1,8,'published','实时流式计算概述','',NULL,'document','',0,0,139,'self','第8章_第1节_实时流式计算概述.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063737,0),(77,8,117,2,10,'published','实时流式计算的应用场景及关键技术','',NULL,'document','',0,0,140,'self','第8章_第2节_实时流式计算的应用场景及关键技术.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063796,0),(78,8,118,3,12,'published','Storm概述','',NULL,'document','',0,0,141,'self','第8章_第3节_概述Storm.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063825,0),(79,8,119,4,14,'published','Storm的架构与原理','',NULL,'document','',0,0,142,'self','第8章_第4节_Storm架构和原理.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063864,0),(80,8,120,5,16,'published','Storm集群搭建部署','',NULL,'document','',0,0,143,'self','第8章_第5节_Storm集群搭建部署.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063901,0),(81,8,121,6,18,'published','Storm常见问题与解决方法','',NULL,'document','',0,0,144,'self','第8章_第6节_Storm常见问题与解决方法.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496063948,0),(82,8,122,7,20,'published','流式大数据处理（讲义）','',NULL,'document','',0,0,145,'self','第八章 讲义-流式大数据处理.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064015,0),(83,9,123,1,9,'published','Spark以及Spark生态圈的介绍','',NULL,'document','',0,0,146,'self','第11章_第1节_Spark以及Spark生态圈的介绍.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064061,0),(84,9,124,2,11,'published','Spark集群搭建','',NULL,'document','',0,0,147,'self','第11章_第2节_Spark集群搭建.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064106,0),(85,9,126,3,13,'published','Spark应用提交工具','',NULL,'document','',0,0,148,'self','第11章_第3节_Spark应用提交工具.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064187,0),(86,9,127,4,15,'published','Scala基本知识讲解','',NULL,'document','',0,0,149,'self','第11章_第4节_Scala基本知识讲解.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064243,0),(87,9,128,5,17,'published','Scala基本操作视频案例','',NULL,'video','',0,0,150,'self','spark.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064301,0),(88,9,129,6,19,'published','RDD特性、常见操作、缓存策略','',NULL,'document','',0,0,151,'self','第11章_第5节_RDD特性、常见操作、缓存策略.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064367,0),(89,9,130,7,21,'published','RDD Dependency、Stage','',NULL,'document','',0,0,152,'self','第11章_第6节_RDD Dependency、Stage .pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064417,0),(90,9,131,8,23,'published','Spark核心组件概述及案例分析','',NULL,'document','',0,0,153,'self','第11章_第7节_Spark核心组件概述及案例分析.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064455,0),(91,9,132,9,25,'published','Spark程序开发实验指南','',NULL,'document','',0,0,154,'self','课程3实验11SPARK程序开发-实验指南.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496064511,0),(92,3,34,18,37,'published','Hadoop概述视频讲解案例','',NULL,'video','',0,0,155,'self','1.Hadoop概述.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496138691,0),(93,4,53,10,24,'published','HBase概述视频讲解案例','',NULL,'video','',0,0,156,'self','2.HBase概述.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496138764,0),(94,4,53,11,25,'published','HBase术语及数据模型','',NULL,'video','',0,0,157,'self','2.HBase术语及数据模型.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496138840,0),(95,5,65,9,28,'published','Hive概述视屏案例','',NULL,'video','',0,0,158,'self','3.Hive简介.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496138869,0),(96,5,66,10,29,'published','Hive与HBase的关系','',NULL,'video','',0,0,159,'self','3.Hive与HBase关系.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496138893,0),(97,9,90,10,26,'published','Spark简介视频案例','',NULL,'video','',0,0,160,'self','4.Spark简介.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496138928,0),(98,9,90,11,27,'published','Spark计算模型','',NULL,'video','',0,0,161,'self','4.Spark计算模型.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496138942,0),(99,3,39,16,38,'published','Hadoop部署一','',NULL,'video','',0,0,162,'self','hadoop_1.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496246101,0),(100,3,39,16,39,'published','Hadoop部署二','',NULL,'video','',0,0,163,'self','hadoop_2.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496246152,0),(101,3,39,16,40,'unpublished','Hadoop部署三','',NULL,'video','',0,0,164,'self','hadoop_3.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496246167,0),(102,3,51,17,41,'published','HDFS操作视屏案例','',NULL,'video','',0,0,165,'self','hdfs.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496246287,0),(103,4,98,11,26,'published','HBase基本操作视频案例','',NULL,'video','',0,0,167,'self','Hbase_2.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1496246389,0),(104,10,134,1,4,'published','了解OpenStack主要组件','',NULL,'document','',0,0,168,'self','OpenStack主要组件.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1498057070,0),(105,10,135,1,5,'published','DevStack部署Openstack','主要对Openstack环境部署有一个概念性的了解。',NULL,'document','',0,0,169,'self','Devstack搭建OpenStack.pdf','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1498057113,0),(106,10,135,3,6,'published','DevStack 部署OpenStack','',NULL,'video','',0,0,170,'self','te-3.mp4','','',0,NULL,NULL,0,0,0,0,0,0,'ungenerated',3,0,1498141257,0);
/*!40000 ALTER TABLE `course_lesson` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_lesson_learn`
--

DROP TABLE IF EXISTS `course_lesson_learn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_lesson_learn` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '学员课时学习记录ID',
  `userId` int(10) unsigned NOT NULL COMMENT '学员ID',
  `courseId` int(10) unsigned NOT NULL COMMENT '课程ID',
  `lessonId` int(10) unsigned NOT NULL COMMENT '课时ID',
  `status` enum('learning','finished') NOT NULL COMMENT '学习状态',
  `startTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学习开始时间',
  `finishedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学习完成时间',
  `learnTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学习时间',
  `watchTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学习观看时间',
  `videoStatus` enum('paused','playing') NOT NULL DEFAULT 'paused' COMMENT '学习观看状态',
  `updateTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `userId_courseId` (`userId`,`courseId`) USING BTREE,
  KEY `userId_lessonId_webCode` (`userId`,`lessonId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_lesson_learn`
--

LOCK TABLES `course_lesson_learn` WRITE;
/*!40000 ALTER TABLE `course_lesson_learn` DISABLE KEYS */;
INSERT INTO `course_lesson_learn` VALUES (2,1,4,3,'learning',1461306033,0,0,0,'paused',0),(3,1,4,4,'learning',1461306157,0,0,0,'paused',0),(4,4,5,5,'learning',1465710398,0,104,0,'paused',0),(6,0,5,5,'learning',1465714007,0,5768,0,'paused',0),(8,4,4,3,'learning',1467899658,0,116,0,'paused',0),(9,700,5,5,'learning',1467910669,0,0,0,'paused',0),(12,0,3,1,'learning',1467954822,0,5630,0,'paused',0),(13,0,4,3,'learning',1467954873,0,0,0,'paused',0),(14,0,4,4,'learning',1467954888,0,5630,0,'paused',0),(20,16,7,11,'learning',1470231806,0,0,21,'paused',1470231993),(22,16,4,3,'learning',1470232978,0,0,0,'paused',0),(23,16,4,4,'learning',1470232981,0,0,0,'paused',0),(24,12,7,11,'learning',1470400782,0,1118,22,'paused',1470987481),(25,12,7,12,'finished',1470462607,1470812605,728,275,'playing',1470818478),(26,12,7,13,'finished',1470468268,1470468403,370,0,'paused',0),(27,0,7,12,'learning',1470492249,0,82,3,'paused',1470809586),(28,0,7,11,'learning',1470492296,0,26,13,'paused',1471261768),(29,0,7,13,'learning',1470553062,0,58,0,'paused',0),(31,12,5,14,'learning',1470899499,0,0,0,'paused',0),(33,16,7,12,'learning',1470921192,0,0,0,'playing',1470921192),(35,1,7,11,'finished',1470989921,1470990687,26,5,'paused',1470990687),(36,12,7,17,'finished',1471003323,1471003530,0,0,'paused',0),(37,12,5,18,'learning',1471006101,0,0,0,'paused',0),(39,12,7,21,'learning',1471271970,0,44,0,'paused',0),(40,12,21,22,'finished',1471406198,1471784288,304,0,'paused',0),(41,12,21,23,'finished',1471406828,1471596599,0,0,'paused',0),(44,1,21,22,'learning',1471428555,0,166,2,'playing',1471508730),(45,1,21,26,'learning',1471429783,0,6,0,'paused',0),(47,12,21,28,'finished',1471507181,1471522296,1802,5,'paused',1471784012),(48,1,21,23,'learning',1471508500,0,0,0,'playing',1471508728),(49,0,21,22,'learning',1471588662,0,214,3,'playing',1480575149),(50,12,21,26,'learning',1471604497,0,0,0,'paused',0),(51,0,21,26,'learning',1471605284,0,68,0,'paused',0),(52,0,21,28,'learning',1471783683,0,1232,2,'paused',1471785289),(53,12,7,29,'learning',1471786537,0,1800,0,'paused',0),(54,0,7,29,'learning',1471787187,0,1148,0,'paused',0),(57,1,5,18,'learning',1471932959,0,0,0,'playing',1471932959),(58,20,21,22,'learning',1471952607,0,20,3,'paused',1471953984),(59,5,20,41,'learning',1473677237,0,0,0,'paused',0),(61,230,22,42,'learning',1473764230,0,0,0,'paused',0),(62,5,5,14,'learning',1473847957,0,0,0,'paused',0),(63,5,5,5,'learning',1473848035,0,0,0,'paused',0),(66,1,1,47,'learning',1474534449,0,0,0,'paused',0),(67,1,21,28,'learning',1474546926,0,0,0,'paused',0),(69,0,4,46,'learning',1474986772,0,0,0,'paused',0),(70,230,23,48,'learning',1476194578,0,0,0,'paused',0),(72,0,21,23,'learning',1477022122,0,0,0,'playing',1480568633),(73,230,24,49,'learning',1477102213,0,0,0,'paused',0),(75,288,7,12,'learning',1490534408,0,4,1,'playing',1490535272),(76,288,7,29,'learning',1490534417,0,0,0,'paused',0),(77,288,7,13,'learning',1490534498,0,420,0,'paused',0),(78,3,1,1,'learning',1491690186,0,0,0,'paused',0),(80,3,3,3,'learning',1494338829,0,0,0,'paused',0),(81,3,3,4,'learning',1494338923,0,0,0,'paused',0),(82,3,2,5,'learning',1494726991,0,0,0,'paused',0),(83,3,2,20,'learning',1494745575,0,0,0,'paused',0),(84,3,2,23,'learning',1494745617,0,0,0,'paused',0),(85,3,2,22,'learning',1494746067,0,0,0,'paused',0),(88,3,2,26,'learning',1494978212,0,0,0,'paused',0),(89,3,2,21,'learning',1495494718,0,0,0,'paused',0),(90,3,2,27,'learning',1495549167,0,0,0,'paused',0),(91,3,2,28,'learning',1495549595,0,0,0,'paused',0),(92,3,3,29,'learning',1496044545,0,0,0,'paused',0),(93,3,9,91,'learning',1496140217,0,0,0,'paused',0),(94,3,9,83,'learning',1496140227,0,0,0,'paused',0),(95,3,9,97,'learning',1496140236,0,0,0,'paused',0),(96,3,9,98,'learning',1496140244,0,0,0,'paused',0),(97,3,9,84,'learning',1496140253,0,0,0,'paused',0),(98,3,9,87,'learning',1496140266,0,0,0,'paused',0),(99,3,9,88,'learning',1496140299,0,0,0,'paused',0),(100,3,9,89,'learning',1496140303,0,0,0,'paused',0),(101,3,9,90,'learning',1496140307,0,0,0,'paused',0),(102,3,7,70,'learning',1496230043,0,0,0,'paused',0),(103,1,9,91,'learning',1496245022,0,0,0,'paused',0),(104,1,9,84,'learning',1496245038,0,4,0,'paused',0),(105,1,9,98,'learning',1496245599,0,0,0,'playing',1496245599),(106,1,9,97,'learning',1496245603,0,0,0,'playing',1496245603),(107,1,9,85,'learning',1496245652,0,0,0,'paused',0),(108,1,9,86,'learning',1496245656,0,0,0,'paused',0),(109,1,9,87,'learning',1496245657,0,0,0,'playing',1496245738),(110,1,9,88,'learning',1496245661,0,0,0,'paused',0),(111,1,9,89,'learning',1496245666,0,0,0,'paused',0),(112,1,9,90,'learning',1496245677,0,0,0,'paused',0),(113,1,7,75,'learning',1496245847,0,0,0,'paused',0),(117,3,3,99,'learning',1496246105,0,0,0,'paused',0),(118,3,3,100,'learning',1496246178,0,0,0,'paused',0),(119,3,3,101,'learning',1496246212,0,0,0,'paused',0),(121,3,3,102,'learning',1496246292,0,0,0,'paused',0),(122,3,4,93,'learning',1496246309,0,0,0,'paused',0),(123,3,4,94,'learning',1496246312,0,0,0,'paused',0),(125,3,4,55,'learning',1496246349,0,0,0,'paused',0),(126,3,4,103,'learning',1496246392,0,0,0,'paused',0),(127,3,5,95,'learning',1496246435,0,0,0,'paused',0),(128,3,5,63,'learning',1496246445,0,0,0,'paused',0),(129,3,5,64,'learning',1496246470,0,0,0,'paused',0),(130,0,9,97,'learning',1496247428,0,2,2,'paused',1496247637),(131,0,9,91,'learning',1496247643,0,0,0,'paused',0),(132,3,7,71,'learning',1496248512,0,0,0,'paused',0),(133,3,7,75,'learning',1496248520,0,0,0,'paused',0),(134,3,7,73,'learning',1496248548,0,0,0,'paused',0),(135,0,9,98,'learning',1496276151,0,0,0,'playing',1496276151),(136,0,3,45,'learning',1496282265,0,14,0,'paused',0),(137,0,3,92,'learning',1496283134,0,0,0,'playing',1503673958),(138,0,3,30,'learning',1496283136,0,0,0,'paused',0),(139,0,3,99,'learning',1496283141,0,2,3,'paused',1496283268),(140,0,3,100,'learning',1496283271,0,8,0,'playing',1496283271),(141,0,3,35,'learning',1496283802,0,0,0,'paused',0),(142,0,3,42,'learning',1496283813,0,12,0,'paused',0),(143,0,4,53,'learning',1496284501,0,0,0,'paused',0),(144,0,4,103,'learning',1496284511,0,0,0,'playing',1496284511),(145,0,9,85,'learning',1496284685,0,24,0,'paused',0),(146,3,10,104,'learning',1498057120,0,0,0,'paused',0),(147,3,10,105,'learning',1498057142,0,0,0,'paused',0),(148,0,10,105,'learning',1498141151,0,0,0,'paused',0),(149,3,10,106,'learning',1498141261,0,0,0,'paused',0),(150,0,10,106,'learning',1498180606,0,34,22,'playing',1498182655),(151,1,3,29,'learning',1502174554,0,0,0,'paused',0),(152,1,3,45,'learning',1502174664,0,0,0,'paused',0),(153,1,3,92,'learning',1502174669,0,0,0,'playing',1502174682),(154,3,3,92,'learning',1502888983,0,0,0,'paused',0),(155,0,5,64,'learning',1502889400,0,2,0,'paused',0),(156,1,10,104,'learning',1503583345,0,0,0,'paused',0),(157,1,10,106,'learning',1503583348,0,0,0,'paused',0),(158,0,9,87,'learning',1503584210,0,0,0,'playing',1503584210),(159,3,5,57,'learning',1503585728,0,0,0,'paused',0);
/*!40000 ALTER TABLE `course_lesson_learn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_lesson_replay`
--

DROP TABLE IF EXISTS `course_lesson_replay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_lesson_replay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lessonId` int(10) unsigned NOT NULL COMMENT '所属课时',
  `courseId` int(10) unsigned NOT NULL COMMENT '所属课程',
  `title` varchar(255) NOT NULL COMMENT '名称',
  `replayId` text NOT NULL COMMENT '云直播中的回放id',
  `userId` int(10) unsigned NOT NULL COMMENT '创建者',
  `createdTime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_lesson_replay`
--

LOCK TABLES `course_lesson_replay` WRITE;
/*!40000 ALTER TABLE `course_lesson_replay` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_lesson_replay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_lesson_view`
--

DROP TABLE IF EXISTS `course_lesson_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_lesson_view` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `courseId` int(10) NOT NULL,
  `lessonId` int(10) NOT NULL,
  `fileId` int(10) NOT NULL,
  `userId` int(10) NOT NULL,
  `fileType` enum('document','video','audio','image','ppt','other','none') NOT NULL DEFAULT 'none',
  `fileStorage` enum('local','cloud','net','none') NOT NULL DEFAULT 'none',
  `fileSource` varchar(32) NOT NULL,
  `createdTime` int(10) unsigned NOT NULL,
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=414 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_lesson_view`
--

LOCK TABLES `course_lesson_view` WRITE;
/*!40000 ALTER TABLE `course_lesson_view` DISABLE KEYS */;
INSERT INTO `course_lesson_view` VALUES (1,4,2,0,1,'video','net','polyv',1461304196,'tlc','-'),(2,4,2,0,1,'video','net','polyv',1461304206,'tlc','-'),(3,4,2,0,1,'video','net','polyv',1461304210,'tlc','-'),(4,4,2,0,1,'video','net','polyv',1461304213,'tlc','-'),(5,4,2,0,1,'video','net','polyv',1461305137,'tlc','-'),(6,4,2,0,1,'video','net','polyv',1461305218,'tlc','-'),(7,4,2,0,1,'video','net','polyv',1461305228,'tlc','-'),(8,4,2,0,1,'video','net','polyv',1461305454,'tlc','-'),(9,4,2,0,1,'video','net','polyv',1461305461,'tlc','-'),(10,4,2,0,1,'video','net','polyv',1461305470,'tlc','-'),(11,4,2,0,1,'video','net','polyv',1461305519,'tlc','-'),(12,4,2,0,1,'video','net','polyv',1461305592,'tlc','-'),(13,4,2,0,1,'video','net','polyv',1461305596,'tlc','-'),(14,4,2,0,1,'video','net','polyv',1461305612,'tlc','-'),(15,4,2,0,1,'video','net','polyv',1461305615,'tlc','-'),(16,4,2,0,1,'video','net','polyv',1461305619,'tlc','-'),(17,4,2,0,1,'video','net','polyv',1461305632,'tlc','-'),(18,4,2,0,1,'video','net','polyv',1461305636,'tlc','-'),(19,4,2,0,1,'video','net','polyv',1461305639,'tlc','-'),(20,4,2,0,1,'video','net','polyv',1461305653,'tlc','-'),(21,4,2,0,1,'video','net','polyv',1461305656,'tlc','-'),(22,4,2,0,1,'video','net','polyv',1461305662,'tlc','-'),(23,4,2,0,1,'video','net','polyv',1461305665,'tlc','-'),(24,4,2,0,1,'video','net','polyv',1461305668,'tlc','-'),(25,4,2,0,1,'video','net','polyv',1461305671,'tlc','-'),(26,4,2,0,1,'video','net','polyv',1461305673,'tlc','-'),(27,4,2,0,1,'video','net','polyv',1461305689,'tlc','-'),(28,4,2,0,1,'video','net','polyv',1461305693,'tlc','-'),(29,4,2,0,1,'video','net','polyv',1461305730,'tlc','-'),(30,4,2,0,1,'video','net','polyv',1461305742,'tlc','-'),(31,4,2,0,1,'video','net','polyv',1461306134,'tlc','-'),(32,4,2,0,0,'video','net','polyv',1467951519,'tlc','-'),(33,4,2,0,0,'video','net','polyv',1467954869,'tlc','-'),(34,8,10,9,0,'video','local','self',1470037541,'','-'),(35,8,10,9,0,'video','local','self',1470037628,'','-'),(36,8,10,9,16,'video','local','self',1470057568,'','-'),(37,8,10,9,16,'video','local','self',1470058005,'','-'),(38,8,10,9,16,'video','local','self',1470058141,'','-'),(39,8,10,9,16,'video','local','self',1470058234,'','-'),(40,8,10,9,16,'video','local','self',1470058377,'','-'),(41,8,10,9,16,'video','local','self',1470227355,'','-'),(42,8,10,9,16,'video','local','self',1470227412,'','-'),(43,8,10,9,16,'video','local','self',1470227441,'','-'),(44,8,10,9,16,'video','local','self',1470227651,'','-'),(45,8,10,9,0,'video','local','self',1470227913,'','-'),(46,8,10,9,16,'video','local','self',1470228401,'','-'),(47,8,10,9,16,'video','local','self',1470228486,'','-'),(48,8,10,9,16,'video','local','self',1470228808,'','-'),(49,8,10,9,16,'video','local','self',1470229399,'','-'),(50,8,10,9,16,'video','local','self',1470230160,'','-'),(51,8,10,9,16,'video','local','self',1470230517,'','-'),(52,8,10,9,16,'video','local','self',1470230930,'','-'),(53,8,10,9,16,'video','local','self',1470231091,'','-'),(54,8,10,9,16,'video','local','self',1470231156,'','-'),(55,8,10,9,16,'video','local','self',1470231192,'','-'),(56,7,11,10,16,'video','local','self',1470231806,'','-'),(57,7,11,10,16,'video','local','self',1470231898,'','-'),(58,4,2,0,16,'video','net','polyv',1470232962,'','-'),(59,7,11,10,12,'video','local','self',1470400782,'','-'),(60,7,12,14,12,'video','cloud','self',1470462607,'','-'),(61,7,12,14,12,'video','cloud','self',1470462776,'','-'),(62,7,12,14,12,'video','cloud','self',1470463270,'','-'),(63,7,12,14,12,'video','cloud','self',1470465345,'','-'),(64,7,12,14,12,'video','cloud','self',1470465472,'','-'),(65,7,12,14,12,'video','cloud','self',1470466254,'','-'),(66,7,11,10,12,'video','local','self',1470466688,'','-'),(67,7,12,14,12,'video','cloud','self',1470479419,'','-'),(68,7,12,14,12,'video','cloud','self',1470479624,'','-'),(69,7,12,14,12,'video','cloud','self',1470479756,'','-'),(70,7,12,14,12,'video','cloud','self',1470482404,'','-'),(71,7,11,10,12,'video','local','self',1470484666,'','-'),(72,7,11,10,12,'video','local','self',1470489915,'','-'),(73,7,11,10,12,'video','local','self',1470489983,'','-'),(74,7,11,10,12,'video','local','self',1470490015,'','-'),(75,7,11,10,12,'video','local','self',1470490097,'','-'),(76,7,11,10,12,'video','local','self',1470490267,'','-'),(77,7,12,14,12,'video','cloud','self',1470490358,'','-'),(78,7,12,14,12,'video','cloud','self',1470490397,'','-'),(79,7,12,14,12,'video','cloud','self',1470490464,'','-'),(80,7,12,14,12,'video','cloud','self',1470490548,'','-'),(81,7,12,14,12,'video','cloud','self',1470490569,'','-'),(82,7,12,14,12,'video','cloud','self',1470491222,'','-'),(83,7,12,14,12,'video','cloud','self',1470491615,'','-'),(84,7,12,14,12,'video','cloud','self',1470491766,'','-'),(85,7,12,14,12,'video','cloud','self',1470491875,'','-'),(86,7,12,14,12,'video','cloud','self',1470492070,'','-'),(87,7,12,14,12,'video','cloud','self',1470492138,'','-'),(88,7,12,14,12,'video','cloud','self',1470492152,'','-'),(89,7,12,14,12,'video','cloud','self',1470492210,'','-'),(90,7,12,14,12,'video','cloud','self',1470492230,'','-'),(91,7,12,14,0,'video','cloud','self',1470492249,'','-'),(92,7,11,10,0,'video','local','self',1470492296,'','-'),(93,7,12,14,12,'video','cloud','self',1470750365,'','-'),(94,7,12,14,12,'video','cloud','self',1470799994,'','-'),(95,7,12,14,12,'video','cloud','self',1470803932,'','-'),(96,7,12,14,12,'video','cloud','self',1470804890,'','-'),(97,7,12,14,0,'video','cloud','self',1470805130,'','-'),(98,7,12,14,12,'video','cloud','self',1470805511,'','-'),(99,7,12,14,12,'video','cloud','self',1470805775,'','-'),(100,7,12,14,12,'video','cloud','self',1470805924,'','-'),(101,7,12,14,12,'video','cloud','self',1470806019,'','-'),(102,7,12,14,12,'video','cloud','self',1470806091,'','-'),(103,7,12,14,12,'video','cloud','self',1470806160,'','-'),(104,7,12,14,12,'video','cloud','self',1470806485,'','-'),(105,7,12,14,12,'video','cloud','self',1470806878,'','-'),(106,7,12,14,12,'video','cloud','self',1470806932,'','-'),(107,7,12,14,12,'video','cloud','self',1470807043,'','-'),(108,7,12,14,12,'video','cloud','self',1470807073,'','-'),(109,7,12,14,12,'video','cloud','self',1470807095,'','-'),(110,7,12,14,12,'video','cloud','self',1470807114,'','-'),(111,7,12,14,12,'video','cloud','self',1470807128,'','-'),(112,7,12,14,12,'video','cloud','self',1470807176,'','-'),(113,7,12,14,12,'video','cloud','self',1470807313,'','-'),(114,7,12,14,12,'video','cloud','self',1470807337,'','-'),(115,7,12,14,12,'video','cloud','self',1470807464,'','-'),(116,7,12,14,12,'video','cloud','self',1470807491,'','-'),(117,7,12,14,12,'video','cloud','self',1470807818,'','-'),(118,7,12,14,12,'video','cloud','self',1470807847,'','-'),(119,7,12,14,12,'video','cloud','self',1470808205,'','-'),(120,7,12,14,12,'video','cloud','self',1470808222,'','-'),(121,7,12,14,12,'video','cloud','self',1470808526,'','-'),(122,7,12,14,0,'video','cloud','self',1470809562,'','-'),(123,7,12,14,12,'video','cloud','self',1470812397,'','-'),(124,7,12,14,12,'video','cloud','self',1470815922,'','-'),(125,7,12,14,12,'video','cloud','self',1470815966,'','-'),(126,7,11,10,12,'video','local','self',1470815973,'','-'),(127,7,11,10,12,'video','local','self',1470816114,'','-'),(128,7,12,14,12,'video','cloud','self',1470816369,'','-'),(129,7,11,10,12,'video','local','self',1470816374,'','-'),(130,7,12,14,12,'video','cloud','self',1470816379,'','-'),(131,7,12,14,12,'video','cloud','self',1470816383,'','-'),(132,7,12,14,12,'video','cloud','self',1470816907,'','-'),(133,7,11,10,12,'video','local','self',1470816929,'','-'),(134,7,12,14,12,'video','cloud','self',1470816934,'','-'),(135,7,11,10,0,'video','local','self',1470889448,'','-'),(136,7,12,14,16,'video','cloud','self',1470921192,'','-'),(137,8,10,9,12,'video','local','self',1470986223,'','-'),(138,8,10,9,12,'video','local','self',1470987003,'','-'),(139,7,11,10,12,'video','local','self',1470987017,'','-'),(140,7,11,10,12,'video','local','self',1470987035,'','-'),(141,7,11,10,12,'video','local','self',1470987216,'','-'),(142,7,11,10,12,'video','local','self',1470987249,'','-'),(143,7,11,10,12,'video','local','self',1470987348,'','-'),(144,7,11,10,12,'video','local','self',1470987480,'','-'),(145,7,11,10,12,'video','local','self',1470988857,'','-'),(146,7,11,10,1,'video','local','self',1470989921,'','-'),(147,7,11,10,1,'video','local','self',1470990477,'','-'),(148,7,17,17,12,'video','local','self',1471003323,'','-'),(149,5,18,19,12,'video','local','self',1471006101,'','-'),(150,7,11,10,0,'video','local','self',1471007121,'','-'),(151,7,11,10,0,'video','local','self',1471007148,'','-'),(152,7,11,10,0,'video','local','self',1471007161,'','-'),(153,7,11,10,0,'video','local','self',1471261762,'','-'),(154,7,11,10,0,'video','local','self',1471261766,'','-'),(155,7,21,23,12,'video','cloud','cloud',1471271970,'','-'),(156,7,21,23,12,'video','cloud','cloud',1471315852,'','-'),(157,7,21,23,12,'video','cloud','cloud',1471315860,'','-'),(158,7,21,23,12,'video','cloud','cloud',1471315928,'','-'),(159,7,21,23,12,'video','cloud','cloud',1471318462,'','-'),(160,7,11,10,12,'video','local','self',1471318467,'','-'),(161,7,11,10,12,'video','local','self',1471318490,'','-'),(162,21,22,24,12,'video','local','self',1471406198,'','-'),(163,21,23,25,12,'video','cloud','cloud',1471406827,'','-'),(164,21,22,24,1,'video','local','self',1471428555,'','-'),(165,21,27,29,12,'video','local','self',1471497805,'','-'),(166,21,28,30,12,'video','local','self',1471507181,'','-'),(167,21,28,30,12,'video','local','self',1471507195,'','-'),(168,21,28,30,12,'video','local','self',1471507575,'','-'),(169,21,28,30,12,'video','local','self',1471507579,'','-'),(170,21,28,30,12,'video','local','self',1471507770,'','-'),(171,21,28,30,12,'video','local','self',1471507836,'','-'),(172,21,28,30,12,'video','local','self',1471507972,'','-'),(173,21,28,30,12,'video','local','self',1471508417,'','-'),(174,21,22,24,1,'video','local','self',1471508436,'','-'),(175,21,23,25,1,'video','cloud','cloud',1471508500,'','-'),(176,21,22,24,1,'video','local','self',1471508506,'','-'),(177,21,22,24,1,'video','local','self',1471508685,'','-'),(178,21,23,25,1,'video','cloud','cloud',1471508691,'','-'),(179,21,22,24,1,'video','local','self',1471508695,'','-'),(180,21,22,24,1,'video','local','self',1471508724,'','-'),(181,21,23,25,1,'video','cloud','cloud',1471508727,'','-'),(182,21,22,24,1,'video','local','self',1471508730,'','-'),(183,21,28,30,12,'video','local','self',1471508784,'','-'),(184,21,28,30,12,'video','local','self',1471508795,'','-'),(185,21,28,30,12,'video','local','self',1471508850,'','-'),(186,21,28,30,12,'video','local','self',1471508892,'','-'),(187,21,28,30,12,'video','local','self',1471508981,'','-'),(188,21,22,24,12,'video','local','self',1471509068,'','-'),(189,21,23,25,12,'video','cloud','cloud',1471509095,'','-'),(190,21,23,25,12,'video','cloud','cloud',1471509346,'','-'),(191,21,23,25,12,'video','cloud','cloud',1471509359,'','-'),(192,21,28,30,12,'video','local','self',1471509364,'','-'),(193,21,23,25,12,'video','cloud','cloud',1471509383,'','-'),(194,21,22,24,12,'video','local','self',1471509399,'','-'),(195,21,28,30,12,'video','local','self',1471510018,'','-'),(196,21,28,30,12,'video','local','self',1471510022,'','-'),(197,21,28,30,12,'video','local','self',1471510325,'','-'),(198,21,28,30,12,'video','local','self',1471510481,'','-'),(199,21,22,24,12,'video','local','self',1471511051,'','-'),(200,21,28,30,12,'video','local','self',1471521711,'','-'),(201,21,22,24,0,'video','local','self',1471588662,'','-'),(202,21,22,24,12,'video','local','self',1471594945,'','-'),(203,21,23,25,12,'video','cloud','cloud',1471594950,'','-'),(204,21,23,25,12,'video','cloud','cloud',1471596109,'','-'),(205,21,23,25,12,'video','cloud','cloud',1471596117,'','-'),(206,7,11,10,12,'video','local','self',1471596139,'','-'),(207,7,12,14,12,'video','cloud','self',1471596179,'','-'),(208,7,12,14,12,'video','cloud','self',1471596226,'','-'),(209,7,12,14,12,'video','cloud','self',1471596255,'','-'),(210,7,12,14,12,'video','cloud','self',1471596305,'','-'),(211,7,12,14,12,'video','cloud','self',1471596355,'','-'),(212,21,22,24,12,'video','local','self',1471596381,'','-'),(213,21,23,25,12,'video','cloud','cloud',1471596392,'','-'),(214,21,22,24,12,'video','local','self',1471596897,'','-'),(215,21,22,24,12,'video','local','self',1471597079,'','-'),(216,21,22,24,12,'video','local','self',1471597187,'','-'),(217,21,23,25,12,'video','cloud','cloud',1471597192,'','-'),(218,21,28,30,12,'video','local','self',1471597221,'','-'),(219,21,28,30,12,'video','local','self',1471597238,'','-'),(220,21,28,30,12,'video','local','self',1471597247,'','-'),(221,21,28,30,12,'video','local','self',1471597793,'','-'),(222,21,28,30,12,'video','local','self',1471597842,'','-'),(223,21,28,30,12,'video','local','self',1471598163,'','-'),(224,21,28,30,12,'video','local','self',1471603648,'','-'),(225,21,28,30,12,'video','local','self',1471603714,'','-'),(226,21,28,30,12,'video','local','self',1471603759,'','-'),(227,21,28,30,12,'video','local','self',1471603854,'','-'),(228,21,22,24,12,'video','local','self',1471603971,'','-'),(229,21,22,24,12,'video','local','self',1471604007,'','-'),(230,21,28,30,12,'video','local','self',1471604010,'','-'),(231,21,28,30,12,'video','local','self',1471669633,'','-'),(232,21,28,30,12,'video','local','self',1471669725,'','-'),(233,21,22,24,12,'video','local','self',1471781784,'','-'),(234,21,22,24,12,'video','local','self',1471781808,'','-'),(235,21,22,24,12,'video','local','self',1471781843,'','-'),(236,21,22,24,12,'video','local','self',1471781927,'','-'),(237,21,22,24,12,'video','local','self',1471781950,'','-'),(238,21,22,24,12,'video','local','self',1471782458,'','-'),(239,21,23,25,12,'video','cloud','cloud',1471782485,'','-'),(240,21,22,24,12,'video','local','self',1471782490,'','-'),(241,21,28,30,12,'video','local','self',1471782502,'','-'),(242,21,28,30,12,'video','local','self',1471782510,'','-'),(243,21,28,30,12,'video','local','self',1471782536,'','-'),(244,21,28,30,12,'video','local','self',1471782592,'','-'),(245,21,28,30,12,'video','local','self',1471782598,'','-'),(246,21,22,24,12,'video','local','self',1471782612,'','-'),(247,21,23,25,12,'video','cloud','cloud',1471782619,'','-'),(248,21,28,30,12,'video','local','self',1471782623,'','-'),(249,21,22,24,12,'video','local','self',1471782637,'','-'),(250,21,23,25,12,'video','cloud','cloud',1471782645,'','-'),(251,21,28,30,12,'video','local','self',1471782960,'','-'),(252,21,28,30,12,'video','local','self',1471783462,'','-'),(253,21,28,30,12,'video','local','self',1471783524,'','-'),(254,21,28,30,0,'video','local','self',1471783683,'','-'),(255,21,28,30,12,'video','local','self',1471783886,'','-'),(256,21,28,30,12,'video','local','self',1471783933,'','-'),(257,21,28,30,12,'video','local','self',1471784045,'','-'),(258,21,22,24,12,'video','local','self',1471784061,'','-'),(259,21,22,24,12,'video','local','self',1471784500,'','-'),(260,21,22,24,12,'video','local','self',1471784634,'','-'),(261,21,22,24,12,'video','local','self',1471784833,'','-'),(262,21,22,24,12,'video','local','self',1471785026,'','-'),(263,21,22,24,12,'video','local','self',1471785107,'','-'),(264,21,22,24,12,'video','local','self',1471785233,'','-'),(265,21,28,30,0,'video','local','self',1471785288,'','-'),(266,21,22,24,12,'video','local','self',1471785578,'','-'),(267,21,23,25,12,'video','cloud','cloud',1471785582,'','-'),(268,21,23,25,12,'video','cloud','cloud',1471785596,'','-'),(269,21,23,25,12,'video','cloud','cloud',1471785658,'','-'),(270,21,22,24,12,'video','local','self',1471785823,'','-'),(271,21,22,24,12,'video','local','self',1471785892,'','-'),(272,21,28,30,12,'video','local','self',1471785965,'','-'),(273,21,23,25,12,'video','cloud','cloud',1471785970,'','-'),(274,21,23,25,12,'video','cloud','cloud',1471785991,'','-'),(275,21,22,24,12,'video','local','self',1471785996,'','-'),(276,21,28,30,12,'video','local','self',1471786003,'','-'),(277,21,22,24,12,'video','local','self',1471786006,'','-'),(278,21,23,25,12,'video','cloud','cloud',1471786029,'','-'),(279,21,28,30,12,'video','local','self',1471786034,'','-'),(280,21,23,25,12,'video','cloud','cloud',1471786154,'','-'),(281,21,28,30,12,'video','local','self',1471786163,'','-'),(282,21,23,25,12,'video','cloud','cloud',1471786168,'','-'),(283,7,12,14,12,'video','cloud','self',1471786183,'','-'),(284,7,21,23,12,'video','cloud','cloud',1471786189,'','-'),(285,7,17,17,12,'video','local','self',1471786378,'','-'),(286,7,29,31,12,'video','local','self',1471786537,'','-'),(287,7,21,23,12,'video','cloud','cloud',1471786564,'','-'),(288,7,29,31,12,'video','local','self',1471786581,'','-'),(289,7,21,23,12,'video','cloud','cloud',1471786586,'','-'),(290,7,29,31,12,'video','local','self',1471786589,'','-'),(291,7,21,23,12,'video','cloud','cloud',1471786625,'','-'),(292,7,29,31,0,'video','local','self',1471787187,'','-'),(293,21,28,30,12,'video','local','self',1471852355,'','-'),(294,21,34,36,12,'video','local','self',1471852913,'','-'),(295,21,34,36,12,'video','local','self',1471852929,'','-'),(296,21,34,36,12,'video','local','self',1471852968,'','-'),(297,21,34,36,12,'video','local','self',1471853045,'','-'),(298,21,34,36,12,'video','local','self',1471853053,'','-'),(299,21,28,30,12,'video','local','self',1471853098,'','-'),(300,21,34,36,12,'video','local','self',1471853930,'','-'),(301,21,34,36,12,'video','local','self',1471854344,'','-'),(302,21,28,30,12,'video','local','self',1471854371,'','-'),(303,21,34,36,12,'video','local','self',1471854662,'','-'),(304,21,28,30,12,'video','local','self',1471931086,'','-'),(305,5,18,19,1,'video','local','self',1471932959,'','-'),(306,21,22,24,20,'video','local','self',1471952607,'','-'),(307,21,22,24,20,'video','local','self',1471953884,'','-'),(308,21,22,24,20,'video','local','self',1471953981,'','-'),(309,7,29,31,12,'video','local','self',1472046674,'','-'),(310,20,41,45,5,'video','local','self',1473677237,'','-'),(311,20,41,45,5,'video','local','self',1473677505,'','-'),(312,20,41,45,5,'video','local','self',1473677534,'','-'),(313,20,41,45,5,'video','local','self',1473677908,'','-'),(314,22,42,46,230,'video','local','self',1473764230,'','-'),(315,22,42,46,230,'video','local','self',1473764249,'','-'),(316,4,43,47,1,'video','local','self',1474346794,'','-'),(317,21,28,30,1,'video','local','self',1474546926,'','-'),(318,23,48,56,230,'video','local','self',1476194578,'','-'),(319,21,22,24,0,'video','local','self',1477022084,'','-'),(320,21,23,25,0,'video','cloud','cloud',1477022122,'','-'),(321,21,22,24,0,'video','local','self',1477022130,'','-'),(322,21,23,25,0,'video','cloud','cloud',1477022244,'','-'),(323,21,22,24,0,'video','local','self',1477022258,'','-'),(324,21,23,25,0,'video','cloud','cloud',1477022261,'','-'),(325,21,22,24,0,'video','local','self',1480499640,'','-'),(326,21,22,24,0,'video','local','self',1480568622,'','-'),(327,21,23,25,0,'video','cloud','cloud',1480568633,'','-'),(328,21,22,24,0,'video','local','self',1480568637,'','-'),(329,21,22,24,0,'video','local','self',1480575149,'','-'),(330,21,22,24,1,'video','local','self',1484033321,'','-'),(331,7,12,14,288,'video','cloud','self',1490534408,'','-'),(332,7,29,31,288,'video','local','self',1490534417,'','-'),(333,7,29,31,288,'video','local','self',1490534495,'','-'),(334,7,12,14,288,'video','cloud','self',1490535272,'','-'),(335,3,2,64,3,'video','local','self',1493816418,'','-'),(336,3,2,64,3,'video','local','self',1493816483,'','-'),(337,3,2,64,3,'video','local','self',1493816867,'','-'),(338,3,2,64,3,'video','local','self',1493817178,'','-'),(339,3,2,64,3,'video','local','self',1493817197,'','-'),(340,3,2,64,3,'video','local','self',1493817215,'','-'),(341,3,2,64,3,'video','local','self',1493817293,'','-'),(342,3,2,64,3,'video','local','self',1493817335,'','-'),(343,3,2,64,3,'video','local','self',1493817514,'','-'),(344,3,2,64,3,'video','local','self',1493818417,'','-'),(345,3,2,64,3,'video','local','self',1493818466,'','-'),(346,3,2,64,3,'video','local','self',1494336033,'','-'),(347,3,2,64,3,'video','local','self',1494337942,'','-'),(348,3,3,65,3,'video','local','self',1494338829,'','-'),(349,3,3,65,3,'video','local','self',1494338878,'','-'),(350,2,5,67,3,'video','local','self',1494726991,'','-'),(351,2,8,70,3,'video','local','self',1494727752,'','-'),(352,2,9,71,3,'video','local','self',1494727814,'','-'),(353,2,5,67,3,'video','local','self',1494727931,'','-'),(354,2,10,72,3,'video','local','self',1494727939,'','-'),(355,2,5,67,3,'video','local','self',1494745570,'','-'),(356,9,97,160,3,'video','local','self',1496140236,'','-'),(357,9,98,161,3,'video','local','self',1496140244,'','-'),(358,9,87,150,3,'video','local','self',1496140266,'','-'),(359,9,98,161,1,'video','local','self',1496245599,'','-'),(360,9,97,160,1,'video','local','self',1496245603,'','-'),(361,9,87,150,1,'video','local','self',1496245657,'','-'),(362,9,87,150,1,'video','local','self',1496245738,'','-'),(363,3,32,95,1,'video','local','self',1496245895,'','-'),(364,3,33,96,1,'video','local','self',1496245919,'','-'),(365,3,34,97,1,'video','local','self',1496245932,'','-'),(366,3,99,162,3,'video','local','self',1496246105,'','-'),(367,3,99,162,3,'video','local','self',1496246170,'','-'),(368,3,100,163,3,'video','local','self',1496246178,'','-'),(369,3,101,164,3,'video','local','self',1496246212,'','-'),(370,3,43,106,3,'video','local','self',1496246252,'','-'),(371,3,102,165,3,'video','local','self',1496246292,'','-'),(372,4,93,156,3,'video','local','self',1496246309,'','-'),(373,4,94,157,3,'video','local','self',1496246312,'','-'),(374,4,56,119,3,'video','local','self',1496246316,'','-'),(375,4,55,118,3,'video','local','self',1496246349,'','-'),(376,4,103,167,3,'video','local','self',1496246392,'','-'),(377,5,95,158,3,'video','local','self',1496246435,'','-'),(378,5,63,126,3,'video','local','self',1496246445,'','-'),(379,5,64,127,3,'video','local','self',1496246470,'','-'),(380,9,87,150,3,'video','local','self',1496246558,'','-'),(381,9,97,160,0,'video','local','self',1496247428,'','-'),(382,9,97,160,0,'video','local','self',1496247630,'','-'),(383,9,97,160,3,'video','local','self',1496248263,'','-'),(384,9,98,161,3,'video','local','self',1496248303,'','-'),(385,5,95,158,3,'video','local','self',1496248622,'','-'),(386,5,95,158,3,'video','local','self',1496248651,'','-'),(387,5,95,158,3,'video','local','self',1496248656,'','-'),(388,9,98,161,0,'video','local','self',1496276151,'','-'),(389,3,92,155,0,'video','local','self',1496283134,'','-'),(390,3,99,162,0,'video','local','self',1496283141,'','-'),(391,3,100,163,0,'video','local','self',1496283271,'','-'),(392,4,103,167,0,'video','local','self',1496284511,'','-'),(393,10,106,170,3,'video','local','self',1498141261,'','-'),(394,10,106,170,3,'video','local','self',1498141324,'','-'),(395,10,106,170,0,'video','local','self',1498180606,'','-'),(396,3,92,155,1,'video','local','self',1502174669,'','-'),(397,3,92,155,1,'video','local','self',1502174681,'','-'),(398,10,106,170,0,'video','local','self',1502888949,'','-'),(399,3,92,155,3,'video','local','self',1502888983,'','-'),(400,3,92,155,3,'video','local','self',1502888991,'','-'),(401,3,92,155,3,'video','local','self',1502889027,'','-'),(402,5,64,127,3,'video','local','self',1502889083,'','-'),(403,5,64,127,0,'video','local','self',1502889400,'','-'),(404,10,106,170,1,'video','local','self',1503583348,'','-'),(405,3,92,155,1,'video','local','self',1503583375,'','-'),(406,3,92,155,1,'video','local','self',1503583403,'','-'),(407,4,94,157,3,'video','local','self',1503584014,'','-'),(408,4,93,156,3,'video','local','self',1503584065,'','-'),(409,4,94,157,3,'video','local','self',1503584070,'','-'),(410,9,87,150,0,'video','local','self',1503584210,'','-'),(411,5,95,158,3,'video','local','self',1503585725,'','-'),(412,3,92,155,0,'video','local','self',1503673958,'','-'),(413,3,92,155,3,'video','local','self',1503722480,'','-');
/*!40000 ALTER TABLE `course_lesson_view` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_material`
--

DROP TABLE IF EXISTS `course_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_material` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程资料ID',
  `courseId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资料所属课程ID',
  `lessonId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资料所属课时ID',
  `title` varchar(1024) NOT NULL COMMENT '资料标题',
  `description` text COMMENT '资料描述',
  `link` varchar(1024) NOT NULL DEFAULT '' COMMENT '外部链接地址',
  `fileId` int(10) unsigned NOT NULL COMMENT '资料文件ID',
  `fileUri` varchar(255) NOT NULL DEFAULT '' COMMENT '资料文件URI',
  `fileMime` varchar(255) NOT NULL DEFAULT '' COMMENT '资料文件MIME',
  `fileSize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资料文件大小',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资料创建人ID',
  `createdTime` int(10) unsigned NOT NULL COMMENT '资料创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_material`
--

LOCK TABLES `course_material` WRITE;
/*!40000 ALTER TABLE `course_material` DISABLE KEYS */;
/*!40000 ALTER TABLE `course_material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_member`
--

DROP TABLE IF EXISTS `course_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '课程学员记录ID',
  `courseId` int(10) unsigned NOT NULL COMMENT '课程ID',
  `userId` int(10) unsigned NOT NULL COMMENT '学员ID',
  `deadline` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学习最后期限',
  `learnedNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已学课时数',
  `credit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学员已获得的学分',
  `noteNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '笔记数目',
  `noteLastUpdateTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最新的笔记更新时间',
  `isLearned` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已学完',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `isVisible` tinyint(2) NOT NULL DEFAULT '1' COMMENT '可见与否，默认为可见',
  `role` enum('student','teacher') NOT NULL DEFAULT 'student' COMMENT '课程会员角色',
  `deadlineNotified` int(10) NOT NULL DEFAULT '0' COMMENT '有效期通知',
  `createdTime` int(10) unsigned NOT NULL COMMENT '学员加入课程时间',
  PRIMARY KEY (`id`),
  KEY `courseId_webCode` (`courseId`,`userId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_member`
--

LOCK TABLES `course_member` WRITE;
/*!40000 ALTER TABLE `course_member` DISABLE KEYS */;
INSERT INTO `course_member` VALUES (13,10,16,0,0,0,0,0,0,'',1,'teacher',0,1469595519),(14,11,16,0,0,0,0,0,0,'',1,'teacher',0,1469603943),(15,12,16,0,0,0,0,0,0,'',1,'teacher',0,1469604264),(16,13,16,0,0,0,0,0,0,'',1,'teacher',0,1469604480),(17,14,16,0,0,0,0,0,0,'',1,'teacher',0,1469605502),(18,15,16,0,0,0,0,0,0,'',1,'teacher',0,1469605803),(19,16,16,0,0,0,0,0,0,'',1,'teacher',0,1469672998),(20,17,16,0,0,0,0,0,0,'',1,'teacher',0,1469673109),(21,18,16,0,0,0,0,0,0,'',1,'teacher',0,1469673191),(22,19,16,0,0,0,0,0,0,'',1,'teacher',0,1469673919),(38,0,16,0,0,0,0,0,0,'',1,'student',0,1470228486),(39,0,16,0,0,0,0,0,0,'',1,'student',0,1470228808),(40,0,16,0,0,0,0,0,0,'',1,'student',0,1470229399),(41,0,16,0,0,0,0,0,0,'',1,'student',0,1470231155),(42,0,16,0,0,0,0,0,0,'',1,'student',0,1470231157),(45,2,3,0,0,0,0,0,0,'',1,'teacher',0,1492308082),(48,2,3,0,0,0,0,0,0,'',1,'student',0,1494726990),(49,3,3,0,0,0,0,0,0,'',1,'teacher',0,1496044074),(50,3,3,0,0,0,0,0,0,'',1,'student',0,1496044544),(51,4,3,0,0,0,0,0,0,'',1,'teacher',0,1496045767),(52,5,3,0,0,0,0,0,0,'',1,'teacher',0,1496046405),(53,6,3,0,0,0,0,0,0,'',1,'teacher',0,1496046682),(54,7,3,0,0,0,0,0,0,'',1,'teacher',0,1496046759),(55,8,3,0,0,0,0,0,0,'',1,'teacher',0,1496046906),(56,9,3,0,0,0,0,0,0,'',1,'teacher',0,1496047058),(57,9,3,0,0,0,0,0,0,'',1,'student',0,1496140217),(58,7,3,0,0,0,0,0,0,'',1,'student',0,1496230042),(59,9,1,0,0,0,0,0,0,'',1,'student',0,1496245021),(60,7,1,0,0,0,0,0,0,'',1,'student',0,1496245846),(61,3,1,0,0,0,0,0,0,'',1,'student',0,1496245895),(62,4,3,0,0,0,0,0,0,'',1,'student',0,1496246309),(63,5,3,0,0,0,0,0,0,'',1,'student',0,1496246434),(64,10,3,0,0,0,0,0,0,'',1,'teacher',0,1498056846),(65,10,3,0,0,0,0,0,0,'',1,'student',0,1498057119),(66,10,1,0,0,0,0,0,0,'',1,'student',0,1503583343),(67,11,3,0,0,0,0,0,0,'',1,'teacher',0,1503583461);
/*!40000 ALTER TABLE `course_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_note`
--

DROP TABLE IF EXISTS `course_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_note` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '笔记ID',
  `userId` int(10) NOT NULL COMMENT '笔记作者ID',
  `courseId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '课程ID',
  `lessonId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '课时ID',
  `content` text NOT NULL COMMENT '笔记内容',
  `length` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '笔记内容的字数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '笔记状态：0:私有, 1:公开',
  `createdTime` int(10) NOT NULL COMMENT '笔记创建时间',
  `updatedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '笔记更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_note`
--

LOCK TABLES `course_note` WRITE;
/*!40000 ALTER TABLE `course_note` DISABLE KEYS */;
INSERT INTO `course_note` VALUES (1,0,5,5,'<p>一个笔记</p>',4,1,1466147913,1467910793),(2,0,8,10,'<p>第一课，提示未安装Flash，VideoJs报错！</p>',25,1,1470037670,1470037702),(3,12,7,12,'<p>1、本地视频文件还有问题</p>',12,1,1470479450,0),(4,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入</p>',24,1,1470479460,0),(5,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的wenj</p>',39,1,1470479470,0),(6,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入</p>',41,1,1470479480,0),(7,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479490,0),(8,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479500,0),(11,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479530,0),(12,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479540,0),(13,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479550,0),(14,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479560,0),(15,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479570,0),(16,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479580,0),(17,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479590,0),(18,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479600,0),(19,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479610,0),(20,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的文档文件，需要转码的文件还没存入tlc中；</p>\r\n\r\n<p> </p>',51,1,1470479620,0),(21,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、</p>',19,1,1470479635,0),(22,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的dai</p>',28,1,1470479645,0),(23,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的待格式转化d</p>',31,1,1470479655,0),(24,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的待格式转换的文件还没有保存在tlcxi</p>',44,1,1470479665,0),(25,12,7,12,'<p>1、本地视频文件还有问题；</p>\r\n\r\n<p>2、从网盘导入的待格式转换的文件还没有保存在tlc系统中。</p>',46,1,1470479668,0),(26,1,21,22,'<p><strong>1.存储实现<br>\r\n　　存储实现的基本目的是建立数据的机内表示。就是逻辑结构的机内表示和数据元素的机内表示。<br>\r\n　　1)存储结构。数据的机内表示称为存储结构。它包括三个方面：<br>\r\n　　　⑴数据元素的机内表示--存储结点--通常定义为记录类型；<br>\r\n　　　⑵逻辑结构的机内表示--存储结构--与结点间的关联方式有关；<br>\r\n　　　⑶附加结点的机内表示--哑结点--实现运算时附加的结点空间。<br><br>\r\n　　2)存储形式。结点间的关联方式有四种。<br>\r\n　　　⑴顺序存储方式--每个存储结点只含一个数据元素，所有存储结点连续存放。用存储结点间的位置关系表示数据间的逻辑关系。<br>\r\n　　　⑵链式存储方式--每个结点不仅含一个数据元素，还包含一组指针。每个指针指向一个与本结点有逻辑关系的结点，用附加的指针表示数据间的逻辑关系。<br>\r\n　　　⑶索引存储方式--每个存储结点只含一个数据元素，所有存储结点连续存放。增设一个索引表，表中的索引指示各结点的存储位置或位置区间端点。<br>\r\n　　　⑷散列存储方式--每个存储结点只含有一个数据元素，各个结点均匀分布在存储区里。用散列函数指示各结点的的存储位置或位置区间端点。</strong><br><br><strong>　2.运算实现<br>\r\n　　1)运算的实现--一个运算的实现是指一个完成该运算功能的程序。<br>\r\n　　2)伪语言算法--用类C语言描述算法，简称为算法。</strong></p>',557,1,1471429258,1471429409);
/*!40000 ALTER TABLE `course_note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `department_id` char(20) NOT NULL,
  `college_id` char(20) DEFAULT NULL,
  `department_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`department_id`),
  KEY `FK_reference_13` (`college_id`) USING BTREE,
  CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES ('311','31','通信工程系'),('312','31','广播电视工程系'),('313','31','电子信息工程系'),('314','31','自动化系'),('315','31','数字媒体技术系'),('316','31','电子科学与技术系'),('321','32','计算机科学与技术系'),('331','33','应用数学系'),('332','33','统计学系'),('333','33','光电学系'),('334','33','工程基础系');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '上传文件ID',
  `groupId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传文件组ID',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传人ID',
  `uri` varchar(255) NOT NULL COMMENT '文件URI',
  `mime` varchar(255) NOT NULL COMMENT '文件MIME',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件状态',
  `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件上传时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file`
--

LOCK TABLES `file` WRITE;
/*!40000 ALTER TABLE `file` DISABLE KEYS */;
INSERT INTO `file` VALUES (1,3,1,'public://course/2016/04-22/131500447440979987.jpg','',44302,0,1461302100),(2,3,1,'public://course/2016/04-22/131523bec68e465125.jpg','',16793,0,1461302123),(3,3,1,'public://course/2016/04-22/131523bf0aef082850.jpg','',7285,0,1461302123),(4,3,1,'public://course/2016/04-22/131523bf293c647096.jpg','',1606,0,1461302123),(5,3,1,'public://course/2016/04-22/132055788b2f295997.jpg','',13586,0,1461302455),(6,3,1,'public://course/2016/04-22/13205578eb5c348962.jpg','',6032,0,1461302455),(7,3,1,'public://course/2016/04-22/1320557909ac298123.jpg','',1315,0,1461302455),(8,3,1,'public://course/2016/04-22/1321382df62b124200.jpg','',16476,0,1461302498),(9,3,1,'public://course/2016/04-22/1321382e5a46796991.jpg','',7326,0,1461302498),(10,3,1,'public://course/2016/04-22/1321382e78f2120398.jpg','',1584,0,1461302498),(11,4,1,'public://user/2016/04-22/1441404d8628138169.jpg','',14333,0,1461307300),(12,4,16,'public://user/2016/07-19/1553233a5c58990176.jpg','',11124,0,1468914803),(13,4,16,'public://user/2016/07-19/1553233aa2a8657150.jpg','',5145,0,1468914803),(14,4,16,'public://user/2016/07-19/1553233ad570283030.jpg','',1818,0,1468914803),(15,3,12,'public://course/2016/07-19/1956408b42d0406809.jpg','',7706,0,1468929400),(16,3,16,'public://course/2016/07-21/194254e1d8a8651818.jpg','',27353,0,1469101374),(17,3,16,'public://course/2016/07-21/194254e23668467309.jpg','',14119,0,1469101374),(18,3,16,'public://course/2016/07-21/194254e26930684440.jpg','',3086,0,1469101374),(19,4,12,'public://user/2016/08-09/220828c518b0172263.jpg','',11151,0,1470751708),(20,4,12,'public://user/2016/08-09/220828c591c8940096.jpg','',5271,0,1470751708),(21,4,12,'public://user/2016/08-09/220828c5fb40028229.jpg','',1841,0,1470751708),(22,4,12,'public://user/2016/08-09/22091065b4f0572450.jpg','',11786,0,1470751750),(23,4,12,'public://user/2016/08-09/2209106612b0723129.jpg','',5706,0,1470751750),(24,4,12,'public://user/2016/08-09/220910665900074793.jpg','',1873,0,1470751750),(25,4,12,'public://user/2016/08-09/2210466b0068783920.jpg','',11124,0,1470751846),(26,4,12,'public://user/2016/08-09/2210466b5a40869441.jpg','',5145,0,1470751846),(27,4,12,'public://user/2016/08-09/2210466ba478899436.jpg','',1818,0,1470751846),(28,4,12,'public://user/2016/08-09/222134ea77b0476829.jpg','',12057,0,1470752494),(29,4,12,'public://user/2016/08-09/222134ead188174323.jpg','',5743,0,1470752494),(30,4,12,'public://user/2016/08-09/222134eb17d8667990.jpg','',1901,0,1470752494),(31,3,1,'public://course/2016/08-16/163158e1c908406692.jpg','',25415,0,1471336318),(32,3,12,'public://course/2016/08-17/1132088ab630241895.jpg','',28949,0,1471404728),(33,3,12,'public://course/2016/08-17/1132088b7980679936.jpg','',14839,0,1471404728),(34,3,12,'public://course/2016/08-17/1132088c15c0966647.jpg','',2970,0,1471404728),(35,4,12,'public://user/2016/08-17/165819b59998647708.jpg','',11139,0,1471424299),(36,4,12,'public://user/2016/08-17/165819b64578199179.jpg','',5389,0,1471424299),(37,4,12,'public://user/2016/08-17/165819b6ce30294191.jpg','',1698,0,1471424299),(38,4,12,'public://user/2016/08-25/205006eef420725426.jpg','',10569,0,1472129406),(39,4,12,'public://user/2016/08-25/205454ec4c70093572.jpg','',18175,0,1472129694),(40,4,12,'public://user/2016/08-28/17581798e170316371.jpg','',10511,0,1472378297),(41,4,12,'public://user/2016/08-28/2112044e4070824019.jpg','',27559,0,1472389924),(42,4,12,'public://user/2016/08-28/2114499537f0403803.jpg','',10504,0,1472390089),(43,4,12,'public://user/2016/08-28/211506a48828084700.jpg','',24721,0,1472390106),(44,4,12,'public://user/2016/08-28/211523b8a2f0902297.jpg','',47225,0,1472390123),(45,4,12,'public://user/2016/08-31/205937911170397569.jpg','',10421,0,1472648377),(46,4,12,'public://user/2016/08-31/2059473612b0995471.jpg','',47034,0,1472648387),(47,4,12,'public://user/2016/09-02/185403bcb5e8800151.jpg','',9700,0,1472813643),(48,4,12,'public://user/2016/09-02/185413570cb0101974.jpg','',27099,0,1472813653),(49,4,12,'public://user/2016/09-02/1904364a2990342814.png','',44067,0,1472814276),(50,4,12,'public://user/2016/09-06/1651048c63e0437000.jpg','',17269,0,1473151864),(51,4,12,'public://user/2016/09-06/1651048cb9d0352377.jpg','',6774,0,1473151864),(52,4,12,'public://user/2016/09-06/1651048cf850816355.jpg','',1834,0,1473151864),(53,4,12,'public://user/2016/09-07/1320331b1fa8950804.jpg','',11753,0,1473225633),(54,4,12,'public://user/2016/09-07/1320331bdb28325569.jpg','',5675,0,1473225633),(55,4,12,'public://user/2016/09-07/1320331c8708264518.jpg','',1868,0,1473225633),(56,4,1,'public://user/2016/09-09/1021360c5c10931651.jpg','',10546,0,1473387696),(57,4,1,'public://user/2016/09-12/223158e5b108510817.jpg','',10606,0,1473690718),(58,4,1,'public://user/2016/09-12/223158e72038894641.jpg','',4917,0,1473690718),(59,4,1,'public://user/2016/09-12/223158e7f710809127.jpg','',1679,0,1473690718),(60,4,260,'public://user/2016/09-13/100359f4f588188372.jpg','',10606,0,1473732239),(61,4,260,'public://user/2016/09-13/100359f54f60222413.jpg','',4917,0,1473732239),(62,4,260,'public://user/2016/09-13/100359f58de0867734.jpg','',1679,0,1473732239),(63,4,1,'public://user/2016/09-26/153439f497c8409558.jpg','',7269,0,1474875279),(64,4,1,'public://user/2016/09-26/153525d5c0a8350307.jpg','',13001,0,1474875325),(65,3,3,'public://course/2016/10-09/161419b8aac0119942.jpg','',46506,0,1476000859),(66,3,3,'public://course/2016/10-09/161419b99138501082.jpg','',19892,0,1476000859),(67,3,3,'public://course/2016/10-09/161419ba3d18375140.jpg','',3211,0,1476000859),(68,4,2234,'public://user/2016/10-20/133333da21c0600928.jpg','',7238,0,1476941613),(69,4,2234,'public://user/2016/10-20/1333426c5058002720.jpg','',28899,0,1476941622),(70,3,230,'public://course/2016/10-22/1008331ec928852382.jpg','',25107,0,1477102113),(71,3,230,'public://course/2016/10-22/100834208ca0562705.jpg','',12352,0,1477102114),(72,3,230,'public://course/2016/10-22/100834219640848362.jpg','',2798,0,1477102114),(73,4,80,'public://user/2016/10-26/184052499cf0844689.jpg','',13039,0,1477478452),(74,4,80,'public://user/2016/10-26/18410622da78501084.jpg','',28941,0,1477478466),(75,3,3,'public://course/2017/05-31/134653d2868b565958.jpg','',29185,0,1496209613),(76,3,3,'public://course/2017/05-31/134653d2ce59165954.jpg','',14848,0,1496209613),(77,3,3,'public://course/2017/05-31/134653d2e3a1657859.jpg','',3379,0,1496209613),(78,3,3,'public://course/2017/05-31/190652c89e05259390.jpg','',29106,0,1496228812),(79,3,3,'public://course/2017/05-31/190652c8ced4959601.jpg','',14672,0,1496228812),(80,3,3,'public://course/2017/05-31/190652c8db49724458.jpg','',3375,0,1496228812),(81,3,3,'public://course/2017/05-31/191320052ee7945844.jpg','',15916,0,1496229200),(82,3,3,'public://course/2017/05-31/1913200575c5118047.jpg','',8814,0,1496229200),(83,3,3,'public://course/2017/05-31/191320058926680325.jpg','',2586,0,1496229200),(84,3,3,'public://course/2017/05-31/191348cbd10e122767.jpg','',27418,0,1496229228),(85,3,3,'public://course/2017/05-31/191348cc0ae7248110.jpg','',12385,0,1496229228),(86,3,3,'public://course/2017/05-31/191348cc1bf7692243.jpg','',3076,0,1496229228),(87,3,3,'public://course/2017/05-31/19151641cdfc728985.jpg','',14842,0,1496229316),(88,3,3,'public://course/2017/05-31/191516420656422951.jpg','',8860,0,1496229316),(89,3,3,'public://course/2017/05-31/19151642168c007266.jpg','',2296,0,1496229316),(90,3,3,'public://course/2017/05-31/19170407b753438026.jpg','',3401,0,1496229424),(91,3,3,'public://course/2017/05-31/19170407e139966086.jpg','',1920,0,1496229424),(92,3,3,'public://course/2017/05-31/19170407ee7d660688.jpg','',798,0,1496229424),(93,3,3,'public://course/2017/05-31/191732c79f1d037256.jpg','',18823,0,1496229452),(94,3,3,'public://course/2017/05-31/191732c7c5ed616441.jpg','',10024,0,1496229452),(95,3,3,'public://course/2017/05-31/191732c7d1f6881432.jpg','',2570,0,1496229452),(96,3,3,'public://course/2017/05-31/19181025c82a939960.png','',132466,0,1496229490),(97,3,3,'public://course/2017/05-31/191810261506726582.png','',56805,0,1496229490),(98,3,3,'public://course/2017/05-31/19181026254c553196.png','',8750,0,1496229490),(99,3,3,'public://course/2017/05-31/192428c89db5260112.png','',41492,0,1496229868),(100,3,3,'public://course/2017/05-31/192428c8eedf857236.png','',49799,0,1496229868),(101,3,3,'public://course/2017/05-31/192428c90277195099.png','',7270,0,1496229868),(102,3,3,'public://course/2017/05-31/192858a674bb290907.jpg','',31425,0,1496230138),(103,3,3,'public://course/2017/05-31/192858a6bb9b361489.jpg','',16076,0,1496230138),(104,3,3,'public://course/2017/05-31/192858a6cbdf085513.jpg','',3524,0,1496230138),(105,3,3,'public://course/2017/05-31/193056063405250728.jpg','',21885,0,1496230256),(106,3,3,'public://course/2017/05-31/193056066949636457.jpg','',11508,0,1496230256),(107,3,3,'public://course/2017/05-31/1930560680dd767316.jpg','',2827,0,1496230256),(108,3,3,'public://course/2017/06-01/000219bbccec512735.jpg','',29201,0,1496246539),(109,3,3,'public://course/2017/06-01/000219bc16af362783.jpg','',14716,0,1496246539),(110,3,3,'public://course/2017/06-01/000219bc2305420004.jpg','',3419,0,1496246539),(111,3,3,'public://course/2017/06-21/225555b26af5097281.jpg','',15619,0,1498056955),(112,3,3,'public://course/2017/06-21/225555b29356522658.jpg','',8254,0,1498056955),(113,3,3,'public://course/2017/06-21/225555b2a2ab573095.jpg','',2403,0,1498056955);
/*!40000 ALTER TABLE `file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_group`
--

DROP TABLE IF EXISTS `file_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '上传文件组ID',
  `name` varchar(255) NOT NULL COMMENT '上传文件组名称',
  `code` varchar(255) NOT NULL COMMENT '上传文件组编码',
  `public` tinyint(4) NOT NULL DEFAULT '1' COMMENT '文件组文件是否公开',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_group`
--

LOCK TABLES `file_group` WRITE;
/*!40000 ALTER TABLE `file_group` DISABLE KEYS */;
INSERT INTO `file_group` VALUES (1,'默认','default',1),(2,'剪切','thumb',2),(3,'课程','course',3),(4,'用户','user',4),(5,'私密课程','course_private',0),(6,'文章','article',5);
/*!40000 ALTER TABLE `file_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '系统日志ID',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `module` varchar(32) NOT NULL COMMENT '日志所属模块',
  `action` varchar(32) NOT NULL COMMENT '日志所属操作类型',
  `message` text NOT NULL COMMENT '日志内容',
  `data` text COMMENT '日志数据',
  `ip` varchar(255) NOT NULL COMMENT '日志记录IP',
  `createdTime` int(10) unsigned NOT NULL COMMENT '日志发生时间',
  `level` char(10) NOT NULL COMMENT '日志等级',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`) USING BTREE,
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=414 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (1,80,'user','login_success','登录成功','','127.0.0.1',1483344475,'info','','-'),(2,80,'user','login_success','登录成功','','127.0.0.1',1484032705,'info','','-'),(3,230,'user','login_success','登录成功','','127.0.0.1',1484032744,'info','','-'),(4,230,'system','update_settings','更新站点设置','{\"name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"slogan\":\"\\u745e\\u5fb7\\u4e91\\u7f51\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"url\":\"http:\\/\\/tlc.cuc.edu.cn\",\"school_name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\",\"school_english_name\":\"red cloud\",\"logo\":\"\",\"themeCfg\":{\"THEME_ICON_COLOR\":\"33ffdd\",\"THEME_FRONT_COLOR\":\"#ea4335\",\"THEME_BACK_COLOR\":\"#ea4335\",\"THEME_NAV_BACK_COLOR\":\"#ea4335\"},\"favicon\":\"files\\/system\\/favicon_1469184392.ico\",\"seo_keywords\":\"\",\"seo_description\":\"\",\"master_email\":\"326728721@qq.com\",\"copyright\":\"\",\"friend_link_enable\":\"0\",\"friend_link\":[],\"about_us_enable\":\"0\",\"about_us_title\":\"\",\"icp\":\"\\u4e2d\\u56fd\\u4f20\\u5a92\\u5927\\u5b66\\u7406\\u5de5\\u5b66\\u90e8\\u672c\\u79d1\\u6559\\u5b66\\u7ba1\\u7406\\u529e\\u516c\\u5ba4 \\uff08c\\uff092016\",\"contact\":\"010-65779210\",\"analytics\":\"\",\"status\":\"open\",\"closed_note\":\"\",\"_csrf_token\":\"8ee6fff0aa9ec34515d8a9522814c6d6903f421b\"}','127.0.0.1',1484032798,'info','','-'),(5,1,'user','login_success','登录成功','','127.0.0.1',1484032970,'info','','-'),(6,1,'course','create','鲍南海观看课程内容《存储实现与运算实现》','','127.0.0.1',1484033321,'info','','-'),(7,1,'user','login_success','登录成功','','127.0.0.1',1487827227,'info','','-'),(8,288,'user','login_success','登录成功','','127.0.0.1',1490534207,'info','','-'),(9,288,'course','create','甘怡观看课程内容《从网盘导入MP4文件测试》','','127.0.0.1',1490534408,'info','','-'),(10,288,'course','create','甘怡观看课程内容《上传个稍微大一点的视频》','','127.0.0.1',1490534417,'info','','-'),(11,288,'course','create','甘怡观看课程内容《上传个稍微大一点的视频》','','127.0.0.1',1490534496,'info','','-'),(12,288,'course','create','甘怡观看课程内容《从网盘导入MP4文件测试》','','127.0.0.1',1490535272,'info','','-'),(13,230,'user','login_success','登录成功','','127.0.0.1',1490704798,'info','','-'),(14,230,'system','update_settings','更新站点设置','{\"name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"slogan\":\"\\u745e\\u5fb7\\u4e91\\u7f51\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"url\":\"http:\\/\\/tlc.com\",\"school_name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\",\"school_english_name\":\"red cloud\",\"logo\":\"\",\"themeCfg\":{\"THEME_ICON_COLOR\":\"33ffdd\",\"THEME_FRONT_COLOR\":\"#ea4335\",\"THEME_BACK_COLOR\":\"#ea4335\",\"THEME_NAV_BACK_COLOR\":\"#ea4335\"},\"favicon\":\"files\\/system\\/favicon_1469184392.ico\",\"seo_keywords\":\"\",\"seo_description\":\"\",\"master_email\":\"326728721@qq.com\",\"copyright\":\"\",\"friend_link_enable\":\"0\",\"friend_link\":[],\"about_us_enable\":\"0\",\"about_us_title\":\"\",\"icp\":\"\\u4e2d\\u56fd\\u4f20\\u5a92\\u5927\\u5b66\\u7406\\u5de5\\u5b66\\u90e8\\u672c\\u79d1\\u6559\\u5b66\\u7ba1\\u7406\\u529e\\u516c\\u5ba4 \\uff08c\\uff092016\",\"contact\":\"010-65779210\",\"analytics\":\"\",\"status\":\"open\",\"closed_note\":\"\",\"_csrf_token\":\"eaf3a334abd7397fe0e71fd4d34b1fcee7437eb9\"}','127.0.0.1',1490706685,'info','','-'),(15,230,'user','login_success','登录成功','','127.0.0.1',1490708671,'info','','-'),(16,230,'course','delete','删除课程《大数据》(#24)','','127.0.0.1',1490708682,'info','','-'),(17,230,'course','delete','删除课程《我和林卫国老师一起上的课》(#23)','','127.0.0.1',1490708690,'info','','-'),(18,230,'course','delete','删除课程《数据库原理》(#22)','','127.0.0.1',1490708693,'info','','-'),(19,230,'course','delete','删除课程《数据结构与算法》(#21)','','127.0.0.1',1490708707,'info','','-'),(20,230,'course','delete','删除课程《信息安全》(#20)','','127.0.0.1',1490708768,'info','','-'),(21,230,'course','delete','删除课程《Python入门基础》(#9)','','127.0.0.1',1490708771,'info','','-'),(22,230,'course','delete','删除课程《计算机网络程序设计》(#7)','','127.0.0.1',1490708773,'info','','-'),(23,230,'course','delete','删除课程《毛泽东概论修改测试》(#5)','','127.0.0.1',1490708776,'info','','-'),(24,230,'course','delete','删除课程《部署测试4》(#4)','','127.0.0.1',1490708778,'info','','-'),(25,230,'course','delete','删除课程《部署测试3》(#3)','','127.0.0.1',1490708782,'info','','-'),(26,3,'user','login_success','登录成功','','127.0.0.1',1491689668,'info','','-'),(27,3,'user','login_success','登录成功','','127.0.0.1',1491690067,'info','','-'),(28,3,'draft','update','更新草稿《111》(#1)的信息','{\"title\":\"111\",\"summary\":\"111\",\"content\":\"<p>sdfsadf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asd<\\/p>\\n\",\"createdTime\":6}','127.0.0.1',1491690160,'info','','-'),(29,3,'draft','update','更新草稿《111》(#1)的信息','{\"title\":\"111\",\"summary\":\"111\",\"content\":\"<p>sdfsadf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>asdf<\\/p>\\n\\n<p>&nbsp;<\\/p>\\n\",\"createdTime\":6}','127.0.0.1',1491690165,'info','','-'),(30,3,'course','add_lesson','添加课程内容《111》(1)','{\"id\":\"1\",\"courseId\":\"1\",\"chapterId\":\"3\",\"number\":\"1\",\"seq\":\"5\",\"status\":\"published\",\"title\":\"111\",\"summary\":\"111\",\"tags\":null,\"type\":\"text\",\"content\":\"<p>sdfsadf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>asdf<\\/p>\\r\\n\\r\\n<p>&nbsp;<\\/p>\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"0\",\"mediaSource\":\"\",\"mediaName\":\"\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1491690166\",\"isTest\":\"0\"}','127.0.0.1',1491690166,'info','','-'),(31,3,'course','publish','发布课程《1》(#1)','','127.0.0.1',1491690175,'info','','-'),(32,3,'user','login_success','登录成功','','127.0.0.1',1491690919,'info','','-'),(33,1,'user','login_success','登录成功','','127.0.0.1',1491690934,'info','','-'),(34,1,'user','login_success','登录成功','','127.0.0.1',1491690988,'info','','-'),(35,2,'user','login_success','登录成功','','127.0.0.1',1491691000,'info','','-'),(36,3,'user','login_success','登录成功','','127.0.0.1',1491691015,'info','','-'),(37,3,'user','login_success','登录成功','','127.0.0.1',1491710867,'info','','-'),(38,3,'user','login_success','登录成功','','127.0.0.1',1491711509,'info','','-'),(39,3,'user','login_success','登录成功','','127.0.0.1',1491714513,'info','','-'),(40,3,'user','login_success','登录成功','','127.0.0.1',1491715664,'info','','-'),(41,2,'user','login_success','登录成功','','127.0.0.1',1491752588,'info','','-'),(42,3,'user','login_success','登录成功','','127.0.0.1',1491814278,'info','','-'),(43,3,'user','login_success','登录成功','','127.0.0.1',1491815274,'info','','-'),(44,3,'user','login_success','登录成功','','127.0.0.1',1492268276,'info','','-'),(45,3,'user','login_success','登录成功','','127.0.0.1',1492306458,'info','','-'),(46,3,'user','login_success','登录成功','','127.0.0.1',1492309973,'info','','-'),(47,2,'user','login_success','登录成功','','127.0.0.1',1492313252,'info','','-'),(48,3,'user','login_success','登录成功','','127.0.0.1',1492313291,'info','','-'),(49,3,'user','login_success','登录成功','','127.0.0.1',1492315414,'info','','-'),(50,3,'user','login_success','登录成功','','127.0.0.1',1492316866,'info','','-'),(51,2,'user','login_success','登录成功','','127.0.0.1',1492316912,'info','','-'),(52,3,'user','login_success','登录成功','','127.0.0.1',1492316927,'info','','-'),(53,3,'user','login_success','登录成功','','127.0.0.1',1492318630,'info','','-'),(54,3,'user','login_success','登录成功','','127.0.0.1',1492321700,'info','','-'),(55,3,'user','login_success','登录成功','','127.0.0.1',1492322258,'info','','-'),(56,3,'user','login_success','登录成功','','127.0.0.1',1492383063,'info','','-'),(57,3,'user','login_success','登录成功','','127.0.0.1',1492433174,'info','','-'),(58,3,'user','login_success','登录成功','','127.0.0.1',1492470477,'info','','-'),(59,3,'user','login_success','登录成功','','127.0.0.1',1492519437,'info','','-'),(60,3,'user','login_success','登录成功','','127.0.0.1',1492687059,'info','','-'),(61,3,'user','login_success','登录成功','','127.0.0.1',1492687294,'info','','-'),(62,1,'user','login_success','登录成功','','127.0.0.1',1492687860,'info','','-'),(63,2,'user','login_success','登录成功','','127.0.0.1',1492687894,'info','','-'),(64,2,'course','publish','发布课程《大数据基础课程》(#3)','','127.0.0.1',1492688759,'info','','-'),(65,3,'user','login_success','登录成功','','127.0.0.1',1493768879,'info','','-'),(66,3,'user','login_success','登录成功','','127.0.0.1',1493814692,'info','','-'),(67,3,'course','add_lesson','添加课程内容《Hadoop概述》(2)','{\"id\":\"2\",\"courseId\":\"3\",\"chapterId\":\"6\",\"number\":\"1\",\"seq\":\"2\",\"status\":\"unpublished\",\"title\":\"Hadoop\\u6982\\u8ff0\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"64\",\"mediaSource\":\"self\",\"mediaName\":\"1.Hadoop\\u6982\\u8ff0.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1493816410\",\"isTest\":\"0\"}','127.0.0.1',1493816410,'info','','-'),(68,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493816418,'info','','-'),(69,3,'user','login_success','登录成功','','127.0.0.1',1493816472,'info','','-'),(70,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493816483,'info','','-'),(71,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493816867,'info','','-'),(72,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493817178,'info','','-'),(73,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493817197,'info','','-'),(74,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493817215,'info','','-'),(75,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493817293,'info','','-'),(76,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493817335,'info','','-'),(77,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493817514,'info','','-'),(78,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493818417,'info','','-'),(79,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1493818466,'info','','-'),(80,3,'user','login_success','登录成功','','127.0.0.1',1494284907,'info','','-'),(81,3,'user','login_success','登录成功','','127.0.0.1',1494334004,'info','','-'),(82,3,'user','login_success','登录成功','','127.0.0.1',1494335427,'info','','-'),(83,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1494336033,'info','','-'),(84,3,'course','add_lesson','添加课程内容《Hadoop概述》(3)','{\"id\":\"3\",\"courseId\":\"3\",\"chapterId\":\"6\",\"number\":\"1\",\"seq\":\"3\",\"status\":\"unpublished\",\"title\":\"Hadoop\\u6982\\u8ff0\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"65\",\"mediaSource\":\"self\",\"mediaName\":\"1.Hadoop\\u6982\\u8ff0.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494337938\",\"isTest\":\"0\"}','127.0.0.1',1494337938,'info','','-'),(85,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1494337942,'info','','-'),(86,3,'lesson','delete','删除课程《大数据基础课程》(#3)的课程内容 Hadoop概述','','127.0.0.1',1494338442,'info','','-'),(87,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1494338829,'info','','-'),(88,3,'course','create','管理员观看课程内容《Hadoop概述》','','127.0.0.1',1494338878,'info','','-'),(89,3,'course','add_lesson','添加课程内容《Hadoop安装配置》(4)','{\"id\":\"4\",\"courseId\":\"3\",\"chapterId\":\"6\",\"number\":\"1\",\"seq\":\"4\",\"status\":\"unpublished\",\"title\":\"Hadoop\\u5b89\\u88c5\\u914d\\u7f6e\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"66\",\"mediaSource\":\"self\",\"mediaName\":\"hadoop\\u5b89\\u88c5\\u914d\\u7f6e.pptx\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494338917\",\"isTest\":\"0\"}','127.0.0.1',1494338917,'info','','-'),(90,3,'user','login_success','登录成功','','127.0.0.1',1494421177,'info','','-'),(91,3,'user','login_success','登录成功','','127.0.0.1',1494421792,'info','','-'),(92,3,'user','login_success','登录成功','','127.0.0.1',1494723618,'info','','-'),(93,3,'user','login_success','登录成功','','127.0.0.1',1494724829,'info','','-'),(94,3,'user','login_success','登录成功','','127.0.0.1',1494726698,'info','','-'),(95,3,'course','add_lesson','添加课程内容《什么是Linux》(5)','{\"id\":\"5\",\"courseId\":\"2\",\"chapterId\":\"8\",\"number\":\"1\",\"seq\":\"5\",\"status\":\"published\",\"title\":\"\\u4ec0\\u4e48\\u662fLinux\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"67\",\"mediaSource\":\"self\",\"mediaName\":\"1.\\u4ec0\\u4e48\\u662fLinux.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494726943\",\"isTest\":\"0\"}','127.0.0.1',1494726943,'info','','-'),(96,3,'course','add_lesson','添加课程内容《Linux》(6)','{\"id\":\"6\",\"courseId\":\"2\",\"chapterId\":\"9\",\"number\":\"2\",\"seq\":\"6\",\"status\":\"published\",\"title\":\"Linux\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"68\",\"mediaSource\":\"self\",\"mediaName\":\"2.Linux\\u4e3b\\u8981\\u7248\\u672c.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494726955\",\"isTest\":\"0\"}','127.0.0.1',1494726955,'info','','-'),(97,3,'course','update_lesson','更新课程内容《Linux的主要版本》(6)','{\"id\":\"6\",\"courseId\":\"2\",\"chapterId\":\"9\",\"number\":\"2\",\"seq\":\"6\",\"status\":\"published\",\"title\":\"Linux\\u7684\\u4e3b\\u8981\\u7248\\u672c\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"68\",\"mediaSource\":\"self\",\"mediaName\":\"2.Linux\\u4e3b\\u8981\\u7248\\u672c.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494726955\",\"isTest\":\"0\"}','127.0.0.1',1494726966,'info','','-'),(98,3,'course','add_lesson','添加课程内容《Linux的优缺点》(7)','{\"id\":\"7\",\"courseId\":\"2\",\"chapterId\":\"10\",\"number\":\"3\",\"seq\":\"7\",\"status\":\"published\",\"title\":\"Linux\\u7684\\u4f18\\u7f3a\\u70b9\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"69\",\"mediaSource\":\"self\",\"mediaName\":\"3.Linux\\u7684\\u4f18\\u7f3a\\u70b9.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494726986\",\"isTest\":\"0\"}','127.0.0.1',1494726986,'info','','-'),(99,3,'course','create','管理员观看课程内容《什么是Linux》','','127.0.0.1',1494726991,'info','','-'),(100,3,'lesson','delete','删除课程《大数据基础课程》(#2)的课程内容 Linux的主要版本','','127.0.0.1',1494727695,'info','','-'),(101,3,'lesson','delete','删除课程《大数据基础课程》(#2)的课程内容 Linux的优缺点','','127.0.0.1',1494727723,'info','','-'),(102,3,'course','add_lesson','添加课程内容《Linux的主要版本》(8)','{\"id\":\"8\",\"courseId\":\"2\",\"chapterId\":\"10\",\"number\":\"2\",\"seq\":\"10\",\"status\":\"published\",\"title\":\"Linux\\u7684\\u4e3b\\u8981\\u7248\\u672c\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"70\",\"mediaSource\":\"self\",\"mediaName\":\"2.Linux\\u4e3b\\u8981\\u7248\\u672c.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494727748\",\"isTest\":\"0\"}','127.0.0.1',1494727748,'info','','-'),(103,3,'course','create','管理员观看课程内容《Linux的主要版本》','','127.0.0.1',1494727752,'info','','-'),(104,3,'course','add_lesson','添加课程内容《Linux的优缺点》(9)','{\"id\":\"9\",\"courseId\":\"2\",\"chapterId\":\"13\",\"number\":\"3\",\"seq\":\"12\",\"status\":\"published\",\"title\":\"Linux\\u7684\\u4f18\\u7f3a\\u70b9\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"71\",\"mediaSource\":\"self\",\"mediaName\":\"3.Linux\\u7684\\u4f18\\u7f3a\\u70b9.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494727811\",\"isTest\":\"0\"}','127.0.0.1',1494727811,'info','','-'),(105,3,'course','create','管理员观看课程内容《Linux的优缺点》','','127.0.0.1',1494727814,'info','','-'),(106,3,'course','add_lesson','添加课程内容《什么是Linux》(10)','{\"id\":\"10\",\"courseId\":\"2\",\"chapterId\":\"8\",\"number\":\"4\",\"seq\":\"13\",\"status\":\"published\",\"title\":\"\\u4ec0\\u4e48\\u662fLinux\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"72\",\"mediaSource\":\"self\",\"mediaName\":\"1.\\u4ec0\\u4e48\\u662fLinux.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494727926\",\"isTest\":\"0\"}','127.0.0.1',1494727926,'info','','-'),(107,3,'course','create','管理员观看课程内容《什么是Linux》','','127.0.0.1',1494727931,'info','','-'),(108,3,'course','create','管理员观看课程内容《什么是Linux》','','127.0.0.1',1494727939,'info','','-'),(109,3,'lesson','delete','删除课程《大数据基础课程》(#2)的课程内容 什么是Linux','','127.0.0.1',1494727959,'info','','-'),(110,3,'lesson','delete','删除课程《大数据基础课程》(#2)的课程内容 Linux的主要版本','','127.0.0.1',1494727963,'info','','-'),(111,3,'course','add_lesson','添加课程内容《Linux的主要版本》(11)','{\"id\":\"11\",\"courseId\":\"2\",\"chapterId\":\"10\",\"number\":\"3\",\"seq\":\"13\",\"status\":\"published\",\"title\":\"Linux\\u7684\\u4e3b\\u8981\\u7248\\u672c\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"73\",\"mediaSource\":\"self\",\"mediaName\":\"2.Linux\\u4e3b\\u8981\\u7248\\u672c.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494727988\",\"isTest\":\"0\"}','127.0.0.1',1494727988,'info','','-'),(112,3,'lesson','delete','删除课程《大数据基础课程》(#2)的课程内容 Linux的优缺点','','127.0.0.1',1494727994,'info','','-'),(113,3,'course','add_lesson','添加课程内容《Linux的优缺点》(12)','{\"id\":\"12\",\"courseId\":\"2\",\"chapterId\":\"13\",\"number\":\"3\",\"seq\":\"14\",\"status\":\"published\",\"title\":\"Linux\\u7684\\u4f18\\u7f3a\\u70b9\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"74\",\"mediaSource\":\"self\",\"mediaName\":\"3.Linux\\u7684\\u4f18\\u7f3a\\u70b9.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494728009\",\"isTest\":\"0\"}','127.0.0.1',1494728009,'info','','-'),(114,3,'course','add_lesson','添加课程内容《Hadoop概述》(13)','{\"id\":\"13\",\"courseId\":\"2\",\"chapterId\":\"12\",\"number\":\"4\",\"seq\":\"15\",\"status\":\"published\",\"title\":\"Hadoop\\u6982\\u8ff0\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"75\",\"mediaSource\":\"self\",\"mediaName\":\"1.Hadoop\\u6982\\u8ff0.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494728036\",\"isTest\":\"0\"}','127.0.0.1',1494728036,'info','','-'),(115,3,'course','add_lesson','添加课程内容《HBase概述》(14)','{\"id\":\"14\",\"courseId\":\"2\",\"chapterId\":\"18\",\"number\":\"5\",\"seq\":\"21\",\"status\":\"published\",\"title\":\"HBase\\u6982\\u8ff0\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"76\",\"mediaSource\":\"self\",\"mediaName\":\"2.HBase\\u6982\\u8ff0.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494728387\",\"isTest\":\"0\"}','127.0.0.1',1494728387,'info','','-'),(116,3,'course','add_lesson','添加课程内容《HBase术语及数据模型》(15)','{\"id\":\"15\",\"courseId\":\"2\",\"chapterId\":\"19\",\"number\":\"6\",\"seq\":\"23\",\"status\":\"published\",\"title\":\"HBase\\u672f\\u8bed\\u53ca\\u6570\\u636e\\u6a21\\u578b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"77\",\"mediaSource\":\"self\",\"mediaName\":\"2.HBase\\u672f\\u8bed\\u53ca\\u6570\\u636e\\u6a21\\u578b.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494728466\",\"isTest\":\"0\"}','127.0.0.1',1494728466,'info','','-'),(117,3,'course','add_lesson','添加课程内容《Hive简介》(16)','{\"id\":\"16\",\"courseId\":\"2\",\"chapterId\":\"24\",\"number\":\"7\",\"seq\":\"31\",\"status\":\"published\",\"title\":\"Hive\\u7b80\\u4ecb\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"78\",\"mediaSource\":\"self\",\"mediaName\":\"3.Hive\\u7b80\\u4ecb.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494728705\",\"isTest\":\"0\"}','127.0.0.1',1494728705,'info','','-'),(118,3,'course','add_lesson','添加课程内容《Hive与HBase的关系》(17)','{\"id\":\"17\",\"courseId\":\"2\",\"chapterId\":\"25\",\"number\":\"8\",\"seq\":\"32\",\"status\":\"published\",\"title\":\"Hive\\u4e0eHBase\\u7684\\u5173\\u7cfb\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"79\",\"mediaSource\":\"self\",\"mediaName\":\"3.Hive\\u4e0eHBase\\u5173\\u7cfb.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494728724\",\"isTest\":\"0\"}','127.0.0.1',1494728724,'info','','-'),(119,3,'course','add_lesson','添加课程内容《Spark简介》(18)','{\"id\":\"18\",\"courseId\":\"2\",\"chapterId\":\"30\",\"number\":\"9\",\"seq\":\"39\",\"status\":\"published\",\"title\":\"Spark\\u7b80\\u4ecb\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"80\",\"mediaSource\":\"self\",\"mediaName\":\"4.Spark\\u7b80\\u4ecb.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494728947\",\"isTest\":\"0\"}','127.0.0.1',1494728947,'info','','-'),(120,3,'course','add_lesson','添加课程内容《Spark计算模型》(19)','{\"id\":\"19\",\"courseId\":\"2\",\"chapterId\":\"31\",\"number\":\"10\",\"seq\":\"40\",\"status\":\"published\",\"title\":\"Spark\\u8ba1\\u7b97\\u6a21\\u578b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"81\",\"mediaSource\":\"self\",\"mediaName\":\"4.Spark\\u8ba1\\u7b97\\u6a21\\u578b.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494728966\",\"isTest\":\"0\"}','127.0.0.1',1494728966,'info','','-'),(121,3,'course','add_lesson','添加课程内容《Hadoop安装配置》(20)','{\"id\":\"20\",\"courseId\":\"2\",\"chapterId\":\"7\",\"number\":\"11\",\"seq\":\"41\",\"status\":\"published\",\"title\":\"Hadoop\\u5b89\\u88c5\\u914d\\u7f6e\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"82\",\"mediaSource\":\"self\",\"mediaName\":\"hadoop\\u5b89\\u88c5\\u914d\\u7f6e.pptx\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494729039\",\"isTest\":\"0\"}','127.0.0.1',1494729039,'info','','-'),(122,3,'lesson','delete','删除课程《大数据基础课程》(#2)的课程内容 Hadoop安装配置','','127.0.0.1',1494729058,'info','','-'),(123,3,'user','login_success','登录成功','','127.0.0.1',1494745243,'info','','-'),(124,3,'course','add_lesson','添加课程内容《Hadoop安装配置》(20)','{\"id\":\"20\",\"courseId\":\"2\",\"chapterId\":\"11\",\"number\":\"11\",\"seq\":\"42\",\"status\":\"published\",\"title\":\"Hadoop\\u5b89\\u88c5\\u914d\\u7f6e\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"82\",\"mediaSource\":\"self\",\"mediaName\":\"Hadoop\\u5b89\\u88c5\\u914d\\u7f6e.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494745327\",\"isTest\":\"0\"}','127.0.0.1',1494745327,'info','','-'),(125,3,'course','add_lesson','添加课程内容《HDFS的基本使用》(21)','{\"id\":\"21\",\"courseId\":\"2\",\"chapterId\":\"15\",\"number\":\"12\",\"seq\":\"43\",\"status\":\"published\",\"title\":\"HDFS\\u7684\\u57fa\\u672c\\u4f7f\\u7528\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"83\",\"mediaSource\":\"self\",\"mediaName\":\"HDFS\\u7684\\u57fa\\u672c\\u4f7f\\u7528.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494745368\",\"isTest\":\"0\"}','127.0.0.1',1494745368,'info','','-'),(126,3,'course','add_lesson','添加课程内容《HBase 环境安装配置》(22)','{\"id\":\"22\",\"courseId\":\"2\",\"chapterId\":\"17\",\"number\":\"13\",\"seq\":\"44\",\"status\":\"published\",\"title\":\"HBase \\u73af\\u5883\\u5b89\\u88c5\\u914d\\u7f6e\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"84\",\"mediaSource\":\"self\",\"mediaName\":\"HBase\\u5b89\\u88c5\\u914d\\u7f6e .pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494745405\",\"isTest\":\"0\"}','127.0.0.1',1494745405,'info','','-'),(127,3,'course','add_lesson','添加课程内容《HBase Shell的基本使用》(23)','{\"id\":\"23\",\"courseId\":\"2\",\"chapterId\":\"21\",\"number\":\"14\",\"seq\":\"45\",\"status\":\"published\",\"title\":\"HBase Shell\\u7684\\u57fa\\u672c\\u4f7f\\u7528\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"85\",\"mediaSource\":\"self\",\"mediaName\":\"HBase Shell\\u7684\\u57fa\\u672c\\u4f7f\\u7528 .pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494745448\",\"isTest\":\"0\"}','127.0.0.1',1494745448,'info','','-'),(128,3,'course','add_lesson','添加课程内容《Hive环境安装》(24)','{\"id\":\"24\",\"courseId\":\"2\",\"chapterId\":\"23\",\"number\":\"15\",\"seq\":\"46\",\"status\":\"published\",\"title\":\"Hive\\u73af\\u5883\\u5b89\\u88c5\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"86\",\"mediaSource\":\"self\",\"mediaName\":\"Hive\\u5b89\\u88c5\\u914d\\u7f6e.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494745501\",\"isTest\":\"0\"}','127.0.0.1',1494745501,'info','','-'),(129,3,'course','add_lesson','添加课程内容《Hive Sql基本命令》(25)','{\"id\":\"25\",\"courseId\":\"2\",\"chapterId\":\"27\",\"number\":\"16\",\"seq\":\"47\",\"status\":\"published\",\"title\":\"Hive Sql\\u57fa\\u672c\\u547d\\u4ee4\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"87\",\"mediaSource\":\"self\",\"mediaName\":\"Hive Sql\\u7684\\u57fa\\u672c\\u547d\\u4ee4 .pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494745536\",\"isTest\":\"0\"}','127.0.0.1',1494745536,'info','','-'),(130,3,'course','add_lesson','添加课程内容《Spark安装与操作》(26)','{\"id\":\"26\",\"courseId\":\"2\",\"chapterId\":\"29\",\"number\":\"17\",\"seq\":\"48\",\"status\":\"published\",\"title\":\"Spark\\u5b89\\u88c5\\u4e0e\\u64cd\\u4f5c\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"88\",\"mediaSource\":\"self\",\"mediaName\":\"Spark\\u5b89\\u88c5\\u4e0e\\u64cd\\u4f5c.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1494745560\",\"isTest\":\"0\"}','127.0.0.1',1494745560,'info','','-'),(131,3,'course','create','管理员观看课程内容《什么是Linux》','','127.0.0.1',1494745570,'info','','-'),(132,3,'user','login_success','登录成功','','127.0.0.1',1494746765,'info','','-'),(133,3,'user','login_success','登录成功','','127.0.0.1',1494891304,'info','','-'),(134,3,'user','login_success','登录成功','','127.0.0.1',1494940441,'info','','-'),(135,3,'user','login_success','登录成功','','127.0.0.1',1494944055,'info','','-'),(136,3,'user','login_success','登录成功','','127.0.0.1',1494978041,'info','','-'),(137,3,'user','login_success','登录成功','','127.0.0.1',1494978954,'info','','-'),(138,3,'user','login_success','登录成功','','127.0.0.1',1495405445,'info','','-'),(139,3,'course','close','关闭课程《大数据基础课程》(#3)','','127.0.0.1',1495405877,'info','','-'),(140,3,'course','delete','删除课程《大数据基础课程》(#3)','','127.0.0.1',1495405929,'info','','-'),(141,3,'course','publish','发布课程《大数据基础课程》(#2)','','127.0.0.1',1495405975,'info','','-'),(142,3,'user','login_success','登录成功','','127.0.0.1',1495456475,'info','','-'),(143,3,'user','login_success','登录成功','','127.0.0.1',1495494297,'info','','-'),(144,3,'user','login_success','登录成功','','127.0.0.1',1495542146,'info','','-'),(145,3,'user','login_success','登录成功','','127.0.0.1',1495545772,'info','','-'),(146,3,'lesson','delete','删除课程《大数据基础课程》(#2)的课程内容 Hive环境安装','','127.0.0.1',1495545796,'info','','-'),(147,3,'course','add_lesson','添加课程内容《Hive安装配置》(27)','{\"id\":\"27\",\"courseId\":\"2\",\"chapterId\":\"23\",\"number\":\"17\",\"seq\":\"49\",\"status\":\"unpublished\",\"title\":\"Hive\\u5b89\\u88c5\\u914d\\u7f6e\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"89\",\"mediaSource\":\"self\",\"mediaName\":\"Hive\\u5b89\\u88c5\\u914d\\u7f6e.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1495545827\",\"isTest\":\"0\"}','127.0.0.1',1495545827,'info','','-'),(148,3,'lesson','delete','删除课程《大数据基础课程》(#2)的课程内容 Hive Sql基本命令','','127.0.0.1',1495545839,'info','','-'),(149,3,'course','add_lesson','添加课程内容《Hive Sql基本命令》(28)','{\"id\":\"28\",\"courseId\":\"2\",\"chapterId\":\"27\",\"number\":\"17\",\"seq\":\"50\",\"status\":\"unpublished\",\"title\":\"Hive Sql\\u57fa\\u672c\\u547d\\u4ee4\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"90\",\"mediaSource\":\"self\",\"mediaName\":\"Hive Sql\\u7684\\u57fa\\u672c\\u547d\\u4ee4 .pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1495545858\",\"isTest\":\"0\"}','127.0.0.1',1495545858,'info','','-'),(150,3,'user','login_success','登录成功','','127.0.0.1',1495579413,'info','','-'),(151,3,'user','login_success','登录成功','','127.0.0.1',1496043667,'info','','-'),(152,3,'course','add_lesson','添加课程内容《初识Hadoop （上）》(29)','{\"id\":\"29\",\"courseId\":\"3\",\"chapterId\":\"35\",\"number\":\"1\",\"seq\":\"4\",\"status\":\"published\",\"title\":\"\\u521d\\u8bc6Hadoop \\uff08\\u4e0a\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"91\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c4\\u7ae0_\\u7b2c1\\u8282_\\u521d\\u8bc6Hadoop(\\u4e0a\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496044474\",\"isTest\":\"0\"}','127.0.0.1',1496044474,'info','','-'),(153,3,'course','add_lesson','添加课程内容《初识Hadoop（下）》(30)','{\"id\":\"30\",\"courseId\":\"3\",\"chapterId\":\"36\",\"number\":\"2\",\"seq\":\"5\",\"status\":\"published\",\"title\":\"\\u521d\\u8bc6Hadoop\\uff08\\u4e0b\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"93\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c4\\u7ae0_\\u7b2c1\\u8282_\\u521d\\u8bc6Hadoop\\uff08\\u4e0b\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496044541\",\"isTest\":\"0\"}','127.0.0.1',1496044541,'info','','-'),(154,3,'course','add_lesson','添加课程内容《Hadoop集群伪分布式搭建指南》(31)','{\"id\":\"31\",\"courseId\":\"3\",\"chapterId\":\"38\",\"number\":\"3\",\"seq\":\"9\",\"status\":\"published\",\"title\":\"Hadoop\\u96c6\\u7fa4\\u4f2a\\u5206\\u5e03\\u5f0f\\u642d\\u5efa\\u6307\\u5357\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"94\",\"mediaSource\":\"self\",\"mediaName\":\"\\u8bfe\\u7a0b3\\u5b9e\\u9a8c5 HADOOP\\u96c6\\u7fa4\\u4f2a\\u5206\\u5e03\\u5f0f\\u642d\\u5efa-\\u5b9e\\u9a8c\\u6307\\u5357.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496044719\",\"isTest\":\"0\"}','127.0.0.1',1496044719,'info','','-'),(155,3,'course','add_lesson','添加课程内容《hadoop_1》(32)','{\"id\":\"32\",\"courseId\":\"3\",\"chapterId\":\"39\",\"number\":\"4\",\"seq\":\"10\",\"status\":\"published\",\"title\":\"hadoop_1\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"95\",\"mediaSource\":\"self\",\"mediaName\":\"hadoop_1.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496044768\",\"isTest\":\"0\"}','127.0.0.1',1496044768,'info','','-'),(156,3,'course','add_lesson','添加课程内容《hadoop_2》(33)','{\"id\":\"33\",\"courseId\":\"3\",\"chapterId\":\"39\",\"number\":\"5\",\"seq\":\"11\",\"status\":\"published\",\"title\":\"hadoop_2\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"96\",\"mediaSource\":\"self\",\"mediaName\":\"hadoop_2.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496044784\",\"isTest\":\"0\"}','127.0.0.1',1496044784,'info','','-'),(157,3,'course','add_lesson','添加课程内容《hadoop_3》(34)','{\"id\":\"34\",\"courseId\":\"3\",\"chapterId\":\"39\",\"number\":\"6\",\"seq\":\"12\",\"status\":\"published\",\"title\":\"hadoop_3\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"97\",\"mediaSource\":\"self\",\"mediaName\":\"hadoop_3.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496044799\",\"isTest\":\"0\"}','127.0.0.1',1496044799,'info','','-'),(158,3,'course','add_lesson','添加课程内容《HDFS文件系统（上）》(35)','{\"id\":\"35\",\"courseId\":\"3\",\"chapterId\":\"41\",\"number\":\"7\",\"seq\":\"16\",\"status\":\"published\",\"title\":\"HDFS\\u6587\\u4ef6\\u7cfb\\u7edf\\uff08\\u4e0a\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"98\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c4\\u7ae0_\\u7b2c2\\u8282_HDFS\\u6587\\u4ef6\\u7cfb\\u7edf\\uff08\\u4e0a\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496044894\",\"isTest\":\"0\"}','127.0.0.1',1496044894,'info','','-'),(159,3,'course','add_lesson','添加课程内容《HDFS文件系统（下）》(36)','{\"id\":\"36\",\"courseId\":\"3\",\"chapterId\":\"42\",\"number\":\"8\",\"seq\":\"17\",\"status\":\"published\",\"title\":\"HDFS\\u6587\\u4ef6\\u7cfb\\u7edf\\uff08\\u4e0b\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"99\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c4\\u7ae0_\\u7b2c2\\u8282_HDFS\\u6587\\u4ef6\\u7cfb\\u7edf\\uff08\\u4e0b\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496044916\",\"isTest\":\"0\"}','127.0.0.1',1496044916,'info','','-'),(160,3,'course','add_lesson','添加课程内容《Hadoop基本操作指南》(37)','{\"id\":\"37\",\"courseId\":\"3\",\"chapterId\":\"44\",\"number\":\"9\",\"seq\":\"20\",\"status\":\"published\",\"title\":\"Hadoop\\u57fa\\u672c\\u64cd\\u4f5c\\u6307\\u5357\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"100\",\"mediaSource\":\"self\",\"mediaName\":\"\\u8bfe\\u7a0b3\\u5b9e\\u9a8c6HADOOP\\u57fa\\u672c\\u64cd\\u4f5c-\\u5b9e\\u9a8c\\u6307\\u5357.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045071\",\"isTest\":\"0\"}','127.0.0.1',1496045071,'info','','-'),(161,3,'course','add_lesson','添加课程内容《Hadoop Shell 学习资料》(38)','{\"id\":\"38\",\"courseId\":\"3\",\"chapterId\":\"43\",\"number\":\"10\",\"seq\":\"21\",\"status\":\"published\",\"title\":\"Hadoop Shell \\u5b66\\u4e60\\u8d44\\u6599\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"101\",\"mediaSource\":\"self\",\"mediaName\":\"hadoop-shell-\\u5b66\\u4e60\\u8d44\\u6599.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045107\",\"isTest\":\"0\"}','127.0.0.1',1496045107,'info','','-'),(162,3,'course','add_lesson','添加课程内容《MapReduce计算框架（上）》(39)','{\"id\":\"39\",\"courseId\":\"3\",\"chapterId\":\"46\",\"number\":\"11\",\"seq\":\"25\",\"status\":\"published\",\"title\":\"MapReduce\\u8ba1\\u7b97\\u6846\\u67b6\\uff08\\u4e0a\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"102\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c4\\u7ae0_\\u7b2c3\\u8282_Map-Reduce\\u8ba1\\u7b97\\u6846\\u67b6\\uff08\\u4e0a\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045230\",\"isTest\":\"0\"}','127.0.0.1',1496045230,'info','','-'),(163,3,'course','add_lesson','添加课程内容《MapReduce计算框架（下）》(40)','{\"id\":\"40\",\"courseId\":\"3\",\"chapterId\":\"47\",\"number\":\"12\",\"seq\":\"26\",\"status\":\"published\",\"title\":\"MapReduce\\u8ba1\\u7b97\\u6846\\u67b6\\uff08\\u4e0b\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"103\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c4\\u7ae0_\\u7b2c3\\u8282_Map-Reduce\\u8ba1\\u7b97\\u6846\\u67b6(\\u4e0b\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045256\",\"isTest\":\"0\"}','127.0.0.1',1496045256,'info','','-'),(164,3,'course','add_lesson','添加课程内容《MapReduce实验（一）》(41)','{\"id\":\"41\",\"courseId\":\"3\",\"chapterId\":\"49\",\"number\":\"13\",\"seq\":\"30\",\"status\":\"published\",\"title\":\"MapReduce\\u5b9e\\u9a8c\\uff08\\u4e00\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"104\",\"mediaSource\":\"self\",\"mediaName\":\"Hadoop\\u5b9e\\u9a8c-MapReduce(1).pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045405\",\"isTest\":\"0\"}','127.0.0.1',1496045405,'info','','-'),(165,3,'course','add_lesson','添加课程内容《MapReduce实验（二）》(42)','{\"id\":\"42\",\"courseId\":\"3\",\"chapterId\":\"50\",\"number\":\"14\",\"seq\":\"31\",\"status\":\"published\",\"title\":\"MapReduce\\u5b9e\\u9a8c\\uff08\\u4e8c\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"105\",\"mediaSource\":\"self\",\"mediaName\":\"Hadoop\\u5b9e\\u9a8c-MapReduce(2).pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045435\",\"isTest\":\"0\"}','127.0.0.1',1496045435,'info','','-'),(166,3,'course','add_lesson','添加课程内容《HDFS操作视频案例》(43)','{\"id\":\"43\",\"courseId\":\"3\",\"chapterId\":\"51\",\"number\":\"15\",\"seq\":\"33\",\"status\":\"published\",\"title\":\"HDFS\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"106\",\"mediaSource\":\"self\",\"mediaName\":\"hdfs.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045515\",\"isTest\":\"0\"}','127.0.0.1',1496045515,'info','','-'),(167,3,'course','add_lesson','添加课程内容《MapReduce 程序开发指南》(44)','{\"id\":\"44\",\"courseId\":\"3\",\"chapterId\":\"48\",\"number\":\"16\",\"seq\":\"34\",\"status\":\"published\",\"title\":\"MapReduce \\u7a0b\\u5e8f\\u5f00\\u53d1\\u6307\\u5357\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"107\",\"mediaSource\":\"self\",\"mediaName\":\"\\u8bfe\\u7a0b4\\u5b9e\\u9a8c8MapReduce\\u7a0b\\u5e8f\\u5f00\\u53d1-\\u5b9e\\u9a8c\\u6307\\u5357.doc\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045565\",\"isTest\":\"0\"}','127.0.0.1',1496045566,'info','','-'),(168,3,'lesson','delete','删除课程《Hadoop基础框架》(#3)的课程内容 MapReduce 程序开发指南','','127.0.0.1',1496045572,'info','','-'),(169,3,'course','add_lesson','添加课程内容《Hadoop2.x集群维护》(45)','{\"id\":\"45\",\"courseId\":\"3\",\"chapterId\":\"52\",\"number\":\"16\",\"seq\":\"35\",\"status\":\"published\",\"title\":\"Hadoop2.x\\u96c6\\u7fa4\\u7ef4\\u62a4\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"108\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c4\\u7ae0_\\u7b2c4\\u8282_Hadoop2.x\\u96c6\\u7fa4\\u7ef4\\u62a4\\uff08\\u4e0a\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045671\",\"isTest\":\"0\"}','127.0.0.1',1496045671,'info','','-'),(170,3,'course','add_lesson','添加课程内容《Hadoop2.x框架基础（讲义）》(46)','{\"id\":\"46\",\"courseId\":\"3\",\"chapterId\":\"52\",\"number\":\"17\",\"seq\":\"36\",\"status\":\"published\",\"title\":\"Hadoop2.x\\u6846\\u67b6\\u57fa\\u7840\\uff08\\u8bb2\\u4e49\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"109\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c\\u56db\\u7ae0\\u8bb2\\u4e49- Hadoop2.x\\u6846\\u67b6\\u57fa\\u7840.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045720\",\"isTest\":\"0\"}','127.0.0.1',1496045720,'info','','-'),(171,3,'course','add_lesson','添加课程内容《HBase概述》(47)','{\"id\":\"47\",\"courseId\":\"4\",\"chapterId\":\"54\",\"number\":\"1\",\"seq\":\"3\",\"status\":\"published\",\"title\":\"HBase\\u6982\\u8ff0\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"110\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c9\\u7ae0\\u7b2c1\\u8282_HBase\\u6982\\u8ff0.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496045879\",\"isTest\":\"0\"}','127.0.0.1',1496045879,'info','','-'),(172,3,'course','add_lesson','添加课程内容《HBase体系架构》(48)','{\"id\":\"48\",\"courseId\":\"4\",\"chapterId\":\"60\",\"number\":\"2\",\"seq\":\"10\",\"status\":\"published\",\"title\":\"HBase\\u4f53\\u7cfb\\u67b6\\u6784\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"111\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c9\\u7ae0\\u7b2c2\\u8282_HBase\\u4f53\\u7cfb\\u67b6\\u6784.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496046080\",\"isTest\":\"0\"}','127.0.0.1',1496046080,'info','','-'),(173,3,'course','add_lesson','添加课程内容《HBase常用操作》(49)','{\"id\":\"49\",\"courseId\":\"4\",\"chapterId\":\"61\",\"number\":\"3\",\"seq\":\"12\",\"status\":\"published\",\"title\":\"HBase\\u5e38\\u7528\\u64cd\\u4f5c\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"112\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c9\\u7ae0\\u7b2c3\\u8282_HBase\\u5e38\\u7528\\u64cd\\u4f5c.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496046137\",\"isTest\":\"0\"}','127.0.0.1',1496046137,'info','','-'),(174,3,'course','add_lesson','添加课程内容《HBase常用操作》(50)','{\"id\":\"50\",\"courseId\":\"4\",\"chapterId\":\"62\",\"number\":\"4\",\"seq\":\"14\",\"status\":\"published\",\"title\":\"HBase\\u5e38\\u7528\\u64cd\\u4f5c\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"113\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c9\\u7ae0\\u7b2c3\\u8282_HBase\\u5e38\\u7528\\u64cd\\u4f5c.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496046182\",\"isTest\":\"0\"}','127.0.0.1',1496046182,'info','','-'),(175,3,'lesson','delete','删除课程《HBase基础和进阶》(#4)的课程内容 HBase常用操作','','127.0.0.1',1496046192,'info','','-'),(176,3,'course','add_lesson','添加课程内容《HBase架构详解》(51)','{\"id\":\"51\",\"courseId\":\"4\",\"chapterId\":\"62\",\"number\":\"4\",\"seq\":\"14\",\"status\":\"published\",\"title\":\"HBase\\u67b6\\u6784\\u8be6\\u89e3\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"114\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c10\\u7ae0\\u7b2c1\\u8282_HBase\\u67b6\\u6784\\u8be6\\u89e3.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496046212\",\"isTest\":\"0\"}','127.0.0.1',1496046212,'info','','-'),(177,3,'course','add_lesson','添加课程内容《HBase高级特性》(52)','{\"id\":\"52\",\"courseId\":\"4\",\"chapterId\":\"63\",\"number\":\"5\",\"seq\":\"16\",\"status\":\"published\",\"title\":\"HBase\\u9ad8\\u7ea7\\u7279\\u6027\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"115\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c10\\u7ae0\\u7b2c2\\u8282_HBase\\u9ad8\\u7ea7\\u7279\\u6027.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496046240\",\"isTest\":\"0\"}','127.0.0.1',1496046240,'info','','-'),(178,3,'course','add_lesson','添加课程内容《HBase表操作》(53)','{\"id\":\"53\",\"courseId\":\"4\",\"chapterId\":\"64\",\"number\":\"6\",\"seq\":\"18\",\"status\":\"published\",\"title\":\"HBase\\u8868\\u64cd\\u4f5c\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"116\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c10\\u7ae0\\u7b2c3\\u8282_HBase\\u8868\\u64cd\\u4f5c.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496046268\",\"isTest\":\"0\"}','127.0.0.1',1496046268,'info','','-'),(179,3,'course','add_lesson','添加课程内容《HBase安装和系列实验》(54)','{\"id\":\"54\",\"courseId\":\"4\",\"chapterId\":\"55\",\"number\":\"7\",\"seq\":\"19\",\"status\":\"published\",\"title\":\"HBase\\u5b89\\u88c5\\u548c\\u7cfb\\u5217\\u5b9e\\u9a8c\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"117\",\"mediaSource\":\"self\",\"mediaName\":\"HBase\\u7684\\u5b89\\u88c5\\u548c\\u7cfb\\u5217\\u5b9e\\u9a8c.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496046303\",\"isTest\":\"0\"}','127.0.0.1',1496046303,'info','','-'),(180,3,'user','login_success','登录成功','','127.0.0.1',1496061670,'info','','-'),(181,3,'course','delete','删除课程《1》(#1)','','127.0.0.1',1496061694,'info','','-'),(182,3,'course','add_lesson','添加课程内容《HBase环境搭建视频》(55)','{\"id\":\"55\",\"courseId\":\"4\",\"chapterId\":\"97\",\"number\":\"8\",\"seq\":\"21\",\"status\":\"published\",\"title\":\"HBase\\u73af\\u5883\\u642d\\u5efa\\u89c6\\u9891\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"118\",\"mediaSource\":\"self\",\"mediaName\":\"Hbase_1.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496061795\",\"isTest\":\"0\"}','127.0.0.1',1496061795,'info','','-'),(183,3,'course','add_lesson','添加课程内容《HBase基本操作视频案例》(56)','{\"id\":\"56\",\"courseId\":\"4\",\"chapterId\":\"98\",\"number\":\"9\",\"seq\":\"23\",\"status\":\"published\",\"title\":\"HBase\\u57fa\\u672c\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"119\",\"mediaSource\":\"self\",\"mediaName\":\"Hbase_2.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496061963\",\"isTest\":\"0\"}','127.0.0.1',1496061963,'info','','-'),(184,3,'course','add_lesson','添加课程内容《Hive概述》(57)','{\"id\":\"57\",\"courseId\":\"5\",\"chapterId\":\"99\",\"number\":\"1\",\"seq\":\"13\",\"status\":\"published\",\"title\":\"Hive\\u6982\\u8ff0\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"120\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c5\\u7ae0\\u7b2c1\\u8282-Hive\\u6982\\u8ff0.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062033\",\"isTest\":\"0\"}','127.0.0.1',1496062033,'info','','-'),(185,3,'course','add_lesson','添加课程内容《Hive的体系结构》(58)','{\"id\":\"58\",\"courseId\":\"5\",\"chapterId\":\"100\",\"number\":\"2\",\"seq\":\"15\",\"status\":\"published\",\"title\":\"Hive\\u7684\\u4f53\\u7cfb\\u7ed3\\u6784\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"121\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c5\\u7ae0\\u7b2c2\\u8282-Hive\\u7684\\u4f53\\u7cfb\\u7ed3\\u6784.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062073\",\"isTest\":\"0\"}','127.0.0.1',1496062073,'info','','-'),(186,3,'course','add_lesson','添加课程内容《Hive数据类型和文件格式》(59)','{\"id\":\"59\",\"courseId\":\"5\",\"chapterId\":\"101\",\"number\":\"3\",\"seq\":\"17\",\"status\":\"published\",\"title\":\"Hive\\u6570\\u636e\\u7c7b\\u578b\\u548c\\u6587\\u4ef6\\u683c\\u5f0f\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"122\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c5\\u7ae0\\u7b2c3\\u8282-Hive\\u6570\\u636e\\u7c7b\\u578b\\u548c\\u6587\\u4ef6\\u683c\\u5f0f.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062139\",\"isTest\":\"0\"}','127.0.0.1',1496062139,'info','','-'),(187,3,'course','add_lesson','添加课程内容《Hive常用操作方式》(60)','{\"id\":\"60\",\"courseId\":\"5\",\"chapterId\":\"102\",\"number\":\"4\",\"seq\":\"19\",\"status\":\"published\",\"title\":\"Hive\\u5e38\\u7528\\u64cd\\u4f5c\\u65b9\\u5f0f\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"123\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c5\\u7ae0\\u7b2c4\\u8282-Hive\\u5e38\\u7528\\u64cd\\u4f5c\\u65b9\\u5f0f.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062213\",\"isTest\":\"0\"}','127.0.0.1',1496062213,'info','','-'),(188,3,'course','add_lesson','添加课程内容《HiveQL数据定义》(61)','{\"id\":\"61\",\"courseId\":\"5\",\"chapterId\":\"103\",\"number\":\"5\",\"seq\":\"21\",\"status\":\"published\",\"title\":\"HiveQL\\u6570\\u636e\\u5b9a\\u4e49\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"124\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c5\\u7ae0\\u7b2c5\\u8282-HiveQL\\u6570\\u636e\\u5b9a\\u4e49.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062264\",\"isTest\":\"0\"}','127.0.0.1',1496062264,'info','','-'),(189,3,'course','add_lesson','添加课程内容《HiveQL查询》(62)','{\"id\":\"62\",\"courseId\":\"5\",\"chapterId\":\"104\",\"number\":\"6\",\"seq\":\"23\",\"status\":\"published\",\"title\":\"HiveQL\\u67e5\\u8be2\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"125\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c5\\u7ae0\\u7b2c6\\u8282-HiveQL\\u67e5\\u8be2.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062307\",\"isTest\":\"0\"}','127.0.0.1',1496062308,'info','','-'),(190,3,'course','add_lesson','添加课程内容《Hive环境搭建》(63)','{\"id\":\"63\",\"courseId\":\"5\",\"chapterId\":\"105\",\"number\":\"7\",\"seq\":\"25\",\"status\":\"published\",\"title\":\"Hive\\u73af\\u5883\\u642d\\u5efa\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"126\",\"mediaSource\":\"self\",\"mediaName\":\"hive_1.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062388\",\"isTest\":\"0\"}','127.0.0.1',1496062388,'info','','-'),(191,3,'course','add_lesson','添加课程内容《Hive Sql基本操作视频案例》(64)','{\"id\":\"64\",\"courseId\":\"5\",\"chapterId\":\"106\",\"number\":\"8\",\"seq\":\"27\",\"status\":\"published\",\"title\":\"Hive Sql\\u57fa\\u672c\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"127\",\"mediaSource\":\"self\",\"mediaName\":\"hive_2.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062429\",\"isTest\":\"0\"}','127.0.0.1',1496062429,'info','','-'),(192,3,'course','add_lesson','添加课程内容《Spark on Yarn》(65)','{\"id\":\"65\",\"courseId\":\"6\",\"chapterId\":\"107\",\"number\":\"1\",\"seq\":\"6\",\"status\":\"published\",\"title\":\"Spark on Yarn\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"128\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c12\\u7ae0_\\u7b2c1\\u8282_Spark on Yarn.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062926\",\"isTest\":\"0\"}','127.0.0.1',1496062926,'info','','-'),(193,3,'course','add_lesson','添加课程内容《Spark HistoryServer》(66)','{\"id\":\"66\",\"courseId\":\"6\",\"chapterId\":\"108\",\"number\":\"2\",\"seq\":\"8\",\"status\":\"published\",\"title\":\"Spark HistoryServer\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"129\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c12\\u7ae0_\\u7b2c2\\u8282_Spark HistoryServer.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496062982\",\"isTest\":\"0\"}','127.0.0.1',1496062982,'info','','-'),(194,3,'course','add_lesson','添加课程内容《Spark Streaming》(67)','{\"id\":\"67\",\"courseId\":\"6\",\"chapterId\":\"109\",\"number\":\"3\",\"seq\":\"10\",\"status\":\"published\",\"title\":\"Spark Streaming\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"130\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c12\\u7ae0_\\u7b2c3\\u8282_Spark Streaming.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063018\",\"isTest\":\"0\"}','127.0.0.1',1496063018,'info','','-'),(195,3,'course','add_lesson','添加课程内容《Spark 项目实战》(68)','{\"id\":\"68\",\"courseId\":\"6\",\"chapterId\":\"110\",\"number\":\"4\",\"seq\":\"12\",\"status\":\"published\",\"title\":\"Spark \\u9879\\u76ee\\u5b9e\\u6218\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"131\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c12\\u7ae0_\\u7b2c5\\u8282_Spark \\u9879\\u76ee\\u5b9e\\u6218.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063088\",\"isTest\":\"0\"}','127.0.0.1',1496063088,'info','','-'),(196,3,'course','add_lesson','添加课程内容《Spark高阶应用&实战（讲义）》(69)','{\"id\":\"69\",\"courseId\":\"6\",\"chapterId\":\"79\",\"number\":\"5\",\"seq\":\"13\",\"status\":\"published\",\"title\":\"Spark\\u9ad8\\u9636\\u5e94\\u7528&\\u5b9e\\u6218\\uff08\\u8bb2\\u4e49\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"132\",\"mediaSource\":\"self\",\"mediaName\":\"\\u8bb2\\u4e49-\\u7b2c\\u5341\\u4e8c\\u7ae0 Spark\\u9ad8\\u9636\\u5e94\\u7528&\\u5b9e\\u6218.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063151\",\"isTest\":\"0\"}','127.0.0.1',1496063151,'info','','-'),(197,3,'course','add_lesson','添加课程内容《大数据协作框架_Sqoop （上）》(70)','{\"id\":\"70\",\"courseId\":\"7\",\"chapterId\":\"111\",\"number\":\"1\",\"seq\":\"6\",\"status\":\"published\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Sqoop \\uff08\\u4e0a\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"133\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c7\\u7ae0_\\u7b2c1\\u8282\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Sqoop\\uff08\\u4e0a\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063226\",\"isTest\":\"0\"}','127.0.0.1',1496063226,'info','','-'),(198,3,'course','add_lesson','添加课程内容《大数据协作框架_Sqoop （下）》(71)','{\"id\":\"71\",\"courseId\":\"7\",\"chapterId\":\"112\",\"number\":\"2\",\"seq\":\"8\",\"status\":\"published\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Sqoop \\uff08\\u4e0b\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"134\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c7\\u7ae0_\\u7b2c1\\u8282\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Sqoop\\uff08\\u4e0b\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063266\",\"isTest\":\"0\"}','127.0.0.1',1496063266,'info','','-'),(199,3,'course','add_lesson','添加课程内容《大数据协作框架_Flume》(72)','{\"id\":\"72\",\"courseId\":\"7\",\"chapterId\":\"113\",\"number\":\"3\",\"seq\":\"10\",\"status\":\"published\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Flume\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"135\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c7\\u7ae0_\\u7b2c2\\u8282\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Flume.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063317\",\"isTest\":\"0\"}','127.0.0.1',1496063317,'info','','-'),(200,3,'course','add_lesson','添加课程内容《大数据协作框架_Kafka》(73)','{\"id\":\"73\",\"courseId\":\"7\",\"chapterId\":\"114\",\"number\":\"4\",\"seq\":\"12\",\"status\":\"published\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Kafka\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"136\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c7\\u7ae0_\\u7b2c3\\u8282\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Kafka.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063362\",\"isTest\":\"0\"}','127.0.0.1',1496063362,'info','','-'),(201,3,'course','add_lesson','添加课程内容《大数据协作框架_Phoenix》(74)','{\"id\":\"74\",\"courseId\":\"7\",\"chapterId\":\"115\",\"number\":\"5\",\"seq\":\"14\",\"status\":\"published\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Phoenix\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"137\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c7\\u7ae0_\\u7b2c4\\u8282\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Phoenix.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063407\",\"isTest\":\"0\"}','127.0.0.1',1496063407,'info','','-'),(202,3,'course','add_lesson','添加课程内容《大数据协作框架（讲义）》(75)','{\"id\":\"75\",\"courseId\":\"7\",\"chapterId\":\"83\",\"number\":\"6\",\"seq\":\"15\",\"status\":\"published\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6\\uff08\\u8bb2\\u4e49\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"138\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c\\u4e03\\u7ae0 \\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6\\uff08\\u8bb2\\u4e49\\uff09.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063440\",\"isTest\":\"0\"}','127.0.0.1',1496063440,'info','','-'),(203,3,'course','add_lesson','添加课程内容《实时流式计算概述》(76)','{\"id\":\"76\",\"courseId\":\"8\",\"chapterId\":\"116\",\"number\":\"1\",\"seq\":\"8\",\"status\":\"published\",\"title\":\"\\u5b9e\\u65f6\\u6d41\\u5f0f\\u8ba1\\u7b97\\u6982\\u8ff0\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"139\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c8\\u7ae0_\\u7b2c1\\u8282_\\u5b9e\\u65f6\\u6d41\\u5f0f\\u8ba1\\u7b97\\u6982\\u8ff0.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063737\",\"isTest\":\"0\"}','127.0.0.1',1496063737,'info','','-'),(204,3,'course','add_lesson','添加课程内容《实时流式计算的应用场景及关键技术》(77)','{\"id\":\"77\",\"courseId\":\"8\",\"chapterId\":\"117\",\"number\":\"2\",\"seq\":\"10\",\"status\":\"published\",\"title\":\"\\u5b9e\\u65f6\\u6d41\\u5f0f\\u8ba1\\u7b97\\u7684\\u5e94\\u7528\\u573a\\u666f\\u53ca\\u5173\\u952e\\u6280\\u672f\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"140\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c8\\u7ae0_\\u7b2c2\\u8282_\\u5b9e\\u65f6\\u6d41\\u5f0f\\u8ba1\\u7b97\\u7684\\u5e94\\u7528\\u573a\\u666f\\u53ca\\u5173\\u952e\\u6280\\u672f.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063796\",\"isTest\":\"0\"}','127.0.0.1',1496063796,'info','','-'),(205,3,'course','add_lesson','添加课程内容《Storm概述》(78)','{\"id\":\"78\",\"courseId\":\"8\",\"chapterId\":\"118\",\"number\":\"3\",\"seq\":\"12\",\"status\":\"published\",\"title\":\"Storm\\u6982\\u8ff0\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"141\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c8\\u7ae0_\\u7b2c3\\u8282_\\u6982\\u8ff0Storm.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063825\",\"isTest\":\"0\"}','127.0.0.1',1496063825,'info','','-'),(206,3,'course','add_lesson','添加课程内容《Storm的架构与原理》(79)','{\"id\":\"79\",\"courseId\":\"8\",\"chapterId\":\"119\",\"number\":\"4\",\"seq\":\"14\",\"status\":\"published\",\"title\":\"Storm\\u7684\\u67b6\\u6784\\u4e0e\\u539f\\u7406\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"142\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c8\\u7ae0_\\u7b2c4\\u8282_Storm\\u67b6\\u6784\\u548c\\u539f\\u7406.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063864\",\"isTest\":\"0\"}','127.0.0.1',1496063864,'info','','-'),(207,3,'course','add_lesson','添加课程内容《Storm集群搭建部署》(80)','{\"id\":\"80\",\"courseId\":\"8\",\"chapterId\":\"120\",\"number\":\"5\",\"seq\":\"16\",\"status\":\"published\",\"title\":\"Storm\\u96c6\\u7fa4\\u642d\\u5efa\\u90e8\\u7f72\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"143\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c8\\u7ae0_\\u7b2c5\\u8282_Storm\\u96c6\\u7fa4\\u642d\\u5efa\\u90e8\\u7f72.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063901\",\"isTest\":\"0\"}','127.0.0.1',1496063901,'info','','-'),(208,3,'course','add_lesson','添加课程内容《Storm常见问题与解决方法》(81)','{\"id\":\"81\",\"courseId\":\"8\",\"chapterId\":\"121\",\"number\":\"6\",\"seq\":\"18\",\"status\":\"published\",\"title\":\"Storm\\u5e38\\u89c1\\u95ee\\u9898\\u4e0e\\u89e3\\u51b3\\u65b9\\u6cd5\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"144\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c8\\u7ae0_\\u7b2c6\\u8282_Storm\\u5e38\\u89c1\\u95ee\\u9898\\u4e0e\\u89e3\\u51b3\\u65b9\\u6cd5.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496063948\",\"isTest\":\"0\"}','127.0.0.1',1496063948,'info','','-'),(209,3,'course','add_lesson','添加课程内容《流式大数据处理（讲义）》(82)','{\"id\":\"82\",\"courseId\":\"8\",\"chapterId\":\"122\",\"number\":\"7\",\"seq\":\"20\",\"status\":\"published\",\"title\":\"\\u6d41\\u5f0f\\u5927\\u6570\\u636e\\u5904\\u7406\\uff08\\u8bb2\\u4e49\\uff09\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"145\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c\\u516b\\u7ae0 \\u8bb2\\u4e49-\\u6d41\\u5f0f\\u5927\\u6570\\u636e\\u5904\\u7406.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064015\",\"isTest\":\"0\"}','127.0.0.1',1496064015,'info','','-'),(210,3,'course','add_lesson','添加课程内容《Spark以及Spark生态圈的介绍》(83)','{\"id\":\"83\",\"courseId\":\"9\",\"chapterId\":\"123\",\"number\":\"1\",\"seq\":\"9\",\"status\":\"published\",\"title\":\"Spark\\u4ee5\\u53caSpark\\u751f\\u6001\\u5708\\u7684\\u4ecb\\u7ecd\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"146\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c11\\u7ae0_\\u7b2c1\\u8282_Spark\\u4ee5\\u53caSpark\\u751f\\u6001\\u5708\\u7684\\u4ecb\\u7ecd.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064061\",\"isTest\":\"0\"}','127.0.0.1',1496064061,'info','','-'),(211,3,'course','add_lesson','添加课程内容《Spark集群搭建》(84)','{\"id\":\"84\",\"courseId\":\"9\",\"chapterId\":\"124\",\"number\":\"2\",\"seq\":\"11\",\"status\":\"published\",\"title\":\"Spark\\u96c6\\u7fa4\\u642d\\u5efa\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"147\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c11\\u7ae0_\\u7b2c2\\u8282_Spark\\u96c6\\u7fa4\\u642d\\u5efa.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064106\",\"isTest\":\"0\"}','127.0.0.1',1496064106,'info','','-'),(212,3,'course','add_lesson','添加课程内容《Spark应用提交工具》(85)','{\"id\":\"85\",\"courseId\":\"9\",\"chapterId\":\"126\",\"number\":\"3\",\"seq\":\"13\",\"status\":\"published\",\"title\":\"Spark\\u5e94\\u7528\\u63d0\\u4ea4\\u5de5\\u5177\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"148\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c11\\u7ae0_\\u7b2c3\\u8282_Spark\\u5e94\\u7528\\u63d0\\u4ea4\\u5de5\\u5177.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064187\",\"isTest\":\"0\"}','127.0.0.1',1496064187,'info','','-'),(213,3,'course','add_lesson','添加课程内容《Scala基本知识讲解》(86)','{\"id\":\"86\",\"courseId\":\"9\",\"chapterId\":\"127\",\"number\":\"4\",\"seq\":\"15\",\"status\":\"published\",\"title\":\"Scala\\u57fa\\u672c\\u77e5\\u8bc6\\u8bb2\\u89e3\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"149\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c11\\u7ae0_\\u7b2c4\\u8282_Scala\\u57fa\\u672c\\u77e5\\u8bc6\\u8bb2\\u89e3.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064243\",\"isTest\":\"0\"}','127.0.0.1',1496064243,'info','','-'),(214,3,'course','add_lesson','添加课程内容《Scala基本操作视频案例》(87)','{\"id\":\"87\",\"courseId\":\"9\",\"chapterId\":\"128\",\"number\":\"5\",\"seq\":\"17\",\"status\":\"published\",\"title\":\"Scala\\u57fa\\u672c\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"150\",\"mediaSource\":\"self\",\"mediaName\":\"spark.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064301\",\"isTest\":\"0\"}','127.0.0.1',1496064301,'info','','-'),(215,3,'course','add_lesson','添加课程内容《RDD特性、常见操作、缓存策略》(88)','{\"id\":\"88\",\"courseId\":\"9\",\"chapterId\":\"129\",\"number\":\"6\",\"seq\":\"19\",\"status\":\"published\",\"title\":\"RDD\\u7279\\u6027\\u3001\\u5e38\\u89c1\\u64cd\\u4f5c\\u3001\\u7f13\\u5b58\\u7b56\\u7565\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"151\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c11\\u7ae0_\\u7b2c5\\u8282_RDD\\u7279\\u6027\\u3001\\u5e38\\u89c1\\u64cd\\u4f5c\\u3001\\u7f13\\u5b58\\u7b56\\u7565.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064367\",\"isTest\":\"0\"}','127.0.0.1',1496064367,'info','','-'),(216,3,'course','add_lesson','添加课程内容《RDD Dependency、Stage》(89)','{\"id\":\"89\",\"courseId\":\"9\",\"chapterId\":\"130\",\"number\":\"7\",\"seq\":\"21\",\"status\":\"published\",\"title\":\"RDD Dependency\\u3001Stage\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"152\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c11\\u7ae0_\\u7b2c6\\u8282_RDD Dependency\\u3001Stage .pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064417\",\"isTest\":\"0\"}','127.0.0.1',1496064417,'info','','-'),(217,3,'course','add_lesson','添加课程内容《Spark核心组件概述及案例分析》(90)','{\"id\":\"90\",\"courseId\":\"9\",\"chapterId\":\"131\",\"number\":\"8\",\"seq\":\"23\",\"status\":\"published\",\"title\":\"Spark\\u6838\\u5fc3\\u7ec4\\u4ef6\\u6982\\u8ff0\\u53ca\\u6848\\u4f8b\\u5206\\u6790\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"153\",\"mediaSource\":\"self\",\"mediaName\":\"\\u7b2c11\\u7ae0_\\u7b2c7\\u8282_Spark\\u6838\\u5fc3\\u7ec4\\u4ef6\\u6982\\u8ff0\\u53ca\\u6848\\u4f8b\\u5206\\u6790.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064455\",\"isTest\":\"0\"}','127.0.0.1',1496064455,'info','','-'),(218,3,'course','add_lesson','添加课程内容《Spark程序开发实验指南》(91)','{\"id\":\"91\",\"courseId\":\"9\",\"chapterId\":\"132\",\"number\":\"9\",\"seq\":\"25\",\"status\":\"published\",\"title\":\"Spark\\u7a0b\\u5e8f\\u5f00\\u53d1\\u5b9e\\u9a8c\\u6307\\u5357\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"154\",\"mediaSource\":\"self\",\"mediaName\":\"\\u8bfe\\u7a0b3\\u5b9e\\u9a8c11SPARK\\u7a0b\\u5e8f\\u5f00\\u53d1-\\u5b9e\\u9a8c\\u6307\\u5357.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496064511\",\"isTest\":\"0\"}','127.0.0.1',1496064511,'info','','-'),(219,3,'user','login_success','登录成功','','127.0.0.1',1496138542,'info','','-'),(220,3,'course','add_lesson','添加课程内容《Hadoop概述视频讲解案例》(92)','{\"id\":\"92\",\"courseId\":\"3\",\"chapterId\":\"34\",\"number\":\"18\",\"seq\":\"37\",\"status\":\"published\",\"title\":\"Hadoop\\u6982\\u8ff0\\u89c6\\u9891\\u8bb2\\u89e3\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"155\",\"mediaSource\":\"self\",\"mediaName\":\"1.Hadoop\\u6982\\u8ff0.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496138691\",\"isTest\":\"0\"}','127.0.0.1',1496138691,'info','','-'),(221,3,'course','add_lesson','添加课程内容《HBase概述视频讲解案例》(93)','{\"id\":\"93\",\"courseId\":\"4\",\"chapterId\":\"53\",\"number\":\"10\",\"seq\":\"24\",\"status\":\"published\",\"title\":\"HBase\\u6982\\u8ff0\\u89c6\\u9891\\u8bb2\\u89e3\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"156\",\"mediaSource\":\"self\",\"mediaName\":\"2.HBase\\u6982\\u8ff0.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496138764\",\"isTest\":\"0\"}','127.0.0.1',1496138764,'info','','-'),(222,3,'course','add_lesson','添加课程内容《HBase术语及数据模型》(94)','{\"id\":\"94\",\"courseId\":\"4\",\"chapterId\":\"53\",\"number\":\"11\",\"seq\":\"25\",\"status\":\"published\",\"title\":\"HBase\\u672f\\u8bed\\u53ca\\u6570\\u636e\\u6a21\\u578b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"157\",\"mediaSource\":\"self\",\"mediaName\":\"2.HBase\\u672f\\u8bed\\u53ca\\u6570\\u636e\\u6a21\\u578b.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496138840\",\"isTest\":\"0\"}','127.0.0.1',1496138840,'info','','-'),(223,3,'course','add_lesson','添加课程内容《Hive概述视屏案例》(95)','{\"id\":\"95\",\"courseId\":\"5\",\"chapterId\":\"65\",\"number\":\"9\",\"seq\":\"28\",\"status\":\"published\",\"title\":\"Hive\\u6982\\u8ff0\\u89c6\\u5c4f\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"158\",\"mediaSource\":\"self\",\"mediaName\":\"3.Hive\\u7b80\\u4ecb.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496138869\",\"isTest\":\"0\"}','127.0.0.1',1496138869,'info','','-'),(224,3,'course','add_lesson','添加课程内容《Hive与HBase的关系》(96)','{\"id\":\"96\",\"courseId\":\"5\",\"chapterId\":\"66\",\"number\":\"10\",\"seq\":\"29\",\"status\":\"published\",\"title\":\"Hive\\u4e0eHBase\\u7684\\u5173\\u7cfb\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"159\",\"mediaSource\":\"self\",\"mediaName\":\"3.Hive\\u4e0eHBase\\u5173\\u7cfb.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496138893\",\"isTest\":\"0\"}','127.0.0.1',1496138893,'info','','-'),(225,3,'course','add_lesson','添加课程内容《Spark简介视频案例》(97)','{\"id\":\"97\",\"courseId\":\"9\",\"chapterId\":\"90\",\"number\":\"10\",\"seq\":\"26\",\"status\":\"published\",\"title\":\"Spark\\u7b80\\u4ecb\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"160\",\"mediaSource\":\"self\",\"mediaName\":\"4.Spark\\u7b80\\u4ecb.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496138928\",\"isTest\":\"0\"}','127.0.0.1',1496138928,'info','','-'),(226,3,'course','add_lesson','添加课程内容《Spark计算模型》(98)','{\"id\":\"98\",\"courseId\":\"9\",\"chapterId\":\"90\",\"number\":\"11\",\"seq\":\"27\",\"status\":\"published\",\"title\":\"Spark\\u8ba1\\u7b97\\u6a21\\u578b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"161\",\"mediaSource\":\"self\",\"mediaName\":\"4.Spark\\u8ba1\\u7b97\\u6a21\\u578b.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496138942\",\"isTest\":\"0\"}','127.0.0.1',1496138942,'info','','-'),(227,3,'user','login_success','登录成功','','127.0.0.1',1496140137,'info','','-'),(228,3,'user','login_success','登录成功','','127.0.0.1',1496140184,'info','','-'),(229,3,'course','create','管理员观看课程内容《Spark简介视频案例》','','127.0.0.1',1496140236,'info','','-'),(230,3,'course','create','管理员观看课程内容《Spark计算模型》','','127.0.0.1',1496140244,'info','','-'),(231,3,'course','create','管理员观看课程内容《Scala基本操作视频案例》','','127.0.0.1',1496140266,'info','','-'),(232,3,'user','login_success','登录成功','','127.0.0.1',1496140834,'info','','-'),(233,3,'user','login_success','登录成功','','127.0.0.1',1496208198,'info','','-'),(234,3,'course','update_picture','更新课程《内存计算框架技术&Spark》(#9)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/134653d2e3a1657859.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/134653d2ce59165954.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/134653d2868b565958.jpg\"}','127.0.0.1',1496209613,'info','','-'),(235,3,'user','login_success','登录成功','','127.0.0.1',1496228772,'info','','-'),(236,3,'course','update_picture','更新课程《Hadoop基础框架》(#3)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8db49724458.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c89e05259390.jpg\"}','127.0.0.1',1496228812,'info','','-'),(237,3,'course','update_picture','更新课程《HBase基础和进阶》(#4)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191320058926680325.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/1913200575c5118047.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191320052ee7945844.jpg\"}','127.0.0.1',1496229200,'info','','-'),(238,3,'course','update_picture','更新课程《Hive基础和进阶》(#5)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191348cc1bf7692243.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191348cc0ae7248110.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191348cbd10e122767.jpg\"}','127.0.0.1',1496229228,'info','','-'),(239,3,'course','update_picture','更新课程《HBase基础和进阶》(#4)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/19151642168c007266.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191516420656422951.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/19151641cdfc728985.jpg\"}','127.0.0.1',1496229316,'info','','-'),(240,3,'course','update_picture','更新课程《Hive基础和进阶》(#5)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/19170407ee7d660688.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/19170407e139966086.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/19170407b753438026.jpg\"}','127.0.0.1',1496229424,'info','','-'),(241,3,'course','update_picture','更新课程《Hive基础和进阶》(#5)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191732c7d1f6881432.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191732c7c5ed616441.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191732c79f1d037256.jpg\"}','127.0.0.1',1496229452,'info','','-'),(242,3,'course','update_picture','更新课程《Spark高级应用和实战》(#6)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/19181026254c553196.png\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/191810261506726582.png\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/19181025c82a939960.png\"}','127.0.0.1',1496229490,'info','','-'),(243,3,'course','update_picture','更新课程《流式数据处理》(#8)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/192428c90277195099.png\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/192428c8eedf857236.png\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/192428c89db5260112.png\"}','127.0.0.1',1496229868,'info','','-'),(244,3,'course','update_picture','更新课程《大数据协同框架》(#7)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/192858a6cbdf085513.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/192858a6bb9b361489.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/192858a674bb290907.jpg\"}','127.0.0.1',1496230138,'info','','-'),(245,3,'course','update_picture','更新课程《内存计算框架技术&Spark》(#9)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/05-31\\/1930560680dd767316.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056063405250728.jpg\"}','127.0.0.1',1496230256,'info','','-'),(246,3,'user','login_success','登录成功','','127.0.0.1',1496238241,'info','','-'),(247,3,'course','publish','发布课程《内存计算框架技术&Spark》(#9)','','127.0.0.1',1496238266,'info','','-'),(248,3,'course','publish','发布课程《流式数据处理》(#8)','','127.0.0.1',1496238274,'info','','-'),(249,3,'course','publish','发布课程《大数据协同框架》(#7)','','127.0.0.1',1496238282,'info','','-'),(250,3,'course','publish','发布课程《Spark高级应用和实战》(#6)','','127.0.0.1',1496238293,'info','','-'),(251,3,'course','publish','发布课程《Hive基础和进阶》(#5)','','127.0.0.1',1496238302,'info','','-'),(252,3,'course','publish','发布课程《HBase基础和进阶》(#4)','','127.0.0.1',1496238311,'info','','-'),(253,3,'course','publish','发布课程《Hadoop基础框架》(#3)','','127.0.0.1',1496238320,'info','','-'),(254,3,'course','close','关闭课程《大数据基础课程》(#2)','','127.0.0.1',1496238339,'info','','-'),(255,3,'user','login_success','登录成功','','127.0.0.1',1496238475,'info','','-'),(256,3,'system','update_settings','更新站点设置','{\"name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"slogan\":\"\\u745e\\u5fb7\\u4e91\\u7f51\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"url\":\"http:\\/\\/tlc.com\",\"school_name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\",\"school_english_name\":\"red cloud\",\"logo\":\"\",\"themeCfg\":{\"THEME_ICON_COLOR\":\"33ffdd\",\"THEME_FRONT_COLOR\":\"#ea4335\",\"THEME_BACK_COLOR\":\"#ea4335\",\"THEME_NAV_BACK_COLOR\":\"#ea4335\"},\"favicon\":\"files\\/system\\/favicon_1469184392.ico\",\"seo_keywords\":\"\",\"seo_description\":\"\",\"master_email\":\"326728721@qq.com\",\"copyright\":\"\",\"friend_link_enable\":\"0\",\"friend_link\":[],\"about_us_enable\":\"0\",\"about_us_title\":\"\",\"icp\":\"\\u5317\\u4eac\\u745e\\u5fb7\\u4e91\\u7f51\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8\\uff08c\\uff092016\",\"contact\":\"\",\"analytics\":\"\",\"status\":\"open\",\"closed_note\":\"\",\"_csrf_token\":\"c13a4e0d743cbb15d34869e6a60f63f22b075731\"}','127.0.0.1',1496238557,'info','','-'),(257,3,'user','login_success','登录成功','','127.0.0.1',1496238569,'info','','-'),(258,3,'system','update_settings','更新站点设置','{\"name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"slogan\":\"\\u745e\\u5fb7\\u4e91\\u7f51\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"url\":\"http:\\/\\/tlc.com\",\"school_name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\",\"school_english_name\":\"red cloud\",\"logo\":\"\",\"themeCfg\":{\"THEME_ICON_COLOR\":\"33ffdd\",\"THEME_FRONT_COLOR\":\"#ea4335\",\"THEME_BACK_COLOR\":\"#ea4335\",\"THEME_NAV_BACK_COLOR\":\"#ea4335\"},\"favicon\":\"files\\/system\\/favicon_1469184392.ico\",\"seo_keywords\":\"\",\"seo_description\":\"\",\"master_email\":\"326728721@qq.com\",\"copyright\":\"\",\"friend_link_enable\":\"0\",\"friend_link\":[],\"about_us_enable\":\"0\",\"about_us_title\":\"\",\"icp\":\"\\u5317\\u4eac\\u745e\\u5fb7\\u4e91\\u7f51\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8\\uff08c\\uff092017 \\u4eacICP\\u590717016632\",\"contact\":\"\",\"analytics\":\"\",\"status\":\"open\",\"closed_note\":\"\",\"_csrf_token\":\"c13a4e0d743cbb15d34869e6a60f63f22b075731\"}','127.0.0.1',1496238643,'info','','-'),(259,3,'user','login_success','登录成功','','127.0.0.1',1496238665,'info','','-'),(260,3,'system','update_settings','更新站点设置','{\"name\":\"\\u745e\\u5fb7\\u53e3\\u888b\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"slogan\":\"\\u745e\\u5fb7\\u53e3\\u888b\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u5e73\\u53f0\",\"url\":\"http:\\/\\/tlc.com\",\"school_name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\",\"school_english_name\":\"red cloud\",\"logo\":\"\",\"themeCfg\":{\"THEME_ICON_COLOR\":\"33ffdd\",\"THEME_FRONT_COLOR\":\"#ea4335\",\"THEME_BACK_COLOR\":\"#ea4335\",\"THEME_NAV_BACK_COLOR\":\"#ea4335\"},\"favicon\":\"files\\/system\\/favicon_1469184392.ico\",\"seo_keywords\":\"\",\"seo_description\":\"\",\"master_email\":\"326728721@qq.com\",\"copyright\":\"\",\"friend_link_enable\":\"0\",\"friend_link\":[],\"about_us_enable\":\"0\",\"about_us_title\":\"\",\"icp\":\"\\u5317\\u4eac\\u745e\\u5fb7\\u4e91\\u7f51\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8\\uff08c\\uff092017 \\u4eacICP\\u590717016632\",\"contact\":\"\",\"analytics\":\"\",\"status\":\"open\",\"closed_note\":\"\",\"_csrf_token\":\"c13a4e0d743cbb15d34869e6a60f63f22b075731\"}','127.0.0.1',1496239188,'info','','-'),(261,3,'system','update_settings','更新站点设置','{\"name\":\"\\u745e\\u5fb7\\u53e3\\u888b\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u7cfb\\u7edf\",\"slogan\":\"\\u745e\\u5fb7\\u53e3\\u888b\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u7cfb\\u7edf\",\"url\":\"http:\\/\\/tlc.com\",\"school_name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\",\"school_english_name\":\"red cloud\",\"logo\":\"\",\"themeCfg\":{\"THEME_ICON_COLOR\":\"33ffdd\",\"THEME_FRONT_COLOR\":\"#ea4335\",\"THEME_BACK_COLOR\":\"#ea4335\",\"THEME_NAV_BACK_COLOR\":\"#ea4335\"},\"favicon\":\"files\\/system\\/favicon_1469184392.ico\",\"seo_keywords\":\"\",\"seo_description\":\"\",\"master_email\":\"326728721@qq.com\",\"copyright\":\"\",\"friend_link_enable\":\"0\",\"friend_link\":[],\"about_us_enable\":\"0\",\"about_us_title\":\"\",\"icp\":\"\\u5317\\u4eac\\u745e\\u5fb7\\u4e91\\u7f51\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8\\uff08c\\uff092017 \\u4eacICP\\u590717016632\",\"contact\":\"\",\"analytics\":\"\",\"status\":\"open\",\"closed_note\":\"\",\"_csrf_token\":\"c13a4e0d743cbb15d34869e6a60f63f22b075731\"}','127.0.0.1',1496239219,'info','','-'),(262,1,'user','login_success','登录成功','','127.0.0.1',1496239837,'info','','-'),(263,3,'user','login_success','登录成功','','127.0.0.1',1496240048,'info','','-'),(264,1,'user','login_success','登录成功','','127.0.0.1',1496240244,'info','','-'),(265,3,'user','login_success','登录成功','','127.0.0.1',1496240367,'info','','-'),(266,1,'user','login_success','登录成功','','127.0.0.1',1496240775,'info','','-'),(267,3,'user','login_success','登录成功','','127.0.0.1',1496240850,'info','','-'),(268,1,'user','login_success','登录成功','','127.0.0.1',1496241023,'info','','-'),(269,3,'user','login_success','登录成功','','127.0.0.1',1496241032,'info','','-'),(270,1,'user','login_success','登录成功','','127.0.0.1',1496241086,'info','','-'),(271,3,'user','login_success','登录成功','','127.0.0.1',1496241104,'info','','-'),(272,3,'user','login_success','登录成功','','127.0.0.1',1496242161,'info','','-'),(273,3,'user','login_success','登录成功','','127.0.0.1',1496242905,'info','','-'),(274,3,'user','login_success','登录成功','','127.0.0.1',1496243167,'info','','-'),(275,3,'user','login_success','登录成功','','127.0.0.1',1496243470,'info','','-'),(276,1,'user','login_success','登录成功','','127.0.0.1',1496244633,'info','','-'),(277,3,'user','login_success','登录成功','','127.0.0.1',1496244787,'info','','-'),(278,1,'user','login_success','登录成功','','127.0.0.1',1496244890,'info','','-'),(279,1,'user','login_success','登录成功','','127.0.0.1',1496245000,'info','','-'),(280,1,'course','create','学生观看课程内容《Spark计算模型》','','127.0.0.1',1496245599,'info','','-'),(281,1,'course','create','学生观看课程内容《Spark简介视频案例》','','127.0.0.1',1496245603,'info','','-'),(282,1,'course','create','学生观看课程内容《Scala基本操作视频案例》','','127.0.0.1',1496245657,'info','','-'),(283,1,'course','create','学生观看课程内容《Scala基本操作视频案例》','','127.0.0.1',1496245738,'info','','-'),(284,1,'course','create','学生观看课程内容《hadoop_1》','','127.0.0.1',1496245895,'info','','-'),(285,1,'course','create','学生观看课程内容《hadoop_2》','','127.0.0.1',1496245919,'info','','-'),(286,1,'course','create','学生观看课程内容《hadoop_3》','','127.0.0.1',1496245932,'info','','-'),(287,3,'user','login_success','登录成功','','127.0.0.1',1496246057,'info','','-'),(288,3,'lesson','delete','删除课程《Hadoop基础框架》(#3)的课程内容 hadoop_1','','127.0.0.1',1496246069,'info','','-'),(289,3,'lesson','delete','删除课程《Hadoop基础框架》(#3)的课程内容 hadoop_2','','127.0.0.1',1496246073,'info','','-'),(290,3,'lesson','delete','删除课程《Hadoop基础框架》(#3)的课程内容 hadoop_3','','127.0.0.1',1496246076,'info','','-'),(291,3,'course','add_lesson','添加课程内容《Hadoop部署一》(99)','{\"id\":\"99\",\"courseId\":\"3\",\"chapterId\":\"39\",\"number\":\"16\",\"seq\":\"38\",\"status\":\"unpublished\",\"title\":\"Hadoop\\u90e8\\u7f72\\u4e00\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"162\",\"mediaSource\":\"self\",\"mediaName\":\"hadoop_1.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496246101\",\"isTest\":\"0\"}','127.0.0.1',1496246101,'info','','-'),(292,3,'course','create','管理员观看课程内容《Hadoop部署一》','','127.0.0.1',1496246105,'info','','-'),(293,3,'course','add_lesson','添加课程内容《Hadoop部署二》(100)','{\"id\":\"100\",\"courseId\":\"3\",\"chapterId\":\"39\",\"number\":\"16\",\"seq\":\"39\",\"status\":\"unpublished\",\"title\":\"Hadoop\\u90e8\\u7f72\\u4e8c\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"163\",\"mediaSource\":\"self\",\"mediaName\":\"hadoop_2.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496246152\",\"isTest\":\"0\"}','127.0.0.1',1496246152,'info','','-'),(294,3,'course','add_lesson','添加课程内容《Hadoop部署三》(101)','{\"id\":\"101\",\"courseId\":\"3\",\"chapterId\":\"39\",\"number\":\"16\",\"seq\":\"40\",\"status\":\"unpublished\",\"title\":\"Hadoop\\u90e8\\u7f72\\u4e09\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"164\",\"mediaSource\":\"self\",\"mediaName\":\"hadoop_3.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496246167\",\"isTest\":\"0\"}','127.0.0.1',1496246167,'info','','-'),(295,3,'course','create','管理员观看课程内容《Hadoop部署一》','','127.0.0.1',1496246170,'info','','-'),(296,3,'course','create','管理员观看课程内容《Hadoop部署二》','','127.0.0.1',1496246178,'info','','-'),(297,3,'course','create','管理员观看课程内容《Hadoop部署三》','','127.0.0.1',1496246212,'info','','-'),(298,3,'course','create','管理员观看课程内容《HDFS操作视频案例》','','127.0.0.1',1496246252,'info','','-'),(299,3,'lesson','delete','删除课程《Hadoop基础框架》(#3)的课程内容 HDFS操作视频案例','','127.0.0.1',1496246262,'info','','-'),(300,3,'course','add_lesson','添加课程内容《HDFS操作视屏案例》(102)','{\"id\":\"102\",\"courseId\":\"3\",\"chapterId\":\"51\",\"number\":\"17\",\"seq\":\"41\",\"status\":\"unpublished\",\"title\":\"HDFS\\u64cd\\u4f5c\\u89c6\\u5c4f\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"165\",\"mediaSource\":\"self\",\"mediaName\":\"hdfs.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496246287\",\"isTest\":\"0\"}','127.0.0.1',1496246287,'info','','-'),(301,3,'course','create','管理员观看课程内容《HDFS操作视屏案例》','','127.0.0.1',1496246292,'info','','-'),(302,3,'course','create','管理员观看课程内容《HBase概述视频讲解案例》','','127.0.0.1',1496246309,'info','','-'),(303,3,'course','create','管理员观看课程内容《HBase术语及数据模型》','','127.0.0.1',1496246312,'info','','-'),(304,3,'course','create','管理员观看课程内容《HBase基本操作视频案例》','','127.0.0.1',1496246316,'info','','-'),(305,3,'lesson','delete','删除课程《HBase基础和进阶》(#4)的课程内容 HBase基本操作视频案例','','127.0.0.1',1496246327,'info','','-'),(306,3,'course','create','管理员观看课程内容《HBase环境搭建视频》','','127.0.0.1',1496246349,'info','','-'),(307,3,'course','add_lesson','添加课程内容《HBase基本操作视频案例》(103)','{\"id\":\"103\",\"courseId\":\"4\",\"chapterId\":\"98\",\"number\":\"11\",\"seq\":\"26\",\"status\":\"unpublished\",\"title\":\"HBase\\u57fa\\u672c\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"167\",\"mediaSource\":\"self\",\"mediaName\":\"Hbase_2.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1496246389\",\"isTest\":\"0\"}','127.0.0.1',1496246389,'info','','-'),(308,3,'course','create','管理员观看课程内容《HBase基本操作视频案例》','','127.0.0.1',1496246392,'info','','-'),(309,3,'course','create','管理员观看课程内容《Hive概述视屏案例》','','127.0.0.1',1496246435,'info','','-'),(310,3,'course','create','管理员观看课程内容《Hive环境搭建》','','127.0.0.1',1496246445,'info','','-'),(311,3,'course','create','管理员观看课程内容《Hive Sql基本操作视频案例》','','127.0.0.1',1496246470,'info','','-'),(312,3,'course','update_picture','更新课程《大数据基础课程》(#2)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/06-01\\/000219bc2305420004.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/06-01\\/000219bc16af362783.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/06-01\\/000219bbccec512735.jpg\"}','127.0.0.1',1496246539,'info','','-'),(313,3,'course','create','管理员观看课程内容《Scala基本操作视频案例》','','127.0.0.1',1496246558,'info','','-'),(314,0,'course','create','游客观看课程内容《Spark简介视频案例》','','127.0.0.1',1496247428,'info','','-'),(315,0,'course','create','游客观看课程内容《Spark简介视频案例》','','127.0.0.1',1496247630,'info','','-'),(316,3,'system','update_settings','更新站点LOGO','{\"logo\":\"myfiles\\/xitong\\/logo_1496248037.png\"}','127.0.0.1',1496248037,'info','','-'),(317,3,'system','update_settings','更新站点设置','{\"name\":\"\\u745e\\u5fb7\\u53e3\\u888b\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u7cfb\\u7edf\",\"slogan\":\"\\u745e\\u5fb7\\u53e3\\u888b\\u4e91\\u8bfe\\u5b9e\\u9a8c\\u7cfb\\u7edf\",\"url\":\"http:\\/\\/tlc.com\",\"school_name\":\"\\u745e\\u5fb7\\u4e91\\u7f51\",\"school_english_name\":\"red cloud\",\"logo\":\"myfiles\\/xitong\\/logo_1496248037.png\",\"themeCfg\":{\"THEME_ICON_COLOR\":\"33ffdd\",\"THEME_FRONT_COLOR\":\"#ea4335\",\"THEME_BACK_COLOR\":\"#ea4335\",\"THEME_NAV_BACK_COLOR\":\"#ea4335\"},\"favicon\":\"files\\/system\\/favicon_1469184392.ico\",\"seo_keywords\":\"\",\"seo_description\":\"\",\"master_email\":\"326728721@qq.com\",\"copyright\":\"\",\"friend_link_enable\":\"0\",\"friend_link\":[],\"about_us_enable\":\"0\",\"about_us_title\":\"\",\"icp\":\"\\u5317\\u4eac\\u745e\\u5fb7\\u4e91\\u7f51\\u79d1\\u6280\\u6709\\u9650\\u516c\\u53f8\\uff08c\\uff092017 \\u4eacICP\\u590717016632\",\"contact\":\"\",\"analytics\":\"\",\"status\":\"open\",\"closed_note\":\"\",\"_csrf_token\":\"c13a4e0d743cbb15d34869e6a60f63f22b075731\"}','127.0.0.1',1496248044,'info','','-'),(318,3,'course','create','管理员观看课程内容《Spark简介视频案例》','','127.0.0.1',1496248263,'info','','-'),(319,3,'course','create','管理员观看课程内容《Spark计算模型》','','127.0.0.1',1496248303,'info','','-'),(320,3,'course','create','管理员观看课程内容《Hive概述视屏案例》','','127.0.0.1',1496248622,'info','','-'),(321,3,'course','create','管理员观看课程内容《Hive概述视屏案例》','','127.0.0.1',1496248651,'info','','-'),(322,3,'course','create','管理员观看课程内容《Hive概述视屏案例》','','127.0.0.1',1496248656,'info','','-'),(323,3,'course','close','关闭课程《内存计算框架技术&Spark》(#9)','','127.0.0.1',1496248711,'info','','-'),(324,3,'course','publish','发布课程《内存计算框架技术&Spark》(#9)','','127.0.0.1',1496248740,'info','','-'),(325,0,'course','create','游客观看课程内容《Spark计算模型》','','127.0.0.1',1496276151,'info','','-'),(326,0,'course','create','游客观看课程内容《Hadoop概述视频讲解案例》','','127.0.0.1',1496283134,'info','','-'),(327,0,'course','create','游客观看课程内容《Hadoop部署一》','','127.0.0.1',1496283141,'info','','-'),(328,0,'course','create','游客观看课程内容《Hadoop部署二》','','127.0.0.1',1496283271,'info','','-'),(329,0,'course','create','游客观看课程内容《HBase基本操作视频案例》','','127.0.0.1',1496284511,'info','','-'),(330,3,'user','login_success','登录成功','','127.0.0.1',1496285837,'info','','-'),(331,3,'user','login_success','登录成功','','127.0.0.1',1497190451,'info','','-'),(332,1,'user','login_success','登录成功','','127.0.0.1',1497191025,'info','','-'),(333,3,'user','login_success','登录成功','','127.0.0.1',1497191142,'info','','-'),(334,3,'user','login_success','登录成功','','127.0.0.1',1497875697,'info','','-'),(335,3,'user','login_success','登录成功','','127.0.0.1',1497913021,'info','','-'),(336,3,'user','login_success','登录成功','','127.0.0.1',1497964422,'info','','-'),(337,3,'user','login_success','登录成功','','127.0.0.1',1497999146,'info','','-'),(338,3,'user','login_success','登录成功','','127.0.0.1',1498050234,'info','','-'),(339,3,'user','login_success','登录成功','','127.0.0.1',1498056803,'info','','-'),(340,3,'course','update_picture','更新课程《OpenStack》(#10)图片','{\"smallPicture\":\"public:\\/\\/course\\/2017\\/06-21\\/225555b2a2ab573095.jpg\",\"middlePicture\":\"public:\\/\\/course\\/2017\\/06-21\\/225555b29356522658.jpg\",\"largePicture\":\"public:\\/\\/course\\/2017\\/06-21\\/225555b26af5097281.jpg\"}','127.0.0.1',1498056955,'info','','-'),(341,3,'course','publish','发布课程《OpenStack》(#10)','','127.0.0.1',1498056964,'info','','-'),(342,3,'course','add_lesson','添加课程内容《了解OpenStack主要组件》(104)','{\"id\":\"104\",\"courseId\":\"10\",\"chapterId\":\"134\",\"number\":\"1\",\"seq\":\"4\",\"status\":\"unpublished\",\"title\":\"\\u4e86\\u89e3OpenStack\\u4e3b\\u8981\\u7ec4\\u4ef6\",\"summary\":\"\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"168\",\"mediaSource\":\"self\",\"mediaName\":\"OpenStack\\u4e3b\\u8981\\u7ec4\\u4ef6.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1498057070\",\"isTest\":\"0\"}','127.0.0.1',1498057070,'info','','-'),(343,3,'course','add_lesson','添加课程内容《DevStack部署Openstack》(105)','{\"id\":\"105\",\"courseId\":\"10\",\"chapterId\":\"135\",\"number\":\"1\",\"seq\":\"5\",\"status\":\"unpublished\",\"title\":\"DevStack\\u90e8\\u7f72Openstack\",\"summary\":\"\\u4e3b\\u8981\\u5bf9Openstack\\u73af\\u5883\\u90e8\\u7f72\\u6709\\u4e00\\u4e2a\\u6982\\u5ff5\\u6027\\u7684\\u4e86\\u89e3\\u3002\",\"tags\":null,\"type\":\"document\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"169\",\"mediaSource\":\"self\",\"mediaName\":\"Devstack\\u642d\\u5efaOpenStack.pdf\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1498057113\",\"isTest\":\"0\"}','127.0.0.1',1498057113,'info','','-'),(344,3,'user','login_success','登录成功','','127.0.0.1',1498083432,'info','','-'),(345,3,'user','login_success','登录成功','','127.0.0.1',1498088715,'info','','-'),(346,3,'user','login_success','登录成功','','127.0.0.1',1498141165,'info','','-'),(347,3,'course','add_lesson','添加课程内容《DevStack 部署OpenStack》(106)','{\"id\":\"106\",\"courseId\":\"10\",\"chapterId\":\"135\",\"number\":\"3\",\"seq\":\"6\",\"status\":\"unpublished\",\"title\":\"DevStack \\u90e8\\u7f72OpenStack\",\"summary\":\"\",\"tags\":null,\"type\":\"video\",\"content\":\"\",\"giveCredit\":\"0\",\"requireCredit\":\"0\",\"mediaId\":\"170\",\"mediaSource\":\"self\",\"mediaName\":\"te-3.mp4\",\"mediaUri\":\"\",\"polyvVid\":\"\",\"polyvVideoSize\":\"0\",\"polyvExtra\":null,\"length\":null,\"materialNum\":\"0\",\"quizNum\":\"0\",\"learnedNum\":\"0\",\"viewedNum\":\"0\",\"startTime\":\"0\",\"endTime\":\"0\",\"replayStatus\":\"ungenerated\",\"userId\":\"3\",\"testCount\":\"0\",\"createdTime\":\"1498141257\",\"isTest\":\"0\"}','127.0.0.1',1498141257,'info','','-'),(348,3,'course','create','管理员观看课程内容《DevStack 部署OpenStack》','','127.0.0.1',1498141261,'info','','-'),(349,3,'course','create','管理员观看课程内容《DevStack 部署OpenStack》','','127.0.0.1',1498141324,'info','','-'),(350,0,'course','create','游客观看课程内容《DevStack 部署OpenStack》','','127.0.0.1',1498180606,'info','','-'),(351,3,'user','login_success','登录成功','','127.0.0.1',1498187012,'info','','-'),(352,3,'user','login_success','登录成功','','127.0.0.1',1499089497,'info','','-'),(353,3,'user','login_success','登录成功','','127.0.0.1',1499090995,'info','','-'),(354,1,'user','login_success','登录成功','','127.0.0.1',1499091046,'info','','-'),(355,3,'user','login_success','登录成功','','127.0.0.1',1499091860,'info','','-'),(356,1,'user','login_success','登录成功','','127.0.0.1',1499092187,'info','','-'),(357,3,'user','login_success','登录成功','','127.0.0.1',1499092456,'info','','-'),(358,3,'user','login_success','登录成功','','127.0.0.1',1499092493,'info','','-'),(359,3,'user','login_success','登录成功','','127.0.0.1',1499092628,'info','','-'),(360,3,'user','login_success','登录成功','','127.0.0.1',1499092847,'info','','-'),(361,3,'user','login_success','登录成功','','127.0.0.1',1500988878,'info','','-'),(362,1,'user','login_success','登录成功','','127.0.0.1',1500988964,'info','','-'),(363,3,'user','login_success','登录成功','','127.0.0.1',1500989174,'info','','-'),(364,3,'user','login_success','登录成功','','127.0.0.1',1500990125,'info','','-'),(365,3,'user','login_success','登录成功','','127.0.0.1',1500991388,'info','','-'),(366,1,'user','login_success','登录成功','','127.0.0.1',1500992248,'info','','-'),(367,3,'user','login_success','登录成功','','127.0.0.1',1500992678,'info','','-'),(368,3,'user','login_success','登录成功','','127.0.0.1',1500992820,'info','','-'),(369,3,'user','login_success','登录成功','','127.0.0.1',1500995216,'info','','-'),(370,3,'user','login_success','登录成功','','127.0.0.1',1501563015,'info','','-'),(371,1,'user','login_success','登录成功','','127.0.0.1',1501563146,'info','','-'),(372,3,'user','login_success','登录成功','','127.0.0.1',1501564323,'info','','-'),(373,1,'user','login_success','登录成功','','127.0.0.1',1501564473,'info','','-'),(374,3,'user','login_success','登录成功','','127.0.0.1',1501564789,'info','','-'),(375,3,'user','login_success','登录成功','','127.0.0.1',1502167152,'info','','-'),(376,1,'user','login_success','登录成功','','127.0.0.1',1502167382,'info','','-'),(377,1,'user','login_success','登录成功','','127.0.0.1',1502174437,'info','','-'),(378,1,'course','create','学生观看课程内容《Hadoop概述视频讲解案例》','','127.0.0.1',1502174669,'info','','-'),(379,1,'course','create','学生观看课程内容《Hadoop概述视频讲解案例》','','127.0.0.1',1502174681,'info','','-'),(380,3,'user','login_success','登录成功','','127.0.0.1',1502174738,'info','','-'),(381,1,'user','login_success','登录成功','','127.0.0.1',1502174782,'info','','-'),(382,3,'user','login_success','登录成功','','127.0.0.1',1502609166,'info','','-'),(383,3,'user','login_success','登录成功','','127.0.0.1',1502887490,'info','','-'),(384,0,'course','create','游客观看课程内容《DevStack 部署OpenStack》','','192.168.122.19',1502888949,'info','','-'),(385,3,'user','login_success','登录成功','','192.168.122.19',1502888969,'info','','-'),(386,3,'course','create','管理员观看课程内容《Hadoop概述视频讲解案例》','','192.168.122.19',1502888983,'info','','-'),(387,3,'course','create','管理员观看课程内容《Hadoop概述视频讲解案例》','','192.168.122.19',1502888991,'info','','-'),(388,3,'course','create','管理员观看课程内容《Hadoop概述视频讲解案例》','','192.168.122.19',1502889027,'info','','-'),(389,3,'course','create','管理员观看课程内容《Hive Sql基本操作视频案例》','','192.168.122.19',1502889083,'info','','-'),(390,0,'course','create','游客观看课程内容《Hive Sql基本操作视频案例》','','192.168.122.146',1502889400,'info','','-'),(391,3,'user','login_success','登录成功','','127.0.0.1',1503580988,'info','','-'),(392,1,'user','login_success','登录成功','','127.0.0.1',1503581044,'info','','-'),(393,1,'course','create','学生观看课程内容《DevStack 部署OpenStack》','','127.0.0.1',1503583348,'info','','-'),(394,1,'course','create','学生观看课程内容《Hadoop概述视频讲解案例》','','127.0.0.1',1503583375,'info','','-'),(395,1,'course','create','学生观看课程内容《Hadoop概述视频讲解案例》','','127.0.0.1',1503583403,'info','','-'),(396,3,'user','login_success','登录成功','','127.0.0.1',1503583443,'info','','-'),(397,3,'course','create','管理员观看课程内容《HBase术语及数据模型》','','127.0.0.1',1503584014,'info','','-'),(398,3,'course','create','管理员观看课程内容《HBase概述视频讲解案例》','','127.0.0.1',1503584065,'info','','-'),(399,3,'course','create','管理员观看课程内容《HBase术语及数据模型》','','127.0.0.1',1503584070,'info','','-'),(400,0,'course','create','游客观看课程内容《Scala基本操作视频案例》','','127.0.0.1',1503584210,'info','','-'),(401,3,'user','login_success','登录成功','','127.0.0.1',1503584816,'info','','-'),(402,3,'course','create','管理员观看课程内容《Hive概述视屏案例》','','127.0.0.1',1503585725,'info','','-'),(403,3,'user','login_success','登录成功','','127.0.0.1',1503586586,'info','','-'),(404,3,'user','login_success','登录成功','','127.0.0.1',1503669576,'info','','-'),(405,1,'user','login_success','登录成功','','127.0.0.1',1503670900,'info','','-'),(406,3,'user','login_success','登录成功','','127.0.0.1',1503673232,'info','','-'),(407,0,'course','create','游客观看课程内容《Hadoop概述视频讲解案例》','','127.0.0.1',1503673958,'info','','-'),(408,1,'user','login_success','登录成功','','127.0.0.1',1503673987,'info','','-'),(409,3,'user','login_success','登录成功','','127.0.0.1',1503722470,'info','','-'),(410,3,'course','create','管理员观看课程内容《Hadoop概述视频讲解案例》','','127.0.0.1',1503722480,'info','','-'),(411,3,'user','login_success','登录成功','','127.0.0.1',1503734226,'info','','-'),(412,3,'user','login_success','登录成功','','127.0.0.1',1505429741,'info','','-'),(413,3,'user','login_success','登录成功','','127.0.0.1',1505918783,'info','','-');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `majors`
--

DROP TABLE IF EXISTS `majors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `majors` (
  `major_id` char(20) NOT NULL,
  `department_id` char(20) DEFAULT NULL,
  `major_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`major_id`),
  KEY `FK_reference_14` (`department_id`) USING BTREE,
  CONSTRAINT `majors_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `majors`
--

LOCK TABLES `majors` WRITE;
/*!40000 ALTER TABLE `majors` DISABLE KEYS */;
INSERT INTO `majors` VALUES ('3111','311','卫星广播与数字微波技术 '),('3112','311','光纤通信技术 '),('3113','311','移动通信技术 '),('3114','311','宽带综合信息网技术 '),('3121','312','宽带综合信息网技术 '),('3122','312','信息处理 '),('3123','312','数字广播电视发送技术 '),('3124','312','广播电视设备综合测试技术 '),('3131','313','数字电视技术 '),('3132','313','数字影视制作技术 '),('3133','313','网络视音频技术 '),('3141','314','广播电视自动化方向 '),('3142','314','演艺智能系统方向 '),('3151','315','广播电视自动化方向 '),('3152','315','演艺智能系统方向 '),('3161','316','专用集成电路设计 '),('3162','316','片上系统集成 '),('3211','321','计算机科学与技术 '),('3212','321','网络安全 '),('3311','331','计算数学与应用软件'),('3312','331','人工智能与信息处理'),('3313','331','基础数学'),('3314','331','经济数学'),('3315','331','应用统计学'),('3316','331','数据挖掘');
/*!40000 ALTER TABLE `majors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '私信Id',
  `fromId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发信人Id',
  `toId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收信人Id',
  `content` text NOT NULL COMMENT '私信内容',
  `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '私信发送时间',
  `title` varchar(500) NOT NULL COMMENT '标题',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (1,1,4,'斯路游戏,欢迎加入课程毛泽东概论',1465710368,'','tlc','-'),(2,1,4,'斯路游戏,欢迎加入课程部署测试4',1467707540,'','tlc','-'),(3,1,4,'斯路游戏,欢迎加入课程毛泽东概论',1467707853,'','tlc','-'),(4,1,700,'冯欣,欢迎加入课程毛泽东概论',1467910700,'','tlc','-'),(5,12,16,'鲍南海写给李冬梅的私信！',1468930293,'','','-'),(6,1,12,'南海兄，别来无恙',1470988993,'','','-'),(7,2,3,'你最近好吗',1475999752,'','','-'),(8,3,80,'我邀请您和我一同讲授《我和林卫国老师一起上的课》，点击<a href=\"http:///Course/CourseInvitation?id=23&amp;accept=1\" target=\"_blank\">此处</a>接受我的邀请。如果这是一个错误的邀请，您可以点击<a href=\"http:///Course/CourseInvitation?id=23&amp;accept=0\" target=\"_blank\">此处</a>拒绝',1476000816,'','','-'),(9,3,1,'我邀请您和我一同讲授《我和林卫国老师一起上的课》，点击<a href=\"http:///Course/CourseInvitation?id=23&amp;accept=1\" target=\"_blank\">此处</a>接受我的邀请。如果这是一个错误的邀请，您可以点击<a href=\"http:///Course/CourseInvitation?id=23&amp;accept=0\" target=\"_blank\">此处</a>拒绝',1476000816,'','','-'),(10,2234,0,'我邀请您和我一同讲授《大数据》，点击<a href=\"http:///Course/CourseInvitation?id=24&amp;accept=1\" target=\"_blank\">此处</a>接受我的邀请。如果这是一个错误的邀请，您可以点击<a href=\"http:///Course/CourseInvitation?id=24&amp;accept=0\" target=\"_blank\">此处</a>拒绝',1477228369,'','','-'),(11,80,3,'我接受了你的邀请',1477229481,'','','-'),(12,2234,0,'我邀请您和我一同讲授《大数据》，点击<a href=\"http:///Course/CourseInvitation/indexAction?id=24&amp;accept=1\" target=\"_blank\">此处</a>接受我的邀请。如果这是一个错误的邀请，您可以点击<a href=\"http:///Course/CourseInvitation/indexAction?id=24&amp;accept=0\" target=\"_blank\">此处</a>拒绝',1477230394,'','','-'),(13,2234,0,'我邀请您和我一同讲授《大数据》，点击<a href=\"http:///Course/CourseInvitation/indexAction?id=24&amp;accept=1\" target=\"_blank\">此处</a>接受我的邀请。如果这是一个错误的邀请，您可以点击<a href=\"http:///Course/CourseInvitation/indexAction?id=24&amp;accept=0\" target=\"_blank\">此处</a>拒绝',1477232331,'','','-'),(14,2234,80,'我邀请您和我一同讲授《大数据》，点击<a href=\"http:///Course/CourseInvitation/indexAction?id=24&amp;accept=1\" target=\"_blank\">此处</a>接受我的邀请。如果这是一个错误的邀请，您可以点击<a href=\"http:///Course/CourseInvitation/indexAction?id=24&amp;accept=0\" target=\"_blank\">此处</a>拒绝',1477232331,'','','-'),(15,2234,1,'我邀请您和我一同讲授《大数据》，点击<a href=\"http:///Course/CourseInvitation/indexAction?id=24&amp;accept=1\" target=\"_blank\">此处</a>接受我的邀请。如果这是一个错误的邀请，您可以点击<a href=\"http:///Course/CourseInvitation/indexAction?id=24&amp;accept=0\" target=\"_blank\">此处</a>拒绝',1477232331,'','','-');
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_conversation`
--

DROP TABLE IF EXISTS `message_conversation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_conversation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '会话Id',
  `fromId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发信人Id',
  `toId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收信人Id',
  `messageNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '此对话的信息条数',
  `latestMessageUserId` int(10) unsigned DEFAULT NULL COMMENT '最后发信人ID',
  `latestMessageTime` int(10) unsigned NOT NULL COMMENT '最后发信时间',
  `latestMessageContent` text NOT NULL COMMENT '最后发信内容',
  `unreadNum` int(10) unsigned NOT NULL COMMENT '未读数量',
  `createdTime` int(10) unsigned NOT NULL COMMENT '会话创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_conversation`
--

LOCK TABLES `message_conversation` WRITE;
/*!40000 ALTER TABLE `message_conversation` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_conversation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_relation`
--

DROP TABLE IF EXISTS `message_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息关联ID',
  `conversationId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的会话ID',
  `messageId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联的消息ID',
  `isRead` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否已读',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_relation`
--

LOCK TABLES `message_relation` WRITE;
/*!40000 ALTER TABLE `message_relation` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_relation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `webCode` char(15) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) COLLATE utf8_unicode_ci DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`version`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions` WRITE;
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `msg_message_task`
--

DROP TABLE IF EXISTS `msg_message_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `msg_message_task` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `msg_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=>短信 2=>邮件 3=>客户端推送 4=>站内信',
  `title` varchar(500) DEFAULT NULL COMMENT '标题',
  `content` text COMMENT '消息内容',
  `from_uid` int(11) NOT NULL DEFAULT '0' COMMENT '来源 0 ：系统  其他：用户uid',
  `to_uid` varchar(100) NOT NULL COMMENT '接收人',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '-1：作废  0:未开始 1:进行中 2:完成,3=>失败',
  `ctime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `ectime` int(11) NOT NULL DEFAULT '0' COMMENT '编辑时间',
  `class_id` text NOT NULL COMMENT '接受班级id串',
  `teacher_id` varchar(100) NOT NULL DEFAULT '' COMMENT '接受的老师id 串',
  `copy_people` varchar(255) NOT NULL COMMENT '附加抄送人',
  `noticetype` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0=>实时通知  1=>批量通知',
  `from_class_id` int(11) unsigned DEFAULT '0' COMMENT '来源 (0=>院系专业内部,非0=>班级内部,-1=>所有班级内部)',
  `ouid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `selectSendType` int(11) NOT NULL DEFAULT '0' COMMENT '当msg_type=3客户端推送时，0,2=>表示给班级推送,1=>表示给所有的设备推送',
  `fromRoles` varchar(255) NOT NULL COMMENT '来自于何种身份（manager=>班级管理者,teacher=>教师,leader=>院系领导）',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  `classNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '班级数',
  `studentNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '学员数',
  `teacherNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '老师数',
  `rangeType` tinyint(1) NOT NULL DEFAULT '0' COMMENT '范围类型',
  PRIMARY KEY (`task_id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `msg_message_task`
--

LOCK TABLES `msg_message_task` WRITE;
/*!40000 ALTER TABLE `msg_message_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `msg_message_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `navigation`
--

DROP TABLE IF EXISTS `navigation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `navigation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '导航ID',
  `name` varchar(255) NOT NULL COMMENT '导航名称',
  `url` varchar(300) NOT NULL COMMENT '链接地址',
  `sequence` tinyint(4) unsigned NOT NULL COMMENT '显示顺序',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父导航ID',
  `createdTime` int(11) NOT NULL COMMENT '创建时间',
  `updateTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `type` varchar(30) NOT NULL COMMENT '类型',
  `isOpen` tinyint(2) NOT NULL DEFAULT '1' COMMENT '默认1，为开启',
  `isNewWin` tinyint(2) NOT NULL DEFAULT '1' COMMENT '默认为1,另开窗口',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `navigation`
--

LOCK TABLES `navigation` WRITE;
/*!40000 ALTER TABLE `navigation` DISABLE KEYS */;
INSERT INTO `navigation` VALUES (1,'测试导航','http://www.baidu.com',1,0,1474116755,1474116755,'top',1,1);
/*!40000 ALTER TABLE `navigation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `node`
--

DROP TABLE IF EXISTS `node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `remark` varchar(255) DEFAULT '',
  `sort` smallint(6) unsigned DEFAULT '0',
  `pid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `public` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否公共权限',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `level` (`level`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `node`
--

LOCK TABLES `node` WRITE;
/*!40000 ALTER TABLE `node` DISABLE KEYS */;
/*!40000 ALTER TABLE `node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '通知ID',
  `userId` int(10) unsigned NOT NULL COMMENT '被通知的用户ID',
  `type` varchar(64) NOT NULL DEFAULT 'default' COMMENT '通知类型',
  `content` text COMMENT '通知内容',
  `createdTime` int(10) unsigned NOT NULL COMMENT '通知时间',
  `isRead` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification`
--

LOCK TABLES `notification` WRITE;
/*!40000 ALTER TABLE `notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `open_user`
--

DROP TABLE IF EXISTS `open_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `open_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '高校云互联id',
  `url` varchar(255) NOT NULL COMMENT '网站地址',
  `backUrl` varchar(255) NOT NULL COMMENT '回调地址',
  `appId` varchar(11) NOT NULL COMMENT 'appId,时间戳+用户id',
  `appKey` varchar(32) NOT NULL COMMENT 'appkey,用户id-时间戳-随机数 的32位MD5加密',
  `provider` varchar(128) NOT NULL COMMENT '提供方，或者就是网站网名/品牌',
  `name` varchar(128) NOT NULL COMMENT '互联应用名',
  `state` varchar(64) NOT NULL COMMENT '状态: new新增需审核 success已审核通过 error错误  stop管理禁用 modify用户修改 delete用户删除',
  `metaId` varchar(22) NOT NULL COMMENT '用于验证用户是否增加网页meta验证,用户id+时间戳',
  `admindes` varchar(255) NOT NULL DEFAULT '0' COMMENT '审核说明',
  `des` varchar(255) NOT NULL COMMENT '说明',
  `catId` int(11) NOT NULL DEFAULT '0' COMMENT '分类id保留的信息',
  `userId` int(11) NOT NULL COMMENT '用户id',
  `cTime` int(10) unsigned NOT NULL COMMENT '创建时间',
  `mTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `opTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间',
  `opUserId` int(11) NOT NULL COMMENT '审核管理员id',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `url` (`url`) USING BTREE,
  KEY `backUrl` (`backUrl`) USING BTREE,
  KEY `appId` (`appId`) USING BTREE,
  KEY `appKey` (`appKey`) USING BTREE,
  KEY `provider` (`provider`) USING BTREE,
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `open_user`
--

LOCK TABLES `open_user` WRITE;
/*!40000 ALTER TABLE `open_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `open_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pc_version`
--

DROP TABLE IF EXISTS `pc_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pc_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL COMMENT '软件名称',
  `url` char(200) NOT NULL COMMENT '下载链接',
  `version` char(20) NOT NULL COMMENT '版本',
  `function` text NOT NULL COMMENT '功能',
  `type` tinyint(1) NOT NULL COMMENT '1:windows 2:Mac',
  `isForceUpdate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否强制升级',
  `isCurrent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否当前版本',
  `ctm` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `mtm` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `webCode` char(15) NOT NULL DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) NOT NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  `appWebCode` char(15) NOT NULL DEFAULT '' COMMENT 'app学校代号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pc_version`
--

LOCK TABLES `pc_version` WRITE;
/*!40000 ALTER TABLE `pc_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `pc_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `problem_feedback`
--

DROP TABLE IF EXISTS `problem_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `problem_feedback` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `type` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0' COMMENT '0=>系统建议，1=>系统问题、2=>课程建议、3=>课程纠错、4=>其他 ,5=>App类问题',
  `roles` tinyint(1) NOT NULL COMMENT '用户角色 1:学生  2：老师',
  `content` text NOT NULL COMMENT '问题内容',
  `ctm` int(11) NOT NULL COMMENT '反馈时间',
  `from` tinyint(1) NOT NULL DEFAULT '0' COMMENT '问题来源0:web 1:ipad 2: iphone 3: android_pad 4: android  ',
  `picture` varchar(255) NOT NULL COMMENT '图片路径',
  `webCode` char(15) NOT NULL DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) NOT NULL DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `problem_feedback`
--

LOCK TABLES `problem_feedback` WRITE;
/*!40000 ALTER TABLE `problem_feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `problem_feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pubcourse_topri`
--

DROP TABLE IF EXISTS `pubcourse_topri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pubcourse_topri` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `courseId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程id',
  `operator` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作人',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0，已删除 1.正常',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pubcourse_topri`
--

LOCK TABLES `pubcourse_topri` WRITE;
/*!40000 ALTER TABLE `pubcourse_topri` DISABLE KEYS */;
/*!40000 ALTER TABLE `pubcourse_topri` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pubcourse_topri_record`
--

DROP TABLE IF EXISTS `pubcourse_topri_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pubcourse_topri_record` (
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `courseId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '课程id',
  `created_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  `title` varchar(255) NOT NULL COMMENT '课程标题',
  `payRange` tinyint(1) NOT NULL DEFAULT '0' COMMENT '收费范围 \r\n0. 对所有人免费\r\n1.对所有学员收费\r\n2.只对外校学员收费',
  `openRange` tinyint(1) NOT NULL DEFAULT '0' COMMENT '课程开发范围 \r\n0.对所有学员开放 \r\n1.只对本校学员开放\r\n 2.不对任何学员开放',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '课程价格',
  `categoryId` varchar(255) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0.未申请 1. 已申请 2.申请中',
  PRIMARY KEY (`courseId`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pubcourse_topri_record`
--

LOCK TABLES `pubcourse_topri_record` WRITE;
/*!40000 ALTER TABLE `pubcourse_topri_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `pubcourse_topri_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_log`
--

DROP TABLE IF EXISTS `push_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `push_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `sendUid` int(11) DEFAULT '0' COMMENT '发送人',
  `getUid` int(11) DEFAULT '0' COMMENT '接收人',
  `channel_ids` text COMMENT '接收人的channel_ids',
  `tagid` char(50) DEFAULT NULL COMMENT '百度tag',
  `classId` int(11) DEFAULT '0' COMMENT '班级id',
  `module` varchar(100) DEFAULT NULL COMMENT '模块',
  `operation` varchar(100) DEFAULT NULL COMMENT '模块下的操作',
  `title` varchar(200) NOT NULL DEFAULT '标题',
  `description` text COMMENT '内容',
  `msg_id` varchar(100) DEFAULT NULL COMMENT '百度返回消息id',
  `send_time` int(11) DEFAULT NULL COMMENT '百度返回发送时间',
  `platform` enum('android','ios') DEFAULT NULL COMMENT '平台',
  `data` text COMMENT '推动的参数以及内容',
  `response` text COMMENT '返回值',
  `ctime` int(10) DEFAULT NULL COMMENT '创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_log`
--

LOCK TABLES `push_log` WRITE;
/*!40000 ALTER TABLE `push_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_user_del_log`
--

DROP TABLE IF EXISTS `push_user_del_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `push_user_del_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '推送日志操作记录id',
  `userId` varchar(100) NOT NULL COMMENT '执行用户channel_id',
  `logId` int(11) NOT NULL DEFAULT '0' COMMENT '推送消息id',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `IDS` (`userId`,`logId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_user_del_log`
--

LOCK TABLES `push_user_del_log` WRITE;
/*!40000 ALTER TABLE `push_user_del_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_user_del_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '题目ID',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '题目类型',
  `stem` text COMMENT '题干',
  `imgURL` varchar(255) DEFAULT NULL COMMENT '题目图片URL',
  `score` float(10,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '分数',
  `answer` text COMMENT '参考答案',
  `analysis` text COMMENT '解析',
  `metas` text COMMENT '题目元信息',
  `categoryId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类别',
  `difficulty` varchar(64) NOT NULL DEFAULT 'normal' COMMENT '难度',
  `target` varchar(255) NOT NULL DEFAULT '' COMMENT '从属于',
  `parentId` int(10) unsigned DEFAULT '0' COMMENT '材料父ID',
  `subCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '子题数量',
  `finishedTimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '完成次数',
  `passedTimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '成功次数',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `updatedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `callNumber` int(10) NOT NULL DEFAULT '0' COMMENT '试卷调用数量 ： =0可编辑,删除；>0可编辑',
  `releaseNumber` int(10) NOT NULL DEFAULT '0' COMMENT '试卷发布数量 : =0 可编辑；>0 不可编辑',
  `isRecycle` tinyint(1) DEFAULT '0' COMMENT '回收站 0=>可用  1=>回收站',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
INSERT INTO `question` VALUES (1,'single_choice','<p>C罗是哪个国家的</p>','',2.0,'[\"3\"]','<p>由百度一下，可得，C罗是葡萄牙人</p>','{\"choices\":[\"\\u7f8e\\u56fd\",\"\\u897f\\u73ed\\u7259\",\"\\u4e2d\\u56fd[image]quizquestion\\/0\\/2016806025517-qj7onm.jpg[\\/image]\",\"\\u8461\\u8404\\u7259\"]}',0,'normal','course-7',0,0,0,0,12,1470466539,1470466539,0,0,0),(2,'essay','<p>rtsp直播程序作业，下周四之前统一发到我邮箱。</p>','',2.0,'[\"<p>\\u65e0<\\/p>\\r\\n\"]','<p>+如果发现重复，一律零分处理</p>','[]',0,'difficulty','course-7',0,0,1,0,12,1470468835,1470468801,2,2,0),(3,'single_choice','<p>算法的时间复杂度取决于</p>','',2.0,'[\"0\"]','<p>算法的时间复杂度取决于问题的规模。</p>','{\"choices\":[\"\\u95ee\\u9898\\u7684\\u89c4\\u6a21\",\"\\u5f85\\u5904\\u7406\\u6570\\u636e\\u7684\\u521d\\u6001\",\"A\\u548cB\",\"\\u8ba1\\u7b97\\u673acpu\"]}',0,'normal','course-21',0,0,0,0,12,1471409075,1471409075,2,2,0),(4,'fill','<p><span style=\"color:rgb(54, 46, 43)\">一个算法一该具有[[有穷性]]，[[确定性]]，[[可行性]]，[[输入]]，[[输出]]这五种特性。</span></p>','',2.0,'[[\"\\u6709\\u7a77\\u6027\"],[\"\\u786e\\u5b9a\\u6027\"],[\"\\u53ef\\u884c\\u6027\"],[\"\\u8f93\\u5165\"],[\"\\u8f93\\u51fa\"]]','<p><span style=\"color:rgb(54, 46, 43)\">一个算法一该具有</span><span style=\"color:rgb(54, 46, 43)\">有穷性，确定性，可行性，输入，输出</span><span style=\"color:rgb(54, 46, 43)\">这五种特性。</span></p>','[]',0,'normal','course-21',0,0,0,0,12,1471409575,1471409575,2,2,0),(5,'determine','<p>在线性表的链式存储结构中，逻辑上相邻的两个元素在物理位置上并不一定紧邻。</p>','',2.0,'[\"1\"]','','[]',0,'normal','course-21',0,0,0,0,12,1471409864,1471409864,2,2,0),(6,'essay','<p>已知一个图的顶点集V和边集E分别为：V={1,2,3,4,5,6,7};</p>\r\n\r\n<p>E={(1,2)3,(1,3)5,(1,4)8,(2,5)10,(2,3)6,(3,4)15,</p>\r\n\r\n<p>(3,5)12,(3,6)9,(4,6)4,(4,7)20,(5,6)18,(6,7)25};</p>\r\n\r\n<p>用克鲁斯卡尔算法得到最小生成树，试写出在最小生成树中依次得到的各条边。</p>','',2.0,'[\"<p><span style=\\\"color:rgb(34, 34, 34)\\\">(1,2)3, &nbsp;(4,6)4, &nbsp;(1,3)5, &nbsp;(1,4)8, &nbsp;(2,5)10, &nbsp;(4,7)20<\\/span><\\/p>\\r\\n\"]','','[]',0,'normal','course-21',0,0,1,1,12,1471410298,1471410298,2,2,0),(7,'essay','<p>问世间情为何物</p>','',2.0,'[\"<p>[image]quizquestion\\/0\\/2016923042527-ngae92.jpg[\\/image]<\\/p>\\r\\n\\r\\n<p>\\u76f4\\u6559\\u4eba\\u751f\\u6b7b\\u76f8\\u8bb8<\\/p>\\r\\n\"]','','[]',0,'normal','course-4',0,0,0,0,1,1474619139,1474619139,1,1,0),(8,'essay','<p>这大概就是爱吧</p>','[image]quizquestion/0/2016923042708-as0b4a.jpg[/image]',8.0,'[\"<p>[image]quizquestion\\/0\\/2016923042704-rze6qj.jpg[\\/image]<\\/p>\\r\\n\"]','<p>[image]quizquestion/0/2016923042700-qau18x.jpg[/image]</p>','[]',0,'simple','course-4',0,0,0,0,1,1474619257,1474619257,0,0,0);
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_audio`
--

DROP TABLE IF EXISTS `question_audio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `info` varchar(5000) DEFAULT NULL COMMENT '翻译的文字',
  `pathUrl` varchar(300) DEFAULT NULL COMMENT '音频地址',
  `armFileSrc` varchar(300) DEFAULT NULL COMMENT 'arm音频文件存放位置',
  `duration` int(11) DEFAULT '0' COMMENT '音频时长',
  `ctime` int(11) DEFAULT NULL COMMENT '发布时间',
  `wyzcRecId` int(10) DEFAULT '0' COMMENT '我赢职场音频Id',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_audio`
--

LOCK TABLES `question_audio` WRITE;
/*!40000 ALTER TABLE `question_audio` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_audio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_category`
--

DROP TABLE IF EXISTS `question_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '题目类别ID',
  `name` varchar(255) NOT NULL COMMENT '类别名称',
  `target` varchar(255) NOT NULL DEFAULT '' COMMENT '从属于',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户',
  `updatedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `seq` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序序号',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_category`
--

LOCK TABLES `question_category` WRITE;
/*!40000 ALTER TABLE `question_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_favorite`
--

DROP TABLE IF EXISTS `question_favorite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '题目收藏ID',
  `questionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被收藏的题目ID',
  `target` varchar(255) NOT NULL DEFAULT '' COMMENT '题目所属对象',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏人ID',
  `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_favorite`
--

LOCK TABLES `question_favorite` WRITE;
/*!40000 ALTER TABLE `question_favorite` DISABLE KEYS */;
INSERT INTO `question_favorite` VALUES (1,1,'testpaper-4',12,1470468280,'','-'),(2,1,'testpaper-4',288,1490535256,'','-');
/*!40000 ALTER TABLE `question_favorite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `courseId` int(11) unsigned NOT NULL COMMENT '资料所属课程ID',
  `uploadFileId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '如果是课程内容，对应的upload_files记录的ID',
  `createUid` int(11) NOT NULL COMMENT '上传人id',
  `createTm` int(11) NOT NULL COMMENT '上传时间',
  `updateUid` int(11) NOT NULL COMMENT '修改人id',
  `updateTm` int(11) NOT NULL COMMENT '修改时间',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `power` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=>仅限本课程学员下载,1=>所有用户可免费下载,2=>非本课程学员可用金币兑换下载',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '文件URL',
  `ext` varchar(12) NOT NULL DEFAULT '' COMMENT '文件的格式',
  `size` bigint(20) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `downloadNum` int(11) NOT NULL DEFAULT '0' COMMENT '文件下载次数',
  `isLessonContent` tinyint(5) unsigned NOT NULL DEFAULT '0' COMMENT '是否是课程内容，1是课程内容',
  `storage` enum('cloud','local') NOT NULL DEFAULT 'local' COMMENT '文件存储方式',
  `isSyncCloud` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否同步到云盘，1同步，0不同步',
  `syncCloudPath` varchar(255) NOT NULL DEFAULT '' COMMENT '同步到网盘中的路径',
  `syncStatus` enum('failure','success','none','waiting') NOT NULL DEFAULT 'none' COMMENT '同步到网盘中的状态',
  `syncStartTime` int(11) unsigned DEFAULT NULL COMMENT '同步开始时间，即将同步任务加入队列的时间戳',
  `syncFinishTime` int(11) unsigned DEFAULT NULL COMMENT '同步完成的时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=157 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resource`
--

LOCK TABLES `resource` WRITE;
/*!40000 ALTER TABLE `resource` DISABLE KEYS */;
INSERT INTO `resource` VALUES (1,9,0,16,1469333454,16,1469333454,'Python重要资料',1,'/undefined/center/course/2016-7/24_41/struts-2.5.1-all.zip','zip',74645804,2,0,'local',0,'','none',NULL,NULL),(2,9,0,16,1469942970,16,1469942970,'Desert.jpg',1,'resource/9/2016731012930-gyqnoj.jpg','jpg',845941,0,1,'local',0,'','none',NULL,NULL),(3,8,0,16,1469970832,16,1469970832,'PHP5.6源码包64位',1,'resource/8/2016731090802-p2uri8.zip','zip',22861109,0,0,'local',0,'','none',NULL,NULL),(4,8,0,16,1470027067,16,1470027067,'第一课 —— 野生动物赏析',1,'courselesson/8/2016801125044-bbpug4.wmv','wmv',26246026,0,1,'local',0,'','none',NULL,NULL),(5,7,0,16,1470231702,16,1470231702,'MP4文件播放测试',1,'courselesson/7/2016803094128-ddo3d9.mp4','mp4',15797610,0,1,'local',0,'','none',NULL,NULL),(6,7,0,12,1470453221,12,1470453221,'1_0.mp4',1,'Documents/videos/1_0.mp4','mp4',15797610,0,1,'cloud',0,'','none',NULL,NULL),(7,7,0,12,1470453221,12,1470453221,'从网盘导入MP4文件测试',1,'Documents/videos/1_0.mp4','mp4',15797610,0,1,'cloud',0,'','none',NULL,NULL),(14,7,23,12,1471271965,12,1471271965,'1_0.mp4',1,'Documents/videos/1_0.mp4','mp4',15797610,0,1,'cloud',0,'','none',NULL,NULL),(9,7,0,12,1470840302,12,1470840302,'非常重要的课件',1,'resource/7/2016810104349-1octnx.zip','zip',1361781,0,0,'local',0,'','none',NULL,NULL),(10,7,17,0,1471003316,0,1471003316,'firefox上传视频咋就',1,'courselesson/7/2016812080152-7mpij2.mp4','mp4',15797610,0,1,'local',0,'','none',NULL,NULL),(11,5,19,0,1471006089,0,1471006089,'上天啦',1,'courselesson/5/2016812084807-ou29gs.mp4','mp4',15797610,0,1,'local',0,'','none',NULL,NULL),(12,7,21,12,1471008713,12,1471008713,'111',1,'courselesson/7/2016812093148-mwlsz9.pdf','pdf',712573,0,1,'local',0,'','none',NULL,NULL),(13,7,22,12,1471009275,12,1471009275,'3333',1,'courselesson/7/2016812094113-ofqt8r.pdf','pdf',712573,0,1,'local',0,'','none',NULL,NULL),(15,21,24,12,1471405940,12,1471405940,'存储实现与运算实现',1,'courselesson/21/2016817114954-nvxhnn.mp4','mp4',15797610,0,1,'local',0,'','none',NULL,NULL),(16,21,25,12,1471406823,12,1471406823,'1_0.mp4',1,'Documents/videos/1_0.mp4','mp4',15797610,0,1,'cloud',0,'','none',NULL,NULL),(17,21,28,12,1471407841,12,1471407841,'线性表基本概念',1,'courselesson/21/2016817122358-cxbiv8.pdf','pdf',139613,0,1,'local',0,'','none',NULL,NULL),(18,20,6,12,1471410829,12,1471410829,'数据结构与算法第一次练习',1,'courselesson/20/2016728040848-mnlgmx.docx','docx',26376,0,1,'local',0,'','none',NULL,NULL),(19,21,0,12,1471419669,12,1471419669,'数据结构与算法重要资料',1,'resource/21/2016817034050-gpjzcg.zip','zip',5972532,0,0,'local',0,'','none',NULL,NULL),(21,21,30,12,1471507175,12,1471507175,'测试一个不能播的视频',1,'courselesson/21/2016818035842-4j9fm0.mp4','mp4',20290728,0,1,'local',0,'','waiting',NULL,NULL),(22,7,31,12,1471786436,12,1471786436,'上传个稍微大一点的视频',1,'courselesson/7/2016821093354-5okzll.avi','avi',94032942,0,1,'local',0,'','none',NULL,NULL),(28,7,39,12,1471875361,12,1471875361,'啦啦啦',1,'courselesson/7/2016822101557-7mxq5c.doc','doc',640512,0,1,'local',0,'','none',NULL,NULL),(29,7,40,12,1471875400,12,1471875400,'忽然之间',1,'courselesson/7/2016822101638-om7jl2.pdf','pdf',712573,0,1,'local',0,'','none',NULL,NULL),(30,7,41,12,1471876009,12,1471876009,'就算时针都停摆',1,'courselesson/7/2016822102646-3fwkmy.pdf','pdf',778993,0,1,'local',0,'','none',NULL,NULL),(32,21,43,12,1471877322,12,1471877322,'你到底属于谁',1,'courselesson/21/2016822104841-dpr3xv.docx','docx',2475709,0,1,'local',0,'','none',NULL,NULL),(33,7,44,12,1471932272,12,1471932272,'好累好困不想活了',1,'courselesson/7/2016823020427-otj91f.pdf','pdf',904202,0,1,'local',0,'','none',NULL,NULL),(34,7,0,12,1472102216,12,1472102216,'i love leilei',1,'resource/7/2016825011648-nknci0.doc','doc',26624,0,0,'local',0,'','none',NULL,NULL),(35,7,0,12,1472102395,12,1472102395,'just upload a resource',1,'resource/7/2016825011951-gi3zlu.zip','zip',137682,0,0,'local',0,'','none',NULL,NULL),(36,7,0,12,1472103732,12,1472103732,'gogogo',1,'resource/7;jsessionid=3g8d2rusom6rj3j3ndnigurb97/2016825014210-ae8h2o.zip','zip',103189,0,0,'local',0,'','none',NULL,NULL),(37,7,0,12,1472539801,12,1472539801,'我想同步到网盘',1,'resource/7/2016830024954-gelodw.zip','zip',137682,0,0,'local',0,'','none',NULL,NULL),(38,7,0,12,1472539975,12,1472539975,'zzzaaa',1,'resource/7/2016830025250-c5mqf4.doc','doc',26624,0,0,'local',0,'','none',NULL,NULL),(39,7,0,12,1472540032,12,1472540032,'cluo啊啊',1,'resource/7/2016830025346-q1qpqi.jpg','jpg',25415,0,0,'local',0,'','none',NULL,NULL),(40,7,0,12,1472540117,12,1472540117,'再来啊！',1,'resource/7/2016830025513-doc819.zip','zip',137682,0,0,'local',0,'','none',NULL,NULL),(41,20,45,5,1473677233,5,1473677233,'测试mov视频播放',1,'courselesson/20/2016912064711-cm81aj.mov','mov',27609910,0,1,'local',0,'','none',NULL,NULL),(42,22,46,230,1473764224,230,1473764224,'PBSG_DNS.webm',1,'courselesson/22/2016913065703-hz78fu.webm','webm',16825677,0,1,'local',0,'','none',NULL,NULL),(98,4,110,3,1496045879,3,1496045879,'HBase概述',1,'courselesson/4/2017529041754-5cwgew.pdf','pdf',663891,0,1,'local',0,'','none',NULL,NULL),(99,4,111,3,1496046080,3,1496046080,'HBase体系架构',1,'courselesson/4/2017529042108-79rbfn.pdf','pdf',746462,0,1,'local',0,'','none',NULL,NULL),(100,4,112,3,1496046137,3,1496046137,'HBase常用操作',1,'courselesson/4/2017529042151-rog7r6.pdf','pdf',603314,0,1,'local',0,'','none',NULL,NULL),(51,1,51,1,1474534440,1,1474534440,'牛啊啊啊啊',1,'courselesson/1/2016922045357-si4yi5.pdf','pdf',1275559,0,1,'local',0,'','none',NULL,NULL),(52,24,58,230,1479349728,230,1479349728,'第一章概要',1,'courselesson/24/20161022101006-t86lhn.pdf','pdf',1275559,0,1,'local',0,'','none',NULL,NULL),(53,24,60,2234,1479350108,2234,1479350108,'啦啦啦啦啦啦',1,'courselesson/24/20161117103251-n3960y.pptx','pptx',345462,0,1,'local',0,'','none',NULL,NULL),(55,2,67,3,1494726943,3,1494726943,'什么是Linux',1,'courselesson/2/2017514095535-a4owwk.mp4','mp4',21377079,0,1,'local',0,'','none',NULL,NULL),(63,2,75,3,1494728036,3,1494728036,'Hadoop概述',1,'courselesson/2/2017514101350-33w7fl.mp4','mp4',133141867,0,1,'local',0,'','none',NULL,NULL),(61,2,73,3,1494727988,3,1494727988,'Linux的主要版本',1,'courselesson/2/2017514101256-dj72z1.mp4','mp4',65852577,0,1,'local',0,'','none',NULL,NULL),(62,2,74,3,1494728009,3,1494728009,'Linux的优缺点',1,'courselesson/2/2017514101323-juklbk.mp4','mp4',47274754,0,1,'local',0,'','none',NULL,NULL),(64,2,76,3,1494728387,3,1494728387,'HBase概述',1,'courselesson/2/2017514101936-1qw4zp.mp4','mp4',71734392,0,1,'local',0,'','none',NULL,NULL),(65,2,77,3,1494728466,3,1494728466,'HBase术语及数据模型',1,'courselesson/2/2017514102042-tv5vb1.mp4','mp4',33687929,0,1,'local',0,'','none',NULL,NULL),(66,2,78,3,1494728705,3,1494728705,'Hive简介',1,'courselesson/2/2017514102456-hwfkwa.mp4','mp4',87889880,0,1,'local',0,'','none',NULL,NULL),(67,2,79,3,1494728724,3,1494728724,'Hive与HBase的关系',1,'courselesson/2/2017514102514-pk23r5.mp4','mp4',28345325,0,1,'local',0,'','none',NULL,NULL),(68,2,80,3,1494728947,3,1494728947,'Spark简介',1,'courselesson/2/2017514102900-gjo5ou.mp4','mp4',33660428,0,1,'local',0,'','none',NULL,NULL),(69,2,81,3,1494728966,3,1494728966,'Spark计算模型',1,'courselesson/2/2017514102917-sm4pxx.mp4','mp4',69397706,0,1,'local',0,'','none',NULL,NULL),(71,2,82,3,1494745327,3,1494745327,'Hadoop安装配置',1,'courselesson/2/2017514030158-o81rek.pdf','pdf',419028,0,1,'local',0,'','none',NULL,NULL),(72,2,83,3,1494745368,3,1494745368,'HDFS的基本使用',1,'courselesson/2/2017514030232-oa1wpw.pdf','pdf',418827,0,1,'local',0,'','none',NULL,NULL),(73,2,84,3,1494745405,3,1494745405,'HBase 环境安装配置',1,'courselesson/2/2017514030315-moep95.pdf','pdf',396517,0,1,'local',0,'','none',NULL,NULL),(74,2,85,3,1494745448,3,1494745448,'HBase Shell的基本使用',1,'courselesson/2/2017514030351-czzkg3.pdf','pdf',506953,0,1,'local',0,'','none',NULL,NULL),(78,2,89,3,1495545830,3,1495545830,'Hive安装配置',1,'courselesson/2/2017523092337-baqf6i.pdf','pdf',391481,0,1,'local',0,'','none',NULL,NULL),(79,2,90,3,1495545861,3,1495545861,'Hive Sql基本命令',1,'courselesson/2/2017523092410-aumsxn.pdf','pdf',372648,0,1,'local',0,'','none',NULL,NULL),(77,2,88,3,1494745560,3,1494745560,'Spark安装与操作',1,'courselesson/2/2017514030548-eu51kr.pdf','pdf',410664,0,1,'local',0,'','none',NULL,NULL),(80,3,91,3,1496044474,3,1496044474,'初识Hadoop （上）',1,'courselesson/3/2017529035433-q7tn80.pdf','pdf',699639,0,1,'local',0,'','none',NULL,NULL),(81,3,93,3,1496044541,3,1496044541,'初识Hadoop（下）',1,'courselesson/3/2017529035531-e7uk4d.pdf','pdf',832733,0,1,'local',0,'','none',NULL,NULL),(82,3,94,3,1496044719,3,1496044719,'Hadoop集群伪分布式搭建指南',1,'courselesson/3/2017529035822-5gu7r7.pdf','pdf',1303327,0,1,'local',0,'','none',NULL,NULL),(151,3,163,3,1496246238,3,1496246238,'Hadoop部署二',1,'courselesson/3/2017531115536-7078kq.mp4','mp4',53183415,0,1,'local',0,'','none',NULL,NULL),(150,3,162,3,1496246236,3,1496246236,'Hadoop部署一',1,'courselesson/3/2017531115450-8jn5wc.mp4','mp4',8941321,0,1,'local',0,'','none',NULL,NULL),(86,3,98,3,1496044894,3,1496044894,'HDFS文件系统（上）',1,'courselesson/3/2017529040123-tnnptv.pdf','pdf',681331,0,1,'local',0,'','none',NULL,NULL),(87,3,99,3,1496044916,3,1496044916,'HDFS文件系统（下）',1,'courselesson/3/2017529040145-lr7aq5.pdf','pdf',650408,0,1,'local',0,'','none',NULL,NULL),(88,3,100,3,1496045071,3,1496045071,'Hadoop基本操作指南',1,'courselesson/3/2017529040418-lzr2u9.pdf','pdf',348747,0,1,'local',0,'','none',NULL,NULL),(89,3,101,3,1496045107,3,1496045107,'Hadoop Shell 学习资料',1,'courselesson/3/2017529040451-6jryrh.pdf','pdf',13400974,0,1,'local',0,'','none',NULL,NULL),(90,3,102,3,1496045230,3,1496045230,'MapReduce计算框架（上）',1,'courselesson/3/2017529040658-o7tt89.pdf','pdf',725694,0,1,'local',0,'','none',NULL,NULL),(91,3,103,3,1496045256,3,1496045256,'MapReduce计算框架（下）',1,'courselesson/3/2017529040726-f2jtcd.pdf','pdf',894528,0,1,'local',0,'','none',NULL,NULL),(92,3,104,3,1496045405,3,1496045405,'MapReduce实验（一）',1,'courselesson/3/2017529040953-r51tuc.pdf','pdf',714014,0,1,'local',0,'','none',NULL,NULL),(93,3,105,3,1496045435,3,1496045435,'MapReduce实验（二）',1,'courselesson/3/2017529041033-q9tr0w.pdf','pdf',623837,0,1,'local',0,'','none',NULL,NULL),(152,3,165,3,1496246289,3,1496246289,'HDFS操作视屏案例',1,'courselesson/3/2017531115751-ed67gg.mp4','mp4',24913217,0,1,'local',0,'','none',NULL,NULL),(96,3,108,3,1496045671,3,1496045671,'Hadoop2.x集群维护',1,'courselesson/3/2017529041413-d0y2vc.pdf','pdf',654759,0,1,'local',0,'','none',NULL,NULL),(97,3,109,3,1496045720,3,1496045720,'Hadoop2.x框架基础（讲义）',1,'courselesson/3/2017529041451-agq199.pdf','pdf',824267,0,1,'local',0,'','none',NULL,NULL),(102,4,114,3,1496046212,3,1496046212,'HBase架构详解',1,'courselesson/4/2017529042322-am2o20.pdf','pdf',708494,0,1,'local',0,'','none',NULL,NULL),(103,4,115,3,1496046240,3,1496046240,'HBase高级特性',1,'courselesson/4/2017529042353-hxvoeu.pdf','pdf',751976,0,1,'local',0,'','none',NULL,NULL),(104,4,116,3,1496046268,3,1496046268,'HBase表操作',1,'courselesson/4/2017529042420-q344pj.pdf','pdf',661924,0,1,'local',0,'','none',NULL,NULL),(105,4,117,3,1496046303,3,1496046303,'HBase安装和系列实验',1,'courselesson/4/2017529042449-2htvk8.pdf','pdf',922636,0,1,'local',0,'','none',NULL,NULL),(106,4,118,3,1496061795,3,1496061795,'HBase环境搭建视频',1,'courselesson/4/2017529084301-jixuv5.mp4','mp4',17752664,0,1,'local',0,'','none',NULL,NULL),(153,4,167,3,1496246390,3,1496246390,'HBase基本操作视频案例',1,'courselesson/4/2017531115929-tuhxzb.mp4','mp4',39902804,0,1,'local',0,'','none',NULL,NULL),(108,5,120,3,1496062033,3,1496062033,'Hive概述',1,'courselesson/5/2017529084707-9v66gy.pdf','pdf',2182752,0,1,'local',0,'','none',NULL,NULL),(109,5,121,3,1496062073,3,1496062073,'Hive的体系结构',1,'courselesson/5/2017529084745-ru937e.pdf','pdf',1476528,0,1,'local',0,'','none',NULL,NULL),(110,5,122,3,1496062139,3,1496062139,'Hive数据类型和文件格式',1,'courselesson/5/2017529084846-kwehqs.pdf','pdf',2223644,0,1,'local',0,'','none',NULL,NULL),(111,5,123,3,1496062213,3,1496062213,'Hive常用操作方式',1,'courselesson/5/2017529085001-l09i8n.pdf','pdf',2602874,0,1,'local',0,'','none',NULL,NULL),(112,5,124,3,1496062264,3,1496062264,'HiveQL数据定义',1,'courselesson/5/2017529085052-yopde7.pdf','pdf',5827672,0,1,'local',0,'','none',NULL,NULL),(113,5,125,3,1496062307,3,1496062307,'HiveQL查询',1,'courselesson/5/2017529085128-k2s0b9.pdf','pdf',1690498,0,1,'local',0,'','none',NULL,NULL),(114,5,126,3,1496062388,3,1496062388,'Hive环境搭建',1,'courselesson/5/2017529085257-ss9yvu.mp4','mp4',30189738,0,1,'local',0,'','none',NULL,NULL),(115,5,127,3,1496062429,3,1496062429,'Hive Sql基本操作视频案例',1,'courselesson/5/2017529085335-8oub59.mp4','mp4',15758495,2,1,'local',0,'','none',NULL,NULL),(116,6,128,3,1496062926,3,1496062926,'Spark on Yarn',1,'courselesson/6/2017529090158-5kk929.pdf','pdf',1196159,0,1,'local',0,'','none',NULL,NULL),(117,6,129,3,1496062982,3,1496062982,'Spark HistoryServer',1,'courselesson/6/2017529090250-rjdgui.pdf','pdf',987398,0,1,'local',0,'','none',NULL,NULL),(118,6,130,3,1496063018,3,1496063018,'Spark Streaming',1,'courselesson/6/2017529090330-ta0tl8.pdf','pdf',793832,0,1,'local',0,'','none',NULL,NULL),(119,6,131,3,1496063088,3,1496063088,'Spark 项目实战',1,'courselesson/6/2017529090437-fv4ymj.pdf','pdf',1166135,0,1,'local',0,'','none',NULL,NULL),(120,6,132,3,1496063151,3,1496063151,'Spark高阶应用&实战（讲义）',1,'courselesson/6/2017529090524-sccp7y.pdf','pdf',2243490,0,1,'local',0,'','none',NULL,NULL),(121,7,133,3,1496063226,3,1496063226,'大数据协作框架_Sqoop （上）',1,'courselesson/7/2017529090644-tbeu3w.pdf','pdf',842898,0,1,'local',0,'','none',NULL,NULL),(122,7,134,3,1496063266,3,1496063266,'大数据协作框架_Sqoop （下）',1,'courselesson/7/2017529090732-hrlwuj.pdf','pdf',2469382,0,1,'local',0,'','none',NULL,NULL),(123,7,135,3,1496063317,3,1496063317,'大数据协作框架_Flume',1,'courselesson/7/2017529090824-7hgyb9.pdf','pdf',1798531,0,1,'local',0,'','none',NULL,NULL),(124,7,136,3,1496063362,3,1496063362,'大数据协作框架_Kafka',1,'courselesson/7/2017529090908-e305pb.pdf','pdf',3970131,0,1,'local',0,'','none',NULL,NULL),(125,7,137,3,1496063407,3,1496063407,'大数据协作框架_Phoenix',1,'courselesson/7/2017529090951-n6iuee.pdf','pdf',1496988,0,1,'local',0,'','none',NULL,NULL),(126,7,138,3,1496063440,3,1496063440,'大数据协作框架（讲义）',1,'courselesson/7/2017529091027-t54o9u.pdf','pdf',756304,0,1,'local',0,'','none',NULL,NULL),(127,8,139,3,1496063737,3,1496063737,'实时流式计算概述',1,'courselesson/8/2017529091524-q0bt26.pdf','pdf',1808871,0,1,'local',0,'','none',NULL,NULL),(128,8,140,3,1496063796,3,1496063796,'实时流式计算的应用场景及关键技术',1,'courselesson/8/2017529091617-qorzn9.pdf','pdf',1842751,0,1,'local',0,'','none',NULL,NULL),(129,8,141,3,1496063825,3,1496063825,'Storm概述',1,'courselesson/8/2017529091659-ma2g1z.pdf','pdf',2203711,0,1,'local',0,'','none',NULL,NULL),(130,8,142,3,1496063864,3,1496063864,'Storm的架构与原理',1,'courselesson/8/2017529091743-orbuzs.pdf','pdf',2922033,0,1,'local',0,'','none',NULL,NULL),(131,8,143,3,1496063901,3,1496063901,'Storm集群搭建部署',1,'courselesson/8/2017529091811-7yuttb.pdf','pdf',3385575,0,1,'local',0,'','none',NULL,NULL),(132,8,144,3,1496063948,3,1496063948,'Storm常见问题与解决方法',1,'courselesson/8/2017529091852-1k5471.pdf','pdf',1397529,0,1,'local',0,'','none',NULL,NULL),(133,8,145,3,1496064015,3,1496064015,'流式大数据处理（讲义）',1,'courselesson/8/2017529091959-2pdj3e.pdf','pdf',762837,0,1,'local',0,'','none',NULL,NULL),(134,9,146,3,1496064061,3,1496064061,'Spark以及Spark生态圈的介绍',1,'courselesson/9/2017529092055-nbr77t.pdf','pdf',1128728,0,1,'local',0,'','none',NULL,NULL),(135,9,147,3,1496064106,3,1496064106,'Spark集群搭建',1,'courselesson/9/2017529092139-evyzbh.pdf','pdf',1437524,0,1,'local',0,'','none',NULL,NULL),(136,9,148,3,1496064187,3,1496064187,'Spark应用提交工具',1,'courselesson/9/2017529092254-6dxj55.pdf','pdf',1764815,0,1,'local',0,'','none',NULL,NULL),(137,9,149,3,1496064243,3,1496064243,'Scala基本知识讲解',1,'courselesson/9/2017529092347-glppmj.pdf','pdf',2710934,0,1,'local',0,'','none',NULL,NULL),(138,9,150,3,1496064301,3,1496064301,'Scala基本操作视频案例',1,'courselesson/9/2017529092446-ignhrq.mp4','mp4',50755145,0,1,'local',0,'','none',NULL,NULL),(139,9,151,3,1496064367,3,1496064367,'RDD特性、常见操作、缓存策略',1,'courselesson/9/2017529092548-dtsh86.pdf','pdf',1738320,0,1,'local',0,'','none',NULL,NULL),(140,9,152,3,1496064417,3,1496064417,'RDD Dependency、Stage',1,'courselesson/9/2017529092646-1fqul6.pdf','pdf',2638139,0,1,'local',0,'','none',NULL,NULL),(141,9,153,3,1496064455,3,1496064455,'Spark核心组件概述及案例分析',1,'courselesson/9/2017529092722-l7pe59.pdf','pdf',941372,0,1,'local',0,'','none',NULL,NULL),(142,9,154,3,1496064511,3,1496064511,'Spark程序开发实验指南',1,'courselesson/9/2017529092830-geqh2j.pdf','pdf',843650,0,1,'local',0,'','none',NULL,NULL),(143,3,155,3,1496138691,3,1496138691,'Hadoop概述视频讲解案例',1,'courselesson/3/2017530060437-28iz05.mp4','mp4',31428014,0,1,'local',0,'','none',NULL,NULL),(144,4,156,3,1496138764,3,1496138764,'HBase概述视频讲解案例',1,'courselesson/4/2017530060554-nquhab.mp4','mp4',20450852,0,1,'local',0,'','none',NULL,NULL),(145,4,157,3,1496138840,3,1496138840,'HBase术语及数据模型',1,'courselesson/4/2017530060709-7g8uma.mp4','mp4',13844746,0,1,'local',0,'','none',NULL,NULL),(146,5,158,3,1496138869,3,1496138869,'Hive概述视屏案例',1,'courselesson/5/2017530060739-dkcojg.mp4','mp4',37191503,0,1,'local',0,'','none',NULL,NULL),(147,5,159,3,1496138893,3,1496138893,'Hive与HBase的关系',1,'courselesson/5/2017530060805-iteuv5.mp4','mp4',10540350,0,1,'local',0,'','none',NULL,NULL),(148,9,160,3,1496138928,3,1496138928,'Spark简介视频案例',1,'courselesson/9/2017530060836-kbr5as.mp4','mp4',17281530,0,1,'local',0,'','none',NULL,NULL),(149,9,161,3,1496138942,3,1496138942,'Spark计算模型',1,'courselesson/9/2017530060857-q8yhd5.mp4','mp4',26016651,0,1,'local',0,'','none',NULL,NULL),(154,10,169,3,1498057115,3,1498057115,'DevStack部署Openstack',1,'courselesson/10/2017621105832-h4oidu.pdf','pdf',444791,0,1,'local',0,'','none',NULL,NULL),(155,10,168,3,1498057117,3,1498057117,'了解OpenStack主要组件',1,'courselesson/10/2017621105749-4fikn6.pdf','pdf',577089,0,1,'local',0,'','none',NULL,NULL),(156,10,170,3,1498141318,3,1498141318,'DevStack 部署OpenStack',1,'courselesson/10/2017622102055-j93aam.mp4','mp4',34869953,0,1,'local',0,'','none',NULL,NULL);
/*!40000 ALTER TABLE `resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_node`
--

DROP TABLE IF EXISTS `role_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_node` (
  `role_id` smallint(6) unsigned NOT NULL,
  `node_id` smallint(6) unsigned NOT NULL,
  `level` tinyint(1) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `access` enum('all') DEFAULT NULL COMMENT '是否开启当前模块以及子模块的所有权限',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`node_id`,`role_id`),
  KEY `groupId` (`role_id`) USING BTREE,
  KEY `nodeId` (`node_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_node`
--

LOCK TABLES `role_node` WRITE;
/*!40000 ALTER TABLE `role_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_struc`
--

DROP TABLE IF EXISTS `role_struc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_struc` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `roleName` char(100) NOT NULL COMMENT '角色',
  `categoryId` int(11) NOT NULL COMMENT '顶级类别id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否使用：0=>不使用 1=>使用',
  `isSupper` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=>非超管创建 1=>超管创建',
  `categorys` text COMMENT '类别[json] : array(L1=>array(),L2=>array(), ..... L9=>array(24,34) )',
  `createUid` int(11) NOT NULL COMMENT '创建人',
  `ctm` int(11) NOT NULL COMMENT '创建时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_struc`
--

LOCK TABLES `role_struc` WRITE;
/*!40000 ALTER TABLE `role_struc` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_struc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_user`
--

DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_user` (
  `role_id` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `user_id` char(32) NOT NULL DEFAULT '',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`role_id`,`user_id`),
  KEY `group_id` (`role_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_user`
--

LOCK TABLES `role_user` WRITE;
/*!40000 ALTER TABLE `role_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `send_message`
--

DROP TABLE IF EXISTS `send_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `send_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(200) DEFAULT NULL COMMENT '信息内容',
  `mobile` text COMMENT '手机号 可能是多个手机号',
  `status` varchar(2) DEFAULT '' COMMENT '发送状态 0=>成功 -1=>失败',
  `uid` int(11) DEFAULT '0' COMMENT '用户id',
  `messagenum` tinyint(3) DEFAULT '0' COMMENT '短信条数',
  `created_at` int(11) DEFAULT NULL COMMENT '发送时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `send_message`
--

LOCK TABLES `send_message` WRITE;
/*!40000 ALTER TABLE `send_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `send_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensitive_words`
--

DROP TABLE IF EXISTS `sensitive_words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensitive_words` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '敏感词过滤表',
  `word` varchar(255) NOT NULL DEFAULT '' COMMENT '敏感词',
  `createTime` int(11) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensitive_words`
--

LOCK TABLES `sensitive_words` WRITE;
/*!40000 ALTER TABLE `sensitive_words` DISABLE KEYS */;
INSERT INTO `sensitive_words` VALUES (4,'滚蛋',1472647213),(9,'爱情',1472653627),(10,'伤心',1472653636),(11,'贱货',1472653694),(12,'fuck',1472653889);
/*!40000 ALTER TABLE `sensitive_words` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session2`
--

DROP TABLE IF EXISTS `session2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session2` (
  `session_id` varchar(255) NOT NULL,
  `session_value` text NOT NULL,
  `session_time` int(11) NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`session_id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session2`
--

LOCK TABLES `session2` WRITE;
/*!40000 ALTER TABLE `session2` DISABLE KEYS */;
/*!40000 ALTER TABLE `session2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '系统设置ID',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '系统设置名',
  `value` longblob COMMENT '系统设置值',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `name_webCode` (`name`,`webCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setting`
--

LOCK TABLES `setting` WRITE;
/*!40000 ALTER TABLE `setting` DISABLE KEYS */;
INSERT INTO `setting` VALUES (1,'_app_last_check','i:1469106704;','center','-'),(28,'site','a:22:{s:4:\"name\";s:30:\"瑞德口袋云课实验系统\";s:6:\"slogan\";s:30:\"瑞德口袋云课实验系统\";s:3:\"url\";s:14:\"http://tlc.com\";s:11:\"school_name\";s:12:\"瑞德云网\";s:19:\"school_english_name\";s:9:\"red cloud\";s:4:\"logo\";s:34:\"myfiles/xitong/logo_1496248037.png\";s:8:\"themeCfg\";a:4:{s:16:\"THEME_ICON_COLOR\";s:6:\"33ffdd\";s:17:\"THEME_FRONT_COLOR\";s:7:\"#ea4335\";s:16:\"THEME_BACK_COLOR\";s:7:\"#ea4335\";s:20:\"THEME_NAV_BACK_COLOR\";s:7:\"#ea4335\";}s:7:\"favicon\";s:35:\"files/system/favicon_1469184392.ico\";s:12:\"seo_keywords\";s:0:\"\";s:15:\"seo_description\";s:0:\"\";s:12:\"master_email\";s:16:\"326728721@qq.com\";s:9:\"copyright\";s:0:\"\";s:18:\"friend_link_enable\";s:1:\"0\";s:11:\"friend_link\";a:0:{}s:15:\"about_us_enable\";s:1:\"0\";s:14:\"about_us_title\";s:0:\"\";s:3:\"icp\";s:65:\"北京瑞德云网科技有限公司（c）2017 京ICP备17016632\";s:7:\"contact\";s:0:\"\";s:9:\"analytics\";s:0:\"\";s:6:\"status\";s:4:\"open\";s:11:\"closed_note\";s:0:\"\";s:11:\"_csrf_token\";s:40:\"c13a4e0d743cbb15d34869e6a60f63f22b075731\";}','tlc','-'),(29,'course','a:0:{}','tlc','-');
/*!40000 ALTER TABLE `setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sn`
--

DROP TABLE IF EXISTS `sn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sn`
--

LOCK TABLES `sn` WRITE;
/*!40000 ALTER TABLE `sn` DISABLE KEYS */;
INSERT INTO `sn` VALUES (1,'MjAxNzAzMTUyODAwMTUK');
/*!40000 ALTER TABLE `sn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL COMMENT '动态发布的人',
  `type` varchar(64) NOT NULL COMMENT '动态类型',
  `objectType` varchar(64) NOT NULL DEFAULT '' COMMENT '动态对象的类型',
  `objectId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '动态对象ID',
  `message` text NOT NULL COMMENT '动态的消息体',
  `properties` text NOT NULL COMMENT '动态的属性',
  `commentNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `likeNum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被赞的数量',
  `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '动态发布时间',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`) USING BTREE,
  KEY `createdTime` (`createdTime`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=612 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (31,1,'start_learn_lesson','lesson',3,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":\"0.00\"},\"lesson\":{\"id\":\"3\",\"number\":\"2\",\"type\":\"document\",\"title\":\"word\",\"summary\":\"\"}}',0,0,1461306033),(32,1,'start_learn_lesson','lesson',2,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":\"0.00\"},\"lesson\":{\"id\":\"2\",\"number\":\"1\",\"type\":\"video\",\"title\":\"\\u6d4b\\u8bd5\\u89c6\\u9891\",\"summary\":\"\"}}',0,0,1461306134),(33,1,'start_learn_lesson','lesson',4,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":\"0.00\"},\"lesson\":{\"id\":\"4\",\"number\":\"3\",\"type\":\"text\",\"title\":\"aaqewrqwer\",\"summary\":\"sdafsafd\"}}',0,0,1461306157),(42,4,'finished_testpaper','testpaper',1,'','{\"testpaper\":{\"id\":\"1\",\"name\":\"\\u7b2c\\u4e00\\u8282\\u7ec3\\u4e60\",\"description\":\"\",\"score\":\"6.0\",\"passedScore\":\"0.0\",\"itemCount\":\"3\"},\"result\":{\"id\":\"1\",\"score\":\"0.0\",\"objectiveScore\":\"0.0\",\"subjectiveScore\":\"0.0\",\"teacherSay\":\"\",\"passedStatus\":\"none\"}}',0,0,1465714169),(43,4,'learned_lesson','lesson',6,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\",\"picture\":\"\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\",\"price\":\"0.00\"},\"lesson\":{\"id\":\"6\",\"number\":\"2\",\"type\":\"practice\",\"title\":\"\\u7b2c\\u4e00\\u8282\\u7ec3\\u4e60\",\"summary\":\"\"}}',0,0,1465714169),(60,4,'become_student','course',4,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":\"0.00\"}}',0,0,1467707540),(61,4,'become_student','course',5,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\",\"picture\":\"\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\",\"price\":\"0.00\"}}',0,0,1467707854),(65,4,'start_learn_lesson','lesson',5,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\",\"picture\":\"\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\",\"price\":\"0.00\"},\"lesson\":{\"id\":\"5\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u5386\\u53f2\\u5185\\u6db5\",\"summary\":\"\"}}',0,0,1467789826),(68,4,'start_learn_lesson','lesson',6,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\",\"picture\":\"\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\",\"price\":\"0.00\"},\"lesson\":{\"id\":\"6\",\"number\":\"2\",\"type\":\"practice\",\"title\":\"\\u7b2c\\u4e00\\u8282\\u7ec3\\u4e60\",\"summary\":\"\"}}',0,0,1467790376),(70,4,'start_learn_lesson','lesson',3,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":\"0.00\"},\"lesson\":{\"id\":\"3\",\"number\":\"2\",\"type\":\"document\",\"title\":\"word\",\"summary\":\"\"}}',0,0,1467902400),(74,700,'become_student','course',5,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\",\"picture\":\"\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\",\"price\":\"0.00\"}}',0,0,1467910700),(76,700,'start_learn_lesson','lesson',5,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\",\"picture\":\"\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\",\"price\":\"0.00\"},\"lesson\":{\"id\":\"5\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u5386\\u53f2\\u5185\\u6db5\",\"summary\":\"\"}}',0,0,1467951371),(77,700,'start_learn_lesson','lesson',6,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\",\"picture\":\"\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\",\"price\":\"0.00\"},\"lesson\":{\"id\":\"6\",\"number\":\"2\",\"type\":\"practice\",\"title\":\"\\u7b2c\\u4e00\\u8282\\u7ec3\\u4e60\",\"summary\":\"\"}}',0,0,1467951442),(78,16,'favorite_course','course',5,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\",\"picture\":\"\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\",\"price\":\"0.00\"}}',0,0,1468905316),(79,16,'favorite_course','course',8,'','{\"course\":{\"id\":\"8\",\"title\":\"\\u4eba\\u5de5\\u667a\\u80fd\\u4e0e\\u5927\\u6570\\u636e\",\"picture\":1,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u4eba\\u5de5\\u667a\\u80fd\\u4e0e\\u5927\\u6570\\u636e\\uff0c\\u4f60\\u503c\\u5f97\\u62e5\\u6709\",\"price\":\"0.00\"}}',0,0,1469195105),(80,16,'favorite_course','course',4,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":\"0.00\"}}',0,0,1469332718),(87,16,'start_learn_lesson','lesson',7,'','{\"course\":{\"id\":\"9\",\"title\":\"Python\\u5165\\u95e8\\u57fa\\u7840\",\"picture\":\"public:\\/\\/course\\/2016\\/07-21\\/194254e23668467309.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"Python\\u4ece\\u5165\\u95e8\\u5230\\u8f6c\\u884c\",\"price\":null},\"lesson\":{\"id\":\"7\",\"number\":\"1\",\"type\":\"document\",\"title\":\"Python\\u5165\\u95e8\\u6458\\u8981\",\"summary\":\"Python\\u524d\\u8a00\\u6458\\u8981\\u89e3\\u6790\"}}',0,0,1470227330),(101,16,'start_learn_lesson','lesson',10,'','{\"course\":{\"id\":\"8\",\"title\":\"\\u4eba\\u5de5\\u667a\\u80fd\\u4e0e\\u5927\\u6570\\u636e\",\"picture\":1,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u4eba\\u5de5\\u667a\\u80fd\\u4e0e\\u5927\\u6570\\u636e\\uff0c\\u4f60\\u503c\\u5f97\\u62e5\\u6709\",\"price\":null},\"lesson\":{\"id\":\"10\",\"number\":\"1\",\"type\":\"video\",\"title\":\"\\u7b2c\\u4e00\\u8bfe \\u2014\\u2014 \\u91ce\\u751f\\u52a8\\u7269\\u8d4f\\u6790\",\"summary\":\"\\u4eba\\u5de5\\u667a\\u80fd\\u4e0e\\u5927\\u6570\\u636e\\u7b2c\\u4e00\\u8bfe\\uff0c\\u91ce\\u751f\\u52a8\\u7269\\u8d4f\\u6790\"}}',0,0,1470231192),(103,16,'start_learn_lesson','lesson',11,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"11\",\"number\":\"1\",\"type\":\"video\",\"title\":\"MP4\\u6587\\u4ef6\\u64ad\\u653e\\u6d4b\\u8bd5\",\"summary\":\"\\u73b0\\u5728\\u6d4b\\u8bd5MP4\\u6587\\u4ef6\\u7684\\u64ad\\u653e\\u529f\\u80fd\\uff01\"}}',0,0,1470231898),(104,16,'start_learn_lesson','lesson',2,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":null},\"lesson\":{\"id\":\"2\",\"number\":\"1\",\"type\":\"video\",\"title\":\"\\u6d4b\\u8bd5\\u89c6\\u9891\",\"summary\":\"\"}}',0,0,1470232962),(106,16,'start_learn_lesson','lesson',4,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":null},\"lesson\":{\"id\":\"4\",\"number\":\"3\",\"type\":\"text\",\"title\":\"aaqewrqwer\",\"summary\":\"sdafsafd\"}}',0,0,1470232981),(116,12,'finished_testpaper','testpaper',4,'','{\"testpaper\":{\"id\":\"4\",\"name\":\"\\u8003\\u9a8c\\u5927\\u5bb6\\u7684\\u65f6\\u5019\\u5230\\u4e86\",\"description\":\"\",\"score\":\"0.0\",\"passedScore\":\"0.0\",\"itemCount\":\"1\"},\"result\":{\"id\":\"11\",\"score\":\"0.0\",\"objectiveScore\":\"0.0\",\"subjectiveScore\":\"0.0\",\"teacherSay\":\"\",\"passedStatus\":\"none\"}}',0,0,1470468403),(117,12,'learned_lesson','lesson',13,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"13\",\"number\":\"2\",\"type\":\"practice\",\"title\":\"\\u8003\\u9a8c\\u5927\\u5bb6\\u7684\\u65f6\\u5019\\u5230\\u4e86\",\"summary\":\"\"}}',0,0,1470468403),(174,12,'learned_lesson','lesson',12,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"12\",\"number\":\"2\",\"type\":\"video\",\"title\":\"\\u4ece\\u7f51\\u76d8\\u5bfc\\u5165MP4\\u6587\\u4ef6\\u6d4b\\u8bd5\",\"summary\":\"\\u9762\\u5bf9\\u75be\\u98ce\\u5427\\uff0c\\u54c8\\u54c8\\u54c8\\u54c8\"}}',0,0,1470812605),(189,12,'start_learn_lesson','lesson',9,'','{\"course\":{\"id\":\"20\",\"title\":\"\\u4fe1\\u606f\\u5b89\\u5168\",\"picture\":1,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u4fe1\\u606f\\u5b89\\u5168\\uff0c\\u4f60\\u61c2\\u5f97\\uff01\\uff01\",\"price\":null},\"lesson\":{\"id\":\"9\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u4fe1\\u606f\\u5b89\\u5168\\u7b80\\u8981\",\"summary\":\"\\u4fe1\\u606f\\u5b89\\u5168\\u7b80\\u5355\\u6982\\u8981\"}}',0,0,1470909252),(190,16,'start_learn_lesson','lesson',12,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"12\",\"number\":\"2\",\"type\":\"video\",\"title\":\"\\u4ece\\u7f51\\u76d8\\u5bfc\\u5165MP4\\u6587\\u4ef6\\u6d4b\\u8bd5\",\"summary\":\"\\u9762\\u5bf9\\u75be\\u98ce\\u5427\\uff0c\\u54c8\\u54c8\\u54c8\\u54c8\"}}',0,0,1470921192),(191,16,'start_learn_lesson','lesson',3,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":null},\"lesson\":{\"id\":\"3\",\"number\":\"2\",\"type\":\"document\",\"title\":\"word\",\"summary\":\"\"}}',0,0,1470921325),(193,12,'start_learn_lesson','lesson',10,'','{\"course\":{\"id\":\"8\",\"title\":\"\\u4eba\\u5de5\\u667a\\u80fd\\u4e0e\\u5927\\u6570\\u636e\",\"picture\":1,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u4eba\\u5de5\\u667a\\u80fd\\u4e0e\\u5927\\u6570\\u636e\\uff0c\\u4f60\\u503c\\u5f97\\u62e5\\u6709\",\"price\":null},\"lesson\":{\"id\":\"10\",\"number\":\"1\",\"type\":\"video\",\"title\":\"\\u7b2c\\u4e00\\u8bfe \\u2014\\u2014 \\u91ce\\u751f\\u52a8\\u7269\\u8d4f\\u6790\",\"summary\":\"\\u4eba\\u5de5\\u667a\\u80fd\\u4e0e\\u5927\\u6570\\u636e\\u7b2c\\u4e00\\u8bfe\\uff0c\\u91ce\\u751f\\u52a8\\u7269\\u8d4f\\u6790\"}}',0,0,1470987003),(202,12,'start_learn_lesson','lesson',7,'','{\"course\":{\"id\":\"9\",\"title\":\"Python\\u5165\\u95e8\\u57fa\\u7840\",\"picture\":\"public:\\/\\/course\\/2016\\/07-21\\/194254e23668467309.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"Python\\u4ece\\u5165\\u95e8\\u5230\\u8f6c\\u884c\",\"price\":null},\"lesson\":{\"id\":\"7\",\"number\":\"1\",\"type\":\"document\",\"title\":\"Python\\u5165\\u95e8\\u6458\\u8981\",\"summary\":\"Python\\u524d\\u8a00\\u6458\\u8981\\u89e3\\u6790\"}}',0,0,1470988578),(206,1,'start_learn_lesson','lesson',11,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"11\",\"number\":\"1\",\"type\":\"video\",\"title\":\"MP4\\u6587\\u4ef6\\u64ad\\u653e\\u6d4b\\u8bd5\",\"summary\":\"\\u73b0\\u5728\\u6d4b\\u8bd5MP4\\u6587\\u4ef6\\u7684\\u64ad\\u653e\\u529f\\u80fd\\uff01\"}}',0,0,1470990477),(207,1,'learned_lesson','lesson',11,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"11\",\"number\":\"1\",\"type\":\"video\",\"title\":\"MP4\\u6587\\u4ef6\\u64ad\\u653e\\u6d4b\\u8bd5\",\"summary\":\"\\u73b0\\u5728\\u6d4b\\u8bd5MP4\\u6587\\u4ef6\\u7684\\u64ad\\u653e\\u529f\\u80fd\\uff01\"}}',0,0,1470990687),(209,12,'learned_lesson','lesson',17,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"17\",\"number\":\"4\",\"type\":\"video\",\"title\":\"firefox\\u4e0a\\u4f20\\u89c6\\u9891\\u548b\\u5c31\",\"summary\":\"\\u8fd9\\u4e48\\u96be\"}}',0,0,1471003530),(210,12,'start_learn_lesson','lesson',20,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"20\",\"number\":\"6\",\"type\":\"document\",\"title\":\"3333\",\"summary\":\"4444\"}}',0,0,1471009278),(211,12,'start_learn_lesson','lesson',13,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"13\",\"number\":\"3\",\"type\":\"practice\",\"title\":\"\\u8003\\u9a8c\\u5927\\u5bb6\\u7684\\u65f6\\u5019\\u5230\\u4e86\",\"summary\":\"\"}}',0,0,1471160234),(213,12,'finished_testpaper','testpaper',5,'','{\"testpaper\":{\"id\":\"5\",\"name\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edcRTSP\\u5927\\u4f5c\\u4e1a\",\"description\":\"\",\"score\":\"2.0\",\"passedScore\":null,\"itemCount\":\"1\"},\"result\":{\"id\":\"13\",\"score\":\"0.0\",\"objectiveScore\":\"0.0\",\"subjectiveScore\":\"0.0\",\"teacherSay\":\"\",\"passedStatus\":\"none\"}}',0,0,1471163259),(221,1,'finished_testpaper','testpaper',5,'','{\"testpaper\":{\"id\":\"5\",\"name\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edcRTSP\\u5927\\u4f5c\\u4e1a\",\"description\":\"\",\"score\":\"2.0\",\"passedScore\":null,\"itemCount\":\"1\"},\"result\":{\"id\":\"14\",\"score\":\"0.0\",\"objectiveScore\":\"0.0\",\"subjectiveScore\":\"0.0\",\"teacherSay\":\"\"}}',0,0,1471336458),(226,1,'finished_testpaper','testpaper',7,'','{\"testpaper\":{\"id\":\"7\",\"name\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u8bfe\\u540e\\u4f5c\\u4e1a\",\"description\":\"\",\"score\":\"0.0\",\"passedScore\":null,\"itemCount\":\"4\"},\"result\":{\"id\":\"15\",\"score\":\"0.0\",\"objectiveScore\":\"0.0\",\"subjectiveScore\":\"0.0\",\"teacherSay\":\"\"}}',0,0,1471413565),(228,1,'start_learn_lesson','lesson',26,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"26\",\"number\":\"5\",\"type\":\"practice\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u7b2c\\u4e00\\u6b21\\u7ec3\\u4e60\",\"summary\":\"\"}}',0,0,1471429783),(241,12,'start_learn_lesson','lesson',27,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"27\",\"number\":\"6\",\"type\":\"video\",\"title\":\"\\u91cd\\u8981\\u8bfe\\u7a0b\",\"summary\":\"\\u5f88\\u91cd\\u8981\"}}',0,0,1471497804),(258,1,'start_learn_lesson','lesson',23,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"23\",\"number\":\"3\",\"type\":\"video\",\"title\":\"\\u7b97\\u6cd5\\u63cf\\u8ff0\\u548c\\u7b97\\u6cd5\\u5206\\u6790\",\"summary\":\"\\u7b97\\u6cd5\\u5206\\u6790\\u662f\\u5bf9\\u4e00\\u4e2a\\u7b97\\u6cd5\\u9700\\u8981\\u591a\\u5c11\\u8ba1\\u7b97\\u65f6\\u95f4\\u548c\\u5b58\\u50a8\\u7a7a\\u95f4\\u4f5c\\u5b9a\\u91cf\\u7684\\u5206\\u6790\\u3002 \\u7b97\\u6cd5\\uff08Algorithm\\uff09\\u662f\\u89e3\\u9898\\u7684\\u6b65\\u9aa4\\uff0c\\u53ef\\u4ee5\\u628a\\u7b97\\u6cd5\\u5b9a\\u4e49\\u6210\\u89e3\\u4e00\\u786e\\u5b9a\\u7c7b\\u95ee\\u9898\\u7684\\u4efb\\u610f\\u4e00\\u79cd\\u7279\\u6b8a\\u7684\\u65b9\\u6cd5\\u3002\"}}',0,0,1471508727),(279,12,'learned_lesson','lesson',28,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"28\",\"number\":\"1\",\"type\":\"video\",\"title\":\"\\u6d4b\\u8bd5\\u4e00\\u4e2a\\u4e0d\\u80fd\\u64ad\\u7684\\u89c6\\u9891\",\"summary\":\"\"}}',0,0,1471522296),(284,12,'start_learn_lesson','lesson',11,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"11\",\"number\":\"1\",\"type\":\"video\",\"title\":\"MP4\\u6587\\u4ef6\\u64ad\\u653e\\u6d4b\\u8bd5\",\"summary\":\"\\u73b0\\u5728\\u6d4b\\u8bd5MP4\\u6587\\u4ef6\\u7684\\u64ad\\u653e\\u529f\\u80fd\\uff01\"}}',0,0,1471596139),(292,12,'learned_lesson','lesson',23,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"23\",\"number\":\"3\",\"type\":\"video\",\"title\":\"\\u7b97\\u6cd5\\u63cf\\u8ff0\\u548c\\u7b97\\u6cd5\\u5206\\u6790\",\"summary\":\"\\u7b97\\u6cd5\\u5206\\u6790\\u662f\\u5bf9\\u4e00\\u4e2a\\u7b97\\u6cd5\\u9700\\u8981\\u591a\\u5c11\\u8ba1\\u7b97\\u65f6\\u95f4\\u548c\\u5b58\\u50a8\\u7a7a\\u95f4\\u4f5c\\u5b9a\\u91cf\\u7684\\u5206\\u6790\\u3002 \\u7b97\\u6cd5\\uff08Algorithm\\uff09\\u662f\\u89e3\\u9898\\u7684\\u6b65\\u9aa4\\uff0c\\u53ef\\u4ee5\\u628a\\u7b97\\u6cd5\\u5b9a\\u4e49\\u6210\\u89e3\\u4e00\\u786e\\u5b9a\\u7c7b\\u95ee\\u9898\\u7684\\u4efb\\u610f\\u4e00\\u79cd\\u7279\\u6b8a\\u7684\\u65b9\\u6cd5\\u3002\"}}',0,0,1471596599),(338,12,'learned_lesson','lesson',22,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"22\",\"number\":\"2\",\"type\":\"video\",\"title\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u4e0e\\u8fd0\\u7b97\\u5b9e\\u73b0\",\"summary\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u7684\\u57fa\\u672c\\u76ee\\u7684\\u662f\\u5efa\\u7acb\\u6570\\u636e\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\\u5c31\\u662f\\u903b\\u8f91\\u7ed3\\u6784\\u7684\\u673a\\u5185\\u8868\\u793a\\u548c\\u6570\\u636e\\u5143\\u7d20\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\"}}',0,0,1471784288),(379,12,'start_learn_lesson','lesson',22,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"22\",\"number\":\"2\",\"type\":\"video\",\"title\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u4e0e\\u8fd0\\u7b97\\u5b9e\\u73b0\",\"summary\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u7684\\u57fa\\u672c\\u76ee\\u7684\\u662f\\u5efa\\u7acb\\u6570\\u636e\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\\u5c31\\u662f\\u903b\\u8f91\\u7ed3\\u6784\\u7684\\u673a\\u5185\\u8868\\u793a\\u548c\\u6570\\u636e\\u5143\\u7d20\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\"}}',0,0,1471786005),(382,12,'start_learn_lesson','lesson',26,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"26\",\"number\":\"6\",\"type\":\"practice\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u7b2c\\u4e00\\u6b21\\u7ec3\\u4e60\",\"summary\":\"\"}}',0,0,1471786040),(383,12,'start_learn_lesson','lesson',25,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"25\",\"number\":\"5\",\"type\":\"text\",\"title\":\"\\u6808\\u548c\\u961f\\u5217\",\"summary\":\"\\u6808\\u548c\\u961f\\u5217\\u90fd\\u662f\\u5728\\u4e00\\u4e2a\\u7279\\u5b9a\\u8303\\u56f4\\u7684\\u5b58\\u50a8\\u5355\\u5143\\u4e2d\\u5b58\\u50a8\\u7684\\u6570\\u636e\\uff0c\\u8fd9\\u4e9b\\u6570\\u636e\\u90fd\\u53ef\\u4ee5\\u91cd\\u65b0\\u88ab\\u53d6\\u51fa\\u4f7f\\u7528\\u3002\\u4e0d\\u540c\\u7684\\u662f\\uff0c\\u6808\\u5c31\\u8c61\\u4e00\\u4e2a\\u5f88\\u7a84\\u7684\\u6876\\u5148\\u5b58\\u8fdb\\u53bb\\u7684\\u6570\\u636e\\u53ea\\u80fd\\u6700\\u540e\\u624d\\u80fd\\u53d6\\u51fa\\u6765\\uff0c\\u800c\\u4e14\\u961f\\u5217\\u5219\\u4e0d\\u4e00\\u6837\\uff0c\\u5373\\u201c\\u5148\\u8fdb\\u540e\\u51fa\\u201d\\u3002\\u961f\\u5217\\u6709\\u70b9\\u8c61\\u65e5\\u5e38\\u6392\\u961f\\u4e70\\u4e1c\\u897f...\"}}',0,0,1471786042),(385,12,'start_learn_lesson','lesson',24,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"24\",\"number\":\"4\",\"type\":\"document\",\"title\":\"\\u7ebf\\u6027\\u8868\\u57fa\\u672c\\u6982\\u5ff5\",\"summary\":\"\\u7ebf\\u6027\\u8868\\u662f\\u6570\\u636e\\u7ed3\\u6784\\u4e2d\\u6700\\u7b80\\u5355\\u3001\\u6700\\u91cd\\u8981\\u7684\\u7ed3\\u6784\\u5f62\\u5f0f\\u4e4b\\u4e00\\uff0c\\u662f\\u6700\\u7ecf\\u5e38\\u9047\\u5230\\u7684\\u4e00\\u79cd\\u64cd\\u4f5c\\u5bf9\\u8c61\\uff0c\\u5728\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\u8bed\\u8a00\\u548c\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\u4e2d\\u5e7f\\u6cdb\\u4f7f\\u7528\\u3002\\u672c\\u7ae0\\u5c06\\u7cfb\\u7edf\\u5730\\u8ba8\\u8bba\\u5176\\u5b58\\u50a8\\u3001\\u8fd0\\u7b97\\u53ca\\u5e94\\u7528\\u3002\"}}',0,0,1471786150),(388,12,'start_learn_lesson','lesson',23,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"23\",\"number\":\"3\",\"type\":\"video\",\"title\":\"\\u7b97\\u6cd5\\u63cf\\u8ff0\\u548c\\u7b97\\u6cd5\\u5206\\u6790\",\"summary\":\"\\u7b97\\u6cd5\\u5206\\u6790\\u662f\\u5bf9\\u4e00\\u4e2a\\u7b97\\u6cd5\\u9700\\u8981\\u591a\\u5c11\\u8ba1\\u7b97\\u65f6\\u95f4\\u548c\\u5b58\\u50a8\\u7a7a\\u95f4\\u4f5c\\u5b9a\\u91cf\\u7684\\u5206\\u6790\\u3002 \\u7b97\\u6cd5\\uff08Algorithm\\uff09\\u662f\\u89e3\\u9898\\u7684\\u6b65\\u9aa4\\uff0c\\u53ef\\u4ee5\\u628a\\u7b97\\u6cd5\\u5b9a\\u4e49\\u6210\\u89e3\\u4e00\\u786e\\u5b9a\\u7c7b\\u95ee\\u9898\\u7684\\u4efb\\u610f\\u4e00\\u79cd\\u7279\\u6b8a\\u7684\\u65b9\\u6cd5\\u3002\"}}',0,0,1471786168),(389,12,'start_learn_lesson','lesson',12,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"12\",\"number\":\"2\",\"type\":\"video\",\"title\":\"\\u4ece\\u7f51\\u76d8\\u5bfc\\u5165MP4\\u6587\\u4ef6\\u6d4b\\u8bd5\",\"summary\":\"\\u9762\\u5bf9\\u75be\\u98ce\\u5427\\uff0c\\u54c8\\u54c8\\u54c8\\u54c8\"}}',0,0,1471786183),(391,12,'start_learn_lesson','lesson',17,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"17\",\"number\":\"4\",\"type\":\"video\",\"title\":\"firefox\\u4e0a\\u4f20\\u89c6\\u9891\\u548b\\u5c31\",\"summary\":\"\\u8fd9\\u4e48\\u96be\"}}',0,0,1471786377),(397,12,'start_learn_lesson','lesson',21,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":11,\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"21\",\"number\":\"7\",\"type\":\"video\",\"title\":\"TCP\\/IP\\u534f\\u8bae\",\"summary\":\"TCP\\/IP\\u534f\\u8bae\"}}',0,0,1471786625),(398,12,'start_learn_lesson','lesson',33,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"33\",\"number\":\"7\",\"type\":\"document\",\"title\":\"\\u7b2c\\u4e8c\\u7ae0\\u7684\\u6587\\u6863\\u3002\\u3002\\u3002\",\"summary\":\"\\u7b2c\\u4e8c\\u7ae0\\u7684\\u6587\\u6863\\u3002\\u3002\\u3002\"}}',0,0,1471852352),(409,12,'start_learn_lesson','lesson',34,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"34\",\"number\":\"8\",\"type\":\"video\",\"title\":\"\\u6d4b\\u8bd5\\u89c6\\u9891\\u5220\\u9664\",\"summary\":\"\\u6d4b\\u8bd5\\u5220\\u9664\"}}',0,0,1471854662),(410,12,'start_learn_lesson','lesson',28,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"28\",\"number\":\"1\",\"type\":\"video\",\"title\":\"\\u6d4b\\u8bd5\\u4e00\\u4e2a\\u4e0d\\u80fd\\u64ad\\u7684\\u89c6\\u9891\",\"summary\":\"\"}}',0,0,1471931086),(411,1,'start_learn_lesson','lesson',18,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\\u4fee\\u6539\\u6d4b\\u8bd5\",\"picture\":\"\\/Public\\/files\\/course\\/poster\\/course-informationsecurity.png\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u6211\\u6b63\\u5728\\u6d4b\\u8bd5\\u8bfe\\u7a0b\\u7684\\u4fee\\u6539\",\"price\":null},\"lesson\":{\"id\":\"18\",\"number\":\"4\",\"type\":\"video\",\"title\":\"\\u4e0a\\u5929\\u5566\",\"summary\":\"\\u5566\\u5566\"}}',0,0,1471932959),(412,1,'finished_testpaper','testpaper',2,'','{\"testpaper\":{\"id\":\"2\",\"name\":\"\\u8bfe\\u540e\\u7ec3\\u4e60\",\"description\":\"\",\"score\":\"6.0\",\"passedScore\":null,\"itemCount\":\"3\"},\"result\":{\"id\":\"18\",\"score\":\"0.0\",\"objectiveScore\":\"0.0\",\"subjectiveScore\":\"0.0\",\"teacherSay\":\"\"}}',0,0,1471933468),(416,20,'start_learn_lesson','lesson',22,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"22\",\"number\":\"2\",\"type\":\"video\",\"title\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u4e0e\\u8fd0\\u7b97\\u5b9e\\u73b0\",\"summary\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u7684\\u57fa\\u672c\\u76ee\\u7684\\u662f\\u5efa\\u7acb\\u6570\\u636e\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\\u5c31\\u662f\\u903b\\u8f91\\u7ed3\\u6784\\u7684\\u673a\\u5185\\u8868\\u793a\\u548c\\u6570\\u636e\\u5143\\u7d20\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\"}}',0,0,1471953981),(417,20,'learned_lesson','lesson',22,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"22\",\"number\":\"2\",\"type\":\"video\",\"title\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u4e0e\\u8fd0\\u7b97\\u5b9e\\u73b0\",\"summary\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u7684\\u57fa\\u672c\\u76ee\\u7684\\u662f\\u5efa\\u7acb\\u6570\\u636e\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\\u5c31\\u662f\\u903b\\u8f91\\u7ed3\\u6784\\u7684\\u673a\\u5185\\u8868\\u793a\\u548c\\u6570\\u636e\\u5143\\u7d20\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\"}}',0,0,1471953987),(418,1,'favorite_course','course',20,'','{\"course\":{\"id\":\"20\",\"title\":\"\\u4fe1\\u606f\\u5b89\\u5168\",\"picture\":\"\\/Public\\/files\\/course\\/poster\\/course-openstack.png\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u4fe1\\u606f\\u5b89\\u5168\\uff0c\\u4f60\\u61c2\\u5f97\\uff01\\uff01\",\"price\":null}}',0,0,1472040748),(419,12,'start_learn_lesson','lesson',29,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":\"\\/Public\\/files\\/course\\/poster\\/course-linux.png\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"29\",\"number\":\"5\",\"type\":\"video\",\"title\":\"\\u4e0a\\u4f20\\u4e2a\\u7a0d\\u5fae\\u5927\\u4e00\\u70b9\\u7684\\u89c6\\u9891\",\"summary\":\"\\u4e0a\\u4f20\\u4e2a\\u7a0d\\u5fae\\u5927\\u4e00\\u70b9\\u7684\\u89c6\\u9891\"}}',0,0,1472046674),(423,5,'start_learn_lesson','lesson',41,'','{\"course\":{\"id\":\"20\",\"title\":\"\\u4fe1\\u606f\\u5b89\\u5168\",\"picture\":\"\\/Public\\/files\\/course\\/poster\\/course-openstack.png\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u4fe1\\u606f\\u5b89\\u5168\\uff0c\\u4f60\\u61c2\\u5f97\\uff01\\uff01\",\"price\":null},\"lesson\":{\"id\":\"41\",\"number\":\"2\",\"type\":\"video\",\"title\":\"\\u6d4b\\u8bd5mov\\u89c6\\u9891\\u64ad\\u653e\",\"summary\":\"\\u4e00\\u5b9a\\u8981\\u6210\\u529f\\u554a\"}}',0,0,1473677908),(425,230,'start_learn_lesson','lesson',9,'','{\"course\":{\"id\":\"20\",\"title\":\"\\u4fe1\\u606f\\u5b89\\u5168\",\"picture\":\"\\/Public\\/files\\/course\\/poster\\/course-openstack.png\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u4fe1\\u606f\\u5b89\\u5168\\uff0c\\u4f60\\u61c2\\u5f97\\uff01\\uff01\",\"price\":null},\"lesson\":{\"id\":\"9\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u4fe1\\u606f\\u5b89\\u5168\\u7b80\\u8981\",\"summary\":\"\\u4fe1\\u606f\\u5b89\\u5168\\u7b80\\u5355\\u6982\\u8981\"}}',0,0,1473762544),(427,230,'start_learn_lesson','lesson',42,'','{\"course\":{\"id\":\"22\",\"title\":\"\\u6570\\u636e\\u5e93\\u539f\\u7406\",\"picture\":\"\\/Public\\/files\\/course\\/poster\\/course-docker.png\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u6570\\u636e\\u5e93\\u539f\\u7406\\u7b80\\u4ecb\",\"price\":null},\"lesson\":{\"id\":\"42\",\"number\":\"1\",\"type\":\"video\",\"title\":\"PBSG_DNS.webm\",\"summary\":\"PBSG_DNS.webm\"}}',0,0,1473764249),(429,5,'start_learn_lesson','lesson',14,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\\u4fee\\u6539\\u6d4b\\u8bd5\",\"picture\":\"\\/Public\\/files\\/course\\/poster\\/course-informationsecurity.png\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u6211\\u6b63\\u5728\\u6d4b\\u8bd5\\u8bfe\\u7a0b\\u7684\\u4fee\\u6539\",\"price\":null},\"lesson\":{\"id\":\"14\",\"number\":\"5\",\"type\":\"text\",\"title\":\"\\u9a6c\\u514b\\u601d\\u4e3b\\u4e49\\u57fa\\u672c\\u539f\\u7406\",\"summary\":\"\\u4e2d\\u56fd\\u7279\\u8272\\u793e\\u4f1a\\u4e3b\\u4e49\"}}',0,0,1473848027),(430,5,'start_learn_lesson','lesson',5,'','{\"course\":{\"id\":\"5\",\"title\":\"\\u6bdb\\u6cfd\\u4e1c\\u6982\\u8bba\\u4fee\\u6539\\u6d4b\\u8bd5\",\"picture\":\"\\/Public\\/files\\/course\\/poster\\/course-informationsecurity.png\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u6211\\u6b63\\u5728\\u6d4b\\u8bd5\\u8bfe\\u7a0b\\u7684\\u4fee\\u6539\",\"price\":null},\"lesson\":{\"id\":\"5\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u5386\\u53f2\\u5185\\u6db5\",\"summary\":\"\"}}',0,0,1473848035),(431,1,'start_learn_lesson','lesson',43,'','{\"course\":{\"id\":\"4\",\"title\":\"\\u90e8\\u7f72\\u6d4b\\u8bd54\",\"picture\":\"public:\\/\\/course\\/2016\\/04-22\\/1321382e5a46796991.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"asdfasdf\",\"price\":null},\"lesson\":{\"id\":\"43\",\"number\":\"4\",\"type\":\"video\",\"title\":\"Firefox\\u4e0a\\u4f20\\uff0c\\u6765\\u5427\\uff01\",\"summary\":\"Firefox\\u4e0a\\u4f20\\uff0c\\u6765\\u5427\\uff01\"}}',0,0,1474346794),(433,1,'start_learn_lesson','lesson',28,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"28\",\"number\":\"6\",\"type\":\"video\",\"title\":\"\\u6d4b\\u8bd5\\u4e00\\u4e2a\\u4e0d\\u80fd\\u64ad\\u7684\\u89c6\\u9891\",\"summary\":\"\"}}',0,0,1474546926),(434,1,'start_learn_lesson','lesson',24,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"24\",\"number\":\"3\",\"type\":\"document\",\"title\":\"\\u7ebf\\u6027\\u8868\\u57fa\\u672c\\u6982\\u5ff5\",\"summary\":\"\\u7ebf\\u6027\\u8868\\u662f\\u6570\\u636e\\u7ed3\\u6784\\u4e2d\\u6700\\u7b80\\u5355\\u3001\\u6700\\u91cd\\u8981\\u7684\\u7ed3\\u6784\\u5f62\\u5f0f\\u4e4b\\u4e00\\uff0c\\u662f\\u6700\\u7ecf\\u5e38\\u9047\\u5230\\u7684\\u4e00\\u79cd\\u64cd\\u4f5c\\u5bf9\\u8c61\\uff0c\\u5728\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\u8bed\\u8a00\\u548c\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\u4e2d\\u5e7f\\u6cdb\\u4f7f\\u7528\\u3002\\u672c\\u7ae0\\u5c06\\u7cfb\\u7edf\\u5730\\u8ba8\\u8bba\\u5176\\u5b58\\u50a8\\u3001\\u8fd0\\u7b97\\u53ca\\u5e94\\u7528\\u3002\"}}',0,0,1474546933),(435,230,'start_learn_lesson','lesson',48,'','{\"course\":{\"id\":\"23\",\"title\":\"\\u6211\\u548c\\u6797\\u536b\\u56fd\\u8001\\u5e08\\u4e00\\u8d77\\u4e0a\\u7684\\u8bfe\",\"picture\":\"public:\\/\\/course\\/2016\\/10-09\\/161419b99138501082.jpg\",\"type\":\"normal\",\"rating\":\"0\",\"about\":\"\\u8fd9\\u95e8\\u8bfe\\u6709\\u4e09\\u4e2a\\u8001\\u5e08\\u4e00\\u8d77\\u4e0a\\uff0c\\u675c\\u6000\\u660c\\u8001\\u5e08\\u9080\\u8bf7\\u4e86\\u9c8d\\u5357\\u6d77\\u8001\\u5e08\\u548c\\u6797\\u536b\\u56fd\\u8001\\u5e08\\u3002\",\"price\":null},\"lesson\":{\"id\":\"48\",\"number\":\"1\",\"type\":\"video\",\"title\":\"\\u5bfc\\u5b66\\u89c6\\u9891\",\"summary\":\"\\u5bfc\\u5b66\\u89c6\\u9891\"}}',0,0,1476194578),(436,230,'start_learn_lesson','lesson',49,'','{\"course\":{\"id\":\"24\",\"title\":\"\\u5927\\u6570\\u636e\",\"picture\":\"public:\\/\\/course\\/2016\\/10-22\\/100834208ca0562705.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"49\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u7b2c\\u4e00\\u7ae0\\u6982\\u8981\",\"summary\":\"\\u7b2c\\u4e00\\u7ae0\\u6982\\u8981\"}}',0,0,1477102213),(437,1,'start_learn_lesson','lesson',22,'','{\"course\":{\"id\":\"21\",\"title\":\"\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\",\"picture\":\"public:\\/\\/course\\/2016\\/08-17\\/1132088b7980679936.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u300a\\u6570\\u636e\\u7ed3\\u6784\\u4e0e\\u7b97\\u6cd5\\u300b\\u662f2013\\u5e74\\u4eba\\u6c11\\u90ae\\u7535\\u51fa\\u7248\\u793e\\u51fa\\u7248\\u7684\\u56fe\\u4e66\\uff0c\\u4f5c\\u8005\\u662f\\u5f6d\\u519b\\u3001\\u5411\\u6bc5\\u3002\\u8be5\\u4e66\\u662f\\u56fd\\u5bb6\\u7ea7\\u53cc\\u8bed\\u6559\\u5b66\\u793a\\u8303\\u8bfe\\u7a0b\\u914d\\u5957\\u6559\\u6750\\uff0c\\u4ee5\\u57fa\\u672c\\u6570\\u636e\\u7ed3\\u6784\\u548c\\u7b97\\u6cd5\\u8bbe\\u8ba1\\u7b56\\u7565\\u4e3a\\u77e5\\u8bc6\\u5355\\u5143\\uff0c\\u7cfb\\u7edf\\u5730\\u4ecb\\u7ecd\\u4e86\\u6570\\u636e\\u7ed3\\u6784\\u7684\\u77e5\\u8bc6\\u4e0e\\u5e94\\u7528\\u3001\\u8ba1\\u7b97\\u673a\\u7b97\\u6cd5\\u7684\\u8bbe...\",\"price\":null},\"lesson\":{\"id\":\"22\",\"number\":\"1\",\"type\":\"video\",\"title\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u4e0e\\u8fd0\\u7b97\\u5b9e\\u73b0\",\"summary\":\"\\u5b58\\u50a8\\u5b9e\\u73b0\\u7684\\u57fa\\u672c\\u76ee\\u7684\\u662f\\u5efa\\u7acb\\u6570\\u636e\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\\u5c31\\u662f\\u903b\\u8f91\\u7ed3\\u6784\\u7684\\u673a\\u5185\\u8868\\u793a\\u548c\\u6570\\u636e\\u5143\\u7d20\\u7684\\u673a\\u5185\\u8868\\u793a\\u3002\"}}',0,0,1484033321),(438,288,'favorite_course','course',20,'','{\"course\":{\"id\":\"20\",\"title\":\"\\u4fe1\\u606f\\u5b89\\u5168\",\"picture\":\"\\/myfiles\\/course\\/poster\\/course-openstack.png\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u4fe1\\u606f\\u5b89\\u5168\\uff0c\\u4f60\\u61c2\\u5f97\\uff01\\uff01\",\"price\":null}}',0,0,1490534323),(441,288,'start_learn_lesson','lesson',29,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":\"\\/myfiles\\/course\\/poster\\/course-linux.png\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"29\",\"number\":\"5\",\"type\":\"video\",\"title\":\"\\u4e0a\\u4f20\\u4e2a\\u7a0d\\u5fae\\u5927\\u4e00\\u70b9\\u7684\\u89c6\\u9891\",\"summary\":\"\\u4e0a\\u4f20\\u4e2a\\u7a0d\\u5fae\\u5927\\u4e00\\u70b9\\u7684\\u89c6\\u9891\"}}',0,0,1490534495),(443,288,'start_learn_lesson','lesson',12,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":\"\\/myfiles\\/course\\/poster\\/course-linux.png\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"12\",\"number\":\"2\",\"type\":\"video\",\"title\":\"\\u4ece\\u7f51\\u76d8\\u5bfc\\u5165MP4\\u6587\\u4ef6\\u6d4b\\u8bd5\",\"summary\":\"\\u9762\\u5bf9\\u75be\\u98ce\\u5427\\uff0c\\u54c8\\u54c8\\u54c8\\u54c8\"}}',0,0,1490535272),(444,288,'start_learn_lesson','lesson',13,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\",\"picture\":\"\\/myfiles\\/course\\/poster\\/course-linux.png\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u8ba1\\u7b97\\u673a\\u7f51\\u7edc\\u7a0b\\u5e8f\\u8bbe\\u8ba1\\uff0c\\u91c7\\u7528C\",\"price\":null},\"lesson\":{\"id\":\"13\",\"number\":\"3\",\"type\":\"practice\",\"title\":\"\\u8003\\u9a8c\\u5927\\u5bb6\\u7684\\u65f6\\u5019\\u5230\\u4e86\",\"summary\":\"\"}}',0,0,1490535619),(467,3,'start_learn_lesson','lesson',1,'','{\"course\":{\"id\":\"1\",\"title\":\"1\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"1\",\"number\":\"1\",\"type\":\"text\",\"title\":\"111\",\"summary\":\"111\"}}',0,0,1491711673),(480,3,'start_learn_lesson','lesson',2,'','{\"course\":{\"id\":\"3\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"2\",\"number\":\"1\",\"type\":\"video\",\"title\":\"Hadoop\\u6982\\u8ff0\",\"summary\":\"\"}}',0,0,1494337942),(482,3,'start_learn_lesson','lesson',3,'','{\"course\":{\"id\":\"3\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"3\",\"number\":\"1\",\"type\":\"video\",\"title\":\"Hadoop\\u6982\\u8ff0\",\"summary\":\"\"}}',0,0,1494338878),(483,3,'start_learn_lesson','lesson',4,'','{\"course\":{\"id\":\"3\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"4\",\"number\":\"1\",\"type\":\"document\",\"title\":\"Hadoop\\u5b89\\u88c5\\u914d\\u7f6e\",\"summary\":\"\"}}',0,0,1494338923),(493,3,'start_learn_lesson','lesson',20,'','{\"course\":{\"id\":\"2\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u672c\\u8bfe\\u7a0b\\u4e3b\\u8981\\u4ecb\\u7ecd\\u5927\\u6570\\u636e\\u57fa\\u7840\\u5165\\u95e8\\u77e5\\u8bc6\\uff0c\\u4ee5\\u53ca\\u76f8\\u5173\\u57fa\\u7840\\u5b9e\\u9a8c\\u73af\\u5883\\u642d\\u5efa\\u4e0e\\u4f7f\\u7528\\u3002\",\"price\":null},\"lesson\":{\"id\":\"20\",\"number\":\"11\",\"type\":\"document\",\"title\":\"Hadoop\\u5b89\\u88c5\\u914d\\u7f6e\",\"summary\":\"\"}}',0,0,1495494407),(503,3,'start_learn_lesson','lesson',21,'','{\"course\":{\"id\":\"2\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u672c\\u8bfe\\u7a0b\\u4e3b\\u8981\\u4ecb\\u7ecd\\u5927\\u6570\\u636e\\u57fa\\u7840\\u5165\\u95e8\\u77e5\\u8bc6\\uff0c\\u4ee5\\u53ca\\u76f8\\u5173\\u57fa\\u7840\\u5b9e\\u9a8c\\u73af\\u5883\\u642d\\u5efa\\u4e0e\\u4f7f\\u7528\\u3002\",\"price\":null},\"lesson\":{\"id\":\"21\",\"number\":\"12\",\"type\":\"document\",\"title\":\"HDFS\\u7684\\u57fa\\u672c\\u4f7f\\u7528\",\"summary\":\"\"}}',0,0,1495542172),(506,3,'start_learn_lesson','lesson',24,'','{\"course\":{\"id\":\"2\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u672c\\u8bfe\\u7a0b\\u4e3b\\u8981\\u4ecb\\u7ecd\\u5927\\u6570\\u636e\\u57fa\\u7840\\u5165\\u95e8\\u77e5\\u8bc6\\uff0c\\u4ee5\\u53ca\\u76f8\\u5173\\u57fa\\u7840\\u5b9e\\u9a8c\\u73af\\u5883\\u642d\\u5efa\\u4e0e\\u4f7f\\u7528\\u3002\",\"price\":null},\"lesson\":{\"id\":\"24\",\"number\":\"15\",\"type\":\"document\",\"title\":\"Hive\\u73af\\u5883\\u5b89\\u88c5\",\"summary\":\"\"}}',0,0,1495543834),(507,3,'start_learn_lesson','lesson',25,'','{\"course\":{\"id\":\"2\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u672c\\u8bfe\\u7a0b\\u4e3b\\u8981\\u4ecb\\u7ecd\\u5927\\u6570\\u636e\\u57fa\\u7840\\u5165\\u95e8\\u77e5\\u8bc6\\uff0c\\u4ee5\\u53ca\\u76f8\\u5173\\u57fa\\u7840\\u5b9e\\u9a8c\\u73af\\u5883\\u642d\\u5efa\\u4e0e\\u4f7f\\u7528\\u3002\",\"price\":null},\"lesson\":{\"id\":\"25\",\"number\":\"16\",\"type\":\"document\",\"title\":\"Hive Sql\\u57fa\\u672c\\u547d\\u4ee4\",\"summary\":\"\"}}',0,0,1495543863),(510,3,'start_learn_lesson','lesson',22,'','{\"course\":{\"id\":\"2\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u672c\\u8bfe\\u7a0b\\u4e3b\\u8981\\u4ecb\\u7ecd\\u5927\\u6570\\u636e\\u57fa\\u7840\\u5165\\u95e8\\u77e5\\u8bc6\\uff0c\\u4ee5\\u53ca\\u76f8\\u5173\\u57fa\\u7840\\u5b9e\\u9a8c\\u73af\\u5883\\u642d\\u5efa\\u4e0e\\u4f7f\\u7528\\u3002\",\"price\":null},\"lesson\":{\"id\":\"22\",\"number\":\"13\",\"type\":\"document\",\"title\":\"HBase \\u73af\\u5883\\u5b89\\u88c5\\u914d\\u7f6e\",\"summary\":\"\"}}',0,0,1495545879),(514,3,'start_learn_lesson','lesson',23,'','{\"course\":{\"id\":\"2\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u672c\\u8bfe\\u7a0b\\u4e3b\\u8981\\u4ecb\\u7ecd\\u5927\\u6570\\u636e\\u57fa\\u7840\\u5165\\u95e8\\u77e5\\u8bc6\\uff0c\\u4ee5\\u53ca\\u76f8\\u5173\\u57fa\\u7840\\u5b9e\\u9a8c\\u73af\\u5883\\u642d\\u5efa\\u4e0e\\u4f7f\\u7528\\u3002\",\"price\":null},\"lesson\":{\"id\":\"23\",\"number\":\"14\",\"type\":\"document\",\"title\":\"HBase Shell\\u7684\\u57fa\\u672c\\u4f7f\\u7528\",\"summary\":\"\"}}',0,0,1495549770),(515,3,'start_learn_lesson','lesson',27,'','{\"course\":{\"id\":\"2\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u672c\\u8bfe\\u7a0b\\u4e3b\\u8981\\u4ecb\\u7ecd\\u5927\\u6570\\u636e\\u57fa\\u7840\\u5165\\u95e8\\u77e5\\u8bc6\\uff0c\\u4ee5\\u53ca\\u76f8\\u5173\\u57fa\\u7840\\u5b9e\\u9a8c\\u73af\\u5883\\u642d\\u5efa\\u4e0e\\u4f7f\\u7528\\u3002\",\"price\":null},\"lesson\":{\"id\":\"27\",\"number\":\"17\",\"type\":\"document\",\"title\":\"Hive\\u5b89\\u88c5\\u914d\\u7f6e\",\"summary\":\"\"}}',0,0,1495550889),(516,3,'start_learn_lesson','lesson',28,'','{\"course\":{\"id\":\"2\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u672c\\u8bfe\\u7a0b\\u4e3b\\u8981\\u4ecb\\u7ecd\\u5927\\u6570\\u636e\\u57fa\\u7840\\u5165\\u95e8\\u77e5\\u8bc6\\uff0c\\u4ee5\\u53ca\\u76f8\\u5173\\u57fa\\u7840\\u5b9e\\u9a8c\\u73af\\u5883\\u642d\\u5efa\\u4e0e\\u4f7f\\u7528\\u3002\",\"price\":null},\"lesson\":{\"id\":\"28\",\"number\":\"17\",\"type\":\"document\",\"title\":\"Hive Sql\\u57fa\\u672c\\u547d\\u4ee4\",\"summary\":\"\"}}',0,0,1495554177),(517,3,'start_learn_lesson','lesson',26,'','{\"course\":{\"id\":\"2\",\"title\":\"\\u5927\\u6570\\u636e\\u57fa\\u7840\\u8bfe\\u7a0b\",\"picture\":\"\",\"type\":\"normal\",\"rating\":null,\"about\":\"\\u672c\\u8bfe\\u7a0b\\u4e3b\\u8981\\u4ecb\\u7ecd\\u5927\\u6570\\u636e\\u57fa\\u7840\\u5165\\u95e8\\u77e5\\u8bc6\\uff0c\\u4ee5\\u53ca\\u76f8\\u5173\\u57fa\\u7840\\u5b9e\\u9a8c\\u73af\\u5883\\u642d\\u5efa\\u4e0e\\u4f7f\\u7528\\u3002\",\"price\":null},\"lesson\":{\"id\":\"26\",\"number\":\"17\",\"type\":\"document\",\"title\":\"Spark\\u5b89\\u88c5\\u4e0e\\u64cd\\u4f5c\",\"summary\":\"\"}}',0,0,1495579431),(522,1,'start_learn_lesson','lesson',84,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"84\",\"number\":\"2\",\"type\":\"document\",\"title\":\"Spark\\u96c6\\u7fa4\\u642d\\u5efa\",\"summary\":\"\"}}',0,0,1496245364),(528,1,'start_learn_lesson','lesson',98,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"98\",\"number\":\"11\",\"type\":\"video\",\"title\":\"Spark\\u8ba1\\u7b97\\u6a21\\u578b\",\"summary\":\"\"}}',0,0,1496245599),(529,1,'start_learn_lesson','lesson',97,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"97\",\"number\":\"10\",\"type\":\"video\",\"title\":\"Spark\\u7b80\\u4ecb\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1496245603),(530,1,'start_learn_lesson','lesson',85,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"85\",\"number\":\"3\",\"type\":\"document\",\"title\":\"Spark\\u5e94\\u7528\\u63d0\\u4ea4\\u5de5\\u5177\",\"summary\":\"\"}}',0,0,1496245652),(531,1,'start_learn_lesson','lesson',86,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"86\",\"number\":\"4\",\"type\":\"document\",\"title\":\"Scala\\u57fa\\u672c\\u77e5\\u8bc6\\u8bb2\\u89e3\",\"summary\":\"\"}}',0,0,1496245656),(533,1,'start_learn_lesson','lesson',88,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"88\",\"number\":\"6\",\"type\":\"document\",\"title\":\"RDD\\u7279\\u6027\\u3001\\u5e38\\u89c1\\u64cd\\u4f5c\\u3001\\u7f13\\u5b58\\u7b56\\u7565\",\"summary\":\"\"}}',0,0,1496245661),(534,1,'start_learn_lesson','lesson',89,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"89\",\"number\":\"7\",\"type\":\"document\",\"title\":\"RDD Dependency\\u3001Stage\",\"summary\":\"\"}}',0,0,1496245666),(535,1,'start_learn_lesson','lesson',91,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"91\",\"number\":\"9\",\"type\":\"document\",\"title\":\"Spark\\u7a0b\\u5e8f\\u5f00\\u53d1\\u5b9e\\u9a8c\\u6307\\u5357\",\"summary\":\"\"}}',0,0,1496245670),(536,1,'start_learn_lesson','lesson',90,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"90\",\"number\":\"8\",\"type\":\"document\",\"title\":\"Spark\\u6838\\u5fc3\\u7ec4\\u4ef6\\u6982\\u8ff0\\u53ca\\u6848\\u4f8b\\u5206\\u6790\",\"summary\":\"\"}}',0,0,1496245677),(537,1,'start_learn_lesson','lesson',87,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"87\",\"number\":\"5\",\"type\":\"video\",\"title\":\"Scala\\u57fa\\u672c\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1496245738),(538,1,'start_learn_lesson','lesson',75,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u540c\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/192858a6bb9b361489.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"75\",\"number\":\"6\",\"type\":\"document\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6\\uff08\\u8bb2\\u4e49\\uff09\",\"summary\":\"\"}}',0,0,1496245847),(539,1,'start_learn_lesson','lesson',32,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"32\",\"number\":\"4\",\"type\":\"video\",\"title\":\"hadoop_1\",\"summary\":\"\"}}',0,0,1496245895),(540,1,'start_learn_lesson','lesson',33,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"33\",\"number\":\"5\",\"type\":\"video\",\"title\":\"hadoop_2\",\"summary\":\"\"}}',0,0,1496245919),(541,1,'start_learn_lesson','lesson',34,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"34\",\"number\":\"6\",\"type\":\"video\",\"title\":\"hadoop_3\",\"summary\":\"\"}}',0,0,1496245932),(543,3,'start_learn_lesson','lesson',99,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"99\",\"number\":\"16\",\"type\":\"video\",\"title\":\"Hadoop\\u90e8\\u7f72\\u4e00\",\"summary\":\"\"}}',0,0,1496246170),(544,3,'start_learn_lesson','lesson',100,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"100\",\"number\":\"16\",\"type\":\"video\",\"title\":\"Hadoop\\u90e8\\u7f72\\u4e8c\",\"summary\":\"\"}}',0,0,1496246178),(545,3,'start_learn_lesson','lesson',101,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"101\",\"number\":\"16\",\"type\":\"video\",\"title\":\"Hadoop\\u90e8\\u7f72\\u4e09\",\"summary\":\"\"}}',0,0,1496246212),(546,3,'start_learn_lesson','lesson',43,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"43\",\"number\":\"15\",\"type\":\"video\",\"title\":\"HDFS\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1496246252),(547,3,'start_learn_lesson','lesson',102,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"102\",\"number\":\"17\",\"type\":\"video\",\"title\":\"HDFS\\u64cd\\u4f5c\\u89c6\\u5c4f\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1496246292),(550,3,'start_learn_lesson','lesson',56,'','{\"course\":{\"id\":\"4\",\"title\":\"HBase\\u57fa\\u7840\\u548c\\u8fdb\\u9636\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/191516420656422951.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"56\",\"number\":\"9\",\"type\":\"video\",\"title\":\"HBase\\u57fa\\u672c\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1496246316),(551,3,'start_learn_lesson','lesson',55,'','{\"course\":{\"id\":\"4\",\"title\":\"HBase\\u57fa\\u7840\\u548c\\u8fdb\\u9636\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/191516420656422951.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"55\",\"number\":\"8\",\"type\":\"video\",\"title\":\"HBase\\u73af\\u5883\\u642d\\u5efa\\u89c6\\u9891\",\"summary\":\"\"}}',0,0,1496246349),(552,3,'start_learn_lesson','lesson',103,'','{\"course\":{\"id\":\"4\",\"title\":\"HBase\\u57fa\\u7840\\u548c\\u8fdb\\u9636\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/191516420656422951.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"103\",\"number\":\"11\",\"type\":\"video\",\"title\":\"HBase\\u57fa\\u672c\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1496246392),(554,3,'start_learn_lesson','lesson',63,'','{\"course\":{\"id\":\"5\",\"title\":\"Hive\\u57fa\\u7840\\u548c\\u8fdb\\u9636\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/191732c7c5ed616441.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"63\",\"number\":\"7\",\"type\":\"video\",\"title\":\"Hive\\u73af\\u5883\\u642d\\u5efa\",\"summary\":\"\"}}',0,0,1496246445),(556,3,'start_learn_lesson','lesson',87,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"87\",\"number\":\"5\",\"type\":\"video\",\"title\":\"Scala\\u57fa\\u672c\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1496246558),(567,3,'start_learn_lesson','lesson',97,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"97\",\"number\":\"10\",\"type\":\"video\",\"title\":\"Spark\\u7b80\\u4ecb\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1496248263),(568,3,'start_learn_lesson','lesson',98,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"98\",\"number\":\"11\",\"type\":\"video\",\"title\":\"Spark\\u8ba1\\u7b97\\u6a21\\u578b\",\"summary\":\"\"}}',0,0,1496248303),(569,3,'start_learn_lesson','lesson',88,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"88\",\"number\":\"6\",\"type\":\"document\",\"title\":\"RDD\\u7279\\u6027\\u3001\\u5e38\\u89c1\\u64cd\\u4f5c\\u3001\\u7f13\\u5b58\\u7b56\\u7565\",\"summary\":\"\"}}',0,0,1496248339),(570,3,'start_learn_lesson','lesson',89,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"89\",\"number\":\"7\",\"type\":\"document\",\"title\":\"RDD Dependency\\u3001Stage\",\"summary\":\"\"}}',0,0,1496248363),(571,3,'start_learn_lesson','lesson',90,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"90\",\"number\":\"8\",\"type\":\"document\",\"title\":\"Spark\\u6838\\u5fc3\\u7ec4\\u4ef6\\u6982\\u8ff0\\u53ca\\u6848\\u4f8b\\u5206\\u6790\",\"summary\":\"\"}}',0,0,1496248404),(573,3,'start_learn_lesson','lesson',83,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"83\",\"number\":\"1\",\"type\":\"document\",\"title\":\"Spark\\u4ee5\\u53caSpark\\u751f\\u6001\\u5708\\u7684\\u4ecb\\u7ecd\",\"summary\":\"\"}}',0,0,1496248448),(575,3,'start_learn_lesson','lesson',70,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u540c\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/192858a6bb9b361489.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"70\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Sqoop \\uff08\\u4e0a\\uff09\",\"summary\":\"\"}}',0,0,1496248497),(577,3,'start_learn_lesson','lesson',75,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u540c\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/192858a6bb9b361489.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"75\",\"number\":\"6\",\"type\":\"document\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6\\uff08\\u8bb2\\u4e49\\uff09\",\"summary\":\"\"}}',0,0,1496248520),(579,3,'start_learn_lesson','lesson',73,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u540c\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/192858a6bb9b361489.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"73\",\"number\":\"4\",\"type\":\"document\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Kafka\",\"summary\":\"\"}}',0,0,1496248575),(584,3,'start_learn_lesson','lesson',104,'','{\"course\":{\"id\":\"10\",\"title\":\"OpenStack\",\"picture\":\"public:\\/\\/course\\/2017\\/06-21\\/225555b29356522658.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"104\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u4e86\\u89e3OpenStack\\u4e3b\\u8981\\u7ec4\\u4ef6\",\"summary\":\"\"}}',0,0,1498057120),(587,3,'start_learn_lesson','lesson',105,'','{\"course\":{\"id\":\"10\",\"title\":\"OpenStack\",\"picture\":\"public:\\/\\/course\\/2017\\/06-21\\/225555b29356522658.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"105\",\"number\":\"1\",\"type\":\"document\",\"title\":\"DevStack\\u90e8\\u7f72Openstack\",\"summary\":\"\\u4e3b\\u8981\\u5bf9Openstack\\u73af\\u5883\\u90e8\\u7f72\\u6709\\u4e00\\u4e2a\\u6982\\u5ff5\\u6027\\u7684\\u4e86\\u89e3\\u3002\"}}',0,0,1498083456),(589,3,'start_learn_lesson','lesson',106,'','{\"course\":{\"id\":\"10\",\"title\":\"OpenStack\",\"picture\":\"public:\\/\\/course\\/2017\\/06-21\\/225555b29356522658.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"106\",\"number\":\"3\",\"type\":\"video\",\"title\":\"DevStack \\u90e8\\u7f72OpenStack\",\"summary\":\"\"}}',0,0,1498141324),(590,3,'start_learn_lesson','lesson',91,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"91\",\"number\":\"9\",\"type\":\"document\",\"title\":\"Spark\\u7a0b\\u5e8f\\u5f00\\u53d1\\u5b9e\\u9a8c\\u6307\\u5357\",\"summary\":\"\"}}',0,0,1498187569),(591,1,'start_learn_lesson','lesson',29,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"29\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u521d\\u8bc6Hadoop \\uff08\\u4e0a\\uff09\",\"summary\":\"\"}}',0,0,1502174554),(592,1,'start_learn_lesson','lesson',45,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"45\",\"number\":\"16\",\"type\":\"document\",\"title\":\"Hadoop2.x\\u96c6\\u7fa4\\u7ef4\\u62a4\",\"summary\":\"\"}}',0,0,1502174664),(595,1,'favorite_course','course',9,'','{\"course\":{\"id\":\"9\",\"title\":\"\\u5185\\u5b58\\u8ba1\\u7b97\\u6846\\u67b6\\u6280\\u672f&Spark\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/193056066949636457.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null}}',0,0,1502174706),(599,3,'start_learn_lesson','lesson',64,'','{\"course\":{\"id\":\"5\",\"title\":\"Hive\\u57fa\\u7840\\u548c\\u8fdb\\u9636\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/191732c7c5ed616441.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"64\",\"number\":\"8\",\"type\":\"video\",\"title\":\"Hive Sql\\u57fa\\u672c\\u64cd\\u4f5c\\u89c6\\u9891\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1502889083),(601,1,'start_learn_lesson','lesson',106,'','{\"course\":{\"id\":\"10\",\"title\":\"OpenStack\",\"picture\":\"public:\\/\\/course\\/2017\\/06-21\\/225555b29356522658.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"106\",\"number\":\"3\",\"type\":\"video\",\"title\":\"DevStack \\u90e8\\u7f72OpenStack\",\"summary\":\"\"}}',0,0,1503583348),(602,1,'start_learn_lesson','lesson',104,'','{\"course\":{\"id\":\"10\",\"title\":\"OpenStack\",\"picture\":\"public:\\/\\/course\\/2017\\/06-21\\/225555b29356522658.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"104\",\"number\":\"1\",\"type\":\"document\",\"title\":\"\\u4e86\\u89e3OpenStack\\u4e3b\\u8981\\u7ec4\\u4ef6\",\"summary\":\"\"}}',0,0,1503583361),(604,1,'start_learn_lesson','lesson',92,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"92\",\"number\":\"18\",\"type\":\"video\",\"title\":\"Hadoop\\u6982\\u8ff0\\u89c6\\u9891\\u8bb2\\u89e3\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1503583403),(605,3,'start_learn_lesson','lesson',71,'','{\"course\":{\"id\":\"7\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u540c\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/192858a6bb9b361489.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"71\",\"number\":\"2\",\"type\":\"document\",\"title\":\"\\u5927\\u6570\\u636e\\u534f\\u4f5c\\u6846\\u67b6_Sqoop \\uff08\\u4e0b\\uff09\",\"summary\":\"\"}}',0,0,1503583997),(607,3,'start_learn_lesson','lesson',93,'','{\"course\":{\"id\":\"4\",\"title\":\"HBase\\u57fa\\u7840\\u548c\\u8fdb\\u9636\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/191516420656422951.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"93\",\"number\":\"10\",\"type\":\"video\",\"title\":\"HBase\\u6982\\u8ff0\\u89c6\\u9891\\u8bb2\\u89e3\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1503584065),(608,3,'start_learn_lesson','lesson',94,'','{\"course\":{\"id\":\"4\",\"title\":\"HBase\\u57fa\\u7840\\u548c\\u8fdb\\u9636\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/191516420656422951.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"94\",\"number\":\"11\",\"type\":\"video\",\"title\":\"HBase\\u672f\\u8bed\\u53ca\\u6570\\u636e\\u6a21\\u578b\",\"summary\":\"\"}}',0,0,1503584070),(609,3,'start_learn_lesson','lesson',95,'','{\"course\":{\"id\":\"5\",\"title\":\"Hive\\u57fa\\u7840\\u548c\\u8fdb\\u9636\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/191732c7c5ed616441.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"95\",\"number\":\"9\",\"type\":\"video\",\"title\":\"Hive\\u6982\\u8ff0\\u89c6\\u5c4f\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1503585725),(610,3,'start_learn_lesson','lesson',57,'','{\"course\":{\"id\":\"5\",\"title\":\"Hive\\u57fa\\u7840\\u548c\\u8fdb\\u9636\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/191732c7c5ed616441.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"57\",\"number\":\"1\",\"type\":\"document\",\"title\":\"Hive\\u6982\\u8ff0\",\"summary\":\"\"}}',0,0,1503585728),(611,3,'start_learn_lesson','lesson',92,'','{\"course\":{\"id\":\"3\",\"title\":\"Hadoop\\u57fa\\u7840\\u6846\\u67b6\",\"picture\":\"public:\\/\\/course\\/2017\\/05-31\\/190652c8ced4959601.jpg\",\"type\":\"normal\",\"rating\":null,\"about\":\"\",\"price\":null},\"lesson\":{\"id\":\"92\",\"number\":\"18\",\"type\":\"video\",\"title\":\"Hadoop\\u6982\\u8ff0\\u89c6\\u9891\\u8bb2\\u89e3\\u6848\\u4f8b\",\"summary\":\"\"}}',0,0,1503722480);
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_data`
--

DROP TABLE IF EXISTS `system_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `list` char(30) DEFAULT 'default',
  `key` char(50) DEFAULT 'default',
  `value` text,
  `mtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `list_webCode` (`list`,`key`,`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_data`
--

LOCK TABLES `system_data` WRITE;
/*!40000 ALTER TABLE `system_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `name` varchar(64) NOT NULL COMMENT '标签名称',
  `createdTime` int(10) unsigned NOT NULL COMMENT '标签创建时间',
  PRIMARY KEY (`id`),
  KEY `name_webCode` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_info`
--

DROP TABLE IF EXISTS `teacher_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `teacher_id` int(11) unsigned DEFAULT NULL COMMENT '对应的教师的ID',
  `contacts` text COMMENT '联系方式',
  `intros` text COMMENT '介绍',
  `teaches` text COMMENT '教授课程',
  `researches` text COMMENT '研究',
  `publications` text COMMENT '发表',
  `tpl` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '模板标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_info`
--

LOCK TABLES `teacher_info` WRITE;
/*!40000 ALTER TABLE `teacher_info` DISABLE KEYS */;
INSERT INTO `teacher_info` VALUES (3,12,'\"[{\\\"key\\\":\\\"\\\\u90ae\\\\u7bb1\\\",\\\"value\\\":\\\"326728721@qq.com\\\"},{\\\"key\\\":\\\"\\\\u529e\\\\u516c\\\\u5ba4\\\",\\\"value\\\":\\\"111111111\\\"},{\\\"key\\\":\\\"\\\\u529e\\\\u516c\\\\u65f6\\\\u95f4\\\",\\\"value\\\":\\\"2222222222\\\"},{\\\"key\\\":\\\"\\\\u7535\\\\u8bdd\\\",\\\"value\\\":\\\"33333333333\\\"}]\"','\"[{\\\"key\\\":\\\"\\\\u7814\\\\u7a76\\\\u9886\\\\u57df\\\",\\\"value\\\":\\\"3fffffffffffffff\\\"},{\\\"key\\\":\\\"\\\\u5b9e\\\\u9a8c\\\\u5ba4\\\",\\\"value\\\":\\\"zzzzzzzzzzzzzz\\\"}]\"','\"[{\\\"key\\\":\\\"\\\\u672c\\\\u79d1\\\\u6559\\\\u5b66\\\",\\\"value\\\":[{\\\"val\\\":\\\"ppp\\\",\\\"href\\\":\\\"\\\"},{\\\"val\\\":\\\"aaa\\\",\\\"href\\\":\\\"\\\"}]},{\\\"key\\\":\\\"\\\\u7814\\\\u7a76\\\\u751f\\\\u6559\\\\u5b66\\\",\\\"value\\\":[{\\\"val\\\":\\\"zzz\\\",\\\"href\\\":\\\"\\\"}]}]\"','\"[{\\\"key\\\":\\\"\\\\u79d1\\\\u7814\\\\u9879\\\\u76ee\\\",\\\"value\\\":[{\\\"val\\\":\\\"wwwwwwwwwwww\\\",\\\"href\\\":\\\"\\\"}]},{\\\"key\\\":\\\"\\\\u5956\\\\u9879\\\",\\\"value\\\":[{\\\"val\\\":\\\"ccccccccccccccccc\\\",\\\"href\\\":\\\"\\\"}]}]\"','\"[{\\\"key\\\":\\\"\\\\u4e66\\\\u7c4d\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\\u597d\\\\u7684\\\",\\\"href\\\":\\\"\\\"}],\\\"updateable\\\":null},{\\\"key\\\":\\\"paper\\\",\\\"value\\\":[{\\\"val\\\":\\\"aaaaaaa\\\",\\\"href\\\":\\\"\\\\\\/My\\\\\\/MyHomepage\\\\\\/downloadAction?path=0032\\\\\\\\69155406-e606-19aa-b5b9-3919f4876dbd.doc\\\"}],\\\"updateable\\\":1}]\"',1),(5,2234,'\"[{\\\"key\\\":\\\"\\\\u90ae\\\\u7bb1\\\",\\\"value\\\":\\\"3232323\\\"},{\\\"key\\\":\\\"\\\\u529e\\\\u516c\\\\u5ba4\\\",\\\"value\\\":\\\"\\\"},{\\\"key\\\":\\\"\\\\u529e\\\\u516c\\\\u65f6\\\\u95f4\\\",\\\"value\\\":\\\"\\\"},{\\\"key\\\":\\\"\\\\u7535\\\\u8bdd\\\",\\\"value\\\":\\\"\\\"}]\"','\"[{\\\"key\\\":\\\"\\\\u7814\\\\u7a76\\\\u9886\\\\u57df\\\",\\\"value\\\":\\\"\\\"},{\\\"key\\\":\\\"\\\\u5b9e\\\\u9a8c\\\\u5ba4\\\",\\\"value\\\":\\\"\\\"}]\"','\"[{\\\"key\\\":\\\"\\\\u672c\\\\u79d1\\\\u6559\\\\u5b66\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}]},{\\\"key\\\":\\\"\\\\u7814\\\\u7a76\\\\u751f\\\\u6559\\\\u5b66\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}]}]\"','\"[{\\\"key\\\":\\\"\\\\u79d1\\\\u7814\\\\u9879\\\\u76ee\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}]},{\\\"key\\\":\\\"test\\\",\\\"value\\\":[]}]\"','\"[{\\\"key\\\":\\\"paper\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}],\\\"updateable\\\":1},{\\\"key\\\":\\\"\\\\u4e66\\\\u7c4d\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}],\\\"updateable\\\":0}]\"',0),(6,230,'\"[{\\\"key\\\":\\\"\\\\u90ae\\\\u7bb1\\\",\\\"value\\\":\\\"12345@qq.com\\\"},{\\\"key\\\":\\\"\\\\u529e\\\\u516c\\\\u5ba4\\\",\\\"value\\\":\\\"\\\"},{\\\"key\\\":\\\"\\\\u529e\\\\u516c\\\\u65f6\\\\u95f4\\\",\\\"value\\\":\\\"\\\"},{\\\"key\\\":\\\"\\\\u7535\\\\u8bdd\\\",\\\"value\\\":\\\"\\\"}]\"','\"[{\\\"key\\\":\\\"\\\\u7814\\\\u7a76\\\\u9886\\\\u57df\\\",\\\"value\\\":\\\"\\\"},{\\\"key\\\":\\\"\\\\u5b9e\\\\u9a8c\\\\u5ba4\\\",\\\"value\\\":\\\"\\\"}]\"','\"[{\\\"key\\\":\\\"\\\\u672c\\\\u79d1\\\\u6559\\\\u5b66\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}]},{\\\"key\\\":\\\"\\\\u7814\\\\u7a76\\\\u751f\\\\u6559\\\\u5b66\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}]}]\"','\"[{\\\"key\\\":\\\"\\\\u79d1\\\\u7814\\\\u9879\\\\u76ee\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}]},{\\\"key\\\":\\\"\\\\u5956\\\\u9879\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}]}]\"','\"[{\\\"key\\\":\\\"paper\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}],\\\"updateable\\\":1},{\\\"key\\\":\\\"\\\\u4e66\\\\u7c4d\\\",\\\"value\\\":[{\\\"val\\\":\\\"\\\",\\\"href\\\":\\\"\\\"}],\\\"updateable\\\":0}]\"',2);
/*!40000 ALTER TABLE `teacher_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testpaper`
--

DROP TABLE IF EXISTS `testpaper`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testpaper` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '试卷ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '试卷名称',
  `description` text COMMENT '试卷说明',
  `limitedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '是否限时，1限时，0不限时',
  `pattern` varchar(255) NOT NULL DEFAULT '' COMMENT '试卷生成/显示模式',
  `target` varchar(255) NOT NULL DEFAULT '' COMMENT '试卷所属对象',
  `status` varchar(32) NOT NULL DEFAULT 'draft' COMMENT '试卷状态：draft,open,closed',
  `score` float(10,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '总分',
  `itemCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '题目数量',
  `createdUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatedUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改人',
  `updatedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `metas` text COMMENT '题型排序',
  `testpaperType` tinyint(1) DEFAULT '0' COMMENT '试卷类型：0 试卷 1作业 2练习',
  `scoreSetType` tinyint(1) NOT NULL DEFAULT '0' COMMENT '分数设置类型\r\n0=>按题型设置分数\r\n1=>按题目设置分数',
  `mode` char(10) NOT NULL DEFAULT 'manually' COMMENT '出题方式 0->手动出题;1->随机出题;2->选择已有试卷',
  `beginTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作业开始时间',
  `endTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '作业截止时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testpaper`
--

LOCK TABLES `testpaper` WRITE;
/*!40000 ALTER TABLE `testpaper` DISABLE KEYS */;
INSERT INTO `testpaper` VALUES (1,'第一节练习','',0,'QuestionType','course-5','open',6.0,3,1,1462843669,1,1462843766,'{\"question_type_seq\":[\"determine\",\"fill\",\"single_choice\"],\"missScore\":{\"choice\":\"0\",\"uncertain_choice\":\"0\"}}',0,0,'manually',0,0),(2,'课后练习','',0,'QuestionType','course-5','open',6.0,3,12,1462844136,1,1462844144,'{\"question_type_seq\":[\"determine\",\"fill\",\"single_choice\"],\"missScore\":{\"choice\":\"0\",\"uncertain_choice\":\"0\"}}',1,0,'manually',0,0),(3,'课堂练习','',0,'QuestionType','course-7','draft',0.0,0,12,1470466581,12,1470466581,'[]',0,0,'manually',0,0),(4,'考验大家的时候到了','',0,'QuestionType','course-7','open',0.0,1,12,1470467718,12,1470468194,'{\"question_type_seq\":[\"single_choice\"],\"missScore\":{\"choice\":\"0\",\"uncertain_choice\":\"0\"}}',0,0,'manually',0,0),(5,'计算机网络RTSP大作业','',0,'QuestionType','course-7','open',2.0,1,12,1470468859,12,1470479311,'{\"question_type_seq\":[\"essay\"],\"missScore\":{\"choice\":\"0\",\"uncertain_choice\":\"0\"}}',1,0,'manually',0,0),(6,'数据结构与算法第一次练习','',0,'QuestionType','course-21','open',0.0,4,12,1471410829,12,1471411025,'{\"question_type_seq\":[\"determine\",\"essay\",\"fill\",\"single_choice\"],\"missScore\":{\"choice\":\"0\",\"uncertain_choice\":\"0\"}}',0,0,'manually',0,0),(7,'数据结构与算法课后作业','',0,'QuestionType','course-21','open',0.0,4,12,1471411780,12,1471411909,'{\"question_type_seq\":[\"determine\",\"essay\",\"fill\",\"single_choice\"],\"missScore\":{\"choice\":\"0\",\"uncertain_choice\":\"0\"}}',1,0,'manually',0,0),(8,'我的第一个班级作业20160829','',0,'QuestionType','course-21','open',8.0,4,12,1472460909,12,1472460921,'{\"question_type_seq\":[\"determine\",\"essay\",\"fill\",\"single_choice\"],\"missScore\":{\"choice\":\"0\",\"uncertain_choice\":\"0\"}}',1,0,'random',0,0),(9,'我的第一个班级作业2016092','',0,'QuestionType','course-7','draft',0.0,0,12,1472789761,12,1472789761,'[]',1,0,'manually',0,0),(14,'吃炒面喝靓汤','',0,'FileType','course-4','open',5.0,0,1,1473475352,1,1473475352,'[]',1,0,'file',0,0),(15,'测试练习添加','',0,'QuestionType','course-21','draft',0.0,0,230,1473768431,230,1473768431,'[]',NULL,0,'manually',0,0),(17,'新的作业','<p>这次作业只许成功不许失败！</p>',0,'FileType','course-4','open',5.0,0,1,1474374418,1,1474375350,'[]',1,0,'file',2016,2016),(20,'中国传媒大学理工学部在线课程','<p>hello world </p>',0,'FileType','course-4','open',10.0,0,1,1474376800,1,1474376831,'[]',1,0,'file',1474290417,1474463217),(21,'手动添加题目的作业来啦','',0,'QuestionType','course-4','open',2.0,1,1,1474617599,1,1474619161,'{\"question_type_seq\":[\"essay\"],\"missScore\":{\"choice\":\"0\",\"uncertain_choice\":\"0\"}}',1,0,'manually',1474619161,1506155161),(22,'123','',0,'QuestionType','course-4','draft',0.0,2,1,1474879899,1,1474879905,'{\"question_type_seq\":[\"essay\"],\"missScore\":{\"choice\":\"0\",\"uncertain_choice\":\"0\"}}',1,0,'manually',0,0),(23,'123456','<p>早点交！</p>',1,'FileType','course-24','open',2.0,0,2234,1476944566,2234,1476944791,'[]',1,0,'file',1476944593,1477894993);
/*!40000 ALTER TABLE `testpaper` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testpaper_item`
--

DROP TABLE IF EXISTS `testpaper_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testpaper_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '试卷条目ID',
  `testId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属试卷',
  `seq` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '题目顺序',
  `questionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '题目ID',
  `questionType` varchar(64) NOT NULL DEFAULT '' COMMENT '题目类别',
  `parentId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父题ID',
  `score` float(10,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '分值',
  `missScore` float(10,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '漏选得分',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testpaper_item`
--

LOCK TABLES `testpaper_item` WRITE;
/*!40000 ALTER TABLE `testpaper_item` DISABLE KEYS */;
INSERT INTO `testpaper_item` VALUES (1,1,1,1,'single_choice',0,2.0,0.0),(2,1,2,2,'determine',0,2.0,0.0),(3,1,3,3,'fill',0,2.0,0.0),(4,2,1,1,'single_choice',0,2.0,0.0),(5,2,2,2,'determine',0,2.0,0.0),(6,2,3,3,'fill',0,2.0,0.0),(7,4,1,1,'single_choice',0,0.0,0.0),(11,5,1,2,'essay',0,2.0,0.0),(12,6,1,3,'single_choice',0,0.0,0.0),(13,6,3,4,'fill',0,0.0,0.0),(14,6,2,5,'determine',0,0.0,0.0),(15,6,4,6,'essay',0,0.0,0.0),(16,7,1,3,'single_choice',0,0.0,0.0),(17,7,3,4,'fill',0,0.0,0.0),(18,7,2,5,'determine',0,0.0,0.0),(19,7,4,6,'essay',0,0.0,0.0),(20,8,1,3,'single_choice',0,2.0,0.0),(21,8,3,4,'fill',0,2.0,0.0),(22,8,2,5,'determine',0,2.0,0.0),(23,8,4,6,'essay',0,2.0,0.0),(24,21,1,7,'essay',0,2.0,0.0),(25,22,0,7,'essay',0,0.0,0.0),(26,22,0,8,'essay',0,0.0,0.0);
/*!40000 ALTER TABLE `testpaper_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testpaper_item_result`
--

DROP TABLE IF EXISTS `testpaper_item_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testpaper_item_result` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '试卷题目做题结果ID',
  `itemId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '试卷条目ID',
  `testId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '试卷ID',
  `testPaperResultId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '试卷结果ID',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '做题人ID',
  `questionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '题目ID',
  `status` enum('none','right','partRight','wrong','noAnswer') NOT NULL DEFAULT 'none' COMMENT '结果状态',
  `score` float(10,1) NOT NULL DEFAULT '0.0' COMMENT '得分',
  `answer` text COMMENT '回答',
  `teacherSay` text COMMENT '老师评价',
  PRIMARY KEY (`id`),
  KEY `testPaperResultId` (`testPaperResultId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testpaper_item_result`
--

LOCK TABLES `testpaper_item_result` WRITE;
/*!40000 ALTER TABLE `testpaper_item_result` DISABLE KEYS */;
INSERT INTO `testpaper_item_result` VALUES (1,0,1,1,4,1,'noAnswer',0.0,'[\"\"]',NULL),(2,0,1,1,4,2,'noAnswer',0.0,'[\"\"]',NULL),(3,0,1,1,4,3,'noAnswer',0.0,'[\"\"]',NULL),(4,0,4,11,12,1,'right',0.0,'[\"3\"]',NULL),(5,0,5,13,12,2,'none',0.0,'[\"<p>\\u8fd9\\u662f\\u6211\\u7684\\u4f5c\\u4e1a<\\/p>\\n\"]',NULL),(6,0,5,14,1,2,'right',1.8,'[\"<p>\\u8fd9\\u662f\\u672c\\u4eba\\u7684rtsp\\u76f4\\u64ad\\u7a0b\\u5e8f\\u4f5c\\u4e1a<img alt=\\\"\\\" src=\\\"\\/Public\\/files\\/course\\/2016\\/08-16\\/163158e1c908406692.jpg\\\" \\/><\\/p>\\n\"]','<p>回答的不错，就是逻辑有点不太清晰，下次配个好看点的图，再接再厉，我看好你，么么哒！</p>'),(7,0,7,15,1,3,'right',0.0,'[\"0\"]',NULL),(8,0,7,15,1,4,'right',0.0,'[\"\\u6709\\u7a77\\u6027\",\"\\u786e\\u5b9a\\u6027\",\"\\u53ef\\u884c\\u6027\",\"\\u8f93\\u5165\",\"\\u8f93\\u51fa\"]',NULL),(9,0,7,15,1,5,'wrong',0.0,'[\"0\"]',NULL),(10,0,7,15,1,6,'right',0.0,'[\"<p><span style=\\\"color:rgb(34, 34, 34)\\\">(3,5)12,(3,6)9,(4,6)4,(4,7)20,(5,6)18,(6,7)25}<\\/span><\\/p>\\n\"]',''),(11,0,2,18,1,1,'noAnswer',0.0,'[\"\"]',NULL),(12,0,2,18,1,2,'noAnswer',0.0,'[\"\"]',NULL),(13,0,2,18,1,3,'noAnswer',0.0,'[\"\"]',NULL);
/*!40000 ALTER TABLE `testpaper_item_result` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testpaper_result`
--

DROP TABLE IF EXISTS `testpaper_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testpaper_result` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '试卷结果ID',
  `paperName` varchar(255) NOT NULL DEFAULT '' COMMENT '试卷名称',
  `testId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '试卷ID',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '做卷人ID',
  `score` float(10,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '总分',
  `objectiveScore` float(10,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '主观题得分',
  `subjectiveScore` float(10,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '客观题得分',
  `teacherSay` text COMMENT '老师评价',
  `rightItemCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '正确题目数',
  `limitedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '试卷限制时间(秒)',
  `beginTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `submitTime` int(10) NOT NULL DEFAULT '0' COMMENT '提交作业的时间',
  `updateTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `status` enum('doing','submitted','finished') NOT NULL COMMENT '状态，正在做、已经提交、已经完成（被批改了）',
  `target` varchar(255) NOT NULL DEFAULT '' COMMENT '试卷结果所属对象',
  `checkTeacherId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '批卷老师ID',
  `checkedTime` int(11) NOT NULL DEFAULT '0' COMMENT '批卷时间',
  `testpaperType` tinyint(1) DEFAULT '0' COMMENT '试卷类型：0 试卷 1作业 2练习',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testpaper_result`
--

LOCK TABLES `testpaper_result` WRITE;
/*!40000 ALTER TABLE `testpaper_result` DISABLE KEYS */;
INSERT INTO `testpaper_result` VALUES (3,'第一节练习',1,4,0.0,0.0,0.0,NULL,0,0,1467708272,0,1467731392,'doing','course-5/lesson-6',0,0,2),(4,'第一节练习',1,700,0.0,0.0,0.0,NULL,0,0,1467910681,0,1467951447,'doing','course-5/lesson-6',0,0,2),(5,'第一节练习',1,16,0.0,0.0,0.0,NULL,0,0,1469247100,0,1469247704,'doing','course-5/lesson-6',0,0,2),(6,'课后练习',2,16,0.0,0.0,0.0,NULL,0,0,1469247856,0,0,'doing','course-5/lesson-6',0,0,2),(7,'课后练习',2,16,0.0,0.0,0.0,NULL,0,0,1469247893,0,0,'doing','course-5/lesson-',0,0,2),(8,'课后练习',2,16,0.0,0.0,0.0,NULL,0,0,1469247919,0,0,'doing','course-5/lesson-',0,0,2),(9,'课后练习',2,16,0.0,0.0,0.0,NULL,0,0,1469247973,0,0,'doing','course-5/lesson-6',0,0,2),(10,'课后练习',2,16,0.0,0.0,0.0,NULL,0,0,1469247990,0,1469249361,'doing','course-5/lesson-6',0,0,2),(11,'考验大家的时候到了',4,12,0.0,0.0,0.0,NULL,1,0,1470468269,0,0,'finished','course-7/lesson-13',0,1470468403,2),(12,'考验大家的时候到了',4,12,0.0,0.0,0.0,NULL,0,0,1470468434,0,0,'doing','course-7/lesson-13',0,0,2),(13,'计算机网络RTSP大作业',5,12,0.0,0.0,0.0,NULL,0,0,1471161376,0,0,'doing','course-7/homework-0',0,0,1),(14,'计算机网络RTSP大作业',5,1,1.8,0.0,1.8,'继续努力，继续进步！',0,0,1471331022,1471331022,1471331022,'finished','course-7/homework-0',12,1471346541,1),(15,'数据结构与算法课后作业',7,1,0.0,0.0,0.0,'不错，有进步，再努力些就会更棒！',3,0,1471413348,1471413565,0,'finished','course-21/homework-0',12,1471415092,1),(16,'数据结构与算法第一次练习',6,1,0.0,0.0,0.0,NULL,0,0,1471429783,0,0,'doing','course-21/lesson-26',0,0,2),(17,'数据结构与算法第一次练习',6,12,0.0,0.0,0.0,NULL,0,0,1471604499,0,0,'doing','course-21/lesson-26',0,0,2),(18,'课后练习',2,1,0.0,0.0,0.0,NULL,0,0,1471932846,1471933468,0,'submitted','course-5/homework-0',0,0,1),(20,'吃炒面喝靓汤',14,260,5.0,0.0,5.0,'干的漂亮！再接再厉！',0,0,1473475798,1473476040,0,'finished','course-4/homework-0',1,1473511843,1),(21,'考验大家的时候到了',4,288,0.0,0.0,0.0,NULL,0,0,1490534947,0,0,'doing','course-7/lesson-13',0,0,2);
/*!40000 ALTER TABLE `testpaper_result` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `theme_config`
--

DROP TABLE IF EXISTS `theme_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `theme_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `config` text,
  `confirmConfig` text,
  `allConfig` text,
  `updatedTime` int(11) NOT NULL DEFAULT '0',
  `createdTime` int(11) NOT NULL DEFAULT '0',
  `updatedUserId` int(11) NOT NULL DEFAULT '0',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `theme_config`
--

LOCK TABLES `theme_config` WRITE;
/*!40000 ALTER TABLE `theme_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `theme_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upgrade_logs`
--

DROP TABLE IF EXISTS `upgrade_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upgrade_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remoteId` int(11) NOT NULL COMMENT 'packageId',
  `installedId` int(11) DEFAULT NULL COMMENT '本地已安装id',
  `ename` varchar(32) NOT NULL COMMENT '名称',
  `cname` varchar(32) NOT NULL COMMENT '中文名称',
  `fromv` varchar(32) DEFAULT NULL COMMENT '初始版本',
  `tov` varchar(32) NOT NULL COMMENT '目标版本',
  `type` smallint(6) NOT NULL COMMENT '升级类型',
  `dbBackPath` text COMMENT '数据库备份文件',
  `srcBackPath` text COMMENT '源文件备份地址',
  `status` varchar(32) NOT NULL COMMENT '状态(ROLLBACK,ERROR,SUCCESS,RECOVERED)',
  `logtime` int(11) NOT NULL COMMENT '升级时间',
  `uid` int(10) unsigned NOT NULL COMMENT 'uid',
  `ip` varchar(32) DEFAULT NULL COMMENT 'ip',
  `reason` text COMMENT '失败原因',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upgrade_logs`
--

LOCK TABLES `upgrade_logs` WRITE;
/*!40000 ALTER TABLE `upgrade_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `upgrade_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload_files`
--

DROP TABLE IF EXISTS `upload_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '上传文件ID',
  `hashId` varchar(128) NOT NULL DEFAULT '' COMMENT '文件的HashID',
  `targetId` int(11) NOT NULL COMMENT '所存目标ID',
  `targetType` varchar(64) NOT NULL DEFAULT '' COMMENT '目标类型',
  `filename` varchar(1024) NOT NULL DEFAULT '' COMMENT '文件名',
  `ext` varchar(12) NOT NULL DEFAULT '' COMMENT '后缀',
  `size` bigint(20) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `etag` varchar(256) NOT NULL DEFAULT '' COMMENT 'ETAG',
  `length` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '长度（音视频则为时长，PPT/文档为页数）',
  `convertHash` varchar(128) NOT NULL DEFAULT '' COMMENT '文件转换时的查询转换进度用的Hash值',
  `ifConvert` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否经过转码，是的话大于0，否则没经过转码',
  `convertStatus` enum('none','waiting','doing','success','error') NOT NULL DEFAULT 'none' COMMENT '文件转换状态',
  `convertParams` text COMMENT '文件转换参数',
  `metas` text COMMENT '元信息',
  `metas2` text COMMENT '元信息',
  `type` enum('document','video','audio','image','ppt','other') NOT NULL DEFAULT 'other' COMMENT '文件类型',
  `storage` enum('local','cloud') NOT NULL COMMENT '文件存储方式',
  `isPublic` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否公开文件',
  `canDownload` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否可下载',
  `usedCount` int(10) unsigned NOT NULL DEFAULT '0',
  `updatedUserId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新用户名',
  `updatedTime` int(10) unsigned DEFAULT '0' COMMENT '文件最后更新时间',
  `createdUserId` int(10) unsigned NOT NULL COMMENT '文件上传人',
  `createdTime` int(10) unsigned NOT NULL COMMENT '文件上传时间',
  PRIMARY KEY (`id`),
  KEY `convertHash_webCode` (`convertHash`(64)) USING BTREE,
  KEY `hashId_webCode` (`hashId`(120)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upload_files`
--

LOCK TABLES `upload_files` WRITE;
/*!40000 ALTER TABLE `upload_files` DISABLE KEYS */;
INSERT INTO `upload_files` VALUES (1,'courselesson/4/2016422021733-c17yqw.pdf',4,'courselesson','test.pdf','pdf',61758,'',0,'ch-courselesson/4/2016422021733-c17yqw.docx',0,'success',NULL,NULL,NULL,'document','local',0,1,0,1,1461306016,1,1461305853),(2,'courselesson/5/2016510092713-of6qp4.pdf',5,'courselesson','最新模板.pdf','pdf',887388,'',0,'ch-courselesson/5/2016510092713-of6qp4.pptx',0,'success',NULL,NULL,NULL,'ppt','local',0,1,0,1,1462843636,1,1462843633),(3,'courselesson/9/2016723083530-a42ot7.doc',9,'courselesson','2015北京东城区南片初二上学期数学试题及答案.doc','doc',640512,'',0,'ch-courselesson/9/2016723083530-a42ot7.doc',0,'waiting',NULL,NULL,NULL,'document','local',0,1,0,16,1469277330,16,1469277330),(4,'courselesson/9/2016724024128-nyn26k.docx',9,'courselesson','tlc系统使用简介.docx','docx',1832895,'',0,'ch-courselesson/9/2016724024128-nyn26k.docx',0,'waiting',NULL,NULL,NULL,'document','local',0,1,0,16,1469342488,16,1469342488),(5,'courselesson/20/2016728012535-jcddtj.docx',20,'courselesson','2016_07_15_任思可_周报.docx','docx',29669,'',0,'ch-courselesson/20/2016728012535-jcddtj.docx',0,'waiting',NULL,NULL,NULL,'document','local',1,1,0,16,1469683535,16,1469683535),(6,'courselesson/20/2016728040848-mnlgmx.docx',20,'courselesson','账号认证系统接口参数说明.docx','docx',26376,'',0,'ch-courselesson/20/2016728040848-mnlgmx.docx',0,'waiting',NULL,NULL,NULL,'document','local',1,1,0,12,1469693328,12,1469693328),(7,'resource/9/2016731012930-gyqnoj.jpg',9,'resource','Desert.jpg','jpg',845941,'',0,'ch-resource/9/2016731012930-gyqnoj.jpg',0,'waiting',NULL,NULL,NULL,'image','local',1,1,0,16,1469942970,16,1469942970),(9,'courselesson/8/2016801125044-bbpug4.wmv',8,'courselesson','Wildlife.wmv','wmv',26246026,'',0,'ch-courselesson/8/2016801125044-bbpug4.wmv',0,'success',NULL,NULL,NULL,'video','local',1,1,0,16,1470027044,16,1470027044),(10,'courselesson/7/2016803094128-ddo3d9.mp4',7,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-courselesson/7/2016803094128-ddo3d9.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,16,1470231688,16,1470231688),(14,'Documents/videos/1_0.mp4',7,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-Documents/videos/1_0.mp4',0,'success',NULL,NULL,NULL,'video','cloud',1,1,0,12,1470453221,12,1470453221),(16,'courselesson/5/2016812075547-qdv6s7.mp4',5,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-courselesson/5/2016812075547-qdv6s7.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,12,1471002947,12,1471002947),(17,'courselesson/7/2016812080152-7mpij2.mp4',7,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-courselesson/7/2016812080152-7mpij2.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,0,1471003312,0,1471003312),(18,'courselesson/5/2016812081604-8bl06j.mp4',5,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-courselesson/5/2016812081604-8bl06j.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,0,1471004164,0,1471004164),(19,'courselesson/5/2016812084807-ou29gs.mp4',5,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-courselesson/5/2016812084807-ou29gs.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,0,1471006087,0,1471006087),(20,'courselesson/5/2016812092543-hfz2ad.mp4',5,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-courselesson/5/2016812092543-hfz2ad.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,12,1471008343,12,1471008343),(21,'courselesson/7/2016812093148-mwlsz9.pdf',7,'courselesson','北京市东城区2014-2015学年七年级下学期期末教学统一检测数学试题.pdf','pdf',712573,'',0,'ch-courselesson/7/2016812093148-mwlsz9.pdf',1,'waiting',NULL,NULL,NULL,'document','local',1,1,0,12,1471008708,12,1471008708),(22,'courselesson/7/2016812094113-ofqt8r.pdf',7,'courselesson','北京市东城区2014-2015学年七年级下学期期末教学统一检测数学试题.pdf','pdf',712573,'',0,'ch-courselesson/7/2016812094113-ofqt8r.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,12,1471009273,12,1471009273),(23,'Documents/videos/1_0.mp4',7,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-Documents/videos/1_0.mp4',0,'success',NULL,NULL,NULL,'video','cloud',1,1,0,12,1471271965,12,1471271965),(24,'courselesson/21/2016817114954-nvxhnn.mp4',21,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-courselesson/21/2016817114954-nvxhnn.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,12,1471405794,12,1471405794),(25,'Documents/videos/1_0.mp4',21,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-Documents/videos/1_0.mp4',0,'success',NULL,NULL,NULL,'video','cloud',1,1,0,12,1471406823,12,1471406823),(26,'courselesson/21/2016817121554-o14ndd.pdf',21,'courselesson','实验一-线性表的基本操作.pdf','pdf',139613,'',0,'ch-courselesson/21/2016817121554-o14ndd.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,12,1471407354,12,1471407354),(27,'courselesson/21/2016817122338-sfpm33.pdf',21,'courselesson','实验一-线性表的基本操作.pdf','pdf',139613,'',0,'ch-courselesson/21/2016817122338-sfpm33.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,12,1471407818,12,1471407818),(28,'courselesson/21/2016817122358-cxbiv8.pdf',21,'courselesson','实验一-线性表的基本操作.pdf','pdf',139613,'',0,'ch-courselesson/21/2016817122358-cxbiv8.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,12,1471407838,12,1471407838),(30,'courselesson/21/2016818035842-4j9fm0.mp4',21,'courselesson','2016818033648-170dk2.mp4','mp4',20290728,'',0,'ch-courselesson/21/2016818035842-4j9fm0.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,12,1471507122,12,1471507122),(31,'courselesson/7/2016821093354-5okzll.avi',7,'courselesson','00_《企业级Hadoop 2.x深入浅出基础课程》课程大纲.avi','avi',94032942,'',0,'ch-courselesson/7/2016821093354-5okzll.avi',0,'success',NULL,NULL,NULL,'video','local',1,1,0,12,1471786434,12,1471786434),(37,'courselesson/7/2016822101427-34ghqo.docx',7,'courselesson','2016西城区初三上册化学期末试题及答案.docx','docx',2477725,'',0,'ch-courselesson/7/2016822101427-34ghqo.docx',1,'waiting',NULL,NULL,NULL,'document','local',1,1,0,12,1471875267,12,1471875267),(38,'courselesson/7/2016822101500-t8ekmp.docx',7,'courselesson','2016北京东城区初三上册化学期末试题及答案.docx','docx',3237730,'',0,'ch-courselesson/7/2016822101500-t8ekmp.docx',1,'waiting',NULL,NULL,NULL,'document','local',1,1,0,12,1471875300,12,1471875300),(39,'courselesson/7/2016822101557-7mxq5c.doc',7,'courselesson','2015北京东城区南片初二上学期数学试题及答案.doc','doc',640512,'',0,'ch-courselesson/7/2016822101557-7mxq5c.doc',1,'waiting',NULL,NULL,NULL,'document','local',1,1,0,12,1471875357,12,1471875357),(40,'courselesson/7/2016822101638-om7jl2.pdf',7,'courselesson','北京市东城区2014-2015学年七年级下学期期末教学统一检测数学试题.pdf','pdf',712573,'',0,'ch-courselesson/7/2016822101638-om7jl2.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,12,1471875398,12,1471875398),(41,'courselesson/7/2016822102646-3fwkmy.pdf',7,'courselesson','北京市朝阳区2014-2015学年七年级（下）期末数学试卷.pdf','pdf',778993,'',0,'ch-courselesson/7/2016822102646-3fwkmy.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,12,1471876006,12,1471876006),(43,'courselesson/21/2016822104841-dpr3xv.docx',21,'courselesson','2015北京东城区初三物理试题及答案.docx','docx',2475709,'',0,'ch-courselesson/21/2016822104841-dpr3xv.docx',1,'waiting',NULL,NULL,NULL,'document','local',1,1,0,12,1471877321,12,1471877321),(44,'courselesson/7/2016823020427-otj91f.pdf',7,'courselesson','2015-2016北京市西城区八年级第二学期数学试卷及答案.pdf','pdf',904202,'',0,'ch-courselesson/7/2016823020427-otj91f.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,12,1471932267,12,1471932267),(45,'courselesson/20/2016912064711-cm81aj.mov',20,'courselesson','PBSG_DNS.mov','mov',27609910,'',0,'ch-courselesson/20/2016912064711-cm81aj.mov',0,'success',NULL,NULL,NULL,'video','local',1,1,0,5,1473677231,5,1473677231),(46,'courselesson/22/2016913065703-hz78fu.webm',22,'courselesson','PBSG_DNS.webm','webm',16825677,'',0,'ch-courselesson/22/2016913065703-hz78fu.webm',0,'success',NULL,NULL,NULL,'other','local',1,1,0,230,1473764223,230,1473764223),(47,'courselesson/4/2016920124625-4u4jm0.mp4',4,'courselesson','1_0.mp4','mp4',15797610,'',0,'ch-courselesson/4/2016920124625-4u4jm0.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,0,1474346785,0,1474346785),(48,'courselesson/4/2016922041215-2ns2y0.pdf',4,'courselesson','实验一-线性表的基本操作.pdf','pdf',139613,'',0,'ch-courselesson/4/2016922041215-2ns2y0.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,1,1474531935,1,1474531935),(50,'courselesson/4/2016922045305-e99roh.pdf',4,'courselesson','2015年北京中考数学试卷与答案word版.pdf','pdf',1275559,'',0,'ch-courselesson/4/2016922045305-e99roh.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,1,1474534385,1,1474534386),(51,'courselesson/1/2016922045357-si4yi5.pdf',1,'courselesson','2015年北京中考数学试卷与答案word版.pdf','pdf',1275559,'',0,'ch-courselesson/1/2016922045357-si4yi5.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,1,1474534437,1,1474534437),(52,'quizquestion/0/2016923042527-ngae92.jpg',0,'quizquestion','Penguins.jpg','jpg',202138,'',0,'ch-quizquestion/0/2016923042527-ngae92.jpg',0,'success',NULL,NULL,NULL,'image','local',1,1,0,1,1474619127,1,1474619127),(53,'quizquestion/0/2016923042700-qau18x.jpg',0,'quizquestion','Lighthouse.jpg','jpg',169651,'',0,'ch-quizquestion/0/2016923042700-qau18x.jpg',0,'success',NULL,NULL,NULL,'image','local',1,1,0,1,1474619220,1,1474619220),(54,'quizquestion/0/2016923042704-rze6qj.jpg',0,'quizquestion','Desert.jpg','jpg',233021,'',0,'ch-quizquestion/0/2016923042704-rze6qj.jpg',0,'success',NULL,NULL,NULL,'image','local',1,1,0,1,1474619224,1,1474619224),(55,'quizquestion/0/2016923042708-as0b4a.jpg',0,'quizquestion','Tulips.jpg','jpg',161689,'',0,'ch-quizquestion/0/2016923042708-as0b4a.jpg',0,'success',NULL,NULL,NULL,'image','local',1,1,0,1,1474619228,1,1474619228),(56,'courselesson/23/20161011100246-tjjjsw.mp4',23,'courselesson','echo-hereweare.mp4','mp4',5360323,'',0,'ch-courselesson/23/20161011100246-tjjjsw.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,230,1476194566,230,1476194566),(57,'courselesson/23/20161011100927-1cetp6.pdf',23,'courselesson','2016922092749-6xpw3i.pdf','pdf',745030,'',0,'ch-courselesson/23/20161011100927-1cetp6.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,230,1476194967,230,1476194967),(58,'courselesson/24/20161022101006-t86lhn.pdf',24,'courselesson','2015年北京中考数学试卷与答案word版.pdf','pdf',1275559,'',0,'ch-courselesson/24/20161022101006-t86lhn.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,230,1477102206,230,1477102206),(59,'courselesson/24/20161117103209-mtgsc7.pptx',24,'courselesson','第三课作业.pptx','pptx',345462,'',0,'ch-courselesson/24/20161117103209-mtgsc7.pptx',1,'waiting',NULL,NULL,NULL,'ppt','local',1,1,0,2234,1479349929,2234,1479349929),(60,'courselesson/24/20161117103251-n3960y.pptx',24,'courselesson','第三课作业.pptx','pptx',345462,'',0,'ch-courselesson/24/20161117103251-n3960y.pptx',1,'waiting',NULL,NULL,NULL,'ppt','local',1,1,0,2234,1479349971,2234,1479349971),(61,'courselesson/23/20161202092204-8mjwt8.pdf',23,'courselesson','Unit9_WinSock_Multiplex.pdf','pdf',2548100,'',0,'ch-courselesson/23/20161202092204-8mjwt8.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,0,1480641724,0,1480641724),(62,'courselesson/2/2017416112950-lh3vjf.docx',2,'courselesson','部署说明文档.docx','docx',39498,'',0,'ch-courselesson/2/2017416112950-lh3vjf.docx',1,'waiting',NULL,NULL,NULL,'document','local',1,1,0,0,1492313390,0,1492313390),(63,'courselesson/3/2017503085927-rvclxu.mp4',3,'courselesson','1.Hadoop概述.mp4','mp4',36402988,'',0,'ch-courselesson/3/2017503085927-rvclxu.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,0,1493816367,0,1493816367),(65,'courselesson/3/2017509095206-mfot0z.mp4',3,'courselesson','1.Hadoop概述.mp4','mp4',31428014,'',0,'ch-courselesson/3/2017509095206-mfot0z.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,3,1494337926,3,1494337926),(66,'courselesson/3/2017509100835-q3qsn3.pptx',3,'courselesson','hadoop安装配置.pptx','pptx',393894,'',0,'ch-courselesson/3/2017509100835-q3qsn3.pptx',1,'waiting',NULL,NULL,NULL,'ppt','local',1,1,0,3,1494338915,3,1494338915),(67,'courselesson/2/2017514095535-a4owwk.mp4',2,'courselesson','1.什么是Linux.mp4','mp4',21377079,'',0,'ch-courselesson/2/2017514095535-a4owwk.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494726935,3,1494726935),(73,'courselesson/2/2017514101256-dj72z1.mp4',2,'courselesson','2.Linux主要版本.mp4','mp4',65852577,'',0,'ch-courselesson/2/2017514101256-dj72z1.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494727976,3,1494727976),(74,'courselesson/2/2017514101323-juklbk.mp4',2,'courselesson','3.Linux的优缺点.mp4','mp4',47274754,'',0,'ch-courselesson/2/2017514101323-juklbk.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494728003,3,1494728003),(75,'courselesson/2/2017514101350-33w7fl.mp4',2,'courselesson','1.Hadoop概述.mp4','mp4',133141867,'',0,'ch-courselesson/2/2017514101350-33w7fl.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494728030,3,1494728030),(76,'courselesson/2/2017514101936-1qw4zp.mp4',2,'courselesson','2.HBase概述.mp4','mp4',71734392,'',0,'ch-courselesson/2/2017514101936-1qw4zp.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494728376,3,1494728376),(77,'courselesson/2/2017514102042-tv5vb1.mp4',2,'courselesson','2.HBase术语及数据模型.mp4','mp4',33687929,'',0,'ch-courselesson/2/2017514102042-tv5vb1.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494728442,3,1494728442),(78,'courselesson/2/2017514102456-hwfkwa.mp4',2,'courselesson','3.Hive简介.mp4','mp4',87889880,'',0,'ch-courselesson/2/2017514102456-hwfkwa.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494728696,3,1494728696),(79,'courselesson/2/2017514102514-pk23r5.mp4',2,'courselesson','3.Hive与HBase关系.mp4','mp4',28345325,'',0,'ch-courselesson/2/2017514102514-pk23r5.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494728714,3,1494728714),(80,'courselesson/2/2017514102900-gjo5ou.mp4',2,'courselesson','4.Spark简介.mp4','mp4',33660428,'',0,'ch-courselesson/2/2017514102900-gjo5ou.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494728940,3,1494728940),(81,'courselesson/2/2017514102917-sm4pxx.mp4',2,'courselesson','4.Spark计算模型.mp4','mp4',69397706,'',0,'ch-courselesson/2/2017514102917-sm4pxx.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1494728957,3,1494728957),(82,'courselesson/2/2017514030158-o81rek.pdf',2,'courselesson','Hadoop安装配置.pdf','pdf',419028,'',0,'ch-courselesson/2/2017514030158-o81rek.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1494745318,3,1494745318),(83,'courselesson/2/2017514030232-oa1wpw.pdf',2,'courselesson','HDFS的基本使用.pdf','pdf',418827,'',0,'ch-courselesson/2/2017514030232-oa1wpw.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1494745352,3,1494745352),(84,'courselesson/2/2017514030315-moep95.pdf',2,'courselesson','HBase安装配置 .pdf','pdf',396517,'',0,'ch-courselesson/2/2017514030315-moep95.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1494745395,3,1494745395),(85,'courselesson/2/2017514030351-czzkg3.pdf',2,'courselesson','HBase Shell的基本使用 .pdf','pdf',506953,'',0,'ch-courselesson/2/2017514030351-czzkg3.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1494745431,3,1494745431),(88,'courselesson/2/2017514030548-eu51kr.pdf',2,'courselesson','Spark安装与操作.pdf','pdf',410664,'',0,'ch-courselesson/2/2017514030548-eu51kr.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1494745548,3,1494745548),(89,'courselesson/2/2017523092337-baqf6i.pdf',2,'courselesson','Hive安装配置.pdf','pdf',391481,'',0,'ch-courselesson/2/2017523092337-baqf6i.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1495545817,3,1495545817),(90,'courselesson/2/2017523092410-aumsxn.pdf',2,'courselesson','Hive Sql的基本命令 .pdf','pdf',372648,'',0,'ch-courselesson/2/2017523092410-aumsxn.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1495545850,3,1495545850),(91,'courselesson/3/2017529035433-q7tn80.pdf',3,'courselesson','第4章_第1节_初识Hadoop(上）.pdf','pdf',699639,'',0,'ch-courselesson/3/2017529035433-q7tn80.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496044473,3,1496044473),(92,'courselesson/3/2017529035452-4qcqum.pdf',3,'courselesson','第4章_第1节_初识Hadoop（下）.pdf','pdf',832733,'',0,'ch-courselesson/3/2017529035452-4qcqum.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,0,3,1496044492,3,1496044492),(93,'courselesson/3/2017529035531-e7uk4d.pdf',3,'courselesson','第4章_第1节_初识Hadoop（下）.pdf','pdf',832733,'',0,'ch-courselesson/3/2017529035531-e7uk4d.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496044531,3,1496044531),(94,'courselesson/3/2017529035822-5gu7r7.pdf',3,'courselesson','课程3实验5 HADOOP集群伪分布式搭建-实验指南.pdf','pdf',1303327,'',0,'ch-courselesson/3/2017529035822-5gu7r7.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496044702,3,1496044702),(98,'courselesson/3/2017529040123-tnnptv.pdf',3,'courselesson','第4章_第2节_HDFS文件系统（上）.pdf','pdf',681331,'',0,'ch-courselesson/3/2017529040123-tnnptv.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496044883,3,1496044883),(99,'courselesson/3/2017529040145-lr7aq5.pdf',3,'courselesson','第4章_第2节_HDFS文件系统（下）.pdf','pdf',650408,'',0,'ch-courselesson/3/2017529040145-lr7aq5.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496044905,3,1496044905),(100,'courselesson/3/2017529040418-lzr2u9.pdf',3,'courselesson','课程3实验6HADOOP基本操作-实验指南.pdf','pdf',348747,'',0,'ch-courselesson/3/2017529040418-lzr2u9.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496045058,3,1496045058),(101,'courselesson/3/2017529040451-6jryrh.pdf',3,'courselesson','hadoop-shell-学习资料.pdf','pdf',13400974,'',0,'ch-courselesson/3/2017529040451-6jryrh.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496045091,3,1496045091),(102,'courselesson/3/2017529040658-o7tt89.pdf',3,'courselesson','第4章_第3节_Map-Reduce计算框架（上）.pdf','pdf',725694,'',0,'ch-courselesson/3/2017529040658-o7tt89.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496045218,3,1496045218),(103,'courselesson/3/2017529040726-f2jtcd.pdf',3,'courselesson','第4章_第3节_Map-Reduce计算框架(下）.pdf','pdf',894528,'',0,'ch-courselesson/3/2017529040726-f2jtcd.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496045246,3,1496045246),(104,'courselesson/3/2017529040953-r51tuc.pdf',3,'courselesson','Hadoop实验-MapReduce(1).pdf','pdf',714014,'',0,'ch-courselesson/3/2017529040953-r51tuc.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496045393,3,1496045393),(105,'courselesson/3/2017529041033-q9tr0w.pdf',3,'courselesson','Hadoop实验-MapReduce(2).pdf','pdf',623837,'',0,'ch-courselesson/3/2017529041033-q9tr0w.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496045433,3,1496045433),(108,'courselesson/3/2017529041413-d0y2vc.pdf',3,'courselesson','第4章_第4节_Hadoop2.x集群维护（上）.pdf','pdf',654759,'',0,'ch-courselesson/3/2017529041413-d0y2vc.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496045653,3,1496045653),(109,'courselesson/3/2017529041451-agq199.pdf',3,'courselesson','第四章讲义- Hadoop2.x框架基础.pdf','pdf',824267,'',0,'ch-courselesson/3/2017529041451-agq199.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496045691,3,1496045691),(110,'courselesson/4/2017529041754-5cwgew.pdf',4,'courselesson','第9章第1节_HBase概述.pdf','pdf',663891,'',0,'ch-courselesson/4/2017529041754-5cwgew.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496045874,3,1496045874),(111,'courselesson/4/2017529042108-79rbfn.pdf',4,'courselesson','第9章第2节_HBase体系架构.pdf','pdf',746462,'',0,'ch-courselesson/4/2017529042108-79rbfn.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496046068,3,1496046068),(112,'courselesson/4/2017529042151-rog7r6.pdf',4,'courselesson','第9章第3节_HBase常用操作.pdf','pdf',603314,'',0,'ch-courselesson/4/2017529042151-rog7r6.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496046111,3,1496046111),(114,'courselesson/4/2017529042322-am2o20.pdf',4,'courselesson','第10章第1节_HBase架构详解.pdf','pdf',708494,'',0,'ch-courselesson/4/2017529042322-am2o20.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496046202,3,1496046202),(115,'courselesson/4/2017529042353-hxvoeu.pdf',4,'courselesson','第10章第2节_HBase高级特性.pdf','pdf',751976,'',0,'ch-courselesson/4/2017529042353-hxvoeu.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496046233,3,1496046233),(116,'courselesson/4/2017529042420-q344pj.pdf',4,'courselesson','第10章第3节_HBase表操作.pdf','pdf',661924,'',0,'ch-courselesson/4/2017529042420-q344pj.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496046260,3,1496046260),(117,'courselesson/4/2017529042449-2htvk8.pdf',4,'courselesson','HBase的安装和系列实验.pdf','pdf',922636,'',0,'ch-courselesson/4/2017529042449-2htvk8.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496046289,3,1496046289),(118,'courselesson/4/2017529084301-jixuv5.mp4',4,'courselesson','Hbase_1.mp4','mp4',17752664,'',0,'ch-courselesson/4/2017529084301-jixuv5.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496061781,3,1496061781),(120,'courselesson/5/2017529084707-9v66gy.pdf',5,'courselesson','第5章第1节-Hive概述.pdf','pdf',2182752,'',0,'ch-courselesson/5/2017529084707-9v66gy.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496062027,3,1496062027),(121,'courselesson/5/2017529084745-ru937e.pdf',5,'courselesson','第5章第2节-Hive的体系结构.pdf','pdf',1476528,'',0,'ch-courselesson/5/2017529084745-ru937e.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496062065,3,1496062065),(122,'courselesson/5/2017529084846-kwehqs.pdf',5,'courselesson','第5章第3节-Hive数据类型和文件格式.pdf','pdf',2223644,'',0,'ch-courselesson/5/2017529084846-kwehqs.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496062126,3,1496062126),(123,'courselesson/5/2017529085001-l09i8n.pdf',5,'courselesson','第5章第4节-Hive常用操作方式.pdf','pdf',2602874,'',0,'ch-courselesson/5/2017529085001-l09i8n.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496062201,3,1496062201),(124,'courselesson/5/2017529085052-yopde7.pdf',5,'courselesson','第5章第5节-HiveQL数据定义.pdf','pdf',5827672,'',0,'ch-courselesson/5/2017529085052-yopde7.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496062252,3,1496062252),(125,'courselesson/5/2017529085128-k2s0b9.pdf',5,'courselesson','第5章第6节-HiveQL查询.pdf','pdf',1690498,'',0,'ch-courselesson/5/2017529085128-k2s0b9.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496062288,3,1496062288),(126,'courselesson/5/2017529085257-ss9yvu.mp4',5,'courselesson','hive_1.mp4','mp4',30189738,'',0,'ch-courselesson/5/2017529085257-ss9yvu.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496062377,3,1496062377),(127,'courselesson/5/2017529085335-8oub59.mp4',5,'courselesson','hive_2.mp4','mp4',15758495,'',0,'ch-courselesson/5/2017529085335-8oub59.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496062415,3,1496062415),(128,'courselesson/6/2017529090158-5kk929.pdf',6,'courselesson','第12章_第1节_Spark on Yarn.pdf','pdf',1196159,'',0,'ch-courselesson/6/2017529090158-5kk929.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496062918,3,1496062918),(129,'courselesson/6/2017529090250-rjdgui.pdf',6,'courselesson','第12章_第2节_Spark HistoryServer.pdf','pdf',987398,'',0,'ch-courselesson/6/2017529090250-rjdgui.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496062970,3,1496062970),(130,'courselesson/6/2017529090330-ta0tl8.pdf',6,'courselesson','第12章_第3节_Spark Streaming.pdf','pdf',793832,'',0,'ch-courselesson/6/2017529090330-ta0tl8.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063010,3,1496063010),(131,'courselesson/6/2017529090437-fv4ymj.pdf',6,'courselesson','第12章_第5节_Spark 项目实战.pdf','pdf',1166135,'',0,'ch-courselesson/6/2017529090437-fv4ymj.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063077,3,1496063077),(132,'courselesson/6/2017529090524-sccp7y.pdf',6,'courselesson','讲义-第十二章 Spark高阶应用&实战.pdf','pdf',2243490,'',0,'ch-courselesson/6/2017529090524-sccp7y.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063124,3,1496063124),(133,'courselesson/7/2017529090644-tbeu3w.pdf',7,'courselesson','第7章_第1节大数据协作框架_Sqoop（上）.pdf','pdf',842898,'',0,'ch-courselesson/7/2017529090644-tbeu3w.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063204,3,1496063204),(134,'courselesson/7/2017529090732-hrlwuj.pdf',7,'courselesson','第7章_第1节大数据协作框架_Sqoop（下）.pdf','pdf',2469382,'',0,'ch-courselesson/7/2017529090732-hrlwuj.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063252,3,1496063252),(135,'courselesson/7/2017529090824-7hgyb9.pdf',7,'courselesson','第7章_第2节大数据协作框架_Flume.pdf','pdf',1798531,'',0,'ch-courselesson/7/2017529090824-7hgyb9.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063304,3,1496063304),(136,'courselesson/7/2017529090908-e305pb.pdf',7,'courselesson','第7章_第3节大数据协作框架_Kafka.pdf','pdf',3970131,'',0,'ch-courselesson/7/2017529090908-e305pb.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063348,3,1496063348),(137,'courselesson/7/2017529090951-n6iuee.pdf',7,'courselesson','第7章_第4节大数据协作框架_Phoenix.pdf','pdf',1496988,'',0,'ch-courselesson/7/2017529090951-n6iuee.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063391,3,1496063391),(138,'courselesson/7/2017529091027-t54o9u.pdf',7,'courselesson','第七章 大数据协作框架（讲义）.pdf','pdf',756304,'',0,'ch-courselesson/7/2017529091027-t54o9u.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063427,3,1496063427),(139,'courselesson/8/2017529091524-q0bt26.pdf',8,'courselesson','第8章_第1节_实时流式计算概述.pdf','pdf',1808871,'',0,'ch-courselesson/8/2017529091524-q0bt26.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063724,3,1496063724),(140,'courselesson/8/2017529091617-qorzn9.pdf',8,'courselesson','第8章_第2节_实时流式计算的应用场景及关键技术.pdf','pdf',1842751,'',0,'ch-courselesson/8/2017529091617-qorzn9.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063777,3,1496063777),(141,'courselesson/8/2017529091659-ma2g1z.pdf',8,'courselesson','第8章_第3节_概述Storm.pdf','pdf',2203711,'',0,'ch-courselesson/8/2017529091659-ma2g1z.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063819,3,1496063819),(142,'courselesson/8/2017529091743-orbuzs.pdf',8,'courselesson','第8章_第4节_Storm架构和原理.pdf','pdf',2922033,'',0,'ch-courselesson/8/2017529091743-orbuzs.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063863,3,1496063863),(143,'courselesson/8/2017529091811-7yuttb.pdf',8,'courselesson','第8章_第5节_Storm集群搭建部署.pdf','pdf',3385575,'',0,'ch-courselesson/8/2017529091811-7yuttb.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063891,3,1496063891),(144,'courselesson/8/2017529091852-1k5471.pdf',8,'courselesson','第8章_第6节_Storm常见问题与解决方法.pdf','pdf',1397529,'',0,'ch-courselesson/8/2017529091852-1k5471.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063932,3,1496063932),(145,'courselesson/8/2017529091959-2pdj3e.pdf',8,'courselesson','第八章 讲义-流式大数据处理.pdf','pdf',762837,'',0,'ch-courselesson/8/2017529091959-2pdj3e.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496063999,3,1496063999),(146,'courselesson/9/2017529092055-nbr77t.pdf',9,'courselesson','第11章_第1节_Spark以及Spark生态圈的介绍.pdf','pdf',1128728,'',0,'ch-courselesson/9/2017529092055-nbr77t.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496064055,3,1496064055),(147,'courselesson/9/2017529092139-evyzbh.pdf',9,'courselesson','第11章_第2节_Spark集群搭建.pdf','pdf',1437524,'',0,'ch-courselesson/9/2017529092139-evyzbh.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496064099,3,1496064099),(148,'courselesson/9/2017529092254-6dxj55.pdf',9,'courselesson','第11章_第3节_Spark应用提交工具.pdf','pdf',1764815,'',0,'ch-courselesson/9/2017529092254-6dxj55.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496064174,3,1496064174),(149,'courselesson/9/2017529092347-glppmj.pdf',9,'courselesson','第11章_第4节_Scala基本知识讲解.pdf','pdf',2710934,'',0,'ch-courselesson/9/2017529092347-glppmj.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496064227,3,1496064227),(150,'courselesson/9/2017529092446-ignhrq.mp4',9,'courselesson','spark.mp4','mp4',50755145,'',0,'ch-courselesson/9/2017529092446-ignhrq.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496064286,3,1496064286),(151,'courselesson/9/2017529092548-dtsh86.pdf',9,'courselesson','第11章_第5节_RDD特性、常见操作、缓存策略.pdf','pdf',1738320,'',0,'ch-courselesson/9/2017529092548-dtsh86.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496064348,3,1496064348),(152,'courselesson/9/2017529092646-1fqul6.pdf',9,'courselesson','第11章_第6节_RDD Dependency、Stage .pdf','pdf',2638139,'',0,'ch-courselesson/9/2017529092646-1fqul6.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496064406,3,1496064406),(153,'courselesson/9/2017529092722-l7pe59.pdf',9,'courselesson','第11章_第7节_Spark核心组件概述及案例分析.pdf','pdf',941372,'',0,'ch-courselesson/9/2017529092722-l7pe59.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496064442,3,1496064442),(154,'courselesson/9/2017529092830-geqh2j.pdf',9,'courselesson','课程3实验11SPARK程序开发-实验指南.pdf','pdf',843650,'',0,'ch-courselesson/9/2017529092830-geqh2j.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1496064510,3,1496064510),(155,'courselesson/3/2017530060437-28iz05.mp4',3,'courselesson','1.Hadoop概述.mp4','mp4',31428014,'',0,'ch-courselesson/3/2017530060437-28iz05.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496138677,3,1496138677),(156,'courselesson/4/2017530060554-nquhab.mp4',4,'courselesson','2.HBase概述.mp4','mp4',20450852,'',0,'ch-courselesson/4/2017530060554-nquhab.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496138754,3,1496138754),(157,'courselesson/4/2017530060709-7g8uma.mp4',4,'courselesson','2.HBase术语及数据模型.mp4','mp4',13844746,'',0,'ch-courselesson/4/2017530060709-7g8uma.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496138829,3,1496138829),(158,'courselesson/5/2017530060739-dkcojg.mp4',5,'courselesson','3.Hive简介.mp4','mp4',37191503,'',0,'ch-courselesson/5/2017530060739-dkcojg.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496138859,3,1496138859),(159,'courselesson/5/2017530060805-iteuv5.mp4',5,'courselesson','3.Hive与HBase关系.mp4','mp4',10540350,'',0,'ch-courselesson/5/2017530060805-iteuv5.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496138885,3,1496138885),(160,'courselesson/9/2017530060836-kbr5as.mp4',9,'courselesson','4.Spark简介.mp4','mp4',17281530,'',0,'ch-courselesson/9/2017530060836-kbr5as.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496138916,3,1496138916),(161,'courselesson/9/2017530060857-q8yhd5.mp4',9,'courselesson','4.Spark计算模型.mp4','mp4',26016651,'',0,'ch-courselesson/9/2017530060857-q8yhd5.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496138937,3,1496138937),(162,'courselesson/3/2017531115450-8jn5wc.mp4',3,'courselesson','hadoop_1.mp4','mp4',8941321,'',0,'ch-courselesson/3/2017531115450-8jn5wc.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496246090,3,1496246090),(163,'courselesson/3/2017531115536-7078kq.mp4',3,'courselesson','hadoop_2.mp4','mp4',53183415,'',0,'ch-courselesson/3/2017531115536-7078kq.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496246136,3,1496246136),(164,'courselesson/3/2017531115605-gxtbte.mp4',3,'courselesson','hadoop_3.mp4','mp4',8764757,'',0,'ch-courselesson/3/2017531115605-gxtbte.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496246165,3,1496246165),(165,'courselesson/3/2017531115751-ed67gg.mp4',3,'courselesson','hdfs.mp4','mp4',24913217,'',0,'ch-courselesson/3/2017531115751-ed67gg.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496246271,3,1496246271),(166,'courselesson/4/2017531115855-cd8dvb.mp4',4,'courselesson','Hbase_1.mp4','mp4',17752664,'',0,'ch-courselesson/4/2017531115855-cd8dvb.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,0,3,1496246335,3,1496246335),(167,'courselesson/4/2017531115929-tuhxzb.mp4',4,'courselesson','Hbase_2.mp4','mp4',39902804,'',0,'ch-courselesson/4/2017531115929-tuhxzb.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1496246369,3,1496246369),(168,'courselesson/10/2017621105749-4fikn6.pdf',10,'courselesson','OpenStack主要组件.pdf','pdf',577089,'',0,'ch-courselesson/10/2017621105749-4fikn6.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1498057069,3,1498057069),(169,'courselesson/10/2017621105832-h4oidu.pdf',10,'courselesson','Devstack搭建OpenStack.pdf','pdf',444791,'',0,'ch-courselesson/10/2017621105832-h4oidu.pdf',0,'success',NULL,NULL,NULL,'document','local',1,1,1,3,1498057112,3,1498057112),(170,'courselesson/10/2017622102055-j93aam.mp4',10,'courselesson','te-3.mp4','mp4',34869953,'',0,'ch-courselesson/10/2017622102055-j93aam.mp4',0,'success',NULL,NULL,NULL,'video','local',1,1,1,3,1498141255,3,1498141255);
/*!40000 ALTER TABLE `upload_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `upload_files_share`
--

DROP TABLE IF EXISTS `upload_files_share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload_files_share` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sourceUserId` int(10) unsigned NOT NULL COMMENT '上传文件的用户ID',
  `targetUserId` int(10) unsigned NOT NULL COMMENT '文件分享目标用户ID',
  `isActive` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效',
  `createdTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatedTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `webCode` char(15) DEFAULT '' COMMENT 'web代号',
  `owebPriv` char(3) DEFAULT '-' COMMENT '其他web权限（-=>无，wr=>读写，r=>只读）',
  PRIMARY KEY (`id`),
  KEY `webCode` (`webCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `upload_files_share`
--

LOCK TABLES `upload_files_share` WRITE;
/*!40000 ALTER TABLE `upload_files_share` DISABLE KEYS */;
/*!40000 ALTER TABLE `upload_files_share` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userNum` varchar(20) NOT NULL,
  `nickname` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '登录密码',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '注册邮箱',
  `verifiedMobile` varchar(255) NOT NULL DEFAULT '' COMMENT '验证手机号',
  `loginTime` int(11) NOT NULL DEFAULT '0',
  `loginIp` varchar(64) NOT NULL DEFAULT '',
  `loginSessionId` varchar(255) NOT NULL DEFAULT '',
  `roles` varchar(255) NOT NULL,
  `newNotificationNum` int(10) unsigned NOT NULL DEFAULT '0',
  `newMessageNum` int(10) unsigned NOT NULL DEFAULT '0',
  `smallAvatar` varchar(255) NOT NULL DEFAULT '',
  `mediumAvatar` varchar(255) NOT NULL DEFAULT '',
  `largeAvatar` varchar(255) NOT NULL DEFAULT '',
  `lock` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '是否锁定，0为否，1为是，锁定就不能登录了',
  `lockTime` int(11) unsigned DEFAULT NULL COMMENT '锁定的时间戳',
  `lockReasonType` enum('other','delete') DEFAULT 'other' COMMENT '锁定的原因',
  `salt` varchar(255) NOT NULL DEFAULT '' COMMENT '用户密码加盐',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'0032','学生','81dc9bdb52d04dc20036dbd8313ed055','student@qq.com','',1503673987,'127.0.0.1','','|ROLE_USER|',0,0,'','','',0,NULL,'other',''),(2,'1145','教师','81dc9bdb52d04dc20036dbd8313ed055','teacher@qq.com','',1492687894,'127.0.0.1','','|ROLE_USER|ROLE_TEACHER|',0,0,'','','',0,NULL,'other',''),(3,'admin','管理员','81dc9bdb52d04dc20036dbd8313ed055','admin@qq.com','',1505918783,'127.0.0.1','','|ROLE_USER|ROLE_TEACHER|ROLE_SUPER_ADMIN|',0,0,'','','',0,NULL,'other','');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `truename` varchar(255) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `gender` enum('male','female','secret') NOT NULL DEFAULT 'male' COMMENT '性别',
  `role` enum('student','teacher') NOT NULL DEFAULT 'student',
  `birthday` varchar(255) NOT NULL DEFAULT '' COMMENT '生日',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '职称，如教授',
  `title_level` varchar(100) NOT NULL DEFAULT '' COMMENT '级别，针对教师！',
  `telephone` varchar(100) NOT NULL DEFAULT '' COMMENT '电话号码（固定电话）',
  `research` varchar(255) NOT NULL DEFAULT '' COMMENT '研究方向',
  `division` varchar(100) NOT NULL DEFAULT '' COMMENT '系别，学部',
  `idcard` varchar(255) NOT NULL DEFAULT '' COMMENT '身份证号码',
  `grade` varchar(20) NOT NULL DEFAULT '' COMMENT '年级',
  `college` varchar(255) NOT NULL DEFAULT '' COMMENT '所属学院',
  `major` varchar(255) NOT NULL DEFAULT '' COMMENT '专业',
  `position` varchar(255) NOT NULL DEFAULT '' COMMENT '位置',
  `department` varchar(255) NOT NULL DEFAULT '' COMMENT '系别',
  `english_name` varchar(255) NOT NULL DEFAULT '' COMMENT '英文名',
  `nation` varchar(100) NOT NULL DEFAULT '' COMMENT '国籍',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profile`
--

LOCK TABLES `user_profile` WRITE;
/*!40000 ALTER TABLE `user_profile` DISABLE KEYS */;
INSERT INTO `user_profile` VALUES (1,'学生','male','teacher','','','','','','','','','实验教学中心','实验教学中心','','','',''),(2,'教师','male','teacher','','','','010-591298','自然语言处理','','','','实验教学中心','实验教学中心','','','',''),(3,'管理员','male','teacher','','','','','','','','','信息工程学院','网络工程专业','','','','');
/*!40000 ALTER TABLE `user_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_token`
--

DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'TOKEN编号',
  `token` varchar(64) NOT NULL COMMENT 'TOKEN值',
  `userId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TOKEN关联的用户ID',
  `type` varchar(255) NOT NULL COMMENT 'TOKEN类型',
  `data` text NOT NULL COMMENT 'TOKEN数据',
  `times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TOKEN的校验次数限制(0表示不限制)',
  `remainedTimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TOKE剩余校验次数',
  `expiredTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'TOKEN过期时间',
  `createdTime` int(10) unsigned NOT NULL COMMENT 'TOKEN创建时间',
  PRIMARY KEY (`id`),
  KEY `token_webCode` (`token`(60)) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=259 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_token`
--

LOCK TABLES `user_token` WRITE;
/*!40000 ALTER TABLE `user_token` DISABLE KEYS */;
INSERT INTO `user_token` VALUES (1,'q04bp2mldwgg44kw0k80owosoogwg0k',1,'fileupload','s:2:\"N;\";',0,0,1461313053,1461305853),(2,'tg0pzfmnz2so4sw044w4w88oo088404',1,'fileupload','s:2:\"N;\";',0,0,1462850789,1462843589),(3,'8u9h7nemn8wsow0sc4gkwgcsc0wg04k',1,'fileupload','s:2:\"N;\";',0,0,1462850821,1462843621),(4,'mubf2d8lcsg00s0kw8cc4o40ssw0kcs',1,'fileupload','s:2:\"N;\";',0,0,1462850833,1462843633),(5,'fa5x0po4y3sos4o4kgk44kgcgkow84s',701,'email-verify','s:2:\"N;\";',0,0,1468037999,1467951599),(6,'qngdw257qa8c404w4c4og4cgog4g40s',16,'fileupload','s:2:\"N;\";',0,0,1469284529,1469277329),(7,'fqlr4ql6ntsg0cgc0kw0gc4c88gs0ow',16,'fileupload','s:2:\"N;\";',0,0,1469349688,1469342488),(8,'sfry56pxe3kgwws880808swgw4cs0gw',16,'fileupload','s:2:\"N;\";',0,0,1469688016,1469680816),(9,'8bzoxxmqzzgo0w8w4ogkcococ0wcgs0',16,'fileupload','s:2:\"N;\";',0,0,1469690735,1469683535),(10,'kl17jtqljpws40gs80g4cgggsg0k0wk',16,'fileupload','s:2:\"N;\";',0,0,1469692953,1469685753),(11,'96p9sq2otgcg08scgg04gww4kgw08ww',16,'fileupload','s:2:\"N;\";',0,0,1469698199,1469690999),(12,'b0us00i80bcckk84ks8cggsc04gg4wk',16,'fileupload','s:2:\"N;\";',0,0,1469698878,1469691678),(13,'3jeak9xild4488k48sow4k8wg8cccko',16,'fileupload','s:2:\"N;\";',0,0,1469699697,1469692497),(14,'fnbfsdgltbco884gwowsc4ocsksw8gk',16,'fileupload','s:2:\"N;\";',0,0,1469700528,1469693328),(15,'jtw9qxr3nhw88kc8c4sw008og000ck0',16,'fileupload','s:2:\"N;\";',0,0,1469700816,1469693616),(16,'sayll18or00o0so8kkgwccwkccws8s',16,'fileupload','s:2:\"N;\";',0,0,1469950169,1469942969),(17,'6f6uxco9xzgook8kwsooc0w4ks4c4gs',16,'fileupload','s:2:\"N;\";',0,0,1469977223,1469970023),(18,'1vb42qweexc0w00ows8cocos848ckkk',16,'fileupload','s:2:\"N;\";',0,0,1469977681,1469970481),(19,'9ady4t48oc8wsw4cgsw8000gwow4cow',16,'fileupload','s:2:\"N;\";',0,0,1470033167,1470025967),(20,'ba4293e9ufww08w4c4w8ocw0oc0okk4',16,'fileupload','s:2:\"N;\";',0,0,1470034244,1470027044),(21,'jwrd3i47nhcgw044kc4ock0g0c0cwk4',16,'fileupload','s:2:\"N;\";',0,0,1470238646,1470231446),(22,'r9rmqo9byb4ss0k48w8o8o0ck0sg4s',16,'fileupload','s:2:\"N;\";',0,0,1470238654,1470231454),(23,'qqbvj6fi1us4s8gcogosgogggc8kks8',16,'fileupload','s:2:\"N;\";',0,0,1470238885,1470231685),(24,'bz5i70dxs80kcc00cowcs84o4wsoko8',12,'fileupload','s:2:\"N;\";',0,0,1470847199,1470839999),(25,'f69xokdhz484cgk48g8cs888k00skkw',12,'fileupload','s:2:\"N;\";',0,0,1470847288,1470840088),(26,'4k4erh53izuokcs44gwocscg48o80sg',12,'fileupload','s:2:\"N;\";',0,0,1470847429,1470840229),(27,'9d81s74lyz8c884k4g40kw8oo888s84',12,'fileupload','s:2:\"N;\";',0,0,1471008997,1471001797),(28,'jd0npft7dxk4csw8ws8wckoosc44o4g',12,'fileupload','s:2:\"N;\";',0,0,1471009103,1471001903),(29,'90il9q07hc00wk0ck40c4kcgsogss00',12,'fileupload','s:2:\"N;\";',0,0,1471009415,1471002215),(30,'26qsxd35ovr44c4cwsk04w4kckgg480',12,'fileupload','s:2:\"N;\";',0,0,1471009733,1471002533),(31,'c7ik910eptkw00s0c8oko0ok8cg8os0',12,'fileupload','s:2:\"N;\";',0,0,1471009953,1471002753),(32,'4azq1xs7lrs4o4w0s884g884k044840',12,'fileupload','s:2:\"N;\";',0,0,1471010075,1471002875),(33,'rzxj3pqxt8gwgss40gk0w4kog8k44so',12,'fileupload','s:2:\"N;\";',0,0,1471010144,1471002944),(34,'dpb8w1a3xn48w0oscckw8ookwosgow8',12,'fileupload','s:2:\"N;\";',0,0,1471010287,1471003087),(35,'m431c1j20xwg8sswk0gwkk08wkskc4o',12,'fileupload','s:2:\"N;\";',0,0,1471010352,1471003152),(36,'8mx6o0wtg5wcg8kgcg848ockc40wc4c',12,'fileupload','s:2:\"N;\";',0,0,1471010468,1471003268),(37,'avto5u0rn00sok40wksoo40gcswswcw',12,'fileupload','s:2:\"N;\";',0,0,1471010511,1471003311),(38,'b9wvp36nf3cowg00wg8wgo40000ck80',12,'fileupload','s:2:\"N;\";',0,0,1471011362,1471004162),(39,'imgkaqw1plkcock8c004c0ogows8gwk',12,'fileupload','s:2:\"N;\";',0,0,1471011590,1471004390),(40,'5ygpwuy2z9s84ckwsgwokgk04ss8w00',12,'fileupload','s:2:\"N;\";',0,0,1471011700,1471004500),(41,'2dpjw3si0ztwcgs0cw8ckw8oc48c0oc',12,'fileupload','s:2:\"N;\";',0,0,1471011915,1471004715),(42,'f8i6roonjbwwc0cgg44co4kgs8ckcco',12,'fileupload','s:2:\"N;\";',0,0,1471011958,1471004758),(43,'4gz7z7paxmaskcss4c4koks0gw4wk4w',12,'fileupload','s:2:\"N;\";',0,0,1471426849,1471419649),(44,'2bdlx8ec64n4kskk0kcgswsw88sskcw',12,'fileupload','s:2:\"N;\";',0,0,1471504995,1471497795),(45,'gq08t9dtyk8wsw4wc44o048oo4kokgs',12,'fileupload','s:2:\"N;\";',0,0,1471514318,1471507118),(46,'kwyjvlcro9sg4koogso80ogk8o84wc4',12,'fileupload','s:2:\"N;\";',0,0,1471793619,1471786419),(47,'j7o1m5il06gow4w88w4oggk0k8ocgcs',12,'fileupload','s:2:\"N;\";',0,0,1471856178,1471848978),(48,'i0zvm2mtohkw80s8wg4swg4kk48w0cg',12,'fileupload','s:2:\"N;\";',0,0,1471857391,1471850191),(49,'h9ronb9yncgscww4kckwowc8448ok08',12,'fileupload','s:2:\"N;\";',0,0,1471857934,1471850734),(50,'e3mlfqygdoo4wok44c08sc000og4sg8',12,'fileupload','s:2:\"N;\";',0,0,1471858182,1471850982),(51,'3s0kj03hvt6osc44848gkk0c0ok8ssk',12,'fileupload','s:2:\"N;\";',0,0,1471860097,1471852897),(52,'6ekydku5a4cgc4sswokoock0g4wswkk',12,'fileupload','s:2:\"N;\";',0,0,1471882467,1471875267),(53,'odu2bpkjznk4c4k00gsgogs0sc4oock',12,'fileupload','s:2:\"N;\";',0,0,1471882500,1471875300),(54,'jum9d72vvkg80gko8ssgcw4408w0ogc',12,'fileupload','s:2:\"N;\";',0,0,1471882557,1471875357),(55,'708s0gufck8ws4c00scokkkowwss40w',12,'fileupload','s:2:\"N;\";',0,0,1471882598,1471875398),(56,'n0ibxapz5f4o4c8s0s0cwowwooo8kkk',12,'fileupload','s:2:\"N;\";',0,0,1471883205,1471876005),(57,'8h06w23k9w8w8scwwcwsgcgsco0w0o4',12,'fileupload','s:2:\"N;\";',0,0,1471884073,1471876873),(58,'ofc4q36dcxcog44kk8ck8w0kwgock4w',12,'fileupload','s:2:\"N;\";',0,0,1471884520,1471877320),(59,'t9mnghpxmk0s8040o4o4484wsscs4kw',12,'fileupload','s:2:\"N;\";',0,0,1471939466,1471932266),(60,'a9bujxqw6d4w04ks4w0s4048448k840',12,'fileupload','s:2:\"N;\";',0,0,1471941085,1471933885),(61,'p1gayxve7y8w4gswcg80w4s4cgocko8',12,'fileupload','s:2:\"N;\";',0,0,1471941488,1471934288),(62,'4i84ks65gf8kw0kwwogwo0oc0ggw4go',12,'fileupload','s:2:\"N;\";',0,0,1472040471,1472033271),(63,'2837sukt8wsgk4c4sc4gg84wg084csg',12,'fileupload','s:2:\"N;\";',0,0,1472040607,1472033407),(64,'tvvdpswpvjksw4c4sw88occg0cgcg48',12,'fileupload','s:2:\"N;\";',0,0,1472040827,1472033627),(65,'fjay17h7wxkwcc8koook8c8sowow4k4',12,'fileupload','s:2:\"N;\";',0,0,1472041145,1472033945),(66,'ca1530fn4zs4koo8wkw840sggc80kko',12,'fileupload','s:2:\"N;\";',0,0,1472042009,1472034809),(67,'lpj82pl8ktwcs8k8gc0k00kss0cckoo',12,'fileupload','s:2:\"N;\";',0,0,1472043906,1472036706),(68,'fns1kxiyg288ko4ko4ow004cksko0ko',12,'fileupload','s:2:\"N;\";',0,0,1472044320,1472037120),(69,'fkftjkkyfvw4sw8g8wskg8c08gss8cw',12,'fileupload','s:2:\"N;\";',0,0,1472106935,1472099735),(70,'acyvqpc3ti8gssg0gokoc40oowkcswo',12,'fileupload','s:2:\"N;\";',0,0,1472107119,1472099919),(71,'q8j4vlx38e8444c8c4sowc4wo0kkok4',12,'fileupload','s:2:\"N;\";',0,0,1472107166,1472099966),(72,'o3bwvuxd034o00w8c4sswo0888ggkg4',12,'fileupload','s:2:\"N;\";',0,0,1472107409,1472100209),(73,'bm76pyyfo9skgc8w4swcwgo8o08swcw',12,'fileupload','s:2:\"N;\";',0,0,1472107659,1472100459),(74,'ef09geyj8zcw00gkkk8kwk0gg80w08o',12,'fileupload','s:2:\"N;\";',0,0,1472108195,1472100995),(75,'jacn0tgkm74k4048c4oo0c4ksgs8wk4',12,'fileupload','s:2:\"N;\";',0,0,1472108489,1472101289),(76,'6m0n8ppzz8cg0occc00s8cs4wogg8ow',12,'fileupload','s:2:\"N;\";',0,0,1472108601,1472101401),(77,'8eawaau8adc04kc8so84gw08s0cggkw',12,'fileupload','s:2:\"N;\";',0,0,1472108684,1472101484),(78,'hzg03lihcdckgco0kko00skgs4ss4c0',12,'fileupload','s:2:\"N;\";',0,0,1472108734,1472101534),(79,'58is8da4gicckw8g4k4g84w8o0kg084',12,'fileupload','s:2:\"N;\";',0,0,1472109045,1472101845),(80,'plz19zrono0cw44oocokwcs4ggs8g8g',12,'fileupload','s:2:\"N;\";',0,0,1472109408,1472102208),(81,'1q09otg2s1xc48ogsg8gckkkg0cksc8',12,'fileupload','s:2:\"N;\";',0,0,1472109591,1472102391),(82,'bgdfgbhq5zsc404kcgkw8g4swskckgw',12,'fileupload','s:2:\"N;\";',0,0,1472109920,1472102720),(83,'djt2qr0z4rsokw80sks0kccskwocs0w',12,'fileupload','s:2:\"N;\";',0,0,1472109954,1472102754),(84,'su2td2jgmk0oo0wc8o8cgg80csk0ogk',12,'fileupload','s:2:\"N;\";',0,0,1472110178,1472102978),(85,'3tpd39x5yfqckwgc0sw84ss0g8go0gg',12,'fileupload','s:2:\"N;\";',0,0,1472110199,1472102999),(86,'4gg1yv7hwnwg4ogwwcocgos00k884cw',12,'fileupload','s:2:\"N;\";',0,0,1472110262,1472103062),(87,'a7optgccs1s0sco8ok4k0gcswgsgg84',12,'fileupload','s:2:\"N;\";',0,0,1472110929,1472103729),(88,'7h0rr93h6uosw0wkks880ow0g0ws4g8',12,'fileupload','s:2:\"N;\";',0,0,1472110966,1472103766),(89,'mg9g9hwoclwosswkck88k08so88o0g0',12,'fileupload','s:2:\"N;\";',0,0,1472111018,1472103818),(90,'1cguga6xszesgw8g4ogwwoo0csssk0k',12,'fileupload','s:2:\"N;\";',0,0,1472111176,1472103976),(91,'18ktx1qprtwk088kkwc0w48sgs8sssc',12,'fileupload','s:2:\"N;\";',0,0,1472544858,1472537658),(92,'f87yn87d18o4o4wscg4wo4ow4sokgss',12,'fileupload','s:2:\"N;\";',0,0,1472546994,1472539794),(93,'n1zlvf1mpz40gwww00w8ck0ooc880o0',12,'fileupload','s:2:\"N;\";',0,0,1472547170,1472539970),(94,'64ircbkwyuos0gs40oc0o8g0ow0g8s',12,'fileupload','s:2:\"N;\";',0,0,1472547225,1472540025),(95,'nhzxfepyr4048gssow4ogs0wo8c0o4g',12,'fileupload','s:2:\"N;\";',0,0,1472547313,1472540113),(96,'iwyk6dv3nhkokk8804k4ckgccckok04',5,'fileupload','s:2:\"N;\";',0,0,1473684426,1473677226),(97,'lwdiaa1km9wgw4sso0w4kw480owcsws',1,'fileupload','s:2:\"N;\";',0,0,1473753118,1473745918),(98,'r3xv74ceog04cko884wo4gswwwo0sg0',1,'fileupload','s:2:\"N;\";',0,0,1473753366,1473746166),(99,'qyfb8enxeggskgcsgw8wgcg8w0wg8cg',230,'fileupload','s:2:\"N;\";',0,0,1473771191,1473763991),(100,'gvy8ll2p9b4k4s0c04ckg840ws0ock4',230,'fileupload','s:2:\"N;\";',0,0,1473771422,1473764222),(101,'1dflxmgc9ha84gcww0cc8o0kwgwg444',1,'fileupload','s:2:\"N;\";',0,0,1474351025,1474343825),(102,'pjh0xh2phtwkwgs4o0kowcwks488g8',1,'fileupload','s:2:\"N;\";',0,0,1474351148,1474343948),(103,'l2jqw55h69s0sks0koc0sk4so0swc8w',1,'fileupload','s:2:\"N;\";',0,0,1474351478,1474344278),(104,'1boc163bara8oc4kss044sogskwcow0',1,'fileupload','s:2:\"N;\";',0,0,1474351548,1474344348),(105,'ikqhmm7v7g0skgkoo84sso8gssook0s',1,'fileupload','s:2:\"N;\";',0,0,1474351804,1474344604),(106,'9ntnf9hs8aw4k0skswoog8kwk4w8kgs',1,'fileupload','s:2:\"N;\";',0,0,1474352257,1474345057),(107,'kpw36vxt3400g8w8w4kkcwgkk4o0c8w',1,'fileupload','s:2:\"N;\";',0,0,1474352564,1474345364),(108,'ryogcvtmz6s04gwg0k8s8w4o8gk4ccs',1,'fileupload','s:2:\"N;\";',0,0,1474352727,1474345527),(109,'56bscjwaczs44swok4kgso8kcsswc4s',1,'fileupload','s:2:\"N;\";',0,0,1474352879,1474345679),(110,'75p9t386h7cwc0c40s4kwsokc0c0cg4',1,'fileupload','s:2:\"N;\";',0,0,1474352971,1474345771),(111,'hati2fv7pzc44s0w8og0os0c0og4gc0',1,'fileupload','s:2:\"N;\";',0,0,1474353417,1474346217),(112,'nrrh1utkz4gco4s8k0wkcso8s444kgw',1,'fileupload','s:2:\"N;\";',0,0,1474353891,1474346691),(113,'qcc50nwmopw0oocks808g8o4w8kkw4g',1,'fileupload','s:2:\"N;\";',0,0,1474353984,1474346784),(114,'7z95eaduw9kwo4sw8csgocw8ggosgko',1,'fileupload','s:2:\"N;\";',0,0,1474539134,1474531934),(115,'8a8kkas6iqskw4g8o8wgwk0sssgc4og',1,'fileupload','s:2:\"N;\";',0,0,1474540723,1474533523),(116,'dwep93yk8w84kocckso4kk4c0o0o88',1,'fileupload','s:2:\"N;\";',0,0,1474541585,1474534385),(117,'rq0qpnam6kgwog8cw8k0o4cw4kwoc48',1,'fileupload','s:2:\"N;\";',0,0,1474541637,1474534437),(118,'ewdr1eupyu0ccs4ow8o40soskog84sk',230,'fileupload','s:2:\"N;\";',0,0,1476201765,1476194565),(119,'p1qc1hjby5w88cgkkg4kg8wos44wo40',230,'fileupload','s:2:\"N;\";',0,0,1476202167,1476194967),(120,'1hriaexqb84k4g4g0ckw48gckggw8kg',230,'fileupload','s:2:\"N;\";',0,0,1477109405,1477102205),(121,'sz1u2auwvk040g4swo0g0k0w8skwko8',2234,'fileupload','s:2:\"N;\";',0,0,1479357128,1479349928),(122,'mb2qhxrx8mscwg00o8k00w00w0wcgok',2234,'fileupload','s:2:\"N;\";',0,0,1479357171,1479349971),(123,'61emdz4izmo08osg4kokows0o4o00kk',80,'fileupload','s:2:\"N;\";',0,0,1480648445,1480641245),(124,'cizc1901bh4cs0scks4s4csg8ow4wwo',80,'fileupload','s:2:\"N;\";',0,0,1480648609,1480641409),(125,'8tl0eayss5s80kskcg88wk0ssk4gc8g',80,'fileupload','s:2:\"N;\";',0,0,1480648877,1480641677),(126,'94qcc3onxjk80o8c084sc8cssc044k8',80,'fileupload','s:2:\"N;\";',0,0,1480648923,1480641723),(127,'c1na2pm47hck00ks80w4o8swoowss4s',3,'fileupload','s:2:\"N;\";',0,0,1492319704,1492312504),(128,'tvrliblcqsgg4c4ko04sg4ggg88c0ww',3,'fileupload','s:2:\"N;\";',0,0,1492320405,1492313205),(129,'hxtjxidz4rccog80wo00oc0oc8ksw08',3,'fileupload','s:2:\"N;\";',0,0,1492320545,1492313345),(130,'3eplogpxen0gkssccws008kc0g0ocww',3,'fileupload','s:2:\"N;\";',0,0,1492320590,1492313390),(131,'e0uqp8a890080c0o8kwsws444s8kow',3,'fileupload','s:2:\"N;\";',0,0,1492320624,1492313424),(132,'f5xm9nb5krsoo0w0ow8o8cko0kkgggc',3,'fileupload','s:2:\"N;\";',0,0,1492322631,1492315431),(133,'dpcegfqltdwgos8owk400kkk8ss0ko0',3,'fileupload','s:2:\"N;\";',0,0,1492324078,1492316878),(134,'bdcw1n5vp9cgwk4cwg8kgsgo4cko8oc',3,'fileupload','s:2:\"N;\";',0,0,1492325503,1492318303),(135,'t4q552m4av40ggs84888cgoc04484gg',3,'fileupload','s:2:\"N;\";',0,0,1492325546,1492318346),(136,'4k379y55z40008wc8o88kowo0cco0kk',3,'fileupload','s:2:\"N;\";',0,0,1492325842,1492318642),(137,'6lalrg75z2scwosg0so4s0o0gs4ws4k',3,'fileupload','s:2:\"N;\";',0,0,1492694349,1492687149),(138,'to48lg9ft008kwkoogcc88wow4ko0ks',3,'fileupload','s:2:\"N;\";',0,0,1492694650,1492687450),(139,'kstba97kwpc8ko0sgs4skwww4s808sw',3,'fileupload','s:2:\"N;\";',0,0,1492694693,1492687493),(140,'13hftaj28ba8gg8404sssgoc48488wo',3,'fileupload','s:2:\"N;\";',0,0,1492694929,1492687729),(141,'9fidu4nhc2ccsocc0cssc8g04g4g8kk',2,'fileupload','s:2:\"N;\";',0,0,1492696025,1492688825),(142,'b9xfoikzy74gow4404g0sc0404s8c4s',2,'fileupload','s:2:\"N;\";',0,0,1492696159,1492688959),(143,'6oe2fu7n33ks0swk8ks48w0sw40cs4w',2,'fileupload','s:2:\"N;\";',0,0,1492697982,1492690782),(144,'1db0lbek97i8w40k8oo4oocgggo4og8',3,'fileupload','s:2:\"N;\";',0,0,1493821960,1493814760),(145,'ir5lhj44480kcogg880ookgc48cwsk0',3,'fileupload','s:2:\"N;\";',0,0,1493822051,1493814851),(146,'5j8oq3213fs48sog8k0c4gs8c804k08',3,'fileupload','s:2:\"N;\";',0,0,1493823061,1493815861),(147,'9mwbt6rvzawwc840kcw0oc8w8048w00',3,'fileupload','s:2:\"N;\";',0,0,1493823254,1493816054),(148,'je5gf7l9fxck8s08w4kgk0wkk44kcs8',3,'fileupload','s:2:\"N;\";',0,0,1493823541,1493816341),(149,'4vlewsiljwu8w4kw80w48kss8sgoows',3,'fileupload','s:2:\"N;\";',0,0,1493823567,1493816367),(150,'kkkart5uc80kc0k00ss4kg4cckockcc',3,'fileupload','s:2:\"N;\";',0,0,1493823600,1493816400),(151,'r777pam0iqswo4s0k8s4kogo0c8k0o8',3,'fileupload','s:2:\"N;\";',0,0,1494345126,1494337926),(152,'wom931sqoqo4kko4w0w8wgo4sssgw8',3,'fileupload','s:2:\"N;\";',0,0,1494346005,1494338805),(153,'i9dx8uu4qegoko0w8ksoccs8os4404s',3,'fileupload','s:2:\"N;\";',0,0,1494346115,1494338915),(154,'t4sommg63yss8cswsw0sok88ogo8w40',3,'fileupload','s:2:\"N;\";',0,0,1494734135,1494726935),(155,'23ddttbzhl7oss084owwo00gkwg4gwk',3,'fileupload','s:2:\"N;\";',0,0,1494734152,1494726952),(156,'7l6pc69gy7gow0k40448o4808ogkw4s',3,'fileupload','s:2:\"N;\";',0,0,1494734179,1494726979),(157,'l8tlm82xu1w4o8sswwg004sw4s4k0w0',3,'fileupload','s:2:\"N;\";',0,0,1494734942,1494727742),(158,'pw9acude674kscssw0c0ocwk8wks40c',3,'fileupload','s:2:\"N;\";',0,0,1494735004,1494727804),(159,'9yijwrxxepkww08g08wgosowkkw8o8c',3,'fileupload','s:2:\"N;\";',0,0,1494735117,1494727917),(160,'8qhr3rxteio0g4c884koowoscg8so00',3,'fileupload','s:2:\"N;\";',0,0,1494735176,1494727976),(161,'fezfrgdfpzww00w4sokw0ws80kko0os',3,'fileupload','s:2:\"N;\";',0,0,1494735202,1494728002),(162,'kwz9s845imo8k8wo0o84koc840wokg8',3,'fileupload','s:2:\"N;\";',0,0,1494735229,1494728029),(163,'e65e3re7m0owswoss08sosok8sk0wcg',3,'fileupload','s:2:\"N;\";',0,0,1494735576,1494728376),(164,'dxfe7tea3soww8s8kko004w8csg4wwo',3,'fileupload','s:2:\"N;\";',0,0,1494735642,1494728442),(165,'mwsvn3f2chco0ooo4c0008g4kg0ckgg',3,'fileupload','s:2:\"N;\";',0,0,1494735896,1494728696),(166,'aqor8p6q0jcwss88kckc4g0w4s4c0ss',3,'fileupload','s:2:\"N;\";',0,0,1494735914,1494728714),(167,'2gyilmn7wl0kc0gcgswo4g008cco8g0',3,'fileupload','s:2:\"N;\";',0,0,1494736140,1494728940),(168,'8x2hu33fzvwogko0c0ogg8o4wosswss',3,'fileupload','s:2:\"N;\";',0,0,1494736156,1494728956),(169,'c1i9n8wbc7wwk40wg8koccoos04o084',3,'fileupload','s:2:\"N;\";',0,0,1494736229,1494729029),(170,'ice6d2yuuqogg4wgsgc4gs4s8skkggw',3,'fileupload','s:2:\"N;\";',0,0,1494752518,1494745318),(171,'kf0bj2ozdq8wg80wok4c4k4o044gwg0',3,'fileupload','s:2:\"N;\";',0,0,1494752552,1494745352),(172,'lfv1yot3i9s4oc0ggw4cwwgk4scwk4g',3,'fileupload','s:2:\"N;\";',0,0,1494752595,1494745395),(173,'7ll1wohk7ncw48scskcsk0s8o4ckggs',3,'fileupload','s:2:\"N;\";',0,0,1494752631,1494745431),(174,'an4udhubu0gskcg0sk0wk8osw8skw80',3,'fileupload','s:2:\"N;\";',0,0,1494752689,1494745489),(175,'lqnx65shq4g0owcokcs00owss8oc8wc',3,'fileupload','s:2:\"N;\";',0,0,1494752722,1494745522),(176,'r9rb1qbpfxwcocs480osc8ck04sgkgc',3,'fileupload','s:2:\"N;\";',0,0,1494752748,1494745548),(177,'d87shz6tb0g0s0ckscg48o80oss80c4',3,'fileupload','s:2:\"N;\";',0,0,1495553017,1495545817),(178,'pk1wbx4bxz44swc0owsscoowosk4ow4',3,'fileupload','s:2:\"N;\";',0,0,1495553050,1495545850),(179,'3v2x2igym5k48wcogs4kgcggwg8gco8',3,'fileupload','s:2:\"N;\";',0,0,1496051673,1496044473),(180,'31icn1n9g0qoog8s0k8cg8k8g440kwo',3,'fileupload','s:2:\"N;\";',0,0,1496051692,1496044492),(181,'1hdk67e51f9cs4w0kwc4g8so4c0oss4',3,'fileupload','s:2:\"N;\";',0,0,1496051731,1496044531),(182,'jgyawzo633coow008ow8s0o4s4gg0kk',3,'fileupload','s:2:\"N;\";',0,0,1496051902,1496044702),(183,'nh3ajy1nzgg4oks4c4o4gg0ws800w08',3,'fileupload','s:2:\"N;\";',0,0,1496051951,1496044751),(184,'mcos7qx7feow4g8sko8wo80kw08sksg',3,'fileupload','s:2:\"N;\";',0,0,1496051977,1496044777),(185,'mzr0wnt9o8gswwc0g4c84880c8scsc',3,'fileupload','s:2:\"N;\";',0,0,1496051992,1496044792),(186,'4rtskot2yvc4kg0wssowg4w4gwso08k',3,'fileupload','s:2:\"N;\";',0,0,1496052083,1496044883),(187,'qwhayhtmqj4cwc844wogwwwkk4w004g',3,'fileupload','s:2:\"N;\";',0,0,1496052105,1496044905),(188,'9bn7pdc6o44ck8gcgkkg444c8kskswk',3,'fileupload','s:2:\"N;\";',0,0,1496052258,1496045058),(189,'t349aref8twcssgogkcwsgc488os84c',3,'fileupload','s:2:\"N;\";',0,0,1496052291,1496045091),(190,'2i15e4w3vjacw880s84sswswgk84g8k',3,'fileupload','s:2:\"N;\";',0,0,1496052418,1496045218),(191,'9pyym39hh4coc0gwcgoccc040g0gkwg',3,'fileupload','s:2:\"N;\";',0,0,1496052446,1496045246),(192,'h1bldh7tjtw04ossk4gc48w4c84s0so',3,'fileupload','s:2:\"N;\";',0,0,1496052593,1496045393),(193,'phz5nelapusw0s4s0k0g0gg4w84k0c4',3,'fileupload','s:2:\"N;\";',0,0,1496052633,1496045433),(194,'rmeyiazosiok40s4sk00ckgw4cw0o0w',3,'fileupload','s:2:\"N;\";',0,0,1496052702,1496045502),(195,'i4ff8mvwdmo00kko8cg04c8s08ss4g4',3,'fileupload','s:2:\"N;\";',0,0,1496052751,1496045551),(196,'5fspdxuwi7oc848gg4ggk4gg80c08g8',3,'fileupload','s:2:\"N;\";',0,0,1496052853,1496045653),(197,'kssctqov25sog0o8owgc0oc4coo88ww',3,'fileupload','s:2:\"N;\";',0,0,1496052891,1496045691),(198,'ofspxd2xt1cw8w0skk8s8scc8ws0swk',3,'fileupload','s:2:\"N;\";',0,0,1496053074,1496045874),(199,'94ghtzeja90kgwgk80s0kscg8488ok0',3,'fileupload','s:2:\"N;\";',0,0,1496053268,1496046068),(200,'dfiwsznxgf4kwk4kc4sgg480sw4gcww',3,'fileupload','s:2:\"N;\";',0,0,1496053311,1496046111),(201,'g11k1u2ke7cokw40sk8s0s4kcgos40w',3,'fileupload','s:2:\"N;\";',0,0,1496053373,1496046173),(202,'cjhtr6z1rk84sg4o4sgg84g8cg4gksc',3,'fileupload','s:2:\"N;\";',0,0,1496053402,1496046202),(203,'i9m10tyr4dck0ockskc04wsc8c0s004',3,'fileupload','s:2:\"N;\";',0,0,1496053433,1496046233),(204,'f9nt3ylado0sk4kks0gg0cwc0swokws',3,'fileupload','s:2:\"N;\";',0,0,1496053460,1496046260),(205,'q1uwhppnsdwcswc00kk84ow8k88cgk8',3,'fileupload','s:2:\"N;\";',0,0,1496053489,1496046289),(206,'lj23brzlcxs4kkkc8c4w4kw408cso48',3,'fileupload','s:2:\"N;\";',0,0,1496068981,1496061781),(207,'oce4bwuzpo0o08kswo4kw8osoowogo4',3,'fileupload','s:2:\"N;\";',0,0,1496069148,1496061948),(208,'1iscii5ez200kk0goc48o0g8cgoo00s',3,'fileupload','s:2:\"N;\";',0,0,1496069227,1496062027),(209,'m1y3rwe22sggc4s8k08ckowkwgkc00k',3,'fileupload','s:2:\"N;\";',0,0,1496069265,1496062065),(210,'spl7vvw1q40skok0wg88ossww8sc0w4',3,'fileupload','s:2:\"N;\";',0,0,1496069326,1496062126),(211,'an1btxjzow00gg888cc0cgs8wscc4sg',3,'fileupload','s:2:\"N;\";',0,0,1496069401,1496062201),(212,'551f4fno9dcs0gsswo0448cks44840c',3,'fileupload','s:2:\"N;\";',0,0,1496069452,1496062252),(213,'3hb2huhmcayos08o8oggswgskgoswg4',3,'fileupload','s:2:\"N;\";',0,0,1496069487,1496062287),(214,'1cjlk50mches08cwg8cks8ckw8cs04o',3,'fileupload','s:2:\"N;\";',0,0,1496069577,1496062377),(215,'nqxh0iaq7gg448cs0k0kwgwo0wk0csc',3,'fileupload','s:2:\"N;\";',0,0,1496069615,1496062415),(216,'lc92c09z71cko44cgk4s0ko4og44ww4',3,'fileupload','s:2:\"N;\";',0,0,1496070118,1496062918),(217,'30otlcsgfkmc0csoswkoo44w4w4sccc',3,'fileupload','s:2:\"N;\";',0,0,1496070170,1496062970),(218,'b121culax4gsosgscsgc0o8w4sgcs0g',3,'fileupload','s:2:\"N;\";',0,0,1496070210,1496063010),(219,'k9le6djmspcc4co0c0ow0cw0gkww80w',3,'fileupload','s:2:\"N;\";',0,0,1496070277,1496063077),(220,'drvkexzcujkkg0sgskkwgwks4o0wock',3,'fileupload','s:2:\"N;\";',0,0,1496070324,1496063124),(221,'cqw9lwsvd9ck0wwgco08kk0ccwwoo8w',3,'fileupload','s:2:\"N;\";',0,0,1496070404,1496063204),(222,'p308mbmzaisk8wg4oswck8ook8wg84s',3,'fileupload','s:2:\"N;\";',0,0,1496070452,1496063252),(223,'l0aq49waa348gsk0gso084owo0ok8ks',3,'fileupload','s:2:\"N;\";',0,0,1496070504,1496063304),(224,'nq2wcinm5sgc4wswsgck84o0gso80sw',3,'fileupload','s:2:\"N;\";',0,0,1496070548,1496063348),(225,'sehbg56iwao0k808swwsg8cgkkc0w8w',3,'fileupload','s:2:\"N;\";',0,0,1496070591,1496063391),(226,'kzlre3v3zj4gokw0gscocgsswkkg8o8',3,'fileupload','s:2:\"N;\";',0,0,1496070627,1496063427),(227,'2iilk05ob3y8ooo0cssos8sc44sgkg8',3,'fileupload','s:2:\"N;\";',0,0,1496070924,1496063724),(228,'i95elp1m0co4ow0gso8cwww0o0socko',3,'fileupload','s:2:\"N;\";',0,0,1496070977,1496063777),(229,'acr35iq6qxkc4sk4og08w4s408os8ws',3,'fileupload','s:2:\"N;\";',0,0,1496071019,1496063819),(230,'82zwdc9i6igwk08g8coo0okgcgcok4k',3,'fileupload','s:2:\"N;\";',0,0,1496071063,1496063863),(231,'c5hwfq81n2808owkw8w4w8ko8socgwo',3,'fileupload','s:2:\"N;\";',0,0,1496071091,1496063891),(232,'s5u1f4lxppck4ko80g0gkwsogg0k440',3,'fileupload','s:2:\"N;\";',0,0,1496071132,1496063932),(233,'c9a2my9tb0g00808oowk8cgk8c4k4o4',3,'fileupload','s:2:\"N;\";',0,0,1496071199,1496063999),(234,'arlhme6km3kgg04okkgow4s8wog8oos',3,'fileupload','s:2:\"N;\";',0,0,1496071255,1496064055),(235,'ioabciil1hk4scwcws4w8ggo0o8og84',3,'fileupload','s:2:\"N;\";',0,0,1496071299,1496064099),(236,'jlileaa0im0wkkcokgk0gcssk8gc8co',3,'fileupload','s:2:\"N;\";',0,0,1496071374,1496064174),(237,'1o2el83vvqu8ogwgw4wwossog0g8c04',3,'fileupload','s:2:\"N;\";',0,0,1496071427,1496064227),(238,'8p1wsjyunl0k04sgkw0cog4w4w4w88o',3,'fileupload','s:2:\"N;\";',0,0,1496071486,1496064286),(239,'o1rt6ijnqu8gwcwswgwkckokscskcws',3,'fileupload','s:2:\"N;\";',0,0,1496071548,1496064348),(240,'60qoehe0q3wowks40swskosogs0k8ok',3,'fileupload','s:2:\"N;\";',0,0,1496071606,1496064406),(241,'fq3f82rmjzwck84k0c800w4kgwkgwwo',3,'fileupload','s:2:\"N;\";',0,0,1496071642,1496064442),(242,'nigll7htbwg0o4c0w40000g00owswgk',3,'fileupload','s:2:\"N;\";',0,0,1496071710,1496064510),(243,'mtkj6a6icr48wcg4o4g4scsw0wwgcg0',3,'fileupload','s:2:\"N;\";',0,0,1496145877,1496138677),(244,'h37bk5q0vvsoo00gs0wggo0kg40sg4k',3,'fileupload','s:2:\"N;\";',0,0,1496145954,1496138754),(245,'9pylhsktf5kwgkkcswks4os8kgsoosk',3,'fileupload','s:2:\"N;\";',0,0,1496146029,1496138829),(246,'nrvejimai1w4kwgksw4og8kkoscwsgw',3,'fileupload','s:2:\"N;\";',0,0,1496146058,1496138858),(247,'ox484tarq5w8w8gggc48kg0cg8c4o00',3,'fileupload','s:2:\"N;\";',0,0,1496146085,1496138885),(248,'9036xxz9lfk0gw08w0kwwgs4ck8kcko',3,'fileupload','s:2:\"N;\";',0,0,1496146116,1496138916),(249,'pi30u78jp00c0c008gkgs8wgc04ksoo',3,'fileupload','s:2:\"N;\";',0,0,1496146137,1496138937),(250,'2k14prcg2c2s8cc8c0wc4g0k4csks8w',3,'fileupload','s:2:\"N;\";',0,0,1496253290,1496246090),(251,'3ktcyo0p5h2csggcsoc04000gcgs4w4',3,'fileupload','s:2:\"N;\";',0,0,1496253335,1496246135),(252,'28b70s2py51cg8co8coc0ws4sc80w44',3,'fileupload','s:2:\"N;\";',0,0,1496253365,1496246165),(253,'b3rr85tac2888wkc0soco4ggccwkcoc',3,'fileupload','s:2:\"N;\";',0,0,1496253470,1496246270),(254,'eazht2evs3cwgkg8gkccosk4kg4kww8',3,'fileupload','s:2:\"N;\";',0,0,1496253535,1496246335),(255,'kb3bj4s7fsgo8os40ck0cs40o0400s8',3,'fileupload','s:2:\"N;\";',0,0,1496253568,1496246368),(256,'nd7e20ewatwog0c8kwgg8kco8k88goo',3,'fileupload','s:2:\"N;\";',0,0,1498064269,1498057069),(257,'i41chcg85vs4kssoswk08wco44w0gkc',3,'fileupload','s:2:\"N;\";',0,0,1498064312,1498057112),(258,'psx1gno3v1cksockw0wcscg00ko4gk4',3,'fileupload','s:2:\"N;\";',0,0,1498148455,1498141255);
/*!40000 ALTER TABLE `user_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_vps`
--

DROP TABLE IF EXISTS `user_vps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_vps` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户、虚拟主机ID映射表',
  `userId` int(11) unsigned NOT NULL COMMENT '用户ID，目前是教师的ID',
  `vpsId` varchar(255) NOT NULL DEFAULT '' COMMENT '虚拟机的ID，以字符串的形式，唯一',
  `createTime` int(11) unsigned NOT NULL COMMENT '选择虚拟机的时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_vps`
--

LOCK TABLES `user_vps` WRITE;
/*!40000 ALTER TABLE `user_vps` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_vps` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-21  7:21:24
