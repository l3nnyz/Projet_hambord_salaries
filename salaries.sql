--
-- Table structure for table `salaries`
--

DROP TABLE IF EXISTS `salaries`;

CREATE TABLE `salaries` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(100) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `date_naissance` date NOT NULL ,
  `date_embauche` date NOT NULL ,
  `salaire` int(6) NOT NULL default 0,
  `service` varchar(50) NOT NULL default '',
   PRIMARY KEY  (`id`)
) ;


--
--  data for table `salaries`
--
INSERT INTO `salaries` VALUES (1,'Cuset','Jean','1966-06-2','1995-05-15',2400,'cuisine'),
(2,'Dian','Charles','1972-08-30','2007-04-01',1800,'cuisine'),
(3,'Paco','Alain','1958-04-25','2002-06-06',2100,'commercial'),
(4,'Perez','Amanda','1972-01-11','1999-03-11',1700,'comptable'),
(5,'Jeanu','Thierry','1970-07-11','1998-01-01',1600,'comptable'),
(6,'Taque','Stephane','1965-04-08','2008-02-01',2000,'entretien'),
(7,'Devos','Yahn','1965-07-08','2009-02-01',2400,'entretien'),
(8,'Tettu','Sylvain','1975-07-11','2006-02-01',2000,'comptable'),
(9,'Triet','Jacques','1968-11-08','2005-10-01',3000,'cuisine'),
(10,'Jolivet','Olivier','1973-02-23','2000-11-06',1800,'commercial'),
(11,'Jollet','Marc','1965-04-28','1999-02-01',4000,'cuisine'),
(12,'Milan','Adrien','1980-04-08','2008-02-01',2000,'entretien'),
(13,'Cerceau','Gilles','1975-03-18','2008-02-01',2000,'entretien'),
(14,'Tuche','Yves','1965-04-08','2006-02-01',2000,'commercial'),
(15,'Trichet','Antoine','1978-11-08','2006-02-01',1600,'cuisine'),
(16,'Alan','Steven','1976-01-21','2006-02-01',2800,'cuisine'),
(17,'Dilou','Tristan','1978-01-18','2006-02-01',2500,'entretien'),
(18,'Jaspe','Anouk','1980-04-08','2008-02-01',2000,'entretien'),
(19,'Anvers','Tonio','1967-06-06','2008-02-01',2400,'entretien'),
(20,'Clouzot','Edouard','1955-04-08','1998-02-01',2500,'entretien')
;



