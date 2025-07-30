CREATE DATABASE  IF NOT EXISTS `coope` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `coope`;
-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: coope
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asamblea`
--

DROP TABLE IF EXISTS `asamblea`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asamblea` (
  `IdAsamblea` int(11) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Resultado` varchar(100) DEFAULT NULL,
  `Tipo` varchar(30) DEFAULT NULL,
  `Tema` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`IdAsamblea`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asamblea`
--

LOCK TABLES `asamblea` WRITE;
/*!40000 ALTER TABLE `asamblea` DISABLE KEYS */;
/*!40000 ALTER TABLE `asamblea` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contribucion`
--

DROP TABLE IF EXISTS `contribucion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contribucion` (
  `IdContri` int(11) NOT NULL,
  `CI` varchar(15) DEFAULT NULL,
  `FchTrabajo` date DEFAULT NULL,
  `HsTrabaj` int(11) DEFAULT NULL,
  `ValorContri` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`IdContri`),
  KEY `CI` (`CI`),
  CONSTRAINT `contribucion_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `miembro` (`CI`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contribucion`
--

LOCK TABLES `contribucion` WRITE;
/*!40000 ALTER TABLE `contribucion` DISABLE KEYS */;
/*!40000 ALTER TABLE `contribucion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `espaciocomun`
--

DROP TABLE IF EXISTS `espaciocomun`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `espaciocomun` (
  `IdEspacio` int(11) NOT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `UbiBloque` varchar(50) DEFAULT NULL,
  `Tipo` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`IdEspacio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `espaciocomun`
--

LOCK TABLES `espaciocomun` WRITE;
/*!40000 ALTER TABLE `espaciocomun` DISABLE KEYS */;
/*!40000 ALTER TABLE `espaciocomun` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `espaciocomunvivienda`
--

DROP TABLE IF EXISTS `espaciocomunvivienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `espaciocomunvivienda` (
  `IdEspacio` int(11) NOT NULL,
  `IdVivienda` int(11) NOT NULL,
  PRIMARY KEY (`IdEspacio`,`IdVivienda`),
  KEY `IdVivienda` (`IdVivienda`),
  CONSTRAINT `espaciocomunvivienda_ibfk_1` FOREIGN KEY (`IdEspacio`) REFERENCES `espaciocomun` (`IdEspacio`),
  CONSTRAINT `espaciocomunvivienda_ibfk_2` FOREIGN KEY (`IdVivienda`) REFERENCES `vivienda` (`IdVivienda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `espaciocomunvivienda`
--

LOCK TABLES `espaciocomunvivienda` WRITE;
/*!40000 ALTER TABLE `espaciocomunvivienda` DISABLE KEYS */;
/*!40000 ALTER TABLE `espaciocomunvivienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fondo`
--

DROP TABLE IF EXISTS `fondo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fondo` (
  `IdFondo` int(11) NOT NULL,
  `Tipo` varchar(50) DEFAULT NULL,
  `SaldoActual` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`IdFondo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fondo`
--

LOCK TABLES `fondo` WRITE;
/*!40000 ALTER TABLE `fondo` DISABLE KEYS */;
/*!40000 ALTER TABLE `fondo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gasto`
--

DROP TABLE IF EXISTS `gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gasto` (
  `IdGasto` int(11) NOT NULL,
  `IdFondo` int(11) DEFAULT NULL,
  `Fch` date DEFAULT NULL,
  `DescG` varchar(100) DEFAULT NULL,
  `MontoG` decimal(10,2) DEFAULT NULL,
  `Tipo` varchar(30) DEFAULT NULL,
  `Fijo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`IdGasto`),
  KEY `IdFondo` (`IdFondo`),
  CONSTRAINT `gasto_ibfk_1` FOREIGN KEY (`IdFondo`) REFERENCES `fondo` (`IdFondo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gasto`
--

LOCK TABLES `gasto` WRITE;
/*!40000 ALTER TABLE `gasto` DISABLE KEYS */;
/*!40000 ALTER TABLE `gasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `limpieza`
--

DROP TABLE IF EXISTS `limpieza`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `limpieza` (
  `IdLimpieza` int(11) NOT NULL,
  `IdEspacio` int(11) DEFAULT NULL,
  `Inicio` date DEFAULT NULL,
  `Fin` date DEFAULT NULL,
  `BloqEncarg` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IdLimpieza`),
  KEY `IdEspacio` (`IdEspacio`),
  CONSTRAINT `limpieza_ibfk_1` FOREIGN KEY (`IdEspacio`) REFERENCES `espaciocomun` (`IdEspacio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `limpieza`
--

LOCK TABLES `limpieza` WRITE;
/*!40000 ALTER TABLE `limpieza` DISABLE KEYS */;
/*!40000 ALTER TABLE `limpieza` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `miembro`
--

DROP TABLE IF EXISTS `miembro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `miembro` (
  `CI` varchar(15) NOT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `Apellido` varchar(50) DEFAULT NULL,
  `Edad` int(11) DEFAULT NULL,
  `Ingresos` decimal(10,2) DEFAULT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `FechIngre` date DEFAULT NULL,
  `Rol` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`CI`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `miembro`
--

LOCK TABLES `miembro` WRITE;
/*!40000 ALTER TABLE `miembro` DISABLE KEYS */;
/*!40000 ALTER TABLE `miembro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `miembroasamblea`
--

DROP TABLE IF EXISTS `miembroasamblea`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `miembroasamblea` (
  `CI` varchar(15) NOT NULL,
  `IdAsamblea` int(11) NOT NULL,
  `Asistio` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`CI`,`IdAsamblea`),
  KEY `IdAsamblea` (`IdAsamblea`),
  CONSTRAINT `miembroasamblea_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `miembro` (`CI`),
  CONSTRAINT `miembroasamblea_ibfk_2` FOREIGN KEY (`IdAsamblea`) REFERENCES `asamblea` (`IdAsamblea`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `miembroasamblea`
--

LOCK TABLES `miembroasamblea` WRITE;
/*!40000 ALTER TABLE `miembroasamblea` DISABLE KEYS */;
/*!40000 ALTER TABLE `miembroasamblea` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `miembrofondo`
--

DROP TABLE IF EXISTS `miembrofondo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `miembrofondo` (
  `CI` varchar(15) NOT NULL,
  `IdFondo` int(11) NOT NULL,
  `FchPago` date DEFAULT NULL,
  `MontoC` decimal(10,2) DEFAULT NULL,
  `Metodo` varchar(30) DEFAULT NULL,
  `EstadoPago` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`CI`,`IdFondo`),
  KEY `IdFondo` (`IdFondo`),
  CONSTRAINT `miembrofondo_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `miembro` (`CI`),
  CONSTRAINT `miembrofondo_ibfk_2` FOREIGN KEY (`IdFondo`) REFERENCES `fondo` (`IdFondo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `miembrofondo`
--

LOCK TABLES `miembrofondo` WRITE;
/*!40000 ALTER TABLE `miembrofondo` DISABLE KEYS */;
/*!40000 ALTER TABLE `miembrofondo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `miembrovivienda`
--

DROP TABLE IF EXISTS `miembrovivienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `miembrovivienda` (
  `CI` varchar(15) NOT NULL,
  `IdVivienda` int(11) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Criterio` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CI`,`IdVivienda`),
  KEY `IdVivienda` (`IdVivienda`),
  CONSTRAINT `miembrovivienda_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `miembro` (`CI`),
  CONSTRAINT `miembrovivienda_ibfk_2` FOREIGN KEY (`IdVivienda`) REFERENCES `vivienda` (`IdVivienda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `miembrovivienda`
--

LOCK TABLES `miembrovivienda` WRITE;
/*!40000 ALTER TABLE `miembrovivienda` DISABLE KEYS */;
/*!40000 ALTER TABLE `miembrovivienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subsidio`
--

DROP TABLE IF EXISTS `subsidio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subsidio` (
  `IdSub` int(11) NOT NULL,
  `IdFondo` int(11) DEFAULT NULL,
  `Tipo` varchar(30) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `MontoS` decimal(10,2) DEFAULT NULL,
  `Destino` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`IdSub`),
  KEY `IdFondo` (`IdFondo`),
  CONSTRAINT `subsidio_ibfk_1` FOREIGN KEY (`IdFondo`) REFERENCES `fondo` (`IdFondo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subsidio`
--

LOCK TABLES `subsidio` WRITE;
/*!40000 ALTER TABLE `subsidio` DISABLE KEYS */;
/*!40000 ALTER TABLE `subsidio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vivienda`
--

DROP TABLE IF EXISTS `vivienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vivienda` (
  `IdVivienda` int(11) NOT NULL,
  `UbicInterna` varchar(50) DEFAULT NULL,
  `NumPuerta` varchar(10) DEFAULT NULL,
  `Estado` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`IdVivienda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vivienda`
--

LOCK TABLES `vivienda` WRITE;
/*!40000 ALTER TABLE `vivienda` DISABLE KEYS */;
/*!40000 ALTER TABLE `vivienda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'coope'
--

--
-- Dumping routines for database 'coope'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-29 23:41:17
