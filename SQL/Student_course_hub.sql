-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 08:49 AM
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
-- Database: `student_course_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `AdminID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`AdminID`, `Username`, `PasswordHash`) VALUES
(1, 'admin', '$2y$10$Lwypeplk5GwJFvbiIPneZuNt0jlUQL3T0KQ/qZRQ7t8y0vxogNRwi');

-- --------------------------------------------------------

--
-- Table structure for table `interestedstudents`
--

CREATE TABLE `interestedstudents` (
  `InterestID` int(11) NOT NULL,
  `ProgrammeID` int(11) NOT NULL,
  `StudentName` varchar(100) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Message` text DEFAULT NULL,
  `Status` enum('new','contacted','enrolled','unsubscribed') DEFAULT 'new',
  `RegisteredAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interestedstudents`
--

INSERT INTO `interestedstudents` (`InterestID`, `ProgrammeID`, `StudentName`, `Email`, `Phone`, `Message`, `Status`, `RegisteredAt`) VALUES
(1, 1, 'John Doe', 'john.doe@example.com', NULL, NULL, 'new', '2026-03-13 16:17:34'),
(2, 4, 'Jane Smith', 'jane.smith@example.com', NULL, NULL, 'new', '2026-03-13 16:17:34'),
(3, 6, 'Alex Brown', 'alex.brown@example.com', NULL, NULL, 'new', '2026-03-13 16:17:34'),
(4, 9, 'Priya Patel', 'priya.patel@example.com', NULL, NULL, 'new', '2026-03-13 16:17:34'),
(5, 3, 'Nabin Karki', 'karkinabin2001@gmail.com', NULL, NULL, 'new', '2026-03-18 17:36:05');

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `LevelID` int(11) NOT NULL,
  `LevelName` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`LevelID`, `LevelName`) VALUES
(1, 'Undergraduate'),
(2, 'Postgraduate');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `ModuleID` int(11) NOT NULL,
  `ModuleName` text NOT NULL,
  `ModuleLeaderID` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Credits` int(11) DEFAULT 15,
  `Image` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`ModuleID`, `ModuleName`, `ModuleLeaderID`, `Description`, `Credits`, `Image`) VALUES
