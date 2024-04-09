-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2024 at 09:48 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buono`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddNewReservation` (IN `customer_id` INT, IN `table_id` INT, IN `date_of_res` DATETIME, IN `num_of_guests` INT)   BEGIN
    -- Directly attempt to insert the new reservation.
    -- The database's own constraints (e.g., CHECK constraints) will enforce the business rules.
    INSERT INTO reservations (TableID, DateofRes, NumofGuests, CustomerID)
    VALUES (table_id, date_of_res, num_of_guests, customer_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CancelReservation` (IN `reservation_id` INT)   BEGIN
  DECLARE reservation_time DATETIME;
  
  SELECT DateofRes INTO reservation_time FROM reservations WHERE ID = reservation_id;
  
  IF TIMESTAMPDIFF(HOUR, NOW(), reservation_time) >= 2 THEN
    DELETE FROM reservations WHERE ID = reservation_id;
  END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CheckTableAvailability` (IN `reservation_datetime` DATETIME, IN `guest_count` INT)   BEGIN
   
    SELECT t.table_id, t.capacity
    FROM Tables t
    WHERE NOT EXISTS (
        SELECT 1
        FROM Reservations r
        WHERE r.table_id = t.table_id
          AND r.DateofRes = reservation_datetime
    )
    AND t.NumofSeats >= NumofGuests
    ORDER BY t.NumofSeats ASC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `ID` int(11) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Password` varchar(15) NOT NULL,
  `Fname` varchar(30) NOT NULL,
  `LName` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menuitems`
--

CREATE TABLE `menuitems` (
  `ID` int(11) NOT NULL,
  `DishName` varchar(30) NOT NULL,
  `Origin` varchar(30) NOT NULL,
  `Vegetarian` tinyint(1) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Image` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`ID`, `DishName`, `Origin`, `Vegetarian`, `Price`, `Image`) VALUES
(1, 'Tour of Italy', 'Italy', 0, 21.00, ''),
(2, 'Chicken and Shrimp Carbonara', 'Italy', 0, 21.00, ''),
(3, 'Paella', 'Spain', 0, 15.00, ''),
(4, 'Eggplant Parmigiana ', 'Italy', 1, 17.00, ''),
(5, 'Tapas', 'Spain', 0, 14.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `ID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `MenuID` int(11) NOT NULL,
  `Price` int(11) NOT NULL,
  `Quantity` int(10) NOT NULL,
  `Image` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `ID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `OrderDate` datetime NOT NULL,
  `Total Amount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `ID` int(11) NOT NULL,
  `TableID` int(11) NOT NULL,
  `DateofRes` datetime NOT NULL,
  `NumofGuests` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL
) ;

--
-- Triggers `reservations`
--
DELIMITER $$
CREATE TRIGGER `BeforeDeleteReservation` BEFORE DELETE ON `reservations` FOR EACH ROW BEGIN
  IF TIMESTAMPDIFF(HOUR, NOW(), OLD.DateofRes) < 2 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reservations cannot be cancelled within 2 hours of the reservation date.';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `BeforeInsertReservation` BEFORE INSERT ON `reservations` FOR EACH ROW BEGIN
    DECLARE reservation_count INT;
    
    SELECT COUNT(*) INTO reservation_count
    FROM reservationseservations
    WHERE DateofRes= NEW.DateofRes;
    
    IF reservation_count >= 10 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot make more than 10 reservations per day';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `BeforeUpdateReservation` BEFORE UPDATE ON `reservations` FOR EACH ROW BEGIN
    
    IF TIMESTAMPDIFF(HOUR, NOW(), OLD.DateofRes) < 24 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Reservations cannot be changed within 24 hours of the reservation date';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tablenumbers`
--

CREATE TABLE `tablenumbers` (
  `ID` int(11) NOT NULL,
  `NumofSeats` int(11) NOT NULL CHECK (`NumofSeats` >= 2 and `NumofSeats` <= 6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tablenumbers`
--

INSERT INTO `tablenumbers` (`ID`, `NumofSeats`) VALUES
(1, 2),
(2, 2),
(3, 4),
(4, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `menuitems`
--
ALTER TABLE `menuitems`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `MenuItem` (`MenuID`),
  ADD KEY `IndividualCustItem` (`CustomerID`) USING BTREE;

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CustomerOrder` (`CustomerID`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `CustomerRes` (`CustomerID`),
  ADD KEY `TableNumber` (`TableID`);

--
-- Indexes for table `tablenumbers`
--
ALTER TABLE `tablenumbers`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menuitems`
--
ALTER TABLE `menuitems`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tablenumbers`
--
ALTER TABLE `tablenumbers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`MenuID`) REFERENCES `menuitems` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `orderitems_ibfk_3` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`TableID`) REFERENCES `tablenumbers` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
