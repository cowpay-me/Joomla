--
-- Table structure for table `#__cowpay`
--
DROP TABLE IF EXISTS `#__cowpay`;

CREATE TABLE IF NOT EXISTS `#__cowpay` (
    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `status` varchar(64) NOT NULL,
    `method` varchar(255) NOT NULL,
    `card` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `phone` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `amount` int(11) NOT NULL DEFAULT '0',
    `transaction_id` varchar(255) NULL DEFAULT NULL,
    `reference_id` varchar(255) NULL DEFAULT NULL,
    `desc` text NULL DEFAULT NULL,
    `floor` varchar(255)  NULL DEFAULT NULL,
    `apartment` varchar(255) NULL DEFAULT NULL,
    `address` varchar(255) NULL DEFAULT NULL,
    `district` varchar(255) NULL DEFAULT NULL,
    `city_code` varchar(255) NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;