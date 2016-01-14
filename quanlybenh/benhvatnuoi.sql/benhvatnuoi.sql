-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2015 at 12:19 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `benhvatnuoi`
--

-- --------------------------------------------------------

--
-- Table structure for table `bvn_accounts`
--

CREATE TABLE IF NOT EXISTS `bvn_accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL,
  `fullname` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `phone` varchar(500) NOT NULL,
  `time_created` datetime NOT NULL,
  `active` int(11) NOT NULL,
  `protected` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bvn_accounts`
--

INSERT INTO `bvn_accounts` (`id`, `username`, `password`, `fullname`, `email`, `phone`, `time_created`, `active`, `protected`) VALUES
(1, 'admin', '70873e8580c9900986939611618d7b1e', 'Thắng', 'thangangle@yahoo.com', '+841686298448', '0000-00-00 00:00:00', 1, 1),
(2, 'zenthangplus', '70873e8580c9900986939611618d7b1e', 'Bùi Xuân Thắng', 'zenthangplus@gmail.com', '+84968698060', '2015-12-09 17:23:55', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `bvn_breeds`
--

CREATE TABLE IF NOT EXISTS `bvn_breeds` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` varchar(500) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bvn_breeds`
--

INSERT INTO `bvn_breeds` (`id`, `sid`, `name`, `description`, `thumbnail`) VALUES
(1, 7, 'Gà', '&lt;p&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Gà&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;(danh pháp hai phần: Gallus gallus, Gallus gallus domesticus) là một loài chim đã được con người thuần hoá cách đây hàng nghìn năm. Một số ý kiến cho&lt;/span&gt;&lt;wbr style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;...&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', '2015/12/ga.jpg'),
(4, 6, 'Trâu', '&lt;p&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Trâu&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;là một loài động vật thuộc họ&amp;nbsp;&lt;/span&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Trâu&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;bò (Bovidae). Chúng sống hoang dã ở Nam Á (Pakistan, Ấn Độ, Bangladesh, Nepal, Bhutan) Đông Nam Á, miền bắc Úc&lt;/span&gt;&lt;wbr style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;.&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', '2015/12/trau.jpg'),
(3, 6, 'Bò', '&lt;p&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Bò&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;nhà 2 hay&amp;nbsp;&lt;/span&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;bò&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;nuôi là loại động vật móng guốc được thuần hóa phổ biến nhất. Chúng là đại diện hiện đại nổi bật của cận họ Bovinae, và là loài phổ biến nhất&amp;nbsp;...&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', '2015/12/bo-1449121615.jpg'),
(5, 7, 'Vịt', '&lt;p&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Vịt&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;là tên gọi phổ thông cho một số loài thuộc họ&amp;nbsp;&lt;/span&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Vịt&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;(Anatidae), bộ Ngỗng (&lt;/span&gt;&lt;wbr style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Anseriformes). Các loài này được chia thành một số phân họ trong toàn bộ các phân&amp;nbsp;...&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', '2015/12/vit.jpg'),
(6, 6, 'Lợn', '&lt;p&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Lợn&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;nhà hay&amp;nbsp;&lt;/span&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;lợn&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;nuôi là một gia súc được thuần hóa, được chăn nuôi để cung cấp thịt. Hầu hết&amp;nbsp;&lt;/span&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;lợn&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;nhà có lớp lông mỏng trên bề mặt da.&amp;nbsp;&lt;/span&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Lợn&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;nhà thường được&lt;/span&gt;&lt;wbr style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;...&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', '2015/12/lon.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `bvn_diseases`
--

CREATE TABLE IF NOT EXISTS `bvn_diseases` (
  `id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `scientific_name` varchar(500) NOT NULL,
  `symptoms` text NOT NULL,
  `lesions` text NOT NULL,
  `description` text NOT NULL,
  `treatments` text NOT NULL,
  `prevention` text NOT NULL,
  `related` text NOT NULL,
  `thumbnail` varchar(500) NOT NULL,
  `video` varchar(500) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bvn_diseases`
--

INSERT INTO `bvn_diseases` (`id`, `gid`, `name`, `scientific_name`, `symptoms`, `lesions`, `description`, `treatments`, `prevention`, `related`, `thumbnail`, `video`) VALUES
(1, 3, 'Bệnh Gumboro', '', '', '', '&lt;p&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Bệnh gumboro&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;là một bệnh truyền nhiễm cấp tính nguy hiểm, gây thiệt hại rất lớn cho chăn nuôi gà, kể cả gà nuôi công nghiệp và gà chăn thả vườn. Bệnh&lt;/span&gt;&lt;br&gt;&lt;/p&gt;', '', '', '', '2015/12/benh-gumboro.jpg', ''),
(2, 3, 'Bệnh dịch tả', 'Bệnh Newcastle', 'Thời gian nung bệnh từ 3-4 ngày trong điều kiện thí nghiệm, 5-7 ngày có khi đến vài tuần trong điều kiện tự nhiên.', '- Viêm túi khí, viêm màng kết hợp mắt và viêm phế quản.\r\n- Khí quản bị viêm và xuất huyết. Viêm túi khí dày đục chứa casein.\r\n- Ruột có những vùng xuất huyết hay hoại tử định vị chủ yếu ở nơi tạo lympho thường ở hạch amydale manh tràng.\r\n-Thực quản, dạ dày tuyến, dạ dày cơ xuất huyết trên bề mặt', '&lt;p&gt;Gây ra bởi virus Paramyxovirus serotype 1 thuộc họ &lt;span style=&quot;font-weight: bold;&quot;&gt;Paramyxoviridae&lt;/span&gt;.&lt;br&gt;&lt;/p&gt;', 'Cách điều trị bệnh dịch tả ở gà', '- Đây là bệnh do virus nên không có thuốc đặc trị hữu hiệu. Phòng bệnh là biện pháp tốt nhất để dịch bệnh không xảy ra.\r\n- Chủng ngừa vaccin Newcastle theo đúng liệu trình.\r\n- Không mua gà bệnh từ nơi khác về để tránh lây lan.\r\n- Vệ sinh chuồng trại định kỳ bằng 1 trong 2 chế phẩm ANTIVIRUS-FMB hoặc PIVIDINE\r\n-Thường xuyên bổ sung vitamin ADE.B.Complex-C: 1 g/1lít nước uống nhằm tăng cường sức đề kháng, chống stress.', 'bệnh A, bệnh B, bệnh C', '2015/12/benh-dich-ta.jpg', 'https://www.youtube.com/watch?v=Bi2Q0iRTRv0');

-- --------------------------------------------------------

--
-- Table structure for table `bvn_diseases_group`
--

CREATE TABLE IF NOT EXISTS `bvn_diseases_group` (
  `id` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` varchar(500) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bvn_diseases_group`
--

INSERT INTO `bvn_diseases_group` (`id`, `bid`, `name`, `description`, `thumbnail`) VALUES
(2, 5, 'Bệnh ở Vịt', 'Nhóm bệnh ở vịt', '2015/12/benh-o-vit.jpg'),
(3, 1, 'Bệnh ở Gà', '&lt;p&gt;Nhóm bệnh ở Gà&lt;/p&gt;', '2015/12/benh-o-ga.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `bvn_species`
--

CREATE TABLE IF NOT EXISTS `bvn_species` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` varchar(500) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bvn_species`
--

INSERT INTO `bvn_species` (`id`, `name`, `description`, `thumbnail`) VALUES
(6, 'Gia súc', '                        &lt;p&gt;&lt;span style=&quot;font-weight: bold; color: rgb(106, 106, 106); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;Gia súc&lt;/span&gt;&lt;span style=&quot;color: rgb(84, 84, 84); font-family: arial, sans-serif; font-size: small; line-height: 18.2px;&quot;&gt;&amp;nbsp;là tên dùng để chỉ một hoặc nhiều loài động vật có vú được thuần hóa và nuôi vì mục đích để sản xuất hàng hóa như lấy thực phẩm, chất xơ hoặc lao&amp;nbsp;...&lt;/span&gt;&lt;br&gt;&lt;/p&gt;                    ', '2015/12/gia-suc-1449325694.jpg'),
(7, 'Gia cầm', '                                                                                                                                                                                                                                                                                                                        &lt;span xss=&quot;removed&quot; style=&quot;font-weight: bold;&quot;&gt;Gia cầm&lt;/span&gt;&lt;span xss=&quot;removed&quot;&gt;&amp;nbsp;là tên gọi chỉ chung cho các loài động vật có hai chân, có lông vũ, thuộc nhóm động vật có cánh được con người nuôi giữ, nhân giống nhằm mục đích&amp;nbsp;...&lt;/span&gt;                                                                                                                                                                                                                                                                    ', '2015/12/gia-cam-1449431591.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bvn_accounts`
--
ALTER TABLE `bvn_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bvn_breeds`
--
ALTER TABLE `bvn_breeds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bvn_diseases`
--
ALTER TABLE `bvn_diseases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bvn_diseases_group`
--
ALTER TABLE `bvn_diseases_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bvn_species`
--
ALTER TABLE `bvn_species`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bvn_accounts`
--
ALTER TABLE `bvn_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `bvn_breeds`
--
ALTER TABLE `bvn_breeds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `bvn_diseases`
--
ALTER TABLE `bvn_diseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `bvn_diseases_group`
--
ALTER TABLE `bvn_diseases_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `bvn_species`
--
ALTER TABLE `bvn_species`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
