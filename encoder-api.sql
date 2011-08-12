-- phpMyAdmin SQL Dump
-- version 3.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 12, 2011 at 02:55 PM
-- Server version: 5.0.91
-- PHP Version: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `encoder-api`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_destinations`
--

DROP TABLE IF EXISTS `api_destinations`;
CREATE TABLE IF NOT EXISTS `api_destinations` (
  `ad_index` int(10) NOT NULL default '0',
  `ad_name` varchar(50) collate utf8_unicode_ci default NULL,
  `ad_url` varchar(50) collate utf8_unicode_ci default NULL,
  `ad_ip` varchar(50) collate utf8_unicode_ci default NULL,
  `ad_update` datetime default NULL,
  PRIMARY KEY  (`ad_index`),
  KEY `ad_name` (`ad_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `api_destinations`
--

INSERT INTO `api_destinations` (`ad_index`, `ad_name`, `ad_url`, `ad_ip`, `ad_update`) VALUES
(1, 'admin-api', 'http://podcast-api-dev.open.ac.uk', '137.108.130.170', '2011-06-10 10:02:09'),
(2, 'admin-app', 'http://podcast-admin-dev.open.ac.uk/callback/', '137.108.130.70', '2011-06-10 10:01:59'),
(3, 'media-api', 'http://media-podcast-api-dev.open.ac.uk', '137.108.130.115', '2011-06-10 10:05:04'),
(4, 'encoder-api', 'http://kmi-encoder04/', '137.108.24.36', '2011-06-10 10:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `api_log`
--

DROP TABLE IF EXISTS `api_log`;
CREATE TABLE IF NOT EXISTS `api_log` (
  `al_index` int(10) NOT NULL auto_increment,
  `al_message` text collate utf8_unicode_ci,
  `al_debug` text collate utf8_unicode_ci NOT NULL,
  `al_reply` text collate utf8_unicode_ci,
  `al_timestamp` datetime default NULL,
  PRIMARY KEY  (`al_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=4 ;

--
-- Dumping data for table `api_log`
--

INSERT INTO `api_log` (`al_index`, `al_message`, `al_debug`, `al_reply`, `al_timestamp`) VALUES
(1, '', '', 'a:3:{s:6:"status";s:4:"NACK";s:4:"data";s:22:"No request values set!";s:9:"timestamp";i:1313157069;}', '2011-08-12 14:51:09'),
(2, '', '', 'a:3:{s:6:"status";s:4:"NACK";s:4:"data";s:22:"No request values set!";s:9:"timestamp";i:1313157178;}', '2011-08-12 14:52:58'),
(3, '', '', 'a:3:{s:6:"status";s:4:"NACK";s:4:"data";s:22:"No request values set!";s:9:"timestamp";i:1313157274;}', '2011-08-12 14:54:34');

-- --------------------------------------------------------

--
-- Table structure for table `api_process`
--

DROP TABLE IF EXISTS `api_process`;
CREATE TABLE IF NOT EXISTS `api_process` (
  `ap_index` int(10) NOT NULL auto_increment,
  `ap_process_id` int(10) default '0',
  `ap_script` varchar(50) collate utf8_unicode_ci default '0',
  `ap_timestamp` datetime NOT NULL,
  `ap_last_checked` datetime NOT NULL,
  `ap_status` enum('Y','N') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`ap_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=11377 ;

--
-- Dumping data for table `api_process`
--

INSERT INTO `api_process` (`ap_index`, `ap_process_id`, `ap_script`, `ap_timestamp`, `ap_last_checked`, `ap_status`) VALUES
(11376, 29477, 'curl -d "number=40&time=2" http://kmi-encoder04/po', '2011-08-12 11:53:31', '2011-08-12 11:54:40', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `command_routes`
--

DROP TABLE IF EXISTS `command_routes`;
CREATE TABLE IF NOT EXISTS `command_routes` (
  `cr_index` int(10) NOT NULL auto_increment,
  `cr_source` varchar(50) collate utf8_unicode_ci default NULL,
  `cr_destination` enum('admin-app','admin-api','encoder-api','media-api') collate utf8_unicode_ci default NULL,
  `cr_execute` enum('admin-app','admin-api','encoder-api','media-api') collate utf8_unicode_ci default NULL,
  `cr_action` varchar(50) collate utf8_unicode_ci default NULL,
  `cr_function` varchar(50) character set ucs2 collate ucs2_unicode_ci default NULL,
  `cr_callback` varchar(50) collate utf8_unicode_ci default NULL,
  `cr_route_type` enum('queue','direct') collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`cr_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=10 ;

--
-- Dumping data for table `command_routes`
--

INSERT INTO `command_routes` (`cr_index`, `cr_source`, `cr_destination`, `cr_execute`, `cr_action`, `cr_function`, `cr_callback`, `cr_route_type`) VALUES
(1, 'admin-api', 'encoder-api', 'encoder-api', 'encoder-pull-file', 'doEncoderPullFile', 'encoder-pull-file', 'queue'),
(2, 'admin-api', 'encoder-api', 'encoder-api', 'encoder-check-output', 'doEncoderCheckOutput', 'encoder-check-output', 'queue'),
(3, 'admin-api', 'encoder-api', 'encoder-api', 'encoder-push-to-media', 'doEncoderPushToMedia', 'encoder-push-to-media', 'queue'),
(4, 'admin-api', 'encoder-api', 'encoder-api', '', '', '', 'queue'),
(5, 'admin-api', 'encoder-api', 'encoder-api', '', '', '', 'queue'),
(6, 'admin-api', 'encoder-api', 'encoder-api', '', '', '', 'direct'),
(7, 'admin-api', 'encoder-api', 'encoder-api', '', '', '', 'direct'),
(8, 'admin-api', 'encoder-api', 'encoder-api', 'status-encoder', 'doStatusEncoder', 'status-encoder', 'direct'),
(9, 'admin-api', 'encoder-api', 'encoder-api', 'poll-encoder', 'doPollEncoder', 'poll-encoder', 'direct');

-- --------------------------------------------------------

--
-- Table structure for table `queue_commands`
--

DROP TABLE IF EXISTS `queue_commands`;
CREATE TABLE IF NOT EXISTS `queue_commands` (
  `cq_index` int(10) NOT NULL auto_increment,
  `cq_cq_index` int(10) NOT NULL default '0',
  `cq_mq_index` int(10) NOT NULL,
  `cq_step` int(10) NOT NULL,
  `cq_command` varchar(255) collate utf8_unicode_ci default '0',
  `cq_filename` varchar(255) collate utf8_unicode_ci NOT NULL,
  `cq_data` text collate utf8_unicode_ci,
  `cq_result` text collate utf8_unicode_ci,
  `cq_time` datetime default NULL,
  `cq_update` datetime default NULL,
  `cq_status` enum('Y','N','F','R','D') collate utf8_unicode_ci default 'N',
  PRIMARY KEY  (`cq_index`),
  KEY `ma_command` (`cq_cq_index`,`cq_command`),
  KEY `cq_filename` (`cq_filename`),
  KEY `cq_step` (`cq_step`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `watch_file`
--

DROP TABLE IF EXISTS `watch_file`;
CREATE TABLE IF NOT EXISTS `watch_file` (
  `wf_index` int(10) NOT NULL auto_increment,
  `wf_filename` varchar(255) collate utf8_unicode_ci default NULL,
  `wf_fileoutname` varchar(255) collate utf8_unicode_ci default NULL,
  `wf_folder` varchar(255) collate utf8_unicode_ci default NULL,
  `wf_extension` varchar(16) collate utf8_unicode_ci NOT NULL,
  `wf_filesize0` int(10) default NULL,
  `wf_filesize1` int(10) default NULL,
  `wf_filesize2` int(10) default NULL,
  `wf_filedate0` int(10) default NULL,
  `wf_filedate1` int(10) default NULL,
  `wf_filedate2` int(10) default NULL,
  `wf_count` int(10) default NULL,
  `wf_status` enum('R','W','U','X') collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`wf_index`),
  KEY `wf_filename` (`wf_filename`),
  KEY `wf_fileoutname` (`wf_fileoutname`),
  KEY `wf_filesize0` (`wf_filesize0`,`wf_filesize1`),
  KEY `wf_filesize1` (`wf_filesize1`,`wf_filesize2`),
  KEY `wf_count` (`wf_count`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=92 ;

--
-- Dumping data for table `watch_file`
--

INSERT INTO `watch_file` (`wf_index`, `wf_filename`, `wf_fileoutname`, `wf_folder`, `wf_extension`, `wf_filesize0`, `wf_filesize1`, `wf_filesize2`, `wf_filedate0`, `wf_filedate1`, `wf_filedate2`, `wf_count`, `wf_status`) VALUES
(1, '15074_dd205-mexico-economic-aspirations-w', '15074_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '', 13065588, 13065588, 13065588, 1312452975, 1312452975, 1312452975, 0, 'R'),
(2, '15075_dd205-mexico-economic-aspirations-w', '15075_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '', 13065588, 13065588, 13065588, 1312453600, 1312453600, 1312453600, 1, 'R'),
(3, '15076_dd205-mexico-economic-aspirations-w', '15076_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '', 13065588, 13065588, 13065588, 1312454030, 1312454030, 1312454030, 1, 'R'),
(4, '15077_dd205-mexico-economic-aspirations-w', '15077_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '', 13065588, 13065588, 13065588, 1312467541, 1312454153, 1312454153, 0, 'R'),
(5, '15078_dd205-mexico-economic-aspirations-w', '15078_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '', 13065588, 13065588, 13065588, 1312454198, 1312454198, 1312454198, 2, 'R'),
(6, '15079_dd205-mexico-economic-aspirations-w', '15079_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '', 13065588, 13065588, 13065588, 1312454258, 1312454258, 1312454258, 2, 'R'),
(7, '15080_dd205-mexico-economic-aspirations-w', '15080_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '', 13065588, 13065588, 13065588, 1312467368, 1312467368, 1312467368, 2, 'R'),
(8, '15081_dd205-mexico-economic-aspirations-w', '15081_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '', 13065588, 13065588, 13065588, 1312467337, 1312467337, 1312467337, 2, 'R'),
(9, '15082_dd205-mexico-economic-aspirations-w_01', '15082_dd205-mexico-economic-aspirations-w_01-480p.mov', 'video', '', 13065588, 13065588, 13065588, 1312468553, 1312468553, 1312468571, 2, 'R'),
(10, '15083_dd205-mexico-economic-aspirations-w_01', '15083_dd205-mexico-economic-aspirations-w_01-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312552658, 1312552658, 1312552658, 0, 'R'),
(11, '15084_Wildlife', '15084_Wildlife-360p.mov', 'video-wide', 'mov', 4999990, 4999990, 4999990, 1312770998, 1312770998, 1312770998, 2, 'W'),
(12, '15085_dd205-mexico-economic-aspirations-w', '15085_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312559359, 1312559381, 1312559359, 1, 'R'),
(13, '15086_dd205-mexico-economic-aspirations-w_01', '15086_dd205-mexico-economic-aspirations-w_01-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312562682, 1312562682, 1312562682, 2, 'R'),
(14, '15087_dd205-mexico-economic-aspirations-w_01', '15087_dd205-mexico-economic-aspirations-w_01-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312564011, 1312564011, 1312564011, 0, 'R'),
(25, '15088_dd205-mexico-economic-aspirations-w_01', '15088_dd205-mexico-economic-aspirations-w_01-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312798371, 1312798371, 1312798371, 1, 'R'),
(26, '15090_Wildlife', '15090_Wildlife-480p.mov', 'video', 'mov', 6502999, 6502999, 6502999, 1312819811, 1312819811, 1312819811, 0, 'R'),
(27, '15091_Wildlife', '15091_Wildlife-480p.mov', 'video', 'mov', 6502999, 6502999, 6502999, 1312877871, 1312877871, 1312877871, 1, 'R'),
(28, '15092_Wildlife', '15092_Wildlife-480p.mov', 'video', 'mov', 6502999, 6502999, 6502999, 1312879761, 1312879761, 1312879761, 1, 'R'),
(29, '15099_Wildlife', '15099_Wildlife-480p.mov', 'video', 'mov', 6502999, 6502999, 6502999, 1312882861, 1312882861, 1312882861, 1, 'R'),
(30, '15100_Wildlife', '15100_Wildlife-480p.mov', 'video', 'mov', 6502999, 6502999, 6502999, 1312883353, 1312883353, 1312883353, 0, 'R'),
(31, '15101_dd205-mexico-economic-aspirations-w_01', '15101_dd205-mexico-economic-aspirations-w_01-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312883441, 1312883441, 1312883441, 1, 'R'),
(32, '15102_dd205-mexico-economic-aspirations-w_01', '15102_dd205-mexico-economic-aspirations-w_01-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312885023, 1312885023, 1312885023, 1, 'R'),
(33, '15103_dd205-mexico-economic-aspirations-w', '15103_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312890472, 1312890472, 1312890472, 1, 'R'),
(34, '15104_dd205-mexico-economic-aspirations-w', '15104_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312891152, 1312891152, 1312891152, 1, 'R'),
(35, '15104_dd205-mexico-economic-aspirations-w', '15104_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, NULL, NULL, 1312891141, NULL, NULL, 0, 'W'),
(36, '15105_dd205-mexico-economic-aspirations-w', '15105_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312891912, 1312891912, 1312891912, 0, 'R'),
(37, '15106_dd205-mexico-economic-aspirations-w', '15106_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312892452, 1312892452, 1312892452, 0, 'R'),
(38, '15107_dd205-mexico-economic-aspirations-w', '15107_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312892731, 1312892731, 1312892731, 1, 'R'),
(39, '15107_dd205-mexico-economic-aspirations-w', '15107_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, NULL, NULL, 1312892710, NULL, NULL, 0, 'W'),
(40, '15108_dd205-mexico-economic-aspirations-w', '15108_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312892931, 1312892931, 1312892931, 1, 'R'),
(41, '15109_dd205-mexico-economic-aspirations-w', '15109_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312893152, 1312893152, 1312893152, 0, 'R'),
(42, '15110_dd205-mexico-economic-aspirations-w', '15110_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312893811, 1312893811, 1312893811, 0, 'R'),
(43, '15111_dd205-mexico-economic-aspirations-w', '15111_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312894052, 1312894052, 1312894052, 1, 'R'),
(44, '15112_dd205-mexico-economic-aspirations-w', '15112_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312894251, 1312894251, 1312894251, 2, 'R'),
(45, '15112_dd205-mexico-economic-aspirations-w', '15112_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, NULL, NULL, 1312894241, NULL, NULL, 0, 'W'),
(46, '15113_dd205-mexico-economic-aspirations-w', '15113_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312895092, 1312895092, 1312895092, 2, 'R'),
(47, '15114_dd205-mexico-economic-aspirations-w', '15114_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312895451, 1312895451, 1312895451, 0, 'R'),
(48, '15115_dd205-mexico-economic-aspirations-w', '15115_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312895561, 1312895561, 1312895561, 1, 'R'),
(49, '15116_dd205-mexico-economic-aspirations-w', '15116_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312895902, 1312895902, 1312895902, 2, 'R'),
(50, '15117_dd205-mexico-economic-aspirations-w', '15117_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312897251, 1312897251, 1312897251, 0, 'R'),
(51, '15118_Wildlife', '15118_Wildlife-480p.mov', 'video', 'mov', 6502999, 6502999, 6502999, 1312897921, 1312897921, 1312897921, 1, 'R'),
(52, '15119_dd205-mexico-economic-aspirations-w', '15119_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312899851, 1312899851, 1312899851, 2, 'R'),
(53, '15120_Wildlife', '15120_Wildlife-480p.mov', 'video', 'mov', 6502999, 6502999, 6502999, 1312900091, 1312900091, 1312900091, 1, 'R'),
(54, '15121_Wildlife', '15121_Wildlife-480p.mov', 'video', 'mov', 6502999, 6502999, 6502999, 1312900391, 1312900391, 1312900391, 1, 'R'),
(55, '15122_dd205-mexico-economic-aspirations-w', '15122_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312900641, 1312900641, 1312900641, 0, 'R'),
(56, '15123_Wildlife', '15123_Wildlife-480p.mov', 'video', 'mov', 6502999, 6502999, 6502999, 1312900811, 1312900811, 1312900811, 1, 'R'),
(57, '15124_dd205-mexico-economic-aspirations-g', '15124_dd205-mexico-economic-aspirations-g-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312901122, 1312901122, 1312901122, 0, 'R'),
(58, '15125_dd205-mexico-economic-aspirations-w', '15125_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312901123, 1312901123, 1312901123, 0, 'R'),
(59, '15126_dd205-mexico-economic-aspirations-g', '15126_dd205-mexico-economic-aspirations-g-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312901671, 1312901671, 1312901671, 1, 'R'),
(60, '15127_dd205-mexico-economic-aspirations-w', '15127_dd205-mexico-economic-aspirations-w-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312902081, 1312902081, 1312902081, 2, 'R'),
(61, '15128_dd205-mexico-economic-aspirations-g', '15128_dd205-mexico-economic-aspirations-g-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312903071, 1312903071, 1312903071, 1, 'R'),
(62, '15131_dd205-mexico-economic-aspirations-g', '15131_dd205-mexico-economic-aspirations-g-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312903371, 1312903371, 1312903371, 1, 'R'),
(63, '15132_dd205-mexico-economic-aspirations-g', '15132_dd205-mexico-economic-aspirations-g-480p.mov', 'video', 'mov', 13065588, 13065588, 13065588, 1312904031, 1312904031, 1312904031, 1, 'R'),
(64, '15133_dd205-mexico-economic-aspirations-w', '15133_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312910981, 1312910981, 1312910981, 1, 'R'),
(65, '15134_dd205-mexico-economic-aspirations-g', '15134_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312962391, 1312962391, 1312962391, 1, 'R'),
(66, '15137_dd205-mexico-economic-aspirations-g', '15137_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312964121, 1312964121, 1312964121, 2, 'R'),
(67, '15139_dd205-mexico-economic-aspirations-g', '15139_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312967291, 1312967291, 1312967291, 0, 'R'),
(68, '15146_dd205-mexico-economic-aspirations-w', '15146_dd205-mexico-economic-aspirations-w-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312969161, 1312969161, 1312969161, 2, 'R'),
(69, '15151_dd205-mexico-economic-aspirations-g', '15151_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312982919, 1312982919, 1312982919, 1, 'R'),
(70, '15152_dd205-mexico-economic-aspirations-g', '15152_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312983488, 1312983488, 1312983488, 2, 'R'),
(71, '15142_dd205-mexico-economic-aspirations-g', '15142_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312972601, 1312972601, 1312972601, 0, 'R'),
(72, '15147_dd205-mexico-economic-aspirations-g', '15147_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312982441, 1312982441, 1312982441, 0, 'R'),
(73, '15153_dd205-mexico-economic-aspirations-g', '15153_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312984131, 1312984131, 1312984131, 1, 'R'),
(74, '15157_dd205-mexico-economic-aspirations-g', '15157_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1312986521, 1312986521, 1312986521, 2, 'R'),
(75, '15167_HD_future_1280_5mb', '15167_HD_future_1280_5mb-480p.mov', 'video', '.mov', 6434115, 6434115, 6434115, 1313070241, 1313070241, 1313070241, 2, 'R'),
(76, '15168_8630-ps-5137', '15168_8630-ps-5137-480p.mov', 'video', '.mov', 68082846, 68082846, 68082846, 1313071555, 1313071555, 1313071555, 0, 'R'),
(85, '15171_test-works', '15171_test-works-480p.mov', 'video', '.mov', 12241073, 12241073, 12241073, 1313074450, 1313074450, 1313074450, 2, 'R'),
(86, '15173_barking_dog', '15173_barking_dog-480p.mov', 'video', '.mov', 319038, 319038, 319038, 1313075871, 1313075871, 1313075871, 0, 'R'),
(87, '15172_dd205-mexico-economic-aspirations-g', '15172_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1313075911, 1313075911, 1313075911, 1, 'R'),
(88, '15174_dd205-mexico-economic-aspirations-g', '15174_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1313076631, 1313076631, 1313076631, 1, 'R'),
(89, '15175_dd205-mexico-economic-aspirations-g', '15175_dd205-mexico-economic-aspirations-g-480p.mov', 'video', '.mov', 13065588, 13065588, 13065588, 1313077171, 1313077171, 1313077171, 2, 'R'),
(90, '15188_Wildlife', '15188_Wildlife-480p.mov', 'video', '.mov', 6502999, 6502999, 6502999, 1313133912, 1313133912, 1313133912, 1, 'R'),
(91, '15195_Wildlife', '15195_Wildlife-480p.mov', 'video', '.mov', 6502999, 6502999, 6502999, 1313146485, 1313146486, 1313146486, 2, 'R');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_map`
--

DROP TABLE IF EXISTS `workflow_map`;
CREATE TABLE IF NOT EXISTS `workflow_map` (
  `wm_index` int(10) NOT NULL auto_increment,
  `wm_workflow` varchar(255) collate utf8_unicode_ci default NULL,
  `wm_type` varchar(255) collate utf8_unicode_ci default NULL,
  `wm_aspect_ratio` varchar(255) collate utf8_unicode_ci default NULL,
  `wm_height` varchar(255) collate utf8_unicode_ci default NULL,
  `wm_watermark_bumper_trailer` varchar(255) collate utf8_unicode_ci default NULL,
  `wm_watermark_only` varchar(255) collate utf8_unicode_ci default NULL,
  `wm_nothing_added` varchar(255) collate utf8_unicode_ci default NULL,
  `wm_media_root` varchar(255) collate utf8_unicode_ci default NULL,
  `wm_outputfile` varchar(255) collate utf8_unicode_ci default NULL,
  `wm_mimetype` varchar(50) collate utf8_unicode_ci default NULL,
  `wm_target_folder` varchar(50) collate utf8_unicode_ci default NULL,
  `wm_flavour` varchar(50) collate utf8_unicode_ci default NULL,
  `wm_target_filename` varchar(50) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`wm_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=561 ;

--
-- Dumping data for table `workflow_map`
--

INSERT INTO `workflow_map` (`wm_index`, `wm_workflow`, `wm_type`, `wm_aspect_ratio`, `wm_height`, `wm_watermark_bumper_trailer`, `wm_watermark_only`, `wm_nothing_added`, `wm_media_root`, `wm_outputfile`, `wm_mimetype`, `wm_target_folder`, `wm_flavour`, `wm_target_filename`) VALUES
(1, 'video-wide-240-watermark-trailers', 'video', '16:9', '<= 240', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(2, 'video-wide-240-watermark-trailers', 'video', '16:9', '<= 240', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(3, 'video-wide-240-watermark-trailers', 'video', '16:9', '<= 240', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(4, 'video-wide-240-watermark', 'video', '16:9', '<= 240', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(5, 'video-wide-240-watermark', 'video', '16:9', '<= 240', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(6, 'video-wide-240-watermark', 'video', '16:9', '<= 240', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(7, 'video-wide-240', 'video', '16:9', '<= 240', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(8, 'video-wide-240', 'video', '16:9', '<= 240', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(9, 'video-wide-240', 'video', '16:9', '<= 240', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(10, 'video-wide-360-watermark-trailers', 'video', '16:9', '<= 360', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(11, 'video-wide-360-watermark-trailers', 'video', '16:9', '<= 360', 'Y', '', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(12, 'video-wide-360-watermark-trailers', 'video', '16:9', '<= 360', 'Y', '', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(13, 'video-wide-360-watermark-trailers', 'video', '16:9', '<= 360', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(14, 'video-wide-360-watermark-trailers', 'video', '16:9', '<= 360', 'Y', '', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(15, 'video-wide-360-watermark-trailers', 'video', '16:9', '<= 360', 'Y', '', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(16, 'video-wide-360-watermark-trailers', 'video', '16:9', '<= 360', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(17, 'video-wide-360-watermark', 'video', '16:9', '<= 360', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(18, 'video-wide-360-watermark', 'video', '16:9', '<= 360', '', 'Y', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(19, 'video-wide-360-watermark', 'video', '16:9', '<= 360', '', 'Y', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(20, 'video-wide-360-watermark', 'video', '16:9', '<= 360', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(21, 'video-wide-360-watermark', 'video', '16:9', '<= 360', '', 'Y', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(22, 'video-wide-360-watermark', 'video', '16:9', '<= 360', '', 'Y', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(23, 'video-wide-360-watermark', 'video', '16:9', '<= 360', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(24, 'video-wide-360', 'video', '16:9', '<= 360', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(25, 'video-wide-360', 'video', '16:9', '<= 360', '', '', 'Y', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(26, 'video-wide-360', 'video', '16:9', '<= 360', '', '', 'Y', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(27, 'video-wide-360', 'video', '16:9', '<= 360', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(28, 'video-wide-360', 'video', '16:9', '<= 360', '', '', 'Y', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(29, 'video-wide-360', 'video', '16:9', '<= 360', '', '', 'Y', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(30, 'video-wide-360', 'video', '16:9', '<= 360', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(31, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(32, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', 'Y', '', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(33, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', 'Y', '', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(34, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', 'Y', '', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(35, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(36, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', 'Y', '', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(37, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', 'Y', '', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(38, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', 'Y', '', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(39, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(40, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(41, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', 'Y', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(42, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', 'Y', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(43, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', 'Y', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(44, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(45, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', 'Y', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(46, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', 'Y', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(47, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', 'Y', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(48, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(49, 'video-wide-480', 'video', '16:9', '<= 480', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(50, 'video-wide-480', 'video', '16:9', '<= 480', '', '', 'Y', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(51, 'video-wide-480', 'video', '16:9', '<= 480', '', '', 'Y', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(52, 'video-wide-480', 'video', '16:9', '<= 480', '', '', 'Y', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(53, 'video-wide-480', 'video', '16:9', '<= 480', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(54, 'video-wide-480', 'video', '16:9', '<= 480', '', '', 'Y', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(55, 'video-wide-480', 'video', '16:9', '<= 480', '', '', 'Y', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(56, 'video-wide-480', 'video', '16:9', '<= 480', '', '', 'Y', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(57, 'video-wide-480', 'video', '16:9', '<= 480', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(58, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(59, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(60, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(61, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(62, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(63, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(64, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(65, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(66, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(67, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(68, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(69, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(70, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(71, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(72, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(73, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(74, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(75, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(76, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(77, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(78, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(79, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(80, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(81, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(82, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(83, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(84, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(85, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(86, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(87, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(88, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(89, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(90, 'video-wide-576', 'video', '16:9', '<= 576', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(91, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(92, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(93, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(94, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(95, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(96, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-720', '.m4v', '/', '720p', '-720.m4v'),
(97, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(98, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(99, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(100, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(101, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(102, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-hd', '.m4v', 'hd/', 'hd', '.m4v'),
(103, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(104, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(105, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(106, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(107, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(108, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(109, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-720', '.m4v', '/', '720p', '-720.m4v'),
(110, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(111, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(112, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(113, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(114, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(115, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-hd', '.m4v', 'hd/', 'hd', '.m4v'),
(116, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(117, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(118, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(119, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(120, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(121, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(122, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-720', '.m4v', '/', '720p', '-720.m4v'),
(123, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(124, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(125, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(126, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(127, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(128, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-hd', '.m4v', 'hd/', 'hd', '.m4v'),
(129, 'video-wide-720', 'video', '16:9', '<= 720', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(130, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(131, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(132, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(133, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(134, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(135, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-720', '.m4v', '/', '720p', '-720.m4v'),
(136, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-1080', '.m4v', '/', '1080p', '-1080.m4v'),
(137, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(138, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(139, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(140, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(141, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(142, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-hd', '.m4v', 'hd/', 'hd', '.m4v'),
(143, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-hd-1080', '.m4v', 'hd-1080/', 'hd-1080', '.m4v'),
(144, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(145, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(146, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(147, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(148, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(149, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(150, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-720', '.m4v', '/', '720p', '-720.m4v'),
(151, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-1080', '.m4v', '/', '1080p', '-1080.m4v'),
(152, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(153, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(154, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(155, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(156, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(157, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-hd', '.m4v', 'hd/', 'hd', '.m4v'),
(158, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-hd-1080', '.m4v', 'hd-1080/', 'hd-1080', '.m4v'),
(159, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(160, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(161, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(162, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(163, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(164, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(165, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-720', '.m4v', '/', '720p', '-720.m4v'),
(166, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-1080', '.m4v', '/', '1080p', '-1080.m4v'),
(167, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(168, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(169, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(170, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(171, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(172, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-hd', '.m4v', 'hd/', 'hd', '.m4v'),
(173, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-hd-1080', '.m4v', 'hd-1080/', 'hd-1080', '.m4v'),
(174, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(175, 'video-240-watermark-trailers', 'video', '4:3', '<= 240', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(176, 'video-240-watermark-trailers', 'video', '4:3', '<= 240', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(177, 'video-240-watermark-trailers', 'video', '4:3', '<= 240', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(178, 'video-240-watermark', 'video', '4:3', '<= 240', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(179, 'video-240-watermark', 'video', '4:3', '<= 240', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(180, 'video-240-watermark', 'video', '4:3', '<= 240', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(181, 'video-240', 'video', '4:3', '<= 240', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(182, 'video-240', 'video', '4:3', '<= 240', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(183, 'video-240', 'video', '4:3', '<= 240', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(184, 'video-360-watermark-trailers', 'video', '4:3', '<= 360', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(185, 'video-360-watermark-trailers', 'video', '4:3', '<= 360', 'Y', '', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(186, 'video-360-watermark-trailers', 'video', '4:3', '<= 360', 'Y', '', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(187, 'video-360-watermark-trailers', 'video', '4:3', '<= 360', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(188, 'video-360-watermark-trailers', 'video', '4:3', '<= 360', 'Y', '', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(189, 'video-360-watermark-trailers', 'video', '4:3', '<= 360', 'Y', '', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(190, 'video-360-watermark-trailers', 'video', '4:3', '<= 360', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(191, 'video-360-watermark', 'video', '4:3', '<= 360', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(192, 'video-360-watermark', 'video', '4:3', '<= 360', '', 'Y', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(193, 'video-360-watermark', 'video', '4:3', '<= 360', '', 'Y', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(194, 'video-360-watermark', 'video', '4:3', '<= 360', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(195, 'video-360-watermark', 'video', '4:3', '<= 360', '', 'Y', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(196, 'video-360-watermark', 'video', '4:3', '<= 360', '', 'Y', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(197, 'video-360-watermark', 'video', '4:3', '<= 360', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(198, 'video-360', 'video', '4:3', '<= 360', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(199, 'video-360', 'video', '4:3', '<= 360', '', '', 'Y', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(200, 'video-360', 'video', '4:3', '<= 360', '', '', 'Y', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(201, 'video-360', 'video', '4:3', '<= 360', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(202, 'video-360', 'video', '4:3', '<= 360', '', '', 'Y', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(203, 'video-360', 'video', '4:3', '<= 360', '', '', 'Y', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(204, 'video-360', 'video', '4:3', '<= 360', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(205, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(206, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', 'Y', '', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(207, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', 'Y', '', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(208, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', 'Y', '', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(209, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(210, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', 'Y', '', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(211, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', 'Y', '', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(212, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', 'Y', '', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(213, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(214, 'video-480-watermark', 'video', '4:3', '<= 480', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(215, 'video-480-watermark', 'video', '4:3', '<= 480', '', 'Y', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(216, 'video-480-watermark', 'video', '4:3', '<= 480', '', 'Y', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(217, 'video-480-watermark', 'video', '4:3', '<= 480', '', 'Y', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(218, 'video-480-watermark', 'video', '4:3', '<= 480', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(219, 'video-480-watermark', 'video', '4:3', '<= 480', '', 'Y', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(220, 'video-480-watermark', 'video', '4:3', '<= 480', '', 'Y', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(221, 'video-480-watermark', 'video', '4:3', '<= 480', '', 'Y', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(222, 'video-480-watermark', 'video', '4:3', '<= 480', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(223, 'video-480', 'video', '4:3', '<= 480', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(224, 'video-480', 'video', '4:3', '<= 480', '', '', 'Y', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(225, 'video-480', 'video', '4:3', '<= 480', '', '', 'Y', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(226, 'video-480', 'video', '4:3', '<= 480', '', '', 'Y', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(227, 'video-480', 'video', '4:3', '<= 480', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(228, 'video-480', 'video', '4:3', '<= 480', '', '', 'Y', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(229, 'video-480', 'video', '4:3', '<= 480', '', '', 'Y', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(230, 'video-480', 'video', '4:3', '<= 480', '', '', 'Y', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(231, 'video-480', 'video', '4:3', '<= 480', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(232, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(233, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(234, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(235, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(236, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(237, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(238, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(239, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(240, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(241, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(242, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', 'Y', '', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(243, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(244, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(245, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(246, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(247, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(248, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(249, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(250, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(251, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(252, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(253, 'video-576-watermark', 'video', '4:3', '<= 576', '', 'Y', '', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(254, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-240', '.m4v', '/', '240p', '-240.m4v'),
(255, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-270', '.m4v', '/', '270p', '-270.m4v'),
(256, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-360', '.m4v', '/', '360p', '-360.m4v'),
(257, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-480', '.m4v', '/', '480p', '-480.m4v'),
(258, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-540', '.m4v', '/', '540p', '-540.m4v'),
(259, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-ipod', '.m4v', 'ipod/', 'ipod', '.m4v'),
(260, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-iphone', '.m4v', 'iphone/', 'iphone', '.m4v'),
(261, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-iphonecellular', '.m4v', 'iphone/', 'iphonecellar', '.3gp'),
(262, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-desktop', '.m4v', 'desktop/', 'desktop', '.m4v'),
(263, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-large', '.m4v', 'large/', 'large', '.m4v'),
(264, 'video-576', 'video', '4:3', '<= 576', '', '', 'Y', './feeds', '-youtube', '.mov', 'youtube/', 'youtube', '.mov'),
(265, 'audio-trailers', 'audio', '-', '-', 'Y', '', '', './feeds', '-mp3', '.mp3', '/', 'default', '.mp3'),
(266, 'audio-trailers', 'audio', '', '', 'Y', '', '', './feeds', '-trailers-mp3', '.mp3', 'ipod-all/', 'ipod-all', '.mp3'),
(267, 'audio-trailers', 'audio', '', '', 'Y', '', '', '', '', '', 'desktop-all/', 'desktop-all', '.mp3'),
(268, 'audio-trailers', 'audio', '', '', 'Y', '', '', '', '', '', 'hd/', 'hd', '.mp3'),
(269, 'audio-trailers', 'audio', '', '', 'Y', '', '', '', '', '', 'hd-1080/', 'hd-1080', '.mp3'),
(270, 'audio-no-trailers', 'audio', '-', '-', '', '', 'Y', './feeds', '-mp3', '.mp3', '/', 'default', '.mp3'),
(271, 'audio-no-trailers', 'audio', '', '', '', '', 'Y', '', '', '', 'ipod-all/', 'ipod-all', '.mp3'),
(272, 'audio-no-trailers', 'audio', '', '', '', '', 'Y', '', '', '', 'desktop-all/', 'desktop-all', '.mp3'),
(273, 'audio-no-trailers', 'audio', '', '', '', '', 'Y', '', '', '', 'hd/', 'hd', '.mp3'),
(274, 'audio-no-trailers', 'audio', '', '', '', '', 'Y', '', '', '', 'hd-1080/', 'hd-1080', '.mp3'),
(275, 'video-wide', 'video', '', '', '', '', '', './feeds', '-360p', '.mov', '/', 'default', '.mov'),
(276, 'video', 'video', '', '', '', '', '', './feeds', '-480p', '.mov', '/', 'default', '.mov'),
(277, 'screencast-wide', 'video', '', '', '', '', '', './feeds', '-c', '.mov', '/', 'default', '.mov'),
(278, 'screencast', 'video', '', '', '', '', '', './feeds', '-c', '.mov', '/', 'default', '.mov'),
(279, 'multi-video-wide', 'video', '', '', '', '', '', './feeds', '-270p', '.mov', '/', '270p', '-270p.mov'),
(280, 'multi-video-wide', 'video', '', '', '', '', '', './feeds', '-360p', '.mov', '/', '360p', '-360p.mov'),
(281, 'multi-video-wide', 'video', '', '', '', '', '', './feeds', '-270p', '.webm', '/', '?', '-270p.webm'),
(282, 'multi-video-wide', 'video', '', '', '', '', '', './feeds', '-360p', '.webm', '/', '?', '-360p.webm'),
(283, 'multi-video', 'video', '', '', '', '', '', './feeds', '-360p', '.mov', '/', '270p', '-360p.mov'),
(284, 'multi-video', 'video', '', '', '', '', '', './feeds', '-480p', '.mov', '/', '360p', '-480p.mov'),
(285, 'multi-video', 'video', '', '', '', '', '', './feeds', '-360p', '.webm', '/', '?', '-360p.webm'),
(286, 'multi-video', 'video', '', '', '', '', '', './feeds', '-480p', '.webm', '/', '?', '-480p.webm'),
(287, 'audio', 'audio', '', '', '', '', '', './feeds', '-c', '.mp3', '/', 'default', '.mov'),
(289, 'video-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(290, 'video-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(291, 'video-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(292, 'video-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(293, 'video', 'video', '', '', '', '', '', './feeds-vle', '-480p', '.mov', 'desktop/', 'desktop', '.mov'),
(294, 'video', 'video', '', '', '', '', '', './feeds-vle', '-720p', '.mov', 'high/', '', '.mov'),
(295, 'video', 'video', '', '', '', '', '', './feeds-vle', '-1080p', '.mov', 'hd/', '', '.mov'),
(296, 'video', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(297, 'screencast-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(298, 'screencast-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(299, 'screencast-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(300, 'screencast-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(301, 'screencast', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(302, 'screencast', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(303, 'screencast', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(304, 'screencast', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(305, 'multi-video-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(306, 'multi-video-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(307, 'multi-video-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(308, 'multi-video-wide', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(309, 'multi-video', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(310, 'multi-video', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(311, 'multi-video', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(312, 'multi-video', 'video', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(313, 'audio', 'audio', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(314, 'audio', 'audio', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(315, 'audio', 'audio', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(316, 'audio', 'audio', '', '', '', '', '', './feeds-vle', '', '', '/', '', ''),
(512, 'video-wide-240-watermark-trailers', 'video', '16:9', '<= 240', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(513, 'video-wide-240-watermark', 'video', '16:9', '<= 240', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(514, 'video-wide-240', 'video', '16:9', '<= 240', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(515, 'video-wide-360-watermark-trailers', 'video', '16:9', '<= 360', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(516, 'video-wide-360-watermark', 'video', '16:9', '<= 360', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(517, 'video-wide-360', 'video', '16:9', '<= 360', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(518, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(519, 'video-wide-480-watermark-trailers', 'video', '16:9', '<= 480', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(520, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(521, 'video-wide-480-watermark', 'video', '16:9', '<= 480', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(522, 'video-wide-480', 'video', '16:9', '<= 480', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(523, 'video-wide-480', 'video', '16:9', '<= 480', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(524, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(525, 'video-wide-576-watermark-trailers', 'video', '16:9', '<= 576', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(526, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(527, 'video-wide-576-watermark', 'video', '16:9', '<= 576', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(528, 'video-wide-576', 'video', '16:9', '<= 576', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(529, 'video-wide-576', 'video', '16:9', '<= 576', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(530, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(531, 'video-wide-720-watermark-trailers', 'video', '16:9', '<= 720', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(532, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(533, 'video-wide-720-watermark', 'video', '16:9', '<= 720', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(534, 'video-wide-720', 'video', '16:9', '<= 720', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(535, 'video-wide-720', 'video', '16:9', '<= 720', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(536, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(537, 'video-wide-1080-watermark-trailers', 'video', '16:9', '<= 1080', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(538, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(539, 'video-wide-1080-watermark', 'video', '16:9', '<= 1080', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(540, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(541, 'video-wide-1080', 'video', '16:9', '<= 1080', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(542, 'video-240-watermark-trailers', 'video', '4:3', '<= 240', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(543, 'video-240-watermark', 'video', '4:3', '<= 240', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(544, 'video-240', 'video', '4:3', '<= 240', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod', '.m4v'),
(545, 'video-360-watermark-trailers', 'video', '4:3', '<= 360', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(546, 'video-360-watermark', 'video', '4:3', '<= 360', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(547, 'video-360', 'video', '4:3', '<= 360', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(548, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(549, 'video-480-watermark-trailers', 'video', '4:3', '<= 480', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(550, 'video-480-watermark', 'video', '4:3', '<= 480', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(551, 'video-480-watermark', 'video', '4:3', '<= 480', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(552, 'video-480', 'video', '4:3', '<= 480', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(553, 'video-480', 'video', '4:3', '<= 480', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(554, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(555, 'video-576-watermark-trailers', 'video', '4:3', '<= 576', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(556, 'video-576-watermark', 'video', '4:3', '<= 576', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(557, 'video-576-watermark', 'video', '4:3', '<= 576', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(558, 'video-576', 'video', '4:3', '<= 576', '', '', '', './feeds', '-ipod', '.m4v', 'ipod-all/', 'ipod-all', '.m4v'),
(559, 'video-576', 'video', '4:3', '<= 576', '', '', '', './feeds', '-desktop', '.m4v', 'desktop-all/', 'desktop-all', '.m4v'),
(560, 'video', 'video', '', '', '', '', '', './feeds', '-480p', '.mov', 'youtube/', 'youtube', '.mov');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
