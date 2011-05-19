-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 13. April 2011 um 21:32
-- Server Version: 5.1.51
-- PHP-Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `horizon_tools`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tt_bugs`
--

DROP TABLE IF EXISTS `tt_bugs`;
CREATE TABLE IF NOT EXISTS `tt_bugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reporterId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','progress','feedback','resolved','closed') NOT NULL DEFAULT 'open' COMMENT 'open=Offen,progress=Wird Bearbeitet,feedback=Warte auf Rückantwort,resolved=Erledigt,closed=Geschlossen',
  `assignId` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tt_depencies`
--

DROP TABLE IF EXISTS `tt_depencies`;
CREATE TABLE IF NOT EXISTS `tt_depencies` (
  `key` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(60) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `depid` varchar(60) NOT NULL,
  `deplevel` tinyint(4) NOT NULL DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`),
  KEY `id` (`id`),
  KEY `depid` (`depid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tt_log`
--

DROP TABLE IF EXISTS `tt_log`;
CREATE TABLE IF NOT EXISTS `tt_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tt_messages`
--

DROP TABLE IF EXISTS `tt_messages`;
CREATE TABLE IF NOT EXISTS `tt_messages` (
  `id` char(32) NOT NULL,
  `from` bigint(20) NOT NULL,
  `to` bigint(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `new` tinyint(1) NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  PRIMARY KEY (`id`,`from`,`to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tt_units`
--

DROP TABLE IF EXISTS `tt_units`;
CREATE TABLE IF NOT EXISTS `tt_units` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `dname` varchar(255) NOT NULL,
  `type` enum('root','type','category','item') NOT NULL,
  `race` enum('normal','diggren','keelaak','nux','quipgrex','sciweens') NOT NULL DEFAULT 'normal',
  `comment` text,
  `max_level` int(10) unsigned NOT NULL DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `lft` int(12) unsigned NOT NULL DEFAULT '0',
  `rgt` int(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tt_userlevel`
--

DROP TABLE IF EXISTS `tt_userlevel`;
CREATE TABLE IF NOT EXISTS `tt_userlevel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(32) NOT NULL,
  `planet` varchar(32) NOT NULL,
  `techid` varchar(60) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`,`planet`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tt_users`
--

DROP TABLE IF EXISTS `tt_users`;
CREATE TABLE IF NOT EXISTS `tt_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `style` varchar(24) NOT NULL DEFAULT 'blue',
  `account_type` enum('user','admin') NOT NULL DEFAULT 'user',
  `techs_id` varchar(32) NOT NULL DEFAULT '',
  `current_planet` varchar(32) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `techid` (`techs_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
