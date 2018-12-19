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

-- Dumping structure for view nwind.order_summary
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `order_summary` (
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
	`TotalItems` BIGINT(21) NULL,
	`TotalAmount` VARCHAR(103) NULL COLLATE 'utf8mb4_general_ci',
	`TotalDiscount` VARCHAR(63) NULL COLLATE 'utf8mb4_general_ci',
	`DiscountedAmount` VARCHAR(63) NULL COLLATE 'utf8mb4_general_ci',
	`GrandTotal` VARCHAR(63) NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=MyISAM;

-- Dumping structure for view nwind.order_summary
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `order_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_summary` AS select 
orders.OrderID as OrderID, 
orders.CustomerID as CustomerID, 
orders.EmployeeID as EmployeeID, 
orders.OrderDate as OrderDate, 
orders.RequiredDate as RequiredDate, 
orders.ShippedDate as ShippedDate, 
orders.ShipVia as ShipVia, 
orders.ShipName as ShipName, 
orders.ShipAddress as ShipAddress, 
orders.ShipCity as ShipCity, 
orders.ShipRegion as ShipRegion, 
orders.ShipPostalCode as ShipPostalCode, 
orders.ShipCountry as ShipCountry, 
format(orders.Freight,2) as Shipping,
(select count(*) from order_details where order_details.OrderID = orders.OrderID) as TotalItems,
format((select sum(order_details.UnitPrice * order_details.Quantity) from order_details where order_details.OrderID = orders.OrderID),2) as TotalAmount,
format((select sum(order_details.UnitPrice * order_details.Quantity * order_details.Discount) from order_details where order_details.OrderID = orders.OrderID),2) as TotalDiscount,
format((select sum(order_details.UnitPrice * order_details.Quantity * (1 - order_details.Discount)) from order_details where order_details.OrderID = orders.OrderID),2) as DiscountedAmount,
format((select sum(order_details.UnitPrice * order_details.Quantity * (1 - order_details.Discount)) from order_details where order_details.OrderID = orders.OrderID) + Freight,2) as GrandTotal
from orders ;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
