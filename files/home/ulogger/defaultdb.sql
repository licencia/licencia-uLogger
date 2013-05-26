-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Värd: localhost
-- Skapad: 05 maj 2013 kl 17:13
-- Serverversion: 5.5.30
-- PHP-version: 5.4.4-14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `ulogger`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `data`
--

CREATE TABLE IF NOT EXISTS `data` (
  `name` varchar(64) NOT NULL,
  `value` varchar(512) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `data`
--

INSERT INTO `data` (`name`, `value`) VALUES
('hash_admin', 'sha256:1000:ZoWjuRGQFjRfhltjH71vjhMy4crFBRRM:rJs9rRsZrfjKjEpPjy653AuiJoutHLEI'),
('hash_licencia', 'sha256:1000:tOFfjMsH31aHdDVahDbZNR3xuE7fwMmX:D4DiLgl/BM6QuO9Ob6zNKpYTA+fPftXZ'),
('ulogger_admin_user', 'admin'),
('ulogger_version', '1.0-beta2'),
('ulogger_http_port', '8080'),
('ulogger_ip_dhcp', 'false'),
('ulogger_ip_address', '192.168.1.100'),
('ulogger_ip_gateway', '192.168.1.1'),
('ulogger_ip_netmask', '255.255.255.0'),
('ulogger_ip_dns', '192.168.1.1'),
('siplogg_filename', ''),
('siplogg_filesize', '0'),
('siplogg_filter', ''),
('siplogg_ring_buffer', '0'),
('siplogg_start_time', '0');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