(1, 'Introduction to Programming', 1, 'Covers the fundamentals of programming using Python and Java.', 15, NULL),
(2, 'Mathematics for Computer Science', 2, 'Teaches discrete mathematics, linear algebra, and probability theory.', 15, NULL),
(3, 'Computer Systems & Architecture', 3, 'Explores CPU design, memory management, and assembly language.', 15, NULL),
(4, 'Databases', 4, 'Covers SQL, relational database design, and NoSQL systems.', 15, NULL),
(5, 'Software Engineering', 5, 'Focuses on agile development, design patterns, and project management.', 15, NULL),
(6, 'Algorithms & Data Structures', 6, 'Examines sorting, searching, graphs, and complexity analysis.', 15, NULL),
(7, 'Cyber Security Fundamentals', 7, 'Provides an introduction to network security, cryptography, and vulnerabilities.', 15, NULL),
(8, 'Artificial Intelligence', 8, 'Introduces AI concepts such as neural networks, expert systems, and robotics.', 15, NULL),
(9, 'Machine Learning', 9, 'Explores supervised and unsupervised learning, including decision trees and clustering.', 15, NULL),
(10, 'Ethical Hacking', 10, 'Covers penetration testing, security assessments, and cybersecurity laws.', 15, NULL),
(11, 'Computer Networks', 1, 'Teaches TCP/IP, network layers, and wireless communication.', 15, NULL),
(12, 'Software Testing & Quality Assurance', 2, 'Focuses on automated testing, debugging, and code reliability.', 15, NULL),
(13, 'Embedded Systems', 3, 'Examines microcontrollers, real-time OS, and IoT applications.', 15, NULL),
(14, 'Human-Computer Interaction', 4, 'Studies UI/UX design, usability testing, and accessibility.', 15, NULL),
(15, 'Blockchain Technologies', 5, 'Covers distributed ledgers, consensus mechanisms, and smart contracts.', 15, NULL),
(16, 'Cloud Computing', 6, 'Introduces cloud services, virtualization, and distributed systems.', 15, NULL),
(17, 'Digital Forensics', 7, 'Teaches forensic investigation techniques for cybercrime.', 15, NULL),
(18, 'Final Year Project', 8, 'A major independent project where students develop a software solution.', 15, NULL),
(19, 'Advanced Machine Learning', 11, 'Covers deep learning, reinforcement learning, and cutting-edge AI techniques.', 15, NULL),
(20, 'Cyber Threat Intelligence', 12, 'Focuses on cybersecurity risk analysis, malware detection, and threat mitigation.', 15, NULL),
(21, 'Big Data Analytics', 13, 'Explores data mining, distributed computing, and AI-driven insights.', 15, NULL),
(22, 'Cloud & Edge Computing', 14, 'Examines scalable cloud platforms, serverless computing, and edge networks.', 15, NULL),
(23, 'Blockchain & Cryptography', 15, 'Covers decentralized applications, consensus algorithms, and security measures.', 15, NULL),
(24, 'AI Ethics & Society', 16, 'Analyzes ethical dilemmas in AI, fairness, bias, and regulatory considerations.', 15, NULL),
(25, 'Quantum Computing', 17, 'Introduces quantum algorithms, qubits, and cryptographic applications.', 15, NULL),
(26, 'Cybersecurity Law & Policy', 18, 'Explores digital privacy, GDPR, and international cyber law.', 15, NULL),
(27, 'Neural Networks & Deep Learning', 19, 'Delves into convolutional networks, GANs, and AI advancements.', 15, NULL),
(28, 'Human-AI Interaction', 20, 'Studies AI usability, NLP systems, and social robotics.', 15, NULL),
(29, 'Autonomous Systems', 11, 'Focuses on self-driving technology, robotics, and intelligent agents.', 15, NULL),
(30, 'Digital Forensics & Incident Response', 12, 'Teaches forensic analysis, evidence gathering, and threat mitigation.', 15, NULL),
(31, 'Postgraduate Dissertation', 13, 'A major research project where students explore advanced topics in computing.', 15, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `programmemodules`
--

CREATE TABLE `programmemodules` (
  `ProgrammeModuleID` int(11) NOT NULL,
  `ProgrammeID` int(11) DEFAULT NULL,
  `ModuleID` int(11) DEFAULT NULL,
  `Year` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programmemodules`
--

INSERT INTO `programmemodules` (`ProgrammeModuleID`, `ProgrammeID`, `ModuleID`, `Year`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 2, 1, 1),
(6, 2, 2, 1),
(7, 2, 3, 1),
(8, 2, 4, 1),
(9, 3, 1, 1),
(10, 3, 2, 1),
(11, 3, 3, 1),
(12, 3, 4, 1),
(13, 4, 1, 1),
(14, 4, 2, 1),
(15, 4, 3, 1),
(16, 4, 4, 1),
(17, 5, 1, 1),
(18, 5, 2, 1),
(19, 5, 3, 1),
(20, 5, 4, 1),
(21, 1, 5, 2),
(22, 1, 6, 2),
(23, 1, 7, 2),
(24, 1, 8, 2),
(25, 2, 5, 2),
(26, 2, 6, 2),
(27, 2, 12, 2),
(28, 2, 14, 2),
(29, 3, 5, 2),
(30, 3, 9, 2),
(31, 3, 8, 2),
(32, 3, 10, 2),
(33, 4, 7, 2),
(34, 4, 10, 2),
(35, 4, 11, 2),
(36, 4, 17, 2),
(37, 5, 5, 2),
(38, 5, 6, 2),
(39, 5, 9, 2),
(40, 5, 16, 2),
(41, 1, 11, 3),
(42, 1, 13, 3),
(43, 1, 15, 3),
(44, 1, 18, 3),
(45, 2, 13, 3),
(46, 2, 15, 3),
(47, 2, 16, 3),
(48, 2, 18, 3),
(49, 3, 13, 3),
(50, 3, 15, 3),
(51, 3, 16, 3),
(52, 3, 18, 3),
(53, 4, 15, 3),
(54, 4, 16, 3),
(55, 4, 17, 3),
(56, 4, 18, 3),
(57, 5, 9, 3),
(58, 5, 14, 3),
(59, 5, 16, 3),
(60, 5, 18, 3),
(61, 6, 19, 1),
(62, 6, 24, 1),
(63, 6, 27, 1),
(64, 6, 29, 1),
(65, 6, 31, 1),
(66, 7, 20, 1),
(67, 7, 26, 1),
(68, 7, 30, 1),
(69, 7, 23, 1),
(70, 7, 31, 1),
(71, 8, 21, 1),
(72, 8, 22, 1),
(73, 8, 27, 1),
(74, 8, 28, 1),
(75, 8, 31, 1),
(76, 9, 19, 1),
(77, 9, 24, 1),
(78, 9, 28, 1),
(79, 9, 29, 1),
(80, 9, 31, 1),
(81, 10, 23, 1),
(82, 10, 22, 1),
(83, 10, 25, 1),
(84, 10, 26, 1),
(85, 10, 31, 1);

-- --------------------------------------------------------

--
-- Table structure for table `programmes`
--

CREATE TABLE `programmes` (
  `ProgrammeID` int(11) NOT NULL,
  `ProgrammeName` text NOT NULL,
  `LevelID` int(11) DEFAULT NULL,
  `ProgrammeLeaderID` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Duration` varchar(50) DEFAULT NULL,
  `Fees` decimal(10,2) DEFAULT NULL,
  `Image` text DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programmes`
--

INSERT INTO `programmes` (`ProgrammeID`, `ProgrammeName`, `LevelID`, `ProgrammeLeaderID`, `Description`, `Duration`, `Fees`, `Image`, `is_published`) VALUES
(1, 'BSc Computer Science', 1, 1, 'A broad computer science degree covering programming, AI, cybersecurity, and software engineering.', NULL, NULL, NULL, 1),
(2, 'BSc Software Engineering', 1, 2, 'A specialized degree focusing on the development and lifecycle of software applications.', NULL, NULL, NULL, 1),
(3, 'BSc Artificial Intelligence', 1, 3, 'Focuses on machine learning, deep learning, and AI applications.', NULL, NULL, NULL, 0),
(4, 'BSc Cyber Security', 1, 4, 'Explores network security, ethical hacking, and digital forensics.', NULL, NULL, NULL, 1),
(5, 'BSc Data Science', 1, 5, 'Covers big data, machine learning, and statistical computing.', NULL, NULL, NULL, 1),
(6, 'MSc Machine Learning', 2, 11, 'A postgraduate degree focusing on deep learning, AI ethics, and neural networks.', NULL, NULL, NULL, 1),
(7, 'MSc Cyber Security', 2, 12, 'A specialized programme covering digital forensics, cyber threat intelligence, and security policy.', NULL, NULL, NULL, 1),
(8, 'MSc Data Science', 2, 13, 'Focuses on big data analytics, cloud computing, and AI-driven insights.', NULL, NULL, NULL, 1),
(9, 'MSc Artificial Intelligence', 2, 14, 'Explores autonomous systems, AI ethics, and deep learning technologies.', NULL, NULL, NULL, 1),
(10, 'MSc Software Engineering', 2, 15, 'Emphasizes software design, blockchain applications, and cutting-edge methodologies.', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `StaffID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `Bio` text DEFAULT NULL,
  `ProfileImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`StaffID`, `Name`, `Email`, `Department`, `Bio`, `ProfileImage`) VALUES
(1, 'Dr. Alice Johnson', NULL, NULL, NULL, NULL),
(2, 'Dr. Brian Lee', NULL, NULL, NULL, NULL),
(3, 'Dr. Carol White', NULL, NULL, NULL, NULL),
(4, 'Dr. David Green', NULL, NULL, NULL, NULL),
(5, 'Dr. Emma Scott', NULL, NULL, NULL, NULL),
(6, 'Dr. Frank Moore', NULL, NULL, NULL, NULL),
(7, 'Dr. Grace Adams', NULL, NULL, NULL, NULL),
(8, 'Dr. Henry Clark', NULL, NULL, NULL, NULL),
(9, 'Dr. Irene Hall', NULL, NULL, NULL, NULL),
(10, 'Dr. James Wright', NULL, NULL, NULL, NULL),
(11, 'Dr. Sophia Miller', NULL, NULL, NULL, NULL),
(12, 'Dr. Benjamin Carter', NULL, NULL, NULL, NULL),
(13, 'Dr. Chloe Thompson', NULL, NULL, NULL, NULL),
(14, 'Dr. Daniel Robinson', NULL, NULL, NULL, NULL),
(15, 'Dr. Emily Davis', NULL, NULL, NULL, NULL),
(16, 'Dr. Nathan Hughes', NULL, NULL, NULL, NULL),
(17, 'Dr. Olivia Martin', NULL, NULL, NULL, NULL),
(18, 'Dr. Samuel Anderson', NULL, NULL, NULL, NULL),
(19, 'Dr. Victoria Hall', NULL, NULL, NULL, NULL),
(20, 'Dr. William Scott', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `interestedstudents`
--
ALTER TABLE `interestedstudents`
  ADD PRIMARY KEY (`InterestID`),
  ADD KEY `ProgrammeID` (`ProgrammeID`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`LevelID`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`ModuleID`),
  ADD KEY `ModuleLeaderID` (`ModuleLeaderID`);

--
-- Indexes for table `programmemodules`
--
ALTER TABLE `programmemodules`
  ADD PRIMARY KEY (`ProgrammeModuleID`),
  ADD KEY `ProgrammeID` (`ProgrammeID`),
  ADD KEY `ModuleID` (`ModuleID`);

--
-- Indexes for table `programmes`
--
ALTER TABLE `programmes`
  ADD PRIMARY KEY (`ProgrammeID`),
  ADD KEY `LevelID` (`LevelID`),
  ADD KEY `ProgrammeLeaderID` (`ProgrammeLeaderID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`StaffID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `interestedstudents`
--
ALTER TABLE `interestedstudents`
  MODIFY `InterestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `programmemodules`
--
ALTER TABLE `programmemodules`
  MODIFY `ProgrammeModuleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `programmes`
--
ALTER TABLE `programmes`
  MODIFY `ProgrammeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `interestedstudents`
--
ALTER TABLE `interestedstudents`
  ADD CONSTRAINT `interestedstudents_ibfk_1` FOREIGN KEY (`ProgrammeID`) REFERENCES `programmes` (`ProgrammeID`) ON DELETE CASCADE;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`ModuleLeaderID`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `programmemodules`
--
ALTER TABLE `programmemodules`
  ADD CONSTRAINT `programmemodules_ibfk_1` FOREIGN KEY (`ProgrammeID`) REFERENCES `programmes` (`ProgrammeID`),
  ADD CONSTRAINT `programmemodules_ibfk_2` FOREIGN KEY (`ModuleID`) REFERENCES `modules` (`ModuleID`);

--
-- Constraints for table `programmes`
--
ALTER TABLE `programmes`
  ADD CONSTRAINT `programmes_ibfk_1` FOREIGN KEY (`LevelID`) REFERENCES `levels` (`LevelID`),
  ADD CONSTRAINT `programmes_ibfk_2` FOREIGN KEY (`ProgrammeLeaderID`) REFERENCES `staff` (`StaffID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

