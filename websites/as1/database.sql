-- MySQL dump 10.13  Distrib 8.0.41, for Linux (x86_64)
--
-- Host: localhost    Database: assignment1
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auction`
--

DROP TABLE IF EXISTS `auction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auction` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `categoryId` int DEFAULT NULL,
  `endDate` datetime NOT NULL,
  `userId` int DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId`),
  KEY `userId` (`userId`),
  CONSTRAINT `auction_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `category` (`id`),
  CONSTRAINT `auction_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auction`
--

LOCK TABLES `auction` WRITE;
/*!40000 ALTER TABLE `auction` DISABLE KEYS */;
INSERT INTO `auction` VALUES (1,'Toyota','best car, only has been 2 years since i bought it . ',1,'2025-04-22 12:00:00',4,'images/auctions/67e545932dbfe_qr-code.png'),(5,'Bugati','I bought it a year ago. now i want to buy a SUV so I am selling it. Only has a 1000km.',17,'2025-04-30 06:49:00',5,'images/auctions/67f081cac1a1c_bugati.jpg'),(6,'Mercedes G  Wagon','The best luxury SUV car in the world. It has only been bought for 3 months. ',11,'2025-04-30 06:52:00',5,'images/auctions/67f08284a41ab_G wagon.jpg'),(7,'Hellcat','I bought it two years ago and still runs smoothly. Engine has been magneficient ',17,'2025-04-29 06:57:00',7,'images/auctions/67f0837e321ea_hellcat.jpg'),(8,'Mustang GT','.',17,'2025-05-05 07:20:00',7,'images/auctions/67f088cb33b2a_ford-mustang.jpg'),(9,'Mustang Lake Hartwell Model','.',13,'2025-05-14 07:31:00',7,'images/auctions/67f08b8c71119_mustang hartwell.jpg'),(10,'Rolls Roys Phantom',' ',11,'2025-05-23 09:06:00',7,'images/auctions/67f0a1d531a5d_rolls royce.jpg'),(11,'Tata nexon',' ',10,'2025-04-28 09:08:00',8,'images/auctions/67f0a24b56bbb_tata-nexon.jpg'),(12,'Ford Ecosport','Brown ford car, like new. Only has few hundred kilometers in it.',10,'2025-04-25 09:11:00',8,'images/auctions/67f0a2c371168_ford ecosport.jpg'),(13,'Hyundai Creta','Red Creta. New model, no scratches and dent.',10,'2025-06-19 09:12:00',8,'images/auctions/67f0a33a38471_Hyundai Creta.jpg'),(14,'Tesla s','red tesla model s, no problems in the car. ',15,'2025-04-22 09:14:00',8,'images/auctions/67f0a38b89b45_tesla model s.jpg');
/*!40000 ALTER TABLE `auction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bid`
--

DROP TABLE IF EXISTS `bid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bid` (
  `id` int NOT NULL AUTO_INCREMENT,
  `auctionId` int DEFAULT NULL,
  `userId` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auctionId` (`auctionId`),
  KEY `userId` (`userId`),
  CONSTRAINT `bid_ibfk_1` FOREIGN KEY (`auctionId`) REFERENCES `auction` (`id`),
  CONSTRAINT `bid_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bid`
--

LOCK TABLES `bid` WRITE;
/*!40000 ALTER TABLE `bid` DISABLE KEYS */;
INSERT INTO `bid` VALUES (1,1,5,1200.00),(2,1,5,4500.00),(3,1,5,7000.00),(6,6,7,200000.00);
/*!40000 ALTER TABLE `bid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'Estate'),(9,'Sedan'),(10,'SUV'),(11,'Luxury'),(12,'Coupe'),(13,'Convertible'),(14,'Hatchback'),(15,'Electric'),(16,'Pickup'),(17,'Sports'),(18,'Minivan'),(19,'Hybrid'),(21,'vintage');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `review` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reviewText` text NOT NULL,
  `userId` int DEFAULT NULL,
  `reviewedUserId` int DEFAULT NULL,
  `auctionId` int DEFAULT NULL,
  `datePosted` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `reviewedUserId` (`reviewedUserId`),
  KEY `auctionId` (`auctionId`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`),
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`reviewedUserId`) REFERENCES `user` (`id`),
  CONSTRAINT `review_ibfk_3` FOREIGN KEY (`auctionId`) REFERENCES `auction` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review`
--

LOCK TABLES `review` WRITE;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` VALUES (1,'very good\r\n',4,4,1,'2025-03-29 03:37:16'),(2,'Brother is rich',7,5,5,'2025-04-05 01:25:55'),(3,'post a image \r\n',7,4,1,'2025-04-05 01:26:40');
/*!40000 ALTER TABLE `review` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `isAdmin` tinyint DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin@example.com','$2y$10$a8FRqGFLrXlkNn8BdTNb7OWByo9O0sIepWYzJuBbHzsKWrPAqcNMy','admin',1),(2,'test@test.com','$2y$10$AjigM4teKXBTAYjwr9f4Vub4VhIPYsDabzjf0kJ0X4c.SWaW2SGLa','testuser',0),(4,'test@t.com','$2y$10$6OIL89iZ3LIctxfR66rGT.FVhQPIBY0mSY4yvkCZ4lEPkwyIhjyRy','usertest',0),(5,'ram@gmail.com','$2y$10$ru9lg7mq2JZqWV3FnmfvHOkr5RfKhlpF0MgAdY9vHnHziR.houAH6','Ram',0),(7,'test@gmail.com','$2y$10$9M8cG9i4EaoiVg2rcrD/O..R2pYfitrdYoiWGbLNDsXRwmyQS.MX.','Testuser',0),(8,'test2@gmail.com','$2y$10$GxguQHTjtS6SI3mU03d77ef9kNek4Iqj39q.aGL6vDKX9Fd1F.lJ.','testuser2',0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-05  3:42:00
