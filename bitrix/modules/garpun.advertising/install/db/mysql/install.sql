CREATE TABLE  if not exists `R_ALGORITM` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `TIME` datetime NOT NULL,
  `DATE_CREATE` datetime NOT NULL,
  `TYPE` varchar(1) NOT NULL,
  `PATH` varchar(255) DEFAULT NULL,
  `PROJECT_ID` int(11) DEFAULT NULL,
  `STATE` varchar(1) DEFAULT NULL,
  `AGENT_ID` int(11) DEFAULT NULL,
  `AGENT2_ID` int(11) DEFAULT NULL,
`TMP_HASH` varchar(255) DEFAULT NULL,
  `EXTERNAL_ID` int(7) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM;
CREATE TABLE  if not exists `R_ALGORITM_IBLOCK` (

  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_IBLOCK` int(255) NOT NULL,
  `ID_ALGORITM` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM;

-- -----------------------------------
-- Dumping table R_ALGORITM_IBLOCK_SECTION
-- -----------------------------------

CREATE TABLE  if not exists `R_ALGORITM_IBLOCK_SECTION` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_IBLOCK` int(255) NOT NULL,
  `ID_SECTION` int(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM;

-- -----------------------------------
-- Dumping table R_ALGORITM_PROPERTY
-- -----------------------------------
CREATE TABLE  if not exists `R_ALGORITM_PROPERTY` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_ALGORITM` int(7) NOT NULL,
  `ID_PROPERTY` int(7) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM; 

-- -----------------------------------
-- Dumping table R_PROJECT
-- -----------------------------------

CREATE TABLE  if not exists `R_PROJECT` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `EXTERNAL_ID` int(11) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `DATE_CREATE` datetime NOT NULL,
  `MODIFY` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM;

-- -----------------------------------
-- Dumping table R_USER
-- -----------------------------------

CREATE TABLE if not exists `R_USER` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `LOGIN` varchar(255) NOT NULL,
  `TOKEN` varchar(255) NOT NULL,
  `DATE_CREATE` datetime NOT NULL,
  `MODIFY` datetime NOT NULL,
  `EXTERNAL_ID` int(7) NOT NULL,
  `EXTERNAL_ADHANDS_ID` int(7) NOT NULL,
  `USER_ID` int(7) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM;

