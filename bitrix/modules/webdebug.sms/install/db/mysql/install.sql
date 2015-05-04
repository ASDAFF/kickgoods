CREATE TABLE IF NOT EXISTS `b_webdebug_sms_templates` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `ACTIVE` char(1) NOT NULL,
  `SORT` int(11) NOT NULL DEFAULT '100',
  `DESCRIPTION` longtext NOT NULL,
  `TEMPLATE` longtext NOT NULL,
  `RECEIVER` varchar(255) NOT NULL,
  `EVENT` varchar(255) NOT NULL,
  `STOP` text,
  `RECEIVER_FROM_EMAIL` char(1) DEFAULT NULL,
  `EMAIL_FIELD` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `b_webdebug_sms_templates_site` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TEMPLATE_ID` int(11) NOT NULL,
  `SITE_ID` varchar(2) NOT NULL,
  PRIMARY KEY (`ID`)
);

CREATE TABLE IF NOT EXISTS `b_webdebug_sms_history` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RECEIVER` varchar(255) NOT NULL,
  `SENDER` varchar(255) DEFAULT NULL,
  `MESSAGE` text NOT NULL,
  `EVENT` varchar(255) DEFAULT NULL,
  `PROVIDER` varchar(255) NOT NULL,
  `DATETIME` datetime NOT NULL,
  PRIMARY KEY (`ID`)
);