-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `f01_persona`
--

DROP TABLE IF EXISTS `f01_persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f01_persona` (
  `F01_ID_PERSONA` int(10) unsigned NOT NULL,
  `F01_TPO_DOC` int(11) DEFAULT NULL,
  `F01_NUM_DOC` varchar(15) DEFAULT NULL,
  `F01_APE_PAT` varchar(300) DEFAULT NULL,
  `F01_APE_MAT` varchar(50) DEFAULT NULL,
  `F01_NOMB_PRI` varchar(50) DEFAULT NULL,
  `F01_NOMB_SEG` varchar(50) DEFAULT NULL,
  `F01_SEXO` int(11) DEFAULT NULL,
  `F01_FE_NAC` datetime DEFAULT NULL,
  `F01_STDO_CIVIL` int(11) DEFAULT NULL,
  `F01_STDO_PERSN` int(11) DEFAULT NULL,
  `F01_CTA_CORREO` varchar(50) DEFAULT NULL,
  `F01_ID_NACIONALIDAD` int(11) DEFAULT NULL,
  `F01_ID_INSTRUCCION` int(11) DEFAULT NULL,
  `F01_ID_PROFESION` int(11) DEFAULT NULL,
  `F01_DPTO_NAC` varchar(2) DEFAULT NULL,
  `F01_PROV_NAC` varchar(2) DEFAULT NULL,
  `F01_DISTRI_NAC` varchar(2) DEFAULT NULL,
  `F01_TELF_FIJO` varchar(20) DEFAULT NULL,
  `F01_TELF_CEL` varchar(20) DEFAULT NULL,
  `F01_DESC_COMENT` varchar(500) DEFAULT NULL,
  `F01_USR_REG` int(11) DEFAULT NULL,
  `F01_FE_USR_REG` datetime DEFAULT NULL,
  `F01_USR_ACT` int(11) DEFAULT NULL,
  `F01_FE_USR_ACT` datetime DEFAULT NULL,
  `F01_ID_USUA_CREA` varchar(35) DEFAULT NULL,
  `F01_FE_USUA_CREA` datetime DEFAULT NULL,
  `F01_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F01_FE_USUA_MODI` datetime DEFAULT NULL,
  `F01_ZONA` varchar(250) DEFAULT NULL,
  `F01_VIA` varchar(100) DEFAULT NULL,
  `F01_INT` varchar(100) DEFAULT NULL,
  `F01_EXT` varchar(50) DEFAULT NULL,
  `F01_PISO` varchar(50) DEFAULT NULL,
  `F01_DEPARTAMENTO` varchar(2) DEFAULT NULL,
  `F01_PROVINCIA` varchar(2) DEFAULT NULL,
  `F01_DISTRITO` varchar(2) DEFAULT NULL,
  `F01_OBSERVACION` varchar(1000) DEFAULT NULL,
  `F01_EDAD` int(10) unsigned DEFAULT NULL,
  `F01_CENTRO` int(11) DEFAULT NULL,
  `F01_CONDICION` varchar(20) DEFAULT NULL,
  `F01_CUENTA` varchar(15) DEFAULT NULL,
  `F01_INGRESO` float DEFAULT NULL,
  `F01_APORTACION` float DEFAULT NULL,
  `F01_SALDO` float DEFAULT NULL,
  `F01_DOCUMENTO` varchar(200) DEFAULT NULL,
  `F01_SITUACION` varchar(70) DEFAULT NULL,
  `F01_FECHA_RETIRO` date DEFAULT NULL,
  `F01_FECHA_FALLE` date DEFAULT NULL,
  `F01_APORTA_BASE` int(10) unsigned DEFAULT NULL,
  `F01_APORTA_SOBRE` float DEFAULT NULL,
  `F01_SOBREVI` varchar(50) DEFAULT NULL,
  `F01_OBS` varchar(500) DEFAULT NULL,
  `F01_FEC_REINGRESO` date DEFAULT NULL,
  `F01_APORTE_COPE` int(10) unsigned DEFAULT 0,
  `F01_OBS2` varchar(500) DEFAULT NULL,
  `F01_TITULO` varchar(100) DEFAULT NULL,
  `F01_FIRMO` varchar(2) DEFAULT NULL,
  `F01_CARTA` varchar(45) DEFAULT NULL,
  `F01_FECHA_BCO` date DEFAULT NULL,
  `F01_USUA_CARTA` varchar(100) DEFAULT NULL,
  `F01_OBS_CARTA` varchar(500) DEFAULT NULL,
  `F01_MTO_RIFA` float DEFAULT NULL,
  `F01_MTO_MULTA` float DEFAULT NULL,
  `F01_JUBILACION` varchar(50) DEFAULT NULL,
  `F01_MTO_OTRO` float DEFAULT NULL,
  `F01_TIPO_APORTE` varchar(45) DEFAULT NULL,
  `F01_CAMBIO_APORTE` varchar(2) DEFAULT NULL,
  `F01_VISIBLE` int(11) DEFAULT 0,
  `F01_COMPRA_DEUDA` int(10) unsigned DEFAULT NULL,
  `F01_CORREO2` varchar(450) DEFAULT NULL,
  `F01_RIFA_CUOTA` float DEFAULT 0,
  `F01_DOCUMENTO2` varchar(450) DEFAULT NULL,
  `F01_DOCUMENTO3` varchar(450) DEFAULT NULL,
  `F01_FEC_ULT_APORTE` varchar(45) DEFAULT NULL,
  `F01_CELULAR1` varchar(45) DEFAULT NULL,
  `F01_CELULAR2` varchar(45) DEFAULT NULL,
  `F01_CELULAR3` varchar(45) DEFAULT NULL,
  `F01_CORREO3` varchar(250) DEFAULT NULL,
  `F01_CORREO4` varchar(250) DEFAULT NULL,
  `F01_CORREO5` varchar(250) DEFAULT NULL,
  `F01_ANT_SOCIO` varchar(250) DEFAULT NULL,
  `F01_COBERTURA` varchar(45) DEFAULT NULL,
  `F01_FECHA_APORTE` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`F01_ID_PERSONA`),
  KEY `FK_f01_nacionalidad` (`F01_ID_NACIONALIDAD`),
  KEY `APEPAT` (`F01_APE_PAT`) USING BTREE,
  KEY `DOCUMENTO` (`F01_NUM_DOC`) USING BTREE,
  KEY `CUENTA` (`F01_CUENTA`) USING BTREE,
  KEY `I_CENTRO` (`F01_CENTRO`) USING BTREE,
  KEY `I_CONDICION` (`F01_CONDICION`) USING BTREE,
  KEY `I_ESTADO` (`F01_STDO_PERSN`) USING BTREE,
  CONSTRAINT `FK_f01_nacionalidad` FOREIGN KEY (`F01_ID_NACIONALIDAD`) REFERENCES `f11_nacionalidad` (`F11_ID_NACIONALIDAD`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f02_cliente`
--

DROP TABLE IF EXISTS `f02_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f02_cliente` (
  `F02_ID_CLIENTE` varchar(11) NOT NULL,
  `F02_ID_PERSONA` int(11) NOT NULL,
  `F02_STDO` int(11) NOT NULL,
  `F02_USR_REG` int(11) DEFAULT NULL,
  `F02_FE_USR_REG` datetime DEFAULT NULL,
  `F02_USR_ACT` int(11) DEFAULT NULL,
  `F02_FE_USR_ACT` datetime DEFAULT NULL,
  `F02_ID_USUA_CREA` varchar(35) DEFAULT NULL,
  `F02_FE_USUA_CREA` datetime DEFAULT NULL,
  `F02_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F02_FE_USUA_MODI` datetime DEFAULT NULL,
  `F02_CLAVE` varchar(50) DEFAULT NULL,
  KEY `UN_ID_PERSONA` (`F02_ID_PERSONA`),
  KEY `Index_2` (`F02_ID_CLIENTE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f03_motivo`
--

DROP TABLE IF EXISTS `f03_motivo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f03_motivo` (
  `F03_ID_MOTIVO` int(11) NOT NULL,
  `F03_DES_MOTIVO` varchar(100) NOT NULL,
  `F03_STDO` int(11) NOT NULL,
  `F03_ID_USUA_CREA` varchar(35) NOT NULL,
  `F03_FE_USUA_CREA` datetime NOT NULL,
  `F03_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F03_FE_USUA_MODI` datetime DEFAULT NULL,
  PRIMARY KEY (`F03_ID_MOTIVO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f05_ubigeo`
--

DROP TABLE IF EXISTS `f05_ubigeo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f05_ubigeo` (
  `ubigeo_departamento` varchar(200) DEFAULT NULL,
  `ubigeo_provincia` varchar(200) DEFAULT NULL,
  `ubigeo_distrito` varchar(200) DEFAULT NULL,
  `ubigeo_codigo` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f06_usuario`
--

DROP TABLE IF EXISTS `f06_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f06_usuario` (
  `f06_id_usuario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f06_nombre` varchar(200) DEFAULT NULL,
  `f06_perfil` int(10) unsigned DEFAULT NULL,
  `f06_password` varchar(60) DEFAULT NULL,
  `f06_usuCrea` varchar(60) DEFAULT NULL,
  `f06_fechaCrea` datetime DEFAULT NULL,
  `f06_usuModi` varchar(60) DEFAULT NULL,
  `f06_fechaModi` datetime DEFAULT NULL,
  `f06_estado` varchar(1) DEFAULT NULL,
  `f06_supervisor` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`f06_id_usuario`),
  KEY `FK_f06_usuario_1` (`f06_perfil`),
  CONSTRAINT `FK_f06_usuario_1` FOREIGN KEY (`f06_perfil`) REFERENCES `f08_perfil` (`f08_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f07_opcion`
--

DROP TABLE IF EXISTS `f07_opcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f07_opcion` (
  `f07_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f07_nombre` varchar(60) DEFAULT NULL,
  `f07_ruta` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`f07_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f08_perfil`
--

DROP TABLE IF EXISTS `f08_perfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f08_perfil` (
  `f08_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f08_nombre` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`f08_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f09_profesion`
--

DROP TABLE IF EXISTS `f09_profesion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f09_profesion` (
  `F09_ID_PROFESION` int(11) NOT NULL,
  `F09_DESC_PROF` varchar(100) NOT NULL,
  `F09_STDO_PROF` int(11) DEFAULT NULL,
  `F09_ID_USUA_CREA` varchar(35) DEFAULT NULL,
  `F09_FE_USUA_CREA` datetime DEFAULT NULL,
  `F09_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F09_FE_USUA_MODI` datetime DEFAULT NULL,
  PRIMARY KEY (`F09_ID_PROFESION`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f10_instruccion`
--

DROP TABLE IF EXISTS `f10_instruccion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f10_instruccion` (
  `F10_ID_INSTRUCCION` int(11) NOT NULL,
  `F10_DESC_INSTRUCC` varchar(100) NOT NULL,
  `F10_STDO_INSTRUCC` int(11) NOT NULL,
  `F10_ID_USUA_CREA` varchar(35) NOT NULL,
  `F10_FE_USUA_CREA` datetime NOT NULL,
  `F10_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F10_FE_USUA_MODI` datetime DEFAULT NULL,
  PRIMARY KEY (`F10_ID_INSTRUCCION`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f11_nacionalidad`
--

DROP TABLE IF EXISTS `f11_nacionalidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f11_nacionalidad` (
  `F11_ID_NACIONALIDAD` int(11) NOT NULL,
  `F11_DESC_NACIONALD` varchar(100) NOT NULL,
  `F11_STDO_NACIONALD` int(11) NOT NULL,
  `F11_ID_USUA_CREA` varchar(35) NOT NULL,
  `F11_FE_USUA_CREA` datetime NOT NULL,
  `F11_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F11_FE_USUA_MODI` datetime DEFAULT NULL,
  PRIMARY KEY (`F11_ID_NACIONALIDAD`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f12_familiar`
--

DROP TABLE IF EXISTS `f12_familiar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f12_familiar` (
  `F12_ID_CLIENTE` varchar(11) NOT NULL,
  `F12_ID_PERSONA` int(11) NOT NULL,
  `F12_ID_TPO_PARENT` int(11) NOT NULL,
  `F12_STDO_PARENT` int(11) NOT NULL,
  `F12_ID_USUA_CREA` varchar(35) DEFAULT NULL,
  `F12_FE_USUA_CREA` datetime DEFAULT NULL,
  `F12_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F12_FE_USUA_MODI` datetime DEFAULT NULL,
  PRIMARY KEY (`F12_ID_CLIENTE`,`F12_ID_PERSONA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f13_perfil_opcion`
--

DROP TABLE IF EXISTS `f13_perfil_opcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f13_perfil_opcion` (
  `f13_perfil` int(10) unsigned NOT NULL,
  `f13_opcion` int(10) unsigned NOT NULL,
  PRIMARY KEY (`f13_perfil`,`f13_opcion`),
  KEY `FK_f13_perfil_opcion_2` (`f13_opcion`),
  CONSTRAINT `FK_f13_perfil_opcion_1` FOREIGN KEY (`f13_perfil`) REFERENCES `f08_perfil` (`f08_codigo`),
  CONSTRAINT `FK_f13_perfil_opcion_2` FOREIGN KEY (`f13_opcion`) REFERENCES `f07_opcion` (`f07_codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f14_operacion`
--

DROP TABLE IF EXISTS `f14_operacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f14_operacion` (
  `f14_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f14_nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`f14_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f15_movimiento`
--

DROP TABLE IF EXISTS `f15_movimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f15_movimiento` (
  `f15_codigo` int(10) unsigned NOT NULL,
  `f15_fecha` date NOT NULL,
  `f15_operacion` int(10) unsigned NOT NULL,
  `f15_cliente` int(10) unsigned NOT NULL,
  `f15_maportacion` float DEFAULT NULL,
  `f15_mretiro` float DEFAULT NULL,
  `f15_maprestamo` decimal(19,2) DEFAULT NULL,
  `f15_miprestamo` float DEFAULT NULL,
  `f15_estado` varchar(1) DEFAULT NULL,
  `f15_usuaCrea` varchar(60) DEFAULT NULL,
  `f15_fechaCrea` datetime DEFAULT NULL,
  `f15_usuaModi` varchar(60) DEFAULT NULL,
  `f15_fechaModi` datetime DEFAULT NULL,
  `f15_mdesembolso` float DEFAULT NULL,
  `f15_prestamo` int(10) unsigned DEFAULT NULL,
  `f15_cheque` varchar(45) DEFAULT NULL,
  `f15_mbeneficio` float DEFAULT NULL,
  `f15_beneficio` int(10) unsigned DEFAULT NULL,
  `f15_mletras` varchar(600) DEFAULT NULL,
  `f15_obs` varchar(700) DEFAULT NULL,
  `f15_gastosa` float DEFAULT NULL,
  `f15_gastosm` float DEFAULT NULL,
  `f15_gastos1` float DEFAULT NULL,
  `f15_gastos2` float DEFAULT NULL,
  `f15_ape_cope` int(10) unsigned DEFAULT NULL,
  `f15_favor` varchar(200) DEFAULT NULL,
  `f15_dni` varchar(45) DEFAULT NULL,
  `f15_megreso` float DEFAULT NULL,
  `f15_serie` int(20) unsigned DEFAULT NULL,
  `f15_formaPago` varchar(10) DEFAULT NULL,
  `f15_mamulta` float DEFAULT NULL,
  `f15_marifa` float DEFAULT NULL,
  `f15_devolucion` float DEFAULT NULL,
  `f15_producto` varchar(2) DEFAULT NULL,
  `f15_obs2` varchar(500) DEFAULT NULL,
  `f15_ingreso` float DEFAULT NULL,
  `f15_definitivo` float DEFAULT NULL,
  `f15_motivo_baja` varchar(250) DEFAULT NULL,
  `f15_std` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`f15_codigo`),
  KEY `FK_f15_movimiento_2` (`f15_operacion`),
  KEY `fecha` (`f15_fecha`) USING BTREE,
  KEY `Cliente` (`f15_cliente`) USING BTREE,
  CONSTRAINT `FK_f15_movimiento_2` FOREIGN KEY (`f15_operacion`) REFERENCES `f14_operacion` (`f14_codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f16_prestamo`
--

DROP TABLE IF EXISTS `f16_prestamo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f16_prestamo` (
  `f16_codigo` int(10) unsigned NOT NULL,
  `f16_fecha` date DEFAULT NULL,
  `f16_importe` float DEFAULT NULL,
  `f16_cliente` int(10) unsigned DEFAULT NULL,
  `f16_garante` int(10) unsigned DEFAULT NULL,
  `f16_interes` float DEFAULT NULL,
  `f16_importeLetras` varchar(400) DEFAULT NULL,
  `f16_cuotas` int(10) unsigned DEFAULT NULL,
  `f16_seguro` float DEFAULT 0,
  `f16_total` float DEFAULT NULL,
  `f16_fpago` int(10) unsigned DEFAULT NULL,
  `f16_cuotasPend` int(10) unsigned DEFAULT NULL,
  `f16_documentot1` varchar(120) DEFAULT NULL,
  `f16_documentot2` varchar(120) DEFAULT NULL,
  `f16_documentot3` varchar(120) DEFAULT NULL,
  `f16_documentog1` varchar(120) DEFAULT NULL,
  `f16_documentog2` varchar(120) DEFAULT NULL,
  `f16_documentog3` varchar(120) DEFAULT NULL,
  `f16_estado` int(11) DEFAULT NULL,
  `f16_fechaPago` int(10) unsigned DEFAULT NULL,
  `f16_fechaApro` datetime DEFAULT NULL,
  `f16_obs` varchar(700) DEFAULT NULL,
  `f16_fechaCancela` date DEFAULT NULL,
  `f16_desembolso` float DEFAULT NULL,
  `f16_aportacion` float DEFAULT NULL,
  `f16_contingencia` float DEFAULT NULL,
  `f16_saldo` varchar(45) DEFAULT NULL,
  `f16_am` float DEFAULT NULL,
  `f16_acuotas` int(10) unsigned DEFAULT NULL,
  `f16_afecha` date DEFAULT NULL,
  `f16_acuotasp` int(10) unsigned DEFAULT NULL,
  `f16_aimportep` float DEFAULT NULL,
  `f16_importec` float DEFAULT NULL,
  `f16_solicitado` float DEFAULT NULL,
  `f16_gastosa` float DEFAULT NULL,
  `f16_gastosm` float DEFAULT NULL,
  `f16_otros` float DEFAULT NULL,
  `f16_otros2` float DEFAULT NULL,
  `f16_fechaUP` date DEFAULT NULL,
  `f16_cuotaMe` float DEFAULT NULL,
  `f16_ultimoPre` int(10) unsigned DEFAULT NULL,
  `f16_aUPago` date DEFAULT NULL,
  `f16_aCuota` float DEFAULT NULL,
  `f16_aPrestamo` int(10) unsigned DEFAULT NULL,
  `f16_producto` varchar(45) DEFAULT NULL,
  `f16_fechaDes` date DEFAULT NULL,
  `f16_sobre` float DEFAULT 0,
  `f16_mov` int(10) unsigned DEFAULT NULL,
  `f16_gastosTra` float DEFAULT 0,
  `f16_garante2` int(10) unsigned DEFAULT NULL,
  `f16_garante3` int(10) unsigned DEFAULT NULL,
  `f16_garante4` int(10) unsigned DEFAULT NULL,
  `f16_cancelaPres` varchar(500) DEFAULT NULL,
  `f16_gracia` varchar(1) DEFAULT NULL,
  `f16_cuotasTramite` int(10) unsigned DEFAULT NULL,
  `f16_otrasDeudas` float DEFAULT NULL,
  `f16_subtotal` float DEFAULT NULL,
  `f16_rebatir` float DEFAULT NULL,
  `f16_usuarioCrea` varchar(200) DEFAULT NULL,
  `f16_usuarioAprueba` varchar(200) DEFAULT NULL,
  `f16_usuarioCaja` varchar(200) DEFAULT NULL,
  `f16_congarante` varchar(1) DEFAULT NULL,
  `f16_motivo` varchar(150) DEFAULT NULL,
  `f16_tramite` varchar(20) DEFAULT NULL,
  `f16_referencia` varchar(45) DEFAULT NULL,
  `f16_cuota_amor` float DEFAULT NULL,
  `f16_cuota_interes` float DEFAULT NULL,
  `f16_sobre_amor` float DEFAULT NULL,
  `f16_sobre_interes` float DEFAULT NULL,
  `f16_sobre_mora` float DEFAULT NULL,
  `f16_saldo_amor` float DEFAULT NULL,
  `f16_saldo_interes` float DEFAULT NULL,
  `f16_saldo_mora` float DEFAULT NULL,
  `f16_saldo_final` float DEFAULT NULL,
  PRIMARY KEY (`f16_codigo`),
  KEY `Index_2` (`f16_cliente`),
  KEY `Index_3` (`f16_garante`),
  KEY `Fecha` (`f16_fecha`) USING BTREE,
  KEY `Index_estado_presta` (`f16_estado`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f17_cuotasprestamo`
--

DROP TABLE IF EXISTS `f17_cuotasprestamo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f17_cuotasprestamo` (
  `f17_prestamo` int(10) unsigned DEFAULT NULL,
  `f17_ncuota` int(10) unsigned DEFAULT NULL,
  `f17_fecha` date DEFAULT NULL,
  `f17_capital` float DEFAULT NULL,
  `f17_interes` float DEFAULT NULL,
  `f17_total` float DEFAULT NULL,
  `f17_saldo` float DEFAULT NULL,
  `f17_cta` varchar(45) DEFAULT NULL,
  `f17_totalA` float DEFAULT NULL,
  `f17_estado` varchar(45) DEFAULT NULL,
  `f17_fechaCancela` date DEFAULT NULL,
  `f17_sobregiro` float DEFAULT NULL,
  `f17_interesSG` float DEFAULT NULL,
  `f17_mora` float DEFAULT NULL,
  `f17_moraSG` float DEFAULT NULL,
  `f17_pagoCaja` float DEFAULT NULL,
  `f17_tramite` float DEFAULT NULL,
  `f17_indice` int(10) unsigned DEFAULT NULL,
  KEY `f17_i_prestamo` (`f17_prestamo`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f18_empresa`
--

DROP TABLE IF EXISTS `f18_empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f18_empresa` (
  `codEntidad` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f18_nombre` varchar(500) DEFAULT NULL,
  `f18_direccion` varchar(500) DEFAULT NULL,
  `f18_telefono` varchar(500) DEFAULT NULL,
  `f18_ruc` varchar(15) DEFAULT NULL,
  `f18_cronograma` int(10) unsigned DEFAULT NULL,
  `f18_moneda` int(10) unsigned DEFAULT NULL,
  `f18_maxcuotas` int(10) unsigned DEFAULT NULL,
  `f18_mincuotas` int(10) unsigned DEFAULT NULL,
  `f18_montomax` float DEFAULT NULL,
  `f18_montomin` float DEFAULT NULL,
  `f18_producto` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`codEntidad`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f19_tarifario`
--

DROP TABLE IF EXISTS `f19_tarifario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f19_tarifario` (
  `f19_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f19_concepto` int(10) unsigned DEFAULT NULL,
  `f19_valor` float DEFAULT NULL,
  `f19_fechaDesde` date DEFAULT NULL,
  `f19_fechaHasta` date DEFAULT NULL,
  `f19_estado` varchar(30) DEFAULT NULL,
  `f19_producto` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`f19_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f20_concepto`
--

DROP TABLE IF EXISTS `f20_concepto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f20_concepto` (
  `f20_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f20_nombre` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`f20_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f21_beneficios`
--

DROP TABLE IF EXISTS `f21_beneficios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f21_beneficios` (
  `f21_codigo` int(10) unsigned NOT NULL,
  `f21_socio` int(10) unsigned DEFAULT NULL,
  `f21_nombres` varchar(400) DEFAULT NULL,
  `f21_tipoPa` varchar(50) DEFAULT NULL,
  `f21_fechaFa` date DEFAULT NULL,
  `f21_beneficiario` varchar(400) DEFAULT NULL,
  `f21_tipoPa2` varchar(50) DEFAULT NULL,
  `f21_fecha` date DEFAULT NULL,
  `f21_importe` float DEFAULT NULL,
  `f21_estado` varchar(45) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `f21_dni1` varchar(45) DEFAULT NULL,
  `f21_direccion1` varchar(400) DEFAULT NULL,
  `f21_dni2` varchar(45) DEFAULT NULL,
  `f21_direccion2` varchar(400) DEFAULT NULL,
  `f21_tipo` varchar(45) DEFAULT NULL,
  `f21_obs` varchar(5000) DEFAULT NULL,
  `f21_aporta` int(10) unsigned DEFAULT NULL,
  `f21_acredita` varchar(200) DEFAULT NULL,
  `f21_lugar_falle` varchar(450) DEFAULT NULL,
  `f21_tramite` varchar(20) DEFAULT NULL,
  `f21_vigencia` varchar(250) DEFAULT NULL,
  KEY `bene_i_fecha` (`f21_fecha`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f22_aportes`
--

DROP TABLE IF EXISTS `f22_aportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f22_aportes` (
  `f22_socio` int(16) unsigned NOT NULL,
  `f22_periodo` date NOT NULL,
  `f22_aportacionP` float DEFAULT NULL,
  `f22_aportacionD` float DEFAULT NULL,
  `f22_diferencia` float DEFAULT 0,
  `f22_fechaPago` date DEFAULT NULL,
  `f22_aportacion` float DEFAULT NULL,
  `f22_aporte_id` int(16) unsigned NOT NULL,
  KEY `Index_1` (`f22_socio`),
  KEY `Index_2` (`f22_periodo`),
  KEY `Index_3` (`f22_aporte_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f23_cheque`
--

DROP TABLE IF EXISTS `f23_cheque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f23_cheque` (
  `f23_codigo` int(10) unsigned NOT NULL,
  `f23_nombre` varchar(500) DEFAULT NULL,
  `f23_monto` varchar(40) DEFAULT NULL,
  `f23_montoLetras` varchar(600) DEFAULT NULL,
  `f23_fecha` date DEFAULT NULL,
  `f23_cheque` varchar(45) DEFAULT NULL,
  `f23_motivo` varchar(100) DEFAULT NULL,
  `f23_centro` int(10) unsigned DEFAULT NULL,
  `f23_condicion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`f23_codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f24_descuentos`
--

DROP TABLE IF EXISTS `f24_descuentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f24_descuentos` (
  `f24_socio` int(14) unsigned NOT NULL,
  `f24_periodo` date NOT NULL,
  `f24_pagoCaja` float DEFAULT NULL,
  `f24_amortiza` float DEFAULT NULL,
  `f24_interesp` float DEFAULT NULL,
  `f24_interesa` float DEFAULT NULL,
  `f24_interesd` float DEFAULT NULL,
  `f24_morap` float DEFAULT NULL,
  `f24_moraa` float DEFAULT NULL,
  `f24_morad` float DEFAULT NULL,
  `f24_otro` float DEFAULT NULL,
  `f24_aportep` float DEFAULT NULL,
  `f24_aportea` float DEFAULT NULL,
  `f24_aported` float DEFAULT NULL,
  `f24_montoP` float DEFAULT NULL,
  `f24_montoA` float DEFAULT NULL,
  `f24_montoD` float DEFAULT NULL,
  `f24_otro2` float DEFAULT NULL,
  `f24_saldo` float DEFAULT NULL,
  `f24_tipo` varchar(45) DEFAULT NULL,
  `f24_centro` int(10) unsigned DEFAULT NULL,
  `f24_aportecp` float DEFAULT NULL,
  `f24_aporteca` float DEFAULT NULL,
  `f24_aportecd` float DEFAULT NULL,
  `f24_multap` float DEFAULT NULL,
  `f24_multaa` float DEFAULT NULL,
  `f24_multad` float DEFAULT NULL,
  `f24_rifap` float DEFAULT NULL,
  `f24_rifaa` float DEFAULT NULL,
  `f24_rifad` float DEFAULT NULL,
  `f24_producto` varchar(1) DEFAULT NULL,
  `f24_amortizap` float DEFAULT NULL,
  `f24_interespp` float DEFAULT NULL,
  `f24_interespa` float DEFAULT NULL,
  `f24_interespd` float DEFAULT NULL,
  `f24_producto2` varchar(2) DEFAULT NULL,
  `f24_amortizap2` float DEFAULT NULL,
  `f24_interespp2` float DEFAULT NULL,
  `f24_interespa2` float DEFAULT NULL,
  `f24_interespd2` float DEFAULT NULL,
  `f24_producto3` varchar(2) DEFAULT NULL,
  `f24_amortizap3` float DEFAULT NULL,
  `f24_interespp3` float DEFAULT NULL,
  `f24_interespa3` float DEFAULT NULL,
  `f24_interespd3` float DEFAULT NULL,
  `f24_producto4` varchar(2) DEFAULT NULL,
  `f24_morapp` float DEFAULT NULL,
  `f24_morapa` float DEFAULT NULL,
  `f24_morapd` float DEFAULT NULL,
  `f24_morapp2` float DEFAULT NULL,
  `f24_morapa2` float DEFAULT NULL,
  `f24_morapd2` float DEFAULT NULL,
  `f24_morapp3` float DEFAULT NULL,
  `f24_morapa3` float DEFAULT NULL,
  `f24_morapd3` float DEFAULT NULL,
  `f24_producto5` varchar(2) DEFAULT NULL,
  `f24_amortizap4` float DEFAULT NULL,
  `f24_interespp4` float DEFAULT NULL,
  `f24_interespa4` float DEFAULT NULL,
  `f24_interespd4` float DEFAULT NULL,
  `f24_morapp4` float DEFAULT NULL,
  `f24_morapa4` float DEFAULT NULL,
  `f24_morapd4` float DEFAULT NULL,
  `f24_producto6` varchar(2) DEFAULT NULL,
  `f24_amortizap5` float DEFAULT NULL,
  `f24_interespp5` float DEFAULT NULL,
  `f24_interespa5` float DEFAULT NULL,
  `f24_interespd5` float DEFAULT NULL,
  `f24_morapp5` float DEFAULT NULL,
  `f24_morapa5` float DEFAULT NULL,
  `f24_morapd5` float DEFAULT NULL,
  `f24_cajaProd` varchar(2) DEFAULT NULL,
  `f24_amortizaCaja` float DEFAULT NULL,
  `f24_interesCaja` float DEFAULT NULL,
  `f24_moraCaja` float DEFAULT NULL,
  `f24_producto7` varchar(2) DEFAULT NULL,
  `f24_amortizap6` float DEFAULT NULL,
  `f24_interespp6` float DEFAULT NULL,
  `f24_interespa6` float DEFAULT NULL,
  `f24_interespd6` float DEFAULT NULL,
  `f24_morapp6` float DEFAULT NULL,
  `f24_morapa6` float DEFAULT NULL,
  `f24_morapd6` float DEFAULT NULL,
  `f24_producto8` varchar(2) DEFAULT NULL,
  `f24_amortizap7` float DEFAULT NULL,
  `f24_interespp7` float DEFAULT NULL,
  `f24_interespa7` float DEFAULT NULL,
  `f24_interespd7` float DEFAULT NULL,
  `f24_morapp7` float DEFAULT NULL,
  `f24_morapa7` float DEFAULT NULL,
  `f24_morapd7` float DEFAULT NULL,
  `f24_producto9` varchar(2) DEFAULT NULL,
  `f24_amortizap8` float DEFAULT NULL,
  `f24_interespp8` float DEFAULT NULL,
  `f24_interespa8` float DEFAULT NULL,
  `f24_interespd8` float DEFAULT NULL,
  `f24_morapp8` float DEFAULT NULL,
  `f24_morapa8` float DEFAULT NULL,
  `f24_morapd8` float DEFAULT NULL,
  `f24_producto10` varchar(2) DEFAULT NULL,
  `f24_amortizap9` float DEFAULT NULL,
  `f24_interespp9` float DEFAULT NULL,
  `f24_interespa9` float DEFAULT NULL,
  `f24_interespd9` float DEFAULT NULL,
  `f24_morapp9` float DEFAULT NULL,
  `f24_morapa9` float DEFAULT NULL,
  `f24_morapd9` float DEFAULT NULL,
  KEY `Index_1` (`f24_socio`,`f24_periodo`),
  KEY `Index_2` (`f24_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f24_descuentos_temp`
--

DROP TABLE IF EXISTS `f24_descuentos_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f24_descuentos_temp` (
  `f24_socio` int(11) NOT NULL,
  `f24_periodo` date NOT NULL,
  `f24_pagoCaja` double DEFAULT NULL,
  `f24_amortiza` double DEFAULT NULL,
  `f24_interesp` double DEFAULT NULL,
  `f24_interesa` double DEFAULT NULL,
  `f24_interesd` double DEFAULT NULL,
  `f24_morap` double DEFAULT NULL,
  `f24_moraa` double DEFAULT NULL,
  `f24_morad` double DEFAULT NULL,
  `f24_otro` double DEFAULT NULL,
  `f24_aportep` double DEFAULT NULL,
  `f24_aportea` double DEFAULT NULL,
  `f24_aported` double DEFAULT NULL,
  `f24_montoP` double DEFAULT NULL,
  `f24_montoA` double DEFAULT NULL,
  `f24_montoD` double DEFAULT NULL,
  `f24_otro2` double DEFAULT NULL,
  `f24_saldo` double DEFAULT NULL,
  `f24_tipo` varchar(45) DEFAULT NULL,
  `f24_centro` int(11) DEFAULT NULL,
  `f24_aportecp` double DEFAULT NULL,
  `f24_aporteca` double DEFAULT NULL,
  `f24_aportecd` double DEFAULT NULL,
  `f24_multap` double DEFAULT NULL,
  `f24_multaa` double DEFAULT NULL,
  `f24_multad` double DEFAULT NULL,
  `f24_rifap` double DEFAULT NULL,
  `f24_rifaa` double DEFAULT NULL,
  `f24_rifad` double DEFAULT NULL,
  `f24_producto` varchar(1) DEFAULT NULL,
  `f24_amortizap` double DEFAULT NULL,
  `f24_interespp` double DEFAULT NULL,
  `f24_interespa` double DEFAULT NULL,
  `f24_interespd` double DEFAULT NULL,
  `f24_producto2` varchar(2) DEFAULT NULL,
  `f24_amortizap2` double DEFAULT NULL,
  `f24_interespp2` double DEFAULT NULL,
  `f24_interespa2` double DEFAULT NULL,
  `f24_interespd2` double DEFAULT NULL,
  `f24_producto3` varchar(2) DEFAULT NULL,
  `f24_amortizap3` double DEFAULT NULL,
  `f24_interespp3` double DEFAULT NULL,
  `f24_interespa3` double DEFAULT NULL,
  `f24_interespd3` double DEFAULT NULL,
  `f24_producto4` varchar(2) DEFAULT NULL,
  `f24_morapp` double DEFAULT NULL,
  `f24_morapa` double DEFAULT NULL,
  `f24_morapd` double DEFAULT NULL,
  `f24_morapp2` double DEFAULT NULL,
  `f24_morapa2` double DEFAULT NULL,
  `f24_morapd2` double DEFAULT NULL,
  `f24_morapp3` double DEFAULT NULL,
  `f24_morapa3` double DEFAULT NULL,
  `f24_morapd3` double DEFAULT NULL,
  `f24_producto5` varchar(2) DEFAULT NULL,
  `f24_amortizap4` double DEFAULT NULL,
  `f24_interespp4` double DEFAULT NULL,
  `f24_interespa4` double DEFAULT NULL,
  `f24_interespd4` double DEFAULT NULL,
  `f24_morapp4` double DEFAULT NULL,
  `f24_morapa4` double DEFAULT NULL,
  `f24_morapd4` double DEFAULT NULL,
  `f24_producto6` varchar(2) DEFAULT NULL,
  `f24_amortizap5` double DEFAULT NULL,
  `f24_interespp5` double DEFAULT NULL,
  `f24_interespa5` double DEFAULT NULL,
  `f24_interespd5` double DEFAULT NULL,
  `f24_morapp5` double DEFAULT NULL,
  `f24_morapa5` double DEFAULT NULL,
  `f24_morapd5` double DEFAULT NULL,
  `f24_cajaProd` varchar(2) DEFAULT NULL,
  `f24_amortizaCaja` double DEFAULT NULL,
  `f24_interesCaja` double DEFAULT NULL,
  `f24_moraCaja` double DEFAULT NULL,
  `f24_producto7` varchar(2) DEFAULT NULL,
  `f24_amortizap6` double DEFAULT NULL,
  `f24_interespp6` double DEFAULT NULL,
  `f24_interespa6` double DEFAULT NULL,
  `f24_interespd6` double DEFAULT NULL,
  `f24_morapp6` double DEFAULT NULL,
  `f24_morapa6` double DEFAULT NULL,
  `f24_morapd6` double DEFAULT NULL,
  `f24_producto8` varchar(2) DEFAULT NULL,
  `f24_amortizap7` double DEFAULT NULL,
  `f24_interespp7` double DEFAULT NULL,
  `f24_interespa7` double DEFAULT NULL,
  `f24_interespd7` double DEFAULT NULL,
  `f24_morapp7` double DEFAULT NULL,
  `f24_morapa7` double DEFAULT NULL,
  `f24_morapd7` double DEFAULT NULL,
  `f24_producto9` varchar(2) DEFAULT NULL,
  `f24_amortizap8` double DEFAULT NULL,
  `f24_interespp8` double DEFAULT NULL,
  `f24_interespa8` double DEFAULT NULL,
  `f24_interespd8` double DEFAULT NULL,
  `f24_morapp8` double DEFAULT NULL,
  `f24_morapa8` double DEFAULT NULL,
  `f24_morapd8` double DEFAULT NULL,
  `f24_producto10` varchar(2) DEFAULT NULL,
  `f24_amortizap9` double DEFAULT NULL,
  `f24_interespp9` double DEFAULT NULL,
  `f24_interespa9` double DEFAULT NULL,
  `f24_interespd9` double DEFAULT NULL,
  `f24_morapp9` double DEFAULT NULL,
  `f24_morapa9` double DEFAULT NULL,
  `f24_morapd9` double DEFAULT NULL,
  KEY `Index_1` (`f24_socio`,`f24_periodo`),
  KEY `Index_2` (`f24_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f25_acciones`
--

DROP TABLE IF EXISTS `f25_acciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f25_acciones` (
  `f25_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f25_nombre` varchar(120) DEFAULT NULL,
  `f25_ruta` varchar(200) DEFAULT NULL,
  `f25_tipo` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`f25_codigo`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f26_perfil_accion`
--

DROP TABLE IF EXISTS `f26_perfil_accion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f26_perfil_accion` (
  `f26_perfil` int(10) unsigned NOT NULL,
  `f26_accion` int(10) unsigned NOT NULL,
  PRIMARY KEY (`f26_perfil`,`f26_accion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f27_aportes_cope`
--

DROP TABLE IF EXISTS `f27_aportes_cope`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f27_aportes_cope` (
  `f27_socio` int(16) unsigned NOT NULL,
  `f27_periodo` date DEFAULT NULL,
  `f27_aportacionP` float DEFAULT NULL,
  `f27_aportacionD` float DEFAULT NULL,
  `f27_diferencia` float DEFAULT NULL,
  `f27_fechaPago` date DEFAULT NULL,
  `f27_aportacion` float DEFAULT NULL,
  `f27_aporte_id` int(16) unsigned NOT NULL,
  KEY `Index_1` (`f27_socio`),
  KEY `Index_2` (`f27_periodo`),
  KEY `Index_3` (`f27_aporte_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f28_obs_detalle`
--

DROP TABLE IF EXISTS `f28_obs_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f28_obs_detalle` (
  `f28_socio` int(10) unsigned DEFAULT 0,
  `f28_titulo` varchar(100) DEFAULT NULL,
  `f28_detalle` varchar(500) DEFAULT NULL,
  `f28_fecha` datetime DEFAULT NULL,
  `f28_usuario` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f29_tramite`
--

DROP TABLE IF EXISTS `f29_tramite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f29_tramite` (
  `f29_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f29_numero` varchar(45) DEFAULT NULL,
  `f29_tipo` varchar(100) DEFAULT NULL,
  `f29_descripcion` text DEFAULT NULL,
  `f29_fisico` varchar(2) DEFAULT NULL,
  `f29_destino` varchar(200) DEFAULT NULL,
  `f29_origen` varchar(200) DEFAULT NULL,
  `f29_fecha` date DEFAULT NULL,
  `f29_ruta` text DEFAULT NULL,
  `f29_estado` varchar(100) DEFAULT NULL,
  `f29_obs` text DEFAULT NULL,
  `f29_referencia` text DEFAULT NULL,
  `f29_obs2` text DEFAULT NULL,
  `f29_respuesta` text DEFAULT NULL,
  `f29_nombres` varchar(300) DEFAULT NULL,
  `f29_obs3` text DEFAULT NULL,
  `f29_fechaF` date DEFAULT NULL,
  `f29_fechaE` date DEFAULT NULL,
  `f29_socio` varchar(14) DEFAULT NULL,
  `f29_importe` varchar(45) DEFAULT NULL,
  `f29_cuotas` varchar(45) DEFAULT NULL,
  `f29_interno` int(10) unsigned DEFAULT 1,
  `f29_hora_registro` varchar(10) DEFAULT NULL,
  `f29_hora_edicion` varchar(10) DEFAULT NULL,
  `f29_adjunto` varchar(2) DEFAULT NULL,
  `f29_usuario_registro` varchar(150) DEFAULT NULL,
  `f29_socio_dni` varchar(20) DEFAULT NULL,
  `f29_tramitante` varchar(250) DEFAULT NULL,
  `f29_tramitante_dni` varchar(45) DEFAULT NULL,
  `f29_ref_docu` varchar(2) DEFAULT NULL,
  `f29_ruta2` varchar(250) DEFAULT NULL,
  `f29_ruta3` varchar(250) DEFAULT NULL,
  `f29_tramitante_cel` varchar(45) DEFAULT NULL,
  `f29_tramitante_email` varchar(250) DEFAULT NULL,
  `f29_cuenta` varchar(45) DEFAULT NULL,
  `f29_tramitante_email2` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`f29_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=19827 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f30_historico`
--

DROP TABLE IF EXISTS `f30_historico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f30_historico` (
  `f30_codigo` int(10) unsigned NOT NULL DEFAULT 0,
  `f30_origen` varchar(500) DEFAULT NULL,
  `f30_destino` varchar(500) DEFAULT NULL,
  `f30_estado` varchar(50) DEFAULT NULL,
  `f30_fecha` date DEFAULT NULL,
  `f30_obs` text DEFAULT NULL,
  `f30_hora` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f31_productos`
--

DROP TABLE IF EXISTS `f31_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f31_productos` (
  `f31_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f31_nombre` varchar(200) NOT NULL,
  `f31_cronograma` varchar(10) NOT NULL,
  `f31_moneda` varchar(10) NOT NULL,
  `f31_max_cuotas` int(10) unsigned DEFAULT NULL,
  `f31_monto_min` float DEFAULT NULL,
  `f31_monto_max` float DEFAULT NULL,
  `f31_conGarante` varchar(2) DEFAULT NULL,
  `f31_estado` varchar(1) DEFAULT NULL,
  `f31_prioridad` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`f31_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f32_tarifa_fam`
--

DROP TABLE IF EXISTS `f32_tarifa_fam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f32_tarifa_fam` (
  `f32_cod_tarifa` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f32_rangoi` int(10) unsigned NOT NULL,
  `f32_rangof` int(10) unsigned NOT NULL,
  `f32_monto` float NOT NULL,
  PRIMARY KEY (`f32_cod_tarifa`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f33_persona_auditoria`
--

DROP TABLE IF EXISTS `f33_persona_auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f33_persona_auditoria` (
  `F01_ID_PERSONA` int(11) NOT NULL,
  `F01_TPO_DOC` int(11) DEFAULT NULL,
  `F01_NUM_DOC` varchar(15) DEFAULT NULL,
  `F01_APE_PAT` varchar(200) DEFAULT NULL,
  `F01_APE_MAT` varchar(200) DEFAULT NULL,
  `F01_NOMB_PRI` varchar(200) DEFAULT NULL,
  `F01_NOMB_SEG` varchar(200) DEFAULT NULL,
  `F01_SEXO` int(11) DEFAULT NULL,
  `F01_FE_NAC` date DEFAULT NULL,
  `F01_STDO_CIVIL` int(11) DEFAULT NULL,
  `F01_STDO_PERSN` int(11) DEFAULT NULL,
  `F01_CTA_CORREO` varchar(200) DEFAULT NULL,
  `F01_ID_NACIONALIDAD` int(11) DEFAULT NULL,
  `F01_ID_INSTRUCCION` int(11) DEFAULT NULL,
  `F01_ID_PROFESION` int(11) DEFAULT NULL,
  `F01_DPTO_NAC` varchar(2) DEFAULT NULL,
  `F01_PROV_NAC` varchar(2) DEFAULT NULL,
  `F01_DISTRI_NAC` varchar(2) DEFAULT NULL,
  `F01_TELF_FIJO` varchar(20) DEFAULT NULL,
  `F01_TELF_CEL` varchar(20) DEFAULT NULL,
  `F01_DESC_COMENT` varchar(600) DEFAULT NULL,
  `F01_USR_REG` int(11) DEFAULT NULL,
  `F01_FE_USR_REG` datetime DEFAULT NULL,
  `F01_USR_ACT` int(11) DEFAULT NULL,
  `F01_FE_USR_ACT` datetime DEFAULT NULL,
  `F01_ID_USUA_CREA` varchar(35) DEFAULT NULL,
  `F01_FE_USUA_CREA` datetime DEFAULT NULL,
  `F01_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F01_FE_USUA_MODI` datetime DEFAULT NULL,
  `F01_ZONA` varchar(250) DEFAULT NULL,
  `F01_VIA` varchar(100) DEFAULT NULL,
  `F01_INT` varchar(100) DEFAULT NULL,
  `F01_EXT` varchar(50) DEFAULT NULL,
  `F01_PISO` varchar(50) DEFAULT NULL,
  `F01_DEPARTAMENTO` varchar(2) DEFAULT NULL,
  `F01_PROVINCIA` varchar(2) DEFAULT NULL,
  `F01_DISTRITO` varchar(2) DEFAULT NULL,
  `F01_OBSERVACION` text DEFAULT NULL,
  `F01_EDAD` int(11) DEFAULT NULL,
  `F01_CENTRO` int(11) DEFAULT NULL,
  `F01_CONDICION` varchar(20) DEFAULT NULL,
  `F01_CUENTA` varchar(15) DEFAULT NULL,
  `F01_INGRESO` double DEFAULT NULL,
  `F01_APORTACION` double DEFAULT NULL,
  `F01_SALDO` double DEFAULT NULL,
  `F01_DOCUMENTO` varchar(200) DEFAULT NULL,
  `F01_SITUACION` varchar(70) DEFAULT NULL,
  `F01_FECHA_RETIRO` date DEFAULT NULL,
  `F01_FECHA_FALLE` date DEFAULT NULL,
  `F01_APORTA_BASE` int(11) DEFAULT NULL,
  `F01_APORTA_SOBRE` double DEFAULT NULL,
  `F01_SOBREVI` varchar(50) DEFAULT NULL,
  `F01_OBS` text DEFAULT NULL,
  `F01_FEC_REINGRESO` date DEFAULT NULL,
  `F01_APORTE_COPE` int(11) DEFAULT 0,
  `F01_OBS2` text DEFAULT NULL,
  `F01_TITULO` varchar(100) DEFAULT NULL,
  `F01_FIRMO` varchar(2) DEFAULT NULL,
  `F01_CARTA` varchar(45) DEFAULT NULL,
  `F01_FECHA_BCO` date DEFAULT NULL,
  `F01_USUA_CARTA` varchar(100) DEFAULT NULL,
  `F01_OBS_CARTA` text DEFAULT NULL,
  `F01_MTO_RIFA` double DEFAULT NULL,
  `F01_MTO_MULTA` double DEFAULT NULL,
  `F01_JUBILACION` varchar(50) DEFAULT NULL,
  `F01_MTO_OTRO` double DEFAULT NULL,
  `F01_TIPO_APORTE` varchar(45) DEFAULT NULL,
  `F01_CAMBIO_APORTE` varchar(2) DEFAULT NULL,
  `F01_ES_FAMILIAR` varchar(1) DEFAULT NULL,
  `F01_SOCIO` varchar(15) DEFAULT NULL,
  `F01_ACCION` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`F01_ID_PERSONA`),
  KEY `FK_f33_nacionalidad` (`F01_ID_NACIONALIDAD`),
  KEY `APEPAT2` (`F01_APE_PAT`),
  CONSTRAINT `FK_f33_nacionalidad` FOREIGN KEY (`F01_ID_NACIONALIDAD`) REFERENCES `f11_nacionalidad` (`F11_ID_NACIONALIDAD`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f35_tipo_documento`
--

DROP TABLE IF EXISTS `f35_tipo_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f35_tipo_documento` (
  `F35_DOCUMENTO_ID` int(11) NOT NULL,
  `F35_DOCUMENTO_DESC` varchar(200) NOT NULL,
  `F35_DOCUMENTO_ABRE` varchar(45) DEFAULT NULL,
  `F35_DOCUMENTO_INTERNO` varchar(1) DEFAULT NULL,
  `F35_DOCUMENTO_ESTADO` int(11) DEFAULT NULL,
  PRIMARY KEY (`F35_DOCUMENTO_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f36_correlativos_documentos`
--

DROP TABLE IF EXISTS `f36_correlativos_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f36_correlativos_documentos` (
  `CORRELATIVO_ID` int(11) NOT NULL,
  `CORRELATIVO_TIPO_DOC` int(11) DEFAULT NULL,
  `CORRELATIVO_SERIE` varchar(5) DEFAULT NULL,
  `CORRELATIVO_NUMERO_INICIO` varchar(45) DEFAULT NULL,
  `CORRELATIVO_NUMERO_FIN` varchar(45) DEFAULT NULL,
  `CORRELATIVO_NUMERO_ACTUAL` varchar(45) DEFAULT NULL,
  `CORRELATIVO_NUMERO_DEL` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`CORRELATIVO_ID`),
  KEY `FK_COR_TIPO_DOC_idx` (`CORRELATIVO_TIPO_DOC`),
  CONSTRAINT `FK_COR_TIPO_DOC` FOREIGN KEY (`CORRELATIVO_TIPO_DOC`) REFERENCES `f35_tipo_documento` (`F35_DOCUMENTO_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f36_correlativos_documentos4`
--

DROP TABLE IF EXISTS `f36_correlativos_documentos4`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f36_correlativos_documentos4` (
  `CORRELATIVO_ID` int(11) NOT NULL,
  `CORRELATIVO_TIPO_DOC` int(11) DEFAULT NULL,
  `CORRELATIVO_SERIE` varchar(5) DEFAULT NULL,
  `CORRELATIVO_NUMERO_INICIO` varchar(45) DEFAULT NULL,
  `CORRELATIVO_NUMERO_FIN` varchar(45) DEFAULT NULL,
  `CORRELATIVO_NUMERO_ACTUAL` varchar(45) DEFAULT NULL,
  `CORRELATIVO_NUMERO_DEL` int(10) unsigned DEFAULT 0,
  PRIMARY KEY (`CORRELATIVO_ID`),
  KEY `FK_COR_TIPO_DOC_idx_4` (`CORRELATIVO_TIPO_DOC`),
  CONSTRAINT `FK_COR_TIPO_DOC_4` FOREIGN KEY (`CORRELATIVO_TIPO_DOC`) REFERENCES `f35_tipo_documento` (`F35_DOCUMENTO_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f37_diario_contable`
--

DROP TABLE IF EXISTS `f37_diario_contable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f37_diario_contable` (
  `f37_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f37_movimiento` int(10) unsigned NOT NULL,
  `f37_fecha` date NOT NULL,
  `f37_codigo_contable` varchar(30) DEFAULT NULL,
  `f37_operacion` int(11) DEFAULT NULL,
  `f37_descripcion` varchar(255) DEFAULT NULL,
  `f37_debe` decimal(14,2) NOT NULL DEFAULT 0.00,
  `f37_haber` decimal(14,2) NOT NULL DEFAULT 0.00,
  `f37_usuario_crea` varchar(100) DEFAULT NULL,
  `f37_fecha_crea` datetime NOT NULL,
  `f37_estado` varchar(20) NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (`f37_codigo`),
  UNIQUE KEY `uq_f37_movimiento` (`f37_movimiento`),
  KEY `idx_f37_fecha` (`f37_fecha`),
  KEY `idx_f37_codigo_contable` (`f37_codigo_contable`),
  KEY `idx_f37_operacion` (`f37_operacion`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f38_codigo_contable`
--

DROP TABLE IF EXISTS `f38_codigo_contable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f38_codigo_contable` (
  `codigo` varchar(20) NOT NULL,
  `nombre_cuenta` varchar(150) NOT NULL,
  `codigo_padre` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f62_parentesco`
--

DROP TABLE IF EXISTS `f62_parentesco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f62_parentesco` (
  `F62_ID_TPO_PARENT` int(11) NOT NULL,
  `F62_DESC_CORTA` varchar(10) NOT NULL,
  `F62_DESC_LARGA` varchar(100) NOT NULL,
  `F62_STDO_PARENT` int(11) NOT NULL,
  `F62_ID_USUA_CREA` varchar(35) NOT NULL,
  `F62_FE_USUA_CREA` datetime NOT NULL,
  `F62_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F62_FE_USUA_MODI` datetime DEFAULT NULL,
  PRIMARY KEY (`F62_ID_TPO_PARENT`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f63_multa`
--

DROP TABLE IF EXISTS `f63_multa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f63_multa` (
  `f63_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f63_socio` varchar(45) NOT NULL,
  `f63_periodo` date NOT NULL,
  `f63_multap` float DEFAULT NULL,
  `f63_multad` float DEFAULT NULL,
  `f63_multar` float DEFAULT NULL,
  `f63_fechaPago` datetime DEFAULT NULL,
  `f63_formaPago` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`f63_codigo`),
  KEY `Index_socio` (`f63_socio`),
  KEY `Index_periodo` (`f63_periodo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f64_rifa`
--

DROP TABLE IF EXISTS `f64_rifa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f64_rifa` (
  `f64_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f64_socio` varchar(45) NOT NULL,
  `f64_periodo` date NOT NULL,
  `f64_rifap` float DEFAULT NULL,
  `f64_rifad` float DEFAULT NULL,
  `f64_rifar` float DEFAULT NULL,
  `f64_fechaPago` datetime DEFAULT NULL,
  `f64_formaPago` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`f64_codigo`),
  KEY `Index_socio` (`f64_socio`),
  KEY `Index_periodo` (`f64_periodo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f92_persona_baja`
--

DROP TABLE IF EXISTS `f92_persona_baja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f92_persona_baja` (
  `F01_ID_PERSONA` int(10) unsigned NOT NULL,
  `F01_TPO_DOC` int(11) DEFAULT NULL,
  `F01_NUM_DOC` varchar(15) DEFAULT NULL,
  `F01_APE_PAT` varchar(300) DEFAULT NULL,
  `F01_APE_MAT` varchar(50) DEFAULT NULL,
  `F01_NOMB_PRI` varchar(50) DEFAULT NULL,
  `F01_NOMB_SEG` varchar(50) DEFAULT NULL,
  `F01_SEXO` int(11) DEFAULT NULL,
  `F01_FE_NAC` datetime DEFAULT NULL,
  `F01_STDO_CIVIL` int(11) DEFAULT NULL,
  `F01_STDO_PERSN` int(11) DEFAULT NULL,
  `F01_CTA_CORREO` varchar(50) DEFAULT NULL,
  `F01_ID_NACIONALIDAD` int(11) DEFAULT NULL,
  `F01_ID_INSTRUCCION` int(11) DEFAULT NULL,
  `F01_ID_PROFESION` int(11) DEFAULT NULL,
  `F01_DPTO_NAC` varchar(2) DEFAULT NULL,
  `F01_PROV_NAC` varchar(2) DEFAULT NULL,
  `F01_DISTRI_NAC` varchar(2) DEFAULT NULL,
  `F01_TELF_FIJO` varchar(20) DEFAULT NULL,
  `F01_TELF_CEL` varchar(20) DEFAULT NULL,
  `F01_DESC_COMENT` varchar(500) DEFAULT NULL,
  `F01_USR_REG` int(11) DEFAULT NULL,
  `F01_FE_USR_REG` datetime DEFAULT NULL,
  `F01_USR_ACT` int(11) DEFAULT NULL,
  `F01_FE_USR_ACT` datetime DEFAULT NULL,
  `F01_ID_USUA_CREA` varchar(35) DEFAULT NULL,
  `F01_FE_USUA_CREA` datetime DEFAULT NULL,
  `F01_ID_USUA_MODI` varchar(35) DEFAULT NULL,
  `F01_FE_USUA_MODI` datetime DEFAULT NULL,
  `F01_ZONA` varchar(250) DEFAULT NULL,
  `F01_VIA` varchar(100) DEFAULT NULL,
  `F01_INT` varchar(100) DEFAULT NULL,
  `F01_EXT` varchar(50) DEFAULT NULL,
  `F01_PISO` varchar(50) DEFAULT NULL,
  `F01_DEPARTAMENTO` varchar(2) DEFAULT NULL,
  `F01_PROVINCIA` varchar(2) DEFAULT NULL,
  `F01_DISTRITO` varchar(2) DEFAULT NULL,
  `F01_OBSERVACION` varchar(1000) DEFAULT NULL,
  `F01_EDAD` int(10) unsigned DEFAULT NULL,
  `F01_CENTRO` int(11) DEFAULT NULL,
  `F01_CONDICION` varchar(20) DEFAULT NULL,
  `F01_CUENTA` varchar(15) DEFAULT NULL,
  `F01_INGRESO` float DEFAULT NULL,
  `F01_APORTACION` float DEFAULT NULL,
  `F01_SALDO` float DEFAULT NULL,
  `F01_DOCUMENTO` varchar(200) DEFAULT NULL,
  `F01_SITUACION` varchar(70) DEFAULT NULL,
  `F01_FECHA_RETIRO` date DEFAULT NULL,
  `F01_FECHA_FALLE` date DEFAULT NULL,
  `F01_APORTA_BASE` int(10) unsigned DEFAULT NULL,
  `F01_APORTA_SOBRE` float DEFAULT NULL,
  `F01_SOBREVI` varchar(50) DEFAULT NULL,
  `F01_OBS` varchar(500) DEFAULT NULL,
  `F01_FEC_REINGRESO` date DEFAULT NULL,
  `F01_APORTE_COPE` int(10) unsigned DEFAULT 0,
  `F01_OBS2` varchar(500) DEFAULT NULL,
  `F01_TITULO` varchar(100) DEFAULT NULL,
  `F01_FIRMO` varchar(2) DEFAULT NULL,
  `F01_CARTA` varchar(45) DEFAULT NULL,
  `F01_FECHA_BCO` date DEFAULT NULL,
  `F01_USUA_CARTA` varchar(100) DEFAULT NULL,
  `F01_OBS_CARTA` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`F01_ID_PERSONA`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `f99_documentos`
--

DROP TABLE IF EXISTS `f99_documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `f99_documentos` (
  `f99_codigo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `f99_nombre` varchar(300) DEFAULT NULL,
  `f99_archivo` varchar(500) DEFAULT NULL,
  `f99_fecha` date DEFAULT NULL,
  `f99_estado` varchar(10) DEFAULT NULL,
  `f99_tipo` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`f99_codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `v37_diario_contable_detalle`
--

DROP TABLE IF EXISTS `v37_diario_contable_detalle`;
/*!50001 DROP VIEW IF EXISTS `v37_diario_contable_detalle`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `v37_diario_contable_detalle` AS SELECT
 1 AS `f37_codigo`,
  1 AS `f37_movimiento`,
  1 AS `f37_fecha`,
  1 AS `f37_codigo_contable`,
  1 AS `f37_operacion`,
  1 AS `f37_nombre_operacion`,
  1 AS `f37_descripcion`,
  1 AS `f37_debe`,
  1 AS `f37_haber`,
  1 AS `f37_usuario_crea`,
  1 AS `f37_fecha_crea`,
  1 AS `f37_estado`,
  1 AS `f15_cliente`,
  1 AS `f15_formaPago`,
  1 AS `f15_cheque`,
  1 AS `f15_ingreso`,
  1 AS `f15_megreso`,
  1 AS `f15_obs`,
  1 AS `f15_std` */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v37_diario_contable_detalle`
--

/*!50001 DROP VIEW IF EXISTS `v37_diario_contable_detalle`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v37_diario_contable_detalle` AS select `d`.`f37_codigo` AS `f37_codigo`,`d`.`f37_movimiento` AS `f37_movimiento`,`d`.`f37_fecha` AS `f37_fecha`,`d`.`f37_codigo_contable` AS `f37_codigo_contable`,`d`.`f37_operacion` AS `f37_operacion`,`o`.`f14_nombre` AS `f37_nombre_operacion`,`d`.`f37_descripcion` AS `f37_descripcion`,`d`.`f37_debe` AS `f37_debe`,`d`.`f37_haber` AS `f37_haber`,`d`.`f37_usuario_crea` AS `f37_usuario_crea`,`d`.`f37_fecha_crea` AS `f37_fecha_crea`,`d`.`f37_estado` AS `f37_estado`,`m`.`f15_cliente` AS `f15_cliente`,`m`.`f15_formaPago` AS `f15_formaPago`,`m`.`f15_cheque` AS `f15_cheque`,`m`.`f15_ingreso` AS `f15_ingreso`,`m`.`f15_megreso` AS `f15_megreso`,`m`.`f15_obs` AS `f15_obs`,`m`.`f15_std` AS `f15_std` from ((`f37_diario_contable` `d` left join `f15_movimiento` `m` on(`m`.`f15_codigo` = `d`.`f37_movimiento`)) left join `f14_operacion` `o` on(`o`.`f14_codigo` = `d`.`f37_operacion`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:20
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f01_persona`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f01_persona` DISABLE KEYS */;
INSERT INTO `f01_persona` VALUES (2,1,'','SOTIL','DIANDERAS','JESUS','',1,NULL,1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'LOS NAZCAS 175 MARANGA SAN MIGUEL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,1,'06603876','SOTIL','MARTINEZ CAÐAMERO','MARIA','ANDREA',1,'1923-11-30 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4351225',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1998-03-23 00:00:00',NULL,NULL,'LOS SAUCOS 150 URB. SALAMANCA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,87,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,1,'25761237','SOTIL','PACHECO','LUIS','ANTONIO',1,'1948-01-17 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2551232',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1981-07-10 00:00:00',NULL,NULL,'CALLE MADAGASCAR MZ. F-1 LT. 3 URB. CEDROS DE VILLA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,62,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,1,'09599230','SOTO','AGUILAR','ARCENIO','HECTOR',1,'1945-07-22 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'3245112',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-02-20 00:00:00',NULL,NULL,'JR. RAUL VILLARAN PASQUEL 363 TERCER PISO LETRA C URB LAS MORAS SAN LUIS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,65,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,1,'07856577','SOTO','BYRNE','LUIS','EDIE',1,'1934-04-20 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1980-01-10 00:00:00',NULL,NULL,'CALLE 4 L 21 LA CALERA SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,76,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,1,'08860107','SOTO','DE LA CRUZ','ARTURO','',1,'1949-02-17 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2715662',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1995-10-20 00:00:00',NULL,NULL,'CALLE ALFA AGUILA MZ. D LT. 10  LA CALERA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,61,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,1,'31121334','SOTO','PUGA','ALEJANDRO','',1,'1943-03-10 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-10-01 00:00:00',NULL,NULL,'ASOC.LOS CHASQUIS MZ C LT 3 - SAN MARTIN DE PORRES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,67,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,1,'06826648','SOTO','YASAKI BERMUDEZ','AMELIA','',1,'1946-12-17 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'5433249',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1994-04-04 00:00:00',NULL,NULL,'AV.TRAPICHE MZ I LT 21 TUNGASUCA - CARABAYLLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,64,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,1,'08269126','SOTOMAYOR','BRUNNER','RICARDO','',1,'1936-04-01 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1987-02-27 00:00:00',NULL,NULL,'RESIDENCIAL STA CRUZ T-407 SAN ISIDRO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,74,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,1,'04004807','SUAREZ','MEJIA','JESUS','EXALTACION',1,'1947-09-14 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'0639339349',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1989-01-25 00:00:00',NULL,NULL,'AV. EVITAMIENTO 869 B TAMBO HUANCAYO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,63,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,1,'32381627','SUAREZ','MORALES','WASHINGTON','INOCENTE',1,'1949-12-28 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'391111',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1989-04-12 00:00:00',NULL,NULL,'JR. SAN MARTIN 504 CARAZ HUAYLAS ANCASH',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,61,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,1,'08364065','SUAREZ','ORIHUELA','FERNANDO','',1,'1935-04-23 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1998-08-10 00:00:00',NULL,NULL,'URB.MARISCAL CACERES AV.BAYOVAR ESTE 323',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,75,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,1,'','SUMAR','BAGLIS','GLADYS','',1,NULL,1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'SAN FELIPE BLOCK 1C-402 JESUS MARIA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,1,'06596970','TABOADA','MUNOZ','AMADOR','',1,'1931-01-24 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2720384',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1986-08-26 00:00:00',NULL,NULL,'EL ALCAZAR 139 DPTO 201 - LA CASTELLANA SURCO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,79,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,1,'09082198','TAGLE','ORTIZ','AMALIA','NANCY',1,'1952-09-11 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-10-29 00:00:00',NULL,NULL,'CALLE YANACOTO 124  SAN MIGUEL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,58,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,1,'32386346','TAMARIZ','ASCENCIO','RODOLFO','ARTURO',1,'1950-07-29 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1989-04-12 00:00:00',NULL,NULL,'LAS PALMERAS MZ.5 LT.4 CARAZ',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,60,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,1,'06149907','TAMARIZ','BAYONA','RUTH','FRANCISCA',1,'1952-03-04 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4490937',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1993-05-10 00:00:00',NULL,NULL,'PEREREWSKY MZ. N LT. 4 CALERA DE LA MERCED',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,58,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,1,'10340855','TAPIA','ALVAREZ','ANA','MARIA',1,'1956-04-20 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'999213282',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1989-02-16 00:00:00',NULL,NULL,'LA MAGNOLIA R-23 URB LOS SAUCES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,54,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,1,'08824367','TAPIA','RECHARTE','LEONIDAS','EDUARDO',1,'1933-07-03 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4499089',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1994-08-10 00:00:00',NULL,NULL,'EMILIANO ZAPATA MZ Z LT 15 LA CALERA SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,77,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,1,'25659329','TAPIA','VILLAVICENCIO','CARMEN','ROSA',1,'1943-09-02 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4575824',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-11-10 00:00:00',NULL,NULL,'AV. VICTOR FAJARDO 460 CALLAO BELLAVISTA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,67,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,1,'25668973','TAQUEDA','MORANTE','AUGUSTO','RICARDO',1,'1952-04-21 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1995-08-17 00:00:00',NULL,NULL,'LINCOLN 575 LA PERLA CALLAO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,58,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,1,'08812194','TARMENO','SANTANA GUERRERO','MARIA','LUISA',1,'1929-09-29 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4499186',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1999-12-10 00:00:00',NULL,NULL,'AVIACION 4045 LA CALERA DE LA MERCED',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,81,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,1,'06823566','TAVARA','AGUILAR','VICTOR','MANUEL',1,'1933-12-25 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4872042',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1995-05-10 00:00:00',NULL,NULL,'AV. CHILLON MZ. I LT. 18 CARABAYLLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,77,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,1,'02532249','TAIPE','YUCRA','OBALDINO','',1,'1938-11-15 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'5433275',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1989-10-10 00:00:00',NULL,NULL,'ANTONIO BASTIDOS 157 MZ. L LT. 31 TUNGAZUCA CARABAYLLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,72,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,1,'08839103','TEJADA','ANDUAGA','MARCO','ANTONIO',1,'1946-03-18 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2730154',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'CALLE ROBINSON LA CALERA SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,64,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,1,'08191447','TEJADA','NAVARRO','AIDEE','CAROLA',1,'1944-11-16 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1988-06-22 00:00:00',NULL,NULL,'LOS SAUCES 181 JACARANDA    SAN BORJA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,66,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,1,'08191447','TEJEDA','CABELLO','CARLOS','ALBERTO',1,'1951-04-21 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'96325204',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-11-10 00:00:00',NULL,NULL,'JR. PEDREGAL 303 COOP. POLICIAL SAN MARTIN DE PORRES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,59,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,1,'21424636','TEJEDA','CALLIRGOS','LUIS','ALBERTO',1,'1950-02-13 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4706416',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1994-12-10 00:00:00',NULL,NULL,'LAS ABSIDIARIAS 1520-B LA VICTORIA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,60,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,1,'08836738','TELLES','GARCIA','CARLOS','ALBERTO',1,'1942-05-05 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-03-30 00:00:00',NULL,NULL,'CALLE 10 CALERA DE LA MERCED SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,68,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'SOBREGIROS',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,1,'07712519','TELLEZ','SOTO','GLADYS','AMERICA',1,'1949-12-31 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2731385',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-07-23 00:00:00',NULL,NULL,'CALLE DESCARTES 135 LA CALERA - SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,61,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,1,'07375881','TELLO','DAVILA','NELLY','ESPERANZA',1,'1940-08-01 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4608446',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1989-02-16 00:00:00',NULL,NULL,'BLOCK B-401 J.C.TELLO SAN MIGUEL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,70,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,1,'06130098','TELLO','HUANAMBAL','MARIA','TEREZA',1,'1937-12-25 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4592698',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1993-11-07 00:00:00',NULL,NULL,'CALLE KOTOSH 1246 URB.MANGOMARCA - ZARATE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,73,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,1,'08860581','TELLO','LEDESMA','ANDRES','',1,'1946-11-28 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'5721977',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-04-10 00:00:00',NULL,NULL,'JR. MOQUEGUA 3991 URB PERU SAN MARTIN DE PORRES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,64,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,1,'08860576','TELLO','LEDESMA','JUAN','JOSE',1,'1939-05-06 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1994-09-10 00:00:00',NULL,NULL,'AV. 3 MAZ. H LT. 1 LA CALERA DE LA MERCED SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,71,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,1,'09010890','TELLO','VASQUEZ','BERNARDINO','ANDRES',1,'1927-05-20 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4608317',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1994-09-10 00:00:00',NULL,NULL,'CALLE CACIQUE DE UMACHIRI 308 - 310 URB. SAN MIGUELITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,83,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,1,'09067842','TELLO','YARLEQUE','JOSE','TUCIDIDES',1,'1947-09-17 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'3629701',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1993-08-19 00:00:00',NULL,NULL,'LOS MILANOS 423 SANTA ANITA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,63,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38,1,'07185784','TENORIO','ARCE','VICTOR','JOSE',1,'1938-09-08 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4238917',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1988-03-24 00:00:00',NULL,NULL,'AV.CUBA 1252 JESUS MARIA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,72,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(39,1,'XXXXXXXXXX','TERAMAE','VASQUEZ','ESTHER','',1,'1951-10-13 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2006-02-28 00:00:00',NULL,NULL,'PQUE.STA.MARIA MAGDALENA  141 - PUEBLO LIBRE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,59,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(40,1,'09894176','TERAN','D BAZAN AHUMADA','NISIDA','DOLORES',1,'1923-09-20 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'5675615',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-05-10 00:00:00',NULL,NULL,'JR. JESUS VERA 1084 URB SAN GERMAN S.MARTIN DE PORRAS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,87,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,25,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(41,1,'08864883','TERRONES','DIAZ','CESAR','HUMBERTO',1,'1947-05-16 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4498405',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1995-02-10 00:00:00',NULL,NULL,'ESTE K-3 LA CALERA SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,63,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(42,1,'17913116','TERRONES','ESTELA','MARIA','ESPERANZA',1,'1938-04-16 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'201841',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-02-10 00:00:00',NULL,NULL,'AV. SANTA 571 - A TRUJILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,72,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'VOLUNTARIO',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(43,1,'08431166','TERRONES','VIGIL','CLARA','EULINA',1,'1948-11-26 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4357820',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-06-10 00:00:00',NULL,NULL,'PARQUE CHIMU 179 ATE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,62,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'FALLECIDO',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(44,1,'06070208','TEVES','DIAZ','WALTER','ENRIQUE',1,'1951-06-05 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'5673727',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1978-05-30 00:00:00',NULL,NULL,'JOSE GALVEZ 107- 109 URB. VALDIVIEZO SAN MARTIN DE PORRAS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,59,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(45,1,'08857651','TICONA','GUTIERREZ','MAXIMO','',1,'1946-02-21 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2712230',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1996-07-10 00:00:00',NULL,NULL,'CALLE ROBINSON 165 LA CALERA SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,64,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(46,1,'06596708','TIO','GONZALES','JOAQUIN','',1,'1930-06-30 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4357558',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1988-08-12 00:00:00',NULL,NULL,'LAS DROSERAS 167 RECAUDADORES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,80,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(47,1,'10345073','TIPACTI','MELGAREJO','CARLOS','',1,'1948-11-10 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'2732224',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1995-04-06 00:00:00',NULL,NULL,'CONJ. HAB. LA MERCED BLOCK 18 DPTO. 402',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,62,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48,1,'06598963','TIRADO','SOLAR','BETTY','ANITA',1,'1952-01-24 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1990-05-31 00:00:00',NULL,NULL,'TEOFILO CASTILLO 365 APOLLO PERU',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,58,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(49,1,'22760150','TOCTO','CEFERINO','TEODOSIO','',1,'1939-04-02 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1997-04-10 00:00:00',NULL,NULL,'JR. RIMAC 413 SAN MARTIN DE PORRES  LIMA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,71,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(50,1,'09859950','TORDOYA','VILLALOBOS','CARLOS','ALFONSO',1,'1952-05-28 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'95129686',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-08-10 00:00:00',NULL,NULL,'TENIENTE RAZURI 796 RIMAC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,58,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'SOBREGIROS',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(51,1,'10413003','TORERO','ECHAJALLA TOLEDO','JUSTINA','',1,'1953-02-28 00:00:00',1,1,NULL,1,NULL,NULL,NULL,NULL,NULL,'4368889',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2001-10-02 00:00:00',NULL,NULL,'LAS AMAPOLAS 195 URB. OLIMPO SALAMANCA ATE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,57,1,'PENSIONISTA',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,15,NULL,NULL,NULL,NULL,7,NULL,NULL,'NO',NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f01_persona` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:20
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f02_cliente`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f02_cliente` DISABLE KEYS */;
INSERT INTO `f02_cliente` VALUES ('46469',501,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'n2rL2nQf'),('46485',502,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Se5cSDB6'),('99414',503,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'XUb06gN1'),('99422',504,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'CrPvN3OE'),('99430',505,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'890J9bT2'),('46515',506,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'46NNqOSz'),('46531',507,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'hxaSDN8N'),('179140',508,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'YICY0xwa'),('46574',509,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'YswmZgWb'),('46663',510,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'iWmwRqNL'),('99651',511,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'g559abPW'),('126322',512,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'TlZ5IN85'),('46876',513,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'jiuedl4J'),('45861',514,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'WrxFv41D'),('46922',515,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'xytCz9xI'),('46949',516,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'KGajsYPY'),('99856',517,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'5bnVUpRC'),('46981',518,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'7KomYTBm'),('47023',519,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'RFHVKAHE'),('47058',520,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'aziGA1LY'),('105767',522,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'B7GlTQGZ'),('100013',524,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'PwR7ADPe'),('144002',525,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'MtdZZfmP'),('47155',526,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1v5gcFMf'),('69876',527,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'W54mG8dN'),('47163',528,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'rS9eBQMT'),('100137',529,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'kZcOehL0'),('47198',530,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'12345678'),('47201',531,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'dptrNUZu'),('100153',532,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Bl7VU0FE'),('38695',533,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'zdCcUNbN'),('47236',534,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'qixyF4Ka'),('47244',535,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'LUaf2qZG'),('47260',536,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'12345678'),('47287',537,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'RFitX5Zx'),('47309',538,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Pqvj3J8V'),('130206',539,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'6jewa2rS'),('100218',540,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'su0Gy3Fh'),('100242',541,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'FeR27QCt'),('100250',542,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'12345678'),('121479',543,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'12345678'),('47392',544,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'CaVN3cGV'),('47511',545,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ddnsgdWq'),('100315',546,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'miZmTnNT'),('47554',547,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Wa6lpwJ7'),('47570',548,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'JSRDGQ3Y'),('179205',549,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'0BWxETcx'),('47627',550,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'12345678'),('47635',551,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Jry5GRT2'),('129933',552,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Ikv7Hy3U');
/*!40000 ALTER TABLE `f02_cliente` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:20
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f03_motivo`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f03_motivo` DISABLE KEYS */;
/*!40000 ALTER TABLE `f03_motivo` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:20
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f05_ubigeo`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f05_ubigeo` DISABLE KEYS */;
INSERT INTO `f05_ubigeo` VALUES ('CHACHAPOYAS','CHACHAPOYAS','AMAZONAS','010101'),('ASUNCION','CHACHAPOYAS','AMAZONAS','010102'),('BALSAS','CHACHAPOYAS','AMAZONAS','010103'),('CHETO','CHACHAPOYAS','AMAZONAS','010104'),('CHILIQUIN','CHACHAPOYAS','AMAZONAS','010105'),('CHUQUIBAMBA','CHACHAPOYAS','AMAZONAS','010106'),('GRANADA','CHACHAPOYAS','AMAZONAS','010107'),('HUANCAS','CHACHAPOYAS','AMAZONAS','010108'),('LA JALCA','CHACHAPOYAS','AMAZONAS','010109'),('LEIMEBAMBA','CHACHAPOYAS','AMAZONAS','010110'),('LEVANTO','CHACHAPOYAS','AMAZONAS','010111'),('MAGDALENA','CHACHAPOYAS','AMAZONAS','010112'),('MARISCAL CASTILLA','CHACHAPOYAS','AMAZONAS','010113'),('MOLINOPAMPA','CHACHAPOYAS','AMAZONAS','010114'),('MONTEVIDEO','CHACHAPOYAS','AMAZONAS','010115'),('OLLEROS','CHACHAPOYAS','AMAZONAS','010116'),('QUINJALCA','CHACHAPOYAS','AMAZONAS','010117'),('SAN FRANCISCO DE DAGUAS','CHACHAPOYAS','AMAZONAS','010118'),('SAN ISIDRO DE MAINO','CHACHAPOYAS','AMAZONAS','010119'),('SOLOCO','CHACHAPOYAS','AMAZONAS','010120'),('SONCHE','CHACHAPOYAS','AMAZONAS','010121'),('BAGUA','BAGUA','AMAZONAS','010201'),('ARAMANGO','BAGUA','AMAZONAS','010202'),('COPALLIN','BAGUA','AMAZONAS','010203'),('EL PARCO','BAGUA','AMAZONAS','010204'),('IMAZA','BAGUA','AMAZONAS','010205'),('LA PECA','BAGUA','AMAZONAS','010206'),('JUMBILLA','BONGARA','AMAZONAS','010301'),('CHISQUILLA','BONGARA','AMAZONAS','010302'),('CHURUJA','BONGARA','AMAZONAS','010303'),('COROSHA','BONGARA','AMAZONAS','010304'),('CUISPES','BONGARA','AMAZONAS','010305'),('FLORIDA','BONGARA','AMAZONAS','010306'),('JAZAN','BONGARA','AMAZONAS','010307'),('RECTA','BONGARA','AMAZONAS','010308'),('SAN CARLOS','BONGARA','AMAZONAS','010309'),('SHIPASBAMBA','BONGARA','AMAZONAS','010310'),('VALERA','BONGARA','AMAZONAS','010311'),('YAMBRASBAMBA','BONGARA','AMAZONAS','010312'),('NIEVA','CONDORCANQUI','AMAZONAS','010401'),('EL CENEPA','CONDORCANQUI','AMAZONAS','010402'),('RIO SANTIAGO','CONDORCANQUI','AMAZONAS','010403'),('LAMUD','LUYA','AMAZONAS','010501'),('CAMPORREDONDO','LUYA','AMAZONAS','010502'),('COCABAMBA','LUYA','AMAZONAS','010503'),('COLCAMAR','LUYA','AMAZONAS','010504'),('CONILA','LUYA','AMAZONAS','010505'),('INGUILPATA','LUYA','AMAZONAS','010506'),('LONGUITA','LUYA','AMAZONAS','010507'),('LONYA CHICO','LUYA','AMAZONAS','010508');
/*!40000 ALTER TABLE `f05_ubigeo` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:20
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f06_usuario`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f06_usuario` DISABLE KEYS */;
INSERT INTO `f06_usuario` VALUES (1,'ADMIN',2,'4289',NULL,NULL,NULL,NULL,'A',NULL),(2,'demo',2,'123',NULL,NULL,NULL,NULL,'I',NULL),(3,'SUPERVISOR',2,'123',NULL,NULL,NULL,NULL,'A',NULL),(4,'MARLENEMAGALLANES',4,'271',NULL,NULL,NULL,NULL,'A',NULL),(5,'GAVYPERALES',2,'4289',NULL,NULL,NULL,NULL,'A',NULL),(6,'BEATRIZMARCOS',7,'1308',NULL,NULL,NULL,NULL,'A',NULL),(7,'BETTSY',5,'123',NULL,NULL,NULL,NULL,'I',NULL),(8,'AUX.ADM',6,'123456',NULL,NULL,NULL,NULL,'A',NULL),(9,'APOYO',6,'2002',NULL,NULL,NULL,NULL,'A',NULL),(10,'CELECTORAL',6,'1234',NULL,NULL,NULL,NULL,'I',NULL),(11,'SECRETARIA',4,'secretaria',NULL,NULL,NULL,NULL,'I',NULL),(12,'LIZBETHALCALDE',5,'123456',NULL,NULL,NULL,NULL,'A',NULL),(13,'CONTABILIDAD',3,'contabilidad',NULL,NULL,NULL,NULL,'I',NULL),(14,'CADMINISTRACION',6,'cadministracion',NULL,NULL,NULL,NULL,'I',NULL),(15,'CVIGILANCIA',6,'cvigilancia',NULL,NULL,NULL,NULL,'I',NULL),(16,'RICARDOZARATE',2,'123456',NULL,NULL,NULL,NULL,'A',NULL),(17,'WEB',8,'',NULL,NULL,NULL,NULL,'I',NULL),(18,'DEISYCAMPOS',4,'jd1125',NULL,NULL,NULL,NULL,'A','N'),(19,'NAYELYPONGO',10,'123456',NULL,NULL,NULL,NULL,'A','N'),(20,'DEUDAJUDICIAL',2,'12321',NULL,NULL,NULL,NULL,'A','N');
/*!40000 ALTER TABLE `f06_usuario` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:20
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f07_opcion`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f07_opcion` DISABLE KEYS */;
INSERT INTO `f07_opcion` VALUES (1,'Socios','listarSocios.jsp'),(2,'Prestamos','listarPrestamos.do'),(3,'Caja','caja.jsp'),(4,'Movimientos','mostrarMovimientos.do'),(5,'Reportes Prestamos','exportarAEE.do'),(6,'Reportes de Socios','rptSocios.jsp'),(7,'Tarifario','mostrarTarifario.do'),(8,'Seguridad','mostrarUsuarios.do'),(10,'Empresa','actualizarEmpresa.do'),(11,'Beneficios','listarBeneficios.do'),(12,'Generar Descuento Prestamos','generarDescuento.jsp'),(13,'Cargar Descuentos','cargarDescuento.jsp'),(14,'Emitir Cheques','mostrarCheques.do'),(15,'Descuento Definitivo Prestamos','descuentos.jsp'),(16,'Aportaciones FONBES','mostrarAportaciones.do'),(17,'Reporte Seguro Desgravamen','mostrarSeguro.do'),(18,'Reporte de Gastos','mostrarGastos.do'),(19,'Reporte de Descuentos','rptDescuentos.jsp'),(20,'Reporte Familiares','rptFamiliares.jsp'),(21,'Pronostico Ingresos','pronosticoIn.jsp'),(22,'Perfiles de Usuario','mostrarPerfiles.do'),(23,'Log de Auditoria','mostrarLog.do'),(24,'Caja Egresos','cajaEgresos.jsp'),(25,'Reporte Egresos','mostrarEgresos.do'),(26,'Accesos','asignarPerfil.jsp'),(27,'Accesos para Acciones','asignarAcciones.jsp'),(28,'Carga de Aportes','cargarAportacion.jsp'),(29,'Cronogramas','listarCrono.jsp'),(30,'Exportar Descuentos','exportarDescuentos.jsp'),(31,'Cambiar Contrase±a','cambiarClave.jsp'),(32,'Gestion de Socios','crmSocios.jsp'),(33,'Correos','correos.jsp'),(34,'Historico de Socios en Baja','operacionesBaja.jsp'),(35,'Movimientos de Socios en Baja','mostrarMovimientosBaja.do'),(36,'Exporta Data de Bajas','exportaBajas.jsp'),(37,'Tarifario de Beneficios','mostrarTBeneficios.do'),(38,'Tarifario de Aportes','mostrarAportesFB.do'),(39,'Productos COOPAM','mostrarProductos.do'),(40,'Carga Otros Conceptos','cargarOtros.jsp'),(41,'Generar Descuento ExtraOrdinario','generarDescuento2.jsp'),(42,'Descuento Definitivo ExtraOrdinario','descuentos2.jsp'),(43,'Aportaciones COOPAM','mostrarAportacionesCope.do'),(44,'Generar Descuento de 2 Productos','generarDescuento3.jsp'),(45,'Descuento Definitivo de 2 Productos','descuentos3.jsp'),(46,'Reporte Aportacion Pendiente','rpteAportaciones.jsp'),(47,'Control Familiares','beneficioSocio.jsp'),(48,'Generar DSCTO. Mutiple','generarDescuento4.jsp'),(49,'Descuento Definitivo Mutiple','descuentos4.jsp'),(50,'Consolidado de Prestamos','exportarConsolidado.do'),(51,'Mis Datos','mostrarDatosSocio.do');
/*!40000 ALTER TABLE `f07_opcion` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f08_perfil`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f08_perfil` DISABLE KEYS */;
INSERT INTO `f08_perfil` VALUES (1,'SUPERVISOR'),(2,'PROGRAMADOR'),(3,'CONTABILIDAD'),(4,'SECRETARIA'),(5,'COBRANZAS'),(6,'CONSULTAS'),(7,'INFORMES'),(8,'SOCIOWEB'),(9,'AUXILIAR'),(10,'AUXILIAR2');
/*!40000 ALTER TABLE `f08_perfil` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f09_profesion`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f09_profesion` DISABLE KEYS */;
INSERT INTO `f09_profesion` VALUES (1,'PROFESOR',1,'','0000-00-00 00:00:00',NULL,NULL),(2,'ADMINISTRADOR DE EMPRESAS',1,NULL,NULL,NULL,NULL),(3,'CONTADOR',1,NULL,NULL,NULL,NULL),(4,'ABOGADO',1,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f09_profesion` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f10_instruccion`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f10_instruccion` DISABLE KEYS */;
INSERT INTO `f10_instruccion` VALUES (1,'TECNICA',1,'','0000-00-00 00:00:00',NULL,NULL),(2,'UNIVERSITARIA INCOMPLETA',1,'','0000-00-00 00:00:00',NULL,NULL),(3,'UNIVERSITARIA COMPLETA',1,'','0000-00-00 00:00:00',NULL,NULL),(4,'TITULADO',1,'','0000-00-00 00:00:00',NULL,NULL);
/*!40000 ALTER TABLE `f10_instruccion` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f11_nacionalidad`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f11_nacionalidad` DISABLE KEYS */;
INSERT INTO `f11_nacionalidad` VALUES (1,'PERUANA',1,'','0000-00-00 00:00:00',NULL,NULL),(2,'CHILENA',1,'','0000-00-00 00:00:00',NULL,NULL),(3,'ARGENTINA',1,'','0000-00-00 00:00:00',NULL,NULL),(4,'COLOMBIANA',1,'','0000-00-00 00:00:00',NULL,NULL);
/*!40000 ALTER TABLE `f11_nacionalidad` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f12_familiar`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f12_familiar` DISABLE KEYS */;
INSERT INTO `f12_familiar` VALUES ('0010101',20660,1,1,NULL,NULL,NULL,NULL),('0010101',20661,2,1,NULL,NULL,NULL,NULL),('0010101',20662,3,1,NULL,NULL,NULL,NULL),('0010111',22605,1,1,NULL,NULL,NULL,NULL),('0011169',31937,11,1,NULL,NULL,NULL,NULL),('0017779',21216,2,1,NULL,NULL,NULL,NULL),('0017779',21217,2,1,NULL,NULL,NULL,NULL),('0021059',31211,11,1,NULL,NULL,NULL,NULL),('0021105015',20744,2,1,NULL,NULL,NULL,NULL),('0021105015',20745,2,1,NULL,NULL,NULL,NULL),('0021105015',20746,2,1,NULL,NULL,NULL,NULL),('0021105015',20747,2,1,NULL,NULL,NULL,NULL),('0021105015',20748,3,1,NULL,NULL,NULL,NULL),('0021105015',20749,9,1,NULL,NULL,NULL,NULL),('0021210',20832,1,1,NULL,NULL,NULL,NULL),('0021210',20833,2,1,NULL,NULL,NULL,NULL),('0021210',20834,2,1,NULL,NULL,NULL,NULL),('0026786',32467,11,1,NULL,NULL,NULL,NULL),('0027618',22106,2,1,NULL,NULL,NULL,NULL),('0027618',22107,2,1,NULL,NULL,NULL,NULL),('0030767012',20666,7,1,NULL,NULL,NULL,NULL),('0030767012',20667,3,1,NULL,NULL,NULL,NULL),('0030767012',20668,4,1,NULL,NULL,NULL,NULL),('0030767012',20669,9,1,NULL,NULL,NULL,NULL),('0030767012',20670,2,1,NULL,NULL,NULL,NULL),('0032905',32244,11,1,NULL,NULL,NULL,NULL),('0033898',23986,2,1,NULL,NULL,NULL,NULL),('0033898',23987,2,1,NULL,NULL,NULL,NULL),('0033898',23988,2,1,NULL,NULL,NULL,NULL),('0035653',21273,1,1,NULL,NULL,NULL,NULL),('0035653',21274,2,1,NULL,NULL,NULL,NULL),('0037826',21570,1,1,NULL,NULL,NULL,NULL),('0039209012',20789,2,1,NULL,NULL,NULL,NULL),('0039209012',20790,2,1,NULL,NULL,NULL,NULL),('0041645',31234,11,1,NULL,NULL,NULL,NULL),('0042439',22155,3,1,NULL,NULL,NULL,NULL),('0042439',22156,4,1,NULL,NULL,NULL,NULL),('0043044',31583,11,1,NULL,NULL,NULL,NULL),('0047457',28597,2,1,NULL,NULL,NULL,NULL),('0047457',28598,2,1,NULL,NULL,NULL,NULL),('0047457',28599,2,1,NULL,NULL,NULL,NULL),('0048410',21649,1,1,NULL,NULL,NULL,NULL),('0048410',21650,2,1,NULL,NULL,NULL,NULL),('0048410',21651,2,1,NULL,NULL,NULL,NULL),('0073695',21442,2,1,NULL,NULL,NULL,NULL),('0075620',29262,1,1,NULL,NULL,NULL,NULL),('0075620',29263,2,1,NULL,NULL,NULL,NULL),('0076171',31819,11,1,NULL,NULL,NULL,NULL),('0077682',22219,1,1,NULL,NULL,NULL,NULL),('0080195',21982,2,1,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f12_familiar` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f13_perfil_opcion`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f13_perfil_opcion` DISABLE KEYS */;
INSERT INTO `f13_perfil_opcion` VALUES (1,1),(1,7),(1,8),(1,10),(1,22),(1,23),(1,26),(1,27),(1,37),(1,38),(1,39),(1,40),(1,42),(1,43),(1,56),(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,10),(2,11),(2,12),(2,13),(2,14),(2,15),(2,16),(2,17),(2,18),(2,19),(2,20),(2,21),(2,22),(2,23),(2,24),(2,25),(2,26),(2,27),(2,28),(2,29),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,36);
/*!40000 ALTER TABLE `f13_perfil_opcion` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f14_operacion`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f14_operacion` DISABLE KEYS */;
INSERT INTO `f14_operacion` VALUES (1,'APORTACION FONBES'),(2,'AMORTIZACION AEE'),(3,'DESEMBOLSO AEE'),(4,'DEVOLUCION FONBES'),(5,'DEVOLUCION COOPAM'),(6,'CANCELACION AEE'),(7,'DESEMBOLSO BENEFICIOS'),(8,'EGRESOS'),(9,'APORTACION COOPAM'),(10,'PAGO SOBREGIRO PRESTAMO'),(11,'PAGO MULTA'),(12,'PAGO RIFA'),(13,'OTRAS DEVOLUCIONES'),(14,'PAGO SOBREGIRO FONBES'),(15,'PAGO SOBREGIRO COOPAM'),(16,'OTROS INGRESOS');
/*!40000 ALTER TABLE `f14_operacion` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f15_movimiento`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f15_movimiento` DISABLE KEYS */;
INSERT INTO `f15_movimiento` VALUES (14836,'2205-12-31',2,70793,NULL,NULL,50.00,NULL,NULL,'ADMIN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'OTROS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22340,'7200-04-20',2,288926,NULL,NULL,100.00,NULL,NULL,'ADMIN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,20,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'OTROS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34231,'6200-06-20',2,280127,NULL,NULL,150.00,NULL,NULL,'ADMIN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,30,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'OTROS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377105,'2013-01-02',7,140864,NULL,NULL,NULL,NULL,NULL,'MARLENE',NULL,NULL,NULL,NULL,NULL,'',3600,1734,'TRES MIL SEISCIENTOS  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377106,'2013-01-02',6,141399,NULL,NULL,262.02,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,6943,'',NULL,NULL,'DOSCIENTOS SESENTA Y CUATRO 20/100 NUEVOS SOLES',NULL,2.18,0,0,0,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377107,'2013-01-02',7,38091,NULL,NULL,NULL,NULL,NULL,'MARLENE',NULL,NULL,NULL,NULL,NULL,'',1800,1735,'MIL OCHOCIENTOS  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377108,'2013-01-02',7,38091,NULL,NULL,NULL,NULL,NULL,'MARLENE',NULL,NULL,NULL,NULL,NULL,'',2000,1736,'DOS MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377109,'2013-01-02',2,141461,NULL,NULL,192.50,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,9930,'',NULL,NULL,'DOSCIENTOS  00/100 NUEVOS SOLES',NULL,7.5,0,0,0,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377110,'2013-01-02',6,166324,NULL,NULL,1866.64,NULL,NULL,'ADMIN',NULL,NULL,NULL,NULL,9213,'',NULL,NULL,'MIL OCHOCIENTOS SESENTA Y SEIS 64/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'OTROS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377111,'2013-01-02',6,317608,NULL,NULL,1581.69,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,9933,'',NULL,NULL,'MIL QUINIENTOS OCHENTA Y TRES 69/100 NUEVOS SOLES',NULL,2,0,0,0,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377112,'2013-01-02',3,22616,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,2000,10513,'04617968',NULL,NULL,'DOS MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377113,'2013-01-02',8,0,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'QUINIENTOS  00/100 NUEVOS SOLES','APERTURA DE CAJA CHICA 2013',NULL,NULL,NULL,NULL,NULL,'LUISA BEATRIZ MARCOS ROJAS','40791651',500,8544,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377114,'2013-01-02',8,0,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'DOS 00/100 NUEVOS SOLES','MOVILIDAD DEJAR DESCUENTO PENSIONISTAS',NULL,NULL,NULL,NULL,NULL,'EDUARDO CHAVESTA PECHE','16561067',2,8545,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377115,'2013-01-03',1,171425,150,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,'CIENTO CINCUENTA  00/100 NUEVOS SOLES','Por el (los) Mes (es):09/2012 a 11/2012',NULL,NULL,NULL,NULL,21,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377116,'2013-01-03',9,171425,21,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,'VEINTIUNO 00/100 NUEVOS SOLES','Por el (los) Mes (es):09/2012 a 11/2012',NULL,NULL,NULL,NULL,21,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377117,'2013-01-03',10,297119,NULL,NULL,150.55,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,9674,'',NULL,NULL,'CIENTO CINCUENTA  55/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377118,'2013-01-03',3,166324,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,4000,10516,'DEP\'.EFECTIV',NULL,NULL,'CUATRO MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377119,'2013-01-03',3,180289,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,4000,10515,'DEP.EFECTIVO',NULL,NULL,'CUATRO MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377120,'2013-01-03',3,87203,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,2000,10514,'DEP.EFECTIVO',NULL,NULL,'DOS MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377121,'2013-01-03',6,87203,NULL,NULL,833.34,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,8294,'',NULL,NULL,'OCHOCIENTOS TREINTA Y TRES 34/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377122,'2013-01-03',1,132748,20,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,'VEINTE  00/100 NUEVOS SOLES','Por el (los) Mes (es):11/2012 a 12/2012',NULL,NULL,NULL,NULL,14,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377123,'2013-01-03',9,132748,14,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,'CATORCE  00/100 NUEVOS SOLES','Por el (los) Mes (es):11/2012 a 12/2012',NULL,NULL,NULL,NULL,14,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377124,'2013-01-03',1,132748,25,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,'VEINTICINCO 00/100 NUEVOS SOLES','Por el (los) Mes (es):01/2013',NULL,NULL,NULL,NULL,7,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377125,'2013-01-03',9,132748,7,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,'SIETE 00/100 NUEVOS SOLES','Por el (los) Mes (es):01/2013',NULL,NULL,NULL,NULL,7,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377126,'2013-01-03',10,276561,NULL,NULL,119.30,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,9824,'',NULL,NULL,'CIENTO DIECINUEVE  30/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377251,'2013-01-03',6,273791,NULL,NULL,666.68,NULL,NULL,'ADMIN',NULL,NULL,NULL,NULL,7863,'',NULL,NULL,'SEISCIENTOS SESENTA Y SEIS 68/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'OTROS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377252,'2013-01-03',6,297119,NULL,NULL,875.00,NULL,NULL,'ADMIN',NULL,NULL,NULL,NULL,9674,'',NULL,NULL,'OCHOCIENTOS SETENTA Y CINCO 00/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'OTROS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377253,'2013-01-03',6,22616,NULL,NULL,1083.33,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,8641,'',NULL,NULL,'MIL OCHENTA Y TRES 33/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377254,'2013-01-03',6,276537,NULL,NULL,2750.00,NULL,NULL,'ADMIN',NULL,NULL,NULL,NULL,10071,'',NULL,NULL,'DOS MIL SETECIENTOS CINCUENTA  00/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'OTROS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377255,'2013-01-03',3,276537,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,3900,10530,'04617970',NULL,NULL,'TRES MIL NOVECIENTOS  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377256,'2013-01-03',6,276561,NULL,NULL,3583.30,NULL,NULL,'ADMIN',NULL,NULL,NULL,NULL,9824,'',NULL,NULL,'TRES MIL QUINIENTOS OCHENTA Y TRES 30/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'OTROS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377257,'2013-01-03',3,222720,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,1500,10519,'04617976',NULL,NULL,'MIL QUINIENTOS  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377258,'2013-01-03',6,261742,NULL,NULL,2100.00,NULL,NULL,'ADMIN',NULL,NULL,NULL,NULL,9963,'',NULL,NULL,'DOS MIL CIEN  00/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'OTROS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377259,'2013-01-03',3,307327,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,2500,10523,'DEP.EFECTIVO',NULL,NULL,'DOS MIL QUINIENTOS  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377260,'2013-01-03',3,261742,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,3000,10532,'04617978',NULL,NULL,'TRES MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377261,'2013-01-03',6,307327,NULL,NULL,1458.33,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,8793,'',NULL,NULL,'MIL CUATROCIENTOS CINCUENTA Y OCHO 33/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377262,'2013-01-03',3,136360,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,2500,10520,'DEP.EFECTIVO',NULL,NULL,'DOS MIL QUINIENTOS  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377263,'2013-01-03',6,136360,NULL,NULL,1041.67,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,9392,'',NULL,NULL,'MIL CUARENTA Y UNO 67/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377264,'2013-01-03',3,176028,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,3000,10527,'DEP.EFECTIVO',NULL,NULL,'TRES MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377265,'2013-01-03',6,176028,NULL,NULL,666.67,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,8569,'',NULL,NULL,'SEISCIENTOS SESENTA Y SEIS 67/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377266,'2013-01-03',3,264997,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,2000,10524,'DEP.EFECTIVO',NULL,NULL,'DOS MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377267,'2013-01-03',3,297119,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,1500,10518,'DEP.EFECTIVO',NULL,NULL,'MIL QUINIENTOS  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377268,'2013-01-03',3,104817,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,3000,10463,'DEP.EFECTIVO',NULL,NULL,'TRES MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377269,'2013-01-03',3,24287,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,3000,10529,'DEP.EFECTIVO',NULL,NULL,'TRES MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377270,'2013-01-03',3,135216,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,2200,10521,'DEP.EFECTIVO',NULL,NULL,'DOS MIL DOSCIENTOS  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377271,'2013-01-03',6,135216,NULL,NULL,166.67,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,8630,'',NULL,NULL,'CIENTO SESENTA Y SEIS 67/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'DEPOSITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377272,'2013-01-03',3,273791,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,2000,10522,'DEP.EFECTIVO',NULL,NULL,'DOS MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377273,'2013-01-03',3,323004,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,2000,10528,'DEP.EFECTIVO',NULL,NULL,'DOS MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377274,'2013-01-03',3,271268,NULL,NULL,NULL,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,2000,10525,'DEP.EFECTIVO',NULL,NULL,'DOS MIL  00/100 NUEVOS SOLES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(120377275,'2013-01-03',2,47511,NULL,NULL,3500.00,NULL,NULL,'BEATRIZ',NULL,NULL,NULL,NULL,10413,'',NULL,NULL,'TRES MIL QUINIENTOS  00/100 NUEVOS SOLES',NULL,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'EFECTIVO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f15_movimiento` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f16_prestamo`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f16_prestamo` DISABLE KEYS */;
INSERT INTO `f16_prestamo` VALUES (1,'2005-01-01',500,261742,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-06-11',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-01-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'2005-02-01',500,280992,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-05',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-02-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'2005-03-01',500,238910,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'2005-01-01',500,276847,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-06-16',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-01-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'2005-02-01',500,20214,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-08-13',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-02-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'2005-03-01',500,282847,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'2005-03-01',400,270458,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-06-22',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'2005-03-01',500,286524,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'2005-02-01',300,280232,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-02',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-02-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'2005-02-01',500,174653,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-02-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'2004-12-01',500,251437,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2004-12-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'2005-03-01',500,105872,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-31',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'2005-03-01',500,223816,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-02',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'2005-01-01',500,273473,NULL,2,NULL,10,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-01-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'2005-03-01',500,239445,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'2005-03-01',500,280445,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,'2005-01-01',500,107514,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-01',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-01-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,'2005-03-01',500,106445,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-07',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,'2005-03-01',400,177466,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-02',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,'2005-03-01',300,253723,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-08-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,'2004-12-01',500,175900,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2004-12-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,'2005-03-01',500,282189,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-08-06',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,'2005-03-01',300,233226,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-15',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,'2005-03-01',400,296988,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-08-17',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,'2005-02-01',500,281875,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-02-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,'2004-12-01',500,279897,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2004-12-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,'2005-03-01',500,282227,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-02',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,'2005-03-01',500,48321,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,'2005-03-01',400,175986,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-08-18',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,'2005-03-01',400,282685,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,'2004-12-01',500,138487,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-09-03',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2004-12-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,'2005-03-01',100,29815,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,'2005-03-01',500,78468,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-05-19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,'2005-03-01',500,106585,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-31',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,'2005-03-01',500,15288,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2011-07-11',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,'2005-03-01',500,15393,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,'2004-12-01',500,144665,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2004-12-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38,'2005-03-01',500,16322,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(39,'2005-01-01',2000,17175,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-01-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(40,'2005-01-01',500,19860,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-01-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(41,'2005-03-01',500,21997,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-08',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(42,'2005-03-01',500,22799,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(43,'2005-03-01',500,22616,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(44,'2005-01-01',500,83135,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-31',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-01-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(45,'2005-03-01',300,28916,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(46,'2005-01-01',500,30767,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-01-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(47,'2005-03-01',300,31038,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-30',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48,'2005-03-01',200,145289,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-31',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-03-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(49,'2004-11-01',500,32506,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-22',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2004-11-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(50,'2005-02-01',500,69868,NULL,2,NULL,10,0,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,NULL,'2010-07-31',NULL,NULL,NULL,'0',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','2005-02-01',0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f16_prestamo` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f17_cuotasprestamo`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f17_cuotasprestamo` DISABLE KEYS */;
INSERT INTO `f17_cuotasprestamo` VALUES (1285,80,'2013-01-30',9.71,1.94,11.65,223,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,81,'2013-03-01',9.71,1.94,11.65,213,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,82,'2013-03-31',9.71,1.94,11.65,204,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,83,'2013-04-30',9.71,1.94,11.65,194,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,84,'2013-05-30',9.71,1.94,11.65,184,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,85,'2013-06-29',9.71,1.94,11.65,175,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,86,'2013-07-29',9.71,1.94,11.65,165,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,87,'2013-08-28',9.71,1.94,11.65,155,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,88,'2013-09-27',9.71,1.94,11.65,146,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,89,'2013-10-27',9.71,1.94,11.65,136,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,90,'2013-11-26',9.71,1.94,11.65,126,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,91,'2013-12-26',9.71,1.94,11.65,116,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,92,'2014-01-25',9.71,1.94,11.65,107,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,93,'2014-02-24',9.71,1.94,11.65,97,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,94,'2014-03-26',9.71,1.94,11.65,87,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,95,'2014-04-25',9.71,1.94,11.65,78,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,96,'2014-05-25',9.71,1.94,11.65,68,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,97,'2014-06-24',9.71,1.94,11.65,58,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,98,'2014-07-24',9.71,1.94,11.65,48,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,99,'2014-08-23',9.71,1.94,11.65,39,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,100,'2014-09-22',9.71,1.94,11.65,29,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,101,'2014-10-22',9.71,1.94,11.65,19,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,102,'2014-11-21',9.71,1.94,11.65,10,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(1285,103,'2014-12-21',9.58,1.94,11.65,0,'283533',11.65,'CANCELADO','2010-09-03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,84,'2013-01-18',9.8,1.96,11.76,177,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,85,'2013-02-17',9.8,1.96,11.76,167,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,86,'2013-03-19',9.8,1.96,11.76,157,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,87,'2013-04-18',9.8,1.96,11.76,147,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,88,'2013-05-18',9.8,1.96,11.76,138,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,89,'2013-06-17',9.8,1.96,11.76,128,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,90,'2013-07-17',9.8,1.96,11.76,118,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,91,'2013-08-16',9.8,1.96,11.76,108,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,92,'2013-09-15',9.8,1.96,11.76,98,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,93,'2013-10-15',9.8,1.96,11.76,89,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,94,'2013-11-14',9.8,1.96,11.76,79,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,95,'2013-12-14',9.8,1.96,11.76,69,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,96,'2014-01-13',9.8,1.96,11.76,59,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,97,'2014-02-12',9.8,1.96,11.76,49,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,98,'2014-03-14',9.8,1.96,11.76,40,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,99,'2014-04-13',9.8,1.96,11.76,30,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,100,'2014-05-13',9.8,1.96,11.76,20,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,101,'2014-06-12',9.8,1.96,11.76,10,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(939,102,'2014-07-12',10.2,1.96,11.76,1,'50105',11.76,'CANCELADO','2010-07-31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(561,88,'2013-01-15',1.96,0.39,2.35,28,'38717',2.35,'CANCELADO','2010-07-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(561,89,'2013-02-14',1.96,0.39,2.35,26,'38717',2.35,'CANCELADO','2010-07-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(561,90,'2013-03-16',1.96,0.39,2.35,24,'38717',2.35,'CANCELADO','2010-07-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(561,91,'2013-04-15',1.96,0.39,2.35,22,'38717',2.35,'CANCELADO','2010-07-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(561,92,'2013-05-15',1.96,0.39,2.35,20,'38717',2.35,'CANCELADO','2010-07-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(561,93,'2013-06-14',1.96,0.39,2.35,18,'38717',2.35,'CANCELADO','2010-07-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(561,94,'2013-07-14',1.96,0.39,2.35,16,'38717',2.35,'CANCELADO','2010-07-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f17_cuotasprestamo` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:21
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f18_empresa`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f18_empresa` DISABLE KEYS */;
INSERT INTO `f18_empresa` VALUES (1,'COOPERATIVA DE SERVICIOS ESPECIAES AYUDA MUTUA DE LOS EMPLEADOS Y EXEMPLEADOS BANCO DE LA NACION','AV DE LAS ARTES NORTE 578 - 586 SAN BORJA','4761821','20147834587',1,1,1,1,10000,10,11);
/*!40000 ALTER TABLE `f18_empresa` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f19_tarifario`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f19_tarifario` DISABLE KEYS */;
INSERT INTO `f19_tarifario` VALUES (1,1,1.25,'2022-08-15','2022-12-31','A',1),(2,2,2,'2010-04-05','2021-12-31','I',1),(3,1,99,'2010-04-05','2010-04-05','I',1),(8,1,2,'2022-12-03','2022-12-03','I',2),(9,2,2,'2020-12-04','2020-12-04','I',2),(10,1,2,'2026-12-01','2026-12-01','A',3),(11,2,2,'2023-04-01','2023-04-01','I',3),(12,0,2,'2016-07-01','2016-07-01','A',NULL),(13,81,3500,'2011-01-01','2011-12-31','A',NULL),(14,83,10,'2011-01-01','2011-12-31','A',NULL),(15,0,2,'2016-07-01','2016-07-01','A',NULL),(16,81,3500,'2007-10-13','2012-12-31','A',NULL),(17,81,4000,'2011-05-16','2011-12-31','A',NULL),(18,4,2,'2022-08-15','2022-12-31','A',1),(19,4,2,'2026-07-01','2026-07-01','A',2),(20,4,2,'2026-12-01','2026-12-01','A',3),(21,0,2,'2016-07-01','2016-07-01','A',NULL),(22,4,0,'2014-06-02','2017-12-31','A',5),(23,3,15,'2022-08-15','2022-12-31','A',1),(24,3,26.82,'2020-12-04','2020-12-04','I',2),(25,3,24,'2026-12-01','2026-12-01','A',3),(26,0,25,'2016-07-01','2016-07-01','A',NULL),(27,1,2,'2024-08-01','2024-08-01','A',4),(28,4,2,'2023-07-07','2023-07-03','A',4),(29,1,1.0833,'2021-01-25','2021-12-31','A',6),(30,4,2,'2019-05-01','2019-12-31','A',6),(31,3,13,'2022-09-10','2022-09-10','A',6),(32,1,1.25,'2016-03-29','2026-03-31','A',5),(33,1,0.41667,'2022-08-15','2026-12-31','A',7),(34,4,2,'2022-08-15','2022-12-31','A',7),(35,1,2,'2026-04-01','2026-04-01','A',8),(36,4,2,'2026-04-01','2026-04-01','A',8),(37,1,1.25,'2022-08-15','2022-12-31','A',9),(38,4,2,'2022-08-15','2022-12-31','A',9),(39,1,1.25,'2022-08-15','2022-12-31','A',10),(40,4,0,'2022-08-15','2022-12-31','A',10),(41,3,5,'2022-08-15','2026-12-31','A',7),(42,3,15,'2022-08-15','2022-12-31','A',9),(43,3,24,'2027-04-01','2027-04-01','A',11),(44,1,2,'2027-04-01','2027-04-01','A',11),(45,4,2,'2027-04-01','2027-04-01','A',11),(46,1,2,'2026-07-01','2026-07-01','A',2),(47,3,24,'2026-07-01','2026-07-01','A',2),(48,3,24,'2023-07-07','2023-07-07','A',4),(49,3,24,'2026-04-01','2026-04-01','A',8),(50,3,15,'2022-08-15','2022-12-31','A',10),(51,3,15,'2016-10-04','2026-10-04','A',5);
/*!40000 ALTER TABLE `f19_tarifario` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f20_concepto`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f20_concepto` DISABLE KEYS */;
INSERT INTO `f20_concepto` VALUES (1,'Tasa Interes Mensual'),(2,'Seguro Desgravamen'),(3,'Tasa Interes Anual'),(4,'Gasto Tramite Administrativo'),(6,'Valor Beneficio'),(7,'Valor Aporte'),(81,'Tipo 1'),(82,'Tipo 2'),(83,'Aporte FONBES'),(84,'Aporte COOPAM');
/*!40000 ALTER TABLE `f20_concepto` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f21_beneficios`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f21_beneficios` DISABLE KEYS */;
INSERT INTO `f21_beneficios` VALUES (1,18139,'BASTANTE TOLEDO HUGO','Titular','2006-08-07','CIPIRAN  DUEÐAS DE BASTANTE FLORA ELENA','Conyuge','2006-08-23',2700,'ENTREGADO','06250075','VALDEMAR MOSSER 436 DPTO 201 BLOCK 7 SURQUILLO','06157326','VALDEMAR MOSSER 436 DPTO 201 BLOCK 7 SURQUILLO  ','DESEMBOLSADO','',0,'PARTIDA DE MATRIMONIO',NULL,NULL,NULL),(2,240176,'RICCI  BERNARDINI  SANTOS CAROLINA','Hermano(a)','2006-08-11','RICCI BERNARDINI MANUEL MARIA ELEAZAR','Titular','2006-08-24',2700,'ENTREGADO','07015737','STA RITA N║ 100 CHORRILLOS  ','06992546','CIPRIANO N RIVAS 328 INT 8 CHORRILLOS','DESEMBOLSADO','',0,'PARTIDA DE DEFUNCION',NULL,NULL,NULL),(3,172812,'VELARDE  JARA  HERLINDA ','Madre','2006-08-11','SUAZO VELARDE JOSE LUIS','Titular','2006-08-23',2700,'ENTREGADO','16154387','LIMA HUAROCHIRI SAN MATEO JIRON TACNA 255  ','06026497','URB SAN GERMAN JIRON FELIPE COHAYLA 988 SAN MARTIN DE PORRES','DESEMBOLSADO','',0,'PARTIDA DE DEFUNCION - COPIA DNI OCCISO',NULL,NULL,NULL),(4,101168,'URIBE  REBATTA CARLOS ADRIAN','Hermano(a)','2006-08-02','URIBE REBATTA ABILIO EFRAIN','Titular','2006-08-26',2700,'ENTREGADO','22256794','CALLE MANUEL PARDO 456 ICA PISCO PISCO  ','08880311','URB LA CALERA DE LA MERCED CALLE ADAM SMITH MZ E LT 5 SECT 1 SURQUILLO','DESEMBOLSADO','',0,'PARTIDA DE DEFUNCION',NULL,NULL,NULL),(5,34827,'MENDIVIL POMAJAMBO ROSA MARIA','Titular','2006-06-12','MENDIVIL TRELLES LUZMILA GLORIA','Otros','2006-09-01',2700,'ENTREGADO','10802179','GENERAL BORGOÐA 155 DPTO F MIRAFLORES','07832279','GENERAL BORGOÐA 155 DPTO F MIRAFLORES','DESEMBOLSADO','',0,'PARTIDA DE DEFUNCION',NULL,NULL,NULL),(6,48267,'PLACIDO  CASTILLO MOISES ','Suegro(a)','2006-08-20','UGAZ VASQUEZ CIRILO','Titular','2006-09-14',2700,'ENTREGADO','08456383','URB COVIDA JIRON FISCARRAL 1341 LOS OLIVOS','10295865','URB COVIDA JIRON FISCARRAL 1341 LOS OLIVOS','DESEMBOLSADO','',0,'PARTIDA DE DEFUNCION',NULL,NULL,NULL),(7,289434,'VIDAL  CARO  MODESTO ','Padre','2006-08-23','VIDAL COTRINA VICTOR ANTONIO','Titular','2006-09-01',2700,'ENTREGADO','08945147','SECTOR 2 GRUPO 4 MZ C LT 13 VILLA EL SALVADOR','08877089','SECTOR 2 GRUPO 4 MZ C LT 13 VILLA EL SALVADOR','DESEMBOLSADO','',0,'PARTIDA DE DEFUNCION',NULL,NULL,NULL),(8,142654,'FERRER SOLIS MIGUEL CARMELO','Conyuge','2006-08-20','MADRID CARVO GRACIELA MARTA','Titular','2006-09-08',2700,'ENTREGADO','43310022','JIRON YUNGAY 457 SAN MIGUEL ','07715235','JIRON YUNGAY 457 SAN MIGUEL ','DESEMBOLSADO','',0,'PARTIDA DE DEFUNCION Y MATRIMONIO',NULL,NULL,NULL),(9,16012,'ALVAREZ  LAZO  LUIS ALBERTO','Padre','2006-08-17','ALVAREZ HERRERA LUZ AMPARO JUDITH','Titular','2006-09-08',2700,'ENTREGADO','30581290','AREQUIPA AREQUIPA YANAHUARA  ','08770060','RESIDENCIAL HIGUERETA TARRAGONA 124 SANTIAGO DE SURCO','DESEMBOLSADO','',0,'PARTIDA DE DEFUNCION',NULL,NULL,NULL),(10,9946,'ARANA FRANCO DE MOCETTI DOLORES','Madre','2005-12-28','MOCETTI ARANA JULIO','Titular','2006-01-02',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(11,16322,'CORNEJO JUAREZ CARLOS ALBERTO','Conyuge','2005-11-23','ANDRADE OSTHERLO JUANA FRANCISCA','Titular','2006-01-05',2700,'ENTREGADO','','URB LA CALERA DE LA MERCED CALLE ADAM SMITH MZ E  DPTO 101SURQUILLO','','URB LA CALERA DE LA MERCED CALLE ADAM SMITH MZ E  DPTO 101SURQUILLO','DESEMBOLSADO','',0,'',NULL,NULL,NULL),(12,36846,'TERESA TRIPE VDA DE MUNOZ','Madre','2005-12-22','MUÐOZ TRIPE MATILDE GRACIELA','Titular','2006-01-05',2700,'ENTREGADO','08824320',' LA CALERA CALLE 27  MZ. U LOTE 6 - SURQUILLO','08825449',' LA CALERA CALLE 27  MZ. U LOTE 6 - SURQUILLO','DESEMBOLSADO','',0,'ACTA DE DEFUNCION',NULL,NULL,NULL),(13,279382,'VILLAGOMEZ HEREDIA ROSA','Madre','2005-12-19','GUTIERREZ VILLAGOMEZ RUBEN CLODOMIRO','Titular','2006-01-06',2700,'ENTREGADO','28804267','PASAJE SANTA TERESITA 213 - ICA -  NAZCA','29102130','PASAJE SANTA TERESITA 213 - ICA -  NAZCA','DESEMBOLSADO','',0,'PARTIDA DE NACIMIENTO - ACTA DE DEFUNCION',NULL,NULL,NULL),(14,50059,'VILCHEZ ARNAO RAFAEL ANGEL','Titular','2005-12-03','VILCHEZ ORNA FELIX ARTURO','Hijo','2006-01-09',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(15,277568,'JOFFRE BAZAN MARTHA','Madre','2005-12-30','ESPINOZA JOFFRE GLADYS PURIFICACION','Titular','2006-01-10',2700,'ENTREGADO','','','','','DESEMBOLSADO','',0,'',NULL,NULL,NULL),(16,45888,'CARMEN SEMINARIO RAMIREZ','Hermana','2005-12-26','SEMINARIO RAMIREZ JUAN','Titular','2006-01-17',2700,'ENTREGADO','07280320','PASEO DE LA REPUBLICA 707 - LA VICTORIA','07933664','PASEO DE LA REPUBLICA 707 - LA VICTORIA','DESEMBOLSADO','',0,'ACTA DE DEFUNCIËN - COPIA DNI OCCISO',NULL,NULL,NULL),(17,100137,'SARA CALLIRGOS DE TEJEDA','Madre','2005-12-30','TEJEDA CALLIRGOS LUIS ALBERTO','Titular','2006-01-19',2700,'ENTREGADO','','AV. LAS OBSIDIANAS N░ 1520 - B  - LA VICTORIA','21424636','AV. LAS OBSIDIANAS N░ 1520 - B  - LA VICTORIA','DESEMBOLSADO','',0,'ACTA DE DEFUNCION',NULL,NULL,NULL),(18,37486,'NINANCURO MENDOZA AQUILINO','Titular','2004-11-24','NINANCURO MENDOZA DOMINGO','Hermano','2006-01-27',2200,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(19,140872,'LA ROSA  LA TORRE ANGEL','Titular','2006-01-13','DIAZ IRIARTE DE LA ROSA CARMEN NOEMI','Conyuge','2006-01-27',2700,'ENTREGADO','06088071','CONJ HAB PALOMINO EDIFICIO III - 1 - 4 LIMA','06182018','CONJ HAB PALOMINO EDIFICIO III - 1 - 4 LIMA','DESEMBOLSADO','',0,'PARTIDA DE MATRIMONIO',NULL,NULL,NULL),(20,36234,'ESTREMADOYRO VALDIVIEZO EMMA','Madre','2006-01-08','MONTOYA ESTREMADOYRO HAYDEE EMMA','Titular','2006-01-27',2700,'ENTREGADO','07596499','ALFA Y OMEGA MZ- A LOTE 14 LA CALERA ','0764083','CONJ HAB. JULIO C. TELLO PLAZA PUNKURI D-201','DESEMBOLSADO','',0,'ACTA DE DEFUNCION',NULL,NULL,NULL),(21,47368,'MARIA ETELVINA GALARRETA VASQUEZ','Suegra','2006-01-15','TERRONES PEREZ VICTOR','Titular','2006-01-31',2700,'ENTREGADO','','OPERACIONES NACIONALES - OPERACIONES LOCALES ','06050251','OPERACIONES NACIONALES - OPERACIONES LOCALES ','DESEMBOLSADO','',0,'ACTA DE DEFUNCION',NULL,NULL,NULL),(22,28975,'GONZALO PACHERRES ARMANDO ALFONSO','Titular','2006-01-09','ALFARO BRACAMONTE MARIA ISABEL','Conyuge','2006-02-06',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(23,286338,'NEYRA JAIMES RAUL ERNESTO','Titular','2006-01-21','NEIRA HUERTO MAGNO','Padre','2006-02-17',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(24,38083,'MIGUEL OLIVA GOMEZ','Padre','2006-02-01','OLIVA GUERRERO NICOLAS','Titular','2006-02-17',2700,'ENTREGADO','','URB TUNGASUCA JIRON FRANCISCO TUPAC AMARU 148 CARABAYLLO','06838289','URB TUNGASUCA JIRON FRANCISCO TUPAC AMARU 148 CARABAYLLO','DESEMBOLSADO','',0,'ACTA DE DEFUNCION',NULL,NULL,NULL),(25,15369,'AGUSTI QUEVEDO OSIAS OCTAVIO','Hermano','2006-02-06','AGUSTI QUEVEDO OSCAR ABEL','Titular','2006-02-24',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(26,276375,'CAVERO VERGARA MIGUEL ANTONIO','Padre','2006-02-03','CAVERO PHUN LIDIA SUSANA','Titular','2006-03-02',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(27,121592,'NOVELLA PINCETTI PROVIDENCIA','Madre','2006-02-25','CARBONEL NOVELLA JUAN ENRIQUE','Titular','2006-03-03',2700,'ENTREGADO','','PSJE SANTA ISABEL 170 - STA MARIA ALTA-CHOSICA','','PSJE SANTA ISABEL 170 - STA MARIA ALTA-CHOSICA','DESEMBOLSADO','210815_ACT',0,'PARTIDA DE NACIMIENTO',NULL,NULL,NULL),(28,224650,'GONZALES MARADIEGUE GILBERTO','Padre','2006-02-13','GONZALES CASTILLO MANUEL ','Titular','2006-03-07',2700,'ENTREGADO','08838645','URB LA CALERA DE LA MERCED CALLE LA MORERA 463','061139410','URB LA CALERA DE LA MERCED CALLE LA MORERA 463','DESEMBOLSADO','',0,'PARTIDA DE NACIMIENTO - ACTA DE DEFUNCION',NULL,NULL,NULL),(29,40533,'JUANA MELGAR CRUZ','Suegra','2006-03-08','PISCOYA  BURGA VICTOR HUMBERTO','Titular','2006-03-14',2700,'ENTREGADO','','JIRON DOMINGO CUETO 329 LINCE','08005267','JIRON DOMINGO CUETO 329 LINCE','DESEMBOLSADO','',0,'ACTA DE DEFUNCION',NULL,NULL,NULL),(30,20915,'REYNALDO CARMONA CHUNGA','Padre','2006-03-28','CARMONA ROJAS CLARA','Titular','2006-03-14',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(31,29351,'LUCIA ORTIZ PERALTA','Madre','2006-02-27','GUIZADO ORTIZ FORTUNATO','Hermano','2006-03-20',2700,'ENTREGADO','','MARIA PARADO DE BELLIDO MZ. 8 LOTE 20 - CHORRILLOS','07037258','MARIA PARADO DE BELLIDO MZ. 8 LOTE 20 - CHORRILLOS','DESEMBOLSADO','',0,'ACTA DE DEFUNCION',NULL,NULL,NULL),(32,29394,'BOCANEGRA  REYNA  OBDULIA V','Conyuge','2006-03-13','GUTIERREZ CHONG ALFREDO','Titular','2006-03-20',2700,'ENTREGADO','06041735','AV. SEBASTIAN LORENTE BLOCK I DPTO. 309 LOS INKAS- LIMA','06041734','AV. SEBASTIAN LORENTE BLOCK I DPTO. 309 LOS INKAS- LIMA','DESEMBOLSADO','',0,'PARTIDA DE MATRIMONIO',NULL,NULL,NULL),(33,45551,'SANDOVAL VALDERRAMA ENRIQUE DEMETRIO','Titular','2006-03-09','CHAVEZ APARCANA DE SALDOVAL LIDIA','Conyuge','2006-03-27',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(34,299383,'SANCHEZ INGUNZA JUAN JOSE','Padre','2006-03-07','SANCHEZ MAURTUA TANIA ISABEL','Titular','2006-03-27',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(35,40622,'PLACIDO ZAMORA VDA. DE ESPINOZA ROSA','Fam. May - 70','2006-03-16','PLACIDO CHAVEZ VICTOR ERROL','Titular','2006-03-31',2000,'ENTREGADO','','CONJ HAB CHABUCA GRANDA JIRON ATAHUAPA 167 DPTO 2 B','08485597','CONJ HAB CHABUCA GRANDA JIRON ATAHUAPA 167 DPTO 2 B','DESEMBOLSADO','',0,'ACTA DE DEFUNCIËN',NULL,NULL,NULL),(36,40622,'VELAZCO CAMPOS GILBERTO','Suegro','2006-03-30','PLACIDO CHAVEZ VICTOR ERROL','Titular','2006-04-05',2700,'ENTREGADO','','CONJ HAB CHABUCA GRANDA JIRON ATAHUAPA 167 DPTO 2 B','08485597','CONJ HAB CHABUCA GRANDA JIRON ATAHUAPA 167 DPTO 2 B','DESEMBOLSADO','',0,'ACTA DE DEFUNCIËN',NULL,NULL,NULL),(37,264911,'NUE CARPIO EUNICE HOHANNA','Hijos',NULL,'NUE MOSCOL JAVIER ALEJANDRO','Titular','2006-04-04',2700,'ENTREGADO','43582736','JR. MARTIN PIO CONCHA F - 13 QUILLIBAMBA CUZCO ','06686032','JR. MARTIN PIO CONCHA F - 13 QUILLIBAMBA CUZCO ','DESEMBOLSADO','',0,'PARTIDA DE NACIMIENTO - ACTA DE DEFUNCION',NULL,NULL,NULL),(38,254428,'TIBURCIO ARGANDONA VICENTE','Padre','2006-03-17','TIBURCIO ORBEZO ROSA MARIELA','Titular','2006-04-04',2700,'ENTREGADO','','URB HORIZONTE DE ZARATE JIRON LAS GAVIOTAS MZ K  LT6 ','22652353','URB HORIZONTE DE ZARATE JIRON LAS GAVIOTAS MZ K  LT6 ','DESEMBOLSADO','',0,'ACTA DE DEFUNCIËN',NULL,NULL,NULL),(39,295701,'HINOJOSA JUAREZ ADOLFO','Fam. May - 70','2006-03-10','HERNANDEZ HINOJOSA GIANCARLO MARCOS','Titular','2006-04-11',2700,'ENTREGADO','08508354','URB ZARUMILLA  JIRON RAFAEL CACERES 892 - SMP','10621439','URB ZARUMILLA  JIRON RAFAEL CACERES 892 - SMP','DESEMBOLSADO','',0,'PARTIDA DE NACIMIENTO - ACTA DE DEFUINCION',NULL,NULL,NULL),(40,49638,'JUAN VENTO CASTRO','Padre','2006-02-22','VENTO FRIAS JUAN JOSE','Titular','2006-04-11',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(41,288918,'ARIAS MENDOZA PABLO','Padre','2006-04-09','ARIAS BARRENECHEA PABLO MARTIN','Titular','2006-04-12',2700,'ENTREGADO','09452433','CONJ. RESIDENCIAL VILLA TALANA - AV LA MERCED 1089 LL - 302 SANTIAGO DE SURCO ','09452433','CONJ. RESIDENCIAL VILLA TALANA - AV LA MERCED 1089 LL - 302 SANTIAGO DE SURCO','DESEMBOLSADO','',0,'ACTA DE DEFUNCION ',NULL,NULL,NULL),(42,280461,'CALDERON CALDERON ADILIA','Madre','2006-04-02','LLICAN CALDERON HENRY ROMULO','Titular','2006-04-17',2700,'ENTREGADO','06089651','URB  LOS JARDINES  JIRON LAS ZARZAMORAS  1109  SMP','06000578','URB  LOS JARDINES  JIRON LAS ZARZAMORAS  1109 SMP ','DESEMBOLSADO','',0,'PARTIDA DE NACIMIENTO - ACTA DE DEFUNCION',NULL,NULL,NULL),(43,220981,'ROSA ANGELICA MALDONADO GUZMAN DE RUIZ','Madre','2006-04-05','RUIZ MALDONADO ESTEFA MARIA','Titular','2006-04-17',2700,'ENTREGADO','','AVENIDA FRAY LUIS DE LEON 289 SAN BORJA','06367983','AVENIDA FRAY LUIS DE LEON 289 SAN BORJA','DESEMBOLSADO','',0,'ACTA DE DEFUNCION ',NULL,NULL,NULL),(44,49506,'EMILIA SAYAS GASTELU','Suegra','2006-04-07','VELEZ AYALA LUCIO MARCOS','Titular','2006-04-26',2700,'ENTREGADO','','URB SOL DE VITARTE MZ E LT 2 SECTOR G ATE VITARTE','06083547','URB SOL DE VITARTE MZ E LT 2 SECTOR G ATE VITARTE','DESEMBOLSADO','',0,'ACTA DE DEFUNCION',NULL,NULL,NULL),(45,79472,'MAXIMINA MAGUINA VDA DE DEXTRE','Madre','2006-04-02','DEXTRE MAGUIÐA EFRAIN ENRIQUE','Titular','2006-04-26',2700,'ENTREGADO','','CALLE MARIANO MELGAR 453 INDEPENDENCIA HUARAZ','31617830','CALLE MARIANO MELGAR 453 INDEPENDENCIA HUARAZ','DESEMBOLSADO','',0,'PARTIDA DE DEFUNCION',NULL,NULL,NULL),(46,96113,'PARRALES SOTOMAYOR ELIZABETH','Otros','2006-03-28','RODRIGUEZ MONTERROSO SONIA IRENE','Titular','2006-04-26',2700,'ENTREGADO','02606991','AA HH MARIA GORETI CASTILLA  MZ L LT 7 PIURA  ','09851293','CONJ HAB JULIO C TELLO PLAZA PUNKURI BLOCK D DPTO 103 SAN MIGUEL','DESEMBOLSADO','',0,'BENEFICIO PAGADO EN CALIDAD DE AEE',NULL,NULL,NULL),(47,40894,'ANTONIO PORTAL BUENO','Padre','2006-04-19','PORTAL DURAND HIPOLITO','Titular','2006-04-28',2700,'ENTREGADO','27043061','CASERIO TERESA CONGA CAJAMARCA CELENDIN SUCRE','27081308','URB PUERTA DE PRO CALLE 10 MZ M4 LT 28 LOS OLIVOS','DESEMBOLSADO','',0,'PARTIDA DE NACIMIENTO SOCIO TITULAR',NULL,NULL,NULL),(48,46159,'MANCHEGO  CHIRINOS  BLANCA RUTH','Conyuge','2006-04-14','SILVA MARIN CLEMENTE NAPOLEON','Titular','2006-04-28',2700,'ENTREGADO','06598886','LAS TRINITARIAS 115 - SALAMANCA - ATE','06598117','LAS TRINITARIAS 115 - SALAMANCA - ATE','DESEMBOLSADO','',0,'ACTA DE DEFUNCIËN - COPIA DNI OCCISO',NULL,NULL,NULL),(49,41769,'GRACIELA RUIZ VEGA','Madre','2006-04-30','RAEZ RUIZ CESAR','Titular','2006-05-12',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL),(50,1001390,'SALDARRIAGA CRUZ SALUSTIANO','Padre','2006-04-26','SALDARRIAGA VELEZ JANET DEL PILAR','Titular','2006-05-12',2700,'ENTREGADO',NULL,NULL,NULL,NULL,'DESEMBOLSADO',NULL,0,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f21_beneficios` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f22_aportes`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f22_aportes` DISABLE KEYS */;
INSERT INTO `f22_aportes` VALUES (10790,'2010-09-01',10,10,0,'2010-08-31',10,24516),(15032,'2010-09-01',20,20,0,'2010-08-31',20,37877),(15059,'2010-09-01',30,30,0,'2010-08-31',30,11783),(15245,'2010-09-01',30,30,0,'2010-08-31',30,20626),(15318,'2010-09-01',40,40,0,'2010-08-31',40,67668),(15342,'2010-09-01',10,10,0,'2010-08-31',10,46468),(15369,'2010-09-01',60,60,0,'2010-08-31',60,29069),(15393,'2010-09-01',50,50,0,'2010-08-31',50,5704),(15458,'2010-09-01',53,53,0,'2010-08-31',53,18222),(15466,'2010-09-01',30,30,0,'2010-08-31',30,73965),(15504,'2010-09-01',10,10,0,'2010-08-31',10,5825),(15547,'2010-09-01',10,10,0,'2010-08-31',10,39354),(15598,'2010-09-01',10,10,0,'2010-08-31',10,23854),(15601,'2010-09-01',10,10,0,'2010-08-31',10,822),(15687,'2010-09-01',10,10,0,'2010-08-31',10,10598),(15725,'2010-09-01',30,30,0,'2010-08-31',30,50634),(15776,'2010-09-01',20,20,0,'2010-08-31',20,64255),(15784,'2010-09-01',20,20,0,'2010-08-31',20,11127),(15792,'2010-09-01',10,10,0,'2010-08-31',10,20623),(15814,'2010-09-01',10,10,0,'2010-08-31',10,69815),(15873,'2010-09-01',40,40,0,'2010-08-31',40,50098),(15954,'2010-09-01',10,10,0,'2010-08-31',10,40471),(15962,'2010-09-01',10,10,0,'2010-08-31',10,51317),(16012,'2010-09-01',20,20,0,'2010-08-31',20,55478),(16020,'2010-09-01',10,10,0,'2010-08-31',10,42917),(16039,'2010-09-01',25,25,0,'2010-08-31',25,48128),(16071,'2010-09-01',10,10,0,'2010-08-31',10,31602),(16098,'2010-09-01',25,25,0,'2010-08-31',25,13360),(16179,'2010-09-01',10,10,0,'2010-08-31',10,52588),(16292,'2010-09-01',40,40,0,'2010-08-31',40,61296),(16322,'2010-09-01',50,50,0,'2010-08-31',50,66277),(16365,'2010-09-01',10,10,0,'2010-08-31',10,65721),(16438,'2010-09-01',40,40,0,'2010-08-31',40,47635),(16470,'2010-09-01',20,20,0,'2010-08-31',20,40214),(16489,'2010-09-01',10,10,0,'2010-08-31',10,57955),(16497,'2010-09-01',40,40,0,'2010-08-31',40,4103),(16527,'2010-09-01',10,10,0,'2010-08-31',10,11559),(16578,'2010-09-01',73,73,0,'2010-08-31',73,45604),(16624,'2010-09-01',10,10,0,'2010-08-31',10,27534),(16675,'2010-09-01',20,20,0,'2010-08-31',20,479),(16683,'2010-09-01',10,10,0,'2010-08-31',10,2917),(16705,'2010-09-01',10,10,0,'2010-08-31',10,13166),(16748,'2010-09-01',20,20,0,'2010-08-31',20,57215),(16772,'2010-09-01',10,10,0,'2010-08-31',10,79107),(16799,'2010-09-01',40,40,0,'2010-08-31',40,56014),(17019,'2010-09-01',20,20,0,'2010-08-31',20,42925),(17043,'2010-09-01',10,10,0,'2010-08-31',10,44444),(17086,'2010-09-01',10,10,0,'2010-08-31',10,8069),(17256,'2010-09-01',10,10,0,'2010-08-31',10,78308),(17264,'2010-09-01',20,20,0,'2010-08-31',20,21984);
/*!40000 ALTER TABLE `f22_aportes` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f23_cheque`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f23_cheque` DISABLE KEYS */;
INSERT INTO `f23_cheque` VALUES (1,'JOSE PABLO ALBUJAR NUÐEZ','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(2,'JUAN PEREZ','3000','TRES MIL  00/100',NULL,NULL,NULL,NULL,NULL),(3,'JOSE PABLO ALBUJAR NUÐEZ','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(4,'JOSE PEREZ','15000','QUINCE MIL  00/100',NULL,NULL,NULL,NULL,NULL),(5,'VICTOR ALCEDO','3000','TRES MIL  00/100',NULL,NULL,NULL,NULL,NULL),(6,'JUAN CASTRO VEGA','1200','MIL DOSCIENTOS  00/100',NULL,NULL,NULL,NULL,NULL),(7,'JUAN PEREZ SALAZAR','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(8,'JUAN SALAZAR PEREZ','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(9,'JUAN PEREZ SALAZAR','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(10,'JOSE PABLO ALBUJAR NUÐEZ','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(11,'JUAN PEREZ','1200','MIL DOSCIENTOS  00/100',NULL,NULL,NULL,NULL,NULL),(12,'DSADAS','3000','TRES MIL  00/100',NULL,NULL,NULL,NULL,NULL),(13,'PABLO ALBUJAR','3000','TRES MIL  00/100',NULL,NULL,NULL,NULL,NULL),(14,'JUAN PEREZ','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(15,'FRDSF','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(16,'FFFFFFFFFFFFFFFFFFFFFFFF','3000','TRES MIL  00/100',NULL,NULL,NULL,NULL,NULL),(17,'DSAADFSA','3000','TRES MIL  00/100',NULL,NULL,NULL,NULL,NULL),(18,'FDSfd','3000','TRES MIL  00/100',NULL,NULL,NULL,NULL,NULL),(19,'FDSFDS','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(20,'FVCXVX','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(21,'FDSFSD','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(22,'FVDfds','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(23,'FDSFDS','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(24,'DFSAD','2000','DOS MIL  00/100',NULL,NULL,NULL,NULL,NULL),(25,'DSADSA','4500.00','CUATRO MIL QUINIENTOS  00/100',NULL,NULL,NULL,NULL,NULL),(26,'ZORRILLA ALMORA DE BERAMENDI MARCELINA','3500.00','TRES MIL QUINIENTOS  00/100',NULL,NULL,NULL,NULL,NULL),(27,'RIVERA MARTINEZ JUAN RODOLFO','1000','MIL  00/100',NULL,NULL,NULL,NULL,NULL),(28,'FLORES FLORES JOSE FRANCISCO','1200','MIL DOSCIENTOS  00/100',NULL,NULL,NULL,NULL,NULL),(29,'FLORES FLORES JOSE FRANCISCO','1200.00','MIL DOSCIENTOS  00/100',NULL,NULL,NULL,NULL,NULL),(30,'FLORES FLORES JOSE FRANCISCO','778','SETENTA Y SIETE 00/100',NULL,NULL,NULL,NULL,NULL),(31,'FLORES FLORES JOSE FRANCISCO','200','VEINTE  00/100',NULL,NULL,NULL,NULL,NULL),(32,'COVEÐAS FARIA EDUARDO CIPRIANO','680.00','SESENTA Y OCHO 00/100',NULL,NULL,NULL,NULL,NULL),(33,'COVENAS FARIAS EDUARDO CIPRIANO','680','SESENTA Y OCHO 00/100',NULL,NULL,NULL,NULL,NULL),(34,'FDSFD','580','CINCUENTA Y OCHO 00/100',NULL,NULL,NULL,NULL,NULL),(35,'1231','280','VEINTIOCHO 00/100',NULL,NULL,NULL,NULL,NULL),(36,'HGH','58','CINCO 00/100',NULL,NULL,NULL,NULL,NULL),(37,'SOLIS','490','CUARENTA Y NUEVE 00/100',NULL,NULL,NULL,NULL,NULL),(38,'SGD','680','SESENTA Y OCHO 00/100',NULL,NULL,NULL,NULL,NULL),(39,'DAFF','3500','TRES MIL QUINIENTOS  00/100',NULL,NULL,NULL,NULL,NULL),(40,'PABLO','680','SEISCIENTOS OCHENTA  00/100','2010-05-31',NULL,NULL,NULL,NULL),(41,'RIVERA MARTINEZ JUAN RODOLFO','2000','DOS MIL  00/100','2010-05-31',NULL,NULL,NULL,NULL),(42,'MILAGROS','531.50','QUINIENTOS TREINTA Y UNO 50/100','2010-05-31',NULL,NULL,NULL,NULL),(43,'MILAGROS','531.50','QUINIENTOS TREINTA Y UNO 50/100','2010-05-31',NULL,NULL,NULL,NULL),(44,'FEITOZA ALTAMIRANO JULIO CESAR','1960','MIL NOVECIENTOS SESENTA  00/100','2010-05-31',NULL,NULL,NULL,NULL),(45,'GFDG','2000','DOS MIL  00/100','2010-06-01','2132213',NULL,NULL,NULL),(46,'GFDG','2000','DOS MIL  00/100','2010-06-01','1234',NULL,NULL,NULL),(47,'RIVERA MARTINEZ JUAN RODOLFO','1960','MIL NOVECIENTOS SESENTA  00/100','2010-06-01','',NULL,NULL,NULL),(48,'RIVERA MARTINEZ JUAN RODOLFO','1960','MIL NOVECIENTOS SESENTA  00/100','2010-06-01','',NULL,NULL,NULL),(49,'GFHGJHVGJNBV','480','CUATROCIENTOS OCHENTA  00/100','2010-06-01','123456789',NULL,NULL,NULL),(50,'GHSFHSFGHFH','251.35','DOSCIENTOS CINCUENTA Y UNO 35/100','2010-06-01','25124521',NULL,NULL,NULL);
/*!40000 ALTER TABLE `f23_cheque` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f24_descuentos`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f24_descuentos` DISABLE KEYS */;
INSERT INTO `f24_descuentos` VALUES (15687000,'2013-01-01',NULL,250,50,50,0,0,0,0,0,10,10,0,317,317,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15733000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16292010,'2013-01-01',NULL,0,0,0,0,0,0,0,0,40,40,0,47,47,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17779000,'2013-01-01',NULL,41.66,10,10,0,0,0,0,0,10,10,0,68.66,68.66,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18619016,'2013-01-01',NULL,0,0,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21210000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22764012,'2013-01-01',NULL,0,0,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27618000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,40,40,0,47,47,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28878000,'2013-01-01',NULL,50,18,18,0,0,0,0,0,20,20,0,95,95,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35653000,'2013-01-01',NULL,20.5,11.2,11.2,0,5,5,0,0,25,25,0,440.68,68.7,371.98,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37826000,'2013-01-01',NULL,62.5,30,30,0,0,0,0,0,10,10,0,109.5,109.5,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37842010,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38261010,'2013-01-01',NULL,100,20,20,0,0,0,0,0,10,10,0,137,137,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38512013,'2013-01-01',NULL,58.33,10.5,10.5,0,0,0,0,0,35,35,0,110.83,110.83,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(40851011,'2013-01-01',NULL,0,0,0,0,0,0,0,0,55,55,0,62,62,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(43044000,'2013-01-01',NULL,61.27,63,63,0,10,10,0,0,10,10,0,3485.24,151.27,3333.97,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48410000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(51225015,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(73695000,'2013-01-01',NULL,55.68,50,50,0,10,10,0,0,10,10,0,471.98,132.68,339.3,0,1979.15,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(74942016,'2013-01-01',NULL,0,0,0,0,0,0,0,0,40,40,0,47,47,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(75213000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(85170000,'2013-01-01',NULL,58.6,40,40,0,10,10,0,0,10,10,0,224.53,125.6,98.93,0,1666.67,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(85774000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(89281000,'2013-01-01',NULL,20.48,20,20,0,5,5,0,0,25,25,0,203.6,77.48,126.12,0,833.32,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(91804018,'2013-01-01',NULL,0,0,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(92339000,'2013-01-01',NULL,83.34,28,28,0,0,0,0,0,10,10,0,128.34,128.34,0,0,750.01,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(99821000,'2013-01-01',NULL,16.75,30,30,0,10,10,0,0,25,25,0,325.5,88.75,236.75,0,1250,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(138584000,'2013-01-01',NULL,81.03,40,40,0,0,0,0,0,10,10,0,140.33,138.03,2.3,0,2000,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(140015016,'2013-01-01',NULL,33.33,7.5,7.5,0,0,0,0,0,10,10,0,57.83,57.83,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(142824000,'2013-01-01',NULL,97.33,37.5,37.5,0,10,10,0,0,10,10,0,215.47,161.83,53.64,0,2187.49,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100013000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100110000,'2013-01-01',NULL,99.59,20,20,0,0,0,0,0,10,10,0,137,136.59,0.41,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100137000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,30,30,0,37,37,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100153000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100218011,'2013-01-01',NULL,100,24,24,0,0,0,0,0,60,60,0,191,191,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100242000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,30,30,0,37,37,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100315000,'2013-01-01',NULL,125,60,60,0,0,0,0,0,10,10,0,202,202,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100331000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100447000,'2013-01-01',NULL,23.29,14,14,0,5,5,0,0,20,20,0,196.97,69.29,127.68,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100498000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100692000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100854000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(100943000,'2013-01-01',NULL,62.09,20,20,0,5,5,0,0,10,10,0,521.32,104.09,417.23,0,100,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(101168000,'2013-01-01',NULL,166.66,30,30,0,0,0,0,0,90,90,0,293.66,293.66,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(101443019,'2013-01-01',NULL,83.33,15,15,0,0,0,0,0,10,10,0,115.33,115.33,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(101451000,'2013-01-01',NULL,166.66,80,80,0,0,0,0,0,20,20,0,273.66,273.66,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(101540000,'2013-01-01',NULL,125,42,42,0,0,0,0,0,30,30,0,204,204,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(101613000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(101672000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(101680000,'2013-01-01',NULL,0,0,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'PENSIONISTA',1,7,7,0,NULL,NULL,NULL,NULL,NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f24_descuentos` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f24_descuentos_temp`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f24_descuentos_temp` DISABLE KEYS */;
INSERT INTO `f24_descuentos_temp` VALUES (9407,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,40,0,40,47,0,47,0,0,'ACTIVO',1,7,0,7,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(20214,'2019-10-01',NULL,32.61,108.089996337891,108.09,0,0,0,0,0,45,45,0,280.19,192.7,87.49,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(22128,'2019-10-01',NULL,255.82,80,80,0,0,0,0,0,50,50,0,470.33,392.82,77.51,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(28908,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(37435,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(48445,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,40,40,0,47,47,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(50865,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,30,30,0,37,37,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(104779,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(105910,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(106259,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(106291,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(106445,'2019-10-01',NULL,0,472.25,55.44,416.81,10,10,0,0,10,10,0,3000,215.27,2784.73,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,23.8500003814697,23.85,0,10,10,0,'4',0,20.5799999237061,20.58,0,10,10,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,53.4000015258789,53.4,0,15,15,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(107042,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,30,30,0,37,37,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(107077,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,35,35,0,42,42,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(107654,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(107719,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(120480,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,50,50,0,57,57,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(120634,'2019-10-01',NULL,138.89,50,50,0,0,0,0,0,30,30,0,225.89,225.89,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(120855,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,25,25,0,32,32,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(120871,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,80,80,0,87,87,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(120898,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(121355,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,65,65,0,72,72,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(121401,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,35,35,0,42,42,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(121444,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,30,30,0,37,37,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(121754,'2019-10-01',NULL,349.74,105,105,0,10,10,0,0,40,40,0,1289.98,511.74,778.24,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(121983,'2019-10-01',NULL,83.33,11,11,0,0,0,0,0,10,10,0,111.33,111.33,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(122173,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,45,45,0,52,52,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(122645,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(122785,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,113.13,113.13,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',72.88,23.25,23.25,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(122963,'2019-10-01',NULL,200,13,13,0,0,0,0,0,20,20,0,240,240,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(123048,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(123072,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,30,30,0,37,37,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(123080,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(123307,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,30,30,0,37,37,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(123439,'2019-10-01',NULL,63.27,55,55,0,0,0,0,0,20,20,0,145.27,145.27,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(123811,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(124605,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(124761,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(124842,'2019-10-01',NULL,208.33,32.5,32.5,0,0,0,0,0,10,10,0,257.83,257.83,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(125334,'2019-10-01',NULL,308.09,55,55,0,5,5,0,0,20,20,0,1326.72,395.09,931.63,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(125482,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(132446,'2019-10-01',NULL,37.3,110,110,0,10,10,0,0,70,70,0,1588.51,234.3,1354.21,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(132462,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,20,20,0,27,27,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(132675,'2019-10-01',NULL,138.89,30,30,0,0,0,0,0,65,65,0,240.89,240.89,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(132799,'2019-10-01',NULL,275.42,NULL,0,0,0,0,0,0,20,20,0,2566.89,302.42,2264.47,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(132942,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,55,55,0,415.24,415.24,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',326.77,26.4699993133545,26.47,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(132985,'2019-10-01',NULL,155.11,24,24,0,0,0,0,0,20,20,0,211,206.11,4.89,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(133000,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,10,10,0,17,17,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(133418,'2019-10-01',NULL,488.23,200,200,0,5,5,0,0,40,40,0,740.23,740.23,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0),(134015,'2019-10-01',NULL,0,NULL,0,0,0,0,0,0,50,50,0,57,57,0,0,0,'ACTIVO',1,7,7,0,0,0,0,0,0,0,'1',0,0,0,0,'7',0,NULL,0,0,'9',0,NULL,0,0,'6',NULL,0,0,NULL,0,0,NULL,0,0,'3',0,NULL,0,0,NULL,0,0,'4',0,NULL,0,0,NULL,0,0,'N',NULL,NULL,NULL,'2',0,NULL,0,0,NULL,0,0,'8',0,NULL,0,0,NULL,0,0,'10',0,NULL,0,0,NULL,0,0,NULL,0,NULL,0,0,NULL,0,0);
/*!40000 ALTER TABLE `f24_descuentos_temp` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f25_acciones`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f25_acciones` DISABLE KEYS */;
INSERT INTO `f25_acciones` VALUES (1,'Nuevo S','javascript:nuevo();',1),(2,'Modificar S','javascript:modificar();',1),(3,'Baja S','javascript:eliminar();',1),(4,'Eliminar S','borrar();',1),(5,'Habilitar S','activar();',1),(6,'Familiares','javascript:familiar();',1),(7,'Ficha','openwin9(\'\',\'\',\'\');',1),(8,'Estado A.E.E.','prestamo();',1),(9,'Descuentos','aportaciones();',1),(10,'Aporte FONBES','aportaciones2();',1),(11,'Nuevo P','javascript:nuevo();',2),(12,'Aprobar P','javascript:aprobar();',2),(13,'Cronograma','javascript:verCronograma();',2),(14,'Calificacion','javascript:calificacion();',2),(15,'Modificar P','javascript:modificar();',2),(16,'Cancelar P','javascript:cancelar();',2),(17,'Eliminar P','javascript:eliminar();',2),(18,'Resumen','javascript:resumen();',2),(19,'Habilitar P','javascript:habilitar();',2),(20,'Reprogramar','javascript:rPrestamo();',2),(21,'Anular P','javascript:anularPrestamo();',2),(22,'Ver Comprobante','javascript:openwin(\'\',\'\',\'\');',3),(23,'Modificar Mov.','modificar();',3),(24,'Anular Mov.','eliminar();',3),(25,'Nuevo F.','javascript:nuevo();',4),(26,'Modificar F.','javascript:modificar();',4),(27,'Baja F.','javascript:eliminar();',4),(28,'Eliminar F.','javascript:borrar();',4),(29,'Activar F.','javascript:activar();',4),(30,'Exportar F.','javascript:exportar();',4),(31,'Aporte COOPAM','aportacionesCope();',1),(32,'Ap.Anual FONBES','aportacionesCopeA();',1),(33,'Obs','actualizaObs();',1),(34,'Carta Descto.','actualizaCarta();',1),(35,'Hacer Socio','javascript:socio();',4),(36,'Ap.Anual COOPAM','aportacionesCopeA2();',1),(37,'Ocultar','visible();',1),(38,'Habilitar','javascript:eliminar();',5),(39,'Mostrar','javascript:buscar();',5);
/*!40000 ALTER TABLE `f25_acciones` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f26_perfil_accion`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f26_perfil_accion` DISABLE KEYS */;
INSERT INTO `f26_perfil_accion` VALUES (1,10),(1,11),(1,12),(1,13),(1,14),(1,15),(1,16),(1,17),(1,18),(1,19),(1,20),(1,21),(1,22),(1,23),(1,24),(1,25),(1,26),(1,27),(1,28),(1,29),(1,30),(1,31),(1,33),(1,34),(1,39),(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,10),(2,11),(2,12),(2,13),(2,14),(2,15),(2,16),(2,17),(2,18),(2,19),(2,20),(2,21),(2,22),(2,23),(2,24),(2,25);
/*!40000 ALTER TABLE `f26_perfil_accion` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f27_aportes_cope`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f27_aportes_cope` DISABLE KEYS */;
INSERT INTO `f27_aportes_cope` VALUES (4132186,'2010-01-01',5,5,0,'2010-12-20',5,7608830),(4132186,'2010-02-01',5,5,0,'2010-12-20',5,578436),(4132186,'2010-03-01',5,5,0,'2010-12-20',5,726623),(4132186,'2010-04-01',5,5,0,'2010-12-20',5,1897805),(4132186,'2010-05-01',5,5,0,'2010-12-20',5,7309159),(4132186,'2010-06-01',5,5,0,'2010-12-20',5,10191452),(4132186,'2010-01-01',5,5,0,'2010-12-20',5,8368852),(4132186,'2010-02-01',5,5,0,'2010-12-20',5,11269904),(4132186,'2010-03-01',5,5,0,'2010-12-20',5,10582036),(4132186,'2010-04-01',5,5,0,'2010-12-20',5,19100470),(4132186,'2010-05-01',5,5,0,'2010-12-20',5,1773454),(4132186,'2010-06-01',5,5,0,'2010-12-20',5,13548651),(303020,'2010-01-01',5,5,0,'2010-12-21',5,32273),(303020,'2010-01-01',5,5,0,'2010-12-22',5,210932),(303020,'2010-02-01',5,5,0,'2010-12-22',5,957843),(303020,'2010-03-01',5,5,0,'2010-12-22',5,1126220),(303020,'2010-04-01',5,5,0,'2010-12-22',5,1242471),(303020,'2010-05-01',5,5,0,'2010-12-22',5,1318594),(303020,'2010-01-01',5,5,0,'2010-12-23',5,1350457),(303020,'2010-03-01',5,5,0,'2010-12-23',5,1281404),(303020,'2010-04-01',5,5,0,'2010-12-23',5,840548),(303020,'2010-05-01',5,5,0,'2010-12-23',5,358531),(303020,'2010-06-01',5,5,0,'2010-12-23',5,786109),(303020,'2010-01-01',5,5,0,'2010-12-23',5,1339852),(303020,'2010-02-01',5,5,0,'2010-12-23',5,1310736),(303020,'2010-03-01',5,5,0,'2010-12-23',5,1019022),(303020,'2010-04-01',5,5,0,'2010-12-23',5,1162904),(303020,'2010-01-01',5,5,0,'2010-12-23',5,1242353),(303020,'2010-02-01',5,5,0,'2010-12-23',5,1207954),(303020,'2010-03-01',5,5,0,'2010-12-23',5,797611),(303020,'2010-04-01',5,5,0,'2010-12-23',5,364192),(303020,'2010-01-01',5,5,0,'2010-12-27',5,943230),(303020,'2010-02-01',5,5,0,'2010-12-27',5,593372),(303020,'2010-03-01',5,5,0,'2010-12-27',5,137172),(303020,'2010-04-01',5,5,0,'2010-12-27',5,420844),(303020,'2010-01-01',5,5,0,'2010-12-27',5,177603),(303020,'2010-02-01',5,5,0,'2010-12-27',5,1140585),(303020,'2010-03-01',5,5,0,'2010-12-27',5,624817),(303020,'2010-04-01',5,5,0,'2010-12-27',5,1217432),(303020,'2010-01-01',5,5,0,'2010-12-27',5,1182507),(303020,'2010-02-01',5,5,0,'2010-12-27',5,745142),(303020,'2010-03-01',5,5,0,'2010-12-27',5,178189),(303020,'2011-01-01',5,5,0,'2011-01-10',5,170617),(303020,'2011-02-01',5,5,0,'2011-01-10',5,318521),(303020,'2011-03-01',5,5,0,'2011-01-10',5,1080753),(303020,'2011-04-01',5,5,0,'2011-01-10',5,1418005),(303020,'2011-05-01',5,5,0,'2011-01-10',5,817566),(303020,'2011-06-01',5,5,0,'2011-01-10',5,1348913),(303020,'2011-07-01',5,5,0,'2011-01-10',5,1261670),(303020,'2011-01-01',6,6,0,'2011-03-12',6,746511);
/*!40000 ALTER TABLE `f27_aportes_cope` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f28_obs_detalle`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f28_obs_detalle` DISABLE KEYS */;
INSERT INTO `f28_obs_detalle` VALUES (20613,'PRUEBA','SOCIO INVENTADO','2011-01-12 12:54:43',NULL),(20613,'PRUEBA 2','SOCIO INVENTADO 2','2011-01-12 12:55:16',NULL),(20613,'PRUEBA 3','SOCIO INVENTADO 3','2011-01-12 17:58:14',NULL),(20613,'SANCIONADO','SE PORTO MAL','2011-01-12 18:00:48',NULL),(557,'NO AFILIAR','POR EMITIR COMUNICADO EN CONTRA DE LA INSTITUCION','2011-01-14 09:15:42',NULL),(719,'NO AFILIAR','POR EMITIR COMUNICADO EN CONTRA DE LA INSTITUCION','2011-01-14 09:17:54',NULL),(3446,'NO AFILIAR','POR EMITIR COMUNICADO EN CONTRA DE LA INSTITUCION','2011-01-14 09:23:50',NULL),(3675,'NO AFILIAR','POR EMITIR COMUNICADO EN CONTRA DE LA INSTITUCION','2011-01-14 09:25:53',NULL),(3675,'NO AFILIAR','POR EMITIR COMUNICADO EN CONTRA DE LA INSTITUCION','2011-01-14 09:31:53',NULL),(2179,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 09:47:29',NULL),(1849,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 09:58:05',NULL),(1662,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 09:58:54',NULL),(1579,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 09:59:21',NULL),(2971,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 09:59:42',NULL),(2871,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:00:03',NULL),(2506,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:01:11',NULL),(2805,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:01:32',NULL),(1986,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:02:00',NULL),(1706,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:02:25',NULL),(2290,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:02:49',NULL),(1945,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:03:11',NULL),(1730,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:03:48',NULL),(1527,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:04:07',NULL),(2391,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:04:28',NULL),(1664,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:04:47',NULL),(1486,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:05:08',NULL),(2389,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:05:29',NULL),(2894,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:05:54',NULL),(1875,'NO ENVIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:06:59',NULL),(1295,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:07:16',NULL),(1821,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:07:32',NULL),(2265,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:07:50',NULL),(2929,'NO AFILIAR','SEGUN CARTA DEL 04-01-11,SI SE RECTIFICAN CON CARTA Y JUSTIFICANDO SU FIRMA EN DICHA CARTA MANIFESTANDO QUIEN LE HIZO FIRMAR','2011-01-14 10:08:17',NULL),(2225,'NO AFILIAR','SEGUN INFORME DEL SEÐOR ARRUNATEGUI','2011-01-14 10:30:05',NULL),(870,'NO AFILIAR','Por renunciar 2 veces a la Institucion','2011-01-20 17:54:13',NULL),(20613,'PRUEBA','CODIGO PARA REALIZAR PRUEBAS','2011-02-02 10:27:30',NULL),(2533,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-02-17 16:34:40',NULL),(1508,'SIN DESCUENTO','','2011-02-17 16:36:49',NULL),(2269,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-02-17 16:37:49',NULL),(953,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-02-17 16:38:06',NULL),(2928,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-02-17 16:38:27',NULL),(1949,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-02-17 16:38:42',NULL),(1286,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-02-17 16:39:01',NULL),(2244,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-02-17 16:39:23',NULL),(2278,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-02-17 16:57:46',NULL),(1865,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-02-17 16:58:08',NULL),(3896,'NO AFILIAR','NO AFILIARA PORQUE PRESENTO CARTA AL BANCO DE LA NACION INDICANDO QUE NO DESEA SER SOCIO.','2011-02-17 16:59:31',NULL),(953,'SIN DESCUENTO','TIENE AEE PENDIENTE','2011-03-04 16:26:13',NULL),(1508,'SIN DESCUENTO','TIENE AEE PENDENTE','2011-03-07 09:47:48',NULL),(904,'SUPENDIDO','envio de carta de fecha 02/02/2011','2011-03-08 15:33:42',NULL);
/*!40000 ALTER TABLE `f28_obs_detalle` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:22
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f29_tramite`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f29_tramite` DISABLE KEYS */;
INSERT INTO `f29_tramite` VALUES (1,'001-11','1','Cronograma de Elecciones del Comite Electoral','NO','CADMINISTRACION',NULL,'2011-01-10','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'002-11','1','Socio solicita refinanciacion y reconsideraci¾n como socio','NO','CADMINISTRACION',NULL,'2011-01-17','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'002-2011','1','prueba','SI','CONTABILIDAD','CADMINISTRACION','2011-05-13','','ELIMINADO',NULL,'','prueba 1','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'001-11','1','Comite Electoral\r\nCronograma Elecciones','SI','CADMINISTRACION','BEATRIZ','2011-01-10','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'002-11','1','Carta de Sr.Bartra\r\nAsunto: Aportes del Pensionista Hector Malca','SI','ADMINISTRACION','BEATRIZ','2011-01-14','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'003-11','1','Carta Sr.Ramon Nolasco\r\n\r\nSolicita rebaja de interes por A.E.E','SI','ADMINISTRACION','BEATRIZ','2011-01-17','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'','1','Carta Sr,Bartra \r\nProceden al descuento de Pensionistas.','SI','ADMINISTRACION','BEATRIZ','2011-01-17','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'','1','Carta Sr. Roberto Bravo\r\nSolicita ampliaci¾n de cuota de A.E.E','SI','ADMINISTRACION','BEATRIZ','2011-01-18','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'006-11','1','Sra. Consuelo Perez\r\nSolicita pque a partir del mes de Marzo pagar cuotas de A.E.E','SI','ADMINISTRACION','BEATRIZ','2011-01-18','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'007-11','1','Sra. Maria Elene Cahuas\r\nInforma que ella no podra pagar deuda de garantizado\r\ny que se le realice descuentos al conyuge','SI','ADMINISTRACION','BEATRIZ','2011-01-19','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'008-11','1','Carta de Medicos Santa Maria\r\nOfrecen servicios medicos que pueden ser ofrecidos a los socios','SI','ADMINISTRACION','BEATRIZ','2011-01-20','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'009-11','1','Carta Sr. Cesar Milla Jurado\r\nSoicita certificado de retencion','SI','ADMINISTRACION','BEATRIZ','2011-01-24','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'0010-11','1','Carta Sr. Jose R. Francesqui Enriquez\r\nSolicita renuncia a la Instituci¾n\r\n','SI','SECRETARIA','BEATRIZ','2011-01-31','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'0011-11','1','Sr. Manuel Yactayo Urrutia\r\nDejo de aportar y solicita reconsideraci¾n en su inscripci¾n','SI','ADMINISTRACION','BEATRIZ','2011-02-03','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'0012-11','1','Sr. Mario A. Rivera Cordova\r\nSolicita reconsideraci¾n en inscripci¾n','SI','ADMINISTRACION','BEATRIZ','2011-02-05','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'0013-11','1','Sr. Eduardo J. Cardenas Bueno\r\nRespuesta a una carta enviada con firma del socio, sin tener conocimeitno del contenido de la misma','SI','SECRETARIA','BEATRIZ','2011-02-07','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,'0014-11','1','Sr. Victor Coz Ramos\r\nGerente Asesoria Juridica B.N\r\nNos informan que en relaci¾ a la referencia, ellos emitieron su respuesta a Compensaciones.','SI','ADMINISTRACION','BEATRIZ','2011-02-07','','ELIMINADO',NULL,'cARTA n║002-11 COOPAM -BN','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,'0015-11','1','Sra. Doris Lopez Galvez\r\nSolicita apoyo economico por familiar enfermo.','SI','ADMINISTRACION','BEATRIZ','2011-02-08','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,'0016-11','1','Carta de SEDAPAL\r\nInforman que se realizara una inspecci¾n al local gratuitamente.','SI','ADMINISTRACION','BEATRIZ','2011-02-11','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,'0017-11','1','Carta del Sr. Bartra \r\nAdjuntan carta de 7 socios que se les descuente como Asociaci¾n.','SI','ADMINISTRACION','BEATRIZ','2011-02-14','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,'0018-11','1','Sra. Carmen Coloma Zapata\r\nSolicita regularizaci¾n de aportes (no se le desconto por estar con Licencia\r\n','SI','ADMINISTRACION','BEATRIZ','2011-02-14','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,'0019-11','1','Sra.Wilma Adrianzen\r\nSolicita informe por que no se le realiza descuentos de su planilla\r\n','SI','CADMINISTRACION','BEATRIZ','2011-02-19','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,'0020-11','1','Carta Sra. Norma Jara \r\nsolicita refinanciamiento de deuda','SI','ADMINISTRACION',NULL,'2011-02-22','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,'0021-11','1','Infocorp \r\nRequerimiento de pago (Americatel)','SI','CONTABILIDAD',NULL,'2011-02-25','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,'0022-11','1','Sr. Saturnino F. Arias Huaman\r\nCarta rectificatoria, por haber firmdo carta sin leer cntenido','SI','SECRETARIA',NULL,'2011-02-25','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,'0023-11','1','Sr.Oscar Torres Cadenillas\r\nSolicitan apoyo con camisetas deportivas para campeonato del Banco de la Nacion.','SI','ADMINISTRACION',NULL,'2011-03-02','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,'0024-11','1','Trabajador Eduardo Chavesta\r\nsolicita dias de falta sean descontados de sus vacaciones','SI','ADMINISTRACION',NULL,'2011-03-05','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,'0025-11','1','Sr. Jorge Collado\r\nSolicita devol. de aportes por beneficio no pagado.','SI','ADMINISTRACION',NULL,'2011-03-04','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,'0026-11','1','Sr.Pedro Rosas Due±as \r\nSolicita dev. de descuento automatico por hija.','SI','ADMINISTRACION',NULL,'2011-03-09','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,'0027-11','1','Sr. Juan Paredes  Sanchez\r\nsolicita Estatuto de Cooperativa','SI','ADMINISTRACION',NULL,'2011-03-11','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,'0028-11','1','Sr. Maximo Ticona Gutierrez\r\nSolicita devolucion de aportes por suegra fallecida ','SI','ADMINISTRACION',NULL,'2011-03-15','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,'0029-11','1','Sr.Julio Lozada Seminario\r\nCarta Notarial indicando que no ha renunciado a la Asociacion Ayuda mutua y solicita por que no se le descuenta','SI','ADMINISTRACION',NULL,'2011-03-28','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,'0030-11','1','Sra. Zoila E.Huaman Quispe\r\nSolicita que no habiendole descontado en enero,febrero y marzo, se le regularize en 4 cuotas ','SI','ADMINISTRACION',NULL,'2011-03-29','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,'0031-11','1','Sr. Asuncion Mercedes M.\r\nSolicita sea revisado su situacion como asociado, habiendo firmado la autorizaci¾n de descuento como cooperativa','SI','ADMINISTRACION',NULL,'2011-04-06','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,'0032-11','1','Sr.Andres Pisfil Ballena\r\nSolicita renuncia por haberse convertido a Cooperativa','SI','ADMINISTRACION',NULL,'2011-04-07','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,'0033-11','1','Sra. Edit Arba±il Guillen\r\nSolicita inscripcion como socia(padre fus socio titular) fecha limite par ainscripcion caduco','SI','ADMINISTRACION',NULL,'2011-04-07','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,'0034-11','1','Sr. Hugo Ricce solicita reconcideracion de isncripcion con familiares anteriores.','SI','ADMINISTRACION',NULL,'2011-04-11','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(38,'0035-11','1','Sr. Hugo Ricce \r\nsolcita pagar 30.00 hasta cancelar la deuda de A.E.E','SI','ADMINISTRACION',NULL,'2011-04-11','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(39,'0036-11','1','Sr.Arnaul Arrunategui\r\nSolicita estado de su cuenta y copias de su A.E.E otorgada','SI','CONTABILIDAD',NULL,'2011-04-16','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(40,'0037-11','1','Sr.Bartra \r\nCarta informando que no se ha podido realizar ningun descuento a los Srs.Rivera Rojas Hubert,De Los Rios Perez Cesar y Madrid Bustios Cesar','SI','ADMINISTRACION',NULL,'2011-04-18','','ELIMINADO',NULL,'','','INCOMPLETO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(45,'0001-2020','21','FICHA SINTOMATOLOGICA COVID-19 - REGRESO AL TRABAJO - DECLARACION JURADA - JOSE CHAVESTA, MARLENE MAGALLANES, BEATRIZ MARCOS, LIZBETH ALCALDE, GAVY PERALES Y RICARDO ZARATE','SI','','BEATRIZMARCOS','2020-07-01','','FINALIZADO','ARCHIVADO',' ','FICHA SINTOMATOLOGICA COVID-19 - REGRESO AL TRABAJO - DECLARACION JURADA - JOSE CHAVESTA, MARLENE MAGALLANES, BEATRIZ MARCOS, LIZBETH ALCALDE, GAVY PERALES Y RICARDO ZARATE','COMPLETO','JOSE CHAVESTA, MARLENE MAGALLANES, BEATRIZ MARCOS, LIZBETH ALCALDE, GAVY PERALES Y RICARDO ZARATE',NULL,'2021-01-20',NULL,' ',' ',' ',1,'7:41:42','9:1:22',NULL,'BEATRIZMARCOS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(46,'0002-2020','19','CARTA DE VACACIONES SOLICITANDO COMPENSACION DE HORAS POR PERIODO DE EMERGENCIA SANITARIA û COVID 19  (16 MARZO 2020 A 30 JUNIO 2020)','SI','','BEATRIZMARCOS','2020-07-01','','FINALIZADO','ARCHIVADO',' ','CARTA DE VACACIONES SOLICITANDO COMPENSACION DE HORAS POR PERIODO DE EMERGENCIA SANITARIA û COVID 19  (16 MARZO 2020 A 30 JUNIO 2020)','COMPLETO','CHAVESTA PECHE JOSE EDUARDO',NULL,'2021-01-20',NULL,'1656106',' ',' ',1,'11:30:31','9:2:34',NULL,'BEATRIZMARCOS','16561067','CHAVESTA PECHE JOSE EDUARDO','16561067',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(47,'0003-2020','19','CARTA DE VACACIONES SOLICITANDO COMPENSACION DE HORAS POR PERIODO DE EMERGENCIA SANITARIA û COVID 19  (16 MARZO 2020 A 30 JUNIO 2020)','SI','','BEATRIZMARCOS','2020-07-02','','FINALIZADO','ARCHIVADO',' ','CARTA DE VACACIONES SOLICITANDO COMPENSACION DE HORAS POR PERIODO DE EMERGENCIA SANITARIA û COVID 19  (16 MARZO 2020 A 30 JUNIO 2020)','COMPLETO','MAGALLANES GOMEZ MARLENE ',NULL,'2021-01-20',NULL,'811333',' ',' ',1,'16:10:10','9:3:22',NULL,'BEATRIZMARCOS','8113332','MAGALLANES GOMEZ MARLENE','8113332',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48,'0004-2020','19','CARTA DE VACACIONES SOLICITANDO COMPENSACION DE HORAS POR PERIODO DE EMERGENCIA SANITARIA û COVID 19  (16 MARZO 2020 A 30 JUNIO 2020)','SI','','BEATRIZMARCOS','2020-07-02','','FINALIZADO','ARCHIVADO',' ','CARTA DE VACACIONES SOLICITANDO COMPENSACION DE HORAS POR PERIODO DE EMERGENCIA SANITARIA û COVID 19  (16 MARZO 2020 A 30 JUNIO 2020)','COMPLETO','MARCOS ROJAS LUISA BEATRIZ',NULL,'2021-01-20',NULL,'4079165',' ',' ',1,'10:59:50','9:4:45',NULL,'BEATRIZMARCOS','40791651','MARCOS ROJAS LUISA BEATRIZ','40791651',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(50,'0005-2020','13','BENEFICIO SOCIO TITULAR Y AEE PENDIENTE','SI','','BEATRIZMARCOS','2020-07-04','BENEF CARTA  05-2021.pdf','FINALIZADO','ARCHIVADO',' AEE 22.01.2019','Se registra Beneficio de socio Titular y AEE pendiente de pago','COMPLETO','VILCHEZ CHUQUIPUL ANGELINCA VIVIANA',NULL,'2021-01-26',NULL,'8005044',' ',' ',1,'12:4:57','15:37:49',NULL,'BEATRIZ MARCOS','40388684','VILCHEZ CHUQUIPUL ROSA VICTORIA','46598451',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(52,'0007-2020','1','AEE POR EMAIL, CON MISMO GARANTE','SI','','BEATRIZMARCOS','2020-07-06','','FINALIZADO','','','','COMPLETO','PALOMINO BEDOYA MELINA ADRIANA',NULL,'2023-09-19',NULL,'0302201','10000','36',1,'11:45:49','15:32:8',NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,NULL,NULL,NULL),(53,'0008-2020','1','AEE POR EMAIL','SI','','BEATRIZMARCOS','2020-07-06','','FINALIZADO','ARCHIVADO','','','COMPLETO','GONZALES QUISPE CARLOS VLADIMIR',NULL,'2021-03-01',NULL,'0345245','5500','36',1,'11:49:57','12:27:51',NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,NULL,NULL,NULL),(54,'0009-2020','1','AEE POR EMAIL','SI','','BEATRIZMARCOS','2020-07-06','','FINALIZADO','ARCHIVADO, Duplicidad con STD 0022-2020','','','COMPLETO','ARRASCO JUAREZ ALBERTO ',NULL,'2021-07-02',NULL,'17086','3000','36',1,'11:51:11','11:3:35',NULL,NULL,NULL,NULL,NULL,NULL,'','',NULL,NULL,NULL,NULL),(55,'0010-2020','2','AEE POR WAHTSAPP','SI','','BEATRIZMARCOS','2020-07-06',NULL,'FINALIZADO','Archivado',NULL,'','COMPLETO','ALIAGA PALOMINO LUIS HERNAN',NULL,'2020-07-07',NULL,'0312169','3000','12',1,'11:53:15','13:1:14',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(56,'0011-2020','1','TRAMITE OFOCINA','SI','','BEATRIZMARCOS','2020-07-06',NULL,'FINALIZADO','Archivado',NULL,'','COMPLETO','GALLEGOS PORRAS JOSE LUIS',NULL,'2020-07-07',NULL,'141372','2500','36',1,'11:54:44','11:18:46',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f29_tramite` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f30_historico`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f30_historico` DISABLE KEYS */;
INSERT INTO `f30_historico` VALUES (1,NULL,'CADMINISTRACION','REGISTRADO','2011-05-18','',NULL),(2,NULL,'CADMINISTRACION','REGISTRADO','2011-05-18','',NULL),(3,'CADMINISTRACION','CONTABILIDAD','REGISTRADO','2011-05-19','prueba 1',NULL),(4,'BEATRIZ','CADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(5,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(6,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(7,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(8,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(9,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(10,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(11,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(12,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(13,'BEATRIZ','SECRETARIA','REGISTRADO','2011-10-31','',NULL),(14,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(15,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(16,'BEATRIZ','SECRETARIA','REGISTRADO','2011-10-31','',NULL),(17,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(18,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(19,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(20,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(21,'BEATRIZ','ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(22,'BEATRIZ','CADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(23,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(24,NULL,'CONTABILIDAD','REGISTRADO','2011-10-31','',NULL),(25,NULL,'SECRETARIA','REGISTRADO','2011-10-31','',NULL),(26,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(27,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(28,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(29,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(30,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(31,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(32,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(33,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(34,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(35,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(36,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(37,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(38,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(39,NULL,'CONTABILIDAD','REGISTRADO','2011-10-31','',NULL),(40,NULL,'ADMINISTRACION','REGISTRADO','2011-10-31','',NULL),(41,'BEATRIZ','SECRETARIA','REGISTRADO','2011-11-12','TEST',NULL),(41,'ADMINISTRADOR','GAVY','REGISTRADO','2020-06-29','fa±ta regularizar documentos','12:1:9'),(42,'ADMINISTRADOR','GAVY','REGISTRADO','2020-06-30','necesita garante','15:57:19'),(43,'GAVY','ACONTABLE','REGISTRADO','2020-06-30','sin garante','16:5:59'),(42,'GAVY','ACONTABLE','APROBADO','2020-06-30','se exonera garante','16:16:27'),(44,'ADMINISTRADOR','GAVY','REGISTRADO','2020-07-02','obs prueba','16:41:38'),(45,'ADMIN','GAVY','REGISTRADO','2020-07-03','quiere 2000 liquido','7:41:42'),(45,'GAVY','ACONTABLE','DERIVADO','2020-07-03',NULL,'7:45:32'),(46,'SECRETARIA','CONTABILIDAD','REGISTRADO','2020-07-03','ampliacion, 1000 liquido','11:30:31'),(46,'CONTABILIDAD','ACONTABLE','DERIVADO','2020-07-03',NULL,'11:36:21');
/*!40000 ALTER TABLE `f30_historico` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f31_productos`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f31_productos` DISABLE KEYS */;
INSERT INTO `f31_productos` VALUES (1,'AMPLIADO','A REBATIR','SOLES',60,1,51000,'SI','A',1),(2,'GRATIFICACION FP.','FLAT','SOLES',1,1,10000,'SI','A',2),(3,'GRATIFICACION NAV.','FLAT','SOLES',1,1,10000,'SI','A',3),(4,'C.DEUDA','FLAT','SOLES',1,1,10000,'SI','A',5),(5,'REFINANCIADO','A REBATIR','SOLES',60,1,60000,'SI','A',1),(6,'PRESTAMO P','A REBATIR','SOLES',60,100,51000,'SI','A',1),(7,'PRESTAMO AM','A REBATIR','SOLES',60,500,15000,'SI','A',1),(8,'BONO BDG','FLAT','SOLES',1,100,10000,'SI','A',4),(9,'PRESTAMO A','A REBATIR','SOLES',60,100,51000,'SI','A',1),(10,'REPROGRAMADO','FLAT','SOLES',60,100,20000,'SI','A',5),(11,'UTILIDAD','FLAT','SOLES',1,1,10000,'SI','A',1);
/*!40000 ALTER TABLE `f31_productos` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f32_tarifa_fam`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f32_tarifa_fam` DISABLE KEYS */;
INSERT INTO `f32_tarifa_fam` VALUES (1,0,50,15),(2,51,60,20),(3,61,70,30),(4,71,100,40);
/*!40000 ALTER TABLE `f32_tarifa_fam` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f33_persona_auditoria`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f33_persona_auditoria` DISABLE KEYS */;
INSERT INTO `f33_persona_auditoria` VALUES (1,1,'41365708','GONZALES','QUISPE','CARLOS','VLADIMIR',1,'1982-06-05',2,1,'',1,0,0,'15',NULL,'15','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:05:21',NULL,NULL,'','JIRON MARIA ANTONIETA','293  INT. 301','','','15','01','35',NULL,36,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','0345245','N'),(2,1,'44065795','DOLORES','BUSTAMANTE','MILY','GANDY',2,'1986-11-18',1,1,'',1,0,0,'15','08','01','OFC 96798','991853074','250618-ACT',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:07:15',NULL,NULL,'','AVENIDA BRASIL','975 TORRE 2  DPTO 1103',NULL,NULL,'15','01','13',NULL,31,1,'ACTIVO','4028576418',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','250618-ACT',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0345008','M'),(3,1,'44065795','DOLORES','BUSTAMANTE','MILY','GANDY',2,'1986-11-18',1,1,'',1,0,0,'15',NULL,'15','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:09:32',NULL,NULL,'','AVENIDA BRASIL','975 TORRE 2  DPTO 1103','','','15','01','13',NULL,31,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','0345008','N'),(4,1,'41588965','CUZCANO','ALLAUCA','ROBERTO','CARLOS',1,'1982-11-07',2,1,'roberto0711@hotmail.com',1,0,0,'15','01','13','ofc 94807','912240348','250618-ACT',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:12:16',NULL,NULL,'LA CAMPIÐA','CALLE UNIVERSO','MZ W  LTO 6 C ',NULL,NULL,'15','01','08',NULL,35,1,'ACTIVO','4057881882',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','250618-ACT',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0341142','M'),(5,1,'41588965','CUZCANO','ALLAUCA','ROBERTO','CARLOS',1,'1982-11-07',1,1,'',1,0,0,'15',NULL,'15','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:13:33',NULL,NULL,'LA CAMPIÐA','CALLE UNIVERSO','MZ W  LTO 6 C ','','','15','01','08',NULL,35,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','0341142','N'),(6,1,'21574110','ARNAO','RAMIREZ','KARINA','CECILIA',2,'1977-09-03',1,1,'karinacecilia2011@hotmail.com',1,0,0,'11','01','01','OFC 56103','995043044','250618-ACT',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:16:20',NULL,NULL,'RESIDENCIAL SAN CARLOS','','B 3',NULL,NULL,'11','01','01',NULL,40,1,'ACTIVO','4009539005',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','250618-ACT',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0298174','M'),(7,1,'21574110','ARNAO','RAMIREZ','KARINA','CECILIA',2,'1977-09-03',1,1,'',1,0,0,'11',NULL,'11','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:17:26',NULL,NULL,'RESIDENCIAL SAN CARLOS','','B 3','','','11','01','01',NULL,40,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','0298174','N'),(8,1,'10799157','AMPUERO','CARO','MARTHA','YSABEL',2,'1960-12-12',2,1,'martha.ampuero18@gmail.com',1,0,0,'01','01','05','','975704058','250618-ACT',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:21:07',NULL,NULL,'BARRIO SANTO DOMINGO','JIRON LIBERTAD','1226',NULL,NULL,'01','01','02',NULL,57,1,'ACTIVO','4000982097',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','250618-ACT',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0276359','M'),(9,1,'10799157','AMPUERO','CARO','MARTHA','YSABEL',2,'1960-12-12',1,1,'',1,0,0,'01',NULL,'01','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:22:13',NULL,NULL,'BARRIO SANTO DOMINGO','JIRON LIBERTAD','1226','','','01','01','02',NULL,57,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','0276359','N'),(10,1,'44162366','CARHUAS','TAIPE','JUAN','ALBERTO',1,'1987-03-16',2,1,'juan_17_1@hotmail.com',1,0,0,'15','01','35','5286129-OFC 16201','983474728','250618-ACT ',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:24:06',NULL,NULL,'URB STA ANA','CALLE 11 ','MZ B 2  LT  09',NULL,NULL,'15','01','17',NULL,31,1,'ACTIVO','4047427512',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','250618-ACT ',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0332607','M'),(11,1,'44162366','CARHUAS','TAIPE','JUAN','ALBERTO',1,'1987-03-16',1,1,'',1,0,0,'15',NULL,'15','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:28:19',NULL,NULL,'URB STA ANA','CALLE 11 ','MZ B 2  LT  09','','','15','01','17',NULL,31,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','0332607','N'),(12,1,'41225357','HIDALGO','NAVARRO','ERILA','',2,'1982-03-24',1,1,'eenf24@gmail.com',1,0,0,'22','09','01','','948319756','250618-ACT\r\n',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:32:01',NULL,NULL,'URB. MIRAFLORES','CALLE FLOR DE LA TOTORA','MZ I LT  5  DPTO 301',NULL,NULL,'13','01','01',NULL,36,1,'ACTIVO','4010757083',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','250618-ACT\r\n',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','8004927','M'),(13,1,'41225357','HIDALGO','NAVARRO','ERILA','',2,'1982-03-24',1,1,'',1,0,0,'22',NULL,'22','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:32:55',NULL,NULL,'URB. MIRAFLORES','CALLE FLOR DE LA TOTORA','MZ I LT  5  DPTO 301','','','13','01','01',NULL,36,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','8004927','N'),(14,1,'10881664','RODRIGUEZ','QUISPE','YOON','',1,'1975-01-14',1,1,'yoonrq2010@hotmail.com',1,0,0,'04','08','10','OFC 98503','990403850','250618-ACT',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:34:58',NULL,NULL,'URB LOS SAUCES','','MZ N LT 15 ',NULL,NULL,'15','01','06',NULL,43,1,'ACTIVO','4053858347',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','250618-ACT',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0335525','M'),(15,1,'10881664','RODRIGUEZ','QUISPE','YOON','',1,'1975-01-14',1,1,'',1,0,0,'04',NULL,'04','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-25 11:35:51',NULL,NULL,'URB LOS SAUCES','','MZ N LT 15 ','','','15','01','06',NULL,43,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','0335525','N'),(16,1,'08783637','ARGUEDAS','BENTIN','AUGUSTO','ARTURO JOSE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dio de Baja Familiar',NULL,NULL,NULL,NULL,'MARLENE','2018-06-27 11:59:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'ACTIVO',NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'F','254509','E'),(17,1,'08782314','BENTIN ','SANCHEZ VDA DE ARGUEDAS','ROSA','',2,'1929-05-06',1,1,'',1,0,0,'',NULL,'','','','270618-ACT',NULL,NULL,NULL,NULL,'MARLENE','2018-06-27 12:02:14',NULL,NULL,'','','','','','15','01','40',NULL,89,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','254509','M'),(18,1,'42602014','VARGAS','RAMIREZ','DAVID','ERNESTO',1,'1984-08-30',1,1,'',1,0,0,'10','06','01','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-27 14:55:40',NULL,NULL,'1254','','',NULL,NULL,'10','06','01',NULL,33,1,'ACTIVO','4028731049',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0311538','N'),(19,1,'09947285','CRUZ','RIVERA','WILSON','ALBERTO',1,'1975-03-13',1,1,'',1,0,0,'15','01','13','','989178515','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-28 10:22:16',NULL,NULL,'URB PALOMARES','CALLE PALOMARES','292',NULL,NULL,'15','01','28',NULL,43,1,'ACTIVO','4019963174',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0314508','N'),(20,1,'09947285','CRUZ','RIVERA','WILSON','ALBERTO',1,'1975-03-13',1,1,'',1,0,0,'15','01','13','','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-28 10:30:27',NULL,NULL,'URB PALOMARES','CALLE PALOMARES','292',NULL,NULL,'15','01','28',NULL,43,1,'ACTIVO','4057346510',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0340367','N'),(21,1,'10435454','ATOCHE','PRIETO','ANDY ','ISRAEL',1,'1971-09-25',2,1,'',1,0,0,'15','01','01','987033452','','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-28 14:35:10',NULL,NULL,'URB INFANTAS','CALLE LIMA','MZ B LT 7 ',NULL,NULL,'15','01','35',NULL,46,1,'ACTIVO','4009362636',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0341371','N'),(22,1,'80156119','VARGAS','PEÐA','ANTONIO','CESAR MANUEL',1,'1978-06-13',1,1,'',1,0,0,'10','01','01','062-284533','364513613','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-28 14:41:42',NULL,NULL,'','JIRON AREQUIPA','1033',NULL,NULL,'10','06','01',NULL,40,1,'ACTIVO','4063892349',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0349453','N'),(23,1,'42602014','VARGAS','RAMIREZ','DAVID','ERNESTO',1,'1984-08-30',1,1,'',1,0,0,'10','06','01','','989621935','',NULL,NULL,NULL,NULL,'MARLENE','2018-06-28 14:46:40',NULL,NULL,'','CPM AFILIADO','S/N',NULL,NULL,'10','06','01',NULL,33,1,'ACTIVO','4028731049',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0331538','N'),(24,1,'08491673','BRINGAS','BACON','GUILLERMO','DAVID',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dio de Baja al Socio',NULL,NULL,NULL,NULL,'ADMIN','2018-06-28 18:20:45',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,'ACTIVO',NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'S','21449','E'),(25,1,'07010133892','APOLAYA','VALDIVIA','JESUS','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dio de Baja Familiar',NULL,NULL,NULL,NULL,'MARLENE','2018-07-02 09:11:53',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'PENSIONISTA',NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'F','133892','E'),(26,1,'45124069','APOLAYA  ','HINOSTROZA','JESUS','ANTONIO',1,'1988-04-01',1,1,'',1,0,0,'',NULL,'','','','Mayor 25 A±os s/c m·ltiple 047-13 Reinscripci¾n.\r\nRegularizaci¾n de aporte mes de abril mayo 2013.\r\nAporte mensual 10 nuevos soles',NULL,NULL,NULL,NULL,'MARLENE','2018-07-02 09:14:08',NULL,NULL,'','','','','','15','01','41',NULL,30,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','133892','M'),(27,1,'10372733','ORTEGA','CABRERA','OSCAR','JUAN',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dio de Baja Familiar',NULL,NULL,NULL,NULL,'MARLENE','2018-07-02 09:16:15',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'PENSIONISTA',NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'F','38431','E'),(28,1,'45946812','ORTEGA ','PORRAS','JUDITH ','DEL ROSARIO',2,'1989-08-30',1,1,'',1,0,0,'',NULL,'','','','Continuaci¾n, Carta m·ltiple 195-14.\r\nAporte mensual 10 nuevos soles',NULL,NULL,NULL,NULL,'MARLENE','2018-07-02 09:17:33',NULL,NULL,'','','','','','','','',NULL,28,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','38431','M'),(29,1,'72561574','ROJAS  ','MINAYA','JUAN ','DIEGO ROBERTO',1,'1993-05-14',1,1,'',1,0,0,'',NULL,'','','','Continuaci¾n, mayor 25 a±os ',NULL,NULL,NULL,NULL,'MARLENE','2018-07-02 09:22:48',NULL,NULL,'','','','','','15','01','01',NULL,25,NULL,NULL,NULL,NULL,10,NULL,NULL,'NO',NULL,NULL,10,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','43818','M'),(30,1,'08837038','FLORES','AMEZ','JULIO','',1,'1939-08-12',2,1,'',1,0,0,'15','01','01','4575125','934508965','030718_ACT',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-03 12:02:41',NULL,NULL,'','JIRON ESPAÐA','907 -3ER PISO -301',NULL,NULL,'07','01','04',NULL,78,1,'PENSIONISTA','400934637',NULL,10,NULL,'F0026751.PDF','NO',NULL,NULL,10,NULL,'','030718_ACT',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,0,'20530',NULL,NULL,NULL,'S','26751','M'),(31,1,'12345678','MEDRANO','ROJAS','LIZBETH','CAROL',2,'1980-01-01',2,1,'',1,0,0,'','','','','','',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-03 15:48:05',NULL,NULL,'','LAS ARTES NORTE','578',NULL,NULL,'15','01','30',NULL,38,1,'ACTIVO','',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0350834','N'),(32,1,'44402809','GONZALES','CORREA','PEDRO','ENRIQUE',1,'1980-01-01',2,1,'',1,0,0,'','','','','','',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-03 15:50:12',NULL,NULL,'','LAS ARTES NORTE','578',NULL,NULL,'15','01','30',NULL,38,1,'ACTIVO','',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0349836','N'),(33,1,'46676900','MEDRANO','ROJAS','LIZBETH','CAROL',2,'1980-01-01',2,1,'',1,0,0,'','','','','','.',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-03 15:50:37',NULL,NULL,'','LAS ARTES NORTE','578',NULL,NULL,'15','01','30',NULL,38,1,'ACTIVO','',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','.',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'AFP',NULL,NULL,NULL,'S','0350834','M'),(34,1,'08822203','DE LA FUENTE','CHAVEZ','JUAN','DE DIOS VICTOR',1,'1932-03-08',2,1,'',1,0,0,'15','01','13','2752025','','300317_ACT\r\nAPORTACIONES VISIBLES EN PLANILLA DESDE EL 01/01/1989.',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-03 16:57:37',NULL,NULL,'URB INGENIEROS - VALLE HERMOSO','CALLE LOS LAURELES','395 DPTO 501',NULL,NULL,'15','01','40',NULL,86,1,'PENSIONISTA','4000929471',NULL,20,NULL,'F0024066.PDF','NO',NULL,NULL,20,NULL,'','300317_ACT\r\nAPORTACIONES VISIBLES EN PLANILLA DESDE EL 01/01/1989.',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,0,'20530',NULL,NULL,NULL,'S','24066','M'),(35,1,'22510634','MARTELL','YACOLCA','SILVIA','JANETH',2,'1974-03-24',1,1,'',1,0,0,'10','01','02','','','CESO CON INCENTIVO\r\nDeuda pendiente por AEE, asumida por su garante Socio \r\nJimÚnez Lugo Umbelinda C¾d. 0267619.\r\n962991166 Nos comunicamos a este numero de celular pero no le pertenece al socio. 040618\r\n191016',NULL,NULL,NULL,NULL,'MARLENE','2018-07-04 11:11:21',NULL,NULL,'URB LOS PORTALES','LOS NOGALES','210',NULL,NULL,'10','01','02',NULL,44,1,'ACTIVO','4010535048',NULL,0,NULL,'','NO',NULL,NULL,0,NULL,'','CESO CON INCENTIVO\r\nDeuda pendiente por AEE, asumida por su garante Socio \r\nJimÚnez Lugo Umbelinda C¾d. 0267619.\r\n962991166 Nos comunicamos a este numero de celular pero no le pertenece al socio. 040618\r\n191016',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,0,'AFP',NULL,NULL,NULL,'S','0298441','M'),(36,1,'70269930','LUDENA ','ASENCIO','JOSE ','LUIS',1,'1992-04-01',1,1,'',1,0,0,'',NULL,'','','','INGRESO JUNIO 2018',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-09 09:53:59',NULL,NULL,'','','','','','15','01','17',NULL,26,NULL,NULL,NULL,NULL,15,NULL,NULL,'NO',NULL,NULL,15,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','263591','M'),(37,1,'08888386','SANTILLAN','DURAND','EDMUNDO','ENRIQUE',1,'1952-06-25',2,1,'condemontecristo60@hotmail.com',1,0,0,'15','08','11','2721772','999355745','090718_ACT',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-09 15:26:29',NULL,NULL,'CON HAB LA MERCED','ALAMEDA LUIS MIROQUESADA','137 - DPTO 301 - BLOCK 11 ',NULL,NULL,'15','01','41',NULL,66,1,'PENSIONISTA','',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','090718_ACT',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,0,'20530',NULL,NULL,NULL,'S','144614','M'),(38,1,'08862117','OYOLA','PIZARRO','HUGO','',1,'1942-08-25',2,1,'',1,0,0,'20','07','02','2663066','','030517_ACT\r\nConjugue continua',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 10:22:41',NULL,NULL,'URB LA CALERA DE LA MERCED','CALLE MARCO POLO','120 DPTO 301',NULL,NULL,'15','01','41',NULL,75,4,'EXTRABAJADOR','4000939396',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','030517_ACT\r\nConjugue continua',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'20530',NULL,NULL,NULL,'S','38741','M'),(39,1,'08862117','OYOLA','PIZARRO','HUGO','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dio de Baja Familiar',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 10:23:55',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'EXTRABAJADOR',NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'F','38741','E'),(40,1,'','OYOLA  ','AGUILAR','KARL ','STEFAN',1,'1983-08-30',1,1,'',1,0,0,'',NULL,'','','','Baja por ser mayor de 25 a±os.',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 10:24:51',NULL,NULL,'','','','','','15','01','41',NULL,34,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','38741','M'),(41,1,'40925920','LI','MONDRAGON','ELIANA','VICTORIA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dio de Baja al Socio',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 11:06:25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'ACTIVO',NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'S','22590','E'),(42,1,'40925920','LI','MONDRAGON','ELIANA','VICTORIA',2,'1981-06-29',2,1,'evlm19@hotmail.com',1,0,0,'20','01','01','326581-OFC 73222','970933280','Carta de renuncia s/n de fecha 220618.\r\nAporte mensual 17 soles',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 11:07:07',NULL,NULL,'LOS EJIDOS DEL NORTE KM 1.2','PASAJE VISTA ALEGRE','MZ B- 10',NULL,NULL,'20','01','01',NULL,37,1,'ACTIVO','4013544927',NULL,0,NULL,'','NO',NULL,NULL,0,NULL,'','Carta de renuncia s/n de fecha 220618.\r\nAporte mensual 17 soles',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,0,'AFP',NULL,NULL,NULL,'S','0305588','M'),(43,1,'06606493','JIMENEZ','LA TORRE','FELIPE','ABELARDO',1,'1925-12-03',1,1,'',1,0,0,'','','','4367609','','Dejo sobre con Declaraci¾n 08/09/2011.',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 15:06:44',NULL,NULL,' RECAUDADORES ','LOS PARACAS',' 672',NULL,NULL,'15','01','03',NULL,92,1,'PENSIONISTA','4000851411',NULL,30,NULL,'','NO',NULL,NULL,30,NULL,'','Dejo sobre con Declaraci¾n 08/09/2011.',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,0,'20530',NULL,NULL,NULL,'S','85456','M'),(44,1,'10435454','ATOCHE','PRIETO','ANDY ','ISRAEL',1,'1971-09-25',2,1,'',1,0,0,'15','01','01','987033452','','.',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-10 15:50:21',NULL,NULL,'URB INFANTAS','CALLE LIMA','MZ B LT 7 ',NULL,NULL,'15','01','35',NULL,46,1,'ACTIVO','4009362636',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','.',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,0,'AFP',NULL,NULL,NULL,'S','0341371','M'),(45,1,'08864362','AGUILAR    ','CASTILLO','DOLORES','',2,'1943-01-01',2,1,'',1,0,0,'15',NULL,'15','','','100718-ACT',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 15:53:05',NULL,NULL,'','','','','','15','01','41',NULL,75,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','38741','M'),(46,1,'08862117','OYOLA','PIZARRO','HUGO','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Dio de Baja Familiar',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 15:53:19',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,'EXTRABAJADOR',NULL,NULL,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'F','38741','E'),(47,1,'80156119','VARGAS','PEÐA','ANTONIO','CESAR MANUEL',1,'1978-06-13',1,1,'',1,0,0,'10','01','01','062-284533','364513613','.',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-10 15:53:50',NULL,NULL,'','JIRON AREQUIPA','1033',NULL,NULL,'10','06','01',NULL,40,1,'ACTIVO','4063892349',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','.',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,0,'AFP',NULL,NULL,NULL,'S','0349453','M'),(48,1,'08862117','OYOLA','PIZARRO','HUGO','',1,'1942-08-25',2,1,'',1,0,0,'20',NULL,'20','4492377','','100718-ACT',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 15:54:15',NULL,NULL,'URB LA CALERA DE LA MERCED','CALLE MARCO POLO','120 DPTO 301','','','15','01','41',NULL,75,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','38741','M'),(49,1,'42602014','VARGAS','RAMIREZ','DAVID','ERNESTO',1,'1984-08-30',1,1,'',1,0,0,'10','06','01','','989621935','.',NULL,NULL,NULL,NULL,'BEATRIZ','2018-07-10 15:54:36',NULL,NULL,'','CPM AFILIADO','S/N',NULL,NULL,'10','06','01',NULL,33,1,'ACTIVO','4028731049',NULL,10,NULL,'','NO',NULL,NULL,10,NULL,'','.',NULL,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,10,0,'AFP',NULL,NULL,NULL,'S','0331538','M'),(50,1,'08782314','BENTIN ','SANCHEZ VDA DE ARGUEDAS','ROSA','',2,'1929-05-06',1,1,'',1,0,0,'15',NULL,'15','','','100718-ACT',NULL,NULL,NULL,NULL,'MARLENE','2018-07-10 16:08:08',NULL,NULL,'','','','','','15','01','40',NULL,89,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'F','254509','M');
/*!40000 ALTER TABLE `f33_persona_auditoria` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f35_tipo_documento`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f35_tipo_documento` DISABLE KEYS */;
INSERT INTO `f35_tipo_documento` VALUES (1,'REFINANCIADO','AREF','1',1),(2,'PERSONAL','APER','1',1),(3,'F.PATRIAS','AFPA','1',1),(4,'NAVIDAD','AFPA','1',1),(5,'UTILIDAD','AFPA','1',1),(6,'BONO BDG','AFPA','1',1),(7,'REGULAR','AFPA','1',1),(8,'AMPLIADO','AFPA','1',1),(9,'AÐOS ANTERIORES','AFPA','1',1),(10,'PROMOCIONAL','AFPA','1',1),(11,'ACT.CONDICION SOCIO','AFPA','1',1),(12,'CAMPAÐA','AFPA','1',1),(13,'BENEFICIO','BENF','1',1),(14,'INSCRIPCION','INSC','1',1),(15,'ACTUALIZA DATOS','RENU','1',1),(16,'AMPL. FAMILIA','RENU','1',1),(17,'RENUNCIA','RENU','1',1),(18,'DEVOLUCION','DEVO','1',1),(19,'CARTA','CART','1',1),(20,'MEMORANDUM','MEMO','1',1),(21,'OTROS','MEMO','1',1),(22,'CARTA','CAR','2',1),(23,'MEMORANDUM','MEMO','2',1),(24,'OTROS','OTRO','2',1),(25,'DECLARACION JURADA','DJU','1',1),(26,'RETIRO FAMILIAR','RFAM','1',1),(27,'ING.EXTR. - AP.COOPAM','AB','1',1),(28,'ING.EXTR. - EXCESO AEE','AB','1',1),(29,'AVAL','AB','1',1),(30,'REPROGRAMADO','AB','1',1),(31,'HIPOTECARIO','AB','1',1);
/*!40000 ALTER TABLE `f35_tipo_documento` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f36_correlativos_documentos`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f36_correlativos_documentos` DISABLE KEYS */;
INSERT INTO `f36_correlativos_documentos` VALUES (1,1,'1','0000000','9999999','1335',0),(2,2,'2','0000000','9999999','1335',0),(3,3,'3','0000000','9999999','1335',0),(4,4,'4','0000000','9999999','1335',0),(5,5,'5','0000000','9999999','1335',0),(6,6,'6','0000000','9999999','1335',0),(7,7,'7','0000000','9999999','1335',0),(8,8,'8','0000000','9999999','1335',0),(9,9,'9','0000000','9999999','1335',0),(10,10,'9','0000000','9999999','1335',0),(11,11,'9','0000000','9999999','1335',0),(12,12,'9','0000000','9999999','1335',0),(13,13,'9','0000000','9999999','1335',0),(14,14,'9','0000000','9999999','1335',0),(15,15,'9','0000000','9999999','1335',0),(16,16,'9','0000000','9999999','1335',0),(17,17,'9','0000000','9999999','1335',0),(18,18,'9','0000000','9999999','1335',0),(19,19,'9','0000000','9999999','1335',0),(20,20,'9','0000000','9999999','1335',0),(21,21,'9','0000000','9999999','1335',0),(22,22,'9','0000000','9999999','408',0),(23,23,'9','0000000','9999999','408',0),(24,24,'9','0000000','9999999','408',0),(25,25,'9','0000000','9999999','1335',0),(26,26,'9','0000000','9999999','1335',0),(27,27,'9','0000000','9999999','1335',0),(28,28,'9','0000000','9999999','1335',0),(29,29,'9','0000000','9999999','1335',0),(30,30,'9','0000000','9999999','1335',0),(31,31,'9','0000000','9999999','1335',0);
/*!40000 ALTER TABLE `f36_correlativos_documentos` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f36_correlativos_documentos4`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f36_correlativos_documentos4` DISABLE KEYS */;
INSERT INTO `f36_correlativos_documentos4` VALUES (1,1,'1','0000000','9999999','0',0),(2,2,'2','0000000','9999999','0',0),(3,3,'3','0000000','9999999','0',0),(4,4,'4','0000000','9999999','0',0),(5,5,'5','0000000','9999999','0',0),(6,6,'6','0000000','9999999','0',0),(7,7,'7','0000000','9999999','0',0),(8,8,'8','0000000','9999999','0',0),(9,9,'9','0000000','9999999','0',0),(10,10,'9','0000000','9999999','0',0),(11,11,'9','0000000','9999999','0',0),(12,12,'9','0000000','9999999','0',0),(13,13,'9','0000000','9999999','0',0),(14,14,'9','0000000','9999999','0',0),(15,15,'9','0000000','9999999','0',0),(16,16,'9','0000000','9999999','0',0),(17,17,'9','0000000','9999999','0',0),(18,18,'9','0000000','9999999','0',0),(19,19,'9','0000000','9999999','0',0),(20,20,'9','0000000','9999999','0',0),(21,21,'9','0000000','9999999','0',0),(22,22,'9','0000000','9999999','0',0),(23,23,'9','0000000','9999999','0',0),(24,24,'9','0000000','9999999','0',0),(25,25,'9','0000000','9999999','0',0),(26,26,'9','0000000','9999999','0',0);
/*!40000 ALTER TABLE `f36_correlativos_documentos4` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f37_diario_contable`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f37_diario_contable` DISABLE KEYS */;
INSERT INTO `f37_diario_contable` VALUES (3,122013468,'2026-09-10','1651',4,'Yo',0.00,0.00,'ADMIN','2026-09-10 20:36:40','ACTIVO'),(4,122013469,'2026-09-12','1651',16,'Yo',0.00,0.00,'ADMIN','2026-09-12 01:19:02','ACTIVO'),(5,122013470,'2026-09-12','1651',6,'',0.00,0.00,'ADMIN','2026-09-12 01:22:26','ACTIVO'),(6,122013471,'2026-09-12','1651',6,'',9846.69,0.00,'ADMIN','2026-09-12 01:29:12','ACTIVO');
/*!40000 ALTER TABLE `f37_diario_contable` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f38_codigo_contable`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f38_codigo_contable` DISABLE KEYS */;
INSERT INTO `f38_codigo_contable` VALUES ('00','CUENTAS DE ORDEN',NULL),('001','AYUDA ECONOMICA EXTRAORDINARIA','00'),('0011','AEE ACTIVOS','001'),('0012','AEE PENSIONES','001'),('0013','AEE FEBAN','001'),('0014','AEE OTROS','001'),('0015','OTROS','001'),('0016','OTROS (EJER. ANTERIORES)','001'),('002','SOBREGIROS G.ADM AEE','00'),('003','SOBREGIRO G.MORAT. AEE','00'),('004','CUANTAS POR COBRAR DIVERSAS','00'),('0041','CUENTAS X COBRAR (EJER.ANTERIO','004'),('10','CAJA Y BANCOS',NULL),('101','CAJA','10'),('1011','CAJA SOLES','101'),('1012','CAJA DOLARES','101'),('102','FONDO FIJO','10'),('1021','CAJA CHICA','102'),('103','REMESAS EN TRANSITO','10'),('104','CUENTAS CORRIENTES','10'),('1041','BANCO DE LA ANCION','104'),('10411','BANCO NACION MN 00-015-000929','1041'),('1042','OTROS BANCOS','104'),('105','CERTIFICADOS BANCARIOS','10'),('106','DEPOSITOS EN AHORROS','10'),('10611','BANCO NACION M.N.4000009666','106'),('10612','BANCO NACION MN 04-015-471005','106'),('10614','BCO.NAC RZV MN 04-093-662070','106'),('10615','BBVA MN 0011600200741401','106'),('10616','BBVA ME 0011630200741428','106'),('108','OTROS DEPOSITOS','10'),('109','FONDOS SUJETOS A RESTRICCION','10'),('14','CTAS.POR COBRAR ACC.Y PERSONAL',NULL),('141','PRESTAMO AL PERSONAL','14'),('1411','PRESTAMOS AL PERSONAL','141'),('14111','PRESTAMO A EMPLEADOS','1411'),('143','PRESTAMOS A DIRECTORES','14'),('16','CUENTAS POR COBRAR DIVERSAS',NULL),('162','RECLAMO A TERCERO (EJ.ANTERIOR','16'),('1621','PEND. DEPOSITO BCO. NACION','162'),('16211','PEND. DEP. ACTIVOS','1621'),('16212','PEND. DEP. PENSIONISTAS','1621'),('16213','PEND. DEP. FEBAN','1621'),('163','MULTA ELECCIONES X COBRAR','16'),('164','DEP. OTORGADO EN GARANTIA','16'),('1641','PRESTAMO DE INST.FINANCIERAS','164'),('1642','PRESTAMO DE INST.NO FINANCIERA','164'),('1644','DEP.EN GARANTIA POR ALQUILER','164'),('1649','OTROS DEPOSITOS EN GARANTIA','164'),('165','PRESTAMO A SOCIOS','16');
/*!40000 ALTER TABLE `f38_codigo_contable` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f62_parentesco`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f62_parentesco` DISABLE KEYS */;
/*!40000 ALTER TABLE `f62_parentesco` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f63_multa`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f63_multa` DISABLE KEYS */;
INSERT INTO `f63_multa` VALUES (1,'4055589','2022-01-01',9,0,9,NULL,NULL);
/*!40000 ALTER TABLE `f63_multa` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f64_rifa`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f64_rifa` DISABLE KEYS */;
INSERT INTO `f64_rifa` VALUES (3,'4055589','2022-01-01',1,0,1,NULL,NULL);
/*!40000 ALTER TABLE `f64_rifa` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f92_persona_baja`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f92_persona_baja` DISABLE KEYS */;
INSERT INTO `f92_persona_baja` VALUES (530,1,'08836738','TELLES','GARCIA','CARLOS','ALBERTO',1,'1942-05-05 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-03-30 00:00:00',NULL,NULL,'CALLE 10 CALERA DE LA MERCED SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,68,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(536,1,'09010890','TELLO','VASQUEZ','BERNARDINO','ANDRES',1,'1927-05-20 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4608317',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1994-09-10 00:00:00',NULL,NULL,'CALLE CACIQUE DE UMACHIRI 308 - 310 URB. SAN MIGUELITO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,83,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'NO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(542,1,'17913116','TERRONES','ESTELA','MARIA','ESPERANZA',1,'1938-04-16 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'201841',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-02-10 00:00:00',NULL,NULL,'AV. SANTA 571 - A TRUJILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,72,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'VOLUNTARIO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(543,1,'08431166','TERRONES','VIGIL','CLARA','EULINA',1,'1948-11-26 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4357820',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-06-10 00:00:00',NULL,NULL,'PARQUE CHIMU 179 ATE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,62,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'FALLECIDO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(550,1,'09859950','TORDOYA','VILLALOBOS','CARLOS','ALFONSO',1,'1952-05-28 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'95129686',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-08-10 00:00:00',NULL,NULL,'TENIENTE RAZURI 796 RIMAC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,58,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(562,1,'16644139','TORRES','MIRANDA','AVERCIO','SILVESTRE',1,'1931-10-22 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'490549',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-12-10 00:00:00',NULL,NULL,'FELIPE SANTIAGO SALAVERRY 315- CHICLAYO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,79,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'NO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(563,1,'06050535','TORRES','MONTALVO','HERMINIO','LUIS',1,'1929-01-08 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2006-02-28 00:00:00',NULL,NULL,'JR.CANGALLO 465-DPTO 102 CERCADO DE LIMA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,81,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'FALLECIDO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(574,1,'07758142','TRUJILLO','DAVILA','LOIDA','NORKA',1,'1951-10-23 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'2613464',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-02-17 00:00:00',NULL,NULL,'HUAMANGA 410 DPTO.B MARBELLA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,59,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'VOLUNTARIO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(581,1,'02880349','UGARTE','BARANDIARAN','MILDA','ELENA',2,'1946-01-12 00:00:00',2,0,'',1,0,0,'15','01','01','2711184','','',NULL,NULL,NULL,NULL,NULL,'2004-04-10 00:00:00',NULL,NULL,'URB LA CALERA DE LA MERCED','CALLE ALFA BOYERO','160',NULL,NULL,'15','01','41',NULL,64,1,'PENSIONISTA','',NULL,0,NULL,'F0048216.PDF','FALLECIMIENTO','2010-11-10','2010-10-31',0,NULL,'','',NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(592,1,'08643758','VALDIVIA','HURTADO','JOSE','AGUSTIN',1,'1941-08-20 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'5791398',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2003-02-10 00:00:00',NULL,NULL,'CALLE 10 DE JUNIO 921 URB. MIGUEL GRAU S. MARTIN DE PORRES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,69,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'FALLECIDO','2010-07-30',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(603,1,'06618862','VALLADARES','NOBLECILLA','ROSA','NELLY',1,'1943-08-15 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'2522621',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-04-10 00:00:00',NULL,NULL,'JR, LIZANDRO DE LA PUENTE 383 URB SANTA LEONOR CHORRILLOS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,67,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'FALLECIDO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(621,1,'07436813','VARGAS','TUESTA','MARIA','BEATRIZ',2,'1952-08-08 00:00:00',1,0,'',1,0,0,'15','01','01','4205927','','',NULL,NULL,NULL,NULL,NULL,'1992-04-06 00:00:00',NULL,NULL,'','BENJAMIN DOIG LOSSIO','MZ D LT 10',NULL,NULL,'07','01','04',NULL,58,1,'PENSIONISTA','',NULL,0,NULL,'','SOBREGIROS','2010-12-29',NULL,0,NULL,'','',NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(622,1,'08845261','VARGAS','VILLAVERDE','VICTOR','MANUEL',1,'1900-01-04 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'2722471',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1988-12-10 00:00:00',NULL,NULL,'CALLE SIETE A LT.16 LA CALERA SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,110,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'FALLECIDO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(625,1,'10341599','VASQUEZ','GONZALES','DARIO','',1,'1931-01-26 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4498542',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1988-10-10 00:00:00',NULL,NULL,'CALLE ROBINSON  184  LA CALERA SURQUILLO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,79,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'FALLECIDO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(656,1,'09588486','VENERO','HERRERA','LOEL','ENRIQUE',1,'1949-07-13 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-02-18 00:00:00',NULL,NULL,'LIZARDO MONTERO # 453 SAN JUAN DE MIRAFLORES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,61,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(657,1,'06012190','VENTO','FRIAS','JUAN','JOSE',1,'1947-01-17 00:00:00',1,0,'',1,0,0,'','','','4589729','','',NULL,NULL,NULL,NULL,NULL,'1980-05-10 00:00:00',NULL,NULL,'JR.CUZCO 1071 LIMA','','',NULL,NULL,'','','',NULL,63,1,'PENSIONISTA','',NULL,0,NULL,'','SOBREGIROS','2010-07-30',NULL,0,NULL,'','',NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(664,1,'25482701','VERA','ZORILLA','ANA','MARIA',1,'1953-01-29 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1991-01-30 00:00:00',NULL,NULL,'JR. VIRREY TOLEDO  263 LA COLONIAL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,57,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(677,1,'08703656','VILCHEZ','ARNAO','RAFAEL','ANGEL',1,'1924-08-06 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'2626948',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1989-10-10 00:00:00',NULL,NULL,'AV. BERTOLOTO 210 DPTO. 105 SAN MIGUEL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,86,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'FALLECIDO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(680,1,'06717410','VILCHEZ','TAMARIZ','PEDRO','AMADEO',1,'1925-03-31 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'3300323',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1988-10-10 00:00:00',NULL,NULL,'LA HUACA 145 PUEBLO LIBRE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,85,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'FALLECIDO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(684,1,'31151378','VILLAFUERTE','CEBRIAN','BLASCO','',1,'1931-11-27 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4781482',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2001-07-10 00:00:00',NULL,NULL,'CALLE PENELOPE 146 URB. STA. ANITA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,79,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'FALLECIDO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(708,1,'08520613','YACTAYO','URRUTIA','MANUEL','EUGENIO',1,'1952-09-03 00:00:00',2,0,'',1,0,0,'','','','5686218','','RETIRADO POR SOBREGIRO DESCUENTO SOLO AEE.',NULL,NULL,NULL,NULL,NULL,'1979-06-07 00:00:00',NULL,NULL,'URB CAJA  DE AGUA ','JR TUMBES','400 ',NULL,NULL,'15','01','32',NULL,58,1,'PENSIONISTA','',NULL,0,NULL,'','SOBREGIROS','2010-12-29',NULL,0,NULL,'','RETIRADO POR SOBREGIRO DESCUENTO SOLO AEE.',NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(717,1,'08720916','ZAPATA','VALDIZAN CARNERO','MATILDE','',1,'1932-03-14 00:00:00',1,0,'',1,0,0,'','','','4610025','','',NULL,NULL,NULL,NULL,NULL,'2000-04-10 00:00:00',NULL,NULL,'CALLE GRAL JUAN JOSE SALAS 283 SAN MIGUELITO','','',NULL,NULL,'','','',NULL,78,1,'PENSIONISTA','',NULL,0,NULL,'','NO','2010-07-31',NULL,0,NULL,'','',NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(726,1,'08213604','ZEVALLOS','GALLARDO','EDDIE','MANUEL',1,'1934-10-21 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1998-01-10 00:00:00',NULL,NULL,'ALAMEDA CIRO ALEGRIA 118 TORRES DE SAN BORJA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,76,1,'PENSIONISTA',NULL,NULL,0,NULL,NULL,'VOLUNTARIO','2010-07-31',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(732,1,'29072597','ZUTTA','RUIZ','MIGUEL','ANGEL',1,'1934-06-05 00:00:00',2,0,'',1,0,0,'01','01','','4350616','','',NULL,NULL,NULL,NULL,NULL,'1993-04-05 00:00:00',NULL,NULL,'URB RECAUDADORES ','LOS PARACAS','424 ',NULL,NULL,'15','01','03',NULL,76,1,'PENSIONISTA','',NULL,0,NULL,'F0051225.PDF','FALLECIMIENTO','2010-07-30','2007-10-03',0,NULL,'','',NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(737,1,'06088049','BALCEDA','MONTALVO','JUAN','JOSE',1,'1948-08-03 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4317067',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-01-30 00:00:00',NULL,NULL,'JR. SUECIA 1434 URB. SAN RAFAEL CHACRA RIOS NO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,62,1,'19990',NULL,NULL,0,NULL,NULL,'FALLECIDO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(738,1,'25585456','BARRAGAN','ESCAJADILLO','EDUARDO','JOSE',1,'1955-03-20 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'2720533',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1992-01-17 00:00:00',NULL,NULL,'CALLE HECTOR VELARDE 436 DPTO. 401 CONJ.HAB. LA MERCED',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,55,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(739,1,'06083870','BOLAÐOS','ABRILES','ALBERTO','',1,'1930-12-20 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1991-12-20 00:00:00',NULL,NULL,'JORGE CHAVEZ 265 - BREÐA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,80,1,'19990',NULL,NULL,0,NULL,NULL,'FALLECIDO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(740,1,'16477363','BRIONES','ZAMORA','ELIDER','ABEL',1,'1955-01-11 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'074235748',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2003-09-25 00:00:00',NULL,NULL,'CALLE ELVIRA GARCIA MZ C LT 4  URB. F.SANTIAGO SALAVERRY CHICLAYO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,55,1,'19990',NULL,NULL,0,NULL,NULL,'NO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(743,1,'10283585','BUENDIA',' GIRIBALDI','JORGE','LUIS',1,'1954-09-15 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4246910',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-08-19 00:00:00',NULL,NULL,'CABO GUTARRA 362 DPTO 302 LIMA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,56,1,'19990',NULL,NULL,0,NULL,NULL,'NO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(749,1,'06760496','CASTAÐEDA','AHUMADA','RICKY','ESCARDO',1,'1965-03-25 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'96955131',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-10-22 00:00:00',NULL,NULL,'PROL. LUCANAS 960 DPTO 3 LA VICTORIA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,45,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(754,1,'10451486','CUMPA','ENRIQUEZ','CESAR','HUMBERTO',1,'1959-11-03 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'3812571',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-07-04 00:00:00',NULL,NULL,'LEON VELARDE 173 SAN MARTIN DE PORRES',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,51,1,'19990',NULL,NULL,0,NULL,NULL,'NO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(758,1,'07588132','ESPEJO','ACURIO','ANA','MARIA',1,'1946-02-01 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'2626068',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-09-06 00:00:00',NULL,NULL,'PLAZA PUNKURI CASTILLA 520 - G 404 - SAN MIGUE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,64,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(760,1,'07005172','FARIAS','ABURTO','JOSE','MANUEL',1,'1950-03-03 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'3349801',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2004-03-31 00:00:00',NULL,NULL,'JR. CHOTA 827 DPTO. 702 CERCADO DE LIMA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,60,1,'19990',NULL,NULL,0,NULL,NULL,'NO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(761,1,'07227875','FERNANDEZ','HERRERA','CARLOS','DAVID',1,'1959-06-10 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'2632414',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1994-03-23 00:00:00',NULL,NULL,'GRAL CORDOVA 1157-A PUEBLO LIBRE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,51,1,'19990',NULL,NULL,0,NULL,NULL,'NO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(766,1,'08118567','GERMAN','SANCHEZ','JOSE','EDUARDO',1,'1956-01-30 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4817292',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1996-09-24 00:00:00',NULL,NULL,'AV. FRANCISCO PIZARRO 717 DPTO. 12 RIMAC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,54,1,'19990',NULL,NULL,0,NULL,NULL,'FALLECIDO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(773,1,'08071922','JULCA','MEZA','VICTOR','RAUL',1,'1955-12-02 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4810286',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-10-11 00:00:00',NULL,NULL,'AV. RICARDO BENTIN 717 - 719 RIMAC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,55,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(774,1,'06598741','LANDA','ABAD','MIRTHA','MARLENI JESUS',1,'1956-01-02 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4379880',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2001-01-01 00:00:00',NULL,NULL,'PSJE LOPEZ GAMARRA 164 SALAMANCA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,54,1,'19990',NULL,NULL,0,NULL,NULL,'NO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(781,1,'06076652','LOPEZ','DIAZ','JUAN','',1,'1927-08-29 00:00:00',2,0,'',1,0,0,'07','01','02','4254782','','.',NULL,NULL,NULL,NULL,NULL,'1991-01-05 00:00:00',NULL,NULL,'URB ELIO','UNIDAD VECINAL MIRONES','49 DPTO. 420 ',NULL,NULL,'15','01','01',NULL,82,4,'EXTRABAJADOR','',NULL,10,NULL,'F0249009.PDF','FALLECIMIENTO','2010-07-15','2010-07-11',10,NULL,'','.',NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(790,1,'07744818','MONTOYA','MENDOZA','ASUNCION','',1,'1950-08-15 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'2610122',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2006-02-02 00:00:00',NULL,NULL,'JR. TARAPACA 457 MAGDALENA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,60,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(792,1,'07288840','MUÐOZ','HIPOLITO','LUIS','HERNESTO',1,'1944-08-25 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'3250508',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-09-15 00:00:00',NULL,NULL,'LOS QUECHUAS 607-INT 11 - LA VICTORIA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,66,1,'19990',NULL,NULL,0,NULL,NULL,'NO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(793,1,'07309703','MUÐOZ','HIPOLITO','MARIA','JULIA',1,'1952-02-20 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-08-31 00:00:00',NULL,NULL,'PROLONGACION UNANUE 1024 - LA VICTORIA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,58,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(798,1,'09307786','OCHOA','ARCOS','FLOR','DE MARIA',1,'1953-02-25 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4355848',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2004-09-16 00:00:00',NULL,NULL,'JR. LA TRINIDAD 295 URB VILLA JARDIN  SAN LUIS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,57,1,'19990',NULL,NULL,0,NULL,NULL,'NO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(800,1,'06707583','OWADA','RAMIREZ','MARIA','FORTUNATA',1,'1960-12-24 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4230821',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1989-06-25 00:00:00',NULL,NULL,'JR. JANGAS 507 BREÐA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(803,1,'08062142','PERALTA','MENDOZA','JOE','VLADIMIR',1,'1953-03-20 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'99674380',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2004-04-20 00:00:00',NULL,NULL,'CAJATAMBO 181 URB.EL MANZANO RIMAC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,57,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(808,1,'08714746','RAMIREZ','GALLEGOS','FLAVIO','',1,'1949-05-07 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'5787468',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-08-01 00:00:00',NULL,NULL,'CARLOS OQUENDO 148 LA ARBOLEDA SAN MIGUEL',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,61,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(810,1,'31668798','RAMIREZ','QUITO','GREGORIO','GUILLER',1,'1959-05-25 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'96202136',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-07-11 00:00:00',NULL,NULL,'AV. HUANDOY  MZ 13 LT 03',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,51,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(815,1,'07200267','SANCHEZ','CASTILLO','JUAN','',1,'1951-10-26 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'3305175',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-10-10 00:00:00',NULL,NULL,'JR. TAYACAJA 444 DPTO 407 LIMA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,59,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(820,1,'06658582','SINCE','GUIZADO','FERNANDO','',1,'1967-03-21 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4774437',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-04-01 00:00:00',NULL,NULL,'AV.EL SOL # 108 - BARRANCO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,43,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(821,1,'06654752','SINCE','GUIZADO','PEDRO','LUIS',1,'1964-04-09 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'4774437',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2005-04-01 00:00:00',NULL,NULL,'EL SOL   108 - BARRANCO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,46,1,'19990',NULL,NULL,0,NULL,NULL,'SOBREGIROS','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL),(822,1,'06131894','SOCOLA','CLAVIJO','ILDE','',1,'1956-01-23 00:00:00',1,0,NULL,1,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1990-06-20 00:00:00',NULL,NULL,'HUARAL 2424',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,54,1,'19990',NULL,NULL,0,NULL,NULL,'VOLUNTARIO','2011-02-02',NULL,0,NULL,NULL,NULL,NULL,0,NULL,NULL,'NO',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `f92_persona_baja` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: sacs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `f99_documentos`
--
-- WHERE:  1 LIMIT 50

/*!40000 ALTER TABLE `f99_documentos` DISABLE KEYS */;
INSERT INTO `f99_documentos` VALUES (1,'dasdas','bebe.jpg','2010-08-17','ELIMINADO',NULL),(2,'PRUEBA','bebe.jpg','2010-08-17','ELIMINADO',NULL),(3,'demo','cuadroMuestra.xls','2010-08-17','ELIMINADO',NULL),(4,'SOLICITUD  PRES','actualizarPrestamos.rtf','2010-08-17','INACTIVO',NULL),(5,'SOLUCITUD AEE  -  01 DE 06','SOLITUD AEE.PDF','2010-08-17','INACTIVO',2),(6,'AUTORIZACION DE DESCUENTO DE AEE  -  02 DE 06','AUT. AEE.PDF','2010-08-17','INACTIVO',2),(7,'PAGARE  -  03 DE 06','pagare.PDF','2010-08-17','INACTIVO',2),(8,'CARTA DE DESCUENTOS AEE  -  04 DE 06','CARTA DE DESCUENTOS.doc','2010-08-17','INACTIVO',NULL),(9,'SOLICITUD ADMISION AEE  -  05 DE 06','Admi._coop..PDF','2010-08-17','INACTIVO',NULL),(10,'DESCUENTO COOPERATIVA  -  06 DE 06','Auto._coop..PDF','2010-08-17','INACTIVO',NULL),(11,'CARTA DE PRELACION AEE - 04 DE 06','CARTA DE PRELACION.doc','2010-08-17','INACTIVO',2),(12,'ADMISION A COOP. - 04 DE 06','ADMISION A COOPERATIVA.PDF','2010-08-17','INACTIVO',NULL),(13,'ADMISION A COOP. - 05 DE 06','ADMISION A COOPERATIVA.PDF','2010-08-17','INACTIVO',1),(14,'AUTORIZACION DE DESCUENTO A COOP. - 06 DE 06','AUTORIZACION DESCTO COOPERATIVA.PDF','2010-08-17','INACTIVO',2),(15,'SOLICITUD AEE','SOLITUD AEE.PDF','2010-08-25','INACTIVO',1),(16,'SOLICITUD AEE','SOLITUD AEE.PDF','2010-08-25','ACTIVO',2);
/*!40000 ALTER TABLE `f99_documentos` ENABLE KEYS */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 14:48:23
