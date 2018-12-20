-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.19 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for view nwind.order_summary_opt
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `order_summary_opt` (
	`OrderID` INT(11) NOT NULL,
	`CustomerID` VARCHAR(5) NULL COLLATE 'utf8_general_ci',
	`EmployeeID` INT(11) NULL,
	`OrderDate` DATETIME NULL,
	`RequiredDate` DATETIME NULL,
	`ShippedDate` DATETIME NULL,
	`ShipVia` INT(11) NULL,
	`ShipName` VARCHAR(40) NULL COLLATE 'utf8_general_ci',
	`ShipAddress` VARCHAR(60) NULL COLLATE 'utf8_general_ci',
	`ShipCity` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`ShipRegion` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`ShipPostalCode` VARCHAR(10) NULL COLLATE 'utf8_general_ci',
	`ShipCountry` VARCHAR(15) NULL COLLATE 'utf8_general_ci',
	`Shipping` VARCHAR(61) NULL COLLATE 'utf8mb4_general_ci',
	`TotalItems` BIGINT(21) NOT NULL,
	`TotalAmount` VARCHAR(103) NULL COLLATE 'utf8mb4_general_ci',
	`TotalDiscount` VARCHAR(63) NULL COLLATE 'utf8mb4_general_ci',
	`DiscountedAmount` VARCHAR(63) NULL COLLATE 'utf8mb4_general_ci',
	`GrandTotal` VARCHAR(63) NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=MyISAM;

-- Dumping structure for view nwind.order_summary_opt
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `order_summary_opt`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_summary_opt` AS SELECT
oo.OrderID as OrderID, 
oo.CustomerID as CustomerID, 
oo.EmployeeID as EmployeeID, 
oo.OrderDate as OrderDate, 
oo.RequiredDate as RequiredDate, 
oo.ShippedDate as ShippedDate, 
oo.ShipVia as ShipVia, 
oo.ShipName as ShipName, 
oo.ShipAddress as ShipAddress, 
oo.ShipCity as ShipCity, 
oo.ShipRegion as ShipRegion, 
oo.ShipPostalCode as ShipPostalCode, 
oo.ShipCountry as ShipCountry, 
format(oo.Freight,2) as Shipping,
COUNT(*) as TotalItems,
format(SUM(od.UnitPrice * od.Quantity),2) as TotalAmount,
format(SUM(od.UnitPrice * od.Quantity * od.Discount),2) as TotalDiscount,
format(SUM(od.UnitPrice * od.Quantity * (1 - od.Discount)),2) as DiscountedAmount,
format(SUM(od.UnitPrice * od.Quantity * (1 - od.Discount)) + oo.Freight,2) as GrandTotal
FROM orders oo
JOIN order_details od
	ON oo.OrderID = od.OrderID
WHERE 1 = 1
GROUP BY OrderID, CustomerID, EmployeeID, OrderDate, 
RequiredDate, ShippedDate, ShipVia, ShipName, ShipAddress, ShipCity, 
ShipRegion, ShipPostalCode, ShipCountry, Shipping ;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
