<meta charset="utf-8">
<?php
  error_reporting(-1);
	require_once 'config.php';

	$host = DB_HOSTNAME;
	$user = DB_USERNAME;
	$pass = DB_PASSWORD;
	$dbname = DB_DATABASE;
	$pr = DB_PREFIX;

	try {  
		$dbh = new PDO("mysql:host=".$host.";dbname=".$dbname, $user, $pass);  
		$dbh->exec('SET NAMES utf8');
    $dbh->exec("SET SQL_MODE = ''"); 
		}  
	catch(PDOException $e) {  
		echo $e->getMessage();  
		}

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_createcsv` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `dn_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `param_id` varchar(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_param` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `dn_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `start` varchar(256) NOT NULL,
  `stop` varchar(256) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `base_id` int(11) NOT NULL DEFAULT '0',
  `delim` varchar(256) NOT NULL DEFAULT ';'
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_prsetup` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `dn_id` int(11) NOT NULL,
  `model` text NOT NULL,
  `sku` text NOT NULL,
  `name` text NOT NULL,
  `price` text NOT NULL,
  `quant` text NOT NULL,
  `quant_d` int(11) NOT NULL DEFAULT '101',
  `des` text NOT NULL,
  `des_d` text NOT NULL,
  `manufac` text NOT NULL,
  `manufac_d` int(11) NOT NULL DEFAULT '0',
  `cat` text NOT NULL,
  `cat_d` int(11) NOT NULL,
  `img` text NOT NULL,
  `img_d` text NOT NULL,
  `img_dir` varchar(100) NOT NULL DEFAULT 'product',
  `attr` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_replace` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `dn_id` int(11) NOT NULL,
  `param_id` int(11) NOT NULL,
  `text_start` text NOT NULL,
  `text_stop` text NOT NULL,
  `rules` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();


$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dn_id` int(3) NOT NULL,
  `link` text NOT NULL,
  `scan` int(11) NOT NULL DEFAULT '1',
  `scan_cron` int(11) NOT NULL DEFAULT '1',
  `key_md5` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_link` (`key_md5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_sen_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dn_id` int(3) NOT NULL,
  `link` text NOT NULL,
  `scan` int(11) NOT NULL DEFAULT '1',
  `scan_cron` int(11) NOT NULL DEFAULT '1',
  `key_md5` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_sen_link` (`key_md5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_link` ADD `scan_cron` int(11) NOT NULL DEFAULT '1' AFTER `scan`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_sen_link` ADD `scan_cron` int(11) NOT NULL DEFAULT '1' AFTER `scan`");
$stmt->execute();

$stmt = $dbh->prepare("SHOW TABLES LIKE '".$pr."manufacturer_description'");
$stmt->execute();
$vers_op = $stmt->fetch(PDO::FETCH_ASSOC);

$vers = '';
if(empty($vers_op)){
  $vers = 'opencart';
}else{
  $vers = 'ocstore';
}

$stmt = $dbh->prepare("SHOW TABLES LIKE '".$pr."url_alias'");
$stmt->execute();
$vers_op_u = $stmt->fetch(PDO::FETCH_ASSOC);

if(empty($vers_op_u)){
  $v_num = 3;
}else{
  $v_num = 2;
}

$vers = $vers.$v_num;

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_setting` (
  `dn_id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `pre_view_param` int(1) NOT NULL DEFAULT '1',
  `dn_name` varchar(256) NOT NULL,
  `start_link` text NOT NULL,
  `pars_stop` int(1) NOT NULL,
  `csv_name` varchar(256) NOT NULL,
  `csv_delim` varchar(10) NOT NULL DEFAULT ';',
  `csv_escape` varchar(10) NOT NULL DEFAULT '&quot;',
  `csv_charset` int(2) NOT NULL DEFAULT '1',
  `pars_pause` varchar(11) NOT NULL DEFAULT '0',
  `filter_round_yes` text NOT NULL,
  `filter_round_method` varchar(4) NOT NULL DEFAULT 'or',
  `filter_round_no` text NOT NULL,
  `filter_link_yes` text NOT NULL,
  `filter_link_no` text NOT NULL,
  `filter_link_method` varchar(4) NOT NULL DEFAULT 'or',
  `action` int(11) NOT NULL DEFAULT '3',
  `sid` text NOT NULL,
  `r_model` int(11) NOT NULL DEFAULT '1',
  `r_sku` int(11) NOT NULL DEFAULT '0',
  `r_name` int(11) NOT NULL DEFAULT '0',
  `r_price` int(11) NOT NULL DEFAULT '1',
  `r_quant` int(11) NOT NULL DEFAULT '0',
  `r_manufac` int(11) NOT NULL DEFAULT '1',
  `r_des` int(11) NOT NULL DEFAULT '0',
  `r_cat` int(11) NOT NULL DEFAULT '1',
  `r_img` int(11) NOT NULL DEFAULT '1',
  `r_img_dir` int(11) NOT NULL DEFAULT '0',
  `r_attr` int(11) NOT NULL DEFAULT '0',
  `vers_op` varchar(15) NOT NULL DEFAULT '".$vers."'
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

//Доп запросы.
$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` CHANGE `pars_pause` `pars_pause` VARCHAR(11) NOT NULL DEFAULT '0'");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_param` 
  ADD `with_teg` INT(1) NOT NULL DEFAULT '1' AFTER `type`, 
  ADD `skip_enter` VARCHAR(14) NOT NULL DEFAULT '0' AFTER `with_teg`, 
  ADD `skip_where` INT(1) NOT NULL DEFAULT '1' AFTER `skip_enter`, 
  ADD `reverse` INT(1) NOT NULL DEFAULT '0' AFTER `skip_where`
");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_createcsv` CHANGE `param_id` `value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_replace` ADD `hash` VARCHAR(5) NOT NULL DEFAULT '0' AFTER `rules`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `csv_charset` INT(2) NOT NULL DEFAULT '1' AFTER `csv_escape`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `vers_op` VARCHAR(10) NOT NULL DEFAULT 'opencart' AFTER `r_attr`");
$stmt->execute();

$stmt = $dbh->prepare("UPDATE `".$pr."pars_setting` SET `vers_op`='".$vers."'");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_replace` ADD `arithm` VARCHAR(256) NOT NULL AFTER `hash`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_prsetup` ADD `seo_url` TEXT NOT NULL AFTER `attr`, ADD `seo_h1` TEXT NOT NULL AFTER `seo_url`, ADD `seo_title` TEXT NOT NULL AFTER `seo_h1`, ADD `seo_desc` TEXT NOT NULL AFTER `seo_title`, ADD `seo_keyw` TEXT NOT NULL AFTER `seo_desc`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_made_url` INT(2) NOT NULL DEFAULT '1' AFTER `r_name`, ADD `r_made_meta` INT(2) NOT NULL DEFAULT '0' AFTER `r_made_url`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_cat_made_url` INT(2) NOT NULL DEFAULT '1' AFTER `r_cat`, ADD `r_cat_made_meta` INT(2) NOT NULL DEFAULT '0' AFTER `r_cat_made_url`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_prsetup` ADD `cat_seo_url` TEXT NOT NULL AFTER `seo_keyw`, ADD `cat_seo_h1` TEXT NOT NULL AFTER `cat_seo_url`, ADD `cat_seo_title` TEXT NOT NULL AFTER `cat_seo_h1`, ADD `cat_seo_desc` TEXT NOT NULL AFTER `cat_seo_title`, ADD `cat_seo_keyw` TEXT NOT NULL AFTER `cat_seo_desc`, ADD `manuf_seo_url` TEXT NOT NULL AFTER `cat_seo_keyw`, ADD `manuf_seo_h1` TEXT NOT NULL AFTER `manuf_seo_url`, ADD `manuf_seo_title` TEXT NOT NULL AFTER `manuf_seo_h1`, ADD `manuf_seo_desc` TEXT NOT NULL AFTER `manuf_seo_title`, ADD `manuf_seo_keyw` TEXT NOT NULL AFTER `manuf_seo_desc`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_manufac_made_url` INT NOT NULL DEFAULT '1' AFTER `r_manufac`, ADD `r_manufac_made_meta` INT NOT NULL DEFAULT '0' AFTER `r_manufac_made_url`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_cat_perent` INT(1) NOT NULL DEFAULT '0' AFTER `r_cat`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_attr_group` INT(1) NOT NULL DEFAULT '1' AFTER `r_attr`");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `hash` varchar(1000) NOT NULL,
    `key_lic` varchar(255) NOT NULL,
    `mod_ver` int(4) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("INSERT INTO ".$pr."pars SET id=1, hash='aa2d6e4f578eb0cfaba23beef76c2194', key_lic='free', mod_ver='1'");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars` CHANGE `mod_ver` `mod_ver` VARCHAR(256) NOT NULL DEFAULT '1'");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars` ADD `date` VARCHAR(256) NOT NULL AFTER `mod_ver`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `page_cou_link` INT NOT NULL DEFAULT '5000' AFTER `start_link`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_store` VARCHAR(256) NOT NULL AFTER `sid`, ADD `r_lang` VARCHAR(256) NOT NULL AFTER `r_store`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_prsetup` ADD `price_spec` TEXT NOT NULL AFTER `price`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_price_spec_groups` VARCHAR(256) NOT NULL DEFAULT 'all' AFTER `r_price`, ADD `r_price_spec_date_start` VARCHAR(256) NOT NULL AFTER `r_price_spec_groups`, ADD `r_price_spec_date_end` VARCHAR(256) NOT NULL AFTER `r_price_spec_date_start`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` CHANGE `r_store` `r_store` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` CHANGE `r_lang` `r_lang` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_status_zero` INT NOT NULL DEFAULT '5' AFTER `r_quant`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_product_status` INT NOT NULL DEFAULT '1' AFTER `r_quant`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_opt` INT NOT NULL DEFAULT '0' AFTER `r_attr`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_prsetup` ADD `opt_name` VARCHAR(2000) NOT NULL AFTER `attr`, ADD `opt_value` VARCHAR(2000) NOT NULL AFTER `opt_name`, ADD `opt_price` VARCHAR(2000) NOT NULL AFTER `opt_value`, ADD `opt_quant` VARCHAR(2000) NOT NULL AFTER `opt_price`, ADD `opt_data` VARCHAR(2000) NOT NULL AFTER `opt_quant`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_prsetup` ADD `opt_data` VARCHAR(2000) NOT NULL AFTER `opt_quant`");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_browser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dn_id` int(11) NOT NULL,
  `proxy_use` int(11) NOT NULL DEFAULT '0',
  `timeout` int(11) NOT NULL DEFAULT '15',
  `connect_timeout` int(11) NOT NULL DEFAULT '10',
  `header_get` int(11) NOT NULL DEFAULT '0',
  `followlocation` int(11) NOT NULL DEFAULT '1',
  `cookie_use` int(11) NOT NULL DEFAULT '0',
  `cookie_session` int(11) NOT NULL DEFAULT '1',
  `user_agent_use` int(11) NOT NULL DEFAULT '1',
  `user_agent_change` int(11) NOT NULL DEFAULT '0',
  `user_agent_list` text NOT NULL,
  `header_use` int(11) NOT NULL DEFAULT '0',
  `header_change` int(11) NOT NULL DEFAULT '0',
  `header_list` text NOT NULL,
  `cache_page` int(11) NOT NULL DEFAULT '1',
  `ch_connect_timeout` int(5) NOT NULL DEFAULT '10',
  `ch_timeout` int(5) NOT NULL DEFAULT '10',
  `ch_url` text NOT NULL,
  `ch_pattern` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();


$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_proxy_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dn_id` int(11) NOT NULL,
  `proxy` varchar(256) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `proxy_index` (`proxy`,`dn_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `pre_view_syntax` INT(1) NOT NULL DEFAULT '1' AFTER `pre_view_param`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `filter_round_param` TEXT NOT NULL AFTER `filter_round_method`, ADD `filter_round_depth` VARCHAR(255) NOT NULL AFTER `filter_round_param`, ADD `filter_round_slash` INT(1) NOT NULL DEFAULT '0' AFTER `filter_round_depth`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `filter_link_param` TEXT NOT NULL AFTER `filter_link_method`, ADD `filter_link_depth` VARCHAR(255) NOT NULL AFTER `filter_link_param`, ADD `filter_link_slash` INT(1) NOT NULL DEFAULT '0' AFTER `filter_link_depth`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `filter_round_domain` INT(1) NOT NULL DEFAULT '1' AFTER `filter_round_slash`, ADD `filter_link_domain` INT(1) NOT NULL DEFAULT '1' AFTER `filter_round_domain`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `logs_reverse` INT(1) NOT NULL DEFAULT '0' AFTER `r_attr_group`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `logs_mb` INT(3) NOT NULL DEFAULT '25' AFTER `logs_reverse`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_prsetup` ADD `opt_quant_d` varchar(2000) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '10' AFTER `opt_quant`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_prsetup` 
ADD `upc` VARCHAR(255) NOT NULL AFTER `opt_data`, 
ADD `ean` VARCHAR(255) NOT NULL AFTER `upc`, 
ADD `jan` VARCHAR(255) NOT NULL AFTER `ean`, 
ADD `isbn` VARCHAR(255) NOT NULL AFTER `jan`, 
ADD `mpn` VARCHAR(255) NOT NULL AFTER `isbn`, 
ADD `location` VARCHAR(255) NOT NULL AFTER `mpn`, 
ADD `minimum` VARCHAR(255) NOT NULL DEFAULT '1' AFTER `location`, 
ADD `subtract` VARCHAR(255) NOT NULL DEFAULT '1' AFTER `minimum`, 
ADD `length` VARCHAR(255) NOT NULL DEFAULT '0.00' AFTER `subtract`, 
ADD `width` VARCHAR(255) NOT NULL DEFAULT '0.00' AFTER `length`, 
ADD `height` VARCHAR(255) NOT NULL DEFAULT '0.00' AFTER `width`, 
ADD `length_class_id` VARCHAR(255) NOT NULL DEFAULT '1' AFTER `height`, 
ADD `weight` VARCHAR(255) NOT NULL AFTER `length_class_id`, 
ADD `weight_class_id` VARCHAR(255) NOT NULL DEFAULT '1' AFTER `weight`, 
ADD `status` VARCHAR(256) NOT NULL DEFAULT '1' AFTER `weight_class_id`,
ADD `sort_order` VARCHAR(255) NOT NULL DEFAULT '0' AFTER `weight_class_id`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` CHANGE `r_product_status` `r_status` int(11) NOT NULL DEFAULT '0' AFTER `r_quant`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `r_upc` INT NOT NULL DEFAULT '0' AFTER `r_opt`, ADD `r_ean` INT NOT NULL DEFAULT '0' AFTER `r_upc`, ADD `r_jan` INT NOT NULL DEFAULT '0' AFTER `r_ean`, ADD `r_isbn` INT NOT NULL DEFAULT '0' AFTER `r_jan`, ADD `r_mpn` INT NOT NULL DEFAULT '0' AFTER `r_isbn`, ADD `r_location` INT NOT NULL DEFAULT '0' AFTER `r_mpn`, ADD `r_minimum` INT NOT NULL DEFAULT '0' AFTER `r_location`, ADD `r_subtract` INT NOT NULL DEFAULT '0' AFTER `r_minimum`, ADD `r_length` INT NOT NULL DEFAULT '0' AFTER `r_subtract`, ADD `r_width` INT NOT NULL DEFAULT '0' AFTER `r_length`, ADD `r_height` INT NOT NULL DEFAULT '0' AFTER `r_width`, ADD `r_length_class_id` INT NOT NULL DEFAULT '0' AFTER `r_height`, ADD `r_weight` INT NOT NULL DEFAULT '0' AFTER `r_length_class_id`, ADD `r_weight_class_id` INT NOT NULL DEFAULT '0' AFTER `r_weight`, ADD `r_sort_order` INT NOT NULL DEFAULT '0' AFTER `r_weight_class_id`");
$stmt->execute();


$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_replace` CHANGE `rules` `rules` longtext COLLATE 'utf8_general_ci' NOT NULL AFTER `text_stop`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `type_grab` INT NOT NULL DEFAULT '1' AFTER `pars_pause`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `thread` INT NOT NULL DEFAULT '1' AFTER `type_grab`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_browser` CHANGE `cache_page` `cache_page` INT(11) NOT NULL DEFAULT '0'");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permit` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("SELECT * FROM `".$pr."pars_cron` WHERE `id`=1");
$stmt->execute();
$cron = $stmt->fetch(PDO::FETCH_ASSOC);

if(empty($cron)){
  $stmt = $dbh->prepare("INSERT INTO `".$pr."pars_cron` SET `permit`='stop'");
  $stmt->execute();
}


$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_cron_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dn_id` int(11) NOT NULL DEFAULT '0',
  `on` int(11) NOT NULL DEFAULT '0',
  `task` int(11) NOT NULL DEFAULT '0',
  `action` int(11) NOT NULL DEFAULT '0',
  `timeout` varchar(10) NOT NULL DEFAULT '4',
  `time_day` varchar(10) NOT NULL DEFAULT '*',
  `time_week` varchar(10) NOT NULL DEFAULT '*',
  `time_hour` varchar(10) NOT NULL DEFAULT '0',
  `thread` int(11) NOT NULL DEFAULT '1',
  `pause` varchar(256) NOT NULL DEFAULT '0',
  `cache_page` int(10) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  `status` varchar(256) NOT NULL DEFAULT 'end',
  `time_end` varchar(256) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."product` ADD `dn_id` INT NOT NULL DEFAULT '0'");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_createcsv` ADD `csv_column` INT NOT NULL AFTER `value`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_cron` ADD `work` INT NOT NULL DEFAULT '0' AFTER `permit`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_cron` ADD `timezone` VARCHAR(20) NOT NULL DEFAULT '+0' AFTER `work`");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_tools_pattern` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dn_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `setting` text NOT NULL,
  `cron_scan` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_cron_tools` ( 
  `id` INT NOT NULL AUTO_INCREMENT ,  
  `task_id` INT NOT NULL DEFAULT '0' ,  
  `pt_id` INT NOT NULL DEFAULT '0' ,  
  `when_do` INT NOT NULL DEFAULT '0' ,  
  `scan` INT NOT NULL DEFAULT '0' ,    
  PRIMARY KEY  (`id`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `grans_permit` INT(1) NOT NULL DEFAULT '0' AFTER `sid`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_prsetup` ADD `grans_permit_list` TEXT NOT NULL AFTER `opt_data`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_browser` ADD `protocol_version` int(11) NOT NULL DEFAULT '2' AFTER `connect_timeout`");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_link` ADD `list` varchar(250) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '0'");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_link` ADD `error` varchar(250) COLLATE 'utf8_general_ci' NOT NULL");
$stmt->execute();

$stmt = $dbh->prepare("CREATE TABLE `".$pr."pars_link_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dn_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
$stmt->execute();

$stmt = $dbh->prepare("ALTER TABLE `".$pr."pars_setting` ADD `link_list` varchar(4) NOT NULL AFTER `start_link`, ADD `link_error` varchar(4) NOT NULL AFTER `link_list`");
$stmt->execute();

//Удаление ненужных файлов модуля.
@unlink(DIR_TEMPLATE.'catalog/simplepars_cachedn.tpl');
@unlink(DIR_TEMPLATE.'catalog/simplepars_cachedn.twig');

//Финал.
$php_v = round((float)phpversion(),1);

if ($php_v < 5.6) {
  echo '<strong style="color: #FF0000;">Внимание !</strong> Модуль SimplePras работает на <strong style="color: #008000;">PHP 5.6</strong> и новее, у вас установлена версия <strong style="color: #FF0000;">PHP '.$php_v.'</strong> Для работы модуля вам необходимо обновить версию PHP на 5.6 и выше.';
} elseif ( $php_v != 7.2 ) {
  echo 'Модуль SimplePars успешно установлен/обновлен!<br><br><strong style="color: #FF0000;">Внимание !</strong> У вас на сервере установлена версия PHP <b>'.$php_v.'</b><br>
  Для завершения Установки/Обновления вам необходимо перейти в архив с модулем в директорию <b>Other versions of PHP</b><br>
  В ней вы увидите папки <br>
  php-5.6_7.0<br>
  php-7.1<br>
  php-7.2<br>
  php-7.3<br>
  Откройте директорию с вашей версией PHP и скопируйте содержимое в корень сайта с заменой.';
}else{
  echo 'Модуль SimplePars успешно установлен/обновлен!';
}
  

?>