-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2024 at 12:59 AM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddNewReservation` (IN `user_id` INT, IN `table_id` INT, IN `reservation_datetime` DATETIME, IN `party_size` INT)   BEGIN
    INSERT INTO reservations (CustomerID, TableID, DateofRes, NumofGuests)
    VALUES (user_id, table_id, reservation_datetime, party_size);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CancelReservation` (IN `reservation_id` INT)   BEGIN
  DECLARE reservation_time DATETIME;
  
  SELECT DateofRes INTO reservation_time FROM reservations WHERE ID = reservation_id;
  
  IF TIMESTAMPDIFF(HOUR, NOW(), reservation_time) >= 2 THEN
    DELETE FROM reservations WHERE ID = reservation_id;
  END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `CheckTableAvailability` (IN `reservation_datetime` DATETIME, IN `guest_count` INT)   BEGIN
   
    SELECT t.ID, t.NumofSeats
    FROM tablenumbers t
    WHERE NOT EXISTS (
        SELECT 1
        FROM Reservations r
        WHERE r.TableID = t.ID
          AND r.DateofRes = reservation_datetime
    )
    AND t.NumofSeats >= guest_count
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

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`ID`, `Email`, `Password`, `Fname`, `LName`) VALUES
(1, 'mariahstoakley@gmail.com', '$2y$10$KXC.Eevn', 'Mariah', '');

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
(1, 'Tour of Italy', 'Italy', 0, 21.00, 'TOI.jpg'),
(2, 'Chicken and Shrimp Carbonara', 'Italy', 0, 21.00, 'CSC.jpg'),
(3, 'Paella', 'Spain', 0, 15.00, 'Pan.jpg'),
(4, 'Eggplant Parmigiana ', 'Italy', 1, 17.00, 'EP.jpg'),
(5, 'Tapas', 'Spain', 0, 14.00, 'Tap.jpg'),
(6, 'Caramelized Onion Dip', 'United States', 1, 14.00, 'COD.jpg'),
(7, 'Creamy Cavatappi', 'Italy', 1, 12.30, 'CC.jpeg'),
(8, 'Cream of Cauliflower Soup', 'French', 1, 16.20, 'CCS.jpeg'),
(9, 'Vegetable Soup', 'Italy', 1, 16.00, 'VS.jpg'),
(10, 'Pumpkin Pancakes', 'United States', 1, 16.00, 'PP.jpg'),
(11, 'Masala dosa', 'India', 1, 9.00, 'MD.jpg'),
(12, 'Seafood paella', 'Spain', 0, 27.00, 'SP.jpg'),
(13, 'Som tam', 'Southeast Asia', 0, 16.00, 'ST.jpg'),
(14, 'Poutine', 'Canada', 0, 14.00, 'PT.jpg'),
(15, 'Stinky tofu', 'Southeast Asia', 1, 15.00, 'STA.jpg'),
(16, 'Chili crab', 'Southeast Asia', 0, 27.00, 'CC.jpg'),
(17, 'Fish ‘n’ chips', ' United Kingdom', 0, 12.00, 'FNC.jpg'),
(18, ' Bunny chow', 'Africa', 0, 16.00, 'BC.jpg'),
(19, 'Piri-piri chicken', 'Africa', 0, 15.00, 'PPC.jpg'),
(20, 'Peking duck', 'China', 0, 30.00, 'PD.jpg');

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

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`ID`, `CustomerID`, `OrderDate`, `Total Amount`) VALUES
(1, 1, '2024-04-16 00:59:04', 42);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`ID`, `TableID`, `DateofRes`, `NumofGuests`, `CustomerID`) VALUES
(1, 1, '2024-04-16 18:30:00', 2, 1);

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
    FROM reservations
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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menuitems`
--
ALTER TABLE `menuitems`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
