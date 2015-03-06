-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 06-03-2015 a las 15:49:54
-- Versión del servidor: 5.5.40-0ubuntu0.14.04.1
-- Versión de PHP: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `crashreport`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apps`
--

CREATE TABLE IF NOT EXISTS `apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `author` varchar(128) NOT NULL,
  `appId` varchar(64) NOT NULL,
  `secureId` varchar(128) NOT NULL,
  `pkg` varchar(128) NOT NULL,
  `error_pkg` varchar(128) NOT NULL,
  `push` varchar(128) NOT NULL,
  `notify` int(11) NOT NULL,
  `disabled` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `app` varchar(64) NOT NULL,
  `PACKAGE_NAME` varchar(255) NOT NULL,
  `APP_VERSION_NAME` varchar(255) NOT NULL,
  `APP_VERSION_CODE` int(11) NOT NULL,
  `ANDROID_VERSION` varchar(255) NOT NULL,
  `USER_EMAIL` varchar(255) NOT NULL,
  `USER_COMMENT` varchar(1024) NOT NULL,
  `DEVICE_FEATURES` varchar(2000) NOT NULL,
  `PHONE_MODEL` varchar(255) NOT NULL,
  `SETTINGS_SECURE` text NOT NULL,
  `INSTALLATION_ID` varchar(255) NOT NULL,
  `SETTINGS_SYSTEM` text NOT NULL,
  `SHARED_PREFERENCES` text NOT NULL,
  `IS_SILENT` int(11) NOT NULL,
  `CRASH_CONFIGURATION` text NOT NULL,
  `USER_CRASH_DATE` varchar(255) NOT NULL,
  `DUMPSYS_MEMINFO` text NOT NULL,
  `BUILD` text NOT NULL,
  `STACK_TRACE` text NOT NULL,
  `PRODUCT` varchar(255) NOT NULL,
  `DISPLAY` varchar(1024) NOT NULL,
  `LOGCAT` text NOT NULL,
  `AVAILABLE_MEM_SIZE` int(11) NOT NULL,
  `USER_APP_START_DATE` varchar(255) NOT NULL,
  `CUSTOM_DATA` text NOT NULL,
  `BRAND` varchar(255) NOT NULL,
  `INITIAL_CONFIGURATION` text NOT NULL,
  `TOTAL_MEM_SIZE` int(11) NOT NULL,
  `FILE_PATH` varchar(255) NOT NULL,
  `ENVIRONMENT` text NOT NULL,
  `REPORT_ID` varchar(255) NOT NULL,
  `DEVICE_ID` varchar(255) NOT NULL,
  `DROPBOX` text NOT NULL,
  `EVENTSLOG` text NOT NULL,
  `RADIOLOG` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(64) NOT NULL,
  `hash` varchar(128) NOT NULL,
  `token` varchar(256) NOT NULL,
  `name` varchar(128) NOT NULL,
  `verificaton_code` varchar(256) NOT NULL,
  `verificated` int(11) NOT NULL,
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
